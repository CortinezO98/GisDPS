<?php
    //Validación de permisos del usuario para el módulo
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $tipo=validar_input(base64_decode($_GET['t']));
    $datos=unserialize($_GET['d']);
    $confirma=validar_input(base64_decode($_GET['confim']));
    $id_cita=validar_input(base64_decode($_GET['id']));
    $id_consecutivo=validar_input(base64_decode($_GET['con']));
    
    $consecutivo=validar_input(base64_decode($_GET['cons']));
    $doc_identidad=validar_input(base64_decode($_GET['doc']));
    
    $cita_no_disponible=0;
    $cita_datos_incompletos=0;
    $cita_agendada=0;
    if ($consecutivo=='' AND $doc_identidad=='') {
        if($confirma=='token' AND $tipo=='reagendar-agendamiento' AND $id_consecutivo!=''){
            $consulta_string_reserva="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora`, `gcar_atencion_preferencial`, `gcar_informacion_poblacional` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_consecutivo`=? AND TC.`gca_estado_agenda`='Reservada' AND TC.`gca_fecha`>=?";

            $consulta_registros_reserva = $enlace_db->prepare($consulta_string_reserva);
            $consulta_registros_reserva->bind_param("ss", $id_consecutivo, date('Y-m-d'));
            $consulta_registros_reserva->execute();
            $resultado_registros_reserva = $consulta_registros_reserva->get_result()->fetch_all(MYSQLI_NUM);

            $consulta_string_agenda="SELECT `gca_id`, `gca_punto`, `gca_usuario`, `gca_semana`, `gca_fecha`, `gca_hora`, `gca_estado`, `gca_estado_agenda`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos` FROM `gestion_citas_agenda` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda`.`gca_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda`.`gca_usuario`=TU.`usu_id` WHERE `gca_id`=?";

            $consulta_registros_agenda = $enlace_db->prepare($consulta_string_agenda);
            $consulta_registros_agenda->bind_param("s", $id_cita);
            $consulta_registros_agenda->execute();
            $resultado_registros_agenda = $consulta_registros_agenda->get_result()->fetch_all(MYSQLI_NUM);

            if ($resultado_registros_agenda[0][7]=='Disponible') {
                $gca_estado_agenda='Reservada';
                // Prepara la sentencia
                $consulta_actualizar_agenda = $enlace_db->prepare("UPDATE `gestion_citas_agenda` SET `gca_estado_agenda`=? WHERE `gca_id`=? AND `gca_estado_agenda`='Disponible'");

                // Agrega variables a sentencia preparada
                $consulta_actualizar_agenda->bind_param('ss', $gca_estado_agenda, $id_cita);
                
                // Ejecuta sentencia preparada
                $consulta_actualizar_agenda->execute();
                if (comprobarSentencia($enlace_db->info)) {
                    $consulta_consecutivo = mysqli_query($enlace_db, "SELECT MAX(`gcar_consecutivo`) FROM `gestion_citas_agenda_reservas`");
                    $resultado_consecutivo = mysqli_fetch_all($consulta_consecutivo);
                    $ultimo_consecutivo=explode('CDPS', $resultado_consecutivo[0][0]);
                    $nuevo_consecutivo=$ultimo_consecutivo[1]+1;
                    $gcar_consecutivo="CDPS".str_pad($nuevo_consecutivo, 6, 0, STR_PAD_LEFT);

                    $gcar_cita=$id_cita;
                    $gcar_punto=$resultado_registros_agenda[0][1];
                    $gcar_usuario=$resultado_registros_agenda[0][2];
                    $gcar_datos_tipo_documento=validar_input($resultado_registros_reserva[0][4]);
                    $gcar_datos_numero_identificacion=validar_input($resultado_registros_reserva[0][5]);
                    $gcar_datos_nombres=validar_input($resultado_registros_reserva[0][6]);
                    $gcar_datos_correo=validar_input($resultado_registros_reserva[0][7]);
                    $gcar_datos_celular=validar_input($resultado_registros_reserva[0][8]);
                    $gcar_datos_fijo=validar_input($resultado_registros_reserva[0][9]);
                    $gcar_datos_autoriza=validar_input($resultado_registros_reserva[0][10]);
                    $gcar_observaciones='';
                    $gcar_atencion_usuario='';
                    $gcar_atencion_fecha='';
                    $gcar_radicado='';
                    $gcar_estado='Reservada';
                    $gcar_atencion_preferencial=validar_input($resultado_registros_reserva[0][17]);
                    $gcar_informacion_poblacional=validar_input($resultado_registros_reserva[0][18]);
                    $gcar_genero='';
                    $gcar_nivel_escolaridad='';
                    $gcar_envio_encuesta='';
                    $gcar_celular='';
                    $gcar_cancela_fecha='';
                    $gcar_cancela_motivo='';
                    $gcar_auxiliar='';

                    // Prepara la sentencia
                    $sentencia_insert_cita = $enlace_db->prepare("INSERT INTO `gestion_citas_agenda_reservas`(`gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_observaciones`, `gcar_atencion_usuario`, `gcar_atencion_fecha`, `gcar_radicado`, `gcar_atencion_preferencial`, `gcar_informacion_poblacional`, `gcar_genero`, `gcar_nivel_escolaridad`, `gcar_envio_encuesta`, `gcar_celular`, `gcar_cancela_fecha`, `gcar_estado`, `gcar_cancela_motivo`, `gcar_auxiliar`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                    // Agrega variables a sentencia preparada
                    $sentencia_insert_cita->bind_param('sssssssssssssssssssssssss', $gcar_consecutivo, $gcar_cita, $gcar_punto, $gcar_usuario, $gcar_datos_tipo_documento, $gcar_datos_numero_identificacion, $gcar_datos_nombres, $gcar_datos_correo, $gcar_datos_celular, $gcar_datos_fijo, $gcar_datos_autoriza, $gcar_observaciones, $gcar_atencion_usuario, $gcar_atencion_fecha, $gcar_radicado, $gcar_atencion_preferencial, $gcar_informacion_poblacional, $gcar_genero, $gcar_nivel_escolaridad, $gcar_envio_encuesta, $gcar_celular, $gcar_cancela_fecha, $gcar_estado, $gcar_cancela_motivo, $gcar_auxiliar);

                    if ($sentencia_insert_cita->execute()) {
                        $cita_agendada=1;

                        //PROGRAMACIÓN NOTIFICACIÓN
                        $asunto='Confirmación Cita';
                        $referencia='Datos Cita';
                        $contenido="<p>Buen día, <b>".$gcar_datos_nombres."</b><br></p>
                            <p>De manera atenta, le confirmamos que su solicitud de <b>Reagendamiento</b> de cita para atención presencial en <b>Prosperidad Social</b> ha sido registrada, le recordamos presentarse en la siguiente fecha y lugar:</p>
                            <ul>
                                <li style='padding-left: 10px;'>Punto de atención: ".$resultado_registros_agenda[0][8]."</li>
                                <li style='padding-left: 10px;'>Dirección: ".$resultado_registros_agenda[0][9]."</li>
                                <li style='padding-left: 10px;'>Fecha: ".date('d-m-Y', strtotime($resultado_registros_agenda[0][4]))."</li>
                                <li style='padding-left: 10px;'>Hora: ".date('h:i A', strtotime($resultado_registros_agenda[0][5]))."</li>
                            </ul>
                          <p>Recuerde:</p>
                            <ul>
                                <li style='padding-left: 10px;'>Asistir con documento de identificación personal en original</li>
                                <li style='padding-left: 10px;'>Llegar al punto de atención con 10 minutos de anticipación</li>
                                <li style='padding-left: 10px;'>Si va a solicitar el registro de alguna novedad en los programas, debe llevar los correspondientes documentos soporte</li>
                                <li style='padding-left: 10px;'>Si le es imposible asistir a la cita ya solicitada, la debe cancelar o reagendar por lo menos con 12 horas de anticipación</li>
                            </ul>
                          <p>Le recordamos que usted puede consultar, reagendar o cancelar su cita, ingresando en la siguiente página web: <a href='https://agendamientodps.grupoasd.com.co/citas/inicio'>https://agendamientodps.grupoasd.com.co/citas/inicio</a></p>
                          <p>Respetado destinatario, este correo ha sido generado por un sistema de envío automático; por favor <b>NO</b> responda al mismo ya que no podrá ser gestionado.</p>
                            <p>Lo invitamos a consultar nuestros demás canales de atención ingresando al siguiente enlace: <a href='https://prosperidadsocial.gov.co/atencion-al-ciudadano/servicio-al-ciudadano/'>https://prosperidadsocial.gov.co/atencion-al-ciudadano/servicio-al-ciudadano/</a></p>
                          <p>
                              <br><br>
                              Cordialmente,
                              <br>
                              <br><img src='cid:firma-dps' style='height: 50px;'></img>
                              <br>Grupo de Participación Ciudadana
                          </p>
                          <p><center><b>Todas las personas tienen derecho a presentar peticiones respetuosas ante las autoridades de forma GRATUITA.</b></center></p>
                            <p><center><b>No recurra a intermediarios. No pague por sus derechos. DENUNCIE.</b></center></p>
                            <p style='font-size: 10px;'>Este mensaje y sus archivos adjuntos van dirigidos exclusivamente a su destinatario, pudiendo contener información confidencial. No está permitida su reproducción o distribución sin la autorización expresa de Prosperidad Social. Si usted no es el destinatario final por favor elimínelo e infórmenos por esta vía, en cumplimiento de la Ley Estatutaria 1581 de 2012 de Protección de datos personales y el Decreto Reglamentario 1377 del 27 de junio de 2013 y demás normas concordantes. Para conocer más sobre nuestra Política de tratamiento de datos personales, lo invitamos a ingresar al siguiente link: <a href='https://prosperidadsocial.gov.co/politica-de-proteccion-de-datos-personales/'>https://prosperidadsocial.gov.co/politica-de-proteccion-de-datos-personales/</a></p>";
                        $nc_address=$gcar_datos_correo.";";
                        $nc_cc="";
                        $modulo_plataforma='Gestión Citas';
                        $estado_notificacion=notificacion_agendamiento($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
                        if ($estado_notificacion) {
                          
                        } else {
                            $estado_notificacion=notificacion_agendamiento($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
                        }

                        $nsms_identificador=$gcar_consecutivo;
                        $contenido_sms="Estimado (a) ".$gcar_datos_nombres.", su cita en el punto ".$resultado_registros_agenda[0][8]." - ".$resultado_registros_agenda[0][9]." será atendida el día, ".$array_dias_nombre[date('N', strtotime($resultado_registros_agenda[0][4]))].' '.date('d', strtotime($resultado_registros_agenda[0][4])).' de '.$array_meses[intval(date('m', strtotime($resultado_registros_agenda[0][4])))].' de '.date('Y', strtotime($resultado_registros_agenda[0][4])).", a las ".date('h:i A', strtotime($resultado_registros_agenda[0][5])).". Revise su correo para más información.";
                        $nsms_url='';
                        $nsms_destino=validar_input($resultado_registros_reserva[0][8]);
                        $estado_notificacion_sms=notificacion_agendamiento_sms($enlace_db, $nsms_identificador, $nsms_destino, $contenido_sms, $nsms_url);
                        if ($estado_notificacion_sms) {
                            $estado_sms=1;
                        }

                        $gcar_estado='Cancelada';
                        $gcar_cancela_fecha=date('Y-m-d H:i:s');
                        // Prepara la sentencia
                        $consulta_actualizar_reserva = $enlace_db->prepare("UPDATE `gestion_citas_agenda_reservas` SET `gcar_estado`=?, `gcar_cancela_fecha`=? WHERE `gcar_consecutivo`=?");

                        // Agrega variables a sentencia preparada
                        $consulta_actualizar_reserva->bind_param('sss', $gcar_estado, $gcar_cancela_fecha, $id_consecutivo);
                        
                        // Ejecuta sentencia preparada
                        $consulta_actualizar_reserva->execute();

                        $gca_estado_agenda='Disponible';
                        // Prepara la sentencia
                        $consulta_actualizar_agenda_anterior = $enlace_db->prepare("UPDATE `gestion_citas_agenda` SET `gca_estado_agenda`=? WHERE `gca_id`=? AND `gca_estado_agenda`='Reservada'");

                        // Agrega variables a sentencia preparada
                        $consulta_actualizar_agenda_anterior->bind_param('ss', $gca_estado_agenda, $resultado_registros_reserva[0][1]);
                        
                        // Ejecuta sentencia preparada
                        $consulta_actualizar_agenda_anterior->execute();

                        header("Location:reagendar-confirmacion?cons=".base64_encode($gcar_consecutivo)."&doc=".base64_encode($gcar_datos_numero_identificacion));
                    } else {
                        $gca_estado_agenda='Disponible';
                        // Prepara la sentencia
                        $consulta_actualizar_agenda = $enlace_db->prepare("UPDATE `gestion_citas_agenda` SET `gca_estado_agenda`=? WHERE `gca_id`=? AND `gca_estado_agenda`='Reservada'");

                        // Agrega variables a sentencia preparada
                        $consulta_actualizar_agenda->bind_param('ss', $gca_estado_agenda, $id_cita);
                        
                        // Ejecuta sentencia preparada
                        $consulta_actualizar_agenda->execute();
                    }
                } else {
                    $cita_no_disponible=1;
                }
            } else {
                $cita_no_disponible=1;
            }
        } else {
            $cita_datos_incompletos=1;
        }
    } else {
        $consulta_string_reserva="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_consecutivo`=? AND `gcar_datos_numero_identificacion`=?";

        $consulta_registros_reserva = $enlace_db->prepare($consulta_string_reserva);
        $consulta_registros_reserva->bind_param("ss", $consecutivo, $doc_identidad);
        $consulta_registros_reserva->execute();
        $resultado_registros_reserva = $consulta_registros_reserva->get_result()->fetch_all(MYSQLI_NUM);
    }

    if (!isset($resultado_registros_reserva)) {
        $resultado_registros_reserva=array();
    }

    $datos_url=serialize($datos);
    $datos_url=urlencode($datos_url);

    /*Enlace para botón finalizar y cancelar*/
    $ruta_cancelar_finalizar="https://www.prosperidadsocial.gov.co/";
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
                            <div class="row">
                                <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                                <div class="col-md-12 pt-0 px-0 text-center mb-2">
                                    <img src="<?php echo IMAGES; ?>logo-cliente.png" class="img-fluid">
                                </div>
                                <div class="col-md-12">
                                    <?php if($cita_no_disponible): ?>
                                        <div class="card text-center">
                                          <div class="card-header">
                                            Cita NO Disponible
                                          </div>
                                          <div class="card-body">
                                            <p class="card-text">Lo sentimos, la cita ya sido agendada por otro usuario. Por favor seleccione otra de las opciones disponibles.</p>
                                            <div class="form-group">
                                                <a href="agendamiento?t=<?php echo base64_encode($tipo); ?>&d=<?php echo $datos_url; ?>" class="btn btn-primary">Aceptar</a>
                                            </div>
                                          </div>
                                        </div>
                                    <?php elseif($cita_datos_incompletos): ?>
                                        <div class="card text-center">
                                          <div class="card-header">
                                            Error de agendamiento
                                          </div>
                                          <div class="card-body">
                                            <p class="card-text">Lo sentimos, se ha presentado un error al intentar agendar la cita, por favor intente nuevamente.</p>
                                            <div class="form-group">
                                                <a href="inicio" class="btn btn-primary">Aceptar</a>
                                            </div>
                                          </div>
                                        </div>
                                    <?php elseif (count($resultado_registros_reserva)>0): ?>
                                        <div class="card text-center">
                                          <div class="card-header">
                                            Confirmación de cita
                                          </div>
                                          <div class="card-body">
                                            <p class="card-text">Estimado (a) <b><?php echo $resultado_registros_reserva[0][6]; ?></b>, su cita de radicado <b><?php echo $consecutivo; ?></b> en el punto <b><?php echo $resultado_registros_reserva[0][12]; ?></b> en la dirección <b><?php echo $resultado_registros_reserva[0][13]; ?></b> el <b><?php echo $array_dias_nombre[date('N', strtotime($resultado_registros_reserva[0][15]))].' '.date('d', strtotime($resultado_registros_reserva[0][15])).' de '.$array_meses[intval(date('m', strtotime($resultado_registros_reserva[0][15])))].' de '.date('Y', strtotime($resultado_registros_reserva[0][15])); ?></b> a las <b><?php echo date('h:i A', strtotime($resultado_registros_reserva[0][16])); ?></b> ha sido agendada con éxito. Revise su correo para más información.</p>
                                            <div class="form-group">
                                                <a href="inicio" class="btn btn-primary">Aceptar</a>
                                            </div>
                                          </div>
                                        </div>
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