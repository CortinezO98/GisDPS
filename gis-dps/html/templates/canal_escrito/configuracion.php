<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Canal Escrito-Configuración";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $parametro=validar_input($_GET['par']);
  $title = "Canal Escrito";
  $subtitle = "Configuración | ".$parametro;
  $pagina=validar_input($_GET['pagina']);
  $parametros_add='&par='.$parametro;

  unset($_SESSION[APP_SESSION.'_registro_creado_canal_escrito_configuracion']);

  // Inicializa variable tipo array
  $data_consulta=array();
  array_push($data_consulta, $parametro);
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
      $filtro_buscar="AND (`ceco_formulario` LIKE ? OR `ceco_campo` LIKE ? OR `ceco_valor` LIKE ? OR `ceco_estado` LIKE ? OR `ceco_actualiza_usuario` LIKE ? OR `ceco_actualiza_fecha` LIKE ? OR `ceco_registro_usuario` LIKE ? OR `ceco_registro_fecha` LIKE ? OR TUA.`usu_nombres_apellidos` LIKE ? OR TUR.`usu_nombres_apellidos`)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`ceco_id`) FROM `gestion_ce_configuracion` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_ce_configuracion`.`ceco_actualiza_usuario`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TUR ON `gestion_ce_configuracion`.`ceco_registro_usuario`=TUR.`usu_id` WHERE 1=1 AND `ceco_formulario`=? ".$filtro_buscar."";

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

  $consulta_string="SELECT `ceco_id`, `ceco_formulario`, `ceco_campo`, `ceco_valor`, `ceco_estado`, `ceco_actualiza_usuario`, `ceco_actualiza_fecha`, `ceco_registro_usuario`, `ceco_registro_fecha`, TUA.`usu_nombres_apellidos`, TUR.`usu_nombres_apellidos` FROM `gestion_ce_configuracion` LEFT JOIN `administrador_usuario` AS TUA ON `gestion_ce_configuracion`.`ceco_actualiza_usuario`=TUA.`usu_id` LEFT JOIN `administrador_usuario` AS TUR ON `gestion_ce_configuracion`.`ceco_registro_usuario`=TUR.`usu_id` WHERE 1=1 AND `ceco_formulario`=? ".$filtro_buscar." ORDER BY `ceco_formulario`, `ceco_campo`, `ceco_valor` ASC LIMIT ?,?";

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
          <div class="row justify-content-center">
            <div class="col-md-3">
              <!-- FASE 1 -->
              <a href="configuracion?pagina=1&id=null&par=proyeccion_consolidacion" class="btn btn-dark col-md-12 text-start mb-1 p-1">1. Proyección Consolidación</a>
              <a href="configuracion?pagina=1&id=null&par=aprobacion_firma_fa" class="btn btn-dark col-md-12 text-start mb-1 p-1">2. Aprobación Firma FA</a>
              <a href="configuracion?pagina=1&id=null&par=firma_fa" class="btn btn-dark col-md-12 text-start mb-1 p-1">3. Firma FA</a>
              <a href="configuracion?pagina=1&id=null&par=inspeccion_proyeccion" class="btn btn-dark col-md-12 text-start mb-1 p-1">4. Inspección Proyección</a>
              <a href="configuracion?pagina=1&id=null&par=proyeccion_fa" class="btn btn-dark col-md-12 text-start mb-1 p-1">5. Proyección FA</a>
              <a href="configuracion?pagina=1&id=null&par=aprobacion_firma" class="btn btn-dark col-md-12 text-start mb-1 p-1">6. Aprobación Firma</a>
              <a href="configuracion?pagina=1&id=null&par=firma_traslados" class="btn btn-dark col-md-12 text-start mb-1 p-1">7. Firma Traslados</a>
              <!-- FASE 2 -->
              <a href="configuracion?pagina=1&id=null&par=proyectores" class="btn btn-dark col-md-12 text-start mb-1 p-1">8. Proyectores</a>
              <a href="configuracion?pagina=1&id=null&par=lanzamientos_tr" class="btn btn-dark col-md-12 text-start mb-1 p-1">9. Seguimiento Lanzamientos TR</a>
              <a href="configuracion?pagina=1&id=null&par=seguimiento_envios_web" class="btn btn-dark col-md-12 text-start mb-1 p-1">10. Seguimiento Envíos Web</a>
              <a href="configuracion?pagina=1&id=null&par=seguimiento_cargue_documentos" class="btn btn-dark col-md-12 text-start mb-1 p-1">11. Seguimiento Cargue Documentos</a>
              <a href="configuracion?pagina=1&id=null&par=seguimiento_radicacion" class="btn btn-dark col-md-12 text-start mb-1 p-1">12. Seguimiento Radicación</a>
              <a href="configuracion?pagina=1&id=null&par=seguimiento_tipificaciones" class="btn btn-dark col-md-12 text-start mb-1 p-1">13. Seguimiento Tipificaciones</a>
              <a href="configuracion?pagina=1&id=null&par=seguimiento_inspeccion_tipificacion" class="btn btn-dark col-md-12 text-start mb-1 p-1">14. Seguimiento Inspección Tipificación</a>

              <!-- JAFOCALIZACION -->
              <a href="configuracion?pagina=1&id=null&par=jafocalizacion_proyeccion_peticiones" class="btn btn-dark col-md-12 text-start mb-1 p-1">15. Proyección de Peticiones</a>
              <a href="configuracion?pagina=1&id=null&par=jafocalizacion_revision_peticiones" class="btn btn-dark col-md-12 text-start mb-1 p-1">16. Revisión de Peticiones</a>
              <a href="configuracion?pagina=1&id=null&par=jafocalizacion_relacion_rae" class="btn btn-dark col-md-12 text-start mb-1 p-1">17. Formato de Relación RAE</a>
              <a href="configuracion?pagina=1&id=null&par=jafocalizacion_gestion_correos" class="btn btn-dark col-md-12 text-start mb-1 p-1">18. Formato de Gestión de Correos</a>
              <a href="configuracion?pagina=1&id=null&par=jafocalizacion_gestion_novedades" class="btn btn-dark col-md-12 text-start mb-1 p-1">19. Formato Gestión de Novedades </a>
              <a href="configuracion?pagina=1&id=null&par=jafocalizacion_gestion_peticiones" class="btn btn-dark col-md-12 text-start mb-1 p-1">20. Formato de Gestión de Peticiones</a>
              <a href="configuracion?pagina=1&id=null&par=jafocalizacion_gestion_aprobacion" class="btn btn-dark col-md-12 text-start mb-1 p-1">21. Formato Gestión de Aprobación</a>
              <a href="configuracion?pagina=1&id=null&par=jafocalizacion_entrega_fisica" class="btn btn-dark col-md-12 text-start mb-1 p-1">22. Formato Entrega Física</a>

              <!-- TMNC -->
              <a href="configuracion?pagina=1&id=null&par=tmnc_sproyeccion_respuestas" class="btn btn-dark col-md-12 text-start mb-1 p-1">23. Proyección de Respuestas</a>
              <a href="configuracion?pagina=1&id=null&par=tmnc_saprobacion_respuestas" class="btn btn-dark col-md-12 text-start mb-1 p-1">24. Aprobación Respuesta</a>
              <a href="configuracion?pagina=1&id=null&par=tmnc_sclasificacion" class="btn btn-dark col-md-12 text-start mb-1 p-1">25. Clasificación</a>
              <a href="configuracion?pagina=1&id=null&par=tmnc_senvios" class="btn btn-dark col-md-12 text-start mb-1 p-1">26. Envíos</a>
              <a href="configuracion?pagina=1&id=null&par=tmnc_sfirma_respuesta" class="btn btn-dark col-md-12 text-start mb-1 p-1">27. Firma Respuesta</a>
              <a href="configuracion?pagina=1&id=null&par=tmnc_spendientes_clasificacion" class="btn btn-dark col-md-12 text-start mb-1 p-1">28. Pendientes Clasificación</a>
              <a href="configuracion?pagina=1&id=null&par=tmnc_scasos_sgestionar" class="btn btn-dark col-md-12 text-start mb-1 p-1">29. Casos Sin Gestionar</a>
              <a href="configuracion?pagina=1&id=null&par=tmnc_saprobacion_novedades" class="btn btn-dark col-md-12 text-start mb-1 p-1">30. Aprobación Novedades CM</a>
            </div>
            <?php if($parametro!=""): ?>
            <div class="col-md-9">
              <div class="row">
                <div class="col-md-5 mb-1">
                  <?php require_once(ROOT.'includes/_search.php'); ?>
                </div>
                <div class="col-md-7 mb-1 text-end">
                  <a href="configuracion_crear?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&par=<?php echo $parametro; ?>" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Crear Registro">
                    <i class="fas fa-plus btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Crear Registro</span>
                  </a>
                </div>
                <div class="col-lg-12">
                  <div class="table-responsive table-fixed" id="headerFixTable">
                    <table class="table table-hover table-bordered table-striped">
                      <thead>
                        <tr>
                          <th class="px-1 py-2" style="width: 65px;"></th>
                          <th class="px-1 py-2">Formulario</th>
                          <th class="px-1 py-2">Campo</th>
                          <th class="px-1 py-2">Valor</th>
                          <th class="px-1 py-2">Estado</th>
                          <th class="px-1 py-2">Registrado por</th>
                          <th class="px-1 py-2">Fecha Registro</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                        <tr>
                          <td class="p-1 text-center">
                              <a href="configuracion_editar?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&par=<?php echo $parametro; ?>&reg=<?php echo base64_encode($resultado_registros[$i][0]); ?>" class="btn btn-warning btn-icon px-1 py-1 mb-1" title="Editar"><i class="fas fa-pen font-size-11"></i></a>
                          </td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][1]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][2]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][3]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][4]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][10]; ?></td>
                          <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][8]; ?></td>
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
            <?php endif; ?>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- modal reportes -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>