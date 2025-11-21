<?php
  // Validación de permisos del usuario para el módulo
  $modulo_plataforma = "Interacciones";
  require_once("../../iniciador.php");
  session_start();

  // ============================
  // 1) Generar listado de meses
  //    – Desde enero 2025 hasta mes actual:
  $array_meses_lista = [];
  $anio_inicio = 2025;
  $anio_hoy   = date('Y');
  $mes_hoy    = date('m');
  for ($a = $anio_inicio; $a <= $anio_hoy; $a++) {
    $mes_max = ($a === $anio_hoy) ? intval($mes_hoy) : 12;
    for ($m = 1; $m <= $mes_max; $m++) {
      $array_meses_lista[] = sprintf('%04d-%02d', $a, $m);
    }
  }
  // Orden ascendente
  sort($array_meses_lista);

  // 2) Preparar nombres de meses (en español)
  $nombres_meses = [
    1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
    7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
  ];

  // 3) Listado de canales (igual que en interacciones.php)
  //    Lo cargamos desde la configuración de auxiliares:
  $array_opciones = [];
  $stmt = $enlace_db->prepare("
    SELECT `gia_opciones`
      FROM `gestion_interacciones_auxiliar`
     WHERE `gia_estado`='Activo'
       AND `gia_campo`='canal_atencion'
  ");
  $stmt->execute();
  $op = $stmt->get_result()->fetch_row()[0] ?? '';
  foreach (explode('|', $op) as $canal) {
    if (trim($canal) !== '') {
      $array_opciones['canal_atencion'][] = trim($canal);
    }
  }
?>
<!-- Modal -->
<div class="modal fade" id="modal-reporte" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Reportes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <?php
        // Valores por defecto del mes actual
        $mes_actual = date('Y-m');
        $def_inicio = date('Y-m-01');
        $def_fin    = date('Y-m-t');
      ?>
      <form name="reporte" action="interacciones_reporte_csv.php" method="POST">
        <div class="modal-body">
          <div class="row">
            <!-- Año-Mes -->
            <div class="col-md-12">
              <div class="form-group mb-2">
                <label for="tipo">Año - Mes</label>
                <select
                  class="form-select form-select-sm"
                  name="tipo"
                  id="tipo"
                  required
                >
                  <?php foreach($array_meses_lista as $mes): ?>
                    <option
                      value="<?= $mes ?>"
                      <?= ($mes_actual === $mes) ? 'selected' : '' ?>
                    >
                      <?= sprintf(
                            '%s - %s',
                            date('Y', strtotime($mes)),
                            $nombres_meses[intval(date('m', strtotime($mes)))]
                          ) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Canal de atención -->
            <div class="col-md-12">
              <div class="form-group mb-2">
                <label for="canal_atencion">Canal de atención</label>
                <select
                  class="form-select form-select-sm"
                  name="canal_atencion"
                  id="canal_atencion"
                  required
                >
                  <option value="Todos">Todos</option>
                  <?php foreach($array_opciones['canal_atencion'] as $canal): ?>
                    <option value="<?= $canal ?>">
                      <?= $canal ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Fecha inicio -->
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="fecha_inicio">Fecha inicio</label>
                <input
                  type="date"
                  class="form-control form-control-sm"
                  name="fecha_inicio"
                  id="fecha_inicio"
                  value="<?= $def_inicio ?>"
                >
              </div>
            </div>

            <!-- Fecha fin -->
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="fecha_fin">Fecha fin</label>
                <input
                  type="date"
                  class="form-control form-control-sm"
                  name="fecha_fin"
                  id="fecha_fin"
                  value="<?= $def_fin ?>"
                >
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-danger btn-sm"
            data-bs-dismiss="modal"
          >Cancelar</button>
          <button
            type="submit"
            name="reporte"
            class="btn btn-primary btn-sm"
          >Generar</button>
        </div>
      </form>
    </div>
  </div>
</div>
