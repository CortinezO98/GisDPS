<?php
// Monitoreos - Reporte Excel (remediado sin cambiar funcionalidad)

// Validación de permisos del usuario para el módulo
$modulo_plataforma = "Calidad-Monitoreos";

require_once('../../app/config/config.php');
require_once("../../app/config/db.php");
require_once("../../app/config/security.php");

// PhpSpreadsheet
require_once('../assets/plugins/PhpSpreadsheet/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Guardas defensivas (no alteran la lógica original)

// No procesar generación si hacen HEAD (como curl -I)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'HEAD') {
    http_response_code(200);
    exit;
}

// Sesión y acceso (mantiene el 302 a login como en comportamiento actual)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION[APP_SESSION . '_session_usu_id']) && empty($_SESSION['usuario_id'])) {
    header('Location: ../login');
    exit;
}

// Helper seguro para conteos
if (!function_exists('safe_count')) {
    function safe_count($v): int { return is_countable($v) ? count($v) : 0; }
}

// Inicializaciones para evitar "Undefined ..."
$titulo_reporte = "Gestion-Calidad-Monitoreos-" . date('Y-m-d_H_i_s') . ".xlsx";
$tipo_reporte   = null;
$tipo_monitoreo = null;
$fecha_inicio   = null;
$fecha_fin      = null;
$id_matriz      = null;
$agente         = null;

$data_consulta = [];
$data_consulta_registros = [];
$resultado_registros = [];                 
$resultado_registros_historial = [];       
$resultado_registros_matriz = [];          
$resultado_registros_respuesta = [];       

$array_estado_historial = [];
$array_estado_historial_date = [];
$array_items_matriz = [
    'nombre' => [],
    'id' => [],
    'consecutivo' => [],
    'peso' => [],
];
$array_respuestas = [];

$filtro_monitoreos = '';
$filtro_monitoreos_matriz = '';

if (!isset($_POST["reporte"])) {
    header('Location: ../login');
    exit;
}

// Entrada validada
$tipo_reporte   = validar_input($_POST['tipo_reporte']   ?? '');
$tipo_monitoreo = validar_input($_POST['tipo_monitoreo'] ?? '');
$fecha_inicio   = validar_input($_POST['fecha_inicio']   ?? '');
$fecha_fin      = validar_input(($_POST['fecha_fin']     ?? '')) . ' 23:59:59';
$id_matriz      = validar_input($_POST['id_matriz']      ?? '');
$agente         = validar_input($_POST['agente']         ?? '');

$titulo_reporte = "Gestión Calidad-Monitoreos-" . $tipo_reporte . "-" . date('Y-m-d H_i_s') . ".xlsx";

// Filtros
$filtro_id_matriz = "";
if ($id_matriz === 'Todas' || $id_matriz === '') {
    $filtro_id_matriz = "";
} else {
    $filtro_id_matriz = " AND TMC.`gcm_matriz`=?";
    $data_consulta_registros[] = $id_matriz;
}

$filtro_tipo = "";
if ($tipo_monitoreo === 'Todos' || $tipo_monitoreo === '') {
    $filtro_tipo = "";
} else {
    $filtro_tipo = " AND TMC.`gcm_tipo_monitoreo`=?";
    $data_consulta_registros[] = $tipo_monitoreo;
}

$filtro_agente = "";
if ($agente === 'Todos' || $agente === '') {
    $filtro_agente = "";
} else {
    $filtro_agente = " AND TMC.`gcm_analista`=?";
    $data_consulta_registros[] = $agente;
}

// Rango fechas (requerido para la consulta)
$data_consulta_registros[] = $fecha_inicio;
$data_consulta_registros[] = $fecha_fin;

