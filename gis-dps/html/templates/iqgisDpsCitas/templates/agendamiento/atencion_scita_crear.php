<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas";
  $subtitle = "Atención sin Cita | Crear registro";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $url_salir="atencion_scita?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);

  if(isset($_POST["guardar_registro"])){
    $gcasc_punto=validar_input($_POST['gcasc_punto']);
    $gcasc_datos_tipo_documento='';
    $gcasc_datos_numero_identificacion='';
    $gcasc_datos_nombres='';
    $gcasc_datos_correo='';
    $gcasc_datos_celular='';
    $gcasc_datos_fijo='';
    $gcasc_datos_autoriza='';
    $gcasc_observaciones=validar_input($_POST['gcasc_observaciones']);
    $gcasc_atencion_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
    $gcasc_atencion_fecha=date('Y-m-d H:i:s');
    $gcasc_radicado=validar_input($_POST['gcasc_radicado']);
    $gcasc_atencion_preferencial='';
    $gcasc_informacion_poblacional='';
    $gcasc_genero='';
    $gcasc_nivel_escolaridad='';
    $gcasc_envio_encuesta=validar_input($_POST['gcasc_envio_encuesta']);
    $gcasc_celular=validar_input($_POST['gcasc_celular']);
    $gcasc_estado='';
    
    if($_SESSION[APP_SESSION.'_registro_creado_atencion_scita']!=1){
      // Prepara la sentencia
      $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_citas_atencion_scita`(`gcasc_punto`, `gcasc_datos_tipo_documento`, `gcasc_datos_numero_identificacion`, `gcasc_datos_nombres`, `gcasc_datos_correo`, `gcasc_datos_celular`, `gcasc_datos_fijo`, `gcasc_datos_autoriza`, `gcasc_observaciones`, `gcasc_atencion_usuario`, `gcasc_atencion_fecha`, `gcasc_radicado`, `gcasc_atencion_preferencial`, `gcasc_informacion_poblacional`, `gcasc_genero`, `gcasc_nivel_escolaridad`, `gcasc_envio_encuesta`, `gcasc_celular`, `gcasc_estado`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

      // Agrega variables a sentencia preparada
      $sentencia_insert->bind_param('sssssssssssssssssss', $gcasc_punto, $gcasc_datos_tipo_documento, $gcasc_datos_numero_identificacion, $gcasc_datos_nombres, $gcasc_datos_correo, $gcasc_datos_celular, $gcasc_datos_fijo, $gcasc_datos_autoriza, $gcasc_observaciones, $gcasc_atencion_usuario, $gcasc_atencion_fecha, $gcasc_radicado, $gcasc_atencion_preferencial, $gcasc_informacion_poblacional, $gcasc_genero, $gcasc_nivel_escolaridad, $gcasc_envio_encuesta, $gcasc_celular, $gcasc_estado);
      
      if ($sentencia_insert->execute()) {
          if ($gcasc_envio_encuesta=='Si') {
            $nsms_identificador='ASC-'.$gcasc_punto.'-'.$gcasc_radicado;
            $contenido_sms="Lo invitamos a calificar la atención recibida en el canal de atención presencial de Prosperidad Social, en el siguiente link: SHORTURL";
            $nsms_url='https://n9.cl/0elky';
            $nsms_destino=$gcasc_celular;
            $estado_notificacion_sms=notificacion_sms($enlace_db, $nsms_identificador, $nsms_destino, $contenido_sms, $nsms_url, '13');
            if ($estado_notificacion_sms) {
                $estado_sms=1;
            }
          }

          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
          $_SESSION[APP_SESSION.'_registro_creado_atencion_scita']=1;
      } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
      }
    } else {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
    }
  }

  $data_consulta_puntos=array();

  if ($permisos_usuario=="Administrador") {
      $filtro_puntos="";
  } elseif ($permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador") {
      $filtro_puntos=" AND TU.`usu_supervisor`=?";
      array_push($data_consulta_puntos, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Supervisor"){
      $filtro_puntos=" AND TU.`usu_supervisor`=?";
      array_push($data_consulta_puntos, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Usuario"){
      $filtro_puntos=" AND `gcpau_usuario`=?";
      array_push($data_consulta_puntos, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  $consulta_string_puntos="SELECT DISTINCT `gcpau_punto_atencion`, TP.`gcpa_punto_atencion` FROM `gestion_citas_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_punto_atencion_usuario`.`gcpau_punto_atencion`=TP.`gcpa_id` WHERE 1=1 ".$filtro_puntos."";
  $consulta_registros_puntos = $enlace_db->prepare($consulta_string_puntos);
  if (count($data_consulta_puntos)>0) {
      $consulta_registros_puntos->bind_param(str_repeat("s", count($data_consulta_puntos)), ...$data_consulta_puntos);
  }
  $consulta_registros_puntos->execute();
  $resultado_registros_puntos = $consulta_registros_puntos->get_result()->fetch_all(MYSQLI_NUM);
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
                      <div class="row">
                        <div class="col-md-12">
                            <div class="form-group my-1">
                                <label for="gcasc_punto">Punto de atención</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="gcasc_punto" id="gcasc_punto" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_scita']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($resultado_registros_puntos); $i++): ?>
                                    <option value="<?php echo $resultado_registros_puntos[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $gcasc_punto==$resultado_registros_puntos[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_puntos[$i][1]; ?></option>
                                  <?php endfor; ?>  
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="gcasc_radicado" class="my-0">Radicado</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="gcasc_radicado" id="gcasc_radicado" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $gcasc_radicado; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_scita']==1) { echo 'disabled'; } ?> required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                                <label for="gcasc_envio_encuesta">¿Envío SMS para realizar encuesta de satisfacción?</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="gcasc_envio_encuesta" id="gcasc_envio_encuesta" onchange="validar_sms();" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_scita']==1) { echo 'disabled'; } ?> required>
                                  <option value="">Seleccione</option>
                                  <option value="Si" <?php if(isset($_POST["guardar_registro"]) AND $gcasc_envio_encuesta=="Si"){ echo "selected"; } ?>>Si</option>
                                  <option value="No" <?php if(isset($_POST["guardar_registro"]) AND $gcasc_envio_encuesta=="No"){ echo "selected"; } ?>>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 d-none" id="gcasc_celular_div">
                            <div class="form-group my-1">
                              <label for="gcasc_celular" class="my-0">Número contacto</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="gcasc_celular" id="gcasc_celular" minlength="10" maxlength="10" value="<?php if(isset($_POST["guardar_registro"])){ echo $gcasc_celular; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_scita']==1) { echo 'disabled'; } ?> disabled autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="gcasc_observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="gcasc_observaciones" id="gcasc_observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_scita']==1) { echo 'disabled'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $gcasc_observaciones; } ?></textarea>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_scita']==1): ?>
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
    function validar_sms(){
        var gcasc_envio_encuesta_opcion = document.getElementById("gcasc_envio_encuesta");
        var gcasc_envio_encuesta = gcasc_envio_encuesta_opcion.options[gcasc_envio_encuesta_opcion.selectedIndex].value;

        if(gcasc_envio_encuesta=="Si") {
            var gcasc_celular = document.getElementById('gcasc_celular').disabled=false;
            $("#gcasc_celular_div").removeClass('d-none').addClass('d-block');
        } else {
            var gcasc_celular = document.getElementById('gcasc_celular').disabled=true;
            $("#gcasc_celular_div").removeClass('d-block').addClass('d-none');
        }
    }
    <?php if($_SESSION[APP_SESSION.'_registro_creado_atencion_scita']!=1): ?>
      validar_sms();
    <?php endif; ?>
  </script>
</body>
</html>