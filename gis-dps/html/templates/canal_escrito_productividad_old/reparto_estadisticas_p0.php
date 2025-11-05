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
                            <!-- <th class="px-1 py-2 background-gris color-blanco">Tipología</th>
                            <th class="px-1 py-2 background-gris color-blanco">Novedad</th>
                            <th class="px-1 py-2 background-gris color-blanco">Comentarios</th> -->
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $id_coordinador=$array_coordinador[$i];
                            $nombre_coordinador=$array_coordinador_datos[$id_coordinador]['nombre'];
                            $total_productividad=0;
                            $total_productividad_ajustada=0;
                          ?>
                          <?php for ($j=0; $j < count($array_coordinador_datos[$id_coordinador]['agentes']); $j++): ?>
                            <?php
                              $id_agente=$array_coordinador_datos[$id_coordinador]['agentes'][$j];
                              $nombre_agente=$array_datos_agente[$id_agente]['nombre'];
                            
                              if (count($array_resumen[$id_agente]['productividad_total'])==0) {
                                $productividad_agente=0;  
                              } else {
                                $productividad_agente=number_format(array_sum($array_resumen[$id_agente]['productividad_total'])/count($array_resumen[$id_agente]['productividad_total']), 2, '.', '');
                              }

                              // echo "<pre>";
                              // print_r($array_resumen[$id_agente]);
                              // echo "</pre>";
                              // echo array_sum($array_resumen[$id_agente]['productividad_total']);

                              if ($productividad_agente==100) {
                                $color_progress='bg-success';
                              } elseif ($productividad_agente>=90) {
                                $color_progress='bg-warning';
                              } else {
                                $color_progress='bg-danger';
                              }

                              $total_productividad+=$productividad_agente; 


                              //Ajustada
                              if (count($array_resumen[$id_agente]['productividad_total_ajustada'])==0) {
                                $productividad_agente_ajustada=0;
                              } else {
                                $productividad_agente_ajustada=number_format(array_sum($array_resumen[$id_agente]['productividad_total_ajustada'])/count($array_resumen[$id_agente]['productividad_total_ajustada']), 2, '.', '');
                              }
                              
                              if ($productividad_agente_ajustada<$productividad_agente) {
                                $productividad_agente_ajustada=$productividad_agente;
                              }

                              if ($productividad_agente_ajustada==100) {
                                $color_progress_ajustada='bg-success';
                              } elseif ($productividad_agente_ajustada>=90) {
                                $color_progress_ajustada='bg-warning';
                              } else {
                                $color_progress_ajustada='bg-danger';
                              }

                              if($productividad_agente_ajustada>0) {
                                $total_productividad_ajustada+=$productividad_agente_ajustada;
                              } else {
                                $total_productividad_ajustada+=$productividad_agente;
                              }
                            ?>
                            <tr>
                              <td class="p-1 font-size-11 text-start"><?php echo $nombre_coordinador; ?></td>
                              <td class="p-1 font-size-11 text-start"><?php echo $id_agente; ?></td>
                              <td class="p-1 font-size-11 text-start"><?php echo $nombre_agente; ?></td>
                              <td class="p-1 font-size-11 text-start">
                                <?php //echo "<pre>"; print_r($array_resumen[$id_agente]['productividad_total']); echo "</pre>"; ?>
                                <div class="progress" style="height: 14px;">
                                  <div class="progress-bar <?php echo $color_progress; ?>" role="progressbar" style="width: <?php echo $productividad_agente; ?>%;" aria-valuenow="<?php echo $productividad_agente; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $productividad_agente; ?>%</div>
                                </div>
                              </td>
                              <td class="p-1 font-size-11 text-start">
                                <?php //echo "<pre>"; print_r($array_resumen[$id_agente]['productividad_total_ajustada']); echo "</pre>"; ?>
                                <?php if($productividad_agente_ajustada>0): ?>
                                  <div class="progress" style="height: 14px;">
                                    <div class="progress-bar <?php echo $color_progress_ajustada; ?>" role="progressbar" style="width: <?php echo $productividad_agente_ajustada; ?>%;" aria-valuenow="<?php echo $productividad_agente_ajustada; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $productividad_agente_ajustada; ?>%</div>
                                  </div>
                                <?php else: ?>
                                  <div class="progress" style="height: 14px;">
                                    <div class="progress-bar <?php echo $color_progress; ?>" role="progressbar" style="width: <?php echo $productividad_agente; ?>%;" aria-valuenow="<?php echo $productividad_agente; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $productividad_agente; ?>%</div>
                                  </div>
                                <?php endif; ?>
                              </td>
                              <!-- <td class="p-1 font-size-11 text-start"><?php echo implode(';', $array_resumen[$id_agente]['tipologia']); ?></td>
                              <td class="p-1 font-size-11 text-start"><?php echo implode(';', $array_resumen[$id_agente]['novedad']); ?></td>
                              <td class="p-1 font-size-11 text-start"><?php echo implode(';', $array_resumen[$id_agente]['comentarios']); ?></td> -->
                            </tr>
                          <?php endfor; ?>
                        </tbody>
                        <thead>
                          <tr>
                            <th class="px-1 py-2 background-gris color-blanco text-end" colspan="3">Total <?php echo $nombre_coordinador; ?></th>
                            <th class="px-1 py-2 background-gris color-blanco">
                              <?php
                                $productividad_coordinador=number_format($total_productividad/count($array_coordinador_datos[$id_coordinador]['agentes']), 2, '.', '');

                                if ($productividad_coordinador==100) {
                                  $color_progress_coor='bg-success';
                                } elseif ($productividad_coordinador>=90) {
                                  $color_progress_coor='bg-warning';
                                } else {
                                  $color_progress_coor='bg-danger';
                                }
                              ?>
                              <div class="progress" style="height: 14px;">
                                <div class="progress-bar <?php echo $color_progress_coor; ?>" role="progressbar" style="width: <?php echo $productividad_coordinador; ?>%;" aria-valuenow="<?php echo $productividad_coordinador; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $productividad_coordinador; ?>%</div>
                              </div>
                            </th>
                            <th class="px-1 py-2 background-gris color-blanco">
                              <?php
                                $productividad_coordinador_ajustada=number_format($total_productividad_ajustada/count($array_coordinador_datos[$id_coordinador]['agentes']), 2, '.', '');

                                if ($productividad_coordinador_ajustada<$productividad_coordinador) {
                                  $productividad_coordinador_ajustada=$productividad_coordinador;
                                }

                                if ($productividad_coordinador_ajustada==100) {
                                  $color_progress_coor_ajustada='bg-success';
                                } elseif ($productividad_coordinador_ajustada>=90) {
                                  $color_progress_coor_ajustada='bg-warning';
                                } else {
                                  $color_progress_coor_ajustada='bg-danger';
                                }

                              ?>
                              <div class="progress" style="height: 14px;">
                                <div class="progress-bar <?php echo $color_progress_coor_ajustada; ?>" role="progressbar" style="width: <?php echo $productividad_coordinador_ajustada; ?>%;" aria-valuenow="<?php echo $productividad_coordinador_ajustada; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $productividad_coordinador_ajustada; ?>%</div>
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
