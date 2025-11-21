<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Monitoreos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  session_start();
  /*VARIABLES*/
  $title = "Calidad";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Monitoreos | ".$bandeja;
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='&bandeja='.base64_encode($bandeja);
  $permisos_usuario = $_SESSION[APP_SESSION . '_session_modulos'][$modulo_plataforma] ?? null;
  unset($_SESSION[APP_SESSION.'_monitoreo_creado']);
  unset($_SESSION[APP_SESSION.'_mon_informacion']);
  unset($_SESSION[APP_SESSION.'_id_monitoreo']);
  unset($_SESSION[APP_SESSION.'_registro_creado_cambio_estado']);
  unset($_SESSION[APP_SESSION.'_registro_eliminado']);
  unset($_SESSION[APP_SESSION.'_registro_cargue_base']);
  unset($_SESSION[APP_SESSION.'_registro_creado_token']);
  unset($_SESSION[APP_SESSION.'_registro_creado_token_cod']);

  // Inicializa variable tipo array
  $data_consulta=array();
  $data_consulta_conteo=array();
  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_permanente=validar_input($_POST['id_filtro']);
  } else {
      $filtro_permanente=validar_input($_GET['id']);
  }

  // Configuracón Paginación
  $registros_x_pagina=50;
  $iniciar_pagina=($pagina-1)*$registros_x_pagina;

  // Valida que filtro se deba ejecutar
  if ($filtro_permanente!="null" AND $filtro_permanente!="") {
      $filtro_buscar="AND (TMC.`gcm_id` LIKE ? OR TM.`gcm_nombre_matriz` LIKE ? OR TMC.`gcm_analista` LIKE ? OR TMC.`gcm_fecha_hora_gestion` LIKE ? OR TMC.`gcm_dependencia` LIKE ? OR TMC.`gcm_identificacion_ciudadano` LIKE ? OR TMC.`gcm_numero_transaccion` LIKE ? OR TMC.`gcm_tipo_monitoreo` LIKE ? OR TMC.`gcm_observaciones_monitoreo` LIKE ? OR TMC.`gcm_nota_enc` LIKE ? OR TMC.`gcm_nota_ecn` LIKE ? OR TMC.`gcm_nota_ecuf` LIKE ? OR TMC.`gcm_estado` LIKE ? OR TMC.`gcm_solucion_contacto` LIKE ? OR TMC.`gcm_causal_nosolucion` LIKE ? OR TMC.`gcm_tipi_programa` LIKE ? OR TMC.`gcm_tipi_tipificacion` LIKE ? OR TMC.`gcm_subtipificacion` LIKE ? OR TMC.`gcm_atencion_wow` LIKE ? OR TMC.`gcm_aplica_voc` LIKE ? OR TMC.`gcm_segmento` LIKE ? OR TMC.`gcm_tabulacion_voc` LIKE ? OR TMC.`gcm_voc` LIKE ? OR TMC.`gcm_emocion_inicial` LIKE ? OR TMC.`gcm_emocion_final` LIKE ? OR TMC.`gcm_que_le_activo` LIKE ? OR TMC.`gcm_atribuible` LIKE ? OR TMC.`gcm_observaciones_info` LIKE ? OR TMC.`gcm_registro_usuario` LIKE ? OR TMC.`gcm_registro_fecha` LIKE ? OR TUA.`usu_nombres_apellidos` LIKE ? OR TS.`usu_nombres_apellidos` LIKE ? OR TUR.`usu_nombres_apellidos` LIKE ? OR TN1.`gic1_item` LIKE ? OR TN2.`gic2_item` LIKE ? OR TN3.`gic3_item` LIKE ? OR TN4.`gic4_item` LIKE ? OR TN5.`gic5_item` LIKE ? OR TN6.`gic6_item` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  if ($permisos_usuario=="Administrador") {
      $filtro_perfil="";
  } elseif ($permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador") {
      $filtro_perfil="";
  } elseif($permisos_usuario=="Supervisor"){
      $filtro_perfil=" AND (TUA.`usu_supervisor`=? OR TMC.`gcm_analista`=?)";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      array_push($data_consulta_conteo, $_SESSION[APP_SESSION.'_session_usu_id']);
      array_push($data_consulta_conteo, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Usuario"){
      $filtro_perfil=" AND TMC.`gcm_analista`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      array_push($data_consulta_conteo, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  if($bandeja=="Mes Actual"){
      $filtro_bandeja=" AND TMC.`gcm_estado`<>? AND TMC.`gcm_estado`<>? AND TMC.`gcm_estado`<>? AND TMC.`gcm_estado`<>? AND TMC.`gcm_tipo_monitoreo`<>? AND TMC.`gcm_tipo_monitoreo`<>? AND TMC.`gcm_registro_fecha` LIKE ? OR ((TMC.`gcm_tipo_monitoreo`=? OR TMC.`gcm_tipo_monitoreo`=?) AND TMC.`gcm_registro_usuario`=?)";
      array_push($data_consulta, 'Pendiente');
      array_push($data_consulta, 'Refutado');
      array_push($data_consulta, 'Refutado-Rechazado');
      array_push($data_consulta, 'Refutado-Rechazado-Nivel 2');
      array_push($data_consulta, 'Calibración-Escucha 1');
      array_push($data_consulta, 'Calibración-Escucha 2');
      $mes_actual=date('Y-m');
      array_push($data_consulta, "$mes_actual%");
      array_push($data_consulta, 'Calibración-Escucha 1');
      array_push($data_consulta, 'Calibración-Escucha 2');
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($bandeja=="Refutados"){
      $filtro_bandeja=" AND (TMC.`gcm_estado`=? OR TMC.`gcm_estado`=?)";
      array_push($data_consulta, 'Refutado');
      array_push($data_consulta, 'Refutado-Nivel 2');
  } elseif($bandeja=="Pendientes"){
      $filtro_bandeja=" AND (TMC.`gcm_estado`=? OR TMC.`gcm_estado`=? OR TMC.`gcm_estado`=? OR ((TMC.`gcm_estado`=? OR TMC.`gcm_estado`=?) AND (TMC.`gcm_nota_enc`<100 OR TMC.`gcm_nota_ecn`<100 OR TMC.`gcm_nota_ecuf`<100)))";
      array_push($data_consulta, 'Pendiente');
      array_push($data_consulta, 'Refutado-Rechazado');
      array_push($data_consulta, 'Refutado-Rechazado-Nivel 2');
      array_push($data_consulta, 'Refutado-Aceptado');
      array_push($data_consulta, 'Refutado-Aceptado-Nivel 2');
  } elseif($bandeja=="Histórico"){
      $filtro_bandeja=" AND TMC.`gcm_estado`<>? AND TMC.`gcm_estado`<>? AND TMC.`gcm_estado`<>? AND TMC.`gcm_estado`<>? AND TMC.`gcm_tipo_monitoreo`<>? AND TMC.`gcm_tipo_monitoreo`<>? AND TMC.`gcm_registro_fecha` NOT LIKE ? OR ((TMC.`gcm_tipo_monitoreo`=? OR TMC.`gcm_tipo_monitoreo`=?) AND TMC.`gcm_registro_usuario`=?)";
      array_push($data_consulta, 'Pendiente');
      array_push($data_consulta, 'Refutado');
      array_push($data_consulta, 'Refutado-Rechazado');
      array_push($data_consulta, 'Refutado-Rechazado-Nivel 2');
      array_push($data_consulta, 'Calibración-Escucha 1');
      array_push($data_consulta, 'Calibración-Escucha 2');
      $mes_actual=date('Y-m');
      array_push($data_consulta, "$mes_actual%");
      array_push($data_consulta, 'Calibración-Escucha 1');
      array_push($data_consulta, 'Calibración-Escucha 2');
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(TMC.`gcm_id`) FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja."";

  // Agrega string a sentencia preparada
  $consulta_contar_registros = $enlace_db->prepare($consulta_contar_string);
  
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_contar_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  // Ejecuta sentencia preparada
  $consulta_contar_registros->execute();
  // Obtiene array resultado de ejecución sentencia preparada
  $resultado_registros_contar = $consulta_contar_registros->get_result()->fetch_all(MYSQLI_NUM);
  $registros_cantidad_total = $resultado_registros_contar[0][0];
  //Cálculo número de páginas 
  $numero_paginas=ceil($registros_cantidad_total/$registros_x_pagina);

  //Agregar pagina a array data_consulta
  array_push($data_consulta, $iniciar_pagina);
  array_push($data_consulta, $registros_x_pagina);

  $consulta_string="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal`, TMC.`gcm_fecha_reac_limite`, TMC.`gcm_fecha_reac`, TMC.`gcm_fecha_calidad_reac_limite`, TMC.`gcm_fecha_calidad_reac`, TMC.`gcm_fecha_snivel_reac_limite`, TMC.`gcm_fecha_snivel_reac`, TMC.`gcm_fecha_sreac_limite`, TMC.`gcm_fecha_sreac`, TMC.`gcm_fecha_novedad_inicio`, TMC.`gcm_fecha_novedad_fin`, TMC.`gcm_novedad_observaciones`, TUA.`usu_estado`, TUA.`usu_supervisor` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja." ORDER BY `gcm_id` DESC LIMIT ?,?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_matriz="SELECT `gcm_id`, `gcm_nombre_matriz`, `gcm_estado`, `gcm_observaciones`, `gcm_registro_usuario`, `gcm_registro_fecha` FROM `gestion_calidad_matriz` ORDER BY `gcm_nombre_matriz`";
  $consulta_registros_matriz = $enlace_db->prepare($consulta_string_matriz);
  $consulta_registros_matriz->execute();
  $resultado_registros_matriz = $consulta_registros_matriz->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_usuario_red` FROM `administrador_usuario` WHERE `usu_estado`='Activo' AND (`usu_cargo_rol` LIKE '%Agente%' OR `usu_cargo_rol` LIKE '%Supervisor%') ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_conteo_pendientes="SELECT COUNT(TMC.`gcm_id`) FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` WHERE (TMC.`gcm_estado`='Pendiente' OR TMC.`gcm_estado`='Refutado-Rechazado' OR TMC.`gcm_estado`='Refutado-Rechazado-Nivel 2' OR ((TMC.`gcm_estado`='Refutado-Aceptado' OR TMC.`gcm_estado`='Refutado-Aceptado-Nivel 2') AND (TMC.`gcm_nota_enc`<100 OR TMC.`gcm_nota_ecn`<100 OR TMC.`gcm_nota_ecuf`<100))) ".$filtro_perfil."";
  $consulta_registros_conteo_pendientes = $enlace_db->prepare($consulta_string_conteo_pendientes);
  if (count($data_consulta_conteo)>0) {
      $consulta_registros_conteo_pendientes->bind_param(str_repeat("s", count($data_consulta_conteo)), ...$data_consulta_conteo);
  }
  $consulta_registros_conteo_pendientes->execute();
  $resultado_registros_conteo_pendientes = $consulta_registros_conteo_pendientes->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_conteo_refutado="SELECT COUNT(TMC.`gcm_id`) FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` WHERE (TMC.`gcm_estado`='Refutado' OR TMC.`gcm_estado`='Refutado-Nivel 2') ".$filtro_perfil."";
  $consulta_registros_conteo_refutado = $enlace_db->prepare($consulta_string_conteo_refutado);
  if (count($data_consulta_conteo)>0) {
      $consulta_registros_conteo_refutado->bind_param(str_repeat("s", count($data_consulta_conteo)), ...$data_consulta_conteo);
  }
  $consulta_registros_conteo_refutado->execute();
  $resultado_registros_conteo_refutado = $consulta_registros_conteo_refutado->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <script type="text/javascript">
      <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
          <?php if($resultado_registros[$i][48]!="" AND $resultado_registros[$i][49]=='' AND $resultado_registros[$i][13]=="Pendiente"): ?>
              //tiempo_gestion
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_anio=<?php echo date('Y',strtotime($resultado_registros[$i][48]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_mes=<?php echo date('m',strtotime($resultado_registros[$i][48]))-1;?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_dia=<?php echo date('d',strtotime($resultado_registros[$i][48]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_hora=<?php echo date('H',strtotime($resultado_registros[$i][48]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_minuto=<?php echo date('i',strtotime($resultado_registros[$i][48]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_segundo=<?php echo date('s',strtotime($resultado_registros[$i][48]));?>;
          <?php endif; ?>
          <?php if($resultado_registros[$i][50]!="" AND $resultado_registros[$i][51]=='' AND $resultado_registros[$i][13]=="Refutado"): ?>
              //tiempo_gestion
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_anio=<?php echo date('Y',strtotime($resultado_registros[$i][50]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_mes=<?php echo date('m',strtotime($resultado_registros[$i][50]))-1;?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_dia=<?php echo date('d',strtotime($resultado_registros[$i][50]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_hora=<?php echo date('H',strtotime($resultado_registros[$i][50]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_minuto=<?php echo date('i',strtotime($resultado_registros[$i][50]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_segundo=<?php echo date('s',strtotime($resultado_registros[$i][50]));?>;
          <?php endif; ?>
          <?php if($resultado_registros[$i][52]!="" AND $resultado_registros[$i][53]=='' AND ($resultado_registros[$i][13]=="Refutado-Rechazado" OR ($resultado_registros[$i][13]=="Refutado-Aceptado" AND ($resultado_registros[$i][10]<100 OR $resultado_registros[$i][11]<100 OR $resultado_registros[$i][12]<100)))): ?>
              //tiempo_gestion
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_anio=<?php echo date('Y',strtotime($resultado_registros[$i][52]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_mes=<?php echo date('m',strtotime($resultado_registros[$i][52]))-1;?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_dia=<?php echo date('d',strtotime($resultado_registros[$i][52]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_hora=<?php echo date('H',strtotime($resultado_registros[$i][52]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_minuto=<?php echo date('i',strtotime($resultado_registros[$i][52]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_segundo=<?php echo date('s',strtotime($resultado_registros[$i][52]));?>;
          <?php endif; ?>
          <?php if($resultado_registros[$i][54]!="" AND $resultado_registros[$i][55]=='' AND $resultado_registros[$i][13]=="Refutado-Nivel 2"): ?>
              //tiempo_gestion
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_anio=<?php echo date('Y',strtotime($resultado_registros[$i][54]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_mes=<?php echo date('m',strtotime($resultado_registros[$i][54]))-1;?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_dia=<?php echo date('d',strtotime($resultado_registros[$i][54]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_hora=<?php echo date('H',strtotime($resultado_registros[$i][54]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_minuto=<?php echo date('i',strtotime($resultado_registros[$i][54]));?>;
              sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_segundo=<?php echo date('s',strtotime($resultado_registros[$i][54]));?>;
          <?php endif; ?>
      <?php endfor; ?>
  </script>
</head>
<body class="sidebar-dark sidebar-icon-only"  onresize="headerFixTable();" onload="headerFixTable(); 
<?php for ($i=0; $i < count($resultado_registros); $i++): ?>
    <?php if(($resultado_registros[$i][48]!="" AND $resultado_registros[$i][49]=='' AND $resultado_registros[$i][13]=="Pendiente") OR ($resultado_registros[$i][50]!="" AND $resultado_registros[$i][51]=='' AND $resultado_registros[$i][13]=="Refutado") OR ($resultado_registros[$i][52]!="" AND $resultado_registros[$i][53]=='' AND ($resultado_registros[$i][13]=="Refutado-Rechazado" OR ($resultado_registros[$i][13]=="Refutado-Aceptado" AND ($resultado_registros[$i][10]<100 OR $resultado_registros[$i][11]<100 OR $resultado_registros[$i][12]<100)))) OR ($resultado_registros[$i][54]!="" AND $resultado_registros[$i][55]=='' AND $resultado_registros[$i][13]=="Refutado-Nivel 2")): ?>
    tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>(sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_anio,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_mes,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_dia,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_hora,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_minuto,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_segundo);
    <?php endif; ?>
<?php endfor; ?>
">
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
          <div class="row">
            <div class="col-md-3 mb-1">
              <?php require_once(ROOT.'includes/_search.php'); ?>
            </div>
            <div class="col-md-9 mb-1 text-end">
              <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Supervisor"): ?>
                <a href="monitoreos_crear_matriz?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Monitoreo">
                  <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Monitoreo</span>
                </a>
              <?php endif; ?>
              <a href="monitoreos?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Pendientes'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes <?php echo ($resultado_registros_conteo_pendientes[0][0]>0) ? "<div class='float-end alert_conteo_menu ms-1'>".$resultado_registros_conteo_pendientes[0][0]."</div>" : ""; ?></span>
              </a>
              <a href="monitoreos?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Refutados'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Refutados">
                <i class="fas fa-user-times btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Refutados <?php echo ($resultado_registros_conteo_refutado[0][0]>0) ? "<div class='float-end alert_conteo_menu ms-1'>".$resultado_registros_conteo_refutado[0][0]."</div>" : ""; ?></span>
              </a>
              <a href="monitoreos?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Mes Actual'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Mes Actual">
                <i class="fas fa-calendar-alt btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Mes Actual</span>
              </a>
              <a href="monitoreos?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
                <i class="fas fa-history btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Histórico</span>
              </a>
              <?php if($permisos_usuario=="Administrador" OR ($_SESSION[APP_SESSION.'_session_cargo']=="LIDER DE CALIDAD" AND $permisos_usuario=="Gestor")): ?>
                <a href="monitoreos_transacciones?pagina=1&id=null&bandeja=<?php echo base64_encode($bandeja); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Transacciones">
                  <i class="fas fa-qrcode btn-icon-prepend me-0 font-size-12"></i>
                </a>
              <?php endif; ?>
              <a href="monitoreos_estadisticas?pagina=1&id=null&bandeja=<?php echo base64_encode($bandeja); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Estadísticas">
                <i class="fas fa-chart-pie btn-icon-prepend me-0 font-size-12"></i>
              </a>
              <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Cliente"): ?>
                <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                  <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                </button>
              <?php endif; ?>
            </div>
            <div class="col-lg-12">
              <div class="table-responsive table-fixed" id="headerFixTable">
                <table class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th class="px-1 py-2" style="width: 65px;"></th>
                      <th class="px-1 py-2">Consecutivo</th>
                      <th class="px-1 py-2">Agente</th>
                      <th class="px-1 py-2">Responsable</th>
                      <th class="px-1 py-2">Matriz</th>
                      <th class="px-1 py-2">Canal</th>
                      <th class="px-1 py-2">Dependencia</th>
                      <th class="px-1 py-2">Número Interacción</th>
                      <th class="px-1 py-2">Fecha Interacción</th>
                      <th class="px-1 py-2">Tipo Monitoreo</th>
                      <th class="px-1 py-2">Nota ENC</th>
                      <th class="px-1 py-2">Nota ECUF</th>
                      <th class="px-1 py-2">Nota ECN</th>
                      <th class="px-1 py-2">Observaciones</th>
                      <th class="px-1 py-2">Registrado por</th>
                      <th class="px-1 py-2">Fecha Registro</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">
                          <?php if ((($permisos_usuario=="Supervisor" OR $permisos_usuario=="Formador" OR $permisos_usuario=="Administrador") AND ($resultado_registros[$i][13]=="Pendiente" OR $resultado_registros[$i][13]=="Refutado-Rechazado" OR $resultado_registros[$i][13]=="Refutado-Rechazado-Nivel 2")) 
                            OR (($permisos_usuario=="Supervisor" OR $permisos_usuario=="Formador" OR $permisos_usuario=="Administrador") AND ($resultado_registros[$i][13]=="Refutado-Aceptado" AND ($resultado_registros[$i][10]<100 OR $resultado_registros[$i][11]<100 OR $resultado_registros[$i][12]<100))) 
                            OR (($permisos_usuario=="Supervisor" OR $permisos_usuario=="Formador" OR $permisos_usuario=="Administrador") AND ($resultado_registros[$i][13]=="Refutado-Aceptado-Nivel 2" AND ($resultado_registros[$i][10]<100 OR $resultado_registros[$i][11]<100 OR $resultado_registros[$i][12]<100))) 
                            OR (($permisos_usuario=="Gestor") AND $resultado_registros[$i][60]==$_SESSION[APP_SESSION.'_session_usu_id'] AND ($resultado_registros[$i][13]=="Pendiente" OR $resultado_registros[$i][13]=="Refutado-Rechazado" OR $resultado_registros[$i][13]=="Refutado-Rechazado-Nivel 2"))): ?>
                              
                              <a href="monitoreos_aceptar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-success btn-icon px-1 py-1 mb-1" title="Aceptar"><i class="fas fa-check-circle font-size-11"></i></a>

                              <?php if (($permisos_usuario=="Supervisor" OR $permisos_usuario=="Formador" OR $permisos_usuario=="Gestor") AND $resultado_registros[$i][13]=="Pendiente" AND date('Y-m-d H:i:s')<$resultado_registros[$i][48]): ?>
                                  <a href="monitoreos_refutar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Refutar"><i class="fas fa-times-circle font-size-11"></i></a>
                              <?php endif; ?>
                              <?php if ((($permisos_usuario=="Supervisor" OR $permisos_usuario=="Formador") AND $resultado_registros[$i][13]=="Refutado-Rechazado" AND date('Y-m-d H:i:s')<$resultado_registros[$i][52]) OR (($permisos_usuario=="Supervisor" OR $permisos_usuario=="Formador") AND $resultado_registros[$i][13]=="Refutado-Aceptado" AND ($resultado_registros[$i][10]<100 OR $resultado_registros[$i][11]<100 OR $resultado_registros[$i][12]<100) AND date('Y-m-d H:i:s')<$resultado_registros[$i][52])): ?>
                                  <a href="monitoreos_refutar_n2?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Refutar Nivel 2"><i class="fas fa-times-circle font-size-11"></i></a>
                              <?php endif; ?>
                          <?php endif; ?>

                          <?php if ($permisos_usuario=="Gestor" AND $_SESSION[APP_SESSION.'_session_cargo']=="LIDER DE CALIDAD" AND ($resultado_registros[$i][13]=="Refutado")): ?>
                              <a href="monitoreos_refutar_aceptar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-success btn-icon px-1 py-1 mb-1" title="Aceptar"><i class="fas fa-check-circle font-size-11"></i></a>
                              <a href="monitoreos_refutar_rechazar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Rechazar"><i class="fas fa-times-circle font-size-11"></i></a>
                          <?php endif; ?>

                          <?php if ($permisos_usuario=="Administrador" AND ($resultado_registros[$i][13]=="Refutado-Nivel 2")): ?>
                              <a href="monitoreos_refutar_n2_aceptar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-success btn-icon px-1 py-1 mb-1" title="Aceptar"><i class="fas fa-check-circle font-size-11"></i></a>
                              <a href="monitoreos_refutar_n2_rechazar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Rechazar"><i class="fas fa-times-circle font-size-11"></i></a>
                          <?php endif; ?>

                          <a href="#" onClick="open_modal_detalle('<?php echo base64_encode($resultado_registros[$i][0]); ?>');" class="btn btn-dark btn-icon px-1 py-1 mb-1" title="Detalle"><i class="fas fa-file-alt font-size-11"></i></a>

                          <?php if ((($permisos_usuario=="Usuario" OR $permisos_usuario=="Supervisor" OR $permisos_usuario=="Formador" OR $permisos_usuario=="Gestor") AND $resultado_registros[$i][13]=="Pendiente") OR (($permisos_usuario=="Usuario" OR $permisos_usuario=="Supervisor" OR $permisos_usuario=="Formador" OR $permisos_usuario=="Gestor") AND ($resultado_registros[$i][13]=="Refutado-Rechazado" OR $resultado_registros[$i][13]=="Refutado-Aceptado" OR $resultado_registros[$i][13]=="Refutado-Rechazado-Nivel 2" OR $resultado_registros[$i][13]=="Refutado-Aceptado-Nivel 2") AND ($resultado_registros[$i][10]<100 OR $resultado_registros[$i][11]<100 OR $resultado_registros[$i][12]<100))): ?>
                              <a href="monitoreos_token?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Token"><i class="fas fa-key font-size-11"></i></a>
                          <?php endif; ?>

                          <?php if ($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                              <a href="monitoreos_informe?reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-info btn-icon px-1 py-1 mb-1" title="Informe"><i class="fas fa-file-download font-size-11"></i></a>
                          <?php endif; ?>

                          <?php if ($permisos_usuario=="Administrador"): ?>
                              <a href="monitoreos_editar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                              <a href="monitoreos_editar_evaluacion?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar Evaluación"><i class="fas fa-tasks font-size-11"></i></a>
                          <?php endif; ?>

                          <?php if ($permisos_usuario=="Administrador"): ?>
                              <a href="monitoreos_eliminar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Eliminar"><i class="fas fa-trash-alt font-size-11"></i></a>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center <?php if($resultado_registros[$i][13]=="Refutado" OR $resultado_registros[$i][13]=="Refutado-Nivel 2" OR $resultado_registros[$i][13]=="Refutado-Rechazado" OR $resultado_registros[$i][13]=="Refutado-Rechazado-Nivel 2") {echo "color-rojo";} elseif($resultado_registros[$i][13]=="Aceptado" OR $resultado_registros[$i][13]=="Refutado-Aceptado" OR $resultado_registros[$i][13]=="Refutado-Aceptado-Nivel 2") {echo "color-verde";} ?>" title="<?php echo $resultado_registros[$i][13]; ?>">
                          <?php if($resultado_registros[$i][13]=="Pendiente") {echo "<span class='fas fa-user-clock'></span>";} ?>
                          <?php if($resultado_registros[$i][13]=="Aceptado") {echo "<span class='fas fa-user-check'></span>";} ?>
                          <?php if($resultado_registros[$i][13]=="Refutado") {echo "<span class='fas fa-user-times'></span>";} ?>
                          <?php if($resultado_registros[$i][13]=="Refutado-Nivel 2") {echo "<span class='fas fa-layer-group'></span>";} ?>
                          <?php if($resultado_registros[$i][13]=="Refutado-Aceptado") {echo "<span class='fas fa-check-double'></span>";} ?>
                          <?php if($resultado_registros[$i][13]=="Refutado-Rechazado") {echo "<span class='fas fa-times-circle'></span>";} ?>
                          <?php if($resultado_registros[$i][13]=="Refutado-Rechazado-Nivel 2") {echo "<span class='fas fa-times-circle'></span>";} ?>
                          <?php if($resultado_registros[$i][13]=="Refutado-Aceptado-Nivel 2") {echo "<span class='fas fa-check-circle'></span>";} ?>
                          <br>
                          <b><?php echo $resultado_registros[$i][0]; ?></b>
                          <p class="" id='tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>'></p>
                      </td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][37]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][39]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][47]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][5]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][7]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][8]; ?></td>
                      <td class="p-1 font-size-11 text-center <?php if($resultado_registros[$i][10]<100){echo 'color-rojo';}else{echo'color-verde';} ?>"><?php echo $resultado_registros[$i][10]; ?></td>
                      <td class="p-1 font-size-11 text-center <?php if($resultado_registros[$i][12]<100){echo 'color-rojo';}else{echo'color-verde';} ?>"><?php echo $resultado_registros[$i][12]; ?></td>
                      <td class="p-1 font-size-11 text-center <?php if($resultado_registros[$i][11]<100){echo 'color-rojo';}else{echo'color-verde';} ?>"><?php echo $resultado_registros[$i][11]; ?></td>
                      <td class="p-1 font-size-11">
                          <div class="scroll" style="max-width: 200px; max-height: 80px; overflow-y: scroll;">
                              <?php echo $resultado_registros[$i][9]; ?>
                          </div>
                      </td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][40]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][36]; ?></td>
                    </tr>
                    <?php endfor; ?>
                  </tbody>
                </table>
                <?php if(count($resultado_registros)==0): ?>
                  <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
                <?php endif; ?>
              </div>
            </div>
            <?php require_once(ROOT.'includes/_pagination-footer.php'); ?>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- modal reportes -->
        <?php require_once('monitoreos_reporte.php'); ?>
        <!-- modal -->
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

    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
        <?php if(($resultado_registros[$i][48]!="" AND $resultado_registros[$i][49]=='' AND $resultado_registros[$i][13]=="Pendiente") OR ($resultado_registros[$i][50]!="" AND $resultado_registros[$i][51]=='' AND $resultado_registros[$i][13]=="Refutado") OR ($resultado_registros[$i][52]!="" AND $resultado_registros[$i][53]=='' AND ($resultado_registros[$i][13]=="Refutado-Rechazado" OR ($resultado_registros[$i][13]=="Refutado-Aceptado" AND ($resultado_registros[$i][10]<100 OR $resultado_registros[$i][11]<100 OR $resultado_registros[$i][12]<100)))) OR ($resultado_registros[$i][54]!="" AND $resultado_registros[$i][55]=='' AND $resultado_registros[$i][13]=="Refutado-Nivel 2")): ?>
            function tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>(anio, mes, dia, hora, minuto, segundo){
                //set de variables para calculos
                var set_segundos = 1000;
                var set_minutos = set_segundos * 60;
                var set_horas = set_minutos * 60;
                var set_dias = set_horas * 24;
                //se definen las fechas de inicio y fin
                var fecha_inicio = new Date(anio,mes,dia,hora,minuto,segundo);
                
                var fecha_fin = new Date(); //fecha actual
                // alert(fecha_inicio);

                //se calcula la diferencia entre fechas segun la que sea mayor entre la de inicio y fin
                var diff=new Date(fecha_inicio-fecha_fin);
                
                //se calcula la difenrencia obtenida entre inicio y fin y se convierte en milisegundos
                diff_milisegundos = diff.getTime()

                //calculo de dias
                var result_dias = Math.floor(diff_milisegundos / set_dias);
                diff_milisegundos = diff_milisegundos - (result_dias * set_dias);

                //calculo de horas
                var result_horas = Math.floor(diff_milisegundos / set_horas);
                diff_milisegundos = diff_milisegundos - (result_horas * set_horas);

                //calculo de minutos
                var result_minutos = Math.floor(diff_milisegundos / set_minutos);
                diff_milisegundos = diff_milisegundos - (result_minutos * set_minutos);

                //calculo de segundos
                var result_segundos = Math.floor(diff_milisegundos / set_segundos);
                //se asigna a la variable result la cadena que muestra el resultado de dias, horas, minutos y segundos
                var result = result_dias + "d:" + result_horas + "h:" + result_minutos + "m:" + result_segundos + "s";

                //ponemos color al tiempo de gestion
                if(result_dias>0){
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').className = "alert alert-success text-center px-1 py-0 m-0";
                    //mostrar el resultado en la celda con el identificador asignado
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').innerHTML = result;
                } else if(result_horas>11 && result_dias==0){
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').className = "alert alert-success text-center px-1 py-0 m-0";
                    //mostrar el resultado en la celda con el identificador asignado
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').innerHTML = result;
                } else if(result_horas>=4 && result_horas<=11 && result_dias==0){
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').className = "alert alert-warning text-center px-1 py-0 m-0";
                    //mostrar el resultado en la celda con el identificador asignado
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').innerHTML = result;
                } else if(result_horas<4 && result_dias==0){
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').className = "alert alert-danger text-center px-1 py-0 m-0";
                    //mostrar el resultado en la celda con el identificador asignado
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').innerHTML = result;
                } else if (result_dias<0) {
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').className = "alert alert-danger text-center px-1 py-0 m-0";
                    //mostrar el resultado en la celda con el identificador asignado
                    document.getElementById('tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>').innerHTML = 'VENCIDO';
                }

                //Indicamos que se ejecute esta función nuevamente dentro de 1 segundo
                timeout=setTimeout("tiempo_limite_<?php echo $resultado_registros[$i][0]; ?>(sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_anio,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_mes,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_dia,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_hora,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_minuto,sessionStorage.inc_<?php echo $resultado_registros[$i][0]; ?>_tiempo_limite_segundo)",1000);
            }
        <?php endif; ?>
    <?php endfor; ?>
  </script>
</body>
</html>
