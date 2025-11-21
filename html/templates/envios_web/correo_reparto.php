<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Envíos WEB";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Envíos WEB";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $estado=validar_input(base64_decode($_GET['estado']));
  $subtitle = "Correo | ".$bandeja." | ".$estado.' | Reparto';
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);

  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');
  $url_salir="correo?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&estado=".base64_encode($estado);
 
  $data_consulta=array();
  if(isset($_POST["guardar_registro"])){
      $gewc_tipologia=validar_input($_POST['gewc_tipologia']);

      $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE ENVÍOS WEB-TRANSVERSAL%')";
      
      // if ($gewc_tipologia=='Notificaciones de correo') {
      // } elseif ($gewc_tipologia=='Envío Radicado a Ciudadano') {
      //   $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE ENVÍO RADICADO A CIUDADANO%')";
      //   $filtro_agente_tipologia=" AND `gewc_tipologia`='Envío Radicado a Ciudadano'";
      // } elseif ($gewc_tipologia=='Tutelas') {
      //   $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE TUTELAS%')";
      //   $filtro_agente_tipologia=" AND `gewc_tipologia`='Tutelas'";
      // } elseif ($gewc_tipologia=='Prioritario') {
      //   $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE PRIORITARIOS%')";
      //   $filtro_agente_tipologia=" AND `gewc_tipologia`='Prioritario'";
      // } elseif ($gewc_tipologia=='Funcionarios') {
      //   $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE FUNCIONARIOS%')";
      //   $filtro_agente_tipologia=" AND `gewc_tipologia`='Funcionarios'";
      // } elseif ($gewc_tipologia=='Ciudadanos') {
      //   $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE CIUDADANOS%')";
      //   $filtro_agente_tipologia=" AND `gewc_tipologia`='Ciudadanos'";
      // } elseif ($gewc_tipologia=='Soy Transparente') {
      //   $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE SOY TRANSPARENTE%')";
      //   $filtro_agente_tipologia=" AND `gewc_tipologia`='Soy Transparente'";
      // }

      $filtro_tipologia='';
      if ($gewc_tipologia<>'Todos') {
        $filtro_tipologia=' AND `gewc_tipologia`=?';
        array_push($data_consulta, $gewc_tipologia);
      }

      $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' AND `usu_reparto`='Activo' ".$filtro_agente." ORDER BY `usu_nombres_apellidos` ASC";
      $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
      $consulta_registros_analistas->execute();
      $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

      $array_agentes=array();
      for ($i=0; $i < count($resultado_registros_analistas); $i++) { 
        $array_agentes[]=$resultado_registros_analistas[$i][0];
      }

      $consulta_string="SELECT `gewc_id` FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos`.`gewc_responsable`=TAG.`usu_id` WHERE 1=1 ".$filtro_tipologia." AND ((`gewc_estado`='Pendiente' AND TAG.`usu_cargo_rol` NOT LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%') OR `gewc_responsable`='')";
      $consulta_registros = $enlace_db->prepare($consulta_string);
      if (count($data_consulta)>0) {
          // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
          $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
      }
      $consulta_registros->execute();
      $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

      // echo "<pre>";
      // print_r($array_agentes);
      // echo "</pre>";

      shuffle($array_agentes);
      $cantidad_reparto=round((count($resultado_registros)/count($array_agentes)), 0, PHP_ROUND_HALF_DOWN);
      
      // Prepara la sentencia
      $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_enviosweb_casos` SET `gewc_responsable`=? WHERE `gewc_id`=?");

      // Agrega variables a sentencia preparada
      $consulta_actualizar->bind_param('ss', $gewc_responsable, $gewc_id);

      $control_agente=0;
      $control_error=0;
      for ($i=0; $i < count($resultado_registros); $i++) { 
        $gewc_id=$resultado_registros[$i][0];
        $gewc_responsable=$array_agentes[$control_agente];
        
        // Ejecuta sentencia preparada
        $consulta_actualizar->execute();

        if (!comprobarSentencia($enlace_db->info)) {
          $control_error++;
        }

        if ($control_agente==count($array_agentes)-1) {
          $control_agente=0;
        } else {
          $control_agente++;
        }
      }

      if ($control_error==0) {
          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
      } else {
          $respuesta_accion = "alertButton('error', 'Error', 'Problemas al actualizar el registro');";
      }
  }

  $consulta_string_reparto="SELECT `gewc_tipologia`, `gewc_responsable`, TU.`usu_nombres_apellidos`, COUNT(`gewc_id`) AS CANTIDAD FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TU ON `gestion_enviosweb_casos`.`gewc_responsable`=TU.`usu_id` WHERE 1=1 AND `gewc_estado`='Pendiente' GROUP BY `gewc_tipologia`, `gewc_responsable` ORDER BY `gewc_tipologia`, CANTIDAD ASC";
  $consulta_registros_reparto = $enlace_db->prepare($consulta_string_reparto);
  $consulta_registros_reparto->execute();
  $resultado_registros_reparto = $consulta_registros_reparto->get_result()->fetch_all(MYSQLI_NUM);
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
                                  <th class="px-1 py-2">Tipología</th>
                                  <th class="px-1 py-2">Responsable</th>
                                  <th class="px-1 py-2">Cantidad</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php for ($i=0; $i < count($resultado_registros_reparto); $i++): ?>
                                <tr>
                                  <td class="p-1 font-size-11 text-center">
                                    <?php echo $resultado_registros_reparto[$i][0]; ?>
                                  </td>
                                  <td class="p-1 font-size-11 text-center">
                                    <?php echo $resultado_registros_reparto[$i][2]; ?>
                                  </td>
                                  <td class="p-1 font-size-11">
                                    <?php echo $resultado_registros_reparto[$i][3]; ?>
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
                          <div class="form-group my-2">
                              <label for="gewc_tipologia" class="my-0">Tipología</label>
                              <select class="form-control form-control-sm form-select" name="gewc_tipologia" id="gewc_tipologia" <?php if($_SESSION[APP_SESSION.'_registro_asignado_radicacion']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <option value="Reparto" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Reparto"){ echo "selected"; } ?>>Reparto</option>
                                  <option value="Subsidio Familiar de Vivienda en especie" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Subsidio Familiar de Vivienda en especie"){ echo "selected"; } ?>>Subsidio Familiar de Vivienda en especie</option>
                                  <option value="Ingreso Solidario" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Ingreso Solidario"){ echo "selected"; } ?>>Ingreso Solidario</option>
                                  <option value="Colombia Mayor" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Colombia Mayor"){ echo "selected"; } ?>>Colombia Mayor</option>
                                  <option value="Compensación del IVA" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Compensación del IVA"){ echo "selected"; } ?>>Compensación del IVA</option>
                                  <option value="Antifraudes" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Antifraudes"){ echo "selected"; } ?>>Antifraudes</option>
                                  <option value="Jóvenes en Acción" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Jóvenes en Acción"){ echo "selected"; } ?>>Jóvenes en Acción</option>
                                  <option value="Tránsito a Renta Ciudadana" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Tránsito a Renta Ciudadana"){ echo "selected"; } ?>>Tránsito a Renta Ciudadana</option>
                                  <option value="Otros programas" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Otros programas"){ echo "selected"; } ?>>Otros programas</option>
                                  <option value="Todos" <?php if(isset($_POST["guardar_registro"]) AND $gewc_tipologia=="Todos"){ echo "selected"; } ?>>Todos</option>
                                  
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