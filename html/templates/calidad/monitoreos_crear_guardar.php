<?php


  // Para depurar, mostramos el contenido de la sesión (elimina o comenta en producción)
//  echo "<pre>";
  //print_r($_SESSION);
  //echo "</pre>";

  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

  // Validación de permisos del usuario para el módulo
  $modulo_plataforma = "Calidad-Monitoreos";
  require_once("../../iniciador.php"); // Aquí se define APP_SESSION y $enlace_db
  require_once("../../app/functions/validar_festivos.php");
  $competencia = isset($_POST['competencia']) ? trim($_POST['competencia']) : null;
  // 1) Primer eco: Verificar que entramos al archivo
//  echo "1) Entré a monitorear_crear_guardar.php<br>";

  // 2) Comprobar que APP_SESSION ya existe
  //if (!defined('APP_SESSION')) {
    //  die("ERROR: La constante APP_SESSION NO está definida.<br>");
  //}
  //echo "2) APP_SESSION existe y vale: '" . APP_SESSION . "'<br>";

  // 3) Verificar que la conexión a la BD ($enlace_db) se definió correctamente
  //if (!isset($enlace_db) || !($enlace_db instanceof mysqli)) {
    //  die("ERROR: \$enlace_db no está definido o no es instancia de mysqli.<br>");
  //}
  //echo "3) \$enlace_db existe y es instancia de mysqli<br>";

  // 4) Verificar que llegaron datos en $_POST
  //if (empty($_POST)) {
    //  die("ERROR: \$_POST está vacío. Revise que el formulario envíe datos correctamente.<br>");
  //}
  //echo "4) \$_POST contiene datos<br>";

  // 5) Verificar que la sesión tiene la llave correcta (prefijo APP_SESSION . '_mon_informacion')
  //if (!isset($_SESSION[APP_SESSION . '_mon_informacion'])) {
    //  die("ERROR: La clave \$_SESSION['".APP_SESSION."_mon_informacion'] NO está definida.<br>");
  //}
  //echo "5) La sesión [ " . APP_SESSION . "_mon_informacion ] existe<br>";

  // ------------------------------------------------------------------------
  // Aquí comienza la lógica para guardar el monitoreo completo
  // ------------------------------------------------------------------------

  $url_fichero = pathinfo(__FILE__, PATHINFO_FILENAME);

  /* VARIABLES */
  $title             = "Calidad";
  $subtitle          = "Monitoreos | Crear Registro | Evaluación";
  $pagina            = validar_input($_GET['pagina']);
  $filtro_permanente = validar_input($_GET['id']);
  $url_salir         = "monitoreos?pagina=" . $pagina . "&id=" . $filtro_permanente . "&bandeja=" . base64_encode('Mes Actual');

  // --------> Cambio clave: ahora entramos si existe cualquier POST
  if (!empty($_POST)) {
      // ---------------------------
      // 1) Recuperar datos de sesión
      // ---------------------------
      $gcm_matriz                   = $_SESSION[APP_SESSION . '_mon_informacion']['matriz'];
      $gcm_analista                 = $_SESSION[APP_SESSION . '_mon_informacion']['analista'];
      $gcm_fecha_hora_gestion       = $_SESSION[APP_SESSION . '_mon_informacion']['fecha_interaccion'];
      $gcm_dependencia              = $_SESSION[APP_SESSION . '_mon_informacion']['dependencia'];
      $gcm_identificacion_ciudadano = $_SESSION[APP_SESSION . '_mon_informacion']['identificacion_ciudadano'];
      $gcm_numero_transaccion       = $_SESSION[APP_SESSION . '_mon_informacion']['numero_interaccion'];
      $gcm_tipo_monitoreo           = $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'];
      $gcm_aplica_indicador         = "Si-Calidad";

      // ------------------------------
      // 2) SOLUCIÓN DE PRIMER CONTACTO
      // ------------------------------
      $gcm_solucion_contacto = validar_input($_POST['solucion_primer_contacto']);
      if ($gcm_solucion_contacto === "No") {
          $gcm_causal_nosolucion = validar_input($_POST['causal_no_solucion']);
      } else {
          $gcm_causal_nosolucion = "";
      }

      // ------------------
      // 3) TIPI y VOC
      // ------------------
      $gcm_tipi_programa       = validar_input($_POST['tipi_programa']);
      $gcm_tipi_tipificacion   = validar_input($_POST['tipi_tipificacion']);
      $gcm_subtipificacion     = validar_input($_POST['subtipificacion']);
      $gcm_atencion_wow        = validar_input($_POST['atencion_wow']);
      $gcm_aplica_voc          = validar_input($_POST['aplica_voc']);

      if ($gcm_aplica_voc === "Si") {
          $gcm_segmento        = validar_input($_POST['segmento']);
          $gcm_tabulacion_voc  = validar_input($_POST['tabulacion_voc']);
          $gcm_voc             = validar_input($_POST['voc']);
          $gcm_emocion_inicial = validar_input($_POST['emocion_inicial']);
          $gcm_emocion_final   = validar_input($_POST['emocion_final']);
          $gcm_que_le_activo   = validar_input($_POST['que_le_activo']);
          $gcm_atribuible      = validar_input($_POST['atribuible']);
      } else {
          $gcm_segmento        = "";
          $gcm_tabulacion_voc  = "";
          $gcm_voc             = "";
          $gcm_emocion_inicial = "";
          $gcm_emocion_final   = "";
          $gcm_que_le_activo   = "";
          $gcm_atribuible      = "";
      }

      // -----------------------------------------
      // 4) Campos adicionales para evaluación telefónica
      // (se mantienen vacíos si no se usan en la matriz)
      // -----------------------------------------
      $gcm_direcciones_misionales = "";
      $gcm_programa               = "";
      $gcm_tipificacion           = "";
      $gcm_subtipificacion_1      = "";
      $gcm_subtipificacion_2      = "";
      $gcm_subtipificacion_3      = "";
      $gcm_observaciones_info     = "";
      $gcm_registro_usuario       = $_SESSION[APP_SESSION . '_session_usu_id'];

      // -------------------------
      // 5) Observaciones generales
      // -------------------------
      $gcm_observaciones_monitoreo = validar_input($_POST['observaciones']);
      $columna_competencia             = validar_input($_POST['competencia']);
      // ----------------------------------------
      // 6) Arrays de ítems (id_campos, grupo_peso, peso_nota, tipo_error)
      // ----------------------------------------
      $items_matriz = $_POST['id_campos'];
      $grupo_peso   = $_POST['grupo_peso'];
      $peso_nota    = $_POST['peso_nota'];
      $tipo_error   = $_POST['tipo_error'];

      $grupos_tipo_error = array_values(array_unique($tipo_error));

      // -----------------------------------------
      // 7) Recorrer cada ítem para armar las respuestas
      // -----------------------------------------
      $item_respuesta    = [];
      $item_comentario   = [];
      $grupos_items_nota = [];
      $grupos_peso_id    = [];

      for ($i = 0; $i < count($items_matriz); $i++) {
          // Si el ítem pertenece a un grupo, inicializo el grupo con nota = 100
          if ($grupo_peso[$i] !== "") {
              $nombreGrupo                    = 'G-' . $grupo_peso[$i];
              $grupos_items_nota[$nombreGrupo] = 100;
              $grupos_peso_id[]               = $nombreGrupo;
          }

          // Respuesta (“Si”/“No”)
          if (isset($_POST['respuesta_' . $items_matriz[$i]])) {
              $item_respuesta[] = $_POST['respuesta_' . $items_matriz[$i]];
          } else {
              $item_respuesta[] = "";
          }

          // Comentario
          if (isset($_POST['comentario_' . $items_matriz[$i]])) {
              $item_comentario[] = $_POST['comentario_' . $items_matriz[$i]];
          } else {
              $item_comentario[] = "";
          }
      }

      $grupos_peso_id = array_values(array_unique($grupos_peso_id));

      // ---------------------------------------------------------
      // 8) Calcular notas por tipo de error y ajustar grupos
      // ---------------------------------------------------------
      $item_calificable_tipo_error = [];
      for ($i = 0; $i < count($items_matriz); $i++) {
          $id_item = $items_matriz[$i];
          $tipoErr = $tipo_error[$i];
          $pesoIt  = $peso_nota[$i];

          if ($grupo_peso[$i] === "") {
              // Ítem individual
              if ($item_respuesta[$i] === "No") {
                  $item_calificable_tipo_error[$tipoErr][$id_item] = 0;
              } else {
                  $item_calificable_tipo_error[$tipoErr][$id_item] = $pesoIt;
              }
          } else {
              // Pertenece a un grupo
              $nombreGrupo = 'G-' . $grupo_peso[$i];
              $item_calificable_tipo_error[$tipoErr][$nombreGrupo] = $pesoIt;

              // Si dentro de un grupo el ítem sale “No”, anulo todo el grupo
              if ($item_respuesta[$i] === "No") {
                  $grupos_items_nota[$nombreGrupo] = 0;
              }
          }
      }

      // Ajustar notas para grupos completos
      for ($gi = 0; $gi < count($grupos_peso_id); $gi++) {
          $grupoId = $grupos_peso_id[$gi]; // ej. “G-2”
          foreach ($tipo_error as $te) {
              if (isset($item_calificable_tipo_error[$te][$grupoId])) {
                  if ($grupos_items_nota[$grupoId] == 0) {
                      $item_calificable_tipo_error[$te][$grupoId] = 0;
                  }
              }
          }
      }

      // ---------------------
      // 9) Calcular nota ENC
      // ---------------------
      if (isset($item_calificable_tipo_error['ENC'])) {
          if (count($item_calificable_tipo_error['ENC']) > 0) {
              $gcm_nota_enc = array_sum($item_calificable_tipo_error['ENC']);
          } else {
              $gcm_nota_enc = "NA";
          }
      } else {
          $gcm_nota_enc = "NA";
      }

      // ----------------------
      // 10) Calcular nota ECUF
      // ----------------------
      if (isset($item_calificable_tipo_error['ECU'])) {
          if (count($item_calificable_tipo_error['ECU']) > 0) {
              $gcm_nota_ecuf = array_sum($item_calificable_tipo_error['ECU']);
          } else {
              $gcm_nota_ecuf = "NA";
          }
      } else {
          $gcm_nota_ecuf = "NA";
      }

      // ----------------------
      // 11) Calcular nota ECN
      // ----------------------
      if (isset($item_calificable_tipo_error['ECN'])) {
          if (count($item_calificable_tipo_error['ECN']) > 0) {
              $gcm_nota_ecn = array_sum($item_calificable_tipo_error['ECN']);
          } else {
              $gcm_nota_ecn = "NA";
          }
      } else {
          $gcm_nota_ecn = "NA";
      }

      // --------------------------------------------------------
      // 12) Determinar estados de cada nota (NA → 1, 100 → 1, otro → 0)
      // --------------------------------------------------------
      $control_estado_enc  = ($gcm_nota_enc === "NA")  ? 1 : (($gcm_nota_enc == 100)  ? 1 : 0);
      $control_estado_ecuf = ($gcm_nota_ecuf === "NA") ? 1 : (($gcm_nota_ecuf == 100) ? 1 : 0);
      $control_estado_ecn  = ($gcm_nota_ecn === "NA")  ? 1 : (($gcm_nota_ecn == 100)  ? 1 : 0);

      // ---------------------------------------------------------------------------------
      // 13) Determinar estado final (“Aceptado” si todas las notas en NA o 100, o calibración)
      //      en otro caso → “Pendiente” (y calculamos fecha límite en 2 días hábiles)
      // ---------------------------------------------------------------------------------
      if (
        ($control_estado_enc == 1 && $control_estado_ecuf == 1 && $control_estado_ecn == 1)
        || ($gcm_tipo_monitoreo == 'Calibración-Escucha 1' || $gcm_tipo_monitoreo == 'Calibración-Escucha 2')
      ) {
          $gcm_estado                    = "Aceptado";
          $gcm_fecha_reac_limite         = "";
          $gcm_fecha_reac                = "";
          $gcm_fecha_calidad_reac_limite = "";
          $gcm_fecha_calidad_reac        = "";
          $gcm_fecha_snivel_reac_limite  = "";
          $gcm_fecha_snivel_reac         = "";
          $gcm_fecha_sreac_limite        = "";
          $gcm_fecha_sreac               = "";
          $gcm_fecha_novedad_inicio      = "";
          $gcm_fecha_novedad_fin         = "";
          $gcm_novedad_observaciones     = "";
      } else {
          $gcm_estado = "Pendiente";
          // Calcular fecha límite en 2 días hábiles
          $dia_control  = date('Y-m-d H:i:s');
          $dias_habiles = 0;
          while ($dias_habiles <= 2) {
              $numero_dia = date("N", strtotime($dia_control));
              $festivo    = validarFestivo($dia_control); // validarFestivo viene de iniciador.php
              if ($numero_dia >= 1 && $numero_dia < 6 && $festivo == '') {
                  $dia_limite = $dia_control;
                  $dias_habiles++;
              }
              $dia_control = date("Y-m-d H:i:s", strtotime("+1 day", strtotime($dia_control)));
          }
          $gcm_fecha_reac_limite         = $dia_limite;
          $gcm_fecha_reac                = "";
          $gcm_fecha_calidad_reac_limite = "";
          $gcm_fecha_calidad_reac        = "";
          $gcm_fecha_snivel_reac_limite  = "";
          $gcm_fecha_snivel_reac         = "";
          $gcm_fecha_sreac_limite        = "";
          $gcm_fecha_sreac               = "";
          $gcm_fecha_novedad_inicio      = "";
          $gcm_fecha_novedad_fin         = "";
          $gcm_novedad_observaciones     = "";
      }

      // -------------------------------------------------------------------
      // 14) Solo insertar un monitoreo nuevo si aún no se ha creado ya este
      //     flujo en la sesión actual
      // -------------------------------------------------------------------
      if (empty($_SESSION[APP_SESSION . '_monitoreo_creado']) || $_SESSION[APP_SESSION . '_monitoreo_creado'] != 1) {
          // Obtener el próximo consecutivo (p. ej. MC0000000001 → MC0000000002)
          $consulta_consecutivo  = mysqli_query($enlace_db, "SELECT MAX(`gcm_id`) FROM `gestion_calidad_monitoreo`");
          $resultado_consecutivo = mysqli_fetch_all($consulta_consecutivo);
          $ultimo_consecutivo    = explode('MC', $resultado_consecutivo[0][0]);
          $nuevo_consecutivo     = intval($ultimo_consecutivo[1]) + 1;
          $inser_consecutivo     = "MC" . str_pad($nuevo_consecutivo, 10, "0", STR_PAD_LEFT);

          // ------------------------------------------------------------------------------
          // 15) Preparamos el INSERT principal con 50 columnas EXACTAS en la tabla
          // ------------------------------------------------------------------------------
          // Primero listamos las 50 columnas en el mismo orden que están en la definición DDL:
          $sql_insert = "
            INSERT INTO `gestion_calidad_monitoreo` (
              `gcm_id`,
              `gcm_matriz`,
              `gcm_analista`,
              `gcm_fecha_hora_gestion`,
              `gcm_dependencia`,
              `gcm_identificacion_ciudadano`,
              `gcm_numero_transaccion`,
              `gcm_tipo_monitoreo`,
              `gcm_observaciones_monitoreo`,
              `gcm_nota_enc`,
              `gcm_nota_ecn`,
              `gcm_nota_ecuf`,
              `gcm_nota_enc_estado`,
              `gcm_nota_ecn_estado`,
              `gcm_nota_ecuf_estado`,
              `gcm_aplica_indicador`,
              `gcm_estado`,
              `gcm_solucion_contacto`,
              `gcm_causal_nosolucion`,
              `gcm_tipi_programa`,
              `gcm_tipi_tipificacion`,
              `gcm_subtipificacion`,
              `gcm_atencion_wow`,
              `gcm_aplica_voc`,
              `gcm_segmento`,
              `gcm_tabulacion_voc`,
              `gcm_voc`,
              `gcm_emocion_inicial`,
              `gcm_emocion_final`,
              `gcm_que_le_activo`,
              `gcm_atribuible`,
              `gcm_direcciones_misionales`,
              `gcm_programa`,
              `gcm_tipificacion`,
              `gcm_subtipificacion_1`,
              `gcm_subtipificacion_2`,
              `gcm_subtipificacion_3`,
              `gcm_observaciones_info`,
              `gcm_fecha_reac_limite`,
              `gcm_fecha_reac`,
              `gcm_fecha_calidad_reac_limite`,
              `gcm_fecha_calidad_reac`,
              `gcm_fecha_snivel_reac_limite`,
              `gcm_fecha_snivel_reac`,
              `gcm_fecha_sreac_limite`,
              `gcm_fecha_sreac`,
              `gcm_fecha_novedad_inicio`,
              `gcm_fecha_novedad_fin`,
              `gcm_novedad_observaciones`,
              `gcm_registro_usuario`,
              `columna_competencia`
            ) 
            VALUES (" .
              // Generamos directamente 50 signos de interrogación
              implode(',', array_fill(0, 51, '?')) . "
            )
          ";

          $sentencia_insert = $enlace_db->prepare($sql_insert);
          if ($sentencia_insert === false) {
              throw new Exception("Error en prepare(): " . $enlace_db->error);
          }

          // Cadena de tipos: 50 veces "s" porque todas las columnas son VARCHAR o TEXT/LOB
          $tipos = str_repeat("s", 51);

          // Bind de parámetros (50 valores en el mismo orden que las columnas arriba)
          $sentencia_insert->bind_param(
            $tipos,
            $inser_consecutivo,                 //  1  `gcm_id`
            $gcm_matriz,                        //  2  `gcm_matriz`
            $gcm_analista,                      //  3  `gcm_analista`
            $gcm_fecha_hora_gestion,            //  4  `gcm_fecha_hora_gestion`
            $gcm_dependencia,                   //  5  `gcm_dependencia`
            $gcm_identificacion_ciudadano,      //  6  `gcm_identificacion_ciudadano`
            $gcm_numero_transaccion,            //  7  `gcm_numero_transaccion`
            $gcm_tipo_monitoreo,                //  8  `gcm_tipo_monitoreo`
            $gcm_observaciones_monitoreo,       //  9  `gcm_observaciones_monitoreo`
            $gcm_nota_enc,                      // 10  `gcm_nota_enc`
            $gcm_nota_ecn,                      // 11  `gcm_nota_ecn`
            $gcm_nota_ecuf,                     // 12  `gcm_nota_ecuf`
            $control_estado_enc,                // 13  `gcm_nota_enc_estado`
            $control_estado_ecn,                // 14  `gcm_nota_ecn_estado`
            $control_estado_ecuf,               // 15  `gcm_nota_ecuf_estado`
            $gcm_aplica_indicador,              // 16  `gcm_aplica_indicador`
            $gcm_estado,                        // 17  `gcm_estado`
            $gcm_solucion_contacto,             // 18  `gcm_solucion_contacto`
            $gcm_causal_nosolucion,             // 19  `gcm_causal_nosolucion`
            $gcm_tipi_programa,                 // 20  `gcm_tipi_programa`
            $gcm_tipi_tipificacion,             // 21  `gcm_tipi_tipificacion`
            $gcm_subtipificacion,               // 22  `gcm_subtipificacion`
            $gcm_atencion_wow,                  // 23  `gcm_atencion_wow`
            $gcm_aplica_voc,                    // 24  `gcm_aplica_voc`
            $gcm_segmento,                      // 25  `gcm_segmento`
            $gcm_tabulacion_voc,                // 26  `gcm_tabulacion_voc`
            $gcm_voc,                           // 27  `gcm_voc`
            $gcm_emocion_inicial,               // 28  `gcm_emocion_inicial`
            $gcm_emocion_final,                 // 29  `gcm_emocion_final`
            $gcm_que_le_activo,                 // 30  `gcm_que_le_activo`
            $gcm_atribuible,                    // 31  `gcm_atribuible`
            $gcm_direcciones_misionales,        // 32  `gcm_direcciones_misionales`
            $gcm_programa,                      // 33  `gcm_programa`
            $gcm_tipificacion,                  // 34  `gcm_tipificacion`
            $gcm_subtipificacion_1,             // 35  `gcm_subtipificacion_1`
            $gcm_subtipificacion_2,             // 36  `gcm_subtipificacion_2`
            $gcm_subtipificacion_3,             // 37  `gcm_subtipificacion_3`
            $gcm_observaciones_info,            // 38  `gcm_observaciones_info`
            $gcm_fecha_reac_limite,             // 39  `gcm_fecha_reac_limite`
            $gcm_fecha_reac,                    // 40  `gcm_fecha_reac`
            $gcm_fecha_calidad_reac_limite,     // 41  `gcm_fecha_calidad_reac_limite`
            $gcm_fecha_calidad_reac,            // 42  `gcm_fecha_calidad_reac`
            $gcm_fecha_snivel_reac_limite,      // 43  `gcm_fecha_snivel_reac_limite`
            $gcm_fecha_snivel_reac,             // 44  `gcm_fecha_snivel_reac`
            $gcm_fecha_sreac_limite,            // 45  `gcm_fecha_sreac_limite`
            $gcm_fecha_sreac,                   // 46  `gcm_fecha_sreac`
            $gcm_fecha_novedad_inicio,          // 47  `gcm_fecha_novedad_inicio`
            $gcm_fecha_novedad_fin,             // 48  `gcm_fecha_novedad_fin`
            $gcm_novedad_observaciones,         // 49  `gcm_novedad_observaciones`
            $gcm_registro_usuario,               // 50  `gcm_registro_usuario`
            $columna_competencia
          );

          // 16) Ejecutar INSERT principal
          if (!$sentencia_insert->execute()) {
              throw new Exception("Error en execute(): " . $sentencia_insert->error);
          }

          // 17) Guardar ID del monitoreo en sesión
          $_SESSION[APP_SESSION . '_id_monitoreo'] = $inser_consecutivo;
          $control_insert = 0;

          // --------------------------------------------------
          // 18) Preparar INSERT para calificaciones (por ítem)
          // --------------------------------------------------
          $sentencia_insert_calificaciones = $enlace_db->prepare("
            INSERT INTO `gestion_calidad_monitoreo_calificaciones`
              (`gcmc_monitoreo`, `gcmc_pregunta`, `gcmc_respuesta`, `gcmc_afectaciones`, `gcmc_comentarios`)
            VALUES (?,?,?,?,?)
          ");
          if ($sentencia_insert_calificaciones === false) {
              throw new Exception("Error en prepare() calificaciones: " . $enlace_db->error);
          }

          for ($i = 0; $i < count($items_matriz); $i++) {
              $item_matriz_pregunta = $items_matriz[$i];
              $afectaciones        = "";
              $respuesta_item      = $item_respuesta[$i];
              $comentarios_insert  = $item_comentario[$i];

              $sentencia_insert_calificaciones->bind_param(
                  "sssss",
                  $inser_consecutivo,
                  $item_matriz_pregunta,
                  $respuesta_item,
                  $afectaciones,
                  $comentarios_insert
              );
              if (!$sentencia_insert_calificaciones->execute()) {
                  throw new Exception("Error en execute() calificaciones: " . $sentencia_insert_calificaciones->error);
              }
              $control_insert++;
          }

          // ---------------------------------------------
          // 19) Si guardó todas las calificaciones exitosas
          // ---------------------------------------------
          if (count($items_matriz) == $control_insert) {
              $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
              $_SESSION[APP_SESSION . '_monitoreo_creado'] = 1;

              // ------------------------------------------------
              // 20) Insertar registro de evento en administrador_log
              // ------------------------------------------------
              $consulta_string_log = "
                INSERT INTO `administrador_log`
                  (`clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_registro_usuario`)
                VALUES (?,?,?,?,?)
              ";
              $log_modulo  = $modulo_plataforma;
              $log_tipo    = "crear";
              $log_accion  = "Crear registro";
              $log_detalle = "Monitoreo [" . $_SESSION[APP_SESSION . '_id_monitoreo'] . "]";
              $log_usuario = $_SESSION[APP_SESSION . '_session_usu_id'];

              $consulta_registros_log = $enlace_db->prepare($consulta_string_log);
              $consulta_registros_log->bind_param(
                  "sssss",
                  $log_modulo,
                  $log_tipo,
                  $log_accion,
                  $log_detalle,
                  $log_usuario
              );
              $consulta_registros_log->execute();

              // ------------------------------------------------------------
              // 21) Si el monitoreo quedó “Pendiente” y no es calibración, envío mail
              // ------------------------------------------------------------
              if (
                $gcm_estado == 'Pendiente'
                && $gcm_tipo_monitoreo != 'Calibración-Escucha 1'
                && $gcm_tipo_monitoreo != 'Calibración-Escucha 2'
              ) {
                  // Obtener información del supervisor / líder de calidad
                  $consulta_string_supervisor = "
                    SELECT
                      TU.`usu_id`,
                      TU.`usu_nombres_apellidos`,
                      TU.`usu_correo_corporativo`,
                      TL.`usu_id`,
                      TL.`usu_correo_corporativo`,
                      TLC.`usu_correo_corporativo`
                    FROM `administrador_usuario` AS TU
                      LEFT JOIN `administrador_usuario` AS TL ON TU.`usu_supervisor` = TL.`usu_id`
                      LEFT JOIN `administrador_usuario` AS TLC ON TU.`usu_lider_calidad` = TLC.`usu_id`
                    WHERE TU.`usu_id` = ?
                  ";
                  $consulta_registros_supervisor = $enlace_db->prepare($consulta_string_supervisor);
                  $consulta_registros_supervisor->bind_param("s", $gcm_analista);
                  $consulta_registros_supervisor->execute();
                  $resultado_registros_supervisor = $consulta_registros_supervisor->get_result()->fetch_all(MYSQLI_NUM);

                  $asunto     = 'Monitoreo Calidad | ' . $inser_consecutivo;
                  $referencia = 'Monitoreo Calidad';
                  $contenido  = "
                    <p style='font-size: 12px;padding: 0px 5px; color: #666666;'>
                      Cordial saludo,<br><br>Se ha registrado el siguiente monitoreo:
                    </p>
                    <table style='width: 500px; font-size: 13px; font-family: Lato, Arial, sans-serif;'>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Id Monitoreo</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>$inser_consecutivo</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Doc. Agente</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>".$resultado_registros_supervisor[0][0]."</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Nombres y Apellidos</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>".$resultado_registros_supervisor[0][1]."</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Tipo Monitoreo</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>$gcm_tipo_monitoreo</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Dependencia</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>$gcm_dependencia</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Número Interacción</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>$gcm_numero_transaccion</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Nota ENC</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>$gcm_nota_enc</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Nota ECUF</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>$gcm_nota_ecuf</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Nota ECN</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>$gcm_nota_ecn</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Registrado por</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>".$_SESSION["iqgis-dps_session_usu_nombre_completo"]."</td>
                      </tr>
                      <tr>
                        <td style='width: 30%; background-color: #1C2262; color: #FFFFFF; padding: 5px; text-align: center;'>Fecha registro</td>
                        <td style='width: 70%; padding: 5px; background-color: #F2F2F2;'>".date('d/m/Y H:i:s')."</td>
                      </tr>
                    </table>
                  ";
                  $nc_address = $resultado_registros_supervisor[0][2] . ";";
                  $nc_cc      = $resultado_registros_supervisor[0][4] . ";" . $resultado_registros_supervisor[0][5] . ";";
                  notificacion(
                    $enlace_db,
                    $asunto,
                    $referencia,
                    $contenido,
                    $nc_address,
                    $modulo_plataforma,
                    $nc_cc
                  );
              }
          }
      } else {
          // Si ya existe un monitoreo creado en esta sesión, solo muestro mensaje de éxito
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '".$url_salir."');";
      }
  } // <--- fin de if(!empty($_POST))

  // -------------------------------------------------
  // 22) Volver a consultar datos de matriz y analista
  //     para mostrarlos en la vista final (resumen)
  // -------------------------------------------------
  $consulta_string_matriz = "
    SELECT 
      `gcm_id`, 
      `gcm_nombre_matriz`, 
      `gcm_estado`, 
      `gcm_canal`, 
      `gcm_observaciones`, 
      `gcm_registro_usuario`, 
      `gcm_registro_fecha` 
    FROM `gestion_calidad_matriz` 
    WHERE `gcm_id` = ?
  ";
  $consulta_registros_matriz = $enlace_db->prepare($consulta_string_matriz);
  $consulta_registros_matriz->bind_param("s", $_SESSION[APP_SESSION . '_mon_informacion']['matriz']);
  $consulta_registros_matriz->execute();
  $resultado_registros_matriz = $consulta_registros_matriz->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_analista = "
    SELECT 
      `usu_id`, 
      `usu_nombres_apellidos` 
    FROM `administrador_usuario` 
    WHERE `usu_id` = ?
  ";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->bind_param("s", $_SESSION[APP_SESSION . '_mon_informacion']['analista']);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);
