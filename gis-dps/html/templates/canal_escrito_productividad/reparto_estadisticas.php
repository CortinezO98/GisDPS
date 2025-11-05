<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Productividad";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);
  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');
  /*VARIABLES*/
  $title = "Canal Escrito";
  $subtitle = "Productividad";
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='';

  function maximo100($valor) {
    if ($valor>100) {
      $valor=100;
    }

    return $valor;
  }

  if(isset($_POST["filtro"])){
    $fecha_inicio=validar_input($_POST['fecha_inicio']);
    $fecha_fin=validar_input($_POST['fecha_fin']);
    
    $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']=$fecha_inicio;
    $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']=$fecha_fin;

    header("Location: reparto_estadisticas");
  } elseif($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']=="" OR $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']=="") {
    // $anio_mes_separado=explode("-", date('Y-m-d'));
    // $numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, $anio_mes_separado[1], $anio_mes_separado[0]); //cantidad de días del mes
    $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']=date('Y-m-d');
    $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']=date('Y-m-d');
  }
  
  //CONSTRUIR ARRAY AÑO-MES-DIA
    $dia_control=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
    while (date('Y-m-d', strtotime($dia_control))<=date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']))) {
        $array_dias_mes[]=$dia_control;
        $array_dias_mes_data[$dia_control]=0;

        $dia_control = date("Y-m-d", strtotime("+ 1 day", strtotime($dia_control)));
    }

  //CONSTRUIR HORARIO
    for ($i=0; $i < 24; $i++) { 
      $array_anio_mes_hora_num[]=validar_cero($i);
      $array_anio_mes_hora_val[]=0;
    }


  if ($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']!="" AND $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']!="") {
    $fecha_inicio_resumen=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio'];
    $fecha_fin_resumen=$_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin'].' 23:59:59';

    // Inicializa variable tipo array
    $data_consulta=array();
    array_push($data_consulta, $fecha_inicio_resumen);
    array_push($data_consulta, $fecha_fin_resumen);

    $filtro_fechas_p1=" AND `cepc_registro_fecha`>=? AND `cepc_registro_fecha`<=?";
    $filtro_fechas_p2=" AND `ceaff_registro_fecha`>=? AND `ceaff_registro_fecha`<=?";
    $filtro_fechas_p3=" AND `ceff_registro_fecha`>=? AND `ceff_registro_fecha`<=?";
    $filtro_fechas_p4=" AND `ceip_registro_fecha`>=? AND `ceip_registro_fecha`<=?";
    $filtro_fechas_p5=" AND `cepfa_registro_fecha`>=? AND `cepfa_registro_fecha`<=?";
    $filtro_fechas_p6=" AND `ceaf_registro_fecha`>=? AND `ceaf_registro_fecha`<=?";
    $filtro_fechas_p7=" AND `ceft_registro_fecha`>=? AND `ceft_registro_fecha`<=?";
    $filtro_fechas_p8=" AND `cep_registro_fecha`>=? AND `cep_registro_fecha`<=?";
    $filtro_fechas_p9=" AND `celtr_registro_fecha`>=? AND `celtr_registro_fecha`<=?";
    $filtro_fechas_p10=" AND `cesew_registro_fecha`>=? AND `cesew_registro_fecha`<=?";
    $filtro_fechas_p11=" AND `cescd_registro_fecha`>=? AND `cescd_registro_fecha`<=?";
    $filtro_fechas_p12=" AND `cesr_registro_fecha`>=? AND `cesr_registro_fecha`<=?";
    $filtro_fechas_p13=" AND `cest_registro_fecha`>=? AND `cest_registro_fecha`<=?";
    $filtro_fechas_p14=" AND `cesit_registro_fecha`>=? AND `cesit_registro_fecha`<=?";

    $filtro_fechas_ja_p1=" AND `cejpp_registro_fecha`>=? AND `cejpp_registro_fecha`<=?";
    $filtro_fechas_ja_p2=" AND `cejrp_registro_fecha`>=? AND `cejrp_registro_fecha`<=?";
    $filtro_fechas_ja_p3=" AND `cejrr_registro_fecha`>=? AND `cejrr_registro_fecha`<=?";
    $filtro_fechas_ja_p4=" AND `cejgc_registro_fecha`>=? AND `cejgc_registro_fecha`<=?";
    $filtro_fechas_ja_p5=" AND `cejgn_registro_fecha`>=? AND `cejgn_registro_fecha`<=?";
    $filtro_fechas_ja_p6=" AND `cejgp_registro_fecha`>=? AND `cejgp_registro_fecha`<=?";
    $filtro_fechas_ja_p7=" AND `cejga_registro_fecha`>=? AND `cejga_registro_fecha`<=?";
    $filtro_fechas_ja_p8=" AND `cejef_registro_fecha`>=? AND `cejef_registro_fecha`<=?";

    $filtro_fechas_tm_p1=" AND `cet_registro_fecha`>=? AND `cet_registro_fecha`<=?";
    $filtro_fechas_tm_p2=" AND `cetar_registro_fecha`>=? AND `cetar_registro_fecha`<=?";
    $filtro_fechas_tm_p3=" AND `cetc_registro_fecha`>=? AND `cetc_registro_fecha`<=?";
    $filtro_fechas_tm_p4=" AND `cete_registro_fecha`>=? AND `cete_registro_fecha`<=?";
    $filtro_fechas_tm_p5=" AND `cetfr_usuario_fecha`>=? AND `cetfr_usuario_fecha`<=?";
    $filtro_fechas_tm_p6=" AND `cetpc_registro_fecha`>=? AND `cetpc_registro_fecha`<=?";
    $filtro_fechas_tm_p7=" AND `cetcsg_registro_fecha`>=? AND `cetcsg_registro_fecha`<=?";
    $filtro_fechas_tm_p8=" AND `cetan_registro_fecha`>=? AND `cetan_registro_fecha`<=?";

    for ($k=0; $k < count($array_dias_mes); $k++) { 
        $fecha_resumen=$array_dias_mes[$k];

        //inicializar arrays
        $consulta_string_meta_hist="SELECT DISTINCT `cep_formulario`, `cep_meta` FROM `gestion_ce_productividad` WHERE `cep_fecha`=?";
        $consulta_registros_meta_hist = $enlace_db->prepare($consulta_string_meta_hist);
        $consulta_registros_meta_hist->bind_param("s", $fecha_resumen);
        $consulta_registros_meta_hist->execute();
        $resultado_registros_meta_hist = $consulta_registros_meta_hist->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_meta_hist); $i++) {
          $array_metas_hist[$resultado_registros_meta_hist[$i][0]]['meta']=$resultado_registros_meta_hist[$i][1];
        }

        $consulta_string_meta="SELECT `cef_id`, `cef_grupo`, `cef_nombre`, `cef_meta`, `cef_auxiliar_1`, `cef_auxiliar_2`, `cef_auxiliar_3` FROM `gestion_ce_formularios` WHERE 1=1";
        $consulta_registros_meta = $enlace_db->prepare($consulta_string_meta);
        $consulta_registros_meta->execute();
        $resultado_registros_meta = $consulta_registros_meta->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_meta); $i++) {
          if ($fecha_resumen==date('Y-m-d') OR !isset($array_metas_hist[$resultado_registros_meta[$i][0]]['meta'])) {
            $array_metas[$resultado_registros_meta[$i][0]]['meta'][$fecha_resumen]=$resultado_registros_meta[$i][3];
          } else {
            $array_metas[$resultado_registros_meta[$i][0]]['meta'][$fecha_resumen]=$array_metas_hist[$resultado_registros_meta[$i][0]]['meta'];
          }

          $array_metas[$resultado_registros_meta[$i][0]]['nombre']=$resultado_registros_meta[$i][2];
        }
    }

        //CONSULTA CONTEO RESUMIDO
        // SELECT `cepc_registro_usuario`, DATE_FORMAT(`cepc_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cepc_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_proyeccion_consolidacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_consolidacion`.`cepc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` GROUP BY `cepc_registro_usuario`, FECHA

        //REPARTO
            //1. Proyección Consolidación
              $id_formulario='reparto_proyeccion_consolidacion';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];

              $usuariosPorFormulario = array();
              $usuariosPorCoordinador = array(); // Nuevo array para relacionar usuarios por coordinador
              $nombresUsuariosArray = array(); // Nuevo array para relacionar ID de usuario con nombre
              $nombresCoordinadoresArray = array();
              $sumaCantidadPorFormularioUsuarioFecha = array();
              $cumplimientoPorFormularioUsuarioFecha = array();
              $array_coordinador = array();
              $usuariosPorFormulario[$id_formulario]=array();


              $consulta_string="SELECT `cepc_registro_usuario`, DATE_FORMAT(`cepc_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cepc_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_proyeccion_consolidacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_consolidacion`.`cepc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p1." GROUP BY `cepc_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = intval($array_metas[$formulario]['meta'][$fecha]);
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cepc_registro_usuario`, DATE_FORMAT(`cepc_registro_fecha`, '%H') AS HORA, COUNT(`cepc_registro_usuario`) AS CONTEO FROM `gestion_cerep_proyeccion_consolidacion` WHERE 1=1 ".$filtro_fechas_p1." GROUP BY `cepc_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }
              
            //2. Aprobación Firma FA
              $id_formulario='reparto_aprobacion_firma_fa';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `ceaff_registro_usuario`, DATE_FORMAT(`ceaff_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`ceaff_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_aprobacion_firma_fa` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_aprobacion_firma_fa`.`ceaff_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p2." GROUP BY `ceaff_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `ceaff_registro_usuario`, DATE_FORMAT(`ceaff_registro_fecha`, '%H') AS HORA, COUNT(`ceaff_registro_usuario`) AS CONTEO FROM `gestion_cerep_aprobacion_firma_fa` WHERE 1=1 ".$filtro_fechas_p2." GROUP BY `ceaff_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //3. Firma FA
              $id_formulario='reparto_firma_fa';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `ceff_registro_usuario`, DATE_FORMAT(`ceff_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`ceff_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_firma_fa` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_firma_fa`.`ceff_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p3." GROUP BY `ceff_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `ceff_registro_usuario`, DATE_FORMAT(`ceff_registro_fecha`, '%H') AS HORA, COUNT(`ceff_registro_usuario`) AS CONTEO FROM `gestion_cerep_firma_fa` WHERE 1=1 ".$filtro_fechas_p3." GROUP BY `ceff_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //4. Inspección Proyección
              $id_formulario='reparto_inspeccion_proyeccion';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `ceip_registro_usuario`, DATE_FORMAT(`ceip_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`ceip_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_inspeccion_proyeccion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_inspeccion_proyeccion`.`ceip_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p4." GROUP BY `ceip_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `ceip_registro_usuario`, DATE_FORMAT(`ceip_registro_fecha`, '%H') AS HORA, COUNT(`ceip_registro_usuario`) AS CONTEO FROM `gestion_cerep_inspeccion_proyeccion` WHERE 1=1 ".$filtro_fechas_p4." GROUP BY `ceip_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //5. Proyección FA
              $id_formulario='reparto_proyeccion_fa';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cepfa_registro_usuario`, DATE_FORMAT(`cepfa_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cepfa_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_proyeccion_fa` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyeccion_fa`.`cepfa_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p5." GROUP BY `cepfa_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cepfa_registro_usuario`, DATE_FORMAT(`cepfa_registro_fecha`, '%H') AS HORA, COUNT(`cepfa_registro_usuario`) AS CONTEO FROM `gestion_cerep_proyeccion_fa` WHERE 1=1 ".$filtro_fechas_p5." GROUP BY `cepfa_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //6. Aprobación Firma
              $id_formulario='reparto_aprobacion_firma';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `ceaf_registro_usuario`, DATE_FORMAT(`ceaf_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`ceaf_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_aprobacion_firma` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_aprobacion_firma`.`ceaf_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p6." GROUP BY `ceaf_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `ceaf_registro_usuario`, DATE_FORMAT(`ceaf_registro_fecha`, '%H') AS HORA, COUNT(`ceaf_registro_usuario`) AS CONTEO FROM `gestion_cerep_aprobacion_firma` WHERE 1=1 ".$filtro_fechas_p6." GROUP BY `ceaf_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //7. Firma Traslados
              $id_formulario='reparto_firma_traslados';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `ceft_registro_usuario`, DATE_FORMAT(`ceft_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`ceft_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_firma_traslados` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_firma_traslados`.`ceft_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p7." GROUP BY `ceft_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `ceft_registro_usuario`, DATE_FORMAT(`ceft_registro_fecha`, '%H') AS HORA, COUNT(`ceft_registro_usuario`) AS CONTEO FROM `gestion_cerep_firma_traslados` WHERE 1=1 ".$filtro_fechas_p7." GROUP BY `ceft_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //8. Proyectores
              $id_formulario='reparto_proyectores';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cep_registro_usuario`, DATE_FORMAT(`cep_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cep_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_proyectores` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_proyectores`.`cep_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p8." GROUP BY `cep_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cep_registro_usuario`, DATE_FORMAT(`cep_registro_fecha`, '%H') AS HORA, COUNT(`cep_registro_usuario`) AS CONTEO FROM `gestion_cerep_proyectores` WHERE 1=1 ".$filtro_fechas_p8." GROUP BY `cep_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //9. Seguimiento Lanzamientos TR
              $id_formulario='reparto_lanzamientos_tr';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `celtr_registro_usuario`, DATE_FORMAT(`celtr_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`celtr_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_lanzamientos_tr` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_lanzamientos_tr`.`celtr_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p9." GROUP BY `celtr_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `celtr_registro_usuario`, DATE_FORMAT(`celtr_registro_fecha`, '%H') AS HORA, COUNT(`celtr_registro_usuario`) AS CONTEO FROM `gestion_cerep_seguimiento_lanzamientos_tr` WHERE 1=1 ".$filtro_fechas_p9." GROUP BY `celtr_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //10. Seguimiento Envíos Web
              $id_formulario='reparto_seguimiento_envios_web';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cesew_registro_usuario`, DATE_FORMAT(`cesew_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cesew_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_envios_web` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_envios_web`.`cesew_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p10." GROUP BY `cesew_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cesew_registro_usuario`, DATE_FORMAT(`cesew_registro_fecha`, '%H') AS HORA, COUNT(`cesew_registro_usuario`) AS CONTEO FROM `gestion_cerep_seguimiento_envios_web` WHERE 1=1 ".$filtro_fechas_p10." GROUP BY `cesew_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //11. Seguimiento Cargue Documentos
              $id_formulario='reparto_seguimiento_cargue_documentos';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cescd_registro_usuario`, DATE_FORMAT(`cescd_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cescd_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_cargue_documentos` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_cargue_documentos`.`cescd_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p11." GROUP BY `cescd_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cescd_registro_usuario`, DATE_FORMAT(`cescd_registro_fecha`, '%H') AS HORA, COUNT(`cescd_registro_usuario`) AS CONTEO FROM `gestion_cerep_seguimiento_cargue_documentos` WHERE 1=1 ".$filtro_fechas_p11." GROUP BY `cescd_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //12. Seguimiento Radicación
              $id_formulario='reparto_seguimiento_radicacion';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cesr_registro_usuario`, DATE_FORMAT(`cesr_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cesr_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_radicacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_radicacion`.`cesr_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p12." GROUP BY `cesr_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cesr_registro_usuario`, DATE_FORMAT(`cesr_registro_fecha`, '%H') AS HORA, COUNT(`cesr_registro_usuario`) AS CONTEO FROM `gestion_cerep_seguimiento_radicacion` WHERE 1=1 ".$filtro_fechas_p12." GROUP BY `cesr_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //13. Seguimiento Tipificaciones
              $id_formulario='reparto_seguimiento_tipificaciones';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cest_registro_usuario`, DATE_FORMAT(`cest_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cest_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_tipificaciones` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_tipificaciones`.`cest_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p13." GROUP BY `cest_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cest_registro_usuario`, DATE_FORMAT(`cest_registro_fecha`, '%H') AS HORA, COUNT(`cest_registro_usuario`) AS CONTEO FROM `gestion_cerep_seguimiento_tipificaciones` WHERE 1=1 ".$filtro_fechas_p13." GROUP BY `cest_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //14. Seguimiento Inspección Tipificación
              $id_formulario='reparto_seguimiento_inspeccion_tipificacion';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cesit_registro_usuario`, DATE_FORMAT(`cesit_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cesit_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cerep_seguimiento_inspeccion_tipificacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_p14." GROUP BY `cesit_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cesit_registro_usuario`, DATE_FORMAT(`cesit_registro_fecha`, '%H') AS HORA, COUNT(`cesit_registro_usuario`) AS CONTEO FROM `gestion_cerep_seguimiento_inspeccion_tipificacion` WHERE 1=1 ".$filtro_fechas_p14." GROUP BY `cesit_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


        //JAFOCALIZACIÓN
            //1. Proyección de Peticiones Vivienda
              $id_formulario='jafocalizacion_proyeccion_peticiones';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cejpp_registro_usuario`, DATE_FORMAT(`cejpp_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cejpp_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_proyeccion_peticiones` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_proyeccion_peticiones`.`cejpp_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p1." GROUP BY `cejpp_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cejpp_registro_usuario`, DATE_FORMAT(`cejpp_registro_fecha`, '%H') AS HORA, COUNT(`cejpp_registro_usuario`) AS CONTEO FROM `gestion_cejafo_proyeccion_peticiones` WHERE 1=1 ".$filtro_fechas_ja_p1." GROUP BY `cejpp_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //2. Revisión de Peticiones Vivienda 
              $id_formulario='jafocalizacion_revision_peticiones';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cejrp_registro_usuario`, DATE_FORMAT(`cejrp_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cejrp_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_revision_peticiones` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_revision_peticiones`.`cejrp_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p2." GROUP BY `cejrp_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cejrp_registro_usuario`, DATE_FORMAT(`cejrp_registro_fecha`, '%H') AS HORA, COUNT(`cejrp_registro_usuario`) AS CONTEO FROM `gestion_cejafo_revision_peticiones` WHERE 1=1 ".$filtro_fechas_ja_p2." GROUP BY `cejrp_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //3. Formato de Relación RAE JeA
              $id_formulario='jafocalizacion_relacion_rae';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cejrr_registro_usuario`, DATE_FORMAT(`cejrr_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cejrr_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_relacion_rae` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_relacion_rae`.`cejrr_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p3." GROUP BY `cejrr_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cejrr_registro_usuario`, DATE_FORMAT(`cejrr_registro_fecha`, '%H') AS HORA, COUNT(`cejrr_registro_usuario`) AS CONTEO FROM `gestion_cejafo_relacion_rae` WHERE 1=1 ".$filtro_fechas_ja_p3." GROUP BY `cejrr_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //4. Formato de Gestión de Correos
              $id_formulario='jafocalizacion_gestion_correos';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cejgc_registro_usuario`, DATE_FORMAT(`cejgc_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cejgc_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_gestion_correo` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_correo`.`cejgc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p4." GROUP BY `cejgc_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cejgc_registro_usuario`, DATE_FORMAT(`cejgc_registro_fecha`, '%H') AS HORA, COUNT(`cejgc_registro_usuario`) AS CONTEO FROM `gestion_cejafo_gestion_correo` WHERE 1=1 ".$filtro_fechas_ja_p4." GROUP BY `cejgc_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //5. Formato Gestión de Novedades JeA
              $id_formulario='jafocalizacion_gestion_novedades';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cejgn_registro_usuario`, DATE_FORMAT(`cejgn_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cejgn_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_gestion_novedades` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_novedades`.`cejgn_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p5." GROUP BY `cejgn_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cejgn_registro_usuario`, DATE_FORMAT(`cejgn_registro_fecha`, '%H') AS HORA, COUNT(`cejgn_registro_usuario`) AS CONTEO FROM `gestion_cejafo_gestion_novedades` WHERE 1=1 ".$filtro_fechas_ja_p5." GROUP BY `cejgn_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //6. Formato de Gestión de Peticiones JeA
              $id_formulario='jafocalizacion_gestion_peticiones';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cejgp_registro_usuario`, DATE_FORMAT(`cejgp_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cejgp_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_gestion_peticiones` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_peticiones`.`cejgp_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p6." GROUP BY `cejgp_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cejgp_registro_usuario`, DATE_FORMAT(`cejgp_registro_fecha`, '%H') AS HORA, COUNT(`cejgp_registro_usuario`) AS CONTEO FROM `gestion_cejafo_gestion_peticiones` WHERE 1=1 ".$filtro_fechas_ja_p6." GROUP BY `cejgp_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //7. Formato Gestión de Aprobación JeA
              $id_formulario='jafocalizacion_gestion_aprobacion';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cejga_registro_usuario`, DATE_FORMAT(`cejga_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cejga_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_gestion_aprobacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_gestion_aprobacion`.`cejga_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p7." GROUP BY `cejga_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cejga_registro_usuario`, DATE_FORMAT(`cejga_registro_fecha`, '%H') AS HORA, COUNT(`cejga_registro_usuario`) AS CONTEO FROM `gestion_cejafo_gestion_aprobacion` WHERE 1=1 ".$filtro_fechas_ja_p7." GROUP BY `cejga_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //8. Formato Entrega Física
              $id_formulario='jafocalizacion_entregas_fisicas';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cejef_registro_usuario`, DATE_FORMAT(`cejef_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cejef_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cejafo_entrega_fisica` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cejafo_entrega_fisica`.`cejef_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_ja_p8." GROUP BY `cejef_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cejef_registro_usuario`, DATE_FORMAT(`cejef_registro_fecha`, '%H') AS HORA, COUNT(`cejef_registro_usuario`) AS CONTEO FROM `gestion_cejafo_entrega_fisica` WHERE 1=1 ".$filtro_fechas_ja_p8." GROUP BY `cejef_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


        //TMNC
            //1. Proyección de Respuestas
              $id_formulario='tmnc_sproyeccion_respuestas';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cet_registro_usuario`, DATE_FORMAT(`cet_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cet_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cetmnc_proyeccion_respuestas` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_proyeccion_respuestas`.`cet_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p1." GROUP BY `cet_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cet_registro_usuario`, DATE_FORMAT(`cet_registro_fecha`, '%H') AS HORA, COUNT(`cet_registro_usuario`) AS CONTEO FROM `gestion_cetmnc_proyeccion_respuestas` WHERE 1=1 ".$filtro_fechas_tm_p1." GROUP BY `cet_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //2. Aprobación Respuesta
              $id_formulario='tmnc_saprobacion_respuestas';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cetar_registro_usuario`, DATE_FORMAT(`cetar_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cetar_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cetmnc_aprobacion_respuesta` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_aprobacion_respuesta`.`cetar_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p2." GROUP BY `cetar_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cetar_registro_usuario`, DATE_FORMAT(`cetar_registro_fecha`, '%H') AS HORA, COUNT(`cetar_registro_usuario`) AS CONTEO FROM `gestion_cetmnc_aprobacion_respuesta` WHERE 1=1 ".$filtro_fechas_tm_p2." GROUP BY `cetar_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //3. Clasificación
              $id_formulario='tmnc_sclasificacion';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cetc_registro_usuario`, DATE_FORMAT(`cetc_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cetc_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cetmnc_clasificacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_clasificacion`.`cetc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p3." GROUP BY `cetc_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cetc_registro_usuario`, DATE_FORMAT(`cetc_registro_fecha`, '%H') AS HORA, COUNT(`cetc_registro_usuario`) AS CONTEO FROM `gestion_cetmnc_clasificacion` WHERE 1=1 ".$filtro_fechas_tm_p3." GROUP BY `cetc_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //4. Envíos
              $id_formulario='tmnc_senvios';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cete_registro_usuario`, DATE_FORMAT(`cete_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cete_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cetmnc_envios` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_envios`.`cete_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p4." GROUP BY `cete_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cete_registro_usuario`, DATE_FORMAT(`cete_registro_fecha`, '%H') AS HORA, COUNT(`cete_registro_usuario`) AS CONTEO FROM `gestion_cetmnc_envios` WHERE 1=1 ".$filtro_fechas_tm_p4." GROUP BY `cete_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //5. Firma Respuesta
              $id_formulario='tmnc_sfirma_respuesta';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cetfr_usuario_registro`, DATE_FORMAT(`cetfr_usuario_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cetfr_usuario_registro`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cetmnc_firma_respuesta` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_firma_respuesta`.`cetfr_usuario_registro`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p5." GROUP BY `cetfr_usuario_registro`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cetfr_usuario_registro`, DATE_FORMAT(`cetfr_usuario_fecha`, '%H') AS HORA, COUNT(`cetfr_usuario_registro`) AS CONTEO FROM `gestion_cetmnc_firma_respuesta` WHERE 1=1 ".$filtro_fechas_tm_p5." GROUP BY `cetfr_usuario_registro`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //6. Pendientes Clasificación
              $id_formulario='tmnc_sclasificacion';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cetpc_registro_usuario`, DATE_FORMAT(`cetpc_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cetpc_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cetmnc_pendiente_clasificacion` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_pendiente_clasificacion`.`cetpc_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p6." GROUP BY `cetpc_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cetpc_registro_usuario`, DATE_FORMAT(`cetpc_registro_fecha`, '%H') AS HORA, COUNT(`cetpc_registro_usuario`) AS CONTEO FROM `gestion_cetmnc_pendiente_clasificacion` WHERE 1=1 ".$filtro_fechas_tm_p6." GROUP BY `cetpc_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //7. Casos Sin Gestionar
              $id_formulario='tmnc_scasos_sgestionar';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cetcsg_registro_usuario`, DATE_FORMAT(`cetcsg_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cetcsg_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cetmnc_casos_sin_gestionar` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_casos_sin_gestionar`.`cetcsg_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p7." GROUP BY `cetcsg_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cetcsg_registro_usuario`, DATE_FORMAT(`cetcsg_registro_fecha`, '%H') AS HORA, COUNT(`cetcsg_registro_usuario`) AS CONTEO FROM `gestion_cetmnc_casos_sin_gestionar` WHERE 1=1 ".$filtro_fechas_tm_p7." GROUP BY `cetcsg_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


            //8. Aprobación Novedades CM
              $id_formulario='tmnc_saprobacion_novedades';
              $nombre_formulario=$array_metas[$id_formulario]['nombre'];
              $usuariosPorFormulario[$id_formulario]=array();

              $consulta_string="SELECT `cetan_registro_usuario`, DATE_FORMAT(`cetan_registro_fecha`, '%Y-%m-%d') AS FECHA, COUNT(`cetan_registro_usuario`) AS CONTEO, TU.`usu_nombres_apellidos`, TC.`usu_nombres_apellidos`, TC.`usu_id` FROM `gestion_cetmnc_aprobacion_novedades` LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_aprobacion_novedades`.`cetan_registro_usuario`=TU.`usu_id` LEFT JOIN `administrador_usuario` AS TC ON TU.`usu_supervisor`=TC.`usu_id` WHERE 1=1 ".$filtro_fechas_tm_p8." GROUP BY `cetan_registro_usuario`, FECHA";
              $consulta_registros = $enlace_db->prepare($consulta_string);
              if (count($data_consulta)>0) {
                  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
              }
              $consulta_registros->execute();
              $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

              if (count($resultado_registros)) {
                for ($i=0; $i < count($resultado_registros); $i++) {
                  $idUsuario = $resultado_registros[$i][0];
                  $idCoordinador = $resultado_registros[$i][5];
                  $formulario = $id_formulario;
                  $fecha = $resultado_registros[$i][1];
                  $cantidad = $resultado_registros[$i][2];
                  $nombre_coordinador = $resultado_registros[$i][4];
                  $nombre_usuario = $resultado_registros[$i][3];
                  
                  //Crear array lista de coordinadores
                  // Si el idCoordinador no existe en el array, agregarlo
                  if (!in_array($idCoordinador, $array_coordinador)) {
                      $array_coordinador[] = $idCoordinador;
                      $usuariosPorCoordinador[$idCoordinador]=array();
                  }

                  // Asociar usuarios a formularios
                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorFormulario[$formulario])) {
                      $usuariosPorFormulario[$formulario][] = $idUsuario;
                      
                  }

                  if (!isset($cumplimientoPorUsuarioResumen[$idUsuario])) {
                    $cumplimientoPorUsuarioResumen[$idUsuario]=array();
                  }

                  if (!isset($cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario])) {
                    $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario]=array();
                  }

                  // Si el id_usuario no existe en el array, agregarlo
                  if (!in_array($idUsuario, $usuariosPorCoordinador[$idCoordinador])) {
                      $usuariosPorCoordinador[$idCoordinador][] = $idUsuario;
                  }
                  
                  // Relacionar ID de usuario con su nombre
                  $nombresUsuariosArray[$idUsuario] = $nombre_usuario;

                  // Relacionar ID de usuario con su coordinador
                  $usuarioscoordinadorArray[$idUsuario] = $idCoordinador;

                  // Crear array de nombres de coordinadores únicos
                  $nombresCoordinadoresArray[$idCoordinador] = $nombre_coordinador;

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha])) {
                      $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 0;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] += $cantidad;
                  
                  // Calcular el cumplimiento por formulario, usuario, fecha
                  if (isset($array_metas[$formulario]['meta'][$fecha])) {
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;

                  //INSERT-UPDATE HISTORIAL PRODUCTIVIDAD
                  if ($fecha==date('Y-m-d')) {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_coordinador`=?, `cep_meta`=?, `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_coordinador, $cep_meta, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  } else {
                    // Prepara la sentencia
                    $sentencia_insert = $enlace_db->prepare("INSERT INTO `gestion_ce_productividad`(`cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_productividad_ajustada`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `cep_gestiones`=?, `cep_productividad`=?, `cep_actualiza_fecha`=?");
                    // Agrega variables a sentencia preparada
                    $sentencia_insert->bind_param('ssssssssssssssss', $cep_id, $cep_formulario, $cep_agente, $cep_coordinador, $cep_fecha, $cep_meta, $cep_gestiones, $cep_productividad, $cep_productividad_ajustada, $cep_tipologia, $cep_novedad, $cep_comentarios, $cep_actualiza_fecha, $cep_gestiones, $cep_productividad, $cep_actualiza_fecha);
                  }

                  $cep_id=$idUsuario.'-'.$formulario.'-'.$fecha;
                  $cep_formulario=$formulario;
                  $cep_agente=$idUsuario;
                  
                  if(isset($usuarioscoordinadorArray[$idUsuario])) {
                    $cep_coordinador=$usuarioscoordinadorArray[$idUsuario];
                  } else {
                    $cep_coordinador='';
                  }


                  $cep_fecha=$fecha;
                  $cep_meta=$array_metas[$formulario]['meta'][$fecha];
                  $cep_gestiones=$cantidad;
                  $cep_productividad=$cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha];
                  $cep_productividad_ajustada='';
                  $cep_tipologia='';
                  $cep_novedad='';
                  $cep_comentarios='';
                  $cep_actualiza_fecha=date('Y-m-d H:i:s');

                  $sentencia_insert->execute();
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
                }

                $consulta_string="SELECT `cetan_registro_usuario`, DATE_FORMAT(`cetan_registro_fecha`, '%H') AS HORA, COUNT(`cetan_registro_usuario`) AS CONTEO FROM `gestion_cetmnc_aprobacion_novedades` WHERE 1=1 ".$filtro_fechas_tm_p8." GROUP BY `cetan_registro_usuario`, HORA";
                $consulta_registros = $enlace_db->prepare($consulta_string);
                if (count($data_consulta)>0) {
                    $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
                }
                $consulta_registros->execute();
                $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

                for ($i=0; $i < count($resultado_registros); $i++) {
                  $id_usuario=$resultado_registros[$i][0];
                  $cantidad = $resultado_registros[$i][2];

                  // Sumar la cantidad por formulario, usuario, fecha
                  if (!isset($sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]])) {
                      $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario] = $array_anio_mes_hora_val;
                  }
                  
                  $sumaCantidadPorFormularioUsuarioHora[$formulario][$id_usuario][$resultado_registros[$i][1]] += $cantidad;
                }
              }


        $consulta_string_justificacion="SELECT `cep_id`, `cep_formulario`, `cep_agente`, `cep_coordinador`, `cep_fecha`, `cep_meta`, `cep_gestiones`, `cep_productividad`, `cep_tipologia`, `cep_novedad`, `cep_comentarios`, `cep_actualiza_fecha`, `cep_registro_fecha`, `cep_productividad_ajustada` FROM `gestion_ce_productividad` WHERE `cep_fecha`>=? AND `cep_fecha`<=?";
        $consulta_registros_justificacion = $enlace_db->prepare($consulta_string_justificacion);
        $consulta_registros_justificacion->bind_param("ss", $fecha_inicio_resumen, $fecha_fin_resumen);
        $consulta_registros_justificacion->execute();
        $resultado_registros_justificacion = $consulta_registros_justificacion->get_result()->fetch_all(MYSQLI_NUM);

        for ($i=0; $i < count($resultado_registros_justificacion); $i++) {
          if (!isset($array_datos_justificacion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['tipologia'])) {
            $array_datos_justificacion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['tipologia']=array();
          }

          if (!isset($array_datos_justificacion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['novedad'])) {
            $array_datos_justificacion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['novedad']=array();
          }

          if ($resultado_registros_justificacion[$i][8]!='') {
            $array_datos_justificacion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['tipologia'][]=$resultado_registros_justificacion[$i][8];
            
            $array_datos_justificacion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['novedad']=array_merge($array_datos_justificacion[$resultado_registros_justificacion[$i][1]]['gestion_agente'][$resultado_registros_justificacion[$i][2]]['novedad'], explode(';', $resultado_registros_justificacion[$i][9]));
          }

          if ($resultado_registros_justificacion[$i][13]=='') {
            $array_datos_justificacion[$resultado_registros_justificacion[$i][1]][$resultado_registros_justificacion[$i][2]]['ajustada'][]=0;
            $array_resumen[$resultado_registros_justificacion[$i][2]]['productividad_total_ajustada'][]=0;
          } else {
            $array_datos_justificacion[$resultado_registros_justificacion[$i][1]][$resultado_registros_justificacion[$i][2]]['ajustada'][]=$resultado_registros_justificacion[$i][13];
            $array_resumen[$resultado_registros_justificacion[$i][2]]['productividad_total_ajustada'][]=$resultado_registros_justificacion[$i][13];
          }
        }
  }

  $consulta_string_analista="SELECT `usu_id`, `usu_nombres_apellidos`, `usu_usuario_red` FROM `administrador_usuario` WHERE `usu_estado`='Activo' AND (`usu_cargo_rol` LIKE '%Agente%') ORDER BY `usu_nombres_apellidos`";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
  <script src="<?php echo PLUGINS; ?>Highcharts/code/highcharts.js"></script>
  <script src="<?php echo PLUGINS; ?>Highcharts/code/highcharts-more.js"></script>
  <script src="<?php echo PLUGINS; ?>Highcharts/code/modules/solid-gauge.js"></script>
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
          <div class="row justify-content-center">
            <div class="col-lg-12 d-flex flex-column">
              <div class="row">
                <div class="col-md-3">
                  <div class="row px-3 mb-3">
                    <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12 color-blanco" data-bs-toggle="modal" data-bs-target="#modal-filtro" title="Filtros">
                      <i class="fas fa-filter btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Filtros</span>
                    </button>
                    <div class="col-lg-12 py-1 font-size-12">
                      <?php if($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']!="" AND $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']!=""): ?>
                        Filtros: <?php echo $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']; ?> A <?php echo $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']; ?>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="btn-group-vertical nav nav-tabs" role="group" aria-label="Button group with nested dropdown">
                    <button type="button" class="btn btn-outline-dark px-1 py-2 active" style="text-align: left !important;" id="p0-tab" data-bs-toggle="tab" href="#p0" role="tab" aria-controls="p0" aria-selected="true">
                      <i class="fas fa-chart-pie btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Resumen Productividad
                    </button>
                    
                    <button type="button" class="btn btn-corp btn-icon-text font-size-12 color-blanco px-1 py-3" style="text-align: left !important;" data-bs-toggle="tab" role="tab">
                      <i class="fas fa-rectangle-list btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Reparto
                    </button>

                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p1-tab" data-bs-toggle="tab" href="#p1" role="tab" aria-controls="p1" aria-selected="true">
                      1. Proyección Consolidación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p2-tab" data-bs-toggle="tab" href="#p2" role="tab" aria-controls="p2" aria-selected="true">
                      2. Aprobación Firma FA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p3-tab" data-bs-toggle="tab" href="#p3" role="tab" aria-controls="p2" aria-selected="true">
                      3. Firma FA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p4-tab" data-bs-toggle="tab" href="#p4" role="tab" aria-controls="p2" aria-selected="true">
                      4. Inspección Proyección
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p5-tab" data-bs-toggle="tab" href="#p5" role="tab" aria-controls="p2" aria-selected="true">
                      5. Proyección FA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p6-tab" data-bs-toggle="tab" href="#p6" role="tab" aria-controls="p2" aria-selected="true">
                      6. Aprobación Firma
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p7-tab" data-bs-toggle="tab" href="#p7" role="tab" aria-controls="p2" aria-selected="true">
                      7. Firma Traslados
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p8-tab" data-bs-toggle="tab" href="#p8" role="tab" aria-controls="p2" aria-selected="true">
                      8. Proyectores
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p9-tab" data-bs-toggle="tab" href="#p9" role="tab" aria-controls="p2" aria-selected="true">
                      9. Seguimiento Lanzamientos TR
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p10-tab" data-bs-toggle="tab" href="#p10" role="tab" aria-controls="p2" aria-selected="true">
                      10. Seguimiento Envíos Web
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p11-tab" data-bs-toggle="tab" href="#p11" role="tab" aria-controls="p2" aria-selected="true">
                      11. Seguimiento Cargue Documentos
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p12-tab" data-bs-toggle="tab" href="#p12" role="tab" aria-controls="p2" aria-selected="true">
                      12. Seguimiento Radicación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p13-tab" data-bs-toggle="tab" href="#p13" role="tab" aria-controls="p2" aria-selected="true">
                      13. Seguimiento Tipificaciones
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="p14-tab" data-bs-toggle="tab" href="#p14" role="tab" aria-controls="p2" aria-selected="true">
                      14. Seguimiento Inspección Tipificación
                    </button>

                    <button type="button" class="btn btn-corp btn-icon-text font-size-12 color-blanco px-1 py-3" style="text-align: left !important;" data-bs-toggle="tab" role="tab">
                      <i class="fas fa-rectangle-list btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Jóvenes en Acción y Focalización
                    </button>

                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p1-tab" data-bs-toggle="tab" href="#ja_p1" role="tab" aria-controls="ja_p1" aria-selected="true">
                      1. Proyección de Peticiones Vivienda
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p2-tab" data-bs-toggle="tab" href="#ja_p2" role="tab" aria-controls="ja_p2" aria-selected="true">
                      2. Revisión de Peticiones Vivienda
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p3-tab" data-bs-toggle="tab" href="#ja_p3" role="tab" aria-controls="ja_p3" aria-selected="true">
                      3. Formato de Relación RAE JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p4-tab" data-bs-toggle="tab" href="#ja_p4" role="tab" aria-controls="ja_p4" aria-selected="true">
                      4. Formato de Gestión de Correos
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p5-tab" data-bs-toggle="tab" href="#ja_p5" role="tab" aria-controls="ja_p5" aria-selected="true">
                      5. Formato Gestión de Novedades JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p6-tab" data-bs-toggle="tab" href="#ja_p6" role="tab" aria-controls="ja_p6" aria-selected="true">
                      6. Formato de Gestión de Peticiones JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p7-tab" data-bs-toggle="tab" href="#ja_p7" role="tab" aria-controls="ja_p7" aria-selected="true">
                      7. Formato Gestión de Aprobación JeA
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="ja_p8-tab" data-bs-toggle="tab" href="#ja_p8" role="tab" aria-controls="ja_p8" aria-selected="true">
                      8. Formato Entrega Física
                    </button>

                    <button type="button" class="btn btn-corp btn-icon-text font-size-12 color-blanco px-1 py-3" style="text-align: left !important;" data-bs-toggle="tab" role="tab">
                      <i class="fas fa-rectangle-list btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Transferencias Monetarias No Condicionadas
                    </button>

                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p1-tab" data-bs-toggle="tab" href="#tm_p1" role="tab" aria-controls="tm_p1" aria-selected="true">
                      1. Proyección de Respuestas
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p2-tab" data-bs-toggle="tab" href="#tm_p2" role="tab" aria-controls="tm_p2" aria-selected="true">
                      2. Aprobación Respuesta
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p3-tab" data-bs-toggle="tab" href="#tm_p3" role="tab" aria-controls="tm_p2" aria-selected="true">
                      3. Clasificación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p4-tab" data-bs-toggle="tab" href="#tm_p4" role="tab" aria-controls="tm_p2" aria-selected="true">
                      4. Envíos
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p5-tab" data-bs-toggle="tab" href="#tm_p5" role="tab" aria-controls="tm_p2" aria-selected="true">
                      5. Firma Respuesta
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p6-tab" data-bs-toggle="tab" href="#tm_p6" role="tab" aria-controls="tm_p2" aria-selected="true">
                      6. Pendientes Clasificación
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p7-tab" data-bs-toggle="tab" href="#tm_p7" role="tab" aria-controls="tm_p2" aria-selected="true">
                      7. Casos Sin Gestionar
                    </button>
                    <button type="button" class="btn btn-outline-dark px-1 py-1" style="text-align: left !important;" id="tm_p8-tab" data-bs-toggle="tab" href="#tm_p8" role="tab" aria-controls="tm_p2" aria-selected="true">
                      8. Aprobación Novedades CM
                    </button>
                  </div>
                </div>
                <div class="col-md-9 ps-0">
                  <div class="tab-content tab-content-basic pt-0 px-1">
                    <!-- p0 -->
                    <?php include('reparto_estadisticas_p0.php'); ?>

                    <!-- p1 -->
                    <?php include('reparto_estadisticas_p1.php'); ?>

                    <!-- p2 -->
                    <?php include('reparto_estadisticas_p2.php'); ?>

                    <!-- p3 -->
                    <?php include('reparto_estadisticas_p3.php'); ?>

                    <!-- p4 -->
                    <?php include('reparto_estadisticas_p4.php'); ?>

                    <!-- p5 -->
                    <?php include('reparto_estadisticas_p5.php'); ?>

                    <!-- p6 -->
                    <?php include('reparto_estadisticas_p6.php'); ?>

                    <!-- p7 -->
                    <?php include('reparto_estadisticas_p7.php'); ?>

                    <!-- p8 -->
                    <?php include('reparto_estadisticas_p8.php'); ?>

                    <!-- p9 -->
                    <?php include('reparto_estadisticas_p9.php'); ?>

                    <!-- p10 -->
                    <?php include('reparto_estadisticas_p10.php'); ?>

                    <!-- p11 -->
                    <?php include('reparto_estadisticas_p11.php'); ?>

                    <!-- p12 -->
                    <?php include('reparto_estadisticas_p12.php'); ?>

                    <!-- p13 -->
                    <?php include('reparto_estadisticas_p13.php'); ?>

                    <!-- p14 -->
                    <?php include('reparto_estadisticas_p14.php'); ?>

                    <!-- p1 -->
                    <?php include('jafocalizacion_estadisticas_p1.php'); ?>

                    <!-- p2 -->
                    <?php include('jafocalizacion_estadisticas_p2.php'); ?>

                    <!-- p3 -->
                    <?php include('jafocalizacion_estadisticas_p3.php'); ?>

                    <!-- p4 -->
                    <?php include('jafocalizacion_estadisticas_p4.php'); ?>

                    <!-- p5 -->
                    <?php include('jafocalizacion_estadisticas_p5.php'); ?>

                    <!-- p6 -->
                    <?php include('jafocalizacion_estadisticas_p6.php'); ?>

                    <!-- p7 -->
                    <?php include('jafocalizacion_estadisticas_p7.php'); ?>

                    <!-- p8 -->
                    <?php include('jafocalizacion_estadisticas_p8.php'); ?>

                    <!-- p1 -->
                    <?php include('tmnc_estadisticas_p1.php'); ?>

                    <!-- p2 -->
                    <?php include('tmnc_estadisticas_p2.php'); ?>

                    <!-- p3 -->
                    <?php include('tmnc_estadisticas_p3.php'); ?>

                    <!-- p4 -->
                    <?php include('tmnc_estadisticas_p4.php'); ?>

                    <!-- p5 -->
                    <?php include('tmnc_estadisticas_p5.php'); ?>

                    <!-- p6 -->
                    <?php include('tmnc_estadisticas_p6.php'); ?>

                    <!-- p7 -->
                    <?php include('tmnc_estadisticas_p7.php'); ?>

                    <!-- p8 -->
                    <?php include('tmnc_estadisticas_p8.php'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- Modal -->
          <div class="modal fade" id="modal-filtro" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="staticBackdropLabel">Filtros</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form name="filtro" action="reparto_estadisticas" method="POST">
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="fecha_inicio">Fecha inicio</label>
                          <input type="date" class="form-control form-control-sm" name="fecha_inicio" id="fecha_inicio" value="<?php echo $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="fecha_fin">Fecha fin</label>
                          <input type="date" class="form-control form-control-sm" name="fecha_fin" id="fecha_fin" value="<?php echo $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin']; ?>" required>
                        </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger py-2 px-2" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" name="filtro" class="btn btn-primary btn-corp py-2 px-2">Aplicar</button>
                </div>
                </form>
              </div>
            </div>
          </div>
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>