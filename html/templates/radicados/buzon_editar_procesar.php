<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Radicación";
    require_once("../../iniciador.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    /*DEFINICIÓN DE VARIABLES*/
    $accion=validar_input($_GET['accion']);
    $tipo=validar_input($_GET['tipo']);
    $resultado_radicado='';
    $resultado_radicado_id='';
    $resultado_historial_id='';

    if ($accion=='plantilla' AND $tipo!='') {
        $consulta_string="SELECT `grcp_id`, `grcp_nombre`, `grcp_estado`, `grcp_tipo`, `grcp_contenido`, `grcp_actualiza_usuario`, `grcp_actualiza_fecha`, `grcp_registro_usuario`, `grcp_registro_fecha`, TUA.`usu_nombres_apellidos` FROM `gestion_radicacion_casos_plantillas` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_radicacion_casos_plantillas`.`grcp_actualiza_usuario`=TUA.`usu_id` WHERE `grcp_estado`='Activo' AND `grcp_tipo`=? ORDER BY `grcp_nombre` ASC";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $tipo);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $resultado_data='';
        $resultado_data.='<option class="font-size-11 py-0" value="" class="font-size-11">Seleccione</option>';
        if (count($resultado_registros)>0) {
            $resultado_control=1;
            for ($i=0; $i < count($resultado_registros); $i++) { 
                $resultado_data.='<option class="font-size-11 py-0" value="'.$resultado_registros[$i][0].'" class="font-size-11">'.$resultado_registros[$i][1].'</option>';
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='plantilla' AND $tipo=='obtener') {
        $id_registro=validar_input($_POST['id_plantilla']);
        $consulta_string="SELECT `grcp_id`, `grcp_nombre`, `grcp_estado`, `grcp_tipo`, `grcp_contenido`, `grcp_actualiza_usuario`, `grcp_actualiza_fecha`, `grcp_registro_usuario`, `grcp_registro_fecha`, TUA.`usu_nombres_apellidos` FROM `gestion_radicacion_casos_plantillas` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_radicacion_casos_plantillas`.`grcp_actualiza_usuario`=TUA.`usu_id` WHERE `grcp_estado`='Activo' AND `grcp_id`=?";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $id_registro);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $resultado_data='';
        if (count($resultado_registros)>0) {
            $resultado_control=1;
            $resultado_data=$resultado_registros[0][4];
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='adjuntos' AND $tipo=='lista_lectura') {
        $historial_id=validar_input($_POST['historial_id']);

        $consulta_string="SELECT `grca_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_radicado`, `grca_radicado_id` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_historial_id`=? ORDER BY `grca_id` ASC";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $historial_id);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $resultado_data='';
        if (count($resultado_registros)>0) {
            $resultado_control=count($resultado_registros);
            $control_separa=false;
            
            for ($i=0; $i < count($resultado_registros); $i++) { 
                if (!$control_separa AND $resultado_registros[$i][5]=='Adjunto') {
                    // $control_separa=true;
                    // $resultado_data.='<hr class="my-1">';
                }

                if ($resultado_registros[$i][5]=='Original' AND $resultado_registros[$i][6]=='Inactivo') {
                    $tipo_btn='btn-outline-danger';
                    $tachado='tachado';
                    $quita_agrega='<a class="dropdown-item adjuntos_borrador d-none " href="#" onclick="adjuntos_agregar('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-check-circle"></span> Agregar adjunto</a>';
                } else {
                    $tipo_btn='btn-secondary';
                    $tachado='';
                    $quita_agrega='<a class="dropdown-item adjuntos_borrador d-none " href="#" onclick="adjuntos_quitar('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-times-circle"></span> Quitar adjunto</a>';
                }

                $resultado_data.='
                    <div class="btn-group mb-1 me-2">
                      <button type="button" class="btn '.$tipo_btn.' '.$tachado.' px-1 py-2" onclick="adjuntos_previa('."'".base64_encode($resultado_registros[$i][0])."'".')">'.validar_extension_icono($resultado_registros[$i][4]).' '.$resultado_registros[$i][2].'</button>
                      <button type="button" class="btn '.$tipo_btn.' px-2 py-2 dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                        <a class="dropdown-item" href="#" onclick="adjuntos_previa('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-eye"></span> Vista previa</a>
                        <a class="dropdown-item" href="buzon_editar_adjunto_descargar.php?id_registro='.base64_encode($resultado_registros[$i][0]).'" target="_blank"><span class="fas fa-download"></span> Descargar</a>
                        '.$quita_agrega.'
                      </div>
                    </div>
                ';
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='adjuntos' AND $tipo=='lista_ver') {
        $historial_id=validar_input($_POST['historial_id']);

        $consulta_string="SELECT `grca_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_radicado`, `grca_radicado_id` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_historial_id`=? ORDER BY `grca_id` ASC";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $historial_id);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $resultado_data='';
        if (count($resultado_registros)>0) {
            $resultado_control=count($resultado_registros);
            $control_separa=false;
            
            for ($i=0; $i < count($resultado_registros); $i++) { 
                if (!$control_separa AND $resultado_registros[$i][5]=='Adjunto') {
                    // $control_separa=true;
                    // $resultado_data.='<hr class="my-1">';
                }

                if ($resultado_registros[$i][5]=='Original' AND $resultado_registros[$i][6]=='Inactivo') {
                    $tipo_btn='btn-outline-danger';
                    $tachado='tachado';
                    $quita_agrega='';
                } else {
                    $tipo_btn='btn-secondary';
                    $tachado='';
                    $quita_agrega='';
                }

                $resultado_data.='
                    <div class="btn-group mb-1 me-2">
                      <button type="button" class="btn '.$tipo_btn.' '.$tachado.' px-1 py-2" onclick="adjuntos_previa('."'".base64_encode($resultado_registros[$i][0])."'".')">'.validar_extension_icono($resultado_registros[$i][4]).' '.$resultado_registros[$i][2].'</button>
                      <button type="button" class="btn '.$tipo_btn.' px-2 py-2 dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                        <a class="dropdown-item" href="#" onclick="adjuntos_previa('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-eye"></span> Vista previa</a>
                        <a class="dropdown-item" href="buzon_editar_adjunto_descargar.php?id_registro='.base64_encode($resultado_registros[$i][0]).'" target="_blank"><span class="fas fa-download"></span> Descargar</a>
                        '.$quita_agrega.'
                      </div>
                    </div>
                ';
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='adjuntos' AND $tipo=='cargar') {
        $radicado=validar_input($_POST['radicado']);
        $radicado_id=validar_input($_POST['radicado_id']);
        $historial_id=validar_input($_POST['historial_id']);
        $response = array();

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["adjuntos"])) {
            $carpetaDestino = "adjuntos/"; // Ruta donde se guardarán los archivos
            $resultado_control=1;
            $resultado_data='';

            // $ruta_guardar="/var/www/html/templates/radicados/storage/".$radicado."/";
            // if (!file_exists($ruta_guardar)) {
            //     mkdir($ruta_guardar, 0777, true);
            // }

            // Prepara la sentencia
            $resultado_insert_adjunto = $enlace_db->prepare("INSERT INTO `gestion_radicacion_casos_adjuntos`(`grca_radicado`, `grca_radicado_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?)");
            // Agrega variables a sentencia preparada
            $resultado_insert_adjunto->bind_param('sssssssss', $radicado, $radicado_id, $historial_id, $nombreArchivo, $rutaDestino, $extension, $grca_tipo, $grca_estado, $grca_registro_usuario);

            // Itera a través de los archivos adjuntos
            foreach ($_FILES["adjuntos"]["tmp_name"] as $key => $archivoTemporal) {
                $nombreArchivo = $_FILES["adjuntos"]["name"][$key];
                $rutaDestino = $carpetaDestino.$radicado.'_'.$historial_id.'_'.$nombreArchivo;
                $extension=pathinfo($nombreArchivo, PATHINFO_EXTENSION);
                $grca_tipo='Adjunto';
                $grca_estado='Activo';
                $grca_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

                // Verifica y mueve el archivo al servidor
                if (move_uploaded_file($archivoTemporal, $rutaDestino)) {
                    
                    if ($resultado_insert_adjunto->execute()) {
                        $resultado_data.='Adjunto '.$nombreArchivo.' cargado con éxito.';
                    } else {
                        $resultado_data.='Error al cargar el adjunto '.$nombreArchivo.'.';
                    }
                } else {
                    $resultado_data.='Error al cargar el adjunto '.$nombreArchivo.'.';
                }
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='adjuntos' AND $tipo=='quitar') {
        $id_documento=validar_input(base64_decode($_POST['id_documento']));

        if ($id_documento!='') {
            $resultado_data='';

            $consulta_string_validar_pre="SELECT `grca_id`, `grca_radicado`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_radicado_id`, `grca_historial_id` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_id`=?";
            $consulta_registros_validar_pre = $enlace_db->prepare($consulta_string_validar_pre);
            $consulta_registros_validar_pre->bind_param("s", $id_documento);
            $consulta_registros_validar_pre->execute();
            $resultado_registros_validar_pre = $consulta_registros_validar_pre->get_result()->fetch_all(MYSQLI_NUM);
            
            $resultado_radicado=$resultado_registros_validar_pre[0][1];
            $resultado_radicado_id=$resultado_registros_validar_pre[0][7];
            $resultado_historial_id=$resultado_registros_validar_pre[0][8];

            if ($resultado_registros_validar_pre[0][5]=='Original') {
                $grca_estado='Inactivo';
                
                // Prepara la sentencia
                $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_radicacion_casos_adjuntos` SET `grca_estado`=? WHERE `grca_id`=?");

                // Agrega variables a sentencia preparada
                $consulta_actualizar->bind_param('ss', $grca_estado, $id_documento);

                // Ejecuta sentencia preparada
                $consulta_actualizar->execute();

                if (comprobarSentencia($enlace_db->info)) {
                    $resultado_control=1;
                } else {
                    $resultado_control=0;
                }
            } else {
                // Prepara la sentencia
                $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_id`=?");

                // Agrega variables a sentencia preparada
                $sentencia_delete->bind_param("s", $id_documento);

                // Evalua resultado de ejecución sentencia preparada
                if ($sentencia_delete->execute()) {
                    unlink($resultado_registros_validar_pre[0][3]);
                    $resultado_control=1;
                } else {
                    $resultado_control=0;
                }
            }

        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='adjuntos' AND $tipo=='agregar') {
        $id_registro=validar_input(base64_decode($_POST['id_registro']));
        $id_documento=validar_input(base64_decode($_POST['id_documento']));

        if ($id_registro!='' && $id_documento!='') {
            $resultado_data='';

            $consulta_string_validar_pre="SELECT `grca_id`, `grca_radicado`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_id`=? AND `grca_caso`=?";
            $consulta_registros_validar_pre = $enlace_db->prepare($consulta_string_validar_pre);
            $consulta_registros_validar_pre->bind_param("ss", $id_documento, $id_registro);
            $consulta_registros_validar_pre->execute();
            $resultado_registros_validar_pre = $consulta_registros_validar_pre->get_result()->fetch_all(MYSQLI_NUM);
              
            if ($resultado_registros_validar_pre[0][5]=='Original' AND $resultado_registros_validar_pre[0][6]=='Inactivo') {
                $grca_estado='Activo';
                
                // Prepara la sentencia
                $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_radicacion_casos_adjuntos` SET `grca_estado`=? WHERE `grca_id`=? AND `grca_caso`=?");

                // Agrega variables a sentencia preparada
                $consulta_actualizar->bind_param('sss', $grca_estado, $id_documento, $id_registro);

                // Ejecuta sentencia preparada
                $consulta_actualizar->execute();

                if (comprobarSentencia($enlace_db->info)) {
                    $resultado_control=1;
                } else {
                    $resultado_control=0;
                }
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='historico' AND $tipo=='crear') {
        $id_registro=validar_input($_POST['id_registro']);
        
        if ($id_registro!='') {
            $resultado_data='';

            $consulta_string_historico="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha` FROM `gestion_radicacion_casos_historial` WHERE `grch_id`=?";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);
              
            if ($resultado_registros_historico[0][3]=='Radicado') {
                // Prepara la sentencia
                $consulta_registro_historial_insert = $enlace_db->prepare("INSERT INTO `gestion_radicacion_casos_historial`(`grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_de_nombre`, `grch_correo_para`, `grch_correo_para_nombre`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                // Agrega variables a sentencia preparada
                $consulta_registro_historial_insert->bind_param('sssssssssssssssssssssssssssssss', $grch_radicado, $grch_radicado_id, $grch_tipo, $grch_tipologia, $grch_clasificacion, $grch_gestion, $grch_gestion_detalle, $grch_duplicado, $grch_unificado, $grch_unificado_id, $grch_dividido, $grch_dividido_cantidad, $grch_observaciones, $grch_correo_id, $grch_correo_de, $grch_correo_de_nombre, $grch_correo_para, $grch_correo_para_nombre, $grch_correo_cc, $grch_correo_bcc, $grch_correo_fecha, $grch_correo_asunto, $grch_correo_contenido, $grch_embeddedimage_ruta, $grch_embeddedimage_nombre, $grch_embeddedimage_tipo, $grch_attachment_ruta, $grch_intentos, $grch_estado_envio, $grch_fecha_envio, $grch_registro_usuario);
                
                $grch_radicado=$resultado_registros_historico[0][1];
                $grch_radicado_id=$resultado_registros_historico[0][2];
                $grch_tipo='Borrador';
                $grch_tipologia=$resultado_registros_historico[0][4];
                $grch_clasificacion=$resultado_registros_historico[0][5];
                $grch_gestion='';
                $grch_gestion_detalle='';
                $grch_duplicado='';
                $grch_unificado='';
                $grch_unificado_id='';
                $grch_dividido='';
                $grch_dividido_cantidad='';
                $grch_observaciones='';
                $grch_correo_id='';
                $grch_correo_de='5';
                $grch_correo_de_nombre='5';
                $grch_correo_para='';
                $grch_correo_para_nombre='';
                $grch_correo_cc=$resultado_registros_historico[0][17];
                $grch_correo_bcc='';
                $grch_correo_fecha=$resultado_registros_historico[0][19];
                $grch_correo_asunto=$resultado_registros_historico[0][20];
                $grch_correo_contenido=$resultado_registros_historico[0][21];
                $grch_embeddedimage_ruta="".IMAGES_ROOT."logo_cliente_notificacion_2.png;".IMAGES_ROOT."logo_certificacion_notificacion_2.png";
                $grch_embeddedimage_nombre="logo_cliente_notificacion;logo_certificacion_notificacion";
                $grch_embeddedimage_tipo="image/png;image/png";
                $grch_attachment_ruta='';
                $grch_intentos='';
                $grch_estado_envio='';
                $grch_fecha_envio='';
                $grch_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

                if ($consulta_registro_historial_insert->execute()) {
                    // Obtén el ID generado
                    $id_insertado_historial = $enlace_db->insert_id;

                    $consulta_string_adjuntos="SELECT `grca_id`, `grca_radicado`, `grca_radicado_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_registro_usuario`, `grca_registro_fecha` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_historial_id`=? ORDER BY `grca_id` ASC";
                    $consulta_registros_adjuntos = $enlace_db->prepare($consulta_string_adjuntos);
                    $consulta_registros_adjuntos->bind_param("s", $id_registro);
                    $consulta_registros_adjuntos->execute();
                    $resultado_registros_adjuntos = $consulta_registros_adjuntos->get_result()->fetch_all(MYSQLI_NUM);


                    // Prepara la sentencia
                    $correo_adjunto_insert = $enlace_db->prepare("INSERT INTO `gestion_radicacion_casos_adjuntos`(`grca_radicado`, `grca_radicado_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?)");

                    // Agrega variables a sentencia preparada
                    $correo_adjunto_insert->bind_param('sssssssss', $grca_radicado, $grca_radicado_id, $grca_historial_id, $grca_nombre, $grca_ruta, $grca_extension, $grca_tipo, $grca_estado, $grca_registro_usuario);

                    $control_adjuntos=0;
                    for ($i=0; $i < count($resultado_registros_adjuntos); $i++) { 
                        $grca_radicado=$resultado_registros_adjuntos[$i][1];
                        $grca_radicado_id=$resultado_registros_adjuntos[$i][2];
                        $grca_historial_id=$id_insertado_historial;
                        $grca_nombre=$resultado_registros_adjuntos[$i][4];
                        $grca_ruta=$resultado_registros_adjuntos[$i][5];
                        $grca_extension=$resultado_registros_adjuntos[$i][6];
                        $grca_tipo='Original';
                        $grca_estado='Activo';
                        $grca_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
                        
                        if ($correo_adjunto_insert->execute()) {
                            $control_adjuntos++;
                        }
                    }

                    if ($control_adjuntos==count($resultado_registros_adjuntos)) {
                        $resultado_data.='Adjunto '.$nombreArchivo.' cargado con éxito.';
                        $resultado_control=1;
                    } else {
                        $resultado_data.='Error al cargar el adjunto '.$nombreArchivo.'.';
                        $resultado_control=0;
                    }
                } else {
                    $resultado_data.='Error al cargar el adjunto '.$nombreArchivo.'.';
                    $resultado_control=0;
                }
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='historico' AND $tipo=='borrador') {
        $id_registro=validar_input($_POST['id_registro']);
        $estado=validar_input($_POST['estado']);
        $url_salir=$_POST['url_salir'];
        
        if ($id_registro!='') {
            $resultado_data='';

            $consulta_string_historico="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha`, RT.`ncr_setfrom`, `grch_correo_de_nombre`, `grch_correo_para_nombre` FROM `gestion_radicacion_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_radicacion_casos_historial`.`grch_correo_de`=RT.`ncr_id` WHERE `grch_radicado_id`=? AND `grch_tipo`='Borrador'";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);
            
            $resultado_data='';
            if (count($resultado_registros_historico)>0) {
                $resultado_control=1;
                $resultado_radicado=$resultado_registros_historico[0][7];
                $resultado_radicado_id=$resultado_registros_historico[0][2];
                $resultado_historial_id=$resultado_registros_historico[0][0];
                $resultado_data.='<div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <div class="row">
                            <div class="col-10">
                                <p class="font-size-12">
                                    <a class="btn btn-success py-1 px-1 font-size-11" onClick="mostrarOcultar('."'".$resultado_registros_historico[0][0]."'".');"><span class="fas fa-plus"></span></a> <span class="fas fa-user"></span> De: '.$resultado_registros_historico[0][31].'
                                    <span id="estado_borrador"><span class="alert alert-warning px-1 py-0 my-0 font-size-11"><span class="fas fa-user-cog me-1"></span>Borrador</span></span>
                                </p>
                                <p class="font-size-11 fw-bold" id="destinos_correo_borrador"></p>
                                <p class="font-size-12 fw-bold" id="asunto_correo_borrador">'.$resultado_registros_historico[0][20].'</p>
                                <div class="col-md-12" id="mensajes_gestion"></div>
                            </div>
                            <div class="col-2 text-end">
                                <a class="btn btn-danger py-1 px-1" id="btn_eliminar" onclick="eliminar_borrador('."'".$resultado_registros_historico[0][0]."'".');"><span class="fas fa-trash-alt"></span> Eliminar borrador</a>
                                <p class="font-size-12 my-0 py-0 px-1"><span class="fas fa-calendar-alt"></span> '.date('d/m/Y', strtotime($resultado_registros_historico[0][19])).'</p>
                                <p class="font-size-12 my-0 py-0 px-1"><span class="fas fa-clock"></span> '.date('H:i', strtotime($resultado_registros_historico[0][19])).'</p>
                            </div>
                            
                          </div>
                          <div class="row d-block" id="historico_correo_contenido_'.$resultado_registros_historico[0][0].'">
                            <div class="col-md-12">
                                <div class="row">
                                  <div class="col-md-4" id="grc_tipologia_div">
                                      <div class="form-group my-2">
                                          <label for="grc_tipologia" class="my-0">Tipología</label>
                                          <select class="form-control form-control-sm form-select" name="grc_tipologia" id="grc_tipologia" required onchange="validar_tipologia();">
                                              <option class="font-size-11" value="">Seleccione</option>';

                                              if ($estado=='En trámite') {
                                                  $resultado_data.='<option value="Funcionarios" '; if($resultado_registros_historico[0][4]=="Funcionarios"){ $resultado_data.='selected'; } $resultado_data.='>Funcionarios</option>';
                                              } else {
                                                $resultado_data.='<option value="Ciudadanos" '; if($resultado_registros_historico[0][4]=="Ciudadanos"){ $resultado_data.='selected'; } $resultado_data.='>Ciudadanos</option>
                                                  <option value="Envío Radicado a Ciudadano" '; if($resultado_registros_historico[0][4]=="Envío Radicado a Ciudadano"){ $resultado_data.='selected'; } $resultado_data.='>Envío Radicado a Ciudadano</option>
                                                  <option value="Funcionarios" '; if($resultado_registros_historico[0][4]=="Funcionarios"){ $resultado_data.='selected'; } $resultado_data.='>Funcionarios</option>
                                                  <option value="Notificaciones de correo" '; if($resultado_registros_historico[0][4]=="Notificaciones de correo"){ $resultado_data.='selected'; } $resultado_data.='>Notificaciones de correo</option>
                                                  <option value="Prioritario" '; if($resultado_registros_historico[0][4]=="Prioritario"){ $resultado_data.='selected'; } $resultado_data.='>Prioritario</option>
                                                  <option value="Soy Transparente" '; if($resultado_registros_historico[0][4]=="Soy Transparente"){ $resultado_data.='selected'; } $resultado_data.='>Soy Transparente</option>
                                                  <option value="Tutelas" '; if($resultado_registros_historico[0][4]=="Tutelas"){ $resultado_data.='selected'; } $resultado_data.='>Tutelas</option>';
                                              }
                                              
                                              
                            $resultado_data.='</select>
                                      </div>
                                  </div>
                                  <div class="col-md-4" id="grc_gestion_div">
                                      <div class="form-group my-2">
                                          <label for="grc_gestion" class="my-0">Gestión</label>
                                          <select class="form-control form-control-sm form-select" name="grc_gestion" id="grc_gestion" required onchange="validar_gestion('."'".$resultado_registros_historico[0][1]."'".', '."'".$resultado_registros_historico[0][2]."'".', '."'".$resultado_registros_historico[0][0]."'".');">
                                              <option class="font-size-11" value="">Seleccione</option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-md-4 d-none" id="grc_gestion_detalle_motivo_div">
                                      <div class="form-group my-2">
                                          <label for="grc_gestion_detalle_motivo" class="my-0">Motivo</label>
                                          <select class="form-control form-control-sm form-select" name="grc_gestion_detalle_motivo" id="grc_gestion_detalle_motivo" disabled required>
                                              <option class="font-size-11" value="">Seleccione</option>
                                              <option value="COPIA" '; if($resultado_registros_historico[0][6]=="COPIA"){ $resultado_data.='selected'; } $resultado_data.='>COPIA</option>
                                              <option value="NO SE DA TRÁMITE COMO DP" '; if($resultado_registros_historico[0][6]=="NO SE DA TRÁMITE COMO DP"){ $resultado_data.='selected'; } $resultado_data.='>NO SE DA TRÁMITE COMO DP</option>
                                              <option value="SPAM" '; if($resultado_registros_historico[0][6]=="SPAM"){ $resultado_data.='selected'; } $resultado_data.='>SPAM</option>
                                              <option value="UNIFICACIÓN" '; if($resultado_registros_historico[0][6]=="UNIFICACIÓN"){ $resultado_data.='selected'; } $resultado_data.='>UNIFICACIÓN</option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-md-4 d-none" id="grc_gestion_detalle_plantilla_div">
                                      <div class="form-group my-2">
                                          <label for="grc_gestion_detalle_plantilla" class="my-0">Plantilla</label>
                                          <select class="form-control form-control-sm form-select" name="grc_gestion_detalle_plantilla" id="grc_gestion_detalle_plantilla" disabled required onchange="validar_plantilla(this.value);">
                                              <option class="font-size-11" value="">Seleccione</option>
                                          </select>
                                      </div>
                                  </div>
                                </div>
                            </div>
                            <hr class="my-1">
                            <div class="col-md-12 my-1 d-none" id="grc_gestion_asunto_div">
                                <div class="form-group m-0">
                                  <div class="input-group">
                                    <label for="grc_gestion_asunto" class="me-1 pt-1 fw-bold">Asunto:</label>
                                    <input type="text" class="form-control form-control-sm" name="grc_gestion_asunto" id="grc_gestion_asunto" value="'.$resultado_registros_historico[0][20].'" required disabled>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-12 my-1 d-none" id="grc_gestion_para_div">
                                <div class="form-group m-0">
                                  <div class="input-group">
                                    <label for="grc_gestion_para" class="me-1 pt-1 fw-bold">Para:</label>
                                    <input type="text" class="form-control form-control-sm" name="grc_gestion_para" id="grc_gestion_para" value="" required disabled>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-12 my-1 d-none" id="grc_gestion_cc_div">
                                <div class="form-group m-0">
                                  <div class="input-group">
                                    <label for="grc_gestion_cc" class="me-1 pt-1 fw-bold">CC:</label>
                                    <input type="text" class="form-control form-control-sm" name="grc_gestion_cc" id="grc_gestion_cc" value="" required disabled>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-12 my-1 d-none" id="grc_gestion_cco_div">
                                <div class="form-group m-0">
                                  <div class="input-group">
                                    <label for="grc_gestion_cco" class="me-1 pt-1 fw-bold">CCO:</label>
                                    <input type="text" class="form-control form-control-sm" name="grc_gestion_cco" id="grc_gestion_cco" value="" required disabled>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-12 font-size-12 mb-2">
                              <div class="custom-file-upload" id="adjunto_div">
                                <input type="file" id="adjunto" name="adjunto[]" multiple onchange="adjuntos_cargar(this.files, '."'".$resultado_registros_historico[0][1]."'".', '."'".$resultado_registros_historico[0][2]."'".', '."'".$resultado_registros_historico[0][0]."'".')">
                                <label for="adjuntos"><span class="fas fa-paperclip"></span> Adjuntos (<span id="adjuntos_conteo"></span>)</label>
                              </div>
                              <span id="adjuntos_lista"></span>
                            </div>
                            <div class="col-md-12">
                              <textarea class="form-control form-control-sm d-none" name="asunto_correo" id="asunto_correo">'.$resultado_registros_historico[0][20].'</textarea>
                              <textarea class="form-control form-control-sm d-none" name="contenido_correo" id="contenido_correo">'.removeEmojis(nl2br(quitarCaracteresDeCorreoElectronico($resultado_registros_historico[0][21]))).'</textarea>
                              <textarea class="form-control form-control-sm d-none" name="plantilla_text" id="plantilla_text"></textarea>
                              <textarea class="form-control form-control-sm" name="grc_gestion_contenido" id="grc_gestion_contenido">'.removeEmojis(nl2br(quitarCaracteresDeCorreoElectronico($resultado_registros_historico[0][21]))).'</textarea>
                            </div>
                            <div class="col-md-12 mt-2 text-end">
                                <a class="btn btn-success py-3 px-3" id="btn_enviar" onclick="enviar_borrador('."'".$resultado_registros_historico[0][0]."'".');"><span class="fas fa-paper-plane me-2"></span> Enviar gestión</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>';
            } else {
                $resultado_control=0;
                $resultado_data='';
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='historico' AND $tipo=='enviar') {
        $id_registro=validar_input($_POST['id_registro']);
        $grch_tipologia=validar_input($_POST['grch_tipologia']);
        $grch_gestion=validar_input($_POST['grch_gestion']);
        $grc_gestion_detalle_motivo=validar_input($_POST['grc_gestion_detalle_motivo']);
        $grc_gestion_detalle_plantilla=validar_input($_POST['grc_gestion_detalle_plantilla']);

        $grch_correo_asunto=$_POST['grch_correo_asunto'];
        $grch_correo_contenido=$_POST['grch_correo_contenido'];

        $grc_gestion_para=validar_input($_POST['grc_gestion_para']);
        $grc_gestion_cc=validar_input($_POST['grc_gestion_cc']);
        $grc_gestion_cco=validar_input($_POST['grc_gestion_cco']);


        if ($id_registro!='') {
            $resultado_data='';

            $consulta_string_historico="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha`, TRAD.`grc_correo_remitente` FROM `gestion_radicacion_casos_historial` LEFT JOIN `gestion_radicacion_casos` AS TRAD ON `gestion_radicacion_casos_historial`.`grch_radicado_id`=TRAD.`grc_id` WHERE `grch_id`=? AND `grch_tipo`='Borrador'";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);
              
            if (count($resultado_registros_historico)>0) {
                $resultado_radicado=$resultado_registros_historico[0][1];
                $resultado_radicado_id=$resultado_registros_historico[0][2];
                $resultado_historial_id=$resultado_registros_historico[0][0];

                $grch_correo_cc='';
                $grch_correo_bcc='';
                
                if ($grch_gestion=='Radicación DELTA' OR $grch_gestion=='Radicación DELTA Soy Transparente') {
                    $grc_estado='Finalizado';
                    if ($grch_tipologia=='Funcionarios') {
                        $grc_estado='En trámite';
                    }
                    $grch_correo_para='delta@prosperidadsocial.gov.co';//debe cambiarse por correo delta
                    $grch_estado_envio='Pendiente';
                    $grc_gestion_detalle='';
                } elseif ($grch_gestion=='Archivar') {
                    $grc_estado='Finalizado';
                    $grch_correo_para='No aplica notificación';//No aplica notificación
                    $grch_estado_envio='Enviado';
                    $grc_gestion_detalle=$grc_gestion_detalle_motivo;
                } elseif ($grch_gestion=='Respuesta') {
                    $grc_estado='Finalizado';
                    
                    if ($grch_tipologia=='Soy Transparente' OR $grch_tipologia=='Envío Radicado a Ciudadano' OR $grch_tipologia=='Funcionarios') {
                        $grch_correo_para='';
                    } else {
                        $grch_correo_para=$resultado_registros_historico[0][31];//debe cambiarse por correo de solictante
                        $grch_correo_cc=$resultado_registros_historico[0][17];
                    }

                    $grch_estado_envio='Pendiente';
                    $grc_gestion_detalle=$grc_gestion_detalle_plantilla;
                } elseif ($grch_gestion=='Respuesta Radicado DELTA') {
                    $grc_estado='Finalizado';
                    $grch_correo_para=$resultado_registros_historico[0][31];//debe cambiarse por correo de solictante
                    $grch_correo_cc=$resultado_registros_historico[0][17];//debe cambiarse por correo de solictante
                    $grch_estado_envio='Pendiente';
                    $grc_gestion_detalle=$grc_gestion_detalle_plantilla;
                } elseif ($grch_gestion=='Correspondencia') {
                    $grc_estado='Finalizado';
                    $grch_correo_para='correspondencia@prosperidadsocial.gov.co';//debe cambiarse por correo de correspondencia
                    $grch_estado_envio='Pendiente';
                    $grc_gestion_detalle='';
                } elseif ($grch_gestion=='Notificaciones Jurídicas') {
                    $grc_estado='Finalizado';
                    $grch_correo_para='notificaciones.juridica@prosperidadsocial.gov.co';//debe cambiarse por correo de jurídica
                    $grch_correo_cc=$resultado_registros_historico[0][31];
                    $grch_estado_envio='Pendiente';
                    $grc_gestion_detalle='';
                }

                if ($grc_gestion_para!='') {
                    $grch_correo_para.=';'.$grc_gestion_para;
                }

                if ($grc_gestion_cc!='') {
                    $grch_correo_cc.=';'.$grc_gestion_cc;
                }

                if ($grc_gestion_cco!='') {
                    $grch_correo_bcc.=';'.$grc_gestion_cco;
                }

                $para_validar=explode(';', $grch_correo_para);

                if (!isset($para_validar[0])) {
                    $para_validar=array();
                }

                $valida_para=true;

                if (count($para_validar)==0) {
                    $valida_para=false;
                }
                    
                if ($valida_para) {
                    // Prepara la sentencia
                    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_radicacion_casos_historial` SET `grch_tipo`=?, `grch_tipologia`=?, `grch_gestion`=?, `grch_gestion_detalle`=?, `grch_correo_para`=?, `grch_correo_cc`=?, `grch_correo_bcc`=?, `grch_correo_asunto`=?, `grch_correo_contenido`=?, `grch_estado_envio`=? WHERE `grch_id`=?");

                    // Agrega variables a sentencia preparada
                    $consulta_actualizar->bind_param('sssssssssss', $grch_tipo, $grch_tipologia, $grch_gestion, $grch_gestion_detalle, $grch_correo_para, $grch_correo_cc, $grch_correo_bcc, $grch_correo_asunto, $grch_correo_contenido, $grch_estado_envio, $id_registro);

                    $grch_tipo='Gestión'; 
                    $grch_tipologia=$grch_tipologia;
                    $grch_gestion=$grch_gestion;
                    $grch_gestion_detalle=$grc_gestion_detalle;
                    $grch_correo_asunto=$grch_correo_asunto;
                    $grch_correo_contenido=$grch_correo_contenido;
                    
                    // Ejecuta sentencia preparada
                    $consulta_actualizar->execute();

                    if (comprobarSentencia($enlace_db->info)) {
                        // Prepara la sentencia actualiza radicado
                        $consulta_actualizar_radicado = $enlace_db->prepare("UPDATE `gestion_radicacion_casos` SET `grc_tipologia`=?, `grc_gestion`=?, `grc_gestion_detalle`=?, `grc_estado`=?, `grc_fecha_gestion`=? WHERE `grc_id`=?");

                        // Agrega variables a sentencia preparada
                        $consulta_actualizar_radicado->bind_param('ssssss', $grc_tipologia, $grc_gestion, $grc_gestion_detalle, $grc_estado, $grc_fecha_gestion, $id_registro_radicado);

                        $grc_tipologia=$grch_tipologia;
                        $grc_gestion=$grch_gestion;
                        $grc_gestion_detalle=$grc_gestion_detalle;
                        $grc_fecha_gestion=date('Y-m-d H:i:s');
                        $id_registro_radicado=$resultado_radicado_id;

                        // Ejecuta sentencia preparada
                        $consulta_actualizar_radicado->execute();
                        if (comprobarSentencia($enlace_db->info)) {
                            $resultado_data.='Gestión enviada con éxito.';
                            $resultado_control=1;
                        } else {
                            $resultado_data.='Error al enviar la gestión';
                            $resultado_control=0;
                        }
                    } else {
                        $resultado_data.='Error al enviar la gestión';
                        $resultado_control=0;
                    }
                } else {
                    $resultado_data.='Error al enviar la gestión - No se encontró destinatario diligenciado';
                    $resultado_control=0;
                }
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='historico' AND $tipo=='eliminar') {
        $id_registro=validar_input($_POST['id_registro']);

        if ($id_registro!='') {
            $resultado_data='';

            $consulta_string_historico="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha` FROM `gestion_radicacion_casos_historial` WHERE `grch_id`=? AND `grch_tipo`='Borrador'";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);
              
            if (count($resultado_registros_historico)>0) {
                $resultado_radicado=$resultado_registros_historico[0][1];
                $resultado_radicado_id=$resultado_registros_historico[0][2];
                $resultado_historial_id=$resultado_registros_historico[0][0];

                $consulta_string_adjuntos="SELECT `grca_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_radicado`, `grca_radicado_id` FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_historial_id`=? ORDER BY `grca_id` ASC";
                $consulta_registros_adjuntos = $enlace_db->prepare($consulta_string_adjuntos);
                $consulta_registros_adjuntos->bind_param("s", $id_registro);
                $consulta_registros_adjuntos->execute();
                $resultado_registros_adjuntos = $consulta_registros_adjuntos->get_result()->fetch_all(MYSQLI_NUM);

                // Prepara la sentencia
                $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_radicacion_casos_historial` WHERE `grch_id`=?");

                // Agrega variables a sentencia preparada
                $sentencia_delete->bind_param('s', $id_registro);

                // Prepara la sentencia
                $sentencia_delete_adjuntos = $enlace_db->prepare("DELETE FROM `gestion_radicacion_casos_adjuntos` WHERE `grca_historial_id`=?");

                // Agrega variables a sentencia preparada
                $sentencia_delete_adjuntos->bind_param('s', $id_registro);

                for ($i=0; $i < count($resultado_registros_adjuntos); $i++) {
                    if ($resultado_registros_adjuntos[$i][5]=='Adjunto') {
                        unlink($resultado_registros_adjuntos[$i][3]);
                    }
                }

                if ($sentencia_delete->execute() AND $sentencia_delete_adjuntos->execute()) {
                    $resultado_data.='Gestión enviada con éxito.';
                    $resultado_control=1;
                } else {
                    $resultado_data.='Error al enviar la gestión';
                    $resultado_control=0;
                }
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='historico' AND $tipo=='desagregar') {
        $id_registro=validar_input($_POST['id_registro']);
        $grc_dividido_cantidad=validar_input($_POST['grc_dividido_cantidad']);
        
        if ($id_registro!='' AND $grc_dividido_cantidad>0) {
            $consulta_string_historico="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha` FROM `gestion_radicacion_casos_historial` WHERE `grch_id`=? AND `grch_tipo`='Radicado'";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);

            if (count($resultado_registros_historico)>0) {
                $consulta_string="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`, `grc_registro_fecha_hora`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo` FROM `gestion_radicacion_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos`.`grc_responsable`=TAG.`usu_id` WHERE `grc_id`=?";

                $consulta_registros = $enlace_db->prepare($consulta_string);
                $consulta_registros->bind_param("s", $resultado_registros_historico[0][2]);
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                if (count($resultado_registros)>0) {
                    $resultado_data='';

                    // Prepara la sentencia
                    $consulta_registro_insert = $enlace_db->prepare("INSERT INTO `gestion_radicacion_casos`(`grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                    // Agrega variables a sentencia preparada
                    $consulta_registro_insert->bind_param('sssssssssssssssss', $grc_radicado, $grc_tipologia, $grc_clasificacion, $grc_responsable, $grc_gestion, $grc_gestion_detalle, $grc_estado, $grc_duplicado, $grc_unificado, $grc_unificado_id, $grc_dividido, $grc_dividido_cantidad, $grc_fecha_gestion, $grc_correo_remitente, $grc_correo_asunto, $grc_correo_fecha, $grc_registro_fecha);

                    // Prepara la sentencia
                    $consulta_registro_historial_insert = $enlace_db->prepare("INSERT INTO `gestion_radicacion_casos_historial`(`grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                    // Agrega variables a sentencia preparada
                    $consulta_registro_historial_insert->bind_param('sssssssssssssssssssssssssssss', $grch_radicado, $grch_radicado_id, $grch_tipo, $grch_tipologia, $grch_clasificacion, $grch_gestion, $grch_gestion_detalle, $grch_duplicado, $grch_unificado, $grch_unificado_id, $grch_dividido, $grch_dividido_cantidad, $grch_observaciones, $grch_correo_id, $grch_correo_de, $grch_correo_para, $grch_correo_cc, $grch_correo_bcc, $grch_correo_fecha, $grch_correo_asunto, $grch_correo_contenido, $grch_embeddedimage_ruta, $grch_embeddedimage_nombre, $grch_embeddedimage_tipo, $grch_attachment_ruta, $grch_intentos, $grch_estado_envio, $grch_fecha_envio, $grch_registro_usuario);

                    for ($i=0; $i < $grc_dividido_cantidad; $i++) { 
                        
                        $data_consulta_consecutivo=array();
                        $filtro_consecutivo='GR'.date('Y');
                        array_push($data_consulta_consecutivo, "%$filtro_consecutivo%");

                        $consulta_string_consecutivo="SELECT MAX(`grc_radicado`) FROM `gestion_radicacion_casos` WHERE `grc_radicado` LIKE ?";
                        $consulta_registros_consecutivo = $enlace_db->prepare($consulta_string_consecutivo);
                        $consulta_registros_consecutivo->bind_param(str_repeat("s", count($data_consulta_consecutivo)), ...$data_consulta_consecutivo);
                        $consulta_registros_consecutivo->execute();
                        $resultado_registros_consecutivo = $consulta_registros_consecutivo->get_result()->fetch_all(MYSQLI_NUM);

                        $ultimo_consecutivo=explode($filtro_consecutivo, $resultado_registros_consecutivo[0][0]);
                        $nuevo_consecutivo=intval($ultimo_consecutivo[1])+1;
                        $inser_consecutivo="GR".date('Y').str_pad($nuevo_consecutivo, 7, 0, STR_PAD_LEFT);

                        $grc_radicado=$inser_consecutivo;
                        $grc_tipologia=$resultado_registros[0][2];
                        $grc_clasificacion=$resultado_registros[0][3];
                        $grc_responsable=$resultado_registros[0][4];
                        $grc_gestion=$resultado_registros[0][5];
                        $grc_gestion_detalle=$resultado_registros[0][6];
                        $grc_estado=$resultado_registros[0][7];
                        $grc_duplicado='Si';
                        $grc_unificado=$resultado_registros[0][9];
                        $grc_unificado_id=$resultado_registros[0][10];
                        $grc_dividido=$resultado_registros[0][1];
                        $grc_dividido_cantidad=$grc_dividido_cantidad;
                        $grc_fecha_gestion=$resultado_registros[0][13];
                        $grc_correo_remitente=$resultado_registros[0][14];
                        $grc_correo_asunto=$resultado_registros[0][15];
                        $grc_correo_fecha=$resultado_registros[0][16];
                        $grc_registro_fecha=$resultado_registros[0][17];

                        if ($consulta_registro_insert->execute()) {
                            // Obtén el ID generado
                            $id_insertado = $enlace_db->insert_id;

                            $grch_radicado=$grc_radicado;
                            $grch_radicado_id=$id_insertado;
                            $grch_tipo='Radicado';
                            $grch_tipologia=$resultado_registros_historico[0][4];
                            $grch_clasificacion=$resultado_registros_historico[0][5];
                            $grch_gestion='';
                            $grch_gestion_detalle='';
                            $grch_duplicado='Si';
                            $grch_unificado='';
                            $grch_unificado_id='';
                            $grch_dividido=$grc_dividido;
                            $grch_dividido_cantidad=$grc_dividido_cantidad;
                            $grch_observaciones='';
                            $grch_correo_id=$resultado_registros_historico[0][14];
                            $grch_correo_de=$resultado_registros_historico[0][15];
                            $grch_correo_para=$resultado_registros_historico[0][16];
                            $grch_correo_cc=$resultado_registros_historico[0][17];
                            $grch_correo_bcc=$resultado_registros_historico[0][18];
                            $grch_correo_fecha=$resultado_registros_historico[0][19];
                            $grch_correo_asunto=$resultado_registros_historico[0][20];
                            $grch_correo_contenido=$resultado_registros_historico[0][21];
                            $grch_embeddedimage_ruta='';
                            $grch_embeddedimage_nombre='';
                            $grch_embeddedimage_tipo='';
                            $grch_attachment_ruta='';
                            $grch_intentos='';
                            $grch_estado_envio='';
                            $grch_fecha_envio='';
                            $grch_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

                            if ($consulta_registro_historial_insert->execute()) {
                                $resultado_data.='Adjunto '.$nombreArchivo.' cargado con éxito.';
                                $resultado_control=1;
                            } else {
                                $resultado_data.='Error al cargar el adjunto '.$nombreArchivo.'.';
                                $resultado_control=0;
                            }
                        } else {
                            $resultado_data.='Error al cargar el adjunto '.$nombreArchivo.'.';
                            $resultado_control=0;
                        }
                    }
                }
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='historico' AND $tipo=='desagregar_lista') {
        $id_registro=validar_input($_POST['id_registro']);
        $id_registro_actual=validar_input($_POST['id_registro_actual']);
        $url_editar=$_POST['url_editar'];
        
        $array_estado_alert['Pendiente']='warning';
        $array_estado_alert['En trámite']='dark';
        $array_estado_alert['Finalizado']='success';

        if ($id_registro!='' AND $id_registro_actual!='') {
            $consulta_string="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`, `grc_registro_fecha_hora`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo` FROM `gestion_radicacion_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos`.`grc_responsable`=TAG.`usu_id` WHERE `grc_duplicado`='Si' AND `grc_dividido`=? AND `grc_id`<>?";

            $consulta_registros = $enlace_db->prepare($consulta_string);
            $consulta_registros->bind_param("ss", $id_registro, $id_registro_actual);
            $consulta_registros->execute();
            $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
            
            // $consulta_string_valida="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`, `grc_registro_fecha_hora`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo` FROM `gestion_radicacion_casos` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_radicacion_casos`.`grc_responsable`=TAG.`usu_id` WHERE `grc_duplicado`='Si' AND `grc_dividido`='".$id_registro."' AND `grc_id`<>'".$id_registro_actual."'";

            $resultado_data='';
            if (count($resultado_registros)>0) {
                $resultado_data='<div class="col-md-12">
                        <p class="alert background-principal color-blanco py-1 px-2 my-0 font-size-11"><span class="fas fa-clone"></span> Casos desagregados</p>
                      </div>';
                $resultado_control=1;


                $consulta_string_original="SELECT `grc_id`, `grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`, `grc_registro_fecha_hora` FROM `gestion_radicacion_casos` WHERE `grc_radicado`=? AND `grc_id`<>?";

                $consulta_registros_original = $enlace_db->prepare($consulta_string_original);
                $consulta_registros_original->bind_param("ss", $resultado_registros[0][11], $id_registro_actual);
                $consulta_registros_original->execute();
                $resultado_registros_original = $consulta_registros_original->get_result()->fetch_all(MYSQLI_NUM);

                if (count($resultado_registros_original)>0) {
                    $resultado_data.='<div class="col-md-12 alert border px-2 py-2 my-1 font-size-11">
                                <p class="my-0 px-0 py-1 font-size-11"><span class="alert alert-dark px-1 py-0 my-0 font-size-11"><span class="fas fa-envelope me-1"></span>Radicado</span><br><b>De: </b>'.$resultado_registros_original[0][14].'<a href="'.$url_editar.'&reg='.base64_encode($resultado_registros_original[0][0]).'" class="btn btn-warning btn-icon px-1 py-1 float-end" title="Editar"><i class="fas fa-pen font-size-11"></i></a></p>
                                <span><b>Asunto: </b>'.$resultado_registros_original[0][15].'</span>
                                <div class="my-1"></div>
                                <span class="alert alert-'.$array_estado_alert[$resultado_registros_original[0][7]].' px-1 py-0 me-1"><span class="fas fa-qrcode me-1"></span>'.$resultado_registros_original[0][1].'</span>
                                <span class="alert alert-'.$array_estado_alert[$resultado_registros_original[0][7]].' px-1 py-0 me-1">'.$resultado_registros_original[0][7].'</span>
                                <span class="alert alert-'.$array_estado_alert[$resultado_registros_original[0][7]].' px-1 py-0 me-1"><span class="fas fa-calendar-alt me-1"></span>'.date('d/m/Y H:i:s', strtotime($resultado_registros_original[0][16])).'</span>
                                <div class="my-2"></div>
                                <span class="alert alert-'.$array_estado_alert[$resultado_registros_original[0][7]].' px-1 py-0 me-1"><span class="fas fa-sitemap me-1"></span>'.$resultado_registros_original[0][2].'</span>';
                                if($resultado_registros_original[0][2]=='Ciudadanos') {
                                    $resultado_data.='<span class="alert alert-'.$array_estado_alert[$resultado_registros_original[0][7]].' px-1 py-0 me-1"><span class="fas fa-timeline me-1"></span>'.$resultado_registros_original[0][3].'</span>';
                                }
                                if($resultado_registros_original[0][5]!='') {
                                    $resultado_data.='<span class="alert alert-'.$array_estado_alert[$resultado_registros_original[0][7]].' px-1 py-0 me-1"><span class="fas fa-user-cog me-1"></span>'.$resultado_registros_original[0][5].'</span>';
                                }
                                $resultado_data.='<span class="alert alert-'.$array_estado_alert[$resultado_registros_original[0][7]].' px-1 py-0 me-1"><span class="fas fa-user-tie me-1"></span>'.$resultado_registros_original[0][19].'</span>
                              </div>';
                }

                
                for ($i=0; $i < count($resultado_registros); $i++) { 
                    $resultado_data.='<div class="col-md-12 alert border px-2 py-2 my-1 font-size-11">
                            <p class="my-0 px-0 py-1 font-size-11"><b>De: </b>'.$resultado_registros[$i][14].'<a href="'.$url_editar.'&reg='.base64_encode($resultado_registros[$i][0]).'" class="btn btn-warning btn-icon px-1 py-1 float-end" title="Editar"><i class="fas fa-pen font-size-11"></i></a></p>
                            <span><b>Asunto: </b>'.$resultado_registros[$i][15].'</span>
                            <div class="my-1"></div>
                            <span class="alert alert-'.$array_estado_alert[$resultado_registros[$i][7]].' px-1 py-0 me-1"><span class="fas fa-qrcode me-1"></span>'.$resultado_registros[$i][1].'</span>
                            <span class="alert alert-'.$array_estado_alert[$resultado_registros[$i][7]].' px-1 py-0 me-1">'.$resultado_registros[$i][7].'</span>
                            <span class="alert alert-'.$array_estado_alert[$resultado_registros[$i][7]].' px-1 py-0 me-1"><span class="fas fa-calendar-alt me-1"></span>'.date('d/m/Y H:i:s', strtotime($resultado_registros[$i][16])).'</span>
                            <div class="my-2"></div>
                            <span class="alert alert-'.$array_estado_alert[$resultado_registros[$i][7]].' px-1 py-0 me-1"><span class="fas fa-sitemap me-1"></span>'.$resultado_registros[$i][2].'</span>';
                            if($resultado_registros[$i][2]=='Ciudadanos') {
                                $resultado_data.='<span class="alert alert-'.$array_estado_alert[$resultado_registros[$i][7]].' px-1 py-0 me-1"><span class="fas fa-timeline me-1"></span>'.$resultado_registros[$i][3].'</span>';
                            }
                            if($resultado_registros[$i][5]!='') {
                                $resultado_data.='<span class="alert alert-'.$array_estado_alert[$resultado_registros[$i][7]].' px-1 py-0 me-1"><span class="fas fa-user-cog me-1"></span>'.$resultado_registros[$i][5].'</span>';
                            }
                            $resultado_data.='<span class="alert alert-'.$array_estado_alert[$resultado_registros[$i][7]].' px-1 py-0 me-1"><span class="fas fa-user-tie me-1"></span>'.$resultado_registros[$i][19].'</span>
                          </div>';
                    
                }
            } else {
                $resultado_control=0;
                $resultado_data='';
            }
        } else {
            $resultado_control=0;
        }

    }

    if ($accion=='historico' AND $tipo=='enviar_destinos') {
        $id_registro=validar_input($_POST['id_registro']);
        $grch_tipologia=validar_input($_POST['grch_tipologia']);
        $grch_gestion=validar_input($_POST['grch_gestion']);

        $grc_gestion_para=validar_input($_POST['grc_gestion_para']);
        $grc_gestion_cc=validar_input($_POST['grc_gestion_cc']);
        $grc_gestion_cco=validar_input($_POST['grc_gestion_cco']);


        if ($id_registro!='') {
            $resultado_data='';

            $consulta_string_historico="SELECT `grch_id`, `grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_para`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`, `grch_registro_fecha`, TRAD.`grc_correo_remitente` FROM `gestion_radicacion_casos_historial` LEFT JOIN `gestion_radicacion_casos` AS TRAD ON `gestion_radicacion_casos_historial`.`grch_radicado_id`=TRAD.`grc_id` WHERE `grch_id`=? AND `grch_tipo`='Borrador'";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);
              
            if (count($resultado_registros_historico)>0) {
                $resultado_control=1;

                $grch_correo_cc='';
                $grch_correo_cco='';
                if ($grch_gestion=='Radicación DELTA' OR $grch_gestion=='Radicación DELTA Soy Transparente') {
                    
                    $grch_correo_para='delta@prosperidadsocial.gov.co';//debe cambiarse por correo delta
                } elseif ($grch_gestion=='Archivar') {
                    $grch_correo_para='No aplica notificación';//No aplica notificación
                } elseif ($grch_gestion=='Respuesta') {
                    if ($grch_tipologia=='Soy Transparente' OR $grch_tipologia=='Envío Radicado a Ciudadano' OR $grch_tipologia=='Funcionarios') {
                        $grch_correo_para='';
                    } else {
                        $grch_correo_para=$resultado_registros_historico[0][31];//debe cambiarse por correo de solictante
                        $grch_correo_cc=$resultado_registros_historico[0][17];
                    }
                        
                } elseif ($grch_gestion=='Respuesta Radicado DELTA') {
                    $grch_correo_para=$resultado_registros_historico[0][31];//debe cambiarse por correo de solictante
                    $grch_correo_cc=$resultado_registros_historico[0][17];//debe cambiarse por correo de solictante
                } elseif ($grch_gestion=='Correspondencia') {
                    $grch_correo_para='correspondencia@prosperidadsocial.gov.co';//debe cambiarse por correo de correspondencia
                } elseif ($grch_gestion=='Notificaciones Jurídicas') {
                    $grch_correo_para='notificaciones.juridica@prosperidadsocial.gov.co';//debe cambiarse por correo de jurídica
                    $grch_correo_cc=$resultado_registros_historico[0][31];
                }

                if ($grc_gestion_para!='') {
                    $grch_correo_para.=';'.$grc_gestion_para;
                }

                if ($grc_gestion_cc!='') {
                    $grch_correo_cc.=';'.$grc_gestion_cc;
                }

                if ($grc_gestion_cco!='') {
                    $grch_correo_cco.=';'.$grc_gestion_cco;
                }

                $resultado_data='<div><span class="fas fa-user"></span> Para: '.$grch_correo_para.'</div>';
                $resultado_data.='<div><span class="fas fa-users"></span> Cc: '.$grch_correo_cc.'</div>';
                // $resultado_data.='<div><span class="fas fa-user-tie"></span> Cco: '.$grch_correo_cco.'</div>';
            } else {
                $resultado_control=0;
            }
        } else {
            $resultado_control=0;
        }
    }

    $data = array(
        "resultado" => $resultado_data,
        "resultado_control" => $resultado_control,
        "resultado_radicado" => $resultado_radicado,
        "resultado_radicado_id" => $resultado_radicado_id,
        "resultado_historial_id" => $resultado_historial_id,
        "resultado_estado" => $grc_estado,

    );

    echo json_encode($data);
?>