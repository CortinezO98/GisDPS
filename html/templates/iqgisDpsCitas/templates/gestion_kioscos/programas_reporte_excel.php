<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Gestión Kioscos";
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    require_once('../assets/plugins/PhpSpreadsheet/vendor/autoload.php');
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    if(isset($_POST["reporte"])){
        $tipo_reporte=validar_input($_POST['tipo_reporte']);
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        $titulo_reporte="Gestión Quioscos -".$tipo_reporte.' - '.date('Y-m-d H_i_s').".xlsx";
        
        // Inicializa variable tipo array
        $data_consulta=array();

        if ($tipo_reporte=='Todos') {
            $filtro_programa='';
        } else {
            $filtro_programa='AND TP.`gkp_id`=?';
            array_push($data_consulta, $tipo_reporte);
        }

        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);
        
        $consulta_string="SELECT `gkc_id`, `gkc_kiosco`, `gkc_boton`, `gkc_datos_tipo_documento`, `gkc_datos_numero_identificacion`, `gkc_datos_nombres`, `gkc_datos_correo`, `gkc_datos_celular`, `gkc_datos_fijo`, `gkc_datos_autoriza`, `gkc_atencion_preferencial`, `gkc_informacion_poblacional`, `gkc_registro_fecha`, TB.`gkpb_nombre`, TP.`gkp_titulo` FROM `gestion_kioscos_consultas` LEFT JOIN `gestion_kioscos_programas_boton` AS TB ON `gestion_kioscos_consultas`.`gkc_boton`=TB.`gkpb_id` LEFT JOIN `gestion_kioscos_programas` AS TP ON TB.`gkpb_programa`=TP.`gkp_id` WHERE 1=1 ".$filtro_programa." AND `gkc_registro_fecha`>=? AND `gkc_registro_fecha`<=? ORDER BY `gkc_id` ASC";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        if (count($data_consulta)>0) {
            // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
            $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            
        }
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
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
    $spreadsheet->getActiveSheet()->setTitle('Reporte Gestión Quioscos');

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
    $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
    $spreadsheet->getActiveSheet()->getStyle('A4:L4')->applyFromArray($styleArrayTitulos);
    $spreadsheet->getActiveSheet()->setAutoFilter('A4:L4');
    $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('4')->getAlignment()->setWrapText(true);

    // Escribiendo los titulos
    $spreadsheet->getActiveSheet()->setCellValue('A4','Programa');
    $spreadsheet->getActiveSheet()->setCellValue('B4','Botón');
    $spreadsheet->getActiveSheet()->setCellValue('C4','Tipo Documento');
    $spreadsheet->getActiveSheet()->setCellValue('D4','Número Identificación');
    $spreadsheet->getActiveSheet()->setCellValue('E4','Nombres y Apellidos');
    $spreadsheet->getActiveSheet()->setCellValue('F4','Correo');
    $spreadsheet->getActiveSheet()->setCellValue('G4','Celular');
    $spreadsheet->getActiveSheet()->setCellValue('H4','Fijo');
    $spreadsheet->getActiveSheet()->setCellValue('I4','Autoriza Tratamiento Datos');
    $spreadsheet->getActiveSheet()->setCellValue('J4','Atención Preferencial');
    $spreadsheet->getActiveSheet()->setCellValue('K4','Información Poblacional');
    $spreadsheet->getActiveSheet()->setCellValue('L4','Fecha Registro');
    
    $spreadsheet->getActiveSheet()->setCellValue('A1','Reporte: Gestión Quioscos');
    $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin);
    
    // Ingresar Data consultada a partir de la fila 4

    for ($i=5; $i < count($resultado_registros)+5; $i++) {
        $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-5][14]);
        $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-5][13]);
        $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-5][3]);
        $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-5][4]);
        $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-5][5]);
        $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-5][6]);
        $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-5][7]);
        $spreadsheet->getActiveSheet()->setCellValue('H'.$i,$resultado_registros[$i-5][8]);
        $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-5][9]);
        $spreadsheet->getActiveSheet()->setCellValue('J'.$i,$resultado_registros[$i-5][10]);
        $spreadsheet->getActiveSheet()->setCellValue('K'.$i,$resultado_registros[$i-5][11]);
        $spreadsheet->getActiveSheet()->setCellValue('L'.$i,$resultado_registros[$i-5][12]);
    }

    //Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$titulo_reporte.'"');
    header('Cache-Control: max-age=0');

    // Guardamos el archivo
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
?>