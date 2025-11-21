<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) VALIDACIÓN DE PERMISOS DEL USUARIO (tu propia lógica)
$modulo_plataforma = "Administrador";
require_once("../../iniciador.php");
$url_fichero = pathinfo(__FILE__, PATHINFO_FILENAME);

// —————————————————————————————————————————————————————————————
// 3) CARGAR PhpSpreadsheet SIN “deprecated” DE libxml_disable_entity_loader()
// (deshabilitamos temporalmente los warnings deprecados)
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Asegúrate de que el autoload de Composer apunte a tu carpeta /vendor
require_once __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Ahora volvemos a mostrar errores normales
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
// —————————————————————————————————————————————————————————————

// 4) VARIABLES Y ARREGLOS PREVIOS
$title    = "Administrador";
$subtitle = "Usuarios | Crear Masivo";
$pagina   = validar_input($_GET['pagina'] ?? '');
$filtro_permanente = validar_input($_GET['id'] ?? '');
$url_salir = "usuarios?pagina={$pagina}&id={$filtro_permanente}";

// Array “cargo/rol” (solo los valores válidos)
$array_cargo = [
    'AGENTE GENERAL'                       => 1,
    'AGENTE TÉCNICO'                       => 1,
    'AGENTE ESPECIALIZADO'                 => 1,
    'AGENTE ESPECIALIZADO MINERO DE DATOS' => 1,
    'AGENTE GENERAL LENGUAJE DE SEÑAS'     => 1,
    'AGENTE INSCRIPCIÓN FA'                => 1,
    'AGENTE INSCRIPCIÓN FA CONSULTA'       => 1,
    'AGENTE DPS AGENDAMIENTO'              => 1,
    'AGENTE PROFESIONAL'                   => 1,
    'AGENTE TUTELAS'                       => 1,
    'AGENTE PRIORITARIOS'                  => 1,
    'AGENTE FUNCIONARIOS'                  => 1,
    'AGENTE CIUDADANOS'                    => 1,
    'AGENTE ENVÍO RADICADO A CIUDADANO'    => 1,
    'AGENTE NOTIFICACIONES DE CORREO'      => 1,
    'INTERPRETE'                           => 1,
    'FORMADOR'                             => 1,
    'COORDINADOR'                          => 1,
    'LÍDER DE CALIDAD Y FORMACIÓN'         => 1,
    'COORDINADOR NACIONAL'                 => 1,
    'PROFESIONAL DE OPERACIÓN ZONAL EN CAMPO' => 1,
    'ADMINISTRADOR PLATAFORMA'             => 1
];

// Array “género”
$array_genero = [
    'Sin definir' => 1,
    'Mujer'       => 1,
    'Hombre'      => 1
];

// Arreglo para acumular errores detallados fila a fila
$control_errores_detalle = [];

// Para “módulo_permiso[]” evitamos undefined index
$array_permisos = [];

// 5) CONSULTA DE SUPERVISORES (para validar que el usu_id exista)
$array_supervisor = [];
$sqlSuper = "
    SELECT `usu_id`, `usu_nombres_apellidos`
    FROM `administrador_usuario`
    ORDER BY `usu_nombres_apellidos`
";
$stmtSuper = $enlace_db->prepare($sqlSuper);
$stmtSuper->execute();
$rsSuper = $stmtSuper->get_result()->fetch_all(MYSQLI_NUM);
$stmtSuper->close();
foreach ($rsSuper as $fila) {
    // $fila[0] = usu_id, $fila[1] = usu_nombres_apellidos
    $array_supervisor[$fila[0]] = 1;
}

// 6) CONSULTA DE CAMPAÑAS (para mapear “área”)
$array_area = [];
$sqlCamp = "
    SELECT `ac_id`, `ac_nombre_campania`
    FROM `administrador_campania`
    ORDER BY `ac_nombre_campania`
";
$stmtCamp = $enlace_db->prepare($sqlCamp);
$stmtCamp->execute();
$rsCamp = $stmtCamp->get_result()->fetch_all(MYSQLI_NUM);
$stmtCamp->close();
foreach ($rsCamp as $fila) {
    // $fila[0] = ac_id, $fila[1] = ac_nombre_campania
    $array_area[$fila[1]] = $fila[0];
}

