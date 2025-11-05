<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Jóvenes Acción-Focalización";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Jóvenes en Acción y Focalización | 4. Formato de Gestión de Correos | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="jafocalizacion_gestion_correos?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='jafocalizacion_gestion_correos' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
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
      $cejgc_fecha_recibido=validar_input($_POST['cejgc_fecha_recibido']);
      $cejgc_gestion=validar_input($_POST['cejgc_gestion']);
      $cejgc_documento=validar_input($_POST['cejgc_documento']);
      $cejgc_tipo_documento=validar_input($_POST['cejgc_tipo_documento']);
      $cejgc_nombre_completo=validar_input($_POST['cejgc_nombre_completo']);
      $cejgc_codigo_beneficiario=validar_input($_POST['cejgc_codigo_beneficiario']);
      $cejgc_email=validar_input($_POST['cejgc_email']);
      $cejgc_celular=validar_input($_POST['cejgc_celular']);
      $cejgc_departamento='';
      $cejgc_municipio=validar_input($_POST['cejgc_municipio']);
      $cejgc_categoria=validar_input($_POST['cejgc_categoria']);
      $cejgc_gestion_2=validar_input($_POST['cejgc_gestion_2']);
      $cejgc_tipificacion=validar_input($_POST['cejgc_tipificacion']);
      $cejgc_carga_di=validar_input($_POST['cejgc_carga_di']);
      $cejgc_carga_soporte_bachiller=validar_input($_POST['cejgc_carga_soporte_bachiller']);
      $cejgc_observaciones=validar_input($_POST['cejgc_observaciones']);
      $cejgc_notificar=validar_input($_POST['notificar']);
      $cejgc_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cejafo_gestion_correo`(`cejgc_fecha_recibido`, `cejgc_gestion`, `cejgc_documento`, `cejgc_tipo_documento`, `cejgc_nombre_completo`, `cejgc_codigo_beneficiario`, `cejgc_email`, `cejgc_celular`, `cejgc_departamento`, `cejgc_municipio`, `cejgc_categoria`, `cejgc_gestion_2`, `cejgc_tipificacion`, `cejgc_carga_di`, `cejgc_carga_soporte_bachiller`, `cejgc_observaciones`, `cejgc_notificar`, `cejgc_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssssssssssss', $cejgc_fecha_recibido, $cejgc_gestion, $cejgc_documento, $cejgc_tipo_documento, $cejgc_nombre_completo, $cejgc_codigo_beneficiario, $cejgc_email, $cejgc_celular, $cejgc_departamento, $cejgc_municipio, $cejgc_categoria, $cejgc_gestion_2, $cejgc_tipificacion, $cejgc_carga_di, $cejgc_carga_soporte_bachiller, $cejgc_observaciones, $cejgc_notificar, $cejgc_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cejgc_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='4. Formato de Gestión de Correos - JAFocalización | Canal Escrito';
                $referencia='4. Formato de Gestión de Correos - JAFocalización | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha recibido</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_fecha_recibido."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Gestión</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_gestion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>No. documento</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_documento."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipo documento</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_tipo_documento."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nombre completo</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_nombre_completo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Código beneficiario</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_codigo_beneficiario."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Email</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_email."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Celular</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_celular."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Municipio</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_departamento[$cejgc_municipio]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Categoría</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_categoria."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Gestión</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_gestion_2."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipificación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_tipificacion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Se carga D.I?</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_carga_di."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Se carga soporte bachiller?</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_carga_soporte_bachiller."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observaciones</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgc_observaciones."</td>
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
                            <label for="cejgc_fecha_recibido" class="my-0">Fecha recibido</label>
                            <input type="date" class="form-control form-control-sm" name="cejgc_fecha_recibido" id="cejgc_fecha_recibido" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgc_fecha_recibido; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgc_gestion" class="my-0">Gestión</label>
                              <select class="form-control form-control-sm form-select" name="cejgc_gestion" id="cejgc_gestion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['gestion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['gestion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['gestion']['id'][$i]; ?>" <?php if($cejgc_gestion==$array_parametros['gestion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['gestion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgc_documento" class="my-0">No. documento</label>
                            <input type="text" class="form-control form-control-sm" name="cejgc_documento" id="cejgc_documento" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgc_documento; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgc_tipo_documento" class="my-0">Tipo documento</label>
                              <select class="form-control form-control-sm form-select" name="cejgc_tipo_documento" id="cejgc_tipo_documento" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['tipo_documento']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['tipo_documento']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['tipo_documento']['id'][$i]; ?>" <?php if($cejgc_tipo_documento==$array_parametros['tipo_documento']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['tipo_documento']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgc_nombre_completo" class="my-0">Nombre completo</label>
                            <input type="text" class="form-control form-control-sm" name="cejgc_nombre_completo" id="cejgc_nombre_completo" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgc_nombre_completo; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgc_codigo_beneficiario" class="my-0">Código beneficiario</label>
                            <input type="text" class="form-control form-control-sm" name="cejgc_codigo_beneficiario" id="cejgc_codigo_beneficiario" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgc_codigo_beneficiario; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgc_email" class="my-0">Email</label>
                            <input type="mail" class="form-control form-control-sm" name="cejgc_email" id="cejgc_email" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgc_email; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgc_celular" class="my-0">Celular</label>
                            <input type="text" class="form-control form-control-sm" name="cejgc_celular" id="cejgc_celular" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgc_celular; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgc_municipio" class="my-0">Municipio/Departamento</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cejgc_municipio" id="cejgc_municipio" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_departamentos); $i++): ?>
                                    <option class="font-size-11 py-0" value="<?php echo $resultado_registros_departamentos[$i][0]; ?>" <?php if($cejgc_municipio==$resultado_registros_departamentos[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_departamentos[$i][2].', '.$resultado_registros_departamentos[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgc_categoria" class="my-0">Categoría</label>
                              <select class="form-control form-control-sm form-select" name="cejgc_categoria" id="cejgc_categoria" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['categoria']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['categoria']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['categoria']['id'][$i]; ?>" <?php if($cejgc_categoria==$array_parametros['categoria']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['categoria']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgc_gestion_2" class="my-0">Gestión</label>
                              <select class="form-control form-control-sm form-select" name="cejgc_gestion_2" id="cejgc_gestion_2" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['gestion_2']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['gestion_2']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['gestion_2']['id'][$i]; ?>" <?php if($cejgc_gestion_2==$array_parametros['gestion_2']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['gestion_2']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgc_tipificacion" class="my-0">Tipificación</label>
                              <select class="form-control form-control-sm form-select" name="cejgc_tipificacion" id="cejgc_tipificacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['tipificacion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['tipificacion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['tipificacion']['id'][$i]; ?>" <?php if($cejgc_tipificacion==$array_parametros['tipificacion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['tipificacion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgc_carga_di" class="my-0">Se carga D.I?</label>
                            <input type="text" class="form-control form-control-sm" name="cejgc_carga_di" id="cejgc_carga_di" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgc_carga_di; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgc_carga_soporte_bachiller" class="my-0">Se carga soporte bachiller?</label>
                            <input type="text" class="form-control form-control-sm" name="cejgc_carga_soporte_bachiller" id="cejgc_carga_soporte_bachiller" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgc_carga_soporte_bachiller; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cejgc_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm height-100" name="cejgc_observaciones" id="cejgc_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cejgc_observaciones; } ?></textarea>
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
</body>
</html>