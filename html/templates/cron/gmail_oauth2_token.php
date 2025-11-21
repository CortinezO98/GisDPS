<?php
// ===================================================================
// gmail_oauth2_token.php
//
// Este script se ejecuta MANUALMENTE (una sola vez) para generar
// el refresh_token y el access_token de tu cuenta Gmail mediante
// el flujo de "Redirect to localhost" (sin usar OOB).
//
// Pasos de uso:
// 1) php gmail_oauth2_token.php
// 2) Copia la URL que muestra el script y pégala en tu navegador.
// 3) Inicia sesión con la cuenta Gmail “notificaciones-iqgis@iq-online.com”
//    y acepta los permisos.
// 4) Google te redirigirá a http://127.0.0.1:8080/?code=XXXXX.
//    Copia el valor completo del parámetro "code" que aparece en la URL.
// 5) Pégalo en la consola donde se ejecutó este script y presiona Enter.
// 6) El script guardará token.json con access_token y refresh_token.
// ===================================================================

// 1) Mostrar todos los errores (solo para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Incluir autoload de Composer
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    echo "== [ERROR] No se encontró el autoload de Composer en:\n   {$autoloadPath}\n";
    exit(1);
}
require_once $autoloadPath;

// 3) Importar las clases necesarias
use Google\Client as Google_Client;
use Google\Service\Gmail as Google_Service_Gmail;

// 4) Verificar que exista credentials.json (Desktop App)
$credentialsPath = __DIR__ . '/credentials.json';
if (!file_exists($credentialsPath)) {
    echo "== [ERROR] No se encontró credentials.json en:\n   {$credentialsPath}\n";
    exit(1);
}

// 5) Crear el cliente de Google
$client = new Google_Client();
$client->setApplicationName('Notificaciones IQGIS');

// 6) Cargar configuración de credenciales (client_id, client_secret, redirect_uris)
$client->setAuthConfig($credentialsPath);

// 7) Indicar el scope: vamos a usar Gmail para enviar correo
$client->setScopes([Google_Service_Gmail::GMAIL_SEND]);

// 8) Necesitamos “offline” para obtener refresh_token
$client->setAccessType('offline');

// 9) Forzar el redirect URI a “http://127.0.0.1:8080” (asegúrate de agregarlo en credentials.json)
$client->setRedirectUri('http://127.0.0.1:8080');

// 10) Generar la URL de autorización
$authUrl = $client->createAuthUrl();
echo "===========================================\n";
echo "1) Abre esta URL en tu navegador e inicia sesión con tu cuenta Gmail:\n\n";
echo "   {$authUrl}\n\n";
echo "2) Permite a la app enviar correos en tu nombre.\n";
echo "3) Google te redirigirá a:\n   http://127.0.0.1:8080/?code=TU_CODIGO_AQUI\n\n";
echo "Copia el valor completo del parámetro 'code' (por ejemplo, '4/abcdefgXYZ123')\n";
echo "y pégalo aquí en la consola.\n\n";
echo "Introduce el código de autorización y presiona Enter: ";

// 11) Leer el input (código de Google) desde STDIN
$handle = fopen("php://stdin", "r");
$code   = trim(fgets($handle));

// 12) Intercambiar el código por access_token y refresh_token
try {
    $accessToken = $client->fetchAccessTokenWithAuthCode($code);
} catch (Exception $e) {
    echo "== [ERROR] Al intercambiar el código: " . $e->getMessage() . "\n";
    exit(1);
}

// 13) Comprobar si Google devolvió error en la respuesta
if (array_key_exists('error', $accessToken)) {
    echo "== [ERROR] Google API devolvió un error:\n   " . print_r($accessToken, true) . "\n";
    exit(1);
}

// 14) Guardar el token (access + refresh) en un archivo token.json
$tokenPath = __DIR__ . '/token.json';
file_put_contents($tokenPath, json_encode($accessToken, JSON_PRETTY_PRINT));
echo "\n>>> Tokens guardados en: {$tokenPath}\n";

// 15) Mostrar el refresh_token por pantalla (puede servirte para validarlo):
echo "Refresh Token: " . ($accessToken['refresh_token'] ?? 'NO SE OBTUVO refresh_token') . "\n";
echo "Access Token válido por (segundos): " . ($accessToken['expires_in'] ?? '?') . "\n";
echo "===========================================\n";

exit(0);