// 7) CONSULTA PARA VERIFICAR DUPLICADOS (usu_id o usu_acceso)
$sqlDup = "
    SELECT COUNT(`usu_id`)
    FROM `administrador_usuario`
    WHERE `usu_id` = ? OR `usu_acceso` = ?
";
$stmtDup = $enlace_db->prepare($sqlDup);
$stmtDup->bind_param("ss", $documento_identidad, $usuario_acceso);

// 8) SENTENCIA INSERT A administrador_usuario (24 columnas)
$sqlIns = "
    INSERT INTO `administrador_usuario` (
        `usu_id`, `usu_acceso`, `usu_contrasena`,
        `usu_nombres_apellidos`, `usu_correo_corporativo`, `usu_fecha_incorporacion`,
        `usu_campania`, `usu_usuario_red`, `usu_cargo_rol`,
        `usu_sede`, `usu_ciudad`, `usu_estado`,
        `usu_supervisor`, `usu_lider_calidad`, `usu_inicio_sesion`,
        `usu_piloto`, `usu_fecha_ingreso_piloto`, `usu_foto`,
        `usu_genero`, `usu_fecha_nacimiento`,
        `usu_modificacion_usuario`, `usu_modificacion_fecha`,
        `usu_ultimo_acceso`, `usu_reparto`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";
$stmtIns = $enlace_db->prepare($sqlIns);
$stmtIns->bind_param(
    'ssssssssssssssssssssssss',
    $documento_identidad,      //  1) usu_id
    $usuario_acceso,           //  2) usu_acceso
    $contrasena,               //  3) usu_contrasena (encriptada)
    $nombres_apellidos,        //  4) usu_nombres_apellidos
    $correo_corporativo,       //  5) usu_correo_corporativo
    $fecha_ingreso,            //  6) usu_fecha_incorporacion (del form)
    $campania,                 //  7) usu_campania (ac_id numérico)
    $usuario_red,              //  8) usu_usuario_red
    $cargo_rol,                //  9) usu_cargo_rol (texto)
    $ubicacion,                // 10) usu_sede (au_id numérico)
    $ciudad,                   // 11) usu_ciudad (ciu_codigo)
    $estado,                   // 12) usu_estado (texto)
    $supervisor,               // 13) usu_supervisor (usu_id o "")
    $lider_calidad,            // 14) usu_lider_calidad (texto o "")
    $inicio_sesion,            // 15) usu_inicio_sesion ("0")
    $piloto,                   // 16) usu_piloto (texto o "")
    $fecha_ingreso_area,       // 17) usu_fecha_ingreso_piloto (YYY-MM-DD)
    $foto,                     // 18) usu_foto (texto o "")
    $genero,                   // 19) usu_genero (Sin definir/Mujer/Hombre)
    $fecha_nacimiento,         // 20) usu_fecha_nacimiento (YYYY-MM-DD)
    $usu_modificacion_usuario, // 21) usu_modificacion_usuario (usu_id de quien crea)
    $usu_modificacion_fecha,   // 22) usu_modificacion_fecha (YYYY-MM-DD HH:MM:SS)
    $usu_ultimo_acceso,        // 23) usu_ultimo_acceso (YYYY-MM-DD HH:MM:SS)
    $usu_reparto               // 24) usu_reparto ("Inactivo")
);

// 9) PROCESAMIENTO AL SUBIR EL FORMULARIO
if (isset($_POST["guardar_registro"])) {

    // 9.1) ¿Llegó el archivo sin error?
    if (!isset($_FILES['documento']) || $_FILES['documento']["error"] > 0) {
        $message_error = "Problemas al cargar el archivo. Error de upload: " 
                          . ($_FILES['documento']["error"] ?? 'Desconocido');
    } else {
        // Carpeta temporal donde se guardará el .xlsx
        $nombre_directorio = __DIR__ . "/storage_temporal/";
        $nombre_archivo    = basename($_FILES['documento']['name']);
        $ruta_archivo      = $nombre_directorio . $nombre_archivo;

        // Si NO se ha procesado antes (sesión vacía)
        if (empty($_SESSION[APP_SESSION . '_registro_creado_usuario_masivo_administrador'])) {

            // 9.2) Movemos el archivo subido a la carpeta temporal
            if (move_uploaded_file($_FILES['documento']['tmp_name'], $ruta_archivo)) {

                // Verificamos que realmente exista
                if (file_exists($ruta_archivo)) {
                    // 9.2.1) Cargar el Excel
                    try {
                        $documentoXLSX = IOFactory::load($ruta_archivo);
                    } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                        die("Error al leer el Excel: " . $e->getMessage());
                    }

                    // Tomamos la primera hoja
                    $hojaActual  = $documentoXLSX->getSheet(0);
                    $ultimaFila  = $hojaActual->getHighestRow();

                    // Convertimos toda la hoja a un arreglo PHP bidimensional
                    // Cada fila será un array numérico: índice 0=Columna A, índice 1=Columna B, ...
                    $allData = $hojaActual->toArray(
                        null,   // valor por defecto si la celda está vacía
                        true,   // calcular fórmulas
                        true,   // formatear datos (p. ej. fechas)
                        true    // datos indexados por letra de columna
                    );
                    // Ahora $allData es un array asociativo: 
                    //   $allData[1]['A'], $allData[1]['B'], ...   (fila 1)
                    //   $allData[2]['A'], $allData[2]['B'], ...   (fila 2), etc.

                    // Variables de control
                    $control_fail    = 0;
                    $control_insert  = 0;
                    $array_data_base = [];

                    // 9.2.2) Recorrer filas (desde la 2 hasta la última)
                    for ($fila = 2; $fila <= $ultimaFila; $fila++) {

                        // — Inserción clave: SALTAR filas completamente vacías —
                        if (
                            trim((string)$allData[$fila]['A']) === '' &&
                            trim((string)$allData[$fila]['B']) === '' &&
                            trim((string)$allData[$fila]['C']) === '' &&
                            trim((string)$allData[$fila]['D']) === '' &&
                            trim((string)$allData[$fila]['E']) === '' &&
                            trim((string)$allData[$fila]['F']) === '' &&
                            trim((string)$allData[$fila]['G']) === '' &&
                            trim((string)$allData[$fila]['H']) === '' &&
                            trim((string)$allData[$fila]['I']) === '' &&
                            trim((string)$allData[$fila]['J']) === ''
                        ) {
                            continue; // saltar fila vacía
                        }

                        $registro = [];

                        //  1) Documento (usu_id) – obligatorio
                        $idRaw = trim((string)$allData[$fila]['A']);
                        if ($idRaw === "") {
                            $control_fail++;
                            $control_errores_detalle[] = "Fila {$fila}: Documento (usu_id) vacío.";
                            continue;
                        }
                        $registro['id'] = validar_input($idRaw);

                        //  2) Nombres y Apellidos – obligatorio
                        $nomRaw = trim((string)$allData[$fila]['B']);
                        if ($nomRaw === "") {
                            $control_fail++;
                            $control_errores_detalle[] = "Fila {$fila}: Nombres y Apellidos vacío.";
                            continue;
                        }
                        $registro['nombres'] = validar_input($nomRaw);

                        //  3) Correo – obligatorio y válido
                        $correoRaw = trim((string)$allData[$fila]['C']);
                        if (!filter_var($correoRaw, FILTER_VALIDATE_EMAIL)) {
                            $control_fail++;
                            $control_errores_detalle[] = "Fila {$fila}: Correo '{$correoRaw}' inválido.";
                            continue;
                        }
                        $registro['correo'] = validar_input($correoRaw);

                        //  4) UsuarioAcceso – obligatorio
                        $usrRaw = trim((string)$allData[$fila]['D']);
                        if ($usrRaw === "") {
                            $control_fail++;
                            $control_errores_detalle[] = "Fila {$fila}: UsuarioAcceso vacío.";
                            continue;
                        }
                        $registro['usuario'] = validar_input($usrRaw);

                        //  5) UsuarioRed – obligatorio
                        $usrRedRaw = trim((string)$allData[$fila]['E']);
                        if ($usrRedRaw === "") {
                            $control_fail++;
                            $control_errores_detalle[] = "Fila {$fila}: UsuarioRed vacío.";
                            continue;
                        }
                        $registro['usuario_red'] = validar_input($usrRedRaw);

                        //  6) Género – si viene vacío, forzamos “Sin definir”
                        $genRaw = trim((string)$allData[$fila]['F']);
                        if ($genRaw === "") {
                            $registro['genero'] = "Sin definir";
                        } else {
                            $genNorm = mb_strtoupper(mb_substr($genRaw, 0, 1))
                                     . mb_strtolower(mb_substr($genRaw, 1));
                            if (isset($array_genero[$genNorm])) {
                                $registro['genero'] = $genNorm;
                            } else {
                                $control_fail++;
                                $control_errores_detalle[] = "Fila {$fila}: Género '{$genRaw}' no válido.";
                                continue;
                            }
                        }

                        //  7) Fecha de nacimiento
                        $fecha_nacimiento = "";
                        try {
                            if (is_numeric($allData[$fila]['G'])) {
                                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($allData[$fila]['G']);
                                $fecha_nacimiento = $dt->format('Y-m-d');
                            } else {
                                $textoFech = trim((string)$allData[$fila]['G']);
                                $dtObj = new DateTime($textoFech);
                                $fecha_nacimiento = $dtObj->format('Y-m-d');
                            }
                            $registro['nacimiento'] = $fecha_nacimiento;
                        } catch (\Exception $e) {
                            $control_fail++;
                            $control_errores_detalle[] = "Fila {$fila}: FechaNacimiento '{$allData[$fila]['G']}' no válida.";
                            continue;
                        }

                        //  8) Área (campaña)
                        $areaRaw = trim((string)$allData[$fila]['H']);
                        if (!isset($array_area[$areaRaw])) {
                            $control_fail++;
                            $control_errores_detalle[] = "Fila {$fila}: Área '{$areaRaw}' no existe.";
                            continue;
                        }
                        $registro['area'] = $array_area[$areaRaw];

                        //  9) Cargo/Rol
                        $crRaw = trim((string)$allData[$fila]['I']);
                        if (!isset($array_cargo[$crRaw])) {
                            $control_fail++;
                            $control_errores_detalle[] = "Fila {$fila}: Cargo/Rol '{$crRaw}' no válido.";
                            continue;
                        }
                        $registro['cargo'] = $crRaw;

                        // 10) Responsable (Supervisor)
                        $supRaw = trim((string)$allData[$fila]['J']);
                        if ($supRaw === "") {
                            $registro['responsable'] = "";
                        } else {
                            if (!isset($array_supervisor[$supRaw])) {
                                $control_fail++;
                                $control_errores_detalle[] = "Fila {$fila}: Supervisor '{$supRaw}' no existe.";
                                continue;
                            }
                            $registro['responsable'] = $supRaw;
                        }

                        // Si llegó hasta aquí, la fila es válida
                        $array_data_base[] = $registro;
                    } // fin del for
                    // 9.3) Si no hubo errores de validación, insertamos fila a fila
                    if ($control_fail === 0) {
                        foreach ($array_data_base as $datosFila) {
                            // 9.3.1) Preparar valores para duplicados
                            $documento_identidad = $datosFila['id'];
                            $usuario_acceso      = $datosFila['usuario'];

                            // Ejecutar consulta duplicado
                            $stmtDup->execute();
                            $stmtDup->bind_result($count_duplicados);
                            $stmtDup->fetch();
                            $stmtDup->free_result();

                            if (intval($count_duplicados) === 0) {
                                // 9.3.2) Generar nueva contraseña aleatoria
                                $nueva_contrasena = generatePassword(10);
                                $salt = substr(base64_encode(openssl_random_pseudo_bytes(22)), 0, 22);
                                $salt = strtr($salt, ['+' => '.']);
                                $contrasena = crypt($nueva_contrasena, '$2y$10$' . $salt);

                                // 9.3.3) Otros datos a insertar
                                $nombres_apellidos  = validar_input($datosFila['nombres']);
                                $correo_corporativo = validar_input($datosFila['correo']);
                                $usuario_red        = validar_input($datosFila['usuario_red']);
                                $genero             = validar_input($datosFila['genero']);
                                $fecha_nacimiento   = $datosFila['nacimiento'];
                                $campania           = $datosFila['area'];
                                $cargo_rol          = validar_input($datosFila['cargo']);
                                $supervisor         = validar_input($datosFila['responsable']);

                                // Campos tomados del formulario
                                $fecha_ingreso      = validar_input($_POST['fecha_ingreso'] ?? '');
                                $fecha_ingreso_area = validar_input($_POST['fecha_ingreso_area'] ?? '');
                                $ciudad             = validar_input($_POST['ciudad'] ?? '');
                                $ubicacion          = validar_input($_POST['ubicacion'] ?? '');
                                $estado             = "Activo";

                                // Campos fijos o vacíos
                                $piloto             = "";
                                $foto               = "";
                                $lider_calidad      = "";
                                $inicio_sesion      = "0";
                                $usu_reparto        = "Inactivo";

                                $usu_modificacion_usuario = $_SESSION[APP_SESSION . '_session_usu_id'] ?? 'system';
                                $usu_modificacion_fecha   = date('Y-m-d H:i:s');
                                $usu_ultimo_acceso        = date('Y-m-d H:i:s');

                                // 9.3.4) Ejecutar INSERT en administrador_usuario
                                if (!$stmtIns->execute()) {
                                    $control_fail++;
                                    $control_errores_detalle[] = "Error al insertar usuario {$documento_identidad}: " 
                                                           . $enlace_db->error;
                                    continue;
                                }
                                $control_insert++;

                                // 9.3.5) Insertar permisos de módulo (si corresponde)
                                $modulo_permiso = $_POST['modulo_permiso'] ?? [];
                                foreach ($modulo_permiso as $perm) {
                                    // $perm = "123|Usuario" donde 123=mod_id, Usuario=perfil
                                    list($mod_id, $perfil) = explode("|", $perm, 2);
                                    $key_registro = $documento_identidad . $mod_id;
                                    $sqlMod = "
                                      INSERT INTO `administrador_usuario_modulo_perfil` (
                                          `per_id`, `per_usuario`, `per_modulo`, `per_perfil`
                                      ) VALUES (?, ?, ?, ?)
                                      ON DUPLICATE KEY UPDATE `per_perfil` = ?
                                    ";
                                    $stmMod = $enlace_db->prepare($sqlMod);
                                    $stmMod->bind_param(
                                        'sssss',
                                        $key_registro,
                                        $documento_identidad,
                                        $mod_id,
                                        $perfil,
                                        $perfil
                                    );
                                    $stmMod->execute();
                                    $stmMod->close();
                                }

                                // 9.3.6) Registrar en log
                                registro_log(
                                    $enlace_db,
                                    $modulo_plataforma,
                                    'crear',
                                    'Creación de usuario ' . $documento_identidad
                                    . ' - ' . $nombres_apellidos
                                );

                                // 9.3.7) Insertar contraseña en histórico
                                $sqlHist = "
                                    INSERT INTO `administrador_usuario_contrasenas` (
                                        `auc_usuario`, `auc_contrasena`
                                    ) VALUES (?, ?)
                                ";
                                $stmtHist = $enlace_db->prepare($sqlHist);
                                $stmtHist->bind_param('ss', $documento_identidad, $contrasena);
                                $stmtHist->execute();
                                $stmtHist->close();

                                // 9.3.8) Programar notificación de email
                                $asunto     = 'Credenciales de acceso - ' . APP_NAME;
                                $referencia = 'Credenciales de Acceso';
                                $contenido  = "
                                    <p style='font-size:12px; padding:5px; color:#666;'>
                                        Cordial saludo,<br><br>
                                        ¡Hemos generado las siguientes credenciales de acceso!
                                    </p>
                                    <center>
                                        <p style='font-size:12px; padding:5px; color:#666;'>
                                            <b>Nombres y Apellidos: </b> {$nombres_apellidos}
                                        </p>
                                        <p style='font-size:12px; padding:5px; color:#666;'>
                                            <b>Usuario:</b> {$usuario_acceso}
                                        </p>
                                        <p style='font-size:12px; padding:5px; color:#666;'>
                                            <b>Contraseña Temporal:</b> {$nueva_contrasena}
                                        </p>
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
                                    'Notificación de credenciales para usuario ' 
                                    . $documento_identidad
                                );

                            } else {
                                // Si ya existe ese usuario
                                $control_fail++;
                                $control_errores_detalle[] = "Usuario duplicado: {$documento_identidad}";
                            }
                        } // fin foreach filas

                        // 9.4) Comprobamos si todo se procesó
                        $totalProcesados = count($array_data_base);
                        if (($control_insert + $control_fail) === $totalProcesados) {
                            $respuesta_accion = "alertButton('success','Registro creado','Usuarios creados correctamente.');";
                            $_SESSION[APP_SESSION . '_registro_creado_usuario_masivo_administrador'] = 1;
                        } else {
                            $respuesta_accion = "alertButton('error','Error','Hubo problemas cargando algunos registros. Revisa los errores.');";
                        }
                    } else {
                        // Si hubo errores de validación antes de insertar
                        $respuesta_accion = "alertButton('error','Error','Problemas validando el archivo. Revisa los errores.');";
                    }
                } else {
                    $respuesta_accion = "alertButton('error','Error','El archivo subido no se encontró en el servidor.');";
                }
            } else {
                $respuesta_accion = "alertButton('error','Error','No se pudo mover el archivo al directorio temporal.');";
            }
        }
    }
} // fin de if(isset($_POST["guardar_registro"]))// 10) CONSULTAS PARA POBLAR SELECTS EN EL FORMULARIO

