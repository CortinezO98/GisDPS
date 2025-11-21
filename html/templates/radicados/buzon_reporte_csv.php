<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Radicación";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    if(isset($_POST["reporte"])){
        $tipo_reporte=validar_input($_POST['tipo_reporte']);
        $tipologia=$_POST['tipologia'];
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        $titulo_reporte="Gestión Radicados -".$tipo_reporte.' - '.date('Y-m-d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);
        $data_consulta_inicio=array();
        array_push($data_consulta_inicio, $fecha_inicio);

        if($tipologia=="Prioritarios"){
          $filtro_tipologia=" AND `grc_tipologia`=?";
          array_push($data_consulta, 'Prioritario');
        } elseif($tipologia=="Funcionarios"){
          $filtro_tipologia=" AND `grc_tipologia`=?";
          array_push($data_consulta, 'Funcionarios');
        } elseif($tipologia=="Ciudadanos"){
          $filtro_tipologia=" AND `grc_tipologia`=?";
          array_push($data_consulta, 'Ciudadanos');
        } elseif($tipologia=="Envío Radicado"){
          $filtro_tipologia=" AND `grc_tipologia`=?";
          array_push($data_consulta, 'Envío Radicado a Ciudadano');
        } elseif($tipologia=="Tutelas"){
          $filtro_tipologia=" AND `grc_tipologia`=?";
          array_push($data_consulta, 'Tutelas');
        } elseif($tipologia=="Notificaciones Correo"){
          $filtro_tipologia=" AND `grc_tipologia`=?";
          array_push($data_consulta, 'Notificaciones de correo');
        } elseif($tipologia=="Todos"){
          $filtro_tipologia=" AND `grc_tipologia`<>? AND `grc_tipologia`<>?";
          array_push($data_consulta, 'Notificaciones de correo');
          array_push($data_consulta, 'Envío Radicado a Ciudadano');
        }



        if ($tipo_reporte=='Consolidado Gestión') {
            $consulta_string="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`, `grc_registro_fecha_hora` FROM `gestion_radicacion_casos` WHERE 1=1 AND `grc_correo_fecha`>=? AND `grc_correo_fecha`<=? ".$filtro_tipologia." ORDER BY `grc_id` ASC";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            if (count($data_consulta)>0) {
                // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
                $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                
            }
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);


            //CONSULTA LOS ID MAXIMOS DE CADA RADICADO EN ESTADO GESTIÓN
            $consulta_string_maximo="SELECT `grch_radicado_id`, MAX(`grch_id`) AS idmax FROM `gestion_radicacion_casos_historial` WHERE `grch_tipo`='Gestión' AND `grch_registro_fecha`>=? GROUP BY `grch_radicado_id`";
            $consulta_registros_maximo = $enlace_db->prepare($consulta_string_maximo);
            if (count($data_consulta_inicio)>0) {
                // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta_inicio en el orden específico de los parámetros de la sentencia preparada
                $consulta_registros_maximo->bind_param(str_repeat("s", count($data_consulta_inicio)), ...$data_consulta_inicio);
                
            }
            $consulta_registros_maximo->execute();
            $resultado_registros_maximo = $consulta_registros_maximo->get_result()->fetch_all(MYSQLI_NUM);

            for ($i=0; $i < count($resultado_registros_maximo); $i++) { 
                $array_maximo[$resultado_registros_maximo[$i][0]]=$resultado_registros_maximo[$i][1];
            }

            //CONSULTA EL HISTÓRICO TIPO GESTIÓN DE CADA RADICADO
            $consulta_string_historial="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_de`, `grch_correo_de_nombre`, `grch_correo_para`, `grch_correo_para_nombre`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha` FROM `gestion_radicacion_casos_historial` WHERE `grch_tipo`='Gestión' AND `grch_registro_fecha`>=?";
            $consulta_registros_historial = $enlace_db->prepare($consulta_string_historial);
            if (count($data_consulta_inicio)>0) {
                // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta_inicio en el orden específico de los parámetros de la sentencia preparada
                $consulta_registros_historial->bind_param(str_repeat("s", count($data_consulta_inicio)), ...$data_consulta_inicio);
                
            }
            $consulta_registros_historial->execute();
            $resultado_registros_historial = $consulta_registros_historial->get_result()->fetch_all(MYSQLI_NUM);

            for ($i=0; $i < count($resultado_registros_historial); $i++) { 
                $array_historial[$resultado_registros_historial[$i][0]]['grch_radicado']=$resultado_registros_historial[$i][1];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_radicado_id']=$resultado_registros_historial[$i][2];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_tipo']=$resultado_registros_historial[$i][3];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_tipologia']=$resultado_registros_historial[$i][4];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_clasificacion']=$resultado_registros_historial[$i][5];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_gestion']=$resultado_registros_historial[$i][6];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_gestion_detalle']=$resultado_registros_historial[$i][7];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_duplicado']=$resultado_registros_historial[$i][8];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_unificado']=$resultado_registros_historial[$i][9];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_unificado_id']=$resultado_registros_historial[$i][10];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_dividido']=$resultado_registros_historial[$i][11];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_dividido_cantidad']=$resultado_registros_historial[$i][12];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_observaciones']=$resultado_registros_historial[$i][13];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_correo_de']=$resultado_registros_historial[$i][14];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_correo_de_nombre']=$resultado_registros_historial[$i][15];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_correo_para']=$resultado_registros_historial[$i][16];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_correo_para_nombre']=$resultado_registros_historial[$i][17];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_correo_cc']=$resultado_registros_historial[$i][18];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_correo_bcc']=$resultado_registros_historial[$i][19];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_correo_fecha']=$resultado_registros_historial[$i][20];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_correo_asunto']=$resultado_registros_historial[$i][21];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_estado_envio']=$resultado_registros_historial[$i][22];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_fecha_envio']=$resultado_registros_historial[$i][23];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_registro_usuario']=$resultado_registros_historial[$i][24];
                $array_historial[$resultado_registros_historial[$i][0]]['grch_registro_fecha']=$resultado_registros_historial[$i][25];
            }

            $consulta_string_agentes="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_correo_corporativo` FROM `administrador_usuario` WHERE 1=1 AND `usu_id`<>'1111111111'";
            $consulta_registros_agentes = $enlace_db->prepare($consulta_string_agentes);
            $consulta_registros_agentes->execute();
            $resultado_registros_agentes = $consulta_registros_agentes->get_result()->fetch_all(MYSQLI_NUM);

            for ($i=0; $i < count($resultado_registros_agentes); $i++) { 
                $array_agentes[$resultado_registros_agentes[$i][0]]['nombres']=$resultado_registros_agentes[$i][1];
                $array_agentes[$resultado_registros_agentes[$i][0]]['correo']=$resultado_registros_agentes[$i][2];
            }

            $consulta_string_remitentes="SELECT `ncr_id`, `ncr_setfrom` FROM `administrador_buzones`";
            $consulta_registros_remitentes = $enlace_db->prepare($consulta_string_remitentes);
            $consulta_registros_remitentes->execute();
            $resultado_registros_remitentes = $consulta_registros_remitentes->get_result()->fetch_all(MYSQLI_NUM);

            for ($i=0; $i < count($resultado_registros_remitentes); $i++) { 
                $array_remitentes[$resultado_registros_remitentes[$i][0]]=$resultado_registros_remitentes[$i][1];
            }
        }
    }
    
    $delimitador = ';';
    $encapsulador = '"';
    $ruta='storage/'.$titulo_reporte;
    // create a file pointer connected to the output stream
    $file = fopen($ruta, 'w');
    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

    if ($tipo_reporte=='Consolidado Gestión') {
        fputcsv($file, array('Reporte: Gestión Radicados'), $delimitador, $encapsulador);
        fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
        
        $titulos=array('Radicado', 
            'Estado', 
            'Tipología', 
            'Clasificación', 
            'Duplicado', 
            'Dividido Radicado', 
            'Dividido Cantidad', 
            'Unificado', 
            'Unificado Radicado', 
            'Radicado Asunto', 
            'Radicado Remitente', 
            'Radicado Fecha/Hora', 
            'Radicado Responsable Documento', 
            'Radicado Responsable Nombres y Apellidos', 
            'Tipo Gestión', 
            'Gestión', 
            // 'Plantilla', 
            // 'Motivo', 
            'Gestión Asunto', 
            'Gestión Remitente', 
            'Gestión Para', 
            'Gestión CC', 
            'Gestión CCO', 
            'Gestión Estado', 
            'Gestión Envío Fecha/Hora', 
            'Gestión Fecha', 
            'Gestión Usuario Documento', 
            'Gestión Usuario Nombres y Apellidos');

        fputcsv($file, $titulos, $delimitador, $encapsulador);

        for ($i=0; $i < count($resultado_registros); $i++) {
            $id_maximo_radicado=$array_maximo[$resultado_registros[$i][0]];
            if ($array_remitentes[$array_historial[$id_maximo_radicado]['grch_correo_de']]!='') {
                $remitente=$array_remitentes[$array_historial[$id_maximo_radicado]['grch_correo_de']];
            } else {
                $remitente=$array_historial[$id_maximo_radicado]['grch_correo_de'];
            }

            $linea=array($resultado_registros[$i][1], //Radicado
                        $resultado_registros[$i][7], //Estado
                        $resultado_registros[$i][2], //Tipología
                        $resultado_registros[$i][3], //Clasificación
                        $resultado_registros[$i][8], //Duplicado
                        $resultado_registros[$i][11], //Dividido Radicado
                        $resultado_registros[$i][12], //Dividido Cantidad
                        $resultado_registros[$i][9], //Unificado
                        $resultado_registros[$i][10], //Unificado Radicado
                        $resultado_registros[$i][15], //Radicado Asunto
                        $resultado_registros[$i][14], //Radicado Remitente
                        $resultado_registros[$i][16], //Radicado Fecha/Hora
                        $resultado_registros[$i][4], //Radicado Responsable Documento
                        $array_agentes[$resultado_registros[$i][4]]['nombres'], //Radicado Responsable Nombres y Apellidos
                        $array_historial[$id_maximo_radicado]['grch_tipo'], //Tipo Gestión
                        $array_historial[$id_maximo_radicado]['grch_gestion'], //Gestión
                        // $resultado_registros[$i][29], // Plantilla
                        // $resultado_registros[$i][29], // Motivo
                        $array_historial[$id_maximo_radicado]['grch_correo_asunto'], //Gestión Asunto
                        $remitente, //Gestión Remitente
                        $array_historial[$id_maximo_radicado]['grch_correo_para'], //Gestión Para
                        $array_historial[$id_maximo_radicado]['grch_correo_cc'], //Gestión CC
                        $array_historial[$id_maximo_radicado]['grch_correo_bcc'], //Gestión CCO
                        $array_historial[$id_maximo_radicado]['grch_estado_envio'], //Gestión Estado
                        $array_historial[$id_maximo_radicado]['grch_fecha_envio'], //Gestión Envío Fecha/Hora
                        $array_historial[$id_maximo_radicado]['grch_registro_fecha'], //Gestión Fecha
                        $array_historial[$id_maximo_radicado]['grch_registro_usuario'], //Gestión Usuario Documento
                        $array_agentes[$array_historial[$id_maximo_radicado]['grch_registro_usuario']]['nombres']);//Gestión Usuario Nombres y Apellidos
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