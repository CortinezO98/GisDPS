<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Gestión";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Gestión | Editar";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $filtro_estado_permanente=validar_input($_GET['estado']);

  if ($filtro_estado_permanente!='null') {
      $filtro_estado_permanente=unserialize($_GET['estado']);
  } else {
      $filtro_estado_permanente=array();
  }
 
  $estado_url=serialize($filtro_estado_permanente);
  $estado_url=urlencode($estado_url);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $url_salir="familias_accion?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".base64_encode($bandeja)."&estado=".$estado_url;
 
  $consulta_string_caso="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_observaciones`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TAG.`usu_nombres_apellidos`, TOCR.`ocr_telefono`, TOCR.`ocr_celular`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio`, TOCR.`ocr_correo` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` WHERE `ocrr_id`=?";
  $consulta_registros_caso = $enlace_db->prepare($consulta_string_caso);
  $consulta_registros_caso->bind_param("s", $id_registro);
  $consulta_registros_caso->execute();
  $resultado_registros_caso = $consulta_registros_caso->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string="SELECT `ocrc_id`, `ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`, `ocrc_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TOCR.`ocr_telefono`, TOCR.`ocr_celular`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio`, TOCR.`ocr_correo` FROM `gestion_ocr_consolidado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_consolidado`.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `ocrc_cod_familia`=? ORDER BY `ocrc_codbeneficiario` ASC";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $resultado_registros_caso[0][1]);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
  $tiene_correo_valida=0;
  $correo_valida='';
  for ($i=0; $i < count($resultado_registros); $i++) { 
    if($resultado_registros[$i][3]=='SI' AND $resultado_registros[$i][40]!=""){
      $tiene_correo_valida=1;
      $correo_valida=$resultado_registros[$i][40];
    }
  }

  if(isset($_POST["guardar_registro"])){
      $estado=validar_input($_POST['estado']);
      $observaciones=validar_input($_POST['observaciones']);
      $correo=validar_input($_POST['correo']);
      $suministra_correo=validar_input($_POST['suministra_correo']);
      $tipificacion=validar_input($_POST['tipificacion']);
      $id_llamada=validar_input($_POST['id_llamada']);
      
      $telefono=validar_input($_POST['telefono']);
      $celular=validar_input($_POST['celular']);
      $correo_cabezafamilia=validar_input($_POST['correo_cabezafamilia']);

      if($_SESSION[APP_SESSION.'_registro_creado_familias_accion']!=1){
          // Prepara la sentencia
          $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ocr_resultado_avances`(`gora_codfamilia`, `gora_estado`, `gora_correo`, `gora_observaciones`, `gora_llamada_tipificacion`, `gora_llamada_id`, `gora_registro_usuario`) VALUES (?,?,?,?,?,?,?)");

          // Agrega variables a sentencia preparada
          $sentencia_insert->bind_param('sssssss', $resultado_registros_caso[0][1], $estado, $correo, $observaciones, $tipificacion, $id_llamada, $_SESSION[APP_SESSION.'_session_usu_id']);
          
          if ($sentencia_insert->execute()) {
              if ($resultado_registros_caso[0][7]==2 AND ($estado=='Intento Contacto-Fallido' OR $estado=='Intento Contacto-Fallido-Segunda Revisión')) {//Actualizado Ssegunda Fase
                if ($estado=='Intento Contacto-Fallido') {
                  $estado='Intento Contacto-Agotado';
                } elseif ($estado=='Intento Contacto-Fallido-Segunda Revisión') {
                  $estado='Intento Contacto-Agotado-Segunda Revisión';
                }
                // Prepara la sentencia
                $consulta_actualizar_intento = $enlace_db->prepare("UPDATE `gestion_ocr_resultado` SET `ocrr_gestion_intentos`=`ocrr_gestion_intentos`+1 WHERE `ocrr_id`=?");

                // Agrega variables a sentencia preparada
                $consulta_actualizar_intento->bind_param('s', $id_registro);
                
                // Ejecuta sentencia preparada
                $consulta_actualizar_intento->execute();
              }

              // Prepara la sentencia
              $consulta_actualizar = $enlace_db->prepare("UPDATE `gestion_ocr_resultado` SET `ocrr_gestion_estado`=?,`ocrr_gestion_observaciones`=?, `ocrr_gestion_fecha`=?, `ocrr_gestion_correo`=? WHERE `ocrr_id`=?");

              // Agrega variables a sentencia preparada
              $consulta_actualizar->bind_param('sssss', $estado, $observaciones, date('Y-m-d H:i:s'), $correo, $id_registro);
              
              // Ejecuta sentencia preparada
              $consulta_actualizar->execute();

              if (comprobarSentencia($enlace_db->info)) {
                  if ($estado=='Intento Contacto-Fallido' OR $estado=='Intento Contacto-Fallido-Segunda Revisión') {//Actualizado Ssegunda Fase
                      // Prepara la sentencia
                      $consulta_actualizar_intento = $enlace_db->prepare("UPDATE `gestion_ocr_resultado` SET `ocrr_gestion_intentos`=`ocrr_gestion_intentos`+1 WHERE `ocrr_id`=?");

                      // Agrega variables a sentencia preparada
                      $consulta_actualizar_intento->bind_param('s', $id_registro);
                      
                      // Ejecuta sentencia preparada
                      $consulta_actualizar_intento->execute();
                  }

                  if ($estado=='Contactado-Pendiente Documentos' OR $estado=='Intento Contacto-Fallido' OR $estado=='Nuevo Contacto-Error Subsanación' OR $estado=='Escalado-Validar' OR $estado=='Contactado-Pendiente Documentos-Segunda Revisión' OR $estado=='Intento Contacto-Fallido-Segunda Revisión' OR $estado=='Nuevo Contacto-Error Subsanación-Segunda Revisión' OR $estado=='Escalado-Validar-Segunda Revisión') {//Actualizado Ssegunda Fase
                      // Prepara la sentencia
                      $consulta_actualizar_tipificacion_llamada = $enlace_db->prepare("UPDATE `gestion_ocr_resultado` SET `ocrr_gestion_llamada_tipificacion`=?, `ocrr_gestion_llamada_id`=? WHERE `ocrr_id`=?");

                      // Agrega variables a sentencia preparada
                      $consulta_actualizar_tipificacion_llamada->bind_param('sss', $tipificacion, $id_llamada, $id_registro);
                      
                      // Ejecuta sentencia preparada
                      $consulta_actualizar_tipificacion_llamada->execute();
                  }

                  if ($telefono!="") {
                    // Prepara la sentencia
                    $consulta_actualizar_datos = $enlace_db->prepare("UPDATE `gestion_ocr` SET `ocr_telefono`=? WHERE `ocr_codfamilia`=? AND `ocr_cabezadefamilia`='SI'");

                    // Agrega variables a sentencia preparada
                    $consulta_actualizar_datos->bind_param('ss', $telefono, $resultado_registros_caso[0][1]);
                    
                    // Ejecuta sentencia preparada
                    $consulta_actualizar_datos->execute();
                  }

                  if ($celular!="") {
                    // Prepara la sentencia
                    $consulta_actualizar_datos = $enlace_db->prepare("UPDATE `gestion_ocr` SET `ocr_celular`=? WHERE `ocr_codfamilia`=? AND `ocr_cabezadefamilia`='SI'");

                    // Agrega variables a sentencia preparada
                    $consulta_actualizar_datos->bind_param('ss', $celular, $resultado_registros_caso[0][1]);
                    
                    // Ejecuta sentencia preparada
                    $consulta_actualizar_datos->execute();
                  }

                  if ($correo_cabezafamilia!="" OR ($suministra_correo=="Si" AND $correo!="")) {
                    if ($suministra_correo=="Si" AND $correo!="") {
                      $correo_actualiza=$correo;
                    } else {
                      $correo_actualiza=$correo_cabezafamilia;
                    }
                    // Prepara la sentencia
                    $consulta_actualizar_datos = $enlace_db->prepare("UPDATE `gestion_ocr` SET `ocr_correo`=? WHERE `ocr_codfamilia`=? AND `ocr_cabezadefamilia`='SI'");

                    // Agrega variables a sentencia preparada
                    $consulta_actualizar_datos->bind_param('ss', $correo_actualiza, $resultado_registros_caso[0][1]);
                    
                    // Ejecuta sentencia preparada
                    $consulta_actualizar_datos->execute();
                  }

                  if ((($estado=="Contactado-Pendiente Documentos" OR $estado=="Contactado-Pendiente Documentos-Segunda Revisión" OR $estado=="Pendiente llamada") AND $suministra_correo=="Si" AND $correo!="") OR (($estado=="Pendiente llamada" OR $estado=="Pendiente llamada-Segunda Revisión") AND $tiene_correo_valida)) {
                    // Envía correo
                    if (($estado=="Contactado-Pendiente Documentos" OR $estado=="Contactado-Pendiente Documentos-Segunda Revisión" OR $estado=="Pendiente llamada") AND $suministra_correo=="Si" AND $correo!="") {
                      $correo_notifica=$correo;
                    } else {
                      $correo_notifica=$correo_valida;
                    }

                    //PROGRAMACIÓN NOTIFICACIÓN
                    $asunto='Actualización de información – Inscripción Familias en Acción';
                    $referencia='Actualización de información – Inscripción Familias en Acción';
                    $contenido="<p>Respetado (a) Señor (a),<br><br>La inscripción es el proceso operativo por medio del cual las familias focalizadas en SISBEN IV, hacen efectiva su vinculación voluntaria al programa, previo conocimiento de las corresponsabilidades adquiridas para el acceso a los incentivos de salud y educación otorgados por el programa Familias en Acción.<br><br>De acuerdo con la información contenida en el Contrato Social todas las preinscripciones están sujetas a un “proceso de auditoría” en donde se validarán cada uno de los soportes entregados durante la inscripción y la información registrada en el sistema de información.<br><br>En atención a su proceso de vinculación al programa Familias en Acción, nos permitimos comunicarle que en este momento nos encontramos en actualización de datos con el fin de validar la información registrada por usted en el proceso inicial de Preinscripción; dentro de esta validación se identificó que algunos de los documentos suministrados se encuentran ilegibles o incompletos.<br><br>En ese sentido, le invitamos a completar/corregir dicha documentación dentro del término de máximo 15 días calendario a partir de la fecha, para de esta manera dar continuidad con su trámite de INSCRIPCIÓN. Para ello, es necesario contar con su colaboración para lo siguiente:<br>
                      <ol>
                        <li>Si cuenta con acceso a un computador o dispositivo electrónico con conexión a internet por favor ingrese al link de internet <a href='https://dps.iq-online.net.co/familiasenaccion/validar'>https://dps.iq-online.net.co/familiasenaccion/validar</a>, ingresando el número de documento de la persona inscrita como cabeza de familia.<br><br>A continuación, la siguiente documentación debe ser cargada consolidada en un solo archivo PDF legible, completa y sin enmendaduras (adjuntamos el paso a paso para el proceso de cargue de documentos):<br><br><b>".$observaciones."</b><br><br></li>
                        <li>Si no tiene acceso a internet para el cargue de los documentos, por favor acérquese a la oficina del Enlace Municipal de Familias en Acción ubicado en la Alcaldía Municipal para allegar la documentación en físico mencionada en el punto anterior, la cual debe ser legible, completa y sin enmendaduras.<br><br>Si se encuentra en la ciudad de Bogotá, por favor acérquese con la documentación indicada a los puntos de atención CADE o SuperCADES. Para conocer los puntos en Bogotá, lo invitamos a ingresar al siguiente link: <a href='https://prosperidadsocial.gov.co/atencion-al-ciudadano/atencion-presencial-bogota/'>https://prosperidadsocial.gov.co/atencion-al-ciudadano/atencion-presencial-bogota/</a></li>
                      </ol>
                      <br>
                      <p>
                      Si dentro de los documentos pendientes se encuentra el contrato social, puede descargarlo adjunto a este mensaje o ingresar al siguiente link: <a href='http://centrodedocumentacion.prosperidadsocial.gov.co/2021/Familias-en-Accion/Docs-Tecnicos-FA/Contrato%20Social%20Codificado%20Kawak.pdf'>http://centrodedocumentacion.prosperidadsocial.gov.co/2021/Familias-en-Accion/Docs-Tecnicos-FA/Contrato%20Social%20Codificado%20Kawak.pdf</a>
                      <br><br><br>
                      <b>Por favor NO responder a este correo ya que solamente es informativo.</b>
                      <br><br><br>
                      Cordialmente,
                      <br>
                      <br><img src='cid:firma-dps' style='height: 50px;'></img>
                      <br><b>Programa Familias en Acción</b>
                      <br>Línea Gratuita Nacional: 01-8000-95-1100 
                      <br>Línea en Bogotá: 601 3791088 
                      <br><a href='https://www.prosperidadsocial.gov.co/'>www.prosperidadsocial.gov.co</a>
                      </p>";
                    $nc_address=$correo_notifica.";";
                    $nc_cc="";
                    $estado_notificacion=notificacion_familias($enlace_db, $asunto, $referencia, $contenido, $nc_address, $modulo_plataforma, $nc_cc);
                    if ($estado_notificacion) {
                      // Prepara la sentencia
                      $consulta_actualizar_notificacion = $enlace_db->prepare("UPDATE `gestion_ocr_resultado` SET `ocrr_gestion_notificacion`='Si', `ocrr_gestion_notificacion_fecha_registro`=? WHERE `ocrr_id`=?");

                      // Agrega variables a sentencia preparada
                      $consulta_actualizar_notificacion->bind_param('ss', date('Y-m-d H:i:s'), $id_registro);
                      
                      // Ejecuta sentencia preparada
                      $consulta_actualizar_notificacion->execute();
                      $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
                      $_SESSION[APP_SESSION.'_registro_creado_familias_accion']=1;
                    } else {
                      $respuesta_accion = "alertButton('error', 'Error', 'Problemas al programar la notificación');";
                    }
                  } else {
                    $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
                    $_SESSION[APP_SESSION.'_registro_creado_familias_accion']=1;
                  }

                  $consulta_string="SELECT `ocrc_id`, `ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`, `ocrc_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TOCR.`ocr_telefono`, TOCR.`ocr_celular`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio`, TOCR.`ocr_correo` FROM `gestion_ocr_consolidado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_consolidado`.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `ocrc_cabezafamilia`='SI' AND `ocrc_cod_familia`=? ORDER BY `ocrc_codbeneficiario` ASC";

                  $consulta_registros = $enlace_db->prepare($consulta_string);
                  $consulta_registros->bind_param("s", $resultado_registros_caso[0][1]);
                  $consulta_registros->execute();
                  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
                  
                  $tiene_celular_valida=0;
                  $celular_valida=array();
                  $nombre_cabeza_familia='';
                  for ($i=0; $i < count($resultado_registros); $i++) { 
                    if($resultado_registros[$i][3]=='SI' AND (($resultado_registros[$i][36]!="" AND strlen($resultado_registros[$i][36])==10) OR ($resultado_registros[$i][37]!="" AND strlen($resultado_registros[$i][37])==10))){
                      $tiene_celular_valida=1;
                      
                      if ($resultado_registros[$i][36]!="" AND strlen($resultado_registros[$i][36])==10) {
                        $celular_valida[]=$resultado_registros[$i][36];
                      }

                      if ($resultado_registros[$i][37]!="" AND strlen($resultado_registros[$i][37])==10) {
                        $celular_valida[]=$resultado_registros[$i][37];
                      }

                      $identificador_valida=$resultado_registros[$i][0];
                      $nombre_cabeza_familia=$resultado_registros[$i][28];
                      if ($resultado_registros[$i][29]!="") {
                        $nombre_cabeza_familia.=' '.$resultado_registros[$i][29];
                      }
                      if ($resultado_registros[$i][30]!="") {
                        $nombre_cabeza_familia.=' '.$resultado_registros[$i][30];
                      }
                      if ($resultado_registros[$i][31]!="") {
                        $nombre_cabeza_familia.=' '.$resultado_registros[$i][31];
                      }
                    }
                  }

                  if (($estado=="Contactado-Pendiente Documentos" OR $estado=="Contactado-Pendiente Documentos-Segunda Revisión") AND $tiene_celular_valida AND $_SESSION[APP_SESSION.'_registro_creado_familias_accion']) {//Actualizado Segunda Fase
                    $celular_valida=array_values(array_unique($celular_valida));
                    $nsms_identificador=$identificador_valida;
                    $contenido_sms=$nombre_cabeza_familia.", acorde con la información suministrada vía telefónica, el link para cargar los documentos corregidos de su inscripción de Familias en Acción es: SHORTURL";
                    $nsms_url='https://dps.iq-online.net.co/familiasenaccion/validar';
                    for ($k=0; $k < count($celular_valida); $k++) {
                      $nsms_destino=$celular_valida[$k];
                      $estado_notificacion_sms=notificacion_familias_sms($enlace_db, $nsms_identificador, $nsms_destino, $contenido_sms, $nsms_url);
                      if ($estado_notificacion_sms) {
                        $estado_sms=1;
                      }
                    }
                  }
              } else {
                  $respuesta_accion = "alertButton('error', 'Error', 'Problemas al actualizar el registro');";
              }

          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente', '".$url_salir."');";
      }
  }

  $consulta_string_caso="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_observaciones`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TAG.`usu_nombres_apellidos`, TOCR.`ocr_telefono`, TOCR.`ocr_celular`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio`, TOCR.`ocr_correo` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` WHERE `ocrr_id`=?";
  $consulta_registros_caso = $enlace_db->prepare($consulta_string_caso);
  $consulta_registros_caso->bind_param("s", $id_registro);
  $consulta_registros_caso->execute();
  $resultado_registros_caso = $consulta_registros_caso->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string="SELECT `ocrc_id`, `ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`, `ocrc_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TOCR.`ocr_telefono`, TOCR.`ocr_celular`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio`, TOCR.`ocr_correo` FROM `gestion_ocr_consolidado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_consolidado`.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `ocrc_cod_familia`=? ORDER BY `ocrc_codbeneficiario` ASC";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $resultado_registros_caso[0][1]);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $control_sr=0;
  for ($i=0; $i < count($resultado_registros); $i++) {
    if ($resultado_registros[$i][25]=='No validado-OCR-SR' OR $resultado_registros[$i][25]=='Validado-OCR-SR' OR $resultado_registros[$i][25]=='Validado-Agente-SR' OR $resultado_registros[$i][25]=='Validado-Edad-SR') {
      $control_sr++;
    }
  }
  $control_cabeza=0;
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <!-- navbar -->
    <?php require_once(ROOT.'includes/_navbar.php'); ?>
    <!-- navbar -->
    <div class="container-fluid page-body-wrapper">
      <!-- sidebar -->
      <?php require_once(ROOT.'includes/_sidebar.php'); ?>
      <!-- sidebar -->
      <!-- main-panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
          <div class="row justify-content-center">
            <?php if (!empty($respuesta_accion)) {echo "<script type='text/javascript'>".$respuesta_accion."</script>";} ?>
            <div class="col-lg-8 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="table-responsive">
                          <table class="table table-bordered table-striped table-hover table-sm">
                              <thead>
                                  <tr>
                                    <td colspan="17" class="p-1 alert">Primera Revisión OCR</td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Cód. Beneficiario</th>
                                      <th class="px-1 py-2">Cabeza Familia</th>
                                      <th class="px-1 py-2">Documento</th>
                                      <th class="px-1 py-2">Nombres y Apellidos</th>
                                      <th class="px-1 py-2">Estado</th>
                                      <th class="px-1 py-2">Novedad</th>
                                      <th class="px-1 py-2">Contrato Existe</th>
                                      <th class="px-1 py-2">Contrato Nombre Titular</th>
                                      <th class="px-1 py-2">Contrato Firma</th>
                                      <th class="px-1 py-2">Contrato Huella</th>
                                      <th class="px-1 py-2">Documento</th>
                                      <th class="px-1 py-2">Nombres</th>
                                      <th class="px-1 py-2">Apellidos</th>
                                      <th class="px-1 py-2">Fecha Nacimiento</th>
                                      <th class="px-1 py-2">Fecha Expedición</th>
                                      <th class="px-1 py-2">Fecha Registro</th>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                                  <?php if($resultado_registros[$i][25]!='No validado-OCR-SR' AND $resultado_registros[$i][25]!='Validado-OCR-SR' AND $resultado_registros[$i][25]!='Validado-Agente-SR' AND $resultado_registros[$i][25]!='Validado-Edad-SR'): ?>
                                  <tr>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][2]; ?></td>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][3]; ?></td>
                                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][32]; ?></td>
                                      <td class="p-1 font-size-11">
                                          <?php echo $resultado_registros[$i][28]; ?>
                                          <?php echo ($resultado_registros[$i][29]!="") ? ' '.$resultado_registros[$i][29] : ''; ?>
                                          <?php echo ($resultado_registros[$i][30]!="") ? ' '.$resultado_registros[$i][30] : ''; ?>
                                          <?php echo ($resultado_registros[$i][31]!="") ? ' '.$resultado_registros[$i][31] : ''; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][25]; ?></td>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][26]; ?></td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                            <?php if ($resultado_registros[$i][17]==1): ?>
                                              <?php if($control_sr==0): ?>
                                                <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_existe');" class="py-0 px-1 font-size-11" id="contrato_existe_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                              <?php else: ?>
                                                <a href="#" class="py-0 px-1 font-size-11" id="contrato_existe_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                              <?php endif; ?>
                                            <?php elseif ($resultado_registros[$i][17]==''): ?>
                                              <?php if($control_sr==0): ?>
                                                <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_existe');" class="py-0 px-1 font-size-11" id="contrato_existe_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                              <?php else: ?>
                                                <a href="#" class="py-0 px-1 font-size-11" id="contrato_existe_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                              <?php endif; ?>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                            <?php if ($resultado_registros[$i][19]!='' AND $resultado_registros[$i][19]!='NA'): ?>
                                              <?php if($control_sr==0): ?>
                                                <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_nombre');" class="py-0 px-1 font-size-11" id="contrato_nombre_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                              <?php else: ?>
                                                <a href="#" class="py-0 px-1 font-size-11" id="contrato_nombre_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                              <?php endif; ?>
                                            <?php elseif ($resultado_registros[$i][19]==''): ?>
                                              <?php if($control_sr==0): ?>
                                                <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_nombre');" class="py-0 px-1 font-size-11" id="contrato_nombre_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                              <?php else: ?>
                                                <a href="#" class="py-0 px-1 font-size-11" id="contrato_nombre_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                              <?php endif; ?>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                            <?php if ($resultado_registros[$i][22]==1): ?>
                                              <?php if($control_sr==0): ?>
                                                <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_firma');" class="py-0 px-1 font-size-11" id="contrato_firma_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                              <?php else: ?>
                                                <a href="#" class="py-0 px-1 font-size-11" id="contrato_firma_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                              <?php endif; ?>
                                            <?php elseif ($resultado_registros[$i][22]==''): ?>
                                              <?php if($control_sr==0): ?>
                                                <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_firma');" class="py-0 px-1 font-size-11" id="contrato_firma_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                              <?php else: ?>
                                                <a href="#" class="py-0 px-1 font-size-11" id="contrato_firma_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                              <?php endif; ?>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                            <?php if ($resultado_registros[$i][23]==1): ?>
                                              <?php if($control_sr==0): ?>
                                                <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_huella');" class="py-0 px-1 font-size-11" id="contrato_huella_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                              <?php else: ?>
                                                <a href="#" class="py-0 px-1 font-size-11" id="contrato_huella_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                              <?php endif; ?>
                                            <?php elseif ($resultado_registros[$i][23]==''): ?>
                                              <?php if($control_sr==0): ?>
                                                <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_huella');" class="py-0 px-1 font-size-11" id="contrato_huella_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                              <?php else: ?>
                                                <a href="#" class="py-0 px-1 font-size-11" id="contrato_huella_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                              <?php endif; ?>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][6]==1): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'documento');" class="py-0 px-1 font-size-11" id="documento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="documento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php endif; ?>
                                          <?php elseif ($resultado_registros[$i][6]==''): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'documento');" class="py-0 px-1 font-size-11" id="documento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="documento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][9]==1): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'nombres');" class="py-0 px-1 font-size-11" id="nombres_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="nombres_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php endif; ?>
                                          <?php elseif ($resultado_registros[$i][9]==''): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'nombres');" class="py-0 px-1 font-size-11" id="nombres_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="nombres_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][11]==1): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'apellidos');" class="py-0 px-1 font-size-11" id="apellidos_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="apellidos_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php endif; ?>
                                          <?php elseif ($resultado_registros[$i][11]==''): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'apellidos');" class="py-0 px-1 font-size-11" id="apellidos_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="apellidos_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][13]==1): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_nacimiento');" class="py-0 px-1 font-size-11" id="fecha_nacimiento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="fecha_nacimiento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php endif; ?>
                                          <?php elseif ($resultado_registros[$i][13]==''): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_nacimiento');" class="py-0 px-1 font-size-11" id="fecha_nacimiento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="fecha_nacimiento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][15]==1): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_expedicion');" class="py-0 px-1 font-size-11" id="fecha_expedicion_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="fecha_expedicion_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php endif; ?>
                                          <?php elseif ($resultado_registros[$i][15]==''): ?>
                                            <?php if($control_sr==0): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_expedicion');" class="py-0 px-1 font-size-11" id="fecha_expedicion_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php else: ?>
                                              <a href="#" class="py-0 px-1 font-size-11" id="fecha_expedicion_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][27]; ?></td>
                                  </tr>
                                <?php endif; ?>
                                <?php endfor; ?>
                              </tbody>
                          </table>
                      </div>
                      <?php if($control_sr>0): ?>
                        <div class="table-responsive mt-1">
                          <table class="table table-bordered table-striped table-hover table-sm">
                              <thead>
                                  <tr>
                                    <td colspan="17" class="p-1 alert">Segunda Revisión OCR</td>
                                  </tr>
                                  <tr>
                                      <th class="px-1 py-2">Cód. Beneficiario</th>
                                      <th class="px-1 py-2">Cabeza Familia</th>
                                      <th class="px-1 py-2">Documento</th>
                                      <th class="px-1 py-2">Nombres y Apellidos</th>
                                      <th class="px-1 py-2">Estado</th>
                                      <th class="px-1 py-2">Novedad</th>
                                      <th class="px-1 py-2">Contrato Existe</th>
                                      <th class="px-1 py-2">Contrato Nombre Titular</th>
                                      <th class="px-1 py-2">Contrato Firma</th>
                                      <th class="px-1 py-2">Contrato Huella</th>
                                      <th class="px-1 py-2">Documento</th>
                                      <th class="px-1 py-2">Nombres</th>
                                      <th class="px-1 py-2">Apellidos</th>
                                      <th class="px-1 py-2">Fecha Nacimiento</th>
                                      <th class="px-1 py-2">Fecha Expedición</th>
                                      <th class="px-1 py-2">Fecha Registro</th>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                                  <?php if($resultado_registros[$i][25]=='No validado-OCR-SR' OR $resultado_registros[$i][25]=='Validado-OCR-SR' OR $resultado_registros[$i][25]=='Validado-Agente-SR' OR $resultado_registros[$i][25]=='Validado-Edad-SR'): ?>
                                  <tr>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][2]; ?></td>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][3]; ?></td>
                                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][32]; ?></td>
                                      <td class="p-1 font-size-11">
                                          <?php echo $resultado_registros[$i][28]; ?>
                                          <?php echo ($resultado_registros[$i][29]!="") ? ' '.$resultado_registros[$i][29] : ''; ?>
                                          <?php echo ($resultado_registros[$i][30]!="") ? ' '.$resultado_registros[$i][30] : ''; ?>
                                          <?php echo ($resultado_registros[$i][31]!="") ? ' '.$resultado_registros[$i][31] : ''; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][25]; ?></td>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][26]; ?></td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                            <?php if ($resultado_registros[$i][17]==1): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_existe');" class="py-0 px-1 font-size-11" id="contrato_existe_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php elseif ($resultado_registros[$i][17]==''): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_existe');" class="py-0 px-1 font-size-11" id="contrato_existe_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                            <?php if ($resultado_registros[$i][19]!='' AND $resultado_registros[$i][19]!='NA'): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_nombre');" class="py-0 px-1 font-size-11" id="contrato_nombre_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php elseif ($resultado_registros[$i][19]==''): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_nombre');" class="py-0 px-1 font-size-11" id="contrato_nombre_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                            <?php if ($resultado_registros[$i][22]==1): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_firma');" class="py-0 px-1 font-size-11" id="contrato_firma_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php elseif ($resultado_registros[$i][22]==''): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_firma');" class="py-0 px-1 font-size-11" id="contrato_firma_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                                            <?php if ($resultado_registros[$i][23]==1): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_huella');" class="py-0 px-1 font-size-11" id="contrato_huella_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                            <?php elseif ($resultado_registros[$i][23]==''): ?>
                                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_huella');" class="py-0 px-1 font-size-11" id="contrato_huella_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                            <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][6]==1): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'documento');" class="py-0 px-1 font-size-11" id="documento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                          <?php elseif ($resultado_registros[$i][6]==''): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'documento');" class="py-0 px-1 font-size-11" id="documento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][9]==1): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'nombres');" class="py-0 px-1 font-size-11" id="nombres_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                          <?php elseif ($resultado_registros[$i][9]==''): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'nombres');" class="py-0 px-1 font-size-11" id="nombres_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][11]==1): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'apellidos');" class="py-0 px-1 font-size-11" id="apellidos_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                          <?php elseif ($resultado_registros[$i][11]==''): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'apellidos');" class="py-0 px-1 font-size-11" id="apellidos_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][13]==1): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_nacimiento');" class="py-0 px-1 font-size-11" id="fecha_nacimiento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                          <?php elseif ($resultado_registros[$i][13]==''): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_nacimiento');" class="py-0 px-1 font-size-11" id="fecha_nacimiento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center">
                                          <?php if ($resultado_registros[$i][15]==1): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_expedicion');" class="py-0 px-1 font-size-11" id="fecha_expedicion_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                                          <?php elseif ($resultado_registros[$i][15]==''): ?>
                                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_expedicion');" class="py-0 px-1 font-size-11" id="fecha_expedicion_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                                          <?php endif; ?>
                                      </td>
                                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][27]; ?></td>
                                  </tr>
                                <?php endif; ?>
                                <?php endfor; ?>
                              </tbody>
                          </table>
                      </div>
                      <?php endif; ?>
                      <div class="d-sm-flex justify-content-between align-items-start mt-2">
                        <div class="col-md-12">
                          <p class="alert background-principal color-blanco py-1 px-2 my-0"><span class="fas fa-address-book"></span> Contactos</p>
                        </div>
                      </div>
                      <div class="table-responsive mt-0">
                        <table class="table select-table">
                          <tbody>
                            <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                            <tr>
                              <td>
                                <div>
                                  <div>
                                    <h6>
                                      <span class="fas fa-user"></span> <?php echo $resultado_registros[$i][28]; ?>
                                      <?php echo ($resultado_registros[$i][29]!="") ? ' '.$resultado_registros[$i][29] : ''; ?>
                                      <?php echo ($resultado_registros[$i][30]!="") ? ' '.$resultado_registros[$i][30] : ''; ?>
                                      <?php echo ($resultado_registros[$i][31]!="") ? ' '.$resultado_registros[$i][31] : ''; ?>
                                    </h6>
                                    <?php if($resultado_registros[$i][36]!="" OR $resultado_registros[$i][37]!="" OR $resultado_registros[$i][40]!=""): ?>
                                      <p>
                                        <?php if($resultado_registros[$i][36]!="" OR $resultado_registros[$i][37]!=""): ?>
                                          <span class="fas fa-mobile me-1"></span>Cel: <?php echo ($resultado_registros[$i][37]!="") ? $resultado_registros[$i][37] : 'No registra'; ?>
                                           | <span class="fas fa-phone me-1"></span>Tel: <?php echo ($resultado_registros[$i][36]!="") ? $resultado_registros[$i][36] : 'No registra'; ?>
                                        <?php endif; ?>
                                          <br><span class="fas fa-envelope me-1"></span><?php echo ($resultado_registros[$i][40]!="") ? $resultado_registros[$i][40] : 'No registra'; ?>
                                      </p>
                                      
                                    <?php else: ?>
                                      <p class="alert alert-warning p-1 font-size-11">¡No se encontraron datos de contacto!</p>
                                    <?php endif; ?>
                                    <?php if($resultado_registros[$i][3]=='SI' AND $control_cabeza==0): ?>
                                      <?php
                                        $control_cabeza=1;
                                        if ($resultado_registros[$i][40]!="") {
                                          $tiene_correo=1;
                                        } else {
                                          $tiene_correo=0;
                                        }

                                        if ($resultado_registros[$i][37]!="" AND strlen($resultado_registros[$i][37])==10) {
                                          $tiene_celular=1;
                                        } else {
                                          $tiene_celular=0;
                                        }
                                      ?>
                                      <div class="row m-0">
                                        <div class="form-group col-md-3 my-0">
                                          <label for="celular" class="my-0">Celular</label>
                                          <input type="text" class="form-control form-control-sm" name="celular" id="celular" minlength="10" maxlength="10" value="" autocomplete="off">
                                        </div>
                                        <div class="form-group col-md-3 my-0">
                                          <label for="telefono" class="my-0">Teléfono</label>
                                          <input type="text" class="form-control form-control-sm" name="telefono" id="telefono" minlength="10" maxlength="10" value="" autocomplete="off">
                                        </div>
                                        <div class="form-group col-md-6 my-0">
                                          <label for="correo_cabezafamilia" class="my-0">Correo</label>
                                          <input type="email" class="form-control form-control-sm" name="correo_cabezafamilia" id="correo_cabezafamilia" maxlength="100" value="" autocomplete="off">
                                        </div>
                                      </div>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </td>
                            </tr>
                            <?php endfor; ?>
                          </tbody>
                        </table>
                      </div>
                      <?php if($control_sr>0 AND ($resultado_registros_caso[0][6]=='Documentos Cargados-Segunda Revisión' OR $resultado_registros_caso[0][6]=='Aplazado Tercera Revisión' OR $resultado_registros_caso[0][6]=='Validado-Agente-Tercera Revisión')): ?>
                        <div class="d-sm-flex justify-content-between align-items-start mt-2">
                          <div class="col-md-12">
                            <p class="alert background-principal color-blanco py-1 px-2 my-0"><span class="fas fa-file-pdf"></span> Documentos Tercera Revisión</p>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <embed src="storage_adjuntos/TR-<?php echo $resultado_registros_caso[0][1]; ?>.pdf?ran=<?php echo generar_codigo(5); ?>#zoom=100" id="visor" style="width: 100%; min-height: 450px;">
                        </div>
                      <?php endif; ?>
                      <?php if($control_sr>0): ?>
                        <div class="d-sm-flex justify-content-between align-items-start mt-2">
                          <div class="col-md-12">
                            <p class="alert background-principal color-blanco py-1 px-2 my-0"><span class="fas fa-file-pdf"></span> Documentos Segunda Revisión</p>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <embed src="storage_adjuntos/<?php echo $resultado_registros_caso[0][1]; ?>.pdf?ran=<?php echo generar_codigo(5); ?>#zoom=100" id="visor" style="width: 100%; min-height: 450px;">
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <?php if($estado_sms): ?>
                        <p class="alert alert-success p-1 font-size-11">¡Se programó notificación automática SMS al celular registrado!</p>
                      <?php endif; ?>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="cod_familia" class="my-0">Cód. Familia</label>
                            <input type="text" class="form-control form-control-sm" name="cod_familia" id="cod_familia" maxlength="100" value="<?php echo $resultado_registros_caso[0][1]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion']==1) { echo 'readonly'; } ?> required readonly autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="estado" class="my-0">Estado</label>
                              <select class="form-control form-control-sm form-select" name="estado" id="estado" <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion']==1) { echo 'disabled'; } ?> required onchange="validar_estado();">
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <!-- PRIMERA FASE -->
                                  <?php if($control_sr==0): ?>
                                    <option value="Pendiente llamada" <?php if($resultado_registros_caso[0][6]=="Pendiente llamada"){ echo "selected"; } ?>>Pendiente llamada</option>
                                    <option value="Contactado-Pendiente Documentos" <?php if($resultado_registros_caso[0][6]=="Contactado-Pendiente Documentos"){ echo "selected"; } ?>>Contactado-Pendiente Documentos</option>
                                    <option value="Intento Contacto-Fallido" <?php if($resultado_registros_caso[0][6]=="Intento Contacto-Fallido"){ echo "selected"; } ?>>Intento Contacto-Fallido</option>
                                    <option value="Escalado-Validar" <?php if($resultado_registros_caso[0][6]=="Escalado-Validar"){ echo "selected"; } ?>>Escalado-Validar</option>
                                    <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Supervisor"): ?>
                                      <option value="Escalado-Cliente" <?php if($resultado_registros_caso[0][6]=="Escalado-Cliente"){ echo "selected"; } ?>>Escalado-Cliente</option>
                                      <option value="Aplazado" <?php if($resultado_registros_caso[0][6]=="Aplazado"){ echo "selected"; } ?>>Aplazado</option>
                                    <?php endif; ?>
                                    <option value="Validado-Agente" <?php if($resultado_registros_caso[0][6]=="Validado-Agente"){ echo "selected"; } ?>>Validado-Agente</option>
                                    <option value="Inscrito SIFA" <?php if($resultado_registros_caso[0][6]=="Inscrito SIFA"){ echo "selected"; } ?>>Inscrito SIFA</option>
                                  <?php endif; ?>

                                  <!-- SEGUNDA FASE -->
                                  
                                  <?php if($control_sr>0): ?>
                                    <?php if($resultado_registros_caso[0][6]=="Documentos Cargados-Segunda Revisión" OR $resultado_registros_caso[0][6]=="Aplazado Tercera Revisión" OR $resultado_registros_caso[0][6]=="Validado-Agente-Tercera Revisión"): ?>
                                      <option value="Aplazado Tercera Revisión" <?php if($resultado_registros_caso[0][6]=="Aplazado Tercera Revisión"){ echo "selected"; } ?>>Aplazado Tercera Revisión</option>
                                      <option value="Validado-Agente-Tercera Revisión" <?php if($resultado_registros_caso[0][6]=="Validado-Agente-Tercera Revisión"){ echo "selected"; } ?>>Validado-Agente-Tercera Revisión</option>
                                    <?php else: ?>
                                      <?php if($resultado_registros_caso[0][6]=="Pendiente llamada-Segunda Revisión" OR $resultado_registros_caso[0][6]=="Aplazado Segunda Revisión"): ?>
                                        <option value="Pendiente llamada-Segunda Revisión" <?php if($resultado_registros_caso[0][6]=="Pendiente llamada-Segunda Revisión"){ echo "selected"; } ?>>Pendiente   llamada-Segunda Revisión</option>
                                      <?php endif; ?>
                                      <?php if($resultado_registros_caso[0][6]=="Intento Contacto-Fallido-Segunda Revisión" OR $resultado_registros_caso[0][6]=="Pendiente llamada-Segunda Revisión"  OR $resultado_registros_caso[0][6]=="Aplazado Segunda Revisión"): ?>
                                        <option value="Intento Contacto-Fallido-Segunda Revisión" <?php if($resultado_registros_caso[0][6]=="Intento Contacto-Fallido-Segunda Revisión"){ echo "selected"; } ?>>Intento Contacto-Fallido-Segunda Revisión</option>
                                        <option value="Contactado-Pendiente Documentos-Segunda Revisión" <?php if($resultado_registros_caso[0][6]=="Contactado-Pendiente Documentos-Segunda Revisión"){ echo "selected"; } ?>>Contactado-Pendiente Documentos-Segunda Revisión</option>
                                        <option value="Escalado-Validar-Segunda Revisión" <?php if($resultado_registros_caso[0][6]=="Escalado-Validar-Segunda Revisión"){ echo "selected"; } ?>>Escalado-Validar-Segunda Revisión</option>
                                      <?php endif; ?>

                                      <?php if($resultado_registros_caso[0][6]=="Aplazado Segunda Revisión"): ?>
                                        <option value="Validado-Agente-Segunda Revisión" <?php if($resultado_registros_caso[0][6]=="Validado-Agente-Segunda Revisión"){ echo "selected"; } ?>>Validado-Agente-Segunda Revisión</option>
                                      <?php endif; ?>
                                      
                                      <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Supervisor"): ?>
                                        <option value="Escalado-Cliente-Segunda Revisión" <?php if($resultado_registros_caso[0][6]=="Escalado-Cliente-Segunda Revisión"){ echo "selected"; } ?>>Escalado-Cliente-Segunda Revisión</option>
                                        <option value="Aplazado Segunda Revisión" <?php if($resultado_registros_caso[0][6]=="Aplazado Segunda Revisión"){ echo "selected"; } ?>>Aplazado Segunda Revisión</option>
                                      <?php endif; ?>
                                    <?php endif; ?>
                                  <?php endif; ?>

                              </select>
                          </div>
                      </div>
                      <?php if($tiene_celular): ?>
                        <p class="alert alert-warning p-1 font-size-11 d-none" id="mensaje_sms_1_div">¡Se enviará notificación automática al número celular registrado!</p>
                      <?php endif; ?>
                      <?php if($tiene_correo): ?>
                        <p class="alert alert-warning p-1 font-size-11 d-none" id="mensaje_correo_1_div">¡Se enviará notificación automática al correo registrado!</p>
                      <?php endif; ?>
                      <div class="col-md-12 d-none" id="suministra_correo_div">
                          <div class="form-group">
                              <label for="suministra_correo" class="my-0">Beneficiario suministra correo?</label>
                              <select class="form-control form-control-sm form-select" name="suministra_correo" id="suministra_correo" <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion']==1) { echo 'disabled'; } ?> disabled required onchange="validar_correo();">
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <option value="Si" <?php if($resultado_registros_caso[0][6]=="Si"){ echo "selected"; } ?>>Si</option>
                                  <option value="No" <?php if($resultado_registros_caso[0][6]=="No"){ echo "selected"; } ?>>No</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="correo_div">
                          <div class="form-group">
                            <label for="correo" class="my-0">Correo</label>
                            <input type="email" class="form-control form-control-sm" name="correo" id="correo" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $correo; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion']==1) { echo 'readonly'; } ?> required disabled autocomplete="off">
                            <p class="alert alert-warning p-1 font-size-11 my-1" id="mensaje_correo_div">¡Se enviará notificación automática al correo suministrado!</p>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                            <label for="observaciones" class="my-0">Observaciones</label>
                            <textarea class="form-control form-control-sm font-size-11 height-100" name="observaciones" id="observaciones" <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion']==1) { echo 'readonly'; } ?> required><?php if(isset($_POST["guardar_registro"])){ echo $observaciones; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12 d-none" id="tipificacion_div">
                          <div class="form-group">
                              <label for="tipificacion" class="my-0">Tipificación llamada</label>
                              <select class="form-control form-control-sm form-select" name="tipificacion" id="tipificacion" <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion']==1) { echo 'disabled'; } ?> required disabled>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  
                              </select>
                          </div>
                          <div class="form-group">
                            <label for="id_llamada" class="my-0">Id llamada</label>
                            <input type="text" class="form-control form-control-sm" name="id_llamada" id="id_llamada" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $id_llamada; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion']==1) { echo 'readonly'; } ?> required disabled autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_familias_accion']==1): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Guardar</button>
                                  <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');">Cancelar</button>
                              <?php endif; ?>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
        <!-- content-wrapper ends -->
        <!-- MODAL estado -->
        <div class="modal fade" id="modal-estado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Cambiar estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body-estado">
                
              </div>
              <div class="modal-footer">
              </div>
            </div>
          </div>
        </div>
        <!-- MODAL estado -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
      function validar_estado(){
          var estado_opcion = document.getElementById("estado");
          var estado = estado_opcion.options[estado_opcion.selectedIndex].value;
          var tiene_correo = <?php echo $tiene_correo; ?>;
          var tiene_celular = <?php echo $tiene_celular; ?>;
          var suministra_correo = document.getElementById('suministra_correo').disabled=true;
          document.getElementById('suministra_correo').value='';
          $("#suministra_correo_div").removeClass('d-block').addClass('d-none');
          $("#mensaje_correo_1_div").removeClass('d-block').addClass('d-none');
          $("#mensaje_sms_1_div").removeClass('d-block').addClass('d-none');

          var tipificacion = document.getElementById('tipificacion').disabled=true;
          var id_llamada = document.getElementById('id_llamada').disabled=true;
          document.getElementById('tipificacion').value='';
          $("#tipificacion_div").removeClass('d-block').addClass('d-none');

          if(estado=="Contactado-Pendiente Documentos" || estado=="Contactado-Pendiente Documentos-Segunda Revisión") {
              var suministra_correo = document.getElementById('suministra_correo').disabled=false;
              $("#suministra_correo_div").removeClass('d-none').addClass('d-block');
          } else if((estado=="Pendiente llamada" || estado=="Pendiente llamada-Segunda Revisión") & tiene_correo=='1') {
              $("#mensaje_correo_1_div").removeClass('d-none').addClass('d-block');
          }

          if((estado=="Contactado-Pendiente Documentos" || estado=="Contactado-Pendiente Documentos-Segunda Revisión") & tiene_celular=='1') {
              $("#mensaje_sms_1_div").removeClass('d-none').addClass('d-block');
          }

          if((estado=="Contactado-Pendiente Documentos" || estado=="Contactado-Pendiente Documentos-Segunda Revisión") & tiene_celular=='0') {
              document.getElementById('celular').required=true;
          } else {
              document.getElementById('celular').required=false;
          }

          if(estado=="Contactado-Pendiente Documentos" || estado=="Contactado-Pendiente Documentos-Segunda Revisión"){
            $("#tipificacion").html('<option class="font-size-11" value="">Seleccione</option>\
              <option value="LINK RECIBIDO">LINK RECIBIDO</option>\
              <option value="SOLICITUD DE LINK">SOLICITUD DE LINK</option>\
              <option value="SE REMITE A ENLACE MUNICIPAL">SE REMITE A ENLACE MUNICIPAL</option>');
            var tipificacion = document.getElementById('tipificacion').disabled=false;
            var id_llamada = document.getElementById('id_llamada').disabled=false;
            $("#tipificacion_div").removeClass('d-none').addClass('d-block');
          } else if(estado=="Intento Contacto-Fallido" || estado=="Intento Contacto-Fallido-Segunda Revisión"){
            $("#tipificacion").html('<option class="font-size-11" value="">Seleccione</option>\
              <option value="NO ATIENDEN LLAMADA">NO ATIENDEN LLAMADA</option>\
              <option value="CONTACTO CON UN TERCERO">CONTACTO CON UN TERCERO</option>\
              <option value="SE CORTA O CAE LLAMADA">SE CORTA O CAE LLAMADA</option>\
              <option value="LLAMADA MUDA">LLAMADA MUDA</option>\
              <option value="NUMERO ERRADO">NUMERO ERRADO</option>\
              <option value="NO CONTESTA">NO CONTESTA</option>\
              <option value="SIN NÚMERO DE CONTACTO, SOLO CORREO">SIN NÚMERO DE CONTACTO, SOLO CORREO</option>');
            var tipificacion = document.getElementById('tipificacion').disabled=false;
            var id_llamada = document.getElementById('id_llamada').disabled=false;
            $("#tipificacion_div").removeClass('d-none').addClass('d-block');
          } else if(estado=="Nuevo Contacto-Error Subsanación"){
            $("#tipificacion").html('<option class="font-size-11" value="">Seleccione</option>\
              <option value="LINK RECIBIDO">LINK RECIBIDO</option>\
              <option value="SOLICITUD DE LINK">SOLICITUD DE LINK</option>\
              <option value="NO ATIENDEN LLAMADA">NO ATIENDEN LLAMADA</option>\
              <option value="CONTACTO CON UN TERCERO">CONTACTO CON UN TERCERO</option>\
              <option value="SE CORTA O CAE LLAMADA">SE CORTA O CAE LLAMADA</option>\
              <option value="LLAMADA MUDA">LLAMADA MUDA</option>\
              <option value="NUMERO ERRADO">NUMERO ERRADO</option>\
              <option value="NO CONTESTA">NO CONTESTA</option>\
              <option value="CAMBIO DE TITULAR">CAMBIO DE TITULAR</option>\
              <option value="DESISTE DE LA SOLICITUD">DESISTE DE LA SOLICITUD</option>\
              <option value="VALIDACION SUPERVISOR">VALIDACION SUPERVISOR</option>');
            var tipificacion = document.getElementById('tipificacion').disabled=false;
            var id_llamada = document.getElementById('id_llamada').disabled=false;
            $("#tipificacion_div").removeClass('d-none').addClass('d-block');
          } else if(estado=="Escalado-Validar" || estado=="Escalado-Validar-Segunda Revisión"){
            $("#tipificacion").html('<option class="font-size-11" value="">Seleccione</option>\
              <option value="CAMBIO DE TITULAR">CAMBIO DE TITULAR</option>\
              <option value="DESISTE DE LA SOLICITUD">DESISTE DE LA SOLICITUD</option>\
              <option value="VALIDACION SUPERVISOR">VALIDACION SUPERVISOR</option>');
            var tipificacion = document.getElementById('tipificacion').disabled=false;
            var id_llamada = document.getElementById('id_llamada').disabled=false;
            $("#tipificacion_div").removeClass('d-none').addClass('d-block');
          } else {
            document.getElementById('tipificacion').value='';
          }

          validar_correo();
      }

      function validar_correo(){
          var suministra_correo_opcion = document.getElementById("suministra_correo");
          var suministra_correo = suministra_correo_opcion.options[suministra_correo_opcion.selectedIndex].value;

          if(suministra_correo=="Si") {
              var correo = document.getElementById('correo').disabled=false;
              $("#correo_div").removeClass('d-none').addClass('d-block');
          } else {
              var correo = document.getElementById('correo').disabled=true;
              document.getElementById('correo').value='';
              $("#correo_div").removeClass('d-block').addClass('d-none');
          }
      }
      validar_estado();

      function open_modal_editar(id_registro, beneficiario, item) {
          var myModal = new bootstrap.Modal(document.getElementById("modal-estado"), {});
          $('.modal-body-estado').load('familias_accion_editar_estado.php?reg='+id_registro+'&beneficiario='+beneficiario+'&item='+item,function(){
              myModal.show();
          });
      }
  </script>
</body>
</html>