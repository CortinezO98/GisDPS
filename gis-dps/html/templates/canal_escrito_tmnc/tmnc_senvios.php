<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-TMNC";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Transacciones Monetarias No Condicionadas | 4. Envíos | ".$bandeja;
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='&bandeja='.base64_encode($bandeja);

  unset($_SESSION[APP_SESSION.'_registro_creado_canal_escrito']);

  // Inicializa variable tipo array
  $data_consulta=array();
  $data_consulta_conteo=array();
  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_permanente=validar_input($_POST['id_filtro']);
  } else {
      $filtro_permanente=validar_input($_GET['id']);
  }

  // Configuracón Paginación
  $registros_x_pagina=50;
  $iniciar_pagina=($pagina-1)*$registros_x_pagina;

  // Valida que filtro se deba ejecutar
  if ($filtro_permanente!="null" AND $filtro_permanente!="") {
      $filtro_buscar="AND (`cete_id_clasificacion` LIKE ? OR `cete_correo_electronico` LIKE ? OR `cete_fecha_ingreso` LIKE ? OR `cete_fecha_clasificacion` LIKE ? OR `cete_cedula_consulta` LIKE ? OR `cete_programa_solicitud` LIKE ? OR `cete_respuesta_enviada` LIKE ? OR `cete_con_datos` LIKE ? OR `cete_datos_incompletos` LIKE ? OR `cete_parrafo_plantilla_16` LIKE ? OR `cete_parrafo_plantilla_17` LIKE ? OR `cete_parrafo_plantilla_18` LIKE ? OR `cete_devolucion_correo` LIKE ? OR `cete_responsable_clasificacion` LIKE ? OR `cete_responsable_envio` LIKE ? OR `cete_observaciones` LIKE ? OR `cete_notificar` LIKE ? OR `cete_registro_usuario` LIKE ? OR `cete_registro_fecha` LIKE ? OR PROGRAMASOLICITUD.`ceco_valor` LIKE ? OR RESPUESTAENVIADA.`ceco_valor` LIKE ? OR CONDATOS.`ceco_valor` LIKE ? OR DATOSINCOMPLETOS.`ceco_valor` LIKE ? OR PLANTILLA16.`ceco_valor` LIKE ? OR PLANTILLA17.`ceco_valor` LIKE ? OR PLANTILLA18.`ceco_valor` LIKE ? OR DEVOLUCIONCORREO.`ceco_valor` LIKE ? OR RESPONSABLECLASIFICACION.`ceco_valor` LIKE ? OR TU.`usu_nombres_apellidos` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  if ($permisos_usuario=="Administrador") {
      $filtro_perfil="";
  } elseif ($permisos_usuario=="Gestor" OR $permisos_usuario=="Coordinador") {
      $filtro_perfil="";
  } elseif($permisos_usuario=="Supervisor"){
      $filtro_perfil="";
      // $filtro_perfil=" AND (TUA.`usu_supervisor`=? OR TMC.`gcm_analista`=?)";
      // array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  } elseif($permisos_usuario=="Usuario"){
      $filtro_perfil=" AND `cete_registro_usuario`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  if($bandeja=="Hoy"){
      $filtro_bandeja=" AND (`cete_registro_fecha`>=?)";
      array_push($data_consulta, date('Y-m-d'));
  } elseif($bandeja=="Histórico"){
      $filtro_bandeja=" AND (`cete_registro_fecha`<?)";
      array_push($data_consulta, date('Y-m-d'));
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`cete_id`) FROM `gestion_cetmnc_envios` LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_envios`.`cete_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS RESPUESTAENVIADA ON `gestion_cetmnc_envios`.`cete_respuesta_enviada`=RESPUESTAENVIADA.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS CONDATOS ON `gestion_cetmnc_envios`.`cete_con_datos`=CONDATOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS DATOSINCOMPLETOS ON `gestion_cetmnc_envios`.`cete_datos_incompletos`=DATOSINCOMPLETOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA16 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_16`=PLANTILLA16.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_17`=PLANTILLA17.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_18`=PLANTILLA18.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS DEVOLUCIONCORREO ON `gestion_cetmnc_envios`.`cete_devolucion_correo`=DEVOLUCIONCORREO.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLECLASIFICACION ON `gestion_cetmnc_envios`.`cete_responsable_clasificacion`=RESPONSABLECLASIFICACION.`ceco_id`
   LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_envios`.`cete_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja."";

  // Agrega string a sentencia preparada
  $consulta_contar_registros = $enlace_db->prepare($consulta_contar_string);
  
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_contar_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  // Ejecuta sentencia preparada
  $consulta_contar_registros->execute();
  // Obtiene array resultado de ejecución sentencia preparada
  $resultado_registros_contar = $consulta_contar_registros->get_result()->fetch_all(MYSQLI_NUM);
  $registros_cantidad_total = $resultado_registros_contar[0][0];
  //Cálculo número de páginas 
  $numero_paginas=ceil($registros_cantidad_total/$registros_x_pagina);

  //Agregar pagina a array data_consulta
  array_push($data_consulta, $iniciar_pagina);
  array_push($data_consulta, $registros_x_pagina);

  $consulta_string="SELECT `cete_id`, `cete_correo_electronico`, `cete_fecha_ingreso`, `cete_fecha_clasificacion`, `cete_cedula_consulta`, `cete_programa_solicitud`, `cete_respuesta_enviada`, `cete_con_datos`, `cete_datos_incompletos`, `cete_parrafo_plantilla_16`, `cete_parrafo_plantilla_17`, `cete_parrafo_plantilla_18`, `cete_devolucion_correo`, `cete_responsable_clasificacion`, `cete_responsable_envio`, `cete_observaciones`, `cete_notificar`, `cete_registro_usuario`, `cete_registro_fecha`, PROGRAMASOLICITUD.`ceco_valor`, RESPUESTAENVIADA.`ceco_valor`, CONDATOS.`ceco_valor`, DATOSINCOMPLETOS.`ceco_valor`, PLANTILLA16.`ceco_valor`, PLANTILLA17.`ceco_valor`, PLANTILLA18.`ceco_valor`, DEVOLUCIONCORREO.`ceco_valor`, RESPONSABLECLASIFICACION.`ceco_valor`, TU.`usu_nombres_apellidos`, `cete_id_clasificacion` FROM `gestion_cetmnc_envios`
   LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_envios`.`cete_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS RESPUESTAENVIADA ON `gestion_cetmnc_envios`.`cete_respuesta_enviada`=RESPUESTAENVIADA.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS CONDATOS ON `gestion_cetmnc_envios`.`cete_con_datos`=CONDATOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS DATOSINCOMPLETOS ON `gestion_cetmnc_envios`.`cete_datos_incompletos`=DATOSINCOMPLETOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA16 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_16`=PLANTILLA16.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_17`=PLANTILLA17.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_envios`.`cete_parrafo_plantilla_18`=PLANTILLA18.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS DEVOLUCIONCORREO ON `gestion_cetmnc_envios`.`cete_devolucion_correo`=DEVOLUCIONCORREO.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS RESPONSABLECLASIFICACION ON `gestion_cetmnc_envios`.`cete_responsable_clasificacion`=RESPONSABLECLASIFICACION.`ceco_id`
   LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_envios`.`cete_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja." ORDER BY `cete_id` DESC LIMIT ?,?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only"  onresize="headerFixTable();" onload="headerFixTable();">
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
          <div class="row">
            <div class="col-md-3">
              <?php require_once('tmnc_menu.php'); ?>
            </div>
            <div class="col-md-9">
              <div class="row">
                <div class="col-md-5 mb-1">
                  <?php require_once(ROOT.'includes/_search.php'); ?>
                </div>
                <div class="col-md-7 mb-1 text-end">
                  <a href="tmnc_senvios_crear?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Registro">
                    <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Registro</span>
                  </a>
                  <a href="tmnc_senvios?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Hoy'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Hoy">
                    <i class="fas fa-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Hoy</span>
                  </a>
                  <a href="tmnc_senvios?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
                    <i class="fas fa-history btn-icon-prepend me-0 font-size-12"></i><span class="d-none d-lg-inline"></span>
                  </a>
                    <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                      <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                    </button>
                </div>
                <div class="col-lg-12">
                  <div class="table-responsive table-fixed" id="headerFixTable">
                    <table class="table table-hover table-bordered table-striped">
                      <thead>
                        <tr>
                          <th class="px-1 py-2" style="width: 65px;"></th>
                          <th class="px-1 py-2">Id Clasificación</th>
                          <th class="px-1 py-2">Correo Electrónico</th>
                          <th class="px-1 py-2">Fecha Ingreso correo</th>
                          <th class="px-1 py-2">Fecha Clasificación</th>
                          <th class="px-1 py-2">Cédula a Consultar</th>
                          <th class="px-1 py-2">Programa al que se eleva solicitud</th>
                          <th class="px-1 py-2">Respuesta Enviada</th>
                          <th class="px-1 py-2">Con Datos</th>
                          <th class="px-1 py-2">Datos Incompletos</th>
                          <th class="px-1 py-2">Párrafo Plantilla 16</th>
                          <th class="px-1 py-2">Párrafo Plantilla 17</th>
                          <th class="px-1 py-2">Párrafo Plantilla 18</th>
                          <th class="px-1 py-2">Devolución Correo</th>
                          <th class="px-1 py-2">Responsable Clasificación</th>
                          <th class="px-1 py-2">Observaciones</th>
                          <th class="px-1 py-2">Registrado por</th>
                          <th class="px-1 py-2">Fecha Registro</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                        <tr>
                          <td class="p-1 text-center">
                              
                          </td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][29]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][1]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][3]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][19]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][20]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][21]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][22]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][23]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][24]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][25]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][26]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][27]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][15]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][28]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][18]; ?></td>
                        </tr>
                        <?php endfor; ?>
                      </tbody>
                    </table>
                    <?php if(count($resultado_registros)==0): ?>
                      <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
                    <?php endif; ?>
                  </div>
                </div>
                <?php require_once(ROOT.'includes/_pagination-footer.php'); ?>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- modal reportes -->
        <?php require_once('tmnc_senvios_reporte.php'); ?>
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>