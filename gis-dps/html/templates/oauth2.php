<?php

require_once("assets/plugins/guzzle-master/vendor/autoload.php");
use GuzzleHttp\Client;

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');      
ini_set('log_errors', '1');         

header('Content-Type: text/html; charset=UTF-8');

$client_id     = "885e835b-9be7-4645-b5fe-6fab6b0c0209";        
$ad_tenant     = "140443af-8e79-4fce-b3ed-de001312984f";         
$client_secret = "A738Q~mVyz16ceu8neXd2132bh3~U41Zhj2myb7q";    
$redirect_uri  = "https://dps.iq-online.net.co/oauth2.php";     


function secure_log($msg) {
    error_log('[OAUTH2] ' . $msg);
}

function safe_redirect($url) {
    header('Location: ' . $url, true, 302);
    exit;
}

if (isset($_GET['code'])) {
    $_SESSION['code'] = (string)$_GET['code'];
}


if ((!isset($_SESSION['code']) || $_SESSION['code'] === '') && (!isset($_SESSION['token']) || $_SESSION['token'] === '')) {
    $authorize = "https://login.microsoftonline.com/" . rawurlencode($ad_tenant) . "/oauth2/v2.0/authorize?";
    $authorize .= "state=" . urlencode(session_id());
    $authorize .= "&scope=" . urlencode('offline_access user.read mail.read');
    $authorize .= "&response_type=code";
    $authorize .= "&approval_prompt=auto";
    $authorize .= "&client_id=" . urlencode($client_id);
    $authorize .= "&redirect_uri=" . urlencode($redirect_uri);
    safe_redirect($authorize);
}


if ((isset($_SESSION['code']) && $_SESSION['code'] !== '') && (!isset($_SESSION['token']) || $_SESSION['token'] === '')) {
    try {
        $client = new Client();
        $url = 'https://login.microsoftonline.com/' . $ad_tenant . '/oauth2/v2.0/token';

        $res = $client->post($url, [
            'form_params' => [
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'scope'         => 'https://graph.microsoft.com/.default',
                'code'          => $_SESSION['code'],
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code',
            ],
            'http_errors' => false,
            'timeout'     => 15,
        ]);

        $body  = (string)$res->getBody();
        $token = json_decode($body, true);

        if (!is_array($token) || empty($token['access_token'])) {
            secure_log('Token exchange failed: HTTP ' . $res->getStatusCode() . ' Body: ' . substr($body, 0, 500));
            echo 'Ocurrió un problema al iniciar sesión. Intenta nuevamente.';
            exit;
        }

        $_SESSION['token'] = $token['access_token'];

        secure_log('Token stored in session (access_token length=' . strlen($_SESSION['token']) . ').');

    } catch (\Throwable $e) {
        secure_log('Exception during token exchange: ' . $e->getMessage());
        echo 'Ocurrió un problema al iniciar sesión. Intenta nuevamente.';
        exit;
    }
}


if (isset($_SESSION['token']) && $_SESSION['token'] !== '') {

    try {
        $opts = [
            'http' => [
                'method'  => 'GET',
                'header'  => "Accept: application/json\r\nAuthorization: Bearer " . $_SESSION['token'] . "\r\n",
                'timeout' => 15,
            ]
        ];
        $context = stream_context_create($opts);
        $json    = @file_get_contents("https://graph.microsoft.com/v1.0/me/messages", false, $context);

        if ($json === false) {
            secure_log('Graph /me/messages failed: ' . json_encode(error_get_last()));
        } else {
        }
    } catch (\Throwable $e) {
        secure_log('Exception on Graph call: ' . $e->getMessage());
    }
    echo 'Autenticación completada correctamente.';
    exit;
}

echo 'Flujo de autenticación no válido o incompleto.';
exit;
