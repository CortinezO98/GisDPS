<?php
    //Validación de permisos del usuario para el módulo
    require_once("../../iniciador_index.php");
    require_once("../../app/functions/validar_festivos.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

    /*DEFINICIÓN DE VARIABLES*/
    $validacion=validar_input($_GET['validacion']);

    if ($validacion=='municipio') {
        $filtro=validar_input($_GET['filtro']);
        
        $consulta_string="SELECT DISTINCT `gcpa_municipio`, TC.`ciu_municipio` FROM `gestion_citas_punto_atencion` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_citas_punto_atencion`.`gcpa_municipio`=TC.`ciu_codigo` WHERE `gcpa_estado`='Activo' AND TC.`ciu_departamento`=? ORDER BY TC.`ciu_municipio`";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $filtro);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $resultado_data='<option value="" class="font-size-11">Seleccione</option>';

        if (count($resultado_registros)>0) {
            $resultado_control=1;
            for ($i=0; $i < count($resultado_registros); $i++) { 
                $resultado_data.='<option value="'.$resultado_registros[$i][0].'" class="">'.$resultado_registros[$i][1].'</option>';
            }
        } else {
            $resultado_control=0;
        }
    }

    $data = array(
        "resultado" => $resultado_data,
        "resultado_control" => $resultado_control,
        
    );

    echo json_encode($data);
?>