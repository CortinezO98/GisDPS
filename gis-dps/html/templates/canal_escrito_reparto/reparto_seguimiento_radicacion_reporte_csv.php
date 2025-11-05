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
        
        $titulo_reporte="Canal Escrito - Seguimiento Radicación - ".date('Y-m-d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `cesr_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `cesr_id`, `cesr_correo_ciudadano`, `cesr_fecha_ingreso_correo`, `cesr_dependencia`, `cesr_senotifica`, `cesr_observaciones`, `cesr_notificar`, `cesr_registro_usuario`, `cesr_registro_fecha`, DEPENDENCIA.`ceco_valor`, NOTIFICA.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_radicacion` LEFT JOIN `gestion_ce_configuracion` AS DEPENDENCIA ON `gestion_cerep_seguimiento_radicacion`.`cesr_dependencia`=DEPENDENCIA.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS NOTIFICA ON `gestion_cerep_seguimiento_radicacion`.`cesr_senotifica`=NOTIFICA.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_radicacion`.`cesr_registro_usuario`=TU.`usu_id` WHERE `cesr_registro_fecha`>=? AND `cesr_registro_fecha`<=? ".$filtro_perfil." ORDER BY `cesr_id`";

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
    fputcsv($file, array('Reporte: Canal Escrito - Seguimiento Radicación'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array('Correo Ciudadano', 'Fecha Ingreso Correo', 'Dependencia', 'Se Notifica', 'Observaciones', 'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $linea=array($resultado_registros[$i][1], $resultado_registros[$i][2], $resultado_registros[$i][9], $resultado_registros[$i][10], $resultado_registros[$i][5], $resultado_registros[$i][7], $resultado_registros[$i][11], $resultado_registros[$i][8]);
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