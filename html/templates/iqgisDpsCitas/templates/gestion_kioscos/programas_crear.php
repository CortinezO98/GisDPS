<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Kioscos";
  $subtitle = "Programas | Crear";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $url_salir="programas?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
      $estado=validar_input($_POST['estado']);
      $programa=validar_input($_POST['programa']);
      $orden=0;
      if($_SESSION[APP_SESSION.'_registro_creado_programa']!=1){
          $codigo_documento=generar_codigo(10);
          if ($_FILES['documento']['name']!="") {
              $archivo_extension = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
              $NombreArchivo=$codigo_documento.".".$archivo_extension;
              $ruta_actual="../kiosco/images/";
              $ruta_final=$ruta_actual.$NombreArchivo;
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
          } else {
              $control_documento=0;
          }

          if (file_exists($ruta_final) AND $control_documento==1) {
            // Prepara la sentencia
            $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_kioscos_programas`(`gkp_titulo`, `gkp_imagen`, `gkp_estado`, `gkp_orden`, `gkp_registro_usuario`) VALUES (?,?,?,?,?)");

            // Agrega variables a sentencia preparada
            $sentencia_insert->bind_param('sssss', $programa, $ruta_final, $estado, $orden, $_SESSION[APP_SESSION.'_session_usu_id']);
            
            if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_programa']=1;
            } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
              unlink($ruta_final);
            }
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
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label for="estado" class="my-0">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_programa']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="programa" class="my-0">Programa</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="programa" id="programa" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $programa; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_programa']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                              <label for="documento" class="my-0">Imagen</label>
                              <input class="form-control form-control-sm custom-file-input" name="documento" id="inputGroupFile01" type="file" accept=".png, .PNG, .jpg, .JPG, .jpeg, .JPEG" <?php if(isset($_SESSION[APP_SESSION.'_registro_creado_programa'])) { echo 'disabled'; } ?> required>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_programa']==1): ?>
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