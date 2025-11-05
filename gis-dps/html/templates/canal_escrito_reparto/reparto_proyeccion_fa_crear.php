<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Reparto";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Reparto | 5. Proyección FA | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="reparto_proyeccion_fa?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='proyeccion_fa' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_campania` FROM `administrador_usuario` LEFT JOIN `administrador_campania` AS TC ON `administrador_usuario`.`usu_campania`=TC.`ac_id` WHERE `usu_estado`='Activo' AND TC.`ac_nombre_campania`='Canal Escrito' AND `usu_cargo_rol`='AGENTE ESPECIALIZADO' ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_analistas); $i++) { 
    $array_analista[$resultado_registros_analistas[$i][0]]=$resultado_registros_analistas[$i][1];
  }

  $consulta_string_departamentos="SELECT `ciu_codigo`, `ciu_departamento` FROM `administrador_departamentos` ORDER BY `ciu_departamento`";
  $consulta_registros_departamentos = $enlace_db->prepare($consulta_string_departamentos);
  $consulta_registros_departamentos->execute();
  $resultado_registros_departamentos = $consulta_registros_departamentos->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_departamentos); $i++) { 
    $array_departamento[$resultado_registros_departamentos[$i][0]]=$resultado_registros_departamentos[$i][1];
  }

  if(isset($_POST["guardar_registro"])){
      $cepfa_radicado_entrada=validar_input($_POST['cepfa_radicado_entrada']);
      $cepfa_abogado_aprobador=validar_input($_POST['cepfa_abogado_aprobador']);
      $cepfa_documento_identidad=validar_input($_POST['cepfa_documento_identidad']);
      $cepfa_nombre_ciudadano=validar_input($_POST['cepfa_nombre_ciudadano']);
      $cepfa_correo_direccion=validar_input($_POST['cepfa_correo_direccion']);
      $cepfa_departamento=validar_input($_POST['cepfa_departamento']);
      $cepfa_solicitud_novedad=validar_input($_POST['cepfa_solicitud_novedad']);
      $cepfa_observaciones=validar_input($_POST['cepfa_observaciones']);
      $cepfa_notificar=validar_input($_POST['notificar']);
      $cepfa_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cerep_proyeccion_fa`(`cepfa_radicado_entrada`, `cepfa_abogado_aprobador`, `cepfa_documento_identidad`, `cepfa_nombre_ciudadano`, `cepfa_correo_direccion`, `cepfa_departamento`, `cepfa_solicitud_novedad`, `cepfa_observaciones`, `cepfa_notificar`, `cepfa_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssss', $cepfa_radicado_entrada, $cepfa_abogado_aprobador, $cepfa_documento_identidad, $cepfa_nombre_ciudadano, $cepfa_correo_direccion, $cepfa_departamento, $cepfa_solicitud_novedad, $cepfa_observaciones, $cepfa_notificar, $cepfa_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cepfa_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='5. Proyección FA - Reparto | Canal Escrito';
                $referencia='5. Proyección FA - Reparto | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado entrada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cepfa_radicado_entrada."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Abogado aprobador</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_analista[$cepfa_abogado_aprobador]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Documento identidad (Si no hay número colocar NO REGISTRA)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cepfa_documento_identidad."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nombre del ciudadano (Si no hay nombre colocar CIUDADANO)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cepfa_nombre_ciudadano."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Correo/dirección de notificación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cepfa_correo_direccion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Departamento</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_departamento[$cepfa_departamento]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Solicitud/novedad de radicado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['solicitud']['texto'][$cepfa_solicitud_novedad]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cepfa_observaciones."</td>
                    </tr>
                    
                    <tr>
                        <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Registrado por</td>
                        <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$_SESSION[APP_SESSION.'_session_usu_nombre_completo']."</td>
                    </tr>
                    <tr>
                        <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha registro</td>
                        <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". date('d/m/Y H:i:s') ."</td>
                    </tr>
                  </table>";
                $nc_address=$_SESSION[APP_SESSION.'_session_usu_correo'].";";
                $nc_cc='';
                notificacion($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
              }
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
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
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body row">
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cepfa_radicado_entrada" class="my-0">Radicado entrada</label>
                            <input type="text" class="form-control form-control-sm" name="cepfa_radicado_entrada" id="cepfa_radicado_entrada" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $cepfa_radicado_entrada; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cepfa_abogado_aprobador" class="my-0">Abogado aprobador</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cepfa_abogado_aprobador" id="cepfa_abogado_aprobador" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($cepfa_abogado_aprobador==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cepfa_documento_identidad" class="my-0">Documento identidad (Si no hay número colocar NO REGISTRA)</label>
                            <input type="text" class="form-control form-control-sm" name="cepfa_documento_identidad" id="cepfa_documento_identidad" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $cepfa_documento_identidad; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cepfa_nombre_ciudadano" class="my-0">Nombre del ciudadano (Si no hay nombre colocar CIUDADANO)</label>
                            <input type="text" class="form-control form-control-sm" name="cepfa_nombre_ciudadano" id="cepfa_nombre_ciudadano" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $cepfa_nombre_ciudadano; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cepfa_correo_direccion" class="my-0">Correo/dirección de notificación</label>
                            <input type="text" class="form-control form-control-sm" name="cepfa_correo_direccion" id="cepfa_correo_direccion" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $cepfa_correo_direccion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cepfa_departamento" class="my-0">Departamento</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cepfa_departamento" id="cepfa_departamento" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_departamentos); $i++): ?>
                                    <option value="<?php echo $resultado_registros_departamentos[$i][0]; ?>" <?php if($cepfa_departamento==$resultado_registros_departamentos[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_departamentos[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cepfa_solicitud_novedad" class="my-0">Solicitud/novedad de radicado</label>
                              <select class="form-control form-control-sm form-select" name="cepfa_solicitud_novedad" id="cepfa_solicitud_novedad" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($array_parametros['solicitud']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['solicitud']['id'][$i]; ?>" <?php if($cepfa_solicitud_novedad==$array_parametros['solicitud']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['solicitud']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cepfa_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cepfa_observaciones" id="cepfa_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cepfa_observaciones; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <div class="form-group custom-control custom-checkbox m-0">
                                  <input type="checkbox" class="custom-control-input" id="customCheckmail" name="notificar" value="Si" <?php if(isset($_POST["guardar_registro"]) AND $_POST["notificar"]=="Si"){ echo "checked"; } ?> <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <label class="custom-control-label p-0 m-0" for="customCheckmail">Enviarme una confirmación por correo</label>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1): ?>
                                  <!-- <a href="interacciones_crear?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo $bandeja; ?>&duplicado=<?php echo base64_encode('si'); ?>&canal=<?php echo base64_encode($canal_atencion); ?>&id_caso=<?php echo base64_encode($id_caso); ?>&tipodoc=<?php echo base64_encode($tipo_documento); ?>&identificacion=<?php echo base64_encode($identificacion); ?>&id_encuesta=<?php echo base64_encode($id_encuesta); ?>" class="btn btn-warning float-end ms-1">Registrar otra interacción asociada</a> -->
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