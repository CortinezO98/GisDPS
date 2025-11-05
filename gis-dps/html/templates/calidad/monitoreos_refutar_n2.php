<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Monitoreos";
  require_once("../../iniciador.php");
  require_once("../../app/functions/validar_festivos.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Monitoreos | Refutar Nivel 2";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="monitoreos?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);

  $consulta_string_monitoreo="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal`, TMC.`gcm_fecha_reac_limite`, TMC.`gcm_fecha_reac`, TMC.`gcm_fecha_calidad_reac_limite`, TMC.`gcm_fecha_calidad_reac`, TMC.`gcm_fecha_snivel_reac_limite`, TMC.`gcm_fecha_snivel_reac`, TMC.`gcm_fecha_sreac_limite`, TMC.`gcm_fecha_sreac`, TMC.`gcm_fecha_novedad_inicio`, TMC.`gcm_fecha_novedad_fin`, TMC.`gcm_novedad_observaciones`, TUR.`usu_correo_corporativo`, TUA.`usu_correo_corporativo`, TS.`usu_correo_corporativo` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE TMC.`gcm_id`=?";

  $consulta_registros_monitoreo = $enlace_db->prepare($consulta_string_monitoreo);
  $consulta_registros_monitoreo->bind_param("s", $id_registro);
  $consulta_registros_monitoreo->execute();
  $resultado_registros_monitoreo = $consulta_registros_monitoreo->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
      $observaciones=validar_input($_POST['observaciones']);
      $tipo_cambio="Refutar-Nivel 2";
      $estado="Refutado-Nivel 2";
      $id_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

      if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_calidad_monitoreo_historial`(`gcmh_monitoreo`, `gcmh_tipo_cambio`, `gcmh_comentarios`, `gcmh_resarcimiento`, `gcmh_registro_usuario`) VALUES (?,?,?,'',?)");

          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssss', $id_registro, $tipo_cambio, $observaciones, $id_usuario);
          if ($sentencia_insert->execute()) {
              $dia_control=date('Y-m-d H:i:s');
              $dias_habiles=0;
              while ($dias_habiles<=2) {
                  $numero_dia=date("N", strtotime($dia_control));
                  $festivo=validarFestivo($dia_control);
                  if ($numero_dia>=1 AND $numero_dia<6 AND $festivo=='') {
                      $dia_limite=$dia_control;
                      $dias_habiles++;
                  }
                  $dia_control = date("Y-m-d H:i:s", strtotime("+ 1 day", strtotime($dia_control)));
              }

              // Prepara la sentencia
              $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_calidad_monitoreo` SET `gcm_estado`=?, `gcm_fecha_snivel_reac`='".date('Y-m-d H:i:s')."', `gcm_fecha_sreac_limite`=? WHERE `gcm_id`=?");

              // Agrega variables a sentencia preparada
              $consulta_actualizar->bind_param('sss', $estado, $dia_limite, $id_registro);

              
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

                  $consulta_string_nivel2="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_correo_corporativo` FROM `administrador_usuario` WHERE `usu_cargo_rol`='COORDINADOR NACIONAL-CALIDAD N2'";
                  $consulta_registros_nivel2 = $enlace_db->prepare($consulta_string_nivel2);
                  $consulta_registros_nivel2->execute();
                  $resultado_registros_nivel2 = $consulta_registros_nivel2->get_result()->fetch_all(MYSQLI_NUM);

                  //PROGRAMACIÓN NOTIFICACIÓN
                  $asunto='Refutado Nivel 2 | '.$id_registro;
                  $referencia='Monitoreo Refutado Nivel 2';
                  $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha refutado el siguiente monitoreo:</p>
                              <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Id Monitoreo</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $id_registro ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Doc. Agente</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $resultado_registros_monitoreo[0][3] ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nombres y Apellidos</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $resultado_registros_monitoreo[0][37] ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipo Monitoreo</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $resultado_registros_monitoreo[0][8] ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Dependencia</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $resultado_registros_monitoreo[0][5] ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Número Interacción</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $resultado_registros_monitoreo[0][7] ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nota ENC</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $resultado_registros_monitoreo[0][10] ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nota ECUF</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $resultado_registros_monitoreo[0][12] ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nota ECN</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $resultado_registros_monitoreo[0][11] ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Registrado por</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". $resultado_registros_monitoreo[0][40] ."</td>
                                  </tr>
                                  <tr>
                                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha actualización</td>
                                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". date('d/m/Y H:i:s') ."</td>
                                  </tr>
                              </table>";
                  $nc_address=$resultado_registros_nivel2[0][2].";";
                  $nc_cc=$resultado_registros_monitoreo[0][60].";".$resultado_registros_monitoreo[0][61].";".$resultado_registros_monitoreo[0][59].";";
                  notificacion($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);

                  $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
                  $_SESSION[APP_SESSION.'_registro_creado_cambio_estado']=1;
              } else {
                  $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
              }
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
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
                      <?php //if(date('Y-m-d H:i:s')<$resultado_registros_monitoreo[0][52]): ?>
                    <?php if(1): ?>
                          <div class="col-md-12">
                              <div class="form-group">
                                <label for="observaciones">Observaciones para refutar y escalar a nivel 2</label>
                                <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']==1) { echo 'readonly'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $observaciones; } ?></textarea>
                              </div>
                          </div>
                      <?php else: ?>
                          <div class="col-md-12">
                              <p class="alert alert-warning p-1 font-size-11">¡Tiempo para retroalimentación vencido!</p>
                          </div>
                      <?php endif; ?>
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
  </script>
</body>
</html>