<?php
    //Validación de permisos del usuario para el módulo
    $modulo_plataforma="Canal Escrito-Productividad";
    require_once('../../app/config/config.php');
    require_once("../../app/config/db.php");
    require_once("../../app/config/security.php");
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    require_once('../assets/plugins/PhpSpreadsheet/vendor/autoload.php');
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;

    function maximo100($valor) {
        if ($valor>100) {
          $valor=100;
        }

        return $valor;
    }

    if(isset($_POST["reporte"])){
        $tipo_reporte=validar_input($_POST['tipo_reporte']);
        $fecha_inicio=validar_input($_POST['fecha_inicio']);
        $fecha_fin=validar_input($_POST['fecha_fin']);
        $agente=validar_input($_POST['agente']);

        $titulo_reporte="Canal Escrito-Productividad-".$tipo_reporte."-".date('Y-m-d H_i_s').".xlsx";
        
        //CONSTRUIR ARRAY AÑO-MES-DIA
            $dia_control=$fecha_inicio;
            while (date('Y-m-d', strtotime($dia_control))<=date('Y-m-d', strtotime($fecha_fin))) {
                $array_dias_mes[]=$dia_control;
                $array_dias_mes_data[$dia_control]=0;

                $dia_control = date("Y-m-d", strtotime("+ 1 day", strtotime($dia_control)));
            }

        //CONSTRUIR HORARIO
            for ($i=0; $i < 24; $i++) { 
              $array_anio_mes_hora_num[]=validar_cero($i);
              $array_anio_mes_hora_val[]=0;
            }

        $array_formularios[]='reparto_proyeccion_consolidacion';
        $array_formularios[]='reparto_aprobacion_firma_fa';
        $array_formularios[]='reparto_firma_fa';
        $array_formularios[]='reparto_inspeccion_proyeccion';
        $array_formularios[]='reparto_proyeccion_fa';
        $array_formularios[]='reparto_aprobacion_firma';
        $array_formularios[]='reparto_firma_traslados';
        $array_formularios[]='reparto_proyectores';
        $array_formularios[]='reparto_lanzamientos_tr';
        $array_formularios[]='reparto_seguimiento_envios_web';
        $array_formularios[]='reparto_seguimiento_cargue_documentos';
        $array_formularios[]='reparto_seguimiento_radicacion';
        $array_formularios[]='reparto_seguimiento_tipificaciones';
        $array_formularios[]='reparto_seguimiento_inspeccion_tipificacion';
        $array_formularios[]='jafocalizacion_proyeccion_peticiones';
        $array_formularios[]='jafocalizacion_revision_peticiones';
        $array_formularios[]='jafocalizacion_relacion_rae';
        $array_formularios[]='jafocalizacion_gestion_correos';
        $array_formularios[]='jafocalizacion_gestion_novedades';
        $array_formularios[]='jafocalizacion_gestion_peticiones';
        $array_formularios[]='jafocalizacion_gestion_aprobacion';
        $array_formularios[]='jafocalizacion_entregas_fisicas';
        $array_formularios[]='tmnc_sproyeccion_respuestas';
        $array_formularios[]='tmnc_saprobacion_respuestas';
        $array_formularios[]='tmnc_sclasificacion';
        $array_formularios[]='tmnc_senvios';
        $array_formularios[]='tmnc_sfirma_respuesta';
        $array_formularios[]='tmnc_sclasificacion';
        $array_formularios[]='tmnc_scasos_sgestionar';
        $array_formularios[]='tmnc_saprobacion_novedades';

        $fecha_inicio_resumen=$fecha_inicio;
        $fecha_fin_resumen=$fecha_fin.' 23:59:59';

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
                      $meta = $array_metas[$formulario]['meta'][$fecha];
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = min(($cantidad / $meta) * 100, 100);
                  } else {
                      $cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = 100;
                  }
                  
                  $totalPorFormularioUsuarioFecha[$formulario][$idUsuario][$fecha] = $cantidad/8;
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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
                }

                for ($i=0; $i < count($usuariosPorFormulario[$formulario]); $i++) { 
                  $idUsuario_resumen=$usuariosPorFormulario[$formulario][$i];
                  $promedioCumplimiento = array_sum($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]) / count($cumplimientoPorFormularioUsuarioFecha[$formulario][$idUsuario_resumen]);
                  $cumplimientoPorFormularioUsuarioResumen[$formulario][$idUsuario_resumen]=$promedioCumplimiento;
                  $cumplimientoPorFormularioUsuarioHora[$formulario][$idUsuario_resumen]=round(array_sum($totalPorFormularioUsuarioFecha[$formulario][$idUsuario])/count($totalPorFormularioUsuarioFecha[$formulario][$idUsuario]));
                  $cumplimientoPorUsuarioResumen[$idUsuario_resumen][]=$promedioCumplimiento;
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

    // Creamos nueva instancia de PHPExcel 
    $spreadsheet = new Spreadsheet();

    // Establecer propiedades
    $spreadsheet->getProperties()
    ->setCreator(APP_NAME_ALL)
    ->setLastModifiedBy($_SESSION[APP_SESSION.'_session_usu_nombre_completo'])
    ->setTitle(APP_NAME_ALL)
    ->setSubject(APP_NAME_ALL)
    ->setDescription(APP_NAME_ALL)
    ->setKeywords(APP_NAME_ALL)
    ->setCategory("Reporte");

    require_once("../../includes/_excel-style.php");

    //Activar hoja 0
    $sheet = $spreadsheet->getActiveSheet(0);
    
    // Nombramos la hoja 0
    $spreadsheet->getActiveSheet()->setTitle('Resumen Productividad');

    //Estilos de la Hoja 0
    $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(80);
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    
    $spreadsheet->getActiveSheet()->getStyle('A3:E3')->applyFromArray($styleArrayTitulos);

    $spreadsheet->getActiveSheet()->setAutoFilter('A3:E3');
    $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);

    // Escribiendo los títulos
    $spreadsheet->getActiveSheet()->setCellValue('A3','Coordinador');
    $spreadsheet->getActiveSheet()->setCellValue('B3','Doc. Agente');
    $spreadsheet->getActiveSheet()->setCellValue('C3','Agente');
    $spreadsheet->getActiveSheet()->setCellValue('D3','Productividad');
    $spreadsheet->getActiveSheet()->setCellValue('E3','Productividad Ajustada');
    // $spreadsheet->getActiveSheet()->setCellValue('F3','Tipología');
    // $spreadsheet->getActiveSheet()->setCellValue('G3','Novedad');
    // $spreadsheet->getActiveSheet()->setCellValue('H3','Comentarios');
    
    $fila_registro=4;
    $fila_registro_form=4;
    // Ingresar Data consultada a partir de la fila 4
    for ($i=0; $i < count($array_coordinador); $i++) {
        $id_coordinador=$array_coordinador[$i];
        $nombre_coordinador=$nombresCoordinadoresArray[$id_coordinador];
        $total_productividad=0;
        $total_productividad_ajustada=0;

        for ($j=0; $j < count($usuariosPorCoordinador[$id_coordinador]); $j++) {
            $id_agente_resumen=$usuariosPorCoordinador[$id_coordinador][$j];
            $nombre_agente=$nombresUsuariosArray[$id_agente_resumen];

            if (count($cumplimientoPorUsuarioResumen[$id_agente_resumen])==0) {
                $productividad_agente=0;  
            } else {
                $productividad_agente=number_format(array_sum($cumplimientoPorUsuarioResumen[$id_agente_resumen])/count($cumplimientoPorUsuarioResumen[$id_agente_resumen]), 2, '.', '');
            }

            if ($productividad_agente==100) {
                $color_progress='bg-success';
            } elseif ($productividad_agente>=90) {
                $color_progress='bg-warning';
            } else {
                $color_progress='bg-danger';
            }

            if (isset($array_resumen[$id_agente_resumen]['productividad_total_ajustada'])) {
                if (count($array_resumen[$id_agente_resumen]['productividad_total_ajustada'])==0) {
                  $productividad_agente_ajustada=0;
                } else {
                  $productividad_agente_ajustada=number_format(array_sum($array_resumen[$id_agente_resumen]['productividad_total_ajustada'])/count($array_resumen[$id_agente_resumen]['productividad_total_ajustada']), 2, '.', '');
                }
            } else {
                $productividad_agente_ajustada=0;
            }

            if ($productividad_agente_ajustada<$productividad_agente) {
                $productividad_agente_ajustada=$productividad_agente;
            }

            if ($productividad_agente_ajustada==100) {
                $color_progress_ajustada='bg-success';
            } elseif ($productividad_agente_ajustada>=90) {
                $color_progress_ajustada='bg-warning';
            } else {
                $color_progress_ajustada='bg-danger';
            }

            $spreadsheet->getActiveSheet()->setCellValue('A'.$fila_registro,$nombre_coordinador);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$fila_registro,$id_agente_resumen);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$fila_registro,$nombre_agente);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$fila_registro,$productividad_agente);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$fila_registro,$productividad_agente_ajustada);
            // $spreadsheet->getActiveSheet()->setCellValue('F'.$fila_registro,$productividad_agente_ajustada);
            // $spreadsheet->getActiveSheet()->setCellValue('G'.$fila_registro,$tipologia);
            // $spreadsheet->getActiveSheet()->setCellValue('H'.$fila_registro,$novedad);
            // $spreadsheet->getActiveSheet()->setCellValue('I'.$fila_registro,$comentarios);
            $fila_registro++;
        }
    }

    //Activar hoja 0
    $sheet = $spreadsheet->setActiveSheetIndex(0);

    $spreadsheet->createSheet();

    //Activar hoja 0
    $numhoja=1;
    $sheet = $spreadsheet->setActiveSheetIndex($numhoja);

    // Nombramos la hoja 0
    $spreadsheet->getActiveSheet()->setTitle('Detalle Productividad');

    //Estilos de la Hoja
    $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(80);
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(20);
    
    $spreadsheet->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleArrayTitulos);

    $spreadsheet->getActiveSheet()->setAutoFilter('A3:M3');
    $spreadsheet->getActiveSheet()->getStyle('3')->getAlignment()->setWrapText(true);

    // Escribiendo los títulos
    $spreadsheet->getActiveSheet()->setCellValue('A3','Formulario');
    $spreadsheet->getActiveSheet()->setCellValue('B3','Coordinador');
    $spreadsheet->getActiveSheet()->setCellValue('C3','Doc. Agente');
    $spreadsheet->getActiveSheet()->setCellValue('D3','Agente');
    $spreadsheet->getActiveSheet()->setCellValue('E3','Productividad');
    $spreadsheet->getActiveSheet()->setCellValue('F3','Productividad Ajustada');
    $spreadsheet->getActiveSheet()->setCellValue('G3','Promedio Meta Diaria');
    $spreadsheet->getActiveSheet()->setCellValue('H3','Días');
    $spreadsheet->getActiveSheet()->setCellValue('I3','Promedio Gestiones Diaria');
    $spreadsheet->getActiveSheet()->setCellValue('J3','Promedio Casos Hora');
    $spreadsheet->getActiveSheet()->setCellValue('K3','Horas No Cargue');
    $spreadsheet->getActiveSheet()->setCellValue('L3','Tipología');
    $spreadsheet->getActiveSheet()->setCellValue('M3','Novedad');
    
    $fila_registro_form=4;
    for ($m=0; $m < count($array_formularios); $m++) { 
        $id_formulario=$array_formularios[$m];
        $total_productividad=0;
        $productividad_control=false;
        $productividad_total=0;
        $productividad_agente_ajustada_resumen=0;

        if (isset($usuariosPorFormulario[$id_formulario])) {
          for ($i=0; $i < count($usuariosPorFormulario[$id_formulario]); $i++) {
            $id_agente_dash=$usuariosPorFormulario[$id_formulario][$i];
            
            $productividad_agente_total=$cumplimientoPorFormularioUsuarioResumen[$id_formulario][$id_agente_dash];
            $productividad_total+=$productividad_agente_total;

            if ($productividad_agente_total<100) {
              $productividad_control=true;
            }

            if (isset($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])) {
              if (count($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])==0) {
                $productividad_agente_ajustada_total=0;
              } else {
                $productividad_agente_ajustada_total=array_sum($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])/count($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada']);
              }
            } else {
              $productividad_agente_ajustada_total=0;
            }
              
            if ($productividad_agente_ajustada_total<$productividad_agente_total) {
              $productividad_agente_ajustada_total=$productividad_agente_total;
            }

            $productividad_agente_ajustada_resumen+=$productividad_agente_ajustada_total;
          }
          
          if (count($usuariosPorFormulario[$id_formulario])>0) {
            $productividad_total=number_format($productividad_total/count($usuariosPorFormulario[$id_formulario]), 2, '.', '');
            $productividad_agente_ajustada_resumen=number_format($productividad_agente_ajustada_resumen/count($usuariosPorFormulario[$id_formulario]), 2, '.', '');
          } else {
            $productividad_total=0;
            $productividad_agente_ajustada_resumen=0;
          }

          if ($productividad_total>=100) {
            $color_progress_total='bg-success';
            $productividad_total=100;
          } elseif ($productividad_total>=90) {
            $color_progress_total='bg-warning';
          } else {
            $color_progress_total='bg-danger';
          }


          if ($productividad_agente_ajustada_resumen==100) {
            $color_progress_ajustada_total='bg-success';
          } elseif ($productividad_agente_ajustada_resumen>=90) {
            $color_progress_ajustada_total='bg-warning';
          } else {
            $color_progress_ajustada_total='bg-danger';
          }
        }

        if(isset($usuariosPorFormulario[$id_formulario])) {
            for ($i=0; $i < count($usuariosPorFormulario[$id_formulario]); $i++) {
                $id_agente_dash=$usuariosPorFormulario[$id_formulario][$i];

                $id_coordinador=$usuarioscoordinadorArray[$id_agente_dash];
                $nombre_coordinador=$nombresCoordinadoresArray[$id_coordinador];
                $nombre_agente=$nombresUsuariosArray[$id_agente_dash];
                
                $productividad_agente=number_format($cumplimientoPorFormularioUsuarioResumen[$id_formulario][$id_agente_dash], 2, '.', '');

                if ($productividad_agente>=100) {
                  $color_progress='bg-success';
                } elseif ($productividad_agente>=90) {
                  $productividad_control=true;
                  $color_progress='bg-warning';
                } else {
                  $color_progress='bg-danger';
                  $productividad_control=true;
                }

                $meta_agente_array=$array_metas[$id_formulario]['meta'];

                if (count($meta_agente_array)>0) {
                  $meta_agente_total=array_sum($meta_agente_array)/count($meta_agente_array);
                } else {
                  $meta_agente_total=0;
                }
                  
                $meta_agente=round($meta_agente_total);

                // if (count($gestiones_agente_array)>0) {
                //   $gestiones_agente_total=array_sum($gestiones_agente_array)/count($gestiones_agente_array);
                // } else {
                //   $gestiones_agente_total=0;
                // }
                  
                // $gestiones_agente=round($gestiones_agente_total);
                
                // if (count($gestiones_hora_array)>0) {
                //   $hora_agente_total=array_sum($gestiones_hora_array)/count($gestiones_hora_array);
                // } else {
                //   $hora_agente_total=0;
                // }
                  
                // $hora_agente=round($hora_agente_total);

                // $total_no_reporta=0;
                // for ($j=8; $j < 18; $j++) { 
                //   if ($array_datos_gestion[$id_formulario]['gestion_agente_hora'][$id_agente_dash][$j]==0) {
                //     $total_no_reporta++;
                //   }
                // }

                
                if (isset($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])) {
                  if (count($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])==0) {
                    $productividad_agente_ajustada_total=0;
                  } else {
                    $productividad_agente_ajustada_total=number_format(array_sum($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])/count($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada']), 2, '.', '');
                  }
                } else {
                  $productividad_agente_ajustada_total=0;
                }
                  
                if ($productividad_agente_ajustada_total<$productividad_agente) {
                  $productividad_agente_ajustada_total=$productividad_agente;
                }

                $productividad_agente_ajustada=$productividad_agente_ajustada_total;

                if ($productividad_agente_ajustada==100) {
                  $color_progress_ajustada='bg-success';
                } elseif ($productividad_agente_ajustada>=90) {
                  $color_progress_ajustada='bg-warning';
                } else {
                  $color_progress_ajustada='bg-danger';
                }

                $dias_laborados=count($cumplimientoPorFormularioUsuarioFecha[$id_formulario][$id_agente_dash]);
                $promedio_gestiones_dia=round(array_sum($sumaCantidadPorFormularioUsuarioFecha[$id_formulario][$id_agente_dash])/$dias_laborados);

                if (count($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['tipologia'])>0) {
                  $tipología_agente=implode(';', array_values(array_unique($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['tipologia'])));
                } else {
                  $tipología_agente='';
                }

                if (count($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['novedad'])>0) {
                  $novedad_agente=implode(';', array_values(array_unique($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['novedad'])));
                } else {
                  $novedad_agente='';
                }

                $spreadsheet->getActiveSheet()->setCellValue('A'.$fila_registro_form, $array_metas[$id_formulario]['nombre']);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$fila_registro_form, $nombre_coordinador);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$fila_registro_form, $id_agente_dash);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$fila_registro_form, $nombre_agente);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$fila_registro_form, $productividad_agente);

                if($productividad_agente_ajustada>0) {
                    $spreadsheet->getActiveSheet()->setCellValue('F'.$fila_registro_form, $productividad_agente_ajustada);
                } else {
                    $spreadsheet->getActiveSheet()->setCellValue('F'.$fila_registro_form, $productividad_agente);
                }

                $spreadsheet->getActiveSheet()->setCellValue('G'.$fila_registro_form, $meta_agente);
                $spreadsheet->getActiveSheet()->setCellValue('H'.$fila_registro_form, $dias_laborados);
                $spreadsheet->getActiveSheet()->setCellValue('I'.$fila_registro_form, $promedio_gestiones_dia);
                $spreadsheet->getActiveSheet()->setCellValue('J'.$fila_registro_form, $cumplimientoPorFormularioUsuarioHora[$id_formulario][$id_agente_dash]);
                $spreadsheet->getActiveSheet()->setCellValue('K'.$fila_registro_form, '');
                $spreadsheet->getActiveSheet()->setCellValue('L'.$fila_registro_form, $tipología_agente);
                $spreadsheet->getActiveSheet()->setCellValue('M'.$fila_registro_form, $novedad_agente);
                // $spreadsheet->getActiveSheet()->setCellValue('L'.$fila_registro_form, $array_datos_gestion[$id_formulario_excel]['gestion_agente'][$id_agente_dash]['tipologia']);
                // $spreadsheet->getActiveSheet()->setCellValue('M'.$fila_registro_form, $array_datos_gestion[$id_formulario_excel]['gestion_agente'][$id_agente_dash]['novedad']);
                // $spreadsheet->getActiveSheet()->setCellValue('N'.$fila_registro_form, $array_datos_gestion[$id_formulario_excel]['gestion_agente'][$id_agente_dash]['comentarios']);

                $fila_registro_form++;
            }
        }
    }


    //Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$titulo_reporte.'"');
    header('Cache-Control: max-age=0');

    // Guardamos el archivo
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
?>