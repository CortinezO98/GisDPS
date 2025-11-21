<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Conocimiento";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Conocimiento";
  $subtitle = "Gestión Conocimiento | Eliminar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_categoria=validar_input($_GET['cat']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="conocimiento?pagina=".$pagina."&id=".$filtro_permanente."&cat=".$id_categoria;

  if(isset($_POST["eliminar_registro"])){
      if($_SESSION[APP_SESSION.'_gconocimiento_registro_eliminado']!=1){
          $consulta_string_validar_pre="SELECT `gc_codigo`, `gc_categoria`, TC.`gcc_orden`, TC.`gcc_nombre_categoria`, `gc_nombre`, `gc_descripcion`, `gc_ruta`, `gc_extension`, `gc_version`, `gc_visitas`, `gc_registro_usuario`, TU.`usu_nombres_apellidos`, `gc_registro_fecha`, `gc_actualiza_fecha`, `gc_actualiza_usuario`, TUA.`usu_nombres_apellidos` FROM `gestion_conocimiento` LEFT JOIN `gestion_conocimiento_categoria` AS TC ON `gestion_conocimiento`.`gc_categoria`=TC.`gcc_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_conocimiento`.`gc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_conocimiento`.`gc_actualiza_usuario`=TUA.`usu_id` WHERE `gc_codigo`=?";

          $consulta_registros_validar_pre = $enlace_db->prepare($consulta_string_validar_pre);
          $consulta_registros_validar_pre->bind_param("s", $id_registro);
          $consulta_registros_validar_pre->execute();
          $resultado_registros_validar_pre = $consulta_registros_validar_pre->get_result()->fetch_all(MYSQLI_NUM);
              
          // Prepara la sentencia
          $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_conocimiento` WHERE `gc_codigo`=?");

          // Agrega variables a sentencia preparada
          $sentencia_delete->bind_param('s', $id_registro);
          
          // Evalua resultado de ejecución sentencia preparada
          if ($sentencia_delete->execute()) {
              unlink($resultado_registros_validar_pre[0][6]);
              $_SESSION[APP_SESSION.'_gconocimiento_registro_eliminado']=1;
              $respuesta_accion = "alertButton('success', 'Registro eliminado', 'Registro eliminado exitosamente', '".$url_salir."');";
          } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al eliminar el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string="SELECT `gc_codigo`, `gc_categoria`, TC.`gcc_orden`, TC.`gcc_nombre_categoria`, `gc_nombre`, `gc_descripcion`, `gc_ruta`, `gc_extension`, `gc_version`, `gc_visitas`, `gc_registro_usuario`, TU.`usu_nombres_apellidos`, `gc_registro_fecha`, `gc_actualiza_fecha`, `gc_actualiza_usuario`, TUA.`usu_nombres_apellidos` FROM `gestion_conocimiento` LEFT JOIN `gestion_conocimiento_categoria` AS TC ON `gestion_conocimiento`.`gc_categoria`=TC.`gcc_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_conocimiento`.`gc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_conocimiento`.`gc_actualiza_usuario`=TUA.`usu_id` WHERE `gc_codigo`=?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

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
                      <?php if($_SESSION[APP_SESSION.'_gconocimiento_registro_eliminado']==1): ?>
                          <p class="alert alert-danger p-1">Documento eliminado exitosamente, haga clic en <b>Finalizar</b> para salir!</p>
                      <?php else: ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="categoria" class="m-0">Categoría</label>
                                <select class="form-control form-control-sm form-select" name="categoria" id="categoria" required disabled>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_categoria); $i++): ?>
                                        <option value='<?php echo $resultado_registros_categoria[$i][0]; ?>' <?php if($resultado_registros[0][1]==$resultado_registros_categoria[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_categoria[$i][1].". ".$resultado_registros_categoria[$i][2]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="nombre_documento" class="m-0">Nombre documento</label>
                              <input type="text" class="form-control form-control-sm" name="nombre_documento" id="nombre_documento" maxlength="300" value="<?php echo $resultado_registros[0][4]; ?>" required readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="descripcion" class="m-0">Descripción</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="descripcion" id="descripcion" maxlength="2000" required readonly><?php echo $resultado_registros[0][5]; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="version" class="m-0">Versión</label>
                              <input type="number" class="form-control form-control-sm" name="version" id="version" maxlength="3" step="0.1" min="1" max="999" value="<?php echo $resultado_registros[0][8]; ?>" readonly>
                            </div>
                        </div>
                        <p class="alert alert-danger p-1">¡El documento será eliminado de forma permanente y no se podrá recuperar, por favor valide antes de continuar!</p>
                      <?php endif; ?>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_gconocimiento_registro_eliminado']==1): ?>
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