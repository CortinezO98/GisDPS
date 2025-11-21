<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Monitoreos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Monitoreos | Editar";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="monitoreos?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);

  if(isset($_POST["guardar_registro"])){
    $analista=validar_input($_POST['analista']);
    $dependencia=validar_input($_POST['dependencia']);
    $tipo_monitoreo=validar_input($_POST['tipo_monitoreo']);
    $numero_interaccion=validar_input($_POST['numero_interaccion']);
    $identificacion_ciudadano=validar_input($_POST['identificacion_ciudadano']);
    $fecha_interaccion=validar_input($_POST['fecha_interaccion']);
    $observaciones=validar_input($_POST['observaciones']);
    
    $consulta_string_revisar="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id`  LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE TMC.`gcm_id`=?";

    $consulta_registros_revisar = $enlace_db->prepare($consulta_string_revisar);
    $consulta_registros_revisar->bind_param("s", $id_registro);
    $consulta_registros_revisar->execute();
    $resultado_registros_revisar = $consulta_registros_revisar->get_result()->fetch_all(MYSQLI_NUM);

    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_calidad_monitoreo` SET `gcm_analista`=?,  `gcm_fecha_hora_gestion`=?, `gcm_dependencia`=?, `gcm_identificacion_ciudadano`=?, `gcm_numero_transaccion`=?, `gcm_tipo_monitoreo`=?, `gcm_observaciones_info`=? WHERE `gcm_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('ssssssss', $analista, $fecha_interaccion, $dependencia, $identificacion_ciudadano, $numero_interaccion, $tipo_monitoreo, $observaciones, $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
    
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

  $consulta_string="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE TMC.`gcm_id`=?";
 
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_usuario_red` FROM `administrador_usuario` WHERE `usu_estado`='Activo' AND (`usu_cargo_rol` LIKE '%AGENTE%' OR `usu_cargo_rol`='INTERPRETE' OR `usu_cargo_rol`='FORMADOR') ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  $fecha_minimo=date("Y-m-d", strtotime("- 1 year", strtotime(date('Y-m-d'))));
  $fecha_control=date("Y-m-d", strtotime("- 20 day", strtotime(date('Y-m-d'))));
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
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="matriz" class="my-0">Matriz</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="matriz" id="matriz" value="<?php echo $resultado_registros[0][2]; ?>" required readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="canal" class="my-0">Canal</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="canal" id="canal" value="<?php echo $resultado_registros[0][47]; ?>" required readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="dependencia" class="my-0">Dependencia</label>
                                <select class="form-control form-control-sm form-select" name="dependencia" id="dependencia" required>
                                  <option value="">Seleccione</option>
                                  <option value="Reparto" <?php if($resultado_registros[0][5]=="Reparto"){ echo "selected"; } ?>>Reparto</option>
                                  <option value="Jóvenes" <?php if($resultado_registros[0][5]=="Jóvenes"){ echo "selected"; } ?>>Jóvenes</option>
                                  <option value="Ingreso Solidario" <?php if($resultado_registros[0][5]=="Ingreso Solidario"){ echo "selected"; } ?>>Ingreso Solidario</option>
                                  <option value="Adulto Mayor" <?php if($resultado_registros[0][5]=="Adulto Mayor"){ echo "selected"; } ?>>Adulto Mayor</option>
                                  <option value="IVA" <?php if($resultado_registros[0][5]=="IVA"){ echo "selected"; } ?>>IVA</option>
                                  <option value="Focalización" <?php if($resultado_registros[0][5]=="Focalización"){ echo "selected"; } ?>>Focalización</option>
                                  <option value="No aplica" <?php if($resultado_registros[0][5]=="No aplica"){ echo "selected"; } ?>>No aplica</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="numero_interaccion" class="my-0">Número interacción</label>
                              <input type="text" class="form-control form-control-sm" name="numero_interaccion" id="numero_interaccion" value="<?php echo $resultado_registros[0][7]; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="identificacion_ciudadano" class="my-0">Identificación ciudadano</label>
                              <input type="text" class="form-control form-control-sm" name="identificacion_ciudadano" id="identificacion_ciudadano" value="<?php echo $resultado_registros[0][6]; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="fecha_interaccion" class="my-0">Fecha interacción</label>
                              <input type="date" class="form-control form-control-sm" name="fecha_interaccion" id="fecha_interaccion" value="<?php echo $resultado_registros[0][4]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="analista" class="my-0">Agente</label>
                                <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="analista" id="analista" required>
                                  <option value="" class="font-size-11">Seleccione</option>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?> 
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" class="font-size-11" data-tokens="<?php echo $resultado_registros_analistas[$i][0].' '.$resultado_registros_analistas[$i][1].' '.$resultado_registros_analistas[$i][2]; ?>" <?php if($resultado_registros[0][3]==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tipo_monitoreo" class="my-0">Tipo monitoreo</label>
                                <select class="form-control form-control-sm form-select" name="tipo_monitoreo" id="tipo_monitoreo" required>
                                  <option value="">Seleccione</option>
                                  <?php if($permisos_usuario=="Gestor" OR $permisos_usuario=="Administrador"): ?>
                                    <option value="Muestra aleatoria" <?php if($resultado_registros[0][8]=="Muestra aleatoria"){ echo "selected"; } ?>>Muestra aleatoria</option>
                                    <option value="Focalizado" <?php if($resultado_registros[0][8]=="Focalizado"){ echo "selected"; } ?>>Focalizado</option>
                                  <?php endif; ?>
                                  <option value="En línea" <?php if($resultado_registros[0][8]=="En línea"){ echo "selected"; } ?>>En línea</option>
                                  <option value="Al lado" <?php if($resultado_registros[0][8]=="Al lado"){ echo "selected"; } ?>>Al lado</option>
                                  <option value="Calibración-Escucha 1" <?php if($resultado_registros[0][8]=="Calibración-Escucha 1"){ echo "selected"; } ?>>Calibración-Escucha 1</option>
                                  <option value="Calibración-Escucha 2" <?php if($resultado_registros[0][8]=="Calibración-Escucha 2"){ echo "selected"; } ?>>Calibración-Escucha 2</option>
                                <?php if($permisos_usuario=="Gestor" OR $permisos_usuario=="Administrador" OR $permisos_usuario=="Supervisor"): ?>
                                    <option value="Seguimiento" <?php if($resultado_registros[0][8]=="Seguimiento"){ echo "selected"; } ?>>Seguimiento</option>
                                <?php endif; ?>
                                <option value="Nuevos" <?php if($resultado_registros[0][8]=="Nuevos"){ echo "selected"; } ?>>Nuevos</option>
                                <option value="Indicador AE" <?php if($resultado_registros[0][8]=="Indicador AE"){ echo "selected"; } ?>>Indicador AE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones"><?php echo $resultado_registros[0][34]; ?></textarea>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Guardar</button>
                                <?php if(isset($_POST["guardar_registro"])): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php endif; ?>
                                <?php if(!isset($_POST["guardar_registro"])): ?>
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
