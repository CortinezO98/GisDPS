<div class="tab-pane fade mt-0" id="p6" role="tabpanel" aria-labelledby="p6"> 
  <div class="row">
    <div class="col-lg-12 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body py-2">
              <div class="row">
                <div class="col-md-12 mb-2">
                  <p class="alert background-principal color-blanco py-1 px-2 my-0">6. Formato de Gestión de Peticiones JeA</p>
                </div>
                <div class="col-md-12">
                  <div class="statistics-details d-md-flex d-sm-block align-items-center justify-content-between my-0">
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-list-check"></span><br><?php echo $array_datos_total['p6']['total_gestion']; ?></h3>
                      <p class="statistics-title text-center">Total Gestiones</p>
                    </div>
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-calendar-alt"></span><br><?php echo $array_datos_total['p6']['total_dias_gestion']; ?></h3>
                      <p class="statistics-title text-center">Días Gestión</p>
                    </div>
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-calendar-day"></span><br><?php echo number_format($array_datos_total['p6']['promedio_diario'], 0, ',', '.'); ?></h3>
                      <p class="statistics-title text-center">Promedio Diario</p>
                    </div>
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-users"></span><br><?php echo $array_datos_total['p6']['total_agente']; ?></h3>
                      <p class="statistics-title text-center"># Agentes</p>
                    </div>
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-user-cog"></span><br><?php echo number_format($array_datos_total['p6']['promedio_agente'], 0, ',', '.'); ?></h3>
                      <p class="statistics-title text-center">Promedio Agente</p>
                    </div>
                  </div>
                </div>
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
                  <h6 class="fw-bold card-title-dash">Gestión diaria</h6>
                </div>
              </div>
              <div class="col-md-12">
                <div id="p6_g1"></div>
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
                  <h6 class="fw-bold card-title-dash">Gestión general por agente</h6>
                </div>
              </div>
              <div class="col-md-12">
                <div id="p6_g2"></div>
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
                <div id="p6_g3"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body">
              <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                  <h6 class="fw-bold card-title-dash">Gestión por departamento</h6>
                </div>
              </div>
              <div class="col-md-12">
                <div id="p6_g4"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>
