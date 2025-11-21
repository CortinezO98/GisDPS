<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Agendamiento Citas";
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
        $punto_atencion=$_POST['punto_atencion'];
        $usuarios=$_POST['usuarios'];
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        if (!isset($estado_reporte)) {
            $estado_reporte=array();
        }

        if (!isset($punto_atencion)) {
            $punto_atencion=array();
        }

        if (!isset($usuarios)) {
            $usuarios=array();
        }

        $titulo_reporte="Gestión Citas -".$tipo_reporte.' - '.date('Y-m-d H_i_s').".xlsx";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);
        $filtro_buscar_estado="";

        if (count($estado_reporte)>0) {
            if ($tipo_reporte=='Consolidado Gestión') {
                //Agregar catidad de variables a filtrar a data consulta
                for ($i=0; $i < count($estado_reporte); $i++) { 
                  $filtro_buscar_estado.="`gcar_estado`=? OR ";
                  array_push($data_consulta, $estado_reporte[$i]);
                }

                $filtro_buscar_estado=" AND (".substr($filtro_buscar_estado, 0, -4).")";
            }
        }

        $filtro_buscar_punto="";

        if (count($punto_atencion)>0) {
            if ($tipo_reporte=='Consolidado Gestión') {
                //Agregar catidad de variables a filtrar a data consulta
                for ($i=0; $i < count($punto_atencion); $i++) { 
                  $filtro_buscar_punto.="`gcar_punto`=? OR ";
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
                  $filtro_buscar_usuario.="`gcar_usuario`=? OR ";
                  array_push($data_consulta, $usuarios[$i]);
                }

                $filtro_buscar_usuario=" AND (".substr($filtro_buscar_usuario, 0, -4).")";
            }
        }

        if ($tipo_reporte=='Consolidado Gestión') {
            $consulta_string="SELECT `gcar_consecutivo`, `gcar_cita`, `gcar_punto`, `gcar_usuario`, `gcar_datos_tipo_documento`, `gcar_datos_numero_identificacion`, `gcar_datos_nombres`, `gcar_datos_correo`, `gcar_datos_celular`, `gcar_datos_fijo`, `gcar_datos_autoriza`, `gcar_observaciones`, `gcar_atencion_usuario`, `gcar_atencion_fecha`, `gcar_registro_fecha`, TP.`gcpa_punto_atencion`, TP.`gcpa_direccion`, TU.`usu_nombres_apellidos`, TC.`gca_fecha`, TC.`gca_hora`, TC.`gca_estado`, TC.`gca_estado_agenda`, `gcar_estado`, TCIU.`ciu_departamento`, TCIU.`ciu_municipio`, `gcar_radicado`, `gcar_atencion_preferencial`, `gcar_informacion_poblacional`, `gcar_cancela_fecha`, `gcar_cancela_motivo`, `gcar_envio_encuesta`, `gcar_genero`, `gcar_nivel_escolaridad` FROM `gestion_citas_agenda_reservas` LEFT JOIN `gestion_citas_punto_atencion` AS TP ON `gestion_citas_agenda_reservas`.`gcar_punto`=TP.`gcpa_id` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citas_agenda_reservas`.`gcar_usuario`=TU.`usu_id` LEFT JOIN `gestion_citas_agenda` AS TC ON `gestion_citas_agenda_reservas`.`gcar_cita`=TC.`gca_id` LEFT JOIN `administrador_ciudades` AS TCIU ON TP.`gcpa_municipio`=TCIU.`ciu_codigo` WHERE 1=1 AND TC.`gca_fecha`>=? AND TC.`gca_fecha`<=? ".$filtro_buscar_estado." ".$filtro_buscar_punto." ".$filtro_buscar_usuario." ORDER BY `gcar_consecutivo` ASC";
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
    $spreadsheet->getActiveSheet()->setTitle('Reporte Gestión Citas');

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
        $spreadsheet->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
        $spreadsheet->getActiveSheet()->getStyle('A4:AB4')->applyFromArray($styleArrayTitulos);
        $spreadsheet->getActiveSheet()->setAutoFilter('A4:AB4');
        $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('4')->getAlignment()->setWrapText(true);

        // Escribiendo los titulos
        $spreadsheet->getActiveSheet()->setCellValue('A4','Consecutivo');
        $spreadsheet->getActiveSheet()->setCellValue('B4','Estado Agendamiento');
        $spreadsheet->getActiveSheet()->setCellValue('C4','Fecha');
        $spreadsheet->getActiveSheet()->setCellValue('D4','Hora');
        $spreadsheet->getActiveSheet()->setCellValue('E4','Doc. Asesor');
        $spreadsheet->getActiveSheet()->setCellValue('F4','Asesor');
        $spreadsheet->getActiveSheet()->setCellValue('G4','Punto Atención');
        $spreadsheet->getActiveSheet()->setCellValue('H4','Dirección');
        $spreadsheet->getActiveSheet()->setCellValue('I4','Municipio');
        $spreadsheet->getActiveSheet()->setCellValue('J4','Departamento');
        $spreadsheet->getActiveSheet()->setCellValue('K4','Ciudadano-Tipo Documento');
        $spreadsheet->getActiveSheet()->setCellValue('L4','Ciudadano-Número Identificación');
        $spreadsheet->getActiveSheet()->setCellValue('M4','Ciudadano-Nombres y Apellidos');
        $spreadsheet->getActiveSheet()->setCellValue('N4','Ciudadano-Correo');
        $spreadsheet->getActiveSheet()->setCellValue('O4','Ciudadano-Celular');
        $spreadsheet->getActiveSheet()->setCellValue('P4','Ciudadano-Fijo');
        $spreadsheet->getActiveSheet()->setCellValue('Q4','Ciudadano-Autoriza Políticas');
        $spreadsheet->getActiveSheet()->setCellValue('R4','Fecha Atención');
        $spreadsheet->getActiveSheet()->setCellValue('S4','Radicado');
        $spreadsheet->getActiveSheet()->setCellValue('T4','Atención Preferencial');
        $spreadsheet->getActiveSheet()->setCellValue('U4','Información Poblacional');
        $spreadsheet->getActiveSheet()->setCellValue('V4','Género');
        $spreadsheet->getActiveSheet()->setCellValue('W4','Nivel Escolaridad');
        $spreadsheet->getActiveSheet()->setCellValue('X4','¿Envío SMS para realizar encuesta de satisfacción?');
        $spreadsheet->getActiveSheet()->setCellValue('Y4','Observaciones');
        $spreadsheet->getActiveSheet()->setCellValue('Z4','Fecha Cancelación');
        $spreadsheet->getActiveSheet()->setCellValue('AA4','Motivo Cancelación');
        $spreadsheet->getActiveSheet()->setCellValue('AB4','Fecha Registro');
        
        $spreadsheet->getActiveSheet()->setCellValue('A1','Reporte: Gestión Citas');
        $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin);
        
        // Ingresar Data consultada a partir de la fila 4

        for ($i=5; $i < count($resultado_registros)+5; $i++) {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-5][0]);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-5][22]);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-5][18]);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$i,$resultado_registros[$i-5][19]);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$resultado_registros[$i-5][3]);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-5][17]);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-5][15]);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$i,$resultado_registros[$i-5][16]);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-5][24]);
            $spreadsheet->getActiveSheet()->setCellValue('J'.$i,$resultado_registros[$i-5][23]);
            $spreadsheet->getActiveSheet()->setCellValue('K'.$i,$resultado_registros[$i-5][4]);
            $spreadsheet->getActiveSheet()->setCellValue('L'.$i,$resultado_registros[$i-5][5]);
            $spreadsheet->getActiveSheet()->setCellValue('M'.$i,$resultado_registros[$i-5][6]);
            $spreadsheet->getActiveSheet()->setCellValue('N'.$i,$resultado_registros[$i-5][7]);
            $spreadsheet->getActiveSheet()->setCellValue('O'.$i,$resultado_registros[$i-5][8]);
            $spreadsheet->getActiveSheet()->setCellValue('P'.$i,$resultado_registros[$i-5][9]);
            $spreadsheet->getActiveSheet()->setCellValue('Q'.$i,$resultado_registros[$i-5][10]);
            $spreadsheet->getActiveSheet()->setCellValue('R'.$i,$resultado_registros[$i-5][13]);
            $spreadsheet->getActiveSheet()->setCellValue('S'.$i,$resultado_registros[$i-5][25]);
            $spreadsheet->getActiveSheet()->setCellValue('T'.$i,$resultado_registros[$i-5][26]);
            $spreadsheet->getActiveSheet()->setCellValue('U'.$i,$resultado_registros[$i-5][27]);
            
            $spreadsheet->getActiveSheet()->setCellValue('V'.$i,$resultado_registros[$i-5][31]);
            $spreadsheet->getActiveSheet()->setCellValue('W'.$i,$resultado_registros[$i-5][32]);

            $spreadsheet->getActiveSheet()->setCellValue('X'.$i,$resultado_registros[$i-5][30]);
            $spreadsheet->getActiveSheet()->setCellValue('Y'.$i,$resultado_registros[$i-5][11]);
            $spreadsheet->getActiveSheet()->setCellValue('Z'.$i,$resultado_registros[$i-5][28]);
            $spreadsheet->getActiveSheet()->setCellValue('AA'.$i,$resultado_registros[$i-5][29]);
            $spreadsheet->getActiveSheet()->setCellValue('AB'.$i,$resultado_registros[$i-5][14]);
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