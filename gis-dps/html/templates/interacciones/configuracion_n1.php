<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Interacciones-Configuración";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Interacciones";
  $subtitle = "Configuración | Tipificación 1";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  unset($_SESSION[APP_SESSION.'_registro_creado_interacciones_configuracion']);
  unset($_SESSION[APP_SESSION.'_registro_eliminado_interacciones_configuracion']);

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
      $filtro_buscar="AND (`gic1_item` LIKE ? OR `gic1_estado` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`gic1_id`) FROM `gestion_interacciones_catnivel1` WHERE 1=1 AND `gic1_id`>0 ".$filtro_buscar."";

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

  $consulta_string="SELECT `gic1_id`, `gic1_item`, `gic1_estado`, `gic1_registro_usuario`, `gic1_registro_fecha` FROM `gestion_interacciones_catnivel1` WHERE 1=1 AND `gic1_id`>0 ".$filtro_buscar." ORDER BY `gic1_item` ASC LIMIT ?,?";

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
            <div class="col-md-5 mb-1">
              <?php require_once(ROOT.'includes/_search.php'); ?>
            </div>
            <div class="col-md-7 mb-1 text-end">
              <a href="configuracion?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Campos">
                <i class="fas fa-list btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Campos</span>
              </a>
              <a href="configuracion_n1?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Tipificación 1">
                <i class="fas fa-sitemap btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Tipificación 1</span>
              </a>
              <a href="configuracion_n2?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Tipificación 2">
                <i class="fas fa-sitemap btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Tipificación 2</span>
              </a>
              <a href="configuracion_n3?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Tipificación 3">
                <i class="fas fa-sitemap btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Tipificación 3</span>
              </a>
              <a href="configuracion_n1_crear?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Registro">
                <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear registro</span>
              </a>
            </div>
            <div class="col-lg-12">
              <div class="table-responsive table-fixed" id="headerFixTable">
                <table class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th class="px-1 py-2" style="width: 65px;"></th>
                      <th class="px-1 py-2">Tipificación 1</th>
                      <th class="px-1 py-2">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">
                          <a href="configuracion_n1_editar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                          <a href="configuracion_n1_eliminar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Eliminar"><i class="fas fa-trash-alt font-size-11"></i></a>
                      </td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][1]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
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
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>