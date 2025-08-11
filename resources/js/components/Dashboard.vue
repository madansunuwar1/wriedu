<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <h1>CRM Dashboard</h1>
      <div class="filters">
        <div class="filter-group">
          <label for="dateFrom">From:</label>
          <input type="date" id="dateFrom" v-model="filters.dateFrom" @change="fetchData" />
        </div>
        <div class="filter-group">
          <label for="dateTo">To:</label>
          <input type="date" id="dateTo" v-model="filters.dateTo" @change="fetchData" />
        </div>
      </div>
    </header>

    <div v-if="loading" class="loading-state"><div class="spinner"></div><p>Loading Dashboard Data...</p></div>
    <div v-if="error" class="error-state"><h2>Oops! Something went wrong.</h2><p>{{ error }}</p><button @click="fetchData">Try Again</button></div>

    <main v-if="dashboardData && !loading && !error" class="dashboard-main">

      <!-- Executive KPI Banner -->
      <section class="kpi-grid">
        <div class="kpi-card"><p>New Enquiries</p><h3 class="kpi-value">{{ dashboardData.kpis_executive?.newEnquiries ?? 0 }}</h3></div>
        <div class="kpi-card"><p>Applications</p><h3 class="kpi-value">{{ dashboardData.kpis_executive?.applications ?? 0 }}</h3></div>
        <div class="kpi-card"><p>Visa Granted</p><h3 class="kpi-value success">{{ dashboardData.kpis_executive?.visaGranted ?? 0 }}</h3></div>
        <div class="kpi-card finance"><p>Receivable Balance</p><h3 class="kpi-value">${{ (dashboardData.kpis_executive?.commissionReceivable ?? 0).toLocaleString() }}</h3></div>
        <div class="kpi-card finance"><p>Payable Balance</p><h3 class="kpi-value orange">${{ (dashboardData.kpis_executive?.commissionPayable ?? 0).toLocaleString() }}</h3></div>
        <div class="kpi-card finance"><p>Net Position</p><h3 class="kpi-value" :class="netPositionColor">${{ (dashboardData.kpis_executive?.netPosition ?? 0).toLocaleString() }}</h3></div>
        <div class="kpi-card finance"><p>Users Online</p><h3 class="kpi-value">{{ dashboardData.kpis_executive?.usersOnline ?? 0 }} <span class="online-dot"></span></h3></div>
      </section>

      <!-- Overall Team Activity -->
      <section class="chart-card full-width">
        <h3 class="chart-title">Overall Team Activity</h3>
        <div class="chart-wrapper">
          <Line v-if="teamActivityChartData.datasets[0].data.length" :data="teamActivityChartData" :options="chartOptions" />
          <p v-else class="no-data">No team activity data for this period.</p>
        </div>
      </section>

      <!-- Sales & Pipeline Performance Section -->
      <section class="dashboard-section">
        <h2 class="section-title">Sales & Pipeline Performance</h2>
        <div class="kpi-grid">
          <div class="kpi-card"><p>Raw Leads (Period)</p><h3 class="kpi-value">{{ dashboardData.kpis_sales?.rawLeads ?? 0 }}</h3></div>
          <div class="kpi-card"><p>Active Leads (Period)</p><h3 class="kpi-value">{{ dashboardData.kpis_sales?.activeLeads ?? 0 }}</h3></div>
          <div class="kpi-card"><p>Lead Conversion Rate</p><h3 class="kpi-value success">{{ dashboardData.kpis_sales?.conversionRate ?? 0 }}%</h3></div>
          <div class="kpi-card"><p>Avg. Time to Convert</p><h3 class="kpi-value">{{ dashboardData.kpis_sales?.avgConversionTime ?? 0 }} Days</h3></div>
        </div>
        <div class="dashboard-grid">
          <div class="chart-card"><h3 class="chart-title">Lead Status Distribution</h3><div class="chart-wrapper"><Bar v-if="leadStatusChartData.datasets[0].data.length" :data="leadStatusChartData" :options="chartOptions" /><p v-else class="no-data">No lead data</p></div></div>
          <div class="leaderboard-card" style="grid-column: span 2;"><h3 class="chart-title">Counselor Leaderboard</h3><table class="leaderboard-table" v-if="dashboardData.charts_sales?.leaderboard?.length > 0"><thead><tr><th>Rank</th><th>Name</th><th>Leads Handled</th><th>Converted</th><th>Rate</th></tr></thead><tbody><tr v-for="(user, i) in dashboardData.charts_sales.leaderboard" :key="user.created_by"><td>{{ i + 1 }}</td><td>{{ user.creator.name }} {{ user.creator.last }}</td><td>{{ user.total_leads }}</td><td>{{ user.converted_leads }}</td><td>{{ user.conversion_rate }}%</td></tr></tbody></table><p v-else class="no-data">No leaderboard data for this period.</p></div>
        </div>
      </section>

      <!-- Operations & Application Performance Section -->
      <section class="dashboard-section">
        <h2 class="section-title">Operations & Application Performance</h2>
        <div class="dashboard-grid">
          <div class="chart-card"><h3 class="chart-title">Application Status</h3><div class="chart-wrapper"><Bar v-if="appStatusChartData.datasets[0].data.length" :data="appStatusChartData" :options="chartOptions" /><p v-else class="no-data">No application data</p></div></div>
          <div class="chart-card"><h3 class="chart-title">CAS Feedback Status</h3><div class="chart-wrapper"><Doughnut v-if="casStatusChartData.datasets[0].data.length" :data="casStatusChartData" :options="chartOptions" /><p v-else class="no-data">No CAS data</p></div></div>
          <div class="chart-card"><h3 class="chart-title">Top Universities by Application</h3><div class="chart-wrapper"><Bar v-if="topUniversitiesChartData.datasets[0].data.length" :data="topUniversitiesChartData" :options="chartOptions" /><p v-else class="no-data">No university data</p></div></div>
          <div class="chart-card"><h3 class="chart-title">Top Countries by Application</h3><div class="chart-wrapper"><Bar v-if="topCountriesChartData.datasets[0].data.length" :data="topCountriesChartData" :options="chartOptions" /><p v-else class="no-data">No country data</p></div></div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Bar, Doughnut, Line } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement, ArcElement, Filler } from 'chart.js';
