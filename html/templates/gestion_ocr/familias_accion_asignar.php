<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Gestión";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Gestión | Asignar";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $filtro_estado_permanente=validar_input($_GET['estado']);

  if ($filtro_estado_permanente!='null') {
      $filtro_estado_permanente=unserialize($_GET['estado']);
  } else {
      $filtro_estado_permanente=array();
  }

  $estado_url=serialize($filtro_estado_permanente);
  $estado_url=urlencode($estado_url);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="familias_accion?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&estado=".$estado_url;

  $consulta_string_caso="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_observaciones`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TAG.`usu_nombres_apellidos`, TOCR.`ocr_telefono`, TOCR.`ocr_celular`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio`, TOCR.`ocr_correo` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` WHERE `ocrr_id`=?";
  $consulta_registros_caso = $enlace_db->prepare($consulta_string_caso);
  $consulta_registros_caso->bind_param("s", $id_registro);
  $consulta_registros_caso->execute();
  $resultado_registros_caso = $consulta_registros_caso->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
      $responsable=validar_input($_POST['responsable']);
      $observaciones='Reasignar caso Id: '.$responsable;

      if($_SESSION[APP_SESSION.'_registro_creado_familias_accion_asignar']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado_avances`(`gora_codfamilia`, `gora_estado`, `gora_correo`, `gora_observaciones`, `gora_llamada_tipificacion`, `gora_llamada_id`, `gora_registro_usuario`) VALUES (?,'Reasignado','',?,'','',?)");

          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sss', $resultado_registros_caso[0][1], $observaciones, $_SESSION[APP_SESSION.'_session_usu_id']);
          
          if ($sentencia_insert->execute()) {
              // Prepara la sentencia
              $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_ocr_resultado` SET `ocrr_gestion_agente`=? WHERE `ocrr_id`=?");

              // Agrega variables a sentencia preparada
              $consulta_actualizar->bind_param('ss', $responsable, $id_registro);
              
              // Ejecuta sentencia preparada
              $consulta_actualizar->execute();

              if (comprobarSentencia($enlace_db->info)) {
                  $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
                  $_SESSION[APP_SESSION.'_registro_creado_familias_accion_asignar']=1;
              } else {
                  $respuesta_accion = "alertButton('error', 'Error', 'Problemas al actualizar el registro');";
              }

          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string_caso="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_observaciones`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TAG.`usu_nombres_apellidos`, TOCR.`ocr_telefono`, TOCR.`ocr_celular`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio`, TOCR.`ocr_correo` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` WHERE `ocrr_id`=?";
  $consulta_registros_caso = $enlace_db->prepare($consulta_string_caso);
  $consulta_registros_caso->bind_param("s", $id_registro);
  $consulta_registros_caso->execute();
  $resultado_registros_caso = $consulta_registros_caso->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' AND `usu_cargo_rol`='AGENTE INSCRIPCIÓN FA' ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);
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
                    <div class="card-body">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="cod_familia" class="my-0">Cod. Familia</label>
                          <input type="text" class="form-control form-control-sm" name="cod_familia" id="cod_familia" value="<?php echo$resultado_registros_caso[0][1]; ?>" readonly>
                        </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="responsable" class="my-0">Responsable</label>
                              <select class="form-control form-control-sm form-select" name="responsable" id="responsable" <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion_asignar']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($resultado_registros_analistas[$i][0]==$resultado_registros_caso[0][5]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion_asignar']==1): ?>
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