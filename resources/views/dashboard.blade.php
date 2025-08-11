@extends('layouts.admin')
@section('content')


<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    :root { --bs-primary-rgb: 78, 115, 223; --bs-success-rgb: 28, 200, 138; --bs-info-rgb: 54, 185, 204; --bs-warning-rgb: 246, 194, 62; --bs-danger-rgb: 231, 74, 59; --bs-secondary-rgb: 133, 135, 150; --bs-light-rgb: 248, 249, 252; --bs-dark-rgb: 90, 92, 105; --hot-lead-color: #dc3545; --raw-lead-color: #6f42c1; }
    .main-content { padding: 1.5rem; }
    .top-bar { border-bottom: 1px solid #e3e6f0; padding-bottom: 1rem; margin-bottom: 1.5rem; }
    .top-bar h1 { font-size: 1.75rem; color: #5a5c69; font-weight: 400; }
    .filters-container { gap: 1rem; }
    .type-filter-container .btn-group .btn, .date-filter-container .btn-group .btn { font-size: 0.8rem; padding: 0.3rem 0.6rem; }
    .type-filter.active { background-color: #858796; color: white; border-color: #858796; }
    .date-filter.active { background-color: #4e73df; color: white; border-color: #4e73df; }
    .custom-date-range .input-group { max-width: 350px; }
    .custom-date-range .form-control-sm { font-size: 0.8rem; padding: 0.3rem 0.5rem;}
    .custom-date-range .btn-sm { font-size: 0.8rem; padding: 0.3rem 0.6rem;}
    .metric-card .card-body { padding: 1.25rem; }
    .metric-card .card-body .row { display: flex; align-items: center; }
    .metric-card .col-auto i { font-size: 2rem; color: rgba(255, 255, 255, 0.5); }
    .card-hot-lead { background-color: var(--hot-lead-color); color: white; }
    .card-raw-lead { background-color: var(--raw-lead-color); color: white; }
    .card-hot-lead .text-xs, .card-raw-lead .text-xs { color: rgba(255,255,255,0.8) !important; }
    .card-hot-lead .col-auto i, .card-raw-lead .col-auto i { color: rgba(255,255,255,0.6) !important; }
    .chart-card { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); border: 1px solid #e3e6f0; border-radius: 0.35rem; }
    .chart-card .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; padding: 0.75rem 1.25rem; display: flex; justify-content: space-between; align-items: center; }
    .chart-card .card-title { margin-bottom: 0; font-size: 0.9rem; font-weight: 700; color: #4e73df; }
    .chart-card .card-body { padding: 1.25rem; }
    .chart-container { position: relative; width: 100%; min-height: 320px; margin-bottom: 0; }
    .chart-container canvas { position: relative; width: 100% !important; height: 100% !important; }
    @media (min-width: 992px) { .chart-container { min-height: 350px; } }
    .chart-container.loading-overlay::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.7); z-index: 10; border-radius: 0.35rem; }
    .chart-container.loading-overlay::after { content: 'Loading...'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 11; color: #4e73df; font-weight: bold; font-size: 1rem; }
    .table-card { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); border: 1px solid #e3e6f0; border-radius: 0.35rem; }
    .table-card .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; padding: 0.75rem 1.25rem; display: flex; justify-content: space-between; align-items: center; }
    .table-card .card-title { margin-bottom: 0; font-size: 0.9rem; font-weight: 700; color: #4e73df; }
    .table-card .card-body { padding: 0; }
    .table-responsive { overflow-x: auto; }
    .table thead th { background-color: #eaecf4; border-bottom-width: 1px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #5a5c69; padding: 0.75rem; white-space: nowrap; }
    .table td { font-size: 0.85rem; vertical-align: middle; padding: 0.75rem; white-space: nowrap; }
    .table .badge { font-size: 0.75rem; padding: 0.3em 0.6em; font-weight: 600; }
    .table .btn-sm { padding: 0.2rem 0.4rem; font-size: 0.75rem; }
    .table-hover tbody tr:hover { background-color: #f8f9fc; }
    @media (max-width: 991px) { .top-bar { flex-direction: column; align-items: flex-start; } .filters-container { width: 100%; flex-direction: column; align-items: stretch; gap: 0.75rem; margin-top: 0.75rem; } .type-filter-container .btn-group, .date-filter-container .btn-group { width: 100%; display: flex; } .type-filter-container .btn-group .btn, .date-filter-container .btn-group .btn { flex-grow: 1; } .custom-date-range { width: 100%; } .custom-date-range .input-group { max-width: none; } .table td, .table th { white-space: normal; } }
    @media (max-width: 1199px) and (min-width: 768px) { .metric-card-col { flex: 0 0 auto; width: 33.33333333%; } }
    @media (max-width: 767px) { .metric-card-col { flex: 0 0 auto; width: 50%; } }
    .card { border: none; } .h-100 { height: 100% !important; } .dashboard-hidden { display: none !important; } .chart-note { font-size: 0.75rem; color: #858796; }
</style>

<div class="main-content">
    <div class="top-bar d-flex flex-wrap justify-content-between align-items-center">
        <h1 class="mb-md-0 mb-3">Dashboard</h1>
        <div class="filters-container d-flex flex-wrap align-items-center">
             <div class="type-filter-container">
                 <div class="btn-group flex-wrap shadow-sm" role="group" aria-label="Data Type Filter">
                     <button type="button" class="btn btn-sm btn-outline-secondary type-filter active" data-type="overall">Overall</button>
                     <button type="button" class="btn btn-sm btn-outline-secondary type-filter" data-type="application">Applications</button>
                     <button type="button" class="btn btn-sm btn-outline-secondary type-filter" data-type="lead">Total Leads</button>
                     <button type="button" class="btn btn-sm btn-outline-secondary type-filter" data-type="hotlead">Hot Leads</button>
                     <button type="button" class="btn btn-sm btn-outline-secondary type-filter" data-type="rawlead">Raw Leads</button>
                     <button type="button" class="btn btn-sm btn-outline-secondary type-filter" data-type="enquiry">Enquiries</button>
                 </div>
             </div>
            <div class="date-filter-container">
                <div class="btn-group flex-wrap shadow-sm me-md-2" role="group" aria-label="Date Range Filter">
                    <button type="button" class="btn btn-sm btn-outline-primary date-filter" data-range="today">Today</button>
                    <button type="button" class="btn btn-sm btn-outline-primary date-filter active" data-range="week">Week</button>
                    <button type="button" class="btn btn-sm btn-outline-primary date-filter" data-range="month">Month</button>
                    <button type="button" class="btn btn-sm btn-outline-primary date-filter" data-range="year">Year</button>
                    <button type="button" class="btn btn-sm btn-outline-primary date-filter" data-range="custom">Custom</button>
                </div>
                <div class="custom-date-range mt-2 mt-md-0" style="display: none;">
                    <div class="input-group input-group-sm shadow-sm">
                        <input type="date" class="form-control form-control-sm" id="start-date" aria-label="Start Date">
                        <span class="input-group-text">to</span>
                        <input type="date" class="form-control form-control-sm" id="end-date" aria-label="End Date">
                        <button class="btn btn-sm btn-primary" id="apply-custom-range">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="dashboard-error-alert-container"></div>

    {{-- Metric Cards --}}
    <div class="row">
        <div class="col-xl-2 col-md-4 mb-4 metric-card-col"> <div class="card text-bg-primary shadow h-100 metric-card"> <div class="card-body"> <div class="row g-0 align-items-center"> <div class="col me-2"> <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Applications</div> <div class="h5 mb-0 font-weight-bold text-white" id="total-applications-count">-</div> </div> <div class="col-auto"><i class="fas fa-file-alt fa-2x text-white-50"></i></div> </div> </div> </div> </div>
        <div class="col-xl-2 col-md-4 mb-4 metric-card-col"> <div class="card text-bg-success shadow h-100 metric-card"> <div class="card-body"> <div class="row g-0 align-items-center"> <div class="col me-2"> <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Total Leads</div> <div class="h5 mb-0 font-weight-bold text-white"><span id="total-leads-count">-</span></div> </div> <div class="col-auto"><i class="fas fa-users fa-2x text-white-50"></i></div> </div> </div> </div> </div>
        <div class="col-xl-2 col-md-4 mb-4 metric-card-col"> <div class="card card-hot-lead shadow h-100 metric-card"> <div class="card-body"> <div class="row g-0 align-items-center"> <div class="col me-2"> <div class="text-xs font-weight-bold text-uppercase mb-1">Hot Leads</div> <div class="h5 mb-0 font-weight-bold"><span id="total-hot-leads-count">-</span></div> </div> <div class="col-auto"><i class="fas fa-fire fa-2x"></i></div> </div> </div> </div> </div>
        <div class="col-xl-2 col-md-4 mb-4 metric-card-col"> <div class="card card-raw-lead shadow h-100 metric-card"> <div class="card-body"> <div class="row g-0 align-items-center"> <div class="col me-2"> <div class="text-xs font-weight-bold text-uppercase mb-1">Raw Leads</div> <div class="h5 mb-0 font-weight-bold"><span id="total-raw-leads-count">-</span></div> </div> <div class="col-auto"><i class="fas fa-seedling fa-2x"></i></div> </div> </div> </div> </div>
        <div class="col-xl-2 col-md-4 mb-4 metric-card-col"> <div class="card text-bg-info shadow h-100 metric-card"> <div class="card-body"> <div class="row g-0 align-items-center"> <div class="col me-2"> <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Enquiries</div> <div class="h5 mb-0 font-weight-bold text-white"><span id="total-enquiries-count">-</span></div> </div> <div class="col-auto"><i class="fas fa-comments fa-2x text-white-50"></i></div> </div> </div> </div> </div>
        <div class="col-xl-2 col-md-4 mb-4 metric-card-col"> <div class="card text-bg-secondary shadow h-100 metric-card"> <div class="card-body"> <div class="row g-0 align-items-center"> <div class="col me-2"> <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Conversion Rate</div> <div class="h5 mb-0 font-weight-bold text-white"><span id="conversion-rate-count">-%</span></div> </div> <div class="col-auto"><i class="fas fa-percentage fa-2x text-white-50"></i></div> </div> </div> </div> </div>
    </div>

    {{-- Trend Chart --}}
    <div class="row">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card chart-card h-100">
                <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary" id="trend-chart-title">Data Trend (Overall)</h6></div>
                <div class="card-body"><div class="chart-container"><div id="applications-trend-chart"></div></div></div>
            </div>
        </div>

        {{-- Status Pies --}}
          <div class="col-xl-4 col-lg-5 mb-4 chart-card-wrapper dashboard-hidden dashboard-application dashboard-overall" id="application-status-chart-wrapper">
            <div class="card chart-card h-100">
                <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Application Status</h6></div>
                <div class="card-body d-flex flex-column"> <div class="chart-container flex-grow-1"><div id="application-status-chart"></div></div> <small class="text-muted text-center mt-1 chart-note">Based on 'document' field. Top 10.</small> </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5 mb-4 chart-card-wrapper dashboard-overall dashboard-lead dashboard-hotlead dashboard-rawlead" id="lead-status-chart-wrapper">
            <div class="card chart-card h-100">
                <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary" id="lead-status-chart-title">Lead Status (Overall)</h6></div>
                <div class="card-body d-flex flex-column"><div class="chart-container flex-grow-1"><div id="lead-status-chart"></div></div> <small class="text-muted text-center mt-1 chart-note">Top 10 Statuses</small></div>
            </div>
        </div>
      
        <div class="col-xl-4 col-lg-5 mb-4 chart-card-wrapper dashboard-hidden dashboard-enquiry dashboard-overall" id="enquiry-status-chart-wrapper">
            <div class="card chart-card h-100">
                <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Enquiry Status</h6></div>
                <div class="card-body d-flex flex-column"> <div class="chart-container flex-grow-1"><div id="enquiry-status-chart"></div></div> <small class="text-muted text-center mt-1 chart-note">Requires 'status' field. Top 10.</small> </div>
            </div>
        </div>
    </div>

    {{-- Detail Charts --}}
    <div class="row">
        {{-- Application Charts --}}
        <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-overall dashboard-application" id="university-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Top 5 Universities (Applications)</h6></div> <div class="card-body"><div class="chart-container"><div id="university-chart"></div></div></div> </div>
        </div>
         <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-overall dashboard-application" id="top-courses-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Top 5 Courses (Applications)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-courses-chart"></div></div></div> </div>
        </div>
        <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-application" id="top-app-assignees-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Top 5 Assignees (Applications)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-app-assignees-chart"></div></div></div> </div>
        </div>
         <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-application" id="top-app-intakes-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Top 5 Intakes (Applications)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-app-intakes-chart"></div></div><small class="text-muted text-center mt-1 chart-note">Requires 'intake' field.</small></div> </div>
        </div>
        <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-application" id="top-app-locations-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Top 5 Locations (Applications)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-app-locations-chart"></div></div><small class="text-muted text-center mt-1 chart-note">Requires 'location' field.</small></div> </div>
        </div>
         <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-application" id="top-app-sources-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Top 5 Sources (Applications)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-app-sources-chart"></div></div><small class="text-muted text-center mt-1 chart-note">Requires 'source' field.</small></div> </div>
        </div>

        {{-- Lead Charts --}}
        <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-overall dashboard-lead dashboard-hotlead dashboard-rawlead" id="lead-source-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary" id="lead-source-chart-title">Top 5 Lead Sources (Overall)</h6></div> <div class="card-body d-flex flex-column"><div class="chart-container flex-grow-1"><div id="lead-source-chart"></div></div></div> </div>
        </div>
        <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-lead dashboard-hotlead dashboard-rawlead" id="top-lead-courses-chart-wrapper">
             <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary" id="top-lead-courses-chart-title">Top 5 Courses (Leads Overall)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-lead-courses-chart"></div></div></div> </div>
        </div>
        <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-lead dashboard-hotlead dashboard-rawlead" id="top-lead-creators-chart-wrapper">
             <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary" id="top-lead-creators-chart-title">Top 5 Creators (Leads Overall)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-lead-creators-chart"></div></div></div> </div>
        </div>
         <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-lead dashboard-hotlead dashboard-rawlead" id="top-lead-intakes-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary" id="top-lead-intakes-chart-title">Top 5 Intakes (Leads Overall)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-lead-intakes-chart"></div></div><small class="text-muted text-center mt-1 chart-note">Requires 'intake' field.</small></div> </div>
        </div>
         <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-lead dashboard-hotlead dashboard-rawlead" id="top-lead-countries-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary" id="top-lead-countries-chart-title">Top 5 Countries (Leads Overall)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-lead-countries-chart"></div></div><small class="text-muted text-center mt-1 chart-note">Requires 'country' field.</small></div> </div>
        </div>
         <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-lead dashboard-hotlead dashboard-rawlead" id="top-lead-universities-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary" id="top-lead-universities-chart-title">Top 5 Universities (Leads Overall)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-lead-universities-chart"></div></div><small class="text-muted text-center mt-1 chart-note">Requires 'university' field.</small></div> </div>
        </div>

        {{-- Enquiry Charts --}}
        <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-enquiry" id="top-enquiry-countries-chart-wrapper">
             <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Top 5 Countries (Enquiries)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-enquiry-countries-chart"></div></div><small class="text-muted text-center mt-1 chart-note">Requires 'country' field.</small></div> </div>
        </div>
        <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-enquiry" id="enquiry-source-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Top 5 Enquiry Sources</h6></div> <div class="card-body d-flex flex-column"> <div class="chart-container flex-grow-1"><div id="enquiry-source-chart"></div></div> <small class="text-muted text-center mt-1 chart-note">Requires 'source' field.</small> </div> </div>
        </div>
         <div class="col-lg-6 mb-4 chart-card-wrapper dashboard-hidden dashboard-enquiry" id="top-enquiry-education-chart-wrapper">
            <div class="card chart-card h-100"> <div class="card-header py-3"><h6 class="card-title m-0 font-weight-bold text-primary">Top 5 Education Levels (Enquiries)</h6></div> <div class="card-body"><div class="chart-container"><div id="top-enquiry-education-chart"></div></div><small class="text-muted text-center mt-1 chart-note">Requires 'education' field.</small></div> </div>
        </div>
    </div>

    {{-- Recent Applications Table --}}
    <div class="row" id="recent-applications-row" style="display: none;">
         <div class="col-lg-12"> <div class="card table-card mb-4"> <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"> <h6 class="card-title m-0 font-weight-bold text-primary">Recent Applications</h6> @if(Route::has('backend.application.index')) <a href="{{ route('backend.application.index') }}" class="btn btn-sm btn-outline-primary"> <i class="fas fa-external-link-alt fa-sm"></i> View All </a> @endif </div> <div class="card-body p-0"> <div class="table-responsive"> <table class="table table-hover mb-0" width="100%" cellspacing="0"> <thead> <tr> <th>ID</th><th>Student Name</th><th>University</th><th>Course</th><th>Date</th><th>Status (Doc)</th><th>Action</th> </tr> </thead> <tbody id="recent-applications-body"> <tr> <td colspan="7" class="text-center py-5"> <div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> <span class="ms-2">Loading...</span> </td> </tr> </tbody> </table> </div> </div> </div> </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let trendChart = null;
        let leadStatusPieChart = null;
        let applicationStatusChart = null;
        let enquiryStatusChart = null;
        let universityBarChart = null;
        let leadSourceDonutChart = null;
        let topCoursesBarChart = null;
        let topAppAssigneesBarChart = null;
        let topLeadCoursesBarChart = null;
        let topLeadCreatorsBarChart = null;
        let topEnquiryCountriesBarChart = null;
        let enquirySourceDonutChart = null;
        let topAppIntakesBarChart = null;
        let topAppLocationsBarChart = null;
        let topAppSourcesBarChart = null;
        let topLeadIntakesBarChart = null;
        let topLeadCountriesBarChart = null;
        let topLeadUniversitiesBarChart = null;
        let topEnquiryEducationBarChart = null;

        let currentViewType = 'overall';
        let currentRange = 'week';
        let currentStartDate = null;
        let currentEndDate = null;

        const totalApplicationsCountEl = document.getElementById('total-applications-count');
        const totalLeadsCountEl = document.getElementById('total-leads-count');
        const totalHotLeadsCountEl = document.getElementById('total-hot-leads-count');
        const totalRawLeadsCountEl = document.getElementById('total-raw-leads-count');
        const totalEnquiriesCountEl = document.getElementById('total-enquiries-count');
        const conversionRateCountEl = document.getElementById('conversion-rate-count');
        const trendTitleEl = document.getElementById('trend-chart-title');
        const allChartCardWrappers = document.querySelectorAll('.chart-card-wrapper');
        const recentAppsRowEl = document.getElementById('recent-applications-row');
        const typeFilterButtons = document.querySelectorAll('.type-filter');
        const dateFilterButtons = document.querySelectorAll('.date-filter');
        const customDateContainer = document.querySelector('.custom-date-range');
        const applyCustomRangeBtn = document.getElementById('apply-custom-range');
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const errorContainer = document.getElementById('dashboard-error-alert-container');
        const recentAppsBodyEl = document.getElementById('recent-applications-body');

        const defaultChartOptions = { chart: { toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Nunito, -apple-system, BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"', }, dataLabels: { enabled: false }, stroke: { curve: 'smooth', width: 2 }, grid: { borderColor: '#e3e6f0', row: { colors: ['#f8f9fc', 'transparent'], opacity: 0.5 } }, xaxis: { labels: { style: { colors: '#858796', fontSize: '11px' }, trim: true, rotate: -30, hideOverlappingLabels: true, }, axisBorder: { show: false }, axisTicks: { show: false } }, yaxis: { labels: { style: { colors: '#858796', fontSize: '11px' }, formatter: val => Number.isInteger(val) ? val.toFixed(0) : '' } }, tooltip: { theme: 'light', x: { show: true } } };
        const defaultPieDonutOptions = { chart: { fontFamily: 'Nunito, -apple-system, BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"', toolbar: { show: false } }, dataLabels: { enabled: false }, legend: { position: 'bottom', fontSize: '11px', itemMargin: { vertical: 3 }, markers: { width: 8, height: 8, radius: 4 }, offsetY: 5, formatter: function(seriesName, opts) { const maxLength = 20; return seriesName.length > maxLength ? seriesName.substring(0, maxLength) + '...' : seriesName; } }, tooltip: { enabled: true, theme: 'light', fillSeriesColor: false, y: { formatter: function(value, { seriesIndex, w }) { if (w && w.config && w.config.labels && w.config.labels[seriesIndex] !== undefined) { const label = w.config.labels[seriesIndex]; return `${label}: ${value}`; } return value; }, } } };
        const defaultBarOptions = deepMerge({}, defaultChartOptions, { chart: { type: 'bar', height: 300 }, plotOptions: { bar: { horizontal: false, columnWidth: '60%', borderRadius: 3, dataLabels: { position: 'top' } } }, dataLabels: { enabled: true, offsetY: -15, style: { fontSize: '10px', colors: ["#304758"] }, formatter: val => val > 0 ? val : '' }, colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#858796'], yaxis: { labels: { formatter: val => Number.isInteger(val) ? val.toFixed(0) : '' } } });

        function deepMerge(target, ...sources) { sources.forEach(source => { Object.keys(source).forEach(key => { const targetValue = target[key]; const sourceValue = source[key]; if (isObject(targetValue) && isObject(sourceValue)) { deepMerge(targetValue, sourceValue); } else { target[key] = sourceValue; } }); }); return target; }
        function isObject(item) { return (item && typeof item === 'object' && !Array.isArray(item)); }

        function initTrendChart(elementId, data) {
             const element = document.getElementById(elementId); if (!element) { console.error(`Element not found: ${elementId}`); return null; }
             const options = deepMerge({}, defaultChartOptions, { series: [ { name: 'Applications', data: data.applications || [] }, { name: 'Leads', data: data.leads || [] }, { name: 'Enquiries', data: data.enquiries || [] } ], colors: ['#4e73df', '#1cc88a', '#36b9cc'], chart: { height: 320, type: 'area' }, stroke: { width: 2, curve: 'smooth' }, xaxis: { type: 'category', categories: data.labels || [], labels: { rotate: -30, hideOverlappingLabels: true, format: 'MMM dd' } }, legend: { position: 'top', horizontalAlign: 'center', offsetY: -5, itemMargin: { horizontal: 10 }, markers:{radius:12} }, tooltip: { y: { formatter: val => val }, x: { format: 'dd MMM yyyy' } } });
            const chart = new ApexCharts(element, options); chart.render(); return chart;
        }
        function initPieChart(elementId, data, title = 'Status') {
             const element = document.getElementById(elementId); if (!element) { console.error(`Element not found: ${elementId}`); return null; }
             const options = deepMerge({}, defaultPieDonutOptions, { series: data.map(item => item.count), labels: data.map(item => item.label), chart: { type: 'pie', height: 300 }, colors: ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b', '#36b9cc', '#858796', '#fd7e14', '#6f42c1', '#20c9a6', '#5a5c69'], dataLabels: { enabled: true, formatter: function (val, opts) { const seriesIndex = opts.seriesIndex; const total = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0); const percent = total > 0 ? ((opts.w.config.series[seriesIndex] / total) * 100).toFixed(0) : 0; return percent > 5 ? `${percent}%` : ''; }, style: { fontSize: '10px', colors: ['#fff'] }, dropShadow: { enabled: true, top: 1, left: 1, blur: 1, opacity: 0.45 } } });
            const chart = new ApexCharts(element, options); chart.render(); return chart;
        }
        function initDonutChart(elementId, data, title = 'Source') {
            const element = document.getElementById(elementId); if (!element) { console.error(`Element not found: ${elementId}`); return null; }
            const options = deepMerge({}, defaultPieDonutOptions, { series: data.map(item => item.count), labels: data.map(item => item.label), chart: { type: 'donut', height: 300 }, colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#858796', '#e74a3b', '#fd7e14', '#6f42c1', '#20c9a6', '#5a5c69'], plotOptions: { pie: { donut: { size: '60%' } } }, dataLabels: { enabled: false } });
            const chart = new ApexCharts(element, options); chart.render(); return chart;
        }
        function initBarChart(elementId, data, title = 'Top List') {
             const element = document.getElementById(elementId); if (!element) { console.error(`Element not found: ${elementId}`); return null; }
             const options = deepMerge({}, defaultBarOptions, { series: [{ name: 'Count', data: data.map(item => item.count) }], xaxis: { categories: data.map(item => item.label) } });
             const chart = new ApexCharts(element, options); chart.render(); return chart;
        }
        function updatePieDonutChart(chartInstance, data) { if (chartInstance && data) { chartInstance.updateOptions({ series: data.map(item => item.count), labels: data.map(item => item.label) }); } }
        function updateBarChart(chartInstance, data) { if (chartInstance && data) { chartInstance.updateOptions({ xaxis: { categories: data.map(item => item.label) } }, false, false); chartInstance.updateSeries([{ data: data.map(item => item.count) }], true); } }

        async function fetchData() {
            showLoadingState(); clearError();
            let url = `/dashboard/filter?type=${currentViewType}`;
            if (currentRange === 'custom' && currentStartDate && currentEndDate) { url += `&start_date=${currentStartDate}&end_date=${currentEndDate}`; }
            else if (currentRange !== 'custom') { url += `&range=${currentRange}`; }
            else { displayError("Please select custom dates or choose a preset range."); hideLoadingState(); return; }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) console.warn("CSRF token missing!");

            try {
                const response = await fetch(url, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                if (!response.ok) {
                    let errorMsg = `HTTP error! Status: ${response.status} ${response.statusText}`;
                    try { const errorData = await response.json(); errorMsg = errorData.error || errorData.message || errorMsg; }
                    catch (e) { errorMsg += ` - Could not parse error response.`; }
                    throw new Error(errorMsg);
                }
                const data = await response.json();
                updateDashboardUI(data);
            } catch (error) {
                console.error('Fetch data failed:', error);
                displayError(`Failed to load dashboard data: ${error.message}. Please try again.`);
                resetUIOnError();
            } finally {
                hideLoadingState();
            }
        }

        function updateDashboardUI(data) {
            if (!data || !data.viewType) { displayError("Received invalid data from server."); resetUIOnError(); return; }
            const type = data.viewType;

            if(totalApplicationsCountEl) totalApplicationsCountEl.textContent = data.applications ?? 0;
            if(totalLeadsCountEl) totalLeadsCountEl.textContent = data.leads ?? 0;
            if(totalHotLeadsCountEl) totalHotLeadsCountEl.textContent = data.hotLeads ?? 0;
            if(totalRawLeadsCountEl) totalRawLeadsCountEl.textContent = data.rawLeads ?? 0;
            if(totalEnquiriesCountEl) totalEnquiriesCountEl.textContent = data.enquiries ?? 0;
            if(conversionRateCountEl) conversionRateCountEl.textContent = `${data.conversionRate ?? 0}%`;

            allChartCardWrappers.forEach(wrapper => {
                const isVisibleOverall = type === 'overall' && wrapper.classList.contains('dashboard-overall');
                const isVisibleForType = wrapper.classList.contains(`dashboard-${type}`);
                wrapper.classList.toggle('dashboard-hidden', !(isVisibleOverall || isVisibleForType));
            });

            document.getElementById('lead-status-chart-wrapper')?.classList.toggle('dashboard-hidden', type !== 'overall' && type !== 'lead' && type !== 'hotlead' && type !== 'rawlead');
            document.getElementById('application-status-chart-wrapper')?.classList.toggle('dashboard-hidden', type !== 'overall' && type !== 'application');
            document.getElementById('enquiry-status-chart-wrapper')?.classList.toggle('dashboard-hidden', type !== 'overall' && type !== 'enquiry');

            recentAppsRowEl.style.display = (type === 'overall' || type === 'application') ? '' : 'none';
            if (type === 'overall' || type === 'application') { populateRecentApplicationsPlaceholder(data); }
            else { if(recentAppsBodyEl) recentAppsBodyEl.innerHTML = ''; }

            updateCharts(data);

            trendTitleEl.textContent = `Data Trend (${capitalizeFirstLetter(type)})`;
            updateChartTitle('lead-status-chart-title', getDynamicTitle('Lead Status', type));
            updateChartTitle('lead-source-chart-title', getDynamicTitle('Top 5 Lead Sources', type));
            updateChartTitle('top-lead-courses-chart-title', getDynamicTitle('Top 5 Courses (Leads)', type));
            updateChartTitle('top-lead-creators-chart-title', getDynamicTitle('Top 5 Creators (Leads)', type));
            updateChartTitle('top-lead-intakes-chart-title', getDynamicTitle('Top 5 Intakes (Leads)', type));
            updateChartTitle('top-lead-countries-chart-title', getDynamicTitle('Top 5 Countries (Leads)', type));
            updateChartTitle('top-lead-universities-chart-title', getDynamicTitle('Top 5 Universities (Leads)', type));
        }

        function capitalizeFirstLetter(string) { return string.charAt(0).toUpperCase() + string.slice(1); }
        function getDynamicTitle(baseTitle, viewType) {
             let suffix = '(Overall)';
             if (viewType === 'hotlead') suffix = '(Hot)';
             else if (viewType === 'rawlead') suffix = '(Raw)';
             else if (viewType === 'lead') suffix = '(Total)';
             return baseTitle.replace('(Overall)', suffix);
        }
        function updateChartTitle(elementId, newTitle) {
            const titleEl = document.getElementById(elementId);
            if (titleEl) { titleEl.textContent = newTitle; }
            else { console.warn(`Title element not found: ${elementId}`); }
        }
        function populateRecentApplicationsPlaceholder(data) {
             if(recentAppsBodyEl) {
                 recentAppsBodyEl.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Recent applications data would load here.</td></tr>`;
                 // If you fetch/include recentApplications data, populate here
             }
        }
        function updateCharts(data) {
            const isVisible = (wrapperId) => { const el = document.getElementById(wrapperId); return el && !el.classList.contains('dashboard-hidden'); }

            if (data.trendData) {
                if (!trendChart) trendChart = initTrendChart('applications-trend-chart', data.trendData);
                else {
                    trendChart.updateOptions({ xaxis: { categories: data.trendData.labels || [] } }, false, false);
                    trendChart.updateSeries([ { name: 'Applications', data: data.trendData.applications || [] }, { name: 'Leads', data: data.trendData.leads || [] }, { name: 'Enquiries', data: data.trendData.enquiries || [] } ], true);
                }
            }

            if (isVisible('lead-status-chart-wrapper')) { if (!leadStatusPieChart) leadStatusPieChart = initPieChart('lead-status-chart', data.leadStatusData || []); else updatePieDonutChart(leadStatusPieChart, data.leadStatusData || []); } else if(leadStatusPieChart) { leadStatusPieChart.destroy(); leadStatusPieChart = null; }
            if (isVisible('application-status-chart-wrapper')) { if (!applicationStatusChart) applicationStatusChart = initPieChart('application-status-chart', data.applicationStatusData || []); else updatePieDonutChart(applicationStatusChart, data.applicationStatusData || []); } else if(applicationStatusChart) { applicationStatusChart.destroy(); applicationStatusChart = null; }
            if (isVisible('enquiry-status-chart-wrapper')) { if (!enquiryStatusChart) enquiryStatusChart = initPieChart('enquiry-status-chart', data.enquiryStatusData || []); else updatePieDonutChart(enquiryStatusChart, data.enquiryStatusData || []); } else if(enquiryStatusChart) { enquiryStatusChart.destroy(); enquiryStatusChart = null; }
            if (isVisible('lead-source-chart-wrapper')) { if (!leadSourceDonutChart) leadSourceDonutChart = initDonutChart('lead-source-chart', data.leadSourceData || []); else updatePieDonutChart(leadSourceDonutChart, data.leadSourceData || []); } else if(leadSourceDonutChart) { leadSourceDonutChart.destroy(); leadSourceDonutChart = null; }
            if (isVisible('enquiry-source-chart-wrapper')) { if (!enquirySourceDonutChart) enquirySourceDonutChart = initDonutChart('enquiry-source-chart', data.enquirySourceData || []); else updatePieDonutChart(enquirySourceDonutChart, data.enquirySourceData || []); } else if(enquirySourceDonutChart) { enquirySourceDonutChart.destroy(); enquirySourceDonutChart = null; }
            if (isVisible('university-chart-wrapper')) { if (!universityBarChart) universityBarChart = initBarChart('university-chart', data.universityData || []); else updateBarChart(universityBarChart, data.universityData || []); } else if(universityBarChart) { universityBarChart.destroy(); universityBarChart = null; }
            if (isVisible('top-courses-chart-wrapper')) { if (!topCoursesBarChart) topCoursesBarChart = initBarChart('top-courses-chart', data.topCoursesData || []); else updateBarChart(topCoursesBarChart, data.topCoursesData || []); } else if(topCoursesBarChart) { topCoursesBarChart.destroy(); topCoursesBarChart = null; }
            if (isVisible('top-app-assignees-chart-wrapper')) { if (!topAppAssigneesBarChart) topAppAssigneesBarChart = initBarChart('top-app-assignees-chart', data.topAppAssigneesData || []); else updateBarChart(topAppAssigneesBarChart, data.topAppAssigneesData || []); } else if(topAppAssigneesBarChart) { topAppAssigneesBarChart.destroy(); topAppAssigneesBarChart = null; }
            if (isVisible('top-lead-courses-chart-wrapper')) { if (!topLeadCoursesBarChart) topLeadCoursesBarChart = initBarChart('top-lead-courses-chart', data.topLeadCoursesData || []); else updateBarChart(topLeadCoursesBarChart, data.topLeadCoursesData || []); } else if(topLeadCoursesBarChart) { topLeadCoursesBarChart.destroy(); topLeadCoursesBarChart = null; }
            if (isVisible('top-lead-creators-chart-wrapper')) { if (!topLeadCreatorsBarChart) topLeadCreatorsBarChart = initBarChart('top-lead-creators-chart', data.topLeadCreatorsData || []); else updateBarChart(topLeadCreatorsBarChart, data.topLeadCreatorsData || []); } else if(topLeadCreatorsBarChart) { topLeadCreatorsBarChart.destroy(); topLeadCreatorsBarChart = null; }
            if (isVisible('top-enquiry-countries-chart-wrapper')) { if (!topEnquiryCountriesBarChart) topEnquiryCountriesBarChart = initBarChart('top-enquiry-countries-chart', data.topEnquiryCountriesData || []); else updateBarChart(topEnquiryCountriesBarChart, data.topEnquiryCountriesData || []); } else if(topEnquiryCountriesBarChart) { topEnquiryCountriesBarChart.destroy(); topEnquiryCountriesBarChart = null; }

            if (isVisible('top-app-intakes-chart-wrapper')) { if (!topAppIntakesBarChart) topAppIntakesBarChart = initBarChart('top-app-intakes-chart', data.topAppIntakesData || []); else updateBarChart(topAppIntakesBarChart, data.topAppIntakesData || []); } else if(topAppIntakesBarChart) { topAppIntakesBarChart.destroy(); topAppIntakesBarChart = null; }
            if (isVisible('top-app-locations-chart-wrapper')) { if (!topAppLocationsBarChart) topAppLocationsBarChart = initBarChart('top-app-locations-chart', data.topAppLocationsData || []); else updateBarChart(topAppLocationsBarChart, data.topAppLocationsData || []); } else if(topAppLocationsBarChart) { topAppLocationsBarChart.destroy(); topAppLocationsBarChart = null; }
            if (isVisible('top-app-sources-chart-wrapper')) { if (!topAppSourcesBarChart) topAppSourcesBarChart = initDonutChart('top-app-sources-chart', data.topAppSourcesData || []); else updatePieDonutChart(topAppSourcesBarChart, data.topAppSourcesData || []); } else if(topAppSourcesBarChart) { topAppSourcesBarChart.destroy(); topAppSourcesBarChart = null; }
            if (isVisible('top-lead-intakes-chart-wrapper')) { if (!topLeadIntakesBarChart) topLeadIntakesBarChart = initBarChart('top-lead-intakes-chart', data.topLeadIntakesData || []); else updateBarChart(topLeadIntakesBarChart, data.topLeadIntakesData || []); } else if(topLeadIntakesBarChart) { topLeadIntakesBarChart.destroy(); topLeadIntakesBarChart = null; }
            if (isVisible('top-lead-countries-chart-wrapper')) { if (!topLeadCountriesBarChart) topLeadCountriesBarChart = initBarChart('top-lead-countries-chart', data.topLeadCountriesData || []); else updateBarChart(topLeadCountriesBarChart, data.topLeadCountriesData || []); } else if(topLeadCountriesBarChart) { topLeadCountriesBarChart.destroy(); topLeadCountriesBarChart = null; }
            if (isVisible('top-lead-universities-chart-wrapper')) { if (!topLeadUniversitiesBarChart) topLeadUniversitiesBarChart = initBarChart('top-lead-universities-chart', data.topLeadUniversitiesData || []); else updateBarChart(topLeadUniversitiesBarChart, data.topLeadUniversitiesData || []); } else if(topLeadUniversitiesBarChart) { topLeadUniversitiesBarChart.destroy(); topLeadUniversitiesBarChart = null; }
            if (isVisible('top-enquiry-education-chart-wrapper')) { if (!topEnquiryEducationBarChart) topEnquiryEducationBarChart = initBarChart('top-enquiry-education-chart', data.topEnquiryEducationData || []); else updateBarChart(topEnquiryEducationBarChart, data.topEnquiryEducationData || []); } else if(topEnquiryEducationBarChart) { topEnquiryEducationBarChart.destroy(); topEnquiryEducationBarChart = null; }
        }

        function setupFilters() {
             typeFilterButtons.forEach(button => {
                 button.addEventListener('click', function() {
                     if (this.classList.contains('active')) return;
                     typeFilterButtons.forEach(btn => btn.classList.remove('active'));
                     this.classList.add('active');
                     currentViewType = this.getAttribute('data-type'); fetchData();
                 });
             });
            dateFilterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (this.classList.contains('active') && this.getAttribute('data-range') !== 'custom') return;
                    const range = this.getAttribute('data-range');
                    dateFilterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active'); currentRange = range;
                    if (currentRange === 'custom') {
                        customDateContainer.style.display = 'block';
                        if (!startDateInput.value) { startDateInput.value = moment().subtract(6, 'days').format('YYYY-MM-DD'); }
                        if (!endDateInput.value) { endDateInput.value = moment().format('YYYY-MM-DD'); }
                        startDateInput.focus();
                    } else {
                        customDateContainer.style.display = 'none';
                        currentStartDate = null; currentEndDate = null; fetchData();
                    }
                });
            });
             applyCustomRangeBtn.addEventListener('click', function() {
                 const startDate = startDateInput.value; const endDate = endDateInput.value;
                 if (startDate && endDate) {
                     const start = moment(startDate); const end = moment(endDate);
                     if (!start.isValid() || !end.isValid()) { displayError('Invalid date format selected.'); return; }
                     if (end.isBefore(start)) { displayError('End date cannot be earlier than start date.'); return; }
                     currentStartDate = startDate; currentEndDate = endDate; currentRange = 'custom';
                     dateFilterButtons.forEach(btn => btn.classList.toggle('active', btn.getAttribute('data-range') === 'custom'));
                     fetchData();
                 } else { displayError('Please select both start and end dates for the custom range.'); }
             });
             const today = moment().format('YYYY-MM-DD');
             startDateInput.max = today; endDateInput.max = today;
             startDateInput.value = moment().subtract(6, 'days').format('YYYY-MM-DD');
             endDateInput.value = today;
             dateFilterButtons.forEach(btn => btn.classList.toggle('active', btn.getAttribute('data-range') === currentRange));
        }

        function showLoadingState() {
            document.querySelectorAll('.chart-container').forEach(c => c.classList.add('loading-overlay'));
            document.querySelectorAll('.type-filter, .date-filter, #apply-custom-range, #start-date, #end-date').forEach(el => el.disabled = true);
            document.body.style.cursor = 'wait';
            if(recentAppsBodyEl) { recentAppsBodyEl.innerHTML = '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div><span class="ms-2">Loading...</span></td></tr>'; }
        }
        function hideLoadingState() {
            document.querySelectorAll('.chart-container').forEach(c => c.classList.remove('loading-overlay'));
            document.querySelectorAll('.type-filter, .date-filter, #apply-custom-range, #start-date, #end-date').forEach(el => el.disabled = false);
            document.body.style.cursor = 'default';
        }
        function displayError(message) {
             console.error("Dashboard Error:", message);
             if(errorContainer) {
                 clearError();
                 const errorDiv = document.createElement('div');
                 errorDiv.className = 'alert alert-danger alert-dismissible fade show mt-3 mb-3';
                 errorDiv.role = 'alert'; errorDiv.id = 'dashboard-error-alert-instance';
                 errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i><strong>Error:</strong> ${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                 errorContainer.appendChild(errorDiv);
             } else { alert(message); }
        }
        function clearError() { if(errorContainer) { const existingError = errorContainer.querySelector('#dashboard-error-alert-instance'); if (existingError) existingError.remove(); } }
        function resetUIOnError() {
             if(totalApplicationsCountEl) totalApplicationsCountEl.textContent = '-';
             if(totalLeadsCountEl) totalLeadsCountEl.textContent = '-';
             if(totalHotLeadsCountEl) totalHotLeadsCountEl.textContent = '-';
             if(totalRawLeadsCountEl) totalRawLeadsCountEl.textContent = '-';
             if(totalEnquiriesCountEl) totalEnquiriesCountEl.textContent = '-';
             if(conversionRateCountEl) conversionRateCountEl.textContent = '-%';

             const errorData = [{ label: 'Error', count: 0 }]; const errorDataBar = [{ label: 'Error', count: 0 }];

             if (trendChart) trendChart.updateSeries([{ data: [] }, { data: [] }, { data: [] }]);
             if (leadStatusPieChart) updatePieDonutChart(leadStatusPieChart, errorData);
             if (applicationStatusChart) updatePieDonutChart(applicationStatusChart, errorData);
             if (enquiryStatusChart) updatePieDonutChart(enquiryStatusChart, errorData);
             if (leadSourceDonutChart) updatePieDonutChart(leadSourceDonutChart, errorData);
             if (enquirySourceDonutChart) updatePieDonutChart(enquirySourceDonutChart, errorData);
             if (universityBarChart) updateBarChart(universityBarChart, errorDataBar);
             if (topCoursesBarChart) updateBarChart(topCoursesBarChart, errorDataBar);
             if (topAppAssigneesBarChart) updateBarChart(topAppAssigneesBarChart, errorDataBar);
             if (topLeadCoursesBarChart) updateBarChart(topLeadCoursesBarChart, errorDataBar);
             if (topLeadCreatorsBarChart) updateBarChart(topLeadCreatorsBarChart, errorDataBar);
             if (topEnquiryCountriesBarChart) updateBarChart(topEnquiryCountriesBarChart, errorDataBar);
             if (topAppIntakesBarChart) updateBarChart(topAppIntakesBarChart, errorDataBar);
             if (topAppLocationsBarChart) updateBarChart(topAppLocationsBarChart, errorDataBar);
             if (topAppSourcesBarChart) updatePieDonutChart(topAppSourcesBarChart, errorData);
             if (topLeadIntakesBarChart) updateBarChart(topLeadIntakesBarChart, errorDataBar);
             if (topLeadCountriesBarChart) updateBarChart(topLeadCountriesBarChart, errorDataBar);
             if (topLeadUniversitiesBarChart) updateBarChart(topLeadUniversitiesBarChart, errorDataBar);
             if (topEnquiryEducationBarChart) updateBarChart(topEnquiryEducationBarChart, errorDataBar);

             if(recentAppsBodyEl) { recentAppsBodyEl.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Failed to load dashboard data.</td></tr>'; }
        }

        if (!document.querySelector('meta[name="csrf-token"]')) { console.warn("CSRF token meta tag is missing."); }
        try { setupFilters(); fetchData(); }
        catch (error) {
            console.error("CRITICAL ERROR during dashboard initialization:", error);
            displayError("A critical error occurred initializing the dashboard. Check the browser console or contact support.");
            allChartCardWrappers.forEach(wrapper => wrapper.classList.add('dashboard-hidden'));
            if(recentAppsRowEl) recentAppsRowEl.style.display = 'none';
        }
    });
</script>
@endsection