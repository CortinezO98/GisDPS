<?php
session_start();

// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validación de permisos del usuario para el módulo
$modulo_plataforma = "Administrador";
require_once("../../iniciador.php");

$url_fichero = pathinfo(__FILE__, PATHINFO_FILENAME);

/* VARIABLES INICIALES */
$title               = "Administrador";
$subtitle            = "Usuario | Crear";
$pagina              = validar_input($_GET['pagina'] ?? '');
$filtro_permanente   = validar_input($_GET['id'] ?? '');
$url_salir           = "usuarios?pagina=" . $pagina . "&id=" . $filtro_permanente;
$respuesta_accion    = ""; // Para mensajes de éxito o error

// Variables para “volver a mostrar” valores en caso de error:
$documento_identidad  = "";
$nombres_apellidos    = "";
$usuario_acceso       = "";
$correo_corporativo   = "";
$fecha_ingreso        = "";
$fecha_ingreso_area   = "";
$fecha_nacimiento     = "";
$genero               = "";
$estado               = "";
$usuario_red          = "";
$ciudad               = "";
$ubicacion            = "";
$campania             = "";
$cargo_rol            = [];
$supervisor           = "";
$usu_reparto          = "";

/**
 * 1. Si se envió el formulario, procesamos la creación del usuario
 */
