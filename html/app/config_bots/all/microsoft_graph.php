<?php
  // require_once("../../modules/guzzle-master/vendor/autoload.php");
  // require_once("../../../config/microsoft-graph.class.php");
  // require_once("../../../config/conexion_db.php");
  require_once("/var/www/html/app/functions/microsoft-graph.class.php");
  
  $modulo_plataforma = "Administrador";
  require_once("/var/www/html/iniciador.php");
  require_once("/var/www/html/templates/administrador/modules/guzzle-master/vendor/autoload.php");

  // Nota: en producción es recomendable desactivar display_errors,
  // pero se deja como está para no cambiar el comportamiento global del sistema.
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  use GuzzleHttp\Client;
  use GuzzleHttp\Exception\RequestException;
  use GuzzleHttp\Psr7\Request;

  ini_set('date.timezone', 'America/Bogota');

  // Cliente HTTP para Microsoft Graph
  $guzzle = new \GuzzleHttp\Client();
  // $guzzle = new \GuzzleHttp\Client(['verify' => '/var/www/html/config/6470c14b98d71132.pem']);

  $mail = new MicrosoftGraph();

  // Consulta de credenciales de la cuenta de correo para lectura
  $consulta_configuracion = mysqli_query(
      $enlace_db,
      "SELECT `ncr_id`, `ncr_host`, `ncr_port`, `ncr_smtpsecure`, `ncr_smtpauth`, 
              `ncr_username`, `ncr_password`, `ncr_setfrom`, `ncr_setfrom_name`, 
              `ncr_tenant`, `ncr_client_id`, `ncr_client_secret`, `ncr_device_code`, 
              `ncr_token`, `ncr_token_refresh`
       FROM `administrador_buzones`
       WHERE `ncr_tipo` = 'Lectura'
         AND `ncr_tenant` <> ''
         AND (`ncr_device_code` = '' OR `ncr_token` = '' OR `ncr_token_refresh` <> '')"
  );

  $resultado_configuracion = mysqli_fetch_all($consulta_configuracion);

  for ($i = 0; $i < count($resultado_configuracion); $i++) {

    $ncr_id              = $resultado_configuracion[$i][0];
    $ncr_tenant          = $resultado_configuracion[$i][9];
    $ncr_client_id       = $resultado_configuracion[$i][10];
    $ncr_client_secret   = $resultado_configuracion[$i][11];
    $ncr_device_code     = $resultado_configuracion[$i][12];
    $ncr_token           = $resultado_configuracion[$i][13];
    $ncr_token_refresh   = $resultado_configuracion[$i][14];

    // Configuración del objeto MicrosoftGraph
    $mail->tenant        = $ncr_tenant;        // Tenant ID de Azure AD
    $mail->client_id     = $ncr_client_id;     // Application (client) ID
    $mail->client_secret = $ncr_client_secret; // Client Secret
    $mail->redirect_uri  = 'https://gisdian.outsourcing.com.co'; // Debe coincidir con Azure
    $mail->auth_code     = $ncr_device_code;
    $mail->token         = $ncr_token;
    $mail->token_refresh = $ncr_token_refresh;

    // 1️⃣ No hay device_code: se obtiene un nuevo device_code
    if ($ncr_device_code == "") {
      $jtoken = $mail->get_code($guzzle);

      // 🔐 REMEDIACIÓN XSS:
      // Antes se hacía un print_r($jtoken) en HTML.
      // Ahora NO se envía nada a la salida HTML, solo se deja en los logs del servidor.
      if (!empty($jtoken)) {
        error_log(
          '[MicrosoftGraph] get_code response (ncr_id=' . $ncr_id . '): ' .
          print_r($jtoken, true)
        );
      }

      // Aquí sigues teniendo la posibilidad de usar $jtoken para actualizar BD
      // si más adelante descomentas la lógica de actualización.
      /*
      $ncr_token_update         = $jtoken['access_token'];
      $ncr_token_refresh_update = $jtoken['refresh_token'];

      $consulta_actualizar_token = $enlace_db->prepare(
        "UPDATE `administrador_buzones`
         SET `ncr_token` = ?, `ncr_token_refresh` = ?, `ncr_fecha_actualiza` = ?
         WHERE `ncr_id` = ?"
      );

      $consulta_actualizar_token->bind_param(
        'ssss',
        $ncr_token_update,
        $ncr_token_refresh_update,
        date('Y-m-d H:i:s'),
        $ncr_id
      );

      $consulta_actualizar_token->execute();
      */
    }

    // 2️⃣ Ya hay device_code, pero no hay token: obtiene token inicial
    if ($ncr_device_code != "" && $ncr_token == "") {
      $jtoken = $mail->get_token($guzzle, false);

      // (Opcional) log seguro del token, NO se envía a HTML
      if (!empty($jtoken)) {
        error_log(
          '[MicrosoftGraph] get_token (initial) response (ncr_id=' . $ncr_id . '): ' .
          print_r($jtoken, true)
        );
      }

      $ncr_token_update         = $jtoken['access_token'];
      $ncr_token_refresh_update = $jtoken['refresh_token'];

      // Actualización de tokens en BD con sentencia preparada
      $consulta_actualizar_token = $enlace_db->prepare(
        "UPDATE `administrador_buzones`
         SET `ncr_token` = ?, `ncr_token_refresh` = ?, `ncr_fecha_actualiza` = ?
         WHERE `ncr_id` = ?"
      );

      $consulta_actualizar_token->bind_param(
        'ssss',
        $ncr_token_update,
        $ncr_token_refresh_update,
        date('Y-m-d H:i:s'),
        $ncr_id
      );

      $consulta_actualizar_token->execute();
    }

    // 3️⃣ Ya hay token y refresh_token: se renueva el token usando el refresh
    if ($ncr_token != "" && $ncr_token_refresh != "") {
      $jtoken = $mail->get_token($guzzle, true);

      // (Opcional) log seguro del token, NO se envía a HTML
      if (!empty($jtoken)) {
        error_log(
          '[MicrosoftGraph] get_token (refresh) response (ncr_id=' . $ncr_id . '): ' .
          print_r($jtoken, true)
        );
      }

      $ncr_token_update         = $jtoken['access_token'];
      $ncr_token_refresh_update = $jtoken['refresh_token'];

      // Actualización de tokens en BD con sentencia preparada
      $consulta_actualizar_token = $enlace_db->prepare(
        "UPDATE `administrador_buzones`
         SET `ncr_token` = ?, `ncr_token_refresh` = ?, `ncr_fecha_actualiza` = ?
         WHERE `ncr_id` = ?"
      );

      $consulta_actualizar_token->bind_param(
        'ssss',
        $ncr_token_update,
        $ncr_token_refresh_update,
        date('Y-m-d H:i:s'),
        $ncr_id
      );

      $consulta_actualizar_token->execute();
    }
  }
?>
