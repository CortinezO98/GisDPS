<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Calidad-Monitoreos";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    require_once('../assets/plugins/PHPfpdf/mc_table.php');
    $id_registro=validar_input(base64_decode($_GET['reg']));
    $consulta_string_monitoreo="SELECT TMC.`gcm_id`, TMC.`gcm_matriz`, TM.`gcm_nombre_matriz`, TMC.`gcm_analista`, TMC.`gcm_fecha_hora_gestion`, TMC.`gcm_dependencia`, TMC.`gcm_identificacion_ciudadano`, TMC.`gcm_numero_transaccion`, TMC.`gcm_tipo_monitoreo`, TMC.`gcm_observaciones_monitoreo`, TMC.`gcm_nota_enc`, TMC.`gcm_nota_ecn`, TMC.`gcm_nota_ecuf`, TMC.`gcm_estado`, TMC.`gcm_solucion_contacto`, TMC.`gcm_causal_nosolucion`, TMC.`gcm_tipi_programa`, TMC.`gcm_tipi_tipificacion`, TMC.`gcm_subtipificacion`, TMC.`gcm_atencion_wow`, TMC.`gcm_aplica_voc`, TMC.`gcm_segmento`, TMC.`gcm_tabulacion_voc`, TMC.`gcm_voc`, TMC.`gcm_emocion_inicial`, TMC.`gcm_emocion_final`, TMC.`gcm_que_le_activo`, TMC.`gcm_atribuible`, TMC.`gcm_direcciones_misionales`, TMC.`gcm_programa`, TMC.`gcm_tipificacion`, TMC.`gcm_subtipificacion_1`, TMC.`gcm_subtipificacion_2`, TMC.`gcm_subtipificacion_3`, TMC.`gcm_observaciones_info`, TMC.`gcm_registro_usuario`, TMC.`gcm_registro_fecha`, TMC.`columna_competencia`, TUA.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TS.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos`, TN1.`gic1_item`, TN2.`gic2_item`, TN3.`gic3_item`, TN4.`gic4_item`, TN5.`gic5_item`, TN6.`gic6_item`, TM.`gcm_canal` FROM `gestion_calidad_monitoreo` AS TMC LEFT JOIN `gestion_calidad_matriz` AS TM ON TMC.`gcm_matriz`=TM.`gcm_id` LEFT JOIN `administrador_usuario` AS TUR ON TMC.`gcm_registro_usuario`=TUR.`usu_id` LEFT JOIN `administrador_usuario` AS TUA ON TMC.`gcm_analista`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TS ON TUA.`usu_supervisor`=TS.`usu_id` LEFT JOIN `gestion_interacciones_catnivel1` AS TN1 ON TMC.`gcm_direcciones_misionales`=TN1.`gic1_id` LEFT JOIN `gestion_interacciones_catnivel2` AS TN2 ON TMC.`gcm_programa`=TN2.`gic2_id` LEFT JOIN `gestion_interacciones_catnivel3` AS TN3 ON TMC.`gcm_tipificacion`=TN3.`gic3_id` LEFT JOIN `gestion_interacciones_catnivel4` AS TN4 ON TMC.`gcm_subtipificacion_1`=TN4.`gic4_id` LEFT JOIN `gestion_interacciones_catnivel5` AS TN5 ON TMC.`gcm_subtipificacion_2`=TN5.`gic5_id` LEFT JOIN `gestion_interacciones_catnivel6` AS TN6 ON TMC.`gcm_subtipificacion_3`=TN6.`gic6_id` WHERE TMC.`gcm_id`=?";

    $consulta_registros_monitoreo = $enlace_db->prepare($consulta_string_monitoreo);
    $consulta_registros_monitoreo->bind_param("s", $id_registro);
    $consulta_registros_monitoreo->execute();
    $resultado_registros_monitoreo = $consulta_registros_monitoreo->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_evaluacion="SELECT `gcmc_id`, `gcmc_monitoreo`, `gcmc_pregunta`, `gcmc_respuesta`, `gcmc_afectaciones`, `gcmc_comentarios`, TIM.`gcmi_matriz`, TIM.`gcmi_item_tipo`, TIM.`gcmi_item_consecutivo`, TIM.`gcmi_item_orden`, TIM.`gcmi_descripcion`, TIM.`gcmi_peso`, TIM.`gcmi_calificable` FROM `gestion_calidad_monitoreo_calificaciones` LEFT JOIN `gestion_calidad_matriz_item` AS TIM ON `gestion_calidad_monitoreo_calificaciones`.`gcmc_pregunta`=TIM.`gcmi_id` WHERE `gcmc_monitoreo`=? AND TIM.`gcmi_matriz`=? ORDER BY `gcmi_item_consecutivo` ASC";
    $consulta_registros_evaluacion = $enlace_db->prepare($consulta_string_evaluacion);
    $consulta_registros_evaluacion->bind_param("ss", $id_registro, $resultado_registros_monitoreo[0][1]);
    $consulta_registros_evaluacion->execute();
    $resultado_registros_evaluacion = $consulta_registros_evaluacion->get_result()->fetch_all(MYSQLI_NUM);

    for ($i=0; $i < count($resultado_registros_evaluacion); $i++) {
        $array_respuestas[$resultado_registros_evaluacion[$i][2]]=$resultado_registros_evaluacion[$i][3];
        $array_comentarios[$resultado_registros_evaluacion[$i][2]]=$resultado_registros_evaluacion[$i][5];
    }

    $consulta_string_matriz="SELECT `gcmi_id`, `gcmi_matriz`, `gcmi_item_tipo`, `gcmi_item_consecutivo`, `gcmi_item_orden`, `gcmi_descripcion`, `gcmi_peso`, `gcmi_calificable`, `gcmi_grupo_peso`, `gcmi_visible`, `gcmi_tipo_error`, `gcmi_grupo_id`, `gcmi_subgrupo_id`, `gcmi_item_id`, `gcmi_subitem_id` FROM `gestion_calidad_matriz_item` WHERE `gcmi_matriz`=? ORDER BY `gcmi_item_consecutivo` ASC";
    $consulta_registros_matriz = $enlace_db->prepare($consulta_string_matriz);
    $consulta_registros_matriz->bind_param("s", $resultado_registros_monitoreo[0][1]);
    $consulta_registros_matriz->execute();
    $resultado_registros_matriz = $consulta_registros_matriz->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_historial="SELECT `gcmh_id`, `gcmh_monitoreo`, `gcmh_tipo_cambio`, `gcmh_comentarios`, `gcmh_registro_usuario`, `gcmh_registro_fecha`, TUR.`usu_nombres_apellidos` FROM `gestion_calidad_monitoreo_historial` LEFT JOIN `administrador_usuario` AS TUR ON `gestion_calidad_monitoreo_historial`.`gcmh_registro_usuario`=TUR.`usu_id` WHERE `gcmh_monitoreo`=? ORDER BY `gcmh_registro_fecha` DESC";

    $consulta_registros_historial = $enlace_db->prepare($consulta_string_historial);
    $consulta_registros_historial->bind_param("s", $id_registro);
    $consulta_registros_historial->execute();
    $resultado_registros_historial = $consulta_registros_historial->get_result()->fetch_all(MYSQLI_NUM);

    $array_header[]='Monitoreo';
    $array_header[]='Agente';
    $array_header[]='Matriz';
    $array_header[]='Canal';
    $array_header[]='Nota ENC';
    $array_header[]='Nota ECUF';
    $array_header[]='Nota ECN';
    $array_header[]='Dependencia';
    $array_header[]='Número Interacción';
    $array_header[]='Identificación Ciudadano';
    $array_header[]='Fecha Interacción';
    $array_header[]='Tipo Monitoreo';
    $array_header[]='Piloto';
    $array_header[]='Supervisor';
    $array_header[]='Solución primer contacto?';
    $array_header[]='Causal NO solución';
    $array_header[]='Programa';
    $array_header[]='Tipificación';
    $array_header[]='Sub-Tipificación';
    $array_header[]='Atención WOW';
    $array_header[]='Se presenta VOC';
    if($resultado_registros_monitoreo[0][20]=='Si') {
        $array_header[]='Segmento';
        $array_header[]='Tabulación VOC';
        $array_header[]='VOC';
        $array_header[]='VOC Emoción Inicial';
        $array_header[]='VOC Emoción Final';
        $array_header[]='Qué le activó';
        $array_header[]='Atribuible';
    }
    $array_header[] = 'Competencia';
    $array_header[]='Observaciones';
    $array_header[]='Registrado por';
    $array_header[]='Fecha registro';

    $array_datos[]=$resultado_registros_monitoreo[0][0];
    $array_datos[]=$resultado_registros_monitoreo[0][37];
    $array_datos[]=$resultado_registros_monitoreo[0][2];
    $array_datos[]=$resultado_registros_monitoreo[0][47];
    $array_datos[]=$resultado_registros_monitoreo[0][10];
    $array_datos[]=$resultado_registros_monitoreo[0][12];
    $array_datos[]=$resultado_registros_monitoreo[0][11];
    $array_datos[]=$resultado_registros_monitoreo[0][5];
    $array_datos[]=$resultado_registros_monitoreo[0][7];
    $array_datos[]=$resultado_registros_monitoreo[0][6];
    $array_datos[]=$resultado_registros_monitoreo[0][4];
    $array_datos[]=$resultado_registros_monitoreo[0][8];
    $array_datos[]=$resultado_registros_monitoreo[0][38];
    $array_datos[]=$resultado_registros_monitoreo[0][39];
    $array_datos[]=$resultado_registros_monitoreo[0][14];
    $array_datos[]=$resultado_registros_monitoreo[0][15];
    $array_datos[]=$resultado_registros_monitoreo[0][16];
    $array_datos[]=$resultado_registros_monitoreo[0][17];
    $array_datos[]=$resultado_registros_monitoreo[0][18];
    $array_datos[]=$resultado_registros_monitoreo[0][19];
    $array_datos[]=$resultado_registros_monitoreo[0][20];
    if($resultado_registros_monitoreo[0][20]=='Si') {
        $array_datos[]=$resultado_registros_monitoreo[0][21];
        $array_datos[]=$resultado_registros_monitoreo[0][22];
        $array_datos[]=$resultado_registros_monitoreo[0][23];
        $array_datos[]=$resultado_registros_monitoreo[0][23];
        $array_datos[]=$resultado_registros_monitoreo[0][25];
        $array_datos[]=$resultado_registros_monitoreo[0][26];
        $array_datos[]=$resultado_registros_monitoreo[0][27];
    }
    $array_datos[] = $resultado_registros_monitoreo[0][37];
    $array_datos[]=$resultado_registros_monitoreo[0][9];
    $array_datos[]=$resultado_registros_monitoreo[0][40];
    $array_datos[]=$resultado_registros_monitoreo[0][36];

    $pdf=new PDF_MC_Table('P','mm','Legal');
    $pdf->SetTitle(utf8_decode('Gestión Calidad Informe Monitoreo - '.$resultado_registros_monitoreo[0][0]));
    $pdf->SetMargins(15, 10, 15);
    $pdf->AddPage();
    $pdf->AliasNbPages();
    
    $pdf->SetFont('times','B',12);
    $pdf->setTextColor(255, 255, 255);
    $pdf->setFillColor(38, 41, 68);
    $pdf->Cell(186,8,utf8_decode('Informe Monitoreo - '.$resultado_registros_monitoreo[0][0]),'',1,'C',1);

    $pdf->Cell(186,3,'','',1,'C');
    $pdf->SetFont('times','B',12);
    $pdf->setFillColor(38, 41, 68);
    $pdf->setTextColor(38, 41, 68);
    $pdf->Cell(3,10,'','',0,'L',1);
    $pdf->Cell(12,10,utf8_decode('Información General'),'',1,'L');

    $pdf->Cell(186,5,'','',1,'C');
    $pdf->SetFont('times','',10);

    $pdf->SetWidths(array(40, 146));
    for ($i=0; $i < count($array_header); $i++){
        $pdf->Row(array(utf8_decode($array_header[$i]), utf8_decode($array_datos[$i])));
    }

    //SECCIÓN HISTORIAL DE GESTIÓN
    $pdf->Cell(186,5,'','',1,'C');
    $pdf->SetFont('times','B',12);
    $pdf->setFillColor(38, 41, 68);
    $pdf->setTextColor(38, 41, 68);
    $pdf->Cell(3,10,'','',0,'L',1);
    $pdf->Cell(12,10,utf8_decode('Historial de Gestión'),'',1,'L');

    $pdf->Cell(186,5,'','',1,'C');
    
    $pdf->SetFont('times','B',7);
    $pdf->Cell(30,4,utf8_decode('TIPO'),'TLRB',0,'C');
    $pdf->Cell(100,4,utf8_decode('OBSERVACIONES'),'TLRB',0,'C');
    $pdf->Cell(30,4,utf8_decode('USUARIO REGISTRO'),'TRB',0,'C');
    $pdf->Cell(26,4,utf8_decode('FECHA REGISTRO'),'TRB',1,'C');


    if (count($resultado_registros_historial)>0) {
        $pdf->SetFont('times','',7);
        $pdf->SetWidths(array(30, 100, 30, 26));
        for ($i=0; $i < count($resultado_registros_historial); $i++) { 
            $pdf->Row(array(utf8_decode($resultado_registros_historial[$i][2]), utf8_decode($resultado_registros_historial[$i][3]), utf8_decode($resultado_registros_historial[$i][6]), utf8_decode($resultado_registros_historial[$i][5])));
        }
    } else {
        $pdf->Cell(186,5,utf8_decode('¡No se encontraron registros!'),'',1,'L');
    }
    
    $pdf->Cell(186,5,'','',1,'C');

    //SECCIÓN MATRIZ EVALUACIÓN
    $pdf->Cell(186,5,'','',1,'C');
    $pdf->SetFont('times','B',12);
    $pdf->setFillColor(38, 41, 68);
    $pdf->setTextColor(38, 41, 68);
    $pdf->Cell(3,10,'','',0,'L',1);
    $pdf->Cell(12,10,utf8_decode('Matriz Evaluación'),'',1,'L');

    $pdf->Cell(186,5,'','',1,'C');
    
    $pdf->SetFont('times','B',7);
    $pdf->Cell(10,4,utf8_decode(''),'TLRB',0,'C');
    $pdf->Cell(100,4,utf8_decode('ATRIBUTOS DE EVALUACIÓN'),'TLRB',0,'C');
    $pdf->Cell(10,4,utf8_decode('PESO'),'TRB',0,'C');
    $pdf->Cell(4,4,utf8_decode('SI'),'TRB',0,'C');
    $pdf->Cell(4,4,utf8_decode('NO'),'TRB',0,'C');
    $pdf->Cell(58,4,utf8_decode('COMENTARIOS'),'TRB',1,'C');

    $pdf->SetFont('times','',7);
    $pdf->SetWidths(array(10, 100, 10, 4, 4, 58));

    for ($i=0; $i < count($resultado_registros_matriz); $i++) { 
        if($resultado_registros_matriz[$i][9]=="Si") {
            if($array_respuestas[$resultado_registros_matriz[$i][0]]=="Si") {
                $marcasi='x';
                $marcano='';
                $comentarios='';
            } elseif($array_respuestas[$resultado_registros_matriz[$i][0]]=="No") {
                $marcasi='';
                $marcano='x';
                $comentarios=$array_comentarios[$resultado_registros_matriz[$i][0]];
            }
            $pdf->Row(array(utf8_decode($resultado_registros_matriz[$i][3]), utf8_decode($resultado_registros_matriz[$i][5]), utf8_decode($resultado_registros_matriz[$i][6].'%'), utf8_decode($marcasi), utf8_decode($marcano), utf8_decode($comentarios)));
        }
    }
    
    $pdf->Output(utf8_decode('Gestión Calidad Informe Monitoreo - '.$resultado_registros_monitoreo[0][0].'.pdf'),'D');
?>
