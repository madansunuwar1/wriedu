<!-- components/dashboard/DashboardChart.vue -->
<template>
  <div class="card chart-card h-100">
    <div class="card-header py-3">
      <h6 class="card-title m-0 font-weight-bold text-primary">{{ title }}</h6>
    </div>
    <div class="card-body d-flex flex-column">
      <div class="chart-container flex-grow-1" :class="{ 'loading-overlay': isLoading }">
        <apexchart :type="type" height="100%" :options="options" :series="series"></apexchart>
      </div>
      <small v-if="note" class="text-muted text-center mt-1 chart-note">{{ note }}</small>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

// Use a shorter alias for the component
const apexchart = VueApexCharts;

const props = defineProps({
  title: String,
  type: { type: String, default: 'area' },
  series: Array,
  chartOptions: Object,
  isLoading: Boolean,
  note: String,
});

const defaultOptions = {
  chart: { toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Nunito, sans-serif' },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 2 },
  grid: { borderColor: '#e3e6f0', row: { colors: ['#f8f9fc', 'transparent'], opacity: 0.5 } },
  xaxis: { labels: { style: { colors: '#858796', fontSize: '11px' }, trim: true, rotate: -30, hideOverlappingLabels: true }, axisBorder: { show: false }, axisTicks: { show: false } },
  yaxis: { labels: { style: { colors: '#858796', fontSize: '11px' }, formatter: val => Number.isInteger(val) ? val.toFixed(0) : '' } },
  tooltip: { theme: 'light', x: { show: true } },
  legend: { position: 'bottom', fontSize: '11px', itemMargin: { vertical: 3 }, markers: { width: 8, height: 8, radius: 4 }, offsetY: 5 },
};

// Merge default options with specific options passed as props
const options = computed(() => ({
  ...defaultOptions,
  ...props.chartOptions,
}));
</script>

<style scoped>
/* Scoped styles from your blade file */
.chart-card { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); border: 1px solid #e3e6f0; border-radius: 0.35rem; }
.card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; padding: 0.75rem 1.25rem; }
.card-title { margin-bottom: 0; font-size: 0.9rem; font-weight: 700; color: #4e73df; }
.card-body { padding: 1.25rem; }
.chart-container { position: relative; width: 100%; min-height: 320px; }
.chart-note { font-size: 0.75rem; color: #858796; }
.loading-overlay::before { content: ''; position: absolute; inset: 0; background: rgba(255, 255, 255, 0.7); z-index: 10; border-radius: 0.35rem; }
.loading-overlay::after { content: 'Loading...'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 11; color: #4e73df; font-weight: bold; }
</style>