<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-TMNC";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Transacciones Monetarias No Condicionadas | 8. Aprobación Novedades CM | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="tmnc_saprobacion_novedades?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='tmnc_saprobacion_novedades' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }
  
  if(isset($_POST["guardar_registro"])){
      $cetan_cod_beneficiario=validar_input($_POST['cetan_cod_beneficiario']);
      $cetan_tipo_documento=validar_input($_POST['cetan_tipo_documento']);
      $cetan_documento=validar_input($_POST['cetan_documento']);
      $cetan_nombres_apellidos=validar_input($_POST['cetan_nombres_apellidos']);
      $cetan_tipo_novedad=validar_input($_POST['cetan_tipo_novedad']);
      $cetan_datos_basicos=validar_input($_POST['cetan_datos_basicos']);
      $cetan_suspension=validar_input($_POST['cetan_suspension']);
      $cetan_reactivacion=validar_input($_POST['cetan_reactivacion']);
      $cetan_retiro=validar_input($_POST['cetan_retiro']);
      $cetan_gestion=validar_input($_POST['cetan_gestion']);
      $cetan_tipo_rechazo=validar_input($_POST['cetan_tipo_rechazo']);
      $cetan_realizo_cambio_datos=validar_input($_POST['cetan_realizo_cambio_datos']);
      
      if (isset($_POST['cetan_correccion_datos'])) {
        $cetan_correccion_datos=$_POST['cetan_correccion_datos'];
      } else {
        $cetan_correccion_datos=array();
      }

      $cetan_correccion_datos_insert=implode(';', $cetan_correccion_datos);

      $cetan_correccion_datos_correo='';
      for ($i=0; $i < count($cetan_correccion_datos); $i++) { 
        $cetan_correccion_datos_correo.=$array_parametros['correccion_datos']['texto'][$cetan_correccion_datos[$i]].'<br>';
      }


      $cetan_observaciones=validar_input($_POST['cetan_observaciones']);
      $cetan_notificar=validar_input($_POST['notificar']);
      $cetan_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cetmnc_aprobacion_novedades`(`cetan_cod_beneficiario`, `cetan_tipo_documento`, `cetan_documento`, `cetan_nombres_apellidos`, `cetan_tipo_novedad`, `cetan_datos_basicos`, `cetan_suspension`, `cetan_reactivacion`, `cetan_retiro`, `cetan_gestion`, `cetan_tipo_rechazo`, `cetan_realizo_cambio_datos`, `cetan_correccion_datos`, `cetan_observaciones`, `cetan_notificar`, `cetan_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssssssssss', $cetan_cod_beneficiario, $cetan_tipo_documento, $cetan_documento, $cetan_nombres_apellidos, $cetan_tipo_novedad, $cetan_datos_basicos, $cetan_suspension, $cetan_reactivacion, $cetan_retiro, $cetan_gestion, $cetan_tipo_rechazo, $cetan_realizo_cambio_datos, $cetan_correccion_datos_insert, $cetan_observaciones, $cetan_notificar, $cetan_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cetan_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='8. Aprobación Novedades CM - TMNC | Canal Escrito';
                $referencia='8. Aprobación Novedades CM - TMNC | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Cod. beneficiario</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetan_cod_beneficiario."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipo documento</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['tipo_documento']['texto'][$cetan_tipo_documento]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Documento</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetan_documento."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nombres y apellidos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetan_nombres_apellidos."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipo novedad</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['tipo_novedad']['texto'][$cetan_tipo_novedad]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Datos básicos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['datos_basicos']['texto'][$cetan_datos_basicos]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Suspensión</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['suspension']['texto'][$cetan_suspension]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Reactivación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['reactivacion']['texto'][$cetan_reactivacion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Retiro</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['retiro']['texto'][$cetan_retiro]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Gestión</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['gestion']['texto'][$cetan_gestion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipo rechazo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['tipo_rechazo']['texto'][$cetan_tipo_rechazo]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Realizó cambio de datos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['realizo_cambio_datos']['texto'][$cetan_realizo_cambio_datos]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Corrección de datos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetan_correccion_datos."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cetan_observaciones."</td>
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

  if (isset($_POST['cetan_correccion_datos'])) {
    $cetan_correccion_datos=$_POST['cetan_correccion_datos'];
  } else {
    $cetan_correccion_datos=array();
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
                            <label for="cetan_cod_beneficiario" class="my-0">Cod. beneficiario</label>
                            <input type="text" class="form-control form-control-sm" name="cetan_cod_beneficiario" id="cetan_cod_beneficiario" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetan_cod_beneficiario; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cetan_tipo_documento" class="my-0">Tipo documento</label>
                              <select class="form-control form-control-sm form-select" name="cetan_tipo_documento" id="cetan_tipo_documento" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['tipo_documento']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['tipo_documento']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['tipo_documento']['id'][$i]; ?>" <?php if($cetan_tipo_documento==$array_parametros['tipo_documento']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['tipo_documento']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetan_documento" class="my-0">Documento</label>
                            <input type="text" class="form-control form-control-sm" name="cetan_documento" id="cetan_documento" minlength="1" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetan_documento; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cetan_nombres_apellidos" class="my-0">Nombres y apellidos</label>
                            <input type="text" class="form-control form-control-sm" name="cetan_nombres_apellidos" id="cetan_nombres_apellidos" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cetan_nombres_apellidos; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cetan_tipo_novedad" class="my-0">Tipo novedad</label>
                              <select class="form-control form-control-sm form-select" name="cetan_tipo_novedad" id="cetan_tipo_novedad" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_tipo_novedad();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['tipo_novedad']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['tipo_novedad']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['tipo_novedad']['id'][$i]; ?>" <?php if($cetan_tipo_novedad==$array_parametros['tipo_novedad']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['tipo_novedad']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>



                      <div class="col-md-12 d-none" id="div_cetan_datos_basicos">
                          <div class="form-group my-1">
                              <label for="cetan_datos_basicos" class="my-0">Datos básicos</label>
                              <select class="form-control form-control-sm form-select" name="cetan_datos_basicos" id="cetan_datos_basicos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['datos_basicos']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['datos_basicos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['datos_basicos']['id'][$i]; ?>" <?php if($cetan_datos_basicos==$array_parametros['datos_basicos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['datos_basicos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetan_suspension">
                          <div class="form-group my-1">
                              <label for="cetan_suspension" class="my-0">Suspensión</label>
                              <select class="form-control form-control-sm form-select" name="cetan_suspension" id="cetan_suspension" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['suspension']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['suspension']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['suspension']['id'][$i]; ?>" <?php if($cetan_suspension==$array_parametros['suspension']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['suspension']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetan_reactivacion">
                          <div class="form-group my-1">
                              <label for="cetan_reactivacion" class="my-0">Reactivación</label>
                              <select class="form-control form-control-sm form-select" name="cetan_reactivacion" id="cetan_reactivacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['reactivacion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['reactivacion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['reactivacion']['id'][$i]; ?>" <?php if($cetan_reactivacion==$array_parametros['reactivacion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['reactivacion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetan_retiro">
                          <div class="form-group my-1">
                              <label for="cetan_retiro" class="my-0">Retiro</label>
                              <select class="form-control form-control-sm form-select" name="cetan_retiro" id="cetan_retiro" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['retiro']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['retiro']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['retiro']['id'][$i]; ?>" <?php if($cetan_retiro==$array_parametros['retiro']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['retiro']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetan_realizo_cambio_datos">
                          <div class="form-group my-1">
                              <label for="cetan_realizo_cambio_datos" class="my-0">Realizó cambio de datos</label>
                              <select class="form-control form-control-sm form-select" name="cetan_realizo_cambio_datos" id="cetan_realizo_cambio_datos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_realizo_cambio_datos();" required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['realizo_cambio_datos']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['realizo_cambio_datos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['realizo_cambio_datos']['id'][$i]; ?>" <?php if($cetan_realizo_cambio_datos==$array_parametros['realizo_cambio_datos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['realizo_cambio_datos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetan_correccion_datos">
                          <div class="form-group my-1">
                              <label for="cetan_correccion_datos" class="my-0">Corrección de datos</label>
                              <select class="form-control form-control-sm form-select" title="Seleccione" data-live-search="false" data-container="body" name="cetan_correccion_datos[]" id="cetan_correccion_datos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled multiple>
                                  <?php if(isset($array_parametros['correccion_datos']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['correccion_datos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['correccion_datos']['id'][$i]; ?>" <?php if($cetan_correccion_datos==$array_parametros['correccion_datos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['correccion_datos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetan_gestion">
                          <div class="form-group my-1">
                              <label for="cetan_gestion" class="my-0">Gestión</label>
                              <select class="form-control form-control-sm form-select" name="cetan_gestion" id="cetan_gestion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_gestion();" required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['gestion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['gestion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['gestion']['id'][$i]; ?>" <?php if($cetan_gestion==$array_parametros['gestion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['gestion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cetan_tipo_rechazo">
                          <div class="form-group my-1">
                              <label for="cetan_tipo_rechazo" class="my-0">Tipo rechazo</label>
                              <select class="form-control form-control-sm form-select" name="cetan_tipo_rechazo" id="cetan_tipo_rechazo" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['tipo_rechazo']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['tipo_rechazo']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['tipo_rechazo']['id'][$i]; ?>" <?php if($cetan_tipo_rechazo==$array_parametros['tipo_rechazo']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['tipo_rechazo']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cetan_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cetan_observaciones" id="cetan_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cetan_observaciones; } ?></textarea>
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
    function validar_tipo_novedad(){
      var tipo_novedad_opcion = document.getElementById("cetan_tipo_novedad");
      var tipo_novedad = tipo_novedad_opcion.options[tipo_novedad_opcion.selectedIndex].text;

      $("#div_cetan_datos_basicos").removeClass('d-block').addClass('d-none');
      $("#div_cetan_suspension").removeClass('d-block').addClass('d-none');
      $("#div_cetan_reactivacion").removeClass('d-block').addClass('d-none');
      $("#div_cetan_retiro").removeClass('d-block').addClass('d-none');
      $("#div_cetan_realizo_cambio_datos").removeClass('d-block').addClass('d-none');
      $("#div_cetan_gestion").removeClass('d-block').addClass('d-none');
      $("#div_cetan_tipo_rechazo").removeClass('d-block').addClass('d-none');
      $("#div_cetan_correccion_datos").removeClass('d-block').addClass('d-none');

      document.getElementById('cetan_datos_basicos').disabled=true;
      document.getElementById('cetan_suspension').disabled=true;
      document.getElementById('cetan_reactivacion').disabled=true;
      document.getElementById('cetan_retiro').disabled=true;
      document.getElementById('cetan_realizo_cambio_datos').disabled=true;
      document.getElementById('cetan_gestion').disabled=true;
      document.getElementById('cetan_tipo_rechazo').disabled=true;
      document.getElementById('cetan_correccion_datos').disabled=true;
      
      if(tipo_novedad=="DATOS BÁSICOS") {
          $("#div_cetan_datos_basicos").removeClass('d-none').addClass('d-block');
          $("#div_cetan_realizo_cambio_datos").removeClass('d-none').addClass('d-block');
          document.getElementById('cetan_datos_basicos').disabled=false;
          document.getElementById('cetan_realizo_cambio_datos').disabled=false;
      } else if(tipo_novedad=="SUSPENSIÓN") {
          $("#div_cetan_suspension").removeClass('d-none').addClass('d-block');
          $("#div_cetan_gestion").removeClass('d-none').addClass('d-block');
          document.getElementById('cetan_suspension').disabled=false;
          document.getElementById('cetan_gestion').disabled=false;
      } else if(tipo_novedad=="REACTIVACIÓN") {
          $("#div_cetan_reactivacion").removeClass('d-none').addClass('d-block');
          $("#div_cetan_gestion").removeClass('d-none').addClass('d-block');
          document.getElementById('cetan_reactivacion').disabled=false;
          document.getElementById('cetan_gestion').disabled=false;
      } else if(tipo_novedad=="RETIRO") {
          $("#div_cetan_retiro").removeClass('d-none').addClass('d-block');
          $("#div_cetan_gestion").removeClass('d-none').addClass('d-block');
          document.getElementById('cetan_retiro').disabled=false;
          document.getElementById('cetan_gestion').disabled=false;
      }
    } 

    function validar_gestion(){
      var gestion_opcion = document.getElementById("cetan_gestion");
      var gestion = gestion_opcion.options[gestion_opcion.selectedIndex].text;

      $("#div_cetan_tipo_rechazo").removeClass('d-block').addClass('d-none');
      document.getElementById('cetan_tipo_rechazo').disabled=true;
      
      if(gestion=="RECHAZADO") {
          $("#div_cetan_tipo_rechazo").removeClass('d-none').addClass('d-block');
          document.getElementById('cetan_tipo_rechazo').disabled=false;
      }
    }

    function validar_realizo_cambio_datos(){
      var cambio_datos_opcion = document.getElementById("cetan_realizo_cambio_datos");
      var cambio_datos = cambio_datos_opcion.options[cambio_datos_opcion.selectedIndex].text;

      $("#div_cetan_correccion_datos").removeClass('d-block').addClass('d-none');
      $("#div_cetan_gestion").removeClass('d-block').addClass('d-none');
      document.getElementById('cetan_correccion_datos').disabled=true;
      document.getElementById('cetan_gestion').disabled=true;
      if(cambio_datos=="SI") {
          $("#div_cetan_correccion_datos").removeClass('d-none').addClass('d-block');
          $("#div_cetan_gestion").removeClass('d-none').addClass('d-block');
          document.getElementById('cetan_correccion_datos').disabled=false;
          document.getElementById('cetan_gestion').disabled=false;
          $('#cetan_correccion_datos').selectpicker();
      }
    }

    jQuery(document).ready(function(){
        jQuery("#cetan_cod_beneficiario").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });
    jQuery(document).ready(function(){
        jQuery("#cetan_documento").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });
    jQuery(document).ready(function(){
        jQuery("#cetan_nombres_apellidos").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[^a-zA-Z ]/g, ''));
        });
    });
    
    validar_tipo_novedad();
  </script>
</body>
</html>