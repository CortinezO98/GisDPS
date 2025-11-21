<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Gestión OCR-Gestión";
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    if(isset($_POST["reporte"])){
        $tipo_reporte=validar_input($_POST['tipo_reporte']);
        $estado_reporte=$_POST['estado_reporte'];
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        if (!isset($estado_reporte)) {
            $estado_reporte=array();
        }

        $titulo_reporte="Gestión OCR -".$tipo_reporte.' - '.date('Y-m-d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);
        $filtro_buscar_estado="";

        if (count($estado_reporte)>0) {
            if ($tipo_reporte=='Consolidado Gestión') {
                //Agregar catidad de variables a filtrar a data consulta
                for ($i=0; $i < count($estado_reporte); $i++) { 
                  $filtro_buscar_estado.="`ocrr_gestion_estado`=? OR ";
                  array_push($data_consulta, $estado_reporte[$i]);
                }

                $filtro_buscar_estado=" AND (".substr($filtro_buscar_estado, 0, -4).")";
            } elseif ($tipo_reporte=='Notificaciones Correo') {
                //Agregar catidad de variables a filtrar a data consulta
                for ($i=0; $i < count($estado_reporte); $i++) { 
                  $filtro_buscar_estado.="`ocrr_gestion_estado`=? OR ";
                  array_push($data_consulta, $estado_reporte[$i]);
                }

                $filtro_buscar_estado=" AND (".substr($filtro_buscar_estado, 0, -4).")";
            } elseif ($tipo_reporte=='Notificaciones SMS') {
                
            }
        }

        if ($tipo_reporte=='Consolidado Gestión') {
            $consulta_string="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_observaciones`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TAG.`usu_nombres_apellidos`, TOCR.`ocr_correo`, TOCR.`ocr_telefono`, TOCR.`ocr_celular`, `ocrr_gestion_fecha`, `ocrr_gestion_llamada_tipificacion`, `ocrr_gestion_llamada_id`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio`, TCIU.`ciu_municipio`, TCIU.`ciu_departamento` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` LEFT JOIN `administrador_ciudades_dane` AS TCIU ON TOCR.`ocr_cod_municipio`=TCIU.`ciu_cod_municipio` WHERE 1=1 AND `ocrr_gestion_fecha`>=? AND `ocrr_gestion_fecha`<=? ".$filtro_buscar_estado." ORDER BY `ocrr_cod_familia` ASC";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            if (count($data_consulta)>0) {
                // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
                $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                
            }
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
        } elseif ($tipo_reporte=='Notificaciones Correo') {
            $consulta_string="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_observaciones`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TAG.`usu_nombres_apellidos`, TOCR.`ocr_correo`, TOCR.`ocr_telefono`, TOCR.`ocr_celular`, `ocrr_gestion_fecha`, `ocrr_gestion_llamada_tipificacion`, `ocrr_gestion_llamada_id` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` WHERE `ocrr_gestion_notificacion`='Si' AND `ocrr_gestion_fecha`>=? AND `ocrr_gestion_fecha`<=? ".$filtro_buscar_estado." ORDER BY `ocrr_cod_familia` ASC";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            if (count($data_consulta)>0) {
                // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
                $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
        } elseif ($tipo_reporte=='Notificaciones SMS') {
            $consulta_string="SELECT `nsms_id`, `nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`, `nsms_fecha_registro`, TCON.`ocrc_cod_familia`, TCON.`ocrc_codbeneficiario`, TCON.`ocrc_cabezafamilia`, TUR.`usu_nombres_apellidos`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_telefono`, TOCR.`ocr_celular` FROM `administrador_notificaciones_sms` LEFT JOIN `gestion_ocr_consolidado` AS TCON ON `administrador_notificaciones_sms`.`nsms_identificador`=TCON.`ocrc_id` LEFT JOIN `administrador_usuario` AS TUR ON `administrador_notificaciones_sms`.`nsms_usuario_registro`=TUR.`usu_id` LEFT JOIN `gestion_ocr` AS TOCR ON TCON.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `nsms_id_modulo`='11' AND `nsms_fecha_registro`>=? AND `nsms_fecha_registro`<=? ORDER BY `nsms_fecha_registro` ASC";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            if (count($data_consulta)>0) {
                // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
                $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
        }
    }
    
    $delimitador = ';';
    $encapsulador = '"';
    $ruta='storage/'.$titulo_reporte;
    // create a file pointer connected to the output stream
    $file = fopen($ruta, 'w');
    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

    if ($tipo_reporte=='Consolidado Gestión') {
        fputcsv($file, array('Reporte: Gestión OCR'), $delimitador, $encapsulador);
        fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
        
        $titulos=array('Estado Gestión', 
            'Intentos', 
            'Cód. Familia', 
            'Cód. Beneficiario', 
            'Cabeza Familia', 
            'Primer Nombre', 
            'Segundo Nombre', 
            'Primer Apellido', 
            'Segundo Apellido', 
            'Documento', 
            'Fecha Nacimiento', 
            'Género', 
            'Fecha Expedición', 
            'Celular', 
            'Teléfono', 
            'Correo', 
            'Municipio', 
            'Departamento', 
            'Notificación', 
            'Notificación Fecha Registro', 
            'Responsable', 
            'Observaciones', 
            'Fecha Gestión', 
            'Tipificación Llamada', 
            'Id Llamada');

        fputcsv($file, $titulos, $delimitador, $encapsulador);

        for ($i=0; $i < count($resultado_registros); $i++) {
            $linea=array($resultado_registros[$i][6], $resultado_registros[$i][7], $resultado_registros[$i][1], $resultado_registros[$i][2], $resultado_registros[$i][3], $resultado_registros[$i][14], $resultado_registros[$i][15], $resultado_registros[$i][16], $resultado_registros[$i][17], $resultado_registros[$i][18], $resultado_registros[$i][19], $resultado_registros[$i][20], $resultado_registros[$i][21], $resultado_registros[$i][25], $resultado_registros[$i][24], $resultado_registros[$i][23], $resultado_registros[$i][31], $resultado_registros[$i][32], $resultado_registros[$i][9], $resultado_registros[$i][11], $resultado_registros[$i][22], $resultado_registros[$i][8], $resultado_registros[$i][26], $resultado_registros[$i][27], $resultado_registros[$i][28]);
            fputcsv($file, $linea, $delimitador, $encapsulador);
        }
    } elseif ($tipo_reporte=='Notificaciones Correo') {
        fputcsv($file, array('Reporte: Gestión OCR - Notificación Correo'), $delimitador, $encapsulador);
        fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
        
        $titulos=array('Estado Gestión', 
            'Cód. Familia', 
            'Cód. Beneficiario', 
            'Cabeza Familia', 
            'Primer Nombre', 
            'Segundo Nombre', 
            'Primer Apellido', 
            'Segundo Apellido', 
            'Documento', 
            'Correo', 
            'Notificación', 
            'Notificación Fecha Registro', 
            'Responsable', 
            'Observaciones');

        fputcsv($file, $titulos, $delimitador, $encapsulador);

        for ($i=0; $i < count($resultado_registros); $i++) {
            $linea=array($resultado_registros[$i][6], $resultado_registros[$i][1], $resultado_registros[$i][2], $resultado_registros[$i][3], $resultado_registros[$i][14], $resultado_registros[$i][15], $resultado_registros[$i][16], $resultado_registros[$i][17], $resultado_registros[$i][18], $resultado_registros[$i][23], $resultado_registros[$i][9], $resultado_registros[$i][11], $resultado_registros[$i][22], $resultado_registros[$i][8]);
            fputcsv($file, $linea, $delimitador, $encapsulador);
        }
    } elseif ($tipo_reporte=='Notificaciones SMS') {
        fputcsv($file, array('Reporte: Gestión OCR - Notificación SMS'), $delimitador, $encapsulador);
        fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
        
        $titulos=array('Cód. Familia', 
            'Cód. Beneficiario', 
            'Cabeza Familia', 
            'Primer Nombre', 
            'Segundo Nombre', 
            'Primer Apellido', 
            'Segundo Apellido', 
            'Documento', 
            'Celular', 
            'Teléfono', 
            'Notificación SMS Destino', 
            'Notificación SMS Mensaje', 
            'Notificación SMS Url', 
            'Notificación SMS Estado Envío', 
            'Notificación SMS Fecha Envío', 
            'Notificación SMS Observaciones', 
            'Notificación SMS Fecha Registro', 
            'Notificación SMS Usuario Registro');

        fputcsv($file, $titulos, $delimitador, $encapsulador);

        for ($i=0; $i < count($resultado_registros); $i++) {
            $linea=array($resultado_registros[$i][14], $resultado_registros[$i][15], $resultado_registros[$i][16], $resultado_registros[$i][18], $resultado_registros[$i][19], $resultado_registros[$i][20], $resultado_registros[$i][21], $resultado_registros[$i][22], $resultado_registros[$i][24], $resultado_registros[$i][23], $resultado_registros[$i][5], $resultado_registros[$i][6], $resultado_registros[$i][7], $resultado_registros[$i][10], $resultado_registros[$i][11], $resultado_registros[$i][9], $resultado_registros[$i][13], $resultado_registros[$i][17]);
            fputcsv($file, $linea, $delimitador, $encapsulador);
        }
    }


    rewind($file);

    fclose($file);

    header("Content-disposition: attachment; filename=".$titulo_reporte);
    header("Content-type: MIME");
    header('Cache-Control: max-age=0');
    readfile($ruta);
    unlink($ruta)

    //Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    // header('Content-Type: text/csv; charset=utf-8');
    // header('Content-Disposition: attachment; filename=HRdata.csv');
    // header('Cache-Control: max-age=0');
?>