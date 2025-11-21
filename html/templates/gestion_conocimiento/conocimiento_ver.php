<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Conocimiento";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Conocimiento";
  $subtitle = "Gestión Conocimiento | Ver";
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $id_busqueda=validar_input($_GET['bus']);
  $url_salir="conocimiento?pagina=".$pagina."&id=".$filtro_permanente."&cat=".$id_categoria;

    $consulta_string="SELECT `gc_codigo`, `gc_categoria`, TC.`gcc_orden`, TC.`gcc_nombre_categoria`, `gc_nombre`, `gc_descripcion`, `gc_ruta`, `gc_extension`, `gc_version`, `gc_visitas`, `gc_registro_usuario`, TU.`usu_nombres_apellidos`, `gc_registro_fecha`, `gc_actualiza_fecha`, `gc_actualiza_usuario`, TUA.`usu_nombres_apellidos` FROM `gestion_conocimiento` LEFT JOIN `gestion_conocimiento_categoria` AS TC ON `gestion_conocimiento`.`gc_categoria`=TC.`gcc_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_conocimiento`.`gc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_conocimiento`.`gc_actualiza_usuario`=TUA.`usu_id` WHERE `gc_codigo`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
    $ruta_documento=$resultado_registros[0][6];
    $extension_documento=strtolower($resultado_registros[0][7]);
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
          <div class="row justify-content-center">
            <div class="col-lg-12 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12 font-size-12 color-gris">
                            <b><?php echo validar_extension_icono($extension_documento)." ".$resultado_registros[0][4]; ?></b>
                            <br><span class="fas fa-database" title="Categoría"></span> Categoría: <?php echo $resultado_registros[0][2].'. '.$resultado_registros[0][3]; ?>
                            <?php if($resultado_registros[0][8]!=""): ?>
                                | <span class="fas fa-file-signature" title="Versión"></span> Versión: <?php echo $resultado_registros[0][8]; ?>
                            <?php endif; ?>
                                | <span class="fas fa-sync-alt" title="Fecha actualización"></span> Actualización: <?php echo date('d-m-Y', strtotime($resultado_registros[0][13])); ?>
                             | <span class="fas fa-eye" title="Visitas"></span> <?php echo number_format($resultado_registros[0][9], 0, '', '.'); ?>
                            <?php if($perfil_modulo=="Administrador" OR $perfil_modulo=="Gestor"): ?>
                                <br><span class="fas fa-user-cog" title="Creado por"></span> Creado por: <?php echo $resultado_registros[0][11]; ?> | <span class="fas fa-user-clock" title="Fecha creación"></span> Fecha creación: <?php echo $resultado_registros[0][13]; ?>
                                <br><span class="fas fa-user-check" title="Actualizado por"></span> Actualizado por: <?php echo $resultado_registros[0][15]; ?>
                            <?php endif; ?>
                        </div>
                        <?php if ($extension_documento=="pdf"): ?>
                            <embed src="<?php echo $ruta_documento; ?>?ran=<?php echo generar_codigo(5); ?>#zoom=100" id="visor" style="width: 100%; min-height: 450px;">
                        <?php elseif ($extension_documento=="xls" OR $extension_documento=="xlsx" OR $extension_documento=="doc" OR $extension_documento=="docx"): ?>
                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https%3A%2F%2Fdps.iq-online.net.co%2Fgestion_conocimiento%2F<?php echo $ruta_documento; ?>?ran=<?php echo generar_codigo(5); ?>&embedded=true" id="visor" style="border: none; width: 100%; min-height: 450px;"></iframe>
                        <?php else: ?>
                            <p class="alert alert-warning p-1 font-size-11 mt-1"><span class="fas fa-exclamation-triangle"></span> ¡No es posible visualizar el documento, por favor contacte al administrador!</p>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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