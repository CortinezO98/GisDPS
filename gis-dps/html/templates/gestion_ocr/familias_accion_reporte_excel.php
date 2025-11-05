<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Gestión OCR-Gestión";
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    require_once('../assets/plugins/PhpSpreadsheet/vendor/autoload.php');
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    if(isset($_POST["reporte"])){
        $tipo_reporte=validar_input($_POST['tipo_reporte']);
        $estado_reporte=$_POST['estado_reporte'];
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        if (!isset($estado_reporte)) {
            $estado_reporte=array();
        }

        $titulo_reporte="Gestión OCR -".$tipo_reporte.' - '.date('Y-m-d H_i_s').".xlsx";
        
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

    if ($tipo_reporte=='Consolidado Gestión') {
        //Estilos de la Hoja 0
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(80);
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
        $spreadsheet->getActiveSheet()->getStyle('A4:Y4')->applyFromArray($styleArrayTitulos);
        $spreadsheet->getActiveSheet()->setAutoFilter('A4:Y4');
        $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('4')->getAlignment()->setWrapText(true);

        // Escribiendo los titulos
        $spreadsheet->getActiveSheet()->setCellValue('A4','Estado Gestión');
        $spreadsheet->getActiveSheet()->setCellValue('B4','Intentos');
        $spreadsheet->getActiveSheet()->setCellValue('C4','Cód. Familia');
        $spreadsheet->getActiveSheet()->setCellValue('D4','Cód. Beneficiario');
        $spreadsheet->getActiveSheet()->setCellValue('E4','Cabeza Familia');
        $spreadsheet->getActiveSheet()->setCellValue('F4','Primer Nombre');
        $spreadsheet->getActiveSheet()->setCellValue('G4','Segundo Nombre');
        $spreadsheet->getActiveSheet()->setCellValue('H4','Primer Apellido');
        $spreadsheet->getActiveSheet()->setCellValue('I4','Segundo Apellido');
        $spreadsheet->getActiveSheet()->setCellValue('J4','Documento');
        $spreadsheet->getActiveSheet()->setCellValue('K4','Fecha Nacimiento');
        $spreadsheet->getActiveSheet()->setCellValue('L4','Género');
        $spreadsheet->getActiveSheet()->setCellValue('M4','Fecha Expedición');
        $spreadsheet->getActiveSheet()->setCellValue('N4','Celular');
        $spreadsheet->getActiveSheet()->setCellValue('O4','Teléfono');
        $spreadsheet->getActiveSheet()->setCellValue('P4','Correo');
        $spreadsheet->getActiveSheet()->setCellValue('Q4','Municipio');
        $spreadsheet->getActiveSheet()->setCellValue('R4','Departamento');
        $spreadsheet->getActiveSheet()->setCellValue('S4','Notificación');
        $spreadsheet->getActiveSheet()->setCellValue('T4','Notificación Fecha Registro');
        $spreadsheet->getActiveSheet()->setCellValue('U4','Responsable');
        $spreadsheet->getActiveSheet()->setCellValue('V4','Observaciones');
        $spreadsheet->getActiveSheet()->setCellValue('W4','Fecha Gestión');
        $spreadsheet->getActiveSheet()->setCellValue('X4','Tipificación Llamada');
        $spreadsheet->getActiveSheet()->setCellValue('Y4','Id Llamada');
        
        $spreadsheet->getActiveSheet()->setCellValue('A1','Reporte: Gestión OCR');
        $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin);
        
        // Ingresar Data consultada a partir de la fila 4

        for ($i=5; $i < count($resultado_registros)+5; $i++) {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-5][6]);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-5][7]);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-5][1]);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-5][2]);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-5][3]);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-5][14]);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-5][15]);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$i,$resultado_registros[$i-5][16]);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-5][17]);
            $spreadsheet->getActiveSheet()->setCellValue('J'.$i,$resultado_registros[$i-5][18]);
            $spreadsheet->getActiveSheet()->setCellValue('K'.$i,$resultado_registros[$i-5][19]);
            $spreadsheet->getActiveSheet()->setCellValue('L'.$i,$resultado_registros[$i-5][20]);
            $spreadsheet->getActiveSheet()->setCellValue('M'.$i,$resultado_registros[$i-5][21]);
            $spreadsheet->getActiveSheet()->setCellValue('N'.$i,$resultado_registros[$i-5][25]);
            $spreadsheet->getActiveSheet()->setCellValue('O'.$i,$resultado_registros[$i-5][24]);
            $spreadsheet->getActiveSheet()->setCellValue('P'.$i,$resultado_registros[$i-5][23]);
            $spreadsheet->getActiveSheet()->setCellValue('Q'.$i,$resultado_registros[$i-5][31]);
            $spreadsheet->getActiveSheet()->setCellValue('R'.$i,$resultado_registros[$i-5][32]);
            $spreadsheet->getActiveSheet()->setCellValue('S'.$i,$resultado_registros[$i-5][9]);
            $spreadsheet->getActiveSheet()->setCellValue('T'.$i,$resultado_registros[$i-5][11]);
            $spreadsheet->getActiveSheet()->setCellValue('U'.$i,$resultado_registros[$i-5][22]);
            $spreadsheet->getActiveSheet()->setCellValue('V'.$i,$resultado_registros[$i-5][8]);
            $spreadsheet->getActiveSheet()->setCellValue('W'.$i,$resultado_registros[$i-5][26]);
            $spreadsheet->getActiveSheet()->setCellValue('X'.$i,$resultado_registros[$i-5][27]);
            $spreadsheet->getActiveSheet()->setCellValue('Y'.$i,$resultado_registros[$i-5][28]);
        }
    } elseif ($tipo_reporte=='Notificaciones Correo') {
        //Estilos de la Hoja 0
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(80);
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
        $spreadsheet->getActiveSheet()->getStyle('A4:N4')->applyFromArray($styleArrayTitulos);
        $spreadsheet->getActiveSheet()->setAutoFilter('A4:N4');
        $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('4')->getAlignment()->setWrapText(true);

        // Escribiendo los titulos
        $spreadsheet->getActiveSheet()->setCellValue('A4','Estado Gestión');
        $spreadsheet->getActiveSheet()->setCellValue('B4','Cód. Familia');
        $spreadsheet->getActiveSheet()->setCellValue('C4','Cód. Beneficiario');
        $spreadsheet->getActiveSheet()->setCellValue('D4','Cabeza Familia');
        $spreadsheet->getActiveSheet()->setCellValue('E4','Primer Nombre');
        $spreadsheet->getActiveSheet()->setCellValue('F4','Segundo Nombre');
        $spreadsheet->getActiveSheet()->setCellValue('G4','Primer Apellido');
        $spreadsheet->getActiveSheet()->setCellValue('H4','Segundo Apellido');
        $spreadsheet->getActiveSheet()->setCellValue('I4','Documento');
        $spreadsheet->getActiveSheet()->setCellValue('J4','Correo');
        $spreadsheet->getActiveSheet()->setCellValue('K4','Notificación');
        $spreadsheet->getActiveSheet()->setCellValue('L4','Notificación Fecha Registro');
        $spreadsheet->getActiveSheet()->setCellValue('M4','Responsable');
        $spreadsheet->getActiveSheet()->setCellValue('N4','Observaciones');
        
        $spreadsheet->getActiveSheet()->setCellValue('A1','Reporte: Gestión OCR - Notificación Correo');
        $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin);
        
        // Ingresar Data consultada a partir de la fila 4

        for ($i=5; $i < count($resultado_registros)+5; $i++) {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-5][6]);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-5][1]);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-5][2]);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-5][3]);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-5][14]);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-5][15]);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-5][16]);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$i,$resultado_registros[$i-5][17]);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-5][18]);
            $spreadsheet->getActiveSheet()->setCellValue('J'.$i,$resultado_registros[$i-5][23]);
            $spreadsheet->getActiveSheet()->setCellValue('K'.$i,$resultado_registros[$i-5][9]);
            $spreadsheet->getActiveSheet()->setCellValue('L'.$i,$resultado_registros[$i-5][11]);
            $spreadsheet->getActiveSheet()->setCellValue('M'.$i,$resultado_registros[$i-5][22]);
            $spreadsheet->getActiveSheet()->setCellValue('N'.$i,$resultado_registros[$i-5][8]);
        }
    } elseif ($tipo_reporte=='Notificaciones SMS') {
        //Estilos de la Hoja 0
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(80);
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
        $spreadsheet->getActiveSheet()->getStyle('A4:R4')->applyFromArray($styleArrayTitulos);
        $spreadsheet->getActiveSheet()->setAutoFilter('A4:R4');
        $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('4')->getAlignment()->setWrapText(true);

        // Escribiendo los titulos
        $spreadsheet->getActiveSheet()->setCellValue('A4','Cód. Familia');
        $spreadsheet->getActiveSheet()->setCellValue('B4','Cód. Beneficiario');
        $spreadsheet->getActiveSheet()->setCellValue('C4','Cabeza Familia');
        $spreadsheet->getActiveSheet()->setCellValue('D4','Primer Nombre');
        $spreadsheet->getActiveSheet()->setCellValue('E4','Segundo Nombre');
        $spreadsheet->getActiveSheet()->setCellValue('F4','Primer Apellido');
        $spreadsheet->getActiveSheet()->setCellValue('G4','Segundo Apellido');
        $spreadsheet->getActiveSheet()->setCellValue('H4','Documento');
        $spreadsheet->getActiveSheet()->setCellValue('I4','Celular');
        $spreadsheet->getActiveSheet()->setCellValue('J4','Teléfono');
        $spreadsheet->getActiveSheet()->setCellValue('K4','Notificación SMS Destino');
        $spreadsheet->getActiveSheet()->setCellValue('L4','Notificación SMS Mensaje');
        $spreadsheet->getActiveSheet()->setCellValue('M4','Notificación SMS Url');
        $spreadsheet->getActiveSheet()->setCellValue('N4','Notificación SMS Estado Envío');
        $spreadsheet->getActiveSheet()->setCellValue('O4','Notificación SMS Fecha Envío');
        $spreadsheet->getActiveSheet()->setCellValue('P4','Notificación SMS Observaciones');
        $spreadsheet->getActiveSheet()->setCellValue('Q4','Notificación SMS Fecha Registro');
        $spreadsheet->getActiveSheet()->setCellValue('R4','Notificación SMS Usuario Registro');
        
        $spreadsheet->getActiveSheet()->setCellValue('A1','Reporte: Gestión OCR - Notificación SMS');
        $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin);
        
        // Ingresar Data consultada a partir de la fila 4

        for ($i=5; $i < count($resultado_registros)+5; $i++) {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-5][14]);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-5][15]);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-5][16]);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-5][18]);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-5][19]);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-5][20]);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-5][21]);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$i,$resultado_registros[$i-5][22]);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-5][24]);
            $spreadsheet->getActiveSheet()->setCellValue('J'.$i,$resultado_registros[$i-5][23]);
            $spreadsheet->getActiveSheet()->setCellValue('K'.$i,$resultado_registros[$i-5][5]);
            $spreadsheet->getActiveSheet()->setCellValue('L'.$i,$resultado_registros[$i-5][6]);
            $spreadsheet->getActiveSheet()->setCellValue('M'.$i,$resultado_registros[$i-5][7]);
            $spreadsheet->getActiveSheet()->setCellValue('N'.$i,$resultado_registros[$i-5][10]);
            $spreadsheet->getActiveSheet()->setCellValue('O'.$i,$resultado_registros[$i-5][11]);
            $spreadsheet->getActiveSheet()->setCellValue('P'.$i,$resultado_registros[$i-5][9]);
            $spreadsheet->getActiveSheet()->setCellValue('Q'.$i,$resultado_registros[$i-5][13]);
            $spreadsheet->getActiveSheet()->setCellValue('R'.$i,$resultado_registros[$i-5][17]);
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