<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Radicación";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));

    $consulta_string_historico="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom` FROM `gestion_radicacion_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_radicacion_casos_historial`.`grch_correo_de`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos_historial`.`grch_registro_usuario`=TAG.`usu_id` WHERE `grch_radicado_id`=? AND `grch_tipo`<>'Borrador' ORDER BY `grch_id` ASC";

    $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
    $consulta_registros_historico->bind_param("s", $id_registro);
    $consulta_registros_historico->execute();
    $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);

    $historial_id=$resultado_registros_historico[0][0];

    $consulta_string_adjuntos="SELECT `grca_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_radicado`, `grca_radicado_id` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_historial_id`=? ORDER BY `grca_id` ASC";
    $consulta_registros_adjuntos = $enlace_db->prepare($consulta_string_adjuntos);
    $consulta_registros_adjuntos->bind_param("s", $historial_id);
    $consulta_registros_adjuntos->execute();
    $resultado_registros_adjuntos = $consulta_registros_adjuntos->get_result()->fetch_all(MYSQLI_NUM);

    $resultado_data='';
    if (count($resultado_registros_adjuntos)>0) {
        $resultado_control=count($resultado_registros_adjuntos);
        $control_separa=false;
        
        for ($i=0; $i < count($resultado_registros_adjuntos); $i++) { 
            if (!$control_separa AND $resultado_registros_adjuntos[$i][5]=='Adjunto') {
                // $control_separa=true;
                // $resultado_data.='<hr class="my-1">';
            }

            if ($resultado_registros_adjuntos[$i][5]=='Original' AND $resultado_registros_adjuntos[$i][6]=='Inactivo') {
                $tipo_btn='btn-outline-danger';
                $tachado='tachado';
                $quita_agrega='';
            } else {
                $tipo_btn='btn-secondary';
                $tachado='';
                $quita_agrega='';
            }

            $resultado_data.='
                <div class="btn-group mb-1 me-2">
                  <button type="button" class="btn '.$tipo_btn.' '.$tachado.' px-1 py-2" onclick="adjuntos_previa('."'".base64_encode($resultado_registros_adjuntos[$i][0])."'".')">'.validar_extension_icono($resultado_registros_adjuntos[$i][4]).' '.$resultado_registros_adjuntos[$i][2].'</button>
                  <button type="button" class="btn '.$tipo_btn.' px-2 py-2 dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                    <a class="dropdown-item" href="#" onclick="adjuntos_previa('."'".base64_encode($resultado_registros_adjuntos[$i][0])."'".')"><span class="fas fa-eye"></span> Vista previa</a>
                    <a class="dropdown-item" href="buzon_editar_adjunto_descargar.php?id_registro='.base64_encode($resultado_registros_adjuntos[$i][0]).'" target="_blank"><span class="fas fa-download"></span> Descargar</a>
                    '.$quita_agrega.'
                  </div>
                </div>
            ';
        }
    } else {
        $resultado_control=0;
    }

    $array_estado_alert['Pendiente']='warning';
    $array_estado_alert['En trámite']='dark';
    $array_estado_alert['Finalizado']='success';
?>
<style type="text/css">
    .ck-editor__editable_inline {
        min-height: 650px;
        max-height: 650px;
    }

    /* Estilo para quitar todos los estilos dentro del editor CKEditor */
    .ck-editor .table td img {
      /* Resetear todos los estilos */
      all: unset !important;
    }
