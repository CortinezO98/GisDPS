<?php
 //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Usuarios";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Usuarios | Crear";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $url_salir="familias_accion_usuarios?pagina=".$pagina."&id=".$filtro_permanente;
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  if(isset($_POST["guardar_registro"])){
      $documento_identidad=validar_input($_POST['documento_identidad']);
      $nombres_apellidos=validar_input($_POST['nombres_apellidos']);
      $usuario_acceso=validar_input($_POST['usuario_acceso']);
      $correo_corporativo=validar_input($_POST['correo_corporativo']);
      $fecha_ingreso=date('Y-m-d');
      $fecha_ingreso_area=date('Y-m-d');
      $fecha_nacimiento=date('Y-m-d');
      $genero='';
      $estado=validar_input($_POST['estado']);
      $usuario_red=$usuario_acceso;
      $ciudad='';
      $ubicacion='';
      $campania='';
      $cargo_rol=validar_input($_POST['cargo_rol']);
      $supervisor='';
      $piloto='';
      $foto='';
      $lider_calidad="";
      $inicio_sesion=0;

        $usu_modificacion_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
        $usu_modificacion_fecha=date('Y-m-d H:i:s');
        $usu_ultimo_acceso=date('Y-m-d H:i:s');

        $departamento=$_POST['departamento'];
        $municipio=$_POST['municipio'];

      if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']!=1){
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
                $_SESSION[APP_SESSION.'_registro_creado_usuario_fa']=1;
                
                // Prepara la sentencia
                $sentencia_insert_ciudad = $enlace_db->prepare("INSERT INTO `gestion_ocr_agentes`(`ocra_id`, `ocra_usuario`, `ocra_tipo`, `ocra_codigo`) VALUES (?,?,?,?)");

                // Agrega variables a sentencia preparada
                $sentencia_insert_ciudad->bind_param('ssss', $ocra_id, $ocra_usuario, $ocra_tipo, $ocra_codigo);
                
                $control_insert=0;
                $control_conteo=0;
                $control_insert_dpto=0;
                $departamento=array_values(array_unique($departamento));
                $municipio=array_values(array_unique($municipio));
                for ($i=0; $i < count($departamento); $i++) { 
                  if ($departamento[$i]!="") {
                    $control_conteo++;
                    $ocra_id=$documento_identidad.$departamento[$i];
                    $ocra_usuario=$documento_identidad;
                    $ocra_tipo='Departamento';
                    $ocra_codigo=$departamento[$i];
                    if ($sentencia_insert_ciudad->execute()) {
                        $control_insert++;
                        $control_insert_dpto++;
                    }
                  }
                }

                for ($i=0; $i < count($municipio); $i++) { 
                  if ($municipio[$i]!="") {
                    $control_conteo++;
                    $ocra_id=$documento_identidad.$municipio[$i];
                    $ocra_usuario=$documento_identidad;
                    $ocra_tipo='Municipio';
                    $ocra_codigo=$municipio[$i];
                    if ($sentencia_insert_ciudad->execute()) {
                        $control_insert++;
                    }
                  }
                }


                //Configura módulos de consulta FA
                $contador_insert_permisos=0;
                $id_modulo='10';
                $per_perfil='Usuario';
                $key_registro=$documento_identidad.$id_modulo;
                // Prepara la sentencia
                $sentencia_insert_permisos = $enlace_db->prepare("INSERT INTO `administrador_usuario_modulo_perfil`(`per_id`, `per_usuario`, `per_modulo`, `per_perfil`) VALUES (?,?,?,?)");
                // Agrega variables a sentencia preparada
                $sentencia_insert_permisos->bind_param('ssss', $key_registro, $documento_identidad, $id_modulo, $per_perfil);
                if ($sentencia_insert_permisos->execute()) {
                  $contador_insert_permisos++;
                }

                $id_modulo='12';
                $key_registro=$documento_identidad.$id_modulo;

                if ($sentencia_insert_permisos->execute()) {
                  $contador_insert_permisos++;
                }

                if ($control_insert_dpto>0) {
                    $id_modulo='18';
                    $key_registro=$documento_identidad.$id_modulo;

                    if ($sentencia_insert_permisos->execute()) {
                      $contador_insert_permisos++;
                    }
                }
                
                if ($control_insert==$control_conteo) {
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
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
            }
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro, usuario duplicado');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string_dpto="SELECT DISTINCT `ciu_cod_departamento`, `ciu_departamento` FROM `administrador_ciudades_dane` ORDER BY `ciu_departamento`";
  $consulta_registros_dpto = $enlace_db->prepare($consulta_string_dpto);
  $consulta_registros_dpto->execute();
  $resultado_registros_dpto = $consulta_registros_dpto->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_municipio="SELECT `ciu_cod_departamento`, `ciu_departamento`, `ciu_cod_municipio`, `ciu_municipio` FROM `administrador_ciudades_dane` ORDER BY `ciu_municipio`, `ciu_departamento`";
  $consulta_registros_municipio = $enlace_db->prepare($consulta_string_municipio);
  $consulta_registros_municipio->execute();
  $resultado_registros_municipio = $consulta_registros_municipio->get_result()->fetch_all(MYSQLI_NUM);
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
                            <div class="form-group">
                              <label for="documento_identidad">Documento identidad</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="documento_identidad" id="documento_identidad" maxlength="20" value="<?php if(isset($_POST["guardar_registro"])){ echo $documento_identidad; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="nombres_apellidos">Nombres y apellidos</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="nombres_apellidos" id="nombres_apellidos" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $nombres_apellidos; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                  <option value="Retirado" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Retirado"){ echo "selected"; } ?>>Retirado</option>
                                  <option value="Bloqueado" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Bloqueado"){ echo "selected"; } ?>>Bloqueado</option>
                                  <option value="Eliminado" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Eliminado"){ echo "selected"; } ?>>Eliminado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="usuario_acceso">Usuario acceso</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="usuario_acceso" id="usuario_acceso" maxlength="20" value="<?php if(isset($_POST["guardar_registro"])){ echo $usuario_acceso; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="correo_corporativo">Correo corporativo</label>
                              <input type="email" class="form-control form-control-sm font-size-11" name="correo_corporativo" id="correo_corporativo" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $correo_corporativo; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'readonly'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo_rol">Cargo/rol</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="cargo_rol" id="cargo_rol" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="AGENTE INSCRIPCIÓN FA CONSULTA" <?php if(isset($_POST["guardar_registro"]) AND $cargo_rol=="AGENTE INSCRIPCIÓN FA CONSULTA"){ echo "selected"; } ?>>AGENTE INSCRIPCIÓN FA CONSULTA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departamento">Departamento(s)</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $departamento[0]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $departamento[1]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $departamento[2]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $departamento[3]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $departamento[4]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="municipio">Municipio(s)</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $municipio[0]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $municipio[1]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $municipio[2]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $municipio[3]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1) { echo 'disabled'; } ?>>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $municipio[4]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']==1): ?>
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