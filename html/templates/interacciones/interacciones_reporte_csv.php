<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Interacciones";
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    if(isset($_POST["reporte"])){
        $tipo=validar_input($_POST['tipo']);
        $canal_atencion=validar_input($_POST['canal_atencion']);
        $fecha_inicio=$tipo.'-01';
        $fecha_fin=$tipo.'-31'.' 23:59:59';

        $fecha_inicio_filtro=validar_input($_POST['fecha_inicio']);
        $fecha_fin_filtro=validar_input($_POST['fecha_fin']).' 23:59:59';

        $titulo_reporte="Gestión Interacciones ".date('Y-m-d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        $filtro_canal='';
        if ($canal_atencion!="Todos") {
            $filtro_canal=' AND TI.`gi_canal_atencion`=?';
            array_push($data_consulta, $canal_atencion);
        }

        $filtro_dias='';
        if ($fecha_inicio_filtro!="" AND $fecha_fin_filtro!="") {
            $filtro_dias=' AND TI.`gi_registro_fecha`>=? AND TI.`gi_registro_fecha`<=?';
            array_push($data_consulta, $fecha_inicio_filtro);
            array_push($data_consulta, $fecha_fin_filtro);
        }

        $consulta_string="SELECT TI.`gi_id`, TI.`gi_id_registro`, TI.`gi_id_caso`, TI.`gi_primer_nombre`, TI.`gi_segundo_nombre`, TI.`gi_primer_apellido`, TI.`gi_segundo_apellido`, TI.`gi_tipo_documento`, TI.`gi_identificacion`, TI.`gi_fecha_nacimiento`, TI.`gi_edad`, TI.`gi_municipio`, TI.`gi_telefono`, TI.`gi_celular`, TI.`gi_email`, TI.`gi_direccion`, TI.`gi_consulta`, TI.`gi_respuesta`, TI.`gi_resultado`, TI.`gi_descripcion_resultado`, TI.`gi_complemento_resultado`, TI.`gi_canal_atencion`, TI.`gi_sms`, TI.`gi_id_encuesta`, TI.`gi_registro_usuario`, TI.`gi_registro_fecha`, TU.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TC.`ciu_departamento`, TC.`ciu_municipio`, TE.`gie_pregunta_1`, TE.`gie_pregunta_2`, TE.`gie_pregunta_3`, TE.`gie_pregunta_4`, TE.`gie_pregunta_5`, TE.`gie_respuesta_fecha`, TI.`gi_beneficiario`, TI.`gi_informacion_poblacional`, TI.`gi_atencion_preferencial`, TI.`gi_genero`, TI.`gi_nivel_escolaridad`, TI.`gi_auxiliar_1`, TI.`gi_auxiliar_2`, TI.`gi_auxiliar_3`, TI.`gi_auxiliar_4`, TI.`gi_auxiliar_5`, TI.`gi_auxiliar_6`, TI.`gi_auxiliar_7`, TI.`gi_auxiliar_8`, TI.`gi_auxiliar_9`, TI.`gi_auxiliar_10` FROM `gestion_interacciones_historico` AS TI LEFT JOIN `administrador_usuario` AS TU ON TI.`gi_registro_usuario`=TU.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TI.`gi_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TI.`gi_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TI.`gi_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TI.`gi_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TI.`gi_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TI.`gi_subtipificacion_3`=TN6.`gic6_id` LEFT JOIN `administrador_ciudades` AS TC ON TI.`gi_municipio`=TC.`ciu_codigo` LEFT JOIN `gestion_interacciones_encuestas` AS TE ON TI.`gi_id_encuesta`=TE.`gie_id` WHERE TI.`gi_registro_fecha`>=? AND TI.`gi_registro_fecha`<=? ".$filtro_canal." ".$filtro_dias." ORDER BY TI.`gi_registro_fecha`";

        $consulta_registros = $enlace_db->prepare($consulta_string);
        if (count($data_consulta)>0) {
            // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
            $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            
        }
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $consulta_string_auxiliar="SELECT `gia_id`, `gia_campo`, `gia_tipo`, `gia_nombre`, `gia_estado`, `gia_opciones` FROM `gestion_interacciones_auxiliar` ORDER BY `gia_id`";
        $consulta_registros_auxiliar = $enlace_db->prepare($consulta_string_auxiliar);
        $consulta_registros_auxiliar->execute();
        $resultado_registros_auxiliar = $consulta_registros_auxiliar->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_auxiliar); $i++) { 
            $array_auxiliar[$resultado_registros_auxiliar[$i][1]]['nombre']=$resultado_registros_auxiliar[$i][3];
        }
    }
    
    $delimitador = ';';
    $encapsulador = '"';
    $ruta='storage/'.$titulo_reporte;
    // create a file pointer connected to the output stream
    $file = fopen($ruta, 'w');
    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($file, array('Reporte: Gestión Interacciones'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio), $delimitador, $encapsulador);
    
    $titulos=array('Id Registro', 'Canal de atención', 'Id Caso', 'Tipo documento', 'Identificación', 'Primer nombre', 'Segundo nombre', 'Primer apellido', 'Segundo apellido', 'Fecha nacimiento', 'Edad', 'Municipio/departamento', 'Dirección', 'Celular', 'Teléfono', 'Email', 'Es beneficiario?', 'Tipificación 1', 'Tipificación 2', 'Tipificación 3', 'Tipificación 4', 'Tipificación 5', 'Tipificación 6', 'Consulta', 'Respuesta', 'Resultado', 'Descripción del resultado', 'Complemento del resultado', 'Desea recibir información por SMS', 'Información Poblacional', 'Atención Preferencial', 'Género', 'Nivel Escolaridad', 'Doc. Usuario Registro', 'Usuario Registro', 'Fecha Registro', 'Id Encuesta', '1. ¿Considera que su inquietud fue resuelta?', '2. Califique el nivel de satisfacción.', '3. Califique el tiempo de su consulta a través de este canal.', '4. ¿Fue completa y clara la información: Opciones de respuesta?.', '5. En este espacio puede dejarnos comentarios-recomendaciones-observaciones o sugerencias.', 'Fecha Respuesta Encuesta', $array_auxiliar['gi_auxiliar_1']['nombre'], $array_auxiliar['gi_auxiliar_2']['nombre'], $array_auxiliar['gi_auxiliar_3']['nombre'], $array_auxiliar['gi_auxiliar_4']['nombre'], $array_auxiliar['gi_auxiliar_5']['nombre'], $array_auxiliar['gi_auxiliar_6']['nombre'], $array_auxiliar['gi_auxiliar_7']['nombre'], $array_auxiliar['gi_auxiliar_8']['nombre'], $array_auxiliar['gi_auxiliar_9']['nombre'], $array_auxiliar['gi_auxiliar_10']['nombre']);

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $municipio=$resultado_registros[$i][34].'/'.$resultado_registros[$i][33];
        $linea=array($resultado_registros[$i][1], $resultado_registros[$i][21], $resultado_registros[$i][2], $resultado_registros[$i][7], $resultado_registros[$i][8], $resultado_registros[$i][3], $resultado_registros[$i][4], $resultado_registros[$i][5], $resultado_registros[$i][6], $resultado_registros[$i][9], $resultado_registros[$i][10], $municipio, $resultado_registros[$i][15], $resultado_registros[$i][13], $resultado_registros[$i][12], $resultado_registros[$i][14], $resultado_registros[$i][41], $resultado_registros[$i][27], $resultado_registros[$i][28], $resultado_registros[$i][29], $resultado_registros[$i][30], $resultado_registros[$i][31], $resultado_registros[$i][32], $resultado_registros[$i][16], $resultado_registros[$i][17], $resultado_registros[$i][18], $resultado_registros[$i][19], $resultado_registros[$i][20], $resultado_registros[$i][22], $resultado_registros[$i][42], $resultado_registros[$i][43], $resultado_registros[$i][44], $resultado_registros[$i][45], $resultado_registros[$i][24], $resultado_registros[$i][26], $resultado_registros[$i][25], $resultado_registros[$i][23], $resultado_registros[$i][35], $resultado_registros[$i][36], $resultado_registros[$i][37], $resultado_registros[$i][38], $resultado_registros[$i][39], $resultado_registros[$i][40], $resultado_registros[$i][46], $resultado_registros[$i][47], $resultado_registros[$i][48], $resultado_registros[$i][49], $resultado_registros[$i][50], $resultado_registros[$i][51], $resultado_registros[$i][52], $resultado_registros[$i][53], $resultado_registros[$i][54], $resultado_registros[$i][55]);
        fputcsv($file, $linea, $delimitador, $encapsulador);
    }
    rewind($file);

    fclose($file);

    header("Content-disposition: attachment; filename=".$titulo_reporte);
    header("Content-type: MIME");
    header('Cache-Control: max-age=0');
    readfile($ruta);
    unlink($ruta)

    //Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    // header('Content-Type: text/csv; charset=utf-8');
    // header('Content-Disposition: attachment; filename=HRdata.csv');
    // header('Cache-Control: max-age=0');
?>
