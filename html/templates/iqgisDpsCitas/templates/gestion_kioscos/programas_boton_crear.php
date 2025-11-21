<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Kioscos";
  $subtitle = "Programas | Botones | Crear";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="programas_boton?pagina=".$pagina."&id=".$filtro_permanente."&reg=".base64_encode($id_registro);

  if(isset($_POST["guardar_registro"])){
      $nombre=validar_input($_POST['nombre']);
      $tipo=validar_input($_POST['tipo']);
      $estado=validar_input($_POST['estado']);
      $url=validar_input($_POST['url']);

      if($_SESSION[APP_SESSION.'_registro_creado_boton']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_kioscos_programas_boton`(`gkpb_programa`, `gkpb_nombre`, `gkpb_tipo`, `gkpb_estado`, `gkpb_url`, `gkpb_registro_usuario`) VALUES (?,?,?,?,?,?)");

          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssss', $id_registro, $nombre, $tipo, $estado, $url, $_SESSION[APP_SESSION.'_session_usu_id']);
          
          if ($sentencia_insert->execute()) {
            $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
            $_SESSION[APP_SESSION.'_registro_creado_boton']=1;
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
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label for="estado" class="my-0">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_boton']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label for="tipo" class="my-0">Tipo</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="tipo" id="tipo" <?php if($_SESSION[APP_SESSION.'_registro_creado_boton']==1) { echo 'disabled'; } ?> required onchange="validar_tipo();">
                                  <option value="">Seleccione</option>
                                  <option value="Preguntas" <?php if(isset($_POST["guardar_registro"]) AND $tipo=="Preguntas"){ echo "selected"; } ?>>Preguntas</option>
                                  <option value="Url" <?php if(isset($_POST["guardar_registro"]) AND $tipo=="Url"){ echo "selected"; } ?>>Url</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="nombre" class="my-0">Nombre botón</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="nombre" id="nombre" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $nombre; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_boton']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-12 d-none" id="div_tipo">
                            <div class="form-group my-1">
                              <label for="url" class="my-0">Url</label>
                              <input type="url" class="form-control form-control-sm font-size-11" name="url" id="url" value="<?php if(isset($_POST["guardar_registro"])){ echo $url; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_boton']==1) { echo 'readonly'; } ?> required disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_boton']==1): ?>
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
      function validar_tipo(){
          var tipo_opcion = document.getElementById("tipo");
          var tipo = tipo_opcion.options[tipo_opcion.selectedIndex].value;
          
          var url = document.getElementById('url').disabled=true;
          $("#div_tipo").removeClass('d-block').addClass('d-none');
          if (tipo=="Url") {
            var url = document.getElementById('url').disabled=false;
            $("#div_tipo").removeClass('d-none').addClass('d-block');
          }
      }
      validar_tipo();
  </script>
</body>
</html>