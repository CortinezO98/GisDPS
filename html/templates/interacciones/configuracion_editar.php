<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Interacciones-Configuración";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Interacciones";
  $subtitle = "Configuración | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="configuracion?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
    $tipo=validar_input($_POST['tipo']);
    $nombre=validar_input($_POST['nombre']);
    $estado=validar_input($_POST['estado']);
    
    if ($tipo=='Lista') {
      $opciones=$_POST['opciones'];
      $opciones_final=implode('|', $opciones);
    } else {
      $opciones_final='';
    }
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_interacciones_auxiliar` SET `gia_tipo`=?, `gia_nombre`=?, `gia_estado`=?, `gia_opciones`=? WHERE `gia_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sssss', $tipo, $nombre, $estado, $opciones_final, $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
    
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

  $consulta_string="SELECT `gia_id`, `gia_campo`, `gia_tipo`, `gia_nombre`, `gia_estado`, `gia_opciones` FROM `gestion_interacciones_auxiliar` WHERE `gia_id`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $resultado_registros_opciones=array();
  $resultado_registros_opciones=explode('|', $resultado_registros[0][5]);
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
                              <label for="campo" class="my-0">Campo</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="campo" id="campo" maxlength="100" value="<?php echo $resultado_registros[0][1]; ?>" required disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][4]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][4]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo">Tipo</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="tipo" id="tipo" required onchange="validar_estado();">
                                  <option value="">Seleccione</option>
                                  <option value="Texto" <?php if($resultado_registros[0][2]=="Texto"){ echo "selected"; } ?>>Texto</option>
                                  <option value="Lista" <?php if($resultado_registros[0][2]=="Lista"){ echo "selected"; } ?>>Lista</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="nombre" class="my-0">Nombre campo</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="nombre" id="nombre" maxlength="100" value="<?php echo $resultado_registros[0][3]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12 mt-1 d-none" id="opciones_respuestas_opciones_div">
                            <div class="form-group my-1">
                                <label for="opciones" class="my-0">Opciones</label>
                                <div class="row" id="opciones_respuestas_opciones">
                                    <?php if(isset($resultado_registros_opciones)): ?>
                                        <?php for ($i=0; $i < count($resultado_registros_opciones); $i++): ?>
                                            <div class="row lista_opciones px-4 col-md-12">
                                                <div class="col-md-12">
                                                    <div class="form-group my-1">
                                                        <input type="text" class="form-control form-control-sm font-size-11" name="opciones[]" id="opciones_<?php echo $i; ?>" maxlength="100" value="<?php echo $resultado_registros_opciones[$i]; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-1 ps-3">
                                                    <a href="#" class="btn btn-danger font-size-11 p-0" style="display: block; width: 185px;" id="del_field" title="Quitar opción"><span class="fas fa-trash-alt"></span> Quitar opción</a>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </div>
                                <a href="#" class="btn btn-primary font-size-11 p-0 mt-1" style="display: block; width: 185px;" id="add_field" title="Añadir opción"><span class="fas fa-plus"></span> Añadir opción</a>
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
  <script type="text/javascript">
      var campos_max = 10;

      var x = 0;
      $('#add_field').click (function(e) {
          e.preventDefault();
          if (x < campos_max) {
              $('#opciones_respuestas_opciones').append('<div class="row lista_opciones px-4 col-md-12">\
                  <div class="col-md-12">\
                      <div class="form-group my-1">\
                          <input type="text" class="form-control form-control-sm font-size-11" name="opciones[]" id="opciones_'+x+'" maxlength="100" value="">\
                      </div>\
                  </div>\
                  <div class="col-12 mb-1 ps-3">\
                      <a href="#" class="btn btn-danger font-size-11 p-0" style="display: block; width: 185px;" id="del_field" title="Quitar opción"><span class="fas fa-trash-alt"></span> Quitar opción</a>\
                  </div>\
              </div>');
              x++;
          }
      });

      $('#opciones_respuestas_opciones').on("click","#del_field",function(e) {
          e.preventDefault();
          $(this).parents('div.lista_opciones').remove();
          x--;
      });

      function validar_estado(){
          var tipo_opcion = document.getElementById("tipo");
          var tipo = tipo_opcion.options[tipo_opcion.selectedIndex].value;

          if(tipo=="Lista") {
              $("#opciones_respuestas_opciones_div").removeClass('d-none').addClass('d-block');
          } else {
              $("#opciones_respuestas_opciones_div").removeClass('d-block').addClass('d-none');
          }
      }
      validar_estado();
  </script>
</body>
</html>