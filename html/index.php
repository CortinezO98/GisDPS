<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('iniciador_index.php');

if (!isset($_SESSION[APP_SESSION.'_session_usu_id']) || empty($_SESSION[APP_SESSION.'_session_usu_id'])) {
    header('Location: templates/login.php');
    exit;
}

header('Location: templates/dashboard.php');
exit;
?>
