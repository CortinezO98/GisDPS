<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Usuarios";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  require_once('../assets/plugins/PHPOffice/vendor/autoload.php');
  use PhpOffice\PhpSpreadsheet\IOFactory;

  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');

  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Usuarios | Crear Masivo";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $url_salir="familias_accion_usuarios?pagina=".$pagina."&id=".$filtro_permanente;

    $consulta_string_usuarios="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE 1=1 ORDER BY `usu_nombres_apellidos` ASC";
    $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
    $consulta_registros_usuarios->execute();
    $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

    for ($i=0; $i < count($resultado_registros_usuarios); $i++) { 
        $usuarios_detalle[$resultado_registros_usuarios[$i][0]]['nombres_apellidos']=$resultado_registros_usuarios[$i][1];
    }

    if(isset($_POST["guardar_registro"])){
        if ($_FILES['documento']["error"] > 0) {
            $message_error = "Problemas al cargar el documento, por favor intente más tarde!";
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
                        $columna_a = $hojaActual->getCellByColumnAndRow(1, $indicefila)->getValue();
                        $columna_b = $hojaActual->getCellByColumnAndRow(2, $indicefila)->getValue();
                        $columna_c = $hojaActual->getCellByColumnAndRow(3, $indicefila)->getValue();
                        $columna_d = $hojaActual->getCellByColumnAndRow(4, $indicefila)->getValue();
                        $columna_e = $hojaActual->getCellByColumnAndRow(5, $indicefila)->getValue();
                        $columna_f = $hojaActual->getCellByColumnAndRow(6, $indicefila)->getValue();
                        
                        $array_data_base[$control_item]['id']=trim(validar_input($columna_a));//ID USUARIO
                        $array_data_base[$control_item]['usuario']=trim(validar_input($columna_b));//USUARIO ACCESO
                        $array_data_base[$control_item]['nombres']=trim(validar_input($columna_c));//NOMBRES Y APELLIDOS
                        $array_data_base[$control_item]['correo']=trim(validar_input($columna_d));//CORREO
                        $array_data_base[$control_item]['dpto']=trim(validar_input($columna_e));//COD DEPARTAMENTO
                        $array_data_base[$control_item]['municipio']=trim(validar_input($columna_f));//COD MUNICIPIO

                        $control_item++;
                    }

                    $consulta_duplicado="SELECT COUNT(`usu_id`) FROM `administrador_usuario` WHERE `usu_id`=?";
                    $consulta_registros_duplicados = $enlace_db->prepare($consulta_duplicado);
                    $consulta_registros_duplicados->bind_param("s", $documento_identidad);
                    
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `administrador_usuario`(`usu_id`, `usu_acceso`, `usu_contrasena`, `usu_nombres_apellidos`, `usu_correo_corporativo`, `usu_fecha_incorporacion`, `usu_campania`, `usu_usuario_red`, `usu_cargo_rol`, `usu_sede`, `usu_ciudad`, `usu_estado`, `usu_supervisor`, `usu_lider_calidad`, `usu_inicio_sesion`, `usu_piloto`, `usu_fecha_ingreso_piloto`, `usu_foto`, `usu_genero`, `usu_fecha_nacimiento`, `usu_modificacion_usuario`, `usu_modificacion_fecha`, `usu_ultimo_acceso`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('sssssssssssssssssssssss', $documento_identidad, $usuario_acceso, $contrasena, $nombres_apellidos, $correo_corporativo, $fecha_ingreso, $campania, $usuario_red, $cargo_rol, $ubicacion, $ciudad, $estado, $supervisor, $lider_calidad, $inicio_sesion, $piloto, $fecha_ingreso_area, $foto, $genero, $fecha_nacimiento, $usu_modificacion_usuario, $usu_modificacion_fecha, $usu_ultimo_acceso);


                    // Prepara la sentencia INSERT AGENTES Y DPTOS MUNICIPIOS
                    $sentencia_insert_ciudad = $enlace_db->prepare("INSERT INTO `gestion_ocr_agentes`(`ocra_id`, `ocra_usuario`, `ocra_tipo`, `ocra_codigo`) VALUES (?,?,?,?)");

                    // Agrega variables a sentencia preparada
                    $sentencia_insert_ciudad->bind_param('ssss', $ocra_id, $ocra_usuario, $ocra_tipo, $ocra_codigo);

                    $control_fail=0;
                    $string_fail="";
                    $control_insert=0;
                    $control_conteo=0;
                    $control_insert_dpto=0;
                    $control_errores_detalle=array();
                    for ($i=0; $i < count($array_data_base); $i++) { 
                        $documento_identidad=$array_data_base[$i]['id'];

                        $consulta_registros_duplicados->execute();
                        $resultado_registros_duplicados = $consulta_registros_duplicados->get_result()->fetch_all(MYSQLI_NUM);

                        if ($resultado_registros_duplicados[0][0]==0) {
                            $nueva_contrasena=generatePassword(10);
                            $salt = substr(base64_encode(openssl_random_pseudo_bytes('30')), 0, 22);
                            $salt = strtr($salt, array('+' => '.'));
                            $contrasena = crypt($nueva_contrasena, '$2y$10$' . $salt);

                            $nombres_apellidos=validar_input($array_data_base[$i]['nombres']);
                            $usuario_acceso=validar_input($array_data_base[$i]['usuario']);
                            $correo_corporativo=validar_input($array_data_base[$i]['correo']);
                            $fecha_ingreso=date('Y-m-d');
                            $fecha_ingreso_area=date('Y-m-d');
                            $fecha_nacimiento=date('Y-m-d');
                            $genero='';
                            $estado='Activo';
                            $usuario_red=$usuario_acceso;
                            $ciudad='';
                            $ubicacion='';
                            $campania='';
                            $cargo_rol='AGENTE INSCRIPCIÓN FA CONSULTA';
                            $supervisor='';
                            $piloto='';
                            $foto='';
                            $lider_calidad='';
                            $inicio_sesion=0;

                            $usu_modificacion_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
                            $usu_modificacion_fecha=date('Y-m-d H:i:s');
                            $usu_ultimo_acceso=date('Y-m-d H:i:s');

                            $departamento=validar_input($array_data_base[$i]['dpto']);
                            $municipio=validar_input($array_data_base[$i]['municipio']);

                            if ($sentencia_insert->execute()) {
                                $control_insert++;

                                //INSERTA CONFIGURACIÓN AGENTES DPTO MUNICIPIO
                                    if ($departamento!="") {
                                        $ocra_id=$documento_identidad.$departamento;
                                        $ocra_usuario=$documento_identidad;
                                        $ocra_tipo='Departamento';
                                        $ocra_codigo=$departamento;
                                        if ($sentencia_insert_ciudad->execute()) {
                                            $control_insert_dpto++;
                                        }
                                    }

                                    if ($municipio!="") {
                                        $ocra_id=$documento_identidad.$municipio;
                                        $ocra_usuario=$documento_identidad;
                                        $ocra_tipo='Municipio';
                                        $ocra_codigo=$municipio;
                                        if ($sentencia_insert_ciudad->execute()) {
                                            // $control_insert++;
                                        }
                                    }

                                //Configura módulos de consulta FA
                                    // Prepara la sentencia
                                    $sentencia_insert_permisos = $enlace_db->prepare("INSERT INTO `administrador_usuario_modulo_perfil`(`per_id`, `per_usuario`, `per_modulo`, `per_perfil`) VALUES (?,?,?,?)");
                                    // Agrega variables a sentencia preparada
                                    $sentencia_insert_permisos->bind_param('ssss', $key_registro, $documento_identidad, $id_modulo, $per_perfil);
                                    
                                    $contador_insert_permisos=0;
                                    $id_modulo='10';
                                    $per_perfil='Usuario';
                                    $key_registro=$documento_identidad.$id_modulo;
                                    if ($sentencia_insert_permisos->execute()) {
                                      $contador_insert_permisos++;
                                    }

                                    $id_modulo='12';
                                    $key_registro=$documento_identidad.$id_modulo;

                                    if ($sentencia_insert_permisos->execute()) {
                                      $contador_insert_permisos++;
                                    }

                                    if ($control_insert_dpto>0) {
                                        $id_modulo='18';
                                        $key_registro=$documento_identidad.$id_modulo;

                                        if ($sentencia_insert_permisos->execute()) {
                                          $contador_insert_permisos++;
                                        }
                                    }

                                registro_log($enlace_db, $modulo_plataforma, 'crear', 'Creación de usuario '.$documento_identidad.'-'.$nombres_apellidos);

                                // Prepara la sentencia
                                $sentencia_insert_contrasena = $enlace_db->prepare("INSERT INTO `administrador_usuario_contrasenas`(`auc_usuario`, `auc_contrasena`) VALUES (?,?)");
                                // Agrega variables a sentencia preparada
                                $sentencia_insert_contrasena->bind_param('ss', $documento_identidad, $contrasena);
                                $sentencia_insert_contrasena->execute();


                                //PROGRAMACIÓN NOTIFICACIÓN
                                $asunto='Credenciales de acceso - '.APP_NAME.' | '.APP_NAME_ALL;
                                $referencia='Credenciales de Acceso';
                                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>¡Hemos generado las siguientes credenciales de acceso!</p>
                                      <center>
                                          <p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'><b>Nombres y Apellidos: ".$nombres_apellidos."</b></p>
                                          <p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'><b>Usuario: ".$usuario_acceso."</b></p>
                                          <p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'><b>Contraseña: ".$nueva_contrasena."</b></p>
                                      </center>";
                                $nc_address=$correo_corporativo.";";
                                $nc_cc='';
                                notificacion($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
                                registro_log($enlace_db, $modulo_plataforma, 'notificacion', 'Notificación de credenciales para usuario '.$documento_identidad.'-'.$nombres_apellidos.' programada');
                            } else {
                                $control_fail++;
                                $control_errores_detalle[]='Problemas al crear el registro: '.$array_data_base[$i]['id'];
                            }
                        } else {
                            $control_fail++;
                            $control_errores_detalle[]='Usuario duplicado: '.$array_data_base[$i]['id'];
                        }
                    }

                    if (($control_insert+$control_fail)==count($array_data_base)) {
                        $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente');";
                        $_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_fa']=1;
                    } else {
                        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar la base, por favor intente nuevamente');";
                    }
                } else {
                  $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar la base, por favor intente nuevamente');";
                }
            } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar la base, por favor intente nuevamente');";
            }
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
            <div class="col-lg-6 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                            <?php if(isset($control_errores_detalle)): ?>
                                <p class="alert alert-danger p-1"><b>No es posible crear algunos usuarios, por favor verifique los siguientes errores:</b><br>
                                    <ol>
                                        <?php for ($i=0; $i < count($control_errores_detalle); $i++): ?>
                                        <li class="alert alert-warning p-1 font-size-11 my-0"><?php echo $control_errores_detalle[$i]; ?></li>
                                        <?php endfor; ?>
                                    </ol>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="documento" class="my-0">Base de usuarios</label>
                            <input class="form-control form-control-sm custom-file-input" name="documento" id="documento" type="file" accept=".xlsx, .XLSX" <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_fa']==1) { echo 'disabled'; } ?> required>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_fa']==1): ?>
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
</body>
</html>