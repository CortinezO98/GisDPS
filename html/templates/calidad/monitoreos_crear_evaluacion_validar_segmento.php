<?php
// Validación de permisos del usuario para el módulo
$modulo_plataforma = "Calidad-Monitoreos";
require_once("../../iniciador.php");

// Aceptar solo peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método no permitido
    exit;
}

$id_tema = isset($_POST['id']) ? trim($_POST['id']) : '';


if ($id_tema === '') {
    echo '<option value=""></option>';
    exit;
}


$sql = "
    SELECT DISTINCT `gcmtv_tabulacion`
    FROM `gestion_calidad_monitoreo_tipificacion_voc`
    WHERE `gcmtv_segmento` = ?
    ORDER BY `gcmtv_tabulacion` ASC
";

$stmt = mysqli_prepare($enlace_db, $sql);

if ($stmt === false) {
    echo '<option value=""></option>';
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $id_tema);

if (!mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);
    echo '<option value=""></option>';
    exit;
}

$result = mysqli_stmt_get_result($stmt);

// Siempre devolvemos primero la opción vacía
echo '<option value=""></option>' . "\n";


if ($result) {
    while ($row = mysqli_fetch_row($result)) {
        $tabulacion = htmlspecialchars($row[0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        echo '<option value="' . $tabulacion . '">' . $tabulacion . '</option>' . "\n";
    }
}

mysqli_stmt_close($stmt);
?>
