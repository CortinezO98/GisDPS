<?php
declare(strict_types=1);

// ————————————————————————————————————————————————————————————————
// monitoreos_reporte_excel.php
// Generación de reporte Excel para Calidad-Monitoreos
// Refactorizado con buenas prácticas sin alterar la lógica original
// ————————————————————————————————————————————————————————————————

namespace Calidad\Reportes;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

try {
    // Activar todas las advertencias para depuración (comentar en producción)
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    // Validación de permisos del usuario para el módulo
    $modulo_plataforma = 'Calidad-Monitoreos';
    require_once __DIR__ . '/../../app/config/config.php';
    require_once __DIR__ . '/../../app/config/db.php';
    require_once __DIR__ . '/../../app/config/security.php';

    // Autoload de PhpSpreadsheet (ajustado a la ruta real en templates/vendor)
    require_once __DIR__ . '/../vendor/autoload.php';

    // Solo procesar si viene el formulario
    if (!isset($_POST['reporte'])) {
        throw new \RuntimeException('Parámetros de reporte faltantes.');
    }

    // ———————————— Recolección y saneamiento de inputs ————————————
    $tipo_reporte   = validar_input($_POST['tipo_reporte']);
    $tipo_monitoreo = validar_input($_POST['tipo_monitoreo']);
    $fecha_inicio   = validar_input($_POST['fecha_inicio']);
    $fecha_fin      = validar_input($_POST['fecha_fin']) . ' 23:59:59';
    $id_matriz      = validar_input($_POST['id_matriz']);
    $agente         = validar_input($_POST['agente']);

    $titulo_reporte = sprintf(
        'Gestión-Calidad-Monitoreos-%s-%s.xlsx',
        $tipo_reporte,
        date('Y-m-d_H_i_s')
    );

    // ————————— Construcción dinámica de filtros SQL —————————
    $where  = ['1=1'];
    $params = [];

    if ($id_matriz !== 'Todas') {
        $where[]  = 'TMC.gcm_matriz = ?';
        $params[] = $id_matriz;
    }
    if ($tipo_monitoreo !== 'Todos') {
        $where[]  = 'TMC.gcm_tipo_monitoreo = ?';
        $params[] = $tipo_monitoreo;
    }
    if ($agente !== 'Todos') {
        $where[]  = 'TMC.gcm_analista = ?';
        $params[] = $agente;
    }

    // Siempre filtrar por rango de fecha
    $where[]  = 'TMC.gcm_registro_fecha >= ?';
    $params[] = $fecha_inicio;
    $where[]  = 'TMC.gcm_registro_fecha <= ?';
    $params[] = $fecha_fin;

    $sql = "
        SELECT
            TMC.gcm_id,
            TMC.gcm_matriz,
            TM.gcm_nombre_matriz,
            TMC.gcm_analista,
            TMC.gcm_fecha_hora_gestion,
            TMC.gcm_dependencia,
            TMC.gcm_identificacion_ciudadano,
            TMC.gcm_numero_transaccion,
            TMC.gcm_tipo_monitoreo,
            TMC.gcm_observaciones_monitoreo,
            TMC.gcm_nota_enc,
            TMC.gcm_nota_ecn,
            TMC.gcm_nota_ecuf,
            TMC.gcm_estado,
            TMC.gcm_solucion_contacto,
            TMC.gcm_causal_nosolucion,
            TMC.gcm_tipi_programa,
            TMC.gcm_tipi_tipificacion,
            TMC.gcm_subtipificacion,
            TMC.gcm_atencion_wow,
            TMC.gcm_aplica_voc,
            TMC.gcm_segmento,
            TMC.gcm_tabulacion_voc,
            TMC.gcm_voc,
            TMC.gcm_emocion_inicial,
            TMC.gcm_emocion_final,
            TMC.gcm_que_le_activo,
            TMC.gcm_atribuible,
            TMC.gcm_direcciones_misionales,
            TMC.gcm_programa,
            TMC.gcm_tipificacion,
            TMC.gcm_subtipificacion_1,
            TMC.gcm_subtipificacion_2,
            TMC.gcm_subtipificacion_3,
            TMC.gcm_observaciones_info,
            TMC.gcm_registro_usuario,
            TMC.gcm_registro_fecha,
            TUA.usu_nombres_apellidos AS analista_nombre,
            TS.usu_nombres_apellidos AS supervisor_nombre,
            TN1.gic1_item,
            TN2.gic2_item,
            TN3.gic3_item,
            TN4.gic4_item,
            TN5.gic5_item,
            TN6.gic6_item,
            TM.gcm_canal,
            TMC.gcm_fecha_reac_limite,
            TMC.gcm_fecha_reac,
            TMC.gcm_fecha_calidad_reac_limite,
            TMC.gcm_fecha_calidad_reac,
            TMC.gcm_fecha_snivel_reac_limite,
            TMC.gcm_fecha_snivel_reac,
            TMC.gcm_fecha_sreac_limite,
            TMC.gcm_fecha_sreac,
            TMC.gcm_fecha_novedad_inicio,
            TMC.gcm_fecha_novedad_fin,
            TMC.gcm_novedad_observaciones,
            TUA.usu_estado,
            TUA.usu_supervisor
        FROM gestion_calidad_monitoreo AS TMC
        LEFT JOIN gestion_calidad_matriz        AS TM  ON TMC.gcm_matriz                 = TM.gcm_id
        LEFT JOIN administrador_usuario         AS TUR ON TMC.gcm_registro_usuario      = TUR.usu_id
        LEFT JOIN administrador_usuario         AS TUA ON TMC.gcm_analista               = TUA.usu_id
        LEFT JOIN administrador_usuario         AS TS  ON TUA.usu_supervisor             = TS.usu_id
        LEFT JOIN gestion_interacciones_catnivel1 AS TN1 ON TMC.gcm_direcciones_misionales = TN1.gic1_id
        LEFT JOIN gestion_interacciones_catnivel2 AS TN2 ON TMC.gcm_programa               = TN2.gic2_id
        LEFT JOIN gestion_interacciones_catnivel3 AS TN3 ON TMC.gcm_tipificacion           = TN3.gic3_id
        LEFT JOIN gestion_interacciones_catnivel4 AS TN4 ON TMC.gcm_subtipificacion_1      = TN4.gic4_id
        LEFT JOIN gestion_interacciones_catnivel5 AS TN5 ON TMC.gcm_subtipificacion_2      = TN5.gic5_id
        LEFT JOIN gestion_interacciones_catnivel6 AS TN6 ON TMC.gcm_subtipificacion_3      = TN6.gic6_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY TMC.gcm_id
    ";

    $stmt = $enlace_db->prepare($sql);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    $registros = $stmt->get_result()->fetch_all(MYSQLI_NUM);

    // ————————— Construcción de filtro para historial —————————
    $histIds = array_column($registros, 0);
    $array_estado_historial = [];
    if (!empty($histIds)) {
        // Genera un WHERE ... IN (...) de manera segura
        $placeholders = implode(',', array_fill(0, count($histIds), '?'));
        $sqlHist = "
            SELECT gcmh_monitoreo, gcmh_tipo_cambio, gcmh_comentarios, gcmh_resarcimiento
            FROM gestion_calidad_monitoreo_historial
            WHERE gcmh_monitoreo IN ($placeholders)
        ";
        $histStmt = $enlace_db->prepare($sqlHist);
        $histStmt->bind_param(str_repeat('s', count($histIds)), ...$histIds);
        $histStmt->execute();
        $historial = $histStmt->get_result()->fetch_all(MYSQLI_NUM);

        // Inicializa y acumula comentarios
        foreach ($historial as [$mon, $tipo, $coment, $resar]) {
            $h =& $array_estado_historial[$mon];
            // Aseguro que existan todas las claves
            foreach (['Refutar','Aceptar','Refutar-Rechazado','Refutar-Aceptado','Refutar-Nivel 2','Refutar-Rechazado-Nivel 2','Refutar-Aceptado-Nivel 2','Resarcimiento'] as $k) {
                if (!isset($h[$k])) {
                    $h[$k] = '';
                }
            }
            // Concateno
            $h[$tipo]       .= $coment;
            $h['Resarcimiento'] .= $resar;
        }
    }

    // ————— En caso de reporte Consolidado-Matriz, cargo configuración de ítems —————
    $array_items_matriz = ['nombre'=>[], 'id'=>[], 'consecutivo'=>[], 'peso'=>[]];
    $array_respuestas   = [];
    if ($tipo_reporte === 'Consolidado-Matriz' && $id_matriz !== 'Todas') {
        // Ítems
        $sqlItems = "
            SELECT gcmi_id, gcmi_item_consecutivo, gcmi_descripcion, gcmi_peso, gcmi_calificable
            FROM gestion_calidad_matriz_item
            WHERE gcmi_matriz = ?
            ORDER BY gcmi_item_consecutivo ASC
        ";
        $itStmt = $enlace_db->prepare($sqlItems);
        $itStmt->bind_param('s', $id_matriz);
        $itStmt->execute();
        $items = $itStmt->get_result()->fetch_all(MYSQLI_NUM);

        foreach ($items as [$id, $cons, $desc, $peso, $calc]) {
            if ($calc === 'Si') {
                $array_items_matriz['id'][]           = $id;
                $array_items_matriz['consecutivo'][]  = (string)$cons;
                $array_items_matriz['consecutivo'][]  = '';
                $array_items_matriz['nombre'][]       = $desc;
                $array_items_matriz['nombre'][]       = 'Comentario';
                $array_items_matriz['peso'][]         = "{$peso}%";
                $array_items_matriz['peso'][]         = '';
            }
        }

        // Respuestas
        if (!empty($histIds)) {
            $placeholders = implode(',', array_fill(0, count($histIds), '?'));
            $sqlResp = "
                SELECT gcmc_monitoreo, gcmc_pregunta, gcmc_respuesta, gcmc_comentarios
                FROM gestion_calidad_monitoreo_calificaciones
                WHERE gcmc_monitoreo IN ($placeholders)
                ORDER BY gcmc_pregunta
            ";
            $rsStmt = $enlace_db->prepare($sqlResp);
            $rsStmt->bind_param(str_repeat('s', count($histIds)), ...$histIds);
            $rsStmt->execute();
            $respuestas = $rsStmt->get_result()->fetch_all(MYSQLI_NUM);

            foreach ($respuestas as [$mon, $preg, $resp, $com]) {
                $array_respuestas[$mon][$preg] = [
                    'respuesta'   => $resp,
                    'comentarios' => $com,
                ];
            }
        }
    }

    // ————————— Creación del Spreadsheet —————————
    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Reporte Gestión Calidad');

    $spreadsheet->getProperties()
        ->setCreator(APP_NAME_ALL)
        ->setLastModifiedBy($_SESSION[APP_SESSION . '_session_usu_nombre_completo'])
        ->setTitle(APP_NAME_ALL)
        ->setSubject(APP_NAME_ALL)
        ->setDescription(APP_NAME_ALL)
        ->setKeywords(APP_NAME_ALL)
        ->setCategory('Reporte');

    // Estilos
    require_once __DIR__ . '/../../includes/_excel-style.php';

    // ————— Encabezados de columna —————————
    $headers = [
        'A3'=>'Consecutivo','B3'=>'Doc. Agente','C3'=>'Agente','D3'=>'Segmento',
        'E3'=>'Responsable','F3'=>'Matriz','G3'=>'Canal','H3'=>'Dependencia',
        'I3'=>'Identificación Ciudadano','J3'=>'Número Transacción','K3'=>'Tipo Monitoreo',
        'L3'=>'Fecha Gestión','M3'=>'Nota ENC','N3'=>'Nota ECUF','O3'=>'Nota ECN',
        'P3'=>'Estado','Q3'=>'Solucionado primer contacto?','R3'=>'Causal NO solución',
        'S3'=>'Programa','T3'=>'Tipificación','U3'=>'Sub-Tipificación','V3'=>'Atención WOW',
        'W3'=>'Se presenta VOC','X3'=>'Segmento VOC','Y3'=>'Tabulación VOC','Z3'=>'VOC',
        'AA3'=>'Emoción inicial','AB3'=>'Emoción final','AC3'=>'Qué le activó','AD3'=>'Atribuible',
        'AE3'=>'Observaciones Generales','AF3'=>'Usuario Registro','AG3'=>'Fecha-Hora Registro',
        'AH3'=>'Obs. Refutar','AI3'=>'Obs. Aceptar','AJ3'=>'Obs. Refutar-Rech.','AK3'=>'Obs. Refutar-Acept.',
        'AL3'=>'Obs. Refutar-Nivel 2','AM3'=>'Obs. Refutar-Rechazado-N2','AN3'=>'Obs. Refutar-Aceptado-N2',
        'AO3'=>'Resarcimiento','AP3'=>'Estado Acep.','AQ3'=>'Estado Refutado','AR3'=>'Estado Ref.-N2','AS3'=>'Estado Acep.-N2'
    ];
    foreach ($headers as $cell => $text) {
        $sheet->setCellValue($cell, $text);
    }

    // Ancho de columnas A→AS
    foreach (array_merge(range('A','Z'), range('AA','AS')) as $col) {
        $sheet->getColumnDimension($col)->setWidth(20);
    }
    $sheet->getRowDimension(3)->setRowHeight(80);
    $sheet->getStyle('A3:AS3')->applyFromArray($styleArrayTitulos);
    $sheet->setAutoFilter('A3:AS3');
    $sheet->getStyle('3')->getAlignment()->setWrapText(true);

    // ————— Encabezados adicionales para Consolidado-Matriz —————————
    if ($tipo_reporte === 'Consolidado-Matriz' && $id_matriz !== 'Todas') {
        $base = 47;
        foreach ($array_items_matriz['nombre'] as $idx => $nombre) {
            $col = $array_columnas[$base + $idx];
            // Peso en fila 2
            $sheet->setCellValue("{$col}2", $array_items_matriz['peso'][$idx]);
            // Nombre en fila 3
            $sheet->setCellValue("{$col}3", $array_items_matriz['consecutivo'][$idx] . ' ' . $nombre);
            $sheet->getColumnDimension($col)->setWidth(20);
        }
    }

    // ————— Poblado de datos a partir de fila 4 —————————
    $fila_actual   = 4;
    $fecha_actual = date('Y-m-d H:i:s');

    foreach ($registros as $row) {
        // Datos fijos
        $sheet
            ->setCellValue("A{$fila_actual}", $row[0])
            ->setCellValue("B{$fila_actual}", $row[3])
            ->setCellValue("C{$fila_actual}", $row[37])
            ->setCellValue("D{$fila_actual}", $row[38])
            ->setCellValue("E{$fila_actual}", $row[39])
            ->setCellValue("F{$fila_actual}", $row[2])
            ->setCellValue("G{$fila_actual}", $row[47])
            ->setCellValue("H{$fila_actual}", $row[5])
            ->setCellValue("I{$fila_actual}", $row[6])
            ->setCellValue("J{$fila_actual}", $row[7])
            ->setCellValue("K{$fila_actual}", $row[8])
            ->setCellValue("L{$fila_actual}", $row[4])
            ->setCellValue("M{$fila_actual}", $row[10])
            ->setCellValue("N{$fila_actual}", $row[12])
            ->setCellValue("O{$fila_actual}", $row[11])
            ->setCellValue("P{$fila_actual}", $row[13])
            ->setCellValue("Q{$fila_actual}", $row[14])
            ->setCellValue("R{$fila_actual}", $row[15])
            ->setCellValue("S{$fila_actual}", $row[16])
            ->setCellValue("T{$fila_actual}", $row[17])
            ->setCellValue("U{$fila_actual}", $row[18])
            ->setCellValue("V{$fila_actual}", $row[19])
            ->setCellValue("W{$fila_actual}", $row[20])
            ->setCellValue("X{$fila_actual}", $row[21])
            ->setCellValue("Y{$fila_actual}", $row[22])
            ->setCellValue("Z{$fila_actual}", $row[23])
            ->setCellValue("AA{$fila_actual}", $row[24])
            ->setCellValue("AB{$fila_actual}", $row[25])
            ->setCellValue("AC{$fila_actual}", $row[26])
            ->setCellValue("AD{$fila_actual}", $row[27])
            ->setCellValue("AE{$fila_actual}", $row[9])
            ->setCellValue("AF{$fila_actual}", $row[40])
            ->setCellValue("AG{$fila_actual}", $row[36]);

        // Historial
        $mon = $row[0];
        foreach ([
            'Refutar','Aceptar','Refutar-Rechazado','Refutar-Aceptado',
            'Refutar-Nivel 2','Refutar-Rechazado-Nivel 2','Refutar-Aceptado-Nivel 2','Resarcimiento'
        ] as $idx => $key) {
            // Columns AH..AO
            $col = chr(ord('H') + 25 + $idx); // H+? => AH..AO
            $sheet->setCellValue("{$col}{$fila_actual}", $array_estado_historial[$mon][$key] ?? '');
        }

        // Fechas de vencimiento
        $estados = [];
        for ($k = 0; $k < 4; $k++) {
            $lim = $row[48 + $k*2] ?? '';
            $act = $row[49 + $k*2] ?? $fecha_actual;
            if ($lim !== '') {
                $estados[] = ($act >= $lim) ? 'VENCIDO' : 'NO VENCIDO';
            } else {
                $estados[] = '';
            }
        }
        // AP, AQ, AR, AS
        list($e1, $e2, $e3, $e4) = $estados;
        $sheet
            ->setCellValue("AP{$fila_actual}", $e1)
            ->setCellValue("AQ{$fila_actual}", $e2)
            ->setCellValue("AR{$fila_actual}", $e4)
            ->setCellValue("AS{$fila_actual}", $e3);

        // Respuestas de matriz
        if ($tipo_reporte === 'Consolidado-Matriz' && $id_matriz !== 'Todas') {
            $baseCol = 47;
            foreach ($array_items_matriz['id'] as $j => $itemId) {
                $colResp = $array_columnas[$baseCol + $j*2];
                $colCom  = $array_columnas[$baseCol + $j*2 + 1];
                $sheet->setCellValue("{$colResp}{$fila_actual}", $array_respuestas[$mon][$itemId]['respuesta']   ?? '');
                $sheet->setCellValue("{$colCom}{$fila_actual}",  $array_respuestas[$mon][$itemId]['comentarios'] ?? '');
            }
        }

        $fila_actual++;
    }

    // ————— Envío del archivo al navegador —————————
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $titulo_reporte . '"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');

} catch (\Throwable $e) {
    error_log('Error al generar reporte Excel: ' . $e->getMessage());
    http_response_code(500);
    exit('Ocurrió un error al generar el reporte. Consulte con el administrador.');
}
