<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Gestión";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión OCR";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Familias en Acción-Gestión | ".$bandeja;
  $pagina=validar_input($_GET['pagina']);
  
  unset($_SESSION[APP_SESSION.'_registro_creado_familias_accion']);
  unset($_SESSION[APP_SESSION.'_registro_creado_familias_accion_asignar']);
  unset($_SESSION[APP_SESSION.'_registro_creado_reasignar_pendientes']);
  unset($_SESSION[APP_SESSION.'_registro_creado_reasignar_escalados']);
  // Inicializa variable tipo array
  $data_consulta=array();
  
  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_permanente=validar_input($_POST['id_filtro']);
      $filtro_estado_permanente=$_POST['id_estado'];
      if ($filtro_estado_permanente=='') {
          $filtro_estado_permanente=array();
      }
  } else {
      $filtro_permanente=validar_input($_GET['id']);
      $filtro_estado_permanente=validar_input($_GET['estado']);
      if ($filtro_estado_permanente!='null') {
          $filtro_estado_permanente=unserialize($_GET['estado']);
      } else {
          $filtro_estado_permanente=array();
      }
  }

  // Configuracón Paginación
  $registros_x_pagina=20;
  $iniciar_pagina=($pagina-1)*$registros_x_pagina;

  if ($permisos_usuario=="Administrador") {
      $filtro_perfil="";
  } elseif ($permisos_usuario=="Gestor") {
      $filtro_perfil="";
  } elseif($permisos_usuario=="Supervisor"){
      $filtro_perfil="";
  } elseif($permisos_usuario=="Cliente"){
      $filtro_perfil="";
  } elseif($permisos_usuario=="Usuario"){
      $filtro_perfil=" AND `ocrr_gestion_agente`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  // Valida que filtro se deba ejecutar
  if (isset($filtro_estado_permanente)) {
      if (count($filtro_estado_permanente)>0 AND $filtro_estado_permanente!="") {
          $estado=serialize($filtro_estado_permanente);
          $estado=urlencode($estado);

          $filtro_buscar_estado="";

          //Agregar catidad de variables a filtrar a data consulta
          for ($i=0; $i < count($filtro_estado_permanente); $i++) { 
              $filtro_buscar_estado.="`ocrr_gestion_estado`=? OR ";
              array_push($data_consulta, $filtro_estado_permanente[$i]);
          }

          $filtro_buscar_estado=" AND (".substr($filtro_buscar_estado, 0, -4).")";
      } else {
          $estado='null';
          $filtro_buscar_estado="";
      }
  } else {
      $estado='null';
      $filtro_buscar_estado="";
  }

  // Valida que filtro se deba ejecutar
  if ($filtro_permanente!="null" AND $filtro_permanente!="") {
      $filtro_buscar="AND (`ocrr_cod_familia`=? OR `ocrr_codbeneficiario`=? OR `ocrr_gestion_agente` LIKE ? OR `ocrr_gestion_estado` LIKE ? OR TOCR.`ocr_documento` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      array_push($data_consulta, $filtro_permanente);
      array_push($data_consulta, $filtro_permanente);
      for ($i=2; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  if ($filtro_buscar_estado=="") {
      if($bandeja=="Pendientes"){
          if($permisos_usuario=="Visitante"){
              $filtro_bandeja=" AND (`ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=?)";
              array_push($data_consulta, 'Intento Contacto-Fallido');
              array_push($data_consulta, 'Intento Contacto-Agotado');
              array_push($data_consulta, 'Contactado-Pendiente Documentos');
              // Segunda Fase
              array_push($data_consulta, 'Intento Contacto-Fallido-Segunda Revisión');
              array_push($data_consulta, 'Intento Contacto-Agotado-Segunda Revisión');
              array_push($data_consulta, 'Contactado-Pendiente Documentos-Segunda Revisión');
          } else {
            $filtro_bandeja=" AND (`ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=?)";
            array_push($data_consulta, 'Aplazado');
            array_push($data_consulta, 'Pendiente llamada');
            array_push($data_consulta, 'Intento Contacto-Fallido');
            // array_push($data_consulta, 'Nuevo Contacto-Error Subsanación');
            array_push($data_consulta, 'Intento Contacto-Agotado');
            // Segunda Fase
            array_push($data_consulta, 'Aplazado Segunda Revisión');
            array_push($data_consulta, 'Pendiente llamada-Segunda Revisión');
            array_push($data_consulta, 'Intento Contacto-Fallido-Segunda Revisión');
            // array_push($data_consulta, 'Nuevo Contacto-Error Subsanación-Segunda Revisión');
            array_push($data_consulta, 'Intento Contacto-Agotado-Segunda Revisión');
            array_push($data_consulta, 'Documentos Cargados-Segunda Revisión');
            array_push($data_consulta, 'Documento pesado');
          }
      } elseif($bandeja=="Revalidación"){
        if($permisos_usuario=="Visitante"){
          $filtro_bandeja=" AND (`ocrr_gestion_estado`=?)";
          array_push($data_consulta, 'Documentos Cargados');
        } else {
          $filtro_bandeja=" AND (`ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=?)";
          array_push($data_consulta, 'Documentos Cargados');
          array_push($data_consulta, 'Contactado-Pendiente Documentos');
          array_push($data_consulta, 'Segunda Revisión OCR');
          //Segunda Fase
          array_push($data_consulta, 'Contactado-Pendiente Documentos-Segunda Revisión');
        }
      } elseif($bandeja=="Escalados"){
          $filtro_bandeja=" AND (`ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=?)";
          array_push($data_consulta, 'Escalado-Validar');
          array_push($data_consulta, 'Escalado-Cliente');
          //Segunda Fase
          array_push($data_consulta, 'Escalado-Validar-Segunda Revisión');
          array_push($data_consulta, 'Escalado-Cliente-Segunda Revisión');
          array_push($data_consulta, 'Aplazado Tercera Revisión');
      } elseif($bandeja=="Cerrados"){
          $filtro_bandeja=" AND (`ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=?)";
          array_push($data_consulta, 'Validado-OCR');
          array_push($data_consulta, 'Validado-Agente');
          array_push($data_consulta, 'Inscrito SIFA');
          array_push($data_consulta, 'Inscrito SIFA RPA');
          // Segunda Fase
          array_push($data_consulta, 'Validado-OCR-Segunda Revisión');
          array_push($data_consulta, 'Validado-Agente-Segunda Revisión');
          array_push($data_consulta, 'Validado-Agente-Tercera Revisión');
      }
  } else {
      $filtro_bandeja="";
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`ocrr_id`) FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` WHERE 1=1 ".$filtro_perfil." ".$filtro_buscar_estado." ".$filtro_buscar." ".$filtro_bandeja."";

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

  $consulta_string="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_observaciones`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TAG.`usu_nombres_apellidos`, `ocrr_gestion_fecha`, `ocrr_gestion_llamada_tipificacion`, `ocrr_gestion_llamada_id` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` WHERE 1=1 ".$filtro_perfil." ".$filtro_buscar_estado." ".$filtro_buscar." ".$filtro_bandeja." ORDER BY `ocrr_gestion_estado` ASC, `ocrr_cod_familia` ASC LIMIT ?,?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $parametros_add='&bandeja='.base64_encode($bandeja).'&estado='.$estado;
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
            <div class="col-md-5 mb-1">
              <form name="filtrado" action="" method="POST">
                <div class="form-group m-0">
                  <div class="input-group">
                    <select class="selectpicker form-control form-control-sm form-select" name="id_estado[]" id="id_estado" multiple title="Estado">
                        <?php if($bandeja=="Pendientes"): ?>
                          <?php if($permisos_usuario!="Visitante"): ?>
                            <option value="Aplazado" <?php if(in_array("Aplazado", $filtro_estado_permanente)){ echo "selected"; } ?>>Aplazado</option>
                            <option value="Aplazado Segunda Revisión" <?php if(in_array("Aplazado Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Aplazado Segunda Revisión</option>
                            <option value="Pendiente llamada" <?php if(in_array("Pendiente llamada", $filtro_estado_permanente)){ echo "selected"; } ?>>Pendiente llamada</option>
                            <option value="Pendiente llamada-Segunda Revisión" <?php if(in_array("Pendiente llamada-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Pendiente llamada-Segunda Revisión</option>
                          <?php endif; ?>
                            <option value="Intento Contacto-Fallido" <?php if(in_array("Intento Contacto-Fallido", $filtro_estado_permanente)){ echo "selected"; } ?>>Intento Contacto-Fallido</option>
                            <option value="Intento Contacto-Fallido-Segunda Revisión" <?php if(in_array("Intento Contacto-Fallido-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Intento Contacto-Fallido-Segunda Revisión</option>
                          <?php if($permisos_usuario!="Visitante"): ?>
                            <!-- <option value="Nuevo Contacto-Error Subsanación" <?php if(in_array("Nuevo Contacto-Error Subsanación", $filtro_estado_permanente)){ echo "selected"; } ?>>Nuevo Contacto-Error Subsanación</option> -->
                          <?php endif; ?>
                            <option value="Intento Contacto-Agotado" <?php if(in_array("Intento Contacto-Agotado", $filtro_estado_permanente)){ echo "selected"; } ?>>Intento Contacto-Agotado</option>
                            <option value="Intento Contacto-Agotado-Segunda Revisión" <?php if(in_array("Intento Contacto-Agotado-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Intento Contacto-Agotado-Segunda Revisión</option>
                          <?php if($permisos_usuario!="Visitante"): ?>
                            <option value="Documentos Cargados-Segunda Revisión" <?php if(in_array("Documentos Cargados-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Documentos Cargados-Segunda Revisión</option>
                            <option value="Documento pesado" <?php if(in_array("Documento pesado", $filtro_estado_permanente)){ echo "selected"; } ?>>Documento pesado</option>
                          <?php endif; ?>
                        <?php elseif ($bandeja=="Revalidación"): ?>
                          <option value="Contactado-Pendiente Documentos" <?php if(in_array("Contactado-Pendiente Documentos", $filtro_estado_permanente)){ echo "selected"; } ?>>Contactado-Pendiente Documentos</option>
                          <option value="Contactado-Pendiente Documentos-Segunda Revisión" <?php if(in_array("Contactado-Pendiente Documentos-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Contactado-Pendiente Documentos-Segunda Revisión</option>
                          <?php if($permisos_usuario!="Visitante"): ?>
                            <option value="Documentos Cargados" <?php if(in_array("Documentos Cargados", $filtro_estado_permanente)){ echo "selected"; } ?>>Documentos Cargados</option>
                            <option value="Segunda Revisión OCR" <?php if(in_array("Segunda Revisión OCR", $filtro_estado_permanente)){ echo "selected"; } ?>>Segunda Revisión OCR</option>
                          <?php endif; ?>
                        <?php elseif ($bandeja=="Escalados"): ?>
                          <option value="Aplazado Tercera Revisión" <?php if(in_array("Aplazado Tercera Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Aplazado Tercera Revisión</option>
                          <option value="Escalado-Validar" <?php if(in_array("Escalado-Validar", $filtro_estado_permanente)){ echo "selected"; } ?>>Escalado-Validar</option>
                          <option value="Escalado-Validar-Segunda Revisión" <?php if(in_array("Escalado-Validar-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Escalado-Validar-Segunda Revisión</option>
                          <option value="Escalado-Cliente" <?php if(in_array("Escalado-Cliente", $filtro_estado_permanente)){ echo "selected"; } ?>>Escalado-Cliente</option>
                          <option value="Escalado-Cliente-Segunda Revisión" <?php if(in_array("Escalado-Cliente-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Escalado-Cliente-Segunda Revisión</option>
                        <?php elseif ($bandeja=="Cerrados"): ?>
                            <option value="Validado-OCR" <?php if(in_array("Validado-OCR", $filtro_estado_permanente)){ echo "selected"; } ?>>Validado-OCR</option>
                            <option value="Validado-OCR-Segunda Revisión" <?php if(in_array("Validado-OCR-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Validado-OCR-Segunda Revisión</option>
                            <option value="Validado-Agente" <?php if(in_array("Validado-Agente", $filtro_estado_permanente)){ echo "selected"; } ?>>Validado-Agente</option>
                            <option value="Validado-Agente-Segunda Revisión" <?php if(in_array("Validado-Agente-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Validado-Agente-Segunda Revisión</option>
                            <option value="Validado-Agente-Tercera Revisión" <?php if(in_array("Validado-Agente-Tercera Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Validado-Agente-Tercera Revisión</option>
                            <option value="Inscrito SIFA" <?php if(in_array("Inscrito SIFA", $filtro_estado_permanente)){ echo "selected"; } ?>>Inscrito SIFA</option>
                            <option value="Inscrito SIFA RPA" <?php if(in_array("Inscrito SIFA RPA", $filtro_estado_permanente)){ echo "selected"; } ?>>Inscrito SIFA RPA</option>
                        <?php endif; ?>
                    </select>
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
              <a href="familias_accion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Pendientes'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes</span>
              </a>
                <a href="familias_accion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Revalidación'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Revalidación">
                  <i class="fas fa-retweet btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Revalidación</span>
                </a>
              <?php if($permisos_usuario!="Visitante"): ?>
                <a href="familias_accion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Escalados'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Escalados">
                  <i class="fas fa-layer-group btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Escalados</span>
                </a>
                <a href="familias_accion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Cerrados'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Cerrados">
                  <i class="fas fa-lock btn-icon-prepend me-0 font-size-12"></i><span class="d-none d-lg-inline"></span>
                </a>
              <?php endif; ?>
              <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Supervisor"): ?>
                <?php if($bandeja=="Pendientes"): ?>
                  <a href="familias_accion_asignar_pendientes?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo $estado; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Reasignar Pendientes">
                    <i class="fas fa-people-arrows btn-icon-prepend me-0 font-size-12"></i>
                  </a>
                <?php endif; ?>
                <?php if($bandeja=="Escalados"): ?>
                  <a href="familias_accion_asignar_escalados?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo $estado; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Reasignar Escalados">
                    <i class="fas fa-people-arrows btn-icon-prepend me-0 font-size-12"></i>
                  </a>
                <?php endif; ?>
                <a href="familias_accion_sms?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Notificaciones SMS">
                  <i class="fas fa-sms btn-icon-prepend me-0 font-size-12"></i>
                </a>
                <a href="familias_accion_procesado?pagina=1&id=null&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Procesado">
                  <i class="fas fa-cogs btn-icon-prepend me-0 font-size-12"></i>
                </a>
                <a href="familias_accion_consolidado?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Consolidado">
                  <i class="fas fa-database btn-icon-prepend me-0 font-size-12"></i>
                </a>
                <a href="familias_accion_estadisticas" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Estadísticas">
                  <i class="fas fa-chart-pie btn-icon-prepend me-0 font-size-12"></i>
                </a>
              <?php endif; ?>
              <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Supervisor"): ?>
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
                      <th class="px-1 py-2" style="width: 60px;"></th>
                      <th class="px-1 py-2">Estado Gestión</th>
                      <th class="px-1 py-2">Intentos</th>
                      <th class="px-1 py-2">Cód. Familia</th>
                      <th class="px-1 py-2">Cód. Beneficiario</th>
                      <th class="px-1 py-2">Cabeza Familia</th>
                      <th class="px-1 py-2">Primer Nombre</th>
                      <th class="px-1 py-2">Segundo Nombre</th>
                      <th class="px-1 py-2">Primer Apellido</th>
                      <th class="px-1 py-2">Segundo Apellido</th>
                      <th class="px-1 py-2">Documento</th>
                      <th class="px-1 py-2">Fecha Nacimiento</th>
                      <th class="px-1 py-2">Género</th>
                      <th class="px-1 py-2">Fecha Expedición</th>
                      <th class="px-1 py-2">Responsable</th>
                      <th class="px-1 py-2">Observaciones</th>
                      <th class="px-1 py-2">Notificación</th>
                      <th class="px-1 py-2">Tipificación</th>
                      <th class="px-1 py-2">Fecha Gestión</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">
                          <a href="#" onClick="open_modal_detalle('<?php echo base64_encode($resultado_registros[$i][0]); ?>');" class="btn btn-dark btn-icon px-1 py-1 mb-1" title="Detalle"><i class="fas fa-file-alt font-size-11"></i></a>
                          <?php if(($permisos_usuario=="Usuario" AND ($resultado_registros[$i][6]=='Aplazado' OR $resultado_registros[$i][6]=='Intento Contacto-Fallido' OR $resultado_registros[$i][6]=='Nuevo Contacto-Error Subsanación' OR $resultado_registros[$i][6]=='Pendiente llamada' OR $resultado_registros[$i][6]=='Aplazado Segunda Revisión' OR $resultado_registros[$i][6]=='Intento Contacto-Fallido-Segunda Revisión' OR $resultado_registros[$i][6]=='Nuevo Contacto-Error Subsanación-Segunda Revisión' OR $resultado_registros[$i][6]=='Pendiente llamada-Segunda Revisión' OR $resultado_registros[$i][6]=='Documentos Cargados-Segunda Revisión')) OR ($permisos_usuario=="Supervisor" AND ($resultado_registros[$i][6]=='Escalado-Validar' OR $resultado_registros[$i][6]=='Escalado-Cliente' OR $resultado_registros[$i][6]=='Escalado-Validar-Segunda Revisión' OR $resultado_registros[$i][6]=='Escalado-Cliente-Segunda Revisión' OR $resultado_registros[$i][6]=='Documentos Cargados-Segunda Revisión')) OR (($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor") AND $resultado_registros[$i][6]!='Segunda Revisión OCR')): ?>
                            <a href="familias_accion_editar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo $estado; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                          <?php endif; ?>

                          <?php if($resultado_registros[$i][6]=='Documentos Cargados' OR $resultado_registros[$i][6]=='Aplazado Segunda Revisión' OR $resultado_registros[$i][6]=='Segunda Revisión OCR' OR $resultado_registros[$i][6]=='Validado-OCR-Segunda Revisión'): ?>
                            <a href="storage_adjuntos/<?php echo $resultado_registros[$i][1]; ?>.pdf" class="btn btn-success btn-icon px-1 py-1 mb-1" title="Documento" target="_blank"><i class="fas fa-file-pdf font-size-11"></i></a>
                          <?php endif; ?>
                          <?php if($resultado_registros[$i][6]=='Documentos Cargados-Segunda Revisión' OR $resultado_registros[$i][6]=='Aplazado Tercera Revisión' OR $resultado_registros[$i][6]=='Validado-Agente-Tercera Revisión'): ?>
                            <a href="storage_adjuntos/TR-<?php echo $resultado_registros[$i][1]; ?>.pdf" class="btn btn-success btn-icon px-1 py-1 mb-1" title="Documento" target="_blank"><i class="fas fa-file-pdf font-size-11"></i></a>
                          <?php endif; ?>
                          <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                            <a href="familias_accion_asignar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&estado=<?php echo $estado; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Asignar"><i class="fas fa-retweet font-size-11"></i></a>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][6]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][7]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][1]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][2]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][3]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][14]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][15]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][16]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][17]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][18]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][19]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][20]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][21]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][22]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][8]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][9]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][24]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][23]; ?></td>
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
        <?php require_once('familias_accion_reporte.php'); ?>
        <!-- modal -->
        <!-- MODAL DETALLE -->
        <div class="modal fade" id="modal-detalle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Detalle</h5>
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
        $('.modal-body-detalle').load('familias_accion_ver.php?reg='+id_registro,function(){
            myModal.show();
        });
    }
  </script>
</body>
</html>