<?php
// En desarrollo muestra errores (quitar en producción)
if (defined('APP_DEBUG') && APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Validación de permisos
$modulo_plataforma = "Calidad-Calculadora Muestral";
require_once("../../iniciador.php");

// Parámetros de paginación para “Finalizar”
$pagina            = isset($_GET['pagina']) ? validar_input($_GET['pagina']) : '';
$filtro_permanente = isset($_GET['id'])     ? validar_input($_GET['id'])     : '';
$url_salir         = "cmuestral?pagina={$pagina}&id={$filtro_permanente}";

// Variables de formulario
$nombre_matriz     = '';
$intervalo_conf    = '';
$valor_z           = '';
$varianza_estimada = '';
$error_muestral    = '';
$respuesta_accion  = '';

// Usuario actual
$usuario_registro = isset($_SESSION[APP_SESSION . '_session_usu_id'])
    ? $_SESSION[APP_SESSION . '_session_usu_id']
    : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_registro'])) {
    // Sanitize inputs
    $nombre_matriz     = validar_input($_POST['nombre_matriz']);
    $intervalo_conf    = validar_input($_POST['intervalo_confianza']);
    $valor_z           = validar_input($_POST['valor_z']);
    $varianza_estimada = validar_input($_POST['varianza_estimada']);
    $error_muestral    = validar_input($_POST['error_muestral']);

    if (!$usuario_registro) {
        $respuesta_accion = "alertButton('error','Error','Usuario no válido');";
    } else {
        // Prepara y ejecuta INSERT
        $sql  = "INSERT INTO `gestion_calidad_cmuestral`
                 (`cm_nombre`, `cm_intervalo_confianza`, `cm_valor_z`,
                  `cm_varianza_estimada`, `cm_error_muestral`, `cm_registro_usuario`)
                 VALUES (?,?,?,?,?,?)";
        $stmt = $enlace_db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param(
                'ssssss',
                $nombre_matriz,
                $intervalo_conf,
                $valor_z,
                $varianza_estimada,
                $error_muestral,
                $usuario_registro
            );
            if ($stmt->execute()) {
                $respuesta_accion = "alertButton('success','Registro creado','Registro creado exitosamente');";
                // limpiar valores para un nuevo registro
                $nombre_matriz = $intervalo_conf = $valor_z = $varianza_estimada = $error_muestral = '';
            } else {
                $respuesta_accion = "alertButton('error','Error','No se pudo crear el registro, intente de nuevo');";
            }
        } else {
            $respuesta_accion = "alertButton('error','Error','Error al preparar la consulta');";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
  <?php require_once(ROOT.'includes/_head.php'); ?>
</head>
<body class="sidebar-dark sidebar-icon-only">
  <div class="container-scroller">
    <?php require_once(ROOT.'includes/_navbar.php'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php require_once(ROOT.'includes/_sidebar.php'); ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <form method="POST">
            <?php if ($respuesta_accion): ?>
              <script><?php echo $respuesta_accion; ?></script>
            <?php endif; ?>
            <div class="row justify-content-center">
              <div class="col-lg-10">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title mb-4"><?php echo $subtitle; ?></h4>
                    <div class="row g-3">
                      <div class="col-12">
                        <label for="nombre_matriz" class="form-label">Nombre calculadora</label>
                        <input
                          type="text"
                          id="nombre_matriz"
                          name="nombre_matriz"
                          class="form-control form-control-sm"
                          maxlength="100"
                          value="<?php echo htmlspecialchars($nombre_matriz, ENT_QUOTES, 'UTF-8'); ?>"
                          required
                        >
                      </div>
                      <div class="col-md-6">
                        <label for="intervalo_confianza" class="form-label">Intervalo confianza (e)</label>
                        <input
                          type="number"
                          id="intervalo_confianza"
                          name="intervalo_confianza"
                          class="form-control form-control-sm"
                          min="0" max="100" step="0.01"
                          value="<?php echo htmlspecialchars($intervalo_conf, ENT_QUOTES, 'UTF-8'); ?>"
                          required
                        >
                      </div>
                      <div class="col-md-6">
                        <label for="valor_z" class="form-label">Valor Z</label>
                        <input
                          type="number"
                          id="valor_z"
                          name="valor_z"
                          class="form-control form-control-sm"
                          min="0" max="100" step="0.01"
                          value="<?php echo htmlspecialchars($valor_z, ENT_QUOTES, 'UTF-8'); ?>"
                          required
                        >
                      </div>
                      <div class="col-md-6">
                        <label for="varianza_estimada" class="form-label">Varianza estimada (p)</label>
                        <input
                          type="number"
                          id="varianza_estimada"
                          name="varianza_estimada"
                          class="form-control form-control-sm"
                          min="0" max="100" step="0.01"
                          value="<?php echo htmlspecialchars($varianza_estimada, ENT_QUOTES, 'UTF-8'); ?>"
                          required
                        >
                      </div>
                      <div class="col-md-6">
                        <label for="error_muestral" class="form-label">Error muestral</label>
                        <input
                          type="number"
                          id="error_muestral"
                          name="error_muestral"
                          class="form-control form-control-sm"
                          min="0" max="100" step="0.01"
                          value="<?php echo htmlspecialchars($error_muestral, ENT_QUOTES, 'UTF-8'); ?>"
                          required
                        >
                      </div>
                    </div>
                    <div class="mt-4 text-end">
                      <button type="submit" name="guardar_registro" class="btn btn-success ms-1">Guardar</button>
                      <a href="<?php echo $url_salir; ?>" class="btn btn-danger">Cancelar</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php require_once(ROOT.'includes/_js.php'); ?>
</body>
</html>
