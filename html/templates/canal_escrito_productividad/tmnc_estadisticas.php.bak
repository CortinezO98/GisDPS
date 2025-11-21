<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Transferencias Monetarias No Condicionadas | Productividad";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  // error_reporting(E_ALL);

  if(isset($_POST["filtro"])){
    $fecha_inicio=validar_input($_POST['fecha_inicio']);
    $fecha_fin=validar_input($_POST['fecha_fin']);
    
    $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']=$fecha_inicio;
    $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']=$fecha_fin;

    header("Location: reparto_estadisticas");
  } elseif($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']=="" OR $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']=="") {
    // $anio_mes_separado=explode("-", date('Y-m-d'));
    // $numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, $anio_mes_separado[1], $anio_mes_separado[0]); //cantidad de días del mes
    $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']=date('Y-m-d');
    $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']=date('Y-m-d');
  }
  
  // Inicializa variable tipo array
  $data_consulta=array();

  if ($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']!="" AND $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']!="") {
    array_push($data_consulta, $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']);
    array_push($data_consulta, $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin'].' 23:59:59');
    $filtro_fechas_tm_p1=" AND `cet_registro_fecha`>=? AND `cet_registro_fecha`<=?";
    $filtro_fechas_tm_p2=" AND `cetar_registro_fecha`>=? AND `cetar_registro_fecha`<=?";
    $filtro_fechas_tm_p3=" AND `cetc_registro_fecha`>=? AND `cetc_registro_fecha`<=?";
    $filtro_fechas_tm_p4=" AND `cete_registro_fecha`>=? AND `cete_registro_fecha`<=?";
    $filtro_fechas_tm_p5=" AND `cetfr_usuario_fecha`>=? AND `cetfr_usuario_fecha`<=?";
    $filtro_fechas_tm_p6=" AND `cetpc_registro_fecha`>=? AND `cetpc_registro_fecha`<=?";
    $filtro_fechas_tm_p7=" AND `cetcsg_registro_fecha`>=? AND `cetcsg_registro_fecha`<=?";
    $filtro_fechas_tm_p8=" AND `cetan_registro_fecha`>=? AND `cetan_registro_fecha`<=?";
  }
  
  
  //CONSTRUIR ARRAY AÑO-MES-DIA
    $dia_control=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
    while (date('Y-m-d', strtotime($dia_control))<=date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
        $array_dias_mes[]=$dia_control;
        $array_dias_mes_data[$dia_control]=0;

        $dia_control = date("Y-m-d", strtotime("+ 1 day", strtotime($dia_control)));
    }

  //CONSTRUIR HORARIO
    for ($i=0; $i < 24; $i++) { 
      $array_anio_mes_hora_num[]=validar_cero($i);
      $array_anio_mes_hora_val[]=0;
    }

  $consulta_string_meta="SELECT `cef_id`, `cef_grupo`, `cef_nombre`, `cef_meta`, `cef_auxiliar_1`, `cef_auxiliar_2`, `cef_auxiliar_3` FROM `gestion_ce_formularios` WHERE `cef_grupo`='tmnc'";
  $consulta_registros_meta = $enlace_db->prepare($consulta_string_meta);
  $consulta_registros_meta->execute();
  $resultado_registros_meta = $consulta_registros_meta->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_meta); $i++) { 
    $array_metas[$resultado_registros_meta[$i][0]]['meta']=$resultado_registros_meta[$i][3];
    $array_metas[$resultado_registros_meta[$i][0]]['nombre']=$resultado_registros_meta[$i][2];

    $array_datos_gestion[$resultado_registros_meta[$i][0]]['promedio_suma']=0;
    $array_datos_gestion[$resultado_registros_meta[$i][0]]['promedio_general']=0;
    $array_datos_gestion[$resultado_registros_meta[$i][0]]['gestion_agente']=array();
    $array_datos_gestion[$resultado_registros_meta[$i][0]]['gestion_agente']['id']=array();
  }

  //1. Proyección de Respuestas
    $id_formulario='tmnc_sproyeccion_respuestas';
    $meta_formulario=$array_metas[$id_formulario]['meta'];
    $nombre_formulario=$array_metas[$id_formulario]['nombre'];

    $consulta_string_tm_p1="SELECT `cet_id`, `cet_radicado_entrada`, `cet_abogado_aprobacion`, `cet_documento_identidad`, `cet_nombre_ciudadano`, `cet_correo_direccion`, `cet_programa_solicitud`, `cet_plantilla`, `cet_con_datos`, `cet_datos_incompletos`, `cet_plantilla_compensacion_iva`, `cet_plantilla_adulto_mayor`, `cet_novedad_radicado`, `cet_motivo_archivo`, `cet_tipo_entidad`, `cet_id_solicitud`, `cet_observaciones`, `cet_notificar`, `cet_registro_usuario`, `cet_registro_fecha`, ABOGADOAPROBACION.`ceco_valor`, PROGRAMASOLICITUD.`ceco_valor`, PLANTILLA.`ceco_valor`, CONDATOS.`ceco_valor`, DATOSINCOMPLETOS.`ceco_valor`, PCOMPENSACIONIVA.`ceco_valor`, PADULTOMAYOR.`ceco_valor`, NOVEDADRADICADO.`ceco_valor`, TIPOENTIDAD.`ceco_valor`, TU.`usu_nombres_apellidos`, PRENTA.`ceco_valor`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cetmnc_proyeccion_respuestas`
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
       LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_proyeccion_respuestas`.`cet_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p1."";
    $consulta_registros_tm_p1 = $enlace_db->prepare($consulta_string_tm_p1);
    if (count($data_consulta)>0) {
        $consulta_registros_tm_p1->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_tm_p1->execute();
    $resultado_registros_tm_p1 = $consulta_registros_tm_p1->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_tm_p1)) {
      for ($i=0; $i < count($resultado_registros_tm_p1); $i++) { 
        $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_tm_p1[$i][18];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['nombre']=$resultado_registros_tm_p1[$i][29];
        $array_datos_agente[$resultado_registros_tm_p1[$i][18]]['nombre']=$resultado_registros_tm_p1[$i][29];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['coordinador']=$resultado_registros_tm_p1[$i][31];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['total']+=1;
        if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['hora'])) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p1[$i][19])))]+=1;
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p1[$i][19]))]+=1;

        $array_coordinador[]=$resultado_registros_tm_p1[$i][32];
        $array_coordinador_datos[$resultado_registros_tm_p1[$i][32]]['nombre']=$resultado_registros_tm_p1[$i][31];
        $array_coordinador_datos[$resultado_registros_tm_p1[$i][32]]['agentes'][]=$resultado_registros_tm_p1[$i][18];
        $array_coordinador_datos[$resultado_registros_tm_p1[$i][32]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p1[$i][32]]['agentes']));
        
      }

      $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

      for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
        $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$array_metas[$id_formulario]['meta'];
        
        if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
        }

        $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

        $array_resumen[$id_agente]['formularios'][]=$id_formulario;
        $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
        $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
      }

      $array_datos_gestion[$id_formulario]['promedio_general']=$array_datos_gestion[$id_formulario]['promedio_suma']/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
    }


  //2. Aprobación Respuesta
    $id_formulario='tmnc_saprobacion_respuestas';
    $meta_formulario=$array_metas[$id_formulario]['meta'];
    $nombre_formulario=$array_metas[$id_formulario]['nombre'];

    $consulta_string_tm_p2="SELECT `cetar_id`, `cetar_radicado`, `cetar_numero_documento`, `cetar_nombre_ciudadano`, `cetar_proyector`, `cetar_apoyo_prosperidad`, `cetar_ingreso_solidario`, `cetar_carta_respuesta`, `cetar_estado`, `cetar_comentario_aprobacion`, `cetar_motivo_rechazo`, `cetar_observaciones`, `cetar_notificar`, `cetar_registro_usuario`, `cetar_registro_fecha`, PROYECTOR.`ceco_valor`, APOYOPROSPERIDAD.`ceco_valor`, INGRESOSOLIDARIO.`ceco_valor`, CARTARESPUESTA.`ceco_valor`, ESTADO.`ceco_valor`, TU.`usu_nombres_apellidos`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cetmnc_aprobacion_respuesta`
     LEFT JOIN `gestion_ce_configuracion` AS PROYECTOR ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_proyector`=PROYECTOR.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS APOYOPROSPERIDAD ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_apoyo_prosperidad`=APOYOPROSPERIDAD.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS INGRESOSOLIDARIO ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_ingreso_solidario`=INGRESOSOLIDARIO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS CARTARESPUESTA ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_carta_respuesta`=CARTARESPUESTA.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_estado`=ESTADO.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p2."";
    $consulta_registros_tm_p2 = $enlace_db->prepare($consulta_string_tm_p2);
    if (count($data_consulta)>0) {
        $consulta_registros_tm_p2->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_tm_p2->execute();
    $resultado_registros_tm_p2 = $consulta_registros_tm_p2->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_tm_p2)) {
      for ($i=0; $i < count($resultado_registros_tm_p2); $i++) { 
        $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_tm_p2[$i][13];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['nombre']=$resultado_registros_tm_p2[$i][20];
        $array_datos_agente[$resultado_registros_tm_p2[$i][13]]['nombre']=$resultado_registros_tm_p2[$i][20];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['coordinador']=$resultado_registros_tm_p2[$i][21];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['total']+=1;
        if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['hora'])) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p2[$i][14])))]+=1;
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p2[$i][14]))]+=1;

        $array_coordinador[]=$resultado_registros_tm_p2[$i][22];
        $array_coordinador_datos[$resultado_registros_tm_p2[$i][22]]['nombre']=$resultado_registros_tm_p2[$i][21];
        $array_coordinador_datos[$resultado_registros_tm_p2[$i][22]]['agentes'][]=$resultado_registros_tm_p2[$i][13];
        $array_coordinador_datos[$resultado_registros_tm_p2[$i][22]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p2[$i][22]]['agentes']));
        
      }

      $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

      for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
        $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$array_metas[$id_formulario]['meta'];
        
        if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
        }

        $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

        $array_resumen[$id_agente]['formularios'][]=$id_formulario;
        $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
        $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
      }

      $array_datos_gestion[$id_formulario]['promedio_general']=$array_datos_gestion[$id_formulario]['promedio_suma']/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
    }


  //3. Clasificación
    $id_formulario='tmnc_sclasificacion';
    $meta_formulario=$array_metas[$id_formulario]['meta'];
    $nombre_formulario=$array_metas[$id_formulario]['nombre'];

    $consulta_string_tm_p3="SELECT `cetc_id`, `cetc_correo_electronico`, `cetc_fecha_ingreso_correo`, `cetc_nombre_ciudadano`, `cetc_cedula_consulta`, `cetc_asunto_correo`, `cetc_programa_solicitud`, `cetc_plantilla_utilizada`, `cetc_solicitud_ciudadano`, `cetc_plantilla_datos_incompletos`, `cetc_plantilla_datos_completos`, `cetc_parrafo_radicacion`, `cetc_parrafo_plantilla_1`, `cetc_parrafo_plantilla_4`, `cetc_parrafo_plantilla_5`, `cetc_parrafo_plantilla_6`, `cetc_situacion_plantilla_8`, `cetc_parrafo_plantilla_8`, `cetc_parrafo_plantilla_10`, `cetc_titular_hogar`, `cetc_parrafo_plantilla_14`, `cetc_parrafo_plantilla_16`, `cetc_situacion_plantilla_17`, `cetc_parrafo_plantilla_17`, `cetc_situacion_plantilla_18`, `cetc_parrafo_plantilla_18`, `cetc_parrafo_plantilla_20`, `cetc_nombre_solicitante`, `cetc_nombre_titular`, `cetc_parrafo_plantilla_21`, `cetc_situacion_plantilla_22`, `cetc_parrafo_plantilla_22`, `cetc_parrafo_plantilla_23`, `cetc_parrafo_plantilla_25`, `cetc_parrafo_plantilla_26`, `cetc_parrafo_plantilla_reemplazo`, `cetc_motivo_devolucion`, `cetc_observaciones`, `cetc_notificar`, `cetc_registro_usuario`, `cetc_registro_fecha`, PROGRAMASOLICITUD.`ceco_valor`, PUTILIZADA.`ceco_valor`, PDATOSINCOMPLETOS.`ceco_valor`, PDATOSCOMPLETOS.`ceco_valor`, PLANTILLA8.`ceco_valor`, PLANTILLA17.`ceco_valor`, PLANTILLA18.`ceco_valor`, PLANTILLA22.`ceco_valor`, MOTIVODEVOLUCION.`ceco_valor`, TU.`usu_nombres_apellidos`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cetmnc_clasificacion`
     LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_clasificacion`.`cetc_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PUTILIZADA ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_utilizada`=PUTILIZADA.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PDATOSINCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_incompletos`=PDATOSINCOMPLETOS.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PDATOSCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_completos`=PDATOSCOMPLETOS.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA8 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_8`=PLANTILLA8.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_17`=PLANTILLA17.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_18`=PLANTILLA18.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA22 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_22`=PLANTILLA22.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS MOTIVODEVOLUCION ON `gestion_cetmnc_clasificacion`.`cetc_motivo_devolucion`=MOTIVODEVOLUCION.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_clasificacion`.`cetc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p3."";
    $consulta_registros_tm_p3 = $enlace_db->prepare($consulta_string_tm_p3);
    if (count($data_consulta)>0) {
        $consulta_registros_tm_p3->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_tm_p3->execute();
    $resultado_registros_tm_p3 = $consulta_registros_tm_p3->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_tm_p3)) {
      for ($i=0; $i < count($resultado_registros_tm_p3); $i++) { 
        $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_tm_p3[$i][39];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['nombre']=$resultado_registros_tm_p3[$i][50];
        $array_datos_agente[$resultado_registros_tm_p3[$i][39]]['nombre']=$resultado_registros_tm_p3[$i][50];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['coordinador']=$resultado_registros_tm_p3[$i][51];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['total']+=1;
        if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['hora'])) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p3[$i][40])))]+=1;
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p3[$i][40]))]+=1;

        $array_coordinador[]=$resultado_registros_tm_p3[$i][52];
        $array_coordinador_datos[$resultado_registros_tm_p3[$i][52]]['nombre']=$resultado_registros_tm_p3[$i][51];
        $array_coordinador_datos[$resultado_registros_tm_p3[$i][52]]['agentes'][]=$resultado_registros_tm_p3[$i][39];
        $array_coordinador_datos[$resultado_registros_tm_p3[$i][52]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p3[$i][52]]['agentes']));
        
      }

      $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

      for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
        $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$array_metas[$id_formulario]['meta'];
        
        if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
        }

        $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

        $array_resumen[$id_agente]['formularios'][]=$id_formulario;
        $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
        $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
      }

      $array_datos_gestion[$id_formulario]['promedio_general']=$array_datos_gestion[$id_formulario]['promedio_suma']/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
    }


  //4. Envíos
    $id_formulario='tmnc_senvios';
    $meta_formulario=$array_metas[$id_formulario]['meta'];
    $nombre_formulario=$array_metas[$id_formulario]['nombre'];

    $consulta_string_tm_p4="SELECT `cete_id`, `cete_correo_electronico`, `cete_fecha_ingreso`, `cete_fecha_clasificacion`, `cete_cedula_consulta`, `cete_programa_solicitud`, `cete_respuesta_enviada`, `cete_con_datos`, `cete_datos_incompletos`, `cete_parrafo_plantilla_16`, `cete_parrafo_plantilla_17`, `cete_parrafo_plantilla_18`, `cete_devolucion_correo`, `cete_responsable_clasificacion`, `cete_responsable_envio`, `cete_observaciones`, `cete_notificar`, `cete_registro_usuario`, `cete_registro_fecha`, PROGRAMASOLICITUD.`ceco_valor`, RESPUESTAENVIADA.`ceco_valor`, CONDATOS.`ceco_valor`, DATOSINCOMPLETOS.`ceco_valor`, PLANTILLA16.`ceco_valor`, PLANTILLA17.`ceco_valor`, PLANTILLA18.`ceco_valor`, DEVOLUCIONCORREO.`ceco_valor`, RESPONSABLECLASIFICACION.`ceco_valor`, TU.`usu_nombres_apellidos`, `cete_id_clasificacion`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cetmnc_envios`
     LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_envios`.`cete_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS RESPUESTAENVIADA ON `gestion_cetmnc_envios`.`cete_respuesta_enviada`=RESPUESTAENVIADA.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS CONDATOS ON `gestion_cetmnc_envios`.`cete_con_datos`=CONDATOS.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS DATOSINCOMPLETOS ON `gestion_cetmnc_envios`.`cete_datos_incompletos`=DATOSINCOMPLETOS.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA16 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_16`=PLANTILLA16.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_17`=PLANTILLA17.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_18`=PLANTILLA18.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS DEVOLUCIONCORREO ON `gestion_cetmnc_envios`.`cete_devolucion_correo`=DEVOLUCIONCORREO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLECLASIFICACION ON `gestion_cetmnc_envios`.`cete_responsable_clasificacion`=RESPONSABLECLASIFICACION.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_envios`.`cete_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p4."";
    $consulta_registros_tm_p4 = $enlace_db->prepare($consulta_string_tm_p4);
    if (count($data_consulta)>0) {
        $consulta_registros_tm_p4->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_tm_p4->execute();
    $resultado_registros_tm_p4 = $consulta_registros_tm_p4->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_tm_p4)) {
      for ($i=0; $i < count($resultado_registros_tm_p4); $i++) { 
        $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_tm_p4[$i][17];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['nombre']=$resultado_registros_tm_p4[$i][28];
        $array_datos_agente[$resultado_registros_tm_p4[$i][17]]['nombre']=$resultado_registros_tm_p4[$i][28];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['coordinador']=$resultado_registros_tm_p4[$i][30];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['total']+=1;
        if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['hora'])) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p4[$i][18])))]+=1;
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p4[$i][18]))]+=1;

        $array_coordinador[]=$resultado_registros_tm_p4[$i][31];
        $array_coordinador_datos[$resultado_registros_tm_p4[$i][31]]['nombre']=$resultado_registros_tm_p4[$i][30];
        $array_coordinador_datos[$resultado_registros_tm_p4[$i][31]]['agentes'][]=$resultado_registros_tm_p4[$i][17];
        $array_coordinador_datos[$resultado_registros_tm_p4[$i][31]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p4[$i][31]]['agentes']));
        
      }

      $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

      for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
        $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$array_metas[$id_formulario]['meta'];
        
        if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
        }

        $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

        $array_resumen[$id_agente]['formularios'][]=$id_formulario;
        $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
        $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
      }

      $array_datos_gestion[$id_formulario]['promedio_general']=$array_datos_gestion[$id_formulario]['promedio_suma']/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
    }


  //5. Firma Respuesta
    $id_formulario='tmnc_sfirma_respuesta';
    $meta_formulario=$array_metas[$id_formulario]['meta'];
    $nombre_formulario=$array_metas[$id_formulario]['nombre'];

    $consulta_string_tm_p5="SELECT `cetfr_id`, `cetfr_fecha_firma`, `cetfr_modulo`, `cetfr_git`, `cetfr_radicado_entrada`, `cetfr_radicado_salida`, `cetfr_aprobador`, `cetfr_responsable_firma`, `cetfr_observaciones`, `cetfr_notificar`, `cetfr_usuario_registro`, `cetfr_usuario_fecha`, MODULO.`ceco_valor`, GIT.`ceco_valor`, APROBADOR.`ceco_valor`, RESPONSABLEFIRMA.`ceco_valor`, TU.`usu_nombres_apellidos`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cetmnc_firma_respuesta`
     LEFT JOIN `gestion_ce_configuracion` AS MODULO ON `gestion_cetmnc_firma_respuesta`.`cetfr_modulo`=MODULO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS GIT ON `gestion_cetmnc_firma_respuesta`.`cetfr_git`=GIT.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS APROBADOR ON `gestion_cetmnc_firma_respuesta`.`cetfr_aprobador`=APROBADOR.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEFIRMA ON `gestion_cetmnc_firma_respuesta`.`cetfr_responsable_firma`=RESPONSABLEFIRMA.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_firma_respuesta`.`cetfr_usuario_registro`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p5."";
    $consulta_registros_tm_p5 = $enlace_db->prepare($consulta_string_tm_p5);
    if (count($data_consulta)>0) {
        $consulta_registros_tm_p5->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_tm_p5->execute();
    $resultado_registros_tm_p5 = $consulta_registros_tm_p5->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_tm_p5)) {
      for ($i=0; $i < count($resultado_registros_tm_p5); $i++) { 
        $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_tm_p5[$i][10];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['nombre']=$resultado_registros_tm_p5[$i][16];
        $array_datos_agente[$resultado_registros_tm_p5[$i][10]]['nombre']=$resultado_registros_tm_p5[$i][16];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['coordinador']=$resultado_registros_tm_p5[$i][17];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['total']+=1;
        if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['hora'])) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p5[$i][11])))]+=1;
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p5[$i][11]))]+=1;

        $array_coordinador[]=$resultado_registros_tm_p5[$i][18];
        $array_coordinador_datos[$resultado_registros_tm_p5[$i][18]]['nombre']=$resultado_registros_tm_p5[$i][17];
        $array_coordinador_datos[$resultado_registros_tm_p5[$i][18]]['agentes'][]=$resultado_registros_tm_p5[$i][10];
        $array_coordinador_datos[$resultado_registros_tm_p5[$i][18]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p5[$i][18]]['agentes']));
        
      }

      $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

      for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
        $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$array_metas[$id_formulario]['meta'];
        
        if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
        }

        $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

        $array_resumen[$id_agente]['formularios'][]=$id_formulario;
        $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
        $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
      }

      $array_datos_gestion[$id_formulario]['promedio_general']=$array_datos_gestion[$id_formulario]['promedio_suma']/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
    }


  //6. Pendientes Clasificación
    $id_formulario='tmnc_sclasificacion';
    $meta_formulario=$array_metas[$id_formulario]['meta'];
    $nombre_formulario=$array_metas[$id_formulario]['nombre'];

    $consulta_string_tm_p6="SELECT `cetpc_id`, `cetpc_pendiente_clasificacion`, `cetpc_pendiente_clasificar`, `cetpc_observaciones`, `cetpc_notificar`, `cetpc_registro_usuario`, `cetpc_registro_fecha`, TU.`usu_nombres_apellidos`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cetmnc_pendiente_clasificacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_pendiente_clasificacion`.`cetpc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p6."";
    $consulta_registros_tm_p6 = $enlace_db->prepare($consulta_string_tm_p6);
    if (count($data_consulta)>0) {
        $consulta_registros_tm_p6->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_tm_p6->execute();
    $resultado_registros_tm_p6 = $consulta_registros_tm_p6->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_tm_p6)) {
      for ($i=0; $i < count($resultado_registros_tm_p6); $i++) { 
        $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_tm_p6[$i][5];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['nombre']=$resultado_registros_tm_p6[$i][7];
        $array_datos_agente[$resultado_registros_tm_p6[$i][5]]['nombre']=$resultado_registros_tm_p6[$i][7];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['coordinador']=$resultado_registros_tm_p6[$i][8];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['total']+=1;
        if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['hora'])) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p6[$i][6])))]+=1;
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p6[$i][6]))]+=1;

        $array_coordinador[]=$resultado_registros_tm_p6[$i][9];
        $array_coordinador_datos[$resultado_registros_tm_p6[$i][9]]['nombre']=$resultado_registros_tm_p6[$i][8];
        $array_coordinador_datos[$resultado_registros_tm_p6[$i][9]]['agentes'][]=$resultado_registros_tm_p6[$i][5];
        $array_coordinador_datos[$resultado_registros_tm_p6[$i][9]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p6[$i][9]]['agentes']));
        
      }

      $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

      for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
        $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$array_metas[$id_formulario]['meta'];
        
        if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
        }

        $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

        $array_resumen[$id_agente]['formularios'][]=$id_formulario;
        $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
        $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
      }

      $array_datos_gestion[$id_formulario]['promedio_general']=$array_datos_gestion[$id_formulario]['promedio_suma']/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
    }


  //7. Casos Sin Gestionar
    $id_formulario='tmnc_scasos_sgestionar';
    $meta_formulario=$array_metas[$id_formulario]['meta'];
    $nombre_formulario=$array_metas[$id_formulario]['nombre'];

    $consulta_string_tm_p7="SELECT `cetcsg_id`, `cetcsg_proceso_ingreso_solidario`, `cetcsg_responsable_envio`, `cetcsg_responsable_proyeccion`, `cetcsg_causal_no_envio`, `cetcsg_causal_no_proyeccion`, `cetcsg_cantidad_casos`, `cetcsg_observaciones`, `cetcsg_notificar`, `cetcsg_registro_usuario`, `cetcsg_registro_fecha`, INGRESOSOLIDARIO.`ceco_valor`, RESPONSABLEENVIO.`ceco_valor`, RESPONSABLEPROYECCION.`ceco_valor`, CNOENVIO.`ceco_valor`, CNPROYECCION.`ceco_valor`, TU.`usu_nombres_apellidos`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cetmnc_casos_sin_gestionar`
        LEFT JOIN `gestion_ce_configuracion` AS INGRESOSOLIDARIO ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_proceso_ingreso_solidario`=INGRESOSOLIDARIO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEENVIO ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_responsable_envio`=RESPONSABLEENVIO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEPROYECCION ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_responsable_proyeccion`=RESPONSABLEPROYECCION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS CNOENVIO ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_causal_no_envio`=CNOENVIO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS CNPROYECCION ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_causal_no_proyeccion`=CNPROYECCION.`ceco_id`
       LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p7."";
    $consulta_registros_tm_p7 = $enlace_db->prepare($consulta_string_tm_p7);
    if (count($data_consulta)>0) {
        $consulta_registros_tm_p7->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_tm_p7->execute();
    $resultado_registros_tm_p7 = $consulta_registros_tm_p7->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_tm_p7)) {
      for ($i=0; $i < count($resultado_registros_tm_p7); $i++) { 
        $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_tm_p7[$i][9];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['nombre']=$resultado_registros_tm_p7[$i][16];
        $array_datos_agente[$resultado_registros_tm_p7[$i][9]]['nombre']=$resultado_registros_tm_p7[$i][16];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['coordinador']=$resultado_registros_tm_p7[$i][17];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['total']+=1;
        if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['hora'])) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p7[$i][10])))]+=1;
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p7[$i][10]))]+=1;

        $array_coordinador[]=$resultado_registros_tm_p7[$i][18];
        $array_coordinador_datos[$resultado_registros_tm_p7[$i][18]]['nombre']=$resultado_registros_tm_p7[$i][17];
        $array_coordinador_datos[$resultado_registros_tm_p7[$i][18]]['agentes'][]=$resultado_registros_tm_p7[$i][9];
        $array_coordinador_datos[$resultado_registros_tm_p7[$i][18]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p7[$i][18]]['agentes']));
        
      }

      $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

      for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
        $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$array_metas[$id_formulario]['meta'];
        
        if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
        }

        $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

        $array_resumen[$id_agente]['formularios'][]=$id_formulario;
        $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
        $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
      }

      $array_datos_gestion[$id_formulario]['promedio_general']=$array_datos_gestion[$id_formulario]['promedio_suma']/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
    }


  //8. Aprobación Novedades CM
    $id_formulario='tmnc_saprobacion_novedades';
    $meta_formulario=$array_metas[$id_formulario]['meta'];
    $nombre_formulario=$array_metas[$id_formulario]['nombre'];

    $consulta_string_tm_p8="SELECT `cetan_id`, `cetan_cod_beneficiario`, `cetan_tipo_documento`, `cetan_documento`, `cetan_nombres_apellidos`, `cetan_tipo_novedad`, `cetan_datos_basicos`, `cetan_suspension`, `cetan_reactivacion`, `cetan_retiro`, `cetan_gestion`, `cetan_tipo_rechazo`, `cetan_realizo_cambio_datos`, `cetan_correccion_datos`, `cetan_observaciones`, `cetan_notificar`, `cetan_registro_usuario`, `cetan_registro_fecha`, TIPODOCUMENTO.`ceco_valor`, TIPONOVEDAD.`ceco_valor`, DATOSBASICOS.`ceco_valor`, SUSPENSION.`ceco_valor`, REACTIVACION.`ceco_valor`, RETIRO.`ceco_valor`, GESTION.`ceco_valor`, TIPORECHAZO.`ceco_valor`, CAMBIODATOS.`ceco_valor`, TU.`usu_nombres_apellidos`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cetmnc_aprobacion_novedades`
       LEFT JOIN `gestion_ce_configuracion` AS TIPODOCUMENTO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_documento`=TIPODOCUMENTO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS TIPONOVEDAD ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_novedad`=TIPONOVEDAD.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS DATOSBASICOS ON `gestion_cetmnc_aprobacion_novedades`.`cetan_datos_basicos`=DATOSBASICOS.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS SUSPENSION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_suspension`=SUSPENSION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS REACTIVACION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_reactivacion`=REACTIVACION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS RETIRO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_retiro`=RETIRO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_gestion`=GESTION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS TIPORECHAZO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_rechazo`=TIPORECHAZO.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS CAMBIODATOS ON `gestion_cetmnc_aprobacion_novedades`.`cetan_realizo_cambio_datos`=CAMBIODATOS.`ceco_id`
       LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_aprobacion_novedades`.`cetan_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p8."";
    $consulta_registros_tm_p8 = $enlace_db->prepare($consulta_string_tm_p8);
    if (count($data_consulta)>0) {
        $consulta_registros_tm_p8->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_tm_p8->execute();
    $resultado_registros_tm_p8 = $consulta_registros_tm_p8->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_tm_p8)) {
      for ($i=0; $i < count($resultado_registros_tm_p8); $i++) { 
        $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_tm_p8[$i][16];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['nombre']=$resultado_registros_tm_p8[$i][27];
        $array_datos_agente[$resultado_registros_tm_p8[$i][16]]['nombre']=$resultado_registros_tm_p8[$i][27];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['coordinador']=$resultado_registros_tm_p8[$i][28];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['total']+=1;
        if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['hora'])) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p8[$i][17])))]+=1;
        $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p8[$i][17]))]+=1;

        $array_coordinador[]=$resultado_registros_tm_p8[$i][29];
        $array_coordinador_datos[$resultado_registros_tm_p8[$i][29]]['nombre']=$resultado_registros_tm_p8[$i][28];
        $array_coordinador_datos[$resultado_registros_tm_p8[$i][29]]['agentes'][]=$resultado_registros_tm_p8[$i][16];
        $array_coordinador_datos[$resultado_registros_tm_p8[$i][29]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p8[$i][29]]['agentes']));
        
      }

      $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

      for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
        $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
        $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$array_metas[$id_formulario]['meta'];
        
        if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
          $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
        }

        $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

        $array_resumen[$id_agente]['formularios'][]=$id_formulario;
        $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
        $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
      }

      $array_datos_gestion[$id_formulario]['promedio_general']=$array_datos_gestion[$id_formulario]['promedio_suma']/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
    }

?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <script src="https://code.highcharts.com/11.4.3/highcharts.js"></script>
  <script src="https://code.highcharts.com/11.4.3/highcharts-more.js"></script>
  <script src="https://code.highcharts.com/11.4.3/modules/solid-gauge.js"></script>
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
                    <a href="<?php echo URL_MENU; ?>/canal_escrito_productividad/reparto_estadisticas" class="btn py-2 px-2 btn-dark mb-1">Reparto</a>
                    <a href="<?php echo URL_MENU; ?>/canal_escrito_productividad/jafocalizacion_estadisticas" class="btn py-2 px-2 btn-dark mb-1">Jóvenes en Acción y Focalización</a>
                    <a href="<?php echo URL_MENU; ?>/canal_escrito_productividad/tmnc_estadisticas" class="btn py-2 px-2 btn-dark mb-1">Transferencias Monetarias No Condicionadas</a>
                    <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 color-blanco" data-bs-toggle="modal" data-bs-target="#modal-filtro" title="Filtros">
                      <i class="fas fa-filter btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Filtros</span>
                    </button>
                    <div class="col-lg-12 py-1 font-size-12">
                      <?php if($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']!="" AND $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']!=""): ?>
                        Filtros: <?php echo $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']; ?> A <?php echo $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']; ?>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="btn-group-vertical nav nav-tabs" role="group" aria-label="Button group with nested dropdown">
                    <button type="button" class="btn btn-outline-dark px-1 py-1 active" style="text-align: left !important;" id="tm_p1-tab" data-bs-toggle="tab" href="#tm_p1" role="tab" aria-controls="tm_p1" aria-selected="true">
                      1. Proyección de Respuestas
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p2-tab" data-bs-toggle="tab" href="#tm_p2" role="tab" aria-controls="tm_p2" aria-selected="true">
                      2. Aprobación Respuesta
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p3-tab" data-bs-toggle="tab" href="#tm_p3" role="tab" aria-controls="tm_p2" aria-selected="true">
                      3. Clasificación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p4-tab" data-bs-toggle="tab" href="#tm_p4" role="tab" aria-controls="tm_p2" aria-selected="true">
                      4. Envíos
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p5-tab" data-bs-toggle="tab" href="#tm_p5" role="tab" aria-controls="tm_p2" aria-selected="true">
                      5. Firma Respuesta
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p6-tab" data-bs-toggle="tab" href="#tm_p6" role="tab" aria-controls="tm_p2" aria-selected="true">
                      6. Pendientes Clasificación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p7-tab" data-bs-toggle="tab" href="#tm_p7" role="tab" aria-controls="tm_p2" aria-selected="true">
                      7. Casos Sin Gestionar
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p8-tab" data-bs-toggle="tab" href="#tm_p8" role="tab" aria-controls="tm_p2" aria-selected="true">
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
                          <input type="date" class="form-control form-control-sm" name="fecha_inicio" id="fecha_inicio" value="<?php echo $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="fecha_fin">Fecha fin</label>
                          <input type="date" class="form-control form-control-sm" name="fecha_fin" id="fecha_fin" value="<?php echo $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']; ?>" required>
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