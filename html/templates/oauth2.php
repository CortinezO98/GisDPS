<?php
  // require_once("../../app/modules/guzzle-master/vendor/autoload.php");
  require_once("assets/plugins/guzzle-master/vendor/autoload.php");
  use GuzzleHttp\Client;
  use GuzzleHttp\Exception\RequestException;
  use GuzzleHttp\Psr7\Request;

session_start();  //Since you likely need to maintain the user session, let's start it an utilize it's ID later
error_reporting(-1);  //Remove from production version
ini_set("display_errors", "on");  //Remove from production version

//Configuration, needs to match with Azure app registration
$client_id     = "885e835b-9be7-4645-b5fe-6fab6b0c0209";  //Application (client) ID
$ad_tenant     = "140443af-8e79-4fce-b3ed-de001312984f";  //Azure Active Directory Tenant ID
$client_secret = "A738Q~mVyz16ceu8neXd2132bh3~U41Zhj2myb7q";  //Client Secret
$redirect_uri  = "https://dps.iq-online.net.co/oauth2.php";   //Must match Azure config

// unset($_SESSION);
if (isset($_GET['code'])) {
  $_SESSION['code'] = $_GET["code"];
}

// $_SESSION['code'] = '...';
// $_SESSION['token'] = '...';

if (
    (empty($_SESSION['code']) && empty($_SESSION['token']))
    || !isset($_SESSION['code'])
) {
  // echo "ingreso code";
  $url  = "https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/authorize?";
  $url .= "state=" . session_id();  //state identifier
  $url .= "&scope=" . urlencode('offline_access user.read mail.read');
  $url .= "&response_type=code";
  $url .= "&approval_prompt=auto";
  $url .= "&client_id=" . $client_id;
  $url .= "&redirect_uri=" . urlencode($redirect_uri);
  header("Location: " . $url);
  exit;
}

if (!empty($_SESSION['code']) && empty($_SESSION['token'])) {
  echo "ingreso token";
  $guzzle = new \GuzzleHttp\Client();
  $url    = 'https://login.microsoftonline.com/' . $ad_tenant . '/oauth2/v2.0/token';

  $token = json_decode(
      $guzzle->post($url, [
          'form_params' => [
              'client_id'     => $client_id,
              'client_secret' => $client_secret,
              'scope'         => 'https://graph.microsoft.com/.default',
              'code'          => $_SESSION['code'],
              'redirect_uri'  => $redirect_uri,
              'grant_type'    => 'authorization_code',
          ],
      ])->getBody()->getContents(),
      true
  );

  echo "<pre>";
  // 🔐 Sanitizamos la impresión del token (debug seguro)
  echo htmlspecialchars(print_r($token, true), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  echo "</pre>";

  if (isset($token['access_token'])) {
      $_SESSION['token'] = $accessToken = $token['access_token'];
  }
}

// Mostrar code y token (para debug) de forma segura
echo htmlspecialchars($_SESSION['code'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo "<hr>";
echo htmlspecialchars($_SESSION['token'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

//Fetching the basic user information that is likely needed by your application
$options = [
    "http" => [  //Use "http" even if you send the request with https
        "method" => "GET",
        "header" => "Accept: application/json\r\n" .
            "Authorization: Bearer " . ($_SESSION['token'] ?? '') . "\r\n"
    ]
];
$context = stream_context_create($options);
$json    = file_get_contents("https://graph.microsoft.com/v1.0/me/messages", false, $context);
if ($json === false) {
    errorhandler(
        [
            "Description" => "Error received during user data fetch.",
            "PHP_Error"   => error_get_last(),
            "\$_GET[]"    => $_GET,
            "HTTP_msg"    => $options
        ],
        $error_email
    );
}
$userdata = json_decode($json, true);  //This should now contain your logged on user information

echo "<pre>";
// 🔐 Sanitizamos la impresión de userdata (REMEDIA Stored XSS)
echo htmlspecialchars(print_r($userdata, true), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo "</pre>";

if (isset($_GET["code"])) {
    echo "<pre>";  //Just for nicer dumps (aunque ahora no hacemos var_dump aquí)
}

if (!isset($_GET["code"]) && !isset($_GET["error"])) {
  // Real authentication part begins
  // (esta parte está comentada en tu código original y se deja igual)
  // ...
} elseif (isset($_GET["error"])) {  //Second load, but with error
  echo "Error handler activated:\n\n";
  // var_dump($_GET);  //Debug print
  errorhandler(
      [
          "Description" => "Error received at the beginning of second stage.",
          "\$_GET[]"    => $_GET,
          "\$_SESSION[]" => $_SESSION
      ],
      $error_email
  );
} elseif (isset($_GET["state"]) && strcmp(session_id(), $_GET["state"]) == 0) {
  echo "Stage2:\n\n";
  // var_dump($_GET);  //Debug print

  // Toda la lógica comentada de $authdata se deja intacta, ya que no se usa actualmente
} else {
  //If we end up here, likely a hacking attempt since state mismatches and no $_GET["error"]
  echo "Hey, please don't try to hack us!\n\n";
  echo "PHP Session ID used as state: " . session_id() . "\n";
  var_dump($_GET);
  errorhandler(
      [
          "Description" => "Likely a hacking attempt, due state mismatch.",
          "\$_GET[]"    => $_GET,
          "\$_SESSION[]" => $_SESSION
      ],
      $error_email
  );
}

// Enlace para repetir la autenticación (redirect_uri es constante, pero lo codificamos por buenas prácticas)
echo "\n<a href=\"" . htmlspecialchars($redirect_uri, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "\">Click here to redo the authentication</a>";
?>
