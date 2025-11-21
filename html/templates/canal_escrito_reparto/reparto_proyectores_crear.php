<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Reparto";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Reparto | 8. Proyectores | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="reparto_proyectores?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='proyectores' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }

  $consulta_string_departamentos="SELECT `ciu_codigo`, `ciu_departamento` FROM `administrador_departamentos` ORDER BY `ciu_departamento`";
  $consulta_registros_departamentos = $enlace_db->prepare($consulta_string_departamentos);
  $consulta_registros_departamentos->execute();
  $resultado_registros_departamentos = $consulta_registros_departamentos->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_departamentos); $i++) { 
    $array_departamento[$resultado_registros_departamentos[$i][0]]=$resultado_registros_departamentos[$i][1];
  }

  if(isset($_POST["guardar_registro"])){
      $cep_radicado_entrada=validar_input($_POST['cep_radicado_entrada']);
      $cep_direccionamiento=validar_input($_POST['cep_direccionamiento']);
      $cep_observacion_traslado=validar_input($_POST['cep_observacion_traslado']);
      $cep_documento_identidad=validar_input($_POST['cep_documento_identidad']);
      $cep_nombre_ciudadano=validar_input($_POST['cep_nombre_ciudadano']);
      $cep_correo_direccion=validar_input($_POST['cep_correo_direccion']);
      $cep_departamento=validar_input($_POST['cep_departamento']);
      $cep_novedad_radicado=validar_input($_POST['cep_novedad_radicado']);
      $cep_observaciones=validar_input($_POST['cep_observaciones']);
      $cep_notificar=validar_input($_POST['notificar']);
      $cep_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cerep_proyectores`(`cep_radicado_entrada`, `cep_direccionamiento`, `cep_observacion_traslado`, `cep_documento_identidad`, `cep_nombre_ciudadano`, `cep_correo_direccion`, `cep_departamento`, `cep_novedad_radicado`, `cep_observaciones`, `cep_notificar`, `cep_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssssssss', $cep_radicado_entrada, $cep_direccionamiento, $cep_observacion_traslado, $cep_documento_identidad, $cep_nombre_ciudadano, $cep_correo_direccion, $cep_departamento, $cep_novedad_radicado, $cep_observaciones, $cep_notificar, $cep_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cep_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='8. Proyectores - Reparto | Canal Escrito';
                $referencia='8. Proyectores - Reparto | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado entrada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cep_radicado_entrada."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Direccionamiento</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['direccionamiento']['texto'][$cep_direccionamiento]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observación traslado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cep_observacion_traslado."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Documento de identidad</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cep_documento_identidad."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nombre del ciudadano</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cep_nombre_ciudadano."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Correo/dirección de notificación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cep_correo_direccion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Departamento</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_departamento[$cep_departamento]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Novedad radicado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['novedad_radicado']['texto'][$cep_novedad_radicado]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cep_observaciones."</td>
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
                            <label for="cep_radicado_entrada" class="my-0">Radicado entrada</label>
                            <input type="text" class="form-control form-control-sm" name="cep_radicado_entrada" id="cep_radicado_entrada" minlength="18" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cep_radicado_entrada; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cep_direccionamiento" class="my-0">Direccionamiento</label>
                              <select class="form-control form-control-sm form-select" name="cep_direccionamiento" id="cep_direccionamiento" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_direccionamiento();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['direccionamiento'])): ?>
                                    <?php for ($i=0; $i < count($array_parametros['direccionamiento']['id']); $i++): ?>
                                      <option value="<?php echo $array_parametros['direccionamiento']['id'][$i]; ?>" <?php if($cep_direccionamiento==$array_parametros['direccionamiento']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['direccionamiento']['valor'][$i]; ?></option>
                                    <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cep_observacion_traslado">
                          <div class="form-group my-1">
                            <label for="cep_observacion_traslado" class="my-0">Observación traslado</label>
                            <textarea class="form-control form-control-sm height-100" name="cep_observacion_traslado" id="cep_observacion_traslado" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> disabled required><?php if(isset($_POST["guardar_registro"])){ echo $cep_observacion_traslado; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cep_documento_identidad" class="my-0">Documento identidad (Si no hay número colocar: NO REGISTRA)</label>
                            <input type="text" class="form-control form-control-sm" name="cep_documento_identidad" id="cep_documento_identidad" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $cep_documento_identidad; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cep_nombre_ciudadano" class="my-0">Nombre del ciudadano (Si no hay nombre colocar: CIUDADANO)</label>
                            <input type="text" class="form-control form-control-sm" name="cep_nombre_ciudadano" id="cep_nombre_ciudadano" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $cep_nombre_ciudadano; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cep_correo_direccion" class="my-0">Correo/dirección de notificación</label>
                            <input type="text" class="form-control form-control-sm" name="cep_correo_direccion" id="cep_correo_direccion" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $cep_correo_direccion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cep_departamento" class="my-0">Departamento</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cep_departamento" id="cep_departamento" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_departamentos); $i++): ?>
                                    <option value="<?php echo $resultado_registros_departamentos[$i][0]; ?>" <?php if($cep_departamento==$resultado_registros_departamentos[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_departamentos[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cep_novedad_radicado" class="my-0">Novedad radicado</label>
                              <select class="form-control form-control-sm form-select" name="cep_novedad_radicado" id="cep_novedad_radicado" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['novedad_radicado'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['novedad_radicado']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['novedad_radicado']['id'][$i]; ?>" <?php if($cep_novedad_radicado==$array_parametros['novedad_radicado']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['novedad_radicado']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cep_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cep_observaciones" id="cep_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cep_observaciones; } ?></textarea>
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
  <script type="text/javascript">
    function validar_direccionamiento(){
      var direccionamiento_opcion = document.getElementById("cep_direccionamiento");
      var direccionamiento = direccionamiento_opcion.options[direccionamiento_opcion.selectedIndex].text;

      $("#div_cep_observacion_traslado").removeClass('d-block').addClass('d-none');
      document.getElementById('cep_observacion_traslado').disabled=true;

      if(direccionamiento=="Oficio Especial Con Traslado" || direccionamiento=="Traslados") {
          $("#div_cep_observacion_traslado").removeClass('d-none').addClass('d-block');
          document.getElementById('cep_observacion_traslado').disabled=false;
      }
    }
    validar_direccionamiento();
  </script>
</body>
</html>