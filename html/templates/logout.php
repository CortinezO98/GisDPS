<?php
  require_once("../iniciador_index.php");
  registro_log($enlace_db, 'Login', 'cierre_sesion', 'Cierre de sesión');
  session_destroy();
  header("Location: login");
  exit;
?>



