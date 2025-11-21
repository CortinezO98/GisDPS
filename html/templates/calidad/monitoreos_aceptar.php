<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Monitoreos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Monitoreos | Aceptar";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="monitoreos?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);

  $consulta_string_monitoreo="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal`, TUA.`usu_estado` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE TMC.`gcm_id`=?";

  $consulta_registros_monitoreo = $enlace_db->prepare($consulta_string_monitoreo);
  $consulta_registros_monitoreo->bind_param("s", $id_registro);
  $consulta_registros_monitoreo->execute();
  $resultado_registros_monitoreo = $consulta_registros_monitoreo->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
      $token=validar_input($_POST['token']);
      $observaciones=validar_input($_POST['observaciones']);
      $resarcimiento=validar_input($_POST['resarcimiento']);
      $causa_odm=validar_input($_POST['causa_odm']);
      $causa_odm_cual=validar_input($_POST['causa_odm_cual']);

      if ($causa_odm=='Otra') {
        $causa_odm.=': '.$causa_odm_cual;
      }


      $tipo_cambio="Aceptar";
      $estado="Aceptado";

      $id_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

      if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']!=1){
          $resultado_registros_token=array();
          if ($resultado_registros_monitoreo[0][48]!='Retirado') {
            $consulta_string_token="SELECT `gmct_id`, `gmct_token`, `gmct_monitoreo`, `gmct_usuario`, `gmct_registro_fecha` FROM `gestion_calidad_monitoreo_token` WHERE `gmct_token`=? AND `gmct_monitoreo`=? AND `gmct_usuario`=? AND `gmct_estado`='Pendiente' AND `gmct_registro_fecha` LIKE '".date('Y-m-d')."%'";
            $consulta_registros_token = $enlace_db->prepare($consulta_string_token);
            $consulta_registros_token->bind_param("sss", $token, $id_registro, $resultado_registros_monitoreo[0][3]);
            $consulta_registros_token->execute();
            $resultado_registros_token = $consulta_registros_token->get_result()->fetch_all(MYSQLI_NUM);
          }

          if (count($resultado_registros_token)>0 OR $resultado_registros_monitoreo[0][48]=='Retirado') {

              // Prepara la sentencia
              $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_calidad_monitoreo_historial`(`gcmh_monitoreo`, `gcmh_tipo_cambio`, `gcmh_comentarios`, `gcmh_resarcimiento`, `gcmh_registro_usuario`) VALUES (?,?,?,?,?)");

              // Agrega variables a sentencia preparada
              $sentencia_insert->bind_param('sssss', $id_registro, $tipo_cambio, $observaciones, $resarcimiento, $id_usuario);

              $tipo_cambio_odm="Aceptar-ODM";
              $resarcimiento_odm='';
              // Prepara la sentencia
              $sentencia_insert_odm = $enlace_db->prepare("INSERT INTO `gestion_calidad_monitoreo_historial`(`gcmh_monitoreo`, `gcmh_tipo_cambio`, `gcmh_comentarios`, `gcmh_resarcimiento`, `gcmh_registro_usuario`) VALUES (?,?,?,?,?)");
              // Agrega variables a sentencia preparada
              $sentencia_insert_odm->bind_param('sssss', $id_registro, $tipo_cambio_odm, $causa_odm, $resarcimiento_odm, $id_usuario);
              
              if ($sentencia_insert->execute() AND $sentencia_insert_odm->execute()) {
                  if (count($resultado_registros_token)>0) {
                    // Prepara la sentencia
                    $consulta_actualizar_token = $enlace_db->prepare("UPDATE `gestion_calidad_monitoreo_token` SET `gmct_estado`='Usado' WHERE `gmct_token`=? AND `gmct_monitoreo`=? AND `gmct_usuario`=? AND `gmct_estado`='Pendiente'");

                    // Agrega variables a sentencia preparada
                    $consulta_actualizar_token->bind_param('sss', $token, $id_registro, $resultado_registros_monitoreo[0][3]);
                    
                    // Ejecuta sentencia preparada
                    $consulta_actualizar_token->execute();
                  }

                  // Prepara la sentencia
                  $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_calidad_monitoreo` SET `gcm_estado`=?, `gcm_fecha_reac`='".date('Y-m-d H:i:s')."' WHERE `gcm_id`=?");

                  // Agrega variables a sentencia preparada
                  $consulta_actualizar->bind_param('ss', $estado, $id_registro);
                  
                  // Ejecuta sentencia preparada
                  $consulta_actualizar->execute();

                  if (comprobarSentencia($enlace_db->info)) {
                      //insert log eventos
                          $consulta_string_log = "INSERT INTO `administrador_log`(`clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_registro_usuario`) VALUES (?,?,?,?,?)";
                      
                          $log_modulo=$modulo_plataforma;
                          $log_tipo="editar";
                          $log_accion="Editar registro";
                          $log_detalle=$tipo_cambio." monitoreo [".$id_registro."]";
                          $log_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
                          
                          $consulta_registros_log = $enlace_db->prepare($consulta_string_log);
                          $consulta_registros_log->bind_param("sssss", $log_modulo, $log_tipo, $log_accion, $log_detalle, $log_usuario);
                          $consulta_registros_log->execute();
                      //insert log eventos
                      $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
                      $_SESSION[APP_SESSION.'_registro_creado_cambio_estado']=1;
                  } else {
                      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
                  }

              } else {
                $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
              }
          } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Token inválido');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string_monitoreo="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal`, TUA.`usu_estado` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE TMC.`gcm_id`=?";

  $consulta_registros_monitoreo = $enlace_db->prepare($consulta_string_monitoreo);
  $consulta_registros_monitoreo->bind_param("s", $id_registro);
  $consulta_registros_monitoreo->execute();
  $resultado_registros_monitoreo = $consulta_registros_monitoreo->get_result()->fetch_all(MYSQLI_NUM);
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
                      <?php if($resultado_registros_monitoreo[0][48]=='Retirado'): ?>
                          <div class="col-md-12">
                              <p class="alert alert-warning p-1 font-size-11">¡Usuario en estado Retirado, no requiere token para proceso de retroalimentación!</p>
                          </div>
                      <?php endif; ?>
                      <?php if($resultado_registros_monitoreo[0][48]=='Activo'): ?>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="token" class="my-0">Token agente</label>
                            <input type="text" class="form-control form-control-sm" name="token" id="token" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $token; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']==1) { echo 'readonly'; } ?> required>
                          </div>
                      </div>
                      <?php endif; ?>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="observaciones" class="my-0">Compromisos de mejora</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']==1) { echo 'readonly'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $observaciones; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                            <div class="form-group">
                                <label for="resarcimiento" class="m-0">Resarcimiento</label>
                                <select class="form-control form-control-sm form-select" name="resarcimiento" id="resarcimiento" <?php if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']==1) { echo 'disabled'; } ?> required>
                                    <option value="">Seleccione</option>
                                    <option value="Si" <?php if(isset($_POST["guardar_registro"]) AND $resarcimiento=="Si"){ echo "selected"; } ?>>Si</option>
                                    <option value="No aplica" <?php if(isset($_POST["guardar_registro"]) AND $resarcimiento=="No aplica"){ echo "selected"; } ?>>No aplica</option>
                                </select>
                            </div>
                      </div>
                      <div class="col-md-12">
                            <div class="form-group">
                                <label for="causa_odm" class="m-0">Posible causa de la ODM</label>
                                <select class="form-control form-control-sm form-select" name="causa_odm" id="causa_odm" <?php if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']==1) { echo 'disabled'; } ?> onchange="validar_odm()" required>
                                    <option value="">Seleccione</option>
                                    <option value="Desconocimiento" <?php if(isset($_POST["guardar_registro"]) AND $causa_odm=="Desconocimiento"){ echo "selected"; } ?>>Desconocimiento</option>
                                    <option value="No se tiene claridad" <?php if(isset($_POST["guardar_registro"]) AND $causa_odm=="No se tiene claridad"){ echo "selected"; } ?>>No se tiene claridad</option>
                                    <option value="Desconcentración" <?php if(isset($_POST["guardar_registro"]) AND $causa_odm=="Desconcentración"){ echo "selected"; } ?>>Desconcentración</option>
                                    <option value="Otra" <?php if(isset($_POST["guardar_registro"]) AND $causa_odm=="Otra"){ echo "selected"; } ?>>Otra</option>
                                </select>
                            </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_causa_odm_cual">
                          <div class="form-group">
                            <label for="causa_odm_cual" class="my-0">¿Cuál?</label>
                            <input type="text" class="form-control form-control-sm" name="causa_odm_cual" id="causa_odm_cual" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $causa_odm_cual; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']==1) { echo 'readonly'; } ?> required disabled>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']==1): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Guardar</button>
                                  <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');">Cancelar</button>
                              <?php endif; ?>
                              <a href="#" onClick="open_modal_detalle('<?php echo base64_encode($resultado_registros_monitoreo[0][0]); ?>');" class="btn btn-warning float-end me-1" title="Detalle Monitoreo"><?php echo $resultado_registros_monitoreo[0][0]; ?></a>
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
        <!-- MODAL DETALLE -->
        <div class="modal fade" id="modal-detalle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Detalle Monitoreo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body-detalle">
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger py-2 px-2" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- MODAL DETALLE -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
      function open_modal_detalle(id_registro) {
          var myModal = new bootstrap.Modal(document.getElementById("modal-detalle"), {});
          $('.modal-body-detalle').load('monitoreos_detalle.php?reg='+id_registro,function(){
              myModal.show();
          });
      }

      function validar_odm(){
          var causa_odm_opcion = document.getElementById("causa_odm");
          var causa_odm = causa_odm_opcion.options[causa_odm_opcion.selectedIndex].value;
          $("#div_causa_odm_cual").removeClass('d-block').addClass('d-none');
          document.getElementById('causa_odm_cual').disabled=true;
          
          if(causa_odm=="Otra") {
              $("#div_causa_odm_cual").removeClass('d-none').addClass('d-block');
              document.getElementById('causa_odm_cual').disabled=false;
          }
      }
  </script>
</body>
</html>