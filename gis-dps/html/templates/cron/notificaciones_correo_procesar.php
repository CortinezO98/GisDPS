<?php
// ===================================================================
// notificaciones_correo_procesar.php
//
// Este script recorre todas las filas de `administrador_notificaciones`
// con nc_estado_envio = 'Pendiente' y las envía por correo.
// Configura PHPMailer en modo SMTP clásico usando App Password.
// ===================================================================

// 1) Mostrar errores (solo para depuración)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Eco de inicio de cron
echo "== [DEBUG] Inicio cron: " . date('Y-m-d H:i:s') . "\n";

// 3) Incluir iniciador.php (configuración y conexión $enlace_db)
$iniciadorPath = __DIR__ . '/../../iniciador.php';
if (!file_exists($iniciadorPath)) {
    echo "== [ERROR] No existe el iniciador.php en: {$iniciadorPath}\n";
    exit(1);
}
require_once $iniciadorPath;
echo "== [DEBUG] Incluido iniciador.php desde: {$iniciadorPath}\n";

// 4) Verificar que $enlace_db (mysqli) exista
if (!isset($enlace_db) || !($enlace_db instanceof mysqli)) {
    echo "== [ERROR] \$enlace_db NO conectado (instancia inválida)\n";
    exit(1);
}
echo "== [DEBUG] \$enlace_db conectado correctamente\n";

// 5) Incluir Composer autoload (PHPMailer)
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    echo "== [ERROR] No existe vendor/autoload.php en: {$autoloadPath}\n";
    exit(1);
}
require_once $autoloadPath;
echo "== [DEBUG] Incluido vendor/autoload.php\n";

// 6) Importar clases necesarias
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
echo "== [DEBUG] Espacio de nombres PHPMailer cargado\n";

// 7) Leer notificaciones pendientes
echo "== [DEBUG] Iniciando SELECT en administrador_notificaciones\n";
$sqlPendientes = "
  SELECT
    nc_id,
    nc_id_set_from,
    nc_address,
    nc_cc,
    nc_bcc,
    nc_reply_to,
    nc_subject,
    nc_body,
    nc_embeddedimage_ruta,
    nc_embeddedimage_nombre,
    nc_embeddedimage_tipo
  FROM administrador_notificaciones
  WHERE nc_estado_envio = 'Pendiente'
";
$resPendientes = $enlace_db->query($sqlPendientes);
if (!$resPendientes) {
    echo "== [ERROR] Falló SELECT: " . $enlace_db->error . "\n";
    exit(1);
}
echo "== [DEBUG] Filas pendientes encontradas: " . $resPendientes->num_rows . "\n";

