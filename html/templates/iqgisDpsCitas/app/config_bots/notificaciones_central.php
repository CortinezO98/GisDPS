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
    $consulta_notificaciones = mysqli_query($enlace_db, "SELECT `nc_id`, `nc_id_modulo`, `nc_prioridad`, `nc_id_set_from`, `nc_address`, `nc_cc`, `nc_bcc`, `nc_reply_to`, `nc_subject`, `nc_body`, `nc_embeddedimage_ruta`, `nc_intentos`, `nc_eliminar`, `nc_estado_envio`, `ncr_host`, `ncr_port`, `ncr_smtpsecure`, `ncr_smtpauth`, `ncr_username`, `ncr_password`, `ncr_setfrom`, `ncr_setfrom_name`, `nc_embeddedimage_ruta`, `nc_embeddedimage_nombre`, `nc_embeddedimage_tipo` FROM `administrador_notificaciones` LEFT JOIN `administrador_buzones` AS RT ON `administrador_notificaciones`.`nc_id_set_from`=RT.`ncr_id` WHERE `nc_estado_envio`='Pendiente' AND `nc_id_set_from`='1' ORDER BY `nc_prioridad` LIMIT 8 OFFSET 0");
    $resultado_notificaciones = mysqli_fetch_all($consulta_notificaciones);
    if (count($resultado_notificaciones)>0) {
        for ($i=0; $i < count($resultado_notificaciones); $i++) { 
            $marca_temporal = date("Y-m-d H:i:s");
            $id_correo=$resultado_notificaciones[$i][0];
            if ($resultado_notificaciones[$i][14]!="" AND $resultado_notificaciones[$i][15]!="" AND $resultado_notificaciones[$i][16]!="" AND $resultado_notificaciones[$i][17]!="" AND $resultado_notificaciones[$i][18]!="" AND $resultado_notificaciones[$i][19]!="" AND $resultado_notificaciones[$i][20]!="" AND $resultado_notificaciones[$i][21]!="" AND $resultado_notificaciones[$i][4]!="" AND $resultado_notificaciones[$i][8]!="" AND $resultado_notificaciones[$i][9]!="") {
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $resultado_notificaciones[$i][14];
                    $mail->Port = $resultado_notificaciones[$i][15];
                    $mail->SMTPSecure = $resultado_notificaciones[$i][16];
                    $mail->SMTPAuth = $resultado_notificaciones[$i][17];
                    $mail->Username = $resultado_notificaciones[$i][18];
                    $mail->Password = $resultado_notificaciones[$i][19];
                    $mail->SetFrom($resultado_notificaciones[$i][20], $resultado_notificaciones[$i][21]);
                    
                    $num_intentos=intval($resultado_notificaciones[$i][11])+1;

                    if ($num_intentos>=2) {
                        $estado_error="Error";
                    } else {
                        $estado_error="Pendiente";
                    }

                    $destino_to=explode(";", $resultado_notificaciones[$i][4]);
                    for ($j=0; $j < count($destino_to); $j++) { 
                        if ($destino_to[$j]!="") {
                            $mail->addAddress($destino_to[$j], $destino_to[$j]);
                        }
                    }

                    $destino_cc=explode(";", $resultado_notificaciones[$i][5]);
                    for ($j=0; $j < count($destino_cc); $j++) { 
                        if ($destino_cc[$j]!="") {
                            $mail->addCC($destino_cc[$j], $destino_cc[$j]);
                        }
                    }

                    $destino_bcc=explode(";", $resultado_notificaciones[$i][6]);
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
                    
                    $mail->IsHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = $resultado_notificaciones[$i][8];
                    $mail->Body    = $resultado_notificaciones[$i][9];
                    
                    if($mail->send()) {
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `administrador_notificaciones` SET `nc_estado_envio`='Enviado', `nc_fecha_envio`='".$marca_temporal."', `nc_intentos`='".$num_intentos."' WHERE `nc_id`='".$id_correo."'");
                    } else {
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `administrador_notificaciones` SET `nc_estado_envio`='".$estado_error."', `nc_fecha_envio`='".$marca_temporal."', `nc_intentos`='".$num_intentos."' WHERE `nc_id`='".$id_correo."'");
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
                    }

                    if ($estado_error_final!="") {
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `administrador_notificaciones` SET `nc_estado_envio`='".$estado_error_final."', `nc_fecha_envio`='".$marca_temporal."', `nc_intentos`='".$num_intentos."' WHERE `nc_id`='".$id_correo."'");
                    }
                }
            } else {
                $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `administrador_notificaciones` SET `nc_estado_envio`='Error-estructura', `nc_fecha_envio`='".$marca_temporal."', `nc_intentos`='1' WHERE `nc_id`='".$id_correo."'");
            }
        }
    }
?>