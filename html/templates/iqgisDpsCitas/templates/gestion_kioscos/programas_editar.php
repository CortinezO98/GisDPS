<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión Kioscos";
  $subtitle = "Programas | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="programas?pagina=".$pagina."&id=".$filtro_permanente;

  if(isset($_POST["guardar_registro"])){
    $estado=validar_input($_POST['estado']);
    $programa=validar_input($_POST['programa']);
    
    if ($_FILES['documento']['name']!="") {
        $codigo_documento=generar_codigo(10);
        $archivo_extension = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
        $ruta_actual="../kiosco/images/";
        $ruta_final=$ruta_actual.$codigo_documento.".".$archivo_extension;
        if ($_FILES['documento']["error"] > 0) {
            $control_actualizar_documento=0;
        } else {
            $ruta_eliminar=$resultado_registros_revisar[0][6];
            /*ahora co la funcion move_uploaded_file lo guardaremos en el destino que queramos*/
            if (move_uploaded_file($_FILES['documento']['tmp_name'], $ruta_final)) {
                $control_actualizar_documento=1;

                // Prepara la sentencia
                $consulta_actualizar_documento = $enlace_db->prepare("UPDATE `gestion_kioscos_programas` SET `gkp_imagen`=? WHERE `gkp_id`=?");
                // Agrega variables a sentencia preparada
                $consulta_actualizar_documento->bind_param("ss", $ruta_final, $id_registro);
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
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_kioscos_programas` SET `gkp_titulo`=?,`gkp_estado`=? WHERE `gkp_id`=?");

    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sss', $programa, $estado, $id_registro);
    
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

    $consulta_string="SELECT `gkp_id`, `gkp_titulo`, `gkp_imagen`, `gkp_estado`, `gkp_registro_usuario`, `gkp_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_kioscos_programas` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_programas`.`gkp_registro_usuario`=TU.`usu_id` WHERE `gkp_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
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
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label for="estado" class="my-0">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado"  required>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][3]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][3]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="programa" class="my-0">Programa</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="programa" id="programa" maxlength="100" value="<?php echo $resultado_registros[0][1]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="documento" class="my-0">Imagen</label>
                                <input class="form-control form-control-sm custom-file-input" name="documento" id="inputGroupFile01" type="file" accept=".png, .PNG, .jpg, .JPG, .jpeg, .JPEG">
                                <p class="alert alert-danger p-1">*Al cargar una nueva imagen, reemplazará la anterior.</p>
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
  <script type="text/javascript">
      $("#inputGroupFile01").change(function(){
          var valor_opcion = document.getElementById("inputGroupFile01").files[0].name;

          if (valor_opcion!="") {
              document.getElementById('inputGroupFile01').innerHTML=valor_opcion.substring(0, 25)+"...";
              $("#inputGroupFile01").addClass("color-verde");
          }
      });
  </script>
</body>
</html>