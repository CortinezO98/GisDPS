<?php
session_start();

// Validación de permisos del usuario para el módulo
$modulo_plataforma = "Calidad-Monitoreos";
require_once("../../iniciador.php");

// Variables de cabecera
$title    = "Calidad";
$subtitle = "Estadísticas";
$pagina   = validar_input($_GET['pagina'] ?? 1);
$bandeja  = validar_input(base64_decode($_GET['bandeja'] ?? ''));

// --- 1) Construcción de filtros y parámetros ---
// Filtro de mes (si viene por POST o por defecto el mes actual)
if (isset($_POST['filtro'])) {
    $pagina            = 1;
    $filtro_permanente = validar_input($_POST['id_filtro']);
} else {
    $filtro_permanente = date('Y-m');
}
$paramsMes = ["{$filtro_permanente}%"];
$filtro_mes = " AND TMC.`gcm_registro_fecha` LIKE ?";

// Filtro de perfil según permisos
$perm = $_SESSION[APP_SESSION . '_session_modulos'][$modulo_plataforma] ?? null;
$paramsPerfil = [];
$filtro_perfil = '';
if ($perm === "Supervisor") {
    $filtro_perfil = " AND (TMC.`gcm_responsable`=? OR TMC.`gcm_analista`=? OR TMC.`gcm_registro_usuario`=?)";
    $paramsPerfil = [
        $_SESSION['usu_id'],
        $_SESSION['usu_id'],
        $_SESSION['usu_id'],
    ];
} elseif ($perm === "Usuario") {
    $filtro_perfil = " AND TMC.`gcm_analista`=?";
    $paramsPerfil = [$_SESSION['usu_id']];
}

// Para los botones Pendientes/Refutados solo usamos perfil
$paramsConteo = $paramsPerfil;
// Para las demás gráficas unimos mes + perfil
$paramsGeneral = array_merge($paramsMes, $paramsPerfil);

// --- 2) Generar array de días del mes ---
list($anio, $mes) = explode('-', $filtro_permanente);
$numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, intval($mes), intval($anio));
$array_anio_mes_dias = $array_anio_mes_dias_num = [];
for ($d = 1; $d <= $numero_dias_mes; $d++) {
    $dd = str_pad($d, 2, '0', STR_PAD_LEFT);
    $array_anio_mes_dias_num[] = $dd;
    $array_anio_mes_dias[]     = "{$filtro_permanente}-{$dd}";
}

// --- Helper para consultas COUNT ---
function fetch_count(mysqli $db, string $sql, array $params): int {
    $stmt = $db->prepare($sql);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result()->fetch_row();
    return intval($res[0] ?? 0);
}

// --- 3) Totales generales de monitoreos e indicadores ---
$sql_base = "
  SELECT COUNT(TMC.`gcm_id`)
    FROM `gestion_calidad_monitoreo` AS TMC
   WHERE 1=1
     AND TMC.`gcm_aplica_indicador`='Si-Calidad'
     {$filtro_mes}
     {$filtro_perfil}
";

$total_mon   = fetch_count($enlace_db, $sql_base,                                        $paramsGeneral);
$total_ecuf  = fetch_count($enlace_db, str_replace(
                    "TMC.`gcm_aplica_indicador`='Si-Calidad'",
                    "TMC.`gcm_aplica_indicador`='Si-Calidad' AND TMC.`gcm_nota_ecuf_estado`='0'",
                    $sql_base
                ), $paramsGeneral);
$total_ecn   = fetch_count($enlace_db, str_replace(
                    "TMC.`gcm_aplica_indicador`='Si-Calidad'",
                    "TMC.`gcm_aplica_indicador`='Si-Calidad' AND TMC.`gcm_nota_ecn_estado`='0'",
                    $sql_base
                ), $paramsGeneral);
$total_enc   = fetch_count($enlace_db, str_replace(
                    "TMC.`gcm_aplica_indicador`='Si-Calidad'",
                    "TMC.`gcm_aplica_indicador`='Si-Calidad' AND TMC.`gcm_nota_enc_estado`='0'",
                    $sql_base
                ), $paramsGeneral);

