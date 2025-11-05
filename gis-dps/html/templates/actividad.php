<?php
    require_once("../iniciador_index.php");
    require_once("../security_session.php");
    /*VARIABLES*/
    $title = "Administrador";
    $subtitle = "Actividad";
    $pagina=validar_input($_GET['pagina']);
    $parametros_add='';

    // Inicializa variable tipo array
    $data_consulta=array();
    array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
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
        $filtro_buscar="AND (`clog_log_modulo` LIKE ? OR `clog_log_tipo` LIKE ? OR `clog_log_accion` LIKE ? OR `clog_log_detalle` LIKE ? OR `clog_user_agent` LIKE ? OR `clog_remote_addr` LIKE ? OR `clog_remote_host` LIKE ? OR `clog_script` LIKE ? OR `clog_registro_usuario` LIKE ? OR `clog_registro_fecha` LIKE ? OR TU.`usu_nombres_apellidos` LIKE ?)";

        //Contar catidad de variables a filtrar
        $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

        //Agregar catidad de variables a filtrar a data consulta
        for ($i=0; $i < $cantidad_filtros; $i++) { 
            array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
        }
    }

    // Prepara string a ejecutar en sentencia preparada
    $consulta_contar_string="SELECT COUNT(`clog_id`) FROM `administrador_log` LEFT JOIN `administrador_usuario` AS TU ON `administrador_log`.`clog_registro_usuario`=TU.`usu_id` WHERE `clog_registro_usuario`=? ".$filtro_buscar."";
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

    $consulta_string="SELECT `clog_id`, `clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_user_agent`, `clog_remote_addr`, `clog_remote_host`, `clog_script`, `clog_registro_usuario`, `clog_registro_fecha`, TU.`usu_nombres_apellidos` FROM `administrador_log` LEFT JOIN `administrador_usuario` AS TU ON `administrador_log`.`clog_registro_usuario`=TU.`usu_id` WHERE `clog_registro_usuario`=? ".$filtro_buscar." ORDER BY `clog_registro_fecha` DESC LIMIT ?,?";
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
          <div class="row">
            <div class="col-md-3 mb-1">
              <?php require_once(ROOT.'includes/_search.php'); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="row justify-content-center">
                <div class="col-lg-12 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-md-6 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="card-title card-title-dash">Actividad</h4>
                          </div>
                          <ul class="bullet-line-list">
                            <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                            <li>
                              <div class="d-flex justify-content-between">
                                <div>
                                  <span class="text-light-green"><?php echo log_icono($resultado_registros[$i][2]); ?></span> <?php echo $resultado_registros[$i][1].' | '.$resultado_registros[$i][3]; ?>
                                  <?php
                                      $detalle_log=" | ".str_replace("]", "", str_replace(" [", ": ", str_replace(" | ", " <b>|</b> ", $resultado_registros[$i][4])));
                                      echo $detalle_log;
                                  ?>
                                </div>
                                <p style="min-width: 100px;"><?php echo date('H:i d/m', strtotime($resultado_registros[$i][10])); ?></p>
                              </div>
                            </li>
                            <?php endfor; ?>
                          </ul>
                          <div class="list align-items-center pt-3">
                            <div class="wrapper w-100">
                              
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php require_once(ROOT.'includes/_pagination-footer.php'); ?>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- footer -->
        <?php require_once(ROOT.'includes/_footer.php'); ?>
        <!-- footer -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>