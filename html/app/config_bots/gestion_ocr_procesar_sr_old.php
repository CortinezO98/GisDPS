<?php
    $modulo_plataforma="Administrador";
    require_once("/var/www/html/iniciador.php");
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $ruta_pendientes="/var/www/html/templates/gestion_ocr/storage_sr_pendientes/";
    $ruta_procesados="/var/www/html/templates/gestion_ocr/storage_sr_procesado/";
    $ruta_error="/var/www/html/templates/gestion_ocr/storage_sr_error/";
    
    // $ruta_pendientes="../gestion_ocr/storage_pendientes/";
    // $ruta_procesados="../gestion_ocr/storage_procesado/";
    // $ruta_error="../gestion_ocr/storage_error/";
    $lista_archivo=scandir($ruta_pendientes);

    // Prepara la sentencia
    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ocr_consolidado`(`ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    // Agrega variables a sentencia preparada
    $sentencia_insert->bind_param('ssssssssssssssssssssssssss', $ocrc_cod_familia, $ocrc_codbeneficiario, $ocrc_cabezafamilia, $ocrc_miembro_id, $ocrc_existe, $ocrc_doc_valida, $ocrc_doc_valor, $ocrc_doc_tipo, $ocrc_nombre_valida, $ocrc_nombre_valor, $ocrc_apellido_valida, $ocrc_apellido_valor, $ocrc_fnacimiento_valida, $ocrc_fnacimiento_valor, $ocrc_fexpedicion_valida, $ocrc_fexpedicion_valor, $ocrc_contrato_existe, $ocrc_contrato_numid, $ocrc_contrato_titular, $ocrc_contrato_municipio, $ocrc_contrato_departamento, $ocrc_contrato_firmado, $ocrc_contrato_huella, $ocrc_registro_path, $ocrc_resultado_estado, $ocrc_resultado_novedad);

    // Prepara la sentencia
    $sentencia_insert_resultado = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado`(`ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_correo`, `ocrr_gestion_observaciones`, `ocrr_gestion_fecha`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_gestion_llamada_tipificacion`, `ocrr_gestion_llamada_id`, `ocrr_sr_fecha`, `ocrr_sr_observaciones`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    // Agrega variables a sentencia preparada
    $sentencia_insert_resultado->bind_param('ssssssssssssssssss', $ocrr_cod_familia, $ocrr_codbeneficiario, $ocrr_cabezafamilia, $ocrr_resultado_familia_estado, $ocrr_gestion_agente, $ocrr_gestion_estado, $ocrr_gestion_intentos, $ocrr_gestion_correo, $ocrr_gestion_observaciones, $ocrr_gestion_fecha, $ocrr_gestion_notificacion, $ocrr_gestion_notificacion_estado, $ocrr_gestion_notificacion_fecha_registro, $ocrr_gestion_notificacion_fecha_envio, $ocrr_gestion_llamada_tipificacion, $ocrr_gestion_llamada_id, $ocrr_sr_fecha, $ocrr_sr_observaciones);
    
    //Consulta miembros x codfamilia y codmiembro
    $consulta_string="SELECT `ocr_id`, `ocr_codbeneficiario`, `ocr_cabezadefamilia`, `ocr_fechanacimiento` FROM `gestion_ocr` WHERE `ocr_codfamilia`=? AND `ocr_documento`=?";
    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("ss", $ocrc_cod_familia, $ocrc_miembro_id);

    //Consulta cabeza familia por codfamilia
    $consulta_string_cabeza="SELECT `ocr_id`, `ocr_codbeneficiario`, `ocr_cabezadefamilia`, `ocr_fechanacimiento`, `ocr_documento` FROM `gestion_ocr` WHERE `ocr_codfamilia`=? AND `ocr_cabezadefamilia`='SI'";
    $consulta_registros_cabeza = $enlace_db->prepare($consulta_string_cabeza);
    $consulta_registros_cabeza->bind_param("s", $ocrc_cod_familia);

    // Prepara la sentencia
    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_ocr` SET `ocr_consolidasr_estado`=?,`ocr_consolidasr_fecha`=? WHERE `ocr_id`=?");
    // Agrega variables a sentencia preparada
    $consulta_actualizar->bind_param('sss', $ocr_consolida_estado, $ocr_consolida_fecha, $ocr_id);

    //Consulta consolidado duplicado consolidado
    $consulta_string_consolidado="SELECT `ocrc_cod_familia`, `ocrc_codbeneficiario` FROM `gestion_ocr_consolidado` WHERE `ocrc_codbeneficiario`=? AND (`ocrc_resultado_estado`='Validado-OCR-SR' OR `ocrc_resultado_estado`='No validado-OCR-SR' OR `ocrc_resultado_estado`='Validado-Edad-SR')";
    $consulta_registros_consolidado = $enlace_db->prepare($consulta_string_consolidado);
    $consulta_registros_consolidado->bind_param("s", $ocrc_codbeneficiario);

    //Consulta consolidado duplicado resultado
    $consulta_string_resultado="SELECT `ocrr_cod_familia`, `ocrr_codbeneficiario` FROM `gestion_ocr_resultado` WHERE `ocrr_cod_familia`=? AND (`ocrr_resultado_familia_estado`='Validado-OCR-SR' OR `ocrr_resultado_familia_estado`='No validado-OCR-SR')";
    $consulta_registros_resultado = $enlace_db->prepare($consulta_string_resultado);
    $consulta_registros_resultado->bind_param("s", $ocrc_cod_familia);

    if (count($lista_archivo)>10) {
        $limite_procesar=10;
    } else {
        $limite_procesar=count($lista_archivo);
    }

    $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos` FROM `administrador_usuario` WHERE `usu_estado`='Activo' AND `usu_cargo_rol`='AGENTE INSCRIPCIÓN FA' ORDER BY `usu_id`";
    $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
    $consulta_registros_analistas->execute();
    $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

    for ($i=0; $i < count($resultado_registros_analistas); $i++) { 
        $array_analistas[]=$resultado_registros_analistas[$i][0];
    }

    shuffle($array_analistas);

    // echo "<pre>";
    // print_r($array_analistas);
    // echo "</pre>";

    $control_errores_agente=0;
    for ($i=2; $i < $limite_procesar; $i++) { //recorre cada json
        $json_parser=file_get_contents($ruta_pendientes.$lista_archivo[$i]);
        $array_json=json_decode($json_parser, true);

        $ocrc_cod_familia=$array_json['familia']['codigo'];
        $ocrc_registro_path=$lista_archivo[$i];
        $ocrr_cod_familia=$ocrc_cod_familia;
        
        $control_error=0;
        $control_registro=0;
        $control_errores_familia=0;
        $control_usuario=0;
        $control_duplicado=0;
        if (count($array_json['familia']['miembros'])>0) {
            $control_cabeza_familia=0;
            //se se validó documentos de algún miembro
            for ($j=0; $j < count($array_json['familia']['miembros']); $j++) {//recorre cada miembro de familia relacionado en el json
                $ocrc_miembro_id=$array_json['familia']['miembros'][$j]['id'];
                $ocrc_existe=$array_json['familia']['miembros'][$j]['existe'];
                $ocrc_doc_valida=$array_json['familia']['miembros'][$j]['documento']['validacion'];
                $ocrc_doc_valor=$array_json['familia']['miembros'][$j]['documento']['valor'];
                $ocrc_doc_tipo=$array_json['familia']['miembros'][$j]['tipo'];
                $ocrc_nombre_valida=$array_json['familia']['miembros'][$j]['nombres']['validacion'];
                $ocrc_nombre_valor=$array_json['familia']['miembros'][$j]['nombres']['valor'];
                $ocrc_apellido_valida=$array_json['familia']['miembros'][$j]['apellidos']['validacion'];
                $ocrc_apellido_valor=$array_json['familia']['miembros'][$j]['apellidos']['valor'];
                $ocrc_fnacimiento_valida=$array_json['familia']['miembros'][$j]['fecha_nacimiento']['validacion'];
                $ocrc_fnacimiento_valor=$array_json['familia']['miembros'][$j]['fecha_nacimiento']['valor'];
                $ocrc_fexpedicion_valida=$array_json['familia']['miembros'][$j]['fecha_expedicion']['validacion'];
                $ocrc_fexpedicion_valor=$array_json['familia']['miembros'][$j]['fecha_expedicion']['valor'];

                $consulta_registros->execute();//consulta datos de base OCR original
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
                
                $ocrc_codbeneficiario=$resultado_registros[0][1];
                $ocrc_cabezafamilia=$resultado_registros[0][2];
                $ocrc_fechanacimiento=$resultado_registros[0][3];

                $fecha_mayor_edad = date("Y-m", strtotime("+ 18 year", strtotime($ocrc_fechanacimiento)));//calcula fecha de cumple mayoría de edad según fecha de nacimiento
                $ocr_consolida_fecha=date('Y-m-d H:i:s');
                $fecha_actual=date('Y-m');
                $ocr_id=$resultado_registros[0][0];
                $ocrc_resultado_novedad='';
                $control_errores_beneficiario=0;//controla si el beneficiario tiene errores de los items validados
                
                if ($ocrc_cabezafamilia=="SI") {
                    $control_cabeza_familia=1;
                    $ocrr_codbeneficiario=$ocrc_codbeneficiario;
                    $ocrr_cabezafamilia=$ocrc_cabezafamilia;
                    if (isset($array_json['familia']['contrato']['existe'])) {
                        //datos json contrato
                        $ocrc_contrato_existe=$array_json['familia']['contrato']['existe'];
                        $ocrc_contrato_numid=$array_json['familia']['contrato']['numId'];
                        $ocrc_contrato_titular=$array_json['familia']['contrato']['titular'];
                        $ocrc_contrato_municipio=$array_json['familia']['contrato']['municipio'];
                        $ocrc_contrato_departamento=$array_json['familia']['contrato']['departamento'];
                        $ocrc_contrato_firmado=$array_json['familia']['contrato']['firmado'];
                        $ocrc_contrato_huella=$array_json['familia']['contrato']['huella'];

                        if ($ocrc_contrato_existe AND $ocrc_contrato_numid AND $ocrc_contrato_firmado AND $ocrc_contrato_huella) {//se valida que cumpla requisitos para contrato cabeza familia
                            
                        } else {
                            $control_errores_beneficiario++;
                            $ocrc_resultado_novedad.='Contrato NO validado';
                        }
                    } else {
                        $ocrc_contrato_existe='NA';
                        $ocrc_contrato_numid='NA';
                        $ocrc_contrato_titular='NA';
                        $ocrc_contrato_municipio='NA';
                        $ocrc_contrato_departamento='NA';
                        $ocrc_contrato_firmado='NA';
                        $ocrc_contrato_huella='NA';
                    }

                    if ($ocrc_doc_valida AND $ocrc_nombre_valida AND $ocrc_apellido_valida AND $ocrc_fnacimiento_valida AND $ocrc_fexpedicion_valida) {//se valida que cumpla requisitos para doc de identidad de cabeza familia
                        
                    } else {
                        $control_errores_beneficiario++;
                        $ocrc_resultado_novedad.='Documento de identidad NO validado, ';
                    }
                } else {//si no es cabeza familia
                    $ocrc_contrato_existe='NA';
                    $ocrc_contrato_numid='NA';
                    $ocrc_contrato_titular='NA';
                    $ocrc_contrato_municipio='NA';
                    $ocrc_contrato_departamento='NA';
                    $ocrc_contrato_firmado='NA';
                    $ocrc_contrato_huella='NA';

                    if ($fecha_mayor_edad<=$fecha_actual) {//se valida si es mayor de edad
                        $ocrc_resultado_novedad.='Beneficiario Mayor de Edad';
                    }

                    if ($ocrc_doc_valida AND $ocrc_nombre_valida AND $ocrc_apellido_valida AND $ocrc_fnacimiento_valida AND $ocrc_fexpedicion_valida) {//se valida que cumpla requisitos para doc de identidad de beneficiario
                        
                    } else {
                        $control_errores_beneficiario++;
                        $ocrc_resultado_novedad.='Documento de identidad NO validado';
                    }
                }

                if ($fecha_mayor_edad<=$fecha_actual AND $ocrc_cabezafamilia=="NO") {
                    $ocrc_resultado_estado='Validado-Edad-SR';//se establece estado de registro beneficiario si es mayor de edad y no es cabeza de familia
                } elseif ($control_errores_beneficiario>0) {
                    $ocrc_resultado_estado='No validado-OCR-SR';//si no es cabeza familia y tiene algún error el beneficiario
                    $control_errores_familia++;//se suma error a errores familia
                } else {
                    $ocrc_resultado_estado='Validado-OCR-SR';//cumple todos los requisitos, no es mayor de edad y no es cabeza de familia y no tiene errores
                }

                if (count($resultado_registros)>0) {
                    $control_usuario++;

                    $consulta_registros_consolidado->execute();//consulta datos de base OCR original
                    $resultado_registros_consolidado = $consulta_registros_consolidado->get_result()->fetch_all(MYSQLI_NUM);

                    if (count($resultado_registros_consolidado)==0) {
                        //registra datos de beneficiario en base consolidado
                        if ($sentencia_insert->execute()) {
                            $control_registro++;
                            $ocr_consolida_estado='Procesado';
                            // Ejecuta sentencia preparada
                            $consulta_actualizar->execute();
                            
                            if (comprobarSentencia($enlace_db->info)) {
                            } else {
                                $control_error++;
                            }
                        } else {
                            $control_error++;
                            $ocr_consolida_estado='Error';
                            // Ejecuta sentencia preparada
                            $consulta_actualizar->execute();
                            
                            if (comprobarSentencia($enlace_db->info)) {
                            } else {
                                $control_error++;
                            }
                        }
                    } else {
                        $control_duplicado=1;
                    }
                } else {
                    $control_error++;
                }
            }

            if ($control_cabeza_familia==0) {
                $consulta_registros_cabeza->execute();//consulta datos de base OCR original
                $resultado_registros_cabeza = $consulta_registros_cabeza->get_result()->fetch_all(MYSQLI_NUM);

                $ocrc_codbeneficiario=$resultado_registros_cabeza[0][1];
                $ocrc_cabezafamilia=$resultado_registros_cabeza[0][2];
                $ocrc_fechanacimiento=$resultado_registros_cabeza[0][3];
                
                $ocrr_codbeneficiario=$ocrc_codbeneficiario;
                $ocrr_cabezafamilia=$ocrc_cabezafamilia;

                $ocrc_miembro_id=$resultado_registros_cabeza[0][4];
                $ocrc_existe='NA';
                $ocrc_doc_valida='NA';
                $ocrc_doc_valor='NA';
                $ocrc_doc_tipo='NA';
                $ocrc_nombre_valida='NA';
                $ocrc_nombre_valor='NA';
                $ocrc_apellido_valida='NA';
                $ocrc_apellido_valor='NA';
                $ocrc_fnacimiento_valida='NA';
                $ocrc_fnacimiento_valor='NA';
                $ocrc_fexpedicion_valida='NA';
                $ocrc_fexpedicion_valor='NA';
                
                $ocr_consolida_fecha=date('Y-m-d H:i:s');
                $fecha_actual=date('Y-m');
                $ocr_id=$resultado_registros_cabeza[0][0];

                $ocrc_resultado_novedad='';
                
                if (isset($array_json['familia']['contrato']['existe'])) {
                    //datos json contrato
                    $ocrc_contrato_existe=$array_json['familia']['contrato']['existe'];
                    $ocrc_contrato_numid=$array_json['familia']['contrato']['numId'];
                    $ocrc_contrato_titular=$array_json['familia']['contrato']['titular'];
                    $ocrc_contrato_municipio=$array_json['familia']['contrato']['municipio'];
                    $ocrc_contrato_departamento=$array_json['familia']['contrato']['departamento'];
                    $ocrc_contrato_firmado=$array_json['familia']['contrato']['firmado'];
                    $ocrc_contrato_huella=$array_json['familia']['contrato']['huella'];

                    if ($ocrc_contrato_existe AND $ocrc_contrato_numid AND $ocrc_contrato_firmado AND $ocrc_contrato_huella) {//se valida que cumpla requisitos para contrato cabeza familia
                        $ocrc_resultado_estado='Validado-OCR-SR';
                    } else {
                        $control_errores_familia++;
                        $ocrc_resultado_estado='No validado-OCR-SR';
                        $ocrc_resultado_novedad.='Contrato NO validado';
                    }
                } else {
                    $ocrc_contrato_existe='NA';
                    $ocrc_contrato_numid='NA';
                    $ocrc_contrato_titular='NA';
                    $ocrc_contrato_municipio='NA';
                    $ocrc_contrato_departamento='NA';
                    $ocrc_contrato_firmado='NA';
                    $ocrc_contrato_huella='NA';
                    $ocrc_resultado_estado='Validado-OCR-SR';
                }

                
                if (count($resultado_registros_cabeza)>0) {

                    $consulta_registros_consolidado->execute();//consulta datos de base OCR original
                    $resultado_registros_consolidado = $consulta_registros_consolidado->get_result()->fetch_all(MYSQLI_NUM);

                    if(count($resultado_registros_consolidado)==0) {
                        //registra datos de beneficiario en base consolidado
                        if ($sentencia_insert->execute()) {
                            $control_registro++;
                            $ocr_consolida_estado='Procesado';
                            // Ejecuta sentencia preparada
                            $consulta_actualizar->execute();
                            
                            if (comprobarSentencia($enlace_db->info)) {
                            } else {
                                $control_error++;
                            }
                        } else {
                            $control_error++;
                            $ocr_consolida_estado='Error';
                            // Ejecuta sentencia preparada
                            $consulta_actualizar->execute();
                            
                            if (comprobarSentencia($enlace_db->info)) {
                            } else {
                                $control_error++;
                            }
                        }
                    } else {
                        $control_duplicado=1;
                    }
                } else {
                    $control_error++;
                }
            }
        } else {
            //solo se validó contrato y asociar resultado a cabeza de familia
            $consulta_registros_cabeza->execute();//consulta datos de base OCR original
            $resultado_registros_cabeza = $consulta_registros_cabeza->get_result()->fetch_all(MYSQLI_NUM);

            $ocrc_codbeneficiario=$resultado_registros_cabeza[0][1];
            $ocrc_cabezafamilia=$resultado_registros_cabeza[0][2];
            $ocrc_fechanacimiento=$resultado_registros_cabeza[0][3];
            
            $ocrr_codbeneficiario=$ocrc_codbeneficiario;
            $ocrr_cabezafamilia=$ocrc_cabezafamilia;
            if (count($resultado_registros_cabeza)>0) {
                $control_usuario++;
                $ocrc_miembro_id=$resultado_registros_cabeza[0][4];
                $ocrc_existe='NA';
                $ocrc_doc_valida='NA';
                $ocrc_doc_valor='NA';
                $ocrc_doc_tipo='NA';
                $ocrc_nombre_valida='NA';
                $ocrc_nombre_valor='NA';
                $ocrc_apellido_valida='NA';
                $ocrc_apellido_valor='NA';
                $ocrc_fnacimiento_valida='NA';
                $ocrc_fnacimiento_valor='NA';
                $ocrc_fexpedicion_valida='NA';
                $ocrc_fexpedicion_valor='NA';
                
                $ocr_consolida_fecha=date('Y-m-d H:i:s');
                $fecha_actual=date('Y-m');
                $ocr_id=$resultado_registros_cabeza[0][0];

                $ocrc_resultado_novedad='';
                
                if (isset($array_json['familia']['contrato']['existe'])) {
                    //datos json contrato
                    $ocrc_contrato_existe=$array_json['familia']['contrato']['existe'];
                    $ocrc_contrato_numid=$array_json['familia']['contrato']['numId'];
                    $ocrc_contrato_titular=$array_json['familia']['contrato']['titular'];
                    $ocrc_contrato_municipio=$array_json['familia']['contrato']['municipio'];
                    $ocrc_contrato_departamento=$array_json['familia']['contrato']['departamento'];
                    $ocrc_contrato_firmado=$array_json['familia']['contrato']['firmado'];
                    $ocrc_contrato_huella=$array_json['familia']['contrato']['huella'];

                    if ($ocrc_contrato_existe AND $ocrc_contrato_numid AND $ocrc_contrato_firmado AND $ocrc_contrato_huella) {//se valida que cumpla requisitos para contrato cabeza familia
                        $ocrc_resultado_estado='Validado-OCR-SR';
                    } else {
                        $control_errores_familia++;
                        $ocrc_resultado_estado='No validado-OCR-SR';
                        $ocrc_resultado_novedad.='Contrato NO validado';
                    }
                } else {
                    $ocrc_contrato_existe='NA';
                    $ocrc_contrato_numid='NA';
                    $ocrc_contrato_titular='NA';
                    $ocrc_contrato_municipio='NA';
                    $ocrc_contrato_departamento='NA';
                    $ocrc_contrato_firmado='NA';
                    $ocrc_contrato_huella='NA';
                    $ocrc_resultado_estado='Validado-OCR-SR';
                }

                if (count($resultado_registros_cabeza)>0) {

                    $consulta_registros_consolidado->execute();//consulta datos de base OCR original
                    $resultado_registros_consolidado = $consulta_registros_consolidado->get_result()->fetch_all(MYSQLI_NUM);

                    if(count($resultado_registros_consolidado)==0) {
                        //registra datos de beneficiario en base consolidado
                        if ($sentencia_insert->execute()) {
                            $control_registro++;
                            $ocr_consolida_estado='Procesado';
                            // Ejecuta sentencia preparada
                            $consulta_actualizar->execute();
                            
                            if (comprobarSentencia($enlace_db->info)) {
                            } else {
                                $control_error++;
                            }
                        } else {
                            $control_error++;
                            $ocr_consolida_estado='Error';
                            // Ejecuta sentencia preparada
                            $consulta_actualizar->execute();
                            
                            if (comprobarSentencia($enlace_db->info)) {
                            } else {
                                $control_error++;
                            }
                        }
                    } else {
                        $control_duplicado=1;
                    }
                } else {
                    $control_error++;
                }
            }
        }

        if ($control_error==0 AND $control_usuario>0) {
            if ($control_errores_familia>0) {
                $ocrr_resultado_familia_estado='No validado-OCR-SR';
                $ocrr_gestion_estado='Aplazado Segunda Revisión';
                $ocrr_gestion_agente=$array_analistas[$control_errores_agente];
                $control_errores_agente++;
                $total_analistas=count($array_analistas);
                if ($control_errores_agente==$total_analistas) {
                    $control_errores_agente=0;
                }
            } else {
                $ocrr_resultado_familia_estado='Validado-OCR-SR';
                $ocrr_gestion_estado='Validado-OCR-Segunda Revisión';
                $ocrr_gestion_agente='';
            }

            $ocrr_gestion_intentos='0';
            $ocrr_gestion_correo='';
            $ocrr_gestion_observaciones='';
            $ocrr_gestion_fecha=date('Y-m-d H:i:s');
            $ocrr_gestion_notificacion='';
            $ocrr_gestion_notificacion_estado='';
            $ocrr_gestion_notificacion_fecha_registro='';
            $ocrr_gestion_notificacion_fecha_envio='';
            $ocrr_gestion_llamada_tipificacion='';
            $ocrr_gestion_llamada_id='';
            $ocrr_sr_fecha='';
            $ocrr_sr_observaciones='';

            $consulta_registros_resultado->execute();//consulta datos de base OCR original
            $resultado_registros_resultado = $consulta_registros_resultado->get_result()->fetch_all(MYSQLI_NUM);

            if(count($resultado_registros_resultado)==0) {
                if ($sentencia_insert_resultado->execute()) {
                    $ruta_actual = $ruta_pendientes.$ocrc_registro_path;
                    $ruta_nueva = $ruta_procesados.$ocrc_registro_path;
                    $moved = rename($ruta_actual, $ruta_nueva);
                    if($moved) {
                        // echo "File moved successfully";
                    }
                }
            } else {
                $ruta_actual = $ruta_pendientes.$ocrc_registro_path;
                $ruta_nueva = $ruta_error.$ocrc_registro_path;
                $moved = rename($ruta_actual, $ruta_nueva);
                if($moved) {
                    // echo "File moved successfully";
                }
            }
        } else {
            // // Prepara la sentencia
            // $sentencia_delete_consolidado = $enlace_db->prepare("DELETE FROM `gestion_ocr_consolidado` WHERE `ocrc_cod_familia`=?");
            // // Agrega variables a sentencia preparada
            // $sentencia_delete_consolidado->bind_param('s', $ocrc_cod_familia);
            // if ($sentencia_delete_historial->execute()) {
            //     // Prepara la sentencia
            //     $sentencia_delete_resultado = $enlace_db->prepare("DELETE FROM `gestion_ocr_resultado` WHERE `ocrr_cod_familia`=?");
            //     // Agrega variables a sentencia preparada
            //     $sentencia_delete_resultado->bind_param('s', $ocrr_cod_familia);
            //     if ($sentencia_delete_historial->execute()) {
            //         $ruta_actual = $ruta_pendientes.$ocrc_registro_path;
            //         $ruta_nueva = $ruta_error.$ocrc_registro_path;
            //         $moved = rename($ruta_actual, $ruta_nueva);
            //         if($moved) {
            //             // echo "File moved error";
            //         }
            //     }
            // }
        }
    }
?>