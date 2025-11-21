<?php
    //Validación de permisos del usuario para el módulo
    header("Location:https://www.prosperidadsocial.gov.co/");

    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $cod_familia=validar_input($_GET['gen']);
    if(isset($_POST["guardar_registro"]) AND $cod_familia!=""){
        $documento=validar_input($_POST['documento']);

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
            $consulta_string="SELECT `ocrr_id`, `ocrr_cod_familia`, TDOC.`gord_codfamilia`, TDOC.`gord_nombre`, TOCR.`ocr_documento`, `ocrr_gestion_estado` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr_resultado_documentos` AS TDOC ON `gestion_ocr_resultado`.`ocrr_cod_familia`=TDOC.`gord_codfamilia` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `ocrr_cabezafamilia`='SI' AND (`ocrr_gestion_estado`='Contactado-Pendiente Documentos' OR `ocrr_gestion_estado`='Intento Contacto-Agotado' OR `ocrr_gestion_estado`='Intento Contacto-Fallido' OR `ocrr_gestion_estado`='Documentos Cargados' OR `ocrr_gestion_estado`='Contactado-Pendiente Documentos-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Agotado-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Fallido-Segunda Revisión' OR `ocrr_gestion_estado`='Documentos Cargados-Segunda Revisión' OR `ocrr_gestion_estado`='Aplazado Segunda Revisión' OR `ocrr_gestion_estado`='Segunda Revisión OCR' OR `ocrr_gestion_estado`='Validado-OCR-Segunda Revisión' OR `ocrr_gestion_estado`='Validado-Agente-Segunda Revisión' OR `ocrr_gestion_estado`='Error en la pagina' OR `ocrr_gestion_estado`='Escalado-Validar-Segunda Revisión' OR `ocrr_gestion_estado`='Escalado-Cliente-Segunda Revisión' OR `ocrr_gestion_estado`='Aplazado Tercera Revisión' OR `ocrr_gestion_estado`='Validado-Agente-Tercera Revisión') AND TOCR.`ocr_documento`=?";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            $consulta_registros->bind_param("s", $documento);
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
            
            if (count($resultado_registros)>0) {
                if ($resultado_registros[0][5]=="Documentos Cargados" OR $resultado_registros[0][5]=="Documentos Cargados-Segunda Revisión" OR $resultado_registros[0][5]=="Aplazado Segunda Revisión" OR $resultado_registros[0][5]=="Segunda Revisión OCR" OR $resultado_registros[0][5]=="Validado-OCR-Segunda Revisión" OR $resultado_registros[0][5]=="Validado-Agente-Segunda Revisión" OR $resultado_registros[0][5]=="Error en la pagina" OR $resultado_registros[0][5]=="Escalado-Validar-Segunda Revisión" OR $resultado_registros[0][5]=="Escalado-Cliente-Segunda Revisión" OR $resultado_registros[0][5]=="Aplazado Tercera Revisión" OR $resultado_registros[0][5]=="Validado-Agente-Tercera Revisión") {
                    $respuesta_accion = "<div class='alert alert-success py-1 col-md-12'>¡El número de identificación ya tiene documentación cargada en el sistema, se encuentra en proceso de validación. Por favor permanecer atento a los canales de atención de Prosperidad Social</div>";
                } else {
                    header("Location:cargar?cod=".base64_encode($resultado_registros[0][1]));
                }
            } else {
                $respuesta_accion = "<div class='alert alert-warning py-1 col-md-12'>¡Número de identificación no registra en base de familias pre-inscritas revisadas o con pendientes de documentación!. Por favor verifique e intente nuevamente!</div>";
            }
        } else {
            $respuesta_accion = "<div class='alert alert-warning py-1 font-size-11 col-md-12'>¡Por favor valide el Captcha, verifique e intente nuevamente!</div>";
        }
    }

    /*Enlace para botón finalizar y cancelar*/
    $ruta_cancelar_finalizar="https://www.prosperidadsocial.gov.co/";
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper">
        <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
        <div class="row justify-content-center">
            <div class="col-md-11 pt-0 px-0 text-center">
                <img src="<?php echo IMAGES; ?>gestion_ocr/logo-dps-familias.png" class="img-fluid">
            </div>
            <div class="col-md-4 pt-2 background-blanco">
                <div class="row">
                    <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                    <div class="col-md-12 py-2 text-center fw-bold">
                        Por favor ingrese el documento de identidad de la cabeza de familia para continuar.
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-12 pt-3 pb-1">
                            <div class="form-group">
                              <label for="documento" class="my-0">Documento identidad cabeza de familia</label>
                              <input type="text" class="form-control form-control-sm" name="documento" id="documento" maxlength="50" value="" required>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2 mx-2">
                            <center><div class="g-recaptcha" data-sitekey="6Lc5fUQiAAAAAMzfNWy9JYn50jUnUQjwAdNNArCO" data-callback="correctCaptcha"></div></center>
                            <?php if(isset($_POST["guardar_registro"]) AND $_POST["g-recaptcha-response"]==''): ?>
                                <div id="response" class="col-md-12"><p class='alert alert-danger p-1'>Por favor valide el Captcha!</p></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Validar documento identidad</button>
                                <button class="btn btn-danger float-end" type="button" onclick="guardar_cancelar();">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>