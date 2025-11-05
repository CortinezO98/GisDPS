<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Control Turnos";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));
    $fecha_turno=validar_input(base64_decode($_GET['fecha_turno']));

    $consulta_string_usuarios="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_cargo_rol`, `usu_estado`, `usu_piloto` FROM `administrador_usuario` LEFT JOIN `administrador_campania` AS TCA ON `administrador_usuario`.`usu_campania`=TCA.`ac_id` WHERE `usu_id`=?";
    $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
    $consulta_registros_usuarios->bind_param("s", $id_registro);
    $consulta_registros_usuarios->execute();
    $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

    $data_consulta_turnos=array();
    array_push($data_consulta_turnos, "$fecha_turno%");
    array_push($data_consulta_turnos, $id_registro);

    $consulta_string_turno_realizado="SELECT `cot_id`, `cot_usuario`, `cot_tipo`, `cot_inicio`, `cot_fin`, `cot_duracion`, `cot_fuente`, `cot_observaciones_inicio`, `cot_observaciones_fin`, `cot_registro_fecha`, TU.`usu_nombres_apellidos` FROM `control_turno` LEFT JOIN `administrador_usuario`AS TU ON `control_turno`.`cot_usuario`=TU.`usu_id` WHERE `cot_inicio` LIKE ? AND `cot_usuario`=? AND `cot_fin`='' ORDER BY `cot_id` ASC";

    $consulta_registros_turno_realizado = $enlace_db->prepare($consulta_string_turno_realizado);
    if (count($data_consulta_turnos)>0) {
        $consulta_registros_turno_realizado->bind_param(str_repeat("s", count($data_consulta_turnos)), ...$data_consulta_turnos);
    }
    $consulta_registros_turno_realizado->execute();
    $resultado_registros_turno_realizado = $consulta_registros_turno_realizado->get_result()->fetch_all(MYSQLI_NUM);
?>
<form name="guardar_registro" action="" method="POST" id="formulario_pregunta" enctype="multipart/form-data">
    <div class="row px-2">
        <div class="col-md-12">
            <div class="form-group">
                <label for="nombre_usuario" class="my-0">Usuario</label>
                <input type="text" class="form-control form-control-sm" name="nombre_usuario" id="nombre_usuario" value="<?php echo $resultado_registros_usuarios[0][1]; ?>" required readonly>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="fecha_turno" class="my-0">Fecha turno</label>
                <input type="date" class="form-control form-control-sm" name="fecha_turno" id="fecha_turno" value="<?php echo $fecha_turno; ?>" required readonly>
            </div>
        </div>
        <?php if(count($resultado_registros_turno_realizado)>0): ?>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="my-0">¿Confirma cierre manual de turno?</label>
                    <select class="form-control form-control-sm form-select" name="estado_turno" id="estado_turno" required>
                        <option value="">Seleccione</option>
                        <option value="cerrar_turno">Si, cerrar turno de forma manual</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="motivo" class="my-0">Motivo</label>
                    <input type="text" class="form-control form-control-sm" name="motivo" id="motivo" value="<?php echo $motivo; ?>" required>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-12">
                <p class="alert alert-warning p-1 font-size-11">¡No se encontraron actividades pendientes de cierre!</p>
            </div>
        <?php endif; ?>
        <div class="col-md-12 mt-1">
            <div class="form-group row">
                <div class="col-md-12 respuesta_contenido"></div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    function guardar_info(){
        var btnEnviar = $('#btnEnviar');
        var btnCancelar = $('#btnCancelar');
        var id_usuario = '<?php echo base64_encode($id_registro); ?>';
        var fecha_turno = '<?php echo base64_encode($fecha_turno); ?>';
        var estado_turno = $("#estado_turno").val();
        var motivo = $("#motivo").val();

        var  formData = new FormData();
        if (id_usuario!='' && fecha_turno!='') {
            formData.append("estado_turno", estado_turno);
            formData.append("motivo", motivo);
        }

        if (id_usuario!="" & fecha_turno!="" & estado_turno!="" & motivo!="") {
            $.ajax({
                type: 'POST',
                url: 'turno_realizado_cerrar_procesar.php?id_usuario='+id_usuario+'&fecha_turno='+fecha_turno,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    $('#estado_turno').prop("disabled", true);
                    $('#motivo').prop("disabled", true);

                    btnEnviar.prop("disabled", true);
                    btnCancelar.prop("disabled", true);
                },
                complete:function(data){
                },
                success: function(data){
                    var resp = $.parseJSON(data);
                    $('.respuesta_contenido').html(resp.resultado);
                    
                    if (resp.resultado_valor) {
                        btnEnviar.prop("disabled", true);
                        btnCancelar.removeAttr("disabled");
                        $('#estado_turno').prop("disabled", true);
                        $('#motivo').prop("disabled", true);
                    } else {
                        btnEnviar.removeAttr("disabled");
                        btnCancelar.removeAttr("disabled");
                        $('#estado_turno').removeAttr("disabled");
                        $('#motivo').removeAttr("disabled");
                    }
                },
                error: function(data){
                    alert("Problemas al tratar de cerrar el turno, por favor verifica e intenta nuevamente");
                }
            });
        } else {
            alert("¡Por favor diligencia todos los campos e intenta nuevamente!");
        }
        
        return false;
    }
</script>