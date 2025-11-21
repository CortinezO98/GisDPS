<?php
// Validación de permisos del usuario para el módulo
$modulo_plataforma = "Calidad-Monitoreos";
require_once("../../iniciador.php");

// Aceptar solo peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método no permitido
    exit;
}

// Obtener y limpiar parámetros de entrada
$id_tema    = isset($_POST['id2']) ? trim($_POST['id2']) : '';
$id_subtema = isset($_POST['id'])  ? trim($_POST['id'])  : '';

// Si faltan datos, devolvemos solo la opción vacía
if ($id_tema === '' || $id_subtema === '') {
    echo '<option value=""></option>';
    exit;
}

// ===== Consulta segura con prepared statements (evita SQLi) =====
$sql = "
    SELECT DISTINCT `gcmt_subtipificacion`
    FROM `gestion_calidad_monitoreo_tipificacion`
    WHERE `gcmt_programa` = ?
      AND `gcmt_tipificacion` = ?
";

$stmt = mysqli_prepare($enlace_db, $sql);

if ($stmt === false) {
    // Opcional: puedes loguear el error con error_log(mysqli_error($enlace_db));
    echo '<option value=""></option>';
    exit;
}

// Usamos 'ss' (string, string); si estos campos fueran numéricos podrías usar 'ii'
mysqli_stmt_bind_param($stmt, 'ss', $id_tema, $id_subtema);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    echo '<option value=""></option>';
    exit;
}

$result = mysqli_stmt_get_result($stmt);

// Siempre devolvemos primero la opción vacía
echo '<option value=""></option>' . "\n";

// ===== Salida segura (evita Stored XSS) =====
if ($result) {
    while ($row = mysqli_fetch_row($result)) {
        // gcmt_subtipificacion está en $row[0]
        // htmlspecialchars evita que contenido almacenado pueda inyectar HTML/JS
        $subtipificacion = htmlspecialchars($row[0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        echo '<option value="' . $subtipificacion . '">' . $subtipificacion . '</option>' . "\n";
    }
}

mysqli_stmt_close($stmt);
?>
