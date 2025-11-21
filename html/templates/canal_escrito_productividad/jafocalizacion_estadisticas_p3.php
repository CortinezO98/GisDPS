<div class="tab-pane fade mt-0" id="ja_p3" role="tabpanel" aria-labelledby="ja_p3"> 
  <div class="row">
    <div class="col-lg-12 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body py-2">
              <div class="row">
                <div class="col-md-12 mb-2">
                  <p class="alert background-principal color-blanco py-1 px-2 my-0">JeA y Focalización | 3. Formato de Relación RAE JeA</p>
                  <?php $id_formulario='jafocalizacion_relacion_rae'; ?>
                </div>
                <?php include 'reparto_estadisticas_include.php'; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body">
              <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                  <h6 class="fw-bold card-title-dash">Gestión por hora por agente</h6>
                </div>
              </div>
              <div class="col-md-12">
                <?php if(isset($array_datos_gestion[$id_formulario]['agentes'])): ?>
                  <?php if(count($array_datos_gestion[$id_formulario]['agentes'])==0): ?>
                    <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
                  <?php endif; ?>
                <?php endif; ?>
                <div id="<?php echo $id_formulario; ?>"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  <?php include 'reparto_estadisticas_include_graph.php'; ?>
</script>