<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Administrador";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  /*VARIABLES*/
  $title = "ADMINISTRADOR";
  $subtitle = "BUZONES";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  unset($_SESSION[APP_SESSION.'_registro_creado_buzon']);

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
      $filtro_buscar="AND (`ncr_host` LIKE ? OR `ncr_port` LIKE ? OR `ncr_smtpsecure` LIKE ? OR `ncr_smtpauth` LIKE ? OR `ncr_username` LIKE ? OR `ncr_password` LIKE ? OR `ncr_setfrom` LIKE ? OR `ncr_setfrom_name` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`ncr_id`) FROM `administrador_buzones` WHERE 1=1 ".$filtro_buscar."";
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

  $consulta_string="SELECT `ncr_id`, `ncr_host`, `ncr_port`, `ncr_smtpsecure`, `ncr_smtpauth`, `ncr_username`, `ncr_password`, `ncr_setfrom`, `ncr_setfrom_name`, `ncr_tipo` FROM `administrador_buzones` WHERE 1=1 ".$filtro_buscar." ORDER BY `ncr_username` ASC LIMIT ?,?";
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
              <a href="buzones_crear?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Buzón">
                <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Buzón</span>
              </a>
            </div>
            <div class="col-lg-12">
              <div class="table-responsive table-fixed" id="headerFixTable">
                <table class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th class="px-1 py-2" style="width: 65px;"></th>
                      <th class="px-1 py-2">Tipo</th>
                      <th class="px-1 py-2">Remitente</th>
                      <th class="px-1 py-2">Nombre</th>
                      <th class="px-1 py-2">Usuario</th>
                      <th class="px-1 py-2">Contraseña</th>
                      <th class="px-1 py-2">Host</th>
                      <th class="px-1 py-2">Puerto</th>
                      <th class="px-1 py-2">SMTP Auth</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">
                          <a href="buzones_editar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                          <a href="buzones_token.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-dark btn-icon px-1 py-1 mb-1" title="Token"><i class="fas fa-key font-size-11"></i></a>
                      </td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][9]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][7]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][8]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][5]; ?></td>
                      <td class="p-1 font-size-11">********</td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][1]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][3]; ?></td>
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