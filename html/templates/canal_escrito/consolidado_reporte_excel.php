<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Canal Escrito";
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
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']);

        $titulo_reporte="Canal Escrito-Interacciones-".$tipo_reporte."-".date('Y-m-d H_i_s').".xlsx";
        
        // Inicializa variable tipo array
        $data_consulta=array();

        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin.' 23:59:59');

        $filtro_fechas_reparto_p1=" AND `cepc_registro_fecha`>=? AND `cepc_registro_fecha`<=?";
        $filtro_fechas_reparto_p2=" AND `ceaff_registro_fecha`>=? AND `ceaff_registro_fecha`<=?";
        $filtro_fechas_reparto_p3=" AND `ceff_registro_fecha`>=? AND `ceff_registro_fecha`<=?";
        $filtro_fechas_reparto_p4=" AND `ceip_registro_fecha`>=? AND `ceip_registro_fecha`<=?";
        $filtro_fechas_reparto_p5=" AND `cepfa_registro_fecha`>=? AND `cepfa_registro_fecha`<=?";
        $filtro_fechas_reparto_p6=" AND `ceaf_registro_fecha`>=? AND `ceaf_registro_fecha`<=?";
        $filtro_fechas_reparto_p7=" AND `ceft_registro_fecha`>=? AND `ceft_registro_fecha`<=?";
        $filtro_fechas_reparto_p8=" AND `cep_registro_fecha`>=? AND `cep_registro_fecha`<=?";
        $filtro_fechas_reparto_p9=" AND `celtr_registro_fecha`>=? AND `celtr_registro_fecha`<=?";
        $filtro_fechas_reparto_p10=" AND `cesew_registro_fecha`>=? AND `cesew_registro_fecha`<=?";
        $filtro_fechas_reparto_p11=" AND `cescd_registro_fecha`>=? AND `cescd_registro_fecha`<=?";
        $filtro_fechas_reparto_p12=" AND `cesr_registro_fecha`>=? AND `cesr_registro_fecha`<=?";
        $filtro_fechas_reparto_p13=" AND `cest_registro_fecha`>=? AND `cest_registro_fecha`<=?";
        $filtro_fechas_reparto_p14=" AND `cesit_registro_fecha`>=? AND `cesit_registro_fecha`<=?";


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
    $spreadsheet->getActiveSheet()->setTitle('Reporte Interacciones');

    //Estilos de la Hoja 0
    $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(80);
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    
    $spreadsheet->getActiveSheet()->getStyle('A3:E3')->applyFromArray($styleArrayTitulos);

    $spreadsheet->getActiveSheet()->setAutoFilter('A3:E3');
    $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);

    // Escribiendo los títulos
    $spreadsheet->getActiveSheet()->setCellValue('A3','Id Transacción');
    $spreadsheet->getActiveSheet()->setCellValue('B3','Doc. Agente');
    $spreadsheet->getActiveSheet()->setCellValue('C3','Nombres y Apellidos');
    $spreadsheet->getActiveSheet()->setCellValue('D3','Fecha - Hora');
    $spreadsheet->getActiveSheet()->setCellValue('E3','Canal');

    $control_filas=4;
        //1. Proyección Consolidación
            $consulta_string_reparto_p1="SELECT `cepc_id`, `cepc_radicado_entrada`, `cepc_tipologia`, `cepc_grupo_responsable`, `cepc_grupo_prorrogas`, `cepc_notificar`, `cepc_registro_usuario`, `cepc_registro_fecha`, TIPOLOGIA.`ceco_valor`, GRESPONSABLE.`ceco_valor`, GPRORROGAS.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_proyeccion_consolidacion` LEFT JOIN `gestion_ce_configuracion` AS TIPOLOGIA ON `gestion_cerep_proyeccion_consolidacion`.`cepc_tipologia`=TIPOLOGIA.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS GRESPONSABLE ON `gestion_cerep_proyeccion_consolidacion`.`cepc_grupo_responsable`=GRESPONSABLE.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS GPRORROGAS ON `gestion_cerep_proyeccion_consolidacion`.`cepc_grupo_prorrogas`=GPRORROGAS.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_consolidacion`.`cepc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p1."";
            $consulta_registros_reparto_p1 = $enlace_db->prepare($consulta_string_reparto_p1);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p1->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p1->execute();
            $resultado_registros_reparto_p1 = $consulta_registros_reparto_p1->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p1)) {
              for ($i=0; $i < count($resultado_registros_reparto_p1); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p1[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p1[$i][6]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p1[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p1[$i][7]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 1. Proyección Consolidación');
                $control_filas++;
              }
            }


        //2. Aprobación Firma FA
            $consulta_string_reparto_p2="SELECT `ceaff_id`, `ceaff_radicado`, `ceaff_proyector`, `ceaff_estado`, `ceaff_observaciones`, `ceaff_notificar`, `ceaff_registro_usuario`, `ceaff_registro_fecha`, PROYECTOR.`usu_nombres_apellidos`, ESTADO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_aprobacion_firma_fa` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_proyector`=PROYECTOR.`usu_id` LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_estado`=ESTADO.`ceco_id`  LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p2."";
            $consulta_registros_reparto_p2 = $enlace_db->prepare($consulta_string_reparto_p2);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p2->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p2->execute();
            $resultado_registros_reparto_p2 = $consulta_registros_reparto_p2->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p2)) {
              for ($i=0; $i < count($resultado_registros_reparto_p2); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p2[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p2[$i][6]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p2[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p2[$i][7]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 2. Aprobación Firma FA');
                $control_filas++;
                
              }
            }


        //3. Firma FA
            $consulta_string_reparto_p3="SELECT `ceff_id`, `ceff_radicado_entrada`, `ceff_radicado_salida`, `ceff_modalidad_envio`, `ceff_observaciones`, `ceff_notificar`, `ceff_registro_usuario`, `ceff_registro_fecha`, MENVIO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_firma_fa` LEFT JOIN `gestion_ce_configuracion` AS MENVIO ON `gestion_cerep_firma_fa`.`ceff_modalidad_envio`=MENVIO.`ceco_id`  LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_firma_fa`.`ceff_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p3."";
            $consulta_registros_reparto_p3 = $enlace_db->prepare($consulta_string_reparto_p3);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p3->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p3->execute();
            $resultado_registros_reparto_p3 = $consulta_registros_reparto_p3->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p3)) {
              for ($i=0; $i < count($resultado_registros_reparto_p3); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p3[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p3[$i][6]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p3[$i][9]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p3[$i][7]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 3. Firma FA');
                $control_filas++;
               
              }
            }


        //4. Inspección Proyección
            $consulta_string_reparto_p4="SELECT `ceip_id`, `ceip_radicado_entrada`, `ceip_proyector_carta`, `ceip_estado`, `ceip_tipo_rechazo`, `ceip_observaciones`, `ceip_notificar`, `ceip_registro_usuario`, `ceip_registro_fecha`, PROYECTOR.`usu_nombres_apellidos`, ESTADO.`ceco_valor`, TRECHAZO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_inspeccion_proyeccion` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_inspeccion_proyeccion`.`ceip_proyector_carta`=PROYECTOR.`usu_id` LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cerep_inspeccion_proyeccion`.`ceip_estado`=ESTADO.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS TRECHAZO ON `gestion_cerep_inspeccion_proyeccion`.`ceip_tipo_rechazo`=TRECHAZO.`ceco_id`  LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_inspeccion_proyeccion`.`ceip_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p4."";
            $consulta_registros_reparto_p4 = $enlace_db->prepare($consulta_string_reparto_p4);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p4->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p4->execute();
            $resultado_registros_reparto_p4 = $consulta_registros_reparto_p4->get_result()->fetch_all(MYSQLI_NUM);

            $consulta_string_reparto_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='inspeccion_proyeccion' ORDER BY `ceco_campo`, `ceco_valor`";
            $consulta_registros_reparto_parametros = $enlace_db->prepare($consulta_string_reparto_parametros);
            $consulta_registros_reparto_parametros->execute();
            $resultado_registros_reparto_parametros = $consulta_registros_reparto_parametros->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p4)) {
              $array_datos_gestion['p4']['tipo_rechazo_lista']=array();
              for ($i=0; $i < count($resultado_registros_reparto_p4); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p4[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p4[$i][7]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p4[$i][12]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p4[$i][8]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 4. Inspección Proyección');
                $control_filas++;
                
              }
            }


        //5. Proyección FA
            $consulta_string_reparto_p5="SELECT `cepfa_id`, `cepfa_radicado_entrada`, `cepfa_documento_identidad`, `cepfa_nombre_ciudadano`, `cepfa_correo_direccion`, `cepfa_departamento`, `cepfa_solicitud_novedad`, `cepfa_observaciones`, `cepfa_notificar`, `cepfa_registro_usuario`, `cepfa_registro_fecha`, DPTO.`ciu_departamento`, SOLICITUD.`ceco_valor`, TU.`usu_nombres_apellidos`, `cepfa_abogado_aprobador`, APROBADOR.`usu_nombres_apellidos` FROM `gestion_cerep_proyeccion_fa` LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cerep_proyeccion_fa`.`cepfa_abogado_aprobador`=APROBADOR.`usu_id` LEFT JOIN `administrador_departamentos` AS DPTO ON `gestion_cerep_proyeccion_fa`.`cepfa_departamento`=DPTO.`ciu_codigo` LEFT JOIN `gestion_ce_configuracion` AS SOLICITUD ON `gestion_cerep_proyeccion_fa`.`cepfa_solicitud_novedad`=SOLICITUD.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_fa`.`cepfa_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p5."";
            $consulta_registros_reparto_p5 = $enlace_db->prepare($consulta_string_reparto_p5);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p5->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p5->execute();
            $resultado_registros_reparto_p5 = $consulta_registros_reparto_p5->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p5)) {
              for ($i=0; $i < count($resultado_registros_reparto_p5); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p5[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p5[$i][9]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p5[$i][13]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p5[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 5. Proyección FA');
                $control_filas++;
                
              }
            }


        //6. Aprobación Firma
            $consulta_string_reparto_p6="SELECT `ceaf_id`, `ceaf_radicado`, `ceaf_tipificador`, `ceaf_proyector`, `ceaf_carta`, `ceaf_estado`, `ceaf_observaciones`, `ceaf_afectacion`, `ceaf_notificar`, `ceaf_registro_usuario`, `ceaf_registro_fecha`, TIPIFICADOR.`usu_nombres_apellidos`, PROYECTOR.`usu_nombres_apellidos`, CARTA.`ceco_valor`, ESTADO.`ceco_valor`, AFECTACION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_aprobacion_firma` LEFT JOIN `administrador_usuario` AS TIPIFICADOR ON `gestion_cerep_aprobacion_firma`.`ceaf_tipificador`=TIPIFICADOR.`usu_id` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_aprobacion_firma`.`ceaf_proyector`=PROYECTOR.`usu_id` LEFT JOIN `gestion_ce_configuracion` AS CARTA ON `gestion_cerep_aprobacion_firma`.`ceaf_carta`=CARTA.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cerep_aprobacion_firma`.`ceaf_estado`=ESTADO.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS AFECTACION ON `gestion_cerep_aprobacion_firma`.`ceaf_afectacion`=AFECTACION.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_aprobacion_firma`.`ceaf_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p6."";
            $consulta_registros_reparto_p6 = $enlace_db->prepare($consulta_string_reparto_p6);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p6->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p6->execute();
            $resultado_registros_reparto_p6 = $consulta_registros_reparto_p6->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p6)) {
              for ($i=0; $i < count($resultado_registros_reparto_p6); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p6[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p6[$i][9]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p6[$i][16]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p6[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 6. Aprobación Firma');
                $control_filas++;
                

              }
            }


        //7. Firma Traslados
            $consulta_string_reparto_p7="SELECT `ceft_id`, `ceft_radicado_entrada`, `ceft_radicado_salida`, `ceft_rechazos`, `ceft_forma`, `ceft_proyector`, `ceft_inspector`, `ceft_aprobador`, `ceft_observaciones`, `ceft_notificar`, `ceft_registro_usuario`, `ceft_registro_fecha`, RECHAZOS.`ceco_valor`, FORMA.`ceco_valor`, TU.`usu_nombres_apellidos`, PROYECTOR.`usu_nombres_apellidos`, INSPECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos` FROM `gestion_cerep_firma_traslados` LEFT JOIN `gestion_ce_configuracion` AS RECHAZOS ON `gestion_cerep_firma_traslados`.`ceft_rechazos`=RECHAZOS.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS FORMA ON `gestion_cerep_firma_traslados`.`ceft_forma`=FORMA.`ceco_id` LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cerep_firma_traslados`.`ceft_proyector`=PROYECTOR.`usu_id` LEFT JOIN `administrador_usuario` AS INSPECTOR ON `gestion_cerep_firma_traslados`.`ceft_inspector`=INSPECTOR.`usu_id` LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cerep_firma_traslados`.`ceft_aprobador`=APROBADOR.`usu_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_firma_traslados`.`ceft_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p7."";
            $consulta_registros_reparto_p7 = $enlace_db->prepare($consulta_string_reparto_p7);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p7->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p7->execute();
            $resultado_registros_reparto_p7 = $consulta_registros_reparto_p7->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p7)) {
                for ($i=0; $i < count($resultado_registros_reparto_p7); $i++) { 
                    $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p7[$i][1]);
                    $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p7[$i][10]);
                    $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p7[$i][14]);
                    $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p7[$i][11]);
                    $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 7. Firma Traslados');
                    $control_filas++;
            
                }
            }


        //8. Proyectores
            $consulta_string_reparto_p8="SELECT `cep_id`, `cep_radicado_entrada`, `cep_direccionamiento`, `cep_observacion_traslado`, `cep_documento_identidad`, `cep_nombre_ciudadano`, `cep_correo_direccion`, `cep_departamento`, `cep_novedad_radicado`, `cep_observaciones`, `cep_notificar`, `cep_registro_usuario`, `cep_registro_fecha`, DIRECCIONAMIENTO.`ceco_valor`, DEPARTAMENTO.`ciu_departamento`, NOVEDAD.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_proyectores` LEFT JOIN `gestion_ce_configuracion` AS DIRECCIONAMIENTO ON `gestion_cerep_proyectores`.`cep_direccionamiento`=DIRECCIONAMIENTO.`ceco_id` LEFT JOIN `administrador_departamentos` AS DEPARTAMENTO ON `gestion_cerep_proyectores`.`cep_departamento`=DEPARTAMENTO.`ciu_codigo` LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cerep_proyectores`.`cep_novedad_radicado`=NOVEDAD.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyectores`.`cep_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p8."";
            $consulta_registros_reparto_p8 = $enlace_db->prepare($consulta_string_reparto_p8);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p8->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p8->execute();
            $resultado_registros_reparto_p8 = $consulta_registros_reparto_p8->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p8)) {
              for ($i=0; $i < count($resultado_registros_reparto_p8); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p8[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p8[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p8[$i][16]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p8[$i][12]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 8. Proyectores');
                $control_filas++;
                
              }

            }


        //9. Seguimiento Lanzamientos TR
            $consulta_string_reparto_p9="SELECT `celtr_id`, `celtr_radicado`, `celtr_area`, `celtr_responsable_grupo`, `celtr_observaciones`, `celtr_notificar`, `celtr_registro_usuario`, `celtr_registro_fecha`, AREA.`ceco_valor`, GRUPO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_lanzamientos_tr` LEFT JOIN `gestion_ce_configuracion` AS AREA ON `gestion_cerep_seguimiento_lanzamientos_tr`.`celtr_area`=AREA.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS GRUPO ON `gestion_cerep_seguimiento_lanzamientos_tr`.`celtr_responsable_grupo`=GRUPO.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_lanzamientos_tr`.`celtr_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p9."";
            $consulta_registros_reparto_p9 = $enlace_db->prepare($consulta_string_reparto_p9);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p9->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p9->execute();
            $resultado_registros_reparto_p9 = $consulta_registros_reparto_p9->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p9)) {
              for ($i=0; $i < count($resultado_registros_reparto_p9); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p9[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p9[$i][6]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p9[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p9[$i][7]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 9. Seguimiento Lanzamientos TR');
                $control_filas++;
                
              }

            }


        //10. Seguimiento Envíos Web
            $consulta_string_reparto_p10="SELECT `cesew_id`, `cesew_radicado_entrada`, `cesew_radicado_salida`, `cesew_tipo_envio`, `cesew_estado`, `cesew_observaciones`, `cesew_notificar`, `cesew_registro_usuario`, `cesew_registro_fecha`, TIPOENVIO.`ceco_valor`, ESTADO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_envios_web` LEFT JOIN `gestion_ce_configuracion` AS TIPOENVIO ON `gestion_cerep_seguimiento_envios_web`.`cesew_tipo_envio`=TIPOENVIO.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cerep_seguimiento_envios_web`.`cesew_estado`=ESTADO.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_envios_web`.`cesew_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p10."";
            $consulta_registros_reparto_p10 = $enlace_db->prepare($consulta_string_reparto_p10);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p10->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p10->execute();
            $resultado_registros_reparto_p10 = $consulta_registros_reparto_p10->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p10)) {
              for ($i=0; $i < count($resultado_registros_reparto_p10); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p10[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p10[$i][7]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p10[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p10[$i][8]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 10. Seguimiento Envíos Web');
                $control_filas++;
                
              }

            }


        //11. Seguimiento Cargue Documentos
            $consulta_string_reparto_p11="SELECT `cescd_id`, `cescd_radicado_entrada`, `cescd_radicado_salida`, `cescd_novedad`, `cescd_observaciones`, `cescd_notificar`, `cescd_registro_usuario`, `cescd_registro_fecha`, NOVEDAD.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_cargue_documentos` LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cerep_seguimiento_cargue_documentos`.`cescd_novedad`=NOVEDAD.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_cargue_documentos`.`cescd_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p11."";
            $consulta_registros_reparto_p11 = $enlace_db->prepare($consulta_string_reparto_p11);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p11->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p11->execute();
            $resultado_registros_reparto_p11 = $consulta_registros_reparto_p11->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p11)) {
              for ($i=0; $i < count($resultado_registros_reparto_p11); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p11[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p11[$i][6]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p11[$i][9]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p11[$i][7]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 11. Seguimiento Cargue Documentos');
                $control_filas++;
                
              }

            }

         
        //12. Seguimiento Radicación
            $consulta_string_reparto_p12="SELECT `cesr_id`, `cesr_correo_ciudadano`, `cesr_fecha_ingreso_correo`, `cesr_dependencia`, `cesr_senotifica`, `cesr_observaciones`, `cesr_notificar`, `cesr_registro_usuario`, `cesr_registro_fecha`, DEPENDENCIA.`ceco_valor`, NOTIFICA.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_radicacion` LEFT JOIN `gestion_ce_configuracion` AS DEPENDENCIA ON `gestion_cerep_seguimiento_radicacion`.`cesr_dependencia`=DEPENDENCIA.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS NOTIFICA ON `gestion_cerep_seguimiento_radicacion`.`cesr_senotifica`=NOTIFICA.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_radicacion`.`cesr_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p12."";
            $consulta_registros_reparto_p12 = $enlace_db->prepare($consulta_string_reparto_p12);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p12->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p12->execute();
            $resultado_registros_reparto_p12 = $consulta_registros_reparto_p12->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p12)) {
              for ($i=0; $i < count($resultado_registros_reparto_p12); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p12[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p12[$i][7]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p12[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p12[$i][8]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 12. Seguimiento Radicación');
                $control_filas++;
                
              }

            }


        //13. Seguimiento Tipificaciones
            $consulta_string_reparto_p13="SELECT `cest_id`, `cest_radicado`, `cest_requiere_traslado`, `cest_oficio_especial`, `cest_observaciones`, `cest_notificar`, `cest_registro_usuario`, `cest_registro_fecha`, TRASLADO.`ceco_valor`, OFICIO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_tipificaciones` LEFT JOIN `gestion_ce_configuracion` AS TRASLADO ON `gestion_cerep_seguimiento_tipificaciones`.`cest_requiere_traslado`=TRASLADO.`ceco_id` LEFT JOIN `gestion_ce_configuracion` AS OFICIO ON `gestion_cerep_seguimiento_tipificaciones`.`cest_oficio_especial`=OFICIO.`ceco_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_tipificaciones`.`cest_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p13."";
            $consulta_registros_reparto_p13 = $enlace_db->prepare($consulta_string_reparto_p13);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p13->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p13->execute();
            $resultado_registros_reparto_p13 = $consulta_registros_reparto_p13->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p13)) {
              for ($i=0; $i < count($resultado_registros_reparto_p13); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p13[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p13[$i][6]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p13[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p13[$i][7]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 13. Seguimiento Tipificaciones');
                $control_filas++;
                
              }

            }


        //14. Seguimiento Inspección Tipificación
            $consulta_string_reparto_p14="SELECT `cesit_id`, `cesit_radicado`, `cesit_abogado_tipificador`, `cesit_abogado_aprobador`, `cesit_traslado_entidades`, `cesit_traslado_entidades_errado`, `cesit_asignaciones_internas`, `cesit_forma_correcta_peticion`, `cesit_traslado_entidades_errado_senalar`, `cesit_asignacion_errada`, `cesit_asignacion_errada_2`, `cesit_observaciones_asignacion`, `cesit_relaciona_informacion_radicacion`, `cesit_campo_errado`, `cesit_diligencia_datos_solicitante`, `cesit_campo_errado_2`, `cesit_observaciones_diligencia_formulario`, `cesit_notificar`, `cesit_registro_usuario`, `cesit_registro_fecha`, abogado_tipificador.`usu_nombres_apellidos`, abogado_aprobador.`usu_nombres_apellidos`, traslado_entidades.`ceco_valor`, traslado_entidades_errado.`ceco_valor`, asignaciones_internas.`ceco_valor`, forma_correcta_peticion.`ceco_valor`, traslado_entidades_errado_senalar.`ceco_valor`, asignacion_errada.`ceco_valor`, asignacion_errada_2.`ceco_valor`, relaciona_informacion_radicacion.`ceco_valor`, campo_errado.`ceco_valor`, diligencia_datos_solicitante.`ceco_valor`, campo_errado_2.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_inspeccion_tipificacion` LEFT JOIN `administrador_usuario` AS abogado_tipificador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_tipificador`=abogado_tipificador.`usu_id`
             LEFT JOIN `administrador_usuario` AS abogado_aprobador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_aprobador`=abogado_aprobador.`usu_id`
             LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades`=traslado_entidades.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado`=traslado_entidades_errado.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS asignaciones_internas ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignaciones_internas`=asignaciones_internas.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS forma_correcta_peticion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_forma_correcta_peticion`=forma_correcta_peticion.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado_senalar ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado_senalar`=traslado_entidades_errado_senalar.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada`=asignacion_errada.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada_2`=asignacion_errada_2.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS relaciona_informacion_radicacion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_relaciona_informacion_radicacion`=relaciona_informacion_radicacion.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS campo_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado`=campo_errado.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS diligencia_datos_solicitante ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_diligencia_datos_solicitante`=diligencia_datos_solicitante.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS campo_errado_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado_2`=campo_errado_2.`ceco_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_reparto_p14."";
            $consulta_registros_reparto_p14 = $enlace_db->prepare($consulta_string_reparto_p14);
            if (count($data_consulta)>0) {
                $consulta_registros_reparto_p14->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_reparto_p14->execute();
            $resultado_registros_reparto_p14 = $consulta_registros_reparto_p14->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_reparto_p14)) {
              for ($i=0; $i < count($resultado_registros_reparto_p14); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_reparto_p14[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_reparto_p14[$i][18]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_reparto_p14[$i][33]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_reparto_p14[$i][19]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Reparto - 14. Seguimiento Inspección Tipificación');
                $control_filas++;
                
              }

            }


        $filtro_fechas_jafoca_p1=" AND `cejpp_registro_fecha`>=? AND `cejpp_registro_fecha`<=?";
        $filtro_fechas_jafoca_p2=" AND `cejrp_registro_fecha`>=? AND `cejrp_registro_fecha`<=?";
        $filtro_fechas_jafoca_p3=" AND `cejrr_registro_fecha`>=? AND `cejrr_registro_fecha`<=?";
        $filtro_fechas_jafoca_p4=" AND `cejgc_registro_fecha`>=? AND `cejgc_registro_fecha`<=?";
        $filtro_fechas_jafoca_p5=" AND `cejgn_registro_fecha`>=? AND `cejgn_registro_fecha`<=?";
        $filtro_fechas_jafoca_p6=" AND `cejgp_registro_fecha`>=? AND `cejgp_registro_fecha`<=?";
        $filtro_fechas_jafoca_p7=" AND `cejga_registro_fecha`>=? AND `cejga_registro_fecha`<=?";
        $filtro_fechas_jafoca_p8=" AND `cejef_registro_fecha`>=? AND `cejef_registro_fecha`<=?";

        //1. Proyección de Peticiones Vivienda
            $consulta_string_jafoca_p1="SELECT `cejpp_id`, `cejpp_radicado_entrada`, `cejpp_proyector`, `cejpp_novedad_radicado`, `cejpp_formato`, `cejpp_identificacion_peticionario`, `cejpp_nombre_peticionario`, `cejpp_correo`, `cejpp_observaciones`, `cejpp_notificar`, `cejpp_registro_usuario`, `cejpp_registro_fecha`, NOVEDAD.`ceco_valor`, FORMATO.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_proyeccion_peticiones` LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_novedad_radicado`=NOVEDAD.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS FORMATO ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_formato`=FORMATO.`ceco_id`
             LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_proyector`=PROYECTOR.`usu_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_jafoca_p1."";
            $consulta_registros_jafoca_p1 = $enlace_db->prepare($consulta_string_jafoca_p1);
            if (count($data_consulta)>0) {
                $consulta_registros_jafoca_p1->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_jafoca_p1->execute();
            $resultado_registros_jafoca_p1 = $consulta_registros_jafoca_p1->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_jafoca_p1)) {
              for ($i=0; $i < count($resultado_registros_jafoca_p1); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_jafoca_p1[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_jafoca_p1[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_jafoca_p1[$i][15]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_jafoca_p1[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'JAFocalización - 1. Proyección de Peticiones Vivienda');
                $control_filas++;
                
              }

            }


        //2. Revisión de Peticiones Vivienda 
            $consulta_string_jafoca_p2="SELECT `cejrp_id`, `cejrp_radicado_entrada`, `cejrp_realiza_traslado`, `cejrp_aprobador`, `cejrp_proyector`, `cejrp_estado`, `cejrp_error_digitalizacion`, `cejrp_caso_particular`, `cejrp_observaciones`, `cejrp_notificar`, `cejrp_registro_usuario`, `cejrp_registro_fecha`, REALIZATRASLADO.`ceco_valor`, ESTADO.`ceco_valor`, ERRORDIGITA.`ceco_valor`, CASOPARTICULAR.`ceco_valor`, TU.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos`, PROYECTOR.`usu_nombres_apellidos` FROM `gestion_cejafo_revision_peticiones` 
              LEFT JOIN `gestion_ce_configuracion` AS REALIZATRASLADO ON `gestion_cejafo_revision_peticiones`.`cejrp_realiza_traslado`=REALIZATRASLADO.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cejafo_revision_peticiones`.`cejrp_estado`=ESTADO.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS ERRORDIGITA ON `gestion_cejafo_revision_peticiones`.`cejrp_error_digitalizacion`=ERRORDIGITA.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS CASOPARTICULAR ON `gestion_cejafo_revision_peticiones`.`cejrp_caso_particular`=CASOPARTICULAR.`ceco_id`
              LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cejafo_revision_peticiones`.`cejrp_aprobador`=APROBADOR.`usu_id`
              LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_revision_peticiones`.`cejrp_proyector`=PROYECTOR.`usu_id`
              LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_revision_peticiones`.`cejrp_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_jafoca_p2."";
            $consulta_registros_jafoca_p2 = $enlace_db->prepare($consulta_string_jafoca_p2);
            if (count($data_consulta)>0) {
                $consulta_registros_jafoca_p2->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_jafoca_p2->execute();
            $resultado_registros_jafoca_p2 = $consulta_registros_jafoca_p2->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_jafoca_p2)) {
              for ($i=0; $i < count($resultado_registros_jafoca_p2); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_jafoca_p2[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_jafoca_p2[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_jafoca_p2[$i][16]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_jafoca_p2[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'JAFocalización - 2. Revisión de Peticiones Vivienda ');
                $control_filas++;
                
              }

            }


        //3. Formato de Relación RAE JeA
            $consulta_string_jafoca_p3="SELECT `cejrr_id`, `cejrr_radicado_salida`, `cejrr_radicado_entrada`, `cejrr_destinatario`, `cejrr_direccion`, `cejrr_municipio`, `cejrr_modalidad_envio`, `cejrr_srjv`, `cejrr_proyector`, `cejrr_aprobador`, `cejrr_firma`, `cejrr_cedula_firmante`, `cejrr_fecha_gestion_rae`, `cejrr_fecha_envio`, `cejrr_qq`, `cejrr_observaciones`, `cejrr_notificar`, `cejrr_registro_usuario`, `cejrr_registro_fecha`, MODALIDADENVIO.`ceco_valor`, SRJV.`ceco_valor`, FIRMA.`ceco_valor`, TU.`usu_nombres_apellidos`, TCIU.`ciu_departamento`, TCIU.`ciu_municipio`, PROYECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos` FROM `gestion_cejafo_relacion_rae` LEFT JOIN `gestion_ce_configuracion` AS MODALIDADENVIO ON `gestion_cejafo_relacion_rae`.`cejrr_modalidad_envio`=MODALIDADENVIO.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS SRJV ON `gestion_cejafo_relacion_rae`.`cejrr_srjv`=SRJV.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS FIRMA ON `gestion_cejafo_relacion_rae`.`cejrr_firma`=FIRMA.`ceco_id`
             LEFT JOIN `administrador_ciudades` AS TCIU ON `gestion_cejafo_relacion_rae`.`cejrr_municipio`=TCIU.`ciu_codigo`
             LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_relacion_rae`.`cejrr_proyector`=PROYECTOR.`usu_id`
             LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cejafo_relacion_rae`.`cejrr_aprobador`=APROBADOR.`usu_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_relacion_rae`.`cejrr_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_jafoca_p3."";
            $consulta_registros_jafoca_p3 = $enlace_db->prepare($consulta_string_jafoca_p3);
            if (count($data_consulta)>0) {
                $consulta_registros_jafoca_p3->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_jafoca_p3->execute();
            $resultado_registros_jafoca_p3 = $consulta_registros_jafoca_p3->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_jafoca_p3)) {
              for ($i=0; $i < count($resultado_registros_jafoca_p3); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_jafoca_p3[$i][2]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_jafoca_p3[$i][17]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_jafoca_p3[$i][22]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_jafoca_p3[$i][18]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'JAFocalización - 3. Formato de Relación RAE JeA');
                $control_filas++;
                
              }

            }


        //4. Formato de Gestión de Correos
            $consulta_string_jafoca_p4="SELECT `cejgc_id`, `cejgc_fecha_recibido`, `cejgc_gestion`, `cejgc_documento`, `cejgc_tipo_documento`, `cejgc_nombre_completo`, `cejgc_codigo_beneficiario`, `cejgc_email`, `cejgc_celular`, `cejgc_departamento`, `cejgc_municipio`, `cejgc_categoria`, `cejgc_gestion_2`, `cejgc_tipificacion`, `cejgc_carga_di`, `cejgc_carga_soporte_bachiller`, `cejgc_observaciones`, `cejgc_notificar`, `cejgc_registro_usuario`, `cejgc_registro_fecha`, GESTION.`ceco_valor`, TIPODOCUMENTO.`ceco_valor`, TC.`ciu_departamento`, TC.`ciu_municipio`, CATEGORIA.`ceco_valor`, GESTION2.`ceco_valor`, TIPIFICACION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_gestion_correo` LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cejafo_gestion_correo`.`cejgc_gestion`=GESTION.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS TIPODOCUMENTO ON `gestion_cejafo_gestion_correo`.`cejgc_tipo_documento`=TIPODOCUMENTO.`ceco_id`
               LEFT JOIN `administrador_ciudades` AS TC ON `gestion_cejafo_gestion_correo`.`cejgc_municipio`=TC.`ciu_codigo`
               LEFT JOIN `gestion_ce_configuracion` AS CATEGORIA ON `gestion_cejafo_gestion_correo`.`cejgc_categoria`=CATEGORIA.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS GESTION2 ON `gestion_cejafo_gestion_correo`.`cejgc_gestion_2`=GESTION2.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS TIPIFICACION ON `gestion_cejafo_gestion_correo`.`cejgc_tipificacion`=TIPIFICACION.`ceco_id`
               LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_correo`.`cejgc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_jafoca_p4."";
            $consulta_registros_jafoca_p4 = $enlace_db->prepare($consulta_string_jafoca_p4);
            if (count($data_consulta)>0) {
                $consulta_registros_jafoca_p4->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_jafoca_p4->execute();
            $resultado_registros_jafoca_p4 = $consulta_registros_jafoca_p4->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_jafoca_p4)) {
              for ($i=0; $i < count($resultado_registros_jafoca_p4); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_jafoca_p4[$i][6]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_jafoca_p4[$i][18]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_jafoca_p4[$i][27]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_jafoca_p4[$i][19]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'JAFocalización - 4. Formato de Gestión de Correos');
                $control_filas++;
                
              }

            }


        //5. Formato Gestión de Novedades JeA
            $consulta_string_jafoca_p5="SELECT `cejgn_id`, `cejgn_id_novedad`, `cejgn_id_persona`, `cejgn_fecha_gestion`, `cejgn_estado`, `cejgn_tipo_rechazo`, `cejgn_observacion_rechazo`, `cejgn_correccion_datos_sija`, `cejgn_codigo_novedad`, `cejgn_observaciones`, `cejgn_notificar`, `cejgn_registro_usuario`, `cejgn_registro_fecha`, ESTADO.`ceco_valor`, TIPORECHAZO.`ceco_valor`, DATOSSIJA.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_gestion_novedades` 
             LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cejafo_gestion_novedades`.`cejgn_estado`=ESTADO.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS TIPORECHAZO ON `gestion_cejafo_gestion_novedades`.`cejgn_tipo_rechazo`=TIPORECHAZO.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS DATOSSIJA ON `gestion_cejafo_gestion_novedades`.`cejgn_correccion_datos_sija`=DATOSSIJA.`ceco_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_novedades`.`cejgn_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_jafoca_p5."";
            $consulta_registros_jafoca_p5 = $enlace_db->prepare($consulta_string_jafoca_p5);
            if (count($data_consulta)>0) {
                $consulta_registros_jafoca_p5->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_jafoca_p5->execute();
            $resultado_registros_jafoca_p5 = $consulta_registros_jafoca_p5->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_jafoca_p5)) {
              for ($i=0; $i < count($resultado_registros_jafoca_p5); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_jafoca_p5[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_jafoca_p5[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_jafoca_p5[$i][16]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_jafoca_p5[$i][12]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'JAFocalización - 5. Formato Gestión de Novedades JeA');
                $control_filas++;
                
              }

            }


        //6. Formato de Gestión de Peticiones JeA
            $consulta_string_jafoca_p6="SELECT `cejgp_id`, `cejgp_radicado`, `cejgp_proyector`, `cejgp_aprobador`, `cejgp_peticionario_identificacion`, `cejgp_peticionario_nombres`, `cejgp_correo_direccion`, `cejgp_municipio`, `cejgp_solicitud`, `cejgp_no_registra_sija`, `cejgp_tipo_documento`, `cejgp_fecha_nacimiento_solicitante`, `cejgp_novedad`, `cejgp_no_radicado`, `cejgp_novedad_adicional`, `cejgp_codigo_beneficiario`, `cejgp_gestion_actualizacion`, `cejgp_institucion_estudia`, `cejgp_nivel_formacion`, `cejgp_convenio`, `cejgp_observacion_actualizacion`, `cejgp_codigo_beneficiario_caso_especial`, `cejgp_municipio_reporte`, `cejgp_observacion_caso_especial`, `cejgp_observaciones`, `cejgp_notificar`, `cejgp_registro_usuario`, `cejgp_registro_fecha`, SOLICITUD.`ceco_valor`, SIJA.`ceco_valor`, TIPODOCUMENTO.`ceco_valor`, NOVEDAD.`ceco_valor`, NOVEDADADD.`ceco_valor`, GESTIONACTUALIZACION.`ceco_valor`, INSTITUCIONESTUDIA.`ceco_valor`, NIVELFORMACION.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos`, TC1.`ciu_departamento`, TC1.`ciu_municipio`, TC2.`ciu_departamento`, TC2.`ciu_municipio` FROM `gestion_cejafo_gestion_peticiones` 
              LEFT JOIN `gestion_ce_configuracion` AS SOLICITUD ON `gestion_cejafo_gestion_peticiones`.`cejgp_solicitud`=SOLICITUD.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS SIJA ON `gestion_cejafo_gestion_peticiones`.`cejgp_no_registra_sija`=SIJA.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS TIPODOCUMENTO ON `gestion_cejafo_gestion_peticiones`.`cejgp_tipo_documento`=TIPODOCUMENTO.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS NOVEDAD ON `gestion_cejafo_gestion_peticiones`.`cejgp_novedad`=NOVEDAD.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS NOVEDADADD ON `gestion_cejafo_gestion_peticiones`.`cejgp_novedad_adicional`=NOVEDADADD.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS GESTIONACTUALIZACION ON `gestion_cejafo_gestion_peticiones`.`cejgp_gestion_actualizacion`=GESTIONACTUALIZACION.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS INSTITUCIONESTUDIA ON `gestion_cejafo_gestion_peticiones`.`cejgp_institucion_estudia`=INSTITUCIONESTUDIA.`ceco_id`
              LEFT JOIN `gestion_ce_configuracion` AS NIVELFORMACION ON `gestion_cejafo_gestion_peticiones`.`cejgp_nivel_formacion`=NIVELFORMACION.`ceco_id`
              LEFT JOIN `administrador_ciudades` AS TC1 ON `gestion_cejafo_gestion_peticiones`.`cejgp_municipio`=TC1.`ciu_codigo`
              LEFT JOIN `administrador_ciudades` AS TC2 ON `gestion_cejafo_gestion_peticiones`.`cejgp_municipio_reporte`=TC2.`ciu_codigo`
              LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_gestion_peticiones`.`cejgp_proyector`=PROYECTOR.`usu_id`
              LEFT JOIN `administrador_usuario` AS APROBADOR ON `gestion_cejafo_gestion_peticiones`.`cejgp_aprobador`=APROBADOR.`usu_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_peticiones`.`cejgp_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_jafoca_p6."";
            $consulta_registros_jafoca_p6 = $enlace_db->prepare($consulta_string_jafoca_p6);
            if (count($data_consulta)>0) {
                $consulta_registros_jafoca_p6->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_jafoca_p6->execute();
            $resultado_registros_jafoca_p6 = $consulta_registros_jafoca_p6->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_jafoca_p6)) {
              for ($i=0; $i < count($resultado_registros_jafoca_p6); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_jafoca_p6[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_jafoca_p6[$i][26]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_jafoca_p6[$i][38]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_jafoca_p6[$i][27]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'JAFocalización - 6. Formato de Gestión de Peticiones JeA');
                $control_filas++;

              }

            }


        //7. Formato Gestión de Aprobación JeA
            $consulta_string_jafoca_p7="SELECT `cejga_id`, `cejga_radicado_entrada`, `cejga_proyector`, `cejga_revisor`, `cejga_cedula_aprobador`, `cejga_gestion`, `cejga_oportunidad_mejora`, `cejga_comentario_delta`, `cejga_observaciones`, `cejga_notificar`, `cejga_registro_usuario`, `cejga_registro_fecha`, GESTION.`ceco_valor`, OPORTUNIDAD.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, REVISOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos` FROM `gestion_cejafo_gestion_aprobacion` 
             LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cejafo_gestion_aprobacion`.`cejga_gestion`=GESTION.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS OPORTUNIDAD ON `gestion_cejafo_gestion_aprobacion`.`cejga_oportunidad_mejora`=OPORTUNIDAD.`ceco_id`
             LEFT JOIN `administrador_usuario` AS PROYECTOR ON `gestion_cejafo_gestion_aprobacion`.`cejga_proyector`=PROYECTOR.`usu_id`
             LEFT JOIN `administrador_usuario` AS REVISOR ON `gestion_cejafo_gestion_aprobacion`.`cejga_revisor`=REVISOR.`usu_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_aprobacion`.`cejga_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_jafoca_p7."";
            $consulta_registros_jafoca_p7 = $enlace_db->prepare($consulta_string_jafoca_p7);
            if (count($data_consulta)>0) {
                $consulta_registros_jafoca_p7->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_jafoca_p7->execute();
            $resultado_registros_jafoca_p7 = $consulta_registros_jafoca_p7->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_jafoca_p7)) {
              for ($i=0; $i < count($resultado_registros_jafoca_p7); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_jafoca_p7[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_jafoca_p7[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_jafoca_p7[$i][16]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_jafoca_p7[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'JAFocalización - 7. Formato Gestión de Aprobación JeA');
                $control_filas++;
                
              }

            }


        //8. Formato Entrega Física
            $consulta_string_jafoca_p8="SELECT `cejef_id`, `cejef_radicado_salida`, `cejef_radicado_entrada`, `cejef_destinatario`, `cejef_direccion`, `cejef_departamento`, `cejef_municipio`, `cejef_observaciones`, `cejef_notificar`, `cejef_registro_usuario`, `cejef_registro_fecha`, TU.`usu_nombres_apellidos`, TC.`ciu_departamento`, TC.`ciu_municipio` FROM `gestion_cejafo_entrega_fisica` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_cejafo_entrega_fisica`.`cejef_municipio`=TC.`ciu_codigo` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_entrega_fisica`.`cejef_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_jafoca_p8."";
            $consulta_registros_jafoca_p8 = $enlace_db->prepare($consulta_string_jafoca_p8);
            if (count($data_consulta)>0) {
                $consulta_registros_jafoca_p8->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_jafoca_p8->execute();
            $resultado_registros_jafoca_p8 = $consulta_registros_jafoca_p8->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_jafoca_p8)) {
              for ($i=0; $i < count($resultado_registros_jafoca_p8); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_jafoca_p8[$i][2]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_jafoca_p8[$i][9]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_jafoca_p8[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_jafoca_p8[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'JAFocalización - 8. Formato Entrega Física');
                $control_filas++;
                
              }

            }


        $filtro_fechas_tmnc_p1=" AND `cet_registro_fecha`>=? AND `cet_registro_fecha`<=?";
        $filtro_fechas_tmnc_p2=" AND `cetar_registro_fecha`>=? AND `cetar_registro_fecha`<=?";
        $filtro_fechas_tmnc_p3=" AND `cetc_registro_fecha`>=? AND `cetc_registro_fecha`<=?";
        $filtro_fechas_tmnc_p4=" AND `cete_registro_fecha`>=? AND `cete_registro_fecha`<=?";
        $filtro_fechas_tmnc_p5=" AND `cetfr_usuario_fecha`>=? AND `cetfr_usuario_fecha`<=?";
        $filtro_fechas_tmnc_p6=" AND `cetpc_registro_fecha`>=? AND `cetpc_registro_fecha`<=?";
        $filtro_fechas_tmnc_p7=" AND `cetcsg_registro_fecha`>=? AND `cetcsg_registro_fecha`<=?";
        $filtro_fechas_tmnc_p8=" AND `cetan_registro_fecha`>=? AND `cetan_registro_fecha`<=?";

        //1. Proyección de Respuestas
            $consulta_string_tmnc_p1="SELECT `cet_id`, `cet_radicado_entrada`, `cet_abogado_aprobacion`, `cet_documento_identidad`, `cet_nombre_ciudadano`, `cet_correo_direccion`, `cet_programa_solicitud`, `cet_plantilla`, `cet_con_datos`, `cet_datos_incompletos`, `cet_plantilla_compensacion_iva`, `cet_plantilla_adulto_mayor`, `cet_novedad_radicado`, `cet_motivo_archivo`, `cet_tipo_entidad`, `cet_id_solicitud`, `cet_observaciones`, `cet_notificar`, `cet_registro_usuario`, `cet_registro_fecha`, ABOGADOAPROBACION.`ceco_valor`, PROGRAMASOLICITUD.`ceco_valor`, PLANTILLA.`ceco_valor`, CONDATOS.`ceco_valor`, DATOSINCOMPLETOS.`ceco_valor`, PCOMPENSACIONIVA.`ceco_valor`, PADULTOMAYOR.`ceco_valor`, NOVEDADRADICADO.`ceco_valor`, TIPOENTIDAD.`ceco_valor`, TU.`usu_nombres_apellidos`, PRENTA.`ceco_valor` FROM `gestion_cetmnc_proyeccion_respuestas`
               LEFT JOIN `gestion_ce_configuracion` AS ABOGADOAPROBACION ON `gestion_cetmnc_proyeccion_respuestas`.`cet_abogado_aprobacion`=ABOGADOAPROBACION.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_proyeccion_respuestas`.`cet_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA ON `gestion_cetmnc_proyeccion_respuestas`.`cet_plantilla`=PLANTILLA.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS CONDATOS ON `gestion_cetmnc_proyeccion_respuestas`.`cet_con_datos`=CONDATOS.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS DATOSINCOMPLETOS ON `gestion_cetmnc_proyeccion_respuestas`.`cet_datos_incompletos`=DATOSINCOMPLETOS.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS PCOMPENSACIONIVA ON `gestion_cetmnc_proyeccion_respuestas`.`cet_plantilla_compensacion_iva`=PCOMPENSACIONIVA.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS PADULTOMAYOR ON `gestion_cetmnc_proyeccion_respuestas`.`cet_plantilla_adulto_mayor`=PADULTOMAYOR.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS PRENTA ON `gestion_cetmnc_proyeccion_respuestas`.`cet_plantilla_renta_ciudadana`=PRENTA.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS NOVEDADRADICADO ON `gestion_cetmnc_proyeccion_respuestas`.`cet_novedad_radicado`=NOVEDADRADICADO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS TIPOENTIDAD ON `gestion_cetmnc_proyeccion_respuestas`.`cet_tipo_entidad`=TIPOENTIDAD.`ceco_id`
               LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_proyeccion_respuestas`.`cet_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_tmnc_p1."";
            $consulta_registros_tmnc_p1 = $enlace_db->prepare($consulta_string_tmnc_p1);
            if (count($data_consulta)>0) {
                $consulta_registros_tmnc_p1->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_tmnc_p1->execute();
            $resultado_registros_tmnc_p1 = $consulta_registros_tmnc_p1->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_tmnc_p1)) {
              for ($i=0; $i < count($resultado_registros_tmnc_p1); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_tmnc_p1[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_tmnc_p1[$i][18]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_tmnc_p1[$i][29]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_tmnc_p1[$i][19]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'TMNC - 1. Proyección de Respuestas');
                $control_filas++;
                
              }

            }


        //2. Aprobación Respuesta
            $consulta_string_tmnc_p2="SELECT `cetar_id`, `cetar_radicado`, `cetar_numero_documento`, `cetar_nombre_ciudadano`, `cetar_proyector`, `cetar_apoyo_prosperidad`, `cetar_ingreso_solidario`, `cetar_carta_respuesta`, `cetar_estado`, `cetar_comentario_aprobacion`, `cetar_motivo_rechazo`, `cetar_observaciones`, `cetar_notificar`, `cetar_registro_usuario`, `cetar_registro_fecha`, PROYECTOR.`ceco_valor`, APOYOPROSPERIDAD.`ceco_valor`, INGRESOSOLIDARIO.`ceco_valor`, CARTARESPUESTA.`ceco_valor`, ESTADO.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_aprobacion_respuesta`
             LEFT JOIN `gestion_ce_configuracion` AS PROYECTOR ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_proyector`=PROYECTOR.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS APOYOPROSPERIDAD ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_apoyo_prosperidad`=APOYOPROSPERIDAD.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS INGRESOSOLIDARIO ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_ingreso_solidario`=INGRESOSOLIDARIO.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS CARTARESPUESTA ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_carta_respuesta`=CARTARESPUESTA.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS ESTADO ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_estado`=ESTADO.`ceco_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_tmnc_p2."";
            $consulta_registros_tmnc_p2 = $enlace_db->prepare($consulta_string_tmnc_p2);
            if (count($data_consulta)>0) {
                $consulta_registros_tmnc_p2->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_tmnc_p2->execute();
            $resultado_registros_tmnc_p2 = $consulta_registros_tmnc_p2->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_tmnc_p2)) {
              for ($i=0; $i < count($resultado_registros_tmnc_p2); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_tmnc_p2[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_tmnc_p2[$i][13]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_tmnc_p2[$i][20]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_tmnc_p2[$i][14]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'TMNC - 2. Aprobación Respuesta');
                $control_filas++;
                
              }

            }


        //3. Clasificación
            $consulta_string_tmnc_p3="SELECT `cetc_id`, `cetc_correo_electronico`, `cetc_fecha_ingreso_correo`, `cetc_nombre_ciudadano`, `cetc_cedula_consulta`, `cetc_asunto_correo`, `cetc_programa_solicitud`, `cetc_plantilla_utilizada`, `cetc_solicitud_ciudadano`, `cetc_plantilla_datos_incompletos`, `cetc_plantilla_datos_completos`, `cetc_parrafo_radicacion`, `cetc_parrafo_plantilla_1`, `cetc_parrafo_plantilla_4`, `cetc_parrafo_plantilla_5`, `cetc_parrafo_plantilla_6`, `cetc_situacion_plantilla_8`, `cetc_parrafo_plantilla_8`, `cetc_parrafo_plantilla_10`, `cetc_titular_hogar`, `cetc_parrafo_plantilla_14`, `cetc_parrafo_plantilla_16`, `cetc_situacion_plantilla_17`, `cetc_parrafo_plantilla_17`, `cetc_situacion_plantilla_18`, `cetc_parrafo_plantilla_18`, `cetc_parrafo_plantilla_20`, `cetc_nombre_solicitante`, `cetc_nombre_titular`, `cetc_parrafo_plantilla_21`, `cetc_situacion_plantilla_22`, `cetc_parrafo_plantilla_22`, `cetc_parrafo_plantilla_23`, `cetc_parrafo_plantilla_25`, `cetc_parrafo_plantilla_26`, `cetc_parrafo_plantilla_reemplazo`, `cetc_motivo_devolucion`, `cetc_observaciones`, `cetc_notificar`, `cetc_registro_usuario`, `cetc_registro_fecha`, PROGRAMASOLICITUD.`ceco_valor`, PUTILIZADA.`ceco_valor`, PDATOSINCOMPLETOS.`ceco_valor`, PDATOSCOMPLETOS.`ceco_valor`, PLANTILLA8.`ceco_valor`, PLANTILLA17.`ceco_valor`, PLANTILLA18.`ceco_valor`, PLANTILLA22.`ceco_valor`, MOTIVODEVOLUCION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_clasificacion`
             LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_clasificacion`.`cetc_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PUTILIZADA ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_utilizada`=PUTILIZADA.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PDATOSINCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_incompletos`=PDATOSINCOMPLETOS.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PDATOSCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_completos`=PDATOSCOMPLETOS.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA8 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_8`=PLANTILLA8.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_17`=PLANTILLA17.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_18`=PLANTILLA18.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA22 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_22`=PLANTILLA22.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS MOTIVODEVOLUCION ON `gestion_cetmnc_clasificacion`.`cetc_motivo_devolucion`=MOTIVODEVOLUCION.`ceco_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_clasificacion`.`cetc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_tmnc_p3."";
            $consulta_registros_tmnc_p3 = $enlace_db->prepare($consulta_string_tmnc_p3);
            if (count($data_consulta)>0) {
                $consulta_registros_tmnc_p3->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_tmnc_p3->execute();
            $resultado_registros_tmnc_p3 = $consulta_registros_tmnc_p3->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_tmnc_p3)) {
              for ($i=0; $i < count($resultado_registros_tmnc_p3); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_tmnc_p3[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_tmnc_p3[$i][39]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_tmnc_p3[$i][50]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_tmnc_p3[$i][40]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'TMNC - 3. Clasificación');
                $control_filas++;
                
              }

            }


        //4. Envíos
            $consulta_string_tmnc_p4="SELECT `cete_id`, `cete_correo_electronico`, `cete_fecha_ingreso`, `cete_fecha_clasificacion`, `cete_cedula_consulta`, `cete_programa_solicitud`, `cete_respuesta_enviada`, `cete_con_datos`, `cete_datos_incompletos`, `cete_parrafo_plantilla_16`, `cete_parrafo_plantilla_17`, `cete_parrafo_plantilla_18`, `cete_devolucion_correo`, `cete_responsable_clasificacion`, `cete_responsable_envio`, `cete_observaciones`, `cete_notificar`, `cete_registro_usuario`, `cete_registro_fecha`, PROGRAMASOLICITUD.`ceco_valor`, RESPUESTAENVIADA.`ceco_valor`, CONDATOS.`ceco_valor`, DATOSINCOMPLETOS.`ceco_valor`, PLANTILLA16.`ceco_valor`, PLANTILLA17.`ceco_valor`, PLANTILLA18.`ceco_valor`, DEVOLUCIONCORREO.`ceco_valor`, RESPONSABLECLASIFICACION.`ceco_valor`, TU.`usu_nombres_apellidos`, `cete_id_clasificacion` FROM `gestion_cetmnc_envios`
             LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_envios`.`cete_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS RESPUESTAENVIADA ON `gestion_cetmnc_envios`.`cete_respuesta_enviada`=RESPUESTAENVIADA.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS CONDATOS ON `gestion_cetmnc_envios`.`cete_con_datos`=CONDATOS.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS DATOSINCOMPLETOS ON `gestion_cetmnc_envios`.`cete_datos_incompletos`=DATOSINCOMPLETOS.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA16 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_16`=PLANTILLA16.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_17`=PLANTILLA17.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_18`=PLANTILLA18.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS DEVOLUCIONCORREO ON `gestion_cetmnc_envios`.`cete_devolucion_correo`=DEVOLUCIONCORREO.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLECLASIFICACION ON `gestion_cetmnc_envios`.`cete_responsable_clasificacion`=RESPONSABLECLASIFICACION.`ceco_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_envios`.`cete_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_tmnc_p4."";
            $consulta_registros_tmnc_p4 = $enlace_db->prepare($consulta_string_tmnc_p4);
            if (count($data_consulta)>0) {
                $consulta_registros_tmnc_p4->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_tmnc_p4->execute();
            $resultado_registros_tmnc_p4 = $consulta_registros_tmnc_p4->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_tmnc_p4)) {
              for ($i=0; $i < count($resultado_registros_tmnc_p4); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_tmnc_p4[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_tmnc_p4[$i][17]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_tmnc_p4[$i][28]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_tmnc_p4[$i][18]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'TMNC - 4. Envíos');
                $control_filas++;
                
              }

            }


        //5. Firma Respuesta
            $consulta_string_tmnc_p5="SELECT `cetfr_id`, `cetfr_fecha_firma`, `cetfr_modulo`, `cetfr_git`, `cetfr_radicado_entrada`, `cetfr_radicado_salida`, `cetfr_aprobador`, `cetfr_responsable_firma`, `cetfr_observaciones`, `cetfr_notificar`, `cetfr_usuario_registro`, `cetfr_usuario_fecha`, MODULO.`ceco_valor`, GIT.`ceco_valor`, APROBADOR.`ceco_valor`, RESPONSABLEFIRMA.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_firma_respuesta`
             LEFT JOIN `gestion_ce_configuracion` AS MODULO ON `gestion_cetmnc_firma_respuesta`.`cetfr_modulo`=MODULO.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS GIT ON `gestion_cetmnc_firma_respuesta`.`cetfr_git`=GIT.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS APROBADOR ON `gestion_cetmnc_firma_respuesta`.`cetfr_aprobador`=APROBADOR.`ceco_id`
             LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEFIRMA ON `gestion_cetmnc_firma_respuesta`.`cetfr_responsable_firma`=RESPONSABLEFIRMA.`ceco_id`
             LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_firma_respuesta`.`cetfr_usuario_registro`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_tmnc_p5."";
            $consulta_registros_tmnc_p5 = $enlace_db->prepare($consulta_string_tmnc_p5);
            if (count($data_consulta)>0) {
                $consulta_registros_tmnc_p5->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_tmnc_p5->execute();
            $resultado_registros_tmnc_p5 = $consulta_registros_tmnc_p5->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_tmnc_p5)) {
              for ($i=0; $i < count($resultado_registros_tmnc_p5); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_tmnc_p5[$i][4]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_tmnc_p5[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_tmnc_p5[$i][16]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_tmnc_p5[$i][11]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'TMNC - 5. Firma Respuesta');
                $control_filas++;
                
              }

            }


        //6. Pendientes Clasificación
            // $consulta_string_tmnc_p6="SELECT `cetpc_id`, `cetpc_pendiente_clasificacion`, `cetpc_pendiente_clasificar`, `cetpc_observaciones`, `cetpc_notificar`, `cetpc_registro_usuario`, `cetpc_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_pendiente_clasificacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_pendiente_clasificacion`.`cetpc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_tmnc_p6."";
            // $consulta_registros_tmnc_p6 = $enlace_db->prepare($consulta_string_tmnc_p6);
            // if (count($data_consulta)>0) {
            //     $consulta_registros_tmnc_p6->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            // }
            // $consulta_registros_tmnc_p6->execute();
            // $resultado_registros_tmnc_p6 = $consulta_registros_tmnc_p6->get_result()->fetch_all(MYSQLI_NUM);

            // if (count($resultado_registros_tmnc_p6)) {
            //   for ($i=0; $i < count($resultado_registros_tmnc_p6); $i++) { 
            //     $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_tmnc_p6[$i][]);
            //     $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_tmnc_p6[$i][]);
            //     $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_tmnc_p6[$i][]);
            //     $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_tmnc_p6[$i][]);
            //     $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'Canal_Escrito');
            //     $control_filas++;
                
            //   }

            // }


        //7. Casos Sin Gestionar
            $consulta_string_tmnc_p7="SELECT `cetcsg_id`, `cetcsg_proceso_ingreso_solidario`, `cetcsg_responsable_envio`, `cetcsg_responsable_proyeccion`, `cetcsg_causal_no_envio`, `cetcsg_causal_no_proyeccion`, `cetcsg_cantidad_casos`, `cetcsg_observaciones`, `cetcsg_notificar`, `cetcsg_registro_usuario`, `cetcsg_registro_fecha`, INGRESOSOLIDARIO.`ceco_valor`, RESPONSABLEENVIO.`ceco_valor`, RESPONSABLEPROYECCION.`ceco_valor`, CNOENVIO.`ceco_valor`, CNPROYECCION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_casos_sin_gestionar`
                LEFT JOIN `gestion_ce_configuracion` AS INGRESOSOLIDARIO ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_proceso_ingreso_solidario`=INGRESOSOLIDARIO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEENVIO ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_responsable_envio`=RESPONSABLEENVIO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLEPROYECCION ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_responsable_proyeccion`=RESPONSABLEPROYECCION.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS CNOENVIO ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_causal_no_envio`=CNOENVIO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS CNPROYECCION ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_causal_no_proyeccion`=CNPROYECCION.`ceco_id`
               LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_tmnc_p7."";
            $consulta_registros_tmnc_p7 = $enlace_db->prepare($consulta_string_tmnc_p7);
            if (count($data_consulta)>0) {
                $consulta_registros_tmnc_p7->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_tmnc_p7->execute();
            $resultado_registros_tmnc_p7 = $consulta_registros_tmnc_p7->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_tmnc_p7)) {
              for ($i=0; $i < count($resultado_registros_tmnc_p7); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_tmnc_p7[$i][12]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_tmnc_p7[$i][9]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_tmnc_p7[$i][16]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_tmnc_p7[$i][10]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'TMNC - 7. Casos Sin Gestionar');
                $control_filas++;
                
              }

            }


        //8. Aprobación Novedades CM
            $consulta_string_tmnc_p8="SELECT `cetan_id`, `cetan_cod_beneficiario`, `cetan_tipo_documento`, `cetan_documento`, `cetan_nombres_apellidos`, `cetan_tipo_novedad`, `cetan_datos_basicos`, `cetan_suspension`, `cetan_reactivacion`, `cetan_retiro`, `cetan_gestion`, `cetan_tipo_rechazo`, `cetan_realizo_cambio_datos`, `cetan_correccion_datos`, `cetan_observaciones`, `cetan_notificar`, `cetan_registro_usuario`, `cetan_registro_fecha`, TIPODOCUMENTO.`ceco_valor`, TIPONOVEDAD.`ceco_valor`, DATOSBASICOS.`ceco_valor`, SUSPENSION.`ceco_valor`, REACTIVACION.`ceco_valor`, RETIRO.`ceco_valor`, GESTION.`ceco_valor`, TIPORECHAZO.`ceco_valor`, CAMBIODATOS.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_aprobacion_novedades`
               LEFT JOIN `gestion_ce_configuracion` AS TIPODOCUMENTO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_documento`=TIPODOCUMENTO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS TIPONOVEDAD ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_novedad`=TIPONOVEDAD.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS DATOSBASICOS ON `gestion_cetmnc_aprobacion_novedades`.`cetan_datos_basicos`=DATOSBASICOS.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS SUSPENSION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_suspension`=SUSPENSION.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS REACTIVACION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_reactivacion`=REACTIVACION.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS RETIRO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_retiro`=RETIRO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS GESTION ON `gestion_cetmnc_aprobacion_novedades`.`cetan_gestion`=GESTION.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS TIPORECHAZO ON `gestion_cetmnc_aprobacion_novedades`.`cetan_tipo_rechazo`=TIPORECHAZO.`ceco_id`
               LEFT JOIN `gestion_ce_configuracion` AS CAMBIODATOS ON `gestion_cetmnc_aprobacion_novedades`.`cetan_realizo_cambio_datos`=CAMBIODATOS.`ceco_id`
               LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_aprobacion_novedades`.`cetan_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_fechas_tmnc_p8."";
            $consulta_registros_tmnc_p8 = $enlace_db->prepare($consulta_string_tmnc_p8);
            if (count($data_consulta)>0) {
                $consulta_registros_tmnc_p8->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            }
            $consulta_registros_tmnc_p8->execute();
            $resultado_registros_tmnc_p8 = $consulta_registros_tmnc_p8->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_tmnc_p8)) {
              for ($i=0; $i < count($resultado_registros_tmnc_p8); $i++) { 
                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_filas,$resultado_registros_tmnc_p8[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_filas,$resultado_registros_tmnc_p8[$i][16]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_filas,$resultado_registros_tmnc_p8[$i][27]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_filas,$resultado_registros_tmnc_p8[$i][17]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_filas,'TMNC - 8. Aprobación Novedades CM');
                $control_filas++;
                
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