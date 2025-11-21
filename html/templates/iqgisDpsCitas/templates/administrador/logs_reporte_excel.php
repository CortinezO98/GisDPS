<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Administrador";
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
        $usuario=validar_input($_POST['usuario']);
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        $titulo_reporte="Logs ".date('Y-m-d H_i_s').".xlsx";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($usuario!='Todos') {
            $filtro_usuario=" AND `clog_registro_usuario`=?";
            array_push($data_consulta, $usuario);
        } else {
            $filtro_usuario="";
        }

        $consulta_string="SELECT `clog_id`, `clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_user_agent`, `clog_remote_addr`, `clog_remote_host`, `clog_script`, `clog_registro_usuario`, `clog_registro_fecha`, TU.`usu_nombres_apellidos` FROM `administrador_log` LEFT JOIN `administrador_usuario` AS TU ON `administrador_log`.`clog_registro_usuario`=TU.`usu_id` WHERE `clog_registro_fecha`>=? AND `clog_registro_fecha`<=? ".$filtro_usuario." ORDER BY `clog_registro_fecha`";

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
    $spreadsheet->getActiveSheet()->setTitle('Reporte Logs');

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
    $spreadsheet->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleArrayTitulos);
    $spreadsheet->getActiveSheet()->setAutoFilter('A4:J4');
    $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('4')->getAlignment()->setWrapText(true);

    // Escribiendo los titulos
    $spreadsheet->getActiveSheet()->setCellValue('A4','Fecha y Hora');
    $spreadsheet->getActiveSheet()->setCellValue('B4','Módulo');
    $spreadsheet->getActiveSheet()->setCellValue('C4','Acción');
    $spreadsheet->getActiveSheet()->setCellValue('D4','Detalle');
    $spreadsheet->getActiveSheet()->setCellValue('E4','Documento Usuario');
    $spreadsheet->getActiveSheet()->setCellValue('F4','Nombres y Apellidos');
    $spreadsheet->getActiveSheet()->setCellValue('G4','Navegador');
    $spreadsheet->getActiveSheet()->setCellValue('H4','Ip');
    $spreadsheet->getActiveSheet()->setCellValue('I4','Host');
    $spreadsheet->getActiveSheet()->setCellValue('J4','Fichero');
    
    $spreadsheet->getActiveSheet()->setCellValue('A1','Reporte: Logs');
    $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin);
    
    // Ingresar Data consultada a partir de la fila 4

    for ($i=5; $i < count($resultado_registros)+5; $i++) {
        $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-5][10]);
        $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-5][1]);
        $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-5][2]);
        $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-5][4]);
        $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-5][9]);
        $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-5][11]);
        $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-5][5]);
        $spreadsheet->getActiveSheet()->setCellValue('H'.$i,$resultado_registros[$i-5][6]);
        $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-5][7]);
        $spreadsheet->getActiveSheet()->setCellValue('J'.$i,$resultado_registros[$i-5][8]);
    }

    //Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$titulo_reporte.'"');
    header('Cache-Control: max-age=0');

    // Guardamos el archivo
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
?>