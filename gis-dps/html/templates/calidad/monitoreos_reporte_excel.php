<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Calidad-Monitoreos";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    require_once('../assets/plugins/PhpSpreadsheet/vendor/autoload.php');
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    if(isset($_POST["reporte"])){
        $tipo_reporte=validar_input($_POST['tipo_reporte']);
        $tipo_monitoreo=validar_input($_POST['tipo_monitoreo']);
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        $id_matriz=validar_input($_POST['id_matriz']);
        $agente=validar_input($_POST['agente']);

        $titulo_reporte="Gestión Calidad-Monitoreos-".$tipo_reporte."-".date('Y-m-d H_i_s').".xlsx";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        $data_consulta_registros=array();

        $filtro_id_matriz="";
        if ($id_matriz=='Todas') {
            $filtro_id_matriz="";
        } else {
            $filtro_id_matriz=" AND TMC.`gcm_matriz`=?";
            array_push($data_consulta_registros, $id_matriz);
        }

        $filtro_tipo="";
        if ($tipo_monitoreo=='Todos') {
            $filtro_tipo="";
        } else {
            $filtro_tipo=" AND TMC.`gcm_tipo_monitoreo`=?";
            array_push($data_consulta_registros, $tipo_monitoreo);
        }

        $filtro_agente="";
        if ($agente=='Todos') {
            $filtro_agente="";
        } else {
            $filtro_agente=" AND TMC.`gcm_analista`=?";
            array_push($data_consulta_registros, $agente);
        }

        array_push($data_consulta_registros, $fecha_inicio);
        array_push($data_consulta_registros, $fecha_fin);

        $consulta_string="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TUA.`usu_nombres_apellidos`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal`, TMC.`gcm_fecha_reac_limite`, TMC.`gcm_fecha_reac`, TMC.`gcm_fecha_calidad_reac_limite`, TMC.`gcm_fecha_calidad_reac`, TMC.`gcm_fecha_snivel_reac_limite`, TMC.`gcm_fecha_snivel_reac`, TMC.`gcm_fecha_sreac_limite`, TMC.`gcm_fecha_sreac`, TMC.`gcm_fecha_novedad_inicio`, TMC.`gcm_fecha_novedad_fin`, TMC.`gcm_novedad_observaciones`, TUA.`usu_estado`, TUA.`usu_supervisor` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE 1=1 ".$filtro_id_matriz." ".$filtro_tipo." ".$filtro_agente." AND TMC.`gcm_registro_fecha`>=? AND TMC.`gcm_registro_fecha`<=? ORDER BY `gcm_id`";

        $consulta_registros = $enlace_db->prepare($consulta_string);
        if (count($data_consulta_registros)>0) {
            // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta_registros en el orden específico de los parámetros de la sentencia preparada
            $consulta_registros->bind_param(str_repeat("s", count($data_consulta_registros)), ...$data_consulta_registros);
            
        }
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $filtro_monitoreos='';
        if (count($resultado_registros)>0) {
            for ($i=0; $i < count($resultado_registros); $i++) { 
                $filtro_monitoreos.="`gcmh_monitoreo`=? OR ";
                $filtro_monitoreos_matriz.="`gcmc_monitoreo`=? OR ";
                array_push($data_consulta, $resultado_registros[$i][0]);
            }
            
            $filtro_monitoreos="AND (".substr($filtro_monitoreos, 0, -4).")";
            $filtro_monitoreos_matriz="AND (".substr($filtro_monitoreos_matriz, 0, -4).")";
        }

        $consulta_string_historial="SELECT `gcmh_id`, `gcmh_monitoreo`, `gcmh_tipo_cambio`, `gcmh_comentarios`, `gcmh_registro_usuario`, `gcmh_registro_fecha`, `gcmh_resarcimiento` FROM `gestion_calidad_monitoreo_historial` WHERE 1=1 ".$filtro_monitoreos."";

        $consulta_registros_historial = $enlace_db->prepare($consulta_string_historial);
        if (count($data_consulta)>0) {
            $consulta_registros_historial->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
        }
            
        $consulta_registros_historial->execute();
        $resultado_registros_historial = $consulta_registros_historial->get_result()->fetch_all(MYSQLI_NUM);
        
        for ($j=0; $j < count($resultado_registros_historial); $j++) { 
            $array_estado_historial[$resultado_registros_historial[$j][1]]['Refutar'].="";
            $array_estado_historial[$resultado_registros_historial[$j][1]]['Aceptar'].="";
            $array_estado_historial[$resultado_registros_historial[$j][1]]['Refutar-Rechazado'].="";
            $array_estado_historial[$resultado_registros_historial[$j][1]]['Refutar-Aceptado'].="";
            $array_estado_historial[$resultado_registros_historial[$j][1]]['Refutar-Nivel 2'].="";
            $array_estado_historial[$resultado_registros_historial[$j][1]]['Refutar-Rechazado-Nivel 2'].="";
            $array_estado_historial[$resultado_registros_historial[$j][1]]['Refutar-Aceptado-Nivel 2'].="";
            $array_estado_historial[$resultado_registros_historial[$j][1]]['Aceptar-ODM'].="";

            $array_estado_historial[$resultado_registros_historial[$j][1]]['Resarcimiento'].=$resultado_registros_historial[$j][6];
            $array_estado_historial[$resultado_registros_historial[$j][1]][$resultado_registros_historial[$j][2]].=$resultado_registros_historial[$j][3];

            if (!isset($array_estado_historial_date[$resultado_registros_historial[$j][1]]['Refutar'])) {
                $array_estado_historial_date[$resultado_registros_historial[$j][1]]['Refutar']="";
                $array_estado_historial_date[$resultado_registros_historial[$j][1]]['Aceptar']="";
                $array_estado_historial_date[$resultado_registros_historial[$j][1]]['Refutar-Rechazado']="";
                $array_estado_historial_date[$resultado_registros_historial[$j][1]]['Refutar-Aceptado']="";
                $array_estado_historial_date[$resultado_registros_historial[$j][1]]['Refutar-Nivel 2']="";
                $array_estado_historial_date[$resultado_registros_historial[$j][1]]['Refutar-Rechazado-Nivel 2']="";
                $array_estado_historial_date[$resultado_registros_historial[$j][1]]['Refutar-Aceptado-Nivel 2']="";
                $array_estado_historial_date[$resultado_registros_historial[$j][1]]['Aceptar-ODM']="";
            }

            $array_estado_historial_date[$resultado_registros_historial[$j][1]][$resultado_registros_historial[$j][2]]=$resultado_registros_historial[$j][5];
        }

        if ($tipo_reporte=='Consolidado-Matriz' AND $id_matriz!="Todas") {
            $consulta_string_matriz="SELECT `gcmi_id`, `gcmi_matriz`, `gcmi_item_tipo`, `gcmi_item_consecutivo`, `gcmi_item_orden`, `gcmi_descripcion`, `gcmi_peso`, `gcmi_calificable`, `gcmi_grupo_peso`, `gcmi_visible` FROM `gestion_calidad_matriz_item` WHERE `gcmi_matriz`=? ORDER BY `gcmi_item_consecutivo` ASC";

            $consulta_registros_matriz = $enlace_db->prepare($consulta_string_matriz);
            $consulta_registros_matriz->bind_param('s', $id_matriz);
            $consulta_registros_matriz->execute();
            $resultado_registros_matriz = $consulta_registros_matriz->get_result()->fetch_all(MYSQLI_NUM);

            for ($i=0; $i < count($resultado_registros_matriz); $i++) {
                if ($resultado_registros_matriz[$i][7]=="Si") {
                    $array_items_matriz['nombre'][]=$resultado_registros_matriz[$i][5];
                    $array_items_matriz['nombre'][]="Comentario";
                    $array_items_matriz['id'][]=$resultado_registros_matriz[$i][0];
                    $array_items_matriz['consecutivo'][]=$resultado_registros_matriz[$i][3];
                    $array_items_matriz['consecutivo'][]="";
                    $array_items_matriz['peso'][]=$resultado_registros_matriz[$i][6]."%";
                    $array_items_matriz['peso'][]="";
                }
            }

            $consulta_string_respuesta="SELECT `gcmc_id`, `gcmc_monitoreo`, `gcmc_pregunta`, `gcmc_respuesta`, `gcmc_afectaciones`, `gcmc_comentarios`, TIM.`gcmi_matriz`, TIM.`gcmi_item_tipo`, TIM.`gcmi_item_consecutivo`, TIM.`gcmi_item_orden`, TIM.`gcmi_descripcion`, TIM.`gcmi_peso`, TIM.`gcmi_calificable` FROM `gestion_calidad_monitoreo_calificaciones` LEFT JOIN `gestion_calidad_matriz_item` AS TIM ON `gestion_calidad_monitoreo_calificaciones`.`gcmc_pregunta`=TIM.`gcmi_id` WHERE 1=1 ".$filtro_monitoreos_matriz."  ORDER BY TIM.`gcmi_item_consecutivo` ASC";

            $consulta_registros_respuesta = $enlace_db->prepare($consulta_string_respuesta);
            if (count($data_consulta)>0) {
                $consulta_registros_respuesta->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_respuesta->execute();
            $resultado_registros_respuesta = $consulta_registros_respuesta->get_result()->fetch_all(MYSQLI_NUM);

            for ($j=0; $j < count($resultado_registros_respuesta); $j++) {
                if ($resultado_registros_respuesta[$j][12]=="Si") {
                    $array_respuestas[$resultado_registros_respuesta[$j][1]][$resultado_registros_respuesta[$j][2]]['respuesta']=$resultado_registros_respuesta[$j][3];
                    $array_respuestas[$resultado_registros_respuesta[$j][1]][$resultado_registros_respuesta[$j][2]]['comentarios']=$resultado_registros_respuesta[$j][5];
                }
            }
        }
    }

    // Creamos nueva instancia de PHPExcel 
    $spreadsheet = new Spreadsheet();

    // Establecer propiedades
    $spreadsheet->getProperties()
    ->setCreator(APP_NAME_ALL)
    ->setLastModifiedBy($_SESSION[APP_SESSION.'_session_usu_nombre_completo'])
    ->setTitle(APP_NAME_ALL)
    ->setSubject(APP_NAME_ALL)
    ->setDescription(APP_NAME_ALL)
    ->setKeywords(APP_NAME_ALL)
    ->setCategory("Reporte");

    require_once("../../includes/_excel-style.php");

    //Activar hoja 0
    $sheet = $spreadsheet->getActiveSheet(0);
    
    // Nombramos la hoja 0
    $spreadsheet->getActiveSheet()->setTitle('Reporte Gestión Calidad');

    //Estilos de la Hoja 0
    $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(80);
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AE')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AH')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AI')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AK')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AL')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AM')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AN')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AO')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AP')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AQ')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AR')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AS')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AT')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('AU')->setWidth(20);
    
    $spreadsheet->getActiveSheet()->getStyle('A3:AU3')->applyFromArray($styleArrayTitulos);

    if ($tipo_reporte=='Consolidado') {
        $spreadsheet->getActiveSheet()->setAutoFilter('A3:AU3');
    } elseif ($tipo_reporte=='Consolidado-Matriz' AND $id_matriz!="Todas") {
        $spreadsheet->getActiveSheet()->getStyle('A3:'.$array_columnas[count($array_items_matriz['nombre'])+49].'3')->applyFromArray($styleArrayTitulos);
        $spreadsheet->getActiveSheet()->setAutoFilter('A3:'.$array_columnas[count($array_items_matriz['nombre'])+49].'3');
    }
    $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);

    // Escribiendo los titulos
    $spreadsheet->getActiveSheet()->setCellValue('A3','Consecutivo');
    $spreadsheet->getActiveSheet()->setCellValue('B3','Doc. Agente');
    $spreadsheet->getActiveSheet()->setCellValue('C3','Agente');
    $spreadsheet->getActiveSheet()->setCellValue('D3','Segmento');
    $spreadsheet->getActiveSheet()->setCellValue('E3','Responsable');
    $spreadsheet->getActiveSheet()->setCellValue('F3','Matriz');
    $spreadsheet->getActiveSheet()->setCellValue('G3','Canal');
    $spreadsheet->getActiveSheet()->setCellValue('H3','Dependencia');
    $spreadsheet->getActiveSheet()->setCellValue('I3','Identificación Ciudadano');
    $spreadsheet->getActiveSheet()->setCellValue('J3','Número Transacción');
    $spreadsheet->getActiveSheet()->setCellValue('K3','Tipo Monitoreo');
    $spreadsheet->getActiveSheet()->setCellValue('L3','Fecha Gestión');
    $spreadsheet->getActiveSheet()->setCellValue('M3','Nota ENC');
    $spreadsheet->getActiveSheet()->setCellValue('N3','Nota ECUF');
    $spreadsheet->getActiveSheet()->setCellValue('O3','Nota ECN');
    $spreadsheet->getActiveSheet()->setCellValue('P3','Estado');
    $spreadsheet->getActiveSheet()->setCellValue('Q3','Solucionado primer contacto?');
    $spreadsheet->getActiveSheet()->setCellValue('R3','Causal NO solución');
    $spreadsheet->getActiveSheet()->setCellValue('S3','Programa');
    $spreadsheet->getActiveSheet()->setCellValue('T3','Tipificación');
    $spreadsheet->getActiveSheet()->setCellValue('U3','Sub-Tipificación');
    $spreadsheet->getActiveSheet()->setCellValue('V3','Atención WOW');
    $spreadsheet->getActiveSheet()->setCellValue('W3','Se presenta VOC (Voz Orientada al Ciudadano)');
    $spreadsheet->getActiveSheet()->setCellValue('X3','Segmento');
    $spreadsheet->getActiveSheet()->setCellValue('Y3','Tabulación VOC (Voz Orientada al Ciudadano)');
    $spreadsheet->getActiveSheet()->setCellValue('Z3','VOC (Voz Orientada al Ciudadano)');
    $spreadsheet->getActiveSheet()->setCellValue('AA3','VOC (Voz Orientada al Ciudadano) Emoción inicial');
    $spreadsheet->getActiveSheet()->setCellValue('AB3','VOC (Voz Orientada al Ciudadano) Emoción final');
    $spreadsheet->getActiveSheet()->setCellValue('AC3','Qué le activó');
    $spreadsheet->getActiveSheet()->setCellValue('AD3','Atribuible');
    $spreadsheet->getActiveSheet()->setCellValue('AE3','Observaciones Generales');
    $spreadsheet->getActiveSheet()->setCellValue('AF3','Usuario Registro');
    $spreadsheet->getActiveSheet()->setCellValue('AG3','Fecha-Hora Registro');
    
    $spreadsheet->getActiveSheet()->setCellValue('AH3','Observaciones Refutar');
    $spreadsheet->getActiveSheet()->setCellValue('AI3','Observaciones Aceptar');
    $spreadsheet->getActiveSheet()->setCellValue('AJ3','Observaciones Refutar-Rechazado');
    $spreadsheet->getActiveSheet()->setCellValue('AK3','Observaciones Refutar-Aceptado');
    $spreadsheet->getActiveSheet()->setCellValue('AL3','Observaciones Refutar-Nivel 2');
    $spreadsheet->getActiveSheet()->setCellValue('AM3','Observaciones Refutar-Rechazado-Nivel 2');
    $spreadsheet->getActiveSheet()->setCellValue('AN3','Observaciones Refutar-Aceptado-Nivel 2');
    $spreadsheet->getActiveSheet()->setCellValue('AO3','Resarcimiento');
    $spreadsheet->getActiveSheet()->setCellValue('AP3','Estado Aceptar');
    $spreadsheet->getActiveSheet()->setCellValue('AQ3','Estado Refutado');
    $spreadsheet->getActiveSheet()->setCellValue('AR3','Estado Refutado-Nivel 2');
    $spreadsheet->getActiveSheet()->setCellValue('AS3','Estado Aceptar-Nivel 2');
    $spreadsheet->getActiveSheet()->setCellValue('AT3','ODM');
    $spreadsheet->getActiveSheet()->setCellValue('AU3','Fecha y Hora de Cierre');

    if ($tipo_reporte=='Consolidado-Matriz' AND $id_matriz!="Todas") {
        for ($i=49; $i < count($array_items_matriz['nombre'])+49; $i++) {
            $nombre_final=$array_items_matriz['consecutivo'][$i-49]." ".$array_items_matriz['nombre'][$i-49];
            $spreadsheet->getActiveSheet()->setCellValue($array_columnas[$i].'2',$array_items_matriz['peso'][$i-49]);
            $spreadsheet->getActiveSheet()->setCellValue($array_columnas[$i].'3',$nombre_final);
            $spreadsheet->getActiveSheet()->getColumnDimension($array_columnas[$i])->setWidth(20);
        }
    }

    // Ingresar Data consultada a partir de la fila 4
    $fecha_actual=date('Y-m-d H:i:s');
    for ($i=4; $i < count($resultado_registros)+4; $i++) {
        $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-4][0]);
        $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-4][3]);
        $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-4][37]);
        $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-4][38]);
        $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-4][39]);
        $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-4][2]);
        $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-4][47]);
        $spreadsheet->getActiveSheet()->setCellValue('H'.$i,$resultado_registros[$i-4][5]);
        $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-4][6]);
        $spreadsheet->getActiveSheet()->setCellValue('J'.$i,$resultado_registros[$i-4][7]);
        $spreadsheet->getActiveSheet()->setCellValue('K'.$i,$resultado_registros[$i-4][8]);
        $spreadsheet->getActiveSheet()->setCellValue('L'.$i,$resultado_registros[$i-4][4]);
        $spreadsheet->getActiveSheet()->setCellValue('M'.$i,$resultado_registros[$i-4][10]);
        $spreadsheet->getActiveSheet()->setCellValue('N'.$i,$resultado_registros[$i-4][12]);
        $spreadsheet->getActiveSheet()->setCellValue('O'.$i,$resultado_registros[$i-4][11]);
        $spreadsheet->getActiveSheet()->setCellValue('P'.$i,$resultado_registros[$i-4][13]);
        $spreadsheet->getActiveSheet()->setCellValue('Q'.$i,$resultado_registros[$i-4][14]);
        $spreadsheet->getActiveSheet()->setCellValue('R'.$i,$resultado_registros[$i-4][15]);
        $spreadsheet->getActiveSheet()->setCellValue('S'.$i,$resultado_registros[$i-4][16]);
        $spreadsheet->getActiveSheet()->setCellValue('T'.$i,$resultado_registros[$i-4][17]);
        $spreadsheet->getActiveSheet()->setCellValue('U'.$i,$resultado_registros[$i-4][18]);
        $spreadsheet->getActiveSheet()->setCellValue('V'.$i,$resultado_registros[$i-4][19]);
        $spreadsheet->getActiveSheet()->setCellValue('W'.$i,$resultado_registros[$i-4][20]);
        $spreadsheet->getActiveSheet()->setCellValue('X'.$i,$resultado_registros[$i-4][21]);
        $spreadsheet->getActiveSheet()->setCellValue('Y'.$i,$resultado_registros[$i-4][22]);
        $spreadsheet->getActiveSheet()->setCellValue('Z'.$i,$resultado_registros[$i-4][23]);
        $spreadsheet->getActiveSheet()->setCellValue('AA'.$i,$resultado_registros[$i-4][24]);
        $spreadsheet->getActiveSheet()->setCellValue('AB'.$i,$resultado_registros[$i-4][25]);
        $spreadsheet->getActiveSheet()->setCellValue('AC'.$i,$resultado_registros[$i-4][26]);
        $spreadsheet->getActiveSheet()->setCellValue('AD'.$i,$resultado_registros[$i-4][27]);
        $spreadsheet->getActiveSheet()->setCellValue('AE'.$i,$resultado_registros[$i-4][9]);
        $spreadsheet->getActiveSheet()->setCellValue('AF'.$i,$resultado_registros[$i-4][40]);
        $spreadsheet->getActiveSheet()->setCellValue('AG'.$i,$resultado_registros[$i-4][36]);
        
        $spreadsheet->getActiveSheet()->setCellValue('AH'.$i,$array_estado_historial[$resultado_registros[$i-4][0]]['Refutar']);
        $spreadsheet->getActiveSheet()->setCellValue('AI'.$i,$array_estado_historial[$resultado_registros[$i-4][0]]['Aceptar']);
        $spreadsheet->getActiveSheet()->setCellValue('AJ'.$i,$array_estado_historial[$resultado_registros[$i-4][0]]['Refutar-Rechazado']);
        $spreadsheet->getActiveSheet()->setCellValue('AK'.$i,$array_estado_historial[$resultado_registros[$i-4][0]]['Refutar-Aceptado']);
        $spreadsheet->getActiveSheet()->setCellValue('AL'.$i,$array_estado_historial[$resultado_registros[$i-4][0]]['Refutar-Nivel 2']);
        $spreadsheet->getActiveSheet()->setCellValue('AM'.$i,$array_estado_historial[$resultado_registros[$i-4][0]]['Refutar-Rechazado-Nivel 2']);
        $spreadsheet->getActiveSheet()->setCellValue('AN'.$i,$array_estado_historial[$resultado_registros[$i-4][0]]['Refutar-Aceptado-Nivel 2']);
        $spreadsheet->getActiveSheet()->setCellValue('AO'.$i,$array_estado_historial[$resultado_registros[$i-4][0]]['Resarcimiento']);

        if($resultado_registros[$i-4][48]!="") {
            $limite_tiempo_1=$resultado_registros[$i-4][48];
            if ($resultado_registros[$i-4][49]!="") {
                $tiempo_1=$resultado_registros[$i-4][49];
            } else {
                $tiempo_1=$fecha_actual;
            }

            if ($tiempo_1>=$limite_tiempo_1) {
                $estado_vencimiento_1='VENCIDO';
            } else {
                $estado_vencimiento_1='NO VENCIDO';
            }
        } else {
            $estado_vencimiento_1='';
        }

        if($resultado_registros[$i-4][50]!="") {
            $limite_tiempo_2=$resultado_registros[$i-4][50];
            if ($resultado_registros[$i-4][51]!="") {
                $tiempo_2=$resultado_registros[$i-4][51];
            } else {
                $tiempo_2=$fecha_actual;
            }

            if ($tiempo_2>=$limite_tiempo_2) {
                $estado_vencimiento_2='VENCIDO';
            } else {
                $estado_vencimiento_2='NO VENCIDO';
            }
        } else {
            $estado_vencimiento_2='';
        }

        if($resultado_registros[$i-4][52]!="") {
            $limite_tiempo_3=$resultado_registros[$i-4][52];
            if ($resultado_registros[$i-4][53]!="") {
                $tiempo_3=$resultado_registros[$i-4][53];
            } else {
                $tiempo_3=$fecha_actual;
            }

            if ($tiempo_3>=$limite_tiempo_3) {
                $estado_vencimiento_3='VENCIDO';
            } else {
                $estado_vencimiento_3='NO VENCIDO';
            }
        } else {
            $estado_vencimiento_3='';
        }


        if($resultado_registros[$i-4][54]!="") {
            $limite_tiempo_4=$resultado_registros[$i-4][54];
            if ($resultado_registros[$i-4][55]!="") {
                $tiempo_4=$resultado_registros[$i-4][55];
            } else {
                $tiempo_4=$fecha_actual;
            }

            if ($tiempo_4>=$limite_tiempo_4) {
                $estado_vencimiento_4='VENCIDO';
            } else {
                $estado_vencimiento_4='NO VENCIDO';
            }
        } else {
            $estado_vencimiento_4='';
        }

        $fecha_cierre='';

        if ($array_estado_historial_date[$resultado_registros[$i-4][0]]['Aceptar']!='') {
            $fecha_cierre=$array_estado_historial_date[$resultado_registros[$i-4][0]]['Aceptar'];
        }

        $spreadsheet->getActiveSheet()->setCellValue('AP'.$i,$estado_vencimiento_1);
        $spreadsheet->getActiveSheet()->setCellValue('AQ'.$i,$estado_vencimiento_2);
        $spreadsheet->getActiveSheet()->setCellValue('AR'.$i,$estado_vencimiento_4);
        $spreadsheet->getActiveSheet()->setCellValue('AS'.$i,$estado_vencimiento_3);
        $spreadsheet->getActiveSheet()->setCellValue('AT'.$i,$array_estado_historial[$resultado_registros[$i-4][0]]['Aceptar-ODM']);
        $spreadsheet->getActiveSheet()->setCellValue('AU'.$i,$fecha_cierre);

        if ($tipo_reporte=='Consolidado-Matriz' AND $id_matriz!="Todas") {
            $columna_respuesta=49;
            $columna_comentario=50;
            for ($j=0; $j < count($array_items_matriz['id']); $j++) {
                $spreadsheet->getActiveSheet()->setCellValue($array_columnas[$columna_respuesta].$i,$array_respuestas[$resultado_registros[$i-4][0]][$array_items_matriz['id'][$j]]['respuesta']);
                $spreadsheet->getActiveSheet()->setCellValue($array_columnas[$columna_comentario].$i,$array_respuestas[$resultado_registros[$i-4][0]][$array_items_matriz['id'][$j]]['comentarios']);
                $columna_respuesta+=2;
                $columna_comentario+=2;
            }
        }
    }

    //Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$titulo_reporte.'"');
    header('Cache-Control: max-age=0');

    // Guardamos el archivo
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
?>