<?php
  session_start();
//  error_reporting(E_ALL);
  //ini_set('display_errors', 1);
  //ini_set('display_startup_errors', 1);
  // Validación de permisos del usuario para el módulo
  $modulo_plataforma = "Calidad-Monitoreos";
  require_once("../../iniciador.php");
  $url_fichero = pathinfo(__FILE__, PATHINFO_FILENAME);

  /* VARIABLES */
  $title             = "Calidad";
  $subtitle          = "Monitoreos | Crear Registro | Matriz";
  $pagina            = validar_input($_GET['pagina']);
  $filtro_permanente = validar_input($_GET['id']);
  $url_salir         = "monitoreos?pagina=" . $pagina . "&id=" . $filtro_permanente . "&bandeja=" . base64_encode('Mes Actual');

  // Limpiar cualquier dato previo de sesión
  unset($_SESSION[APP_SESSION . '_mon_informacion']['matriz']);

  // Consultar todas las matrices activas, ordenadas por canal y nombre
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
    WHERE `gcm_estado` = 'Activo' 
    ORDER BY `gcm_canal` ASC, `gcm_nombre_matriz` ASC
  ";

  $consulta_registros_matriz = $enlace_db->prepare($consulta_string_matriz);
  $consulta_registros_matriz->execute();
  $resultado_registros_matriz = $consulta_registros_matriz->get_result()->fetch_all(MYSQLI_NUM);

  // Construir arrays auxiliares: 
  //   - $array_canales: lista única de canales
  //   - $array_canales_matriz: para cada canal, lista de IDs de matriz
  //   - $array_matriz: para cada ID de matriz, su nombre
  $array_canales = [];
  $array_canales_matriz = [];
  $array_matriz = [];
  foreach ($resultado_registros_matriz as $fila) {
      $id_matriz = $fila[0];
      $nombre_matriz = $fila[1];
      $canal = $fila[3];

      $array_canales[] = $canal;
      $array_canales_matriz[$canal][] = $id_matriz;
      $array_matriz[$id_matriz]['nombre'] = $nombre_matriz;
  }

  if (!empty($array_canales)) {
      $array_canales = array_values(array_unique($array_canales));
  }
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
          <form name="guardar_registro" action="" method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center">
              <div class="col-lg-6 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="col-md-12 mb-2">
                          <p class="alert alert-success font-size-11 p-1">
                            ¡Seleccione un canal y una matriz para iniciar el monitoreo!
                          </p>
                        </div>
                        <div class="col-md-12">
                          <div class="accordion" id="accordionExample">
                            <?php for ($i = 0; $i < count($array_canales); $i++): 
                              $canal = $array_canales[$i];
                            ?>
                              <div class="accordion-item">
                                <h2 class="accordion-header" id="heading_<?php echo $i; ?>">
                                  <button 
                                    class="accordion-button p-1 collapsed background-principal color-blanco" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse_<?php echo $i; ?>" 
                                    aria-expanded="false" 
                                    aria-controls="collapse_<?php echo $i; ?>"
                                  >
                                    <?php echo htmlspecialchars($canal, ENT_QUOTES, 'UTF-8'); ?>
                                  </button>
                                </h2>
                                <div 
                                  id="collapse_<?php echo $i; ?>" 
                                  class="accordion-collapse collapse" 
                                  aria-labelledby="heading_<?php echo $i; ?>" 
                                  data-bs-parent="#accordionExample"
                                >
                                  <div class="accordion-body">
                                    <?php 
                                      if (isset($array_canales_matriz[$canal])) {
                                        foreach ($array_canales_matriz[$canal] as $id_matriz) {
                                          $nombre_m = $array_matriz[$id_matriz]['nombre'];
                                    ?>
                                      <a 
                                        href="monitoreos_crear_informacion?pagina=<?php echo $pagina; ?>&id=<?php echo $filtro_permanente; ?>&mat=<?php echo base64_encode($id_matriz); ?>" 
                                        class="btn btn-dark col-md-12 text-left mb-1 p-1"
                                      >
                                        <?php echo htmlspecialchars($nombre_m, ENT_QUOTES, 'UTF-8'); ?>
                                        <span class="fas fa-arrow-right float-end"></span>
                                      </a>
                                    <?php 
                                        }
                                      }
                                    ?>
                                  </div>
                                </div>
                              </div>
                            <?php endfor; ?>
                          </div>
                        </div>
                        <div class="col-md-12 mt-3">
                          <div class="form-group">
                            <button 
                              class="btn btn-danger float-end" 
                              type="button" 
                              onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');"
                            >
                              Cancelar
                            </button>
                          </div>
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
      </div>
      <!-- main-panel -->
    </div>
  </div>
  <?php require_once(ROOT . 'includes/_js.php'); ?>
</body>
</html>