import axios from 'axios';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement, ArcElement, Filler);

// --- STATE MANAGEMENT ---
const loading = ref(true);
const error = ref(null);
const dashboardData = ref(null);
const filters = ref({ dateFrom: '', dateTo: '' });
const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } }
});

// --- API & DATA HANDLING ---
const fetchData = async () => {
  loading.value = true;
  error.value = null;
  dashboardData.value = null;
  try {
    const params = new URLSearchParams(filters.value);
    const response = await axios.get(`/api/dashboard/stats?${params.toString()}`);
    dashboardData.value = response.data;
  } catch (err) {
    console.error(`Dashboard fetch error:`, err);
    error.value = err.response?.data?.message || "An error occurred while fetching dashboard data.";
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  const today = new Date();
  const priorDate = new Date(new Date().setDate(today.getDate() - 30));
  filters.value.dateFrom = priorDate.toISOString().split('T')[0];
  filters.value.dateTo = today.toISOString().split('T')[0];
  fetchData();
});

// --- COMPUTED PROPERTIES & HELPERS ---
const netPositionColor = computed(() => (dashboardData.value?.kpis_executive?.netPosition ?? 0) >= 0 ? 'success' : 'danger');

const generateChartData = (distribution, label, backgroundColor) => {
  if (!distribution || Object.keys(distribution).length === 0) {
      return { labels: [], datasets: [{ data: [] }] };
  }
  return {
      labels: Object.keys(distribution),
      datasets: [{ label, backgroundColor, data: Object.values(distribution) }]
  };
};

