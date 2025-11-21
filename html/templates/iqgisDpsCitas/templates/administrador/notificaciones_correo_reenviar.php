<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Administrador";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Aadministrador";
  $subtitle = "Notificaciones Correo | Reenviar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="notificaciones_correo?pagina=".$pagina."&id=".$filtro_permanente;

  $consulta_string="SELECT `nc_id`, `nc_id_modulo`, `nc_prioridad`, `nc_id_set_from`, `nc_address`, `nc_cc`, `nc_bcc`, `nc_reply_to`, `nc_subject`, `nc_body`, `nc_embeddedimage_ruta`, `nc_embeddedimage_nombre`, `nc_embeddedimage_tipo`, `nc_intentos`, `nc_eliminar`, `nc_estado_envio`, `nc_fecha_envio`, `nc_fecha_registro`, `nc_usuario_registro`, `ncr_username`, `ncr_setfrom_name`, TU.`usu_nombres_apellidos` FROM `administrador_notificaciones` LEFT JOIN `administrador_buzones` AS RT ON `administrador_notificaciones`.`nc_id_set_from`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TU ON `administrador_notificaciones`.`nc_usuario_registro`=TU.`usu_id` WHERE `nc_id`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
    $destinatario=str_replace(array("\r\n", "\n\r", "\r", "\n"), ";", $_POST['destinatario']);
    $destinatario_cc=str_replace(array("\r\n", "\n\r", "\r", "\n"), ";", $_POST['destinatario_cc']);
    if($_SESSION[APP_SESSION.'_registro_creado_notificacion']!=1){
        //PROGRAMACIÓN NOTIFICACIÓN
        /*SE ESTRUCTURA COTENIDO DE CORREO*/
            $contenido_correo=$resultado_registros[0][9];
        /*SE ESTRUCTURA COTENIDO DE CORREO*/

        /*SE CONFIGURAN PARÁMETROS A REGISTRAR EN SISTEMA DE NOTIFICACIÓN*/
        $nc_id_modulo=$modulo_plataforma;
        $nc_prioridad='1';
        $nc_id_set_from='1';
        $nc_address=$destinatario;
        $nc_cc=$destinatario_cc;
        $nc_bcc='';
        $nc_reply_to="";
        $nc_subject=$resultado_registros[0][8];
        $nc_body=str_replace("'", '"', $contenido_correo);
        $nc_embeddedimage_ruta=$resultado_registros[0][10];
        $nc_embeddedimage_nombre=$resultado_registros[0][11];
        $nc_embeddedimage_tipo=$resultado_registros[0][12];
        $nc_intentos="";
        $nc_eliminar="Si";
        $nc_estado_envio="Pendiente";
        $nc_fecha_envio="";
        $nc_usuario_registro=$_SESSION[APP_SESSION.'_session_usu_id'];

        $verifica_notificacion=0;
        for ($i=0; $i < 10; $i++) {
          $consulta_notificacion = mysqli_query($enlace_db, "INSERT INTO `administrador_notificaciones`(`nc_id_modulo`, `nc_prioridad`, `nc_id_set_from`, `nc_address`, `nc_cc`, `nc_bcc`, `nc_reply_to`, `nc_subject`, `nc_body`, `nc_embeddedimage_ruta`, `nc_embeddedimage_nombre`, `nc_embeddedimage_tipo`, `nc_intentos`, `nc_eliminar`, `nc_estado_envio`, `nc_fecha_envio`, `nc_usuario_registro`) VALUES ('".$nc_id_modulo."','".$nc_prioridad."','".$nc_id_set_from."','".$nc_address."','".$nc_cc."','".$nc_bcc."','".$nc_reply_to."','".$nc_subject."','".$nc_body."','".$nc_embeddedimage_ruta."','".$nc_embeddedimage_nombre."','".$nc_embeddedimage_tipo."','".$nc_intentos."','".$nc_eliminar."','".$nc_estado_envio."','".$nc_fecha_envio."','".$nc_usuario_registro."');");

            if ($consulta_notificacion) {
                $_SESSION[APP_SESSION.'_registro_creado_notificacion']=1;
                registro_log($enlace_db, $modulo_plataforma, 'notificacion', 'Notificación programada '.$nc_subject);
                break;
            }
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
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="asunto">Asunto</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="asunto" id="asunto" maxlength="100" value="<?php echo $resultado_registros[0][8]; ?>" required readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="remitente">Remitente</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="remitente" id="remitente" maxlength="100" value="<?php echo $resultado_registros[0][19]; ?>" required readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="destinatario">Destinatario</label>
                              <textarea class="form-control form-control-sm height-100" name="destinatario" id="destinatario" <?php if($_SESSION[APP_SESSION.'_registro_creado_notificacion']==1) { echo 'readonly'; } ?>><?php echo str_replace(";", "\r", $destinatario); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="destinatario_cc">Destinatario CC</label>
                              <textarea class="form-control form-control-sm height-100" name="destinatario_cc" id="destinatario_cc" <?php if($_SESSION[APP_SESSION.'_registro_creado_notificacion']==1) { echo 'readonly'; } ?>><?php echo str_replace(";", "\r", $destinatario_cc); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_notificacion']==1): ?>
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