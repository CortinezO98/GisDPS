<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-TMNC";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Transacciones Monetarias No Condicionadas | 6. Pendientes Clasificación | ".$bandeja;
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='&bandeja='.base64_encode($bandeja);

  unset($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']);

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
      $filtro_buscar="AND (`cetpc_pendiente_clasificacion` LIKE ? OR `cetpc_pendiente_clasificar` LIKE ? OR `cetpc_observaciones` LIKE ? OR `cetpc_notificar` LIKE ? OR `cetpc_registro_usuario` LIKE ? OR `cetpc_registro_fecha` LIKE ? OR TU.`usu_nombres_apellidos` LIKE ?)";

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
      $filtro_perfil="";
      // $filtro_perfil=" AND (TUA.`usu_supervisor`=? OR TMC.`gcm_analista`=?)";
      // array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Usuario"){
      $filtro_perfil=" AND `cetpc_registro_usuario`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  if($bandeja=="Hoy"){
      $filtro_bandeja=" AND (`cetpc_registro_fecha`>=?)";
      array_push($data_consulta, date('Y-m-d'));
  } elseif($bandeja=="Histórico"){
      $filtro_bandeja=" AND (`cetpc_registro_fecha`<?)";
      array_push($data_consulta, date('Y-m-d'));
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`cetpc_id`) FROM `gestion_cetmnc_pendiente_clasificacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_pendiente_clasificacion`.`cetpc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja."";

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

  $consulta_string="SELECT `cetpc_id`, `cetpc_pendiente_clasificacion`, `cetpc_pendiente_clasificar`, `cetpc_observaciones`, `cetpc_notificar`, `cetpc_registro_usuario`, `cetpc_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_pendiente_clasificacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_pendiente_clasificacion`.`cetpc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja." ORDER BY `cetpc_id` DESC LIMIT ?,?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only"  onresize="headerFixTable();" onload="headerFixTable();">
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
            <div class="col-md-3">
              <?php require_once('tmnc_menu.php'); ?>
            </div>
            <div class="col-md-9">
              <div class="row">
                <div class="col-md-5 mb-1">
                  <?php require_once(ROOT.'includes/_search.php'); ?>
                </div>
                <div class="col-md-7 mb-1 text-end">
                  <a href="tmnc_spendientes_clasificacion_crear?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Registro">
                    <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Registro</span>
                  </a>
                  <a href="tmnc_spendientes_clasificacion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Hoy'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Hoy">
                    <i class="fas fa-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Hoy</span>
                  </a>
                  <a href="tmnc_spendientes_clasificacion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
                    <i class="fas fa-history btn-icon-prepend me-0 font-size-12"></i><span class="d-none d-lg-inline"></span>
                  </a>
                    <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                      <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                    </button>
                </div>
                <div class="col-lg-12">
                  <div class="table-responsive table-fixed" id="headerFixTable">
                    <table class="table table-hover table-bordered table-striped">
                      <thead>
                        <tr>
                          <th class="px-1 py-2" style="width: 65px;"></th>
                          <th class="px-1 py-2">Pendientes por clasificar</th>
                          <th class="px-1 py-2">Observaciones</th>
                          <th class="px-1 py-2">Registrado por</th>
                          <th class="px-1 py-2">Fecha Registro</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                        <tr>
                          <td class="p-1 text-center">
                              
                          </td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][3]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][7]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][6]; ?></td>
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
        <!-- content-wrapper ends -->
        <!-- modal reportes -->
        <?php require_once('tmnc_spendientes_clasificacion_reporte.php'); ?>
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>