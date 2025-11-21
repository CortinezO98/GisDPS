<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Calidad-Monitoreos";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));

    $consulta_string_monitoreo="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE TMC.`gcm_id`=?";
    $consulta_registros_monitoreo = $enlace_db->prepare($consulta_string_monitoreo);
    $consulta_registros_monitoreo->bind_param("s", $id_registro);
    $consulta_registros_monitoreo->execute();
    $resultado_registros_monitoreo = $consulta_registros_monitoreo->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_evaluacion="SELECT `gcmc_id`, `gcmc_monitoreo`, `gcmc_pregunta`, `gcmc_respuesta`, `gcmc_afectaciones`, `gcmc_comentarios`, TIM.`gcmi_matriz`, TIM.`gcmi_item_tipo`, TIM.`gcmi_item_consecutivo`, TIM.`gcmi_item_orden`, TIM.`gcmi_descripcion`, TIM.`gcmi_peso`, TIM.`gcmi_calificable` FROM `gestion_calidad_monitoreo_calificaciones` LEFT JOIN `gestion_calidad_matriz_item` AS TIM ON `gestion_calidad_monitoreo_calificaciones`.`gcmc_pregunta`=TIM.`gcmi_id` WHERE `gcmc_monitoreo`=? AND TIM.`gcmi_matriz`=? ORDER BY `gcmi_item_consecutivo` ASC";
    $consulta_registros_evaluacion = $enlace_db->prepare($consulta_string_evaluacion);
    $consulta_registros_evaluacion->bind_param("ss", $id_registro, $resultado_registros_monitoreo[0][1]);
    $consulta_registros_evaluacion->execute();
    $resultado_registros_evaluacion = $consulta_registros_evaluacion->get_result()->fetch_all(MYSQLI_NUM);

    for ($i=0; $i < count($resultado_registros_evaluacion); $i++) {
        $array_respuestas[$resultado_registros_evaluacion[$i][2]]=$resultado_registros_evaluacion[$i][3];
        $array_comentarios[$resultado_registros_evaluacion[$i][2]]=$resultado_registros_evaluacion[$i][5];
    }

    $consulta_string_matriz="SELECT `gcmi_id`, `gcmi_matriz`, `gcmi_item_tipo`, `gcmi_item_consecutivo`, `gcmi_item_orden`, `gcmi_descripcion`, `gcmi_peso`, `gcmi_calificable`, `gcmi_grupo_peso`, `gcmi_visible`, `gcmi_tipo_error`, `gcmi_grupo_id`, `gcmi_subgrupo_id`, `gcmi_item_id`, `gcmi_subitem_id` FROM `gestion_calidad_matriz_item` WHERE `gcmi_matriz`=? ORDER BY `gcmi_item_consecutivo` ASC";
    $consulta_registros_matriz = $enlace_db->prepare($consulta_string_matriz);
    $consulta_registros_matriz->bind_param("s", $resultado_registros_monitoreo[0][1]);
    $consulta_registros_matriz->execute();
    $resultado_registros_matriz = $consulta_registros_matriz->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_historial="SELECT `gcmh_id`, `gcmh_monitoreo`, `gcmh_tipo_cambio`, `gcmh_comentarios`, `gcmh_registro_usuario`, `gcmh_registro_fecha`, TUR.`usu_nombres_apellidos` FROM `gestion_calidad_monitoreo_historial` LEFT JOIN `administrador_usuario` AS TUR ON `gestion_calidad_monitoreo_historial`.`gcmh_registro_usuario`=TUR.`usu_id` WHERE `gcmh_monitoreo`=? ORDER BY `gcmh_registro_fecha` DESC";
    $consulta_registros_historial = $enlace_db->prepare($consulta_string_historial);
    $consulta_registros_historial->bind_param("s", $id_registro);
    $consulta_registros_historial->execute();
    $resultado_registros_historial = $consulta_registros_historial->get_result()->fetch_all(MYSQLI_NUM);

    $observaciones="Revisión/visualización detalle de monitoreo";
    $tipo_cambio="Revisión";
    $resarcimiento='';
    $id_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

    // Prepara la sentencia insert historial
    $sentencia_insert_historial = $enlace_db->prepare("INSERT INTO `gestion_calidad_monitoreo_historial`(`gcmh_monitoreo`, `gcmh_tipo_cambio`, `gcmh_comentarios`, `gcmh_resarcimiento`, `gcmh_registro_usuario`) VALUES (?,?,?,?,?)");
    // Agrega variables a sentencia preparada
    $sentencia_insert_historial->bind_param('sssss', $id_registro, $tipo_cambio, $observaciones, $resarcimiento, $id_usuario);
    $sentencia_insert_historial->execute();
