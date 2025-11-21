<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Gestión Conocimiento";
    require_once("../../iniciador.php");
    $id_registro=validar_input(base64_decode($_GET['reg']));

    $actualizar_conteo="UPDATE `gestion_conocimiento` SET `gc_visitas`=`gc_visitas`+1 WHERE `gc_codigo`=?";

    $consulta_conteo = $enlace_db->prepare($actualizar_conteo);
    $consulta_conteo->bind_param("s", $id_registro);
    $consulta_conteo->execute();

    header('Location:conocimiento_ver?reg='.base64_encode($id_registro));

    
?>