?>


<!DOCTYPE html> <html lang="<?php echo LANG; ?>"> <head>
  <?php require_once(ROOT . 'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <!-- navbar -->
    <?php require_once(ROOT . 'includes/_navbar.php'); ?>
    <!-- navbar -->
    <div class="container-fluid page-body-wrapper">
      <!-- sidebar -->
      <?php require_once(ROOT . 'includes/_sidebar.php'); ?>
      <!-- sidebar -->
      <!-- main-panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <form name="guardar_registro" action="monitoreos_crear_guardar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>" method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center">
              <div class="col-lg-4 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <?php if (!empty($respuesta_accion)): ?>
                          <script type="text/javascript">
                            <?php echo $respuesta_accion; ?>
                          </script>
                        <?php endif; ?>
                        <div class="col-md-12 mb-3">
                          <p class="alert alert-success font-size-11 p-1">
                            ¡Se ha generado el monitoreo <?php 
                              echo htmlspecialchars($_SESSION[APP_SESSION . '_id_monitoreo'], ENT_QUOTES, 'UTF-8'); 
                            ?>!
                          </p>
                        </div>
                        <div class="table-responsive">
                          <table class="table table-bordered table-striped table-hover table-sm">
                            <tbody>
                              <tr>
                                <th class="p-1 font-size-11" style="width: 170px;">Matriz</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($resultado_registros_matriz[0][1]) 
                                          ? htmlspecialchars($resultado_registros_matriz[0][1], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Canal</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($resultado_registros_matriz[0][3]) 
                                          ? htmlspecialchars($resultado_registros_matriz[0][3], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <?php if (isset($resultado_registros_matriz[0][3]) && $resultado_registros_matriz[0][3] === "Escrito"): ?>
                                <tr>
                                  <th class="p-1 font-size-11">Dependencia</th>
                                  <td class="p-1 font-size-11">
                                    <?php 
                                      echo isset($_SESSION[APP_SESSION . '_mon_informacion']['dependencia']) 
                                            ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['dependencia'], ENT_QUOTES, 'UTF-8') 
                                            : '';
                                    ?>
                                  </td>
                                </tr>
                              <?php endif; ?>
                              <tr>
                                <th class="p-1 font-size-11">Número Interacción</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($_SESSION[APP_SESSION . '_mon_informacion']['numero_interaccion']) 
                                          ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['numero_interaccion'], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Identificación Ciudadano</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($_SESSION[APP_SESSION . '_mon_informacion']['identificacion_ciudadano']) 
                                          ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['identificacion_ciudadano'], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Fecha Interacción</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($_SESSION[APP_SESSION . '_mon_informacion']['fecha_interaccion']) 
                                          ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['fecha_interaccion'], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Doc. Agente</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($resultado_registros_analistas[0][0]) 
                                          ? htmlspecialchars($resultado_registros_analistas[0][0], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Agente</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($resultado_registros_analistas[0][1]) 
                                          ? htmlspecialchars($resultado_registros_analistas[0][1], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Tipo Monitoreo</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                          ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div><!-- /.card-body -->
                    </div><!-- /.card -->
                  </div><!-- /.col-12 -->
                </div><!-- /.row flex-grow -->
              </div><!-- /.col-lg-4 -->
            </div><!-- /.row justify-content-center -->
          </form>
        </div><!-- /.content-wrapper -->
      </div><!-- /.main-panel -->
    </div><!-- /.page-body-wrapper -->
  </div><!-- /.container-scroller -->
  <?php require_once(ROOT . 'includes/_js.php'); ?>
</body>
</html>
