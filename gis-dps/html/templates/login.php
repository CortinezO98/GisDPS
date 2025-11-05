<?php
  require_once(__DIR__ . '/../iniciador_index.php');
  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');
  unset($_SESSION[APP_SESSION.'_session_password_recovery']);
  //Si sesion esta iniciada se redirige al contenido, sino muestra index de logueo//
  if(isset($_SESSION[APP_SESSION.'_session_usu_id']) AND $_SESSION[APP_SESSION.'_session_usu_inicio_sesion']!=0):
    header("Location:dashboard");
  else:
    $consulta_string_parametros = "SELECT `ap_id`, `ap_parametro`, `ap_estado` FROM `administrador_plataforma`";
    $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
    $consulta_registros_parametros->execute();
    $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

    for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
      $parametros[$resultado_registros_parametros[$i][1]]=$resultado_registros_parametros[$i][2];
    }

    if(isset($_POST["form_sing_in"])){
      //obtiene variable usuario y contraseña
      $user=validar_input($_POST['user']);
      $password=validar_input($_POST['password']);
      
      $consulta_string = "SELECT `usu_id`, `usu_acceso`, `usu_contrasena`, `usu_nombres_apellidos`, `usu_correo_corporativo`, `usu_estado`, `usu_inicio_sesion`, `usu_foto`, `usu_cargo_rol` FROM `administrador_usuario` WHERE `usu_acceso`= ?";
      $consulta_registros = $enlace_db->prepare($consulta_string);
      $consulta_registros->bind_param("s", $user);
      $consulta_registros->execute();
      $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

      if (count($resultado_registros)>0) {
        if($user==$resultado_registros[0][1] AND $resultado_registros[0][5]=='Activo'){
          if (!isset($_COOKIE['intentos'])) {
            setcookie('intentos', 0, time() + 365 * 24 * 60 * 60);
          }
          setcookie('intentos', $_COOKIE['intentos']+1, time() + 365 * 24 * 60 * 60);
          if (crypt($password, $resultado_registros[0][2]) == $resultado_registros[0][2]) {
            if($parametros['login2factor']=='Activo') {
              // Prepara la sentencia
              $consulta_registros_token_validar = $enlace_db->prepare("SELECT `tk_id`, `tk_usuario`, `tk_tipo`, `tk_estado` FROM `administrador_usuario_token` WHERE `tk_tipo`='acceso' AND `tk_usuario`=? AND `tk_estado`='Activo' AND TIME_TO_SEC(TIMEDIFF(NOW(), `tk_registro_fecha`))<'180'");
              // Agrega variables a sentencia preparada
              $consulta_registros_token_validar->bind_param('s', $resultado_registros[0][0]);
              //ejecutar y obtener
              $consulta_registros_token_validar->execute();
              $resultado_registros_token_validar = $consulta_registros_token_validar->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros_token_validar)==0) {
                $token_hex   = random_int(0, 9).random_int(0, 9).random_int(0, 9).random_int(0, 9).random_int(0, 9).random_int(0, 9);
                // Prepara la sentencia
                $sentencia_insert_token = $enlace_db->prepare("INSERT INTO `administrador_usuario_token`(`tk_usuario`, `tk_tipo`, `tk_token`, `tk_estado`) VALUES (?, 'acceso',?,'Activo')");
                // Agrega variables a sentencia preparada
                $sentencia_insert_token->bind_param('ss', $resultado_registros[0][0], $token_hex);
                if ($sentencia_insert_token->execute()) {
                  
                } else {
                  $respuesta_accion = '<div class="alert alert-danger" role="alert"><button aria-label="" class="close" data-dismiss="alert"></button><strong>Error: </strong>¡Problemas al generar el token, por favor intente más tarde!</div>';
                }
              } elseif(count($resultado_registros_token_validar)>0) {
                  $_SESSION[APP_SESSION.'_validacion_token']=1;
                  registro_log($enlace_db, 'Token', 'token_sesion', 'Solicitud token', 'NULL', $resultado_registros[0][0]);
                  header("Location: validar_acceso.php?1=".base64_encode($resultado_registros[0][0])."&2=".base64_encode($resultado_registros[0][4])."");
              }
            } elseif($parametros['login2factor']=='Inactivo') {
              unset($_COOKIE['intentos']);
              setcookie('intentos', 0, 0);
              $_SESSION[APP_SESSION.'_session_usu_id']=$resultado_registros[0][0];
              $_SESSION[APP_SESSION.'_session_usu_acceso']=$resultado_registros[0][1];
              $_SESSION[APP_SESSION.'_session_usu_nombre_completo']=$resultado_registros[0][3];
              $_SESSION[APP_SESSION.'_session_usu_estado_usuario']=$resultado_registros[0][5];
              $_SESSION[APP_SESSION.'_session_usu_correo']=$resultado_registros[0][4];
              $_SESSION[APP_SESSION.'_session_usu_inicio_sesion']=$resultado_registros[0][6];
              $_SESSION[APP_SESSION.'_session_usu_foto']=$resultado_registros[0][7];
              $_SESSION[APP_SESSION.'_session_usu_cargo']=$resultado_registros[0][8];
              $_SESSION[APP_SESSION.'_session_cargo']=$resultado_registros[0][8];
              registro_log($enlace_db, 'Login', 'inicio_sesion', 'Inicio de sesión');
              // Prepra la sentencia
              $consulta_actualizar_ingreso = $enlace_db->prepare("UPDATE `administrador_usuario` SET `usu_ultimo_acceso`=? WHERE `usu_id`=?");
              // Agrega variables a sentencia preparada
              $consulta_actualizar_ingreso->bind_param("ss", date('Y-m-d H:i:s'), $resultado_registros[0][0]);
              // Ejecuta sentencia preparada
              $consulta_actualizar_ingreso->execute();

              $consulta_string_phistorial = "SELECT `auc_id`, `auc_usuario`, `auc_contrasena`, `auc_registro_fecha` FROM `administrador_usuario_contrasenas` WHERE `auc_usuario`=? ORDER BY `auc_registro_fecha` DESC LIMIT 1";
              $consulta_registros_phistorial = $enlace_db->prepare($consulta_string_phistorial);
              $consulta_registros_phistorial->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
              $consulta_registros_phistorial->execute();
              $resultado_registros_phistorial = $consulta_registros_phistorial->get_result()->fetch_all(MYSQLI_NUM);

              $fecha_control=date("Y-m-d", strtotime("+ 30 day", strtotime($resultado_registros_phistorial[0][3])));

              if (date('Y-m-d')>=$fecha_control) {
                $_SESSION[APP_SESSION.'_session_usu_inicio_sesion']=0;
              }

              if ($_SESSION[APP_SESSION.'_session_usu_inicio_sesion']==1) {
                  header("Location: dashboard");
              } else {
                  header("Location: password");
              }
            }
          } else {
            if ($_COOKIE['intentos']>=3) {
              // Prepra la sentencia
              $consulta_actualizar_estado = $enlace_db->prepare("UPDATE `administrador_usuario` SET `usu_estado`='Bloqueado' WHERE `usu_id`=?");
              // Agrega variables a sentencia preparada
              $consulta_actualizar_estado->bind_param("s", $resultado_registros[0][0]);
              // Ejecuta sentencia preparada
              $consulta_actualizar_estado->execute();
              registro_log($enlace_db, 'Login', 'bloqueo_usuario', 'Bloqueo de usuario por intentos erróneos', null, $resultado_registros[0][0]);
              unset($_COOKIE['intentos']);
              setcookie('intentos', 0, 0);
              $respuesta_accion = "<p class='alert alert-warning p-1 font-size-11'>¡El usuario ha sido bloqueado, por favor contacte al administrador!</p>";
            } else {
              $respuesta_accion = "<p class='alert alert-warning p-1 font-size-11'>¡Usuario y/o contraseña incorrectos, verifique e intente nuevamente!</p>";
            }
          }
        } else {
            $respuesta_accion = "<p class='alert alert-warning p-1 font-size-11'>¡Usuario y/o contraseña incorrectos, verifique e intente nuevamente!</p>";
        }
      } else {
        $respuesta_accion = "<p class='alert alert-warning p-1 font-size-11'>¡Usuario y/o contraseña incorrectos, verifique e intente nuevamente!</p>";
      }
    }
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="row">
                    <div class="col-4 text-center">
                        <img src="<?php echo LOGO_ENTIDAD; ?>" class="img-fluid"/>
                    </div>
                    <div class="col-8 text-center">
                        <img src="<?php echo LOGO_CLIENTE; ?>?v=1" class="img-fluid"/>
                    </div>
                </div>
              <div class="brand-logo text-center"><h3><?php echo APP_NAME_LOGIN; ?></h3></div>
              <h4 class="fw-light pt-2">Iniciar sesión</h4>
              <form class="pt-3" method="post" action="">
                <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" name="user" id="user" value="<?php if(isset($_POST["form_sing_in"])){ echo $user; } ?>" placeholder="Usuario" maxlenght="50" autocomplete="off" autofocus required>
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="password" id="password" value="" placeholder="Contraseña" maxlenght="20" autocomplete="off" required>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                    <button type="submit" name="form_sing_in" id="submit_btn" class="btn btn-primary btn-corp ">Iniciar Sesión</button>
                    <?php if($parametros['restorepassword']=='Activo'): ?>
                      <a href="#" class="auth-link text-black">¿Olvidaste tu contraseña?</a>
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
</body>
</html>
<?php endif; ?>
