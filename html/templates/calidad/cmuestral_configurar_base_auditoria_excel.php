<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Calidad-Calculadora Muestral";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    require_once('../assets/plugins/PhpSpreadsheet/vendor/autoload.php');
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;

    $array_meses=[1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril", 5=>"Mayo", 6=>"Junio", 7=>"Julio", 8=>"Agosto", 9=>"Septiembre", 10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre"];
	// error_reporting(E_ALL);
	// ini_set('display_errors', '1');

    $id_registro=validar_input(base64_decode($_GET['reg']));
    $fecha_dia=validar_input(base64_decode($_GET['fecha']));
    $mes_calculadora=validar_input($_GET['date']);
    
    $titulo_reporte="Gestión Calidad-Calculadora Muestral ".date('Y-m-d H_i_s').".xlsx";

    $consulta_string="SELECT `cmm_id`, `cmm_calculadora`, `cmm_mes`, `cmm_fecha`, `cmm_segmento`, TSEG.`cms_nombre_segmento`, `cmm_usuario`, TU.`usu_nombres_apellidos`, `cmm_monitor`, `cmm_muestra_auditoria`, `cmm_muestra_fecha_hora` FROM `gestion_calidad_cmuestral_muestras` LEFT JOIN `gestion_calidad_cmuestral_segmento` AS TSEG ON `gestion_calidad_cmuestral_muestras`.`cmm_segmento`=TSEG.`cms_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_calidad_cmuestral_muestras`.`cmm_usuario`=TU.`usu_id` WHERE `cmm_calculadora`=? AND `cmm_mes`=? AND `cmm_fecha`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param('sss', $id_registro, $mes_calculadora, $fecha_dia);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    $array_estado['seleccionable']='Seleccionable';
    $array_estado['no_seleccionable_segmento']='No seleccionable segmento';
    $array_estado['no_seleccionable_usuario']='No seleccionable usuario';
    $array_estado['no_seleccionable_fecha_piloto']='No seleccionable fecha piloto';
    $array_estado['excluido_fecha_piloto']='Excluido fecha piloto';
    $array_estado['no_seleccionable_datos_incompletos']='No seleccionable datos incompletos';
    $array_estado['no_seleccionable']='No seleccionable';
    $array_estado['auditoria']='Muestra Aleatoria';
    $array_estado['auditoria_dian']='Muestra Aleatoria';

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
    $spreadsheet->getActiveSheet()->setTitle('Reporte Calculadora Muestral');

    //Estilos de la Hoja 0
    $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(80);
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $spreadsheet->getActiveSheet()->getStyle('A3:G3')->applyFromArray($styleArrayTitulos);
    $spreadsheet->getActiveSheet()->setAutoFilter('A3:G3');
    $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);

    // Escribiendo los titulos
    $spreadsheet->getActiveSheet()->setCellValue('A3','Segmento');
    $spreadsheet->getActiveSheet()->setCellValue('B3','Año-Mes-Semana');
    $spreadsheet->getActiveSheet()->setCellValue('C3','Fecha');
    $spreadsheet->getActiveSheet()->setCellValue('D3','Doc. Usuario');
    $spreadsheet->getActiveSheet()->setCellValue('E3','Nombres y Apellidos');
    $spreadsheet->getActiveSheet()->setCellValue('F3','Id Transacción');
    $spreadsheet->getActiveSheet()->setCellValue('G3','Fecha-Hora Transacción');
    
    // Ingresar Data consultada a partir de la fila 4
    for ($i=4; $i < count($resultado_registros)+4; $i++) {
        $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-4][4]);
        $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-4][2]);
        $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-4][3]);
        $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-4][6]);
        $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-4][7]);
        $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-4][9]);
        $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-4][10]);
    }

    //Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$titulo_reporte.'"');
    header('Cache-Control: max-age=0');

    // Guardamos el archivo
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
?>