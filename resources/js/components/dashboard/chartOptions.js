// /resources/js/Pages/chartOptions.js
const defaultOptions = {
    chart: {
        toolbar: { show: false },
        zoom: { enabled: false },
        fontFamily: 'Nunito, -apple-system, BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"',
    },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    grid: { borderColor: '#e3e6f0', row: { colors: ['#f8f9fc', 'transparent'], opacity: 0.5 } },
    xaxis: {
        labels: { style: { colors: '#858796', fontSize: '11px' }, trim: true, rotate: -30, hideOverlappingLabels: true, },
        axisBorder: { show: false }, axisTicks: { show: false }
    },
    yaxis: { labels: { style: { colors: '#858796', fontSize: '11px' }, formatter: val => Number.isInteger(val) ? val.toFixed(0) : '' } },
    tooltip: { theme: 'light', x: { show: true } }
};

const pieDonutBase = {
    ...defaultOptions,
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '11px', itemMargin: { vertical: 3 }, markers: { width: 8, height: 8, radius: 4 }, offsetY: 5,
        formatter: (seriesName, opts) => {
            const maxLength = 20;
            return seriesName.length > maxLength ? seriesName.substring(0, maxLength) + '...' : seriesName;
        }
    },
    tooltip: { enabled: true, theme: 'light', fillSeriesColor: false, y: { formatter: (value, { seriesIndex, w }) => `${w.config.labels[seriesIndex]}: ${value}` } },
    colors: ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b', '#36b9cc', '#858796', '#fd7e14', '#6f42c1', '#20c9a6', '#5a5c69'],
};

export const trendChartOptions = { ...defaultOptions, chart: { type: 'area', height: 320 }, legend: { position: 'top', horizontalAlign: 'center', offsetY: -5, itemMargin: { horizontal: 10 }, markers: { radius: 12 } } };
export const pieChartOptions = { ...pieDonutBase, chart: { type: 'pie', height: 300 }, dataLabels: { enabled: true, formatter: (val, opts) => { const total = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0); const percent = total > 0 ? ((opts.w.config.series[opts.seriesIndex] / total) * 100).toFixed(0) : 0; return percent > 5 ? `${percent}%` : ''; }, style: { fontSize: '10px', colors: ['#fff'] }, dropShadow: { enabled: true, top: 1, left: 1, blur: 1, opacity: 0.45 } } };
export const donutChartOptions = { ...pieDonutBase, chart: { type: 'donut', height: 300 }, plotOptions: { pie: { donut: { size: '60%' } } } };
export const barChartOptions = { ...defaultOptions, chart: { type: 'bar', height: 320 }, plotOptions: { bar: { horizontal: false, columnWidth: '60%', borderRadius: 3, dataLabels: { position: 'top' } } }, dataLabels: { enabled: true, offsetY: -15, style: { fontSize: '10px', colors: ["#304758"] }, formatter: val => val > 0 ? val : '' }, colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#858796'] };