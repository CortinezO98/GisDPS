<?php
	$server=DB_HOST;
	$user=DB_USER;
	$password=DB_PASS;
	$db=DB_NAME;

	$enlace_db = new mysqli($server, $user, $password, $db);
	$acentos = mysqli_query($enlace_db, "SET NAMES 'utf8'");
	if ($enlace_db->connect_errno) {
	    echo "Fallo al conectar a Base de Datos: (" . $enlace_db->connect_errno . ") " . $enlace_db->connect_error;
	}
?>