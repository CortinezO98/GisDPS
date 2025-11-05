<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Monitoreos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Calidad";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Transacciones";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  unset($_SESSION[APP_SESSION.'_registro_cargue_base']);

  // Inicializa variable tipo array
  $data_consulta=array();

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
      $filtro_buscar="AND (`gcmt_id` LIKE ? OR `gcmt_campo_1` LIKE ? OR `gcmt_campo_2` LIKE ? OR `gcmt_campo_3` LIKE ? OR `gcmt_campo_4` LIKE ? OR `gcmt_campo_5` LIKE ? OR `gcmt_campo_6` LIKE ? OR `gcmt_campo_7` LIKE ? OR `gcmt_campo_8` LIKE ? OR `gcmt_campo_9` LIKE ? OR `gcmt_campo_10` LIKE ? OR `gcmt_estado` LIKE ? OR `gcmt_registro_fecha` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`gcmt_id`) FROM `gestion_calidad_monitoreo_transacciones` WHERE 1=1 ".$filtro_buscar."";

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

  $consulta_string="SELECT `gcmt_id`, `gcmt_campo_1`, `gcmt_campo_2`, `gcmt_campo_3`, `gcmt_campo_4`, `gcmt_campo_5`, `gcmt_campo_6`, `gcmt_campo_7`, `gcmt_campo_8`, `gcmt_campo_9`, `gcmt_campo_10`, `gcmt_estado`, `gcmt_base`, `gcmt_registro_fecha` FROM `gestion_calidad_monitoreo_transacciones` WHERE 1=1 ".$filtro_buscar." ORDER BY `gcmt_registro_fecha` DESC LIMIT ?,?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

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
            <div class="col-md-3 mb-1">
              <?php require_once(ROOT.'includes/_search.php'); ?>
            </div>
            <div class="col-md-9 mb-1 text-end">
              <a href="monitoreos?pagina=1&id=null&bandeja=<?php echo base64_encode('Pendientes'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes <?php echo ($resultado_registros_conteo_pendientes[0][0]>0) ? "<div class='float-end alert_conteo_menu ms-1'>".$resultado_registros_conteo_pendientes[0][0]."</div>" : ""; ?></span>
              </a>
              <a href="monitoreos?pagina=1&id=null&bandeja=<?php echo base64_encode('Refutados'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Refutados">
                <i class="fas fa-user-times btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Refutados <?php echo ($resultado_registros_conteo_refutado[0][0]>0) ? "<div class='float-end alert_conteo_menu ms-1'>".$resultado_registros_conteo_refutado[0][0]."</div>" : ""; ?></span>
              </a>
              <a href="monitoreos?pagina=1&id=null&bandeja=<?php echo base64_encode('Mes Actual'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Mes Actual">
                <i class="fas fa-calendar-alt btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Mes Actual</span>
              </a>
              <a href="monitoreos?pagina=1&id=null&bandeja=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
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
                      <th class="px-1 py-2">Id</th>
                      <th class="px-1 py-2">Base</th>
                      <th class="px-1 py-2">Estado</th>
                      <th class="px-1 py-2">Transacción</th>
                      <th class="px-1 py-2">Campaña</th>
                      <th class="px-1 py-2">Fecha</th>
                      <th class="px-1 py-2">Agente</th>
                      <th class="px-1 py-2">Campo 5</th>
                      <th class="px-1 py-2">Campo 6</th>
                      <th class="px-1 py-2">Campo 7</th>
                      <th class="px-1 py-2">Campo 8</th>
                      <th class="px-1 py-2">Campo 9</th>
                      <th class="px-1 py-2">Campo 10</th>
                      <th class="px-1 py-2">Fecha Registro</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">

                      </td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][0]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][12]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][11]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][1]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][3]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][5]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][6]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][7]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][8]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][9]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][10]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][13]; ?></td>
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
        <?php require_once('interacciones_reporte.php'); ?>
        <!-- modal -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>