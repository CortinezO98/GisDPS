<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Radicación";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');

  /*VARIABLES*/
  $title = "Radicación";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $estado=validar_input(base64_decode($_GET['estado']));
  $subtitle = "Buzón | Estadísticas";
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
      $filtro_mes=" AND `grc_correo_fecha_anio`=? AND `grc_correo_fecha_mes`=?";
      $filtro_anio=" AND `grc_correo_fecha_anio`=?";
      $fecha_filtro=explode('-', $filtro_permanente);
      array_push($data_consulta, $fecha_filtro[0]);
      array_push($data_consulta, $fecha_filtro[1]);
      array_push($data_consulta_anio, $fecha_filtro[0]);
  } else {
      $filtro_permanente=date('Y-m');
      $filtro_mes=" AND `grc_correo_fecha_anio`=? AND `grc_correo_fecha_mes`=?";
      $filtro_anio=" AND `grc_correo_fecha_anio`=?";
      $fecha_filtro=explode('-', $filtro_permanente);
      array_push($data_consulta, $fecha_filtro[0]);
      array_push($data_consulta, $fecha_filtro[1]);
      array_push($data_consulta_anio, $fecha_filtro[0]);
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
    $consulta_string_diaria="SELECT `grc_correo_fecha_amd`, `grc_estado`, count(`grc_estado`) FROM `gestion_radicacion_casos` WHERE `grc_tipologia`<>'Envío Radicado a Ciudadano' AND `grc_tipologia`<>'Notificaciones de correo' ".$filtro_mes." GROUP BY `grc_correo_fecha_amd`, `grc_estado`";
    $consulta_registros_diaria = $enlace_db->prepare($consulta_string_diaria);
    $consulta_registros_diaria->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $consulta_registros_diaria->execute();
    $resultado_registros_diaria = $consulta_registros_diaria->get_result()->fetch_all(MYSQLI_NUM);

    $array_gestion_diaria['Total']=0;
    $array_gestion_diaria_estado['Pendiente']=0;
    $array_gestion_diaria_estado['En trámite']=0;
    $array_gestion_diaria_estado['Finalizado']=0;
    for ($i=0; $i < count($resultado_registros_diaria); $i++) { 
      $array_gestion_diaria[$resultado_registros_diaria[$i][0]][$resultado_registros_diaria[$i][1]]=$resultado_registros_diaria[$i][2];
      $array_gestion_diaria_estado[$resultado_registros_diaria[$i][1]]+=$resultado_registros_diaria[$i][2];
      $array_gestion_diaria_total[$resultado_registros_diaria[$i][0]]['Total']+=$resultado_registros_diaria[$i][2];
      $array_gestion_diaria['Total']+=$resultado_registros_diaria[$i][2];
    }

    $consulta_string_tipologia_anio="SELECT `grc_tipologia`, `grc_correo_fecha_mes`, count(`grc_tipologia`) FROM `gestion_radicacion_casos` WHERE `grc_tipologia`<>'Envío Radicado a Ciudadano' AND `grc_tipologia`<>'Notificaciones Correo' AND `grc_estado`='Finalizado' ".$filtro_anio." GROUP BY `grc_tipologia`, `grc_correo_fecha_mes`";
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

    $consulta_string_gestion_anio="SELECT `grc_gestion`, `grc_correo_fecha_mes`, count(`grc_gestion`) FROM `gestion_radicacion_casos` WHERE `grc_tipologia`<>'Envío Radicado a Ciudadano' AND `grc_tipologia`<>'Notificaciones Correo' AND `grc_estado`='Finalizado' ".$filtro_anio." GROUP BY `grc_gestion`, `grc_correo_fecha_mes`";
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

    $consulta_string_plantillas_anio="SELECT `grch_gestion_detalle`, TPLA.`grcp_nombre`, TC.`grc_correo_fecha_mes`, count(`grch_gestion_detalle`) FROM `gestion_radicacion_casos_historial` LEFT JOIN `gestion_radicacion_casos` AS TC ON `gestion_radicacion_casos_historial`.`grch_radicado_id`=TC.`grc_id` LEFT JOIN `gestion_radicacion_casos_plantillas` AS TPLA ON `gestion_radicacion_casos_historial`.`grch_gestion_detalle`=TPLA.`grcp_id` WHERE 1=1 AND TC.`grc_estado`='Finalizado' AND (`grch_gestion`='Respuesta' OR `grch_gestion`='Respuesta Radicado DELTA') AND TPLA.`grcp_nombre` IS NOT NULL ".$filtro_anio." GROUP BY `grch_gestion_detalle`, TC.`grc_correo_fecha_mes`";
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

    $consulta_string_archivo_anio="SELECT `grch_gestion_detalle`, count(`grch_gestion_detalle`) FROM `gestion_radicacion_casos_historial` LEFT JOIN `gestion_radicacion_casos` AS TC ON `gestion_radicacion_casos_historial`.`grch_radicado_id`=TC.`grc_id` WHERE 1=1 AND TC.`grc_estado`='Finalizado' AND `grch_gestion_detalle`<>'' AND (`grch_gestion`='Archivar') ".$filtro_anio." GROUP BY `grch_gestion_detalle`";
    $consulta_registros_archivo_anio = $enlace_db->prepare($consulta_string_archivo_anio);
    $consulta_registros_archivo_anio->bind_param(str_repeat("s", count($data_consulta_anio)), ...$data_consulta_anio);
    $consulta_registros_archivo_anio->execute();
    $resultado_registros_archivo_anio = $consulta_registros_archivo_anio->get_result()->fetch_all(MYSQLI_NUM);
    
    $total_archivo=0;
    for ($i=0; $i < count($resultado_registros_archivo_anio); $i++) { 
      $total_archivo+=$resultado_registros_archivo_anio[$i][1];
    }

    $consulta_string_agente_nombre="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario`";
    $consulta_registros_agente_nombre = $enlace_db->prepare($consulta_string_agente_nombre);
    // $consulta_registros_agente_nombre->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $consulta_registros_agente_nombre->execute();
    $resultado_registros_agente_nombre = $consulta_registros_agente_nombre->get_result()->fetch_all(MYSQLI_NUM);

    $array_agente_nombre=array();
    for ($i=0; $i < count($resultado_registros_agente_nombre); $i++) { 
      $array_agente_nombre[$resultado_registros_agente_nombre[$i][0]]=$resultado_registros_agente_nombre[$i][1];
    }

    $consulta_string_agente_diaria="SELECT `grc_correo_fecha_amd`, `grc_responsable`, count(`grc_responsable`) FROM `gestion_radicacion_casos` WHERE `grc_estado`='Finalizado' AND `grc_responsable`<>'' ".$filtro_mes." GROUP BY `grc_correo_fecha_amd`, `grc_responsable`";
    $consulta_registros_agente_diaria = $enlace_db->prepare($consulta_string_agente_diaria);
    $consulta_registros_agente_diaria->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $consulta_registros_agente_diaria->execute();
    $resultado_registros_agente_diaria = $consulta_registros_agente_diaria->get_result()->fetch_all(MYSQLI_NUM);

    $array_agentes=array();
    for ($i=0; $i < count($resultado_registros_agente_diaria); $i++) {
      $array_agentes[]=$resultado_registros_agente_diaria[$i][1];
      $array_agente_nombre[$resultado_registros_agente_diaria[$i][1]]=$array_agente_nombre[$resultado_registros_agente_diaria[$i][1]];
      $array_agente_diaria[$resultado_registros_agente_diaria[$i][1]][$resultado_registros_agente_diaria[$i][0]]=$resultado_registros_agente_diaria[$i][2];
      $array_agente_diaria_total[$resultado_registros_agente_diaria[$i][1]]['Total']+=$resultado_registros_agente_diaria[$i][2];
    }

    $array_agentes=array_values(array_unique($array_agentes));

    $consulta_string_agente_estado="SELECT `grc_responsable`, `grc_estado`, count(`grc_estado`) FROM `gestion_radicacion_casos` WHERE `grc_responsable`<>'' ".$filtro_mes." GROUP BY `grc_responsable`, `grc_estado`";
    $consulta_registros_agente_estado = $enlace_db->prepare($consulta_string_agente_estado);
    $consulta_registros_agente_estado->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
    $consulta_registros_agente_estado->execute();
    $resultado_registros_agente_estado = $consulta_registros_agente_estado->get_result()->fetch_all(MYSQLI_NUM);

    $array_agentes_estado=array();
    for ($i=0; $i < count($resultado_registros_agente_estado); $i++) {
      $array_agentes_estado[]=$resultado_registros_agente_estado[$i][0];
      $array_agente_estado_nombre[$resultado_registros_agente_estado[$i][0]]=$array_agente_nombre[$resultado_registros_agente_estado[$i][0]];
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
                                <th class="px-1 py-2"><?php echo date('d', strtotime($array_anio_mes_dias[$i])).'-'.$array_mes_min[intval(date('m', strtotime($array_anio_mes_dias[$i])))]; ?></th>
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
                              <td class="p-1 font-size-11 text-center fw-bold">En trámite</td>
                              <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                                <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_diaria[$array_anio_mes_dias[$i]]['En trámite'])) ? $array_gestion_diaria[$array_anio_mes_dias[$i]]['En trámite'] : '0'; ?></td>
                              <?php endfor; ?>
                              <td class="p-1 font-size-11 text-center fw-bold"><?php echo $array_gestion_diaria_estado['En trámite']; ?></td>
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
                                  <td class="p-1 font-size-11 text-center fw-bold">En trámite</td>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_diaria_estado['En trámite'])) ? $array_gestion_diaria_estado['En trámite'] : '0'; ?></td>
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
                                  <td class="p-1 font-size-11 text-center fw-bold">Ciudadanos</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Ciudadanos'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Ciudadanos'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Ciudadanos'])) ? $array_tipologia_total['Ciudadanos'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Funcionarios</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Funcionarios'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Funcionarios'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Funcionarios'])) ? $array_tipologia_total['Funcionarios'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Prioritarios</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Prioritario'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Prioritario'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Prioritario'])) ? $array_tipologia_total['Prioritario'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Soy Transparente</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Soy Transparente'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Soy Transparente'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Soy Transparente'])) ? $array_tipologia_total['Soy Transparente'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Tutelas</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_tipologia_mes['Tutelas'][$array_mes_gestion[$i]])) ? $array_tipologia_mes['Tutelas'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_tipologia_total['Tutelas'])) ? $array_tipologia_total['Tutelas'] : '0'; ?></td>
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
                                  <td class="p-1 font-size-11 text-center fw-bold">Radicación DELTA</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_mes['Radicación DELTA'][$array_mes_gestion[$i]])) ? $array_gestion_mes['Radicación DELTA'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_total['Radicación DELTA'])) ? $array_gestion_total['Radicación DELTA'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Radicación DELTA Soy Transparente</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_mes['Radicación DELTA Soy Transparente'][$array_mes_gestion[$i]])) ? $array_gestion_mes['Radicación DELTA Soy Transparente'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_total['Radicación DELTA Soy Transparente'])) ? $array_gestion_total['Radicación DELTA Soy Transparente'] : '0'; ?></td>
                                </tr>
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
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Correspondencia</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_mes['Correspondencia'][$array_mes_gestion[$i]])) ? $array_gestion_mes['Correspondencia'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_total['Correspondencia'])) ? $array_gestion_total['Correspondencia'] : '0'; ?></td>
                                </tr>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold">Notificaciones Jurídica</td>
                                  <?php for ($i=0; $i < count($array_mes_gestion); $i++): ?>
                                    <td class="p-1 font-size-11 text-center"><?php echo (isset($array_gestion_mes['Notificaciones Jurídica'][$array_mes_gestion[$i]])) ? $array_gestion_mes['Notificaciones Jurídica'][$array_mes_gestion[$i]] : '0'; ?></td>
                                  <?php endfor; ?>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo (isset($array_gestion_total['Notificaciones Jurídica'])) ? $array_gestion_total['Notificaciones Jurídica'] : '0'; ?></td>
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
                      
                      <div class="row">
                        <div class="col-md-4">
                          <div class="text-center">
                            <h6 class="fw-bold card-title-dash">Motivos Archivo</h6>
                          </div>
                          <div class="table-responsive table-fixed" id="headerFixTable">
                            <table class="table table-hover table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Motivos Archivo</th>
                                  <th class="px-1 py-2">Total</th>
                                  <th class="px-1 py-2">Porcentaje</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php for ($i=0; $i < count($resultado_registros_archivo_anio); $i++): ?>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo $resultado_registros_archivo_anio[$i][0]; ?></td>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo $resultado_registros_archivo_anio[$i][1]; ?></td>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo number_format(($resultado_registros_archivo_anio[$i][1]/$total_archivo)*100); ?>%</td>
                                </tr>
                                <?php endfor; ?>
                              </tbody>
                              <thead>
                                <tr>
                                  <th class="p-1 font-size-11 text-center fw-bold">Total</th>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo $total_archivo; ?></td>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo number_format(($total_archivo/$total_archivo)*100); ?>%</td>
                                </tr>
                              </thead>
                            </table>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div id="motivo_archivo_porcentaje"></div>
                        </div>
                        <div class="col-md-4">
                          <div id="motivo_archivo"></div>
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
                                  <th class="px-1 py-2">En trámite</th>
                                  <th class="px-1 py-2">Finalizado</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php for ($j=0; $j < count($array_agentes_estado); $j++): ?>
                                <tr>
                                  <td class="p-1 font-size-11 text-center fw-bold"><?php echo $array_agente_estado_nombre[$array_agentes_estado[$j]]; ?></td>
                                  <td class="p-1 font-size-11 text-center"><?php echo (isset($array_agente_estado_diaria[$array_agentes_estado[$j]]['Pendiente'])) ? $array_agente_estado_diaria[$array_agentes_estado[$j]]['Pendiente'] : '0'; ?></td>
                                  <td class="p-1 font-size-11 text-center"><?php echo (isset($array_agente_estado_diaria[$array_agentes_estado[$j]]['En trámite'])) ? $array_agente_estado_diaria[$array_agentes_estado[$j]]['En trámite'] : '0'; ?></td>
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
          text: 'Pendientes y En trámite',
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
            name: 'En trámite',
            color: '#E67E22',
            data: [
              <?php for ($i=0; $i < count($array_anio_mes_dias); $i++): ?>
                <?php echo (isset($array_gestion_diaria[$array_anio_mes_dias[$i]]['En trámite'])) ? $array_gestion_diaria[$array_anio_mes_dias[$i]]['En trámite'] : '0'; ?>,
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
                name: 'En trámite',
                y: <?php echo $array_gestion_diaria_estado['En trámite']; ?>,
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
          categories: ['Prioritario', 'Soy Transparente', 'Funcionarios', 'Ciudadanos', 'Envío Radicado a Ciudadano', 'Tutelas', 'Notificaciones de correo'],
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
                  <?php echo (isset($array_tipologia_total['Prioritario'])) ? $array_tipologia_total['Prioritario'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Soy Transparente'])) ? $array_tipologia_total['Soy Transparente'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Funcionarios'])) ? $array_tipologia_total['Funcionarios'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Ciudadanos'])) ? $array_tipologia_total['Ciudadanos'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Envío Radicado a Ciudadano'])) ? $array_tipologia_total['Envío Radicado a Ciudadano'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Tutelas'])) ? $array_tipologia_total['Tutelas'] : '0'; ?>, 
                  <?php echo (isset($array_tipologia_total['Notificaciones de correo'])) ? $array_tipologia_total['Notificaciones de correo'] : '0'; ?>
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
          categories: ['Radicación DELTA', 'Radicación DELTA Soy Transparente', 'Respuesta', 'Archivar', 'Correspondencia', 'Notificaciones Jurídica'],
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
                  <?php echo (isset($array_gestion_total['Radicación DELTA'])) ? $array_gestion_total['Radicación DELTA'] : '0'; ?>, 
                  <?php echo (isset($array_gestion_total['Radicación DELTA Soy Transparente'])) ? $array_gestion_total['Radicación DELTA Soy Transparente'] : '0'; ?>, 
                  <?php echo (isset($array_gestion_total['Respuesta'])) ? $array_gestion_total['Respuesta'] : '0'; ?>, 
                  <?php echo (isset($array_gestion_total['Archivar'])) ? $array_gestion_total['Archivar'] : '0'; ?>, 
                  <?php echo (isset($array_gestion_total['Correspondencia'])) ? $array_gestion_total['Correspondencia'] : '0'; ?>, 
                  <?php echo (isset($array_gestion_total['Notificaciones Jurídica'])) ? $array_gestion_total['Notificaciones Jurídica'] : '0'; ?>
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

    Highcharts.chart('motivo_archivo_porcentaje', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            height: 320,
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Motivos Archivo',
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
            <?php for ($i=0; $i < count($resultado_registros_archivo_anio); $i++): ?>
              {
                  name: '<?php echo $resultado_registros_archivo_anio[$i][0]; ?>',
                  y: <?php echo $resultado_registros_archivo_anio[$i][1]; ?>,
              },
            <?php endfor; ?>
            ]
        }]
    });

    Highcharts.chart('motivo_archivo', {
      chart: {
          type: 'column',
          height: 320
      },
      title: {
          text: 'Motivos Archivo',
          style: {
              fontSize: '14px'
          }
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: [
              <?php for ($i=0; $i < count($resultado_registros_archivo_anio); $i++): ?>
                '<?php echo $resultado_registros_archivo_anio[$i][0]; ?>',
              <?php endfor; ?>
              ],
          title: {
              text: null
          }
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Motivos',
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
          name: 'Motivos Archivo',
          colorByPoint: true,
          data: [
                  <?php for ($i=0; $i < count($resultado_registros_archivo_anio); $i++): ?>
                    <?php echo $resultado_registros_archivo_anio[$i][1]; ?>,
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