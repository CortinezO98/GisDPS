<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Canal Escrito-Reparto";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    if(isset($_POST["reporte"])){
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        $titulo_reporte="Canal Escrito - Seguimiento Inspección Tipificación - ".date('Y-m-d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `cesit_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `cesit_id`, `cesit_radicado`, `cesit_abogado_tipificador`, `cesit_abogado_aprobador`, `cesit_traslado_entidades`, `cesit_traslado_entidades_errado`, `cesit_asignaciones_internas`, `cesit_forma_correcta_peticion`, `cesit_traslado_entidades_errado_senalar`, `cesit_asignacion_errada`, `cesit_asignacion_errada_2`, `cesit_observaciones_asignacion`, `cesit_relaciona_informacion_radicacion`, `cesit_campo_errado`, `cesit_diligencia_datos_solicitante`, `cesit_campo_errado_2`, `cesit_observaciones_diligencia_formulario`, `cesit_notificar`, `cesit_registro_usuario`, `cesit_registro_fecha`, abogado_tipificador.`usu_nombres_apellidos`, abogado_aprobador.`usu_nombres_apellidos`, traslado_entidades.`ceco_valor`, traslado_entidades_errado.`ceco_valor`, asignaciones_internas.`ceco_valor`, forma_correcta_peticion.`ceco_valor`, traslado_entidades_errado_senalar.`ceco_valor`, asignacion_errada.`ceco_valor`, asignacion_errada_2.`ceco_valor`, relaciona_informacion_radicacion.`ceco_valor`, campo_errado.`ceco_valor`, diligencia_datos_solicitante.`ceco_valor`, campo_errado_2.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_inspeccion_tipificacion` LEFT JOIN `administrador_usuario` AS abogado_tipificador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_tipificador`=abogado_tipificador.`usu_id`
             LEFT JOIN `administrador_usuario` AS abogado_aprobador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_aprobador`=abogado_aprobador.`usu_id`
             LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades`=traslado_entidades.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado`=traslado_entidades_errado.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS asignaciones_internas ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignaciones_internas`=asignaciones_internas.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS forma_correcta_peticion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_forma_correcta_peticion`=forma_correcta_peticion.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado_senalar ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado_senalar`=traslado_entidades_errado_senalar.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada`=asignacion_errada.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada_2`=asignacion_errada_2.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS relaciona_informacion_radicacion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_relaciona_informacion_radicacion`=relaciona_informacion_radicacion.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS campo_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado`=campo_errado.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS diligencia_datos_solicitante ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_diligencia_datos_solicitante`=diligencia_datos_solicitante.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS campo_errado_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado_2`=campo_errado_2.`ceco_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_registro_usuario`=TU.`usu_id` WHERE `cesit_registro_fecha`>=? AND `cesit_registro_fecha`<=? ".$filtro_perfil." ORDER BY `cesit_id`";

        $consulta_registros = $enlace_db->prepare($consulta_string);
        if (count($data_consulta)>0) {
            // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
            $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            
        }
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='seguimiento_inspeccion_tipificacion' ORDER BY `ceco_campo`, `ceco_valor`";
        $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
        $consulta_registros_parametros->execute();
        $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
            $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
        }
    }

    $delimitador = ';';
    $encapsulador = '"';
    $ruta='storage/'.$titulo_reporte;
    // create a file pointer connected to the output stream
    $file = fopen($ruta, 'w');
    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($file, array('Reporte: Canal Escrito - Seguimiento Inspección Tipificación'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array('No. radicado', 'Abogado tipificador', 'Abogado aprobador', '6. Traslados a otras entidades', '7. Traslado errado entidades', '7.1. Traslado errado entidades (Señalar la entidad)', '8. Asignaciones internas P.S', '8.1. Asignación P.S errada', '8.2. Asignación P.S errada', '9. Determina de forma correcta el tipo de petición ', '14. Relaciona de manera correcta los datos del campo información radicación', '15. Campo errado', '16. Diligencia de manera correcta los datos del solicitante', '17. Campo errado', '18. Observación (Aportes o recomendaciones evidenciados en el diligenciamiento del formulario de tipificación que permita adelantar los PDA)', 'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
          $cesit_asignacion_errada_2=explode(';', $resultado_registros[$i][10]);

          $cesit_asignacion_errada_2_mostrar='';
          for ($j=0; $j < count($cesit_asignacion_errada_2); $j++) { 
            if ($cesit_asignacion_errada_2[$j]!="") {
                $cesit_asignacion_errada_2_mostrar.=$array_parametros['asignacion_errada_2']['texto'][$cesit_asignacion_errada_2[$j]].', ';
            }
          }

          $cesit_campo_errado=explode(';', $resultado_registros[$i][13]);

          $cesit_campo_errado_mostrar='';
          for ($j=0; $j < count($cesit_campo_errado); $j++) { 
            if ($cesit_campo_errado[$j]) {
                $cesit_campo_errado_mostrar.=$array_parametros['campo_errado']['texto'][$cesit_campo_errado[$j]].', ';
            }
          }

          $cesit_campo_errado_2=explode(';', $resultado_registros[$i][15]);

          $cesit_campo_errado_2_mostrar='';
          for ($j=0; $j < count($cesit_campo_errado_2); $j++) { 
            if ($cesit_campo_errado_2[$j]) {
                $cesit_campo_errado_2_mostrar.=$array_parametros['campo_errado_2']['texto'][$cesit_campo_errado_2[$j]].', ';
            }
          }


        $linea=array($resultado_registros[$i][1], $resultado_registros[$i][20], $resultado_registros[$i][21], $resultado_registros[$i][22], $resultado_registros[$i][23], $resultado_registros[$i][8], $resultado_registros[$i][24], $resultado_registros[$i][27], $cesit_asignacion_errada_2_mostrar, $resultado_registros[$i][25], $resultado_registros[$i][29], $cesit_campo_errado_mostrar, $resultado_registros[$i][31], $cesit_campo_errado_2_mostrar, $resultado_registros[$i][16], $resultado_registros[$i][18], $resultado_registros[$i][33], $resultado_registros[$i][19]);
        fputcsv($file, $linea, $delimitador, $encapsulador);
    }
    rewind($file);

    fclose($file);

    header("Content-disposition: attachment; filename=".$titulo_reporte);
    header("Content-type: MIME");
    header('Cache-Control: max-age=0');
    readfile($ruta);
    unlink($ruta);

    //Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    // header('Content-Type: text/csv; charset=utf-8');
    // header('Content-Disposition: attachment; filename=HRdata.csv');
    // header('Cache-Control: max-age=0');
?>