$array_gestion = [
    'monitoreos' => $total_mon,
    'ecuf'       => $total_ecuf,
    'ecn'        => $total_ecn,
    'enc'        => $total_enc,
    'pecuf'      => $total_mon ? (($total_mon - $total_ecuf)/$total_mon)*100 : 0,
    'pecn'       => $total_mon ? (($total_mon - $total_ecn)/$total_mon)*100 : 0,
    'penc'       => $total_mon ? (($total_mon - $total_enc)/$total_mon)*100 : 0,
];

// --- 4) Gráficos por monitor y por día (si no es “Usuario”) ---
$array_gestion_monitores     = [];
$array_gestion_monitores_doc = [];
$array_monitor_dia           = [];
$array_monitor_dia_doc       = [];
$array_semanas               = ['total_1'=>0,'total_2'=>0,'total_3'=>0,'total_4'=>0];

if ($perm !== "Usuario") {
    // 4.1) Totales por monitor
    $sql_monitores = "
      SELECT
        TMC.`gcm_registro_usuario`,
        U.`usu_nombres_apellidos`,
        COUNT(TMC.`gcm_id`) AS cnt
      FROM `gestion_calidad_monitoreo` AS TMC
      LEFT JOIN `administrador_usuario` AS U
        ON TMC.`gcm_registro_usuario`=U.`usu_id`
      WHERE 1=1
        AND TMC.`gcm_aplica_indicador`='Si-Calidad'
        {$filtro_mes}
        {$filtro_perfil}
      GROUP BY TMC.`gcm_registro_usuario`
      ORDER BY U.`usu_nombres_apellidos`
    ";
    $stmt = $enlace_db->prepare($sql_monitores);
    if ($paramsGeneral) {
        $stmt->bind_param(str_repeat('s', count($paramsGeneral)), ...$paramsGeneral);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as $r) {
        $uid = $r['gcm_registro_usuario'];
        $cnt = intval($r['cnt']);
        $array_gestion_monitores_doc[] = $uid;
        $array_gestion_monitores[$uid] = [
            'nombre'     => $r['usu_nombres_apellidos'],
            'monitoreos' => $cnt,
            'ecuf'=>0,'ecn'=>0,'enc'=>0,'pecuf'=>0,'pecn'=>0,'penc'=>0
        ];
    }
    // (Repetir para ecuf, ecn y enc ajustando la WHERE)

    // 4.2) Gestión diaria por monitor
    $sql_diario = "
      SELECT
        TMC.`gcm_registro_usuario`,
        U.`usu_nombres_apellidos`,
        TMC.`gcm_registro_fecha`,
        COUNT(TMC.`gcm_id`) AS cnt
      FROM `gestion_calidad_monitoreo` AS TMC
      LEFT JOIN `administrador_usuario` AS U
        ON TMC.`gcm_registro_usuario`=U.`usu_id`
      WHERE 1=1
        AND TMC.`gcm_aplica_indicador`='Si-Calidad'
        {$filtro_mes}
        {$filtro_perfil}
      GROUP BY TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`
      ORDER BY U.`usu_nombres_apellidos`, TMC.`gcm_registro_fecha`
    ";
    $stmt = $enlace_db->prepare($sql_diario);
    if ($paramsGeneral) {
        $stmt->bind_param(str_repeat('s', count($paramsGeneral)), ...$paramsGeneral);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as $r) {
        $uid  = $r['gcm_registro_usuario'];
        $iso  = date('Y-m-d', strtotime($r['gcm_registro_fecha']));
        $cnt  = intval($r['cnt']);
        $día  = intval(date('d', strtotime($iso)));

        $array_monitor_dia_doc[] = $uid;
        $array_monitor_dia[$uid]['nombre'] = $r['usu_nombres_apellidos'];
        $array_monitor_dia[$uid]['monitoreos'][$iso] = $cnt;

        if ($día <= 6)      $array_semanas['total_1'] += $cnt;
        elseif ($día <= 13) $array_semanas['total_2'] += $cnt;
        elseif ($día <= 20) $array_semanas['total_3'] += $cnt;
        else                $array_semanas['total_4'] += $cnt;
    }
    // Rellenar ceros
    $array_monitor_dia_doc = array_unique($array_monitor_dia_doc);
    foreach ($array_monitor_dia_doc as $uid) {
        foreach ($array_anio_mes_dias as $dia) {
            if (!isset($array_monitor_dia[$uid]['monitoreos'][$dia])) {
                $array_monitor_dia[$uid]['monitoreos'][$dia] = 0;
            }
        }
    }
    // Rangos semanales
    $array_semanas['rango_1'] = "01 al 06 de {$filtro_permanente}";
    $array_semanas['rango_2'] = "07 al 13 de {$filtro_permanente}";
    $array_semanas['rango_3'] = "14 al 20 de {$filtro_permanente}";
    $array_semanas['rango_4'] = "21 al {$numero_dias_mes} de {$filtro_permanente}";

    // 4.3) Monitoreos por matriz × usuario
    // (Implementar SELECT y llenado de $array_usuarios, $array_matrices, etc.)
}

// --- 5) Conteos de Pendientes y Refutados (solo perfil) ---
$sql_pendientes = "
  SELECT COUNT(TMC.`gcm_id`)
    FROM `gestion_calidad_monitoreo` AS TMC
   WHERE (
     TMC.`gcm_estado` IN ('Pendiente','Refutado-Rechazado','Refutado-Rechazado-Nivel 2')
     OR (
       TMC.`gcm_estado` IN ('Refutado-Aceptado','Refutado-Aceptado-Nivel 2')
       AND (TMC.`gcm_nota_enc`<100 OR TMC.`gcm_nota_ecn`<100 OR TMC.`gcm_nota_ecuf`<100)
     )
   )
   {$filtro_perfil}
";
$pendientes = fetch_count($enlace_db, $sql_pendientes, $paramsConteo);

$sql_refutados = "
  SELECT COUNT(TMC.`gcm_id`)
    FROM `gestion_calidad_monitoreo` AS TMC
   WHERE TMC.`gcm_estado` IN ('Refutado','Refutado-Nivel 2')
     {$filtro_perfil}
";
$refutados = fetch_count($enlace_db, $sql_refutados, $paramsConteo);

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
              <a href="monitoreos?pagina=1&id=null&bandeja=<?php echo base64_encode('Pendientes'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes <?php echo ($resultado_registros_conteo_pendientes[0][0]>0) ? "<div class='float-end alert_conteo_menu ms-1'>".$resultado_registros_conteo_pendientes[0][0]."</div>" : ""; ?></span>
              </a>
              <a href="monitoreos?pagina=1&id=null&bandeja=<?php echo base64_encode('Refutados'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Refutados">
                <i class="fas fa-user-times btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Refutados <?php echo ($resultado_registros_conteo_refutado[0][0]>0) ? "<div class='float-end alert_conteo_menu ms-1'>".$resultado_registros_conteo_refutado[0][0]."</div>" : ""; ?></span>
              </a>
              <a href="monitoreos?pagina=1&id=null&bandeja=<?php echo base64_encode('Mes Actual'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Mes Actual">
                <i class="fas fa-calendar-alt btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Mes Actual</span>
              </a>
              <a href="monitoreos?pagina=1&id=null&bandeja=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
                <i class="fas fa-history btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Histórico</span>
              </a>
              <?php if($permisos_usuario=="Administrador" OR ($_SESSION[APP_SESSION.'_session_cargo']=="LIDER DE CALIDAD" AND $permisos_usuario=="Gestor")): ?>
                <a href="monitoreos_transacciones?pagina=1&id=null&bandeja=<?php echo base64_encode($bandeja); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Transacciones">
                  <i class="fas fa-qrcode btn-icon-prepend me-0 font-size-12"></i>
                </a>
              <?php endif; ?>
              <a href="monitoreos_estadisticas?pagina=1&id=null&bandeja=<?php echo base64_encode($bandeja); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Estadísticas">
                <i class="fas fa-chart-pie btn-icon-prepend me-0 font-size-12"></i>
              </a>
            </div>
            <?php if ($resultado_registros_gestion[0][0]>0): ?>
              <div class="col-lg-6 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div id="grafica_gestion"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div id="grafica_resultado_indicadores"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php if($permisos_usuario!="Usuario"): ?>
                  <div class="col-lg-12 d-flex flex-column">
                    <div class="row flex-grow">
                      <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <div id="grafica_gestion_monitor"></div>
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
                            <div id="grafica_monitor_dia"></div>
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
                            <div id="grafica_matriz"></div>
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
                            <div id="grafica_semana"></div>
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
                            <div id="grafica_monitoreos_matriz"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              <?php endif; ?>
            <?php else: ?>
                <div class="col-md-12">
                  <p class="alert alert-warning p-1">
                      <span class="fas fa-exclamation-triangle"></span> No se encontraron registros
                  </p>
                </div>
            <?php endif; ?>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
      Highcharts.chart('grafica_gestion', {
          chart: {
              type: 'column',
              height: 300
          },
          title: {
              text: 'Gestión General | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
              style: {
                  fontSize: '14px'
              }
          },
          subtitle: {
              text: null
          },
          xAxis: {
              categories: ['Monitoreos', 'ECUF', 'ECN', 'ENC'],
              title: {
                  text: null
              }
          },
          yAxis: {
              min: 0,
              title: {
                  text: 'Cantidad monitoreos',
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
              name: 'Casos',
              colorByPoint: true,
              data: [<?php echo $array_gestion['monitoreos']; ?>, <?php echo $array_gestion['ecuf']; ?>, <?php echo $array_gestion['ecn']; ?>, <?php echo $array_gestion['enc']; ?>]
          }]
      });

      Highcharts.chart('grafica_resultado_indicadores', {
          chart: {
              type: 'bar',
              height: 300
          },
          title: {
              text: 'Resultado Indicadores General | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
              style: {
                  fontSize: '14px'
              }
          },
          subtitle: {
              text: null
          },
          xAxis: {
              categories: ['PENC', 'PECN', 'PECUF'],
              title: {
                  text: null
              }
          },
          yAxis: {
              min: 0,
              max: 100,
              title: {
                  text: 'Porcentaje',
                  align: 'high'
              },
              labels: {
                  overflow: 'justify'
              }
          },
          tooltip: {
              headerFormat: '<span style="font-size:10px">{point.key}: <b>{point.y} %</span>',
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
              data: [<?php echo number_format($array_gestion['penc'], 2, '.', ''); ?>, <?php echo number_format($array_gestion['pecn'], 2, '.', ''); ?>, <?php echo number_format($array_gestion['pecuf'], 2, '.', ''); ?>]
          }]
      });

      <?php if($permisos_usuario!="Usuario"): ?>
      Highcharts.chart('grafica_gestion_monitor', {
          chart: {
              zoomType: 'xy',
              height: 500,
          },
          title: {
              text: 'Indicadores Monitor | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
              style: {
                  fontSize: '14px'
              }
          },
          subtitle: {
              text: null
          },
          credits: {
              enabled: false
          },
          xAxis: [{
              categories: [
                          <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                              '<?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['nombre']; ?>',
                          <?php endfor; ?>
                          ],
              crosshair: true
          }],
          yAxis: [{ // Primary yAxis
              labels: {
                  format: '{value}',
                  style: {
                      color: Highcharts.getOptions().colors[2]
                  }
              },
              title: {
                  text: 'Porcentaje',
                  style: {
                      color: Highcharts.getOptions().colors[2]
                  }
              },
              opposite: true,
          }, { // Secondary yAxis
              gridLineWidth: 0,
              title: {
                  text: 'Cantidad monitoreos',
                  style: {
                      color: Highcharts.getOptions().colors[0]
                  }
              },
              labels: {
                  format: '{value}',
                  style: {
                      color: Highcharts.getOptions().colors[0]
                  }
              }

          }],
          tooltip: {
              shared: true
          },
          legend: {
              layout: 'horizontal',
              align: 'center',
              x: 0,
              verticalAlign: 'top',
              y: -20,
              floating: false,
              backgroundColor:
                  Highcharts.defaultOptions.legend.backgroundColor || // theme
                  'rgba(255,255,255,0.25)'
          },
          series: [{
              name: 'Monitoreos',
              type: 'column',
              yAxis: 1,
              color: '#4472C4',
              data: [
                      <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                          <?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['monitoreos']; ?>,
                      <?php endfor; ?>
              ],
              dataLabels: {
                  enabled: false,
                  inside: true,
                  rotation: 270,
                  align: 'left',
                  verticalAlign: 'bottom',
                  y: -5,
                  style: {
                      fontSize: '9px',
                      fontWeight: 'normal'
                  }
              },
              tooltip: {
                  valueSuffix: ''
              }

          },{
              name: 'ECUF',
              type: 'column',
              yAxis: 1,
              color: '#ED7D31',
              data: [
                      <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                          <?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['ecuf']; ?>,
                      <?php endfor; ?>
              ],
              dataLabels: {
                  enabled: false,
                  inside: true,
                  rotation: 270,
                  align: 'left',
                  verticalAlign: 'bottom',
                  y: -5,
                  style: {
                      fontSize: '9px',
                      fontWeight: 'normal'
                  }
              },
              tooltip: {
                  valueSuffix: ''
              }

          },{
              name: 'ECN',
              type: 'column',
              yAxis: 1,
              color: '#A5A5A5',
              data: [
                      <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                          <?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['ecn']; ?>,
                      <?php endfor; ?>
              ],
              dataLabels: {
                  enabled: false,
                  inside: true,
                  rotation: 270,
                  align: 'left',
                  verticalAlign: 'bottom',
                  y: -5,
                  style: {
                      fontSize: '9px',
                      fontWeight: 'normal'
                  }
              },
              tooltip: {
                  valueSuffix: ''
              }

          },{
              name: 'ENC',
              type: 'column',
              yAxis: 1,
              color: '#FFC000',
              data: [
                      <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                          <?php echo $array_gestion_monitores[$array_gestion_monitores_doc[$i]]['enc']; ?>,
                      <?php endfor; ?>
              ],
              dataLabels: {
                  enabled: false,
                  inside: true,
                  rotation: 270,
                  align: 'left',
                  verticalAlign: 'bottom',
                  y: -5,
                  style: {
                      fontSize: '9px',
                      fontWeight: 'normal'
                  }
              },
              tooltip: {
                  valueSuffix: ''
              }

          }, {
              name: '% PECUF',
              type: 'spline',
              color: '#5B9BD5',
              yAxis: 0,
              data: [
                      <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                          <?php echo number_format($array_gestion_monitores[$array_gestion_monitores_doc[$i]]['pecuf'], 2, '.', ''); ?>,
                      <?php endfor; ?>
              ],
              marker: {
                  enabled: true
              },
              dataLabels: {
                  enabled: false,
                  inside: false,
                  // rotation: 270,
                  // align: 'left',
                  // verticalAlign: 'bottom',
                  // y: -5,
                  style: {
                      fontSize: '9px',
                      fontWeight: 'normal'
                  }
              },
              dashStyle: 'shortdot',
              tooltip: {
                  valueSuffix: ' %'
              }

          }, {
              name: '% PECN',
              type: 'spline',
              color: '#70AD47',
              yAxis: 0,
              data: [
                      <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                          <?php echo number_format($array_gestion_monitores[$array_gestion_monitores_doc[$i]]['pecn'], 2, '.', ''); ?>,
                      <?php endfor; ?>
              ],
              marker: {
                  enabled: true
              },
              dataLabels: {
                  enabled: false,
                  inside: false,
                  // rotation: 270,
                  // align: 'left',
                  // verticalAlign: 'bottom',
                  // y: -5,
                  style: {
                      fontSize: '9px',
                      fontWeight: 'normal'
                  }
              },
              dashStyle: 'shortdot',
              tooltip: {
                  valueSuffix: ' %'
              }

          }, {
              name: '% PENC',
              type: 'spline',
              color: '#264478',
              yAxis: 0,
              data: [
                      <?php for ($i=0; $i < count($array_gestion_monitores_doc); $i++): ?>
                          <?php echo number_format($array_gestion_monitores[$array_gestion_monitores_doc[$i]]['penc'], 2, '.', ''); ?>,
                      <?php endfor; ?>
              ],
              marker: {
                  enabled: true
              },
              dataLabels: {
                  enabled: false,
                  inside: false,
                  // rotation: 270,
                  // align: 'left',
                  // verticalAlign: 'bottom',
                  // y: -5,
                  style: {
                      fontSize: '9px',
                      fontWeight: 'normal'
                  }
              },
              dashStyle: 'shortdot',
              tooltip: {
                  valueSuffix: ' %'
              }

          }],
      });
      
      Highcharts.chart('grafica_monitor_dia', {
          chart: {
              type: 'spline',
              height: 300
          },
          title: {
              text: 'Gestión Diaria Monitor | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
              style: {
                  fontSize: '14px'
              }
          },
          subtitle: {
              text: null
          },
          xAxis: {
              categories: [
                          <?php for ($i=0; $i < count($array_anio_mes_dias_num); $i++): ?>
                              '<?php echo $array_anio_mes_dias_num[$i]; ?>',    
                          <?php endfor; ?>
                          ],
          },
          yAxis: {
              title: {
                  text: 'Cantidad monitoreos'
              }
          },
          tooltip: {
              shared: true,
              style: {
                  fontSize: '10px'
              }
          },
          legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle',
              itemStyle: {
                  fontWeight: 'normal',
                  fontSize: '10px'
              }
          },
          plotOptions: {
              spline: {
                  dataLabels: {
                      enabled: false
                  },
              }
          },
          credits: {
              enabled: false
          },
          series: [
          <?php for ($i=0; $i < count($array_monitor_dia_doc); $i++): ?>
              {
                  name: '<?php echo $array_monitor_dia[$array_monitor_dia_doc[$i]]['nombre']; ?>',
                  data: [
                      <?php for ($j=0; $j < count($array_anio_mes_dias_num); $j++): ?>    
                          <?php echo $array_monitor_dia[$array_monitor_dia_doc[$i]]['monitoreos'][$array_anio_mes_dias[$j]]; ?>,
                      <?php endfor; ?>
                      ]
              },
          <?php endfor; ?>
          ],
          responsive: {
              rules: [{
                  condition: {
                      maxWidth: 500
                  },
                  chartOptions: {
                      legend: {
                          layout: 'horizontal',
                          align: 'center',
                          verticalAlign: 'bottom'
                      }
                  }
              }]
          }
      });

      Highcharts.chart('grafica_matriz', {
          chart: {
              plotBackgroundColor: null,
              plotBorderWidth: null,
              plotShadow: false,
              type: 'pie',
              height: 300,
          },
          credits: {
              enabled: false
          },
          title: {
              text: 'Monitoreos por Matriz',
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
              <?php for ($i=0; $i < count($array_matrices); $i++): ?>
              {
                  name: '<?php echo $array_matrices_detalle[$array_matrices[$i]]['nombre_matriz']; ?>',
                  y: <?php echo $array_matrices_detalle[$array_matrices[$i]]['cantidad']; ?>,
              },
              <?php endfor; ?>
              ]
          }]
      });

      Highcharts.chart('grafica_semana', {
          chart: {
              type: 'spline',
              height: 300
          },
          title: {
              text: '% Participación Monitoreos Semanal | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
              style: {
                  fontSize: '14px'
              }
          },
          subtitle: {
              text: null
          },
          xAxis: {
              categories: ['<?php echo $array_semanas['rango_1']; ?>', '<?php echo $array_semanas['rango_2']; ?>', '<?php echo $array_semanas['rango_3']; ?>', '<?php echo $array_semanas['rango_4']; ?>'],
          },
          yAxis: {
              title: {
                  text: 'Cantidad monitoreos'
              }
          },
          tooltip: {
              shared: true,
              style: {
                  fontSize: '10px'
              }
          },
          legend: {
              layout: 'vertical',
              align: 'center',
              verticalAlign: 'top',
              itemStyle: {
                  fontWeight: 'normal',
                  fontSize: '10px'
              }
          },
          plotOptions: {
              spline: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  dataLabels: {
                      enabled: true,
                      formatter: function() {
                          var total = <?php echo $array_semanas['total_1']; ?> + <?php echo $array_semanas['total_2']; ?> + <?php echo $array_semanas['total_3']; ?> + <?php echo $array_semanas['total_4']; ?>;
                          var porcentaje = (this.y/total)*100;
                          return this.y + ' [' + porcentaje.toFixed(2) + '%]';
                      }
                      // format: '{point.y} [{point.percentage:.1f} %]'
                  }
              }
          },
          credits: {
              enabled: false
          },
          series: [{
                  name: 'Cantidad monitoreos',
                  data: [<?php echo $array_semanas['total_1']; ?>, <?php echo $array_semanas['total_2']; ?>, <?php echo $array_semanas['total_3']; ?>, <?php echo $array_semanas['total_4']; ?>]
              }]
      });

      Highcharts.chart('grafica_monitoreos_matriz', {
          chart: {
              type: 'bar',
              height: <?php echo (count($array_usuarios)<3)? 200 : count($array_usuarios)*50; ?>
          },
          title: {
              text: 'Gestión Monitores por Matriz | <?php echo $array_meses[intval(date('m', strtotime($filtro_permanente)))]; ?> <?php echo date('Y', strtotime($filtro_permanente)); ?>',
              style: {
                  fontSize: '14px'
              }
          },
          subtitle: {
              text: null
          },
          xAxis: {
              categories: [
                          <?php for ($i=0; $i < count($array_usuarios); $i++): ?>
                              '<?php echo $array_usuarios_detalle[$array_usuarios[$i]]['nombre']; ?>',
                          <?php endfor; ?>
              ],
              title: {
                  text: null
              }
          },
          yAxis: {
              min: 0,
              title: {
                  text: 'Cantidad monitoreos',
                  align: 'high'
              },
              labels: {
                  overflow: 'justify'
              }
          },
          tooltip: {
              valueSuffix: ' monitoreos',
              shared: true
          },
          plotOptions: {
              bar: {
                  dataLabels: {
                      enabled: true
                  }
              }
          },
          legend: {
              layout: 'horizontal',
              align: 'center',
              verticalAlign: 'top',
              x: 0,
              y: -20,
              floating: false,
              borderWidth: 0,
              backgroundColor:
                  Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
              shadow: false
          },
          credits: {
              enabled: false
          },
          series: [
              <?php for ($i=0; $i < count($array_matrices); $i++): ?>
                  {
                      name: '<?php echo $array_matrices_detalle[$array_matrices[$i]]['nombre_matriz']; ?>',
                      data: [
                          <?php for ($j=0; $j < count($array_usuarios); $j++): ?>
                              <?php echo $array_usuario_monitoreos[$array_matrices[$i]][$array_usuarios[$j]]; ?>,
                          <?php endfor; ?>
                          ]
                  },
              <?php endfor; ?>
          ]
      });
      <?php endif; ?>
  </script>
</body>
</html>
