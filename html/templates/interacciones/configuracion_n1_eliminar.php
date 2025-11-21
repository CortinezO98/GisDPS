<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Interacciones-Configuración";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Interacciones";
  $subtitle = "Configuración | Tipificación 1 | Eliminar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="configuracion_n1?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["eliminar_registro"])){
      if($_SESSION[APP_SESSION.'_registro_eliminado_interacciones_configuracion']!=1){
          // Prepara la sentencia
          $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_interacciones_catnivel1` WHERE `gic1_id`=?");

          // Agrega variables a sentencia preparada
          $sentencia_delete->bind_param('s', $id_registro);
          
          // Evalua resultado de ejecución sentencia preparada
          if ($sentencia_delete->execute()) {
              $_SESSION[APP_SESSION.'_registro_eliminado_interacciones_configuracion']=1;
              $respuesta_accion = "alertButton('success', 'Registro eliminado', 'Registro eliminado exitosamente', '".$url_salir."');";
          } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al eliminar el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string="SELECT `gic1_id`, `gic1_item`, `gic1_estado`, `gic1_registro_usuario`, `gic1_registro_fecha` FROM `gestion_interacciones_catnivel1` WHERE `gic1_id`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_validar="SELECT COUNT(`gic2_id`) FROM `gestion_interacciones_catnivel2` WHERE `gic2_padre`=?";
  $consulta_registros_validar = $enlace_db->prepare($consulta_string_validar);
  $consulta_registros_validar->bind_param("s", $id_registro);
  $consulta_registros_validar->execute();
  $resultado_registros_validar = $consulta_registros_validar->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_uso="SELECT COUNT(`gi_direcciones_misionales`) FROM `gestion_interacciones_historico` WHERE `gi_direcciones_misionales`=?";
  $consulta_registros_uso = $enlace_db->prepare($consulta_string_uso);
  $consulta_registros_uso->bind_param("s", $id_registro);
  $consulta_registros_uso->execute();
  $resultado_registros_uso = $consulta_registros_uso->get_result()->fetch_all(MYSQLI_NUM);
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
                      <?php if($_SESSION[APP_SESSION.'_registro_eliminado_interacciones_configuracion']==1): ?>
                          <p class="alert alert-danger p-1">Registro eliminado exitosamente, haga clic en <b>Finalizar</b> para salir!</p>
                      <?php else: ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gic1_estado">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="gic1_estado" id="gic1_estado" required disabled>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][2]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][2]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="gic1_item" class="my-0">Tipificación 1</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="gic1_item" id="gic1_item" maxlength="100" value="<?php echo $resultado_registros[0][1]; ?>" required readonly>
                            </div>
                        </div>
                        <?php if($resultado_registros_validar[0][0]>0 OR $resultado_registros_uso[0][0]>0): ?>
                          <p class="alert alert-warning p-1">¡No es posible eliminar el registro, por favor valide antes de continuar!</p>
                        <?php else: ?>
                          <p class="alert alert-danger p-1">¡El registro será eliminado de forma permanente y no se podrá recuperar, por favor valide antes de continuar!</p>
                        <?php endif; ?>
                      <?php endif; ?>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_eliminado_interacciones_configuracion']==1 OR $resultado_registros_validar[0][0]>0 OR $resultado_registros_uso[0][0]>0): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-warning float-end ms-1" type="submit" name="eliminar_registro" id="eliminar_registro_btn">Si, eliminar</button>
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