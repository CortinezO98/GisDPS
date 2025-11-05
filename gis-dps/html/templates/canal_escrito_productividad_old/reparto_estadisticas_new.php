<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Productividad";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Productividad";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

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


  if ($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']!="" AND $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']!="") {
    for ($k=0; $k < count($array_dias_mes); $k++) { 
        $fecha_resumen=$array_dias_mes[$k];

        $fecha_inicio_resumen=$fecha_resumen;
        $fecha_fin_resumen=$fecha_resumen.' 23:59:59';
        unset($data_consulta);
        unset($array_resumen);
        unset($array_coordinador);
        unset($array_datos_gestion);
        unset($array_metas_hist);
        unset($array_metas);
        unset($array_coordinador_datos);
        unset($array_datos_agente);


        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_resumen);
        array_push($data_consulta, $fecha_resumen.' 23:59:59');
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

        $filtro_fechas_ja_p1=" AND `cejpp_registro_fecha`>=? AND `cejpp_registro_fecha`<=?";
        $filtro_fechas_ja_p2=" AND `cejrp_registro_fecha`>=? AND `cejrp_registro_fecha`<=?";
        $filtro_fechas_ja_p3=" AND `cejrr_registro_fecha`>=? AND `cejrr_registro_fecha`<=?";
        $filtro_fechas_ja_p4=" AND `cejgc_registro_fecha`>=? AND `cejgc_registro_fecha`<=?";
        $filtro_fechas_ja_p5=" AND `cejgn_registro_fecha`>=? AND `cejgn_registro_fecha`<=?";
        $filtro_fechas_ja_p6=" AND `cejgp_registro_fecha`>=? AND `cejgp_registro_fecha`<=?";
        $filtro_fechas_ja_p7=" AND `cejga_registro_fecha`>=? AND `cejga_registro_fecha`<=?";
        $filtro_fechas_ja_p8=" AND `cejef_registro_fecha`>=? AND `cejef_registro_fecha`<=?";

        $filtro_fechas_tm_p1=" AND `cet_registro_fecha`>=? AND `cet_registro_fecha`<=?";
        $filtro_fechas_tm_p2=" AND `cetar_registro_fecha`>=? AND `cetar_registro_fecha`<=?";
        $filtro_fechas_tm_p3=" AND `cetc_registro_fecha`>=? AND `cetc_registro_fecha`<=?";
        $filtro_fechas_tm_p4=" AND `cete_registro_fecha`>=? AND `cete_registro_fecha`<=?";
        $filtro_fechas_tm_p5=" AND `cetfr_usuario_fecha`>=? AND `cetfr_usuario_fecha`<=?";
        $filtro_fechas_tm_p6=" AND `cetpc_registro_fecha`>=? AND `cetpc_registro_fecha`<=?";
        $filtro_fechas_tm_p7=" AND `cetcsg_registro_fecha`>=? AND `cetcsg_registro_fecha`<=?";
        $filtro_fechas_tm_p8=" AND `cetan_registro_fecha`>=? AND `cetan_registro_fecha`<=?";

        //inicializar arrays
          $array_coordinador=array();

        $consulta_string_meta_hist="SELECT DISTINCT `cep_formulario`, `cep_meta` FROM `gestion_ce_productividad` WHERE `cep_fecha`=?";
        $consulta_registros_meta_hist = $enlace_db->prepare($consulta_string_meta_hist);
        $consulta_registros_meta_hist->bind_param("s", $fecha_resumen);
        $consulta_registros_meta_hist->execute();
        $resultado_registros_meta_hist = $consulta_registros_meta_hist->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_meta_hist); $i++) {
          $array_metas_hist[$resultado_registros_meta_hist[$i][0]]['meta']=$resultado_registros_meta_hist[$i][1];
        }

        $consulta_string_meta="SELECT `cef_id`, `cef_grupo`, `cef_nombre`, `cef_meta`, `cef_auxiliar_1`, `cef_auxiliar_2`, `cef_auxiliar_3` FROM `gestion_ce_formularios` WHERE 1=1";
        $consulta_registros_meta = $enlace_db->prepare($consulta_string_meta);
        $consulta_registros_meta->execute();
        $resultado_registros_meta = $consulta_registros_meta->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_meta); $i++) {
          if ($fecha_resumen==date('Y-m-d') OR !isset($array_metas_hist[$resultado_registros_meta[$i][0]]['meta'])) {
            $array_metas[$resultado_registros_meta[$i][0]]['meta']=$resultado_registros_meta[$i][3];
          } else {
            $array_metas[$resultado_registros_meta[$i][0]]['meta']=$array_metas_hist[$resultado_registros_meta[$i][0]]['meta'];
          }

          // if (count($array_dias_mes)>0) {
          //   $array_metas[$resultado_registros_meta[$i][0]]['meta']=$array_metas[$resultado_registros_meta[$i][0]]['meta']*count($array_dias_mes);
          // }

          $array_metas[$resultado_registros_meta[$i][0]]['nombre']=$resultado_registros_meta[$i][2];

          $array_datos_gestion[$resultado_registros_meta[$i][0]]['promedio_suma']=0;
          $array_datos_gestion[$resultado_registros_meta[$i][0]]['promedio_general']=0;
          $array_datos_gestion[$resultado_registros_meta[$i][0]]['gestion_agente']=array();
          $array_datos_gestion[$resultado_registros_meta[$i][0]]['gestion_agente']['id']=array();
        }


        if ($fecha_resumen==date('Y-m-d')) {
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
        } else {
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
        }


        //CONSULTA CONTEO RESUMIDO
        // SELECT `cepc_registro_usuario`, DATE_FORMAT(`cepc_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`cepc_registro_fecha`, '%H') AS HORA, COUNT(`cepc_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_proyeccion_consolidacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_consolidacion`.`cepc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` GROUP BY `cepc_registro_usuario`, FECHA, HORA


        //REPARTO
            //1. Proyección Consolidación
              $id_formulario='reparto_proyeccion_consolidacion';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `cepc_registro_usuario`, DATE_FORMAT(`cepc_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`cepc_registro_fecha`, '%H') AS HORA, COUNT(`cepc_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_proyeccion_consolidacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_consolidacion`.`cepc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p1." GROUP BY `cepc_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  // $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //2. Aprobación Firma FA
              $id_formulario='reparto_aprobacion_firma_fa';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `ceaff_registro_usuario`, DATE_FORMAT(`ceaff_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`ceaff_registro_fecha`, '%H') AS HORA, COUNT(`ceaff_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_aprobacion_firma_fa` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p2." GROUP BY `ceaff_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;

                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //3. Firma FA
              $id_formulario='reparto_firma_fa';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `ceff_registro_usuario`, DATE_FORMAT(`ceff_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`ceff_registro_fecha`, '%H') AS HORA, COUNT(`ceff_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_firma_fa` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_firma_fa`.`ceff_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p3." GROUP BY `ceff_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //4. Inspección Proyección
              $id_formulario='reparto_inspeccion_proyeccion';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `ceip_registro_usuario`, DATE_FORMAT(`ceip_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`ceip_registro_fecha`, '%H') AS HORA, COUNT(`ceip_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_inspeccion_proyeccion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_inspeccion_proyeccion`.`ceip_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p4." GROUP BY `ceip_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                $array_datos_gestion[$id_formulario]['tipo_rechazo_lista']=array();
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //5. Proyección FA
              $id_formulario='reparto_proyeccion_fa';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `cepfa_registro_usuario`, DATE_FORMAT(`cepfa_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`cepfa_registro_fecha`, '%H') AS HORA, COUNT(`cepfa_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_proyeccion_fa` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_fa`.`cepfa_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p5." GROUP BY `cepfa_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //6. Aprobación Firma
              $id_formulario='reparto_aprobacion_firma';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `ceaf_registro_usuario`, DATE_FORMAT(`ceaf_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`ceaf_registro_fecha`, '%H') AS HORA, COUNT(`ceaf_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_aprobacion_firma` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_aprobacion_firma`.`ceaf_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p6." GROUP BY `ceaf_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //7. Firma Traslados
              $id_formulario='reparto_firma_traslados';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `ceft_registro_usuario`, DATE_FORMAT(`ceft_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`ceft_registro_fecha`, '%H') AS HORA, COUNT(`ceft_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_firma_traslados` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_firma_traslados`.`ceft_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p7." GROUP BY `ceft_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //8. Proyectores
              $id_formulario='reparto_proyectores';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `cep_registro_usuario`, DATE_FORMAT(`cep_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`cep_registro_fecha`, '%H') AS HORA, COUNT(`cep_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_proyectores` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyectores`.`cep_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p8." GROUP BY `cep_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //9. Seguimiento Lanzamientos TR
              $id_formulario='reparto_lanzamientos_tr';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `celtr_registro_usuario`, DATE_FORMAT(`celtr_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`celtr_registro_fecha`, '%H') AS HORA, COUNT(`celtr_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_lanzamientos_tr` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_lanzamientos_tr`.`celtr_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p9." GROUP BY `celtr_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //10. Seguimiento Envíos Web
              $id_formulario='reparto_seguimiento_envios_web';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `cesew_registro_usuario`, DATE_FORMAT(`cesew_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`cesew_registro_fecha`, '%H') AS HORA, COUNT(`cesew_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_envios_web` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_envios_web`.`cesew_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p10." GROUP BY `cesew_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //11. Seguimiento Cargue Documentos
              $id_formulario='reparto_seguimiento_cargue_documentos';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `cescd_registro_usuario`, DATE_FORMAT(`cescd_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`cescd_registro_fecha`, '%H') AS HORA, COUNT(`cescd_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_cargue_documentos` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_cargue_documentos`.`cescd_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p11." GROUP BY `cescd_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //12. Seguimiento Radicación
              $id_formulario='reparto_seguimiento_radicacion';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `cesr_registro_usuario`, DATE_FORMAT(`cesr_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`cesr_registro_fecha`, '%H') AS HORA, COUNT(`cesr_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_radicacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_radicacion`.`cesr_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p12." GROUP BY `cesr_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //13. Seguimiento Tipificaciones
              $id_formulario='reparto_seguimiento_tipificaciones';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `cest_registro_usuario`, DATE_FORMAT(`cest_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`cest_registro_fecha`, '%H') AS HORA, COUNT(`cest_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_tipificaciones` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_tipificaciones`.`cest_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p13." GROUP BY `cest_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //14. Seguimiento Inspección Tipificación
              $id_formulario='reparto_seguimiento_inspeccion_tipificacion';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string="SELECT `cesit_registro_usuario`, DATE_FORMAT(`cesit_registro_fecha`, '%Y-%m-%d') AS FECHA, DATE_FORMAT(`cesit_registro_fecha`, '%H') AS HORA, COUNT(`cesit_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_inspeccion_tipificacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p14." GROUP BY `cesit_registro_usuario`, FECHA, HORA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros[$i][0];
                  $array_datos_agente[$resultado_registros[$i][0]]['nombre']=$resultado_registros[$i][4];
                  $array_datos_agente[$resultado_registros[$i][0]]['coordinador']=$resultado_registros[$i][6];
                  
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['total']+=$resultado_registros[$i][3];
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha']=$array_dias_mes_data;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['hora'][intval($resultado_registros[$i][2])]+=$resultado_registros[$i][3];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha'][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
                  // $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=$resultado_registros[$i][1];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros[$i][0]]['fecha_conteo'][]=$resultado_registros[$i][1];
                  
                  $array_coordinador[]=$resultado_registros[$i][6];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['nombre']=$resultado_registros[$i][5];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes'][]=$resultado_registros[$i][0];
                  $array_coordinador_datos[$resultado_registros[$i][6]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros[$i][6]]['agentes']));
                }
            
                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente[$id_agente]['coordinador'];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


        //JAFOCALIZACIÓN
            //1. Proyección de Peticiones Vivienda
              $id_formulario='jafocalizacion_proyeccion_peticiones';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string_ja_p1="SELECT `cejpp_id`, `cejpp_radicado_entrada`, `cejpp_proyector`, `cejpp_novedad_radicado`, `cejpp_formato`, `cejpp_identificacion_peticionario`, `cejpp_nombre_peticionario`, `cejpp_correo`, `cejpp_observaciones`, `cejpp_notificar`, `cejpp_registro_usuario`, `cejpp_registro_fecha`, NOVEDAD.`ceco_valor`, FORMATO.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_proyeccion_peticiones` LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_novedad_radicado`=NOVEDAD.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS FORMATO ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_formato`=FORMATO.`ceco_id`
               LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_proyector`=PROYECTOR.`usu_id`
               LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p1."";
              $consulta_registros_ja_p1 = $enlace_db->prepare($consulta_string_ja_p1);
              if (count($data_consulta)>0) {
                  $consulta_registros_ja_p1->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros_ja_p1->execute();
              $resultado_registros_ja_p1 = $consulta_registros_ja_p1->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros_ja_p1)) {
                for ($i=0; $i < count($resultado_registros_ja_p1); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_ja_p1[$i][10];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p1[$i][10]]['nombre']=$resultado_registros_ja_p1[$i][15];
                  $array_datos_agente[$resultado_registros_ja_p1[$i][10]]['nombre']=$resultado_registros_ja_p1[$i][15];
                  $array_datos_agente_coordinador[$resultado_registros_ja_p1[$i][10]]=$resultado_registros_ja_p1[$i][17];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p1[$i][10]]['coordinador']=$resultado_registros_ja_p1[$i][16];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p1[$i][10]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p1[$i][10]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p1[$i][10]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p1[$i][10]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p1[$i][10]]['hora'][intval(date('H', strtotime($resultado_registros_ja_p1[$i][11])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p1[$i][10]]['fecha'][date('Y-m-d', strtotime($resultado_registros_ja_p1[$i][11]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_ja_p1[$i][11]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p1[$i][10]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_ja_p1[$i][11]));

                  $array_coordinador[]=$resultado_registros_ja_p1[$i][17];
                  $array_coordinador_datos[$resultado_registros_ja_p1[$i][17]]['nombre']=$resultado_registros_ja_p1[$i][16];
                  $array_coordinador_datos[$resultado_registros_ja_p1[$i][17]]['agentes'][]=$resultado_registros_ja_p1[$i][10];
                  $array_coordinador_datos[$resultado_registros_ja_p1[$i][17]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_ja_p1[$i][17]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //2. Revisión de Peticiones Vivienda 
              $id_formulario='jafocalizacion_revision_peticiones';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string_ja_p2="SELECT `cejrp_id`, `cejrp_radicado_entrada`, `cejrp_realiza_traslado`, `cejrp_aprobador`, `cejrp_proyector`, `cejrp_estado`, `cejrp_error_digitalizacion`, `cejrp_caso_particular`, `cejrp_observaciones`, `cejrp_notificar`, `cejrp_registro_usuario`, `cejrp_registro_fecha`, REALIZATRASLADO.`ceco_valor`, ESTADO.`ceco_valor`, ERRORDIGITA.`ceco_valor`, CASOPARTICULAR.`ceco_valor`, TU.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos`, PROYECTOR.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_revision_peticiones` 
                LEFT JOIN `gestion_ce_configuracion` AS REALIZATRASLADO ON `gestion_cejafo_revision_peticiones`.`cejrp_realiza_traslado`=REALIZATRASLADO.`ceco_id`
                LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cejafo_revision_peticiones`.`cejrp_estado`=ESTADO.`ceco_id`
                LEFT JOIN `gestion_ce_configuracion` AS ERRORDIGITA ON `gestion_cejafo_revision_peticiones`.`cejrp_error_digitalizacion`=ERRORDIGITA.`ceco_id`
                LEFT JOIN `gestion_ce_configuracion` AS CASOPARTICULAR ON `gestion_cejafo_revision_peticiones`.`cejrp_caso_particular`=CASOPARTICULAR.`ceco_id`
                LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cejafo_revision_peticiones`.`cejrp_aprobador`=APROBADOR.`usu_id`
                LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_revision_peticiones`.`cejrp_proyector`=PROYECTOR.`usu_id`
                LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_revision_peticiones`.`cejrp_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p2."";
              $consulta_registros_ja_p2 = $enlace_db->prepare($consulta_string_ja_p2);
              if (count($data_consulta)>0) {
                  $consulta_registros_ja_p2->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros_ja_p2->execute();
              $resultado_registros_ja_p2 = $consulta_registros_ja_p2->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros_ja_p2)) {
                for ($i=0; $i < count($resultado_registros_ja_p2); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_ja_p2[$i][10];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p2[$i][10]]['nombre']=$resultado_registros_ja_p2[$i][16];
                  $array_datos_agente[$resultado_registros_ja_p2[$i][10]]['nombre']=$resultado_registros_ja_p2[$i][16];
                  $array_datos_agente_coordinador[$resultado_registros_ja_p2[$i][10]]=$resultado_registros_ja_p2[$i][20];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p2[$i][10]]['coordinador']=$resultado_registros_ja_p2[$i][19];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p2[$i][10]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p2[$i][10]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p2[$i][10]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p2[$i][10]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p2[$i][10]]['hora'][intval(date('H', strtotime($resultado_registros_ja_p2[$i][11])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p2[$i][10]]['fecha'][date('Y-m-d', strtotime($resultado_registros_ja_p2[$i][11]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_ja_p2[$i][11]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p2[$i][10]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_ja_p2[$i][11]));

                  $array_coordinador[]=$resultado_registros_ja_p2[$i][20];
                  $array_coordinador_datos[$resultado_registros_ja_p2[$i][20]]['nombre']=$resultado_registros_ja_p2[$i][19];
                  $array_coordinador_datos[$resultado_registros_ja_p2[$i][20]]['agentes'][]=$resultado_registros_ja_p2[$i][10];
                  $array_coordinador_datos[$resultado_registros_ja_p2[$i][20]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_ja_p2[$i][20]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //3. Formato de Relación RAE JeA
              $id_formulario='jafocalizacion_relacion_rae';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string_ja_p3="SELECT `cejrr_id`, `cejrr_radicado_salida`, `cejrr_radicado_entrada`, `cejrr_destinatario`, `cejrr_direccion`, `cejrr_municipio`, `cejrr_modalidad_envio`, `cejrr_srjv`, `cejrr_proyector`, `cejrr_aprobador`, `cejrr_firma`, `cejrr_cedula_firmante`, `cejrr_fecha_gestion_rae`, `cejrr_fecha_envio`, `cejrr_qq`, `cejrr_observaciones`, `cejrr_notificar`, `cejrr_registro_usuario`, `cejrr_registro_fecha`, MODALIDADENVIO.`ceco_valor`, SRJV.`ceco_valor`, FIRMA.`ceco_valor`, TU.`usu_nombres_apellidos`, TCIU.`ciu_departamento`, TCIU.`ciu_municipio`, PROYECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_relacion_rae` LEFT JOIN `gestion_ce_configuracion` AS MODALIDADENVIO ON `gestion_cejafo_relacion_rae`.`cejrr_modalidad_envio`=MODALIDADENVIO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS SRJV ON `gestion_cejafo_relacion_rae`.`cejrr_srjv`=SRJV.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS FIRMA ON `gestion_cejafo_relacion_rae`.`cejrr_firma`=FIRMA.`ceco_id`
               LEFT JOIN `administrador_ciudades` AS TCIU ON `gestion_cejafo_relacion_rae`.`cejrr_municipio`=TCIU.`ciu_codigo`
               LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_relacion_rae`.`cejrr_proyector`=PROYECTOR.`usu_id`
               LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cejafo_relacion_rae`.`cejrr_aprobador`=APROBADOR.`usu_id`
               LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_relacion_rae`.`cejrr_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p3."";
              $consulta_registros_ja_p3 = $enlace_db->prepare($consulta_string_ja_p3);
              if (count($data_consulta)>0) {
                  $consulta_registros_ja_p3->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros_ja_p3->execute();
              $resultado_registros_ja_p3 = $consulta_registros_ja_p3->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros_ja_p3)) {
                for ($i=0; $i < count($resultado_registros_ja_p3); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_ja_p3[$i][17];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p3[$i][17]]['nombre']=$resultado_registros_ja_p3[$i][22];
                  $array_datos_agente[$resultado_registros_ja_p3[$i][17]]['nombre']=$resultado_registros_ja_p3[$i][22];
                  $array_datos_agente_coordinador[$resultado_registros_ja_p3[$i][17]]=$resultado_registros_ja_p3[$i][28];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p3[$i][17]]['coordinador']=$resultado_registros_ja_p3[$i][27];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p3[$i][17]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p3[$i][17]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p3[$i][17]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p3[$i][17]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p3[$i][17]]['hora'][intval(date('H', strtotime($resultado_registros_ja_p3[$i][18])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p3[$i][17]]['fecha'][date('Y-m-d', strtotime($resultado_registros_ja_p3[$i][18]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_ja_p3[$i][18]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p3[$i][17]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_ja_p3[$i][18]));

                  $array_coordinador[]=$resultado_registros_ja_p3[$i][28];
                  $array_coordinador_datos[$resultado_registros_ja_p3[$i][28]]['nombre']=$resultado_registros_ja_p3[$i][27];
                  $array_coordinador_datos[$resultado_registros_ja_p3[$i][28]]['agentes'][]=$resultado_registros_ja_p3[$i][17];
                  $array_coordinador_datos[$resultado_registros_ja_p3[$i][28]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_ja_p3[$i][28]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //4. Formato de Gestión de Correos
              $id_formulario='jafocalizacion_gestion_correos';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string_ja_p4="SELECT `cejgc_id`, `cejgc_fecha_recibido`, `cejgc_gestion`, `cejgc_documento`, `cejgc_tipo_documento`, `cejgc_nombre_completo`, `cejgc_codigo_beneficiario`, `cejgc_email`, `cejgc_celular`, `cejgc_departamento`, `cejgc_municipio`, `cejgc_categoria`, `cejgc_gestion_2`, `cejgc_tipificacion`, `cejgc_carga_di`, `cejgc_carga_soporte_bachiller`, `cejgc_observaciones`, `cejgc_notificar`, `cejgc_registro_usuario`, `cejgc_registro_fecha`, GESTION.`ceco_valor`, TIPODOCUMENTO.`ceco_valor`, TC.`ciu_departamento`, TC.`ciu_municipio`, CATEGORIA.`ceco_valor`, GESTION2.`ceco_valor`, TIPIFICACION.`ceco_valor`, TU.`usu_nombres_apellidos`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cejafo_gestion_correo` LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cejafo_gestion_correo`.`cejgc_gestion`=GESTION.`ceco_id`
                 LEFT JOIN `gestion_ce_configuracion` AS TIPODOCUMENTO ON `gestion_cejafo_gestion_correo`.`cejgc_tipo_documento`=TIPODOCUMENTO.`ceco_id`
                 LEFT JOIN `administrador_ciudades` AS TC ON `gestion_cejafo_gestion_correo`.`cejgc_municipio`=TC.`ciu_codigo`
                 LEFT JOIN `gestion_ce_configuracion` AS CATEGORIA ON `gestion_cejafo_gestion_correo`.`cejgc_categoria`=CATEGORIA.`ceco_id`
                 LEFT JOIN `gestion_ce_configuracion` AS GESTION2 ON `gestion_cejafo_gestion_correo`.`cejgc_gestion_2`=GESTION2.`ceco_id`
                 LEFT JOIN `gestion_ce_configuracion` AS TIPIFICACION ON `gestion_cejafo_gestion_correo`.`cejgc_tipificacion`=TIPIFICACION.`ceco_id`
                 LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_correo`.`cejgc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p4."";
              $consulta_registros_ja_p4 = $enlace_db->prepare($consulta_string_ja_p4);
              if (count($data_consulta)>0) {
                  $consulta_registros_ja_p4->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros_ja_p4->execute();
              $resultado_registros_ja_p4 = $consulta_registros_ja_p4->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros_ja_p4)) {
                for ($i=0; $i < count($resultado_registros_ja_p4); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_ja_p4[$i][18];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p4[$i][18]]['nombre']=$resultado_registros_ja_p4[$i][27];
                  $array_datos_agente[$resultado_registros_ja_p4[$i][18]]['nombre']=$resultado_registros_ja_p4[$i][27];
                  $array_datos_agente_coordinador[$resultado_registros_ja_p4[$i][18]]=$resultado_registros_ja_p4[$i][29];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p4[$i][18]]['coordinador']=$resultado_registros_ja_p4[$i][28];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p4[$i][18]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p4[$i][18]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p4[$i][18]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p4[$i][18]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p4[$i][18]]['hora'][intval(date('H', strtotime($resultado_registros_ja_p4[$i][19])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p4[$i][18]]['fecha'][date('Y-m-d', strtotime($resultado_registros_ja_p4[$i][19]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_ja_p4[$i][19]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p4[$i][18]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_ja_p4[$i][19]));

                  $array_coordinador[]=$resultado_registros_ja_p4[$i][29];
                  $array_coordinador_datos[$resultado_registros_ja_p4[$i][29]]['nombre']=$resultado_registros_ja_p4[$i][28];
                  $array_coordinador_datos[$resultado_registros_ja_p4[$i][29]]['agentes'][]=$resultado_registros_ja_p4[$i][18];
                  $array_coordinador_datos[$resultado_registros_ja_p4[$i][29]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_ja_p4[$i][29]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //5. Formato Gestión de Novedades JeA
              $id_formulario='jafocalizacion_gestion_novedades';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string_ja_p5="SELECT `cejgn_id`, `cejgn_id_novedad`, `cejgn_id_persona`, `cejgn_fecha_gestion`, `cejgn_estado`, `cejgn_tipo_rechazo`, `cejgn_observacion_rechazo`, `cejgn_correccion_datos_sija`, `cejgn_codigo_novedad`, `cejgn_observaciones`, `cejgn_notificar`, `cejgn_registro_usuario`, `cejgn_registro_fecha`, ESTADO.`ceco_valor`, TIPORECHAZO.`ceco_valor`, DATOSSIJA.`ceco_valor`, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_gestion_novedades` 
               LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cejafo_gestion_novedades`.`cejgn_estado`=ESTADO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS TIPORECHAZO ON `gestion_cejafo_gestion_novedades`.`cejgn_tipo_rechazo`=TIPORECHAZO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS DATOSSIJA ON `gestion_cejafo_gestion_novedades`.`cejgn_correccion_datos_sija`=DATOSSIJA.`ceco_id`
               LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_novedades`.`cejgn_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p5."";
              $consulta_registros_ja_p5 = $enlace_db->prepare($consulta_string_ja_p5);
              if (count($data_consulta)>0) {
                  $consulta_registros_ja_p5->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros_ja_p5->execute();
              $resultado_registros_ja_p5 = $consulta_registros_ja_p5->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros_ja_p5)) {
                for ($i=0; $i < count($resultado_registros_ja_p5); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_ja_p5[$i][11];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p5[$i][11]]['nombre']=$resultado_registros_ja_p5[$i][16];
                  $array_datos_agente[$resultado_registros_ja_p5[$i][11]]['nombre']=$resultado_registros_ja_p5[$i][16];
                  $array_datos_agente_coordinador[$resultado_registros_ja_p5[$i][11]]=$resultado_registros_ja_p5[$i][18];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p5[$i][11]]['coordinador']=$resultado_registros_ja_p5[$i][17];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p5[$i][11]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p5[$i][11]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p5[$i][11]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p5[$i][11]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p5[$i][11]]['hora'][intval(date('H', strtotime($resultado_registros_ja_p5[$i][12])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p5[$i][11]]['fecha'][date('Y-m-d', strtotime($resultado_registros_ja_p5[$i][12]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_ja_p5[$i][12]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p5[$i][11]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_ja_p5[$i][12]));

                  $array_coordinador[]=$resultado_registros_ja_p5[$i][18];
                  $array_coordinador_datos[$resultado_registros_ja_p5[$i][18]]['nombre']=$resultado_registros_ja_p5[$i][17];
                  $array_coordinador_datos[$resultado_registros_ja_p5[$i][18]]['agentes'][]=$resultado_registros_ja_p5[$i][11];
                  $array_coordinador_datos[$resultado_registros_ja_p5[$i][18]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_ja_p5[$i][18]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //6. Formato de Gestión de Peticiones JeA
              $id_formulario='jafocalizacion_gestion_peticiones';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string_ja_p6="SELECT `cejgp_id`, `cejgp_radicado`, `cejgp_proyector`, `cejgp_aprobador`, `cejgp_peticionario_identificacion`, `cejgp_peticionario_nombres`, `cejgp_correo_direccion`, `cejgp_municipio`, `cejgp_solicitud`, `cejgp_no_registra_sija`, `cejgp_tipo_documento`, `cejgp_fecha_nacimiento_solicitante`, `cejgp_novedad`, `cejgp_no_radicado`, `cejgp_novedad_adicional`, `cejgp_codigo_beneficiario`, `cejgp_gestion_actualizacion`, `cejgp_institucion_estudia`, `cejgp_nivel_formacion`, `cejgp_convenio`, `cejgp_observacion_actualizacion`, `cejgp_codigo_beneficiario_caso_especial`, `cejgp_municipio_reporte`, `cejgp_observacion_caso_especial`, `cejgp_observaciones`, `cejgp_notificar`, `cejgp_registro_usuario`, `cejgp_registro_fecha`, SOLICITUD.`ceco_valor`, SIJA.`ceco_valor`, TIPODOCUMENTO.`ceco_valor`, NOVEDAD.`ceco_valor`, NOVEDADADD.`ceco_valor`, GESTIONACTUALIZACION.`ceco_valor`, INSTITUCIONESTUDIA.`ceco_valor`, NIVELFORMACION.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos`, TC1.`ciu_departamento`, TC1.`ciu_municipio`, TC2.`ciu_departamento`, TC2.`ciu_municipio`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_gestion_peticiones` 
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
               LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_peticiones`.`cejgp_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p6."";
              $consulta_registros_ja_p6 = $enlace_db->prepare($consulta_string_ja_p6);
              if (count($data_consulta)>0) {
                  $consulta_registros_ja_p6->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros_ja_p6->execute();
              $resultado_registros_ja_p6 = $consulta_registros_ja_p6->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros_ja_p6)) {
                for ($i=0; $i < count($resultado_registros_ja_p6); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_ja_p6[$i][26];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p6[$i][26]]['nombre']=$resultado_registros_ja_p6[$i][38];
                  $array_datos_agente[$resultado_registros_ja_p6[$i][26]]['nombre']=$resultado_registros_ja_p6[$i][38];
                  $array_datos_agente_coordinador[$resultado_registros_ja_p6[$i][26]]=$resultado_registros_ja_p6[$i][44];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p6[$i][26]]['coordinador']=$resultado_registros_ja_p6[$i][43];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p6[$i][26]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p6[$i][26]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p6[$i][26]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p6[$i][26]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p6[$i][26]]['hora'][intval(date('H', strtotime($resultado_registros_ja_p6[$i][27])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p6[$i][26]]['fecha'][date('Y-m-d', strtotime($resultado_registros_ja_p6[$i][27]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_ja_p6[$i][27]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p6[$i][26]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_ja_p6[$i][27]));

                  $array_coordinador[]=$resultado_registros_ja_p6[$i][44];
                  $array_coordinador_datos[$resultado_registros_ja_p6[$i][44]]['nombre']=$resultado_registros_ja_p6[$i][43];
                  $array_coordinador_datos[$resultado_registros_ja_p6[$i][44]]['agentes'][]=$resultado_registros_ja_p6[$i][26];
                  $array_coordinador_datos[$resultado_registros_ja_p6[$i][44]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_ja_p6[$i][44]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //7. Formato Gestión de Aprobación JeA
              $id_formulario='jafocalizacion_gestion_aprobacion';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string_ja_p7="SELECT `cejga_id`, `cejga_radicado_entrada`, `cejga_proyector`, `cejga_revisor`, `cejga_cedula_aprobador`, `cejga_gestion`, `cejga_oportunidad_mejora`, `cejga_comentario_delta`, `cejga_observaciones`, `cejga_notificar`, `cejga_registro_usuario`, `cejga_registro_fecha`, GESTION.`ceco_valor`, OPORTUNIDAD.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, REVISOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_gestion_aprobacion` 
               LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cejafo_gestion_aprobacion`.`cejga_gestion`=GESTION.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS OPORTUNIDAD ON `gestion_cejafo_gestion_aprobacion`.`cejga_oportunidad_mejora`=OPORTUNIDAD.`ceco_id`
               LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_gestion_aprobacion`.`cejga_proyector`=PROYECTOR.`usu_id`
               LEFT JOIN `administrador_usuario` AS REVISOR ON `gestion_cejafo_gestion_aprobacion`.`cejga_revisor`=REVISOR.`usu_id`
               LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_aprobacion`.`cejga_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p7."";
              $consulta_registros_ja_p7 = $enlace_db->prepare($consulta_string_ja_p7);
              if (count($data_consulta)>0) {
                  $consulta_registros_ja_p7->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros_ja_p7->execute();
              $resultado_registros_ja_p7 = $consulta_registros_ja_p7->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros_ja_p7)) {
                for ($i=0; $i < count($resultado_registros_ja_p7); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_ja_p7[$i][10];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p7[$i][10]]['nombre']=$resultado_registros_ja_p7[$i][16];
                  $array_datos_agente[$resultado_registros_ja_p7[$i][10]]['nombre']=$resultado_registros_ja_p7[$i][16];
                  $array_datos_agente_coordinador[$resultado_registros_ja_p7[$i][10]]=$resultado_registros_ja_p7[$i][18];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p7[$i][10]]['coordinador']=$resultado_registros_ja_p7[$i][17];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p7[$i][10]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p7[$i][10]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p7[$i][10]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p7[$i][10]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p7[$i][10]]['hora'][intval(date('H', strtotime($resultado_registros_ja_p7[$i][11])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p7[$i][10]]['fecha'][date('Y-m-d', strtotime($resultado_registros_ja_p7[$i][11]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_ja_p7[$i][11]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p7[$i][10]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_ja_p7[$i][11]));

                  $array_coordinador[]=$resultado_registros_ja_p7[$i][18];
                  $array_coordinador_datos[$resultado_registros_ja_p7[$i][18]]['nombre']=$resultado_registros_ja_p7[$i][17];
                  $array_coordinador_datos[$resultado_registros_ja_p7[$i][18]]['agentes'][]=$resultado_registros_ja_p7[$i][10];
                  $array_coordinador_datos[$resultado_registros_ja_p7[$i][18]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_ja_p7[$i][18]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


            //8. Formato Entrega Física
              $id_formulario='jafocalizacion_entregas_fisicas';
              $meta_formulario=$array_metas[$id_formulario]['meta'];
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $consulta_string_ja_p8="SELECT `cejef_id`, `cejef_radicado_salida`, `cejef_radicado_entrada`, `cejef_destinatario`, `cejef_direccion`, `cejef_departamento`, `cejef_municipio`, `cejef_observaciones`, `cejef_notificar`, `cejef_registro_usuario`, `cejef_registro_fecha`, TU.`usu_nombres_apellidos`, TC.`ciu_departamento`, TC.`ciu_municipio`, TCO.`usu_nombres_apellidos`, TCO.`usu_id` FROM `gestion_cejafo_entrega_fisica` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_cejafo_entrega_fisica`.`cejef_municipio`=TC.`ciu_codigo` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_entrega_fisica`.`cejef_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TCO ON TU.`usu_supervisor`=TCO.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p8."";
              $consulta_registros_ja_p8 = $enlace_db->prepare($consulta_string_ja_p8);
              if (count($data_consulta)>0) {
                  $consulta_registros_ja_p8->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros_ja_p8->execute();
              $resultado_registros_ja_p8 = $consulta_registros_ja_p8->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros_ja_p8)) {
                for ($i=0; $i < count($resultado_registros_ja_p8); $i++) { 
                  $array_datos_gestion[$id_formulario]['gestion_agente']['id'][]=$resultado_registros_ja_p8[$i][9];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p8[$i][9]]['nombre']=$resultado_registros_ja_p8[$i][11];
                  $array_datos_agente[$resultado_registros_ja_p8[$i][9]]['nombre']=$resultado_registros_ja_p8[$i][11];
                  $array_datos_agente_coordinador[$resultado_registros_ja_p8[$i][9]]=$resultado_registros_ja_p8[$i][15];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p8[$i][9]]['coordinador']=$resultado_registros_ja_p8[$i][14];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p8[$i][9]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p8[$i][9]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p8[$i][9]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p8[$i][9]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p8[$i][9]]['hora'][intval(date('H', strtotime($resultado_registros_ja_p8[$i][10])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p8[$i][9]]['fecha'][date('Y-m-d', strtotime($resultado_registros_ja_p8[$i][10]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_ja_p8[$i][10]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_ja_p8[$i][9]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_ja_p8[$i][10]));

                  $array_coordinador[]=$resultado_registros_ja_p8[$i][15];
                  $array_coordinador_datos[$resultado_registros_ja_p8[$i][15]]['nombre']=$resultado_registros_ja_p8[$i][14];
                  $array_coordinador_datos[$resultado_registros_ja_p8[$i][15]]['agentes'][]=$resultado_registros_ja_p8[$i][9];
                  $array_coordinador_datos[$resultado_registros_ja_p8[$i][15]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_ja_p8[$i][15]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


        //TMNC
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
                  $array_datos_agente_coordinador[$resultado_registros_tm_p1[$i][18]]=$resultado_registros_tm_p1[$i][32];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['coordinador']=$resultado_registros_tm_p1[$i][31];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p1[$i][19])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p1[$i][19]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_tm_p1[$i][19]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p1[$i][18]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_tm_p1[$i][19]));

                  $array_coordinador[]=$resultado_registros_tm_p1[$i][32];
                  $array_coordinador_datos[$resultado_registros_tm_p1[$i][32]]['nombre']=$resultado_registros_tm_p1[$i][31];
                  $array_coordinador_datos[$resultado_registros_tm_p1[$i][32]]['agentes'][]=$resultado_registros_tm_p1[$i][18];
                  $array_coordinador_datos[$resultado_registros_tm_p1[$i][32]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p1[$i][32]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

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
                  $array_datos_agente_coordinador[$resultado_registros_tm_p2[$i][13]]=$resultado_registros_tm_p2[$i][22];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['coordinador']=$resultado_registros_tm_p2[$i][21];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p2[$i][14])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p2[$i][14]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_tm_p2[$i][14]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p2[$i][13]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_tm_p2[$i][14]));

                  $array_coordinador[]=$resultado_registros_tm_p2[$i][22];
                  $array_coordinador_datos[$resultado_registros_tm_p2[$i][22]]['nombre']=$resultado_registros_tm_p2[$i][21];
                  $array_coordinador_datos[$resultado_registros_tm_p2[$i][22]]['agentes'][]=$resultado_registros_tm_p2[$i][13];
                  $array_coordinador_datos[$resultado_registros_tm_p2[$i][22]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p2[$i][22]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

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
                  $array_datos_agente_coordinador[$resultado_registros_tm_p3[$i][39]]=$resultado_registros_tm_p3[$i][52];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['coordinador']=$resultado_registros_tm_p3[$i][51];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p3[$i][40])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p3[$i][40]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_tm_p3[$i][40]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p3[$i][39]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_tm_p3[$i][40]));

                  $array_coordinador[]=$resultado_registros_tm_p3[$i][52];
                  $array_coordinador_datos[$resultado_registros_tm_p3[$i][52]]['nombre']=$resultado_registros_tm_p3[$i][51];
                  $array_coordinador_datos[$resultado_registros_tm_p3[$i][52]]['agentes'][]=$resultado_registros_tm_p3[$i][39];
                  $array_coordinador_datos[$resultado_registros_tm_p3[$i][52]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p3[$i][52]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

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
                  $array_datos_agente_coordinador[$resultado_registros_tm_p4[$i][17]]=$resultado_registros_tm_p4[$i][31];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['coordinador']=$resultado_registros_tm_p4[$i][30];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p4[$i][18])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p4[$i][18]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_tm_p4[$i][18]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p4[$i][17]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_tm_p4[$i][18]));

                  $array_coordinador[]=$resultado_registros_tm_p4[$i][31];
                  $array_coordinador_datos[$resultado_registros_tm_p4[$i][31]]['nombre']=$resultado_registros_tm_p4[$i][30];
                  $array_coordinador_datos[$resultado_registros_tm_p4[$i][31]]['agentes'][]=$resultado_registros_tm_p4[$i][17];
                  $array_coordinador_datos[$resultado_registros_tm_p4[$i][31]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p4[$i][31]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

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
                  $array_datos_agente_coordinador[$resultado_registros_tm_p5[$i][10]]=$resultado_registros_tm_p5[$i][18];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['coordinador']=$resultado_registros_tm_p5[$i][17];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p5[$i][11])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p5[$i][11]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_tm_p5[$i][11]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p5[$i][10]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_tm_p5[$i][11]));

                  $array_coordinador[]=$resultado_registros_tm_p5[$i][18];
                  $array_coordinador_datos[$resultado_registros_tm_p5[$i][18]]['nombre']=$resultado_registros_tm_p5[$i][17];
                  $array_coordinador_datos[$resultado_registros_tm_p5[$i][18]]['agentes'][]=$resultado_registros_tm_p5[$i][10];
                  $array_coordinador_datos[$resultado_registros_tm_p5[$i][18]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p5[$i][18]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

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
                  $array_datos_agente_coordinador[$resultado_registros_tm_p6[$i][5]]=$resultado_registros_tm_p6[$i][9];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['coordinador']=$resultado_registros_tm_p6[$i][8];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p6[$i][6])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p6[$i][6]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_tm_p6[$i][6]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p6[$i][5]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_tm_p6[$i][6]));

                  $array_coordinador[]=$resultado_registros_tm_p6[$i][9];
                  $array_coordinador_datos[$resultado_registros_tm_p6[$i][9]]['nombre']=$resultado_registros_tm_p6[$i][8];
                  $array_coordinador_datos[$resultado_registros_tm_p6[$i][9]]['agentes'][]=$resultado_registros_tm_p6[$i][5];
                  $array_coordinador_datos[$resultado_registros_tm_p6[$i][9]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p6[$i][9]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

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
                  $array_datos_agente_coordinador[$resultado_registros_tm_p7[$i][9]]=$resultado_registros_tm_p7[$i][18];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['coordinador']=$resultado_registros_tm_p7[$i][17];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p7[$i][10])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p7[$i][10]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_tm_p7[$i][10]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p7[$i][9]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_tm_p7[$i][10]));

                  $array_coordinador[]=$resultado_registros_tm_p7[$i][18];
                  $array_coordinador_datos[$resultado_registros_tm_p7[$i][18]]['nombre']=$resultado_registros_tm_p7[$i][17];
                  $array_coordinador_datos[$resultado_registros_tm_p7[$i][18]]['agentes'][]=$resultado_registros_tm_p7[$i][9];
                  $array_coordinador_datos[$resultado_registros_tm_p7[$i][18]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p7[$i][18]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

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
                  $array_datos_agente_coordinador[$resultado_registros_tm_p8[$i][16]]=$resultado_registros_tm_p8[$i][29];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['coordinador']=$resultado_registros_tm_p8[$i][28];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['total']+=1;
                  if (!isset($array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['hora'])) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['hora']=$array_anio_mes_hora_val;
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['fecha']=$array_dias_mes_data;
                  }
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['hora'][intval(date('H', strtotime($resultado_registros_tm_p8[$i][17])))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['fecha'][date('Y-m-d', strtotime($resultado_registros_tm_p8[$i][17]))]+=1;
                  $array_datos_gestion[$id_formulario]['gestion_agente']['fecha'][]=date('Y-m-d', strtotime($resultado_registros_tm_p8[$i][17]));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$resultado_registros_tm_p8[$i][16]]['fecha_conteo'][]=date('Y-m-d', strtotime($resultado_registros_tm_p8[$i][17]));

                  $array_coordinador[]=$resultado_registros_tm_p8[$i][29];
                  $array_coordinador_datos[$resultado_registros_tm_p8[$i][29]]['nombre']=$resultado_registros_tm_p8[$i][28];
                  $array_coordinador_datos[$resultado_registros_tm_p8[$i][29]]['agentes'][]=$resultado_registros_tm_p8[$i][16];
                  $array_coordinador_datos[$resultado_registros_tm_p8[$i][29]]['agentes']=array_values(array_unique($array_coordinador_datos[$resultado_registros_tm_p8[$i][29]]['agentes']));
                  
                }

                $array_datos_gestion[$id_formulario]['gestion_agente']['id']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente']['id']));

                for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++) { 
                  $id_agente=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']));
                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']=array_values(array_unique($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha']));
                  
                  $total_dias_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['fecha_conteo']);
                  $meta_calculada=$array_metas[$id_formulario]['meta'];
                  
                  if ($total_dias_agente>0) {
                    $meta_calculada=$array_metas[$id_formulario]['meta']*$total_dias_agente;
                  }

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['meta_calculada']=$meta_calculada;

                  $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total']*100)/$meta_calculada;
                  
                  if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']>100) {
                    $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje']=100;
                  }

                  // $array_datos_gestion[$id_formulario]['promedio_suma']+=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['formularios'][]=$id_formulario;
                  $array_resumen[$id_agente]['productividad'][$id_formulario]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $array_resumen[$id_agente]['productividad_total'][]=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];

                  $array_resumen[$id_agente]['tipologia']=array();
                  $array_resumen[$id_agente]['novedad']=array();
                  $array_resumen[$id_agente]['comentarios']=array();
                  $array_resumen[$id_agente]['productividad_total_ajustada']=array();

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  $cep_id=$id_agente.'-'.$id_formulario.'-'.date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']));
                  $cep_formulario=$id_formulario;
                  $cep_agente=$id_agente;
                  $cep_coordinador=$array_datos_agente_coordinador[$id_agente];
                  $cep_fecha=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
                  $cep_meta=$array_metas[$id_formulario]['meta'];
                  $cep_gestiones=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['total'];
                  $cep_productividad=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente]['porcentaje'];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');
                  
                  if (date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']))==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
                    $sentencia_insert->execute();
                  }
                }

              }


              // echo "<pre>";
              // print_r($array_datos_gestion);
              // echo "</pre>";


        $array_coordinador=array_values(array_unique($array_coordinador));

        $consulta_string_justificacion="SELECT `cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`, `cep_registro_fecha`, `cep_productividad_ajustada` FROM `gestion_ce_productividad` WHERE `cep_fecha`>=? AND `cep_fecha`<=?";
        $consulta_registros_justificacion = $enlace_db->prepare($consulta_string_justificacion);
        $consulta_registros_justificacion->bind_param("ss", $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'], $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']);
        $consulta_registros_justificacion->execute();
        $resultado_registros_justificacion = $consulta_registros_justificacion->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_justificacion); $i++) { 
          $array_datos_gestion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['tipologia']=$resultado_registros_justificacion[$i][8];
          $array_datos_gestion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['novedad']=$resultado_registros_justificacion[$i][9];
          
          if ($resultado_registros_justificacion[$i][13]=='') {
            $array_datos_gestion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['ajustada']=0;
            $array_resumen[$resultado_registros_justificacion[$i][2]]['productividad_total_ajustada'][]=0;
          } else {
            $array_datos_gestion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['ajustada']=$resultado_registros_justificacion[$i][13];
            $array_resumen[$resultado_registros_justificacion[$i][2]]['productividad_total_ajustada'][]=$resultado_registros_justificacion[$i][13];
          }
          
          if ($resultado_registros_justificacion[$i][8]!='') {
            $array_resumen[$resultado_registros_justificacion[$i][2]]['tipologia'][]=$resultado_registros_justificacion[$i][8];
          }

          if ($resultado_registros_justificacion[$i][9]!='') {
            $array_resumen[$resultado_registros_justificacion[$i][2]]['novedad'][]=$resultado_registros_justificacion[$i][9];
          }

          if ($resultado_registros_justificacion[$i][10]!='') {
            $array_resumen[$resultado_registros_justificacion[$i][2]]['comentarios'][]=$resultado_registros_justificacion[$i][10];
          }

          if ($resultado_registros_justificacion[$i][13]>$resultado_registros_justificacion[$i][7]) {
            $productividad_suma=$resultado_registros_justificacion[$i][13];
          } else {
            $productividad_suma=$resultado_registros_justificacion[$i][7];
          }

          $array_datos_gestion[$resultado_registros_justificacion[$i][1]]['promedio_suma']+=$productividad_suma;

          $array_datos_gestion[$resultado_registros_justificacion[$i][1]]['promedio_general']=$array_datos_gestion[$resultado_registros_justificacion[$i][1]]['promedio_suma']/count($array_datos_gestion[$resultado_registros_justificacion[$i][1]]['gestion_agente']['id']);
            
        }
    }


  }
  
  

  

  

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_usuario_red` FROM `administrador_usuario` WHERE `usu_estado`='Activo' AND (`usu_cargo_rol` LIKE '%Agente%') ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <script src="<?php echo PLUGINS; ?>Highcharts/code/highcharts.js"></script>
  <script src="<?php echo PLUGINS; ?>Highcharts/code/highcharts-more.js"></script>
  <script src="<?php echo PLUGINS; ?>Highcharts/code/modules/solid-gauge.js"></script>
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
                  <div class="row px-3 mb-3">
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
                    <button type="button" class="btn btn-outline-dark px-1 py-2 active" style="text-align: left !important;" id="p0-tab" data-bs-toggle="tab" href="#p0" role="tab" aria-controls="p0" aria-selected="true">
                      <i class="fas fa-chart-pie btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Resumen Productividad
                    </button>
                    
                    <button type="button" class="btn btn-corp btn-icon-text font-size-12 color-blanco px-1 py-3" style="text-align: left !important;" data-bs-toggle="tab" role="tab">
                      <i class="fas fa-rectangle-list btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Reparto
                    </button>

                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p1-tab" data-bs-toggle="tab" href="#p1" role="tab" aria-controls="p1" aria-selected="true">
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

                    <button type="button" class="btn btn-corp btn-icon-text font-size-12 color-blanco px-1 py-3" style="text-align: left !important;" data-bs-toggle="tab" role="tab">
                      <i class="fas fa-rectangle-list btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Jóvenes en Acción y Focalización
                    </button>

                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p1-tab" data-bs-toggle="tab" href="#ja_p1" role="tab" aria-controls="ja_p1" aria-selected="true">
                      1. Proyección de Peticiones Vivienda
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p2-tab" data-bs-toggle="tab" href="#ja_p2" role="tab" aria-controls="ja_p2" aria-selected="true">
                      2. Revisión de Peticiones Vivienda
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p3-tab" data-bs-toggle="tab" href="#ja_p3" role="tab" aria-controls="ja_p3" aria-selected="true">
                      3. Formato de Relación RAE JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p4-tab" data-bs-toggle="tab" href="#ja_p4" role="tab" aria-controls="ja_p4" aria-selected="true">
                      4. Formato de Gestión de Correos
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p5-tab" data-bs-toggle="tab" href="#ja_p5" role="tab" aria-controls="ja_p5" aria-selected="true">
                      5. Formato Gestión de Novedades JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p6-tab" data-bs-toggle="tab" href="#ja_p6" role="tab" aria-controls="ja_p6" aria-selected="true">
                      6. Formato de Gestión de Peticiones JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p7-tab" data-bs-toggle="tab" href="#ja_p7" role="tab" aria-controls="ja_p7" aria-selected="true">
                      7. Formato Gestión de Aprobación JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p8-tab" data-bs-toggle="tab" href="#ja_p8" role="tab" aria-controls="ja_p8" aria-selected="true">
                      8. Formato Entrega Física
                    </button>

                    <button type="button" class="btn btn-corp btn-icon-text font-size-12 color-blanco px-1 py-3" style="text-align: left !important;" data-bs-toggle="tab" role="tab">
                      <i class="fas fa-rectangle-list btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Transferencias Monetarias No Condicionadas
                    </button>

                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p1-tab" data-bs-toggle="tab" href="#tm_p1" role="tab" aria-controls="tm_p1" aria-selected="true">
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
                    <!-- p0 -->
                    <?php include('reparto_estadisticas_p0.php'); ?>

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