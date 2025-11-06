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


if (!ctype_digit((string)$raw_id)) {
    echo '<option value=""></option>';
    exit;
}

$segmento_id = (int) $raw_id;


$sql = "SELECT DISTINCT `gcmtv_tabulacion`
        FROM `gestion_calidad_monitoreo_tipificacion_voc`
        WHERE `gcmtv_segmento` = ?
        ORDER BY `gcmtv_tabulacion` ASC";

if (!($stmt = $enlace_db->prepare($sql))) {
    echo '<option value=""></option>';
    exit;
}

$stmt->bind_param('i', $segmento_id); 
$stmt->execute();

$result = $stmt->get_result();
if ($result === false) {
    $stmt->close();
    echo '<option value=""></option>';
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
?>
