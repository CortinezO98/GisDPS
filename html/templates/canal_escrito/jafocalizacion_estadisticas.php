<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Jóvenes en Acción y Focalización | Estadísticas";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  if(isset($_POST["filtro"])){
    $fecha_inicio=validar_input($_POST['fecha_inicio']);
    $fecha_fin=validar_input($_POST['fecha_fin']);
    
    $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_inicio']=$fecha_inicio;
    $_SESSION[APP_SESSION.'_session_ce_estadisticas']['fecha_fin']=$fecha_fin;

    header("Location: jafocalizacion_estadisticas");
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
    $filtro_fechas_p1=" AND `cejpp_registro_fecha`>=? AND `cejpp_registro_fecha`<=?";
    $filtro_fechas_p2=" AND `cejrp_registro_fecha`>=? AND `cejrp_registro_fecha`<=?";
    $filtro_fechas_p3=" AND `cejrr_registro_fecha`>=? AND `cejrr_registro_fecha`<=?";
    $filtro_fechas_p4=" AND `cejgc_registro_fecha`>=? AND `cejgc_registro_fecha`<=?";
    $filtro_fechas_p5=" AND `cejgn_registro_fecha`>=? AND `cejgn_registro_fecha`<=?";
    $filtro_fechas_p6=" AND `cejgp_registro_fecha`>=? AND `cejgp_registro_fecha`<=?";
    $filtro_fechas_p7=" AND `cejga_registro_fecha`>=? AND `cejga_registro_fecha`<=?";
    $filtro_fechas_p8=" AND `cejef_registro_fecha`>=? AND `cejef_registro_fecha`<=?";
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
    $array_datos_gestion['p1']['agente_lista']=array();
    $array_datos_gestion['p1']['novedad_lista']=array();
    $array_datos_gestion['p2']['gestion_agente']['id']=array();
    $array_datos_gestion['p2']['proyector_lista']=array();
    $array_datos_gestion['p2']['estado_lista']=array();
    $array_datos_gestion['p3']['gestion_agente']['id']=array();
    $array_datos_gestion['p3']['modalidad_envio_lista']=array();
    $array_datos_gestion['p4']['gestion_agente']['id']=array();
    $array_datos_gestion['p4']['agente_lista']=array();
    $array_datos_gestion['p4']['estado_lista']=array();
    $array_datos_gestion['p4']['proyector_lista']=array();
    $array_datos_gestion['p5']['gestion_agente']['id']=array();
    $array_datos_gestion['p5']['agente_lista']=array();
    $array_datos_gestion['p5']['novedad_lista']=array();
    $array_datos_gestion['p6']['gestion_agente']['id']=array();
    $array_datos_gestion['p7']['gestion_agente']['id']=array();
    $array_datos_gestion['p7']['agente_lista']=array();
    $array_datos_gestion['p7']['estado_lista']=array();
    $array_datos_gestion['p7']['proyector_lista']=array();
    $array_datos_gestion['p8']['gestion_agente']['id']=array();
    $array_mapa_p6=array();
    $array_mapa_p8=array();


  //1. Proyección de Peticiones Vivienda
    $consulta_string_p1="SELECT `cejpp_id`, `cejpp_radicado_entrada`, `cejpp_proyector`, `cejpp_novedad_radicado`, `cejpp_formato`, `cejpp_identificacion_peticionario`, `cejpp_nombre_peticionario`, `cejpp_correo`, `cejpp_observaciones`, `cejpp_notificar`, `cejpp_registro_usuario`, `cejpp_registro_fecha`, NOVEDAD.`ceco_valor`, FORMATO.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_proyeccion_peticiones` LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_novedad_radicado`=NOVEDAD.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS FORMATO ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_formato`=FORMATO.`ceco_id`
     LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_proyector`=PROYECTOR.`usu_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p1."";
    $consulta_registros_p1 = $enlace_db->prepare($consulta_string_p1);
    if (count($data_consulta)>0) {
        $consulta_registros_p1->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p1->execute();
    $resultado_registros_p1 = $consulta_registros_p1->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p1)) {
      for ($i=0; $i < count($resultado_registros_p1); $i++) { 
        $array_datos_gestion['p1']['gestion_agente']['id'][]=$resultado_registros_p1[$i][10];
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][10]]['nombre']=$resultado_registros_p1[$i][15];
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][10]]['total']+=1;
        if (!isset($array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][10]]['hora'])) {
          $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][10]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][10]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][10]]['hora'][intval(date('H', strtotime($resultado_registros_p1[$i][11])))]+=1;
        $array_datos_gestion['p1']['gestion_agente'][$resultado_registros_p1[$i][10]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p1[$i][11]))]+=1;
        
        $array_datos_gestiones['p1']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p1[$i][11]))]+=1;
        $array_datos_gestiones['p1']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p1[$i][11]))]=1;
        $array_datos_gestiones['p1']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p1[$i][11]))]=1;

        $array_datos_gestion['p1']['agente_lista'][]=$resultado_registros_p1[$i][10];
        $array_datos_gestion['p1']['agente_nombre'][$resultado_registros_p1[$i][10]]=$resultado_registros_p1[$i][15];
        $array_datos_gestion['p1']['agente'][$resultado_registros_p1[$i][10]][$resultado_registros_p1[$i][3]]+=1;

        $array_datos_gestion['p1']['novedad_lista'][]=$resultado_registros_p1[$i][3];
        $array_datos_gestion['p1']['novedad_nombre'][$resultado_registros_p1[$i][3]]=$resultado_registros_p1[$i][12];
      }

      $array_datos_gestion['p1']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p1']['gestion_agente']['id']));
      
      $array_datos_total['p1']['total_gestion']=count($resultado_registros_p1);
      $array_datos_total['p1']['total_dias_gestion']=count($array_datos_gestiones['p1']['total_dias']);
      $array_datos_total['p1']['promedio_diario']=count($resultado_registros_p1)/count($array_datos_gestiones['p1']['total_dias']);
      $array_datos_total['p1']['promedio_hora']=count($resultado_registros_p1)/count($array_datos_gestiones['p1']['total_dias_hora']);
      $array_datos_total['p1']['promedio_agente']=count($resultado_registros_p1)/count($array_datos_gestion['p1']['gestion_agente']['id']);
      $array_datos_total['p1']['total_agente']=count($array_datos_gestion['p1']['gestion_agente']['id']);

      $array_datos_gestion['p1']['agente_lista']=array_values(array_unique($array_datos_gestion['p1']['agente_lista']));
      $array_datos_gestion['p1']['novedad_lista']=array_values(array_unique($array_datos_gestion['p1']['novedad_lista']));
    }


  //2. Revisión de Peticiones Vivienda 
    $consulta_string_p2="SELECT `cejrp_id`, `cejrp_radicado_entrada`, `cejrp_realiza_traslado`, `cejrp_aprobador`, `cejrp_proyector`, `cejrp_estado`, `cejrp_error_digitalizacion`, `cejrp_caso_particular`, `cejrp_observaciones`, `cejrp_notificar`, `cejrp_registro_usuario`, `cejrp_registro_fecha`, REALIZATRASLADO.`ceco_valor`, ESTADO.`ceco_valor`, ERRORDIGITA.`ceco_valor`, CASOPARTICULAR.`ceco_valor`, TU.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos`, PROYECTOR.`usu_nombres_apellidos` FROM `gestion_cejafo_revision_peticiones` 
      LEFT JOIN `gestion_ce_configuracion` AS REALIZATRASLADO ON `gestion_cejafo_revision_peticiones`.`cejrp_realiza_traslado`=REALIZATRASLADO.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cejafo_revision_peticiones`.`cejrp_estado`=ESTADO.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS ERRORDIGITA ON `gestion_cejafo_revision_peticiones`.`cejrp_error_digitalizacion`=ERRORDIGITA.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS CASOPARTICULAR ON `gestion_cejafo_revision_peticiones`.`cejrp_caso_particular`=CASOPARTICULAR.`ceco_id`
      LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cejafo_revision_peticiones`.`cejrp_aprobador`=APROBADOR.`usu_id`
      LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_revision_peticiones`.`cejrp_proyector`=PROYECTOR.`usu_id`
      LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_revision_peticiones`.`cejrp_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p2."";
    $consulta_registros_p2 = $enlace_db->prepare($consulta_string_p2);
    if (count($data_consulta)>0) {
        $consulta_registros_p2->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p2->execute();
    $resultado_registros_p2 = $consulta_registros_p2->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p2)) {
      for ($i=0; $i < count($resultado_registros_p2); $i++) { 
        $array_datos_gestion['p2']['gestion_agente']['id'][]=$resultado_registros_p2[$i][10];
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][10]]['nombre']=$resultado_registros_p2[$i][16];
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][10]]['total']+=1;
        if (!isset($array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][10]]['hora'])) {
          $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][10]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][10]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][10]]['hora'][intval(date('H', strtotime($resultado_registros_p2[$i][11])))]+=1;
        $array_datos_gestion['p2']['gestion_agente'][$resultado_registros_p2[$i][10]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p2[$i][11]))]+=1;
        
        $array_datos_gestiones['p2']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p2[$i][11]))]+=1;
        $array_datos_gestiones['p2']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p2[$i][11]))]=1;
        $array_datos_gestiones['p2']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p2[$i][11]))]=1;

        $array_datos_gestion['p2']['proyector_lista'][]=$resultado_registros_p2[$i][4];
        $array_datos_gestion['p2']['proyector_nombre'][$resultado_registros_p2[$i][4]]=$resultado_registros_p2[$i][18];
        $array_datos_gestion['p2']['proyector'][$resultado_registros_p2[$i][4]][$resultado_registros_p2[$i][5]]+=1;

        $array_datos_gestion['p2']['estado_lista'][]=$resultado_registros_p2[$i][5];
        $array_datos_gestion['p2']['estado_nombre'][$resultado_registros_p2[$i][5]]=$resultado_registros_p2[$i][13];
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


  //3. Formato de Relación RAE JeA
    $consulta_string_p3="SELECT `cejrr_id`, `cejrr_radicado_salida`, `cejrr_radicado_entrada`, `cejrr_destinatario`, `cejrr_direccion`, `cejrr_municipio`, `cejrr_modalidad_envio`, `cejrr_srjv`, `cejrr_proyector`, `cejrr_aprobador`, `cejrr_firma`, `cejrr_cedula_firmante`, `cejrr_fecha_gestion_rae`, `cejrr_fecha_envio`, `cejrr_qq`, `cejrr_observaciones`, `cejrr_notificar`, `cejrr_registro_usuario`, `cejrr_registro_fecha`, MODALIDADENVIO.`ceco_valor`, SRJV.`ceco_valor`, FIRMA.`ceco_valor`, TU.`usu_nombres_apellidos`, TCIU.`ciu_departamento`, TCIU.`ciu_municipio`, PROYECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos` FROM `gestion_cejafo_relacion_rae` LEFT JOIN `gestion_ce_configuracion` AS MODALIDADENVIO ON `gestion_cejafo_relacion_rae`.`cejrr_modalidad_envio`=MODALIDADENVIO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS SRJV ON `gestion_cejafo_relacion_rae`.`cejrr_srjv`=SRJV.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS FIRMA ON `gestion_cejafo_relacion_rae`.`cejrr_firma`=FIRMA.`ceco_id`
     LEFT JOIN `administrador_ciudades` AS TCIU ON `gestion_cejafo_relacion_rae`.`cejrr_municipio`=TCIU.`ciu_codigo`
     LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_relacion_rae`.`cejrr_proyector`=PROYECTOR.`usu_id`
     LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cejafo_relacion_rae`.`cejrr_aprobador`=APROBADOR.`usu_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_relacion_rae`.`cejrr_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p3."";
    $consulta_registros_p3 = $enlace_db->prepare($consulta_string_p3);
    if (count($data_consulta)>0) {
        $consulta_registros_p3->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p3->execute();
    $resultado_registros_p3 = $consulta_registros_p3->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p3)) {
      for ($i=0; $i < count($resultado_registros_p3); $i++) { 
        $array_datos_gestion['p3']['gestion_agente']['id'][]=$resultado_registros_p3[$i][17];
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][17]]['nombre']=$resultado_registros_p3[$i][22];
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][17]]['total']+=1;
        if (!isset($array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][17]]['hora'])) {
          $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][17]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][17]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][17]]['hora'][intval(date('H', strtotime($resultado_registros_p3[$i][18])))]+=1;
        $array_datos_gestion['p3']['gestion_agente'][$resultado_registros_p3[$i][17]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p3[$i][18]))]+=1;
        
        $array_datos_gestiones['p3']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p3[$i][18]))]+=1;
        $array_datos_gestiones['p3']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p3[$i][18]))]=1;
        $array_datos_gestiones['p3']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p3[$i][18]))]=1;

        $array_datos_gestion['p3']['modalidad_envio_lista'][]=$resultado_registros_p3[$i][6];
        $array_datos_gestion['p3']['modalidad_envio_nombre'][$resultado_registros_p3[$i][6]]=$resultado_registros_p3[$i][19];
        $array_datos_gestion['p3']['modalidad_envio'][$resultado_registros_p3[$i][6]]+=1;
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


  //4. Formato de Gestión de Correos
    $consulta_string_p4="SELECT `cejgc_id`, `cejgc_fecha_recibido`, `cejgc_gestion`, `cejgc_documento`, `cejgc_tipo_documento`, `cejgc_nombre_completo`, `cejgc_codigo_beneficiario`, `cejgc_email`, `cejgc_celular`, `cejgc_departamento`, `cejgc_municipio`, `cejgc_categoria`, `cejgc_gestion_2`, `cejgc_tipificacion`, `cejgc_carga_di`, `cejgc_carga_soporte_bachiller`, `cejgc_observaciones`, `cejgc_notificar`, `cejgc_registro_usuario`, `cejgc_registro_fecha`, GESTION.`ceco_valor`, TIPODOCUMENTO.`ceco_valor`, TC.`ciu_departamento`, TC.`ciu_municipio`, CATEGORIA.`ceco_valor`, GESTION2.`ceco_valor`, TIPIFICACION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_gestion_correo` LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cejafo_gestion_correo`.`cejgc_gestion`=GESTION.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS TIPODOCUMENTO ON `gestion_cejafo_gestion_correo`.`cejgc_tipo_documento`=TIPODOCUMENTO.`ceco_id`
       LEFT JOIN `administrador_ciudades` AS TC ON `gestion_cejafo_gestion_correo`.`cejgc_municipio`=TC.`ciu_codigo`
       LEFT JOIN `gestion_ce_configuracion` AS CATEGORIA ON `gestion_cejafo_gestion_correo`.`cejgc_categoria`=CATEGORIA.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS GESTION2 ON `gestion_cejafo_gestion_correo`.`cejgc_gestion_2`=GESTION2.`ceco_id`
       LEFT JOIN `gestion_ce_configuracion` AS TIPIFICACION ON `gestion_cejafo_gestion_correo`.`cejgc_tipificacion`=TIPIFICACION.`ceco_id`
       LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_correo`.`cejgc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p4."";
    $consulta_registros_p4 = $enlace_db->prepare($consulta_string_p4);
    if (count($data_consulta)>0) {
        $consulta_registros_p4->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p4->execute();
    $resultado_registros_p4 = $consulta_registros_p4->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p4)) {
      for ($i=0; $i < count($resultado_registros_p4); $i++) { 
        $array_datos_gestion['p4']['gestion_agente']['id'][]=$resultado_registros_p4[$i][18];
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][18]]['nombre']=$resultado_registros_p4[$i][27];
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][18]]['total']+=1;
        if (!isset($array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][18]]['hora'])) {
          $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][18]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][18]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][18]]['hora'][intval(date('H', strtotime($resultado_registros_p4[$i][19])))]+=1;
        $array_datos_gestion['p4']['gestion_agente'][$resultado_registros_p4[$i][18]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p4[$i][19]))]+=1;
        
        $array_datos_gestiones['p4']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p4[$i][19]))]+=1;
        $array_datos_gestiones['p4']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p4[$i][19]))]=1;
        $array_datos_gestiones['p4']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p4[$i][19]))]=1;

        $array_datos_gestion['p4']['agente_lista'][]=$resultado_registros_p4[$i][18];
        $array_datos_gestion['p4']['agente_nombre'][$resultado_registros_p4[$i][18]]=$resultado_registros_p4[$i][27];
        $array_datos_gestion['p4']['agente'][$resultado_registros_p4[$i][18]][$resultado_registros_p4[$i][2]]+=1;

        $array_datos_gestion['p4']['estado_lista'][]=$resultado_registros_p4[$i][2];
        $array_datos_gestion['p4']['estado_nombre'][$resultado_registros_p4[$i][2]]=$resultado_registros_p4[$i][20];

        $array_datos_gestion['p4']['proyector_lista'][]=$resultado_registros_p4[$i][18];
        $array_datos_gestion['p4']['proyector_nombre'][$resultado_registros_p4[$i][18]]=$resultado_registros_p4[$i][27];
        $array_datos_gestion['p4']['proyector_estado'][$resultado_registros_p4[$i][18]][$resultado_registros_p4[$i][2]]+=1;
        $array_datos_gestion['p4']['proyector_estado_total'][$resultado_registros_p4[$i][18]]+=1;
      }

      $array_datos_gestion['p4']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p4']['gestion_agente']['id']));
      
      $array_datos_total['p4']['total_gestion']=count($resultado_registros_p4);
      $array_datos_total['p4']['total_dias_gestion']=count($array_datos_gestiones['p4']['total_dias']);
      $array_datos_total['p4']['promedio_diario']=count($resultado_registros_p4)/count($array_datos_gestiones['p4']['total_dias']);
      $array_datos_total['p4']['promedio_hora']=count($resultado_registros_p4)/count($array_datos_gestiones['p4']['total_dias_hora']);
      $array_datos_total['p4']['promedio_agente']=count($resultado_registros_p4)/count($array_datos_gestion['p4']['gestion_agente']['id']);
      $array_datos_total['p4']['total_agente']=count($array_datos_gestion['p4']['gestion_agente']['id']);

      $array_datos_gestion['p4']['agente_lista']=array_values(array_unique($array_datos_gestion['p4']['agente_lista']));
      $array_datos_gestion['p4']['estado_lista']=array_values(array_unique($array_datos_gestion['p4']['estado_lista']));
      $array_datos_gestion['p4']['proyector_lista']=array_values(array_unique($array_datos_gestion['p4']['proyector_lista']));
    }


  //5. Formato Gestión de Novedades JeA
    $consulta_string_p5="SELECT `cejgn_id`, `cejgn_id_novedad`, `cejgn_id_persona`, `cejgn_fecha_gestion`, `cejgn_estado`, `cejgn_tipo_rechazo`, `cejgn_observacion_rechazo`, `cejgn_correccion_datos_sija`, `cejgn_codigo_novedad`, `cejgn_observaciones`, `cejgn_notificar`, `cejgn_registro_usuario`, `cejgn_registro_fecha`, ESTADO.`ceco_valor`, TIPORECHAZO.`ceco_valor`, DATOSSIJA.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_gestion_novedades` 
     LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cejafo_gestion_novedades`.`cejgn_estado`=ESTADO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS TIPORECHAZO ON `gestion_cejafo_gestion_novedades`.`cejgn_tipo_rechazo`=TIPORECHAZO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS DATOSSIJA ON `gestion_cejafo_gestion_novedades`.`cejgn_correccion_datos_sija`=DATOSSIJA.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_novedades`.`cejgn_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p5."";
    $consulta_registros_p5 = $enlace_db->prepare($consulta_string_p5);
    if (count($data_consulta)>0) {
        $consulta_registros_p5->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p5->execute();
    $resultado_registros_p5 = $consulta_registros_p5->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p5)) {
      for ($i=0; $i < count($resultado_registros_p5); $i++) { 
        $array_datos_gestion['p5']['gestion_agente']['id'][]=$resultado_registros_p5[$i][11];
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][11]]['nombre']=$resultado_registros_p5[$i][16];
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][11]]['total']+=1;
        if (!isset($array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][11]]['hora'])) {
          $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][11]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][11]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][11]]['hora'][intval(date('H', strtotime($resultado_registros_p5[$i][12])))]+=1;
        $array_datos_gestion['p5']['gestion_agente'][$resultado_registros_p5[$i][11]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p5[$i][12]))]+=1;
        
        $array_datos_gestiones['p5']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p5[$i][12]))]+=1;
        $array_datos_gestiones['p5']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p5[$i][12]))]=1;
        $array_datos_gestiones['p5']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p5[$i][12]))]=1;

        $array_datos_gestion['p5']['agente_lista'][]=$resultado_registros_p5[$i][11];
        $array_datos_gestion['p5']['agente_nombre'][$resultado_registros_p5[$i][11]]=$resultado_registros_p5[$i][16];
        $array_datos_gestion['p5']['agente'][$resultado_registros_p5[$i][11]][$resultado_registros_p5[$i][4]]+=1;

        $array_datos_gestion['p5']['novedad_lista'][]=$resultado_registros_p5[$i][4];
        $array_datos_gestion['p5']['novedad_nombre'][$resultado_registros_p5[$i][4]]=$resultado_registros_p5[$i][13];
      }

      $array_datos_gestion['p5']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p5']['gestion_agente']['id']));
      
      $array_datos_total['p5']['total_gestion']=count($resultado_registros_p5);
      $array_datos_total['p5']['total_dias_gestion']=count($array_datos_gestiones['p5']['total_dias']);
      $array_datos_total['p5']['promedio_diario']=count($resultado_registros_p5)/count($array_datos_gestiones['p5']['total_dias']);
      $array_datos_total['p5']['promedio_hora']=count($resultado_registros_p5)/count($array_datos_gestiones['p5']['total_dias_hora']);
      $array_datos_total['p5']['promedio_agente']=count($resultado_registros_p5)/count($array_datos_gestion['p5']['gestion_agente']['id']);
      $array_datos_total['p5']['total_agente']=count($array_datos_gestion['p5']['gestion_agente']['id']);

      $array_datos_gestion['p5']['agente_lista']=array_values(array_unique($array_datos_gestion['p5']['agente_lista']));
      $array_datos_gestion['p5']['novedad_lista']=array_values(array_unique($array_datos_gestion['p5']['novedad_lista']));
    }


  //6. Formato de Gestión de Peticiones JeA
    $consulta_string_p6="SELECT `cejgp_id`, `cejgp_radicado`, `cejgp_proyector`, `cejgp_aprobador`, `cejgp_peticionario_identificacion`, `cejgp_peticionario_nombres`, `cejgp_correo_direccion`, `cejgp_municipio`, `cejgp_solicitud`, `cejgp_no_registra_sija`, `cejgp_tipo_documento`, `cejgp_fecha_nacimiento_solicitante`, `cejgp_novedad`, `cejgp_no_radicado`, `cejgp_novedad_adicional`, `cejgp_codigo_beneficiario`, `cejgp_gestion_actualizacion`, `cejgp_institucion_estudia`, `cejgp_nivel_formacion`, `cejgp_convenio`, `cejgp_observacion_actualizacion`, `cejgp_codigo_beneficiario_caso_especial`, `cejgp_municipio_reporte`, `cejgp_observacion_caso_especial`, `cejgp_observaciones`, `cejgp_notificar`, `cejgp_registro_usuario`, `cejgp_registro_fecha`, SOLICITUD.`ceco_valor`, SIJA.`ceco_valor`, TIPODOCUMENTO.`ceco_valor`, NOVEDAD.`ceco_valor`, NOVEDADADD.`ceco_valor`, GESTIONACTUALIZACION.`ceco_valor`, INSTITUCIONESTUDIA.`ceco_valor`, NIVELFORMACION.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos`, TC1.`ciu_departamento`, TC1.`ciu_municipio`, TC2.`ciu_departamento`, TC2.`ciu_municipio` FROM `gestion_cejafo_gestion_peticiones` 
      LEFT JOIN `gestion_ce_configuracion` AS SOLICITUD ON `gestion_cejafo_gestion_peticiones`.`cejgp_solicitud`=SOLICITUD.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS SIJA ON `gestion_cejafo_gestion_peticiones`.`cejgp_no_registra_sija`=SIJA.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS TIPODOCUMENTO ON `gestion_cejafo_gestion_peticiones`.`cejgp_tipo_documento`=TIPODOCUMENTO.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cejafo_gestion_peticiones`.`cejgp_novedad`=NOVEDAD.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS NOVEDADADD ON `gestion_cejafo_gestion_peticiones`.`cejgp_novedad_adicional`=NOVEDADADD.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS GESTIONACTUALIZACION ON `gestion_cejafo_gestion_peticiones`.`cejgp_gestion_actualizacion`=GESTIONACTUALIZACION.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS INSTITUCIONESTUDIA ON `gestion_cejafo_gestion_peticiones`.`cejgp_institucion_estudia`=INSTITUCIONESTUDIA.`ceco_id`
      LEFT JOIN `gestion_ce_configuracion` AS NIVELFORMACION ON `gestion_cejafo_gestion_peticiones`.`cejgp_nivel_formacion`=NIVELFORMACION.`ceco_id`
      LEFT JOIN `administrador_ciudades` AS TC1 ON `gestion_cejafo_gestion_peticiones`.`cejgp_municipio`=TC1.`ciu_codigo`
      LEFT JOIN `administrador_ciudades` AS TC2 ON `gestion_cejafo_gestion_peticiones`.`cejgp_municipio_reporte`=TC2.`ciu_codigo`
      LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_gestion_peticiones`.`cejgp_proyector`=PROYECTOR.`usu_id`
      LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cejafo_gestion_peticiones`.`cejgp_aprobador`=APROBADOR.`usu_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_peticiones`.`cejgp_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p6."";
    $consulta_registros_p6 = $enlace_db->prepare($consulta_string_p6);
    if (count($data_consulta)>0) {
        $consulta_registros_p6->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p6->execute();
    $resultado_registros_p6 = $consulta_registros_p6->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p6)) {
      for ($i=0; $i < count($resultado_registros_p6); $i++) { 
        $array_datos_gestion['p6']['gestion_agente']['id'][]=$resultado_registros_p6[$i][26];
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][26]]['nombre']=$resultado_registros_p6[$i][38];
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][26]]['total']+=1;
        if (!isset($array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][26]]['hora'])) {
          $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][26]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][26]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][26]]['hora'][intval(date('H', strtotime($resultado_registros_p6[$i][27])))]+=1;
        $array_datos_gestion['p6']['gestion_agente'][$resultado_registros_p6[$i][26]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p6[$i][27]))]+=1;
        
        $array_datos_gestiones['p6']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p6[$i][27]))]+=1;
        $array_datos_gestiones['p6']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p6[$i][27]))]=1;
        $array_datos_gestiones['p6']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p6[$i][27]))]=1;

        //Mapa colombia
          if ($resultado_registros_p6[$i][39]=='ARCHIPIÉLAGO DE SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA') {
            $array_mapa_p6['co-sa']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='CAUCA') {
            $array_mapa_p6['co-ca']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='NARIÑO') {
            $array_mapa_p6['co-na']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='CHOCÓ') {
            $array_mapa_p6['co-ch']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='TOLIMA') {
            $array_mapa_p6['co-to']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='CAQUETÁ') {
            $array_mapa_p6['co-cq']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='HUILA') {
            $array_mapa_p6['co-hu']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='PUTUMAYO') {
            $array_mapa_p6['co-pu']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='AMAZONAS') {
            $array_mapa_p6['co-am']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='BOLÍVAR') {
            $array_mapa_p6['co-bl']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='VALLE DEL CAUCA') {
            $array_mapa_p6['co-vc']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='SUCRE') {
            $array_mapa_p6['co-su']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='ATLÁNTICO') {
            $array_mapa_p6['co-at']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='CESAR') {
            $array_mapa_p6['co-ce']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='LA GUAJIRA') {
            $array_mapa_p6['co-lg']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='MAGDALENA') {
            $array_mapa_p6['co-ma']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='ARAUCA') {
            $array_mapa_p6['co-ar']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='NORTE DE SANTANDER') {
            $array_mapa_p6['co-ns']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='CASANARE') {
            $array_mapa_p6['co-cs']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='GUAVIARE') {
            $array_mapa_p6['co-gv']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='META') {
            $array_mapa_p6['co-me']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='VAUPÉS') {
            $array_mapa_p6['co-vp']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='VICHADA') {
            $array_mapa_p6['co-vd']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='ANTIOQUIA') {
            $array_mapa_p6['co-an']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='CÓRDOBA') {
            $array_mapa_p6['co-co']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='BOYACÁ') {
            $array_mapa_p6['co-by']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='SANTANDER') {
            $array_mapa_p6['co-st']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='CALDAS') {
            $array_mapa_p6['co-cl']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='CUNDINAMARCA') {
            $array_mapa_p6['co-cu']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='BOGOTÁ, D.C.') {
            $array_mapa_p6['co-1136']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='RISARALDA') {
            $array_mapa_p6['co-ri']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='QUINDÍO') {
            $array_mapa_p6['co-qd']+=1;
          } elseif ($resultado_registros_p6[$i][39]=='GUAINÍA') {
            $array_mapa_p6['co-gn']+=1;
          }
      }

      $array_datos_gestion['p6']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p6']['gestion_agente']['id']));
      
      $array_datos_total['p6']['total_gestion']=count($resultado_registros_p6);
      $array_datos_total['p6']['total_dias_gestion']=count($array_datos_gestiones['p6']['total_dias']);
      $array_datos_total['p6']['promedio_diario']=count($resultado_registros_p6)/count($array_datos_gestiones['p6']['total_dias']);
      $array_datos_total['p6']['promedio_hora']=count($resultado_registros_p6)/count($array_datos_gestiones['p6']['total_dias_hora']);
      $array_datos_total['p6']['promedio_agente']=count($resultado_registros_p6)/count($array_datos_gestion['p6']['gestion_agente']['id']);
      $array_datos_total['p6']['total_agente']=count($array_datos_gestion['p6']['gestion_agente']['id']);
    }


  //7. Formato Gestión de Aprobación JeA
    $consulta_string_p7="SELECT `cejga_id`, `cejga_radicado_entrada`, `cejga_proyector`, `cejga_revisor`, `cejga_cedula_aprobador`, `cejga_gestion`, `cejga_oportunidad_mejora`, `cejga_comentario_delta`, `cejga_observaciones`, `cejga_notificar`, `cejga_registro_usuario`, `cejga_registro_fecha`, GESTION.`ceco_valor`, OPORTUNIDAD.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, REVISOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_gestion_aprobacion` 
     LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cejafo_gestion_aprobacion`.`cejga_gestion`=GESTION.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS OPORTUNIDAD ON `gestion_cejafo_gestion_aprobacion`.`cejga_oportunidad_mejora`=OPORTUNIDAD.`ceco_id`
     LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_gestion_aprobacion`.`cejga_proyector`=PROYECTOR.`usu_id`
     LEFT JOIN `administrador_usuario` AS REVISOR ON `gestion_cejafo_gestion_aprobacion`.`cejga_revisor`=REVISOR.`usu_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_aprobacion`.`cejga_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p7."";
    $consulta_registros_p7 = $enlace_db->prepare($consulta_string_p7);
    if (count($data_consulta)>0) {
        $consulta_registros_p7->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p7->execute();
    $resultado_registros_p7 = $consulta_registros_p7->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p7)) {
      for ($i=0; $i < count($resultado_registros_p7); $i++) { 
        $array_datos_gestion['p7']['gestion_agente']['id'][]=$resultado_registros_p7[$i][10];
        $array_datos_gestion['p7']['gestion_agente'][$resultado_registros_p7[$i][10]]['nombre']=$resultado_registros_p7[$i][16];
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

        $array_datos_gestion['p7']['proyector_lista'][]=$resultado_registros_p7[$i][2];
        $array_datos_gestion['p7']['proyector_nombre'][$resultado_registros_p7[$i][2]]=$resultado_registros_p7[$i][14];
        $array_datos_gestion['p7']['proyector'][$resultado_registros_p7[$i][2]][$resultado_registros_p7[$i][5]]+=1;

        $array_datos_gestion['p7']['estado_lista'][]=$resultado_registros_p7[$i][5];
        $array_datos_gestion['p7']['estado_nombre'][$resultado_registros_p7[$i][5]]=$resultado_registros_p7[$i][12];

        $array_datos_gestion['p7']['agente_lista'][]=$resultado_registros_p7[$i][10];
        $array_datos_gestion['p7']['agente_nombre'][$resultado_registros_p7[$i][10]]=$resultado_registros_p7[$i][16];
        $array_datos_gestion['p7']['agente_estado'][$resultado_registros_p7[$i][10]][$resultado_registros_p7[$i][5]]+=1;
        $array_datos_gestion['p7']['agente_estado_total'][$resultado_registros_p7[$i][10]]+=1;
      }

      $array_datos_gestion['p7']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p7']['gestion_agente']['id']));
      
      $array_datos_total['p7']['total_gestion']=count($resultado_registros_p7);
      $array_datos_total['p7']['total_dias_gestion']=count($array_datos_gestiones['p7']['total_dias']);
      $array_datos_total['p7']['promedio_diario']=count($resultado_registros_p7)/count($array_datos_gestiones['p7']['total_dias']);
      $array_datos_total['p7']['promedio_hora']=count($resultado_registros_p7)/count($array_datos_gestiones['p7']['total_dias_hora']);
      $array_datos_total['p7']['promedio_agente']=count($resultado_registros_p7)/count($array_datos_gestion['p7']['gestion_agente']['id']);
      $array_datos_total['p7']['total_agente']=count($array_datos_gestion['p7']['gestion_agente']['id']);

      $array_datos_gestion['p7']['proyector_lista']=array_values(array_unique($array_datos_gestion['p7']['proyector_lista']));
      $array_datos_gestion['p7']['estado_lista']=array_values(array_unique($array_datos_gestion['p7']['estado_lista']));
      $array_datos_gestion['p7']['agente_lista']=array_values(array_unique($array_datos_gestion['p7']['agente_lista']));
    }


  //8. Formato Entrega Física
    $consulta_string_p8="SELECT `cejef_id`, `cejef_radicado_salida`, `cejef_radicado_entrada`, `cejef_destinatario`, `cejef_direccion`, `cejef_departamento`, `cejef_municipio`, `cejef_observaciones`, `cejef_notificar`, `cejef_registro_usuario`, `cejef_registro_fecha`, TU.`usu_nombres_apellidos`, TC.`ciu_departamento`, TC.`ciu_municipio` FROM `gestion_cejafo_entrega_fisica` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_cejafo_entrega_fisica`.`cejef_municipio`=TC.`ciu_codigo` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_entrega_fisica`.`cejef_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_p8."";
    $consulta_registros_p8 = $enlace_db->prepare($consulta_string_p8);
    if (count($data_consulta)>0) {
        $consulta_registros_p8->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    }
    $consulta_registros_p8->execute();
    $resultado_registros_p8 = $consulta_registros_p8->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_p8)) {
      for ($i=0; $i < count($resultado_registros_p8); $i++) { 
        $array_datos_gestion['p8']['gestion_agente']['id'][]=$resultado_registros_p8[$i][9];
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][9]]['nombre']=$resultado_registros_p8[$i][11];
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][9]]['total']+=1;
        if (!isset($array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][9]]['hora'])) {
          $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][9]]['hora']=$array_anio_mes_hora_val;
          $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][9]]['fecha']=$array_dias_mes_data;
        }
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][9]]['hora'][intval(date('H', strtotime($resultado_registros_p8[$i][10])))]+=1;
        $array_datos_gestion['p8']['gestion_agente'][$resultado_registros_p8[$i][9]]['fecha'][date('Y-m-d', strtotime($resultado_registros_p8[$i][10]))]+=1;
        
        $array_datos_gestiones['p8']['gestion_diaria'][date('Y-m-d', strtotime($resultado_registros_p8[$i][10]))]+=1;
        $array_datos_gestiones['p8']['total_dias'][date('Y-m-d', strtotime($resultado_registros_p8[$i][10]))]=1;
        $array_datos_gestiones['p8']['total_dias_hora'][date('Y-m-d H', strtotime($resultado_registros_p8[$i][10]))]=1;

        //Mapa colombia
          if ($resultado_registros_p8[$i][12]=='ARCHIPIÉLAGO DE SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA') {
            $array_mapa_p8['co-sa']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='CAUCA') {
            $array_mapa_p8['co-ca']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='NARIÑO') {
            $array_mapa_p8['co-na']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='CHOCÓ') {
            $array_mapa_p8['co-ch']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='TOLIMA') {
            $array_mapa_p8['co-to']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='CAQUETÁ') {
            $array_mapa_p8['co-cq']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='HUILA') {
            $array_mapa_p8['co-hu']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='PUTUMAYO') {
            $array_mapa_p8['co-pu']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='AMAZONAS') {
            $array_mapa_p8['co-am']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='BOLÍVAR') {
            $array_mapa_p8['co-bl']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='VALLE DEL CAUCA') {
            $array_mapa_p8['co-vc']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='SUCRE') {
            $array_mapa_p8['co-su']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='ATLÁNTICO') {
            $array_mapa_p8['co-at']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='CESAR') {
            $array_mapa_p8['co-ce']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='LA GUAJIRA') {
            $array_mapa_p8['co-lg']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='MAGDALENA') {
            $array_mapa_p8['co-ma']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='ARAUCA') {
            $array_mapa_p8['co-ar']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='NORTE DE SANTANDER') {
            $array_mapa_p8['co-ns']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='CASANARE') {
            $array_mapa_p8['co-cs']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='GUAVIARE') {
            $array_mapa_p8['co-gv']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='META') {
            $array_mapa_p8['co-me']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='VAUPÉS') {
            $array_mapa_p8['co-vp']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='VICHADA') {
            $array_mapa_p8['co-vd']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='ANTIOQUIA') {
            $array_mapa_p8['co-an']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='CÓRDOBA') {
            $array_mapa_p8['co-co']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='BOYACÁ') {
            $array_mapa_p8['co-by']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='SANTANDER') {
            $array_mapa_p8['co-st']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='CALDAS') {
            $array_mapa_p8['co-cl']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='CUNDINAMARCA') {
            $array_mapa_p8['co-cu']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='BOGOTÁ, D.C.') {
            $array_mapa_p8['co-1136']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='RISARALDA') {
            $array_mapa_p8['co-ri']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='QUINDÍO') {
            $array_mapa_p8['co-qd']+=1;
          } elseif ($resultado_registros_p8[$i][12]=='GUAINÍA') {
            $array_mapa_p8['co-gn']+=1;
          }
      }

      $array_datos_gestion['p8']['gestion_agente']['id']=array_values(array_unique($array_datos_gestion['p8']['gestion_agente']['id']));
      
      $array_datos_total['p8']['total_gestion']=count($resultado_registros_p8);
      $array_datos_total['p8']['total_dias_gestion']=count($array_datos_gestiones['p8']['total_dias']);
      $array_datos_total['p8']['promedio_diario']=count($resultado_registros_p8)/count($array_datos_gestiones['p8']['total_dias']);
      $array_datos_total['p8']['promedio_hora']=count($resultado_registros_p8)/count($array_datos_gestiones['p8']['total_dias_hora']);
      $array_datos_total['p8']['promedio_agente']=count($resultado_registros_p8)/count($array_datos_gestion['p8']['gestion_agente']['id']);
      $array_datos_total['p8']['total_agente']=count($array_datos_gestion['p8']['gestion_agente']['id']);
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
                      1. Proyección de Peticiones Vivienda
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p2-tab" data-bs-toggle="tab" href="#p2" role="tab" aria-controls="p2" aria-selected="true">
                      2. Revisión de Peticiones Vivienda
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p3-tab" data-bs-toggle="tab" href="#p3" role="tab" aria-controls="p2" aria-selected="true">
                      3. Formato de Relación RAE JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p4-tab" data-bs-toggle="tab" href="#p4" role="tab" aria-controls="p2" aria-selected="true">
                      4. Formato de Gestión de Correos
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p5-tab" data-bs-toggle="tab" href="#p5" role="tab" aria-controls="p2" aria-selected="true">
                      5. Formato Gestión de Novedades JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p6-tab" data-bs-toggle="tab" href="#p6" role="tab" aria-controls="p2" aria-selected="true">
                      6. Formato de Gestión de Peticiones JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p7-tab" data-bs-toggle="tab" href="#p7" role="tab" aria-controls="p2" aria-selected="true">
                      7. Formato Gestión de Aprobación JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p8-tab" data-bs-toggle="tab" href="#p8" role="tab" aria-controls="p2" aria-selected="true">
                      8. Formato Entrega Física
                    </button>
                  </div>
                </div>
                <div class="col-md-9 ps-0">
                  <div class="tab-content tab-content-basic pt-0 px-1">
                    <!-- p1 -->
                    <?php include('jafocalizacion_estadisticas_p1.php'); ?>

                    <!-- p2 -->
                    <?php include('jafocalizacion_estadisticas_p2.php'); ?>

                    <!-- p3 -->
                    <?php include('jafocalizacion_estadisticas_p3.php'); ?>

                    <!-- p4 -->
                    <?php include('jafocalizacion_estadisticas_p4.php'); ?>

                    <!-- p5 -->
                    <?php include('jafocalizacion_estadisticas_p5.php'); ?>

                    <!-- p6 -->
                    <?php include('jafocalizacion_estadisticas_p6.php'); ?>

                    <!-- p7 -->
                    <?php include('jafocalizacion_estadisticas_p7.php'); ?>

                    <!-- p8 -->
                    <?php include('jafocalizacion_estadisticas_p8.php'); ?>
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