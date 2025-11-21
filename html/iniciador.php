<?php
    //Cargamos librerias
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once(__DIR__.'/app/config/config.php');
    require_once(__DIR__.'/app/config/security_index.php');
    require_once(__DIR__.'/app/config/db.php');
?>
