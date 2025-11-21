<?php

	// require_once("/var/www/html/config/conexion_db.php");
	// require_once("/var/www/html/config/microsoft-graph.class.php");
	// require_once("/var/www/html/app/modules/guzzle-master/vendor/autoload.php");

	// require_once("/var/www/html/app/functions/microsoft-graph.class.php");
  	// $modulo_plataforma="Administrador";
  	// require_once("/var/www/html/iniciador.php");
  	// require_once("/var/www/html/templates/administrador/modules/guzzle-master/vendor/autoload.php");

  	require_once("/var/www/html/app/functions/microsoft-graph.class.php");
  	$modulo_plataforma="Administrador";
  	require_once("/var/www/html/iniciador.php");
  	require_once("/var/www/html/templates/administrador/modules/guzzle-master/vendor/autoload.php");
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	use GuzzleHttp\Client;
	use GuzzleHttp\Exception\RequestException;
	use GuzzleHttp\Psr7\Request;

	
	ini_set('date.timezone', 'America/Bogota');

	$guzzle = new \GuzzleHttp\Client();
	$mail = new MicrosoftGraph();

	$consulta_string_buzon="SELECT `ncr_id`, `ncr_host`, `ncr_port`, `ncr_smtpsecure`, `ncr_smtpauth`, `ncr_username`, `ncr_password`, `ncr_setfrom`, `ncr_setfrom_name`, `ncr_tenant`, `ncr_client_id`, `ncr_client_secret`, `ncr_device_code`, `ncr_token`, `ncr_token_refresh` FROM `administrador_buzones` WHERE `ncr_username`='servicioalciudadano1@prosperidadsocial.gov.co' AND `ncr_tipo`='Lectura'";
	$consulta_registros_buzon = $enlace_db->prepare($consulta_string_buzon);
	$consulta_registros_buzon->execute();
	$resultado_registros_buzon = $consulta_registros_buzon->get_result()->fetch_all(MYSQLI_NUM);

	$ncr_id = $resultado_registros_buzon[0][0];
	$ncr_tenant = $resultado_registros_buzon[0][9];
	$ncr_client_id = $resultado_registros_buzon[0][10];
	$ncr_client_secret = $resultado_registros_buzon[0][11];
	$ncr_device_code = $resultado_registros_buzon[0][12];
	$ncr_token = $resultado_registros_buzon[0][13];
	$ncr_token_refresh = $resultado_registros_buzon[0][14];

	$mail->tenant = $ncr_tenant;
	$mail->client_id = $ncr_client_id;
	$mail->client_secret = $ncr_client_secret;
	$mail->redirect_uri = 'https://dps.iq-online.net.co';
	$mail->auth_code=$ncr_device_code;
	$mail->token=$ncr_token;
	$mail->token_refresh=$ncr_token_refresh;

	$folders = $mail->get_folder_id($guzzle);

	for ($i=0; $i < count($folders['value']); $i++) { 
		$array_folders[$folders['value'][$i]['displayName']]['id']=$folders['value'][$i]['id'];
	}

	$folders_2 = $mail->get_folder_id_2($guzzle);

	for ($i=0; $i < count($folders_2['value']); $i++) { 
		$array_folders[$folders_2['value'][$i]['displayName']]['id']=$folders_2['value'][$i]['id'];
	}

	$folders_3 = $mail->get_folder_id_3($guzzle);

	for ($i=0; $i < count($folders_3['value']); $i++) { 
		$array_folders[$folders_3['value'][$i]['displayName']]['id']=$folders_3['value'][$i]['id'];
	}

	$mails_folder=$mail->get_mails_folder($guzzle, $array_folders['iqgis_pendiente']['id']);

	// if (!isset($mails_folder['value'])) {
	// 	$mails_folder['value']=array();
	// }

	// echo "<pre>";
	// print_r($mails_folder);
	// echo "</pre>"; 

	$limite_lectura=count($mails_folder['value']);
	
	if ($limite_lectura>0) {
		if ($limite_lectura>100) {
			$limite_lectura=100;
		}

		$data_consulta_analistas=array();
		$gewc_registro_fecha=date('Y-m-d');


		array_push($data_consulta_analistas, $gewc_registro_fecha);

		for ($i=0; $i < $limite_lectura; $i++) { 
	  		$mensaje_id = $mails_folder['value'][$i]['id'];
			$mensaje_fecha = $mails_folder['value'][$i]['receivedDateTime'];
			$mensaje_de = strtolower($mails_folder['value'][$i]['from']['emailAddress']['address']);
			$mensaje_de_nombre = strtolower($mails_folder['value'][$i]['from']['emailAddress']['name']);
			$mensaje_para='';
			$mensaje_para_nombre='';
			for ($j=0; $j < count($mails_folder['value'][$i]['toRecipients']); $j++) { 
				$mensaje_para .= $mails_folder['value'][$i]['toRecipients'][$j]['emailAddress']['address'].';';
				$mensaje_para_nombre .= $mails_folder['value'][$i]['toRecipients'][$j]['emailAddress']['name'].';';
				
			}
			

			$mensaje_cc = '';

			for ($j=0; $j < count($mails_folder['value'][$i]['ccRecipients']); $j++) { 
				$mensaje_cc.=$mails_folder['value'][$i]['ccRecipients'][$j]['emailAddress']['address'].';';
			}
			

			$mensaje_asunto = removeEmojis($mails_folder['value'][$i]['subject']);
			// $nombre_dia_final=$nombre_dias[date("l", strtotime($mensaje_fecha))];
			// $nombre_mes_final=$nombre_mes[date("F", strtotime($mensaje_fecha))];
		    // //Se configura y convierte el formato de fecha de enviado
		    // $fecha_enviado=trim($nombre_dia_final.", ".date("d", strtotime($mensaje_fecha))." de ".$nombre_mes_final." de ".date("Y H:i:s", strtotime($mensaje_fecha)));
		    $fecha_enviado_registro=date("Y-m-d H:i:s", strtotime($mensaje_fecha));

			//Obtenemos contenido de mensaje de los remitentes permitidos
			$mensaje_contenido=removeEmojis($mails_folder['value'][$i]['body']['content']);
			
			if ($mensaje_contenido=="") {
				$mensaje_contenido="No se encontró contenido en esta sección.";
			}

			if ($mensaje_asunto=="") {
				$mensaje_asunto="Sin asunto";
			}

			if ($mensaje_para=="") {
				$mensaje_para="Sin destinatario";
			}

			if ($mensaje_de!="" AND $mensaje_de!="@" AND $mensaje_asunto!="") {
				## INSERT RADICADO CASO
				// Prepara la sentencia
		        $consulta_registro_correo_ingreso_insert = $enlace_db->prepare("INSERT INTO `gestion_enviosweb_casos`(`gewc_radicado`, `gewc_radicado_entrada`, `gewc_radicado_salida`, `gewc_tipologia`, `gewc_clasificacion`, `gewc_responsable`, `gewc_gestion`, `gewc_gestion_detalle`, `gewc_estado`, `gewc_fecha_gestion`, `gewc_correo_remitente`, `gewc_correo_asunto`, `gewc_correo_fecha`, `gewc_registro_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

		        // Agrega variables a sentencia preparada
		        $consulta_registro_correo_ingreso_insert->bind_param('ssssssssssssss', $gewc_radicado, $gewc_radicado_entrada, $gewc_radicado_salida, $gewc_tipologia, $gewc_clasificacion, $gewc_responsable, $gewc_gestion, $gewc_gestion_detalle, $gewc_estado, $gewc_fecha_gestion, $gewc_correo_remitente, $gewc_correo_asunto, $gewc_correo_fecha, $gewc_registro_fecha);


		        ## INSERT HISTORIAL
		        // Prepara la sentencia
		        $consulta_registro_historial_insert = $enlace_db->prepare("INSERT INTO `gestion_enviosweb_casos_historial`(`gewch_radicado`, `gewch_radicado_id`, `gewch_tipo`, `gewch_tipologia`, `gewch_gestion`, `gewch_gestion_detalle`, `gewch_anonimo`, `gewch_publicacion`, `gewch_correo_id`, `gewch_correo_de`, `gewch_correo_de_nombre`, `gewch_correo_para`, `gewch_correo_para_nombre`, `gewch_correo_cc`, `gewch_correo_bcc`, `gewch_correo_fecha`, `gewch_correo_asunto`, `gewch_correo_contenido`, `gewch_embeddedimage_ruta`, `gewch_embeddedimage_nombre`, `gewch_embeddedimage_tipo`, `gewch_attachment_ruta`, `gewch_intentos`, `gewch_estado_envio`, `gewch_fecha_envio`, `gewch_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

		        // Agrega variables a sentencia preparada
		        $consulta_registro_historial_insert->bind_param('ssssssssssssssssssssssssss', $gewch_radicado, $gewch_radicado_id, $gewch_tipo, $gewch_tipologia, $gewch_gestion, $gewch_gestion_detalle, $gewch_anonimo, $gewch_publicacion, $gewch_correo_id, $gewch_correo_de, $gewch_correo_de_nombre, $gewch_correo_para, $gewch_correo_para_nombre, $gewch_correo_cc, $gewch_correo_bcc, $gewch_correo_fecha, $gewch_correo_asunto, $gewch_correo_contenido, $gewch_embeddedimage_ruta, $gewch_embeddedimage_nombre, $gewch_embeddedimage_tipo, $gewch_attachment_ruta, $gewch_intentos, $gewch_estado_envio, $gewch_fecha_envio, $gewch_registro_usuario);

		        $gewc_radicado_entrada=str_replace('Gestión de la petición ', '', $mensaje_asunto);
				$gewc_radicado_salida='';
				$gewc_tipologia='';
				$gewc_clasificacion='';


				//Recupera todos los adjuntos del correo
				$attachments = $mail->get_mail_attachments($guzzle, $array_folders['iqgis_pendiente']['id'], $mensaje_id);

				if (!isset($attachments['value'])) {
					$attachments['value']=array();
				}

		        //Tipificación
				    $nombre_radicado_salida='';
		        	if(count($attachments['value'])>0){
						for ($j=0; $j < count($attachments['value']); $j++) { 
	   						if ($attachments['value'][$j]['name']!="") {
				            	$nombre_soporte1=$attachments['value'][$j]['name'];

					            if (stripos($nombre_soporte1, 'DPS - Petición Respuesta Firma Mecánica') !== false) {
								    $nombre_radicado_salida=$nombre_soporte1;
								}
				            }
						}
					}

					if ($nombre_radicado_salida!='') {
				        $tipo_explode=explode('-', $nombre_radicado_salida);

				        if ($tipo_explode[2]=='2002') {
				        	$gewc_tipologia='Reparto';
				        } elseif ($tipo_explode[2]=='3000') {
				        	$gewc_tipologia='Subsidio Familiar de Vivienda en especie';
				        } elseif ($tipo_explode[2]=='4423') {
				        	$gewc_tipologia='Ingreso Solidario';
				        } elseif ($tipo_explode[2]=='4422') {
				        	$gewc_tipologia='Colombia Mayor';
				        } elseif ($tipo_explode[2]=='4421') {
				        	$gewc_tipologia='Compensación del IVA';
				        } elseif ($tipo_explode[2]=='4401') {
				        	$gewc_tipologia='Antifraudes';
				        } elseif ($tipo_explode[2]=='4412') {
				        	$gewc_tipologia='Jóvenes en Acción';
				        } elseif ($tipo_explode[2]=='4411') {
				        	$gewc_tipologia='Tránsito a Renta Ciudadana';
				        } else {
				        	$gewc_tipologia='Otros programas';
				        }

						$gewc_radicado_salida=$tipo_explode[0].'-'.$tipo_explode[1].'-'.$tipo_explode[2].'-'.$tipo_explode[3];
					} else {
						$gewc_radicado_salida='';
						$gewc_tipologia='Otros programas';
						$gewc_clasificacion='Sin radicado salida';
					}


				$filtro_agente='';
				$filtro_agente=" AND `usu_cargo_rol` LIKE '%AGENTE ENVÍOS WEB-TRANSVERSAL%'";
				$filtro_agente_tipologia="";
				

				// $agente_responsable='1020773403';
				if ($filtro_agente!='') {
					$consulta_string_analista="SELECT `usu_id`, TCONTEO.CANTIDAD FROM `administrador_usuario` LEFT JOIN (SELECT `gewc_responsable`, COUNT(`gewc_id`) AS CANTIDAD FROM `gestion_enviosweb_casos` WHERE `gewc_registro_fecha_hora`=? ".$filtro_agente_tipologia." GROUP BY `gewc_responsable`) AS TCONTEO ON `administrador_usuario`.`usu_id`=TCONTEO.`gewc_responsable` WHERE `usu_estado`='Activo' AND `usu_reparto`='Activo' ".$filtro_agente." ORDER BY TCONTEO.CANTIDAD ASC";

					$consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
					if (count($data_consulta_analistas)>0) {
					    // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta_analistas en el orden específico de los parámetros de la sentencia preparada
					    $consulta_registros_analistas->bind_param(str_repeat("s", count($data_consulta_analistas)), ...$data_consulta_analistas);
					}
					$consulta_registros_analistas->execute();
					$resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

					if (count($resultado_registros_analistas)>0) {
						$agente_responsable=$resultado_registros_analistas[0][0];
					}
				}

				$gewc_radicado='';
				$gewc_responsable=$agente_responsable;
				$gewc_gestion='';
				$gewc_gestion_detalle='';
				$gewc_estado='Pendiente';
				$gewc_fecha_gestion='';
				$gewc_correo_remitente=$mensaje_de;
				$gewc_correo_asunto=$mensaje_asunto;
				$gewc_correo_fecha=$fecha_enviado_registro;
				$gewc_registro_fecha=date('Y-m-d');

				if ($consulta_registro_correo_ingreso_insert->execute()) { //Se valida el tipo de correo
					// Obtén el ID generado
					$id_insertado = $enlace_db->insert_id;


					// $data_consulta_consecutivo=array();
					// $filtro_consecutivo='GR'.date('Y');
					// array_push($data_consulta_consecutivo, "%$filtro_consecutivo%");

					// $consulta_string_consecutivo="SELECT MAX(`grc_radicado`) FROM `gestion_enviosweb_casos` WHERE `grc_radicado` LIKE ?";
					// $consulta_registros_consecutivo = $enlace_db->prepare($consulta_string_consecutivo);
					// $consulta_registros_consecutivo->bind_param(str_repeat("s", count($data_consulta_consecutivo)), ...$data_consulta_consecutivo);
					// $consulta_registros_consecutivo->execute();
					// $resultado_registros_consecutivo = $consulta_registros_consecutivo->get_result()->fetch_all(MYSQLI_NUM);

			        // $ultimo_consecutivo=explode($filtro_consecutivo, $resultado_registros_consecutivo[0][0]);
			        // $nuevo_consecutivo=intval($ultimo_consecutivo[1])+1;
			        $inser_consecutivo="GE".date('Y').str_pad($id_insertado, 7, 0, STR_PAD_LEFT);

			        // Prepara la sentencia
					$consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_enviosweb_casos` SET `gewc_radicado`=? WHERE `gewc_id`=?");

					// Agrega variables a sentencia preparada
					$consulta_actualizar->bind_param('ss', $inser_consecutivo, $id_insertado);

					// Ejecuta sentencia preparada
					$consulta_actualizar->execute();

					//Adjuntos en body correo
					for ($j=0; $j < count($attachments['value']); $j++) { 
						if ($attachments['value'][$j]['name']!="") {
			            	// Define el contenido de la imagen en base64
							$base64ImageData = $attachments['value'][$j]['contentBytes']; // Sustituye por los datos de la imagen en base64
							$contentType = $attachments['value'][$j]['contentType']; // tipo de imagen
							// Define el CID a buscar y reemplazar
							$cidToReplace = $attachments['value'][$j]['contentId'];

							// Encuentra todas las coincidencias del CID en la cadena HTML
							$matches = [];
							if (preg_match_all('/cid:' . preg_quote($cidToReplace, '/') . '/', $mensaje_contenido, $matches)) {
							    // Realiza el reemplazo por la imagen codificada en base64
							    foreach ($matches[0] as $match) {
							        $mensaje_contenido = str_replace($match, 'data:'.$contentType.';base64,' . $base64ImageData, $mensaje_contenido);
							    }
							}
			            }
					}

					## DATOS HISTORIAL

					$gewch_radicado=$inser_consecutivo;
					$gewch_radicado_id=$id_insertado;
					$gewch_tipo='Radicado';
					$gewch_tipologia=$gewc_tipologia;
					$gewch_gestion='';
					$gewch_gestion_detalle='';
					$gewch_anonimo='';
					$gewch_publicacion='';
					$gewch_correo_id=$mensaje_id;
					$gewch_correo_de=$mensaje_de;
					$gewch_correo_de_nombre=$mensaje_de_nombre;
					$gewch_correo_para=$mensaje_para;
					$gewch_correo_para_nombre=$mensaje_para_nombre;
					$gewch_correo_cc=$mensaje_cc;
					$gewch_correo_bcc='';
					$gewch_correo_fecha=$fecha_enviado_registro;
					$gewch_correo_asunto=$mensaje_asunto;
					$gewch_correo_contenido=$mensaje_contenido;
					$gewch_embeddedimage_ruta='';
					$gewch_embeddedimage_nombre='';
					$gewch_embeddedimage_tipo='';
					$gewch_attachment_ruta='';
					$gewch_intentos='';
					$gewch_estado_envio='';
					$gewch_fecha_envio='';
					$gewch_registro_usuario='';

					if ($consulta_registro_historial_insert->execute()) { //Inserta registro historial
						// Obtén el ID generado
						$id_insertado_historial = $enlace_db->insert_id;

						$descarga_adjunto=0;
					  	$ruta_guardar="/var/www/html/templates/envios_web/storage/".$inser_consecutivo."/";
						if(count($attachments['value'])>0){
					       	if (!file_exists($ruta_guardar)) {
							    mkdir($ruta_guardar, 0777, true);
							}

							$descarga_adjunto=0;
							for ($j=0; $j < count($attachments['value']); $j++) { 
								if (!isset($attachments['value'][$j]['isInline'])) {
									$attachments['value'][$j]['isInline']='';
								}

		   						if ($attachments['value'][$j]['name']!="" AND $attachments['value'][$j]['isInline']=='') {
					            	$nombre_soporte1=$attachments['value'][$j]['name'];
					            	$nombre_soporte_guardar=$attachments['value'][$j]['name'];
						            $ruta_guardar_final=$ruta_guardar.$codigo.$nombre_soporte1;

						            $extension=pathinfo($ruta_guardar_final, PATHINFO_EXTENSION);

						            if ($extension!='') {
							            $contenido_adjunto=base64_decode($attachments['value'][$j]['contentBytes']);
					       				if (file_put_contents($ruta_guardar_final, $contenido_adjunto)) {
					       					$descarga_adjunto=1;
					       					// Prepara la sentencia
									        $correo_adjunto_insert = $enlace_db->prepare("INSERT INTO `gestion_enviosweb_casos_adjuntos`(`gewca_radicado`, `gewca_radicado_id`, `gewca_historial_id`, `gewca_nombre`, `gewca_ruta`, `gewca_extension`, `gewca_tipo`, `gewca_peso`, `gewca_estado`, `gewca_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?)");

									        // Agrega variables a sentencia preparada
									        $correo_adjunto_insert->bind_param('ssssssssss', $gewca_radicado, $gewca_radicado_id, $gewca_historial_id, $gewca_nombre, $gewca_ruta, $gewca_extension, $gewca_tipo, $gewca_peso, $gewca_estado, $gewca_registro_usuario);

									        $gewca_radicado=$inser_consecutivo;
											$gewca_radicado_id=$id_insertado;
											$gewca_historial_id=$id_insertado_historial;
											$gewca_nombre=$nombre_soporte_guardar;
											$gewca_ruta='storage/'.$inser_consecutivo.'/'.$nombre_soporte1;
											$gewca_ruta_size='/var/www/html/templates/envios_web/storage/'.$inser_consecutivo.'/'.$nombre_soporte1;
											$gewca_extension=$extension;
											$gewca_tipo='Original';
											if (file_exists($gewca_ruta_size)) {
										    	$gewca_peso = filesize($gewca_ruta_size);
											} else {
												$gewca_peso='';
											}
											$gewca_estado='Activo';
											$gewca_registro_usuario='1';

											$correo_adjunto_insert->execute();
					       				} else {
					       					$descarga_adjunto=0;
					       				}
						            }
					            }
							}
						}
						$mover_correo=$mail->mail_move($guzzle, $array_folders['iqgis_pendiente']['id'], $mensaje_id, $array_folders['iqgis_cargado']['id']);
					} else {
						// $mover_correo=$mail->mail_move($guzzle, $array_folders['iqgis_pendiente']['id'], $mensaje_id, $array_folders['iqgis_error']['id']);
					}

				} else {
					// $mover_correo=$mail->mail_move($guzzle, $array_folders['iqgis_pendiente']['id'], $mensaje_id, $array_folders['iqgis_error']['id']);
				}
			} else {
				$mover_correo=$mail->mail_move($guzzle, $array_folders['iqgis_pendiente']['id'], $mensaje_id, $array_folders['iqgis_error']['id']);
			}
	  	}
	}
?>