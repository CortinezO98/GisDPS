<?php

    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $_SERVER['HTTPS'] = 'on';
    }


    //Cargamos librerias
    session_start();
    error_reporting(0);
    ini_set('display_errors', '0');
    require_once('app/config/config.php');
    require_once("app/config/security_index.php");
    require_once("app/config/db.php");
?>
