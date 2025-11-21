<!-- Modal -->
<div class="modal fade" id="modal-reporte" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Reportes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="reporte" action="logs_reporte_excel.php" method="POST">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <div class="form-group">
                  <label for="usuario">Usuario</label>
                  <select class="form-control form-control-sm form-select font-size-11" name="usuario" id="usuario" required>
                      <option value="Todos">Todos</option>
                      <?php for ($i=0; $i < count($resultado_registros_usuarios); $i++): ?> 
                          <option value="<?php echo $resultado_registros_usuarios[$i][0]; ?>"><?php echo $resultado_registros_usuarios[$i][1]; ?></option>
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