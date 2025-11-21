<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Reparto | Estadísticas";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  if(isset($_POST["filtro"])){
    $fecha_inicio=validar_input($_POST['fecha_inicio']);
    $fecha_fin=validar_input($_POST['fecha_fin']);
    
    $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']=$fecha_inicio;
    $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']=$fecha_fin;

    header("Location: reparto_estadisticas");
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
    $filtro_fechas_p1=" AND `cepc_registro_fecha`>=? AND `cepc_registro_fecha`<=?";
    $filtro_fechas_p2=" AND `ceaff_registro_fecha`>=? AND `ceaff_registro_fecha`<=?";
    $filtro_fechas_p3=" AND `ceff_registro_fecha`>=? AND `ceff_registro_fecha`<=?";
    $filtro_fechas_p4=" AND `ceip_registro_fecha`>=? AND `ceip_registro_fecha`<=?";
    $filtro_fechas_p5=" AND `cepfa_registro_fecha`>=? AND `cepfa_registro_fecha`<=?";
    $filtro_fechas_p6=" AND `ceaf_registro_fecha`>=? AND `ceaf_registro_fecha`<=?";
    $filtro_fechas_p7=" AND `ceft_registro_fecha`>=? AND `ceft_registro_fecha`<=?";
    $filtro_fechas_p8=" AND `cep_registro_fecha`>=? AND `cep_registro_fecha`<=?";
    $filtro_fechas_p9=" AND `celtr_registro_fecha`>=? AND `celtr_registro_fecha`<=?";
    $filtro_fechas_p10=" AND `cesew_registro_fecha`>=? AND `cesew_registro_fecha`<=?";
    $filtro_fechas_p11=" AND `cescd_registro_fecha`>=? AND `cescd_registro_fecha`<=?";
    $filtro_fechas_p12=" AND `cesr_registro_fecha`>=? AND `cesr_registro_fecha`<=?";
    $filtro_fechas_p13=" AND `cest_registro_fecha`>=? AND `cest_registro_fecha`<=?";
    $filtro_fechas_p14=" AND `cesit_registro_fecha`>=? AND `cesit_registro_fecha`<=?";
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

    $array_datos_total['p9']['total_gestion']=0;
    $array_datos_total['p9']['total_dias_gestion']=0;
    $array_datos_total['p9']['promedio_diario']=0;
    $array_datos_total['p9']['promedio_hora']=0;
    $array_datos_total['p9']['promedio_agente']=0;
    $array_datos_total['p9']['total_agente']=0;

    $array_datos_total['p10']['total_gestion']=0;
    $array_datos_total['p10']['total_dias_gestion']=0;
    $array_datos_total['p10']['promedio_diario']=0;
    $array_datos_total['p10']['promedio_hora']=0;
    $array_datos_total['p10']['promedio_agente']=0;
    $array_datos_total['p10']['total_agente']=0;

    $array_datos_total['p11']['total_gestion']=0;
    $array_datos_total['p11']['total_dias_gestion']=0;
    $array_datos_total['p11']['promedio_diario']=0;
    $array_datos_total['p11']['promedio_hora']=0;
    $array_datos_total['p11']['promedio_agente']=0;
    $array_datos_total['p11']['total_agente']=0;

    $array_datos_total['p12']['total_gestion']=0;
    $array_datos_total['p12']['total_dias_gestion']=0;
    $array_datos_total['p12']['promedio_diario']=0;
    $array_datos_total['p12']['promedio_hora']=0;
    $array_datos_total['p12']['promedio_agente']=0;
    $array_datos_total['p12']['total_agente']=0;

    $array_datos_total['p13']['total_gestion']=0;
    $array_datos_total['p13']['total_dias_gestion']=0;
    $array_datos_total['p13']['promedio_diario']=0;
    $array_datos_total['p13']['promedio_hora']=0;
    $array_datos_total['p13']['promedio_agente']=0;
    $array_datos_total['p13']['total_agente']=0;

    $array_datos_total['p14']['total_gestion']=0;
    $array_datos_total['p14']['total_dias_gestion']=0;
    $array_datos_total['p14']['promedio_diario']=0;
    $array_datos_total['p14']['promedio_hora']=0;
    $array_datos_total['p14']['promedio_agente']=0;
    $array_datos_total['p14']['total_agente']=0;

    $array_datos_gestion['p1']['gestion_agente']['id']=array();
    $array_datos_gestion['p1']['grupo_responsable_lista']=array();
    $array_datos_gestion['p2']['gestion_agente']['id']=array();
    $array_datos_gestion['p2']['proyector_lista']=array();
    $array_datos_gestion['p2']['estado_lista']=array();
    $array_datos_gestion['p3']['gestion_agente']['id']=array();
    $array_datos_gestion['p3']['modalidad_envio_lista']=array();
    $array_datos_gestion['p4']['gestion_agente']['id']=array();
    $array_datos_gestion['p4']['proyector_lista']=array();
    $array_datos_gestion['p4']['estado_lista']=array();
    $array_datos_gestion['p4']['tipo_rechazo_lista']=array();
    $array_datos_gestion['p5']['gestion_agente']['id']=array();
    $array_datos_gestion['p5']['solicitud_lista']=array();
    $array_datos_gestion['p6']['gestion_agente']['id']=array();
    $array_datos_gestion['p6']['afectaciona_lista']=array();
    $array_datos_gestion['p6']['carta_lista']=array();
    $array_datos_gestion['p6']['proyector_lista']=array();
    $array_datos_gestion['p6']['estado_lista']=array();
    $array_datos_gestion['p7']['gestion_agente']['id']=array();
    $array_datos_gestion['p7']['forma_lista']=array();
    $array_datos_gestion['p8']['gestion_agente']['id']=array();
    $array_datos_gestion['p8']['direccionamiento_lista']=array();
    $array_datos_gestion['p8']['agente_lista']=array();
    $array_datos_gestion['p8']['novedad_lista']=array();
    $array_datos_gestion['p9']['gestion_agente']['id']=array();
    $array_datos_gestion['p9']['area_lista']=array();
    $array_datos_gestion['p9']['grupo_responsable_lista']=array();
    $array_datos_gestion['p10']['gestion_agente']['id']=array();
    $array_datos_gestion['p10']['tipo_envio_lista']=array();
    $array_datos_gestion['p10']['estado_lista']=array();
    $array_datos_gestion['p11']['gestion_agente']['id']=array();
    $array_datos_gestion['p11']['novedad_lista']=array();
    $array_datos_gestion['p12']['gestion_agente']['id']=array();
    $array_datos_gestion['p12']['dependencia_lista']=array();
    $array_datos_gestion['p12']['notifica_lista']=array();
    $array_datos_gestion['p13']['gestion_agente']['id']=array();
    $array_datos_gestion['p13']['direccionamiento_lista']=array();
    $array_datos_gestion['p14']['gestion_agente']['id']=array();
    $array_datos_gestion['p14']['traslado_errado_entidades_lista']=array();
    $array_datos_gestion['p14']['asignacion_ps_errada_lista']=array();
    $array_datos_gestion['p14']['forma_correcta_peticion_lista']=array();
    $array_datos_gestion['p14']['relaciona_informacion_lista']=array();
    $array_datos_gestion['p14']['diligencia_datos_lista']=array();
    $array_datos_gestion['p14']['asignacion_ps_errada_2_lista']=array();



  //1. Proyección Consolidación
    $consulta_string_p1="SELECT `cepc_id`, `cepc_radicado_entrada`, `cepc_tipologia`, `cepc_grupo_responsable`, `cepc_grupo_prorrogas`, `cepc_notificar`, `cepc_registro_usuario`, `cepc_registro_fecha`, TIPOLOGIA.`ceco_valor`, GRESPONSABLE.`ceco_valor`, GPRORROGAS.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_proyeccion_consolidacion` LEFT JOIN `gestion_ce_configuracion` AS TIPOLOGIA ON `gestion_cerep_proyeccion_consolidacion`.`cepc_tipologia`=TIPOLOGIA.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS GRESPONSABLE ON `gestion_cerep_proyeccion_consolidacion`.`cepc_grupo_responsable`=GRESPONSABLE.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS GPRORROGAS ON `gestion_cerep_proyeccion_consolidacion`.`cepc_grupo_prorrogas`=GPRORROGAS.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_consolidacion`.`cepc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p1."";
    $consulta_registros_p1 = $enlace_db->prepare($consulta_string_p1);
    if (count($data_consulta)>0) {
        $consulta_registros_p1->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p1->execute();
    $resultado_registros_p1 = $consulta_registros_p1->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p1)) {
      for ($i=0; $i < count($resultado_registros_p1); $i++) { 
        $array_datos_gestion['p1']['gestion_agente']['id'][]=$resultado_registros_p1[$i][6];
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][6]]['nombre']=$resultado_registros_p1[$i][11];
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][6]]['total']+=1;
        if (!isset($array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][6]]['hora'])) {
          $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][6]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][6]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][6]]['hora'][intval(date('H', strtotime($resultado_registros_p1[$i][7])))]+=1;
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][6]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p1[$i][7]))]+=1;
        
        $array_datos_gestiones['p1']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p1[$i][7]))]+=1;
        $array_datos_gestiones['p1']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p1[$i][7]))]=1;
        $array_datos_gestiones['p1']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p1[$i][7]))]=1;

        $array_datos_gestion['p1']['grupo_responsable_lista'][]=$resultado_registros_p1[$i][3];
        $array_datos_gestion['p1']['grupo_responsable_nombre'][$resultado_registros_p1[$i][3]]=$resultado_registros_p1[$i][9];
        $array_datos_gestion['p1']['grupo_responsable'][$resultado_registros_p1[$i][3]]+=1;
      }

      $array_datos_gestion['p1']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p1']['gestion_agente']['id']));
      
      $array_datos_total['p1']['total_gestion']=count($resultado_registros_p1);
      $array_datos_total['p1']['total_dias_gestion']=count($array_datos_gestiones['p1']['total_dias']);
      $array_datos_total['p1']['promedio_diario']=count($resultado_registros_p1)/count($array_datos_gestiones['p1']['total_dias']);
      $array_datos_total['p1']['promedio_hora']=count($resultado_registros_p1)/count($array_datos_gestiones['p1']['total_dias_hora']);
      $array_datos_total['p1']['promedio_agente']=count($resultado_registros_p1)/count($array_datos_gestion['p1']['gestion_agente']['id']);
      $array_datos_total['p1']['total_agente']=count($array_datos_gestion['p1']['gestion_agente']['id']);

      $array_datos_gestion['p1']['grupo_responsable_lista']=array_values(array_unique($array_datos_gestion['p1']['grupo_responsable_lista']));
    }


  //2. Aprobación Firma FA
    $consulta_string_p2="SELECT `ceaff_id`, `ceaff_radicado`, `ceaff_proyector`, `ceaff_estado`, `ceaff_observaciones`, `ceaff_notificar`, `ceaff_registro_usuario`, `ceaff_registro_fecha`, PROYECTOR.`usu_nombres_apellidos`, ESTADO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_aprobacion_firma_fa` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_proyector`=PROYECTOR.`usu_id` LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_estado`=ESTADO.`ceco_id`  LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p2."";
    $consulta_registros_p2 = $enlace_db->prepare($consulta_string_p2);
    if (count($data_consulta)>0) {
        $consulta_registros_p2->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p2->execute();
    $resultado_registros_p2 = $consulta_registros_p2->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p2)) {
      for ($i=0; $i < count($resultado_registros_p2); $i++) { 
        $array_datos_gestion['p2']['gestion_agente']['id'][]=$resultado_registros_p2[$i][6];
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][6]]['nombre']=$resultado_registros_p2[$i][10];
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][6]]['total']+=1;
        if (!isset($array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][6]]['hora'])) {
          $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][6]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][6]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][6]]['hora'][intval(date('H', strtotime($resultado_registros_p2[$i][7])))]+=1;
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][6]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p2[$i][7]))]+=1;
        
        $array_datos_gestiones['p2']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p2[$i][7]))]+=1;
        $array_datos_gestiones['p2']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p2[$i][7]))]=1;
        $array_datos_gestiones['p2']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p2[$i][7]))]=1;

        $array_datos_gestion['p2']['proyector_lista'][]=$resultado_registros_p2[$i][2];
        $array_datos_gestion['p2']['proyector_nombre'][$resultado_registros_p2[$i][2]]=$resultado_registros_p2[$i][8];
        $array_datos_gestion['p2']['proyector'][$resultado_registros_p2[$i][2]][$resultado_registros_p2[$i][3]]+=1;

        $array_datos_gestion['p2']['estado_lista'][]=$resultado_registros_p2[$i][3];
        $array_datos_gestion['p2']['estado_nombre'][$resultado_registros_p2[$i][3]]=$resultado_registros_p2[$i][9];
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
    }


  //3. Firma FA
    $consulta_string_p3="SELECT `ceff_id`, `ceff_radicado_entrada`, `ceff_radicado_salida`, `ceff_modalidad_envio`, `ceff_observaciones`, `ceff_notificar`, `ceff_registro_usuario`, `ceff_registro_fecha`, MENVIO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_firma_fa` LEFT JOIN `gestion_ce_configuracion` AS MENVIO ON `gestion_cerep_firma_fa`.`ceff_modalidad_envio`=MENVIO.`ceco_id`  LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_firma_fa`.`ceff_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p3."";
    $consulta_registros_p3 = $enlace_db->prepare($consulta_string_p3);
    if (count($data_consulta)>0) {
        $consulta_registros_p3->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p3->execute();
    $resultado_registros_p3 = $consulta_registros_p3->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p3)) {
      for ($i=0; $i < count($resultado_registros_p3); $i++) { 
        $array_datos_gestion['p3']['gestion_agente']['id'][]=$resultado_registros_p3[$i][6];
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][6]]['nombre']=$resultado_registros_p3[$i][9];
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][6]]['total']+=1;
        if (!isset($array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][6]]['hora'])) {
          $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][6]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][6]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][6]]['hora'][intval(date('H', strtotime($resultado_registros_p3[$i][7])))]+=1;
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][6]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p3[$i][7]))]+=1;
        
        $array_datos_gestiones['p3']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p3[$i][7]))]+=1;
        $array_datos_gestiones['p3']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p3[$i][7]))]=1;
        $array_datos_gestiones['p3']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p3[$i][7]))]=1;

        $array_datos_gestion['p3']['modalidad_envio_lista'][]=$resultado_registros_p3[$i][3];
        $array_datos_gestion['p3']['modalidad_envio_nombre'][$resultado_registros_p3[$i][3]]=$resultado_registros_p3[$i][8];
        $array_datos_gestion['p3']['modalidad_envio'][$resultado_registros_p3[$i][3]]+=1;
      }

      $array_datos_gestion['p3']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p3']['gestion_agente']['id']));
      
      $array_datos_total['p3']['total_gestion']=count($resultado_registros_p3);
      $array_datos_total['p3']['total_dias_gestion']=count($array_datos_gestiones['p3']['total_dias']);
      $array_datos_total['p3']['promedio_diario']=count($resultado_registros_p3)/count($array_datos_gestiones['p3']['total_dias']);
      $array_datos_total['p3']['promedio_hora']=count($resultado_registros_p3)/count($array_datos_gestiones['p3']['total_dias_hora']);
      $array_datos_total['p3']['promedio_agente']=count($resultado_registros_p3)/count($array_datos_gestion['p3']['gestion_agente']['id']);
      $array_datos_total['p3']['total_agente']=count($array_datos_gestion['p3']['gestion_agente']['id']);

      $array_datos_gestion['p3']['modalidad_envio_lista']=array_values(array_unique($array_datos_gestion['p3']['modalidad_envio_lista']));
    }


  //4. Inspección Proyección
    $consulta_string_p4="SELECT `ceip_id`, `ceip_radicado_entrada`, `ceip_proyector_carta`, `ceip_estado`, `ceip_tipo_rechazo`, `ceip_observaciones`, `ceip_notificar`, `ceip_registro_usuario`, `ceip_registro_fecha`, PROYECTOR.`usu_nombres_apellidos`, ESTADO.`ceco_valor`, TRECHAZO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_inspeccion_proyeccion` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_inspeccion_proyeccion`.`ceip_proyector_carta`=PROYECTOR.`usu_id` LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cerep_inspeccion_proyeccion`.`ceip_estado`=ESTADO.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS TRECHAZO ON `gestion_cerep_inspeccion_proyeccion`.`ceip_tipo_rechazo`=TRECHAZO.`ceco_id`  LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_inspeccion_proyeccion`.`ceip_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p4."";
    $consulta_registros_p4 = $enlace_db->prepare($consulta_string_p4);
    if (count($data_consulta)>0) {
        $consulta_registros_p4->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p4->execute();
    $resultado_registros_p4 = $consulta_registros_p4->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='inspeccion_proyeccion' ORDER BY `ceco_campo`, `ceco_valor`";
    $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
    $consulta_registros_parametros->execute();
    $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

    for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
        $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
    }

    if (count($resultado_registros_p4)) {
      $array_datos_gestion['p4']['tipo_rechazo_lista']=array();
      for ($i=0; $i < count($resultado_registros_p4); $i++) { 
        $array_datos_gestion['p4']['gestion_agente']['id'][]=$resultado_registros_p4[$i][7];
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][7]]['nombre']=$resultado_registros_p4[$i][12];
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][7]]['total']+=1;
        if (!isset($array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][7]]['hora'])) {
          $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][7]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][7]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][7]]['hora'][intval(date('H', strtotime($resultado_registros_p4[$i][8])))]+=1;
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][7]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p4[$i][8]))]+=1;
        
        $array_datos_gestiones['p4']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p4[$i][8]))]+=1;
        $array_datos_gestiones['p4']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p4[$i][8]))]=1;
        $array_datos_gestiones['p4']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p4[$i][8]))]=1;

        $array_datos_gestion['p4']['proyector_lista'][]=$resultado_registros_p4[$i][2];
        $array_datos_gestion['p4']['proyector_nombre'][$resultado_registros_p4[$i][2]]=$resultado_registros_p4[$i][9];
        $array_datos_gestion['p4']['proyector'][$resultado_registros_p4[$i][2]][$resultado_registros_p4[$i][3]]+=1;
        
        $array_datos_gestion['p4']['estado_lista'][]=$resultado_registros_p4[$i][3];
        $array_datos_gestion['p4']['estado_nombre'][$resultado_registros_p4[$i][3]]=$resultado_registros_p4[$i][10];

        if ($resultado_registros_p4[$i][4]!="") {
          $ceip_tipo_rechazo=explode(';', $resultado_registros_p4[$i][4]);
          $ceip_tipo_rechazo_mostrar='';
          for ($j=0; $j < count($ceip_tipo_rechazo); $j++) {
              if ($ceip_tipo_rechazo[$j]!="") {
                  $array_datos_gestion['p4']['tipo_rechazo_lista'][]=$ceip_tipo_rechazo[$j];
                  $array_datos_gestion['p4']['tipo_rechazo_nombre'][$ceip_tipo_rechazo[$j]]=$array_parametros['tipo_rechazo']['texto'][$ceip_tipo_rechazo[$j]];
                  $array_datos_gestion['p4']['proyector_tipo_rechazo'][$resultado_registros_p4[$i][2]][$ceip_tipo_rechazo[$j]]+=1;
                  $array_datos_gestion['p4']['proyector_tipo_rechazo_total'][$resultado_registros_p4[$i][2]]+=1;
              }
          }
        }
      }

      $array_datos_gestion['p4']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p4']['gestion_agente']['id']));
      
      $array_datos_total['p4']['total_gestion']=count($resultado_registros_p4);
      $array_datos_total['p4']['total_dias_gestion']=count($array_datos_gestiones['p4']['total_dias']);
      $array_datos_total['p4']['promedio_diario']=count($resultado_registros_p4)/count($array_datos_gestiones['p4']['total_dias']);
      $array_datos_total['p4']['promedio_hora']=count($resultado_registros_p4)/count($array_datos_gestiones['p4']['total_dias_hora']);
      $array_datos_total['p4']['promedio_agente']=count($resultado_registros_p4)/count($array_datos_gestion['p4']['gestion_agente']['id']);
      $array_datos_total['p4']['total_agente']=count($array_datos_gestion['p4']['gestion_agente']['id']);

      $array_datos_gestion['p4']['proyector_lista']=array_values(array_unique($array_datos_gestion['p4']['proyector_lista']));
      $array_datos_gestion['p4']['estado_lista']=array_values(array_unique($array_datos_gestion['p4']['estado_lista']));
      $array_datos_gestion['p4']['tipo_rechazo_lista']=array_values(array_unique($array_datos_gestion['p4']['tipo_rechazo_lista']));
    }


  //5. Proyección FA
    $consulta_string_p5="SELECT `cepfa_id`, `cepfa_radicado_entrada`, `cepfa_documento_identidad`, `cepfa_nombre_ciudadano`, `cepfa_correo_direccion`, `cepfa_departamento`, `cepfa_solicitud_novedad`, `cepfa_observaciones`, `cepfa_notificar`, `cepfa_registro_usuario`, `cepfa_registro_fecha`, DPTO.`ciu_departamento`, SOLICITUD.`ceco_valor`, TU.`usu_nombres_apellidos`, `cepfa_abogado_aprobador`, APROBADOR.`usu_nombres_apellidos` FROM `gestion_cerep_proyeccion_fa` LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cerep_proyeccion_fa`.`cepfa_abogado_aprobador`=APROBADOR.`usu_id` LEFT JOIN `administrador_departamentos` AS DPTO ON `gestion_cerep_proyeccion_fa`.`cepfa_departamento`=DPTO.`ciu_codigo` LEFT JOIN `gestion_ce_configuracion` AS SOLICITUD ON `gestion_cerep_proyeccion_fa`.`cepfa_solicitud_novedad`=SOLICITUD.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_fa`.`cepfa_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p5."";
    $consulta_registros_p5 = $enlace_db->prepare($consulta_string_p5);
    if (count($data_consulta)>0) {
        $consulta_registros_p5->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p5->execute();
    $resultado_registros_p5 = $consulta_registros_p5->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p5)) {
      for ($i=0; $i < count($resultado_registros_p5); $i++) { 
        $array_datos_gestion['p5']['gestion_agente']['id'][]=$resultado_registros_p5[$i][9];
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][9]]['nombre']=$resultado_registros_p5[$i][13];
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][9]]['total']+=1;
        if (!isset($array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][9]]['hora'])) {
          $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][9]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][9]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][9]]['hora'][intval(date('H', strtotime($resultado_registros_p5[$i][10])))]+=1;
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][9]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p5[$i][10]))]+=1;
        
        $array_datos_gestiones['p5']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p5[$i][10]))]+=1;
        $array_datos_gestiones['p5']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p5[$i][10]))]=1;
        $array_datos_gestiones['p5']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p5[$i][10]))]=1;

        $array_datos_gestion['p5']['solicitud_lista'][]=$resultado_registros_p5[$i][6];
        $array_datos_gestion['p5']['solicitud_nombre'][$resultado_registros_p5[$i][6]]=$resultado_registros_p5[$i][12];
        $array_datos_gestion['p5']['solicitud'][$resultado_registros_p5[$i][6]]+=1;

        //Mapa colombia
          if ($resultado_registros_p5[$i][11]=='ARCHIPIÉLAGO DE SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA') {
            $array_mapa_p5['co-sa']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='CAUCA') {
            $array_mapa_p5['co-ca']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='NARIÑO') {
            $array_mapa_p5['co-na']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='CHOCÓ') {
            $array_mapa_p5['co-ch']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='TOLIMA') {
            $array_mapa_p5['co-to']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='CAQUETÁ') {
            $array_mapa_p5['co-cq']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='HUILA') {
            $array_mapa_p5['co-hu']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='PUTUMAYO') {
            $array_mapa_p5['co-pu']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='AMAZONAS') {
            $array_mapa_p5['co-am']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='BOLÍVAR') {
            $array_mapa_p5['co-bl']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='VALLE DEL CAUCA') {
            $array_mapa_p5['co-vc']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='SUCRE') {
            $array_mapa_p5['co-su']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='ATLÁNTICO') {
            $array_mapa_p5['co-at']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='CESAR') {
            $array_mapa_p5['co-ce']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='LA GUAJIRA') {
            $array_mapa_p5['co-lg']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='MAGDALENA') {
            $array_mapa_p5['co-ma']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='ARAUCA') {
            $array_mapa_p5['co-ar']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='NORTE DE SANTANDER') {
            $array_mapa_p5['co-ns']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='CASANARE') {
            $array_mapa_p5['co-cs']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='GUAVIARE') {
            $array_mapa_p5['co-gv']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='META') {
            $array_mapa_p5['co-me']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='VAUPÉS') {
            $array_mapa_p5['co-vp']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='VICHADA') {
            $array_mapa_p5['co-vd']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='ANTIOQUIA') {
            $array_mapa_p5['co-an']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='CÓRDOBA') {
            $array_mapa_p5['co-co']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='BOYACÁ') {
            $array_mapa_p5['co-by']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='SANTANDER') {
            $array_mapa_p5['co-st']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='CALDAS') {
            $array_mapa_p5['co-cl']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='CUNDINAMARCA') {
            $array_mapa_p5['co-cu']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='BOGOTÁ, D.C.') {
            $array_mapa_p5['co-1136']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='RISARALDA') {
            $array_mapa_p5['co-ri']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='QUINDÍO') {
            $array_mapa_p5['co-qd']+=1;
          } elseif ($resultado_registros_p5[$i][11]=='GUAINÍA') {
            $array_mapa_p5['co-gn']+=1;
          }
      }

      $array_datos_gestion['p5']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p5']['gestion_agente']['id']));
      
      $array_datos_total['p5']['total_gestion']=count($resultado_registros_p5);
      $array_datos_total['p5']['total_dias_gestion']=count($array_datos_gestiones['p5']['total_dias']);
      $array_datos_total['p5']['promedio_diario']=count($resultado_registros_p5)/count($array_datos_gestiones['p5']['total_dias']);
      $array_datos_total['p5']['promedio_hora']=count($resultado_registros_p5)/count($array_datos_gestiones['p5']['total_dias_hora']);
      $array_datos_total['p5']['promedio_agente']=count($resultado_registros_p5)/count($array_datos_gestion['p5']['gestion_agente']['id']);
      $array_datos_total['p5']['total_agente']=count($array_datos_gestion['p5']['gestion_agente']['id']);

      $array_datos_gestion['p5']['solicitud_lista']=array_values(array_unique($array_datos_gestion['p5']['solicitud_lista']));
    }


  //6. Aprobación Firma
    $consulta_string_p6="SELECT `ceaf_id`, `ceaf_radicado`, `ceaf_tipificador`, `ceaf_proyector`, `ceaf_carta`, `ceaf_estado`, `ceaf_observaciones`, `ceaf_afectacion`, `ceaf_notificar`, `ceaf_registro_usuario`, `ceaf_registro_fecha`, TIPIFICADOR.`usu_nombres_apellidos`, PROYECTOR.`usu_nombres_apellidos`, CARTA.`ceco_valor`, ESTADO.`ceco_valor`, AFECTACION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_aprobacion_firma` LEFT JOIN `administrador_usuario` AS TIPIFICADOR ON `gestion_cerep_aprobacion_firma`.`ceaf_tipificador`=TIPIFICADOR.`usu_id` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_aprobacion_firma`.`ceaf_proyector`=PROYECTOR.`usu_id` LEFT JOIN `gestion_ce_configuracion` AS CARTA ON `gestion_cerep_aprobacion_firma`.`ceaf_carta`=CARTA.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cerep_aprobacion_firma`.`ceaf_estado`=ESTADO.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS AFECTACION ON `gestion_cerep_aprobacion_firma`.`ceaf_afectacion`=AFECTACION.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_aprobacion_firma`.`ceaf_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p6."";
    $consulta_registros_p6 = $enlace_db->prepare($consulta_string_p6);
    if (count($data_consulta)>0) {
        $consulta_registros_p6->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p6->execute();
    $resultado_registros_p6 = $consulta_registros_p6->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p6)) {
      for ($i=0; $i < count($resultado_registros_p6); $i++) { 
        $array_datos_gestion['p6']['gestion_agente']['id'][]=$resultado_registros_p6[$i][9];
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][9]]['nombre']=$resultado_registros_p6[$i][16];
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][9]]['total']+=1;
        if (!isset($array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][9]]['hora'])) {
          $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][9]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][9]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][9]]['hora'][intval(date('H', strtotime($resultado_registros_p6[$i][10])))]+=1;
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][9]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p6[$i][10]))]+=1;
        
        $array_datos_gestiones['p6']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p6[$i][10]))]+=1;
        $array_datos_gestiones['p6']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p6[$i][10]))]=1;
        $array_datos_gestiones['p6']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p6[$i][10]))]=1;

        if ($resultado_registros_p6[$i][7]!='') {//Afectación a
          $array_datos_gestion['p6']['afectaciona_lista'][]=$resultado_registros_p6[$i][7];
          $array_datos_gestion['p6']['afectaciona_nombre'][$resultado_registros_p6[$i][7]]=$resultado_registros_p6[$i][15];
          $array_datos_gestion['p6']['afectaciona'][$resultado_registros_p6[$i][7]]+=1;
        }

        if ($resultado_registros_p6[$i][4]!='') {//Carta
          $array_datos_gestion['p6']['carta_lista'][]=$resultado_registros_p6[$i][4];
          $array_datos_gestion['p6']['carta_nombre'][$resultado_registros_p6[$i][4]]=$resultado_registros_p6[$i][13];
          $array_datos_gestion['p6']['carta'][$resultado_registros_p6[$i][4]]+=1;
        }





        $array_datos_gestion['p6']['proyector_lista'][]=$resultado_registros_p6[$i][3];
        $array_datos_gestion['p6']['proyector_nombre'][$resultado_registros_p6[$i][3]]=$resultado_registros_p6[$i][12];
        
        $array_datos_gestion['p6']['estado_lista'][]=$resultado_registros_p6[$i][5];
        $array_datos_gestion['p6']['estado_nombre'][$resultado_registros_p6[$i][5]]=$resultado_registros_p6[$i][14];

        if ($resultado_registros_p6[$i][5]!="") {
          $array_datos_gestion['p6']['proyector_estado'][$resultado_registros_p6[$i][3]][$resultado_registros_p6[$i][5]]+=1;
          $array_datos_gestion['p6']['proyector_estado_total'][$resultado_registros_p6[$i][3]]+=1;
        }

      }

      $array_datos_gestion['p6']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p6']['gestion_agente']['id']));
      
      $array_datos_total['p6']['total_gestion']=count($resultado_registros_p6);
      $array_datos_total['p6']['total_dias_gestion']=count($array_datos_gestiones['p6']['total_dias']);
      $array_datos_total['p6']['promedio_diario']=count($resultado_registros_p6)/count($array_datos_gestiones['p6']['total_dias']);
      $array_datos_total['p6']['promedio_hora']=count($resultado_registros_p6)/count($array_datos_gestiones['p6']['total_dias_hora']);
      $array_datos_total['p6']['promedio_agente']=count($resultado_registros_p6)/count($array_datos_gestion['p6']['gestion_agente']['id']);
      $array_datos_total['p6']['total_agente']=count($array_datos_gestion['p6']['gestion_agente']['id']);

      $array_datos_gestion['p6']['afectaciona_lista']=array_values(array_unique($array_datos_gestion['p6']['afectaciona_lista']));
      $array_datos_gestion['p6']['carta_lista']=array_values(array_unique($array_datos_gestion['p6']['carta_lista']));

      $array_datos_gestion['p6']['proyector_lista']=array_values(array_unique($array_datos_gestion['p6']['proyector_lista']));
      $array_datos_gestion['p6']['estado_lista']=array_values(array_unique($array_datos_gestion['p6']['estado_lista']));
    }


  //7. Firma Traslados
    $consulta_string_p7="SELECT `ceft_id`, `ceft_radicado_entrada`, `ceft_radicado_salida`, `ceft_rechazos`, `ceft_forma`, `ceft_proyector`, `ceft_inspector`, `ceft_aprobador`, `ceft_observaciones`, `ceft_notificar`, `ceft_registro_usuario`, `ceft_registro_fecha`, RECHAZOS.`ceco_valor`, FORMA.`ceco_valor`, TU.`usu_nombres_apellidos`, PROYECTOR.`usu_nombres_apellidos`, INSPECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos` FROM `gestion_cerep_firma_traslados` LEFT JOIN `gestion_ce_configuracion` AS RECHAZOS ON `gestion_cerep_firma_traslados`.`ceft_rechazos`=RECHAZOS.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS FORMA ON `gestion_cerep_firma_traslados`.`ceft_forma`=FORMA.`ceco_id` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_firma_traslados`.`ceft_proyector`=PROYECTOR.`usu_id` LEFT JOIN `administrador_usuario` AS INSPECTOR ON `gestion_cerep_firma_traslados`.`ceft_inspector`=INSPECTOR.`usu_id` LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cerep_firma_traslados`.`ceft_aprobador`=APROBADOR.`usu_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_firma_traslados`.`ceft_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p7."";
    $consulta_registros_p7 = $enlace_db->prepare($consulta_string_p7);
    if (count($data_consulta)>0) {
        $consulta_registros_p7->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p7->execute();
    $resultado_registros_p7 = $consulta_registros_p7->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p7)) {
      for ($i=0; $i < count($resultado_registros_p7); $i++) { 
        $array_datos_gestion['p7']['gestion_agente']['id'][]=$resultado_registros_p7[$i][10];
        $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][10]]['nombre']=$resultado_registros_p7[$i][14];
        $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][10]]['total']+=1;
        if (!isset($array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][10]]['hora'])) {
          $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][10]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][10]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][10]]['hora'][intval(date('H', strtotime($resultado_registros_p7[$i][11])))]+=1;
        $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][10]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p7[$i][11]))]+=1;
        
        $array_datos_gestiones['p7']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p7[$i][11]))]+=1;
        $array_datos_gestiones['p7']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p7[$i][11]))]=1;
        $array_datos_gestiones['p7']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p7[$i][11]))]=1;

        if ($resultado_registros_p7[$i][4]!='') {
          $array_datos_gestion['p7']['forma_lista'][]=$resultado_registros_p7[$i][4];
          $array_datos_gestion['p7']['forma_nombre'][$resultado_registros_p7[$i][4]]=$resultado_registros_p7[$i][13];
          $array_datos_gestion['p7']['forma'][$resultado_registros_p7[$i][4]]+=1;
        }
      }

      $array_datos_gestion['p7']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p7']['gestion_agente']['id']));
      
      $array_datos_total['p7']['total_gestion']=count($resultado_registros_p7);
      $array_datos_total['p7']['total_dias_gestion']=count($array_datos_gestiones['p7']['total_dias']);
      $array_datos_total['p7']['promedio_diario']=count($resultado_registros_p7)/count($array_datos_gestiones['p7']['total_dias']);
      $array_datos_total['p7']['promedio_hora']=count($resultado_registros_p7)/count($array_datos_gestiones['p7']['total_dias_hora']);
      $array_datos_total['p7']['promedio_agente']=count($resultado_registros_p7)/count($array_datos_gestion['p7']['gestion_agente']['id']);
      $array_datos_total['p7']['total_agente']=count($array_datos_gestion['p7']['gestion_agente']['id']);

      $array_datos_gestion['p7']['forma_lista']=array_values(array_unique($array_datos_gestion['p7']['forma_lista']));
    }


  //8. Proyectores
    $consulta_string_p8="SELECT `cep_id`, `cep_radicado_entrada`, `cep_direccionamiento`, `cep_observacion_traslado`, `cep_documento_identidad`, `cep_nombre_ciudadano`, `cep_correo_direccion`, `cep_departamento`, `cep_novedad_radicado`, `cep_observaciones`, `cep_notificar`, `cep_registro_usuario`, `cep_registro_fecha`, DIRECCIONAMIENTO.`ceco_valor`, DEPARTAMENTO.`ciu_departamento`, NOVEDAD.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_proyectores` LEFT JOIN `gestion_ce_configuracion` AS DIRECCIONAMIENTO ON `gestion_cerep_proyectores`.`cep_direccionamiento`=DIRECCIONAMIENTO.`ceco_id` LEFT JOIN `administrador_departamentos` AS DEPARTAMENTO ON `gestion_cerep_proyectores`.`cep_departamento`=DEPARTAMENTO.`ciu_codigo` LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cerep_proyectores`.`cep_novedad_radicado`=NOVEDAD.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyectores`.`cep_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p8."";
    $consulta_registros_p8 = $enlace_db->prepare($consulta_string_p8);
    if (count($data_consulta)>0) {
        $consulta_registros_p8->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p8->execute();
    $resultado_registros_p8 = $consulta_registros_p8->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p8)) {
      for ($i=0; $i < count($resultado_registros_p8); $i++) { 
        $array_datos_gestion['p8']['gestion_agente']['id'][]=$resultado_registros_p8[$i][11];
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][11]]['nombre']=$resultado_registros_p8[$i][16];
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][11]]['total']+=1;
        if (!isset($array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][11]]['hora'])) {
          $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][11]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][11]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][11]]['hora'][intval(date('H', strtotime($resultado_registros_p8[$i][12])))]+=1;
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][11]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p8[$i][12]))]+=1;
        
        $array_datos_gestiones['p8']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p8[$i][12]))]+=1;
        $array_datos_gestiones['p8']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p8[$i][12]))]=1;
        $array_datos_gestiones['p8']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p8[$i][12]))]=1;

        $array_datos_gestion['p8']['direccionamiento_lista'][]=$resultado_registros_p8[$i][2];
        $array_datos_gestion['p8']['direccionamiento_nombre'][$resultado_registros_p8[$i][2]]=$resultado_registros_p8[$i][13];
        $array_datos_gestion['p8']['direccionamiento'][$resultado_registros_p8[$i][2]]+=1;

        //Mapa colombia
          if ($resultado_registros_p8[$i][14]=='ARCHIPIÉLAGO DE SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA') {
            $array_mapa_p8['co-sa']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='CAUCA') {
            $array_mapa_p8['co-ca']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='NARIÑO') {
            $array_mapa_p8['co-na']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='CHOCÓ') {
            $array_mapa_p8['co-ch']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='TOLIMA') {
            $array_mapa_p8['co-to']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='CAQUETÁ') {
            $array_mapa_p8['co-cq']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='HUILA') {
            $array_mapa_p8['co-hu']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='PUTUMAYO') {
            $array_mapa_p8['co-pu']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='AMAZONAS') {
            $array_mapa_p8['co-am']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='BOLÍVAR') {
            $array_mapa_p8['co-bl']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='VALLE DEL CAUCA') {
            $array_mapa_p8['co-vc']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='SUCRE') {
            $array_mapa_p8['co-su']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='ATLÁNTICO') {
            $array_mapa_p8['co-at']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='CESAR') {
            $array_mapa_p8['co-ce']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='LA GUAJIRA') {
            $array_mapa_p8['co-lg']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='MAGDALENA') {
            $array_mapa_p8['co-ma']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='ARAUCA') {
            $array_mapa_p8['co-ar']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='NORTE DE SANTANDER') {
            $array_mapa_p8['co-ns']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='CASANARE') {
            $array_mapa_p8['co-cs']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='GUAVIARE') {
            $array_mapa_p8['co-gv']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='META') {
            $array_mapa_p8['co-me']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='VAUPÉS') {
            $array_mapa_p8['co-vp']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='VICHADA') {
            $array_mapa_p8['co-vd']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='ANTIOQUIA') {
            $array_mapa_p8['co-an']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='CÓRDOBA') {
            $array_mapa_p8['co-co']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='BOYACÁ') {
            $array_mapa_p8['co-by']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='SANTANDER') {
            $array_mapa_p8['co-st']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='CALDAS') {
            $array_mapa_p8['co-cl']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='CUNDINAMARCA') {
            $array_mapa_p8['co-cu']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='BOGOTÁ, D.C.') {
            $array_mapa_p8['co-1136']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='RISARALDA') {
            $array_mapa_p8['co-ri']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='QUINDÍO') {
            $array_mapa_p8['co-qd']+=1;
          } elseif ($resultado_registros_p8[$i][14]=='GUAINÍA') {
            $array_mapa_p8['co-gn']+=1;
          }

        $array_datos_gestion['p8']['agente_lista'][]=$resultado_registros_p8[$i][11];
        $array_datos_gestion['p8']['agente_nombre'][$resultado_registros_p8[$i][11]]=$resultado_registros_p8[$i][16];
        $array_datos_gestion['p8']['agente'][$resultado_registros_p8[$i][11]][$resultado_registros_p8[$i][8]]+=1;
        $array_datos_gestion['p8']['agente_novedad'][$resultado_registros_p8[$i][11]][$resultado_registros_p8[$i][8]]+=1;

        $array_datos_gestion['p8']['novedad_lista'][]=$resultado_registros_p8[$i][8];
        $array_datos_gestion['p8']['novedad_nombre'][$resultado_registros_p8[$i][8]]=$resultado_registros_p8[$i][15];
      }

      $array_datos_gestion['p8']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p8']['gestion_agente']['id']));
      
      $array_datos_total['p8']['total_gestion']=count($resultado_registros_p8);
      $array_datos_total['p8']['total_dias_gestion']=count($array_datos_gestiones['p8']['total_dias']);
      $array_datos_total['p8']['promedio_diario']=count($resultado_registros_p8)/count($array_datos_gestiones['p8']['total_dias']);
      $array_datos_total['p8']['promedio_hora']=count($resultado_registros_p8)/count($array_datos_gestiones['p8']['total_dias_hora']);
      $array_datos_total['p8']['promedio_agente']=count($resultado_registros_p8)/count($array_datos_gestion['p8']['gestion_agente']['id']);
      $array_datos_total['p8']['total_agente']=count($array_datos_gestion['p8']['gestion_agente']['id']);

      $array_datos_gestion['p8']['direccionamiento_lista']=array_values(array_unique($array_datos_gestion['p8']['direccionamiento_lista']));

      $array_datos_gestion['p8']['agente_lista']=array_values(array_unique($array_datos_gestion['p8']['agente_lista']));
      $array_datos_gestion['p8']['novedad_lista']=array_values(array_unique($array_datos_gestion['p8']['novedad_lista']));
    }


  //9. Seguimiento Lanzamientos TR
    $consulta_string_p9="SELECT `celtr_id`, `celtr_radicado`, `celtr_area`, `celtr_responsable_grupo`, `celtr_observaciones`, `celtr_notificar`, `celtr_registro_usuario`, `celtr_registro_fecha`, AREA.`ceco_valor`, GRUPO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_lanzamientos_tr` LEFT JOIN `gestion_ce_configuracion` AS AREA ON `gestion_cerep_seguimiento_lanzamientos_tr`.`celtr_area`=AREA.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS GRUPO ON `gestion_cerep_seguimiento_lanzamientos_tr`.`celtr_responsable_grupo`=GRUPO.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_lanzamientos_tr`.`celtr_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p9."";
    $consulta_registros_p9 = $enlace_db->prepare($consulta_string_p9);
    if (count($data_consulta)>0) {
        $consulta_registros_p9->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p9->execute();
    $resultado_registros_p9 = $consulta_registros_p9->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p9)) {
      for ($i=0; $i < count($resultado_registros_p9); $i++) { 
        $array_datos_gestion['p9']['gestion_agente']['id'][]=$resultado_registros_p9[$i][6];
        $array_datos_gestion['p9']['gestion_agente'][$resultado_registros_p9[$i][6]]['nombre']=$resultado_registros_p9[$i][10];
        $array_datos_gestion['p9']['gestion_agente'][$resultado_registros_p9[$i][6]]['total']+=1;
        if (!isset($array_datos_gestion['p9']['gestion_agente'][$resultado_registros_p9[$i][6]]['hora'])) {
          $array_datos_gestion['p9']['gestion_agente'][$resultado_registros_p9[$i][6]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p9']['gestion_agente'][$resultado_registros_p9[$i][6]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p9']['gestion_agente'][$resultado_registros_p9[$i][6]]['hora'][intval(date('H', strtotime($resultado_registros_p9[$i][7])))]+=1;
        $array_datos_gestion['p9']['gestion_agente'][$resultado_registros_p9[$i][6]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p9[$i][7]))]+=1;
        
        $array_datos_gestiones['p9']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p9[$i][7]))]+=1;
        $array_datos_gestiones['p9']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p9[$i][7]))]=1;
        $array_datos_gestiones['p9']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p9[$i][7]))]=1;

        $array_datos_gestion['p9']['area_lista'][]=$resultado_registros_p9[$i][2];
        $array_datos_gestion['p9']['area_nombre'][$resultado_registros_p9[$i][2]]=$resultado_registros_p9[$i][8];
        $array_datos_gestion['p9']['area'][$resultado_registros_p9[$i][2]]+=1;

        $array_datos_gestion['p9']['grupo_responsable_lista'][]=$resultado_registros_p9[$i][3];
        $array_datos_gestion['p9']['grupo_responsable_nombre'][$resultado_registros_p9[$i][3]]=$resultado_registros_p9[$i][9];
        $array_datos_gestion['p9']['grupo_responsable'][$resultado_registros_p9[$i][3]]+=1;
      }

      $array_datos_gestion['p9']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p9']['gestion_agente']['id']));
      
      $array_datos_total['p9']['total_gestion']=count($resultado_registros_p9);
      $array_datos_total['p9']['total_dias_gestion']=count($array_datos_gestiones['p9']['total_dias']);
      $array_datos_total['p9']['promedio_diario']=count($resultado_registros_p9)/count($array_datos_gestiones['p9']['total_dias']);
      $array_datos_total['p9']['promedio_hora']=count($resultado_registros_p9)/count($array_datos_gestiones['p9']['total_dias_hora']);
      $array_datos_total['p9']['promedio_agente']=count($resultado_registros_p9)/count($array_datos_gestion['p9']['gestion_agente']['id']);
      $array_datos_total['p9']['total_agente']=count($array_datos_gestion['p9']['gestion_agente']['id']);

      $array_datos_gestion['p9']['area_lista']=array_values(array_unique($array_datos_gestion['p9']['area_lista']));
      $array_datos_gestion['p9']['grupo_responsable_lista']=array_values(array_unique($array_datos_gestion['p9']['grupo_responsable_lista']));
    }


  //10. Seguimiento Envíos Web
    $consulta_string_p10="SELECT `cesew_id`, `cesew_radicado_entrada`, `cesew_radicado_salida`, `cesew_tipo_envio`, `cesew_estado`, `cesew_observaciones`, `cesew_notificar`, `cesew_registro_usuario`, `cesew_registro_fecha`, TIPOENVIO.`ceco_valor`, ESTADO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_envios_web` LEFT JOIN `gestion_ce_configuracion` AS TIPOENVIO ON `gestion_cerep_seguimiento_envios_web`.`cesew_tipo_envio`=TIPOENVIO.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cerep_seguimiento_envios_web`.`cesew_estado`=ESTADO.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_envios_web`.`cesew_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p10."";
    $consulta_registros_p10 = $enlace_db->prepare($consulta_string_p10);
    if (count($data_consulta)>0) {
        $consulta_registros_p10->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p10->execute();
    $resultado_registros_p10 = $consulta_registros_p10->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p10)) {
      for ($i=0; $i < count($resultado_registros_p10); $i++) { 
        $array_datos_gestion['p10']['gestion_agente']['id'][]=$resultado_registros_p10[$i][7];
        $array_datos_gestion['p10']['gestion_agente'][$resultado_registros_p10[$i][7]]['nombre']=$resultado_registros_p10[$i][11];
        $array_datos_gestion['p10']['gestion_agente'][$resultado_registros_p10[$i][7]]['total']+=1;
        if (!isset($array_datos_gestion['p10']['gestion_agente'][$resultado_registros_p10[$i][7]]['hora'])) {
          $array_datos_gestion['p10']['gestion_agente'][$resultado_registros_p10[$i][7]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p10']['gestion_agente'][$resultado_registros_p10[$i][7]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p10']['gestion_agente'][$resultado_registros_p10[$i][7]]['hora'][intval(date('H', strtotime($resultado_registros_p10[$i][8])))]+=1;
        $array_datos_gestion['p10']['gestion_agente'][$resultado_registros_p10[$i][7]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p10[$i][8]))]+=1;
        
        $array_datos_gestiones['p10']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p10[$i][8]))]+=1;
        $array_datos_gestiones['p10']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p10[$i][8]))]=1;
        $array_datos_gestiones['p10']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p10[$i][8]))]=1;

        $array_datos_gestion['p10']['tipo_envio_lista'][]=$resultado_registros_p10[$i][3];
        $array_datos_gestion['p10']['tipo_envio_nombre'][$resultado_registros_p10[$i][3]]=$resultado_registros_p10[$i][9];
        $array_datos_gestion['p10']['tipo_envio'][$resultado_registros_p10[$i][3]]+=1;

        $array_datos_gestion['p10']['estado_lista'][]=$resultado_registros_p10[$i][4];
        $array_datos_gestion['p10']['estado_nombre'][$resultado_registros_p10[$i][4]]=$resultado_registros_p10[$i][10];
        $array_datos_gestion['p10']['estado'][$resultado_registros_p10[$i][4]]+=1;
      }

      $array_datos_gestion['p10']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p10']['gestion_agente']['id']));
      
      $array_datos_total['p10']['total_gestion']=count($resultado_registros_p10);
      $array_datos_total['p10']['total_dias_gestion']=count($array_datos_gestiones['p10']['total_dias']);
      $array_datos_total['p10']['promedio_diario']=count($resultado_registros_p10)/count($array_datos_gestiones['p10']['total_dias']);
      $array_datos_total['p10']['promedio_hora']=count($resultado_registros_p10)/count($array_datos_gestiones['p10']['total_dias_hora']);
      $array_datos_total['p10']['promedio_agente']=count($resultado_registros_p10)/count($array_datos_gestion['p10']['gestion_agente']['id']);
      $array_datos_total['p10']['total_agente']=count($array_datos_gestion['p10']['gestion_agente']['id']);

      $array_datos_gestion['p10']['tipo_envio_lista']=array_values(array_unique($array_datos_gestion['p10']['tipo_envio_lista']));
      $array_datos_gestion['p10']['estado_lista']=array_values(array_unique($array_datos_gestion['p10']['estado_lista']));
    }


  //11. Seguimiento Cargue Documentos
    $consulta_string_p11="SELECT `cescd_id`, `cescd_radicado_entrada`, `cescd_radicado_salida`, `cescd_novedad`, `cescd_observaciones`, `cescd_notificar`, `cescd_registro_usuario`, `cescd_registro_fecha`, NOVEDAD.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_cargue_documentos` LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cerep_seguimiento_cargue_documentos`.`cescd_novedad`=NOVEDAD.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_cargue_documentos`.`cescd_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p11."";
    $consulta_registros_p11 = $enlace_db->prepare($consulta_string_p11);
    if (count($data_consulta)>0) {
        $consulta_registros_p11->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p11->execute();
    $resultado_registros_p11 = $consulta_registros_p11->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p11)) {
      for ($i=0; $i < count($resultado_registros_p11); $i++) { 
        $array_datos_gestion['p11']['gestion_agente']['id'][]=$resultado_registros_p11[$i][6];
        $array_datos_gestion['p11']['gestion_agente'][$resultado_registros_p11[$i][6]]['nombre']=$resultado_registros_p11[$i][9];
        $array_datos_gestion['p11']['gestion_agente'][$resultado_registros_p11[$i][6]]['total']+=1;
        if (!isset($array_datos_gestion['p11']['gestion_agente'][$resultado_registros_p11[$i][6]]['hora'])) {
          $array_datos_gestion['p11']['gestion_agente'][$resultado_registros_p11[$i][6]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p11']['gestion_agente'][$resultado_registros_p11[$i][6]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p11']['gestion_agente'][$resultado_registros_p11[$i][6]]['hora'][intval(date('H', strtotime($resultado_registros_p11[$i][7])))]+=1;
        $array_datos_gestion['p11']['gestion_agente'][$resultado_registros_p11[$i][6]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p11[$i][7]))]+=1;
        
        $array_datos_gestiones['p11']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p11[$i][7]))]+=1;
        $array_datos_gestiones['p11']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p11[$i][7]))]=1;
        $array_datos_gestiones['p11']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p11[$i][7]))]=1;

        $array_datos_gestion['p11']['novedad_lista'][]=$resultado_registros_p11[$i][3];
        $array_datos_gestion['p11']['novedad_nombre'][$resultado_registros_p11[$i][3]]=$resultado_registros_p11[$i][8];
        $array_datos_gestion['p11']['novedad'][$resultado_registros_p11[$i][3]]+=1;
      }

      $array_datos_gestion['p11']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p11']['gestion_agente']['id']));
      
      $array_datos_total['p11']['total_gestion']=count($resultado_registros_p11);
      $array_datos_total['p11']['total_dias_gestion']=count($array_datos_gestiones['p11']['total_dias']);
      $array_datos_total['p11']['promedio_diario']=count($resultado_registros_p11)/count($array_datos_gestiones['p11']['total_dias']);
      $array_datos_total['p11']['promedio_hora']=count($resultado_registros_p11)/count($array_datos_gestiones['p11']['total_dias_hora']);
      $array_datos_total['p11']['promedio_agente']=count($resultado_registros_p11)/count($array_datos_gestion['p11']['gestion_agente']['id']);
      $array_datos_total['p11']['total_agente']=count($array_datos_gestion['p11']['gestion_agente']['id']);

      $array_datos_gestion['p11']['novedad_lista']=array_values(array_unique($array_datos_gestion['p11']['novedad_lista']));
    }

 
  //12. Seguimiento Radicación
    $consulta_string_p12="SELECT `cesr_id`, `cesr_correo_ciudadano`, `cesr_fecha_ingreso_correo`, `cesr_dependencia`, `cesr_senotifica`, `cesr_observaciones`, `cesr_notificar`, `cesr_registro_usuario`, `cesr_registro_fecha`, DEPENDENCIA.`ceco_valor`, NOTIFICA.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_radicacion` LEFT JOIN `gestion_ce_configuracion` AS DEPENDENCIA ON `gestion_cerep_seguimiento_radicacion`.`cesr_dependencia`=DEPENDENCIA.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS NOTIFICA ON `gestion_cerep_seguimiento_radicacion`.`cesr_senotifica`=NOTIFICA.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_radicacion`.`cesr_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p12."";
    $consulta_registros_p12 = $enlace_db->prepare($consulta_string_p12);
    if (count($data_consulta)>0) {
        $consulta_registros_p12->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p12->execute();
    $resultado_registros_p12 = $consulta_registros_p12->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p12)) {
      for ($i=0; $i < count($resultado_registros_p12); $i++) { 
        $array_datos_gestion['p12']['gestion_agente']['id'][]=$resultado_registros_p12[$i][7];
        $array_datos_gestion['p12']['gestion_agente'][$resultado_registros_p12[$i][7]]['nombre']=$resultado_registros_p12[$i][11];
        $array_datos_gestion['p12']['gestion_agente'][$resultado_registros_p12[$i][7]]['total']+=1;
        if (!isset($array_datos_gestion['p12']['gestion_agente'][$resultado_registros_p12[$i][7]]['hora'])) {
          $array_datos_gestion['p12']['gestion_agente'][$resultado_registros_p12[$i][7]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p12']['gestion_agente'][$resultado_registros_p12[$i][7]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p12']['gestion_agente'][$resultado_registros_p12[$i][7]]['hora'][intval(date('H', strtotime($resultado_registros_p12[$i][8])))]+=1;
        $array_datos_gestion['p12']['gestion_agente'][$resultado_registros_p12[$i][7]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p12[$i][8]))]+=1;
        
        $array_datos_gestiones['p12']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p12[$i][8]))]+=1;
        $array_datos_gestiones['p12']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p12[$i][8]))]=1;
        $array_datos_gestiones['p12']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p12[$i][8]))]=1;

        $array_datos_gestion['p12']['dependencia_lista'][]=$resultado_registros_p12[$i][3];
        $array_datos_gestion['p12']['dependencia_nombre'][$resultado_registros_p12[$i][3]]=$resultado_registros_p12[$i][9];
        $array_datos_gestion['p12']['dependencia'][$resultado_registros_p12[$i][3]]+=1;

        $array_datos_gestion['p12']['notifica_lista'][]=$resultado_registros_p12[$i][4];
        $array_datos_gestion['p12']['notifica_nombre'][$resultado_registros_p12[$i][4]]=$resultado_registros_p12[$i][10];
        $array_datos_gestion['p12']['notifica'][$resultado_registros_p12[$i][4]]+=1;
      }

      $array_datos_gestion['p12']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p12']['gestion_agente']['id']));
      
      $array_datos_total['p12']['total_gestion']=count($resultado_registros_p12);
      $array_datos_total['p12']['total_dias_gestion']=count($array_datos_gestiones['p12']['total_dias']);
      $array_datos_total['p12']['promedio_diario']=count($resultado_registros_p12)/count($array_datos_gestiones['p12']['total_dias']);
      $array_datos_total['p12']['promedio_hora']=count($resultado_registros_p12)/count($array_datos_gestiones['p12']['total_dias_hora']);
      $array_datos_total['p12']['promedio_agente']=count($resultado_registros_p12)/count($array_datos_gestion['p12']['gestion_agente']['id']);
      $array_datos_total['p12']['total_agente']=count($array_datos_gestion['p12']['gestion_agente']['id']);

      $array_datos_gestion['p12']['dependencia_lista']=array_values(array_unique($array_datos_gestion['p12']['dependencia_lista']));
      $array_datos_gestion['p12']['notifica_lista']=array_values(array_unique($array_datos_gestion['p12']['notifica_lista']));
    }


  //13. Seguimiento Tipificaciones
    $consulta_string_p13="SELECT `cest_id`, `cest_radicado`, `cest_requiere_traslado`, `cest_oficio_especial`, `cest_observaciones`, `cest_notificar`, `cest_registro_usuario`, `cest_registro_fecha`, TRASLADO.`ceco_valor`, OFICIO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_tipificaciones` LEFT JOIN `gestion_ce_configuracion` AS TRASLADO ON `gestion_cerep_seguimiento_tipificaciones`.`cest_requiere_traslado`=TRASLADO.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS OFICIO ON `gestion_cerep_seguimiento_tipificaciones`.`cest_oficio_especial`=OFICIO.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_tipificaciones`.`cest_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p13."";
    $consulta_registros_p13 = $enlace_db->prepare($consulta_string_p13);
    if (count($data_consulta)>0) {
        $consulta_registros_p13->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p13->execute();
    $resultado_registros_p13 = $consulta_registros_p13->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p13)) {
      for ($i=0; $i < count($resultado_registros_p13); $i++) { 
        $array_datos_gestion['p13']['gestion_agente']['id'][]=$resultado_registros_p13[$i][6];
        $array_datos_gestion['p13']['gestion_agente'][$resultado_registros_p13[$i][6]]['nombre']=$resultado_registros_p13[$i][10];
        $array_datos_gestion['p13']['gestion_agente'][$resultado_registros_p13[$i][6]]['total']+=1;
        if (!isset($array_datos_gestion['p13']['gestion_agente'][$resultado_registros_p13[$i][6]]['hora'])) {
          $array_datos_gestion['p13']['gestion_agente'][$resultado_registros_p13[$i][6]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p13']['gestion_agente'][$resultado_registros_p13[$i][6]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p13']['gestion_agente'][$resultado_registros_p13[$i][6]]['hora'][intval(date('H', strtotime($resultado_registros_p13[$i][7])))]+=1;
        $array_datos_gestion['p13']['gestion_agente'][$resultado_registros_p13[$i][6]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p13[$i][7]))]+=1;
        
        $array_datos_gestiones['p13']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p13[$i][7]))]+=1;
        $array_datos_gestiones['p13']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p13[$i][7]))]=1;
        $array_datos_gestiones['p13']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p13[$i][7]))]=1;

        $array_datos_gestion['p13']['direccionamiento_lista'][]=$resultado_registros_p13[$i][3];
        $array_datos_gestion['p13']['direccionamiento_nombre'][$resultado_registros_p13[$i][3]]=$resultado_registros_p13[$i][9];
        $array_datos_gestion['p13']['direccionamiento'][$resultado_registros_p13[$i][3]]+=1;
      }

      $array_datos_gestion['p13']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p13']['gestion_agente']['id']));
      
      $array_datos_total['p13']['total_gestion']=count($resultado_registros_p13);
      $array_datos_total['p13']['total_dias_gestion']=count($array_datos_gestiones['p13']['total_dias']);
      $array_datos_total['p13']['promedio_diario']=count($resultado_registros_p13)/count($array_datos_gestiones['p13']['total_dias']);
      $array_datos_total['p13']['promedio_hora']=count($resultado_registros_p13)/count($array_datos_gestiones['p13']['total_dias_hora']);
      $array_datos_total['p13']['promedio_agente']=count($resultado_registros_p13)/count($array_datos_gestion['p13']['gestion_agente']['id']);
      $array_datos_total['p13']['total_agente']=count($array_datos_gestion['p13']['gestion_agente']['id']);

      $array_datos_gestion['p13']['direccionamiento_lista']=array_values(array_unique($array_datos_gestion['p13']['direccionamiento_lista']));
    }


  //14. Seguimiento Inspección Tipificación
    $consulta_string_p14="SELECT `cesit_id`, `cesit_radicado`, `cesit_abogado_tipificador`, `cesit_abogado_aprobador`, `cesit_traslado_entidades`, `cesit_traslado_entidades_errado`, `cesit_asignaciones_internas`, `cesit_forma_correcta_peticion`, `cesit_traslado_entidades_errado_senalar`, `cesit_asignacion_errada`, `cesit_asignacion_errada_2`, `cesit_observaciones_asignacion`, `cesit_relaciona_informacion_radicacion`, `cesit_campo_errado`, `cesit_diligencia_datos_solicitante`, `cesit_campo_errado_2`, `cesit_observaciones_diligencia_formulario`, `cesit_notificar`, `cesit_registro_usuario`, `cesit_registro_fecha`, abogado_tipificador.`usu_nombres_apellidos`, abogado_aprobador.`usu_nombres_apellidos`, traslado_entidades.`ceco_valor`, traslado_entidades_errado.`ceco_valor`, asignaciones_internas.`ceco_valor`, forma_correcta_peticion.`ceco_valor`, traslado_entidades_errado_senalar.`ceco_valor`, asignacion_errada.`ceco_valor`, asignacion_errada_2.`ceco_valor`, relaciona_informacion_radicacion.`ceco_valor`, campo_errado.`ceco_valor`, diligencia_datos_solicitante.`ceco_valor`, campo_errado_2.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_inspeccion_tipificacion` LEFT JOIN `administrador_usuario` AS abogado_tipificador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_tipificador`=abogado_tipificador.`usu_id`
     LEFT JOIN `administrador_usuario` AS abogado_aprobador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_aprobador`=abogado_aprobador.`usu_id`
     LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades`=traslado_entidades.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado`=traslado_entidades_errado.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS asignaciones_internas ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignaciones_internas`=asignaciones_internas.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS forma_correcta_peticion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_forma_correcta_peticion`=forma_correcta_peticion.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado_senalar ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado_senalar`=traslado_entidades_errado_senalar.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada`=asignacion_errada.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada_2`=asignacion_errada_2.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS relaciona_informacion_radicacion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_relaciona_informacion_radicacion`=relaciona_informacion_radicacion.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS campo_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado`=campo_errado.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS diligencia_datos_solicitante ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_diligencia_datos_solicitante`=diligencia_datos_solicitante.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS campo_errado_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado_2`=campo_errado_2.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p14."";
    $consulta_registros_p14 = $enlace_db->prepare($consulta_string_p14);
    if (count($data_consulta)>0) {
        $consulta_registros_p14->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p14->execute();
    $resultado_registros_p14 = $consulta_registros_p14->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p14)) {
      for ($i=0; $i < count($resultado_registros_p14); $i++) { 
        $array_datos_gestion['p14']['gestion_agente']['id'][]=$resultado_registros_p14[$i][18];
        $array_datos_gestion['p14']['gestion_agente'][$resultado_registros_p14[$i][18]]['nombre']=$resultado_registros_p14[$i][33];
        $array_datos_gestion['p14']['gestion_agente'][$resultado_registros_p14[$i][18]]['total']+=1;
        if (!isset($array_datos_gestion['p14']['gestion_agente'][$resultado_registros_p14[$i][18]]['hora'])) {
          $array_datos_gestion['p14']['gestion_agente'][$resultado_registros_p14[$i][18]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p14']['gestion_agente'][$resultado_registros_p14[$i][18]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p14']['gestion_agente'][$resultado_registros_p14[$i][18]]['hora'][intval(date('H', strtotime($resultado_registros_p14[$i][19])))]+=1;
        $array_datos_gestion['p14']['gestion_agente'][$resultado_registros_p14[$i][18]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p14[$i][19]))]+=1;
        
        $array_datos_gestiones['p14']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p14[$i][19]))]+=1;
        $array_datos_gestiones['p14']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p14[$i][19]))]=1;
        $array_datos_gestiones['p14']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p14[$i][19]))]=1;

        if ($resultado_registros_p14[$i][5]!='') {//7. Traslado errado entidades
          $array_datos_gestion['p14']['traslado_errado_entidades_lista'][]=$resultado_registros_p14[$i][5];
          $array_datos_gestion['p14']['traslado_errado_entidades_nombre'][$resultado_registros_p14[$i][5]]=$resultado_registros_p14[$i][23];
          $array_datos_gestion['p14']['traslado_errado_entidades'][$resultado_registros_p14[$i][5]]+=1;
        }

        if ($resultado_registros_p14[$i][9]!='') {//8.1. Asignación P.S errada
          $array_datos_gestion['p14']['asignacion_ps_errada_lista'][]=$resultado_registros_p14[$i][9];
          $array_datos_gestion['p14']['asignacion_ps_errada_nombre'][$resultado_registros_p14[$i][9]]=$resultado_registros_p14[$i][27];
          $array_datos_gestion['p14']['asignacion_ps_errada'][$resultado_registros_p14[$i][9]]+=1;
        }

        if ($resultado_registros_p14[$i][7]!='') {//9. Determina de forma correcta el tipo de petición
          $array_datos_gestion['p14']['forma_correcta_peticion_lista'][]=$resultado_registros_p14[$i][7];
          $array_datos_gestion['p14']['forma_correcta_peticion_nombre'][$resultado_registros_p14[$i][7]]=$resultado_registros_p14[$i][25];
          $array_datos_gestion['p14']['forma_correcta_peticion'][$resultado_registros_p14[$i][7]]+=1;
        }

        if ($resultado_registros_p14[$i][12]!='') {//14. Relaciona de manera correcta los datos del campo "información radicación"
          $array_datos_gestion['p14']['relaciona_informacion_lista'][]=$resultado_registros_p14[$i][12];
          $array_datos_gestion['p14']['relaciona_informacion_nombre'][$resultado_registros_p14[$i][12]]=$resultado_registros_p14[$i][29];
          $array_datos_gestion['p14']['relaciona_informacion'][$resultado_registros_p14[$i][12]]+=1;
        }

        if ($resultado_registros_p14[$i][14]!='') {//16. Diligencia de manera correcta los datos del solicitante
          $array_datos_gestion['p14']['diligencia_datos_lista'][]=$resultado_registros_p14[$i][14];
          $array_datos_gestion['p14']['diligencia_datos_nombre'][$resultado_registros_p14[$i][14]]=$resultado_registros_p14[$i][31];
          $array_datos_gestion['p14']['diligencia_datos'][$resultado_registros_p14[$i][14]]+=1;
        }

        if ($resultado_registros_p14[$i][10]!='') {//8.2. Asignación P.S errada
          $array_datos_gestion['p14']['asignacion_ps_errada_2_lista'][]=$resultado_registros_p14[$i][10];
          $array_datos_gestion['p14']['asignacion_ps_errada_2_nombre'][$resultado_registros_p14[$i][10]]=$resultado_registros_p14[$i][28];
          $array_datos_gestion['p14']['asignacion_ps_errada_2'][$resultado_registros_p14[$i][10]]+=1;
        }
      }

      $array_datos_gestion['p14']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p14']['gestion_agente']['id']));
      
      $array_datos_total['p14']['total_gestion']=count($resultado_registros_p14);
      $array_datos_total['p14']['total_dias_gestion']=count($array_datos_gestiones['p14']['total_dias']);
      $array_datos_total['p14']['promedio_diario']=count($resultado_registros_p14)/count($array_datos_gestiones['p14']['total_dias']);
      $array_datos_total['p14']['promedio_hora']=count($resultado_registros_p14)/count($array_datos_gestiones['p14']['total_dias_hora']);
      $array_datos_total['p14']['promedio_agente']=count($resultado_registros_p14)/count($array_datos_gestion['p14']['gestion_agente']['id']);
      $array_datos_total['p14']['total_agente']=count($array_datos_gestion['p14']['gestion_agente']['id']);

      $array_datos_gestion['p14']['traslado_errado_entidades_lista']=array_values(array_unique($array_datos_gestion['p14']['traslado_errado_entidades_lista']));
      $array_datos_gestion['p14']['asignacion_ps_errada_lista']=array_values(array_unique($array_datos_gestion['p14']['asignacion_ps_errada_lista']));
      $array_datos_gestion['p14']['forma_correcta_peticion_lista']=array_values(array_unique($array_datos_gestion['p14']['forma_correcta_peticion_lista']));
      $array_datos_gestion['p14']['relaciona_informacion_lista']=array_values(array_unique($array_datos_gestion['p14']['relaciona_informacion_lista']));
      $array_datos_gestion['p14']['diligencia_datos_lista']=array_values(array_unique($array_datos_gestion['p14']['diligencia_datos_lista']));
      $array_datos_gestion['p14']['asignacion_ps_errada_2_lista']=array_values(array_unique($array_datos_gestion['p14']['asignacion_ps_errada_2_lista']));
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
                    <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                      <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 mb-2" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                        <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i> Reporte Consolidado
                      </button>
                    <?php endif; ?>
                  </div>
                  <div class="btn-group-vertical nav nav-tabs" role="group" aria-label="Button group with nested dropdown">
                    <button type="button" class="btn btn-outline-dark px-1 py-1 active" style="text-align: left !important;" id="p1-tab" data-bs-toggle="tab" href="#p1" role="tab" aria-controls="p1" aria-selected="true">
                      1. Proyección Consolidación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p2-tab" data-bs-toggle="tab" href="#p2" role="tab" aria-controls="p2" aria-selected="true">
                      2. Aprobación Firma FA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p3-tab" data-bs-toggle="tab" href="#p3" role="tab" aria-controls="p2" aria-selected="true">
                      3. Firma FA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p4-tab" data-bs-toggle="tab" href="#p4" role="tab" aria-controls="p2" aria-selected="true">
                      4. Inspección Proyección
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p5-tab" data-bs-toggle="tab" href="#p5" role="tab" aria-controls="p2" aria-selected="true">
                      5. Proyección FA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p6-tab" data-bs-toggle="tab" href="#p6" role="tab" aria-controls="p2" aria-selected="true">
                      6. Aprobación Firma
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p7-tab" data-bs-toggle="tab" href="#p7" role="tab" aria-controls="p2" aria-selected="true">
                      7. Firma Traslados
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p8-tab" data-bs-toggle="tab" href="#p8" role="tab" aria-controls="p2" aria-selected="true">
                      8. Proyectores
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p9-tab" data-bs-toggle="tab" href="#p9" role="tab" aria-controls="p2" aria-selected="true">
                      9. Seguimiento Lanzamientos TR
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p10-tab" data-bs-toggle="tab" href="#p10" role="tab" aria-controls="p2" aria-selected="true">
                      10. Seguimiento Envíos Web
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p11-tab" data-bs-toggle="tab" href="#p11" role="tab" aria-controls="p2" aria-selected="true">
                      11. Seguimiento Cargue Documentos
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p12-tab" data-bs-toggle="tab" href="#p12" role="tab" aria-controls="p2" aria-selected="true">
                      12. Seguimiento Radicación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p13-tab" data-bs-toggle="tab" href="#p13" role="tab" aria-controls="p2" aria-selected="true">
                      13. Seguimiento Tipificaciones
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p14-tab" data-bs-toggle="tab" href="#p14" role="tab" aria-controls="p2" aria-selected="true">
                      14. Seguimiento Inspección Tipificación
                    </button>
                  </div>
                </div>
                <div class="col-md-9 ps-0">
                  <div class="tab-content tab-content-basic pt-0 px-1">
                    <!-- p1 -->
                    <?php include('reparto_estadisticas_p1.php'); ?>

                    <!-- p2 -->
                    <?php include('reparto_estadisticas_p2.php'); ?>

                    <!-- p3 -->
                    <?php include('reparto_estadisticas_p3.php'); ?>

                    <!-- p4 -->
                    <?php include('reparto_estadisticas_p4.php'); ?>

                    <!-- p5 -->
                    <?php include('reparto_estadisticas_p5.php'); ?>

                    <!-- p6 -->
                    <?php include('reparto_estadisticas_p6.php'); ?>

                    <!-- p7 -->
                    <?php include('reparto_estadisticas_p7.php'); ?>

                    <!-- p8 -->
                    <?php include('reparto_estadisticas_p8.php'); ?>

                    <!-- p9 -->
                    <?php include('reparto_estadisticas_p9.php'); ?>

                    <!-- p10 -->
                    <?php include('reparto_estadisticas_p10.php'); ?>

                    <!-- p11 -->
                    <?php include('reparto_estadisticas_p11.php'); ?>

                    <!-- p12 -->
                    <?php include('reparto_estadisticas_p12.php'); ?>

                    <!-- p13 -->
                    <?php include('reparto_estadisticas_p13.php'); ?>

                    <!-- p14 -->
                    <?php include('reparto_estadisticas_p14.php'); ?>
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
        <!-- modal reportes -->
        <?php require_once('consolidado_reporte.php'); ?>
        <!-- modal -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>