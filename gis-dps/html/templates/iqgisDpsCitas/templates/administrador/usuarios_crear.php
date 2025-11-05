<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Administrador";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "ADMINISTRADOR";
  $subtitle = "USUARIOS <i class='fas fa-chevron-right'></i> CREAR";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $url_salir="usuarios?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
      $documento_identidad=validar_input($_POST['documento_identidad']);
      $nombres_apellidos=validar_input($_POST['nombres_apellidos']);
      $usuario_acceso=validar_input($_POST['usuario_acceso']);
      $correo_corporativo=validar_input($_POST['correo_corporativo']);
      $fecha_ingreso=validar_input($_POST['fecha_ingreso']);
      $fecha_ingreso_area=validar_input($_POST['fecha_ingreso_area']);
      $fecha_nacimiento=validar_input($_POST['fecha_nacimiento']);
      $genero=validar_input($_POST['genero']);
      $estado=validar_input($_POST['estado']);
      $usuario_red=validar_input($_POST['usuario_red']);
      $ciudad=validar_input($_POST['ciudad']);
      $ubicacion=validar_input($_POST['ubicacion']);
      $campania=validar_input($_POST['campania']);
      $cargo_rol=validar_input($_POST['cargo_rol']);
      $supervisor=validar_input($_POST['supervisor']);
      $piloto='';
      $foto='avatar/'.strtolower($genero).'.jpg';
      $lider_calidad="";
      $inicio_sesion=0;

        $usu_modificacion_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
        $usu_modificacion_fecha=date('Y-m-d H:i:s');
        $usu_ultimo_acceso=date('Y-m-d H:i:s');

      if($_SESSION[APP_SESSION.'_registro_creado_usuario']!=1){
          $nueva_contrasena=generatePassword(10);
          $salt = substr(base64_encode(openssl_random_pseudo_bytes('30')), 0, 22);
          $salt = strtr($salt, array('+' => '.'));
          $contrasena = crypt($nueva_contrasena, '$2y$10$' . $salt);

          $consulta_duplicado="SELECT COUNT(`usu_id`) FROM `administrador_usuario` WHERE `usu_id`=? OR `usu_acceso`=?";
          $consulta_registros_duplicados = $enlace_db->prepare($consulta_duplicado);
          $consulta_registros_duplicados->bind_param("ss", $documento_identidad, $usuario_acceso);
          $consulta_registros_duplicados->execute();
          $resultado_registros_duplicados = $consulta_registros_duplicados->get_result()->fetch_all(MYSQLI_NUM);

          if ($resultado_registros_duplicados[0][0]==0) {
            // Prepara la sentencia
            $sentencia_insert = $enlace_db->prepare("INSERT INTO `administrador_usuario`(`usu_id`, `usu_acceso`, `usu_contrasena`, `usu_nombres_apellidos`, `usu_correo_corporativo`, `usu_fecha_incorporacion`, `usu_campania`, `usu_usuario_red`, `usu_cargo_rol`, `usu_sede`, `usu_ciudad`, `usu_estado`, `usu_supervisor`, `usu_lider_calidad`, `usu_inicio_sesion`, `usu_piloto`, `usu_fecha_ingreso_piloto`, `usu_foto`, `usu_genero`, `usu_fecha_nacimiento`, `usu_modificacion_usuario`, `usu_modificacion_fecha`, `usu_ultimo_acceso`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

            // Agrega variables a sentencia preparada
            $sentencia_insert->bind_param('sssssssssssssssssssssss', $documento_identidad, $usuario_acceso, $contrasena, $nombres_apellidos, $correo_corporativo, $fecha_ingreso, $campania, $usuario_red, $cargo_rol, $ubicacion, $ciudad, $estado, $supervisor, $lider_calidad, $inicio_sesion, $piloto, $fecha_ingreso_area, $foto, $genero, $fecha_nacimiento, $usu_modificacion_usuario, $usu_modificacion_fecha, $usu_ultimo_acceso);
            
            if ($sentencia_insert->execute()) {
                $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
                $_SESSION[APP_SESSION.'_registro_creado_usuario']=1;
                registro_log($enlace_db, $modulo_plataforma, 'crear', 'Creación de usuario '.$documento_identidad.'-'.$nombres_apellidos);

                // Prepara la sentencia
                $sentencia_insert_contrasena = $enlace_db->prepare("INSERT INTO `administrador_usuario_contrasenas`(`auc_usuario`, `auc_contrasena`) VALUES (?,?)");
                // Agrega variables a sentencia preparada
                $sentencia_insert_contrasena->bind_param('ss', $documento_identidad, $contrasena);
                $sentencia_insert_contrasena->execute();
                

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
                registro_log($enlace_db, $modulo_plataforma, 'notificacion', 'Notificación de credenciales para usuario '.$documento_identidad.'-'.$nombres_apellidos.' programada');

            } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
            }
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro, usuario duplicado');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string_ciudad="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_departamento`, `ciu_municipio`";
  $consulta_registros_ciudad = $enlace_db->prepare($consulta_string_ciudad);
  $consulta_registros_ciudad->execute();
  $resultado_registros_ciudad = $consulta_registros_ciudad->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_supervisor="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE 1=1 ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_supervisor = $enlace_db->prepare($consulta_string_supervisor);
  $consulta_registros_supervisor->execute();
  $resultado_registros_supervisor = $consulta_registros_supervisor->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_calidad="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_cargo_rol`='Líder de calidad y formación' OR `usu_cargo_rol`='Sistema' ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_calidad = $enlace_db->prepare($consulta_string_calidad);
  $consulta_registros_calidad->execute();
  $resultado_registros_calidad = $consulta_registros_calidad->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_ubicacion="SELECT `au_id`, `au_nombre_ubicacion`, `au_observaciones` FROM `administrador_ubicacion` ORDER BY `au_nombre_ubicacion`";
  $consulta_registros_ubicacion = $enlace_db->prepare($consulta_string_ubicacion);
  $consulta_registros_ubicacion->execute();
  $resultado_registros_ubicacion = $consulta_registros_ubicacion->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_campania="SELECT `ac_id`, `ac_nombre_campania`, `ac_observaciones` FROM `administrador_campania` ORDER BY `ac_nombre_campania`";
  $consulta_registros_campania = $enlace_db->prepare($consulta_string_campania);
  $consulta_registros_campania->execute();
  $resultado_registros_campania = $consulta_registros_campania->get_result()->fetch_all(MYSQLI_NUM);
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
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="documento_identidad">Documento identidad</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="documento_identidad" id="documento_identidad" maxlength="20" value="<?php if(isset($_POST["guardar_registro"])){ echo $documento_identidad; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                              <label for="nombres_apellidos">Nombres y apellidos</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="nombres_apellidos" id="nombres_apellidos" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $nombres_apellidos; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="genero">Género</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="genero" id="genero" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="Sin definir" <?php if(isset($_POST["guardar_registro"]) AND $genero=="Sin definir"){ echo "selected"; } ?>>Sin definir</option>
                                  <option value="Mujer" <?php if(isset($_POST["guardar_registro"]) AND $genero=="Mujer"){ echo "selected"; } ?>>Mujer</option>
                                  <option value="Hombre" <?php if(isset($_POST["guardar_registro"]) AND $genero=="Hombre"){ echo "selected"; } ?>>Hombre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_nacimiento">Fecha nacimiento</label>
                              <input type="date" class="form-control form-control-sm font-size-11" name="fecha_nacimiento" id="fecha_nacimiento" maxlength="20" value="<?php if(isset($_POST["guardar_registro"])){ echo $fecha_nacimiento; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_ingreso">Fecha ingreso</label>
                              <input type="date" class="form-control form-control-sm font-size-11" name="fecha_ingreso" id="fecha_ingreso" maxlength="20" value="<?php if(isset($_POST["guardar_registro"])){ echo $fecha_ingreso; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_ingreso_area">Fecha ingreso área</label>
                              <input type="date" class="form-control form-control-sm font-size-11" name="fecha_ingreso_area" id="fecha_ingreso_area" maxlength="20" value="<?php if(isset($_POST["guardar_registro"])){ echo $fecha_ingreso_area; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                  <option value="Retirado" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Retirado"){ echo "selected"; } ?>>Retirado</option>
                                  <option value="Bloqueado" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Bloqueado"){ echo "selected"; } ?>>Bloqueado</option>
                                  <option value="Eliminado" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Eliminado"){ echo "selected"; } ?>>Eliminado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="usuario_acceso">Usuario acceso</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="usuario_acceso" id="usuario_acceso" maxlength="20" value="<?php if(isset($_POST["guardar_registro"])){ echo $usuario_acceso; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="usuario_red">Usuario de red</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="usuario_red" id="usuario_red" maxlength="20" value="<?php if(isset($_POST["guardar_registro"])){ echo $usuario_red; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="correo_corporativo">Correo corporativo</label>
                              <input type="email" class="form-control form-control-sm font-size-11" name="correo_corporativo" id="correo_corporativo" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $correo_corporativo; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ciudad">Ciudad</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="ciudad" id="ciudad" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'disabled'; } ?> required>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_ciudad); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_ciudad[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $ciudad==$resultado_registros_ciudad[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_ciudad[$i][2].", ".$resultado_registros_ciudad[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ubicacion">Ubicación</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="ubicacion" id="ubicacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'disabled'; } ?> required>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_ubicacion); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_ubicacion[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $ubicacion==$resultado_registros_ubicacion[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_ubicacion[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="campania">Área</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="campania" id="campania" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'disabled'; } ?> required>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_campania); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_campania[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $campania==$resultado_registros_campania[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_campania[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo_rol">Cargo/rol</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="cargo_rol" id="cargo_rol" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="AGENTE GENERAL" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE GENERAL"){ echo "selected"; } ?>>AGENTE GENERAL</option>
                                  <option value="AGENTE TÉCNICO" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE TÉCNICO"){ echo "selected"; } ?>>AGENTE TÉCNICO</option>
                                  <option value="AGENTE ESPECIALIZADO" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE ESPECIALIZADO"){ echo "selected"; } ?>>AGENTE ESPECIALIZADO</option>
                                  <option value="AGENTE ESPECIALIZADO MINERO DE DATOS" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE ESPECIALIZADO MINERO DE DATOS"){ echo "selected"; } ?>>AGENTE ESPECIALIZADO MINERO DE DATOS</option>
                                  <option value="AGENTE GENERAL LENGUAJE DE SEÑAS" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE GENERAL LENGUAJE DE SEÑAS"){ echo "selected"; } ?>>AGENTE GENERAL LENGUAJE DE SEÑAS</option>
                                  <option value="AGENTE INSCRIPCIÓN FA" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE INSCRIPCIÓN FA"){ echo "selected"; } ?>>AGENTE INSCRIPCIÓN FA</option>
                                  <option value="AGENTE INSCRIPCIÓN FA CONSULTA" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE INSCRIPCIÓN FA CONSULTA"){ echo "selected"; } ?>>AGENTE INSCRIPCIÓN FA CONSULTA</option>
                                  <option value="AGENTE DPS AGENDAMIENTO" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE DPS AGENDAMIENTO"){ echo "selected"; } ?>>AGENTE DPS AGENDAMIENTO</option>
                                  <option value="AGENTE PROFESIONAL" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE PROFESIONAL"){ echo "selected"; } ?>>AGENTE PROFESIONAL</option>
                                  <option value="AGENTE QUIOSCO" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE QUIOSCO"){ echo "selected"; } ?>>AGENTE QUIOSCO</option>
                                  <option value="INTERPRETE" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="INTERPRETE"){ echo "selected"; } ?>>INTERPRETE</option>
                                  <option value="FORMADOR" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="FORMADOR"){ echo "selected"; } ?>>FORMADOR</option>
                                  <option value="COORDINADOR" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="COORDINADOR"){ echo "selected"; } ?>>COORDINADOR</option>
                                  <option value="LIDER DE CALIDAD" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="LIDER DE CALIDAD"){ echo "selected"; } ?>>LIDER DE CALIDAD</option>
                                  <option value="COORDINADOR NACIONAL" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="COORDINADOR NACIONAL"){ echo "selected"; } ?>>COORDINADOR NACIONAL</option>
                                  <option value="PROFESIONAL DE OPERACION ZONAL EN CAMPO" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="PROFESIONAL DE OPERACION ZONAL EN CAMPO"){ echo "selected"; } ?>>PROFESIONAL DE OPERACION ZONAL EN CAMPO</option>
                                  <option value="ADMINISTRADOR PLATAFORMA" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="ADMINISTRADOR PLATAFORMA"){ echo "selected"; } ?>>ADMINISTRADOR PLATAFORMA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supervisor">Responsable</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="supervisor" id="supervisor" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1) { echo 'disabled'; } ?> required>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_supervisor); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_supervisor[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $supervisor==$resultado_registros_supervisor[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_supervisor[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario']==1): ?>
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