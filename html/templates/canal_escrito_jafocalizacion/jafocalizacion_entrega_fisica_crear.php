<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Jóvenes Acción-Focalización";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Jóvenes en Acción y Focalización | 8. Formato Entrega Física | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="jafocalizacion_entrega_fisica?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='jafocalizacion_entrega_fisica' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }

  $consulta_string_departamentos="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_municipio`, `ciu_departamento`";
  $consulta_registros_departamentos = $enlace_db->prepare($consulta_string_departamentos);
  $consulta_registros_departamentos->execute();
  $resultado_registros_departamentos = $consulta_registros_departamentos->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_departamentos); $i++) { 
    $array_departamento[$resultado_registros_departamentos[$i][0]]=$resultado_registros_departamentos[$i][2].', '.$resultado_registros_departamentos[$i][1];
  }

  if(isset($_POST["guardar_registro"])){
      $cejef_radicado_salida=validar_input($_POST['cejef_radicado_salida']);
      $cejef_radicado_entrada=validar_input($_POST['cejef_radicado_entrada']);
      $cejef_destinatario=validar_input($_POST['cejef_destinatario']);
      $cejef_direccion=validar_input($_POST['cejef_direccion']);
      $cejef_departamento='';
      $cejef_municipio=validar_input($_POST['cejef_municipio']);
      $cejef_observaciones=validar_input($_POST['cejef_observaciones']);
      $cejef_notificar=validar_input($_POST['notificar']);
      $cejef_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cejafo_entrega_fisica`(`cejef_radicado_salida`, `cejef_radicado_entrada`, `cejef_destinatario`, `cejef_direccion`, `cejef_departamento`, `cejef_municipio`, `cejef_observaciones`, `cejef_notificar`, `cejef_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssssss', $cejef_radicado_salida, $cejef_radicado_entrada, $cejef_destinatario, $cejef_direccion, $cejef_departamento, $cejef_municipio, $cejef_observaciones, $cejef_notificar, $cejef_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cejef_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='8. Formato Entrega Física - JAFocalización | Canal Escrito';
                $referencia='8. Formato Entrega Física - JAFocalización | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado salida</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejef_radicado_salida."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado entrada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejef_radicado_entrada."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Destinatario</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejef_destinatario."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Dirección</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejef_direccion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Municipio</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_departamento[$cejef_municipio]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejef_observaciones."</td>
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
                            <label for="cejef_radicado_salida" class="my-0">Radicado salida</label>
                            <input type="text" class="form-control form-control-sm" name="cejef_radicado_salida" id="cejef_radicado_salida" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejef_radicado_salida; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejef_radicado_entrada" class="my-0">Radicado entrada</label>
                            <input type="text" class="form-control form-control-sm" name="cejef_radicado_entrada" id="cejef_radicado_entrada" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejef_radicado_entrada; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejef_destinatario" class="my-0">Destinatario</label>
                            <input type="text" class="form-control form-control-sm" name="cejef_destinatario" id="cejef_destinatario" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejef_destinatario; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejef_direccion" class="my-0">Dirección</label>
                            <input type="text" class="form-control form-control-sm" name="cejef_direccion" id="cejef_direccion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejef_direccion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejef_municipio" class="my-0">Municipio/Departamento</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cejef_municipio" id="cejef_municipio" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_departamentos); $i++): ?>
                                    <option class="font-size-11 py-0" value="<?php echo $resultado_registros_departamentos[$i][0]; ?>" <?php if($cejef_municipio==$resultado_registros_departamentos[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_departamentos[$i][2].', '.$resultado_registros_departamentos[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cejef_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cejef_observaciones" id="cejef_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cejef_observaciones; } ?></textarea>
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
        jQuery("#cejef_radicado_salida").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[ ]/g, ''));
        });
    });
    jQuery(document).ready(function(){
        jQuery("#cejef_radicado_entrada").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[ ]/g, ''));
        });
    });
  </script>
</body>
</html>