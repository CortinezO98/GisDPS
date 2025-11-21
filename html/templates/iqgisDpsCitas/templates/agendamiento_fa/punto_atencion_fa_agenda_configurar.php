<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Agendamiento Citas FA-Punto Atención";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Agendamiento Citas FA";
  $subtitle = "Puntos de Atención | Configurar";
  $pagina=validar_input($_GET['pagina']);
  $filtro_permanente=validar_input($_GET['id']);
  $id_registro=validar_input(base64_decode($_GET['reg']));
  $semana=validar_input(base64_decode($_GET['sem']));
  $url_salir="punto_atencion_fa?pagina=".$pagina."&id=".$filtro_permanente;

  //AGREGAR NÚMERO AL DÍA, SEGÚN SEMANA SELECCIONADA
    $dias_semana = array();
    for ($i=0; $i < 7; $i++) { 
        array_push($dias_semana, date("Y-m-d", strtotime("first day", strtotime($semana . $i))));
    }
    for ($i=0; $i < count($dias_semana); $i++) { 
      $array_cupos_mostrar[$dias_semana[$i]]+=0;
    }

    $consulta_string="SELECT `gcpa_id`, `gcpa_regional`, `gcpa_municipio`, `gcpa_punto_atencion`, `gcpa_direccion`, `gcpa_estado`, `gcpa_registro_usuario`, `gcpa_registro_fecha`, TC.`ciu_departamento`, TC.`ciu_municipio`, TU.`usu_nombres_apellidos`, `gcpa_lunes`, `gcpa_martes`, `gcpa_miercoles`, `gcpa_jueves`, `gcpa_viernes`, `gcpa_sabado`, `gcpa_domingo` FROM `gestion_citasfa_punto_atencion` LEFT JOIN `administrador_ciudades` AS TC ON `gestion_citasfa_punto_atencion`.`gcpa_municipio`=TC.`ciu_codigo` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_punto_atencion`.`gcpa_registro_usuario`=TU.`usu_id` WHERE `gcpa_id`=?";

    $consulta_registros = $enlace_db->prepare($consulta_string);
    $consulta_registros->bind_param("s", $id_registro);
    $consulta_registros->execute();
    $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

    if(isset($_POST["guardar_registro"])){
      $cantidad_turnos_1=validar_input($_POST['cantidad_turnos_1']);
      $cantidad_turnos_2=validar_input($_POST['cantidad_turnos_2']);
      $cantidad_turnos_3=validar_input($_POST['cantidad_turnos_3']);
      $cantidad_turnos_4=validar_input($_POST['cantidad_turnos_4']);
      $cantidad_turnos_5=validar_input($_POST['cantidad_turnos_5']);
      $cantidad_turnos_6=validar_input($_POST['cantidad_turnos_6']);
      $cantidad_turnos_7=validar_input($_POST['cantidad_turnos_7']);
      $array_cupos=array();
      if ($cantidad_turnos_1!='' AND $cantidad_turnos_1>0 AND $resultado_registros[0][11]!='') {
        $horario_1=explode('-', $resultado_registros[0][11]);
        $horario_1_inicio=intval(date('H', strtotime($horario_1[0])));
        $horario_1_fin=intval(date('H', strtotime($horario_1[1])));
        $rangos=$horario_1_fin-$horario_1_inicio-1;
        $turnos_rango=ceil($cantidad_turnos_1/$rangos);
        $restante=$cantidad_turnos_1-($turnos_rango*($rangos-1));

        unset($array_cupos[$dias_semana[0]]);
        $control_hora=$horario_1_inicio;
        for ($i=0; $i < $rangos-1; $i++) {
          $array_cupos[$dias_semana[0]]['hora'][]=validar_cero($control_hora).':00';
          $control_hora++;
          $array_cupos[$dias_semana[0]]['cupos'][]=$turnos_rango;
        }

        $array_cupos[$dias_semana[0]]['hora'][]=validar_cero($control_hora).':00';
        $array_cupos[$dias_semana[0]]['cupos'][]=$restante;
      }

      if ($cantidad_turnos_2!='' AND $cantidad_turnos_2>0 AND $resultado_registros[0][12]!='') {
        $horario_1=explode('-', $resultado_registros[0][12]);
        $horario_1_inicio=intval(date('H', strtotime($horario_1[0])));
        $horario_1_fin=intval(date('H', strtotime($horario_1[1])));
        $rangos=$horario_1_fin-$horario_1_inicio-1;
        $turnos_rango=ceil($cantidad_turnos_2/$rangos);
        $restante=$cantidad_turnos_2-($turnos_rango*($rangos-1));

        unset($array_cupos[$dias_semana[1]]);
        $control_hora=$horario_1_inicio;
        for ($i=0; $i < $rangos-1; $i++) {
          $array_cupos[$dias_semana[1]]['hora'][]=validar_cero($control_hora).':00';
          $control_hora++;
          $array_cupos[$dias_semana[1]]['cupos'][]=$turnos_rango;
        }

        $array_cupos[$dias_semana[1]]['hora'][]=validar_cero($control_hora).':00';
        $array_cupos[$dias_semana[1]]['cupos'][]=$restante;
      }

      if ($cantidad_turnos_3!='' AND $cantidad_turnos_3>0 AND $resultado_registros[0][13]!='') {
        $horario_1=explode('-', $resultado_registros[0][13]);
        $horario_1_inicio=intval(date('H', strtotime($horario_1[0])));
        $horario_1_fin=intval(date('H', strtotime($horario_1[1])));
        $rangos=$horario_1_fin-$horario_1_inicio-1;
        $turnos_rango=ceil($cantidad_turnos_3/$rangos);
        $restante=$cantidad_turnos_3-($turnos_rango*($rangos-1));

        unset($array_cupos[$dias_semana[2]]);
        $control_hora=$horario_1_inicio;
        for ($i=0; $i < $rangos-1; $i++) {
          $array_cupos[$dias_semana[2]]['hora'][]=validar_cero($control_hora).':00';
          $control_hora++;
          $array_cupos[$dias_semana[2]]['cupos'][]=$turnos_rango;
        }

        $array_cupos[$dias_semana[2]]['hora'][]=validar_cero($control_hora).':00';
        $array_cupos[$dias_semana[2]]['cupos'][]=$restante;
      }

      if ($cantidad_turnos_4!='' AND $cantidad_turnos_4>0 AND $resultado_registros[0][14]!='') {
        $horario_1=explode('-', $resultado_registros[0][14]);
        $horario_1_inicio=intval(date('H', strtotime($horario_1[0])));
        $horario_1_fin=intval(date('H', strtotime($horario_1[1])));
        $rangos=$horario_1_fin-$horario_1_inicio-1;
        $turnos_rango=ceil($cantidad_turnos_4/$rangos);
        $restante=$cantidad_turnos_4-($turnos_rango*($rangos-1));

        unset($array_cupos[$dias_semana[3]]);
        $control_hora=$horario_1_inicio;
        for ($i=0; $i < $rangos-1; $i++) {
          $array_cupos[$dias_semana[3]]['hora'][]=validar_cero($control_hora).':00';
          $control_hora++;
          $array_cupos[$dias_semana[3]]['cupos'][]=$turnos_rango;
        }

        $array_cupos[$dias_semana[3]]['hora'][]=validar_cero($control_hora).':00';
        $array_cupos[$dias_semana[3]]['cupos'][]=$restante;
      }

      if ($cantidad_turnos_5!='' AND $cantidad_turnos_5>0 AND $resultado_registros[0][15]!='') {
        $horario_1=explode('-', $resultado_registros[0][15]);
        $horario_1_inicio=intval(date('H', strtotime($horario_1[0])));
        $horario_1_fin=intval(date('H', strtotime($horario_1[1])));
        $rangos=$horario_1_fin-$horario_1_inicio-1;
        $turnos_rango=ceil($cantidad_turnos_5/$rangos);
        $restante=$cantidad_turnos_5-($turnos_rango*($rangos-1));

        unset($array_cupos[$dias_semana[4]]);
        $control_hora=$horario_1_inicio;
        for ($i=0; $i < $rangos-1; $i++) {
          $array_cupos[$dias_semana[4]]['hora'][]=validar_cero($control_hora).':00';
          $control_hora++;
          $array_cupos[$dias_semana[4]]['cupos'][]=$turnos_rango;
        }

        $array_cupos[$dias_semana[4]]['hora'][]=validar_cero($control_hora).':00';
        $array_cupos[$dias_semana[4]]['cupos'][]=$restante;
      }

      if ($cantidad_turnos_6!='' AND $cantidad_turnos_6>0 AND $resultado_registros[0][16]!='') {
        $horario_1=explode('-', $resultado_registros[0][16]);
        $horario_1_inicio=intval(date('H', strtotime($horario_1[0])));
        $horario_1_fin=intval(date('H', strtotime($horario_1[1])));
        $rangos=$horario_1_fin-$horario_1_inicio-1;
        $turnos_rango=ceil($cantidad_turnos_6/$rangos);
        $restante=$cantidad_turnos_6-($turnos_rango*($rangos-1));

        unset($array_cupos[$dias_semana[5]]);
        $control_hora=$horario_1_inicio;
        for ($i=0; $i < $rangos-1; $i++) {
          $array_cupos[$dias_semana[5]]['hora'][]=validar_cero($control_hora).':00';
          $control_hora++;
          $array_cupos[$dias_semana[5]]['cupos'][]=$turnos_rango;
        }

        $array_cupos[$dias_semana[5]]['hora'][]=validar_cero($control_hora).':00';
        $array_cupos[$dias_semana[5]]['cupos'][]=$restante;
      }

      if ($cantidad_turnos_7!='' AND $cantidad_turnos_7>0 AND $resultado_registros[0][17]!='') {
        $horario_1=explode('-', $resultado_registros[0][17]);
        $horario_1_inicio=intval(date('H', strtotime($horario_1[0])));
        $horario_1_fin=intval(date('H', strtotime($horario_1[1])));
        $rangos=$horario_1_fin-$horario_1_inicio-1;
        $turnos_rango=ceil($cantidad_turnos_7/$rangos);
        $restante=$cantidad_turnos_7-($turnos_rango*($rangos-1));

        unset($array_cupos[$dias_semana[6]]);
        $control_hora=$horario_1_inicio;
        for ($i=0; $i < $rangos-1; $i++) {
          $array_cupos[$dias_semana[6]]['hora'][]=validar_cero($control_hora).':00';
          $control_hora++;
          $array_cupos[$dias_semana[6]]['cupos'][]=$turnos_rango;
        }

        $array_cupos[$dias_semana[6]]['hora'][]=validar_cero($control_hora).':00';
        $array_cupos[$dias_semana[6]]['cupos'][]=$restante;
      }

      $consulta_string_agenda="SELECT `gca_id`, `gca_punto`, `gca_usuario`, `gca_semana`, `gca_fecha`, `gca_hora`, `gca_estado`, `gca_estado_agenda`, `gca_observaciones`, `gca_actualiza_usuario`, `gca_actualiza_fecha`, `gca_registro_usuario`, `gca_registro_fecha` FROM `gestion_citasfa_agenda` WHERE `gca_punto`=? AND `gca_semana`=? AND `gca_fecha`=? AND `gca_estado_agenda`='Reservada'";

      $consulta_registros_agenda = $enlace_db->prepare($consulta_string_agenda);
      $consulta_registros_agenda->bind_param("sss", $id_registro, $semana, $gca_fecha);
      

      // Prepara la sentencia
      $sentencia_delete = $enlace_db->prepare("DELETE FROM `gestion_citasfa_agenda` WHERE `gca_punto`=? AND `gca_semana`=? AND `gca_fecha`=?");
      // Agrega variables a sentencia preparada
      $sentencia_delete->bind_param('sss', $id_registro, $semana, $gca_fecha);

      // Prepara la sentencia
      $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_citasfa_agenda`(`gca_id`, `gca_punto`, `gca_usuario`, `gca_semana`, `gca_fecha`, `gca_hora`, `gca_estado`, `gca_estado_agenda`, `gca_observaciones`, `gca_actualiza_usuario`, `gca_actualiza_fecha`, `gca_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

      // Agrega variables a sentencia preparada
      $sentencia_insert->bind_param('ssssssssssss', $gca_id, $gca_punto, $gca_usuario, $gca_semana, $gca_fecha, $gca_hora, $gca_estado, $gca_estado_agenda, $gca_observaciones, $gca_actualiza_usuario, $gca_actualiza_fecha, $gca_registro_usuario);
      
      $control_registro=0;
      $control_insert=0;
      for ($j=0; $j < count($dias_semana); $j++) {
        $gca_fecha=$dias_semana[$j];
        $consulta_registros_agenda->execute();
        $resultado_registros_agenda = $consulta_registros_agenda->get_result()->fetch_all(MYSQLI_NUM);
        if (count($resultado_registros_agenda)==0) {
          if ($sentencia_delete->execute()) {
            if (isset($array_cupos[$dias_semana[$j]]['hora'])) {
              for ($k=0; $k < count($array_cupos[$dias_semana[$j]]['hora']); $k++) { 
                for ($m=0; $m < $array_cupos[$dias_semana[$j]]['cupos'][$k]; $m++) { 
                  $gca_hora=$array_cupos[$dias_semana[$j]]['hora'][$k];
                  $gca_id=$dias_semana[$j].'-'.$id_registro.'-'.$gca_hora.'-'.$k.'-'.$m;
                  $gca_punto=$id_registro;
                  $gca_usuario=$id_registro;
                  $gca_semana=$semana;
                  $gca_fecha=$dias_semana[$j];
                  $gca_estado='Disponible';
                  $gca_estado_agenda='Disponible';
                  $gca_observaciones='';
                  $gca_actualiza_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
                  $gca_actualiza_fecha=date('Y-m-d H:i:s');
                  $gca_registro_usuario=$_SESSION[APP_SESSION.'_session_usu_id'];
                  $control_registro++;
                  
                  if ($sentencia_insert->execute()) {
                      $control_insert++;
                  } else {
                    // echo "INSERT INTO `gestion_citasfa_agenda`(`gca_id`, `gca_punto`, `gca_usuario`, `gca_semana`, `gca_fecha`, `gca_hora`, `gca_estado`, `gca_estado_agenda`, `gca_observaciones`, `gca_actualiza_usuario`, `gca_actualiza_fecha`, `gca_registro_usuario`) VALUES ('".$gca_id."','".$gca_punto."','".$gca_usuario."','".$gca_semana."','".$gca_fecha."','".$gca_hora."','".$gca_estado."','".$gca_estado_agenda."','".$gca_observaciones."','".$gca_actualiza_usuario."','".$gca_actualiza_fecha."','".$gca_registro_usuario."');<br>";
                  }
                }
              }
            }
            
          }
        }
      }

      // echo "<pre>";
      // print_r($array_cupos);
      // echo "</pre>";

      if ($control_insert==$control_registro) {
        $respuesta_accion = "alertButton('success', 'Registro editado', 'Registro editado exitosamente');";
      } else {
        $respuesta_accion = "alertButton('error', 'Error', 'Problemas al editar el registro');";
      }
    }

    $consulta_string_ciudad="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_departamento`, `ciu_municipio`";
    $consulta_registros_ciudad = $enlace_db->prepare($consulta_string_ciudad);
    $consulta_registros_ciudad->execute();
    $resultado_registros_ciudad = $consulta_registros_ciudad->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_usuario="SELECT `gcpau_id`, `gcpau_punto_atencion`, `gcpau_usuario`, `gcpau_lunes`, `gcpau_lunes_break_1`, `gcpau_lunes_break_2`, `gcpau_lunes_almuerzo`, `gcpau_martes`, `gcpau_martes_break_1`, `gcpau_martes_break_2`, `gcpau_martes_almuerzo`, `gcpau_miercoles`, `gcpau_miercoles_break_1`, `gcpau_miercoles_break_2`, `gcpau_miercoles_almuerzo`, `gcpau_jueves`, `gcpau_jueves_break_1`, `gcpau_jueves_break_2`, `gcpau_jueves_almuerzo`, `gcpau_viernes`, `gcpau_viernes_break_1`, `gcpau_viernes_break_2`, `gcpau_viernes_almuerzo`, `gcpau_sabado`, `gcpau_sabado_break_1`, `gcpau_sabado_break_2`, `gcpau_sabado_almuerzo`, `gcpau_domingo`, `gcpau_domingo_break_1`, `gcpau_domingo_break_2`, `gcpau_domingo_almuerzo`, TU.`usu_nombres_apellidos` FROM `gestion_citasfa_punto_atencion_usuario` LEFT JOIN `administrador_usuario` AS TU ON `gestion_citasfa_punto_atencion_usuario`.`gcpau_usuario`=TU.`usu_id` WHERE `gcpau_punto_atencion`=?";

    $consulta_registros_usuario = $enlace_db->prepare($consulta_string_usuario);
    $consulta_registros_usuario->bind_param("s", $id_registro);
    $consulta_registros_usuario->execute();
    $resultado_registros_usuario = $consulta_registros_usuario->get_result()->fetch_all(MYSQLI_NUM);

    $consulta_string_agenda="SELECT `gca_id`, `gca_punto`, `gca_usuario`, `gca_semana`, `gca_fecha`, `gca_hora`, `gca_estado`, `gca_estado_agenda`, `gca_observaciones`, `gca_actualiza_usuario`, `gca_actualiza_fecha`, `gca_registro_usuario`, `gca_registro_fecha` FROM `gestion_citasfa_agenda` WHERE `gca_punto`=? AND `gca_semana`=?";

    $consulta_registros_agenda = $enlace_db->prepare($consulta_string_agenda);
    $consulta_registros_agenda->bind_param("ss", $id_registro, $semana);
    $consulta_registros_agenda->execute();
    $resultado_registros_agenda = $consulta_registros_agenda->get_result()->fetch_all(MYSQLI_NUM);
    
    $agendados=0;
    for ($i=0; $i < count($resultado_registros_agenda); $i++) { 
      $array_cupos_mostrar[$resultado_registros_agenda[$i][4]]+=1;

      if ($resultado_registros_agenda[$i][7]=='Agendado') {
        $agendados++;
      }
    }
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <!-- navbar -->
    <?php require_once(ROOT.'includes/_navbar.php'); ?>
    <!-- navbar -->
    <div class="container-fluid page-body-wrapper">
      <!-- sidebar -->
      <?php require_once(ROOT.'includes/_sidebar.php'); ?>
      <!-- sidebar -->
      <!-- main-panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
          <div class="row justify-content-center">
            <?php if (!empty($respuesta_accion)) {echo "<script type='text/javascript'>".$respuesta_accion."</script>";} ?>
            <div class="col-lg-3 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="semana" class="my-0">Semana</label>
                              <input type="week" class="form-control form-control-sm font-size-11" name="semana" id="semana" maxlength="100" value="<?php echo $semana; ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                                <label for="estado" class="my-0">Estado</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="estado" id="estado" disabled>
                                  <option value="">Seleccione</option>
                                  <option value="Activo" <?php if($resultado_registros[0][5]=="Activo"){ echo "selected"; } ?>>Activo</option>
                                  <option value="Inactivo" <?php if($resultado_registros[0][5]=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                                <label for="regional" class="my-0">Regional</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="regional" id="regional" disabled>
                                  <option value="">Seleccione</option>
                                  <option value="REGIONAL AMAZONAS" <?php if($resultado_registros[0][1]=="REGIONAL AMAZONAS"){ echo "selected"; } ?>>REGIONAL AMAZONAS</option>
                                  <option value="REGIONAL ANTIOQUIA" <?php if($resultado_registros[0][1]=="REGIONAL ANTIOQUIA"){ echo "selected"; } ?>>REGIONAL ANTIOQUIA</option>
                                  <option value="REGIONAL ARAUCA" <?php if($resultado_registros[0][1]=="REGIONAL ARAUCA"){ echo "selected"; } ?>>REGIONAL ARAUCA</option>
                                  <option value="REGIONAL ATLÁNTICO" <?php if($resultado_registros[0][1]=="REGIONAL ATLÁNTICO"){ echo "selected"; } ?>>REGIONAL ATLÁNTICO</option>
                                  <option value="REGIONAL BOGOTÁ" <?php if($resultado_registros[0][1]=="REGIONAL BOGOTÁ"){ echo "selected"; } ?>>REGIONAL BOGOTÁ</option>
                                  <option value="REGIONAL BOLÍVAR" <?php if($resultado_registros[0][1]=="REGIONAL BOLÍVAR"){ echo "selected"; } ?>>REGIONAL BOLÍVAR</option>
                                  <option value="REGIONAL BOYACÁ" <?php if($resultado_registros[0][1]=="REGIONAL BOYACÁ"){ echo "selected"; } ?>>REGIONAL BOYACÁ</option>
                                  <option value="REGIONAL CALDAS" <?php if($resultado_registros[0][1]=="REGIONAL CALDAS"){ echo "selected"; } ?>>REGIONAL CALDAS</option>
                                  <option value="REGIONAL CAQUETÁ" <?php if($resultado_registros[0][1]=="REGIONAL CAQUETÁ"){ echo "selected"; } ?>>REGIONAL CAQUETÁ</option>
                                  <option value="REGIONAL CASANARE" <?php if($resultado_registros[0][1]=="REGIONAL CASANARE"){ echo "selected"; } ?>>REGIONAL CASANARE</option>
                                  <option value="REGIONAL CAUCA" <?php if($resultado_registros[0][1]=="REGIONAL CAUCA"){ echo "selected"; } ?>>REGIONAL CAUCA</option>
                                  <option value="REGIONAL CESÁR" <?php if($resultado_registros[0][1]=="REGIONAL CESÁR"){ echo "selected"; } ?>>REGIONAL CESÁR</option>
                                  <option value="REGIONAL CHOCÓ" <?php if($resultado_registros[0][1]=="REGIONAL CHOCÓ"){ echo "selected"; } ?>>REGIONAL CHOCÓ</option>
                                  <option value="REGIONAL CÓRDOBA" <?php if($resultado_registros[0][1]=="REGIONAL CÓRDOBA"){ echo "selected"; } ?>>REGIONAL CÓRDOBA</option>
                                  <option value="REGIONAL CUNDINAMARCA" <?php if($resultado_registros[0][1]=="REGIONAL CUNDINAMARCA"){ echo "selected"; } ?>>REGIONAL CUNDINAMARCA</option>
                                  <option value="REGIONAL GUAINÍA" <?php if($resultado_registros[0][1]=="REGIONAL GUAINÍA"){ echo "selected"; } ?>>REGIONAL GUAINÍA</option>
                                  <option value="REGIONAL GUAVIARE" <?php if($resultado_registros[0][1]=="REGIONAL GUAVIARE"){ echo "selected"; } ?>>REGIONAL GUAVIARE</option>
                                  <option value="REGIONAL HUILA" <?php if($resultado_registros[0][1]=="REGIONAL HUILA"){ echo "selected"; } ?>>REGIONAL HUILA</option>
                                  <option value="REGIONAL LA GUAJIRA" <?php if($resultado_registros[0][1]=="REGIONAL LA GUAJIRA"){ echo "selected"; } ?>>REGIONAL LA GUAJIRA</option>
                                  <option value="REGIONAL MAGDALENA" <?php if($resultado_registros[0][1]=="REGIONAL MAGDALENA"){ echo "selected"; } ?>>REGIONAL MAGDALENA</option>
                                  <option value="REGIONAL MAGDALENA MEDIO" <?php if($resultado_registros[0][1]=="REGIONAL MAGDALENA MEDIO"){ echo "selected"; } ?>>REGIONAL MAGDALENA MEDIO</option>
                                  <option value="REGIONAL META" <?php if($resultado_registros[0][1]=="REGIONAL META"){ echo "selected"; } ?>>REGIONAL META</option>
                                  <option value="REGIONAL NARIÑO" <?php if($resultado_registros[0][1]=="REGIONAL NARIÑO"){ echo "selected"; } ?>>REGIONAL NARIÑO</option>
                                  <option value="REGIONAL NORTE DE SANTANDER" <?php if($resultado_registros[0][1]=="REGIONAL NORTE DE SANTANDER"){ echo "selected"; } ?>>REGIONAL NORTE DE SANTANDER</option>
                                  <option value="REGIONAL PUTUMAYO" <?php if($resultado_registros[0][1]=="REGIONAL PUTUMAYO"){ echo "selected"; } ?>>REGIONAL PUTUMAYO</option>
                                  <option value="REGIONAL QUINDÍO" <?php if($resultado_registros[0][1]=="REGIONAL QUINDÍO"){ echo "selected"; } ?>>REGIONAL QUINDÍO</option>
                                  <option value="REGIONAL RISARALDA" <?php if($resultado_registros[0][1]=="REGIONAL RISARALDA"){ echo "selected"; } ?>>REGIONAL RISARALDA</option>
                                  <option value="REGIONAL SAN ANDRÉS" <?php if($resultado_registros[0][1]=="REGIONAL SAN ANDRÉS"){ echo "selected"; } ?>>REGIONAL SAN ANDRÉS</option>
                                  <option value="REGIONAL SANTANDER" <?php if($resultado_registros[0][1]=="REGIONAL SANTANDER"){ echo "selected"; } ?>>REGIONAL SANTANDER</option>
                                  <option value="REGIONAL SUCRE" <?php if($resultado_registros[0][1]=="REGIONAL SUCRE"){ echo "selected"; } ?>>REGIONAL SUCRE</option>
                                  <option value="REGIONAL TOLIMA" <?php if($resultado_registros[0][1]=="REGIONAL TOLIMA"){ echo "selected"; } ?>>REGIONAL TOLIMA</option>
                                  <option value="REGIONAL URABÁ" <?php if($resultado_registros[0][1]=="REGIONAL URABÁ"){ echo "selected"; } ?>>REGIONAL URABÁ</option>
                                  <option value="REGIONAL VALLE" <?php if($resultado_registros[0][1]=="REGIONAL VALLE"){ echo "selected"; } ?>>REGIONAL VALLE</option>
                                  <option value="REGIONAL VAUPÉS" <?php if($resultado_registros[0][1]=="REGIONAL VAUPÉS"){ echo "selected"; } ?>>REGIONAL VAUPÉS</option>
                                  <option value="REGIONAL VICHADA" <?php if($resultado_registros[0][1]=="REGIONAL VICHADA"){ echo "selected"; } ?>>REGIONAL VICHADA</option>
                                  <option value="RED CADE" <?php if($resultado_registros[0][1]=="RED CADE"){ echo "selected"; } ?>>RED CADE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                                <label for="ciudad" class="my-0">Municipio</label>
                                <select class="form-control form-control-sm form-select font-size-11" name="ciudad" id="ciudad" disabled>
                                    <option value="">Seleccione</option>
                                    <?php for ($i=0; $i < count($resultado_registros_ciudad); $i++): ?> 
                                        <option value="<?php echo $resultado_registros_ciudad[$i][0]; ?>" <?php if($resultado_registros[0][2]==$resultado_registros_ciudad[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_ciudad[$i][2].", ".$resultado_registros_ciudad[$i][1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="punto_atencion" class="my-0">Punto de atención</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="punto_atencion" id="punto_atencion" maxlength="100" value="<?php echo $resultado_registros[0][3]; ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="direccion" class="my-0">Dirección</label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="direccion" id="direccion" maxlength="100" value="<?php echo $resultado_registros[0][4]; ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-1">
                              <label for="direccion" class="my-0">Usuarios</label>
                              <?php for ($i=0; $i < count($resultado_registros_usuario); $i++): ?>
                              <input type="text" class="form-control form-control-sm font-size-11" name="direccion" id="direccion" maxlength="100" value="<?php echo $resultado_registros_usuario[$i][31]; ?>" disabled>
                              <?php endfor; ?>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12 mb-3">
                            <hr class="my-1">
                            <label for="" class="form-label my-0 fw-bold">Turnos diarios</label>
                            <hr class="my-1">
                            <div class="row">
                              <div class="table-responsive table-fixed" id="headerFixTable">
                                <table class="table table-hover table-bordered table-striped">
                                  <tbody>
                                    <tr>
                                      <td class="align-middle p-1" style="min-width: 100px; width: 120px;">
                                          Lunes <?php echo date("d", strtotime($dias_semana[0])); ?><br><?php echo $array_mes_min[intval(date("m", strtotime($dias_semana[0])))]; ?>
                                      </td>
                                      <td class="p-1 ps-2 font-size-11 align-top">
                                        <div class="form-group my-1">
                                          <input type="number" class="form-control form-control-sm font-size-11" min="0" max="10000" name="cantidad_turnos_1" id="cantidad_turnos_1" value="<?php echo $array_cupos_mostrar[$dias_semana[0]]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1 OR $resultado_registros[0][11]=='') { echo 'readonly'; } ?> required>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td class="align-middle p-1" style="min-width: 100px; width: 120px;">
                                          Martes <?php echo date("d", strtotime($dias_semana[1])); ?><br><?php echo $array_mes_min[intval(date("m", strtotime($dias_semana[1])))]; ?>
                                      </td>
                                      <td class="p-1 ps-2 font-size-11 align-top">
                                        <div class="form-group my-1">
                                          <input type="number" class="form-control form-control-sm font-size-11" min="0" max="10000" name="cantidad_turnos_2" id="cantidad_turnos_2" value="<?php echo $array_cupos_mostrar[$dias_semana[1]]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1 OR $resultado_registros[0][12]=='') { echo 'readonly'; } ?> required>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td class="align-middle p-1" style="min-width: 100px; width: 120px;">
                                          Miércoles <?php echo date("d", strtotime($dias_semana[2])); ?><br><?php echo $array_mes_min[intval(date("m", strtotime($dias_semana[2])))]; ?>
                                      </td>
                                      <td class="p-1 ps-2 font-size-11 align-top">
                                        <div class="form-group my-1">
                                          <input type="number" class="form-control form-control-sm font-size-11" min="0" max="10000" name="cantidad_turnos_3" id="cantidad_turnos_3" value="<?php echo $array_cupos_mostrar[$dias_semana[2]]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1 OR $resultado_registros[0][13]=='') { echo 'readonly'; } ?> required>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td class="align-middle p-1" style="min-width: 100px; width: 120px;">
                                          Jueves <?php echo date("d", strtotime($dias_semana[3])); ?><br><?php echo $array_mes_min[intval(date("m", strtotime($dias_semana[3])))]; ?>
                                      </td>
                                      <td class="p-1 ps-2 font-size-11 align-top">
                                        <div class="form-group my-1">
                                          <input type="number" class="form-control form-control-sm font-size-11" min="0" max="10000" name="cantidad_turnos_4" id="cantidad_turnos_4" value="<?php echo $array_cupos_mostrar[$dias_semana[3]]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1 OR $resultado_registros[0][14]=='') { echo 'readonly'; } ?> required>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td class="align-middle p-1" style="min-width: 100px; width: 120px;">
                                          Viernes <?php echo date("d", strtotime($dias_semana[4])); ?><br><?php echo $array_mes_min[intval(date("m", strtotime($dias_semana[4])))]; ?>
                                      </td>
                                      <td class="p-1 ps-2 font-size-11 align-top">
                                        <div class="form-group my-1">
                                          <input type="number" class="form-control form-control-sm font-size-11" min="0" max="10000" name="cantidad_turnos_5" id="cantidad_turnos_5" value="<?php echo $array_cupos_mostrar[$dias_semana[4]]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1 OR $resultado_registros[0][15]=='') { echo 'readonly'; } ?> required>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td class="align-middle p-1" style="min-width: 100px; width: 120px;">
                                          Sábado <?php echo date("d", strtotime($dias_semana[5])); ?><br><?php echo $array_mes_min[intval(date("m", strtotime($dias_semana[5])))]; ?>
                                      </td>
                                      <td class="p-1 ps-2 font-size-11 align-top">
                                        <div class="form-group my-1">
                                          <input type="number" class="form-control form-control-sm font-size-11" min="0" max="10000" name="cantidad_turnos_6" id="cantidad_turnos_6" value="<?php echo $array_cupos_mostrar[$dias_semana[5]]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1 OR $resultado_registros[0][16]=='') { echo 'readonly'; } ?> required>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td class="align-middle p-1" style="min-width: 100px; width: 120px;">
                                          Domingo <?php echo date("d", strtotime($dias_semana[6])); ?><br><?php echo $array_mes_min[intval(date("m", strtotime($dias_semana[6])))]; ?>
                                      </td>
                                      <td class="p-1 ps-2 font-size-11 align-top">
                                        <div class="form-group my-1">
                                          <input type="number" class="form-control form-control-sm font-size-11" min="0" max="10000" name="cantidad_turnos_7" id="cantidad_turnos_7" value="<?php echo $array_cupos_mostrar[$dias_semana[6]]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_agendamiento_punto']==1 OR $resultado_registros[0][17]=='') { echo 'readonly'; } ?> required>
                                        </div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if($agendados==0): ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Guardar</button>
                                <?php endif; ?>
                                <?php if(isset($_POST["guardar_registro"]) OR $agendados>0): ?>
                                    <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                                <?php endif; ?>
                                <?php if(!isset($_POST["guardar_registro"]) AND $agendados==0): ?>
                                    <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');">Cancelar</button>
                                <?php endif; ?>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
    function activa_check(estado, usuario, turno) {
      $(".agenda_"+usuario+"_"+turno).prop("checked", estado);
    }
  </script>
</body>
</html>