<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Gestión";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Gestión | Consolidado";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';
  
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
      $filtro_buscar="AND (`ocr_codfamilia` LIKE ? OR `ocr_codbeneficiario` LIKE ? OR `ocr_cabezadefamilia` LIKE ? OR `ocr_primernombre` LIKE ? OR `ocr_segundonombre` LIKE ? OR `ocr_primerapellido` LIKE ? OR `ocr_segundoapellido` LIKE ? OR `ocr_documento` LIKE ? OR `ocr_fechanacimiento` LIKE ? OR `ocr_genero` LIKE ? OR `ocr_fechaexpedicion` LIKE ? OR `ocr_estado` LIKE ? OR `ocr_observaciones` LIKE ? OR `ocr_resultadoocr_estado` LIKE ? OR `ocr_resultadoocr_fecha` LIKE ? OR `ocr_resultadoocr_observaciones` LIKE ? OR `ocr_consolida_estado` LIKE ? OR `ocr_consolida_fecha` LIKE ? OR `ocr_registro_fecha` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`ocr_id`) FROM `gestion_ocr` WHERE 1=1 ".$filtro_buscar."";

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

  $consulta_string="SELECT `ocr_id`, `ocr_codfamilia`, `ocr_codbeneficiario`, `ocr_cabezadefamilia`, `ocr_primernombre`, `ocr_segundonombre`, `ocr_primerapellido`, `ocr_segundoapellido`, `ocr_documento`, `ocr_fechanacimiento`, `ocr_genero`, `ocr_fechaexpedicion`, `ocr_estado`, `ocr_observaciones`, `ocr_resultadoocr_estado`, `ocr_resultadoocr_fecha`, `ocr_resultadoocr_observaciones`, `ocr_consolida_estado`, `ocr_consolida_fecha`, `ocr_registro_fecha` FROM `gestion_ocr` WHERE 1=1 ".$filtro_buscar." ORDER BY `ocr_codfamilia` ASC LIMIT ?,?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
      
  }
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
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
              <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Cliente"): ?>
                <!-- <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                  <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                </button> -->
              <?php endif; ?>
            </div>
            <div class="col-lg-12">
              <div class="table-responsive table-fixed" id="headerFixTable">
                <table class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
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
                      <th class="px-1 py-2">Estado OCR</th>
                      <th class="px-1 py-2">Fecha Estado OCR</th>
                      <th class="px-1 py-2">Observaciones</th>
                      <th class="px-1 py-2">Estado Consolidado</th>
                      <th class="px-1 py-2">Fecha Estado Consolidado</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][1]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][2]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][3]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][5]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][6]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][7]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][8]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][9]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][10]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][11]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][14]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][15]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][16]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][17]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][18]; ?></td>
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
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>