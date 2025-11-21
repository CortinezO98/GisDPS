<?php
    //Validación de permisos del usuario para el módulo
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $tipo=validar_input(base64_decode($_GET['t']));
    $resultado_registros_reserva=array();
    if ($tipo=='consultar') {
        if(isset($_POST["buscar_cita"])){
            $tipo_documento=validar_input($_POST['tipo_documento']);
            $numero_identificacion=validar_input($_POST['numero_identificacion']);

            $fecha_actual=date('Y-m-d');
            // $hora_actual=date('H:i');
            if ($tipo_documento!="" AND $numero_identificacion!="" AND $_POST['g-recaptcha-response']!="") {
                $captcha_response = true;
                $recaptcha = $_POST['g-recaptcha-response'];
             
                $url = 'https://www.google.com/recaptcha/api/siteverify';
                $data = array(
                    'secret' => '6Lc5fUQiAAAAAP3VxAbOZ3q7QxKIuIbjywi7P1qO',
                    'response' => $recaptcha
                );
                $options = array(
                    'http' => array (
                        'method' => 'POST',
                        'content' => http_build_query($data)
                    )
                );
                $context  = stream_context_create($options);
                $verify = file_get_contents($url, false, $context);
                $captcha_success = json_decode($verify);
                $captcha_response = $captcha_success->success;
             
                if ($captcha_response) {
                    $consulta_string_reserva="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora`, TC.`gca_estado_agenda` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_datos_tipo_documento`=? AND `gcar_datos_numero_identificacion`=? AND `gcar_estado`='Reservada' AND TC.`gca_fecha`>=? ORDER BY `gcar_consecutivo` DESC LIMIT 1";

                    $consulta_registros_reserva = $enlace_db->prepare($consulta_string_reserva);
                    $consulta_registros_reserva->bind_param("sss", $tipo_documento, $numero_identificacion, $fecha_actual);
                    $consulta_registros_reserva->execute();
                    $resultado_registros_reserva = $consulta_registros_reserva->get_result()->fetch_all(MYSQLI_NUM);

                    if (count($resultado_registros_reserva)>0) {
                        $fecha_hora_inicio=date('Y-m-d H:i');

                        $citas_disponibles=0;
                        for ($i=0; $i < count($resultado_registros_reserva); $i++) { 
                            $fecha_cita=$resultado_registros_reserva[$i][15].' '.$resultado_registros_reserva[$i][16];
                            if($fecha_cita>$fecha_hora_inicio) {
                                $citas_disponibles++;
                            }
                        }
                    }
                } else {
                    $respuesta_accion = "<div class='alert alert-danger py-1 font-size-11 col-md-12'>¡Problemas al procesar los datos, por favor verifique e intente nuevamente!</div>";
                }
            } else {
                $respuesta_accion = "<div class='alert alert-danger py-1 font-size-11 col-md-12'>¡Problemas al procesar los datos, por favor verifique e intente nuevamente!</div>";
            }
        }
    }

    /*Enlace para botón finalizar y cancelar*/
    $ruta_cancelar_finalizar="inicio";
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

        .menu-punto {
            border: solid 1px #8C8C8C;
            color: #1F1F1F;
        }

        .menu-punto:hover {
            background-color: #005AC6;
            color: #FFF !important;
        }

        /*.icono {
          color: #005AC6;
        }*/

        a {
            text-decoration: none !important;
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
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="sidebar-dark sidebar-icon-only" style="background-color: #F4F5F7 !important;">
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper pt-0">
      <!-- main-panel -->
      <div class="">
        <div class="content-wrapper pt-2">
          <div class="row">
            <div class="col-sm-12">
              <div class="row justify-content-center">
                <div class="col-lg-5 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                            <form name="buscar_cita" action="" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12 pt-0 px-0 text-center mb-2">
                                        <img src="<?php echo IMAGES; ?>logo-cliente.png" class="img-fluid">
                                    </div>
                                    <div class="card-header mb-2">
                                        Consulta tu cita
                                    </div>
                                    <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                                    <?php if ($tipo=='consultar'): ?>
                                        <?php if(!isset($_POST["buscar_cita"])): ?>
                                            <div class="col-md-12 pt-0 pb-1">
                                                <div class="form-group mt-1 mb-0">
                                                    <label for="tipo_documento" class="my-0">Tipo de documento *</label>
                                                    <select class="form-control form-control-sm form-select" name="tipo_documento" id="tipo_documento" required>
                                                      <option value="">Seleccione</option>
                                                      <option value="CC">Cédula de Ciudadanía</option>
                                                      <option value="CE">Cédula de Extranjería</option>
                                                      <option value="NUIP">NUIP - Número Único de Identificación Personal</option>
                                                      <option value="TI">Tarjeta de Identidad</option>
                                                      <option value="PEP">Permiso Especial de Permanencia</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-0 pb-1 mb-2">
                                                <div class="form-group mt-1 mb-0">
                                                  <label for="numero_identificacion" class="my-0">Número de identificación *</label>
                                                  <input type="text" class="form-control form-control-sm" name="numero_identificacion" id="numero_identificacion" maxlength="50" value="<?php echo $datos_mostrar['numero_identificacion']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2 mx-2">
                                                <center><div class="g-recaptcha" data-sitekey="6Lc5fUQiAAAAAMzfNWy9JYn50jUnUQjwAdNNArCO" data-callback="correctCaptcha"></div></center>
                                            </div>
                                            <div class="col-md-12 mb-1">
                                                <div class="form-group">
                                                    <button class="btn btn-success float-end ms-1" type="submit" name="buscar_cita">Consultar</button>
                                                    <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $ruta_cancelar_finalizar; ?>');">Cancelar</button>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_POST["buscar_cita"]) AND count($resultado_registros_reserva)>0 AND $citas_disponibles>0 AND $captcha_response): ?>
                                            <div class="card text-center">
                                              <div class="card-body">
                                                <p class="card-text">Estimado (a) <b><?php echo $resultado_registros_reserva[0][6]; ?></b>, su cita de radicado <b><?php echo $resultado_registros_reserva[0][0]; ?></b> en el punto <b><?php echo $resultado_registros_reserva[0][12]; ?></b> en la dirección <b><?php echo $resultado_registros_reserva[0][13]; ?></b> el <b><?php echo $array_dias_nombre[date('N', strtotime($resultado_registros_reserva[0][15]))].' '.date('d', strtotime($resultado_registros_reserva[0][15])).' de '.$array_meses[intval(date('m', strtotime($resultado_registros_reserva[0][15])))].' de '.date('Y', strtotime($resultado_registros_reserva[0][15])); ?></b> a las <b><?php echo date('h:i A', strtotime($resultado_registros_reserva[0][16])); ?></b> se encuentra confirmada.</p>
                                                <div class="form-group">
                                                    <a href="inicio" class="btn btn-primary">Aceptar</a>
                                                </div>
                                              </div>
                                            </div>
                                        <?php elseif(isset($_POST["buscar_cita"]) AND $tipo_documento!="" AND $numero_identificacion!="" AND $captcha_response AND $citas_disponibles==0): ?>
                                            <div class="card text-center">
                                              <div class="card-body">
                                                <p class="card-text">No se ha encontrado una cita agendada. Por favor haga clic en "Aceptar" si desea realizar otra consulta, o haga clic en "Agendar cita" si desea programar una cita con uno de nuestros asesores.</p>
                                                <div class="form-group">
                                                    <a href="datospersonales?t=<?php echo base64_encode('agendar'); ?>" class="btn btn-primary">Agendar Cita</a>
                                                    <a href="inicio" class="btn btn-primary">Aceptar</a>
                                                </div>
                                              </div>
                                            </div>
                                        <?php elseif(isset($_POST["buscar_cita"]) AND ($tipo_documento=="" OR $numero_identificacion=="" OR !$captcha_response)): ?>
                                            <div class="card text-center">
                                              <div class="card-body">
                                                <p class="card-text">Formulario no válido, por favor intente nuevamente</p>
                                                <div class="form-group">
                                                    <a href="inicio" class="btn btn-primary">Aceptar</a>
                                                </div>
                                              </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="card text-center">
                                          <div class="card-body">
                                            <p class="card-text">Formulario no válido, por favor intente nuevamente</p>
                                            <div class="form-group">
                                                <a href="inicio" class="btn btn-primary">Aceptar</a>
                                            </div>
                                          </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </form>
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