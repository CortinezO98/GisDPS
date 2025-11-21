<?php
    require_once '../clases/token.class.php';

    $_token = new token;
    $fecha = date('Y-m-d');
    echo $_token->actualizarToken($fecha);
?>