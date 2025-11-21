<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas-Punto Atención";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas";
  $subtitle = "Agenda | Cancelar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $url_salir="agenda?pagina=".$pagina."&id=".$filtro_permanente;
error_reporting(E_ALL);
ini_set('display_errors', '1');

  

$agenda_turno[]='2022-12-11-3-12:00-4-0';
$agenda_turno[]='2022-12-11-3-09:00-1-0';
$agenda_turno[]='2022-12-10-3-15:00-7-1';
$agenda_turno[]='2022-12-11-3-09:00-1-1';
$agenda_turno[]='2022-12-10-3-15:00-7-10';
$agenda_turno[]='2022-12-10-3-15:00-7-100';
$agenda_turno[]='2022-12-11-3-10:00-2-0';
$agenda_turno[]='2022-12-11-3-11:00-3-0';
$agenda_turno[]='2022-12-11-3-10:00-2-1';
$agenda_turno[]='2022-12-11-3-08:00-0-0';
$agenda_turno[]='2022-12-11-3-11:00-3-1';
$agenda_turno[]='2022-12-11-3-11:00-3-10';
$agenda_turno[]='2022-12-11-3-15:00-7-0';
$agenda_turno[]='2022-12-11-3-08:00-0-1';
$agenda_turno[]='2022-12-10-3-15:00-7-101';
$agenda_turno[]='2022-12-10-3-14:00-6-0';
$agenda_turno[]='2022-12-11-3-13:00-5-0';
$agenda_turno[]='2022-12-10-3-14:00-6-1';
$agenda_turno[]='2022-12-11-3-15:00-7-1';
$agenda_turno[]='2022-12-10-3-15:00-7-102';
$agenda_turno[]='2022-12-10-3-15:00-7-103';
$agenda_turno[]='2022-12-10-3-15:00-7-104';
$agenda_turno[]='2022-12-10-3-15:00-7-105';
$agenda_turno[]='2022-12-10-3-13:00-5-0';
$agenda_turno[]='2022-12-11-3-10:00-2-10';
$agenda_turno[]='2022-12-11-3-13:00-5-1';
$agenda_turno[]='2022-12-11-3-08:00-0-10';
$agenda_turno[]='2022-12-10-3-13:00-5-1';
$agenda_turno[]='2022-12-10-3-13:00-5-10';
$agenda_turno[]='2022-12-11-3-15:00-7-10';
$agenda_turno[]='2022-12-10-3-14:00-6-10';
$agenda_turno[]='2022-12-11-3-08:00-0-100';
$agenda_turno[]='2022-12-11-3-08:00-0-101';
$agenda_turno[]='2022-12-11-3-09:00-1-10';
$agenda_turno[]='2022-12-11-3-12:00-4-1';
$agenda_turno[]='2022-12-11-3-08:00-0-102';
$agenda_turno[]='2022-12-10-3-13:00-5-100';
$agenda_turno[]='2022-12-11-3-14:00-6-0';
$agenda_turno[]='2022-12-10-3-15:00-7-106';
$agenda_turno[]='2022-12-10-3-15:00-7-107';
$agenda_turno[]='2022-12-10-3-15:00-7-108';
$agenda_turno[]='2022-12-10-3-15:00-7-109';
$agenda_turno[]='2022-12-11-3-15:00-7-100';
$agenda_turno[]='2022-12-10-3-14:00-6-100';
$agenda_turno[]='2022-12-10-3-15:00-7-11';
$agenda_turno[]='2022-12-10-3-15:00-7-110';
$agenda_turno[]='2022-12-11-3-10:00-2-100';
$agenda_turno[]='2022-12-11-3-13:00-5-10';
$agenda_turno[]='2022-12-11-3-13:00-5-100';
$agenda_turno[]='2022-12-10-3-13:00-5-101';
$agenda_turno[]='2022-12-10-3-15:00-7-111';
$agenda_turno[]='2022-12-10-3-13:00-5-102';
$agenda_turno[]='2022-12-10-3-14:00-6-101';
$agenda_turno[]='2022-12-10-3-15:00-7-112';
$agenda_turno[]='2022-12-10-3-13:00-5-103';
$agenda_turno[]='2022-12-10-3-14:00-6-102';
$agenda_turno[]='2022-12-10-3-15:00-7-113';
$agenda_turno[]='2022-12-10-3-15:00-7-114';
$agenda_turno[]='2022-12-10-3-15:00-7-115';
$agenda_turno[]='2022-12-10-3-15:00-7-116';
$agenda_turno[]='2022-12-10-3-15:00-7-117';
$agenda_turno[]='2022-12-10-3-15:00-7-118';
$agenda_turno[]='2022-12-10-3-13:00-5-104';
$agenda_turno[]='2022-12-10-3-14:00-6-103';
$agenda_turno[]='2022-12-10-3-15:00-7-119';
$agenda_turno[]='2022-12-10-3-15:00-7-12';
$agenda_turno[]='2022-12-10-3-15:00-7-120';
$agenda_turno[]='2022-12-10-3-14:00-6-104';
$agenda_turno[]='2022-12-10-3-14:00-6-105';
$agenda_turno[]='2022-12-10-3-13:00-5-105';
$agenda_turno[]='2022-12-10-3-15:00-7-121';
$agenda_turno[]='2022-12-10-3-15:00-7-122';
$agenda_turno[]='2022-12-10-3-14:00-6-106';
$agenda_turno[]='2022-12-10-3-13:00-5-106';
$agenda_turno[]='2022-12-10-3-15:00-7-123';
$agenda_turno[]='2022-12-10-3-15:00-7-124';
$agenda_turno[]='2022-12-10-3-15:00-7-125';
$agenda_turno[]='2022-12-10-3-15:00-7-126';
$agenda_turno[]='2022-12-10-3-14:00-6-107';
$agenda_turno[]='2022-12-10-3-14:00-6-108';
$agenda_turno[]='2022-12-10-3-15:00-7-127';
$agenda_turno[]='2022-12-10-3-14:00-6-109';
$agenda_turno[]='2022-12-10-3-15:00-7-128';
$agenda_turno[]='2022-12-10-3-15:00-7-129';
$agenda_turno[]='2022-12-11-3-14:00-6-1';
$agenda_turno[]='2022-12-11-3-14:00-6-10';
$agenda_turno[]='2022-12-10-3-15:00-7-13';
$agenda_turno[]='2022-12-10-3-14:00-6-11';
$agenda_turno[]='2022-12-10-3-15:00-7-130';
$agenda_turno[]='2022-12-10-3-15:00-7-132';
$agenda_turno[]='2022-12-10-3-15:00-7-133';
$agenda_turno[]='2022-12-10-3-15:00-7-131';
$agenda_turno[]='2022-12-10-3-15:00-7-134';
$agenda_turno[]='2022-12-10-3-13:00-5-108';
$agenda_turno[]='2022-12-10-3-13:00-5-109';
$agenda_turno[]='2022-12-10-3-13:00-5-11';
$agenda_turno[]='2022-12-10-3-15:00-7-135';
$agenda_turno[]='2022-12-10-3-13:00-5-110';
$agenda_turno[]='2022-12-10-3-14:00-6-110';
$agenda_turno[]='2022-12-10-3-15:00-7-136';
$agenda_turno[]='2022-12-10-3-15:00-7-137';
$agenda_turno[]='2022-12-10-3-13:00-5-111';
$agenda_turno[]='2022-12-10-3-15:00-7-138';
$agenda_turno[]='2022-12-10-3-15:00-7-139';
$agenda_turno[]='2022-12-10-3-15:00-7-14';
$agenda_turno[]='2022-12-10-3-15:00-7-140';
$agenda_turno[]='2022-12-10-3-15:00-7-141';
$agenda_turno[]='2022-12-10-3-15:00-7-142';
$agenda_turno[]='2022-12-10-3-15:00-7-143';
$agenda_turno[]='2022-12-10-3-15:00-7-144';
$agenda_turno[]='2022-12-11-3-15:00-7-101';
$agenda_turno[]='2022-12-10-3-15:00-7-145';
$agenda_turno[]='2022-12-10-3-14:00-6-111';
$agenda_turno[]='2022-12-10-3-15:00-7-146';
$agenda_turno[]='2022-12-10-3-15:00-7-147';
$agenda_turno[]='2022-12-10-3-15:00-7-148';
$agenda_turno[]='2022-12-10-3-14:00-6-112';
$agenda_turno[]='2022-12-10-3-15:00-7-149';
$agenda_turno[]='2022-12-10-3-15:00-7-15';
$agenda_turno[]='2022-12-11-3-15:00-7-102';
$agenda_turno[]='2022-12-10-3-14:00-6-113';
$agenda_turno[]='2022-12-10-3-15:00-7-150';
$agenda_turno[]='2022-12-10-3-15:00-7-151';
$agenda_turno[]='2022-12-10-3-15:00-7-152';
$agenda_turno[]='2022-12-10-3-15:00-7-153';
$agenda_turno[]='2022-12-10-3-14:00-6-114';
$agenda_turno[]='2022-12-11-3-15:00-7-103';
$agenda_turno[]='2022-12-10-3-14:00-6-115';
$agenda_turno[]='2022-12-10-3-15:00-7-154';
$agenda_turno[]='2022-12-11-3-10:00-2-101';
$agenda_turno[]='2022-12-10-3-15:00-7-155';
$agenda_turno[]='2022-12-10-3-15:00-7-156';
$agenda_turno[]='2022-12-11-3-11:00-3-100';
$agenda_turno[]='2022-12-10-3-15:00-7-157';
$agenda_turno[]='2022-12-10-3-15:00-7-158';
$agenda_turno[]='2022-12-10-3-15:00-7-159';
$agenda_turno[]='2022-12-10-3-15:00-7-16';
$agenda_turno[]='2022-12-10-3-14:00-6-116';
$agenda_turno[]='2022-12-10-3-15:00-7-160';
$agenda_turno[]='2022-12-10-3-13:00-5-107';



  $gcar_cancela_motivo='Atención de citas hasta el 10 de diciembre, 1:00pm';

  // Prepara la sentencia
  $consulta_actualizar_agenda = $enlace_db->prepare("UPDATE `gestion_citasfa_agenda` SET `gca_estado`='Cancelada', `gca_estado_agenda`='Cancelada' WHERE `gca_id`=?");

  // Agrega variables a sentencia preparada
  $consulta_actualizar_agenda->bind_param('s', $gca_id);

  // Prepara la sentencia
  $consulta_actualizar_reserva = $enlace_db->prepare("UPDATE `gestion_citasfa_agenda_reservas` SET `gcar_estado`='Cancelada' WHERE `gcar_cita`=?");

  // Agrega variables a sentencia preparada
  $consulta_actualizar_reserva->bind_param('s', $gcar_cita);
  
  $control_cancela=0;
  $control_notificacion=0;
  for ($i=0; $i < count($agenda_turno); $i++) { 
    echo $gca_id=$agenda_turno[$i];
    echo "<br>";
    $gcar_cancela_fecha=date('Y-m-d H:i:s');
    $gcar_cita=$agenda_turno[$i];
    
    $consulta_string="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_observaciones`, `gcar_atencion_usuario`, `gcar_atencion_fecha`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora`, TC.`gca_estado`, TC.`gca_estado_agenda`, `gcar_estado` FROM `gestion_citasfa_agenda_reservas` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citasfa_agenda` AS TC ON `gestion_citasfa_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_cita`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $gcar_cita);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    // echo "<pre>";
    // print_r($resultado_registros);
    // echo "</pre>";

    // Ejecuta sentencia preparada
    $consulta_actualizar_agenda->execute();

    // Ejecuta sentencia preparada
    $consulta_actualizar_reserva->execute();

    if (comprobarSentencia($enlace_db->info)) {
      echo "ACTUALIZA";
      echo "<br>";
        $control_cancela++;

        //PROGRAMACIÓN NOTIFICACIÓN
          $asunto='Cancelación Cita';
          $referencia='Datos Cita';
          $contenido="<p>Estimado, <b>".$resultado_registros[0][6]."</b><br></p>
              <p>Su cita para atención presencial en <b>Prosperidad Social</b> en el punto ".$resultado_registros[0][15]." en la dirección ".$resultado_registros[0][16]." el ".date('d-m-Y', strtotime($resultado_registros[0][18]))." a las ".date('h:i A', strtotime($resultado_registros[0][19]))." ha sido <b>cancelada</b>. Motivo: ".$gcar_cancela_motivo."</p>
            <p>Le recordamos que usted puede consultar, reagendar o cancelar su cita, ingresando en la siguiente página web: <a href='https://dps.iq-online.net.co/citas/inicio'>https://dps.iq-online.net.co/citas/inicio</a></p>
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
          $nc_address=$resultado_registros[0][7].";";
          $nc_cc="";
          $modulo_plataforma='Gestión Agenda';
          $estado_notificacion=notificacion_agendamiento($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
          if ($estado_notificacion) {
            $control_notificacion++;
            echo "correo";
            echo "<br>";
          } else {

          }

          $nsms_identificador=$resultado_registros[0][0];
          $contenido_sms="Estimado (a) ".$resultado_registros[0][6].", su cita en el punto ".$resultado_registros[0][15]." - ".$resultado_registros[0][16]." el ".$array_dias_nombre[date('N', strtotime($resultado_registros[0][18]))].' '.date('d', strtotime($resultado_registros[0][18])).' de '.$array_meses[intval(date('m', strtotime($resultado_registros[0][18])))].' de '.date('Y', strtotime($resultado_registros[0][18])).", a las ".date('h:i A', strtotime($resultado_registros[0][19]))." ha sido cancelada. Motivo: ".$gcar_cancela_motivo;
          $nsms_url='';
          $nsms_destino=$resultado_registros[0][8];
          $estado_notificacion_sms=notificacion_agendamiento_sms($enlace_db, $nsms_identificador, $nsms_destino, $contenido_sms, $nsms_url);
          if ($estado_notificacion_sms) {
              $estado_sms=1;
              echo "sms";
              echo "<br>";
          }
    }
  }

  if ($control_cancela==count($agenda_turno) AND $control_notificacion==count($agenda_turno)) {
    $respuesta_accion = "alertButton('success', 'Agenda cancelada', 'Agenda cancelada exitosamente', '".$url_salir."');";
    // $_SESSION[APP_SESSION.'_registro_cancelado_agendamiento']=1;
  } else {
    $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cancelar la agenda');";
  }
?>