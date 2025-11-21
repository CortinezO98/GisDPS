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
        
        $titulo_reporte="Canal Escrito - TMNC - 8. Aprobación Novedades CM - ".date('Y_m_d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `cetan_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `cetan_id`, `cetan_cod_beneficiario`, `cetan_tipo_documento`, `cetan_documento`, `cetan_nombres_apellidos`, `cetan_tipo_novedad`, `cetan_datos_basicos`, `cetan_suspension`, `cetan_reactivacion`, `cetan_retiro`, `cetan_gestion`, `cetan_tipo_rechazo`, `cetan_realizo_cambio_datos`, `cetan_correccion_datos`, `cetan_observaciones`, `cetan_notificar`, `cetan_registro_usuario`, `cetan_registro_fecha`, TIPODOCUMENTO.`ceco_valor`, TIPONOVEDAD.`ceco_valor`, DATOSBASICOS.`ceco_valor`, SUSPENSION.`ceco_valor`, REACTIVACION.`ceco_valor`, RETIRO.`ceco_valor`, GESTION.`ceco_valor`, TIPORECHAZO.`ceco_valor`, CAMBIODATOS.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_aprobacion_novedades`
     LEFT JOIN `gestion_ce_configuracion` AS TIPODOCUMENTO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_documento`=TIPODOCUMENTO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS TIPONOVEDAD ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_novedad`=TIPONOVEDAD.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS DATOSBASICOS ON `gestion_cetmnc_aprobacion_novedades`.`cetan_datos_basicos`=DATOSBASICOS.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS SUSPENSION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_suspension`=SUSPENSION.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS REACTIVACION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_reactivacion`=REACTIVACION.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS RETIRO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_retiro`=RETIRO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_gestion`=GESTION.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS TIPORECHAZO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_rechazo`=TIPORECHAZO.`ceco_id`
     LEFT JOIN `gestion_ce_configuracion` AS CAMBIODATOS ON `gestion_cetmnc_aprobacion_novedades`.`cetan_realizo_cambio_datos`=CAMBIODATOS.`ceco_id`
     LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_aprobacion_novedades`.`cetan_registro_usuario`=TU.`usu_id` WHERE `cetan_registro_fecha`>=? AND `cetan_registro_fecha`<=? ".$filtro_perfil." ORDER BY `cetan_id`";

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
    fputcsv($file, array('Reporte: Canal Escrito TMNC - 8. Aprobación Novedades CM'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array('Cod. beneficiario', 
'Tipo documento', 
'Documento', 
'Nombres y apellidos', 
'Tipo novedad', 
'Datos básicos', 
'Suspensión', 
'Reactivación', 
'Retiro', 
'Gestión', 
'Tipo rechazo', 
'Realizó cambio de datos', 
'Corrección de datos', 
'Observaciones', 'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $linea=array($resultado_registros[$i][1], 
$resultado_registros[$i][18], 
$resultado_registros[$i][3], 
$resultado_registros[$i][4], 
$resultado_registros[$i][19], 
$resultado_registros[$i][20], 
$resultado_registros[$i][21], 
$resultado_registros[$i][22], 
$resultado_registros[$i][23], 
$resultado_registros[$i][24], 
$resultado_registros[$i][25], 
$resultado_registros[$i][26], 
$resultado_registros[$i][13], 
$resultado_registros[$i][14], $resultado_registros[$i][16], $resultado_registros[$i][27], $resultado_registros[$i][17]);
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