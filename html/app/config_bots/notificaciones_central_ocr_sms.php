<?php
    $modulo_plataforma="Administrador";
    require_once("/var/www/html/iniciador.php");
    // require_once("../iniciador.php");
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    require_once("/var/www/html/templates/assets/plugins/guzzle-master/vendor/autoload.php");
    //consulta de notificaciones pendientes de enviar
    $consulta_notificaciones = mysqli_query($enlace_db, "SELECT `nsms_id`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`, `nsms_fecha_registro`, TR.`nsmsr_api`, TR.`nsmsr_username`, TR.`nsmsr_password` FROM `administrador_notificaciones_sms` LEFT JOIN `administrador_buzones_sms` AS TR ON `administrador_notificaciones_sms`.`nsms_id_set_from`=TR.`nsmsr_id` WHERE (`nsms_estado_envio`='Pendiente' OR `nsms_estado_envio`='Error de autenticación') AND `nsms_id_modulo`='11' ORDER BY `nsms_prioridad` LIMIT 100 OFFSET 0");
    $resultado_notificaciones = mysqli_fetch_all($consulta_notificaciones);
    if (count($resultado_notificaciones)>0) {
        $client = new GuzzleHttp\Client();
        for ($i=0; $i < count($resultado_notificaciones); $i++) { 
            $marca_temporal = date("Y-m-d H:i:s");
            $id_notificacion=$resultado_notificaciones[$i][0];
            if ($resultado_notificaciones[$i][4]!="" AND $resultado_notificaciones[$i][5]!="" AND $resultado_notificaciones[$i][13]!="" AND $resultado_notificaciones[$i][14]!="" AND $resultado_notificaciones[$i][15]!="") {
                $num_intentos=intval($resultado_notificaciones[$i][7])+1;

                if ($num_intentos>=2) {
                    $estado_error="Error";
                } else {
                    $estado_error="Pendiente";
                }

                try {
                    $api_url = $resultado_notificaciones[$i][13];
                    $api_user = $resultado_notificaciones[$i][14];
                    $api_pass = $resultado_notificaciones[$i][15];
                    $sms_to = '57'.$resultado_notificaciones[$i][4];
                    $sms_body = $resultado_notificaciones[$i][5];
                    $sms_url = $resultado_notificaciones[$i][6];

                    $response = $client->post($api_url,
                        ['json' => 
                            [
                                'to' => $sms_to,
                                'text' => $sms_body,
                                'customdata' => 'IQGIS-OCR-FA',
                                'isPremium' => false,
                                'isFlash' => false,
                                'isLongmessage' => true,
                                'isRandomRoute' => false,
                                'shortUrlConfig' => [
                                    'url' => $sms_url
                                ]
                            ],
                            'auth' => [$api_user, $api_pass]
                        ]
                    );

                    $jsonData = json_decode($response->getBody(), true);

                    // echo "<pre>";
                    // print_r($jsonData);
                    // echo "</pre>";
                    
                    $nsms_observaciones=$jsonData['statusMessage'].';'.$jsonData['messageId'];
                    if($jsonData['statusCode']==200) {
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `administrador_notificaciones_sms` SET `nsms_estado_envio`='Enviado', `nsms_fecha_envio`='".$marca_temporal."', `nsms_intentos`='".$num_intentos."', `nsms_observaciones`='".$nsms_observaciones."' WHERE `nsms_id`='".$id_notificacion."'");
                    } else {
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `administrador_notificaciones_sms` SET `nsms_estado_envio`='".$estado_error."', `nsms_fecha_envio`='".$marca_temporal."', `nsms_intentos`='".$num_intentos."', `nsms_observaciones`='".$nsms_observaciones."' WHERE `nsms_id`='".$id_notificacion."'");
                    }
                }  catch (Exception $e) {
                    $reporte_error_code="";
                    $reporte_error_mensaje="";
                    $estado_error_final="";
                    $reporte_error_code=$e->getCode(); // error messages from anything else!
                    $reporte_error_mensaje=$e->getMessage(); // error messages from anything else!
                    //Validación excepciones
                    settype($reporte_error_code, 'string');
                    settype($reporte_error_mensaje, 'string');

                    if ($reporte_error_code=='400') {
                        $estado_error_final='Destinatario inválido';
                    } elseif ($reporte_error_code=='401') {
                        $estado_error_final='Error de autenticación';
                    }

                    if ($estado_error_final!="") {
                        $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `administrador_notificaciones_sms` SET `nsms_estado_envio`='".$estado_error_final."', `nsms_fecha_envio`='".$marca_temporal."', `nsms_intentos`='".$num_intentos."', `nsms_observaciones`='".$reporte_error_mensaje."' WHERE `nsms_id`='".$id_notificacion."'");
                    }
                }
            } else {
                $consulta_notificaciones_update = mysqli_query($enlace_db, "UPDATE `administrador_notificaciones_sms` SET `nsms_estado_envio`='Error-estructura', `nsms_fecha_envio`='".$marca_temporal."', `nsms_intentos`='1' WHERE `nsms_id`='".$id_notificacion."'");
            }
        }
    }
?>