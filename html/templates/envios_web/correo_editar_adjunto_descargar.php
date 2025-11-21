<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Envíos WEB";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['id_registro']));

    $consulta_string="SELECT `gewca_id`, `gewca_radicado`, `gewca_nombre`, `gewca_ruta`, `gewca_extension` FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_id`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $Url=$resultado_registros[0][3];
    $NombreDoc=$resultado_registros[0][2];
    header("Content-disposition: attachment; filename=".$NombreDoc);
    header("Content-type: MIME");
    header('Cache-Control: max-age=0');
    readfile($Url);
?>