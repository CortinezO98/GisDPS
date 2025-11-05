<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Gestión Traslado IVA";
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
        $estado_reporte=$_POST['estado_reporte'];
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        if (!isset($estado_reporte)) {
            $estado_reporte=array();
        }

        $titulo_reporte="Traslado IVA -".$tipo_reporte.' - '.date('Y-m-d H_i_s').".xlsx";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);
        $filtro_buscar_estado="";

        if (count($estado_reporte)>0) {
            if ($tipo_reporte=='Consolidado Gestión') {
                //Agregar catidad de variables a filtrar a data consulta
                for ($i=0; $i < count($estado_reporte); $i++) { 
                  $filtro_buscar_estado.="`gti_estado`=? OR ";
                  array_push($data_consulta, $estado_reporte[$i]);
                }

                $filtro_buscar_estado=" AND (".substr($filtro_buscar_estado, 0, -4).")";
            }
        }

        if ($tipo_reporte=='Consolidado Gestión') {
            $consulta_string="SELECT `gti_id`, `gti_interaccion_id`, `gti_interaccion_fecha`, `gti_remitente`, `gti_cliente_identificacion`, `gti_cliente_nombre`, `gti_titular_cedula`, `gti_titular_fecha_expedicion`, `gti_beneficiario_identificacion`, `gti_link_foto`, `gti_departamento`, `gti_municipio`, `gti_direccion`, `gti_ruta_fichero`, `gti_estado`, `gti_responsable`, `gti_numero_novedad`, `gti_observaciones`, `gti_fecha_gestion`, `gti_registro_fecha`, TU.`usu_nombres_apellidos` FROM `gestion_traslado_iva` LEFT JOIN `administrador_usuario` AS TU ON `gestion_traslado_iva`.`gti_responsable`=TU.`usu_id` WHERE 1=1 AND `gti_fecha_gestion`>=? AND `gti_fecha_gestion`<=? ".$filtro_buscar_estado." ORDER BY `gti_registro_fecha` ASC";
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
        $spreadsheet->getActiveSheet()->getStyle('A4:O4')->applyFromArray($styleArrayTitulos);
        $spreadsheet->getActiveSheet()->setAutoFilter('A4:O4');
        $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('4')->getAlignment()->setWrapText(true);

        // Escribiendo los titulos
        $spreadsheet->getActiveSheet()->setCellValue('A4','Estado');
        $spreadsheet->getActiveSheet()->setCellValue('B4','Id Interacción');
        $spreadsheet->getActiveSheet()->setCellValue('C4','Identificación Usuario');
        $spreadsheet->getActiveSheet()->setCellValue('D4','Nombres y Apellidos');
        $spreadsheet->getActiveSheet()->setCellValue('E4','Identificación Titular');
        $spreadsheet->getActiveSheet()->setCellValue('F4','Fecha Expedición');
        $spreadsheet->getActiveSheet()->setCellValue('G4','Identificación Beneficiario');
        $spreadsheet->getActiveSheet()->setCellValue('H4','Municipio/Departamento');
        $spreadsheet->getActiveSheet()->setCellValue('I4','Dirección');
        $spreadsheet->getActiveSheet()->setCellValue('J4','Responsable');
        $spreadsheet->getActiveSheet()->setCellValue('K4','No. Novedad');
        $spreadsheet->getActiveSheet()->setCellValue('L4','Fecha Gestión');
        $spreadsheet->getActiveSheet()->setCellValue('M4','Observaciones');
        $spreadsheet->getActiveSheet()->setCellValue('N4','Fecha Interacción');
        $spreadsheet->getActiveSheet()->setCellValue('O4','Fecha Registro');
        
        $spreadsheet->getActiveSheet()->setCellValue('A1','Reporte: Traslado IVA');
        $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin);
        
        // Ingresar Data consultada a partir de la fila 4
        for ($i=5; $i < count($resultado_registros)+5; $i++) {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-5][14]);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-5][1]);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-5][4]);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-5][5]);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-5][6]);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-5][7]);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-5][8]);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$i,$resultado_registros[$i-5][11].' / '.$resultado_registros[$i-5][10]);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-5][12]);
            $spreadsheet->getActiveSheet()->setCellValue('J'.$i,$resultado_registros[$i-5][20]);
            $spreadsheet->getActiveSheet()->setCellValue('K'.$i,$resultado_registros[$i-5][16]);
            $spreadsheet->getActiveSheet()->setCellValue('L'.$i,$resultado_registros[$i-5][18]);
            $spreadsheet->getActiveSheet()->setCellValue('M'.$i,$resultado_registros[$i-5][17]);
            $spreadsheet->getActiveSheet()->setCellValue('N'.$i,$resultado_registros[$i-5][2]);
            $spreadsheet->getActiveSheet()->setCellValue('O'.$i,$resultado_registros[$i-5][19]);
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