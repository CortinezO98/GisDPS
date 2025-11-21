<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  /*VARIABLES*/
  $parametro=validar_input($_GET['par']);
  $title = "Canal Escrito";
  $subtitle = "Productividad | Justificar";
  $pagina=validar_input($_GET['pagina']);
  $grupo=validar_input(base64_decode($_GET['grupo']));
  $formulario=validar_input(base64_decode($_GET['formulario']));
  $fecha=validar_input(base64_decode($_GET['fecha']));
  $url_salir="reparto_estadisticas";

  $consulta_string="SELECT `cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`, `cep_registro_fecha`, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, `cep_productividad_ajustada` FROM `gestion_ce_productividad` LEFT JOIN `administrador_usuario` AS TU ON `gestion_ce_productividad`.`cep_agente`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON `gestion_ce_productividad`.`cep_coordinador`=TC.`usu_id` WHERE `cep_formulario`=? AND `cep_fecha`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("ss", $formulario, $fecha);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
    
    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_ce_productividad` SET `cep_tipologia`=?, `cep_novedad`=?, `cep_comentarios`=?, `cep_actualiza_fecha`=?, `cep_productividad_ajustada`=? WHERE `cep_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('ssssss', $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_productividad_ajustada, $cep_id);
    
    $control_update=0;
    $control_justifica=0;
    for ($i=0; $i < count($resultado_registros); $i++) { 
      $cep_id=$resultado_registros[$i][0];
      $cep_actualiza_fecha=date('Y-m-d H:i:s');
      $cep_tipologia=$_POST['tipologia_'.$resultado_registros[$i][0]];
      
      if ($cep_tipologia!='') {
        $control_justifica++;
        
        if (isset($_POST['novedad_'.$resultado_registros[$i][0]])) {
          $novedad=$_POST['novedad_'.$resultado_registros[$i][0]];

          $cep_novedad=implode(';', $novedad);
        } else {
          $cep_novedad='';
        }

        $cep_comentarios=$_POST['comentarios_'.$resultado_registros[$i][0]];
        $cep_productividad_ajustada=$_POST['productividad_ajustada_'.$resultado_registros[$i][0]];

        // Ejecuta sentencia preparada
        $consulta_actualizar->execute();
        
        if (comprobarSentencia($enlace_db->info)) {
            $control_update++;
        }
      }
    }

    if ($control_update==$control_justifica) {
      $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

  $consulta_string="SELECT `cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`, `cep_registro_fecha`, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, `cep_productividad_ajustada` FROM `gestion_ce_productividad` LEFT JOIN `administrador_usuario` AS TU ON `gestion_ce_productividad`.`cep_agente`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON `gestion_ce_productividad`.`cep_coordinador`=TC.`usu_id` WHERE `cep_formulario`=? AND `cep_fecha`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("ss", $formulario, $fecha);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $array_novedad['Administrativos'][]='Permiso Remunerado';
  $array_novedad['Administrativos'][]='Permiso No Remunerado';
  $array_novedad['Administrativos'][]='Día De La Familia/Dia Mágico';
  $array_novedad['Administrativos'][]='Incapacidad';
  $array_novedad['Administrativos'][]='Licencia De Luto';
  $array_novedad['Administrativos'][]='Licencia De Maternidad';
  $array_novedad['Administrativos'][]='Suspensión';
  $array_novedad['Administrativos'][]='Retiro';
  $array_novedad['Administrativos'][]='Calamidad';
  $array_novedad['Administrativos'][]='Vacaciones';
  $array_novedad['Administrativos'][]='Día Compensatorio';
  $array_novedad['Administrativos'][]='Licencia de Matrimonio';
  $array_novedad['Administrativos'][]='Dia Graduación Personal';
  $array_novedad['Administrativos'][]='Dia Graduación Hijo';

  $array_novedad['Operativos'][]='Caso Complejo';
  $array_novedad['Operativos'][]='Coaching';
  $array_novedad['Operativos'][]='Falla Correo PS';
  $array_novedad['Operativos'][]='Apoyo a otras áreas';
  $array_novedad['Operativos'][]='Nuevo en el Proceso';
  $array_novedad['Operativos'][]='Desplazamiento Operación';
  $array_novedad['Operativos'][]='Fallas Internet/luz/ equipo';
  $array_novedad['Operativos'][]='Canguro en otros procesos';
  $array_novedad['Operativos'][]='Curva de aprendizaje';
  $array_novedad['Operativos'][]='Apoyo a otras áreas de Canal escrito';
  $array_novedad['Operativos'][]='Mesa de Servicios - Tecnología';
  $array_novedad['Operativos'][]='Falla Aplicativos PS';
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
            <div class="col-lg-12 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12 mb-2">
                          <p class="alert background-principal color-blanco py-1 px-2 my-0"><i class="fas fa-chart-pie btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Formulario: <?php echo $resultado_registros[0][1]; ?></p>
                        </div>
                        <div class="table-responsive table-fixed" id="headerFixTable">
                          <table class="table table-hover table-bordered table-striped">
                            <thead>
                              <tr>
                                <th class="px-1 py-2">Fecha</th>
                                <th class="px-1 py-2">Coordinador</th>
                                <th class="px-1 py-2">Agente</th>
                                <th class="px-1 py-2" style="min-width: 250px;">Productividad</th>
                                <th class="px-1 py-2">Productividad Ajustada</th>
                                <th class="px-1 py-2">Tipología</th>
                                <th class="px-1 py-2">Novedad</th>
                                <th class="px-1 py-2">Comentarios</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                              <?php
                                $productividad_agente=number_format($resultado_registros[$i][7], 2, '.', '');
                                if ($productividad_agente==100) {
                                  $color_progress='bg-success';
                                } elseif ($productividad_agente>=90) {
                                  $color_progress='bg-warning';
                                } else {
                                  $color_progress='bg-danger';
                                }
                              ?>
                              <tr>
                                <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4]; ?></td>
                                <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][14]; ?></td>
                                <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][13]; ?></td>
                                <td class="p-1 font-size-11">
                                  <div class="progress" style="height: 14px;">
                                    <div class="progress-bar <?php echo $color_progress; ?>" role="progressbar" style="width: <?php echo $productividad_agente; ?>%;" aria-valuenow="<?php echo $productividad_agente; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $productividad_agente; ?>%</div>
                                  </div>
                                </td>
                                <td class="p-1 font-size-11">
                                  <input type="number" class="form-control form-control-sm font-size-11" name="productividad_ajustada_<?php echo $resultado_registros[$i][0]; ?>" id="productividad_ajustada_<?php echo $resultado_registros[$i][0]; ?>" min="0" max="100" step="0.1" value="<?php echo $resultado_registros[$i][15]; ?>">
                                </td>
                                <td class="p-1 font-size-11">
                                  <select class="form-control form-control-sm form-select" name="tipologia_<?php echo $resultado_registros[$i][0]; ?>" id="tipologia_<?php echo $resultado_registros[$i][0]; ?>" onchange="validar_novedad('<?php echo $resultado_registros[$i][0]; ?>');">
                                      <option class="font-size-11 py-0" value="">Seleccione</option>
                                      <option class="font-size-11 py-0" value="Administrativos" <?php if($resultado_registros[$i][8]=='Administrativos'){ echo "selected"; } ?>>Administrativos</option>
                                      <option class="font-size-11 py-0" value="Operativos" <?php if($resultado_registros[$i][8]=='Operativos'){ echo "selected"; } ?>>Operativos</option>
                                  </select>
                                </td>
                                <td class="p-1 font-size-11">
                                  <select class="selectpicker form-control form-control-sm form-select" data-live-search="false" data-width="300px" data-container="body" name="novedad_<?php echo $resultado_registros[$i][0]; ?>[]" id="novedad_<?php echo $resultado_registros[$i][0]; ?>" title="Seleccione" multiple>
                                      <?php if($resultado_registros[$i][8]!=''): ?>
                                        <?php
                                          $array_novedad_item=explode(';', $resultado_registros[$i][9]);
                                        ?>

                                        <?php for ($j=0; $j < count($array_novedad[$resultado_registros[$i][8]]); $j++): ?>
                                          <option class="font-size-11 py-0" value="<?php echo $array_novedad[$resultado_registros[$i][8]][$j]; ?>" <?php if(in_array($array_novedad[$resultado_registros[$i][8]][$j], $array_novedad_item)){ echo "selected"; } ?>><?php echo $array_novedad[$resultado_registros[$i][8]][$j]; ?></option>
                                          
                                        <?php endfor; ?>
                                      <?php endif; ?>
                                  </select>
                                </td>
                                <td class="p-1 font-size-11">
                                  <textarea class="form-control form-control-sm height-100" name="comentarios_<?php echo $resultado_registros[$i][0]; ?>" id="comentarios_<?php echo $resultado_registros[$i][0]; ?>"><?php echo $resultado_registros[$i][10]; ?></textarea>
                                </td>
                              </tr>
                              <?php endfor; ?>
                            </tbody>
                          </table>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Guardar</button>
                                <?php if(isset($_POST["guardar_registro"])): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php endif; ?>
                                <?php if(!isset($_POST["guardar_registro"])): ?>
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
      function validar_novedad(id){
          var tipologia_opcion = document.getElementById("tipologia_"+id);
          var tipologia = tipologia_opcion.options[tipologia_opcion.selectedIndex].value;

          $("#novedad_"+id).html("");
          $('#novedad_'+id).selectpicker('destroy');
          $('#novedad_'+id).selectpicker('refresh');

          if(tipologia=="Administrativos") {
              $("#novedad_"+id).html('<option class="font-size-11 py-0" value="Permiso Remunerado">Permiso Remunerado</option>\
                <option class="font-size-11 py-0" value="Permiso No Remunerado">Permiso No Remunerado</option>\
                <option class="font-size-11 py-0" value="Día De La Familia/Dia Mágico">Día De La Familia/Dia Mágico</option>\
                <option class="font-size-11 py-0" value="Incapacidad">Incapacidad</option>\
                <option class="font-size-11 py-0" value="Licencia De Luto">Licencia De Luto</option>\
                <option class="font-size-11 py-0" value="Licencia De Maternidad">Licencia De Maternidad</option>\
                <option class="font-size-11 py-0" value="Suspensión">Suspensión</option>\
                <option class="font-size-11 py-0" value="Retiro">Retiro</option>\
                <option class="font-size-11 py-0" value="Calamidad">Calamidad</option>\
                <option class="font-size-11 py-0" value="Vacaciones">Vacaciones</option>\
                <option class="font-size-11 py-0" value="Día Compensatorio">Día Compensatorio</option>\
                <option class="font-size-11 py-0" value="Licencia de Matrimonio">Licencia de Matrimonio</option>\
                <option class="font-size-11 py-0" value="Dia Graduación Personal">Dia Graduación Personal</option>\
                <option class="font-size-11 py-0" value="Dia Graduación Hijo">Dia Graduación Hijo</option>\
                ');
              $('#novedad_'+id).selectpicker('refresh');
          } else {
              $("#novedad_"+id).html('<option class="font-size-11 py-0" value="Caso Complejo">Caso Complejo</option>\
                <option class="font-size-11 py-0" value="Coaching">Coaching</option>\
                <option class="font-size-11 py-0" value="Falla Correo PS">Falla Correo PS</option>\
                <option class="font-size-11 py-0" value="Apoyo a otras áreas">Apoyo a otras áreas</option>\
                <option class="font-size-11 py-0" value="Nuevo en el Proceso">Nuevo en el Proceso</option>\
                <option class="font-size-11 py-0" value="Desplazamiento Operación">Desplazamiento Operación</option>\
                <option class="font-size-11 py-0" value="Fallas Internet/luz/ equipo">Fallas Internet/luz/ equipo</option>\
                <option class="font-size-11 py-0" value="Canguro en otros procesos">Canguro en otros procesos</option>\
                <option class="font-size-11 py-0" value="Curva de aprendizaje">Curva de aprendizaje</option>\
                <option class="font-size-11 py-0" value="Apoyo a otras áreas de Canal escrito">Apoyo a otras áreas de Canal escrito</option>\
                <option class="font-size-11 py-0" value="Mesa de Servicios - Tecnología">Mesa de Servicios - Tecnología</option>\
                <option class="font-size-11 py-0" value="Falla Aplicativos PS">Falla Aplicativos PS</option>\
                ');
              $('#novedad_'+id).selectpicker('refresh');
          }
      }
  </script>
</body>
</html>