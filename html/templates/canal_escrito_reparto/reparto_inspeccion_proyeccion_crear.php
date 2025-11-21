<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Reparto";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Reparto | 4. Inspección Proyección | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="reparto_inspeccion_proyeccion?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='inspeccion_proyeccion' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
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
      $ceip_radicado_entrada=validar_input($_POST['ceip_radicado_entrada']);
      $ceip_proyector_carta=validar_input($_POST['ceip_proyector_carta']);
      $ceip_estado=validar_input($_POST['ceip_estado']);

      if (isset($_POST['ceip_tipo_rechazo'])) {
        $ceip_tipo_rechazo=$_POST['ceip_tipo_rechazo'];
      } else {
        $ceip_tipo_rechazo=array();
      }

      $ceip_tipo_rechazo_insert=implode(';', $ceip_tipo_rechazo);
      $ceip_tipo_rechazo_correo='';
      for ($i=0; $i < count($ceip_tipo_rechazo); $i++) { 
        $ceip_tipo_rechazo_correo.=$array_parametros['tipo_rechazo']['texto'][$ceip_tipo_rechazo[$i]].'<br>';
      }

      $ceip_observaciones=validar_input($_POST['ceip_observaciones']);
      $ceip_notificar=validar_input($_POST['notificar']);
      $ceip_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cerep_inspeccion_proyeccion`(`ceip_radicado_entrada`, `ceip_proyector_carta`, `ceip_estado`, `ceip_tipo_rechazo`, `ceip_observaciones`, `ceip_notificar`, `ceip_registro_usuario`) VALUES (?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssss', $ceip_radicado_entrada, $ceip_proyector_carta, $ceip_estado, $ceip_tipo_rechazo_insert, $ceip_observaciones, $ceip_notificar, $ceip_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($ceip_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='4. Inspección Proyección - Reparto | Canal Escrito';
                $referencia='4. Inspección Proyección - Reparto | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado entrada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$ceip_radicado_entrada."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Proyector de carta</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_analista[$ceip_proyector_carta]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Estado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['estado']['texto'][$ceip_estado]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipo de rechazo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$ceip_tipo_rechazo_correo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$ceip_observaciones."</td>
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

  if (isset($_POST['ceip_tipo_rechazo'])) {
    $ceip_tipo_rechazo=$_POST['ceip_tipo_rechazo'];
  } else {
    $ceip_tipo_rechazo=array();
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
                            <label for="ceip_radicado_entrada" class="my-0">Radicado entrada</label>
                            <input type="text" class="form-control form-control-sm" name="ceip_radicado_entrada" id="ceip_radicado_entrada" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $ceip_radicado_entrada; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="ceip_proyector_carta" class="my-0">Proyector de carta</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="ceip_proyector_carta" id="ceip_proyector_carta" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($ceip_proyector_carta==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="ceip_estado" class="my-0">Estado</label>
                              <select class="form-control form-control-sm form-select" name="ceip_estado" id="ceip_estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_estado();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($array_parametros['estado']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['estado']['id'][$i]; ?>" <?php if($ceip_estado==$array_parametros['estado']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['estado']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_ceip_tipo_rechazo">
                          <div class="form-group my-1">
                              <label for="ceip_tipo_rechazo" class="my-0">Tipo de rechazo</label>
                              <select class="form-control form-control-sm form-select" title="Seleccione" data-live-search="false" data-container="body" name="ceip_tipo_rechazo[]" id="ceip_tipo_rechazo" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> disabled required multiple>
                                  <?php for ($i=0; $i < count($array_parametros['tipo_rechazo']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['tipo_rechazo']['id'][$i]; ?>" <?php if(in_array($array_parametros['tipo_rechazo']['id'][$i], $ceip_tipo_rechazo)){ echo "selected"; } ?>><?php echo $array_parametros['tipo_rechazo']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="ceip_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="ceip_observaciones" id="ceip_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $ceip_observaciones; } ?></textarea>
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
    function validar_estado(){
      var estado_opcion = document.getElementById("ceip_estado");
      var estado = estado_opcion.options[estado_opcion.selectedIndex].text;

      $("#div_ceip_tipo_rechazo").removeClass('d-block').addClass('d-none');
      document.getElementById('ceip_tipo_rechazo').disabled=true;

      if(estado=="Rechazado") {
          $("#div_ceip_tipo_rechazo").removeClass('d-none').addClass('d-block');
          document.getElementById('ceip_tipo_rechazo').disabled=false;
          $('#ceip_tipo_rechazo').selectpicker();
      }
    }
    validar_estado();
  </script>
</body>
</html>