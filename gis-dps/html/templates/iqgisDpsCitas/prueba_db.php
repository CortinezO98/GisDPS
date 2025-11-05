<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluimos la configuración de la base
require_once('app/config/db.php');

// Intentamos conectar con mysqli
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
    die("Error de conexión MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
} else {
    echo "Conexión exitosa a la base de datos '" . DB_NAME . "' usando usuario '" . DB_USER . "'<br>";
    // Contar cuántas tablas hay, por ejemplo
    $result = $mysqli->query("SHOW TABLES");
    echo "Tablas en la base: <ul>";
    while ($fila = $result->fetch_array()) {
        echo "<li>" . htmlentities($fila[0]) . "</li>";
    }
    echo "</ul>";
    $mysqli->close();
}
?>
