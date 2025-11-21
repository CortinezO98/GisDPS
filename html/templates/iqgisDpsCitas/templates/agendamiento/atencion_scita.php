<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Atención sin Cita | ".$bandeja;
  $pagina=validar_input($_GET['pagina']);
  unset($_SESSION[APP_SESSION.'_registro_creado_atencion_scita']);

  // Inicializa variable tipo array
  $data_consulta=array();
  $data_consulta_usuarios=array();
  $data_consulta_puntos=array();
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
      $filtro_buscar="AND (`gcasc_punto` LIKE ? OR `gcasc_datos_tipo_documento` LIKE ? OR `gcasc_datos_numero_identificacion` LIKE ? OR `gcasc_datos_nombres` LIKE ? OR `gcasc_datos_correo` LIKE ? OR `gcasc_datos_celular` LIKE ? OR `gcasc_datos_fijo` LIKE ? OR `gcasc_datos_autoriza` LIKE ? OR `gcasc_observaciones` LIKE ? OR `gcasc_atencion_usuario` LIKE ? OR `gcasc_atencion_fecha` LIKE ? OR `gcasc_radicado` LIKE ? OR `gcasc_atencion_preferencial` LIKE ? OR `gcasc_informacion_poblacional` LIKE ? OR `gcasc_genero` LIKE ? OR `gcasc_nivel_escolaridad` LIKE ? OR `gcasc_envio_encuesta` LIKE ? OR `gcasc_celular` LIKE ? OR `gcasc_estado` LIKE ? OR `gcasc_registro_fecha` LIKE ? OR TU.`usu_nombres_apellidos` LIKE ? OR TP.`gcpa_punto_atencion` LIKE ?)";

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
      $filtro_perfil=" AND TU.`usu_supervisor`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Supervisor"){
      $filtro_perfil=" AND TU.`usu_supervisor`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Usuario"){
      $filtro_perfil=" AND `gcasc_atencion_usuario`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  if($bandeja=="Hoy"){
      $filtro_bandeja=" AND (`gcasc_registro_fecha`>=?)";
      array_push($data_consulta, date('Y-m-d'));
  } elseif($bandeja=="Histórico"){
      $filtro_bandeja=" AND (`gcasc_registro_fecha`<?)";
      array_push($data_consulta, date('Y-m-d'));
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`gcasc_id`) FROM `gestion_citas_atencion_scita` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_atencion_scita`.`gcasc_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_atencion_scita`.`gcasc_atencion_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja."";

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

  $consulta_string="SELECT `gcasc_id`, `gcasc_punto`, `gcasc_datos_tipo_documento`, `gcasc_datos_numero_identificacion`, `gcasc_datos_nombres`, `gcasc_datos_correo`, `gcasc_datos_celular`, `gcasc_datos_fijo`, `gcasc_datos_autoriza`, `gcasc_observaciones`, `gcasc_atencion_usuario`, `gcasc_atencion_fecha`, `gcasc_radicado`, `gcasc_atencion_preferencial`, `gcasc_informacion_poblacional`, `gcasc_genero`, `gcasc_nivel_escolaridad`, `gcasc_envio_encuesta`, `gcasc_celular`, `gcasc_estado`, `gcasc_registro_fecha`, TU.`usu_nombres_apellidos`, TP.`gcpa_punto_atencion` FROM `gestion_citas_atencion_scita` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_atencion_scita`.`gcasc_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_atencion_scita`.`gcasc_atencion_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja." ORDER BY `gcasc_registro_fecha` DESC LIMIT ?,?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  if ($permisos_usuario=="Administrador") {
      $filtro_usuarios="";
      $filtro_puntos="";
  } elseif ($permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador") {
      $filtro_usuarios=" AND TU.`usu_supervisor`=?";
      array_push($data_consulta_usuarios, $_SESSION[APP_SESSION.'_session_usu_id']);
      $filtro_puntos=" AND TU.`usu_supervisor`=?";
      array_push($data_consulta_puntos, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Supervisor"){
      $filtro_usuarios=" AND TU.`usu_supervisor`=?";
      array_push($data_consulta_usuarios, $_SESSION[APP_SESSION.'_session_usu_id']);
      $filtro_puntos=" AND TU.`usu_supervisor`=?";
      array_push($data_consulta_puntos, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Usuario"){
      $filtro_usuarios=" AND `gcpau_usuario`=?";
      array_push($data_consulta_usuarios, $_SESSION[APP_SESSION.'_session_usu_id']);
      $filtro_puntos=" AND `gcpau_usuario`=?";
      array_push($data_consulta_puntos, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  $consulta_string_usuarios="SELECT `gcpau_usuario`, TU.`usu_nombres_apellidos` FROM `gestion_citas_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_usuarios."";
  $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
  if (count($data_consulta_usuarios)>0) {
      $consulta_registros_usuarios->bind_param(str_repeat("s", count($data_consulta_usuarios)), ...$data_consulta_usuarios);
  }
  $consulta_registros_usuarios->execute();
  $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_puntos="SELECT DISTINCT `gcpau_punto_atencion`, TP.`gcpa_punto_atencion` FROM `gestion_citas_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_punto_atencion_usuario`.`gcpau_punto_atencion`=TP.`gcpa_id` WHERE 1=1 ".$filtro_puntos."";
  $consulta_registros_puntos = $enlace_db->prepare($consulta_string_puntos);
  if (count($data_consulta_puntos)>0) {
      $consulta_registros_puntos->bind_param(str_repeat("s", count($data_consulta_puntos)), ...$data_consulta_puntos);
  }
  $consulta_registros_puntos->execute();
  $resultado_registros_puntos = $consulta_registros_puntos->get_result()->fetch_all(MYSQLI_NUM);

  $parametros_add='&bandeja='.base64_encode($bandeja);
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
              <a href="atencion_scita_crear?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Hoy'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear registro">
                <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear registro</span>
              </a>
              <a href="atencion_scita?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Hoy'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Hoy">
                <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Hoy</span>
              </a>
              <a href="atencion_scita?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
                <i class="fas fa-history btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Histórico</span>
              </a>
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
                      <th class="px-1 py-2">Radicado</th>
                      <th class="px-1 py-2">Punto Atención</th>
                      <th class="px-1 py-2">Asesor</th>
                      <th class="px-1 py-2">Fecha Atención</th>
                      <th class="px-1 py-2">Observaciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">
                        
                      </td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][12]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][22]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][21]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][20]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][9]; ?></td>
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
        <?php require_once('atencion_scita_reporte.php'); ?>
        <!-- modal -->
        
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>