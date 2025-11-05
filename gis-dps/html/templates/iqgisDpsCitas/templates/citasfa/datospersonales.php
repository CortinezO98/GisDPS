<?php
    //Validación de permisos del usuario para el módulo
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $tipo=validar_input(base64_decode($_GET['t']));
    
    if ($tipo=='agendar') {
        if(isset($_POST["form_datos"])){
            $tipo_documento=validar_input($_POST['tipo_documento']);
            $numero_identificacion=validar_input($_POST['numero_identificacion']);
            $nombres=validar_input($_POST['nombres']);
            $correo=validar_input($_POST['correo']);
            $celular=validar_input($_POST['celular']);
            $fijo=validar_input($_POST['fijo']);
            $discapacidad=validar_input($_POST['discapacidad']);
            $tipo_discapacidad=validar_input($_POST['tipo_discapacidad']);
            $autoriza=validar_input($_POST['autoriza']);

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

            if ($discapacidad=='Si') {
                if ($tipo_discapacidad!='') {
                    $valida_discapacidad=1;
                } else {
                    $valida_discapacidad=0;
                }
            } else {
                $valida_discapacidad=1;
            }
         
            if ($tipo_documento!="" AND $numero_identificacion!="" AND $nombres!="" AND $correo!="" AND $celular!="" AND $discapacidad!="" AND $valida_discapacidad AND $autoriza!="" AND $captcha_response) {
                
                $array_datos['tipo_documento']=$tipo_documento;
                $array_datos['numero_identificacion']=$numero_identificacion;
                $array_datos['nombres']=$nombres;
                $array_datos['correo']=$correo;
                $array_datos['celular']=$celular;
                $array_datos['fijo']=$fijo;
                $array_datos['discapacidad']=$discapacidad;
                $array_datos['tipo_discapacidad']=$tipo_discapacidad;
                $array_datos['autoriza']=$autoriza;

                $datos=serialize($array_datos);
                $datos=urlencode($datos);

                header("Location:agendamiento?t=".base64_encode($tipo)."&d=".$datos);
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
                <div class="col-lg-4 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                            <form name="form_datos" id="form_datos" method="POST" action="">
                            <div class="row justify-content-center">
                                <div class="col-md-12 py-2">
                                    <div class="row">
                                        <div class="col-md-12 pt-0 px-0 text-center mb-2">
                                            <img src="<?php echo IMAGES; ?>logo-cliente.png" class="img-fluid">
                                        </div>
                                        <div class="card-header mb-2">
                                            Agenda tu cita
                                        </div>
                                        <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                                        <?php if ($tipo=='agendar'): ?>
                                            <div class="col-md-12 py-2 text-center fw-bold">
                                                Hola, soy tu asistente virtual. Por favor diligencia el formulario para poder atender tu agendamiento de cita.
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mt-1 mb-0">
                                                    <label for="tipo_documento" class="my-0">Tipo de documento *</label>
                                                    <select class="form-control form-control-sm form-select" name="tipo_documento" id="tipo_documento" required>
                                                      <option value="">Seleccione</option>
                                                      <option value="CC" <?php if(isset($_POST["form_datos"]) AND $tipo_documento=='CC'){ echo 'selected'; } ?>>Cédula de Ciudadanía</option>
                                                      <option value="CE" <?php if(isset($_POST["form_datos"]) AND $tipo_documento=='CE'){ echo 'selected'; } ?>>Cédula de Extranjería</option>
                                                      <option value="NUIP" <?php if(isset($_POST["form_datos"]) AND $tipo_documento=='NUIP'){ echo 'selected'; } ?>>NUIP - Número Único de Identificación Personal</option>
                                                      <option value="TI" <?php if(isset($_POST["form_datos"]) AND $tipo_documento=='TI'){ echo 'selected'; } ?>>Tarjeta de Identidad</option>
                                                      <option value="PEP" <?php if(isset($_POST["form_datos"]) AND $tipo_documento=='PEP'){ echo 'selected'; } ?>>Permiso Especial de Permanencia</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-0 pb-1">
                                                <div class="form-group mt-1 mb-0">
                                                  <label for="numero_identificacion" class="my-0">Número de identificación *</label>
                                                  <input type="text" class="form-control form-control-sm" name="numero_identificacion" id="numero_identificacion" maxlength="50" value="<?php if(isset($_POST["form_datos"])){ echo $numero_identificacion; } ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-0 pb-1">
                                                <div class="form-group mt-1 mb-0">
                                                  <label for="nombres" class="my-0">Nombres y apellidos *</label>
                                                  <input type="text" class="form-control form-control-sm" name="nombres" id="nombres" maxlength="100" value="<?php if(isset($_POST["form_datos"])){ echo $nombres; } ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-0 pb-1">
                                                <div class="form-group mt-1 mb-0">
                                                  <label for="correo" class="my-0">Correo electrónico *</label>
                                                  <input type="email" class="form-control form-control-sm" name="correo" id="correo" maxlength="100" value="<?php if(isset($_POST["form_datos"])){ echo $correo; } ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-0 pb-1">
                                                <div class="form-group mt-1 mb-0">
                                                  <label for="celular" class="my-0">Número celular *</label>
                                                  <input type="text" class="form-control form-control-sm" name="celular" id="celular" minlength="10" maxlength="10" value="<?php if(isset($_POST["form_datos"])){ echo $celular; } ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-0 pb-1">
                                                <div class="form-group mt-1 mb-0">
                                                  <label for="fijo" class="my-0">Número fijo</label>
                                                  <input type="text" class="form-control form-control-sm" name="fijo" id="fijo" minlength="7" maxlength="10" value="<?php if(isset($_POST["form_datos"])){ echo $fijo; } ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mt-1 mb-0">
                                                    <label for="discapacidad" class="my-0">¿Presenta discapacidad? *</label>
                                                    <select class="form-control form-control-sm form-select" name="discapacidad" id="discapacidad" required onchange="validar_discapacidad();">
                                                      <option value="">Seleccione</option>
                                                      <option value="Si" <?php if(isset($_POST["form_datos"]) AND $discapacidad=='Si'){ echo 'selected'; } ?>>Si</option>
                                                      <option value="No" <?php if(isset($_POST["form_datos"]) AND $discapacidad=='No'){ echo 'selected'; } ?>>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 d-none" id="tipo_discapacidad_div">
                                                <div class="form-group mt-1 mb-0">
                                                    <label for="tipo_discapacidad" class="my-0">Discapacidad *</label>
                                                    <select class="form-control form-control-sm form-select" name="tipo_discapacidad" id="tipo_discapacidad" disabled required>
                                                      <option value="">Seleccione</option>
                                                      <option value="Discapacidad Física" <?php if(isset($_POST["form_datos"]) AND $tipo_discapacidad=='Discapacidad Física'){ echo 'selected'; } ?>>Discapacidad Física</option>
                                                      <option value="Discapacidad Visual" <?php if(isset($_POST["form_datos"]) AND $tipo_discapacidad=='Discapacidad Visual'){ echo 'selected'; } ?>>Discapacidad Visual</option>
                                                      <option value="Sordoceguera" <?php if(isset($_POST["form_datos"]) AND $tipo_discapacidad=='Sordoceguera'){ echo 'selected'; } ?>>Sordoceguera</option>
                                                      <option value="Discapacidad intelectual" <?php if(isset($_POST["form_datos"]) AND $tipo_discapacidad=='Discapacidad intelectual'){ echo 'selected'; } ?>>Discapacidad intelectual</option>
                                                      <option value="Discapacidad Psicosocial (mental)" <?php if(isset($_POST["form_datos"]) AND $tipo_discapacidad=='Discapacidad Psicosocial (mental)'){ echo 'selected'; } ?>>Discapacidad Psicosocial (mental)</option>
                                                      <option value="Discapacidad Múltiple" <?php if(isset($_POST["form_datos"]) AND $tipo_discapacidad=='Discapacidad Múltiple'){ echo 'selected'; } ?>>Discapacidad Múltiple</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-check form-switch ps-5 pe-1">
                                                    <input class="form-check-input px-0" type="checkbox" name="autoriza" id="autoriza" value="Acepto" required <?php if(isset($_POST["form_datos"]) AND $autoriza=='Acepto'){ echo 'checked'; } ?>>
                                                    <label class="form-check-label" for="">¿Aceptas que tus datos personales sean utilizados para la gestión de tu trámite? <a href="http://centrodedocumentacion.prosperidadsocial.gov.co/2020/Transparencia/Politica-de-Tratamiento-de-Datos-Personales-v2.1.pdf" target="_blank">Más información Política de Tratamiento de Datos</a></label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2 mx-2">
                                                <center><div class="g-recaptcha" data-sitekey="6Lc5fUQiAAAAAMzfNWy9JYn50jUnUQjwAdNNArCO" data-callback="correctCaptcha"></div></center>
                                                <?php if(isset($_POST["form_datos"]) AND $_POST["g-recaptcha-response"]==''): ?>
                                                    <div id="response" class="col-md-12"><p class='alert alert-danger p-1'>Por favor valide el Captcha!</p></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button class="btn btn-success float-end ms-1 submitForm" type="submit" name="form_datos">Continuar</button>
                                                    <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $ruta_cancelar_finalizar; ?>');">Cancelar</button>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="card text-center">
                                                  <div class="card-header">
                                                    Error de formulario
                                                  </div>
                                                  <div class="card-body">
                                                    <p class="card-text">Formulario no válido, por favor intente nuevamente</p>
                                                    <div class="form-group">
                                                        <a href="inicio" class="btn btn-primary">Aceptar</a>
                                                    </div>
                                                  </div>
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
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
  <script type="text/javascript">
      function validar_discapacidad(){
          var discapacidad_opcion = document.getElementById("discapacidad");
          var discapacidad = discapacidad_opcion.options[discapacidad_opcion.selectedIndex].value;

          if(discapacidad=="Si") {
              $("#tipo_discapacidad_div").removeClass('d-none').addClass('d-block');
              var tipo_discapacidad = document.getElementById('tipo_discapacidad').disabled=false;
          } else {
              $("#tipo_discapacidad_div").removeClass('d-block').addClass('d-none');
              var tipo_discapacidad = document.getElementById('tipo_discapacidad').disabled=true;
          }
      }
  </script>
</body>
</html>