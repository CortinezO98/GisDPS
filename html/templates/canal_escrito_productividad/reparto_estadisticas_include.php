<div class="col-md-12">
  <?php
    $total_productividad=0;
    $productividad_control=false;
    $productividad_total=0;
    $productividad_agente_ajustada_resumen=0;

    if (isset($usuariosPorFormulario[$id_formulario])) {
      for ($i=0; $i < count($usuariosPorFormulario[$id_formulario]); $i++) {
        $id_agente_dash=$usuariosPorFormulario[$id_formulario][$i];
        
        $productividad_agente_total=$cumplimientoPorFormularioUsuarioResumen[$id_formulario][$id_agente_dash];
        $productividad_total+=$productividad_agente_total;

        if ($productividad_agente_total<100) {
          $productividad_control=true;
        }

        if (isset($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])) {
          if (count($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])==0) {
            $productividad_agente_ajustada_total=0;
          } else {
            $productividad_agente_ajustada_total=array_sum($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])/count($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada']);
          }
        } else {
          $productividad_agente_ajustada_total=0;
        }
          
        if ($productividad_agente_ajustada_total<$productividad_agente_total) {
          $productividad_agente_ajustada_total=$productividad_agente_total;
        }

        $productividad_agente_ajustada_resumen+=$productividad_agente_ajustada_total;
      }
      
      if (count($usuariosPorFormulario[$id_formulario])>0) {
        $productividad_total=number_format($productividad_total/count($usuariosPorFormulario[$id_formulario]), 2, '.', '');
        $productividad_agente_ajustada_resumen=number_format($productividad_agente_ajustada_resumen/count($usuariosPorFormulario[$id_formulario]), 2, '.', '');
      } else {
        $productividad_total=0;
        $productividad_agente_ajustada_resumen=0;
      }

      if ($productividad_total>=100) {
        $color_progress_total='bg-success';
        $productividad_total=100;
      } elseif ($productividad_total>=90) {
        $color_progress_total='bg-warning';
      } else {
        $color_progress_total='bg-danger';
      }


      if ($productividad_agente_ajustada_resumen==100) {
        $color_progress_ajustada_total='bg-success';
      } elseif ($productividad_agente_ajustada_resumen>=90) {
        $color_progress_ajustada_total='bg-warning';
      } else {
        $color_progress_ajustada_total='bg-danger';
      }
    }
  ?>
  <?php if($productividad_control AND $_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']==date('Y-m-d', strtotime($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_fin'])) AND ($permisos_usuario=="Administrador" OR $permisos_usuario=="Gestor")): ?>
    <a href="productividad_justificar?grupo=<?php echo base64_encode('reparto'); ?>&formulario=<?php echo base64_encode($id_formulario); ?>&fecha=<?php echo base64_encode($_SESSION[APP_SESSION.'_session_ce_productividad']['fecha_inicio']); ?>" class="btn py-2 px-2 btn-danger mb-1"><i class="fas fa-flag font-size-11"></i> Justificar</a>
  <?php endif; ?>
  <div class="row">
    <div class="col-md-6">
      <b>Productividad General</b>
      <div class="progress my-1" style="height: 30px;">
        <div class="progress-bar <?php echo $color_progress_total; ?>" role="progressbar" style="width: <?php echo $productividad_total; ?>%;" aria-valuenow="<?php echo $productividad_total; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($productividad_total, 2, '.', ''); ?>%</div>
      </div>
    </div>
    <div class="col-md-6">
      <b>Productividad General Ajustada</b>
      <div class="progress my-1" style="height: 30px;">
        <div class="progress-bar <?php echo $color_progress_ajustada_total; ?>" role="progressbar" style="width: <?php echo $productividad_agente_ajustada_resumen; ?>%;" aria-valuenow="<?php echo $productividad_agente_ajustada_resumen; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($productividad_agente_ajustada_resumen, 2, '.', ''); ?>%</div>
      </div>
    </div>
  </div>

  <div class="table-responsive table-fixed" id="headerFixTable">
    <table class="table table-hover table-bordered table-striped">
      <thead>
        <tr>
          <th class="px-1 py-2">Coordinador</th>
          <th class="px-1 py-2">Agente</th>
          <th class="px-1 py-2" style="width: 150px;">Productividad</th>
          <th class="px-1 py-2" style="width: 150px;">Productividad Ajustada</th>
          <th class="px-1 py-2 text-center">Promedio Meta Diaria</th>
          <th class="px-1 py-2 text-center">Días</th>
          <th class="px-1 py-2 text-center">Promedio Gestiones Diaria</th>
          <th class="px-1 py-2 text-center">Promedio Casos Hora</th>
          <th class="px-1 py-2 text-center">Horas No Cargue</th>
          <th class="px-1 py-2">Tipología</th>
          <th class="px-1 py-2">Novedad</th>
        </tr>
      </thead>
      <?php if(isset($usuariosPorFormulario[$id_formulario])): ?>
        <tbody>
          <?php for ($i=0; $i < count($usuariosPorFormulario[$id_formulario]); $i++): ?>
          <?php
            $id_agente_dash=$usuariosPorFormulario[$id_formulario][$i];

            $id_coordinador=$usuarioscoordinadorArray[$id_agente_dash];
            $nombre_coordinador=$nombresCoordinadoresArray[$id_coordinador];
            $nombre_agente=$nombresUsuariosArray[$id_agente_dash];
            
            $productividad_agente=number_format($cumplimientoPorFormularioUsuarioResumen[$id_formulario][$id_agente_dash], 2, '.', '');

            if ($productividad_agente>=100) {
              $color_progress='bg-success';
            } elseif ($productividad_agente>=90) {
              $productividad_control=true;
              $color_progress='bg-warning';
            } else {
              $color_progress='bg-danger';
              $productividad_control=true;
            }

            $meta_agente_array=$array_metas[$id_formulario]['meta'];

            if (count($meta_agente_array)>0) {
              $meta_agente_total=array_sum($meta_agente_array)/count($meta_agente_array);
            } else {
              $meta_agente_total=0;
            }
              
            $meta_agente=round($meta_agente_total);

            $total_no_reporta=0;
            for ($j=8; $j < 18; $j++) { 
              if ($sumaCantidadPorFormularioUsuarioHora[$id_formulario][$id_agente_dash][$j]==0) {
                $total_no_reporta++;
              }
            }

            
            if (isset($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])) {
              if (count($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])==0) {
                $productividad_agente_ajustada_total=0;
              } else {
                $productividad_agente_ajustada_total=number_format(array_sum($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada'])/count($array_datos_justificacion[$id_formulario][$id_agente_dash]['ajustada']), 2, '.', '');
              }
            } else {
              $productividad_agente_ajustada_total=0;
            }
              
            if ($productividad_agente_ajustada_total<$productividad_agente) {
              $productividad_agente_ajustada_total=$productividad_agente;
            }

            $productividad_agente_ajustada=$productividad_agente_ajustada_total;

            if ($productividad_agente_ajustada==100) {
              $color_progress_ajustada='bg-success';
            } elseif ($productividad_agente_ajustada>=90) {
              $color_progress_ajustada='bg-warning';
            } else {
              $color_progress_ajustada='bg-danger';
            }

            $dias_laborados=count($cumplimientoPorFormularioUsuarioFecha[$id_formulario][$id_agente_dash]);
            $promedio_gestiones_dia=round(array_sum($sumaCantidadPorFormularioUsuarioFecha[$id_formulario][$id_agente_dash])/$dias_laborados);

            if (!isset($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['tipologia'])) {
              $array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['tipologia']=array();
            }

            if (count($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['tipologia'])>0) {
              $tipología_agente=implode(';', array_values(array_unique($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['tipologia'])));
            } else {
              $tipología_agente='';
            }

            if (!isset($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['novedad'])) {
              $array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['novedad']=array();
            }

            if (count($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['novedad'])>0) {
              $novedad_agente=implode(';', array_values(array_unique($array_datos_justificacion[$id_formulario]['gestion_agente'][$id_agente_dash]['novedad'])));
            } else {
              $novedad_agente='';
            }
          ?>
          <tr>
            <td class="p-1 font-size-11 text-start"><?php echo $nombre_coordinador; ?></td>
            <td class="p-1 font-size-11 text-start"><?php echo $nombre_agente; ?></td>
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
            <td class="p-1 font-size-11 text-center"><?php echo $dias_laborados; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $promedio_gestiones_dia; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $cumplimientoPorFormularioUsuarioHora[$id_formulario][$id_agente_dash]; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $total_no_reporta; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $tipología_agente; ?></td>
            <td class="p-1 font-size-11 text-center"><?php echo $novedad_agente; ?></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      <?php endif; ?>
    </table>
    <?php if(isset($usuariosPorFormulario[$id_formulario])): ?>
      <?php if(count($usuariosPorFormulario[$id_formulario])==0): ?>
        <p class="alert alert-dark p-1">¡No se encontraron registros!</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>