</style>
<?php for ($i=0; $i < count($resultado_registros_historico); $i++): ?>
    <?php if($resultado_registros_historico[$i][3]!='Borrador'): ?>
    <?php
      if ($resultado_registros_historico[$i][3]=='Radicado') {
        $fecha_gestion=date('d/m/Y', strtotime($resultado_registros_historico[$i][19]));
        $hora_gestion=date('H:i', strtotime($resultado_registros_historico[$i][19]));
      } elseif ($resultado_registros_historico[$i][3]=='Gestión') {
        if ($resultado_registros_historico[$i][28]!='') {
          $fecha_gestion=date('d/m/Y', strtotime($resultado_registros_historico[$i][28]));
          $hora_gestion=date('H:i', strtotime($resultado_registros_historico[$i][28]));
        } else {
          $fecha_gestion='Pendiente';
          $hora_gestion='Pendiente';
        }
      } elseif ($resultado_registros_historico[$i][3]=='Borrador') {
        $fecha_gestion=date('d/m/Y', strtotime($resultado_registros_historico[$i][30]));
        $hora_gestion=date('H:i', strtotime($resultado_registros_historico[$i][30]));
      }
    ?>
    <div class="row flex-grow">
      <div class="col-12 grid-margin stretch-card my-1">
        <div class="card card-rounded">
          <div class="card-body">
            <div class="row">
              <div class="col-9">
                  <p class="font-size-11 my-0 py-0">
                    <?php if ($resultado_registros_historico[$i][3]=='Radicado'): ?>
                     <span class="alert alert-<?php echo $array_estado_alert['Finalizado']; ?> px-1 py-0 my-0"><span class="fas fa-qrcode me-1"></span><?php echo $resultado_registros_historico[$i][1]; ?></span>
                    <?php endif; ?>
                    <?php if ($resultado_registros_historico[$i][3]=='Radicado'): ?>
                      <span class="alert alert-dark px-1 py-0 my-0 font-size-11"><span class="fas fa-envelope me-1"></span>Radicado</span>
                    <?php elseif ($resultado_registros_historico[$i][3]=='Gestión'): ?>
                      <span class="alert alert-success px-1 py-0 my-0 font-size-11"><span class="fas fa-user-check me-1"></span>Gestión</span>
                    <?php elseif ($resultado_registros_historico[$i][3]=='Borrador'): ?>
                      <span class="alert alert-warning px-1 py-0 my-0 font-size-11"><span class="fas fa-user-cog me-1"></span>Borrador</span>
                    <?php endif; ?>

                    <span class="alert alert-<?php echo $array_estado_alert['Finalizado']; ?> px-1 py-0 me-1"><span class="fas fa-sitemap me-1"></span><?php echo $resultado_registros_historico[$i][4]; ?></span>
                    <?php if($resultado_registros_historico[$i][4]=='Ciudadanos'): ?><span class="alert alert-<?php echo $array_estado_alert['Finalizado']; ?> px-1 py-0 me-1"><span class="fas fa-timeline me-1"></span><?php echo $resultado_registros_historico[$i][5]; ?></span><?php endif; ?>

                    <?php if($resultado_registros_historico[$i][6]!=''): ?><span class="alert alert-<?php echo $array_estado_alert['Finalizado']; ?> px-1 py-0 me-1"><span class="fas fa-cog me-1"></span><?php echo $resultado_registros_historico[$i][6]; ?></span><?php endif; ?>
                    <br>
                    <?php if ($resultado_registros_historico[$i][3]!='Radicado'): ?>
                      <span class="alert alert-<?php echo $array_estado_alert['Finalizado']; ?> px-1 py-0 me-1"><span class="fas fa-user-tie me-1"></span><?php echo $resultado_registros_historico[$i][31]; ?></span>
                    <?php endif; ?>
                  </p>
                  <hr class="my-1">
                  <p class="font-size-12">
                    <a class="btn btn-success py-1 px-1 font-size-11" onClick="mostrarOcultar('<?php echo $resultado_registros_historico[$i][0]; ?>');"><span class="fas fa-plus"></span></a> <span class="fas fa-user"></span> De: <?php echo ($resultado_registros_historico[$i][3]=='Radicado') ? $resultado_registros_historico[$i][15] : $resultado_registros_historico[$i][33]; ?>
                    
                  </p>
                  <p class="font-size-12 fw-bold" id="asunto_correo_original"><?php echo $resultado_registros_historico[$i][20]; ?></p>
              </div>
              <div class="col-3 text-end">
                  <?php if ($resultado_registros_historico[$i][3]=='Radicado'): ?>
                    <a href="#" onClick="open_modal_detalle('<?php echo base64_encode($resultado_registros_historico[$i][2]); ?>');" class="btn btn-dark btn-icon px-1 py-1" title="Imprimir"><i class="fas fa-print font-size-11"></i></a> <a class="btn btn-danger py-1 px-1" id="btn_cerrar_duplicado" onclick="cerrar_duplicado();"><span class="fas fa-times-circle"></span> Cerrar</a>
                  <?php endif; ?>
                    <p class="font-size-12 my-0 py-0 px-1"><span class="fas fa-calendar-alt"></span> <?php echo $fecha_gestion; ?></p>
                    <p class="font-size-12 my-0 py-0 px-1"><span class="fas fa-clock"></span> <?php echo $hora_gestion; ?></p>
              </div>
              <div class="d-none" id="historico_correo_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>">
                <div class="col-md-12 font-size-12 mb-2">
                  <div>
                    <span id='adjuntos_lista'><?php echo $resultado_data; ?></span>
                  </div>
                </div>
                <div class="col-md-12">
                  <textarea class="form-control form-control-sm" name="grc_gestion_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>" id="grc_gestion_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>"><?php echo removeEmojis($resultado_registros_historico[$i][21]); ?></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
<?php endfor; ?>
<script type="text/javascript">
    // Función para crear el editor y habilitar o deshabilitar según el parámetro
    function createAndToggleEditorView(id_contenido) {
      CKEDITOR.ClassicEditor.create(document.getElementById(id_contenido), {
          // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
          toolbar: {
              items: [
                  // 'exportPDF','exportWord', '|',
                  // 'exportPDF', '|',
                  // 'findAndReplace', 'selectAll', '|',
                  // 'heading', '|',
                  // 'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', '|',
                  //   'bulletedList', 'numberedList', 'todoList', '|',
                  //   'outdent', 'indent', '|',
                  //   'undo', 'redo',
                  //   '-',
                  //   'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                  //   'alignment', '|',
                  //   'link', 'insertImage', 'blockQuote', 'insertTable', 'sourceEditing', '|'
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
                      attributes: false,
                      classes: false,
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
          exportpdf: {
              margin_left: '10cm',
              margin_right: '10cm',
              margin_top: '10cm',
              margin_bottom: '10cm'
          },
          // extraPlugins: 'exportpdf',
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
          ],
      })
      .then(editorv => {
          // Deshabilitar el editorv al crearlo por primera vez
          editorv.enableReadOnlyMode(id_contenido);
          // editor.disableReadOnlyMode(id_contenido);
          // Ocultar la barra de herramientas
          // const toolbarElement = document.querySelector(id_contenido + ' .ck-toolbar');
          // toolbarElement.style.display = 'none';

      })
      .catch( error => {
          console.error(error);
      });
    }

    <?php for ($i=0; $i < count($resultado_registros_historico); $i++): ?>
      createAndToggleEditorView('grc_gestion_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>');
    <?php endfor; ?>
</script>