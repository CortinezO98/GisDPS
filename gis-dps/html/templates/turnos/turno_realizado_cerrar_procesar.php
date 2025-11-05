<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Control Turnos";
    require_once("../../iniciador.php");
    
    /*DEFINICIÓN DE VARIABLES*/
    $id_usuario=validar_input(base64_decode($_GET['id_usuario']));
    $fecha_turno=validar_input(base64_decode($_GET['fecha_turno']));
    
    $resultado_update="";
    $resultado_update_valor="";
    if ($id_usuario!='' AND $fecha_turno!='') {
        $estado_turno=validar_input($_POST['estado_turno']);
        $motivo=validar_input($_POST['motivo']);

        if ($estado_turno=='cerrar_turno') {
            $fecha_actual=date("Y-m-d H:i:s");
            $data_consulta_turnos=array();
            array_push($data_consulta_turnos, $id_usuario);
            array_push($data_consulta_turnos, "$FechaInicio%");
            $consulta_string_turno_realizado="SELECT `cot_id`, `cot_usuario`, `cot_tipo`, `cot_inicio`, `cot_fin`, `cot_duracion`, `cot_fuente`, `cot_observaciones_inicio`, `cot_observaciones_fin`, `cot_registro_fecha` FROM `control_turno` WHERE `cot_usuario`=? AND `cot_inicio` LIKE ? AND `cot_fin`=''";
            $consulta_registros_turno_realizado = $enlace_db->prepare($consulta_string_turno_realizado);
            if (count($data_consulta_turnos)>0) {
                $consulta_registros_turno_realizado->bind_param(str_repeat("s", count($data_consulta_turnos)), ...$data_consulta_turnos);
            }
            $consulta_registros_turno_realizado->execute();
            $resultado_registros_turno_realizado = $consulta_registros_turno_realizado->get_result()->fetch_all(MYSQLI_NUM);

            // Prepara la sentencia
            $consulta_actualizar = $enlace_db->prepare("UPDATE `control_turno` SET `cot_fin`=?, `cot_duracion`=?, `cot_observaciones_fin`=? WHERE `cot_id`=? AND `cot_usuario`=? AND `cot_fin`=''");

            // Agrega variables a sentencia preparada
            $consulta_actualizar->bind_param('sssss', $fecha_actual, $duracion_turno, $motivo, $id_turno, $id_usuario);
            $control_cierre=0;
            for ($i=0; $i < count($resultado_registros_turno_realizado); $i++) { 
                $duracion_turno = dateDiff($resultado_registros_turno_realizado[$i][3],$fecha_actual);
                $id_turno=$resultado_registros_turno_realizado[$i][0];
                
                // Ejecuta sentencia preparada
                $consulta_actualizar->execute();

                if (comprobarSentencia($enlace_db->info)) {
                    $control_cierre++;
                }
            }

            if ($control_cierre==count($resultado_registros_turno_realizado)) {
                $resultado_update="<p class='alert alert-success p-1 text-center font-size-11'>Registro actualizado exitosamente!</p>";
                $resultado_update_valor=1;
            } else {
                $resultado_update="<p class='alert alert-danger p-1 text-center font-size-11'>¡Problemas al actualizar el registro, por favor verifique e intente nuevamente!</p>";
                $resultado_update_valor=0;
            }
        }
        
        $data = array(
            "resultado" => $resultado_update,
            "resultado_valor" => $resultado_update_valor
        );

        echo json_encode($data);
    }
?>
