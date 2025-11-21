<!-- Modal -->
<div class="modal fade" id="modal-reporte" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Reportes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="reporte" action="correo_reporte_csv.php" method="POST">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <div class="form-group">
                  <label for="tipo_reporte" class="m-0">Tipo reporte</label>
                  <select class="form-control form-control-sm form-select" name="tipo_reporte" id="tipo_reporte" required>
                      <option value="">Seleccione</option>
                      <option value="Consolidado Gestión">Consolidado Gestión</option>
                  </select>
              </div>
          </div>
          <div class="col-md-12">
              <div class="form-group">
                  <label for="tipologia" class="m-0">Tipología</label>
                  <select class="selectpicker form-control form-control-sm form-select" name="tipologia" id="tipologia" title="Seleccione">
                      <option value="Reparto">Reparto</option>
                      <option value="Subsidio Familiar de Vivienda en especie">Subsidio Familiar de Vivienda en especie</option>
                      <option value="Ingreso Solidario">Ingreso Solidario</option>
                      <option value="Colombia Mayor">Colombia Mayor</option>
                      <option value="Compensación del IVA">Compensación del IVA</option>
                      <option value="Antifraudes">Antifraudes</option>
                      <option value="Jóvenes en Acción">Jóvenes en Acción</option>
                      <option value="Tránsito a Renta Ciudadana">Tránsito a Renta Ciudadana</option>
                      <option value="Otros programas">Otros programas</option>
                      <option value="Todos">Todos</option>
                    </select>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label for="fecha_inicio">Fecha inicio</label>
                <input type="date" class="form-control form-control-sm" name="fecha_inicio" id="fecha_inicio" value="" required>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label for="fecha_fin">Fecha fin</label>
                <input type="date" class="form-control form-control-sm" name="fecha_fin" id="fecha_fin" value="" required>
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger py-2 px-2" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" name="reporte" class="btn btn-primary btn-corp py-2 px-2">Generar</button>
      </div>
      </form>
    </div>
  </div>
</div>