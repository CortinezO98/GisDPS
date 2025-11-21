<?php

    // require '../../assets/plugins/PHPMailer-master/src/Exception.php';
    // require '../../assets/plugins/PHPMailer-master/src/PHPMailer.php';
    // require '../../assets/plugins/PHPMailer-master/src/SMTP.php';



    require_once("/var/www/html/app/functions/microsoft-graph-test.class.php");
    $modulo_plataforma="Administrador";
    require_once("/var/www/html/iniciador.php");
    require_once("/var/www/html/templates/administrador/modules/guzzle-master/vendor/autoload.php");
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Psr7\Request;

    
    ini_set('date.timezone', 'America/Bogota');

    $guzzle = new \GuzzleHttp\Client();
    $mail = new MicrosoftGraph();

    //consulta de notificaciones pendientes de enviar
    $consulta_notificaciones = mysqli_query($enlace_db, "SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha`, RT.`ncr_host`, RT.`ncr_port`, RT.`ncr_smtpsecure`, RT.`ncr_smtpauth`, RT.`ncr_username`, RT.`ncr_password`, RT.`ncr_setfrom`, RT.`ncr_setfrom_name`, RT.`ncr_tenant`, RT.`ncr_client_id`, RT.`ncr_client_secret`, RT.`ncr_device_code`, RT.`ncr_token`, RT.`ncr_token_refresh` FROM `gestion_radicacion_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_radicacion_casos_historial`.`grch_correo_de`=RT.`ncr_id` WHERE `grch_estado_envio`='Pendiente' AND `grch_gestion`='Respuesta' ORDER BY `grch_registro_fecha` LIMIT 12 OFFSET 0");

    $resultado_notificaciones = mysqli_fetch_all($consulta_notificaciones);

    if (count($resultado_notificaciones)>0) {
        for ($i=0; $i < count($resultado_notificaciones); $i++) {
            $marca_temporal = date("Y-m-d H:i:s");
            $id_correo=$resultado_notificaciones[$i][0];

            $consulta_string_adjuntos="SELECT `grca_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_radicado`, `grca_radicado_id` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_historial_id`=? AND `grca_estado`='Activo' ORDER BY `grca_id` ASC";
            $consulta_registros_adjuntos = $enlace_db->prepare($consulta_string_adjuntos);
            $consulta_registros_adjuntos->bind_param("s", $id_correo);
            
            if ($resultado_notificaciones[$i][35]!="" AND $resultado_notificaciones[$i][36]!="" AND $resultado_notificaciones[$i][16]!="" AND $resultado_notificaciones[$i][20]!="" AND $resultado_notificaciones[$i][21]!="") {

                try {
                    $ncr_tenant = $resultado_notificaciones[0][39];
                    $ncr_client_id = $resultado_notificaciones[0][40];
                    $ncr_client_secret = $resultado_notificaciones[0][41];
                    $ncr_device_code = $resultado_notificaciones[0][42];
                    $ncr_token = $resultado_notificaciones[0][43];
                    $ncr_token_refresh = $resultado_notificaciones[0][44];

                    $mail->tenant = $ncr_tenant;
                    $mail->client_id = $ncr_client_id;
                    $mail->client_secret = $ncr_client_secret;
                    $mail->redirect_uri = 'https://dps.iq-online.net.co';
                    $mail->auth_code=$ncr_device_code;
                    $mail->token=$ncr_token;
                    $mail->token_refresh=$ncr_token_refresh;

                    $num_intentos=intval($resultado_notificaciones[$i][26])+1;

                    if ($num_intentos>=2) {
                        $estado_error="Error";
                    } else {
                        $estado_error="Pendiente";
                    }

                    $control_destinatario=0;
                    $destino_to=explode(";", $resultado_notificaciones[$i][16]);
                    $toRecipients=array();
                    for ($j=0; $j < count($destino_to); $j++) { 
                        if ($destino_to[$j]!="") {
                            $control_destinatario++;
                            $toRecipients[]['emailAddress'] = ['address' => $destino_to[$j]];
                        }
                    }


                    $destino_cc=explode(";", $resultado_notificaciones[$i][17]);
                    $ccRecipients=array();
                    for ($j=0; $j < count($destino_cc); $j++) { 
                        if ($destino_cc[$j]!="") {
                            $ccRecipients[]['emailAddress'] = ['address' => $destino_cc[$j]];
                        }
                    }

                    $destino_bcc=explode(";", $resultado_notificaciones[$i][18]);
                    $bccRecipients=array();
                    for ($j=0; $j < count($destino_bcc); $j++) { 
                        if ($destino_bcc[$j]!="") {
                            $bccRecipients[]['emailAddress'] = ['address' => $destino_bcc[$j]];
                        }
                    }

                    //embeddedimage
                    $image_embedded_ruta=explode(";", $resultado_notificaciones[$i][22]);
                    $image_embedded_nombre=explode(";", $resultado_notificaciones[$i][23]);
                    $image_embedded_tipo=explode(";", $resultado_notificaciones[$i][24]);
                    $attachments=array();
                    for ($j=0; $j < count($image_embedded_ruta); $j++) { 
                        if ($image_embedded_ruta[$j]!="" AND $image_embedded_nombre[$j]!="" AND $image_embedded_tipo[$j]!="" AND $resultado_notificaciones[$i][6]!='Radicación DELTA') {
                            $attachments[]=array('@odata.type' => '#microsoft.graph.fileAttachment',
                                            'Name' => $image_embedded_nombre[$j],
                                            'ContentBytes' => base64_encode(file_get_contents($image_embedded_ruta[$j])),
                                            'ContentType' => mime_content_type($image_embedded_ruta[$j]),
                                            'ContentId' => $image_embedded_nombre[$j]);
                        }
                    }


                    // CONSULTA ADJUNTOS DEL HISTORIAL DE GESTIÓN
                    $consulta_registros_adjuntos->execute();
                    $resultado_registros_adjuntos = $consulta_registros_adjuntos->get_result()->fetch_all(MYSQLI_NUM);

                    
                    if (count($resultado_registros_adjuntos)>0) {
                        for ($j=0; $j < count($resultado_registros_adjuntos); $j++) { 
                            if ($resultado_registros_adjuntos[$j][3]!="") {
                                $ruta_final='/var/www/html/templates/radicados/'.$resultado_registros_adjuntos[$j][3];
                                $nombre_final=$resultado_registros_adjuntos[$j][2];
                                if (file_exists($ruta_final)) {
                                    $attachments[]=array('@odata.type' => '#microsoft.graph.fileAttachment',
                                                    'Name' => $nombre_final,
                                                    'ContentBytes' => base64_encode(file_get_contents($ruta_final)),
                                                    'ContentType' => mime_content_type($ruta_final));
                                }
                            }
                        }
                    }
                    
                    $from=$resultado_notificaciones[$i][38];
                    $subject=$resultado_notificaciones[$i][20];

                    $contenido_correo=str_replace('https://dps.iq-online.net.co/assets/images/logo_cliente_notificacion_2.png', 'cid:logo_cliente_notificacion', $resultado_notificaciones[$i][21]);
                    $contenido_correo=str_replace('https://dps.iq-online.net.co/assets/images/logo_certificacion_notificacion_2.png', 'cid:logo_certificacion_notificacion', $contenido_correo);
                    $contenido_correo= '<div style="width: 500px !important; max-width: 500px !important;">'.$contenido_correo.'</div>';

                    $body=[
                        'contentType' => 'html',
                        'content' => $contenido_correo
                    ];

                    $resultado_envio = $mail->mail_send($guzzle, $from, $subject, $body, $toRecipients, $ccRecipients, $bccRecipients, $attachments);

                    if ($resultado_envio=='') {
                        $estado_final='Enviado';
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `gestion_radicacion_casos_historial` SET `grch_estado_envio`='Enviado', `grch_fecha_envio`='".$marca_temporal."', `grch_intentos`='".$num_intentos."' WHERE `grch_id`='".$id_correo."'");
                    } elseif ($resultado_envio=='401') {
                        $estado_final='Error de autenticación';
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `gestion_radicacion_casos_historial` SET `grch_estado_envio`='".$estado_final."', `grch_fecha_envio`='".$marca_temporal."', `grch_intentos`='".$num_intentos."' WHERE `grch_id`='".$id_correo."'");
                    } elseif ($resultado_envio=='400') {
                        $estado_final='Error-estructura-envío';
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `gestion_radicacion_casos_historial` SET `grch_estado_envio`='".$estado_final."', `grch_fecha_envio`='".$marca_temporal."', `grch_intentos`='".$num_intentos."' WHERE `grch_id`='".$id_correo."'");
                    }
                }  catch (Exception $e) {
                    $reporte_error="";
                    $estado_error_final="";
                    $reporte_error=$e->getMessage(); // error messages from anything else!
                    //Validación excepciones
                    settype($reporte_error, 'string');
                    if (stristr($reporte_error, 'Invalid address:')) {
                        $estado_error_final='Destinatario inválido';
                    } elseif ($reporte_error=='SMTP Error: Could not authenticate.') {
                        $estado_error_final='Error de autenticación';
                    } elseif ($reporte_error=='You must provide at least one recipient email address.') {
                        $estado_error_final='Sin destinatario';
                    }

                    // echo $reporte_error;

                    // if ($estado_error_final!="") {
                    //     $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `gestion_radicacion_casos_historial` SET `grch_estado_envio`='".$estado_error_final."', `grch_fecha_envio`='".$marca_temporal."', `grch_intentos`='".$num_intentos."' WHERE `grch_id`='".$id_correo."'");
                    // }
                }
            } else {
                $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `gestion_radicacion_casos_historial` SET `grch_estado_envio`='Error-estructura', `grch_fecha_envio`='".$marca_temporal."', `grch_intentos`='1' WHERE `grch_id`='".$id_correo."'");
            }
        }
    }
?>