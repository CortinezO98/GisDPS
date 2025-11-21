<?php
    $array_colores_turnos['turno']='#1E8449';
    $array_colores_turnos['almuerzo']='#2874A6';
    $array_colores_turnos['break']='#F1C40F';
    $array_colores_turnos['pausaactiva']='#B03A2E';
    $array_colores_turnos['capacitacion']='#6C3483';
    $array_colores_turnos['retroalimentacion']='#1ABC9C';

    $array_iconos_turnos['turno']='user-clock';
    $array_iconos_turnos['almuerzo']='utensils';
    $array_iconos_turnos['break']='coffee';
    $array_iconos_turnos['pausaactiva']='walking';
    $array_iconos_turnos['capacitacion']='chalkboard-teacher';
    $array_iconos_turnos['retroalimentacion']='retweet';

    $array_nombres_turnos['turno']='Turno';
    $array_nombres_turnos['almuerzo']='Almuerzo';
    $array_nombres_turnos['break']='Break';
    $array_nombres_turnos['pausaactiva']='Pausa Activa';
    $array_nombres_turnos['capacitacion']='Capacitación';
    $array_nombres_turnos['retroalimentacion']='Retroalimentación';

    //Arrays
    $array_dias = array(1 => "Lu", 2 => "Ma", 3 => "Mi", 4 => "Ju", 5 => "Vi", 6 => "Sá", 0 => "Do");
    $array_dias_nombre = array(1 => "Lunes", 2 => "Martes", 3 => "Miércoles", 4 => "Jueves", 5 => "Viernes", 6 => "Sábado", 7 => "Domingo");
    $array_mes = array(1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre");
    $array_meses=[1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril", 5=>"Mayo", 6=>"Junio", 7=>"Julio", 8=>"Agosto", 9=>"Septiembre", 10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre"];
    $array_mes_min = array(1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Ago", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic");

    //validaciones de seguridad
    function validar_input($variable) {
      $variable = trim((string)$variable);
      $variable = strip_tags($variable);
      $variable = stripslashes($variable);
      $variable = htmlspecialchars($variable);
      $variable = str_replace("'", "", $variable);
      $variable = str_replace(" ", "", $variable);
      return $variable;
    }

    function validar_output($variable) {
      $variable = trim((string)$variable);
      $variable = strip_tags($variable);
      $variable = stripslashes($variable);
      $variable = htmlspecialchars($variable);
      $variable = str_replace("'", "", $variable);
      return $variable;
    }

    function removeEmojis($string) {
      $string = str_replace("?", "{%}", $string);
      $string = mb_convert_encoding($string, "ISO-8859-1", "UTF-8");
      $string = mb_convert_encoding($string, "UTF-8", "ISO-8859-1");
      $string = str_replace(array("?", "? ", " ?"), array(""), $string);
      $string = str_replace("{%}", "?", $string);
      $string = str_replace("&lt;", "[", $string);
      $string = str_replace("&gt;", "]", $string);
      return trim($string);
    }

    function convertirPeso($bytes) {
      $kilobytes = $bytes / 1024;
      $megabytes = $kilobytes / 1024;

      if ($megabytes >= 1) {
          return number_format($megabytes, 2) . ' MB';
      } elseif ($kilobytes >= 1) {
          return number_format($kilobytes, 2) . ' KB';
      } else {
          return $bytes . ' Bytes';
      }
    }

    function recortarTexto($texto, $maxCaracteres) {
      // Verifica si la longitud del texto es mayor que el límite
      if (strlen($texto) > $maxCaracteres) {
          // Recorta el texto y agrega puntos suspensivos
          $textoRecortado = substr($texto, 0, $maxCaracteres) . '...';
          return $textoRecortado;
      } else {
          // Devuelve el texto sin cambios si no supera el límite
          return $texto;
      }
    }

    function quitarCaracteresDeCorreoElectronico($cadena) {
        // Utilizamos una expresión regular para encontrar y reemplazar las direcciones de correo electrónico
        // entre < y > con los caracteres < y >
        return preg_replace_callback('/<([^<>]+)>/', function ($matches) {
            // Verificamos si lo que está entre < y > parece ser una dirección de correo electrónico
            if (filter_var($matches[1], FILTER_VALIDATE_EMAIL)) {
                // Si es una dirección de correo electrónico, reemplazamos < y > por espacios en blanco
                return str_replace(['<', '>'], ' ', $matches[0]);
            } else {
                // Si no es una dirección de correo electrónico, dejamos la etiqueta tal como está
                return $matches[0];
            }
        }, $cadena);
    }


    function comprobarSentencia ($valor) {
        preg_match_all('/(\S[^:]+): (\d+)/', $valor, $matches); 
        $array_info = array_combine ($matches[1], $matches[2]);

        if ($array_info['Rows matched']==1 AND $array_info['Warnings']==0) {
            return true;
        } else {
            return false;
        }
    }

    //Función para obtener día festivo
    function validarFestivo($fecha) {
        $validarFestivo = new festivos();
        $dia_validar = date('d', strtotime($fecha));
        $mes_validar = date('m', strtotime($fecha));
        $anio_validar = date('Y', strtotime($fecha));
        $dia_semana = date('w', strtotime($fecha));
        $validarFestivo->festivos($anio_validar);
        $festivo = "";
        if ($validarFestivo->esFestivo($dia_validar, $mes_validar)||$dia_semana==0) {
            $festivo = "festivo_domingo";
        }
        return $festivo;
    }

    function validar_fecha_unix($fecha){
        if ($fecha!="") {
            $unix_date = (floatval($fecha) - 25569) * 86400;
            
            return gmdate("Y-m-d H:i:s", $unix_date);
        } else {
            return "";
        }
            
    }

    function includeFileContent($fileName) {
        ob_start();
        ob_implicit_flush(false);
        include($fileName);
        return ob_get_clean();
    }

    function validar_cero($dato) { 
        if (iconv_strlen($dato)==1) {
          $dato_final="0".$dato;
        } else {
          $dato_final=$dato;
        } 
        return $dato_final; 
    }

    function eliminardobleSalto($cadena) {
        $patron = "#(<br />((\r)*)((\n)*))+#";//Patrón de búsqueda, que mediante expresión regular busca varios saltos seguidos
        $sustituto = "<br />";//sustituye por un solo salto
        $cadenasalida=preg_replace ($patron,$sustituto,$cadena);
 
        return $cadenasalida;
    }

    function generar_token($longitud=20) {
      return bin2hex(random_bytes(($longitud - ($longitud % 2)) / 2));
    }

    function generatePassword($length) {
        $key = "";
        $pattern = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_*@()";
        $max = strlen($pattern)-1;
        for($i = 0; $i < $length; $i++){
            $key .= substr($pattern, mt_rand(0,$max), 1);
        }
        return $key;
    }

    function notificacion($enlace_db, $asunto, $referencia, $contenido, $nc_address, $nc_id_modulo="1", $nc_cc="", $nc_id_set_from="1", $nc_prioridad="Alta") {
        // PROGRAMAR NOTIFICACIÓN CORREO
          /*SE ESTRUCTURA COTENIDO DE CORREO*/
              $contenido_correo="<center><table style='width:100%; max-width: 600px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                      <tr>
                          <td style='padding: 5px 5px 5px 5px;'><img src='cid:logo' style='width: 80px;'></img></td>
                          <td style='padding: 5px 5px 5px 5px; text-align: right;'><img src='cid:logo_notificacion_correo' style='width: 120px;'></td>
                      </tr>
                  </table>
                  <table style='width:100%; max-width: 600px; font-family: Lato, Arial, sans-serif;'>
                      <tr>
                          <td style='padding: 5px 5px 5px 5px;'>
                              <p style='font-size: 13px;padding: 0px 5px 0px 5px;color: #666666;'><b>".$referencia."</b></p>
                              ".$contenido."
                              <br>
                              <center>
                                  <a href='".URL."' target='_blank' style='border-radius:4px; color:#ffffff; font-size:12px; padding: 5px 5px 5px 5px; text-align:center; text-decoration:none !important; width:50%; display: block; background-color: ".COLOR_PRINCIPAL."'>Ir a ".APP_NAME."</a>
                              </center>
                              <br>
                          </td>
                      </tr>
                  </table>
                  <table style='width: 100%; max-width: 600px; background: #666666; font-family: Lato, Arial, sans-serif;'>
                      <tr>
                          <td style='font-size: 12px; padding: 5px 10px 5px 10px; color: #FFFFFF'>
                              <center>".APP_NAME." | ".APP_NAME_ALL." | &copy; Copyright 2022-".date('Y')." Todos los derechos reservados.</center>
                          </td>
                      </tr>
                  </table>
                  <table style='width:100%; max-width: 600px; font-family: Lato, Arial, sans-serif;'>
                      <tr>
                          <td style='padding: 5px 5px 5px 5px;'>
                              <center>
                                  <p style='font-size: 12px;font-family: Lato, Arial, sans-serif; color: #666666;'>Te recordamos que este correo electrónico es utilizado solamente para el envío de notificaciones automáticas.
                                      <br>Por favor no respondas con consultas personales ya que no podrán ser respondidas.
                                  </p>
                              </center>
                          </td>
                      </tr>
                  </table>
                  <center>
                      <table style='max-width: 600px; font-family: Lato, Arial, sans-serif;'>
                          <tr>
                              <td style='width: 45px;'><img src='cid:firma-verde' style='margin: 5px; width: 45px;'></td>
                              <td>
                                  <p style='font-size: 11px; color: #196F3D; font-family: Lato, Arial, sans-serif;'><br>No imprima este mensaje de no ser necesario;<br>de ésta manera aportamos al cuidado del planeta.</p>
                              </td>
                          </tr>
                      </table>
                  </center>
              </center>";
          /*SE ESTRUCTURA COTENIDO DE CORREO*/

          /*SE CONFIGURAN PARÁMETROS A REGISTRAR EN SISTEMA DE NOTIFICACIÓN*/
          $nc_bcc="";
          $nc_reply_to="";
          $nc_subject=$asunto." - ".APP_NAME." | ".APP_NAME_ALL;
          $nc_body=str_replace("'", '"', $contenido_correo);
          $nc_embeddedimage_ruta="".IMAGES_ROOT."firma-verde.png;".LOGO_ENTIDAD_ROOT.";".IMAGES_ROOT."logo_notificacion_correo.png";
          $nc_embeddedimage_nombre="firma-verde;logo;logo_notificacion_correo";
          $nc_embeddedimage_tipo="image/png;image/png;image/png";
          $nc_intentos="";
          $nc_eliminar="Si";
          $nc_estado_envio="Pendiente";
          $nc_fecha_envio="";
          $nc_usuario_registro=$_SESSION[APP_SESSION.'_session_usu_id'];

          $verifica_notificacion=0;
          for ($i=0; $i < 10; $i++) {
            $consulta_notificacion = mysqli_query($enlace_db, "INSERT INTO `administrador_notificaciones`(`nc_id_modulo`, `nc_prioridad`, `nc_id_set_from`, `nc_address`, `nc_cc`, `nc_bcc`, `nc_reply_to`, `nc_subject`, `nc_body`, `nc_embeddedimage_ruta`, `nc_embeddedimage_nombre`, `nc_embeddedimage_tipo`, `nc_intentos`, `nc_eliminar`, `nc_estado_envio`, `nc_fecha_envio`, `nc_usuario_registro`) VALUES ('".$nc_id_modulo."','".$nc_prioridad."','".$nc_id_set_from."','".$nc_address."','".$nc_cc."','".$nc_bcc."','".$nc_reply_to."','".$nc_subject."','".$nc_body."','".$nc_embeddedimage_ruta."','".$nc_embeddedimage_nombre."','".$nc_embeddedimage_tipo."','".$nc_intentos."','".$nc_eliminar."','".$nc_estado_envio."','".$nc_fecha_envio."','".$nc_usuario_registro."');");

              if ($consulta_notificacion) {
                  registro_log($enlace_db, 'Notificación', 'notificacion', 'Notificación programada '.$nc_subject);
                  return true;
                  break;
              }
          }
          return false;
    }

    function notificacion_familias($enlace_db, $asunto, $referencia, $contenido, $nc_address, $nc_id_modulo="1", $nc_cc="", $nc_id_set_from="2", $nc_prioridad="Alta") {
        // PROGRAMAR NOTIFICACIÓN CORREO
          /*SE ESTRUCTURA COTENIDO DE CORREO*/
              $contenido_correo="<center>
                  <table style='width:100%; max-width: 600px; font-family: Lato, Arial, sans-serif;'>
                      <tr>
                          <td style='padding: 5px 5px 5px 5px;'>
                              ".$contenido."
                              <br>
                          </td>
                      </tr>
                  </table>
              </center>";
          /*SE ESTRUCTURA COTENIDO DE CORREO*/

          /*SE CONFIGURAN PARÁMETROS A REGISTRAR EN SISTEMA DE NOTIFICACIÓN*/
          $nc_bcc="";
          $nc_reply_to="";
          $nc_subject=$asunto;
          $nc_body=str_replace("'", '"', $contenido_correo);
          $nc_embeddedimage_ruta="".IMAGES_ROOT."gestion_ocr/firma-dps.png";
          $nc_embeddedimage_nombre="firma-dps";
          $nc_embeddedimage_tipo="image/png";
          $nc_intentos="";
          $nc_eliminar="Si";
          $nc_estado_envio="Pendiente";
          $nc_fecha_envio="";
          $nc_usuario_registro=$_SESSION[APP_SESSION.'_session_usu_id'];

          $verifica_notificacion=0;
          for ($i=0; $i < 10; $i++) {
            $consulta_notificacion = mysqli_query($enlace_db, "INSERT INTO `administrador_notificaciones`(`nc_id_modulo`, `nc_prioridad`, `nc_id_set_from`, `nc_address`, `nc_cc`, `nc_bcc`, `nc_reply_to`, `nc_subject`, `nc_body`, `nc_embeddedimage_ruta`, `nc_embeddedimage_nombre`, `nc_embeddedimage_tipo`, `nc_intentos`, `nc_eliminar`, `nc_estado_envio`, `nc_fecha_envio`, `nc_usuario_registro`) VALUES ('".$nc_id_modulo."','".$nc_prioridad."','".$nc_id_set_from."','".$nc_address."','".$nc_cc."','".$nc_bcc."','".$nc_reply_to."','".$nc_subject."','".$nc_body."','".$nc_embeddedimage_ruta."','".$nc_embeddedimage_nombre."','".$nc_embeddedimage_tipo."','".$nc_intentos."','".$nc_eliminar."','".$nc_estado_envio."','".$nc_fecha_envio."','".$nc_usuario_registro."');");

              if ($consulta_notificacion) {
                  registro_log($enlace_db, 'Notificación', 'notificacion', 'Notificación programada '.$nc_subject);
                  return true;
                  break;
              }
          }
    }

    function notificacion_sms($enlace_db, $nsms_identificador, $nsms_destino, $nsms_body, $nsms_url, $nsms_id_modulo="1", $nsms_id_set_from="1", $nsms_prioridad="Alta") {
        // PROGRAMAR NOTIFICACIÓN SMS

          /*SE CONFIGURAN PARÁMETROS A REGISTRAR EN SISTEMA DE NOTIFICACIÓN*/
          $nsms_intentos="";
          $nsms_observaciones="";
          $nsms_estado_envio="Pendiente";
          $nsms_fecha_envio="";
          $nsms_usuario_registro=$_SESSION[APP_SESSION.'_session_usu_id'];

          $verifica_notificacion=0;
          for ($i=0; $i < 10; $i++) {
              // Prepara la sentencia
              $sentencia_insert_notificacion = $enlace_db->prepare("INSERT INTO `administrador_notificaciones_sms`(`nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

              // Agrega variables a sentencia preparada
              $sentencia_insert_notificacion->bind_param('ssssssssssss', $nsms_identificador, $nsms_id_modulo, $nsms_prioridad, $nsms_id_set_from, $nsms_destino, $nsms_body, $nsms_url, $nsms_intentos, $nsms_observaciones, $nsms_estado_envio, $nsms_fecha_envio, $nsms_usuario_registro);
            
              if ($sentencia_insert_notificacion->execute()) {
                  registro_log($enlace_db, 'Notificación', 'notificacion_sms', 'Notificación SMS programada: '.$nsms_body);
                  return true;
                  break;
              }
          }
    }

    function notificacion_familias_sms($enlace_db, $nsms_identificador, $nsms_destino, $nsms_body, $nsms_url, $nsms_id_modulo="11", $nsms_id_set_from="1", $nsms_prioridad="Alta") {
        // PROGRAMAR NOTIFICACIÓN SMS

          /*SE CONFIGURAN PARÁMETROS A REGISTRAR EN SISTEMA DE NOTIFICACIÓN*/
          $nsms_intentos="";
          $nsms_observaciones="";
          $nsms_estado_envio="Pendiente";
          $nsms_fecha_envio="";
          $nsms_usuario_registro=$_SESSION[APP_SESSION.'_session_usu_id'];

          $verifica_notificacion=0;
          for ($i=0; $i < 10; $i++) {
              // Prepara la sentencia
              $sentencia_insert_notificacion = $enlace_db->prepare("INSERT INTO `administrador_notificaciones_sms`(`nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

              // Agrega variables a sentencia preparada
              $sentencia_insert_notificacion->bind_param('ssssssssssss', $nsms_identificador, $nsms_id_modulo, $nsms_prioridad, $nsms_id_set_from, $nsms_destino, $nsms_body, $nsms_url, $nsms_intentos, $nsms_observaciones, $nsms_estado_envio, $nsms_fecha_envio, $nsms_usuario_registro);
            
              if ($sentencia_insert_notificacion->execute()) {
                  registro_log($enlace_db, 'Notificación', 'notificacion_sms', 'Notificación SMS programada: '.$nsms_body);
                  return true;
                  break;
              }
          }
    }

    function notificacion_familias_carguedocs_sms($enlace_db, $nsms_identificador, $nsms_destino, $nsms_body, $nsms_url, $nsms_id_modulo="11DOCS", $nsms_id_set_from="1", $nsms_prioridad="Alta") {
        // PROGRAMAR NOTIFICACIÓN SMS

          /*SE CONFIGURAN PARÁMETROS A REGISTRAR EN SISTEMA DE NOTIFICACIÓN*/
          $nsms_intentos="";
          $nsms_observaciones="";
          $nsms_estado_envio="Pendiente";
          $nsms_fecha_envio="";
          $nsms_usuario_registro='1111111111';

          $verifica_notificacion=0;
          for ($i=0; $i < 10; $i++) {
              // Prepara la sentencia
              $sentencia_insert_notificacion = $enlace_db->prepare("INSERT INTO `administrador_notificaciones_sms`(`nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

              // Agrega variables a sentencia preparada
              $sentencia_insert_notificacion->bind_param('ssssssssssss', $nsms_identificador, $nsms_id_modulo, $nsms_prioridad, $nsms_id_set_from, $nsms_destino, $nsms_body, $nsms_url, $nsms_intentos, $nsms_observaciones, $nsms_estado_envio, $nsms_fecha_envio, $nsms_usuario_registro);
            
              if ($sentencia_insert_notificacion->execute()) {
                  registro_log($enlace_db, 'Notificación', 'notificacion_sms', 'Notificación SMS programada: '.$nsms_body);
                  return true;
                  break;
              }
          }
    }

    function notificacion_agendamiento_sms($enlace_db, $nsms_identificador, $nsms_destino, $nsms_body, $nsms_url, $nsms_id_modulo="13", $nsms_id_set_from="1", $nsms_prioridad="Alta") {
        // PROGRAMAR NOTIFICACIÓN SMS

          /*SE CONFIGURAN PARÁMETROS A REGISTRAR EN SISTEMA DE NOTIFICACIÓN*/
          $nsms_intentos="";
          $nsms_observaciones="";
          $nsms_estado_envio="Pendiente";
          $nsms_fecha_envio="";
          $nsms_usuario_registro='1111111111';

          $verifica_notificacion=0;
          for ($i=0; $i < 10; $i++) {
              // Prepara la sentencia
              $sentencia_insert_notificacion = $enlace_db->prepare("INSERT INTO `administrador_notificaciones_sms`(`nsms_identificador`, `nsms_id_modulo`, `nsms_prioridad`, `nsms_id_set_from`, `nsms_destino`, `nsms_body`, `nsms_url`, `nsms_intentos`, `nsms_observaciones`, `nsms_estado_envio`, `nsms_fecha_envio`, `nsms_usuario_registro`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

              // Agrega variables a sentencia preparada
              $sentencia_insert_notificacion->bind_param('ssssssssssss', $nsms_identificador, $nsms_id_modulo, $nsms_prioridad, $nsms_id_set_from, $nsms_destino, $nsms_body, $nsms_url, $nsms_intentos, $nsms_observaciones, $nsms_estado_envio, $nsms_fecha_envio, $nsms_usuario_registro);
            
              if ($sentencia_insert_notificacion->execute()) {
                  registro_log($enlace_db, 'Notificación', 'notificacion_sms', 'Notificación SMS programada: '.$nsms_body);
                  return true;
                  break;
              }
          }
    }

    function notificacion_agendamiento($enlace_db, $asunto, $referencia, $contenido, $nc_address, $nc_id_modulo="1", $nc_cc="", $nc_id_set_from="3", $nc_prioridad="Alta") {
        // PROGRAMAR NOTIFICACIÓN CORREO
          /*SE ESTRUCTURA COTENIDO DE CORREO*/
              $contenido_correo="<center>
                  <table style='width:100%; max-width: 600px; font-family: Lato, Arial, sans-serif;'>
                      <tr>
                          <td style='padding: 5px 5px 5px 5px;'>
                              ".$contenido."
                              <br>
                          </td>
                      </tr>
                  </table>
              </center>";
          /*SE ESTRUCTURA COTENIDO DE CORREO*/

          /*SE CONFIGURAN PARÁMETROS A REGISTRAR EN SISTEMA DE NOTIFICACIÓN*/
          $nc_bcc="";
          $nc_reply_to="";
          $nc_subject=$asunto;
          $nc_body=str_replace("'", '"', $contenido_correo);
          $nc_embeddedimage_ruta="".IMAGES_ROOT."gestion_ocr/firma-dps.png";
          $nc_embeddedimage_nombre="firma-dps";
          $nc_embeddedimage_tipo="image/png";
          $nc_intentos="";
          $nc_eliminar="Si";
          $nc_estado_envio="Pendiente";
          $nc_fecha_envio="";
          $nc_usuario_registro='1111111111';

          $verifica_notificacion=0;
          for ($i=0; $i < 10; $i++) {
            $consulta_notificacion = mysqli_query($enlace_db, "INSERT INTO `administrador_notificaciones`(`nc_id_modulo`, `nc_prioridad`, `nc_id_set_from`, `nc_address`, `nc_cc`, `nc_bcc`, `nc_reply_to`, `nc_subject`, `nc_body`, `nc_embeddedimage_ruta`, `nc_embeddedimage_nombre`, `nc_embeddedimage_tipo`, `nc_intentos`, `nc_eliminar`, `nc_estado_envio`, `nc_fecha_envio`, `nc_usuario_registro`) VALUES ('".$nc_id_modulo."','".$nc_prioridad."','".$nc_id_set_from."','".$nc_address."','".$nc_cc."','".$nc_bcc."','".$nc_reply_to."','".$nc_subject."','".$nc_body."','".$nc_embeddedimage_ruta."','".$nc_embeddedimage_nombre."','".$nc_embeddedimage_tipo."','".$nc_intentos."','".$nc_eliminar."','".$nc_estado_envio."','".$nc_fecha_envio."','".$nc_usuario_registro."');");

              if ($consulta_notificacion) {
                  // registro_log($enlace_db, 'Notificación', 'notificacion', 'Notificación programada '.$nc_subject);
                  return true;
                  break;
              }
          }
    }

    function registro_log($enlace_db, $log_modulo = NULL, $log_tipo = NULL, $log_detalle = NULL, $array_log = NULL, $clog_registro_usuario = NULL) {
    // Si no vino un $clog_registro_usuario concreto, intentamos tomarlo de la sesión;
    // si no existe en $_SESSION, forzamos a "0" para que NO sea NULL.
        if (empty($clog_registro_usuario)) {
            $clog_registro_usuario = isset($_SESSION[APP_SESSION . '_session_usu_id'])
                ? $_SESSION[APP_SESSION . '_session_usu_id']
                : 0;
        }

        $consulta_string_log = "
          INSERT INTO `administrador_log`(
            `clog_log_modulo`,
            `clog_log_tipo`,
            `clog_log_accion`,
            `clog_log_detalle`,
            `clog_user_agent`,
            `clog_remote_addr`,
            `clog_remote_host`,
            `clog_script`,
            `clog_registro_usuario`
          ) VALUES (?,?,?,?,?,?,?,?,?)
        ";
        $consulta_registros_log = $enlace_db->prepare($consulta_string_log);
        if (!$consulta_registros_log) {
            error_log("registro_log: Error en prepare(): " . $enlace_db->error);
            return false;
            }

    // Dependiendo de $log_tipo, llenaremos $log_accion y $log_detalle_insert
        switch ($log_tipo) {
          case 'crear':
            $log_accion = "Crear registro";
            $log_detalle_insert = $log_detalle;
            $consulta_registros_log->bind_param(
              "sssssssss",
              $log_modulo,
              $log_tipo,
              $log_accion,
              $log_detalle_insert,
              $_SERVER['HTTP_USER_AGENT'],
              $_SERVER['REMOTE_ADDR'],
              $_SERVER['REMOTE_HOST'],
              $_SERVER['PHP_SELF'],
              $clog_registro_usuario
            );
            $consulta_registros_log->execute();
            break;

          case 'editar':
            $log_accion = "Editar registro";
            if (isset($array_log)) {
              for ($i = 0; $i < count($array_log['campo']); $i++) {
                if ($array_log['valor_old'][$i] != $array_log['valor_new'][$i]) {
                  $log_detalle_insert = $log_detalle
                    . " | Item [" . $array_log['campo'][$i] . "]"
                    . " | Anterior [" . $array_log['valor_old'][$i] . "]"
                    . " | Nuevo [" . $array_log['valor_new'][$i] . "]";
                  $consulta_registros_log->bind_param(
                    "sssssssss",
                    $log_modulo,
                    $log_tipo,
                    $log_accion,
                    $log_detalle_insert,
                    $_SERVER['HTTP_USER_AGENT'],
                    $_SERVER['REMOTE_ADDR'],
                    $_SERVER['REMOTE_HOST'],
                    $_SERVER['PHP_SELF'],
                    $clog_registro_usuario
                  );
                  $consulta_registros_log->execute();
                }
              }
            } else {
              $log_detalle_insert = $log_detalle;
              $consulta_registros_log->bind_param(
                "sssssssss",
                $log_modulo,
                $log_tipo,
                $log_accion,
                $log_detalle_insert,
                $_SERVER['HTTP_USER_AGENT'],
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['REMOTE_HOST'],
                $_SERVER['PHP_SELF'],
                $clog_registro_usuario
              );
              $consulta_registros_log->execute();
            }
            break;

          case 'eliminar':
            $log_accion = "Eliminar registro";
            $log_detalle_insert = $log_detalle;
            $consulta_registros_log->bind_param(
              "sssssssss",
              $log_modulo,
              $log_tipo,
              $log_accion,
              $log_detalle_insert,
              $_SERVER['HTTP_USER_AGENT'],
              $_SERVER['REMOTE_ADDR'],
              $_SERVER['REMOTE_HOST'],
              $_SERVER['PHP_SELF'],
              $clog_registro_usuario
            );
            $consulta_registros_log->execute();
            break;

          case 'inicio_sesion':
            $log_accion = "Inicio de sesión";
            $log_detalle_insert = $log_detalle;
            $consulta_registros_log->bind_param(
              "sssssssss",
              $log_modulo,
              $log_tipo,
              $log_accion,
              $log_detalle_insert,
              $_SERVER['HTTP_USER_AGENT'],
              $_SERVER['REMOTE_ADDR'],
              $_SERVER['REMOTE_HOST'],
              $_SERVER['PHP_SELF'],
              $clog_registro_usuario
            );
            $consulta_registros_log->execute();
            break;

          case 'bloqueo_usuario':
            $log_accion = "Bloqueo usuario";
            $log_detalle_insert = $log_detalle;
            $consulta_registros_log->bind_param(
              "sssssssss",
              $log_modulo,
              $log_tipo,
              $log_accion,
              $log_detalle_insert,
              $_SERVER['HTTP_USER_AGENT'],
              $_SERVER['REMOTE_ADDR'],
              $_SERVER['REMOTE_HOST'],
              $_SERVER['PHP_SELF'],
              $clog_registro_usuario
            );
            $consulta_registros_log->execute();
            break;

          case 'cierre_sesion':
            $log_accion = "Cierre de sesión";
            $log_detalle_insert = $log_detalle;
            $consulta_registros_log->bind_param(
              "sssssssss",
              $log_modulo,
              $log_tipo,
              $log_accion,
              $log_detalle_insert,
              $_SERVER['HTTP_USER_AGENT'],
              $_SERVER['REMOTE_ADDR'],
              $_SERVER['REMOTE_HOST'],
              $_SERVER['PHP_SELF'],
              $clog_registro_usuario
            );
            $consulta_registros_log->execute();
            break;

          case 'notificacion':
            $log_accion = "Notificación";
            $log_detalle_insert = $log_detalle;
            $consulta_registros_log->bind_param(
              "sssssssss",
              $log_modulo,
              $log_tipo,
              $log_accion,
              $log_detalle_insert,
              $_SERVER['HTTP_USER_AGENT'],
              $_SERVER['REMOTE_ADDR'],
              $_SERVER['REMOTE_HOST'],
              $_SERVER['PHP_SELF'],
              $clog_registro_usuario
            );
            $consulta_registros_log->execute();
            break;

          case 'notificacion_error':
            $log_accion = "Notificación error";
            $log_detalle_insert = $log_detalle;
            $consulta_registros_log->bind_param(
              "sssssssss",
              $log_modulo,
              $log_tipo,
              $log_accion,
              $log_detalle_insert,
              $_SERVER['HTTP_USER_AGENT'],
              $_SERVER['REMOTE_ADDR'],
              $_SERVER['REMOTE_HOST'],
              $_SERVER['PHP_SELF'],
              $clog_registro_usuario
            );
            $consulta_registros_log->execute();
            break;

          case 'token_sesion':
            $log_accion = "Token sesión";
            $log_detalle_insert = $log_detalle;
            $consulta_registros_log->bind_param(
              "sssssssss",
              $log_modulo,
              $log_tipo,
              $log_accion,
              $log_detalle_insert,
              $_SERVER['HTTP_USER_AGENT'],
              $_SERVER['REMOTE_ADDR'],
              $_SERVER['REMOTE_HOST'],
              $_SERVER['PHP_SELF'],
              $clog_registro_usuario
            );
            $consulta_registros_log->execute();
            break;

          default:
        // Si fuera otro tipo, también lo registramos de forma genérica
            $log_accion = ucfirst($log_tipo);
            $log_detalle_insert = $log_detalle;
            $consulta_registros_log->bind_param(
              "sssssssss",
              $log_modulo,
              $log_tipo,
              $log_accion,
              $log_detalle_insert,
              $_SERVER['HTTP_USER_AGENT'],
              $_SERVER['REMOTE_ADDR'],
              $_SERVER['REMOTE_HOST'],
              $_SERVER['PHP_SELF'],
              $clog_registro_usuario
            );
            $consulta_registros_log->execute();
            break;
        }
    }






    function log_icono($tipo_log) {
      if ($tipo_log=="inicio_sesion"):
          $icono='<i class="fas fa-sign-in-alt"></i>';
      elseif ($tipo_log=="cierre_sesion"):
          $icono='<i class="fas fa-sign-out-alt"></i>';
      elseif($tipo_log=="crear"):
          $icono='<i class="fas fa-plus-square"></i>';
      elseif($tipo_log=="editar"):
          $icono='<i class="fas fa-edit"></i>';
      elseif($tipo_log=="eliminar"):
          $icono='<i class="fas fa-trash-alt"></i>';
      elseif($tipo_log=="notificacion"):
          $icono='<i class="fas fa-envelope"></i>';
      elseif($tipo_log=="notificacion_error"):
          $icono='<i class="fas fa-envelope"></i>';
      elseif($tipo_log=="token_sesion"):
          $icono='<i class="fas fa-qrcode"></i>';
      elseif($tipo_log=="bloqueo_usuario"):
          $icono='<i class="fas fa-user-lock"></i>';
      else:
          $icono='';
      endif;

      return $icono;
    }

    function nombre_ciudad($enlace_db, $id_ciudad = NULL) {
      $consulta_string="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` WHERE `ciu_codigo`=?";

      $consulta_registros = $enlace_db->prepare($consulta_string);
      $consulta_registros->bind_param("s", $id_ciudad);
      $consulta_registros->execute();
      $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

      return $resultado_registros[0][2].", ".$resultado_registros[0][1];
    }

    function validar_extension_icono($extension) {
      //comprobar si es imagen
      if($extension=="png" || $extension=="jpeg" || $extension=="gif" || $extension=="jpg" || $extension=="bmp"){
          $icono_resultado="<span class='fas fa-file-image'></span>";
      }
      //compruebo si es audio
      elseif($extension=="mp3" || $extension=="wav" || $extension=="wma" || $extension=="ogg" || $extension=="mp4"){
          $icono_resultado="<span class='fas fa-file-audio'></span>";
      }
      //comrpuebo si son zip, rar u otros
      elseif ($extension=="zip" || $extension=="rar" || $extension=="tgz" || $extension=="tar") {
          $icono_resultado="<span class='fas fa-file-archive'></span>";
      }
      //compruebo si es un archivo de codigo
      elseif ($extension=="php" || $extension=="php3" || $extension=="html" || $extension=="css" || $extension=="py" || $extension=="java" || $extension=="js" || $extension=="sql") {
          $icono_resultado="<span class='fas fa-file-code'></span>";
      }
      //compruebo si es el archivo es de tipo pdf
      elseif ($extension=="pdf") {
          $icono_resultado="<span class='fas fa-file-pdf'></span>";
      }
       //compruebo si es el archivo es excel
      elseif ($extension=="xlsx") {
          $icono_resultado="<span class='fas fa-file-excel'></span>";
      }
       //compruebo si es el archivo es de powerpoint
      elseif ($extension=="pptx") {
          $icono_resultado="<span class='fas fa-file-powerpoint'></span>";
      }
       //compruebo si es el archivo es de word
      elseif ($extension=="docx") {
          $icono_resultado="<span class='fas fa-file-word'></span>";
      }
       //compruebo si es el archivo es de texto
      elseif ($extension=="txt") {
          $icono_resultado="<span class='fas fa-file-alt'></span>";
      }
       //compruebo si es el archivo es de video
      elseif ($extension=="avi" || $extension=="avi" || $extension=="asf" || $extension=="dvd" || $extension=="m1v" || $extension=="movie" || $extension=="mpeg" || $extension=="wn" || $extension=="wmv") {
          $icono_resultado="<span class='fas fa-file-video'></span>";
      } else {
          $icono_resultado="<span class='fas fa-file-alt'></span>";
      }

      return $icono_resultado;
    }

    function generar_codigo($longitud_codigo) {
      $alphabeth ="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWYZ1234567890_";
      $codigo = "";
      for($i=0;$i<$longitud_codigo;$i++){
          $codigo .= $alphabeth[rand(0,strlen($alphabeth)-1)];
      }

      return $codigo;
    }

    function strip_word_html($text, $allowed_tags = '<a><ul><li><b><i><sup><sub><em><strong><u><br><br/><br /><p><h2><h3><h4><h5><h6>') {
       mb_regex_encoding('UTF-8');
       //replace MS special characters first
       $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
       $replace = array('\'', '\'', '"', '"', '-');
       $text = preg_replace($search, $replace, $text);
       
       if(mb_stripos($text, '/*') !== FALSE){
           $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
       }
       //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
       $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
       $text = strip_tags($text, $allowed_tags);
       //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
       $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
       //strip out inline css and simplify style tags
       $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
       $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
       $text = preg_replace($search, $replace, $text);
       //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
       //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
       //some MS Style Definitions - this last bit gets rid of any leftover comments */
       $num_matches = preg_match_all("/\<!--/u", $text, $matches);
       if($num_matches){
           $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
       }
       $text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $text);
        return $text;
    }

    function dateDiff($start, $end) {
        $start_ts = strtotime($start); 
        $end_ts = strtotime($end); 
        $diff = $end_ts - $start_ts;
        return round($diff); 
    }

    //funcion que convierte segundos en formato de horas:minutos:segundos
    function conversorSegundosHoras($tiempo_en_segundos) {
        $horas = floor($tiempo_en_segundos / 3600);
        $minutos = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
        $segundos = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);
        return $horas . 'h:' . $minutos . "m:" . $segundos."s";
        //return $horas . 'h:' . $minutos . "m";
    }

    //funcion que convierte segundos en formato de horas:minutos:segundos
    function conversorSegundosHoras_ns($tiempo_en_segundos) {
        $horas = floor($tiempo_en_segundos / 3600);
        $minutos = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
        $segundos = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);
        return $horas . 'h:' . $minutos . "m";
    }

    function formatear_fecha_grafica($fecha_validar) {
        $mes_validar=date("m", strtotime($fecha_validar))-1;
        $resultado_fecha=date("Y,", strtotime($fecha_validar)).$mes_validar.",".date("d,H,i,s", strtotime($fecha_validar));
        return $resultado_fecha;
    }

    function formatear_fecha_grafica_fin($fecha_validar_fin, $fecha_validar_inicio) {
        if ($fecha_validar_fin=="") {
            if (date("Y-m-d", strtotime($fecha_validar_inicio))==date("Y-m-d")) {
                $fecha_validar=date("Y-m-d H:i:s");
            } else {
                $fecha_validar=date("Y-m-d", strtotime($fecha_validar_inicio))." 23:59:59";
            }
        } else {
            $fecha_validar=$fecha_validar_fin;
        }

        $mes_validar=date("m", strtotime($fecha_validar))-1;
        $resultado_fecha=date("Y,", strtotime($fecha_validar)).$mes_validar.",".date("d,H,i,s", strtotime($fecha_validar));
        return $resultado_fecha;
    }

    function esMobil() {
        $useragent=$_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
            return 1;
        } else {
            return 0;
        }
    }

    //array columnas
        $array_columnas[]="A";
        $array_columnas[]="B";
        $array_columnas[]="C";
        $array_columnas[]="D";
        $array_columnas[]="E";
        $array_columnas[]="F";
        $array_columnas[]="G";
        $array_columnas[]="H";
        $array_columnas[]="I";
        $array_columnas[]="J";
        $array_columnas[]="K";
        $array_columnas[]="L";
        $array_columnas[]="M";
        $array_columnas[]="N";
        $array_columnas[]="O";
        $array_columnas[]="P";
        $array_columnas[]="Q";
        $array_columnas[]="R";
        $array_columnas[]="S";
        $array_columnas[]="T";
        $array_columnas[]="U";
        $array_columnas[]="V";
        $array_columnas[]="W";
        $array_columnas[]="X";
        $array_columnas[]="Y";
        $array_columnas[]="Z";
        $array_columnas[]="AA";
        $array_columnas[]="AB";
        $array_columnas[]="AC";
        $array_columnas[]="AD";
        $array_columnas[]="AE";
        $array_columnas[]="AF";
        $array_columnas[]="AG";
        $array_columnas[]="AH";
        $array_columnas[]="AI";
        $array_columnas[]="AJ";
        $array_columnas[]="AK";
        $array_columnas[]="AL";
        $array_columnas[]="AM";
        $array_columnas[]="AN";
        $array_columnas[]="AO";
        $array_columnas[]="AP";
        $array_columnas[]="AQ";
        $array_columnas[]="AR";
        $array_columnas[]="AS";
        $array_columnas[]="AT";
        $array_columnas[]="AU";
        $array_columnas[]="AV";
        $array_columnas[]="AW";
        $array_columnas[]="AX";
        $array_columnas[]="AY";
        $array_columnas[]="AZ";
        $array_columnas[]="BA";
        $array_columnas[]="BB";
        $array_columnas[]="BC";
        $array_columnas[]="BD";
        $array_columnas[]="BE";
        $array_columnas[]="BF";
        $array_columnas[]="BG";
        $array_columnas[]="BH";
        $array_columnas[]="BI";
        $array_columnas[]="BJ";
        $array_columnas[]="BK";
        $array_columnas[]="BL";
        $array_columnas[]="BM";
        $array_columnas[]="BN";
        $array_columnas[]="BO";
        $array_columnas[]="BP";
        $array_columnas[]="BQ";
        $array_columnas[]="BR";
        $array_columnas[]="BS";
        $array_columnas[]="BT";
        $array_columnas[]="BU";
        $array_columnas[]="BV";
        $array_columnas[]="BW";
        $array_columnas[]="BX";
        $array_columnas[]="BY";
        $array_columnas[]="BZ";
        $array_columnas[]="CA";
        $array_columnas[]="CB";
        $array_columnas[]="CC";
        $array_columnas[]="CD";
        $array_columnas[]="CE";
        $array_columnas[]="CF";
        $array_columnas[]="CG";
        $array_columnas[]="CH";
        $array_columnas[]="CI";
        $array_columnas[]="CJ";
        $array_columnas[]="CK";
        $array_columnas[]="CL";
        $array_columnas[]="CM";
        $array_columnas[]="CN";
        $array_columnas[]="CO";
        $array_columnas[]="CP";
        $array_columnas[]="CQ";
        $array_columnas[]="CR";
        $array_columnas[]="CS";
        $array_columnas[]="CT";
        $array_columnas[]="CU";
        $array_columnas[]="CV";
        $array_columnas[]="CW";
        $array_columnas[]="CX";
        $array_columnas[]="CY";
        $array_columnas[]="CZ";

        $array_columnas[]="DA";
        $array_columnas[]="DB";
        $array_columnas[]="DC";
        $array_columnas[]="DD";
        $array_columnas[]="DE";
        $array_columnas[]="DF";
        $array_columnas[]="DG";
        $array_columnas[]="DH";
        $array_columnas[]="DI";
        $array_columnas[]="DJ";
        $array_columnas[]="DK";
        $array_columnas[]="DL";
        $array_columnas[]="DM";
        $array_columnas[]="DN";
        $array_columnas[]="DO";
        $array_columnas[]="DP";
        $array_columnas[]="DQ";
        $array_columnas[]="DR";
        $array_columnas[]="DS";
        $array_columnas[]="DT";
        $array_columnas[]="DU";
        $array_columnas[]="DV";
        $array_columnas[]="DW";
        $array_columnas[]="DX";
        $array_columnas[]="DY";
        $array_columnas[]="DZ";

        $array_columnas[]="EA";
        $array_columnas[]="EB";
        $array_columnas[]="EC";
        $array_columnas[]="ED";
        $array_columnas[]="EE";
        $array_columnas[]="EF";
        $array_columnas[]="EG";
        $array_columnas[]="EH";
        $array_columnas[]="EI";
        $array_columnas[]="EJ";
        $array_columnas[]="EK";
        $array_columnas[]="EL";
        $array_columnas[]="EM";
        $array_columnas[]="EN";
        $array_columnas[]="EO";
        $array_columnas[]="EP";
        $array_columnas[]="EQ";
        $array_columnas[]="ER";
        $array_columnas[]="ES";
        $array_columnas[]="ET";
        $array_columnas[]="EU";
        $array_columnas[]="EV";
        $array_columnas[]="EW";
        $array_columnas[]="EX";
        $array_columnas[]="EY";
        $array_columnas[]="EZ";

        $array_columnas[]="FA";
        $array_columnas[]="FB";
        $array_columnas[]="FC";
        $array_columnas[]="FD";
        $array_columnas[]="FE";
        $array_columnas[]="FF";
        $array_columnas[]="FG";
        $array_columnas[]="FH";
        $array_columnas[]="FI";
        $array_columnas[]="FJ";
        $array_columnas[]="FK";
        $array_columnas[]="FL";
        $array_columnas[]="FM";
        $array_columnas[]="FN";
        $array_columnas[]="FO";
        $array_columnas[]="FP";
        $array_columnas[]="FQ";
        $array_columnas[]="FR";
        $array_columnas[]="FS";
        $array_columnas[]="FT";
        $array_columnas[]="FU";
        $array_columnas[]="FV";
        $array_columnas[]="FW";
        $array_columnas[]="FX";
        $array_columnas[]="FY";
        $array_columnas[]="FZ";

        $array_columnas[]="GA";
        $array_columnas[]="GB";
        $array_columnas[]="GC";
        $array_columnas[]="GD";
        $array_columnas[]="GE";
        $array_columnas[]="GF";
        $array_columnas[]="GG";
        $array_columnas[]="GH";
        $array_columnas[]="GI";
        $array_columnas[]="GJ";
        $array_columnas[]="GK";
        $array_columnas[]="GL";
        $array_columnas[]="GM";
        $array_columnas[]="GN";
        $array_columnas[]="GO";
        $array_columnas[]="GP";
        $array_columnas[]="GQ";
        $array_columnas[]="GR";
        $array_columnas[]="GS";
        $array_columnas[]="GT";
        $array_columnas[]="GU";
        $array_columnas[]="GV";
        $array_columnas[]="GW";
        $array_columnas[]="GX";
        $array_columnas[]="GY";
        $array_columnas[]="GZ";
    //array columnas
?>
