<div class="tab-pane fade mt-0" id="p14" role="tabpanel" aria-labelledby="p14"> 
  <div class="row">
    <div class="col-lg-12 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body py-2">
              <div class="row">
                <div class="col-md-12 mb-2">
                  <p class="alert background-principal color-blanco py-1 px-2 my-0">14. Seguimiento Inspección Tipificación</p>
                </div>
                <div class="col-md-12">
                  <div class="statistics-details d-md-flex d-sm-block align-items-center justify-content-between my-0">
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-list-check"></span><br><?php echo $array_datos_total['p14']['total_gestion']; ?></h3>
                      <p class="statistics-title text-center">Total Gestiones</p>
                    </div>
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-calendar-alt"></span><br><?php echo $array_datos_total['p14']['total_dias_gestion']; ?></h3>
                      <p class="statistics-title text-center">Días Gestión</p>
                    </div>
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-calendar-day"></span><br><?php echo number_format($array_datos_total['p14']['promedio_diario'], 0, ',', '.'); ?></h3>
                      <p class="statistics-title text-center">Promedio Diario</p>
                    </div>
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-users"></span><br><?php echo $array_datos_total['p14']['total_agente']; ?></h3>
                      <p class="statistics-title text-center"># Agentes</p>
                    </div>
                    <div>
                      <h3 class="rate-percentage text-success text-center"><span class="fas fa-user-cog"></span><br><?php echo number_format($array_datos_total['p14']['promedio_agente'], 0, ',', '.'); ?></h3>
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
                <div id="p14_g1"></div>
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
                <div id="p14_g2"></div>
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
                <div id="p14_g3"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body">
              <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                  <h6 class="fw-bold card-title-dash">7. Traslado errado entidades</h6>
                </div>
              </div>
              <div class="col-md-12">
                <div id="p14_g4"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body">
              <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                  <h6 class="fw-bold card-title-dash">8.1 Asignación P.S errada</h6>
                </div>
              </div>
              <div class="col-md-12">
                <div id="p14_g5"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body">
              <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                  <h6 class="fw-bold card-title-dash">9. Determina de forma correcta el tipo de petición</h6>
                </div>
              </div>
              <div class="col-md-12">
                <div id="p14_g6"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body">
              <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                  <h6 class="fw-bold card-title-dash">14. Relaciona de manera correcta los datos del campo información radicación</h6>
                </div>
              </div>
              <div class="col-md-12">
                <div id="p14_g7"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body">
              <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                  <h6 class="fw-bold card-title-dash">16. Diligencia de manera correcta los datos del solicitante</h6>
                </div>
              </div>
              <div class="col-md-12">
                <div id="p14_g8"></div>
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
                  <h6 class="fw-bold card-title-dash">8.2. Asignación P.S errada</h6>
                </div>
              </div>
              <div class="col-md-12">
                <div id="p14_g9"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>
