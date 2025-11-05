<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Calculadora Muestral";
  require_once("../../iniciador.php");
  require_once("../../app/functions/validar_festivos.php");
  require_once('../assets/plugins/PHPOffice/vendor/autoload.php');
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  use PhpOffice\PhpSpreadsheet\IOFactory;
  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');

  /*VARIABLES*/
  $title = "Calidad";
  $subtitle = "Calculadora Muestral | Configuración - Cargar Transacciones";
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $fecha_dia=validar_input(base64_decode($_GET['fecha']));
  $mes_calculadora=validar_input($_GET['date']);
  $url_salir="cmuestral_configurar?reg=".base64_encode($id_registro)."&date=".$mes_calculadora;

  $consulta_string_usuarios="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_fecha_ingreso_piloto`, `usu_fecha_incorporacion` FROM `administrador_usuario` WHERE `usu_estado`='Activo' ORDER BY `usu_nombres_apellidos` ASC";
  $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
  $consulta_registros_usuarios->execute();
  $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_usuarios); $i++) { 
      $usuarios_detalle[$resultado_registros_usuarios[$i][1]]['nombre']=$resultado_registros_usuarios[$i][1];
      $usuarios_detalle[$resultado_registros_usuarios[$i][0]]['fecha_piloto']=$resultado_registros_usuarios[$i][2];
      $usuarios_detalle[$resultado_registros_usuarios[$i][0]]['fecha_ingreso']=$resultado_registros_usuarios[$i][3];
  }

  $consulta_string_segmento="SELECT `cms_id`, `cms_calculadora`, `cms_nombre_segmento`, `cms_peso` FROM `gestion_calidad_cmuestral_segmento` WHERE `cms_calculadora`=? ORDER BY `cms_nombre_segmento` ASC";
  $consulta_registros_segmento = $enlace_db->prepare($consulta_string_segmento);
  $consulta_registros_segmento->bind_param("s", $id_registro);
  $consulta_registros_segmento->execute();
  $resultado_registros_segmento = $consulta_registros_segmento->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_semana="SELECT `cmm_id`, `cmm_calculadora`, `cmm_mes`, `cmm_segmento`, `cmm_total_mes`, `cmm_muestra_calculada`, `cmm_muestra_auditoria`, `cmm_numero_agentes`, `cmm_muestras_agente_mes`, `cmm_muestras_agente_semana`, `cmm_semana_dias`, `cmm_semana_peso`, `cmm_semana_porcentaje`, `cmm_semana_muestras`, `cmm_semana_inicio`, `cmm_semana_fin`, `cmm_muestra_realizada`, `cmm_muestra_recalculada` FROM `gestion_calidad_cmuestral_mensual` WHERE `cmm_calculadora`=? AND `cmm_mes`=?";
  $consulta_registros_semana = $enlace_db->prepare($consulta_string_semana);
  $consulta_registros_semana->bind_param("ss", $id_registro, $mes_calculadora);
  $consulta_registros_semana->execute();
  $resultado_registros_semana = $consulta_registros_semana->get_result()->fetch_all(MYSQLI_NUM);

  $array_usuarios_seleccionables=array();

  if(isset($_POST["guardar_registro"])){
      $base_transacciones=validar_input($_POST['base_transacciones']);
      $muestras=validar_input($_POST['muestras']);

      $consulta_string_muestras="SELECT `cmm_id`, `cmm_calculadora`, `cmm_mes`, `cmm_fecha`, `cmm_segmento`, `cmm_usuario`, `cmm_monitor`, `cmm_muestra_auditoria`, `cmm_muestra_fecha_hora` FROM `gestion_calidad_cmuestral_muestras` WHERE `cmm_calculadora`=? AND `cmm_mes`=?";
      $consulta_registros_muestras = $enlace_db->prepare($consulta_string_muestras);
      $consulta_registros_muestras->bind_param("ss", $id_registro, $mes_calculadora);
      $consulta_registros_muestras->execute();
      $resultado_registros_muestras = $consulta_registros_muestras->get_result()->fetch_all(MYSQLI_NUM);

      $total_semana=round(count($resultado_registros_usuarios))-count($resultado_registros_muestras);
      $total_diario=round($total_semana/$resultado_registros_semana[0][11]);
      $usuarios_auditado=array();
      for ($i=0; $i < count($resultado_registros_muestras); $i++) { 
        $usuarios_auditado[]=$resultado_registros_muestras[$i][5];
      }

      $id_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

      if($_SESSION[APP_SESSION.'registro_cargue_base_transacciones']!=1){
          if ($_FILES['documento']["error"] > 0) {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar el documento');";
          } else {
              /*ahora co la funcion move_uploaded_file lo guardaremos en el destino que queramos*/
              $nombre_directorio="storage_temporal/";
              $nombre_archivo=$_FILES['documento']['name'];
              if (move_uploaded_file($_FILES['documento']['tmp_name'], $nombre_directorio.$nombre_archivo)) {
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
                      $control_errores=0;
                      for ($indicefila = 2; $indicefila <= $numeroMayorDeFila; $indicefila++) {
                          if ($base_transacciones=='Unificada') {
                              $columna_a = $hojaActual->getCellByColumnAndRow(1, $indicefila)->getValue();
                              $columna_b = $hojaActual->getCellByColumnAndRow(2, $indicefila)->getValue();
                              $columna_c = $hojaActual->getCellByColumnAndRow(3, $indicefila)->getValue();
                              $columna_d = $hojaActual->getCellByColumnAndRow(4, $indicefila)->getFormattedValue();
                              $columna_e = $hojaActual->getCellByColumnAndRow(5, $indicefila)->getValue();

                              if (trim(validar_input($columna_a))!='' AND trim(validar_input($columna_b))!='' AND trim(validar_input($columna_c))!='' AND trim(validar_input($columna_d))!='' AND trim(validar_input($columna_e))!='') {
                                $array_data_base[$control_item]['id_transaccion']=trim(validar_input($columna_a));//ID TRANSACCIÓN
                                $array_data_base[$control_item]['id_agente']=trim(validar_input($columna_b));//ID AGENTE
                                $array_data_base[$control_item]['nombre_agente']=trim(validar_input($columna_c));//NOMBRE AGENTE
                                $array_data_base[$control_item]['fecha']=date('Y-m-d H:i:s', strtotime($columna_d));//FECHA TRANSACCIÓN
                                $array_data_base[$control_item]['canal']=trim(validar_input($columna_e));//CANAL-PROCESO
                                
                                $temp_fecha_piloto=$usuarios_detalle[$array_data_base[$control_item]['id_agente']]['fecha_piloto'];

                                if ($temp_fecha_piloto!="") {
                                    $temp_usuario_estado=1;
                                    $limite_fecha_piloto = date("Y-m-d", strtotime("+ 30 day", strtotime($temp_fecha_piloto)));
                                    if (date('Y-m-d')>$limite_fecha_piloto) {
                                        $fecha_piloto_estado=1;
                                    } else {
                                        $fecha_piloto_estado=0;
                                    }
                                } else {
                                    $temp_usuario_estado=1;
                                    $fecha_piloto_estado=1;
                                    // $control_errores++;
                                    // $control_errores_detalle[]='Usuario o fecha área no encontrado para la fila: '.$indicefila.'<br>';
                                }
                                
                                if ($temp_usuario_estado AND $fecha_piloto_estado) {
                                    $array_data_base[$control_item]['estado']='seleccionable';
                                    if (!isset($array_base_seleccionables[$array_data_base[$control_item]['id_agente']])) {
                                        $array_base_seleccionables[$array_data_base[$control_item]['id_agente']]=array();
                                    }
                                    $array_base_seleccionables[$array_data_base[$control_item]['id_agente']][]=$control_item;
                                    $array_usuarios_seleccionables[]=$array_data_base[$control_item]['id_agente'];
                                } elseif(!$temp_usuario_estado){
                                    $array_data_base[$control_item]['estado']='excluido_usuario';
                                } elseif(!$fecha_piloto_estado){
                                    $array_data_base[$control_item]['estado']='excluido_fecha_area';
                                } else {
                                    $array_data_base[$control_item]['estado']='no_seleccionable';
                                }

                                $control_item++;
                              }
                          }
                      }

                      $array_usuarios_seleccionables=array_values(array_unique($array_usuarios_seleccionables));

                      shuffle($array_usuarios_seleccionables);
                      // echo "agentes: ";
                      // echo count($array_usuarios_seleccionables);
                      $array_usuarios_auditar=array();
                      if ($muestras<=count($array_usuarios_seleccionables)) {
                        $control_muestras=0;
                        for ($k=0; $control_muestras < $muestras; $k++) { 
                            if (isset($array_base_seleccionables[$array_usuarios_seleccionables[$k]])) {
                                if (count($array_base_seleccionables[$array_usuarios_seleccionables[$k]])>0 AND !in_array($array_usuarios_seleccionables[$k], $usuarios_auditado)) {
                                    shuffle($array_base_seleccionables[$array_usuarios_seleccionables[$k]]);
                                    $array_base_auditar[$array_usuarios_seleccionables[$k]][]=$array_base_seleccionables[$array_usuarios_seleccionables[$k]][0];
                                    $array_usuarios_auditar[]=$array_usuarios_seleccionables[$k];
                                    $control_muestras++;
                                }
                            }

                            if ($k>=count($array_usuarios_seleccionables)) {
                                break;
                            }
                        }

                        // echo "<pre>";
                        // print_r($usuarios_auditado);
                        // print_r($array_usuarios_seleccionables);
                        // print_r($array_base_auditar);
                        // echo "</pre>";

                        // Prepara la sentencia
                        $sentencia_insert_data = $enlace_db->prepare("INSERT INTO `gestion_calidad_cmuestral_transacciones`(`gcmt_calculadora`, `gcmt_mes`, `gcmt_fecha`, `gcmt_segmento`, `gcmt_transaccion_id`, `gcmt_campo_1`, `gcmt_campo_2`, `gcmt_campo_3`, `gcmt_campo_4`, `gcmt_campo_5`, `gcmt_campo_6`, `gcmt_campo_7`, `gcmt_campo_8`, `gcmt_campo_9`, `gcmt_campo_10`, `gcmt_estado`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                        
                        // Agrega variables a sentencia preparada
                        $sentencia_insert_data->bind_param('ssssssssssssssss', $id_registro, $mes_calculadora, $fecha_dia, $base_transacciones, $gcmt_transaccion_id, $gcmt_campo_1, $gcmt_campo_2, $gcmt_campo_3, $gcmt_campo_4, $gcmt_campo_5, $gcmt_campo_6, $gcmt_campo_7, $gcmt_campo_8, $gcmt_campo_9, $gcmt_campo_10, $gcmt_estado);
                        
                        $control_insert=0;
                        $control_fail=0;
                        $string_fail="";
                        
                        for ($i=0; $i < count($array_data_base); $i++) { 
                            $gcmt_transaccion_id=$array_data_base[$i]['id_transaccion'];
                            $gcmt_campo_1=$array_data_base[$i]['id_agente'];
                            $gcmt_campo_2=$array_data_base[$i]['fecha'];
                            $gcmt_campo_3=$array_data_base[$i]['canal'];
                            $gcmt_campo_4='';
                            $gcmt_campo_5='';
                            $gcmt_campo_6='';
                            $gcmt_campo_7='';
                            $gcmt_campo_8='';
                            $gcmt_campo_9='';
                            $gcmt_campo_10='';
                            $gcmt_estado=$array_data_base[$i]['estado'];
                            
                            if ($sentencia_insert_data->execute()) {
                                $control_insert++;
                            } else {
                                $control_fail++;
                                $string_fail.=$columna_a."\r\n";
                            }
                        }

                        if (($control_insert+$control_fail)==count($array_data_base)) {
                            //insert log eventos
                                $consulta_string_log = "INSERT INTO `administrador_log`(`clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_registro_usuario`) VALUES (?,?,?,?,?)";
                            
                                $log_modulo=$modulo_plataforma;
                                $log_tipo="crear";
                                $log_accion="Crear registro";
                                $log_detalle="Cargue base transacciones [".$base_transacciones."]";
                                $log_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
                                
                                $consulta_registros_log = $enlace_db->prepare($consulta_string_log);
                                $consulta_registros_log->bind_param("sssss", $log_modulo, $log_tipo, $log_accion, $log_detalle, $log_usuario);
                                $consulta_registros_log->execute();
                            //insert log eventos

                            // Prepara la sentencia
                            $sentencia_insert_muestras = $enlace_db->prepare("INSERT INTO `gestion_calidad_cmuestral_muestras`(`cmm_calculadora`, `cmm_mes`, `cmm_fecha`, `cmm_segmento`, `cmm_usuario`, `cmm_monitor`, `cmm_muestra_auditoria`, `cmm_muestra_fecha_hora`) VALUES (?,?,?,?,?,?,?,?)");
                            
                            // Agrega variables a sentencia preparada
                            $sentencia_insert_muestras->bind_param('ssssssss', $id_registro, $mes_calculadora, $fecha_dia, $canal, $cmm_usuario, $cmm_monitor, $cmm_muestra_auditoria, $cmm_muestra_fecha_hora);

                            for ($i=0; $i < count($array_usuarios_auditar); $i++) { 
                              $cmm_usuario=$array_usuarios_auditar[$i];
                              $cmm_monitor='';
                              $cmm_muestra_auditoria=$array_data_base[$array_base_auditar[$cmm_usuario][0]]['id_transaccion'];
                              $cmm_muestra_fecha_hora=$array_data_base[$array_base_auditar[$cmm_usuario][0]]['fecha'];
                              $canal=$array_data_base[$array_base_auditar[$cmm_usuario][0]]['canal'];
                              // echo "INSERT INTO `gestion_calidad_cmuestral_muestras`(`cmm_calculadora`, `cmm_mes`, `cmm_fecha`, `cmm_segmento`, `cmm_usuario`, `cmm_monitor`, `cmm_muestra_auditoria`) VALUES ('".$id_registro."','".$mes_calculadora."','".$fecha_dia."','".$base_transacciones."','".$cmm_usuario."','".$cmm_monitor."','".$cmm_muestra_auditoria."')<br>";
                              if ($cmm_usuario!="" AND $cmm_muestra_auditoria!="") {
                                if ($sentencia_insert_muestras->execute()) {

                                } else {
                                    // echo "no insert: ".$cmm_usuario.' | '.$cmm_muestra_auditoria.'<br>';
                                }
                              }
                            }


                            $respuesta_accion = "alertButton('success', 'Registro creado', 'Base cargada exitosamente | Cargado: ".$control_insert." | Error: ".$control_fail."');";
                            $_SESSION[APP_SESSION.'registro_cargue_base_transacciones']=1;

                            $nombre_temporal_control="storage_temporal/CARGAR_FAIL".date('YmdHis').".txt";
                            $archivo_fail = fopen($nombre_temporal_control,'a');
                            fputs($archivo_fail,$string_fail);
                            fclose($archivo_fail);
                        } else {
                            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar base, por favor intente nuevamente');";
                        }
                      } else {
                        //No alcanzan los usuarios seleccionables para las muestras
                        $respuesta_accion = "alertButton('error', 'Error', 'Cantidad de muestras superior a cantidad de agentes, por favor intente nuevamente');";

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
            <div class="col-lg-7 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <?php if($control_errores>0): ?>
                          <div class="col-md-12">
                              <p class="alert alert-danger p-1 font-size-11">Por favor verifique los siguientes errores:</p>
                              <?php for ($i=0; $i < count($control_errores_detalle); $i++): ?>
                              <p class="alert alert-warning p-1 font-size-11 my-0"><?php echo $control_errores_detalle[$i]; ?></p>
                              <?php endfor; ?>
                          </div>
                      <?php endif; ?>
                      
                      <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="mes" class="m-0">Mes</label>
                              <input type="text" class="form-control form-control-sm" name="mes" id="mes" value="<?php echo $mes_calculadora; ?>" readonly required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="fecha" class="m-0">Fecha</label>
                              <input type="text" class="form-control form-control-sm" name="fecha" id="fecha" value="<?php echo $fecha_dia; ?>" readonly required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="base_transacciones" class="m-0">Base transacciones</label>
                                <select class="form-control form-control-sm" name="base_transacciones" id="base_transacciones" <?php if($_SESSION[APP_SESSION.'registro_cargue_base_transacciones']==1) { echo 'disabled'; } ?> required>
                                    <option value="">Seleccione</option>
                                    <option value="Unificada" <?php if(isset($_POST["guardar_registro"]) AND $base_transacciones=='Unificada'){ echo "selected"; } ?>>Unificada</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="muestras" class="m-0">Muestras</label>
                              <input type="number" class="form-control form-control-sm" name="muestras" id="muestras" min="10" value="<?php echo $_POST["muestras"]; ?>" <?php if(isset($_SESSION[APP_SESSION.'registro_cargue_base_transacciones'])) { echo 'disabled'; } ?> required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="documento" class="my-0">Documento base</label>
                                <input class="form-control form-control-sm custom-file-input" name="documento" id="inputGroupFile01" type="file" <?php if(isset($_SESSION[APP_SESSION.'registro_cargue_base_transacciones'])) { echo 'disabled'; } ?> accept=".xlsx, .XLSX" required>
                            </div>
                        </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'registro_cargue_base_transacciones']==1): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Cargar transacciones</button>
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
  <script type="text/javascript">
      $("#inputGroupFile01").change(function(){
          var valor_opcion = document.getElementById("inputGroupFile01").files[0].name;

          if (valor_opcion!="") {
              document.getElementById('inputGroupFile01label').innerHTML=valor_opcion.substring(0, 25)+"...";
              $("#inputGroupFile01label").addClass("color-verde");
          }
      });
  </script>
</body>
</html>