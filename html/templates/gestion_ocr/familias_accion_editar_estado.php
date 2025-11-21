<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Gestión OCR-Gestión";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));
    $id_beneficiario=validar_input(base64_decode($_GET['beneficiario']));
    $id_item=validar_input($_GET['item']);
    
    $consulta_string="SELECT `ocrc_id`, `ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`, `ocrc_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion` FROM `gestion_ocr_consolidado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_consolidado`.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=? ORDER BY `ocrc_codbeneficiario` ASC";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("ss", $id_registro, $id_beneficiario);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<div class="row px-4 py-2">
    <div class="col-md-12">
        <form name="guardar_cambio_estado" id="guardar_cambio_estado" action="" method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-12">
                  <div class="form-group">
                    <label for="beneficiario_item" class="my-0">Beneficiario</label>
                    <input type="text" class="form-control form-control-sm" name="beneficiario_item" id="beneficiario_item" maxlength="100" value="<?php echo $resultado_registros[0][28]; ?><?php echo ($resultado_registros[0][29]!="") ? ' '.$resultado_registros[0][29] : ''; ?><?php echo ($resultado_registros[0][30]!="") ? ' '.$resultado_registros[0][30] : ''; ?><?php echo ($resultado_registros[0][31]!="") ? ' '.$resultado_registros[0][31] : ''; ?>" required disabled autocomplete="off">
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                    <label for="validacion_item" class="my-0">Validación</label>
                    <input type="text" class="form-control form-control-sm" name="validacion_item" id="validacion_item" maxlength="100" value="<?php echo $id_item; ?>" required disabled autocomplete="off">
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                      <label for="estado_item" class="my-0">Estado</label>
                      <select class="form-control form-control-sm form-select" name="estado_item" id="estado_item" required>
                            <option class="font-size-11" value="">Seleccione</option>
                            <option value="Cumple">Cumple</option>
                            <option value="No cumple">No cumple</option>
                      </select>
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                    <label for="observaciones_item" class="my-0">Observaciones/justificación</label>
                    <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones_item" id="observaciones_item" required></textarea>
                  </div>
              </div>
                <div class="col-md-12">
                    <div class="col-md-12 p-0 respuesta_cambio"></div>
                </div>
              <div class="col-md-12">
                  <div class="form-group">
                      <button class="btn btn-success float-end ms-1" type="submit" name="guardar_cambio_estado" id="btnguardar_cambio_estado" onclick="guardar_info();">Guardar</button>
                      <button class="btn btn-danger float-end" type="button" id="btncancelar" data-bs-dismiss="modal">Cancelar</button>
                  </div>
              </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function guardar_info(){
      $('#guardar_cambio_estado').bind("submit",function(){
        var btnEnviar = $('#btnguardar_cambio_estado');
        var btnCancelar = $('#btncancelar');
        var id_registro = '<?php echo base64_encode($id_registro); ?>';
        var id_registro_id = '<?php echo $id_registro; ?>';
        var id_beneficiario = '<?php echo base64_encode($id_beneficiario); ?>';
        var id_beneficiario_id = '<?php echo $id_beneficiario; ?>';
        var id_item = '<?php echo $id_item; ?>';
        
        $.ajax({
            type: 'POST',
            url: 'familias_accion_editar_estado_procesar.php?reg='+id_registro+'&beneficiario='+id_beneficiario+'&item='+id_item,
            data:$('#guardar_cambio_estado').serialize(),
            beforeSend: function(){
                $('#estado_item').prop("disabled", true);
                $('#observaciones_item').prop("disabled", true);
                btnEnviar.prop("disabled", true);
                btnCancelar.prop("disabled", true);
            },
            complete:function(data){
            },
            success: function(data){
                var resp = $.parseJSON(data);
                $('.respuesta_cambio').html(resp.resultado_estado);
                if (resp.resultado_estado_valor) {
                    btnCancelar.removeAttr("disabled");
                    $('#estado_item').prop("disabled", true);
                    $('#observaciones_item').prop("disabled", true);
                    btnCancelar.html('Cerrar');
                    if (resp.estado=='Cumple') {
                        $('#'+id_item+'_'+id_registro_id).html("<span class='fas fa-check-circle color-verde'></span>");
                    } else {
                        $('#'+id_item+'_'+id_registro_id).html("<span class='fas fa-times-circle color-rojo'></span>");
                    }
                } else {
                    btnEnviar.removeAttr("disabled");
                    $('#estado_item').removeAttr("disabled");
                    $('#observaciones_item').removeAttr("disabled");
                }
            },
            error: function(data){
                alert("Problemas al tratar de actualizar el estado, por favor verifica e intenta nuevamente");
            }
        });
        return false;
      });
    }
</script>