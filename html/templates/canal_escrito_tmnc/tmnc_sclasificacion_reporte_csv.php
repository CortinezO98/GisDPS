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
        
        $titulo_reporte="Canal Escrito - TMNC - 3. Clasificación - ".date('Y_m_d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `cetc_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `cetc_id`, `cetc_correo_electronico`, `cetc_fecha_ingreso_correo`, `cetc_nombre_ciudadano`, `cetc_cedula_consulta`, `cetc_asunto_correo`, `cetc_programa_solicitud`, `cetc_plantilla_utilizada`, `cetc_solicitud_ciudadano`, `cetc_plantilla_datos_incompletos`, `cetc_plantilla_datos_completos`, `cetc_parrafo_radicacion`, `cetc_parrafo_plantilla_1`, `cetc_parrafo_plantilla_4`, `cetc_parrafo_plantilla_5`, `cetc_parrafo_plantilla_6`, `cetc_situacion_plantilla_8`, `cetc_parrafo_plantilla_8`, `cetc_parrafo_plantilla_10`, `cetc_titular_hogar`, `cetc_parrafo_plantilla_14`, `cetc_parrafo_plantilla_16`, `cetc_situacion_plantilla_17`, `cetc_parrafo_plantilla_17`, `cetc_situacion_plantilla_18`, `cetc_parrafo_plantilla_18`, `cetc_parrafo_plantilla_20`, `cetc_nombre_solicitante`, `cetc_nombre_titular`, `cetc_parrafo_plantilla_21`, `cetc_situacion_plantilla_22`, `cetc_parrafo_plantilla_22`, `cetc_parrafo_plantilla_23`, `cetc_parrafo_plantilla_25`, `cetc_parrafo_plantilla_26`, `cetc_parrafo_plantilla_reemplazo`, `cetc_motivo_devolucion`, `cetc_observaciones`, `cetc_notificar`, `cetc_registro_usuario`, `cetc_registro_fecha`, PROGRAMASOLICITUD.`ceco_valor`, PUTILIZADA.`ceco_valor`, PDATOSINCOMPLETOS.`ceco_valor`, PDATOSCOMPLETOS.`ceco_valor`, PLANTILLA8.`ceco_valor`, PLANTILLA17.`ceco_valor`, PLANTILLA18.`ceco_valor`, PLANTILLA22.`ceco_valor`, MOTIVODEVOLUCION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_clasificacion`
   LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_clasificacion`.`cetc_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PUTILIZADA ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_utilizada`=PUTILIZADA.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PDATOSINCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_incompletos`=PDATOSINCOMPLETOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PDATOSCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_completos`=PDATOSCOMPLETOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA8 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_8`=PLANTILLA8.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_17`=PLANTILLA17.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_18`=PLANTILLA18.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA22 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_22`=PLANTILLA22.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS MOTIVODEVOLUCION ON `gestion_cetmnc_clasificacion`.`cetc_motivo_devolucion`=MOTIVODEVOLUCION.`ceco_id`
   LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_clasificacion`.`cetc_registro_usuario`=TU.`usu_id` WHERE `cetc_registro_fecha`>=? AND `cetc_registro_fecha`<=? ".$filtro_perfil." ORDER BY `cetc_id`";

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
    fputcsv($file, array('Reporte: Canal Escrito TMNC - 3. Clasificación'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array(
'Correo electrónico', 
'Fecha ingreso correo', 
'Nombre del Ciudadano', 
'Cédula a consultar', 
'Asunto del correo', 
'Programa al que eleva la solicitud', 
'Plantilla utilizada', 
'Solicitud del ciudadano', 
'Plantilla datos incompletos', 
'Plantilla datos completos', 
'Párrafo en proceso de radicación o respuesta', 
'Párrafo plantilla 6', 
'Situación plantilla 8', 
'Párrafo plantilla 8', 
'Párrafo plantilla 10', 
'Titular del hogar', 
'Párrafo plantilla 14', 
'Párrafo plantilla 16', 
'Situación plantilla 17', 
'Párrafo plantilla 17', 
'Situación plantilla 18', 
'Párrafo plantilla 18', 
'Párrafo plantilla 20', 
'Párrafo plantilla 21', 
'Situación plantilla 22', 
'Párrafo plantilla 22', 
'Párrafo plantilla 23', 
'Párrafo plantilla 25', 
'Párrafo plantilla 26', 
'Párrafo plantilla reemplazo', 
'Motivo devolución correo', 
'Observaciones', 
'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $linea=array($resultado_registros[$i][1], 
$resultado_registros[$i][2], 
$resultado_registros[$i][3], 
$resultado_registros[$i][4], 
$resultado_registros[$i][5], 
$resultado_registros[$i][41], 
$resultado_registros[$i][42], 
$resultado_registros[$i][8], 
$resultado_registros[$i][43], 
$resultado_registros[$i][44], 
$resultado_registros[$i][11], 
$resultado_registros[$i][15], 
$resultado_registros[$i][45], 
$resultado_registros[$i][17], 
$resultado_registros[$i][18], 
$resultado_registros[$i][19], 
$resultado_registros[$i][20], 
$resultado_registros[$i][21], 
$resultado_registros[$i][46], 
$resultado_registros[$i][23], 
$resultado_registros[$i][47], 
$resultado_registros[$i][25], 
$resultado_registros[$i][26], 
$resultado_registros[$i][29], 
$resultado_registros[$i][48], 
$resultado_registros[$i][31], 
$resultado_registros[$i][32], 
$resultado_registros[$i][33], 
$resultado_registros[$i][34], 
$resultado_registros[$i][35], 
$resultado_registros[$i][49], 
$resultado_registros[$i][37], 
            $resultado_registros[$i][39], $resultado_registros[$i][50], $resultado_registros[$i][40]);
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