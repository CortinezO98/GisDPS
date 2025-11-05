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
//      header("Location:https://prosperidadsocial.gov.co/");
//    }
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
    $id_boton=base64_decode($_GET['id']);

    $consulta_string="SELECT `gkp_id`, `gkp_titulo`, `gkp_imagen`, `gkp_estado`, `gkp_registro_usuario`, `gkp_registro_fecha` FROM `gestion_kioscos_programas` WHERE `gkp_estado`='Activo' AND `gkp_id`=? ORDER BY `gkp_titulo`";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_boton);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_botones="SELECT `gkpb_id`, `gkpb_programa`, `gkpb_nombre`, `gkpb_tipo`, `gkpb_estado`, `gkpb_url`, `gkpb_registro_usuario`, `gkpb_registro_fecha` FROM `gestion_kioscos_programas_boton` WHERE `gkpb_estado`='Activo' AND `gkpb_programa`=?";
    $consulta_registros_botones = $enlace_db->prepare($consulta_string_botones);
    $consulta_registros_botones->bind_param("s", $id_boton);
    $consulta_registros_botones->execute();
    $resultado_registros_botones = $consulta_registros_botones->get_result()->fetch_all(MYSQLI_NUM);
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
    <div class=" pt-0">
      <!-- main-panel -->
      <div class="">
        <div class="content-wrapper pt-2" style="background-color: #FFCD00;">
          <div class="row">
            <div class="col-sm-12">
              <div class="row justify-content-center">
                <div class="col-lg-3">
                  <div class="row flex-grow">
                    <div class="col-md-5 py-2 text-center fw-bold" style="color: #FFF;">
                      <img src="<?php echo IMAGES; ?>logo-cliente.png?v=1" class="img-fluid">
                    </div>
                    <div class="col-md-7 py-2 text-center fw-bold" style="color: #FFF;">
                      <img src="<?php echo IMAGES; ?>logo-cliente-ps.png?v=1" class="img-fluid">
                    </div>
                    <div class="col-12 col-lg-12" style="background-color: #FFCD00; border-radius: 50px;">
                      <div class="">
                        <div class="">
                            <div class="row justify-content-center">
                                <div class="col-md-12 p-2">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <img src="<?php echo $resultado_registros[0][2]; ?>" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> 
                <div class="col-lg-6 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-md-12 py-2">
                                    <div class="py-4 text-center fw-bold">
                                        Por favor seleccione una de las opciones:
                                    </div>
                                    <?php for ($i=0; $i < count($resultado_registros_botones); $i++): ?>
                                      <?php if($resultado_registros_botones[$i][3]=='Preguntas'): ?>
                                        <a href="preguntas?t=<?php echo base64_encode('preguntas'); ?>&id=<?php echo base64_encode($resultado_registros_botones[$i][0]); ?>" class="btn btn-primary d-block mb-1"><?php echo $resultado_registros_botones[$i][2]; ?></a>
                                      <?php elseif($resultado_registros_botones[$i][3]=='Url'): ?>
                                        <a href="<?php echo $resultado_registros_botones[$i][5]; ?>" target="_blank" class="btn btn-primary d-block mb-1"><span class="fas fa-arrow-up-right-from-square"></span> <?php echo $resultado_registros_botones[$i][2]; ?></a>
                                      <?php endif; ?>
                                    <?php endfor; ?>
                                    <a href="inicio" class="btn btn-dark d-block mt-4"><span class="fas fa-home"></span> Ir al menú principal</a>
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
