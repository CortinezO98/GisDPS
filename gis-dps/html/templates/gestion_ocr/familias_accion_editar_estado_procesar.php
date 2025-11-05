<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Gestión OCR-Gestión";
    require_once("../../iniciador.php");
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    /*DEFINICIÓN DE VARIABLES*/
    $id_registro=validar_input(base64_decode($_GET['reg']));
    $id_beneficiario=validar_input(base64_decode($_GET['beneficiario']));
    $id_item=validar_input($_GET['item']);

    if ($id_registro!='' AND $id_beneficiario!='' AND $id_item!='') {
        $estado_item=validar_input($_POST['estado_item']);
        $observaciones_item='Actualiza estado Beneficiario: '.$id_beneficiario.' | Item: '.$id_item.' | Nuevo estado: '.$estado_item.' | Observaciones: '.validar_input($_POST['observaciones_item']);
        
        if ($estado_item=='Cumple') {
            $estado_actualizar='1';
        } else {
            $estado_actualizar='';
        }

        if ($id_item=='contrato_existe') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_contrato_existe`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }

        if ($id_item=='contrato_codigo') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_contrato_numid`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }

        if ($id_item=='contrato_nombre') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_contrato_titular`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }

        if ($id_item=='contrato_firma') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_contrato_firmado`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }

        if ($id_item=='contrato_huella') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_contrato_huella`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }

        if ($id_item=='documento') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_doc_valida`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }

        if ($id_item=='nombres') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_nombre_valida`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }

        if ($id_item=='apellidos') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_apellido_valida`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }

        if ($id_item=='fecha_nacimiento') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_fnacimiento_valida`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }

        if ($id_item=='fecha_expedicion') {
            // Prepara la sentencia
            $sentencia_update_estado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_fexpedicion_valida`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
        }
        
        // Agrega variables a sentencia preparada
        $sentencia_update_estado->bind_param('sss', $estado_actualizar, $id_registro, $id_beneficiario);
        // $sentencia_update_estado->execute();

        if ($sentencia_update_estado->execute()) {
            $resultado_update_estado="<p class='alert alert-success p-1 mb-1 text-center font-size-11'>¡Estado actualizado exitosamente!</p>";
            $resultado_update_estado_valor=1;

            $consulta_string_validar="SELECT `ocrc_id`, `ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`, `ocrc_registro_fecha` FROM `gestion_ocr_consolidado` WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=? ORDER BY `ocrc_registro_fecha` ASC";

            $consulta_registros_validar = $enlace_db->prepare($consulta_string_validar);
            $consulta_registros_validar->bind_param("ss", $id_registro, $id_beneficiario);
            $consulta_registros_validar->execute();
            $resultado_registros_validar = $consulta_registros_validar->get_result()->fetch_all(MYSQLI_NUM);

            $estado_avance='Actualiza estado item';
            // Prepara la sentencia
            $sentencia_insert_avance = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado_avances`(`gora_codfamilia`, `gora_estado`, `gora_correo`, `gora_observaciones`, `gora_llamada_tipificacion`, `gora_llamada_id`, `gora_registro_usuario`) VALUES (?,?,'',?,'','',?)");

            // Agrega variables a sentencia preparada
            $sentencia_insert_avance->bind_param('ssss', $resultado_registros_validar[0][1], $estado_avance, $observaciones_item, $_SESSION[APP_SESSION.'_session_usu_id']);

            $sentencia_insert_avance->execute();

            $ocrc_doc_valida=$resultado_registros_validar[0][6];
            $ocrc_nombre_valida=$resultado_registros_validar[0][9];
            $ocrc_apellido_valida=$resultado_registros_validar[0][11];
            $ocrc_fnacimiento_valida=$resultado_registros_validar[0][13];
            $ocrc_fexpedicion_valida=$resultado_registros_validar[0][15];

            $control_errores_beneficiario=0;
            if ($ocrc_doc_valida AND $ocrc_nombre_valida AND $ocrc_apellido_valida AND $ocrc_fnacimiento_valida AND $ocrc_fexpedicion_valida) {//se valida que cumpla requisitos para doc de identidad de cabeza familia
                    
            } else {
                $control_errores_beneficiario++;
            }

            if ($resultado_registros_validar[0][3]=='SI') {
                $ocrc_contrato_existe=$resultado_registros_validar[0][17];
                $ocrc_contrato_numid=$resultado_registros_validar[0][18];
                $ocrc_contrato_firmado=$resultado_registros_validar[0][22];
                $ocrc_contrato_huella=$resultado_registros_validar[0][23];
                
                if ($ocrc_contrato_existe AND $ocrc_contrato_numid AND $ocrc_contrato_firmado AND $ocrc_contrato_huella) {//se valida que cumpla requisitos para contrato cabeza familia
                    
                } else {
                    $control_errores_beneficiario++;
                }
            }

            if ($control_errores_beneficiario==0) {
                if ($resultado_registros_validar[0][25]=='No validado-OCR-SR' OR $resultado_registros_validar[0][25]=='Validado-OCR-SR') {
                    $estado_update_resultado='Validado-Agente-SR';
                } else {
                    $estado_update_resultado='Validado-Agente';
                }

                // Prepara la sentencia
                $sentencia_update_resultado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_resultado_estado`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
                // Agrega variables a sentencia preparada
                $sentencia_update_resultado->bind_param('sss', $estado_update_resultado, $id_registro, $id_beneficiario);
                $sentencia_update_resultado->execute();
            } elseif ($control_errores_beneficiario>0) {
                if ($resultado_registros_validar[0][25]=='No validado-OCR-SR' OR $resultado_registros_validar[0][25]=='Validado-OCR-SR' OR $resultado_registros_validar[0][25]=='Validado-Agente-SR') {
                    $estado_update_resultado='No validado-OCR-SR';
                } else {
                    $estado_update_resultado='No validado-OCR';
                }

                // Prepara la sentencia
                $sentencia_update_resultado = $enlace_db->prepare("UPDATE `gestion_ocr_consolidado` SET `ocrc_resultado_estado`=? WHERE `ocrc_id`=? AND `ocrc_codbeneficiario`=?");
                // Agrega variables a sentencia preparada
                $sentencia_update_resultado->bind_param('sss', $estado_update_resultado, $id_registro, $id_beneficiario);
                $sentencia_update_resultado->execute();
            }
        } else {
            $resultado_update_estado="<p class='alert alert-danger p-1 mb-1 text-center font-size-11'>¡Problemas al actualizar el estado!</p>";
            $resultado_update_estado_valor=0;
        }

        $data = array(
            "resultado_estado" => $resultado_update_estado,
            "resultado_estado_valor" => $resultado_update_estado_valor,
            "estado" => $estado_item
        );

        echo json_encode($data);
    }
?>