// Ciudades
$sqlCiu = "
    SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio`
    FROM `administrador_ciudades`
    ORDER BY `ciu_departamento`, `ciu_municipio`
";
$stmtCiu = $enlace_db->prepare($sqlCiu);
$stmtCiu->execute();
$resultado_registros_ciudad = $stmtCiu->get_result()->fetch_all(MYSQLI_NUM);
$stmtCiu->close();

// Supervisores (de nuevo, para el select si lo necesitas en el formulario)
$sqlSup2 = "
    SELECT `usu_id`, `usu_nombres_apellidos`
    FROM `administrador_usuario`
    ORDER BY `usu_nombres_apellidos`
";
$stmtSup2 = $enlace_db->prepare($sqlSup2);
$stmtSup2->execute();
$resultado_registros_supervisor = $stmtSup2->get_result()->fetch_all(MYSQLI_NUM);
$stmtSup2->close();

// Calidad (roles “LÍDER DE CALIDAD Y FORMACIÓN” o “Sistema”)
$sqlCal = "
    SELECT `usu_id`, `usu_nombres_apellidos`
    FROM `administrador_usuario`
    WHERE `usu_cargo_rol` = 'LÍDER DE CALIDAD Y FORMACIÓN'
       OR `usu_cargo_rol` = 'Sistema'
    ORDER BY `usu_nombres_apellidos`
";
$stmtCal = $enlace_db->prepare($sqlCal);
$stmtCal->execute();
$resultado_registros_calidad = $stmtCal->get_result()->fetch_all(MYSQLI_NUM);
$stmtCal->close();

// Ubicaciones
$sqlUbi = "
    SELECT `au_id`, `au_nombre_ubicacion`, `au_observaciones`
    FROM `administrador_ubicacion`
    ORDER BY `au_nombre_ubicacion`
";
$stmtUbi = $enlace_db->prepare($sqlUbi);
$stmtUbi->execute();
$resultado_registros_ubicacion = $stmtUbi->get_result()->fetch_all(MYSQLI_NUM);
$stmtUbi->close();

// Campañas (para el select, si lo necesitas)
$sqlCamp2 = "
    SELECT `ac_id`, `ac_nombre_campania`, `ac_observaciones`
    FROM `administrador_campania`
    ORDER BY `ac_nombre_campania`
";
$stmtCamp2 = $enlace_db->prepare($sqlCamp2);
$stmtCamp2->execute();
$resultado_registros_campania = $stmtCamp2->get_result()->fetch_all(MYSQLI_NUM);
$stmtCamp2->close();

// Módulos (para el listado de permisos)
$sqlMod = "
    SELECT `mod_id`, `mod_modulo_nombre`
    FROM `administrador_modulo`
    ORDER BY `mod_modulo_nombre`
";
$stmtMod = $enlace_db->prepare($sqlMod);
$stmtMod->execute();
$resultado_registros_modulos = $stmtMod->get_result()->fetch_all(MYSQLI_NUM);
$stmtMod->close();

?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT . 'includes/_head.php'); ?>
  <style>
    /* Ajuste para el input file */
    .custom-file-input {
      height: calc(1.5em + 0.75rem + 2px);
    }
  </style>
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

      <!-- Main panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center">

              <!--  Mensaje de alerta JavaScript  -->
              <?php 
                if (!empty($respuesta_accion)) {
                    echo "<script type='text/javascript'>{$respuesta_accion};</script>";
                }
              ?>

              <!-- Primera columna: campos fijos + upload del Excel -->
              <div class="col-lg-6 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">

                          <!-- Errores detallados -->
                          <div class="col-md-12">
                            <?php if (count($control_errores_detalle) > 0): ?>
                              <div class="alert alert-danger p-1">
                                <b>No es posible crear algunos usuarios, verifique:</b>
                                <ul class="ps-3 mb-0">
                                  <?php foreach ($control_errores_detalle as $err): ?>
                                  <li class="alert alert-warning p-1 font-size-11 my-1">
                                    <?php echo htmlspecialchars($err); ?>
                                  </li>
                                  <?php endforeach; ?>
                                </ul>
                              </div>
                            <?php endif; ?>
                          </div>

                          <!-- Estado -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="estado">Estado</label>
                              <select 
                                class="form-control form-control-sm form-select font-size-11" 
                                name="estado" 
                                id="estado" 
                                <?php if (!empty($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_administrador'])) echo 'disabled'; ?> 
                                required
                              >
                                <option value="">Seleccione</option>
                                <option value="Activo"
                                  <?php if (($_POST['estado'] ?? '') === "Activo") echo "selected"; ?>>
                                  Activo
                                </option>
                              </select>
                            </div>
                          </div>

                          <!-- Fecha ingreso -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_ingreso">Fecha ingreso</label>
                              <input 
                                type="date" 
                                class="form-control form-control-sm font-size-11" 
                                name="fecha_ingreso" 
                                id="fecha_ingreso" 
                                value="<?php echo $_POST['fecha_ingreso'] ?? ''; ?>" 
                                <?php if (!empty($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_administrador'])) echo 'readonly'; ?> 
                                required
                              >
                            </div>
                          </div>

                          <!-- Fecha ingreso área -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="fecha_ingreso_area">Fecha ingreso área</label>
                              <input 
                                type="date" 
                                class="form-control form-control-sm font-size-11" 
                                name="fecha_ingreso_area" 
                                id="fecha_ingreso_area" 
                                value="<?php echo $_POST['fecha_ingreso_area'] ?? ''; ?>" 
                                <?php if (!empty($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_administrador'])) echo 'readonly'; ?> 
                                required
                              >
                            </div>
                          </div>

                          <!-- Ciudad -->
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="ciudad">Ciudad</label>
                              <select 
                                class="form-control form-control-sm form-select font-size-11" 
                                name="ciudad" 
                                id="ciudad" 
                                <?php if (!empty($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_administrador'])) echo 'disabled'; ?> 
                                required
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($resultado_registros_ciudad as $filaCiu): ?>
                                <option 
                                  value="<?php echo htmlspecialchars($filaCiu[0]); ?>" 
                                  <?php if (($_POST['ciudad'] ?? '') === $filaCiu[0]) echo "selected"; ?>>
                                  <?php echo htmlspecialchars($filaCiu[2] . ", " . $filaCiu[1]); ?>
                                </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <!-- Ubicación -->
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="ubicacion">Ubicación</label>
                              <select 
                                class="form-control form-control-sm form-select font-size-11" 
                                name="ubicacion" 
                                id="ubicacion" 
                                <?php if (!empty($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_administrador'])) echo 'disabled'; ?> 
                                required
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($resultado_registros_ubicacion as $filaUbi): ?>
                                <option 
                                  value="<?php echo htmlspecialchars($filaUbi[0]); ?>" 
                                  <?php if (($_POST['ubicacion'] ?? '') === $filaUbi[0]) echo "selected"; ?>>
                                  <?php echo htmlspecialchars($filaUbi[1]); ?>
                                </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <!-- Base de usuarios (archivo Excel) -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="documento" class="my-0">Base de usuarios (.xlsx)</label>
                              <input 
                                class="form-control form-control-sm custom-file-input" 
                                name="documento" 
                                id="documento" 
                                type="file" 
                                accept=".xlsx" 
                                <?php if (!empty($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_administrador'])) echo 'disabled'; ?> 
                                required
                              >
                            </div>
                          </div>

                        </div> <!-- .row interna -->
                      </div> <!-- .card-body -->
                    </div> <!-- .card -->
                  </div> <!-- .col-12 -->
                </div> <!-- .row flex-grow -->
              </div> <!-- .col-lg-6 -->

              <!-- Segunda columna: módulos y permisos -->
              <div class="col-lg-5 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-12 mb-3">
                            <table class="table table-bordered table-striped table-hover table-sm">
                              <thead>
                                <tr>
                                  <th class="px-1 py-2">Módulo</th>
                                  <th class="px-1 py-2">Permiso</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($resultado_registros_modulos as $filaModulo): 
                                  list($mod_id, $mod_nombre) = $filaModulo;
                                ?>
                                  <tr>
                                    <td class="px-1 py-0 font-size-11">
                                      <?php echo htmlspecialchars($mod_nombre); ?>
                                    </td>
                                    <td class="p-0 font-size-11">
                                      <select 
                                        class="form-control form-control-sm form-select font-size-11 py-0 my-0" 
                                        style="height: 10px !important;" 
                                        name="modulo_permiso[]" 
                                        <?php if (!empty($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_administrador'])) echo 'disabled'; ?>
                                      >
                                        <option value="<?php echo $mod_id; ?>|">Seleccione</option>
                                        <option value="<?php echo $mod_id; ?>|Visitante"
                                          <?php if (($array_permisos[$mod_id] ?? '') === "Visitante") echo "selected"; ?>>
                                          Visitante
                                        </option>
                                        <option value="<?php echo $mod_id; ?>|Cliente"
                                          <?php if (($array_permisos[$mod_id] ?? '') === "Cliente") echo "selected"; ?>>
                                          Cliente
                                        </option>
                                        <option value="<?php echo $mod_id; ?>|Usuario"
                                          <?php if (($array_permisos[$mod_id] ?? '') === "Usuario") echo "selected"; ?>>
                                          Usuario
                                        </option>
                                        <option value="<?php echo $mod_id; ?>|Supervisor"
                                          <?php if (($array_permisos[$mod_id] ?? '') === "Supervisor") echo "selected"; ?>>
                                          Supervisor
                                        </option>
                                        <option value="<?php echo $mod_id; ?>|Formador"
                                          <?php if (($array_permisos[$mod_id] ?? '') === "Formador") echo "selected"; ?>>
                                          Formador
                                        </option>
                                        <option value="<?php echo $mod_id; ?>|Gestor"
                                          <?php if (($array_permisos[$mod_id] ?? '') === "Gestor") echo "selected"; ?>>
                                          Gestor
                                        </option>
                                      </select>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>

                          <!-- Botones Guardar / Cancelar -->
                          <div class="col-md-12">
                            <div class="form-group">
                              <?php if (!empty($_SESSION[APP_SESSION.'_registro_creado_usuario_masivo_administrador'])): ?>
                                <a href="<?php echo htmlspecialchars($url_salir); ?>" class="btn btn-dark float-end">
                                  Finalizar
                                </a>
                              <?php else: ?>
                                <button 
                                  class="btn btn-success float-end ms-1" 
                                  type="submit" 
                                  name="guardar_registro" 
                                  id="guardar_registro_btn"
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
                              <?php endif; ?>
                            </div>
                          </div>

                        </div> <!-- .row interna -->
                      </div> <!-- .card-body -->
                    </div> <!-- .card -->
                  </div> <!-- .col-12 -->
                </div> <!-- .row flex-grow -->
              </div> <!-- .col-lg-5 -->

            </div> <!-- .row justify-content-center -->
          </form>
        </div> <!-- content-wrapper termina -->
      </div> <!-- main-panel termina -->
    </div> <!-- page-body-wrapper -->
  </div> <!-- container-scroller -->

  <?php require_once(ROOT . 'includes/_js.php'); ?>
</body>
</html>
