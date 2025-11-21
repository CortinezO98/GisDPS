<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Traslado IVA";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Traslado IVA";
  $subtitle = "Casos | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="casos?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);



  if(isset($_POST["guardar_registro"])){
    $estado=validar_input($_POST['estado']);
    $no_novedad=validar_input($_POST['no_novedad']);
    $observaciones=validar_input($_POST['observaciones']);
    
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_traslado_iva` SET `gti_estado`=?,`gti_responsable`=?,`gti_numero_novedad`=?,`gti_observaciones`=?,`gti_fecha_gestion`=? WHERE `gti_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('ssssss', $estado, $_SESSION[APP_SESSION.'_session_usu_id'], $no_novedad, $observaciones, date('Y-m-d H:i:s'), $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
    
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

  $consulta_string="SELECT `gti_id`, `gti_interaccion_id`, `gti_interaccion_fecha`, `gti_remitente`, `gti_cliente_identificacion`, `gti_cliente_nombre`, `gti_titular_cedula`, `gti_titular_fecha_expedicion`, `gti_beneficiario_identificacion`, `gti_link_foto`, `gti_departamento`, `gti_municipio`, `gti_direccion`, `gti_ruta_fichero`, `gti_estado`, `gti_responsable`, `gti_numero_novedad`, `gti_observaciones`, `gti_fecha_gestion`, `gti_registro_fecha`, TU.`usu_nombres_apellidos`, `gti_estado_bloqueo`, `gti_fecha_bloqueo` FROM `gestion_traslado_iva` LEFT JOIN `administrador_usuario` AS TU ON `gestion_traslado_iva`.`gti_responsable`=TU.`usu_id` WHERE `gti_id`=?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  if ($resultado_registros[0][21]) {
    $date1 = new DateTime($resultado_registros[0][22]);
    $date2 = new DateTime("now");
    $diff = $date1->diff($date2);

    $tiempo_bloqueo=( ($diff->days * 24 ) * 60 ) + ( $diff->i );
  }

  if (!$resultado_registros[0][21] AND $resultado_registros[0][14]=='Pendiente') {
    // Prepara la sentencia
    $consulta_actualizar_bloqueo = $enlace_db->prepare("UPDATE `gestion_traslado_iva` SET `gti_estado_bloqueo`='1',`gti_fecha_bloqueo`=? WHERE `gti_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar_bloqueo->bind_param('ss', date('Y-m-d H:i:s'), $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar_bloqueo->execute();
  }

  if ($resultado_registros[0][21] AND $resultado_registros[0][14]=='Pendiente' AND $tiempo_bloqueo>=15) {
    // Prepara la sentencia
    $consulta_actualizar_bloqueo = $enlace_db->prepare("UPDATE `gestion_traslado_iva` SET `gti_estado_bloqueo`='1',`gti_fecha_bloqueo`=? WHERE `gti_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar_bloqueo->bind_param('ss', date('Y-m-d H:i:s'), $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar_bloqueo->execute();
  }
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
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
          <div class="row justify-content-center">
            <?php if (!empty($respuesta_accion)) {echo "<script type='text/javascript'>".$respuesta_accion."</script>";} ?>
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="table-responsive">
                          <table class="table table-bordered table-striped table-hover table-sm">
                              <tbody>
                                  <tr>
                                      <th class="px-1 py-2">Estado</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][14]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Id Interacción</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][1]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Identificación Usuario</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][4]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Nombres y Apellidos</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][5]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Identificación Titular</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][6]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Fecha Expedición</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][7]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Identificación Beneficiario</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][8]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Municipio/Departamento</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][11].' / '.$resultado_registros[0][10]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Dirección</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][12]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Responsable</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][20]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">No. Novedad</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][16]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Fecha Gestión</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][18]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Observaciones</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][17]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Fecha Interacción</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][2]; ?></td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Fecha Registro</th>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][19]; ?></td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <?php if ($resultado_registros[0][21] AND $resultado_registros[0][14]=='Pendiente' AND $tiempo_bloqueo<15): ?>
                          <p class="alert alert-warning p-1 font-size-11">¡El caso se encuentra bloqueado, por favor intente más tarde!</p>
                        <?php else: ?>
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label for="estado">Estado</label>
                                  <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" required>
                                    <option value="">Seleccione</option>
                                    <option value="Pendiente" <?php if($resultado_registros[0][14]=="Pendiente"){ echo "selected"; } ?>>Pendiente</option>
                                    <option value="Cerrado" <?php if($resultado_registros[0][14]=="Cerrado"){ echo "selected"; } ?>>Cerrado</option>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                <label for="no_novedad" class="my-0">No. novedad</label>
                                <input type="text" class="form-control form-control-sm font-size-11" name="no_novedad" id="no_novedad" maxlength="100" value="<?php echo $resultado_registros[0][16]; ?>" required>
                              </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="observaciones" class="my-0">Observaciones</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones" required><?php echo $resultado_registros[0][17]; ?></textarea>
                            </div>
                          </div>

                        <?php endif; ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if ($resultado_registros[0][21] AND $resultado_registros[0][14]=='Pendiente' AND $tiempo_bloqueo<15): ?>

                                <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Guardar</button>
                                <?php endif; ?>
                                <?php if(isset($_POST["guardar_registro"])): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php endif; ?>
                                <?php if(!isset($_POST["guardar_registro"])): ?>
                                    <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');">Cancelar</button>
                                <?php endif; ?>
                                <button class="btn btn-warning float-end me-1" type="button" onClick="open_modal_detalle('<?php echo base64_encode($resultado_registros[0][0]); ?>');"><i class="fas fa-image font-size-11"></i> Ver imagen</button>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
        <!-- content-wrapper ends -->
        <!-- MODAL DETALLE -->
        <div class="modal fade" id="modal-detalle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Imagen</h5>
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
        $('.modal-body-detalle').load('casos_ver_foto.php?reg='+id_registro,function(){
            myModal.show();
        });
    }
  </script>
</body>
</html>