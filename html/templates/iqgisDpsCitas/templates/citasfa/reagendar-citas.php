<?php
    //Validación de permisos del usuario para el módulo
    require_once("../../iniciador_index.php");
    require_once("../../app/functions/validar_festivos.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $tipo=validar_input(base64_decode($_GET['t']));
    $id_punto=validar_input(base64_decode($_GET['idp']));
    $id_consecutivo=validar_input(base64_decode($_GET['con']));
    $datos_mostrar=unserialize($_GET['d']);
    $datos=serialize($datos_mostrar);
    $datos=urlencode($datos);
    $resultado_registros_agenda=array();
    
    $consulta_string_reserva="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora` FROM `gestion_citasfa_agenda_reservas` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citasfa_agenda` AS TC ON `gestion_citasfa_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_consecutivo`=? AND TC.`gca_estado_agenda`='Reservada' AND TC.`gca_fecha`>=?";

    $consulta_registros_reserva = $enlace_db->prepare($consulta_string_reserva);
    $consulta_registros_reserva->bind_param("ss", $id_consecutivo, date('Y-m-d'));
    $consulta_registros_reserva->execute();
    $resultado_registros_reserva = $consulta_registros_reserva->get_result()->fetch_all(MYSQLI_NUM);

    if($id_punto!=""){
        $dia_control=$resultado_registros_reserva[0][15];
        $fecha_inicio=$resultado_registros_reserva[0][15];
        $dias_habiles=0;
        while ($dias_habiles<=2) {
            $numero_dia=date("N", strtotime($dia_control));
            $festivo=validarFestivo($dia_control);
            if ($numero_dia>=1 AND $numero_dia<6 AND $festivo=='') {
                $dias_habiles++;
            }
            $dia_control = date("Y-m-d", strtotime("+ 1 day", strtotime($dia_control)));
        }

        $fecha_fin=$dia_control;

        $consulta_string_pa="SELECT `gcpa_id`, `gcpa_regional`, `gcpa_municipio`, `gcpa_punto_atencion`, `gcpa_direccion`, `gcpa_estado`, TC.`ciu_departamento`, TC.`ciu_municipio` FROM `gestion_citasfa_punto_atencion` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_citasfa_punto_atencion`.`gcpa_municipio`=TC.`ciu_codigo` WHERE `gcpa_id`=?";
        $consulta_registros_pa = $enlace_db->prepare($consulta_string_pa);
        $consulta_registros_pa->bind_param("s", $id_punto);
        $consulta_registros_pa->execute();
        $resultado_registros_pa = $consulta_registros_pa->get_result()->fetch_all(MYSQLI_NUM);

        $consulta_string_agenda="SELECT `gca_id`, `gca_punto`, `gca_usuario`, `gca_semana`, `gca_fecha`, `gca_hora`, `gca_estado`, `gca_estado_agenda`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos` FROM `gestion_citasfa_agenda` LEFT JOIN `gestion_citasfa_punto_atencion` AS TP ON `gestion_citasfa_agenda`.`gca_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_agenda`.`gca_usuario`=TU.`usu_id` WHERE `gca_estado`='Disponible' AND `gca_estado_agenda`='Disponible' AND `gca_punto`=? AND `gca_fecha`>? AND `gca_fecha`<=? ORDER BY `gca_fecha` ASC, `gca_hora` ASC";

        $consulta_registros_agenda = $enlace_db->prepare($consulta_string_agenda);
        $consulta_registros_agenda->bind_param("sss", $id_punto, $fecha_inicio, $fecha_fin);
        $consulta_registros_agenda->execute();
        $resultado_registros_agenda = $consulta_registros_agenda->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_agenda); $i++) { 
            $array_horario[$resultado_registros_agenda[$i][4]][$resultado_registros_agenda[$i][5]]+=1;
            $array_fechas[]=$resultado_registros_agenda[$i][4];
            $array_fechas_hora[$resultado_registros_agenda[$i][4]][]=$resultado_registros_agenda[$i][5];
        }

        $array_fechas=array_values(array_unique($array_fechas));

        for ($i=0; $i < count($array_fechas); $i++) { 
            $array_fechas_hora[$array_fechas[$i]]=array_values(array_unique($array_fechas_hora[$array_fechas[$i]]));
        }
    }


    if (count($resultado_registros_reserva)>0) {
        $fecha_hora_inicio=date('Y-m-d H:i');
        $hora_inicio=date("Y-m-d H:i", strtotime("+ 30 minute", strtotime($fecha_hora_inicio)));

        $citas_disponibles=0;
        for ($i=0; $i < count($resultado_registros_agenda); $i++) { 
            $fecha_cita=$resultado_registros_agenda[$i][4].' '.$resultado_registros_agenda[$i][5];
            if($fecha_cita>=$hora_inicio) {
                $citas_disponibles++;
            }
        }
    }

    $array_tipo_documento['CC']='Cédula de Ciudadanía';
    $array_tipo_documento['CE']='Cédula de Extranjería';
    $array_tipo_documento['NUIP']='NUIP - Número Único de Identificación Personal';
    $array_tipo_documento['RC']='Registro Civil';
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

        .estado-cita {
            color: #FFF;
            background-color: #2ECC71;
            border-radius: 10px;
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
                            <div class="card mb-2">
                              <div class="card-header font-size-12">
                                Datos del usuario
                              </div>
                              <div class="card-body py-2">
                                <p class="card-text my-0"><b>Tipo de documento:</b> <?php echo $array_tipo_documento[$resultado_registros_reserva[0][4]]; ?></p>
                                <p class="card-text my-0"><b>Número de identificación:</b> <?php echo $resultado_registros_reserva[0][5]; ?></p>
                                <p class="card-text my-0"><b>Nombres y apellidos:</b> <?php echo $resultado_registros_reserva[0][6]; ?></p>
                                <p class="card-text my-0"><b>Correo electrónico:</b> <?php echo $resultado_registros_reserva[0][7]; ?></p>
                                <p class="card-text my-0"><b>Número celular:</b> <?php echo $resultado_registros_reserva[0][8]; ?></p>
                                <p class="card-text my-0"><b>Número fijo:</b> <?php echo $resultado_registros_reserva[0][9]; ?></p>
                              </div>
                            </div>
                            <div class="card mb-2">
                              <div class="card-header font-size-12">
                                Datos cita actual
                              </div>
                              <div class="card-body py-2">
                                <p class="card-text my-0"><span class="fas fa-calendar-alt"></span> <?php echo date('d-m-Y', strtotime($resultado_registros_reserva[0][15])); ?> <span class="fas fa-clock"></span> <?php echo date('h:i A', strtotime($resultado_registros_reserva[0][16])); ?> </p>
                                <p class="card-text fw-bold my-0"><b><span class="fas fa-location-dot"></span></b> <?php echo $resultado_registros_reserva[0][12]; ?></p>
                                <p class="card-text ps-3 my-0"><?php echo $resultado_registros_reserva[0][13]; ?></p>
                              </div>
                            </div>
                            <div class="card">
                              <div class="card-header font-size-12">
                                Punto de atención nueva cita
                              </div>
                              <div class="card-body py-2">
                                <p class="card-text fw-bold my-0"><b><span class="fas fa-location-dot"></span></b> <?php echo $resultado_registros_pa[0][3]; ?></p>
                                <p class="card-text ps-3 my-0"><?php echo $resultado_registros_pa[0][4]; ?></p>
                                <p class="card-text ps-3 my-0"><?php echo $resultado_registros_pa[0][7].', '.$resultado_registros_pa[0][6]; ?></p>
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
                            <form name="buscar_agenda" action="" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <?php if (count($resultado_registros_reserva)>0): ?>
                                        <div class="col-md-12 py-2 fw-bold">
                                            <a href="reagendar-agendamiento?t=<?php echo base64_encode($tipo); ?>&con=<?php echo base64_encode($id_consecutivo); ?>" class="btn btn-primary"><span class="fas fa-arrow-left"></span> Regresar</a>
                                            Seleccione una cita de acuerdo a su preferencia
                                        </div>
                                        <div class="row">
                                        <?php if($citas_disponibles>0): ?>
                                            <div class="accordion accordion-flush" id="accordionFlushAgenda">
                                                <?php for ($i=0; $i < count($array_fechas); $i++): ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="flush-heading<?php echo $i; ?>">
                                                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?php echo $i; ?>" aria-expanded="false" aria-controls="flush-collapse<?php echo $i; ?>">
                                                        <span class="fas fa-calendar-alt me-1"></span> <?php echo $array_dias_nombre[date('N', strtotime($array_fechas[$i]))].' '.date('d', strtotime($array_fechas[$i])).' de '.$array_meses[intval(date('m', strtotime($array_fechas[$i])))].' de '.date('Y', strtotime($array_fechas[$i])); ?>
                                                      </button>
                                                    </h2>
                                                    <div id="flush-collapse<?php echo $i; ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?php echo $i; ?>" data-bs-parent="#accordionFlushAgenda">
                                                      <div class="accordion-body row">
                                                            <?php for ($j=0; $j < count($array_fechas_hora[$array_fechas[$i]]); $j++): ?>
                                                                <div class="col-md-6 enlace-punto">
                                                                    <a href="reagendar-confirmacion?t=<?php echo base64_encode($tipo); ?>&con=<?php echo base64_encode($id_consecutivo); ?>&id=<?php echo base64_encode($array_fechas[$i]); ?>&id2=<?php echo base64_encode($array_fechas_hora[$array_fechas[$i]][$j]); ?>&id3=<?php echo base64_encode($id_punto); ?>&confim=<?php echo base64_encode('token'); ?>" class="">
                                                                        <div class="card mb-1 menu-punto">
                                                                          <div class="card-body">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="float-end estado-cita font-size-11 py-1 px-2"><span class="fas fa-check-circle"></span> <?php echo $array_horario[$array_fechas[$i]][$array_fechas_hora[$array_fechas[$i]][$j]]; ?> Disponible(s)</div>
                                                                                    <p class="my-0 fw-bold"><span class="fas fa-clock"></span> <?php echo date('h:i A', strtotime($array_fechas_hora[$array_fechas[$i]][$j])); ?></p>
                                                                                    <p class=" my-0"><span class="fas fa-calendar-alt"></span> <?php echo $array_dias_nombre[date('N', strtotime($array_fechas[$i]))].' '.date('d', strtotime($array_fechas[$i])).' de '.$array_meses[intval(date('m', strtotime($array_fechas[$i])))].' de '.date('Y', strtotime($array_fechas[$i])); ?></p>
                                                                                </div>
                                                                            </div>
                                                                          </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            <?php endfor; ?>
                                                      </div>
                                                    </div>
                                                </div>
                                                <?php endfor; ?>  
                                            </div>
                                        <?php else: ?>
                                            <div class="col-md-12">
                                                <p class="alert alert-dark p-1">¡No se encontraron citas disponibles!</p>
                                            </div>
                                        <?php endif; ?>
                                        </div>
                                        <div class="col-md-12 py-2 fw-bold">
                                            <a href="reagendar-agendamiento?t=<?php echo base64_encode($tipo); ?>&con=<?php echo base64_encode($id_consecutivo); ?>" class="btn btn-primary"><span class="fas fa-arrow-left"></span> Regresar</a>
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
</body>
</html>