document.addEventListener("DOMContentLoaded", function () {
  try {
    // Basic Area Chart -------> AREA CHART
    if (document.querySelector("#chart-area-basic")) {
      var options_area = {
        series: [
          {
            name: "STOCK ABC",
            data: [
              [1327359600000, 30.95],
              [1327446000000, 31.34],
              // ... rest of the data ...
              [1346104800000, 32.06],
            ],
          },
        ],
        chart: {
          fontFamily: "inherit",
          type: "area",
          height: 350,
          zoom: {
            enabled: false,
          },
          toolbar: {
            show: false,
          },
        },
        grid: {
          show: false,
        },
        colors: ["#615dff"],
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "straight",
        },
        xaxis: {
          type: "datetime",
          labels: {
            style: {
              colors: [
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
              ],
            },
          },
        },
        yaxis: {
          opposite: true,
          labels: {
            style: {
              colors: [
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
              ],
            },
          },
        },
        tooltip: {
          theme: "dark",
        },
        legend: {
          horizontalAlign: "left",
        },
      };

      var chart_area_basic = new ApexCharts(
        document.querySelector("#chart-area-basic"),
        options_area
      );
      chart_area_basic.render();
    } else {
      console.log("Chart element #chart-area-basic not found");
    }

    // Spline Area Chart -------> AREA CHART
    if (document.querySelector("#chart-area-spline")) {
      var options_spline = {
        series: [
          {
            name: "series1",
            data: [31, 40, 28, 51, 42, 109, 100],
          },
          {
            name: "series2",
            data: [11, 32, 45, 32, 34, 52, 41],
          },
        ],
        chart: {
          fontFamily: "inherit",
          height: 350,
          type: "area",
          toolbar: {
            show: false,
          },
        },
        grid: {
          show: false,
        },
        colors: ["#615dff", "#3dd9eb"],
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "smooth",
        },
        xaxis: {
          type: "datetime",
          categories: [
            "2018-09-19T00:00:00.000Z",
            "2018-09-19T01:30:00.000Z",
            "2018-09-19T02:30:00.000Z",
            "2018-09-19T03:30:00.000Z",
            "2018-09-19T04:30:00.000Z",
            "2018-09-19T05:30:00.000Z",
            "2018-09-19T06:30:00.000Z",
          ],
          labels: {
            style: {
              colors: [
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
              ],
            },
          },
        },
        yaxis: {
          labels: {
            style: {
              colors: [
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
              ],
            },
          },
        },
        tooltip: {
          x: {
            format: "dd/MM/yy HH:mm",
          },
          theme: "dark",
        },
        legend: {
          labels: {
            colors: ["#a1aab2"],
          },
        },
      };

      var chart_area_spline = new ApexCharts(
        document.querySelector("#chart-area-spline"),
        options_spline
      );
      chart_area_spline.render();
    } else {
      console.log("Chart element #chart-area-spline not found");
    }

    // Real Time Area Chart -------> AREA CHART
    if (document.querySelector("#chart-area-datetime")) {
      var options = {
        series: [
          {
            data: [
              [1327359600000, 30.95],
              [1327446000000, 31.34],
              // ... rest of the data ...
              [1361919600000, 39.6],
            ],
          },
        ],
        chart: {
          id: "area-datetime",
          fontFamily: "inherit",
          type: "area",
          height: 350,
          zoom: {
            autoScaleYaxis: true,
          },
          toolbar: {
            show: false,
          },
        },
        grid: {
          show: false,
        },
        colors: ["#615dff"],
        annotations: {
          yaxis: [
            {
              y: 30,
              borderColor: "#999",
              label: {
                show: true,
                text: "Support",
                style: {
                  color: "#fff",
                  background: "#39b69a",
                },
              },
            },
          ],
          xaxis: [
            {
              x: new Date("14 Nov 2012").getTime(),
              borderColor: "#999",
              yAxisIndex: 0,
              label: {
                show: true,
                text: "Rally",
                style: {
                  color: "#fff",
                  background: "#6610f2",
                },
              },
            },
          ],
        },
        dataLabels: {
          enabled: false,
        },
        markers: {
          size: 0,
          style: "hollow",
        },
        xaxis: {
          type: "datetime",
          min: new Date("01 Mar 2012").getTime(),
          tickAmount: 6,
          labels: {
            style: {
              colors: [
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
              ],
            },
          },
        },
        yaxis: {
          labels: {
            style: {
              colors: [
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
                "#a1aab2",
              ],
            },
          },
        },
        tooltip: {
          x: {
            format: "dd MMM yyyy",
          },
          theme: "dark",
        },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.9,
            stops: [0, 100],
          },
        },
      };

      var chart_area_datetime = new ApexCharts(
        document.querySelector("#chart-area-datetime"),
        options
      );
      chart_area_datetime.render();

      var resetCssClasses = function (activeEl) {
        var els = document.querySelectorAll("button");
        Array.prototype.forEach.call(els, function (el) {
          el.classList.remove("active");
        });

        activeEl.target.classList.add("active");
      };
    } else {
      console.log("Chart element #chart-area-datetime not found");
    }
  } catch (error) {
    console.error("Error initializing area charts:", error);
  }
});