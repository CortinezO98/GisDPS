<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Jóvenes Acción-Focalización";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Jóvenes en Acción y Focalización | 5. Formato Gestión de Novedades JeA | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="jafocalizacion_gestion_novedades?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='jafocalizacion_gestion_novedades' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }

  if(isset($_POST["guardar_registro"])){
      $cejgn_id_novedad=validar_input($_POST['cejgn_id_novedad']);
      $cejgn_id_persona=validar_input($_POST['cejgn_id_persona']);
      $cejgn_fecha_gestion=validar_input($_POST['cejgn_fecha_gestion']);
      $cejgn_estado=validar_input($_POST['cejgn_estado']);
      $cejgn_tipo_rechazo=validar_input($_POST['cejgn_tipo_rechazo']);
      $cejgn_observacion_rechazo=validar_input($_POST['cejgn_observacion_rechazo']);
      $cejgn_correccion_datos_sija=validar_input($_POST['cejgn_correccion_datos_sija']);
      $cejgn_codigo_novedad=validar_input($_POST['cejgn_codigo_novedad']);
      $cejgn_observaciones='';
      $cejgn_notificar=validar_input($_POST['notificar']);
      $cejgn_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cejafo_gestion_novedades`(`cejgn_id_novedad`, `cejgn_id_persona`, `cejgn_fecha_gestion`, `cejgn_estado`, `cejgn_tipo_rechazo`, `cejgn_observacion_rechazo`, `cejgn_correccion_datos_sija`, `cejgn_codigo_novedad`, `cejgn_observaciones`, `cejgn_notificar`, `cejgn_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssssssss', $cejgn_id_novedad, $cejgn_id_persona, $cejgn_fecha_gestion, $cejgn_estado, $cejgn_tipo_rechazo, $cejgn_observacion_rechazo, $cejgn_correccion_datos_sija, $cejgn_codigo_novedad, $cejgn_observaciones, $cejgn_notificar, $cejgn_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cejgn_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='5. Formato Gestión de Novedades - JAFocalización | Canal Escrito';
                $referencia='5. Formato Gestión de Novedades - JAFocalización | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Id novedad</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgn_id_novedad."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Id persona</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgn_id_persona."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha gestión</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgn_fecha_gestion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Estado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgn_estado']['texto'][$cejgn_estado]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipo rechazo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgn_tipo_rechazo']['texto'][$cejgn_tipo_rechazo]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observación de rechazo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgn_observacion_rechazo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Corrección datos SIJA</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgn_correccion_datos_sija']['texto'][$cejgn_correccion_datos_sija]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Código de la novedad</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgn_codigo_novedad."</td>
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
                            <label for="cejgn_id_novedad" class="my-0">Id novedad</label>
                            <input type="text" class="form-control form-control-sm" name="cejgn_id_novedad" id="cejgn_id_novedad" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgn_id_novedad; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgn_id_persona" class="my-0">Id persona</label>
                            <input type="text" class="form-control form-control-sm" name="cejgn_id_persona" id="cejgn_id_persona" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgn_id_persona; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgn_fecha_gestion" class="my-0">Fecha gestión</label>
                            <input type="date" class="form-control form-control-sm" name="cejgn_fecha_gestion" id="cejgn_fecha_gestion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgn_fecha_gestion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgn_estado" class="my-0">Estado</label>
                              <select class="form-control form-control-sm form-select" name="cejgn_estado" id="cejgn_estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgn_estado']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgn_estado']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgn_estado']['id'][$i]; ?>" <?php if($cejgn_estado==$array_parametros['cejgn_estado']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgn_estado']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgn_tipo_rechazo" class="my-0">Tipo rechazo</label>
                              <select class="form-control form-control-sm form-select" name="cejgn_tipo_rechazo" id="cejgn_tipo_rechazo" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgn_tipo_rechazo']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgn_tipo_rechazo']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgn_tipo_rechazo']['id'][$i]; ?>" <?php if($cejgn_tipo_rechazo==$array_parametros['cejgn_tipo_rechazo']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgn_tipo_rechazo']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cejgn_observacion_rechazo" class="my-0">Observación de rechazo</label>
                            <textarea class="form-control form-control-sm height-100" name="cejgn_observacion_rechazo" id="cejgn_observacion_rechazo" maxlength="350" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cejgn_observacion_rechazo; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgn_correccion_datos_sija" class="my-0">Corrección datos SIJA</label>
                              <select class="form-control form-control-sm form-select" name="cejgn_correccion_datos_sija" id="cejgn_correccion_datos_sija" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgn_correccion_datos_sija']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgn_correccion_datos_sija']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgn_correccion_datos_sija']['id'][$i]; ?>" <?php if($cejgn_correccion_datos_sija==$array_parametros['cejgn_correccion_datos_sija']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgn_correccion_datos_sija']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgn_codigo_novedad" class="my-0">Código de la novedad</label>
                            <input type="text" class="form-control form-control-sm" name="cejgn_codigo_novedad" id="cejgn_codigo_novedad" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgn_codigo_novedad; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off">
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
        jQuery("#cejgn_id_novedad").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });
    jQuery(document).ready(function(){
        jQuery("#cejgn_id_persona").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });
    jQuery(document).ready(function(){
        jQuery("#cejgn_codigo_novedad").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });
  </script>
</body>
</html>