<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Gestión";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión OCR";
  $subtitle = "Familias en Acción-Gestión | Procesado";
  $pagina=validar_input($_GET['pagina']);
  
  // Inicializa variable tipo array
  $data_consulta=array();
  
  // Ejemplo filtro campo buscar
  if (isset($_POST["filtro"])) {
      $pagina=1;
      $filtro_permanente=validar_input($_POST['id_filtro']);
      $filtro_estado_permanente=$_POST['id_estado'];
      if ($filtro_estado_permanente=='') {
          $filtro_estado_permanente=array();
      }
  } else {
      $filtro_permanente=validar_input($_GET['id']);
      $filtro_estado_permanente=validar_input($_GET['estado']);
      if ($filtro_estado_permanente!='null') {
          $filtro_estado_permanente=unserialize($_GET['estado']);
      } else {
          $filtro_estado_permanente=array();
      }
  }

  // Configuracón Paginación
  $registros_x_pagina=50;
  $iniciar_pagina=($pagina-1)*$registros_x_pagina;

  // Valida que filtro se deba ejecutar
  if (isset($filtro_estado_permanente)) {
      if (count($filtro_estado_permanente)>0 AND $filtro_estado_permanente!="") {
          $estado=serialize($filtro_estado_permanente);
          $estado=urlencode($estado);

          $filtro_buscar_estado="";

          //Agregar catidad de variables a filtrar a data consulta
          for ($i=0; $i < count($filtro_estado_permanente); $i++) { 
              if ($filtro_estado_permanente[$i]=='Contrato Existe') {
                  $filtro_buscar_estado.="`ocrc_contrato_existe`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Contrato NO existe') {
                  $filtro_buscar_estado.="`ocrc_contrato_existe`='' AND ";
              }

              if ($filtro_estado_permanente[$i]=='Contrato Código-Validado') {
                  $filtro_buscar_estado.="`ocrc_contrato_numid`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Contrato Código-NO Validado') {
                  $filtro_buscar_estado.="`ocrc_contrato_numid`='' AND ";
              }

              if ($filtro_estado_permanente[$i]=='Contrato Nombre Titular-Validado') {
                  $filtro_buscar_estado.="`ocrc_contrato_titular`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Contrato Nombre Titular-NO Validado') {
                  $filtro_buscar_estado.="`ocrc_contrato_titular`='' AND ";
              }

              if ($filtro_estado_permanente[$i]=='Contrato Firma-Validado') {
                  $filtro_buscar_estado.="`ocrc_contrato_firmado`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Contrato Firma-NO Validado') {
                  $filtro_buscar_estado.="`ocrc_contrato_firmado`='' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Contrato Huella-Validado') {
                  $filtro_buscar_estado.="`ocrc_contrato_huella`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Contrato Huella-NO Validado') {
                  $filtro_buscar_estado.="`ocrc_contrato_huella`='' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Documento-Validado') {
                  $filtro_buscar_estado.="`ocrc_doc_valida`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Documento-NO Validado') {
                  $filtro_buscar_estado.="`ocrc_doc_valida`='' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Nombres-Validado') {
                  $filtro_buscar_estado.="`ocrc_nombre_valida`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Nombres-NO Validado') {
                  $filtro_buscar_estado.="`ocrc_nombre_valida`='' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Apellidos-Validado') {
                  $filtro_buscar_estado.="`ocrc_apellido_valida`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Apellidos-NO Validado') {
                  $filtro_buscar_estado.="`ocrc_apellido_valida`='' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Fecha Nacimiento-Validado') {
                  $filtro_buscar_estado.="`ocrc_fnacimiento_valida`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Fecha Nacimiento-NO Validado') {
                  $filtro_buscar_estado.="`ocrc_fnacimiento_valida`='' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Fecha Expedición-Validado') {
                  $filtro_buscar_estado.="`ocrc_fexpedicion_valida`='1' AND ";
              }
              if ($filtro_estado_permanente[$i]=='Fecha Expedición-NO Validado') {
                  $filtro_buscar_estado.="`ocrc_fexpedicion_valida`='' AND ";
              }

          }

          $filtro_buscar_estado=" AND (".substr($filtro_buscar_estado, 0, -4).")";
      } else {
          $estado='null';
          $filtro_buscar_estado="";
      }
  } else {
      $estado='null';
      $filtro_buscar_estado="";
  }

  // Valida que filtro se deba ejecutar
  if ($filtro_permanente!="null" AND $filtro_permanente!="") {
      $filtro_buscar="AND (`ocrc_cod_familia` LIKE ? OR `ocrc_codbeneficiario` LIKE ? OR `ocrc_cabezafamilia` LIKE ? OR `ocrc_miembro_id` LIKE ? OR `ocrc_existe` LIKE ? OR `ocrc_doc_valida` LIKE ? OR `ocrc_doc_valor` LIKE ? OR `ocrc_doc_tipo` LIKE ? OR `ocrc_nombre_valida` LIKE ? OR `ocrc_nombre_valor` LIKE ? OR `ocrc_apellido_valida` LIKE ? OR `ocrc_apellido_valor` LIKE ? OR `ocrc_fnacimiento_valida` LIKE ? OR `ocrc_fnacimiento_valor` LIKE ? OR `ocrc_fexpedicion_valida` LIKE ? OR `ocrc_fexpedicion_valor` LIKE ? OR `ocrc_contrato_existe` LIKE ? OR `ocrc_contrato_numid` LIKE ? OR `ocrc_contrato_titular` LIKE ? OR `ocrc_contrato_municipio` LIKE ? OR `ocrc_contrato_departamento` LIKE ? OR `ocrc_contrato_firmado` LIKE ? OR `ocrc_contrato_huella` LIKE ? OR `ocrc_registro_path` LIKE ? OR `ocrc_resultado_estado` LIKE ? OR `ocrc_resultado_novedad` LIKE ? OR `ocrc_registro_fecha` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      for ($i=0; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`ocrc_id`) FROM `gestion_ocr_consolidado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_consolidado`.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE 1=1 ".$filtro_buscar_estado." ".$filtro_buscar."";

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

  if (!isset($_GET['pagina']) || ($pagina>$numero_paginas AND $numero_paginas>0) || $pagina<=0) {
      header('Location:familias_accion_procesado.php?pagina=1&id=null');
  }

  //Agregar pagina a array data_consulta
  array_push($data_consulta, $iniciar_pagina);
  array_push($data_consulta, $registros_x_pagina);

  $consulta_string="SELECT `ocrc_id`, `ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`, `ocrc_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion` FROM `gestion_ocr_consolidado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_consolidado`.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE 1=1 ".$filtro_buscar_estado." ".$filtro_buscar." ORDER BY `ocrc_cod_familia` ASC LIMIT ?,?";

  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $parametros_add='&estado='.$estado;
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only" onload="headerFixTable();" onresize="headerFixTable();">
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
            <div class="col-md-5 mb-1">
              <form name="filtrado" action="" method="POST">
                <div class="form-group m-0">
                  <div class="input-group">
                    <select class="selectpicker form-control form-control-sm form-select" name="id_estado[]" id="id_estado" multiple title="Estado">
                        <option value="Contrato Existe" <?php if(in_array("Contrato Existe", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato Existe</option>
                        <option value="Contrato NO existe" <?php if(in_array("Contrato NO existe", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato NO existe</option>
                        <option value="Contrato Código-Validado" <?php if(in_array("Contrato Código-Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato Código-Validado</option>
                        <option value="Contrato Código-NO Validado" <?php if(in_array("Contrato Código-NO Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato Código-NO Validado</option>
                        <option value="Contrato Nombre Titular-Validado" <?php if(in_array("Contrato Nombre Titular-Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato Nombre Titular-Validado</option>
                        <option value="Contrato Nombre Titular-NO Validado" <?php if(in_array("Contrato Nombre Titular-NO Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato Nombre Titular-NO Validado</option>

                        <option value="Contrato Firma-Validado" <?php if(in_array("Contrato Firma-Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato Firma-Validado</option>
                        <option value="Contrato Firma-NO Validado" <?php if(in_array("Contrato Firma-NO Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato Firma-NO Validado</option>
                        <option value="Contrato Huella-Validado" <?php if(in_array("Contrato Huella-Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato Huella-Validado</option>
                        <option value="Contrato Huella-NO Validado" <?php if(in_array("Contrato Huella-NO Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Contrato Huella-NO Validado</option>
                        <option value="Documento-Validado" <?php if(in_array("Documento-Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Documento-Validado</option>
                        <option value="Documento-NO Validado" <?php if(in_array("Documento-NO Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Documento-NO Validado</option>
                        <option value="Nombres-Validado" <?php if(in_array("Nombres-Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Nombres-Validado</option>
                        <option value="Nombres-NO Validado" <?php if(in_array("Nombres-NO Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Nombres-NO Validado</option>
                        <option value="Apellidos-Validado" <?php if(in_array("Apellidos-Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Apellidos-Validado</option>
                        <option value="Apellidos-NO Validado" <?php if(in_array("Apellidos-NO Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Apellidos-NO Validado</option>
                        <option value="Fecha Nacimiento-Validado" <?php if(in_array("Fecha Nacimiento-Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Fecha Nacimiento-Validado</option>
                        <option value="Fecha Nacimiento-NO Validado" <?php if(in_array("Fecha Nacimiento-NO Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Fecha Nacimiento-NO Validado</option>
                        <option value="Fecha Expedición-Validado" <?php if(in_array("Fecha Expedición-Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Fecha Expedición-Validado</option>
                        <option value="Fecha Expedición-NO Validado" <?php if(in_array("Fecha Expedición-NO Validado", $filtro_estado_permanente)){ echo "selected"; } ?>>Fecha Expedición-NO Validado</option>
                    </select>
                    <input type="text" class="form-control form-control-sm" name="id_filtro" value='<?php if (isset($_POST["filtro"])) { echo $filtro_permanente; } else {if($filtro_permanente!="null"){echo $filtro_permanente;}} ?>' placeholder="Búsqueda" autofocus>
                    <div class="input-group-append">
                      <button class="btn py-2 px-2 btn-primary btn-corp" type="submit" name="filtro"><span class="fas fa-search font-size-12"></span></button>
                      <a href="<?php echo $url_fichero; ?>?pagina=1&id=null<?php echo $parametros_add; ?>" class="btn py-2 px-2 btn-primary btn-corp"><span class="fas fa-sync-alt font-size-12"></span></a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="col-md-7 mb-1 text-end">
              <a href="familias_accion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Pendientes'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes</span>
              </a>
                <a href="familias_accion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Revalidación'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Revalidación">
                  <i class="fas fa-retweet btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Revalidación</span>
                </a>
              <?php if($permisos_usuario!="Visitante"): ?>
                <a href="familias_accion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Escalados'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Escalados">
                  <i class="fas fa-layer-group btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Escalados</span>
                </a>
                <a href="familias_accion?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Cerrados'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Cerrados">
                  <i class="fas fa-lock btn-icon-prepend me-0 font-size-12"></i><span class="d-none d-lg-inline"></span>
                </a>
              <?php endif; ?>
              <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Supervisor"): ?>
                <a href="familias_accion_sms?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Notificaciones SMS">
                  <i class="fas fa-sms btn-icon-prepend me-0 font-size-12"></i>
                </a>
                <a href="familias_accion_procesado?pagina=1&id=null&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Procesado">
                  <i class="fas fa-cogs btn-icon-prepend me-0 font-size-12"></i>
                </a>
                <a href="familias_accion_consolidado?pagina=1&id=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Consolidado">
                  <i class="fas fa-database btn-icon-prepend me-0 font-size-12"></i>
                </a>
                <a href="familias_accion_estadisticas" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Estadísticas">
                  <i class="fas fa-chart-pie btn-icon-prepend me-0 font-size-12"></i>
                </a>
              <?php endif; ?>
              <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor" OR $permisos_usuario=="Cliente"): ?>
                <!-- <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                  <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                </button> -->
              <?php endif; ?>
            </div>
            <div class="col-lg-12">
              <div class="table-responsive table-fixed" id="headerFixTable">
                <table class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th class="px-1 py-2">Cód. Familia</th>
                      <th class="px-1 py-2">Cód. Beneficiario</th>
                      <th class="px-1 py-2">Cabeza Familia</th>
                      <th class="px-1 py-2">Documento</th>
                      <th class="px-1 py-2">Nombres y Apellidos</th>
                      <th class="px-1 py-2">Estado</th>
                      <th class="px-1 py-2">Novedad</th>
                      <th class="px-1 py-2">Contrato Existe</th>
                      <th class="px-1 py-2">Contrato Código</th>
                      <th class="px-1 py-2">Contrato Nombre Titular</th>
                      <th class="px-1 py-2">Contrato Firma</th>
                      <th class="px-1 py-2">Contrato Huella</th>
                      <th class="px-1 py-2">Documento</th>
                      <th class="px-1 py-2">Nombres</th>
                      <th class="px-1 py-2">Apellidos</th>
                      <th class="px-1 py-2">Fecha Nacimiento</th>
                      <th class="px-1 py-2">Fecha Expedición</th>
                      <th class="px-1 py-2">Fecha Registro</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                    <tr>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][1]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][2]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][3]; ?></td>
                      <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][32]; ?></td>
                      <td class="p-1 font-size-11">
                          <?php echo $resultado_registros[$i][28]; ?>
                          <?php echo ($resultado_registros[$i][29]!="") ? ' '.$resultado_registros[$i][29] : ''; ?>
                          <?php echo ($resultado_registros[$i][30]!="") ? ' '.$resultado_registros[$i][30] : ''; ?>
                          <?php echo ($resultado_registros[$i][31]!="") ? ' '.$resultado_registros[$i][31] : ''; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][25]; ?></td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][26]; ?></td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                            <?php if ($resultado_registros[$i][17]==1): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_existe');" class="py-0 px-1 font-size-11" id="contrato_existe_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                            <?php elseif ($resultado_registros[$i][17]==''): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_existe');" class="py-0 px-1 font-size-11" id="contrato_existe_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                            <?php endif; ?>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                            <?php if ($resultado_registros[$i][18]!='' AND $resultado_registros[$i][18]!='NA'): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_codigo');" class="py-0 px-1 font-size-11" id="contrato_codigo_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                            <?php elseif ($resultado_registros[$i][18]==''): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_codigo');" class="py-0 px-1 font-size-11" id="contrato_codigo_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                            <?php endif; ?>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                            <?php if ($resultado_registros[$i][19]!='' AND $resultado_registros[$i][19]!='NA'): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_nombre');" class="py-0 px-1 font-size-11" id="contrato_nombre_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                            <?php elseif ($resultado_registros[$i][19]==''): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_nombre');" class="py-0 px-1 font-size-11" id="contrato_nombre_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                            <?php endif; ?>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                            <?php if ($resultado_registros[$i][22]==1): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_firma');" class="py-0 px-1 font-size-11" id="contrato_firma_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                            <?php elseif ($resultado_registros[$i][22]==''): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_firma');" class="py-0 px-1 font-size-11" id="contrato_firma_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                            <?php endif; ?>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][3]=='SI'): ?>
                            <?php if ($resultado_registros[$i][23]==1): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_huella');" class="py-0 px-1 font-size-11" id="contrato_huella_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                            <?php elseif ($resultado_registros[$i][23]==''): ?>
                              <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'contrato_huella');" class="py-0 px-1 font-size-11" id="contrato_huella_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                            <?php endif; ?>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][6]==1): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'documento');" class="py-0 px-1 font-size-11" id="documento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                          <?php elseif ($resultado_registros[$i][6]==''): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'documento');" class="py-0 px-1 font-size-11" id="documento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][9]==1): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'nombres');" class="py-0 px-1 font-size-11" id="nombres_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                          <?php elseif ($resultado_registros[$i][9]==''): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'nombres');" class="py-0 px-1 font-size-11" id="nombres_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][11]==1): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'apellidos');" class="py-0 px-1 font-size-11" id="apellidos_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                          <?php elseif ($resultado_registros[$i][11]==''): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'apellidos');" class="py-0 px-1 font-size-11" id="apellidos_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][13]==1): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_nacimiento');" class="py-0 px-1 font-size-11" id="fecha_nacimiento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                          <?php elseif ($resultado_registros[$i][13]==''): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_nacimiento');" class="py-0 px-1 font-size-11" id="fecha_nacimiento_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center">
                          <?php if ($resultado_registros[$i][15]==1): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_expedicion');" class="py-0 px-1 font-size-11" id="fecha_expedicion_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-check-circle color-verde'></span></a>
                          <?php elseif ($resultado_registros[$i][15]==''): ?>
                            <a href="#" onClick="open_modal_editar('<?php echo base64_encode($resultado_registros[$i][0]); ?>', '<?php echo base64_encode($resultado_registros[$i][2]); ?>', 'fecha_expedicion');" class="py-0 px-1 font-size-11" id="fecha_expedicion_<?php echo $resultado_registros[$i][0]; ?>"><span class='fas fa-times-circle color-rojo'></span></a>
                          <?php endif; ?>
                      </td>
                      <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][27]; ?></td>
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
        <!-- content-wrapper ends -->
        <!-- modal reportes -->
        <?php require_once('familias_accion_reporte.php'); ?>
        <!-- modal -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>