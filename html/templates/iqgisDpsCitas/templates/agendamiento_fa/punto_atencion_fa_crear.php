<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas FA-Punto Atención";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Agendamiento Citas FA";
  $subtitle = "Puntos de Atención | Crear";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $url_salir="punto_atencion_fa?pagina=".$pagina."&id=".$filtro_permanente;
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  if(isset($_POST["guardar_registro"])){
      $estado=validar_input($_POST['estado']);
      $regional=validar_input($_POST['regional']);
      $ciudad=validar_input($_POST['ciudad']);
      $punto_atencion=validar_input($_POST['punto_atencion']);
      $direccion=validar_input($_POST['direccion']);
      $usuarios=$_POST['usuarios'];

      $horario_dias['dias']=$_POST["horario_dia"];
      for ($j=1; $j <= count($array_dias_nombre); $j++) { 
        $horario_dias['horario_atencion'][$j]=$_POST["horario_atencion_".$j];
      }

      if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']!=1){
          if ($horario_dias['horario_atencion'][1][0]!="" AND $horario_dias['horario_atencion'][1][1]!="") {
            $gcpa_lunes=$horario_dias['horario_atencion'][1][0].'-'.$horario_dias['horario_atencion'][1][1];
          } else {
            $gcpa_lunes="";
          }

          if ($horario_dias['horario_atencion'][2][0]!="" AND $horario_dias['horario_atencion'][2][1]!="") {
            $gcpa_martes=$horario_dias['horario_atencion'][2][0].'-'.$horario_dias['horario_atencion'][2][1];
          } else {
            $gcpa_martes="";
          }

          if ($horario_dias['horario_atencion'][3][0]!="" AND $horario_dias['horario_atencion'][3][1]!="") {
            $gcpa_miercoles=$horario_dias['horario_atencion'][3][0].'-'.$horario_dias['horario_atencion'][3][1];
          } else {
            $gcpa_miercoles="";
          }

          if ($horario_dias['horario_atencion'][4][0]!="" AND $horario_dias['horario_atencion'][4][1]!="") {
            $gcpa_jueves=$horario_dias['horario_atencion'][4][0].'-'.$horario_dias['horario_atencion'][4][1];
          } else {
            $gcpa_jueves="";
          }

          if ($horario_dias['horario_atencion'][5][0]!="" AND $horario_dias['horario_atencion'][5][1]!="") {
            $gcpa_viernes=$horario_dias['horario_atencion'][5][0].'-'.$horario_dias['horario_atencion'][5][1];
          } else {
            $gcpa_viernes="";
          }

          if ($horario_dias['horario_atencion'][6][0]!="" AND $horario_dias['horario_atencion'][6][1]!="") {
            $gcpa_sabado=$horario_dias['horario_atencion'][6][0].'-'.$horario_dias['horario_atencion'][6][1];
          } else {
            $gcpa_sabado="";
          }

          if ($horario_dias['horario_atencion'][7][0]!="" AND $horario_dias['horario_atencion'][7][1]!="") {
            $gcpa_domingo=$horario_dias['horario_atencion'][7][0].'-'.$horario_dias['horario_atencion'][7][1];
          } else {
            $gcpa_domingo="";
          }
            
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_citasfa_punto_atencion`(`gcpa_regional`, `gcpa_municipio`, `gcpa_punto_atencion`, `gcpa_direccion`, `gcpa_estado`, `gcpa_lunes`, `gcpa_martes`, `gcpa_miercoles`, `gcpa_jueves`, `gcpa_viernes`, `gcpa_sabado`, `gcpa_domingo`, `gcpa_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssssssssss', $regional, $ciudad, $punto_atencion, $direccion, $estado, $gcpa_lunes, $gcpa_martes, $gcpa_miercoles, $gcpa_jueves, $gcpa_viernes, $gcpa_sabado, $gcpa_domingo, $_SESSION[APP_SESSION.'_session_usu_id']);
          
          if ($sentencia_insert->execute()) {
            // Prepara la sentencia
            $sentencia_insert_usuarios = $enlace_db->prepare("INSERT INTO `gestion_citasfa_punto_atencion_usuario`(`gcpau_id`, `gcpau_punto_atencion`, `gcpau_usuario`, `gcpau_lunes`, `gcpau_lunes_break_1`, `gcpau_lunes_break_2`, `gcpau_lunes_almuerzo`, `gcpau_martes`, `gcpau_martes_break_1`, `gcpau_martes_break_2`, `gcpau_martes_almuerzo`, `gcpau_miercoles`, `gcpau_miercoles_break_1`, `gcpau_miercoles_break_2`, `gcpau_miercoles_almuerzo`, `gcpau_jueves`, `gcpau_jueves_break_1`, `gcpau_jueves_break_2`, `gcpau_jueves_almuerzo`, `gcpau_viernes`, `gcpau_viernes_break_1`, `gcpau_viernes_break_2`, `gcpau_viernes_almuerzo`, `gcpau_sabado`, `gcpau_sabado_break_1`, `gcpau_sabado_break_2`, `gcpau_sabado_almuerzo`, `gcpau_domingo`, `gcpau_domingo_break_1`, `gcpau_domingo_break_2`, `gcpau_domingo_almuerzo`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

            // Agrega variables a sentencia preparada
            $sentencia_insert_usuarios->bind_param('sssssssssssssssssssssssssssssss', $gcpau_id, $gcpau_punto_atencion, $gcpau_usuario, $gcpau_lunes, $gcpau_lunes_break_1, $gcpau_lunes_break_2, $gcpau_lunes_almuerzo, $gcpau_martes, $gcpau_martes_break_1, $gcpau_martes_break_2, $gcpau_martes_almuerzo, $gcpau_miercoles, $gcpau_miercoles_break_1, $gcpau_miercoles_break_2, $gcpau_miercoles_almuerzo, $gcpau_jueves, $gcpau_jueves_break_1, $gcpau_jueves_break_2, $gcpau_jueves_almuerzo, $gcpau_viernes, $gcpau_viernes_break_1, $gcpau_viernes_break_2, $gcpau_viernes_almuerzo, $gcpau_sabado, $gcpau_sabado_break_1, $gcpau_sabado_break_2, $gcpau_sabado_almuerzo, $gcpau_domingo, $gcpau_domingo_break_1, $gcpau_domingo_break_2, $gcpau_domingo_almuerzo);
            
            $control_insert=0;
            $id_registro=$enlace_db->insert_id;
            for ($i=0; $i < count($usuarios); $i++) { 
              $gcpau_id=$usuarios[$i].'-'.$id_registro;
              $gcpau_punto_atencion=$id_registro;
              $gcpau_usuario=$usuarios[$i];
              $gcpau_lunes='';
              $gcpau_lunes_break_1='';
              $gcpau_lunes_break_2='';
              $gcpau_lunes_almuerzo='';
              $gcpau_martes='';
              $gcpau_martes_break_1='';
              $gcpau_martes_break_2='';
              $gcpau_martes_almuerzo='';
              $gcpau_miercoles='';
              $gcpau_miercoles_break_1='';
              $gcpau_miercoles_break_2='';
              $gcpau_miercoles_almuerzo='';
              $gcpau_jueves='';
              $gcpau_jueves_break_1='';
              $gcpau_jueves_break_2='';
              $gcpau_jueves_almuerzo='';
              $gcpau_viernes='';
              $gcpau_viernes_break_1='';
              $gcpau_viernes_break_2='';
              $gcpau_viernes_almuerzo='';
              $gcpau_sabado='';
              $gcpau_sabado_break_1='';
              $gcpau_sabado_break_2='';
              $gcpau_sabado_almuerzo='';
              $gcpau_domingo='';
              $gcpau_domingo_break_1='';
              $gcpau_domingo_break_2='';
              $gcpau_domingo_almuerzo='';
              
              if ($sentencia_insert_usuarios->execute()) {
                  $control_insert++;
              }
            }

            if ($control_insert==count($usuarios)) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']=1;
            } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
            }
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string_ciudad="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_departamento`, `ciu_municipio`";
  $consulta_registros_ciudad = $enlace_db->prepare($consulta_string_ciudad);
  $consulta_registros_ciudad->execute();
  $resultado_registros_ciudad = $consulta_registros_ciudad->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_usuarios="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' ORDER BY `usu_nombres_apellidos` ASC";
  $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
  $consulta_registros_usuarios->execute();
  $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);
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
            <div class="col-lg-8 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                                <div class="form-group my-1">
                                    <label for="estado" class="my-0">Estado</label>
                                    <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1) { echo 'disabled'; } ?> required>
                                      <option value="">Seleccione</option>
                                      <option value="Activo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Activo"){ echo "selected"; } ?>>Activo</option>
                                      <option value="Inactivo" <?php if(isset($_POST["guardar_registro"]) AND $estado=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-1">
                                    <label for="regional" class="my-0">Regional</label>
                                    <select class="form-control form-control-sm form-select font-size-11" name="regional" id="regional" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1) { echo 'disabled'; } ?> required>
                                      <option value="">Seleccione</option>
                                      <option value="REGIONAL AMAZONAS" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL AMAZONAS"){ echo "selected"; } ?>>REGIONAL AMAZONAS</option>
                                      <option value="REGIONAL ANTIOQUIA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL ANTIOQUIA"){ echo "selected"; } ?>>REGIONAL ANTIOQUIA</option>
                                      <option value="REGIONAL ARAUCA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL ARAUCA"){ echo "selected"; } ?>>REGIONAL ARAUCA</option>
                                      <option value="REGIONAL ATLÁNTICO" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL ATLÁNTICO"){ echo "selected"; } ?>>REGIONAL ATLÁNTICO</option>
                                      <option value="REGIONAL BOGOTÁ" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL BOGOTÁ"){ echo "selected"; } ?>>REGIONAL BOGOTÁ</option>
                                      <option value="REGIONAL BOLÍVAR" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL BOLÍVAR"){ echo "selected"; } ?>>REGIONAL BOLÍVAR</option>
                                      <option value="REGIONAL BOYACÁ" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL BOYACÁ"){ echo "selected"; } ?>>REGIONAL BOYACÁ</option>
                                      <option value="REGIONAL CALDAS" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL CALDAS"){ echo "selected"; } ?>>REGIONAL CALDAS</option>
                                      <option value="REGIONAL CAQUETÁ" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL CAQUETÁ"){ echo "selected"; } ?>>REGIONAL CAQUETÁ</option>
                                      <option value="REGIONAL CASANARE" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL CASANARE"){ echo "selected"; } ?>>REGIONAL CASANARE</option>
                                      <option value="REGIONAL CAUCA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL CAUCA"){ echo "selected"; } ?>>REGIONAL CAUCA</option>
                                      <option value="REGIONAL CESÁR" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL CESÁR"){ echo "selected"; } ?>>REGIONAL CESÁR</option>
                                      <option value="REGIONAL CHOCÓ" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL CHOCÓ"){ echo "selected"; } ?>>REGIONAL CHOCÓ</option>
                                      <option value="REGIONAL CÓRDOBA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL CÓRDOBA"){ echo "selected"; } ?>>REGIONAL CÓRDOBA</option>
                                      <option value="REGIONAL CUNDINAMARCA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL CUNDINAMARCA"){ echo "selected"; } ?>>REGIONAL CUNDINAMARCA</option>
                                      <option value="REGIONAL GUAINÍA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL GUAINÍA"){ echo "selected"; } ?>>REGIONAL GUAINÍA</option>
                                      <option value="REGIONAL GUAVIARE" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL GUAVIARE"){ echo "selected"; } ?>>REGIONAL GUAVIARE</option>
                                      <option value="REGIONAL HUILA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL HUILA"){ echo "selected"; } ?>>REGIONAL HUILA</option>
                                      <option value="REGIONAL LA GUAJIRA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL LA GUAJIRA"){ echo "selected"; } ?>>REGIONAL LA GUAJIRA</option>
                                      <option value="REGIONAL MAGDALENA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL MAGDALENA"){ echo "selected"; } ?>>REGIONAL MAGDALENA</option>
                                      <option value="REGIONAL MAGDALENA MEDIO" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL MAGDALENA MEDIO"){ echo "selected"; } ?>>REGIONAL MAGDALENA MEDIO</option>
                                      <option value="REGIONAL META" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL META"){ echo "selected"; } ?>>REGIONAL META</option>
                                      <option value="REGIONAL NARIÑO" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL NARIÑO"){ echo "selected"; } ?>>REGIONAL NARIÑO</option>
                                      <option value="REGIONAL NORTE DE SANTANDER" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL NORTE DE SANTANDER"){ echo "selected"; } ?>>REGIONAL NORTE DE SANTANDER</option>
                                      <option value="REGIONAL PUTUMAYO" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL PUTUMAYO"){ echo "selected"; } ?>>REGIONAL PUTUMAYO</option>
                                      <option value="REGIONAL QUINDÍO" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL QUINDÍO"){ echo "selected"; } ?>>REGIONAL QUINDÍO</option>
                                      <option value="REGIONAL RISARALDA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL RISARALDA"){ echo "selected"; } ?>>REGIONAL RISARALDA</option>
                                      <option value="REGIONAL SAN ANDRÉS" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL SAN ANDRÉS"){ echo "selected"; } ?>>REGIONAL SAN ANDRÉS</option>
                                      <option value="REGIONAL SANTANDER" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL SANTANDER"){ echo "selected"; } ?>>REGIONAL SANTANDER</option>
                                      <option value="REGIONAL SUCRE" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL SUCRE"){ echo "selected"; } ?>>REGIONAL SUCRE</option>
                                      <option value="REGIONAL TOLIMA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL TOLIMA"){ echo "selected"; } ?>>REGIONAL TOLIMA</option>
                                      <option value="REGIONAL URABÁ" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL URABÁ"){ echo "selected"; } ?>>REGIONAL URABÁ</option>
                                      <option value="REGIONAL VALLE" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL VALLE"){ echo "selected"; } ?>>REGIONAL VALLE</option>
                                      <option value="REGIONAL VAUPÉS" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL VAUPÉS"){ echo "selected"; } ?>>REGIONAL VAUPÉS</option>
                                      <option value="REGIONAL VICHADA" <?php if(isset($_POST["guardar_registro"]) AND $regional=="REGIONAL VICHADA"){ echo "selected"; } ?>>REGIONAL VICHADA</option>
                                      <option value="RED CADE" <?php if(isset($_POST["guardar_registro"]) AND $regional=="RED CADE"){ echo "selected"; } ?>>RED CADE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group my-1">
                                    <label for="ciudad" class="my-0">Municipio</label>
                                    <select class="form-control form-control-sm form-select font-size-11" name="ciudad" id="ciudad" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1) { echo 'disabled'; } ?> required>
                                        <option value="">Seleccione</option>
                                        <?php for ($i=0; $i < count($resultado_registros_ciudad); $i++): ?> 
                                            <option value="<?php echo $resultado_registros_ciudad[$i][0]; ?>" <?php if(isset($_POST["guardar_registro"]) AND $ciudad==$resultado_registros_ciudad[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_ciudad[$i][2].", ".$resultado_registros_ciudad[$i][1]; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group my-1">
                                  <label for="punto_atencion" class="my-0">Punto de atención</label>
                                  <input type="text" class="form-control form-control-sm font-size-11" name="punto_atencion" id="punto_atencion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $punto_atencion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1) { echo 'readonly'; } ?> required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group my-1">
                                  <label for="direccion" class="my-0">Dirección</label>
                                  <input type="text" class="form-control form-control-sm font-size-11" name="direccion" id="direccion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $direccion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1) { echo 'readonly'; } ?> required>
                                </div>
                            </div>
                            <div class="col-md-12 mt-1">
                                <div class="form-group my-1">
                                    <div class="row" id="opciones_respuestas_usuario">
                                        <?php if(isset($usuarios)): ?>
                                            <?php for ($i=0; $i < count($usuarios); $i++): ?>
                                                <div class="row lista_usuario px-4 col-md-12">
                                                    <div class="col-md-12">
                                                        <div class="form-group my-1">
                                                            <label for="usuarios" class="my-0">Usuario</label>
                                                            <select class="form-control form-control-sm form-select font-size-11" name="usuarios[]" id="usuarios_<?php echo $i; ?>" required <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1) { echo 'disabled'; } ?>>
                                                                <option class="font-size-11" value="">Seleccione</option>
                                                                <?php for ($j=0; $j < count($resultado_registros_usuarios); $j++): ?>
                                                                    <option value="<?php echo $resultado_registros_usuarios[$j][0]; ?>" <?php if($usuarios[$i]==$resultado_registros_usuarios[$j][0]){ echo "selected"; } ?>><?php echo $resultado_registros_usuarios[$j][1]; ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                     <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']!=1): ?>
                                                        <div class="col-12 mb-1 ps-3">
                                                            <a href="#" class="btn btn-danger font-size-11 p-0" style="display: block; width: 185px;" id="del_field_usuario" title="Quitar usuario"><span class="fas fa-trash-alt"></span> Quitar usuario</a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <hr class="col-12">
                                                </div>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']!=1): ?>
                                        <a href="#" class="btn btn-primary font-size-11 p-0 mt-1" style="display: block; width: 185px;" id="add_field_usuario" title="Añadir usuario"><span class="fas fa-plus"></span> Añadir usuario</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <hr class="my-1">
                          <label for="usuario_1" class="form-label my-0 fw-bold">Horarios</label>
                          <hr class="my-1">
                          <div class="row">
                            <?php for ($j=1; $j <= count($array_dias_nombre); $j++): ?>
                              <div class="col-md-7">
                                  <div class="form-check form-switch px-5">
                                      <input class="form-check-input" type="checkbox" name="horario_dia[]" id="horario_dia_<?php echo $j; ?>" value="<?php echo $j; ?>" <?php //echo (in_array($j, $usuario_dias_data[$resultado_registros_usuario[$i][2]]['dias'])) ? 'checked' : ''; ?>>
                                      <label class="form-check-label" for="horario_dia_<?php echo $j; ?>"><?php echo $array_dias_nombre[$j]; ?></label>
                                  </div>
                              </div>
                              <div class="col-md-5">
                                <div class="form-group">
                                  <label for="horario_atencion" class="my-0">Atención</label>
                                  <input type="time" class="form-control form-control-sm font-size-11" name="horario_atencion_<?php echo $j; ?>[]" id="horario_atencion" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][$j][0]; ?>">
                                  <input type="time" class="form-control form-control-sm font-size-11" name="horario_atencion_<?php echo $j; ?>[]" id="horario_atencion" value="<?php echo $usuario_dias_data[$resultado_registros_usuario[$i][2]]['horario_atencion'][$j][1]; ?>">
                                </div>
                              </div>
                            <?php endfor; ?>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php else: ?>
                                    <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Guardar</button>
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