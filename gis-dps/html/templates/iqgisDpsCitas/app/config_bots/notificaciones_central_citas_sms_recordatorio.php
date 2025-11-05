<?php
    $modulo_plataforma="Administrador";
    require_once("/var/www/html/iniciador.php");
    // require_once("../iniciador.php");
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    require_once("/var/www/html/templates/assets/plugins/guzzle-master/vendor/autoload.php");
    //consulta de notificaciones pendientes de enviar
    
    $fecha_hoy = date("Y-m-d");
    $fecha_recordatorio = date("Y-m-d", strtotime("- 5 day", strtotime($fecha_hoy)));
    
    $consulta_string_fecha="SELECT DISTINCT TC.`gca_fecha` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE TC.`gca_fecha`>? ORDER BY TC.`gca_fecha` ASC LIMIT 1";

    $consulta_registros_fecha = $enlace_db->prepare($consulta_string_fecha);
    $consulta_registros_fecha->bind_param("s", $fecha_hoy);
    $consulta_registros_fecha->execute();
    $resultado_registros_fecha = $consulta_registros_fecha->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_agenda="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, TC.`gca_semana`, TC.`gca_fecha`, TC.`gca_hora`, TC.`gca_estado`, TC.`gca_estado_agenda`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE TC.`gca_fecha`=? AND `gcar_estado`='Reservada'";

    $consulta_registros_agenda = $enlace_db->prepare($consulta_string_agenda);
    $consulta_registros_agenda->bind_param("s", $resultado_registros_fecha[0][0]);
    $consulta_registros_agenda->execute();
    $resultado_registros_agenda = $consulta_registros_agenda->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_duplicado="SELECT `nsms_id`, `nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`, `nsms_fecha_registro` FROM `administrador_notificaciones_sms` WHERE `nsms_identificador`=?";

    $consulta_registros_duplicado = $enlace_db->prepare($consulta_string_duplicado);
    $consulta_registros_duplicado->bind_param("s", $nsms_identificador);
    
    if (count($resultado_registros_agenda)>0) {
        // Prepara la sentencia
        $sentencia_insert = $enlace_db->prepare("INSERT INTO `administrador_notificaciones_sms`(`nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        // Agrega variables a sentencia preparada
        $sentencia_insert->bind_param('ssssssssssss', $nsms_identificador, $nsms_id_modulo, $nsms_prioridad, $nsms_id_set_from, $nsms_destino, $nsms_body, $nsms_url, $nsms_intentos, $nsms_observaciones, $nsms_estado_envio, $nsms_fecha_envio, $nsms_usuario_registro);

        for ($i=0; $i < count($resultado_registros_agenda); $i++) { 
            $nsms_identificador='R'.$resultado_registros_agenda[$i][0];
            
            $consulta_registros_duplicado->execute();
            $resultado_registros_duplicado = $consulta_registros_duplicado->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_duplicado)==0) {
                $nsms_id_modulo='R13';
                $nsms_prioridad='2';
                $nsms_id_set_from='1';
                $nsms_destino=$resultado_registros_agenda[$i][8];
                $nsms_body="Sr (a) ".$resultado_registros_agenda[$i][6].", le recordamos su cita el día ".$array_dias_nombre[date('N', strtotime($resultado_registros_agenda[$i][12]))].' '.date('d', strtotime($resultado_registros_agenda[$i][12])).' de '.$array_meses[intval(date('m', strtotime($resultado_registros_agenda[$i][12])))].' de '.date('Y', strtotime($resultado_registros_agenda[$i][12]))." a las ".date('h:i A', strtotime($resultado_registros_agenda[$i][13]))." en la sede ".$resultado_registros_agenda[$i][16]." - ".$resultado_registros_agenda[$i][17].". Si desea reprogramar ingrese a SHORTURL";
                $nsms_url='https://agendamientodps.grupoasd.com.co/citas/inicio';
                
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