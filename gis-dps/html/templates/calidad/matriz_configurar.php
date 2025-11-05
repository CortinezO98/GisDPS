<?php
  session_start();
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Matriz Calidad";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Matriz de Calidad | Configurar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="matriz?pagina=".$pagina."&id=".$filtro_permanente;
  $permisos_usuario = $_SESSION[APP_SESSION . '_session_modulos'][$modulo_plataforma] ?? null;  
  unset($_SESSION[APP_SESSION.'_registro_creado_matriz']);
  unset($_SESSION[APP_SESSION.'_registro_creado_matriz_configurar']);

  $consulta_string="SELECT `gcmi_id`, `gcmi_matriz`, `gcmi_item_tipo`, `gcmi_item_consecutivo`, `gcmi_item_orden`, `gcmi_descripcion`, `gcmi_peso`, `gcmi_calificable`, `gcmi_grupo_peso`, `gcmi_visible`, `gcmi_tipo_error`, `gcmi_grupo_id`, `gcmi_subgrupo_id`, `gcmi_item_id`, `gcmi_subitem_id` FROM `gestion_calidad_matriz_item` WHERE `gcmi_matriz`=? ORDER BY `gcmi_item_consecutivo` ASC";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros); $i++) { 
      if ($resultado_registros[$i][2]=="Grupo") {
          $array_grupos[]=$resultado_registros[$i][0];
      }
      if ($resultado_registros[$i][2]=="Sub-Grupo") {
          $array_grupos_sub[$resultado_registros[$i][11]][]=$resultado_registros[$i][0];
      }
      if ($resultado_registros[$i][2]=="Item") {
          $array_item[$resultado_registros[$i][11]][$resultado_registros[$i][12]][]=$resultado_registros[$i][0];
      }
      if ($resultado_registros[$i][2]=="Sub-Item") {
          $array_item_sub[$resultado_registros[$i][11]][$resultado_registros[$i][12]][$resultado_registros[$i][13]][]=$resultado_registros[$i][0];
      }
  }

  $consulta_string_matriz="SELECT `gcm_id`, `gcm_nombre_matriz`, `gcm_estado`, `gcm_canal`, `gcm_observaciones`, `gcm_registro_usuario`, `gcm_registro_fecha` FROM `gestion_calidad_matriz` WHERE `gcm_id`=?";

  $consulta_registros_matriz = $enlace_db->prepare($consulta_string_matriz);
  $consulta_registros_matriz->bind_param("s", $id_registro);
  $consulta_registros_matriz->execute();
  $resultado_registros_matriz = $consulta_registros_matriz->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_conteo_monitoreos="SELECT COUNT(`gcm_id`) FROM `gestion_calidad_monitoreo` WHERE `gcm_matriz`=?";

  $consulta_registros_conteo_monitoreos = $enlace_db->prepare($consulta_string_conteo_monitoreos);
  $consulta_registros_conteo_monitoreos->bind_param("s", $id_registro);
  $consulta_registros_conteo_monitoreos->execute();
  $resultado_registros_conteo_monitoreos = $consulta_registros_conteo_monitoreos->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-md-3 mb-1">
              
            </div>
            <div class="col-md-9 mb-1 text-end">
              <a href="matriz.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Regresar">
                <i class="fas fa-arrow-left btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Regresar</span>
              </a>
              <?php if(($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor") AND $resultado_registros_conteo_monitoreos[0][0]==0): ?>
                <a href="matriz_configurar_crear.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>&tipo=<?php echo base64_encode('Grupo'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Grupo">
                  <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Grupo</span>
                </a>
              <?php endif; ?>
            </div>
            <div class="col-lg-12">
              <?php if (count($resultado_registros)>0): ?>
                  <div id="table-fixed" class="table-responsive table-fixed">
                      <?php if ($resultado_registros_conteo_monitoreos[0][0]>0): ?>
                      <p class="alert alert-warning font-size-11 p-1">¡No es posible modificar la estructura debido a que ya se han cargado monitoreos realizados!</p>
                      <?php endif; ?>
                      <p class="alert alert-success font-size-11 p-1 text-center"><b>Matriz:</b> <?php echo $resultado_registros_matriz[0][1]; ?> | <b>Canal:</b> <?php echo $resultado_registros_matriz[0][3]; ?> | <b>Estado:</b> <?php echo $resultado_registros_matriz[0][2]; ?></p>
                      <p class="alert alert-warning font-size-11 p-1"><b>Por favor tener en cuenta la siguiente información para una correcta configuración:</b><br>
                          <span class="fas fa-check-square"></span> Cada matriz puede tener como máximo 3 grupos preconfigurados (ENC, ECU y ECN).<br>
                          <span class="fas fa-check-square"></span> El orden de visualización de cada item está dado por el campo "Consecutivo".<br>
                          <span class="fas fa-check-square"></span> El campo "Visible" permite que un item sea visible o no al momento de crear un monitoreo.<br>
                          <span class="fas fa-check-square"></span> El campo "Calificable" permite que un item tenga o no, check para calificación al momento de crear un monitoreo.<br>
                          <span class="fas fa-check-square"></span> El campo "Grupo Calificación" permite agrupar items para que el cálculo de la nota sea agrupada, es decir, que si uno de los items del grupo no se cumple, la nota del grupo será 0 (Cero).<br>
                          <span class="fas fa-check-square"></span> El campo "Peso" permite asignar distribución de porcentaje para cada item, proporcional al peso que debe tener sobre la calificación del grupo al que pertenece.
                      </p>
                      <table class="table table-bordered table-striped table-sm">
                          <thead>
                              <tr>
                                  <th class="align-middle" style="width: 100px;">Acciones</th>
                                  <th class="align-middle" style="width: 100px;">Consecutivo</th>
                                  <th class="align-middle" style="width: 50px;">Visible</th>
                                  <th class="align-middle">Atributos de Evaluación</th>
                                  <th class="align-middle" style="width: 100px;">Calificable</th>
                                  <th class="align-middle" style="width: 100px;">Grupo Calificación</th>
                                  <th class="align-middle" style="width: 100px;">Peso</th>
                              </tr>
                          </thead>    
                          <tbody>    
                              <?php
                                  for ($i=0; $i < count($resultado_registros); $i++) { 
                              ?>
                              <tr>
                                  <td class="p-1 font-size-11">
                                      <?php if(($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor") AND $resultado_registros_conteo_monitoreos[0][0]==0): ?>
                                          <?php if($resultado_registros[$i][2]!="Sub-Item"): 
                                              if ($resultado_registros[$i][2]=="Grupo") {
                                                  $tipo_item_agregar="Sub-Grupo";
                                              } elseif ($resultado_registros[$i][2]=="Sub-Grupo") {
                                                  $tipo_item_agregar="Item";
                                              } elseif ($resultado_registros[$i][2]=="Item") {
                                                  $tipo_item_agregar="Sub-Item";
                                              }
                                          ?>
                                            <a href="matriz_configurar_crear.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>&tipo=<?php echo base64_encode($tipo_item_agregar); ?>&tierr=<?php echo base64_encode($resultado_registros[$i][10]); ?>&idg_grupo=<?php echo base64_encode($resultado_registros[$i][11]); ?>&idg_subgrupo=<?php echo base64_encode($resultado_registros[$i][12]); ?>&idg_item=<?php echo base64_encode($resultado_registros[$i][13]); ?>" class="btn btn-success btn-icon px-1 py-1 mb-1" title="Agregar Item"><i class="fas fa-plus font-size-11"></i></a>
                                          <?php endif; ?>

                                          <?php if($resultado_registros[$i][2]!="Grupo"): ?>
                                              <a href="matriz_configurar_editar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>&tipo=<?php echo base64_encode($resultado_registros[$i][2]); ?>&item=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                                          <?php endif; ?>
                                          <?php if($resultado_registros[$i][2]=="Grupo" AND !isset($array_grupos_sub[$resultado_registros[$i][11]])):
                                                  $mostrar_eliminar=1;
                                              elseif($resultado_registros[$i][2]=="Sub-Grupo" AND !isset($array_item[$resultado_registros[$i][11]][$resultado_registros[$i][12]])):
                                                  $mostrar_eliminar=1;
                                              elseif($resultado_registros[$i][2]=="Item" AND !isset($array_item_sub[$resultado_registros[$i][11]][$resultado_registros[$i][12]][$resultado_registros[$i][13]])):
                                                  $mostrar_eliminar=1;
                                              elseif($resultado_registros[$i][2]=="Sub-Item"):
                                                  $mostrar_eliminar=1;
                                              else:
                                                  $mostrar_eliminar=0;
                                              endif;
                                          ?>
                                          <?php if($mostrar_eliminar==1): ?>
                                            <a href="matriz_configurar_eliminar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>&tipo=<?php echo base64_encode($resultado_registros[$i][2]); ?>&item=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Eliminar"><i class="fas fa-trash-alt font-size-11"></i></a>
                                          <?php endif; ?>
                                          
                                      <?php endif; ?>
                                  </td>
                                  <td class="p-1 font-size-11 <?php if($resultado_registros[$i][2]=='Grupo'){echo'matriz-grupo';} elseif($resultado_registros[$i][2]=='Sub-Grupo'){echo'matriz-grupo-sub';} elseif($resultado_registros[$i][2]=='Item'){echo'matriz-item';}?>"><?php echo $resultado_registros[$i][3]; ?></td>
                                  <td class="p-1 font-size-11 <?php if($resultado_registros[$i][2]=='Grupo'){echo'matriz-grupo';} elseif($resultado_registros[$i][2]=='Sub-Grupo'){echo'matriz-grupo-sub';} elseif($resultado_registros[$i][2]=='Item'){echo'matriz-item';}?>"><?php echo $resultado_registros[$i][9]; ?></td>
                                  <td class="p-1 font-size-11 <?php if($resultado_registros[$i][2]=='Grupo'){echo'matriz-grupo';} elseif($resultado_registros[$i][2]=='Sub-Grupo'){echo'matriz-grupo-sub';} elseif($resultado_registros[$i][2]=='Item'){echo'matriz-item';}?>"><?php echo $resultado_registros[$i][5]; ?></td>
                                  <td class="p-1 font-size-11 <?php if($resultado_registros[$i][2]=='Grupo'){echo'matriz-grupo';} elseif($resultado_registros[$i][2]=='Sub-Grupo'){echo'matriz-grupo-sub';} elseif($resultado_registros[$i][2]=='Item'){echo'matriz-item';}?> text-center"><?php echo $resultado_registros[$i][7]; ?></td>
                                  <td class="p-1 font-size-11 <?php if($resultado_registros[$i][2]=='Grupo'){echo'matriz-grupo';} elseif($resultado_registros[$i][2]=='Sub-Grupo'){echo'matriz-grupo-sub';} elseif($resultado_registros[$i][2]=='Item'){echo'matriz-item';}?> text-center"><?php echo ($resultado_registros[$i][8]!="") ? 'G-'.$resultado_registros[$i][8] : ''; ?></td>
                                  <td class="p-1 font-size-11 <?php if($resultado_registros[$i][2]=='Grupo'){echo'matriz-grupo';} elseif($resultado_registros[$i][2]=='Sub-Grupo'){echo'matriz-grupo-sub';} elseif($resultado_registros[$i][2]=='Item'){echo'matriz-item';}?> text-center"><?php echo $resultado_registros[$i][6]; ?>%</td>
                              </tr>
                              <?php
                                  }
                              ?>
                          </tbody>
                      </table>
                  </div>
              <?php else: ?>
                  <p class="alert alert-warning">
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
