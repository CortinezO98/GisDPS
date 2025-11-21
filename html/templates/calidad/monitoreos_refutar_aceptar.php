<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Monitoreos";
  require_once("../../iniciador.php");
  require_once("../../app/functions/validar_festivos.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Monitoreos | Refutado-Aceptar";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="monitoreos?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);

  $consulta_string_monitoreo="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal`, TUR.`usu_correo_corporativo`, TUA.`usu_correo_corporativo`, TS.`usu_correo_corporativo` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE TMC.`gcm_id`=?";

  $consulta_registros_monitoreo = $enlace_db->prepare($consulta_string_monitoreo);
  $consulta_registros_monitoreo->bind_param("s", $id_registro);
  $consulta_registros_monitoreo->execute();
  $resultado_registros_monitoreo = $consulta_registros_monitoreo->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
      $token=validar_input($_POST['token']);
      $observaciones=validar_input($_POST['observaciones']);
      $observaciones_refute=validar_input($_POST['observaciones_refute']);
      $tipo_cambio="Refutar-Aceptado";
      $id_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

      if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']!=1){
          $items_matriz=$_POST['id_campos'];
          $grupo_peso=$_POST['grupo_peso'];
          $peso_nota=$_POST['peso_nota'];
          $tipo_error=$_POST['tipo_error'];

          for ($i=0; $i < count($items_matriz); $i++) { 
              if ($grupo_peso[$i]!="") {
                  $grupos_items_nota['G-'.$grupo_peso[$i]]=100;
                  $grupos_peso_id[]='G-'.$grupo_peso[$i];
              }

              if (isset($_POST['respuesta_'.$items_matriz[$i]])) {
                  $item_respuesta[]=$_POST['respuesta_'.$items_matriz[$i]];
                  $item_id_respuesta[$items_matriz[$i]]=$_POST['respuesta_'.$items_matriz[$i]];
              } else {
                  $item_respuesta[]="";
                  $item_id_respuesta[$items_matriz[$i]]="";
              }

              if (isset($_POST['comentario_'.$items_matriz[$i]])) {
                  $item_comentario[]=$_POST['comentario_'.$items_matriz[$i]];
              } else {
                  $item_comentario[]="";
              }
          }

          $grupos_peso_id=array_values(array_unique($grupos_peso_id));

          for ($i=0; $i < count($items_matriz); $i++) { 
              if ($grupo_peso[$i]=="") {
                  if ($item_respuesta[$i]=="No") {
                      $item_calificable_tipo_error[$tipo_error[$i]][$items_matriz[$i]]=0;
                  } else {
                      $item_calificable_tipo_error[$tipo_error[$i]][$items_matriz[$i]]=$peso_nota[$i];
                  }
              } else {
                  $item_calificable_tipo_error[$tipo_error[$i]]['G-'.$grupo_peso[$i]]=$peso_nota[$i];
              }

              if ($grupo_peso[$i]!="" and $item_respuesta[$i]=="No") {
                  $grupos_items_nota['G-'.$grupo_peso[$i]]=0;
              }
          }

          for ($i=0; $i < count($grupos_peso_id); $i++) { 
              for ($j=0; $j < count($tipo_error); $j++) { 
                  if (isset($item_calificable_tipo_error[$tipo_error[$j]][$grupos_peso_id[$i]])) {
                      if ($grupos_items_nota[$grupos_peso_id[$i]]==0) {
                          $item_calificable_tipo_error[$tipo_error[$j]][$grupos_peso_id[$i]]=0;
                      }
                  }
              }
          }
          
          if (isset($item_calificable_tipo_error['ENC'])) {
              if (count($item_calificable_tipo_error['ENC'])>0) {
                  $gcm_nota_enc=array_sum($item_calificable_tipo_error['ENC']);
              } else {
                  $gcm_nota_enc="NA";
              }
          } else {
              $gcm_nota_enc="NA";
          }
                        
                 
          if (isset($item_calificable_tipo_error['ECU'])) {
              if (count($item_calificable_tipo_error['ECU'])>0) {
                  $gcm_nota_ecuf=array_sum($item_calificable_tipo_error['ECU']);
              } else {
                  $gcm_nota_ecuf="NA";
              }
          } else {
              $gcm_nota_ecuf="NA";
          }

          if (isset($item_calificable_tipo_error['ECN'])) {
              if (count($item_calificable_tipo_error['ECN'])>0) {
                  $gcm_nota_ecn=array_sum($item_calificable_tipo_error['ECN']);
              } else {
                  $gcm_nota_ecn="NA";
              }
          } else {
              $gcm_nota_ecn="NA";
          }

          if ($gcm_nota_enc==="NA") {
              $control_estado_enc=1;
          } else {
              if ($gcm_nota_enc==100) {
                  $control_estado_enc=1;
              } else {
                  $control_estado_enc=0;
              }
          }

          if ($gcm_nota_ecuf==="NA") {
              $control_estado_ecuf=1;
          } else {
              if ($gcm_nota_ecuf==100) {
                  $control_estado_ecuf=1;
              } else {
                  $control_estado_ecuf=0;
              }
          }

          if ($gcm_nota_ecn==="NA") {
              $control_estado_ecn=1;
          } else {
              if ($gcm_nota_ecn==100) {
                  $control_estado_ecn=1;
              } else {
                  $control_estado_ecn=0;
              }
          }

          if ($control_estado_enc==1 AND $control_estado_ecuf==1 AND $control_estado_ecn==1) {
              $gcm_estado="Aceptado";
          } else {
              $gcm_estado="Refutado-Aceptado";
          }

          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_calidad_monitoreo_historial`(`gcmh_monitoreo`, `gcmh_tipo_cambio`, `gcmh_comentarios`, `gcmh_resarcimiento`, `gcmh_registro_usuario`) VALUES (?,?,?,'',?)");

          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssss', $id_registro, $tipo_cambio, $observaciones_refute, $id_usuario);
          
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
              $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_calidad_monitoreo` SET `gcm_observaciones_monitoreo`=?, `gcm_nota_enc`=?, `gcm_nota_ecn`=?, `gcm_nota_ecuf`=?, `gcm_nota_enc_estado`=?, `gcm_nota_ecn_estado`=?, `gcm_nota_ecuf_estado`=?, `gcm_estado`=?, `gcm_fecha_calidad_reac`='".date('Y-m-d H:i:s')."' WHERE `gcm_id`=?");

              // Agrega variables a sentencia preparada
              $consulta_actualizar->bind_param('sssssssss', $observaciones, $gcm_nota_enc, $gcm_nota_ecn, $gcm_nota_ecuf, $control_estado_enc, $control_estado_ecn, $control_estado_ecuf, $gcm_estado, $id_registro);
              
              // Ejecuta sentencia preparada
              $consulta_actualizar->execute();

              if (comprobarSentencia($enlace_db->info)) {
                  $control_insert=0;
          
                  for ($i=0; $i < count($items_matriz); $i++) {
                      unset($sentencia_insert_calificaciones);
                      
                      $item_matriz_pregunta=$items_matriz[$i];
                      $respuesta_item=$item_respuesta[$i];
                      $comentarios_insert=$item_comentario[$i];

                      // Prepara la sentencia
                      $sentencia_insert_calificaciones = $enlace_db->prepare("UPDATE `gestion_calidad_monitoreo_calificaciones` SET `gcmc_respuesta`=?,`gcmc_comentarios`=? WHERE `gcmc_pregunta`=? AND `gcmc_monitoreo`=?");

                      // Agrega variables a sentencia preparada
                      $sentencia_insert_calificaciones->bind_param('ssss', $respuesta_item, $comentarios_insert, $item_matriz_pregunta, $id_registro);

                      $sentencia_insert_calificaciones->execute();

                      if (comprobarSentencia($enlace_db->info)) {
                          $control_insert++;
                      }
                  }

                  if (count($items_matriz)==$control_insert) {
                      //insert log eventos
                          $consulta_string_log = "INSERT INTO `administrador_log`(`clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_registro_usuario`) VALUES (?,?,?,?,?)";
                      
                          $log_modulo=$modulo_plataforma;
                          $log_tipo="editar";
                          $log_accion="Editar registro";
                          $log_detalle="Actualizó evaluación monitoreo [".$id_registro."]";
                          $log_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
                          
                          $consulta_registros_log = $enlace_db->prepare($consulta_string_log);
                          $consulta_registros_log->bind_param("sssss", $log_modulo, $log_tipo, $log_accion, $log_detalle, $log_usuario);
                          $consulta_registros_log->execute();
                      //insert log eventos
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

                      //PROGRAMACIÓN NOTIFICACIÓN
                      $asunto='Refutado-Aceptado | '.$id_registro;
                      $referencia='Monitoreo Refutado-Aceptado';
                      $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha revisado el siguiente monitoreo:</p>
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
                      $nc_address=$resultado_registros_monitoreo[0][49].";";
                      $nc_cc=$resultado_registros_monitoreo[0][50].";".$resultado_registros_monitoreo[0][48].";";
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
            <div class="col-lg-8 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <?php if (count($resultado_registros_matriz)>0): ?>
                          <div class="table-responsive">
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
                                          <?php if($resultado_registros_matriz[$i][7]=="Si"): ?>
                                              <input type="hidden" name="id_campos[]" value="<?php echo $resultado_registros_matriz[$i][0]; ?>">
                                              <input type="hidden" name="grupo_peso[]" value="<?php echo $resultado_registros_matriz[$i][8]; ?>">
                                              <input type="hidden" name="peso_nota[]" value="<?php echo $resultado_registros_matriz[$i][6]; ?>">
                                              <input type="hidden" name="tipo_error[]" value="<?php echo $resultado_registros_matriz[$i][10]; ?>">
                                          <?php endif; ?>
                                      <tr class="<?php if($resultado_registros_matriz[$i][2]=='Grupo'){echo'matriz-grupo';} elseif($resultado_registros_matriz[$i][2]=='Sub-Grupo'){echo'matriz-grupo-sub';} elseif($resultado_registros_matriz[$i][2]=='Item'){echo'matriz-item';}?>">
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_matriz[$i][3]; ?></td>
                                          <td class="p-1 font-size-11"><?php echo $resultado_registros_matriz[$i][5]; ?></td>
                                          <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros_matriz[$i][6]; ?>%</td>
                                          <td class="p-1 font-size-11 text-center">
                                              <?php if($resultado_registros_matriz[$i][7]=="Si"): ?>
                                              <div class="form-group m-0 p-0">
                                                  <div class="form-group custom-control custom-checkbox m-0">
                                                      <input type="radio" class="custom-control-input" id="customCheckreqsi<?php echo $i; ?>" name="respuesta_<?php echo $resultado_registros_matriz[$i][0]; ?>" value="Si" <?php if($array_respuestas[$resultado_registros_matriz[$i][0]]=="Si"){ echo "checked"; } ?> onclick="validar_comentario('Si', '<?php echo $i; ?>');" required <?php if($_SESSION['registro_creado_cambio_estado']==1) { echo 'disabled'; } ?>>
                                                      <label class="custom-control-label p-0 m-0" for="customCheckreqsi<?php echo $i; ?>"></label>
                                                  </div>
                                              </div>
                                              <?php endif; ?>
                                          </td>
                                          <td class="p-1 font-size-11 text-center">
                                              <?php if($resultado_registros_matriz[$i][7]=="Si"): ?>
                                              <div class="form-group m-0 p-0">
                                                  <div class="form-group custom-control custom-checkbox m-0">
                                                      <input type="radio" class="custom-control-input" id="customCheckreqno<?php echo $i; ?>" name="respuesta_<?php echo $resultado_registros_matriz[$i][0]; ?>" value="No" <?php if($array_respuestas[$resultado_registros_matriz[$i][0]]=="No"){ echo "checked"; } ?> onclick="validar_comentario('No', '<?php echo $i; ?>');" required <?php if($_SESSION['registro_creado_cambio_estado']==1) { echo 'disabled'; } ?>>
                                                      <label class="custom-control-label p-0 m-0" for="customCheckreqno<?php echo $i; ?>"></label>
                                                  </div>
                                              </div>
                                              <?php endif; ?>
                                          </td>
                                          <td class="p-1 font-size-11">
                                              <?php if($resultado_registros_matriz[$i][7]=="Si"): ?>
                                              <input type="text" class="form-control form-control-sm color-rojo <?php if($array_respuestas[$resultado_registros_matriz[$i][0]]=="Si"){ echo "d-none"; } ?>" name="comentario_<?php echo $resultado_registros_matriz[$i][0]; ?>" id="comentario_<?php echo $i; ?>" value="<?php echo $array_comentarios[$resultado_registros_matriz[$i][0]]; ?>" maxlength="2000" required <?php if($_SESSION['registro_creado_cambio_estado']==1) { echo 'readonly'; } ?> <?php if($array_respuestas[$resultado_registros_matriz[$i][0]]=="Si"){ echo "disabled"; } ?>>
                                              <?php endif; ?>
                                          </td>
                                      </tr>
                                      <?php endif; ?>
                                      <?php
                                          }
                                      ?>
                                  </tbody>
                              </table>
                          </div>
                      <?php else: ?>
                          <p class="alert alert-warning p-1">
                              <span class="fas fa-exclamation-triangle"></span> No se encontraron registros
                          </p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="observaciones">Observaciones monitoreo</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']==1) { echo 'readonly'; } ?>><?php echo $resultado_registros_monitoreo[0][9]; ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="observaciones_refute">Observaciones Refutado-Aceptar</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones_refute" id="observaciones_refute" <?php if($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']==1) { echo 'readonly'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $observaciones_refute; } ?></textarea>
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
                              <a href="#" onClick="open_modal_detalle('<?php echo base64_encode($resultado_registros_monitoreo[0][0]); ?>');" class="btn btn-warning float-end me-1 mt-1" title="Detalle Monitoreo"><?php echo $resultado_registros_monitoreo[0][0]; ?></a>
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

      function validar_comentario(tipo, id_elemento){
            if (tipo=="Si") {
                $("#comentario_"+id_elemento).removeClass('d-block').addClass('d-none');
                document.getElementById("comentario_"+id_elemento).disabled = true;
            } else {
                $("#comentario_"+id_elemento).removeClass('d-none').addClass('d-block');
                document.getElementById("comentario_"+id_elemento).disabled = false;
            }
        }
  </script>
</body>
</html>