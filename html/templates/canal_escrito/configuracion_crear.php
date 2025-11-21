<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Configuración";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $parametro=validar_input($_GET['par']);
  $title = "Canal Escrito";
  $subtitle = "Configuración | ".$parametro." | Crear Registro";
  $pagina=validar_input($_GET['pagina']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="configuracion?pagina=".$pagina."&id=".$filtro_permanente."&par=".$parametro;

  if(isset($_POST["guardar_registro"])){
      $ceco_formulario=$parametro;
      $ceco_campo=validar_input($_POST['ceco_campo']);
      $ceco_valor=validar_input($_POST['ceco_valor']);
      $ceco_estado=validar_input($_POST['ceco_estado']);
      $ceco_actualiza_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
      $ceco_actualiza_fecha='';
      $ceco_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];


      if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito_configuracion']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_configuracion`(`ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`) VALUES (?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssss', $ceco_formulario, $ceco_campo, $ceco_valor, $ceco_estado, $ceco_actualiza_usuario, $ceco_actualiza_fecha, $ceco_registro_usuario);
          // Prepara la sentencia
          
          if ($sentencia_insert->execute()) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente');";
              $_SESSION[APP_SESSION.'_registro_creado_canal_escrito_configuracion']=1;
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente');";
      }
  }

  $array_parametros['proyeccion_consolidacion'][]='tipologia';
  $array_parametros['proyeccion_consolidacion'][]='grupo_responsable';
  $array_parametros['proyeccion_consolidacion'][]='grupo_prorrogas';
  
  $array_parametros['aprobacion_firma_fa'][]='estado';
  
  $array_parametros['firma_fa'][]='modalidad_envio';
  
  $array_parametros['inspeccion_proyeccion'][]='estado';
  $array_parametros['inspeccion_proyeccion'][]='tipo_rechazo';
  
  $array_parametros['proyeccion_fa'][]='solicitud';
  
  $array_parametros['aprobacion_firma'][]='carta';
  $array_parametros['aprobacion_firma'][]='estado';
  $array_parametros['aprobacion_firma'][]='afectacion';
  
  $array_parametros['firma_traslados'][]='rechazos';
  $array_parametros['firma_traslados'][]='forma';
  
  $array_parametros['proyectores'][]='direccionamiento';
  $array_parametros['proyectores'][]='novedad_radicado';
  
  $array_parametros['lanzamientos_tr'][]='area';
  $array_parametros['lanzamientos_tr'][]='responsable_grupo';
  
  $array_parametros['seguimiento_envios_web'][]='tipo_envio';
  $array_parametros['seguimiento_envios_web'][]='estado';
  
  $array_parametros['seguimiento_cargue_documentos'][]='novedad';
  
  $array_parametros['seguimiento_radicacion'][]='dependencia';
  $array_parametros['seguimiento_radicacion'][]='senotifica';

  $array_parametros['seguimiento_tipificaciones'][]='direccionamiento';

  $array_parametros['seguimiento_inspeccion_tipificacion'][]='traslado_entidades';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='traslado_entidades_errado';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='asignaciones_internas';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='forma_correcta_peticion';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='traslado_entidades_errado_senalar';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='asignacion_errada';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='asignacion_errada_2';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='relaciona_informacion_radicacion';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='campo_errado';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='diligencia_datos_solicitante';
  $array_parametros['seguimiento_inspeccion_tipificacion'][]='campo_errado_2';
  
  //JAFOCALIZACION
  $array_parametros['jafocalizacion_proyeccion_peticiones'][]='cejpp_novedad_radicado';
  $array_parametros['jafocalizacion_proyeccion_peticiones'][]='cejpp_formato';

  $array_parametros['jafocalizacion_revision_peticiones'][]='cejrp_realiza_traslado';
  $array_parametros['jafocalizacion_revision_peticiones'][]='cejrp_estado';
  $array_parametros['jafocalizacion_revision_peticiones'][]='cejrp_error_digitalizacion';
  $array_parametros['jafocalizacion_revision_peticiones'][]='cejrp_caso_particular';

  $array_parametros['jafocalizacion_relacion_rae'][]='cejrr_modalidad_envio';
  $array_parametros['jafocalizacion_relacion_rae'][]='cejrr_srjv';
  $array_parametros['jafocalizacion_relacion_rae'][]='cejrr_firma';

  $array_parametros['jafocalizacion_gestion_correos'][]='gestion';
  $array_parametros['jafocalizacion_gestion_correos'][]='tipo_documento';
  $array_parametros['jafocalizacion_gestion_correos'][]='categoria';
  $array_parametros['jafocalizacion_gestion_correos'][]='gestion_2';
  $array_parametros['jafocalizacion_gestion_correos'][]='tipificacion';

  $array_parametros['jafocalizacion_gestion_novedades'][]='cejgn_estado';
  $array_parametros['jafocalizacion_gestion_novedades'][]='cejgn_tipo_rechazo';
  $array_parametros['jafocalizacion_gestion_novedades'][]='cejgn_correccion_datos_sija';

  $array_parametros['jafocalizacion_gestion_peticiones'][]='cejgp_solicitud';
  $array_parametros['jafocalizacion_gestion_peticiones'][]='cejgp_no_registra_sija';
  $array_parametros['jafocalizacion_gestion_peticiones'][]='cejgp_tipo_documento';
  $array_parametros['jafocalizacion_gestion_peticiones'][]='cejgp_novedad';
  $array_parametros['jafocalizacion_gestion_peticiones'][]='cejgp_novedad_adicional';
  $array_parametros['jafocalizacion_gestion_peticiones'][]='cejgp_gestion_actualizacion';
  $array_parametros['jafocalizacion_gestion_peticiones'][]='cejgp_institucion_estudia';
  $array_parametros['jafocalizacion_gestion_peticiones'][]='cejgp_nivel_formacion';

  $array_parametros['jafocalizacion_gestion_aprobacion'][]='cejga_gestion';
  $array_parametros['jafocalizacion_gestion_aprobacion'][]='cejga_oportunidad_mejora';

  $array_parametros['jafocalizacion_entrega_fisica'][]='';


  // TMNC
  $array_parametros['tmnc_sproyeccion_respuestas'][]='requiere_respuesta';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='abogado_aprobacion';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='programa_solicitud';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='plantilla';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='con_datos';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='datos_incompletos';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='plantilla_compensacion_iva';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='plantilla_adulto_mayor';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='plantilla_renta_ciudadana';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='novedad_radicado';
  $array_parametros['tmnc_sproyeccion_respuestas'][]='tipo_entidad';

  $array_parametros['tmnc_saprobacion_respuestas'][]='proyector';
  $array_parametros['tmnc_saprobacion_respuestas'][]='apoyo_prosperidad';
  $array_parametros['tmnc_saprobacion_respuestas'][]='ingreso_solidario';
  $array_parametros['tmnc_saprobacion_respuestas'][]='carta_respuesta';
  $array_parametros['tmnc_saprobacion_respuestas'][]='estado';

  $array_parametros['tmnc_sclasificacion'][]='programa_solicitud';
  $array_parametros['tmnc_sclasificacion'][]='plantilla_utilizada';
  $array_parametros['tmnc_sclasificacion'][]='plantilla_datos_incompletos';
  $array_parametros['tmnc_sclasificacion'][]='plantilla_datos_completos';
  $array_parametros['tmnc_sclasificacion'][]='situacion_plantilla_8';
  $array_parametros['tmnc_sclasificacion'][]='situacion_plantilla_17';
  $array_parametros['tmnc_sclasificacion'][]='situacion_plantilla_18';
  $array_parametros['tmnc_sclasificacion'][]='situacion_plantilla_22';
  $array_parametros['tmnc_sclasificacion'][]='motivo_devolucion';

  $array_parametros['tmnc_senvios'][]='programa_solicitud';
  $array_parametros['tmnc_senvios'][]='respuesta_enviada';
  $array_parametros['tmnc_senvios'][]='con_datos';
  $array_parametros['tmnc_senvios'][]='datos_incompletos';
  $array_parametros['tmnc_senvios'][]='parrafo_plantilla_16';
  $array_parametros['tmnc_senvios'][]='parrafo_plantilla_17';
  $array_parametros['tmnc_senvios'][]='parrafo_plantilla_18';
  $array_parametros['tmnc_senvios'][]='devolucion_correo';
  $array_parametros['tmnc_senvios'][]='responsable_clasificacion';

  $array_parametros['tmnc_sfirma_respuesta'][]='modulo';
  $array_parametros['tmnc_sfirma_respuesta'][]='git';
  $array_parametros['tmnc_sfirma_respuesta'][]='proyector';
  $array_parametros['tmnc_sfirma_respuesta'][]='aprobador';
  $array_parametros['tmnc_sfirma_respuesta'][]='responsable_firma';

  $array_parametros['tmnc_scasos_sgestionar'][]='proceso_ingreso_solidario';
  $array_parametros['tmnc_scasos_sgestionar'][]='responsable_envio';
  $array_parametros['tmnc_scasos_sgestionar'][]='responsable_proyeccion';
  $array_parametros['tmnc_scasos_sgestionar'][]='causal_no_envio';
  $array_parametros['tmnc_scasos_sgestionar'][]='causal_no_proyeccion';

  $array_parametros['tmnc_saprobacion_novedades'][]='tipo_documento';
  $array_parametros['tmnc_saprobacion_novedades'][]='tipo_novedad';
  $array_parametros['tmnc_saprobacion_novedades'][]='datos_basicos';
  $array_parametros['tmnc_saprobacion_novedades'][]='suspension';
  $array_parametros['tmnc_saprobacion_novedades'][]='reactivacion';
  $array_parametros['tmnc_saprobacion_novedades'][]='retiro';
  $array_parametros['tmnc_saprobacion_novedades'][]='gestion';
  $array_parametros['tmnc_saprobacion_novedades'][]='tipo_rechazo';
  $array_parametros['tmnc_saprobacion_novedades'][]='realizo_cambio_datos';
  $array_parametros['tmnc_saprobacion_novedades'][]='correccion_datos';

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
                              <label for="ceco_formulario" class="my-0">Formulario</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="ceco_formulario" id="ceco_formulario" maxlength="100" value="<?php echo $parametro; ?>" required disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ceco_campo">Campo</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="ceco_campo" id="ceco_campo" required <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito_configuracion']==1) { echo 'disabled'; } ?>>
                                  <option value="">Seleccione</option>
                                  <?php if(isset($array_parametros[$parametro])): ?>
                                    <?php for ($i=0; $i < count($array_parametros[$parametro]); $i++): ?>
                                      <option value="<?php echo $array_parametros[$parametro][$i]; ?>" <?php if($ceco_campo==$array_parametros[$parametro][$i]){ echo "selected"; } ?>><?php echo $array_parametros[$parametro][$i]; ?></option>
                                    <?php endfor; ?>
                                  <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="ceco_valor" class="my-0">Valor</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="ceco_valor" id="ceco_valor" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $ceco_valor; } ?>" required <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito_configuracion']==1) { echo 'readonly'; } ?>>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ceco_estado">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="ceco_estado" id="ceco_estado" required <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito_configuracion']==1) { echo 'disabled'; } ?>>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($ceco_estado=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($ceco_estado=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_canal_escrito_configuracion']==1): ?>
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