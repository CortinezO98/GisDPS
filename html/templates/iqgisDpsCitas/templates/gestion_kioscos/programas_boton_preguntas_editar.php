<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Kioscos";
  $subtitle = "Programas | Botones | Preguntas | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $id_boton=validar_input(base64_decode($_GET['btn']));
  $id_pregunta=validar_input(base64_decode($_GET['pre']));
  $url_salir="programas_boton_preguntas?pagina=".$pagina."&id=".$filtro_permanente."&reg=".base64_encode($id_registro)."&btn=".base64_encode($id_boton);

  if(isset($_POST["guardar_registro"])){
    $estado=validar_input($_POST['estado']);
    $pregunta=validar_input($_POST['pregunta']);
    $respuesta=validar_input($_POST['respuesta']);
    $palabras_claves=validar_input($_POST['palabras_claves']);
    $orden=0;
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_kioscos_preguntas` SET `gkbp_orden`=?,`gkbp_pregunta`=?,`gkbp_respuesta`=?,`gkbp_palabras_claves`=?,`gkbp_estado`=?,`gkbp_actualiza_usuario`=?,`gkbp_actualiza_fecha`=? WHERE `gkbp_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('ssssssss', $orden, $pregunta, $respuesta, $palabras_claves, $estado, $_SESSION[APP_SESSION.'_session_usu_id'], date('Y-m-d H:i:s'), $id_pregunta);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();

    // Evalua resultado de ejecución sentencia preparada
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

    $consulta_string="SELECT `gkbp_id`, `gkbp_programa`, `gkbp_boton`, `gkbp_orden`, `gkbp_pregunta`, `gkbp_respuesta`, `gkbp_palabras_claves`, `gkbp_estado`, `gkbp_actualiza_usuario`, `gkbp_actualiza_fecha`, `gkbp_registro_usuario`, `gkbp_registro_fecha`, TP.`gkp_titulo`, TB.`gkpb_nombre`, TU.`usu_nombres_apellidos` FROM `gestion_kioscos_preguntas` LEFT JOIN `gestion_kioscos_programas` AS TP ON `gestion_kioscos_preguntas`.`gkbp_programa`=TP.`gkp_id` LEFT JOIN `gestion_kioscos_programas_boton` AS TB ON `gestion_kioscos_preguntas`.`gkbp_boton`=TB.`gkpb_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_preguntas`.`gkbp_registro_usuario`=TU.`usu_id` WHERE `gkbp_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_pregunta);
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
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label for="estado" class="my-0">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" required onchange="validar_tipo();">
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][7]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][7]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="pregunta" class="my-0">Pregunta</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="pregunta" id="pregunta" required><?php echo $resultado_registros[0][4]; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="respuesta" class="my-0">Respuesta</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="respuesta" id="respuesta" required><?php echo $resultado_registros[0][5]; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="palabras_claves" class="my-0">Palabras claves</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="palabras_claves" id="palabras_claves" required><?php echo $resultado_registros[0][6]; ?></textarea>
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