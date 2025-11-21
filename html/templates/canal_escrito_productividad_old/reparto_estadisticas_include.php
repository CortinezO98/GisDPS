<div class="col-md-12">
  <?php
    $total_productividad=0;
    $productividad_control=false;
    for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++){
      $id_agente_dash=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
      
      $productividad_agente=number_format($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['porcentaje'], 2, '.', '');
      $productividad_agente_ajustada=number_format($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['ajustada'], 2, '.', '');

      if ($productividad_agente_ajustada<$productividad_agente) {
        $productividad_agente_ajustada=$productividad_agente;
      }

      if($productividad_agente_ajustada>0) {
        $total_productividad+=$productividad_agente_ajustada;
      } else {
        $total_productividad+=$productividad_agente;
      }

      if ($productividad_agente==100) {

      } elseif ($productividad_agente>=90) {
        $productividad_control=true;
      } else {
        $productividad_control=true;
      }
    }

    if (count($array_datos_gestion[$id_formulario]['gestion_agente']['id'])>0) {
      $total_productividad_consolidada=$total_productividad/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
    } else {
      $total_productividad_consolidada=0;
    }

    if ($total_productividad_consolidada==100) {
      $color_progress_total='bg-success';
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
          <th class="px-1 py-2" style="width: 150px;">Productividad</th>
          <th class="px-1 py-2" style="width: 150px;">Productividad Ajustada</th>
          <th class="px-1 py-2 text-center">Meta</th>
          <th class="px-1 py-2 text-center">Días</th>
          <th class="px-1 py-2 text-center">Gestiones</th>
          <th class="px-1 py-2 text-center">Casos Hora</th>
          <th class="px-1 py-2 text-center">Horas No Cargue</th>
          <th class="px-1 py-2">Tipología</th>
          <th class="px-1 py-2">Novedad</th>
        </tr>
      </thead>
      <?php if(isset($array_datos_gestion[$id_formulario]['gestion_agente']['id'])): ?>
        <tbody>
          <?php
            $total_productividad=0;
            $total_productividad_agente=0;
            $total_productividad_agente_consolidada=0;
            $total_productividad_agente_ajustada=0;
            $total_productividad_agente_ajustada_consolidada=0;
          ?>
          <?php for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++): ?>
          <?php
            $id_agente_dash=$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i];
            $productividad_agente=number_format($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['porcentaje'], 2, '.', '');
            if ($productividad_agente==100) {
              $color_progress='bg-success';
            } elseif ($productividad_agente>=90) {
              $productividad_control=true;
              $color_progress='bg-warning';
            } else {
              $color_progress='bg-danger';
              $productividad_control=true;
            }

            $productividad_agente_ajustada=number_format($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['ajustada'], 2, '.', '');

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

            $total_productividad_agente+=$productividad_agente;
            $total_productividad_agente_ajustada+=$productividad_agente_ajustada;

            if($productividad_agente_ajustada>0) {
              $total_productividad+=$productividad_agente_ajustada;
            } else {
              $total_productividad+=$productividad_agente;
            }

            $total_horas_agente=count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['fecha_conteo'])*8;
            $total_realizado_agente=$array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['total'];

            if ($total_horas_agente>0) {
              $total_hora_agente=round($total_realizado_agente/$total_horas_agente);
            } else {
              $total_hora_agente=0;
            }

            $total_no_reporta=0;
            for ($j=8; $j < 18; $j++) { 
              if ($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['hora'][$j]==0) {
                $total_no_reporta++;
              }
            }

          ?>
          <tr>
            <td class="p-1 font-size-11 text-start"><?php echo $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['coordinador']; ?></td>
            <td class="p-1 font-size-11 text-start"><?php echo $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['nombre']; ?></td>
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
            <td class="p-1 font-size-11 text-center"><?php echo $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['meta_calculada']; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo count($array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['fecha_conteo']); ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['total']; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $total_hora_agente; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $total_no_reporta; ?></td>
            <td class="p-1 font-size-11 text-start"><?php echo $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['tipologia']; ?></td>
            <td class="p-1 font-size-11 text-start"><?php echo $array_datos_gestion[$id_formulario]['gestion_agente'][$id_agente_dash]['novedad']; ?></td>
          </tr>
          <?php endfor; ?>
        </tbody>
        <?php
        if (count($array_datos_gestion[$id_formulario]['gestion_agente']['id'])>0) {
          $total_productividad_agente=$total_productividad_agente/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
          $total_productividad_agente_ajustada=$total_productividad_agente_ajustada/count($array_datos_gestion[$id_formulario]['gestion_agente']['id']);
        } else {
          $total_productividad_agente=0;
          $total_productividad_agente_ajustada=0;
        }
        ?>
        <thead>
          <tr>
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
          </tr>
        </thead>
      <?php endif; ?>
    </table>
    <?php if(isset($array_datos_gestion[$id_formulario]['gestion_agente']['id'])): ?>
      <?php if(count($array_datos_gestion[$id_formulario]['gestion_agente']['id'])==0): ?>
        <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>