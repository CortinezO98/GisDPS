<?php
    $modulo_plataforma="Administrador";
    require_once("/var/www/html/iniciador.php");
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $ruta_pendientes="/var/www/html/templates/gestion_ocr/storage_pendientes/";
    $ruta_procesados="/var/www/html/templates/gestion_ocr/storage_procesado/";
    $ruta_error="/var/www/html/templates/gestion_ocr/storage_error/";
    
    // $ruta_pendientes="../gestion_ocr/storage_pendientes/";
    // $ruta_procesados="../gestion_ocr/storage_procesado/";
    // $ruta_error="../gestion_ocr/storage_error/";
    $lista_archivo = scandir($ruta_pendientes);

    // Prepara la sentencia
    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ocr_consolidado`(`ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    // Agrega variables a sentencia preparada
    $sentencia_insert->bind_param(
        'ssssssssssssssssssssssssss',
        $ocrc_cod_familia,
        $ocrc_codbeneficiario,
        $ocrc_cabezafamilia,
        $ocrc_miembro_id,
        $ocrc_existe,
        $ocrc_doc_valida,
        $ocrc_doc_valor,
        $ocrc_doc_tipo,
        $ocrc_nombre_valida,
        $ocrc_nombre_valor,
        $ocrc_apellido_valida,
        $ocrc_apellido_valor,
        $ocrc_fnacimiento_valida,
        $ocrc_fnacimiento_valor,
        $ocrc_fexpedicion_valida,
        $ocrc_fexpedicion_valor,
        $ocrc_contrato_existe,
        $ocrc_contrato_numid,
        $ocrc_contrato_titular,
        $ocrc_contrato_municipio,
        $ocrc_contrato_departamento,
        $ocrc_contrato_firmado,
        $ocrc_contrato_huella,
        $ocrc_registro_path,
        $ocrc_resultado_estado,
        $ocrc_resultado_novedad
    );

    // Prepara la sentencia
    $sentencia_insert_resultado = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado`(`ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_correo`, `ocrr_gestion_observaciones`, `ocrr_gestion_fecha`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_gestion_llamada_tipificacion`, `ocrr_gestion_llamada_id`, `ocrr_sr_fecha`, `ocrr_sr_observaciones`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    // Agrega variables a sentencia preparada
    $sentencia_insert_resultado->bind_param(
        'ssssssssssssssssss',
        $ocrr_cod_familia,
        $ocrr_codbeneficiario,
        $ocrr_cabezafamilia,
        $ocrr_resultado_familia_estado,
        $ocrr_gestion_agente,
        $ocrr_gestion_estado,
        $ocrr_gestion_intentos,
        $ocrr_gestion_correo,
        $ocrr_gestion_observaciones,
        $ocrr_gestion_fecha,
        $ocrr_gestion_notificacion,
        $ocrr_gestion_notificacion_estado,
        $ocrr_gestion_notificacion_fecha_registro,
        $ocrr_gestion_notificacion_fecha_envio,
        $ocrr_gestion_llamada_tipificacion,
        $ocrr_gestion_llamada_id,
        $ocrr_sr_fecha,
        $ocrr_sr_observaciones
    );
    
    $consulta_string = "SELECT `ocr_id`, `ocr_codbeneficiario`, `ocr_cabezadefamilia`, `ocr_fechanacimiento` FROM `gestion_ocr` WHERE `ocr_codfamilia`=? AND `ocr_documento`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("ss", $ocrc_cod_familia, $ocrc_miembro_id);

    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_ocr` SET `ocr_consolida_estado`=?,`ocr_consolida_fecha`=? WHERE `ocr_id`=?");
    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sss', $ocr_consolida_estado, $ocr_consolida_fecha, $ocr_id);

    if (count($lista_archivo) > 402) {
        $limite_procesar = 402;
    } else {
        $limite_procesar = count($lista_archivo);
    }

    $consulta_string_analista = "SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' AND `usu_cargo_rol`='AGENTE INSCRIPCIÓN FA' ORDER BY `usu_id`";
    $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
    $consulta_registros_analistas->execute();
    $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

    for ($i = 0; $i < count($resultado_registros_analistas); $i++) { 
        $array_analistas[] = $resultado_registros_analistas[$i][0];
    }

    shuffle($array_analistas);

    // echo "<pre>";
    // print_r($array_analistas);
    // echo "</pre>";

    $control_errores_agente = 0;

    for ($i = 2; $i < $limite_procesar; $i++) { // recorre cada json
        $json_path = $ruta_pendientes . $lista_archivo[$i];
        $json_parser = file_get_contents($json_path);
        $array_json  = json_decode($json_parser, true);

        // ⚠️ Si el JSON no es válido, lo mandamos a error para no romper el flujo
        if ($array_json === null) {
            $ocrc_registro_path = $lista_archivo[$i];
            $ruta_actual = $ruta_pendientes . $ocrc_registro_path;
            $ruta_nueva  = $ruta_error . $ocrc_registro_path;
            @rename($ruta_actual, $ruta_nueva);
            continue;
        }

        $ocrc_cod_familia = $array_json['familia']['codigo'];

        // ================================
        //    🔐 REMEDIACIÓN XSS AQUÍ
        // ================================
        // Antes: se imprimía directamente el código de familia, nombre de archivo
        // y todo el array_json con print_r sin sanitizar.
        //
        // Ahora: se codifica la salida para contexto HTML usando htmlspecialchars,
        // evitando que cualquier dato malicioso se ejecute como HTML/JS.
        echo htmlspecialchars($ocrc_cod_familia, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        echo " | ";
        echo htmlspecialchars($lista_archivo[$i], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        echo "<pre>";
        echo htmlspecialchars(print_r($array_json, true), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        echo "</pre>";
        echo "<br>";

        $ocrc_registro_path = $lista_archivo[$i];

        if ($ocrc_cod_familia != "") {
            $control_error = 0;
            $control_registro = 0;
            $control_errores_familia = 0;

            for ($j = 0; $j < count($array_json['familia']['miembros']); $j++) { // recorre cada miembro de familia relacionado en el json
                $ocrc_miembro_id             = $array_json['familia']['miembros'][$j]['id'];
                $ocrc_existe                 = $array_json['familia']['miembros'][$j]['existe'];
                $ocrc_doc_valida             = $array_json['familia']['miembros'][$j]['documento']['validacion'];
                $ocrc_doc_valor              = $array_json['familia']['miembros'][$j]['documento']['valor'];
                $ocrc_doc_tipo               = $array_json['familia']['miembros'][$j]['tipo'];
                $ocrc_nombre_valida          = $array_json['familia']['miembros'][$j]['nombres']['validacion'];
                $ocrc_nombre_valor           = $array_json['familia']['miembros'][$j]['nombres']['valor'];
                $ocrc_apellido_valida        = $array_json['familia']['miembros'][$j]['apellidos']['validacion'];
                $ocrc_apellido_valor         = $array_json['familia']['miembros'][$j]['apellidos']['valor'];
                $ocrc_fnacimiento_valida     = $array_json['familia']['miembros'][$j]['fecha_nacimiento']['validacion'];
                $ocrc_fnacimiento_valor      = $array_json['familia']['miembros'][$j]['fecha_nacimiento']['valor'];
                $ocrc_fexpedicion_valida     = $array_json['familia']['miembros'][$j]['fecha_expedicion']['validacion'];
                $ocrc_fexpedicion_valor      = $array_json['familia']['miembros'][$j]['fecha_expedicion']['valor'];

                $consulta_registros->execute();//consulta datos de base OCR original
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
                
                $ocrc_codbeneficiario = $resultado_registros[0][1];
                $ocrc_cabezafamilia   = $resultado_registros[0][2];
                $ocrc_fechanacimiento = $resultado_registros[0][3];

                $fecha_mayor_edad = date("Y-m", strtotime("+ 18 year", strtotime($ocrc_fechanacimiento)));//calcula fecha de cumple mayoría de edad según fecha de nacimiento
                $ocr_consolida_fecha = date('Y-m-d H:i:s');
                $fecha_actual        = date('Y-m');
                $ocr_id              = $resultado_registros[0][0];

                $ocrc_resultado_novedad = '';
                $control_errores_beneficiario = 0;//controla si el beneficiario tiene errores de los items validados

                if ($ocrc_cabezafamilia == "SI") {
                    //datos json contrato
                    $ocrc_contrato_existe      = $array_json['familia']['contrato']['existe'];
                    $ocrc_contrato_numid       = $array_json['familia']['contrato']['numId'];
                    $ocrc_contrato_titular     = $array_json['familia']['contrato']['titular'];
                    $ocrc_contrato_municipio   = $array_json['familia']['contrato']['municipio'];
                    $ocrc_contrato_departamento= $array_json['familia']['contrato']['departamento'];
                    $ocrc_contrato_firmado     = $array_json['familia']['contrato']['firmado'];
                    $ocrc_contrato_huella      = $array_json['familia']['contrato']['huella'];

                    $ocrr_cod_familia      = $ocrc_cod_familia;
                    $ocrr_codbeneficiario  = $ocrc_codbeneficiario;
                    $ocrr_cabezafamilia    = $ocrc_cabezafamilia;

                    if ($ocrc_doc_valida && $ocrc_nombre_valida && $ocrc_apellido_valida && $ocrc_fnacimiento_valida && $ocrc_fexpedicion_valida) {
                        // cumple requisitos doc identidad cabeza familia
                    } else {
                        $control_errores_beneficiario++;
                        $ocrc_resultado_novedad .= 'Documento de identidad NO validado, ';
                    }

                    if ($ocrc_contrato_existe && $ocrc_contrato_numid && $ocrc_contrato_firmado && $ocrc_contrato_huella) {
                        // cumple requisitos contrato cabeza familia
                    } else {
                        $control_errores_beneficiario++;
                        $ocrc_resultado_novedad .= 'Contrato NO validado';
                    }
                } else { // si no es cabeza familia
                    $ocrc_contrato_existe       = 'NA';
                    $ocrc_contrato_numid        = 'NA';
                    $ocrc_contrato_titular      = 'NA';
                    $ocrc_contrato_municipio    = 'NA';
                    $ocrc_contrato_departamento = 'NA';
                    $ocrc_contrato_firmado      = 'NA';
                    $ocrc_contrato_huella       = 'NA';

                    if ($fecha_mayor_edad <= $fecha_actual) {// se valida si es mayor de edad
                        $ocrc_resultado_novedad .= 'Beneficiario Mayor de Edad';
                    }

                    if ($ocrc_doc_valida && $ocrc_nombre_valida && $ocrc_apellido_valida && $ocrc_fnacimiento_valida && $ocrc_fexpedicion_valida) {
                        // cumple requisitos doc identidad beneficiario
                    } else {
                        $control_errores_beneficiario++;
                        $ocrc_resultado_novedad .= 'Documento de identidad NO validado';
                    }
                }

                if ($fecha_mayor_edad <= $fecha_actual && $ocrc_cabezafamilia == "NO") {
                    $ocrc_resultado_estado = 'Validado-Edad';// mayor de edad y no cabeza de familia
                } elseif ($control_errores_beneficiario > 0) {
                    $ocrc_resultado_estado = 'No validado-OCR';// errores en beneficiario
                    $control_errores_familia++;// suma error a familia
                } else {
                    $ocrc_resultado_estado = 'Validado-OCR';// cumple todo
                }

                if (count($resultado_registros) > 0) {
                    //registra datos de beneficiario en base consolidado
                    if ($sentencia_insert->execute()) {
                        $control_registro++;
                        $ocr_consolida_estado = 'Procesado';
                        // Ejecuta sentencia preparada
                        $consulta_actualizar->execute();
                        
                        if (comprobarSentencia($enlace_db->info)) {
                        } else {
                            $control_error++;
                        }
                    } else {
                        $control_error++;
                        $ocr_consolida_estado = 'Error';
                        // Ejecuta sentencia preparada
                        $consulta_actualizar->execute();
                        
                        if (comprobarSentencia($enlace_db->info)) {
                        } else {
                            $control_error++;
                        }
                    }
                } else {
                    $control_error++;
                }
            }

            if ($control_error == 0) {
                if ($control_errores_familia > 0) {
                    $ocrr_resultado_familia_estado = 'No validado-OCR';
                    $ocrr_gestion_estado           = 'Aplazado';
                    $ocrr_gestion_agente           = $array_analistas[$control_errores_agente];
                    $control_errores_agente++;
                    $total_analistas = count($array_analistas);
                    if ($control_errores_agente == $total_analistas) {
                        $control_errores_agente = 0;
                    }
                } else {
                    $ocrr_resultado_familia_estado = 'Validado-OCR';
                    $ocrr_gestion_estado           = 'Validado-OCR';
                    $ocrr_gestion_agente           = '';
                }

                $ocrr_gestion_intentos                   = '0';
                $ocrr_gestion_correo                     = '';
                $ocrr_gestion_observaciones              = '';
                $ocrr_gestion_fecha                      = date('Y-m-d H:i:s');
                $ocrr_gestion_notificacion               = '';
                $ocrr_gestion_notificacion_estado        = '';
                $ocrr_gestion_notificacion_fecha_registro= '';
                $ocrr_gestion_notificacion_fecha_envio   = '';
                $ocrr_gestion_llamada_tipificacion       = '';
                $ocrr_gestion_llamada_id                 = '';
                $ocrr_sr_fecha                           = '';
                $ocrr_sr_observaciones                   = '';

                if ($sentencia_insert_resultado->execute()) {
                    $observaciones_log = 'Procesado OCR primera revisión';
                    // Prepara la sentencia
                    $sentencia_insert_log = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado_avances`(`gora_codfamilia`, `gora_estado`, `gora_correo`, `gora_observaciones`, `gora_llamada_tipificacion`, `gora_llamada_id`, `gora_registro_usuario`) VALUES (?,'Procesado OCR','',?,'','','1')");

                    // Agrega variables a sentencia preparada
                    $sentencia_insert_log->bind_param('ss', $ocrr_cod_familia, $observaciones_log);

                    $sentencia_insert_log->execute();

                    if ($ocrr_gestion_agente != "") {//Si se asigna a un agente se registra log en avance de caso
                        $observaciones_log = 'Reasignar caso a usuario: ' . $ocrr_gestion_agente;
                        // Prepara la sentencia
                        $sentencia_insert_log = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado_avances`(`gora_codfamilia`, `gora_estado`, `gora_correo`, `gora_observaciones`, `gora_llamada_tipificacion`, `gora_llamada_id`, `gora_registro_usuario`) VALUES (?,'Asignado','',?,'','','1')");

                        // Agrega variables a sentencia preparada
                        $sentencia_insert_log->bind_param('ss', $ocrr_cod_familia, $observaciones_log);

                        $sentencia_insert_log->execute();
                    }

                    $ruta_actual = $ruta_pendientes . $ocrc_registro_path;
                    $ruta_nueva  = $ruta_procesados . $ocrc_registro_path;
                    $moved = rename($ruta_actual, $ruta_nueva);
                    if ($moved) {
                        // echo "File moved successfully";
                    }
                }
            } else {
                // Prepara la sentencia
                $sentencia_delete_consolidado = $enlace_db->prepare("DELETE FROM `gestion_ocr_consolidado` WHERE `ocrc_cod_familia`=?");
                // Agrega variables a sentencia preparada
                $sentencia_delete_consolidado->bind_param('s', $ocrc_cod_familia);
                if ($sentencia_delete_historial->execute()) {
                    // Prepara la sentencia
                    $sentencia_delete_resultado = $enlace_db->prepare("DELETE FROM `gestion_ocr_resultado` WHERE `ocrr_cod_familia`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_delete_resultado->bind_param('s', $ocrr_cod_familia);
                    if ($sentencia_delete_historial->execute()) {
                        $ruta_actual = $ruta_pendientes . $ocrc_registro_path;
                        $ruta_nueva  = $ruta_error . $ocrc_registro_path;
                        $moved = rename($ruta_actual, $ruta_nueva);
                        if ($moved) {
                            // echo "File moved error";
                        }
                    }
                }
            }
        } else {
            $ruta_actual = $ruta_pendientes . $ocrc_registro_path;
            $ruta_nueva  = $ruta_error . $ocrc_registro_path;
            $moved = rename($ruta_actual, $ruta_nueva);
            if ($moved) {
                // echo "File moved error";
            }
        }
    }
?>
