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
$client_id = "885e835b-9be7-4645-b5fe-6fab6b0c0209";  //Application (client) ID
$ad_tenant = "140443af-8e79-4fce-b3ed-de001312984f";  //Azure Active Directory Tenant ID, with Multitenant apps you can use "common" as Tenant ID, but using specific endpoint is recommended when possible
$client_secret = "A738Q~mVyz16ceu8neXd2132bh3~U41Zhj2myb7q";  //Client Secret, remember that this expires someday unless you haven't set it not to do so
$redirect_uri = "https://dps.iq-online.net.co/oauth2.php";  //This needs to match 100% what is set in Azure
// unset($_SESSION);
if (isset($_GET['code'])) {
  $_SESSION['code']=$_GET["code"];
}

// $_SESSION['code']='0.AQIAcxL1lWFi9U6tKPPX1DVlPFmcpLgXkBpIvGWnmeRzrs4GAHk.AgABAAIAAAD--DLA3VO7QrddgJg7WevrAgDs_wQA9P_cjwTM0l2Uc20BIjBfTuijHBimdRwpuMSO04R4PF6pTpj-nRvRcfCnH3mOX3TTPR8HCb6xOluTfvTn-jToUxN9Dq_S90Fq1MjdFewB9IfPzTuChHquxJVQEaIyhp9ES75lo4afs6NBG0aBmdJfZsQlP5RymJziibJkj5VX9Uvik_F8wBvDrr17x03bWA0HdAbhCjPyEb1sNlAEJg8CLvtYU4_G-7W3znl9rwW3YUd4rGyITGxdfDuvWUEUFE6fQLwvTLhuoIdECnjgX2Xns84Cpqtj3CleecYiGKc7HoV6gslyrHtSw7NfwnovFrKWcoiXKOmKkaWZWfwkITrOxn8GFBlgAWkQMRzBAaIwGIEcyRR2yOOoQSEB59tinj9OwNdMafpuPq6Vbstl03khHlTzGo_FEUfxdmOWcskz2-Z5vLdP5BiFbyLNUCTH2ARRlJQ2u_b5GejxZOSCR-gfEIVWHySXATIViuW3IA1Hr5lDdJ0JmRb1EsnZn0sJpzo5_Y-J-WmU0w9fQl0ZhLmkPt_OT95S1uCB_iVO8pTq2YdE_AaJSYu9V6AwInpiJZl2hUJgsmwD8nxgg0MigRcwdzoFBhjGTOesQ0UkIjhIL1Egu4FGJPSJfOPQ-sh1FZlSk_1xattLXjQUuXyCa1i3PTSdul2gyh_5dcV9SVuvhwg1PYNiHMvzO3a47hZGrala5QI7nQJ7F9DvSlt0NOhAaObImYo';

