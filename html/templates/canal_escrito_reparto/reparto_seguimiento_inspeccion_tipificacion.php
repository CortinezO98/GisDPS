<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Reparto";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Canal Escrito";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Reparto | 14. Seguimiento Inspección Tipificación | ".$bandeja;
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
      $filtro_buscar="AND (`cesit_radicado` LIKE ? OR `cesit_abogado_tipificador` LIKE ? OR `cesit_abogado_aprobador` LIKE ? OR `cesit_traslado_entidades` LIKE ? OR `cesit_traslado_entidades_errado` LIKE ? OR `cesit_asignaciones_internas` LIKE ? OR `cesit_forma_correcta_peticion` LIKE ? OR `cesit_traslado_entidades_errado_senalar` LIKE ? OR `cesit_asignacion_errada` LIKE ? OR `cesit_asignacion_errada_2` LIKE ? OR `cesit_observaciones_asignacion` LIKE ? OR `cesit_relaciona_informacion_radicacion` LIKE ? OR `cesit_campo_errado` LIKE ? OR `cesit_diligencia_datos_solicitante` LIKE ? OR `cesit_campo_errado_2` LIKE ? OR `cesit_observaciones_diligencia_formulario` LIKE ? OR `cesit_notificar` LIKE ? OR `cesit_registro_usuario` LIKE ? OR `cesit_registro_fecha` LIKE ? OR abogado_tipificador.`usu_nombres_apellidos` LIKE ? OR abogado_aprobador.`usu_nombres_apellidos` LIKE ? OR traslado_entidades.`ceco_valor` LIKE ? OR traslado_entidades_errado.`ceco_valor` LIKE ? OR asignaciones_internas.`ceco_valor` LIKE ? OR forma_correcta_peticion.`ceco_valor` LIKE ? OR traslado_entidades_errado_senalar.`ceco_valor` LIKE ? OR asignacion_errada.`ceco_valor` LIKE ? OR asignacion_errada_2.`ceco_valor` LIKE ? OR relaciona_informacion_radicacion.`ceco_valor` LIKE ? OR campo_errado.`ceco_valor` LIKE ? OR diligencia_datos_solicitante.`ceco_valor` LIKE ? OR campo_errado_2.`ceco_valor` LIKE ? OR TU.`usu_nombres_apellidos` LIKE ?)";

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
      $filtro_perfil=" AND `cesit_registro_usuario`=?";
      array_push($data_consulta, $_SESSION[APP_SESSION.'_session_usu_id']);
  }

  if($bandeja=="Hoy"){
      $filtro_bandeja=" AND (`cesit_registro_fecha`>=?)";
      array_push($data_consulta, date('Y-m-d'));
  } elseif($bandeja=="Histórico"){
      $filtro_bandeja=" AND (`cesit_registro_fecha`<?)";
      array_push($data_consulta, date('Y-m-d'));
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`cesit_id`) FROM `gestion_cerep_seguimiento_inspeccion_tipificacion` LEFT JOIN `administrador_usuario` AS abogado_tipificador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_tipificador`=abogado_tipificador.`usu_id`
 LEFT JOIN `administrador_usuario` AS abogado_aprobador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_aprobador`=abogado_aprobador.`usu_id`
 LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades`=traslado_entidades.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado`=traslado_entidades_errado.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS asignaciones_internas ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignaciones_internas`=asignaciones_internas.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS forma_correcta_peticion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_forma_correcta_peticion`=forma_correcta_peticion.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado_senalar ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado_senalar`=traslado_entidades_errado_senalar.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada`=asignacion_errada.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada_2`=asignacion_errada_2.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS relaciona_informacion_radicacion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_relaciona_informacion_radicacion`=relaciona_informacion_radicacion.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS campo_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado`=campo_errado.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS diligencia_datos_solicitante ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_diligencia_datos_solicitante`=diligencia_datos_solicitante.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS campo_errado_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado_2`=campo_errado_2.`ceco_id`
 LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja."";

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

  $consulta_string="SELECT `cesit_id`, `cesit_radicado`, `cesit_abogado_tipificador`, `cesit_abogado_aprobador`, `cesit_traslado_entidades`, `cesit_traslado_entidades_errado`, `cesit_asignaciones_internas`, `cesit_forma_correcta_peticion`, `cesit_traslado_entidades_errado_senalar`, `cesit_asignacion_errada`, `cesit_asignacion_errada_2`, `cesit_observaciones_asignacion`, `cesit_relaciona_informacion_radicacion`, `cesit_campo_errado`, `cesit_diligencia_datos_solicitante`, `cesit_campo_errado_2`, `cesit_observaciones_diligencia_formulario`, `cesit_notificar`, `cesit_registro_usuario`, `cesit_registro_fecha`, abogado_tipificador.`usu_nombres_apellidos`, abogado_aprobador.`usu_nombres_apellidos`, traslado_entidades.`ceco_valor`, traslado_entidades_errado.`ceco_valor`, asignaciones_internas.`ceco_valor`, forma_correcta_peticion.`ceco_valor`, traslado_entidades_errado_senalar.`ceco_valor`, asignacion_errada.`ceco_valor`, asignacion_errada_2.`ceco_valor`, relaciona_informacion_radicacion.`ceco_valor`, campo_errado.`ceco_valor`, diligencia_datos_solicitante.`ceco_valor`, campo_errado_2.`ceco_valor`, TU.`usu_nombres_apellidos` FROM `gestion_cerep_seguimiento_inspeccion_tipificacion` LEFT JOIN `administrador_usuario` AS abogado_tipificador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_tipificador`=abogado_tipificador.`usu_id`
 LEFT JOIN `administrador_usuario` AS abogado_aprobador ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_abogado_aprobador`=abogado_aprobador.`usu_id`
 LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades`=traslado_entidades.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado`=traslado_entidades_errado.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS asignaciones_internas ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignaciones_internas`=asignaciones_internas.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS forma_correcta_peticion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_forma_correcta_peticion`=forma_correcta_peticion.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS traslado_entidades_errado_senalar ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_traslado_entidades_errado_senalar`=traslado_entidades_errado_senalar.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada`=asignacion_errada.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS asignacion_errada_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_asignacion_errada_2`=asignacion_errada_2.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS relaciona_informacion_radicacion ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_relaciona_informacion_radicacion`=relaciona_informacion_radicacion.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS campo_errado ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado`=campo_errado.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS diligencia_datos_solicitante ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_diligencia_datos_solicitante`=diligencia_datos_solicitante.`ceco_id`
 LEFT JOIN `gestion_ce_configuracion` AS campo_errado_2 ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_campo_errado_2`=campo_errado_2.`ceco_id`
 LEFT JOIN `administrador_usuario` AS TU ON `gestion_cerep_seguimiento_inspeccion_tipificacion`.`cesit_registro_usuario`=TU.`usu_id` WHERE 1=1 ".$filtro_buscar." ".$filtro_perfil." ".$filtro_bandeja." ORDER BY `cesit_id` DESC LIMIT ?,?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $consulta_string_parametros="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha` FROM `gestion_ce_configuracion` WHERE `ceco_formulario`='seguimiento_inspeccion_tipificacion' ORDER BY `ceco_campo`, `ceco_valor`";
  $consulta_registros_parametros = $enlace_db->prepare($consulta_string_parametros);
  $consulta_registros_parametros->execute();
  $resultado_registros_parametros = $consulta_registros_parametros->get_result()->fetch_all(MYSQLI_NUM);

  for ($i=0; $i < count($resultado_registros_parametros); $i++) { 
      $array_parametros[$resultado_registros_parametros[$i][2]]['texto'][$resultado_registros_parametros[$i][0]]=$resultado_registros_parametros[$i][3];
  }
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
              <?php require_once('reparto_menu.php'); ?>
            </div>
            <div class="col-md-9">
              <div class="row">
                <div class="col-md-5 mb-1">
                  <?php require_once(ROOT.'includes/_search.php'); ?>
                </div>
                <div class="col-md-7 mb-1 text-end">
                  <a href="reparto_seguimiento_inspeccion_tipificacion_crear?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode($bandeja); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Registro">
                    <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Registro</span>
                  </a>
                  <a href="reparto_seguimiento_inspeccion_tipificacion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Hoy'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Hoy">
                    <i class="fas fa-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Hoy</span>
                  </a>
                  <a href="reparto_seguimiento_inspeccion_tipificacion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Histórico'); ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Histórico">
                    <i class="fas fa-history btn-icon-prepend me-0 font-size-12"></i><span class="d-none d-lg-inline"></span>
                  </a>
                    <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                      <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                    </button>
                  <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Cliente"): ?>
                  <?php endif; ?>
                </div>
                <div class="col-lg-12">
                  <div class="table-responsive table-fixed" id="headerFixTable">
                    <table class="table table-hover table-bordered table-striped">
                      <thead>
                        <tr>
                          <th class="px-1 py-2" style="width: 65px;"></th>
                          <th class="px-1 py-2">No. radicado</th>
                          <th class="px-1 py-2">Abogado tipificador</th>
                          <th class="px-1 py-2">Abogado aprobador</th>
                          <th class="px-1 py-2">6. Traslados a otras entidades</th>
                          <th class="px-1 py-2">7. Traslado errado entidades</th>
                          <th class="px-1 py-2">7.1. Traslado errado entidades (Señalar la entidad)</th>
                          <th class="px-1 py-2">8. Asignaciones internas P.S</th>
                          <th class="px-1 py-2">8.1. Asignación P.S errada</th>
                          <th class="px-1 py-2">8.2. Asignación P.S errada</th>
                          <th class="px-1 py-2">9. Determina de forma correcta el tipo de petición </th>
                          <th class="px-1 py-2">14. Relaciona de manera correcta los datos del campo "información radicación"</th>
                          <th class="px-1 py-2">15. Campo errado</th>
                          <th class="px-1 py-2">16. Diligencia de manera correcta los datos del solicitante</th>
                          <th class="px-1 py-2">17. Campo errado</th>
                          <th class="px-1 py-2">18. Observación (Aportes o recomendaciones evidenciados en el diligenciamiento del formulario de tipificación que permita adelantar los PDA)</th>
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
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][20]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][21]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][22]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][23]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][8]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][24]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][27]; ?></td>
                          <td class="p-1 font-size-11">
                            <?php
                              $cesit_asignacion_errada_2=explode(';', $resultado_registros[$i][10]);

                              $cesit_asignacion_errada_2_mostrar='';
                              for ($j=0; $j < count($cesit_asignacion_errada_2); $j++) { 
                                if ($cesit_asignacion_errada_2[$j]!="") {
                                    $cesit_asignacion_errada_2_mostrar.=$array_parametros['asignacion_errada_2']['texto'][$cesit_asignacion_errada_2[$j]].'<br>';
                                }
                              }
                            ?>
                            <?php echo $cesit_asignacion_errada_2_mostrar; ?>
                          </td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][25]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][29]; ?></td>
                          <td class="p-1 font-size-11">
                            <?php
                              $cesit_campo_errado=explode(';', $resultado_registros[$i][13]);

                              $cesit_campo_errado_mostrar='';
                              for ($j=0; $j < count($cesit_campo_errado); $j++) { 
                                if ($cesit_campo_errado[$j]) {
                                    $cesit_campo_errado_mostrar.=$array_parametros['campo_errado']['texto'][$cesit_campo_errado[$j]].'<br>';
                                }
                              }
                            ?>
                            <?php echo $cesit_campo_errado_mostrar; ?>
                          </td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][31]; ?></td>
                          <td class="p-1 font-size-11">
                            <?php
                              $cesit_campo_errado_2=explode(';', $resultado_registros[$i][15]);

                              $cesit_campo_errado_2_mostrar='';
                              for ($j=0; $j < count($cesit_campo_errado_2); $j++) { 
                                if ($cesit_campo_errado_2[$j]) {
                                    $cesit_campo_errado_2_mostrar.=$array_parametros['campo_errado_2']['texto'][$cesit_campo_errado_2[$j]].'<br>';
                                }
                              }
                            ?>
                            <?php echo $cesit_campo_errado_2_mostrar; ?>
                          </td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][16]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][33]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][19]; ?></td>
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
        <?php require_once('reparto_seguimiento_inspeccion_tipificacion_reporte.php'); ?>
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>