<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Conocimiento";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Conocimiento";
  $subtitle = "Gestión Conocimiento";
  $pagina=validar_input($_GET['pagina']);
  $categoria=validar_input($_GET['cat']);
  $parametros_add='&cat='.$categoria;

  unset($_SESSION[APP_SESSION.'_gconocimiento_registro_creado']);
  unset($_SESSION[APP_SESSION.'_gconocimiento_registro_eliminado']);

  // Inicializa variable tipo array
  $data_consulta=array();
  $data_consulta_cat=array();
  $data_consulta_noticia=array();
  $filtro_buscar='';

  if($categoria!="null" AND $categoria!=""){
      $filtro_categoria_validar=" AND `gc_categoria`=?";
      array_push($data_consulta, $categoria);
  } else {
      $filtro_categoria_validar="";
  }

  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_permanente=validar_input($_POST['id_filtro']);
      if ($filtro_permanente=="") {
          $filtro_permanente="null";
      }
  } else {
      $filtro_permanente=validar_input($_GET['id']);
  }

  // Valida que filtro se deba ejecutar
  if ($filtro_permanente!="null" AND $filtro_permanente!="") {
      $filtro_buscar="AND (TC.`gcc_nombre_categoria` LIKE ? OR `gc_nombre` LIKE ? OR `gc_descripcion` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  $consulta_string="SELECT `gc_codigo`, `gc_categoria`, TC.`gcc_orden`, TC.`gcc_nombre_categoria`, `gc_nombre`, `gc_descripcion`, `gc_ruta`, `gc_extension`, `gc_version`, `gc_visitas`, `gc_registro_usuario`, TU.`usu_nombres_apellidos`, `gc_registro_fecha`, `gc_actualiza_fecha`, `gc_actualiza_usuario`, TUA.`usu_nombres_apellidos` FROM `gestion_conocimiento` LEFT JOIN `gestion_conocimiento_categoria` AS TC ON `gestion_conocimiento`.`gc_categoria`=TC.`gcc_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_conocimiento`.`gc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_conocimiento`.`gc_actualiza_usuario`=TUA.`usu_id` WHERE 1=1 ".$filtro_categoria_validar." ".$filtro_buscar." ORDER BY TC.`gcc_orden`, TC.`gcc_nombre_categoria`";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (count($data_consulta)>0) {
      $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_categorias="SELECT `gcc_id`, `gcc_orden`, `gcc_nombre_categoria`, `gcc_descripcion`, `gcc_estado` FROM `gestion_conocimiento_categoria` ORDER BY `gcc_orden`, `gcc_nombre_categoria`";
  $consulta_registros_categorias = $enlace_db->prepare($consulta_string_categorias);
  $consulta_registros_categorias->execute();
  $resultado_registros_categorias = $consulta_registros_categorias->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_categorias); $i++) { 
      $array_categorias_detalle[$resultado_registros_categorias[$i][0]]['nombre']=$resultado_registros_categorias[$i][2];
      $array_categorias_detalle[$resultado_registros_categorias[$i][0]]['orden']=$resultado_registros_categorias[$i][1];
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
            <div class="col-lg-3">
              <div class="list-group font-size-11">
                  <?php for ($i=0; $i < count($resultado_registros_categorias); $i++): ?>
                      <a href="conocimiento?pagina=1&id=<?php echo $filtro_permanente; ?>&cat=<?php echo $resultado_registros_categorias[$i][0]; ?>" class="list-group-item list-group-item-action list-group-item-dark"><?php echo $resultado_registros_categorias[$i][1].". <span class='lowercase'>".$resultado_registros_categorias[$i][2]."</span>"; ?></a>
                  <?php endfor; ?>
              </div>
            </div>
            <div class="col-lg-9">
                <div class="col-md-12 seccion-filtro-top p-0">
                    <?php if($categoria!="null" AND $categoria!=""): ?>
                        <div class="float-start mb-1">Filtros: </div>
                    <?php endif; ?>
                    <div class="background-principal color-blanco seccion-filtro-top float-start px-1 mb-1 ml-1">
                        <?php if($categoria!="null" AND $categoria!="") { echo $array_categorias_detalle[$categoria]['orden'].". ".$array_categorias_detalle[$categoria]['nombre'].' <a href="conocimiento?pagina=1&id='.$filtro_permanente.'&cat=null" class="color-rojo" title="Eliminar"><span class="fas fa-times"></span></a>'; } ?>
                    </div>
                </div>
                <form name="filtrado" action="" method="POST">
                  <div class="form-group m-0">
                    <div class="input-group">
                      <input type="text" class="form-control form-control-sm" name="id_filtro" value='<?php if (isset($_POST["filtro"])) { echo $_POST['id_filtro']; } else {if($filtro_permanente!="null"){echo $filtro_permanente;}} ?>' placeholder="Ingresa aquí tu búsqueda" required autofocus autocomplete="off">
                      <div class="input-group-append">
                        <button class="btn py-2 px-2 btn-primary btn-corp" type="submit" name="filtro"><span class="fas fa-search font-size-12"></span></button>
                        <a href="<?php echo $url_fichero; ?>?pagina=1&id=null<?php echo $parametros_add; ?>" class="btn py-2 px-2 btn-primary btn-corp"><span class="fas fa-sync-alt font-size-12"></span></a>
                        <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                            <a href="conocimiento_crear?pagina=1&id=null<?php echo $parametros_add; ?>" class="btn py-2 px-2 btn-primary btn-corp" title="Crear documento"><span class="fas fa-plus font-size-12"></span></a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </form>
                <?php if (count($resultado_registros)>0): ?>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                        <div class="col-lg-12 d-flex flex-column">
                          <div class="row flex-grow">
                            <div class="col-12 grid-margin stretch-card my-1">
                              <div class="card card-rounded">
                                <div class="card-body">
                                  <div class="col-md-12">
                                      <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                                          <a href="conocimiento_editar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&cat=<?php echo $categoria; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                                          <a href="conocimiento_eliminar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&cat=<?php echo $categoria; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Eliminar"><i class="fas fa-trash-alt font-size-11"></i></a>
                                      <?php endif; ?>
                                      <?php echo validar_extension_icono($resultado_registros[$i][7]); ?>
                                      <?php if(strtolower($resultado_registros[$i][7])=="pdf" OR strtolower($resultado_registros[$i][7])=="xls" OR strtolower($resultado_registros[$i][7])=="xlsx" OR strtolower($resultado_registros[$i][7])=="doc" OR strtolower($resultado_registros[$i][7])=="docx"): ?>
                                          <a href="conocimiento_ver_control.php?reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" target="_blank" class="color-principal fw-bold"><?php echo $resultado_registros[$i][4]; ?> <span class="fas fa-external-link-alt"></span></a>
                                      <?php else: ?>
                                          <?php echo $resultado_registros[$i][4]; ?>
                                      <?php endif; ?>
                                  </div>
                                  <div class="col-md-12 font-size-12 color-gris my-2 fst-italic">
                                      <?php echo $resultado_registros[$i][5]; ?>
                                  </div>
                                  <div class="col-md-12 font-size-12 color-gris">
                                      <span class="fas fa-database" title="Categoría"></span> Categoría: <?php echo $resultado_registros[$i][2].'. '.$resultado_registros[$i][3]; ?>
                                      <?php if($resultado_registros[$i][8]!=""): ?>
                                          | <span class="fas fa-file-signature" title="Versión"></span> Versión: <?php echo $resultado_registros[$i][8]; ?>
                                      <?php endif; ?>
                                          | <span class="fas fa-sync-alt" title="Fecha actualización"></span> Actualización: <?php echo date('d-m-Y', strtotime($resultado_registros[$i][13])); ?>
                                       | <span class="fas fa-eye" title="Visitas"></span> <?php echo number_format($resultado_registros[$i][9], 0, '', '.'); ?>
                                      <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                                          <br><span class="fas fa-user-cog" title="Creado por"></span> Creado por: <?php echo $resultado_registros[$i][11]; ?> | <span class="fas fa-user-clock" title="Fecha creación"></span> Fecha creación: <?php echo $resultado_registros[$i][13]; ?>
                                          <br><span class="fas fa-user-check" title="Actualizado por"></span> Actualizado por: <?php echo $resultado_registros[$i][15]; ?>
                                      <?php endif; ?>
                                  </div>  
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    <?php endfor; ?>
                <?php else: ?>
                    <p class="alert alert-warning col-md-12 p-1 font-size-11">
                        <span class="fas fa-exclamation-triangle"></span> No se encontraron registros
                    </p>
                <?php endif; ?>
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