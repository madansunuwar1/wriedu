@extends('layouts.admin')
@include('backend.script.session')
@include('backend.script.alert')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
@if(session('success'))
<script>
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'OK'
    });
</script>
@endif
<div class="container-fluid px-4 py-3">
    <!-- Enhanced Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-coins-outline text-primary fs-4 me-3"></i>
                                <div>
                                    <h1 class="h3 mb-1">Commission Management</h1>
                                    <p class="text-muted mb-0">Manage and track commission payments and records</p>
                                </div>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="#" class="text-decoration-none">
                                            <i class="ti ti-home me-1"></i>Dashboard
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Commission Management</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Refresh Data">
                                <i class="ti ti-refresh me-1"></i>Refresh
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Export Report">
                                <i class="ti ti-file-export me-1"></i>Report
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="ti ti-settings me-1"></i>Settings
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-user-cog me-2"></i>User Preferences</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-bell me-2"></i>Notifications</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-help me-2"></i>Help & Support</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card border shadow-sm">
        <div class="card-header border-bottom pb-3 pt-4 px-4">
            <ul class="nav nav-tabs" id="commissionTabs" role="tablist">
                <li class="nav-item me-3" role="presentation">
                    <button class="nav-link active d-flex align-items-center fw-semibold" id="payable-tab" data-bs-toggle="tab" data-bs-target="#payable" type="button" role="tab" aria-controls="payable" aria-selected="true">
                        <i class="ti ti-coins text-info me-3"></i>
                        <span>Commission Payable</span>
                        <span class="badge bg-primary ms-3 rounded-pill">{{ count($commission_payable) }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center fw-semibold" id="commission-tab" data-bs-toggle="tab" data-bs-target="#commission" type="button" role="tab" aria-controls="commission" aria-selected="false">
                        <i class="ti ti-percentage text-info me-3"></i>
                        <span>Commission Records</span>
                        <span class="badge bg-secondary ms-3 rounded-pill">{{ count($comissions) }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="commissionTabContent">

                <!-- Commission Payable Tab -->
                <div class="tab-pane fade show active" id="payable" role="tabpanel" aria-labelledby="payable-tab">
                    <div class="row mb-4 g-3">
                        <div class="col-lg-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" onkeyup="filterTable('payableTable')" placeholder="Search by university, product, partner...">
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle me-2" type="button" data-bs-toggle="dropdown">Filter</button>
                            <button class="btn btn-outline-primary btn-sm me-2">Import</button>
                            <button class="btn btn-primary btn-sm">Export</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="payableTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>University</th>
                                    <th>Product</th>
                                    <th>Partner</th>
                                    <th>Commission Types</th>
                                    <th class="text-center">Bonus</th>
                                    <th class="text-center">Intensive</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($commission_payable as $commission)
                                <tr onclick="window.location='{{ route('backend.commission.payable.record', $commission->id) }}';" style="cursor: pointer;">
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td><i class="ti ti-school-outline text-primary me-2"></i>{{ $commission->university }}</td>
                                    <td><span class="badge badge-outline border text-primary border-secondary">{{ $commission->product }}</span></td>
                                    <td>{{ $commission->partner }}</td>
                                    <td class="py-3">
                                        <div class="d-flex flex-wrap gap-1">
                                            @if(is_null($commission->commission_types))
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    <i class="ti ti-minus me-1"></i>N/A
                                                </span>
                                            @elseif(is_array($commission->commission_types))
                                                @foreach($commission->commission_types as $type => $data)
                                                    @if(is_array($data) && isset($data['value']))
                                                        <span class="badge text-success border border-secondary rounded-pill">
                                                            <i class="ti ti-percentage me-1"></i>{{ $type }}: {{ $data['value'] }}
                                                        </span>
                                                    @else
                                                        <span class="badge text-success border border-secondary rounded-pill">
                                                            <i class="ti ti-percentage me-1"></i>{{ $type }}: {{ $data }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @elseif(is_string($commission->commission_types))
                                                <span class="badge text-success border border-secondary rounded-pill">
                                                    <i class="ti ti-percentage me-1"></i>{{ $commission->commission_types }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger border border-secondary text-red-600">
                                                    <i class="ti ti-alert-triangle me-1"></i>Invalid Data
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        @if($commission->bonus_commission > 0)
                                        <span class="d-flex align-items-center justify-content-center gap-2 text-success fw-medium">
                                            <i class="ti ti-check" style="color: #198754; font-size: 1.25rem;"></i>Bonus Available
                                        </span>
                                        @else
                                        <span class="d-flex align-items-center justify-content-center gap-2 fw-medium" style="color: #dc3545;">
                                            <i class="ti ti-x" style="color: #dc3545; font-size: 1.25rem;"></i>No Bonus
                                        </span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3">
                                        @if($commission->intensive_commission > 0)
                                        <span class="d-flex align-items-center justify-content-center gap-2 text-warning fw-medium">
                                            <i class="ti ti-check" style="color: #ffc107; font-size: 1.25rem;"></i>Incentive
                                        </span>
                                        @else
                                        <span class="d-flex align-items-center justify-content-center gap-2 fw-medium" style="color: #dc3545;">
                                            <i class="ti ti-x" style="color: #dc3545; font-size: 1.25rem;"></i>Standard
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Commission Records Tab -->
                <div class="tab-pane fade" id="commission" role="tabpanel" aria-labelledby="commission-tab">
                    <div class="row mb-4 g-3">
                        <div class="col-lg-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" onkeyup="filterTable('commissionTable')" placeholder="Search by university, product, partner...">
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle me-2" type="button" data-bs-toggle="dropdown">Filter</button>
                            <button class="btn btn-outline-primary btn-sm me-2">Import</button>
                            <button class="btn btn-primary btn-sm">Export</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="commissionTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>University</th>
                                    <th>Product</th>
                                    <th>Partner</th>
                                    <th>Commission Types</th>
                                    <th class="text-center">Bonus</th>
                                    <th class="text-center">Intensive</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($comissions as $commission)
                                <tr onclick="window.location='{{ route('backend.comission.record', $commission->id) }}';" style="cursor: pointer;">
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td><i class="ti ti-school-outline text-primary me-2"></i>{{ $commission->university }}</td>
                                    <td><span class="badge badge-outline border text-primary border-secondary">{{ $commission->product }}</span></td>
                                    <td>{{ $commission->partner }}</td>
                                    <td class="py-3">
                                        <div class="d-flex flex-wrap gap-1">
                                            @if(is_null($commission->commission_types))
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    <i class="ti ti-minus me-1"></i>N/A
                                                </span>
                                            @elseif(is_array($commission->commission_types))
                                                @foreach($commission->commission_types as $type => $data)
                                                    @if(is_array($data) && isset($data['value']))
                                                        <span class="badge text-success border border-secondary rounded-pill">
                                                            <i class="ti ti-percentage me-1"></i>{{ $type }}: {{ $data['value'] }}
                                                        </span>
                                                    @else
                                                        <span class="badge text-success border border-secondary rounded-pill">
                                                            <i class="ti ti-percentage me-1"></i>{{ $type }}: {{ $data }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @elseif(is_string($commission->commission_types))
                                                <span class="badge text-success border border-secondary rounded-pill">
                                                    <i class="ti ti-percentage me-1"></i>{{ $commission->commission_types }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger border border-secondary text-red-600">
                                                    <i class="ti ti-alert-triangle me-1"></i>Invalid Data
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        @if($commission->bonus_commission > 0)
                                        <span class="d-flex align-items-center justify-content-center gap-2 text-success fw-medium">
                                            <i class="ti ti-check" style="color: #198754; font-size: 1.25rem;"></i>Bonus Available
                                        </span>
                                        @else
                                        <span class="d-flex align-items-center justify-content-center gap-2 fw-medium" style="color: #dc3545;">
                                            <i class="ti ti-x" style="color: #dc3545; font-size: 1.25rem;"></i>No Bonus
                                        </span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3">
                                        @if($commission->intensive_commission > 0)
                                        <span class="d-flex align-items-center justify-content-center gap-2 text-warning fw-medium">
                                            <i class="ti ti-check" style="color: #ffc107; font-size: 1.25rem;"></i>Incentive
                                        </span>
                                        @else
                                        <span class="d-flex align-items-center justify-content-center gap-2 fw-medium" style="color: #dc3545;">
                                            <i class="ti ti-x" style="color: #dc3545; font-size: 1.25rem;"></i>Standard
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modern Card Styles */
    .card {
        border-radius: 0.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    /* Table Styles */
    .table {
        --bs-table-striped-bg: rgba(var(--bs-light-rgb), 0.35);
        --bs-table-hover-bg: rgba(var(--bs-primary-rgb), 0.075);
        margin-bottom: 0;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: var(--bs-gray-700);
        background-color: var(--bs-light);
        border-bottom-width: 1px;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem;
        border-top: 1px solid var(--bs-gray-200);
    }

    /* Badge Styles */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    /* Button Styles */
    .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .btn-group .btn {
        border-radius: 0.375rem 0 0 0.375rem;
    }

    .btn-group .dropdown-toggle {
        border-radius: 0 0.375rem 0.375rem 0;
    }

    /* Dropdown Styles */
    .dropdown-menu {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
        padding: 0.5rem;
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--bs-primary);
    }

    /* Nav Tabs Styles */
    .nav-tabs {
        border-bottom: none;
    }

    .nav-tabs .nav-link {
        border: none;
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 1rem 1.5rem;
        color: var(--bs-gray-700);
        font-weight: 500;
        transition: all 0.2s ease;
        margin-bottom: -1px;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent;
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--bs-primary);
    }

    .nav-tabs .nav-link.active {
        background-color: var(--bs-white);
        color: var(--bs-primary);
        border-bottom: 3px solid var(--bs-primary);
    }

    /* Search Bar Styles */
    .search-container .input-group-text {
        background-color: var(--bs-light);
        border: none;
        padding: 0.75rem 1rem;
    }

    .search-container .form-control {
        border: none;
        background-color: var(--bs-light);
        padding: 0.75rem;
        font-size: 0.875rem;
    }

    .search-container .form-control:focus {
        box-shadow: none;
        background-color: var(--bs-light);
    }

    /* Empty State Styles */
    .empty-state {
        text-align: center;
        padding: 2rem;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: rgba(var(--bs-light-rgb), 0.5);
        color: var(--bs-secondary);
        font-size: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 767.98px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            margin-top: 1rem;
            width: 100%;
            justify-content: flex-end;
        }

        .nav-tabs .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }

        .search-container .input-group {
            width: 100%;
        }
    }

    <script> // Enhanced table filtering with better UX

    function filterTable(tableId) {
        const searchId=tableId==='payableTable' ? 'searchInputPayable': 'searchInputCommission';
        const input=document.getElementById(searchId);
        const filter=input.value.toUpperCase();
        const table=document.getElementById(tableId);
        const rows=table.getElementsByTagName("tr");
        let visibleRows=0;

        // Skip header row
        for (let i=1; i < rows.length; i++) {
            let found=false;
            const cells=rows[i].getElementsByTagName("td");

            for (let j=0; j < cells.length - 1; j++) {

                // Exclude actions column
                if (cells[j]) {
                    const txtValue=cells[j].textContent || cells[j].innerText;

                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found=true;
                        break;
                    }
                }
            }

            if (found) {
                rows[i].style.display="";
                visibleRows++;
            }

            else {
                rows[i].style.display="none";
            }
        }

        // Show/hide empty state
        updateEmptyState(tableId, visibleRows===0 && filter !=='');
    }

    function updateEmptyState(tableId, show) {
        const table=document.getElementById(tableId).closest('.tab-pane');
        const emptyRow=table.querySelector('tr.empty-state-row');

        if (show) {
            if ( !emptyRow) {
                const newRow=document.createElement('tr');
                newRow.className='empty-state-row';
                newRow.innerHTML=` <td colspan="8" class="text-center py-5"><div class="empty-state d-flex flex-column align-items-center"><div class="empty-icon bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                style="width: 80px; height: 80px;"><i class="ti ti-database-off text-muted" style="font-size: 2rem;"></i></div><h5 class="text-muted fw-semibold mb-2">No Matching Records Found</h5><p class="text-muted mb-3">Try adjusting your search term or filters.</p></div></td>`;
                table.querySelector('tbody').appendChild(newRow);
            }
        }

        else if (emptyRow) {
            emptyRow.remove();
        }
    }

    // Modal functions for import

    function clearSearch(inputId, tableId) {
        document.getElementById(inputId).value="";
        filterTable(tableId);
    }

    // CRUD Operations for Commission Payable
    function editPayableRecord(id) {
        window.location.href=`/backend/commission/payable/$ {
            id
        }

        /edit`;
    }

    function duplicatePayableRecord(id) {
        $.ajax({
            url: `/commission-payable/$ {
                id
            }

            /duplicate`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            }

            ,
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Record duplicated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'

                }).then(()=> {
                    location.reload();
                });
        }

        ,
        error: function(xhr) {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to duplicate record.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
    }
    });
    }

    function markAsPaid(id) {
        Swal.fire({
            title: 'Mark as Paid?',
            text: "This will move the record to Commission Records and remove it from Payable.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, mark as paid!'

        }).then((result)=> {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/commission-payable/$ {
                        id
                    }

                    /mark-as-paid`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }

                    ,
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Record marked as paid successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'

                        }).then(()=> {
                            location.reload();
                        });
                }

                ,
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to mark record as paid.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
            }
        });
    }
    });
    }

    function deletePayableRecord(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'

        }).then((result)=> {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/commission-payable/$ {
                        id
                    }

                    `,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }

                    ,
                    success: function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Record has been deleted.',
                            icon: 'success',
                            confirmButtonText: 'OK'

                        }).then(()=> {
                            location.reload();
                        });
                }

                ,
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to delete record.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
            }
        });
    }
    });
    }

    // CRUD Operations for Commission Records
    function editCommissionRecord(id) {
        window.location.href=`/backend/commission/$ {
            id
        }

        /edit`;
    }

    function duplicateCommissionRecord(id) {
        $.ajax({
            url: `/commission/$ {
                id
            }

            /duplicate`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            }

            ,
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Record duplicated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'

                }).then(()=> {
                    location.reload();
                });
        }

        ,
        error: function(xhr) {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to duplicate record.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
    }
    });
    }

    function deleteCommissionRecord(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'

        }).then((result)=> {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/commission/$ {
                        id
                    }

                    `,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }

                    ,
                    success: function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Record has been deleted.',
                            icon: 'success',
                            confirmButtonText: 'OK'

                        }).then(()=> {
                            location.reload();
                        });
                }

                ,
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to delete record.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
            }
        });
    }
    });
    }

    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList=[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

            const tooltipList=tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
        });
    </script>@endsection