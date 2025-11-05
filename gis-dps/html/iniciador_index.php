<?php

if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

    //Cargamos librerias
session_start();
error_reporting(0);
ini_set('display_errors', '0');
require_once(__DIR__.'/app/config/config.php');
require_once(__DIR__.'/app/config/security_index.php');
require_once(__DIR__.'/app/config/db.php');

?>
