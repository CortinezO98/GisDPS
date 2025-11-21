<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Envíos WEB";
    require_once("../../iniciador.php");

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    $consulta_string="SELECT `gewc_id`, `gewc_radicado`, `gewc_radicado_entrada`, `gewc_radicado_salida`, `gewc_tipologia`, `gewc_clasificacion`, `gewc_responsable`, `gewc_gestion`, `gewc_gestion_detalle`, `gewc_estado`, `gewc_fecha_gestion`, `gewc_correo_remitente`, `gewc_correo_asunto`, `gewc_correo_fecha`, `gewc_registro_fecha`, `gewc_registro_fecha_hora`, TAG.`usu_nombres_apellidos` FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos`.`gewc_responsable`=TAG.`usu_id` WHERE `gewc_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_historico="SELECT `gewch_id`, `gewch_radicado`, `gewch_radicado_id`, `gewch_tipo`, `gewch_gestion`, `gewch_gestion_detalle`, `gewch_anonimo`, `gewch_publicacion`, `gewch_correo_id`, `gewch_correo_de`, `gewch_correo_de_nombre`, `gewch_correo_para`, `gewch_correo_para_nombre`, `gewch_correo_cc`, `gewch_correo_bcc`, `gewch_correo_fecha`, `gewch_correo_asunto`, `gewch_correo_contenido`, `gewch_embeddedimage_ruta`, `gewch_embeddedimage_nombre`, `gewch_embeddedimage_tipo`, `gewch_attachment_ruta`, `gewch_intentos`, `gewch_estado_envio`, `gewch_fecha_envio`, `gewch_registro_usuario`, `gewch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom` FROM `gestion_enviosweb_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_enviosweb_casos_historial`.`gewch_correo_de`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos_historial`.`gewch_registro_usuario`=TAG.`usu_id` WHERE `gewch_radicado_id`=? AND `gewch_tipo`<>'Borrador' ORDER BY `gewch_id` DESC";

    $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
    $consulta_registros_historico->bind_param("s", $id_registro);
    $consulta_registros_historico->execute();
    $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);

    $array_estado_alert['Pendiente']='warning';
    $array_estado_alert['En trámite']='dark';
    $array_estado_alert['Finalizado']='success';

    $consulta_string_adjuntos="SELECT `gewca_id`, `gewca_historial_id`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_estado`, `gewca_radicado`, `gewca_radicado_id` FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_historial_id`=? AND `gewca_estado`='Activo' ORDER BY `gewca_id` ASC";
    $consulta_registros_adjuntos = $enlace_db->prepare($consulta_string_adjuntos);
    $consulta_registros_adjuntos->bind_param("s", $historial_id);
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
    <div class="row flex-grow">
      <div class="col-12 grid-margin stretch-card my-1">
        <div class="card card-rounded">
          <div class="card-body">
            <div class="row">
              <div class="">
                <div class="col-md-12">
                  <?php for ($i=0; $i < count($resultado_registros_historico); $i++): ?>
                    <textarea class="form-control form-control-sm" name="grc_gestion_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>" id="grc_gestion_contenido_<?php echo $resultado_registros_historico[$i][0]; ?>">
                      <?php if($resultado_registros_historico[$i][3]!='Borrador'): ?>
                        <?php
                          if ($resultado_registros_historico[$i][3]=='Radicado') {
                              $fecha_correo=$resultado_registros_historico[$i][15];
                          }

                          if ($resultado_registros_historico[$i][3]=='Gestión' AND $resultado_registros_historico[$i][24]!='') {
                              $fecha_correo=$resultado_registros_historico[$i][24];
                          }

                          $nombre_dia_final=$array_dias_nombre[intval(date("N", strtotime($fecha_correo)))];
                          $nombre_mes_final=$array_meses[intval(date("m", strtotime($fecha_correo)))];
                          //Se configura y convierte el formato de fecha de enviado
                          $fecha_enviado=$nombre_dia_final.", ".date("d", strtotime($fecha_correo))." de ".$nombre_mes_final." de ".date("Y H:i a", strtotime($fecha_correo));
                          $fecha_enviado_ultimo=$nombre_dia_final." ".date("d/m/Y", strtotime($fecha_correo))." ".date("H:i a", strtotime($fecha_correo));
                          
                          if ($resultado_registros_historico[$i][3]=='Gestión' AND $resultado_registros_historico[$i][24]=='') {
                              $fecha_enviado='Pendiente';
                              $fecha_enviado_ultimo='Pendiente';
                          }

                          if ($resultado_registros_historico[$i][29]!='') {
                            $remitente=$resultado_registros_historico[$i][29];
                          } else {
                            $remitente=$resultado_registros_historico[$i][9];
                          }

                          $detalle_correo_original='';
                          if ($i==0) {
                            $detalle_correo_original.='<span style="font-size:17px;"><b>'.$resultado_registros_historico[$i][16].'</b></span><br><br>';
                            $detalle_correo_original.='<span style="font-size:16px;">De: '.$remitente.'</span><br>';
                            $detalle_correo_original.='<span style="font-size:12px;">'.$fecha_enviado_ultimo.'</span><br>';

                            $detalle_correo_original.='<span style="font-size:14px;">Para: '.$resultado_registros_historico[$i][11].'</span><br>';
                          
                            if ($resultado_registros_historico[$i][13]!='') {
                              $detalle_correo_original.='<span style="font-size:14px;">CC: '.$resultado_registros_historico[$i][13].'</span><br>';
                            }

                            if ($resultado_registros_historico[$i][14]!='') {
                              $detalle_correo_original.='<span style="font-size:14px;">CCO: '.$resultado_registros_historico[$i][14].'</span><br>';
                            }

                          } else {
                            $detalle_correo_original.='<span style="font-size:14px;"><b>De:</b> '.$remitente.'</span><br>';
                            $detalle_correo_original.='<span style="font-size:14px;"><b>Enviado:</b> '.$fecha_enviado.'</span><br>';
                            $detalle_correo_original.='<span style="font-size:14px;"><b>Para:</b> '.$resultado_registros_historico[$i][11].'</span><br>';
                          
                            if ($resultado_registros_historico[$i][13]!='') {
                              $detalle_correo_original.='<span style="font-size:14px;"><b>Cc:</b> '.$resultado_registros_historico[$i][13].'</span><br>';
                            }
                            
                            if ($resultado_registros_historico[$i][14]!='') {
                              $detalle_correo_original.='<span style="font-size:14px;">CCO: '.$resultado_registros_historico[$i][14].'</span><br>';
                            }
                            
                            $detalle_correo_original.='<span style="font-size:14px;"><b>Asunto:</b> '.$resultado_registros_historico[$i][16].'</span><br><br><br>';
                          }

                          $historial_id=$resultado_registros_historico[$i][0];

                          
                          $consulta_registros_adjuntos->execute();
                          $resultado_registros_adjuntos = $consulta_registros_adjuntos->get_result()->fetch_all(MYSQLI_NUM);

                          $resultado_adjuntos='';
                          if (count($resultado_registros_adjuntos)>0) {
                              $resultado_adjuntos.='<br><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAAOCAYAAADjXQYbAAABhWlDQ1BJQ0MgcHJvZmlsZQAAKJF9kT1Iw1AUhU9TpUWrDnYQcchQneyiIo6likWwUNoKrTqYvPQPmjQkKS6OgmvBwZ/FqoOLs64OroIg+APi6uKk6CIl3pcUWsR44fE+zrvn8N59gNCsMtXsiQGqZhnpRFzM5VfFwCv64YMfgwhKzNSTmcUsPOvrnnqp7qI8y7vvzxpQCiYDfCJxjOmGRbxBPLtp6Zz3icOsLCnE58STBl2Q+JHrsstvnEsOCzwzbGTT88RhYrHUxXIXs7KhEs8QRxRVo3wh57LCeYuzWq2z9j35C0MFbSXDdVpjSGAJSaQgQkYdFVRhIUq7RoqJNJ3HPfyjjj9FLplcFTByLKAGFZLjB/+D37M1i9NTblIoDvS+2PbHOBDYBVoN2/4+tu3WCeB/Bq60jr/WBOY+SW90tMgRMLQNXFx3NHkPuNwBRp50yZAcyU9LKBaB9zP6pjwwfAv0rblza5/j9AHI0qyWb4CDQ2CiRNnrHu8Ods/t3572/H4AF8xyglM+M6QAAAAGYktHRAD/AP8A/6C9p5MAAAAJcEhZcwAADdcAAA3XAUIom3gAAAAHdElNRQfnCwEDDiJBXAXdAAAAz0lEQVQY083MIU4DURSF4XNu350CEoFAEzod2iVg2QEoJKKWNaDwJDU1GJKSkOC6C0gzJBBMDQkCg8BA37sHAwaB7mf/5GfTNJuV4d46th2lvBem/bZtnwDAEuNSiJe7+YPnghmVr/EjEdaLkscAwJwn5tXtoK4PYNoyECSo4V599UkGAII6MrMT+114Sof4w/CPlYqSZJJeAxhExNtGN82UY2Ed24G0SBH53L26+Sr5mMKaQuvuflGYRgSAYX/3NLmfkewW6QPL5Wj++Dz9Bu5tUtyvGsf6AAAAAElFTkSuQmCC"> <span style="font-size:12px;">'.count($resultado_registros_adjuntos).' archivos adjuntos<br>';
                              for ($j=0; $j < count($resultado_registros_adjuntos); $j++) { 

                                  $resultado_adjuntos.=''.$resultado_registros_adjuntos[$j][2].';';
                                      
                                  
                              }
                              $resultado_adjuntos.='</span><br><br>';
                          }
                        ?>
                        <?php echo $detalle_correo_original; ?>
                        <?php echo $resultado_adjuntos; ?>
                        <?php echo removeEmojis(nl2br($resultado_registros_historico[$i][17])); ?>
                      <?php endif; ?>
                    </textarea>
                  <?php endfor; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

<script type="text/javascript">
    // Función para crear el editor y habilitar o deshabilitar según el parámetro
    function createAndToggleEditorView(id_contenido) {
      CKEDITOR.ClassicEditor.create(document.getElementById(id_contenido), {
          // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
          toolbar: {
              items: [
                  // 'exportPDF','exportWord', '|',
                  'exportPDF', '|',
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
                  // 'undo', 'redo', '|',
                  // 'fontFamily', 'fontSize', 'bold', 'italic', 'underline', 'strikethrough', 'fontColor', 'highlight', '|',
                  // 'bulletedList', 'numberedList',
                  // 'outdent', 'indent', 'alignment', '|',
                  // 'subscript', 'superscript', '|',
                  // 'link', 'insertImage', 'blockQuote', 'insertTable', '|',
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
          exportPdf: {
              tokenUrl: '',
              stylesheets: [
                  // './path/to/fonts.css',
                  // 'EDITOR_STYLES',
                  // './path/to/style.css'
              ],
              fileName: '<?php echo $resultado_registros[0][1]; ?>.pdf',
              converterOptions: {
                  format: 'A4',
                  margin_top: '20mm',
                  margin_bottom: '20mm',
                  margin_right: '12mm',
                  margin_left: '12mm',
                  page_orientation: 'portrait'
              }
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