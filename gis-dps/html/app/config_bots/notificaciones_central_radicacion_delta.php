<?php
// Remediado: Second-Order SQL Injection y hardening leve sin cambiar funcionalidad

require_once("/var/www/html/app/functions/microsoft-graph-test.class.php");
$modulo_plataforma = "Administrador";
require_once("/var/www/html/iniciador.php");
require_once("/var/www/html/templates/administrador/modules/guzzle-master/vendor/autoload.php");
error_reporting(E_ALL);
ini_set('display_errors', '0'); 
ini_set('log_errors', '1');

ini_set('date.timezone', 'America/Bogota');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;


$guzzle = new \GuzzleHttp\Client();
$mail   = new MicrosoftGraph();


function update_estado_envio_rad(mysqli $db, string $estado, string $marcaTemporal, int $intentos, int $idCorreo): void {
    $stmt = $db->prepare("
        UPDATE `gestion_radicacion_casos_historial`
           SET `grch_estado_envio` = ?, `grch_fecha_envio` = ?, `grch_intentos` = ?
         WHERE `grch_id` = ?
    ");
    if ($stmt) {
        $stmt->bind_param('ssii', $estado, $marcaTemporal, $intentos, $idCorreo);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log('[rad_delta] Error preparando UPDATE: ' . $db->error);
    }
}


function path_dentro_de(string $base, string $rel): ?string {
    $baseReal = rtrim(realpath($base), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $destReal = realpath($base . DIRECTORY_SEPARATOR . ltrim($rel, DIRECTORY_SEPARATOR));
    if ($destReal !== false && strncmp($destReal, $baseReal, strlen($baseReal)) === 0) {
        return $destReal;
    }
    return null;
}


$consulta_notificaciones = mysqli_query(
    $enlace_db,
    "SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`,
            `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`,
            `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`,
            `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`,
            `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`,
            `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`,
            `grch_registro_usuario`, `grch_registro_fecha`,
            RT.`ncr_host`, RT.`ncr_port`, RT.`ncr_smtpsecure`, RT.`ncr_smtpauth`, RT.`ncr_username`, RT.`ncr_password`,
            RT.`ncr_setfrom`, RT.`ncr_setfrom_name`, RT.`ncr_tenant`, RT.`ncr_client_id`, RT.`ncr_client_secret`,
            RT.`ncr_device_code`, RT.`ncr_token`, RT.`ncr_token_refresh`
       FROM `gestion_radicacion_casos_historial`
  LEFT JOIN `administrador_buzones` AS RT
         ON `gestion_radicacion_casos_historial`.`grch_correo_de` = RT.`ncr_id`
      WHERE `grch_estado_envio`='Pendiente'
        AND `grch_gestion`='Radicación DELTA'
   ORDER BY `grch_registro_fecha`
      LIMIT 12 OFFSET 0"
);

$resultado_notificaciones = mysqli_fetch_all($consulta_notificaciones);

if (count($resultado_notificaciones) > 0) {

    $consulta_string_adjuntos = "
        SELECT `grca_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`,
               `grca_extension`, `grca_tipo`, `grca_estado`, `grca_radicado`, `grca_radicado_id`
          FROM `gestion_radicacion_casos_adjuntos`
         WHERE `grca_historial_id` = ? AND `grca_estado`='Activo'
      ORDER BY `grca_id` ASC
    ";
    $consulta_registros_adjuntos = $enlace_db->prepare($consulta_string_adjuntos);

    for ($i = 0; $i < count($resultado_notificaciones); $i++) {
        $marca_temporal = date("Y-m-d H:i:s");
        $id_correo = (int)$resultado_notificaciones[$i][0];

        if ($consulta_registros_adjuntos) {
            $consulta_registros_adjuntos->bind_param("i", $id_correo);
        }

        $from           = (string)$resultado_notificaciones[$i][38]; 
        $subject        = (string)$resultado_notificaciones[$i][20];
        $contenido_html = (string)$resultado_notificaciones[$i][21];
        $ncr_tenant        = (string)$resultado_notificaciones[$i][39];
        $ncr_client_id     = (string)$resultado_notificaciones[$i][40];
        $ncr_client_secret = (string)$resultado_notificaciones[$i][41];
        $ncr_device_code   = (string)$resultado_notificaciones[$i][42];
        $ncr_token         = (string)$resultado_notificaciones[$i][43];
        $ncr_token_refresh = (string)$resultado_notificaciones[$i][44];

        if ($from !== "" && $subject !== "" && $contenido_html !== "" && $ncr_tenant !== "" && $ncr_client_id !== "") {
            try {
                $mail->tenant        = $ncr_tenant;
                $mail->client_id     = $ncr_client_id;
                $mail->client_secret = $ncr_client_secret;
                $mail->redirect_uri  = 'https://portalkiosko.asdcloud.co/';
                $mail->auth_code     = $ncr_device_code;
                $mail->token         = $ncr_token;
                $mail->token_refresh = $ncr_token_refresh;

                $num_intentos = (int)$resultado_notificaciones[$i][26] + 1;
                $estado_error = ($num_intentos >= 2) ? 'Error' : 'Pendiente';
                $toRecipients  = [];
                $ccRecipients  = [];
                $bccRecipients = [];

                $destino_to = array_filter(explode(";", (string)$resultado_notificaciones[$i][16]));
                foreach ($destino_to as $addr) {
                    $addr = trim($addr);
                    if ($addr !== "") {
                        $toRecipients[]['emailAddress'] = ['address' => $addr];
                    }
                }

                $destino_cc = array_filter(explode(";", (string)$resultado_notificaciones[$i][17]));
                foreach ($destino_cc as $addr) {
                    $addr = trim($addr);
                    if ($addr !== "") {
                        $ccRecipients[]['emailAddress'] = ['address' => $addr];
                    }
                }

                $destino_bcc = array_filter(explode(";", (string)$resultado_notificaciones[$i][18]));
                foreach ($destino_bcc as $addr) {
                    $addr = trim($addr);
                    if ($addr !== "") {
                        $bccRecipients[]['emailAddress'] = ['address' => $addr];
                    }
                }

                $attachments = [];
                $img_base = '/var/www/html/';
                $image_embedded_ruta   = explode(";", (string)$resultado_notificaciones[$i][22]);
                $image_embedded_nombre = explode(";", (string)$resultado_notificaciones[$i][23]);
                $image_embedded_tipo   = explode(";", (string)$resultado_notificaciones[$i][24]);

                $count_imgs = max(count($image_embedded_ruta), count($image_embedded_nombre), count($image_embedded_tipo));
                for ($j = 0; $j < $count_imgs; $j++) {
                    $rutaImg   = $image_embedded_ruta[$j]   ?? '';
                    $nombreImg = $image_embedded_nombre[$j] ?? '';
                    $tipoImg   = $image_embedded_tipo[$j]   ?? '';
                    if ($rutaImg !== "" && $nombreImg !== "" && $tipoImg !== "") {
                        $rutaReal = path_dentro_de($img_base, $rutaImg);
                        if ($rutaReal && is_file($rutaReal)) {
                            $attachments[] = [
                                '@odata.type'  => '#microsoft.graph.fileAttachment',
                                'Name'         => $nombreImg,
                                'ContentBytes' => base64_encode(file_get_contents($rutaReal)),
                                'ContentType'  => mime_content_type($rutaReal),
                                'ContentId'    => $nombreImg
                            ];
                        }
                    }
                }

                $resultado_registros_adjuntos = [];
                if ($consulta_registros_adjuntos) {
                    $consulta_registros_adjuntos->execute();
                    $resultado_registros_adjuntos = $consulta_registros_adjuntos->get_result()->fetch_all(MYSQLI_NUM);
                }

                if (count($resultado_registros_adjuntos) > 0) {
                    $base_adj = '/var/www/html/templates/radicados/';
                    foreach ($resultado_registros_adjuntos as $adj) {
                        $rutaRel = (string)$adj[3];
                        $nombre_final = (string)$adj[2];
                        if ($rutaRel !== "") {
                            $ruta_final = path_dentro_de($base_adj, $rutaRel);
                            if ($ruta_final && file_exists($ruta_final)) {
                                $attachments[] = [
                                    '@odata.type'  => '#microsoft.graph.fileAttachment',
                                    'Name'         => $nombre_final,
                                    'ContentBytes' => base64_encode(file_get_contents($ruta_final)),
                                    'ContentType'  => mime_content_type($ruta_final)
                                ];
                            }
                        }
                    }
                }

                $contenido_correo = str_replace(
                    'https://portalkiosko.asdcloud.co/templates/assets/images/logo_cliente_notificacion_2.png',
                    'cid:logo_cliente_notificacion',
                    $contenido_html
                );
                $contenido_correo = str_replace(
                    'https://portalkiosko.asdcloud.co/templates/assets/images/logo_certificacion_notificacion_2.png',
                    'cid:logo_certificacion_notificacion',
                    $contenido_correo
                );

                $body = [
                    'contentType' => 'html',
                    'content'     => $contenido_correo
                ];

                $resultado_envio = $mail->mail_send($guzzle, $from, $subject, $body, $toRecipients, $ccRecipients, $bccRecipients, $attachments);

                if ($resultado_envio === '') {
                    update_estado_envio_rad($enlace_db, 'Enviado', $marca_temporal, $num_intentos, $id_correo);
                } elseif ($resultado_envio === '401') {
                    update_estado_envio_rad($enlace_db, 'Error de autenticación', $marca_temporal, $num_intentos, $id_correo);
                } elseif ($resultado_envio === '400') {
                    update_estado_envio_rad($enlace_db, 'Error-estructura-envío', $marca_temporal, $num_intentos, $id_correo);
                } else {
                    update_estado_envio_rad($enlace_db, 'Error', $marca_temporal, $num_intentos, $id_correo);
                    error_log('[rad_delta] Error genérico envío ID ' . $id_correo . ': ' . print_r($resultado_envio, true));
                }

            } catch (Exception $e) {
                $reporte_error = (string)$e->getMessage();
                $estado_error_final = '';

                if (stripos($reporte_error, 'Invalid address:') !== false) {
                    $estado_error_final = 'Destinatario inválido';
                } elseif ($reporte_error === 'SMTP Error: Could not authenticate.') {
                    $estado_error_final = 'Error de autenticación';
                } elseif ($reporte_error === 'You must provide at least one recipient email address.') {
                    $estado_error_final = 'Sin destinatario';
                } else {
                    $estado_error_final = 'Error';
                }

                $num_intentos = (int)$resultado_notificaciones[$i][26] + 1;
                update_estado_envio_rad($enlace_db, $estado_error_final, $marca_temporal, $num_intentos, $id_correo);
                error_log('[rad_delta] Excepción ID ' . $id_correo . ': ' . $reporte_error);
            }

        } else {
            update_estado_envio_rad($enlace_db, 'Error-estructura', $marca_temporal, 1, $id_correo);
        }
    }

    if ($consulta_registros_adjuntos) {
        $consulta_registros_adjuntos->close();
    }
}
?>
