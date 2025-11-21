<?php
    use PhpOffice\PhpSpreadsheet\IOFactory;
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Radicación";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    require_once('../assets/plugins/TCPDF-main/tcpdf.php');
    
    
    class CustomTCPDF extends TCPDF {
        public function Header() {
            // Agrega la imagen al encabezado
            $imagePath = '../images/logo/logo_superior.png';
            $this->Image($imagePath, '', 5, 55, '', 'PNG', '', 'T', false, 55, '', false, false, 0, false, false, false);
            // Set font
            $this->SetFont('helvetica', 'B', 8);
            // Title
            $this->Cell(20,4, '', '', 0, 'C');
            $this->MultiCell(50, 5, 'Rama Judicial del Poder Público Consejo Superior de la Judicatura', 0, 'C', 0, 1, '', '');
            $this->Cell(148,4, '', '', 0, 'C');
            $this->Cell(50,4, 'SIGCMA', '', 1, 'C');
            $this->Cell(50,4, '', '', 0, 'C');
            $this->MultiCell(100, 5, 'FORMATO CALIFICACIÓN INTEGRAL DE SERVICIOS', 0, 'C', 0, 1, '', '');
        }
    }
    
    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));

    $consulta_string="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom` FROM `gestion_radicacion_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_radicacion_casos_historial`.`grch_correo_de`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos_historial`.`grch_registro_usuario`=TAG.`usu_id` WHERE `grch_radicado_id`=? AND `grch_tipo`<>'Borrador' ORDER BY `grch_id` ASC";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    // echo "<pre>";
    // print_r($resultado_registros);
    // echo "</pre>";

    //PDF TCPDF
    $pdf = new CustomTCPDF('P', 'mm', array(216, 355.6), true, 'UTF-8');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetCreator('Tu nombre');
    $pdf->SetAuthor('Tu nombre');
    $pdf->SetTitle('Calificación Integral de Servicios');
    // set default header data PDF_HEADER_LOGO
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    $pdf->SetFont('Helvetica', '', 8);

    $pdf->AddPage();

    //LÍNEA TÍTULO FORMATO
    $pdf->SetFont('Helvetica', 'B', 8);
    $pdf->MultiCell('', 3, 'Prueba', 0, 'C', 0, 1, '', '');
    $pdf->MultiCell('', 3, '', '', 'L', 0, 1, '', '');

    $tbl = $resultado_registros[0][21];
    
    $pdf->writeHTML($tbl, true, false, false, false, '');

    // //LÍNEA 1
    // $pdf->SetFont('Helvetica', 'B', 7);
    // $pdf->MultiCell(20, 3, 'APELLIDOS:', 0, 'L', 0, 0, '', '');
    // $pdf->SetFont('Helvetica', '', 6);
    // $pdf->MultiCell(40, 3, $rescalificacion[0]['ea_apellidos'], 'B', 'L', 0, 0, '', '');
    // $pdf->SetFont('Helvetica', 'B', 7);
    // $pdf->MultiCell(20, 3, 'NOMBRES:', 0, 'L', 0, 0, '', '');
    // $pdf->SetFont('Helvetica', '', 6);
    // $pdf->MultiCell(40, 3, $rescalificacion[0]['ea_nombres'], 'B', 'L', 0, 0, '', '');
    // $pdf->SetFont('Helvetica', 'B', 7);
    // $pdf->MultiCell(20, 3, 'CÉDULA:', 0, 'L', 0, 0, '', '');
    // $pdf->SetFont('Helvetica', '', 6);
    // $pdf->MultiCell(40, 3, $rescalificacion[0]['ea_identificacion'], 'B', 'L', 0, 1, '', '');

    // //LÍNEA 2
    // $pdf->MultiCell('', 3, '', '', 'L', 0, 1, '', '');

    // $pdf->SetFont('Helvetica', 'B', 7);
    // $pdf->MultiCell(30, 7, 'CARGO EN CARRERA:', 0, 'L', 0, 0, '', '', true, 0, false, true, 7, 'M');
    // $pdf->SetFont('Helvetica', '', 6);
    // $pdf->MultiCell(104, 7, $rescalificacion[0]['ac_nombre'], 'B', 'L', 0, 0, '', '', true, 0, false, true, 7, 'M');
    
    // $pdf->SetFont('Helvetica', 'B', 7);
    // $pdf->MultiCell(12, 7, 'DESDE:', 0, 'L', 0, 0, '', '', true, 0, false, true, 7, 'M');
    // $pdf->SetFont('Helvetica', '', 6);


    // //LÍNEA 3
    // $pdf->MultiCell('', 3, '', '', 'L', 0, 1, '', '');

    // $pdf->SetFont('Helvetica', 'B', 7);
    // $pdf->MultiCell(38, 7, 'CORPORACIÓN O JUZGADO:', '', 'L', 0, 0, '', '', true, 0, false, true, 7, 'M');
    // $pdf->SetFont('Helvetica', '', 6);
    // $pdf->MultiCell(90, 7, $rescalificacion[0]['ad_nombre'], 'B', 'L', 0, 0, '', '', true, 0, false, true, 7, 'M');
    // $pdf->SetFont('Helvetica', 'B', 7);
    // $pdf->MultiCell(17, 7, 'MUNICIPIO:', '', 'L', 0, 0, '', '', true, 0, false, true, 7, 'M');
    // $pdf->SetFont('Helvetica', '', 6);
    // $pdf->MultiCell(35, 7, 'BOGOTÁ D.C.', 'B', 'L', 0, 1, '', '', true, 0, false, true, 7, 'M');
    
    
    
    
    // $pdf->SetFont('Helvetica', 'B', 8);
    
    // $pdf->MultiCell('', 3, '', 0, 'L', 0, 1, '', '');

    // $tbl = '<table cellspacing="2" cellpadding="3" border="0.1">';
    // $tbl .= '<tr>
    //             <td colspan="3" align="center"><b>2. CALIFICACIÓN INTEGRAL DE SERVICIOS</b></td>
    //         </tr>';
    // $tbl .= '</table>';
    // $pdf->writeHTML($tbl, true, false, false, false, '');



    // $tbl = '<table cellspacing="2" cellpadding="3" border="0.1">';
    // $tbl .= '<tr>
    //             <td align="center" width="475" colspan="2"><b>4. CALIFICACIÓN INTEGRAL - PUNTAJE TOTAL</b><br>(Calidad + Eficiencia o Rendimiento + Organización del Trabajo + Publicaciones)</td>
    //             <td align="center" width="44"><b>'.$rescalificacion[0]['cfc_nota_general'].'</b></td>
    //         </tr>
    //         <tr>
    //             <td align="center" rowspan="2"><b>SATISFACTORIA</b></td>
    //             <td align="center"><b>EXCELENTE</b></td>
    //             <td align="center"><b>'.$nota_excelente.'</b></td>
    //         </tr>
    //         <tr>
    //             <td align="center"><b>BUENA</b></td>
    //             <td align="center"><b>'.$nota_buena.'</b></td>
    //         </tr>
    //         <tr>
    //             <td align="center" colspan="2"><b>INSATISFACTORIA</b></td>
    //             <td align="center"><b>'.$nota_insatisfactoria.'</b></td>
    //         </tr>';
    // $tbl .= '</table>';
    // $pdf->writeHTML($tbl, true, false, false, false, '');

    
    // $tbl = '<table cellspacing="2" cellpadding="3" border="0.1">';
    // $tbl .= '<tr>
    //             <td align="center"><b>CALIFICADOR</b></td>
    //         </tr>';
    // $tbl .= '</table>';
    // $pdf->writeHTML($tbl, true, false, false, false, '');


    // $pdf->SetFont('Helvetica', '', 7);
    // $pdf->MultiCell('', 5, 'En ___________________________ a los (____) días del mes de ___________________________ del año (_______), se notifica personalmente al (la) señor (a) '.$rescalificacion[0]['ea_nombres'].' '.$rescalificacion[0]['ea_apellidos'].', identificado (a) con la cédula de ciudadanía No. '.$rescalificacion[0]['ea_identificacion'].' expedida en ______________________, el presente acto administrativo.', 0, 'L', 0, 1, '', '');
    // $pdf->MultiCell('', 4, '', '', 'L', 0, 1, '', '');
    // $pdf->MultiCell(180, 5, 'Se hace saber al interesado (a) que contra este acto administrativo procede el recurso de reposición,  ante quien profirió la decisión, del cual podrá hacer uso por escrito, en esta diligencia de notificación o dentro de los diez (10) días siguientes. Se deja constancia de que, con el fin de dar cumplimiento a lo ordenado en el artículo 76 del Código de Procedimiento Administrativo y de lo Contencioso Administrativo, se entrega al(a) notificado(a) copia íntegra, auténtica y gratuita del presente acto administrativo.', 0, 'L', 0, 1, '', '');
    
    
    // $pdf->SetFont('Helvetica', '', 7);
    // $pdf->MultiCell('', 4, '', '', 'L', 0, 1, '', '');
    // $pdf->MultiCell(70, 20, 'El (la) notificado (a),', 'B', 'L', 0, 0, '', '');
    // $pdf->MultiCell(40, 20, '', '', 'L', 0, 0, '', '');
    // $pdf->MultiCell(70, 20, 'Quien notifica,', 'B', 'L', 0, 1, '', '');
    // $pdf->MultiCell(70, 4, 'C.C. No. de ', '', 'L', 0, 0, '', '');
    // $pdf->MultiCell(40, 4, '', '', 'L', 0, 0, '', '');
    // $pdf->MultiCell(70, 4, 'C.C. No.                                           de ', '', 'L', 0, 1, '', '');
    // $pdf->MultiCell(70, 4, 'Nombre: ', 'L', 0, 0, '', '');
    // $pdf->MultiCell(40, 4, '', '', 'L', 0, 0, '', '');
    // $pdf->MultiCell(70, 4, 'Nombre: ', '', 'L', 0, 1, '', '');

    // $pdf->Output('FORMATO CALIFICACIÓN.pdf', 'I');
?>