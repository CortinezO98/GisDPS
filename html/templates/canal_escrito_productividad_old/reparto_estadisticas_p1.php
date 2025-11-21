<div class="tab-pane fade mt-0" id="p1" role="tabpanel" aria-labelledby="p1"> 
  <div class="row">
    <div class="col-lg-12 d-flex flex-column">
      <div class="row flex-grow">
        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card my-1">
          <div class="card card-rounded">
            <div class="card-body py-2">
              <div class="row">
                <div class="col-md-12 mb-2">
                  <p class="alert background-principal color-blanco py-1 px-2 my-0">Reparto | 1. Proyección Consolidación</p>
                  <?php $id_formulario='reparto_proyeccion_consolidacion'; ?>
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
                <div id="p1_g2"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  // var gaugeOptions = {
  //     chart: {
  //         type: 'solidgauge',
  //         height: 150,
  //         spacingTop: 0,
  //         spacingRight: 0,
  //         spacingBottom: 0,
  //         spacingLeft: 0,
  //         plotBorderWidth: 0
  //     },
  //     title: null,
  //     pane: {
  //         center: ['50%', '85%'],
  //         size: '140%',
  //         startAngle: -90,
  //         endAngle: 90,
  //         background: {
  //             backgroundColor:
  //                 Highcharts.defaultOptions.legend.backgroundColor || '#EEE',
  //             innerRadius: '60%',
  //             outerRadius: '100%',
  //             shape: 'arc'
  //         }
  //     },
  //     tooltip: {
  //         enabled: false
  //     },
  //     // the value axis
  //     yAxis: {
  //         stops: [
  //             [0.89, '#C0392B'], // green
  //             [0.9, '#F4D03F'], // yellow
  //             [1, '#00BF6F'] // red
  //         ],
  //         lineWidth: 0,
  //         minorTickInterval: null,
  //         tickAmount: 2,
  //         title: {
  //             y: -55
  //         },
  //         labels: {
  //             y: 16
  //         }
  //     },
  //     plotOptions: {
  //         solidgauge: {
  //             dataLabels: {
  //                 y: 5,
  //                 borderWidth: 0,
  //                 useHTML: true
  //             }
  //         }
  //     }
  // };

  // // Porcentaje aprobación
  // var chartSpeed = Highcharts.chart('p1_g1', Highcharts.merge(gaugeOptions, {
  //     yAxis: {
  //         min: 0,
  //         max: 100,
  //         title: {
  //             text: '<b>Productividad General</b>',
  //             style: {
  //                 fontSize: '13px'
  //             }
  //         }
  //     },
  //     credits: {
  //         enabled: false
  //     },
  //     series: [{
  //         name: 'Productividad',
  //         data: [<?php echo number_format($total_productividad_consolidada, 2, '.', ''); ?>],
  //         dataLabels: {
  //             format:
  //                 '<div style="text-align:center">' +
  //                 '<span style="font-size:25px">{y}</span><br/>' +
  //                 '<span style="font-size:12px;opacity:0.4">Porcentaje</span>' +
  //                 '</div>'
  //         },
  //         tooltip: {
  //             valueSuffix: ' %'
  //         }
  //     }]
  // }));

  Highcharts.chart('p1_g2', {
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
        <?php if(isset($array_datos_gestion[$id_formulario]['gestion_agente']['id'])): ?>
          <?php for ($i=0; $i < count($array_datos_gestion[$id_formulario]['gestion_agente']['id']); $i++): ?>
          {
              name: '<?php echo $array_datos_gestion[$id_formulario]['gestion_agente'][$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i]]['nombre']; ?>',
              data: [
                <?php for ($j=0; $j < count($array_anio_mes_hora_num); $j++): ?>
                  <?php if(isset($array_datos_gestion[$id_formulario]['gestion_agente'][$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i]]['hora'][$j])): ?>
                    <?php echo $array_datos_gestion[$id_formulario]['gestion_agente'][$array_datos_gestion[$id_formulario]['gestion_agente']['id'][$i]]['hora'][$j]; ?>,
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
</script>