<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Envíos WEB";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));

    $consulta_string="SELECT `gewca_id`, `gewca_radicado`, `gewca_nombre`, `gewca_ruta`, `gewca_extension` FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_id`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>

<?php for ($i=0; $i < count($resultado_registros); $i++): ?>
  <div class="row flex-grow">
    <div class="col-12 grid-margin stretch-card my-1">
      <div class="card card-rounded">
        <div class="card-body">
          <div class="row">
            <div class="col-9 color-gris my-2">
                <b><?php echo validar_extension_icono(strtolower($resultado_registros[0][4]))." ".$resultado_registros[0][2]; ?></b>
            </div>
            <div class="col-3 text-end">
                <a class="btn btn-danger py-1 px-1" id="btn_cerrar_duplicado" onclick="cerrar_dividida();"><span class="fas fa-times-circle"></span> Cerrar</a>
            </div>
            <?php if (strtolower($resultado_registros[0][4])=="pdf"): ?>
                <embed src="<?php echo $resultado_registros[0][3]; ?>?ran=<?php echo generar_codigo(5); ?>#zoom=100" id="visor" style="width: 100%; min-height: 600px;">
            <?php elseif (strtolower($resultado_registros[0][4])=="png" OR strtolower($resultado_registros[0][4])=="jpg" OR strtolower($resultado_registros[0][4])=="jpeg"): ?>
                <img src="<?php echo $resultado_registros[0][3]; ?>?ran=<?php echo generar_codigo(5); ?>" id="visor" class="img-fluid"></img>
            <?php elseif (strtolower($resultado_registros[0][4])=="xls" OR strtolower($resultado_registros[0][4])=="xlsx" OR strtolower($resultado_registros[0][4])=="doc" OR strtolower($resultado_registros[0][4])=="docx"): ?>
                <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https%3A%2F%2Fdps.iq-online.net.co%2Fenvios_web%2F<?php echo $resultado_registros[0][3]; ?>?ran=<?php echo generar_codigo(5); ?>&embedded=true" id="visor" style="border: none; width: 100%; min-height: 450px;"></iframe>
            <?php else: ?>
                <p class="alert alert-warning p-1 font-size-11 mt-1"><span class="fas fa-exclamation-triangle"></span> ¡No es posible visualizar el documento, por favor contacte al administrador!</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endfor; ?>