<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Monitoreos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Monitoreos | Eliminar";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="monitoreos?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);

  if(isset($_POST["eliminar_registro"])){
      if($_SESSION[APP_SESSION.'_registro_eliminado']!=1){
          // Prepara la sentencia
          $sentencia_delete_historial = $enlace_db->prepare("DELETE FROM `gestion_calidad_monitoreo_historial` WHERE `gcmh_monitoreo`=?");
          // Agrega variables a sentencia preparada
          $sentencia_delete_historial->bind_param('s', $id_registro);
          
          // Evalua resultado de ejecución sentencia preparada
          if ($sentencia_delete_historial->execute()) {
              // Prepara la sentencia
              $sentencia_delete_calificaciones = $enlace_db->prepare("DELETE FROM `gestion_calidad_monitoreo_calificaciones` WHERE `gcmc_monitoreo`=?");
              // Agrega variables a sentencia preparada
              $sentencia_delete_calificaciones->bind_param('s', $id_registro);
              
              // Evalua resultado de ejecución sentencia preparada
              if ($sentencia_delete_calificaciones->execute()) {
                  // Prepara la sentencia
                  $sentencia_delete_monitoreo = $enlace_db->prepare("DELETE FROM `gestion_calidad_monitoreo` WHERE `gcm_id`=?");
                  // Agrega variables a sentencia preparada
                  $sentencia_delete_monitoreo->bind_param('s', $id_registro);
                  
                  // Evalua resultado de ejecución sentencia preparada
                  if ($sentencia_delete_monitoreo->execute()) {
                      $consulta_string_log = "INSERT INTO `administrador_log`(`clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_registro_usuario`) VALUES (?,?,?,?,?)";
                      
                      $log_modulo=$modulo_plataforma;
                      $log_tipo="eliminar";
                      $log_accion="Eliminar registro";
                      $log_detalle="Monitoreo [".$id_registro."]";
                      $log_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
                      
                      $consulta_registros_log = $enlace_db->prepare($consulta_string_log);
                      $consulta_registros_log->bind_param("sssss", $log_modulo, $log_tipo, $log_accion, $log_detalle, $log_usuario);
                      $consulta_registros_log->execute();

                      $_SESSION[APP_SESSION.'_registro_eliminado']=1;
                      $respuesta_accion = "alertButton('success', 'Registro eliminado', 'Registro eliminado exitosamente', '".$url_salir."');";
                  } else {
                      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al eliminar el registro');";
                  }
              } else {
                  $respuesta_accion = "alertButton('error', 'Error', 'Problemas al eliminar el registro');";
              }
          } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al eliminar el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string_monitoreo="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE TMC.`gcm_id`=?";

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
                      <?php if($_SESSION[APP_SESSION.'_registro_eliminado']==1): ?>
                          <p class="alert alert-danger p-1">¡Registro eliminado exitosamente, haga clic en <b>Finalizar</b> para salir!</p>
                      <?php else: ?>
                          <div class="table-responsive">
                              <table class="table table-bordered table-striped table-hover table-sm">
                                  <tbody>
                                      <tr>
                                          <th class="p-1 font-size-11">Monitoreo</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][0]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Agente</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][37]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Responsable</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][39]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Matriz</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][2]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Canal</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][47]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Nota ENC</th>
                                          <td class="p-1 font-size-11 <?php if($resultado_registros_monitoreo[0][10]<100){echo 'rechazado';}else{echo'aceptado';} ?>"><?php echo $resultado_registros_monitoreo[0][10]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Nota ECUF</th>
                                          <td class="p-1 font-size-11 <?php if($resultado_registros_monitoreo[0][12]<100){echo 'rechazado';}else{echo'aceptado';} ?>"><?php echo $resultado_registros_monitoreo[0][12]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Nota ECN</th>
                                          <td class="p-1 font-size-11 <?php if($resultado_registros_monitoreo[0][11]<100){echo 'rechazado';}else{echo'aceptado';} ?>"><?php echo $resultado_registros_monitoreo[0][11]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Dependencia</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][5]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Número Interacción</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][7]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Identificación Ciudadano</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][6]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Fecha Interacción</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][4]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Tipo Monitoreo</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][8]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Solución primer contacto?</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][14]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Causal NO solución</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][15]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Programa</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][16]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Tipificación</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][17]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Sub-Tipificación</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][18]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Atención WOW</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][19]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Se presenta VOC</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][20]; ?></td>
                                      </tr>
                                      <?php if($resultado_registros_monitoreo[0][20]=='Si'): ?>
                                          <tr>
                                              <th class="p-1 font-size-11">Segmento</th>
                                              <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][21]; ?></td>
                                          </tr>
                                          <tr>
                                              <th class="p-1 font-size-11">Tabulación VOC</th>
                                              <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][22]; ?></td>
                                          </tr>
                                          <tr>
                                              <th class="p-1 font-size-11">VOC</th>
                                              <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][23]; ?></td>
                                          </tr>
                                          <tr>
                                              <th class="p-1 font-size-11">VOC Emoción Inicial</th>
                                              <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][23]; ?></td>
                                          </tr>
                                          <tr>
                                              <th class="p-1 font-size-11">VOC Emoción Final</th>
                                              <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][25]; ?></td>
                                          </tr>
                                          <tr>
                                              <th class="p-1 font-size-11">Qué le activó</th>
                                              <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][26]; ?></td>
                                          </tr>
                                          <tr>
                                              <th class="p-1 font-size-11">Atribuible</th>
                                              <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][27]; ?></td>
                                          </tr>
                                      <?php endif; ?>
                                      <tr>
                                          <th class="p-1 font-size-11">Observaciones</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][9]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Registrado por</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][40]; ?></td>
                                      </tr>
                                      <tr>
                                          <th class="p-1 font-size-11">Fecha registro</th>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_monitoreo[0][36]; ?></td>
                                      </tr>
                                  </tbody>
                              </table>
                          </div>
                          <p class="alert alert-danger p-1">¡El registro será eliminado de forma permanente y no se podrá recuperar, por favor valide antes de continuar!</p>
                      <?php endif; ?>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_eliminado']==1): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-warning float-end ms-1" type="submit" name="eliminar_registro" id="eliminar_registro_btn">Si, eliminar</button>
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