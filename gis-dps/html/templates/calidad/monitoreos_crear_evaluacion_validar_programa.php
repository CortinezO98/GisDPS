<?php

$modulo_plataforma = "Calidad-Monitoreos";
require_once("../../iniciador.php");


if (isset($enlace_db) && $enlace_db instanceof mysqli) {
    @$enlace_db->set_charset('utf8mb4');
}

header('Content-Type: text/html; charset=utf-8');

$raw_id = $_POST['id'] ?? null;

if ($raw_id === null || $raw_id === '') {
    echo '<option value=""></option>';
    exit;
}


if (ctype_digit((string)$raw_id)) {
    $programa_val = (int)$raw_id;
    $param_type = 'i';
} else {
    $programa_val = mb_substr(trim((string)$raw_id), 0, 100);
    $param_type = 's';
}


$sql = "SELECT DISTINCT `gcmt_tipificacion`
        FROM `gestion_calidad_monitoreo_tipificacion`
        WHERE `gcmt_programa` = ?
        ORDER BY `gcmt_tipificacion` ASC";

$stmt = $enlace_db->prepare($sql);
if ($stmt === false) {
    echo '<option value=""></option>';
    exit;
}


if ($param_type === 'i') {
    $stmt->bind_param('i', $programa_val);
} else {
    $stmt->bind_param('s', $programa_val);
}

// Ejecutar
$stmt->execute();


if (method_exists($stmt, 'get_result')) {
    $result = $stmt->get_result();
    if ($result === false) {
        echo '<option value=""></option>';
        $stmt->close();
        exit;
    }

    $rows = $result->fetch_all(MYSQLI_NUM);

    echo '<option value=""></option>' . PHP_EOL;
    foreach ($rows as $r) {
        $valor = (string)$r[0];
        $safeVal = htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        echo "  <option value=\"{$safeVal}\">{$safeVal}</option>" . PHP_EOL;
    }

    $result->free();
    $stmt->close();
    exit;
}


$stmt->bind_result($gcmt_tipificacion);

echo '<option value=""></option>' . PHP_EOL;
while ($stmt->fetch()) {
    $valor = (string)$gcmt_tipificacion;
    $safeVal = htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo "  <option value=\"{$safeVal}\">{$safeVal}</option>" . PHP_EOL;
}

$stmt->close();
?>
