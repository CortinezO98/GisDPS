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
        
        $titulo_reporte="Canal Escrito - JAFocalización - Proyección de Peticiones Vivienda - ".date('Y_m_d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `cejpp_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `cejpp_id`, `cejpp_radicado_entrada`, `cejpp_proyector`, `cejpp_novedad_radicado`, `cejpp_formato`, `cejpp_identificacion_peticionario`, `cejpp_nombre_peticionario`, `cejpp_correo`, `cejpp_observaciones`, `cejpp_notificar`, `cejpp_registro_usuario`, `cejpp_registro_fecha`, NOVEDAD.`ceco_valor`, FORMATO.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_proyeccion_peticiones` LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_novedad_radicado`=NOVEDAD.`ceco_id`
           LEFT JOIN `gestion_ce_configuracion` AS FORMATO ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_formato`=FORMATO.`ceco_id`
           LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_proyector`=PROYECTOR.`usu_id`
           LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_registro_usuario`=TU.`usu_id` WHERE `cejpp_registro_fecha`>=? AND `cejpp_registro_fecha`<=? ".$filtro_perfil." ORDER BY `cejpp_id`";

        $consulta_registros = $enlace_db->prepare($consulta_string);
        if (count($data_consulta)>0) {
            // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
            $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            
        }
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
    }

    $delimitador = ';';
    $encapsulador = '"';
    $ruta='storage/'.$titulo_reporte;
    // create a file pointer connected to the output stream
    $file = fopen($ruta, 'w');
    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($file, array('Reporte: Canal Escrito JAFocalización - 1. Proyección de Peticiones Vivienda'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array('Radicado entrada', 
'Novedad Radicado', 
'Formato', 
'Número Identificación Peticionario', 
'Nombres y Apellidos Peticionario', 
'Correo/Dirección de Notificación', 
'Observaciones', 
'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $linea=array($resultado_registros[$i][1], $resultado_registros[$i][12], $resultado_registros[$i][13], $resultado_registros[$i][5], $resultado_registros[$i][6], $resultado_registros[$i][7], $resultado_registros[$i][8], $resultado_registros[$i][10], $resultado_registros[$i][15], $resultado_registros[$i][11]);
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