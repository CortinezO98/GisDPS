<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Gestión Traslado IVA";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));

    $consulta_string="SELECT `gti_id`, `gti_interaccion_id`, `gti_interaccion_fecha`, `gti_remitente`, `gti_cliente_identificacion`, `gti_cliente_nombre`, `gti_titular_cedula`, `gti_titular_fecha_expedicion`, `gti_beneficiario_identificacion`, `gti_link_foto`, `gti_departamento`, `gti_municipio`, `gti_direccion`, `gti_ruta_fichero`, `gti_estado`, `gti_responsable`, `gti_numero_novedad`, `gti_observaciones`, `gti_fecha_gestion`, `gti_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_traslado_iva` LEFT JOIN `administrador_usuario` AS TU ON `gestion_traslado_iva`.`gti_responsable`=TU.`usu_id` WHERE `gti_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<div class="row px-4 py-2">
    <div class="col-md-12">
        <div class="row">
          <div class="col-md-12 p-1">
            <img src="storage/<?php echo $resultado_registros[0][13]; ?>" class="img-fluid">
          </div>
        </div>
    </div>
</div>