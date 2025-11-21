<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Conocimiento";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Conocimiento";
  $subtitle = "Gestión Conocimiento | Crear";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_categoria=validar_input($_GET['cat']);
  $url_salir="conocimiento?pagina=".$pagina."&id=".$filtro_permanente."&cat=".$id_categoria;

  if(isset($_POST["guardar_registro"])){
      $categoria=validar_input($_POST['categoria']);
      $nombre_documento=validar_input($_POST['nombre_documento']);
      $descripcion=validar_input($_POST['descripcion']);
      $version=validar_input($_POST['version']);
      $visitas=0;

      if($_SESSION[APP_SESSION.'_gconocimiento_registro_creado']!=1){
          $codigo_documento=generar_codigo(10);
          if ($_FILES['documento']['name']!="") {
              $archivo_extension = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
              $NombreArchivo=$codigo_documento.".".$archivo_extension;
              $ruta_actual="storage/";
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
              $control_documento_no=0;
          } else {
              $control_documento_no=1;
          }

          if ((file_exists($ruta_final) AND $control_documento==1) OR $control_documento_no=1) {
              // Prepara la sentencia
              $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_conocimiento`(`gc_codigo`, `gc_categoria`, `gc_nombre`, `gc_descripcion`, `gc_ruta`, `gc_extension`, `gc_version`, `gc_visitas`, `gc_actualiza_usuario`, `gc_actualiza_fecha`, `gc_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
              // Agrega variables a sentencia preparada
              $sentencia_insert->bind_param('sssssssssss', $codigo_documento, $categoria, $nombre_documento, $descripcion, $ruta_final, $archivo_extension, $version, $visitas, $_SESSION[APP_SESSION.'_session_usu_id'], date('Y-m-d H:i:s'), $_SESSION[APP_SESSION.'_session_usu_id']);

              if ($sentencia_insert->execute()) {
                  $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
                  $_SESSION[APP_SESSION.'_gconocimiento_registro_creado']=1;
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

  $consulta_string_categoria="SELECT `gcc_id`, `gcc_orden`, `gcc_nombre_categoria`, `gcc_descripcion` FROM `gestion_conocimiento_categoria` WHERE 1=1 ORDER BY `gcc_orden`, `gcc_nombre_categoria`";
  $consulta_registros_categoria = $enlace_db->prepare($consulta_string_categoria);
  $consulta_registros_categoria->execute();
  $resultado_registros_categoria = $consulta_registros_categoria->get_result()->fetch_all(MYSQLI_NUM);
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
                              <label for="categoria" class="m-0">Categoría</label>
                              <select class="form-control form-control-sm form-select" name="categoria" id="categoria" <?php if(isset($_SESSION[APP_SESSION.'_gconocimiento_registro_creado'])) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($resultado_registros_categoria); $i++): ?>
                                      <option value='<?php echo $resultado_registros_categoria[$i][0]; ?>' <?php if(isset($_POST["guardar_registro"]) AND $categoria==$resultado_registros_categoria[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_categoria[$i][1].". ".$resultado_registros_categoria[$i][2]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="nombre_documento" class="m-0">Nombre documento</label>
                            <input type="text" class="form-control form-control-sm" name="nombre_documento" id="nombre_documento" maxlength="300" value="<?php if(isset($_POST["guardar_registro"])){ echo $nombre_documento; } ?>" <?php if(isset($_SESSION[APP_SESSION.'_gconocimiento_registro_creado'])) { echo 'readonly'; } ?> required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="descripcion" class="m-0">Descripción</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="descripcion" id="descripcion" maxlength="2000" <?php if(isset($_SESSION[APP_SESSION.'_gconocimiento_registro_creado'])) { echo 'readonly'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $descripcion; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="version" class="m-0">Versión</label>
                            <input type="number" class="form-control form-control-sm" name="version" id="version" maxlength="3" step="0.1" min="1" max="999" value="<?php if(isset($_POST["guardar_registro"])){ echo $version; } ?>" <?php if(isset($_SESSION[APP_SESSION.'_gconocimiento_registro_creado'])) { echo 'readonly'; } ?>>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="documento" class="my-0">Adjuntar documento</label>
                              <input class="form-control form-control-sm custom-file-input" name="documento" id="inputGroupFile01" type="file" <?php if(isset($_SESSION[APP_SESSION.'_gconocimiento_registro_creado'])) { echo 'disabled'; } ?> required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_gconocimiento_registro_creado']==1): ?>
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