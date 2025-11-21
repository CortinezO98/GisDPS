<!-- Modal -->
<div class="modal fade" id="modal-reporte" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Reportes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="reporte" action="familias_accion_reporte_csv.php" method="POST">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <div class="form-group">
                  <label for="tipo_reporte" class="m-0">Tipo reporte</label>
                  <select class="form-control form-control-sm form-select" name="tipo_reporte" id="tipo_reporte" required>
                      <option value="">Seleccione</option>
                      <option value="Consolidado Gestión">Consolidado Gestión</option>
                      <option value="Notificaciones Correo">Notificaciones Correo</option>
                      <option value="Notificaciones SMS">Notificaciones SMS</option>
                  </select>
              </div>
          </div>
          <div class="col-md-12">
              <div class="form-group">
                  <label for="estado_reporte" class="m-0">Tipo reporte</label>
                  <select class="selectpicker form-control form-control-sm form-select" name="estado_reporte[]" id="estado_reporte" multiple title="Estado">
                      <option value="Aplazado">Aplazado</option>
                      <option value="Aplazado Segunda Revisión">Aplazado Segunda Revisión</option>
                      <option value="Aplazado Tercera Revisión">Aplazado Tercera Revisión</option>
                      <option value="Pendiente llamada">Pendiente llamada</option>
                      <option value="Pendiente llamada-Segunda Revisión">Pendiente llamada-Segunda Revisión</option>
                      <option value="Intento Contacto-Fallido">Intento Contacto-Fallido</option>
                      <option value="Intento Contacto-Fallido-Segunda Revisión">Intento Contacto-Fallido-Segunda Revisión</option>
                      <!-- <option value="Nuevo Contacto-Error Subsanación">Nuevo Contacto-Error Subsanación</option> -->
                      <option value="Intento Contacto-Agotado">Intento Contacto-Agotado</option>
                      <option value="Intento Contacto-Agotado-Segunda Revisión">Intento Contacto-Agotado-Segunda Revisión</option>
                      <option value="Contactado-Pendiente Documentos">Contactado-Pendiente Documentos</option>
                      <option value="Contactado-Pendiente Documentos-Segunda Revisión">Contactado-Pendiente Documentos-Segunda Revisión</option>
                      <option value="Documentos Cargados">Documentos Cargados</option>
                      <option value="Documentos Cargados-Segunda Revisión">Documentos Cargados-Segunda Revisión</option>
                      <option value="Escalado-Validar">Escalado-Validar</option>
                      <option value="Escalado-Validar-Segunda Revisión">Escalado-Validar-Segunda Revisión</option>
                      <option value="Escalado-Cliente">Escalado-Cliente</option>
                      <option value="Escalado-Cliente-Segunda Revisión">Escalado-Cliente-Segunda Revisión</option>
                      <option value="Segunda Revisión OCR">Segunda Revisión OCR</option>
                      <option value="Validado-OCR">Validado-OCR</option>
                      <option value="Validado-OCR-Segunda Revisión">Validado-OCR-Segunda Revisión</option>
                      <option value="Validado-Agente">Validado-Agente</option>
                      <option value="Validado-Agente-Segunda Revisión">Validado-Agente-Segunda Revisión</option>
                      <option value="Validado-Agente-Tercera Revisión">Validado-Agente-Tercera Revisión</option>
                      <option value="Inscrito SIFA">Inscrito SIFA</option>
                      <option value="Inscrito SIFA RPA">Inscrito SIFA RPA</option>
                      <option value="Error en la página">Error en la página</option>
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