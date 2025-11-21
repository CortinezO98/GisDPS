<?php
// Calidad - Calculadora Muestral | Configuración - Cargar Transacciones

// Validación de permisos del usuario para el módulo
$modulo_plataforma = "Calidad-Calculadora Muestral";

require_once("../../iniciador.php");
require_once("../../app/functions/validar_festivos.php");

// Autoload de PHPOffice (ruta original del proyecto)
require_once('../assets/plugins/PHPOffice/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;

// Guardas e inicializaciones
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!function_exists('validar_input')) {
    function validar_input($v) {
        if (is_array($v)) return $v;
        $v = trim((string)$v);
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
if (!function_exists('safe_count')) {
    function safe_count($v): int { return is_countable($v) ? count($v) : 0; }
}

// Constantes usadas en includes
if (!defined('ROOT')) { define('ROOT', '../../'); }
if (!defined('LANG')) { define('LANG', 'es'); }

// Variables de estado para evitar "undefined"
$respuesta_accion          = '';
$control_errores           = 0;
$control_errores_detalle   = [];
$array_data_base           = [];
$array_base_seleccionables = [];
$array_usuarios_seleccionables = [];
$array_usuarios_auditar    = [];
$usuarios_auditado         = [];

// Variables de entrada (GET) con saneo
$id_registro      = validar_input(base64_decode($_GET['reg']   ?? ''));
$fecha_dia        = validar_input(base64_decode($_GET['fecha'] ?? ''));
$mes_calculadora  = validar_input($_GET['date'] ?? '');

// Si falta contexto mínimo, se redirige a la pantalla anterior segura
if ($id_registro === '' || $mes_calculadora === '' || $fecha_dia === '') {
    header('Location: cmuestral_configurar?reg=' . base64_encode($id_registro) . '&date=' . $mes_calculadora);
    exit;
}

// URL de salida
$url_salir = "cmuestral_configurar?reg=" . base64_encode($id_registro) . "&date=" . $mes_calculadora;

// Títulos de página
$title    = "Calidad";
$subtitle = "Calculadora Muestral | Configuración - Cargar Transacciones";

// Consultas base
$consulta_string_usuarios = "SELECT `usu_id`, `usu_nombres_apellidos`, `usu_fecha_ingreso_piloto`, `usu_fecha_incorporacion`
                             FROM `administrador_usuario`
                             WHERE `usu_estado`='Activo'
                             ORDER BY `usu_nombres_apellidos` ASC";
$consulta_registros_usuarios = $enlace_db->prepare($consulta_string_usuarios);
$consulta_registros_usuarios->execute();
$resultado_registros_usuarios = $consulta_registros_usuarios->get_result()->fetch_all(MYSQLI_NUM);

// Mapa de datos de usuario: por id (para fechas) y opcional por nombre (solo se usa nombre si hiciera falta)
$usuarios_detalle = [];
for ($i = 0; $i < safe_count($resultado_registros_usuarios); $i++) {
    $idUsr  = $resultado_registros_usuarios[$i][0] ?? null;
    $nomUsr = $resultado_registros_usuarios[$i][1] ?? '';

    if ($idUsr !== null) {
        $usuarios_detalle[$idUsr] = [
            'nombre'        => $nomUsr,
            'fecha_piloto'  => $resultado_registros_usuarios[$i][2] ?? '',
            'fecha_ingreso' => $resultado_registros_usuarios[$i][3] ?? '',
        ];
    }
}

$consulta_string_segmento = "SELECT `cms_id`, `cms_calculadora`, `cms_nombre_segmento`, `cms_peso`
                             FROM `gestion_calidad_cmuestral_segmento`
                             WHERE `cms_calculadora`=?
                             ORDER BY `cms_nombre_segmento` ASC";
$consulta_registros_segmento = $enlace_db->prepare($consulta_string_segmento);
$consulta_registros_segmento->bind_param("s", $id_registro);
$consulta_registros_segmento->execute();
$resultado_registros_segmento = $consulta_registros_segmento->get_result()->fetch_all(MYSQLI_NUM);

$consulta_string_semana = "SELECT `cmm_id`, `cmm_calculadora`, `cmm_mes`, `cmm_segmento`, `cmm_total_mes`,
                                  `cmm_muestra_calculada`, `cmm_muestra_auditoria`, `cmm_numero_agentes`,
                                  `cmm_muestras_agente_mes`, `cmm_muestras_agente_semana`, `cmm_semana_dias`,
                                  `cmm_semana_peso`, `cmm_semana_porcentaje`, `cmm_semana_muestras`,
                                  `cmm_semana_inicio`, `cmm_semana_fin`, `cmm_muestra_realizada`,
                                  `cmm_muestra_recalculada`
                           FROM `gestion_calidad_cmuestral_mensual`
                           WHERE `cmm_calculadora`=? AND `cmm_mes`=?";
$consulta_registros_semana = $enlace_db->prepare($consulta_string_semana);
$consulta_registros_semana->bind_param("ss", $id_registro, $mes_calculadora);
$consulta_registros_semana->execute();
$resultado_registros_semana = $consulta_registros_semana->get_result()->fetch_all(MYSQLI_NUM);

// Días de semana (col 11) con valor por defecto 1 para evitar división por cero/undefined
$semana_dias = 1;
if (!empty($resultado_registros_semana) && isset($resultado_registros_semana[0][11]) && (int)$resultado_registros_semana[0][11] > 0) {
    $semana_dias = (int)$resultado_registros_semana[0][11];
}

// Procesamiento del formulario
if (isset($_POST["guardar_registro"])) {
    $base_transacciones = validar_input($_POST['base_transacciones'] ?? '');
    $muestras           = (int) validar_input($_POST['muestras'] ?? '0');

    // Validaciones rápidas de entrada
    if ($base_transacciones === '' || $muestras <= 0) {
        $respuesta_accion = "alertButton('error', 'Error', 'Datos incompletos: seleccione base y número de muestras');";
    } else {
        // Traer muestras ya registradas para este mes/calculadora
        $consulta_string_muestras = "SELECT `cmm_id`, `cmm_calculadora`, `cmm_mes`, `cmm_fecha`, `cmm_segmento`, `cmm_usuario`,
                                            `cmm_monitor`, `cmm_muestra_auditoria`, `cmm_muestra_fecha_hora`
                                     FROM `gestion_calidad_cmuestral_muestras`
                                     WHERE `cmm_calculadora`=? AND `cmm_mes`=?";
        $consulta_registros_muestras = $enlace_db->prepare($consulta_string_muestras);
        $consulta_registros_muestras->bind_param("ss", $id_registro, $mes_calculadora);
        $consulta_registros_muestras->execute();
        $resultado_registros_muestras = $consulta_registros_muestras->get_result()->fetch_all(MYSQLI_NUM);

        // Totales (manteniendo la lógica original)
        $total_semana = max(0, safe_count($resultado_registros_usuarios) - safe_count($resultado_registros_muestras));
        $total_diario = max(1, (int) round($total_semana / $semana_dias)); // no se usa más abajo, pero se conserva

        $usuarios_auditado = [];
        for ($i = 0; $i < safe_count($resultado_registros_muestras); $i++) {
            // índice 5 = cmm_usuario
            if (isset($resultado_registros_muestras[$i][5])) {
                $usuarios_auditado[] = $resultado_registros_muestras[$i][5];
            }
        }

        $id_usuario = $_SESSION[APP_SESSION . '_session_usu_id'] ?? null;

        if (empty($_SESSION[APP_SESSION . 'registro_cargue_base_transacciones']) || $_SESSION[APP_SESSION . 'registro_cargue_base_transacciones'] != 1) {
            // Validación de carga de archivo
            if (!isset($_FILES['documento']) || ($_FILES['documento']["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar el documento');";
            } else {
                // Validación de extensión
                $nombre_archivo_original = $_FILES['documento']['name'] ?? 'archivo.xlsx';
                $ext = strtolower(pathinfo($nombre_archivo_original, PATHINFO_EXTENSION));
                if (!in_array($ext, ['xlsx'])) {
                    $respuesta_accion = "alertButton('error', 'Error', 'Formato inválido: solo se acepta .xlsx');";
                } else {
                    $nombre_directorio = "storage_temporal/";
                    if (!is_dir($nombre_directorio)) {
                        @mkdir($nombre_directorio, 0775, true);
                    }
                    // Nombre seguro y único
                    $nombre_archivo_seguro = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($nombre_archivo_original));
                    $nombre_destino = $nombre_directorio . date('YmdHis') . '_' . $nombre_archivo_seguro;

                    if (move_uploaded_file($_FILES['documento']['tmp_name'], $nombre_destino)) {
                        if (file_exists($nombre_destino)) {
                            clearstatcache();

                            // Carga del Excel
                            try {
                                $documento            = IOFactory::load($nombre_destino);
                                $hojaActual           = $documento->getSheet(0);
                                $numeroMayorDeFila    = (int)$hojaActual->getHighestRow();
                            } catch (Throwable $e) {
                                $respuesta_accion = "alertButton('error', 'Error', 'No se pudo leer el archivo XLSX');";
                                $numeroMayorDeFila = 1;
                            }

                            $numero_total_registros = max(0, $numeroMayorDeFila - 1);
                            $control_item   = 0;

                            // Parseo de filas (manteniendo columnas originales para "Unificada")
                            for ($indicefila = 2; $indicefila <= $numeroMayorDeFila; $indicefila++) {
                                if ($base_transacciones === 'Unificada') {
                                    $columna_a = (string)$hojaActual->getCellByColumnAndRow(1, $indicefila)->getValue();           // ID TRANSACCIÓN
                                    $columna_b = (string)$hojaActual->getCellByColumnAndRow(2, $indicefila)->getValue();           // ID AGENTE
                                    $columna_c = (string)$hojaActual->getCellByColumnAndRow(3, $indicefila)->getValue();           // NOMBRE AGENTE
                                    $columna_d = (string)$hojaActual->getCellByColumnAndRow(4, $indicefila)->getFormattedValue();  // FECHA TRANSACCIÓN
                                    $columna_e = (string)$hojaActual->getCellByColumnAndRow(5, $indicefila)->getValue();           // CANAL-PROCESO

                                    $a = trim(validar_input($columna_a));
                                    $b = trim(validar_input($columna_b));
                                    $c = trim(validar_input($columna_c));
                                    $d = trim(validar_input($columna_d));
                                    $e = trim(validar_input($columna_e));

                                    if ($a !== '' && $b !== '' && $c !== '' && $d !== '' && $e !== '') {
                                        // Normalizar fecha/hora
                                        $fecha_norm = date('Y-m-d H:i:s', strtotime($d));

                                        $array_data_base[$control_item] = [
                                            'id_transaccion' => $a,
                                            'id_agente'      => $b,
                                            'nombre_agente'  => $c,
                                            'fecha'          => $fecha_norm,
                                            'canal'          => $e,
                                            'estado'         => 'no_seleccionable',
                                        ];

                                        // Reglas de elegibilidad según fechas del usuario
                                        $temp_fecha_piloto = $usuarios_detalle[$b]['fecha_piloto'] ?? '';

                                        // Si no hay registro del usuario, por compatibilidad lo marcamos como elegible
                                        $temp_usuario_estado = 1;
                                        $fecha_piloto_estado = 1;

                                        if ($temp_fecha_piloto !== '') {
                                            $limite_fecha_piloto = date("Y-m-d", strtotime("+30 day", strtotime($temp_fecha_piloto)));
                                            $fecha_piloto_estado = (date('Y-m-d') > $limite_fecha_piloto) ? 1 : 0;
                                        }

                                        if ($temp_usuario_estado && $fecha_piloto_estado) {
                                            $array_data_base[$control_item]['estado'] = 'seleccionable';
                                            if (!isset($array_base_seleccionables[$b])) {
                                                $array_base_seleccionables[$b] = [];
                                            }
                                            $array_base_seleccionables[$b][] = $control_item;
                                            $array_usuarios_seleccionables[] = $b;
                                        } elseif (!$temp_usuario_estado) {
                                            $array_data_base[$control_item]['estado'] = 'excluido_usuario';
                                        } elseif (!$fecha_piloto_estado) {
                                            $array_data_base[$control_item]['estado'] = 'excluido_fecha_area';
                                        }

                                        $control_item++;
                                    } else {
                                        // Fila con campos faltantes (opcional sumar a errores)
                                        // $control_errores++;
                                        // $control_errores_detalle[] = "Fila $indicefila: campos obligatorios vacíos.";
                                    }
                                }
                            }

                            // Únicos, aleatorios
                            $array_usuarios_seleccionables = array_values(array_unique($array_usuarios_seleccionables));
                            if (safe_count($array_usuarios_seleccionables) > 1) {
                                shuffle($array_usuarios_seleccionables);
                            }

                            // Selección de usuarios a auditar (muestras)
                            if ($muestras <= safe_count($array_usuarios_seleccionables)) {
                                $control_muestras = 0;
                                $k = 0;
                                // Evita bucles infinitos si hay usuarios ya auditados
                                $max_loops = safe_count($array_usuarios_seleccionables) + $muestras + 5;

                                while ($control_muestras < $muestras && $k < $max_loops) {
                                    $idxUser = $k % max(1, safe_count($array_usuarios_seleccionables));
                                    $usrId   = $array_usuarios_seleccionables[$idxUser] ?? null;

                                    if ($usrId !== null && isset($array_base_seleccionables[$usrId])) {
                                        if (safe_count($array_base_seleccionables[$usrId]) > 0 && !in_array($usrId, $usuarios_auditado, true)) {
                                            if (safe_count($array_base_seleccionables[$usrId]) > 1) {
                                                shuffle($array_base_seleccionables[$usrId]);
                                            }
                                            // Toma la primera transacción elegible del usuario
                                            $array_base_auditar[$usrId][] = $array_base_seleccionables[$usrId][0];
                                            $array_usuarios_auditar[]     = $usrId;
                                            $control_muestras++;
                                        }
                                    }
                                    $k++;
                                }

                                // INSERT masivo en gestion_calidad_cmuestral_transacciones
                                $sentencia_insert_data = $enlace_db->prepare(
                                    "INSERT INTO `gestion_calidad_cmuestral_transacciones`
                                        (`gcmt_calculadora`, `gcmt_mes`, `gcmt_fecha`, `gcmt_segmento`,
                                         `gcmt_transaccion_id`, `gcmt_campo_1`, `gcmt_campo_2`, `gcmt_campo_3`,
                                         `gcmt_campo_4`, `gcmt_campo_5`, `gcmt_campo_6`, `gcmt_campo_7`,
                                         `gcmt_campo_8`, `gcmt_campo_9`, `gcmt_campo_10`, `gcmt_estado`)
                                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                                );
                                $sentencia_insert_data->bind_param(
                                    'ssssssssssssssss',
                                    $id_registro, $mes_calculadora, $fecha_dia, $base_transacciones,
                                    $gcmt_transaccion_id, $gcmt_campo_1, $gcmt_campo_2, $gcmt_campo_3,
                                    $gcmt_campo_4, $gcmt_campo_5, $gcmt_campo_6, $gcmt_campo_7,
                                    $gcmt_campo_8, $gcmt_campo_9, $gcmt_campo_10, $gcmt_estado
                                );

                                $control_insert = 0;
                                $control_fail   = 0;
                                $string_fail    = "";

                                for ($i = 0; $i < safe_count($array_data_base); $i++) {
                                    $gcmt_transaccion_id = $array_data_base[$i]['id_transaccion'] ?? '';
                                    $gcmt_campo_1        = $array_data_base[$i]['id_agente']      ?? '';
                                    $gcmt_campo_2        = $array_data_base[$i]['fecha']          ?? '';
                                    $gcmt_campo_3        = $array_data_base[$i]['canal']          ?? '';
                                    $gcmt_campo_4        = '';
                                    $gcmt_campo_5        = '';
                                    $gcmt_campo_6        = '';
                                    $gcmt_campo_7        = '';
                                    $gcmt_campo_8        = '';
                                    $gcmt_campo_9        = '';
                                    $gcmt_campo_10       = '';
                                    $gcmt_estado         = $array_data_base[$i]['estado']         ?? 'no_seleccionable';

                                    if ($sentencia_insert_data->execute()) {
                                        $control_insert++;
                                    } else {
                                        $control_fail++;
                                        $string_fail .= ($array_data_base[$i]['id_transaccion'] ?? 'sin_id') . "\r\n";
                                    }
                                }

                                if (($control_insert + $control_fail) === safe_count($array_data_base)) {
                                    // LOG
                                    $consulta_string_log = "INSERT INTO `administrador_log`
                                        (`clog_log_modulo`, `clog_log_tipo`, `clog_log_accion`, `clog_log_detalle`, `clog_registro_usuario`)
                                        VALUES (?,?,?,?,?)";
                                    $log_modulo  = $modulo_plataforma;
                                    $log_tipo    = "crear";
                                    $log_accion  = "Crear registro";
                                    $log_detalle = "Cargue base transacciones [" . $base_transacciones . "]";
                                    $log_usuario = $_SESSION[APP_SESSION . '_session_usu_id'] ?? '';

                                    $consulta_registros_log = $enlace_db->prepare($consulta_string_log);
                                    $consulta_registros_log->bind_param("sssss", $log_modulo, $log_tipo, $log_accion, $log_detalle, $log_usuario);
                                    $consulta_registros_log->execute();

                                    // INSERT muestras seleccionadas
                                    $sentencia_insert_muestras = $enlace_db->prepare(
                                        "INSERT INTO `gestion_calidad_cmuestral_muestras`
                                            (`cmm_calculadora`, `cmm_mes`, `cmm_fecha`, `cmm_segmento`,
                                             `cmm_usuario`, `cmm_monitor`, `cmm_muestra_auditoria`, `cmm_muestra_fecha_hora`)
                                         VALUES (?,?,?,?,?,?,?,?)"
                                    );
                                    $sentencia_insert_muestras->bind_param(
                                        'ssssssss',
                                        $id_registro, $mes_calculadora, $fecha_dia, $canal,
                                        $cmm_usuario, $cmm_monitor, $cmm_muestra_auditoria, $cmm_muestra_fecha_hora
                                    );

                                    for ($i = 0; $i < safe_count($array_usuarios_auditar); $i++) {
                                        $cmm_usuario          = $array_usuarios_auditar[$i];
                                        $cmm_monitor          = '';
                                        $idxPrimerItemUsuario = $array_base_auditar[$cmm_usuario][0] ?? null;

                                        if ($idxPrimerItemUsuario !== null && isset($array_data_base[$idxPrimerItemUsuario])) {
                                            $cmm_muestra_auditoria = $array_data_base[$idxPrimerItemUsuario]['id_transaccion'] ?? '';
                                            $cmm_muestra_fecha_hora= $array_data_base[$idxPrimerItemUsuario]['fecha'] ?? '';
                                            $canal                 = $array_data_base[$idxPrimerItemUsuario]['canal'] ?? '';

                                            if ($cmm_usuario !== "" && $cmm_muestra_auditoria !== "") {
                                                $sentencia_insert_muestras->execute();
                                            }
                                        }
                                    }

                                    $respuesta_accion = "alertButton('success', 'Registro creado', 'Base cargada exitosamente | Cargado: {$control_insert} | Error: {$control_fail}');";
                                    $_SESSION[APP_SESSION . 'registro_cargue_base_transacciones'] = 1;

                                    // Archivo con fallos (si los hubo)
                                    $nombre_temporal_control = "storage_temporal/CARGAR_FAIL" . date('YmdHis') . ".txt";
                                    if ($string_fail !== '') {
                                        $archivo_fail = fopen($nombre_temporal_control, 'a');
                                        if ($archivo_fail) {
                                            fputs($archivo_fail, $string_fail);
                                            fclose($archivo_fail);
                                        }
                                    }
                                } else {
                                    $respuesta_accion = "alertButton('error', 'Error', 'Problemas al cargar base, por favor intente nuevamente');";
                                }
                            } else {
                                $respuesta_accion = "alertButton('error', 'Error', 'No se pudo ubicar el archivo cargado');";
                            }
                        } else {
                            $respuesta_accion = "alertButton('error', 'Error', 'No se pudo mover el archivo cargado');";
                        }
                    }
                }
            }
        } else {
            $respuesta_accion = "alertButton('success', 'Registro creado', 'Registro creado exitosamente');";
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
            <?php if (!empty($respuesta_accion)) {echo "<script type='text/javascript'>".$respuesta_accion."</script>";} ?>
            <div class="col-lg-7 d-flex flex-column">
              <div class="row flex-grow">
                <div class="col-12 grid-margin stretch-card">
                  <div class="card card-rounded">
                    <div class="card-body">
                      <?php if($control_errores>0): ?>
                          <div class="col-md-12">
                              <p class="alert alert-danger p-1 font-size-11">Por favor verifique los siguientes errores:</p>
                              <?php for ($i=0; $i < safe_count($control_errores_detalle); $i++): ?>
                              <p class="alert alert-warning p-1 font-size-11 my-0"><?php echo $control_errores_detalle[$i]; ?></p>
                              <?php endfor; ?>
                          </div>
                      <?php endif; ?>

                      <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="mes" class="m-0">Mes</label>
                              <input type="text" class="form-control form-control-sm" name="mes" id="mes" value="<?php echo $mes_calculadora; ?>" readonly required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="fecha" class="m-0">Fecha</label>
                              <input type="text" class="form-control form-control-sm" name="fecha" id="fecha" value="<?php echo $fecha_dia; ?>" readonly required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="base_transacciones" class="m-0">Base transacciones</label>
                                <select class="form-control form-control-sm" name="base_transacciones" id="base_transacciones" <?php echo (!empty($_SESSION[APP_SESSION.'registro_cargue_base_transacciones']) && $_SESSION[APP_SESSION.'registro_cargue_base_transacciones']==1) ? 'disabled' : ''; ?> required>
                                    <option value="">Seleccione</option>
                                    <option value="Unificada" <?php echo (isset($_POST["guardar_registro"]) && ($base_transacciones ?? '')==='Unificada') ? "selected" : ""; ?>>Unificada</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="muestras" class="m-0">Muestras</label>
                              <input type="number" class="form-control form-control-sm" name="muestras" id="muestras" min="10"
                                     value="<?php echo htmlspecialchars($_POST["muestras"] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
                                     <?php echo (!empty($_SESSION[APP_SESSION.'registro_cargue_base_transacciones'])) ? 'disabled' : ''; ?> required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="documento" class="my-0">Documento base</label>
                                <input class="form-control form-control-sm custom-file-input" name="documento" id="inputGroupFile01" type="file"
                                       <?php echo (!empty($_SESSION[APP_SESSION.'registro_cargue_base_transacciones'])) ? 'disabled' : ''; ?>
                                       accept=".xlsx,.XLSX" required>
                            </div>
                        </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <?php if(!empty($_SESSION[APP_SESSION.'registro_cargue_base_transacciones']) && $_SESSION[APP_SESSION.'registro_cargue_base_transacciones']==1): ?>
                                  <a href="<?php echo $url_salir; ?>" class="btn btn-dark float-end">Finalizar</a>
                              <?php else: ?>
                                  <button class="btn btn-success float-end ms-1" type="submit" name="guardar_registro" id="guardar_registro_btn">Cargar transacciones</button>
                                  <button class="btn btn-danger float-end" type="button" onclick="alertButton('cancel', null, null, '<?php echo $url_salir; ?>');">Cancelar</button>
                              <?php endif; ?>
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
  <?php require_once(ROOT.'includes/_js.php'); ?>
  <script type="text/javascript">
      $("#inputGroupFile01").on('change', function(){
          var fileObj = document.getElementById("inputGroupFile01").files[0];
          if (fileObj && fileObj.name) {
              var nombre = fileObj.name;
              var corto  = (nombre.length > 25) ? (nombre.substring(0, 25) + "...") : nombre;
              // Si existe un label con ese id, lo actualizamos (la vista original lo usa)
              var label = document.getElementById('inputGroupFile01label') || document.getElementById('inputGroupFile01');
              if (label) { label.innerHTML = corto; }
              $("#inputGroupFile01label").addClass("color-verde");
          }
      });
  </script>
</body>
</html>
