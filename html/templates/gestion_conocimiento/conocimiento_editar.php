<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Conocimiento";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Conocimiento";
  $subtitle = "Gestión Conocimiento | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_categoria=validar_input($_GET['cat']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="conocimiento?pagina=".$pagina."&id=".$filtro_permanente."&cat=".$id_categoria;

  if(isset($_POST["guardar_registro"])){
    $categoria=validar_input($_POST['categoria']);
    $nombre_documento=validar_input($_POST['nombre_documento']);
    $descripcion=validar_input($_POST['descripcion']);
    $version=validar_input($_POST['version']);

    $consulta_string_revisar="SELECT `gc_codigo`, `gc_categoria`, TC.`gcc_orden`, TC.`gcc_nombre_categoria`, `gc_nombre`, `gc_descripcion`, `gc_ruta`, `gc_extension`, `gc_version`, `gc_visitas`, `gc_registro_usuario`, TU.`usu_nombres_apellidos`, `gc_registro_fecha`, `gc_actualiza_fecha`, `gc_actualiza_usuario`, TUA.`usu_nombres_apellidos` FROM `gestion_conocimiento` LEFT JOIN `gestion_conocimiento_categoria` AS TC ON `gestion_conocimiento`.`gc_categoria`=TC.`gcc_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_conocimiento`.`gc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_conocimiento`.`gc_actualiza_usuario`=TUA.`usu_id` WHERE `gc_codigo`=?";

    $consulta_registros_revisar = $enlace_db->prepare($consulta_string_revisar);
    $consulta_registros_revisar->bind_param("s", $id_registro);
    $consulta_registros_revisar->execute();
    $resultado_registros_revisar = $consulta_registros_revisar->get_result()->fetch_all(MYSQLI_NUM);
    
    if ($_FILES['documento']['name']!="") {
        $codigo_documento=generar_codigo(10);
        $archivo_extension = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
        $ruta_actual="storage/";
        $ruta_final=$ruta_actual.$codigo_documento.".".$archivo_extension;
        if ($_FILES['documento']["error"] > 0) {
            $control_actualizar_documento=0;
        } else {
            $ruta_eliminar=$resultado_registros_revisar[0][6];
            /*ahora co la funcion move_uploaded_file lo guardaremos en el destino que queramos*/
            if (move_uploaded_file($_FILES['documento']['tmp_name'], $ruta_final)) {
                $control_actualizar_documento=1;

                // Prepara la sentencia
                $consulta_actualizar_documento = $enlace_db->prepare("UPDATE `gestion_conocimiento` SET `gc_ruta`=?, `gc_extension`=? WHERE `gc_codigo`=?");
                // Agrega variables a sentencia preparada
                $consulta_actualizar_documento->bind_param("sss", $ruta_final, $archivo_extension, $id_registro);
                $consulta_actualizar_documento->execute();
                if (comprobarSentencia($enlace_db->info)) {
                    $control_actualizar_documento=1;
                    unlink($ruta_eliminar);
                } else {
                    $control_actualizar_documento=0;
                }
            } else {
                $control_actualizar_documento=0;
            }
        }
    } else {
       $control_actualizar_documento=1;
    }

    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_conocimiento` SET `gc_categoria`=?,`gc_nombre`=?,`gc_descripcion`=?,`gc_version`=?,`gc_actualiza_usuario`=?,`gc_actualiza_fecha`=? WHERE `gc_codigo`=?");
    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param("sssssss", $categoria, $nombre_documento, $descripcion, $version, $_SESSION[APP_SESSION.'_session_usu_id'], date('Y-m-d H:i:s'), $id_registro);
    
    if ($control_actualizar_documento==1) {
        // Ejecuta sentencia preparada
        $consulta_actualizar->execute();

        // Evalua resultado de ejecución sentencia preparada
        if (comprobarSentencia($enlace_db->info)) {
            $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
        } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
        }
    } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

    $consulta_string="SELECT `gc_codigo`, `gc_categoria`, TC.`gcc_orden`, TC.`gcc_nombre_categoria`, `gc_nombre`, `gc_descripcion`, `gc_ruta`, `gc_extension`, `gc_version`, `gc_visitas`, `gc_registro_usuario`, TU.`usu_nombres_apellidos`, `gc_registro_fecha`, `gc_actualiza_fecha`, `gc_actualiza_usuario`, TUA.`usu_nombres_apellidos` FROM `gestion_conocimiento` LEFT JOIN `gestion_conocimiento_categoria` AS TC ON `gestion_conocimiento`.`gc_categoria`=TC.`gcc_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_conocimiento`.`gc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_conocimiento`.`gc_actualiza_usuario`=TUA.`usu_id` WHERE `gc_codigo`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_categoria="SELECT `gcc_id`, `gcc_orden`, `gcc_nombre_categoria`, `gcc_descripcion` FROM `gestion_conocimiento_categoria` WHERE 1=1 ORDER BY `gcc_orden`, `gcc_nombre_categoria`";
    $consulta_registros_categoria = $enlace_db->prepare($consulta_string_categoria);
    $consulta_registros_categoria->execute();
    $resultado_registros_categoria = $consulta_registros_categoria->get_result()->fetch_all(MYSQLI_NUM);
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
                            <div class="form-group">
                                <label for="categoria" class="m-0">Categoría</label>
                                <select class="form-control form-control-sm form-select" name="categoria" id="categoria" required>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_categoria); $i++): ?>
                                        <option value='<?php echo $resultado_registros_categoria[$i][0]; ?>' <?php if($resultado_registros[0][1]==$resultado_registros_categoria[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_categoria[$i][1].". ".$resultado_registros_categoria[$i][2]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="nombre_documento" class="m-0">Nombre documento</label>
                              <input type="text" class="form-control form-control-sm" name="nombre_documento" id="nombre_documento" maxlength="300" value="<?php echo $resultado_registros[0][4]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="descripcion" class="m-0">Descripción</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="descripcion" id="descripcion" maxlength="2000" required><?php echo $resultado_registros[0][5]; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="version" class="m-0">Versión</label>
                              <input type="number" class="form-control form-control-sm" name="version" id="version" maxlength="3" step="0.1" min="1" max="999" value="<?php echo $resultado_registros[0][8]; ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="documento" class="my-0">Adjuntar documento</label>
                                <input class="form-control form-control-sm custom-file-input" name="documento" id="inputGroupFile01" type="file">
                                <p class="alert alert-danger p-1">*Al cargar un nuevo archivo, reemplazará el anterior.</p>
                            </div>
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
</body>
</html>