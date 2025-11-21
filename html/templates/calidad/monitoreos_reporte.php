<!-- Modal -->
<div class="modal fade" id="modal-reporte" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Reportes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="reporte" action="monitoreos_reporte_excel.php" method="POST">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <div class="form-group">
                  <label for="tipo_reporte" class="m-0">Tipo reporte</label>
                  <select class="form-control form-control-sm form-select" name="tipo_reporte" id="tipo_reporte" required>
                      <option value="">Seleccione</option>
                      <option value="Consolidado">Consolidado</option>
                      <option value="Consolidado-Matriz">Consolidado-Matriz</option>
                  </select>
              </div>
          </div>
          <div class="col-md-12">
              <div class="form-group">
                  <label for="id_matriz" class="m-0">Matriz monitoreo</label>
                  <select class="form-control form-control-sm form-select" name="id_matriz" id="id_matriz" required>
                      <option value="">Seleccione</option>
                      <option value="Todas">Todas</option>
                      <?php for ($i=0; $i < count($resultado_registros_matriz); $i++): ?>
                        <option value="<?php echo $resultado_registros_matriz[$i][0]; ?>"><?php echo $resultado_registros_matriz[$i][1]; ?></option>
                      <?php endfor; ?>
                  </select>
              </div>
          </div>
          <div class="col-md-12">
              <div class="form-group">
                  <label for="tipo_monitoreo" class="m-0">Tipo monitoreo</label>
                  <select class="form-control form-control-sm form-select" name="tipo_monitoreo" id="tipo_monitoreo" required>
                      <option value="">Seleccione</option>
                      <option value="Todos">Todos</option>
                      <option value="Muestra aleatoria">Muestra aleatoria</option>
                      <option value="En línea">En línea</option>
                      <option value="Al lado">Al lado</option>
                      <option value="Calibración-Escucha 1">Calibración-Escucha 1</option>
                      <option value="Calibración-Escucha 2">Calibración-Escucha 2</option>
                      <option value="Seguimiento">Seguimiento</option>
                      <option value="Focalizado">Focalizado</option>
                      <option value="Nuevos">Nuevos</option>
                  </select>
              </div>
          </div>
          <div class="col-md-12">
              <div class="form-group">
                  <label for="agente" class="m-0">Agente</label>
                  <select class="selectpicker form-control form-control-sm form-select" name="agente" id="agente" data-live-search="true" data-container="body" required>
                      <option value="">Seleccione</option>
                      <option value="Todos" class="font-size-11">Todos</option>
                      <?php for ($i=0; $i < count($resultado_registros_analistas); $i++): ?> 
                        <option value="<?php echo $resultado_registros_analistas[$i][0]; ?>" class="font-size-11" data-tokens="<?php echo $resultado_registros_analistas[$i][0].' '.$resultado_registros_analistas[$i][1].' '.$resultado_registros_analistas[$i][2]; ?>"><?php echo $resultado_registros_analistas[$i][1]; ?></option>
                      <?php endfor; ?>
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