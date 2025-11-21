<?php
    require_once("../iniciador_index.php");
    require_once("../security_session.php");
    /*VARIABLES*/
    $title = "Administrador";
    $subtitle = "Mi Perfil | Cambiar Foto";
    $url_salir="perfil";

    if(isset($_POST["guardar_registro"])){
      if($_SESSION[APP_SESSION.'_session_cambiar_foto']!=1){
          if ($_FILES['documento']['name']!="") {
              $archivo_extension = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
              $NombreArchivo=$_SESSION[APP_SESSION.'_session_usu_id'].".".$archivo_extension;
              $ruta_actual="assets/images/avatar/";
              $ruta_actual_guardar="avatar/";
              $ruta_final=$ruta_actual.$NombreArchivo;
              $ruta_final_guardar=$ruta_actual_guardar.$NombreArchivo;
              if ($_FILES['documento']["error"] > 0) {
                  $control_documento=0;
              } else {
                /*ahora co la funcion move_uploaded_file lo guardaremos en el destino que queramos*/
                  if (move_uploaded_file($_FILES['documento']['tmp_name'], $ruta_final)) {
                      $control_documento=1;
                  } else {
                      $control_documento=0;
                  }
              }
              $control_documento_no=0;
          } else {
              $control_documento_no=0;
          }

          if ((file_exists($ruta_final) AND $control_documento==1)) {
              // Prepara la sentencia
              $consulta_actualizar = $enlace_db->prepare("UPDATE `administrador_usuario` SET `usu_foto`=? WHERE `usu_id`=?");
              // Agrega variables a sentencia preparada
              $consulta_actualizar->bind_param("ss", $ruta_final_guardar, $_SESSION[APP_SESSION.'_session_usu_id']);
              
              // Ejecuta sentencia preparada
              $consulta_actualizar->execute();

              // Evalua resultado de ejecución sentencia preparada
              if (comprobarSentencia($enlace_db->info)) {
                  $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
                  $_SESSION[APP_SESSION.'_session_usu_foto']=$ruta_final_guardar;
                  $_SESSION[APP_SESSION.'_session_cambiar_foto']=1;
              } else {
                  $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
                  unlink($ruta_final);
              }
          } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
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
                      <h4>Cambiar Foto</h4>
                      <?php if($_SESSION[APP_SESSION.'_session_cambiar_foto']!=1): ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="documento" class="my-0">Adjuntar imagen</label>
                                <input class="form-control form-control-sm custom-file-input" name="documento" id="inputGroupFile01" type="file" accept=".png, .PNG, .jpg, .JPG, .jpeg, .JPEG" <?php if(isset($_SESSION[APP_SESSION.'_session_cambiar_foto'])) { echo 'disabled'; } ?> required>
                            </div>
                        </div>
                      <?php endif; ?>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_session_cambiar_foto']==1): ?>
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
        <!-- footer -->
        <?php require_once(ROOT.'includes/_footer.php'); ?>
        <!-- footer -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
      $("#inputGroupFile01").change(function(){
          var valor_opcion = document.getElementById("inputGroupFile01").files[0].name;

          if (valor_opcion!="") {
              document.getElementById('inputGroupFile01').innerHTML=valor_opcion.substring(0, 25)+"...";
              $("#inputGroupFile01").addClass("color-verde");
          }
      });
  </script>
</body>
</html>