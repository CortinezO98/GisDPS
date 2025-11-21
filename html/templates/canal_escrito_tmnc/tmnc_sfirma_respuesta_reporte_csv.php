<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Canal Escrito-TMNC";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    if(isset($_POST["reporte"])){
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        $titulo_reporte="Canal Escrito - TMNC - 5. Firma Respuesta - ".date('Y_m_d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `cetfr_usuario_registro`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `cetfr_id`, `cetfr_fecha_firma`, `cetfr_modulo`, `cetfr_git`, `cetfr_radicado_entrada`, `cetfr_radicado_salida`, `cetfr_aprobador`, `cetfr_responsable_firma`, `cetfr_observaciones`, `cetfr_notificar`, `cetfr_usuario_registro`, `cetfr_usuario_fecha`, MODULO.`ceco_valor`, GIT.`ceco_valor`, APROBADOR.`ceco_valor`, RESPONSABLEFIRMA.`ceco_valor`, TU.`usu_nombres_apellidos`, `cetfr_proyector`, PROYECTOR.`ceco_valor` FROM `gestion_cetmnc_firma_respuesta`
   LEFT JOIN `gestion_ce_configuracion` AS MODULO ON `gestion_cetmnc_firma_respuesta`.`cetfr_modulo`=MODULO.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS GIT ON `gestion_cetmnc_firma_respuesta`.`cetfr_git`=GIT.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PROYECTOR ON `gestion_cetmnc_firma_respuesta`.`cetfr_proyector`=PROYECTOR.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS APROBADOR ON `gestion_cetmnc_firma_respuesta`.`cetfr_aprobador`=APROBADOR.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEFIRMA ON `gestion_cetmnc_firma_respuesta`.`cetfr_responsable_firma`=RESPONSABLEFIRMA.`ceco_id`
   LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_firma_respuesta`.`cetfr_usuario_registro`=TU.`usu_id` WHERE `cetfr_usuario_fecha`>=? AND `cetfr_usuario_fecha`<=? ".$filtro_perfil." ORDER BY `cetfr_id`";

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
    fputcsv($file, array('Reporte: Canal Escrito TMNC - 5. Firma Respuesta'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array(
'Fecha de firma', 
'Módulo', 
'GIT', 
'Radicado entrada', 
'Radicado salida', 
'Proyector', 
'Aprobador', 
'Observaciones', 
        'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $linea=array(
            $resultado_registros[$i][1], 
            $resultado_registros[$i][12], 
            $resultado_registros[$i][13], 
            $resultado_registros[$i][4], 
            $resultado_registros[$i][5], 
            $resultado_registros[$i][18], 
            $resultado_registros[$i][14], 
            $resultado_registros[$i][8], 
$resultado_registros[$i][10], $resultado_registros[$i][16], $resultado_registros[$i][11]);
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