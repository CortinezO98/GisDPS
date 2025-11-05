<?php
  // Validación de permisos del usuario para el módulo
  $modulo_plataforma = "Administrador";
  require_once("../../iniciador.php");
  $url_fichero = pathinfo(__FILE__, PATHINFO_FILENAME);

  /* VARIABLES */
  $title  = "Aadministrador";
  $subtitle = "Notificaciones Correo | Editar";
  $pagina = validar_input($_GET['pagina']);
  $filtro_permanente = validar_input($_GET['id']);
  $id_registro = validar_input(base64_decode($_GET['reg']));
  $url_salir   = "notificaciones_correo?pagina=" . $pagina . "&id=" . $filtro_permanente;

  if (isset($_POST["guardar_registro"])) {
    // 1) Leer valores que escribió el usuario y reemplazar saltos de línea por ';'
    $destinatario    = str_replace(
      array("\r\n", "\n\r", "\r", "\n"),
      ";",
      $_POST['destinatario']
    );
    $destinatario_cc = str_replace(
      array("\r\n", "\n\r", "\r", "\n"),
      ";",
      $_POST['destinatario_cc']
    );

    // 2) Preparar sentencia para UPDATE
    $consulta_actualizar = $enlace_db->prepare("
      UPDATE `administrador_notificaciones`
         SET `nc_address`      = ?,
             `nc_cc`           = ?,
             `nc_estado_envio` = 'Pendiente'
       WHERE `nc_id` = ?
    ");
    $consulta_actualizar->bind_param("ssi", $destinatario, $destinatario_cc, $id_registro);
    $consulta_actualizar->execute();

    if (comprobarSentencia($enlace_db->info)) {
      $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

  // 3) Obtener la fila actual para precargar el formulario
  $consulta_string = "
    SELECT
      `nc_id`,
      `nc_id_modulo`,
      `nc_prioridad`,
      `nc_id_set_from`,
      `nc_address`,
      `nc_cc`,
      `nc_bcc`,
      `nc_reply_to`,
      `nc_subject`,
      `nc_body`,
      `nc_embeddedimage_ruta`,
      `nc_embeddedimage_nombre`,
      `nc_embeddedimage_tipo`,
      `nc_intentos`,
      `nc_eliminar`,
      `nc_estado_envio`,
      `nc_fecha_envio`,
      `nc_fecha_registro`,
      `nc_usuario_registro`,
      `ncr_username`,
      `ncr_setfrom_name`,
      TU.`usu_nombres_apellidos`
    FROM `administrador_notificaciones`
    LEFT JOIN `administrador_buzones` AS RT 
      ON `administrador_notificaciones`.`nc_id_set_from` = RT.`ncr_id`
    LEFT JOIN `administrador_usuario` AS TU 
      ON `administrador_notificaciones`.`nc_usuario_registro` = TU.`usu_id`
    WHERE `nc_id` = ?
  ";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT . 'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <!-- navbar -->
    <?php require_once(ROOT . 'includes/_navbar.php'); ?>
    <!-- /navbar -->
    <div class="container-fluid page-body-wrapper">
      <!-- sidebar -->
      <?php require_once(ROOT . 'includes/_sidebar.php'); ?>
      <!-- /sidebar -->
      <!-- main-panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center">
              <?php
                if (!empty($respuesta_accion)) {
                  echo "<script type='text/javascript'>" . $respuesta_accion . "</script>";
                }
              ?>
              <div class="col-lg-4 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                          <!-- Asunto (readonly) -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="asunto">Asunto</label>
                              <input
                                type="text"
                                class="form-control form-control-sm font-size-11"
                                name="asunto"
                                id="asunto"
                                maxlength="100"
                                value="<?php echo htmlspecialchars($resultado_registros[0][8]); ?>"
                                required
                                readonly
                              >
                            </div>
                          </div>
                          <!-- Remitente (readonly) -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="remitente">Remitente</label>
                              <input
                                type="text"
                                class="form-control form-control-sm font-size-11"
                                name="remitente"
                                id="remitente"
                                maxlength="100"
                                value="<?php echo htmlspecialchars($resultado_registros[0][20]); ?>"
                                required
                                readonly
                              >
                            </div>
                          </div>
                          <!-- Destinatario -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="destinatario">Destinatario</label>
                              <textarea
                                class="form-control form-control-sm height-100"
                                name="destinatario"
                                id="destinatario"
                              ><?php echo htmlspecialchars(str_replace(";", "\r", $resultado_registros[0][4])); ?></textarea>
                            </div>
                          </div>
                          <!-- Destinatario CC -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="destinatario_cc">Destinatario CC</label>
                              <textarea
                                class="form-control form-control-sm height-100"
                                name="destinatario_cc"
                                id="destinatario_cc"
                              ><?php echo htmlspecialchars(str_replace(";", "\r", $resultado_registros[0][5])); ?></textarea>
                            </div>
                          </div>
                          <!-- Botones -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <button
                                class="btn btn-success float-end ms-1"
                                type="submit"
                                name="guardar_registro"
                              >Guardar</button>
                              <?php if (isset($_POST["guardar_registro"])): ?>
                                <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                <button
                                  class="btn btn-danger float-end"
                                  type="button"
                                  onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');"
                                >Cancelar</button>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div><!-- row -->
                      </div><!-- card-body -->
                    </div><!-- card -->
                  </div><!-- col -->
                </div><!-- row -->
              </div><!-- col-lg-4 -->
            </div><!-- row justify-content-center -->
          </form>
        </div><!-- content-wrapper -->
      </div><!-- main-panel -->
    </div><!-- container-fluid -->
  </div><!-- container-scroller -->
  <?php require_once(ROOT . 'includes/_js.php'); ?>
</body>
</html>
