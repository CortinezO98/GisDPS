<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Consultas Enlace";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión OCR";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Familias en Acción-Consultas Enlace | ".$bandeja;
  $pagina=validar_input($_GET['pagina']);
  
  unset($_SESSION[APP_SESSION.'_registro_creado_familias_accion']);
  unset($_SESSION[APP_SESSION.'_registro_creado_familias_accion_asignar']);
  // Inicializa variable tipo array
  $data_consulta=array();

  $consulta_string_filtros="SELECT `ocra_id`, `ocra_usuario`, `ocra_tipo`, `ocra_codigo` FROM `gestion_ocr_agentes` WHERE `ocra_usuario`=?";
  $consulta_registros_filtro = $enlace_db->prepare($consulta_string_filtros);
  $consulta_registros_filtro->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
  $consulta_registros_filtro->execute();
  $resultado_registros_filtro = $consulta_registros_filtro->get_result()->fetch_all(MYSQLI_NUM);
  
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

  if ($permisos_usuario=="Administrador") {
      $filtro_perfil="";
  } elseif ($permisos_usuario=="Gestor") {
      $filtro_perfil="";
  } elseif($permisos_usuario=="Supervisor"){
      $filtro_perfil="";
  } elseif($permisos_usuario=="Cliente"){
      $filtro_perfil="";
  } elseif($permisos_usuario=="Usuario"){
      $filtro_perfil="";
      for ($i=0; $i < count($resultado_registros_filtro); $i++) { 
        if ($resultado_registros_filtro[$i][2]=='Departamento') {
          $filtro_perfil.="TOCR.`ocr_cod_departamento`=? OR ";
          array_push($data_consulta, $resultado_registros_filtro[$i][3]);
        } elseif ($resultado_registros_filtro[$i][2]=='Municipio') {
          $filtro_perfil.="TOCR.`ocr_cod_municipio`=? OR ";
          array_push($data_consulta, $resultado_registros_filtro[$i][3]);
        }
      }

      if (count($resultado_registros_filtro)>0) {
        $filtro_perfil=" AND (".substr($filtro_perfil, 0, -4).")";
      }
  }

  // Valida que filtro se deba ejecutar
  if (isset($filtro_estado_permanente)) {
      if (count($filtro_estado_permanente)>0 AND $filtro_estado_permanente!="") {
          $estado=serialize($filtro_estado_permanente);
          $estado=urlencode($estado);

          $filtro_buscar_estado="";

          //Agregar catidad de variables a filtrar a data consulta
          for ($i=0; $i < count($filtro_estado_permanente); $i++) { 
              $filtro_buscar_estado.="`ocrr_gestion_estado`=? OR ";
              array_push($data_consulta, $filtro_estado_permanente[$i]);
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
      $filtro_buscar="AND (`ocrr_cod_familia`=? OR `ocrr_codbeneficiario`=? OR `ocrr_gestion_agente` LIKE ? OR `ocrr_gestion_estado` LIKE ? OR `ocrr_gestion_observaciones` LIKE ? OR TOCR.`ocr_primernombre` LIKE ? OR TOCR.`ocr_segundonombre` LIKE ? OR TOCR.`ocr_primerapellido` LIKE ? OR TOCR.`ocr_segundoapellido` LIKE ? OR TOCR.`ocr_documento` LIKE ? OR TOCR.`ocr_fechanacimiento` LIKE ? OR TOCR.`ocr_fechaexpedicion` LIKE ? OR TAG.`usu_nombres_apellidos` LIKE ?)";

      //Contar catidad de variables a filtrar
      $cantidad_filtros=count(explode('?', $filtro_buscar))-1;

      //Agregar catidad de variables a filtrar a data consulta
      array_push($data_consulta, $filtro_permanente);
      array_push($data_consulta, $filtro_permanente);
      for ($i=2; $i < $cantidad_filtros; $i++) { 
          array_push($data_consulta, "%$filtro_permanente%");//Se agrega llave por ser variable evaluada en un like
      }
  }

  if ($filtro_buscar_estado=="") {
      if($bandeja=="Pendientes"){
          $filtro_bandeja=" AND (`ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=? OR `ocrr_gestion_estado`=?)";
          array_push($data_consulta, 'Intento Contacto-Fallido');
          array_push($data_consulta, 'Intento Contacto-Agotado');
          array_push($data_consulta, 'Contactado-Pendiente Documentos');
          array_push($data_consulta, 'Intento Contacto-Fallido-Segunda Revisión');
          array_push($data_consulta, 'Intento Contacto-Agotado-Segunda Revisión');
          array_push($data_consulta, 'Contactado-Pendiente Documentos-Segunda Revisión');
      }
  } else {
      $filtro_bandeja="";
  }

  // Prepara string a ejecutar en sentencia preparada
  $consulta_contar_string="SELECT COUNT(`ocrr_id`) FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` LEFT JOIN `administrador_ciudades_dane` AS TCIU ON TOCR.`ocr_cod_municipio`=TCIU.`ciu_cod_municipio` WHERE 1=1 ".$filtro_perfil." ".$filtro_buscar_estado." ".$filtro_buscar." ".$filtro_bandeja."";

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

  $consulta_string="SELECT `ocrr_id`, `ocrr_cod_familia`, `ocrr_codbeneficiario`, `ocrr_cabezafamilia`, `ocrr_resultado_familia_estado`, `ocrr_gestion_agente`, `ocrr_gestion_estado`, `ocrr_gestion_intentos`, `ocrr_gestion_observaciones`, `ocrr_gestion_notificacion`, `ocrr_gestion_notificacion_estado`, `ocrr_gestion_notificacion_fecha_registro`, `ocrr_gestion_notificacion_fecha_envio`, `ocrr_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion`, TAG.`usu_nombres_apellidos`, `ocrr_gestion_fecha`, `ocrr_gestion_llamada_tipificacion`, `ocrr_gestion_llamada_id`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio`, TCIU.`ciu_municipio`, TCIU.`ciu_departamento` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` LEFT JOIN `administrador_usuario` AS TAG ON `gestion_ocr_resultado`.`ocrr_gestion_agente`=TAG.`usu_id` LEFT JOIN `administrador_ciudades_dane` AS TCIU ON TOCR.`ocr_cod_municipio`=TCIU.`ciu_cod_municipio` WHERE 1=1 ".$filtro_perfil." ".$filtro_buscar_estado." ".$filtro_buscar." ".$filtro_bandeja." ORDER BY `ocrr_gestion_estado` ASC, `ocrr_cod_familia` ASC LIMIT ?,?";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  if (count($data_consulta)>0) {
      // Agrega variables a sentencia preparada según cantidad de variables agregadas a array data_consulta en el orden específico de los parámetros de la sentencia preparada
      $consulta_registros->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
  }
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  $parametros_add='&bandeja='.base64_encode($bandeja).'&estado='.$estado;
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
                        <option value="Intento Contacto-Fallido" <?php if(in_array("Intento Contacto-Fallido", $filtro_estado_permanente)){ echo "selected"; } ?>>Intento Contacto-Fallido</option>
                        <option value="Intento Contacto-Fallido-Segunda Revisión" <?php if(in_array("Intento Contacto-Fallido-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Intento Contacto-Fallido-Segunda Revisión</option>
                        <option value="Intento Contacto-Agotado" <?php if(in_array("Intento Contacto-Agotado", $filtro_estado_permanente)){ echo "selected"; } ?>>Intento Contacto-Agotado</option>
                        <option value="Intento Contacto-Agotado-Segunda Revisión" <?php if(in_array("Intento Contacto-Agotado-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Intento Contacto-Agotado-Segunda Revisión</option>
                        <option value="Contactado-Pendiente Documentos" <?php if(in_array("Contactado-Pendiente Documentos", $filtro_estado_permanente)){ echo "selected"; } ?>>Contactado-Pendiente Documentos</option>
                        <option value="Contactado-Pendiente Documentos-Segunda Revisión" <?php if(in_array("Contactado-Pendiente Documentos-Segunda Revisión", $filtro_estado_permanente)){ echo "selected"; } ?>>Contactado-Pendiente Documentos-Segunda Revisión</option>
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
              <a href="familias_accion_consultas_enlace?pagina=1&id=<?php echo $filtro_permanente; ?>&bandeja=<?php echo base64_encode('Pendientes'); ?>&estado=null" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" title="Pendientes">
                <i class="fas fa-user-clock btn-icon-prepend me-0 me-lg-1 font-size-12"></i><span class="d-none d-lg-inline">Pendientes</span>
              </a>
            </div>
            <?php if($permisos_usuario=="Usuario" AND count($resultado_registros_filtro)==0): ?>
              <div class="col-md-12">
                <p class="alert alert-warning p-1">¡Usuario no se encuentra habilitado para realizar consultas, por favor contacte con el administrador!</p>
              </div>
            <?php else: ?>
              <div class="col-lg-12">
                <div class="table-responsive table-fixed" id="headerFixTable">
                  <table class="table table-hover table-bordered table-striped">
                    <thead>
                      <tr>
                        <th class="px-1 py-2" style="width: 60px;"></th>
                        <th class="px-1 py-2">Estado Gestión</th>
                        <th class="px-1 py-2">Intentos</th>
                        <th class="px-1 py-2">Cód. Familia</th>
                        <th class="px-1 py-2">Cód. Beneficiario</th>
                        <th class="px-1 py-2">Cabeza Familia</th>
                        <th class="px-1 py-2">Primer Nombre</th>
                        <th class="px-1 py-2">Segundo Nombre</th>
                        <th class="px-1 py-2">Primer Apellido</th>
                        <th class="px-1 py-2">Segundo Apellido</th>
                        <th class="px-1 py-2">Documento</th>
                        <th class="px-1 py-2">Fecha Nacimiento</th>
                        <th class="px-1 py-2">Género</th>
                        <th class="px-1 py-2">Fecha Expedición</th>
                        <th class="px-1 py-2">Departamento</th>
                        <th class="px-1 py-2">Municipio</th>
                        <th class="px-1 py-2">Observaciones</th>
                        <th class="px-1 py-2">Tipificación</th>
                        <th class="px-1 py-2">Fecha Gestión</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php for ($i=0; $i < count($resultado_registros); $i++): ?>
                      <tr>
                        <td class="p-1 text-center">
                            <a href="#" onClick="open_modal_detalle('<?php echo base64_encode($resultado_registros[$i][0]); ?>');" class="btn btn-dark btn-icon px-1 py-1 mb-1" title="Detalle"><i class="fas fa-file-alt font-size-11"></i></a>
                        </td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][6]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][7]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][1]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][2]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][3]; ?></td>
                        <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][14]; ?></td>
                        <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][15]; ?></td>
                        <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][16]; ?></td>
                        <td class="p-1 font-size-11"><?php echo $resultado_registros[$i][17]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][18]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][19]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][20]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][21]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][29]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][28]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][8]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][24]; ?></td>
                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][23]; ?></td>
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
            <?php endif; ?>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- modal reportes -->
        <?php require_once('familias_accion_reporte.php'); ?>
        <!-- modal -->
        <!-- MODAL DETALLE -->
        <div class="modal fade" id="modal-detalle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Detalle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body-detalle">
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger py-2 px-2" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- MODAL DETALLE -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
    function open_modal_detalle(id_registro) {
        var myModal = new bootstrap.Modal(document.getElementById("modal-detalle"), {});
        $('.modal-body-detalle').load('familias_accion_consultas_enlace_ver.php?reg='+id_registro,function(){
            myModal.show();
        });
    }
  </script>
</body>
</html>