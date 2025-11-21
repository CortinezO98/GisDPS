Highcharts.chart('<?php echo $id_formulario; ?>', {
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
        pointFormat: '<tr><td style="color:{series.color};padding:0;font-size:10px">{series.name}: </td>' +
            '<td style="padding:0;font-size:10px"><b>{point.y}</b></td></tr>',
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
      <?php if(isset($array_datos_gestion[$id_formulario]['agentes'])): ?>
        <?php for ($i=0; $i < count($array_datos_gestion[$id_formulario]['agentes']); $i++): ?>
          <?php
            $id_agente_graph=$array_datos_gestion[$id_formulario]['agentes'][$i];
          ?>
        {
            name: '<?php echo $array_datos_agente[$id_agente_graph]['nombre']; ?>',
            data: [
              <?php for ($j=0; $j < count($array_anio_mes_hora_num); $j++): ?>
                <?php if(isset($array_datos_gestion[$id_formulario]['gestion_agente_hora'][$id_agente_graph][$j])): ?>
                  <?php echo $array_datos_gestion[$id_formulario]['gestion_agente_hora'][$id_agente_graph][$j]; ?>,
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