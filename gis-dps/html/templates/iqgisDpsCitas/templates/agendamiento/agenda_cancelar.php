<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas-Punto Atención";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas";
  $subtitle = "Agenda | Cancelar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $url_salir="agenda?pagina=".$pagina."&id=".$filtro_permanente;
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  if(isset($_POST["buscar_agenda"])){
      $estado=validar_input($_POST['estado']);
      $punto_atencion=validar_input($_POST['punto_atencion']);
      $fecha=validar_input($_POST['fecha']);
      $usuario=validar_input($_POST['usuario']);

      $data_consulta=array();
      //Agregar pagina a array data_consulta
      array_push($data_consulta, $punto_atencion);
      array_push($data_consulta, $fecha);

      $filtro_usuario='';
      if ($usuario!='Todos') {
        $filtro_usuario=' AND `gca_usuario`=?';
        array_push($data_consulta, $usuario);
      }

      $consulta_string_agenda="SELECT `gca_id`, `gca_punto`, `gca_usuario`, `gca_semana`, `gca_fecha`, `gca_hora`, `gca_estado`, `gca_estado_agenda`, TP.`gcpa_regional`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`ciu_departamento`, TC.`ciu_municipio` FROM `gestion_citas_agenda` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda`.`gca_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda`.`gca_usuario`=TU.`usu_id` LEFT JOIN `administrador_ciudades` AS TC ON TP.`gcpa_municipio`=TC.`ciu_codigo` WHERE `gca_estado_agenda`<>'Cancelada' AND `gca_punto`=? AND `gca_fecha`=? ".$filtro_usuario." ORDER BY `gca_fecha`, `gca_hora`";
      $consulta_registros_agenda = $enlace_db->prepare($consulta_string_agenda);
      if (count($data_consulta)>0) {
          $consulta_registros_agenda->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
      }
      $consulta_registros_agenda->execute();
      $resultado_registros_agenda = $consulta_registros_agenda->get_result()->fetch_all(MYSQLI_NUM);
  }

  if(isset($_POST["guardar_registro"])){
      $estado=validar_input($_POST['estado']);
      $punto_atencion=validar_input($_POST['punto_atencion']);
      $fecha=validar_input($_POST['fecha']);
      $usuario=validar_input($_POST['usuario']);
      $agenda_turno=$_POST['agenda_turno'];
      $gcar_cancela_motivo=validar_input($_POST['gcar_cancela_motivo']);

      if (!isset($agenda_turno[0])) {
        $agenda_turno=array();
      }

      // $data_consulta=array();
      // //Agregar pagina a array data_consulta
      // array_push($data_consulta, $punto_atencion);
      // array_push($data_consulta, $fecha);

      // $filtro_usuario='';
      // if ($usuario!='Todos') {
      //   $filtro_usuario=' AND `gcar_usuario`=?';
      //   array_push($data_consulta, $usuario);
      // }

      if($_SESSION[APP_SESSION.'_registro_cancelado_agendamiento']!=1){
          // Prepara la sentencia
          $consulta_actualizar_agenda = $enlace_db->prepare("UPDATE `gestion_citas_agenda` SET `gca_estado`='Cancelada', `gca_estado_agenda`='Cancelada' WHERE `gca_id`=?");

          // Agrega variables a sentencia preparada
          $consulta_actualizar_agenda->bind_param('s', $gca_id);

          // Prepara la sentencia
          $consulta_actualizar_reserva = $enlace_db->prepare("UPDATE `gestion_citas_agenda_reservas` SET `gcar_estado`='Cancelada', `gcar_cancela_fecha`=?, `gcar_cancela_motivo`=? WHERE `gcar_cita`=?");

          // Agrega variables a sentencia preparada
          $consulta_actualizar_reserva->bind_param('sss', $gcar_cancela_fecha, $gcar_cancela_motivo, $gcar_cita);
          
          $control_cancela=0;
          $control_notificacion=0;
          for ($i=0; $i < count($agenda_turno); $i++) { 
            $gca_id=$agenda_turno[$i];
            $gcar_cancela_fecha=date('Y-m-d H:i:s');
            $gcar_cita=$agenda_turno[$i];
            
            $consulta_string="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_observaciones`, `gcar_atencion_usuario`, `gcar_atencion_fecha`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora`, TC.`gca_estado`, TC.`gca_estado_agenda`, `gcar_estado` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` WHERE `gcar_cita`=?";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            $consulta_registros->bind_param("s", $gcar_cita);
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);


            // Ejecuta sentencia preparada
            $consulta_actualizar_reserva->execute();

            // Ejecuta sentencia preparada
            $consulta_actualizar_agenda->execute();

            if (comprobarSentencia($enlace_db->info)) {
                $control_cancela++;

                if ($resultado_registros[0][21]=='Reservada') {
                  //PROGRAMACIÓN NOTIFICACIÓN
                    $asunto='Cancelación Cita';
                    $referencia='Datos Cita';
                    $contenido="<p>Estimado, <b>".$resultado_registros[0][6]."</b><br></p>
                        <p>Su cita para atención presencial en <b>Prosperidad Social</b> en el punto ".$resultado_registros[0][15]." en la dirección ".$resultado_registros[0][16]." el ".date('d-m-Y', strtotime($resultado_registros[0][18]))." a las ".date('h:i A', strtotime($resultado_registros[0][19]))." ha sido <b>cancelada</b>. Motivo: ".$gcar_cancela_motivo."</p>
                      <p>Le recordamos que usted puede consultar, reagendar o cancelar su cita, ingresando en la siguiente página web: <a href='https://dps.iq-online.net.co/citas/inicio'>https://dps.iq-online.net.co/citas/inicio</a></p>
                      <p>Respetado destinatario, este correo ha sido generado por un sistema de envío automático; por favor <b>NO</b> responda al mismo ya que no podrá ser gestionado.</p>
                        <p>Lo invitamos a consultar nuestros demás canales de atención ingresando al siguiente enlace: <a href='https://prosperidadsocial.gov.co/atencion-al-ciudadano/servicio-al-ciudadano/'>https://prosperidadsocial.gov.co/atencion-al-ciudadano/servicio-al-ciudadano/</a></p>
                      <p>
                          <br><br>
                          Cordialmente,
                          <br>
                          <br><img src='cid:firma-dps' style='height: 50px;'></img>
                          <br>Grupo de Participación Ciudadana
                      </p>
                      <p><center><b>Todas las personas tienen derecho a presentar peticiones respetuosas ante las autoridades de forma GRATUITA.</b></center></p>
                        <p><center><b>No recurra a intermediarios. No pague por sus derechos. DENUNCIE.</b></center></p>
                        <p style='font-size: 10px;'>Este mensaje y sus archivos adjuntos van dirigidos exclusivamente a su destinatario, pudiendo contener información confidencial. No está permitida su reproducción o distribución sin la autorización expresa de Prosperidad Social. Si usted no es el destinatario final por favor elimínelo e infórmenos por esta vía, en cumplimiento de la Ley Estatutaria 1581 de 2012 de Protección de datos personales y el Decreto Reglamentario 1377 del 27 de junio de 2013 y demás normas concordantes. Para conocer más sobre nuestra Política de tratamiento de datos personales, lo invitamos a ingresar al siguiente link: <a href='https://prosperidadsocial.gov.co/politica-de-proteccion-de-datos-personales/'>https://prosperidadsocial.gov.co/politica-de-proteccion-de-datos-personales/</a></p>";
                    $nc_address=$resultado_registros[0][7].";";
                    $nc_cc="";
                    $modulo_plataforma='Gestión Agenda';
                    $estado_notificacion=notificacion_agendamiento($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
                    if ($estado_notificacion) {
                      $control_notificacion++;
                    } else {

                    }

                    $nsms_identificador=$resultado_registros[0][0];
                    $contenido_sms="Estimado (a) ".$resultado_registros[0][6].", su cita en el punto ".$resultado_registros[0][15]." - ".$resultado_registros[0][16]." el ".$array_dias_nombre[date('N', strtotime($resultado_registros[0][18]))].' '.date('d', strtotime($resultado_registros[0][18])).' de '.$array_meses[intval(date('m', strtotime($resultado_registros[0][18])))].' de '.date('Y', strtotime($resultado_registros[0][18])).", a las ".date('h:i A', strtotime($resultado_registros[0][19]))." ha sido cancelada. Motivo: ".$gcar_cancela_motivo.".";
                    $nsms_url='';
                    $nsms_destino=$resultado_registros[0][8];
                    $estado_notificacion_sms=notificacion_agendamiento_sms($enlace_db, $nsms_identificador, $nsms_destino, $contenido_sms, $nsms_url);
                    if ($estado_notificacion_sms) {
                        $estado_sms=1;
                    }
                } else {
                  $control_notificacion++;
                }
            }
          }

          if ($control_cancela==count($agenda_turno) AND $control_notificacion==count($agenda_turno)) {
            $respuesta_accion = "alertButton('success', 'Agenda cancelada', 'Agenda cancelada exitosamente', '".$url_salir."');";
            $_SESSION[APP_SESSION.'_registro_cancelado_agendamiento']=1;
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cancelar la agenda');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Agenda cancelada', 'Agenda cancelada exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string_pa="SELECT `gcpa_id`, `gcpa_regional`, `gcpa_municipio`, `gcpa_punto_atencion`, `gcpa_direccion`, `gcpa_estado`, `gcpa_registro_usuario`, `gcpa_registro_fecha`, TC.`ciu_departamento`, TC.`ciu_municipio`, TU.`usu_nombres_apellidos` FROM `gestion_citas_punto_atencion` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_citas_punto_atencion`.`gcpa_municipio`=TC.`ciu_codigo` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_punto_atencion`.`gcpa_registro_usuario`=TU.`usu_id` WHERE 1=1 ORDER BY TC.`ciu_departamento`, TC.`ciu_municipio`";
  $consulta_registros_pa = $enlace_db->prepare($consulta_string_pa);
  $consulta_registros_pa->execute();
  $resultado_registros_pa = $consulta_registros_pa->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_usuario="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' ORDER BY `usu_nombres_apellidos` ASC";
  $consulta_registros_usuario = $enlace_db->prepare($consulta_string_usuario);
  $consulta_registros_usuario->execute();
  $resultado_registros_usuario = $consulta_registros_usuario->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-lg-6 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <?php if($_SESSION[APP_SESSION.'_registro_cancelado_agendamiento']==1): ?>
                          <p class="alert alert-danger p-1">¡Agenda cancelada exitosamente, haga clic en <b>Finalizar</b> para salir!</p>
                        <?php else: ?>
                          <div class="col-md-6">
                              <div class="form-group my-1">
                                  <label for="estado" class="my-0">Estado</label>
                                  <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" <?php if($_SESSION[APP_SESSION.'_registro_cancelado_agendamiento']==1) { echo 'disabled'; } ?> required>
                                    <option value="">Seleccione</option>
                                    <option value="Cancelar agenda" <?php if((isset($_POST["buscar_agenda"]) OR isset($_POST["guardar_registro"])) AND $estado=="Cancelar agenda"){ echo "selected"; } ?>>Cancelar agenda</option>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group my-1">
                                <label for="fecha" class="my-0">Fecha</label>
                                <input type="date" class="form-control form-control-sm font-size-11" name="fecha" id="fecha" min="<?php echo date('Y-m-d'); ?>" value="<?php if((isset($_POST["buscar_agenda"]) OR isset($_POST["guardar_registro"]))){ echo $fecha; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1) { echo 'readonly'; } ?> required>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group my-1">
                                  <label for="punto_atencion" class="my-0">Punto de atención</label>
                                  <select class="form-control form-control-sm form-select font-size-11" name="punto_atencion" id="punto_atencion" <?php if($_SESSION[APP_SESSION.'_registro_cancelado_agendamiento']==1) { echo 'disabled'; } ?> required>
                                      <option value="">Seleccione</option>
                                      <?php for ($i=0; $i < count($resultado_registros_pa); $i++): ?> 
                                          <option value="<?php echo $resultado_registros_pa[$i][0]; ?>" <?php if((isset($_POST["buscar_agenda"]) OR isset($_POST["guardar_registro"])) AND $punto_atencion==$resultado_registros_pa[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_pa[$i][3]." [".$resultado_registros_pa[$i][9].", ".$resultado_registros_pa[$i][8]."]"; ?></option>
                                      <?php endfor; ?>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group my-1">
                                  <label for="usuario" class="my-0">Usuario</label>
                                  <select class="form-control form-control-sm form-select font-size-11" name="usuario" id="usuario" <?php if($_SESSION[APP_SESSION.'_registro_cancelado_agendamiento']==1) { echo 'disabled'; } ?> required>
                                      <option value="">Seleccione</option>
                                      <?php for ($i=0; $i < count($resultado_registros_usuario); $i++): ?> 
                                          <option value="<?php echo $resultado_registros_usuario[$i][0]; ?>" <?php if((isset($_POST["buscar_agenda"]) OR isset($_POST["guardar_registro"])) AND $usuario==$resultado_registros_usuario[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_usuario[$i][1]; ?></option>
                                      <?php endfor; ?>
                                  </select>
                              </div>
                          </div>
                          <?php if(isset($_POST["buscar_agenda"])): ?>
                            <div class="col-md-12 mb-3">
                                <div class="row">
                                  <div class="table-responsive table-fixed" id="headerFixTable">
                                    <table class="table table-hover table-bordered table-striped">
                                      <thead>
                                        <tr>
                                          <th class="align-middle p-1" style="min-width: 100px; width: 120px;">
                                              <div class="form-check form-switch my-0">
                                                  <input class="form-check-input mx-0 px-0" type="checkbox" id="agenda_check" onClick="activa_check(this.checked);">
                                                  <label class="form-check-label px-0" for="agenda_check">Agenda disponible</label>
                                              </div>
                                          </th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr>
                                          <td class="p-1 ps-2 font-size-11 align-top">
                                            <?php if(count($resultado_registros_agenda)>0): ?>
                                              <?php for ($j=0; $j < count($resultado_registros_agenda); $j++): ?>
                                                <div class="form-check form-switch my-0">
                                                    <input class="form-check-input mx-0 px-0 agenda_turno" type="checkbox" name="agenda_turno[]" id="agenda_turno_<?php echo $j; ?>" value="<?php echo $resultado_registros_agenda[$j][0]; ?>">
                                                    <label class="form-check-label px-0" for="agenda_turno_<?php echo $j; ?>"><?php echo $resultado_registros_agenda[$j][4]." ".$resultado_registros_agenda[$j][5]." - ".$resultado_registros_agenda[$j][7]; ?></label>
                                                </div>
                                              <?php endfor; ?>
                                            <?php else: ?>
                                              <p class="alert alert-warning p-1 font-size-11">No se encontraron registros</p>
                                            <?php endif; ?>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                            </div>
                            <?php if(count($resultado_registros_agenda)>0): ?>
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label for="gcar_cancela_motivo" class="my-0">Motivo cancelación</label>
                                  <textarea class="form-control form-control-sm font-size-11 height-100" name="gcar_cancela_motivo" id="gcar_cancela_motivo" maxlength="200" <?php if($_SESSION[APP_SESSION.'_registro_cancelado_agendamiento']==1) { echo 'disabled'; } ?> required><?php if((isset($_POST["buscar_agenda"]) OR isset($_POST["guardar_registro"]))){ echo $gcar_cancela_motivo; } ?></textarea>
                                </div>
                              </div>
                              <p class="alert alert-danger p-1">¡Las citas seleccionadas de la agenda serán canceladas, por favor valide antes de continuar!</p>
                            <?php endif; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_cancelado_agendamiento']==1): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php else: ?>
                                    <?php if(isset($_POST["buscar_agenda"])): ?>
                                        <?php if(count($resultado_registros_agenda)>0): ?>
                                          <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Cancelar agenda</button>
                                        <?php endif; ?>
                                        <a href="agenda_cancelar?pagina=1&id=<?php echo $filtro_permanente; ?>" class="btn btn-dark float-end ms-1">Reiniciar</a>
                                    <?php endif; ?>
                                    <?php if(!isset($_POST["buscar_agenda"])): ?>
                                        <button class="btn btn-success float-end ms-1" type="submit" name="buscar_agenda" id="guardar_registro_btn">Buscar agenda</button>
                                    <?php endif; ?>
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
          </div>
          </form>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
    function activa_check(estado) {
      $(".agenda_turno").prop("checked", estado);
    }
  </script>
</body>
</html>