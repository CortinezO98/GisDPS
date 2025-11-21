<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Reparto";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Reparto | 14. Seguimiento Inspección Tipificación | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $parametros_add='';
  $url_salir="reparto_seguimiento_inspeccion_tipificacion?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  
  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='seguimiento_inspeccion_tipificacion' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
    $array_parametros[$resultado_registros_parametros[$i][2]]['id'][]=$resultado_registros_parametros[$i][0];
    $array_parametros[$resultado_registros_parametros[$i][2]]['valor'][]=$resultado_registros_parametros[$i][3];
    $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_campania` FROM `administrador_usuario` LEFT JOIN `administrador_campania` AS TC ON `administrador_usuario`.`usu_campania`=TC.`ac_id` WHERE `usu_estado`='Activo' AND TC.`ac_nombre_campania`='Canal Escrito' ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_analistas); $i++) { 
    $array_analista[$resultado_registros_analistas[$i][0]]=$resultado_registros_analistas[$i][1];
  }
  
  if(isset($_POST["guardar_registro"])){
      $cesit_radicado=validar_input($_POST['cesit_radicado']);
      $cesit_abogado_tipificador=validar_input($_POST['cesit_abogado_tipificador']);
      $cesit_abogado_aprobador=validar_input($_POST['cesit_abogado_aprobador']);
      $cesit_traslado_entidades=validar_input($_POST['cesit_traslado_entidades']);
      $cesit_traslado_entidades_errado=validar_input($_POST['cesit_traslado_entidades_errado']);
      $cesit_asignaciones_internas=validar_input($_POST['cesit_asignaciones_internas']);
      $cesit_forma_correcta_peticion=validar_input($_POST['cesit_forma_correcta_peticion']);
      $cesit_traslado_entidades_errado_senalar=validar_input($_POST['cesit_traslado_entidades_errado_senalar']);
      $cesit_asignacion_errada=validar_input($_POST['cesit_asignacion_errada']);
      $cesit_observaciones_asignacion='';
      $cesit_relaciona_informacion_radicacion=validar_input($_POST['cesit_relaciona_informacion_radicacion']);
      $cesit_diligencia_datos_solicitante=validar_input($_POST['cesit_diligencia_datos_solicitante']);
      $cesit_observaciones_diligencia_formulario=validar_input($_POST['cesit_observaciones_diligencia_formulario']);
      $cesit_notificar=validar_input($_POST['notificar']);
      $cesit_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      

      if (isset($_POST['cesit_asignacion_errada_2'])) {
        $cesit_asignacion_errada_2=$_POST['cesit_asignacion_errada_2'];
      } else {
        $cesit_asignacion_errada_2=array();
      }

      $cesit_asignacion_errada_2_insert=implode(';', $cesit_asignacion_errada_2);

      $cesit_asignacion_errada_2_correo='';
      for ($i=0; $i < count($cesit_asignacion_errada_2); $i++) { 
        $cesit_asignacion_errada_2_correo.=$array_parametros['asignacion_errada_2']['texto'][$cesit_asignacion_errada_2[$i]].'<br>';
      }

      if (isset($_POST['cesit_campo_errado'])) {
        $cesit_campo_errado=$_POST['cesit_campo_errado'];
      } else {
        $cesit_campo_errado=array();
      }

      $cesit_campo_errado_insert=implode(';', $cesit_campo_errado);

      $cesit_campo_errado_correo='';
      for ($i=0; $i < count($cesit_campo_errado); $i++) { 
        $cesit_campo_errado_correo.=$array_parametros['campo_errado']['texto'][$cesit_campo_errado[$i]].'<br>';
      }

      if (isset($_POST['cesit_campo_errado_2'])) {
        $cesit_campo_errado_2=$_POST['cesit_campo_errado_2'];
      } else {
        $cesit_campo_errado_2=array();
      }

      $cesit_campo_errado_2_insert=implode(';', $cesit_campo_errado_2);

      $cesit_campo_errado_2_correo='';
      for ($i=0; $i < count($cesit_campo_errado_2); $i++) { 
        $cesit_campo_errado_2_correo.=$array_parametros['campo_errado_2']['texto'][$cesit_campo_errado_2[$i]].'<br>';
      }


      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_cerep_seguimiento_inspeccion_tipificacion`(`cesit_radicado`, `cesit_abogado_tipificador`, `cesit_abogado_aprobador`, `cesit_traslado_entidades`, `cesit_traslado_entidades_errado`, `cesit_asignaciones_internas`, `cesit_forma_correcta_peticion`, `cesit_traslado_entidades_errado_senalar`, `cesit_asignacion_errada`, `cesit_asignacion_errada_2`, `cesit_observaciones_asignacion`, `cesit_relaciona_informacion_radicacion`, `cesit_campo_errado`, `cesit_diligencia_datos_solicitante`, `cesit_campo_errado_2`, `cesit_observaciones_diligencia_formulario`, `cesit_notificar`, `cesit_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('ssssssssssssssssss', $cesit_radicado, $cesit_abogado_tipificador, $cesit_abogado_aprobador, $cesit_traslado_entidades, $cesit_traslado_entidades_errado, $cesit_asignaciones_internas, $cesit_forma_correcta_peticion, $cesit_traslado_entidades_errado_senalar, $cesit_asignacion_errada, $cesit_asignacion_errada_2_insert, $cesit_observaciones_asignacion, $cesit_relaciona_informacion_radicacion, $cesit_campo_errado_insert, $cesit_diligencia_datos_solicitante, $cesit_campo_errado_2_insert, $cesit_observaciones_diligencia_formulario, $cesit_notificar, $cesit_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito']=1;

              if ($cesit_notificar=='Si') {
                //PROGRAMACIÓN NOTIFICACIÓN
                $asunto='14. Seguimiento Inspección Tipificación - Reparto | Canal Escrito';
                $referencia='14. Seguimiento Inspección Tipificación - Reparto | Canal Escrito';
                $contenido="<p style='font-size: 12px;padding: 0px 5px 0px 5px; color: #666666;'>Cordial saludo,<br><br>Se ha generado el siguiente registro:</p>
                  <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>No. radicado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cesit_radicado."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Abogado tipificador</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_analista[$cesit_abogado_tipificador]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>Abogado aprobador</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_analista[$cesit_abogado_aprobador]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>6. Traslados a otras entidades</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['traslado_entidades']['texto'][$cesit_traslado_entidades]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>7. Traslado errado entidades</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['traslado_entidades_errado']['texto'][$cesit_traslado_entidades_errado]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>7.1. Traslado errado entidades (Señalar la entidad)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cesit_traslado_entidades_errado_senalar."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>8. Asignaciones internas P.S</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['asignaciones_internas']['texto'][$cesit_asignaciones_internas]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>8.1. Asignación P.S errada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['asignacion_errada']['texto'][$cesit_asignacion_errada]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>8.2. Asignación P.S errada</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cesit_asignacion_errada_2_correo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>9. Determina de forma correcta el tipo de petición </td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['forma_correcta_peticion']['texto'][$cesit_forma_correcta_peticion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>14. Relaciona de manera correcta los datos del campo información radicación</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['relaciona_informacion_radicacion']['texto'][$cesit_relaciona_informacion_radicacion]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>15. Campo errado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cesit_campo_errado_2_correo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>16. Diligencia de manera correcta los datos del solicitante</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$array_parametros['diligencia_datos_solicitante']['texto'][$cesit_diligencia_datos_solicitante]."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>17. Campo errado</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cesit_campo_errado_2_correo."</td>
                    </tr>
                    <tr>
                      <td style='width: 30%;background-color: #1C2262; color: #FFFFFF; padding: 5px 5px 5px 5px; text-align: center;'>18. Observación (Aportes o recomendaciones evidenciados en el diligenciamiento del formulario de tipificación que permita adelantar los PDA)</td>
                      <td style='width: 70%;padding: 5px 5px 5px 5px;background-color: #F2F2F2;'>".$cesit_observaciones_diligencia_formulario."</td>
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
                            <label for="cesit_radicado" class="my-0">No. radicado</label>
                            <input type="text" class="form-control form-control-sm" name="cesit_radicado" id="cesit_radicado" minlength="18" maxlength="18" value="<?php if(isset($_POST["guardar_registro"])){ echo $cesit_radicado; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cesit_abogado_tipificador" class="my-0">Abogado tipificador</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cesit_abogado_tipificador" id="cesit_abogado_tipificador" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($cesit_abogado_tipificador==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cesit_abogado_aprobador" class="my-0">Abogado aprobador</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="cesit_abogado_aprobador" id="cesit_abogado_aprobador" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($cesit_abogado_aprobador==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cesit_traslado_entidades" class="my-0">6. Traslados a otras entidades</label>
                              <select class="form-control form-control-sm form-select" name="cesit_traslado_entidades" id="cesit_traslado_entidades" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_traslado_entidades();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['traslado_entidades']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['traslado_entidades']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['traslado_entidades']['id'][$i]; ?>" <?php if($cesit_traslado_entidades==$array_parametros['traslado_entidades']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['traslado_entidades']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cesit_traslado_entidades_errado">
                          <div class="form-group my-1">
                              <label for="cesit_traslado_entidades_errado" class="my-0">7. Traslado errado entidades</label>
                              <select class="form-control form-control-sm form-select" name="cesit_traslado_entidades_errado" id="cesit_traslado_entidades_errado" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_traslado_error_entidades();" required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['traslado_entidades_errado']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['traslado_entidades_errado']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['traslado_entidades_errado']['id'][$i]; ?>" <?php if($cesit_traslado_entidades_errado==$array_parametros['traslado_entidades_errado']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['traslado_entidades_errado']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cesit_traslado_entidades_errado_senalar">
                          <div class="form-group my-1">
                              <label for="cesit_traslado_entidades_errado_senalar" class="my-0">7.1. Traslado errado entidades (Señalar la entidad)</label>
                              <input type="text" class="form-control form-control-sm" name="cesit_traslado_entidades_errado_senalar" id="cesit_traslado_entidades_errado_senalar" maxlength="500" value="<?php if(isset($_POST["guardar_registro"])){ echo $cesit_traslado_entidades_errado_senalar; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?> autocomplete="off" required disabled>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cesit_asignaciones_internas" class="my-0">8. Asignaciones internas P.S</label>
                              <select class="form-control form-control-sm form-select" name="cesit_asignaciones_internas" id="cesit_asignaciones_internas" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_asignaciones_internas();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['asignaciones_internas']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['asignaciones_internas']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['asignaciones_internas']['id'][$i]; ?>" <?php if($cesit_asignaciones_internas==$array_parametros['asignaciones_internas']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['asignaciones_internas']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cesit_asignacion_errada">
                          <div class="form-group my-1">
                              <label for="cesit_asignacion_errada" class="my-0">8.1. Asignación P.S errada</label>
                              <select class="form-control form-control-sm form-select" name="cesit_asignacion_errada" id="cesit_asignacion_errada" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_asignacion_errada();" required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['asignacion_errada']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['asignacion_errada']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['asignacion_errada']['id'][$i]; ?>" <?php if($cesit_asignacion_errada==$array_parametros['asignacion_errada']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['asignacion_errada']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cesit_asignacion_errada_2">
                          <div class="form-group my-1">
                              <label for="cesit_asignacion_errada_2" class="my-0">8.2. Asignación P.S errada</label>
                              <select class="form-control form-control-sm form-select" title="Seleccione" data-live-search="false" data-container="body" name="cesit_asignacion_errada_2[]" id="cesit_asignacion_errada_2" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> disabled required multiple>
                                  <?php if(isset($array_parametros['asignacion_errada_2']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['asignacion_errada_2']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['asignacion_errada_2']['id'][$i]; ?>" <?php if($cesit_asignacion_errada_2==$array_parametros['asignacion_errada_2']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['asignacion_errada_2']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cesit_forma_correcta_peticion" class="my-0">9. Determina de forma correcta el tipo de petición</label>
                              <select class="form-control form-control-sm form-select" name="cesit_forma_correcta_peticion" id="cesit_forma_correcta_peticion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['forma_correcta_peticion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['forma_correcta_peticion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['forma_correcta_peticion']['id'][$i]; ?>" <?php if($cesit_forma_correcta_peticion==$array_parametros['forma_correcta_peticion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['forma_correcta_peticion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cesit_relaciona_informacion_radicacion" class="my-0">14. Relaciona de manera correcta los datos del campo "información radicación"</label>
                              <select class="form-control form-control-sm form-select" name="cesit_relaciona_informacion_radicacion" id="cesit_relaciona_informacion_radicacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_relaciona_informacion_radicacion();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['relaciona_informacion_radicacion']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['relaciona_informacion_radicacion']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['relaciona_informacion_radicacion']['id'][$i]; ?>" <?php if($cesit_relaciona_informacion_radicacion==$array_parametros['relaciona_informacion_radicacion']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['relaciona_informacion_radicacion']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cesit_campo_errado">
                          <div class="form-group my-1">
                              <label for="cesit_campo_errado" class="my-0">15. Campo errado</label>
                              <select class="form-control form-control-sm form-select" title="Seleccione" data-live-search="false" data-container="body" name="cesit_campo_errado[]" id="cesit_campo_errado" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> disabled required multiple>
                                  <?php if(isset($array_parametros['campo_errado']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['campo_errado']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['campo_errado']['id'][$i]; ?>" <?php if($cesit_campo_errado==$array_parametros['campo_errado']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['campo_errado']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="cesit_diligencia_datos_solicitante" class="my-0">16. Diligencia de manera correcta los datos del solicitante</label>
                              <select class="form-control form-control-sm form-select" name="cesit_diligencia_datos_solicitante" id="cesit_diligencia_datos_solicitante" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> onchange="validar_diligencia_datos_solicitante();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php if(isset($array_parametros['diligencia_datos_solicitante']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['diligencia_datos_solicitante']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['diligencia_datos_solicitante']['id'][$i]; ?>" <?php if($cesit_diligencia_datos_solicitante==$array_parametros['diligencia_datos_solicitante']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['diligencia_datos_solicitante']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="div_cesit_campo_errado_2">
                          <div class="form-group my-1">
                              <label for="cesit_campo_errado_2" class="my-0">17. Campo errado</label>
                              <select class="form-control form-control-sm form-select" title="Seleccione" data-live-search="false" data-container="body" name="cesit_campo_errado_2[]" id="cesit_campo_errado_2" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'disabled'; } ?> disabled required multiple>
                                  <?php if(isset($array_parametros['campo_errado_2']['id'])): ?>
                                  <?php for ($i=0; $i < count($array_parametros['campo_errado_2']['id']); $i++): ?>
                                    <option value="<?php echo $array_parametros['campo_errado_2']['id'][$i]; ?>" <?php if($cesit_campo_errado_2==$array_parametros['campo_errado_2']['id'][$i]){ echo "selected"; } ?>><?php echo $array_parametros['campo_errado_2']['valor'][$i]; ?></option>
                                  <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cesit_observaciones_diligencia_formulario" class="my-0">18. Observación (Aportes o recomendaciones evidenciados en el diligenciamiento del formulario de tipificación que permita adelantar los PDA)</label>
                            <textarea class="form-control form-control-sm height-100" name="cesit_observaciones_diligencia_formulario" id="cesit_observaciones_diligencia_formulario" <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $cesit_observaciones_diligencia_formulario; } ?></textarea>
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
    function validar_traslado_entidades(){
      var cesit_traslado_entidades_opcion = document.getElementById("cesit_traslado_entidades");
      var cesit_traslado_entidades = cesit_traslado_entidades_opcion.options[cesit_traslado_entidades_opcion.selectedIndex].text;

      $("#div_cesit_traslado_entidades_errado").removeClass('d-block').addClass('d-none');
      $("#div_cesit_traslado_entidades_errado_senalar").removeClass('d-block').addClass('d-none');
      
      document.getElementById('cesit_traslado_entidades_errado').disabled=true;
      document.getElementById('cesit_traslado_entidades_errado_senalar').disabled=true;

      if(cesit_traslado_entidades=="Si") {
          $("#div_cesit_traslado_entidades_errado").removeClass('d-none').addClass('d-block');
          document.getElementById('cesit_traslado_entidades_errado').disabled=false;
      }
    }

    function validar_traslado_error_entidades(){
      var cesit_traslado_entidades_errado_opcion = document.getElementById("cesit_traslado_entidades_errado");
      var cesit_traslado_entidades_errado = cesit_traslado_entidades_errado_opcion.options[cesit_traslado_entidades_errado_opcion.selectedIndex].text;

      $("#div_cesit_traslado_entidades_errado_senalar").removeClass('d-block').addClass('d-none');
      
      document.getElementById('cesit_traslado_entidades_errado_senalar').disabled=true;
      
      if(cesit_traslado_entidades_errado=="Excede" || cesit_traslado_entidades_errado=="Omite") {
          $("#div_cesit_traslado_entidades_errado_senalar").removeClass('d-none').addClass('d-block');
          document.getElementById('cesit_traslado_entidades_errado_senalar').disabled=false;
      }
    }

    function validar_asignaciones_internas(){
      var cesit_asignaciones_internas_opcion = document.getElementById("cesit_asignaciones_internas");
      var cesit_asignaciones_internas = cesit_asignaciones_internas_opcion.options[cesit_asignaciones_internas_opcion.selectedIndex].text;
      
      $("#div_cesit_asignacion_errada").removeClass('d-block').addClass('d-none');
      $("#div_cesit_asignacion_errada_2").removeClass('d-block').addClass('d-none');

      document.getElementById('cesit_asignacion_errada').disabled=true;
      document.getElementById('cesit_asignacion_errada_2').disabled=true;
      
      if(cesit_asignaciones_internas=="Si") {
          $("#div_cesit_asignacion_errada").removeClass('d-none').addClass('d-block');
          document.getElementById('cesit_asignacion_errada').disabled=false;
      }
    }

    function validar_asignacion_errada(){
      var cesit_asignacion_errada_opcion = document.getElementById("cesit_asignacion_errada");
      var cesit_asignacion_errada = cesit_asignacion_errada_opcion.options[cesit_asignacion_errada_opcion.selectedIndex].text;
      
      $("#div_cesit_asignacion_errada_2").removeClass('d-block').addClass('d-none');
      
      document.getElementById('cesit_asignacion_errada_2').disabled=true;
      
      if(cesit_asignacion_errada=="Excede" || cesit_asignacion_errada=="Omite") {
          $("#div_cesit_asignacion_errada_2").removeClass('d-none').addClass('d-block');
          document.getElementById('cesit_asignacion_errada_2').disabled=false;
          $('#cesit_asignacion_errada_2').selectpicker();
      }
    }

    // function validar_forma_correcta_peticion(){
    //   var cesit_forma_correcta_peticion_opcion = document.getElementById("cesit_forma_correcta_peticion");
    //   var cesit_forma_correcta_peticion = cesit_forma_correcta_peticion_opcion.options[cesit_forma_correcta_peticion_opcion.selectedIndex].text;
      
    //   $("#div_cesit_traslado_entidades_errado_senalar").removeClass('d-block').addClass('d-none');
    //   $("#div_cesit_asignacion_errada").removeClass('d-block').addClass('d-none');
    //   $("#div_cesit_asignacion_errada_2").removeClass('d-block').addClass('d-none');

    //   $("#div_cesit_relaciona_informacion_radicacion").removeClass('d-block').addClass('d-none');
    //   $("#div_cesit_campo_errado").removeClass('d-block').addClass('d-none');
    //   $("#div_cesit_diligencia_datos_solicitante").removeClass('d-block').addClass('d-none');
    //   $("#div_cesit_campo_errado_2").removeClass('d-block').addClass('d-none');
      
    //   document.getElementById('cesit_traslado_entidades_errado_senalar').disabled=true;
    //   document.getElementById('cesit_asignacion_errada').disabled=true;
    //   document.getElementById('cesit_asignacion_errada_2').disabled=true;
      
    //   document.getElementById('cesit_relaciona_informacion_radicacion').disabled=true;
    //   document.getElementById('cesit_campo_errado').disabled=true;
    //   document.getElementById('cesit_diligencia_datos_solicitante').disabled=true;
    //   document.getElementById('cesit_campo_errado_2').disabled=true;

    //   if(cesit_forma_correcta_peticion=="Si" || cesit_forma_correcta_peticion=="No") {
    //       $("#div_cesit_relaciona_informacion_radicacion").removeClass('d-none').addClass('d-block');
    //       document.getElementById('cesit_relaciona_informacion_radicacion').disabled=false;
    //   }
    // }

    function validar_relaciona_informacion_radicacion(){//Validación campo 14 Relaciona de manera correcta los datos del campo "información radicación"
      var cesit_relaciona_informacion_radicacion_opcion = document.getElementById("cesit_relaciona_informacion_radicacion");
      var cesit_relaciona_informacion_radicacion = cesit_relaciona_informacion_radicacion_opcion.options[cesit_relaciona_informacion_radicacion_opcion.selectedIndex].text;
      
      $("#div_cesit_campo_errado").removeClass('d-block').addClass('d-none');
      
      document.getElementById('cesit_campo_errado').disabled=true;
      
      if(cesit_relaciona_informacion_radicacion=="No") {
          $("#div_cesit_campo_errado").removeClass('d-none').addClass('d-block');
          document.getElementById('cesit_campo_errado').disabled=false;
          $('#cesit_campo_errado').selectpicker();
      }
    }

    function validar_diligencia_datos_solicitante(){
      var cesit_diligencia_datos_solicitante_opcion = document.getElementById("cesit_diligencia_datos_solicitante");
      var cesit_diligencia_datos_solicitante = cesit_diligencia_datos_solicitante_opcion.options[cesit_diligencia_datos_solicitante_opcion.selectedIndex].text;

      $("#div_cesit_campo_errado_2").removeClass('d-block').addClass('d-none');
      
      document.getElementById('cesit_campo_errado_2').disabled=true;
      
      if(cesit_diligencia_datos_solicitante=="No") {
          $("#div_cesit_campo_errado_2").removeClass('d-none').addClass('d-block');
          document.getElementById('cesit_campo_errado_2').disabled=false;
          $('#cesit_campo_errado_2').selectpicker();
      }
    }

    validar_traslado_entidades();
    validar_traslado_error_entidades();
    validar_asignaciones_internas();
    validar_asignacion_errada();
    // validar_forma_correcta_peticion();
    validar_relaciona_informacion_radicacion();
    validar_diligencia_datos_solicitante();
  </script>
</body>
</html>