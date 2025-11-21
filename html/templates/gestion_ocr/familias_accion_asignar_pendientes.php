<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Gestión";
  require_once("../../iniciador.php");
  require_once('../assets/plugins/PHPOffice/vendor/autoload.php');
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  use PhpOffice\PhpSpreadsheet\IOFactory;

  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Gestión | Reasignar Pendientes";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $filtro_estado_permanente=validar_input($_GET['estado']);

  if ($filtro_estado_permanente!='null') {
      $filtro_estado_permanente=unserialize($_GET['estado']);
  } else {
      $filtro_estado_permanente=array();
  }

  $estado_url=serialize($filtro_estado_permanente);
  $estado_url=urlencode($estado_url);
  $url_salir="familias_accion?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&estado=".$estado_url;
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  if(isset($_POST["guardar_registro"])){
      
      if($_SESSION[APP_SESSION.'_registro_creado_reasignar_pendientes']!=1){
          if ($_FILES['base_casos']["error"] > 0) {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar el documento');";
          } else {
              /*ahora co la funcion move_uploaded_file lo guardaremos en el destino que queramos*/
              $nombre_directorio="storage_temporal/";
              $nombre_archivo=$_FILES['base_casos']['name'];
              if (move_uploaded_file($_FILES['base_casos']['tmp_name'], $nombre_directorio.$nombre_archivo)) {
                  $nombre_archivo = $nombre_directorio.$nombre_archivo;

                  if (file_exists ($nombre_archivo)){
                      clearstatcache();
                      // unset($objPHPExcel);
                      // unset($objReader);
                      // ini_set('memory_limit', '2048M');

                      $documento = IOFactory::load($nombre_archivo);
                      $hojaActual = $documento->getSheet(0);
                      $numeroMayorDeFila = $hojaActual->getHighestRow();

                      $numero_total_registros=intval($numeroMayorDeFila)-1;

                      $control_item=0;
                      for ($indicefila = 2; $indicefila <= $numeroMayorDeFila; $indicefila++) {
                          $columna_a = $hojaActual->getCellByColumnAndRow(1, $indicefila)->getValue();
                          $columna_b = $hojaActual->getCellByColumnAndRow(2, $indicefila)->getValue();

                          $array_data_base[$control_item]['cod_familia']=trim(validar_input($columna_a));
                          $array_data_base[$control_item]['usuario']=trim(validar_input($columna_b));
                          
                          $control_item++;
                      }

                      // echo "<pre>";
                      // print_r($usuarios_auditado);
                      // print_r($array_usuarios_seleccionables);
                      // print_r($array_base_auditar);
                      // echo "</pre>";

                      $consulta_string_caso="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_gestion_estado` FROM `gestion_ocr_resultado` WHERE `ocrr_cod_familia`=? AND (`ocrr_gestion_estado`='Aplazado' OR `ocrr_gestion_estado`='Pendiente llamada' OR `ocrr_gestion_estado`='Intento Contacto-Fallido' OR `ocrr_gestion_estado`='Nuevo Contacto-Error Subsanación' OR `ocrr_gestion_estado`='Intento Contacto-Agotado' OR `ocrr_gestion_estado`='Aplazado Segunda Revisión' OR `ocrr_gestion_estado`='Pendiente llamada-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Fallido-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Agotado-Segunda Revisión' OR `ocrr_gestion_estado`='Documentos Cargados-Segunda Revisión' OR `ocrr_gestion_estado`='Aplazado Tercera Revisión')";
                      $consulta_registros_caso = $enlace_db->prepare($consulta_string_caso);
                      $consulta_registros_caso->bind_param("s", $cod_familia);

                      // Prepara la sentencia
                      $sentencia_insert_log = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado_avances`(`gora_codfamilia`, `gora_estado`, `gora_correo`, `gora_observaciones`, `gora_llamada_tipificacion`, `gora_llamada_id`, `gora_registro_usuario`) VALUES (?,'Reasignado','',?,'','',?)");

                      // Agrega variables a sentencia preparada
                      $sentencia_insert_log->bind_param('sss', $cod_familia, $observaciones, $_SESSION[APP_SESSION.'_session_usu_id']);                      

                      // Prepara la sentencia
                      $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_ocr_resultado` SET `ocrr_gestion_agente`=? WHERE `ocrr_id`=?");

                      // Agrega variables a sentencia preparada
                      $consulta_actualizar->bind_param('ss', $responsable, $id_registro);

                      $control_insert=0;
                      $control_fail=0;
                      $string_fail="";
                      
                      for ($i=0; $i < count($array_data_base); $i++) { 
                          $cod_familia=$array_data_base[$i]['cod_familia'];
                          $responsable=$array_data_base[$i]['usuario'];
                          $observaciones='Reasignar caso Id: '.$responsable;
                          $consulta_registros_caso->execute();
                          $resultado_registros_caso = $consulta_registros_caso->get_result()->fetch_all(MYSQLI_NUM);

                          if (count($resultado_registros_caso)==1) {
                            $id_registro=$resultado_registros_caso[0][0];
                            // Ejecuta sentencia preparada
                            $consulta_actualizar->execute();
                            if (comprobarSentencia($enlace_db->info)) {
                                $sentencia_insert_log->execute();
                                $control_insert++;
                            } else {
                                $control_fail++;
                                $string_fail.='No actualizado: '.$cod_familia."\r\n";
                                $control_errores_detalle[]='No actualizado: '.$cod_familia.'<br>';
                            }
                          } else {
                            $control_fail++;
                            $string_fail.='No encontrado: '.$cod_familia."\r\n";
                            $control_errores_detalle[]='No encontrado: '.$cod_familia.'<br>';
                          }
                      }

                      if (($control_insert+$control_fail)==count($array_data_base)) {
                          $respuesta_accion = "alertButton('success', 'Registro creado', 'Base cargada exitosamente | Cargado: ".$control_insert." | Error: ".$control_fail."');";
                          $_SESSION[APP_SESSION.'_registro_creado_reasignar_pendientes']=1;

                          // $nombre_temporal_control="storage_temporal/CARGAR_FAIL".date('YmdHis').".txt";
                          // $archivo_fail = fopen($nombre_temporal_control,'a');
                          // fputs($archivo_fail,$string_fail);
                          // fclose($archivo_fail);
                      } else {
                          $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar base, por favor intente nuevamente');";
                      }
                  } else {
                    $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar base, por favor intente nuevamente');";
                  }
              } else {
                $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar base, por favor intente nuevamente');";
              }
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente');";
      }
  }
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
                    <div class="card-body row">
                      <?php if($control_fail>0): ?>
                          <div class="col-md-12">
                              <p class="alert alert-danger p-1 font-size-11">Por favor verifique los siguientes errores:</p>
                              <?php for ($i=0; $i < count($control_errores_detalle); $i++): ?>
                              <p class="alert alert-warning p-1 font-size-11 my-0"><?php echo $control_errores_detalle[$i]; ?></p>
                              <?php endfor; ?>
                          </div>
                      <?php endif; ?>
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="base_casos" class="my-0">Documento base</label>
                              <input class="form-control form-control-sm custom-file-input" name="base_casos" id="inputGroupFile01" type="file" <?php if(isset($_SESSION[APP_SESSION.'_registro_creado_reasignar_pendientes'])) { echo 'disabled'; } ?> accept=".xlsx, .XLSX" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_reasignar_pendientes']==1): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Cargar base</button>
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
          </form>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>