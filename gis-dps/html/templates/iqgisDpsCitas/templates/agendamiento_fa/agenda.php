<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas-Agenda";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas";
  $subtitle = "Agenda";
  $pagina=validar_input($_GET['pagina']);
  
  unset($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']);
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
      $filtro_buscar="AND (`gca_usuario` LIKE ? OR `gca_semana` LIKE ? OR `gca_fecha` LIKE ? OR `gca_hora` LIKE ? OR `gca_estado` LIKE ? OR `gca_estado_agenda` LIKE ? OR TP.`gcpa_punto_atencion` LIKE ? OR TP.`gcpa_direccion` LIKE ? OR TU.`usu_nombres_apellidos` LIKE ? OR TC.`ciu_departamento` LIKE ? OR TC.`ciu_municipio` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`gca_id`) FROM `gestion_citasfa_agenda` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_agenda`.`gca_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_agenda`.`gca_usuario`=TU.`usu_id` LEFT JOIN `administrador_ciudades` AS TC ON TP.`gcpa_municipio`=TC.`ciu_codigo` WHERE 1=1 ".$filtro_buscar."";

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

  $consulta_string="SELECT `gca_id`, `gca_punto`, `gca_usuario`, `gca_semana`, `gca_fecha`, `gca_hora`, `gca_estado`, `gca_estado_agenda`, TP.`gcpa_regional`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`ciu_departamento`, TC.`ciu_municipio` FROM `gestion_citasfa_agenda` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_agenda`.`gca_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_agenda`.`gca_usuario`=TU.`usu_id` LEFT JOIN `administrador_ciudades` AS TC ON TP.`gcpa_municipio`=TC.`ciu_codigo` WHERE 1=1 ".$filtro_buscar." ORDER BY TC.`ciu_departamento`, TC.`ciu_municipio`, TP.`gcpa_punto_atencion` LIMIT ?,?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $parametros_add='';
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
              <!-- <a href="agenda_crear?pagina=1&id=<?php echo $filtro_permanente; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Escalados">
                <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear registro</span>
              </a> -->
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
                      <th class="px-1 py-2" style="width: 55px;"></th>
                      <th class="px-1 py-2">Estado</th>
                      <th class="px-1 py-2">Estado Agendamiento</th>
                      <th class="px-1 py-2">Regional</th>
                      <th class="px-1 py-2">Departamento</th>
                      <th class="px-1 py-2">Municipio</th>
                      <th class="px-1 py-2">Punto Atención</th>
                      <th class="px-1 py-2">Dirección</th>
                      <th class="px-1 py-2">Fecha</th>
                      <th class="px-1 py-2">Hora</th>
                      <th class="px-1 py-2">Asesor</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">
                          <!-- <a href="punto_atencion_editar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                          <a href="punto_atencion_configurar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-dark btn-icon px-1 py-1 mb-1" title="Configurar"><i class="fas fa-cog font-size-11"></i></a> -->
                      </td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][6]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][7]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][8]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][12]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][13]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][9]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][10]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][4]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][5]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][11]; ?></td>
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
        <?php //require_once('familias_accion_reporte.php'); ?>
        <!-- modal -->
        
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>