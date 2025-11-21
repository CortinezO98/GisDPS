<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Jóvenes Acción-Focalización";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Jóvenes en Acción y Focalización | 7. Formato Gestión de Aprobación JeA | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="jafocalizacion_gestion_aprobacion?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='jafocalizacion_gestion_aprobacion' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
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
      $cejga_radicado_entrada=validar_input($_POST['cejga_radicado_entrada']);
      $cejga_proyector=validar_input($_POST['cejga_proyector']);
      $cejga_revisor='';
      $cejga_cedula_aprobador='';
      $cejga_gestion=validar_input($_POST['cejga_gestion']);

      if (isset($_POST['cejga_oportunidad_mejora'])) {
        $cejga_oportunidad_mejora=$_POST['cejga_oportunidad_mejora'];
      } else {
        $cejga_oportunidad_mejora=array();
      }

      $cejga_oportunidad_mejora_insert=implode(';', $cejga_oportunidad_mejora);

      $cejga_oportunidad_mejora_correo='';
      for ($i=0; $i < count($cejga_oportunidad_mejora); $i++) { 
        $cejga_oportunidad_mejora_correo.=$array_parametros['cejga_oportunidad_mejora']['texto'][$cejga_oportunidad_mejora[$i]].'<br>';
      }

      $cejga_comentario_delta=validar_input($_POST['cejga_comentario_delta']);
      $cejga_observaciones=validar_input($_POST['cejga_observaciones']);
      $cejga_notificar=validar_input($_POST['notificar']);
      $cejga_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cejafo_gestion_aprobacion`(`cejga_radicado_entrada`, `cejga_proyector`, `cejga_revisor`, `cejga_cedula_aprobador`, `cejga_gestion`, `cejga_oportunidad_mejora`, `cejga_comentario_delta`, `cejga_observaciones`, `cejga_notificar`, `cejga_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssss', $cejga_radicado_entrada, $cejga_proyector, $cejga_revisor, $cejga_cedula_aprobador, $cejga_gestion, $cejga_oportunidad_mejora_insert, $cejga_comentario_delta, $cejga_observaciones, $cejga_notificar, $cejga_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cejga_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='7. Formato Gestión de Aprobación - JAFocalización | Canal Escrito';
                $referencia='7. Formato Gestión de Aprobación - JAFocalización | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado entrada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejga_radicado_entrada."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Proyector</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_analista[$cejga_proyector]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Gestión</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejga_gestion']['texto'][$cejga_gestion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Oportunidades de mejora</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejga_oportunidad_mejora_correo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Comentario delta</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejga_comentario_delta."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejga_observaciones."</td>
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
                            <label for="cejga_radicado_entrada" class="my-0">Radicado entrada</label>
                            <input type="text" class="form-control form-control-sm" name="cejga_radicado_entrada" id="cejga_radicado_entrada" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejga_radicado_entrada; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejga_proyector" class="my-0">Proyector</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cejga_proyector" id="cejga_proyector" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($cejga_proyector==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejga_gestion" class="my-0">Gestión</label>
                              <select class="form-control form-control-sm form-select" name="cejga_gestion" id="cejga_gestion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejga_gestion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejga_gestion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejga_gestion']['id'][$i]; ?>" <?php if($cejga_gestion==$array_parametros['cejga_gestion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejga_gestion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejga_oportunidad_mejora" class="my-0">Oportunidades de mejora</label>
                              <select class="selectpicker form-control form-control-sm form-select" title="Seleccione" data-live-search="false" data-container="body" name="cejga_oportunidad_mejora[]" id="cejga_oportunidad_mejora" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required multiple>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejga_oportunidad_mejora']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejga_oportunidad_mejora']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejga_oportunidad_mejora']['id'][$i]; ?>" <?php if($cejga_oportunidad_mejora==$array_parametros['cejga_oportunidad_mejora']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejga_oportunidad_mejora']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cejga_comentario_delta" class="my-0">Comentario delta</label>
                            <textarea class="form-control form-control-sm height-100" name="cejga_comentario_delta" id="cejga_comentario_delta" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cejga_comentario_delta; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cejga_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cejga_observaciones" id="cejga_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cejga_observaciones; } ?></textarea>
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
        jQuery("#cejga_radicado_entrada").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[ ]/g, ''));
        });
    });
  </script>
</body>
</html>