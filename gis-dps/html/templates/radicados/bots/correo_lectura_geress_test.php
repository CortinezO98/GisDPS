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

	$consulta_string_buzon="SELECT `ncr_id`, `ncr_host`, `ncr_port`, `ncr_smtpsecure`, `ncr_smtpauth`, `ncr_username`, `ncr_password`, `ncr_setfrom`, `ncr_setfrom_name`, `ncr_tenant`, `ncr_client_id`, `ncr_client_secret`, `ncr_device_code`, `ncr_token`, `ncr_token_refresh` FROM `administrador_buzones` WHERE `ncr_username`='geress@prosperidadsocial.gov.co' AND `ncr_tipo`='Lectura'";
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

	$mails_folder=$mail->get_mails_folder($guzzle, $array_folders['iqgis_radicados_test']['id']);

	// if (!isset($mails_folder['value'])) {
	// 	$mails_folder['value']=array();
	// }

	// echo "<pre>";
	// print_r($mails_folder);
	// echo "</pre>";

	$array_dominio_prioritarios[]='fiscalia.gov.co';
	$array_dominio_prioritarios[]='policia.gov.co';
	$array_dominio_prioritarios[]='correo.policia.gov.co';
	$array_dominio_prioritarios[]='procuraduria.gov.co';
	$array_dominio_prioritarios[]='contraloria.gov.co';
	$array_dominio_prioritarios[]='personeria.gov.co';
	$array_dominio_prioritarios[]='defensoria.gov.co';

	$array_prioritarios_remitentes[]='cabilveo@instanticorrup.org';
	$array_prioritarios_remitentes[]='asesorext02@summav.com';
	$array_prioritarios_remitentes[]='ivancepedacongresista@gmail.com';
	$array_prioritarios_remitentes[]='juanitaG@juanitaenelcongreso.com';
	$array_prioritarios_remitentes[]='equipofabiandiaz@gmail.com';
	$array_prioritarios_remitentes[]='control_interno@prosperidadsocial.gov.co';


	$array_prioritarios_palabra[]='audiencia';
	$array_prioritarios_palabra[]='citación';
	$array_prioritarios_palabra[]='desacato';
	$array_prioritarios_palabra[]='fallo';
	$array_prioritarios_palabra[]='cámara';
	$array_prioritarios_palabra[]='senado';
	$array_prioritarios_palabra[]='control interno';

	// Ciudadanos TRANSITO A RC FA
	$array_ciudadano_palabra['transito_rc'][]='tránsito a renta ciudadana';
	$array_ciudadano_palabra['transito_rc'][]='familias en acción';
	$array_ciudadano_palabra['transito_rc'][]='renta ciudadana';
	$array_ciudadano_palabra['transito_rc'][]='crecimiento y desarrollo';

	// Ciudadanos JÓVENES EN ACCIÓN
	$array_ciudadano_palabra['jovenes_accion'][]='jóvenes en acción';
	$array_ciudadano_palabra['jovenes_accion'][]='jóvenes';
	$array_ciudadano_palabra['jovenes_accion'][]='sena';


	// Ciudadanos COMPENSACIÓN IVA
	$array_ciudadano_palabra['compensacion_iva'][]='devolución de iva';
	$array_ciudadano_palabra['compensacion_iva'][]='compensación de iva';
	$array_ciudadano_palabra['compensacion_iva'][]='iva';
	$array_ciudadano_palabra['compensacion_iva'][]='devolución';


	// Ciudadanos COLOMBIA MAYOR
	$array_ciudadano_palabra['colombia_mayor'][]='colombia mayor';
	$array_ciudadano_palabra['colombia_mayor'][]='adulto mayor';
	$array_ciudadano_palabra['colombia_mayor'][]='mayor';

	// Ciudadanos INGRESO SOLIDARIO
	$array_ciudadano_palabra['ingreso_solidario'][]='ingreso.solidario@prosperidadsocial.gov.co';
	$array_ciudadano_palabra['ingreso_solidario'][]='ingreso solidario';
	$array_ciudadano_palabra['ingreso_solidario'][]='is';

	if (!isset($mails_folder['value'])) {
		$mails_folder['value']=array();
	}


	$limite_lectura=count($mails_folder['value']);
	
	if ($limite_lectura>0) {
		if ($limite_lectura>100) {
			$limite_lectura=100;
		}

		$data_consulta_analistas=array();
		$grc_registro_fecha=date('Y-m-d');


		array_push($data_consulta_analistas, $grc_registro_fecha);

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
			$mensaje_contenido=removeEmojis(quitarCaracteresDeCorreoElectronico($mails_folder['value'][$i]['body']['content']));
			
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
		        $consulta_registro_correo_ingreso_insert = $enlace_db->prepare("INSERT INTO `gestion_radicacion_casos`(`grc_radicado`, `grc_tipologia`, `grc_clasificacion`, `grc_responsable`, `grc_gestion`, `grc_gestion_detalle`, `grc_estado`, `grc_duplicado`, `grc_unificado`, `grc_unificado_id`, `grc_dividido`, `grc_dividido_cantidad`, `grc_fecha_gestion`, `grc_correo_remitente`, `grc_correo_asunto`, `grc_correo_fecha`, `grc_registro_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

		        // Agrega variables a sentencia preparada
		        $consulta_registro_correo_ingreso_insert->bind_param('sssssssssssssssss', $grc_radicado, $grc_tipologia, $grc_clasificacion, $grc_responsable, $grc_gestion, $grc_gestion_detalle, $grc_estado, $grc_duplicado, $grc_unificado, $grc_unificado_id, $grc_dividido, $grc_dividido_cantidad, $grc_fecha_gestion, $grc_correo_remitente, $grc_correo_asunto, $grc_correo_fecha, $grc_registro_fecha);


		        ## INSERT HISTORIAL
		        // Prepara la sentencia
		        $consulta_registro_historial_insert = $enlace_db->prepare("INSERT INTO `gestion_radicacion_casos_historial`(`grch_radicado`, `grch_radicado_id`, `grch_tipo`, `grch_tipologia`, `grch_clasificacion`, `grch_gestion`, `grch_gestion_detalle`, `grch_duplicado`, `grch_unificado`, `grch_unificado_id`, `grch_dividido`, `grch_dividido_cantidad`, `grch_observaciones`, `grch_correo_id`, `grch_correo_de`, `grch_correo_de_nombre`, `grch_correo_para`, `grch_correo_para_nombre`, `grch_correo_cc`, `grch_correo_bcc`, `grch_correo_fecha`, `grch_correo_asunto`, `grch_correo_contenido`, `grch_embeddedimage_ruta`, `grch_embeddedimage_nombre`, `grch_embeddedimage_tipo`, `grch_attachment_ruta`, `grch_intentos`, `grch_estado_envio`, `grch_fecha_envio`, `grch_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

		        // Agrega variables a sentencia preparada
		        $consulta_registro_historial_insert->bind_param('sssssssssssssssssssssssssssssss', $grch_radicado, $grch_radicado_id, $grch_tipo, $grch_tipologia, $grch_clasificacion, $grch_gestion, $grch_gestion_detalle, $grch_duplicado, $grch_unificado, $grch_unificado_id, $grch_dividido, $grch_dividido_cantidad, $grch_observaciones, $grch_correo_id, $grch_correo_de, $grch_correo_de_nombre, $grch_correo_para, $grch_correo_para_nombre, $grch_correo_cc, $grch_correo_bcc, $grch_correo_fecha, $grch_correo_asunto, $grch_correo_contenido, $grch_embeddedimage_ruta, $grch_embeddedimage_nombre, $grch_embeddedimage_tipo, $grch_attachment_ruta, $grch_intentos, $grch_estado_envio, $grch_fecha_envio, $grch_registro_usuario);


		        $remitente_explode=explode('@', $mensaje_de);
		        $remitente_dominio=$remitente_explode[1];

		        $grc_tipologia = '';
		        $grc_clasificacion='';
		        $tipificado=false;

		        //Tipificación
			        // Verificamos si el correo es confirmación de lectura o de entrega
					if ($mensaje_de === 'microsoftexchange329e71ec88ae4615bbc36ab6ce41109e@dpsco.onmicrosoft.com' OR $mensaje_de === 'postmaster@outlook.com' OR stripos($mensaje_asunto, 'entregado:') !== false OR stripos($mensaje_asunto, 'retransmitido:') !== false OR stripos($mensaje_asunto, 'leído:') !== false OR stripos($mensaje_asunto, 'read:') !== false OR stripos($mensaje_asunto, 'not read:') !== false OR $mensaje_asunto=='RESPUESTA AUTOMATICA - CONFIRMACION RECIBIDO NOTIFICACIONES JUDICIALES') {
					    $grc_tipologia = 'Notificaciones de correo';
					    $grc_clasificacion = '';
					    $tipificado=true;
					}

					if (!$tipificado) {
						// Verificamos si el remitente está en el dominio de funcionarios
						if ($mensaje_de === 'deltapeticiones@prosperidadsocial.gov.co') {
						    $grc_tipologia = 'Envío Radicado a Ciudadano';
						    $grc_clasificacion = '';
						    $tipificado=true;
						} elseif ($mensaje_de === 'soytransparente@prosperidadsocial.gov.co') {
							$grc_tipologia = 'Soy Transparente';
							$grc_clasificacion = '';
					        $tipificado=true;
						} elseif ($remitente_dominio === 'cendoj.ramajudicial.gov.co' OR $mensaje_de === 'web-desarrollo@hotmail.com') {
							$grc_tipologia = 'Tutelas';
							$grc_clasificacion = '';
					        $tipificado=true;
						} elseif (in_array($remitente_dominio, $array_dominio_prioritarios)) {
						    $grc_tipologia = 'Prioritario';
						    $grc_clasificacion = 'Dominio';
						    $tipificado=true;
						} elseif (in_array($mensaje_de, $array_prioritarios_remitentes)) {
						    $grc_tipologia = 'Prioritario';
						    $grc_clasificacion = 'Remitente';
						    $tipificado=true;
						}
					}

					if (!$tipificado) {
						for ($j=0; $j < count($array_prioritarios_palabra); $j++) { 
					        $palabra=$array_prioritarios_palabra[$j];
					        if (stripos($mensaje_asunto, $palabra) !== false || stripos($mensaje_contenido, $palabra) !== false) {
					            $grc_tipologia = 'Prioritario';
					            $grc_clasificacion = 'Palabra Clave';
					            $tipificado=true;
					            break;
					        }
				        }
					}

					if (!$tipificado) {
						if ($remitente_dominio === 'prosperidadsocial.gov.co') {
							$grc_tipologia = 'Funcionarios';
							$grc_clasificacion = '';
					        $tipificado=true;
						}
					}

					if (!$tipificado) {
						for ($j=0; $j < count($array_ciudadano_palabra['transito_rc']); $j++) { 
					        $palabra=$array_ciudadano_palabra['transito_rc'][$j];
					        if (stripos($mensaje_asunto, $palabra) !== false || stripos($mensaje_contenido, $palabra) !== false) {
					            $grc_tipologia = 'Ciudadanos';
					            $grc_clasificacion = 'Tránsito a R.C (FA)';
					            $tipificado=true;
					            break;
					        }
				        }
					}

					if (!$tipificado) {
						for ($j=0; $j < count($array_ciudadano_palabra['jovenes_accion']); $j++) { 
					        $palabra=$array_ciudadano_palabra['jovenes_accion'][$j];
					        if (stripos($mensaje_asunto, $palabra) !== false || stripos($mensaje_contenido, $palabra) !== false) {
					            $grc_tipologia = 'Ciudadanos';
					            $grc_clasificacion = 'Jóvenes en Acción';
					            $tipificado=true;
					            break;
					        }
				        }
					}

					if (!$tipificado) {
						for ($j=0; $j < count($array_ciudadano_palabra['compensacion_iva']); $j++) { 
					        $palabra=$array_ciudadano_palabra['compensacion_iva'][$j];
					        if (stripos($mensaje_asunto, $palabra) !== false || stripos($mensaje_contenido, $palabra) !== false) {
					            $grc_tipologia = 'Ciudadanos';
					            $grc_clasificacion = 'Compensación del IVA';
					            $tipificado=true;
					            break;
					        }
				        }
					}

					if (!$tipificado) {
						for ($j=0; $j < count($array_ciudadano_palabra['colombia_mayor']); $j++) { 
					        $palabra=$array_ciudadano_palabra['colombia_mayor'][$j];
					        if (stripos($mensaje_asunto, $palabra) !== false || stripos($mensaje_contenido, $palabra) !== false) {
					            $grc_tipologia = 'Ciudadanos';
					            $grc_clasificacion = 'Colombia Mayor';
					            $tipificado=true;
					            break;
					        }
				        }
					}

					if (!$tipificado) {
						for ($j=0; $j < count($array_ciudadano_palabra['ingreso_solidario']); $j++) { 
					        $palabra=$array_ciudadano_palabra['ingreso_solidario'][$j];
					        if (stripos($mensaje_asunto, $palabra) !== false || stripos($mensaje_contenido, $palabra) !== false) {
					            $grc_tipologia = 'Ciudadanos';
					            $grc_clasificacion = 'Ingreso Solidario';
					            $tipificado=true;
					            break;
					        }
				        }
					}

					if (!$tipificado) {
						$grc_tipologia = 'Ciudadanos';
						$grc_clasificacion = 'Otros temas';
				        $tipificado=true;
					}


				$filtro_agente='';
				if ($grc_tipologia=='Notificaciones de correo') {
					$filtro_agente=" AND `usu_cargo_rol` LIKE '%AGENTE NOTIFICACIONES DE CORREO%'";
					$filtro_agente_tipologia=" AND `grc_tipologia`='Notificaciones de correo'";
				} elseif ($grc_tipologia=='Envío Radicado a Ciudadano') {
					$filtro_agente=" AND `usu_cargo_rol` LIKE '%AGENTE ENVÍO RADICADO A CIUDADANO%'";
					$filtro_agente_tipologia=" AND `grc_tipologia`='Envío Radicado a Ciudadano'";
				} elseif ($grc_tipologia=='Tutelas') {
					$filtro_agente=" AND `usu_cargo_rol` LIKE '%AGENTE TUTELAS%'";
					$filtro_agente_tipologia=" AND `grc_tipologia`='Tutelas'";
				} elseif ($grc_tipologia=='Soy Transparente') {
					$filtro_agente=" AND `usu_cargo_rol` LIKE '%AGENTE SOY TRANSPARENTE%'";
					$filtro_agente_tipologia=" AND `grc_tipologia`='Soy Transparente'";
				} elseif ($grc_tipologia=='Prioritario') {
					$filtro_agente=" AND `usu_cargo_rol` LIKE '%AGENTE PRIORITARIOS%'";
					$filtro_agente_tipologia=" AND `grc_tipologia`='Prioritario'";
				} elseif ($grc_tipologia=='Funcionarios') {
					$filtro_agente=" AND `usu_cargo_rol` LIKE '%AGENTE FUNCIONARIOS%'";
					$filtro_agente_tipologia=" AND `grc_tipologia`='Funcionarios'";
				} elseif ($grc_tipologia=='Ciudadanos') {
					$filtro_agente=" AND `usu_cargo_rol` LIKE '%AGENTE CIUDADANOS%'";
					$filtro_agente_tipologia=" AND `grc_tipologia`='Ciudadanos'";
				}

				$agente_responsable='';
				if ($filtro_agente!='') {
					$consulta_string_analista="SELECT `usu_id`, TCONTEO.CANTIDAD FROM `administrador_usuario` LEFT JOIN (SELECT `grc_responsable`, COUNT(`grc_id`) AS CANTIDAD FROM `gestion_radicacion_casos` WHERE `grc_registro_fecha_hora`=? ".$filtro_agente_tipologia." GROUP BY `grc_responsable`) AS TCONTEO ON `administrador_usuario`.`usu_id`=TCONTEO.`grc_responsable` WHERE `usu_estado`='Activo' AND `usu_reparto`='Activo' ".$filtro_agente." ORDER BY TCONTEO.CANTIDAD ASC";

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

				$grc_radicado='';
				$grc_responsable=$agente_responsable;
				
				if ($grc_tipologia=='Notificaciones de correo') {
					$grc_gestion='Archivar';
					$grc_gestion_detalle='';
					$grc_estado='Finalizado';
				} else {
					$grc_gestion='';
					$grc_gestion_detalle='';
					$grc_estado='Pendiente';
				}


				$grc_duplicado='';
				$grc_unificado='';
				$grc_unificado_id='';
				$grc_dividido='';
				$grc_dividido_cantidad='';
				$grc_fecha_gestion='';
				$grc_correo_remitente=$mensaje_de;
				$grc_correo_asunto=$mensaje_asunto;
				$grc_correo_fecha=$fecha_enviado_registro;
				$grc_registro_fecha=date('Y-m-d');

				// $consulta_registro_correo_ingreso_insert->execute()
				if (true) { //Se valida el tipo de correo
					// Obtén el ID generado
					// $id_insertado = $enlace_db->insert_id;


					// $data_consulta_consecutivo=array();
					// $filtro_consecutivo='GR'.date('Y');
					// array_push($data_consulta_consecutivo, "%$filtro_consecutivo%");

					// $consulta_string_consecutivo="SELECT MAX(`grc_radicado`) FROM `gestion_radicacion_casos` WHERE `grc_radicado` LIKE ?";
					// $consulta_registros_consecutivo = $enlace_db->prepare($consulta_string_consecutivo);
					// $consulta_registros_consecutivo->bind_param(str_repeat("s", count($data_consulta_consecutivo)), ...$data_consulta_consecutivo);
					// $consulta_registros_consecutivo->execute();
					// $resultado_registros_consecutivo = $consulta_registros_consecutivo->get_result()->fetch_all(MYSQLI_NUM);

			        // $ultimo_consecutivo=explode($filtro_consecutivo, $resultado_registros_consecutivo[0][0]);
			        // $nuevo_consecutivo=intval($ultimo_consecutivo[1])+1;
			        $inser_consecutivo="GR".date('Y').str_pad($id_insertado, 7, 0, STR_PAD_LEFT);

			        // Prepara la sentencia
					$consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_radicacion_casos` SET `grc_radicado`=? WHERE `grc_id`=?");

					// Agrega variables a sentencia preparada
					$consulta_actualizar->bind_param('ss', $inser_consecutivo, $id_insertado);

					// Ejecuta sentencia preparada
					// $consulta_actualizar->execute();

					//Recupera todos los adjuntos del correo
					$attachments = $mail->get_mail_attachments($guzzle, $array_folders['iqgis_radicados_pendiente']['id'], $mensaje_id);

					if (!isset($attachments['value'])) {
						$attachments['value']=array();
					}

					echo "<pre>";
					print_r($attachments);
					echo "</pre>";

					for ($j=0; $j < count($attachments['value']); $j++) { 
						if ($attachments['value'][$j]['contentId']!="") {
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

					echo "<textarea>".$mensaje_contenido."</textarea>";

					## DATOS HISTORIAL

					$grch_radicado=$inser_consecutivo;
					$grch_radicado_id=$id_insertado;
					$grch_tipo='Radicado';
					$grch_tipologia=$grc_tipologia;
					$grch_clasificacion=$grc_clasificacion;
					$grch_gestion='';
					$grch_gestion_detalle='';
					$grch_duplicado='';
					$grch_unificado='';
					$grch_unificado_id='';
					$grch_dividido='';
					$grch_dividido_cantidad='';
					$grch_observaciones='';
					$grch_correo_id=$mensaje_id;
					$grch_correo_de=$mensaje_de;
					$grch_correo_de_nombre=$mensaje_de_nombre;
					$grch_correo_para=$mensaje_para;
					$grch_correo_para_nombre=$mensaje_para_nombre;
					$grch_correo_cc=$mensaje_cc;
					$grch_correo_bcc='';
					$grch_correo_fecha=$fecha_enviado_registro;
					$grch_correo_asunto=$mensaje_asunto;
					$grch_correo_contenido=$mensaje_contenido;
					$grch_embeddedimage_ruta='';
					$grch_embeddedimage_nombre='';
					$grch_embeddedimage_tipo='';
					$grch_attachment_ruta='';
					$grch_intentos='';
					$grch_estado_envio='';
					$grch_fecha_envio='';
					$grch_registro_usuario='';


					// if ($consulta_registro_historial_insert->execute()) { //Inserta registro historial
					// 	// Obtén el ID generado
					// 	$id_insertado_historial = $enlace_db->insert_id;

					// 	$descarga_adjunto=0;
					//   	$ruta_guardar="/var/www/html/templates/radicados/storage/".$inser_consecutivo."/";
					// 	if(count($attachments['value'])>0){
					//        	if (!file_exists($ruta_guardar)) {
					// 		    mkdir($ruta_guardar, 0777, true);
					// 		}

					// 		$descarga_adjunto=0;
					// 		for ($j=0; $j < count($attachments['value']); $j++) { 
					// 			if (!isset($attachments['value'][$j]['isInline'])) {
					// 				$attachments['value'][$j]['isInline']='';
					// 			}

		   			// 			if ($attachments['value'][$j]['name']!="" AND $attachments['value'][$j]['isInline']=='') {
					//             	$nombre_soporte1=$attachments['value'][$j]['name'];
					//             	$nombre_soporte_guardar=$attachments['value'][$j]['name'];
					// 	            $ruta_guardar_final=$ruta_guardar.$codigo.$nombre_soporte1;

					// 	            $extension=pathinfo($ruta_guardar_final, PATHINFO_EXTENSION);

					// 	            if ($extension!='') {
					// 		            $contenido_adjunto=base64_decode($attachments['value'][$j]['contentBytes']);
					//        				if (file_put_contents($ruta_guardar_final, $contenido_adjunto)) {
					//        					$descarga_adjunto=1;
					//        					// Prepara la sentencia
					// 				        $correo_adjunto_insert = $enlace_db->prepare("INSERT INTO `gestion_radicacion_casos_adjuntos`(`grca_radicado`, `grca_radicado_id`, `grca_historial_id`, `grca_nombre`, `grca_ruta`, `grca_extension`, `grca_tipo`, `grca_estado`, `grca_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?)");

					// 				        // Agrega variables a sentencia preparada
					// 				        $correo_adjunto_insert->bind_param('sssssssss', $grca_radicado, $grca_radicado_id, $grca_historial_id, $grca_nombre, $grca_ruta, $grca_extension, $grca_tipo, $grca_estado, $grca_registro_usuario);

					// 				        $grca_radicado=$inser_consecutivo;
					// 						$grca_radicado_id=$id_insertado;
					// 						$grca_historial_id=$id_insertado_historial;
					// 						$grca_nombre=$nombre_soporte_guardar;
					// 						$grca_ruta='storage/'.$inser_consecutivo.'/'.$nombre_soporte1;
					// 						$grca_extension=$extension;
					// 						$grca_tipo='Original';
					// 						$grca_estado='Activo';
					// 						$grca_registro_usuario='1';

					// 						// $correo_adjunto_insert->execute();
					//        				} else {
					//        					$descarga_adjunto=0;
					//        				}
					// 	            }
					//             }
					// 		}
					// 	}
					// 	// $mover_correo=$mail->mail_move($guzzle, $array_folders['iqgis_radicados_pendiente']['id'], $mensaje_id, $array_folders['iqgis_radicados_cargado']['id']);
					// } else {
					// 	// $mover_correo=$mail->mail_move($guzzle, $array_folders['iqgis_radicados_pendiente']['id'], $mensaje_id, $array_folders['iqgis_radicados_error']['id']);
					// }

				} else {
					// $mover_correo=$mail->mail_move($guzzle, $array_folders['iqgis_radicados_pendiente']['id'], $mensaje_id, $array_folders['iqgis_radicados_error']['id']);
				}
			} else {
				// $mover_correo=$mail->mail_move($guzzle, $array_folders['iqgis_radicados_pendiente']['id'], $mensaje_id, $array_folders['iqgis_radicados_error']['id']);
			}
	  	}
	}
?>