<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Radicación";
    require_once("../../iniciador.php");
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['id_registro']));

    $consulta_string="SELECT `grca_id`, `grca_radicado`, `grca_nombre`, `grca_ruta`, `grca_extension` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_id`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $Url=$resultado_registros[0][3];
    $NombreDoc=$resultado_registros[0][2];

    // echo strtolower(pathinfo($NombreDoc, PATHINFO_EXTENSION));
    if (strtolower(pathinfo($NombreDoc, PATHINFO_EXTENSION))=='zip') {
        header('Content-Type: application/zip');
    } else {
        header("Content-type: MIME");
    }

    header("Content-disposition: attachment; filename=".$NombreDoc);
    header('Cache-Control: max-age=0');
    ob_clean();
    readfile($Url);
?>