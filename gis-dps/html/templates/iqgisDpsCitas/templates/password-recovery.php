<?php
    require_once("../iniciador_index.php");
    require_once("../security_session.php");
    /*VARIABLES*/
    $title = "Administrador";
    $subtitle = "Mi Perfil | Cambiar Contraseña";
    $url_salir="perfil";

    if(isset($_POST["guardar_registro"])){
      $password_1=validar_input($_POST['password_1']);
      $password_2=validar_input($_POST['password_2']);

      if($_SESSION[APP_SESSION.'_session_password_recovery']!=1){
        if ($password_1==$password_2) {
            $estado_valida_password=1;
            if (!strlen($password_1)>=8) {
              $estado_valida_password=0;
            }

            if (!preg_match("/[0-9]/", $password_1)) {
              $estado_valida_password=0;
            }

            if (!preg_match("/[a-z]/", $password_1)) {
              $estado_valida_password=0;
            }

            if (!preg_match("/[A-Z]/", $password_1)) {
              $estado_valida_password=0;
            }

            if (!preg_match("/[~!#$%^*+=<>]/", $password_1)) {
              $estado_valida_password=0;
            }

            if ($estado_valida_password) {
              $consulta_string_phistorial = "SELECT `auc_id`, `auc_usuario`, `auc_contrasena`, `auc_registro_fecha` FROM `administrador_usuario_contrasenas` WHERE `auc_usuario`=? ORDER BY `auc_registro_fecha` DESC LIMIT 1";
              $consulta_registros_phistorial = $enlace_db->prepare($consulta_string_phistorial);
              $consulta_registros_phistorial->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
              $consulta_registros_phistorial->execute();
              $resultado_registros_phistorial = $consulta_registros_phistorial->get_result()->fetch_all(MYSQLI_NUM);

              if (crypt($password_1, $resultado_registros_phistorial[0][2]) == $resultado_registros_phistorial[0][2]) {
                $respuesta_accion = "<p class='alert alert-warning p-1 font-size-11'>¡Contraseña usada recientemente, verifique e intente nuevamente!</p>";
              } else {
                $salt = substr(base64_encode(openssl_random_pseudo_bytes('30')), 0, 22);
                $salt = strtr($salt, array('+' => '.'));
                $contrasena_guardar = crypt($password_1, '$2y$10$' . $salt);

                // Prepra la sentencia
                $consulta_actualizar_password = $enlace_db->prepare("UPDATE `administrador_usuario` SET `usu_contrasena`=?, `usu_inicio_sesion`='1' WHERE `usu_id`=?");
                // Agrega variables a sentencia preparada
                $consulta_actualizar_password->bind_param("ss", $contrasena_guardar, $_SESSION[APP_SESSION.'_session_usu_id']);
                // Ejecuta sentencia preparada
                $consulta_actualizar_password->execute();

                if (comprobarSentencia($enlace_db->info)) {
                    $respuesta_accion = "<p class='alert alert-success p-1 font-size-11'>¡Contraseña actualizada exitosamente!</p>";
                    // Prepara la sentencia
                    $sentencia_insert_contrasena = $enlace_db->prepare("INSERT INTO `administrador_usuario_contrasenas`(`auc_usuario`, `auc_contrasena`) VALUES (?,?)");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert_contrasena->bind_param('ss', $_SESSION[APP_SESSION.'_session_usu_id'], $contrasena_guardar);
                    $sentencia_insert_contrasena->execute();
                    registro_log($enlace_db, 'Login', 'editar', 'Contraseña actualizada para usuario '.$_SESSION[APP_SESSION.'_session_usu_id'].'-'.$_SESSION[APP_SESSION.'_session_usu_nombre_completo']);

                    //PROGRAMACIÓN NOTIFICACIÓN
                    $asunto='Cambio de Contraseña - '.APP_NAME.' | '.APP_NAME_ALL;
                    $referencia='Cambio de Contraseña';
                    $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>¡Se ha realizado actualización de contraseña!</p>
                            <center>
                                <p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'><b>Nombres y Apellidos: ".$_SESSION[APP_SESSION.'_session_usu_nombre_completo']."</b></p>
                            </center>";
                    $nc_address=$_SESSION[APP_SESSION.'_session_usu_correo'].";";
                    $nc_cc='';
                    notificacion($enlace_db, $asunto, $referencia, $contenido, $nc_address, 'Login', $nc_cc);
                    registro_log($enlace_db, 'Login', 'notificacion', 'Notificación de cambio de contraseña para usuario '.$_SESSION[APP_SESSION.'_session_usu_id'].'-'.$_SESSION[APP_SESSION.'_session_usu_nombre_completo'].' programada');
                    $_SESSION[APP_SESSION.'_session_password_recovery']=1;
                } else {
                    $respuesta_accion = "<p class='alert alert-warning p-1 font-size-11'>¡Problemas al actualizar la contraseña, verifique e intente nuevamente!</p>";
                }
              }
            } else {
              $respuesta_accion = "<p class='alert alert-warning p-1 font-size-11'>¡Contraseña no cumple requisitos mínimos de seguridad, verifique e intente nuevamente!</p>";
            }
        } else {
          $respuesta_accion = "<p class='alert alert-warning p-1 font-size-11'>¡Contraseña no coincide, verifique e intente nuevamente!</p>";
        }
      } else {
          $respuesta_accion = "<p class='alert alert-success p-1 font-size-11'>¡Contraseña actualizada exitosamente!</p>";
      }
    }
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <link rel="stylesheet" href="<?php echo PLUGINS; ?>PWStrength-master/css/styles.css" />
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
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <h4>Actualización de contraseña</h4>
                      <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                      <?php if($_SESSION[APP_SESSION.'_session_password_recovery']!=1): ?>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="password" class="my-0">Por favor ingresa una contraseña nueva</label>
                              <input type="password" class="form-control form-control-sm font-size-11" name="password_1" id="password" minlenght="8" maxlenght="15" value="" placeholder="Contraseña" <?php if($_SESSION[APP_SESSION.'_session_password_recovery']==1) { echo 'readonly'; } ?> onkeyup="getPassword();" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="password_2" class="my-0">Confirmar contraseña</label>
                              <input type="password" class="form-control form-control-sm font-size-11" name="password_2" id="password_2" minlenght="8" maxlenght="15" value="" placeholder="Contraseña" <?php if($_SESSION[APP_SESSION.'_session_password_recovery']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <h6 class="fw-light pt-2">Requisitos mínimos:</h6>
                        <ul class="lead list-group" id="requirements">
                          <li id="length" class="list-group-item font-size-11 py-1">8 caracteres</li>
                          <li id="lowercase" class="list-group-item font-size-11 py-1">1 letra minúscula</li>
                          <li id="uppercase" class="list-group-item font-size-11 py-1">1 letra mayúscula</li>
                          <li id="number" class="list-group-item font-size-11 py-1">1 número</li>
                          <li id="special" class="list-group-item font-size-11 py-1">1 caracter especial</li>
                        </ul>
                      <?php endif; ?>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_session_password_recovery']==1): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Actualizar Contraseña</button>
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
        <!-- footer -->
        <?php require_once(ROOT.'includes/_footer.php'); ?>
        <!-- footer -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript" src="<?php echo PLUGINS; ?>PWStrength-master/js/checkpw.js"></script>
</body>
</html>