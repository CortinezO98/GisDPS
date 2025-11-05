<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Reparto";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Reparto | 7. Firma Traslados | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="reparto_firma_traslados?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='firma_traslados' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
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
      $ceft_radicado_entrada=validar_input($_POST['ceft_radicado_entrada']);
      $ceft_radicado_salida=validar_input($_POST['ceft_radicado_salida']);
      $ceft_rechazos=validar_input($_POST['ceft_rechazos']);
      
      if (isset($_POST['ceft_forma'])) {
        $ceft_forma=$_POST['ceft_forma'];
      } else {
        $ceft_forma=array();
      }

      $ceft_forma_insert=implode(';', $ceft_forma);

      $ceft_forma_correo='';
      for ($i=0; $i < count($ceft_forma); $i++) { 
        $ceft_forma_correo.=$array_parametros['forma']['texto'][$ceft_forma[$i]].'<br>';
      }

      $ceft_proyector=validar_input($_POST['ceft_proyector']);
      $ceft_inspector=validar_input($_POST['ceft_inspector']);
      $ceft_aprobador=validar_input($_POST['ceft_aprobador']);
      $ceft_observaciones=validar_input($_POST['ceft_observaciones']);
      $ceft_notificar=validar_input($_POST['notificar']);
      $ceft_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cerep_firma_traslados`(`ceft_radicado_entrada`, `ceft_radicado_salida`, `ceft_rechazos`, `ceft_forma`, `ceft_proyector`, `ceft_inspector`, `ceft_aprobador`, `ceft_observaciones`, `ceft_notificar`, `ceft_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssss', $ceft_radicado_entrada, $ceft_radicado_salida, $ceft_rechazos, $ceft_forma_insert, $ceft_proyector, $ceft_inspector, $ceft_aprobador, $ceft_observaciones, $ceft_notificar, $ceft_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($ceft_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='7. Firma Traslados - Reparto | Canal Escrito';
                $referencia='7. Firma Traslados - Reparto | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado entrada o ERP</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$ceft_radicado_entrada."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado salida</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$ceft_radicado_salida."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Rechazos</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['rechazos']['texto'][$ceft_rechazos]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Forma</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$ceft_forma_correo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Proyector</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_analista[$ceft_proyector]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Inspector</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_analista[$ceft_inspector]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Abogado aprobador</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_analista[$ceft_aprobador]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$ceft_observaciones."</td>
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

  if (isset($_POST['ceft_forma'])) {
    $ceft_forma=$_POST['ceft_forma'];
  } else {
    $ceft_forma=array();
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
                            <label for="ceft_radicado_entrada" class="my-0">Radicado entrada o ERP</label>
                            <input type="text" class="form-control form-control-sm" name="ceft_radicado_entrada" id="ceft_radicado_entrada" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $ceft_radicado_entrada; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_ceft_radicado_salida">
                          <div class="form-group my-1">
                            <label for="ceft_radicado_salida" class="my-0">Radicado salida</label>
                            <input type="text" class="form-control form-control-sm" name="ceft_radicado_salida" id="ceft_radicado_salida" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $ceft_radicado_salida; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" disabled required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="ceft_rechazos" class="my-0">Rechazos</label>
                              <select class="form-control form-control-sm form-select" name="ceft_rechazos" id="ceft_rechazos" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_rechazos();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($array_parametros['rechazos']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['rechazos']['id'][$i]; ?>" <?php if($ceft_rechazos==$array_parametros['rechazos']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['rechazos']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_ceft_forma">
                          <div class="form-group my-1">
                              <label for="ceft_forma" class="my-0">Forma</label>
                              <select class="form-control form-control-sm form-select" title="Seleccione" data-live-search="false" data-container="body" name="ceft_forma[]" id="ceft_forma" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> disabled required multiple>
                                  <?php for ($i=0; $i < count($array_parametros['forma']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['forma']['id'][$i]; ?>" <?php if($ceft_forma==$array_parametros['forma']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['forma']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_ceft_proyector">
                          <div class="form-group my-1">
                            <label for="ceft_proyector" class="my-0">Proyector</label>
                            <select class="form-control form-control-sm form-select" data-live-search="true" data-container="body" name="ceft_proyector" id="ceft_proyector" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> disabled required>
                                <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                  <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($ceft_proyector==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                <?php endfor; ?>
                            </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_ceft_inspector">
                          <div class="form-group my-1">
                            <label for="ceft_inspector" class="my-0">Inspector</label>
                            <select class="form-control form-control-sm form-select" data-live-search="true" data-container="body" name="ceft_inspector" id="ceft_inspector" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> disabled required>
                                <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                  <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($ceft_inspector==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                <?php endfor; ?>
                            </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_ceft_aprobador">
                          <div class="form-group my-1">
                            <label for="ceft_aprobador" class="my-0">Abogado aprobador</label>
                            <select class="form-control form-control-sm form-select" data-live-search="true" data-container="body" name="ceft_aprobador" id="ceft_aprobador" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> disabled required>
                                <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                  <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($ceft_aprobador==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                <?php endfor; ?>
                            </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="ceft_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="ceft_observaciones" id="ceft_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $ceft_observaciones; } ?></textarea>
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
    function validar_rechazos(){
      var rechazos_opcion = document.getElementById("ceft_rechazos");
      var rechazos = rechazos_opcion.options[rechazos_opcion.selectedIndex].text;

      $("#div_ceft_radicado_salida").removeClass('d-block').addClass('d-none');
      $("#div_ceft_forma").removeClass('d-block').addClass('d-none');
      $("#div_ceft_proyector").removeClass('d-block').addClass('d-none');
      $("#div_ceft_inspector").removeClass('d-block').addClass('d-none');
      $("#div_ceft_aprobador").removeClass('d-block').addClass('d-none');
      document.getElementById('ceft_radicado_salida').disabled=true;
      document.getElementById('ceft_forma').disabled=true;
      document.getElementById('ceft_proyector').disabled=true;
      document.getElementById('ceft_inspector').disabled=true;
      document.getElementById('ceft_aprobador').disabled=true;

      if(rechazos=="Si") {
          $("#div_ceft_forma").removeClass('d-none').addClass('d-block');
          $("#div_ceft_proyector").removeClass('d-none').addClass('d-block');
          $("#div_ceft_inspector").removeClass('d-none').addClass('d-block');
          $("#div_ceft_aprobador").removeClass('d-none').addClass('d-block');
          document.getElementById('ceft_forma').disabled=false;
          document.getElementById('ceft_proyector').disabled=false;
          document.getElementById('ceft_inspector').disabled=false;
          document.getElementById('ceft_aprobador').disabled=false;
          $('#ceft_forma').selectpicker();
          $('#ceft_proyector').selectpicker();
          $('#ceft_inspector').selectpicker();
          $('#ceft_aprobador').selectpicker();
      } else if(rechazos=="No") {
          $("#div_ceft_radicado_salida").removeClass('d-none').addClass('d-block');
          document.getElementById('ceft_radicado_salida').disabled=false;
      }
    }
    validar_rechazos();
  </script>
</body>
</html>