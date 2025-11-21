<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Interacciones-Configuración";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Interacciones";
  $subtitle = "Configuración | Tipificación 3 | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $url_salir="configuracion_n3?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
      $gic3_padre=validar_input($_POST['gic3_padre']);
      $gic3_item=validar_input($_POST['gic3_item']);
      $gic3_estado=validar_input($_POST['gic3_estado']);
      $gic3_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

      if($_SESSION[APP_SESSION.'_registro_creado_interacciones_configuracion']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_interacciones_catnivel3`(`gic3_padre`, `gic3_item`, `gic3_estado`, `gic3_registro_usuario`) VALUES (?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssss', $gic3_padre, $gic3_item, $gic3_estado, $gic3_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente');";
              $_SESSION[APP_SESSION.'_registro_creado_interacciones_configuracion']=1;
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente');";
      }
  }

  $consulta_string="SELECT `gic2_id`, `gic2_padre`, `gic2_item`, `gic2_estado`, `gic2_registro_usuario`, `gic2_registro_fecha`, TP.`gic1_item`, TP.`gic1_estado` FROM `gestion_interacciones_catnivel2` LEFT JOIN `gestion_interacciones_catnivel1` AS TP ON `gestion_interacciones_catnivel2`.`gic2_padre`=TP.`gic1_id` WHERE `gic2_id`>0 AND `gic2_estado`='Activo' AND TP.`gic1_estado`='Activo'";
  $consulta_registros = $enlace_db->prepare($consulta_string);
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
                                <select class="form-control form-control-sm form-select font-size-11" name="gic3_estado" id="gic3_estado" required <?php if($_SESSION[APP_SESSION.'_registro_creado_interacciones_configuracion']==1) { echo 'disabled'; } ?>>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($gic3_estado=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($gic3_estado=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="gic3_padre">Tipificación 2</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="gic3_padre" id="gic3_padre" required <?php if($_SESSION[APP_SESSION.'_registro_creado_interacciones_configuracion']==1) { echo 'disabled'; } ?>>
                                  <option value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                                    <option value="<?php echo $resultado_registros[$i][0]; ?>" <?php if($gic3_padre==$resultado_registros[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros[$i][6].' | '.$resultado_registros[$i][2]; ?></option>
                                  <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="gic3_item" class="my-0">Tipificación 3</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="gic3_item" id="gic3_item" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $gic3_item; } ?>" required <?php if($_SESSION[APP_SESSION.'_registro_creado_interacciones_configuracion']==1) { echo 'disabled'; } ?>>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_interacciones_configuracion']==1): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Guardar</button>
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