<script type="text/javascript">
  Highcharts.chart('p14_g1', {
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
              <?php if(isset($array_datos_gestiones['p14']['gestion_diaria'][$array_dias_mes[$i]])): ?>
                <?php echo $array_datos_gestiones['p14']['gestion_diaria'][$array_dias_mes[$i]]; ?>,
              <?php else: ?>
                0,
              <?php endif; ?>
            <?php endfor; ?>
          ]

      }]
  });

  Highcharts.chart('p14_g2', {
      chart: {
          type: 'bar',
          height: '<?php echo (isset($array_datos_gestion['p14']['gestion_agente']['id'])) ? (count($array_datos_gestion['p14']['gestion_agente']['id'])>2) ? count($array_datos_gestion['p14']['gestion_agente']['id'])*40 : '150' : '150'; ?> px'
      },
      title: {
          text: null
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: [
            <?php if(isset($array_datos_gestion['p14']['gestion_agente']['id'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p14']['gestion_agente']['id']); $i++): ?>
                '<?php echo $array_datos_gestion['p14']['gestion_agente'][$array_datos_gestion['p14']['gestion_agente']['id'][$i]]['nombre']; ?>',
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
            <?php if(isset($array_datos_gestion['p14']['gestion_agente']['id'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p14']['gestion_agente']['id']); $i++): ?>
                <?php if(isset($array_datos_gestion['p14']['gestion_agente'][$array_datos_gestion['p14']['gestion_agente']['id'][$i]]['total'])): ?>
                  <?php echo $array_datos_gestion['p14']['gestion_agente'][$array_datos_gestion['p14']['gestion_agente']['id'][$i]]['total']; ?>,
                <?php else: ?>
                  0,
                <?php endif; ?>
              <?php endfor; ?>
            <?php endif; ?>
          ]

      }]
  });

  Highcharts.chart('p14_g3', {
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
        <?php if(isset($array_datos_gestion['p14']['gestion_agente']['id'])): ?>
          <?php for ($i=0; $i < count($array_datos_gestion['p14']['gestion_agente']['id']); $i++): ?>
          {
              name: '<?php echo $array_datos_gestion['p14']['gestion_agente'][$array_datos_gestion['p14']['gestion_agente']['id'][$i]]['nombre']; ?>',
              data: [
                <?php for ($j=0; $j < count($array_anio_mes_hora_num); $j++): ?>
                  <?php if(isset($array_datos_gestion['p14']['gestion_agente'][$array_datos_gestion['p14']['gestion_agente']['id'][$i]]['hora'][$j])): ?>
                    <?php echo $array_datos_gestion['p14']['gestion_agente'][$array_datos_gestion['p14']['gestion_agente']['id'][$i]]['hora'][$j]; ?>,
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

  Highcharts.chart('p14_g4', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie',
          height: 250
      },
      title: {
          text: null
      },
      tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      accessibility: {
          point: {
              valueSuffix: '%'
          }
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.percentage:.1f}% | {point.y}'
              }
          }
      },
      credits: {
          enabled: false
      },
      series: [{
          name: '7. Traslado errado entidades',
          colorByPoint: true,
          data: [
            <?php if(isset($array_datos_gestion['p14']['traslado_errado_entidades_lista'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p14']['traslado_errado_entidades_lista']); $i++): ?>
              {
                  name: '<?php echo $array_datos_gestion['p14']['traslado_errado_entidades_nombre'][$array_datos_gestion['p14']['traslado_errado_entidades_lista'][$i]]; ?>',
                  y: <?php echo $array_datos_gestion['p14']['traslado_errado_entidades'][$array_datos_gestion['p14']['traslado_errado_entidades_lista'][$i]]; ?>
              },
              <?php endfor; ?>
            <?php endif; ?>
          ]
      }]
  });

  Highcharts.chart('p14_g5', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie',
          height: 250
      },
      title: {
          text: null
      },
      tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      accessibility: {
          point: {
              valueSuffix: '%'
          }
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.percentage:.1f}% | {point.y}'
              }
          }
      },
      credits: {
          enabled: false
      },
      series: [{
          name: '8.1 Asignación P.S errada',
          colorByPoint: true,
          data: [
            <?php if(isset($array_datos_gestion['p14']['asignacion_ps_errada_lista'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p14']['asignacion_ps_errada_lista']); $i++): ?>
              {
                  name: '<?php echo $array_datos_gestion['p14']['asignacion_ps_errada_nombre'][$array_datos_gestion['p14']['asignacion_ps_errada_lista'][$i]]; ?>',
                  y: <?php echo $array_datos_gestion['p14']['asignacion_ps_errada'][$array_datos_gestion['p14']['asignacion_ps_errada_lista'][$i]]; ?>
              },
              <?php endfor; ?>
            <?php endif; ?>
          ]
      }]
  });

  Highcharts.chart('p14_g6', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie',
          height: 250
      },
      title: {
          text: null
      },
      tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      accessibility: {
          point: {
              valueSuffix: '%'
          }
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.percentage:.1f}% | {point.y}'
              }
          }
      },
      credits: {
          enabled: false
      },
      series: [{
          name: '9. Determina de forma correcta el tipo de petición',
          colorByPoint: true,
          data: [
            <?php if(isset($array_datos_gestion['p14']['forma_correcta_peticion_lista'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p14']['forma_correcta_peticion_lista']); $i++): ?>
              {
                  name: '<?php echo $array_datos_gestion['p14']['forma_correcta_peticion_nombre'][$array_datos_gestion['p14']['forma_correcta_peticion_lista'][$i]]; ?>',
                  y: <?php echo $array_datos_gestion['p14']['forma_correcta_peticion'][$array_datos_gestion['p14']['forma_correcta_peticion_lista'][$i]]; ?>
              },
              <?php endfor; ?>
            <?php endif; ?>
          ]
      }]
  });

  Highcharts.chart('p14_g7', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie',
          height: 250
      },
      title: {
          text: null
      },
      tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      accessibility: {
          point: {
              valueSuffix: '%'
          }
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.percentage:.1f}% | {point.y}'
              }
          }
      },
      credits: {
          enabled: false
      },
      series: [{
          name: '14. Relaciona de manera correcta los datos del campo información radicación',
          colorByPoint: true,
          data: [
            <?php if(isset($array_datos_gestion['p14']['relaciona_informacion_lista'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p14']['relaciona_informacion_lista']); $i++): ?>
              {
                  name: '<?php echo $array_datos_gestion['p14']['relaciona_informacion_nombre'][$array_datos_gestion['p14']['relaciona_informacion_lista'][$i]]; ?>',
                  y: <?php echo $array_datos_gestion['p14']['relaciona_informacion'][$array_datos_gestion['p14']['relaciona_informacion_lista'][$i]]; ?>
              },
              <?php endfor; ?>
            <?php endif; ?>
          ]
      }]
  });

  Highcharts.chart('p14_g8', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie',
          height: 250
      },
      title: {
          text: null
      },
      tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      accessibility: {
          point: {
              valueSuffix: '%'
          }
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.percentage:.1f}% | {point.y}'
              }
          }
      },
      credits: {
          enabled: false
      },
      series: [{
          name: '16. Diligencia de manera correcta los datos del solicitante',
          colorByPoint: true,
          data: [
            <?php if(isset($array_datos_gestion['p14']['diligencia_datos_lista'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p14']['diligencia_datos_lista']); $i++): ?>
              {
                  name: '<?php echo $array_datos_gestion['p14']['diligencia_datos_nombre'][$array_datos_gestion['p14']['diligencia_datos_lista'][$i]]; ?>',
                  y: <?php echo $array_datos_gestion['p14']['diligencia_datos'][$array_datos_gestion['p14']['diligencia_datos_lista'][$i]]; ?>
              },
              <?php endfor; ?>
            <?php endif; ?>
          ]
      }]
  });

  Highcharts.chart('p14_g9', {
      chart: {
          type: 'bar',
          height: '<?php echo (isset($array_datos_gestion['p14']['asignacion_ps_errada_2_lista'])) ? (count($array_datos_gestion['p14']['asignacion_ps_errada_2_lista'])>2) ? count($array_datos_gestion['p14']['asignacion_ps_errada_2_lista'])*40 : '150' : '150'; ?> px'
      },
      title: {
          text: null
      },
      subtitle: {
          text: null
      },
      xAxis: {
          categories: [
            <?php if(isset($array_datos_gestion['p14']['asignacion_ps_errada_2_lista'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p14']['asignacion_ps_errada_2_lista']); $i++): ?>
                '<?php echo $array_datos_gestion['p14']['asignacion_ps_errada_2_nombre'][$array_datos_gestion['p14']['asignacion_ps_errada_2_lista'][$i]]; ?>',
              <?php endfor; ?>
            <?php endif; ?>
          ],
          crosshair: true
      },
      yAxis: {
          min: 0,
          title: {
              text: '8.2. Asignación P.S errada'
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
          name: '8.2. Asignación P.S errada',
          data: [
            <?php if(isset($array_datos_gestion['p14']['asignacion_ps_errada_2_lista'])): ?>
              <?php for ($i=0; $i < count($array_datos_gestion['p14']['asignacion_ps_errada_2_lista']); $i++): ?>
                <?php if(isset($array_datos_gestion['p14']['asignacion_ps_errada_2'][$array_datos_gestion['p14']['asignacion_ps_errada_2_lista'][$i]])): ?>
                  <?php echo $array_datos_gestion['p14']['asignacion_ps_errada_2'][$array_datos_gestion['p14']['asignacion_ps_errada_2_lista'][$i]]; ?>,
                <?php else: ?>
                  0,
                <?php endif; ?>
              <?php endfor; ?>
            <?php endif; ?>
          ]

      }]
  });
</script>