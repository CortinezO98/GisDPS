<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Interacciones";
  require_once("../../iniciador.php");
  session_start();
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  $modulo_plataforma = "Interacciones";
  /*VARIABLES*/
  $title = "Interacciones";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Registro Interacciones | ".$bandeja;
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='&bandeja='.base64_encode($bandeja);

  unset($_SESSION[APP_SESSION.'_registro_creado_interaccion']);
  $permisos_usuario = $_SESSION[APP_SESSION . '_session_modulos'][$modulo_plataforma] ?? null;
  // Inicializa variable tipo array
  $data_consulta=array();

  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_permanente=validar_input($_POST['id_filtro']);
  } else {
      $filtro_permanente=validar_input($_GET['id']);
  }

  if($bandeja=="Hoy"){
      $filtro_bandeja=" AND (`gi_gestion_fecha`=?)";
      array_push($data_consulta, date('Y-m-d'));
      if ($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador" OR $permisos_usuario=="Supervisor") {
          $filtro_perfil="";
      } elseif($permisos_usuario=="Usuario"){
          $filtro_perfil=" AND `gi_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      }
  } elseif($bandeja=="Mis Interacciones"){
      $filtro_bandeja="";
      if ($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador" OR $permisos_usuario=="Supervisor") {
          $filtro_perfil=" AND `gi_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      } elseif($permisos_usuario=="Usuario"){
          $filtro_perfil=" AND `gi_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
      }
  } elseif($bandeja=="Histórico"){
      $filtro_bandeja=" AND (`gi_gestion_fecha`<>?)";
      array_push($data_consulta, date('Y-m-d'));
      if ($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador" OR $permisos_usuario=="Supervisor") {
          $filtro_perfil="";
      } elseif($permisos_usuario=="Usuario"){
          $filtro_perfil="";
      }
  }

  // Configuracón Paginación
  $registros_x_pagina=20;
  $iniciar_pagina=($pagina-1)*$registros_x_pagina;

  // Valida que filtro se deba ejecutar
  if ($filtro_permanente!="null" AND $filtro_permanente!="") {
      $filtro_buscar="AND (`gi_id_caso` LIKE ? OR `gi_identificacion` LIKE ? OR `gi_primer_nombre`LIKE ? OR `gi_segundo_nombre`LIKE ? OR `gi_primer_apellido`LIKE ? OR `gi_segundo_apellido` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`gi_id`) FROM `gestion_interacciones_historico` LEFT JOIN `administrador_usuario` AS TU ON `gestion_interacciones_historico`.`gi_registro_usuario`=TU.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON `gestion_interacciones_historico`.`gi_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON `gestion_interacciones_historico`.`gi_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON `gestion_interacciones_historico`.`gi_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON `gestion_interacciones_historico`.`gi_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON `gestion_interacciones_historico`.`gi_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON `gestion_interacciones_historico`.`gi_subtipificacion_3`=TN6.`gic6_id` WHERE 1=1 ".$filtro_bandeja." ".$filtro_perfil." ".$filtro_buscar."";

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

  $consulta_string="SELECT `gi_id`, `gi_id_registro`, `gi_id_caso`, `gi_primer_nombre`, `gi_segundo_nombre`, `gi_primer_apellido`, `gi_segundo_apellido`, `gi_tipo_documento`, `gi_identificacion`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, gi_consulta, gi_respuesta, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, `gi_resultado`, `gi_descripcion_resultado`, `gi_canal_atencion`, `gi_registro_usuario`, `gi_registro_fecha`, TU.`usu_nombres_apellidos`, `gi_beneficiario` FROM `gestion_interacciones_historico` LEFT JOIN `administrador_usuario` AS TU ON `gestion_interacciones_historico`.`gi_registro_usuario`=TU.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON `gestion_interacciones_historico`.`gi_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON `gestion_interacciones_historico`.`gi_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON `gestion_interacciones_historico`.`gi_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON `gestion_interacciones_historico`.`gi_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON `gestion_interacciones_historico`.`gi_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON `gestion_interacciones_historico`.`gi_subtipificacion_3`=TN6.`gic6_id` WHERE 1=1 ".$filtro_bandeja." ".$filtro_perfil." ".$filtro_buscar." ORDER BY `gi_id` DESC LIMIT ?,?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $array_meses_lista = [];
  for ($i = 0; $i < 12; $i++) {
      $mes = date('Y-m', strtotime("-{$i} month"));
      $array_meses_lista[] = $mes;
  }
  sort($array_meses_lista);


  $consulta_string_opciones="SELECT `gia_id`, `gia_campo`, `gia_tipo`, `gia_nombre`, `gia_estado`, `gia_opciones` FROM `gestion_interacciones_auxiliar` WHERE `gia_estado`='Activo' AND `gia_campo` not like '%gi_auxiliar_%' AND `gia_tipo`='Lista' ORDER BY `gia_id`";
  $consulta_registros_opciones = $enlace_db->prepare($consulta_string_opciones);
  $consulta_registros_opciones->execute();
  $resultado_registros_opciones = $consulta_registros_opciones->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_opciones); $i++) {
    $explode_opciones= explode('|', $resultado_registros_opciones[$i][5]);
    for ($j=0; $j < count($explode_opciones); $j++) {
      if (trim($explode_opciones[$j])!="") {
        $array_opciones[$resultado_registros_opciones[$i][1]][]=trim($explode_opciones[$j]);
      }
    }
  }

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
              <a href="interacciones_crear_id?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Interacción">
                <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Interacción</span>
              </a>
              <a href="interacciones?pagina=1&id=null&bandeja=<?php echo base64_encode('Hoy'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Hoy">
                <i class="fas fa-calendar-day btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Hoy</span>
              </a>
              <a href="interacciones?pagina=1&id=null&bandeja=<?php echo base64_encode('Mis Interacciones'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Mis Interacciones">
                <i class="fas fa-user-cog btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Mis Interacciones</span>
              </a>
              <a href="interacciones?pagina=1&id=null&bandeja=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
                <i class="fas fa-history btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Histórico</span>
              </a>

              <?php if ( in_array($permisos_usuario, ['Administrador','Gestor','Cliente']) ): ?>
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
                      <th class="px-1 py-2">Id Caso</th>
                      <th class="px-1 py-2">Canal de atención</th>
                      <th class="px-1 py-2">Primer Nombre</th>
                      <th class="px-1 py-2">Segundo Nombre</th>
                      <th class="px-1 py-2">Primer Apellido</th>
                      <th class="px-1 py-2">Segundo Apellido</th>
                      <th class="px-1 py-2">Identificación</th>
                      <th class="px-1 py-2">Beneficiario?</th>
                      <th class="px-1 py-2">Tipificación 1</th>
                      <th class="px-1 py-2">Tipificación 2</th>
                      <th class="px-1 py-2">Tipificación 3</th>
                      <th class="px-1 py-2">Consulta</th>
                      <th class="px-1 py-2">Respuesta</th>
                      <th class="px-1 py-2">Resultado</th>
                      <th class="px-1 py-2">Descripción del resultado</th>
                      <th class="px-1 py-2">Registrado por</th>
                      <th class="px-1 py-2">Fecha Registro</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][17]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][3]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][5]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][6]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][8]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][21]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][9]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][10]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][11]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][12]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][13]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][17]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][18]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][22]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][21]; ?></td>
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
