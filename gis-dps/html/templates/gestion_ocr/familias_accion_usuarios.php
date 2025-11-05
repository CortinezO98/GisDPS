<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Usuarios";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Usuarios";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';
  unset($_SESSION[APP_SESSION.'_registro_creado_usuario_fa']);
  unset($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_fa']);

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
      $filtro_buscar="AND (`usu_id` LIKE ? OR `usu_acceso` LIKE ? OR `usu_nombres_apellidos` LIKE ? OR `usu_correo_corporativo` LIKE ? OR `usu_fecha_incorporacion` LIKE ? OR TCA.`ac_nombre_campania` LIKE ? OR `usu_usuario_red` LIKE ? OR `usu_cargo_rol` LIKE ? OR TS.`au_nombre_ubicacion` LIKE ? OR `usu_estado` LIKE ? OR TC.`ciu_departamento` LIKE ? OR TC.`ciu_municipio` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`usu_id`) FROM `administrador_usuario` LEFT JOIN `administrador_ciudades` AS TC ON `administrador_usuario`.`usu_ciudad`=TC.`ciu_codigo` LEFT JOIN `administrador_ubicacion` AS TS ON `administrador_usuario`.`usu_sede`=TS.`au_id` LEFT JOIN `administrador_campania` AS TCA ON `administrador_usuario`.`usu_campania`=TCA.`ac_id` WHERE 1=1 AND `usu_id`<>'1111111111' AND `usu_cargo_rol` LIKE '%AGENTE INSCRIPCIÓN FA CONSULTA%' ".$filtro_buscar."";
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

  $consulta_string="SELECT `usu_id`, `usu_acceso`, `usu_nombres_apellidos`, `usu_correo_corporativo`, `usu_fecha_incorporacion`, TCA.`ac_nombre_campania`, `usu_usuario_red`, `usu_cargo_rol`, TS.`au_nombre_ubicacion`, `usu_estado`, TC.`ciu_departamento`, TC.`ciu_municipio` FROM `administrador_usuario` LEFT JOIN `administrador_ciudades` AS TC ON `administrador_usuario`.`usu_ciudad`=TC.`ciu_codigo` LEFT JOIN `administrador_ubicacion` AS TS ON `administrador_usuario`.`usu_sede`=TS.`au_id` LEFT JOIN `administrador_campania` AS TCA ON `administrador_usuario`.`usu_campania`=TCA.`ac_id` WHERE 1=1 AND `usu_id`<>'1111111111' AND `usu_cargo_rol` LIKE '%AGENTE INSCRIPCIÓN FA CONSULTA%' ".$filtro_buscar." ORDER BY `usu_nombres_apellidos` LIMIT ?,?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_ciudad_usuario="SELECT DISTINCT `ocra_id`, `ocra_usuario`, `ocra_tipo`, `ocra_codigo`, TD.`ciu_departamento`, TM.`ciu_municipio` FROM `gestion_ocr_agentes` LEFT JOIN `administrador_ciudades_dane` AS TD ON `gestion_ocr_agentes`.`ocra_codigo`=TD.`ciu_cod_departamento` LEFT JOIN `administrador_ciudades_dane` AS TM ON `gestion_ocr_agentes`.`ocra_codigo`=TM.`ciu_cod_municipio`";
  $consulta_registros_ciudad_usuario = $enlace_db->prepare($consulta_string_ciudad_usuario);
  $consulta_registros_ciudad_usuario->execute();
  $resultado_registros_ciudad_usuario = $consulta_registros_ciudad_usuario->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_ciudad_usuario); $i++) { 
    if ($resultado_registros_ciudad_usuario[$i][2]=='Municipio') {
      $array_usuario_ciudad[$resultado_registros_ciudad_usuario[$i][1]][$resultado_registros_ciudad_usuario[$i][2]][]=$resultado_registros_ciudad_usuario[$i][5];
    } elseif ($resultado_registros_ciudad_usuario[$i][2]=='Departamento') {
      $array_usuario_ciudad[$resultado_registros_ciudad_usuario[$i][1]][$resultado_registros_ciudad_usuario[$i][2]][]=$resultado_registros_ciudad_usuario[$i][4];
    }
  }
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
              <a href="familias_accion_usuarios_crear_masivo?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Usuario Masivo">
                <i class="fas fa-upload btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Usuario Masivo</span>
              </a>
              <a href="familias_accion_usuarios_crear?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Usuario">
                <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Usuario</span>
              </a>
            </div>
            <div class="col-lg-12">
              <div class="table-responsive table-fixed" id="headerFixTable">
                <table class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th class="px-1 py-2" style="width: 65px;"></th>
                      <th class="px-1 py-2">Estado</th>
                      <th class="px-1 py-2">Doc. Identidad</th>
                      <th class="px-1 py-2">Nombres y Apellidos</th>
                      <th class="px-1 py-2">Usuario Acceso</th>
                      <th class="px-1 py-2">Correo Corporativo</th>
                      <th class="px-1 py-2">Cargo/Rol</th>
                      <th class="px-1 py-2">Departamento</th>
                      <th class="px-1 py-2">Municipio</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">
                          <a href="familias_accion_usuarios_editar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                      </td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][9]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][0]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][1]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][3]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][7]; ?></td>
                      <td class="p-1 font-size-11"><?php echo (isset($array_usuario_ciudad[$resultado_registros[$i][0]]['Departamento'])) ? implode('<br>', $array_usuario_ciudad[$resultado_registros[$i][0]]['Departamento']) : ''; ?></td>
                      <td class="p-1 font-size-11"><?php echo (isset($array_usuario_ciudad[$resultado_registros[$i][0]]['Municipio'])) ? implode('<br>', $array_usuario_ciudad[$resultado_registros[$i][0]]['Municipio']) : ''; ?></td>
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