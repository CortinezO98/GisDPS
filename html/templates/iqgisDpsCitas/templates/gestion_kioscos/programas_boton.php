<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Kioscos";
  $subtitle = "Programas | Botones";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="programas?pagina=".$pagina."&id=".$filtro_permanente;

    unset($_SESSION[APP_SESSION.'_registro_creado_boton']);

    $consulta_string="SELECT `gkp_id`, `gkp_titulo`, `gkp_imagen`, `gkp_estado`, `gkp_registro_usuario`, `gkp_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_kioscos_programas` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_programas`.`gkp_registro_usuario`=TU.`usu_id` WHERE `gkp_id`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
    
    $consulta_string_botones="SELECT `gkpb_id`, `gkpb_programa`, `gkpb_nombre`, `gkpb_tipo`, `gkpb_estado`, `gkpb_url`, `gkpb_registro_usuario`, `gkpb_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_kioscos_programas_boton` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_programas_boton`.`gkpb_registro_usuario`=TU.`usu_id` WHERE `gkpb_programa`=?";
    $consulta_registros_botones = $enlace_db->prepare($consulta_string_botones);
    $consulta_registros_botones->bind_param("s", $id_registro);
    $consulta_registros_botones->execute();
    $resultado_registros_botones = $consulta_registros_botones->get_result()->fetch_all(MYSQLI_NUM);
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
                              <input type="text" class="form-control form-control-sm font-size-11" name="programa" id="programa" maxlength="100" value="<?php echo $resultado_registros[0][1]; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <img src="<?php echo $resultado_registros[0][2]; ?>" style="width: 150px; height: 150px; border-radius: 0px;">
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
                        <div class="col-md-3 mb-1">
                        </div>
                        <div class="col-md-9 mb-1 text-end">
                          <a href="programas?pagina=1&id=<?php echo $filtro_permanente; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Regresar">
                            <i class="fas fa-arrow-left btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Regresar</span>
                          </a>
                          <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                            <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                          </button>
                          <a href="programas_boton_crear?pagina=1&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear botón">
                            <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear botón</span>
                          </a>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive table-fixed" id="headerFixTable">
                              <table class="table table-hover table-bordered table-striped">
                                <thead>
                                  <tr>
                                    <th class="px-1 py-2" style="width: 55px;"></th>
                                    <th class="px-1 py-2">Estado</th>
                                    <th class="px-1 py-2">Nombre</th>
                                    <th class="px-1 py-2">Tipo</th>
                                    <th class="px-1 py-2">Url</th>
                                    <th class="px-1 py-2">Usuario Registro</th>
                                    <th class="px-1 py-2">Fecha Registro</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php for ($i=0; $i < count($resultado_registros_botones); $i++): ?>
                                  <tr>
                                    <td class="p-1 text-center">
                                        <a href="programas_boton_editar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>&btn=<?php echo base64_encode($resultado_registros_botones[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                                        <?php if($resultado_registros_botones[$i][3]=="Preguntas"): ?>
                                          <a href="programas_boton_preguntas?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($id_registro); ?>&btn=<?php echo base64_encode($resultado_registros_botones[$i][0]); ?>" class="btn btn-dark btn-icon px-1 py-1 mb-1" title="Preguntas"><i class="fas fa-list-ol font-size-11"></i></a>
                                        <?php endif; ?>
                                        <?php if($permisos_usuario=="Administrador"): ?>
                                          <!-- <a href="programas_eliminar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&reg=<?php echo base64_encode($resultado_registros_botones[$i][0]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Eliminar"><i class="fas fa-trash-alt font-size-11"></i></a> -->
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros_botones[$i][4]; ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros_botones[$i][2]; ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros_botones[$i][3]; ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros_botones[$i][5]; ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros_botones[$i][8]; ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros_botones[$i][7]; ?></td>
                                  </tr>
                                  <?php endfor; ?>
                                </tbody>
                              </table>
                              <?php if(count($resultado_registros_botones)==0): ?>
                                <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
                              <?php endif; ?>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- modal reportes -->
        <?php require_once('programas_boton_reporte.php'); ?>
        <!-- modal -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>