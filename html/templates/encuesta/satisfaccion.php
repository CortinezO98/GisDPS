<?php
    //Validación de permisos del usuario para el módulo
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $id_encuesta=validar_input(base64_decode($_GET['int']));
    if(isset($_POST["guardar_registro"])){
        $pregunta_1=validar_input($_POST['pregunta_1']);
        $pregunta_2=validar_input($_POST['pregunta_2']);
        $pregunta_3=validar_input($_POST['pregunta_3']);
        $pregunta_4=validar_input($_POST['pregunta_4']);
        $pregunta_5=validar_input($_POST['pregunta_5']);

        // Prepara la sentencia
        $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_interacciones_encuestas` SET `gie_pregunta_1`=?,`gie_pregunta_2`=?,`gie_pregunta_3`=?,`gie_pregunta_4`=?,`gie_pregunta_5`=?,`gie_respuesta_fecha`=? WHERE `gie_id`=?");

        // Agrega variables a sentencia preparada
        $consulta_actualizar->bind_param('sssssss', $pregunta_1, $pregunta_2, $pregunta_3, $pregunta_4, $pregunta_5, date('Y-m-d H:i:s'), $id_encuesta);
        
        // Ejecuta sentencia preparada
        $consulta_actualizar->execute();
        
        if (comprobarSentencia($enlace_db->info)) {
            header("Location:satisfaccion.php?int=".base64_encode($id_encuesta));
        } else {
            $respuesta_accion = "<div class='alert alert-warning py-1 font-size-11 col-md-12'>¡Problemas al enviar la encuestas, por favor intente más tarde!</div>";
        }
    }

    $consulta_string_encuesta="SELECT `gie_id`, `gie_pregunta_1`, `gie_pregunta_2`, `gie_pregunta_3`, `gie_pregunta_4`, `gie_pregunta_5`, `gie_respuesta_fecha`, `gie_registro_usuario`, `gie_registro_fecha` FROM `gestion_interacciones_encuestas` WHERE `gie_id`=?";
    $consulta_registros_encuesta = $enlace_db->prepare($consulta_string_encuesta);
    $consulta_registros_encuesta->bind_param('s', $id_encuesta);
    $consulta_registros_encuesta->execute();
    $resultado_registros_encuesta = $consulta_registros_encuesta->get_result()->fetch_all(MYSQLI_NUM);

    /*Enlace para botón finalizar y cancelar*/
    $ruta_cancelar_finalizar="https://www.prosperidadsocial.gov.co/";
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
    <?php require_once(ROOT.'includes/_head.php'); ?>
    <style type="text/css">
        .clasificacion {
          direction: rtl;
          unicode-bidi: bidi-override;
        }

        label:hover {
            color: orange !important;
        }
        label:hover ~ label {
          color: orange;
        }

        input[type="radio"]:checked ~ label {
          color: orange;
        }
    </style>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper">
      <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
        <div class="row justify-content-center">
            <div class="col-md-6 pt-2 background-blanco mb-3">
                <div class="row">
                    <div class="col-md-12">
                        <img src="<?php echo IMAGES; ?>interacciones_encuesta/LOGO-PROSPERIDAD-SOCIAL.png" class="img-fluid">
                    </div>
                    <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                    <?php if(count($resultado_registros_encuesta)>0): ?>
                        <?php if($resultado_registros_encuesta[0][6]==""): ?>
                            <div class="col-md-12 py-2 text-center fw-bold">
                                Para nosotros es muy importante conocer su opinión sobre la calidad de nuestro servicio, por eso lo invitamos a responder 5 preguntas:
                            </div>
                            <div class="col-md-12 pt-3 pb-1">
                                1. ¿Considera que su inquietud fue resuelta?
                            </div>
                            <div class="col-md-12">
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="pregunta_1" value="Si" id="pregunta_1_1" autocomplete="off" required>
                                    <label class="btn btn-outline-dark py-2" for="pregunta_1_1">Si</label>
                                    <input type="radio" class="btn-check" name="pregunta_1" value="No" id="pregunta_1_2" autocomplete="off" required>
                                    <label class="btn btn-outline-dark py-2" for="pregunta_1_2">No</label>
                                </div>
                            </div>
                            <div class="col-md-12 py-3 fw-bold">
                                En una escala de 1 a 5 siendo 5 la mayor valoración y 1 la menor valoración califique:
                            </div>
                            <div class="col-md-12 pt-3 pb-1">
                                2. Califique el nivel de satisfacción.
                            </div>
                            <div class="col-md-12">
                               <div class="btn-group clasificacion" role="group">
                                    <input type="radio" class="btn-check" name="pregunta_2" value="5" id="pregunta_2_5" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_2_5">★</label>
                                    <input type="radio" class="btn-check" name="pregunta_2" value="4" id="pregunta_2_4" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_2_4">★</label>
                                    <input type="radio" class="btn-check" name="pregunta_2" value="3" id="pregunta_2_3" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_2_3">★</label>
                                    <input type="radio" class="btn-check" name="pregunta_2" value="2" id="pregunta_2_2" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_2_2">★</label>
                                    <input type="radio" class="btn-check" name="pregunta_2" value="1" id="pregunta_2_1" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_2_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-12 pt-3 pb-1">
                                3. Califique el tiempo de su consulta a través de este canal.
                            </div>
                            <div class="col-md-12">
                                <div class="btn-group clasificacion" role="group">
                                    <input type="radio" class="btn-check" name="pregunta_3" value="5" id="pregunta_3_5" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_3_5">★</label>
                                    <input type="radio" class="btn-check" name="pregunta_3" value="4" id="pregunta_3_4" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_3_4">★</label>
                                    <input type="radio" class="btn-check" name="pregunta_3" value="3" id="pregunta_3_3" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_3_3">★</label>
                                    <input type="radio" class="btn-check" name="pregunta_3" value="2" id="pregunta_3_2" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_3_2">★</label>
                                    <input type="radio" class="btn-check" name="pregunta_3" value="1" id="pregunta_3_1" autocomplete="off" required>
                                    <label class="btn py-2 px-1" style="font-size: 30px;" for="pregunta_3_1">★</label>
                                </div>
                            </div>
                            <div class="col-md-12 pt-3 pb-1 font-size-13">
                                4. ¿Fue completa y clara la información: Opciones de respuesta?.
                            </div>
                            <div class="col-md-12">
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="pregunta_4" value="Si" id="pregunta_4_1" autocomplete="off" required>
                                    <label class="btn btn-outline-dark py-2" for="pregunta_4_1">Si</label>
                                    <input type="radio" class="btn-check" name="pregunta_4" value="No" id="pregunta_4_2" autocomplete="off" required>
                                    <label class="btn btn-outline-dark py-2" for="pregunta_4_2">No</label>
                                </div>
                            </div>
                            <div class="col-md-12 pt-3 pb-1 font-size-13">
                                5. En este espacio puede dejarnos comentarios, recomendaciones, observaciones o sugerencias.
                            </div>
                            <div class="col-md-12 pt-0 pb-1">
                                <div class="form-group">
                                  <textarea class="form-control form-control-sm height-100" name="pregunta_5" id="pregunta_5"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Enviar encuesta</button>
                                    <a href="<?php echo $ruta_cancelar_finalizar; ?>" class="btn btn-danger float-end">Cancelar</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="col-md-12 py-2 text-center fw-bold">
                                <p class="alert alert-success font-size-11">¡Gracias por sus comentarios!<br>No encontramos encuestas pendientes por responder</p>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="<?php echo $ruta_cancelar_finalizar; ?>" class="btn btn-dark float-end">Finalizar</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="col-md-12 py-2 text-center fw-bold">
                            No encontramos encuestas pendientes por responder
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
</body>
</html>