if (isset($_POST["guardar_registro"])) {
    // 2. Recoger y sanitizar todos los campos del formulario
    $documento_identidad  = validar_input($_POST['documento_identidad'] ?? '');
    $nombres_apellidos    = validar_input($_POST['nombres_apellidos'] ?? '');
    $usuario_acceso       = validar_input($_POST['usuario_acceso'] ?? '');
    $correo_corporativo   = validar_input($_POST['correo_corporativo'] ?? '');
    $fecha_ingreso        = validar_input($_POST['fecha_ingreso'] ?? '');
    $fecha_ingreso_area   = validar_input($_POST['fecha_ingreso_area'] ?? '');
    $fecha_nacimiento     = validar_input($_POST['fecha_nacimiento'] ?? '');
    $genero               = validar_input($_POST['genero'] ?? '');
    $estado               = validar_input($_POST['estado'] ?? '');
    $usuario_red          = validar_input($_POST['usuario_red'] ?? '');
    $ciudad               = validar_input($_POST['ciudad'] ?? '');
    $ubicacion            = validar_input($_POST['ubicacion'] ?? '');
    $campania             = validar_input($_POST['campania'] ?? '');
    $cargo_rol_arr        = $_POST['cargo_rol'] ?? [];
    $cargo_rol_guardar    = implode(';', array_map('validar_input', $cargo_rol_arr));
    $supervisor           = validar_input($_POST['supervisor'] ?? '');
    $usu_reparto          = validar_input($_POST['usu_reparto'] ?? '');
    $piloto               = ''; // Si no lo llenas en el formulario, queda vacío
    $foto                 = 'avatar/' . strtolower($genero) . '.jpg';
    $lider_calidad        = "";
    $inicio_sesion        = '0'; // VARCHAR(1)
    $usu_modificacion_usuario = $_SESSION[APP_SESSION . '_session_usu_id'] ?? '';
    $usu_modificacion_fecha   = date('Y-m-d H:i:s');
    $usu_ultimo_acceso        = date('Y-m-d H:i:s');

    // 3. Verificar duplicados por documento o por usuario de acceso
    $sql_duplicado = "
        SELECT COUNT(`usu_id`)
          FROM `administrador_usuario`
         WHERE `usu_id` = ?
            OR `usu_acceso` = ?
    ";
    $stm_duplicado = $enlace_db->prepare($sql_duplicado);
    if (!$stm_duplicado) {
        die("Error al preparar selección de duplicados: " . $enlace_db->error);
    }
    $stm_duplicado->bind_param("ss", $documento_identidad, $usuario_acceso);
    $stm_duplicado->execute();
    $fila_dup = $stm_duplicado->get_result()->fetch_row();
    $stm_duplicado->close();

    if ($fila_dup[0] > 0) {
        // Ya existe un usuario con esa cédula o ese usuario de acceso
        $respuesta_accion = "alertButton('error','Error','Ya existe un usuario con esa Cédula o Usuario de Acceso.');";
    } else {
        // 4. Generamos la contraseña aleatoria y la encriptamos
        $nueva_contrasena = generatePassword(10);
        $salt = substr(base64_encode(openssl_random_pseudo_bytes(30)), 0, 22);
        $salt = strtr($salt, array('+' => '.'));
        $contrasena = crypt($nueva_contrasena, '$2y$10$' . $salt);

        // 5. Insertar en tabla `administrador_usuario` (ahora 24 columnas, incluyendo `usu_reparto`)
        $sql_insert = "
            INSERT INTO `administrador_usuario` (
                `usu_id`,
                `usu_acceso`,
                `usu_contrasena`,
                `usu_nombres_apellidos`,
                `usu_correo_corporativo`,
                `usu_fecha_incorporacion`,
                `usu_campania`,
                `usu_usuario_red`,
                `usu_cargo_rol`,
                `usu_sede`,
                `usu_ciudad`,
                `usu_estado`,
                `usu_supervisor`,
                `usu_lider_calidad`,
                `usu_inicio_sesion`,
                `usu_piloto`,
                `usu_fecha_ingreso_piloto`,
                `usu_reparto`,
                `usu_foto`,
                `usu_genero`,
                `usu_fecha_nacimiento`,
                `usu_modificacion_usuario`,
                `usu_modificacion_fecha`,
                `usu_ultimo_acceso`
            ) VALUES (
                ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
            )
        ";
        $stm_insert = $enlace_db->prepare($sql_insert);
        if (!$stm_insert) {
            die("Error al preparar INSERT usuario: " . $enlace_db->error);
        }

        // 6. Ligamos los 24 parámetros, todos como string (VARCHAR)
        $stm_insert->bind_param(
            'ssssssssssssssssssssssss',
            $documento_identidad,       // 1  - usu_id
            $usuario_acceso,            // 2  - usu_acceso
            $contrasena,                // 3  - usu_contrasena
            $nombres_apellidos,         // 4  - usu_nombres_apellidos
            $correo_corporativo,        // 5  - usu_correo_corporativo
            $fecha_ingreso,             // 6  - usu_fecha_incorporacion
            $campania,                  // 7  - usu_campania
            $usuario_red,               // 8  - usu_usuario_red
            $cargo_rol_guardar,         // 9  - usu_cargo_rol
            $ubicacion,                 // 10 - usu_sede
            $ciudad,                    // 11 - usu_ciudad
            $estado,                    // 12 - usu_estado
            $supervisor,                // 13 - usu_supervisor
            $lider_calidad,             // 14 - usu_lider_calidad
            $inicio_sesion,             // 15 - usu_inicio_sesion
            $piloto,                    // 16 - usu_piloto
            $fecha_ingreso_area,        // 17 - usu_fecha_ingreso_piloto
            $usu_reparto,               // 18 - usu_reparto
            $foto,                      // 19 - usu_foto
            $genero,                    // 20 - usu_genero
            $fecha_nacimiento,          // 21 - usu_fecha_nacimiento
            $usu_modificacion_usuario,  // 22 - usu_modificacion_usuario
            $usu_modificacion_fecha,    // 23 - usu_modificacion_fecha
            $usu_ultimo_acceso          // 24 - usu_ultimo_acceso
        );

        // 7. Ejecutamos el INSERT principal
        if ($stm_insert->execute()) {
            // 8. Log de creación
            registro_log(
                $enlace_db,
                $modulo_plataforma,
                'crear',
                'Creación de usuario ' . $documento_identidad . ' - ' . $nombres_apellidos
            );

            // 9. Insertar la contraseña en su tabla de históricos
            $stm_pass = $enlace_db->prepare("
                INSERT INTO `administrador_usuario_contrasenas` (
                   `auc_usuario`, `auc_contrasena`
                ) VALUES (?,?)
            ");
            if (!$stm_pass) {
                die("Error al preparar INSERT contraseñas: " . $enlace_db->error);
            }
            $stm_pass->bind_param('ss', $documento_identidad, $contrasena);
            $stm_pass->execute();
            $stm_pass->close();

            // 10. Programar notificación de credenciales
            $asunto     = 'Credenciales de acceso - ' . APP_NAME . ' | ' . APP_NAME_ALL;
            $referencia = 'Credenciales de Acceso';
            $contenido  = "
                <p style='font-size:12px; padding:0px 5px; color:#666666;'>
                  Cordial saludo,<br><br>
                  ¡Hemos generado las siguientes credenciales de acceso!
                </p>
                <center>
                  <p style='font-size:12px; color:#666666;'><b>Nombres y Apellidos: {$nombres_apellidos}</b></p>
                  <p style='font-size:12px; color:#666666;'><b>Usuario: {$usuario_acceso}</b></p>
                  <p style='font-size:12px; color:#666666;'><b>Contraseña: {$nueva_contrasena}</b></p>
                </center>
            ";
            $nc_address = $correo_corporativo . ";";
            $nc_cc      = "";
            notificacion(
                $enlace_db,
                $asunto,
                $referencia,
                $contenido,
                $nc_address,
                $modulo_plataforma,
                $nc_cc
            );
            registro_log(
                $enlace_db,
                $modulo_plataforma,
                'notificacion',
                'Notificación de credenciales para usuario ' . $documento_identidad . ' - ' . $nombres_apellidos . ' programada'
            );

            // 11. Mensaje de éxito y reiniciar variables para poder crear otro usuario
            $respuesta_accion = "alertButton('success','Registro creado','Usuario creado exitosamente');";

            // Limpiar campos (para que el formulario quede en blanco si quieres agregar otro)
            $documento_identidad  = "";
            $nombres_apellidos    = "";
            $usuario_acceso       = "";
            $correo_corporativo   = "";
            $fecha_ingreso        = "";
            $fecha_ingreso_area   = "";
            $fecha_nacimiento     = "";
            $genero               = "";
            $estado               = "";
            $usuario_red          = "";
            $ciudad               = "";
            $ubicacion            = "";
            $campania             = "";
            $cargo_rol            = [];
            $supervisor           = "";
            $usu_reparto          = "";
        } else {
            // Si falló el execute() del INSERT
            $error_sql = $stm_insert->error;
            die("Error al ejecutar INSERT usuario: " . $error_sql);
        }

        $stm_insert->close();
    }
}

/**
 * 12. Consultas para llenar los dropdowns (ciudad, supervisor, calidad, ubicación, campaña)
 */
$consulta_ciudad = $enlace_db->prepare("
    SELECT `ciu_codigo`,`ciu_departamento`,`ciu_municipio`
      FROM `administrador_ciudades`
  ORDER BY `ciu_departamento`,`ciu_municipio`
");
$consulta_ciudad->execute();
$resultado_registros_ciudad = $consulta_ciudad->get_result()->fetch_all(MYSQLI_NUM);

$consulta_supervisor = $enlace_db->prepare("
    SELECT `usu_id`,`usu_nombres_apellidos`
      FROM `administrador_usuario`
  ORDER BY `usu_nombres_apellidos`
");
$consulta_supervisor->execute();
$resultado_registros_supervisor = $consulta_supervisor->get_result()->fetch_all(MYSQLI_NUM);

$consulta_calidad = $enlace_db->prepare("
    SELECT `usu_id`,`usu_nombres_apellidos`
      FROM `administrador_usuario`
     WHERE `usu_cargo_rol`='Líder de calidad y formación'
        OR `usu_cargo_rol`='Sistema'
  ORDER BY `usu_nombres_apellidos`
");
$consulta_calidad->execute();
$resultado_registros_calidad = $consulta_calidad->get_result()->fetch_all(MYSQLI_NUM);

$consulta_ubicacion = $enlace_db->prepare("
    SELECT `au_id`,`au_nombre_ubicacion`,`au_observaciones`
      FROM `administrador_ubicacion`
  ORDER BY `au_nombre_ubicacion`
");
$consulta_ubicacion->execute();
$resultado_registros_ubicacion = $consulta_ubicacion->get_result()->fetch_all(MYSQLI_NUM);

$consulta_campania = $enlace_db->prepare("
    SELECT `ac_id`,`ac_nombre_campania`,`ac_observaciones`
      FROM `administrador_campania`
  ORDER BY `ac_nombre_campania`
");
$consulta_campania->execute();
$resultado_registros_campania = $consulta_campania->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT . 'includes/_head.php'); ?>
  <style>
    /* Opcional: ajustar un poco el ancho de los selects múltiples */
    .selectpicker {
      width: 100% !important;
    }
  </style>
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
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center">
              <?php
                // Mostrar alerta si hay respuesta de acción
                if (!empty($respuesta_accion)) {
                    echo "<script type='text/javascript'>{$respuesta_accion}</script>";
                }
              ?>
              <div class="col-lg-6 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">

                          <!-- Documento identidad -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="documento_identidad" class="my-0">Documento identidad</label>
                              <input
                                type="text"
                                class="form-control form-control-sm font-size-11"
                                name="documento_identidad"
                                id="documento_identidad"
                                maxlength="20"
                                value="<?php echo htmlspecialchars($documento_identidad, ENT_QUOTES); ?>"
                                required
                              >
                            </div>
                          </div>

                          <!-- Nombres y apellidos -->
                          <div class="col-md-8">
                            <div class="form-group">
                              <label for="nombres_apellidos" class="my-0">Nombres y apellidos</label>
                              <input
                                type="text"
                                class="form-control form-control-sm font-size-11"
                                name="nombres_apellidos"
                                id="nombres_apellidos"
                                maxlength="100"
                                value="<?php echo htmlspecialchars($nombres_apellidos, ENT_QUOTES); ?>"
                                required
                              >
                            </div>
                          </div>

                          <!-- Género -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="genero" class="my-0">Género</label>
                              <select
                                class="form-control form-control-sm form-select font-size-11"
                                name="genero"
                                id="genero"
                                required
                              >
                                <option value="">Seleccione</option>
                                <option value="Sin definir" <?php if ($genero === "Sin definir") echo "selected"; ?>>Sin definir</option>
                                <option value="Mujer"        <?php if ($genero === "Mujer") echo "selected"; ?>>Mujer</option>
                                <option value="Hombre"       <?php if ($genero === "Hombre") echo "selected"; ?>>Hombre</option>
                              </select>
                            </div>
                          </div>

                          <!-- Fecha nacimiento -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_nacimiento" class="my-0">Fecha nacimiento</label>
                              <input
                                type="date"
                                class="form-control form-control-sm font-size-11"
                                name="fecha_nacimiento"
                                id="fecha_nacimiento"
                                value="<?php echo htmlspecialchars($fecha_nacimiento, ENT_QUOTES); ?>"
                                required
                              >
                            </div>
                          </div>

                          <!-- Fecha ingreso -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_ingreso" class="my-0">Fecha ingreso</label>
                              <input
                                type="date"
                                class="form-control form-control-sm font-size-11"
                                name="fecha_ingreso"
                                id="fecha_ingreso"
                                value="<?php echo htmlspecialchars($fecha_ingreso, ENT_QUOTES); ?>"
                                required
                              >
                            </div>
                          </div>

                          <!-- Fecha ingreso área -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_ingreso_area" class="my-0">Fecha ingreso área</label>
                              <input
                                type="date"
                                class="form-control form-control-sm font-size-11"
                                name="fecha_ingreso_area"
                                id="fecha_ingreso_area"
                                value="<?php echo htmlspecialchars($fecha_ingreso_area, ENT_QUOTES); ?>"
                                required
                              >
                            </div>
                          </div>

                          <!-- Estado -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="estado" class="my-0">Estado</label>
                              <select
                                class="form-control form-control-sm form-select font-size-11"
                                name="estado"
                                id="estado"
                                required
                              >
                                <option value="">Seleccione</option>
                                <option value="Activo"    <?php if ($estado === "Activo") echo "selected"; ?>>Activo</option>
                                <option value="Inactivo"  <?php if ($estado === "Inactivo") echo "selected"; ?>>Inactivo</option>
                                <option value="Retirado"  <?php if ($estado === "Retirado") echo "selected"; ?>>Retirado</option>
                                <option value="Bloqueado" <?php if ($estado === "Bloqueado") echo "selected"; ?>>Bloqueado</option>
                                <option value="Eliminado" <?php if ($estado === "Eliminado") echo "selected"; ?>>Eliminado</option>
                              </select>
                            </div>
                          </div>

                          <!-- Usuario acceso -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="usuario_acceso" class="my-0">Usuario acceso</label>
                              <input
                                type="text"
                                class="form-control form-control-sm font-size-11"
                                name="usuario_acceso"
                                id="usuario_acceso"
                                maxlength="20"
                                value="<?php echo htmlspecialchars($usuario_acceso, ENT_QUOTES); ?>"
                                required
                              >
                            </div>
                          </div>

                          <!-- Usuario de red -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="usuario_red" class="my-0">Usuario de red</label>
                              <input
                                type="text"
                                class="form-control form-control-sm font-size-11"
                                name="usuario_red"
                                id="usuario_red"
                                maxlength="20"
                                value="<?php echo htmlspecialchars($usuario_red, ENT_QUOTES); ?>"
                                required
                              >
                            </div>
                          </div>

                          <!-- Correo corporativo -->
                          <div class="col-md-8">
                            <div class="form-group">
                              <label for="correo_corporativo" class="my-0">Correo corporativo</label>
                              <input
                                type="email"
                                class="form-control form-control-sm font-size-11"
                                name="correo_corporativo"
                                id="correo_corporativo"
                                maxlength="100"
                                value="<?php echo htmlspecialchars($correo_corporativo, ENT_QUOTES); ?>"
                                required
                              >
                            </div>
                          </div>

                          <!-- Ciudad -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="ciudad" class="my-0">Ciudad</label>
                              <select
                                class="form-control form-control-sm form-select font-size-11"
                                name="ciudad"
                                id="ciudad"
                                required
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($resultado_registros_ciudad as $row_ciudad): ?>
                                  <option
                                    value="<?php echo htmlspecialchars($row_ciudad[0], ENT_QUOTES); ?>"
                                    <?php if ($ciudad === $row_ciudad[0]) echo "selected"; ?>
                                  >
                                    <?php echo htmlspecialchars($row_ciudad[2] . ", " . $row_ciudad[1], ENT_QUOTES); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <!-- Ubicación (usu_sede) -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="ubicacion" class="my-0">Ubicación</label>
                              <select
                                class="form-control form-control-sm form-select font-size-11"
                                name="ubicacion"
                                id="ubicacion"
                                required
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($resultado_registros_ubicacion as $row_ubic): ?>
                                  <option
                                    value="<?php echo htmlspecialchars($row_ubic[0], ENT_QUOTES); ?>"
                                    <?php if ($ubicacion === $row_ubic[0]) echo "selected"; ?>
                                  >
                                    <?php echo htmlspecialchars($row_ubic[1], ENT_QUOTES); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <!-- Área (campania) -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="campania" class="my-0">Área</label>
                              <select
                                class="form-control form-control-sm form-select font-size-11"
                                name="campania"
                                id="campania"
                                required
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($resultado_registros_campania as $row_camp): ?>
                                  <option
                                    value="<?php echo htmlspecialchars($row_camp[0], ENT_QUOTES); ?>"
                                    <?php if ($campania === $row_camp[0]) echo "selected"; ?>
                                  >
                                    <?php echo htmlspecialchars($row_camp[1], ENT_QUOTES); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <!-- Cargo/rol (multiple) -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="cargo_rol" class="my-0">Cargo/rol</label>
                              <select
                                class="selectpicker form-control form-control-sm form-select font-size-11"
                                data-live-search="true"
                                data-container="body"
                                name="cargo_rol[]"
                                id="cargo_rol"
                                multiple
                                required
                              >
                                <option value="">Seleccione</option>
                                <?php
                                  $opciones_rol = [
                                    "AGENTE GENERAL",
                                    "AGENTE TÉCNICO",
                                    "AGENTE ESPECIALIZADO",
                                    "AGENTE ESPECIALIZADO MINERO DE DATOS",
                                    "AGENTE GENERAL LENGUAJE DE SEÑAS",
                                    "AGENTE INSCRIPCIÓN FA",
                                    "AGENTE INSCRIPCIÓN FA CONSULTA",
                                    "AGENTE DPS AGENDAMIENTO",
                                    "AGENTE PROFESIONAL",
                                    "AGENTE TUTELAS",
                                    "AGENTE PRIORITARIOS",
                                    "AGENTE SOY TRANSPARENTE",
                                    "AGENTE FUNCIONARIOS",
                                    "AGENTE CIUDADANOS",
                                    "AGENTE ENVÍO RADICADO A CIUDADANO",
                                    "AGENTE NOTIFICACIONES DE CORREO",
                                    "AGENTE RADICADOS-ENTRENAMIENTO",
                                    "AGENTE ENVÍOS WEB-TRANSVERSAL",
                                    "AGENTE ENVÍOS WEB-ENTRENAMIENTO",
                                    "INTERPRETE",
                                    "FORMADOR",
                                    "COORDINADOR",
                                    "LIDER DE CALIDAD",
                                    "COORDINADOR NACIONAL",
                                    "PROFESIONAL DE OPERACION ZONAL EN CAMPO",
                                    "ADMINISTRADOR PLATAFORMA"
                                  ];
                                  foreach ($opciones_rol as $rol_item):
                                ?>
                                  <option
                                    value="<?php echo htmlspecialchars($rol_item, ENT_QUOTES); ?>"
                                    <?php
                                      if (
                                        is_array($cargo_rol_arr) &&
                                        in_array($rol_item, $cargo_rol_arr)
                                      ) {
                                          echo "selected";
                                      }
                                    ?>
                                  >
                                    <?php echo htmlspecialchars($rol_item, ENT_QUOTES); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <!-- Responsable (supervisor) -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="supervisor" class="my-0">Responsable</label>
                              <select
                                class="form-control form-control-sm form-select font-size-11"
                                name="supervisor"
                                id="supervisor"
                                required
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($resultado_registros_supervisor as $row_sup): ?>
                                  <option
                                    value="<?php echo htmlspecialchars($row_sup[0], ENT_QUOTES); ?>"
                                    <?php if ($supervisor === $row_sup[0]) echo "selected"; ?>
                                  >
                                    <?php echo htmlspecialchars($row_sup[1], ENT_QUOTES); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <!-- Reparto -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="usu_reparto" class="my-0">Reparto</label>
                              <select
                                class="form-control form-control-sm form-select font-size-11"
                                name="usu_reparto"
                                id="usu_reparto"
                                required
                              >
                                <option value="">Seleccione</option>
                                <option value="Activo"   <?php if ($usu_reparto === "Activo") echo "selected"; ?>>Activo</option>
                                <option value="Inactivo" <?php if ($usu_reparto === "Inactivo") echo "selected"; ?>>Inactivo</option>
                              </select>
                            </div>
                          </div>

                          <!-- Botones Guardar y Cancelar -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <button
                                class="btn btn-success float-end ms-1"
                                type="submit"
                                name="guardar_registro"
                              >
                                Guardar
                              </button>
                              <button
                                class="btn btn-danger float-end"
                                type="button"
                                onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');"
                              >
                                Cancelar
                              </button>
                            </div>
                          </div>

                        </div> <!-- .row principal -->
                      </div> <!-- .card-body -->
                    </div> <!-- .card -->
                  </div> <!-- .col-12 -->
                </div> <!-- .row flex-grow -->
              </div> <!-- .col-lg-6 -->
            </div> <!-- .row justify-content-center -->
          </form>
        </div> <!-- .content-wrapper -->
      </div> <!-- .main-panel -->
    </div> <!-- .container-fluid page-body-wrapper -->
  </div> <!-- .container-scroller -->

  <?php require_once(ROOT . 'includes/_js.php'); ?>
</body>
</html>
