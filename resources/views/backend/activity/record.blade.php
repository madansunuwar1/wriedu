@extends('layouts.admin')

@section('title', isset($selectedUser) ? $selectedUser->name."'s Activity Logs" : 'Activity Logs')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center">
            <div class="me-3 bg-primary bg-opacity-10 p-3 rounded">
                <i class="fas fa-history fs-4 text-primary"></i>
            </div>
            <div>
                <h1 class="h4 mb-0">Activity Logs</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Time Period Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <form method="GET" action="" id="filterForm">
                        <div class="row align-items-center">
                            
                            <div class="col-md-13 ">
                                <div class="d-flex  justify-content-md-end">
                                    <span class="text-muted me-2">Custom Range:</span>
                                    <div class="input-group input-group-sm" style="width: 280px;">
                                        <input type="date" 
                                               name="date_from" 
                                               class="form-control" 
                                               value="{{ request('date_from') }}"
                                               placeholder="Start Date">
                                        <span class="input-group-text">to</span>
                                        <input type="date" 
                                               name="date_to" 
                                               class="form-control" 
                                               value="{{ request('date_to') }}"
                                               placeholder="End Date">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="period" id="selectedPeriod" value="{{ request('period') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Total Activities</h6>
                            <h3 class="mb-0">{{ $activityStats['total'] ?? $logs->total() ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Created</h6>
                            <h3 class="mb-0">{{ $activityStats['created'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-plus-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Status Updated</h6>
                            <h3 class="mb-0">{{ $activityStats['updated'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-edit text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">commented</h6>
                            <h3 class="mb-0">{{ $activityStats['commented'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-cog text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Activity Logs</h5>
                @if($logs && $logs->count() > 0)
                    <div class="text-muted small">
                        Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} records
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card-body p-0">
            @if ($logs && $logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">User</th>
                                <th>Action</th>
                                <th>Subject</th>
                                <th>Date & Time</th>
                                <th class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                @php
                                    $actionColors = [
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        'login' => 'info',
                                        'logout' => 'secondary',
                                        'commented' => 'info',
                                        'status_updated' => 'primary',
                                        'forwarded' => 'info',
                                    ];
                                    $actionIcons = [
                                        'created' => 'plus-circle',
                                        'updated' => 'edit',
                                        'deleted' => 'trash-alt',
                                        'login' => 'sign-in-alt',
                                        'logout' => 'sign-out-alt',
                                        'commented' => 'comments',
                                        'status_updated' => 'file-alt',
                                        'forwarded' => 'share',
                                    ];
                                    $description = strtolower($log->description);
                                    $badgeColor = $actionColors[$description] ?? 'primary';
                                    $actionIcon = $actionIcons[$description] ?? 'cog';
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        @if ($log->causer)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $log->causer->name }}</h6>
                                                    <small class="text-muted">{{ $log->causer->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title bg-secondary bg-opacity-10 text-secondary rounded-circle">
                                                        <i class="fas fa-server"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">System</h6>
                                                    <small class="text-muted">Automated</small>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $badgeColor }} rounded-pill">
                                            <i class="fas fa-{{ $actionIcon }} me-1"></i>
                                            {{ ucfirst($log->description) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ class_basename($log->subject_type ?? 'Unknown') }}
                                            @if($log->subject_id)
                                                #{{ $log->subject_id }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="fw-medium">{{ $log->created_at->format('M j, Y') }}</small><br>
                                            <small class="text-muted">{{ $log->created_at->format('g:i A') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($log->properties && count($log->properties) > 0)
                                            <button class="btn btn-sm btn-outline-primary rounded-pill" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailsModal{{ $log->id }}">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>
                                        @else
                                            <span class="badge bg-light text-muted">No details</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox fa-3x text-muted"></i>
                    </div>
                    <h5 class="mb-2">No Activity Logs Found</h5>
                    <p class="text-muted mb-4">There are no activity logs for the selected time period.</p>
                    <button class="btn btn-primary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            @endif
        </div>
        
        @if ($logs && $logs->count() > 0)
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
                    </div>
                    <div>
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Details Modals -->
@if($logs && $logs->count() > 0)
    @foreach ($logs as $log)
        @if ($log->properties && count($log->properties) > 0)
            <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <!-- Modal Header -->
                        <div class="modal-header bg-gradient-primary text-white border-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-20 p-2 rounded-circle me-3">
                                    <i class="fas fa-clipboard-list fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title mb-0 fw-bold">Activity Details</h5>
                                    <small class="opacity-75">Detailed information about this activity</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body p-0">
                            <!-- Activity Overview Card -->
                            <div class="bg-light border-bottom">
                                <div class="p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <div>
                                                    <label class="text-muted small mb-1">Performed By</label>
                                                    <div class="fw-semibold">{{ optional($log->causer)->name ?? 'System' }}</div>
                                                    @if($log->causer)
                                                        <small class="text-muted">{{ $log->causer->email }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                                    <i class="fas fa-bolt text-success"></i>
                                                </div>
                                                <div>
                                                    <label class="text-muted small mb-1">Action Type</label>
                                                    <div class="fw-semibold">{{ ucfirst($log->description) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                                    <i class="fas fa-cube text-info"></i>
                                                </div>
                                                <div>
                                                    <label class="text-muted small mb-1">Subject</label>
                                                    <div class="fw-semibold">{{ class_basename($log->subject_type ?? 'Unknown') }}</div>
                                                    @if($log->subject_id)
                                                        <small class="text-muted">ID: {{ $log->subject_id }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                                    <i class="fas fa-clock text-warning"></i>
                                                </div>
                                                <div>
                                                    <label class="text-muted small mb-1">Timestamp</label>
                                                    <div class="fw-semibold">{{ $log->created_at->format('M j, Y') }}</div>
                                                    <small class="text-muted">{{ $log->created_at->format('g:i A') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Details -->
                            <div class="p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-list-ul text-primary me-2"></i>
                                        <h6 class="mb-0 fw-bold">Activity Properties</h6>
                                    </div>
                                    <span class="badge bg-light text-dark">{{ count($log->properties) }} {{ count($log->properties) == 1 ? 'Property' : 'Properties' }}</span>
                                </div>
                                
                                <!-- Properties Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover table-borderless">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-semibold text-muted" style="width: 30%;">Property</th>
                                                <th class="fw-semibold text-muted">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($log->properties as $key => $value)
                                                <tr>
                                                    <td class="fw-medium text-dark">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-key text-primary me-2 fs-6"></i>
                                                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if(is_array($value) || is_object($value))
                                                            <div class="bg-light rounded p-2">
                                                                <small class="text-muted d-block mb-1">Complex Data:</small>
                                                                <code class="text-dark">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                                                            </div>
                                                        @elseif(is_bool($value))
                                                            <span class="badge bg-{{ $value ? 'success' : 'danger' }} rounded-pill">
                                                                <i class="fas fa-{{ $value ? 'check' : 'times' }} me-1"></i>
                                                                {{ $value ? 'True' : 'False' }}
                                                            </span>
                                                        @elseif(is_null($value))
                                                            <span class="text-muted fst-italic">
                                                                <i class="fas fa-minus-circle me-1"></i>
                                                                null
                                                            </span>
                                                        @elseif(is_numeric($value))
                                                            <span class="badge bg-info rounded-pill">
                                                                <i class="fas fa-hashtag me-1"></i>
                                                                {{ number_format($value) }}
                                                            </span>
                                                        @elseif(filter_var($value, FILTER_VALIDATE_EMAIL))
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-envelope text-primary me-2"></i>
                                                                <a href="mailto:{{ $value }}" class="text-decoration-none">{{ $value }}</a>
                                                            </div>
                                                        @elseif(filter_var($value, FILTER_VALIDATE_URL))
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-link text-primary me-2"></i>
                                                                <a href="{{ $value }}" target="_blank" class="text-decoration-none">{{ Str::limit($value, 50) }}</a>
                                                            </div>
                                                        @else
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-quote-left text-muted me-2 fs-6"></i>
                                                                <span class="text-dark">{{ $value }}</span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Additional Info -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="alert alert-info border-0 bg-info bg-opacity-10">
                                            <div class="d-flex">
                                                <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                                <div>
                                                    <strong class="text-info">Information:</strong>
                                                    <p class="mb-0 small text-muted">
                                                        This activity was recorded on {{ $log->created_at->format('F j, Y \a\t g:i A') }} 
                                                        and contains the complete details of the performed action.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer border-top bg-light">
                            <div class="d-flex justify-content-between w-100">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Activity ID: {{ $log->id }}
                                </small>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                    <i class="fas fa-check me-2"></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

@push('styles')
<style>
.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.period-btn {
    transition: all 0.2s ease;
}

.period-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Professional Modal Styles */
.modal-content {
    border-radius: 12px;
    overflow: hidden;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

/* Properties Table Styles */
.table-borderless td {
    border: none;
    padding: 0.75rem;
    vertical-align: middle;
}

.table-borderless tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.table-borderless tr:hover td:first-child {
    border-left: 3px solid #007bff;
    padding-left: calc(0.75rem - 3px);
}

.table-responsive {
    border-radius: 8px;
    border: 1px solid #e9ecef;
    max-height: 400px;
}

code {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.85rem;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    background-color: #f8f9fa;
    white-space: pre-wrap;
    word-break: break-word;
}

.card {
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Modal animation */
.modal.fade .modal-dialog {
    transform: translate(0, -50px);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: translate(0, 0);
}

@media (max-width: 768px) {
    .row.align-items-center {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .period-btn {
        flex: 1;
        font-size: 0.8rem;
    }
    
    .input-group {
        width: 100% !important;
    }
    
    .modal-lg {
        max-width: 95%;
    }
    
    .activity-json {
        font-size: 0.75rem;
        max-height: 200px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Period button functionality
    const periodButtons = document.querySelectorAll('.period-btn');
    const periodInput = document.getElementById('selectedPeriod');
    const dateFromInput = document.querySelector('input[name="date_from"]');
    const dateToInput = document.querySelector('input[name="date_to"]');
    const filterForm = document.getElementById('filterForm');

    // Period button click handlers
    periodButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const period = this.dataset.period;
            
            // Update button states
            periodButtons.forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
            
            // Clear custom dates
            dateFromInput.value = '';
            dateToInput.value = '';
            
            // Set period and submit form
            periodInput.value = period;
            filterForm.submit();
        });
    });

    // Custom date change handlers
    [dateFromInput, dateToInput].forEach(input => {
        input.addEventListener('change', function() {
            if (dateFromInput.value || dateToInput.value) {
                // Clear period selection
                periodButtons.forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                });
                periodInput.value = '';
            }
        });
    });

    // Form validation
    filterForm.addEventListener('submit', function(e) {
        const startDate = dateFromInput.value;
        const endDate = dateToInput.value;
        
        if (startDate && endDate) {
            if (new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                alert('Start date cannot be later than end date.');
                return false;
            }
        }
    });
});
</script>
@endpush

@endsection