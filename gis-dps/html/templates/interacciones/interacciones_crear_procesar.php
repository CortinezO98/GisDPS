<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Interacciones";
    require_once("../../iniciador.php");
    $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
    /*DEFINICIÓN DE VARIABLES*/
    $validacion=validar_input($_GET['validacion']);

    if ($validacion=='programa') {
        $nivel1=validar_input($_GET['nivel1']);
        
        $consulta_string="SELECT `gic2_id`, `gic2_padre`, `gic2_item`, `gic2_estado`, `gic2_registro_usuario`, `gic2_registro_fecha` FROM `gestion_interacciones_catnivel2` WHERE `gic2_estado`='Activo' AND `gic2_padre`=? ORDER BY `gic2_item` ASC";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $nivel1);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $resultado_data='';

        if (count($resultado_registros)>0) {
            $resultado_control=1;
            for ($i=0; $i < count($resultado_registros); $i++) { 
                $resultado_data.='<option class="font-size-11 py-0" value="'.$resultado_registros[$i][0].'" class="font-size-11">'.$resultado_registros[$i][2].'</option>';
            }
        } else {
            $resultado_control=0;
        }
    }

    if ($validacion=='tipificacion') {
        $nivel2=validar_input($_GET['nivel2']);
        
        $consulta_string="SELECT `gic3_id`, `gic3_padre`, `gic3_item`, `gic3_estado`, `gic3_registro_usuario`, `gic3_registro_fecha` FROM `gestion_interacciones_catnivel3` WHERE `gic3_estado`='Activo' AND `gic3_padre`=? ORDER BY `gic3_item` ASC";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $nivel2);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        $resultado_data='';

        if (count($resultado_registros)>0) {
            $resultado_control=1;
            for ($i=0; $i < count($resultado_registros); $i++) { 
                $resultado_data.='<option class="font-size-11 py-0" value="'.$resultado_registros[$i][0].'" class="font-size-11">'.$resultado_registros[$i][2].'</option>';
            }
        } else {
            $resultado_control=0;
        }
    }

    // if ($validacion=='subtipificacion_1') {
    //     $nivel3=validar_input($_GET['nivel3']);
        
    //     $consulta_string="SELECT `gic4_id`, `gic4_padre`, `gic4_item`, `gic4_estado`, `gic4_registro_usuario`, `gic4_registro_fecha` FROM `gestion_interacciones_catnivel4` WHERE `gic4_estado`='Activo' AND `gic4_padre`=? ORDER BY `gic4_item` ASC";
    //     $consulta_registros = $enlace_db->prepare($consulta_string);
    //     $consulta_registros->bind_param("s", $nivel3);
    //     $consulta_registros->execute();
    //     $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    //     $resultado_data='';

    //     if (count($resultado_registros)>0) {
    //         $resultado_control=1;
    //         for ($i=0; $i < count($resultado_registros); $i++) { 
    //             $resultado_data.='<option class="font-size-11 py-0" value="'.$resultado_registros[$i][0].'" class="font-size-11">'.$resultado_registros[$i][2].'</option>';
    //         }
    //     } else {
    //         $resultado_control=0;
    //     }
    // }

    // if ($validacion=='subtipificacion_2') {
    //     $nivel4=validar_input($_GET['nivel4']);
        
    //     $consulta_string="SELECT `gic5_id`, `gic5_padre`, `gic5_item`, `gic5_estado`, `gic5_registro_usuario`, `gic5_registro_fecha` FROM `gestion_interacciones_catnivel5` WHERE `gic5_estado`='Activo' AND `gic5_padre`=? ORDER BY `gic5_item` ASC";
    //     $consulta_registros = $enlace_db->prepare($consulta_string);
    //     $consulta_registros->bind_param("s", $nivel4);
    //     $consulta_registros->execute();
    //     $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    //     $resultado_data='';

    //     if (count($resultado_registros)>0) {
    //         $resultado_control=1;
    //         for ($i=0; $i < count($resultado_registros); $i++) { 
    //             $resultado_data.='<option class="font-size-11 py-0" value="'.$resultado_registros[$i][0].'" class="font-size-11">'.$resultado_registros[$i][2].'</option>';
    //         }
    //     } else {
    //         $resultado_control=0;
    //     }
    // }

    // if ($validacion=='subtipificacion_3') {
    //     $nivel5=validar_input($_GET['nivel5']);
        
    //     $consulta_string="SELECT `gic6_id`, `gic6_padre`, `gic6_item`, `gic6_estado`, `gic6_registro_usuario`, `gic6_registro_fecha` FROM `gestion_interacciones_catnivel6` WHERE `gic6_estado`='Activo' AND `gic6_padre`=? ORDER BY `gic6_item` ASC";
    //     $consulta_registros = $enlace_db->prepare($consulta_string);
    //     $consulta_registros->bind_param("s", $nivel5);
    //     $consulta_registros->execute();
    //     $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    //     $resultado_data='';

    //     if (count($resultado_registros)>0) {
    //         $resultado_control=1;
    //         for ($i=0; $i < count($resultado_registros); $i++) { 
    //             $resultado_data.='<option class="font-size-11 py-0" value="'.$resultado_registros[$i][0].'" class="font-size-11">'.$resultado_registros[$i][2].'</option>';
    //         }
    //     } else {
    //         $resultado_control=0;
    //     }
    // }

    if ($validacion=='resultado') {
        $resultado=validar_input($_GET['resultado']);

        $resultado_data='';

        if ($resultado=='Exitoso') {
            $resultado_data.='<option class="font-size-11 py-0" value="NO RESUELTA EN PRIMER CONTACTO" class="font-size-11">NO RESUELTA EN PRIMER CONTACTO</option>';
            $resultado_data.='<option class="font-size-11 py-0" value="SOLUCIÓN EN PRIMER CONTACTO" class="font-size-11">SOLUCIÓN EN PRIMER CONTACTO</option>';
            $resultado_control=1;
        } elseif ($resultado=='No exitoso') {
            $resultado_data.='<option class="font-size-11 py-0" value="ABANDONA SALA" class="font-size-11">ABANDONA SALA</option>';
            $resultado_data.='<option class="font-size-11 py-0" value="LLAMADA/SMS DE BROMA" class="font-size-11">LLAMADA/SMS DE BROMA</option>';
            $resultado_data.='<option class="font-size-11 py-0" value="LLAMADA/ CHAT EQUIVOCADA" class="font-size-11">LLAMADA/ CHAT EQUIVOCADA</option>';
            $resultado_data.='<option class="font-size-11 py-0" value="LLAMADA/SMS O INTERACCIÓN DE PRUEBA " class="font-size-11">LLAMADA/SMS O INTERACCIÓN DE PRUEBA </option>';
            $resultado_data.='<option class="font-size-11 py-0" value="LLAMADA NO SE ESCUCHA" class="font-size-11">LLAMADA NO SE ESCUCHA</option>';
            $resultado_data.='<option class="font-size-11 py-0" value="SE CORTÓ LLAMADA" class="font-size-11">SE CORTÓ LLAMADA</option>';
            $resultado_control=1;
        }
    }

    if ($validacion=='descripcion_resultado') {
        $descripcion_resultado=validar_input($_GET['descripcion_resultado']);

        $resultado_data='';

        if ($descripcion_resultado=='NO RESUELTA EN PRIMER CONTACTO') {
            $resultado_data.='<option class="font-size-11 py-0" value="SE CORTÓ LLAMADA" class="font-size-11">SE CORTÓ LLAMADA</option>';
            $resultado_data.='<option class="font-size-11 py-0" value="NO SUPERA FILTRO" class="font-size-11">NO SUPERA FILTRO</option>';
            $resultado_data.='<option class="font-size-11 py-0" value="ABANDONA SALA DE CHAT" class="font-size-11">ABANDONA SALA DE CHAT</option>';
            $resultado_data.='<option class="font-size-11 py-0" value="SIN SISTEMA" class="font-size-11">SIN SISTEMA</option>';
            $resultado_control=1;
        } else {
            $resultado_control=0;
        }
    }

    if ($validacion=='datos_usuario') {
        $identificacion=validar_input($_GET['identificacion']);
        
        $consulta_string="SELECT `giu_identificacion`, `giu_tipo_documento`, `giu_primer_nombre`, `giu_segundo_nombre`, `giu_primer_apellido`, `giu_segundo_apellido`, `giu_fecha_nacimiento`, `giu_municipio`, `giu_telefono`, `giu_celular`, `giu_email`, `giu_direccion`, `giu_fecha_actualiza`, `giu_informacion_poblacional`, `giu_atencion_preferencial`, `giu_genero`, `giu_escolaridad` FROM `gestion_interacciones_usuarios` WHERE `giu_identificacion`=?";
        $consulta_registros = $enlace_db->prepare($consulta_string);
        $consulta_registros->bind_param("s", $identificacion);
        $consulta_registros->execute();
        $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

        if (count($resultado_registros)>0) {
            $resultado_data='<p class="alert alert-success p-1 font-size-11">¡Datos del usuario encontrados, por favor realice las actualizaciones necesarias!</p>';
            $resultado_informacion_poblacional=explode(';', $resultado_registros[0][13]);
            $resultado_atencion_preferencial=explode(';', $resultado_registros[0][14]);
            $resultado_genero=$resultado_registros[0][15];
            $resultado_escolaridad=$resultado_registros[0][16];
            $resultado_primer_nombre=$resultado_registros[0][2];
            $resultado_segundo_nombre=$resultado_registros[0][3];
            $resultado_primer_apellido=$resultado_registros[0][4];
            $resultado_segundo_apellido=$resultado_registros[0][5];
            $resultado_fecha_nacimiento=$resultado_registros[0][6];
            $resultado_municipio=$resultado_registros[0][7];
            $resultado_telefono=$resultado_registros[0][8];
            $resultado_celular=$resultado_registros[0][9];
            $resultado_email=$resultado_registros[0][10];
            $resultado_direccion=$resultado_registros[0][11];
            $resultado_control=1;
        } else {
            $resultado_data='<p class="alert alert-warning p-1 font-size-11">¡No se encontraron datos del usuario!</p>';
            $resultado_informacion_poblacional='';
            $resultado_atencion_preferencial='';
            $resultado_genero='';
            $resultado_escolaridad='';
            $resultado_primer_nombre='';
            $resultado_segundo_nombre='';
            $resultado_primer_apellido='';
            $resultado_segundo_apellido='';
            $resultado_fecha_nacimiento='';
            $resultado_municipio='';
            $resultado_telefono='';
            $resultado_celular='';
            $resultado_email='';
            $resultado_direccion='';
            $resultado_control=0;
        }
    } else {
        $resultado_informacion_poblacional='';
        $resultado_atencion_preferencial='';
        $resultado_genero='';
        $resultado_escolaridad='';
        $resultado_primer_nombre='';
        $resultado_segundo_nombre='';
        $resultado_primer_apellido='';
        $resultado_segundo_apellido='';
        $resultado_fecha_nacimiento='';
        $resultado_municipio='';
        $resultado_telefono='';
        $resultado_celular='';
        $resultado_email='';
        $resultado_direccion='';
    }

    $data = array(
        "resultado" => $resultado_data,
        "resultado_control" => $resultado_control,
        "resultado_informacion_poblacional" => $resultado_informacion_poblacional,
        "resultado_atencion_preferencial" => $resultado_atencion_preferencial,
        "resultado_genero" => $resultado_genero,
        "resultado_escolaridad" => $resultado_escolaridad,
        "resultado_primer_nombre" => $resultado_primer_nombre,
        "resultado_segundo_nombre" => $resultado_segundo_nombre,
        "resultado_primer_apellido" => $resultado_primer_apellido,
        "resultado_segundo_apellido" => $resultado_segundo_apellido,
        "resultado_fecha_nacimiento" => $resultado_fecha_nacimiento,
        "resultado_municipio" => $resultado_municipio,
        "resultado_telefono" => $resultado_telefono,
        "resultado_celular" => $resultado_celular,
        "resultado_email" => $resultado_email,
        "resultado_direccion" => $resultado_direccion
    );

    echo json_encode($data);
?>
