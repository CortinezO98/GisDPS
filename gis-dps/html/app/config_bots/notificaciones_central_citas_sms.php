<?php
$modulo_plataforma = "Administrador";
require_once("/var/www/html/iniciador.php");

require_once("/var/www/html/templates/assets/plugins/guzzle-master/vendor/autoload.php");

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


error_reporting(E_ALL);
ini_set('display_errors', '0');  
ini_set('log_errors', '1');      
date_default_timezone_set('America/Bogota');
if (function_exists('mysqli_set_charset')) {
    @mysqli_set_charset($enlace_db, 'utf8mb4');
}


function actualizar_estado_sms(mysqli $db, string $estado, string $fecha, int $intentos, ?string $observaciones, int $id): void {
    $sql = "UPDATE `administrador_notificaciones_sms`
               SET `nsms_estado_envio` = ?,
                   `nsms_fecha_envio`  = ?,
                   `nsms_intentos`     = ?,
                   `nsms_observaciones`= ?
             WHERE `nsms_id` = ?";
    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param('ssisi', $estado, $fecha, $intentos, $observaciones, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log('[citas_sms] Error preparando UPDATE: ' . $db->error);
    }
}


function normalizar_destino(string $raw): string {
    $solo_digitos = preg_replace('/\D+/', '', $raw ?? '');
    return '57' . $solo_digitos;
}

$consulta = "
    SELECT `nsms_id`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`,
           `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`,
           `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`,
           `nsms_usuario_registro`, `nsms_fecha_registro`,
           TR.`nsmsr_api`, TR.`nsmsr_username`, TR.`nsmsr_password`
      FROM `administrador_notificaciones_sms`
 LEFT JOIN `administrador_buzones_sms` AS TR
        ON `administrador_notificaciones_sms`.`nsms_id_set_from` = TR.`nsmsr_id`
     WHERE (`nsms_estado_envio`='Pendiente' OR `nsms_estado_envio`='Error de autenticación')
       AND `nsms_id_modulo`='13'
  ORDER BY `nsms_prioridad`
     LIMIT 50 OFFSET 0
";
$res = mysqli_query($enlace_db, $consulta);
$rows = $res ? mysqli_fetch_all($res) : [];

if ($res) { mysqli_free_result($res); }

if (count($rows) > 0) {
    $client = new Client();

    foreach ($rows as $r) {
        $marca_temporal   = date("Y-m-d H:i:s");
        $id_notificacion  = (int)$r[0];

        $destino   = (string)$r[4];
        $body      = (string)$r[5];
        $api_url   = (string)$r[13];
        $api_user  = (string)$r[14];
        $api_pass  = (string)$r[15];

        if ($destino !== "" && $body !== "" && $api_url !== "" && $api_user !== "" && $api_pass !== "") {

            $num_intentos = ((int)$r[7]) + 1;
            $estado_error = ($num_intentos >= 2) ? "Error" : "Pendiente";

            try {
                $sms_to  = normalizar_destino($destino);
                $sms_url = (string)$r[6];

                $payload = [
                    'to'            => $sms_to,
                    'text'          => $body,
                    'customdata'    => 'IQGIS-CITAS-FA',
                    'isPremium'     => false,
                    'isFlash'       => false,
                    'isLongmessage' => true,
                    'isRandomRoute' => false
                ];

                if ($sms_url !== '') {
                    $payload['shortUrlConfig'] = ['url' => $sms_url];
                }

                $response = $client->post($api_url, [
                    'json' => $payload,
                    'auth' => [$api_user, $api_pass]
                ]);

                $jsonData = json_decode($response->getBody(), true);
                $statusMessage = isset($jsonData['statusMessage']) ? (string)$jsonData['statusMessage'] : '';
                $messageId     = isset($jsonData['messageId']) ? (string)$jsonData['messageId'] : '';
                $nsms_observaciones = $statusMessage . ';' . $messageId;

                if (isset($jsonData['statusCode']) && (int)$jsonData['statusCode'] === 200) {
                    actualizar_estado_sms($enlace_db, 'Enviado', $marca_temporal, $num_intentos, $nsms_observaciones, $id_notificacion);
                } else {
                    actualizar_estado_sms($enlace_db, $estado_error, $marca_temporal, $num_intentos, $nsms_observaciones, $id_notificacion);
                }

            } catch (RequestException $e) {
                $code    = (string)$e->getCode();
                $mensaje = $e->getMessage();

                $estado_error_final = '';
                if ($code === '400') {
                    $estado_error_final = 'Destinatario inválido';
                } elseif ($code === '401') {
                    $estado_error_final = 'Error de autenticación';
                }

                if ($estado_error_final !== '') {
                    actualizar_estado_sms($enlace_db, $estado_error_final, $marca_temporal, $num_intentos, $mensaje, $id_notificacion);
                } else {
                    actualizar_estado_sms($enlace_db, $estado_error, $marca_temporal, $num_intentos, $mensaje, $id_notificacion);
                }

                error_log('[citas_sms] RequestException id='.$id_notificacion.' code='.$code.' msg='.$mensaje);
            } catch (Exception $e) {
                $mensaje = $e->getMessage();
                actualizar_estado_sms($enlace_db, $estado_error, $marca_temporal, $num_intentos, $mensaje, $id_notificacion);
                error_log('[citas_sms] Exception id='.$id_notificacion.' msg='.$mensaje);
            }

        } else {
            actualizar_estado_sms($enlace_db, 'Error-estructura', $marca_temporal, 1, null, $id_notificacion);
        }
    }
}
?>
