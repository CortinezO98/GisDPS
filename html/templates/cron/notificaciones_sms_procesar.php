<?php
// ===================================================================
// notificaciones_sms_procesar.php
//
// Este script recorre todas las filas de `administrador_notificaciones_sms`
// con nsms_estado_envio = 'Pendiente' y las envía vía HTTP (cURL) al
// SMS gateway configurado en administrador_buzones_sms.
// ===================================================================

// 1) Mostrar errores (solo para depuración)
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Eco de inicio de proceso
echo "== [DEBUG] Inicio SMS cron: " . date('Y-m-d H:i:s') . "\n";

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

// 5) Leer SMS pendientes
echo "== [DEBUG] Iniciando SELECT en administrador_notificaciones_sms\n";
$sqlPendientesSMS = "
  SELECT
    nsms_id,
    nsms_id_set_from,
    nsms_destino,
    nsms_body,
    nsms_intentos
  FROM administrador_notificaciones_sms
  WHERE nsms_estado_envio = 'Pendiente'
";
$resPendientesSMS = $enlace_db->query($sqlPendientesSMS);
if (!$resPendientesSMS) {
    echo "== [ERROR] Falló SELECT en administrador_notificaciones_sms: " . $enlace_db->error . "\n";
    exit(1);
}
echo "== [DEBUG] Filas SMS pendientes encontradas: " . $resPendientesSMS->num_rows . "\n";

// 6) Recorrer cada SMS pendiente
while ($filaSMS = $resPendientesSMS->fetch_assoc()) {
    $nsms_id         = (int)$filaSMS['nsms_id'];
    $idSetFromSMS    = (int)$filaSMS['nsms_id_set_from'];
    $destino         = trim($filaSMS['nsms_destino']);   // Número destino (p.ej. '573001234567')
    $mensaje         = trim($filaSMS['nsms_body']);      // Texto SMS
    $intentosActual  = (int)$filaSMS['nsms_intentos'];

    echo "== [DEBUG] Procesando SMS nsms_id={$nsms_id}, idSetFrom={$idSetFromSMS}\n";

    // 7) Obtener credenciales del buzón SMS en administrador_buzones_sms
    $stmtBuzonSMS = $enlace_db->prepare("
      SELECT 
        nsmsr_api,
        nsmsr_username,
        nsmsr_password
      FROM administrador_buzones_sms
      WHERE nsmsr_id = ?
    ");
    if (!$stmtBuzonSMS) {
        echo "== [ERROR] Falló prepare() admin_buzones_sms: " . $enlace_db->error . "\n";
        // Marcar error y continuar
        $updErrSMS = $enlace_db->prepare("
          UPDATE administrador_notificaciones_sms
             SET nsms_estado_envio = 'Error: buzón SMS no configurado'
           WHERE nsms_id = ?
        ");
        $updErrSMS->bind_param('i', $nsms_id);
        $updErrSMS->execute();
        $updErrSMS->close();
        continue;
    }
    $stmtBuzonSMS->bind_param('i', $idSetFromSMS);
    $stmtBuzonSMS->execute();
    $datosBuzonSMS = $stmtBuzonSMS->get_result()->fetch_assoc();
    $stmtBuzonSMS->close();

    if (!$datosBuzonSMS) {
        echo "== [ERROR] buzón SMS (nsmsr_id={$idSetFromSMS}) no encontrado\n";
        $updErrSMS = $enlace_db->prepare("
          UPDATE administrador_notificaciones_sms
             SET nsms_estado_envio = 'Error: buzón SMS no encontrado'
           WHERE nsms_id = ?
        ");
        $updErrSMS->bind_param('i', $nsms_id);
        $updErrSMS->execute();
        $updErrSMS->close();
        continue;
    }
    $apiURL   = trim($datosBuzonSMS['nsmsr_api']);       // URL para el SMS gateway
    $apiUser  = trim($datosBuzonSMS['nsmsr_username']);  // Usuario API
    $apiPass  = trim($datosBuzonSMS['nsmsr_password']);  // Contraseña/API Key
    echo "== [DEBUG] Datos buzón SMS: apiURL={$apiURL}, user={$apiUser}\n";

    // 8) Preparar datos para cURL (Ejemplo genérico; adapta según tu proveedor)
    //    Supongamos que tu SMS gateway acepta POST con campos:
    //      - user    = nsmsr_username
    //      - pass    = nsmsr_password
    //      - to      = número destino (incluyendo código país, p.ej. 573001234567)
    //      - message = cuerpo del SMS
    //    Ajusta esta parte a lo que tu proveedor requiera (pueden ser GET, JSON, etc.)

    $postData = [
        'user'    => $apiUser,
        'pass'    => $apiPass,
        'to'      => $destino,
        'message' => $mensaje,
    ];

    // 9) Ejecutar cURL para enviar el SMS
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData)); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    // Opcional: si tu API usa SSL y tienes que omitir verificación (no recomendado en producción):
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    echo "== [DEBUG] cURL HTTP_CODE={$httpCode}, response={$response}\n";

    // 10) Analizar respuesta del SMS gateway (ejemplo genérico)
    //     Cada proveedor responde distinto. Supón que:
    //       - Si HTTP code = 200 y en $response contiene "OK", se considera exitoso. 
    //       - De lo contrario, se marca error.
    $exitoEnvio = false;
    if ($httpCode === 200) {
        // Supongamos que tu gateway devuelve algo como "OK" o JSON con {"status":"sent"}.
        if (stripos($response, 'OK') !== false || stripos($response, 'sent') !== false) {
            $exitoEnvio = true;
        }
    }

    // 11) Actualizar estado en la base de datos
    if ($exitoEnvio) {
        // Si salió OK, marcar como Enviado y fecha
        $stmtUpdOK = $enlace_db->prepare("
          UPDATE administrador_notificaciones_sms
             SET nsms_estado_envio = 'Enviado',
                 nsms_fecha_envio  = ?
           WHERE nsms_id = ?
        ");
        $fechaAhora = date('Y-m-d H:i:s');
        $stmtUpdOK->bind_param('si', $fechaAhora, $nsms_id);
        $stmtUpdOK->execute();
        $stmtUpdOK->close();

        echo "== [DEBUG] nsms_id={$nsms_id} marcado como 'Enviado'\n";
    } else {
        // Si falló, incrementar nsms_intentos y marcar error
        $stmtUpdErr = $enlace_db->prepare("
          UPDATE administrador_notificaciones_sms
             SET nsms_estado_envio = CONCAT('Error: ', ?),
                 nsms_intentos     = nsms_intentos + 1
           WHERE nsms_id = ?
        ");
        // Usa la cadena de error de cURL o el contenido de respuesta
        $errorMsg = $curlErr ?: $response;
        $stmtUpdErr->bind_param('si', $errorMsg, $nsms_id);
        $stmtUpdErr->execute();
        $stmtUpdErr->close();

        error_log("cURL SMS error (nsms_id={$nsms_id}): HTTP_CODE={$httpCode}, resp={$response}, curlErr={$curlErr}");
        echo "== [ERROR] nsms_id={$nsms_id} fallo envío, error: {$errorMsg}\n";
    }
}

// 12) Liberar recursos y cerrar conexión
$resPendientesSMS->free();
$enlace_db->close();

// 13) Eco de fin
echo "== [DEBUG] Fin SMS cron: " . date('Y-m-d H:i:s') . "\n";
exit(0);
