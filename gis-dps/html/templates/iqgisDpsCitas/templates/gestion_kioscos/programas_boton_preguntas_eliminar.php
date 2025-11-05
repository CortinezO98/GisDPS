<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Kioscos";
  $subtitle = "Programas | Botones | Preguntas | Eliminar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $id_boton=validar_input(base64_decode($_GET['btn']));
  $id_pregunta=validar_input(base64_decode($_GET['pre']));
  $url_salir="programas_boton_preguntas?pagina=".$pagina."&id=".$filtro_permanente."&reg=".base64_encode($id_registro)."&btn=".base64_encode($id_boton);

  if(isset($_POST["eliminar_registro"])){
    if($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta_eliminar']!=1){
        $pregunta=validar_input($_POST['pregunta']);
        // Prepara la sentencia
        $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_kioscos_preguntas` WHERE `gkbp_id`=?");
        // Agrega variables a sentencia preparada
        $sentencia_delete->bind_param('s', $id_pregunta);
        
        // Evalua resultado de ejecución sentencia preparada
        if ($sentencia_delete->execute()) {
            $consulta_string_log = "INSERT INTO `administrador_log`(`clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_registro_usuario`) VALUES (?,?,?,?,?)";
            
            $log_modulo=$modulo_plataforma;
            $log_tipo="eliminar";
            $log_accion="Eliminar registro";
            $log_detalle="Pregunta [".$pregunta."]";
            $log_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
            
            $consulta_registros_log = $enlace_db->prepare($consulta_string_log);
            $consulta_registros_log->bind_param("sssss", $log_modulo, $log_tipo, $log_accion, $log_detalle, $log_usuario);
            $consulta_registros_log->execute();

            $_SESSION[APP_SESSION.'_registro_creado_boton_pregunta_eliminar']=1;
            $respuesta_accion = "alertButton('success', 'Registro eliminado', 'Registro eliminado exitosamente', '".$url_salir."');";
        } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al eliminar el registro');";
        }
    } else {
        $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
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
                        <?php if($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta_eliminar']==1): ?>
                            <p class="alert alert-danger p-1">¡Registro eliminado exitosamente, haga clic en <b>Finalizar</b> para salir!</p>
                        <?php else: ?>
                          <div class="col-md-6">
                              <div class="form-group my-1">
                                  <label for="estado" class="my-0">Estado</label>
                                  <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" required disabled>
                                    <option value="">Seleccione</option>
                                    <option value="Activo" <?php if($resultado_registros[0][7]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                    <option value="Inactivo" <?php if($resultado_registros[0][7]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                <label for="pregunta" class="my-0">Pregunta</label>
                                <textarea class="form-control form-control-sm font-size-11 height-100" name="pregunta" id="pregunta" required readonly><?php echo $resultado_registros[0][4]; ?></textarea>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                <label for="respuesta" class="my-0">Respuesta</label>
                                <textarea class="form-control form-control-sm font-size-11 height-100" name="respuesta" id="respuesta" required readonly><?php echo $resultado_registros[0][5]; ?></textarea>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                <label for="palabras_claves" class="my-0">Palabras claves</label>
                                <textarea class="form-control form-control-sm font-size-11 height-100" name="palabras_claves" id="palabras_claves" required readonly><?php echo $resultado_registros[0][6]; ?></textarea>
                              </div>
                          </div>
                          <div class="col-md-12">
                            <p class="alert alert-danger p-1">¡El registro será eliminado de forma permanente y no se podrá recuperar, por favor valide antes de continuar!</p>
                          </div>
                        <?php endif; ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta_eliminar']==1): ?>
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