<?php
  session_start();
  //error_reporting(E_ALL);
  //ini_set('display_errors', 1);
 // ini_set('display_startup_errors', 1);

  // Validación de permisos del usuario para el módulo
  $modulo_plataforma = "Calidad-Monitoreos";
  require_once("../../iniciador.php");
  $url_fichero = pathinfo(__FILE__, PATHINFO_FILENAME);

  /* VARIABLES */
  $title             = "Calidad";
  $subtitle          = "Monitoreos | Crear Registro | Información";
  $pagina            = validar_input($_GET['pagina']);
  $filtro_permanente = validar_input($_GET['id']);
  // Se reemplaza $bandeja indefinida por base64_encode('Mes Actual')
  $url_salir = "monitoreos?pagina=" . $pagina . "&id=" . $filtro_permanente . "&bandeja=" . base64_encode('Mes Actual');



  if (isset($_POST['guardar_informacion'])) {
      // Asegúrate de que esos índices coincidan exactamente con
      // los "name" de tus <input> y <select> en el formulario.
      $_SESSION[APP_SESSION . '_mon_informacion'] = [
          'matriz'                   => validar_input($_POST['id_matriz']),
          'canal'                    => validar_input($_POST['canal']),
          'dependencia'              => validar_input($_POST['dependencia']),
          'numero_interaccion'       => validar_input($_POST['numero_interaccion']),
          'identificacion_ciudadano' => validar_input($_POST['identificacion_ciudadano']),
          'fecha_interaccion'        => validar_input($_POST['fecha_interaccion']),
          'analista'                 => validar_input($_POST['analista']),
          'tipo_monitoreo'           => validar_input($_POST['tipo_monitoreo']),
      ];
      // (Opcional) Para depurar, muestra en pantalla:
      // echo "<pre>SESION GUARDADA:\n";
      // print_r($_SESSION);
      // echo "</pre>";
      // exit;

      // Luego redirige a la siguiente etapa:
      header("Location: monitoreos_crear_evaluacion.php?pagina=$pagina&id=$filtro_permanente");
      exit;
  }







  // Si ya existe un valor en sesión, lo usamos. De lo contrario, lo tomamos de $_GET['mat'] (codificado en base64)
  if (isset($_SESSION[APP_SESSION . '_mon_informacion']['matriz']) 
      && $_SESSION[APP_SESSION . '_mon_informacion']['matriz'] !== ""
  ) {
      $id_matriz = $_SESSION[APP_SESSION . '_mon_informacion']['matriz'];
  } else {
      $id_matriz = validar_input(base64_decode($_GET['mat']));
  }

  // Consulta datos de la matriz seleccionada
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
  $consulta_registros_matriz->bind_param("s", $id_matriz);
  $consulta_registros_matriz->execute();
  $resultado_registros_matriz = $consulta_registros_matriz->get_result()->fetch_all(MYSQLI_NUM);

  // Armar filtro adicional según rol
  $data_consulta = [];
  if ($permisos_usuario == "Administrador" || $permisos_usuario == "Gestor") {
      $filtro_perfil = "";
  } elseif ($_SESSION[APP_SESSION . '_session_cargo'] == "Supervisor") {
      $filtro_perfil = " AND (`usu_supervisor` = ?)";
      $data_consulta[] = $_SESSION[APP_SESSION . '_session_usu_id'];
  } else {
      $filtro_perfil = "";
  }

  // Consultar agentes activos (rol AGENTE, INTERPRETE o FORMADOR), con filtro por supervisor si aplica
  $consulta_string_analista = "
    SELECT 
      `usu_id`, 
      `usu_nombres_apellidos`, 
      `usu_usuario_red` 
    FROM `administrador_usuario` 
    WHERE `usu_estado` = 'Activo' 
      AND (`usu_cargo_rol` LIKE '%AGENTE%' 
           OR `usu_cargo_rol` = 'INTERPRETE' 
           OR `usu_cargo_rol` = 'FORMADOR')
      $filtro_perfil
    ORDER BY `usu_nombres_apellidos`
  ";
  $consulta_registros_analistas = $enlace_db->prepare($consulta_string_analista);
  if (count($data_consulta) > 0) {
      $tipos = str_repeat("s", count($data_consulta));
      $consulta_registros_analistas->bind_param($tipos, ...$data_consulta);
  }
  $consulta_registros_analistas->execute();
  $resultado_registros_analistas = $consulta_registros_analistas->get_result()->fetch_all(MYSQLI_NUM);

  // Para limitar fechas: permitir hasta 1 año atrás
  $fecha_minimo   = date("Y-m-d", strtotime("-1 year"));
  $fecha_control  = date("Y-m-d", strtotime("-20 day"));
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
            action="monitoreos_crear_evaluacion.php?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>" 
            method="POST" 
            enctype="multipart/form-data"
          >
            <div class="row justify-content-center">
              <div class="col-lg-6 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="matriz" class="m-0">Matriz</label>
                              <input 
                                type="text" 
                                class="form-control form-control-sm font-size-11" 
                                name="matriz" 
                                id="matriz" 
                                value="<?php 
                                  echo isset($resultado_registros_matriz[0][1]) 
                                        ? htmlspecialchars($resultado_registros_matriz[0][1], ENT_QUOTES, 'UTF-8') 
                                        : '';
                                ?>" 
                                readonly
                              >
                              <input 
                                type="hidden" 
                                name="id_matriz" 
                                id="id_matriz" 
                                value="<?php 
                                  echo isset($resultado_registros_matriz[0][0]) 
                                        ? htmlspecialchars($resultado_registros_matriz[0][0], ENT_QUOTES, 'UTF-8') 
                                        : '';
                                ?>" 
                              >
                            </div>
                          </div>

                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="canal" class="m-0">Canal</label>
                              <input 
                                type="text" 
                                class="form-control form-control-sm font-size-11" 
                                name="canal" 
                                id="canal" 
                                value="<?php 
                                  echo isset($resultado_registros_matriz[0][3]) 
                                        ? htmlspecialchars($resultado_registros_matriz[0][3], ENT_QUOTES, 'UTF-8') 
                                        : '';
                                ?>" 
                                readonly
                              >
                            </div>
                          </div>

                          <?php if (isset($resultado_registros_matriz[0][3]) && $resultado_registros_matriz[0][3] === "Escrito"): ?>
                            <div class="col-md-12 mb-3">
                              <div class="form-group">
                                <label for="dependencia" class="m-0">Dependencia</label>
                                <select 
                                  class="form-control form-control-sm form-select" 
                                  name="dependencia" 
                                  id="dependencia" 
                                  required
                                >
                                  <option value="">Seleccione</option>
                                  <?php 
                                    $opciones_dep = [
                                      "Reparto", "Jóvenes", "Ingreso Solidario",
                                      "Adulto Mayor", "IVA", "Focalización", "No aplica"
                                    ];
                                    foreach ($opciones_dep as $dep) {
                                      $selected = (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['dependencia']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['dependencia'] === $dep
                                      ) ? "selected" : "";
                                  ?>
                                    <option value="<?php echo $dep; ?>" <?php echo $selected; ?>>
                                      <?php echo $dep; ?>
                                    </option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                          <?php endif; ?>

                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="numero_interaccion" class="m-0">Número interacción</label>
                              <input 
                                type="text" 
                                class="form-control form-control-sm font-size-11" 
                                name="numero_interaccion" 
                                id="numero_interaccion" 
                                maxlength="100" 
                                value="<?php 
                                  echo isset($_SESSION[APP_SESSION . '_mon_informacion']['numero_interaccion']) 
                                        ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['numero_interaccion'], ENT_QUOTES, 'UTF-8') 
                                        : '';
                                ?>" 
                                onkeyup="validar_transaccion();" 
                                required
                              >
                              <div id="coincidencias"></div>
                            </div>
                          </div>

                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="identificacion_ciudadano" class="m-0">Identificación ciudadano</label>
                              <input 
                                type="text" 
                                class="form-control form-control-sm font-size-11" 
                                name="identificacion_ciudadano" 
                                id="identificacion_ciudadano" 
                                maxlength="100" 
                                value="<?php 
                                  echo isset($_SESSION[APP_SESSION . '_mon_informacion']['identificacion_ciudadano']) 
                                        ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['identificacion_ciudadano'], ENT_QUOTES, 'UTF-8') 
                                        : '';
                                ?>" 
                                onkeyup="validar_transaccion();" 
                                required
                              >
                              <div id="coincidencias"></div>
                            </div>
                          </div>

                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="fecha_interaccion" class="m-0">Fecha interacción</label>
                              <input 
                                type="date" 
                                class="form-control form-control-sm font-size-11" 
                                name="fecha_interaccion" 
                                id="fecha_interaccion" 
                                value="<?php 
                                  echo isset($_SESSION[APP_SESSION . '_mon_informacion']['fecha_interaccion']) 
                                        ? htmlspecialchars($_SESSION[APP_SESSION . '_mon_informacion']['fecha_interaccion'], ENT_QUOTES, 'UTF-8') 
                                        : '';
                                ?>" 
                                onkeyup="validar_transaccion();" 
                                required
                              >
                              <div id="coincidencias"></div>
                            </div>
                          </div>

                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="analista" class="m-0">Agente</label>
                              <select 
                                class="selectpicker form-control form-control-sm form-select" 
                                data-live-search="true" 
                                data-container="body" 
                                name="analista" 
                                id="analista" 
                                required
                              >
                                <option value="" class="font-size-11">Seleccione</option>
                                <?php foreach ($resultado_registros_analistas as $fila): 
                                  // [0] = usu_id, [1] = nombres_apellidos, [2] = usuario_red
                                  $id_usu   = $fila[0];
                                  $nombres  = $fila[1];
                                  $usuarioR = $fila[2];
                                  $selected = (
                                    isset($_SESSION[APP_SESSION . '_mon_informacion']['analista']) 
                                    && $_SESSION[APP_SESSION . '_mon_informacion']['analista'] == $id_usu
                                  ) ? "selected" : "";
                                ?>
                                  <option 
                                    value="<?php echo $id_usu; ?>" 
                                    class="font-size-11" 
                                    data-tokens="<?php echo htmlspecialchars("$id_usu $nombres $usuarioR", ENT_QUOTES, 'UTF-8'); ?>" 
                                    <?php echo $selected; ?>
                                  >
                                    <?php echo htmlspecialchars($nombres, ENT_QUOTES, 'UTF-8'); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-12 mb-3">
                            <div class="form-group">
                              <label for="tipo_monitoreo" class="m-0">Tipo monitoreo</label>
                              <select 
                                class="form-control form-control-sm form-select" 
                                name="tipo_monitoreo" 
                                id="tipo_monitoreo" 
                                required
                              >
                                <option value="">Seleccione</option>
                                <?php if ($permisos_usuario != "Supervisor" AND $permisos_usuario != "Formador"): ?>
                                  <option 
                                    value="Muestra aleatoria" 
                                    <?php 
                                      echo (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'] === "Muestra aleatoria"
                                      ) ? "selected" : "";
                                    ?>
                                  >Muestra aleatoria</option>
                                  <option 
                                    value="Focalizado" 
                                    <?php 
                                      echo (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'] === "Focalizado"
                                      ) ? "selected" : "";
                                    ?>
                                  >Focalizado</option>
                                  <option 
                                    value="En línea" 
                                    <?php 
                                      echo (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'] === "En línea"
                                      ) ? "selected" : "";
                                    ?>
                                  >En línea</option>
                                  <option 
                                    value="Al lado" 
                                    <?php 
                                      echo (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'] === "Al lado"
                                      ) ? "selected" : "";
                                    ?>
                                  >Al lado</option>
                                <?php endif; ?>  
                                  <option 
                                    value="Calibración-Escucha 1" 
                                    <?php 
                                      echo (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'] === "Calibración-Escucha 1"
                                      ) ? "selected" : "";
                                    ?>
                                  >Calibración-Escucha 1</option>
                                <?php if ($permisos_usuario != "Supervisor" AND $permisos_usuario != "Formador"): ?>
                                  <option 
                                    value="Calibración-Escucha 2" 
                                    <?php 
                                      echo (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'] === "Calibración-Escucha 2"
                                      ) ? "selected" : "";
                                    ?>
                                  >Calibración-Escucha 2</option>
                                <?php endif; ?>
                                  <option 
                                    value="Seguimiento" 
                                    <?php 
                                      echo (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'] === "Seguimiento"
                                      ) ? "selected" : "";
                                    ?>
                                  >Seguimiento</option>
                                
                                <?php if ($permisos_usuario != "Supervisor" AND $permisos_usuario != "Formador"): ?>
                                  <option 
                                    value="Nuevos" 
                                    <?php 
                                      echo (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'] === "Nuevos"
                                      ) ? "selected" : "";
                                    ?>
                                  >Nuevos</option>
                                  <option 
                                    value="Indicador AE" 
                                    <?php 
                                      echo (
                                        isset($_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo']) 
                                        && $_SESSION[APP_SESSION . '_mon_informacion']['tipo_monitoreo'] === "Indicador AE"
                                      ) ? "selected" : "";
                                    ?>
                                  >Indicador AE</option>
                                <?php endif; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-12">
                            <div class="form-group">
                              <button 
                                class="btn btn-success float-end ms-1" 
                                type="submit" 
                                name="guardar_informacion"
                              >Siguiente</button>
                              <a 
                                href="<?php echo $url_salir; ?>" 
                                class="btn btn-warning float-end ms-1"
                              >Regresar</a>
                              <button 
                                class="btn btn-danger float-end" 
                                type="button" 
                                onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');"
                              >Cancelar</button>
                            </div>
                          </div>
                        </div><!-- /.row -->
                      </div><!-- /.card-body -->
                    </div><!-- /.card -->
                  </div><!-- /.col-12 -->
                </div><!-- /.row flex-grow -->
              </div><!-- /.col-lg-6 -->
            </div><!-- /.row justify-content-center -->
          </form>
        </div><!-- /.content-wrapper -->
      </div><!-- /.main-panel -->
    </div><!-- /.page-body-wrapper -->
  </div><!-- /.container-scroller -->
  <?php require_once(ROOT . 'includes/_js.php'); ?>

  <script type="text/javascript">
    function validar_transaccion() {
      var numero_interaccion = document.getElementById("numero_interaccion").value;
      var perfil = '<?php echo $_SESSION[APP_SESSION . '_session_cargo']; ?>';
      if (numero_interaccion !== '') {
        consultar_duplicado();
      }
    }

    function consultar_duplicado() {
      $.ajax({
        success: function() {
          $("#coincidencias").load("monitoreos_crear_informacion_duplicado.php?id=" + $("#numero_interaccion").val());
        }
      });
    }

    jQuery(document).ready(function(){
      jQuery("#identificacion_ciudadano").on('input', function (evt) {
        jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
      });
    });
  </script>
</body>
</html>