// 8) Recorrer cada notificación pendiente
while ($fila = $resPendientes->fetch_assoc()) {
    $nc_id        = (int)$fila['nc_id'];
    $idSetFrom    = (int)$fila['nc_id_set_from'];
    $toRaw        = rtrim($fila['nc_address'], ';');
    $ccRaw        = rtrim($fila['nc_cc'], ';');
    $bccRaw       = rtrim($fila['nc_bcc'], ';');
    $replyToRaw   = rtrim($fila['nc_reply_to'], ';');
    $subject      = $fila['nc_subject'];
    $bodyHTML     = $fila['nc_body'];
    $rutaImgsRaw  = rtrim($fila['nc_embeddedimage_ruta'], ';');
    $nombresImgs  = rtrim($fila['nc_embeddedimage_nombre'], ';');
    $tiposImgs    = rtrim($fila['nc_embeddedimage_tipo'], ';');

    echo "== [DEBUG] Procesando nc_id={$nc_id}, idSetFrom={$idSetFrom}\n";

    // 9) Obtener configuración del buzón en administrador_buzones
    $stmtBuzon = $enlace_db->prepare("
      SELECT 
        ncr_host,
        ncr_port,
        ncr_smtpsecure,
        ncr_smtpauth,
        ncr_username,
        ncr_password,
        ncr_setfrom,
        ncr_setfrom_name,
        ncr_tipo
      FROM administrador_buzones
      WHERE ncr_id = ?
    ");
    if (!$stmtBuzon) {
        echo "== [ERROR] Falló prepare() admin_buzones: " . $enlace_db->error . "\n";
        // Marcar error y continuar
        $updErr = $enlace_db->prepare("
          UPDATE administrador_notificaciones
             SET nc_estado_envio = 'Error: buzón no configurado'
           WHERE nc_id = ?
        ");
        $updErr->bind_param('i', $nc_id);
        $updErr->execute();
        $updErr->close();
        continue;
    }
    $stmtBuzon->bind_param('i', $idSetFrom);
    $stmtBuzon->execute();
    $datosBuzon = $stmtBuzon->get_result()->fetch_assoc();
    $stmtBuzon->close();

    if (!$datosBuzon) {
        echo "== [ERROR] buzón (ncr_id={$idSetFrom}) no encontrado\n";
        $updErr = $enlace_db->prepare("
          UPDATE administrador_notificaciones
             SET nc_estado_envio = 'Error: buzón no encontrado'
           WHERE nc_id = ?
        ");
        $updErr->bind_param('i', $nc_id);
        $updErr->execute();
        $updErr->close();
        continue;
    }
    echo "== [DEBUG] Datos de buzón cargados: host={$datosBuzon['ncr_host']}, port={$datosBuzon['ncr_port']}, tipo={$datosBuzon['ncr_tipo']}\n";

    // 10) Crear PHPMailer en modo SMTP clásico
    $mail = new PHPMailer(true);
    try {
        echo "== [DEBUG] Iniciando envío SMTP clásico para nc_id={$nc_id}\n";

        // ––– Configuración SMTP clásico –––
        $mail->isSMTP();
        $mail->SMTPDebug  = 0;
        $mail->Host       = $datosBuzon['ncr_host'];         // p.ej. smtp.gmail.com
        $mail->Port       = (int)$datosBuzon['ncr_port'];    // p.ej. 587
        $mail->SMTPSecure = $datosBuzon['ncr_smtpsecure'];   // p.ej. tls
        $mail->SMTPAuth   = ($datosBuzon['ncr_smtpauth'] === 'true');
        $mail->Username   = $datosBuzon['ncr_username'];     // p.ej. iqgisdps@gmail.com
        $mail->Password   = $datosBuzon['ncr_password'];     // App Password de 16 caracteres

        // From
        $mail->setFrom(
            $datosBuzon['ncr_setfrom'], 
            $datosBuzon['ncr_setfrom_name']
        );

        // 11) Destinatarios TO
        $toList = explode(';', $toRaw);
        $addedTo = 0;
        foreach ($toList as $addr) {
            $addr = trim($addr);
            if (filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($addr);
                $addedTo++;
            }
        }
        echo "== [DEBUG] Añadidos {$addedTo} destinatarios To\n";

        // 12) CC
        if (!empty($ccRaw)) {
            $ccList = explode(';', $ccRaw);
            $addedCC = 0;
            foreach ($ccList as $addr) {
                $addr = trim($addr);
                if (filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                    $mail->addCC($addr);
                    $addedCC++;
                }
            }
            echo "== [DEBUG] Añadidos {$addedCC} destinatarios CC\n";
        }

        // 13) BCC
        if (!empty($bccRaw)) {
            $bccList = explode(';', $bccRaw);
            $addedBCC = 0;
            foreach ($bccList as $addr) {
                $addr = trim($addr);
                if (filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                    $mail->addBCC($addr);
                    $addedBCC++;
                }
            }
            echo "== [DEBUG] Añadidos {$addedBCC} destinatarios BCC\n";
        }

        // 14) Reply-To
        if (!empty($replyToRaw) && filter_var($replyToRaw, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($replyToRaw);
            echo "== [DEBUG] Añadido Reply-To: {$replyToRaw}\n";
        }

        // 15) Asunto y cuerpo en HTML
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHTML;
        echo "== [DEBUG] Asunto y body preparados\n";

        // 16) Imágenes embebidas (opcional)
        if (!empty($rutaImgsRaw)) {
            $rutasArr   = explode(';', $rutaImgsRaw);
            $nombresArr = explode(';', $nombresImgs);
            $tiposArr   = explode(';', $tiposImgs);
            foreach ($rutasArr as $i => $rutaImg) {
                $rutaImg = trim($rutaImg);
                if (is_file($rutaImg)) {
                    $cid  = $nombresArr[$i] ?? "img{$i}";
                    $tipo = $tiposArr[$i] ?? '';
                    $mail->addEmbeddedImage($rutaImg, $cid, $nombresArr[$i], 'base64', $tipo);
                    echo "== [DEBUG] Imagen embebida: {$rutaImg} con CID={$cid}\n";
                } else {
                    echo "== [WARNING] Imagen no encontrada: {$rutaImg}\n";
                }
            }
        }

        // 17) Envío final
        $mail->send();
        echo "== [DEBUG] PHPMailer::send() exitoso para nc_id={$nc_id}\n";

        // 18) Si todo OK, marcaremos como “Enviado”
        $stmtUpd = $enlace_db->prepare("
          UPDATE administrador_notificaciones
             SET 
               nc_estado_envio = 'Enviado',
               nc_fecha_envio  = ?
           WHERE nc_id = ?
        ");
        $ahora = date('Y-m-d H:i:s');
        $stmtUpd->bind_param('si', $ahora, $nc_id);
        $stmtUpd->execute();
        $stmtUpd->close();
        echo "== [DEBUG] nc_id={$nc_id} actualizado a ‘Enviado’\n";
    }
    catch (Exception $e) {
        // 19) Si falla PHPMailer, actualizamos con el error
        $errorInfo = $mail->ErrorInfo;
        echo "== [ERROR] PHPMailer Falló (nc_id={$nc_id}): {$errorInfo}\n";

        $stmtUpd = $enlace_db->prepare("
          UPDATE administrador_notificaciones
             SET 
               nc_estado_envio = CONCAT('Error: ', ?),
               nc_intentos     = nc_intentos + 1
           WHERE nc_id = ?
        ");
        $stmtUpd->bind_param('si', $errorInfo, $nc_id);
        $stmtUpd->execute();
        $stmtUpd->close();

        error_log("PHPMailer error (nc_id={$nc_id}): " . $errorInfo);
    }

    // 20) Limpiar PHPMailer para la próxima iteración
    $mail->clearAddresses();
    $mail->clearCCs();
    $mail->clearBCCs();
    $mail->clearReplyTos();
    $mail->clearAttachments();
    $mail->clearEmbeddedImages();
}

// 21) Liberar recursos y cerrar conexión
$resPendientes->free();
$enlace_db->close();

// 22) Eco de fin
echo "== [DEBUG] Fin cron: " . date('Y-m-d H:i:s') . "\n";
exit(0);
