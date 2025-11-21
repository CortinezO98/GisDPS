<?php
	session_start();
    //Si sesion esta iniciada se redirige al contenido, sino muestra index de logueo//
    if(!isset($_SESSION[APP_SESSION.'_session_usu_id']) OR $_SESSION[APP_SESSION.'_session_usu_id']==null OR $_SESSION[APP_SESSION.'_session_usu_id']==""){
    	header("Location:../login");
    } else {
        if (isset($_SESSION[APP_SESSION.'_session_modulos'][$modulo_plataforma]) AND $_SESSION[APP_SESSION.'_session_modulos'][$modulo_plataforma]!="") {
        	$permisos_usuario=$_SESSION[APP_SESSION.'_session_modulos'][$modulo_plataforma];
        } else {
        	header("Location:../error-unauthorized");
        }
    }

    require_once("functions.php");
?>