?>
<div class="row px-4 py-2">
    <div class="col-md-4">
        <div class="row">
          <div class="col-md-12 p-1">
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
            <p class="alert background-principal color-blanco py-1 px-2 my-1"><span class="fas fa-history"></span> Historial de Gestión</p>
            <?php if (count($resultado_registros_historial)>0): ?>
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th class="p-1 font-size-11">Tipo</th>
                            <th class="p-1 font-size-11">Observaciones</th>
                            <th class="p-1 font-size-11">Usuario Registro</th>
                            <th class="p-1 font-size-11">Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for ($i=0; $i < count($resultado_registros_historial); $i++) { 
                        ?>
                        <tr>
                            <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][2]; ?></td>
                            <td class="p-1 font-size-11"><?php echo nl2br($resultado_registros_historial[$i][3]); ?></td>
                            <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][6]; ?></td>
                            <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][5]; ?></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="alert alert-warning p-1 font-size-11">
                    <span class="fas fa-exclamation-triangle"></span> No se encontraron registros
                </p>
            <?php endif; ?>
          </div>
        </div>
    </div>
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-12 p-1">
          <?php if (count($resultado_registros_matriz)>0): ?>
              <table class="table table-bordered table-sm">
                  <thead>
                      <tr>
                          <th class="p-1 font-size-11" style="width: 50px;"></th>
                          <th class="p-1 font-size-11">Atributos de Evaluación</th>
                          <th class="p-1 font-size-11" style="width: 100px;">Peso</th>
                          <th class="p-1 font-size-11" style="width: 50px;">Si</th>
                          <th class="p-1 font-size-11" style="width: 50px;">No</th>
                          <th class="p-1 font-size-11" style="width: 300px;">Comentarios</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php
                          for ($i=0; $i < count($resultado_registros_matriz); $i++) { 
                      ?>
                      <?php if($resultado_registros_matriz[$i][9]=="Si"): ?>
                      <tr class="<?php if($resultado_registros_matriz[$i][2]=='Grupo'){echo'matriz-grupo';} elseif($resultado_registros_matriz[$i][2]=='Sub-Grupo'){echo 'matriz-grupo-sub';} elseif($resultado_registros_matriz[$i][2]=='Item'){echo 'matriz-item';}?>">
                          <td class="p-1 font-size-11"><?php echo $resultado_registros_matriz[$i][3]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros_matriz[$i][5]; ?></td>
                          <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros_matriz[$i][6]; ?>%</td>
                          <td class="p-1 font-size-11 text-center">
                              <?php if($array_respuestas[$resultado_registros_matriz[$i][0]]=="Si"): ?>
                              <div class="form-group m-0 p-0">
                                  <div class="form-group custom-control custom-checkbox m-0">
                                      <input type="radio" class="custom-control-input" id="customCheckreqsi<?php echo $i; ?>" name="respuesta_<?php echo $i; ?>" value="Si" <?php if($array_respuestas[$resultado_registros_matriz[$i][0]]=="Si"){ echo "checked"; } ?> disabled>
                                      <label class="custom-control-label p-0 m-0" for="customCheckreqsi<?php echo $i; ?>"></label>
                                  </div>
                              </div>
                              <?php endif; ?>
                          </td>
                          <td class="p-1 font-size-11 text-center">
                              <?php if($array_respuestas[$resultado_registros_matriz[$i][0]]=="No"): ?>
                              <div class="form-group m-0 p-0">
                                  <div class="form-group custom-control custom-checkbox m-0">
                                      <input type="radio" class="custom-control-input" id="customCheckreqno<?php echo $i; ?>" name="respuesta_<?php echo $i; ?>" value="No" <?php if($array_respuestas[$resultado_registros_matriz[$i][0]]=="No"){ echo "checked"; } ?> disabled>
                                      <label class="custom-control-label p-0 m-0" for="customCheckreqno<?php echo $i; ?>"></label>
                                  </div>
                              </div>
                              <?php endif; ?>
                          </td>
                          <td class="p-1 font-size-11">
                              <?php
                                  if($array_respuestas[$resultado_registros_matriz[$i][0]]=="No") {
                                      echo $array_comentarios[$resultado_registros_matriz[$i][0]];
                                  }
                              ?>
                          </td>
                      </tr>
                      <?php endif; ?>
                      <?php
                          }
                      ?>
                  </tbody>
              </table>
          <?php else: ?>
              <p class="alert alert-warning p-1">
                  <span class="fas fa-exclamation-triangle"></span> No se encontraron registros
              </p>
          <?php endif; ?>
        </div>
      </div>
    </div>
</div>