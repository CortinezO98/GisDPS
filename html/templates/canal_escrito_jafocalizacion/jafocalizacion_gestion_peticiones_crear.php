<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Jóvenes Acción-Focalización";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Jóvenes en Acción y Focalización | 6. Formato de Gestión de Peticiones JeA | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="jafocalizacion_gestion_peticiones?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='jafocalizacion_gestion_peticiones' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
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

  $consulta_string_departamentos="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_municipio`, `ciu_departamento`";
  $consulta_registros_departamentos = $enlace_db->prepare($consulta_string_departamentos);
  $consulta_registros_departamentos->execute();
  $resultado_registros_departamentos = $consulta_registros_departamentos->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_departamentos); $i++) { 
    $array_departamento[$resultado_registros_departamentos[$i][0]]=$resultado_registros_departamentos[$i][2].', '.$resultado_registros_departamentos[$i][1];
  }

  if(isset($_POST["guardar_registro"])){
      $cejgp_radicado=validar_input($_POST['cejgp_radicado']);
      $cejgp_proyector='';
      $cejgp_aprobador=validar_input($_POST['cejgp_aprobador']);
      $cejgp_peticionario_identificacion=validar_input($_POST['cejgp_peticionario_identificacion']);
      $cejgp_peticionario_nombres=validar_input($_POST['cejgp_peticionario_nombres']);
      $cejgp_correo_direccion=validar_input($_POST['cejgp_correo_direccion']);
      $cejgp_municipio=validar_input($_POST['cejgp_municipio']);
      $cejgp_solicitud=validar_input($_POST['cejgp_solicitud']);
      $cejgp_no_registra_sija=validar_input($_POST['cejgp_no_registra_sija']);
      $cejgp_tipo_documento=validar_input($_POST['cejgp_tipo_documento']);
      $cejgp_fecha_nacimiento_solicitante=validar_input($_POST['cejgp_fecha_nacimiento_solicitante']);
      $cejgp_novedad=validar_input($_POST['cejgp_novedad']);
      $cejgp_no_radicado=validar_input($_POST['cejgp_no_radicado']);
      $cejgp_novedad_adicional=validar_input($_POST['cejgp_novedad_adicional']);
      $cejgp_codigo_beneficiario=validar_input($_POST['cejgp_codigo_beneficiario']);
      $cejgp_gestion_actualizacion=validar_input($_POST['cejgp_gestion_actualizacion']);
      $cejgp_institucion_estudia=validar_input($_POST['cejgp_institucion_estudia']);
      $cejgp_nivel_formacion=validar_input($_POST['cejgp_nivel_formacion']);
      $cejgp_convenio=validar_input($_POST['cejgp_convenio']);
      $cejgp_observacion_actualizacion=validar_input($_POST['cejgp_observacion_actualizacion']);
      $cejgp_codigo_beneficiario_caso_especial=validar_input($_POST['cejgp_codigo_beneficiario_caso_especial']);
      $cejgp_municipio_reporte=validar_input($_POST['cejgp_municipio_reporte']);
      $cejgp_observacion_caso_especial=validar_input($_POST['cejgp_observacion_caso_especial']);
      $cejgp_observaciones='';
      $cejgp_notificar=validar_input($_POST['notificar']);
      $cejgp_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      
      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cejafo_gestion_peticiones`(`cejgp_radicado`, `cejgp_proyector`, `cejgp_aprobador`, `cejgp_peticionario_identificacion`, `cejgp_peticionario_nombres`, `cejgp_correo_direccion`, `cejgp_municipio`, `cejgp_solicitud`, `cejgp_no_registra_sija`, `cejgp_tipo_documento`, `cejgp_fecha_nacimiento_solicitante`, `cejgp_novedad`, `cejgp_no_radicado`, `cejgp_novedad_adicional`, `cejgp_codigo_beneficiario`, `cejgp_gestion_actualizacion`, `cejgp_institucion_estudia`, `cejgp_nivel_formacion`, `cejgp_convenio`, `cejgp_observacion_actualizacion`, `cejgp_codigo_beneficiario_caso_especial`, `cejgp_municipio_reporte`, `cejgp_observacion_caso_especial`, `cejgp_observaciones`, `cejgp_notificar`, `cejgp_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssssssssssssssssssss', $cejgp_radicado, $cejgp_proyector, $cejgp_aprobador, $cejgp_peticionario_identificacion, $cejgp_peticionario_nombres, $cejgp_correo_direccion, $cejgp_municipio, $cejgp_solicitud, $cejgp_no_registra_sija, $cejgp_tipo_documento, $cejgp_fecha_nacimiento_solicitante, $cejgp_novedad, $cejgp_no_radicado, $cejgp_novedad_adicional, $cejgp_codigo_beneficiario, $cejgp_gestion_actualizacion, $cejgp_institucion_estudia, $cejgp_nivel_formacion, $cejgp_convenio, $cejgp_observacion_actualizacion, $cejgp_codigo_beneficiario_caso_especial, $cejgp_municipio_reporte, $cejgp_observacion_caso_especial, $cejgp_observaciones, $cejgp_notificar, $cejgp_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cejgp_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='6. Formato de Gestión de Peticiones - JAFocalización | Canal Escrito';
                $referencia='6. Formato de Gestión de Peticiones - JAFocalización | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>No. de radicado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_radicado."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Aprobador</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_analista[$cejgp_aprobador]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Número de identificación Peticionario - si no tiene colocar NO REGISTRA</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_peticionario_identificacion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nombres y apellidos peticionario</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_peticionario_nombres."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Email ciudadano/Dirección</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_correo_direccion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Municipio/Departamento</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_departamento[$cejgp_municipio]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Solicitud</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgp_solicitud']['texto'][$cejgp_solicitud]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>No registra en Sija potencial</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgp_no_registra_sija']['texto'][$cejgp_no_registra_sija]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipo de documento</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgp_tipo_documento']['texto'][$cejgp_tipo_documento]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha nacimiento solicitante</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_fecha_nacimiento_solicitante."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Novedad</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgp_novedad']['texto'][$cejgp_novedad]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>No. radicado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_no_radicado."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Novedad adicional</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgp_novedad_adicional']['texto'][$cejgp_novedad_adicional]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Código beneficiario</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_codigo_beneficiario."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Gestión actualización</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgp_gestion_actualizacion']['texto'][$cejgp_gestion_actualizacion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Institución donde estudia</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgp_institucion_estudia']['texto'][$cejgp_institucion_estudia]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Nivel de formación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['cejgp_nivel_formacion']['texto'][$cejgp_nivel_formacion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Convenio (Diligenciar el nombre completo de la nueva IES)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_convenio."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observación actualización pendiente</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_observacion_actualizacion."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Código de beneficiario (aplica para casos especiales)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_codigo_beneficiario_caso_especial."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Municipio/Departamento de reporte(aplica para casos especiales)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_departamento[$cejgp_municipio_reporte]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Observación caso especial</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cejgp_observacion_caso_especial."</td>
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
                            <label for="cejgp_radicado" class="my-0">No. de radicado</label>
                            <input type="text" class="form-control form-control-sm" name="cejgp_radicado" id="cejgp_radicado" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgp_radicado; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_aprobador" class="my-0">Aprobador</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cejgp_aprobador" id="cejgp_aprobador" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($cejgp_aprobador==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgp_peticionario_identificacion" class="my-0">Número de identificación Peticionario - si no tiene colocar NO REGISTRA</label>
                            <input type="text" class="form-control form-control-sm" name="cejgp_peticionario_identificacion" id="cejgp_peticionario_identificacion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgp_peticionario_identificacion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgp_peticionario_nombres" class="my-0">Nombres y apellidos peticionario</label>
                            <input type="text" class="form-control form-control-sm" name="cejgp_peticionario_nombres" id="cejgp_peticionario_nombres" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgp_peticionario_nombres; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgp_correo_direccion" class="my-0">Email ciudadano/Dirección</label>
                            <input type="text" class="form-control form-control-sm" name="cejgp_correo_direccion" id="cejgp_correo_direccion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgp_correo_direccion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_municipio" class="my-0">Municipio/Departamento</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cejgp_municipio" id="cejgp_municipio" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_departamentos); $i++): ?>
                                    <option class="font-size-11 py-0" value="<?php echo $resultado_registros_departamentos[$i][0]; ?>" <?php if($cejgp_municipio==$resultado_registros_departamentos[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_departamentos[$i][2].', '.$resultado_registros_departamentos[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_solicitud" class="my-0">Solicitud</label>
                              <select class="form-control form-control-sm form-select" name="cejgp_solicitud" id="cejgp_solicitud" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgp_solicitud']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgp_solicitud']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgp_solicitud']['id'][$i]; ?>" <?php if($cejgp_solicitud==$array_parametros['cejgp_solicitud']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgp_solicitud']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_no_registra_sija" class="my-0">No registra en Sija potencial</label>
                              <select class="form-control form-control-sm form-select" name="cejgp_no_registra_sija" id="cejgp_no_registra_sija" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgp_no_registra_sija']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgp_no_registra_sija']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgp_no_registra_sija']['id'][$i]; ?>" <?php if($cejgp_no_registra_sija==$array_parametros['cejgp_no_registra_sija']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgp_no_registra_sija']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_tipo_documento" class="my-0">Tipo de documento</label>
                              <select class="form-control form-control-sm form-select" name="cejgp_tipo_documento" id="cejgp_tipo_documento" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgp_tipo_documento']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgp_tipo_documento']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgp_tipo_documento']['id'][$i]; ?>" <?php if($cejgp_tipo_documento==$array_parametros['cejgp_tipo_documento']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgp_tipo_documento']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgp_fecha_nacimiento_solicitante" class="my-0">Fecha nacimiento solicitante</label>
                            <input type="date" class="form-control form-control-sm" name="cejgp_fecha_nacimiento_solicitante" id="cejgp_fecha_nacimiento_solicitante" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgp_fecha_nacimiento_solicitante; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_novedad" class="my-0">Novedad</label>
                              <select class="form-control form-control-sm form-select" name="cejgp_novedad" id="cejgp_novedad" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgp_novedad']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgp_novedad']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgp_novedad']['id'][$i]; ?>" <?php if($cejgp_novedad==$array_parametros['cejgp_novedad']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgp_novedad']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgp_no_radicado" class="my-0">No. radicado</label>
                            <input type="text" class="form-control form-control-sm" name="cejgp_no_radicado" id="cejgp_no_radicado" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgp_no_radicado; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_novedad_adicional" class="my-0">Novedad adicional</label>
                              <select class="form-control form-control-sm form-select" name="cejgp_novedad_adicional" id="cejgp_novedad_adicional" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgp_novedad_adicional']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgp_novedad_adicional']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgp_novedad_adicional']['id'][$i]; ?>" <?php if($cejgp_novedad_adicional==$array_parametros['cejgp_novedad_adicional']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgp_novedad_adicional']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgp_codigo_beneficiario" class="my-0">Código beneficiario actualización pendiente</label>
                            <input type="text" class="form-control form-control-sm" name="cejgp_codigo_beneficiario" id="cejgp_codigo_beneficiario" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgp_codigo_beneficiario; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_gestion_actualizacion" class="my-0">Gestión actualización pendiente</label>
                              <select class="form-control form-control-sm form-select" name="cejgp_gestion_actualizacion" id="cejgp_gestion_actualizacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgp_gestion_actualizacion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgp_gestion_actualizacion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgp_gestion_actualizacion']['id'][$i]; ?>" <?php if($cejgp_gestion_actualizacion==$array_parametros['cejgp_gestion_actualizacion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgp_gestion_actualizacion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_institucion_estudia" class="my-0">Institución donde estudia (Casos cambio de formación académica)</label>
                              <select class="form-control form-control-sm form-select" name="cejgp_institucion_estudia" id="cejgp_institucion_estudia" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgp_institucion_estudia']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgp_institucion_estudia']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgp_institucion_estudia']['id'][$i]; ?>" <?php if($cejgp_institucion_estudia==$array_parametros['cejgp_institucion_estudia']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgp_institucion_estudia']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_nivel_formacion" class="my-0">Nivel de formación (Casos cambio de formación académica)</label>
                              <select class="form-control form-control-sm form-select" name="cejgp_nivel_formacion" id="cejgp_nivel_formacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['cejgp_nivel_formacion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['cejgp_nivel_formacion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['cejgp_nivel_formacion']['id'][$i]; ?>" <?php if($cejgp_nivel_formacion==$array_parametros['cejgp_nivel_formacion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['cejgp_nivel_formacion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgp_convenio" class="my-0">Convenio (Diligenciar el nombre completo de la nueva IES)</label>
                            <input type="text" class="form-control form-control-sm" name="cejgp_convenio" id="cejgp_convenio" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgp_convenio; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cejgp_observacion_actualizacion" class="my-0">Observación actualización pendiente</label>
                            <textarea class="form-control form-control-sm height-100" name="cejgp_observacion_actualizacion" id="cejgp_observacion_actualizacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cejgp_observacion_actualizacion; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cejgp_codigo_beneficiario_caso_especial" class="my-0">Código de beneficiario (aplica para casos especiales)</label>
                            <input type="text" class="form-control form-control-sm" name="cejgp_codigo_beneficiario_caso_especial" id="cejgp_codigo_beneficiario_caso_especial" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $cejgp_codigo_beneficiario_caso_especial; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cejgp_municipio_reporte" class="my-0">Municipio/Departamento de reporte(aplica para casos especiales)</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cejgp_municipio_reporte" id="cejgp_municipio_reporte" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <?php for ($i=0; $i < count($resultado_registros_departamentos); $i++): ?>
                                    <option class="font-size-11 py-0" value="<?php echo $resultado_registros_departamentos[$i][0]; ?>" <?php if($cejgp_municipio_reporte==$resultado_registros_departamentos[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_departamentos[$i][2].', '.$resultado_registros_departamentos[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cejgp_observacion_caso_especial" class="my-0">Observación caso especial</label>
                            <textarea class="form-control form-control-sm height-100" name="cejgp_observacion_caso_especial" id="cejgp_observacion_caso_especial" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cejgp_observacion_caso_especial; } ?></textarea>
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
        jQuery("#cejgp_radicado").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[ ]/g, ''));
        });
    });
    jQuery(document).ready(function(){
        jQuery("#cejgp_peticionario_identificacion").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });
    jQuery(document).ready(function(){
        jQuery("#cejgp_no_radicado").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[ ]/g, ''));
        });
    });
    jQuery(document).ready(function(){
        jQuery("#cejgp_codigo_beneficiario_caso_especial").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });

    jQuery(document).ready(function(){
        jQuery("#cejgp_codigo_beneficiario").on('input', function (evt) {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
        });
    });
  </script>
</body>
</html>