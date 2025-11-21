<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Control Turnos";
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
        $campania=validar_input($_POST['campania']);
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        $data_consulta_usuarios=array();
        $data_consulta_turnos=array();

        $titulo_reporte="Control Turnos - ".$tipo_reporte." ".date('d-m-Y H_i_s').".xlsx";

        if ($campania!='Todos') {
            $filtro_usuario_operacion='AND (`usu_campania`=?)';
            array_push($data_consulta_usuarios, $campania);
        } else {
            $filtro_usuario_operacion='';
        }

        $consulta_string_usuarios="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_cargo_rol`, `usu_estado`, `usu_piloto` FROM `administrador_usuario` LEFT JOIN `administrador_campania` AS TCA ON `administrador_usuario`.`usu_campania`=TCA.`ac_id` WHERE 1=1 AND `usu_estado`='Activo' AND `usu_id`<>'1111111111' ".$filtro_usuario_operacion." ORDER BY `usu_nombres_apellidos` ASC";
        $consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
        if (count($data_consulta_usuarios)>0) {
            $consulta_registros_usuarios->bind_param(str_repeat("s", count($data_consulta_usuarios)), ...$data_consulta_usuarios);
        }
        $consulta_registros_usuarios->execute();
        $resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

        $filtro_usuarios='';
        array_push($data_consulta_turnos, $fecha_inicio);
        array_push($data_consulta_turnos, $fecha_fin);
        if (count($resultado_registros_usuarios)>0) {
            for ($i=0; $i < count($resultado_registros_usuarios); $i++) { 
                $filtro_usuarios.='`cot_usuario`=? OR ';
                array_push($data_consulta_turnos, $resultado_registros_usuarios[$i][0]);
            }
            $filtro_usuarios='AND ('.substr($filtro_usuarios, 0, -4).')';
        }

        if ($tipo_reporte=="Consolidado") {
            $consulta_string="SELECT `cot_id`, `cot_usuario`, `cot_tipo`, `cot_inicio`, `cot_fin`, `cot_duracion`, `cot_fuente`, `cot_observaciones_inicio`, `cot_observaciones_fin`, `cot_registro_fecha`, TU.`usu_nombres_apellidos` FROM `control_turno` LEFT JOIN `administrador_usuario`AS TU ON `control_turno`.`cot_usuario`=TU.`usu_id` WHERE 1=1 AND `cot_inicio`>=? AND `cot_inicio`<=? ".$filtro_usuarios." ORDER BY `cot_id` ASC";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            if (count($data_consulta_turnos)>0) {
                $consulta_registros->bind_param(str_repeat("s", count($data_consulta_turnos)), ...$data_consulta_turnos);
            }
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

            $fecha_actual=date("Y-m-d H:i:s");

            $fecha_control=$fecha_inicio;
            $array_fechas=array();
            array_push($array_fechas, $fecha_control);
            while ($fecha_control<date('Y-m-d', strtotime($fecha_fin))) {
                $fecha_control=date("Y-m-d", strtotime("+1 day", strtotime($fecha_control)));
                array_push($array_fechas, $fecha_control);
            }

            for ($i=0; $i < count($resultado_registros); $i++) {
                $fecha_registro=date('Y-m-d', strtotime($resultado_registros[$i][3]));
                // $array_fechas[]=$fecha_registro;
                if ($resultado_registros[$i][5]!="") {
                    $array_turnos[$resultado_registros[$i][1]][$fecha_registro][$resultado_registros[$i][2]]['duracion_total']+=$resultado_registros[$i][5]+0;
                } else {
                    if (date('Y-m-d', strtotime($resultado_registros[$i][3]))==date("Y-m-d")) {
                        $duracion = dateDiff($resultado_registros[$i][3],$fecha_actual);
                    } else {
                        $fecha_cierre=date('Y-m-d', strtotime($resultado_registros[$i][3])).' 23:59:59';
                        $duracion = dateDiff($resultado_registros[$i][3],$fecha_cierre);
                    }
                    $array_turnos[$resultado_registros[$i][1]][$fecha_registro][$resultado_registros[$i][2]]['duracion_total']+=$duracion+0;
                }
                
                if ($resultado_registros[$i][7]!="") {
                    $array_turnos[$resultado_registros[$i][1]][$fecha_registro]['observaciones_inicio'].='-'.$resultado_registros[$i][7];
                }
                if ($resultado_registros[$i][8]!="") {
                    $array_turnos[$resultado_registros[$i][1]][$fecha_registro]['observaciones_fin'].='-'.$resultado_registros[$i][8];
                }
            }
            $array_fechas=array_values(array_unique($array_fechas));
        } elseif ($tipo_reporte=="Log actividades") {
            $consulta_string="SELECT `cot_id`, `cot_usuario`, `cot_tipo`, `cot_inicio`, `cot_fin`, `cot_duracion`, `cot_fuente`, `cot_observaciones_inicio`, `cot_observaciones_fin`, `cot_registro_fecha`, TU.`usu_nombres_apellidos` FROM `control_turno` LEFT JOIN `administrador_usuario`AS TU ON `control_turno`.`cot_usuario`=TU.`usu_id` WHERE 1=1 AND `cot_inicio`>=? AND `cot_inicio`<=? ".$filtro_usuarios." ORDER BY `cot_id` ASC";
            $consulta_registros = $enlace_db->prepare($consulta_string);
            if (count($data_consulta_turnos)>0) {
                $consulta_registros->bind_param(str_repeat("s", count($data_consulta_turnos)), ...$data_consulta_turnos);
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
    $spreadsheet->getActiveSheet()->setTitle('Reporte Turnos');

    if ($tipo_reporte=="Consolidado") {
        //Estilos de la Hoja 0
            $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
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
            
            $spreadsheet->getActiveSheet()->getStyle('A5:D5')->applyFromArray($styleArrayTitulos);
            $spreadsheet->getActiveSheet()->getStyle('E5:E5')->applyFromArray($styleArrayTitulos_turno);
            $spreadsheet->getActiveSheet()->getStyle('F5:F5')->applyFromArray($styleArrayTitulos_break);
            $spreadsheet->getActiveSheet()->getStyle('G5:G5')->applyFromArray($styleArrayTitulos_almuerzo);
            $spreadsheet->getActiveSheet()->getStyle('H5:H5')->applyFromArray($styleArrayTitulos_pausa);
            $spreadsheet->getActiveSheet()->getStyle('I5:I5')->applyFromArray($styleArrayTitulos_capacitacion);
            $spreadsheet->getActiveSheet()->getStyle('J5:J5')->applyFromArray($styleArrayTitulos_retro);
            $spreadsheet->getActiveSheet()->getStyle('K5:L5')->applyFromArray($styleArrayTitulos);
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:L5');
            $spreadsheet->getActiveSheet()->getStyle('5')->getAlignment()->setWrapText(true);

        // Escribiendo los titulos
            $spreadsheet->getActiveSheet()->setCellValue('A5','Doc. Usuario');
            $spreadsheet->getActiveSheet()->setCellValue('B5','Nombres y Apellidos');
            $spreadsheet->getActiveSheet()->setCellValue('C5','Piloto');
            $spreadsheet->getActiveSheet()->setCellValue('D5','Fecha');
            $spreadsheet->getActiveSheet()->setCellValue('E5','Turno');
            $spreadsheet->getActiveSheet()->setCellValue('F5','Brek');
            $spreadsheet->getActiveSheet()->setCellValue('G5','Almuerzo');
            $spreadsheet->getActiveSheet()->setCellValue('H5','Pausa Activa');
            $spreadsheet->getActiveSheet()->setCellValue('I5','Capacitación');
            $spreadsheet->getActiveSheet()->setCellValue('J5','Retroalimentación');
            $spreadsheet->getActiveSheet()->setCellValue('K5','Observaciones Apertura');
            $spreadsheet->getActiveSheet()->setCellValue('L5','Observaciones Cierre');
            
            $spreadsheet->getActiveSheet()->setCellValue('A1','Tipo reporte: '.$tipo_reporte);
            $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha reporte: '.date('Y-m-d H:i:s'));
            $spreadsheet->getActiveSheet()->setCellValue('A2','Filtro: '.$fecha_inicio.' A '.date('Y-m-d', strtotime($fecha_fin)));

        //Ingresar Data consultada a partir de la fila 6
        $control_fila=6;
        for ($j=0; $j < count($array_fechas); $j++) { 
            for ($i=0; $i < count($resultado_registros_usuarios); $i++) {
                $id_usuario=$resultado_registros_usuarios[$i][0];
                if ($array_turnos[$id_usuario][$array_fechas[$j]]['turno']['duracion_total']>0) {
                    $duracion_turno=conversorSegundosHoras_ns($array_turnos[$id_usuario][$array_fechas[$j]]['turno']['duracion_total']);
                } else {
                    $duracion_turno='';
                }

                if ($array_turnos[$id_usuario][$array_fechas[$j]]['break']['duracion_total']>0) {
                    $duracion_break=conversorSegundosHoras_ns($array_turnos[$id_usuario][$array_fechas[$j]]['break']['duracion_total']);
                } else {
                    $duracion_break='';
                }

                if ($array_turnos[$id_usuario][$array_fechas[$j]]['almuerzo']['duracion_total']>0) {
                    $duracion_almuerzo=conversorSegundosHoras_ns($array_turnos[$id_usuario][$array_fechas[$j]]['almuerzo']['duracion_total']);
                } else {
                    $duracion_almuerzo='';
                }

                if ($array_turnos[$id_usuario][$array_fechas[$j]]['pausaactiva']['duracion_total']>0) {
                    $duracion_pausaactiva=conversorSegundosHoras_ns($array_turnos[$id_usuario][$array_fechas[$j]]['pausaactiva']['duracion_total']);
                } else {
                    $duracion_pausaactiva='';
                }

                if ($array_turnos[$id_usuario][$array_fechas[$j]]['capacitacion']['duracion_total']>0) {
                    $duracion_capacitacion=conversorSegundosHoras_ns($array_turnos[$id_usuario][$array_fechas[$j]]['capacitacion']['duracion_total']);
                } else {
                    $duracion_capacitacion='';
                }

                if ($array_turnos[$id_usuario][$array_fechas[$j]]['retroalimentacion']['duracion_total']>0) {
                    $duracion_retroalimentacion=conversorSegundosHoras_ns($array_turnos[$id_usuario][$array_fechas[$j]]['retroalimentacion']['duracion_total']);
                } else {
                    $duracion_retroalimentacion='';
                }

                $spreadsheet->getActiveSheet()->setCellValue('A'.$control_fila,$resultado_registros_usuarios[$i][0]);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$control_fila,$resultado_registros_usuarios[$i][1]);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$control_fila,$resultado_registros_usuarios[$i][5]);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$control_fila,$array_fechas[$j]);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$control_fila,$duracion_turno);
                $spreadsheet->getActiveSheet()->setCellValue('F'.$control_fila,$duracion_break);
                $spreadsheet->getActiveSheet()->setCellValue('G'.$control_fila,$duracion_almuerzo);
                $spreadsheet->getActiveSheet()->setCellValue('H'.$control_fila,$duracion_pausaactiva);
                $spreadsheet->getActiveSheet()->setCellValue('I'.$control_fila,$duracion_capacitacion);
                $spreadsheet->getActiveSheet()->setCellValue('J'.$control_fila,$duracion_retroalimentacion);
                $spreadsheet->getActiveSheet()->setCellValue('K'.$control_fila,$array_turnos[$id_usuario][$array_fechas[$j]]['observaciones_inicio']);
                $spreadsheet->getActiveSheet()->setCellValue('L'.$control_fila,$array_turnos[$id_usuario][$array_fechas[$j]]['observaciones_fin']);
                $control_fila++;
            }
        }
    } elseif ($tipo_reporte=="Log actividades") {
        //Estilos de la Hoja 0
            $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
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
            
            $spreadsheet->getActiveSheet()->getStyle('A5:J5')->applyFromArray($styleArrayTitulos);
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:J5');
            $spreadsheet->getActiveSheet()->getStyle('5')->getAlignment()->setWrapText(true);

        // Escribiendo los titulos
            $spreadsheet->getActiveSheet()->setCellValue('A5','Doc. Usuario');
            $spreadsheet->getActiveSheet()->setCellValue('B5','Nombres y Apellidos');
            $spreadsheet->getActiveSheet()->setCellValue('C5','Piloto');
            $spreadsheet->getActiveSheet()->setCellValue('D5','Fecha');
            $spreadsheet->getActiveSheet()->setCellValue('E5','Tipo Actividad');
            $spreadsheet->getActiveSheet()->setCellValue('F5','Inicio');
            $spreadsheet->getActiveSheet()->setCellValue('G5','Fin');
            $spreadsheet->getActiveSheet()->setCellValue('H5','Duración');
            $spreadsheet->getActiveSheet()->setCellValue('I5','Observaciones Apertura');
            $spreadsheet->getActiveSheet()->setCellValue('J5','Observaciones Cierre');
            
            $spreadsheet->getActiveSheet()->setCellValue('A1','Tipo reporte: '.$tipo_reporte);
            $spreadsheet->getActiveSheet()->setCellValue('A2','Fecha reporte: '.date('Y-m-d H:i:s'));
            $spreadsheet->getActiveSheet()->setCellValue('A2','Filtro: '.$fecha_inicio.' A '.date('Y-m-d', strtotime($fecha_fin)));

        $fecha_actual=date("Y-m-d H:i:s");
        //Ingresar Data consultada a partir de la fila 6
        for ($i=6; $i < count($resultado_registros)+6; $i++) {
            if ($resultado_registros[$i-6][5]!="") {
                $duracion_actividad=$resultado_registros[$i-6][5];
            } else {
                if (date('Y-m-d', strtotime($resultado_registros[$i-6][3]))==date("Y-m-d")) {
                    $duracion_actividad = dateDiff($resultado_registros[$i-6][3],$fecha_actual);
                } else {
                    $fecha_cierre=date('Y-m-d', strtotime($resultado_registros[$i-6][3])).' 23:59:59';
                    $duracion_actividad = dateDiff($resultado_registros[$i-6][3],$fecha_cierre);
                }
            }

            $spreadsheet->getActiveSheet()->setCellValue('A'.$i,$resultado_registros[$i-6][1]);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$i,$resultado_registros[$i-6][10]);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$i,$resultado_registros[$i-6][11]);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$i,date('Y-m-d', strtotime($resultado_registros[$i-6][3])));
            $spreadsheet->getActiveSheet()->setCellValue('E'.$i,$array_nombres_turnos[$resultado_registros[$i-6][2]]);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$i,$resultado_registros[$i-6][3]);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$i,$resultado_registros[$i-6][4]);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$i,conversorSegundosHoras_ns($duracion_actividad));
            $spreadsheet->getActiveSheet()->setCellValue('I'.$i,$resultado_registros[$i-6][7]);
            $spreadsheet->getActiveSheet()->setCellValue('J'.$i,$resultado_registros[$i-6][8]);
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