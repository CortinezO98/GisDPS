<?php
// notificaciones_central_ocr.php (versión remediada 2nd-order SQLi, sin cambiar la lógica de negocio)

$modulo_plataforma = "Administrador";
require_once("/var/www/html/iniciador.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '/var/www/html/templates/assets/plugins/PHPMailer-master/src/Exception.php';
require '/var/www/html/templates/assets/plugins/PHPMailer-master/src/PHPMailer.php';
require '/var/www/html/templates/assets/plugins/PHPMailer-master/src/SMTP.php';

// Hardening: registrar errores pero no mostrarlos en el navegador
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
date_default_timezone_set('America/Bogota');

/**
 * UPDATE seguro en administrador_notificaciones (mitiga 2nd-order SQLi).
 */
function actualizar_estado_envio(mysqli $db, string $estado, string $fecha, int $intentos, int $idCorreo): void {
    $stmt = $db->prepare("
        UPDATE `administrador_notificaciones`
           SET `nc_estado_envio` = ?, `nc_fecha_envio` = ?, `nc_intentos` = ?
         WHERE `nc_id` = ?
    ");
    if ($stmt) {
        $stmt->bind_param('ssii', $estado, $fecha, $intentos, $idCorreo);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log('[noti_ocr] Error preparando UPDATE: ' . $db->error);
    }
}


function path_dentro_de(string $basePath, string $rutaRelativa): ?string {
    $baseReal = rtrim(realpath($basePath), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $destReal = realpath($basePath . DIRECTORY_SEPARATOR . ltrim($rutaRelativa, DIRECTORY_SEPARATOR));
    if ($destReal !== false && strncmp($destReal, $baseReal, strlen($baseReal)) === 0) {
        return $destReal;
    }
    return null;
}


//Consultar notificaciones pendientes
$consulta_notificaciones = mysqli_query(
    $enlace_db,
    "SELECT `nc_id`, `nc_id_modulo`, `nc_prioridad`, `nc_id_set_from`,
            `nc_address`, `nc_cc`, `nc_bcc`, `nc_reply_to`,
            `nc_subject`, `nc_body`,
            `nc_embeddedimage_ruta`, `nc_intentos`, `nc_eliminar`,
            `nc_estado_envio`,
            RT.`ncr_host`, RT.`ncr_port`, RT.`ncr_smtpsecure`, RT.`ncr_smtpauth`,
            RT.`ncr_username`, RT.`ncr_password`, RT.`ncr_setfrom`, RT.`ncr_setfrom_name`,
            `nc_embeddedimage_ruta`, `nc_embeddedimage_nombre`, `nc_embeddedimage_tipo`
       FROM `administrador_notificaciones`
  LEFT JOIN `administrador_buzones` AS RT
         ON `administrador_notificaciones`.`nc_id_set_from` = RT.`ncr_id`
      WHERE `nc_estado_envio`='Pendiente'
        AND `nc_id_set_from`='2'
   ORDER BY `nc_prioridad`
      LIMIT 8 OFFSET 0"
);

$resultado_notificaciones = mysqli_fetch_all($consulta_notificaciones);

if (count($resultado_notificaciones) > 0) {
    foreach ($resultado_notificaciones as $row) {
        $marca_temporal = date("Y-m-d H:i:s");

        // Claves/ítems base
        $id_correo     = (int)$row[0];
        $host          = (string)$row[14];
        $port          = (string)$row[15];
        $secure        = (string)$row[16];
        $smtpAuth      = (string)$row[17];
        $username      = (string)$row[18];
        $password      = (string)$row[19];
        $setFrom       = (string)$row[20];
        $setFromName   = (string)$row[21];

        $to_raw        = (string)$row[4];
        $subject       = (string)$row[8];
        $bodyHtml      = (string)$row[9];

        // Mínimos requeridos 
        if ($host !== "" && $port !== "" && $secure !== "" && $smtpAuth !== "" &&
            $username !== "" && $password !== "" && $setFrom !== "" && $setFromName !== "" &&
            $to_raw !== "" && $subject !== "" && $bodyHtml !== "") {

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->SMTPDebug  = SMTP::DEBUG_OFF; 
                $mail->Host       = $host;
                $mail->Port       = (int)$port;
                $mail->SMTPSecure = $secure; 
                $mail->SMTPAuth   = strtolower($smtpAuth) === '1' || strtolower($smtpAuth) === 'true';
                $mail->Username   = $username;
                $mail->Password   = $password;
                $mail->setFrom($setFrom, $setFromName);

                // Intentos 
                $num_intentos = (int)$row[11] + 1;
                $estado_error = ($num_intentos >= 2) ? "Error" : "Pendiente";

                // TO
                $destino_to = array_filter(explode(";", $to_raw));
                foreach ($destino_to as $addr) {
                    $addr = trim($addr);
                    if ($addr !== "") {
                        $mail->addAddress($addr, $addr);
                    }
                }

                // CC
                $cc_raw = (string)$row[5];
                if ($cc_raw !== "") {
                    foreach (array_filter(explode(";", $cc_raw)) as $addr) {
                        $addr = trim($addr);
                        if ($addr !== "") {
                            $mail->addCC($addr, $addr);
                        }
                    }
                }

                // BCC
                $bcc_raw = (string)$row[6];
                if ($bcc_raw !== "") {
                    foreach (array_filter(explode(";", $bcc_raw)) as $addr) {
                        $addr = trim($addr);
                        if ($addr !== "") {
                            $mail->addBCC($addr, $addr);
                        }
                    }
                }

                // Reply-To
                $reply_to = (string)$row[7];
                if ($reply_to !== "") {
                    foreach (array_filter(explode(";", $reply_to)) as $addr) {
                        $addr = trim($addr);
                        if ($addr !== "") {
                            $mail->addReplyTo($addr, $addr);
                        }
                    }
                }

                // Imágenes embebidas
                $img_rutas   = explode(";", (string)$row[22]);
                $img_nombres = explode(";", (string)$row[23]);
                $img_tipos   = explode(";", (string)$row[24]);

                $base_imgs = '/var/www/html/';

                $count_imgs = max(count($img_rutas), count($img_nombres), count($img_tipos));
                for ($j = 0; $j < $count_imgs; $j++) {
                    $rutaRel = $img_rutas[$j]   ?? '';
                    $nombre  = $img_nombres[$j] ?? '';
                    $tipo    = $img_tipos[$j]   ?? '';

                    if ($rutaRel !== "" && $nombre !== "" && $tipo !== "") {
                        $rutaAbs = path_dentro_de($base_imgs, $rutaRel) ?? $rutaRel; 
                        if (is_file($rutaAbs)) {
                            $mail->AddEmbeddedImage($rutaAbs, $nombre, basename($rutaAbs), 'base64', $tipo);
                        }
                    }
                }

                if (defined('UPLOADS_ROOT')) {
                    $adj1 = rtrim(UPLOADS_ROOT, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'Manual_de_ciudadano_para_creacion_y_envio_de_documentos_solicitados_en_archivo_PDF.pdf';
                    $adj2 = rtrim(UPLOADS_ROOT, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'Contrato_Social.pdf';

                    if (is_file($adj1)) {
                        $mail->AddAttachment($adj1, 'Manual_de_ciudadano_para_creacion_y_envio_de_documentos_solicitados_en_archivo_PDF.pdf');
                    }
                    if (is_file($adj2)) {
                        $mail->AddAttachment($adj2, 'Contrato_Social.pdf');
                    }
                }

                // Contenido
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = $subject;
                $mail->Body    = $bodyHtml;

                if ($mail->send()) {
                    actualizar_estado_envio($enlace_db, 'Enviado', $marca_temporal, $num_intentos, $id_correo);
                } else {
                    actualizar_estado_envio($enlace_db, $estado_error, $marca_temporal, $num_intentos, $id_correo);
                }

            } catch (Exception $e) {
                $num_intentos = isset($num_intentos) ? $num_intentos : ((int)$row[11] + 1);
                $reporte_error = (string)$e->getMessage();
                $estado_error_final = '';

                if (stripos($reporte_error, 'Invalid address:') !== false) {
                    $estado_error_final = 'Destinatario inválido';
                } elseif ($reporte_error === 'You must provide at least one recipient email address.') {
                    $estado_error_final = 'Sin destinatario';
                } elseif ($reporte_error === 'SMTP Error: Could not authenticate.') {
                    $estado_error_final = 'Error de autenticación';
                } else {
                    $estado_error_final = 'Error';
                }

                actualizar_estado_envio($enlace_db, $estado_error_final, $marca_temporal, $num_intentos, $id_correo);
                error_log('[noti_ocr] Excepción id='.$id_correo.' -> '.$reporte_error);
            }

        } else {
            actualizar_estado_envio($enlace_db, 'Error-estructura', $marca_temporal, 1, $id_correo);
        }
    }
}
?>
