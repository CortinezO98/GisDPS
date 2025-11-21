<?php
    require_once("../iniciador_index.php");
    require_once("../security_session.php");
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $tipo=base64_decode($_GET['tipo']);
    $accion=base64_decode($_GET['accion']);
    if ($tipo!="" AND $accion!="" AND !esMobil()) {
        if ($accion=="inicio") {
            if ($tipo=='turno') {
                //validacion de que no existe un turno iniciado
                $consulta_string_duplicado="SELECT COUNT(`cot_usuario`) FROM `control_turno` WHERE `cot_usuario`=? AND `cot_tipo`='turno' AND `cot_inicio` LIKE '".date('Y-m-d')."%' AND `cot_fin`=''";
                $consulta_registros_duplicado = $enlace_db->prepare($consulta_string_duplicado);
                $consulta_registros_duplicado->bind_param('s', $_SESSION[APP_SESSION.'_session_usu_id']);
                $consulta_registros_duplicado->execute();
                $resultado_registros_duplicado = $consulta_registros_duplicado->get_result()->fetch_all(MYSQLI_NUM);

                $control_duplicado=$resultado_registros_duplicado[0][0];
            } else {
                $control_duplicado=0;
            }

            // Prepara la sentencia
            $sentencia_insert = $enlace_db->prepare("INSERT INTO `control_turno`(`cot_usuario`, `cot_tipo`, `cot_inicio`, `cot_fin`, `cot_duracion`, `cot_fuente`, `cot_observaciones_inicio`, `cot_observaciones_fin`) VALUES (?,?,?,'','',?,'','')");

            // Agrega variables a sentencia preparada
            $sentencia_insert->bind_param('ssss', $_SESSION[APP_SESSION.'_session_usu_id'], $tipo, date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR']);

            if ($control_duplicado==0) {
                if ($sentencia_insert->execute()) {
                    echo "<meta http-equiv='refresh' content='0; url=dashboard'>";
                } else {
                    echo $message_error="Se ha presentado un error al iniciar el turno, por favor intente nuevamente!";
                }
            } else {
                echo "<meta http-equiv='refresh' content='0; url=dashboard'>";
            }
        } elseif ($accion=="cierre") {
            $fecha_actual = date("Y-m-d H:i:s");
            $consulta_string="SELECT `cot_id`, `cot_usuario`, `cot_tipo`, `cot_inicio`, `cot_fin`, `cot_duracion`, `cot_fuente`, `cot_observaciones_inicio`, `cot_observaciones_fin`, `cot_registro_fecha` FROM `control_turno` WHERE `cot_usuario`=? AND `cot_tipo`=? AND `cot_inicio` LIKE '".date("Y-m-d")."%' AND `cot_fin`=''";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            $consulta_registros->bind_param("ss", $_SESSION[APP_SESSION.'_session_usu_id'], $tipo);
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

            $duracion = dateDiff($resultado_registros[0][3],$fecha_actual);

            // Prepara la sentencia
            $consulta_actualizar = $enlace_db->prepare("UPDATE `control_turno` SET `cot_fin`=?, `cot_duracion`=? WHERE `cot_id`=?");

            // Agrega variables a sentencia preparada
            $consulta_actualizar->bind_param('sss', $fecha_actual, $duracion, $resultado_registros[0][0]);

            // Ejecuta sentencia preparada
            $consulta_actualizar->execute();

            if (comprobarSentencia($enlace_db->info)) {
                echo "<meta http-equiv='refresh' content='0; url=dashboard'>";
            } else {
                echo $message_error="Se ha presentado un error al iniciar el turno, por favor intente nuevamente!";
            }
        }
    } else {
        echo $message_error="Se ha presentado un error al iniciar el turno, por favor intente nuevamente!";
    }
?>