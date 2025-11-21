<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Matriz Calidad";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Matriz de Calidad | Crear";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $url_salir="matriz?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
      $estado=validar_input($_POST['estado']);
      $nombre_matriz=validar_input($_POST['nombre_matriz']);
      $canal=validar_input($_POST['canal']);
      $observaciones=validar_input($_POST['observaciones']);
      $usuario_registro=$_SESSION[APP_SESSION.'_session_usu_id'];

      if($_SESSION[APP_SESSION.'_registro_creado_matriz']!=1){
          $codigo_registro=generar_codigo(10);
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_calidad_matriz`(`gcm_id`, `gcm_nombre_matriz`, `gcm_estado`, `gcm_canal`, `gcm_observaciones`, `gcm_registro_usuario`) VALUES (?,?,?,?,?,?)");

          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssss', $codigo_registro, $nombre_matriz, $estado, $canal, $observaciones, $usuario_registro);
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "<script type='text/javascript'>alertify.success('¡Registro creado exitosamente!', 0);</script>";
            $_SESSION[APP_SESSION.'_registro_creado_matriz']=1;
          } else {
            $respuesta_accion = "<script type='text/javascript'>alertify.warning('¡Problemas al crear el registro, por favor verifique e intente nuevamente!', 0);</script>";
          }
          
          
      } else {
          $respuesta_accion = "<script type='text/javascript'>alertify.success('¡Registro creado exitosamente, haga clic en <b>Finalizar</b> para salir!', 0);</script>";
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
            <div class="col-lg-6 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="estado">Estado</label>
                              <select class="form-control form-control-sm" name="estado" id="estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_matriz']==1) { echo 'disabled'; } ?> required>
                                <option value="">Seleccione</option>
                                <option value="Activo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Activo"){ echo "selected"; } ?>>Activo</option>
                                <option value="Inactivo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="nombre_matriz">Nombre matriz</label>
                            <input type="text" class="form-control form-control-sm" name="nombre_matriz" id="nombre_matriz" maxlength="50" value="<?php if(isset($_POST["guardar_registro"])){ echo $nombre_matriz; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_matriz']==1) { echo 'readonly'; } ?> required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="canal">Canal</label>
                              <select class="form-control form-control-sm" name="canal" id="canal" <?php if($_SESSION[APP_SESSION.'_registro_creado_matriz']==1) { echo 'disabled'; } ?> required>
                                <option value="">Seleccione</option>
                                <option value="Telefónico" <?php if(isset($_POST["guardar_registro"]) AND $canal=="Telefónico"){ echo "selected"; } ?>>Telefónico</option>
                                <option value="Virtual" <?php if(isset($_POST["guardar_registro"]) AND $canal=="Virtual"){ echo "selected"; } ?>>Virtual</option>
                                <option value="Presencial" <?php if(isset($_POST["guardar_registro"]) AND $canal=="Presencial"){ echo "selected"; } ?>>Presencial</option>
                                <option value="Escrito" <?php if(isset($_POST["guardar_registro"]) AND $canal=="Escrito"){ echo "selected"; } ?>>Escrito</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_matriz']==1) { echo 'readonly'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $observaciones; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_matriz']==1): ?>
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