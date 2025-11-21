<?php
  session_start();
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Calculadora Muestral";
  require_once("../../iniciador.php");
  require_once("../../app/functions/validar_festivos.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Calculadora Muestral | Configuración";
  $parametros_add='';
  $permisos_usuario = $_SESSION[APP_SESSION . '_session_modulos'][$modulo_plataforma] ?? null;
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $fecha_calculadora=validar_input($_GET['date']);
  unset($_SESSION[APP_SESSION.'registro_creado_fecha']);
  unset($_SESSION[APP_SESSION.'registro_cargue_base_transacciones']);
  unset($_SESSION[APP_SESSION.'calculadora_transacciones_eliminado']);

  // Inicializa variable tipo array
  $data_consulta_segmento=array();
  $data_consulta_segmento_2=array();
  $array_meses=[1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril", 5=>"Mayo", 6=>"Junio", 7=>"Julio", 8=>"Agosto", 9=>"Septiembre", 10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre"];

  $consulta_string="SELECT `cm_id`, `cm_nombre`, `cm_intervalo_confianza`, `cm_valor_z`, `cm_varianza_estimada`, `cm_error_muestral`, `cm_registro_usuario`, `cm_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_calidad_cmuestral` LEFT JOIN `administrador_usuario` AS TU ON `gestion_calidad_cmuestral`.`cm_registro_usuario`=TU.`usu_id` WHERE `cm_id`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
  $consulta_string_segmento="SELECT `cms_id`, `cms_calculadora`, `cms_nombre_segmento`, `cms_peso` FROM `gestion_calidad_cmuestral_segmento` WHERE `cms_calculadora`=? ORDER BY `cms_nombre_segmento` ASC";

  $consulta_registros_segmento = $enlace_db->prepare($consulta_string_segmento);
  $consulta_registros_segmento->bind_param("s", $id_registro);
  $consulta_registros_segmento->execute();
  $resultado_registros_segmento = $consulta_registros_segmento->get_result()->fetch_all(MYSQLI_NUM);

  $filtro_segmento_2="";
  array_push($data_consulta_segmento_2, $fecha_calculadora);
  for ($i=0; $i < count($resultado_registros_segmento); $i++) { 
      $filtro_segmento_2.="`ccmm_segmento`=? OR ";
      array_push($data_consulta_segmento_2, $resultado_registros_segmento[$i][0]);//Se agrega llave por ser variable evaluada en un like
  }

  $filtro_segmento_2=" AND (".substr($filtro_segmento_2, 0, -4).")";
  if (count($resultado_registros_segmento)>0) {
      $consulta_string_fechas="SELECT DISTINCT SUBSTR(`cmm_mes`, 1, 7) AS FECHA FROM `gestion_calidad_cmuestral_mensual` WHERE `cmm_calculadora`=? ORDER BY FECHA ASC";
      $consulta_registros_fechas = $enlace_db->prepare($consulta_string_fechas);
      $consulta_registros_fechas->bind_param("s", $id_registro);
      $consulta_registros_fechas->execute();
      $resultado_registros_fechas = $consulta_registros_fechas->get_result()->fetch_all(MYSQLI_NUM);

      for ($i=0; $i < count($resultado_registros_fechas); $i++) {
          $detalle_mes=explode('-', $resultado_registros_fechas[$i][0]);
          $mes_num=$detalle_mes[1]*1;
          $fechas_array[]=$detalle_mes[0].'-'.$array_meses[$mes_num];
          $fechas_array_link[]=$resultado_registros_fechas[$i][0];
      }

      $consulta_string_control="SELECT `gcmt_fecha`, `gcmt_segmento`, `gcmt_estado`, COUNT(`gcmt_transaccion_id`), `gcmt_mes` FROM `gestion_calidad_cmuestral_transacciones` WHERE `gcmt_calculadora`=? AND `gcmt_mes`=? GROUP BY `gcmt_fecha`, `gcmt_segmento`, `gcmt_estado`, `gcmt_mes`";
      $consulta_registros_control = $enlace_db->prepare($consulta_string_control);
      $consulta_registros_control->bind_param("ss", $id_registro, $fecha_calculadora);
      $consulta_registros_control->execute();
      $resultado_registros_control = $consulta_registros_control->get_result()->fetch_all(MYSQLI_NUM);

      for ($i=0; $i < count($resultado_registros_control); $i++) {
          $array_control[$resultado_registros_control[$i][0]][$resultado_registros_control[$i][2]]=$resultado_registros_control[$i][3];

          $array_aleatorio[$resultado_registros_control[$i][0]][$resultado_registros_control[$i][1]][$resultado_registros_control[$i][2]]=$resultado_registros_control[$i][3];

          if ($resultado_registros_control[$i][2]=='seleccionable' OR $resultado_registros_control[$i][2]=='auditoria' OR $resultado_registros_control[$i][2]=='auditoria_dian') {
              $array_aleatorio[$resultado_registros_control[$i][0]][$resultado_registros_control[$i][1]]['seleccionable_total']+=$resultado_registros_control[$i][3];
          }

          if ($resultado_registros_control[$i][2]=='auditoria') {
              $array_aleatorio[$resultado_registros_control[$i][0]][$resultado_registros_control[$i][1]]['auditoria_proveedor']+=$resultado_registros_control[$i][3];
          }
      }

      $consulta_string_semanas="SELECT DISTINCT `cmm_mes` FROM `gestion_calidad_cmuestral_mensual` WHERE `cmm_calculadora`=? ORDER BY `cmm_mes` ASC";
      $consulta_registros_semanas = $enlace_db->prepare($consulta_string_semanas);
      $consulta_registros_semanas->bind_param("s", $id_registro);
      $consulta_registros_semanas->execute();
      $resultado_registros_semanas = $consulta_registros_semanas->get_result()->fetch_all(MYSQLI_NUM);

      for ($i=0; $i < count($resultado_registros_semanas); $i++) { 
          $array_semanas[substr($resultado_registros_semanas[$i][0], 0, 7)][]=$resultado_registros_semanas[$i][0];
      }

      $consulta_string_semanas_fecha="SELECT DISTINCT `cmm_mes`, `cmm_semana_inicio`, `cmm_semana_fin` FROM `gestion_calidad_cmuestral_mensual` WHERE `cmm_calculadora`=? AND `cmm_mes`=? ORDER BY `cmm_mes` ASC";
      $consulta_registros_semanas_fecha = $enlace_db->prepare($consulta_string_semanas_fecha);
      $consulta_registros_semanas_fecha->bind_param("ss", $id_registro, $fecha_calculadora);
      $consulta_registros_semanas_fecha->execute();
      $resultado_registros_semanas_fecha = $consulta_registros_semanas_fecha->get_result()->fetch_all(MYSQLI_NUM);

      if (count($resultado_registros_semanas_fecha)>0) {
        $dia_inicio = $resultado_registros_semanas_fecha[0][1];
        $dia_control=$dia_inicio;
        $dia_final=$resultado_registros_semanas_fecha[0][2];
        $dias_habiles=0;
        while ($dia_control<=$dia_final) {
            $numero_dia=date("N", strtotime($dia_control));
            $festivo=validarFestivo($dia_control);
            if ($numero_dia>=1 AND $numero_dia<6 AND $festivo=='') {
                $array_dias_mes[]=$dia_control;
                $dias_habiles++;
            }
            $dia_control = date("Y-m-d", strtotime("+ 1 day", strtotime($dia_control)));
        }
      }
  }
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only"  onresize="headerFixTable();" onload="headerFixTable();">
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
          <div class="row justify-content-center">
            <div class="col-lg-3 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                          <div class="col-md-12 fw-bold font-size-12">
                              <?php echo $resultado_registros[0][1]; ?>
                          </div>
                          <div class="col-md-12">
                              <hr class="my-1">
                              <span class="fas fa-clipboard-list" title="Segmentos"></span> <b>Segmentos:</b><br>
                              <ul>
                              <?php for ($i=0; $i < count($resultado_registros_segmento); $i++): ?>
                                  <li><?php echo $resultado_registros_segmento[$i][2]; ?></li> 
                              <?php endfor; ?>
                              </ul>
                              <hr class="my-1">
                              <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                                  <a href="cmuestral_configurar_fecha_crear.php?reg=<?php echo base64_encode($id_registro); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Crear fecha"><i class="fas fa-plus font-size-11"></i></a>
                              <?php endif; ?>
                              <span class="fas fa-calendar-alt" title="Fechas"></span> Fechas:<br>
                              <?php for ($m=0; $m < count($fechas_array); $m++): ?>
                                  <div class="ml-3 mt-2">
                                      <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                                          <!-- <a href="cmuestral_configurar_fecha_editar.php?reg=<?php echo base64_encode($id_registro); ?>" class="color-corporativo font-size-11" title="Editar fecha"><span class="fas fa-pen"></span></a> -->
                                      <?php endif; ?>
                                      <b><span class="fas fa-calendar-check"></span> <?php echo $fechas_array[$m]; ?></b><br>
                                      <?php for ($i=0; $i < count($array_semanas[$fechas_array_link[$m]]); $i++): ?>
                                          <a href="cmuestral_configurar?reg=<?php echo base64_encode($id_registro); ?>&date=<?php echo $array_semanas[$fechas_array_link[$m]][$i]; ?>" class="btn py-1 px-2 my-1 btn-icon-text btn-outline-success font-size-12" title="Crear Calculadora">
                                            <span class="d-lg-inline">S<?php echo $i+1; ?></span>
                                          </a>
                                      <?php endfor; ?>
                                  </div>
                              <?php endfor; ?>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-9 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row justify-content-center">
                          <div class="col-md-4">
                            <?php if($fecha_calculadora!=""): ?>
                                <div class="float-start fw-bold font-size-12">
                                    Fecha: <span class="fas fa-calendar-check"></span> <?php echo $fecha_calculadora; ?>
                                </div>
                            <?php endif; ?>
                          </div>
                          <div class="col-md-8">
                            <a href="cmuestral?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 float-end" title="Regresar">
                              <i class="fas fa-arrow-left btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Regresar</span>
                            </a>
                          </div>
                      </div>
                      <?php if ($fecha_calculadora==""): ?>
                          <div class="col-md-12 pt-2">
                              <p class="alert alert-warning col-md-12 p-1 font-size-11">
                                  <span class="fas fa-exclamation-triangle"></span> Por favor seleccione una fecha
                              </p>
                          </div>
                      <?php endif; ?>
                      <?php if ($fecha_calculadora!=""): ?>
                          <div class="col-md-12 pt-2 pl-1 pt-0">
                              <?php for ($i=0; $i < count($array_dias_mes); $i++): ?>
                                  <div class="col-md-12 border mb-2 py-1 context fondo-blanco">
                                      <div class="titulo-seccion-conocimiento px-1">
                                          <?php if(!isset($array_control[$array_dias_mes[$i]])): ?>
                                              <a href="cmuestral_configurar_transacciones_cargar.php?reg=<?php echo base64_encode($id_registro); ?>&date=<?php echo $fecha_calculadora; ?>&fecha=<?php echo base64_encode($array_dias_mes[$i]); ?>" class="btn btn-dark btn-icon px-1 py-1 mb-1" title="Cargar transacciones"><i class="fas fa-qrcode font-size-11"></i></a>
                                          <?php endif; ?>
                                          <?php if(isset($array_control[$array_dias_mes[$i]])): ?>
                                              <a href="cmuestral_configurar_transacciones_eliminar.php?reg=<?php echo base64_encode($id_registro); ?>&date=<?php echo $fecha_calculadora; ?>&fecha=<?php echo base64_encode($array_dias_mes[$i]); ?>" class="btn btn-danger btn-icon px-1 py-1 mb-1" title="Eliminar transacciones"><i class="fas fa-qrcode font-size-11"></i></a>
                                          <?php endif; ?>
                                          
                                          <?php if(isset($array_control[$array_dias_mes[$i]])): ?>
                                              <a href="cmuestral_configurar_base_auditoria_excel.php?reg=<?php echo base64_encode($id_registro); ?>&date=<?php echo $fecha_calculadora; ?>&fecha=<?php echo base64_encode($array_dias_mes[$i]); ?>" class="btn px-1 py-1 mb-1 btn-primary btn-corp btn-icon-text" title="Auditoría">
                                                <i class="fas fa-file-excel btn-icon-prepend me-lg-1 font-size-11"></i><span class="d-lg-inline">Auditoría</span>
                                              </a>
                                          <?php endif; ?>
                                          
                                          <span class="fas fa-calendar-alt"></span> <?php echo $array_dias[date("N", strtotime($array_dias_mes[$i]))].', '.date("d", strtotime($array_dias_mes[$i])).' de '.$array_meses[intval(date("m", strtotime($array_dias_mes[$i])))].' '.date("Y", strtotime($array_dias_mes[$i])); ?>
                                          <?php if (isset($array_control[$array_dias_mes[$i]])): ?>
                                              <!-- <a href="#"  onclick="mostrar_ocultar_contenido('n_<?php echo $array_dias_mes[$i]; ?>');" class="btn py-1 px-1 btn-primary btn-corp float-end "><span class="fas fa-chevron-down" id="n_<?php echo $array_dias_mes[$i]; ?>_icono"></span></a> -->
                                          <?php endif; ?>
                                      </div>
                                      <?php if (isset($array_control[$array_dias_mes[$i]])): ?>
                                          <!-- <div class="table-responsive table-fixed d-none" id="n_<?php echo $array_dias_mes[$i]; ?>">
                                              <table class="table table-bordered table-striped table-hover table-sm">
                                                  <thead>
                                                      <tr>
                                                          <th class="align-middle">Segmento</th>
                                                          <th class="align-middle">Auditoria</th>
                                                          <th class="align-middle">Seleccionable</th>
                                                          <th class="align-middle">No seleccionable usuario</th>
                                                          <th class="align-middle">Excluido fecha piloto</th>
                                                          <th class="align-middle">No seleccionable</th>
                                                      </tr>
                                                  </thead>    
                                                  <tbody>    
                                                      <?php
                                                          for ($j=0; $j < count($resultado_registros_segmento); $j++) {
                                                      ?>
                                                      <tr>
                                                          <td class="align-middle"><?php echo $resultado_registros_segmento[$j][2]; ?></td>
                                                          <td class="align-middle text-center"><?php echo $array_aleatorio[$array_dias_mes[$i]][$resultado_registros_segmento[$j][0]]['auditoria']; ?></td>
                                                          <td class="align-middle text-center"><?php echo $array_aleatorio[$array_dias_mes[$i]][$resultado_registros_segmento[$j][0]]['seleccionable_total']; ?></td>
                                                          <td class="align-middle text-center"><?php echo $array_aleatorio[$array_dias_mes[$i]][$resultado_registros_segmento[$j][0]]['excluido_usuario']; ?></td>
                                                          <td class="align-middle text-center"><?php echo $array_aleatorio[$array_dias_mes[$i]][$resultado_registros_segmento[$j][0]]['excluido_fecha']; ?></td>
                                                          <td class="align-middle text-center"><?php echo $array_aleatorio[$array_dias_mes[$i]][$resultado_registros_segmento[$j][0]]['no_seleccionable']; ?></td>
                                                      </tr>
                                                      <?php
                                                          }
                                                      ?>
                                                  </tbody>
                                              </table>
                                          </div> -->
                                      <?php else: ?>
                                          <!-- <p class="alert alert-warning font-size-11 p-1">
                                              <span class="fas fa-exclamation-triangle"></span> No se encontraron registros
                                          </p> -->
                                      <?php endif; ?>
                                  </div>
                              <?php endfor; ?>
                          </div>
                      <?php endif; ?>
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
    function mostrar_ocultar_contenido(id_elemento){
            if ($("#"+id_elemento).hasClass("d-block")) {
                $("#"+id_elemento).removeClass('d-block').addClass('d-none');
                $("#"+id_elemento+"_icono").removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                $("#"+id_elemento).removeClass('d-none').addClass('d-block');
                $("#"+id_elemento+"_icono").removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }
        }
  </script>
</body>
</html>
