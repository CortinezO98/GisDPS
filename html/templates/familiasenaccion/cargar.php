<?php
    //Validación de permisos del usuario para el módulo
    header("Location:https://www.prosperidadsocial.gov.co/");
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $cod_familia=validar_input(base64_decode($_GET['cod']));

    if ($cod_familia!="" AND isset($_GET['cod'])) {
        $consulta_string_valida="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_gestion_estado` FROM `gestion_ocr_resultado` WHERE `ocrr_cabezafamilia`='SI' AND (`ocrr_gestion_estado`='Contactado-Pendiente Documentos' OR `ocrr_gestion_estado`='Intento Contacto-Agotado' OR `ocrr_gestion_estado`='Intento Contacto-Fallido' OR `ocrr_gestion_estado`='Contactado-Pendiente Documentos-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Agotado-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Fallido-Segunda Revisión') AND `ocrr_cod_familia`=?";
        $consulta_registros_valida = $enlace_db->prepare($consulta_string_valida);
        $consulta_registros_valida->bind_param("s", $cod_familia);
        $consulta_registros_valida->execute();
        $resultado_registros_valida = $consulta_registros_valida->get_result()->fetch_all(MYSQLI_NUM);
    } 


    if(isset($_POST["guardar_registro"]) AND count($resultado_registros_valida)==1 AND $cod_familia!="" AND isset($_GET['cod'])){
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
            $consulta_string="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_gestion_estado` FROM `gestion_ocr_resultado` WHERE `ocrr_cabezafamilia`='SI' AND (`ocrr_gestion_estado`='Contactado-Pendiente Documentos' OR `ocrr_gestion_estado`='Intento Contacto-Agotado' OR `ocrr_gestion_estado`='Intento Contacto-Fallido' OR `ocrr_gestion_estado`='Documentos Cargados' OR `ocrr_gestion_estado`='Contactado-Pendiente Documentos-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Agotado-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Fallido-Segunda Revisión' OR `ocrr_gestion_estado`='Documentos Cargados-Segunda Revisión') AND `ocrr_cod_familia`=?";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            $consulta_registros->bind_param("s", $cod_familia);
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
            
            if ($resultado_registros[0][2]=='Contactado-Pendiente Documentos' OR $resultado_registros[0][2]=='Intento Contacto-Agotado' OR $resultado_registros[0][2]=='Intento Contacto-Fallido') {
                $estado_update='Documentos Cargados';
                $pre_doc='';
            } else {
                $estado_update='Documentos Cargados-Segunda Revisión';
                $pre_doc='TR-';
            }

            if ($_FILES['documento']['name']!="") {
                if ($_FILES['documento']['size']<1000000) {
                    $control_documento_peso=1;
                    $archivo_extension = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
                    if (strtolower($archivo_extension)=='pdf') {
                        $NombreArchivo=$pre_doc.$cod_familia.".".$archivo_extension;
                        $ruta_actual="../gestion_ocr/storage_adjuntos/";
                        $ruta_actual_guardar="/storage_adjuntos/";
                        $ruta_final=$ruta_actual.$NombreArchivo;
                        $ruta_final_guardar=$ruta_actual_guardar.$NombreArchivo;
                        if ($_FILES['documento']["error"] > 0) {
                            $control_documento=0;
                        } else {
                          /*ahora co la funcion move_uploaded_file lo guardaremos en el destino que queramos*/
                            if (move_uploaded_file($_FILES['documento']['tmp_name'], $ruta_final)) {
                                $control_documento=1;
                            } else {
                                $control_documento=0;
                            }
                        }
                    } else {
                        $control_documento=0;
                    }
                } else {
                    $control_documento=0;
                    $control_documento_peso=0;
                }
            } else {
                $control_documento=0;
            }

            if (file_exists($ruta_final) AND $control_documento==1) {
                if (count($resultado_registros)==1) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado_documentos`(`gord_codfamilia`, `gord_nombre`, `gord_ruta`, `gord_extension`) VALUES (?,?,?,?)");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssss', $cod_familia, $NombreArchivo, $ruta_final_guardar, $archivo_extension);

                    if ($sentencia_insert->execute()) {

                        $ocrr_gestion_fecha=date('Y-m-d H:i:s');

                        // Prepara la sentencia
                        $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_ocr_resultado` SET `ocrr_gestion_estado`=?, `ocrr_gestion_fecha`=? WHERE `ocrr_cod_familia`=?");

                        // Agrega variables a sentencia preparada
                        $consulta_actualizar->bind_param('sss', $estado_update, $ocrr_gestion_fecha, $cod_familia);
                        
                        // Ejecuta sentencia preparada
                        $consulta_actualizar->execute();
                        if (comprobarSentencia($enlace_db->info)) {
                            $correo='';
                            $observaciones='Documento cargado por cabeza de familia';
                            $tipificacion='';
                            $id_llamada='';
                            // Prepara la sentencia
                            $sentencia_insert_avance = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado_avances`(`gora_codfamilia`, `gora_estado`, `gora_correo`, `gora_observaciones`, `gora_llamada_tipificacion`, `gora_llamada_id`, `gora_registro_usuario`) VALUES (?,?,?,?,?,?,'1')");

                            // Agrega variables a sentencia preparada
                            $sentencia_insert_avance->bind_param('ssssss', $cod_familia, $estado_update, $correo, $observaciones, $tipificacion, $id_llamada);
                            $sentencia_insert_avance->execute();

                            //Valida SMS
                            $consulta_string_sms="SELECT `ocr_id`, `ocr_codfamilia`, `ocr_cabezadefamilia`, `ocr_telefono`, `ocr_celular`, `ocr_primernombre`, `ocr_segundonombre`, `ocr_primerapellido`, `ocr_segundoapellido` FROM `gestion_ocr` WHERE `ocr_cabezadefamilia`='SI' AND `ocr_codfamilia`=?";
                            $consulta_registros_sms = $enlace_db->prepare($consulta_string_sms);
                            $consulta_registros_sms->bind_param("s", $cod_familia);
                            $consulta_registros_sms->execute();
                            $resultado_registros_sms = $consulta_registros_sms->get_result()->fetch_all(MYSQLI_NUM);

                            $tiene_celular_valida=0;
                            $celular_valida=array();
                            $nombre_cabeza_familia='';
                            for ($i=0; $i < count($resultado_registros_sms); $i++) { 
                                if($resultado_registros_sms[$i][2]=='SI' AND (($resultado_registros_sms[$i][3]!="" AND strlen($resultado_registros_sms[$i][3])==10) OR ($resultado_registros_sms[$i][4]!="" AND strlen($resultado_registros_sms[$i][4])==10))){
                                  $tiene_celular_valida=1;
                                  
                                  if ($resultado_registros_sms[$i][3]!="" AND strlen($resultado_registros_sms[$i][3])==10) {
                                    $celular_valida[]=$resultado_registros_sms[$i][3];
                                  }

                                  if ($resultado_registros_sms[$i][4]!="" AND strlen($resultado_registros_sms[$i][4])==10) {
                                    $celular_valida[]=$resultado_registros_sms[$i][4];
                                  }

                                  $identificador_valida=$resultado_registros_sms[$i][1];
                                  $nombre_cabeza_familia=$resultado_registros_sms[$i][5];
                                  if ($resultado_registros_sms[$i][6]!="") {
                                    $nombre_cabeza_familia.=' '.$resultado_registros_sms[$i][6];
                                  }
                                  if ($resultado_registros_sms[$i][7]!="") {
                                    $nombre_cabeza_familia.=' '.$resultado_registros_sms[$i][7];
                                  }
                                  if ($resultado_registros_sms[$i][8]!="") {
                                    $nombre_cabeza_familia.=' '.$resultado_registros_sms[$i][8];
                                  }
                                }
                            }

                            if ($tiene_celular_valida) {
                                $celular_valida=array_values(array_unique($celular_valida));
                                $nsms_identificador=$identificador_valida;
                                $contenido_sms=$nombre_cabeza_familia.", confirmamos el cargue exitoso del documento de subsanación asociado a su código de familia.";
                                $nsms_url='';
                                for ($k=0; $k < count($celular_valida); $k++) {
                                  $nsms_destino=$celular_valida[$k];
                                  $estado_notificacion_sms=notificacion_familias_carguedocs_sms($enlace_db, $nsms_identificador, $nsms_destino, $contenido_sms, $nsms_url);
                                  if ($estado_notificacion_sms) {
                                    $estado_sms=1;
                                  }
                                }
                            }

                            header("Location:cargar?cod=".base64_encode($cod_familia));
                        } else {
                            $respuesta_accion = "<script type='text/javascript'>alertify.warning('¡Problemas al cargar el documento, por favor verifique e intente nuevamente!', 0);</script>";
                        }
                    } else {
                        $respuesta_accion = "<div class='alert alert-warning py-1 font-size-11 col-md-12'>¡Problemas al cargar el documento, por favor verifique e intente nuevamente!</div>";
                        unlink($ruta_final);
                    }
                } else {
                    $respuesta_accion = "<div class='alert alert-warning py-1 font-size-11 col-md-12'>¡Problemas al cargar el documento, por favor verifique e intente nuevamente!</div>";
                    unlink($ruta_final);
                }

            } elseif(!$control_documento_peso) {
                $respuesta_accion = "<div class='alert alert-danger py-1 font-size-11 col-md-12'>¡Problemas al cargar el documento, el tamaño excede el permitido (1Mb)!</div>";
            } else {
                $respuesta_accion = "<div class='alert alert-danger py-1 font-size-11 col-md-12'>¡Problemas al cargar el documento, por favor verifique e intente nuevamente!</div>";
            }
        } else {
            $respuesta_accion = "<div class='alert alert-warning py-1 font-size-11 col-md-12'>¡Por favor valide el Captcha, verifique e intente nuevamente!</div>";
        }
    }

    if ($cod_familia!="" AND isset($_GET['cod'])) {
        $consulta_string="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_gestion_estado` FROM `gestion_ocr_resultado` WHERE `ocrr_cabezafamilia`='SI' AND (`ocrr_gestion_estado`='Contactado-Pendiente Documentos' OR `ocrr_gestion_estado`='Intento Contacto-Agotado' OR `ocrr_gestion_estado`='Intento Contacto-Fallido' OR `ocrr_gestion_estado`='Documentos Cargados' OR `ocrr_gestion_estado`='Contactado-Pendiente Documentos-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Agotado-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Fallido-Segunda Revisión' OR `ocrr_gestion_estado`='Documentos Cargados-Segunda Revisión') AND `ocrr_cod_familia`=?";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $cod_familia);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-md-4 pt-2">
                <div class="row">
                    <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                    <?php if(count($resultado_registros)>0): ?>
                        <?php if($resultado_registros[0][2]=='Contactado-Pendiente Documentos' OR $resultado_registros[0][2]=='Intento Contacto-Agotado' OR $resultado_registros[0][2]=='Intento Contacto-Fallido' OR $resultado_registros[0][2]=='Contactado-Pendiente Documentos-Segunda Revisión' OR $resultado_registros[0][2]=='Intento Contacto-Agotado-Segunda Revisión' OR $resultado_registros[0][2]=='Intento Contacto-Fallido-Segunda Revisión'): ?>
                            <div class="col-md-12 py-2 text-center fw-bold">
                                Por favor cargue los documentos solicitados como corrección, recuerde que se acepta un solo archivo en formato PDF
                            </div>
                            <div class="col-md-12 pt-3 pb-1">
                                <div class="form-group">
                                    <label for="documento" class="my-0">Adjuntar documento</label>
                                    <input class="form-control form-control-sm custom-file-input" name="documento" id="inputGroupFile01" type="file" accept=".pdf, .PDF" required>
                                <p class="alert alert-warning font-size-11 p-1">*Formato permitido: pdf<br>*Tamaño máximo: 1Mb</p>
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
                                    <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Enviar documento</button>
                                    <button class="btn btn-danger float-end" type="button" onclick="guardar_cancelar();">Cancelar</button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="col-md-12 py-2 text-center fw-bold">
                                <p class="alert alert-success font-size-11">¡Gracias por la gestión realizada, en nuestro sistema no tiene novedades o documentos pendientes por corregir para el proyecto Familias en Acción!</p>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="<?php echo $ruta_cancelar_finalizar; ?>" class="btn btn-dark float-end">Finalizar</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="col-md-12 py-2 text-center fw-bold">
                            No encontramos el número de identificación, por favor verifique e intente nuevamente
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <a href="<?php echo $ruta_cancelar_finalizar; ?>" class="btn btn-dark float-end">Finalizar</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </form>
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
    <script type="text/javascript">
        $("#documento").change(function(){
            var valor_opcion = document.getElementById("documento").files[0].name;

            if (valor_opcion!="") {
                document.getElementById('documento').innerHTML=valor_opcion.substring(0, 25)+"...";
                $("#documento").addClass("color-verde");
            }
        });
    </script>
</body>
</html>