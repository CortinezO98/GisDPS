<?php
session_start();

// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Permisos del usuario para el módulo
$modulo_plataforma = "Administrador";
require_once("../../iniciador.php");

// Tras incluir config.php (que silencia errores), volvemos a habilitarlos
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$url_fichero = pathinfo(__FILE__, PATHINFO_FILENAME);

/* VARIABLES */
$title             = "Administrador";
$subtitle          = "Usuarios | Editar";
$pagina            = validar_input($_GET['pagina'] ?? '');
$filtro_permanente = validar_input($_GET['id'] ?? '');
$id_registro       = validar_input(base64_decode($_GET['reg'] ?? ''));
$url_salir         = "usuarios?pagina=" . $pagina . "&id=" . $filtro_permanente;

$respuesta_accion = ""; // Para mensajes JavaScript

// ———————————————————————————————
// 1. PROCESAR EDICIÓN DEL REGISTRO
// ———————————————————————————————
if (isset($_POST["guardar_registro"])) {
    // 1.a) Recoger y sanitizar campos
    $nombres_apellidos   = validar_input($_POST['nombres_apellidos'] ?? '');
    $usuario_acceso      = validar_input($_POST['usuario_acceso'] ?? '');
    $correo_corporativo  = validar_input($_POST['correo_corporativo'] ?? '');
    $fecha_ingreso       = validar_input($_POST['fecha_ingreso'] ?? '');
    $fecha_ingreso_area  = validar_input($_POST['fecha_ingreso_area'] ?? '');
    $fecha_nacimiento    = validar_input($_POST['fecha_nacimiento'] ?? '');
    $genero              = validar_input($_POST['genero'] ?? '');
    $estado              = validar_input($_POST['estado'] ?? '');
    $usuario_red         = validar_input($_POST['usuario_red'] ?? '');
    $ciudad              = validar_input($_POST['ciudad'] ?? '');
    $ubicacion           = validar_input($_POST['ubicacion'] ?? '');
    $campania            = validar_input($_POST['campania'] ?? '');
    $cargo_rol_arr       = $_POST['cargo_rol'] ?? [];
    // Sanear cada elemento de cargo_rol antes de implode
    $cargo_rol_saneado = [];
    foreach ($cargo_rol_arr as $cr) {
        $cargo_rol_saneado[] = validar_input($cr);
    }
    $cargo_rol_guardar = implode(';', $cargo_rol_saneado);

    $supervisor            = validar_input($_POST['supervisor'] ?? '');
    $piloto                = validar_input($_POST['piloto'] ?? '');
    $usu_reparto           = validar_input($_POST['usu_reparto'] ?? '');
    $lider_calidad         = ""; // según lógica original
    $foto                  = 'avatar/' . strtolower($genero) . '.jpg';

    $usu_modificacion_usuario = $_SESSION[APP_SESSION . '_session_usu_id'] ?? '';
    $usu_modificacion_fecha   = date('Y-m-d H:i:s');

    // 1.b) Procesar permisos de módulos
    $modulo_permiso  = $_POST['modulo_permiso'] ?? [];
    $contador_insert = 0;

    foreach ($modulo_permiso as $mp) {
        $mp = validar_input($mp);
        $modulo_separado = explode("|", $mp);
        $key_registro    = $id_registro . $modulo_separado[0];

        if ($modulo_separado[1] === "") {
            // DELETE si el perfil está vacío
            $sentencia_perm = $enlace_db->prepare("
                DELETE FROM `administrador_usuario_modulo_perfil`
                WHERE `per_id` = ?
            ");
            if (!$sentencia_perm) {
                die("Error al preparar DELETE permisos: " . $enlace_db->error);
            }
            $sentencia_perm->bind_param('s', $key_registro);
        } else {
            // INSERT ... ON DUPLICATE KEY UPDATE
            $sentencia_perm = $enlace_db->prepare("
                INSERT INTO `administrador_usuario_modulo_perfil`(
                    `per_id`,`per_usuario`,`per_modulo`,`per_perfil`
                )
                VALUES (?,?,?,?)
                ON DUPLICATE KEY UPDATE `per_perfil` = ?
            ");
            if (!$sentencia_perm) {
                die("Error al preparar INSERT permisos: " . $enlace_db->error);
            }
            $perfil = validar_input($modulo_separado[1]);
            $sentencia_perm->bind_param(
                'sssss',
                $key_registro,
                $id_registro,
                $modulo_separado[0],
                $perfil,
                $perfil
            );
        }

        if ($sentencia_perm->execute()) {
            $contador_insert++;
        }
        $sentencia_perm->close();
    }

    // 1.c) Preparar UPDATE para administrador_usuario (incluyendo usu_reparto)
    $sql_update = "
        UPDATE `administrador_usuario` SET
            `usu_acceso`               = ?,
            `usu_nombres_apellidos`    = ?,
            `usu_correo_corporativo`   = ?,
            `usu_fecha_incorporacion`  = ?,
            `usu_campania`             = ?,
            `usu_usuario_red`          = ?,
            `usu_cargo_rol`            = ?,
            `usu_sede`                 = ?,   -- aquí guardamos 'ubicacion'
            `usu_ciudad`               = ?,
            `usu_estado`               = ?,
            `usu_supervisor`           = ?,
            `usu_lider_calidad`        = ?,
            `usu_piloto`               = ?,
            `usu_genero`               = ?,
            `usu_fecha_nacimiento`     = ?,
            `usu_modificacion_usuario` = ?,
            `usu_modificacion_fecha`   = ?,
            `usu_fecha_ingreso_piloto` = ?,   -- aquí guardamos 'fecha_ingreso_area'
            `usu_reparto`              = ?    -- aquí guardamos 'usu_reparto'
        WHERE `usu_id` = ?
    ";

    $consulta_actualizar = $enlace_db->prepare($sql_update);
    if (!$consulta_actualizar) {
        die("Error al preparar UPDATE usuario: " . $enlace_db->error);
    }

    // Hay 20 placeholders en total ahora (19 campos + WHERE)
    $consulta_actualizar->bind_param(
        'ssssssssssssssssssss',
        $usuario_acceso,           //  1
        $nombres_apellidos,        //  2
        $correo_corporativo,       //  3
        $fecha_ingreso,            //  4
        $campania,                 //  5   <-- aquí va campania
        $usuario_red,              //  6
        $cargo_rol_guardar,        //  7
        $ubicacion,                //  8   <-- aquí va ubicacion (usu_sede)
        $ciudad,                   //  9
        $estado,                   // 10
        $supervisor,               // 11
        $lider_calidad,            // 12
        $piloto,                   // 13
        $genero,                   // 14
        $fecha_nacimiento,         // 15
        $usu_modificacion_usuario, // 16
        $usu_modificacion_fecha,   // 17
        $fecha_ingreso_area,       // 18  <-- aquí va fecha_ingreso_area (usu_fecha_ingreso_piloto)
        $usu_reparto,              // 19  <-- aquí va repartición (usu_reparto)
        $id_registro               // 20  <-- WHERE usu_id
    );

    if (!$consulta_actualizar->execute()) {
        die("Error al ejecutar UPDATE usuario: " . $consulta_actualizar->error);
    }

    $filas_afectadas = $enlace_db->affected_rows;
    $total_permisos  = count($modulo_permiso);

    if ($filas_afectadas >= 0 && $contador_insert === $total_permisos) {
        $respuesta_accion = "alertButton('success','Registro editado','Registro editado exitosamente');";
        registro_log(
            $enlace_db,
            $modulo_plataforma,
            'editar',
            'Registro editado para usuario ' . $id_registro . ' - ' . $nombres_apellidos
        );
    } else {
        $respuesta_accion = "alertButton('error','Error','Problemas al editar el registro');";
    }

    $consulta_actualizar->close();
}

// ———————————————————————————————
// 2. PROCESAR REINICIO DE CONTRASEÑA
// ———————————————————————————————
if (isset($_POST["reset_contrasena"])) {
    $nombres_apellidos   = validar_input($_POST['nombres_apellidos'] ?? '');
    $usuario_acceso      = validar_input($_POST['usuario_acceso'] ?? '');
    $correo_corporativo  = validar_input($_POST['correo_corporativo'] ?? '');

    $nueva_contrasena = generatePassword(10);
    $salt             = substr(base64_encode(openssl_random_pseudo_bytes(30)), 0, 22);
    $salt             = strtr($salt, array('+' => '.'));
    $contrasena       = crypt($nueva_contrasena, '$2y$10$' . $salt);
    $inicio_sesion    = '0';
    $usu_modificacion_usuario = $_SESSION[APP_SESSION . '_session_usu_id'] ?? '';
    $usu_modificacion_fecha   = date('Y-m-d H:i:s');

    $sql_reset = "
        UPDATE `administrador_usuario` SET
            `usu_contrasena`           = ?,
            `usu_inicio_sesion`        = ?,
            `usu_modificacion_usuario` = ?,
            `usu_modificacion_fecha`   = ?
        WHERE `usu_id` = ?
    ";
    $consulta_reset = $enlace_db->prepare($sql_reset);
    if (!$consulta_reset) {
        die("Error al preparar RESET contraseña: " . $enlace_db->error);
    }
    $consulta_reset->bind_param(
        'sssss',
        $contrasena,
        $inicio_sesion,
        $usu_modificacion_usuario,
        $usu_modificacion_fecha,
        $id_registro
    );
    if (!$consulta_reset->execute()) {
        die("Error al ejecutar RESET contraseña: " . $consulta_reset->error);
    }

    // Insertar en administrador_usuario_contrasenas
    $consulta_insert_contrasena = $enlace_db->prepare("
        INSERT INTO `administrador_usuario_contrasenas`(`auc_usuario`,`auc_contrasena`)
        VALUES (?,?)
    ");
    if (!$consulta_insert_contrasena) {
        die("Error al preparar INSERT contraseñas: " . $enlace_db->error);
    }
    $consulta_insert_contrasena->bind_param('ss', $id_registro, $contrasena);
    $consulta_insert_contrasena->execute();

    $respuesta_accion = "alertButton('success','Registro editado','Contraseña reiniciada exitosamente');";
    registro_log(
        $enlace_db,
        $modulo_plataforma,
        'editar',
        'Contraseña reseteada para usuario ' . $id_registro . ' - ' . $nombres_apellidos
    );

    // Notificación de credenciales
    $asunto     = 'Credenciales de acceso - ' . APP_NAME . ' | ' . APP_NAME_ALL;
    $referencia = 'Credenciales de Acceso';
    $contenido  = "
      <p style='font-size: 12px; padding: 0px 5px; color: #666666;'>
        Cordial saludo,<br><br>
        ¡Hemos generado las siguientes credenciales de acceso!
      </p>
      <center>
        <p style='font-size: 12px; color: #666666;'><b>Nombres y Apellidos: {$nombres_apellidos}</b></p>
        <p style='font-size: 12px; color: #666666;'><b>Usuario: {$usuario_acceso}</b></p>
        <p style='font-size: 12px; color: #666666;'><b>Contraseña: {$nueva_contrasena}</b></p>
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
        'Notificación de credenciales para usuario ' . $id_registro . ' - ' . $nombres_apellidos . ' programada'
    );

    $consulta_reset->close();
    $consulta_insert_contrasena->close();
}

// ———————————————————————————————
// 3. CONSULTAS PARA DROPDOWNS Y TABLA DE MÓDULOS
// ———————————————————————————————
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

$consulta_modulos = $enlace_db->prepare("
    SELECT `mod_id`,`mod_modulo_nombre`
    FROM `administrador_modulo`
    ORDER BY `mod_modulo_nombre`
");
$consulta_modulos->execute();
$resultado_registros_modulos = $consulta_modulos->get_result()->fetch_all(MYSQLI_NUM);

$consulta_permisos = $enlace_db->prepare("
    SELECT `per_id`,`per_usuario`,`per_modulo`,`per_perfil`
    FROM `administrador_usuario_modulo_perfil`
    WHERE `per_usuario` = ?
");
$consulta_permisos->bind_param("s", $id_registro);
$consulta_permisos->execute();
$resultado_registros_permisos = $consulta_permisos->get_result()->fetch_all(MYSQLI_NUM);

$array_permisos = [];
foreach ($resultado_registros_permisos as $perm) {
    $array_permisos[$perm[2]] = $perm[3];
}

// ———————————————————————————————
// 4. OBTENER DATOS DEL USUARIO PARA PRELLENAR EL FORMULARIO
// ———————————————————————————————
$consulta_usuario = $enlace_db->prepare("
    SELECT 
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
      `usu_genero`,
      `usu_fecha_nacimiento`,
      `usu_fecha_ingreso_piloto`,
      `usu_reparto`     -- ahora incluimos reparto
    FROM `administrador_usuario`
    WHERE `usu_id` = ?
");
$consulta_usuario->bind_param("s", $id_registro);
$consulta_usuario->execute();

// (A diferencia de fetch_all, aquí usamos fetch_assoc para acceder por nombre de columna)
$result_usuario = $consulta_usuario->get_result()->fetch_assoc();
if (!$result_usuario) {
    die("Usuario no encontrado.");
}

// Guardamos en variables explícitas para “ubicar” fácilmente:
$valor_sede      = $result_usuario['usu_sede'];
$valor_campania  = $result_usuario['usu_campania'];
$valor_reparto   = $result_usuario['usu_reparto'];   // <-- nuevo: reparto

// Como el array de cargo_rol viene separado por “;”, lo convertimos en array PHP:
$array_cargo_rol = explode(';', $result_usuario['usu_cargo_rol']);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
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
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center">
              <?php
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

                          <!-- Documento identidad (readonly) -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="documento_identidad" class="my-0">Documento identidad</label>
                              <input
                                type="text"
                                class="form-control form-control-sm font-size-11"
                                name="documento_identidad"
                                id="documento_identidad"
                                maxlength="20"
                                value="<?php echo htmlspecialchars($result_usuario['usu_id'], ENT_QUOTES); ?>"
                                readonly
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
                                value="<?php echo htmlspecialchars($result_usuario['usu_nombres_apellidos'], ENT_QUOTES); ?>"
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
                                <option
                                  value="Sin definir"
                                  <?php if ($result_usuario['usu_genero'] == "Sin definir") echo "selected"; ?>
                                >Sin definir</option>
                                <option
                                  value="Mujer"
                                  <?php if ($result_usuario['usu_genero'] == "Mujer") echo "selected"; ?>
                                >Mujer</option>
                                <option
                                  value="Hombre"
                                  <?php if ($result_usuario['usu_genero'] == "Hombre") echo "selected"; ?>
                                >Hombre</option>
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
                                value="<?php echo htmlspecialchars($result_usuario['usu_fecha_nacimiento'], ENT_QUOTES); ?>"
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
                                value="<?php echo htmlspecialchars($result_usuario['usu_fecha_incorporacion'], ENT_QUOTES); ?>"
                                required
                              >
                            </div>
                          </div>

                          <!-- Fecha ingreso área (usu_fecha_ingreso_piloto) -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_ingreso_area" class="my-0">Fecha ingreso área</label>
                              <input
                                type="date"
                                class="form-control form-control-sm font-size-11"
                                name="fecha_ingreso_area"
                                id="fecha_ingreso_area"
                                value="<?php echo htmlspecialchars($result_usuario['usu_fecha_ingreso_piloto'], ENT_QUOTES); ?>"
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
                                <?php
                                  $estados = ["Activo","Inactivo","Retirado","Bloqueado","Eliminado"];
                                  foreach ($estados as $e):
                                ?>
                                  <option
                                    value="<?php echo $e; ?>"
                                    <?php if ($result_usuario['usu_estado'] == $e) echo "selected"; ?>
                                  >
                                    <?php echo $e; ?>
                                  </option>
                                <?php endforeach; ?>
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
                                value="<?php echo htmlspecialchars($result_usuario['usu_acceso'], ENT_QUOTES); ?>"
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
                                value="<?php echo htmlspecialchars($result_usuario['usu_usuario_red'], ENT_QUOTES); ?>"
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
                                value="<?php echo htmlspecialchars($result_usuario['usu_correo_corporativo'], ENT_QUOTES); ?>"
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
                                    <?php if ($result_usuario['usu_ciudad'] == $row_ciudad[0]) echo "selected"; ?>
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
                                    <?php if ($valor_sede == $row_ubic[0]) echo "selected"; ?>
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
                                    <?php if ($valor_campania == $row_camp[0]) echo "selected"; ?>
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
                                    <?php if (in_array($rol_item, $array_cargo_rol)) echo "selected"; ?>
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
                                    <?php if ($result_usuario['usu_supervisor'] == $row_sup[0]) echo "selected"; ?>
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
                                <option
                                  value="Activo"
                                  <?php if ($valor_reparto == "Activo") echo "selected"; ?>
                                >Activo</option>
                                <option
                                  value="Inactivo"
                                  <?php if ($valor_reparto == "Inactivo") echo "selected"; ?>
                                >Inactivo</option>
                              </select>
                            </div>
                          </div>

                        </div> <!-- .row principal -->
                      </div> <!-- .card-body -->
                    </div> <!-- .card -->
                  </div> <!-- .col-12 -->
                </div> <!-- .row flex-grow -->
              </div> <!-- .col-lg-6 izquierda -->

              <!-- Segunda columna: tabla de módulos y botones -->
              <div class="col-lg-5 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">

                          <!-- Tabla de módulos y permisos -->
                          <div class="col-md-12 mb-3">
                            <table class="table table-bordered table-striped table-hover table-sm">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Módulo</th>
                                  <th class="px-1 py-2">Permiso</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($resultado_registros_modulos as $modulo_row):
                                  $mod_id      = $modulo_row[0];
                                  $mod_nombre  = $modulo_row[1];
                                  $perfil_actual = $array_permisos[$mod_id] ?? "";
                                ?>
                                  <tr>
                                    <td class="p-1 font-size-11">
                                      <?php echo htmlspecialchars($mod_nombre, ENT_QUOTES); ?>
                                    </td>
                                    <td class="p-1 font-size-11">
                                      <select
                                        class="form-control form-control-sm form-select font-size-11"
                                        name="modulo_permiso[]"
                                      >
                                        <option value="<?php echo $mod_id . "|"; ?>"
                                          <?php if ($perfil_actual == "") echo "selected"; ?>
                                        >Seleccione</option>
                                        <option value="<?php echo $mod_id . "|Visitante"; ?>"
                                          <?php if ($perfil_actual == "Visitante") echo "selected"; ?>
                                        >Visitante</option>
                                        <option value="<?php echo $mod_id . "|Cliente"; ?>"
                                          <?php if ($perfil_actual == "Cliente") echo "selected"; ?>
                                        >Cliente</option>
                                        <option value="<?php echo $mod_id . "|Usuario"; ?>"
                                          <?php if ($perfil_actual == "Usuario") echo "selected"; ?>
                                        >Usuario</option>
                                        <option value="<?php echo $mod_id . "|Supervisor"; ?>"
                                          <?php if ($perfil_actual == "Supervisor") echo "selected"; ?>
                                        >Supervisor</option>
                                        <option value="<?php echo $mod_id . "|Formador"; ?>"
                                          <?php if ($perfil_actual == "Formador") echo "selected"; ?>
                                        >Formador</option>
                                        <option value="<?php echo $mod_id . "|Gestor"; ?>"
                                          <?php if ($perfil_actual == "Gestor") echo "selected"; ?>
                                        >Gestor</option>
                                        <option value="<?php echo $mod_id . "|Administrador"; ?>"
                                          <?php if ($perfil_actual == "Administrador") echo "selected"; ?>
                                        >Administrador</option>
                                      </select>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>

                          <!-- Botones Guardar / Reset contraseña / Finalizar / Cancelar -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <button
                                class="btn btn-success float-end ms-1"
                                type="submit"
                                name="guardar_registro"
                              >Guardar</button>
                              <button
                                class="btn btn-warning float-end ms-1"
                                type="submit"
                                name="reset_contrasena"
                              >Reset contraseña</button>
                              <?php if (isset($_POST["guardar_registro"]) || isset($_POST["reset_contrasena"])): ?>
                                <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                <button
                                  class="btn btn-danger float-end"
                                  type="button"
                                  onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');"
                                >Cancelar</button>
                              <?php endif; ?>
                            </div>
                          </div>

                        </div> <!-- .row secundaria -->
                      </div> <!-- .card-body secundaria -->
                    </div> <!-- .card secundaria -->
                  </div> <!-- .col-12 secundaria -->
                </div> <!-- .row flex-grow secundaria -->
              </div> <!-- .col-lg-5 derecha -->
            </div> <!-- .row justify-content-center -->
          </form>
        </div> <!-- .content-wrapper -->
      </div> <!-- .main-panel -->
    </div> <!-- .container-fluid -->
  </div> <!-- .container-scroller -->

  <?php require_once(ROOT . 'includes/_js.php'); ?>
</body>
</html>
