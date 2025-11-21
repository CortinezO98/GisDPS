<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-TMNC";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Transacciones Monetarias No Condicionadas | 3. Clasificación | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="tmnc_sclasificacion?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='tmnc_sclasificacion' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }
  
  if(isset($_POST["guardar_registro"])){
      $cetc_correo_electronico=validar_input($_POST['cetc_correo_electronico']);
      $cetc_fecha_ingreso_correo=validar_input($_POST['cetc_fecha_ingreso_correo']);
      $cetc_nombre_ciudadano=validar_input($_POST['cetc_nombre_ciudadano']);
      $cetc_cedula_consulta=validar_input($_POST['cetc_cedula_consulta']);
      $cetc_asunto_correo=validar_input($_POST['cetc_asunto_correo']);
      $cetc_programa_solicitud=validar_input($_POST['cetc_programa_solicitud']);
      $cetc_plantilla_utilizada=validar_input($_POST['cetc_plantilla_utilizada']);
      $cetc_solicitud_ciudadano=validar_input($_POST['cetc_solicitud_ciudadano']);
      $cetc_plantilla_datos_incompletos=validar_input($_POST['cetc_plantilla_datos_incompletos']);
      $cetc_plantilla_datos_completos=validar_input($_POST['cetc_plantilla_datos_completos']);
      $cetc_parrafo_radicacion=validar_input($_POST['cetc_parrafo_radicacion']);
      $cetc_parrafo_plantilla_1='';
      $cetc_parrafo_plantilla_4='';
      $cetc_parrafo_plantilla_5='';
      $cetc_parrafo_plantilla_6=validar_input($_POST['cetc_parrafo_plantilla_6']);
      $cetc_situacion_plantilla_8=validar_input($_POST['cetc_situacion_plantilla_8']);
      $cetc_parrafo_plantilla_8=validar_input($_POST['cetc_parrafo_plantilla_8']);
      $cetc_parrafo_plantilla_10=validar_input($_POST['cetc_parrafo_plantilla_10']);
      $cetc_titular_hogar=validar_input($_POST['cetc_titular_hogar']);
      $cetc_parrafo_plantilla_14=validar_input($_POST['cetc_parrafo_plantilla_14']);
      $cetc_parrafo_plantilla_16=validar_input($_POST['cetc_parrafo_plantilla_16']);
      $cetc_situacion_plantilla_17=validar_input($_POST['cetc_situacion_plantilla_17']);
      $cetc_parrafo_plantilla_17=validar_input($_POST['cetc_parrafo_plantilla_17']);
      $cetc_situacion_plantilla_18=validar_input($_POST['cetc_situacion_plantilla_18']);
      $cetc_parrafo_plantilla_18=validar_input($_POST['cetc_parrafo_plantilla_18']);
      $cetc_parrafo_plantilla_20=validar_input($_POST['cetc_parrafo_plantilla_20']);
      $cetc_nombre_solicitante=validar_input($_POST['cetc_nombre_solicitante']);
      $cetc_nombre_titular=validar_input($_POST['cetc_nombre_titular']);
      $cetc_parrafo_plantilla_21=validar_input($_POST['cetc_parrafo_plantilla_21']);
      $cetc_situacion_plantilla_22='';
      $cetc_parrafo_plantilla_22='';
      $cetc_parrafo_plantilla_23=validar_input($_POST['cetc_parrafo_plantilla_23']);
      $cetc_parrafo_plantilla_25=validar_input($_POST['cetc_parrafo_plantilla_25']);
      $cetc_parrafo_plantilla_26=validar_input($_POST['cetc_parrafo_plantilla_26']);
      $cetc_parrafo_plantilla_reemplazo=validar_input($_POST['cetc_parrafo_plantilla_reemplazo']);
      $cetc_motivo_devolucion=validar_input($_POST['cetc_motivo_devolucion']);
      $cetc_observaciones=validar_input($_POST['cetc_observaciones']);
      $cetc_notificar=validar_input($_POST['notificar']);
      $cetc_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cetmnc_clasificacion`(`cetc_correo_electronico`, `cetc_fecha_ingreso_correo`, `cetc_nombre_ciudadano`, `cetc_cedula_consulta`, `cetc_asunto_correo`, `cetc_programa_solicitud`, `cetc_plantilla_utilizada`, `cetc_solicitud_ciudadano`, `cetc_plantilla_datos_incompletos`, `cetc_plantilla_datos_completos`, `cetc_parrafo_radicacion`, `cetc_parrafo_plantilla_1`, `cetc_parrafo_plantilla_4`, `cetc_parrafo_plantilla_5`, `cetc_parrafo_plantilla_6`, `cetc_situacion_plantilla_8`, `cetc_parrafo_plantilla_8`, `cetc_parrafo_plantilla_10`, `cetc_titular_hogar`, `cetc_parrafo_plantilla_14`, `cetc_parrafo_plantilla_16`, `cetc_situacion_plantilla_17`, `cetc_parrafo_plantilla_17`, `cetc_situacion_plantilla_18`, `cetc_parrafo_plantilla_18`, `cetc_parrafo_plantilla_20`, `cetc_nombre_solicitante`, `cetc_nombre_titular`, `cetc_parrafo_plantilla_21`, `cetc_situacion_plantilla_22`, `cetc_parrafo_plantilla_22`, `cetc_parrafo_plantilla_23`, `cetc_parrafo_plantilla_25`, `cetc_parrafo_plantilla_26`, `cetc_parrafo_plantilla_reemplazo`, `cetc_motivo_devolucion`, `cetc_observaciones`, `cetc_notificar`, `cetc_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssssssssssssssssssssssssssssssssssss', $cetc_correo_electronico, $cetc_fecha_ingreso_correo, $cetc_nombre_ciudadano, $cetc_cedula_consulta, $cetc_asunto_correo, $cetc_programa_solicitud, $cetc_plantilla_utilizada, $cetc_solicitud_ciudadano, $cetc_plantilla_datos_incompletos, $cetc_plantilla_datos_completos, $cetc_parrafo_radicacion, $cetc_parrafo_plantilla_1, $cetc_parrafo_plantilla_4, $cetc_parrafo_plantilla_5, $cetc_parrafo_plantilla_6, $cetc_situacion_plantilla_8, $cetc_parrafo_plantilla_8, $cetc_parrafo_plantilla_10, $cetc_titular_hogar, $cetc_parrafo_plantilla_14, $cetc_parrafo_plantilla_16, $cetc_situacion_plantilla_17, $cetc_parrafo_plantilla_17, $cetc_situacion_plantilla_18, $cetc_parrafo_plantilla_18, $cetc_parrafo_plantilla_20, $cetc_nombre_solicitante, $cetc_nombre_titular, $cetc_parrafo_plantilla_21, $cetc_situacion_plantilla_22, $cetc_parrafo_plantilla_22, $cetc_parrafo_plantilla_23, $cetc_parrafo_plantilla_25, $cetc_parrafo_plantilla_26, $cetc_parrafo_plantilla_reemplazo, $cetc_motivo_devolucion, $cetc_observaciones, $cetc_notificar, $cetc_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cetc_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='3. Clasificación - TMNC | Canal Escrito';
                $referencia='3. Clasificación - TMNC | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Correo electrónico</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_correo_electronico."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha ingreso correo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_fecha_ingreso_correo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nombre del Ciudadano (EN MAYÚSCULAS)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_nombre_ciudadano."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Cédula a consultar</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_cedula_consulta."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Asunto del correo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_asunto_correo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Programa al que eleva la solicitud</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['programa_solicitud']['texto'][$cetc_programa_solicitud]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Plantilla utilizada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['plantilla_utilizada']['texto'][$cetc_plantilla_utilizada]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Solicitud del ciudadano (Transcribir petición, queja o solicitud concreta)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_solicitud_ciudadano."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Plantilla datos incompletos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['plantilla_datos_incompletos']['texto'][$cetc_plantilla_datos_incompletos]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Plantilla datos completos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['plantilla_datos_completos']['texto'][$cetc_plantilla_datos_completos]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo en proceso de radicación o respuesta</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_radicacion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 6</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_6."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Situación plantilla 8</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['situacion_plantilla_8']['texto'][$cetc_situacion_plantilla_8]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 8</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_8."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 10</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_10."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Titular del hogar (Únicamente nombres y apellidos) no colocar párrafo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_titular_hogar."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 14</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_14."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 16</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_16."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Situación plantilla 17</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['situacion_plantilla_17']['texto'][$cetc_situacion_plantilla_17]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 17</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_17."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Situación plantilla 18</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['situacion_plantilla_18']['texto'][$cetc_situacion_plantilla_18]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 18</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_18."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 20</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_20."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 21</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_21."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 23</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_23."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 25</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_25."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 26</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_26."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla reemplazo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_parrafo_plantilla_reemplazo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Motivo devolución correo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['motivo_devolucion']['texto'][$cetc_motivo_devolucion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetc_observaciones."</td>
                    </tr>
                    
                    <tr>
                        <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Registrado por</td>
                        <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$_SESSION[APP_SESSION.'_session_usu_nombre_completo']."</td>
                    </tr>
                    <tr>
                        <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha registro</td>
                        <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". date('d/m/Y H:i:s') ."</td>
                    </tr>
                  </table>";
                $nc_address=$_SESSION[APP_SESSION.'_session_usu_correo'].";";
                $nc_cc='';
                notificacion($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
              }
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  }
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
                    <div class="card-body row">
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetc_correo_electronico" class="my-0">Correo electrónico</label>
                            <input type="mail" class="form-control form-control-sm" name="cetc_correo_electronico" id="cetc_correo_electronico" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_correo_electronico; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetc_fecha_ingreso_correo" class="my-0">Fecha ingreso correo</label>
                            <input type="date" class="form-control form-control-sm" name="cetc_fecha_ingreso_correo" id="cetc_fecha_ingreso_correo" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_fecha_ingreso_correo; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetc_nombre_ciudadano" class="my-0">Nombre del Ciudadano (EN MAYÚSCULAS)</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_nombre_ciudadano" id="cetc_nombre_ciudadano" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_nombre_ciudadano; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetc_cedula_consulta" class="my-0">Cédula a consultar</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_cedula_consulta" id="cetc_cedula_consulta" minlength="1" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_cedula_consulta; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetc_asunto_correo" class="my-0">Asunto del correo</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_asunto_correo" id="cetc_asunto_correo" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_asunto_correo; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cetc_programa_solicitud" class="my-0">Programa al que eleva la solicitud</label>
                              <select class="form-control form-control-sm form-select" name="cetc_programa_solicitud" id="cetc_programa_solicitud" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['programa_solicitud']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['programa_solicitud']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['programa_solicitud']['id'][$i]; ?>" <?php if($cetc_programa_solicitud==$array_parametros['programa_solicitud']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['programa_solicitud']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cetc_plantilla_utilizada" class="my-0">Plantilla utilizada</label>
                              <select class="form-control form-control-sm form-select" name="cetc_plantilla_utilizada" id="cetc_plantilla_utilizada" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_plantilla_utilizada();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['plantilla_utilizada']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['plantilla_utilizada']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['plantilla_utilizada']['id'][$i]; ?>" <?php if($cetc_plantilla_utilizada==$array_parametros['plantilla_utilizada']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['plantilla_utilizada']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_solicitud_ciudadano">
                          <div class="form-group my-1">
                            <label for="cetc_solicitud_ciudadano" class="my-0">Solicitud del ciudadano (Transcribir petición, queja o solicitud concreta)</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_solicitud_ciudadano" id="cetc_solicitud_ciudadano" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_solicitud_ciudadano; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_plantilla_datos_incompletos">
                          <div class="form-group my-1">
                              <label for="cetc_plantilla_datos_incompletos" class="my-0">Plantilla datos incompletos</label>
                              <select class="form-control form-control-sm form-select" name="cetc_plantilla_datos_incompletos" id="cetc_plantilla_datos_incompletos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled onchange="validar_plantilla_datos_incompletos();">
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['plantilla_datos_incompletos']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['plantilla_datos_incompletos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['plantilla_datos_incompletos']['id'][$i]; ?>" <?php if($cetc_plantilla_datos_incompletos==$array_parametros['plantilla_datos_incompletos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['plantilla_datos_incompletos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_plantilla_datos_completos">
                          <div class="form-group my-1">
                              <label for="cetc_plantilla_datos_completos" class="my-0">Plantilla datos completos</label>
                              <select class="form-control form-control-sm form-select" name="cetc_plantilla_datos_completos" id="cetc_plantilla_datos_completos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled onchange="validar_plantilla_datos_completos();">
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['plantilla_datos_completos']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['plantilla_datos_completos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['plantilla_datos_completos']['id'][$i]; ?>" <?php if($cetc_plantilla_datos_completos==$array_parametros['plantilla_datos_completos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['plantilla_datos_completos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_motivo_devolucion">
                          <div class="form-group my-1">
                              <label for="cetc_motivo_devolucion" class="my-0">Motivo devolución correo</label>
                              <select class="form-control form-control-sm form-select" name="cetc_motivo_devolucion" id="cetc_motivo_devolucion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['motivo_devolucion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['motivo_devolucion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['motivo_devolucion']['id'][$i]; ?>" <?php if($cetc_motivo_devolucion==$array_parametros['motivo_devolucion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['motivo_devolucion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_radicacion">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_radicacion" class="my-0">Párrafo en proceso de radicación o respuesta</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_radicacion" id="cetc_parrafo_radicacion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_radicacion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>


                      <div class="col-md-12 d-none" id="div_cetc_situacion_plantilla_8">
                          <div class="form-group my-1">
                              <label for="cetc_situacion_plantilla_8" class="my-0">Situación plantilla 8</label>
                              <select class="form-control form-control-sm form-select" name="cetc_situacion_plantilla_8" id="cetc_situacion_plantilla_8" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['situacion_plantilla_8']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['situacion_plantilla_8']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['situacion_plantilla_8']['id'][$i]; ?>" <?php if($cetc_situacion_plantilla_8==$array_parametros['situacion_plantilla_8']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['situacion_plantilla_8']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_situacion_plantilla_17">
                          <div class="form-group my-1">
                              <label for="cetc_situacion_plantilla_17" class="my-0">Situación plantilla 17</label>
                              <select class="form-control form-control-sm form-select" name="cetc_situacion_plantilla_17" id="cetc_situacion_plantilla_17" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['situacion_plantilla_17']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['situacion_plantilla_17']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['situacion_plantilla_17']['id'][$i]; ?>" <?php if($cetc_situacion_plantilla_17==$array_parametros['situacion_plantilla_17']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['situacion_plantilla_17']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_situacion_plantilla_18">
                          <div class="form-group my-1">
                              <label for="cetc_situacion_plantilla_18" class="my-0">Situación plantilla 18</label>
                              <select class="form-control form-control-sm form-select" name="cetc_situacion_plantilla_18" id="cetc_situacion_plantilla_18" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['situacion_plantilla_18']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['situacion_plantilla_18']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['situacion_plantilla_18']['id'][$i]; ?>" <?php if($cetc_situacion_plantilla_18==$array_parametros['situacion_plantilla_18']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['situacion_plantilla_18']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>


                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_6">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_6" class="my-0">Párrafo plantilla 6</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_6" id="cetc_parrafo_plantilla_6" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_6; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_8">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_8" class="my-0">Párrafo plantilla 8</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_8" id="cetc_parrafo_plantilla_8" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_8; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_10">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_10" class="my-0">Párrafo plantilla 10</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_10" id="cetc_parrafo_plantilla_10" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_10; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_14">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_14" class="my-0">Párrafo plantilla 14</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_14" id="cetc_parrafo_plantilla_14" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_14; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_16">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_16" class="my-0">Párrafo plantilla 16</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_16" id="cetc_parrafo_plantilla_16" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_16; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_17">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_17" class="my-0">Párrafo plantilla 17</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_17" id="cetc_parrafo_plantilla_17" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_17; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_18">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_18" class="my-0">Párrafo plantilla 18</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_18" id="cetc_parrafo_plantilla_18" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_18; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_20">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_20" class="my-0">Párrafo plantilla 20</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_20" id="cetc_parrafo_plantilla_20" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_20; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_21">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_21" class="my-0">Párrafo plantilla 21</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_21" id="cetc_parrafo_plantilla_21" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_21; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_23">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_23" class="my-0">Párrafo plantilla 23</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_23" id="cetc_parrafo_plantilla_23" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_23; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_25">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_25" class="my-0">Párrafo plantilla 25</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_25" id="cetc_parrafo_plantilla_25" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_25; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_26">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_26" class="my-0">Párrafo plantilla 26</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_26" id="cetc_parrafo_plantilla_26" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_26; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetc_parrafo_plantilla_reemplazo">
                          <div class="form-group my-1">
                            <label for="cetc_parrafo_plantilla_reemplazo" class="my-0">Párrafo plantilla reemplazo</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_parrafo_plantilla_reemplazo" id="cetc_parrafo_plantilla_reemplazo" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_parrafo_plantilla_reemplazo; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>


                      <div class="col-md-12 d-none" id="div_cetc_titular_hogar">
                          <div class="form-group my-1">
                            <label for="cetc_titular_hogar" class="my-0">Titular del hogar (Únicamente nombres y apellidos) no colocar párrafo</label>
                            <input type="text" class="form-control form-control-sm" name="cetc_titular_hogar" id="cetc_titular_hogar" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetc_titular_hogar; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cetc_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cetc_observaciones" id="cetc_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cetc_observaciones; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <div class="form-group custom-control custom-checkbox m-0">
                                  <input type="checkbox" class="custom-control-input" id="customCheckmail" name="notificar" value="Si" <?php if(isset($_POST["guardar_registro"]) AND $_POST["notificar"]=="Si"){ echo "checked"; } ?> <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <label class="custom-control-label p-0 m-0" for="customCheckmail">Enviarme una confirmación por correo</label>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1): ?>
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
          </form>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
    function validar_plantilla_utilizada(){
      var plantilla_opcion = document.getElementById("cetc_plantilla_utilizada");
      var plantilla = plantilla_opcion.options[plantilla_opcion.selectedIndex].text;

      $("#div_cetc_solicitud_ciudadano").removeClass('d-block').addClass('d-none');
      $("#div_cetc_plantilla_datos_incompletos").removeClass('d-block').addClass('d-none');
      $("#div_cetc_plantilla_datos_completos").removeClass('d-block').addClass('d-none');
      $("#div_cetc_motivo_devolucion").removeClass('d-block').addClass('d-none');
      document.getElementById('cetc_solicitud_ciudadano').disabled=true;
      document.getElementById('cetc_plantilla_datos_incompletos').disabled=true;
      document.getElementById('cetc_plantilla_datos_completos').disabled=true;
      document.getElementById('cetc_motivo_devolucion').disabled=true;
      
      if(plantilla=="CON DATOS") {
          $("#div_cetc_solicitud_ciudadano").removeClass('d-none').addClass('d-block');
          $("#div_cetc_plantilla_datos_completos").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_solicitud_ciudadano').disabled=false;
          document.getElementById('cetc_plantilla_datos_completos').disabled=false;
      } else if(plantilla=="DATOS INCOMPLETOS") {
          $("#div_cetc_plantilla_datos_incompletos").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_plantilla_datos_incompletos').disabled=false;
      } else if(plantilla=="DEVOLUCIÓN CORREO") {
          $("#div_cetc_motivo_devolucion").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_motivo_devolucion').disabled=false;
      }
    }

    function validar_plantilla_datos_incompletos(){
      var plantilla_opcion = document.getElementById("cetc_plantilla_datos_incompletos");
      var plantilla = plantilla_opcion.options[plantilla_opcion.selectedIndex].text;

      $("#div_cetc_parrafo_radicacion").removeClass('d-block').addClass('d-none');
      document.getElementById('cetc_parrafo_radicacion').disabled=true;
      
      if(plantilla=="EN PROCESO DE RADICACIÓN O RESPUESTA") {
          $("#div_cetc_parrafo_radicacion").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_radicacion').disabled=false;
      }
    }

    function validar_plantilla_datos_completos(){
      var plantilla_opcion = document.getElementById("cetc_plantilla_datos_completos");
      var plantilla = plantilla_opcion.options[plantilla_opcion.selectedIndex].text;

      $("#div_cetc_situacion_plantilla_8").removeClass('d-block').addClass('d-none');
      $("#div_cetc_situacion_plantilla_17").removeClass('d-block').addClass('d-none');
      $("#div_cetc_situacion_plantilla_18").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_6").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_8").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_10").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_14").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_16").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_17").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_18").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_20").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_21").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_23").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_25").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_26").removeClass('d-block').addClass('d-none');
      $("#div_cetc_parrafo_plantilla_reemplazo").removeClass('d-block').addClass('d-none');
      $("#div_cetc_titular_hogar").removeClass('d-block').addClass('d-none');

      document.getElementById('cetc_situacion_plantilla_8').disabled=true;
      document.getElementById('cetc_situacion_plantilla_17').disabled=true;
      document.getElementById('cetc_situacion_plantilla_18').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_6').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_8').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_10').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_14').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_16').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_17').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_18').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_20').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_21').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_23').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_25').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_26').disabled=true;
      document.getElementById('cetc_parrafo_plantilla_reemplazo').disabled=true;
      document.getElementById('cetc_titular_hogar').disabled=true;
      
      if(plantilla=="PLANTILLA 6 HOGAR EN OTROS PROGRAMAS SOCIALES") {
          $("#div_cetc_parrafo_plantilla_6").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_6').disabled=false;
      } else if(plantilla=="PLANTILLA 8 ESTADO NO POTENCIAL BENEFICIARIO") {
          $("#div_cetc_parrafo_plantilla_8").removeClass('d-none').addClass('d-block');
          $("#div_cetc_situacion_plantilla_8").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_8').disabled=false;
          document.getElementById('cetc_situacion_plantilla_8').disabled=false;
      } else if(plantilla=="PLANTILLA 10 NO SOY BENEFICIARIO QUIERO ACCEDER") {
          $("#div_cetc_parrafo_plantilla_10").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_10').disabled=false;
      } else if(plantilla=="PLANTILLA 11 HOGAR CUBIERTO CON OTRO BENEFICIARIO") {
          $("#div_cetc_titular_hogar").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_titular_hogar').disabled=false;
      } else if(plantilla=="PLANTILLA 14 SOLICITUD PAGO A UN TERCERO") {
          $("#div_cetc_parrafo_plantilla_14").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_14').disabled=false;
      } else if(plantilla=="PLANTILLA 16 RECHAZADOS, EXCLUIDOS, RETIRADOS, SUSPENDIDOS") {
          $("#div_cetc_parrafo_plantilla_16").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_16').disabled=false;
      } else if(plantilla=="PLANTILLA 17 ESTADO BENEFICIARIO E INFORMACIÓN DE PAGOS") {
          $("#div_cetc_parrafo_plantilla_17").removeClass('d-none').addClass('d-block');
          $("#div_cetc_situacion_plantilla_17").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_17').disabled=false;
          document.getElementById('cetc_situacion_plantilla_17').disabled=false;
      } else if(plantilla=="PLANTILLA 18 CAMBIO DE TITULAR POR FALLECIMIENTO") {
          $("#div_cetc_parrafo_plantilla_18").removeClass('d-none').addClass('d-block');
          $("#div_cetc_situacion_plantilla_18").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_18').disabled=false;
          document.getElementById('cetc_situacion_plantilla_18').disabled=false;
      } else if(plantilla=="PLANTILLA 20 PETICIÓN PRESENTADA POR TERCEROS") {
          $("#div_cetc_parrafo_plantilla_20").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_20').disabled=false;
      } else if(plantilla=="PLANTILLA 21 SUPLANTACIÓN 3 OFICIOS") {
          $("#div_cetc_parrafo_plantilla_21").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_21').disabled=false;
      } else if(plantilla=="PLANTILLA 23 RECHAZO R80 CUENTA CON SALDO SUPERIOR A $5 MILLONES") {
          $("#div_cetc_parrafo_plantilla_23").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_23').disabled=false;
      } else if(plantilla=="PLANTILLA 25 EXCLUIDO POR DUPLICIDAD") {
          $("#div_cetc_parrafo_plantilla_25").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_25').disabled=false;
      } else if(plantilla=="PLANTILLA 26 GIROS 30 Y 31") {
          $("#div_cetc_parrafo_plantilla_26").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_26').disabled=false;
      } else if(plantilla=="PLANTILLA REEMPLAZO") {
          $("#div_cetc_parrafo_plantilla_reemplazo").removeClass('d-none').addClass('d-block');
          document.getElementById('cetc_parrafo_plantilla_reemplazo').disabled=false;
      }
    }

    jQuery(document).ready(function(){
        jQuery("#cetc_nombre_ciudadano").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[^a-zA-Z ]/g, ''));
        });
    });

    jQuery(document).ready(function(){
        jQuery("#cetc_cedula_consulta").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });

    jQuery(document).ready(function(){
        jQuery("#cetc_titular_hogar").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[^a-zA-Z ]/g, ''));
        });
    });
  </script>
</body>
</html>