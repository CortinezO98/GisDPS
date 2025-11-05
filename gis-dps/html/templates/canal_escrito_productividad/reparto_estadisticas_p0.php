<div class="tab-pane fade show active mt-0" id="p0" role="tabpanel" aria-labelledby="p0"> 
  <div class="row">
    <div class="col-lg-12 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body py-2">
              <div class="row">
                <div class="col-md-12 mb-2">
                  <p class="alert background-principal color-blanco py-1 px-2 my-0"><i class="fas fa-chart-pie btn-icon-prepend me-0 me-lg-1 font-size-12"></i> Resumen Productividad</p>
                </div>
                <div class="col-md-12 mb-2 text-end">
                  <?php if($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor"): ?>
                    <button type="button" class="btn py-2 px-2 btn-primary btn-corp btn-icon-text font-size-12" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportes">
                      <i class="fas fa-file-excel btn-icon-prepend me-0 font-size-12"></i>
                    </button>
                  <?php endif; ?>
                </div>
                <div class="col-md-12">
                  <div class="table-responsive table-fixed" id="headerFixTable">
                    <table class="table table-hover table-bordered table-striped">
                      <?php for ($i=0; $i < count($array_coordinador); $i++): ?>
                        <thead>
                          <tr>
                            <th class="px-1 py-2 background-gris color-blanco">Coordinador</th>
                            <th class="px-1 py-2 background-gris color-blanco">Doc. Agente</th>
                            <th class="px-1 py-2 background-gris color-blanco">Agente</th>
                            <th class="px-1 py-2 background-gris color-blanco" style="width: 300px;">Productividad</th>
                            <th class="px-1 py-2 background-gris color-blanco" style="width: 300px;">Productividad Ajustada</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $id_coordinador=$array_coordinador[$i];
                            $nombre_coordinador=$nombresCoordinadoresArray[$id_coordinador];
                            $total_productividad=0;
                            $total_productividad_ajustada=0;
                          ?>
                          <?php for ($j=0; $j < count($usuariosPorCoordinador[$id_coordinador]); $j++): ?>
                            <?php
                              $id_agente_resumen=$usuariosPorCoordinador[$id_coordinador][$j];
                              $nombre_agente=$nombresUsuariosArray[$id_agente_resumen];
                            
                              if (count($cumplimientoPorUsuarioResumen[$id_agente_resumen])==0) {
                                $productividad_agente=0;  
                              } else {
                                $productividad_agente=number_format(array_sum($cumplimientoPorUsuarioResumen[$id_agente_resumen])/count($cumplimientoPorUsuarioResumen[$id_agente_resumen]), 2, '.', '');
                              }

                              $total_productividad+=$productividad_agente;

                              if ($productividad_agente==100) {
                                $color_progress='bg-success';
                              } elseif ($productividad_agente>=90) {
                                $color_progress='bg-warning';
                              } else {
                                $color_progress='bg-danger';
                              }

                              if (isset($array_resumen[$id_agente_resumen]['productividad_total_ajustada'])) {
                                if (count($array_resumen[$id_agente_resumen]['productividad_total_ajustada'])==0) {
                                  $productividad_agente_ajustada=0;
                                } else {
                                  $productividad_agente_ajustada=number_format(array_sum($array_resumen[$id_agente_resumen]['productividad_total_ajustada'])/count($array_resumen[$id_agente_resumen]['productividad_total_ajustada']), 2, '.', '');
                                }
                              } else {
                                $productividad_agente_ajustada=0;
                              }
                              
                              if ($productividad_agente_ajustada<$productividad_agente) {
                                $productividad_agente_ajustada=$productividad_agente;
                              }

                              $total_productividad_ajustada+=$productividad_agente_ajustada;

                              if ($productividad_agente_ajustada==100) {
                                $color_progress_ajustada='bg-success';
                              } elseif ($productividad_agente_ajustada>=90) {
                                $color_progress_ajustada='bg-warning';
                              } else {
                                $color_progress_ajustada='bg-danger';
                              }
                            ?>
                            <tr>
                              <td class="p-1 font-size-11 text-start"><?php echo $nombre_coordinador; ?></td>
                              <td class="p-1 font-size-11 text-start"><?php echo $id_agente_resumen; ?></td>
                              <td class="p-1 font-size-11 text-start"><?php echo $nombre_agente; ?></td>
                              <td class="p-1 font-size-11 text-start">
                                <div class="progress" style="height: 14px;">
                                  <div class="progress-bar <?php echo $color_progress; ?>" role="progressbar" style="width: <?php echo $productividad_agente; ?>%;" aria-valuenow="<?php echo $productividad_agente; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $productividad_agente; ?>%</div>
                                </div>
                              </td>
                              <td class="p-1 font-size-11 text-start">
                                <div class="progress" style="height: 14px;">
                                  <div class="progress-bar <?php echo $color_progress_ajustada; ?>" role="progressbar" style="width: <?php echo $productividad_agente_ajustada; ?>%;" aria-valuenow="<?php echo $productividad_agente_ajustada; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $productividad_agente_ajustada; ?>%</div>
                                </div>
                              </td>
                            </tr>
                          <?php endfor; ?>
                          <?php
                            $total_productividad=number_format($total_productividad/count($usuariosPorCoordinador[$id_coordinador]), 2, '.', '');
                            $total_productividad_ajustada=number_format($total_productividad_ajustada/count($usuariosPorCoordinador[$id_coordinador]), 2, '.', '');

                            if ($total_productividad==100) {
                              $color_progress_total='bg-success';
                            } elseif ($total_productividad>=90) {
                              $color_progress_total='bg-warning';
                            } else {
                              $color_progress_total='bg-danger';
                            }

                            if ($total_productividad_ajustada==100) {
                              $color_progress_total_ajustada='bg-success';
                            } elseif ($total_productividad_ajustada>=90) {
                              $color_progress_total_ajustada='bg-warning';
                            } else {
                              $color_progress_total_ajustada='bg-danger';
                            }
                          ?>
                        </tbody>
                        <thead>
                          <tr>
                            <th class="px-1 py-2 background-gris color-blanco" colspan="3">Total <?php echo $nombre_coordinador; ?></th>
                            <th class="px-1 py-2 background-gris color-blanco" style="width: 300px;">
                              <div class="progress" style="height: 14px;">
                                  <div class="progress-bar <?php echo $color_progress_total; ?>" role="progressbar" style="width: <?php echo $total_productividad; ?>%;" aria-valuenow="<?php echo $total_productividad; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $total_productividad; ?>%</div>
                                </div>
                            </th>
                            <th class="px-1 py-2 background-gris color-blanco" style="width: 300px;">
                              <div class="progress" style="height: 14px;">
                                  <div class="progress-bar <?php echo $color_progress_total_ajustada; ?>" role="progressbar" style="width: <?php echo $total_productividad_ajustada; ?>%;" aria-valuenow="<?php echo $total_productividad_ajustada; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $total_productividad_ajustada; ?>%</div>
                                </div>
                            </th>
                          </tr>
                        </thead>
                      <?php endfor; ?>
                    </table>
                  </div>
                  <?php if(count($array_coordinador)==0): ?>
                    <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- modal reportes -->
<?php require_once('productividad_reporte.php'); ?>
<!-- modal -->