// $_SESSION['token']='eyJ0eXAiOiJKV1QiLCJub25jZSI6ImJhQlVFcXc2cW5hUUVSYk9LNDdSTkFPYW02cmNJTVo3c3lhZU5vU2JLUE0iLCJhbGciOiJSUzI1NiIsIng1dCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyIsImtpZCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyJ9.eyJhdWQiOiJodHRwczovL2dyYXBoLm1pY3Jvc29mdC5jb20iLCJpc3MiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC85NWY1MTI3My02MjYxLTRlZjUtYWQyOC1mM2Q3ZDQzNTY1M2MvIiwiaWF0IjoxNjc0MjU1MjAxLCJuYmYiOjE2NzQyNTUyMDEsImV4cCI6MTY3NDI1OTkwOSwiYWNjdCI6MCwiYWNyIjoiMSIsImFpbyI6IkFUUUF5LzhUQUFBQXlabGYrTXhJbkw2dCs5YWtxZTR1cnF0QklVSUtISlhOK0tnaXR3L2I5dk5WVVUrQTFWWmpoZnJWTURkNnQrVE4iLCJhbXIiOlsicHdkIl0sImFwcF9kaXNwbGF5bmFtZSI6IkdUTzIiLCJhcHBpZCI6ImI4YTQ5YzU5LTkwMTctNDgxYS1iYzY1LWE3OTllNDczYWVjZSIsImFwcGlkYWNyIjoiMSIsImZhbWlseV9uYW1lIjoiSWJhcmd1ZW4gU2VybmEiLCJnaXZlbl9uYW1lIjoiTWFyaW8gU3RpdmVucyIsImlkdHlwIjoidXNlciIsImlwYWRkciI6IjQ1LjIzOC4xODEuMjQzIiwibmFtZSI6Ik1hcmlvIFN0aXZlbnMgSWJhcmd1ZW4gU2VybmEiLCJvaWQiOiJiZjBkZjFjZi04OTc3LTQxMzgtOTM4Yy0yZWZiMTk4YWQxZTIiLCJvbnByZW1fc2lkIjoiUy0xLTUtMjEtNTgzOTA3MjUyLTExMjM1NjE5NDUtNzI1MzQ1NTQzLTUzNTcwIiwicGxhdGYiOiIzIiwicHVpZCI6IjEwMDMzRkZGOUY3QjBCNjEiLCJyaCI6IjAuQVFJQWN4TDFsV0ZpOVU2dEtQUFgxRFZsUEFNQUFBQUFBQUFBd0FBQUFBQUFBQUFDQUdrLiIsInNjcCI6Ik1haWwuUmVhZCBVc2VyLlJlYWQgcHJvZmlsZSBvcGVuaWQgZW1haWwiLCJzdWIiOiJsUzZKT1Q4MzBrMkZkWlNFZW8wVERqTHlPc05ERjJ0clNxcElkbDdZT3U0IiwidGVuYW50X3JlZ2lvbl9zY29wZSI6IkVVIiwidGlkIjoiOTVmNTEyNzMtNjI2MS00ZWY1LWFkMjgtZjNkN2Q0MzU2NTNjIiwidW5pcXVlX25hbWUiOiJtc2liYXJndWVuQG9lc2lhLmNvbSIsInVwbiI6Im1zaWJhcmd1ZW5Ab2VzaWEuY29tIiwidXRpIjoieEVaS204U21LRWFBZVVkUnZmQ0pBQSIsInZlciI6IjEuMCIsIndpZHMiOlsiYjc5ZmJmNGQtM2VmOS00Njg5LTgxNDMtNzZiMTk0ZTg1NTA5Il0sInhtc19zdCI6eyJzdWIiOiJVcVl2b25nNTdnNEk2VXJuZ1pucVVEY3hVMVQtakhFalZyZjJ1UVY5YTdFIn0sInhtc190Y2R0IjoxMzE3OTYzOTY1LCJ4bXNfdGRiciI6IkVVIn0.qlrNVSSPIAr9TGVDZj9GLp1fXxW6kfMkliW47vyACHGAlWY2Ugr9f--lK2SnQNUmf5RhCgHa9zysYJ4Hzv9IHs3bQbBFugz7oG5FD8Jr14X3n5ZdfL1u3Zav4_OWDduERlcA5O4HZRDkKashQ2g2BZ1xFzRYaYk23s6w7VzEUWvsYs6Qu-jLDbXGIBA80l3FI1DBYPqLD9DETNUDs_tLegPjVTTCs0-91QAQHur5CQbtfHU2Bp7t3c3LlG6ZsnfsdBCOaIn4GLRXeUI01Lw0dSGSATKU9arkOEm3xxVWv5pGoHhASli6n7m3K1Q7di8joohB9SbE_-sldsOYrWGSGQ
// ';

if (($_SESSION['code']=="" AND $_SESSION['token']=="") OR (!isset($_SESSION['code']))) {
  // echo "ingreso code";
  $url = "https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/authorize?";
  $url .= "state=" . session_id();  //This at least semi-random string is likely good enough as state identifier
  $url .= "&scope=".urlencode('offline_access user.read mail.read');  //This scope seems to be enough, but you can try "&scope=profile+openid+email+offline_access+User.Read" if you like
  $url .= "&response_type=code";
  $url .= "&approval_prompt=auto";
  $url .= "&client_id=" . $client_id;
  $url .= "&redirect_uri=" . urlencode($redirect_uri);
  header("Location: " . $url);
}


if ($_SESSION['code']!="" AND $_SESSION['token']=="") {
  echo "ingreso token";
  $guzzle = new \GuzzleHttp\Client();
  $url = 'https://login.microsoftonline.com/' . $ad_tenant . '/oauth2/v2.0/token';
  $token = json_decode($guzzle->post($url, [
      'form_params' => [
          'client_id' => $client_id,
          'client_secret' => $client_secret,
          'scope' => 'https://graph.microsoft.com/.default',
          'code' => $_SESSION['code'],
          'redirect_uri' => $redirect_uri,
          'grant_type' => 'authorization_code',
      ],
  ])->getBody()->getContents());

  echo "<pre>";
  print_r($token);
  echo "</pre>";


  $_SESSION['token']=$accessToken = $token->access_token;
}

echo $_SESSION['code'];
echo "<hr>";
echo $_SESSION['token'];

