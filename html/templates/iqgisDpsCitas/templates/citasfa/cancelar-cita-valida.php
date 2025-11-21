<?php
    //Validación de permisos del usuario para el módulo
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $tipo=validar_input(base64_decode($_GET['t']));
    $id_cita=validar_input(base64_decode($_GET['id']));
    
    $error_cancelar_cita=0;
    if ($tipo=='cancelar-confirma' AND $id_cita!="") {
        if(isset($_POST["cancelar_cita"])){
            $gcar_estado='Cancelada';
            // Prepara la sentencia
            $consulta_actualizar_reserva = $enlace_db->prepare("UPDATE `gestion_citasfa_agenda_reservas` SET `gcar_cancela_fecha`=?, `gcar_estado`=? WHERE `gcar_consecutivo`=?");

            // Agrega variables a sentencia preparada
            $consulta_actualizar_reserva->bind_param('sss', date('Y-m-d H:i:s'), $gcar_estado, $id_cita);
            
            // Ejecuta sentencia preparada
            $consulta_actualizar_reserva->execute();
            
            if (comprobarSentencia($enlace_db->info)) {
                $consulta_string="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_observaciones`, `gcar_atencion_usuario`, `gcar_atencion_fecha`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora`, TC.`gca_estado`, TC.`gca_estado_agenda`, `gcar_radicado` FROM `gestion_citasfa_agenda_reservas` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citasfa_agenda` AS TC ON `gestion_citasfa_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_consecutivo`=?";

                $consulta_registros = $enlace_db->prepare($consulta_string);
                $consulta_registros->bind_param("s", $id_cita);
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                $estado='Disponible';
                // Prepara la sentencia
                $consulta_actualizar_agenda = $enlace_db->prepare("UPDATE `gestion_citasfa_agenda` SET `gca_estado_agenda`=? WHERE `gca_id`=?");

                // Agrega variables a sentencia preparada
                $consulta_actualizar_agenda->bind_param('ss', $estado, $resultado_registros[0][1]);
                
                // Ejecuta sentencia preparada
                $consulta_actualizar_agenda->execute();

                if (comprobarSentencia($enlace_db->info)) {
                    //PROGRAMACIÓN NOTIFICACIÓN
                    $asunto='Cancelación Cita';
                    $referencia='Datos Cita';
                    $contenido="<p>Buen día, <b>".$resultado_registros[0][6]."</b><br></p>
                            <p>De manera atenta, le confirmamos que su solicitud de <b>Cancelación</b> de cita para realizar inscripción al programa Familias en Acción de <b>Prosperidad Social</b> ha sido registrada.</p>
                            
                          <p>Le recordamos que para agendar una nueva cita deberá ingresar en la siguiente página web: <a href='https://dps.iq-online.net.co/citasfa/inicio'>https://dps.iq-online.net.co/citasfa/inicio</a></p>
                          <p>Respetado destinatario, este correo ha sido generado por un sistema de envío automático; por favor <b>NO</b> responda al mismo ya que no podrá ser gestionado.</p>
                            <p>Lo invitamos a consultar nuestros demás canales de atención ingresando al siguiente enlace: <a href='https://prosperidadsocial.gov.co/atencion-al-ciudadano/servicio-al-ciudadano/'>https://prosperidadsocial.gov.co/atencion-al-ciudadano/servicio-al-ciudadano/</a></p>
                          <p>
                              <br><br>
                              Cordialmente,
                              <br>
                              <br><img src='cid:firma-dps' style='height: 50px;'></img>
                              <br>Programa Familias en Acción
                          </p>
                          <p><center><b>Todas las personas tienen derecho a presentar peticiones respetuosas ante las autoridades de forma GRATUITA.</b></center></p>
                            <p><center><b>No recurra a intermediarios. No pague por sus derechos. DENUNCIE.</b></center></p>
                            <p style='font-size: 10px;'>Este mensaje y sus archivos adjuntos van dirigidos exclusivamente a su destinatario, pudiendo contener información confidencial. No está permitida su reproducción o distribución sin la autorización expresa de Prosperidad Social. Si usted no es el destinatario final por favor elimínelo e infórmenos por esta vía, en cumplimiento de la Ley Estatutaria 1581 de 2012 de Protección de datos personales y el Decreto Reglamentario 1377 del 27 de junio de 2013 y demás normas concordantes. Para conocer más sobre nuestra Política de tratamiento de datos personales, lo invitamos a ingresar al siguiente link: <a href='https://prosperidadsocial.gov.co/politica-de-proteccion-de-datos-personales/'>https://prosperidadsocial.gov.co/politica-de-proteccion-de-datos-personales/</a></p>";
                    
                    $nc_address=$resultado_registros[0][7].";";
                    $nc_cc="";
                    $modulo_plataforma='Gestión Citas FA';
                    $estado_notificacion=notificacion_agendamiento($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
                    if ($estado_notificacion) {
                      
                    } else {

                    }
                    header("Location:cancelar-cita-confirmacion");
                } else {
                  $error_cancelar_cita=1;
                }
            } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
            }
        }

        $fecha_actual=date('Y-m-d');
        $hora_actual=date('H:i');

        $consulta_string_reserva="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora` FROM `gestion_citasfa_agenda_reservas` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citasfa_agenda` AS TC ON `gestion_citasfa_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_consecutivo`=? AND TC.`gca_estado_agenda`='Reservada' AND TC.`gca_fecha`>=?";

        $consulta_registros_reserva = $enlace_db->prepare($consulta_string_reserva);
        $consulta_registros_reserva->bind_param("ss", $id_cita, $fecha_actual);
        $consulta_registros_reserva->execute();
        $resultado_registros_reserva = $consulta_registros_reserva->get_result()->fetch_all(MYSQLI_NUM);
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
                            <form name="cancelar_cita" action="" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12 pt-0 px-0 text-center mb-2">
                                        <img src="<?php echo IMAGES; ?>logo-cliente.png" class="img-fluid">
                                    </div>
                                    <div class="card-header mb-2">
                                        Cancela tu cita
                                    </div>
                                    <?php if ($tipo=='cancelar-confirma' AND $id_cita!=""): ?>
                                        <?php if (count($resultado_registros_reserva)>0): ?>
                                            <div class="card text-center">
                                              <div class="card-body">
                                                <p class="card-text">Estimado (a) <b><?php echo $resultado_registros_reserva[0][6]; ?></b>, su cita de radicado <b><?php echo $resultado_registros_reserva[0][0]; ?></b> en el punto <b><?php echo $resultado_registros_reserva[0][12]; ?></b> en la dirección <b><?php echo $resultado_registros_reserva[0][13]; ?></b> el <b><?php echo $array_dias_nombre[date('N', strtotime($resultado_registros_reserva[0][15]))].' '.date('d', strtotime($resultado_registros_reserva[0][15])).' de '.$array_meses[intval(date('m', strtotime($resultado_registros_reserva[0][15])))].' de '.date('Y', strtotime($resultado_registros_reserva[0][15])); ?></b> a las <b><?php echo date('h:i A', strtotime($resultado_registros_reserva[0][16])); ?></b> será cancelada.</p>

                                                <p class="card-text">Haga clic en <b>"Confirmar cancelación de cita"</b> para cancelar definitivamente la cita agendada. De lo contrario, haga clic en <b>"Salir"</b>.</p>
                                                <div class="form-group">
                                                    <button class="btn btn-danger" type="submit" name="cancelar_cita">Confirmar cancelación de cita</button>
                                                    <a href="inicio" class="btn btn-primary">Salir</a>
                                                </div>
                                              </div>
                                            </div>
                                        <?php elseif(count($resultado_registros_reserva)==0): ?>
                                            <div class="card text-center">
                                              <div class="card-body">
                                                <p class="card-text">No se ha encontrado una cita agendada. Por favor haga clic en "Aceptar" si desea realizar otra consulta, o haga clic en "Agendar cita" si desea programar una cita con uno de nuestros asesores.</p>
                                                <div class="form-group">
                                                    <a href="datospersonales?t=<?php echo base64_encode('agendar'); ?>" class="btn btn-primary">Agendar Cita</a>
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
                                    <?php if($error_cancelar_cita): ?>
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