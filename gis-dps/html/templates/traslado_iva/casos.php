<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Traslado IVA";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Traslado IVA";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Casos | ".$bandeja;
  $pagina=validar_input($_GET['pagina']);
  
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
      $filtro_buscar="AND (`gti_id` LIKE ? OR `gti_interaccion_id` LIKE ? OR `gti_interaccion_fecha` LIKE ? OR `gti_remitente` LIKE ? OR `gti_cliente_identificacion` LIKE ? OR `gti_cliente_nombre` LIKE ? OR `gti_titular_cedula` LIKE ? OR `gti_titular_fecha_expedicion` LIKE ? OR `gti_beneficiario_identificacion` LIKE ? OR `gti_link_foto` LIKE ? OR `gti_departamento` LIKE ? OR `gti_municipio` LIKE ? OR `gti_direccion` LIKE ? OR `gti_ruta_fichero` LIKE ? OR `gti_estado` LIKE ? OR `gti_responsable` LIKE ? OR `gti_numero_novedad` LIKE ? OR `gti_observaciones` LIKE ? OR `gti_fecha_gestion` LIKE ? OR `gti_registro_fecha` LIKE ? OR TU.`usu_nombres_apellidos`)";

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
  } elseif($permisos_usuario=="Usuario"){
      $filtro_perfil=" AND `gcar_usuario`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  if($bandeja=="Pendientes"){
      $filtro_bandeja=" AND (`gti_estado`=?)";
      array_push($data_consulta, 'Pendiente');
  } elseif($bandeja=="Cerrados"){
      $filtro_bandeja=" AND (`gti_estado`=?)";
      array_push($data_consulta, 'Cerrado');
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`gti_id`) FROM `gestion_traslado_iva` LEFT JOIN `administrador_usuario` AS TU ON `gestion_traslado_iva`.`gti_responsable`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja."";

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

  $consulta_string="SELECT `gti_id`, `gti_interaccion_id`, `gti_interaccion_fecha`, `gti_remitente`, `gti_cliente_identificacion`, `gti_cliente_nombre`, `gti_titular_cedula`, `gti_titular_fecha_expedicion`, `gti_beneficiario_identificacion`, `gti_link_foto`, `gti_departamento`, `gti_municipio`, `gti_direccion`, `gti_ruta_fichero`, `gti_estado`, `gti_responsable`, `gti_numero_novedad`, `gti_observaciones`, `gti_fecha_gestion`, `gti_registro_fecha`, TU.`usu_nombres_apellidos`, `gti_estado_bloqueo`, `gti_fecha_bloqueo` FROM `gestion_traslado_iva` LEFT JOIN `administrador_usuario` AS TU ON `gestion_traslado_iva`.`gti_responsable`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja." ORDER BY `gti_registro_fecha` LIMIT ?,?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

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
                    <input type="text" class="form-control form-control-sm" name="id_filtro" value='<?php if (isset($_POST["filtro"])) { echo $filtro_permanente; } else {if($filtro_permanente!="null"){echo $filtro_permanente;}} ?>' placeholder="Búsqueda" required autofocus>
                    <div class="input-group-append">
                      <button class="btn py-2 px-2 btn-primary btn-corp" type="submit" name="filtro"><span class="fas fa-search font-size-12"></span></button>
                      <a href="<?php echo $url_fichero; ?>?pagina=1&id=null<?php echo $parametros_add; ?>" class="btn py-2 px-2 btn-primary btn-corp"><span class="fas fa-sync-alt font-size-12"></span></a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="col-md-7 mb-1 text-end">
                <a href="casos?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Pendientes'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                  <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes</span>
                </a>
                <a href="casos?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Cerrados'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Cerrados">
                  <i class="fas fa-lock btn-icon-prepend me-0 font-size-12"></i><span class="d-none d-lg-inline"></span>
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
                      <th class="px-1 py-2">Estado</th>
                      <th class="px-1 py-2">Id Interacción</th>
                      <th class="px-1 py-2">Identificación Usuario</th>
                      <th class="px-1 py-2">Nombres y Apellidos</th>
                      <th class="px-1 py-2">Identificación Titular</th>
                      <th class="px-1 py-2">Fecha Expedición</th>
                      <th class="px-1 py-2">Identificación Beneficiario</th>
                      <th class="px-1 py-2">Municipio/Departamento</th>
                      <th class="px-1 py-2">Dirección</th>
                      <th class="px-1 py-2">Responsable</th>
                      <th class="px-1 py-2">No. Novedad</th>
                      <th class="px-1 py-2">Fecha Gestión</th>
                      <th class="px-1 py-2">Observaciones</th>
                      <th class="px-1 py-2">Fecha Registro</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                      <?php
                        $date2 = new DateTime("now");
                        $tiempo_bloqueo='';
                        if ($resultado_registros[$i][21] AND $resultado_registros[$i][14]=='Pendiente') {
                          $date1 = new DateTime($resultado_registros[$i][22]);
                          $diff = $date1->diff($date2);

                          $tiempo_bloqueo=( ($diff->days * 24 ) * 60 ) + ( $diff->i );
                        }
                      ?>

                    <tr>
                      <td class="p-1 text-center">
                        <?php if($resultado_registros[$i][14]=="Pendiente" OR ($resultado_registros[$i][14]=="Cerrado" AND $permisos_usuario=='Administrador')): ?>
                          <a href="casos_editar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                        <?php endif; ?>
                        <a href="#" onClick="open_modal_detalle('<?php echo base64_encode($resultado_registros[$i][0]); ?>');" class="btn btn-dark btn-icon px-1 py-1 mb-1" title="Detalle"><i class="fas fa-file-alt font-size-11"></i></a>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                        <?php if ($resultado_registros[$i][21] AND $resultado_registros[$i][14]=='Pendiente' AND $tiempo_bloqueo!='' AND $tiempo_bloqueo<15): ?>
                          <p class="alert alert-warning p-0 font-size-11 my-0"><span class="fas fa-user"></span> Bloqueado</p>
                        <?php endif; ?>
                        <?php echo $resultado_registros[$i][14]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][1]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][4]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][5]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][6]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][7]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][8]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][11].' / '.$resultado_registros[$i][10]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][12]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][20]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][16]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][18]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][17]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][19]; ?></td>
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
        <?php require_once('casos_reporte.php'); ?>
        <!-- modal -->
        <!-- MODAL DETALLE -->
        <div class="modal fade" id="modal-detalle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Detalle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body-detalle">
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger py-2 px-2" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- MODAL DETALLE -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
    function open_modal_detalle(id_registro) {
        var myModal = new bootstrap.Modal(document.getElementById("modal-detalle"), {});
        $('.modal-body-detalle').load('casos_ver.php?reg='+id_registro,function(){
            myModal.show();
        });
    }
  </script>
</body>
</html>