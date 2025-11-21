<?php
  // require_once("../../modules/guzzle-master/vendor/autoload.php");
  // require_once("../../../config/microsoft-graph.class.php");
  // require_once("../../../config/conexion_db.php");
  require_once("/var/www/html/app/functions/microsoft-graph.class.php");
  $modulo_plataforma="Administrador";
  require_once("/var/www/html/iniciador.php");
  require_once("/var/www/html/templates/administrador/modules/guzzle-master/vendor/autoload.php");
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  
  use GuzzleHttp\Client;
  use GuzzleHttp\Exception\RequestException;
  use GuzzleHttp\Psr7\Request;
  
  ini_set('date.timezone', 'America/Bogota');
  
  $guzzle = new \GuzzleHttp\Client();
  // $guzzle = new \GuzzleHttp\Client(['verify' => '/var/www/html/config/6470c14b98d71132.pem']);

  $mail = new MicrosoftGraph();

  //consulta de credenciales cuenta correo
  $consulta_configuracion = mysqli_query($enlace_db, "SELECT `ncr_id`, `ncr_host`, `ncr_port`, `ncr_smtpsecure`, `ncr_smtpauth`, `ncr_username`, `ncr_password`, `ncr_setfrom`, `ncr_setfrom_name`, `ncr_tenant`, `ncr_client_id`, `ncr_client_secret`, `ncr_device_code`, `ncr_token`, `ncr_token_refresh` FROM `administrador_buzones` WHERE `ncr_tipo`='Lectura' AND `ncr_tenant`<>'' AND (`ncr_device_code`='' OR `ncr_token`='' OR `ncr_token_refresh`<>'')");
  $resultado_configuracion = mysqli_fetch_all($consulta_configuracion);

  for ($i=0; $i < count($resultado_configuracion); $i++) { 
    $ncr_id = $resultado_configuracion[$i][0];
    $ncr_tenant = $resultado_configuracion[$i][9];
    $ncr_client_id = $resultado_configuracion[$i][10];
    $ncr_client_secret = $resultado_configuracion[$i][11];
    $ncr_device_code = $resultado_configuracion[$i][12];
    $ncr_token = $resultado_configuracion[$i][13];
    $ncr_token_refresh = $resultado_configuracion[$i][14];
    
    $mail->tenant = $ncr_tenant; //Azure Active Directory Tenant ID, with Multitenant apps you can use "common" as Tenant ID, but using specific endpoint is recommended when possible
    $mail->client_id = $ncr_client_id; //Application (client) ID
    $mail->client_secret = $ncr_client_secret; //Client Secret, remember that this expires someday unless you haven't set it not to do so
    $mail->redirect_uri = 'https://gisdian.outsourcing.com.co'; //This needs to match 100% what is set in Azure
    $mail->auth_code=$ncr_device_code;
    $mail->token=$ncr_token;
    $mail->token_refresh=$ncr_token_refresh;
    
    if ($ncr_device_code=="") {//obtiene device_code
      $jtoken = $mail->get_code($guzzle);
      echo "<pre>";
      print_r($jtoken);
      echo "</pre>";

      // $ncr_token_update=$jtoken['access_token'];
      // $ncr_token_refresh_update=$jtoken['refresh_token'];

      // // Prepara la sentencia
      // $consulta_actualizar_token = $enlace_db->prepare("UPDATE `administrador_buzones` SET `ncr_token`=?, `ncr_token_refresh`=?, `ncr_fecha_actualiza`=? WHERE `ncr_id`=?");

      // // Agrega variables a sentencia preparada
      // $consulta_actualizar_token->bind_param('ssss', $ncr_token_update, $ncr_token_refresh_update, date('Y-m-d H:i:s'), $ncr_id);
      
      // // Ejecuta sentencia preparada
      // $consulta_actualizar_token->execute();

    }

    if ($ncr_device_code!="" AND $ncr_token=="") {//obtiene token inicial con device_code
      $jtoken = $mail->get_token($guzzle, false);
      // echo "<pre>";
      // print_r($jtoken);
      // echo "</pre>";

      $ncr_token_update=$jtoken['access_token'];
      $ncr_token_refresh_update=$jtoken['refresh_token'];

      // Prepara la sentencia
      $consulta_actualizar_token = $enlace_db->prepare("UPDATE `administrador_buzones` SET `ncr_token`=?, `ncr_token_refresh`=?, `ncr_fecha_actualiza`=? WHERE `ncr_id`=?");

      // Agrega variables a sentencia preparada
      $consulta_actualizar_token->bind_param('ssss', $ncr_token_update, $ncr_token_refresh_update, date('Y-m-d H:i:s'), $ncr_id);
      
      // Ejecuta sentencia preparada
      $consulta_actualizar_token->execute();

    }

    if ($ncr_token!="" AND $ncr_token_refresh!="") {//obtiene token inicial con device_code
      $jtoken = $mail->get_token($guzzle, true);
      // echo "<pre>";
      // print_r($jtoken);
      // echo "</pre>";

      $ncr_token_update=$jtoken['access_token'];
      $ncr_token_refresh_update=$jtoken['refresh_token'];

      // Prepara la sentencia
      $consulta_actualizar_token = $enlace_db->prepare("UPDATE `administrador_buzones` SET `ncr_token`=?, `ncr_token_refresh`=?, `ncr_fecha_actualiza`=? WHERE `ncr_id`=?");

      // Agrega variables a sentencia preparada
      $consulta_actualizar_token->bind_param('ssss', $ncr_token_update, $ncr_token_refresh_update, date('Y-m-d H:i:s'), $ncr_id);
      
      // Ejecuta sentencia preparada
      $consulta_actualizar_token->execute();
    }
  }
?>