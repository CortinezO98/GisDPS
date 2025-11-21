<?php
  // ---------------------------------------------------------
  // 1) INICIAR SESIÓN Y CONFIGURAR ERRORES
  // ---------------------------------------------------------
  session_start();

  // Mostrar todos los errores en pantalla para depuración
//  ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL);

  // ---------------------------------------------------------
  // 2) VALIDACIÓN DE PERMISOS Y CARGA DE INICIALIZADOR
  // ---------------------------------------------------------
  $modulo_plataforma = "Administrador";
  require_once("../../iniciador.php");
  $url_fichero = pathinfo(__FILE__, PATHINFO_FILENAME);

//  ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
 // error_reporting(E_ALL);



  /* VARIABLES */
  $title             = "Aadministrador";
  $subtitle          = "Notificaciones Correo | Reenviar";
  $pagina            = validar_input($_GET['pagina'] ?? '');
  $filtro_permanente = validar_input($_GET['id'] ?? '');
  $id_registro       = validar_input(base64_decode($_GET['reg'] ?? ''));
  $url_salir         = "notificaciones_correo?pagina={$pagina}&id={$filtro_permanente}";

  // ---------------------------------------------------------
  // 3) OBTENER LA FILA ORIGINAL DE LA NOTIFICACIÓN
  // ---------------------------------------------------------
  $consulta_string = "
    SELECT 
      `nc_id`,                  -- 0
      `nc_id_modulo`,           -- 1
      `nc_prioridad`,           -- 2
      `nc_id_set_from`,         -- 3
      `nc_address`,             -- 4
      `nc_cc`,                  -- 5
      `nc_bcc`,                 -- 6
      `nc_reply_to`,            -- 7
      `nc_subject`,             -- 8
      `nc_body`,                -- 9
      `nc_embeddedimage_ruta`,  -- 10
      `nc_embeddedimage_nombre`,-- 11
      `nc_embeddedimage_tipo`,  -- 12
      `nc_intentos`,            -- 13
      `nc_eliminar`,            -- 14
      `nc_estado_envio`,        -- 15
      `nc_fecha_envio`,         -- 16
      `nc_fecha_registro`,      -- 17
      `nc_usuario_registro`,    -- 18
      `ncr_username`,           -- 19
      `ncr_setfrom_name`,       -- 20
      TU.`usu_nombres_apellidos`-- 21
    FROM `administrador_notificaciones`
    LEFT JOIN `administrador_buzones` AS RT 
      ON `administrador_notificaciones`.`nc_id_set_from` = RT.`ncr_id`
    LEFT JOIN `administrador_usuario` AS TU 
      ON `administrador_notificaciones`.`nc_usuario_registro` = TU.`usu_id`
    WHERE `nc_id` = ?
  ";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (!$consulta_registros) {
      error_log("Error en prepare(): " . $enlace_db->error);
      die("Error al preparar consulta: " . htmlspecialchars($enlace_db->error));
  }
  $consulta_registros->bind_param("s", $id_registro);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  // ---------------------------------------------------------
  // 4) PRE-CARGAR “Destinatario” Y “Destinatario CC” (ANTES DEL FORMULARIO)
  // ---------------------------------------------------------
  $destinatario    = "";
  $destinatario_cc = "";
  // También tomaremos, si existe, el nc_usuario_registro original (índice 18)
  $usuario_original = null;
  if (count($resultado_registros) > 0) {
      $destinatario     = str_replace(";", "\r", $resultado_registros[0][4]); // índice 4 = nc_address
      $destinatario_cc  = str_replace(";", "\r", $resultado_registros[0][5]); // índice 5 = nc_cc
      $usuario_original = $resultado_registros[0][18]; // índice 18 = nc_usuario_registro
  }

  // ---------------------------------------------------------
  // 5) PROCESO AL ENVIAR EL FORMULARIO (INSERT NUEVA NOTIFICACIÓN)
  // ---------------------------------------------------------
  if (isset($_POST["guardar_registro"])) {
    // 5.1) Determinar quién será el usuario que registra esta nueva notificación:
    //      priorizamos la sesión, y si no existe, usamos el usuario original
    if (isset($_SESSION[APP_SESSION . '_session_usu_id'])) {
      $nc_usuario_registro = $_SESSION[APP_SESSION . '_session_usu_id'];
    } else {
      // Si la sesión no existe, usar el valor original de la fila (validar que no sea null)
      if ($usuario_original !== null) {
        $nc_usuario_registro = $usuario_original;
      } else {
        // Si no hay tampoco usuario original, establecer en 0 o abortar
        $nc_usuario_registro = 0;
      }
    }

    // 5.2) Leer valores que escribió el usuario y convertir saltos de línea a ';'
    $destinatario    = str_replace(
      ["\r\n", "\n\r", "\r", "\n"],
      ";",
      $_POST['destinatario'] ?? ''
    );
    $destinatario_cc = str_replace(
      ["\r\n", "\n\r", "\r", "\n"],
      ";",
      $_POST['destinatario_cc'] ?? ''
    );

    // 5.3) Verificar si ya hemos insertado en esta sesión (para evitar doble inserción)
    if (!isset($_SESSION[APP_SESSION . '_registro_creado_notificacion'])
        || $_SESSION[APP_SESSION . '_registro_creado_notificacion'] != 1) {

      // 5.4) Estructurar contenido del correo (body HTML)
      $contenido_correo = $resultado_registros[0][9]; // índice 9 = nc_body

      // 5.5) Preparar parámetros para INSERT
      $nc_id_modulo            = $modulo_plataforma;             // varchar(100)
      $nc_prioridad            = '1';                            // varchar(10)
      $nc_id_set_from          = 1;                              // int(10)
      $nc_address              = $destinatario;                  // varchar(2000)
      $nc_cc                   = $destinatario_cc;               // varchar(2000)
      $nc_bcc                  = '';                             // varchar(2000)
      $nc_reply_to             = "";                             // varchar(2000)
      $nc_subject              = $resultado_registros[0][8];     // varchar(200)
      $nc_body                 = str_replace("'", '"', $contenido_correo); // longblob
      $nc_embeddedimage_ruta   = $resultado_registros[0][10];    // varchar(2000)
      $nc_embeddedimage_nombre = $resultado_registros[0][11];    // varchar(100)
      $nc_embeddedimage_tipo   = $resultado_registros[0][12];    // varchar(100)
      $nc_intentos             = "";                             // varchar(2)
      $nc_eliminar             = "Si";                           // varchar(2)
      $nc_estado_envio         = "Pendiente";                     // varchar(20)
      $nc_fecha_envio          = "";                             // varchar(20)
      // $nc_usuario_registro ya está definido más arriba

      // 5.6) Intentar insertar (hasta 10 reintentos)
      $insert_ok = false;
      $sql_insert = "
        INSERT INTO `administrador_notificaciones`
        (
          `nc_id_modulo`,
          `nc_prioridad`,
          `nc_id_set_from`,
          `nc_address`,
          `nc_cc`,
          `nc_bcc`,
          `nc_reply_to`,
          `nc_subject`,
          `nc_body`,
          `nc_embeddedimage_ruta`,
          `nc_embeddedimage_nombre`,
          `nc_embeddedimage_tipo`,
          `nc_intentos`,
          `nc_eliminar`,
          `nc_estado_envio`,
          `nc_fecha_envio`,
          `nc_usuario_registro`
        ) VALUES (
          ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
      ";

      for ($i = 0; $i < 10; $i++) {
        $stmt_insert = $enlace_db->prepare($sql_insert);
        if (!$stmt_insert) {
          error_log("Error en prepare() de INSERT: " . $enlace_db->error);
          break;
        }

        // Cadena de tipos: 17 marcadores = 17 caracteres
        // Según DDL:
        // 1 nc_id_modulo       -> varchar => s
        // 2 nc_prioridad       -> varchar => s
        // 3 nc_id_set_from     -> int     => i
        // 4 nc_address         -> varchar => s
        // 5 nc_cc              -> varchar => s
        // 6 nc_bcc             -> varchar => s
        // 7 nc_reply_to        -> varchar => s
        // 8 nc_subject         -> varchar => s
        // 9 nc_body            -> longblob=> s
        // 10 nc_embeddedimage_ruta   -> varchar => s
        // 11 nc_embeddedimage_nombre -> varchar => s
        // 12 nc_embeddedimage_tipo   -> varchar => s
        // 13 nc_intentos       -> varchar => s
        // 14 nc_eliminar       -> varchar => s
        // 15 nc_estado_envio   -> varchar => s
        // 16 nc_fecha_envio    -> varchar => s
        // 17 nc_usuario_registro -> int => i
        $types = "ssisssssssssssssi";

        $stmt_insert->bind_param(
          $types,
          $nc_id_modulo,
          $nc_prioridad,
          $nc_id_set_from,
          $nc_address,
          $nc_cc,
          $nc_bcc,
          $nc_reply_to,
          $nc_subject,
          $nc_body,
          $nc_embeddedimage_ruta,
          $nc_embeddedimage_nombre,
          $nc_embeddedimage_tipo,
          $nc_intentos,
          $nc_eliminar,
          $nc_estado_envio,
          $nc_fecha_envio,
          $nc_usuario_registro
        );

        if ($stmt_insert->execute()) {
          $_SESSION[APP_SESSION . '_registro_creado_notificacion'] = 1;
          registro_log(
            $enlace_db,
            $modulo_plataforma,
            'notificacion',
            'Notificación programada ' . $nc_subject
          );
          $insert_ok = true;
          $stmt_insert->close();
          break;
        } else {
          // Registrar error y continuar reintento
          error_log("Error en execute() de INSERT: " . $stmt_insert->error);
          $stmt_insert->close();
        }
      }

      if (!$insert_ok) {
        // Si tras 10 intentos no pudo insertar, mostrar mensaje y registrar log
        $respuesta_accion = "alertButton('error', 'Error', 'No se pudo programar la notificación.');";
        error_log("Fallo definitivo al insertar notificación para nc_id={$id_registro}");
      }
    }
    else {
      // Ya se creó en esta sesión, solo mostrar mensaje
      $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente', '{$url_salir}');";
    }
  }
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT . 'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <!-- Navbar -->
    <?php require_once(ROOT . 'includes/_navbar.php'); ?>
    <!-- /Navbar -->
    <div class="container-fluid page-body-wrapper">
      <!-- Sidebar -->
      <?php require_once(ROOT . 'includes/_sidebar.php'); ?>
      <!-- /Sidebar -->
      <!-- Main-panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center">
              <?php
                if (!empty($respuesta_accion)) {
                  echo "<script type='text/javascript'>" . $respuesta_accion . "</script>";
                }
              ?>
              <div class="col-lg-4 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                          <!-- Asunto (readonly) -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="asunto">Asunto</label>
                              <input
                                type="text"
                                class="form-control form-control-sm font-size-11"
                                name="asunto"
                                id="asunto"
                                maxlength="100"
                                value="<?php echo htmlspecialchars($resultado_registros[0][8]); ?>"
                                required
                                readonly
                              >
                            </div>
                          </div>
                          <!-- Remitente (readonly) -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="remitente">Remitente</label>
                              <input
                                type="text"
                                class="form-control form-control-sm font-size-11"
                                name="remitente"
                                id="remitente"
                                maxlength="100"
                                value="<?php echo htmlspecialchars($resultado_registros[0][20]); ?>"
                                required
                                readonly
                              >
                            </div>
                          </div>
                          <!-- Destinatario -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="destinatario">Destinatario</label>
                              <textarea
                                class="form-control form-control-sm height-100"
                                name="destinatario"
                                id="destinatario"
                                <?php if (isset($_SESSION[APP_SESSION . '_registro_creado_notificacion']) && $_SESSION[APP_SESSION . '_registro_creado_notificacion'] == 1) echo 'readonly'; ?>
                              ><?php echo htmlspecialchars($destinatario); ?></textarea>
                            </div>
                          </div>
                          <!-- Destinatario CC -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="destinatario_cc">Destinatario CC</label>
                              <textarea
                                class="form-control form-control-sm height-100"
                                name="destinatario_cc"
                                id="destinatario_cc"
                                <?php if (isset($_SESSION[APP_SESSION . '_registro_creado_notificacion']) && $_SESSION[APP_SESSION . '_registro_creado_notificacion'] == 1) echo 'readonly'; ?>
                              ><?php echo htmlspecialchars($destinatario_cc); ?></textarea>
                            </div>
                          </div>
                          <!-- Botones -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <?php if (isset($_SESSION[APP_SESSION . '_registro_creado_notificacion']) && $_SESSION[APP_SESSION . '_registro_creado_notificacion'] == 1): ?>
                                <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                <button
                                  class="btn btn-success float-end ms-1"
                                  type="submit"
                                  name="guardar_registro"
                                >Guardar</button>
                                <button
                                  class="btn btn-danger float-end"
                                  type="button"
                                  onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');"
                                >Cancelar</button>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div><!-- .row -->
                      </div><!-- .card-body -->
                    </div><!-- .card -->
                  </div><!-- .col-12 -->
                </div><!-- .row -->
              </div><!-- .col-lg-4 -->
            </div><!-- .row -->
          </form>
        </div><!-- .content-wrapper -->
      </div><!-- .main-panel -->
    </div><!-- .page-body-wrapper -->
  </div><!-- .container-scroller -->
  <?php require_once(ROOT . 'includes/_js.php'); ?>
</body>
</html>
