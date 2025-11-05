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
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <tbody>
                        <tr>
                            <th class="px-1 py-2">Estado</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][14]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Id Interacción</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][1]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Identificación Usuario</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][4]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Nombres y Apellidos</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][5]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Identificación Titular</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][6]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Fecha Expedición</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][7]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Identificación Beneficiario</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][8]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Municipio/Departamento</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][11].' / '.$resultado_registros[0][10]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Dirección</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][12]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Responsable</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][20]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">No. Novedad</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][16]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Fecha Gestión</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][18]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Observaciones</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][17]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Fecha Interacción</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][2]; ?></td>
                        </tr>
                        <tr>
                            <th class="px-1 py-2">Fecha Registro</th>
                            <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[0][19]; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
          </div>
        </div>
    </div>
</div>