// Consulta principal
$consulta_string = "SELECT 
    TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, 
    TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, 
    TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, 
    TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, 
    TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, 
    TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, 
    TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, 
    TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, 
    TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, 
    TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, 
    TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, 
    TUA.`usu_nombres_apellidos`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, 
    TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, 
    TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal`, 
    TMC.`gcm_fecha_reac_limite`, TMC.`gcm_fecha_reac`, 
    TMC.`gcm_fecha_calidad_reac_limite`, TMC.`gcm_fecha_calidad_reac`, 
    TMC.`gcm_fecha_snivel_reac_limite`, TMC.`gcm_fecha_snivel_reac`, 
    TMC.`gcm_fecha_sreac_limite`, TMC.`gcm_fecha_sreac`, 
    TMC.`gcm_fecha_novedad_inicio`, TMC.`gcm_fecha_novedad_fin`, TMC.`gcm_novedad_observaciones`, 
    TUA.`usu_estado`, TUA.`usu_supervisor`
FROM `gestion_calidad_monitoreo` AS TMC 
LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` 
LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` 
LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` 
LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` 
LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` 
LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` 
LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` 
LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` 
LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` 
LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` 
WHERE 1=1 {$filtro_id_matriz} {$filtro_tipo} {$filtro_agente} 
  AND TMC.`gcm_registro_fecha`>=? AND TMC.`gcm_registro_fecha`<=?
ORDER BY `gcm_id`";

$consulta_registros = $enlace_db->prepare($consulta_string);
if (safe_count($data_consulta_registros) > 0) {
    $consulta_registros->bind_param(str_repeat("s", safe_count($data_consulta_registros)), ...$data_consulta_registros);
}
$consulta_registros->execute();
$resultado = $consulta_registros->get_result();
if ($resultado) {
    $resultado_registros = $resultado->fetch_all(MYSQLI_NUM);
} else {
    $resultado_registros = [];
}

// Arma filtros con IDs resultantes para consultas relacionadas
if (safe_count($resultado_registros) > 0) {
    foreach ($resultado_registros as $row) {
        // $row[0] es gcm_id
        $filtro_monitoreos        .= "`gcmh_monitoreo`=? OR ";
        $filtro_monitoreos_matriz .= "`gcmc_monitoreo`=? OR ";
        $data_consulta[] = $row[0];
    }
    $filtro_monitoreos        = "AND (" . substr($filtro_monitoreos, 0, -4) . ")";
    $filtro_monitoreos_matriz = "AND (" . substr($filtro_monitoreos_matriz, 0, -4) . ")";
}

// Historial de monitoreos
$consulta_string_historial = "SELECT 
    `gcmh_id`, `gcmh_monitoreo`, `gcmh_tipo_cambio`, `gcmh_comentarios`, 
    `gcmh_registro_usuario`, `gcmh_registro_fecha`, `gcmh_resarcimiento`
FROM `gestion_calidad_monitoreo_historial`
WHERE 1=1 {$filtro_monitoreos}";

$consulta_registros_historial = $enlace_db->prepare($consulta_string_historial);
if (safe_count($data_consulta) > 0) {
    $consulta_registros_historial->bind_param(str_repeat("s", safe_count($data_consulta)), ...$data_consulta);
}
$consulta_registros_historial->execute();
$res_hist = $consulta_registros_historial->get_result();
if ($res_hist) {
    $resultado_registros_historial = $res_hist->fetch_all(MYSQLI_NUM);
} else {
    $resultado_registros_historial = [];
}

foreach ($resultado_registros_historial as $h) {
    $monitoreo_id   = $h[1];
    $tipo_cambio    = $h[2]; // 'Refutar', 'Aceptar', etc.
    $comentario     = $h[3];
    $fecha_evento   = $h[5];
    $resarcimiento  = $h[6];

    // Inicializar llaves esperadas
    $array_estado_historial[$monitoreo_id]['Refutar']                        = $array_estado_historial[$monitoreo_id]['Refutar']                        ?? "";
    $array_estado_historial[$monitoreo_id]['Aceptar']                        = $array_estado_historial[$monitoreo_id]['Aceptar']                        ?? "";
    $array_estado_historial[$monitoreo_id]['Refutar-Rechazado']              = $array_estado_historial[$monitoreo_id]['Refutar-Rechazado']              ?? "";
    $array_estado_historial[$monitoreo_id]['Refutar-Aceptado']               = $array_estado_historial[$monitoreo_id]['Refutar-Aceptado']               ?? "";
    $array_estado_historial[$monitoreo_id]['Refutar-Nivel 2']                = $array_estado_historial[$monitoreo_id]['Refutar-Nivel 2']                ?? "";
    $array_estado_historial[$monitoreo_id]['Refutar-Rechazado-Nivel 2']      = $array_estado_historial[$monitoreo_id]['Refutar-Rechazado-Nivel 2']      ?? "";
    $array_estado_historial[$monitoreo_id]['Refutar-Aceptado-Nivel 2']       = $array_estado_historial[$monitoreo_id]['Refutar-Aceptado-Nivel 2']       ?? "";
    $array_estado_historial[$monitoreo_id]['Aceptar-ODM']                    = $array_estado_historial[$monitoreo_id]['Aceptar-ODM']                    ?? "";
    $array_estado_historial[$monitoreo_id]['Resarcimiento']                  = $array_estado_historial[$monitoreo_id]['Resarcimiento']                  ?? "";

    $array_estado_historial[$monitoreo_id]['Resarcimiento'] .= $resarcimiento ?? '';
    if (!empty($tipo_cambio)) {
        $array_estado_historial[$monitoreo_id][$tipo_cambio] = ($array_estado_historial[$monitoreo_id][$tipo_cambio] ?? '') . $comentario;
    }

    // Fechas por tipo
    $array_estado_historial_date[$monitoreo_id]['Refutar']                        = $array_estado_historial_date[$monitoreo_id]['Refutar']                        ?? "";
    $array_estado_historial_date[$monitoreo_id]['Aceptar']                        = $array_estado_historial_date[$monitoreo_id]['Aceptar']                        ?? "";
    $array_estado_historial_date[$monitoreo_id]['Refutar-Rechazado']              = $array_estado_historial_date[$monitoreo_id]['Refutar-Rechazado']              ?? "";
    $array_estado_historial_date[$monitoreo_id]['Refutar-Aceptado']               = $array_estado_historial_date[$monitoreo_id]['Refutar-Aceptado']               ?? "";
    $array_estado_historial_date[$monitoreo_id]['Refutar-Nivel 2']                = $array_estado_historial_date[$monitoreo_id]['Refutar-Nivel 2']                ?? "";
    $array_estado_historial_date[$monitoreo_id]['Refutar-Rechazado-Nivel 2']      = $array_estado_historial_date[$monitoreo_id]['Refutar-Rechazado-Nivel 2']      ?? "";
    $array_estado_historial_date[$monitoreo_id]['Refutar-Aceptado-Nivel 2']       = $array_estado_historial_date[$monitoreo_id]['Refutar-Aceptado-Nivel 2']       ?? "";
    $array_estado_historial_date[$monitoreo_id]['Aceptar-ODM']                    = $array_estado_historial_date[$monitoreo_id]['Aceptar-ODM']                    ?? "";

    if (!empty($tipo_cambio)) {
        $array_estado_historial_date[$monitoreo_id][$tipo_cambio] = $fecha_evento;
    }
}

// Si Consolidado-Matriz e id_matriz != Todas → traer items y respuestas
if ($tipo_reporte === 'Consolidado-Matriz' && $id_matriz !== "Todas" && $id_matriz !== "") {
    // Items de la matriz
    $consulta_string_matriz = "SELECT 
        `gcmi_id`, `gcmi_matriz`, `gcmi_item_tipo`, `gcmi_item_consecutivo`, 
        `gcmi_item_orden`, `gcmi_descripcion`, `gcmi_peso`, `gcmi_calificable`, 
        `gcmi_grupo_peso`, `gcmi_visible`
    FROM `gestion_calidad_matriz_item`
    WHERE `gcmi_matriz`=?
    ORDER BY `gcmi_item_consecutivo` ASC";

    $consulta_registros_matriz = $enlace_db->prepare($consulta_string_matriz);
    $consulta_registros_matriz->bind_param('s', $id_matriz);
    $consulta_registros_matriz->execute();
    $res_matriz = $consulta_registros_matriz->get_result();
    if ($res_matriz) {
        $resultado_registros_matriz = $res_matriz->fetch_all(MYSQLI_NUM);
    } else {
        $resultado_registros_matriz = [];
    }

    foreach ($resultado_registros_matriz as $rm) {
        // índice: [7] calificable, [9] visible
        if (($rm[7] ?? '') === "Si") {
            $array_items_matriz['nombre'][]      = $rm[5] ?? '';
            $array_items_matriz['nombre'][]      = "Comentario";
            $array_items_matriz['id'][]          = $rm[0] ?? '';
            $array_items_matriz['consecutivo'][] = $rm[3] ?? '';
            $array_items_matriz['consecutivo'][] = "";
            $array_items_matriz['peso'][]        = ($rm[6] ?? '') . "%";
            $array_items_matriz['peso'][]        = "";
        }
    }

    // Respuestas por monitoreo+pregunta
    $consulta_string_respuesta = "SELECT 
        `gcmc_id`, `gcmc_monitoreo`, `gcmc_pregunta`, `gcmc_respuesta`, 
        `gcmc_afectaciones`, `gcmc_comentarios`, 
        TIM.`gcmi_matriz`, TIM.`gcmi_item_tipo`, TIM.`gcmi_item_consecutivo`, 
        TIM.`gcmi_item_orden`, TIM.`gcmi_descripcion`, TIM.`gcmi_peso`, TIM.`gcmi_calificable`
    FROM `gestion_calidad_monitoreo_calificaciones`
    LEFT JOIN `gestion_calidad_matriz_item` AS TIM 
        ON `gestion_calidad_monitoreo_calificaciones`.`gcmc_pregunta`=TIM.`gcmi_id`
    WHERE 1=1 {$filtro_monitoreos_matriz}
    ORDER BY TIM.`gcmi_item_consecutivo` ASC";

    $consulta_registros_respuesta = $enlace_db->prepare($consulta_string_respuesta);
    if (safe_count($data_consulta) > 0) {
        $consulta_registros_respuesta->bind_param(str_repeat("s", safe_count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_respuesta->execute();
    $res_resp = $consulta_registros_respuesta->get_result();
    if ($res_resp) {
        $resultado_registros_respuesta = $res_resp->fetch_all(MYSQLI_NUM);
    } else {
        $resultado_registros_respuesta = [];
    }

    foreach ($resultado_registros_respuesta as $rr) {
        $monitoreo = $rr[1] ?? null;
        $pregunta  = $rr[2] ?? null;
        $calificable = $rr[12] ?? '';
        if ($calificable === "Si" && $monitoreo && $pregunta) {
            $array_respuestas[$monitoreo][$pregunta]['respuesta']  = $rr[3] ?? '';
            $array_respuestas[$monitoreo][$pregunta]['comentarios'] = $rr[5] ?? '';
        }
    }
}

// Construcción del Excel
$spreadsheet = new Spreadsheet();

// Establecer propiedades
$spreadsheet->getProperties()
    ->setCreator(APP_NAME_ALL)
    ->setLastModifiedBy($_SESSION[APP_SESSION.'_session_usu_nombre_completo'] ?? 'Usuario')
    ->setTitle(APP_NAME_ALL)
    ->setSubject(APP_NAME_ALL)
    ->setDescription(APP_NAME_ALL)
    ->setKeywords(APP_NAME_ALL)
    ->setCategory("Reporte");

// Estilos (usa tu include original)
require_once("../../includes/_excel-style.php");

// Activar hoja 0
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->setTitle('Reporte Gestión Calidad');

// Estilos / tamaños
$spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(80);
foreach (range('A','Z') as $col) { $spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth(20); }
// Columnas AA..AU
$extraCols = ['AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU'];
foreach ($extraCols as $col) { $spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth(20); }

$spreadsheet->getActiveSheet()->getStyle('A3:AU3')->applyFromArray($styleArrayTitulos);

// AutoFilter según tipo
if ($tipo_reporte === 'Consolidado') {
    $spreadsheet->getActiveSheet()->setAutoFilter('A3:AU3');
} elseif ($tipo_reporte === 'Consolidado-Matriz' && $id_matriz !== "Todas") {
    // $array_columnas debe venir de _excel-style.php (mantiene comportamiento actual)
    $totalCols = (isset($array_items_matriz['nombre']) ? safe_count($array_items_matriz['nombre']) : 0) + 49;
    $spreadsheet->getActiveSheet()->getStyle('A3:' . ($array_columnas[$totalCols] ?? 'AU') . '3')->applyFromArray($styleArrayTitulos);
    $spreadsheet->getActiveSheet()->setAutoFilter('A3:' . ($array_columnas[$totalCols] ?? 'AU') . '3');
}
$spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);

// Títulos
$sheet->setCellValue('A3','Consecutivo');
$sheet->setCellValue('B3','Doc. Agente');
$sheet->setCellValue('C3','Agente');
$sheet->setCellValue('D3','Segmento');
$sheet->setCellValue('E3','Responsable');
$sheet->setCellValue('F3','Matriz');
$sheet->setCellValue('G3','Canal');
$sheet->setCellValue('H3','Dependencia');
$sheet->setCellValue('I3','Identificación Ciudadano');
$sheet->setCellValue('J3','Número Transacción');
$sheet->setCellValue('K3','Tipo Monitoreo');
$sheet->setCellValue('L3','Fecha Gestión');
$sheet->setCellValue('M3','Nota ENC');
$sheet->setCellValue('N3','Nota ECUF');
$sheet->setCellValue('O3','Nota ECN');
$sheet->setCellValue('P3','Estado');
$sheet->setCellValue('Q3','Solucionado primer contacto?');
$sheet->setCellValue('R3','Causal NO solución');
$sheet->setCellValue('S3','Programa');
$sheet->setCellValue('T3','Tipificación');
$sheet->setCellValue('U3','Sub-Tipificación');
$sheet->setCellValue('V3','Atención WOW');
$sheet->setCellValue('W3','Se presenta VOC (Voz Orientada al Ciudadano)');
$sheet->setCellValue('X3','Segmento');
$sheet->setCellValue('Y3','Tabulación VOC (Voz Orientada al Ciudadano)');
$sheet->setCellValue('Z3','VOC (Voz Orientada al Ciudadano)');
$sheet->setCellValue('AA3','VOC (Voz Orientada al Ciudadano) Emoción inicial');
$sheet->setCellValue('AB3','VOC (Voz Orientada al Ciudadano) Emoción final');
$sheet->setCellValue('AC3','Qué le activó');
$sheet->setCellValue('AD3','Atribuible');
$sheet->setCellValue('AE3','Observaciones Generales');
$sheet->setCellValue('AF3','Usuario Registro');
$sheet->setCellValue('AG3','Fecha-Hora Registro');

$sheet->setCellValue('AH3','Observaciones Refutar');
$sheet->setCellValue('AI3','Observaciones Aceptar');
$sheet->setCellValue('AJ3','Observaciones Refutar-Rechazado');
$sheet->setCellValue('AK3','Observaciones Refutar-Aceptado');
$sheet->setCellValue('AL3','Observaciones Refutar-Nivel 2');
$sheet->setCellValue('AM3','Observaciones Refutar-Rechazado-Nivel 2');
$sheet->setCellValue('AN3','Observaciones Refutar-Aceptado-Nivel 2');
$sheet->setCellValue('AO3','Resarcimiento');
$sheet->setCellValue('AP3','Estado Aceptar');
$sheet->setCellValue('AQ3','Estado Refutado');
$sheet->setCellValue('AR3','Estado Refutado-Nivel 2');
$sheet->setCellValue('AS3','Estado Aceptar-Nivel 2');
$sheet->setCellValue('AT3','ODM');
$sheet->setCellValue('AU3','Fecha y Hora de Cierre');

// Encabezados dinámicos de ítems si procede
if ($tipo_reporte === 'Consolidado-Matriz' && $id_matriz !== "Todas") {
    $nombres = $array_items_matriz['nombre'] ?? [];
    $pesos   = $array_items_matriz['peso'] ?? [];
    $consecs = $array_items_matriz['consecutivo'] ?? [];

    for ($i = 49; $i < 49 + safe_count($nombres); $i++) {
        $idx = $i - 49;
        $nombre_final = ($consecs[$idx] ?? '') . " " . ($nombres[$idx] ?? '');
        if (isset($array_columnas[$i])) {
            $sheet->setCellValue($array_columnas[$i] . '2', $pesos[$idx] ?? '');
            $sheet->setCellValue($array_columnas[$i] . '3', $nombre_final);
            $sheet->getColumnDimension($array_columnas[$i])->setWidth(20);
        }
    }
}

// Data
$fecha_actual = date('Y-m-d H:i:s');
$filas = safe_count($resultado_registros);

for ($i = 4; $i < $filas + 4; $i++) {
    $r = $resultado_registros[$i - 4];

    // Campos base por índice (se conservan tal cual estaban)
    $sheet->setCellValue('A'.$i,  $r[0]  ?? '');
    $sheet->setCellValue('B'.$i,  $r[3]  ?? '');
    $sheet->setCellValue('C'.$i,  $r[37] ?? '');
    $sheet->setCellValue('D'.$i,  $r[38] ?? '');
    $sheet->setCellValue('E'.$i,  $r[39] ?? '');
    $sheet->setCellValue('F'.$i,  $r[2]  ?? '');
    $sheet->setCellValue('G'.$i,  $r[47] ?? '');
    $sheet->setCellValue('H'.$i,  $r[5]  ?? '');
    $sheet->setCellValue('I'.$i,  $r[6]  ?? '');
    $sheet->setCellValue('J'.$i,  $r[7]  ?? '');
    $sheet->setCellValue('K'.$i,  $r[8]  ?? '');
    $sheet->setCellValue('L'.$i,  $r[4]  ?? '');
    $sheet->setCellValue('M'.$i,  $r[10] ?? '');
    $sheet->setCellValue('N'.$i,  $r[12] ?? '');
    $sheet->setCellValue('O'.$i,  $r[11] ?? '');
    $sheet->setCellValue('P'.$i,  $r[13] ?? '');
    $sheet->setCellValue('Q'.$i,  $r[14] ?? '');
    $sheet->setCellValue('R'.$i,  $r[15] ?? '');
    $sheet->setCellValue('S'.$i,  $r[16] ?? '');
    $sheet->setCellValue('T'.$i,  $r[17] ?? '');
    $sheet->setCellValue('U'.$i,  $r[18] ?? '');
    $sheet->setCellValue('V'.$i,  $r[19] ?? '');
    $sheet->setCellValue('W'.$i,  $r[20] ?? '');
    $sheet->setCellValue('X'.$i,  $r[21] ?? '');
    $sheet->setCellValue('Y'.$i,  $r[22] ?? '');
    $sheet->setCellValue('Z'.$i,  $r[23] ?? '');
    $sheet->setCellValue('AA'.$i, $r[24] ?? '');
    $sheet->setCellValue('AB'.$i, $r[25] ?? '');
    $sheet->setCellValue('AC'.$i, $r[26] ?? '');
    $sheet->setCellValue('AD'.$i, $r[27] ?? '');
    $sheet->setCellValue('AE'.$i, $r[9]  ?? '');
    $sheet->setCellValue('AF'.$i, $r[40] ?? '');
    $sheet->setCellValue('AG'.$i, $r[36] ?? '');

    $idMon = $r[0] ?? null;

    $sheet->setCellValue('AH'.$i, $array_estado_historial[$idMon]['Refutar']                         ?? '');
    $sheet->setCellValue('AI'.$i, $array_estado_historial[$idMon]['Aceptar']                         ?? '');
    $sheet->setCellValue('AJ'.$i, $array_estado_historial[$idMon]['Refutar-Rechazado']               ?? '');
    $sheet->setCellValue('AK'.$i, $array_estado_historial[$idMon]['Refutar-Aceptado']                ?? '');
    $sheet->setCellValue('AL'.$i, $array_estado_historial[$idMon]['Refutar-Nivel 2']                 ?? '');
    $sheet->setCellValue('AM'.$i, $array_estado_historial[$idMon]['Refutar-Rechazado-Nivel 2']       ?? '');
    $sheet->setCellValue('AN'.$i, $array_estado_historial[$idMon]['Refutar-Aceptado-Nivel 2']        ?? '');
    $sheet->setCellValue('AO'.$i, $array_estado_historial[$idMon]['Resarcimiento']                   ?? '');

    $estado_vencimiento_1 = '';
    $estado_vencimiento_2 = '';
    $estado_vencimiento_3 = '';
    $estado_vencimiento_4 = '';

    if (!empty($r[48])) {
        $limite_tiempo_1 = $r[48];
        $tiempo_1 = !empty($r[49]) ? $r[49] : $fecha_actual;
        $estado_vencimiento_1 = ($tiempo_1 >= $limite_tiempo_1) ? 'VENCIDO' : 'NO VENCIDO';
    }

    if (!empty($r[50])) {
        $limite_tiempo_2 = $r[50];
        $tiempo_2 = !empty($r[51]) ? $r[51] : $fecha_actual;
        $estado_vencimiento_2 = ($tiempo_2 >= $limite_tiempo_2) ? 'VENCIDO' : 'NO VENCIDO';
    }

    if (!empty($r[52])) {
        $limite_tiempo_3 = $r[52];
        $tiempo_3 = !empty($r[53]) ? $r[53] : $fecha_actual;
        $estado_vencimiento_3 = ($tiempo_3 >= $limite_tiempo_3) ? 'VENCIDO' : 'NO VENCIDO';
    }

    if (!empty($r[54])) {
        $limite_tiempo_4 = $r[54];
        $tiempo_4 = !empty($r[55]) ? $r[55] : $fecha_actual;
        $estado_vencimiento_4 = ($tiempo_4 >= $limite_tiempo_4) ? 'VENCIDO' : 'NO VENCIDO';
    }

    $fecha_cierre = '';
    if (!empty($array_estado_historial_date[$idMon]['Aceptar'])) {
        $fecha_cierre = $array_estado_historial_date[$idMon]['Aceptar'];
    }

    $sheet->setCellValue('AP'.$i, $estado_vencimiento_1);
    $sheet->setCellValue('AQ'.$i, $estado_vencimiento_2);
    $sheet->setCellValue('AR'.$i, $estado_vencimiento_4);
    $sheet->setCellValue('AS'.$i, $estado_vencimiento_3);
    $sheet->setCellValue('AT'.$i, $array_estado_historial[$idMon]['Aceptar-ODM'] ?? '');
    $sheet->setCellValue('AU'.$i, $fecha_cierre);

    // Respuestas de ítems si Consolidado-Matriz
    if ($tipo_reporte === 'Consolidado-Matriz' && $id_matriz !== "Todas") {
        $idsItems = $array_items_matriz['id'] ?? [];
        $columna_respuesta  = 49;
        $columna_comentario = 50;

        foreach ($idsItems as $idItem) {
            $resp = $array_respuestas[$idMon][$idItem]['respuesta']  ?? '';
            $com  = $array_respuestas[$idMon][$idItem]['comentarios'] ?? '';

            if (isset($array_columnas[$columna_respuesta])) {
                $sheet->setCellValue($array_columnas[$columna_respuesta] . $i, $resp);
            }
            if (isset($array_columnas[$columna_comentario])) {
                $sheet->setCellValue($array_columnas[$columna_comentario] . $i, $com);
            }

            $columna_respuesta  += 2;
            $columna_comentario += 2;
        }
    }
}

// Salida XLSX
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $titulo_reporte . '"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
