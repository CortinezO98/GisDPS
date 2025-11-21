<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Envíos WEB";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');

  /*VARIABLES*/
  $title = "Envíos WEB";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $estado=validar_input(base64_decode($_GET['estado']));
  $subtitle = "Correo | Estadísticas";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  // Inicializa variable tipo array
  $data_consulta=array();
  $data_consulta_anio=array();

  $array_anio_mes_dias_num=array();
  $array_gestion_diaria=array();
  
  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_permanente=validar_input($_POST['id_filtro']);
      $filtro_mes=" AND `gewc_correo_fecha` LIKE ?";
      $anio_consulta=date('Y', strtotime($filtro_permanente));
      array_push($data_consulta, "$filtro_permanente%");
      array_push($data_consulta_anio, "$anio_consulta%");
  } else {
      $filtro_permanente=date('Y-m');
      $filtro_mes=" AND `gewc_correo_fecha` LIKE ?";
      $anio_consulta=date('Y', strtotime($filtro_permanente));
      array_push($data_consulta, "$filtro_permanente%");
      array_push($data_consulta_anio, "$anio_consulta%");
  }

  //CONSTRUIR ARRAY AÑO-MES-DIA
      $anio_mes_separado=explode("-", $filtro_permanente);
      $numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, $anio_mes_separado[1], $anio_mes_separado[0]); //cantidad de días del mes
      for ($k=1; $k <= $numero_dias_mes; $k++) { 
          $array_anio_mes_dias_num[]=validar_cero($k);
          $fecha_dia=$filtro_permanente."-".validar_cero($k);
          $array_anio_mes_dias[] = $fecha_dia;
      }

  
  $array_mes_gestion[]='01';
  $array_mes_gestion[]='02';
  $array_mes_gestion[]='03';
  $array_mes_gestion[]='04';
  $array_mes_gestion[]='05';
  $array_mes_gestion[]='06';
  $array_mes_gestion[]='07';
  $array_mes_gestion[]='08';
  $array_mes_gestion[]='09';
  $array_mes_gestion[]='10';
  $array_mes_gestion[]='11';
  $array_mes_gestion[]='12';

  //CONSULTA GRÁFICA GESTIÓN y RESULTADO INDICADORES
    $consulta_string_diaria="SELECT DATE(`gewc_correo_fecha`) AS FECHA, `gewc_estado`, count(`gewc_estado`) FROM `gestion_enviosweb_casos` WHERE 1=1 ".$filtro_mes." GROUP BY FECHA, `gewc_estado`";
    $consulta_registros_diaria = $enlace_db->prepare($consulta_string_diaria);
    $consulta_registros_diaria->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $consulta_registros_diaria->execute();
    $resultado_registros_diaria = $consulta_registros_diaria->get_result()->fetch_all(MYSQLI_NUM);

    $array_gestion_diaria['Total']=0;
    $array_gestion_diaria_estado['Pendiente']=0;
    $array_gestion_diaria_estado['Finalizado']=0;
    for ($i=0; $i < count($resultado_registros_diaria); $i++) { 
      $array_gestion_diaria[$resultado_registros_diaria[$i][0]][$resultado_registros_diaria[$i][1]]=$resultado_registros_diaria[$i][2];
      $array_gestion_diaria_estado[$resultado_registros_diaria[$i][1]]+=$resultado_registros_diaria[$i][2];
      $array_gestion_diaria_total[$resultado_registros_diaria[$i][0]]['Total']+=$resultado_registros_diaria[$i][2];
      $array_gestion_diaria['Total']+=$resultado_registros_diaria[$i][2];
    }

    $consulta_string_tipologia_anio="SELECT `gewc_tipologia`, MONTH(`gewc_correo_fecha`) AS FECHA, count(`gewc_tipologia`) FROM `gestion_enviosweb_casos` WHERE 1=1 AND `gewc_estado`='Finalizado' ".$filtro_mes." GROUP BY `gewc_tipologia`, FECHA";
    $consulta_registros_tipologia_anio = $enlace_db->prepare($consulta_string_tipologia_anio);
    $consulta_registros_tipologia_anio->bind_param(str_repeat("s", count($data_consulta_anio)), ...$data_consulta_anio);
    $consulta_registros_tipologia_anio->execute();
    $resultado_registros_tipologia_anio = $consulta_registros_tipologia_anio->get_result()->fetch_all(MYSQLI_NUM);
  
    for ($i=0; $i < count($resultado_registros_tipologia_anio); $i++) { 
      $array_tipologia_mes[$resultado_registros_tipologia_anio[$i][0]][$resultado_registros_tipologia_anio[$i][1]]=$resultado_registros_tipologia_anio[$i][2];
      $array_tipologia_total[$resultado_registros_tipologia_anio[$i][0]]+=$resultado_registros_tipologia_anio[$i][2];
      $array_tipologia_mes_total[$resultado_registros_tipologia_anio[$i][1]]+=$resultado_registros_tipologia_anio[$i][2];
      $array_tipologia_mes['Total']+=$resultado_registros_tipologia_anio[$i][2];
    }

    $consulta_string_gestion_anio="SELECT `gewc_gestion`, MONTH(`gewc_correo_fecha`) AS FECHA, count(`gewc_gestion`) FROM `gestion_enviosweb_casos` WHERE 1=1 AND `gewc_estado`='Finalizado' ".$filtro_mes." GROUP BY `gewc_gestion`, FECHA";
    $consulta_registros_gestion_anio = $enlace_db->prepare($consulta_string_gestion_anio);
    $consulta_registros_gestion_anio->bind_param(str_repeat("s", count($data_consulta_anio)), ...$data_consulta_anio);
    $consulta_registros_gestion_anio->execute();
    $resultado_registros_gestion_anio = $consulta_registros_gestion_anio->get_result()->fetch_all(MYSQLI_NUM);
  
    for ($i=0; $i < count($resultado_registros_gestion_anio); $i++) { 
      $array_gestion_mes[$resultado_registros_gestion_anio[$i][0]][$resultado_registros_gestion_anio[$i][1]]=$resultado_registros_gestion_anio[$i][2];
      $array_gestion_total[$resultado_registros_gestion_anio[$i][0]]+=$resultado_registros_gestion_anio[$i][2];
      $array_gestion_mes_total[$resultado_registros_gestion_anio[$i][1]]+=$resultado_registros_gestion_anio[$i][2];
      $array_gestion_mes['Total']+=$resultado_registros_gestion_anio[$i][2];
    }

    $consulta_string_plantillas_anio="SELECT `gewch_gestion_detalle`, TPLA.`gewcp_nombre`, MONTH(TC.`gewc_correo_fecha`) AS FECHA, count(`gewch_gestion_detalle`) FROM `gestion_enviosweb_casos_historial` LEFT JOIN `gestion_enviosweb_casos` AS TC ON `gestion_enviosweb_casos_historial`.`gewch_radicado_id`=TC.`gewc_id` LEFT JOIN `gestion_enviosweb_casos_plantillas` AS TPLA ON `gestion_enviosweb_casos_historial`.`gewch_gestion_detalle`=TPLA.`gewcp_id` WHERE 1=1 AND TC.`gewc_estado`='Finalizado' AND TPLA.`gewcp_nombre` IS NOT NULL ".$filtro_mes." GROUP BY `gewch_gestion_detalle`, FECHA";
    $consulta_registros_plantillas_anio = $enlace_db->prepare($consulta_string_plantillas_anio);
    $consulta_registros_plantillas_anio->bind_param(str_repeat("s", count($data_consulta_anio)), ...$data_consulta_anio);
    $consulta_registros_plantillas_anio->execute();
    $resultado_registros_plantillas_anio = $consulta_registros_plantillas_anio->get_result()->fetch_all(MYSQLI_NUM);
    
    $array_plantilla_id=array();
    for ($i=0; $i < count($resultado_registros_plantillas_anio); $i++) { 
      $array_plantilla_id[]=$resultado_registros_plantillas_anio[$i][0];
      $array_plantilla_nombre[$resultado_registros_plantillas_anio[$i][0]]=$resultado_registros_plantillas_anio[$i][1];
      $array_plantilla_mes[$resultado_registros_plantillas_anio[$i][0]][$resultado_registros_plantillas_anio[$i][2]]=$resultado_registros_plantillas_anio[$i][3];
      $array_plantilla_total[$resultado_registros_plantillas_anio[$i][0]]+=$resultado_registros_plantillas_anio[$i][3];
      $array_plantilla_mes_total[$resultado_registros_plantillas_anio[$i][2]]+=$resultado_registros_plantillas_anio[$i][3];
      $array_plantilla['Total']+=$resultado_registros_plantillas_anio[$i][3];
    }

    $array_plantilla_id=array_values(array_unique($array_plantilla_id));

    $consulta_string_archivo_anio="SELECT `gewch_gestion_detalle`, count(`gewch_gestion_detalle`) FROM `gestion_enviosweb_casos_historial` LEFT JOIN `gestion_enviosweb_casos` AS TC ON `gestion_enviosweb_casos_historial`.`gewch_radicado_id`=TC.`gewc_id` WHERE 1=1 AND TC.`gewc_estado`='Finalizado' AND `gewch_gestion_detalle`<>'' AND (`gewch_gestion`='Archivar') ".$filtro_mes." GROUP BY `gewch_gestion_detalle`";
    $consulta_registros_archivo_anio = $enlace_db->prepare($consulta_string_archivo_anio);
    $consulta_registros_archivo_anio->bind_param(str_repeat("s", count($data_consulta_anio)), ...$data_consulta_anio);
    $consulta_registros_archivo_anio->execute();
    $resultado_registros_archivo_anio = $consulta_registros_archivo_anio->get_result()->fetch_all(MYSQLI_NUM);
    
    $total_archivo=0;
    for ($i=0; $i < count($resultado_registros_archivo_anio); $i++) { 
      $total_archivo+=$resultado_registros_archivo_anio[$i][1];
    }

    $consulta_string_agente_diaria="SELECT DATE(`gewc_correo_fecha`) AS FECHA, `gewc_responsable`, count(`gewc_responsable`), TAG.`usu_nombres_apellidos` FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos`.`gewc_responsable`=TAG.`usu_id` WHERE `gewc_estado`='Finalizado' AND `gewc_responsable`<>'' ".$filtro_mes." GROUP BY FECHA, `gewc_responsable`";
    $consulta_registros_agente_diaria = $enlace_db->prepare($consulta_string_agente_diaria);
    $consulta_registros_agente_diaria->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $consulta_registros_agente_diaria->execute();
    $resultado_registros_agente_diaria = $consulta_registros_agente_diaria->get_result()->fetch_all(MYSQLI_NUM);

    $array_agentes=array();
    for ($i=0; $i < count($resultado_registros_agente_diaria); $i++) {
      $array_agentes[]=$resultado_registros_agente_diaria[$i][1];
      $array_agente_nombre[$resultado_registros_agente_diaria[$i][1]]=$resultado_registros_agente_diaria[$i][3];
      $array_agente_diaria[$resultado_registros_agente_diaria[$i][1]][$resultado_registros_agente_diaria[$i][0]]=$resultado_registros_agente_diaria[$i][2];
      $array_agente_diaria_total[$resultado_registros_agente_diaria[$i][1]]['Total']+=$resultado_registros_agente_diaria[$i][2];
    }

    $array_agentes=array_values(array_unique($array_agentes));

    $consulta_string_agente_estado="SELECT `gewc_responsable`, `gewc_estado`, count(`gewc_estado`), TAG.`usu_nombres_apellidos` FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos`.`gewc_responsable`=TAG.`usu_id` WHERE `gewc_responsable`<>'' ".$filtro_mes." GROUP BY `gewc_responsable`, `gewc_estado`";
    $consulta_registros_agente_estado = $enlace_db->prepare($consulta_string_agente_estado);
    $consulta_registros_agente_estado->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $consulta_registros_agente_estado->execute();
    $resultado_registros_agente_estado = $consulta_registros_agente_estado->get_result()->fetch_all(MYSQLI_NUM);

    $array_agentes_estado=array();
    for ($i=0; $i < count($resultado_registros_agente_estado); $i++) {
      $array_agentes_estado[]=$resultado_registros_agente_estado[$i][0];
      $array_agente_estado_nombre[$resultado_registros_agente_estado[$i][0]]=$resultado_registros_agente_estado[$i][3];
      $array_agente_estado_diaria[$resultado_registros_agente_estado[$i][0]][$resultado_registros_agente_estado[$i][1]]=$resultado_registros_agente_estado[$i][2];
    }

    $array_agentes_estado=array_values(array_unique($array_agentes_estado));

