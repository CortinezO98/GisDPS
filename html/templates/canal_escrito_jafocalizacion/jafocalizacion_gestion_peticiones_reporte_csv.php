<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Canal Escrito-Jóvenes Acción-Focalización";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    if(isset($_POST["reporte"])){
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']).' 23:59:59';
        
        $titulo_reporte="Canal Escrito - JAFocalización - Formato de Gestión de Peticiones JeA - ".date('Y_m_d H_i_s').".csv";
        
        // Inicializa variable tipo array
        $data_consulta=array();
        array_push($data_consulta, $fecha_inicio);
        array_push($data_consulta, $fecha_fin);

        if ($permisos_usuario=="Usuario") {
          $filtro_perfil=" AND `cejgp_registro_usuario`=?";
          array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
        } else {
            $filtro_perfil="";
        }

        $consulta_string="SELECT `cejgp_id`, `cejgp_radicado`, `cejgp_proyector`, `cejgp_aprobador`, `cejgp_peticionario_identificacion`, `cejgp_peticionario_nombres`, `cejgp_correo_direccion`, `cejgp_municipio`, `cejgp_solicitud`, `cejgp_no_registra_sija`, `cejgp_tipo_documento`, `cejgp_fecha_nacimiento_solicitante`, `cejgp_novedad`, `cejgp_no_radicado`, `cejgp_novedad_adicional`, `cejgp_codigo_beneficiario`, `cejgp_gestion_actualizacion`, `cejgp_institucion_estudia`, `cejgp_nivel_formacion`, `cejgp_convenio`, `cejgp_observacion_actualizacion`, `cejgp_codigo_beneficiario_caso_especial`, `cejgp_municipio_reporte`, `cejgp_observacion_caso_especial`, `cejgp_observaciones`, `cejgp_notificar`, `cejgp_registro_usuario`, `cejgp_registro_fecha`, SOLICITUD.`ceco_valor`, SIJA.`ceco_valor`, TIPODOCUMENTO.`ceco_valor`, NOVEDAD.`ceco_valor`, NOVEDADADD.`ceco_valor`, GESTIONACTUALIZACION.`ceco_valor`, INSTITUCIONESTUDIA.`ceco_valor`, NIVELFORMACION.`ceco_valor`, PROYECTOR.`usu_nombres_apellidos`, APROBADOR.`usu_nombres_apellidos`, TU.`usu_nombres_apellidos`, TC1.`ciu_departamento`, TC1.`ciu_municipio`, TC2.`ciu_departamento`, TC2.`ciu_municipio` FROM `gestion_cejafo_gestion_peticiones` 
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
   LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_peticiones`.`cejgp_registro_usuario`=TU.`usu_id` WHERE `cejgp_registro_fecha`>=? AND `cejgp_registro_fecha`<=? ".$filtro_perfil." ORDER BY `cejgp_id`";

        $consulta_registros = $enlace_db->prepare($consulta_string);
        if (count($data_consulta)>0) {
            // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
            $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
            
        }
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
    }

    $delimitador = ';';
    $encapsulador = '"';
    $ruta='storage/'.$titulo_reporte;
    // create a file pointer connected to the output stream
    $file = fopen($ruta, 'w');
    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($file, array('Reporte: Canal Escrito JAFocalización - 6. Formato de Gestión de Peticiones JeA'), $delimitador, $encapsulador);
    fputcsv($file, array('Fecha filtro: '.$fecha_inicio.' A '.$fecha_fin), $delimitador, $encapsulador);
    
    $titulos=array('No. de Radicado', 
'Aprobador', 
'Número de Identificación Peticionario', 
'Nombres y Apellidos Peticionario', 
'Email Ciudadano/Dirección', 
'Municipio', 
'Departamento', 
'Solicitud', 
'No Registra en Sija Potencial', 
'Tipo de Documento', 
'Fecha Nacimiento Solicitante', 
'Novedad', 
'No. Radicado', 
'Novedad Adicional', 
'Código Beneficiario', 
'Gestión Actualización', 
'Institución donde Estudia', 
'Nivel de Formación', 
'Convenio', 
'Observación Actualización Pendiente', 
'Código de Beneficiario', 
'Municipio', 'Departamento de Reporte', 
'Observación Caso Especial', 'Doc Usuario Registro', 'Registrado por', 'Fecha Registro');

    fputcsv($file, $titulos, $delimitador, $encapsulador);

    for ($i=0; $i < count($resultado_registros); $i++) {
        $linea=array($resultado_registros[$i][1], $resultado_registros[$i][37], $resultado_registros[$i][4], $resultado_registros[$i][5], $resultado_registros[$i][6], $resultado_registros[$i][40], $resultado_registros[$i][39], $resultado_registros[$i][28], $resultado_registros[$i][29], $resultado_registros[$i][30], $resultado_registros[$i][11], $resultado_registros[$i][31], $resultado_registros[$i][13], $resultado_registros[$i][32], $resultado_registros[$i][15], $resultado_registros[$i][33], $resultado_registros[$i][34], $resultado_registros[$i][35], $resultado_registros[$i][19], $resultado_registros[$i][20], $resultado_registros[$i][21], $resultado_registros[$i][42], $resultado_registros[$i][41], $resultado_registros[$i][23], $resultado_registros[$i][26], $resultado_registros[$i][38], $resultado_registros[$i][27]);
        fputcsv($file, $linea, $delimitador, $encapsulador);
    }
    rewind($file);

    fclose($file);

    header("Content-disposition: attachment; filename=".$titulo_reporte);
    header("Content-type: MIME");
    header('Cache-Control: max-age=0');
    readfile($ruta);
    unlink($ruta);

    //Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    // header('Content-Type: text/csv; charset=utf-8');
    // header('Content-Disposition: attachment; filename=HRdata.csv');
    // header('Cache-Control: max-age=0');
?>