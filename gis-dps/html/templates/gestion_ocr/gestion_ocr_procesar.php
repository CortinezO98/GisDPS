<?php
$modulo_plataforma = "Administrador";
require_once("/var/www/html/iniciador.php");


error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

function safe_log($msg) {
    error_log('[OCR_PROC] ' . $msg);
}

$ruta_pendientes  = "/var/www/html/templates/gestion_ocr/storage_pendientes/";
$ruta_procesados  = "/var/www/html/templates/gestion_ocr/storage_procesado/";
$ruta_error       = "/var/www/html/templates/gestion_ocr/storage_error/";

$lista_archivo = @scandir($ruta_pendientes);
if ($lista_archivo === false) {
    safe_log("No se pudo leer directorio de pendientes: {$ruta_pendientes}");
    exit;
}

$sentencia_insert = $enlace_db->prepare("
    INSERT INTO `gestion_ocr_consolidado`(
        `ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`,
        `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`,
        `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`,
        `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`,
        `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`,
        `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`,
        `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$sentencia_insert_resultado = $enlace_db->prepare("
    INSERT INTO `gestion_ocr_resultado`(
        `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`,
        `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`,
        `ocrr_gestion_intentos`, `ocrr_gestion_correo`, `ocrr_gestion_observaciones`, `ocrr_gestion_fecha`,
        `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`,
        `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`,
        `ocrr_gestion_llamada_tipificacion`, `ocrr_gestion_llamada_id`, `ocrr_sr_fecha`, `ocrr_sr_observaciones`
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$consulta_string = "
    SELECT `ocr_id`, `ocr_codbeneficiario`, `ocr_cabezadefamilia`, `ocr_fechanacimiento`
    FROM `gestion_ocr`
    WHERE `ocr_codfamilia` = ? AND `ocr_documento` = ?
";
$consulta_registros = $enlace_db->prepare($consulta_string);

$consulta_actualizar = $enlace_db->prepare("
    UPDATE `gestion_ocr`
    SET `ocr_consolida_estado` = ?, `ocr_consolida_fecha` = ?
    WHERE `ocr_id` = ?
");

$sentencia_delete_consolidado = $enlace_db->prepare("
    DELETE FROM `gestion_ocr_consolidado` WHERE `ocrc_cod_familia` = ?
");
$sentencia_delete_resultado = $enlace_db->prepare("
    DELETE FROM `gestion_ocr_resultado` WHERE `ocrr_cod_familia` = ?
");


$limite_procesar = (count($lista_archivo) > 402) ? 402 : count($lista_archivo);

$consulta_string_analista = "
    SELECT `usu_id`, `usu_nombres_apellidos`
    FROM `administrador_usuario`
    WHERE `usu_estado`='Activo' AND `usu_cargo_rol`='AGENTE INSCRIPCIÓN FA'
    ORDER BY `usu_id`
";
$consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
$consulta_registros_analistas->execute();


$resultado_registros_analistas = [];
if (method_exists($consulta_registros_analistas, 'get_result')) {
    $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);
} else {
    $consulta_registros_analistas->bind_result($tmp_aid, $tmp_aname);
    while ($consulta_registros_analistas->fetch()) {
        $resultado_registros_analistas[] = [$tmp_aid, $tmp_aname];
    }
}

$array_analistas = [];
foreach ($resultado_registros_analistas as $r) {
    $array_analistas[] = $r[0];
}
if (!empty($array_analistas)) shuffle($array_analistas);

$control_errores_agente = 0;


for ($idx = 2; $idx < $limite_procesar; $idx++) {
    $archivo = $lista_archivo[$idx];
    if ($archivo === '.' || $archivo === '..') continue;

    $path_archivo = $ruta_pendientes . $archivo;
    if (!is_file($path_archivo)) continue;

    $json_parser = @file_get_contents($path_archivo);
    if ($json_parser === false) {
        @rename($path_archivo, $ruta_error . $archivo);
        safe_log("No se pudo leer JSON: {$path_archivo}");
        continue;
    }

    $array_json = json_decode($json_parser, true);
    if (!is_array($array_json)) {
        @rename($path_archivo, $ruta_error . $archivo);
        safe_log("JSON inválido: {$path_archivo}");
        continue;
    }

    $ocrc_cod_familia  = $array_json['familia']['codigo'] ?? '';
    $ocrc_registro_path = $archivo;

    if ($ocrc_cod_familia === '') {
        @rename($path_archivo, $ruta_error . $archivo);
        continue;
    }

    $control_error = 0;
    $control_registro = 0;
    $control_errores_familia = 0;

    if (!isset($array_json['familia']['miembros']) || !is_array($array_json['familia']['miembros'])) {
        @rename($path_archivo, $ruta_error . $archivo);
        safe_log("JSON sin miembros: familia={$ocrc_cod_familia}");
        continue;
    }

    foreach ($array_json['familia']['miembros'] as $miembro) {
        $ocrc_miembro_id         = $miembro['id'] ?? '';
        $ocrc_existe             = (string)($miembro['existe'] ?? '');
        $ocrc_doc_valida         = (string)($miembro['documento']['validacion'] ?? '');
        $ocrc_doc_valor          = (string)($miembro['documento']['valor'] ?? '');
        $ocrc_doc_tipo           = (string)($miembro['tipo'] ?? '');
        $ocrc_nombre_valida      = (string)($miembro['nombres']['validacion'] ?? '');
        $ocrc_nombre_valor       = (string)($miembro['nombres']['valor'] ?? '');
        $ocrc_apellido_valida    = (string)($miembro['apellidos']['validacion'] ?? '');
        $ocrc_apellido_valor     = (string)($miembro['apellidos']['valor'] ?? '');
        $ocrc_fnacimiento_valida = (string)($miembro['fecha_nacimiento']['validacion'] ?? '');
        $ocrc_fnacimiento_valor  = (string)($miembro['fecha_nacimiento']['valor'] ?? '');
        $ocrc_fexpedicion_valida = (string)($miembro['fecha_expedicion']['validacion'] ?? '');
        $ocrc_fexpedicion_valor  = (string)($miembro['fecha_expedicion']['valor'] ?? '');

        $consulta_registros->bind_param("ss", $ocrc_cod_familia, $ocrc_miembro_id);
        $consulta_registros->execute();

        $resultado_registros = [];
        if (method_exists($consulta_registros, 'get_result')) {
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
        } else {
            $consulta_registros->bind_result($tmp_id, $tmp_codbenef, $tmp_cabeza, $tmp_fnac);
            while ($consulta_registros->fetch()) {
                $resultado_registros[] = [$tmp_id, $tmp_codbenef, $tmp_cabeza, $tmp_fnac];
            }
        }

        if (count($resultado_registros) === 0) {
            $control_error++;
            continue;
        }

        $ocr_id               = $resultado_registros[0][0];
        $ocrc_codbeneficiario = $resultado_registros[0][1];
        $ocrc_cabezafamilia   = $resultado_registros[0][2];
        $ocrc_fechanacimiento = $resultado_registros[0][3];

        $fecha_mayor_edad = '';
        if (!empty($ocrc_fechanacimiento)) {
            $fecha_mayor_edad = date("Y-m", strtotime("+ 18 year", strtotime($ocrc_fechanacimiento)));
        }
        $fecha_actual        = date('Y-m');
        $ocr_consolida_fecha = date('Y-m-d H:i:s');

        if ($ocrc_cabezafamilia === "SI") {
            $ocrc_contrato_existe      = $array_json['familia']['contrato']['existe'] ?? '';
            $ocrc_contrato_numid       = $array_json['familia']['contrato']['numId'] ?? '';
            $ocrc_contrato_titular     = $array_json['familia']['contrato']['titular'] ?? '';
            $ocrc_contrato_municipio   = $array_json['familia']['contrato']['municipio'] ?? '';
            $ocrc_contrato_departamento= $array_json['familia']['contrato']['departamento'] ?? '';
            $ocrc_contrato_firmado     = $array_json['familia']['contrato']['firmado'] ?? '';
            $ocrc_contrato_huella      = $array_json['familia']['contrato']['huella'] ?? '';
        } else {
            $ocrc_contrato_existe      = 'NA';
            $ocrc_contrato_numid       = 'NA';
            $ocrc_contrato_titular     = 'NA';
            $ocrc_contrato_municipio   = 'NA';
            $ocrc_contrato_departamento= 'NA';
            $ocrc_contrato_firmado     = 'NA';
            $ocrc_contrato_huella      = 'NA';
        }

        $ocrc_resultado_novedad = '';
        $control_errores_beneficiario = 0;

        if ($ocrc_cabezafamilia === "SI") {
            if (!($ocrc_doc_valida && $ocrc_nombre_valida && $ocrc_apellido_valida && $ocrc_fnacimiento_valida && $ocrc_fexpedicion_valida)) {
                $control_errores_beneficiario++;
                $ocrc_resultado_novedad .= 'Documento de identidad NO validado, ';
            }
            if (!($ocrc_contrato_existe && $ocrc_contrato_numid && $ocrc_contrato_firmado && $ocrc_contrato_huella)) {
                $control_errores_beneficiario++;
                $ocrc_resultado_novedad .= 'Contrato NO validado';
            }
        } else {
            if ($fecha_mayor_edad !== '' && $fecha_mayor_edad <= $fecha_actual) {
                $ocrc_resultado_novedad .= 'Beneficiario Mayor de Edad';
            }
            if (!($ocrc_doc_valida && $ocrc_nombre_valida && $ocrc_apellido_valida && $ocrc_fnacimiento_valida && $ocrc_fexpedicion_valida)) {
                $control_errores_beneficiario++;
                $ocrc_resultado_novedad .= 'Documento de identidad NO validado';
            }
        }

        if ($fecha_mayor_edad !== '' && $fecha_mayor_edad <= $fecha_actual && $ocrc_cabezafamilia === "NO") {
            $ocrc_resultado_estado = 'Validado-Edad';
        } elseif ($control_errores_beneficiario > 0) {
            $ocrc_resultado_estado = 'No validado-OCR';
            $control_errores_familia++;
        } else {
            $ocrc_resultado_estado = 'Validado-OCR';
        }


        $sentencia_insert->bind_param(
            'ssssssssssssssssssssssssss',
            $ocrc_cod_familia, $ocrc_codbeneficiario, $ocrc_cabezafamilia, $ocrc_miembro_id,
            $ocrc_existe, $ocrc_doc_valida, $ocrc_doc_valor, $ocrc_doc_tipo,
            $ocrc_nombre_valida, $ocrc_nombre_valor, $ocrc_apellido_valida, $ocrc_apellido_valor,
            $ocrc_fnacimiento_valida, $ocrc_fnacimiento_valor, $ocrc_fexpedicion_valida, $ocrc_fexpedicion_valor,
            $ocrc_contrato_existe, $ocrc_contrato_numid, $ocrc_contrato_titular, $ocrc_contrato_municipio,
            $ocrc_contrato_departamento, $ocrc_contrato_firmado, $ocrc_contrato_huella,
            $ocrc_registro_path, $ocrc_resultado_estado, $ocrc_resultado_novedad
        );

        if ($sentencia_insert->execute()) {
            $control_registro++;
            $ocr_consolida_estado = 'Procesado';
            $consulta_actualizar->bind_param('sss', $ocr_consolida_estado, $ocr_consolida_fecha, $ocr_id);
            $consulta_actualizar->execute();
            if (function_exists('comprobarSentencia')) {
                if (!comprobarSentencia($enlace_db->info)) {
                    $control_error++;
                }
            }
        } else {
            $control_error++;
            $ocr_consolida_estado = 'Error';
            $consulta_actualizar->bind_param('sss', $ocr_consolida_estado, $ocr_consolida_fecha, $ocr_id);
            $consulta_actualizar->execute();
            if (function_exists('comprobarSentencia')) {
                if (!comprobarSentencia($enlace_db->info)) {
                    $control_error++;
                }
            }
        }
    } 
    if ($control_error === 0) {
        if ($control_errores_familia > 0) {
            $ocrr_resultado_familia_estado = 'No validado-OCR';
            $ocrr_gestion_estado = 'Aplazado';
            if (!empty($array_analistas)) {
                $ocrr_gestion_agente = $array_analistas[$control_errores_agente] ?? '';
                $control_errores_agente++;
                $total_analistas = count($array_analistas);
                if ($control_errores_agente >= $total_analistas) $control_errores_agente = 0;
            } else {
                $ocrr_gestion_agente = '';
            }
        } else {
            $ocrr_resultado_familia_estado = 'Validado-OCR';
            $ocrr_gestion_estado = 'Validado-OCR';
            $ocrr_gestion_agente = '';
        }

        $ocrr_cod_familia = $ocrc_cod_familia;
        $ocrr_codbeneficiario = $ocrc_codbeneficiario ?? '';
        $ocrr_cabezafamilia = $ocrc_cabezafamilia ?? '';
        $ocrr_gestion_intentos = '0';
        $ocrr_gestion_correo = '';
        $ocrr_gestion_observaciones = '';
        $ocrr_gestion_fecha = date('Y-m-d H:i:s');
        $ocrr_gestion_notificacion = '';
        $ocrr_gestion_notificacion_estado = '';
        $ocrr_gestion_notificacion_fecha_registro = '';
        $ocrr_gestion_notificacion_fecha_envio = '';
        $ocrr_gestion_llamada_tipificacion = '';
        $ocrr_gestion_llamada_id = '';
        $ocrr_sr_fecha = '';
        $ocrr_sr_observaciones = '';

        $sentencia_insert_resultado->bind_param(
            'ssssssssssssssssss',
            $ocrr_cod_familia, $ocrr_codbeneficiario, $ocrr_cabezafamilia,
            $ocrr_resultado_familia_estado, $ocrr_gestion_agente, $ocrr_gestion_estado,
            $ocrr_gestion_intentos, $ocrr_gestion_correo, $ocrr_gestion_observaciones, $ocrr_gestion_fecha,
            $ocrr_gestion_notificacion, $ocrr_gestion_notificacion_estado,
            $ocrr_gestion_notificacion_fecha_registro, $ocrr_gestion_notificacion_fecha_envio,
            $ocrr_gestion_llamada_tipificacion, $ocrr_gestion_llamada_id, $ocrr_sr_fecha, $ocrr_sr_observaciones
        );

        if ($sentencia_insert_resultado->execute()) {
            $observaciones_log = 'Procesado OCR primera revisión';
            $sentencia_insert_log = $enlace_db->prepare("
                INSERT INTO `gestion_ocr_resultado_avances`
                (`gora_codfamilia`, `gora_estado`, `gora_correo`, `gora_observaciones`, `gora_llamada_tipificacion`, `gora_llamada_id`, `gora_registro_usuario`)
                VALUES (?,'Procesado OCR','',?,'','','1')
            ");
            $sentencia_insert_log->bind_param('ss', $ocrr_cod_familia, $observaciones_log);
            $sentencia_insert_log->execute();
            $sentencia_insert_log->close();

            if (!empty($ocrr_gestion_agente)) {
                $observaciones_log = 'Reasignar caso a usuario: ' . $ocrr_gestion_agente;
                $sentencia_insert_log = $enlace_db->prepare("
                    INSERT INTO `gestion_ocr_resultado_avances`
                    (`gora_codfamilia`, `gora_estado`, `gora_correo`, `gora_observaciones`, `gora_llamada_tipificacion`, `gora_llamada_id`, `gora_registro_usuario`)
                    VALUES (?,'Asignado','',?,'','','1')
                ");
                $sentencia_insert_log->bind_param('ss', $ocrr_cod_familia, $observaciones_log);
                $sentencia_insert_log->execute();
                $sentencia_insert_log->close();
            }

            @rename($path_archivo, $ruta_procesados . $ocrc_registro_path);

        } else {
            @rename($path_archivo, $ruta_error . $ocrc_registro_path);
            safe_log("Fallo inserción resultado familia: familia={$ocrr_cod_familia}");
        }

    } else {
        if ($sentencia_delete_consolidado) {
            $sentencia_delete_consolidado->bind_param('s', $ocrc_cod_familia);
            $sentencia_delete_consolidado->execute();
        }
        if ($sentencia_delete_resultado) {
            $sentencia_delete_resultado->bind_param('s', $ocrc_cod_familia);
            $sentencia_delete_resultado->execute();
        }
        @rename($path_archivo, $ruta_error . $ocrc_registro_path);
    }
} 
?>
