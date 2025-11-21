<?php
    //Validación de permisos del usuario para el módulo
    require_once("../../iniciador_index.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/

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
                <div class="col-lg-11 d-flex flex-column">
                  <div class="row flex-grow">
                    <div class="col-12 col-lg-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
                          <div class="row justify-content-center">
                              <div class="col-md-11 pt-2">
                                  <div class="row justify-content-center">
                                      <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                                      <div class="col-md-12 pt-0 px-0 text-center">
                                          <img src="<?php echo IMAGES; ?>logo-cliente.png" class="img-fluid">
                                      </div>
                                      <div class="col-md-12 py-4 text-center fw-bold">
                                          Bienvenido al menú de gestión de citas. Aquí podrás gestionar o solicitar tu cita para el proceso de inscripciones del programa Familias en Acción. Por favor seleccione una de las opciones:
                                      </div>
                                      <div class="col-md-3 mb-1">
                                          <div class="card text-center">
                                            <div class="card-header">
                                              Agenda tu cita para inscripción de Familias
                                            </div>
                                            <div class="card-body">
                                              <p class="card-text">Puedes programar una cita en uno de nuestros puntos de inscripción según horarios disponibles</p>
                                              <a href="datospersonales?t=<?php echo base64_encode('agendar'); ?>" class="btn btn-primary">Continuar</a>
                                            </div>
                                          </div>
                                      </div>
                                      <div class="col-md-3 mb-1">
                                          <div class="card text-center">
                                            <div class="card-header">
                                              Consulta tu cita
                                            </div>
                                            <div class="card-body">
                                              <p class="card-text">Puedes consultar información sobre tu cita agendada en cualquier momento.</p>
                                              <a href="consultas?t=<?php echo base64_encode('consultar'); ?>" class="btn btn-primary">Continuar</a>
                                            </div>
                                          </div>
                                      </div>
                                      <div class="col-md-3 mb-1">
                                          <div class="card text-center">
                                            <div class="card-header">
                                              Reagenda tu cita
                                            </div>
                                            <div class="card-body">
                                              <p class="card-text">Puedes reagendar tu cita según nuestros horarios disponibles.</p>
                                              <a href="reagendar?t=<?php echo base64_encode('reagendar'); ?>" class="btn btn-primary">Continuar</a>
                                            </div>
                                          </div>
                                      </div>
                                      <div class="col-md-3 mb-1">
                                          <div class="card text-center">
                                            <div class="card-header">
                                              Cancela tu cita
                                            </div>
                                            <div class="card-body">
                                              <p class="card-text">Puedes cancelar tu cita si ya no requieres ser atendido para que otros usuarios puedan agendar citas.</p>
                                              <a href="cancelar-cita?t=<?php echo base64_encode('cancelar'); ?>" class="btn btn-primary">Continuar</a>
                                            </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
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