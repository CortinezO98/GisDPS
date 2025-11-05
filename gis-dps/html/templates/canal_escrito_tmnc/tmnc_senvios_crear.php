<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-TMNC";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Transacciones Monetarias No Condicionadas | 4. Envíos | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="tmnc_senvios?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='tmnc_senvios' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }
  
  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_campania` FROM `administrador_usuario` LEFT JOIN `administrador_campania` AS TC ON `administrador_usuario`.`usu_campania`=TC.`ac_id` WHERE `usu_estado`='Activo' AND TC.`ac_nombre_campania`='Canal Escrito' ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_analistas); $i++) { 
    $array_analista[$resultado_registros_analistas[$i][0]]=$resultado_registros_analistas[$i][1];
  }

  if(isset($_POST["guardar_registro"])){
      $cete_id_clasificacion=validar_input($_POST['cete_id_clasificacion']);
      $cete_correo_electronico=validar_input($_POST['cete_correo_electronico']);
      $cete_fecha_ingreso=validar_input($_POST['cete_fecha_ingreso']);
      $cete_fecha_clasificacion=validar_input($_POST['cete_fecha_clasificacion']);
      $cete_cedula_consulta=validar_input($_POST['cete_cedula_consulta']);
      $cete_programa_solicitud=validar_input($_POST['cete_programa_solicitud']);
      $cete_respuesta_enviada=validar_input($_POST['cete_respuesta_enviada']);
      $cete_con_datos=validar_input($_POST['cete_con_datos']);
      $cete_datos_incompletos=validar_input($_POST['cete_datos_incompletos']);
      $cete_parrafo_plantilla_16=validar_input($_POST['cete_parrafo_plantilla_16']);
      $cete_parrafo_plantilla_17=validar_input($_POST['cete_parrafo_plantilla_17']);
      $cete_parrafo_plantilla_18=validar_input($_POST['cete_parrafo_plantilla_18']);
      $cete_devolucion_correo=validar_input($_POST['cete_devolucion_correo']);
      $cete_responsable_clasificacion=validar_input($_POST['cete_responsable_clasificacion']);
      $cete_responsable_envio='';
      $cete_observaciones=validar_input($_POST['cete_observaciones']);
      $cete_notificar=validar_input($_POST['notificar']);
      $cete_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cetmnc_envios`(`cete_id_clasificacion`, `cete_correo_electronico`, `cete_fecha_ingreso`, `cete_fecha_clasificacion`, `cete_cedula_consulta`, `cete_programa_solicitud`, `cete_respuesta_enviada`, `cete_con_datos`, `cete_datos_incompletos`, `cete_parrafo_plantilla_16`, `cete_parrafo_plantilla_17`, `cete_parrafo_plantilla_18`, `cete_devolucion_correo`, `cete_responsable_clasificacion`, `cete_responsable_envio`, `cete_observaciones`, `cete_notificar`, `cete_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssssssssssss', $cete_id_clasificacion, $cete_correo_electronico, $cete_fecha_ingreso, $cete_fecha_clasificacion, $cete_cedula_consulta, $cete_programa_solicitud, $cete_respuesta_enviada, $cete_con_datos, $cete_datos_incompletos, $cete_parrafo_plantilla_16, $cete_parrafo_plantilla_17, $cete_parrafo_plantilla_18, $cete_devolucion_correo, $cete_responsable_clasificacion, $cete_responsable_envio, $cete_observaciones, $cete_notificar, $cete_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cete_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='4. Envíos - TMNC | Canal Escrito';
                $referencia='4. Envíos - TMNC | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Id clasificación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cete_id_clasificacion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Correo electrónico</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cete_correo_electronico."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha ingreso correo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cete_fecha_ingreso."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha clasificación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cete_fecha_clasificacion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Cédula a consultar</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cete_cedula_consulta."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Programa al que se eleva solicitud</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['programa_solicitud']['texto'][$cete_programa_solicitud]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Respuesta enviada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['respuesta_enviada']['texto'][$cete_respuesta_enviada]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Con datos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['con_datos']['texto'][$cete_con_datos]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Datos incompletos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['datos_incompletos']['texto'][$cete_datos_incompletos]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 16</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['parrafo_plantilla_16']['texto'][$cete_parrafo_plantilla_16]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 17</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['parrafo_plantilla_17']['texto'][$cete_parrafo_plantilla_17]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Párrafo plantilla 18</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['parrafo_plantilla_18']['texto'][$cete_parrafo_plantilla_18]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Devolución correo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['devolucion_correo']['texto'][$cete_devolucion_correo]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Responsable clasificación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['responsable_clasificacion']['texto'][$cete_responsable_clasificacion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cete_observaciones."</td>
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
                            <label for="cete_id_clasificacion" class="my-0">Id clasificación</label>
                            <input type="text" class="form-control form-control-sm" name="cete_id_clasificacion" id="cete_id_clasificacion" minlength="7" maxlength="10" value="<?php if(isset($_POST["guardar_registro"])){ echo $cete_id_clasificacion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cete_correo_electronico" class="my-0">Correo electrónico</label>
                            <input type="mail" class="form-control form-control-sm" name="cete_correo_electronico" id="cete_correo_electronico" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cete_correo_electronico; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cete_fecha_ingreso" class="my-0">Fecha ingreso correo</label>
                            <input type="date" class="form-control form-control-sm" name="cete_fecha_ingreso" id="cete_fecha_ingreso" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cete_fecha_ingreso; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cete_fecha_clasificacion" class="my-0">Fecha clasificación</label>
                            <input type="date" class="form-control form-control-sm" name="cete_fecha_clasificacion" id="cete_fecha_clasificacion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cete_fecha_clasificacion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cete_cedula_consulta" class="my-0">Cédula a consultar</label>
                            <input type="text" class="form-control form-control-sm" name="cete_cedula_consulta" id="cete_cedula_consulta" minlength="1" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cete_cedula_consulta; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cete_programa_solicitud" class="my-0">Programa al que se eleva solicitud</label>
                              <select class="form-control form-control-sm form-select" name="cete_programa_solicitud" id="cete_programa_solicitud" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['programa_solicitud']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['programa_solicitud']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['programa_solicitud']['id'][$i]; ?>" <?php if($cete_programa_solicitud==$array_parametros['programa_solicitud']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['programa_solicitud']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cete_respuesta_enviada" class="my-0">Respuesta enviada</label>
                              <select class="form-control form-control-sm form-select" name="cete_respuesta_enviada" id="cete_respuesta_enviada" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_respuesta_enviada();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['respuesta_enviada']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['respuesta_enviada']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['respuesta_enviada']['id'][$i]; ?>" <?php if($cete_respuesta_enviada==$array_parametros['respuesta_enviada']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['respuesta_enviada']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cete_con_datos">
                          <div class="form-group my-1">
                              <label for="cete_con_datos" class="my-0">Con datos</label>
                              <select class="form-control form-control-sm form-select" name="cete_con_datos" id="cete_con_datos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_plantilla();" required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['con_datos']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['con_datos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['con_datos']['id'][$i]; ?>" <?php if($cete_con_datos==$array_parametros['con_datos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['con_datos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cete_datos_incompletos">
                          <div class="form-group my-1">
                              <label for="cete_datos_incompletos" class="my-0">Datos incompletos</label>
                              <select class="form-control form-control-sm form-select" name="cete_datos_incompletos" id="cete_datos_incompletos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['datos_incompletos']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['datos_incompletos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['datos_incompletos']['id'][$i]; ?>" <?php if($cete_datos_incompletos==$array_parametros['datos_incompletos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['datos_incompletos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cete_parrafo_plantilla_16">
                          <div class="form-group my-1">
                              <label for="cete_parrafo_plantilla_16" class="my-0">Párrafo plantilla 16</label>
                              <select class="form-control form-control-sm form-select" name="cete_parrafo_plantilla_16" id="cete_parrafo_plantilla_16" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['parrafo_plantilla_16']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['parrafo_plantilla_16']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['parrafo_plantilla_16']['id'][$i]; ?>" <?php if($cete_parrafo_plantilla_16==$array_parametros['parrafo_plantilla_16']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['parrafo_plantilla_16']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cete_parrafo_plantilla_17">
                          <div class="form-group my-1">
                              <label for="cete_parrafo_plantilla_17" class="my-0">Párrafo plantilla 17</label>
                              <select class="form-control form-control-sm form-select" name="cete_parrafo_plantilla_17" id="cete_parrafo_plantilla_17" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['parrafo_plantilla_17']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['parrafo_plantilla_17']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['parrafo_plantilla_17']['id'][$i]; ?>" <?php if($cete_parrafo_plantilla_17==$array_parametros['parrafo_plantilla_17']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['parrafo_plantilla_17']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cete_parrafo_plantilla_18">
                          <div class="form-group my-1">
                              <label for="cete_parrafo_plantilla_18" class="my-0">Párrafo plantilla 18</label>
                              <select class="form-control form-control-sm form-select" name="cete_parrafo_plantilla_18" id="cete_parrafo_plantilla_18" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['parrafo_plantilla_18']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['parrafo_plantilla_18']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['parrafo_plantilla_18']['id'][$i]; ?>" <?php if($cete_parrafo_plantilla_18==$array_parametros['parrafo_plantilla_18']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['parrafo_plantilla_18']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cete_devolucion_correo">
                          <div class="form-group my-1">
                              <label for="cete_devolucion_correo" class="my-0">Devolución correo</label>
                              <select class="form-control form-control-sm form-select" name="cete_devolucion_correo" id="cete_devolucion_correo" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['devolucion_correo']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['devolucion_correo']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['devolucion_correo']['id'][$i]; ?>" <?php if($cete_devolucion_correo==$array_parametros['devolucion_correo']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['devolucion_correo']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cete_responsable_clasificacion" class="my-0">Responsable clasificación</label>
                              <select class="form-control form-control-sm form-select" name="cete_responsable_clasificacion" id="cete_responsable_clasificacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['responsable_clasificacion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['responsable_clasificacion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['responsable_clasificacion']['id'][$i]; ?>" <?php if($cete_responsable_clasificacion==$array_parametros['responsable_clasificacion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['responsable_clasificacion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cete_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cete_observaciones" id="cete_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cete_observaciones; } ?></textarea>
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
    jQuery(document).ready(function(){
        jQuery("#cete_id_clasificacion").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });

    jQuery(document).ready(function(){
        jQuery("#cete_cedula_consulta").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });
  </script>

  <script type="text/javascript">
    function validar_respuesta_enviada(){
      var respuesta_enviada_opcion = document.getElementById("cete_respuesta_enviada");
      var respuesta_enviada = respuesta_enviada_opcion.options[respuesta_enviada_opcion.selectedIndex].text;

      $("#div_cete_con_datos").removeClass('d-block').addClass('d-none');
      $("#div_cete_datos_incompletos").removeClass('d-block').addClass('d-none');
      $("#div_cete_devolucion_correo").removeClass('d-block').addClass('d-none');
      document.getElementById('cete_con_datos').disabled=true;
      document.getElementById('cete_datos_incompletos').disabled=true;
      document.getElementById('cete_devolucion_correo').disabled=true;
      
      if(respuesta_enviada=="CON DATOS") {
          $("#div_cete_con_datos").removeClass('d-none').addClass('d-block');
          document.getElementById('cete_con_datos').disabled=false;
      } else if(respuesta_enviada=="DATOS INCOMPLETOS") {
          $("#div_cete_datos_incompletos").removeClass('d-none').addClass('d-block');
          document.getElementById('cete_datos_incompletos').disabled=false;
      } else if(respuesta_enviada=="DEVOLUCIÓN DE CORREO") {
          $("#div_cete_devolucion_correo").removeClass('d-none').addClass('d-block');
          document.getElementById('cete_devolucion_correo').disabled=false;
      }
    }

    function validar_plantilla(){
      var cete_con_datos_opcion = document.getElementById("cete_con_datos");
      var cete_con_datos = cete_con_datos_opcion.options[cete_con_datos_opcion.selectedIndex].text;

      $("#div_cete_parrafo_plantilla_16").removeClass('d-block').addClass('d-none');
      $("#div_cete_parrafo_plantilla_17").removeClass('d-block').addClass('d-none');
      $("#div_cete_parrafo_plantilla_18").removeClass('d-block').addClass('d-none');
      document.getElementById('cete_parrafo_plantilla_16').disabled=true;
      document.getElementById('cete_parrafo_plantilla_17').disabled=true;
      document.getElementById('cete_parrafo_plantilla_18').disabled=true;
      
      if(cete_con_datos=="PLANTILLA 16 RECHAZADOS, EXCLUIDOS, RETIRADOS, SUSPENDIDOS") {
          $("#div_cete_parrafo_plantilla_16").removeClass('d-none').addClass('d-block');
          document.getElementById('cete_parrafo_plantilla_16').disabled=false;
      } else if(cete_con_datos=="PLANTILLA 17 ESTADO BENEFICIARIO E INFORMACIÓN DE PAGOS") {
          $("#div_cete_parrafo_plantilla_17").removeClass('d-none').addClass('d-block');
          document.getElementById('cete_parrafo_plantilla_17').disabled=false;
      } else if(cete_con_datos=="PLANTILLA 18 CAMBIO DE TITULAR POR FALLECIMIENTO") {
          $("#div_cete_parrafo_plantilla_18").removeClass('d-none').addClass('d-block');
          document.getElementById('cete_parrafo_plantilla_18').disabled=false;
      }
    }

    validar_respuesta_enviada();
    validar_plantilla();
  </script>
</body>
</html>