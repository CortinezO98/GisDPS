<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Interacciones-Configuración";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Interacciones";
  $subtitle = "Configuración | Tipificación 3 | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="configuracion_n3?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
    $gic3_item=validar_input($_POST['gic3_item']);
    $gic3_estado=validar_input($_POST['gic3_estado']);
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_interacciones_catnivel3` SET `gic3_item`=?, `gic3_estado`=? WHERE `gic3_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sss', $gic3_item, $gic3_estado, $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
    
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

  $consulta_string="SELECT `gic3_id`, `gic3_padre`, `gic3_item`, `gic3_estado`, `gic3_registro_usuario`, `gic3_registro_fecha`, TP.`gic2_item`, TP.`gic2_estado`, TP1.`gic1_estado` FROM `gestion_interacciones_catnivel3` LEFT JOIN `gestion_interacciones_catnivel2` AS TP ON `gestion_interacciones_catnivel3`.`gic3_padre`=TP.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TP1 ON TP.`gic2_padre`=TP1.`gic1_id` WHERE `gic3_id`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-lg-6 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gic3_estado">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="gic3_estado" id="gic3_estado" required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][3]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][3]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="gic3_padre" class="my-0">Tipificación 2</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="gic3_padre" id="gic3_padre" maxlength="100" value="<?php echo $resultado_registros[0][6]; ?>" required disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="gic3_item" class="my-0">Tipificación 3</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="gic3_item" id="gic3_item" maxlength="100" value="<?php echo $resultado_registros[0][2]; ?>" required>
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
</body>
</html>