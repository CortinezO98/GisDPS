<?php


require_once("/var/www/html/app/functions/microsoft-graph.class.php");
$modulo_plataforma = "Administrador";
require_once("/var/www/html/iniciador.php");
require_once("/var/www/html/templates/administrador/modules/guzzle-master/vendor/autoload.php");
error_reporting(E_ALL);
ini_set('display_errors', '0'); 

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

date_default_timezone_set('America/Bogota');

$guzzle = new \GuzzleHttp\Client();

$mail = new MicrosoftGraph();


$mail->redirect_uri = 'https://portalkiosko.asdcloud.co/'; 


$consulta_configuracion = mysqli_query($enlace_db,
  "SELECT `ncr_id`, `ncr_host`, `ncr_port`, `ncr_smtpsecure`, `ncr_smtpauth`, `ncr_username`, `ncr_password`, `ncr_setfrom`, `ncr_setfrom_name`, `ncr_tenant`, `ncr_client_id`, `ncr_client_secret`, `ncr_device_code`, `ncr_token`, `ncr_token_refresh` 
   FROM `administrador_buzones` 
   WHERE `ncr_tipo`='Lectura' AND `ncr_tenant`<>'' 
     AND (`ncr_device_code`='' OR `ncr_token`='' OR `ncr_token_refresh`<>'')"
);

$resultado_configuracion = mysqli_fetch_all($consulta_configuracion);

for ($i = 0; $i < count($resultado_configuracion); $i++) {
    $ncr_id = $resultado_configuracion[$i][0];
    $ncr_tenant = $resultado_configuracion[$i][9];
    $ncr_client_id = $resultado_configuracion[$i][10];
    $ncr_client_secret = $resultado_configuracion[$i][11];
    $ncr_device_code = $resultado_configuracion[$i][12];
    $ncr_token = $resultado_configuracion[$i][13];
    $ncr_token_refresh = $resultado_configuracion[$i][14];
    
    $mail->tenant = $ncr_tenant;
    $mail->client_id = $ncr_client_id;
    $mail->client_secret = $ncr_client_secret;
    $mail->auth_code = $ncr_device_code;
    $mail->token = $ncr_token;
    $mail->token_refresh = $ncr_token_refresh;


    if ($ncr_device_code == "") { 
        try {
            $jtoken = $mail->get_code($guzzle);
            $logPath = '/var/log/app/microsoft_graph_tokens.log';
            $logEntry = sprintf("[%s] DEVICE_CODE_RESPONSE for ncr_id=%s : %s\n",
                date('Y-m-d H:i:s'), $ncr_id, json_encode($jtoken, JSON_UNESCAPED_UNICODE));
            @file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);

        } catch (\Throwable $e) {
            error_log("[MICROSOFT_GRAPH] get_code failed for ncr_id=$ncr_id : " . $e->getMessage());
        }
    }

    if ($ncr_device_code != "" && $ncr_token == "") { 
        try {
            $jtoken = $mail->get_token($guzzle, false);

            if (isset($jtoken['access_token'])) {
                $ncr_token_update = $jtoken['access_token'];
            } else {
                $ncr_token_update = '';
            }
            if (isset($jtoken['refresh_token'])) {
                $ncr_token_refresh_update = $jtoken['refresh_token'];
            } else {
                $ncr_token_refresh_update = '';
            }

            $consulta_actualizar_token = $enlace_db->prepare(
                "UPDATE `administrador_buzones` SET `ncr_token`=?, `ncr_token_refresh`=?, `ncr_fecha_actualiza`=? WHERE `ncr_id`=?"
            );
            if ($consulta_actualizar_token !== false) {
                $fecha_now = date('Y-m-d H:i:s');
                $consulta_actualizar_token->bind_param('ssss', $ncr_token_update, $ncr_token_refresh_update, $fecha_now, $ncr_id);
                $consulta_actualizar_token->execute();
                $consulta_actualizar_token->close();
            } else {
                error_log("[MICROSOFT_GRAPH] prepare update token failed: " . $enlace_db->error);
            }

            error_log("[MICROSOFT_GRAPH] token obtained and stored for ncr_id=$ncr_id");
        } catch (\Throwable $e) {
            error_log("[MICROSOFT_GRAPH] get_token(initial) failed for ncr_id=$ncr_id : " . $e->getMessage());
        }
    }

    if ($ncr_token != "" && $ncr_token_refresh != "") {
        try {
            $jtoken = $mail->get_token($guzzle, true);

            $ncr_token_update = $jtoken['access_token'] ?? '';
            $ncr_token_refresh_update = $jtoken['refresh_token'] ?? '';

            $consulta_actualizar_token = $enlace_db->prepare(
                "UPDATE `administrador_buzones` SET `ncr_token`=?, `ncr_token_refresh`=?, `ncr_fecha_actualiza`=? WHERE `ncr_id`=?"
            );
            if ($consulta_actualizar_token !== false) {
                $fecha_now = date('Y-m-d H:i:s');
                $consulta_actualizar_token->bind_param('ssss', $ncr_token_update, $ncr_token_refresh_update, $fecha_now, $ncr_id);
                $consulta_actualizar_token->execute();
                $consulta_actualizar_token->close();
            } else {
                error_log("[MICROSOFT_GRAPH] prepare update token failed: " . $enlace_db->error);
            }

            error_log("[MICROSOFT_GRAPH] token refreshed and stored for ncr_id=$ncr_id");
        } catch (\Throwable $e) {
            error_log("[MICROSOFT_GRAPH] get_token(refresh) failed for ncr_id=$ncr_id : " . $e->getMessage());
        }
    }
}
