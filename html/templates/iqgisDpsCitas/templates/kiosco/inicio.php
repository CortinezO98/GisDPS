<?php
    //Cargamos librerias
    session_start();
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set("session.cookie_lifetime","28800");
    ini_set("session.gc_maxlifetime","28800");
    require_once('../../app/config/config.php');

    //Si sesion esta iniciada se redirige al contenido, sino muestra index de logueo//
//    if(!isset($_SESSION[APP_SESSION.'_session_usu_id']) OR $_SESSION[APP_SESSION.'_session_usu_id']==null OR $_SESSION[APP_SESSION.'_session_usu_id']==""){
  //    header("Location:https://prosperidadsocial.gov.co/");
    //}
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
    $consulta_string="SELECT `gkp_id`, `gkp_titulo`, `gkp_imagen`, `gkp_estado`, `gkp_registro_usuario`, `gkp_registro_fecha` FROM `gestion_kioscos_programas` WHERE `gkp_estado`='Activo' ORDER BY `gkp_orden`";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <title>Prosperidad Social - Gobierno de Colombia</title>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <style type="text/css">
    .card-header {
      background-color: #005AC6;
      color: #FFF;
    }

    .btn-primary {
      background-color: #F42F63;
      border-color: #F42F63;
    }

    .btn-primary:hover {
      background-color: #F42F63;
      border-color: #F42F63;
    }
  </style>
  <link rel="shortcut icon" href="favicon-PROSPERIDADSOCIAL-min-32x32.png" />
</head>

<body class="sidebar-dark sidebar-icon-only" style="background-color: #FFCD00 !important;">
  <div class="container-scroller">
    <div class="container-fluid pt-0">
      <!-- main-panel -->
      <div class="">
        <div class="content-wrapper pt-2" style="background-color: #FFCD00 !important;">
          <div class="row">
            <div class="col-sm-12">
              <div class="row justify-content-center">
                <div class="col-lg-12 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded" style="background-color: #FFCD00;">
                        <div class="card-body">
                          <div class="row justify-content-center">
                              <div class="col-md-12 pt-0">
                                  <div class="row justify-content-between">
                                      <div class="col-md-3 py-0 text-center fw-bold" style="color: #FFF;">
                                        <img src="<?php echo IMAGES; ?>logo-cliente.png?v=1" class="img-fluid">
                                      </div>
                                      <div class="col-md-4 py-0 text-center fw-bold" style="color: #FFF;">
                                        <img src="<?php echo IMAGES; ?>logo-cliente-ps.png?v=1" class="img-fluid">
                                      </div>
                                  </div>
                                  <div class="row justify-content-center">
                                      <div class="col-md-9 pb-2 pt-0 text-center fw-bold">
                                          Bienvenido(a) al menú de información y atención en Quiosco.<br><br>
                                          Seleccione la opción que desea consultar:
                                      </div>
                                  </div>
                                  <div class="row justify-content-center">
                                      <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                                        <div class="col-md-2 mb-3">
                                            <a href="programa?id=<?php echo base64_encode($resultado_registros[$i][0]); ?>">
                                              <img src="<?php echo $resultado_registros[$i][2]; ?>" class="img-fluid">
                                            </a>
                                        </div>
                                      <?php endfor; ?>
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
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>
