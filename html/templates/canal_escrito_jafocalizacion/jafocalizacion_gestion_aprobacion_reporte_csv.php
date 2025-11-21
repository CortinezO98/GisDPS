<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Canal Escrito-Jóvenes Acción-Focalización";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    if(isset($_POST["reporte"])){
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        $titulo_reporte="Canal Escrito - JAFocalización - Formato Gestión de Aprobación JeA - ".date('Y_m_d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `cejga_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `cejga_id`, `cejga_radicado_entrada`, `cejga_proyector`, `cejga_revisor`, `cejga_cedula_aprobador`, `cejga_gestion`, `cejga_oportunidad_mejora`, `cejga_comentario_delta`, `cejga_observaciones`, `cejga_notificar`, `cejga_registro_usuario`, `cejga_registro_fecha`, GESTION.`ceco_valor`, OPORTUNIDAD.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, REVISOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_gestion_aprobacion` 
   LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cejafo_gestion_aprobacion`.`cejga_gestion`=GESTION.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS OPORTUNIDAD ON `gestion_cejafo_gestion_aprobacion`.`cejga_oportunidad_mejora`=OPORTUNIDAD.`ceco_id`
   LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_gestion_aprobacion`.`cejga_proyector`=PROYECTOR.`usu_id`
   LEFT JOIN `administrador_usuario` AS REVISOR ON `gestion_cejafo_gestion_aprobacion`.`cejga_revisor`=REVISOR.`usu_id`
   LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_aprobacion`.`cejga_registro_usuario`=TU.`usu_id` WHERE `cejga_registro_fecha`>=? AND `cejga_registro_fecha`<=? ".$filtro_perfil." ORDER BY `cejga_id`";

        $consulta_registros = $enlace_db->prepare($consulta_string);
        if (count($data_consulta)>0) {
            // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
            $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            
        }
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='jafocalizacion_gestion_aprobacion' AND `ceco_estado`='Activo' ORDER BY `ceco_campo`, `ceco_valor`";
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
    fputcsv($file, array('Reporte: Canal Escrito JAFocalización - 7. Formato Gestión de Aprobación JeA'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array(
'Radicado Entrada', 
'Proyector', 
'Gestión', 
'Oportunidades de Mejora', 
'Comentario Delta', 
'Observaciones', 
'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $cejga_oportunidad_mejora=explode(';', $resultado_registros[$i][6]);

        $cejga_oportunidad_mejora_mostrar='';
        for ($j=0; $j < count($cejga_oportunidad_mejora); $j++) { 
          if ($cejga_oportunidad_mejora[$j]!="") {
            $cejga_oportunidad_mejora_mostrar.=$array_parametros['cejga_oportunidad_mejora']['texto'][$cejga_oportunidad_mejora[$j]].';';
          }
        }

        $linea=array($resultado_registros[$i][1], $resultado_registros[$i][14], $resultado_registros[$i][12], $cejga_oportunidad_mejora_mostrar, $resultado_registros[$i][7], $resultado_registros[$i][8], $resultado_registros[$i][10], $resultado_registros[$i][16], $resultado_registros[$i][11]);
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