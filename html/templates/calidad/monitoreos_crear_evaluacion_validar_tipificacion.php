<?php
	//Validación de permisos del usuario para el módulo
  $modulo_plataforma="Calidad-Monitoreos";
  require_once("../../iniciador.php");
  
	$id_tema = $_POST["id2"];
	$id_subtema = $_POST["id"];
	$consulta = mysqli_query($enlace_db, "SELECT DISTINCT `gcmt_subtipificacion` FROM `gestion_calidad_monitoreo_tipificacion` WHERE `gcmt_programa`='".$id_tema."' AND `gcmt_tipificacion`='".$id_subtema."'");
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