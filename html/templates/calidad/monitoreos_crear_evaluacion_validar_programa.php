<?php
	//Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Monitoreos";
  require_once("../../iniciador.php");
	$id_tema = $_POST["id"];
	$consulta = mysqli_query($enlace_db, "SELECT DISTINCT `gcmt_tipificacion` FROM `gestion_calidad_monitoreo_tipificacion` WHERE `gcmt_programa`='".$id_tema."' ORDER BY `gcmt_tipificacion` ASC");
	$resultado = mysqli_fetch_all($consulta);
?>
	<option value=""></option>
<?php
	for ($i=0; $i < count($resultado); $i++) { 
?>
  <option value="<?php echo $resultado[$i][0]; ?>"><?php echo $resultado[$i][0]; ?></option>
<?php
	}
?>