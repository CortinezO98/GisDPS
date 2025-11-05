<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas FA-Punto Atención";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas FA";
  $subtitle = "Puntos de Atención | Configurar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="punto_atencion_fa?pagina=".$pagina."&id=".$filtro_permanente;

    $consulta_string_usuario="SELECT `gcpau_id`, `gcpau_punto_atencion`, `gcpau_usuario`, `gcpau_lunes`, `gcpau_lunes_break_1`, `gcpau_lunes_break_2`, `gcpau_lunes_almuerzo`, `gcpau_martes`, `gcpau_martes_break_1`, `gcpau_martes_break_2`, `gcpau_martes_almuerzo`, `gcpau_miercoles`, `gcpau_miercoles_break_1`, `gcpau_miercoles_break_2`, `gcpau_miercoles_almuerzo`, `gcpau_jueves`, `gcpau_jueves_break_1`, `gcpau_jueves_break_2`, `gcpau_jueves_almuerzo`, `gcpau_viernes`, `gcpau_viernes_break_1`, `gcpau_viernes_break_2`, `gcpau_viernes_almuerzo`, `gcpau_sabado`, `gcpau_sabado_break_1`, `gcpau_sabado_break_2`, `gcpau_sabado_almuerzo`, `gcpau_domingo`, `gcpau_domingo_break_1`, `gcpau_domingo_break_2`, `gcpau_domingo_almuerzo` FROM `gestion_citasfa_punto_atencion_usuario` WHERE `gcpau_punto_atencion`=?";

    $consulta_registros_usuario = $enlace_db->prepare($consulta_string_usuario);
    $consulta_registros_usuario->bind_param("s", $id_registro);
    $consulta_registros_usuario->execute();
    $resultado_registros_usuario = $consulta_registros_usuario->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
    for ($i=0; $i < count($resultado_registros_usuario); $i++) { 
      $usuario_dias[$resultado_registros_usuario[$i][2]]['dias']=$_POST["usuario_".$resultado_registros_usuario[$i][2]."_dia"];
      for ($j=1; $j <= count($array_dias_nombre); $j++) { 
        $usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][$j]=$_POST["horario_atencion_".$resultado_registros_usuario[$i][2]."_".$j];
        // $usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][$j]=$_POST["horario_break_1_".$resultado_registros_usuario[$i][2]."_".$j];
        // $usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][$j]=$_POST["horario_almuerzo_".$resultado_registros_usuario[$i][2]."_".$j];
        // $usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][$j]=$_POST["horario_break_2_".$resultado_registros_usuario[$i][2]."_".$j];
      }
    }

    // Prepara la sentencia
    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_citasfa_punto_atencion_usuario`(`gcpau_id`, `gcpau_punto_atencion`, `gcpau_usuario`, `gcpau_lunes`, `gcpau_lunes_break_1`, `gcpau_lunes_break_2`, `gcpau_lunes_almuerzo`, `gcpau_martes`, `gcpau_martes_break_1`, `gcpau_martes_break_2`, `gcpau_martes_almuerzo`, `gcpau_miercoles`, `gcpau_miercoles_break_1`, `gcpau_miercoles_break_2`, `gcpau_miercoles_almuerzo`, `gcpau_jueves`, `gcpau_jueves_break_1`, `gcpau_jueves_break_2`, `gcpau_jueves_almuerzo`, `gcpau_viernes`, `gcpau_viernes_break_1`, `gcpau_viernes_break_2`, `gcpau_viernes_almuerzo`, `gcpau_sabado`, `gcpau_sabado_break_1`, `gcpau_sabado_break_2`, `gcpau_sabado_almuerzo`, `gcpau_domingo`, `gcpau_domingo_break_1`, `gcpau_domingo_break_2`, `gcpau_domingo_almuerzo`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `gcpau_lunes`=?, `gcpau_lunes_break_1`=?, `gcpau_lunes_break_2`=?, `gcpau_lunes_almuerzo`=?, `gcpau_martes`=?, `gcpau_martes_break_1`=?, `gcpau_martes_break_2`=?, `gcpau_martes_almuerzo`=?, `gcpau_miercoles`=?, `gcpau_miercoles_break_1`=?, `gcpau_miercoles_break_2`=?, `gcpau_miercoles_almuerzo`=?, `gcpau_jueves`=?, `gcpau_jueves_break_1`=?, `gcpau_jueves_break_2`=?, `gcpau_jueves_almuerzo`=?, `gcpau_viernes`=?, `gcpau_viernes_break_1`=?, `gcpau_viernes_break_2`=?, `gcpau_viernes_almuerzo`=?, `gcpau_sabado`=?, `gcpau_sabado_break_1`=?, `gcpau_sabado_break_2`=?, `gcpau_sabado_almuerzo`=?, `gcpau_domingo`=?, `gcpau_domingo_break_1`=?, `gcpau_domingo_break_2`=?, `gcpau_domingo_almuerzo`=?");

    // Agrega variables a sentencia preparada
    $sentencia_insert->bind_param('sssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', $gcpau_id, $gcpau_punto_atencion, $gcpau_usuario, $gcpau_lunes, $gcpau_lunes_break_1, $gcpau_lunes_break_2, $gcpau_lunes_almuerzo, $gcpau_martes, $gcpau_martes_break_1, $gcpau_martes_break_2, $gcpau_martes_almuerzo, $gcpau_miercoles, $gcpau_miercoles_break_1, $gcpau_miercoles_break_2, $gcpau_miercoles_almuerzo, $gcpau_jueves, $gcpau_jueves_break_1, $gcpau_jueves_break_2, $gcpau_jueves_almuerzo, $gcpau_viernes, $gcpau_viernes_break_1, $gcpau_viernes_break_2, $gcpau_viernes_almuerzo, $gcpau_sabado, $gcpau_sabado_break_1, $gcpau_sabado_break_2, $gcpau_sabado_almuerzo, $gcpau_domingo, $gcpau_domingo_break_1, $gcpau_domingo_break_2, $gcpau_domingo_almuerzo, $gcpau_lunes, $gcpau_lunes_break_1, $gcpau_lunes_break_2, $gcpau_lunes_almuerzo, $gcpau_martes, $gcpau_martes_break_1, $gcpau_martes_break_2, $gcpau_martes_almuerzo, $gcpau_miercoles, $gcpau_miercoles_break_1, $gcpau_miercoles_break_2, $gcpau_miercoles_almuerzo, $gcpau_jueves, $gcpau_jueves_break_1, $gcpau_jueves_break_2, $gcpau_jueves_almuerzo, $gcpau_viernes, $gcpau_viernes_break_1, $gcpau_viernes_break_2, $gcpau_viernes_almuerzo, $gcpau_sabado, $gcpau_sabado_break_1, $gcpau_sabado_break_2, $gcpau_sabado_almuerzo, $gcpau_domingo, $gcpau_domingo_break_1, $gcpau_domingo_break_2, $gcpau_domingo_almuerzo);
    
    for ($i=0; $i < count($resultado_registros_usuario); $i++) { 
      $gcpau_id=$id_registro.'-'.$id_registro;
      $gcpau_punto_atencion=$id_registro;
      $gcpau_usuario=$id_registro;
      $gcpau_lunes=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][1][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][1][1];
      $gcpau_lunes_break_1=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][1][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][1][1];
      $gcpau_lunes_break_2=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][1][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][1][1];
      $gcpau_lunes_almuerzo=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][1][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][1][1];
      $gcpau_martes=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][2][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][2][1];
      $gcpau_martes_break_1=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][2][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][2][1];
      $gcpau_martes_break_2=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][2][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][2][1];
      $gcpau_martes_almuerzo=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][2][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][2][1];
      $gcpau_miercoles=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][3][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][3][1];
      $gcpau_miercoles_break_1=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][3][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][3][1];
      $gcpau_miercoles_break_2=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][3][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][3][1];
      $gcpau_miercoles_almuerzo=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][3][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][3][1];
      $gcpau_jueves=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][4][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][4][1];
      $gcpau_jueves_break_1=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][4][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][4][1];
      $gcpau_jueves_break_2=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][4][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][4][1];
      $gcpau_jueves_almuerzo=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][4][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][4][1];
      $gcpau_viernes=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][5][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][5][1];
      $gcpau_viernes_break_1=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][5][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][5][1];
      $gcpau_viernes_break_2=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][5][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][5][1];
      $gcpau_viernes_almuerzo=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][5][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][5][1];
      $gcpau_sabado=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][6][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][6][1];
      $gcpau_sabado_break_1=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][6][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][6][1];
      $gcpau_sabado_break_2=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][6][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][6][1];
      $gcpau_sabado_almuerzo=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][6][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][6][1];
      $gcpau_domingo=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][7][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_atencion'][7][1];
      $gcpau_domingo_break_1=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][7][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_1'][7][1];
      $gcpau_domingo_break_2=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][7][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_break_2'][7][1];
      $gcpau_domingo_almuerzo=$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][7][0].'-'.$usuario_dias[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][7][1];
      
      if ($sentencia_insert->execute()) {
          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
      } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
      }
    }

  }

    $consulta_string="SELECT `gcpa_id`, `gcpa_regional`, `gcpa_municipio`, `gcpa_punto_atencion`, `gcpa_direccion`, `gcpa_estado`, `gcpa_registro_usuario`, `gcpa_registro_fecha`, TC.`ciu_departamento`, TC.`ciu_municipio`, TU.`usu_nombres_apellidos` FROM `gestion_citasfa_punto_atencion` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_citasfa_punto_atencion`.`gcpa_municipio`=TC.`ciu_codigo` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_punto_atencion`.`gcpa_registro_usuario`=TU.`usu_id` WHERE `gcpa_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_ciudad="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_departamento`, `ciu_municipio`";
    $consulta_registros_ciudad = $enlace_db->prepare($consulta_string_ciudad);
    $consulta_registros_ciudad->execute();
    $resultado_registros_ciudad = $consulta_registros_ciudad->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_usuario="SELECT `gcpau_id`, `gcpau_punto_atencion`, `gcpau_usuario`, `gcpau_lunes`, `gcpau_lunes_break_1`, `gcpau_lunes_break_2`, `gcpau_lunes_almuerzo`, `gcpau_martes`, `gcpau_martes_break_1`, `gcpau_martes_break_2`, `gcpau_martes_almuerzo`, `gcpau_miercoles`, `gcpau_miercoles_break_1`, `gcpau_miercoles_break_2`, `gcpau_miercoles_almuerzo`, `gcpau_jueves`, `gcpau_jueves_break_1`, `gcpau_jueves_break_2`, `gcpau_jueves_almuerzo`, `gcpau_viernes`, `gcpau_viernes_break_1`, `gcpau_viernes_break_2`, `gcpau_viernes_almuerzo`, `gcpau_sabado`, `gcpau_sabado_break_1`, `gcpau_sabado_break_2`, `gcpau_sabado_almuerzo`, `gcpau_domingo`, `gcpau_domingo_break_1`, `gcpau_domingo_break_2`, `gcpau_domingo_almuerzo`, TU.`usu_nombres_apellidos` FROM `gestion_citasfa_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` WHERE `gcpau_punto_atencion`=?";

    $consulta_registros_usuario = $enlace_db->prepare($consulta_string_usuario);
    $consulta_registros_usuario->bind_param("s", $id_registro);
    $consulta_registros_usuario->execute();
    $resultado_registros_usuario = $consulta_registros_usuario->get_result()->fetch_all(MYSQLI_NUM);
    for ($i=0; $i < count($resultado_registros_usuario); $i++) { 
      $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias']=array();
    }
    for ($i=0; $i < count($resultado_registros_usuario); $i++) { 
      $lun_horario=explode('-', $resultado_registros_usuario[$i][3]);
      if ($lun_horario[0]!='' AND $lun_horario[1]!='') {
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias'][]=1;
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][1]=explode('-', $resultado_registros_usuario[$i][3]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_1'][1]=explode('-', $resultado_registros_usuario[$i][4]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][1]=explode('-', $resultado_registros_usuario[$i][6]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_2'][1]=explode('-', $resultado_registros_usuario[$i][5]);
      }

      $mar_horario=explode('-', $resultado_registros_usuario[$i][7]);
      if ($mar_horario[0]!='' AND $mar_horario[1]!='') {
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias'][]=2;
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][2]=explode('-', $resultado_registros_usuario[$i][7]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_1'][2]=explode('-', $resultado_registros_usuario[$i][8]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][2]=explode('-', $resultado_registros_usuario[$i][10]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_2'][2]=explode('-', $resultado_registros_usuario[$i][9]);
      }

      $mie_horario=explode('-', $resultado_registros_usuario[$i][11]);
      if ($mie_horario[0]!='' AND $mie_horario[1]!='') {
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias'][]=3;
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][3]=explode('-', $resultado_registros_usuario[$i][11]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_1'][3]=explode('-', $resultado_registros_usuario[$i][12]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][3]=explode('-', $resultado_registros_usuario[$i][14]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_2'][3]=explode('-', $resultado_registros_usuario[$i][13]);
      }

      $jue_horario=explode('-', $resultado_registros_usuario[$i][15]);
      if ($jue_horario[0]!='' AND $jue_horario[1]!='') {
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias'][]=4;
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][4]=explode('-', $resultado_registros_usuario[$i][15]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_1'][4]=explode('-', $resultado_registros_usuario[$i][16]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][4]=explode('-', $resultado_registros_usuario[$i][18]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_2'][4]=explode('-', $resultado_registros_usuario[$i][17]);
      }

      $vie_horario=explode('-', $resultado_registros_usuario[$i][19]);
      if ($vie_horario[0]!='' AND $vie_horario[1]!='') {
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias'][]=5;
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][5]=explode('-', $resultado_registros_usuario[$i][19]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_1'][5]=explode('-', $resultado_registros_usuario[$i][20]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][5]=explode('-', $resultado_registros_usuario[$i][22]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_2'][5]=explode('-', $resultado_registros_usuario[$i][21]);
      }

      $sab_horario=explode('-', $resultado_registros_usuario[$i][23]);
      if ($sab_horario[0]!='' AND $sab_horario[1]!='') {
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias'][]=6;
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][6]=explode('-', $resultado_registros_usuario[$i][23]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_1'][6]=explode('-', $resultado_registros_usuario[$i][24]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][6]=explode('-', $resultado_registros_usuario[$i][26]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_2'][6]=explode('-', $resultado_registros_usuario[$i][25]);
      }

      $dom_horario=explode('-', $resultado_registros_usuario[$i][27]);
      if ($dom_horario[0]!='' AND $dom_horario[1]!='') {
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias'][]=7;
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][7]=explode('-', $resultado_registros_usuario[$i][27]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_1'][7]=explode('-', $resultado_registros_usuario[$i][28]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][7]=explode('-', $resultado_registros_usuario[$i][30]);
        $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_2'][7]=explode('-', $resultado_registros_usuario[$i][29]);
      }
    }
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <!-- navbar -->
    <?php require_once(ROOT.'includes/_navbar.php'); ?>
    <!-- navbar -->
    <div class="container-fluid page-body-wrapper">
      <!-- sidebar -->
      <?php require_once(ROOT.'includes/_sidebar.php'); ?>
      <!-- sidebar -->
      <!-- main-panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
          <div class="row justify-content-center">
            <?php if (!empty($respuesta_accion)) {echo "<script type='text/javascript'>".$respuesta_accion."</script>";} ?>
            <div class="col-lg-3 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                            <div class="form-group my-1">
                                <label for="estado" class="my-0">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" disabled>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][5]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][5]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                                <label for="regional" class="my-0">Regional</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="regional" id="regional" disabled>
                                  <option value="">Seleccione</option>
                                  <option value="REGIONAL AMAZONAS" <?php if($resultado_registros[0][1]=="REGIONAL AMAZONAS"){ echo "selected"; } ?>>REGIONAL AMAZONAS</option>
                                  <option value="REGIONAL ANTIOQUIA" <?php if($resultado_registros[0][1]=="REGIONAL ANTIOQUIA"){ echo "selected"; } ?>>REGIONAL ANTIOQUIA</option>
                                  <option value="REGIONAL ARAUCA" <?php if($resultado_registros[0][1]=="REGIONAL ARAUCA"){ echo "selected"; } ?>>REGIONAL ARAUCA</option>
                                  <option value="REGIONAL ATLÁNTICO" <?php if($resultado_registros[0][1]=="REGIONAL ATLÁNTICO"){ echo "selected"; } ?>>REGIONAL ATLÁNTICO</option>
                                  <option value="REGIONAL BOGOTÁ" <?php if($resultado_registros[0][1]=="REGIONAL BOGOTÁ"){ echo "selected"; } ?>>REGIONAL BOGOTÁ</option>
                                  <option value="REGIONAL BOLÍVAR" <?php if($resultado_registros[0][1]=="REGIONAL BOLÍVAR"){ echo "selected"; } ?>>REGIONAL BOLÍVAR</option>
                                  <option value="REGIONAL BOYACÁ" <?php if($resultado_registros[0][1]=="REGIONAL BOYACÁ"){ echo "selected"; } ?>>REGIONAL BOYACÁ</option>
                                  <option value="REGIONAL CALDAS" <?php if($resultado_registros[0][1]=="REGIONAL CALDAS"){ echo "selected"; } ?>>REGIONAL CALDAS</option>
                                  <option value="REGIONAL CAQUETÁ" <?php if($resultado_registros[0][1]=="REGIONAL CAQUETÁ"){ echo "selected"; } ?>>REGIONAL CAQUETÁ</option>
                                  <option value="REGIONAL CASANARE" <?php if($resultado_registros[0][1]=="REGIONAL CASANARE"){ echo "selected"; } ?>>REGIONAL CASANARE</option>
                                  <option value="REGIONAL CAUCA" <?php if($resultado_registros[0][1]=="REGIONAL CAUCA"){ echo "selected"; } ?>>REGIONAL CAUCA</option>
                                  <option value="REGIONAL CESÁR" <?php if($resultado_registros[0][1]=="REGIONAL CESÁR"){ echo "selected"; } ?>>REGIONAL CESÁR</option>
                                  <option value="REGIONAL CHOCÓ" <?php if($resultado_registros[0][1]=="REGIONAL CHOCÓ"){ echo "selected"; } ?>>REGIONAL CHOCÓ</option>
                                  <option value="REGIONAL CÓRDOBA" <?php if($resultado_registros[0][1]=="REGIONAL CÓRDOBA"){ echo "selected"; } ?>>REGIONAL CÓRDOBA</option>
                                  <option value="REGIONAL CUNDINAMARCA" <?php if($resultado_registros[0][1]=="REGIONAL CUNDINAMARCA"){ echo "selected"; } ?>>REGIONAL CUNDINAMARCA</option>
                                  <option value="REGIONAL GUAINÍA" <?php if($resultado_registros[0][1]=="REGIONAL GUAINÍA"){ echo "selected"; } ?>>REGIONAL GUAINÍA</option>
                                  <option value="REGIONAL GUAVIARE" <?php if($resultado_registros[0][1]=="REGIONAL GUAVIARE"){ echo "selected"; } ?>>REGIONAL GUAVIARE</option>
                                  <option value="REGIONAL HUILA" <?php if($resultado_registros[0][1]=="REGIONAL HUILA"){ echo "selected"; } ?>>REGIONAL HUILA</option>
                                  <option value="REGIONAL LA GUAJIRA" <?php if($resultado_registros[0][1]=="REGIONAL LA GUAJIRA"){ echo "selected"; } ?>>REGIONAL LA GUAJIRA</option>
                                  <option value="REGIONAL MAGDALENA" <?php if($resultado_registros[0][1]=="REGIONAL MAGDALENA"){ echo "selected"; } ?>>REGIONAL MAGDALENA</option>
                                  <option value="REGIONAL MAGDALENA MEDIO" <?php if($resultado_registros[0][1]=="REGIONAL MAGDALENA MEDIO"){ echo "selected"; } ?>>REGIONAL MAGDALENA MEDIO</option>
                                  <option value="REGIONAL META" <?php if($resultado_registros[0][1]=="REGIONAL META"){ echo "selected"; } ?>>REGIONAL META</option>
                                  <option value="REGIONAL NARIÑO" <?php if($resultado_registros[0][1]=="REGIONAL NARIÑO"){ echo "selected"; } ?>>REGIONAL NARIÑO</option>
                                  <option value="REGIONAL NORTE DE SANTANDER" <?php if($resultado_registros[0][1]=="REGIONAL NORTE DE SANTANDER"){ echo "selected"; } ?>>REGIONAL NORTE DE SANTANDER</option>
                                  <option value="REGIONAL PUTUMAYO" <?php if($resultado_registros[0][1]=="REGIONAL PUTUMAYO"){ echo "selected"; } ?>>REGIONAL PUTUMAYO</option>
                                  <option value="REGIONAL QUINDÍO" <?php if($resultado_registros[0][1]=="REGIONAL QUINDÍO"){ echo "selected"; } ?>>REGIONAL QUINDÍO</option>
                                  <option value="REGIONAL RISARALDA" <?php if($resultado_registros[0][1]=="REGIONAL RISARALDA"){ echo "selected"; } ?>>REGIONAL RISARALDA</option>
                                  <option value="REGIONAL SAN ANDRÉS" <?php if($resultado_registros[0][1]=="REGIONAL SAN ANDRÉS"){ echo "selected"; } ?>>REGIONAL SAN ANDRÉS</option>
                                  <option value="REGIONAL SANTANDER" <?php if($resultado_registros[0][1]=="REGIONAL SANTANDER"){ echo "selected"; } ?>>REGIONAL SANTANDER</option>
                                  <option value="REGIONAL SUCRE" <?php if($resultado_registros[0][1]=="REGIONAL SUCRE"){ echo "selected"; } ?>>REGIONAL SUCRE</option>
                                  <option value="REGIONAL TOLIMA" <?php if($resultado_registros[0][1]=="REGIONAL TOLIMA"){ echo "selected"; } ?>>REGIONAL TOLIMA</option>
                                  <option value="REGIONAL URABÁ" <?php if($resultado_registros[0][1]=="REGIONAL URABÁ"){ echo "selected"; } ?>>REGIONAL URABÁ</option>
                                  <option value="REGIONAL VALLE" <?php if($resultado_registros[0][1]=="REGIONAL VALLE"){ echo "selected"; } ?>>REGIONAL VALLE</option>
                                  <option value="REGIONAL VAUPÉS" <?php if($resultado_registros[0][1]=="REGIONAL VAUPÉS"){ echo "selected"; } ?>>REGIONAL VAUPÉS</option>
                                  <option value="REGIONAL VICHADA" <?php if($resultado_registros[0][1]=="REGIONAL VICHADA"){ echo "selected"; } ?>>REGIONAL VICHADA</option>
                                  <option value="RED CADE" <?php if($resultado_registros[0][1]=="RED CADE"){ echo "selected"; } ?>>RED CADE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                                <label for="ciudad" class="my-0">Municipio</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="ciudad" id="ciudad" disabled>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_ciudad); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_ciudad[$i][0]; ?>" <?php if($resultado_registros[0][2]==$resultado_registros_ciudad[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_ciudad[$i][2].", ".$resultado_registros_ciudad[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="punto_atencion" class="my-0">Punto de atención</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="punto_atencion" id="punto_atencion" maxlength="100" value="<?php echo $resultado_registros[0][3]; ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="direccion" class="my-0">Dirección</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="direccion" id="direccion" maxlength="100" value="<?php echo $resultado_registros[0][4]; ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="direccion" class="my-0">Usuarios</label>
                              <?php for ($i=0; $i < count($resultado_registros_usuario); $i++): ?>
                              <input type="text" class="form-control form-control-sm font-size-11" name="direccion" id="direccion" maxlength="100" value="<?php echo $resultado_registros_usuario[$i][31]; ?>" disabled>
                              <?php endfor; ?>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-9 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                            <div class="col-md-12 mb-3">
                                <hr class="my-1">
                                <label for="usuario_<?php echo $resultado_registros_usuario[$i][2]; ?>" class="form-label my-0 fw-bold"><?php echo $resultado_registros_usuario[$i][31]; ?></label>
                                <hr class="my-1">
                                <div class="row">
                                  <?php for ($j=1; $j <= count($array_dias_nombre); $j++): ?>
                                    <div class="col-md-3">
                                        <div class="form-check form-switch px-5">
                                            <input class="form-check-input" type="checkbox" name="usuario_<?php echo $resultado_registros_usuario[$i][2]; ?>_dia[]" id="usuario_<?php echo $resultado_registros_usuario[$i][2]; ?>_dia_<?php echo $j; ?>" value="<?php echo $j; ?>" <?php echo (in_array($j, $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias'])) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="usuario_<?php echo $resultado_registros_usuario[$i][2]; ?>_dia_<?php echo $j; ?>"><?php echo $array_dias_nombre[$j]; ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                      <div class="form-group">
                                        <label for="horario_atencion" class="my-0">Atención</label>
                                        <input type="time" class="form-control form-control-sm font-size-11" name="horario_atencion_<?php echo $resultado_registros_usuario[$i][2]; ?>_<?php echo $j; ?>[]" id="horario_atencion" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][$j][0]; ?>">
                                        <input type="time" class="form-control form-control-sm font-size-11" name="horario_atencion_<?php echo $resultado_registros_usuario[$i][2]; ?>_<?php echo $j; ?>[]" id="horario_atencion" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][$j][1]; ?>">
                                      </div>
                                    </div>
                                    <div class="col-md-2">
                                      <div class="form-group">
                                        <label for="horario_break_1" class="my-0">Break 1</label>
                                        <input type="time" class="form-control form-control-sm font-size-11" name="horario_break_1_<?php echo $resultado_registros_usuario[$i][2]; ?>_<?php echo $j; ?>[]" id="horario_break_1" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_1'][$j][0]; ?>">
                                        <input type="time" class="form-control form-control-sm font-size-11" name="horario_break_1_<?php echo $resultado_registros_usuario[$i][2]; ?>_<?php echo $j; ?>[]" id="horario_break_1" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_1'][$j][1]; ?>">
                                      </div>
                                    </div>
                                    <div class="col-md-2">
                                      <div class="form-group">
                                        <label for="horario_almuerzo" class="my-0">Almuerzo</label>
                                        <input type="time" class="form-control form-control-sm font-size-11" name="horario_almuerzo_<?php echo $resultado_registros_usuario[$i][2]; ?>_<?php echo $j; ?>[]" id="horario_almuerzo" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][$j][0]; ?>">
                                        <input type="time" class="form-control form-control-sm font-size-11" name="horario_almuerzo_<?php echo $resultado_registros_usuario[$i][2]; ?>_<?php echo $j; ?>[]" id="horario_almuerzo" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_almuerzo'][$j][1]; ?>">
                                      </div>
                                    </div>
                                    <div class="col-md-2">
                                      <div class="form-group">
                                        <label for="horario_break_2" class="my-0">Break 2</label>
                                        <input type="time" class="form-control form-control-sm font-size-11" name="horario_break_2_<?php echo $resultado_registros_usuario[$i][2]; ?>_<?php echo $j; ?>[]" id="horario_break_2" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_2'][$j][0]; ?>">
                                        <input type="time" class="form-control form-control-sm font-size-11" name="horario_break_2_<?php echo $resultado_registros_usuario[$i][2]; ?>_<?php echo $j; ?>[]" id="horario_break_2" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_break_2'][$j][1]; ?>">
                                      </div>
                                    </div>
                                  <?php endfor; ?>
                                </div>
                            </div>
                        <?php for ($i=0; $i < count($resultado_registros_usuario); $i++): ?>
                        <?php endfor; ?>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Guardar</button>
                                <?php if(isset($_POST["guardar_registro"])): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php endif; ?>
                                <?php if(!isset($_POST["guardar_registro"])): ?>
                                    <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');">Cancelar</button>
                                <?php endif; ?>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>