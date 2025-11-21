<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Administrador";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "ADMINISTRADOR";
  $subtitle = "USUARIOS <i class='fas fa-chevron-right'></i> EDITAR";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
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
    $piloto=validar_input($_POST['piloto']);
    $lider_calidad="";
    $foto='avatar/'.strtolower($genero).'.jpg';

    $usu_modificacion_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
    $usu_modificacion_fecha=date('Y-m-d H:i:s');

    $modulo_permiso=$_POST['modulo_permiso'];
    $contador_insert=0;
    
    for ($i=0; $i < count($modulo_permiso); $i++) {
        $modulo_separado=explode("|", $modulo_permiso[$i]);
        $key_registro=$id_registro.$modulo_separado[0];
        
        if ($modulo_separado[1]=="") {
            // Prepara la sentencia
            $sentencia_insert = $enlace_db->prepare("DELETE FROM `administrador_usuario_modulo_perfil` WHERE `per_id`=?");

            // Agrega variables a sentencia preparada
            $sentencia_insert->bind_param('s', $key_registro);
        } else {
            // Prepara la sentencia
            $sentencia_insert = $enlace_db->prepare("INSERT INTO `administrador_usuario_modulo_perfil`(`per_id`, `per_usuario`, `per_modulo`, `per_perfil`) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE `per_perfil` = ?");

            // Agrega variables a sentencia preparada
            $sentencia_insert->bind_param('sssss', $key_registro, $id_registro, $modulo_separado[0], $modulo_separado[1], $modulo_separado[1]);
        }

        if ($sentencia_insert->execute()) {
          $contador_insert++;
        }
    }

    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `administrador_usuario` SET `usu_acceso`=?,`usu_nombres_apellidos`=?,`usu_correo_corporativo`=?,`usu_fecha_incorporacion`=?,`usu_campania`=?,`usu_usuario_red`=?,`usu_cargo_rol`=?,`usu_sede`=?,`usu_ciudad`=?,`usu_estado`=?,`usu_supervisor`=?,`usu_lider_calidad`=?, `usu_piloto`=?, `usu_genero`=?, `usu_fecha_nacimiento`=?, `usu_modificacion_usuario`=?, `usu_modificacion_fecha`=?, `usu_fecha_ingreso_piloto`=? WHERE `usu_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sssssssssssssssssss', $usuario_acceso, $nombres_apellidos, $correo_corporativo, $fecha_ingreso, $campania, $usuario_red, $cargo_rol, $ubicacion, $ciudad, $estado, $supervisor, $lider_calidad, $piloto, $genero, $fecha_nacimiento, $usu_modificacion_usuario, $usu_modificacion_fecha, $fecha_ingreso_area, $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
    
    if (comprobarSentencia($enlace_db->info) AND $contador_insert==count($modulo_permiso)) {
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

    $consulta_string_ciudad="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_departamento`, `ciu_municipio`";

    $consulta_registros_ciudad = $enlace_db->prepare($consulta_string_ciudad);
    $consulta_registros_ciudad->execute();
    $resultado_registros_ciudad = $consulta_registros_ciudad->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string="SELECT `usu_id`, `usu_acceso`, `usu_contrasena`, `usu_nombres_apellidos`, `usu_correo_corporativo`, `usu_fecha_incorporacion`, `usu_campania`, `usu_usuario_red`, `usu_cargo_rol`, `usu_sede`, `usu_ciudad`, `usu_estado`, `usu_supervisor`, `usu_lider_calidad`, `usu_inicio_sesion`, `usu_piloto`, `usu_genero`, `usu_fecha_nacimiento`, `usu_fecha_ingreso_piloto` FROM `administrador_usuario` WHERE `usu_id`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

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

    $consulta_string_modulos="SELECT `mod_id`, `mod_modulo_nombre` FROM `administrador_modulo` ORDER BY `mod_modulo_nombre`";
    $consulta_registros_modulos = $enlace_db->prepare($consulta_string_modulos);
    $consulta_registros_modulos->execute();
    $resultado_registros_modulos = $consulta_registros_modulos->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_permisos="SELECT `per_id`, `per_usuario`, `per_modulo`, `per_perfil` FROM `administrador_usuario_modulo_perfil` WHERE `per_usuario`=?";
    $consulta_registros_permisos = $enlace_db->prepare($consulta_string_permisos);
    $consulta_registros_permisos->bind_param("s", $id_registro);
    $consulta_registros_permisos->execute();
    $resultado_registros_permisos = $consulta_registros_permisos->get_result()->fetch_all(MYSQLI_NUM);

    for ($i=0; $i < count($resultado_registros_permisos); $i++) { 
        $array_permisos[$resultado_registros_permisos[$i][2]]=$resultado_registros_permisos[$i][3];
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
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="documento_identidad">Documento identidad</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="documento_identidad" id="documento_identidad" maxlength="20" value="<?php echo $resultado_registros[0][0]; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                              <label for="nombres_apellidos">Nombres y apellidos</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="nombres_apellidos" id="nombres_apellidos" maxlength="100" value="<?php echo $resultado_registros[0][3]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="genero">Género</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="genero" id="genero" required>
                                  <option value="">Seleccione</option>
                                  <option value="Sin definir" <?php if($resultado_registros[0][16]=="Sin definir"){ echo "selected"; } ?>>Sin definir</option>
                                  <option value="Mujer" <?php if($resultado_registros[0][16]=="Mujer"){ echo "selected"; } ?>>Mujer</option>
                                  <option value="Hombre" <?php if($resultado_registros[0][16]=="Hombre"){ echo "selected"; } ?>>Hombre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_nacimiento">Fecha nacimiento</label>
                              <input type="date" class="form-control form-control-sm font-size-11" name="fecha_nacimiento" id="fecha_nacimiento" maxlength="20" value="<?php echo $resultado_registros[0][17]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_ingreso">Fecha ingreso</label>
                              <input type="date" class="form-control form-control-sm font-size-11" name="fecha_ingreso" id="fecha_ingreso" maxlength="20" value="<?php echo $resultado_registros[0][5]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_ingreso_area">Fecha ingreso área</label>
                              <input type="date" class="form-control form-control-sm font-size-11" name="fecha_ingreso_area" id="fecha_ingreso_area" maxlength="20" value="<?php echo $resultado_registros[0][18]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="usuario_acceso">Usuario acceso</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="usuario_acceso" id="usuario_acceso" maxlength="20" value="<?php echo $resultado_registros[0][1]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="usuario_red">Usuario de red</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="usuario_red" id="usuario_red" maxlength="20" value="<?php echo $resultado_registros[0][7]; ?>" required>
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
                                <label for="ciudad">Ciudad</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="ciudad" id="ciudad" required>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_ciudad); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_ciudad[$i][0]; ?>" <?php if($resultado_registros[0][10]==$resultado_registros_ciudad[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_ciudad[$i][2].", ".$resultado_registros_ciudad[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ubicacion">Ubicación</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="ubicacion" id="ubicacion" required>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_ubicacion); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_ubicacion[$i][0]; ?>" <?php if($resultado_registros[0][9]==$resultado_registros_ubicacion[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_ubicacion[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="campania">Área</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="campania" id="campania" required>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_campania); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_campania[$i][0]; ?>" <?php if($resultado_registros[0][6]==$resultado_registros_campania[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_campania[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo_rol">Cargo/rol</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="cargo_rol" id="cargo_rol" required>
                                  <option value="">Seleccione</option>
                                  <option value="AGENTE GENERAL" <?php if($resultado_registros[0][8]=="AGENTE GENERAL"){ echo "selected"; } ?>>AGENTE GENERAL</option>
                                  <option value="AGENTE TÉCNICO" <?php if($resultado_registros[0][8]=="AGENTE TÉCNICO"){ echo "selected"; } ?>>AGENTE TÉCNICO</option>
                                  <option value="AGENTE ESPECIALIZADO" <?php if($resultado_registros[0][8]=="AGENTE ESPECIALIZADO"){ echo "selected"; } ?>>AGENTE ESPECIALIZADO</option>
                                  <option value="AGENTE ESPECIALIZADO MINERO DE DATOS" <?php if($resultado_registros[0][8]=="AGENTE ESPECIALIZADO MINERO DE DATOS"){ echo "selected"; } ?>>AGENTE ESPECIALIZADO MINERO DE DATOS</option>
                                  <option value="AGENTE GENERAL LENGUAJE DE SEÑAS" <?php if($resultado_registros[0][8]=="AGENTE GENERAL LENGUAJE DE SEÑAS"){ echo "selected"; } ?>>AGENTE GENERAL LENGUAJE DE SEÑAS</option>
                                  <option value="AGENTE INSCRIPCIÓN FA" <?php if($resultado_registros[0][8]=="AGENTE INSCRIPCIÓN FA"){ echo "selected"; } ?>>AGENTE INSCRIPCIÓN FA</option>
                                  <option value="AGENTE INSCRIPCIÓN FA CONSULTA" <?php if($resultado_registros[0][8]=="AGENTE INSCRIPCIÓN FA CONSULTA"){ echo "selected"; } ?>>AGENTE INSCRIPCIÓN FA CONSULTA</option>
                                  <option value="AGENTE DPS AGENDAMIENTO" <?php if($resultado_registros[0][8]=="AGENTE DPS AGENDAMIENTO"){ echo "selected"; } ?>>AGENTE DPS AGENDAMIENTO</option>
                                  <option value="AGENTE PROFESIONAL" <?php if($resultado_registros[0][8]=="AGENTE PROFESIONAL"){ echo "selected"; } ?>>AGENTE PROFESIONAL</option>
                                  <option value="AGENTE QUIOSCO" <?php if($resultado_registros[0][8]=="AGENTE QUIOSCO"){ echo "selected"; } ?>>AGENTE QUIOSCO</option>
                                  <option value="INTERPRETE" <?php if($resultado_registros[0][8]=="INTERPRETE"){ echo "selected"; } ?>>INTERPRETE</option>
                                  <option value="FORMADOR" <?php if($resultado_registros[0][8]=="FORMADOR"){ echo "selected"; } ?>>FORMADOR</option>
                                  <option value="COORDINADOR" <?php if($resultado_registros[0][8]=="COORDINADOR"){ echo "selected"; } ?>>COORDINADOR</option>
                                  <option value="LIDER DE CALIDAD" <?php if($resultado_registros[0][8]=="LIDER DE CALIDAD"){ echo "selected"; } ?>>LIDER DE CALIDAD</option>
                                  <option value="COORDINADOR NACIONAL" <?php if($resultado_registros[0][8]=="COORDINADOR NACIONAL"){ echo "selected"; } ?>>COORDINADOR NACIONAL</option>
                                  <option value="PROFESIONAL DE OPERACION ZONAL EN CAMPO" <?php if($resultado_registros[0][8]=="PROFESIONAL DE OPERACION ZONAL EN CAMPO"){ echo "selected"; } ?>>PROFESIONAL DE OPERACION ZONAL EN CAMPO</option>
                                  <option value="ADMINISTRADOR PLATAFORMA" <?php if($resultado_registros[0][8]=="ADMINISTRADOR PLATAFORMA"){ echo "selected"; } ?>>ADMINISTRADOR PLATAFORMA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supervisor">Responsable</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="supervisor" id="supervisor" required>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_supervisor); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_supervisor[$i][0]; ?>" <?php if($resultado_registros[0][12]==$resultado_registros_supervisor[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_supervisor[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12 mb-3">
                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th class="px-1 py-2">Módulo</th>
                                        <th class="px-1 py-2">Permiso</th>
                                    </tr>
                                </thead>
                                <tbody>    
                                    <?php for ($i=0; $i < count($resultado_registros_modulos); $i++): ?>
                                    <tr>
                                        <td class="p-1 font-size-11"><?php echo $resultado_registros_modulos[$i][1]; ?></td>
                                        <td class="p-1 font-size-11">
                                            <select class="form-control form-control-sm form-select font-size-11" name="modulo_permiso[]" id="modulo_permiso">
                                              <option value="<?php echo $resultado_registros_modulos[$i][0]; ?>|">Seleccione</option>
                                              <option value="<?php echo $resultado_registros_modulos[$i][0]; ?>|Visitante" <?php if($array_permisos[$resultado_registros_modulos[$i][0]]=="Visitante"){ echo "selected"; } ?>>Visitante</option>
                                              <option value="<?php echo $resultado_registros_modulos[$i][0]; ?>|Cliente" <?php if($array_permisos[$resultado_registros_modulos[$i][0]]=="Cliente"){ echo "selected"; } ?>>Cliente</option>
                                              <option value="<?php echo $resultado_registros_modulos[$i][0]; ?>|Usuario" <?php if($array_permisos[$resultado_registros_modulos[$i][0]]=="Usuario"){ echo "selected"; } ?>>Usuario</option>
                                              <option value="<?php echo $resultado_registros_modulos[$i][0]; ?>|Supervisor" <?php if($array_permisos[$resultado_registros_modulos[$i][0]]=="Supervisor"){ echo "selected"; } ?>>Supervisor</option>
                                              <option value="<?php echo $resultado_registros_modulos[$i][0]; ?>|Formador" <?php if($array_permisos[$resultado_registros_modulos[$i][0]]=="Formador"){ echo "selected"; } ?>>Formador</option>
                                              <option value="<?php echo $resultado_registros_modulos[$i][0]; ?>|Gestor" <?php if($array_permisos[$resultado_registros_modulos[$i][0]]=="Gestor"){ echo "selected"; } ?>>Gestor</option>
                                              <option value="<?php echo $resultado_registros_modulos[$i][0]; ?>|Administrador" <?php if($array_permisos[$resultado_registros_modulos[$i][0]]=="Administrador"){ echo "selected"; } ?>>Administrador</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
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