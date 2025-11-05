<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Calidad-Monitoreos";
    require_once("../../iniciador.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
    $numero_transaccion=validar_input($_GET['numero_transaccion']);
    $perfil=validar_input($_GET['perfil']);

    if ($numero_transaccion!='') {
        $consulta_string="SELECT `gcmt_id`, `gcmt_piloto`, `gcmt_campania`, `gcmt_fecha`, `gcmt_agente`, `gcmt_nit`, `gcmt_registro_fecha`, `gcmt_agente_id`, TU.`usu_nombres_apellidos`, TU.`usu_usuario_red` FROM `gestion_calidad_monitoreo_transacciones` LEFT JOIN `administrador_usuario` AS TU ON `gestion_calidad_monitoreo_transacciones`.`gcmt_agente_id`=TU.`usu_id` WHERE `gcmt_id`=?";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $numero_transaccion);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        if (count($resultado_registros)>0) {
            $resultado_data=1;
            $piloto=$resultado_registros[0][1];
            $campania=$resultado_registros[0][2];
            $fecha=$resultado_registros[0][3];
            $agente=$resultado_registros[0][4];
            $nit=$resultado_registros[0][5];
            $agente_id=$resultado_registros[0][7];
            if ($resultado_registros[0][8]!="") {
                $resultado_data_agente=1;
                $agente_nombre=$resultado_registros[0][8];
                $agente_red=$resultado_registros[0][9];
                $lista_agentes='<option value="" class="font-size-11">Seleccione</option>';
                $lista_agentes.='<option value="'.$agente_id.'" class="font-size-11" data-tokens="'.$agente_id.' '.$agente_nombre.' '.$agente_red.'">'.$agente_nombre.'</option>';
            } else {
                $resultado_data_agente=0;
                $agente_nombre='';
                $lista_agentes='';
            }
        } else {
            $resultado_data=0;
            $piloto='';
            $campania='';
            $fecha='';
            $agente='';
            $nit='';
            $agente_id='';
            $resultado_data_agente=0;
            $agente_nombre='';
            $lista_agentes='';
        }

        $consulta_string_duplicado="SELECT `gcm_id`, `gcm_numero_transaccion` FROM `gestion_calidad_monitoreo` WHERE `gcm_tipo_monitoreo`='Muestra aleatoria' AND `gcm_numero_transaccion`=?";
        $consulta_registros_duplicado = $enlace_db->prepare($consulta_string_duplicado);
        $consulta_registros_duplicado->bind_param("s", $numero_transaccion);
        $consulta_registros_duplicado->execute();
        $resultado_registros_duplicado = $consulta_registros_duplicado->get_result()->fetch_all(MYSQLI_NUM);

        if (count($resultado_registros_duplicado)>0) {
            $tipo_monitoreo='<option value="">Seleccione</option>';
            $tipo_monitoreo.='<option value="Escucha en línea">Escucha en línea</option>';
            $tipo_monitoreo.='<option value="Calibración">Calibración</option>';
            $tipo_monitoreo.='<option value="PST">PST</option>';
            $tipo_monitoreo.='<option value="Seguimiento">Seguimiento</option>';
            $tipo_monitoreo.='<option value="Llamada sorpresa">Llamada sorpresa</option>';
        } else {
            $tipo_monitoreo='<option value="">Seleccione</option>';
            
            if ($perfil=="Supervisor") {
                $tipo_monitoreo.='<option value="Escucha en línea">Escucha en línea</option>';
                $tipo_monitoreo.='<option value="Calibración">Calibración</option>';
                $tipo_monitoreo.='<option value="PST">PST</option>';
                $tipo_monitoreo.='<option value="Seguimiento">Seguimiento</option>';
                $tipo_monitoreo.='<option value="Llamada sorpresa">Llamada sorpresa</option>';
            } else {
                $tipo_monitoreo.='<option value="Escucha en línea">Escucha en línea</option>';
                $tipo_monitoreo.='<option value="Muestra aleatoria">Muestra aleatoria</option>';
                $tipo_monitoreo.='<option value="Calibración">Calibración</option>';
                $tipo_monitoreo.='<option value="PST">PST</option>';
                $tipo_monitoreo.='<option value="Seguimiento">Seguimiento</option>';
                $tipo_monitoreo.='<option value="Llamada sorpresa">Llamada sorpresa</option>';
            }
        }
    }

    $data = array(
        "resultado" => $resultado_data,
        "piloto" => $piloto,
        "campania" => $campania,
        "fecha" => $fecha,
        "agente" => $agente,
        "nit" => $nit,
        "agente_id" => $agente_id,
        "resultado_data_agente" => $resultado_data_agente,
        "lista_agentes" => $lista_agentes,
        "tipo_monitoreo" => $tipo_monitoreo
    );

    echo json_encode($data);
?>