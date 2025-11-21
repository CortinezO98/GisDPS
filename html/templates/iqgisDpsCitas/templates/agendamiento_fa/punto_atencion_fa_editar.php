<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas FA-Punto Atención";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas FA";
  $subtitle = "Puntos de Atención | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="punto_atencion_fa?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
    $estado=validar_input($_POST['estado']);
    $regional=validar_input($_POST['regional']);
    $ciudad=validar_input($_POST['ciudad']);
    $punto_atencion=validar_input($_POST['punto_atencion']);
    $direccion=validar_input($_POST['direccion']);
    $usuarios=$_POST['usuarios'];

    $horario_dias['dias']=$_POST["horario_dia"];
    for ($j=1; $j <= count($array_dias_nombre); $j++) { 
      $horario_dias['horario_atencion'][$j]=$_POST["horario_atencion_".$j];
    }

    if ($horario_dias['horario_atencion'][1][0]!="" AND $horario_dias['horario_atencion'][1][1]!="") {
      $gcpa_lunes=$horario_dias['horario_atencion'][1][0].'-'.$horario_dias['horario_atencion'][1][1];
    } else {
      $gcpa_lunes="";
    }

    if ($horario_dias['horario_atencion'][2][0]!="" AND $horario_dias['horario_atencion'][2][1]!="") {
      $gcpa_martes=$horario_dias['horario_atencion'][2][0].'-'.$horario_dias['horario_atencion'][2][1];
    } else {
      $gcpa_martes="";
    }

    if ($horario_dias['horario_atencion'][3][0]!="" AND $horario_dias['horario_atencion'][3][1]!="") {
      $gcpa_miercoles=$horario_dias['horario_atencion'][3][0].'-'.$horario_dias['horario_atencion'][3][1];
    } else {
      $gcpa_miercoles="";
    }

    if ($horario_dias['horario_atencion'][4][0]!="" AND $horario_dias['horario_atencion'][4][1]!="") {
      $gcpa_jueves=$horario_dias['horario_atencion'][4][0].'-'.$horario_dias['horario_atencion'][4][1];
    } else {
      $gcpa_jueves="";
    }

    if ($horario_dias['horario_atencion'][5][0]!="" AND $horario_dias['horario_atencion'][5][1]!="") {
      $gcpa_viernes=$horario_dias['horario_atencion'][5][0].'-'.$horario_dias['horario_atencion'][5][1];
    } else {
      $gcpa_viernes="";
    }

    if ($horario_dias['horario_atencion'][6][0]!="" AND $horario_dias['horario_atencion'][6][1]!="") {
      $gcpa_sabado=$horario_dias['horario_atencion'][6][0].'-'.$horario_dias['horario_atencion'][6][1];
    } else {
      $gcpa_sabado="";
    }

    if ($horario_dias['horario_atencion'][7][0]!="" AND $horario_dias['horario_atencion'][7][1]!="") {
      $gcpa_domingo=$horario_dias['horario_atencion'][7][0].'-'.$horario_dias['horario_atencion'][7][1];
    } else {
      $gcpa_domingo="";
    }
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_citasfa_punto_atencion` SET `gcpa_regional`=?,`gcpa_municipio`=?,`gcpa_punto_atencion`=?,`gcpa_direccion`=?,`gcpa_estado`=?,`gcpa_lunes`=?,`gcpa_martes`=?,`gcpa_miercoles`=?,`gcpa_jueves`=?,`gcpa_viernes`=?,`gcpa_sabado`=?,`gcpa_domingo`=? WHERE `gcpa_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sssssssssssss', $regional, $ciudad, $punto_atencion, $direccion, $estado, $gcpa_lunes, $gcpa_martes, $gcpa_miercoles, $gcpa_jueves, $gcpa_viernes, $gcpa_sabado, $gcpa_domingo, $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
    
    if (comprobarSentencia($enlace_db->info)) {
        $consulta_string_usuario_punto="SELECT `gcpau_id`, `gcpau_punto_atencion`, `gcpau_usuario`, `gcpau_lunes`, `gcpau_lunes_break_1`, `gcpau_lunes_break_2`, `gcpau_lunes_almuerzo`, `gcpau_martes`, `gcpau_martes_break_1`, `gcpau_martes_break_2`, `gcpau_martes_almuerzo`, `gcpau_miercoles`, `gcpau_miercoles_break_1`, `gcpau_miercoles_break_2`, `gcpau_miercoles_almuerzo`, `gcpau_jueves`, `gcpau_jueves_break_1`, `gcpau_jueves_break_2`, `gcpau_jueves_almuerzo`, `gcpau_viernes`, `gcpau_viernes_break_1`, `gcpau_viernes_break_2`, `gcpau_viernes_almuerzo`, `gcpau_sabado`, `gcpau_sabado_break_1`, `gcpau_sabado_break_2`, `gcpau_sabado_almuerzo`, `gcpau_domingo`, `gcpau_domingo_break_1`, `gcpau_domingo_break_2`, `gcpau_domingo_almuerzo`, TU.`usu_nombres_apellidos` FROM `gestion_citasfa_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` WHERE `gcpau_punto_atencion`=?";

        $consulta_registros_usuario_punto = $enlace_db->prepare($consulta_string_usuario_punto);
        $consulta_registros_usuario_punto->bind_param("s", $id_registro);
        $consulta_registros_usuario_punto->execute();
        $resultado_registros_usuario_punto = $consulta_registros_usuario_punto->get_result()->fetch_all(MYSQLI_NUM);
        
        // Prepara la sentencia
        $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_citasfa_punto_atencion_usuario` WHERE `gcpau_punto_atencion`=? AND `gcpau_usuario`=?");

        // Agrega variables a sentencia preparada
        $sentencia_delete->bind_param('ss', $id_registro, $gcpau_usuario);

        for ($i=0; $i < count($resultado_registros_usuario_punto); $i++) { 
          $gcpau_usuario=$resultado_registros_usuario_punto[$i][2];
          if (!in_array($gcpau_usuario, $usuarios)) {
            $sentencia_delete->execute();
          }
        }

        // Prepara la sentencia
        $sentencia_insert_usuarios = $enlace_db->prepare("INSERT INTO `gestion_citasfa_punto_atencion_usuario`(`gcpau_id`, `gcpau_punto_atencion`, `gcpau_usuario`, `gcpau_lunes`, `gcpau_lunes_break_1`, `gcpau_lunes_break_2`, `gcpau_lunes_almuerzo`, `gcpau_martes`, `gcpau_martes_break_1`, `gcpau_martes_break_2`, `gcpau_martes_almuerzo`, `gcpau_miercoles`, `gcpau_miercoles_break_1`, `gcpau_miercoles_break_2`, `gcpau_miercoles_almuerzo`, `gcpau_jueves`, `gcpau_jueves_break_1`, `gcpau_jueves_break_2`, `gcpau_jueves_almuerzo`, `gcpau_viernes`, `gcpau_viernes_break_1`, `gcpau_viernes_break_2`, `gcpau_viernes_almuerzo`, `gcpau_sabado`, `gcpau_sabado_break_1`, `gcpau_sabado_break_2`, `gcpau_sabado_almuerzo`, `gcpau_domingo`, `gcpau_domingo_break_1`, `gcpau_domingo_break_2`, `gcpau_domingo_almuerzo`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `gcpau_punto_atencion`=?, `gcpau_usuario`=?");

        // Agrega variables a sentencia preparada
        $sentencia_insert_usuarios->bind_param('sssssssssssssssssssssssssssssssss', $gcpau_id, $gcpau_punto_atencion, $gcpau_usuario, $gcpau_lunes, $gcpau_lunes_break_1, $gcpau_lunes_break_2, $gcpau_lunes_almuerzo, $gcpau_martes, $gcpau_martes_break_1, $gcpau_martes_break_2, $gcpau_martes_almuerzo, $gcpau_miercoles, $gcpau_miercoles_break_1, $gcpau_miercoles_break_2, $gcpau_miercoles_almuerzo, $gcpau_jueves, $gcpau_jueves_break_1, $gcpau_jueves_break_2, $gcpau_jueves_almuerzo, $gcpau_viernes, $gcpau_viernes_break_1, $gcpau_viernes_break_2, $gcpau_viernes_almuerzo, $gcpau_sabado, $gcpau_sabado_break_1, $gcpau_sabado_break_2, $gcpau_sabado_almuerzo, $gcpau_domingo, $gcpau_domingo_break_1, $gcpau_domingo_break_2, $gcpau_domingo_almuerzo, $gcpau_punto_atencion, $gcpau_usuario);
        
        $control_insert=0;
        for ($i=0; $i < count($usuarios); $i++) { 
          $gcpau_id=$usuarios[$i].'-'.$id_registro;
          $gcpau_punto_atencion=$id_registro;
          $gcpau_usuario=$usuarios[$i];
          $gcpau_lunes='';
          $gcpau_lunes_break_1='';
          $gcpau_lunes_break_2='';
          $gcpau_lunes_almuerzo='';
          $gcpau_martes='';
          $gcpau_martes_break_1='';
          $gcpau_martes_break_2='';
          $gcpau_martes_almuerzo='';
          $gcpau_miercoles='';
          $gcpau_miercoles_break_1='';
          $gcpau_miercoles_break_2='';
          $gcpau_miercoles_almuerzo='';
          $gcpau_jueves='';
          $gcpau_jueves_break_1='';
          $gcpau_jueves_break_2='';
          $gcpau_jueves_almuerzo='';
          $gcpau_viernes='';
          $gcpau_viernes_break_1='';
          $gcpau_viernes_break_2='';
          $gcpau_viernes_almuerzo='';
          $gcpau_sabado='';
          $gcpau_sabado_break_1='';
          $gcpau_sabado_break_2='';
          $gcpau_sabado_almuerzo='';
          $gcpau_domingo='';
          $gcpau_domingo_break_1='';
          $gcpau_domingo_break_2='';
          $gcpau_domingo_almuerzo='';
          
          if ($sentencia_insert_usuarios->execute()) {
              $control_insert++;
          }
        }

        if ($control_insert==count($usuarios)) {
          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
        } else {
          $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
        }
    } else {
      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

    $consulta_string="SELECT `gcpa_id`, `gcpa_regional`, `gcpa_municipio`, `gcpa_punto_atencion`, `gcpa_direccion`, `gcpa_estado`, `gcpa_registro_usuario`, `gcpa_registro_fecha`, TC.`ciu_departamento`, TC.`ciu_municipio`, TU.`usu_nombres_apellidos`, `gcpa_lunes`, `gcpa_martes`, `gcpa_miercoles`, `gcpa_jueves`, `gcpa_viernes`, `gcpa_sabado`, `gcpa_domingo` FROM `gestion_citasfa_punto_atencion` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_citasfa_punto_atencion`.`gcpa_municipio`=TC.`ciu_codigo` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_punto_atencion`.`gcpa_registro_usuario`=TU.`usu_id` WHERE `gcpa_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
    unset($array_horario);
    $array_horario[]=$resultado_registros[0][11];
    $array_horario[]=$resultado_registros[0][12];
    $array_horario[]=$resultado_registros[0][13];
    $array_horario[]=$resultado_registros[0][14];
    $array_horario[]=$resultado_registros[0][15];
    $array_horario[]=$resultado_registros[0][16];
    $array_horario[]=$resultado_registros[0][17];

    $consulta_string_ciudad="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_departamento`, `ciu_municipio`";
    $consulta_registros_ciudad = $enlace_db->prepare($consulta_string_ciudad);
    $consulta_registros_ciudad->execute();
    $resultado_registros_ciudad = $consulta_registros_ciudad->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_usuarios="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' ORDER BY `usu_nombres_apellidos` ASC";
    $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
    $consulta_registros_usuarios->execute();
    $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_usuario_punto="SELECT `gcpau_id`, `gcpau_punto_atencion`, `gcpau_usuario`, `gcpau_lunes`, `gcpau_lunes_break_1`, `gcpau_lunes_break_2`, `gcpau_lunes_almuerzo`, `gcpau_martes`, `gcpau_martes_break_1`, `gcpau_martes_break_2`, `gcpau_martes_almuerzo`, `gcpau_miercoles`, `gcpau_miercoles_break_1`, `gcpau_miercoles_break_2`, `gcpau_miercoles_almuerzo`, `gcpau_jueves`, `gcpau_jueves_break_1`, `gcpau_jueves_break_2`, `gcpau_jueves_almuerzo`, `gcpau_viernes`, `gcpau_viernes_break_1`, `gcpau_viernes_break_2`, `gcpau_viernes_almuerzo`, `gcpau_sabado`, `gcpau_sabado_break_1`, `gcpau_sabado_break_2`, `gcpau_sabado_almuerzo`, `gcpau_domingo`, `gcpau_domingo_break_1`, `gcpau_domingo_break_2`, `gcpau_domingo_almuerzo`, TU.`usu_nombres_apellidos` FROM `gestion_citasfa_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` WHERE `gcpau_punto_atencion`=?";

    $consulta_registros_usuario_punto = $enlace_db->prepare($consulta_string_usuario_punto);
    $consulta_registros_usuario_punto->bind_param("s", $id_registro);
    $consulta_registros_usuario_punto->execute();
    $resultado_registros_usuario_punto = $consulta_registros_usuario_punto->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-lg-8 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                                <div class="form-group my-1">
                                    <label for="estado" class="my-0">Estado</label>
                                    <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado"  required>
                                      <option value="">Seleccione</option>
                                      <option value="Activo" <?php if($resultado_registros[0][5]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                      <option value="Inactivo" <?php if($resultado_registros[0][5]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-1">
                                    <label for="regional" class="my-0">Regional</label>
                                    <select class="form-control form-control-sm form-select font-size-11" name="regional" id="regional"  required>
                                      <option value="">Seleccione</option>
                                      <option value="REGIONAL AMAZONAS" <?php if($resultado_registros[0][1]=="REGIONAL AMAZONAS"){ echo "selected"; } ?>>REGIONAL AMAZONAS</option>
                                      <option value="REGIONAL ANTIOQUIA" <?php if($resultado_registros[0][1]=="REGIONAL ANTIOQUIA"){ echo "selected"; } ?>>REGIONAL ANTIOQUIA</option>
                                      <option value="REGIONAL ARAUCA" <?php if($resultado_registros[0][1]=="REGIONAL ARAUCA"){ echo "selected"; } ?>>REGIONAL ARAUCA</option>
                                      <option value="REGIONAL ATLÁNTICO" <?php if($resultado_registros[0][1]=="REGIONAL ATLÁNTICO"){ echo "selected"; } ?>>REGIONAL ATLÁNTICO</option>
                                      <option value="REGIONAL BOGOTÁ" <?php if($resultado_registros[0][1]=="REGIONAL BOGOTÁ"){ echo "selected"; } ?>>REGIONAL BOGOTÁ</option>
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
                                    <select class="form-control form-control-sm form-select font-size-11" name="ciudad" id="ciudad"  required>
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
                                  <input type="text" class="form-control form-control-sm font-size-11" name="punto_atencion" id="punto_atencion" maxlength="100" value="<?php echo $resultado_registros[0][3]; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group my-1">
                                  <label for="direccion" class="my-0">Dirección</label>
                                  <input type="text" class="form-control form-control-sm font-size-11" name="direccion" id="direccion" maxlength="100" value="<?php echo $resultado_registros[0][4]; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12 mt-1">
                                <div class="form-group my-1">
                                    <div class="row" id="opciones_respuestas_usuario">
                                        <?php if(isset($resultado_registros_usuario_punto)): ?>
                                            <?php for ($i=0; $i < count($resultado_registros_usuario_punto); $i++): ?>
                                                <div class="row lista_usuario px-4 col-md-12">
                                                    <div class="col-md-12">
                                                        <div class="form-group my-1">
                                                            <label for="usuarios" class="my-0">Usuario</label>
                                                            <select class="form-control form-control-sm form-select font-size-11" name="usuarios[]" id="usuarios_<?php echo $i; ?>" required <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1) { echo 'disabled'; } ?>>
                                                                <option class="font-size-11" value="">Seleccione</option>
                                                                <?php for ($j=0; $j < count($resultado_registros_usuarios); $j++): ?>
                                                                    <option value="<?php echo $resultado_registros_usuarios[$j][0]; ?>" <?php if($resultado_registros_usuario_punto[$i][2]==$resultado_registros_usuarios[$j][0]){ echo "selected"; } ?>><?php echo $resultado_registros_usuarios[$j][1]; ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1 ps-3">
                                                        <a href="#" class="btn btn-danger font-size-11 p-0" style="display: block; width: 185px;" id="del_field_usuario" title="Quitar usuario"><span class="fas fa-trash-alt"></span> Quitar usuario</a>
                                                    </div>
                                                </div>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                    </div>
                                    <a href="#" class="btn btn-primary font-size-11 p-0 mt-1" style="display: block; width: 185px;" id="add_field_usuario" title="Añadir usuario"><span class="fas fa-plus"></span> Añadir usuario</a>
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <hr class="my-1">
                          <label for="usuario_1" class="form-label my-0 fw-bold">Horarios</label>
                          <hr class="my-1">
                          <div class="row">
                            <?php for ($j=1; $j <= count($array_dias_nombre); $j++): ?>
                              <div class="col-md-7">
                                  <div class="form-check form-switch px-5">
                                      <input class="form-check-input" type="checkbox" name="horario_dia[]" id="horario_dia_<?php echo $j; ?>" value="<?php echo $j; ?>" <?php echo ($array_horario[$j-1]!='') ? 'checked' : ''; ?>>
                                      <label class="form-check-label" for="horario_dia_<?php echo $j; ?>"><?php echo $array_dias_nombre[$j]; ?></label>
                                  </div>
                              </div>
                              <div class="col-md-5">
                                <div class="form-group">
                                  <label for="horario_atencion" class="my-0">Atención</label>
                                  <input type="time" class="form-control form-control-sm font-size-11" name="horario_atencion_<?php echo $j; ?>[]" id="horario_atencion" value="<?php echo explode('-', $array_horario[$j-1])[0]; ?>">
                                  <input type="time" class="form-control form-control-sm font-size-11" name="horario_atencion_<?php echo $j; ?>[]" id="horario_atencion" value="<?php echo explode('-', $array_horario[$j-1])[1]; ?>">
                                </div>
                              </div>
                            <?php endfor; ?>
                          </div>
                        </div>
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
  <script type="text/javascript">
      var campos_max = 10;

      var x = 0;
      $('#add_field_usuario').click (function(e) {
          e.preventDefault();
          if (x < campos_max) {
              $('#opciones_respuestas_usuario').append('<div class="row lista_usuario px-4 col-md-12">\
                  <div class="col-md-12">\
                      <div class="form-group my-1">\
                          <label for="usuarios" class="my-0">Usuario</label>\
                          <select class="form-control form-control-sm form-select font-size-11" name="usuarios[]" id="usuarios_'+x+'" required onchange="validar_captacion_pediatria('+x+');">\
                              <option class="font-size-11" value="">Seleccione</option>\
                              <?php for ($i=0; $i < count($resultado_registros_usuarios); $i++): ?>
                                  <option value="<?php echo $resultado_registros_usuarios[$i][0]; ?>"><?php echo $resultado_registros_usuarios[$i][1]; ?></option>\
                              <?php endfor; ?>
                          </select>\
                      </div>\
                  </div>\
                  <div class="col-12 mb-1 ps-3">\
                      <a href="#" class="btn btn-danger font-size-11 p-0" style="display: block; width: 185px;" id="del_field_usuario" title="Quitar usuario"><span class="fas fa-trash-alt"></span> Quitar usuario</a>\
                  </div>\
              </div>');
              x++;
          }
      });

      $('#opciones_respuestas_usuario').on("click","#del_field_usuario",function(e) {
          e.preventDefault();
          $(this).parents('div.lista_usuario').remove();
          x--;
      });
  </script>
</body>
</html>