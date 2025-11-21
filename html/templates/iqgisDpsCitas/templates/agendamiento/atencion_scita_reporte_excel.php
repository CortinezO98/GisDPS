<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Agendamiento Citas";
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
        $punto_atencion=$_POST['punto_atencion'];
        $usuarios=$_POST['usuarios'];
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        if (!isset($punto_atencion)) {
            $punto_atencion=array();
        }

        if (!isset($usuarios)) {
            $usuarios=array();
        }

        $titulo_reporte="Gestión Atención sin Cita -".$tipo_reporte.' - '.date('Y-m-d H_i_s').".xlsx";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);
        $filtro_buscar_estado="";

        $filtro_buscar_punto="";

        if (count($punto_atencion)>0) {
            if ($tipo_reporte=='Consolidado Gestión') {
                //Agregar catidad de variables a filtrar a data consulta
                for ($i=0; $i < count($punto_atencion); $i++) { 
                  $filtro_buscar_punto.="`gcasc_punto`=? OR ";
                  array_push($data_consulta, $punto_atencion[$i]);
                }

                $filtro_buscar_punto=" AND (".substr($filtro_buscar_punto, 0, -4).")";
            }
        }

        $filtro_buscar_usuario="";

        if (count($usuarios)>0) {
            if ($tipo_reporte=='Consolidado Gestión') {
                //Agregar catidad de variables a filtrar a data consulta
                for ($i=0; $i < count($usuarios); $i++) { 
                  $filtro_buscar_usuario.="`gcasc_atencion_usuario`=? OR ";
                  array_push($data_consulta, $usuarios[$i]);
                }

                $filtro_buscar_usuario=" AND (".substr($filtro_buscar_usuario, 0, -4).")";
            }
        }

        if ($tipo_reporte=='Consolidado Gestión') {
            $consulta_string="SELECT `gcasc_id`, `gcasc_punto`, `gcasc_datos_tipo_documento`, `gcasc_datos_numero_identificacion`, `gcasc_datos_nombres`, `gcasc_datos_correo`, `gcasc_datos_celular`, `gcasc_datos_fijo`, `gcasc_datos_autoriza`, `gcasc_observaciones`, `gcasc_atencion_usuario`, `gcasc_atencion_fecha`, `gcasc_radicado`, `gcasc_atencion_preferencial`, `gcasc_informacion_poblacional`, `gcasc_genero`, `gcasc_nivel_escolaridad`, `gcasc_envio_encuesta`, `gcasc_celular`, `gcasc_estado`, `gcasc_registro_fecha`, TU.`usu_nombres_apellidos`, TP.`gcpa_punto_atencion` FROM `gestion_citas_atencion_scita` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_atencion_scita`.`gcasc_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_atencion_scita`.`gcasc_atencion_usuario`=TU.`usu_id` WHERE 1=1 AND `gcasc_registro_fecha`>=? AND `gcasc_registro_fecha`<=? ".$filtro_buscar_punto." ".$filtro_buscar_usuario." ORDER BY `gcasc_id` ASC";
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
    $spreadsheet->getActiveSheet()->setTitle('Gestión Atención sin Cita');

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
        $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
        $spreadsheet->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleArrayTitulos);
        $spreadsheet->getActiveSheet()->setAutoFilter('A4:I4');
        $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('4')->getAlignment()->setWrapText(true);

        // Escribiendo los titulos
        $spreadsheet->getActiveSheet()->setCellValue('A4','Radicado');
        $spreadsheet->getActiveSheet()->setCellValue('B4','Punto Atención');
        $spreadsheet->getActiveSheet()->setCellValue('C4','Doc. Asesor');
        $spreadsheet->getActiveSheet()->setCellValue('D4','Nombres y Apellidos Asesor');
        $spreadsheet->getActiveSheet()->setCellValue('E4','Fecha Atención');
        $spreadsheet->getActiveSheet()->setCellValue('F4','¿Envío SMS para realizar encuesta de satisfacción?');
        $spreadsheet->getActiveSheet()->setCellValue('G4','Número Contacto');
        $spreadsheet->getActiveSheet()->setCellValue('H4','Observaciones');
        $spreadsheet->getActiveSheet()->setCellValue('I4','Fecha Registro');
        
        $spreadsheet->getActiveSheet()->setCellValue('A1','Reporte: Gestión Atención sin Cita');
        $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin);
        
        // Ingresar Data consultada a partir de la fila 4

        for ($i=5; $i < count($resultado_registros)+5; $i++) {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-5][12]);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-5][22]);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-5][10]);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-5][21]);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-5][20]);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-5][17]);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-5][18]);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$i,$resultado_registros[$i-5][9]);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-5][20]);
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