<?php
    //Si sesion esta iniciada se redirige al contenido, sino muestra index de logueo//
    if(!isset($_SESSION[APP_SESSION.'_session_usu_id']) OR $_SESSION[APP_SESSION.'_session_usu_id']==null OR $_SESSION[APP_SESSION.'_session_usu_id']==""){
        header("Location:login");
    }
?>