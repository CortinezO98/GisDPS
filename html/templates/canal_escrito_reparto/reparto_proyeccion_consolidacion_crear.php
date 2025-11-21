<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Reparto";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Reparto | 1. Proyección Consolidación | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="reparto_proyeccion_consolidacion?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja); 
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='proyeccion_consolidacion' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }

  if(isset($_POST["guardar_registro"])){
      $cepc_radicado_entrada=validar_input($_POST['cepc_radicado_entrada']);
      $cepc_tipologia=validar_input($_POST['cepc_tipologia']);
      $cepc_grupo_responsable=validar_input($_POST['cepc_grupo_responsable']);
      $cepc_grupo_prorrogas=validar_input($_POST['cepc_grupo_prorrogas']);
      $cepc_notificar=validar_input($_POST['notificar']);
      $cepc_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cerep_proyeccion_consolidacion`(`cepc_radicado_entrada`, `cepc_tipologia`, `cepc_grupo_responsable`, `cepc_grupo_prorrogas`, `cepc_notificar`, `cepc_registro_usuario`) VALUES (?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssss', $cepc_radicado_entrada, $cepc_tipologia, $cepc_grupo_responsable, $cepc_grupo_prorrogas, $cepc_notificar, $cepc_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cepc_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='1. Proyección Consolidación - Reparto | Canal Escrito';
                $referencia='1. Proyección Consolidación - Reparto | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Radicado entrada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cepc_radicado_entrada."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Tipología</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['tipologia']['texto'][$cepc_tipologia]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Grupo responsable</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['grupo_responsable']['texto'][$cepc_grupo_responsable]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Grupo prórrogas</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['grupo_prorrogas']['texto'][$cepc_grupo_prorrogas]."</td>
                    </tr>
                    
                    <tr>
                        <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Registrado por</td>
                        <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$_SESSION[APP_SESSION.'_session_usu_nombre_completo']."</td>
                    </tr>
                    <tr>
                        <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Fecha registro</td>
                        <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>". date('d/m/Y H:i:s') ."</td>
                    </tr>
                  </table>";
                $nc_address=$_SESSION[APP_SESSION.'_session_usu_correo'].";";
                $nc_cc='';
                notificacion($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
              }
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
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
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="cepc_radicado_entrada" class="my-0">Radicado entrada</label>
                            <input type="text" class="form-control form-control-sm" name="cepc_radicado_entrada" id="cepc_radicado_entrada" minlength="18" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cepc_radicado_entrada; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cepc_tipologia" class="my-0">Tipología</label>
                              <select class="form-control form-control-sm form-select" name="cepc_tipologia" id="cepc_tipologia" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_tipologia();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($array_parametros['tipologia']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['tipologia']['id'][$i]; ?>" <?php if($cepc_tipologia==$array_parametros['tipologia']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['tipologia']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cepc_grupo_responsable">
                          <div class="form-group my-1">
                              <label for="cepc_grupo_responsable" class="my-0">Grupo responsable</label>
                              <select class="form-control form-control-sm form-select" name="cepc_grupo_responsable" id="cepc_grupo_responsable" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($array_parametros['grupo_responsable']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['grupo_responsable']['id'][$i]; ?>" <?php if($cepc_grupo_responsable==$array_parametros['grupo_responsable']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['grupo_responsable']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cepc_grupo_prorrogas">
                          <div class="form-group my-1">
                              <label for="cepc_grupo_prorrogas" class="my-0">Grupo prórrogas</label>
                              <select class="form-control form-control-sm form-select" name="cepc_grupo_prorrogas" id="cepc_grupo_prorrogas" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($array_parametros['grupo_prorrogas']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['grupo_prorrogas']['id'][$i]; ?>" <?php if($cepc_grupo_prorrogas==$array_parametros['grupo_prorrogas']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['grupo_prorrogas']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <div class="form-group custom-control custom-checkbox m-0">
                                  <input type="checkbox" class="custom-control-input" id="customCheckmail" name="notificar" value="Si" <?php if(isset($_POST["guardar_registro"]) AND $_POST["notificar"]=="Si"){ echo "checked"; } ?> <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?>>
                                  <label class="custom-control-label p-0 m-0" for="customCheckmail">Enviarme una confirmación por correo</label>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1): ?>
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
          </form>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
    function validar_tipologia(){
      var tipologia_opcion = document.getElementById("cepc_tipologia");
      var tipologia = tipologia_opcion.options[tipologia_opcion.selectedIndex].text;

      $("#div_cepc_grupo_responsable").removeClass('d-block').addClass('d-none');
      $("#div_cepc_grupo_prorrogas").removeClass('d-block').addClass('d-none');
      document.getElementById('cepc_grupo_responsable').disabled=true;
      document.getElementById('cepc_grupo_prorrogas').disabled=true;

      if(tipologia=="Prórroga") {
          $("#div_cepc_grupo_prorrogas").removeClass('d-none').addClass('d-block');
          document.getElementById('cepc_grupo_prorrogas').disabled=false;
      } else if(tipologia=="Consolidada") {
          $("#div_cepc_grupo_responsable").removeClass('d-none').addClass('d-block');
          document.getElementById('cepc_grupo_responsable').disabled=false;
      }
    }
    validar_tipologia();
  </script>
</body>
</html>