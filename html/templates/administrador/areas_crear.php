<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Administrador";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "ADMINISTRADOR";
  $subtitle = "ÁREAS <i class='fas fa-chevron-right'></i> CREAR";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $url_salir="areas?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
      $nombre_campania=validar_input($_POST['nombre_campania']);
      $observaciones=validar_input($_POST['observaciones']);

      if($_SESSION[APP_SESSION.'_registro_creado_area']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `administrador_campania`(`ac_nombre_campania`, `ac_observaciones`) VALUES (?,?)");

          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ss', $nombre_campania, $observaciones);
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_area']=1;
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
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
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="nombre_campania" class="my-0">Nombre área</label>
                            <input type="text" class="form-control form-control-sm font-size-11" name="nombre_campania" id="nombre_campania" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $nombre_campania; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_area']==1) { echo 'readonly'; } ?> required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones" maxlength="500" <?php if($_SESSION[APP_SESSION.'_registro_creado_area']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $observaciones; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_area']==1): ?>
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