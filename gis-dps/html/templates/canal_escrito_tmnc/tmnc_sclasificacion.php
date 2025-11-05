<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-TMNC";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Transacciones Monetarias No Condicionadas | 3. Clasificación | ".$bandeja;
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
      $filtro_buscar="AND (`cetc_correo_electronico` LIKE ? OR `cetc_fecha_ingreso_correo` LIKE ? OR `cetc_nombre_ciudadano` LIKE ? OR `cetc_cedula_consulta` LIKE ? OR `cetc_asunto_correo` LIKE ? OR `cetc_programa_solicitud` LIKE ? OR `cetc_plantilla_utilizada` LIKE ? OR `cetc_solicitud_ciudadano` LIKE ? OR `cetc_plantilla_datos_incompletos` LIKE ? OR `cetc_plantilla_datos_completos` LIKE ? OR `cetc_parrafo_radicacion` LIKE ? OR `cetc_parrafo_plantilla_1` LIKE ? OR `cetc_parrafo_plantilla_4` LIKE ? OR `cetc_parrafo_plantilla_5` LIKE ? OR `cetc_parrafo_plantilla_6` LIKE ? OR `cetc_situacion_plantilla_8` LIKE ? OR `cetc_parrafo_plantilla_8` LIKE ? OR `cetc_parrafo_plantilla_10` LIKE ? OR `cetc_titular_hogar` LIKE ? OR `cetc_parrafo_plantilla_14` LIKE ? OR `cetc_parrafo_plantilla_16` LIKE ? OR `cetc_situacion_plantilla_17` LIKE ? OR `cetc_parrafo_plantilla_17` LIKE ? OR `cetc_situacion_plantilla_18` LIKE ? OR `cetc_parrafo_plantilla_18` LIKE ? OR `cetc_parrafo_plantilla_20` LIKE ? OR `cetc_nombre_solicitante` LIKE ? OR `cetc_nombre_titular` LIKE ? OR `cetc_parrafo_plantilla_21` LIKE ? OR `cetc_situacion_plantilla_22` LIKE ? OR `cetc_parrafo_plantilla_22` LIKE ? OR `cetc_parrafo_plantilla_23` LIKE ? OR `cetc_motivo_devolucion` LIKE ? OR `cetc_observaciones` LIKE ? OR `cetc_notificar` LIKE ? OR `cetc_registro_usuario` LIKE ? OR `cetc_registro_fecha` LIKE ? OR PROGRAMASOLICITUD.`ceco_valor` LIKE ? OR PUTILIZADA.`ceco_valor` LIKE ? OR PDATOSINCOMPLETOS.`ceco_valor` LIKE ? OR PDATOSCOMPLETOS.`ceco_valor` LIKE ? OR PLANTILLA8.`ceco_valor` LIKE ? OR PLANTILLA17.`ceco_valor` LIKE ? OR PLANTILLA18.`ceco_valor` LIKE ? OR PLANTILLA22.`ceco_valor` LIKE ? OR MOTIVODEVOLUCION.`ceco_valor` LIKE ? OR TU.`usu_nombres_apellidos` LIKE ?)";

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
      $filtro_perfil=" AND `cetc_registro_usuario`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  if($bandeja=="Hoy"){
      $filtro_bandeja=" AND (`cetc_registro_fecha`>=?)";
      array_push($data_consulta, date('Y-m-d'));
  } elseif($bandeja=="Histórico"){
      $filtro_bandeja=" AND (`cetc_registro_fecha`<?)";
      array_push($data_consulta, date('Y-m-d'));
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`cetc_id`) FROM `gestion_cetmnc_clasificacion` LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_clasificacion`.`cetc_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PUTILIZADA ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_utilizada`=PUTILIZADA.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PDATOSINCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_incompletos`=PDATOSINCOMPLETOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PDATOSCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_completos`=PDATOSCOMPLETOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA8 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_8`=PLANTILLA8.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_17`=PLANTILLA17.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_18`=PLANTILLA18.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA22 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_22`=PLANTILLA22.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS MOTIVODEVOLUCION ON `gestion_cetmnc_clasificacion`.`cetc_motivo_devolucion`=MOTIVODEVOLUCION.`ceco_id`
   LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_clasificacion`.`cetc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja."";

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

  $consulta_string="SELECT `cetc_id`, `cetc_correo_electronico`, `cetc_fecha_ingreso_correo`, `cetc_nombre_ciudadano`, `cetc_cedula_consulta`, `cetc_asunto_correo`, `cetc_programa_solicitud`, `cetc_plantilla_utilizada`, `cetc_solicitud_ciudadano`, `cetc_plantilla_datos_incompletos`, `cetc_plantilla_datos_completos`, `cetc_parrafo_radicacion`, `cetc_parrafo_plantilla_1`, `cetc_parrafo_plantilla_4`, `cetc_parrafo_plantilla_5`, `cetc_parrafo_plantilla_6`, `cetc_situacion_plantilla_8`, `cetc_parrafo_plantilla_8`, `cetc_parrafo_plantilla_10`, `cetc_titular_hogar`, `cetc_parrafo_plantilla_14`, `cetc_parrafo_plantilla_16`, `cetc_situacion_plantilla_17`, `cetc_parrafo_plantilla_17`, `cetc_situacion_plantilla_18`, `cetc_parrafo_plantilla_18`, `cetc_parrafo_plantilla_20`, `cetc_nombre_solicitante`, `cetc_nombre_titular`, `cetc_parrafo_plantilla_21`, `cetc_situacion_plantilla_22`, `cetc_parrafo_plantilla_22`, `cetc_parrafo_plantilla_23`, `cetc_parrafo_plantilla_25`, `cetc_parrafo_plantilla_26`, `cetc_parrafo_plantilla_reemplazo`, `cetc_motivo_devolucion`, `cetc_observaciones`, `cetc_notificar`, `cetc_registro_usuario`, `cetc_registro_fecha`, PROGRAMASOLICITUD.`ceco_valor`, PUTILIZADA.`ceco_valor`, PDATOSINCOMPLETOS.`ceco_valor`, PDATOSCOMPLETOS.`ceco_valor`, PLANTILLA8.`ceco_valor`, PLANTILLA17.`ceco_valor`, PLANTILLA18.`ceco_valor`, PLANTILLA22.`ceco_valor`, MOTIVODEVOLUCION.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cetmnc_clasificacion`
   LEFT JOIN `gestion_ce_configuracion` AS PROGRAMASOLICITUD ON `gestion_cetmnc_clasificacion`.`cetc_programa_solicitud`=PROGRAMASOLICITUD.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PUTILIZADA ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_utilizada`=PUTILIZADA.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PDATOSINCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_incompletos`=PDATOSINCOMPLETOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PDATOSCOMPLETOS ON `gestion_cetmnc_clasificacion`.`cetc_plantilla_datos_completos`=PDATOSCOMPLETOS.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA8 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_8`=PLANTILLA8.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA17 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_17`=PLANTILLA17.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA18 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_18`=PLANTILLA18.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS PLANTILLA22 ON `gestion_cetmnc_clasificacion`.`cetc_situacion_plantilla_22`=PLANTILLA22.`ceco_id`
   LEFT JOIN `gestion_ce_configuracion` AS MOTIVODEVOLUCION ON `gestion_cetmnc_clasificacion`.`cetc_motivo_devolucion`=MOTIVODEVOLUCION.`ceco_id`
   LEFT JOIN `administrador_usuario` AS TU ON `gestion_cetmnc_clasificacion`.`cetc_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja." ORDER BY `cetc_id` DESC LIMIT ?,?";

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
                  <a href="tmnc_sclasificacion_crear?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Registro">
                    <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Registro</span>
                  </a>
                  <a href="tmnc_sclasificacion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Hoy'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Hoy">
                    <i class="fas fa-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Hoy</span>
                  </a>
                  <a href="tmnc_sclasificacion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
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
                          <th class="px-1 py-2">Correo electrónico</th>
                          <th class="px-1 py-2">Fecha ingreso correo</th>
                          <th class="px-1 py-2">Nombre del Ciudadano</th>
                          <th class="px-1 py-2">Cédula a consultar</th>
                          <th class="px-1 py-2">Asunto del correo</th>
                          <th class="px-1 py-2">Programa al que eleva la solicitud</th>
                          <th class="px-1 py-2">Plantilla utilizada</th>
                          <th class="px-1 py-2">Solicitud del ciudadano</th>
                          <th class="px-1 py-2">Plantilla datos incompletos</th>
                          <th class="px-1 py-2">Plantilla datos completos</th>
                          <th class="px-1 py-2">Párrafo en proceso de radicación o respuesta</th>
                          <th class="px-1 py-2">Párrafo plantilla 6</th>
                          <th class="px-1 py-2">Situación plantilla 8</th>
                          <th class="px-1 py-2">Párrafo plantilla 8</th>
                          <th class="px-1 py-2">Párrafo plantilla 10</th>
                          <th class="px-1 py-2">Titular del hogar</th>
                          <th class="px-1 py-2">Párrafo plantilla 14</th>
                          <th class="px-1 py-2">Párrafo plantilla 16</th>
                          <th class="px-1 py-2">Situación plantilla 17</th>
                          <th class="px-1 py-2">Párrafo plantilla 17</th>
                          <th class="px-1 py-2">Situación plantilla 18</th>
                          <th class="px-1 py-2">Párrafo plantilla 18</th>
                          <th class="px-1 py-2">Párrafo plantilla 20</th>
                          <th class="px-1 py-2">Párrafo plantilla 21</th>
                          <th class="px-1 py-2">Situación plantilla 22</th>
                          <th class="px-1 py-2">Párrafo plantilla 22</th>
                          <th class="px-1 py-2">Párrafo plantilla 23</th>
                          <th class="px-1 py-2">Párrafo plantilla 25</th>
                          <th class="px-1 py-2">Párrafo plantilla 26</th>
                          <th class="px-1 py-2">Párrafo plantilla reemplazo</th>
                          <th class="px-1 py-2">Motivo devolución correo</th>
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
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][1]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][3]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][5]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][41]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][42]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][8]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][43]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][44]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][11]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][15]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][45]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][17]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][18]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][19]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][20]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][21]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][46]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][23]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][47]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][25]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][26]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][29]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][48]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][31]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][32]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][33]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][34]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][35]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][49]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][37]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][50]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][40]; ?></td>
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
        <?php require_once('tmnc_sclasificacion_reporte.php'); ?>
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>