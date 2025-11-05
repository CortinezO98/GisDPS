<?php
    require_once("../iniciador_index.php");
    require_once("../security_session.php");
    unset($_SESSION[APP_SESSION.'_session_password_recovery']);
    unset($_SESSION[APP_SESSION.'_session_password_update']);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    /*VARIABLES*/
    $title = "Inicio";
    $subtitle = "Dashboard";

    //CONSULTA PERMISOS MÓDULOS
        $consulta_string_permisos="SELECT `per_id`, `per_usuario`, `per_modulo`, `per_perfil`, `mod_modulo_nombre` FROM `administrador_usuario_modulo_perfil` LEFT JOIN `administrador_modulo` ON `administrador_usuario_modulo_perfil`.`per_modulo`=`administrador_modulo`.`mod_id` WHERE `per_usuario`=?";
        $consulta_registros_permisos = $enlace_db->prepare($consulta_string_permisos);
        $consulta_registros_permisos->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
        $consulta_registros_permisos->execute();
        $resultado_modulos_usuario = $consulta_registros_permisos->get_result()->fetch_all(MYSQLI_NUM);
        
        unset($_SESSION[APP_SESSION.'_session_modulos']);
        
        for ($i=0; $i < count($resultado_modulos_usuario); $i++) {
            $_SESSION[APP_SESSION.'_session_modulos'][$resultado_modulos_usuario[$i][4]]=$resultado_modulos_usuario[$i][3];
        }
    //CONSULTA PERMISOS MÓDULOS

    //CONSULTA TOP ACTIVIDAD
      $consulta_string_actividad="SELECT `clog_id`, `clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_user_agent`, `clog_remote_addr`, `clog_remote_host`, `clog_script`, `clog_registro_usuario`, `clog_registro_fecha`, TU.`usu_nombres_apellidos` FROM `administrador_log` LEFT JOIN `administrador_usuario` AS TU ON `administrador_log`.`clog_registro_usuario`=TU.`usu_id` WHERE `clog_registro_usuario`=? ORDER BY `clog_registro_fecha` DESC LIMIT 10";
      $consulta_registros_actividad = $enlace_db->prepare($consulta_string_actividad);
      $consulta_registros_actividad->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
      $consulta_registros_actividad->execute();
      $resultado_registros_actividad = $consulta_registros_actividad->get_result()->fetch_all(MYSQLI_NUM);
  //VALIDA EXPIRA CONTRASEÑA
      $consulta_string_phistorial = "SELECT `auc_id`, `auc_usuario`, `auc_contrasena`, `auc_registro_fecha` FROM `administrador_usuario_contrasenas` WHERE `auc_usuario`=? ORDER BY `auc_registro_fecha` DESC LIMIT 1";
      $consulta_registros_phistorial = $enlace_db->prepare($consulta_string_phistorial);
      $consulta_registros_phistorial->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
      $consulta_registros_phistorial->execute();
      $resultado_registros_phistorial = $consulta_registros_phistorial->get_result()->fetch_all(MYSQLI_NUM);
      $fecha_control_pexpira=date("Y-m-d", strtotime("+ 30 day", strtotime($resultado_registros_phistorial[0][3])));
      $fecha_control_paviso=date("Y-m-d", strtotime("+ 20 day", strtotime($resultado_registros_phistorial[0][3])));
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <?php require_once(ROOT.'includes/_head-charts.php'); ?>
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
          <div class="row">
            <div class="col-sm-12">
              <div class="row">
                <div class="col-lg-12 d-flex flex-column">
                  <?php if (date('Y-m-d')>=$fecha_control_paviso): ?>
                    <p class="alert alert-warning py-1">¡Recuerde que su contraseña expira el <b><?php echo date('d/m/Y', strtotime($fecha_control_pexpira)); ?></b>, por favor realice el cambio antes de la fecha indicada! <a href="perfil" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Mi perfil">
                      <i class="fas fa-user btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-lg-inline">Ir a Mi perfil</span>
                    </a></p>
                  <?php endif; ?>
                </div>
                <div class="col-lg-8 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <p class="alert alert-warning">
                              <span class="fas fa-exclamation-triangle font-size-11 p-1"></span> ¡No se encontró dashboard activo!
                          </p>
                          <?php if ($_SESSION[APP_SESSION.'_session_usu_cargo']=="AGENTE QUIOSCO"): ?>
                            <div class="col-md-3 px-1">
                              <a href="<?php echo URL_MENU; ?>/kiosco/inicio" class="btn py-2 px-2 btn-dark btn-icon-text font-size-12 d-block" target="_blank" title="Ir a Quioscos">
                                <i class="fas fa-external-link btn-icon-prepend me-0 me-lg-1 font-size-12"></i> <span class="d-inline">Ir a Quioscos</span>
                              </a>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-md-6 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="card-title card-title-dash">Actividad</h4>
                          </div>
                          <ul class="bullet-line-list">
                            <?php for ($i=0; $i < count($resultado_registros_actividad); $i++): ?>
                            <li>
                              <div class="d-flex justify-content-between">
                                <div>
                                  <span class="text-light-green">
                                    <?php echo log_icono($resultado_registros_actividad[$i][2]); ?>
                                  </span> <?php echo $resultado_registros_actividad[$i][1].' | '.$resultado_registros_actividad[$i][3]; ?></div>
                                <p><?php echo date('H:i d/m', strtotime($resultado_registros_actividad[$i][10])); ?></p>
                              </div>
                            </li>
                            <?php endfor; ?>
                          </ul>
                          <div class="list align-items-center pt-3">
                            <div class="wrapper w-100">
                              
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
        </div>
        <!-- content-wrapper ends -->
        <!-- footer -->
        <?php require_once(ROOT.'includes/_footer.php'); ?>
        <!-- footer -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>