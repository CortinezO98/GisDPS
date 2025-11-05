<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Envíos WEB";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Envíos WEB";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $estado=validar_input(base64_decode($_GET['estado']));
  $subtitle = "Correo | ".$bandeja." | ".$estado.' | Reparto Individual';
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);

  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');
  $url_salir="correo?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&estado=".base64_encode($estado);
 
  $data_consulta=array();
  $filtro_agente='';
  $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE ENVÍOS WEB-TRANSVERSAL%' OR `usu_cargo_rol` LIKE '%AGENTE ENVÍOS WEB-ENTRENAMIENTO%')";
  if($bandeja=='Reparto'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Reparto');
  } elseif($bandeja=='Subsidio Familiar de Vivienda en especie'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Subsidio Familiar de Vivienda en especie');
  } elseif($bandeja=='Ingreso Solidario'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Ingreso Solidario');
  } elseif($bandeja=='Colombia Mayor'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Colombia Mayor');
  } elseif($bandeja=='Compensación del IVA'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Compensación del IVA');
  } elseif($bandeja=='Antifraudes'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Antifraudes');
  } elseif($bandeja=='Jóvenes en Acción'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Jóvenes en Acción');
  } elseif($bandeja=='Tránsito a Renta Ciudadana'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Tránsito a Renta Ciudadana');
  } elseif($bandeja=='Otros programas'){
      $filtro_bandeja=" AND `gewc_tipologia`=?";
      array_push($data_consulta, 'Otros programas');
  }

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' ".$filtro_agente." ORDER BY `usu_nombres_apellidos` ASC";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
      $gewc_responsable=validar_input($_POST['gewc_responsable']);
      $check_asignacion=$_POST['check_asignacion'];

      if (!isset($check_asignacion[0])) {
        $check_asignacion=array();
      }
      
      // echo "<pre>";
      // print_r($check_asignacion);
      // echo "</pre>";

      // Prepara la sentencia
      $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_enviosweb_casos` SET `gewc_responsable`=? WHERE `gewc_id`=?");

      // Agrega variables a sentencia preparada
      $consulta_actualizar->bind_param('ss', $gewc_responsable, $gewc_id);

      $control_agente=0;
      $control_error=0;
      for ($i=0; $i < count($check_asignacion); $i++) { 
        $gewc_id=$check_asignacion[$i];
        
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

  $consulta_string_reparto="SELECT `gewc_id`, `gewc_radicado`, `gewc_radicado_entrada`, `gewc_radicado_salida`, `gewc_tipologia`, `gewc_clasificacion`, `gewc_responsable`, `gewc_gestion`, `gewc_gestion_detalle`, `gewc_estado`, `gewc_fecha_gestion`, `gewc_correo_remitente`, `gewc_correo_asunto`, `gewc_correo_fecha`, `gewc_registro_fecha`, `gewc_registro_fecha_hora`, TAG.`usu_nombres_apellidos` FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos`.`gewc_responsable`=TAG.`usu_id` WHERE 1=1 ".$filtro_bandeja." AND `gewc_estado`='Pendiente' ORDER BY `gewc_correo_fecha`";
  $consulta_registros_reparto = $enlace_db->prepare($consulta_string_reparto);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros_reparto->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros_reparto->execute();
  $resultado_registros_reparto = $consulta_registros_reparto->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_reparto_conteo="SELECT `gewc_tipologia`, `gewc_responsable`, TU.`usu_nombres_apellidos`, COUNT(`gewc_id`) AS CANTIDAD FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TU ON `gestion_enviosweb_casos`.`gewc_responsable`=TU.`usu_id` WHERE 1=1 AND `gewc_estado`='Pendiente' ".$filtro_bandeja." GROUP BY `gewc_tipologia`, `gewc_responsable` ORDER BY `gewc_tipologia`, CANTIDAD ASC";
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
                                    <?php echo $resultado_registros_reparto[$i][4]; ?>
                                  </td>
                                  <td class="p-1 font-size-11">
                                    <?php echo $resultado_registros_reparto[$i][12]; ?>
                                  </td>
                                  <td class="p-1 font-size-11">
                                    <?php echo $resultado_registros_reparto[$i][13]; ?>
                                  </td>
                                  <td class="p-1 font-size-11">
                                    <?php echo $resultado_registros_reparto[$i][16]; ?>
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
                              <label for="gewc_responsable" class="my-0">Responsable</label>
                              <select class="form-control form-control-sm form-select" name="gewc_responsable" id="gewc_responsable" <?php if($_SESSION[APP_SESSION.'_registro_asignado_radicacion']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $gewc_responsable==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
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