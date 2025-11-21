<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-TMNC";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Transacciones Monetarias No Condicionadas | 7. Casos Sin Gestionar | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="tmnc_scasos_sgestionar?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='tmnc_scasos_sgestionar' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
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
      $cetcsg_proceso_ingreso_solidario=validar_input($_POST['cetcsg_proceso_ingreso_solidario']);
      $cetcsg_responsable_envio=validar_input($_POST['cetcsg_responsable_envio']);
      $cetcsg_responsable_proyeccion=validar_input($_POST['cetcsg_responsable_proyeccion']);
      $cetcsg_causal_no_envio=validar_input($_POST['cetcsg_causal_no_envio']);
      $cetcsg_causal_no_proyeccion=validar_input($_POST['cetcsg_causal_no_proyeccion']);
      $cetcsg_cantidad_casos=validar_input($_POST['cetcsg_cantidad_casos']);
      $cetcsg_observaciones=validar_input($_POST['cetcsg_observaciones']);
      $cetcsg_notificar=validar_input($_POST['notificar']);
      $cetcsg_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cetmnc_casos_sin_gestionar`(`cetcsg_proceso_ingreso_solidario`, `cetcsg_responsable_envio`, `cetcsg_responsable_proyeccion`, `cetcsg_causal_no_envio`, `cetcsg_causal_no_proyeccion`, `cetcsg_cantidad_casos`, `cetcsg_observaciones`, `cetcsg_notificar`, `cetcsg_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssssss', $cetcsg_proceso_ingreso_solidario, $cetcsg_responsable_envio, $cetcsg_responsable_proyeccion, $cetcsg_causal_no_envio, $cetcsg_causal_no_proyeccion, $cetcsg_cantidad_casos, $cetcsg_observaciones, $cetcsg_notificar, $cetcsg_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cetcsg_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='7. Casos Sin Gestionar - TMNC | Canal Escrito';
                $referencia='7. Casos Sin Gestionar - TMNC | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Proceso ingreso solidario</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['proceso_ingreso_solidario']['texto'][$cetcsg_proceso_ingreso_solidario]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Responsable envíos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['responsable_envio']['texto'][$cetcsg_responsable_envio]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Responsable proyección</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['responsable_proyeccion']['texto'][$cetcsg_responsable_proyeccion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Causal no envío</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['causal_no_envio']['texto'][$cetcsg_causal_no_envio]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Causal no proyección</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['causal_no_proyeccion']['texto'][$cetcsg_causal_no_proyeccion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Cantidad casos pendientes por gestionar y/o escalados</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetcsg_cantidad_casos."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetcsg_observaciones."</td>
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
                              <label for="cetcsg_proceso_ingreso_solidario" class="my-0">Proceso ingreso solidario</label>
                              <select class="form-control form-control-sm form-select" name="cetcsg_proceso_ingreso_solidario" id="cetcsg_proceso_ingreso_solidario" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_proceso();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['proceso_ingreso_solidario']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['proceso_ingreso_solidario']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['proceso_ingreso_solidario']['id'][$i]; ?>" <?php if($cetcsg_proceso_ingreso_solidario==$array_parametros['proceso_ingreso_solidario']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['proceso_ingreso_solidario']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetcsg_responsable_envio">
                          <div class="form-group my-1">
                              <label for="cetcsg_responsable_envio" class="my-0">Responsable envíos</label>
                              <select class="form-control form-control-sm form-select" name="cetcsg_responsable_envio" id="cetcsg_responsable_envio" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['responsable_envio']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['responsable_envio']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['responsable_envio']['id'][$i]; ?>" <?php if($cetcsg_responsable_envio==$array_parametros['responsable_envio']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['responsable_envio']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetcsg_responsable_proyeccion">
                          <div class="form-group my-1">
                              <label for="cetcsg_responsable_proyeccion" class="my-0">Responsable proyección</label>
                              <select class="form-control form-control-sm form-select" name="cetcsg_responsable_proyeccion" id="cetcsg_responsable_proyeccion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['responsable_proyeccion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['responsable_proyeccion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['responsable_proyeccion']['id'][$i]; ?>" <?php if($cetcsg_responsable_proyeccion==$array_parametros['responsable_proyeccion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['responsable_proyeccion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetcsg_causal_no_envio">
                          <div class="form-group my-1">
                              <label for="cetcsg_causal_no_envio" class="my-0">Causal no envío</label>
                              <select class="form-control form-control-sm form-select" name="cetcsg_causal_no_envio" id="cetcsg_causal_no_envio" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['causal_no_envio']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['causal_no_envio']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['causal_no_envio']['id'][$i]; ?>" <?php if($cetcsg_causal_no_envio==$array_parametros['causal_no_envio']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['causal_no_envio']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetcsg_causal_no_proyeccion">
                          <div class="form-group my-1">
                              <label for="cetcsg_causal_no_proyeccion" class="my-0">Causal no proyección</label>
                              <select class="form-control form-control-sm form-select" name="cetcsg_causal_no_proyeccion" id="cetcsg_causal_no_proyeccion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['causal_no_proyeccion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['causal_no_proyeccion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['causal_no_proyeccion']['id'][$i]; ?>" <?php if($cetcsg_causal_no_proyeccion==$array_parametros['causal_no_proyeccion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['causal_no_proyeccion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetcsg_cantidad_casos" class="my-0">Cantidad casos pendientes por gestionar y/o escalados</label>
                            <input type="text" class="form-control form-control-sm" name="cetcsg_cantidad_casos" id="cetcsg_cantidad_casos" minlength="1" maxlength="5" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetcsg_cantidad_casos; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cetcsg_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cetcsg_observaciones" id="cetcsg_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cetcsg_observaciones; } ?></textarea>
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
    function validar_proceso(){
      var proceso_opcion = document.getElementById("cetcsg_proceso_ingreso_solidario");
      var proceso = proceso_opcion.options[proceso_opcion.selectedIndex].text;

      $("#div_cetcsg_responsable_envio").removeClass('d-block').addClass('d-none');
      $("#div_cetcsg_responsable_proyeccion").removeClass('d-block').addClass('d-none');
      $("#div_cetcsg_causal_no_envio").removeClass('d-block').addClass('d-none');
      $("#div_cetcsg_causal_no_proyeccion").removeClass('d-block').addClass('d-none');
      document.getElementById('cetcsg_responsable_envio').disabled=true;
      document.getElementById('cetcsg_responsable_proyeccion').disabled=true;
      document.getElementById('cetcsg_causal_no_envio').disabled=true;
      document.getElementById('cetcsg_causal_no_proyeccion').disabled=true;

      if(proceso=="ENVÍOS") {
          $("#div_cetcsg_responsable_envio").removeClass('d-none').addClass('d-block');
          $("#div_cetcsg_causal_no_envio").removeClass('d-none').addClass('d-block');
          document.getElementById('cetcsg_responsable_envio').disabled=false;
          document.getElementById('cetcsg_causal_no_envio').disabled=false;
      } else if(proceso=="PROYECCIÓN") {
          $("#div_cetcsg_responsable_proyeccion").removeClass('d-none').addClass('d-block');
          $("#div_cetcsg_causal_no_proyeccion").removeClass('d-none').addClass('d-block');
          document.getElementById('cetcsg_responsable_proyeccion').disabled=false;
          document.getElementById('cetcsg_causal_no_proyeccion').disabled=false;
      }
    }
    jQuery(document).ready(function(){
        jQuery("#cetcsg_cantidad_casos").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });
    validar_proceso();
  </script>
</body>
</html>