<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas-Punto Atención";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas";
  $subtitle = "Puntos de Atención | Eliminar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="punto_atencion?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["eliminar_registro"])){
      if($_SESSION[APP_SESSION.'_registro_eliminado_agendamiento_punto']!=1){
          // Prepara la sentencia
          $sentencia_delete_pa = $enlace_db->prepare("DELETE FROM `gestion_citas_punto_atencion` WHERE `gcpa_id`=?");
          // Agrega variables a sentencia preparada
          $sentencia_delete_pa->bind_param('s', $id_registro);
          
          // Evalua resultado de ejecución sentencia preparada
          if ($sentencia_delete_pa->execute()) {
              $consulta_string_log = "INSERT INTO `administrador_log`(`clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_registro_usuario`) VALUES (?,?,?,?,?)";
              
              $log_modulo=$modulo_plataforma;
              $log_tipo="eliminar";
              $log_accion="Eliminar registro";
              $log_detalle="Punto Atención [".$id_registro."]";
              $log_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
              
              $consulta_registros_log = $enlace_db->prepare($consulta_string_log);
              $consulta_registros_log->bind_param("sssss", $log_modulo, $log_tipo, $log_accion, $log_detalle, $log_usuario);
              $consulta_registros_log->execute();

              $_SESSION[APP_SESSION.'_registro_eliminado_agendamiento_punto']=1;
              $respuesta_accion = "alertButton('success', 'Registro eliminado', 'Registro eliminado exitosamente', '".$url_salir."');";
          } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al eliminar el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  }
    $consulta_string="SELECT `gcpa_id`, `gcpa_regional`, `gcpa_municipio`, `gcpa_punto_atencion`, `gcpa_direccion`, `gcpa_estado`, `gcpa_registro_usuario`, `gcpa_registro_fecha`, TC.`ciu_departamento`, TC.`ciu_municipio`, TU.`usu_nombres_apellidos` FROM `gestion_citas_punto_atencion` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_citas_punto_atencion`.`gcpa_municipio`=TC.`ciu_codigo` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_punto_atencion`.`gcpa_registro_usuario`=TU.`usu_id` WHERE `gcpa_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_ciudad="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_departamento`, `ciu_municipio`";
    $consulta_registros_ciudad = $enlace_db->prepare($consulta_string_ciudad);
    $consulta_registros_ciudad->execute();
    $resultado_registros_ciudad = $consulta_registros_ciudad->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_usuarios="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' ORDER BY `usu_nombres_apellidos` ASC";
    $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
    $consulta_registros_usuarios->execute();
    $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_usuario_punto="SELECT `gcpau_id`, `gcpau_punto_atencion`, `gcpau_usuario`, `gcpau_lunes`, `gcpau_lunes_break_1`, `gcpau_lunes_break_2`, `gcpau_lunes_almuerzo`, `gcpau_martes`, `gcpau_martes_break_1`, `gcpau_martes_break_2`, `gcpau_martes_almuerzo`, `gcpau_miercoles`, `gcpau_miercoles_break_1`, `gcpau_miercoles_break_2`, `gcpau_miercoles_almuerzo`, `gcpau_jueves`, `gcpau_jueves_break_1`, `gcpau_jueves_break_2`, `gcpau_jueves_almuerzo`, `gcpau_viernes`, `gcpau_viernes_break_1`, `gcpau_viernes_break_2`, `gcpau_viernes_almuerzo`, `gcpau_sabado`, `gcpau_sabado_break_1`, `gcpau_sabado_break_2`, `gcpau_sabado_almuerzo`, `gcpau_domingo`, `gcpau_domingo_break_1`, `gcpau_domingo_break_2`, `gcpau_domingo_almuerzo`, TU.`usu_nombres_apellidos` FROM `gestion_citas_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` WHERE `gcpau_punto_atencion`=?";

    $consulta_registros_usuario_punto = $enlace_db->prepare($consulta_string_usuario_punto);
    $consulta_registros_usuario_punto->bind_param("s", $id_registro);
    $consulta_registros_usuario_punto->execute();
    $resultado_registros_usuario_punto = $consulta_registros_usuario_punto->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_agenda="SELECT DISTINCT `gca_semana` FROM `gestion_citas_agenda` WHERE `gca_punto`=? ORDER BY `gca_semana` DESC";
    $consulta_registros_agenda = $enlace_db->prepare($consulta_string_agenda);
    $consulta_registros_agenda->bind_param("s", $id_registro);
    $consulta_registros_agenda->execute();
    $resultado_registros_agenda = $consulta_registros_agenda->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <?php if($_SESSION[APP_SESSION.'_registro_eliminado_agendamiento_punto']==1): ?>
                            <p class="alert alert-danger p-1">¡Registro eliminado exitosamente, haga clic en <b>Finalizar</b> para salir!</p>
                        <?php else: ?>
                          <div class="col-md-6">
                              <div class="form-group my-1">
                                  <label for="estado" class="my-0">Estado</label>
                                  <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado"  required disabled>
                                    <option value="">Seleccione</option>
                                    <option value="Activo" <?php if($resultado_registros[0][5]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                    <option value="Inactivo" <?php if($resultado_registros[0][5]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group my-1">
                                  <label for="regional" class="my-0">Regional</label>
                                  <select class="form-control form-control-sm form-select font-size-11" name="regional" id="regional"  required disabled>
                                    <option value="">Seleccione</option>
                                    <option value="REGIONAL AMAZONAS" <?php if($resultado_registros[0][1]=="REGIONAL AMAZONAS"){ echo "selected"; } ?>>REGIONAL AMAZONAS</option>
                                    <option value="REGIONAL ANTIOQUIA" <?php if($resultado_registros[0][1]=="REGIONAL ANTIOQUIA"){ echo "selected"; } ?>>REGIONAL ANTIOQUIA</option>
                                    <option value="REGIONAL ARAUCA" <?php if($resultado_registros[0][1]=="REGIONAL ARAUCA"){ echo "selected"; } ?>>REGIONAL ARAUCA</option>
                                    <option value="REGIONAL ATLÁNTICO" <?php if($resultado_registros[0][1]=="REGIONAL ATLÁNTICO"){ echo "selected"; } ?>>REGIONAL ATLÁNTICO</option>
                                    <option value="REGIONAL BOGOTÁ" <?php if($resultado_registros[0][1]=="REGIONAL BOGOTÁ"){ echo "selected"; } ?>>REGIONAL BOGOTÁ</option>
                                    <option value="REGIONAL BOGOTÁ" <?php if($resultado_registros[0][1]=="REGIONAL BOGOTÁ"){ echo "selected"; } ?>>REGIONAL BOGOTÁ</option>
                                    <option value="REGIONAL BOLÍVAR" <?php if($resultado_registros[0][1]=="REGIONAL BOLÍVAR"){ echo "selected"; } ?>>REGIONAL BOLÍVAR</option>
                                    <option value="REGIONAL BOYACÁ" <?php if($resultado_registros[0][1]=="REGIONAL BOYACÁ"){ echo "selected"; } ?>>REGIONAL BOYACÁ</option>
                                    <option value="REGIONAL CALDAS" <?php if($resultado_registros[0][1]=="REGIONAL CALDAS"){ echo "selected"; } ?>>REGIONAL CALDAS</option>
                                    <option value="REGIONAL CAQUETÁ" <?php if($resultado_registros[0][1]=="REGIONAL CAQUETÁ"){ echo "selected"; } ?>>REGIONAL CAQUETÁ</option>
                                    <option value="REGIONAL CASANARE" <?php if($resultado_registros[0][1]=="REGIONAL CASANARE"){ echo "selected"; } ?>>REGIONAL CASANARE</option>
                                    <option value="REGIONAL CAUCA" <?php if($resultado_registros[0][1]=="REGIONAL CAUCA"){ echo "selected"; } ?>>REGIONAL CAUCA</option>
                                    <option value="REGIONAL CESÁR" <?php if($resultado_registros[0][1]=="REGIONAL CESÁR"){ echo "selected"; } ?>>REGIONAL CESÁR</option>
                                    <option value="REGIONAL CHOCÓ" <?php if($resultado_registros[0][1]=="REGIONAL CHOCÓ"){ echo "selected"; } ?>>REGIONAL CHOCÓ</option>
                                    <option value="REGIONAL CÓRDOBA" <?php if($resultado_registros[0][1]=="REGIONAL CÓRDOBA"){ echo "selected"; } ?>>REGIONAL CÓRDOBA</option>
                                    <option value="REGIONAL CUNDINAMARCA" <?php if($resultado_registros[0][1]=="REGIONAL CUNDINAMARCA"){ echo "selected"; } ?>>REGIONAL CUNDINAMARCA</option>
                                    <option value="REGIONAL GUAINÍA" <?php if($resultado_registros[0][1]=="REGIONAL GUAINÍA"){ echo "selected"; } ?>>REGIONAL GUAINÍA</option>
                                    <option value="REGIONAL GUAVIARE" <?php if($resultado_registros[0][1]=="REGIONAL GUAVIARE"){ echo "selected"; } ?>>REGIONAL GUAVIARE</option>
                                    <option value="REGIONAL HUILA" <?php if($resultado_registros[0][1]=="REGIONAL HUILA"){ echo "selected"; } ?>>REGIONAL HUILA</option>
                                    <option value="REGIONAL LA GUAJIRA" <?php if($resultado_registros[0][1]=="REGIONAL LA GUAJIRA"){ echo "selected"; } ?>>REGIONAL LA GUAJIRA</option>
                                    <option value="REGIONAL MAGDALENA" <?php if($resultado_registros[0][1]=="REGIONAL MAGDALENA"){ echo "selected"; } ?>>REGIONAL MAGDALENA</option>
                                    <option value="REGIONAL MAGDALENA MEDIO" <?php if($resultado_registros[0][1]=="REGIONAL MAGDALENA MEDIO"){ echo "selected"; } ?>>REGIONAL MAGDALENA MEDIO</option>
                                    <option value="REGIONAL META" <?php if($resultado_registros[0][1]=="REGIONAL META"){ echo "selected"; } ?>>REGIONAL META</option>
                                    <option value="REGIONAL NARIÑO" <?php if($resultado_registros[0][1]=="REGIONAL NARIÑO"){ echo "selected"; } ?>>REGIONAL NARIÑO</option>
                                    <option value="REGIONAL NORTE DE SANTANDER" <?php if($resultado_registros[0][1]=="REGIONAL NORTE DE SANTANDER"){ echo "selected"; } ?>>REGIONAL NORTE DE SANTANDER</option>
                                    <option value="REGIONAL PUTUMAYO" <?php if($resultado_registros[0][1]=="REGIONAL PUTUMAYO"){ echo "selected"; } ?>>REGIONAL PUTUMAYO</option>
                                    <option value="REGIONAL QUINDÍO" <?php if($resultado_registros[0][1]=="REGIONAL QUINDÍO"){ echo "selected"; } ?>>REGIONAL QUINDÍO</option>
                                    <option value="REGIONAL RISARALDA" <?php if($resultado_registros[0][1]=="REGIONAL RISARALDA"){ echo "selected"; } ?>>REGIONAL RISARALDA</option>
                                    <option value="REGIONAL SAN ANDRÉS" <?php if($resultado_registros[0][1]=="REGIONAL SAN ANDRÉS"){ echo "selected"; } ?>>REGIONAL SAN ANDRÉS</option>
                                    <option value="REGIONAL SANTANDER" <?php if($resultado_registros[0][1]=="REGIONAL SANTANDER"){ echo "selected"; } ?>>REGIONAL SANTANDER</option>
                                    <option value="REGIONAL SUCRE" <?php if($resultado_registros[0][1]=="REGIONAL SUCRE"){ echo "selected"; } ?>>REGIONAL SUCRE</option>
                                    <option value="REGIONAL TOLIMA" <?php if($resultado_registros[0][1]=="REGIONAL TOLIMA"){ echo "selected"; } ?>>REGIONAL TOLIMA</option>
                                    <option value="REGIONAL URABÁ" <?php if($resultado_registros[0][1]=="REGIONAL URABÁ"){ echo "selected"; } ?>>REGIONAL URABÁ</option>
                                    <option value="REGIONAL VALLE" <?php if($resultado_registros[0][1]=="REGIONAL VALLE"){ echo "selected"; } ?>>REGIONAL VALLE</option>
                                    <option value="REGIONAL VAUPÉS" <?php if($resultado_registros[0][1]=="REGIONAL VAUPÉS"){ echo "selected"; } ?>>REGIONAL VAUPÉS</option>
                                    <option value="REGIONAL VICHADA" <?php if($resultado_registros[0][1]=="REGIONAL VICHADA"){ echo "selected"; } ?>>REGIONAL VICHADA</option>
                                    <option value="RED CADE" <?php if($resultado_registros[0][1]=="RED CADE"){ echo "selected"; } ?>>RED CADE</option>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group my-1">
                                  <label for="ciudad" class="my-0">Municipio</label>
                                  <select class="form-control form-control-sm form-select font-size-11" name="ciudad" id="ciudad"  required disabled>
                                      <option value="">Seleccione</option>
                                      <?php for ($i=0; $i < count($resultado_registros_ciudad); $i++): ?> 
                                          <option value="<?php echo $resultado_registros_ciudad[$i][0]; ?>" <?php if($resultado_registros[0][2]==$resultado_registros_ciudad[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_ciudad[$i][2].", ".$resultado_registros_ciudad[$i][1]; ?></option>
                                      <?php endfor; ?>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group my-1">
                                <label for="punto_atencion" class="my-0">Punto de atención</label>
                                <input type="text" class="form-control form-control-sm font-size-11" name="punto_atencion" id="punto_atencion" maxlength="100" value="<?php echo $resultado_registros[0][3]; ?>" required disabled>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group my-1">
                                <label for="direccion" class="my-0">Dirección</label>
                                <input type="text" class="form-control form-control-sm font-size-11" name="direccion" id="direccion" maxlength="100" value="<?php echo $resultado_registros[0][4]; ?>" required disabled>
                              </div>
                          </div>
                          <div class="col-md-12">
                            <?php if(count($resultado_registros_usuario_punto)>0): ?>
                              <p class="alert alert-warning p-1 font-size-11">¡No es posible eliminar el punto de atención debido a que tiene usuarios asociados!</p>
                            <?php endif; ?>
                            <?php if(count($resultado_registros_agenda)>0): ?>
                              <p class="alert alert-warning p-1 font-size-11">¡No es posible eliminar el punto de atención debido a que tiene agendas asociados!</p>
                            <?php endif; ?>
                            <p class="alert alert-danger p-1">¡El registro será eliminado de forma permanente y no se podrá recuperar, por favor valide antes de continuar!</p>
                          </div>
                          <?php endif; ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_eliminado_agendamiento_punto']==1): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php else: ?>
                                    <?php if(count($resultado_registros_usuario_punto)==0 AND count($resultado_registros_agenda)==0): ?>
                                      <button class="btn btn-warning float-end ms-1" type="submit" name="eliminar_registro" id="eliminar_registro_btn">Si, eliminar</button>
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
      var campos_max = 10;

      var x = 0;
      $('#add_field_usuario').click (function(e) {
          e.preventDefault();
          if (x < campos_max) {
              $('#opciones_respuestas_usuario').append('<div class="row lista_usuario px-4 col-md-12">\
                  <div class="col-md-12">\
                      <div class="form-group my-1">\
                          <label for="usuarios" class="my-0">Usuario</label>\
                          <select class="form-control form-control-sm form-select font-size-11" name="usuarios[]" id="usuarios_'+x+'" required onchange="validar_captacion_pediatria('+x+');">\
                              <option class="font-size-11" value="">Seleccione</option>\
                              <?php for ($i=0; $i < count($resultado_registros_usuarios); $i++): ?>
                                  <option value="<?php echo $resultado_registros_usuarios[$i][0]; ?>"><?php echo $resultado_registros_usuarios[$i][1]; ?></option>\
                              <?php endfor; ?>
                          </select>\
                      </div>\
                  </div>\
                  <div class="col-12 mb-1 ps-3">\
                      <a href="#" class="btn btn-danger font-size-11 p-0" style="display: block; width: 185px;" id="del_field_usuario" title="Quitar usuario"><span class="fas fa-trash-alt"></span> Quitar usuario</a>\
                  </div>\
              </div>');
              x++;
          }
      });

      $('#opciones_respuestas_usuario').on("click","#del_field_usuario",function(e) {
          e.preventDefault();
          $(this).parents('div.lista_usuario').remove();
          x--;
      });
  </script>
</body>
</html>