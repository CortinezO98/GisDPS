<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Kioscos";
  $subtitle = "Programas | Botones | Preguntas";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $id_boton=validar_input(base64_decode($_GET['btn']));
  $url_salir="programas_boton?pagina=".$pagina."&id=".$filtro_permanente."&reg=".base64_encode($id_registro);

    unset($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta']);
    unset($_SESSION[APP_SESSION.'_registro_creado_boton_pregunta_eliminar']);

    $consulta_string_botones="SELECT `gkpb_id`, `gkpb_programa`, `gkpb_nombre`, `gkpb_tipo`, `gkpb_estado`, `gkpb_url`, `gkpb_registro_usuario`, `gkpb_registro_fecha`, TU.`usu_nombres_apellidos`, TP.`gkp_titulo`, TP.`gkp_imagen` FROM `gestion_kioscos_programas_boton` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_programas_boton`.`gkpb_registro_usuario`=TU.`usu_id` LEFT JOIN `gestion_kioscos_programas` AS TP ON `gestion_kioscos_programas_boton`.`gkpb_programa`=TP.`gkp_id` WHERE `gkpb_id`=?";
    $consulta_registros_botones = $enlace_db->prepare($consulta_string_botones);
    $consulta_registros_botones->bind_param("s", $id_boton);
    $consulta_registros_botones->execute();
    $resultado_registros_botones = $consulta_registros_botones->get_result()->fetch_all(MYSQLI_NUM);

  // Inicializa variable tipo array
  $data_consulta=array();
  array_push($data_consulta, $id_boton);
  
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
      $filtro_buscar="AND (`gkbp_pregunta` LIKE ? OR `gkbp_respuesta` LIKE ? OR `gkbp_palabras_claves` LIKE ? OR `gkbp_estado` LIKE ? OR `gkbp_actualiza_usuario` LIKE ? OR `gkbp_actualiza_fecha` LIKE ? OR `gkbp_registro_usuario` LIKE ? OR `gkbp_registro_fecha` LIKE ? OR TP.`gkp_titulo` LIKE ? OR TB.`gkpb_nombre` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`gkbp_id`) FROM `gestion_kioscos_preguntas` LEFT JOIN `gestion_kioscos_programas` AS TP ON `gestion_kioscos_preguntas`.`gkbp_programa`=TP.`gkp_id` LEFT JOIN `gestion_kioscos_programas_boton` AS TB ON `gestion_kioscos_preguntas`.`gkbp_boton`=TB.`gkpb_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_preguntas`.`gkbp_registro_usuario`=TU.`usu_id` WHERE `gkbp_boton`=? ".$filtro_buscar."";

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

  $consulta_string="SELECT `gkbp_id`, `gkbp_programa`, `gkbp_boton`, `gkbp_orden`, `gkbp_pregunta`, `gkbp_respuesta`, `gkbp_palabras_claves`, `gkbp_estado`, `gkbp_actualiza_usuario`, `gkbp_actualiza_fecha`, `gkbp_registro_usuario`, `gkbp_registro_fecha`, TP.`gkp_titulo`, TB.`gkpb_nombre`, TU.`usu_nombres_apellidos` FROM `gestion_kioscos_preguntas` LEFT JOIN `gestion_kioscos_programas` AS TP ON `gestion_kioscos_preguntas`.`gkbp_programa`=TP.`gkp_id` LEFT JOIN `gestion_kioscos_programas_boton` AS TB ON `gestion_kioscos_preguntas`.`gkbp_boton`=TB.`gkpb_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_preguntas`.`gkbp_registro_usuario`=TU.`usu_id` WHERE `gkbp_boton`=? ".$filtro_buscar." ORDER BY `gkbp_orden` LIMIT ?,?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  function addLink($content) {
      $reg_exUrl = "/.[http|https|ftp|ftps]*\:\/\/.[^$|\s]*/";
      return preg_replace($reg_exUrl, " <a href='$0' target='_blank'>$0<span class='fas fa-arrow-up-right-from-square'></span></a>", $content);
  }

  $parametros_add='&reg='.base64_encode($id_registro).'&btn='.base64_encode($id_boton);
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
            <div class="col-lg-3 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="programa" class="my-0">Programa</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="programa" id="programa" maxlength="100" value="<?php echo $resultado_registros_botones[0][9]; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <img src="<?php echo $resultado_registros_botones[0][10]; ?>" style="width: 150px; height: 150px; border-radius: 0px;">
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="boton" class="my-0">Nombre botón</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="boton" id="boton" maxlength="100" value="<?php echo $resultado_registros_botones[0][2]; ?>" readonly>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-9 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-5 mb-1">
                          <?php require_once(ROOT.'includes/_search.php'); ?>
                        </div>
                        <div class="col-md-7 mb-1 text-end">
                          <a href="programas_boton?pagina=1&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Regresar">
                            <i class="fas fa-arrow-left btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Regresar</span>
                          </a>
                          <a href="#" onclick="open_modal_ordenar('<?php echo base64_encode($id_boton); ?>');" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Ordenar">
                            <i class="fas fa-arrow-up-1-9 btn-icon-prepend me-0 font-size-12"></i><span class="d-none d-lg-inline"></span>
                          </a>
                          <a href="programas_boton_preguntas_crear?pagina=1&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>&btn=<?php echo base64_encode($id_boton); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear pregunta">
                            <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear pregunta</span>
                          </a>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive table-fixed" id="headerFixTable">
                              <table class="table table-hover table-bordered table-striped">
                                <thead>
                                  <tr>
                                    <th class="px-1 py-2" style="width: 55px;"></th>
                                    <th class="px-1 py-2">Orden</th>
                                    <th class="px-1 py-2">Estado</th>
                                    <th class="px-1 py-2">Pregunta</th>
                                    <th class="px-1 py-2">Respuesta</th>
                                    <th class="px-1 py-2">Palabras Claves</th>
                                    <th class="px-1 py-2">Usuario Registro</th>
                                    <th class="px-1 py-2">Fecha Registro</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                                  <tr>
                                    <td class="p-1 text-center">
                                        <a href="programas_boton_preguntas_editar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>&btn=<?php echo base64_encode($id_boton); ?>&pre=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                                        <?php if($permisos_usuario=="Administrador"): ?>
                                          <a href="programas_boton_preguntas_eliminar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>&btn=<?php echo base64_encode($id_boton); ?>&pre=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Eliminar"><i class="fas fa-trash-alt font-size-11"></i></a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][3]; ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][7]; ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][4]; ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo addLink($resultado_registros[$i][5]); ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][6]; ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][14]; ?></td>
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
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- MODAL ORDENAR -->
      <div class="modal fade" id="modal-ordenar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Ordenar preguntas</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="close_modal_ordenar();" aria-label="Close"></button>
            </div>
            <div class="modal-body-ordenar">
              
            </div>
            <div class="modal-footer">
              <button class="btn btn-primary btn-corp" data-bs-toggle="modal" onclick="close_modal_ordenar();" data-bs-dismiss="modal">Aceptar</button>
            </div>
          </div>
        </div>
      </div>
      <!-- MODAL ORDENAR -->
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <script type="text/javascript">
    function open_modal_ordenar(id_registro) {
        var myModal = new bootstrap.Modal(document.getElementById("modal-ordenar"), {});
        $('.modal-body-ordenar').load('programas_boton_preguntas_ordenar.php?id='+id_registro,function(){
            myModal.show();
        });
    }

    function close_modal_ordenar() {
        var myModal = new bootstrap.Modal(document.getElementById("modal-ordenar"), {});
        $('.modal-body-ordenar').html('');
        window.location.reload();
    }
  </script>
</body>
</html>