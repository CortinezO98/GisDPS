<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Matriz Calidad";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Matriz de Calidad | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="matriz?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
      $estado=validar_input($_POST['estado']);
      $nombre_matriz=validar_input($_POST['nombre_matriz']);
      $canal=validar_input($_POST['canal']);
      $observaciones=validar_input($_POST['observaciones']);

      // Prepara la sentencia
      $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_calidad_matriz` SET `gcm_nombre_matriz`=?,`gcm_estado`=?, `gcm_canal`=?,`gcm_observaciones`=? WHERE  `gcm_id`=?");

      // Agrega variables a sentencia preparada
      $consulta_actualizar->bind_param('sssss', $nombre_matriz, $estado, $canal, $observaciones, $id_registro);
      
      // Ejecuta sentencia preparada
      $consulta_actualizar->execute();
      
      if (comprobarSentencia($enlace_db->info)) {
          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
      } else {
          $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
      }
  }

  $consulta_string="SELECT `gcm_id`, `gcm_nombre_matriz`, `gcm_estado`, `gcm_canal`, `gcm_observaciones`, `gcm_registro_usuario`, `gcm_registro_fecha` FROM `gestion_calidad_matriz` WHERE `gcm_id`=?";
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select class="form-control form-control-sm" name="estado" id="estado" required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][2]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][2]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="nombre_matriz">Nombre matriz</label>
                              <input type="text" class="form-control form-control-sm" name="nombre_matriz" id="nombre_matriz" maxlength="50" value="<?php echo $resultado_registros[0][1]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="canal">Canal</label>
                                <select class="form-control form-control-sm" name="canal" id="canal" required>
                                  <option value="">Seleccione</option>
                                  <option value="Telefónico" <?php if($resultado_registros[0][3]=="Telefónico"){ echo "selected"; } ?>>Telefónico</option>
                                  <option value="Virtual" <?php if($resultado_registros[0][3]=="Virtual"){ echo "selected"; } ?>>Virtual</option>
                                  <option value="Presencial" <?php if($resultado_registros[0][3]=="Presencial"){ echo "selected"; } ?>>Presencial</option>
                                  <option value="Escrito" <?php if($resultado_registros[0][3]=="Escrito"){ echo "selected"; } ?>>Escrito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones"><?php echo $resultado_registros[0][4]; ?></textarea>
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