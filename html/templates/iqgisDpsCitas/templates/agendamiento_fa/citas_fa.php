<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas FA";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas FA";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Citas | ".$bandeja;
  $pagina=validar_input($_GET['pagina']);
  unset($_SESSION[APP_SESSION.'_registro_creado_atencion_cita']);

  // Inicializa variable tipo array
  $data_consulta=array();
  $data_consulta_usuarios=array();
  $data_consulta_puntos=array();
  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_permanente=validar_input($_POST['id_filtro']);
      $filtro_fecha_permanente=validar_input($_POST['fecha_filtro']);
  } else {
      $filtro_permanente=validar_input($_GET['id']);
      $filtro_fecha_permanente=validar_input(base64_decode($_GET['fecha']));
  }

  //AGREGAR NÚMERO AL DÍA, SEGÚN SEMANA SELECCIONADA
  $dias_semana = array();
  for ($i=0; $i < 7; $i++) { 
      array_push($dias_semana, date("Y-m-d", strtotime("first day", strtotime($filtro_fecha_permanente . $i))));
  }

  // echo "<pre>";
  // print_r($dias_semana);
  // echo "</pre>";

  $fecha_inicio_consulta = date("Y-m-d", strtotime($dias_semana[0]));
  $fecha_fin_consulta = date("Y-m-d", strtotime($dias_semana[6]));
  array_push($data_consulta, $fecha_inicio_consulta);
  array_push($data_consulta, $fecha_fin_consulta);

  // Configuracón Paginación
  $registros_x_pagina=50;
  $iniciar_pagina=($pagina-1)*$registros_x_pagina;

  // Valida que filtro se deba ejecutar
  if ($filtro_permanente!="null" AND $filtro_permanente!="") {
      $filtro_buscar="AND (`gcar_consecutivo` LIKE ? OR `gcar_cita` LIKE ? OR `gcar_punto` LIKE ? OR `gcar_usuario` LIKE ? OR `gcar_datos_tipo_documento` LIKE ? OR `gcar_datos_numero_identificacion` LIKE ? OR `gcar_datos_nombres` LIKE ? OR `gcar_datos_correo` LIKE ? OR `gcar_datos_celular` LIKE ? OR `gcar_datos_fijo` LIKE ? OR `gcar_datos_autoriza` LIKE ? OR `gcar_observaciones` LIKE ? OR `gcar_atencion_usuario` LIKE ? OR `gcar_atencion_fecha` LIKE ? OR `gcar_registro_fecha` LIKE ? OR TP.`gcpa_punto_atencion` LIKE ? OR TP.`gcpa_direccion` LIKE ? OR TU.`usu_nombres_apellidos` LIKE ? OR TC.`gca_fecha` LIKE ? OR TC.`gca_hora` LIKE ? OR `gcar_estado` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  if($bandeja=="Pendientes"){
      if ($permisos_usuario=="Administrador") {
          $filtro_perfil="";
      } elseif ($permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador") {
          $filtro_perfil="";
      } elseif($permisos_usuario=="Supervisor"){
          $filtro_perfil="";
      } elseif($permisos_usuario=="Usuario"){
          $consulta_string_punto_atencion="SELECT `gcpau_punto_atencion`, `gcpau_usuario` FROM `gestion_citasfa_punto_atencion_usuario` WHERE `gcpau_usuario`=?";
          $consulta_registros_punto_atencion = $enlace_db->prepare($consulta_string_punto_atencion);
          $consulta_registros_punto_atencion->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
          $consulta_registros_punto_atencion->execute();
          $resultado_registros_punto_atencion = $consulta_registros_punto_atencion->get_result()->fetch_all(MYSQLI_NUM);

          $filtro_perfil=" AND `gcar_punto`=?";
          array_push($data_consulta, $resultado_registros_punto_atencion[0][0]);
      }
      $filtro_bandeja=" AND (`gcar_estado`=?)";
      array_push($data_consulta, 'Reservada');
  } elseif($bandeja=="Cerrados"){
      if ($permisos_usuario=="Administrador") {
          $filtro_perfil="";
      } elseif ($permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador") {
          $filtro_perfil=" AND TU.`usu_supervisor`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      } elseif($permisos_usuario=="Supervisor"){
          $filtro_perfil=" AND TU.`usu_supervisor`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      } elseif($permisos_usuario=="Usuario"){
          $filtro_perfil=" AND `gcar_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      }
      $filtro_bandeja=" AND (`gcar_estado`=? OR `gcar_estado`=? OR `gcar_estado`=?)";
      array_push($data_consulta, 'Asiste');
      array_push($data_consulta, 'No asiste');
      array_push($data_consulta, 'Cancelada');
  } elseif($bandeja=="Mi grupo"){
      if ($permisos_usuario=="Administrador") {
          $filtro_perfil="";
      } elseif ($permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador") {
          $filtro_perfil=" AND TU.`usu_supervisor`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      } elseif($permisos_usuario=="Supervisor"){
          $filtro_perfil=" AND TU.`usu_supervisor`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      } elseif($permisos_usuario=="Usuario"){
          $consulta_string_punto_atencion="SELECT `gcpau_punto_atencion`, `gcpau_usuario` FROM `gestion_citasfa_punto_atencion_usuario` WHERE `gcpau_usuario`=?";
          $consulta_registros_punto_atencion = $enlace_db->prepare($consulta_string_punto_atencion);
          $consulta_registros_punto_atencion->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
          $consulta_registros_punto_atencion->execute();
          $resultado_registros_punto_atencion = $consulta_registros_punto_atencion->get_result()->fetch_all(MYSQLI_NUM);

          $filtro_perfil=" AND `gcar_usuario`<>? AND `gcar_punto`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
          array_push($data_consulta, $resultado_registros_punto_atencion[0][0]);

      }
      $filtro_bandeja=" AND (`gcar_estado`<>?)";
      array_push($data_consulta, 'Cancelada');
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`gcar_consecutivo`) FROM `gestion_citasfa_agenda_reservas` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citasfa_agenda` AS TC ON `gestion_citasfa_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE 1=1 AND TC.`gca_fecha`>=? AND TC.`gca_fecha`<=? ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja."";

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

  $consulta_string="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_observaciones`, `gcar_atencion_usuario`, `gcar_atencion_fecha`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora`, TC.`gca_estado`, TC.`gca_estado_agenda`, `gcar_estado` FROM `gestion_citasfa_agenda_reservas` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citasfa_agenda` AS TC ON `gestion_citasfa_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE 1=1 AND TC.`gca_fecha`>=? AND TC.`gca_fecha`<=? ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja." ORDER BY TC.`gca_fecha`, TC.`gca_hora` LIMIT ?,?";
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

  $consulta_string_usuarios="SELECT `gcpau_usuario`, TU.`usu_nombres_apellidos` FROM `gestion_citasfa_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_usuarios."";
  $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
  if (count($data_consulta_usuarios)>0) {
      $consulta_registros_usuarios->bind_param(str_repeat("s", count($data_consulta_usuarios)), ...$data_consulta_usuarios);
  }
  $consulta_registros_usuarios->execute();
  $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_puntos="SELECT DISTINCT `gcpau_punto_atencion`, TP.`gcpa_punto_atencion` FROM `gestion_citasfa_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_punto_atencion_usuario`.`gcpau_punto_atencion`=TP.`gcpa_id` WHERE 1=1 ".$filtro_puntos."";
  $consulta_registros_puntos = $enlace_db->prepare($consulta_string_puntos);
  if (count($data_consulta_puntos)>0) {
      $consulta_registros_puntos->bind_param(str_repeat("s", count($data_consulta_puntos)), ...$data_consulta_puntos);
  }
  $consulta_registros_puntos->execute();
  $resultado_registros_puntos = $consulta_registros_puntos->get_result()->fetch_all(MYSQLI_NUM);

  $parametros_add='&bandeja='.base64_encode($bandeja).'&fecha='.base64_encode($filtro_fecha_permanente);
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
                    <input type="week" class="form-control form-control-sm" name="fecha_filtro" value='<?php if (isset($_POST["filtro"])) { echo $filtro_fecha_permanente; } else {if($filtro_fecha_permanente!="null"){echo $filtro_fecha_permanente;}} ?>' required>
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
              <a href="citas_fa?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Pendientes'); ?>&fecha=<?php echo base64_encode($filtro_fecha_permanente); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes</span>
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
                      <th class="px-1 py-2">Estado Agendamiento</th>
                      <th class="px-1 py-2">Fecha</th>
                      <th class="px-1 py-2">Hora</th>
                      <th class="px-1 py-2">Punto Atención</th>
                      <th class="px-1 py-2">Dirección</th>
                      <th class="px-1 py-2">Número Identificación</th>
                      <th class="px-1 py-2">Nombres y Apellidos</th>
                      <th class="px-1 py-2">Fecha Atención</th>
                      <th class="px-1 py-2">Observaciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">
                        
                      </td>
                      <td class="p-1 font-size-11 text-center">
                        <?php if($resultado_registros[$i][22]=='Reservada'): ?>
                          <div class="background-gris color-blanco radius-10 font-size-11 py-1 px-2">Reservada</div>
                        <?php elseif($resultado_registros[$i][22]=='Asiste'): ?>
                          <div class="background-verde color-blanco radius-10 font-size-11 py-1 px-2">Asiste</div>
                        <?php elseif($resultado_registros[$i][22]=='No asiste'): ?>
                          <div class="background-rojo color-blanco radius-10 font-size-11 py-1 px-2">No asiste</div>
                        <?php elseif($resultado_registros[$i][22]=='Cancelada'): ?>
                          <div class="background-gris color-blanco radius-10 font-size-11 py-1 px-2">Cancelada</div>
                        <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][18]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][19]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][15]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][16]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4].' '.$resultado_registros[$i][5]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][6]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][13]; ?></td>
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
        <?php require_once('citas_fa_reporte.php'); ?>
        <!-- modal -->
        
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>