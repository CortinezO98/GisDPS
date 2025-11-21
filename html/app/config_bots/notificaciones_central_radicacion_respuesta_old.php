<?php
    $modulo_plataforma="Administrador";
    require_once("/var/www/html/iniciador.php");
    // require_once("../../iniciador.php");
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    // require '../../assets/plugins/PHPMailer-master/src/Exception.php';
    // require '../../assets/plugins/PHPMailer-master/src/PHPMailer.php';
    // require '../../assets/plugins/PHPMailer-master/src/SMTP.php';
    require '/var/www/html/templates/assets/plugins/PHPMailer-master/src/Exception.php';
    require '/var/www/html/templates/assets/plugins/PHPMailer-master/src/PHPMailer.php';
    require '/var/www/html/templates/assets/plugins/PHPMailer-master/src/SMTP.php';

    //consulta de notificaciones pendientes de enviar
    $consulta_notificaciones = mysqli_query($enlace_db, "SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha`, RT.`ncr_host`, RT.`ncr_port`, RT.`ncr_smtpsecure`, RT.`ncr_smtpauth`, RT.`ncr_username`, RT.`ncr_password`, RT.`ncr_setfrom`, RT.`ncr_setfrom_name` FROM `gestion_radicacion_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_radicacion_casos_historial`.`grch_correo_de`=RT.`ncr_id` WHERE `grch_estado_envio`='Pendiente' AND `grch_gestion`='Respuesta' ORDER BY `grch_registro_fecha` LIMIT 12 OFFSET 0");

    $resultado_notificaciones = mysqli_fetch_all($consulta_notificaciones);

    if (count($resultado_notificaciones)>0) {
        for ($i=0; $i < count($resultado_notificaciones); $i++) {
            $marca_temporal = date("Y-m-d H:i:s");
            echo $id_correo=$resultado_notificaciones[$i][0];

            $consulta_string_adjuntos="SELECT `grca_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_radicado`, `grca_radicado_id` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_historial_id`=? AND `grca_estado`='Activo' ORDER BY `grca_id` ASC";
            $consulta_registros_adjuntos = $enlace_db->prepare($consulta_string_adjuntos);
            $consulta_registros_adjuntos->bind_param("s", $id_correo);
            
            if ($resultado_notificaciones[$i][35]!="" AND $resultado_notificaciones[$i][36]!="" AND $resultado_notificaciones[$i][16]!="" AND $resultado_notificaciones[$i][20]!="" AND $resultado_notificaciones[$i][21]!="") {
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $resultado_notificaciones[$i][31];
                    $mail->Port = $resultado_notificaciones[$i][32];
                    $mail->SMTPSecure = $resultado_notificaciones[$i][33];
                    $mail->SMTPAuth = $resultado_notificaciones[$i][34];
                    $mail->Username = $resultado_notificaciones[$i][35];
                    $mail->Password = $resultado_notificaciones[$i][36];
                    $mail->SetFrom($resultado_notificaciones[$i][37], $resultado_notificaciones[$i][38]);
                    $mail->ConfirmReadingTo = $resultado_notificaciones[$i][37];
                    // Agregar el encabezado de notificación de entrega
                    $mail->addCustomHeader('Disposition-Notification-To: '.$resultado_notificaciones[$i][37]);
                    $mail->addCustomHeader('X-Confirm-Reading-To: '.$resultado_notificaciones[$i][37]);
                    $mail->addCustomHeader('Return-Receipt-To: '.$resultado_notificaciones[$i][37]);

                    $num_intentos=intval($resultado_notificaciones[$i][26])+1;

                    if ($num_intentos>=2) {
                        $estado_error="Error";
                    } else {
                        $estado_error="Pendiente";
                    }

                    $control_destinatario=0;
                    $destino_to=explode(";", $resultado_notificaciones[$i][16]);
                    for ($j=0; $j < count($destino_to); $j++) { 
                        if ($destino_to[$j]!="") {
                            $control_destinatario++;
                            $mail->addAddress($destino_to[$j], $destino_to[$j]);
                        }
                    }

                    $destino_cc=explode(";", $resultado_notificaciones[$i][17]);
                    for ($j=0; $j < count($destino_cc); $j++) { 
                        if ($destino_cc[$j]!="") {
                            $mail->addCC($destino_cc[$j], $destino_cc[$j]);
                        }
                    }

                    $destino_bcc=explode(";", $resultado_notificaciones[$i][18]);
                    for ($j=0; $j < count($destino_bcc); $j++) { 
                        if ($destino_bcc[$j]!="") {
                            $mail->addBCC($destino_bcc[$j], $destino_bcc[$j]);
                        }
                    }

                    //embeddedimage
                    $image_embedded_ruta=explode(";", $resultado_notificaciones[$i][22]);
                    $image_embedded_nombre=explode(";", $resultado_notificaciones[$i][23]);
                    $image_embedded_tipo=explode(";", $resultado_notificaciones[$i][24]);
                    for ($j=0; $j < count($image_embedded_ruta); $j++) { 
                        if ($image_embedded_ruta[$j]!="" AND $image_embedded_nombre[$j]!="" AND $image_embedded_tipo[$j]!="") {
                            $mail->AddEmbeddedImage($image_embedded_ruta[$j], $image_embedded_nombre[$j], $image_embedded_ruta[$j], 'base64', $image_embedded_tipo[$j]);
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
                                    $mail->AddAttachment($ruta_final, $nombre_final);
                                }
                            }
                        }
                    }
                    
                    $mail->IsHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = $resultado_notificaciones[$i][20];


                    $contenido_correo=str_replace('https://dps.iq-online.net.co/assets/images/logo_cliente_notificacion_2.png', 'cid:logo_cliente_notificacion', $resultado_notificaciones[$i][21]);
                    $contenido_correo=str_replace('https://dps.iq-online.net.co/assets/images/logo_certificacion_notificacion_2.png', 'cid:logo_certificacion_notificacion', $contenido_correo);


                    $mail->Body    = '<div style="width: 600px; max-width: 600px;">'.$contenido_correo.'</div>';
                    
                    if($mail->send()) {
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `gestion_radicacion_casos_historial` SET `grch_estado_envio`='Enviado', `grch_fecha_envio`='".$marca_temporal."', `grch_intentos`='".$num_intentos."' WHERE `grch_id`='".$id_correo."'");
                    } else {
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `gestion_radicacion_casos_historial` SET `grch_estado_envio`='".$estado_error."', `grch_fecha_envio`='".$marca_temporal."', `grch_intentos`='".$num_intentos."' WHERE `grch_id`='".$id_correo."'");
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

                    if ($estado_error_final!="") {
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `gestion_radicacion_casos_historial` SET `grch_estado_envio`='".$estado_error_final."', `grch_fecha_envio`='".$marca_temporal."', `grch_intentos`='".$num_intentos."' WHERE `grch_id`='".$id_correo."'");
                    }
                }
            } else {
                $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `gestion_radicacion_casos_historial` SET `grch_estado_envio`='Error-estructura', `grch_fecha_envio`='".$marca_temporal."', `grch_intentos`='1' WHERE `grch_id`='".$id_correo."'");
            }
        }
    }
?>