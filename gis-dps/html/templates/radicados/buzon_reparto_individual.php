<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Radicación";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Radicación";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $estado=validar_input(base64_decode($_GET['estado']));
  $subtitle = "Buzón | ".$bandeja." | ".$estado.' | Reparto';
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);

  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');
  $url_salir="buzon?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&estado=".base64_encode($estado);
 
  $data_consulta=array();
  $filtro_agente='';

  if($bandeja=="Prioritarios"){
      $filtro_bandeja=" AND `grc_tipologia`=?";
      array_push($data_consulta, 'Prioritario');
      $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE PRIORITARIOS%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
  } elseif($bandeja=="Soy Transparente"){
      $filtro_bandeja=" AND `grc_tipologia`=?";
      array_push($data_consulta, 'Soy Transparente');
      $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE SOY TRANSPARENTE%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
  } elseif($bandeja=="Funcionarios"){
      $filtro_bandeja=" AND `grc_tipologia`=?";
      array_push($data_consulta, 'Funcionarios');
      $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE FUNCIONARIOS%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
  } elseif($bandeja=="Ciudadanos"){
      $filtro_bandeja=" AND `grc_tipologia`=?";
      array_push($data_consulta, 'Ciudadanos');
      $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE CIUDADANOS%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
  } elseif($bandeja=="Envío Radicado"){
      $filtro_bandeja=" AND `grc_tipologia`=?";
      array_push($data_consulta, 'Envío Radicado a Ciudadano');
      $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE ENVÍO RADICADO A CIUDADANO%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
  } elseif($bandeja=="Tutelas"){
      $filtro_bandeja=" AND `grc_tipologia`=?";
      array_push($data_consulta, 'Tutelas');
      $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE TUTELAS%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
  } elseif($bandeja=="Notificaciones Correo"){
      $filtro_bandeja=" AND `grc_tipologia`=?";
      array_push($data_consulta, 'Notificaciones de correo');
      $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE NOTIFICACIONES DE CORREO%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
  }

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' ".$filtro_agente." ORDER BY `usu_nombres_apellidos` ASC";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
      $grc_responsable=validar_input($_POST['grc_responsable']);
      $check_asignacion=$_POST['check_asignacion'];

      if (!isset($check_asignacion[0])) {
        $check_asignacion=array();
      }
      
      // echo "<pre>";
      // print_r($check_asignacion);
      // echo "</pre>";

      // Prepara la sentencia
      $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_radicacion_casos` SET `grc_responsable`=? WHERE `grc_id`=?");

      // Agrega variables a sentencia preparada
      $consulta_actualizar->bind_param('ss', $grc_responsable, $grc_id);

      $control_agente=0;
      $control_error=0;
      for ($i=0; $i < count($check_asignacion); $i++) { 
        $grc_id=$check_asignacion[$i];
        
        // Ejecuta sentencia preparada
        $consulta_actualizar->execute();

        if (!comprobarSentencia($enlace_db->info)) {
          $control_error++;
        }
      }

      if ($control_error==0) {
          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
      } else {
          $respuesta_accion = "alertButton('error', 'Error', 'Problemas al actualizar el registro');";
      }
  }

  $consulta_string_reparto="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`, `grc_registro_fecha_hora`, TAG.`usu_nombres_apellidos` FROM `gestion_radicacion_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos`.`grc_responsable`=TAG.`usu_id` WHERE 1=1 ".$filtro_bandeja." AND `grc_estado`='Pendiente' ORDER BY `grc_correo_fecha`";
  $consulta_registros_reparto = $enlace_db->prepare($consulta_string_reparto);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros_reparto->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros_reparto->execute();
  $resultado_registros_reparto = $consulta_registros_reparto->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_reparto_conteo="SELECT `grc_tipologia`, `grc_responsable`, TU.`usu_nombres_apellidos`, COUNT(`grc_id`) AS CANTIDAD FROM `gestion_radicacion_casos` LEFT JOIN `administrador_usuario` AS TU ON `gestion_radicacion_casos`.`grc_responsable`=TU.`usu_id` WHERE 1=1 AND `grc_estado`='Pendiente' ".$filtro_bandeja." GROUP BY `grc_tipologia`, `grc_responsable` ORDER BY `grc_tipologia`, CANTIDAD ASC";
  $consulta_registros_reparto_conteo = $enlace_db->prepare($consulta_string_reparto_conteo);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros_reparto_conteo->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros_reparto_conteo->execute();
  $resultado_registros_reparto_conteo = $consulta_registros_reparto_conteo->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-lg-8 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-lg-12 mt-2">
                          <div class="table-responsive table-fixed" id="headerFixTable">
                            <table class="table table-hover table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2"></th>
                                  <th class="px-1 py-2">Consecutivo</th>
                                  <th class="px-1 py-2">Tipología</th>
                                  <th class="px-1 py-2">Asunto</th>
                                  <th class="px-1 py-2">Fecha/Hora</th>
                                  <th class="px-1 py-2">Responsable</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php for ($i=0; $i < count($resultado_registros_reparto); $i++): ?>
                                <tr>
                                  <td class="p-1 font-size-11 text-center">
                                    <div class="form-group m-0">
                                        <div class="form-group custom-control custom-checkbox m-0">
                                            <input type="checkbox" class="custom-control-input" id="customCheckasignacion_<?php echo $resultado_registros_reparto[$i][0]; ?>" name="check_asignacion[]" value="<?php echo $resultado_registros_reparto[$i][0]; ?>">
                                        </div>
                                    </div>
                                  </td>
                                  <td class="p-1 font-size-11 text-center">
                                    <?php echo $resultado_registros_reparto[$i][1]; ?>
                                  </td>
                                  <td class="p-1 font-size-11 text-center">
                                    <?php echo $resultado_registros_reparto[$i][2]; ?>
                                  </td>
                                  <td class="p-1 font-size-11">
                                    <?php echo $resultado_registros_reparto[$i][15]; ?>
                                  </td>
                                  <td class="p-1 font-size-11">
                                    <?php echo $resultado_registros_reparto[$i][18]; ?>
                                  </td>
                                  <td class="p-1 font-size-11">
                                    <?php echo $resultado_registros_reparto[$i][19]; ?>
                                  </td>
                                </tr>
                                <?php endfor; ?>
                              </tbody>
                            </table>
                            <?php if(count($resultado_registros_reparto)==0): ?>
                              <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="col-md-12">
                          <div class="table-responsive table-fixed" id="headerFixTable">
                            <table class="table table-hover table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Tipología</th>
                                  <th class="px-1 py-2">Responsable</th>
                                  <th class="px-1 py-2">Cantidad</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php for ($i=0; $i < count($resultado_registros_reparto_conteo); $i++): ?>
                                <tr>
                                  <td class="p-1 font-size-11 text-center">
                                    <?php echo $resultado_registros_reparto_conteo[$i][0]; ?>
                                  </td>
                                  <td class="p-1 font-size-11 text-center">
                                    <?php echo $resultado_registros_reparto_conteo[$i][2]; ?>
                                  </td>
                                  <td class="p-1 font-size-11">
                                    <?php echo $resultado_registros_reparto_conteo[$i][3]; ?>
                                  </td>
                                </tr>
                                <?php endfor; ?>
                              </tbody>
                            </table>
                            <?php if(count($resultado_registros_reparto_conteo)==0): ?>
                              <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
                            <?php endif; ?>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-2">
                              <label for="grc_responsable" class="my-0">Responsable</label>
                              <select class="form-control form-control-sm form-select" name="grc_responsable" id="grc_responsable" <?php if($_SESSION[APP_SESSION.'_registro_asignado_radicacion']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $grc_responsable==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Guardar</button>
                                <?php if(isset($_POST["guardar_registro"])): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php endif; ?>
                                <?php if(!isset($_POST["guardar_registro"])): ?>
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