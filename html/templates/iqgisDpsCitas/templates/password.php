<?php
  require_once("../iniciador_index.php");
  require_once("../security_session.php");
  //Si sesion esta iniciada se redirige al contenido, sino muestra index de logueo//
  if(isset($_SESSION[APP_SESSION.'_session_usu_id']) AND $_SESSION[APP_SESSION.'_session_usu_inicio_sesion']==1):
    header("Location:dashboard");
  elseif(!isset($_SESSION[APP_SESSION.'_session_usu_id'])):
    header("Location:login");
  else:
    if(isset($_POST["form_sing_in"])){
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
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="row">
                    <div class="row">
                    <div class="col-7 text-center">
                        <img src="<?php echo LOGO_ENTIDAD; ?>" class="img-fluid"/>
                    </div>
                    <div class="col-5 text-center">
                        <img src="<?php echo LOGO_CLIENTE; ?>" class="img-fluid"/>
                    </div>
                </div>
              <div class="brand-logo text-center"><h3><?php echo APP_NAME_LOGIN; ?></h3></div>
              <h4>Actualización de contraseña</h4>
              <?php if($_SESSION[APP_SESSION.'_session_password_recovery']!=1): ?>
                <h6 class="fw-light pt-2">Por favor ingresa una contraseña nueva.</h6>
              <?php endif; ?>

              <form class="pt-1" method="post" action="">
                <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                <?php if($_SESSION[APP_SESSION.'_session_password_recovery']!=1): ?>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" name="password_1" id="password" value="" placeholder="Contraseña" minlenght="8" maxlenght="15" autocomplete="off" autofocus required <?php if($_SESSION[APP_SESSION.'_session_password_recovery']==1) { echo 'readonly'; } ?> onkeyup="getPassword();">
                  </div>
                  <h6 class="fw-light pt-2">Confirmar contraseña.</h6>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" name="password_2" id="password_2" value="" placeholder="Contraseña" minlenght="8" maxlenght="15" autocomplete="off" required <?php if($_SESSION[APP_SESSION.'_session_password_recovery']==1) { echo 'readonly'; } ?>>
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
                <div class="mt-3">
                  <?php if($_SESSION[APP_SESSION.'_session_password_recovery']==1): ?>
                      <a href="logout" class="btn btn-dark float-end">Continuar</a>
                  <?php else: ?>
                      <button type="submit" name="form_sing_in" id="submit_btn" class="btn btn-success float-end ms-1">Actualizar Contraseña</button>
                      <a href="logout" class="btn btn-danger float-end">Cancelar</a>
                  <?php endif; ?>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript" src="<?php echo PLUGINS; ?>PWStrength-master/js/checkpw.js"></script>
</body>
</html>
<?php endif; ?>