<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Administrador";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Administrador";
  $subtitle = "Usuarios | Masivo";

  $consulta_string="SELECT `usu_id`, `usu_acceso`, `usu_contrasena`, `usu_nombres_apellidos`, `usu_correo_corporativo`, `usu_fecha_incorporacion`, `usu_campania`, `usu_usuario_red`, `usu_cargo_rol`, `usu_sede`, `usu_ciudad`, `usu_estado`, `usu_supervisor`, `usu_lider_calidad`, `usu_inicio_sesion`, `usu_piloto`, `usu_genero`, `usu_fecha_nacimiento`, `usu_fecha_ingreso_piloto` FROM `administrador_usuario` WHERE `usu_contrasena`=''";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros); $i++) { 
    $nombres_apellidos=$resultado_registros[$i][3];
    $usuario_acceso=$resultado_registros[$i][1];
    $correo_corporativo=$resultado_registros[$i][4];
    $nueva_contrasena=generatePassword(10);
    $salt = substr(base64_encode(openssl_random_pseudo_bytes('30')), 0, 22);
    $salt = strtr($salt, array('+' => '.'));
    $contrasena = crypt($nueva_contrasena, '$2y$10$' . $salt);
    $inicio_sesion=0;
    $usu_modificacion_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
    $usu_modificacion_fecha=date('Y-m-d H:i:s');

    // Prepra la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `administrador_usuario` SET `usu_contrasena`=?, `usu_inicio_sesion`=?, `usu_modificacion_usuario`=?, `usu_modificacion_fecha`=? WHERE `usu_id`=?");
    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param("sssss", $contrasena, $inicio_sesion, $usu_modificacion_usuario, $usu_modificacion_fecha, $resultado_registros[$i][0]);
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
            
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Contraseña reiniciada exitosamente');";
        // Prepara la sentencia
        $sentencia_insert_contrasena = $enlace_db->prepare("INSERT INTO `administrador_usuario_contrasenas`(`auc_usuario`, `auc_contrasena`) VALUES (?,?)");
        // Agrega variables a sentencia preparada
        $sentencia_insert_contrasena->bind_param('ss', $resultado_registros[$i][0], $contrasena);
        $sentencia_insert_contrasena->execute();
        registro_log($enlace_db, $modulo_plataforma, 'editar', 'Contraseña reseteada para usuario '.$resultado_registros[$i][0].'-'.$nombres_apellidos);

        //PROGRAMACIÓN NOTIFICACIÓN
        $asunto='Credenciales de acceso - '.APP_NAME.' | '.APP_NAME_ALL;
        $referencia='Credenciales de Acceso';
        $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>¡Hemos generado las siguientes credenciales de acceso!</p>
                <center>
                    <p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'><b>Nombres y Apellidos: ".$nombres_apellidos."</b></p>
                    <p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'><b>Usuario: ".$usuario_acceso."</b></p>
                    <p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'><b>Contraseña: ".$nueva_contrasena."</b></p>
                </center>";
        $nc_address=$correo_corporativo.";";
        $nc_cc='';
        notificacion($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
        registro_log($enlace_db, $modulo_plataforma, 'notificacion', 'Notificación de credenciales para usuario '.$resultado_registros[$i][0].'-'.$nombres_apellidos.' programada');
    } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al reiniciar contraseña');";
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
            <div class="col-lg-6 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      
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