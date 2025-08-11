document.addEventListener("DOMContentLoaded", function () {
  // Prepare data from Laravel
  @php
  $universities = $forms->groupBy('university');
  $universityCounts = $universities->map->count();
  @endphp

  var options_basic = {
      series: [{
          data: [
              @foreach($universityCounts as $university => $count)
                  {{ $count }},
              @endforeach
          ]
      }],
      chart: {
          fontFamily: "inherit",
          type: "bar",
          height: 350,
          toolbar: { show: false }
      },
      plotOptions: {
          bar: { horizontal: true }
      },
      xaxis: {
          categories: [
              @foreach($universityCounts->keys() as $university)
                  "{{ $university }}",
              @endforeach
          ]
      }
  };

  var chart_bar_basic = new ApexCharts(
      document.querySelector("#chart-bar-basic"),
      options_basic
  );
  chart_bar_basic.render();
});