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
          $filtro_tipologia="";
        }



        if ($tipo_reporte=='Consolidado Gestión') {
            $consulta_string="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`, `grc_registro_fecha_hora`, TAR.`usu_nombres_apellidos`, TAR.`usu_correo_corporativo`, TMAX.idmax, TH.`grch_id`, TH.`grch_radicado`, TH.`grch_radicado_id`, TH.`grch_tipo`, TH.`grch_tipologia`, TH.`grch_clasificacion`, TH.`grch_gestion`, TH.`grch_gestion_detalle`, TH.`grch_duplicado`, TH.`grch_unificado`, TH.`grch_unificado_id`, TH.`grch_dividido`, TH.`grch_dividido_cantidad`, TH.`grch_observaciones`, TH.`grch_correo_id`, TH.`grch_correo_de`, TH.`grch_correo_de_nombre`, TH.`grch_correo_para`, TH.`grch_correo_para_nombre`, TH.`grch_correo_cc`, TH.`grch_correo_bcc`, TH.`grch_correo_fecha`, TH.`grch_correo_asunto`, TH.`grch_correo_contenido`, TH.`grch_embeddedimage_ruta`, TH.`grch_embeddedimage_nombre`, TH.`grch_embeddedimage_tipo`, TH.`grch_attachment_ruta`, TH.`grch_intentos`, TH.`grch_estado_envio`, TH.`grch_fecha_envio`, TH.`grch_registro_usuario`, TH.`grch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom` FROM `gestion_radicacion_casos` LEFT JOIN `administrador_usuario` AS TAR ON `gestion_radicacion_casos`.`grc_responsable`=TAR.`usu_id` LEFT JOIN (SELECT `grch_radicado_id`, MAX(`grch_id`) AS idmax FROM `gestion_radicacion_casos_historial` WHERE `grch_tipo`='Gestión' GROUP BY `grch_radicado_id`) AS TMAX ON `gestion_radicacion_casos`.`grc_id`=TMAX.`grch_radicado_id` LEFT JOIN `gestion_radicacion_casos_historial` AS TH ON TMAX.idmax=TH.`grch_id` LEFT JOIN `administrador_usuario` AS TAG ON TH.`grch_registro_usuario`=TAG.`usu_id` LEFT JOIN `administrador_buzones` AS RT ON TH.`grch_correo_de`=RT.`ncr_id` WHERE 1=1 AND `grc_correo_fecha`>=? AND `grc_correo_fecha`<=? ".$filtro_tipologia." ORDER BY `grc_id` ASC";
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
            if ($resultado_registros[$i][57]!='') {
                $remitente=$resultado_registros[$i][57];
            } else {
                $remitente=$resultado_registros[$i][37];
            }

            $linea=array($resultado_registros[$i][1], 
                            $resultado_registros[$i][7], 
                            $resultado_registros[$i][2], 
                            $resultado_registros[$i][3], 
                            $resultado_registros[$i][8], 
                            $resultado_registros[$i][11], 
                            $resultado_registros[$i][12], 
                            $resultado_registros[$i][9], 
                            $resultado_registros[$i][10], 
                            $resultado_registros[$i][15], 
                            $resultado_registros[$i][14], 
                            $resultado_registros[$i][16], 
                            $resultado_registros[$i][4], 
                            $resultado_registros[$i][19], 
                            $resultado_registros[$i][25], 
                            $resultado_registros[$i][28], 
                            // $resultado_registros[$i][29], 
                            // $resultado_registros[$i][29], 
                            $resultado_registros[$i][44], 
                            $remitente, 
                            $resultado_registros[$i][39], 
                            $resultado_registros[$i][41], 
                            $resultado_registros[$i][42], 
                            $resultado_registros[$i][51], 
                            $resultado_registros[$i][52], 
                            $resultado_registros[$i][54], 
                            $resultado_registros[$i][53], 
                            $resultado_registros[$i][55]);
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