<script type="text/javascript">
  Highcharts.chart('p6_g1', {
      chart: {
          type: 'spline',
          height: '250px'
      },
      title: {
          text: null
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: [
            <?php for ($i=0; $i < count($array_dias_mes); $i++): ?>
              '<?php echo date('d/m/Y', strtotime($array_dias_mes[$i])); ?>',
            <?php endfor; ?>
          ],
          crosshair: true
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Gestiones'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          column: {
              pointPadding: 0.2,
              borderWidth: 0,
              dataLabels: {
                  enabled: true,
                  style: {
                      fontSize: '8px'
                  },
                  format: '{point.y}'
              }
          }
      },
      legend: false,
      credits: {
          enabled: false
      },
      series: [{
          name: 'Gestiones',
          data: [
            <?php for ($i=0; $i < count($array_dias_mes); $i++): ?>
              <?php if(isset($array_datos_gestiones['p6']['gestion_diaria'][$array_dias_mes[$i]])): ?>
                <?php echo $array_datos_gestiones['p6']['gestion_diaria'][$array_dias_mes[$i]]; ?>,
              <?php else: ?>
                0,
              <?php endif; ?>
            <?php endfor; ?>
          ]

      }]
  });

  Highcharts.chart('p6_g2', {
      chart: {
          type: 'bar',
          height: '<?php echo (isset($array_datos_gestion['p6']['gestion_agente']['id'])) ? (count($array_datos_gestion['p6']['gestion_agente']['id'])>2) ? count($array_datos_gestion['p6']['gestion_agente']['id'])*40 : '150' : '150'; ?> px'
      },
      title: {
          text: null
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: [
            <?php if(isset($array_datos_gestion['p6']['gestion_agente']['id'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p6']['gestion_agente']['id']); $i++): ?>
                '<?php echo $array_datos_gestion['p6']['gestion_agente'][$array_datos_gestion['p6']['gestion_agente']['id'][$i]]['nombre']; ?>',
              <?php endfor; ?>
            <?php endif; ?>
          ],
          crosshair: true
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Gestiones'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          bar: {
              pointPadding: 0.2,
              borderWidth: 0,
              dataLabels: {
                  enabled: true,
                  style: {
                      fontSize: '8px'
                  },
                  format: '{point.y}'
              }
          }
      },
      legend: false,
      credits: {
          enabled: false
      },
      series: [{
          name: 'Gestiones',
          data: [
            <?php if(isset($array_datos_gestion['p6']['gestion_agente']['id'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p6']['gestion_agente']['id']); $i++): ?>
                <?php if(isset($array_datos_gestion['p6']['gestion_agente'][$array_datos_gestion['p6']['gestion_agente']['id'][$i]]['total'])): ?>
                  <?php echo $array_datos_gestion['p6']['gestion_agente'][$array_datos_gestion['p6']['gestion_agente']['id'][$i]]['total']; ?>,
                <?php else: ?>
                  0,
                <?php endif; ?>
              <?php endfor; ?>
            <?php endif; ?>
          ]

      }]
  });

  Highcharts.chart('p6_g3', {
      chart: {
          type: 'spline',
          height: '350px'
      },
      title: {
          text: null
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: [
            <?php for ($i=0; $i < count($array_anio_mes_hora_num); $i++): ?>
              '<?php echo $array_anio_mes_hora_num[$i]; ?>',
            <?php endfor; ?>
          ],
          crosshair: true
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Gestiones'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          column: {
              pointPadding: 0.2,
              borderWidth: 0,
              dataLabels: {
                  enabled: true,
                  style: {
                      fontSize: '8px'
                  },
                  format: '{point.y}'
              }
          }
      },
      legend: {
        layout: "horizontal",
        align: "center",
        verticalAlign: "bottom",
        itemStyle: {
          color: '#000000',
          fontWeight: 'normal',
          fontSize: 11,
        },
      },
      credits: {
          enabled: false
      },
      series: [
        <?php if(isset($array_datos_gestion['p6']['gestion_agente']['id'])): ?>
          <?php for ($i=0; $i < count($array_datos_gestion['p6']['gestion_agente']['id']); $i++): ?>
          {
              name: '<?php echo $array_datos_gestion['p6']['gestion_agente'][$array_datos_gestion['p6']['gestion_agente']['id'][$i]]['nombre']; ?>',
              data: [
                <?php for ($j=0; $j < count($array_anio_mes_hora_num); $j++): ?>
                  <?php if(isset($array_datos_gestion['p6']['gestion_agente'][$array_datos_gestion['p6']['gestion_agente']['id'][$i]]['hora'][$j])): ?>
                    <?php echo $array_datos_gestion['p6']['gestion_agente'][$array_datos_gestion['p6']['gestion_agente']['id'][$i]]['hora'][$j]; ?>,
                  <?php else: ?>
                    0,
                  <?php endif; ?>
                <?php endfor; ?>
              ]
          },
          <?php endfor; ?>
        <?php endif; ?>
      ]
  });

  <?php
    $array_mapa_co_sa = ($array_mapa_p6['co-sa']>0) ? $array_mapa_p6['co-sa'] : null;
    $array_mapa_co_ca = ($array_mapa_p6['co-ca']>0) ? $array_mapa_p6['co-ca'] : null;
    $array_mapa_co_na = ($array_mapa_p6['co-na']>0) ? $array_mapa_p6['co-na'] : null;
    $array_mapa_co_ch = ($array_mapa_p6['co-ch']>0) ? $array_mapa_p6['co-ch'] : null;
    $array_mapa_co_to = ($array_mapa_p6['co-to']>0) ? $array_mapa_p6['co-to'] : null;
    $array_mapa_co_cq = ($array_mapa_p6['co-cq']>0) ? $array_mapa_p6['co-cq'] : null;
    $array_mapa_co_hu = ($array_mapa_p6['co-hu']>0) ? $array_mapa_p6['co-hu'] : null;
    $array_mapa_co_pu = ($array_mapa_p6['co-pu']>0) ? $array_mapa_p6['co-pu'] : null;
    $array_mapa_co_am = ($array_mapa_p6['co-am']>0) ? $array_mapa_p6['co-am'] : null;
    $array_mapa_co_bl = ($array_mapa_p6['co-bl']>0) ? $array_mapa_p6['co-bl'] : null;
    $array_mapa_co_vc = ($array_mapa_p6['co-vc']>0) ? $array_mapa_p6['co-vc'] : null;
    $array_mapa_co_su = ($array_mapa_p6['co-su']>0) ? $array_mapa_p6['co-su'] : null;
    $array_mapa_co_at = ($array_mapa_p6['co-at']>0) ? $array_mapa_p6['co-at'] : null;
    $array_mapa_co_ce = ($array_mapa_p6['co-ce']>0) ? $array_mapa_p6['co-ce'] : null;
    $array_mapa_co_lg = ($array_mapa_p6['co-lg']>0) ? $array_mapa_p6['co-lg'] : null;
    $array_mapa_co_ma = ($array_mapa_p6['co-ma']>0) ? $array_mapa_p6['co-ma'] : null;
    $array_mapa_co_ar = ($array_mapa_p6['co-ar']>0) ? $array_mapa_p6['co-ar'] : null;
    $array_mapa_co_ns = ($array_mapa_p6['co-ns']>0) ? $array_mapa_p6['co-ns'] : null;
    $array_mapa_co_cs = ($array_mapa_p6['co-cs']>0) ? $array_mapa_p6['co-cs'] : null;
    $array_mapa_co_gv = ($array_mapa_p6['co-gv']>0) ? $array_mapa_p6['co-gv'] : null;
    $array_mapa_co_me = ($array_mapa_p6['co-me']>0) ? $array_mapa_p6['co-me'] : null;
    $array_mapa_co_vp = ($array_mapa_p6['co-vp']>0) ? $array_mapa_p6['co-vp'] : null;
    $array_mapa_co_vd = ($array_mapa_p6['co-vd']>0) ? $array_mapa_p6['co-vd'] : null;
    $array_mapa_co_an = ($array_mapa_p6['co-an']>0) ? $array_mapa_p6['co-an'] : null;
    $array_mapa_co_co = ($array_mapa_p6['co-co']>0) ? $array_mapa_p6['co-co'] : null;
    $array_mapa_co_by = ($array_mapa_p6['co-by']>0) ? $array_mapa_p6['co-by'] : null;
    $array_mapa_co_st = ($array_mapa_p6['co-st']>0) ? $array_mapa_p6['co-st'] : null;
    $array_mapa_co_cl = ($array_mapa_p6['co-cl']>0) ? $array_mapa_p6['co-cl'] : null;
    $array_mapa_co_cu = ($array_mapa_p6['co-cu']>0) ? $array_mapa_p6['co-cu'] : null;
    $array_mapa_co_1136 = ($array_mapa_p6['co-1136']>0) ? $array_mapa_p6['co-1136'] : null;
    $array_mapa_co_ri = ($array_mapa_p6['co-ri']>0) ? $array_mapa_p6['co-ri'] : null;
    $array_mapa_co_qd = ($array_mapa_p6['co-qd']>0) ? $array_mapa_p6['co-qd'] : null;
    $array_mapa_co_gn = ($array_mapa_p6['co-gn']>0) ? $array_mapa_p6['co-gn'] : null;
  ?>
  var data_mapa = [
      ['co-sa', <?php echo $array_mapa_co_sa; ?>],
      ['co-ca', <?php echo $array_mapa_co_ca; ?>],
      ['co-na', <?php echo $array_mapa_co_na; ?>],
      ['co-ch', <?php echo $array_mapa_co_ch; ?>],
      ['co-to', <?php echo $array_mapa_co_to; ?>],
      ['co-cq', <?php echo $array_mapa_co_cq; ?>],
      ['co-hu', <?php echo $array_mapa_co_hu; ?>],
      ['co-pu', <?php echo $array_mapa_co_pu; ?>],
      ['co-am', <?php echo $array_mapa_co_am; ?>],
      ['co-bl', <?php echo $array_mapa_co_bl; ?>],
      ['co-vc', <?php echo $array_mapa_co_vc; ?>],
      ['co-su', <?php echo $array_mapa_co_su; ?>],
      ['co-at', <?php echo $array_mapa_co_at; ?>],
      ['co-ce', <?php echo $array_mapa_co_ce; ?>],
      ['co-lg', <?php echo $array_mapa_co_lg; ?>],
      ['co-ma', <?php echo $array_mapa_co_ma; ?>],
      ['co-ar', <?php echo $array_mapa_co_ar; ?>],
      ['co-ns', <?php echo $array_mapa_co_ns; ?>],
      ['co-cs', <?php echo $array_mapa_co_cs; ?>],
      ['co-gv', <?php echo $array_mapa_co_gv; ?>],
      ['co-me', <?php echo $array_mapa_co_me; ?>],
      ['co-vp', <?php echo $array_mapa_co_vp; ?>],
      ['co-vd', <?php echo $array_mapa_co_vd; ?>],
      ['co-an', <?php echo $array_mapa_co_an; ?>],
      ['co-co', <?php echo $array_mapa_co_co; ?>],
      ['co-by', <?php echo $array_mapa_co_by; ?>],
      ['co-st', <?php echo $array_mapa_co_st; ?>],
      ['co-cl', <?php echo $array_mapa_co_cl; ?>],
      ['co-cu', <?php echo $array_mapa_co_cu; ?>],
      ['co-1136', <?php echo $array_mapa_co_1136; ?>],
      ['co-ri', <?php echo $array_mapa_co_ri; ?>],
      ['co-qd', <?php echo $array_mapa_co_qd; ?>],
      ['co-gn', <?php echo $array_mapa_co_gn; ?>]
  ];

  // Create the chart
  Highcharts.mapChart('p6_g4', {
      chart: {
          map: 'countries/co/co-all',
          marginTop: 10
      },
      title: {
          text: null,
          style: {
              fontSize: '14px'
          }
      },
      subtitle: {
          text: null
      },
      credits: {
           enabled: false
      },
      mapNavigation: {
          enabled: true,
          buttonOptions: {
              verticalAlign: 'bottom'
          }
      },
      tooltip: {
          pointFormat: '{point.name}: <b>{point.value}</b>'
      },
      colorAxis: {
          min: 1,
          max: 100,
          type: 'logarithmic',
          minColor: '#FFC300',
          maxColor: '#C70039',
          lineWidth: 0
      },
      series: [{
          data: data_mapa,
          name: 'Departamentos',
          states: {
              hover: {
                  color: '#BADA55'
              }
          },
          dataLabels: {
              enabled: true,
              style: {
                  fontWeight: 'normal',
                  fontSize: '10px',
              },
              format: '{point.name}'
          }
      }]
  });
</script>