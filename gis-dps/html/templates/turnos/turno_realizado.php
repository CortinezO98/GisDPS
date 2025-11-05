<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Control Turnos";
  session_start();
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  $permisos_usuario = $_SESSION[APP_SESSION . '_session_modulos'][$modulo_plataforma] ?? null;

// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Turnos";
  $subtitle = "Turno Realizado";
  $parametros_add='';

  // Inicializa variable tipo array
  $data_consulta_usuarios=array();
  $data_consulta_areas=array();
  $data_consulta_turnos=array();

  $filtro_buscar='';
  $filtro_perfil='';
  
  if ($permisos_usuario=="Administrador") {
      $filtro_perfil="";
  } elseif ($permisos_usuario=="Gestor") {
      $filtro_perfil="AND (`usu_supervisor`=? OR `usu_id`=?)";
      array_push($data_consulta_areas, $_SESSION[APP_SESSION.'_session_usu_id']);
      array_push($data_consulta_areas, $_SESSION[APP_SESSION.'_session_usu_id']);

      array_push($data_consulta_usuarios, $_SESSION[APP_SESSION.'_session_usu_id']);
      array_push($data_consulta_usuarios, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Usuario"){
      $filtro_perfil=" AND (`usu_id`=?)";
      array_push($data_consulta_areas, $_SESSION[APP_SESSION.'_session_usu_id']);
      
      array_push($data_consulta_usuarios, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  $consulta_string_areas="SELECT DISTINCT `usu_campania`, TC.`ac_nombre_campania` FROM `administrador_usuario` LEFT JOIN `administrador_campania` AS TC ON `administrador_usuario`.`usu_campania`=TC.`ac_id` WHERE 1=1 AND `usu_estado`='Activo' AND `usu_id`<>'1111111111' ".$filtro_perfil." ORDER BY TC.`ac_nombre_campania` ASC";
  $consulta_registros_areas = $enlace_db->prepare($consulta_string_areas);
  if (count($data_consulta_areas)>0) {
      $consulta_registros_areas->bind_param(str_repeat("s", count($data_consulta_areas)), ...$data_consulta_areas);
  }
  $consulta_registros_areas->execute();
  $resultado_registros_areas = $consulta_registros_areas->get_result()->fetch_all(MYSQLI_NUM);
  
  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_permanente=validar_input($_POST['id_filtro']);
      $FechaInicio = validar_input($_POST["filtro_fecha"]);
      $filtro_operacion = validar_input($_POST["operacion"]);
  } else {
      $filtro_permanente=validar_input($_GET['id']);
      $FechaInicio = validar_input(base64_decode($_GET['fechainicio']));
      $filtro_operacion = validar_input(base64_decode($_GET["operacion"]));
  }

  if ($filtro_operacion!='Todos') {
      $filtro_usuario_operacion='AND (`usu_campania`=?)';
      array_push($data_consulta_usuarios, $filtro_operacion);
  } else {
      $filtro_usuario_operacion='';
  }

  // Valida que filtro se deba ejecutar
  if ($filtro_permanente!="null" AND $filtro_permanente!="") {
      $filtro_buscar="AND (`usu_id` LIKE ? OR `usu_nombres_apellidos` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta_usuarios, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  $consulta_string_usuarios="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_cargo_rol`, `usu_estado`, `usu_piloto` FROM `administrador_usuario` LEFT JOIN `administrador_campania` AS TCA ON `administrador_usuario`.`usu_campania`=TCA.`ac_id` WHERE 1=1 AND `usu_estado`='Activo' AND `usu_id`<>'1111111111' ".$filtro_perfil." ".$filtro_usuario_operacion." ".$filtro_buscar." ORDER BY `usu_nombres_apellidos` ASC";
  $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
  if (count($data_consulta_usuarios)>0) {
      $consulta_registros_usuarios->bind_param(str_repeat("s", count($data_consulta_usuarios)), ...$data_consulta_usuarios);
  }
  $consulta_registros_usuarios->execute();
  $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

  $filtro_usuarios='';
  array_push($data_consulta_turnos, "$FechaInicio%");
  if (count($resultado_registros_usuarios)>0) {
      for ($i=0; $i < count($resultado_registros_usuarios); $i++) { 
          $filtro_usuarios.='`cot_usuario`=? OR ';
          array_push($data_consulta_turnos, $resultado_registros_usuarios[$i][0]);
      }
      $filtro_usuarios='AND ('.substr($filtro_usuarios, 0, -4).')';
  }

  $consulta_string_turno_realizado="SELECT `cot_id`, `cot_usuario`, `cot_tipo`, `cot_inicio`, `cot_fin`, `cot_duracion`, `cot_fuente`, `cot_observaciones_inicio`, `cot_observaciones_fin`, `cot_registro_fecha`, TU.`usu_nombres_apellidos` FROM `control_turno` LEFT JOIN `administrador_usuario`AS TU ON `control_turno`.`cot_usuario`=TU.`usu_id` WHERE 1=1 AND `cot_inicio` LIKE ? ".$filtro_usuarios." ORDER BY `cot_id` ASC";

  $consulta_registros_turno_realizado = $enlace_db->prepare($consulta_string_turno_realizado);
  if (count($data_consulta_turnos)>0) {
      $consulta_registros_turno_realizado->bind_param(str_repeat("s", count($data_consulta_turnos)), ...$data_consulta_turnos);
  }
  $consulta_registros_turno_realizado->execute();
  $resultado_registros_turno_realizado = $consulta_registros_turno_realizado->get_result()->fetch_all(MYSQLI_NUM);

  if (count($resultado_registros_turno_realizado)>0) {
      $fecha_actual=date("Y-m-d H:i:s");
      for ($i=0; $i < count($resultado_registros_turno_realizado); $i++) {

          if ($resultado_registros_turno_realizado[$i][4]=='') {
              $array_turnos[$resultado_registros_turno_realizado[$i][1]]['actual']=$resultado_registros_turno_realizado[$i][2];
          }

          $array_turnos_id[$resultado_registros_turno_realizado[$i][1]][]=$resultado_registros_turno_realizado[$i][0];
          $array_turnos[$resultado_registros_turno_realizado[$i][1]][$resultado_registros_turno_realizado[$i][0]]['tipo']=$resultado_registros_turno_realizado[$i][2];
          $array_turnos[$resultado_registros_turno_realizado[$i][1]][$resultado_registros_turno_realizado[$i][0]]['inicio']=$resultado_registros_turno_realizado[$i][3];
          $array_turnos[$resultado_registros_turno_realizado[$i][1]][$resultado_registros_turno_realizado[$i][0]]['fin']=$resultado_registros_turno_realizado[$i][4];
          if ($resultado_registros_turno_realizado[$i][5]!="") {
              $array_turnos[$resultado_registros_turno_realizado[$i][1]][$resultado_registros_turno_realizado[$i][0]]['duracion']=$resultado_registros_turno_realizado[$i][5];
              $array_turnos[$resultado_registros_turno_realizado[$i][1]][$resultado_registros_turno_realizado[$i][2]]['duracion_total']+=$resultado_registros_turno_realizado[$i][5]+0;
          } else {
              if (date('Y-m-d', strtotime($resultado_registros_turno_realizado[$i][3]))==date("Y-m-d")) {
                  $duracion = dateDiff($resultado_registros_turno_realizado[$i][3],$fecha_actual);
              } else {
                  $fecha_cierre=date('Y-m-d', strtotime($resultado_registros_turno_realizado[$i][3])).' 23:59:59';
                  $duracion = dateDiff($resultado_registros_turno_realizado[$i][3],$fecha_cierre);
              }
              $array_turnos[$resultado_registros_turno_realizado[$i][1]][$resultado_registros_turno_realizado[$i][0]]['duracion']=$duracion;
              $array_turnos[$resultado_registros_turno_realizado[$i][1]][$resultado_registros_turno_realizado[$i][2]]['duracion_total']+=$duracion+0;
          }
      }
  }
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <?php //require_once(ROOT.'includes/_head-charts.php'); ?>
  <script src="<?php echo PLUGINS; ?>HighchartsGantt/code/highcharts-gantt.js"></script>
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
            <div class="col-md-6 mb-1">
              <form name="filtrado" action="" method="POST">
                <div class="form-group m-0">
                  <div class="input-group">
                    <select class="form-control form-control-sm form-select font-size-11" name="operacion" id="operacion" required style="max-width: 300px;">
                        <option value="Todos" <?php if($filtro_operacion=="Todos"){ echo "selected"; } ?>>Todos</option>
                        <?php for ($i=0; $i < count($resultado_registros_areas); $i++): ?>
                            <option value="<?php echo $resultado_registros_areas[$i][0]; ?>" <?php if($filtro_operacion==$resultado_registros_areas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_areas[$i][1]; ?></option>
                        <?php endfor; ?>
                    </select>
                    <input type="text" class="form-control form-control-sm" name="id_filtro" value='<?php if (isset($_POST["filtro"])) { echo $_POST['id_filtro']; } else {if($filtro_permanente!="null"){echo $filtro_permanente;}} ?>' placeholder="Búsqueda">
                    <input type="date" class="form-control form-control-sm" name="filtro_fecha" value='<?php echo $FechaInicio; ?>' placeholder="Búsqueda" required autofocus>
                    <div class="input-group-append">
                      <button class="btn py-2 px-2 btn-primary btn-corp" type="submit" name="filtro"><span class="fas fa-search font-size-12"></span></button>
                      <a href="<?php echo $url_fichero; ?>?fechainicio=<?php echo base64_encode($FechaInicio);?>&operacion=<?php echo base64_encode('Todos'); ?>&id=null" class="btn py-2 px-2 btn-primary btn-corp"><span class="fas fa-sync-alt font-size-12"></span></a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="col-md-6 mb-1 text-end">
              <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                  <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                </button>
              <?php endif; ?>
            </div>
            <div class="col-md-12">
              <div class="btn px-2 py-1 ms-1 font-size-11" style="background-color: #1E8449; color: #FFF;"><span class="fas fa-user-clock"></span> Turno</div>
              <div class="btn px-2 py-1 ms-1 font-size-11" style="background-color: #2874A6; color: #FFF;"><span class="fas fa-utensils"></span> Almuerzo</div>
              <div class="btn px-2 py-1 ms-1 font-size-11" style="background-color: #F1C40F; color: #FFF;"><span class="fas fa-coffee"></span> Break</div>
              <div class="btn px-2 py-1 ms-1 font-size-11" style="background-color: #B03A2E; color: #FFF;"><span class="fas fa-walking"></span> Pausa Activa</div>
              <div class="btn px-2 py-1 ms-1 font-size-11" style="background-color: #6C3483; color: #FFF;"><span class="fas fa-chalkboard-teacher"></span> Capacitación</div>
              <div class="btn px-2 py-1 ms-1 font-size-11" style="background-color: #1ABC9C; color: #FFF;"><span class="fas fa-retweet"></span> Retroalimentación</div>
            </div>
            <div class="col-lg-12 pt-1">
              <?php if (count($resultado_registros_usuarios)>0): ?>
                <div id="container" style="border-radius: 10px;"></div>
              <?php endif; ?>
              <?php if(count($resultado_registros_usuarios)==0): ?>
                <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- modal reportes -->
        <?php require_once('turno_realizado_reporte.php'); ?>
        <!-- modal -->
        <!-- MODAL CIERRE TURNO -->
        <!-- Modal -->
        <div class="modal fade" id="modal-cierre-turno" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Cierre de turno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body-cierre-turno">
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger py-2 px-2" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" name="reporte" class="btn btn-primary btn-corp py-2 px-2" id="btnEnviar" onclick="guardar_info();">Guardar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- MODAL CIERRE TURNO -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
      function open_modal_turno(id_registro, fecha_turno) {
          var myModal = new bootstrap.Modal(document.getElementById("modal-cierre-turno"), {});
          $('.modal-body-cierre-turno').load('turno_realizado_cerrar.php?reg='+id_registro+'&fecha_turno='+fecha_turno,function(){
              myModal.show();
          });
      }

      function close_modal_turno() {
          $('.modal-body-cierre-turno').html('');
          window.location.reload();
      }
  </script>
  <script type="text/javascript">
      var map = Highcharts.map,
      series,
      usuarios;

      Highcharts.setOptions({
          lang: {
              months: [
                  'Enero', 'Febrero', 'Marzo', 'Abril',
                  'Mayo', 'Junio', 'Julio', 'Agosto',
                  'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
              ],
              shortMonths: [
                  'Enero', 'Febrero', 'Marzo', 'Abril',
                  'Mayo', 'Junio', 'Julio', 'Agosto',
                  'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
              ],
              weekdays: [
                  'Domingo', 'Lunes', 'Martes', 'Miércoles',
                  'Jueves', 'Viernes', 'Sábado'
              ],
          }
      });
      <?php
          $fecha_validar=$FechaInicio;
          $mes_validar=date("m", strtotime($fecha_validar))-1;
          $resultado_fecha=date("Y,", strtotime($fecha_validar)).$mes_validar.",".date("d", strtotime($fecha_validar));
      ?>
      usuarios = [
          <?php for ($j=0; $j < count($resultado_registros_usuarios); $j++): ?>
              <?php
                  $id_usuario=$resultado_registros_usuarios[$j][0];
              ?>
              {
                  nombre: '<?php echo $resultado_registros_usuarios[$j][1]; ?>',
                  id: '<?php echo base64_encode($resultado_registros_usuarios[$j][0]); ?>',
                  fecha_turno: '<?php echo base64_encode($FechaInicio); ?>',
                  duracion_turno: '<?php echo ($array_turnos[$id_usuario]['turno']['duracion_total']>0) ? conversorSegundosHoras_ns($array_turnos[$id_usuario]['turno']['duracion_total']) : ''; ?>',
                  duracion_break: '<?php echo ($array_turnos[$id_usuario]['break']['duracion_total']>0) ? conversorSegundosHoras_ns($array_turnos[$id_usuario]['break']['duracion_total']) : ''; ?>',
                  duracion_almuerzo: '<?php echo ($array_turnos[$id_usuario]['almuerzo']['duracion_total']>0) ? conversorSegundosHoras_ns($array_turnos[$id_usuario]['almuerzo']['duracion_total']) : ''; ?>',
                  duracion_pausa: '<?php echo ($array_turnos[$id_usuario]['pausaactiva']['duracion_total']>0) ? conversorSegundosHoras_ns($array_turnos[$id_usuario]['pausaactiva']['duracion_total']) : ''; ?>',
                  duracion_capacitacion: '<?php echo ($array_turnos[$id_usuario]['capacitacion']['duracion_total']>0) ? conversorSegundosHoras_ns($array_turnos[$id_usuario]['capacitacion']['duracion_total']) : ''; ?>',
                  duracion_retroalimentacion: '<?php echo ($array_turnos[$id_usuario]['retroalimentacion']['duracion_total']>0) ? conversorSegundosHoras_ns($array_turnos[$id_usuario]['retroalimentacion']['duracion_total']) : ''; ?>',
                  current: 0,
                  deals: [
                  <?php if(isset($array_turnos_id[$id_usuario])): ?>
                      <?php for ($k=0; $k < count($array_turnos_id[$id_usuario]); $k++): ?>
                          <?php
                              $turno_id=$array_turnos_id[$id_usuario][$k];
                              $turno_tipo=$array_nombres_turnos[$array_turnos[$id_usuario][$turno_id]['tipo']];
                              $turno_duracion=$array_turnos[$id_usuario][$turno_id]['duracion'];
                              $turno_inicio=$array_turnos[$id_usuario][$turno_id]['inicio'];
                              $turno_fin=$array_turnos[$id_usuario][$turno_id]['fin'];
                              $turno_color=$array_colores_turnos[$array_turnos[$id_usuario][$turno_id]['tipo']];
                              $turno_icono=$array_iconos_turnos[$array_turnos[$id_usuario][$turno_id]['tipo']];
                          ?>
                          {
                              tipo: '<?php echo $turno_tipo; ?>',
                              from: Date.UTC(<?php echo formatear_fecha_grafica($turno_inicio); ?>),
                              to: Date.UTC(<?php echo formatear_fecha_grafica_fin($turno_fin, $turno_inicio); ?>),
                              duracion: '<?php echo $turno_duracion; ?>',
                              color: '<?php echo $turno_color; ?>',
                              fontSymbol: '<?php echo $turno_icono; ?>'
                          },
                      <?php endfor; ?>
                  <?php else: ?>
                      {
                          tipo: '',
                          from: '',
                          to: '',
                          duracion: '',
                          color: '',
                          fontSymbol: ''
                      },
                  <?php endif; ?>
                  ]
              },
          <?php endfor; ?>
      ];

      // Parse car data into series.
      series = usuarios.map(function (usuario, i) {
          var data = usuario.deals.map(function (deal) {
              return {
                  id: 'deal-' + i,
                  tipo: deal.tipo,
                  start: deal.from,
                  end: deal.to,
                  color: deal.color,
                  fontSymbol: deal.fontSymbol,
                  y: i
              };
          });
          return {
              name: usuario.nombre,
              id_usuario: usuario.id,
              fecha_turno: usuario.fecha_turno,
              duracion_turno: usuario.duracion_turno,
              duracion_break: usuario.duracion_break,
              duracion_almuerzo: usuario.duracion_almuerzo,
              duracion_pausa: usuario.duracion_pausa,
              duracion_capacitacion: usuario.duracion_capacitacion,
              duracion_retroalimentacion: usuario.duracion_retroalimentacion,
              data: data,
              dataLabels: [{
                  enabled: true,
                  format: '<div style="width: 15px; height: 15px; overflow: hidden;">' +
                      '<span class="fas fa-{point.fontSymbol} "></span></div>',
                  useHTML: true,
                  align: 'left'
              }],
              point: {
                   events: {
                      click: function() {
                          open_modal_turno(usuario.id, usuario.fecha_turno);
                      }
                  }
              },
              current: usuario.deals[usuario.current]
          };
      });

      Highcharts.ganttChart('container', {
          series: series,
          title: {
              text: null
          },
          tooltip: {
              pointFormat: '<span><b>{point.tipo}</b></span><br/><span><b>Inicio:</b> {point.start:%H:%M:%S, %e %b %Y}</span><br/><span><b>Fin:</b> {point.end:%H:%M:%S, %e %b %Y}</span>'
          },
          xAxis: [{
                  min: Date.UTC(<?php echo $resultado_fecha; ?>, 7, 0, 0),
                  max: Date.UTC(<?php echo $resultado_fecha; ?>, 21, 0, 0),
                  grid: {
                      cellHeight: 50
                  },
                  labels: {
                      align: 'center',
                      style: {
                          fontSize: '10px'
                      }
                  },
                  tickInterval: 1000 * 60 * 60,
              }, {
                  // Set the second axis to have a height of 60px
                  grid: {
                      cellHeight: 50
              }
          }],
          credits: false,
          yAxis: {
              staticScale: 25,
              labels: {
                  align: 'left',
                  useHTML: true,
                  style: {
                      fontSize: '10px',
                      width: '200px',
                      padding: '2px'
                  }
              },
              type: 'category',
              grid: {
                  columns: [{
                      title: {
                          text: 'Usuario',
                          style: {
                              fontSize: '9px',
                          }
                      },
                      categories: map(series, function (s) {
                          return s.name;
                      })
                  }, {
                      title: {
                          text: 'Turno',
                          align: 'left',
                          style: {
                              fontSize: '9px'
                          },
                          rotation: -90,
                      },
                      categories: map(series, function (s) {
                          return s.duracion_turno;
                      })
                  }, {
                      title: {
                          text: 'Break',
                          align: 'left',
                          style: {
                              fontSize: '9px'
                          },
                          rotation: -90,
                      },
                      categories: map(series, function (s) {
                          return s.duracion_break;
                      })
                  }, {
                      title: {
                          text: 'Almuerzo',
                          align: 'left',
                          style: {
                              fontSize: '9px'
                          },
                          rotation: -90,
                      },
                      categories: map(series, function (s) {
                          return s.duracion_almuerzo;
                      })
                  }, {
                      title: {
                          text: 'Pausa Activa',
                          align: 'left',
                          style: {
                              fontSize: '9px'
                          },
                          rotation: -90,
                      },
                      categories: map(series, function (s) {
                          return s.duracion_pausa;
                      })
                  }, {
                      title: {
                          text: 'Capacitación',
                          align: 'left',
                          style: {
                              fontSize: '9px'
                          },
                          rotation: -90,
                      },
                      categories: map(series, function (s) {
                          return s.duracion_capacitacion;
                      })
                  }, {
                      title: {
                          text: 'Retroalimentación',
                          align: 'left',
                          style: {
                              fontSize: '9px',
                          },
                          rotation: -90,
                      },
                      categories: map(series, function (s) {
                          return s.duracion_retroalimentacion;
                      })
                  }]
              }
          }
      });
  </script>
</body>
</html>
