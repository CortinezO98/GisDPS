<?php
  // Validación de permisos del usuario para el módulo
  $modulo_plataforma = "Interacciones";
  error_reporting(E_ALL);
  ini_set('display_errors','1');

  require_once('../../app/config/config.php');
  require_once('../../app/config/db.php');
  require_once('../../app/config/security.php');

  if (!isset($_POST['reporte'])) {
    http_response_code(400);
    exit('Solicitud inválida');
  }

  // 1) Sanitizar inputs
  $tipo           = validar_input($_POST['tipo']);              // "2025-06"
  $canal_atencion = validar_input($_POST['canal_atencion']);    // "Todos" o canal específico
  $ini_mes        = "{$tipo}-01";                               // "2025-06-01"
  $fin_mes        = "{$tipo}-" . date('t', strtotime($ini_mes)); // "2025-06-30"
  
  // Rango personalizado (si lo rellenaron)
  $f_ini_custom = trim($_POST['fecha_inicio']);
  $f_fin_custom = trim($_POST['fecha_fin']);

  // Montar rango final
  $fecha_inicio = ($f_ini_custom !== '') ? $f_ini_custom : $ini_mes;
  $fecha_fin    = ($f_fin_custom !== '') ? "{$f_fin_custom} 23:59:59" : "{$fin_mes} 23:59:59";

  // Nombre del archivo
  $titulo_reporte = "Gestión_Interacciones_" . date('Ymd_His') . ".csv";
  $ruta = __DIR__ . "/storage/{$titulo_reporte}";

  // 2) Construir filtros dinámicos
  $params = [];
  $types  = '';

  // Always filtro por registro_fecha entre fechas
  $query_filtros = " WHERE `gi_registro_fecha` >= ? AND `gi_registro_fecha` <= ?";
  $params[] = $fecha_inicio;
  $params[] = $fecha_fin;
  $types .= 'ss';

  // Canal?
  if ($canal_atencion !== 'Todos') {
    $query_filtros .= " AND `gi_canal_atencion` = ?";
    $params[] = $canal_atencion;
    $types .= 's';
  }

  // 3) Query principal
  $sql = "
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
      TC.`ciu_departamento`,
      TC.`ciu_municipio`,
      TI.`gi_direccion`,
      TI.`gi_celular`,
      TI.`gi_telefono`,
      TI.`gi_email`,
      TI.`gi_beneficiario`,
      TN1.`gic1_item`,
      TN2.`gic2_item`,
      TN3.`gic3_item`,
      TN4.`gic4_item`,
      TN5.`gic5_item`,
      TN6.`gic6_item`,
      TI.`gi_consulta`,
      TI.`gi_respuesta`,
      TI.`gi_resultado`,
      TI.`gi_descripcion_resultado`,
      TI.`gi_complemento_resultado`,
      TI.`gi_sms`,
      TI.`gi_id_encuesta`,
      TU.`usu_nombres_apellidos`,
      TI.`gi_registro_usuario`,
      TI.`gi_registro_fecha`
    FROM `gestion_interacciones_historico` AS TI
    LEFT JOIN `administrador_ciudades` AS TC
      ON TI.`gi_municipio` = TC.`ciu_codigo`
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
    LEFT JOIN `gestion_interacciones_encuestas` AS TE
      ON TI.`gi_id_encuesta` = TE.`gie_id`
    LEFT JOIN `administrador_usuario` AS TU
      ON TI.`gi_registro_usuario` = TU.`usu_id`
    {$query_filtros}
    ORDER BY TI.`gi_registro_fecha` ASC
  ";

  $stmt = $enlace_db->prepare($sql);
  if ($stmt === false) {
    http_response_code(500);
    exit("Error al preparar la consulta: " . $enlace_db->error);
  }
  if (count($params)) {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $result = $stmt->get_result();

  // 4) Crear CSV
  $del = ';';
  $enc = '"';
  if (!is_dir(__DIR__.'/storage')) {
    mkdir(__DIR__.'/storage', 0755, true);
  }
  $fp = fopen($ruta, 'w');
  // BOM para Excel
  fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

  // Cabecera
  fputcsv($fp, ['Reporte: Gestión Interacciones'], $del, $enc);
  fputcsv($fp, ["Rango: {$fecha_inicio}  a  {$fecha_fin}"], $del, $enc);

  // Títulos de columnas
  $headers = [
    'ID','Registro','Caso',
    'Tipo doc','Identificación','1er Nombre','2º Nombre','1er Apellido','2º Apellido',
    'Nacim.','Edad','Depto','Municipio','Dirección','Celular','Teléfono','Email',
    'Beneficiario',
    'Tip1','Tip2','Tip3','Tip4','Tip5','Tip6',
    'Consulta','Respuesta','Resultado','Desc. Resultado','Complemento',
    'SMS','Encuesta','Usuario','ID Usuario','Fecha Registro'
  ];
  fputcsv($fp, $headers, $del, $enc);

  // Filas
  while ($row = $result->fetch_row()) {
    fputcsv($fp, $row, $del, $enc);
  }
  fclose($fp);

  // 5) Entregar descarga
  header('Content-Type: text/csv; charset=UTF-8');
  header("Content-Disposition: attachment; filename=\"{$titulo_reporte}\"");
  header('Cache-Control: max-age=0');
  readfile($ruta);
  // opcional: borrar después
  @unlink($ruta);
  exit;
