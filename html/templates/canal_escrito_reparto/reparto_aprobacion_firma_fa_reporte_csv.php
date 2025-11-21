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
        
        $titulo_reporte="Canal Escrito - Aprobación Firma FA - ".date('Y_m_d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `ceaff_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `ceaff_id`, `ceaff_radicado`, `ceaff_proyector`, `ceaff_estado`, `ceaff_observaciones`, `ceaff_notificar`, `ceaff_registro_usuario`, `ceaff_registro_fecha`, PROYECTOR.`usu_nombres_apellidos`, ESTADO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_aprobacion_firma_fa` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_proyector`=PROYECTOR.`usu_id` LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_estado`=ESTADO.`ceco_id`  LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_registro_usuario`=TU.`usu_id` WHERE `ceaff_registro_fecha`>=? AND `ceaff_registro_fecha`<=? ".$filtro_perfil." ORDER BY `ceaff_id`";

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
    fputcsv($file, array('Reporte: Canal Escrito - Aprobación Firma FA'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array('Radicado', 'Proyector', 'Estado', 'Observaciones', 'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $linea=array($resultado_registros[$i][1], $resultado_registros[$i][8], $resultado_registros[$i][9], $resultado_registros[$i][4], $resultado_registros[$i][6], $resultado_registros[$i][10], $resultado_registros[$i][7]);
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