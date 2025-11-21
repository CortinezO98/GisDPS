<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Radicación";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['id_registro']));

    $consulta_string="SELECT `grca_id`, `grca_radicado`, `grca_nombre`, `grca_ruta`, `grca_extension` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_id`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<div class="row px-4 py-2">
    <div class="col-md-12 color-gris my-2">
        <b><?php echo validar_extension_icono(strtolower($resultado_registros[0][4]))." ".$resultado_registros[0][2]; ?></b>
    </div>
    <?php if (strtolower($resultado_registros[0][4])=="pdf"): ?>
        <embed src="<?php echo $resultado_registros[0][3]; ?>?ran=<?php echo generar_codigo(5); ?>#zoom=100" id="visor" style="width: 100%; min-height: 600px;">
    <?php elseif (strtolower($resultado_registros[0][4])=="png" OR strtolower($resultado_registros[0][4])=="jpg" OR strtolower($resultado_registros[0][4])=="jpeg"): ?>
        <img src="<?php echo $resultado_registros[0][3]; ?>?ran=<?php echo generar_codigo(5); ?>" id="visor" class="img-fluid"></img>
    <?php elseif (strtolower($resultado_registros[0][4])=="xls" OR strtolower($resultado_registros[0][4])=="xlsx" OR strtolower($resultado_registros[0][4])=="doc" OR strtolower($resultado_registros[0][4])=="docx"): ?>
        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https%3A%2F%2Fdps.iq-online.net.co%2Fradicados%2F<?php echo $resultado_registros[0][3]; ?>?ran=<?php echo generar_codigo(5); ?>&embedded=true" id="visor" style="border: none; width: 100%; min-height: 450px;"></iframe>
    <?php else: ?>
        <p class="alert alert-warning p-1 font-size-11 mt-1"><span class="fas fa-exclamation-triangle"></span> ¡No es posible visualizar el documento, por favor contacte al administrador!</p>
    <?php endif; ?>
</div>