<?php
    //Validación de permisos del usuario para el módulo
    require_once("../../iniciador_index.php");
    require_once("../../app/functions/validar_festivos.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $tipo=validar_input(base64_decode($_GET['t']));
    $datos_mostrar=unserialize($_GET['d']);
    $datos=serialize($datos_mostrar);
    $datos=urlencode($datos);
    $resultado_registros_punto_atencion=array();
    $resultado_registros_municipio=array();

    if(isset($_POST["buscar_punto"])){
        $departamento_filtro=validar_input($_POST['departamento_filtro']);
        $municipio_filtro=validar_input($_POST['municipio_filtro']);
        
        $consulta_string_municipio="SELECT DISTINCT `gcpa_municipio`, TC.`ciu_municipio` FROM `gestion_citas_punto_atencion` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_citas_punto_atencion`.`gcpa_municipio`=TC.`ciu_codigo` WHERE `gcpa_estado`='Activo' AND TC.`ciu_departamento`=? ORDER BY TC.`ciu_municipio`";
        $consulta_registros_municipio = $enlace_db->prepare($consulta_string_municipio);
        $consulta_registros_municipio->bind_param("s", $departamento_filtro);
        $consulta_registros_municipio->execute();
        $resultado_registros_municipio = $consulta_registros_municipio->get_result()->fetch_all(MYSQLI_NUM);

        $consulta_string_punto_atencion="SELECT `gcpa_id`, `gcpa_regional`, `gcpa_municipio`, `gcpa_punto_atencion`, `gcpa_direccion` FROM `gestion_citas_punto_atencion` WHERE `gcpa_municipio`=? AND `gcpa_estado`='Activo' ORDER BY `gcpa_punto_atencion`";
        $consulta_registros_punto_atencion = $enlace_db->prepare($consulta_string_punto_atencion);
        $consulta_registros_punto_atencion->bind_param("s", $municipio_filtro);
        $consulta_registros_punto_atencion->execute();
        $resultado_registros_punto_atencion = $consulta_registros_punto_atencion->get_result()->fetch_all(MYSQLI_NUM);
    }

    $consulta_string_reserva="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_datos_numero_identificacion`=? AND TC.`gca_estado_agenda`='Reservada' AND TC.`gca_fecha`>=?";

    $consulta_registros_reserva = $enlace_db->prepare($consulta_string_reserva);
    $consulta_registros_reserva->bind_param("ss", $datos_mostrar['numero_identificacion'], date('Y-m-d'));
    $consulta_registros_reserva->execute();
    $resultado_registros_reserva = $consulta_registros_reserva->get_result()->fetch_all(MYSQLI_NUM);

    if (count($resultado_registros_reserva)>0) {
        $fecha_hora_inicio=date('Y-m-d H:i');

        $citas_agendadas=0;
        for ($i=0; $i < count($resultado_registros_reserva); $i++) { 
            $fecha_cita=$resultado_registros_reserva[$i][15].' '.$resultado_registros_reserva[$i][16];
            if($fecha_cita>$fecha_hora_inicio) {
                $citas_agendadas++;
            }
        }
    }

    if ($citas_agendadas==0) {
        $consulta_string_departamento="SELECT DISTINCT TC.`ciu_departamento` FROM `gestion_citas_punto_atencion` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_citas_punto_atencion`.`gcpa_municipio`=TC.`ciu_codigo` WHERE `gcpa_estado`='Activo' ORDER BY TC.`ciu_departamento`";
        $consulta_registros_departamento = $enlace_db->prepare($consulta_string_departamento);
        $consulta_registros_departamento->execute();
        $resultado_registros_departamento = $consulta_registros_departamento->get_result()->fetch_all(MYSQLI_NUM);
    }

    $array_tipo_documento['CC']='Cédula de Ciudadanía';
    $array_tipo_documento['CE']='Cédula de Extranjería';
    $array_tipo_documento['NUIP']='NUIP - Número Único de Identificación Personal';
    $array_tipo_documento['TI']='Tarjeta de Identidad';
    $array_tipo_documento['PEP']='Permiso Especial de Permanencia';

    /*Enlace para botón finalizar y cancelar*/
    $ruta_cancelar_finalizar="inicio";
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
    <title>Prosperidad Social - Gobierno de Colombia</title>
    <?php require_once(ROOT.'includes/_head.php'); ?>
    <style type="text/css">
        .card-header {
          background-color: #005AC6;
          color: #FFF;
        }

        .menu-punto {
            /*border: solid 1px #8C8C8C;*/
            color: #1F1F1F;
        }

        .menu-punto:hover {
            background-color: #005AC6;
            color: #FFF !important;
        }

        /*.icono {
          color: #005AC6;
        }*/

        a {
            text-decoration: none !important;
        }

        .btn-primary {
          background-color: #F42F63;
          border-color: #F42F63;
        }

        .btn-primary:hover {
          background-color: #F42F63;
          border-color: #F42F63;
        }
    </style>
    <link rel="shortcut icon" href="favicon-PROSPERIDADSOCIAL-min-32x32.png" />
</head>
<body class="sidebar-dark sidebar-icon-only" style="background-color: #F4F5F7 !important;">
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper pt-0">
      <!-- main-panel -->
      <div class="">
        <div class="content-wrapper pt-2">
          <div class="row">
            <div class="col-sm-12">
              <div class="row justify-content-center">
                <div class="col-lg-4 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                            <div class="col-md-12 pt-0 px-0 text-center mb-2">
                                <img src="<?php echo IMAGES; ?>logo-cliente.png" class="img-fluid">
                            </div>
                            <div class="card">
                              <div class="card-header font-size-12">
                                Datos del usuario
                              </div>
                              <div class="card-body py-2">
                                <p class="card-text my-0"><b>Tipo de documento:</b> <?php echo $array_tipo_documento[$datos_mostrar['tipo_documento']]; ?></p>
                                <p class="card-text my-0"><b>Número de identificación:</b> <?php echo $datos_mostrar['numero_identificacion']; ?></p>
                                <p class="card-text my-0"><b>Nombres y apellidos:</b> <?php echo $datos_mostrar['nombres']; ?></p>
                                <p class="card-text my-0"><b>Correo electrónico:</b> <?php echo $datos_mostrar['correo']; ?></p>
                                <p class="card-text my-0"><b>Número celular:</b> <?php echo $datos_mostrar['celular']; ?></p>
                                <p class="card-text my-0"><b>Número fijo:</b> <?php echo $datos_mostrar['fijo']; ?></p>
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-8 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                            <div class="col-md-12 pb-4 fw-bold">Proceso de Agendamiento</div>
                            <form name="buscar_punto" action="" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <?php if ($citas_agendadas==0): ?>
                                        <div class="col-md-12 py-2 fw-bold">
                                            Seleccione un departamento y municipio para ver los puntos de atención disponibles
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group my-1">
                                                <label for="departamento_filtro">Departamento *</label>
                                                <select class="form-control form-control-sm form-select" name="departamento_filtro" id="departamento_filtro" required onchange="validar_municipio();">
                                                  <option value="">Seleccione</option>
                                                  <?php for ($i=0; $i < count($resultado_registros_departamento); $i++): ?>
                                                    <option value="<?php echo $resultado_registros_departamento[$i][0]; ?>" <?php if(isset($_POST["buscar_punto"]) AND $departamento_filtro==$resultado_registros_departamento[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_departamento[$i][0]; ?></option>
                                                  <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group my-1">
                                                <label for="municipio_filtro">Municipio *</label>
                                                <select class="form-control form-control-sm form-select" name="municipio_filtro" id="municipio_filtro" required>
                                                  <option value="">Seleccione</option>
                                                  <?php for ($i=0; $i < count($resultado_registros_municipio); $i++): ?>
                                                    <option value="<?php echo $resultado_registros_municipio[$i][0]; ?>" <?php if(isset($_POST["buscar_punto"]) AND $municipio_filtro==$resultado_registros_municipio[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_municipio[$i][1]; ?></option>
                                                  <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 my-2">
                                            <div class="form-group">
                                                <button class="btn btn-success float-end ms-1" type="submit" name="buscar_punto">Consultar Puntos de Atención</button>
                                                <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $ruta_cancelar_finalizar; ?>');">Cancelar</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                        <?php if(isset($_POST["buscar_punto"]) AND count($resultado_registros_punto_atencion)>0): ?>
                                            <div class="col-md-12 py-2 fw-bold">
                                                Seleccione un punto de atención para ver las citas disponibles
                                            </div>
                                            <?php for ($i=0; $i < count($resultado_registros_punto_atencion); $i++): ?>
                                                <div class="col-md-6 enlace-punto">
                                                    <a href="agendamiento-citas?t=<?php echo base64_encode($tipo); ?>&d=<?php echo $datos; ?>&idp=<?php echo base64_encode($resultado_registros_punto_atencion[$i][0]); ?>&confim=<?php echo base64_encode('token-citas'); ?>" class="">
                                                        <div class="card mb-1 menu-punto">
                                                          <div class="card-body">
                                                            <h5 class="fw-bold mb-1"><span class="fas fa-location-dot icono"></span> <?php echo $resultado_registros_punto_atencion[$i][3]; ?></h5>
                                                            <h6 class="mb-0 ps-3"><?php echo $resultado_registros_punto_atencion[$i][4]; ?></h6>
                                                          </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php endfor; ?>
                                        <?php else: ?>
                                            <?php if(isset($_POST["buscar_punto"])):?>
                                                <div class="col-md-12">
                                                    <p class="alert alert-warning p-1">¡No se encontraron puntos de atención disponibles!</p>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-md-12">
                                            <div class="card text-center">
                                              <div class="card-header">
                                                Usuario con cita activa
                                              </div>
                                              <div class="card-body">
                                                <p class="card-text">Estimado usuario, recuerde que solo puede tener una cita agendada, en caso de querer cambiar y/o cancelar una cita, ingrese a la opción <b>Reagendar Cita</b> o <b>Cancelar Cita</b>.</p>
                                                <div class="form-group">
                                                    <a href="inicio" class="btn btn-primary">Aceptar</a>
                                                </div>
                                              </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </form>
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
      function validar_municipio(){
          var departamento = document.getElementById("departamento_filtro").value;

          if(departamento!="") {
              var municipio_filtro = document.getElementById('municipio_filtro').disabled=false;
              $("#municipio_filtro").html("");
              // $('#municipio_filtro').selectpicker('destroy');
              // $('#municipio_filtro').selectpicker('refresh');
              
              $.post("agendamiento_procesar.php?validacion=municipio&filtro="+departamento, { }, function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      $("#municipio_filtro").html(resp.resultado);
                      // $('#municipio_filtro').selectpicker('refresh');
                  } else {
                      var municipio_filtro = document.getElementById('municipio_filtro').disabled=true;
                      // $('#municipio_filtro').selectpicker('refresh');
                  }
              });
          }
      }
  </script>
</body>
</html>