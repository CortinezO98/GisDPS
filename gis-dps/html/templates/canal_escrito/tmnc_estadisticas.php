<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Transferencias Monetarias No Condicionadas | Estadísticas";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  // error_reporting(E_ALL);

  if(isset($_POST["filtro"])){ 
    $fecha_inicio=validar_input($_POST['fecha_inicio']);
    $fecha_fin=validar_input($_POST['fecha_fin']);
    
    $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']=$fecha_inicio;
    $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']=$fecha_fin;

    header("Location: tmnc_estadisticas");
  } elseif($_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']=="" OR $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']=="") {
    $anio_mes_separado=explode("-", date('Y-m-d'));
    $numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, $anio_mes_separado[1], $anio_mes_separado[0]); //cantidad de días del mes
    $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']=date('Y-m').'-01';
    $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']=date('Y-m').'-'.$numero_dias_mes;
  }
  
  // Inicializa variable tipo array
  $data_consulta=array();

  if ($_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']!="" AND $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']!="") {
    array_push($data_consulta, $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']);
    array_push($data_consulta, $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']);
    $filtro_fechas_p1=" AND `cet_registro_fecha`>=? AND `cet_registro_fecha`<=?";
    $filtro_fechas_p2=" AND `cetar_registro_fecha`>=? AND `cetar_registro_fecha`<=?";
    $filtro_fechas_p3=" AND `cetc_registro_fecha`>=? AND `cetc_registro_fecha`<=?";
    $filtro_fechas_p4=" AND `cete_registro_fecha`>=? AND `cete_registro_fecha`<=?";
    $filtro_fechas_p5=" AND `cetfr_usuario_fecha`>=? AND `cetfr_usuario_fecha`<=?";
    $filtro_fechas_p6=" AND `cetpc_registro_fecha`>=? AND `cetpc_registro_fecha`<=?";
    $filtro_fechas_p7=" AND `cetcsg_registro_fecha`>=? AND `cetcsg_registro_fecha`<=?";
    $filtro_fechas_p8=" AND `cetan_registro_fecha`>=? AND `cetan_registro_fecha`<=?";
  }
  
  
  //CONSTRUIR ARRAY AÑO-MES-DIA
    $dia_control=$_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio'];
    while (date('Y-m-d', strtotime($dia_control))<=date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']))) {
        $array_dias_mes[]=$dia_control;
        $array_dias_mes_data[$dia_control]=0;

        $dia_control = date("Y-m-d", strtotime("+ 1 day", strtotime($dia_control)));
    }

  //CONSTRUIR HORARIO
    for ($i=0; $i < 24; $i++) { 
      $array_anio_mes_hora_num[]=validar_cero($i);
      $array_anio_mes_hora_val[]=0;
    }

  //INICIALIZA ARRAYS
    $array_datos_total['p1']['total_gestion']=0;
    $array_datos_total['p1']['total_dias_gestion']=0;
    $array_datos_total['p1']['promedio_diario']=0;
    $array_datos_total['p1']['promedio_hora']=0;
    $array_datos_total['p1']['promedio_agente']=0;
    $array_datos_total['p1']['total_agente']=0;

    $array_datos_total['p2']['total_gestion']=0;
    $array_datos_total['p2']['total_dias_gestion']=0;
    $array_datos_total['p2']['promedio_diario']=0;
    $array_datos_total['p2']['promedio_hora']=0;
    $array_datos_total['p2']['promedio_agente']=0;
    $array_datos_total['p2']['total_agente']=0;

    $array_datos_total['p3']['total_gestion']=0;
    $array_datos_total['p3']['total_dias_gestion']=0;
    $array_datos_total['p3']['promedio_diario']=0;
    $array_datos_total['p3']['promedio_hora']=0;
    $array_datos_total['p3']['promedio_agente']=0;
    $array_datos_total['p3']['total_agente']=0;

    $array_datos_total['p4']['total_gestion']=0;
    $array_datos_total['p4']['total_dias_gestion']=0;
    $array_datos_total['p4']['promedio_diario']=0;
    $array_datos_total['p4']['promedio_hora']=0;
    $array_datos_total['p4']['promedio_agente']=0;
    $array_datos_total['p4']['total_agente']=0;

    $array_datos_total['p5']['total_gestion']=0;
    $array_datos_total['p5']['total_dias_gestion']=0;
    $array_datos_total['p5']['promedio_diario']=0;
    $array_datos_total['p5']['promedio_hora']=0;
    $array_datos_total['p5']['promedio_agente']=0;
    $array_datos_total['p5']['total_agente']=0;

    $array_datos_total['p6']['total_gestion']=0;
    $array_datos_total['p6']['total_dias_gestion']=0;
    $array_datos_total['p6']['promedio_diario']=0;
    $array_datos_total['p6']['promedio_hora']=0;
    $array_datos_total['p6']['promedio_agente']=0;
    $array_datos_total['p6']['total_agente']=0;

    $array_datos_total['p7']['total_gestion']=0;
    $array_datos_total['p7']['total_dias_gestion']=0;
    $array_datos_total['p7']['promedio_diario']=0;
    $array_datos_total['p7']['promedio_hora']=0;
    $array_datos_total['p7']['promedio_agente']=0;
    $array_datos_total['p7']['total_agente']=0;

    $array_datos_total['p8']['total_gestion']=0;
    $array_datos_total['p8']['total_dias_gestion']=0;
    $array_datos_total['p8']['promedio_diario']=0;
    $array_datos_total['p8']['promedio_hora']=0;
    $array_datos_total['p8']['promedio_agente']=0;
    $array_datos_total['p8']['total_agente']=0;

    $array_datos_gestion['p1']['gestion_agente']['id']=array();
    $array_datos_gestion['p1']['novedad_lista']=array();
    $array_datos_gestion['p1']['grupo_responsable_lista']=array();
    $array_datos_gestion['p2']['gestion_agente']['id']=array();
    $array_datos_gestion['p2']['proyector_lista']=array();
    $array_datos_gestion['p2']['estado_lista']=array();
    $array_datos_gestion['p2']['agente_lista']=array();
    $array_datos_gestion['p2']['carta_respuesta_lista']=array();
    $array_datos_gestion['p3']['gestion_agente']['id']=array();
    $array_datos_gestion['p3']['plantilla_lista']=array();
    $array_datos_gestion['p3']['motivo_devo_correo_lista']=array();
    $array_datos_gestion['p4']['gestion_agente']['id']=array();
    $array_datos_gestion['p4']['tipo_respuesta_lista']=array();
    $array_datos_gestion['p5']['gestion_agente']['id']=array();
    $array_datos_gestion['p5']['modulo_lista']=array();
    $array_datos_gestion['p5']['git_lista']=array();
    $array_datos_gestion['p6']['gestion_agente']['id']=array();
    $array_datos_gestion['p7']['gestion_agente']['id']=array();
    $array_datos_gestion['p7']['proceso_lista']=array();
    $array_datos_gestion['p7']['causal_no_proyeccion_lista']=array();
    $array_datos_gestion['p7']['causal_no_envio_lista']=array();
    $array_datos_gestion['p7']['proyector_lista']=array();
    $array_datos_gestion['p7']['envio_lista']=array();
    $array_datos_gestion['p8']['gestion_agente']['id']=array();
    $array_datos_gestion['p8']['tipo_novedad_lista']=array();
    $array_datos_gestion['p8']['datos_basicos_lista']=array();
    $array_datos_gestion['p8']['reactivacion_lista']=array();
    $array_datos_gestion['p8']['retiro_lista']=array();
    $array_datos_gestion['p8']['suspension_lista']=array();

  //1. Proyección de Respuestas
    $consulta_string_p1="SELECT `cet_id`, `cet_radicado_entrada`, `cet_abogado_aprobacion`, `cet_documento_identidad`, `cet_nombre_ciudadano`, `cet_correo_direccion`, `cet_programa_solicitud`, `cet_plantilla`, `cet_con_datos`, `cet_datos_incompletos`, `cet_plantilla_compensacion_iva`, `cet_plantilla_adulto_mayor`, `cet_novedad_radicado`, `cet_motivo_archivo`, `cet_tipo_entidad`, `cet_id_solicitud`, `cet_observaciones`, `cet_notificar`, `cet_registro_usuario`, `cet_registro_fecha`, ABOGADOAPROBACION.`ceco_valor`, PROGRAMASOLICITUD.`ceco_valor`, PLANTILLA.`ceco_valor`, CONDATOS.`ceco_valor`, DATOSINCOMPLETOS.`ceco_valor`, PCOMPENSACIONIVA.`ceco_valor`, PADULTOMAYOR.`ceco_valor`, NOVEDADRADICADO.`ceco_valor`, TIPOENTIDAD.`ceco_valor`, TU.`usu_nombres_apellidos`, PRENTA.`ceco_valor` FROM `gestion_cetmnc_proyeccion_respuestas`
       LEFT JOIN `gestion_ce_configuracion` AS ABOGADOAPROBACION ON `gestion_cetmnc_proyeccion_respuestas`.`cet_abogado_aprobacion`=ABOGADOAPROBACION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_proyeccion_respuestas`.`cet_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA ON `gestion_cetmnc_proyeccion_respuestas`.`cet_plantilla`=PLANTILLA.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS CONDATOS ON `gestion_cetmnc_proyeccion_respuestas`.`cet_con_datos`=CONDATOS.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS DATOSINCOMPLETOS ON `gestion_cetmnc_proyeccion_respuestas`.`cet_datos_incompletos`=DATOSINCOMPLETOS.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS PCOMPENSACIONIVA ON `gestion_cetmnc_proyeccion_respuestas`.`cet_plantilla_compensacion_iva`=PCOMPENSACIONIVA.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS PADULTOMAYOR ON `gestion_cetmnc_proyeccion_respuestas`.`cet_plantilla_adulto_mayor`=PADULTOMAYOR.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS PRENTA ON `gestion_cetmnc_proyeccion_respuestas`.`cet_plantilla_renta_ciudadana`=PRENTA.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS NOVEDADRADICADO ON `gestion_cetmnc_proyeccion_respuestas`.`cet_novedad_radicado`=NOVEDADRADICADO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS TIPOENTIDAD ON `gestion_cetmnc_proyeccion_respuestas`.`cet_tipo_entidad`=TIPOENTIDAD.`ceco_id`
       LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_proyeccion_respuestas`.`cet_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p1."";
    $consulta_registros_p1 = $enlace_db->prepare($consulta_string_p1);
    if (count($data_consulta)>0) {
        $consulta_registros_p1->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p1->execute();
    $resultado_registros_p1 = $consulta_registros_p1->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p1)) {
      for ($i=0; $i < count($resultado_registros_p1); $i++) { 
        $array_datos_gestion['p1']['gestion_agente']['id'][]=$resultado_registros_p1[$i][18];
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][18]]['nombre']=$resultado_registros_p1[$i][29];
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][18]]['total']+=1;
        if (!isset($array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][18]]['hora'])) {
          $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][18]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][18]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][18]]['hora'][intval(date('H', strtotime($resultado_registros_p1[$i][19])))]+=1;
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][18]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p1[$i][19]))]+=1;
        
        $array_datos_gestiones['p1']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p1[$i][19]))]+=1;
        $array_datos_gestiones['p1']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p1[$i][19]))]=1;
        $array_datos_gestiones['p1']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p1[$i][19]))]=1;

        $array_datos_gestion['p1']['novedad_lista'][]=$resultado_registros_p1[$i][12];
        $array_datos_gestion['p1']['novedad_nombre'][$resultado_registros_p1[$i][12]]=$resultado_registros_p1[$i][27];
        $array_datos_gestion['p1']['novedad'][$resultado_registros_p1[$i][12]]+=1;

        $array_datos_gestion['p1']['grupo_responsable_lista'][]=$resultado_registros_p1[$i][6];
        $array_datos_gestion['p1']['grupo_responsable_nombre'][$resultado_registros_p1[$i][6]]=$resultado_registros_p1[$i][21];
        $array_datos_gestion['p1']['grupo_responsable'][$resultado_registros_p1[$i][6]]+=1;
      }

      $array_datos_gestion['p1']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p1']['gestion_agente']['id']));
      
      $array_datos_total['p1']['total_gestion']=count($resultado_registros_p1);
      $array_datos_total['p1']['total_dias_gestion']=count($array_datos_gestiones['p1']['total_dias']);
      $array_datos_total['p1']['promedio_diario']=count($resultado_registros_p1)/count($array_datos_gestiones['p1']['total_dias']);
      $array_datos_total['p1']['promedio_hora']=count($resultado_registros_p1)/count($array_datos_gestiones['p1']['total_dias_hora']);
      $array_datos_total['p1']['promedio_agente']=count($resultado_registros_p1)/count($array_datos_gestion['p1']['gestion_agente']['id']);
      $array_datos_total['p1']['total_agente']=count($array_datos_gestion['p1']['gestion_agente']['id']);

      $array_datos_gestion['p1']['novedad_lista']=array_values(array_unique($array_datos_gestion['p1']['novedad_lista']));
      $array_datos_gestion['p1']['grupo_responsable_lista']=array_values(array_unique($array_datos_gestion['p1']['grupo_responsable_lista']));
    }


  //2. Aprobación Respuesta
    $consulta_string_p2="SELECT `cetar_id`, `cetar_radicado`, `cetar_numero_documento`, `cetar_nombre_ciudadano`, `cetar_proyector`, `cetar_apoyo_prosperidad`, `cetar_ingreso_solidario`, `cetar_carta_respuesta`, `cetar_estado`, `cetar_comentario_aprobacion`, `cetar_motivo_rechazo`, `cetar_observaciones`, `cetar_notificar`, `cetar_registro_usuario`, `cetar_registro_fecha`, PROYECTOR.`ceco_valor`, APOYOPROSPERIDAD.`ceco_valor`, INGRESOSOLIDARIO.`ceco_valor`, CARTARESPUESTA.`ceco_valor`, ESTADO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_aprobacion_respuesta`
     LEFT JOIN `gestion_ce_configuracion` AS PROYECTOR ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_proyector`=PROYECTOR.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS APOYOPROSPERIDAD ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_apoyo_prosperidad`=APOYOPROSPERIDAD.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS INGRESOSOLIDARIO ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_ingreso_solidario`=INGRESOSOLIDARIO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS CARTARESPUESTA ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_carta_respuesta`=CARTARESPUESTA.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_estado`=ESTADO.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p2."";
    $consulta_registros_p2 = $enlace_db->prepare($consulta_string_p2);
    if (count($data_consulta)>0) {
        $consulta_registros_p2->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p2->execute();
    $resultado_registros_p2 = $consulta_registros_p2->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p2)) {
      for ($i=0; $i < count($resultado_registros_p2); $i++) { 
        $array_datos_gestion['p2']['gestion_agente']['id'][]=$resultado_registros_p2[$i][13];
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][13]]['nombre']=$resultado_registros_p2[$i][20];
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][13]]['total']+=1;
        if (!isset($array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][13]]['hora'])) {
          $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][13]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][13]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][13]]['hora'][intval(date('H', strtotime($resultado_registros_p2[$i][14])))]+=1;
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][13]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p2[$i][14]))]+=1;
        
        $array_datos_gestiones['p2']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p2[$i][14]))]+=1;
        $array_datos_gestiones['p2']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p2[$i][14]))]=1;
        $array_datos_gestiones['p2']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p2[$i][14]))]=1;

        $array_datos_gestion['p2']['proyector_lista'][]=$resultado_registros_p2[$i][4];
        $array_datos_gestion['p2']['proyector_nombre'][$resultado_registros_p2[$i][4]]=$resultado_registros_p2[$i][15];
        $array_datos_gestion['p2']['proyector'][$resultado_registros_p2[$i][4]]+=1;
        $array_datos_gestion['p2']['proyector_estado'][$resultado_registros_p2[$i][4]][$resultado_registros_p2[$i][8]]+=1;
        
        if ($resultado_registros_p2[$i][8]!="") {
          $array_datos_gestion['p2']['proyector_estado_total'][$resultado_registros_p2[$i][4]]+=1;
        }

        $array_datos_gestion['p2']['agente_lista'][]=$resultado_registros_p2[$i][13];
        $array_datos_gestion['p2']['agente_nombre'][$resultado_registros_p2[$i][13]]=$resultado_registros_p2[$i][20];
        $array_datos_gestion['p2']['agente'][$resultado_registros_p2[$i][13]][$resultado_registros_p2[$i][7]]+=1;

        $array_datos_gestion['p2']['carta_respuesta_lista'][]=$resultado_registros_p2[$i][7];
        $array_datos_gestion['p2']['carta_respuesta_nombre'][$resultado_registros_p2[$i][7]]=$resultado_registros_p2[$i][18];

        $array_datos_gestion['p2']['estado_lista'][]=$resultado_registros_p2[$i][8];
        $array_datos_gestion['p2']['estado_nombre'][$resultado_registros_p2[$i][8]]=$resultado_registros_p2[$i][19];
      }

      $array_datos_gestion['p2']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p2']['gestion_agente']['id']));
      
      $array_datos_total['p2']['total_gestion']=count($resultado_registros_p2);
      $array_datos_total['p2']['total_dias_gestion']=count($array_datos_gestiones['p2']['total_dias']);
      $array_datos_total['p2']['promedio_diario']=count($resultado_registros_p2)/count($array_datos_gestiones['p2']['total_dias']);
      $array_datos_total['p2']['promedio_hora']=count($resultado_registros_p2)/count($array_datos_gestiones['p2']['total_dias_hora']);
      $array_datos_total['p2']['promedio_agente']=count($resultado_registros_p2)/count($array_datos_gestion['p2']['gestion_agente']['id']);
      $array_datos_total['p2']['total_agente']=count($array_datos_gestion['p2']['gestion_agente']['id']);

      $array_datos_gestion['p2']['proyector_lista']=array_values(array_unique($array_datos_gestion['p2']['proyector_lista']));
      $array_datos_gestion['p2']['estado_lista']=array_values(array_unique($array_datos_gestion['p2']['estado_lista']));

      $array_datos_gestion['p2']['agente_lista']=array_values(array_unique($array_datos_gestion['p2']['agente_lista']));
      $array_datos_gestion['p2']['carta_respuesta_lista']=array_values(array_unique($array_datos_gestion['p2']['carta_respuesta_lista']));
    }


  //3. Clasificación
    $consulta_string_p3="SELECT `cetc_id`, `cetc_correo_electronico`, `cetc_fecha_ingreso_correo`, `cetc_nombre_ciudadano`, `cetc_cedula_consulta`, `cetc_asunto_correo`, `cetc_programa_solicitud`, `cetc_plantilla_utilizada`, `cetc_solicitud_ciudadano`, `cetc_plantilla_datos_incompletos`, `cetc_plantilla_datos_completos`, `cetc_parrafo_radicacion`, `cetc_parrafo_plantilla_1`, `cetc_parrafo_plantilla_4`, `cetc_parrafo_plantilla_5`, `cetc_parrafo_plantilla_6`, `cetc_situacion_plantilla_8`, `cetc_parrafo_plantilla_8`, `cetc_parrafo_plantilla_10`, `cetc_titular_hogar`, `cetc_parrafo_plantilla_14`, `cetc_parrafo_plantilla_16`, `cetc_situacion_plantilla_17`, `cetc_parrafo_plantilla_17`, `cetc_situacion_plantilla_18`, `cetc_parrafo_plantilla_18`, `cetc_parrafo_plantilla_20`, `cetc_nombre_solicitante`, `cetc_nombre_titular`, `cetc_parrafo_plantilla_21`, `cetc_situacion_plantilla_22`, `cetc_parrafo_plantilla_22`, `cetc_parrafo_plantilla_23`, `cetc_parrafo_plantilla_25`, `cetc_parrafo_plantilla_26`, `cetc_parrafo_plantilla_reemplazo`, `cetc_motivo_devolucion`, `cetc_observaciones`, `cetc_notificar`, `cetc_registro_usuario`, `cetc_registro_fecha`, PROGRAMASOLICITUD.`ceco_valor`, PUTILIZADA.`ceco_valor`, PDATOSINCOMPLETOS.`ceco_valor`, PDATOSCOMPLETOS.`ceco_valor`, PLANTILLA8.`ceco_valor`, PLANTILLA17.`ceco_valor`, PLANTILLA18.`ceco_valor`, PLANTILLA22.`ceco_valor`, MOTIVODEVOLUCION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_clasificacion`
     LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_clasificacion`.`cetc_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PUTILIZADA ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_utilizada`=PUTILIZADA.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PDATOSINCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_incompletos`=PDATOSINCOMPLETOS.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PDATOSCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_completos`=PDATOSCOMPLETOS.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA8 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_8`=PLANTILLA8.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_17`=PLANTILLA17.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_18`=PLANTILLA18.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA22 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_22`=PLANTILLA22.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS MOTIVODEVOLUCION ON `gestion_cetmnc_clasificacion`.`cetc_motivo_devolucion`=MOTIVODEVOLUCION.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_clasificacion`.`cetc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p3."";
    $consulta_registros_p3 = $enlace_db->prepare($consulta_string_p3);
    if (count($data_consulta)>0) {
        $consulta_registros_p3->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p3->execute();
    $resultado_registros_p3 = $consulta_registros_p3->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p3)) {
      for ($i=0; $i < count($resultado_registros_p3); $i++) { 
        $array_datos_gestion['p3']['gestion_agente']['id'][]=$resultado_registros_p3[$i][39];
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][39]]['nombre']=$resultado_registros_p3[$i][50];
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][39]]['total']+=1;
        if (!isset($array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][39]]['hora'])) {
          $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][39]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][39]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][39]]['hora'][intval(date('H', strtotime($resultado_registros_p3[$i][40])))]+=1;
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][39]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p3[$i][40]))]+=1;
        
        $array_datos_gestiones['p3']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p3[$i][40]))]+=1;
        $array_datos_gestiones['p3']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p3[$i][40]))]=1;
        $array_datos_gestiones['p3']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p3[$i][40]))]=1;

        $array_datos_gestion['p3']['plantilla_lista'][]=$resultado_registros_p3[$i][7];
        $array_datos_gestion['p3']['plantilla_nombre'][$resultado_registros_p3[$i][7]]=$resultado_registros_p3[$i][42];
        $array_datos_gestion['p3']['plantilla'][$resultado_registros_p3[$i][7]]+=1;

        if ($resultado_registros_p3[$i][36]!="") {
          $array_datos_gestion['p3']['motivo_devo_correo_lista'][]=$resultado_registros_p3[$i][36];
          $array_datos_gestion['p3']['motivo_devo_correo_nombre'][$resultado_registros_p3[$i][36]]=$resultado_registros_p3[$i][49];
          $array_datos_gestion['p3']['motivo_devo_correo'][$resultado_registros_p3[$i][36]]+=1;
        }
      }

      $array_datos_gestion['p3']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p3']['gestion_agente']['id']));
      
      $array_datos_total['p3']['total_gestion']=count($resultado_registros_p3);
      $array_datos_total['p3']['total_dias_gestion']=count($array_datos_gestiones['p3']['total_dias']);
      $array_datos_total['p3']['promedio_diario']=count($resultado_registros_p3)/count($array_datos_gestiones['p3']['total_dias']);
      $array_datos_total['p3']['promedio_hora']=count($resultado_registros_p3)/count($array_datos_gestiones['p3']['total_dias_hora']);
      $array_datos_total['p3']['promedio_agente']=count($resultado_registros_p3)/count($array_datos_gestion['p3']['gestion_agente']['id']);
      $array_datos_total['p3']['total_agente']=count($array_datos_gestion['p3']['gestion_agente']['id']);

      $array_datos_gestion['p3']['plantilla_lista']=array_values(array_unique($array_datos_gestion['p3']['plantilla_lista']));
      $array_datos_gestion['p3']['motivo_devo_correo_lista']=array_values(array_unique($array_datos_gestion['p3']['motivo_devo_correo_lista']));
    }


  //4. Envíos
    $consulta_string_p4="SELECT `cete_id`, `cete_correo_electronico`, `cete_fecha_ingreso`, `cete_fecha_clasificacion`, `cete_cedula_consulta`, `cete_programa_solicitud`, `cete_respuesta_enviada`, `cete_con_datos`, `cete_datos_incompletos`, `cete_parrafo_plantilla_16`, `cete_parrafo_plantilla_17`, `cete_parrafo_plantilla_18`, `cete_devolucion_correo`, `cete_responsable_clasificacion`, `cete_responsable_envio`, `cete_observaciones`, `cete_notificar`, `cete_registro_usuario`, `cete_registro_fecha`, PROGRAMASOLICITUD.`ceco_valor`, RESPUESTAENVIADA.`ceco_valor`, CONDATOS.`ceco_valor`, DATOSINCOMPLETOS.`ceco_valor`, PLANTILLA16.`ceco_valor`, PLANTILLA17.`ceco_valor`, PLANTILLA18.`ceco_valor`, DEVOLUCIONCORREO.`ceco_valor`, RESPONSABLECLASIFICACION.`ceco_valor`, TU.`usu_nombres_apellidos`, `cete_id_clasificacion` FROM `gestion_cetmnc_envios`
     LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_envios`.`cete_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS RESPUESTAENVIADA ON `gestion_cetmnc_envios`.`cete_respuesta_enviada`=RESPUESTAENVIADA.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS CONDATOS ON `gestion_cetmnc_envios`.`cete_con_datos`=CONDATOS.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS DATOSINCOMPLETOS ON `gestion_cetmnc_envios`.`cete_datos_incompletos`=DATOSINCOMPLETOS.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA16 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_16`=PLANTILLA16.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_17`=PLANTILLA17.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_18`=PLANTILLA18.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS DEVOLUCIONCORREO ON `gestion_cetmnc_envios`.`cete_devolucion_correo`=DEVOLUCIONCORREO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLECLASIFICACION ON `gestion_cetmnc_envios`.`cete_responsable_clasificacion`=RESPONSABLECLASIFICACION.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_envios`.`cete_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p4."";
    $consulta_registros_p4 = $enlace_db->prepare($consulta_string_p4);
    if (count($data_consulta)>0) {
        $consulta_registros_p4->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p4->execute();
    $resultado_registros_p4 = $consulta_registros_p4->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p4)) {
      for ($i=0; $i < count($resultado_registros_p4); $i++) { 
        $array_datos_gestion['p4']['gestion_agente']['id'][]=$resultado_registros_p4[$i][17];
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][17]]['nombre']=$resultado_registros_p4[$i][28];
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][17]]['total']+=1;
        if (!isset($array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][17]]['hora'])) {
          $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][17]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][17]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][17]]['hora'][intval(date('H', strtotime($resultado_registros_p4[$i][18])))]+=1;
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][17]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p4[$i][18]))]+=1;
        
        $array_datos_gestiones['p4']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p4[$i][18]))]+=1;
        $array_datos_gestiones['p4']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p4[$i][18]))]=1;
        $array_datos_gestiones['p4']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p4[$i][18]))]=1;

        $array_datos_gestion['p4']['tipo_respuesta_lista'][]=$resultado_registros_p4[$i][6];
        $array_datos_gestion['p4']['tipo_respuesta_nombre'][$resultado_registros_p4[$i][6]]=$resultado_registros_p4[$i][20];
        $array_datos_gestion['p4']['tipo_respuesta'][$resultado_registros_p4[$i][6]]+=1;
      }

      $array_datos_gestion['p4']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p4']['gestion_agente']['id']));
      
      $array_datos_total['p4']['total_gestion']=count($resultado_registros_p4);
      $array_datos_total['p4']['total_dias_gestion']=count($array_datos_gestiones['p4']['total_dias']);
      $array_datos_total['p4']['promedio_diario']=count($resultado_registros_p4)/count($array_datos_gestiones['p4']['total_dias']);
      $array_datos_total['p4']['promedio_hora']=count($resultado_registros_p4)/count($array_datos_gestiones['p4']['total_dias_hora']);
      $array_datos_total['p4']['promedio_agente']=count($resultado_registros_p4)/count($array_datos_gestion['p4']['gestion_agente']['id']);
      $array_datos_total['p4']['total_agente']=count($array_datos_gestion['p4']['gestion_agente']['id']);

      $array_datos_gestion['p4']['tipo_respuesta_lista']=array_values(array_unique($array_datos_gestion['p4']['tipo_respuesta_lista']));
    }


  //5. Firma Respuesta
    $consulta_string_p5="SELECT `cetfr_id`, `cetfr_fecha_firma`, `cetfr_modulo`, `cetfr_git`, `cetfr_radicado_entrada`, `cetfr_radicado_salida`, `cetfr_aprobador`, `cetfr_responsable_firma`, `cetfr_observaciones`, `cetfr_notificar`, `cetfr_usuario_registro`, `cetfr_usuario_fecha`, MODULO.`ceco_valor`, GIT.`ceco_valor`, APROBADOR.`ceco_valor`, RESPONSABLEFIRMA.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_firma_respuesta`
     LEFT JOIN `gestion_ce_configuracion` AS MODULO ON `gestion_cetmnc_firma_respuesta`.`cetfr_modulo`=MODULO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS GIT ON `gestion_cetmnc_firma_respuesta`.`cetfr_git`=GIT.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS APROBADOR ON `gestion_cetmnc_firma_respuesta`.`cetfr_aprobador`=APROBADOR.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEFIRMA ON `gestion_cetmnc_firma_respuesta`.`cetfr_responsable_firma`=RESPONSABLEFIRMA.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_firma_respuesta`.`cetfr_usuario_registro`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p5."";
    $consulta_registros_p5 = $enlace_db->prepare($consulta_string_p5);
    if (count($data_consulta)>0) {
        $consulta_registros_p5->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p5->execute();
    $resultado_registros_p5 = $consulta_registros_p5->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p5)) {
      for ($i=0; $i < count($resultado_registros_p5); $i++) { 
        $array_datos_gestion['p5']['gestion_agente']['id'][]=$resultado_registros_p5[$i][10];
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][10]]['nombre']=$resultado_registros_p5[$i][16];
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][10]]['total']+=1;
        if (!isset($array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][10]]['hora'])) {
          $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][10]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][10]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][10]]['hora'][intval(date('H', strtotime($resultado_registros_p5[$i][11])))]+=1;
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][10]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p5[$i][11]))]+=1;
        
        $array_datos_gestiones['p5']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p5[$i][11]))]+=1;
        $array_datos_gestiones['p5']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p5[$i][11]))]=1;
        $array_datos_gestiones['p5']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p5[$i][11]))]=1;

        $array_datos_gestion['p5']['modulo_lista'][]=$resultado_registros_p5[$i][2];
        $array_datos_gestion['p5']['modulo_nombre'][$resultado_registros_p5[$i][2]]=$resultado_registros_p5[$i][12];
        $array_datos_gestion['p5']['modulo'][$resultado_registros_p5[$i][2]]+=1;

        $array_datos_gestion['p5']['git_lista'][]=$resultado_registros_p5[$i][3];
        $array_datos_gestion['p5']['git_nombre'][$resultado_registros_p5[$i][3]]=$resultado_registros_p5[$i][13];
        $array_datos_gestion['p5']['git'][$resultado_registros_p5[$i][3]]+=1;
      }

      $array_datos_gestion['p5']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p5']['gestion_agente']['id']));
      
      $array_datos_total['p5']['total_gestion']=count($resultado_registros_p5);
      $array_datos_total['p5']['total_dias_gestion']=count($array_datos_gestiones['p5']['total_dias']);
      $array_datos_total['p5']['promedio_diario']=count($resultado_registros_p5)/count($array_datos_gestiones['p5']['total_dias']);
      $array_datos_total['p5']['promedio_hora']=count($resultado_registros_p5)/count($array_datos_gestiones['p5']['total_dias_hora']);
      $array_datos_total['p5']['promedio_agente']=count($resultado_registros_p5)/count($array_datos_gestion['p5']['gestion_agente']['id']);
      $array_datos_total['p5']['total_agente']=count($array_datos_gestion['p5']['gestion_agente']['id']);

      $array_datos_gestion['p5']['modulo_lista']=array_values(array_unique($array_datos_gestion['p5']['modulo_lista']));
      $array_datos_gestion['p5']['git_lista']=array_values(array_unique($array_datos_gestion['p5']['git_lista']));
    }


  //6. Pendientes Clasificación
    $consulta_string_p6="SELECT `cetpc_id`, `cetpc_pendiente_clasificacion`, `cetpc_pendiente_clasificar`, `cetpc_observaciones`, `cetpc_notificar`, `cetpc_registro_usuario`, `cetpc_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_pendiente_clasificacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_pendiente_clasificacion`.`cetpc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p6."";
    $consulta_registros_p6 = $enlace_db->prepare($consulta_string_p6);
    if (count($data_consulta)>0) {
        $consulta_registros_p6->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p6->execute();
    $resultado_registros_p6 = $consulta_registros_p6->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p6)) {
      for ($i=0; $i < count($resultado_registros_p6); $i++) { 
        $array_datos_gestion['p6']['gestion_agente']['id'][]=$resultado_registros_p6[$i][5];
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][5]]['nombre']=$resultado_registros_p6[$i][7];
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][5]]['total']+=1;
        if (!isset($array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][5]]['hora'])) {
          $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][5]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][5]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][5]]['hora'][intval(date('H', strtotime($resultado_registros_p6[$i][6])))]+=1;
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][5]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p6[$i][6]))]+=1;
        
        $array_datos_gestiones['p6']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p6[$i][6]))]+=1;
        $array_datos_gestiones['p6']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p6[$i][6]))]=1;
        $array_datos_gestiones['p6']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p6[$i][6]))]=1;
      }

      $array_datos_gestion['p6']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p6']['gestion_agente']['id']));
      
      $array_datos_total['p6']['total_gestion']=count($resultado_registros_p6);
      $array_datos_total['p6']['total_dias_gestion']=count($array_datos_gestiones['p6']['total_dias']);
      $array_datos_total['p6']['promedio_diario']=count($resultado_registros_p6)/count($array_datos_gestiones['p6']['total_dias']);
      $array_datos_total['p6']['promedio_hora']=count($resultado_registros_p6)/count($array_datos_gestiones['p6']['total_dias_hora']);
      $array_datos_total['p6']['promedio_agente']=count($resultado_registros_p6)/count($array_datos_gestion['p6']['gestion_agente']['id']);
      $array_datos_total['p6']['total_agente']=count($array_datos_gestion['p6']['gestion_agente']['id']);
    }


  //7. Casos Sin Gestionar
    $consulta_string_p7="SELECT `cetcsg_id`, `cetcsg_proceso_ingreso_solidario`, `cetcsg_responsable_envio`, `cetcsg_responsable_proyeccion`, `cetcsg_causal_no_envio`, `cetcsg_causal_no_proyeccion`, `cetcsg_cantidad_casos`, `cetcsg_observaciones`, `cetcsg_notificar`, `cetcsg_registro_usuario`, `cetcsg_registro_fecha`, INGRESOSOLIDARIO.`ceco_valor`, RESPONSABLEENVIO.`ceco_valor`, RESPONSABLEPROYECCION.`ceco_valor`, CNOENVIO.`ceco_valor`, CNPROYECCION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_casos_sin_gestionar`
        LEFT JOIN `gestion_ce_configuracion` AS INGRESOSOLIDARIO ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_proceso_ingreso_solidario`=INGRESOSOLIDARIO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEENVIO ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_responsable_envio`=RESPONSABLEENVIO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEPROYECCION ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_responsable_proyeccion`=RESPONSABLEPROYECCION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS CNOENVIO ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_causal_no_envio`=CNOENVIO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS CNPROYECCION ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_causal_no_proyeccion`=CNPROYECCION.`ceco_id`
       LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p7."";
    $consulta_registros_p7 = $enlace_db->prepare($consulta_string_p7);
    if (count($data_consulta)>0) {
        $consulta_registros_p7->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p7->execute();
    $resultado_registros_p7 = $consulta_registros_p7->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p7)) {
      for ($i=0; $i < count($resultado_registros_p7); $i++) { 
        $array_datos_gestion['p7']['gestion_agente']['id'][]=$resultado_registros_p7[$i][9];
        $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][9]]['nombre']=$resultado_registros_p7[$i][16];
        $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][9]]['total']+=1;
        if (!isset($array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][9]]['hora'])) {
          $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][9]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][9]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][9]]['hora'][intval(date('H', strtotime($resultado_registros_p7[$i][10])))]+=1;
        $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][9]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p7[$i][10]))]+=1;
        
        $array_datos_gestiones['p7']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p7[$i][10]))]+=1;
        $array_datos_gestiones['p7']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p7[$i][10]))]=1;
        $array_datos_gestiones['p7']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p7[$i][10]))]=1;

        if ($resultado_registros_p7[$i][1]!="") {
          $array_datos_gestion['p7']['proceso_lista'][]=$resultado_registros_p7[$i][1];
          $array_datos_gestion['p7']['proceso_nombre'][$resultado_registros_p7[$i][1]]=$resultado_registros_p7[$i][11];
          $array_datos_gestion['p7']['proceso'][$resultado_registros_p7[$i][1]]+=1;
        }

        if ($resultado_registros_p7[$i][5]!="") {
          $array_datos_gestion['p7']['causal_no_proyeccion_lista'][]=$resultado_registros_p7[$i][5];
          $array_datos_gestion['p7']['causal_no_proyeccion_nombre'][$resultado_registros_p7[$i][5]]=$resultado_registros_p7[$i][15];
          $array_datos_gestion['p7']['causal_no_proyeccion'][$resultado_registros_p7[$i][5]]+=1;
        }

        if ($resultado_registros_p7[$i][4]!="") {
          $array_datos_gestion['p7']['causal_no_envio_lista'][]=$resultado_registros_p7[$i][4];
          $array_datos_gestion['p7']['causal_no_envio_nombre'][$resultado_registros_p7[$i][4]]=$resultado_registros_p7[$i][14];
          $array_datos_gestion['p7']['causal_no_envio'][$resultado_registros_p7[$i][4]]+=1;
        }

        if ($resultado_registros_p7[$i][3]!="") {
          $array_datos_gestion['p7']['proyector_lista'][]=$resultado_registros_p7[$i][3];
          $array_datos_gestion['p7']['proyector_nombre'][$resultado_registros_p7[$i][3]]=$resultado_registros_p7[$i][13];
          $array_datos_gestion['p7']['proyector'][$resultado_registros_p7[$i][3]]+=1;
        }

        if ($resultado_registros_p7[$i][2]!="") {
          $array_datos_gestion['p7']['envio_lista'][]=$resultado_registros_p7[$i][2];
          $array_datos_gestion['p7']['envio_nombre'][$resultado_registros_p7[$i][2]]=$resultado_registros_p7[$i][12];
          $array_datos_gestion['p7']['envio'][$resultado_registros_p7[$i][2]]+=1;
        }
      }

      $array_datos_gestion['p7']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p7']['gestion_agente']['id']));
      
      $array_datos_total['p7']['total_gestion']=count($resultado_registros_p7);
      $array_datos_total['p7']['total_dias_gestion']=count($array_datos_gestiones['p7']['total_dias']);
      $array_datos_total['p7']['promedio_diario']=count($resultado_registros_p7)/count($array_datos_gestiones['p7']['total_dias']);
      $array_datos_total['p7']['promedio_hora']=count($resultado_registros_p7)/count($array_datos_gestiones['p7']['total_dias_hora']);
      $array_datos_total['p7']['promedio_agente']=count($resultado_registros_p7)/count($array_datos_gestion['p7']['gestion_agente']['id']);
      $array_datos_total['p7']['total_agente']=count($array_datos_gestion['p7']['gestion_agente']['id']);

      $array_datos_gestion['p7']['proceso_lista']=array_values(array_unique($array_datos_gestion['p7']['proceso_lista']));
      $array_datos_gestion['p7']['causal_no_proyeccion_lista']=array_values(array_unique($array_datos_gestion['p7']['causal_no_proyeccion_lista']));
      $array_datos_gestion['p7']['causal_no_envio_lista']=array_values(array_unique($array_datos_gestion['p7']['causal_no_envio_lista']));
      $array_datos_gestion['p7']['proyector_lista']=array_values(array_unique($array_datos_gestion['p7']['proyector_lista']));
      $array_datos_gestion['p7']['envio_lista']=array_values(array_unique($array_datos_gestion['p7']['envio_lista']));
    }


  //8. Aprobación Novedades CM
    $consulta_string_p8="SELECT `cetan_id`, `cetan_cod_beneficiario`, `cetan_tipo_documento`, `cetan_documento`, `cetan_nombres_apellidos`, `cetan_tipo_novedad`, `cetan_datos_basicos`, `cetan_suspension`, `cetan_reactivacion`, `cetan_retiro`, `cetan_gestion`, `cetan_tipo_rechazo`, `cetan_realizo_cambio_datos`, `cetan_correccion_datos`, `cetan_observaciones`, `cetan_notificar`, `cetan_registro_usuario`, `cetan_registro_fecha`, TIPODOCUMENTO.`ceco_valor`, TIPONOVEDAD.`ceco_valor`, DATOSBASICOS.`ceco_valor`, SUSPENSION.`ceco_valor`, REACTIVACION.`ceco_valor`, RETIRO.`ceco_valor`, GESTION.`ceco_valor`, TIPORECHAZO.`ceco_valor`, CAMBIODATOS.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_aprobacion_novedades`
       LEFT JOIN `gestion_ce_configuracion` AS TIPODOCUMENTO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_documento`=TIPODOCUMENTO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS TIPONOVEDAD ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_novedad`=TIPONOVEDAD.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS DATOSBASICOS ON `gestion_cetmnc_aprobacion_novedades`.`cetan_datos_basicos`=DATOSBASICOS.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS SUSPENSION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_suspension`=SUSPENSION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS REACTIVACION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_reactivacion`=REACTIVACION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS RETIRO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_retiro`=RETIRO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_gestion`=GESTION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS TIPORECHAZO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_rechazo`=TIPORECHAZO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS CAMBIODATOS ON `gestion_cetmnc_aprobacion_novedades`.`cetan_realizo_cambio_datos`=CAMBIODATOS.`ceco_id`
       LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_aprobacion_novedades`.`cetan_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p8."";
    $consulta_registros_p8 = $enlace_db->prepare($consulta_string_p8);
    if (count($data_consulta)>0) {
        $consulta_registros_p8->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p8->execute();
    $resultado_registros_p8 = $consulta_registros_p8->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p8)) {
      for ($i=0; $i < count($resultado_registros_p8); $i++) { 
        $array_datos_gestion['p8']['gestion_agente']['id'][]=$resultado_registros_p8[$i][16];
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][16]]['nombre']=$resultado_registros_p8[$i][27];
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][16]]['total']+=1;
        if (!isset($array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][16]]['hora'])) {
          $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][16]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][16]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][16]]['hora'][intval(date('H', strtotime($resultado_registros_p8[$i][17])))]+=1;
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][16]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p8[$i][17]))]+=1;
        
        $array_datos_gestiones['p8']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p8[$i][17]))]+=1;
        $array_datos_gestiones['p8']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p8[$i][17]))]=1;
        $array_datos_gestiones['p8']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p8[$i][17]))]=1;

        if ($resultado_registros_p8[$i][5]!="") {
          $array_datos_gestion['p8']['tipo_novedad_lista'][]=$resultado_registros_p8[$i][5];
          $array_datos_gestion['p8']['tipo_novedad_nombre'][$resultado_registros_p8[$i][5]]=$resultado_registros_p8[$i][19];
          $array_datos_gestion['p8']['tipo_novedad'][$resultado_registros_p8[$i][5]]+=1;
        }

        if ($resultado_registros_p8[$i][6]!="") {//DATOS BÁSICOS
          $array_datos_gestion['p8']['datos_basicos_lista'][]=$resultado_registros_p8[$i][6];
          $array_datos_gestion['p8']['datos_basicos_nombre'][$resultado_registros_p8[$i][6]]=$resultado_registros_p8[$i][20];
          $array_datos_gestion['p8']['datos_basicos'][$resultado_registros_p8[$i][6]]+=1;
        }

        if ($resultado_registros_p8[$i][8]!="") {//REACTIVACIÓN
          $array_datos_gestion['p8']['reactivacion_lista'][]=$resultado_registros_p8[$i][8];
          $array_datos_gestion['p8']['reactivacion_nombre'][$resultado_registros_p8[$i][8]]=$resultado_registros_p8[$i][22];
          $array_datos_gestion['p8']['reactivacion'][$resultado_registros_p8[$i][8]]+=1;
        }

        if ($resultado_registros_p8[$i][9]!="") {//RETIRO
          $array_datos_gestion['p8']['retiro_lista'][]=$resultado_registros_p8[$i][9];
          $array_datos_gestion['p8']['retiro_nombre'][$resultado_registros_p8[$i][9]]=$resultado_registros_p8[$i][23];
          $array_datos_gestion['p8']['retiro'][$resultado_registros_p8[$i][9]]+=1;
        }

        if ($resultado_registros_p8[$i][7]!="") {//SUSPENSIÓN
          $array_datos_gestion['p8']['suspension_lista'][]=$resultado_registros_p8[$i][7];
          $array_datos_gestion['p8']['suspension_nombre'][$resultado_registros_p8[$i][7]]=$resultado_registros_p8[$i][21];
          $array_datos_gestion['p8']['suspension'][$resultado_registros_p8[$i][7]]+=1;
        }
      }

      $array_datos_gestion['p8']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p8']['gestion_agente']['id']));
      
      $array_datos_total['p8']['total_gestion']=count($resultado_registros_p8);
      $array_datos_total['p8']['total_dias_gestion']=count($array_datos_gestiones['p8']['total_dias']);
      $array_datos_total['p8']['promedio_diario']=count($resultado_registros_p8)/count($array_datos_gestiones['p8']['total_dias']);
      $array_datos_total['p8']['promedio_hora']=count($resultado_registros_p8)/count($array_datos_gestiones['p8']['total_dias_hora']);
      $array_datos_total['p8']['promedio_agente']=count($resultado_registros_p8)/count($array_datos_gestion['p8']['gestion_agente']['id']);
      $array_datos_total['p8']['total_agente']=count($array_datos_gestion['p8']['gestion_agente']['id']);

      $array_datos_gestion['p8']['tipo_novedad_lista']=array_values(array_unique($array_datos_gestion['p8']['tipo_novedad_lista']));
      $array_datos_gestion['p8']['datos_basicos_lista']=array_values(array_unique($array_datos_gestion['p8']['datos_basicos_lista']));
      $array_datos_gestion['p8']['reactivacion_lista']=array_values(array_unique($array_datos_gestion['p8']['reactivacion_lista']));
      $array_datos_gestion['p8']['retiro_lista']=array_values(array_unique($array_datos_gestion['p8']['retiro_lista']));
      $array_datos_gestion['p8']['suspension_lista']=array_values(array_unique($array_datos_gestion['p8']['suspension_lista']));
    }

?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <?php require_once(ROOT.'includes/_head-charts.php'); ?>
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
          <div class="row justify-content-center">
            <div class="col-lg-12 d-flex flex-column">
              <div class="row">
                <div class="col-md-3">
                  <div class="row px-3">
                    <a href="<?php echo URL_MENU; ?>/canal_escrito/reparto_estadisticas" class="btn py-2 px-2 btn-dark mb-1">Reparto</a>
                    <a href="<?php echo URL_MENU; ?>/canal_escrito/jafocalizacion_estadisticas" class="btn py-2 px-2 btn-dark mb-1">Jóvenes en Acción y Focalización</a>
                    <a href="<?php echo URL_MENU; ?>/canal_escrito/tmnc_estadisticas" class="btn py-2 px-2 btn-dark mb-1">Transferencias Monetarias No Condicionadas</a>
                    <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 color-blanco" data-bs-toggle="modal" data-bs-target="#modal-filtro" title="Filtros">
                      <i class="fas fa-filter btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Filtros</span>
                    </button>
                    <div class="col-lg-12 py-1 font-size-12">
                      <?php if($_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']!="" AND $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']!=""): ?>
                        Filtros: <?php echo $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']; ?> A <?php echo $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']; ?>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="btn-group-vertical nav nav-tabs" role="group" aria-label="Button group with nested dropdown">
                    <button type="button" class="btn btn-outline-dark px-1 py-1 active" style="text-align: left !important;" id="p1-tab" data-bs-toggle="tab" href="#p1" role="tab" aria-controls="p1" aria-selected="true">
                      1. Proyección de Respuestas
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p2-tab" data-bs-toggle="tab" href="#p2" role="tab" aria-controls="p2" aria-selected="true">
                      2. Aprobación Respuesta
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p3-tab" data-bs-toggle="tab" href="#p3" role="tab" aria-controls="p2" aria-selected="true">
                      3. Clasificación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p4-tab" data-bs-toggle="tab" href="#p4" role="tab" aria-controls="p2" aria-selected="true">
                      4. Envíos
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p5-tab" data-bs-toggle="tab" href="#p5" role="tab" aria-controls="p2" aria-selected="true">
                      5. Firma Respuesta
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p6-tab" data-bs-toggle="tab" href="#p6" role="tab" aria-controls="p2" aria-selected="true">
                      6. Pendientes Clasificación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p7-tab" data-bs-toggle="tab" href="#p7" role="tab" aria-controls="p2" aria-selected="true">
                      7. Casos Sin Gestionar
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p8-tab" data-bs-toggle="tab" href="#p8" role="tab" aria-controls="p2" aria-selected="true">
                      8. Aprobación Novedades CM
                    </button>
                  </div>
                </div>
                <div class="col-md-9 ps-0">
                  <div class="tab-content tab-content-basic pt-0 px-1">
                    <!-- p1 -->
                    <?php include('tmnc_estadisticas_p1.php'); ?>

                    <!-- p2 -->
                    <?php include('tmnc_estadisticas_p2.php'); ?>

                    <!-- p3 -->
                    <?php include('tmnc_estadisticas_p3.php'); ?>

                    <!-- p4 -->
                    <?php include('tmnc_estadisticas_p4.php'); ?>

                    <!-- p5 -->
                    <?php include('tmnc_estadisticas_p5.php'); ?>

                    <!-- p6 -->
                    <?php include('tmnc_estadisticas_p6.php'); ?>

                    <!-- p7 -->
                    <?php include('tmnc_estadisticas_p7.php'); ?>

                    <!-- p8 -->
                    <?php include('tmnc_estadisticas_p8.php'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- Modal -->
          <div class="modal fade" id="modal-filtro" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="staticBackdropLabel">Filtros</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form name="filtro" action="reparto_estadisticas" method="POST">
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="fecha_inicio">Fecha inicio</label>
                          <input type="date" class="form-control form-control-sm" name="fecha_inicio" id="fecha_inicio" value="<?php echo $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="fecha_fin">Fecha fin</label>
                          <input type="date" class="form-control form-control-sm" name="fecha_fin" id="fecha_fin" value="<?php echo $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']; ?>" required>
                        </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger py-2 px-2" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" name="filtro" class="btn btn-primary btn-corp py-2 px-2">Aplicar</button>
                </div>
                </form>
              </div>
            </div>
          </div>
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>