// --- Chart Data Computeds ---
const teamActivityChartData = computed(() => generateChartData(dashboardData.value?.charts_main?.activityOverTime, 'Actions', '#42A5F5'));
const leadStatusChartData = computed(() => generateChartData(dashboardData.value?.charts_sales?.leadStatusDistribution, 'Leads', '#42A5F5'));
const appStatusChartData = computed(() => generateChartData(dashboardData.value?.charts_operations?.applicationStatusDistribution, 'Applications', '#66BB6A'));
const casStatusChartData = computed(() => generateChartData(dashboardData.value?.charts_operations?.casStatusDistribution, 'CAS Tickets', ['#FF6384','#36A2EB','#FFCE56','#4BC0C0']));
const topUniversitiesChartData = computed(() => generateChartData(dashboardData.value?.charts_operations?.topUniversities, 'Applications', '#FFA726'));
const topCountriesChartData = computed(() => generateChartData(dashboardData.value?.charts_operations?.topCountries, 'Applications', '#9966FF'));
</script>

<style scoped>
/* Base & Layout */
.dashboard-container { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; padding: 1.5rem 2rem; background-color: #f8f9fa; }
.dashboard-main { display: flex; flex-direction: column; gap: 2.5rem; }
.dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
h1 { color: #212529; margin: 0; font-weight: 600; font-size: 1.75rem; }
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; }
.full-width { grid-column: 1 / -1; }

/* Section Styling */
.dashboard-section { display: flex; flex-direction: column; gap: 1.5rem; }
.section-title { font-size: 1.5rem; font-weight: 600; color: #343a40; margin: 0; padding-bottom: 0.75rem; border-bottom: 1px solid #dee2e6; }

/* Filters */
.filters { display: flex; gap: 1rem; align-items: center; }
.filter-group { display: flex; align-items: center; gap: .5rem; }
.filter-group label { font-weight: 500; color: #495057; font-size: 0.9rem; }
.filter-group input { padding: .5rem; border: 1px solid #ced4da; border-radius: 6px; background-color: #fff; }

/* States */
.loading-state, .error-state { text-align: center; padding: 4rem; color: #6c757d; }
.spinner { border: 4px solid #e9ecef; width: 36px; height: 36px; border-radius: 50%; border-left-color: #007bff; animation: spin 1s ease infinite; margin: 0 auto 1rem; }
@keyframes spin { 0%{transform:rotate(0)} 100%{transform:rotate(360deg)} }

/* Cards */
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1.5rem; }
.kpi-card, .chart-card, .leaderboard-card { background-color: #fff; padding: 1.25rem; border-radius: 8px; border: 1px solid #e9ecef; }
.kpi-card p { margin: 0 0 .25rem; font-size: 0.9rem; color: #6c757d; font-weight: 500; }
.kpi-value { margin: 0; font-size: 2rem; font-weight: 700; color: #343a40; }
.kpi-value.success { color: #198754; }
.kpi-value.danger { color: #dc3545; }
.kpi-value.orange { color: #fd7e14; }
.kpi-card.finance { background-color: #f0f7ff; }
.online-dot { display: inline-block; width: 10px; height: 10px; background-color: #198754; border-radius: 50%; margin-left: .5rem; animation: pulse 2s infinite; }
@keyframes pulse { 0%{box-shadow:0 0 0 0 #19875499} 70%{box-shadow:0 0 0 10px #19875400} 100%{box-shadow:0 0 0 0 #19875400} }

/* Charts & Tables */
.chart-card { min-height: 400px; display: flex; flex-direction: column; }
.chart-wrapper { position: relative; flex-grow: 1; }
.chart-title { margin: 0 0 1rem; text-align: center; font-weight: 600; color: #343a40; flex-shrink: 0; }
.no-data { text-align: center; margin: auto; color: #adb5bd; }
.leaderboard-card { overflow-x: auto; }
.leaderboard-table { width: 100%; border-collapse: collapse; text-align: left; }
.leaderboard-table th, .leaderboard-table td { padding: .75rem 1rem; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
.leaderboard-table th { font-weight: 600; color: #495057; font-size: 0.9rem; }
</style>