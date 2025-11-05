<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Administrador";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "ADMINISTRADOR";
  $subtitle = "BUZONES <i class='fas fa-chevron-right'></i> EDITAR";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="buzones?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
    $tipo=validar_input($_POST['tipo']);
    $remitente=validar_input($_POST['remitente']);
    $nombre_remitente=validar_input($_POST['nombre_remitente']);
    $usuario=validar_input($_POST['usuario']);
    $contrasena=validar_input($_POST['contrasena']);
    $host=validar_input($_POST['host']);
    $puerto=validar_input($_POST['puerto']);
    $smtp_secure=validar_input($_POST['smtp_secure']);
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `administrador_buzones` SET `ncr_host`=?, `ncr_port`=?, `ncr_smtpsecure`=?, `ncr_username`=?, `ncr_password`=?, `ncr_setfrom`=?, `ncr_setfrom_name`=?, `ncr_tipo`=? WHERE `ncr_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sssssssss', $host, $puerto, $smtp_secure, $usuario, $contrasena, $remitente, $nombre_remitente, $tipo, $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
    
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

  $consulta_string="SELECT `ncr_id`, `ncr_host`, `ncr_port`, `ncr_smtpsecure`, `ncr_smtpauth`, `ncr_username`, `ncr_password`, `ncr_setfrom`, `ncr_setfrom_name`, `ncr_tipo` FROM `administrador_buzones` WHERE `ncr_id`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
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
                        <div class="col-md-12">
                          <div class="form-group">
                              <label for="tipo">Tipo</label>
                              <select class="form-control form-control-sm form-select font-size-11" name="tipo" id="tipo" required>
                                <option value="">Seleccione</option>
                                <option value="Envío" <?php if($resultado_registros[0][9]=="Envío"){ echo "selected"; } ?>>Envío</option>
                                <option value="Lectura" <?php if($resultado_registros[0][9]=="Lectura"){ echo "selected"; } ?>>Lectura</option>
                              </select>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="remitente">Cuenta buzón</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="remitente" id="remitente" maxlength="100" value="<?php echo $resultado_registros[0][7]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="nombre_remitente">Nombre buzón</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="nombre_remitente" id="nombre_remitente" maxlength="100" value="<?php echo $resultado_registros[0][8]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="usuario">Usuario</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="usuario" id="usuario" maxlength="100" value="<?php echo $resultado_registros[0][5]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="contrasena">Contraseña</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="contrasena" id="contrasena" maxlength="100" value="<?php echo $resultado_registros[0][6]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="host">Host</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="host" id="host" maxlength="100" value="<?php echo $resultado_registros[0][1]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="puerto">Puerto</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="puerto" id="puerto" maxlength="100" value="<?php echo $resultado_registros[0][2]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="smtp_secure">SMTP auth</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="smtp_secure" id="smtp_secure" maxlength="100" value="<?php echo $resultado_registros[0][3]; ?>" required>
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