//Fetching the basic user information that is likely needed by your application
  $options = array(
    "http" => array(  //Use "http" even if you send the request with https
      "method" => "GET",
      "header" => "Accept: application/json\r\n" .
        "Authorization: Bearer " . $_SESSION['token'] . "\r\n"
    )
  );
  $context = stream_context_create($options);
  $json = file_get_contents("https://graph.microsoft.com/v1.0/me/messages", false, $context);
  if ($json === false) errorhandler(array("Description" => "Error received during user data fetch.", "PHP_Error" => error_get_last(), "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);
  $userdata = json_decode($json, true);  //This should now contain your logged on user information

  echo "<pre>";
  print_r($userdata);
  echo "</pre>";







if (isset($_GET["code"])) echo "<pre>";  //This is just for easier and better looking var_dumps for debug purposes

if (!isset($_GET["code"]) and !isset($_GET["error"])) {  //Real authentication part begins
  // //First stage of the authentication process; This is just a simple redirect (first load of this page)
  // $url = "https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/authorize?";
  // $url .= "state=" . session_id();  //This at least semi-random string is likely good enough as state identifier
  // $url .= "&scope=".urlencode('offline_access user.read mail.read');  //This scope seems to be enough, but you can try "&scope=profile+openid+email+offline_access+User.Read" if you like
  // $url .= "&response_type=code";
  // $url .= "&approval_prompt=auto";
  // $url .= "&client_id=" . $client_id;
  // $url .= "&redirect_uri=" . urlencode($redirect_uri);
  // header("Location: " . $url);  //So off you go my dear browser and welcome back for round two after some redirects at Azure end

} elseif (isset($_GET["error"])) {  //Second load of this page begins, but hopefully we end up to the next elseif section...
  echo "Error handler activated:\n\n";
  // var_dump($_GET);  //Debug print
  errorhandler(array("Description" => "Error received at the beginning of second stage.", "\$_GET[]" => $_GET, "\$_SESSION[]" => $_SESSION), $error_email);
} elseif (strcmp(session_id(), $_GET["state"]) == 0) {  //Checking that the session_id matches to the state for security reasons
  echo "Stage2:\n\n";  //And now the browser has returned from its various redirects at Azure side and carrying some gifts inside $_GET
  // var_dump($_GET);  //Debug print


  // //Verifying the received tokens with Azure and finalizing the authentication part
  // $content = "grant_type=authorization_code";
  // // $content = "grant_type=client_credentials";
  // $content .= "&client_id=" . $client_id;
  // $content .= "&redirect_uri=" . urlencode($redirect_uri);
  // $content .= "&code=" . $_GET["code"];
  // $content .= "&client_secret=" . urlencode($client_secret);
  // $options = array(
  //   "http" => array(  //Use "http" even if you send the request with https
  //     "method"  => "POST",
  //     "header"  => "Content-Type: application/x-www-form-urlencoded\r\n" .
  //       "Content-Length: " . strlen($content) . "\r\n",
  //     "content" => $content
  //   )
  // );
  // $context  = stream_context_create($options);
  // $json = file_get_contents("https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/token", false, $context);
  // if ($json === false) errorhandler(array("Description" => "Error received during Bearer token fetch.", "PHP_Error" => error_get_last(), "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);
  // $authdata = json_decode($json, true);
  // if (isset($authdata["error"])) errorhandler(array("Description" => "Bearer token fetch contained an error.", "\$authdata[]" => $authdata, "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);

  // var_dump($authdata);  //Debug print

  // //Fetching the basic user information that is likely needed by your application
  // $options = array(
  //   "http" => array(  //Use "http" even if you send the request with https
  //     "method" => "GET",
  //     "header" => "Accept: application/json\r\n" .
  //       "Authorization: Bearer " . $authdata["access_token"] . "\r\n"
  //   )
  // );
  // $context = stream_context_create($options);
  // $json = file_get_contents("https://graph.microsoft.com/v1.0/me/messages", false, $context);
  // if ($json === false) errorhandler(array("Description" => "Error received during user data fetch.", "PHP_Error" => error_get_last(), "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);
  // $userdata = json_decode($json, true);  //This should now contain your logged on user information
  // if (isset($userdata["error"])) errorhandler(array("Description" => "User data fetch contained an error.", "\$userdata[]" => $userdata, "\$authdata[]" => $authdata, "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);

  // var_dump($userdata);  //Debug print
} else {
  //If we end up here, something has obviously gone wrong... Likely a hacking attempt since sent and returned state aren't matching and no $_GET["error"] received.
  echo "Hey, please don't try to hack us!\n\n";
  echo "PHP Session ID used as state: " . session_id() . "\n";  //And for production version you likely don't want to show these for the potential hacker
  var_dump($_GET);  //But this being a test script having the var_dumps might be useful
  errorhandler(array("Description" => "Likely a hacking attempt, due state mismatch.", "\$_GET[]" => $_GET, "\$_SESSION[]" => $_SESSION), $error_email);
}
echo "\n<a href=\"" . $redirect_uri . "\">Click here to redo the authentication</a>";  //Only to ease up your tests
?>