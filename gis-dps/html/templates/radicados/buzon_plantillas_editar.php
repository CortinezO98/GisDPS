<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Radicación-Configuración";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Radicación";
  $subtitle = "Configuración | Plantillas | Editar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="buzon_plantillas?pagina=".$pagina."&id=".$filtro_permanente;
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  if(isset($_POST["guardar_registro"])){
    $grcp_estado=validar_input($_POST['grcp_estado']);
    $grcp_tipo=validar_input($_POST['grcp_tipo']);
    $grcp_nombre=validar_input($_POST['grcp_nombre']);
    $grcp_contenido=$_POST['grcp_contenido'];

    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_radicacion_casos_plantillas` SET `grcp_nombre`=?, `grcp_estado`=?, `grcp_tipo`=?, `grcp_contenido`=?, `grcp_actualiza_usuario`=?, `grcp_actualiza_fecha`=? WHERE `grcp_id`=?");
    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param("sssssss", $grcp_nombre, $grcp_estado, $grcp_tipo, $grcp_contenido, $_SESSION[APP_SESSION.'_session_usu_id'], date('Y-m-d H:i:s'), $id_registro);
    
    // Ejecuta sentencia preparada
    $consulta_actualizar->execute();

    // Evalua resultado de ejecución sentencia preparada
    if (comprobarSentencia($enlace_db->info)) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
    } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
    }
  }

    $consulta_string="SELECT `grcp_id`, `grcp_nombre`, `grcp_estado`, `grcp_tipo`, `grcp_contenido`, `grcp_actualiza_usuario`, `grcp_actualiza_fecha`, `grcp_registro_usuario`, `grcp_registro_fecha`, TUA.`usu_nombres_apellidos` FROM `gestion_radicacion_casos_plantillas` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_radicacion_casos_plantillas`.`grcp_actualiza_usuario`=TUA.`usu_id` WHERE `grcp_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <style type="text/css">
    .ck-editor__editable_inline {
        min-height: 550px;
        max-height: 550px;
    }

  </style>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="grcp_estado" class="m-0">Estado</label>
                                <select class="form-control form-control-sm form-select" name="grcp_estado" id="grcp_estado" required>
                                    <option value="">Seleccione</option>
                                    <option value='Activo' <?php if($resultado_registros[0][2]=='Activo'){ echo "selected"; } ?>>Activo</option>
                                    <option value='Inactivo' <?php if($resultado_registros[0][2]=='Inactivo'){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="grcp_tipo" class="m-0">Tipo</label>
                                <select class="form-control form-control-sm form-select" name="grcp_tipo" id="grcp_tipo" required>
                                    <option value="">Seleccione</option>
                                    <option value='Correspondencia' <?php if($resultado_registros[0][3]=='Correspondencia'){ echo "selected"; } ?>>Correspondencia</option>
                                    <option value='Notificaciones Jurídicas' <?php if($resultado_registros[0][3]=='Notificaciones Jurídicas'){ echo "selected"; } ?>>Notificaciones Jurídicas</option>
                                    <option value='Respuesta' <?php if($resultado_registros[0][3]=='Respuesta'){ echo "selected"; } ?>>Respuesta</option>
                                    <option value='Respuesta Radicado DELTA' <?php if($resultado_registros[0][3]=='Respuesta Radicado DELTA'){ echo "selected"; } ?>>Respuesta Radicado DELTA</option>
                                    <option value='Soy Transparente' <?php if($resultado_registros[0][3]=='Soy Transparente'){ echo "selected"; } ?>>Soy Transparente</option>
                                    <option value='Firma' <?php if($resultado_registros[0][3]=='Firma'){ echo "selected"; } ?>>Firma</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="grcp_nombre" class="m-0">Nombre plantilla</label>
                              <input type="text" class="form-control form-control-sm" name="grcp_nombre" id="grcp_nombre" maxlength="300" value="<?php echo $resultado_registros[0][1]; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="grcp_contenido" class="m-0">Contenido plantilla</label>
                              <textarea class="form-control form-control-sm font-size-11 height-100" name="grcp_contenido" id="grcp_contenido"><?php echo $resultado_registros[0][4]; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Guardar</button>
                                <?php if(isset($_POST["guardar_registro"])): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php endif; ?>
                                <?php if(!isset($_POST["guardar_registro"])): ?>
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
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/super-build/ckeditor.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/super-build/translations/es.js"></script>
  <script type="text/javascript">
    CKEDITOR.ClassicEditor.create(document.getElementById("grcp_contenido"), {
        // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
        toolbar: {
            items: [
                'undo', 'redo', '|',
                'fontFamily', 'fontSize', 'bold', 'italic', 'underline', 'strikethrough', 'fontColor', 'highlight', '|',
                'bulletedList', 'numberedList',
                'outdent', 'indent', 'alignment', '|',
                'subscript', 'superscript', '|',
                'link', 'insertImage', 'blockQuote', 'insertTable', 'sourceEditing', '|',
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
        },
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
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
        placeholder: 'Escriba el contenido aquí!',
        // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
        fontFamily: {
            options: [
                'default',
                'Arial, Helvetica, sans-serif',
                'Courier New, Courier, monospace',
                'Georgia, serif',
                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif',
                'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif',
                'Verdana, Geneva, sans-serif'
            ],
            supportAllValues: true
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
        fontSize: {
            options: [ 10, 12, 14, 'default', 18, 20, 22 ],
            supportAllValues: true
        },
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
            ]
        },
        // Be careful with enabling previews
        // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
        htmlEmbed: {
            showPreviews: true
        },
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
                }
            }
        },
        language: {
            ui: 'es',
            content: 'es'
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
        mention: {
            feeds: [
                {
                    marker: '@',
                    feed: [
                        '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                        '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                        '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                        '@sugar', '@sweet', '@topping', '@wafer'
                    ],
                    minimumCharacters: 1
                }
            ]
        },
        // Deshabilita el tamaño predeterminado
        // image2_defaultSize: '300px',
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
    });
  </script>
</body>
</html>