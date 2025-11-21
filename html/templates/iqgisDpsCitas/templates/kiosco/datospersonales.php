<?php
    //Cargamos librerias
    session_start();
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set("session.cookie_lifetime","28800");
    ini_set("session.gc_maxlifetime","28800");
    require_once('../../app/config/config.php');

    //Si sesion esta iniciada se redirige al contenido, sino muestra index de logueo//
    if(!isset($_SESSION[APP_SESSION.'_session_usu_id']) OR $_SESSION[APP_SESSION.'_session_usu_id']==null OR $_SESSION[APP_SESSION.'_session_usu_id']==""){
      header("Location:https://prosperidadsocial.gov.co/");
    }
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

    if ($tipo=='preguntas') {
        if(isset($_POST["form_datos"])){
            $gkc_kiosco='1';
            $tipo_documento=validar_input($_POST['tipo_documento']);
            $numero_identificacion=validar_input($_POST['numero_identificacion']);
            $nombres=validar_input($_POST['nombres']);
            $correo=validar_input($_POST['correo']);
            $celular=validar_input($_POST['celular']);
            $fijo=validar_input($_POST['fijo']);

            if (isset($_POST['preferencial'])) {
              $preferencial=$_POST['preferencial'];
            } else {
              $preferencial=array();
            }

            $preferencial_insert=validar_input(implode(';', $preferencial));

            if (isset($_POST['poblacional'])) {
              $poblacional=$_POST['poblacional'];
            } else {
              $poblacional=array();
            }

            $poblacional_insert=validar_input(implode(';', $poblacional));

            $preferencial=$preferencial_insert;
            $poblacional=$poblacional_insert;
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
         
            if ($tipo_documento!="" AND $numero_identificacion!="" AND $nombres!="" AND $correo!="" AND $celular!="" AND $autoriza!="" AND $captcha_response) {
                
                $array_datos['tipo_documento']=$tipo_documento;
                $array_datos['numero_identificacion']=$numero_identificacion;
                $array_datos['nombres']=$nombres;
                $array_datos['correo']=$correo;
                $array_datos['celular']=$celular;
                $array_datos['fijo']=$fijo;
                $array_datos['preferencial']=$preferencial;
                $array_datos['poblacional']=$poblacional;
                $array_datos['autoriza']=$autoriza;

                $datos=serialize($array_datos);
                $datos=urlencode($datos);

                // Prepara la sentencia
                $sentencia_insert_consulta = $enlace_db->prepare("INSERT INTO `gestion_kioscos_consultas`(`gkc_kiosco`, `gkc_boton`, `gkc_datos_tipo_documento`, `gkc_datos_numero_identificacion`, `gkc_datos_nombres`, `gkc_datos_correo`, `gkc_datos_celular`, `gkc_datos_fijo`, `gkc_datos_autoriza`, `gkc_atencion_preferencial`, `gkc_informacion_poblacional`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");

                // Agrega variables a sentencia preparada
                $sentencia_insert_consulta->bind_param('sssssssssss', $gkc_kiosco, $id_boton, $tipo_documento, $numero_identificacion, $nombres, $correo, $celular, $fijo, $autoriza, $preferencial, $poblacional);
                  
                if ($sentencia_insert_consulta->execute()) {
                    header("Location:preguntas?t=".base64_encode($tipo)."&id=".base64_encode($id_boton)."&d=".$datos);

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
    </style>
  <link rel="shortcut icon" href="favicon-PROSPERIDADSOCIAL-min-32x32.png" />
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="sidebar-dark sidebar-icon-only" style="background-color: #FFCD00 !important;">
  <div class="container-scroller">
    <div class="pt-0">
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
                  </div>
                </div>
                <div class="col-lg-4 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                            <form name="form_datos" id="form_datos" method="POST" action="">
                            <div class="row justify-content-center">
                                <div class="col-md-12 py-2">
                                    <div class="row">
                                        <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                                        <?php if ($tipo=='preguntas'): ?>
                                            <div class="col-md-12 py-2 text-center fw-bold">
                                                Hola, soy tu asistente virtual. Por favor diligencia el formulario para poder continuar.
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
                                            <div class="col-md-12 pt-0 pb-1">
                                                <div class="form-group mt-1 mb-0">
                                                    <label for="preferencial" class="my-0">Por favor indique si presenta o no, alguna de las siguientes características *</label>
                                                    <select class="selectpicker form-control form-control-sm form-select" title="Seleccione" data-live-search="false" data-container="body" name="preferencial[]" id="preferencial" required multiple>
                                                      <option value="Niñas/ Niños / Adolescentes" <?php if(isset($_POST["form_datos"]) AND $preferencial=='Niñas/ Niños / Adolescentes'){ echo 'selected'; } ?>>Niñas/ Niños / Adolescentes</option>
                                                      <option value="Adulto Mayor" <?php if(isset($_POST["form_datos"]) AND $preferencial=='Adulto Mayor'){ echo 'selected'; } ?>>Adulto Mayor</option>
                                                      <option value="Población Desplazada" <?php if(isset($_POST["form_datos"]) AND $preferencial=='Población Desplazada'){ echo 'selected'; } ?>>Población Desplazada</option>
                                                      <option value="Mujer Gestante" <?php if(isset($_POST["form_datos"]) AND $preferencial=='Mujer Gestante'){ echo 'selected'; } ?>>Mujer Gestante</option>
                                                      <option value="Persona con Discapacidad" <?php if(isset($_POST["form_datos"]) AND $preferencial=='Persona con Discapacidad'){ echo 'selected'; } ?>>Persona con Discapacidad</option>
                                                      <option value="Víctima del Conflicto Armado" <?php if(isset($_POST["form_datos"]) AND $preferencial=='Víctima del Conflicto Armado'){ echo 'selected'; } ?>>Víctima del Conflicto Armado</option>
                                                      <option value="Campesinos y campesinas" <?php if(isset($_POST["form_datos"]) AND $preferencial=='Campesinos y campesinas'){ echo 'selected'; } ?>>Campesinos y campesinas</option>
                                                      <option value="Periodista" <?php if(isset($_POST["form_datos"]) AND $preferencial=='Periodista'){ echo 'selected'; } ?>>Periodista</option>
                                                      <option value="Ninguna de las anteriores" <?php if(isset($_POST["form_datos"]) AND $preferencial=='Ninguna de las anteriores'){ echo 'selected'; } ?>>Ninguna de las anteriores</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-0 pb-1">
                                                <div class="form-group mt-1 mb-0">
                                                    <label for="poblacional" class="my-0">Por favor indique si se identifica o no con alguno de los siguientes grupos poblacionales *</label>
                                                    <select class="selectpicker form-control form-control-sm form-select" title="Seleccione" data-live-search="false" data-container="body" name="poblacional[]" id="poblacional" required multiple>
                                                      <option value="Gitano (Población Rom)" <?php if(isset($_POST["form_datos"]) AND $poblacional=='Gitano (Población Rom)'){ echo 'selected'; } ?>>Gitano (Población Rom)</option>
                                                      <option value="Indígena" <?php if(isset($_POST["form_datos"]) AND $poblacional=='Indígena'){ echo 'selected'; } ?>>Indígena</option>
                                                      <option value="Mestizo" <?php if(isset($_POST["form_datos"]) AND $poblacional=='Mestizo'){ echo 'selected'; } ?>>Mestizo</option>
                                                      <option value="Afrocolombiano" <?php if(isset($_POST["form_datos"]) AND $poblacional=='Afrocolombiano'){ echo 'selected'; } ?>>Afrocolombiano</option>
                                                      <option value="Raizal" <?php if(isset($_POST["form_datos"]) AND $poblacional=='Raizal'){ echo 'selected'; } ?>>Raizal</option>
                                                      <option value="Palenquero" <?php if(isset($_POST["form_datos"]) AND $poblacional=='Palenquero'){ echo 'selected'; } ?>>Palenquero</option>
                                                      <option value="Comunidades negras" <?php if(isset($_POST["form_datos"]) AND $poblacional=='Comunidades negras'){ echo 'selected'; } ?>>Comunidades negras</option>
                                                      <option value="LGTBI" <?php if(isset($_POST["form_datos"]) AND $poblacional=='LGTBI'){ echo 'selected'; } ?>>LGTBI</option>
                                                      <option value="Otro Grupo" <?php if(isset($_POST["form_datos"]) AND $poblacional=='Otro Grupo'){ echo 'selected'; } ?>>Otro Grupo</option>
                                                      <option value="Ninguna de las anteriores" <?php if(isset($_POST["form_datos"]) AND $poblacional=='Ninguna de las anteriores'){ echo 'selected'; } ?>>Ninguna de las anteriores</option>
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
</body>
</html>