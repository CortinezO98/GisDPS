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
        
        $titulo_reporte="Canal Escrito - Firma Traslados - ".date('Y_m_d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `ceft_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `ceft_id`, `ceft_radicado_entrada`, `ceft_radicado_salida`, `ceft_rechazos`, `ceft_forma`, `ceft_proyector`, `ceft_inspector`, `ceft_aprobador`, `ceft_observaciones`, `ceft_notificar`, `ceft_registro_usuario`, `ceft_registro_fecha`, RECHAZOS.`ceco_valor`, FORMA.`ceco_valor`, TU.`usu_nombres_apellidos`, PROYECTOR.`usu_nombres_apellidos`, INSPECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos` FROM `gestion_cerep_firma_traslados` LEFT JOIN `gestion_ce_configuracion` AS RECHAZOS ON `gestion_cerep_firma_traslados`.`ceft_rechazos`=RECHAZOS.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS FORMA ON `gestion_cerep_firma_traslados`.`ceft_forma`=FORMA.`ceco_id` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_firma_traslados`.`ceft_proyector`=PROYECTOR.`usu_id` LEFT JOIN `administrador_usuario` AS INSPECTOR ON `gestion_cerep_firma_traslados`.`ceft_inspector`=INSPECTOR.`usu_id` LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cerep_firma_traslados`.`ceft_aprobador`=APROBADOR.`usu_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_firma_traslados`.`ceft_registro_usuario`=TU.`usu_id` WHERE `ceft_registro_fecha`>=? AND `ceft_registro_fecha`<=? ".$filtro_perfil." ORDER BY `ceft_id`";

        $consulta_registros = $enlace_db->prepare($consulta_string);
        if (count($data_consulta)>0) {
            // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
            $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            
        }
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='firma_traslados' ORDER BY `ceco_campo`, `ceco_valor`";
        $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
        $consulta_registros_parametros->execute();
        $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
            $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
        }

        $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_campania` FROM `administrador_usuario` LEFT JOIN `administrador_campania` AS TC ON `administrador_usuario`.`usu_campania`=TC.`ac_id` WHERE 1=1 AND TC.`ac_nombre_campania`='Canal Escrito' ORDER BY `usu_nombres_apellidos`";
        $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
        $consulta_registros_analistas->execute();
        $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_analistas); $i++) { 
        $array_analista[$resultado_registros_analistas[$i][0]]=$resultado_registros_analistas[$i][1];
        }
    }

    $delimitador = ';';
    $encapsulador = '"'; 
    $ruta='storage/'.$titulo_reporte;
    // create a file pointer connected to the output stream
    $file = fopen($ruta, 'w');
    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($file, array('Reporte: Canal Escrito - Firma Traslados'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array('Radicado Entrada o ERP', 'Radicado Salida', 'Rechazos', 'Forma', 'Proyector', 'Inspector', 'Abogado Aprobador', 'Observaciones', 'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $ceft_forma=explode(';', $resultado_registros[$i][4]);

        $ceft_forma_mostrar='';
        for ($j=0; $j < count($ceft_forma); $j++) { 
            if ($ceft_forma[$j]!="") {
                $ceft_forma_mostrar.=$array_parametros['forma']['texto'][$ceft_forma[$j]].',';
            }
        }


        $linea=array($resultado_registros[$i][1], $resultado_registros[$i][2], $resultado_registros[$i][12], $ceft_forma_mostrar, $array_analista[$resultado_registros[$i][5]], $array_analista[$resultado_registros[$i][6]], $array_analista[$resultado_registros[$i][7]], $resultado_registros[$i][8], $resultado_registros[$i][10], $resultado_registros[$i][14], $resultado_registros[$i][11]);
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