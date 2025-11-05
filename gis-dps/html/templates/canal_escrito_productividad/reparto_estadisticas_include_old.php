<div class="col-md-12">
  <?php
    $total_productividad=0;
    $productividad_control=false;
    $total_productividad_consolidada=0;

    if (!isset($array_datos_gestion[$id_formulario]['agentes'])) {
      $array_datos_gestion[$id_formulario]['agentes']=array();
    }

    for ($i=0; $i < count($array_datos_gestion[$id_formulario]['agentes']); $i++) {
      $id_agente_dash=$array_datos_gestion[$id_formulario]['agentes'][$i];
      
      unset($productividad_agente_array);
      $productividad_agente_array=array();
      for ($j=0; $j < count($array_datos_gestion[$id_formulario]['gestion_agente_fecha_lista'][$id_agente_dash]); $j++) { 
        $fecha_agente=$array_datos_gestion[$id_formulario]['gestion_agente_fecha_lista'][$id_agente_dash][$j];
        $productividad_agente_fecha=maximo100(($array_datos_gestion[$id_formulario]['gestion_agente_fecha'][$id_agente_dash][$fecha_agente]*100)/$array_metas[$id_formulario]['meta'][$fecha_agente]);
        $productividad_agente_array[]=$productividad_agente_fecha;
      }

      $productividad_total+=array_sum($productividad_agente_array)/count($productividad_agente_array);

      // $productividad_agente_ajustada=number_format($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['ajustada'], 2, '.', '');

      // if ($productividad_agente_ajustada<$productividad_agente) {
      //   $productividad_agente_ajustada=$productividad_agente;
      // }

      // if($productividad_agente_ajustada>0) {
      //   $total_productividad+=$productividad_agente_ajustada;
      // } else {
      //   $total_productividad+=$productividad_agente;
      // }

      if ($productividad_total==100) {

      } elseif ($productividad_total>=90) {
        $productividad_control=true;
      } else {
        $productividad_control=true;
      }
    }
    
    

    if (count($array_datos_gestion[$id_formulario]['agentes'])>0) {
      $total_productividad_consolidada=$productividad_total/count($array_datos_gestion[$id_formulario]['agentes']);
    } else {
      $total_productividad_consolidada=0;
    }
    
    $total_productividad_consolidada=number_format($total_productividad_consolidada, 2, '.', '');

    if ($total_productividad_consolidada>=100) {
      $color_progress_total='bg-success';
      $total_productividad_consolidada=100;
    } elseif ($total_productividad_consolidada>=90) {
      $color_progress_total='bg-warning';
    } else {
      $color_progress_total='bg-danger';
    }
  ?>
  <b>Productividad General</b>
  <?php if($productividad_control AND $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin'])) AND ($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor")): ?>
    <a href="productividad_justificar?grupo=<?php echo base64_encode('reparto'); ?>&formulario=<?php echo base64_encode($id_formulario); ?>&fecha=<?php echo base64_encode($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']); ?>" class="btn py-2 px-2 btn-danger mb-1"><i class="fas fa-flag font-size-11"></i> Justificar</a>
  <?php endif; ?>
  <div class="progress my-1" style="height: 30px;">
    <div class="progress-bar <?php echo $color_progress_total; ?>" role="progressbar" style="width: <?php echo $total_productividad_consolidada; ?>%;" aria-valuenow="<?php echo $total_productividad_consolidada; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($total_productividad_consolidada, 2, '.', ''); ?>%</div>
  </div>

  <div class="table-responsive table-fixed" id="headerFixTable">
    <table class="table table-hover table-bordered table-striped">
      <thead>
        <tr>
          <th class="px-1 py-2">Coordinador</th>
          <th class="px-1 py-2">Agente</th>
          <!-- <th class="px-1 py-2">Calculos</th> -->
          <th class="px-1 py-2" style="width: 150px;">Productividad</th>
          <th class="px-1 py-2" style="width: 150px;">Productividad Ajustada</th>
          <th class="px-1 py-2 text-center">Promedio Meta Diaria</th>
          <th class="px-1 py-2 text-center">Días</th>
          <th class="px-1 py-2 text-center">Promedio Gestiones Diaria</th>
          <th class="px-1 py-2 text-center">Promedio Casos Hora</th>
          <th class="px-1 py-2 text-center">Promedio Horas No Cargue</th>
          <!-- <th class="px-1 py-2">Tipología</th> -->
          <!-- <th class="px-1 py-2">Novedad</th> -->
        </tr>
      </thead>
      <?php if(isset($array_datos_gestion[$id_formulario]['agentes'])): ?>
        <tbody>
          <?php
            $total_productividad=0;
            $total_productividad_agente=0;
            $total_productividad_agente_consolidada=0;
            $total_productividad_agente_ajustada=0;
            $total_productividad_agente_ajustada_consolidada=0;
          ?>
          <?php for ($i=0; $i < count($array_datos_gestion[$id_formulario]['agentes']); $i++): ?>
          <?php
            $id_agente_dash=$array_datos_gestion[$id_formulario]['agentes'][$i];
            
            unset($productividad_agente_array);
            unset($productividad_agente_ajustada_array);
            unset($meta_agente_array);
            unset($gestiones_agente_array);

            $productividad_agente_array=array();
            $productividad_agente_ajustada_array=array();
            $meta_agente_array=array();
            $gestiones_agente_array=array();

            for ($j=0; $j < count($array_datos_gestion[$id_formulario]['gestion_agente_fecha_lista'][$id_agente_dash]); $j++) { 
              $fecha_agente=$array_datos_gestion[$id_formulario]['gestion_agente_fecha_lista'][$id_agente_dash][$j];
              $productividad_agente_fecha=maximo100(($array_datos_gestion[$id_formulario]['gestion_agente_fecha'][$id_agente_dash][$fecha_agente]*100)/$array_metas[$id_formulario]['meta'][$fecha_agente]);
              $productividad_agente_array[]=$productividad_agente_fecha;
              $meta_agente_array[]=$array_metas[$id_formulario]['meta'][$fecha_agente];
              $gestiones_agente_array[]=$array_datos_gestion[$id_formulario]['gestion_agente_fecha'][$id_agente_dash][$fecha_agente];
              $gestiones_hora_array[]=$array_datos_gestion[$id_formulario]['gestion_agente_fecha'][$id_agente_dash][$fecha_agente]/8;

              //SELECCIÓN DE PRODUCTIVIDAD DIARIA ENTRE CALCULADA Y AJUSTADA
              $productividad_agente_ajustada_fecha=$array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash][$fecha_agente]['ajustada'];

              if ($productividad_agente_ajustada_fecha<$productividad_agente_fecha) {
                $productividad_agente_ajustada_fecha=$productividad_agente_fecha;
              }

              $productividad_agente_ajustada_array[]=$productividad_agente_ajustada_fecha;


            }
            
            if (count($productividad_agente_array)>0) {
              $productividad_agente_total=array_sum($productividad_agente_array)/count($productividad_agente_array);
            } else {
              $productividad_agente_total=0;
            }
              
            $productividad_agente=number_format($productividad_agente_total, 2, '.', '');
            
            if ($productividad_agente>=100) {
              $color_progress='bg-success';
            } elseif ($productividad_agente>=90) {
              $productividad_control=true;
              $color_progress='bg-warning';
            } else {
              $color_progress='bg-danger';
              $productividad_control=true;
            }

            if (count($meta_agente_array)>0) {
              $meta_agente_total=array_sum($meta_agente_array)/count($meta_agente_array);
            } else {
              $meta_agente_total=0;
            }
              
            $meta_agente=round($meta_agente_total);

            if (count($gestiones_agente_array)>0) {
              $gestiones_agente_total=array_sum($gestiones_agente_array)/count($gestiones_agente_array);
            } else {
              $gestiones_agente_total=0;
            }
              
            $gestiones_agente=round($gestiones_agente_total);
            
            if (count($gestiones_hora_array)>0) {
              $hora_agente_total=array_sum($gestiones_hora_array)/count($gestiones_hora_array);
            } else {
              $hora_agente_total=0;
            }
              
            $hora_agente=round($hora_agente_total);

            $total_no_reporta=0;
            for ($j=8; $j < 18; $j++) { 
              if ($array_datos_gestion[$id_formulario]['gestion_agente_hora'][$id_agente_dash][$j]==0) {
                $total_no_reporta++;
              }
            }

            if (count($productividad_agente_ajustada_array)>0) {
              $productividad_agente_ajustada_total=array_sum($productividad_agente_ajustada_array)/count($productividad_agente_ajustada_array);
            } else {
              $productividad_agente_ajustada_total=0;
            }
              
            if ($productividad_agente_ajustada_total<$productividad_agente_total) {
              $productividad_agente_ajustada_total=$productividad_agente_total;
            }

            $productividad_agente_ajustada=number_format($productividad_agente_ajustada_total, 2, '.', '');

            if ($productividad_agente_ajustada==100) {
              $color_progress_ajustada='bg-success';
            } elseif ($productividad_agente_ajustada>=90) {
              $color_progress_ajustada='bg-warning';
            } else {
              $color_progress_ajustada='bg-danger';
            }









            // $total_productividad_agente+=$productividad_agente;
            // $total_productividad_agente_ajustada+=$productividad_agente_ajustada;

            // if($productividad_agente_ajustada>0) {
            //   $total_productividad+=$productividad_agente_ajustada;
            // } else {
            //   $total_productividad+=$productividad_agente;
            // }

            

          ?>
          <tr>
            <td class="p-1 font-size-11 text-start"><?php echo $array_coordinador_datos[$array_datos_agente[$id_agente_dash]['coordinador']]['nombre']; ?></td>
            <td class="p-1 font-size-11 text-start"><?php echo $array_datos_agente[$id_agente_dash]['nombre']; ?></td>
            <td class="p-1 font-size-11">
              <div class="progress" style="height: 14px;">
                <div class="progress-bar <?php echo $color_progress; ?>" role="progressbar" style="width: <?php echo $productividad_agente; ?>%;" aria-valuenow="<?php echo $productividad_agente; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $productividad_agente; ?>%</div>
              </div>
            </td>
            <td class="p-1 font-size-11 text-start">
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
            <td class="p-1 font-size-11 text-center"><?php echo $meta_agente; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo count($array_datos_gestion[$id_formulario]['gestion_agente_fecha'][$id_agente_dash]); ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $gestiones_agente; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $hora_agente; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $total_no_reporta; ?></td>
            <!-- <td class="p-1 font-size-11 text-center"><?php echo $array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['tipologia']; ?></td> -->
            <!-- <td class="p-1 font-size-11 text-center"><?php echo $array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['novedad']; ?></td> -->
          </tr>
          <?php endfor; ?>
        </tbody>
        <?php
        // if (count($array_datos_gestion[$id_formulario]['gestion_agente']['id'])>0) {
        //   $total_productividad_agente=$total_productividad_agente/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
        //   $total_productividad_agente_ajustada=$total_productividad_agente_ajustada/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
        // } else {
        //   $total_productividad_agente=0;
        //   $total_productividad_agente_ajustada=0;
        // }
        ?>
        <thead>
          <!-- <tr>
            <th class="p-1 font-size-11 text-end" colspan="2">Total</th>
            <th class="p-1 font-size-11">
              <div class="progress" style="height: 14px;">
                <div class="progress-bar <?php echo $color_progress; ?>" role="progressbar" style="width: <?php echo $total_productividad_agente; ?>%;" aria-valuenow="<?php echo $total_productividad_agente; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($total_productividad_agente, 2, '.', ''); ?>%</div>
              </div>
            </th>
            <th class="p-1 font-size-11 text-start">
              <?php if($total_productividad_agente_ajustada>0): ?>
                <div class="progress" style="height: 14px;">
                  <div class="progress-bar <?php echo $color_progress_ajustada; ?>" role="progressbar" style="width: <?php echo $total_productividad_agente_ajustada; ?>%;" aria-valuenow="<?php echo $total_productividad_agente_ajustada; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($total_productividad_agente_ajustada, 2, '.', ''); ?>%</div>
                </div>
              <?php else: ?>
                <div class="progress" style="height: 14px;">
                  <div class="progress-bar <?php echo $color_progress; ?>" role="progressbar" style="width: <?php echo $total_productividad_agente; ?>%;" aria-valuenow="<?php echo $total_productividad_agente; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($total_productividad_agente, 2, '.', ''); ?>%</div>
                </div>
              <?php endif; ?>
            </th>
            <th class="p-1 font-size-11 text-center"></th>
            <th class="p-1 font-size-11 text-center"></th>
            <th class="p-1 font-size-11 text-center"></th>
            <th class="p-1 font-size-11 text-center"></th>
            <th class="p-1 font-size-11 text-center"></th>
            <th class="p-1 font-size-11 text-center"></th>
            <th class="p-1 font-size-11 text-center"></th>
          </tr> -->
        </thead>
      <?php endif; ?>
    </table>
    <?php if(isset($array_datos_gestion[$id_formulario]['agentes'])): ?>
      <?php if(count($array_datos_gestion[$id_formulario]['agentes'])==0): ?>
        <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>