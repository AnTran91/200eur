$(document).ready(function() {
  // based on prepared DOM, initialize echarts instance
  var $element = $('#js-promo-echarts');

  if ($element.length > 0) {

    var chart = echarts.init($element[0]);

    var option = {
      xAxis: {
        type: 'category',
        boundaryGap: false,
        data: $element.data('dates')
      },
      yAxis: {
        type: 'value',
        axisLabel: {
          formatter: '{value}'
        }
      },
      tooltip: {
        trigger: 'axis',
        formatter: "{b} : {c}"
      },
      title: {
        text: $element.data('title'),
        x: 'center'
      },
      legend: {
        orient: 'vertical',
        x: 'left'
      },
      toolbox: {
        show: true,
        feature: {
          saveAsImage: {
            show: true,
            title: ' '
          }
        }
      },
      series: [
        {
          data: $element.data('values'),
          type: 'line',
          areaStyle: {}
        }
      ],
      dataZoom: [
        {
          show: true
        }
      ]
    };
    // use configuration item and data specified to show chart
    chart.setOption(option);
  }
});
