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
//
//    }
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
    $tipo=validar_input(base64_decode($_GET['t']));
    $id_boton=validar_input(base64_decode($_GET['id']));

    $consulta_string_botones="SELECT `gkpb_id`, `gkpb_programa`, `gkpb_nombre`, `gkpb_tipo`, `gkpb_estado`, `gkpb_url`, `gkpb_registro_usuario`, `gkpb_registro_fecha`, TP.`gkp_titulo`, TP.`gkp_imagen` FROM `gestion_kioscos_programas_boton` LEFT JOIN `gestion_kioscos_programas` AS TP ON `gestion_kioscos_programas_boton`.`gkpb_programa`=TP.`gkp_id` WHERE `gkpb_id`=?";
    $consulta_registros_botones = $enlace_db->prepare($consulta_string_botones);
    $consulta_registros_botones->bind_param("s", $id_boton);
    $consulta_registros_botones->execute();
    $resultado_registros_botones = $consulta_registros_botones->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_preguntas="SELECT `gkbp_id`, `gkbp_programa`, `gkbp_boton`, `gkbp_orden`, `gkbp_pregunta`, `gkbp_respuesta`, `gkbp_palabras_claves`, `gkbp_estado`, `gkbp_actualiza_usuario`, `gkbp_actualiza_fecha`, `gkbp_registro_usuario`, `gkbp_registro_fecha` FROM `gestion_kioscos_preguntas` WHERE `gkbp_estado`='Activo' AND `gkbp_boton`=? ORDER BY `gkbp_orden`";
    $consulta_registros_preguntas = $enlace_db->prepare($consulta_string_preguntas);
    $consulta_registros_preguntas->bind_param("s", $id_boton);
    $consulta_registros_preguntas->execute();
    $resultado_registros_preguntas = $consulta_registros_preguntas->get_result()->fetch_all(MYSQLI_NUM);

    function addLink($content) {
        $reg_exUrl = "/.[http|https|ftp|ftps]*\:\/\/.[^$|\s]*/";
        return preg_replace($reg_exUrl, " <a href='$0' target='_blank'>$0<span class='fas fa-arrow-up-right-from-square'></span></a>", $content);
    }
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
                                            <img src="<?php echo $resultado_registros_botones[0][9]; ?>" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                    <a href="inicio" class="btn btn-dark d-block mt-3"><span class="fas fa-home"></span> Ir al menú principal</a>
                  </div>
                </div> 
                <div class="col-lg-9 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-md-12 py-2">
                                    <div class="py-4 text-center fw-bold">
                                        Por favor seleccione una de las opciones:
                                    </div>
                                    <div class="accordion" id="accordionPanelsStayOpenExample">
                                      <?php for ($i=0; $i < count($resultado_registros_preguntas); $i++): ?>
                                        <div class="accordion-item">
                                          <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $i; ?>">
                                            <button class="accordion-button collapsed btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $i; ?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $i; ?>">
                                              <?php echo $resultado_registros_preguntas[$i][4]; ?>
                                            </button>
                                          </h2>
                                          <div id="panelsStayOpen-collapse<?php echo $i; ?>" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $i; ?>">
                                            <div class="accordion-body">
                                              <?php echo nl2br(addLink($resultado_registros_preguntas[$i][5])); ?>
                                            </div>
                                          </div>
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
