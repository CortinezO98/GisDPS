<?php
    $modulo_plataforma="Administrador";
    require_once("/var/www/html/iniciador.php");
    // require_once("../iniciador.php");
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    // require_once("/var/www/html/templates/assets/plugins/guzzle-master/vendor/autoload.php");
    //consulta de notificaciones pendientes de enviar
    
    $fecha_hoy = date("Y-m-d");
    $fecha_recordatorio = date("Y-m-d", strtotime("- 5 day", strtotime($fecha_hoy)));
    $consulta_notificaciones = mysqli_query($enlace_db, "SELECT `nsms_id`, `nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`, `nsms_fecha_registro`, TR.`ocrr_gestion_estado`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido` FROM `administrador_notificaciones_sms` LEFT JOIN `gestion_ocr_consolidado` AS TCON ON `administrador_notificaciones_sms`.`nsms_identificador`=TCON.`ocrc_id` LEFT JOIN `gestion_ocr_resultado` AS TR ON TCON.`ocrc_cod_familia`=TR.`ocrr_cod_familia` LEFT JOIN `gestion_ocr` AS TOCR ON TCON.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `nsms_id_modulo`='11' AND TR.`ocrr_gestion_estado`='Contactado-Pendiente Documentos' AND `nsms_fecha_registro` LIKE '%".$fecha_recordatorio."%' ORDER BY `nsms_fecha_registro` ASC");
    $resultado_notificaciones = mysqli_fetch_all($consulta_notificaciones);

    if (count($resultado_notificaciones)>0) {
        // Prepara la sentencia
        $sentencia_insert = $enlace_db->prepare("INSERT INTO `administrador_notificaciones_sms`(`nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        // Agrega variables a sentencia preparada
        $sentencia_insert->bind_param('ssssssssssss', $nsms_identificador, $nsms_id_modulo, $nsms_prioridad, $nsms_id_set_from, $nsms_destino, $nsms_body, $nsms_url, $nsms_intentos, $nsms_observaciones, $nsms_estado_envio, $nsms_fecha_envio, $nsms_usuario_registro);
        
        for ($i=0; $i < count($resultado_notificaciones); $i++) { 
            $nsms_identificador='R'.$resultado_notificaciones[$i][1];
            
            $consulta_duplicado = mysqli_query($enlace_db, "SELECT `nsms_id`, `nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`, `nsms_fecha_registro` FROM `administrador_notificaciones_sms` WHERE `nsms_identificador`='".$nsms_identificador."' AND `nsms_fecha_registro` LIKE '%".$fecha_hoy."%'");
            $resultado_duplicado = mysqli_fetch_all($consulta_duplicado);
            
            if (count($resultado_duplicado)==0) {
                $nsms_id_modulo=$resultado_notificaciones[$i][2];
                $nsms_prioridad='2';
                $nsms_id_set_from=$resultado_notificaciones[$i][4];
                $nsms_destino=$resultado_notificaciones[$i][5];

                $nombre_cabeza_familia=$resultado_notificaciones[$i][15];
                if ($resultado_notificaciones[$i][16]!="") {
                    $nombre_cabeza_familia.=' '.$resultado_notificaciones[$i][16];
                }
                if ($resultado_notificaciones[$i][17]!="") {
                    $nombre_cabeza_familia.=' '.$resultado_notificaciones[$i][17];
                }
                if ($resultado_notificaciones[$i][18]!="") {
                    $nombre_cabeza_familia.=' '.$resultado_notificaciones[$i][18];
                }

                $nsms_body=$nombre_cabeza_familia.", recuerde cargar los documentos corregidos de su inscripción de Familias en Acción en el link: SHORTURL";
                $nsms_url=$resultado_notificaciones[$i][7];
                $nsms_intentos='';
                $nsms_observaciones='';
                $nsms_estado_envio='Pendiente';
                $nsms_fecha_envio='';
                $nsms_usuario_registro='1111111111';
                if ($sentencia_insert->execute()) {

                }
            }
        }
    }
?>