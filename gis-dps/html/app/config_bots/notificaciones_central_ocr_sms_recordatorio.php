<?php

$modulo_plataforma = "Administrador";
require_once("/var/www/html/iniciador.php");

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
date_default_timezone_set('America/Bogota');
if (function_exists('mysqli_set_charset')) {
    @mysqli_set_charset($enlace_db, 'utf8mb4');
}

$fecha_hoy = date("Y-m-d");
$fecha_recordatorio = date("Y-m-d", strtotime("- 5 day", strtotime($fecha_hoy)));
$like_recordatorio = '%' . $fecha_recordatorio . '%';
$like_hoy = '%' . $fecha_hoy . '%';

$sql_notif = "
    SELECT 
        `nsms_id`, `nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`,
        `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`,
        `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`,
        `nsms_fecha_envio`, `nsms_usuario_registro`, `nsms_fecha_registro`,
        TR.`ocrr_gestion_estado`,
        TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`,
        TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`
    FROM `administrador_notificaciones_sms`
    LEFT JOIN `gestion_ocr_consolidado` AS TCON
        ON `administrador_notificaciones_sms`.`nsms_identificador` = TCON.`ocrc_id`
    LEFT JOIN `gestion_ocr_resultado` AS TR
        ON TCON.`ocrc_cod_familia` = TR.`ocrr_cod_familia`
    LEFT JOIN `gestion_ocr` AS TOCR
        ON TCON.`ocrc_codbeneficiario` = TOCR.`ocr_codbeneficiario`
    WHERE `nsms_id_modulo` = '11'
      AND TR.`ocrr_gestion_estado` = 'Contactado-Pendiente Documentos'
      AND `nsms_fecha_registro` LIKE ?
    ORDER BY `nsms_fecha_registro` ASC
";

$stmt_notif = $enlace_db->prepare($sql_notif);
if ($stmt_notif === false) {
    error_log('[ocr_sms_recordatorio] Error preparando SELECT principal: ' . $enlace_db->error);
    exit;
}
$stmt_notif->bind_param('s', $like_recordatorio);
$stmt_notif->execute();
$res_notif = $stmt_notif->get_result();
$resultado_notificaciones = $res_notif ? $res_notif->fetch_all(MYSQLI_NUM) : [];
$stmt_notif->close();

if (count($resultado_notificaciones) > 0) {

    $sql_insert = "
        INSERT INTO `administrador_notificaciones_sms`
            (`nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`,
             `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`,
             `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`,
             `nsms_fecha_envio`, `nsms_usuario_registro`)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    ";
    $sentencia_insert = $enlace_db->prepare($sql_insert);
    if ($sentencia_insert === false) {
        error_log('[ocr_sms_recordatorio] Error preparando INSERT: ' . $enlace_db->error);
        exit;
    }
    $sentencia_insert->bind_param(
        'ssssssssssss',
        $nsms_identificador,
        $nsms_id_modulo,
        $nsms_prioridad,
        $nsms_id_set_from,
        $nsms_destino,
        $nsms_body,
        $nsms_url,
        $nsms_intentos,
        $nsms_observaciones,
        $nsms_estado_envio,
        $nsms_fecha_envio,
        $nsms_usuario_registro
    );

    $sql_duplicado = "
        SELECT `nsms_id`
        FROM `administrador_notificaciones_sms`
        WHERE `nsms_identificador` = ?
          AND `nsms_fecha_registro` LIKE ?
        LIMIT 1
    ";
    $stmt_dup = $enlace_db->prepare($sql_duplicado);
    if ($stmt_dup === false) {
        error_log('[ocr_sms_recordatorio] Error preparando SELECT duplicado: ' . $enlace_db->error);
        $sentencia_insert->close();
        exit;
    }

    for ($i = 0; $i < count($resultado_notificaciones); $i++) {
        $nsms_identificador = 'R' . (string)$resultado_notificaciones[$i][1];
        $stmt_dup->bind_param('ss', $nsms_identificador, $like_hoy);
        $stmt_dup->execute();
        $res_dup = $stmt_dup->get_result();
        $ya_existe_hoy = ($res_dup && $res_dup->num_rows > 0);
        if ($res_dup) { $res_dup->free(); }

        if (!$ya_existe_hoy) {
            $nsms_id_modulo   = (string)$resultado_notificaciones[$i][2];
            $nsms_prioridad   = '2';
            $nsms_id_set_from = (string)$resultado_notificaciones[$i][4];
            $nsms_destino     = (string)$resultado_notificaciones[$i][5];

            // Construcción de nombre 
            $nombre_cabeza_familia = (string)$resultado_notificaciones[$i][15];
            if (!empty($resultado_notificaciones[$i][16])) {
                $nombre_cabeza_familia .= ' ' . $resultado_notificaciones[$i][16];
            }
            if (!empty($resultado_notificaciones[$i][17])) {
                $nombre_cabeza_familia .= ' ' . $resultado_notificaciones[$i][17];
            }
            if (!empty($resultado_notificaciones[$i][18])) {
                $nombre_cabeza_familia .= ' ' . $resultado_notificaciones[$i][18];
            }

            $nsms_body = $nombre_cabeza_familia . ", recuerde cargar los documentos corregidos de su inscripción de Familias en Acción en el link: SHORTURL";

            $nsms_url            = (string)$resultado_notificaciones[$i][7];
            $nsms_intentos       = '';  
            $nsms_observaciones  = '';
            $nsms_estado_envio   = 'Pendiente';
            $nsms_fecha_envio    = '';
            $nsms_usuario_registro = '1111111111';

            if (!$sentencia_insert->execute()) {
                error_log('[ocr_sms_recordatorio] Error INSERT id=' . $nsms_identificador . ' -> ' . $enlace_db->error);
            }
        }
    }

    $stmt_dup->close();
    $sentencia_insert->close();
}
?>
