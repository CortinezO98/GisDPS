<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Envíos WEB";
    require_once("../../iniciador.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Psr7\Request;

// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    /*DEFINICIÓN DE VARIABLES*/
    $accion=validar_input($_GET['accion']);
    $tipo=validar_input($_GET['tipo']);
    $resultado_radicado='';
    $resultado_radicado_id='';
    $resultado_historial_id='';

    if ($accion=='plantilla' AND $tipo!='') {
        $consulta_string="SELECT `gewcp_id`, `gewcp_nombre`, `gewcp_estado`, `gewcp_tipo`, `gewcp_contenido`, `gewcp_actualiza_usuario`, `gewcp_actualiza_fecha`, `gewcp_registro_usuario`, `gewcp_registro_fecha`, TUA.`usu_nombres_apellidos` FROM `gestion_enviosweb_casos_plantillas` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_enviosweb_casos_plantillas`.`gewcp_actualiza_usuario`=TUA.`usu_id` WHERE `gewcp_estado`='Activo' AND `gewcp_tipo`='Transversal' ORDER BY `gewcp_nombre` ASC";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        // $consulta_registros->bind_param("s", $tipo);
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
        $consulta_string="SELECT `gewcp_id`, `gewcp_nombre`, `gewcp_estado`, `gewcp_tipo`, `gewcp_contenido`, `gewcp_actualiza_usuario`, `gewcp_actualiza_fecha`, `gewcp_registro_usuario`, `gewcp_registro_fecha`, TUA.`usu_nombres_apellidos` FROM `gestion_enviosweb_casos_plantillas` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_enviosweb_casos_plantillas`.`gewcp_actualiza_usuario`=TUA.`usu_id` WHERE `gewcp_estado`='Activo' AND `gewcp_id`=?";
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

        $consulta_string="SELECT `gewca_id`, `gewca_historial_id`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_estado`, `gewca_radicado`, `gewca_radicado_id`, `gewca_peso` FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_historial_id`=? ORDER BY `gewca_id` ASC";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $historial_id);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $resultado_data='';
        if (count($resultado_registros)>0) {
            $resultado_control=count($resultado_registros);
            $control_separa=false;
            $total_peso_adjuntos=0;

            for ($i=0; $i < count($resultado_registros); $i++) { 
                if (!$control_separa AND $resultado_registros[$i][5]=='Adjunto') {
                    // $control_separa=true;
                    // $resultado_data.='<hr class="my-1">';
                }

                if ($resultado_registros[$i][5]=='Original' AND $resultado_registros[$i][6]=='Inactivo') {
                    $tipo_btn='btn-outline-danger';
                    $tachado='tachado';
                    $quita_agrega='<a class="dropdown-item adjuntos_borrador" onclick="adjuntos_agregar('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-check-circle"></span> Agregar adjunto</a>';
                } else {
                    $tipo_btn='btn-secondary';
                    $tachado='';
                    $quita_agrega='<a class="dropdown-item adjuntos_borrador" onclick="adjuntos_quitar('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-times-circle"></span> Quitar adjunto</a>';
                }

                if ($resultado_registros[$i][9]=='') {
                    $gewca_ruta_size='/var/www/html/templates/envios_web/'.$resultado_registros[$i][3];
                    if (file_exists($gewca_ruta_size)) {
                        $gewca_peso = filesize($gewca_ruta_size);
                    } else {
                        $gewca_peso=0;
                    }
                    $total_peso_adjuntos+=$gewca_peso;
                } else {
                    $gewca_peso=$resultado_registros[$i][9];
                    $total_peso_adjuntos+=$gewca_peso;
                }

                $resultado_data.='
                    <div class="btn-group mb-1 me-1" style="width: 300px;">
                      <button type="button" class="btn '.$tipo_btn.' '.$tachado.' px-1 py-2 text-start" onclick="adjuntos_previa('."'".base64_encode($resultado_registros[$i][0])."'".')" title="'.$resultado_registros[$i][2].'" style="width: 290px;">'.validar_extension_icono($resultado_registros[$i][4]).' '.recortarTexto($resultado_registros[$i][2], 28).'<div class="font-size-10 text-start">'.convertirPeso($gewca_peso).'</div></button>
                      <button type="button" class="btn '.$tipo_btn.' px-2 py-2 dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                        <a class="dropdown-item" href="#" onclick="validar_dividida('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-clone"></span> Vista dividida</a>
                        <a class="dropdown-item" href="#" onclick="adjuntos_previa('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-eye"></span> Vista previa</a>
                        <a class="dropdown-item" href="correo_editar_adjunto_descargar.php?id_registro='.base64_encode($resultado_registros[$i][0]).'" target="_blank"><span class="fas fa-download"></span> Descargar</a>
                        '.$quita_agrega.'
                      </div>
                    </div>
                ';
            }

            if ($total_peso_adjuntos>18000000) {
                $resultado_data.='<p class="alert alert-danger p-1 font-size-11">¡El tamaño total de los adjuntos ('.convertirPeso($total_peso_adjuntos).') supera el máximo permitido, por favor valide antes de continuar!</p>';
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($accion=='adjuntos' AND $tipo=='lista_ver') {
        $historial_id=validar_input($_POST['historial_id']);

        $consulta_string="SELECT `gewca_id`, `gewca_historial_id`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_estado`, `gewca_radicado`, `gewca_radicado_id` FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_historial_id`=? ORDER BY `gewca_id` ASC";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $historial_id);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $resultado_data='';
        if (count($resultado_registros)>0) {
            $resultado_control=count($resultado_registros);
            $control_separa=false;
            $total_peso_adjuntos=0;

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

                if ($resultado_registros[$i][9]=='') {
                    $gewca_ruta_size='/var/www/html/templates/envios_web/'.$resultado_registros[$i][3];
                    if (file_exists($gewca_ruta_size)) {
                        $gewca_peso = filesize($gewca_ruta_size);
                    } else {
                        $gewca_peso=0;
                    }
                    $total_peso_adjuntos+=$gewca_peso;
                } else {
                    $gewca_peso=$resultado_registros[$i][9];
                    $total_peso_adjuntos+=$gewca_peso;
                }

                $resultado_data.='
                    <div class="btn-group mb-1 me-1" style="width: 300px;">
                      <button type="button" class="btn '.$tipo_btn.' '.$tachado.' px-1 py-2 text-start" onclick="adjuntos_previa('."'".base64_encode($resultado_registros[$i][0])."'".')" title="'.$resultado_registros[$i][2].'" style="width: 290px;">'.validar_extension_icono($resultado_registros[$i][4]).' '.recortarTexto($resultado_registros[$i][2], 28).'<div class="font-size-10 text-start">'.convertirPeso($gewca_peso).'</div></button>
                      <button type="button" class="btn '.$tipo_btn.' px-2 py-2 dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                        <a class="dropdown-item" href="#" onclick="validar_dividida('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-clone"></span> Vista dividida</a>
                        <a class="dropdown-item" href="#" onclick="adjuntos_previa('."'".base64_encode($resultado_registros[$i][0])."'".')"><span class="fas fa-eye"></span> Vista previa</a>
                        <a class="dropdown-item" href="correo_editar_adjunto_descargar.php?id_registro='.base64_encode($resultado_registros[$i][0]).'" target="_blank"><span class="fas fa-download"></span> Descargar</a>
                        '.$quita_agrega.'
                      </div>
                    </div>
                ';
            }

            if ($total_peso_adjuntos>18000000) {
                $resultado_data.='<p class="alert alert-danger p-1 font-size-11">¡El tamaño total de los adjuntos ('.convertirPeso($total_peso_adjuntos).') supera el máximo permitido, por favor valide antes de continuar!</p>';
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

            // Prepara la sentencia
            $resultado_insert_adjunto = $enlace_db->prepare("INSERT INTO `gestion_enviosweb_casos_adjuntos`(`gewca_radicado`, `gewca_radicado_id`, `gewca_historial_id`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_peso`, `gewca_estado`, `gewca_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?)");
            // Agrega variables a sentencia preparada
            $resultado_insert_adjunto->bind_param('ssssssssss', $radicado, $radicado_id, $historial_id, $nombreArchivo, $rutaDestino, $extension, $grca_tipo, $gewca_peso, $grca_estado, $grca_registro_usuario);

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
                    
                    $gewca_ruta_size='/var/www/html/templates/envios_web/storage/'.$rutaDestino;
                    if (file_exists($gewca_ruta_size)) {
                        $gewca_peso = filesize($gewca_ruta_size);
                    } else {
                        $gewca_peso='';
                    }

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

            $consulta_string_validar_pre="SELECT `gewca_id`, `gewca_radicado`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_estado`, `gewca_radicado_id`, `gewca_historial_id` FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_id`=?";
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
                $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_enviosweb_casos_adjuntos` SET `gewca_estado`=? WHERE `gewca_id`=?");

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
                $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_id`=?");

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
        $id_documento=validar_input(base64_decode($_POST['id_documento']));

        if ($id_documento!='') {
            $resultado_data='';

            $consulta_string_validar_pre="SELECT `gewca_id`, `gewca_radicado`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_estado`, `gewca_radicado_id`, `gewca_historial_id` FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_id`=?";
            $consulta_registros_validar_pre = $enlace_db->prepare($consulta_string_validar_pre);
            $consulta_registros_validar_pre->bind_param("s", $id_documento);
            $consulta_registros_validar_pre->execute();
            $resultado_registros_validar_pre = $consulta_registros_validar_pre->get_result()->fetch_all(MYSQLI_NUM);
            
            $resultado_radicado=$resultado_registros_validar_pre[0][1];
            $resultado_radicado_id=$resultado_registros_validar_pre[0][7];
            $resultado_historial_id=$resultado_registros_validar_pre[0][8];

            if ($resultado_registros_validar_pre[0][5]=='Original' AND $resultado_registros_validar_pre[0][6]=='Inactivo') {
                $grca_estado='Activo';
                
                // Prepara la sentencia
                $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_enviosweb_casos_adjuntos` SET `gewca_estado`=? WHERE `gewca_id`=?");

                // Agrega variables a sentencia preparada
                $consulta_actualizar->bind_param('ss', $grca_estado, $id_documento);

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

            $consulta_string_historico="SELECT `gewch_id`, `gewch_radicado`, `gewch_radicado_id`, `gewch_tipo`, `gewch_tipologia`, `gewch_gestion`, `gewch_gestion_detalle`, `gewch_anonimo`, `gewch_publicacion`, `gewch_correo_id`, `gewch_correo_de`, `gewch_correo_de_nombre`, `gewch_correo_para`, `gewch_correo_para_nombre`, `gewch_correo_cc`, `gewch_correo_bcc`, `gewch_correo_fecha`, `gewch_correo_asunto`, `gewch_correo_contenido`, `gewch_embeddedimage_ruta`, `gewch_embeddedimage_nombre`, `gewch_embeddedimage_tipo`, `gewch_attachment_ruta`, `gewch_intentos`, `gewch_estado_envio`, `gewch_fecha_envio`, `gewch_registro_usuario`, `gewch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom` FROM `gestion_enviosweb_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_enviosweb_casos_historial`.`gewch_correo_de`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos_historial`.`gewch_registro_usuario`=TAG.`usu_id` WHERE `gewch_id`=?";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);

            if ($resultado_registros_historico[0][3]=='Radicado') {
                // Prepara la sentencia
                $consulta_registro_historial_insert = $enlace_db->prepare("INSERT INTO `gestion_enviosweb_casos_historial`(`gewch_radicado`, `gewch_radicado_id`, `gewch_tipo`, `gewch_tipologia`, `gewch_gestion`, `gewch_gestion_detalle`, `gewch_anonimo`, `gewch_publicacion`, `gewch_correo_id`, `gewch_correo_de`, `gewch_correo_de_nombre`, `gewch_correo_para`, `gewch_correo_para_nombre`, `gewch_correo_cc`, `gewch_correo_bcc`, `gewch_correo_fecha`, `gewch_correo_asunto`, `gewch_correo_contenido`, `gewch_embeddedimage_ruta`, `gewch_embeddedimage_nombre`, `gewch_embeddedimage_tipo`, `gewch_attachment_ruta`, `gewch_intentos`, `gewch_estado_envio`, `gewch_fecha_envio`, `gewch_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                // Agrega variables a sentencia preparada
                $consulta_registro_historial_insert->bind_param('ssssssssssssssssssssssssss', $gewch_radicado, $gewch_radicado_id, $gewch_tipo, $gewch_tipologia, $gewch_gestion, $gewch_gestion_detalle, $gewch_anonimo, $gewch_publicacion, $gewch_correo_id, $gewch_correo_de, $gewch_correo_de_nombre, $gewch_correo_para, $gewch_correo_para_nombre, $gewch_correo_cc, $gewch_correo_bcc, $gewch_correo_fecha, $gewch_correo_asunto, $gewch_correo_contenido, $gewch_embeddedimage_ruta, $gewch_embeddedimage_nombre, $gewch_embeddedimage_tipo, $gewch_attachment_ruta, $gewch_intentos, $gewch_estado_envio, $gewch_fecha_envio, $gewch_registro_usuario);
                
                $gewch_radicado=$resultado_registros_historico[0][1];
                $gewch_radicado_id=$resultado_registros_historico[0][2];
                $gewch_tipo='Borrador';
                $gewch_tipologia=$resultado_registros_historico[0][4];
                $gewch_gestion='';
                $gewch_gestion_detalle='';
                $gewch_anonimo='';
                $gewch_publicacion='';
                $gewch_correo_id='';
                $gewch_correo_de='5';
                $gewch_correo_de_nombre='5';
                $gewch_correo_para='';
                $gewch_correo_para_nombre='';
                $gewch_correo_cc='';
                $gewch_correo_bcc='';
                $gewch_correo_fecha=$resultado_registros_historico[0][16];
                $gewch_correo_asunto=$resultado_registros_historico[0][17];
                $gewch_correo_contenido=$resultado_registros_historico[0][18];
                $gewch_embeddedimage_ruta="".IMAGES_ROOT."logo_cliente_notificacion.png;".IMAGES_ROOT."logo_certificacion_notificacion.png";
                $gewch_embeddedimage_nombre="logo_cliente_notificacion;logo_certificacion_notificacion";
                $gewch_embeddedimage_tipo="image/png;image/png";
                $gewch_attachment_ruta='';
                $gewch_intentos='';
                $gewch_estado_envio='';
                $gewch_fecha_envio='';
                $gewch_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];

                if ($consulta_registro_historial_insert->execute()) {
                    // Obtén el ID generado
                    $id_insertado_historial = $enlace_db->insert_id;

                    $consulta_string_adjuntos="SELECT `gewca_id`, `gewca_radicado`, `gewca_radicado_id`, `gewca_historial_id`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_estado`, `gewca_registro_usuario`, `gewca_registro_fecha`, `gewca_peso` FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_historial_id`=? ORDER BY `gewca_id` ASC";
                    $consulta_registros_adjuntos = $enlace_db->prepare($consulta_string_adjuntos);
                    $consulta_registros_adjuntos->bind_param("s", $id_registro);
                    $consulta_registros_adjuntos->execute();
                    $resultado_registros_adjuntos = $consulta_registros_adjuntos->get_result()->fetch_all(MYSQLI_NUM);


                    // Prepara la sentencia
                    $correo_adjunto_insert = $enlace_db->prepare("INSERT INTO `gestion_enviosweb_casos_adjuntos`(`gewca_radicado`, `gewca_radicado_id`, `gewca_historial_id`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_peso`, `gewca_estado`, `gewca_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?)");

                    // Agrega variables a sentencia preparada
                    $correo_adjunto_insert->bind_param('ssssssssss', $gewca_radicado, $gewca_radicado_id, $gewca_historial_id, $gewca_nombre, $gewca_ruta, $gewca_extension, $gewca_tipo, $gewca_peso, $gewca_estado, $gewca_registro_usuario);

                    $control_adjuntos=0;
                    for ($i=0; $i < count($resultado_registros_adjuntos); $i++) { 
                        $gewca_radicado=$resultado_registros_adjuntos[$i][1];
                        $gewca_radicado_id=$resultado_registros_adjuntos[$i][2];
                        $gewca_historial_id=$id_insertado_historial;
                        $gewca_nombre=$resultado_registros_adjuntos[$i][4];
                        $gewca_ruta=$resultado_registros_adjuntos[$i][5];
                        $gewca_extension=$resultado_registros_adjuntos[$i][6];
                        $gewca_tipo='Original';
                        $gewca_peso=$resultado_registros_adjuntos[$i][11];
                        $gewca_estado='Activo';
                        $gewca_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
                        
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

            $consulta_string_historico="SELECT `gewch_id`, `gewch_radicado`, `gewch_radicado_id`, `gewch_tipo`, `gewch_gestion`, `gewch_gestion_detalle`, `gewch_anonimo`, `gewch_publicacion`, `gewch_correo_id`, `gewch_correo_de`, `gewch_correo_de_nombre`, `gewch_correo_para`, `gewch_correo_para_nombre`, `gewch_correo_cc`, `gewch_correo_bcc`, `gewch_correo_fecha`, `gewch_correo_asunto`, `gewch_correo_contenido`, `gewch_embeddedimage_ruta`, `gewch_embeddedimage_nombre`, `gewch_embeddedimage_tipo`, `gewch_attachment_ruta`, `gewch_intentos`, `gewch_estado_envio`, `gewch_fecha_envio`, `gewch_registro_usuario`, `gewch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom`, `gewch_tipologia`, TCA.`gewc_clasificacion`, TCA.`gewc_radicado_salida` FROM `gestion_enviosweb_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_enviosweb_casos_historial`.`gewch_correo_de`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos_historial`.`gewch_registro_usuario`=TAG.`usu_id` LEFT JOIN `gestion_enviosweb_casos` AS TCA ON `gestion_enviosweb_casos_historial`.`gewch_radicado_id`=TCA.`gewc_id` WHERE `gewch_radicado_id`=? AND `gewch_tipo`='Borrador'";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);
            
            $resultado_data='';
            if (count($resultado_registros_historico)>0) {
                $resultado_control=1;
                $resultado_radicado=$resultado_registros_historico[0][1];
                $resultado_radicado_id=$resultado_registros_historico[0][2];
                $resultado_historial_id=$resultado_registros_historico[0][0];
                $resultado_data.='<div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <div class="row">
                            <div class="col-10">
                                <p class="font-size-12">
                                    <a class="btn btn-success py-1 px-1 font-size-11" onClick="mostrarOcultar('."'".$resultado_registros_historico[0][0]."'".');"><span class="fas fa-plus"></span></a> <span class="fas fa-user"></span> De: '.$resultado_registros_historico[0][29].'
                                    <span id="estado_borrador"><span class="alert alert-warning px-1 py-0 my-0 font-size-11"><span class="fas fa-user-cog me-1"></span>Borrador</span></span>
                                    <br><span class="fas fa-envelope"></span> Para: <span id="destinatario_borrador">'.$resultado_registros_historico[0][11].'</span>
                                </p>
                                <p class="font-size-12 fw-bold" id="asunto_correo_borrador">'.$resultado_registros_historico[0][16].'</p>
                                <div class="col-md-12" id="mensajes_gestion"></div>
                            </div>
                            <div class="col-2 text-end">
                                <a class="btn btn-danger py-1 px-1" id="btn_eliminar" onclick="eliminar_borrador('."'".$resultado_registros_historico[0][0]."'".');"><span class="fas fa-trash-alt"></span> Eliminar borrador</a>
                                <p class="font-size-12 my-0 py-0 px-1"><span class="fas fa-calendar-alt"></span> '.date('d/m/Y', strtotime($resultado_registros_historico[0][15])).'</p>
                                <p class="font-size-12 my-0 py-0 px-1"><span class="fas fa-clock"></span> '.date('H:i', strtotime($resultado_registros_historico[0][15])).'</p>
                            </div>
                            
                          </div>
                          <div class="row d-block" id="historico_correo_contenido_'.$resultado_registros_historico[0][0].'">
                            <div class="col-md-12">
                                <div class="row">
                                  <div class="col-md-3" id="grc_tipologia_div">
                                      <div class="form-group my-2">
                                          <label for="grc_tipologia" class="my-0">Tipología</label>
                                          <select class="form-control form-control-sm form-select" name="grc_tipologia" id="grc_tipologia" required onchange="validar_tipologia();">
                                              <option class="font-size-11" value="">Seleccione</option>';

                                            $resultado_data.='<option value="Reparto" '; if($resultado_registros_historico[0][30]=="Reparto"){ $resultado_data.='selected'; } $resultado_data.='>Reparto</option>
                                              <option value="Subsidio Familiar de Vivienda en especie" '; if($resultado_registros_historico[0][30]=="Subsidio Familiar de Vivienda en especie"){ $resultado_data.='selected'; } $resultado_data.='>Subsidio Familiar de Vivienda en especie</option>
                                              <option value="Ingreso Solidario" '; if($resultado_registros_historico[0][30]=="Ingreso Solidario"){ $resultado_data.='selected'; } $resultado_data.='>Ingreso Solidario</option>
                                              <option value="Colombia Mayor" '; if($resultado_registros_historico[0][30]=="Colombia Mayor"){ $resultado_data.='selected'; } $resultado_data.='>Colombia Mayor</option>
                                              <option value="Compensación del IVA" '; if($resultado_registros_historico[0][30]=="Compensación del IVA"){ $resultado_data.='selected'; } $resultado_data.='>Compensación del IVA</option>
                                              <option value="Antifraudes" '; if($resultado_registros_historico[0][30]=="Antifraudes"){ $resultado_data.='selected'; } $resultado_data.='>Antifraudes</option>
                                              <option value="Jóvenes en Acción" '; if($resultado_registros_historico[0][30]=="Jóvenes en Acción"){ $resultado_data.='selected'; } $resultado_data.='>Jóvenes en Acción</option>
                                              <option value="Tránsito a Renta Ciudadana" '; if($resultado_registros_historico[0][30]=="Tránsito a Renta Ciudadana"){ $resultado_data.='selected'; } $resultado_data.='>Tránsito a Renta Ciudadana</option>
                                              <option value="Otros programas" '; if($resultado_registros_historico[0][30]=="Otros programas"){ $resultado_data.='selected'; } $resultado_data.='>Otros programas</option>
                                            </select>
                                      </div>
                                  </div>';
                                
                                if($resultado_registros_historico[0][31]=='Sin radicado salida')  {
                                    $resultado_data.='<div class="col-md-3">
                                            <div class="form-group my-2">
                                                <label for="gewc_radicado_salida" class="my-0">Radicado</label>
                                                <input type="text" class="form-control form-control-sm" name="gewc_radicado_salida" id="gewc_radicado_salida" value="'.$resultado_registros_historico[0][32].'" required>
                                            </div>
                                        </div>';
                                }
                                              
                            $resultado_data.='
                                  <div class="col-md-3" id="grc_gestion_div">
                                      <div class="form-group my-2">
                                          <label for="grc_gestion" class="my-0">Gestión</label>
                                          <select class="form-control form-control-sm form-select" name="grc_gestion" id="grc_gestion" required onchange="validar_gestion('."'".$resultado_registros_historico[0][1]."'".', '."'".$resultado_registros_historico[0][2]."'".', '."'".$resultado_registros_historico[0][0]."'".');">
                                              <option class="font-size-11" value="">Seleccione</option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-md-3 d-none" id="grc_gestion_detalle_motivo_div">
                                      <div class="form-group my-2">
                                          <label for="grc_gestion_detalle_motivo" class="my-0">Motivo</label>
                                          <select class="form-control form-control-sm form-select" name="grc_gestion_detalle_motivo" id="grc_gestion_detalle_motivo" disabled required>
                                              <option class="font-size-11" value="">Seleccione</option>
                                              <option value="DUPLICADO" '; if($resultado_registros_historico[0][5]=="DUPLICADO"){ $resultado_data.='selected'; } $resultado_data.='>DUPLICADO</option>
                                              <option value="CORRECCIÓN DE INSUMO" '; if($resultado_registros_historico[0][5]=="CORRECCIÓN DE INSUMO"){ $resultado_data.='selected'; } $resultado_data.='>CORRECCIÓN DE INSUMO</option>
                                              <option value="DETENCIÓN DE ENVÍO" '; if($resultado_registros_historico[0][5]=="DETENCIÓN DE ENVÍO"){ $resultado_data.='selected'; } $resultado_data.='>DETENCIÓN DE ENVÍO</option>
                                              <option value="RECHAZO" '; if($resultado_registros_historico[0][5]=="RECHAZO"){ $resultado_data.='selected'; } $resultado_data.='>RECHAZO</option>
                                              <option value="OTROS" '; if($resultado_registros_historico[0][5]=="OTROS"){ $resultado_data.='selected'; } $resultado_data.='>OTROS</option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-md-3 d-none" id="grc_gestion_detalle_plantilla_div">
                                      <div class="form-group my-2">
                                          <label for="grc_gestion_detalle_plantilla" class="my-0">Plantilla</label>
                                          <select class="form-control form-control-sm form-select" name="grc_gestion_detalle_plantilla" id="grc_gestion_detalle_plantilla" disabled required onchange="validar_plantilla(this.value);">
                                              <option class="font-size-11" value="">Seleccione</option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-md-12">
                                      <div class="form-group m-0 d-inline-block">
                                          <div class="form-group custom-control custom-checkbox m-0">
                                              <input type="checkbox" class="custom-control-input" id="gewch_anonimo" name="gewch_anonimo" value="Si">
                                              <label class="custom-control-label p-0 m-0" for="gewch_anonimo">Anónimo</label>
                                          </div>
                                      </div>
                                      <div class="form-group m-0 d-inline-block">
                                          <div class="form-group custom-control custom-checkbox m-0">
                                              <input type="checkbox" class="custom-control-input" id="gewch_publicacion" name="gewch_publicacion" value="Si">
                                              <label class="custom-control-label p-0 m-0" for="gewch_publicacion">Publicación</label>
                                          </div>
                                      </div>
                                  </div>
                                </div>
                            </div>
                            <hr class="my-1">
                            <div class="col-md-12 my-1" id="grc_gestion_asunto_div">
                                <div class="form-group m-0">
                                  <div class="input-group">
                                    <label for="grc_gestion_asunto" class="me-1 pt-1 fw-bold" style="width: 50px;">Asunto:</label>
                                    <input type="text" class="form-control form-control-sm" name="grc_gestion_asunto" id="grc_gestion_asunto" value="'.$resultado_registros_historico[0][16].'" required>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-12 my-1" id="grc_gestion_para_div">
                                <hr class="my-1">
                                <div class="form-group m-0">
                                  <div class="input-group">
                                    <label for="grc_gestion_para_input" class="me-1 pt-1 fw-bold" style="width: 50px;">Para:</label>
                                    <input type="text" class="form-control form-control-sm" style="height: 15px;" name="grc_gestion_para_input" id="grc_gestion_para_input" value="" onkeydown="checkEnter(event, '."'grc_gestion_para'".')">
                                  </div>
                                </div>
                                <div class="px-1 py-0" name="grc_gestion_para_view" id="grc_gestion_para_view"></div>
                                <input type="text" class="form-control form-control-sm d-none" name="grc_gestion_para" id="grc_gestion_para" value="" readonly>
                            </div>
                            <div class="col-md-12 my-1" id="grc_gestion_cc_div">
                                <hr class="my-1">
                                <div class="form-group m-0">
                                  <div class="input-group">
                                    <label for="grc_gestion_cc_input" class="me-1 pt-1 fw-bold" style="width: 50px;">CC:</label>
                                    <input type="text" class="form-control form-control-sm" style="height: 15px;" name="grc_gestion_cc_input" id="grc_gestion_cc_input" value="" onkeydown="checkEnter(event, '."'grc_gestion_cc'".')">
                                  </div>
                                </div>
                                <div class="px-1 py-0" name="grc_gestion_cc_view" id="grc_gestion_cc_view"></div>
                                <input type="text" class="form-control form-control-sm d-none" name="grc_gestion_cc" id="grc_gestion_cc" value="" readonly>
                            </div>
                            <div class="col-md-12 my-1" id="grc_gestion_cco_div">
                                <hr class="my-1">
                                <div class="form-group m-0">
                                  <div class="input-group">
                                    <label for="grc_gestion_cco_input" class="me-1 pt-1 fw-bold" style="width: 50px;">CCO:</label>
                                    <input type="text" class="form-control form-control-sm" style="height: 15px;" name="grc_gestion_cco_input" id="grc_gestion_cco_input" value="" onkeydown="checkEnter(event, '."'grc_gestion_cco'".')">
                                  </div>
                                </div>
                                <div class="px-1 py-0" name="grc_gestion_cco_view" id="grc_gestion_cco_view"></div>
                                <input type="text" class="form-control form-control-sm d-none" name="grc_gestion_cco" id="grc_gestion_cco" value="" readonly>
                            </div>
                            <hr class="my-1">
                            <div class="col-md-12 font-size-12 mb-2">
                              <div class="custom-file-upload" id="adjunto_div">
                                <input type="file" id="adjunto" name="adjunto[]" multiple onchange="adjuntos_cargar(this.files, '."'".$resultado_registros_historico[0][1]."'".', '."'".$resultado_registros_historico[0][2]."'".', '."'".$resultado_registros_historico[0][0]."'".')">
                                <label for="adjuntos"><span class="fas fa-paperclip"></span> Adjuntos (<span id="adjuntos_conteo"></span>)</label>
                              </div>
                              <span id="adjuntos_lista"></span>
                            </div>
                            <div class="col-md-12">
                              <textarea class="form-control form-control-sm d-none" name="asunto_correo" id="asunto_correo">'.$resultado_registros_historico[0][16].'</textarea>
                              <textarea class="form-control form-control-sm d-none" name="contenido_correo" id="contenido_correo">'.removeEmojis(nl2br($resultado_registros_historico[0][17])).'</textarea>
                              <textarea class="form-control form-control-sm d-none" name="plantilla_text" id="plantilla_text"></textarea>
                              <textarea class="form-control form-control-sm" name="grc_gestion_contenido" id="grc_gestion_contenido">'.removeEmojis(nl2br($resultado_registros_historico[0][17])).'</textarea>
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
        // error_reporting(E_ALL);
        // ini_set('display_errors', '1');
        $id_registro=validar_input($_POST['id_registro']);
        $gewch_tipologia=validar_input($_POST['grch_tipologia']);
        $gewc_radicado_salida=validar_input($_POST['gewc_radicado_salida']);

        $gewch_gestion=validar_input($_POST['grch_gestion']);
        $gewch_gestion_detalle=validar_input($_POST['grch_gestion_detalle']);
        $gewch_correo_asunto=$_POST['grch_correo_asunto'];
        $gewch_correo_contenido=$_POST['grch_correo_contenido'];

        $gewc_gestion_para=validar_input($_POST['grc_gestion_para']);
        $gewc_gestion_cc=validar_input($_POST['grc_gestion_cc']);
        $gewc_gestion_cco=validar_input($_POST['grc_gestion_cco']);

        $gewch_anonimo=validar_input($_POST['gewch_anonimo']);
        $gewch_publicacion=validar_input($_POST['gewch_publicacion']);

        if ($id_registro!='') {
            $resultado_data='';

            $consulta_string_historico="SELECT `gewch_id`, `gewch_radicado`, `gewch_radicado_id`, `gewch_tipo`, `gewch_gestion`, `gewch_gestion_detalle`, `gewch_anonimo`, `gewch_publicacion`, `gewch_correo_id`, `gewch_correo_de`, `gewch_correo_de_nombre`, `gewch_correo_para`, `gewch_correo_para_nombre`, `gewch_correo_cc`, `gewch_correo_bcc`, `gewch_correo_fecha`, `gewch_correo_asunto`, `gewch_correo_contenido`, `gewch_embeddedimage_ruta`, `gewch_embeddedimage_nombre`, `gewch_embeddedimage_tipo`, `gewch_attachment_ruta`, `gewch_intentos`, `gewch_estado_envio`, `gewch_fecha_envio`, `gewch_registro_usuario`, `gewch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom`, `gewch_tipologia` FROM `gestion_enviosweb_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_enviosweb_casos_historial`.`gewch_correo_de`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos_historial`.`gewch_registro_usuario`=TAG.`usu_id` WHERE `gewch_id`=? AND `gewch_tipo`='Borrador'";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);
              
            if (count($resultado_registros_historico)>0) {
                $resultado_radicado=$resultado_registros_historico[0][1];
                $resultado_radicado_id=$resultado_registros_historico[0][2];
                $resultado_historial_id=$resultado_registros_historico[0][0];

                $valida_para=true;

                if ($gewch_gestion=='Archivar') {
                    $gewc_estado='Finalizado';
                    $gewch_correo_para='';//No aplica notificación
                    $gewch_correo_cc='';
                    $gewch_correo_bcc='';
                    $gewch_estado_envio='Enviado';
                } elseif ($gewch_gestion=='Respuesta') {
                    $gewc_estado='Finalizado';
                    $gewch_correo_para=$gewc_gestion_para;
                    $gewch_correo_cc=$gewc_gestion_cc;
                    $gewch_correo_bcc=$gewc_gestion_cco;
                    $gewch_estado_envio='Pendiente';

                    $para_validar=explode(';', $gewch_correo_para);

                    if (!isset($para_validar[0])) {
                        $para_validar=array();
                    }

                    if (count($para_validar)==0) {
                        $valida_para=false;
                    }
                }

                if ($valida_para) {
                    // Prepara la sentencia
                    $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_enviosweb_casos_historial` SET `gewch_tipo`=?, `gewch_tipologia`=?, `gewch_gestion`=?, `gewch_gestion_detalle`=?, `gewch_anonimo`=?, `gewch_publicacion`=?, `gewch_correo_para`=?, `gewch_correo_cc`=?, `gewch_correo_bcc`=?, `gewch_correo_asunto`=?, `gewch_correo_contenido`=?, `gewch_estado_envio`=? WHERE `gewch_id`=?");

                    // Agrega variables a sentencia preparada
                    $consulta_actualizar->bind_param('sssssssssssss', $gewch_tipo, $gewch_tipologia, $gewch_gestion, $gewch_gestion_detalle, $gewch_anonimo, $gewch_publicacion, $gewch_correo_para, $gewch_correo_cc, $gewch_correo_bcc, $gewch_correo_asunto, $gewch_correo_contenido, $gewch_estado_envio, $id_registro);

                    $gewch_tipo='Gestión';
                    $gewch_tipologia=$gewch_tipologia;
                    $gewch_gestion=$gewch_gestion;
                    $gewch_gestion_detalle=$gewch_gestion_detalle;
                    $gewch_anonimo=$gewch_anonimo;
                    $gewch_publicacion=$gewch_publicacion;
                    $gewch_correo_para=$gewc_gestion_para;
                    $gewch_correo_cc=$gewc_gestion_cc;
                    $gewch_correo_bcc=$gewc_gestion_cco;

                    $gewch_correo_asunto=$gewch_correo_asunto;
                    $gewch_correo_contenido=$gewch_correo_contenido;
                    
                    // Ejecuta sentencia preparada
                    $consulta_actualizar->execute();

                    if (comprobarSentencia($enlace_db->info)) {
                        

                        // Prepara la sentencia actualiza radicado
                        $consulta_actualizar_radicado = $enlace_db->prepare("UPDATE `gestion_enviosweb_casos` SET `gewc_tipologia`=?, `gewc_gestion`=?, `gewc_gestion_detalle`=?, `gewc_estado`=?, `gewc_fecha_gestion`=? WHERE `gewc_id`=?");

                        // Agrega variables a sentencia preparada
                        $consulta_actualizar_radicado->bind_param('ssssss', $gewc_tipologia, $gewc_gestion, $gewc_gestion_detalle, $gewc_estado, $gewc_fecha_gestion, $id_registro_radicado);

                        $gewc_tipologia=$gewch_tipologia;
                        $gewc_gestion=$gewch_gestion;
                        $gewc_gestion_detalle=$gewch_gestion_detalle;
                        $gewc_fecha_gestion=date('Y-m-d H:i:s');
                        $id_registro_radicado=$resultado_radicado_id;

                        if ($gewc_radicado_salida!='') {
                            // Prepara la sentencia actualiza radicado
                            $consulta_actualizar_radicado_salida = $enlace_db->prepare("UPDATE `gestion_enviosweb_casos` SET `gewc_radicado_salida`=? WHERE `gewc_id`=?");

                            // Agrega variables a sentencia preparada
                            $consulta_actualizar_radicado_salida->bind_param('ss', $gewc_radicado_salida, $id_registro_radicado);
                            // Ejecuta sentencia preparada
                            $consulta_actualizar_radicado_salida->execute();
                        }

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
                    $resultado_data.='Error al enviar la gestión - No se encontró un destinatario diligenciado';
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

            $consulta_string_historico="SELECT `gewch_id`, `gewch_radicado`, `gewch_radicado_id`, `gewch_tipo`, `gewch_gestion`, `gewch_gestion_detalle`, `gewch_anonimo`, `gewch_publicacion`, `gewch_correo_id`, `gewch_correo_de`, `gewch_correo_de_nombre`, `gewch_correo_para`, `gewch_correo_para_nombre`, `gewch_correo_cc`, `gewch_correo_bcc`, `gewch_correo_fecha`, `gewch_correo_asunto`, `gewch_correo_contenido`, `gewch_embeddedimage_ruta`, `gewch_embeddedimage_nombre`, `gewch_embeddedimage_tipo`, `gewch_attachment_ruta`, `gewch_intentos`, `gewch_estado_envio`, `gewch_fecha_envio`, `gewch_registro_usuario`, `gewch_registro_fecha`, TAG.`usu_nombres_apellidos`, TAG.`usu_correo_corporativo`, RT.`ncr_setfrom` FROM `gestion_enviosweb_casos_historial` LEFT JOIN `administrador_buzones` AS RT ON `gestion_enviosweb_casos_historial`.`gewch_correo_de`=RT.`ncr_id` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_enviosweb_casos_historial`.`gewch_registro_usuario`=TAG.`usu_id` WHERE `gewch_id`=? AND `gewch_tipo`='Borrador'";

            $consulta_registros_historico = $enlace_db->prepare($consulta_string_historico);
            $consulta_registros_historico->bind_param("s", $id_registro);
            $consulta_registros_historico->execute();
            $resultado_registros_historico = $consulta_registros_historico->get_result()->fetch_all(MYSQLI_NUM);
              
            if (count($resultado_registros_historico)>0) {
                $resultado_radicado=$resultado_registros_historico[0][1];
                $resultado_radicado_id=$resultado_registros_historico[0][2];
                $resultado_historial_id=$resultado_registros_historico[0][0];

                $consulta_string_adjuntos="SELECT `gewca_id`, `gewca_historial_id`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_estado`, `gewca_radicado`, `gewca_radicado_id` FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_historial_id`=? ORDER BY `gewca_id` ASC";
                $consulta_registros_adjuntos = $enlace_db->prepare($consulta_string_adjuntos);
                $consulta_registros_adjuntos->bind_param("s", $id_registro);
                $consulta_registros_adjuntos->execute();
                $resultado_registros_adjuntos = $consulta_registros_adjuntos->get_result()->fetch_all(MYSQLI_NUM);

                // Prepara la sentencia
                $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_enviosweb_casos_historial` WHERE `gewch_id`=?");

                // Agrega variables a sentencia preparada
                $sentencia_delete->bind_param('s', $id_registro);

                // Prepara la sentencia
                $sentencia_delete_adjuntos = $enlace_db->prepare("DELETE FROM `gestion_enviosweb_casos_adjuntos` WHERE `gewca_historial_id`=?");

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

    if ($accion=='buscar' AND $tipo=='usuario') {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        require_once("../../app/functions/microsoft-graph-test.class.php");
        require_once("../administrador/modules/guzzle-master/vendor/autoload.php");
        $guzzle = new \GuzzleHttp\Client();
        $mail = new MicrosoftGraph();
        
        $consulta_string="SELECT `ncr_id`, `ncr_host`, `ncr_port`, `ncr_smtpsecure`, `ncr_smtpauth`, `ncr_username`, `ncr_password`, `ncr_setfrom`, `ncr_setfrom_name`, `ncr_tipo`, `ncr_tenant`, `ncr_client_id`, `ncr_client_secret`, `ncr_device_code`, `ncr_token`, `ncr_token_refresh`, `ncr_fecha_actualiza` FROM `administrador_buzones` WHERE `ncr_username`='geress@prosperidadsocial.gov.co'";

        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $ncr_tenant = $resultado_registros[0][10];
        $ncr_client_id = $resultado_registros[0][11];
        $ncr_client_secret = $resultado_registros[0][12];
        $ncr_device_code = $resultado_registros[0][13];
        $ncr_token = $resultado_registros[0][14];
        $ncr_token_refresh = $resultado_registros[0][15];

        $mail->tenant = $ncr_tenant;
        $mail->client_id = $ncr_client_id;
        $mail->client_secret = $ncr_client_secret;
        $mail->redirect_uri = 'https://dps.iq-online.net.co';
        $mail->auth_code=$ncr_device_code;
        $mail->token=$ncr_token;
        $mail->token_refresh=$ncr_token_refresh;

        $resultado_data = $mail->get_users($guzzle);

        // $resultado_data='Prueba';
        // $resultado_data.='<option class="font-size-11 py-0" value="" class="font-size-11">Seleccione</option>';
        // if (count($resultado_registros)>0) {
        //     $resultado_control=1;
        //     for ($i=0; $i < count($resultado_registros); $i++) { 
        //         $resultado_data.='<option class="font-size-11 py-0" value="'.$resultado_registros[$i][0].'" class="font-size-11">'.$resultado_registros[$i][1].'</option>';
        //     }
        // } else {
        //     $resultado_control=0;
        // }
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