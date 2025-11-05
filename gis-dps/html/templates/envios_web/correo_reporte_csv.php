<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Envíos WEB";
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    if(isset($_POST["reporte"])){
        $tipo_reporte=validar_input($_POST['tipo_reporte']);
        $tipologia=$_POST['tipologia'];
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        $titulo_reporte="Gestión Envíos Web -".$tipo_reporte.' - '.date('Y-m-d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if($tipologia=='Reparto'){
          $filtro_tipologia=" AND `gewc_tipologia`=?";
          array_push($data_consulta, 'Reparto');
        } elseif($tipologia=='Subsidio Familiar de Vivienda en especie'){
          $filtro_tipologia=" AND `gewc_tipologia`=?";
          array_push($data_consulta, 'Subsidio Familiar de Vivienda en especie');
        } elseif($tipologia=='Ingreso Solidario'){
          $filtro_tipologia=" AND `gewc_tipologia`=?";
          array_push($data_consulta, 'Ingreso Solidario');
        } elseif($tipologia=='Colombia Mayor'){
          $filtro_tipologia=" AND `gewc_tipologia`=?";
          array_push($data_consulta, 'Colombia Mayor');
        } elseif($tipologia=='Compensación del IVA'){
          $filtro_tipologia=" AND `gewc_tipologia`=?";
          array_push($data_consulta, 'Compensación del IVA');
        } elseif($tipologia=='Antifraudes'){
          $filtro_tipologia=" AND `gewc_tipologia`=?";
          array_push($data_consulta, 'Antifraudes');
        } elseif($tipologia=='Jóvenes en Acción'){
          $filtro_tipologia=" AND `gewc_tipologia`=?";
          array_push($data_consulta, 'Jóvenes en Acción');
        } elseif($tipologia=='Tránsito a Renta Ciudadana'){
          $filtro_tipologia=" AND `gewc_tipologia`=?";
          array_push($data_consulta, 'Tránsito a Renta Ciudadana');
        } elseif($tipologia=='Otros programas'){
          $filtro_tipologia=" AND `gewc_tipologia`=?";
          array_push($data_consulta, 'Otros programas');
        } elseif($tipologia=="Todos"){
          $filtro_tipologia="";
        }


        if ($tipo_reporte=='Consolidado Gestión') {
            $consulta_string="SELECT `gewc_id`, `gewc_radicado`, `gewc_radicado_entrada`, `gewc_radicado_salida`, `gewc_tipologia`, `gewc_clasificacion`, `gewc_responsable`, `gewc_gestion`, `gewc_gestion_detalle`, `gewc_estado`, `gewc_fecha_gestion`, `gewc_correo_remitente`, `gewc_correo_asunto`, `gewc_correo_fecha`, `gewc_registro_fecha`, `gewc_registro_fecha_hora`, TAR.`usu_nombres_apellidos`, TAR.`usu_correo_corporativo`, TMAX.idmax, TH.`gewch_id`, TH.`gewch_radicado`, TH.`gewch_radicado_id`, TH.`gewch_tipo`, TH.`gewch_tipologia`, TH.`gewch_gestion`, TH.`gewch_gestion_detalle`, TH.`gewch_anonimo`, TH.`gewch_publicacion`, TH.`gewch_correo_id`, TH.`gewch_correo_de`, TH.`gewch_correo_de_nombre`, TH.`gewch_correo_para`, TH.`gewch_correo_para_nombre`, TH.`gewch_correo_cc`, TH.`gewch_correo_bcc`, TH.`gewch_correo_fecha`, TH.`gewch_correo_asunto`, TH.`gewch_correo_contenido`, TH.`gewch_embeddedimage_ruta`, TH.`gewch_embeddedimage_nombre`, TH.`gewch_embeddedimage_tipo`, TH.`gewch_attachment_ruta`, TH.`gewch_intentos`, TH.`gewch_estado_envio`, TH.`gewch_fecha_envio`, TH.`gewch_registro_usuario`, TH.`gewch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom` FROM `gestion_enviosweb_casos` LEFT JOIN `administrador_usuario` AS TAR ON `gestion_enviosweb_casos`.`gewc_responsable`=TAR.`usu_id` LEFT JOIN (SELECT `gewch_radicado_id`, MAX(`gewch_id`) AS idmax FROM `gestion_enviosweb_casos_historial` WHERE `gewch_tipo`='Gestión' GROUP BY `gewch_radicado_id`) AS TMAX ON `gestion_enviosweb_casos`.`gewc_id`=TMAX.`gewch_radicado_id` LEFT JOIN `gestion_enviosweb_casos_historial` AS TH ON TMAX.idmax=TH.`gewch_id` LEFT JOIN `administrador_usuario` AS TAG ON TH.`gewch_registro_usuario`=TAG.`usu_id` LEFT JOIN `administrador_buzones` AS RT ON TH.`gewch_correo_de`=RT.`ncr_id` WHERE 1=1 AND `gewc_correo_fecha`>=? AND `gewc_correo_fecha`<=? ".$filtro_tipologia." ORDER BY `gewc_id` ASC";
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
        fputcsv($file, array('Reporte: Gestión Envíos Web'), $delimitador, $encapsulador);
        fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
        
        $titulos=array('Radicado', 
            'Estado', 
            'Radicado Entrada', 
            'Radicado Salida', 
            'Tipología', 
            'Radicado Asunto', 
            'Radicado Remitente', 
            'Radicado Fecha/Hora', 
            'Radicado Responsable Documento', 
            'Radicado Responsable Nombres y Apellidos', 
            'Tipo Gestión', 
            'Gestión', 
            'Gestión Detalle', 
            'Gestión Asunto', 
            'Gestión Remitente', 
            'Gestión Para', 
            'Gestión CC', 
            'Gestión CCO', 
            'Gestión Anónimo', 
            'Gestión Publicación', 
            'Gestión Estado', 
            'Gestión Envío Fecha/Hora', 
            'Gestión Fecha', 
            'Gestión Usuario Documento', 
            'Gestión Usuario Nombres y Apellidos');

        fputcsv($file, $titulos, $delimitador, $encapsulador);

        $array_ft['false']='No';
        $array_ft['true']='Si';

        for ($i=0; $i < count($resultado_registros); $i++) {
            if ($resultado_registros[$i][49]!='') {
                $remitente=$resultado_registros[$i][49];
            } else {
                $remitente=$resultado_registros[$i][29];
            }

            $linea=array($resultado_registros[$i][1], 
                            $resultado_registros[$i][9], 
                            $resultado_registros[$i][2], 
                            $resultado_registros[$i][3], 
                            $resultado_registros[$i][4], 
                            $resultado_registros[$i][12], 
                            $resultado_registros[$i][11], 
                            $resultado_registros[$i][13], 
                            $resultado_registros[$i][6], 
                            $resultado_registros[$i][16], 
                            $resultado_registros[$i][22], 
                            $resultado_registros[$i][24], 
                            $resultado_registros[$i][25], 
                            $resultado_registros[$i][36], 
                            $remitente, 
                            $resultado_registros[$i][31], 
                            $resultado_registros[$i][33], 
                            $resultado_registros[$i][34], 
                            $array_ft[$resultado_registros[$i][26]], 
                            $array_ft[$resultado_registros[$i][27]], 
                            $resultado_registros[$i][43], 
                            $resultado_registros[$i][44], 
                            $resultado_registros[$i][46], 
                            $resultado_registros[$i][45], 
                            $resultado_registros[$i][47]);
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