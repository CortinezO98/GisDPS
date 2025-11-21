<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Administrador";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  /*VARIABLES*/
  $title = "Administrador";
  $subtitle = "Notificaciones Correo";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  unset($_SESSION[APP_SESSION.'_registro_creado_notificacion']);
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
      $filtro_buscar="AND (`nc_id_modulo` LIKE ? OR `nc_prioridad` LIKE ? OR `nc_id_set_from` LIKE ? OR `nc_address` LIKE ? OR `nc_cc` LIKE ? OR `nc_bcc` LIKE ? OR `nc_reply_to` LIKE ? OR `nc_subject` LIKE ? OR `nc_body` LIKE ? OR `nc_embeddedimage_ruta` LIKE ? OR `nc_embeddedimage_nombre` LIKE ? OR `nc_embeddedimage_tipo` LIKE ? OR `nc_intentos` LIKE ? OR `nc_eliminar` LIKE ? OR `nc_estado_envio` LIKE ? OR `nc_fecha_envio` LIKE ? OR `nc_fecha_registro` LIKE ? OR `nc_usuario_registro` LIKE ? OR `ncr_username` LIKE ? OR `ncr_setfrom_name` LIKE ? OR TU.`usu_nombres_apellidos` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`nc_id`) FROM `administrador_notificaciones` LEFT JOIN `administrador_buzones` AS RT ON `administrador_notificaciones`.`nc_id_set_from`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TU ON `administrador_notificaciones`.`nc_usuario_registro`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar."";
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

  $consulta_string="SELECT `nc_id`, `nc_id_modulo`, `nc_prioridad`, `nc_id_set_from`, `nc_address`, `nc_cc`, `nc_bcc`, `nc_reply_to`, `nc_subject`, `nc_body`, `nc_embeddedimage_ruta`, `nc_embeddedimage_nombre`, `nc_embeddedimage_tipo`, `nc_intentos`, `nc_eliminar`, `nc_estado_envio`, `nc_fecha_envio`, `nc_fecha_registro`, `nc_usuario_registro`, `ncr_username`, `ncr_setfrom_name`, TU.`usu_nombres_apellidos` FROM `administrador_notificaciones` LEFT JOIN `administrador_buzones` AS RT ON `administrador_notificaciones`.`nc_id_set_from`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TU ON `administrador_notificaciones`.`nc_usuario_registro`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ORDER BY `nc_fecha_registro` DESC LIMIT ?,?";
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
              
            </div>
            <div class="col-lg-12">
              <div class="table-responsive table-fixed" id="headerFixTable">
                <table class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th class="px-1 py-2" style="width: 65px;"></th>
                      <th class="px-1 py-2">Id Notificación</th>
                      <th class="px-1 py-2">Estado</th>
                      <th class="px-1 py-2">Asunto</th>
                      <th class="px-1 py-2">Remitente</th>
                      <th class="px-1 py-2">Destinatario</th>
                      <th class="px-1 py-2">Destinatario CC</th>
                      <th class="px-1 py-2">Intentos</th>
                      <th class="px-1 py-2">Fecha Envío</th>
                      <th class="px-1 py-2">Fecha Registro</th>
                      <th class="px-1 py-2">Usuario Registro</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 text-center">
                          <?php if ($resultado_registros[$i][15]=='Destinatario inválido' OR $resultado_registros[$i][15]=='Error de autenticación'): ?>
                            <a href="notificaciones_correo_editar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                          <?php endif; ?>
                          <a href="notificaciones_correo_reenviar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-success btn-icon px-1 py-1 mb-1" title="Reenviar"><i class="fas fa-share-square font-size-11"></i></a>
                      </td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][0]; ?></td>
                      <td class="p-1 font-size-11 text-center">
                        <?php if ($resultado_registros[$i][15]=='Pendiente'): ?>
                              <span class="fas fa-clock size-3" title="<?php echo $resultado_registros[$i][15]; ?>"></span>
                          <?php elseif ($resultado_registros[$i][15]=='Enviado'): ?>
                              <span class="fas fa-paper-plane color-verde size-3" title="<?php echo $resultado_registros[$i][15]; ?>"></span>
                          <?php elseif ($resultado_registros[$i][15]=='Destinatario inválido'): ?>
                              <span class="fas fa-user-times color-rojo size-3" title="<?php echo $resultado_registros[$i][15]; ?>"></span>
                          <?php elseif ($resultado_registros[$i][15]=='Error de autenticación'): ?>
                              <span class="fas fa-key color-rojo size-3" title="<?php echo $resultado_registros[$i][15]; ?>"></span>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][8]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][19]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][5]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][13]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][16]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][17]; ?></td>
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
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>