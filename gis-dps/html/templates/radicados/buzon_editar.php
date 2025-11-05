<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Radicación";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Radicación";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $estado=validar_input(base64_decode($_GET['estado']));
  $subtitle = "Buzón | ".$bandeja." | ".$estado.' | Editar';
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);

  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');

  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="buzon?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&estado=".base64_encode($estado);
  $url_editar="buzon_editar?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&estado=".base64_encode($estado);

  $consulta_string="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`, `grc_registro_fecha_hora`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo` FROM `gestion_radicacion_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos`.`grc_responsable`=TAG.`usu_id` WHERE `grc_id`=?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_historico="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom` FROM `gestion_radicacion_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_radicacion_casos_historial`.`grch_correo_de`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos_historial`.`grch_registro_usuario`=TAG.`usu_id` WHERE `grch_radicado_id`=? AND `grch_tipo`<>'Borrador' ORDER BY `grch_id` ASC";

  $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
  $consulta_registros_historico->bind_param("s", $id_registro);
  $consulta_registros_historico->execute();
  $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_borrador="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom` FROM `gestion_radicacion_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_radicacion_casos_historial`.`grch_correo_de`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos_historial`.`grch_registro_usuario`=TAG.`usu_id` WHERE `grch_radicado_id`=? AND `grch_tipo`='Borrador' ORDER BY `grch_id` ASC";

  $consulta_registros_borrador = $enlace_db->prepare($consulta_string_borrador);
  $consulta_registros_borrador->bind_param("s", $id_registro);
  $consulta_registros_borrador->execute();
  $resultado_registros_borrador = $consulta_registros_borrador->get_result()->fetch_all(MYSQLI_NUM);

  $control_borrador=false;
  if (count($resultado_registros_borrador)>0) {
    $control_borrador=true;
  }

  
  // VALIDA DUPLICADOS
  $fechah_inicio=date("Y-m-d", strtotime("- 60 days", strtotime(date('Y-m-d'))));
  $resultado_registros_duplicado=array();
  if ($resultado_registros[0][2]<>'Envío Radicado a Ciudadano') {
    $consulta_string_duplicado="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`, `grc_registro_fecha_hora`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo` FROM `gestion_radicacion_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos`.`grc_responsable`=TAG.`usu_id` WHERE `grc_id`<>? AND `grc_correo_remitente`=? AND `grc_correo_fecha`>=? AND `grc_dividido`<>? AND `grc_radicado`<>? AND `grc_duplicado`='' AND `grc_correo_remitente`<>'microsoftexchange329e71ec88ae4615bbc36ab6ce41109e@prosperidadsocial.gov.co' ORDER BY `grc_correo_fecha` DESC";

    $consulta_registros_duplicado = $enlace_db->prepare($consulta_string_duplicado);
    $consulta_registros_duplicado->bind_param("sssss", $id_registro, $resultado_registros[0][14], $fechah_inicio, $resultado_registros[0][1], $resultado_registros[0][11]);
    $consulta_registros_duplicado->execute();
    $resultado_registros_duplicado = $consulta_registros_duplicado->get_result()->fetch_all(MYSQLI_NUM);
  }



  $array_estado_alert['Pendiente']='warning';
  $array_estado_alert['En trámite']='dark';
  $array_estado_alert['Finalizado']='success';

  $consulta_string_firma="SELECT `grcp_id`, `grcp_nombre`, `grcp_estado`, `grcp_tipo`, `grcp_contenido`, `grcp_actualiza_usuario`, `grcp_actualiza_fecha`, `grcp_registro_usuario`, `grcp_registro_fecha`, TUA.`usu_nombres_apellidos` FROM `gestion_radicacion_casos_plantillas` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_radicacion_casos_plantillas`.`grcp_actualiza_usuario`=TUA.`usu_id` WHERE `grcp_estado`='Activo' AND `grcp_tipo`='Firma'";
  $consulta_registros_firma = $enlace_db->prepare($consulta_string_firma);
  // $consulta_registros_firma->bind_param("s", $id_registro);
  $consulta_registros_firma->execute();
  $resultado_registros_firma = $consulta_registros_firma->get_result()->fetch_all(MYSQLI_NUM);

  $firma="Cordialmente,<br><table style='width:100%; max-width: 600px; font-size: 11px; font-family: Times New Roman', Times, serif; border: solid 0px !important;'>
                      <tr>
                          <td style='width: 130px;'>
                            <img src='https://dps.iq-online.net.co/assets/images/logo_cliente_notificacion_2.png' style='width: 130px; max-width: 130px;'>
                          </td>
                          <td style='padding: 5px 5px 5px 5px; text-align: left;'>".str_replace('<p>', '<p style="padding:0px; margin:0px;">', $resultado_registros_firma[0][4])."</td>
                      </tr>
                      <tr>
                          <td style='width: 130px;'>
                            <img src='https://dps.iq-online.net.co/assets/images/logo_certificacion_notificacion_2.png' style='width: 130px;max-width: 130px;'>
                          </td>
                          <td style='padding: 5px 5px 5px 5px; text-align: left;'>
                            
                          </td>
                      </tr>
                  </table>
                  <center><b>Todas las personas tienen derecho a presentar peticiones respetuosas ante las autoridades de forma GRATUITA<br>No recurra a intermediarios. No pague por sus derechos. DENUNCIE</b></center>";
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <style type="text/css">
    .scroll-mail::-webkit-scrollbar-track {
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
      border-radius: 10px;
      background-color: #F5F5F5;
    }

    .scroll-mail::-webkit-scrollbar {
      width: 5px;
      height: 8px;
      background-color: #F5F5F5;
    }

    .scroll-mail::-webkit-scrollbar-thumb {
      border-radius: 10px;
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
      background-color: darkgrey;
    }

    .scroll-mail {
        overflow-y: auto;
        overflow-x: auto;
        height: 600px !important;
    }

    .ck-editor__editable_inline {
        min-height: 650px;
        max-height: 650px;
    }

    /* Estilos personalizados para el campo de entrada de archivos */
    .custom-file-upload {
        position: relative;
        display: inline-block;
    }

    .custom-file-upload input[type="file"] {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0); /* Para Internet Explorer */
    }

    .custom-file-upload label {
        display: inline-block;
        padding: 10px 10px 8px 10px;
        background-color: #3498db;
        color: #fff;
        cursor: pointer;
        border-radius: 4px;
    }

    .custom-file-upload label:hover {
        background-color: #2980b9;
    }

    .tachado {
        text-decoration:line-through;
    }

    /* Estilo para quitar todos los estilos dentro del editor CKEditor */
    .ck-editor .table td img {
      /* Resetear todos los estilos */
      all: unset !important;
    }
  </style>
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
            <div class="col-lg-3 flex-column pe-0">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="col-md-12 alert border px-2 py-2 my-1 font-size-11">
                        <!-- <textarea style="width: 500px; height: 500px;"><?php echo $firma; ?></textarea> -->
                        <p class="my-0 px-0 py-1 font-size-11"><b>De: </b><?php echo $resultado_registros[0][14]; ?></p>
                        <span><b>Asunto: </b><?php echo $resultado_registros[0][15]; ?></span>
                        <div class="my-1"></div>
                        <span class="info_original alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-1 py-0 me-1"><span class="fas fa-qrcode me-1"></span><?php echo $resultado_registros[0][1]; ?></span>
                        <span class="info_original alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-1 py-0 me-1"><?php echo $resultado_registros[0][7]; ?></span>
                        <span class="info_original alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-1 py-0 me-1"><span class="fas fa-calendar-alt me-1"></span><?php echo date('d/m/Y H:i:s', strtotime($resultado_registros[0][16])); ?></span>
                        <div class="my-2"></div>
                        <span class="info_original alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-1 py-0 me-1"><span class="fas fa-sitemap me-1"></span><?php echo $resultado_registros[0][2]; ?></span>
                        <?php if($resultado_registros[0][2]=='Ciudadanos'): ?><span class="info_original alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-1 py-0 me-1"><span class="fas fa-timeline me-1"></span><?php echo $resultado_registros[0][3]; ?></span><?php endif; ?>
                        <?php if($resultado_registros[0][5]!=''): ?><span class="info_original alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-1 py-0 me-1"><span class="fas fa-user-cog me-1"></span><?php echo $resultado_registros[0][5]; ?></span><?php endif; ?>
                        <span class="info_original alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-1 py-0 me-1"><span class="fas fa-user-tie me-1"></span><?php echo $resultado_registros[0][19]; ?></span>
                      </div>
                      <button class="btn btn-danger mb-1 p-1" type="button" onclick="alertButton('exit', null, null, '<?php echo $url_salir; ?>');">Salir de la gestión</button>
                      
                      <div id="contenido_desagregar_lista"></div>

                      <?php if(count($resultado_registros_duplicado)>0): ?>
                        <div class="col-md-12">
                          <p class="alert background-principal color-blanco py-1 px-2 my-0 font-size-11"><span class="fas fa-history"></span> Casos relacionados</p>
                        </div>
                      <?php endif; ?>

                      <?php for ($i=0; $i < count($resultado_registros_duplicado); $i++): ?>
                        <?php if(strlen($resultado_registros[0][15])>10 AND $resultado_registros[0][15]==$resultado_registros_duplicado[$i][15]): ?>
                          <div class="col-md-12 alert border px-2 py-2 my-1 font-size-11">
                            <span class="alert alert-danger px-1 py-0 me-1">Posible duplicado</span><a class="btn btn-success btn-icon px-1 py-1 float-end" title="Comparar" onClick="validar_duplicado('<?php echo base64_encode($resultado_registros_duplicado[$i][0]); ?>');"><i class="fas fa-object-ungroup font-size-11"></i></a><br>
                            <span><b>Asunto: </b><?php echo $resultado_registros_duplicado[$i][15]; ?></span>
                            <div class="my-1"></div>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-qrcode me-1"></span><?php echo $resultado_registros_duplicado[$i][1]; ?></span>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><?php echo $resultado_registros_duplicado[$i][7]; ?></span>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-calendar-alt me-1"></span><?php echo date('d/m/Y H:i a', strtotime($resultado_registros_duplicado[$i][16])); ?></span>
                            <div class="my-2"></div>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-sitemap me-1"></span><?php echo $resultado_registros_duplicado[$i][2]; ?></span>
                            <?php if($resultado_registros_duplicado[$i][2]=='Ciudadanos'): ?><span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-timeline me-1"></span><?php echo $resultado_registros_duplicado[$i][3]; ?></span><?php endif; ?>
                            <?php if($resultado_registros_duplicado[$i][5]!=''): ?><span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-user-cog me-1"></span><?php echo $resultado_registros_duplicado[$i][5]; ?></span><?php endif; ?>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-user-tie me-1"></span><?php echo $resultado_registros_duplicado[$i][19]; ?></span>
                          </div>
                        <?php endif; ?>
                      <?php endfor; ?>

                      <?php for ($i=0; $i < count($resultado_registros_duplicado); $i++): ?>
                        <?php if($resultado_registros[0][15]!=$resultado_registros_duplicado[$i][15]): ?>
                          <div class="col-md-12 alert border px-2 py-2 my-1 font-size-11">
                            <span><b>Asunto: </b><?php echo $resultado_registros_duplicado[$i][15]; ?><a class="btn btn-success btn-icon px-1 py-1 float-end" title="Comparar" onClick="validar_duplicado('<?php echo base64_encode($resultado_registros_duplicado[$i][0]); ?>');"><i class="fas fa-object-ungroup font-size-11"></i></a></span>
                            <div class="my-1"></div>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-qrcode me-1"></span><?php echo $resultado_registros_duplicado[$i][1]; ?></span>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><?php echo $resultado_registros_duplicado[$i][7]; ?></span>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-calendar-alt me-1"></span><?php echo date('d/m/Y H:i a', strtotime($resultado_registros_duplicado[$i][16])); ?></span>
                            <div class="my-2"></div>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-sitemap me-1"></span><?php echo $resultado_registros_duplicado[$i][2]; ?></span>
                            <?php if($resultado_registros_duplicado[$i][2]=='Ciudadanos'): ?><span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-timeline me-1"></span><?php echo $resultado_registros_duplicado[$i][3]; ?></span><?php endif; ?>
                            <?php if($resultado_registros_duplicado[$i][5]!=''): ?><span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-user-cog me-1"></span><?php echo $resultado_registros_duplicado[$i][5]; ?></span><?php endif; ?>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros_duplicado[$i][7]]; ?> px-1 py-0 me-1"><span class="fas fa-user-tie me-1"></span><?php echo $resultado_registros_duplicado[$i][19]; ?></span>
                          </div>
                        <?php endif; ?>
                      <?php endfor; ?>
                    </div>
                  </div>
                </div>
              </div>
              
            </div>
            <div class="col-lg-9 flex-column" id="visor_correo_1">
              <?php for ($i=0; $i < count($resultado_registros_historico); $i++): ?>
                <?php if($resultado_registros_historico[$i][3]!='Borrador'): ?>
                <?php
                  if ($resultado_registros_historico[$i][3]=='Radicado') {
                    $fecha_gestion=date('d/m/Y', strtotime($resultado_registros_historico[$i][19]));
                    $hora_gestion=date('H:i', strtotime($resultado_registros_historico[$i][19]));
                  } elseif ($resultado_registros_historico[$i][3]=='Gestión') {
                    if ($resultado_registros_historico[$i][28]!='') {
                      $fecha_gestion=date('d/m/Y', strtotime($resultado_registros_historico[$i][28]));
                      $hora_gestion=date('H:i', strtotime($resultado_registros_historico[$i][28]));
                    } else {
                      $fecha_gestion='Pendiente';
                      $hora_gestion='Pendiente';
                    }
                  } elseif ($resultado_registros_historico[$i][3]=='Borrador') {
                    $fecha_gestion=date('d/m/Y', strtotime($resultado_registros_historico[$i][30]));
                    $hora_gestion=date('H:i', strtotime($resultado_registros_historico[$i][30]));
                  }
                ?>
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card my-1">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-9">
                              <p class="font-size-12">
                                <a class="btn btn-success py-1 px-1 font-size-11" onClick="mostrarOcultar('<?php echo $resultado_registros_historico[$i][0]; ?>');"><span class="fas fa-plus"></span></a> <span class="fas fa-user"></span> De: <?php echo ($resultado_registros_historico[$i][3]=='Radicado') ? $resultado_registros_historico[$i][15] : $resultado_registros_historico[$i][33]; ?>
                                <?php if ($resultado_registros_historico[$i][3]=='Radicado'): ?>
                                  <span class="alert alert-dark px-1 py-0 my-0 font-size-11"><span class="fas fa-envelope me-1"></span>Radicado <?php echo ($resultado_registros_historico[$i][8]=='Si') ? '[Desagregado de '.$resultado_registros_historico[$i][11].']' : ''; ?></span>
                                <?php elseif ($resultado_registros_historico[$i][3]=='Gestión'): ?>
                                  <span class="alert alert-success px-1 py-0 my-0 font-size-11"><span class="fas fa-user-check me-1"></span>Gestión </span>
                                <?php elseif ($resultado_registros_historico[$i][3]=='Borrador'): ?>
                                  <span class="alert alert-warning px-1 py-0 my-0 font-size-11"><span class="fas fa-user-cog me-1"></span>Borrador</span>
                                <?php endif; ?>
                              </p>
                              <?php if($resultado_registros_historico[$i][17]!=''): ?>
                                <p class="font-size-12">
                                  <span class="fas fa-users"></span> Cc: <?php echo $resultado_registros_historico[$i][17]; ?>
                                </p>
                              <?php endif; ?>
                              <p class="font-size-12 fw-bold" id="asunto_correo_original"><?php echo $resultado_registros_historico[$i][20]; ?></p>

                              <?php if($resultado_registros_historico[$i][3]=='Radicado'): ?>
                                <?php
                                  $nombre_dia_final=$array_dias_nombre[intval(date("N", strtotime($resultado_registros_historico[$i][19])))];
                                  $nombre_mes_final=$array_meses[intval(date("m", strtotime($resultado_registros_historico[$i][19])))];
                                  //Se configura y convierte el formato de fecha de enviado
                                  $fecha_enviado=$nombre_dia_final.", ".date("d", strtotime($resultado_registros_historico[$i][19]))." de ".$nombre_mes_final." de ".date("Y H:i a", strtotime($resultado_registros_historico[$i][19]));
                                  
                                  $detalle_correo_original='<hr><b>De:</b> '.$resultado_registros_historico[$i][15].'<br> 
                                    <b>Enviados:</b> '.$fecha_enviado.'<br>
                                    <b>Para:</b> '.$resultado_registros_historico[$i][16].'<br>';
                                  if ($resultado_registros_historico[$i][17]!='') {
                                    $detalle_correo_original.='<b>Cc:</b> '.$resultado_registros_historico[$i][17].'<br>';
                                  }

                                  $detalle_correo_original.='<b>Asunto:</b> '.$resultado_registros_historico[$i][20].'<br><br><br>';
                                ?>
                                <textarea class="form-control form-control-sm d-none" name="correo_remitente" id="correo_remitente"><?php echo $resultado_registros_historico[$i][15]; ?></textarea>
                                <textarea class="form-control form-control-sm d-none" name="info_correo" id="info_correo"><?php echo $detalle_correo_original; ?></textarea>
                                <textarea class="form-control form-control-sm d-none" name="firma_contenido" id="firma_contenido"><?php echo $firma; ?></textarea>
                              <?php endif; ?>
                              <div class="col-md-12 font-size-12 mb-2">
                                <div id='adjuntos_lista_<?php echo $resultado_registros_historico[$i][0]; ?>'></div>
                              </div>
                          </div>
                          <div class="col-3 text-end">
                              <?php if ($resultado_registros_historico[$i][3]=='Radicado'): ?>
                                <input type="number" class="form-control form-control-sm font-size-11 py-0 d-none" name="grc_dividido_cantidad" id="grc_dividido_cantidad" value="" step="1" min="1" max="120" placeholder="Cantidad" style="width: 110px; height: 10px;" required disabled>
                                <a class="btn btn-dark py-1 px-1 <?php echo ($control_borrador) ? 'd-none' : ''; ?>" id="btn_unificar" onclick="unificar();" title="Unificar"><span class="fas fa-object-group"></span></a>
                                <?php if($resultado_registros_historico[$i][8]!='Si'): ?>
                                  <a class="btn btn-dark py-1 px-1 <?php echo ($control_borrador) ? 'd-none' : ''; ?>" id="btn_desagregar" onclick="desagregar();" title="Desagregar"><span class="fas fa-clone"></span></a>
                                  <a class="btn btn-success py-1 px-1 d-none my-1" id="btn_desagregar_confirmar" onclick="desagregar_confirmar('<?php echo $resultado_registros_historico[$i][0]; ?>', '<?php echo $resultado_registros_historico[$i][1]; ?>');"><span class="fas fa-check-circle"></span> Confirmar</a>
                                  <a class="btn btn-danger py-1 px-1 d-none" id="btn_desagregar_cancelar" onclick="desagregar_cancelar();"><span class="fas fa-times-circle"></span> Cancelar</a>
                                <?php endif; ?>
                                <a class="btn btn-success py-1 px-1 <?php echo ($control_borrador) ? 'd-none' : ''; ?>" id="btn_responder" onclick="responder('<?php echo $resultado_registros_historico[$i][0]; ?>');"><span class="fas fa-reply-all"></span></a>
                              <?php endif; ?>
                              <p></p>
                              <p class="font-size-12 my-0 py-0 px-1"><span class="fas fa-calendar-alt"></span> <?php echo $fecha_gestion; ?></p>
                              <p class="font-size-12 my-0 py-0 px-1"><span class="fas fa-clock"></span> <?php echo $hora_gestion; ?></p>
                          </div>
                          <div class="d-none" id="historico_correo_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>">
                            <div class="col-md-12">
                              <textarea class="form-control form-control-sm" name="grc_gestion_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>" id="grc_gestion_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>"><?php echo eliminardobleSalto(removeEmojis(nl2br(quitarCaracteresDeCorreoElectronico($resultado_registros_historico[$i][21])))); ?></textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php endif; ?>
              <?php endfor; ?>
              <div id="contenido_borrador"></div>
            </div>
            <div class="col-lg-4 flex-column d-none ps-0" id="visor_correo_2">
            </div>
          </div>
          </form>
        </div>
        <!-- content-wrapper ends -->
        <!-- MODAL DETALLE -->
        <div class="modal fade" id="modal-detalle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Histórico de gestión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body-detalle">
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger py-2 px-2" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- MODAL DETALLE -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/super-build/ckeditor.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/super-build/translations/es.js"></script>
  <script type="text/javascript">
    // Variable global para almacenar la instancia del editor
    let editorInstance = null;
    let editorCreated = false;

    // Función para crear el editor y habilitar o deshabilitar según el parámetro
    function createAndToggleEditor(disabled, plantilla, reset, info) {
      if (!editorCreated) {
        CKEDITOR.ClassicEditor.create(document.getElementById("grc_gestion_contenido"), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    // 'exportPDF','exportWord', '|',
                    // 'exportPDF', '|',
                    // 'findAndReplace', 'selectAll', '|',
                    // 'heading', '|',
                    // 'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', '|',
                    //   'bulletedList', 'numberedList', 'todoList', '|',
                    //   'outdent', 'indent', '|',
                    //   'undo', 'redo',
                    //   '-',
                    //   'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    //   'alignment', '|',
                    //   'link', 'insertImage', 'blockQuote', 'insertTable', 'sourceEditing', '|'
                    'undo', 'redo', '|',
                    'fontFamily', 'fontSize', 'bold', 'italic', 'underline', 'strikethrough', 'fontColor', 'highlight', '|',
                    'bulletedList', 'numberedList',
                    'outdent', 'indent', 'alignment', '|',
                    'subscript', 'superscript', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', '|',
                ],
                shouldNotGroupWhenFull: true,
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Escriba el contenido aquí!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: false,
                        classes: false,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    openInNewTab: {
                        mode: 'manual',
                        label: 'Abrir en una nueva pestaña',
                        attributes: {
                            target: '_blank',
                            rel: 'noopener noreferrer'
                        }
                    }
                }
            },
            language: {
                ui: 'es',
                content: 'es'
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            exportpdf: {
                margin_left: '10cm',
                margin_right: '10cm',
                margin_top: '10cm',
                margin_bottom: '10cm'
            },
            // extraPlugins: 'exportpdf',
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents',
                'PasteFromOfficeEnhanced'
            ],
        })
        .then(editor => {
            // Almacenar la instancia del editor en la variable global
            editorInstance = editor;
            editorCreated = true; // Marcar que el editor ha sido creado

            // Deshabilitar el editor al crearlo por primera vez
            if (disabled) {
              editor.enableReadOnlyMode('grc_gestion_contenido');
            } else {
              editor.disableReadOnlyMode('grc_gestion_contenido');
            }

            // Ocultar la barra de herramientas
            const toolbarElement = document.querySelector('.ck-toolbar');
            toolbarElement.style.display = 'none';
        })
        .catch( error => {
            console.error(error);
        });
      }

      if (editorInstance) {
        // Habilitar o deshabilitar el editor según el parámetro
        if (disabled) {
          editorInstance.enableReadOnlyMode('grc_gestion_contenido');
        } else {
          editorInstance.disableReadOnlyMode('grc_gestion_contenido');
        }

        // Mostrar u ocultar la barra de herramientas según corresponda
        const toolbarElement = document.querySelector('.ck-toolbar');
        toolbarElement.style.display = disabled ? 'none' : 'block';
        
        if (plantilla) {
          var textoAConcatenar = $('#plantilla_text').val();
          var infoAConcatenar = $('#info_correo').val();
          var firma_contenido = $('#firma_contenido').val();

          editorInstance.setData(textoAConcatenar + '<br><br>' + firma_contenido + '<br><br>'+ infoAConcatenar + editorInstance.getData());
        }

        if (reset) {
          var contenidoOrigen = $('#contenido_correo').val();
          var asuntoOrigen = $('#asunto_correo').val();
          $("#grc_gestion_asunto").val(asuntoOrigen);

          editorInstance.setData(contenidoOrigen);
        }

        if (info) {
          var infoAConcatenar = $('#info_correo').val();

          editorInstance.setData(infoAConcatenar + editorInstance.getData());
        }
      }
    }

    // Función para crear el editor y habilitar o deshabilitar según el parámetro
    function createAndToggleEditorView(id_contenido) {
      CKEDITOR.ClassicEditor.create(document.getElementById(id_contenido), {
          // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
          toolbar: {
              items: [
                  // 'exportPDF','exportWord', '|',
                  // 'exportPDF', '|',
                  // 'findAndReplace', 'selectAll', '|',
                  // 'heading', '|',
                  // 'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', '|',
                  //   'bulletedList', 'numberedList', 'todoList', '|',
                  //   'outdent', 'indent', '|',
                  //   'undo', 'redo',
                  //   '-',
                  //   'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                  //   'alignment', '|',
                  //   'link', 'insertImage', 'blockQuote', 'insertTable', 'sourceEditing', '|'
                  'undo', 'redo', '|',
                  'fontFamily', 'fontSize', 'bold', 'italic', 'underline', 'strikethrough', 'fontColor', 'highlight', '|',
                  'bulletedList', 'numberedList',
                  'outdent', 'indent', 'alignment', '|',
                  'subscript', 'superscript', '|',
                  'link', 'insertImage', 'blockQuote', 'insertTable', '|',
              ],
              shouldNotGroupWhenFull: true,
          },
          // Changing the language of the interface requires loading the language file using the <script> tag.
          list: {
              properties: {
                  styles: true,
                  startIndex: true,
                  reversed: true
              }
          },
          // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
          heading: {
              options: [
                  { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                  { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                  { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                  { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                  { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                  { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                  { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
              ]
          },
          // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
          placeholder: 'Escriba el contenido aquí!',
          // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
          fontFamily: {
              options: [
                  'default',
                  'Arial, Helvetica, sans-serif',
                  'Courier New, Courier, monospace',
                  'Georgia, serif',
                  'Lucida Sans Unicode, Lucida Grande, sans-serif',
                  'Tahoma, Geneva, sans-serif',
                  'Times New Roman, Times, serif',
                  'Trebuchet MS, Helvetica, sans-serif',
                  'Verdana, Geneva, sans-serif'
              ],
              supportAllValues: true
          },
          // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
          fontSize: {
              options: [ 10, 12, 14, 'default', 18, 20, 22 ],
              supportAllValues: true
          },
          // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
          // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
          htmlSupport: {
              allow: [
                  {
                      name: /.*/,
                      attributes: false,
                      classes: false,
                      styles: true
                  }
              ]
          },
          // Be careful with enabling previews
          // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
          htmlEmbed: {
              showPreviews: true
          },
          // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
          link: {
              decorators: {
                  addTargetToExternalLinks: true,
                  defaultProtocol: 'https://',
                  openInNewTab: {
                      mode: 'manual',
                      label: 'Abrir en una nueva pestaña',
                      attributes: {
                          target: '_blank',
                          rel: 'noopener noreferrer'
                      }
                  }
              }
          },
          language: {
              ui: 'es',
              content: 'es'
          },
          // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
          mention: {
              feeds: [
                  {
                      marker: '@',
                      feed: [
                          '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                          '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                          '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                          '@sugar', '@sweet', '@topping', '@wafer'
                      ],
                      minimumCharacters: 1
                  }
              ]
          },
          exportPdf: {
              tokenUrl: '',
              stylesheets: [
                  // './path/to/fonts.css',
                  // 'EDITOR_STYLES',
                  // './path/to/style.css'
              ],
              fileName: 'my-file.pdf',
              converterOptions: {
                  format: 'A4',
                  margin_top: '20mm',
                  margin_bottom: '20mm',
                  margin_right: '12mm',
                  margin_left: '12mm',
                  page_orientation: 'portrait'
              }
          },
          // extraPlugins: 'exportpdf',
          // The "super-build" contains more premium features that require additional configuration, disable them below.
          // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
          removePlugins: [
              // These two are commercial, but you can try them out without registering to a trial.
              // 'ExportPdf',
              // 'ExportWord',
              'CKBox',
              'CKFinder',
              'EasyImage',
              // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
              // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
              // Storing images as Base64 is usually a very bad idea.
              // Replace it on production website with other solutions:
              // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
              // 'Base64UploadAdapter',
              'RealTimeCollaborativeComments',
              'RealTimeCollaborativeTrackChanges',
              'RealTimeCollaborativeRevisionHistory',
              'PresenceList',
              'Comments',
              'TrackChanges',
              'TrackChangesData',
              'RevisionHistory',
              'Pagination',
              'WProofreader',
              // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
              // from a local file system (file://) - load this site via HTTP server if you enable MathType.
              'MathType',
              // The following features are part of the Productivity Pack and require additional license.
              'SlashCommand',
              'Template',
              'DocumentOutline',
              'FormatPainter',
              'TableOfContents',
              'PasteFromOfficeEnhanced'
          ],
      })
      .then(editorv => {
          // Deshabilitar el editorv al crearlo por primera vez
          editorv.enableReadOnlyMode(id_contenido);
          // editor.disableReadOnlyMode(id_contenido);
          // Ocultar la barra de herramientas
          // const toolbarElement = document.querySelector(id_contenido + ' .ck-toolbar');
          // toolbarElement.style.display = 'none';

      })
      .catch( error => {
          console.error(error);
      });
    }

    function adjuntos_lectura_historico(radicado, radicado_id, historial_id){
      var  formData = new FormData();
      formData.append("radicado", radicado);
      formData.append("radicado_id", radicado_id);
      formData.append("historial_id", historial_id);

      if (true) {
          $.ajax({
              type: 'POST',
              url: 'buzon_editar_procesar.php?accion=adjuntos&tipo=lista_ver',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  $('#adjuntos_conteo').html('');
                  $('#adjuntos_lista_'+historial_id).html('¡Cargando, por favor espere!');
              },
              complete:function(data){
              },
              success: function(data){
                  var resp = $.parseJSON(data);
                  // console.log(data);
                  if (resp.resultado_control>0) {
                      $('#adjuntos_lista_'+historial_id).html(resp.resultado);
                  } else {
                      $('#adjuntos_lista_'+historial_id).html('<span class="alert alert-warning p-1 font-size-12">¡No se encontraron documentos adjuntos!</span>');
                  }
              },
              error: function(data){
                  alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
              }
          });
      } else {
          alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
      }
    }

    <?php for ($i=0; $i < count($resultado_registros_historico); $i++): ?>
      adjuntos_lectura_historico('<?php echo $resultado_registros_historico[$i][1]; ?>', '<?php echo $resultado_registros_historico[$i][2]; ?>', '<?php echo $resultado_registros_historico[$i][0]; ?>');
      createAndToggleEditorView('grc_gestion_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>');
    <?php endfor; ?>
    
    // Llamar a la función para crear el editor y dejarlo deshabilitado al cargar la página
    // createAndToggleEditor(false, false, false);

    function adjuntos_lectura(radicado, radicado_id, historial_id){
      var  formData = new FormData();
      formData.append("radicado", radicado);
      formData.append("radicado_id", radicado_id);
      formData.append("historial_id", historial_id);

      if (true) {
          $.ajax({
              type: 'POST',
              url: 'buzon_editar_procesar.php?accion=adjuntos&tipo=lista_lectura',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  $('#adjuntos_conteo').html('');
                  $('#adjuntos_lista').html('¡Cargando, por favor espere!');
              },
              complete:function(data){
              },
              success: function(data){
                  var resp = $.parseJSON(data);
                  // console.log(data);
                  if (resp.resultado_control>0) {
                      $('#adjuntos_conteo').html(resp.resultado_control);
                      $('#adjuntos_lista').html(resp.resultado);
                      validar_gestion(radicado, radicado_id, historial_id);
                  } else {
                      $('#adjuntos_conteo').html('0');
                      $('#adjuntos_lista').html('<span class="alert alert-warning p-1 font-size-12">¡No se encontraron documentos adjuntos!</span>');
                  }
              },
              error: function(data){
                  alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
              }
          });
      } else {
          alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
      }
    }

    function adjuntos_cargar(files, radicado, radicado_id, historial_id){
      var  formData = new FormData();
      formData.append("radicado", radicado);
      formData.append("radicado_id", radicado_id);
      formData.append("historial_id", historial_id);
      // Agrega todos los archivos al FormData
      for (let i = 0; i < files.length; i++) {
          formData.append("adjuntos[]", files[i]);
      }

      // formData.append("adjunto", files[0]);
      if (true) {
          $.ajax({
              type: 'POST',
              url: 'buzon_editar_procesar.php?accion=adjuntos&tipo=cargar',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  // $('#adjuntos_conteo').html('');
                  $('#adjuntos_lista').html('¡Cargando, por favor espere!');
              },
              complete:function(data){
              },
              success: function(data){
                  var resp = $.parseJSON(data);
                  // console.log(data);
                  if (resp.resultado_control) {
                    adjuntos_lectura(radicado, radicado_id, historial_id);
                  } else {
                    adjuntos_lectura(radicado, radicado_id, historial_id);
                  }
              },
              error: function(data){
                  alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
              }
          });
      } else {
          alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
      }
    }

    function adjuntos_quitar(id_documento){
      var  formData = new FormData();
      formData.append("id_documento", id_documento);

      if (true) {
          $.ajax({
              type: 'POST',
              url: 'buzon_editar_procesar.php?accion=adjuntos&tipo=quitar',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  $('#adjuntos_conteo').html('');
                  $('#adjuntos_lista').html('¡Cargando, por favor espere!');
              },
              complete:function(data){
              },
              success: function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                    adjuntos_lectura(resp.resultado_radicado, resp.resultado_radicado_id, resp.resultado_historial_id);
                    // validar_gestion(resp.resultado_radicado, resp.resultado_radicado_id, resp.resultado_historial_id);
                  }
              },
              error: function(data){
                  alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
              }
          });
      } else {
          alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
      }
    }

    function adjuntos_agregar(id_documento){
      var  formData = new FormData();
      formData.append("id_registro", '<?php echo base64_encode($resultado_registros[0][1]); ?>');
      formData.append("id_documento", id_documento);

      if (true) {
          $.ajax({
              type: 'POST',
              url: 'buzon_editar_procesar.php?accion=adjuntos&tipo=agregar',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  $('#adjuntos_conteo').html('');
                  $('#adjuntos_lista').html('¡Cargando, por favor espere!');
              },
              complete:function(data){
              },
              success: function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      adjuntos();
                  }
              },
              error: function(data){
                  alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
              }
          });
      } else {
          alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
      }
    }
  </script>
  <script type="text/javascript">
      function responder(id_registro){
        var  formData = new FormData();
        formData.append("id_registro", id_registro);

        if (id_registro!='') {
            $.ajax({
                type: 'POST',
                url: 'buzon_editar_procesar.php?accion=historico&tipo=crear',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                  $("#contenido_borrador").html('<p class="alert alert-warning p-1 font-size-11">¡Creando borrador de respuesta, por favor espere!</p>');
                  document.getElementById('btn_responder').disabled=true;
                    // $('#adjuntos_conteo').html('');
                    // $('#adjuntos_lista').html('¡Cargando, por favor espere!');
                },
                complete:function(data){
                },
                success: function(data){
                    var resp = $.parseJSON(data);
                    if (resp.resultado_control) {
                      $("#contenido_borrador").html('<p class="alert alert-success p-1 font-size-11">¡Borrador de respuesta creado exitosamente!</p>');
                      $("#btn_responder").removeClass('d-block').addClass('d-none');
                      validar_borrador('<?php echo $resultado_registros[0][0]; ?>');

                      // $("#asunto_correo_original").removeClass('d-block').addClass('d-none');
                      
                      // $("#grc_tipologia_div").removeClass('d-none').addClass('d-block');
                      // $("#grc_gestion_div").removeClass('d-none').addClass('d-block');
                      

                      // alert(resp.resultado_control);
                        // adjuntos();
                    } else {

                    }
                },
                error: function(data){
                    alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
                    $("#contenido_borrador").html('<p class="alert alert-danger p-1 font-size-11">¡No podemos crear el borrador de respuesta en estos momentos, por favor intente más tarde!</p>');
                    document.getElementById('btn_responder').disabled=false;
                    $("#btn_responder").removeClass('d-none').addClass('d-inline-block');
                }
            });
        } else {
            alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
        }
      }

      function validar_borrador(id_registro){
        var  formData = new FormData();
        formData.append("id_registro", id_registro);
        formData.append("estado", '<?php echo $resultado_registros[0][7]; ?>');
        formData.append("url_salir", '<?php echo $url_salir; ?>');

        // alert('<?php echo $url_salir; ?>');
        if (id_registro!='') {
            // ASUNTO ORIGINAL SE DEBE OCULTAR Y MOSTRAR DIV DE EDITAR ASUNTO SEGÚN GESTIÓN
            // AGREGAR BOTÓN DESCARTAR PARA ELIMINAR BORRADOR Y ADJUNTOS, SIEMPRE Y CUANDO ESTÉ EN TIPO BORRADOR Y ESTADO ENVÍO VACÍO
            // AGREGAR BOTÓN DE ENVIAR, EL CUAL PONE ESTADO ENVÍO EN PENDIENTE Y SE BLOQUEA LA ELIMINACIÓN
            $.ajax({
                type: 'POST',
                url: 'buzon_editar_procesar.php?accion=historico&tipo=borrador',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                  $("#contenido_borrador").html('<p class="alert alert-warning p-1 font-size-11">¡Cargando, por favor espere!</p>');
                },
                complete:function(data){
                },
                success: function(data){
                    var resp = $.parseJSON(data);
                    if (resp.resultado_control) {
                      $("#contenido_borrador").html(resp.resultado);
                      // $("#asunto_correo_original").removeClass('d-block').addClass('d-none');
                      
                      $("#grc_tipologia_div").removeClass('d-none').addClass('d-block');
                      $("#grc_gestion_div").removeClass('d-none').addClass('d-block');
                      createAndToggleEditor(true, false, false, false);
                      validar_tipologia();
                      adjuntos_lectura(resp.resultado_radicado, resp.resultado_radicado_id, resp.resultado_historial_id);
                    } else {
                      $("#contenido_borrador").html('');
                    }
                },
                error: function(data){
                    alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
                    $("#mensajes_gestion").html('<p class="alert alert-danger p-1 font-size-11">¡No podemos crear el borrador de respuesta en estos momentos, por favor intente más tarde!</p>');
                    document.getElementById('btn_responder').disabled=false;
                    $("#btn_responder").removeClass('d-none').addClass('d-inline-block');
                }
            });
        } else {
            alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
        }
      }

      function enviar_borrador(id_registro){
        var grch_tipologia = document.getElementById("grc_tipologia").value;
        var grch_gestion = document.getElementById("grc_gestion").value;
        var grc_gestion_detalle_motivo = document.getElementById('grc_gestion_detalle_motivo').value;
        var grc_gestion_detalle_plantilla = document.getElementById('grc_gestion_detalle_plantilla').value;
        var grch_correo_asunto = document.getElementById('grc_gestion_asunto').value;
        var grch_correo_contenido = editorInstance.getData();

        var grc_gestion_para = document.getElementById('grc_gestion_para').value;
        var grc_gestion_cc = document.getElementById('grc_gestion_cc').value;
        var grc_gestion_cco = document.getElementById('grc_gestion_cco').value;

        var valida_destinatario=true;

        if (grch_gestion=='Respuesta Radicado DELTA') {
          if (grc_gestion_para=='' || grc_gestion_para==null) {
            // valida_destinatario=false;
          }
        }
        
        var  formData = new FormData();
        formData.append("id_registro", id_registro);
        formData.append("grch_tipologia", grch_tipologia);
        formData.append("grch_gestion", grch_gestion);
        formData.append("grc_gestion_detalle_motivo", grc_gestion_detalle_motivo);
        formData.append("grc_gestion_detalle_plantilla", grc_gestion_detalle_plantilla);
        formData.append("grch_correo_asunto", grch_correo_asunto);
        formData.append("grch_correo_contenido", grch_correo_contenido);
        formData.append("grc_gestion_para", grc_gestion_para);
        formData.append("grc_gestion_cc", grc_gestion_cc);
        formData.append("grc_gestion_cco", grc_gestion_cco);
        
        if (id_registro!='' && grch_tipologia!='' && grch_gestion!='' && valida_destinatario) {
            $.ajax({
                type: 'POST',
                url: 'buzon_editar_procesar.php?accion=historico&tipo=enviar',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                  $("#btn_eliminar").removeClass('d-block').addClass('d-none');
                  $("#mensajes_gestion").html('<p class="alert alert-warning p-1 font-size-11">¡Enviando, por favor espere!</p>');
                },
                complete:function(data){
                },
                success: function(data){
                    var resp = $.parseJSON(data);
                    console.log(resp.resultado);
                    if (resp.resultado_control) {
                      $("#mensajes_gestion").html('<p class="alert alert-success p-1 font-size-11">¡Gestión enviada exitosamente!</p>');
                      $("#btn_enviar").removeClass('d-block').addClass('d-none');
                      $("#asunto_correo_borrador").html(grch_correo_asunto);

                      var grc_gestion_asunto = document.getElementById('grc_gestion_asunto').disabled=true;
                      var adjunto = document.getElementById('adjunto').disabled=true;
                      $("#grc_gestion_asunto_div").removeClass('d-block').addClass('d-none');


                      $("#estado_borrador").html('<span class="alert alert-success px-1 py-0 my-0 font-size-11"><span class="fas fa-user-check me-1"></span>Gestión </span>');
                      createAndToggleEditor(true, false, false, false);
                      editar_adjunto(false);
                      estado_radicado(resp.resultado_estado);
                      $("#historico_correo_contenido_"+id_registro).removeClass('d-block').addClass('d-none');
                      // adjuntos_lectura(resp.resultado_radicado, resp.resultado_radicado_id, resp.resultado_historial_id);
                    } else {
                      createAndToggleEditor(false, false, false, false);
                      $("#btn_eliminar").removeClass('d-none').addClass('d-block');
                      $("#mensajes_gestion").html('<p class="alert alert-warning p-1 font-size-11">¡No se ha podido enviar la gestión, por favor intente nuevamente!</p>');

                    }
                },
                error: function(data){
                    alert("2Problemas al tratar de enviar la gestión, por favor verifica e intenta nuevamente");
                    $("#mensajes_gestion").html('<p class="alert alert-warning p-1 font-size-11">¡No se ha podido enviar la gestión, por favor intente nuevamente!</p>');
                    document.getElementById('btn_responder').disabled=false;
                    // $("#btn_responder").removeClass('d-none').addClass('d-inline-block');
                }
            });
        } else {
            alert("1Problemas al tratar de enviar la gestión, por favor verifica e intenta nuevamente");
        }
      }

      function eliminar_borrador(id_registro){
        var  formData = new FormData();
        formData.append("id_registro", id_registro);
        
        if (id_registro!='') {
            $.ajax({
                type: 'POST',
                url: 'buzon_editar_procesar.php?accion=historico&tipo=eliminar',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                  $("#contenido_borrador").html('<p class="alert alert-warning p-1 font-size-11">¡Eliminando borrador de respuesta, por favor espere!</p>');
                  document.getElementById('btn_responder').disabled=true;
                },
                complete:function(data){
                },
                success: function(data){
                    var resp = $.parseJSON(data);
                    if (resp.resultado_control) {
                      $("#contenido_borrador").html('<p class="alert alert-success p-1 font-size-11">¡Borrador de respuesta eliminado exitosamente!</p>');
                      $("#btn_responder").removeClass('d-none').addClass('d-inline-block');
                      document.getElementById('btn_responder').disabled=false;
                      editorInstance = null;
                      editorCreated = false;
                    } else {
                      $("#contenido_borrador").html('<p class="alert alert-danger p-1 font-size-11">¡No se ha podido eliminar el borrador de respuesta, por favor intente nuevamente!</p>');

                    }
                },
                error: function(data){
                    alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
                    $("#contenido_borrador").html('<p class="alert alert-danger p-1 font-size-11">¡No se ha podido eliminar el borrador de respuesta, por favor intente nuevamente!</p>');
                }
            });
        } else {
            alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
        }
      }

      function desagregar(){
        $("#btn_unificar").removeClass('d-inline-block').addClass('d-none');
        $("#btn_desagregar").removeClass('d-inline-block').addClass('d-none');
        $("#btn_responder").removeClass('d-inline-block').addClass('d-none');
        $("#btn_desagregar_confirmar").removeClass('d-none').addClass('d-inline-block');
        $("#btn_desagregar_cancelar").removeClass('d-none').addClass('d-inline-block');
        $("#grc_dividido_cantidad").removeClass('d-none').addClass('d-inline-block');
        document.getElementById('grc_dividido_cantidad').disabled=false;
      }

      function desagregar_cancelar(){
        $("#btn_unificar").removeClass('d-none').addClass('d-inline-block');
        $("#btn_desagregar").removeClass('d-none').addClass('d-inline-block');
        $("#btn_responder").removeClass('d-none').addClass('d-inline-block');
        $("#btn_desagregar_confirmar").removeClass('d-inline-block').addClass('d-none');
        $("#btn_desagregar_cancelar").removeClass('d-inline-block').addClass('d-none');
        $("#grc_dividido_cantidad").removeClass('d-inline-block').addClass('d-none');
        document.getElementById('grc_dividido_cantidad').disabled=true;
      }

      function desagregar_confirmar(id_registro, id_consecutivo){
        var grc_dividido_cantidad = document.getElementById("grc_dividido_cantidad").value;
        var  formData = new FormData();
        formData.append("id_registro", id_registro);
        formData.append("grc_dividido_cantidad", grc_dividido_cantidad);

        if (id_registro!='' && grc_dividido_cantidad>0) {
            $.ajax({
                type: 'POST',
                url: 'buzon_editar_procesar.php?accion=historico&tipo=desagregar',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                  $("#contenido_borrador").html('<p class="alert alert-warning p-1 font-size-11">¡Desagregando casos, por favor espere!</p>');
                  document.getElementById('grc_dividido_cantidad').disabled=true;
                },
                complete:function(data){
                },
                success: function(data){
                    var resp = $.parseJSON(data);
                    if (resp.resultado_control) {
                      $("#contenido_borrador").html('<p class="alert alert-success p-1 font-size-11">¡Casos desagregados exitosamente!</p>');
                      desagregar_lista(id_consecutivo, id_registro);
                      desagregar_cancelar();
                    } else {

                    }
                },
                error: function(data){
                    alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
                    // $("#contenido_borrador").html('<p class="alert alert-danger p-1 font-size-11">¡No podemos crear el borrador de respuesta en estos momentos, por favor intente más tarde!</p>');
                    // document.getElementById('btn_responder').disabled=false;
                    // $("#btn_responder").removeClass('d-none').addClass('d-inline-block');
                }
            });
        } else {
            alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
        }
      }

      function desagregar_lista(id_registro, id_registro_actual){
        var  formData = new FormData();
        formData.append("id_registro", id_registro);
        formData.append("id_registro_actual", id_registro_actual);
        formData.append("url_editar", '<?php echo $url_editar; ?>');

        console.log(id_registro);
        console.log(id_registro_actual);

        if (id_registro!='' && id_registro_actual!='') {
            $.ajax({
                type: 'POST',
                url: 'buzon_editar_procesar.php?accion=historico&tipo=desagregar_lista',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                  $("#contenido_desagregar_lista").html('<p class="alert alert-warning p-1 font-size-11">¡Cargando, por favor espere!</p>');
                },
                complete:function(data){
                },
                success: function(data){
                    var resp = $.parseJSON(data);
                    if (resp.resultado_control) {
                      $("#contenido_desagregar_lista").html(resp.resultado);
                    } else {
                      $("#contenido_desagregar_lista").html('');
                    }
                },
                error: function(data){
                    alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
                    $("#mensajes_gestion").html('<p class="alert alert-danger p-1 font-size-11">¡No podemos crear el borrador de respuesta en estos momentos, por favor intente más tarde!</p>');
                    document.getElementById('btn_responder').disabled=false;
                    $("#btn_responder").removeClass('d-none').addClass('d-inline-block');
                }
            });
        } else {
            alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
        }
      }

      function editar_adjunto(accion) {
        // Obtén todos los elementos con la clase "adjuntos_borrador"
        var adjuntos_borrador = document.querySelectorAll(".adjuntos_borrador");

        // Itera a través de los elementos y agrega la clase "d-none"
        adjuntos_borrador.forEach(function(elemento) {
          if (accion) {
            elemento.classList.remove("d-none");
            elemento.classList.add("d-block");  
          } else {
            elemento.classList.remove("d-block");
            elemento.classList.add("d-none");  
          }
          
        });
      }

      function estado_radicado(estado) {
        // Obtén todos los elementos con la clase "info_original"
        var info_original = document.querySelectorAll(".info_original");

        // Itera a través de los elementos y agrega la clase "d-none"
        info_original.forEach(function(elemento) {
          if (estado=='Pendiente') {
            elemento.classList.remove("alert-warning");
            elemento.classList.remove("alert-success");
            elemento.classList.remove("alert-dark");
            elemento.classList.add("alert-warning");  
          } else if (estado=='En trámite') {
            elemento.classList.remove("alert-warning");
            elemento.classList.remove("alert-success");
            elemento.classList.remove("alert-dark");
            elemento.classList.add("alert-dark");  
          } else if (estado=='Finalizado') {
            elemento.classList.remove("alert-warning");
            elemento.classList.remove("alert-success");
            elemento.classList.remove("alert-dark");
            elemento.classList.add("alert-success");  
          }
          
        });
      }

      function validar_gestion(radicado, radicado_id, historial_id){
          var gestion_opcion = document.getElementById("grc_gestion");
          var grc_gestion = gestion_opcion.options[gestion_opcion.selectedIndex].value;

          var tipologia = document.getElementById("grc_tipologia");
          var grc_tipologia = tipologia.options[tipologia.selectedIndex].value;
          
          var grc_gestion_detalle_motivo = document.getElementById('grc_gestion_detalle_motivo').disabled=true;
          var grc_gestion_detalle_plantilla = document.getElementById('grc_gestion_detalle_plantilla').disabled=true;
          var grc_gestion_asunto = document.getElementById('grc_gestion_asunto').disabled=true;
          var adjunto = document.getElementById('adjunto').disabled=true;
          $("#grc_gestion_asunto_div").removeClass('d-block').addClass('d-none');
          $("#grc_gestion_detalle_plantilla").html('');
          $("#grc_gestion_detalle_motivo_div").removeClass('d-block').addClass('d-none');
          $("#grc_gestion_detalle_plantilla_div").removeClass('d-block').addClass('d-none');

          var grc_gestion_para = document.getElementById('grc_gestion_para').disabled=true;
          $("#grc_gestion_para_div").removeClass('d-block').addClass('d-none');

          var grc_gestion_cc = document.getElementById('grc_gestion_cc').disabled=true;
          $("#grc_gestion_cc_div").removeClass('d-block').addClass('d-none');

          var grc_gestion_cco = document.getElementById('grc_gestion_cco').disabled=true;
          $("#grc_gestion_cco_div").removeClass('d-block').addClass('d-none');



          // createAndToggleEditor(true, false, true); // Habilitar el editor
          createAndToggleEditor(true, false, true, false);
          editar_adjunto(false);

          $('#plantilla_text').val('');
          $("#mensajes_gestion").html('');
          if(grc_gestion=="Archivar") {
              var grc_gestion_detalle_motivo = document.getElementById('grc_gestion_detalle_motivo').disabled=false;
              $("#grc_gestion_detalle_motivo_div").removeClass('d-none').addClass('d-block');
              $("#mensajes_gestion").html('<p class="alert alert-warning p-1 font-size-11">¡El caso será archivado, por favor valide antes de continuar!</p>');
          } else if(grc_gestion=="Respuesta") {
              $.post("buzon_editar_procesar.php?accion=plantilla&tipo="+grc_gestion, { }, function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      $("#grc_gestion_detalle_plantilla").html(resp.resultado);
                  }
              });

              var grc_gestion_detalle_plantilla = document.getElementById('grc_gestion_detalle_plantilla').disabled=false;
              $("#grc_gestion_detalle_plantilla_div").removeClass('d-none').addClass('d-block');
              $("#mensajes_gestion").html('<p class="alert alert-warning p-1 font-size-11">¡Se programará notificación de respuesta, por favor valide antes de continuar!</p>');

              if (grc_tipologia=='Envío Radicado a Ciudadano' || grc_tipologia=='Soy Transparente' || grc_tipologia=='Funcionarios') {
                var grc_gestion_para = document.getElementById('grc_gestion_para').disabled=false;
                $("#grc_gestion_para_div").removeClass('d-none').addClass('d-block');
              }

              if (grc_tipologia=='Funcionarios') {
                var grc_gestion_cc = document.getElementById('grc_gestion_cc').disabled=false;
                $("#grc_gestion_cc_div").removeClass('d-none').addClass('d-block');
              }

              var grc_gestion_cco = document.getElementById('grc_gestion_cco').disabled=false;
              $("#grc_gestion_cco_div").removeClass('d-none').addClass('d-block');


          } else if(grc_gestion=="Respuesta Radicado DELTA") {
              $.post("buzon_editar_procesar.php?accion=plantilla&tipo="+grc_gestion, { }, function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      $("#grc_gestion_detalle_plantilla").html(resp.resultado);
                  }
              });

              var grc_gestion_detalle_plantilla = document.getElementById('grc_gestion_detalle_plantilla').disabled=false;
              $("#grc_gestion_detalle_plantilla_div").removeClass('d-none').addClass('d-block');
              $("#mensajes_gestion").html('<p class="alert alert-warning p-1 font-size-11">¡Se programará notificación de respuesta, por favor valide antes de continuar!</p>');

              var grc_gestion_para = document.getElementById('grc_gestion_para').disabled=false;
              $("#grc_gestion_para_div").removeClass('d-none').addClass('d-block');

          } else if(grc_gestion=="Correspondencia") {
              $.post("buzon_editar_procesar.php?accion=plantilla&tipo="+grc_gestion, { }, function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      $("#grc_gestion_detalle_plantilla").html(resp.resultado);
                  }
              });
              var grc_gestion_detalle_plantilla = document.getElementById('grc_gestion_detalle_plantilla').disabled=false;
              $("#grc_gestion_detalle_plantilla_div").removeClass('d-none').addClass('d-block');
              $("#mensajes_gestion").html('<p class="alert alert-warning p-1 font-size-11">¡El caso será reenviado al área de <b>Correspondencia</b>, por favor valide antes de continuar!</p>');
          } else if(grc_gestion=="Notificaciones Jurídicas") {
              $.post("buzon_editar_procesar.php?accion=plantilla&tipo="+grc_gestion, { }, function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      $("#grc_gestion_detalle_plantilla").html(resp.resultado);
                  }
              });
              var grc_gestion_detalle_plantilla = document.getElementById('grc_gestion_detalle_plantilla').disabled=false;
              $("#grc_gestion_detalle_plantilla_div").removeClass('d-none').addClass('d-block');
              $("#mensajes_gestion").html('<p class="alert alert-warning p-1 font-size-11">¡El caso será reenviado al área de <b>Notificaciones Jurídicas</b>, por favor valide antes de continuar!</p>');
          } else if(grc_gestion=="Radicación DELTA" || grc_gestion=="Radicación DELTA Soy Transparente") {
              document.getElementById('adjunto').disabled=false;
              $("#grc_gestion_asunto_div").removeClass('d-none').addClass('d-block');
              document.getElementById('grc_gestion_asunto').disabled=false;
              var correo_remitente = $("#correo_remitente").html();
              var grc_gestion_asunto = $("#grc_gestion_asunto").val();
                
              editar_adjunto(true);

              $("#grc_gestion_asunto").val(correo_remitente + ' ' + grc_gestion_asunto);
              $("#mensajes_gestion").html('<p class="alert alert-warning p-1 font-size-11">¡El caso será radicado en <b>DELTA</b>, por favor valide antes de continuar!</p>');
              createAndToggleEditor(false, false, false, true); // Habilitar el editor
          }

          validar_destinos(historial_id);
      }

      function validar_destinos(id_registro) {
        var grch_tipologia = document.getElementById("grc_tipologia").value;
        var grch_gestion = document.getElementById("grc_gestion").value;
        var grc_gestion_para = document.getElementById('grc_gestion_para').value;
        var grc_gestion_cc = document.getElementById('grc_gestion_cc').value;
        var grc_gestion_cco = document.getElementById('grc_gestion_cco').value;

        var  formData = new FormData();
        formData.append("id_registro", id_registro);
        formData.append("grch_tipologia", grch_tipologia);
        formData.append("grch_gestion", grch_gestion);
        formData.append("grc_gestion_para", grc_gestion_para);
        formData.append("grc_gestion_cc", grc_gestion_cc);
        formData.append("grc_gestion_cco", grc_gestion_cco);
        if (id_registro!='' && grch_tipologia!='' && grch_gestion!='') {
            $.ajax({
                type: 'POST',
                url: 'buzon_editar_procesar.php?accion=historico&tipo=enviar_destinos',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                  $("#destinos_correo_borrador").html('Cargando...');
                },
                complete:function(data){
                },
                success: function(data){
                    var resp = $.parseJSON(data);
                    console.log(resp.resultado);
                    if (resp.resultado_control) {
                      $("#destinos_correo_borrador").html(resp.resultado);
                      
                    }
                },
                error: function(data){
                    alert("Problemas al tratar de enviar la gestión, por favor verifica e intenta nuevamente");
                }
            });
        }
      }

      function validar_tipologia(){
          var tipologia = document.getElementById("grc_tipologia");
          var grc_tipologia = tipologia.options[tipologia.selectedIndex].value;
          var estado_gestion = '<?php echo $resultado_registros[0][7]; ?>';
          // Obtener referencia al elemento select
          const selectElement = document.getElementById("grc_gestion");
          
          // Limpiar todas las opciones existentes
          selectElement.innerHTML = "";

          // Crear y agregar nuevas opciones en función del valor
          if (estado_gestion === "En trámite") {
              selectElement.appendChild(new Option("Seleccione", ""));
              selectElement.appendChild(new Option("Respuesta Radicado DELTA", "Respuesta Radicado DELTA"));
          } else if (grc_tipologia === "Ciudadanos") {
              selectElement.appendChild(new Option("Seleccione", ""));
              selectElement.appendChild(new Option("Radicación DELTA", "Radicación DELTA"));
              selectElement.appendChild(new Option("Archivar", "Archivar"));
              selectElement.appendChild(new Option("Respuesta", "Respuesta"));
              selectElement.appendChild(new Option("Correspondencia", "Correspondencia"));
              
          } else if (grc_tipologia === "Envío Radicado a Ciudadano") {
              selectElement.appendChild(new Option("Seleccione", ""));
              selectElement.appendChild(new Option("Archivar", "Archivar"));
              selectElement.appendChild(new Option("Respuesta", "Respuesta"));
              
          } else if (grc_tipologia === "Funcionarios") {
              selectElement.appendChild(new Option("Seleccione", ""));
              selectElement.appendChild(new Option("Radicación DELTA", "Radicación DELTA"));
              selectElement.appendChild(new Option("Archivar", "Archivar"));
              selectElement.appendChild(new Option("Respuesta", "Respuesta"));
              selectElement.appendChild(new Option("Correspondencia", "Correspondencia"));
              
          } else if (grc_tipologia === "Notificaciones de correo") {
              selectElement.appendChild(new Option("Seleccione", ""));
              selectElement.appendChild(new Option("Archivar", "Archivar"));
              
          } else if (grc_tipologia === "Prioritario") {
              selectElement.appendChild(new Option("Seleccione", ""));
              selectElement.appendChild(new Option("Radicación DELTA", "Radicación DELTA"));
              selectElement.appendChild(new Option("Archivar", "Archivar"));
              selectElement.appendChild(new Option("Respuesta", "Respuesta"));
              selectElement.appendChild(new Option("Correspondencia", "Correspondencia"));
              
          } else if (grc_tipologia === "Soy Transparente") {
              selectElement.appendChild(new Option("Seleccione", ""));
              selectElement.appendChild(new Option("Radicación DELTA", "Radicación DELTA"));
              selectElement.appendChild(new Option("Radicación DELTA Soy Transparente", "Radicación DELTA Soy Transparente"));
              selectElement.appendChild(new Option("Archivar", "Archivar"));
              selectElement.appendChild(new Option("Respuesta", "Respuesta"));
              
          } else if (grc_tipologia === "Tutelas") {
              selectElement.appendChild(new Option("Seleccione", ""));
              selectElement.appendChild(new Option("Notificaciones Jurídicas", "Notificaciones Jurídicas"));
          }

          // validar_gestion();
      }

      function adjuntos_previa(id_registro) {
          var myModal = new bootstrap.Modal(document.getElementById("modal-detalle"), {});
          $('.modal-body-detalle').load('buzon_editar_adjunto_ver.php?id_registro='+id_registro,function(){
              $("#staticBackdropLabel").html('Vista previa');
              myModal.show();
          });
      }

      function validar_plantilla(id_plantilla){
        var  formData = new FormData();
        formData.append("id_plantilla", id_plantilla);

        if (true) {
            $.ajax({
                type: 'POST',
                url: 'buzon_editar_procesar.php?accion=plantilla&tipo=obtener',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    $('#plantilla_text').val('');
                    
                },
                complete:function(data){
                },
                success: function(data){
                    var resp = $.parseJSON(data);

                    if (resp.resultado_control) {
                        $('#plantilla_text').val(resp.resultado);
                        var grc_gestion_asunto = document.getElementById('grc_gestion_asunto').disabled=false;
                        // var grc_correo_remitente = $("#grc_correo_remitente").html();
                        // var grc_gestion_asunto = $("#grc_gestion_asunto").val();
                        // $("#grc_gestion_asunto").val(grc_gestion_asunto);
                        createAndToggleEditor(false, true, false, false);
                    }
                },
                error: function(data){
                    alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
                }
            });
        } else {
            alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
        }
      }

      function mostrarOcultar(idelemento){
        if ($("#historico_correo_contenido_"+idelemento).hasClass("d-none")) {
          $("#historico_correo_contenido_"+idelemento).removeClass('d-none').addClass('d-block');
        } else {
          $("#historico_correo_contenido_"+idelemento).removeClass('d-block').addClass('d-none');
        }
      }

      function open_modal_detalle(id_registro) {
        var myModal = new bootstrap.Modal(document.getElementById("modal-detalle"), {});
        $('.modal-body-detalle').load('buzon_historico_ver.php?reg='+id_registro,function(){
            $("#staticBackdropLabel").html('Histórico de gestión');
            myModal.show();
        });
      }

      function validar_duplicado(id_registro) {
        $("#visor_correo_1").removeClass('col-lg-9').addClass('col-lg-5');
        $("#visor_correo_2").removeClass('d-none').addClass('d-inline-block');
        $('#visor_correo_2').load('buzon_editar_duplicado_ver.php?reg='+id_registro,function(){
            
        });
      }

      function cerrar_duplicado() {
        $("#visor_correo_1").removeClass('col-lg-5').addClass('col-lg-9');
        $("#visor_correo_2").removeClass('d-inline-block').addClass('d-none');
        $('#visor_correo_2').html('');
      }

      validar_borrador('<?php echo $resultado_registros[0][0]; ?>');
      <?php if($resultado_registros[0][8]=='Si'): ?>
        desagregar_lista('<?php echo $resultado_registros[0][11]; ?>', '<?php echo $resultado_registros[0][0]; ?>');
      <?php else: ?>
        desagregar_lista('<?php echo $resultado_registros[0][1]; ?>', '<?php echo $resultado_registros[0][0]; ?>');
      <?php endif; ?>
      
  </script>
</body>
</html>