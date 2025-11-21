<?php
    // Validación de permisos del usuario para el módulo
    $modulo_plataforma = "Interacciones";

    // En producción puedes poner display_errors en 0
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);

    // Limpia cualquier buffer previo
    while (ob_get_level()) {
        ob_end_clean();
    }

    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST["reporte"])) {
        http_response_code(400);
        exit('Solicitud inválida.');
    }


    $tipo           = isset($_POST['tipo']) ? validar_input($_POST['tipo']) : date('Y-m'); // YYYY-MM
    $canal_atencion = isset($_POST['canal_atencion']) ? validar_input($_POST['canal_atencion']) : 'Todos';

    $fecha_inicio = $tipo . '-01';
    $fecha_fin    = $tipo . '-31' . ' 23:59:59';

    $fecha_inicio_filtro = isset($_POST['fecha_inicio']) ? validar_input($_POST['fecha_inicio']) : '';
    $fecha_fin_filtro    = isset($_POST['fecha_fin']) && $_POST['fecha_fin'] !== ''
        ? validar_input($_POST['fecha_fin']) . ' 23:59:59'
        : '';

    $titulo_reporte = "Gestión Interacciones " . date('Y-m-d H_i_s') . ".csv";

    // Inicializa array de parámetros
    $data_consulta = array();
    $data_consulta[] = $fecha_inicio;
    $data_consulta[] = $fecha_fin;

    // Filtro por canal
    $filtro_canal = '';
    if ($canal_atencion !== "Todos") {
        $filtro_canal = ' AND TI.`gi_canal_atencion` = ?';
        $data_consulta[] = $canal_atencion;
    }

    // Filtro por rango de días
    $filtro_dias = '';
    if ($fecha_inicio_filtro !== '' && $fecha_fin_filtro !== '') {
        $filtro_dias = ' AND TI.`gi_registro_fecha` >= ? AND TI.`gi_registro_fecha` <= ?';
        $data_consulta[] = $fecha_inicio_filtro;
        $data_consulta[] = $fecha_fin_filtro;
    }


    $consulta_string = "
        SELECT 
            TI.`gi_id`,
            TI.`gi_id_registro`,
            TI.`gi_id_caso`,
            TI.`gi_primer_nombre`,
            TI.`gi_segundo_nombre`,
            TI.`gi_primer_apellido`,
            TI.`gi_segundo_apellido`,
            TI.`gi_tipo_documento`,
            TI.`gi_identificacion`,
            TI.`gi_fecha_nacimiento`,
            TI.`gi_edad`,
            TI.`gi_municipio`,
            TI.`gi_telefono`,
            TI.`gi_celular`,
            TI.`gi_email`,
            TI.`gi_direccion`,
            TI.`gi_consulta`,
            TI.`gi_respuesta`,
            TI.`gi_resultado`,
            TI.`gi_descripcion_resultado`,
            TI.`gi_complemento_resultado`,
            TI.`gi_canal_atencion`,
            TI.`gi_sms`,
            TI.`gi_id_encuesta`,
            TI.`gi_registro_usuario`,
            TI.`gi_registro_fecha`,
            TU.`usu_nombres_apellidos`,
            TN1.`gic1_item`,
            TN2.`gic2_item`,
            TN3.`gic3_item`,
            TN4.`gic4_item`,
            TN5.`gic5_item`,
            TN6.`gic6_item`,
            TC.`ciu_departamento`,
            TC.`ciu_municipio`,
            TE.`gie_pregunta_1`,
            TE.`gie_pregunta_2`,
            TE.`gie_pregunta_3`,
            TE.`gie_pregunta_4`,
            TE.`gie_pregunta_5`,
            TE.`gie_respuesta_fecha`,
            TI.`gi_beneficiario`,
            TI.`gi_informacion_poblacional`,
            TI.`gi_atencion_preferencial`,
            TI.`gi_genero`,
            TI.`gi_nivel_escolaridad`,
            TI.`gi_auxiliar_1`,
            TI.`gi_auxiliar_2`,
            TI.`gi_auxiliar_3`,
            TI.`gi_auxiliar_4`,
            TI.`gi_auxiliar_5`,
            TI.`gi_auxiliar_6`,
            TI.`gi_auxiliar_7`,
            TI.`gi_auxiliar_8`,
            TI.`gi_auxiliar_9`,
            TI.`gi_auxiliar_10`
        FROM `gestion_interacciones_historico` AS TI
        LEFT JOIN `administrador_usuario` AS TU 
            ON TI.`gi_registro_usuario` = TU.`usu_id`
        LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 
            ON TI.`gi_direcciones_misionales` = TN1.`gic1_id`
        LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 
            ON TI.`gi_programa` = TN2.`gic2_id`
        LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 
            ON TI.`gi_tipificacion` = TN3.`gic3_id`
        LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 
            ON TI.`gi_subtipificacion_1` = TN4.`gic4_id`
        LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 
            ON TI.`gi_subtipificacion_2` = TN5.`gic5_id`
        LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 
            ON TI.`gi_subtipificacion_3` = TN6.`gic6_id`
        LEFT JOIN `administrador_ciudades` AS TC 
            ON TI.`gi_municipio` = TC.`ciu_codigo`
        LEFT JOIN `gestion_interacciones_encuestas` AS TE 
            ON TI.`gi_id_encuesta` = TE.`gie_id`
        WHERE 
            TI.`gi_registro_fecha` >= ?
            AND TI.`gi_registro_fecha` <= ?
            $filtro_canal
            $filtro_dias
        ORDER BY TI.`gi_registro_fecha`
    ";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    if ($consulta_registros === false) {
        http_response_code(500);
        exit('Error al preparar la consulta de datos: ' . $enlace_db->error);
    }

    if (count($data_consulta) > 0) {
        $tipos = str_repeat("s", count($data_consulta));
        $consulta_registros->bind_param($tipos, ...$data_consulta);
    }

    if (!$consulta_registros->execute()) {
        http_response_code(500);
        exit('Error al ejecutar la consulta de datos: ' . $consulta_registros->error);
    }

    // Prepara bind_result con array reutilizable
    $consulta_registros->store_result();
    $num_cols = $consulta_registros->field_count;
    $row = array_fill(0, $num_cols, null);
    $binds = array();
    foreach ($row as $i => &$val) {
        $binds[] = &$val;
    }
    call_user_func_array(array($consulta_registros, 'bind_result'), $binds);


    $array_auxiliar = array(
        'gi_auxiliar_1'  => array('nombre' => 'Auxiliar 1'),
        'gi_auxiliar_2'  => array('nombre' => 'Auxiliar 2'),
        'gi_auxiliar_3'  => array('nombre' => 'Auxiliar 3'),
        'gi_auxiliar_4'  => array('nombre' => 'Auxiliar 4'),
        'gi_auxiliar_5'  => array('nombre' => 'Auxiliar 5'),
        'gi_auxiliar_6'  => array('nombre' => 'Auxiliar 6'),
        'gi_auxiliar_7'  => array('nombre' => 'Auxiliar 7'),
        'gi_auxiliar_8'  => array('nombre' => 'Auxiliar 8'),
        'gi_auxiliar_9'  => array('nombre' => 'Auxiliar 9'),
        'gi_auxiliar_10' => array('nombre' => 'Auxiliar 10'),
    );

    $consulta_string_auxiliar = "
        SELECT 
            `gia_id`,
            `gia_campo`,
            `gia_tipo`,
            `gia_nombre`,
            `gia_estado`,
            `gia_opciones`
        FROM `gestion_interacciones_auxiliar`
        ORDER BY `gia_id`
    ";

    $resultado_aux = $enlace_db->query($consulta_string_auxiliar);
    if ($resultado_aux !== false) {
        while ($fila_aux = $resultado_aux->fetch_assoc()) {
            $campo  = $fila_aux['gia_campo'];
            $nombre = $fila_aux['gia_nombre'];
            $array_auxiliar[$campo]['nombre'] = $nombre;
        }
        $resultado_aux->free();
    }

    $delimitador   = ';';
    $encapsulador  = '"';
    $ruta          = 'storage/' . $titulo_reporte;

    $file = fopen($ruta, 'w');
    if ($file === false) {
        http_response_code(500);
        exit('No se pudo crear el archivo de reporte.');
    }

    // BOM UTF-8
    fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($file, array('Reporte: Gestión Interacciones'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: ' . $fecha_inicio), $delimitador, $encapsulador);

    $titulos = array(
        'Id Registro',
        'Canal de atención',
        'Id Caso',
        'Tipo documento',
        'Identificación',
        'Primer nombre',
        'Segundo nombre',
        'Primer apellido',
        'Segundo apellido',
        'Fecha nacimiento',
        'Edad',
        'Municipio/departamento',
        'Dirección',
        'Celular',
        'Teléfono',
        'Email',
        'Es beneficiario?',
        'Tipificación 1',
        'Tipificación 2',
        'Tipificación 3',
        'Tipificación 4',
        'Tipificación 5',
        'Tipificación 6',
        'Consulta',
        'Respuesta',
        'Resultado',
        'Descripción del resultado',
        'Complemento del resultado',
        'Desea recibir información por SMS',
        'Información Poblacional',
        'Atención Preferencial',
        'Género',
        'Nivel Escolaridad',
        'Doc. Usuario Registro',
        'Usuario Registro',
        'Fecha Registro',
        'Id Encuesta',
        '1. ¿Considera que su inquietud fue resuelta?',
        '2. Califique el nivel de satisfacción.',
        '3. Califique el tiempo de su consulta a través de este canal.',
        '4. ¿Fue completa y clara la información: Opciones de respuesta?.',
        '5. En este espacio puede dejarnos comentarios-recomendaciones-observaciones o sugerencias.',
        'Fecha Respuesta Encuesta',
        $array_auxiliar['gi_auxiliar_1']['nombre'],
        $array_auxiliar['gi_auxiliar_2']['nombre'],
        $array_auxiliar['gi_auxiliar_3']['nombre'],
        $array_auxiliar['gi_auxiliar_4']['nombre'],
        $array_auxiliar['gi_auxiliar_5']['nombre'],
        $array_auxiliar['gi_auxiliar_6']['nombre'],
        $array_auxiliar['gi_auxiliar_7']['nombre'],
        $array_auxiliar['gi_auxiliar_8']['nombre'],
        $array_auxiliar['gi_auxiliar_9']['nombre'],
        $array_auxiliar['gi_auxiliar_10']['nombre']
    );

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    // ==========================
    // 5) ESCRIBIR CADA FILA DIRECTAMENTE
    // ==========================
    while ($consulta_registros->fetch()) {
        // $row tiene SIEMPRE la fila actual
        $municipio = $row[34] . '/' . $row[33];

        $linea = array(
            $row[1],
            $row[21],
            $row[2],
            $row[7],
            $row[8],
            $row[3],
            $row[4],
            $row[5],
            $row[6],
            $row[9],
            $row[10],
            $municipio,
            $row[15],
            $row[13],
            $row[12],
            $row[14],
            $row[41],
            $row[27],
            $row[28],
            $row[29],
            $row[30],
            $row[31],
            $row[32],
            $row[16],
            $row[17],
            $row[18],
            $row[19],
            $row[20],
            $row[22],
            $row[42],
            $row[43],
            $row[44],
            $row[45],
            $row[24],
            $row[26],
            $row[25],
            $row[23],
            $row[35],
            $row[36],
            $row[37],
            $row[38],
            $row[39],
            $row[40],
            $row[46],
            $row[47],
            $row[48],
            $row[49],
            $row[50],
            $row[51],
            $row[52],
            $row[53],
            $row[54],
            $row[55]
        );

        fputcsv($file, $linea, $delimitador, $encapsulador);
    }

    $consulta_registros->free_result();
    $consulta_registros->close();
    fclose($file);


    if (ob_get_length()) {
        ob_end_clean();
    }

    header("Content-Disposition: attachment; filename=\"{$titulo_reporte}\"");
    header("Content-Type: text/csv; charset=UTF-8");
    header('Cache-Control: max-age=0');

    readfile($ruta);
    unlink($ruta);

    exit();
?>
