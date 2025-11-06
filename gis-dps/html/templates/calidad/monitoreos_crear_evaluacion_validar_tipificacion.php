<?php
// Validación de permisos del usuario para el módulo
$modulo_plataforma = "Calidad-Monitoreos";
require_once("../../iniciador.php");

// Intento de asegurar charset (si iniciador no lo hace)
if (isset($enlace_db) && $enlace_db instanceof mysqli) {
    @$enlace_db->set_charset('utf8mb4');
}

// Cabecera (AJAX/html fragment)
header('Content-Type: text/html; charset=utf-8');

// --- Obtener y validar inputs ---
$raw_programa = $_POST['id2'] ?? null; // gcmt_programa
$raw_tipificacion = $_POST['id'] ?? null; // gcmt_tipificacion

// Si falta alguno, devolvemos la opción vacía (compatibilidad)
if ($raw_programa === null || $raw_tipificacion === null) {
    echo '<option value=""></option>';
    exit;
}

// Normalizar y detectar tipo: preferimos números si vienen como dígitos
$maxLen = 100; 
$programa_is_int = ctype_digit((string)$raw_programa);
$tipificacion_is_int = ctype_digit((string)$raw_tipificacion);

if ($programa_is_int) {
    $programa_val = (int)$raw_programa;
} else {
    $programa_val = mb_substr(trim((string)$raw_programa), 0, $maxLen);
}

if ($tipificacion_is_int) {
    $tipificacion_val = (int)$raw_tipificacion;
} else {
    $tipificacion_val = mb_substr(trim((string)$raw_tipificacion), 0, $maxLen);
}

// --- Preparar SQL con placeholders ---
$sql = "SELECT DISTINCT `gcmt_subtipificacion`
        FROM `gestion_calidad_monitoreo_tipificacion`
        WHERE `gcmt_programa` = ? AND `gcmt_tipificacion` = ?
        ORDER BY `gcmt_subtipificacion` ASC";

$stmt = $enlace_db->prepare($sql);
if ($stmt === false) {
    // Falla prepare -> devolver opción vacía y registrar si se desea
    echo '<option value=""></option>';
    // error_log("[VALIDAR_TIPIFICACION] prepare failed: " . $enlace_db->error);
    exit;
}

// Determinar tipos para bind_param
$typeA = $programa_is_int ? 'i' : 's';
$typeB = $tipificacion_is_int ? 'i' : 's';
$types = $typeA . $typeB;

// Bind de parámetros según tipo
if ($types === 'ii') {
    $stmt->bind_param('ii', $programa_val, $tipificacion_val);
} elseif ($types === 'is') {
    $stmt->bind_param('is', $programa_val, $tipificacion_val);
} elseif ($types === 'si') {
    $stmt->bind_param('si', $programa_val, $tipificacion_val);
} else { // 'ss'
    $stmt->bind_param('ss', $programa_val, $tipificacion_val);
}

// Ejecutar
$stmt->execute();

// Intentar usar get_result (mysqlnd). Si no está, se usa fallback con bind_result/fetch.
$result = null;
if (method_exists($stmt, 'get_result')) {
    $result = $stmt->get_result();
    if ($result === false) {
        // En caso improbable de error
        echo '<option value=""></option>';
        $stmt->close();
        exit;
    }

    $rows = $result->fetch_all(MYSQLI_NUM);

    echo '<option value=""></option>' . PHP_EOL;
    foreach ($rows as $r) {
        $valor = (string) $r[0];
        $safeVal = htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        echo "  <option value=\"{$safeVal}\">{$safeVal}</option>" . PHP_EOL;
    }

    $result->free();
    $stmt->close();
    exit;
}

// Fallback si no hay get_result()
$stmt->bind_result($gcmt_subtipificacion);

echo '<option value=""></option>' . PHP_EOL;
while ($stmt->fetch()) {
    $valor = (string) $gcmt_subtipificacion;
    $safeVal = htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo "  <option value=\"{$safeVal}\">{$safeVal}</option>" . PHP_EOL;
}

$stmt->close();
?>
