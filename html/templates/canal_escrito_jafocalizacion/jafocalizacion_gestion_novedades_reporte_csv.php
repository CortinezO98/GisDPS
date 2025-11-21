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
        
        $titulo_reporte="Canal Escrito - JAFocalización - Formato Gestión de Novedades JeA - ".date('Y_m_d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `cejgn_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `cejgn_id`, `cejgn_id_novedad`, `cejgn_id_persona`, `cejgn_fecha_gestion`, `cejgn_estado`, `cejgn_tipo_rechazo`, `cejgn_observacion_rechazo`, `cejgn_correccion_datos_sija`, `cejgn_codigo_novedad`, `cejgn_observaciones`, `cejgn_notificar`, `cejgn_registro_usuario`, `cejgn_registro_fecha`, ESTADO.`ceco_valor`, TIPORECHAZO.`ceco_valor`, DATOSSIJA.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_gestion_novedades` 
   LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cejafo_gestion_novedades`.`cejgn_estado`=ESTADO.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS TIPORECHAZO ON `gestion_cejafo_gestion_novedades`.`cejgn_tipo_rechazo`=TIPORECHAZO.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS DATOSSIJA ON `gestion_cejafo_gestion_novedades`.`cejgn_correccion_datos_sija`=DATOSSIJA.`ceco_id`
   LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_novedades`.`cejgn_registro_usuario`=TU.`usu_id` WHERE `cejgn_registro_fecha`>=? AND `cejgn_registro_fecha`<=? ".$filtro_perfil." ORDER BY `cejgn_id`";

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
    fputcsv($file, array('Reporte: Canal Escrito JAFocalización - 5. Formato Gestión de Novedades JeA'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array(
'Id Novedad', 
'Id Persona', 
'Fecha Gestión', 
'Estado', 
'Tipo Rechazo', 
'Observación de Rechazo', 
'Corrección Datos SIJA', 
'Código de la Novedad', 
'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $linea=array($resultado_registros[$i][1], 
$resultado_registros[$i][2], 
$resultado_registros[$i][3], 
$resultado_registros[$i][13], 
$resultado_registros[$i][14], 
$resultado_registros[$i][6], 
$resultado_registros[$i][15], 
$resultado_registros[$i][8], 
$resultado_registros[$i][11], $resultado_registros[$i][16], $resultado_registros[$i][12]);
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