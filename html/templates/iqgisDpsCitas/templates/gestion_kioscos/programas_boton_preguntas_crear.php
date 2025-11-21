<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Kioscos";
  $subtitle = "Programas | Botones | Preguntas | Crear";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $id_boton=validar_input(base64_decode($_GET['btn']));
  $url_salir="programas_boton_preguntas?pagina=".$pagina."&id=".$filtro_permanente."&reg=".base64_encode($id_registro)."&btn=".base64_encode($id_boton);

  if(isset($_POST["guardar_registro"])){
      $estado=validar_input($_POST['estado']);
      $pregunta=validar_input($_POST['pregunta']);
      $respuesta=validar_input($_POST['respuesta']);
      $palabras_claves=validar_input($_POST['palabras_claves']);
      $orden=0;
      if($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_kioscos_preguntas`(`gkbp_programa`, `gkbp_boton`, `gkbp_orden`, `gkbp_pregunta`, `gkbp_respuesta`, `gkbp_palabras_claves`, `gkbp_estado`, `gkbp_actualiza_usuario`, `gkbp_actualiza_fecha`, `gkbp_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?)");

          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssss', $id_registro, $id_boton, $orden, $pregunta, $respuesta, $palabras_claves, $estado, $_SESSION[APP_SESSION.'_session_usu_id'], date('Y-m-d H:i:s'), $_SESSION[APP_SESSION.'_session_usu_id']);
          
          if ($sentencia_insert->execute()) {
            $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
            $_SESSION[APP_SESSION.'_registro_creado_boton_pregunta']=1;
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
            unlink($ruta_final);
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
            <div class="col-lg-6 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label for="estado" class="my-0">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="pregunta" class="my-0">Pregunta</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="pregunta" id="pregunta" <?php if($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta']==1) { echo 'readonly'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $pregunta; } ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="respuesta" class="my-0">Respuesta</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="respuesta" id="respuesta" <?php if($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta']==1) { echo 'readonly'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $respuesta; } ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="palabras_claves" class="my-0">Palabras claves</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="palabras_claves" id="palabras_claves" <?php if($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta']==1) { echo 'readonly'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $palabras_claves; } ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta']==1): ?>
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