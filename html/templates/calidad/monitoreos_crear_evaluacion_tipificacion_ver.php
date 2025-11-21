<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Calidad-Monitoreos";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));

    $consulta_string="SELECT `gi_id_caso`, `gi_primer_nombre`, `gi_segundo_nombre`, `gi_primer_apellido`, `gi_segundo_apellido`, `gi_tipo_documento`, `gi_identificacion`, `gi_fecha_nacimiento`, `gi_edad`, `gi_municipio`, `gi_telefono`, `gi_celular`, `gi_email`, `gi_direccion`, `gi_direcciones_misionales`, `gi_programa`, `gi_tipificacion`, `gi_subtipificacion_1`, `gi_subtipificacion_2`, `gi_subtipificacion_3`, `gi_consulta`, `gi_respuesta`, `gi_resultado`, `gi_descripcion_resultado`, `gi_complemento_resultado`, `gi_canal_atencion`, `gi_registro_usuario`, `gi_registro_fecha`, TU.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TC.`ciu_departamento`, TC.`ciu_municipio` FROM `gestion_interacciones` LEFT JOIN `administrador_usuario` AS TU ON `gestion_interacciones`.`gi_registro_usuario`=TU.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON `gestion_interacciones`.`gi_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON `gestion_interacciones`.`gi_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON `gestion_interacciones`.`gi_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON `gestion_interacciones`.`gi_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON `gestion_interacciones`.`gi_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON `gestion_interacciones`.`gi_subtipificacion_3`=TN6.`gic6_id` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_interacciones`.`gi_municipio`=TC.`ciu_codigo` WHERE `gi_id_caso`=? ORDER BY `gi_registro_fecha` DESC";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<div class="row px-4 py-2">
    <div class="col-md-3">
        <div class="row">
            <?php if(count($resultado_registros)>0): ?>
                <div class="col-md-12">
                    <p class="alert background-principal color-blanco py-1 px-2"><span class="fas fa-info-circle"></span> Información general del caso</p>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="canal_atencion" class="my-0">Canal de atención</label>
                        <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][25]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                      <label for="id_caso" class="my-0">Id Caso</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][0]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="tipo_documento" class="my-0">Tipo documento</label>
                        <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][5]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                      <label for="identificacion" class="my-0">Identificación</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][6]; ?>" readonly>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-md-12 fondo-blanco p-0 mb-2">
                    <p class="alert alert-warning text-left p-1 m-0"><span class="fas fa-exclamation-triangle"></span> ¡No se encontraron registros!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-9">
        <div class="row">
            <?php if(count($resultado_registros)>0): ?>
                <div class="col-md-12">
                    <p class="alert background-principal color-blanco py-1 px-2"><span class="fas fa-user"></span> Datos personales del ciudadano</p>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="primer_nombre" class="my-0">Primer nombre</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][1]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="segundo_nombre" class="my-0">Segundo nombre</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][2]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="primer_apellido" class="my-0">Primer apellido</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][3]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="segundo_apellido" class="my-0">Segundo apellido</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][4]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="fecha_nacimiento" class="my-0">Fecha nacimiento</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][7]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="edad" class="my-0">Edad</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][8]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="municipio" class="my-0">Municipio/departamento</label>
                        <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][36].'/'.$resultado_registros[0][35]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="direccion" class="my-0">Dirección</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][13]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="celular" class="my-0">Celular</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][11]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="telefono" class="my-0">Teléfono</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][10]; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label for="email" class="my-0">Email</label>
                      <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[0][12]; ?>" readonly>
                    </div>
                </div>
                <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <div class="col-md-12">
                        <hr>
                        <p class="alert background-principal color-blanco py-1 px-2"><span class="fas fa-file-alt"></span> Información de la interacción</p>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="direcciones_misionales" class="my-0">Direcciones misionales</label>
                            <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[$i][29]; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="programa" class="my-0">Programa</label>
                            <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[$i][30]; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tipificacion" class="my-0">Tipificación</label>
                            <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[$i][31]; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="subtipificacion_1" class="my-0">Subtipificación 1</label>
                            <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[$i][32]; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="subtipificacion_2" class="my-0">Subtipificación 2</label>
                            <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[$i][33]; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="subtipificacion_3" class="my-0">Subtipificación 3</label>
                            <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[$i][34]; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                          <label for="consulta" class="my-0">Consulta</label>
                          <textarea class="form-control form-control-sm" name="" readonly><?php echo $resultado_registros[$i][20]; ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <p class="alert background-principal color-blanco py-1 px-2"><span class="fas fa-check-circle"></span> Resultado de la interacción</p>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                          <label for="respuesta" class="my-0">Respuesta</label>
                          <textarea class="form-control form-control-sm" name="" readonly><?php echo $resultado_registros[$i][21]; ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="resultado" class="my-0">Resultado</label>
                            <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[$i][22]; ?>" readonly>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="descripcion_resultado" class="my-0">Descripción del resultado</label>
                            <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[$i][23]; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="complemento_resultado" class="my-0">Complemento del resultado</label>
                            <input type="text" class="form-control form-control-sm" name="" value="<?php echo $resultado_registros[$i][24]; ?>" readonly>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php else: ?>
                <div class="col-md-12 fondo-blanco p-0 mb-2">
                    <p class="alert alert-warning text-left p-1 m-0"><span class="fas fa-exclamation-triangle"></span> ¡No se encontraron registros!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>