<?php

  session_start();
//  echo "<pre>";
  //print_r($_SESSION);
  //echo "</pre>";

  //error_reporting(E_ALL);
  //ini_set('display_errors', 1);
  //ini_set('display_startup_errors', 1); 
  // Validación de permisos del usuario para el módulo
  $modulo_plataforma = "Calidad-Monitoreos";
  require_once("../../iniciador.php");
  $url_fichero = pathinfo(__FILE__, PATHINFO_FILENAME);
  $permisos_usuario = $_SESSION[APP_SESSION . '_session_modulos'][$modulo_plataforma] ?? null;
  /* VARIABLES */
  $title             = "Calidad";
  $subtitle          = "Monitoreos | Crear Registro | Evaluación";
  $pagina            = validar_input($_GET['pagina']);
  $filtro_permanente = validar_input($_GET['id']);
  $url_salir         = "monitoreos?pagina=" . $pagina . "&id=" . $filtro_permanente . "&bandeja=" . base64_encode('Mes Actual');
  // Si viene del paso anterior, guardamos en $_SESSION la información básica
  if (isset($_POST["guardar_informacion"])) {
      $_SESSION[APP_SESSION . '_mon_informacion']['matriz']                 = validar_input($_POST['id_matriz']);
      $_SESSION[APP_SESSION . '_mon_informacion']['canal']                  = validar_input($_POST['canal']);
      $_SESSION[APP_SESSION . '_mon_informacion']['dependencia']            = validar_input($_POST['dependencia']);
      $_SESSION[APP_SESSION . '_mon_informacion']['numero_interaccion']     = validar_input($_POST['numero_interaccion']);
      $_SESSION[APP_SESSION . '_mon_informacion']['identificacion_ciudadano'] = validar_input($_POST['identificacion_ciudadano']);
      $_SESSION[APP_SESSION . '_mon_informacion']['fecha_interaccion']      = validar_input($_POST['fecha_interaccion']);
      $_SESSION[APP_SESSION . '_mon_informacion']['analista']               = validar_input($_POST['analista']);
      $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']         = validar_input($_POST['tipo_monitoreo']);
  }

  // Consultar los ítems de la matriz para evaluación
  $consulta_string = "
    SELECT 
      `gcmi_id`, 
      `gcmi_matriz`, 
      `gcmi_item_tipo`, 
      `gcmi_item_consecutivo`, 
      `gcmi_item_orden`, 
      `gcmi_descripcion`, 
      `gcmi_peso`, 
      `gcmi_calificable`, 
      `gcmi_grupo_peso`, 
      `gcmi_visible`, 
      `gcmi_tipo_error`, 
      `gcmi_grupo_id`, 
      `gcmi_subgrupo_id`, 
      `gcmi_item_id`, 
      `gcmi_subitem_id` 
    FROM `gestion_calidad_matriz_item` 
    WHERE `gcmi_matriz` = ? 
    ORDER BY `gcmi_item_consecutivo` ASC
  ";
  $consulta_registros = $enlace_db->prepare($consulta_string);
  $consulta_registros->bind_param("s", $_SESSION[APP_SESSION . '_mon_informacion']['matriz']);
  $consulta_registros->execute();
  $resultado_registros = $consulta_registros->get_result()->fetch_all(MYSQLI_NUM);

  // Consultar datos de la matriz (de nuevo, para mostrar en la tabla)
  $consulta_string_matriz = "
    SELECT 
      `gcm_id`, 
      `gcm_nombre_matriz`, 
      `gcm_estado`, 
      `gcm_canal`, 
      `gcm_observaciones`, 
      `gcm_registro_usuario`, 
      `gcm_registro_fecha` 
    FROM `gestion_calidad_matriz` 
    WHERE `gcm_id` = ?
  ";
  $consulta_registros_matriz = $enlace_db->prepare($consulta_string_matriz);
  $consulta_registros_matriz->bind_param("s", $_SESSION[APP_SESSION . '_mon_informacion']['matriz']);
  $consulta_registros_matriz->execute();
  $resultado_registros_matriz = $consulta_registros_matriz->get_result()->fetch_all(MYSQLI_NUM);

  // Consultar datos del analista (agente)
  $consulta_string_analista = "
    SELECT 
      `usu_id`, 
      `usu_nombres_apellidos` 
    FROM `administrador_usuario` 
    WHERE `usu_id` = ?
  ";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  $consulta_registros_analistas->bind_param("s", $_SESSION[APP_SESSION . '_mon_informacion']['analista']);
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  // Consultar programas disponibles para TIPI
  $consulta_string_programas = "
    SELECT DISTINCT `gcmt_programa` 
    FROM `gestion_calidad_monitoreo_tipificacion` 
    ORDER BY `gcmt_programa` ASC
  ";
  $consulta_registros_programas = $enlace_db->prepare($consulta_string_programas);
  $consulta_registros_programas->execute();
  $resultado_registros_programas = $consulta_registros_programas->get_result()->fetch_all(MYSQLI_NUM);

  // Consultar segmentos para VOC
  $consulta_string_segmentos = "
    SELECT DISTINCT `gcmtv_segmento` 
    FROM `gestion_calidad_monitoreo_tipificacion_voc` 
    ORDER BY `gcmtv_segmento` ASC
  ";
  $consulta_registros_segmentos = $enlace_db->prepare($consulta_string_segmentos);
  $consulta_registros_segmentos->execute();
  $resultado_registros_segmentos = $consulta_registros_segmentos->get_result()->fetch_all(MYSQLI_NUM);

  // Listas de valores fijos para emociones y “qué le activó”
  $array_emociones = [
    "Decepcionado",
    "Agradecido",
    "Alegre",
    "Sorprendido",
    "Resignado",
    "Preocupado",
    "Confundido",
    "Impaciente",
    "Interesado",
    "Molesto",
    "Tranquilo",
    "Triste",
    "Optimista",
    "N/A"
  ];
  $array_que_activo = [
    "La respuesta brindada no fue satisfactoria a su necesidad",
    "La atención brindada no cumple con un buen servicio",
    "No solucionaron en esta línea sino que fue remitido a otra entidad",
    "Desconoce completamente la información",
    "No está de acuerdo con los parámetros o cambios generados por el programa o la entidad",
    "La información brindada resolvió su necesidad",
    "El servicio brindado cumplió sus expectativas",
    "Se cortó la llamada o abandona transacción",
    "No se brinda información por ser un tercero"
  ];

  $array_atribuible = [
    "Agente", "Alcaldía", "AV VILLAS", "BANCA CAJA SOCIAL",
    "BANCAMIA S.A. BANCO DE LAS MICROFINANZAS", "BANCO AGRARIO",
    "BANCO CORPBANCA – ITAÚ", "BANCO DE BOGOTA", "BANCO DE OCCIDENTE",
    "BANCO FALABELLA S.A.", "BANCO FINANDINA S.A.", "BANCO GNB SUDAMERIS",
    "BANCO PICHINCHA S.A.", "BANCO POPULAR", 
    "BANCO SERFINANSA- SERVICIOS FINANCIEROS S.A.", "BANCO WWB S.A.",
    "BANCOLOMBIA", "BANCOLOMBIA – ALM", "BANCOLOMBIA – NEQUI",
    "BANCOOMEVA S.A.", "BANCOOPCENTRAL", "BBVA COLOMBIA", "DALE",
    "DAVIPLATA", "DAVIVIENDA S.A - DAVIPLATA", "DAVIVIENDA S.A - EFECTY",
    "DAVIVIENDA S.A.", "MOVII S.A.", "Otra Entidad", "Prosperidad social",
    "SCOTIABANK", "SISBEN", "SUPERGIROS", "TPAGA", "UARIV",
    "Usuario Final", "UT IQ - ASD"
  ];

  // Consultar “direcciones misionales” (categoría nivel 1)
  $consulta_string_direcmisionales = "
    SELECT 
      `gic1_id`, 
      `gic1_item`, 
      `gic1_estado`, 
      `gic1_registro_usuario`, 
      `gic1_registro_fecha` 
    FROM `gestion_interacciones_catnivel1` 
    WHERE `gic1_estado` = 'Activo' 
    ORDER BY `gic1_item` ASC
  ";
  $consulta_registros_direcmisionales = $enlace_db->prepare($consulta_string_direcmisionales);
  $consulta_registros_direcmisionales->execute();
  $resultado_registros_direcmisionales = $consulta_registros_direcmisionales->get_result()->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT . 'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <!-- navbar -->
    <?php require_once(ROOT . 'includes/_navbar.php'); ?>
    <!-- navbar -->
    <div class="container-fluid page-body-wrapper">
      <!-- sidebar -->
      <?php require_once(ROOT . 'includes/_sidebar.php'); ?>
      <!-- sidebar -->
      <!-- main-panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <form 
            name="guardar_registro" 
            action="monitoreos_crear_guardar.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>" 
            method="POST" 
            enctype="multipart/form-data"
          >
            <div class="row justify-content-center">
              <div class="col-lg-4 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="table-responsive mb-3">
                          <table class="table table-bordered table-striped table-hover table-sm">
                            <tbody>
                              <tr>
                                <th class="p-1 font-size-11" style="width: 170px;">Matriz</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($resultado_registros_matriz[0][1]) 
                                          ? htmlspecialchars($resultado_registros_matriz[0][1], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Canal</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($resultado_registros_matriz[0][3]) 
                                          ? htmlspecialchars($resultado_registros_matriz[0][3], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <?php if (isset($resultado_registros_matriz[0][3]) && $resultado_registros_matriz[0][3] === "Escrito"): ?>
                                <tr>
                                  <th class="p-1 font-size-11">Dependencia</th>
                                  <td class="p-1 font-size-11">
                                    <?php 
                                      echo isset($_SESSION[APP_SESSION . '_mon_informacion']['dependencia']) 
                                            ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['dependencia'], ENT_QUOTES, 'UTF-8') 
                                            : '';
                                    ?>
                                  </td>
                                </tr>
                              <?php endif; ?>
                              <tr>
                                <th class="p-1 font-size-11">Número Interacción</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($_SESSION[APP_SESSION . '_mon_informacion']['numero_interaccion']) 
                                          ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['numero_interaccion'], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Identificación Ciudadano</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($_SESSION[APP_SESSION . '_mon_informacion']['identificacion_ciudadano']) 
                                          ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['identificacion_ciudadano'], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Fecha Interacción</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($_SESSION[APP_SESSION . '_mon_informacion']['fecha_interaccion']) 
                                          ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['fecha_interaccion'], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Doc. Agente</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($resultado_registros_analistas[0][0]) 
                                          ? htmlspecialchars($resultado_registros_analistas[0][0], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Agente</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($resultado_registros_analistas[0][1]) 
                                          ? htmlspecialchars($resultado_registros_analistas[0][1], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="p-1 font-size-11">Tipo Monitoreo</th>
                                <td class="p-1 font-size-11">
                                  <?php 
                                    echo isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                          ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'], ENT_QUOTES, 'UTF-8') 
                                          : '';
                                  ?>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>

                        <div class="row">
                          <!-- SOLUCIÓN PRIMER CONTACTO -->
                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="solucion_primer_contacto" class="my-0">Solucionado primer contacto?</label>
                              <select 
                                class="form-control form-control-sm form-select" 
                                name="solucion_primer_contacto" 
                                id="solucion_primer_contacto" 
                                required 
                                onchange="validar_solucion_contacto();"
                              >
                                <option value="">Seleccione</option>
                                <option 
                                  value="Si" 
                                  <?php 
                                    echo (
                                      isset($_SESSION[APP_SESSION . '_mon_informacion']['solucion_primer_contacto']) 
                                      && $_SESSION[APP_SESSION . '_mon_informacion']['solucion_primer_contacto'] === "Si"
                                    ) ? "selected" : "";
                                  ?>
                                >Si</option>
                                <option 
                                  value="No" 
                                  <?php 
                                    echo (
                                      isset($_SESSION[APP_SESSION . '_mon_informacion']['solucion_primer_contacto']) 
                                      && $_SESSION[APP_SESSION . '_mon_informacion']['solucion_primer_contacto'] === "No"
                                    ) ? "selected" : "";
                                  ?>
                                >No</option>
                                <option 
                                  value="No aplica" 
                                  <?php 
                                    echo (
                                      isset($_SESSION[APP_SESSION . '_mon_informacion']['solucion_primer_contacto']) 
                                      && $_SESSION[APP_SESSION . '_mon_informacion']['solucion_primer_contacto'] === "No aplica"
                                    ) ? "selected" : "";
                                  ?>
                                >No aplica</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-12 mb-3 d-none" id="causal_no_solucion_div">
                            <div class="form-group">
                              <label for="causal_no_solucion" class="my-0">Causal NO solución</label>
                              <select 
                                class="form-control form-control-sm form-select" 
                                name="causal_no_solucion" 
                                id="causal_no_solucion" 
                                required 
                                disabled
                              >
                                <option value="">Seleccione</option>
                                <?php 
                                  $causales = [
                                    "AGENTE FINALIZA INTERACCIÓN",
                                    "CIUDADANO FINALIZA INTERACCIÓN",
                                    "NO SUPERA FILTRO",
                                    "NO ES EL TITULAR",
                                    "SE EVACUA INTERACCIÓN",
                                    "FALLAS TÉCNICAS",
                                    "INFORMACIÓN INCORRECTA",
                                    "INFORMACIÓN INCOMPLETA"
                                  ];
                                  foreach ($causales as $causa) {
                                    $selected = (
                                      isset($_SESSION[APP_SESSION . '_mon_informacion']['causal_no_solucion']) 
                                      && $_SESSION[APP_SESSION . '_mon_informacion']['causal_no_solucion'] === $causa
                                    ) ? "selected" : "";
                                ?>
                                  <option value="<?php echo $causa; ?>" <?php echo $selected; ?>>
                                    <?php echo $causa; ?>
                                  </option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>

                          <!-- TIPI: Programa, Tipificación, Subtipificación -->
                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="tipi_programa" class="my-0">Programa</label>
                              <select 
                                class="form-control form-control-sm form-select" 
                                name="tipi_programa" 
                                id="tipi_programa" 
                                required 
                                onchange="validar_programa();"
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($resultado_registros_programas as $fila_prog): 
                                  $prog = $fila_prog[0];
                                ?>
                                  <option value="<?php echo htmlspecialchars($prog, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($prog, ENT_QUOTES, 'UTF-8'); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="tipi_tipificacion" class="my-0">Tipificación</label>
                              <select 
                                class="form-control form-control-sm form-select" 
                                name="tipi_tipificacion" 
                                id="tipi_tipificacion" 
                                required 
                                onchange="validar_tipificacion();"
                              >
                                <option value="">Seleccione</option>
                                <!-- Se llenará dinámicamente con AJAX -->
                              </select>
                            </div>
                          </div>
                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="subtipificacion" class="my-0">Sub-Tipificación</label>
                              <select 
                                class="form-control form-control-sm form-select" 
                                name="subtipificacion" 
                                id="subtipificacion" 
                                required
                              >
                                <option value="">Seleccione</option>
                                <!-- Se llenará dinámicamente con AJAX -->
                              </select>
                            </div>
                          </div>

                          <!-- Botón para abrir modal con detalle de Tipificación de interacción -->
                          <div class="col-md-12">
                            <a 
                              href="#" 
                              onClick="open_modal_detalle('<?php 
                                echo base64_encode($_SESSION[APP_SESSION . '_mon_informacion']['numero_interaccion']); 
                              ?>');" 
                              class="btn btn-success mb-1 font-size-11" 
                              title="Ver Tipificación Interacción"
                            >
                              <span class="fas fa-file-alt"></span> Tipificación Interacción
                            </a>
                          </div>
                        </div><!-- /.row -->
                      </div><!-- /.card-body -->
                    </div><!-- /.card -->
                  </div><!-- /.col-12 -->
                </div><!-- /.row flex-grow -->
              </div><!-- /.col-lg-4 -->

              <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <?php if (count($resultado_registros) > 0): ?>
                          <div id="table-fixed" class="table-responsive table-fixed mb-3">
                            <table class="table table-bordered table-sm">
                              <thead>
                                <tr>
                                  <th style="width: 50px;"></th>
                                  <th>Atributos de Evaluación</th>
                                  <th style="width: 100px;">Peso</th>
                                  <th style="width: 50px;">Si</th>
                                  <th style="width: 50px;">No</th>
                                  <th style="width: 300px;">Comentarios</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
                                  // Recorremos cada fila de ítem de la matriz
                                  for ($i = 0; $i < count($resultado_registros); $i++) {
                                    $fila = $resultado_registros[$i];
                                    // Posiciones en $fila:
                                    // 0: gcmi_id
                                    // 2: gcmi_item_tipo (Grupo / Sub-Grupo / Item)
                                    // 5: gcmi_descripcion
                                    // 6: gcmi_peso
                                    // 7: gcmi_calificable ("Si"/"No")
                                    // 8: gcmi_grupo_peso
                                    // 10: gcmi_tipo_error
                                    // 9: gcmi_visible ("Si"/"No")
                                    if ($fila[9] === "Si") {
                                      // Determine la clase CSS según tipo de elemento
                                      $claseFila = '';
                                      if ($fila[2] === 'Grupo') {
                                        $claseFila = 'matriz-grupo';
                                      } elseif ($fila[2] === 'Sub-Grupo') {
                                        $claseFila = 'matriz-grupo-sub';
                                      } elseif ($fila[2] === 'Item') {
                                        $claseFila = 'matriz-item';
                                      }
                                ?>
                                  <tr class="<?php echo $claseFila; ?>">
                                    <td class="p-1 font-size-11">
                                      <?php if ($fila[7] === "Si"): 
                                        // Solo generamos inputs ocultos si es “calificable”
                                        $idCampo = $fila[0];
                                        $grupoPeso = $fila[8];
                                        $pesoNota = $fila[6];
                                        $tipoError = $fila[10];
                                      ?>
                                        <input type="hidden" name="id_campos[]" value="<?php echo htmlspecialchars($idCampo, ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="grupo_peso[]" value="<?php echo htmlspecialchars($grupoPeso, ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="peso_nota[]" value="<?php echo htmlspecialchars($pesoNota, ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="tipo_error[]" value="<?php echo htmlspecialchars($tipoError, ENT_QUOTES, 'UTF-8'); ?>">
                                      <?php endif; ?>
                                      <?php echo htmlspecialchars($fila[3], ENT_QUOTES, 'UTF-8'); // gcmi_item_consecutivo ?>
                                    </td>
                                    <td class="p-1 font-size-11"><?php echo htmlspecialchars($fila[5], ENT_QUOTES, 'UTF-8'); // gcmi_descripcion ?></td>
                                    <td class="p-1 font-size-11 text-center"><?php echo htmlspecialchars($fila[6], ENT_QUOTES, 'UTF-8'); ?>%</td>
                                    <td class="p-1 font-size-11 text-center">
                                      <?php if ($fila[7] === "Si"): ?>
                                        <div class="form-group m-0 p-0">
                                          <div class="form-group custom-control custom-checkbox m-0">
                                            <input 
                                              type="radio" 
                                              class="custom-control-input" 
                                              id="customCheckreqsi<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>" 
                                              name="respuesta_<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>" 
                                              value="Si" 
                                              checked 
                                              onclick="validar_comentario('Si', '<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>');" 
                                              required
                                            >
                                            <label 
                                              class="custom-control-label p-0 m-0" 
                                              for="customCheckreqsi<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>"
                                            ></label>
                                          </div>
                                        </div>
                                      <?php endif; ?>
                                    </td>
                                    <td class="p-1 font-size-11 text-center">
                                      <?php if ($fila[7] === "Si"): ?>
                                        <div class="form-group m-0 p-0">
                                          <div class="form-group custom-control custom-checkbox m-0">
                                            <input 
                                              type="radio" 
                                              class="custom-control-input" 
                                              id="customCheckreqno<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>" 
                                              name="respuesta_<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>" 
                                              value="No" 
                                              onclick="validar_comentario('No', '<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>');" 
                                              required
                                            >
                                            <label 
                                              class="custom-control-label p-0 m-0" 
                                              for="customCheckreqno<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>"
                                            ></label>
                                          </div>
                                        </div>
                                      <?php endif; ?>
                                    </td>
                                    <td class="p-1 font-size-11 text-center">
                                      <?php if ($fila[7] === "Si"): ?>
                                        <input 
                                          type="text" 
                                          class="form-control form-control-sm d-none color-rojo" 
                                          name="comentario_<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>" 
                                          id="comentario_<?php echo htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8'); ?>" 
                                          maxlength="2000" 
                                          required 
                                          disabled 
                                          value=""
                                        >
                                      <?php endif; ?>
                                    </td>
                                  </tr>
                                <?php 
                                    } // end if visible
                                  } // end for
                                ?>
                              </tbody>
                            </table>
                          </div>
                        <?php else: ?>
                          <p class="alert alert-warning">
                            <span class="fas fa-exclamation-triangle p-1"></span> 
                            No se encontraron registros
                          </p>
                        <?php endif; ?>

                          <div class="col-md-7 mb-3">
                            <div class="form-group">
                              <label for="atencion_wow" class="my-0">Termómetro</label>
                              <select class="form-control form-control-sm form-select" name="atencion_wow" id="atencion_wow" required>
                                <option value="" <?php if(empty($_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow'])) echo 'selected'; ?>>Seleccione...</option>
                                <option value="NA" <?php if(isset($_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']) && $_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']=='NA') echo 'selected'; ?>>NA</option>
                                <option value="1"  <?php if(isset($_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']) && $_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']=='1')  echo 'selected'; ?>>1</option>
                                <option value="2"  <?php if(isset($_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']) && $_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']=='2')  echo 'selected'; ?>>2</option>
                                <option value="3"  <?php if(isset($_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']) && $_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']=='3')  echo 'selected'; ?>>3</option>
                                <option value="WOW"<?php if(isset($_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']) && $_SESSION[APP_SESSION.'_mon_informacion']['atencion_wow']=='WOW')echo 'selected'; ?>>WOW</option>
                              </select>
                            </div>
                          </div>


                          <div class="col-md-7 mb-3">
                            <div class="form-group">
                              <div class="form-group custom-control custom-checkbox m-0">
                                <input 
                                  type="checkbox" 
                                  class="custom-control-input" 
                                  id="customCheckvoc" 
                                  name="aplica_voc" 
                                  value="Si" 
                                  onclick="validar_voc();"
                                >
                                <label 
                                  class="custom-control-label p-0 m-0" 
                                  for="customCheckvoc"
                                >Se presenta VOC <span class="font-size-11">(Voz Orientada al Ciudadano)</span></label>
                              </div>
                            </div>
                          </div>

                          <!-- DIVS que se muestran/ocultan según VOC -->
                          <div class="col-md-12 mb-2 d-none" id="voc_div_1">
                            <p class="alert background-principal color-blanco py-1 px-2">
                              Tipificación VOC <span class="font-size-11">(Voz Orientada al Ciudadano)</span>
                            </p>
                          </div>

                          <div class="col-md-6 mb-3 d-none" id="voc_div_2">
                            <div class="form-group">
                              <label for="segmento" class="my-0">Segmento</label>
                              <select 
                                class="form-control form-control-sm form-select" 
                                name="segmento" 
                                id="segmento" 
                                required 
                                disabled 
                                onchange="validar_segmento();"
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($resultado_registros_segmentos as $fila_seg): 
                                  $seg = $fila_seg[0];
                                ?>
                                  <option value="<?php echo htmlspecialchars($seg, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($seg, ENT_QUOTES, 'UTF-8'); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 mb-3 d-none" id="voc_div_3">
                            <div class="form-group">
                              <label for="tabulacion_voc" class="my-0">Tabulación VOC <span class="font-size-11">(Voz Orientada al Ciudadano)</span></label>
                              <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                name="tabulacion_voc" 
                                id="tabulacion_voc" 
                                required 
                                disabled
                              >
                            </div>
                          </div>

                          <div class="col-md-12 mb-3 d-none" id="voc_div_4">
                            <div class="form-group">
                              <label for="voc" class="my-0">VOC <span class="font-size-11">(Voz Orientada al Ciudadano)</span></label>
                              <textarea 
                                class="form-control form-control-sm" 
                                name="voc" 
                                id="voc" 
                                maxlength="300"
                              ><?php 
                                  echo isset($_SESSION[APP_SESSION . '_mon_informacion']['voc']) 
                                        ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['voc'], ENT_QUOTES, 'UTF-8') 
                                        : '';
                                ?></textarea>
                            </div>
                          </div>

                          <div class="col-md-6 mb-3 d-none" id="voc_div_5">
                            <div class="form-group">
                              <label for="emocion_inicial" class="my-0">VOC <span class="font-size-11">(Voz Orientada al Ciudadano)</span> Emoción inicial</label>
                              <select 
                                class="form-control form-control-sm form-select" 
                                name="emocion_inicial" 
                                id="emocion_inicial" 
                                required 
                                disabled
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($array_emociones as $emocion): ?>
                                  <option value="<?php echo htmlspecialchars($emocion, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($emocion, ENT_QUOTES, 'UTF-8'); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 mb-3 d-none" id="voc_div_6">
                            <div class="form-group">
                              <label for="emocion_final" class="my-0">VOC <span class="font-size-11">(Voz Orientada al Ciudadano)</span> Emoción final</label>
                              <select 
                                class="form-control form-control-sm form-select" 
                                name="emocion_final" 
                                id="emocion_final" 
                                required 
                                disabled
                              >
                                <option value="">Seleccione</option>
                                <?php foreach ($array_emociones as $emocion): ?>
                                  <option value="<?php echo htmlspecialchars($emocion, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($emocion, ENT_QUOTES, 'UTF-8'); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 mb-3 d-none" id="voc_div_7">
                            <div class="form-group">
                              <label for="que_le_activo" class="my-0">Qué le activó</label>
                              <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                name="que_le_activo" 
                                id="que_le_activo" 
                                value="" 
                                required 
                                disabled
                              >
                            </div>
                          </div>

                          <div class="col-md-6 mb-3 d-none" id="voc_div_8">
                            <div class="form-group">
                              <label for="atribuible" class="my-0">Atribuible</label>
                              <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                name="atribuible" 
                                id="atribuible" 
                                value="" 
                                required 
                                disabled
                              >
                            </div>
                          </div>

                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="competencia" class="my-0">Competencia <span class="font-size-11">(Máx. 200 caracteres)</span></label>
                              <input type="text" class="form-control form-control-sm font-size-11" name="competencia" id="competencia" maxlength="200"value="<?php echo isset($_SESSION[APP_SESSION . '_mon_informacion']['competencia']) ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['competencia'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>
                          </div>


                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="observaciones" class="my-0">Observaciones generales</label>
                              <textarea 
                                class="form-control form-control-sm font-size-11 height-100" 
                                name="observaciones" 
                                id="observaciones"
                                required
                              ><?php 
                                  echo isset($_SESSION[APP_SESSION . '_mon_informacion']['observaciones']) 
                                        ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['observaciones'], ENT_QUOTES, 'UTF-8') 
                                        : '';
                                ?></textarea>
                            </div>
                          </div>
                        </div><!-- /.row -->

                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <button 
                                class="btn btn-success float-end ms-1" 
                                type="submit" 
                                name="guardar_monitoreo"
                              >Guardar</button>
                              <a 
                                href="monitoreos_crear_informacion?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>" 
                                class="btn btn-warning float-end ms-1"
                              >Regresar</a>
                              <button 
                                class="btn btn-danger float-end" 
                                type="button" 
                                onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');"
                              >Cancelar</button>
                            </div>
                          </div>
                        </div>
                      </div><!-- /.card-body -->
                    </div><!-- /.card -->
                  </div><!-- /.col-12 -->
                </div><!-- /.row flex-grow -->
              </div><!-- /.col-lg-8 -->
            </div><!-- /.row justify-content-center -->
          </form>
        </div><!-- /.content-wrapper -->

        <!-- MODAL DETALLE -->
        <div 
          class="modal fade" 
          id="modal-detalle" 
          data-bs-backdrop="static" 
          data-bs-keyboard="false" 
          tabindex="-1" 
          aria-labelledby="staticBackdropLabel" 
          aria-hidden="true"
        >
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Tipificación Interacción</h5>
                <button 
                  type="button" 
                  class="btn-close" 
                  data-bs-dismiss="modal" 
                  aria-label="Close"
                ></button>
              </div>
              <div class="modal-body-detalle">
                <!-- Se cargará vía AJAX -->
              </div>
              <div class="modal-footer">
                <button 
                  type="button" 
                  class="btn btn-danger py-2 px-2" 
                  data-bs-dismiss="modal"
                >Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- MODAL DETALLE -->
      </div><!-- /.main-panel -->
    </div><!-- /.page-body-wrapper -->
  </div><!-- /.container-scroller -->
  <?php require_once(ROOT . 'includes/_js.php'); ?>

  <script type="text/javascript">
    function open_modal_detalle(id_registro) {
      var myModal = new bootstrap.Modal(document.getElementById("modal-detalle"), {});
      $('.modal-body-detalle').load(
        'monitoreos_crear_evaluacion_tipificacion_ver.php?reg=' + id_registro,
        function() {
          myModal.show();
        }
      );
    }

    function validar_programa() {
      var idPrograma = $("#tipi_programa").val();
      $("#tipi_tipificacion").html("");
      $("#subtipificacion").html("");
      $.post(
        "monitoreos_crear_evaluacion_validar_programa.php",
        { id: idPrograma },
        function(data) {
          $("#tipi_tipificacion").html(data);
        }
      );
    }

    function validar_tipificacion() {
      var idProg = $("#tipi_programa").val();
      var idTipi = $("#tipi_tipificacion").val();
      $("#subtipificacion").html("");
      $.post(
        "monitoreos_crear_evaluacion_validar_tipificacion.php",
        { id: idTipi, id2: idProg },
        function(data) {
          $("#subtipificacion").html(data);
        }
      );
    }

    function validar_solucion_contacto() {
      var valor_opcion = $("#solucion_primer_contacto").val();
      if (valor_opcion === "No") {
        $("#causal_no_solucion_div").removeClass('d-none').addClass('d-block');
        $("#causal_no_solucion").prop('disabled', false);
      } else {
        $("#causal_no_solucion_div").removeClass('d-block').addClass('d-none');
        $("#causal_no_solucion").prop('disabled', true);
      }
    }

    function validar_comentario(tipo, id_elemento) {
      if (tipo === "Si") {
        $("#comentario_" + id_elemento).removeClass('d-block').addClass('d-none');
        $("#comentario_" + id_elemento).prop('disabled', true);
      } else {
        $("#comentario_" + id_elemento).removeClass('d-none').addClass('d-block');
        $("#comentario_" + id_elemento).prop('disabled', false);
      }
    }

    function validar_voc() {
      var checked = $("#customCheckvoc").is(":checked");
      if (checked) {
        $("#voc_div_1, #voc_div_2, #voc_div_3, #voc_div_4, #voc_div_5, #voc_div_6, #voc_div_7, #voc_div_8")
          .removeClass('d-none').addClass('d-block');
        $("#segmento, #tabulacion_voc, #voc, #emocion_inicial, #emocion_final, #que_le_activo, #atribuible")
          .prop('disabled', false);
      } else {
        $("#voc_div_1, #voc_div_2, #voc_div_3, #voc_div_4, #voc_div_5, #voc_div_6, #voc_div_7, #voc_div_8")
          .removeClass('d-block').addClass('d-none');
        $("#segmento, #tabulacion_voc, #voc, #emocion_inicial, #emocion_final, #que_le_activo, #atribuible")
          .prop('disabled', true);
      }
    }

    function validar_segmento() {
      var idSeg = $("#segmento").val();
      $("#tabulacion_voc").html("");
      $.post(
        "monitoreos_crear_evaluacion_validar_segmento.php",
        { id: idSeg },
        function(data) {
          $("#tabulacion_voc").html(data);
        }
      );
    }
    document.querySelector("form[name='guardar_registro']")
      .addEventListener("submit", function(e) {
        var comp = document.getElementById("competencia").value;
        if (comp.length > 200) {
          e.preventDefault();
          alert("El campo 'Competencia' no puede superar los 200 caracteres.");
          document.getElementById("competencia").focus();
        }
      });
    // [ Opcionales: Si decides usar “direcciones misionales” para tipificar más niveles,
    //   puedes agregar aquí validar_nivel1(), validar_nivel2(), etc. como en el código original. ]
  </script>
</body>
</html>
