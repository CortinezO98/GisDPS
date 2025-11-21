<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Interacciones";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
error_reporting(E_ALL);
ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Interacciones";
  $subtitle = "Registro Interacciones | Crear Interacción";
  $pagina='1';
  $filtro_permanente='null';
  $token=validar_input($_GET['token']);
  $bandeja=base64_encode('Hoy');
  $url_salir="interacciones?pagina=".$pagina."&id=".$filtro_permanente."&bandeja=".$bandeja;
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
  $duplicado=validar_input(base64_decode($_GET['duplicado']));
  $dup_canal=validar_input(base64_decode($_GET['canal']));
  $dup_id_caso=validar_input(base64_decode($_GET['id_caso']));
  $dup_tipodoc=validar_input(base64_decode($_GET['tipodoc']));
  $dup_identificacion=validar_input(base64_decode($_GET['identificacion']));
  $dup_id_encuesta=validar_input(base64_decode($_GET['id_encuesta']));
  
  if ($duplicado=='si') {
      unset($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]);
      $token_duplicado=generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6);
      header("Location:interacciones_crear?token=".$token_duplicado."&canal=".base64_encode($dup_canal)."&id_caso=".base64_encode($dup_id_caso)."&tipodoc=".base64_encode($dup_tipodoc)."&identificacion=".base64_encode($dup_identificacion)."&id_encuesta=".base64_encode($dup_id_encuesta));
  }

  if(isset($_POST["guardar_registro"])){
      $id_caso=validar_input($_POST['id_caso']);
      $primer_nombre=validar_input($_POST['primer_nombre']);
      $segundo_nombre=validar_input($_POST['segundo_nombre']);
      $primer_apellido=validar_input($_POST['primer_apellido']);
      $segundo_apellido=validar_input($_POST['segundo_apellido']);
      $tipo_documento=validar_input($_POST['tipo_documento']);
      $identificacion=validar_input($_POST['identificacion']);
      $sms=validar_input($_POST['sms']);
      $fecha_nacimiento=validar_input($_POST['fecha_nacimiento']);
      $edad=validar_input($_POST['edad']);
      $municipio=validar_input($_POST['municipio']);
      $telefono=validar_input($_POST['telefono']);
      $celular=validar_input($_POST['celular']);
      $email=validar_input($_POST['email']);
      $direccion=validar_input($_POST['direccion']);
      $direcciones_misionales=validar_input($_POST['direcciones_misionales']);
      $programa=validar_input($_POST['programa']);
      $tipificacion=validar_input($_POST['tipificacion']);
      $subtipificacion_1=validar_input($_POST['subtipificacion_1']);
      $subtipificacion_2=validar_input($_POST['subtipificacion_2']);
      $subtipificacion_3=validar_input($_POST['subtipificacion_3']);
      $consulta=validar_input($_POST['consulta']);
      $respuesta=validar_input($_POST['respuesta']);
      $resultado=validar_input($_POST['resultado']);
      $descripcion_resultado=validar_input($_POST['descripcion_resultado']);
      $complemento_resultado=validar_input($_POST['complemento_resultado']);
      $canal_atencion=validar_input($_POST['canal_atencion']);
      $beneficiario=validar_input($_POST['beneficiario']);
      
      if (isset($_POST['informacion_poblacional'])) {
        $informacion_poblacional=$_POST['informacion_poblacional'];
      } else {
        $informacion_poblacional=array();
      }

      $informacion_poblacional_insert=implode(';', $informacion_poblacional);

      if (isset($_POST['atencion_preferencial'])) {
        $atencion_preferencial=$_POST['atencion_preferencial'];
      } else {
        $atencion_preferencial=array();
      }

      $atencion_preferencial_insert=implode(';', $atencion_preferencial);

      $genero=validar_input($_POST['genero']);
      $escolaridad=validar_input($_POST['escolaridad']);
      
      $gi_auxiliar_1=validar_input($_POST['gi_auxiliar_1']);
      $gi_auxiliar_2=validar_input($_POST['gi_auxiliar_2']);
      $gi_auxiliar_3=validar_input($_POST['gi_auxiliar_3']);
      $gi_auxiliar_4=validar_input($_POST['gi_auxiliar_4']);
      $gi_auxiliar_5=validar_input($_POST['gi_auxiliar_5']);
      $gi_auxiliar_6=validar_input($_POST['gi_auxiliar_6']);
      $gi_auxiliar_7=validar_input($_POST['gi_auxiliar_7']);
      $gi_auxiliar_8=validar_input($_POST['gi_auxiliar_8']);
      $gi_auxiliar_9=validar_input($_POST['gi_auxiliar_9']);
      $gi_auxiliar_10=validar_input($_POST['gi_auxiliar_10']);

      $id_encuesta_fecha=validar_input($_POST['id_encuesta_fecha']);

      if($dup_id_caso=='' AND $canal_atencion=='SMS' AND $id_encuesta_fecha!='') {
        if ($dup_id_encuesta!="") {
            $id_encuesta=$dup_id_encuesta;
        } else {
            $id_encuesta=validar_input($_POST['id_encuesta']);
        }
      } else {
        $id_encuesta='';
      }
          
      $mes_actual=date('Y-m');
      $fecha_actual=date('Y-m-d');

      if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]!=1){
          if ($token=='') {
            $token=generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6).'-'.generar_token(6);
          }

          // // Prepara la sentencia
          // $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_interacciones`(`gi_id_registro`, `gi_id_caso`, `gi_primer_nombre`, `gi_segundo_nombre`, `gi_primer_apellido`, `gi_segundo_apellido`, `gi_tipo_documento`, `gi_identificacion`, `gi_direcciones_misionales`, `gi_programa`, `gi_tipificacion`, `gi_subtipificacion_1`, `gi_subtipificacion_2`, `gi_subtipificacion_3`, `gi_resultado`, `gi_descripcion_resultado`, `gi_canal_atencion`, `gi_beneficiario`, `gi_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // // Agrega variables a sentencia preparada
          // $sentencia_insert->bind_param('sssssssssssssssssss', $token, $id_caso, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $tipo_documento, $identificacion, $direcciones_misionales, $programa, $tipificacion, $subtipificacion_1, $subtipificacion_2, $subtipificacion_3, $resultado, $descripcion_resultado, $canal_atencion, $beneficiario, $_SESSION[APP_SESSION.'_session_usu_id']);

          // Prepara la sentencia
          $sentencia_insert_detalle = $enlace_db->prepare("INSERT INTO `gestion_interacciones_historico`(`gi_id_registro`, `gi_id_caso`, `gi_primer_nombre`, `gi_segundo_nombre`, `gi_primer_apellido`, `gi_segundo_apellido`, `gi_tipo_documento`, `gi_identificacion`, `gi_fecha_nacimiento`, `gi_edad`, `gi_municipio`, `gi_telefono`, `gi_celular`, `gi_email`, `gi_direccion`, `gi_direcciones_misionales`, `gi_programa`, `gi_tipificacion`, `gi_subtipificacion_1`, `gi_subtipificacion_2`, `gi_subtipificacion_3`, `gi_consulta`, `gi_respuesta`, `gi_resultado`, `gi_descripcion_resultado`, `gi_complemento_resultado`, `gi_canal_atencion`, `gi_sms`, `gi_id_encuesta`, `gi_beneficiario`, `gi_informacion_poblacional`, `gi_atencion_preferencial`, `gi_genero`, `gi_nivel_escolaridad`, `gi_auxiliar_1`, `gi_auxiliar_2`, `gi_auxiliar_3`, `gi_auxiliar_4`, `gi_auxiliar_5`, `gi_auxiliar_6`, `gi_auxiliar_7`, `gi_auxiliar_8`, `gi_auxiliar_9`, `gi_auxiliar_10`, `gi_gestion_fecha`, `gi_registro_usuario`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          // Agrega variables a sentencia preparada
          $sentencia_insert_detalle->bind_param('ssssssssssssssssssssssssssssssssssssssssssssss', $token, $id_caso, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $tipo_documento, $identificacion, $fecha_nacimiento, $edad, $municipio, $telefono, $celular, $email, $direccion, $direcciones_misionales, $programa, $tipificacion, $subtipificacion_1, $subtipificacion_2, $subtipificacion_3, $consulta, $respuesta, $resultado, $descripcion_resultado, $complemento_resultado, $canal_atencion, $sms, $id_encuesta, $beneficiario, $informacion_poblacional_insert, $atencion_preferencial_insert, $genero, $escolaridad, $gi_auxiliar_1, $gi_auxiliar_2, $gi_auxiliar_3, $gi_auxiliar_4, $gi_auxiliar_5, $gi_auxiliar_6, $gi_auxiliar_7, $gi_auxiliar_8, $gi_auxiliar_9, $gi_auxiliar_10, $fecha_actual, $_SESSION[APP_SESSION.'_session_usu_id']);
          if ($municipio!="" OR $tipo_documento=='NO IDENTIFICADO') {
            if ($sentencia_insert_detalle->execute()) {
                $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente');";
                $_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]=1;

                if ($canal_atencion=="Redes Sociales" OR $canal_atencion=="Chat Web" OR $canal_atencion=="WhatsApp" OR $canal_atencion=="Video llamada" OR $canal_atencion=="SMS") {
                  // Prepara la sentencia
                  $sentencia_insert_usuario = $enlace_db->prepare("INSERT INTO `gestion_interacciones_usuarios`(`giu_identificacion`, `giu_tipo_documento`, `giu_primer_nombre`, `giu_segundo_nombre`, `giu_primer_apellido`, `giu_segundo_apellido`, `giu_fecha_nacimiento`, `giu_municipio`, `giu_telefono`, `giu_celular`, `giu_email`, `giu_direccion`, `giu_informacion_poblacional`, `giu_atencion_preferencial`, `giu_genero`, `giu_escolaridad`, `giu_fecha_actualiza`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                  // Agrega variables a sentencia preparada
                  $sentencia_insert_usuario->bind_param('sssssssssssssssss', $identificacion, $tipo_documento, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $fecha_nacimiento, $municipio, $telefono, $celular, $email, $direccion, $informacion_poblacional_insert, $atencion_preferencial_insert, $genero, $escolaridad, date('Y-m-d H:i:s'));
                  $sentencia_insert_usuario->execute();
                } else {
                  // Prepara la sentencia
                  $sentencia_insert_usuario = $enlace_db->prepare("INSERT INTO `gestion_interacciones_usuarios`(`giu_identificacion`, `giu_tipo_documento`, `giu_primer_nombre`, `giu_segundo_nombre`, `giu_primer_apellido`, `giu_segundo_apellido`, `giu_fecha_nacimiento`, `giu_municipio`, `giu_telefono`, `giu_celular`, `giu_email`, `giu_direccion`, `giu_informacion_poblacional`, `giu_atencion_preferencial`, `giu_genero`, `giu_escolaridad`, `giu_fecha_actualiza`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `giu_identificacion`=?, `giu_tipo_documento`=?, `giu_primer_nombre`=?, `giu_segundo_nombre`=?, `giu_primer_apellido`=?, `giu_segundo_apellido`=?, `giu_fecha_nacimiento`=?, `giu_municipio`=?, `giu_telefono`=?, `giu_celular`=?, `giu_email`=?, `giu_direccion`=?, `giu_informacion_poblacional`=?, `giu_atencion_preferencial`=?, `giu_genero`=?, `giu_escolaridad`=?, `giu_fecha_actualiza`=?");
                  // Agrega variables a sentencia preparada
                  $sentencia_insert_usuario->bind_param('ssssssssssssssssssssssssssssssssss', $identificacion, $tipo_documento, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $fecha_nacimiento, $municipio, $telefono, $celular, $email, $direccion, $informacion_poblacional_insert, $atencion_preferencial_insert, $genero, $escolaridad, date('Y-m-d H:i:s'), $identificacion, $tipo_documento, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $fecha_nacimiento, $municipio, $telefono, $celular, $email, $direccion, $informacion_poblacional_insert, $atencion_preferencial_insert, $genero, $escolaridad, date('Y-m-d H:i:s'));
                  $sentencia_insert_usuario->execute();
                }

                if($dup_id_caso=='' AND $canal_atencion=='SMS' AND $id_encuesta_fecha!='' AND $id_encuesta!='') {
                    // Prepara la sentencia
                    $sentencia_insert_encuesta = $enlace_db->prepare("INSERT INTO `gestion_interacciones_encuestas`(`gie_id`, `gie_pregunta_1`, `gie_pregunta_2`, `gie_pregunta_3`, `gie_pregunta_4`, `gie_pregunta_5`, `gie_respuesta_fecha`, `gie_registro_usuario`) VALUES (?,'','','','','','',?) ON DUPLICATE KEY UPDATE `gie_registro_usuario`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert_encuesta->bind_param('sss', $id_encuesta, $_SESSION[APP_SESSION.'_session_usu_id'], $_SESSION[APP_SESSION.'_session_usu_id']);
                    $sentencia_insert_encuesta->execute();
                }
            } else {
              $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro');";
            }
          } else {
            $respuesta_accion = "alertButton('error', 'Error', 'Problemas al crear el registro, por favor verifique e intente nuevamente');";
          }
      } else {
          $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente');";
      }
  }

  $consulta_string_ciudad="SELECT `ciu_codigo`, `ciu_departamento`, `ciu_municipio` FROM `administrador_ciudades` ORDER BY `ciu_departamento`, `ciu_municipio`";
  $consulta_registros_ciudad = $enlace_db->prepare($consulta_string_ciudad);
  $consulta_registros_ciudad->execute();
  $resultado_registros_ciudad = $consulta_registros_ciudad->get_result()->fetch_all(MYSQLI_NUM);



  $consulta_string_direcmisionales="SELECT `gic1_id`, `gic1_item`, `gic1_estado`, `gic1_registro_usuario`, `gic1_registro_fecha` FROM `gestion_interacciones_catnivel1` WHERE `gic1_estado`='Activo' AND `gic1_id`>0 ORDER BY `gic1_item` ASC";
  $consulta_registros_direcmisionales = $enlace_db->prepare($consulta_string_direcmisionales);
  $consulta_registros_direcmisionales->execute();
  $resultado_registros_direcmisionales = $consulta_registros_direcmisionales->get_result()->fetch_all(MYSQLI_NUM);

  if(isset($_POST["guardar_registro"])){
      if ($direcciones_misionales!="") {
          $consulta_string_nivel2="SELECT `gic2_id`, `gic2_padre`, `gic2_item`, `gic2_estado`, `gic2_registro_usuario`, `gic2_registro_fecha` FROM `gestion_interacciones_catnivel2` WHERE `gic2_estado`='Activo' AND `gic2_padre`=? ORDER BY `gic2_item` ASC";
          $consulta_registros_nivel2 = $enlace_db->prepare($consulta_string_nivel2);
          $consulta_registros_nivel2->bind_param("s", $direcciones_misionales);
          $consulta_registros_nivel2->execute();
          $resultado_registros_nivel2 = $consulta_registros_nivel2->get_result()->fetch_all(MYSQLI_NUM);
      }

      if ($programa!="") {
          $consulta_string_nivel3="SELECT `gic3_id`, `gic3_padre`, `gic3_item`, `gic3_estado`, `gic3_registro_usuario`, `gic3_registro_fecha` FROM `gestion_interacciones_catnivel3` WHERE `gic3_estado`='Activo' AND `gic3_padre`=? ORDER BY `gic3_item` ASC";
          $consulta_registros_nivel3 = $enlace_db->prepare($consulta_string_nivel3);
          $consulta_registros_nivel3->bind_param("s", $programa);
          $consulta_registros_nivel3->execute();
          $resultado_registros_nivel3 = $consulta_registros_nivel3->get_result()->fetch_all(MYSQLI_NUM);
      }

      // if ($tipificacion!="") {
      //     $consulta_string_nivel4="SELECT `gic4_id`, `gic4_padre`, `gic4_item`, `gic4_estado`, `gic4_registro_usuario`, `gic4_registro_fecha` FROM `gestion_interacciones_catnivel4` WHERE `gic4_estado`='Activo' AND `gic4_padre`=? ORDER BY `gic4_item` ASC";
      //     $consulta_registros_nivel4 = $enlace_db->prepare($consulta_string_nivel4);
      //     $consulta_registros_nivel4->bind_param("s", $tipificacion);
      //     $consulta_registros_nivel4->execute();
      //     $resultado_registros_nivel4 = $consulta_registros_nivel4->get_result()->fetch_all(MYSQLI_NUM);
      // }

      // if ($subtipificacion_1!="") {
      //     $consulta_string_nivel5="SELECT `gic5_id`, `gic5_padre`, `gic5_item`, `gic5_estado`, `gic5_registro_usuario`, `gic5_registro_fecha` FROM `gestion_interacciones_catnivel5` WHERE `gic5_estado`='Activo' AND `gic5_padre`=? ORDER BY `gic5_item` ASC";
      //     $consulta_registros_nivel5 = $enlace_db->prepare($consulta_string_nivel5);
      //     $consulta_registros_nivel5->bind_param("s", $subtipificacion_1);
      //     $consulta_registros_nivel5->execute();
      //     $resultado_registros_nivel5 = $consulta_registros_nivel5->get_result()->fetch_all(MYSQLI_NUM);
      // }

      // if ($subtipificacion_2!="") {
      //     $consulta_string_nivel6="SELECT `gic6_id`, `gic6_padre`, `gic6_item`, `gic6_estado`, `gic6_registro_usuario`, `gic6_registro_fecha` FROM `gestion_interacciones_catnivel6` WHERE `gic6_estado`='Activo' AND `gic6_padre`=? ORDER BY `gic6_item` ASC";
      //     $consulta_registros_nivel6 = $enlace_db->prepare($consulta_string_nivel6);
      //     $consulta_registros_nivel6->bind_param("s", $subtipificacion_2);
      //     $consulta_registros_nivel6->execute();
      //     $resultado_registros_nivel6 = $consulta_registros_nivel6->get_result()->fetch_all(MYSQLI_NUM);
      // }

      if ($resultado=='Exitoso') {
          $array_descripcion_resultado[]="NO RESUELTA EN PRIMER CONTACTO";
          $array_descripcion_resultado[]="PENDIENTE REENVÍO DE RESPUESTA";
          $array_descripcion_resultado[]="PENDIENTE RADICACIÓN PQR";
          $array_descripcion_resultado[]="SOLUCIÓN EN PRIMER CONTACTO";
      } elseif ($resultado=='No exitoso') {
          $array_descripcion_resultado[]="ABANDONA SALA";
          $array_descripcion_resultado[]="LLAMADA/SMS DE BROMA";
          $array_descripcion_resultado[]="LLAMADA/ CHAT EQUIVOCADA";
          $array_descripcion_resultado[]="LLAMADA/SMS O INTERACCIÓN DE PRUEBA ";
          $array_descripcion_resultado[]="LLAMADA NO SE ESCUCHA";
          $array_descripcion_resultado[]="SE CORTÓ LLAMADA";
      }

      if ($descripcion_resultado=='NO RESUELTA EN PRIMER CONTACTO') {
          $array_complemento_resultado[]="SE CORTÓ LLAMADA";
          $array_complemento_resultado[]="NO SUPERA FILTRO";
          $array_complemento_resultado[]="ABANDONA SALA DE CHAT";
          $array_complemento_resultado[]="SIN SISTEMA";
      }
  }

  if (!isset($_POST['informacion_poblacional'])) {
    $informacion_poblacional=array();
  }

  if (!isset($_POST['atencion_preferencial'])) {
    $atencion_preferencial=array();
  }

  $consulta_string_auxiliar="SELECT `gia_id`, `gia_campo`, `gia_tipo`, `gia_nombre`, `gia_estado`, `gia_opciones` FROM `gestion_interacciones_auxiliar` WHERE `gia_estado`='Activo' AND `gia_campo` like '%gi_auxiliar_%' ORDER BY `gia_id`";
  $consulta_registros_auxiliar = $enlace_db->prepare($consulta_string_auxiliar);
  $consulta_registros_auxiliar->execute();
  $resultado_registros_auxiliar = $consulta_registros_auxiliar->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_opciones="SELECT `gia_id`, `gia_campo`, `gia_tipo`, `gia_nombre`, `gia_estado`, `gia_opciones` FROM `gestion_interacciones_auxiliar` WHERE `gia_estado`='Activo' AND `gia_campo` not like '%gi_auxiliar_%' AND `gia_tipo`='Lista' ORDER BY `gia_id`";
  $consulta_registros_opciones = $enlace_db->prepare($consulta_string_opciones);
  $consulta_registros_opciones->execute();
  $resultado_registros_opciones = $consulta_registros_opciones->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_opciones); $i++) {
    $explode_opciones= explode('|', $resultado_registros_opciones[$i][5]);
    for ($j=0; $j < count($explode_opciones); $j++) {
      if (trim($explode_opciones[$j])!="") {
        $array_opciones[$resultado_registros_opciones[$i][1]][]=trim($explode_opciones[$j]);
      }
    }
  }

  for ($i=0; $i < count($array_opciones['informacion_poblacional']); $i++) { 
    $array_informacion_poblacional[]=$array_opciones['informacion_poblacional'][$i];
  }

  for ($i=0; $i < count($array_opciones['atencion_preferencial']); $i++) { 
    $array_atencion_preferencial[]=$array_opciones['atencion_preferencial'][$i];
  }

  for ($i=0; $i < count($array_opciones['genero']); $i++) { 
    $array_genero[]=$array_opciones['genero'][$i];
  }

  for ($i=0; $i < count($array_opciones['escolaridad']); $i++) { 
    $array_escolaridad[]=$array_opciones['escolaridad'][$i];
  }

  // echo "<pre>";
  // print_r($array_opciones);
  // echo "</pre>";
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
          <div class="row">
            <?php if (!empty($respuesta_accion)) {echo "<script type='text/javascript'>".$respuesta_accion."</script>";} ?>
            <div class="col-lg-3 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="col-md-12">
                        <p class="alert background-principal color-blanco py-1 px-2 my-0"><span class="fas fa-info-circle"></span> Información general del caso</p>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="canal_atencion" class="my-0">Canal de atención</label>
                              <select class="form-control form-control-sm form-select" name="canal_atencion" id="canal_atencion" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required onchange="validar_canal_atencion(); validar_url_encuesta();">
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($array_opciones['canal_atencion']); $i++): ?>
                                    <option value="<?php echo $array_opciones['canal_atencion'][$i]; ?>" <?php if($canal_atencion==$array_opciones['canal_atencion'][$i] OR $dup_canal==$array_opciones['canal_atencion'][$i]){ echo "selected"; } ?>><?php echo $array_opciones['canal_atencion'][$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="id_caso" class="my-0">Id Caso</label>
                            <input type="text" class="form-control form-control-sm" name="id_caso" id="id_caso" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $id_caso; } elseif($dup_id_caso!=''){ echo $dup_id_caso; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> onkeyup="validar_url_encuesta();" onchange="validar_url_encuesta();" required>
                            <input type="hidden" class="form-control form-control-sm" name="id_encuesta_fecha" id="id_encuesta_fecha" value="<?php echo date('YmdHis'); ?>" readonly>
                            <input type="hidden" class="form-control form-control-sm" name="id_encuesta" id="id_encuesta" value="" readonly>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="tipo_documento" class="my-0">Tipo documento</label>
                              <select class="form-control form-control-sm form-select" name="tipo_documento" id="tipo_documento" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> onchange="validar_tipo_documento();" required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <option value="TARJETA DE IDENTIDAD" <?php if($tipo_documento=="TARJETA DE IDENTIDAD" OR $dup_tipodoc=="TARJETA DE IDENTIDAD"){ echo "selected"; } ?>>TARJETA DE IDENTIDAD</option>
                                  <option value="CÉDULA DE CIUDADANÍA/CONTRASEÑA" <?php if($tipo_documento=="CÉDULA DE CIUDADANÍA/CONTRASEÑA" OR $dup_tipodoc=="CÉDULA DE CIUDADANÍA/CONTRASEÑA"){ echo "selected"; } ?>>CÉDULA DE CIUDADANÍA/CONTRASEÑA</option>
                                  <option value="NO IDENTIFICADO" <?php if($tipo_documento=="NO IDENTIFICADO" OR $dup_tipodoc=="NO IDENTIFICADO"){ echo "selected"; } ?>>NO IDENTIFICADO</option>
                                  <option value="CEDULA DE EXTRAJERIA" <?php if($tipo_documento=="CEDULA DE EXTRAJERIA" OR $dup_tipodoc=="CEDULA DE EXTRAJERIA"){ echo "selected"; } ?>>CEDULA DE EXTRAJERIA</option>
                                  <option value="REGISTRO CIVIL/NUIP" <?php if($tipo_documento=="REGISTRO CIVIL/NUIP" OR $dup_tipodoc=="REGISTRO CIVIL/NUIP"){ echo "selected"; } ?>>REGISTRO CIVIL/NUIP</option>
                                  <option value="PASAPORTE" <?php if($tipo_documento=="PASAPORTE" OR $dup_tipodoc=="PASAPORTE"){ echo "selected"; } ?>>PASAPORTE</option>
                                  <option value="PEP Permiso Especial de Permanencia" <?php if($tipo_documento=="PEP Permiso Especial de Permanencia" OR $dup_tipodoc=="PEP Permiso Especial de Permanencia"){ echo "selected"; } ?>>PEP Permiso Especial de Permanencia</option>
                                  <option value="PPT Permiso de Protección Temporal" <?php if($tipo_documento=="PPT Permiso de Protección Temporal" OR $dup_tipodoc=="PPT Permiso de Protección Temporal"){ echo "selected"; } ?>>PPT Permiso de Protección Temporal</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="identificacion" class="my-0">Identificación</label>
                            <input type="text" class="form-control form-control-sm" name="identificacion" id="identificacion" maxlength="100" autocomplete="off" value="<?php if(isset($_POST["guardar_registro"])){ echo $identificacion; } elseif($dup_identificacion!=''){ echo $dup_identificacion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> required onkeyup="validar_datos_usuario();">
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group m-0">
                              <div class="form-group custom-control custom-checkbox m-0">
                                  <input type="checkbox" class="custom-control-input" id="customCheckbloqueo" name="bloqueo" value="Si" <?php if(isset($_POST["guardar_registro"]) AND $_POST["bloqueo"]=="Si"){ echo "checked"; } ?> <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?>>
                                  <label class="custom-control-label p-0 m-0" for="customCheckbloqueo">Bloquear usuario</label>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-12" id="respuesta_datos_usuario"></div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="beneficiario" class="my-0">Es beneficiario?</label>
                              <select class="form-control form-control-sm form-select" name="beneficiario" id="beneficiario" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <option value="Si" <?php if($beneficiario=="Si"){ echo "selected"; } ?>>Si</option>
                                  <option value="No" <?php if($beneficiario=="No"){ echo "selected"; } ?>>No</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="sms" class="my-0">Desea recibir información por SMS?</label>
                              <select class="form-control form-control-sm form-select" name="sms" id="sms" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> disabled required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <option value="Si" <?php if($sms=="Si"){ echo "selected"; } ?>>Si</option>
                                  <option value="No" <?php if($sms=="No"){ echo "selected"; } ?>>No</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="informacion_poblacional" class="my-0">Información poblacional</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="false" data-container="body" name="informacion_poblacional[]" id="informacion_poblacional" title="Seleccione" multiple <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> onchange="validar_poblacional();" required>
                                  <?php for ($i=0; $i < count($array_informacion_poblacional); $i++): ?>
                                    <option class="font-size-11 py-0" value="<?php echo $array_informacion_poblacional[$i]; ?>" <?php if(in_array($array_informacion_poblacional[$i], $informacion_poblacional)){ echo "selected"; } ?>><?php echo $array_informacion_poblacional[$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="atencion_preferencial" class="my-0">Atención preferencial</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="false" data-container="body" name="atencion_preferencial[]" id="atencion_preferencial" title="Seleccione" multiple <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> onchange="validar_preferencial();" required>
                                  <?php for ($i=0; $i < count($array_atencion_preferencial); $i++): ?>
                                    <option class="font-size-11 py-0" value="<?php echo $array_atencion_preferencial[$i]; ?>" <?php if(in_array($array_atencion_preferencial[$i], $atencion_preferencial)){ echo "selected"; } ?>><?php echo $array_atencion_preferencial[$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="genero" class="my-0">Género</label>
                              <select class="form-control form-control-sm form-select" name="genero" id="genero" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($array_genero); $i++): ?>
                                    <option value="<?php echo $array_genero[$i]; ?>" <?php if($genero==$array_genero[$i] OR $dup_tipodoc==$array_genero[$i]){ echo "selected"; } ?>><?php echo $array_genero[$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                              <label for="escolaridad" class="my-0">Nivel escolaridad</label>
                              <select class="form-control form-control-sm form-select" name="escolaridad" id="escolaridad" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required>
                                  <option class="font-size-11" value="">Seleccione</option>
                                  <?php for ($i=0; $i < count($array_escolaridad); $i++): ?>
                                    <option value="<?php echo $array_escolaridad[$i]; ?>" <?php if($escolaridad==$array_escolaridad[$i] OR $dup_tipodoc==$array_escolaridad[$i]){ echo "selected"; } ?>><?php echo $array_escolaridad[$i]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-9 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body row">
                      <div class="col-md-12">
                        <p class="alert background-principal color-blanco py-1 px-2 my-0"><span class="fas fa-user"></span> Datos personales del ciudadano</p>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="primer_nombre" class="my-0">Primer nombre</label>
                            <input type="text" class="form-control form-control-sm" name="primer_nombre" id="primer_nombre" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $primer_nombre; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="segundo_nombre" class="my-0">Segundo nombre</label>
                            <input type="text" class="form-control form-control-sm" name="segundo_nombre" id="segundo_nombre" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $segundo_nombre; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="primer_apellido" class="my-0">Primer apellido</label>
                            <input type="text" class="form-control form-control-sm" name="primer_apellido" id="primer_apellido" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $primer_apellido; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="segundo_apellido" class="my-0">Segundo apellido</label>
                            <input type="text" class="form-control form-control-sm" name="segundo_apellido" id="segundo_apellido" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $segundo_apellido; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="fecha_nacimiento" class="my-0">Fecha nacimiento</label>
                            <input type="date" class="form-control form-control-sm" name="fecha_nacimiento" id="fecha_nacimiento" value="<?php if(isset($_POST["guardar_registro"])){ echo $fecha_nacimiento; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> required onchange="calcular_edad()">
                          <div class="col-md-12 my-0" id="respuesta_fecha_nacimiento"></div>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="edad" class="my-0">Edad</label>
                            <input type="number" class="form-control form-control-sm" name="edad" id="edad" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $edad; } ?>" readonly>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group my-1">
                              <label for="municipio" class="my-0">Municipio/departamento</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="municipio" id="municipio" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required>
                                  <?php for ($i=0; $i < count($resultado_registros_ciudad); $i++): ?>
                                    <option class="font-size-11 py-0" value="<?php echo $resultado_registros_ciudad[$i][0]; ?>" data-tokens="<?php echo $resultado_registros_ciudad[$i][2].' '.$resultado_registros_ciudad[$i][1]; ?>" <?php if($municipio==$resultado_registros_ciudad[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_ciudad[$i][2].', '.$resultado_registros_ciudad[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="direccion" class="my-0">Dirección</label>
                            <input type="text" class="form-control form-control-sm" name="direccion" id="direccion" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $direccion; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="celular" class="my-0">Celular</label>
                            <input type="text" class="form-control form-control-sm" name="celular" id="celular" minlength="10" maxlength="10" value="<?php if(isset($_POST["guardar_registro"])){ echo $celular; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> required onkeyup="validar_celular();" onchange="validar_celular();" autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="telefono" class="my-0">Teléfono</label>
                            <input type="text" class="form-control form-control-sm" name="telefono" id="telefono" minlength="10" maxlength="10" value="<?php if(isset($_POST["guardar_registro"])){ echo $telefono; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> required onkeyup="validar_celular();" onchange="validar_celular();" autocomplete="off">
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group my-1">
                            <label for="email" class="my-0">Email</label>
                            <input type="email" class="form-control form-control-sm" name="email" id="email" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $email; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> autocomplete="off" required>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <p class="alert background-principal color-blanco py-1 px-2 my-0"><span class="fas fa-file-alt"></span> Información de la interacción</p>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group my-1">
                              <label for="direcciones_misionales" class="my-0">Tipificación 1</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="direcciones_misionales" id="direcciones_misionales" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required onchange="validar_nivel1();">
                                  <?php for ($i=0; $i < count($resultado_registros_direcmisionales); $i++): ?>
                                    <option class="font-size-11 py-0" value="<?php echo $resultado_registros_direcmisionales[$i][0]; ?>" <?php if($direcciones_misionales==$resultado_registros_direcmisionales[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_direcmisionales[$i][1]; ?></option>
                                  <?php endfor; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group my-1">
                              <label for="programa" class="my-0">Tipificación 2</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="programa" id="programa" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required onchange="validar_nivel2();">
                                  <?php if(isset($resultado_registros_nivel2)): ?>
                                      <?php for ($i=0; $i < count($resultado_registros_nivel2); $i++): ?> 
                                        <option class="font-size-11 py-0" value="<?php echo $resultado_registros_nivel2[$i][0]; ?>" class="font-size-11" <?php if(isset($_POST["guardar_registro"]) AND $programa==$resultado_registros_nivel2[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_nivel2[$i][2]; ?></option>
                                      <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group my-1">
                              <label for="tipificacion" class="my-0">Tipificación 3</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="tipificacion" id="tipificacion" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required onchange="validar_sms();">
                                  <?php if(isset($resultado_registros_nivel3)): ?>
                                      <?php for ($i=0; $i < count($resultado_registros_nivel3); $i++): ?> 
                                        <option class="font-size-11 py-0" value="<?php echo $resultado_registros_nivel3[$i][0]; ?>" class="font-size-11" <?php if(isset($_POST["guardar_registro"]) AND $tipificacion==$resultado_registros_nivel3[$i][0]){ echo "selected"; } ?>><?php echo $resultado_registros_nivel3[$i][2]; ?></option>
                                      <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="consulta" class="my-0">Consulta</label>
                            <textarea class="form-control form-control-sm height-100" name="consulta" id="consulta" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $consulta; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <p class="alert background-principal color-blanco py-1 px-2 my-0"><span class="fas fa-check-circle"></span> Resultado de la interacción</p>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group my-1">
                            <label for="respuesta" class="my-0">Respuesta</label>
                            <textarea class="form-control form-control-sm height-100" name="respuesta" id="respuesta" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?>><?php if(isset($_POST["guardar_registro"])){ echo $respuesta; } ?></textarea>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group my-1">
                              <label for="resultado" class="my-0">Resultado</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="resultado" id="resultado" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required onchange="validar_resultado();">
                                  <option class="font-size-11 py-0" value="Exitoso" <?php if($resultado=="Exitoso"){ echo "selected"; } ?>>Exitoso</option>
                                  <option class="font-size-11 py-0" value="No exitoso" <?php if($resultado=="No exitoso"){ echo "selected"; } ?>>No exitoso</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group my-1">
                              <label for="descripcion_resultado" class="my-0">Descripción del resultado</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="descripcion_resultado" id="descripcion_resultado" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required onchange="validar_descripcion_resultado();">
                                  <?php if(isset($array_descripcion_resultado)): ?>
                                      <?php for ($i=0; $i < count($array_descripcion_resultado); $i++): ?> 
                                        <option class="font-size-11 py-0" value="<?php echo $array_descripcion_resultado[$i]; ?>" class="font-size-11" <?php if(isset($_POST["guardar_registro"]) AND $descripcion_resultado==$array_descripcion_resultado[$i]){ echo "selected"; } ?>><?php echo $array_descripcion_resultado[$i]; ?></option>
                                      <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group my-1">
                              <label for="complemento_resultado" class="my-0">Complemento del resultado</label>
                              <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="complemento_resultado" id="complemento_resultado" title="Seleccione" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required>
                                  <?php if(isset($array_complemento_resultado)): ?>
                                      <?php for ($i=0; $i < count($array_complemento_resultado); $i++): ?> 
                                        <option class="font-size-11 py-0" value="<?php echo $array_complemento_resultado[$i]; ?>" class="font-size-11" <?php if(isset($_POST["guardar_registro"]) AND $complemento_resultado==$array_complemento_resultado[$i]){ echo "selected"; } ?>><?php echo $array_complemento_resultado[$i]; ?></option>
                                      <?php endfor; ?>
                                  <?php endif; ?>
                              </select>
                          </div>
                      </div>
                      <?php if(count($resultado_registros_auxiliar)>0): ?>
                        <div class="col-md-12">
                            <p class="alert background-principal color-blanco py-1 px-2 my-0"><span class="fas fa-check-circle"></span> Información complementaria</p>
                        </div>
                        <?php for ($i=0; $i < count($resultado_registros_auxiliar); $i++): ?>
                          <?php if($resultado_registros_auxiliar[$i][2]=='Texto'): ?>
                            <div class="col-md-4">
                                <div class="form-group my-1">
                                  <label for="<?php echo $resultado_registros_auxiliar[$i][1]; ?>" class="my-0"><?php echo $resultado_registros_auxiliar[$i][3]; ?></label>
                                  <input type="text" class="form-control form-control-sm" name="<?php echo $resultado_registros_auxiliar[$i][1]; ?>" id="<?php echo $resultado_registros_auxiliar[$i][1]; ?>" maxlength="100" value="<?php if(isset($_POST["guardar_registro"])){ echo $_POST[$resultado_registros_auxiliar[$i][1]]; } ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'readonly'; } ?> autocomplete="off" required>
                                </div>
                            </div>
                          <?php elseif($resultado_registros_auxiliar[$i][2]=='Lista'): ?>
                            <?php
                              $array_auxiliar_opciones=array();
                              $array_auxiliar_opciones=explode('|', $resultado_registros_auxiliar[$i][5]);
                            ?>
                            <div class="col-md-4">
                                <div class="form-group my-1">
                                    <label for="<?php echo $resultado_registros_auxiliar[$i][1]; ?>" class="my-0"><?php echo $resultado_registros_auxiliar[$i][3]; ?></label>
                                    <select class="selectpicker form-control form-control-sm form-select" data-live-search="true" data-container="body" name="<?php echo $resultado_registros_auxiliar[$i][1]; ?>" id="<?php echo $resultado_registros_auxiliar[$i][1]; ?>" <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1) { echo 'disabled'; } ?> required>
                                        <option class="font-size-11" value="">Seleccione</option>
                                        <?php if(isset($array_auxiliar_opciones)): ?>
                                            <?php for ($j=0; $j < count($array_auxiliar_opciones); $j++): ?> 
                                              <option value="<?php echo $array_auxiliar_opciones[$j]; ?>" class="font-size-11" <?php if(isset($_POST["guardar_registro"]) AND $_POST[$resultado_registros_auxiliar[$i][1]]==$array_auxiliar_opciones[$j]){ echo "selected"; } ?>><?php echo $array_auxiliar_opciones[$j]; ?></option>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                          <?php endif; ?>
                        <?php endfor; ?>
                      <?php endif; ?>
                      <div class="col-md-12">
                          <p class="alert alert-warning p-1 font-size-11 d-none" id="div_url_encuesta"><a href="#" class="btn btn-dark" title="Copiar url encuesta" onclick="copyToClipboard('url_encuesta'); validar_url_copiada();"><span class="fas fa-copy"></span> Copiar url encuesta</a> <span id="url_encuesta"></span></p>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if($_SESSION[APP_SESSION.'_registro_creado_interaccion'.$token]==1): ?>
                                  <a href="interacciones_crear?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo $bandeja; ?>&duplicado=<?php echo base64_encode('si'); ?>&canal=<?php echo base64_encode($canal_atencion); ?>&id_caso=<?php echo base64_encode($id_caso); ?>&tipodoc=<?php echo base64_encode($tipo_documento); ?>&identificacion=<?php echo base64_encode($identificacion); ?>&id_encuesta=<?php echo base64_encode($id_encuesta); ?>" class="btn btn-warning float-end ms-1">Registrar otra interacción asociada</a>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Guardar</button>
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
          </form>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
      function validar_canal_atencion(){
          var canal_atencion_opcion = document.getElementById("canal_atencion");
          var canal_atencion = canal_atencion_opcion.options[canal_atencion_opcion.selectedIndex].value;

          if(canal_atencion=="Redes Sociales" || canal_atencion=="Chat Web" || canal_atencion=="WhatsApp" || canal_atencion=="Video llamada" || canal_atencion=="SMS") {
              // var tipo_documento = document.getElementById('tipo_documento').disabled=true;
              // var identificacion = document.getElementById('identificacion').disabled=true;
              var informacion_poblacional  = document.getElementById('informacion_poblacional').disabled=true;
              var atencion_preferencial  = document.getElementById('atencion_preferencial').disabled=true;
              var genero  = document.getElementById('genero').disabled=true;
              var escolaridad  = document.getElementById('escolaridad').disabled=true;
              // var primer_nombre = document.getElementById('primer_nombre').disabled=true;
              // var segundo_nombre = document.getElementById('segundo_nombre').disabled=true;
              // var primer_apellido = document.getElementById('primer_apellido').disabled=true;
              // var segundo_apellido = document.getElementById('segundo_apellido').disabled=true;
              // var fecha_nacimiento = document.getElementById('fecha_nacimiento').disabled=true;
              // var municipio = document.getElementById('municipio').disabled=true;
              // var direccion = document.getElementById('direccion').disabled=true;
              // var celular = document.getElementById('celular').disabled=true;
              // var telefono = document.getElementById('telefono').disabled=true;
              // var email = document.getElementById('email').disabled=true;
              // document.getElementById('tipo_documento').value='';
              // document.getElementById('identificacion').value='';
              $('#informacion_poblacional').selectpicker('val', '');
              $('#informacion_poblacional').selectpicker('destroy');
              $('#informacion_poblacional').selectpicker();

              $('#atencion_preferencial').selectpicker('val', '');
              $('#atencion_preferencial').selectpicker('destroy');
              $('#atencion_preferencial').selectpicker();

              document.getElementById("genero").value='';
              document.getElementById("escolaridad").value='';
              // document.getElementById('primer_nombre').value='';
              // document.getElementById('segundo_nombre').value='';
              // document.getElementById('primer_apellido').value='';
              // document.getElementById('segundo_apellido').value='';
              // document.getElementById('fecha_nacimiento').value='';
              // document.getElementById('edad').value='';
              // $('#municipio').selectpicker('val', '');
              // $('#municipio').selectpicker('destroy');
              // $('#municipio').selectpicker();
              // document.getElementById('direccion').value='';
              // document.getElementById('celular').value='';
              // document.getElementById('telefono').value='';
              // document.getElementById('email').value='';
              $("#respuesta_datos_usuario").html('<p class="alert alert-warning p-1 font-size-11">¡Canal de atención no requiere datos personales del usuario!</p>');
          } else {
              // var tipo_documento = document.getElementById('tipo_documento').disabled=false;
              // var identificacion = document.getElementById('identificacion').disabled=false;
              var informacion_poblacional  = document.getElementById('informacion_poblacional').disabled=false;
              $('#informacion_poblacional').selectpicker('destroy');
              $('#informacion_poblacional').selectpicker();
              var atencion_preferencial  = document.getElementById('atencion_preferencial').disabled=false;
              $('#atencion_preferencial').selectpicker('destroy');
              $('#atencion_preferencial').selectpicker();
              
              var genero  = document.getElementById('genero').disabled=false;
              var escolaridad  = document.getElementById('escolaridad').disabled=false;
              // var primer_nombre = document.getElementById('primer_nombre').disabled=false;
              // var segundo_nombre = document.getElementById('segundo_nombre').disabled=false;
              // var primer_apellido = document.getElementById('primer_apellido').disabled=false;
              // var segundo_apellido = document.getElementById('segundo_apellido').disabled=false;
              // var fecha_nacimiento = document.getElementById('fecha_nacimiento').disabled=false;
              // var municipio = document.getElementById('municipio').disabled=false;
              // var direccion = document.getElementById('direccion').disabled=false;
              // var celular = document.getElementById('celular').disabled=false;
              // var telefono = document.getElementById('telefono').disabled=false;
              // var email = document.getElementById('email').disabled=false;
              $("#respuesta_datos_usuario").html('');
              // validar_tipo_documento();
          }
      }

      function validar_tipo_documento(){
          var tipo_documento_opcion = document.getElementById("tipo_documento");
          var tipo_documento = tipo_documento_opcion.options[tipo_documento_opcion.selectedIndex].value;

          if(tipo_documento=="NO IDENTIFICADO") {
              var identificacion  = document.getElementById('identificacion').disabled=true;
              var sms  = document.getElementById('sms').disabled=true;
              var informacion_poblacional  = document.getElementById('informacion_poblacional').disabled=true;
              var atencion_preferencial  = document.getElementById('atencion_preferencial').disabled=true;
              var genero  = document.getElementById('genero').disabled=true;
              var escolaridad  = document.getElementById('escolaridad').disabled=true;
              var primer_nombre  = document.getElementById('primer_nombre').disabled=true;
              var segundo_nombre  = document.getElementById('segundo_nombre').disabled=true;
              var primer_apellido  = document.getElementById('primer_apellido').disabled=true;
              var segundo_apellido  = document.getElementById('segundo_apellido').disabled=true;
              var fecha_nacimiento  = document.getElementById('fecha_nacimiento').disabled=true;
              var municipio  = document.getElementById('municipio').disabled=true;
              $('#municipio').selectpicker('destroy');
              $('#municipio').selectpicker();
              var direccion  = document.getElementById('direccion').disabled=true;
              var celular  = document.getElementById('celular').disabled=true;
              var telefono  = document.getElementById('telefono').disabled=true;
              var email  = document.getElementById('email').disabled=true;
              

              document.getElementById("identificacion").value='';
              $('#informacion_poblacional').selectpicker('val', '');
              $('#informacion_poblacional').selectpicker('destroy');
              $('#informacion_poblacional').selectpicker();

              $('#atencion_preferencial').selectpicker('val', '');
              $('#atencion_preferencial').selectpicker('destroy');
              $('#atencion_preferencial').selectpicker();

              document.getElementById("genero").value='';
              document.getElementById("escolaridad").value='';
              document.getElementById("primer_nombre").value='';
              document.getElementById("segundo_nombre").value='';
              document.getElementById("primer_apellido").value='';
              document.getElementById("segundo_apellido").value='';
              document.getElementById("fecha_nacimiento").value='';
              document.getElementById("edad").value='';
              $('#municipio').selectpicker('val', '');
              $('#municipio').selectpicker('destroy');
              $('#municipio').selectpicker();
              document.getElementById("direccion").value='';
              document.getElementById("celular").value='';
              document.getElementById("telefono").value='';
              document.getElementById("email").value='';
          } else {
              var identificacion  = document.getElementById('identificacion').disabled=false;
              var sms  = document.getElementById('sms').disabled=false;
              var informacion_poblacional  = document.getElementById('informacion_poblacional').disabled=false;
              $('#informacion_poblacional').selectpicker('destroy');
              $('#informacion_poblacional').selectpicker();
              var atencion_preferencial  = document.getElementById('atencion_preferencial').disabled=false;
              $('#atencion_preferencial').selectpicker('destroy');
              $('#atencion_preferencial').selectpicker();
              
              var genero  = document.getElementById('genero').disabled=false;
              var escolaridad  = document.getElementById('escolaridad').disabled=false;
              var primer_nombre  = document.getElementById('primer_nombre').disabled=false;
              var segundo_nombre  = document.getElementById('segundo_nombre').disabled=false;
              var primer_apellido  = document.getElementById('primer_apellido').disabled=false;
              var segundo_apellido  = document.getElementById('segundo_apellido').disabled=false;
              var fecha_nacimiento  = document.getElementById('fecha_nacimiento').disabled=false;
              var municipio  = document.getElementById('municipio').disabled=false;
              $('#municipio').selectpicker('destroy');
              $('#municipio').selectpicker();
              var direccion  = document.getElementById('direccion').disabled=false;
              var celular  = document.getElementById('celular').disabled=false;
              var telefono  = document.getElementById('telefono').disabled=false;
              var email  = document.getElementById('email').disabled=false;
          }
          // validar_canal_atencion();
          validar_sms();
      }

      function validar_poblacional(){
          var informacion_poblacional = $("#informacion_poblacional").val()+'';
          var informacion_poblacional_array = informacion_poblacional.split(",");

          for (var i = 0; i < informacion_poblacional_array.length; i++) {
            if(informacion_poblacional_array[i]=='No aporta') {
                $('#informacion_poblacional').selectpicker('deselectAll');
                $('#informacion_poblacional').selectpicker('val', 'No aporta');
            } else if(informacion_poblacional_array[i]=='Ninguna de las anteriores') {
                $('#informacion_poblacional').selectpicker('deselectAll');
                $('#informacion_poblacional').selectpicker('val', 'Ninguna de las anteriores');
            }
          }
      }

      function validar_preferencial(){
          var atencion_preferencial = $("#atencion_preferencial").val()+'';
          var atencion_preferencial_array = atencion_preferencial.split(",");
          var control_ninos = 0;

          for (var i = 0; i < atencion_preferencial_array.length; i++) {
            if(atencion_preferencial_array[i]=='No aporta') {
                $('#atencion_preferencial').selectpicker('deselectAll');
                $('#atencion_preferencial').selectpicker('val', 'No aporta');
            } else if(atencion_preferencial_array[i]=='Ninguna de las anteriores') {
                $('#atencion_preferencial').selectpicker('deselectAll');
                $('#atencion_preferencial').selectpicker('val', 'Ninguna de las anteriores');
            }

            if(atencion_preferencial_array[i]=='Adulto Mayor' || atencion_preferencial_array[i]=='Niñas/ Niños / Adolescentes') {
                control_ninos++;
            }
          }

          if (control_ninos==2) {
            $('#atencion_preferencial').selectpicker('deselectAll');
          }
      }

      function validar_nivel1(){
          var direcciones_misionales_opcion = document.getElementById("direcciones_misionales");
          var direcciones_misionales = direcciones_misionales_opcion.options[direcciones_misionales_opcion.selectedIndex].value;

          if(direcciones_misionales!="") {
              var programa = document.getElementById('programa').disabled=false;
              var tipificacion = document.getElementById('tipificacion').disabled=false;
              $("#programa").html("");
              $('#programa').selectpicker('destroy');
              $('#programa').selectpicker('refresh');
              $("#tipificacion").html("");
              $('#tipificacion').selectpicker('destroy');
              $('#tipificacion').selectpicker('refresh');
              
              $.post("interacciones_crear_procesar.php?validacion=programa&nivel1="+direcciones_misionales, { }, function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      $("#programa").html(resp.resultado);
                      $('#programa').selectpicker('refresh');
                  } else {
                      var programa = document.getElementById('programa').disabled=true;
                      var tipificacion = document.getElementById('tipificacion').disabled=true;
                      $('#programa').selectpicker('refresh');
                      $('#tipificacion').selectpicker('refresh');
                  }
              });
          }
          validar_sms();
      }

      function validar_nivel2(){
          var direcciones_misionales_opcion = document.getElementById("direcciones_misionales");
          var direcciones_misionales = direcciones_misionales_opcion.options[direcciones_misionales_opcion.selectedIndex].value;

          var programa_opcion = document.getElementById("programa");
          var programa = programa_opcion.options[programa_opcion.selectedIndex].value;

          if(direcciones_misionales!="" && programa!="") {
              var tipificacion = document.getElementById('tipificacion').disabled=false;
              $("#tipificacion").html("");
              $('#tipificacion').selectpicker('destroy');
              $('#tipificacion').selectpicker('refresh');
              $.post("interacciones_crear_procesar.php?validacion=tipificacion&nivel1="+direcciones_misionales+"&nivel2="+programa, { }, function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      $("#tipificacion").html(resp.resultado);
                      $('#tipificacion').selectpicker('refresh');
                  } else {
                      var tipificacion = document.getElementById('tipificacion').disabled=true;
                      $('#tipificacion').selectpicker('refresh');
                  }
              });
          }
          validar_sms();
      }

      function validar_sms(){
          try {
              var direcciones_misionales_opcion = document.getElementById("direcciones_misionales");
              var direcciones_misionales = direcciones_misionales_opcion.options[direcciones_misionales_opcion.selectedIndex].text.toUpperCase();
          } catch (error) {
              direcciones_misionales="";
          }

          try {
              var programa_opcion = document.getElementById("programa");
              var programa = programa_opcion.options[programa_opcion.selectedIndex].text.toUpperCase();
          } catch (error) {
              programa="";
          }

          try {
              var tipificacion_opcion = document.getElementById("tipificacion");
              var tipificacion = tipificacion_opcion.options[tipificacion_opcion.selectedIndex].text.toUpperCase();
          } catch (error) {
              tipificacion="";
          }

          if(direcciones_misionales=="JÓVENES EN ACCIÓN" && (programa=="ENTREGA DE INCENTIVOS" && (tipificacion=="1. INFORMACIÓN DE FECHAS DE TMC Y/O MESES LIQUIDADOS, IES QUE REPORTAN, CADA CUÁNTO SE GENERA TMC, DOCUMENTO PARA RECIBIR TMC/ CRONOGRAMA DE ENTREGA" || tipificacion=="2. RELIQUIDACIÓN/RETROACTIVO/NO COBROS GENERADOS EN EL SISTEMA") || programa=="NO TMC" && (tipificacion=="1. ESTADO DIFERENTE A ACTIVO EN LA PESTAÑA DE FORMACIÓN" || tipificacion=="2.EL PERÍODO DE VERIFICACIÓN ES MENOR O MAYOR A LA FECHA DE VINCULACIÓN/FINALIZO INTERVENCIÒN PESTAÑA FORMACIÓN" || tipificacion=="3.NO CUMPLE PROMEDIO PERMANENCIA/EXCELENCIA" || tipificacion=="4. JOVEN EN PERÍODO DE GRACIA/ NO SE ENCUENTRA EN ETAPA PRODUCTIVA NI LECTIVA/ INCONSISTENCIA EN FECHAS O REPORTE DE ETAPAS (LECTIVA O PRODUCTIVA)" || tipificacion=="5.NO REPORTE (IES/SENA)" || tipificacion=="6.REGISTRO AL PROGRAMA JEA ES MUY RECIENTE." || tipificacion=="7.DIFERENCIA EN EL NÚMERO DE PERÍODOS ACADÉMICOS REPORTADOS/PERIODO ACADÉMICO NO HA AUMENTADO" || tipificacion=="8.DIFERENCIAS EN PROGRAMA DE FORMACIÓN. SNIES REPORTADO POR LA IES NO ES IGUAL AL ALMACENADO EN SIJA" || tipificacion=="9. ESTADO EN TRÁNSITO NIVEL DE FORMACIÓN/CAMBIO DE PROGRAMA DE FORMACIÓN/APLAZADO"))) {
              var sms = document.getElementById('sms').disabled=false;
          } else if(direcciones_misionales=="JÓVENES EN ACCIÓN" && programa=="INFORMACIÓN BANCO Y/O PAGOS") {
              var sms = document.getElementById('sms').disabled=false;
          } else if(direcciones_misionales=="INGRESO SOLIDARIO" && (programa=="INFORMACIÓN GENERAL Y PAGOS" && (tipificacion=="1. INFORMACIÓN GENERAL DEL PROGRAMA/ INFORMACIÓN HOGAR/PERSONA: BENEFICIARIO / SUSPENDIDO / EXCLUIDO" || tipificacion=="2. INFORMACIÓN DE PAGOS"))) {
              var sms = document.getElementById('sms').disabled=false;
          } else if(direcciones_misionales=="COMPENSACIÓN DEL IVA" && (programa=="INFORMACIÓN GENERAL Y PAGOS" && (tipificacion=="1. INFORMACIÓN GENERAL DEL PROGRAMA/INFORMACIÓN HOGAR/PERSONA: BENEFICIARIO/ NO ELEGIBLE/NO VIGENTE" || tipificacion=="2. INFORMACIÓN DE PAGOS"))) {
              var sms = document.getElementById('sms').disabled=false;
          } else if(direcciones_misionales=="COLOMBIA MAYOR" && (programa=="INFORMACIÓN GENERAL Y PAGOS" && (tipificacion=="1. INFORMACIÓN GENERAL DEL PROGRAMA/INFORMACIÓN ESTADOS: ACTIVO, INSCRITO, POTENCIAL BENEFICIARIO, PRIORIZADO, SUSPENDIDO, RETIRADO" || tipificacion=="2. INFORMACIÓN DE PAGOS"))) {
              var sms = document.getElementById('sms').disabled=false;
          } else if(direcciones_misionales=="FAMILIAS EN ACCIÓN" && ((programa=="ENTREGA DE INCENTIVOS" && (tipificacion=="1.INFORMACIÓN TMC/MESES GENERADOS EDUCACIÓN Y/O SALUD/BENEFICIARIOS CON TMC/CRONOGRAMA DE ENTREGA" || tipificacion=="2. RELIQUIDACION/RETROACTIVO/NO COBROS GENERADOS EN EL SISTEMA")) || (programa=="NO TMC" && (tipificacion=="2.VERIFICACION VACACIONES CALENDARIO A(DIC/ENE)/B(JUN/JUL)" || tipificacion=="3. SALUD O EDUCACIÓN POR EDAD MÁXIMA" || tipificacion=="4. EDUCACIÓN PRIMARIA BOGOTÁ" || tipificacion=="5.INCUMPLIMIENTO DE COMPROMISOS/RETIRO O DESERCIÓN" || tipificacion=="6. POR REPITENCIA" || tipificacion=="7. DATOS DESACTUALIZADOS DE SALUD O EDUCACIÓN/NO MATRICULADO/NO TMC POR PRIORIZACIÓN ESCOLAR/SIN MARCA DISCAPACIDAD" || tipificacion=="8.  BACHILLER GRADUADO")))) {
              var sms = document.getElementById('sms').disabled=false;
          } else if(direcciones_misionales=="RENTA CIUDADANA" && programa=="INFORMACIÓN BANCO Y/O PAGOS" && tipificacion=="2. INFORMACIÓN DE PAGOS") {
              var sms = document.getElementById('sms').disabled=false;
          } else {
              var sms = document.getElementById('sms').disabled=true;
          }
      }

      function validar_resultado(){
          var resultado_opcion = document.getElementById("resultado");
          var resultado = resultado_opcion.options[resultado_opcion.selectedIndex].value;

          if(resultado!="") {
              var descripcion_resultado = document.getElementById('descripcion_resultado').disabled=false;
              var complemento_resultado = document.getElementById('complemento_resultado').disabled=false;
              $("#descripcion_resultado").html("");
              $('#descripcion_resultado').selectpicker('destroy');
              $('#descripcion_resultado').selectpicker('refresh');
              $("#complemento_resultado").html("");
              $('#complemento_resultado').selectpicker('destroy');
              $('#complemento_resultado').selectpicker('refresh');
              
              $.post("interacciones_crear_procesar.php?validacion=resultado&resultado="+resultado, { }, function(data){
                  var resp = $.parseJSON(data);
                  $("#descripcion_resultado").html(resp.resultado);
                  $('#descripcion_resultado').selectpicker('refresh');

              });

              if (resultado=='No exitoso') {
                  var complemento_resultado = document.getElementById('complemento_resultado').disabled=true;
                  $('#complemento_resultado').selectpicker('refresh');
              }
          }
      }

      function validar_descripcion_resultado(){
          var descripcion_resultado_opcion = document.getElementById("descripcion_resultado");
          var descripcion_resultado = descripcion_resultado_opcion.options[descripcion_resultado_opcion.selectedIndex].value;

          if(descripcion_resultado!="") {
              var complemento_resultado = document.getElementById('complemento_resultado').disabled=false;
              $("#complemento_resultado").html("");
              $('#complemento_resultado').selectpicker('destroy');
              $('#complemento_resultado').selectpicker('refresh');
              
              $.post("interacciones_crear_procesar.php?validacion=descripcion_resultado&descripcion_resultado="+descripcion_resultado, { }, function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      $("#complemento_resultado").html(resp.resultado);
                      $('#complemento_resultado').selectpicker('refresh');
                  } else {
                      var complemento_resultado = document.getElementById('complemento_resultado').disabled=true;
                      $('#complemento_resultado').selectpicker('refresh');
                  }
              });
          }
      }

      function validar_url_encuesta(){
          var canal_atencion_opcion = document.getElementById("canal_atencion");
          var canal_atencion = canal_atencion_opcion.options[canal_atencion_opcion.selectedIndex].value;

          var id_caso = document.getElementById("id_caso").value;
          var id_encuesta_fecha = document.getElementById("id_encuesta_fecha").value;

          if(id_caso!="" && canal_atencion=="SMS" && id_encuesta_fecha!="") {
              document.getElementById("id_encuesta").value=id_encuesta_fecha+"-"+id_caso;
              $("#url_encuesta").html("https://dps.iq-online.net.co/encuesta/satisfaccion?int="+btoa(id_encuesta_fecha+"-"+id_caso));
              $("#div_url_encuesta").removeClass('d-none').addClass('d-block');
              $("#guardar_registro_btn").removeClass('d-block').addClass('d-none');
          } else {
              $("#url_encuesta").html("");
              $("#div_url_encuesta").removeClass('d-block').addClass('d-none');
              $("#guardar_registro_btn").removeClass('d-none').addClass('d-block');
          }
      }

      function validar_url_copiada(){
          $("#guardar_registro_btn").removeClass('d-none').addClass('d-block');
      }

      function validar_datos_usuario(){
          var identificacion = document.getElementById("identificacion").value;
          var bloqueo = document.getElementById("customCheckbloqueo").checked;

          if(identificacion!="" && !bloqueo) {
              // $('#informacion_poblacional').selectpicker('val', '');
              // $('#informacion_poblacional').selectpicker('destroy');
              // $('#informacion_poblacional').selectpicker();

              // $('#atencion_preferencial').selectpicker('val', '');
              // $('#atencion_preferencial').selectpicker('destroy');
              // $('#atencion_preferencial').selectpicker();

              document.getElementById('genero').value='';
              document.getElementById('escolaridad').value='';
              document.getElementById('primer_nombre').value='';
              document.getElementById('segundo_nombre').value='';
              document.getElementById('primer_apellido').value='';
              document.getElementById('segundo_apellido').value='';
              document.getElementById('fecha_nacimiento').value='';
              document.getElementById('telefono').value='';
              document.getElementById('celular').value='';
              document.getElementById('email').value='';
              document.getElementById('direccion').value='';
              $('#municipio').selectpicker('val', '');
              $('#municipio').selectpicker('destroy');
              $('#municipio').selectpicker();
              $.post("interacciones_crear_procesar.php?validacion=datos_usuario&identificacion="+identificacion, { }, function(data){
                  var resp = $.parseJSON(data);
                  if (resp.resultado_control) {
                      $("#respuesta_datos_usuario").html(resp.resultado);
                      $('#informacion_poblacional').selectpicker('val', resp.resultado_informacion_poblacional);
                      $('#atencion_preferencial').selectpicker('val', resp.resultado_atencion_preferencial);
                      document.getElementById('genero').value=resp.resultado_genero;
                      document.getElementById('escolaridad').value=resp.resultado_escolaridad;
                      document.getElementById('primer_nombre').value=resp.resultado_primer_nombre;
                      document.getElementById('segundo_nombre').value=resp.resultado_segundo_nombre;
                      document.getElementById('primer_apellido').value=resp.resultado_primer_apellido;
                      document.getElementById('segundo_apellido').value=resp.resultado_segundo_apellido;
                      document.getElementById('fecha_nacimiento').value=resp.resultado_fecha_nacimiento;
                      $('#municipio').selectpicker('val', resp.resultado_municipio);
                      // document.getElementById('municipio').value='';
                      // $("#municipio").html("");
                      // $('#municipio').selectpicker('destroy');
                      // $('#municipio').selectpicker('refresh');
                      // document.getElementById('municipio').value=resp.resultado_municipio;
                      // $('#municipio').selectpicker('refresh');
                      document.getElementById('telefono').value=resp.resultado_telefono;
                      document.getElementById('celular').value=resp.resultado_celular;
                      document.getElementById('email').value=resp.resultado_email;
                      document.getElementById('direccion').value=resp.resultado_direccion;
                      calcular_edad();
                  } else {
                      $("#respuesta_datos_usuario").html(resp.resultado);
                  }
              });
          }
          validar_canal_atencion();
      }

      function validar_celular(){
          var celular = document.getElementById("celular").value;
          var telefono = document.getElementById("telefono").value;

          if(celular!="") {
              document.getElementById('telefono').required=false;
          } else {
              document.getElementById('telefono').required=true;
          }

          if(telefono!="") {
              document.getElementById('celular').required=false;
          } else {
              document.getElementById('celular').required=true;
          }
      }

      function calcular_edad() {
          var fecha = document.getElementById("fecha_nacimiento").value;
          var hoy = new Date();
          var cumpleanos = new Date(fecha);
          var edad = hoy.getFullYear() - cumpleanos.getFullYear();
          var m = hoy.getMonth() - cumpleanos.getMonth();

          $("#guardar_registro_btn").removeClass('d-none').addClass('d-block');

          if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
              edad--;
          }

          if (edad>9 && edad<=120) {
            $("#respuesta_fecha_nacimiento").html('');
            document.getElementById('edad').value=edad;
          } else {
            $("#respuesta_fecha_nacimiento").html('<p class="alert alert-warning px-1 py-0 my-0 font-size-11">¡Fecha de nacimiento no válida!</p>');
            document.getElementById('edad').value='';
          }
      }

      jQuery(document).ready(function(){
          jQuery("#primer_nombre").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[^a-zA-Z ]/g, ''));
          });
      });

      jQuery(document).ready(function(){
          jQuery("#segundo_nombre").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[^a-zA-Z ]/g, ''));
          });
      });

      jQuery(document).ready(function(){
          jQuery("#primer_apellido").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[^a-zA-Z ]/g, ''));
          });
      });

      jQuery(document).ready(function(){
          jQuery("#segundo_apellido").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().toUpperCase().replace(/[^a-zA-Z ]/g, ''));
          });
      });

      jQuery(document).ready(function(){
          jQuery("#direccion").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().toUpperCase());
          });
      });

      jQuery(document).ready(function(){
          jQuery("#email").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().toUpperCase());
          });
      });

      jQuery(document).ready(function(){
          jQuery("#identificacion").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
          });
      });

      jQuery(document).ready(function(){
          jQuery("#celular").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
          });
      });

      jQuery(document).ready(function(){
          jQuery("#telefono").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
          });
      });

      jQuery(document).ready(function(){
          jQuery("#edad").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
          });
      });

      jQuery(document).ready(function(){
          jQuery("#consulta").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().toUpperCase());
          });
      });

      jQuery(document).ready(function(){
          jQuery("#respuesta").on('input', function (evt) {
              jQuery(this).val(jQuery(this).val().toUpperCase());
          });
      });

      <?php if(isset($_POST["guardar_registro"])): ?>
          // validar_canal_atencion();
          // validar_nivel1();
          // validar_nivel2();
          // validar_nivel3();
          // validar_nivel4();
          // validar_nivel5();
          // validar_resultado();
          // validar_descripcion_resultado();
          // validar_datos_usuario();
          // validar_celular();
          // calcular_edad();
      <?php endif; ?>

      <?php if($dup_canal!=""): ?>
          validar_datos_usuario();
          validar_canal_atencion();
      <?php endif; ?>
  </script>
</body>
</html>