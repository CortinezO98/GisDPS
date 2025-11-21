<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Calidad-Calculadora Muestral";
    require_once("../../iniciador.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');

    /*VARIABLES*/
    $title = "Calidad";
    $subtitle = "Calculadora Muestral | Configuración - Eliminar Transacciones";
    $id_registro=validar_input(base64_decode($_GET['reg']));
    $fecha_dia=validar_input(base64_decode($_GET['fecha']));
    $mes_calculadora=validar_input($_GET['date']);
    $url_salir="cmuestral_configurar?reg=".base64_encode($id_registro)."&date=".$mes_calculadora;

    if(isset($_POST["eliminar_registro"])){
        $base_transacciones=validar_input($_POST['base_transacciones']);
        if($_SESSION[APP_SESSION.'calculadora_transacciones_eliminado']!=1){
            // Prepara la sentencia
            $sentencia_delete_base = $enlace_db->prepare("DELETE FROM `gestion_calidad_cmuestral_transacciones` WHERE `gcmt_calculadora`=? AND `gcmt_mes`=? AND `gcmt_fecha`=? AND `gcmt_segmento`=?");
            // Agrega variables a sentencia preparada
            $sentencia_delete_base->bind_param('ssss', $id_registro, $mes_calculadora, $fecha_dia, $base_transacciones);
            
            // Prepara la sentencia
            $sentencia_delete_muestras = $enlace_db->prepare("DELETE FROM `gestion_calidad_cmuestral_muestras` WHERE `cmm_calculadora`=? AND `cmm_mes`=? AND `cmm_fecha`=?");
            // Agrega variables a sentencia preparada
            $sentencia_delete_muestras->bind_param('sss', $id_registro, $mes_calculadora, $fecha_dia);

            // Evalua resultado de ejecución sentencia preparada
            if ($sentencia_delete_base->execute() AND $sentencia_delete_muestras->execute()) {
                $_SESSION[APP_SESSION.'calculadora_transacciones_eliminado']=1;
                $respuesta_accion = "alertButton('success', 'Registro eliminado', 'Registro eliminado exitosamente');";
            } else {
                $respuesta_accion = "alertButton('error', 'Error', 'Problemas al eliminar el registro, por favor intente nuevamente');";
            }
        } else {
            $respuesta_accion = "alertButton('success', 'Registro eliminado', 'Registro eliminado exitosamente');";
        }
    }

    $consulta_string_segmento="SELECT `cms_id`, `cms_calculadora`, `cms_nombre_segmento`, `cms_peso` FROM `gestion_calidad_cmuestral_segmento` WHERE `cms_calculadora`=? ORDER BY `cms_nombre_segmento` ASC";
    $consulta_registros_segmento = $enlace_db->prepare($consulta_string_segmento);
    $consulta_registros_segmento->bind_param("s", $id_registro);
    $consulta_registros_segmento->execute();
    $resultado_registros_segmento = $consulta_registros_segmento->get_result()->fetch_all(MYSQLI_NUM);

    $ruta_cancelar_finalizar="gestion_cmuestral_configurar.php?reg=".base64_encode($id_registro)."&date=".$mes_calculadora;
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
            <div class="col-lg-7 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <?php if($_SESSION[APP_SESSION.'calculadora_transacciones_eliminado']!=1): ?>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="mes" class="m-0">Mes</label>
                              <input type="text" class="form-control form-control-sm" name="mes" id="mes" value="<?php echo $mes_calculadora; ?>" readonly required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha" class="m-0">Fecha</label>
                              <input type="text" class="form-control form-control-sm" name="fecha" id="fecha" value="<?php echo $fecha_dia; ?>" readonly required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="base_transacciones" class="m-0">Base transacciones</label>
                                <select class="form-control form-control-sm" name="base_transacciones" id="base_transacciones" <?php if($_SESSION[APP_SESSION.'registro_cargue_base_transacciones']==1) { echo 'disabled'; } ?> required>
                                    <option value="">Seleccione</option>
                                    <option value="Unificada" <?php if(isset($_POST["guardar_registro"]) AND $base_transacciones=='Unificada'){ echo "selected"; } ?>>Unificada</option>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-12">
                            <?php if($_SESSION[APP_SESSION.'calculadora_transacciones_eliminado']==1): ?>
                                <p class="alert alert-danger p-1 font-size-11">¡Registro eliminado exitosamente, haga clic en <b>Finalizar</b> para salir!</p>
                            <?php else: ?>
                                <p class="alert alert-danger p-1 font-size-11">¡El registro será eliminado de forma permanente y no se podrá recuperar, por favor valide antes de continuar!</p>
                            <?php endif; ?>
                        </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <?php if($_SESSION[APP_SESSION.'calculadora_transacciones_eliminado']!=1): ?>
                                <button class="btn btn-warning float-end ms-1" type="submit" name="eliminar_registro">Si, eliminar</button>
                            <?php endif; ?>
                            <?php if($_SESSION[APP_SESSION.'calculadora_transacciones_eliminado']==1): ?>
                                <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                            <?php else: ?>
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