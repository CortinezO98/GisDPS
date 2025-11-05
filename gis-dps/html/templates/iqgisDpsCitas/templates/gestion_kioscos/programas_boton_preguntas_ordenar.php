<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  $id_boton=validar_input(base64_decode($_GET['id']));

  $consulta_string="SELECT `gkbp_id`, `gkbp_programa`, `gkbp_boton`, `gkbp_orden`, `gkbp_pregunta`, `gkbp_respuesta`, `gkbp_palabras_claves`, `gkbp_estado`, `gkbp_actualiza_usuario`, `gkbp_actualiza_fecha`, `gkbp_registro_usuario`, `gkbp_registro_fecha`, TP.`gkp_titulo`, TB.`gkpb_nombre`, TU.`usu_nombres_apellidos` FROM `gestion_kioscos_preguntas` LEFT JOIN `gestion_kioscos_programas` AS TP ON `gestion_kioscos_preguntas`.`gkbp_programa`=TP.`gkp_id` LEFT JOIN `gestion_kioscos_programas_boton` AS TB ON `gestion_kioscos_preguntas`.`gkbp_boton`=TB.`gkpb_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_preguntas`.`gkbp_registro_usuario`=TU.`usu_id` WHERE `gkbp_boton`=? ORDER BY `gkbp_orden`";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param('s', $id_boton);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

?>
<div class="row px-2 justify-content-center">
    <div class="col-lg-12 col-md-12 py-2">
        <div id="mensaje"></div>
        <ul id="mi_lista">
            <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                <li id="miorden_<?php echo $resultado_registros[$i][0]; ?>"><p class="alert alert-dark font-size-11 px-1 py-0 my-1"><?php echo $resultado_registros[$i][3]; ?> | <?php echo $resultado_registros[$i][4]; ?></p></li>
            <?php endfor; ?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(function () {
            $("#mi_lista").sortable({update: function () {
                    var ordem_atual = $(this).sortable("serialize");
                    $.post("programas_boton_preguntas_ordenar_editar.php?id=<?php echo base64_encode($id_boton); ?>", ordem_atual, function (retorno) {
                        //Imprimir resultado 
                        $("#mensaje").html(retorno);
                        //Muestra mensaje
                        $("#mensaje").slideDown('slow');
                        RetirarMensaje();
                    });
                }
            });
        });
                    
        // Elimina mensajes despues de un determiando periodo de tiempo 1900 milissegundos
        function RetirarMensaje(){
                setTimeout( function (){
                    $("#mensaje").slideUp('slow', function(){});
                }, 3000);
            }
        }
    );
</script>