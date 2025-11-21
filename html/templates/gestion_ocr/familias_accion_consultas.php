<?php
  //Validación de permisos del usuario para el módulo
  $modulo_plataforma="Gestión OCR-Consultas";
  require_once("../../iniciador.php");
  $url_fichero=pathinfo(__FILE__, PATHINFO_FILENAME);

  /*VARIABLES*/
  $title = "Gestión OCR";
  $bandeja=validar_input(base64_decode($_GET['bandeja']));
  $subtitle = "Familias en Acción-Consultas";
  
  // Inicializa variable tipo array
  $data_consulta=array();

  $consulta_string_filtros="SELECT `ocra_id`, `ocra_usuario`, `ocra_tipo`, `ocra_codigo` FROM `gestion_ocr_agentes` WHERE `ocra_usuario`=?";
  $consulta_registros_filtro = $enlace_db->prepare($consulta_string_filtros);
  $consulta_registros_filtro->bind_param("s", $_SESSION[APP_SESSION.'_session_usu_id']);
  $consulta_registros_filtro->execute();
  $resultado_registros_filtro = $consulta_registros_filtro->get_result()->fetch_all(MYSQLI_NUM);
  
  if(isset($_POST["guardar_registro"])){
      $documento=validar_input($_POST['documento']);
      array_push($data_consulta, $documento);

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

      $consulta_string_caso="SELECT `ocrr_id`, `ocrr_cod_familia`, TDOC.`gord_codfamilia`, TDOC.`gord_nombre`, TOCR.`ocr_documento`, TOCR.`ocr_cod_departamento`, TOCR.`ocr_cod_municipio` FROM `gestion_ocr_resultado` LEFT JOIN `gestion_ocr_resultado_documentos` AS TDOC ON `gestion_ocr_resultado`.`ocrr_cod_familia`=TDOC.`gord_codfamilia` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_resultado`.`ocrr_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `ocrr_cabezafamilia`='SI' AND (`ocrr_gestion_estado`='Contactado-Pendiente Documentos' OR `ocrr_gestion_estado`='Intento Contacto-Agotado' OR `ocrr_gestion_estado`='Intento Contacto-Fallido' OR `ocrr_gestion_estado`='Contactado-Pendiente Documentos-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Agotado-Segunda Revisión' OR `ocrr_gestion_estado`='Intento Contacto-Fallido-Segunda Revisión') AND TOCR.`ocr_documento`=? ".$filtro_perfil."";
      $consulta_registros_caso = $enlace_db->prepare($consulta_string_caso);
      if (count($data_consulta)>0) {
        $consulta_registros_caso->bind_param(str_repeat("s", count($data_consulta)), ...$data_consulta);
      }
      $consulta_registros_caso->execute();
      $resultado_registros_caso = $consulta_registros_caso->get_result()->fetch_all(MYSQLI_NUM);

      if (count($resultado_registros_caso)>0) {
          $respuesta_accion = "<div class='alert alert-success py-1 font-size-11 col-md-12'>¡Número de identificación validado exitosamente!</div>";
          $consulta_string="SELECT `ocrc_id`, `ocrc_cod_familia`, `ocrc_codbeneficiario`, `ocrc_cabezafamilia`, `ocrc_miembro_id`, `ocrc_existe`, `ocrc_doc_valida`, `ocrc_doc_valor`, `ocrc_doc_tipo`, `ocrc_nombre_valida`, `ocrc_nombre_valor`, `ocrc_apellido_valida`, `ocrc_apellido_valor`, `ocrc_fnacimiento_valida`, `ocrc_fnacimiento_valor`, `ocrc_fexpedicion_valida`, `ocrc_fexpedicion_valor`, `ocrc_contrato_existe`, `ocrc_contrato_numid`, `ocrc_contrato_titular`, `ocrc_contrato_municipio`, `ocrc_contrato_departamento`, `ocrc_contrato_firmado`, `ocrc_contrato_huella`, `ocrc_registro_path`, `ocrc_resultado_estado`, `ocrc_resultado_novedad`, `ocrc_registro_fecha`, TOCR.`ocr_primernombre`, TOCR.`ocr_segundonombre`, TOCR.`ocr_primerapellido`, TOCR.`ocr_segundoapellido`, TOCR.`ocr_documento`, TOCR.`ocr_fechanacimiento`, TOCR.`ocr_genero`, TOCR.`ocr_fechaexpedicion` FROM `gestion_ocr_consolidado` LEFT JOIN `gestion_ocr` AS TOCR ON `gestion_ocr_consolidado`.`ocrc_codbeneficiario`=TOCR.`ocr_codbeneficiario` WHERE `ocrc_cod_familia`=? ORDER BY `ocrc_codbeneficiario` ASC";

          $consulta_registros = $enlace_db->prepare($consulta_string);
          $consulta_registros->bind_param("s", $resultado_registros_caso[0][1]);
          $consulta_registros->execute();
          $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

          $consulta_string_historial="SELECT `gora_id`, `gora_codfamilia`, `gora_estado`, `gora_observaciones`, `gora_registro_usuario`, `gora_registro_fecha`, TUR.`usu_nombres_apellidos` FROM `gestion_ocr_resultado_avances` LEFT JOIN `administrador_usuario` AS TUR ON `gestion_ocr_resultado_avances`.`gora_registro_usuario`=TUR.`usu_id` WHERE `gora_codfamilia`=? ORDER BY `gora_registro_fecha` DESC";
          $consulta_registros_historial = $enlace_db->prepare($consulta_string_historial);
          $consulta_registros_historial->bind_param("s", $resultado_registros_caso[0][1]);
          $consulta_registros_historial->execute();
          $resultado_registros_historial = $consulta_registros_historial->get_result()->fetch_all(MYSQLI_NUM);
      } else {
          $respuesta_accion = "<div class='alert alert-warning py-1 font-size-11 col-md-12'>¡Número de identificación no encontrado en nuestra base de datos de Familias pre inscritas, por favor verifique e intente nuevamente!</div>";
      }
  }

  if (!isset($resultado_registros_caso)) {
    $resultado_registros_caso=array();
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
            <div class="col-md-3 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                          <div class="col-md-12 py-2 text-center fw-bold">
                              Por favor ingrese el documento de identidad de la cabeza de familia para continuar.
                          </div>
                          <div class="col-md-12">
                              <div class="col-md-12 pt-3">
                                  <div class="form-group">
                                    <label for="documento" class="my-0">Documento identidad cabeza de familia</label>
                                    <input type="text" class="form-control form-control-sm" name="documento" id="documento" maxlength="50" value="<?php if(isset($_POST["guardar_registro"])) { echo $documento; } ?>" <?php if(isset($_POST["guardar_registro"])) { echo 'readonly'; } ?> required>
                                  </div>
                                  <?php if (!empty($respuesta_accion)) {echo $respuesta_accion;} ?>
                              </div>
                              <div class="col-md-12">
                                  <div class="form-group">
                                      <?php if(isset($_POST["guardar_registro"])): ?>
                                          <a href="<?php echo $url_fichero; ?>" class="btn btn-dark float-end">Realizar otra búsqueda</a>
                                      <?php else: ?>
                                          <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro">Validar documento</button>
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
            <div class="col-md-9 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <div class="row">
                        <?php if($permisos_usuario=="Usuario" AND count($resultado_registros_filtro)==0): ?>
                          <div class="col-md-12">
                            <p class="alert alert-warning p-1">¡Usuario no se encuentra habilitado para realizar consultas, por favor contacte con el administrador!</p>
                          </div>
                        <?php else: ?>
                          <?php if(count($resultado_registros_caso)>0): ?>
                            <div class="col-md-12">
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
                                    <?php
                                        for ($i=0; $i < count($resultado_registros); $i++) { 
                                    ?>
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
                                            <?php echo ($resultado_registros[$i][17]) ? "<span class='fas fa-check-circle color-verde'></span>" : "<span class='fas fa-times-circle color-rojo'></span>"; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                            <?php echo ($resultado_registros[$i][19]) ? "<span class='fas fa-check-circle color-verde'></span>" : "<span class='fas fa-times-circle color-rojo'></span>"; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                            <?php echo ($resultado_registros[$i][22]) ? "<span class='fas fa-check-circle color-verde'></span>" : "<span class='fas fa-times-circle color-rojo'></span>"; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                            <?php echo ($resultado_registros[$i][23]) ? "<span class='fas fa-check-circle color-verde'></span>" : "<span class='fas fa-times-circle color-rojo'></span>"; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                            <?php echo ($resultado_registros[$i][6]) ? "<span class='fas fa-check-circle color-verde'></span>" : "<span class='fas fa-times-circle color-rojo'></span>"; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                            <?php echo ($resultado_registros[$i][9]) ? "<span class='fas fa-check-circle color-verde'></span>" : "<span class='fas fa-times-circle color-rojo'></span>"; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                            <?php echo ($resultado_registros[$i][11]) ? "<span class='fas fa-check-circle color-verde'></span>" : "<span class='fas fa-times-circle color-rojo'></span>"; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                            <?php echo ($resultado_registros[$i][13]) ? "<span class='fas fa-check-circle color-verde'></span>" : "<span class='fas fa-times-circle color-rojo'></span>"; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center">
                                            <?php echo ($resultado_registros[$i][15]) ? "<span class='fas fa-check-circle color-verde'></span>" : "<span class='fas fa-times-circle color-rojo'></span>"; ?>
                                        </td>
                                        <td class="p-1 font-size-11 text-center"><?php echo $resultado_registros[$i][27]; ?></td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                  </tbody>
                                </table>
                                <?php if(count($resultado_registros)==0): ?>
                                  <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
                                <?php endif; ?>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <p class="alert background-principal color-blanco py-1 px-2 my-1"><span class="fas fa-history"></span> Historial de Gestión</p>
                                <?php if (count($resultado_registros_historial)>0): ?>
                                    <table class="table table-bordered table-striped table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th class="p-1 font-size-11">Estado</th>
                                                <th class="p-1 font-size-11">Observaciones</th>
                                                <th class="p-1 font-size-11">Usuario Registro</th>
                                                <th class="p-1 font-size-11">Fecha Registro</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                for ($i=0; $i < count($resultado_registros_historial); $i++) { 
                                            ?>
                                            <tr>
                                                <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][2]; ?></td>
                                                <td class="p-1 font-size-11"><?php echo nl2br($resultado_registros_historial[$i][3]); ?></td>
                                                <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][6]; ?></td>
                                                <td class="p-1 font-size-11"><?php echo $resultado_registros_historial[$i][5]; ?></td>
                                            </tr>
                                            <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p class="alert alert-warning p-1 font-size-11">
                                        <span class="fas fa-exclamation-triangle"></span> No se encontraron registros
                                    </p>
                                <?php endif; ?>
                            </div>
                          <?php else: ?>
                            <div class="col-md-12">
                              <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
                            </div>
                          <?php endif; ?>
                        <?php endif; ?>
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
        <!-- modal -->
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>