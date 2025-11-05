<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Envíos WEB";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Envíos Web";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $estado=validar_input(base64_decode($_GET['estado']));
  $subtitle = "Correo | ".$bandeja." | ".$estado;
  $pagina=validar_input($_GET['pagina']);
  
  unset($_SESSION[APP_SESSION.'_registro_creado_radicacion']);
  unset($_SESSION[APP_SESSION.'_registro_asignado_radicacion']);
  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');
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
  $registros_x_pagina=100;
  $iniciar_pagina=($pagina-1)*$registros_x_pagina;

  if ($permisos_usuario=="Administrador") {
      $filtro_perfil="";
  } elseif ($permisos_usuario=="Gestor") {
      $filtro_perfil="";
  } elseif($permisos_usuario=="Supervisor"){
      $filtro_perfil="";
  } elseif($permisos_usuario=="Cliente"){
      $filtro_perfil="";
  } elseif($permisos_usuario=="Usuario" AND $bandeja!="Todos"){
      $filtro_perfil=" AND `gewc_responsable`=?";
      $filtro_perfil_conteo=" AND `gewc_responsable`=?";
      
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      array_push($data_consulta_conteo, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Usuario" AND $bandeja=="Todos"){
      $filtro_perfil="";
      $filtro_perfil_conteo=" AND `gewc_responsable`=?";
      array_push($data_consulta_conteo, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  $fecha_actual=date('Y-m');

  if ($estado=="Pendientes") {
      $filtro_buscar_estado=" AND (`gewc_estado`=? OR `gewc_estado`=? OR `gewc_estado`=?)";
      array_push($data_consulta, 'Pendiente');
      array_push($data_consulta, 'Asignado');
      array_push($data_consulta, 'En trámite');
  } elseif ($estado=="Mes Actual") {
      $filtro_buscar_estado=" AND `gewc_estado`=? AND `gewc_correo_fecha` LIKE ?";
      array_push($data_consulta, 'Finalizado');
      array_push($data_consulta, "%$fecha_actual%");
  } elseif($estado=="Histórico"){
      $filtro_buscar_estado=" AND `gewc_estado`=?";
      array_push($data_consulta, 'Finalizado');
  }

  // Valida que filtro se deba ejecutar
  if ($filtro_permanente!="null" AND $filtro_permanente!="") {
      $filtro_buscar="AND (`gewc_radicado` LIKE ? OR `gewc_radicado_entrada` LIKE ? OR `gewc_radicado_salida` LIKE ? OR `gewc_correo_asunto` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      array_push($data_consulta, $filtro_permanente);
      array_push($data_consulta, $filtro_permanente);
      for ($i=2; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  if($bandeja=='Reparto'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Reparto');
  } elseif($bandeja=='Subsidio Familiar de Vivienda en especie'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Subsidio Familiar de Vivienda en especie');
  } elseif($bandeja=='Ingreso Solidario'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Ingreso Solidario');
  } elseif($bandeja=='Colombia Mayor'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Colombia Mayor');
  } elseif($bandeja=='Compensación del IVA'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Compensación del IVA');
  } elseif($bandeja=='Antifraudes'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Antifraudes');
  } elseif($bandeja=='Jóvenes en Acción'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Jóvenes en Acción');
  } elseif($bandeja=='Tránsito a Renta Ciudadana'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Tránsito a Renta Ciudadana');
  } elseif($bandeja=='Otros programas'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Otros programas');
  } elseif($bandeja=="Todos"){
      $filtro_bandeja="";
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`gewc_id`) FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos`.`gewc_responsable`=TAG.`usu_id` WHERE 1=1 ".$filtro_perfil." ".$filtro_buscar_estado." ".$filtro_buscar." ".$filtro_bandeja."";

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

  $consulta_string="SELECT `gewc_id`, `gewc_radicado`, `gewc_radicado_entrada`, `gewc_radicado_salida`, `gewc_tipologia`, `gewc_clasificacion`, `gewc_responsable`, `gewc_gestion`, `gewc_gestion_detalle`, `gewc_estado`, `gewc_fecha_gestion`, `gewc_correo_remitente`, `gewc_correo_asunto`, `gewc_correo_fecha`, `gewc_registro_fecha`, `gewc_registro_fecha_hora`, TAG.`usu_nombres_apellidos` FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos`.`gewc_responsable`=TAG.`usu_id` WHERE 1=1 ".$filtro_perfil." ".$filtro_buscar_estado." ".$filtro_buscar." ".$filtro_bandeja." ORDER BY `gewc_correo_fecha` ASC LIMIT ?,?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  // Prepara string a ejecutar en sentencia preparada
  $consulta_bandejas_string="SELECT `gewc_tipologia`, COUNT(`gewc_id`) FROM `gestion_enviosweb_casos` WHERE 1=1 AND `gewc_estado`='Pendiente' ".$filtro_perfil_conteo." GROUP BY `gewc_tipologia`";

  // Agrega string a sentencia preparada
  $consulta_bandejas_registros = $enlace_db->prepare($consulta_bandejas_string);

  if (count($data_consulta_conteo)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta_conteo en el orden específico de los parámetros de la sentencia preparada
      $consulta_bandejas_registros->bind_param(str_repeat("s", count($data_consulta_conteo)), ...$data_consulta_conteo);
  }
  // Ejecuta sentencia preparada
  $consulta_bandejas_registros->execute();
  // Obtiene array resultado de ejecución sentencia preparada
  $resultado_registros_bandejas = $consulta_bandejas_registros->get_result()->fetch_all(MYSQLI_NUM);

  $array_conteo['Reparto']=0;
  $array_conteo['Subsidio Familiar de Vivienda en especie']=0;
  $array_conteo['Ingreso Solidario']=0;
  $array_conteo['Colombia Mayor']=0;
  $array_conteo['Compensación del IVA']=0;
  $array_conteo['Antifraudes']=0;
  $array_conteo['Jóvenes en Acción']=0;
  $array_conteo['Tránsito a Renta Ciudadana']=0;
  $array_conteo['Otros programas']=0;

  for ($i=0; $i < count($resultado_registros_bandejas); $i++) { 
    $array_conteo[$resultado_registros_bandejas[$i][0]]=$resultado_registros_bandejas[$i][1];
  }

  $array_estado_alert['Pendiente']='warning';
  $array_estado_alert['En trámite']='dark';
  $array_estado_alert['Finalizado']='success';

  $parametros_add='&bandeja='.base64_encode($bandeja).'&estado='.base64_encode($estado);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only" onload="headerFixTable();" onresize="headerFixTable();">
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
            <div class="col-lg-2 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 col-lg-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                          <nav class="navbar-mail">
                            <ul class="navbar-nav flex-column w-100">
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Reparto') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Reparto'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-info-circle me-2"></i>Reparto</span>
                                    <?php if($array_conteo['Reparto']>0): ?>
                                      <span class="bg-danger text-white p-1 radius-10"><?php echo $array_conteo['Reparto']; ?></span>
                                    <?php endif; ?>
                                  </span>
                                </a>
                              </li>
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Subsidio Familiar de Vivienda en especie') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Subsidio Familiar de Vivienda en especie'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-info-circle me-2"></i>Subsidio Familiar de Vivienda en especie</span>
                                    <?php if($array_conteo['Subsidio Familiar de Vivienda en especie']>0): ?>
                                      <span class="bg-danger text-white p-1 radius-10"><?php echo $array_conteo['Subsidio Familiar de Vivienda en especie']; ?></span>
                                    <?php endif; ?>
                                  </span>
                                </a>
                              </li>
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Ingreso Solidario') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Ingreso Solidario'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-info-circle me-2"></i>Ingreso Solidario</span>
                                    <?php if($array_conteo['Ingreso Solidario']>0): ?>
                                      <span class="bg-danger text-white p-1 radius-10"><?php echo $array_conteo['Ingreso Solidario']; ?></span>
                                    <?php endif; ?>
                                  </span>
                                </a>
                              </li>
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Colombia Mayor') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Colombia Mayor'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-info-circle me-2"></i>Colombia Mayor</span>
                                    <?php if($array_conteo['Colombia Mayor']>0): ?>
                                      <span class="bg-danger text-white p-1 radius-10"><?php echo $array_conteo['Colombia Mayor']; ?></span>
                                    <?php endif; ?>
                                  </span>
                                </a>
                              </li>
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Compensación del IVA') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Compensación del IVA'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-info-circle me-2"></i>Compensación del IVA</span>
                                    <?php if($array_conteo['Compensación del IVA']>0): ?>
                                      <span class="bg-danger text-white p-1 radius-10"><?php echo $array_conteo['Compensación del IVA']; ?></span>
                                    <?php endif; ?>
                                  </span>
                                </a>
                              </li>
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Antifraudes') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Antifraudes'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-info-circle me-2"></i>Antifraudes</span>
                                    <?php if($array_conteo['Antifraudes']>0): ?>
                                      <span class="bg-danger text-white p-1 radius-10"><?php echo $array_conteo['Antifraudes']; ?></span>
                                    <?php endif; ?>
                                  </span>
                                </a>
                              </li>
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Jóvenes en Acción') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Jóvenes en Acción'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-info-circle me-2"></i>Jóvenes en Acción</span>
                                    <?php if($array_conteo['Jóvenes en Acción']>0): ?>
                                      <span class="bg-danger text-white p-1 radius-10"><?php echo $array_conteo['Jóvenes en Acción']; ?></span>
                                    <?php endif; ?>
                                  </span>
                                </a>
                              </li>
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Tránsito a Renta Ciudadana') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Tránsito a Renta Ciudadana'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-info-circle me-2"></i>Tránsito a Renta Ciudadana</span>
                                    <?php if($array_conteo['Tránsito a Renta Ciudadana']>0): ?>
                                      <span class="bg-danger text-white p-1 radius-10"><?php echo $array_conteo['Tránsito a Renta Ciudadana']; ?></span>
                                    <?php endif; ?>
                                  </span>
                                </a>
                              </li>
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Otros programas') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Otros programas'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-info-circle me-2"></i>Otros programas</span>
                                    <?php if($array_conteo['Otros programas']>0): ?>
                                      <span class="bg-danger text-white p-1 radius-10"><?php echo $array_conteo['Otros programas']; ?></span>
                                    <?php endif; ?>
                                  </span>
                                </a>
                              </li>
                              
                              <li class="nav-item">
                                <a class="btn btn-outline-dark d-block px-1 py-2 mb-1 <?php echo ($bandeja=='Todos') ? 'active' : ''; ?>" href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Todos'); ?>&estado=<?php echo base64_encode('Pendientes'); ?>">
                                  <span class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center text-start"><i class="fas fa-inbox me-2"></i>Todos</span>
                                    <!-- <span class="bg-danger text-white p-1 radius-10">500</span> -->
                                  </span>
                                </a>
                              </li>
                              
                              <!-- <li class="d-grid border-top pt-3 mt-5">
                                <a href="#!" class="nav-link">
                                  <i data-feather="settings" class="icon-xs me-1"></i> Setting
                                </a>
                              </li> -->
                            </ul>
                          </nav>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-10 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-md-12 col-lg-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="list align-items-center pt-0">
                        <div class="wrapper w-100">
                          <div class="col-md-12">
                            <div class="row px-2">
                              <div class="col-md-5 mb-1">
                                <form name="filtrado" action="" method="POST">
                                  <div class="form-group m-0">
                                    <div class="input-group">
                                      <input type="text" class="form-control form-control-sm" name="id_filtro" value='<?php if (isset($_POST["filtro"])) { echo $filtro_permanente; } else {if($filtro_permanente!="null"){echo $filtro_permanente;}} ?>' placeholder="Búsqueda" autofocus>
                                      <div class="input-group-append">
                                        <button class="btn py-2 px-2 btn-primary btn-corp" type="submit" name="filtro"><span class="fas fa-search font-size-12"></span></button>
                                        <a href="<?php echo $url_fichero; ?>?pagina=1&id=null<?php echo $parametros_add; ?>" class="btn py-2 px-2 btn-primary btn-corp"><span class="fas fa-sync-alt font-size-12"></span></a>
                                      </div>
                                    </div>
                                  </div>
                                </form>
                              </div>
                              <div class="col-md-7 mb-1 text-end">
                                  <a href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo base64_encode('Pendientes'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                                    <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes</span>
                                  </a>
                                  <a href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo base64_encode('Mes Actual'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Mes Actual">
                                    <i class="fas fa-calendar-alt btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Mes Actual</span>
                                  </a>
                                  <a href="correo?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
                                    <i class="fas fa-lock btn-icon-prepend me-0 font-size-12"></i><span class="d-none d-lg-inline"></span>
                                  </a>
                                <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                                  <?php if($bandeja=="Todos"): ?>
                                    <a href="correo_reparto?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo base64_encode($estado); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Reasignar Pendientes">
                                      <i class="fas fa-people-arrows btn-icon-prepend me-0 font-size-12"></i>
                                    </a>
                                  <?php else: ?>
                                    <a href="correo_reparto_individual?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo base64_encode($estado); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Reasignar Individual">
                                      <i class="fas fa-check-square btn-icon-prepend me-0 font-size-12"></i>
                                    </a>
                                  <?php endif; ?>
                                <?php endif; ?>
                                <?php if($bandeja=='Todos' AND ($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Supervisor")): ?>
                                  <a href="correo_estadisticas" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Estadísticas" target="_blank">
                                    <i class="fas fa-chart-pie btn-icon-prepend me-0 font-size-12"></i>
                                  </a>
                                  <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                                    <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                                  </button>
                                <?php endif; ?>
                              </div>
                              <div class="col-lg-12 mt-2">
                                <div class="table-responsive table-fixed" id="headerFixTable">
                                  <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                      <tr>
                                        <th class="px-1 py-2" style="width: 65px;"></th>
                                        <th class="px-1 py-2">Radicado</th>
                                        <th class="px-1 py-2">Radicado Entrada</th>
                                        <th class="px-1 py-2">Radicado Salida</th>
                                        <th class="px-1 py-2">Tipología</th>
                                        <th class="px-1 py-2">Estado</th>
                                        <th class="px-1 py-2">Gestión</th>
                                        <th class="px-1 py-2">Fecha Gestión</th>
                                        <th class="px-1 py-2">Asunto/Remitente</th>
                                        <th class="px-1 py-2">Fecha/Hora</th>
                                        <th class="px-1 py-2">Responsable</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                                      <tr>
                                        <td class="p-1 text-center">
                                          <?php if(($bandeja!="Todos" AND $resultado_registros[$i][6]==$_SESSION[APP_SESSION.'_session_usu_id'] AND ($resultado_registros[$i][9]=='Pendiente' OR $resultado_registros[$i][9]=='Finalizado' OR $resultado_registros[$i][9]=='En trámite')) OR (($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor") AND ($resultado_registros[$i][9]=='Pendiente' OR $resultado_registros[$i][9]=='Finalizado' OR $resultado_registros[$i][9]=='En trámite'))): ?>
                                            <a href="correo_editar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo base64_encode($estado); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                                          <?php endif; ?>
                                          <a href="#" onClick="open_modal_detalle('<?php echo base64_encode($resultado_registros[$i][0]); ?>');" class="btn btn-dark btn-icon px-1 py-1 mb-1" title="Imprimir"><i class="fas fa-print font-size-11"></i></a>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                          <div class="fw-bold"><?php echo $resultado_registros[$i][1]; ?></div>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                          <div class="fw-bold"><?php echo $resultado_registros[$i][2]; ?></div>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                          <div class="fw-bold"><?php echo $resultado_registros[$i][3]; ?></div>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                          <div class="alert alert-<?php echo $array_estado_alert[$resultado_registros[$i][9]]; ?> px-1 py-0 my-1"><?php echo $resultado_registros[$i][4]; ?></div>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                          <div class="alert alert-<?php echo $array_estado_alert[$resultado_registros[$i][9]]; ?> px-1 py-0 my-1"><?php echo $resultado_registros[$i][9]; ?></div>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                          <?php if($resultado_registros[$i][7]!=''): ?>
                                            <div class="alert alert-<?php echo $array_estado_alert[$resultado_registros[$i][9]]; ?> px-1 py-0 my-1"><?php echo $resultado_registros[$i][7]; ?></div>
                                            <?php if($resultado_registros[$i][8]!=''): ?>
                                              <div class="alert alert-<?php echo $array_estado_alert[$resultado_registros[$i][9]]; ?> px-1 py-0 my-1"><?php echo $resultado_registros[$i][8]; ?></div>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                        </td>
                                        <td class="p-1 font-size-11">
                                          <?php echo $resultado_registros[$i][10]; ?>
                                        </td>
                                        <td class="p-1 font-size-11">
                                          <b><?php echo $resultado_registros[$i][12]; ?></b><br><?php echo $resultado_registros[$i][11]; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                          <?php echo $resultado_registros[$i][13]; ?>
                                        </td>
                                        <td class="p-1 font-size-11">
                                          <?php echo $resultado_registros[$i][16]; ?>
                                        </td>
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
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- modal reportes -->
        <?php require_once('correo_reporte.php'); ?>
        <!-- modal -->
        <!-- MODAL DETALLE -->
        <div class="modal fade" id="modal-detalle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Histórico de gestión</h5>
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
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/super-build/ckeditor.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/super-build/translations/es.js"></script>
  <script type="text/javascript">
    function open_modal_detalle(id_registro) {
        var myModal = new bootstrap.Modal(document.getElementById("modal-detalle"), {});
        $('.modal-body-detalle').load('correo_historico_ver.php?reg='+id_registro,function(){
            myModal.show();
        });
    }
  </script>
</body>
</html>