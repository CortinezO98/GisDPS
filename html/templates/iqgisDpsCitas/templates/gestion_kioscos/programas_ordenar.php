<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión Kioscos";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  $consulta_string="SELECT `gkp_id`, `gkp_titulo`, `gkp_imagen`, `gkp_estado`, `gkp_registro_usuario`, `gkp_registro_fecha`, TU.`usu_nombres_apellidos`, `gkp_orden` FROM `gestion_kioscos_programas` LEFT JOIN `administrador_usuario` AS TU ON `gestion_kioscos_programas`.`gkp_registro_usuario`=TU.`usu_id` WHERE 1=1 ORDER BY `gkp_orden`";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

?>
<div class="row px-2 justify-content-center">
    <div class="col-lg-12 col-md-12 py-2">
        <div id="mensaje"></div>
        <ul id="mi_lista">
            <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                <li id="miorden_<?php echo $resultado_registros[$i][0]; ?>"><p class="alert alert-dark font-size-11 px-1 py-0 my-1"><?php echo $resultado_registros[$i][7]; ?> | <?php echo $resultado_registros[$i][1]; ?></p></li>
            <?php endfor; ?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(function () {
            $("#mi_lista").sortable({update: function () {
                    var ordem_atual = $(this).sortable("serialize");
                    $.post("programas_ordenar_editar.php?id=", ordem_atual, function (retorno) {
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