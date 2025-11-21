<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Configuración";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $parametro=validar_input($_GET['par']);
  $title = "Canal Escrito";
  $subtitle = "Configuración | ".$parametro." | Editar";
  $pagina=validar_input($_GET['pagina']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="configuracion?pagina=".$pagina."&id=".$filtro_permanente."&par=".$parametro;

  if(isset($_POST["guardar_registro"])){
    $ceco_valor=validar_input($_POST['ceco_valor']);
    $ceco_estado=validar_input($_POST['ceco_estado']);
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_ce_configuracion` SET `ceco_valor`=?, `ceco_estado`=? WHERE `ceco_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sss', $ceco_valor, $ceco_estado, $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();
    
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

  $consulta_string="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha`, TUA.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos` FROM `gestion_ce_configuracion` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_ce_configuracion`.`ceco_actualiza_usuario`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TUR ON `gestion_ce_configuracion`.`ceco_registro_usuario`=TUR.`usu_id` WHERE `ceco_id`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-lg-6 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="ceco_formulario" class="my-0">Formulario</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="ceco_formulario" id="ceco_formulario" maxlength="100" value="<?php echo $resultado_registros[0][1]; ?>" required disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="ceco_campo" class="my-0">Campo</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="ceco_campo" id="ceco_campo" maxlength="100" value="<?php echo $resultado_registros[0][2]; ?>" required disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="ceco_valor" class="my-0">Valor</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="ceco_valor" id="ceco_valor" maxlength="100" value="<?php echo $resultado_registros[0][3]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ceco_estado">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="ceco_estado" id="ceco_estado" required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][4]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][4]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Guardar</button>
                                <?php if(isset($_POST["guardar_registro"])): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php endif; ?>
                                <?php if(!isset($_POST["guardar_registro"])): ?>
                                    <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');">Cancelar</button>
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
          </form>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>