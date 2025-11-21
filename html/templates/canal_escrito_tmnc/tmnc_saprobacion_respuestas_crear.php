<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-TMNC";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Transacciones Monetarias No Condicionadas | 2. Aprobación Respuesta | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="tmnc_saprobacion_respuestas?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='tmnc_saprobacion_respuestas' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }
  
  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_campania` FROM `administrador_usuario` LEFT JOIN `administrador_campania` AS TC ON `administrador_usuario`.`usu_campania`=TC.`ac_id` WHERE `usu_estado`='Activo' AND TC.`ac_nombre_campania`='Canal Escrito' ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_analistas); $i++) { 
    $array_analista[$resultado_registros_analistas[$i][0]]=$resultado_registros_analistas[$i][1];
  }

  if(isset($_POST["guardar_registro"])){
      $cetar_radicado=validar_input($_POST['cetar_radicado']);
      $cetar_numero_documento=validar_input($_POST['cetar_numero_documento']);
      $cetar_nombre_ciudadano=validar_input($_POST['cetar_nombre_ciudadano']);
      $cetar_proyector=validar_input($_POST['cetar_proyector']);
      $cetar_apoyo_prosperidad=validar_input($_POST['cetar_apoyo_prosperidad']);
      $cetar_ingreso_solidario=validar_input($_POST['cetar_ingreso_solidario']);
      $cetar_carta_respuesta=validar_input($_POST['cetar_carta_respuesta']);
      $cetar_estado=validar_input($_POST['cetar_estado']);
      $cetar_comentario_aprobacion=validar_input($_POST['cetar_comentario_aprobacion']);
      $cetar_motivo_rechazo=validar_input($_POST['cetar_motivo_rechazo']);
      $cetar_observaciones=validar_input($_POST['cetar_observaciones']);
      $cetar_notificar=validar_input($_POST['notificar']);
      $cetar_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cetmnc_aprobacion_respuesta`(`cetar_radicado`, `cetar_numero_documento`, `cetar_nombre_ciudadano`, `cetar_proyector`, `cetar_apoyo_prosperidad`, `cetar_ingreso_solidario`, `cetar_carta_respuesta`, `cetar_estado`, `cetar_comentario_aprobacion`, `cetar_motivo_rechazo`, `cetar_observaciones`, `cetar_notificar`, `cetar_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssssssssss', $cetar_radicado, $cetar_numero_documento, $cetar_nombre_ciudadano, $cetar_proyector, $cetar_apoyo_prosperidad, $cetar_ingreso_solidario, $cetar_carta_respuesta, $cetar_estado, $cetar_comentario_aprobacion, $cetar_motivo_rechazo, $cetar_observaciones, $cetar_notificar, $cetar_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cetar_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='2. Aprobación Respuesta - TMNC | Canal Escrito';
                $referencia='2. Aprobación Respuesta - TMNC | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>No. radicado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetar_radicado."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Número Documento de identidad (Si no hay número colocar 0)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetar_numero_documento."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nombre del ciudadano (Si no hay nombre colocar CIUDADANO)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetar_nombre_ciudadano."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Proyector</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['proyector']['texto'][$cetar_proyector]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Apoyo prosperidad social</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['apoyo_prosperidad']['texto'][$cetar_apoyo_prosperidad]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Ingreso solidario</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['ingreso_solidario']['texto'][$cetar_ingreso_solidario]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Carta de respuesta</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['carta_respuesta']['texto'][$cetar_carta_respuesta]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Estado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['estado']['texto'][$cetar_estado]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Comentario de aprobación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetar_comentario_aprobacion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Motivo del rechazo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetar_motivo_rechazo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetar_observaciones."</td>
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
                            <label for="cetar_radicado" class="my-0">No. radicado</label>
                            <input type="text" class="form-control form-control-sm" name="cetar_radicado" id="cetar_radicado" minlength="13" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetar_radicado; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetar_numero_documento" class="my-0">Número Documento de identidad (Si no hay número colocar 0)</label>
                            <input type="text" class="form-control form-control-sm" name="cetar_numero_documento" id="cetar_numero_documento" minlength="1" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetar_numero_documento; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetar_nombre_ciudadano" class="my-0">Nombre del ciudadano (Si no hay nombre colocar CIUDADANO)</label>
                            <input type="text" class="form-control form-control-sm" name="cetar_nombre_ciudadano" id="cetar_nombre_ciudadano" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetar_nombre_ciudadano; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cetar_proyector" class="my-0">Proyector</label>
                              <select class="form-control form-control-sm form-select" name="cetar_proyector" id="cetar_proyector" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_proyector();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['proyector']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['proyector']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['proyector']['id'][$i]; ?>" <?php if($cetar_proyector==$array_parametros['proyector']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['proyector']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetar_apoyo_prosperidad">
                          <div class="form-group my-1">
                              <label for="cetar_apoyo_prosperidad" class="my-0">Apoyo prosperidad social</label>
                              <select class="form-control form-control-sm form-select" name="cetar_apoyo_prosperidad" id="cetar_apoyo_prosperidad" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['apoyo_prosperidad']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['apoyo_prosperidad']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['apoyo_prosperidad']['id'][$i]; ?>" <?php if($cetar_apoyo_prosperidad==$array_parametros['apoyo_prosperidad']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['apoyo_prosperidad']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetar_ingreso_solidario">
                          <div class="form-group my-1">
                              <label for="cetar_ingreso_solidario" class="my-0">Ingreso solidario</label>
                              <select class="form-control form-control-sm form-select" name="cetar_ingreso_solidario" id="cetar_ingreso_solidario" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['ingreso_solidario']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['ingreso_solidario']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['ingreso_solidario']['id'][$i]; ?>" <?php if($cetar_ingreso_solidario==$array_parametros['ingreso_solidario']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['ingreso_solidario']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cetar_carta_respuesta" class="my-0">Carta de respuesta</label>
                              <select class="form-control form-control-sm form-select" name="cetar_carta_respuesta" id="cetar_carta_respuesta" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['carta_respuesta']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['carta_respuesta']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['carta_respuesta']['id'][$i]; ?>" <?php if($cetar_carta_respuesta==$array_parametros['carta_respuesta']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['carta_respuesta']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cetar_estado" class="my-0">Estado</label>
                              <select class="form-control form-control-sm form-select" name="cetar_estado" id="cetar_estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_estado();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['estado']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['estado']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['estado']['id'][$i]; ?>" <?php if($cetar_estado==$array_parametros['estado']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['estado']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetar_comentario_aprobacion">
                          <div class="form-group my-1">
                            <label for="cetar_comentario_aprobacion" class="my-0">Comentario de aprobación</label>
                            <input type="text" class="form-control form-control-sm" name="cetar_comentario_aprobacion" id="cetar_comentario_aprobacion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetar_comentario_aprobacion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetar_motivo_rechazo">
                          <div class="form-group my-1">
                            <label for="cetar_motivo_rechazo" class="my-0">Motivo del rechazo</label>
                            <input type="text" class="form-control form-control-sm" name="cetar_motivo_rechazo" id="cetar_motivo_rechazo" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetar_motivo_rechazo; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div><body></body>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cetar_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cetar_observaciones" id="cetar_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cetar_observaciones; } ?></textarea>
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
    jQuery(document).ready(function(){
        jQuery("#cetar_radicado").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[ ]/g, ''));
        });
    });

    jQuery(document).ready(function(){
        jQuery("#cetar_numero_documento").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });

    jQuery(document).ready(function(){
        jQuery("#cetar_nombre_ciudadano").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[^a-zA-Z ]/g, ''));
        });
    });
  </script>

  <script type="text/javascript">
    function validar_proyector(){
      var proyector_opcion = document.getElementById("cetar_proyector");
      var proyector = proyector_opcion.options[proyector_opcion.selectedIndex].text;

      $("#div_cetar_apoyo_prosperidad").removeClass('d-block').addClass('d-none');
      $("#div_cetar_ingreso_solidario").removeClass('d-block').addClass('d-none');
      document.getElementById('cetar_apoyo_prosperidad').disabled=true;
      document.getElementById('cetar_ingreso_solidario').disabled=true;
      
      if(proyector=="APOYO PROSPERIDAD SOCIAL") {
          $("#div_cetar_apoyo_prosperidad").removeClass('d-none').addClass('d-block');
          document.getElementById('cetar_apoyo_prosperidad').disabled=false;
      } else if(proyector=="INGRESO SOLIDARIO") {
          $("#div_cetar_ingreso_solidario").removeClass('d-none').addClass('d-block');
          document.getElementById('cetar_ingreso_solidario').disabled=false;
      }
    }

    function validar_estado(){
      var estado_opcion = document.getElementById("cetar_estado");
      var estado = estado_opcion.options[estado_opcion.selectedIndex].text;

      $("#div_cetar_comentario_aprobacion").removeClass('d-block').addClass('d-none');
      $("#div_cetar_motivo_rechazo").removeClass('d-block').addClass('d-none');
      document.getElementById('cetar_comentario_aprobacion').disabled=true;
      document.getElementById('cetar_motivo_rechazo').disabled=true;
      
      if(estado=="APROBADO") {
          $("#div_cetar_comentario_aprobacion").removeClass('d-none').addClass('d-block');
          document.getElementById('cetar_comentario_aprobacion').disabled=false;
      } else if(estado=="RECHAZADO") {
          $("#div_cetar_motivo_rechazo").removeClass('d-none').addClass('d-block');
          document.getElementById('cetar_motivo_rechazo').disabled=false;
      }
    }
    validar_proyector();
    validar_estado();
  </script>
</body>
</html>