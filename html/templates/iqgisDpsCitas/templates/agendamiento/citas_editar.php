<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas";
  $subtitle = "Citas | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $fecha=validar_input(base64_decode($_GET['fecha']));
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="citas?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&fecha=".base64_encode($fecha);

  if(isset($_POST["guardar_registro"])){
    $estado=validar_input($_POST['estado']);
    $radicado=validar_input($_POST['radicado']);
    $observaciones=validar_input($_POST['observaciones']);
    $gcar_genero='';
    $gcar_nivel_escolaridad='';
    $gcar_envio_encuesta=validar_input($_POST['gcar_envio_encuesta']);
    $numero_contacto=validar_input($_POST['numero_contacto']);
    $gcar_celular='';

    // if (isset($_POST['informacion_poblacional'])) {
    //   $informacion_poblacional=$_POST['informacion_poblacional'];
    // } else {
    //   $informacion_poblacional=array();
    // }

    // $informacion_poblacional_insert=implode(';', $informacion_poblacional);

    // if (isset($_POST['atencion_preferencial'])) {
    //   $atencion_preferencial=$_POST['atencion_preferencial'];
    // } else {
    //   $atencion_preferencial=array();
    // }

    // $atencion_preferencial_insert=implode(';', $atencion_preferencial);
    if($_SESSION[APP_SESSION.'_registro_creado_atencion_cita']!=1){
      // Prepara la sentencia
      $consulta_actualizar_reserva = $enlace_db->prepare("UPDATE `gestion_citas_agenda_reservas` SET `gcar_observaciones`=?,`gcar_atencion_usuario`=?,`gcar_atencion_fecha`=?,`gcar_radicado`=?, `gcar_genero`=?, `gcar_nivel_escolaridad`=?, `gcar_envio_encuesta`=?, `gcar_celular`=?, `gcar_estado`=? WHERE `gcar_consecutivo`=?");

      // Agrega variables a sentencia preparada
      $consulta_actualizar_reserva->bind_param('ssssssssss', $observaciones, $_SESSION[APP_SESSION.'_session_usu_id'], date('Y-m-d H:i:s'), $radicado, $gcar_genero, $gcar_nivel_escolaridad, $gcar_envio_encuesta, $gcar_celular, $estado, $id_registro);
      
      // Ejecuta sentencia preparada
      $consulta_actualizar_reserva->execute();
      
      if (comprobarSentencia($enlace_db->info)) {
          if ($numero_contacto!="") {
            // Prepara la sentencia
            $consulta_actualizar_contacto = $enlace_db->prepare("UPDATE `gestion_citas_agenda_reservas` SET `gcar_datos_celular`=? WHERE `gcar_consecutivo`=?");

            // Agrega variables a sentencia preparada
            $consulta_actualizar_contacto->bind_param('ss', $numero_contacto, $id_registro);
            
            // Ejecuta sentencia preparada
            $consulta_actualizar_contacto->execute();
          }
          
          $consulta_string="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_observaciones`, `gcar_atencion_usuario`, `gcar_atencion_fecha`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora`, TC.`gca_estado`, TC.`gca_estado_agenda`, `gcar_radicado` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_consecutivo`=?";

          $consulta_registros = $enlace_db->prepare($consulta_string);
          $consulta_registros->bind_param("s", $id_registro);
          $consulta_registros->execute();
          $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

          // Prepara la sentencia
          $consulta_actualizar_agenda = $enlace_db->prepare("UPDATE `gestion_citas_agenda` SET `gca_estado_agenda`=? WHERE `gca_id`=?");

          // Agrega variables a sentencia preparada
          $consulta_actualizar_agenda->bind_param('ss', $estado, $resultado_registros[0][1]);
          
          // Ejecuta sentencia preparada
          $consulta_actualizar_agenda->execute();

          if (comprobarSentencia($enlace_db->info)) {
            if ($estado=='Asiste' AND $gcar_envio_encuesta=='Si') {
              $nsms_identificador=$resultado_registros[0][0];
              $contenido_sms="Lo invitamos a calificar la atención recibida en el canal de atención presencial de Prosperidad Social, en el siguiente link: SHORTURL";
              $nsms_url='https://n9.cl/0elky';
              $nsms_destino=$resultado_registros[0][8];
              $estado_notificacion_sms=notificacion_sms($enlace_db, $nsms_identificador, $nsms_destino, $contenido_sms, $nsms_url, '13');
              if ($estado_notificacion_sms) {
                  $estado_sms=1;
              }
            }

            $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
            $_SESSION[APP_SESSION.'_registro_creado_atencion_cita']=1;
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
          }
      } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
      }
    } else {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
    }
  }

  $consulta_string="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_observaciones`, `gcar_atencion_usuario`, `gcar_atencion_fecha`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora`, TC.`gca_estado`, TC.`gca_estado_agenda`, `gcar_radicado`, `gcar_atencion_preferencial`, `gcar_informacion_poblacional`, `gcar_envio_encuesta` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_consecutivo`=?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
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
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
          <div class="row justify-content-center">
            <?php if (!empty($respuesta_accion)) {echo "<script type='text/javascript'>".$respuesta_accion."</script>";} ?>
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <p><b>Punto de atención:</b> <?php echo $resultado_registros[0][15]; ?></p>
                        <p><b>Dirección:</b> <?php echo $resultado_registros[0][16]; ?></p>
                        <p><b>Fecha:</b> <?php echo $resultado_registros[0][18]; ?></p>
                        <p><b>Hora:</b> <?php echo $resultado_registros[0][19]; ?></p>
                        <p><b>Tipo de documento:</b> <?php echo $resultado_registros[0][4]; ?></p>
                        <p><b>Número de identificación:</b> <?php echo $resultado_registros[0][5]; ?></p>
                        <p><b>Nombres y apellidos:</b> <?php echo $resultado_registros[0][6]; ?></p>
                        <p><b>Correo electrónico:</b> <?php echo $resultado_registros[0][7]; ?></p>
                        <p><b>Número celular:</b> <?php echo $resultado_registros[0][8]; ?></p>
                        <p><b>Número fijo:</b> <?php echo $resultado_registros[0][9]; ?></p>
                        <p><b>Preferencial:</b> <?php echo $resultado_registros[0][23]; ?></p>
                        <p><b>Poblacional:</b> <?php echo $resultado_registros[0][24]; ?></p>
                        <p><b>Asesor:</b> <?php echo $resultado_registros[0][17]; ?></p>
                        <p><b>Estado agendamiento:</b> <?php echo $resultado_registros[0][21]; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                            <div class="form-group my-1">
                                <label for="estado">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" onchange="validar_estado();" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_cita']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="Asiste" <?php if($resultado_registros[0][21]=="Asiste"){ echo "selected"; } ?>>Asiste</option>
                                  <option value="No asiste" <?php if($resultado_registros[0][21]=="No asiste"){ echo "selected"; } ?>>No asiste</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 d-none" id="radicado_div">
                            <div class="form-group my-1">
                              <label for="radicado" class="my-0">Radicado</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="radicado" id="radicado" maxlength="100" value="<?php echo $resultado_registros[0][22]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_cita']==1) { echo 'disabled'; } ?> required disabled autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-12 d-none" id="encuesta_div">
                            <div class="form-group my-1">
                                <label for="gcar_envio_encuesta">¿Envío SMS para realizar encuesta de satisfacción?</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="gcar_envio_encuesta" id="gcar_envio_encuesta" onchange="validar_sms();" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_cita']==1) { echo 'disabled'; } ?> required disabled>
                                  <option value="">Seleccione</option>
                                  <option value="Si" <?php if($resultado_registros[0][25]=="Si"){ echo "selected"; } ?>>Si</option>
                                  <option value="No" <?php if($resultado_registros[0][25]=="No"){ echo "selected"; } ?>>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 d-none" id="numero_contacto_div">
                            <div class="form-group my-1">
                              <label for="numero_contacto" class="my-0">Número contacto</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="numero_contacto" id="numero_contacto" minlength="10" maxlength="10" value="<?php echo $resultado_registros[0][8]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_cita']==1) { echo 'disabled'; } ?> disabled autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_cita']==1) { echo 'disabled'; } ?> required><?php echo $resultado_registros[0][11]; ?></textarea>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_cita']==1): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php else: ?>
                                    <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Guardar</button>
                                    <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');">Cancelar</button>
                                <?php endif; ?>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
    function validar_estado(){
        var estado_opcion = document.getElementById("estado");
        var estado = estado_opcion.options[estado_opcion.selectedIndex].value;

        if(estado=="Asiste") {
            var radicado = document.getElementById('radicado').disabled=false;
            var encuesta = document.getElementById('gcar_envio_encuesta').disabled=false;
            $("#radicado_div").removeClass('d-none').addClass('d-block');
            $("#encuesta_div").removeClass('d-none').addClass('d-block');
        } else {
            var radicado = document.getElementById('radicado').disabled=true;
            var encuesta = document.getElementById('gcar_envio_encuesta').disabled=true;
            $("#radicado_div").removeClass('d-block').addClass('d-none');
            $("#encuesta_div").removeClass('d-block').addClass('d-none');
        }
    }

    function validar_sms(){
        var gcar_envio_encuesta_opcion = document.getElementById("gcar_envio_encuesta");
        var gcar_envio_encuesta = gcar_envio_encuesta_opcion.options[gcar_envio_encuesta_opcion.selectedIndex].value;

        if(gcar_envio_encuesta=="Si") {
            var numero_contacto = document.getElementById('numero_contacto').disabled=false;
            $("#numero_contacto_div").removeClass('d-none').addClass('d-block');
        } else {
            var numero_contacto = document.getElementById('numero_contacto').disabled=true;
            $("#numero_contacto_div").removeClass('d-block').addClass('d-none');
        }
    }
    <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_cita']!=1): ?>
      validar_estado();
      validar_sms();
    <?php endif; ?>
  </script>
</body>
</html>