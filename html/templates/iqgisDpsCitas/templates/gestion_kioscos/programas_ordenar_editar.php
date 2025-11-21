<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  $array_contenidos = $_POST['miorden'];

    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_kioscos_programas` SET `gkp_orden`=? WHERE `gkp_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('ss', $orden, $id_pregunta);
    

    $orden=0;
    for ($i=0; $i < count($array_contenidos); $i++) { 
        $orden++;
        $id_pregunta = validar_input($array_contenidos[$i]);
        
        // Ejecuta sentencia preparada
        $consulta_actualizar->execute();
    }

    if ($orden==count($array_contenidos)) {
        $resultado_valor=1;
    } else {
        $resultado_valor=0;
    }

    if ($resultado_valor) {
        echo '<p class="alert alert-success font-size-11 p-1">Orden actualizado</p>';
    } else {
        echo '<p class="alert alert-warning font-size-11 p-1">Problemas al actualizar el orden</p>';
    }

?>