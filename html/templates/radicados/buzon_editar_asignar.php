<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Radicación";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Radicación";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $estado=validar_input(base64_decode($_GET['estado']));
  $subtitle = "Buzón | ".$bandeja." | ".$estado.' | Asignar';
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="buzon?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&estado=".base64_encode($estado);
 
  if(isset($_POST["guardar_registro"])){
      $grc_estado=validar_input($_POST['grc_estado']);
      $grc_responsable=validar_input($_POST['grc_responsable']);
      if($_SESSION[APP_SESSION.'_registro_asignado_radicacion']!=1){
        // Prepara la sentencia
        $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_radicacion_casos` SET `grc_responsable`=?, `grc_estado`=? WHERE `grc_id`=?");
        // Agrega variables a sentencia preparada
        $consulta_actualizar->bind_param('sss', $grc_responsable, $grc_estado, $id_registro);
        
        // Ejecuta sentencia preparada
        $consulta_actualizar->execute();
        if (comprobarSentencia($enlace_db->info)) {
          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
          $_SESSION[APP_SESSION.'_registro_asignado_radicacion']=1;
        } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al actualizar el registro');";
        }
      } else {
      }
  }
  $consulta_string="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_correo_id`, `grc_correo_remitente`, `grc_correo_fecha`, `grc_correo_para`, `grc_correo_cc`, `grc_correo_asunto`, `grc_correo_contenido`, `grc_registro_fecha_hora`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, `grc_gestion_asunto`, `grc_gestion_contenido`, `grc_gestion_fecha` FROM `gestion_radicacion_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos`.`grc_responsable`=TAG.`usu_id` WHERE `grc_id`=?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
  $array_estado_alert['Pendiente']='warning';
  $array_estado_alert['En trámite']='dark';
  $array_estado_alert['Finalizado']='success';
  $filtro_agente='';
  if ($resultado_registros[0][2]=='Notificaciones de correo') {
    $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE NOTIFICACIONES DE CORREO%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
    $filtro_agente_tipologia=" AND `grc_tipologia`='Notificaciones de correo'";
  } elseif ($resultado_registros[0][2]=='Envío Radicado a Ciudadano') {
    $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE ENVÍO RADICADO A CIUDADANO%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
    $filtro_agente_tipologia=" AND `grc_tipologia`='Envío Radicado a Ciudadano'";
  } elseif ($resultado_registros[0][2]=='Tutelas') {
    $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE TUTELAS%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
    $filtro_agente_tipologia=" AND `grc_tipologia`='Tutelas'";
  } elseif ($resultado_registros[0][2]=='Prioritario') {
    $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE PRIORITARIOS%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
    $filtro_agente_tipologia=" AND `grc_tipologia`='Prioritario'";
  } elseif ($resultado_registros[0][2]=='Funcionarios') {
    $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE FUNCIONARIOS%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
    $filtro_agente_tipologia=" AND `grc_tipologia`='Funcionarios'";
  } elseif ($resultado_registros[0][2]=='Ciudadanos') {
    $filtro_agente=" AND (`usu_cargo_rol` LIKE '%AGENTE CIUDADANOS%' OR `usu_cargo_rol` LIKE '%AGENTE RADICADOS-ENTRENAMIENTO%')";
    $filtro_agente_tipologia=" AND `grc_tipologia`='Ciudadanos'";
  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' ".$filtro_agente." ORDER BY `usu_nombres_apellidos` ASC";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <style type="text/css">
    .scroll-mail::-webkit-scrollbar-track {
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
      border-radius: 10px;
      background-color: #F5F5F5;
    }
    .scroll-mail::-webkit-scrollbar {
      width: 5px;
      height: 8px;
    .scroll-mail::-webkit-scrollbar-thumb {
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
      background-color: darkgrey;
    .scroll-mail {
        overflow-y: auto;
        overflow-x: auto;
        height: 600px !important;
    .ck-editor__editable_inline {
        min-height: 650px;
        max-height: 650px;
    /* Estilos personalizados para el campo de entrada de archivos */
    .custom-file-upload {
        position: relative;
        display: inline-block;
    .custom-file-upload input[type="file"] {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0); /* Para Internet Explorer */
    .custom-file-upload label {
        padding: 10px 10px 8px 10px;
        background-color: #3498db;
        color: #fff;
        border-radius: 4px;
    .custom-file-upload label:hover {
        background-color: #2980b9;
    .tachado {
        text-decoration:line-through;
  </style>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <!-- navbar -->
    <?php require_once(ROOT.'includes/_navbar.php'); ?>
    <div class="container-fluid page-body-wrapper">
      <!-- sidebar -->
      <?php require_once(ROOT.'includes/_sidebar.php'); ?>
      <!-- main-panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
          <div class="row justify-content-center">
            <?php if (!empty($respuesta_accion)) {echo "<script type='text/javascript'>".$respuesta_accion."</script>";} ?>
            <div class="col-lg-8 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12 font-size-12">
                          <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-2 py-0"><span class="fas fa-qrcode me-1"></span><?php echo $resultado_registros[0][1]; ?></span> <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-2 py-0"><span class="fas fa-sitemap me-1"></span><?php echo $resultado_registros[0][2]; ?></span>
                          <?php if($resultado_registros[0][2]=='Ciudadanos'): ?>
                            <span class="alert alert-<?php echo $array_estado_alert[$resultado_registros[0][7]]; ?> px-2 py-0"><span class="fas fa-timeline me-1"></span><?php echo $resultado_registros[0][3]; ?></span>
                          <?php endif; ?>
                          <div class="py-0 my-1"><b>De: </b><span id="grc_correo_remitente"><?php echo $resultado_registros[0][10]; ?></span> | <b>Enviado: </b><?php echo date('d/m/Y H:i:s', strtotime($resultado_registros[0][11])); ?></div>
                        </div>
                        <div class="col-md-12 my-1">
                            <div class="form-group m-0">
                              <div class="input-group">
                                <label for="grc_gestion_asunto" class="me-1 pt-1 fw-bold">Asunto:</label>
                                <input type="text" class="form-control form-control-sm" name="grc_gestion_asunto" id="grc_gestion_asunto" value="<?php echo ($resultado_registros[0][19]!='') ? $resultado_registros[0][19]: $resultado_registros[0][14]; ?>" required disabled>
                              </div>
                            </div>
                        <div class="col-md-12 font-size-12 mb-2">
                          <div>
                            <div class="custom-file-upload">
                              <input type="file" id="adjunto" name="adjunto[]" multiple onchange="adjuntos_cargar(this.files)" disabled>
                              <label for="adjuntos"><span class="fas fa-paperclip"></span> Adjuntos (<span id='adjuntos_conteo'></span>)</label>
                            <span id='adjuntos_lista'></span>
                          </div>
                        <div class="col-md-12">
                          <textarea class="form-control form-control-sm" name="grc_gestion_contenido" id="grc_gestion_contenido"><?php echo $resultado_registros[0][15]; ?></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 d-flex flex-column">
                      <div class="col-md-12">
                          <div class="form-group my-2">
                              <label for="grc_estado" class="my-0">Estado</label>
                              <select class="form-control form-control-sm form-select" name="grc_estado" id="grc_estado" <?php if($_SESSION[APP_SESSION.'_registro_asignado_radicacion']==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <option value="Pendiente" <?php if($resultado_registros[0][7]=="Pendiente"){ echo "selected"; } ?>>Pendiente</option>
                                  <option value="En trámite" <?php if($resultado_registros[0][7]=="En trámite"){ echo "selected"; } ?>>En trámite</option>
                                  <option value="Finalizado" <?php if($resultado_registros[0][7]=="Finalizado"){ echo "selected"; } ?>>Finalizado</option>
                              </select>
                              <label for="grc_responsable" class="my-0">Responsable</label>
                              <select class="form-control form-control-sm form-select" name="grc_responsable" id="grc_responsable" <?php if($_SESSION[APP_SESSION.'_registro_asignado_radicacion']==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?>
                                    <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" <?php if($resultado_registros[0][4]==$resultado_registros_analistas[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                                  <?php endfor; ?>
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_asignado_radicacion']==1): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Guardar</button>
                                  <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');">Cancelar</button>
                              <?php endif; ?>
          </div>
          </form>
        </div>
        <!-- content-wrapper ends -->
        <!-- MODAL detalle -->
        <div class="modal fade" id="modal-detalle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              <div class="modal-body-detalle">
                
              <div class="modal-footer">
      </div>
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
  <script type="text/javascript">
    // Variable global para almacenar la instancia del editor
    let editorInstance = null;
    let editorCreated = false;
    // Función para crear el editor y habilitar o deshabilitar según el parámetro
    function createAndToggleEditor(disabled, plantilla) {
      if (!editorCreated) {
        CKEDITOR.ClassicEditor.create(document.getElementById("grc_gestion_contenido"), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    // 'exportPDF','exportWord', '|',
                    // 'findAndReplace', 'selectAll', '|',
                    // 'heading', '|',
                    // 'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', '|',
                    //   'bulletedList', 'numberedList', 'todoList', '|',
                    //   'outdent', 'indent', '|',
                    //   'undo', 'redo',
                    //   '-',
                    //   'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    //   'alignment', '|',
                    //   'link', 'insertImage', 'blockQuote', 'insertTable', '|'
                    'undo', 'redo', '|',
                    'fontFamily', 'fontSize', 'bold', 'italic', 'underline', 'strikethrough', 'fontColor', 'highlight', '|',
                    'bulletedList', 'numberedList',
                    'outdent', 'indent', 'alignment', '|',
                    'subscript', 'superscript', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', '|',
                ],
                shouldNotGroupWhenFull: true,
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Escriba el contenido aquí!',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                supportAllValues: true
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    openInNewTab: {
                        mode: 'manual',
                        label: 'Abrir en una nueva pestaña',
                        attributes: {
                            target: '_blank',
                            rel: 'noopener noreferrer'
                        }
            language: {
                ui: 'es',
                content: 'es'
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents',
                'PasteFromOfficeEnhanced'
            ]
        })
        .then(editor => {
            // Almacenar la instancia del editor en la variable global
            editorInstance = editor;
            editorCreated = true; // Marcar que el editor ha sido creado
            // Deshabilitar el editor al crearlo por primera vez
            if (disabled) {
              editor.enableReadOnlyMode('grc_gestion_contenido');
            } else {
              editor.disableReadOnlyMode('grc_gestion_contenido');
            }
            // Ocultar la barra de herramientas
            const toolbarElement = document.querySelector('.ck-toolbar');
            toolbarElement.style.display = 'none';
        .catch( error => {
            console.error(error);
        });
      if (editorInstance) {
          // Habilitar o deshabilitar el editor según el parámetro
          if (disabled) {
            editorInstance.enableReadOnlyMode('grc_gestion_contenido');
          } else {
            editorInstance.disableReadOnlyMode('grc_gestion_contenido');
          }
          // Mostrar u ocultar la barra de herramientas según corresponda
          const toolbarElement = document.querySelector('.ck-toolbar');
          toolbarElement.style.display = disabled ? 'none' : 'block';
          if (plantilla) {
            var textoAConcatenar = $('#plantilla_text').val();
            var infoAConcatenar = $('#info_correo').val();
            editorInstance.setData(textoAConcatenar + '<br><br>'+ infoAConcatenar + editorInstance.getData());
    // Llamar a la función para crear el editor y dejarlo deshabilitado al cargar la página
    createAndToggleEditor(true, false);
    
  </script>
      function adjuntos_lectura(){
        var  formData = new FormData();
        formData.append("id_registro", '<?php echo base64_encode($resultado_registros[0][1]); ?>');
        if (true) {
            $.ajax({
                type: 'POST',
                url: 'buzon_editar_procesar.php?accion=adjuntos&tipo=lista_lectura',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    $('#adjuntos_conteo').html('');
                    $('#adjuntos_lista').html('¡Cargando, por favor espere!');
                },
                complete:function(data){
                success: function(data){
                    var resp = $.parseJSON(data);
                    // console.log(data);
                    if (resp.resultado_control>0) {
                        $('#adjuntos_conteo').html(resp.resultado_control);
                        $('#adjuntos_lista').html(resp.resultado);
                        
                    } else {
                        $('#adjuntos_conteo').html('0');
                        $('#adjuntos_lista').html('<span class="alert alert-warning p-1 font-size-12">¡No se encontraron documentos adjuntos!</span>');
                error: function(data){
                    alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
            });
            alert("Problemas al tratar de obtener los adjuntos del correo, por favor verifica e intenta nuevamente");
      function adjuntos_previa(id_registro) {
          var myModal = new bootstrap.Modal(document.getElementById("modal-detalle"), {});
          $('.modal-body-detalle').load('buzon_editar_adjunto_ver.php?id_registro='+id_registro,function(){
              $("#staticBackdropLabel").html('Vista previa');
              myModal.show();
          });
      adjuntos_lectura();
  
</body>
</html>
