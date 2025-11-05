<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Kioscos";
  $subtitle = "Programas | Botones | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $id_boton=validar_input(base64_decode($_GET['btn']));
  $url_salir="programas_boton?pagina=".$pagina."&id=".$filtro_permanente."&reg=".base64_encode($id_registro);

  if(isset($_POST["guardar_registro"])){
    $nombre=validar_input($_POST['nombre']);
    $tipo=validar_input($_POST['tipo']);
    $estado=validar_input($_POST['estado']);
    $url=validar_input($_POST['url']);
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_kioscos_programas_boton` SET `gkpb_nombre`=?,`gkpb_tipo`=?,`gkpb_estado`=?,`gkpb_url`=? WHERE `gkpb_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sssss', $nombre, $tipo, $estado, $url, $id_boton);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();

    // Evalua resultado de ejecución sentencia preparada
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

    $consulta_string="SELECT `gkpb_id`, `gkpb_programa`, `gkpb_nombre`, `gkpb_tipo`, `gkpb_estado`, `gkpb_url`, `gkpb_registro_usuario`, `gkpb_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_kioscos_programas_boton` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_programas_boton`.`gkpb_registro_usuario`=TU.`usu_id` WHERE `gkpb_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_boton);
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
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label for="estado" class="my-0">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" required onchange="validar_tipo();">
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][4]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][4]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label for="tipo" class="my-0">Tipo</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="tipo" id="tipo"  required>
                                  <option value="">Seleccione</option>
                                  <option value="Preguntas" <?php if($resultado_registros[0][3]=="Preguntas"){ echo "selected"; } ?>>Preguntas</option>
                                  <option value="Url" <?php if($resultado_registros[0][3]=="Url"){ echo "selected"; } ?>>Url</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="nombre" class="my-0">Nombre botón</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="nombre" id="nombre" maxlength="100" value="<?php echo $resultado_registros[0][2]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12 d-none" id="div_tipo">
                            <div class="form-group my-1">
                              <label for="url" class="my-0">Url</label>
                              <input type="url" class="form-control form-control-sm font-size-11" name="url" id="url" maxlength="100" value="<?php echo $resultado_registros[0][5]; ?>" required disabled>
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