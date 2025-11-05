<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Interacciones";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $token=generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6);
  
  if ($token!='') {
      header("Location:interacciones_crear?token=".$token);
  }
?>