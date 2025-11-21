<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-TMNC";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Transacciones Monetarias No Condicionadas | 1. Proyección de Respuestas | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="tmnc_sproyeccion_respuestas?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='tmnc_sproyeccion_respuestas' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
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
      $cet_radicado_entrada=validar_input($_POST['cet_radicado_entrada']);
      $cet_requiere_respuesta=validar_input($_POST['cet_requiere_respuesta']);
      $cet_abogado_aprobacion=validar_input($_POST['cet_abogado_aprobacion']);
      $cet_documento_identidad=validar_input($_POST['cet_documento_identidad']);
      $cet_nombre_ciudadano=validar_input($_POST['cet_nombre_ciudadano']);
      $cet_correo_direccion=validar_input($_POST['cet_correo_direccion']);
      $cet_programa_solicitud=validar_input($_POST['cet_programa_solicitud']);
      $cet_plantilla=validar_input($_POST['cet_plantilla']);
      $cet_con_datos=validar_input($_POST['cet_con_datos']);
      $cet_datos_incompletos=validar_input($_POST['cet_datos_incompletos']);
      $cet_plantilla_compensacion_iva=validar_input($_POST['cet_plantilla_compensacion_iva']);
      $cet_plantilla_adulto_mayor=validar_input($_POST['cet_plantilla_adulto_mayor']);
      $cet_plantilla_renta_ciudadana=validar_input($_POST['cet_plantilla_renta_ciudadana']);
      $cet_novedad_radicado=validar_input($_POST['cet_novedad_radicado']);
      $cet_motivo_archivo=validar_input($_POST['cet_motivo_archivo']);
      $cet_tipo_entidad=validar_input($_POST['cet_tipo_entidad']);
      $cet_id_solicitud=validar_input($_POST['cet_id_solicitud']);
      $cet_observaciones=validar_input($_POST['cet_observaciones']);
      $cet_notificar=validar_input($_POST['notificar']);
      $cet_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cetmnc_proyeccion_respuestas`(`cet_radicado_entrada`, `cet_requiere_respuesta`, `cet_abogado_aprobacion`, `cet_documento_identidad`, `cet_nombre_ciudadano`, `cet_correo_direccion`, `cet_programa_solicitud`, `cet_plantilla`, `cet_con_datos`, `cet_datos_incompletos`, `cet_plantilla_compensacion_iva`, `cet_plantilla_adulto_mayor`, `cet_plantilla_renta_ciudadana`, `cet_novedad_radicado`, `cet_motivo_archivo`, `cet_tipo_entidad`, `cet_id_solicitud`, `cet_observaciones`, `cet_notificar`, `cet_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssssssssssssss', $cet_radicado_entrada, $cet_requiere_respuesta, $cet_abogado_aprobacion, $cet_documento_identidad, $cet_nombre_ciudadano, $cet_correo_direccion, $cet_programa_solicitud, $cet_plantilla, $cet_con_datos, $cet_datos_incompletos, $cet_plantilla_compensacion_iva, $cet_plantilla_adulto_mayor, $cet_plantilla_renta_ciudadana, $cet_novedad_radicado, $cet_motivo_archivo, $cet_tipo_entidad, $cet_id_solicitud, $cet_observaciones, $cet_notificar, $cet_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cet_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='1. Proyección de Respuestas - TMNC | Canal Escrito';
                $referencia='1. Proyección de Respuestas - TMNC | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado entrada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cet_radicado_entrada."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Abogado asignado para aprobación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['requiere_respuesta']['texto'][$cet_requiere_respuesta]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Abogado asignado para aprobación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['abogado_aprobacion']['texto'][$cet_abogado_aprobacion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Documento identidad (Si no ha número colocar NO REGISTRA)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cet_documento_identidad."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nombre del ciudadano (Si no hay nombre colocar CIUDADANO)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cet_nombre_ciudadano."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Correo/dirección de notificación (De no existir colocar SIN DIRECCIÓN)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cet_correo_direccion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Programa al que eleva la solicitud</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['programa_solicitud']['texto'][$cet_programa_solicitud]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Plantilla utilizada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['plantilla']['texto'][$cet_plantilla]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Con datos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['con_datos']['texto'][$cet_con_datos]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Datos incompletos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['datos_incompletos']['texto'][$cet_datos_incompletos]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Plantilla compensación IVA</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['plantilla_compensacion_iva']['texto'][$cet_plantilla_compensacion_iva]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Plantilla adulto mayor</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['plantilla_adulto_mayor']['texto'][$cet_plantilla_adulto_mayor]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Plantilla renta ciudadana</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['plantilla_renta_ciudadana']['texto'][$cet_plantilla_renta_ciudadana]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Novedad radicado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['novedad_radicado']['texto'][$cet_novedad_radicado]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Motivo porque se archiva</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cet_motivo_archivo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipo de entidad</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['tipo_entidad']['texto'][$cet_tipo_entidad]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Id solicitud</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cet_id_solicitud."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cet_observaciones."</td>
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
                            <label for="cet_radicado_entrada" class="my-0">Radicado entrada</label>
                            <input type="text" class="form-control form-control-sm" name="cet_radicado_entrada" id="cet_radicado_entrada" minlength="13" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cet_radicado_entrada; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cet_requiere_respuesta" class="my-0">Requiere respuesta</label>
                              <select class="form-control form-control-sm form-select" name="cet_requiere_respuesta" id="cet_requiere_respuesta" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_requiere_respuesta();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['requiere_respuesta']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['requiere_respuesta']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['requiere_respuesta']['id'][$i]; ?>" <?php if($cet_requiere_respuesta==$array_parametros['requiere_respuesta']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['requiere_respuesta']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_abogado_aprobacion">
                          <div class="form-group my-1">
                              <label for="cet_abogado_aprobacion" class="my-0">Abogado asignado para aprobación</label>
                              <select class="form-control form-control-sm form-select" name="cet_abogado_aprobacion" id="cet_abogado_aprobacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['abogado_aprobacion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['abogado_aprobacion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['abogado_aprobacion']['id'][$i]; ?>" <?php if($cet_abogado_aprobacion==$array_parametros['abogado_aprobacion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['abogado_aprobacion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_documento_identidad">
                          <div class="form-group my-1">
                            <label for="cet_documento_identidad" class="my-0">Documento identidad (Si no ha número colocar NO REGISTRA)</label>
                            <input type="text" class="form-control form-control-sm" name="cet_documento_identidad" id="cet_documento_identidad" minlength="1" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cet_documento_identidad; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_nombre_ciudadano">
                          <div class="form-group my-1">
                            <label for="cet_nombre_ciudadano" class="my-0">Nombre del ciudadano (Si no hay nombre colocar CIUDADANO)</label>
                            <input type="text" class="form-control form-control-sm" name="cet_nombre_ciudadano" id="cet_nombre_ciudadano" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cet_nombre_ciudadano; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_correo_direccion">
                          <div class="form-group my-1">
                            <label for="cet_correo_direccion" class="my-0">Correo/dirección de notificación (De no existir colocar SIN DIRECCIÓN)</label>
                            <input type="text" class="form-control form-control-sm" name="cet_correo_direccion" id="cet_correo_direccion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cet_correo_direccion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cet_programa_solicitud" class="my-0">Programa al que eleva la solicitud</label>
                              <select class="form-control form-control-sm form-select" name="cet_programa_solicitud" id="cet_programa_solicitud" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_programa();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['programa_solicitud']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['programa_solicitud']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['programa_solicitud']['id'][$i]; ?>" <?php if($cet_programa_solicitud==$array_parametros['programa_solicitud']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['programa_solicitud']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_plantilla">
                          <div class="form-group my-1">
                              <label for="cet_plantilla" class="my-0">Plantilla utilizada</label>
                              <select class="form-control form-control-sm form-select" name="cet_plantilla" id="cet_plantilla" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_plantilla();" required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['plantilla']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['plantilla']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['plantilla']['id'][$i]; ?>" <?php if($cet_plantilla==$array_parametros['plantilla']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['plantilla']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_con_datos">
                          <div class="form-group my-1">
                              <label for="cet_con_datos" class="my-0">Con datos</label>
                              <select class="form-control form-control-sm form-select" name="cet_con_datos" id="cet_con_datos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['con_datos']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['con_datos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['con_datos']['id'][$i]; ?>" <?php if($cet_con_datos==$array_parametros['con_datos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['con_datos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_datos_incompletos">
                          <div class="form-group my-1">
                              <label for="cet_datos_incompletos" class="my-0">Datos incompletos</label>
                              <select class="form-control form-control-sm form-select" name="cet_datos_incompletos" id="cet_datos_incompletos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['datos_incompletos']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['datos_incompletos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['datos_incompletos']['id'][$i]; ?>" <?php if($cet_datos_incompletos==$array_parametros['datos_incompletos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['datos_incompletos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_plantilla_compensacion_iva">
                          <div class="form-group my-1">
                              <label for="cet_plantilla_compensacion_iva" class="my-0">Plantilla compensación IVA</label>
                              <select class="form-control form-control-sm form-select" name="cet_plantilla_compensacion_iva" id="cet_plantilla_compensacion_iva" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled onchange="validar_plantilla_iva();">
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['plantilla_compensacion_iva']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['plantilla_compensacion_iva']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['plantilla_compensacion_iva']['id'][$i]; ?>" <?php if($cet_plantilla_compensacion_iva==$array_parametros['plantilla_compensacion_iva']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['plantilla_compensacion_iva']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_plantilla_adulto_mayor">
                          <div class="form-group my-1">
                              <label for="cet_plantilla_adulto_mayor" class="my-0">Plantilla adulto mayor</label>
                              <select class="form-control form-control-sm form-select" name="cet_plantilla_adulto_mayor" id="cet_plantilla_adulto_mayor" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled onchange="validar_plantilla_mayor();">
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['plantilla_adulto_mayor']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['plantilla_adulto_mayor']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['plantilla_adulto_mayor']['id'][$i]; ?>" <?php if($cet_plantilla_adulto_mayor==$array_parametros['plantilla_adulto_mayor']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['plantilla_adulto_mayor']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_plantilla_renta_ciudadana">
                          <div class="form-group my-1">
                              <label for="cet_plantilla_renta_ciudadana" class="my-0">Plantilla renta ciudadana</label>
                              <select class="form-control form-control-sm form-select" name="cet_plantilla_renta_ciudadana" id="cet_plantilla_renta_ciudadana" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled onchange="validar_plantilla_renta_ciudadana();">
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['plantilla_renta_ciudadana']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['plantilla_renta_ciudadana']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['plantilla_renta_ciudadana']['id'][$i]; ?>" <?php if($cet_plantilla_renta_ciudadana==$array_parametros['plantilla_renta_ciudadana']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['plantilla_renta_ciudadana']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12" id="div_cet_novedad_radicado">
                          <div class="form-group my-1">
                              <label for="cet_novedad_radicado" class="my-0">Novedad radicado</label>
                              <select class="form-control form-control-sm form-select" name="cet_novedad_radicado" id="cet_novedad_radicado" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required onchange="validar_novedad_radicado();">
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['novedad_radicado']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['novedad_radicado']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['novedad_radicado']['id'][$i]; ?>" <?php if($cet_novedad_radicado==$array_parametros['novedad_radicado']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['novedad_radicado']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_motivo_archivo">
                          <div class="form-group my-1">
                            <label for="cet_motivo_archivo" class="my-0">Motivo porque se archiva</label>
                            <input type="text" class="form-control form-control-sm" name="cet_motivo_archivo" id="cet_motivo_archivo" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cet_motivo_archivo; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cet_tipo_entidad">
                          <div class="form-group my-1">
                              <label for="cet_tipo_entidad" class="my-0">Tipo de entidad</label>
                              <select class="form-control form-control-sm form-select" name="cet_tipo_entidad" id="cet_tipo_entidad" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['tipo_entidad']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['tipo_entidad']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['tipo_entidad']['id'][$i]; ?>" <?php if($cet_tipo_entidad==$array_parametros['tipo_entidad']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['tipo_entidad']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12" id="div_cet_id_solicitud">
                          <div class="form-group my-1">
                            <label for="cet_id_solicitud" class="my-0">Id solicitud</label>
                            <input type="text" class="form-control form-control-sm" name="cet_id_solicitud" id="cet_id_solicitud" minlength="13" maxlength="20" value="<?php if(isset($_POST["guardar_registro"])){ echo $cet_id_solicitud; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cet_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cet_observaciones" id="cet_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cet_observaciones; } ?></textarea>
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
    function validar_requiere_respuesta(){
      var requiere_respuesta_opcion = document.getElementById("cet_requiere_respuesta");
      var requiere_respuesta = requiere_respuesta_opcion.options[requiere_respuesta_opcion.selectedIndex].text;

      $("#div_cet_abogado_aprobacion").removeClass('d-block').addClass('d-none');
      $("#div_cet_documento_identidad").removeClass('d-block').addClass('d-none');
      $("#div_cet_nombre_ciudadano").removeClass('d-block').addClass('d-none');
      $("#div_cet_correo_direccion").removeClass('d-block').addClass('d-none');
      $("#div_cet_motivo_archivo").removeClass('d-block').addClass('d-none');
      $("#div_cet_novedad_radicado").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_abogado_aprobacion').disabled=true;
      document.getElementById('cet_documento_identidad').disabled=true;
      document.getElementById('cet_nombre_ciudadano').disabled=true;
      document.getElementById('cet_correo_direccion').disabled=true;
      document.getElementById('cet_motivo_archivo').disabled=true;
      document.getElementById('cet_novedad_radicado').disabled=true;
      
      if(requiere_respuesta=="SI") {
          $("#div_cet_abogado_aprobacion").removeClass('d-none').addClass('d-block');
          $("#div_cet_documento_identidad").removeClass('d-none').addClass('d-block');
          $("#div_cet_nombre_ciudadano").removeClass('d-none').addClass('d-block');
          $("#div_cet_correo_direccion").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_abogado_aprobacion').disabled=false;
          document.getElementById('cet_documento_identidad').disabled=false;
          document.getElementById('cet_nombre_ciudadano').disabled=false;
          document.getElementById('cet_correo_direccion').disabled=false;
      } else if(requiere_respuesta=="NO") {
          $("#div_cet_motivo_archivo").removeClass('d-none').addClass('d-block');
          $("#div_cet_novedad_radicado").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_motivo_archivo').disabled=false;
          document.getElementById('cet_novedad_radicado').disabled=false;
      }
    }

    function validar_programa(){
      var programa_opcion = document.getElementById("cet_programa_solicitud");
      var programa = programa_opcion.options[programa_opcion.selectedIndex].text;

      $("#div_cet_plantilla").removeClass('d-block').addClass('d-none');
      $("#div_cet_plantilla_compensacion_iva").removeClass('d-block').addClass('d-none');
      $("#div_cet_plantilla_adulto_mayor").removeClass('d-block').addClass('d-none');
      $("#div_cet_plantilla_renta_ciudadana").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_plantilla').disabled=true;
      document.getElementById('cet_plantilla_compensacion_iva').disabled=true;
      document.getElementById('cet_plantilla_adulto_mayor').disabled=true;
      document.getElementById('cet_plantilla_renta_ciudadana').disabled=true;
      
      if(programa=="COLOMBIA MAYOR") {
          $("#div_cet_plantilla_adulto_mayor").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_plantilla_adulto_mayor').disabled=false;
      } else if(programa=="COMPENSACIÓN DEL IVA") {
          $("#div_cet_plantilla_compensacion_iva").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_plantilla_compensacion_iva').disabled=false;
      } else if(programa=="INGRESO SOLIDARIO") {
          $("#div_cet_plantilla").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_plantilla').disabled=false;
      } else if(programa=="RENTA CIUDADANA") {
          $("#div_cet_plantilla_renta_ciudadana").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_plantilla_renta_ciudadana').disabled=false;
      }
      validar_plantilla();
    }
    
    function validar_plantilla(){
      var plantilla_opcion = document.getElementById("cet_plantilla");
      var plantilla = plantilla_opcion.options[plantilla_opcion.selectedIndex].text;

      $("#div_cet_con_datos").removeClass('d-block').addClass('d-none');
      $("#div_cet_datos_incompletos").removeClass('d-block').addClass('d-none');
      $("#div_cet_motivo_archivo").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_con_datos').disabled=true;
      document.getElementById('cet_datos_incompletos').disabled=true;
      document.getElementById('cet_motivo_archivo').disabled=true;
      
      $("#div_cet_novedad_radicado").removeClass('d-block').addClass('d-none');
      $("#div_cet_id_solicitud").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_novedad_radicado').disabled=true;
      document.getElementById('cet_id_solicitud').disabled=true;
      
      if(plantilla=="ARCHIVAR") {
          $("#div_cet_motivo_archivo").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_motivo_archivo').disabled=false;

          $("#div_cet_novedad_radicado").removeClass('d-none').addClass('d-block');
          $("#div_cet_id_solicitud").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_novedad_radicado').disabled=false;
          document.getElementById('cet_id_solicitud').disabled=false;
      } else if(plantilla=="CON DATOS") {
          $("#div_cet_con_datos").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_con_datos').disabled=false;
      } else if(plantilla=="DATOS INCOMPLETOS") {
          $("#div_cet_datos_incompletos").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_datos_incompletos').disabled=false;
      }
    }

    function validar_plantilla_iva() {
      var plantilla_opcion = document.getElementById("cet_plantilla_compensacion_iva");
      var plantilla = plantilla_opcion.options[plantilla_opcion.selectedIndex].text;

      $("#div_cet_motivo_archivo").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_motivo_archivo').disabled=true;

      $("#div_cet_novedad_radicado").removeClass('d-block').addClass('d-none');
      $("#div_cet_id_solicitud").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_novedad_radicado').disabled=true;
      document.getElementById('cet_id_solicitud').disabled=true;
      
      if(plantilla=="ARCHIVAR") {
          $("#div_cet_motivo_archivo").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_motivo_archivo').disabled=false;

          $("#div_cet_novedad_radicado").removeClass('d-none').addClass('d-block');
          $("#div_cet_id_solicitud").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_novedad_radicado').disabled=false;
          document.getElementById('cet_id_solicitud').disabled=false;
      }
    }

    function validar_plantilla_mayor(){
      var plantilla_opcion = document.getElementById("cet_plantilla_adulto_mayor");
      var plantilla = plantilla_opcion.options[plantilla_opcion.selectedIndex].text;

      $("#div_cet_motivo_archivo").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_motivo_archivo').disabled=true;

      $("#div_cet_novedad_radicado").removeClass('d-block').addClass('d-none');
      $("#div_cet_id_solicitud").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_novedad_radicado').disabled=true;
      document.getElementById('cet_id_solicitud').disabled=true;
      
      if(plantilla=="ARCHIVAR") {
          $("#div_cet_motivo_archivo").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_motivo_archivo').disabled=false;

          $("#div_cet_novedad_radicado").removeClass('d-none').addClass('d-block');
          $("#div_cet_id_solicitud").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_novedad_radicado').disabled=false;
          document.getElementById('cet_id_solicitud').disabled=false;
      }
    }

    function validar_plantilla_renta_ciudadana(){
      var plantilla_opcion = document.getElementById("cet_plantilla_renta_ciudadana");
      var plantilla = plantilla_opcion.options[plantilla_opcion.selectedIndex].text;

      $("#div_cet_motivo_archivo").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_motivo_archivo').disabled=true;

      $("#div_cet_novedad_radicado").removeClass('d-block').addClass('d-none');
      $("#div_cet_id_solicitud").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_novedad_radicado').disabled=true;
      document.getElementById('cet_id_solicitud').disabled=true;
      
      if(plantilla=="ARCHIVAR") {
          $("#div_cet_motivo_archivo").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_motivo_archivo').disabled=false;

          $("#div_cet_novedad_radicado").removeClass('d-none').addClass('d-block');
          $("#div_cet_id_solicitud").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_novedad_radicado').disabled=false;
          document.getElementById('cet_id_solicitud').disabled=false;
      }
    }

    function validar_novedad_radicado() {
      var novedad_opcion = document.getElementById("cet_novedad_radicado");
      var novedad = novedad_opcion.options[novedad_opcion.selectedIndex].text;

      $("#div_cet_tipo_entidad").removeClass('d-block').addClass('d-none');
      document.getElementById('cet_tipo_entidad').disabled=true;
      
      if(novedad=="RESPUESTA A ENTIDAD") {
          $("#div_cet_tipo_entidad").removeClass('d-none').addClass('d-block');
          document.getElementById('cet_tipo_entidad').disabled=false;
      }
    }

    validar_requiere_respuesta();
    validar_programa();
    validar_plantilla();
    validar_plantilla_iva();
    validar_plantilla_mayor();
    validar_novedad_radicado();
  </script>

  <script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#cet_radicado_entrada").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[ ]/g, ''));
        });
    });

    jQuery(document).ready(function(){
        jQuery("#cet_nombre_ciudadano").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[^a-zA-Z ]/g, ''));
        });
    });
  </script>
</body>
</html>