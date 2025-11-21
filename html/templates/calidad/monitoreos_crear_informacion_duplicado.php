<?php 
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Calidad-Monitoreos";
    require_once("../../iniciador.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

	$id=validar_input($_GET['id']);
    if ($id!="") {
    	$consulta_string="SELECT `gcm_id`, `gcm_numero_transaccion`, `gcm_tipo_monitoreo` FROM `gestion_calidad_monitoreo` WHERE `gcm_numero_transaccion`=?";
	    $consulta_registros = $enlace_db->prepare($consulta_string);
	    $consulta_registros->bind_param("s", $id);
	    $consulta_registros->execute();
	    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

	    if (count($resultado_registros)>0) {
    	    echo "<p class='alert alert-danger p-1 text-center font-size-11'>El número de transacción tiene monitoreos asociados:<br>";
		    for ($i=0; $i < count($resultado_registros); $i++) {
 ?>
 				<?php echo $resultado_registros[$i][0]." | ".$resultado_registros[$i][1]." | ".$resultado_registros[$i][2]."<br>"; ?>
<?php 
 			}
 			echo "</p>";
 		}
    }
?>
