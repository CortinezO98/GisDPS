<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Usuarios";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Usuarios | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="familias_accion_usuarios?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
    $nombres_apellidos=validar_input($_POST['nombres_apellidos']);
    $usuario_acceso=validar_input($_POST['usuario_acceso']);
    $correo_corporativo=validar_input($_POST['correo_corporativo']);
    $estado=validar_input($_POST['estado']);
    $cargo_rol=validar_input($_POST['cargo_rol']);

    $usu_modificacion_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
    $usu_modificacion_fecha=date('Y-m-d H:i:s');

    $departamento=$_POST['departamento'];
    $municipio=$_POST['municipio'];

    // Prepara la sentencia
    $sentencia_insert_ciudad = $enlace_db->prepare("INSERT INTO `gestion_ocr_agentes`(`ocra_id`, `ocra_usuario`, `ocra_tipo`, `ocra_codigo`) VALUES (?,?,?,?)");

    // Agrega variables a sentencia preparada
    $sentencia_insert_ciudad->bind_param('ssss', $ocra_id, $ocra_usuario, $ocra_tipo, $ocra_codigo);
    
    $control_insert=0;
    $control_conteo=0;
    $control_insert_dpto=0;
    $departamento=array_values(array_unique($departamento));
    $municipio=array_values(array_unique($municipio));

    // Prepara la sentencia
    $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_ocr_agentes` WHERE `ocra_usuario`=?");

    // Agrega variables a sentencia preparada
    $sentencia_delete->bind_param('s', $id_registro);
    $sentencia_delete->execute();
    for ($i=0; $i < count($departamento); $i++) { 
      if ($departamento[$i]!="") {
        $control_conteo++;
        $ocra_id=$id_registro.$departamento[$i];
        $ocra_usuario=$id_registro;
        $ocra_tipo='Departamento';
        $ocra_codigo=$departamento[$i];
        if ($sentencia_insert_ciudad->execute()) {
            $control_insert_dpto++;
            $control_insert++;
        }
      }
    }

    for ($i=0; $i < count($municipio); $i++) { 
      if ($municipio[$i]!="") {
        $control_conteo++;
        $ocra_id=$id_registro.$municipio[$i];
        $ocra_usuario=$id_registro;
        $ocra_tipo='Municipio';
        $ocra_codigo=$municipio[$i];
        if ($sentencia_insert_ciudad->execute()) {
            $control_insert++;
        }
      }
    }

    // Prepara la sentencia
    $sentencia_delete_permisos = $enlace_db->prepare("DELETE FROM `administrador_usuario_modulo_perfil` WHERE `per_usuario`=?");

    // Agrega variables a sentencia preparada
    $sentencia_delete_permisos->bind_param('s', $id_registro);
    $sentencia_delete_permisos->execute();

    //Configura módulos de consulta FA
    $contador_insert_permisos=0;
    $id_modulo='10';
    $per_perfil='Usuario';
    $key_registro=$id_registro.$id_modulo;
    // Prepara la sentencia
    $sentencia_insert_permisos = $enlace_db->prepare("INSERT INTO `administrador_usuario_modulo_perfil`(`per_id`, `per_usuario`, `per_modulo`, `per_perfil`) VALUES (?,?,?,?)");
    // Agrega variables a sentencia preparada
    $sentencia_insert_permisos->bind_param('ssss', $key_registro, $id_registro, $id_modulo, $per_perfil);
    if ($sentencia_insert_permisos->execute()) {
      $contador_insert_permisos++;
    }

    $id_modulo='12';
    $key_registro=$id_registro.$id_modulo;

    if ($sentencia_insert_permisos->execute()) {
      $contador_insert_permisos++;
    }

    if ($control_insert_dpto>0) {
        $id_modulo='18';
        $key_registro=$id_registro.$id_modulo;

        if ($sentencia_insert_permisos->execute()) {
          $contador_insert_permisos++;
        }
    }
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `administrador_usuario` SET `usu_acceso`=?,`usu_nombres_apellidos`=?,`usu_correo_corporativo`=?,`usu_cargo_rol`=?,`usu_estado`=?, `usu_modificacion_usuario`=?, `usu_modificacion_fecha`=? WHERE `usu_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('ssssssss', $usuario_acceso, $nombres_apellidos, $correo_corporativo, $cargo_rol, $estado, $usu_modificacion_usuario, $usu_modificacion_fecha, $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
    
    if (comprobarSentencia($enlace_db->info) AND $control_insert==$control_conteo) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
        registro_log($enlace_db, $modulo_plataforma, 'editar', 'Registro editado para usuario '.$id_registro.'-'.$nombres_apellidos);
    } else {
      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

    if(isset($_POST["reset_contrasena"])){
        $nombres_apellidos=validar_input($_POST['nombres_apellidos']);
        $usuario_acceso=validar_input($_POST['usuario_acceso']);
        $correo_corporativo=validar_input($_POST['correo_corporativo']);
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
        $consulta_actualizar->bind_param("sssss", $contrasena, $inicio_sesion, $usu_modificacion_usuario, $usu_modificacion_fecha, $id_registro);
        // Ejecuta sentencia preparada
        $consulta_actualizar->execute();
                
        if (comprobarSentencia($enlace_db->info)) {
            $respuesta_accion = "alertButton('success', 'Registro editado', 'Contraseña reiniciada exitosamente');";
            // Prepara la sentencia
            $sentencia_insert_contrasena = $enlace_db->prepare("INSERT INTO `administrador_usuario_contrasenas`(`auc_usuario`, `auc_contrasena`) VALUES (?,?)");
            // Agrega variables a sentencia preparada
            $sentencia_insert_contrasena->bind_param('ss', $id_registro, $contrasena);
            $sentencia_insert_contrasena->execute();
            registro_log($enlace_db, $modulo_plataforma, 'editar', 'Contraseña reseteada para usuario '.$id_registro.'-'.$nombres_apellidos);

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
            registro_log($enlace_db, $modulo_plataforma, 'notificacion', 'Notificación de credenciales para usuario '.$id_registro.'-'.$nombres_apellidos.' programada');
        } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al reiniciar contraseña');";
        }
    }

    $consulta_string="SELECT `usu_id`, `usu_acceso`, `usu_contrasena`, `usu_nombres_apellidos`, `usu_correo_corporativo`, `usu_fecha_incorporacion`, `usu_campania`, `usu_usuario_red`, `usu_cargo_rol`, `usu_sede`, `usu_ciudad`, `usu_estado`, `usu_supervisor`, `usu_lider_calidad`, `usu_inicio_sesion`, `usu_piloto`, `usu_genero`, `usu_fecha_nacimiento`, `usu_fecha_ingreso_piloto` FROM `administrador_usuario` WHERE `usu_id`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_dpto="SELECT DISTINCT `ciu_cod_departamento`, `ciu_departamento` FROM `administrador_ciudades_dane` ORDER BY `ciu_departamento`";
    $consulta_registros_dpto = $enlace_db->prepare($consulta_string_dpto);
    $consulta_registros_dpto->execute();
    $resultado_registros_dpto = $consulta_registros_dpto->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_municipio="SELECT `ciu_cod_departamento`, `ciu_departamento`, `ciu_cod_municipio`, `ciu_municipio` FROM `administrador_ciudades_dane` ORDER BY `ciu_municipio`, `ciu_departamento`";
    $consulta_registros_municipio = $enlace_db->prepare($consulta_string_municipio);
    $consulta_registros_municipio->execute();
    $resultado_registros_municipio = $consulta_registros_municipio->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_ciudad_usuario="SELECT DISTINCT `ocra_id`, `ocra_usuario`, `ocra_tipo`, `ocra_codigo`, TD.`ciu_cod_departamento`, TM.`ciu_cod_municipio` FROM `gestion_ocr_agentes` LEFT JOIN `administrador_ciudades_dane` AS TD ON `gestion_ocr_agentes`.`ocra_codigo`=TD.`ciu_cod_departamento` LEFT JOIN `administrador_ciudades_dane` AS TM ON `gestion_ocr_agentes`.`ocra_codigo`=TM.`ciu_cod_municipio` WHERE `ocra_usuario`=?";
    $consulta_registros_ciudad_usuario = $enlace_db->prepare($consulta_string_ciudad_usuario);
    $consulta_registros_ciudad_usuario->bind_param("s", $id_registro);
    $consulta_registros_ciudad_usuario->execute();
    $resultado_registros_ciudad_usuario = $consulta_registros_ciudad_usuario->get_result()->fetch_all(MYSQLI_NUM);

    for ($i=0; $i < count($resultado_registros_ciudad_usuario); $i++) { 
      if ($resultado_registros_ciudad_usuario[$i][2]=='Municipio') {
        $array_usuario_ciudad[$resultado_registros_ciudad_usuario[$i][1]][$resultado_registros_ciudad_usuario[$i][2]][]=$resultado_registros_ciudad_usuario[$i][5];
      } elseif ($resultado_registros_ciudad_usuario[$i][2]=='Departamento') {
        $array_usuario_ciudad[$resultado_registros_ciudad_usuario[$i][1]][$resultado_registros_ciudad_usuario[$i][2]][]=$resultado_registros_ciudad_usuario[$i][4];
      }
    }

    if (!isset($array_usuario_ciudad[$id_registro]['Departamento'])) {
      $array_usuario_ciudad[$id_registro]['Departamento']=array();
    }

    if (!isset($array_usuario_ciudad[$id_registro]['Municipio'])) {
      $array_usuario_ciudad[$id_registro]['Municipio']=array();
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
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="documento_identidad">Documento identidad</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="documento_identidad" id="documento_identidad" maxlength="20" value="<?php echo $resultado_registros[0][0]; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="nombres_apellidos">Nombres y apellidos</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="nombres_apellidos" id="nombres_apellidos" maxlength="100" value="<?php echo $resultado_registros[0][3]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][11]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][11]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                  <option value="Retirado" <?php if($resultado_registros[0][11]=="Retirado"){ echo "selected"; } ?>>Retirado</option>
                                  <option value="Bloqueado" <?php if($resultado_registros[0][11]=="Bloqueado"){ echo "selected"; } ?>>Bloqueado</option>
                                  <option value="Eliminado" <?php if($resultado_registros[0][11]=="Eliminado"){ echo "selected"; } ?>>Eliminado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="usuario_acceso">Usuario acceso</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="usuario_acceso" id="usuario_acceso" maxlength="20" value="<?php echo $resultado_registros[0][1]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="correo_corporativo">Correo corporativo</label>
                              <input type="email" class="form-control form-control-sm font-size-11" name="correo_corporativo" id="correo_corporativo" maxlength="100" value="<?php echo $resultado_registros[0][4]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo_rol">Cargo/rol</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="cargo_rol" id="cargo_rol" required>
                                  <option value="">Seleccione</option>
                                  <option value="AGENTE INSCRIPCIÓN FA CONSULTA" <?php if($resultado_registros[0][8]=="AGENTE INSCRIPCIÓN FA CONSULTA"){ echo "selected"; } ?>>AGENTE INSCRIPCIÓN FA CONSULTA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departamento">Departamento(s)</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Departamento'][0]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Departamento'][1]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Departamento'][2]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Departamento'][3]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="departamento[]" id="departamento">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_dpto); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_dpto[$i][0]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Departamento'][4]==$resultado_registros_dpto[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_dpto[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="municipio">Municipio(s)</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Municipio'][0]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Municipio'][1]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Municipio'][2]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Municipio'][3]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-control form-control-sm form-select font-size-11" name="municipio[]" id="municipio">
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_municipio[$i][2]; ?>" <?php if($array_usuario_ciudad[$id_registro]['Municipio'][4]==$resultado_registros_municipio[$i][2]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][3].", ".$resultado_registros_municipio[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Guardar</button>
                                <button class="btn btn-warning float-end ms-1" type="submit" name="reset_contrasena">Reset contraseña</button>
                                <?php if(isset($_POST["guardar_registro"]) OR isset($_POST["reset_contrasena"])): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php endif; ?>
                                <?php if(!isset($_POST["guardar_registro"]) AND !isset($_POST["reset_contrasena"])): ?>
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