?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <?php require_once(ROOT.'includes/_head-charts.php'); ?>
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
          <div class="row">
            <div class="col-md-3 mb-1">
              <form name="filtrado" action="" method="POST">
                <div class="form-group m-0">
                  <div class="input-group">
                    <input type="month" class="form-control form-control-sm" name="id_filtro" value='<?php if (isset($_POST["filtro"])) { echo $_POST['id_filtro']; } else {if($filtro_permanente!="null"){echo $filtro_permanente;}} ?>' placeholder="Búsqueda" required autofocus>
                    <div class="input-group-append">
                      <button class="btn py-2 px-2 btn-primary btn-corp" type="submit" name="filtro"><span class="fas fa-search font-size-12"></span></button>
                      <a href="<?php echo $url_fichero; ?>?pagina=1&id=null<?php echo $parametros_add; ?>" class="btn py-2 px-2 btn-primary btn-corp"><span class="fas fa-sync-alt font-size-12"></span></a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="col-md-9 mb-1 text-end">
              <!-- <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
              </button> -->
            </div>
            <div class="col-lg-12 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="text-center">
                        <h6 class="fw-bold card-title-dash">Seguimiento por día</h6>
                      </div>
                      <div class="table-responsive table-fixed mb-3" id="headerFixTable">
                        <table class="table table-hover table-bordered table-striped">
                          <thead>
                            <tr>
                              <th class="px-1 py-2">Estado</th>
                              <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                                <th class="px-1 py-2"><?php echo date('d', strtotime($array_anio_mes_dias[$i])).'-'.$array_mes_min[date('m', strtotime($array_anio_mes_dias[$i]))]; ?></th>
                              <?php endfor; ?>
                              <th class="px-1 py-2">Total</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td class="p-1 font-size-11 text-center fw-bold">Ingreso Total</td>
                              <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                                <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_diaria_total[$array_anio_mes_dias[$i]]['Total'])) ? $array_gestion_diaria_total[$array_anio_mes_dias[$i]]['Total'] : '0'; ?></td>
                              <?php endfor; ?>
                              <td class="p-1 font-size-11 text-center fw-bold"><?php echo $array_gestion_diaria['Total']; ?></td>
                            </tr>
                            <tr>
                              <td class="p-1 font-size-11 text-center fw-bold">Pendiente</td>
                              <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                                <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_diaria[$array_anio_mes_dias[$i]]['Pendiente'])) ? $array_gestion_diaria[$array_anio_mes_dias[$i]]['Pendiente'] : '0'; ?></td>
                              <?php endfor; ?>
                              <td class="p-1 font-size-11 text-center fw-bold"><?php echo $array_gestion_diaria_estado['Pendiente']; ?></td>
                            </tr>
                            <tr>
                              <td class="p-1 font-size-11 text-center fw-bold">Finalizado</td>
                              <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                                <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_diaria[$array_anio_mes_dias[$i]]['Finalizado'])) ? $array_gestion_diaria[$array_anio_mes_dias[$i]]['Finalizado'] : '0'; ?></td>
                              <?php endfor; ?>
                              <td class="p-1 font-size-11 text-center fw-bold"><?php echo $array_gestion_diaria_estado['Finalizado']; ?></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div id="seguimiento_diario"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="text-center">
                            <h6 class="fw-bold card-title-dash">Estado General</h6>
                          </div>
                          <div class="table-responsive table-fixed" id="headerFixTable">
                            <table class="table table-hover table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Estado</th>
                                  <th class="px-1 py-2">Total</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Pendiente</td>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_diaria_estado['Pendiente'])) ? $array_gestion_diaria_estado['Pendiente'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Finalizado</td>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_diaria_estado['Finalizado'])) ? $array_gestion_diaria_estado['Finalizado'] : '0'; ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div id="estado_global"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="d-sm-flex justify-content-between align-items-start mb-3">
                        <div>
                          <h6 class="fw-bold card-title-dash text-center">Comparativo buzón/Gestión</h6>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="text-center">
                            <h6 class="fw-bold card-title-dash">Tipología</h6>
                          </div>
                          <div class="table-responsive table-fixed" id="headerFixTable">
                            <table class="table table-hover table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Tipología</th>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <th class="px-1 py-2"><?php echo $array_mes_min[$i+1].'<br>'.$anio_consulta; ?></th>
                                  <?php endfor; ?>
                                  <th class="px-1 py-2">Total</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Reparto</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Reparto'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Reparto'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Reparto'])) ? $array_tipologia_total['Reparto'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Subsidio Familiar de Vivienda en especie</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Subsidio Familiar de Vivienda en especie'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Subsidio Familiar de Vivienda en especie'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Subsidio Familiar de Vivienda en especie'])) ? $array_tipologia_total['Subsidio Familiar de Vivienda en especie'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Ingreso Solidario</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Ingreso Solidario'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Ingreso Solidario'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Ingreso Solidario'])) ? $array_tipologia_total['Ingreso Solidario'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Colombia Mayor</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Colombia Mayor'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Colombia Mayor'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Colombia Mayor'])) ? $array_tipologia_total['Colombia Mayor'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Compensación del IVA</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Compensación del IVA'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Compensación del IVA'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Compensación del IVA'])) ? $array_tipologia_total['Compensación del IVA'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Antifraudes</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Antifraudes'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Antifraudes'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Antifraudes'])) ? $array_tipologia_total['Antifraudes'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Jóvenes en Acción</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Jóvenes en Acción'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Jóvenes en Acción'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Jóvenes en Acción'])) ? $array_tipologia_total['Jóvenes en Acción'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Tránsito a Renta Ciudadana</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Tránsito a Renta Ciudadana'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Tránsito a Renta Ciudadana'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Tránsito a Renta Ciudadana'])) ? $array_tipologia_total['Tránsito a Renta Ciudadana'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Otros programas</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Otros programas'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Otros programas'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Otros programas'])) ? $array_tipologia_total['Otros programas'] : '0'; ?></td>
                                </tr>
                              </tbody>
                              <thead>
                                <tr>
                                  <th class="p-1 font-size-11 text-center fw-bold">Total</th>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes_total[$array_mes_gestion[$i]])) ? $array_tipologia_mes_total[$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_mes['Total'])) ? $array_tipologia_mes['Total'] : '0'; ?></td>
                                </tr>
                              </thead>
                            </table>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div id="tipologia"></div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-6">
                          <div class="text-center">
                            <h6 class="fw-bold card-title-dash">Gestión de tipologías</h6>
                          </div>
                          <div class="table-responsive table-fixed" id="headerFixTable">
                            <table class="table table-hover table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Gestión</th>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <th class="px-1 py-2"><?php echo $array_mes_min[$i+1].'<br>'.$anio_consulta; ?></th>
                                  <?php endfor; ?>
                                  <th class="px-1 py-2">Total</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Respuesta</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_mes['Respuesta'][$array_mes_gestion[$i]])) ? $array_gestion_mes['Respuesta'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_total['Respuesta'])) ? $array_gestion_total['Respuesta'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Archivar</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_mes['Archivar'][$array_mes_gestion[$i]])) ? $array_gestion_mes['Archivar'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_total['Archivar'])) ? $array_gestion_total['Archivar'] : '0'; ?></td>
                                </tr>
                              </tbody>
                              <thead>
                                <tr>
                                  <th class="p-1 font-size-11 text-center fw-bold">Total</th>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_mes_total[$array_mes_gestion[$i]])) ? $array_gestion_mes_total[$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_mes['Total'])) ? $array_gestion_mes['Total'] : '0'; ?></td>
                                </tr>
                              </thead>
                            </table>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div id="gestion_tipologia"></div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="d-sm-flex justify-content-between align-items-start mb-3">
                        <div>
                          <h6 class="fw-bold card-title-dash text-center">Gestión Plantillas y Motivos Archivo</h6>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-5">
                          <div class="text-center">
                            <h6 class="fw-bold card-title-dash">Plantillas</h6>
                          </div>
                          <div class="table-responsive table-fixed" id="headerFixTable">
                            <table class="table table-hover table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Plantilla</th>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <th class="px-1 py-2"><?php echo $array_mes_min[$i+1].'<br>'.$anio_consulta; ?></th>
                                  <?php endfor; ?>
                                  <th class="px-1 py-2">Total</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php for ($j=0; $j < count($array_plantilla_id); $j++): ?>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo $array_plantilla_nombre[$array_plantilla_id[$j]]; ?></td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_plantilla_mes[$array_plantilla_id[$j]][$array_mes_gestion[$i]])) ? $array_plantilla_mes[$array_plantilla_id[$j]][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_plantilla_total[$array_plantilla_id[$j]])) ? $array_plantilla_total[$array_plantilla_id[$j]] : '0'; ?></td>
                                </tr>
                                <?php endfor; ?>
                              </tbody>
                              <thead>
                                <tr>
                                  <th class="p-1 font-size-11 text-center fw-bold">Total</th>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_plantilla_mes_total[$array_mes_gestion[$i]])) ? $array_plantilla_mes_total[$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_plantilla['Total'])) ? $array_plantilla['Total'] : '0'; ?></td>
                                </tr>
                              </thead>
                            </table>
                          </div>
                        </div>
                        <div class="col-md-7">
                          <div id="plantillas"></div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="d-sm-flex justify-content-between align-items-start mb-3">
                        <div>
                          <h6 class="fw-bold card-title-dash text-center">Productividad</h6>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="text-center">
                            <h6 class="fw-bold card-title-dash">Gestión por Agente Estado</h6>
                          </div>
                          <div class="table-responsive table-fixed" id="headerFixTable">
                            <table class="table table-hover table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Agente</th>
                                  <th class="px-1 py-2">Pendiente</th>
                                  <th class="px-1 py-2">Finalizado</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php for ($j=0; $j < count($array_agentes_estado); $j++): ?>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo $array_agente_estado_nombre[$array_agentes_estado[$j]]; ?></td>
                                  <td class="p-1 font-size-11 text-center"><?php echo (isset($array_agente_estado_diaria[$array_agentes_estado[$j]]['Pendiente'])) ? $array_agente_estado_diaria[$array_agentes_estado[$j]]['Pendiente'] : '0'; ?></td>
                                  <td class="p-1 font-size-11 text-center"><?php echo (isset($array_agente_estado_diaria[$array_agentes_estado[$j]]['Finalizado'])) ? $array_agente_estado_diaria[$array_agentes_estado[$j]]['Finalizado'] : '0'; ?></td>
                                </tr>
                                <?php endfor; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="text-center">
                            <h6 class="fw-bold card-title-dash">Gestión por Agente Diario</h6>
                          </div>
                          <div class="table-responsive table-fixed" id="headerFixTable">
                            <table class="table table-hover table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Agente</th>
                                  <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                                    <th class="px-1 py-2"><?php echo date('d', strtotime($array_anio_mes_dias[$i])).'-'.$array_mes_min[date('m', strtotime($array_anio_mes_dias[$i]))]; ?></th>
                                  <?php endfor; ?>
                                  <th class="px-1 py-2">Total</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php for ($j=0; $j < count($array_agentes); $j++): ?>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo $array_agente_nombre[$array_agentes[$j]]; ?></td>
                                  <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_agente_diaria[$array_agentes[$j]][$array_anio_mes_dias[$i]])) ? $array_agente_diaria[$array_agentes[$j]][$array_anio_mes_dias[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo $array_agente_diaria_total[$array_agentes[$j]]['Total']; ?></td>
                                </tr>
                                <?php endfor; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div id="gestion_agente_diario"></div>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>


          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
    Highcharts.chart('seguimiento_diario', {
      chart: {
          type: 'spline',
          height: '250px'
      },
      title: {
          text: 'Pendientes y Finalizado',
          style: {
              fontSize: '14px'
          }
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: [
            <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
              '<?php echo date('d', strtotime($array_anio_mes_dias[$i])).'-'.$array_mes_min[date('m', strtotime($array_anio_mes_dias[$i]))]; ?>',
            <?php endfor; ?>
          ],
          crosshair: true
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Radicados'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0;font-size:10px">{series.name}: </td>' +
              '<td style="padding:0;font-size:10px"><b>{point.y}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          column: {
              pointPadding: 0.2,
              borderWidth: 0,
              dataLabels: {
                  enabled: true,
                  style: {
                      fontSize: '8px'
                  },
                  format: '{point.y}'
              }
          }
      },
      legend: {
        layout: "horizontal",
        align: "center",
        verticalAlign: "bottom",
        itemStyle: {
          color: '#000000',
          fontWeight: 'normal',
          fontSize: 11,
        },
      },
      credits: {
          enabled: false
      },
      series: [
        {
            name: 'Pendiente',
            color: '#3498DB',
            data: [
              <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                <?php echo (isset($array_gestion_diaria[$array_anio_mes_dias[$i]]['Pendiente'])) ? $array_gestion_diaria[$array_anio_mes_dias[$i]]['Pendiente'] : '0'; ?>,
              <?php endfor; ?>
            ]
        },
        {
            name: 'Finalizado',
            color: '#E67E22',
            data: [
              <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                <?php echo (isset($array_gestion_diaria[$array_anio_mes_dias[$i]]['Finalizado'])) ? $array_gestion_diaria[$array_anio_mes_dias[$i]]['Finalizado'] : '0'; ?>,
              <?php endfor; ?>
            ]
        },
      ]
    });

    Highcharts.chart('estado_global', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            height: 200,
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Estado General',
            style: {
                fontSize: '14px'
            }
        },
        tooltip: {
            pointFormat: '<b>{point.y}</b> ({point.percentage:.1f}%)'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y} [{point.percentage:.1f} %]'
                }
            }
        },
        series: [{
            colorByPoint: true,
            data: [
            {
                name: 'Pendiente',
                y: <?php echo $array_gestion_diaria_estado['Pendiente']; ?>,
            },
            {
                name: 'Finalizado',
                y: <?php echo $array_gestion_diaria_estado['Finalizado']; ?>,
            },
            ]
        }]
    });

    Highcharts.chart('tipologia', {
      chart: {
          type: 'column',
          height: 280
      },
      title: {
          text: 'Tipología',
          style: {
              fontSize: '14px'
          }
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: ['Reparto', 'Subsidio Familiar de Vivienda en especie', 'Ingreso Solidario', 'Colombia Mayor', 'Compensación del IVA', 'Antifraudes', 'Jóvenes en Acción', 'Tránsito a Renta Ciudadana', 'Otros programas'],
          title: {
              text: null
          }
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Radicados',
              align: 'high'
          },
          labels: {
              overflow: 'justify'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}: <b>{point.y}</span>',
          pointFormat: '',
          footerFormat: '',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          column: {
              dataLabels: {
                  enabled: true
              }
          }
      },
      legend: false,
      credits: {
          enabled: false
      },
      series: [{
          name: 'Radicados',
          colorByPoint: true,
          data: [
                  <?php echo (isset($array_tipologia_total['Reparto'])) ? $array_tipologia_total['Reparto'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Subsidio Familiar de Vivienda en especie'])) ? $array_tipologia_total['Subsidio Familiar de Vivienda en especie'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Ingreso Solidario'])) ? $array_tipologia_total['Ingreso Solidario'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Colombia Mayor'])) ? $array_tipologia_total['Colombia Mayor'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Compensación del IVA'])) ? $array_tipologia_total['Compensación del IVA'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Antifraudes'])) ? $array_tipologia_total['Antifraudes'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Jóvenes en Acción'])) ? $array_tipologia_total['Jóvenes en Accióneo'] : '0'; ?>
                  <?php echo (isset($array_tipologia_total['Tránsito a Renta Ciudadana'])) ? $array_tipologia_total['Tránsito a Renta Ciudadanaeo'] : '0'; ?>
                  <?php echo (isset($array_tipologia_total['Otros programas'])) ? $array_tipologia_total['Otros programaseo'] : '0'; ?>
                ]
      }]
    });

    Highcharts.chart('gestion_tipologia', {
      chart: {
          type: 'bar',
          height: 280
      },
      title: {
          text: 'Gestión de Tipologías',
          style: {
              fontSize: '14px'
          }
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: ['Respuesta', 'Archivar'],
          title: {
              text: null
          }
      },
      yAxis: {
          min: 0,
          // max: 100,
          title: {
              text: 'Radicados',
              align: 'high'
          },
          labels: {
              overflow: 'justify'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}: <b>{point.y}</span>',
          pointFormat: '',
          footerFormat: '',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          bar: {
              dataLabels: {
                  enabled: true
              }
          }
      },
      legend: false,
      credits: {
          enabled: false
      },
      series: [{
          name: 'Porcentaje',
          colorByPoint: true,
          data: [
                  <?php echo (isset($array_gestion_total['Respuesta'])) ? $array_gestion_total['Respuesta'] : '0'; ?>, 
                  <?php echo (isset($array_gestion_total['Archivar'])) ? $array_gestion_total['Archivar'] : '0'; ?>, 
                ]
      }]
    });

    Highcharts.chart('plantillas', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            height: 400,
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Plantillas',
            style: {
                fontSize: '14px'
            }
        },
        tooltip: {
            pointFormat: '<b>{point.y}</b> ({point.percentage:.1f}%)'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y} [{point.percentage:.1f} %]'
                }
            }
        },
        series: [{
            colorByPoint: true,
            data: [
            <?php for ($i=0; $i < count($array_plantilla_id); $i++): ?>
              {
                  name: '<?php echo $array_plantilla_nombre[$array_plantilla_id[$i]]; ?>',
                  y: <?php echo (isset($array_plantilla_total[$array_plantilla_id[$i]])) ? $array_plantilla_total[$array_plantilla_id[$i]] : '0'; ?>,
              },
            <?php endfor; ?>
            ]
        }]
    });


    Highcharts.chart('gestion_agente_diario', {
      chart: {
          type: 'spline',
          height: '400px'
      },
      title: {
          text: 'Gestión por Agente',
          style: {
              fontSize: '14px'
          }
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: [
            <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
              '<?php echo date('d', strtotime($array_anio_mes_dias[$i])).'-'.$array_mes_min[date('m', strtotime($array_anio_mes_dias[$i]))]; ?>',
            <?php endfor; ?>
          ],
          crosshair: true
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Radicados'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0;font-size:10px">{series.name}: </td>' +
              '<td style="padding:0;font-size:10px"><b>{point.y}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          column: {
              pointPadding: 0.2,
              borderWidth: 0,
              dataLabels: {
                  enabled: true,
                  style: {
                      fontSize: '8px'
                  },
                  format: '{point.y}'
              }
          }
      },
      legend: {
        layout: "horizontal",
        align: "center",
        verticalAlign: "bottom",
        itemStyle: {
          color: '#000000',
          fontWeight: 'normal',
          fontSize: 11,
        },
      },
      credits: {
          enabled: false
      },
      series: [
        <?php for ($j=0; $j < count($array_agentes); $j++): ?>
          {
              name: '<?php echo $array_agente_nombre[$array_agentes[$j]]; ?>',
              // color: '#3498DB',
              data: [
                <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                  <?php echo (isset($array_agente_diaria[$array_agentes[$j]][$array_anio_mes_dias[$i]])) ? $array_agente_diaria[$array_agentes[$j]][$array_anio_mes_dias[$i]] : '0'; ?>,
                <?php endfor; ?>
              ]
          },
        <?php endfor; ?>
      ]
    });
  </script>
</body>
</html>