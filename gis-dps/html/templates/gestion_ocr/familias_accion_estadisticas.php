<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Gestión";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Gestión | Estadísticas";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  // Inicializa variable tipo array
  $data_consulta=array();

  $array_usuarios=array();
  $array_estados=array();
  $array_estados_nombre=array();
  $array_estados_conteo=array();
  $array_usuarios_detalle=array();
  $array_usuario_casos=array();
   
  $array_estados_nombre[]='Escalado-Cliente';
  $array_estados_nombre[]='Inscrito SIFA';
  $array_estados_nombre[]='Intento Contacto-Fallido';
  $array_estados_nombre[]='Intento Contacto-Agotado';
  $array_estados_nombre[]='Contactado-Pendiente Documentos';
  $array_estados_nombre[]='Pendiente llamada';
  $array_estados_nombre[]='Nuevo Contacto-Error Subsanación';
  $array_estados_nombre[]='Documentos Cargados';
  $array_estados_nombre[]='Escalado-Validar';
  $array_estados_nombre[]='Escalado-Cliente';
  $array_estados_nombre[]='Validado-Agente';
  $array_estados_nombre[]='Inscrito SIFA RPA';

  $array_estados_nombre[]='Intento Contacto-Fallido-Segunda Revisión';
  $array_estados_nombre[]='Intento Contacto-Agotado-Segunda Revisión';
  $array_estados_nombre[]='Contactado-Pendiente Documentos-Segunda Revisión';
  $array_estados_nombre[]='Pendiente llamada-Segunda Revisión';
  $array_estados_nombre[]='Intento Contacto-Fallido-Segunda Revisión';
  $array_estados_nombre[]='Intento Contacto-Agotado-Segunda Revisión';
  $array_estados_nombre[]='Documentos Cargados-Segunda Revisión';
  $array_estados_nombre[]='Contactado-Pendiente Documentos-Segunda Revisión';
  $array_estados_nombre[]='Escalado-Validar-Segunda Revisión';
  $array_estados_nombre[]='Escalado-Cliente-Segunda Revisión';
  $array_estados_nombre[]='Aplazado Tercera Revisión';
  $array_estados_nombre[]='Validado-Agente-Segunda Revisión';
  $array_estados_nombre[]='Validado-Agente-Tercera Revisión';

  $array_estados_conteo['Escalado-Cliente']=0;
  $array_estados_conteo['Inscrito SIFA']=0;
  $array_estados_conteo['Intento Contacto-Fallido']=0;
  $array_estados_conteo['Intento Contacto-Agotado']=0;
  $array_estados_conteo['Contactado-Pendiente Documentos']=0;
  $array_estados_conteo['Pendiente llamada']=0;
  $array_estados_conteo['Nuevo Contacto-Error Subsanación']=0;
  $array_estados_conteo['Documentos Cargados']=0;
  $array_estados_conteo['Escalado-Validar']=0;
  $array_estados_conteo['Escalado-Cliente']=0;
  $array_estados_conteo['Validado-Agente']=0;
  $array_estados_conteo['Inscrito SIFA RPA']=0;
  $array_estados_conteo['Intento Contacto-Fallido-Segunda Revisión']=0;
  $array_estados_conteo['Intento Contacto-Agotado-Segunda Revisión']=0;
  $array_estados_conteo['Contactado-Pendiente Documentos-Segunda Revisión']=0;
  $array_estados_conteo['Pendiente llamada-Segunda Revisión']=0;
  $array_estados_conteo['Intento Contacto-Fallido-Segunda Revisión']=0;
  $array_estados_conteo['Intento Contacto-Agotado-Segunda Revisión']=0;
  $array_estados_conteo['Documentos Cargados-Segunda Revisión']=0;
  $array_estados_conteo['Contactado-Pendiente Documentos-Segunda Revisión']=0;
  $array_estados_conteo['Escalado-Validar-Segunda Revisión']=0;
  $array_estados_conteo['Escalado-Cliente-Segunda Revisión']=0;
  $array_estados_conteo['Aplazado Tercera Revisión']=0;
  $array_estados_conteo['Validado-Agente-Segunda Revisión']=0;
  $array_estados_conteo['Validado-Agente-Tercera Revisión']=0;

  
  $array_hora[]='00';
  $array_hora[]='01';
  $array_hora[]='02';
  $array_hora[]='03';
  $array_hora[]='04';
  $array_hora[]='05';
  $array_hora[]='06';
  $array_hora[]='07';
  $array_hora[]='08';
  $array_hora[]='09';
  $array_hora[]='10';
  $array_hora[]='11';
  $array_hora[]='12';
  $array_hora[]='13';
  $array_hora[]='14';
  $array_hora[]='15';
  $array_hora[]='16';
  $array_hora[]='17';
  $array_hora[]='18';
  $array_hora[]='19';
  $array_hora[]='20';
  $array_hora[]='21';
  $array_hora[]='22';
  $array_hora[]='23';

  $array_hora_total[0]=0;
  $array_hora_total[1]=0;
  $array_hora_total[2]=0;
  $array_hora_total[3]=0;
  $array_hora_total[4]=0;
  $array_hora_total[5]=0;
  $array_hora_total[6]=0;
  $array_hora_total[7]=0;
  $array_hora_total[8]=0;
  $array_hora_total[9]=0;
  $array_hora_total[10]=0;
  $array_hora_total[11]=0;
  $array_hora_total[12]=0;
  $array_hora_total[13]=0;
  $array_hora_total[14]=0;
  $array_hora_total[15]=0;
  $array_hora_total[16]=0;
  $array_hora_total[17]=0;
  $array_hora_total[18]=0;
  $array_hora_total[19]=0;
  $array_hora_total[20]=0;
  $array_hora_total[21]=0;
  $array_hora_total[22]=0;
  $array_hora_total[23]=0;

  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_fecha_inicio=validar_input($_POST['fecha_inicio']);
      $filtro_fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
      $filtro_mes=" AND `ocrr_gestion_fecha`>=? AND `ocrr_gestion_fecha`<=?";
      $filtro_mes_hora=" AND `ocrr_registro_fecha`>=? AND `ocrr_registro_fecha`<=?";
      array_push($data_consulta, $filtro_fecha_inicio);
      array_push($data_consulta, $filtro_fecha_fin);

      $filtro_usuario=validar_input($_POST['usuario']);
      if ($filtro_usuario!="") {
        $filtro_usuario_string=" AND `ocrr_gestion_agente`=?";
        array_push($data_consulta, $filtro_usuario);
      }
  } else {
      $filtro_fecha_inicio=date('Y-m-').'01';
      $filtro_fecha_fin=date("Y-m-t", strtotime($filtro_fecha_inicio)).' 23:59:59';
      $filtro_mes=" AND `ocrr_gestion_fecha`>=? AND `ocrr_gestion_fecha`<=?";
      $filtro_mes_hora=" AND `ocrr_registro_fecha`>=? AND `ocrr_registro_fecha`<=?";
      array_push($data_consulta, $filtro_fecha_inicio);
      array_push($data_consulta, $filtro_fecha_fin);
      $filtro_usuario_string="";
  }

  $consulta_string="SELECT `ocrr_gestion_estado`, `ocrr_gestion_agente`, TAG.`usu_nombres_apellidos`, COUNT(`ocrr_id`) FROM `gestion_ocr_resultado` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` WHERE 1=1 ".$filtro_mes." ".$filtro_usuario_string." GROUP BY `ocrr_gestion_estado`, `ocrr_gestion_agente`";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros); $i++) { 
      $array_usuarios[]=$resultado_registros[$i][1];
      $array_estados[]=$resultado_registros[$i][0];
      $array_usuarios_detalle[$resultado_registros[$i][1]]['nombre']=$resultado_registros[$i][2];
      $array_usuario_casos[$resultado_registros[$i][0]][$resultado_registros[$i][1]]+=$resultado_registros[$i][3];
      $array_estados_conteo[$resultado_registros[$i][0]]+=$resultado_registros[$i][3];
  }

  $array_usuarios=array_values(array_unique($array_usuarios));
  $array_estados=array_values(array_unique($array_estados));

  for ($i=0; $i < count($array_estados); $i++) { 
      for ($j=0; $j < count($array_usuarios); $j++) {
          $array_usuario_casos[$array_estados[$i]][$array_usuarios[$j]]+=0;
      }
  }

  $consulta_string_hora="SELECT HOUR(`ocrr_registro_fecha`), COUNT(`ocrr_id`) FROM `gestion_ocr_resultado` WHERE 1=1 ".$filtro_mes_hora." GROUP BY HOUR(`ocrr_registro_fecha`)";

  $consulta_registros_hora = $enlace_db->prepare($consulta_string_hora);
  $consulta_registros_hora->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros_hora->execute();
  $resultado_registros_hora = $consulta_registros_hora->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_hora); $i++) { 
    $array_hora_total[$resultado_registros_hora[$i][0]]+=$resultado_registros_hora[$i][1];
  }

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' AND `usu_cargo_rol`='AGENTE INSCRIPCIÓN FA' ORDER BY `usu_id`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-md-7 mb-1">
              <form name="filtrado" action="" method="POST">
                <div class="form-group m-0">
                  <div class="input-group">
                    <label for="fecha_inicio" class="me-1 pt-1">Usuario: </label>
                    <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="usuario" id="usuario">
                        <option value="" <?php if($filtro_usuario==""){ echo "selected"; } ?>>Todos</option>
                        <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                          <option class="font-size-11" value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($filtro_usuario==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                        <?php endfor; ?>
                    </select>
                    <label for="fecha_inicio" class="mx-1 pt-1">Inicio: </label>
                    <input type="date" class="form-control form-control-sm" name="fecha_inicio" value='<?php if (isset($_POST["filtro"])) { echo $_POST['fecha_inicio']; } else {if($filtro_fecha_inicio!="null"){echo $filtro_fecha_inicio;}} ?>' required autofocus>
                    <label for="fecha_inicio" class="mx-1 pt-1">Fin: </label>
                    <input type="date" class="form-control form-control-sm" name="fecha_fin" value='<?php if (isset($_POST["filtro"])) { echo $_POST['fecha_fin']; } else {if($filtro_fecha_fin!="null"){echo date('Y-m-d', strtotime($filtro_fecha_fin));}} ?>' required>
                    <div class="input-group-append">
                      <button class="btn py-2 px-2 btn-primary btn-corp" type="submit" name="filtro"><span class="fas fa-search font-size-12"></span></button>
                      <a href="<?php echo $url_fichero; ?>" class="btn py-2 px-2 btn-primary btn-corp"><span class="fas fa-sync-alt font-size-12"></span></a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="col-md-5 mb-1 text-end">
              <a href="familias_accion?pagina=1&id=null&bandeja=<?php echo base64_encode('Pendientes'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes</span>
              </a>
              <a href="familias_accion?pagina=1&id=null&bandeja=<?php echo base64_encode('Revalidación'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Revalidación">
                <i class="fas fa-retweet btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Revalidación</span>
              </a>
              <a href="familias_accion?pagina=1&id=null&bandeja=<?php echo base64_encode('Escalados'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Escalados">
                <i class="fas fa-layer-group btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Escalados</span>
              </a>
              <a href="familias_accion?pagina=1&id=null&bandeja=<?php echo base64_encode('Cerrados'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Cerrados">
                <i class="fas fa-lock btn-icon-prepend me-0 font-size-12"></i><span class="d-none d-lg-inline"></span>
              </a>
              <a href="familias_accion_procesado?pagina=1&id=null&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Procesado">
                <i class="fas fa-cogs btn-icon-prepend me-0 font-size-12"></i>
              </a>
              <a href="familias_accion_consolidado?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Consolidado">
                <i class="fas fa-database btn-icon-prepend me-0 font-size-12"></i>
              </a>
              <a href="familias_accion_estadisticas" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Estadísticas">
                <i class="fas fa-chart-pie btn-icon-prepend me-0 font-size-12"></i>
              </a>
            </div>
            <?php if (count($resultado_registros)>0): ?>
              <div class="col-lg-12 d-flex flex-column mt-2">
                <div class="row flex-grow">
                  <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body py-2">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="statistics-details d-md-flex d-sm-block align-items-center justify-content-between my-2">
                              <div>
                                <h3 class="rate-percentage text-success text-center"><span class="fas fa-file-pdf"></span><br><?php echo number_format($array_estados_conteo['Validado-OCR'], 0, ',', '.'); ?></h3>
                                <p class="statistics-title text-center">Validado OCR</p>
                              </div>
                              <div>
                                <h3 class="rate-percentage text-success text-center"><span class="fas fa-user-clock"></span><br><?php echo number_format($array_estados_conteo['Aplazado'], 0, ',', '.'); ?></h3>
                                <p class="statistics-title text-center">Aplazado</p>
                              </div>
                              <div>
                                <h3 class="rate-percentage text-success text-center"><span class="fas fa-user-check"></span><br><?php echo number_format($array_estados_conteo['Validado-Agente'], 0, ',', '.'); ?></h3>
                                <p class="statistics-title text-center">Validado-Agente</p>
                              </div>
                              <div>
                                <h3 class="rate-percentage text-success text-center"><span class="fas fa-check-double"></span><br><?php echo number_format($array_estados_conteo['Validado-OCR-Segunda Revisión'], 0, ',', '.'); ?></h3>
                                <p class="statistics-title text-center">Validado-OCR-Segunda Revisión</p>
                              </div>
                              <div>
                                <h3 class="rate-percentage text-success text-center"><span class="fas fa-user-clock"></span><br><?php echo number_format($array_estados_conteo['Aplazado Segunda Revisión'], 0, ',', '.'); ?></h3>
                                <p class="statistics-title text-center">Aplazado Segunda Revisión</p>
                              </div>
                              <div>
                                <h3 class="rate-percentage text-success text-center"><span class="fas fa-robot"></span><br><?php echo number_format($array_estados_conteo['Inscrito SIFA RPA'], 0, ',', '.'); ?></h3>
                                <p class="statistics-title text-center">Inscrito SIFA RPA</p>
                              </div>
                            </div>
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
                        <div id="grafica_gestion_estado"></div>
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
                        <div id="grafica_gestion_hora"></div>
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
                        <div id="grafica_gestion_usuario"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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
      Highcharts.chart('grafica_gestion_estado', {
          chart: {
              type: 'bar',
              height: 800
          },
          title: {
              text: 'Gestión por Estado',
              style: {
                  fontSize: '14px'
              }
          },
          subtitle: {
              text: null
          },
          xAxis: {
              categories: [
                          <?php for ($i=0; $i < count($array_estados_nombre); $i++): ?>
                              '<?php echo $array_estados_nombre[$i]; ?>',
                          <?php endfor; ?>
              ],
              title: {
                  text: null
              }
          },
          yAxis: {
              min: 0,
              title: {
                  text: 'Cantidad casos',
                  align: 'high'
              },
              labels: {
                  overflow: 'justify'
              }
          },
          tooltip: {
              valueSuffix: ' casos',
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
              {
                name: 'Casos',
                data: [
                  <?php for ($i=0; $i < count($array_estados_nombre); $i++): ?>
                        <?php echo $array_estados_conteo[$array_estados_nombre[$i]]; ?>,
                  <?php endfor; ?>
                    ]
              },
          ]
      });

      Highcharts.chart('grafica_gestion_hora', {
          chart: {
              type: 'column',
              height: 400
          },
          title: {
              text: 'Gestión OCR por Hora',
              style: {
                  fontSize: '14px'
              }
          },
          subtitle: {
              text: null
          },
          xAxis: {
              categories: [
                          <?php for ($i=0; $i < 24; $i++): ?>
                              '<?php echo $array_hora[$i]; ?>',
                          <?php endfor; ?>
              ],
              title: {
                  text: null
              }
          },
          yAxis: {
              min: 0,
              title: {
                  text: 'Cantidad casos',
                  align: 'middle'
              },
              labels: {
                  overflow: 'justify'
              }
          },
          tooltip: {
              valueSuffix: ' casos',
              shared: true
          },
          plotOptions: {
              column: {
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
              {
                name: 'Casos',
                data: [
                  <?php for ($i=0; $i < count($array_hora_total); $i++): ?>
                        <?php echo $array_hora_total[$i]; ?>,
                  <?php endfor; ?>
                    ]
              },
          ]
      });

      Highcharts.chart('grafica_gestion_usuario', {
          chart: {
              type: 'bar',
              height: <?php echo (count($array_usuarios)<3)? 400 : count($array_usuarios)*90; ?>
          },
          title: {
              text: 'Gestión por Usuario',
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
                  text: 'Cantidad casos',
                  align: 'high'
              },
              labels: {
                  overflow: 'justify'
              }
          },
          tooltip: {
              valueSuffix: ' casos',
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
              <?php for ($i=0; $i < count($array_estados); $i++): ?>
                  {
                      name: '<?php echo $array_estados[$i]; ?>',
                      data: [
                          <?php for ($j=0; $j < count($array_usuarios); $j++): ?>
                              <?php echo $array_usuario_casos[$array_estados[$i]][$array_usuarios[$j]]; ?>,
                          <?php endfor; ?>
                          ]
                  },
              <?php endfor; ?>
          ]
      });

  </script>
</body>
</html>