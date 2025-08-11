@extends('layouts.admin')
@include('backend.script.session')
@include('backend.script.alert')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@if(session('success'))
<script>
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'swal-custom-popup',
            confirmButton: 'swal-custom-ok-button'
        }
    });
</script>
@endif
<style>
    .main-container {  background-color: #f8f9fa; }
    .icon-btn {
        position: relative; display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; border-radius: 8px; border: 1px solid #dee2e6;
        background: white; color: #6c757d; text-decoration: none; transition: all 0.2s ease; cursor: pointer;
    }
    .icon-btn:hover {
        background: #f8f9fa; border-color: #adb5bd; color: #495057;
        transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .icon-btn.btn-success:hover { background: #198754; border-color: #198754; color: white; }
    .icon-btn.btn-primary:hover { background: #0d6efd; border-color: #0d6efd; color: white; }
    .icon-btn.btn-outline-primary:hover { background: #0d6efd; border-color: #0d6efd; color: white; }
    .icon-btn.btn-outline-secondary:hover { background: #6c757d; border-color: #6c757d; color: white; }
    .icon-btn.btn-outline-danger:hover { background: #dc3545; border-color: #dc3545; color: white; }
    .tooltip-text {
        position: absolute; bottom: -35px; left: 50%; transform: translateX(-50%);
        background: #333; color: white; padding: 6px 12px; border-radius: 4px;
        font-size: 12px; white-space: nowrap; opacity: 0; visibility: hidden;
        transition: all 0.2s ease; z-index: 1000;
    }
    .tooltip-text::before {
        content: ''; position: absolute; top: -4px; left: 50%; transform: translateX(-50%);
        border-left: 4px solid transparent; border-right: 4px solid transparent; border-bottom: 4px solid #333;
    }
    .icon-btn:hover .tooltip-text { opacity: 1; visibility: visible; }
    .status-indicator { display: inline-block; width: 6px; height: 30px; border-radius: 3px; vertical-align: middle; }
    .red { background-color: #F56565; }
    .green { background-color: #2e7d32; }
    .blue { background-color: #4299E1; }
    .table-hover tbody tr:hover { background-color: rgba(46, 125, 50, 0.05); }
    .badge { font-weight: 500; padding: 4px 8px; font-size: 12px; }
    .user-info { position: relative; }
    .user-actions {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        opacity: 0; transition: opacity 0.2s ease; z-index: 10;
    }
    .user-info:hover .user-actions { opacity: 1; }
    .action-dots {
        background: none; border: none; color: #6c757d; font-size: 16px; cursor: pointer;
        padding: 4px 8px; border-radius: 4px; transition: all 0.2s ease;
    }
    .action-dots:hover { color: #495057; }
    .filter-panel-transition { transition: all 0.3s ease; }
    .swal-custom-popup {}
    .swal-custom-ok-button {}
    .dropdown-menu { z-index: 1010; padding: 0px !important; border-radius: 8px; }
    .dropdown-item.text-danger:hover { background-color: #fbebeb !important; color: #dc3545 !important; }
    .dropdown-item.text-success:hover { background-color: #eaf6ea !important; color: #198754 !important; }
    .user-name-link:hover h6 { color: #2e7d32; }
</style>

<div class="main-container">
    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-users text-2xl text-[#2e7d32] me-2"></i>
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Lead Data</div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <div class="position-relative" style="width: 300px;">
                    <input type="text" class="form-control product-search ps-5" id="searchInput" placeholder="Search leads...">
                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                </div>
                <div class="d-flex gap-2">
                    <button id="toggleFilters" class="icon-btn btn-outline-secondary">
                        <i class="ti ti-filter fs-5"></i>
                        <span class="tooltip-text">Filters</span>
                    </button>
                    <a id="btn-add-lead" href="{{ route('backend.leadform.create') }}" class="icon-btn btn-success">
                        <i class="ti ti-plus fs-5"></i>
                        <span class="tooltip-text">Add Lead</span>
                    </a>
                    <button id="btn-export-data" onclick="downloadData()" class="icon-btn btn-outline-primary">
                        <i class="ti ti-file-export fs-5"></i>
                        <span class="tooltip-text">Export</span>
                    </button>
                </div>
            </div>

            <div id="filterPanel" class="mt-3 p-3 border rounded bg-light filter-panel-transition" style="display: none;background-color: rgb(248 252 255) !important">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Lead Type</label>
                        <select class="form-select" id="filterLeadType">
                            <option value="all">All Leads</option>
                            <option value="raw">Raw Leads Only</option>
                            <option value="hot">Hot Leads Only</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Location</label>
                        <select class="form-select" id="filterLocation">
                            <option value="">All Locations</option>
                            @foreach($leads->pluck('locations')->unique()->filter() as $location)
                                @if($location !== 'N/A')<option value="{{ $location }}">{{ $location }}</option>@endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Last Qualification</label>
                        <select class="form-select" id="filterLastQualification">
                            <option value="">All Qualifications</option>
                            @foreach($leads->pluck('lastqualification')->unique()->filter() as $qualification)
                                @if($qualification !== 'N/A')<option value="{{ $qualification }}">{{ $qualification }}</option>@endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Country</label>
                        <select class="form-select" id="filterCountry">
                            <option value="">All Countries</option>
                            @foreach($leads->pluck('country')->unique()->filter() as $country)
                                @if($country !== 'N/A')<option value="{{ $country }}">{{ $country }}</option>@endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">User</label>
                        <select class="form-select" id="filterUser">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                @php $fullName = trim(($user->name ?? '') . ' ' . ($user->last ?? '')); @endphp
                                <option value="{{ $fullName }}">{{ $fullName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Status</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">All status</option>
                            @foreach($leads->pluck('status')->unique()->filter() as $status)
                                @if($status !== 'N/A')<option value="{{ $status }}">{{ $status }}</option>@endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label fw-medium">Date Range</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            <input type="text" class="form-control" id="filterDateFrom" placeholder="From date">
                            <span class="input-group-text">to</span>
                            <input type="text" class="form-control" id="filterDateTo" placeholder="To date">
                        </div>
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end gap-2">
                        <button id="applyFilters" class="icon-btn btn-primary">
                            <i class="ti ti-check fs-5"></i><span class="tooltip-text">Apply</span>
                        </button>
                        <button id="resetFilters" class="icon-btn btn-outline-secondary">
                            <i class="ti ti-rotate-2 fs-5"></i><span class="tooltip-text">Reset</span>
                        </button>
                        <button id="clearDates" class="icon-btn btn-outline-danger">
                            <i class="ti ti-trash fs-5"></i><span class="tooltip-text">Clear Dates</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15px;"></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Name</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-phone me-1"></i><span>Phone</span></div></th>
                            <th><div class="d-flex text-nowrap"><i class="ti ti-school me-1"></i><span>Last Qualification</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-status-change me-1"></i><span>Status</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-user-circle me-1"></i><span>User</span></div></th>
                            <th><div class="text-nowrap"><i class="ti ti-calendar-time me-1"></i><span>last followup</span></div></th>
                            <th><div class="text-nowrap"><i class="ti ti-calendar me-1"></i><span>Date Added</span></div></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php
                        list($notDroppedLeads, $droppedLeads) = $leads->partition(fn ($lead) => $lead->status !== 'Dropped');
                        $finalSortedLeads = $notDroppedLeads->sortByDesc('created_at')->merge($droppedLeads->sortByDesc('created_at'));
                        @endphp
                        @foreach ($finalSortedLeads as $lead)
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('backend.leadform.records', $lead->id) }}'">
                            <!-- 0: Status Indicator -->
                            <td style="padding: 0 5px; vertical-align: middle; text-align: center;">
                                @if ($lead->status == 'Dropped') <div class="status-indicator red"></div>
                                @elseif ($lead->status == 'Visa Granted') <div class="status-indicator green"></div>
                                @elseif ($lead->sources == '1') <div class="status-indicator blue"></div>
                                @endif
                            </td>
                            <!-- 1: Name & Actions -->
                            <td>
                                <div class="user-info d-flex align-items-center" onclick="event.stopPropagation()">
                                    <img src="{{ $lead->avatar ? asset('storage/avatars/' . $lead->avatar) : asset('assets/images/profile/user-1.jpg') }}" alt="avatar" class="rounded-circle" width="35" style="object-fit: cover;">
                                    <div class="ms-3 flex-grow-1">
                                        <a href="{{ route('backend.leadform.records', $lead->id) }}" class="text-dark text-decoration-none user-name-link">
                                            <h6 class="user-name mb-0 fw-medium">{{ $lead->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $lead->email ?? '' }}</small>
                                        </a>
                                    </div>
                                    <div class="user-actions">
                                        <div class="dropdown">
                                            <button class="action-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if ($lead->sources == '1')
                                                <li><a class="dropdown-item d-flex align-items-center gap-3 text-success" href="#" onclick="handleConvertClick({{ $lead->id }})"><i class="fs-4 ti ti-flame"></i>Convert to Hot</a></li>
                                                @endif
                                                @if($lead->status !== 'Dropped')
                                                <li><a class="dropdown-item d-flex align-items-center gap-3 text-danger" href="#" onclick="confirmWithdrawal(event, {{ $lead->id }})"><i class="fs-4 ti ti-trash"></i>Withdraw</a></li>
                                                <form id="withdrawForm{{$lead->id}}" action="{{ route('backend.lead.withdraw', ['id' => $lead->id]) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!-- 2: Phone -->
                            <td>{{ $lead->phone ?? '-' }}</td>
                            <!-- 3: Last Qualification -->
                            <td>{{ $lead->lastqualification ?? '-' }}</td>
                            <!-- 4: Status -->
                            <td>
                                @php
                                    $statusText = $lead->status ?? '-';
                                    if(!empty($lead->forwarded_to)) {
                                        $application = \App\Models\Application::where('lead_id', $lead->id)->first();
                                        $statusText = $application ? $application->status : $statusText;
                                    }
                                    $badgeClass = 'bg-primary-subtle text-primary';
                                    if ($statusText == 'Visa Granted') { $badgeClass = 'bg-success-subtle text-success'; }
                                    elseif ($statusText == 'Dropped') { $badgeClass = 'bg-danger-subtle text-danger'; }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                            </td>
                            <!-- 5: User -->
                            <td>{{ $lead->creator ? ($lead->creator->name . ' ' . $lead->creator->last) : '-' }}</td>
                           <td>
                            @if ($lead->lead_comments && $lead->lead_comments->isNotEmpty())
                                {{ \Carbon\Carbon::parse($lead->lead_comments->sortByDesc('created_at')->first()->created_at)->diffForHumans() }}
                            @else
                               -
                            @endif
                        </td>
                            <!-- 6: Date Added -->
                             <td>{{ $lead->created_at ? $lead->created_at->format('Y-m-d (h:i A)') : '-' }}</td>


                            <!-- HIDDEN COLUMNS FOR SEARCH/FILTER/EXPORT -->
                            <td class="d-none">{{ $lead->email ?? '-' }}</td>
                            <td class="d-none">{{ $lead->locations ?? '-' }}</td>
                            <td class="d-none">{{ $lead->passed ?? '-' }}</td>
                            <td class="d-none">{{ $lead->country ?? '-' }}</td>
                            <td class="d-none">{{ $lead->sources ?? '0' }}</td>
                            <td class="d-none">{{ $lead->courselevel ?? '-' }}</td>
                            <td class="d-none">{{ $lead->gpa ?? '-' }}</td>
                            <td class="d-none">{{ $lead->englishTest ?? '-' }}</td>
                            <td class="d-none">{{ $lead->higher ?? '-' }}</td>
                            <td class="d-none">{{ $lead->less ?? '-' }}</td>
                            <td class="d-none">{{ $lead->score ?? '-' }}</td>
                            <td class="d-none">{{ $lead->englishscore ?? '-' }}</td>
                            <td class="d-none">{{ $lead->englishtheory ?? '-' }}</td>
                            <td class="d-none">{{ $lead->otherScore ?? '-' }}</td>
                            <td class="d-none">{{ !empty($lead->academic) && $lead->academic !== 'N/A' ? $lead->academic : '-' }}</td>
                            <td class="d-none">{{ $lead->location ?? '-' }}</td>
                            <td class="d-none">{{ $lead->university ?? '-' }}</td>
                            <td class="d-none">{{ $lead->course ?? '-' }}</td>
                            <td class="d-none">{{ $lead->intake ?? '-' }}</td>
                            <td class="d-none">{{ $lead->offerss ?? '-' }}</td>
                            <td class="d-none">{{ $lead->source ?? '-' }}</td>
                            <td class="d-none">{{ $lead->otherDetails ?? '-' }}</td>
                            <td class="d-none">{{ $lead->link ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted" id="paginationInfo">
                        Showing <span id="startItem">1</span> to <span id="endItem">10</span> of <span id="totalItems">{{ $leads->count() }}</span> entries
                    </div>
                    <div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0" id="paginationControls">
                                <li class="page-item disabled" id="prevPage"><a class="page-link" href="#" aria-label="Previous"><i class="ti ti-chevron-left"></i></a></li>
                                <li class="page-item" id="nextPage"><a class="page-link" href="#" aria-label="Next"><i class="ti ti-chevron-right"></i></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// --- SweetAlert Functions ---
function confirmWithdrawal(event, leadId) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to withdraw this lead? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, withdraw it!',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-custom-popup', confirmButton: 'swal-custom-ok-button' }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`withdrawForm${leadId}`).submit();
        }
    });
}

function handleConvertClick(leadId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to convert this to a Hot Lead?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, convert it!',
        cancelButtonText: 'No, cancel',
        customClass: { popup: 'swal-custom-popup', confirmButton: 'swal-custom-ok-button' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('backend/lead/convert-to-hot') }}/${leadId}`;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            document.body.appendChild(form);

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Converted!', data.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Error!', data.message || 'Failed to convert lead.', 'error');
                }
            })
            .catch(error => Swal.fire('Request Error!', 'An error occurred. Check console.', 'error'));
        }
    });
}

// --- Data Export Function ---
function downloadData() {
    const rowsToExport = window.advancedTableInstance.filteredRows;
    const headers = [
        "Name", "Email", "Phone", "Address Location", "Last Qualification", "Course Level", "Passed Year", "GPA",
        "English Test", "Higher", "Less", "Overall Score", "English Score (No Test)", "English Theory (No Test)", "Other Test Score",
        "Application Status", "Academic Docs Received", "Study Country", "Campus Location", "University", "Applied Course", "Intake",
        "Offer Status", "Referral Source", "Other Referral Details", "User", "Date Added", "Sources Value (1=Raw, 0=Hot)", "Last Followed Up", "Link"
    ];
    const data = [headers];
    const indices = window.advancedTableInstance.columnIndices;

    rowsToExport.forEach(row => {
        const cols = row.querySelectorAll('td');
        const rowData = [
            cols[indices.name].querySelector('h6')?.textContent.trim() || '', // Name
            cols[indices.hiddenEmail].textContent.trim(), // Email
            cols[indices.phone].textContent.trim(), // Phone
            cols[indices.hiddenAddressLocation].textContent.trim(), // Address Location
            cols[indices.lastQualification].textContent.trim(), // Last Qualification
            cols[indices.hiddenCourseLevel].textContent.trim(), // Course Level
            cols[indices.hiddenPassedYear].textContent.trim(), // Passed Year
            cols[indices.hiddenGpa].textContent.trim(), // GPA
            cols[indices.hiddenEnglishTest].textContent.trim(), // English Test
            cols[indices.hiddenHigher].textContent.trim(), // Higher
            cols[indices.hiddenLess].textContent.trim(), // Less
            cols[indices.hiddenScore].textContent.trim(), // Overall Score
            cols[indices.hiddenEnglishScore].textContent.trim(), // English Score
            cols[indices.hiddenEnglishTheory].textContent.trim(), // English Theory
            cols[indices.hiddenOtherScore].textContent.trim(), // Other Score
            cols[indices.status].querySelector('.badge')?.textContent.trim() || '', // App Status
            cols[indices.hiddenAcademicDocs].textContent.trim(), // Academic Docs
            cols[indices.hiddenCountry].textContent.trim(), // Study Country
            cols[indices.hiddenCampusLocation].textContent.trim(), // Campus Location
            cols[indices.hiddenUniversity].textContent.trim(), // University
            cols[indices.hiddenCourse].textContent.trim(), // Applied Course
            cols[indices.hiddenIntake].textContent.trim(), // Intake
            cols[indices.hiddenOfferStatus].textContent.trim(), // Offer Status
            cols[indices.hiddenReferralSource].textContent.trim(), // Referral Source
            cols[indices.hiddenOtherDetails].textContent.trim(), // Other Details
            cols[indices.user].textContent.trim(), // User
            cols[indices.dateAdded].textContent.trim(), // Date Added
            cols[indices.hiddenSourcesValue].textContent.trim(), // Sources Value
            cols[indices.hiddenLastFollowUp].textContent.trim(), // Last Followed Up
            cols[indices.hiddenLink].textContent.trim(), // Link
        ];
        data.push(rowData);
    });

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Leads Data');
    XLSX.writeFile(wb, `leads_data_${new Date().toISOString().split('T')[0]}.xlsx`);
}

// --- Main Table Class ---
class AdvancedTable {
    constructor(tableId, options) {
        this.table = document.getElementById(tableId);
        this.tbody = this.table.querySelector('tbody');
        this.originalRows = Array.from(this.tbody.querySelectorAll('tr'));
        this.itemsPerPage = options.itemsPerPage || 9;
        this.filterOptions = options.filterOptions || {};
        this.currentPage = options.initialPage || 1;
        this.filteredRows = [...this.originalRows];
        this.totalPages = 0;
        this.columnIndices = this.getColumnIndices();
        this.initPagination();
    }

    getColumnIndices() {
        return {
            statusIndicator: 0, name: 1, phone: 2, lastQualification: 3, status: 4, user: 5, 
            lastFollowup: 6, dateAdded: 7,
            hiddenEmail: 8, hiddenAddressLocation: 9, hiddenPassedYear: 10, hiddenCountry: 11, 
            hiddenSourcesValue: 12, hiddenCourseLevel: 13, hiddenGpa: 14, hiddenEnglishTest: 15, 
            hiddenHigher: 16, hiddenLess: 17, hiddenScore: 18, hiddenEnglishScore: 19, 
            hiddenEnglishTheory: 20, hiddenOtherScore: 21, hiddenAcademicDocs: 22, 
            hiddenCampusLocation: 23, hiddenUniversity: 24, hiddenCourse: 25, hiddenIntake: 26, 
            hiddenOfferStatus: 27, hiddenReferralSource: 28, hiddenOtherDetails: 29,
            hiddenLink: 30
        };
    }

    initPagination() {
        this.paginationControls = document.getElementById('paginationControls');
        this.prevPage = document.getElementById('prevPage');
        this.nextPage = document.getElementById('nextPage');
        this.paginationInfo = document.getElementById('paginationInfo');
        this.paginationControls.addEventListener('click', (e) => {
            e.preventDefault();
            const target = e.target.closest('.page-link');
            if (!target) return;
            const pageItem = target.closest('.page-item');
            if (pageItem.classList.contains('disabled') || pageItem.classList.contains('active') || pageItem.classList.contains('ellipsis')) return;
            if (pageItem.id === 'prevPage') { if (this.currentPage > 1) this.currentPage--; }
            else if (pageItem.id === 'nextPage') { if (this.currentPage < this.totalPages) this.currentPage++; }
            else {
                const pageNumber = parseInt(target.textContent, 10);
                if (!isNaN(pageNumber)) this.currentPage = pageNumber;
            }
            this.updatePagination();
        });
    }

    setFilter(key, value) {
        this.filterOptions[key] = value;
        this.currentPage = 1;
    }

    resetFilters() {
        this.filterOptions = { 
            name: '', 
            leadType: 'all', 
            location: '', 
            lastQualification: '', 
            country: '', 
            user: '', 
            status: '',
            dateFrom: '', 
            dateTo: '' 
        };
        this.currentPage = 1;
    }

    _checkRowAgainstFilters(row) {
        const cols = row.querySelectorAll('td');
        const f = this.filterOptions;
        const i = this.columnIndices;

        // Search Filter
        if (f.name && f.name.trim() !== '') {
            const searchTerm = f.name.toLowerCase();
            const nameText = cols[i.name].querySelector('h6')?.textContent.toLowerCase() || '';
            const emailText = cols[i.name].querySelector('small')?.textContent.toLowerCase() || '';
            const phoneText = cols[i.phone].textContent.toLowerCase() || '';
            if (!nameText.includes(searchTerm) && !emailText.includes(searchTerm) && !phoneText.includes(searchTerm)) {
                return false;
            }
        }

        // Dropdown Filters
        if (f.location && f.location !== '' && cols[i.hiddenAddressLocation].textContent.trim() !== f.location) {
            return false;
        }
        
        if (f.lastQualification && f.lastQualification !== '' && cols[i.lastQualification].textContent.trim() !== f.lastQualification) {
            return false;
        }
        
        if (f.country && f.country !== '' && cols[i.hiddenCountry].textContent.trim() !== f.country) {
            return false;
        }
        
        if (f.user && f.user !== '' && cols[i.user].textContent.trim() !== f.user) {
            return false;
        }
        
        if (f.status && f.status !== '' && cols[i.status].querySelector('.badge')?.textContent.trim() !== f.status) {
            return false;
        }

        // Lead Type Filter
        if (f.leadType === 'raw' && cols[i.hiddenSourcesValue].textContent.trim() !== '1') {
            return false;
        }
        if (f.leadType === 'hot' && cols[i.hiddenSourcesValue].textContent.trim() === '1') {
            return false;
        }

        // Date Filter
        if (f.dateFrom || f.dateTo) {
            const dateAddedString = cols[i.dateAdded].textContent.trim();
            if (!dateAddedString || dateAddedString === '-') return false;
            
            const rowDate = new Date(dateAddedString);
            if (isNaN(rowDate.getTime())) return false;
            
            if (f.dateFrom) {
                const filterFromDate = new Date(f.dateFrom + "T00:00:00");
                if (rowDate < filterFromDate) return false;
            }
            
            if (f.dateTo) {
                const filterToDate = new Date(f.dateTo + "T23:59:59");
                if (rowDate > filterToDate) return false;
            }
        }

        return true;
    }

    filterAndPaginate() {
        this.filteredRows = this.originalRows.filter(row => this._checkRowAgainstFilters(row));
        this.totalPages = Math.ceil(this.filteredRows.length / this.itemsPerPage);
        if (this.currentPage > this.totalPages && this.totalPages > 0) this.currentPage = this.totalPages;
        else if (this.totalPages === 0) this.currentPage = 1;
        this.updatePagination();
    }

    updatePagination() {
        this.clearPaginationLinks();
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = Math.min(startIndex + this.itemsPerPage, this.filteredRows.length);
        this.originalRows.forEach(row => row.style.display = 'none');
        for (let i = startIndex; i < endIndex; i++) {
            if (this.filteredRows[i]) this.filteredRows[i].style.display = '';
        }
        document.getElementById('startItem').textContent = this.filteredRows.length === 0 ? '0' : startIndex + 1;
        document.getElementById('endItem').textContent = endIndex;
        document.getElementById('totalItems').textContent = this.filteredRows.length;
        this.prevPage.classList.toggle('disabled', this.currentPage <= 1);
        this.nextPage.classList.toggle('disabled', this.currentPage >= this.totalPages || this.totalPages === 0);
        this.generatePaginationLinks();
        saveState(this);
    }

    clearPaginationLinks() {
        this.paginationControls.querySelectorAll('.page-item:not(#prevPage):not(#nextPage)').forEach(item => item.remove());
    }

    generatePaginationLinks() {
        const maxVisible = 5;
        if (this.totalPages <= 1) return;
        const createPageItem = (page, isActive = false, isEllipsis = false) => {
            const li = document.createElement('li');
            li.className = `page-item ${isActive ? 'active' : ''} ${isEllipsis ? 'disabled ellipsis' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${isEllipsis ? '...' : page}</a>`;
            return li;
        };
        if (this.totalPages <= maxVisible) {
            for (let i = 1; i <= this.totalPages; i++) this.nextPage.insertAdjacentElement('beforebegin', createPageItem(i, i === this.currentPage));
        } else {
            this.nextPage.insertAdjacentElement('beforebegin', createPageItem(1, 1 === this.currentPage));
            if (this.currentPage > 3) this.nextPage.insertAdjacentElement('beforebegin', createPageItem(0, false, true));
            let start = Math.max(2, this.currentPage - 1);
            let end = Math.min(this.totalPages - 1, this.currentPage + 1);
            if (this.currentPage === 1) end = 3;
            if (this.currentPage === this.totalPages) start = this.totalPages - 2;
            for (let i = start; i <= end; i++) this.nextPage.insertAdjacentElement('beforebegin', createPageItem(i, i === this.currentPage));
            if (this.currentPage < this.totalPages - 2) this.nextPage.insertAdjacentElement('beforebegin', createPageItem(0, false, true));
            this.nextPage.insertAdjacentElement('beforebegin', createPageItem(this.totalPages, this.totalPages === this.currentPage));
        }
    }
}

// --- State Management ---
const stateKey = 'leadTableState';
function saveState(tableInstance) {
    const state = {
        currentPage: tableInstance.currentPage,
        filterOptions: tableInstance.filterOptions,
        searchInput: document.getElementById('searchInput').value,
        filterPanelOpen: document.getElementById('filterPanel').style.display !== 'none',
    };
    localStorage.setItem(stateKey, JSON.stringify(state));
}

function loadState() {
    const savedState = localStorage.getItem(stateKey);
    if (savedState) {
        try {
            const state = JSON.parse(savedState);
            document.getElementById('searchInput').value = state.searchInput || '';
            document.getElementById('filterPanel').style.display = state.filterPanelOpen ? 'block' : 'none';
            // Apply filter values to UI
            if(state.filterOptions) {
                document.getElementById('filterLeadType').value = state.filterOptions.leadType || 'all';
                document.getElementById('filterLocation').value = state.filterOptions.location || '';
                document.getElementById('filterLastQualification').value = state.filterOptions.lastQualification || '';
                document.getElementById('filterCountry').value = state.filterOptions.country || '';
                document.getElementById('filterUser').value = state.filterOptions.user || '';
                document.getElementById('filterStatus').value = state.filterOptions.status || '';
                document.getElementById('filterDateFrom').value = state.filterOptions.dateFrom || '';
                document.getElementById('filterDateTo').value = state.filterOptions.dateTo || '';
            }
            return state;
        } catch (e) {
            localStorage.removeItem(stateKey);
            return null;
        }
    }
    return null;
}

// --- DOMContentLoaded Initializer ---
document.addEventListener('DOMContentLoaded', function() {
    const loadedState = loadState();
    const initialOptions = {
        itemsPerPage: 9,
        filterOptions: loadedState ? loadedState.filterOptions : { 
            name: '', 
            leadType: 'all', 
            location: '', 
            lastQualification: '', 
            country: '', 
            user: '', 
            status: '',
            dateFrom: '', 
            dateTo: '' 
        },
        initialPage: loadedState ? loadedState.currentPage : 1
    };

    const table = new AdvancedTable('dataTable', initialOptions);
    window.advancedTableInstance = table;
    table.filterAndPaginate();

    // --- Event Listeners for INSTANT filtering ---
    document.getElementById('searchInput').addEventListener('input', (e) => {
        table.setFilter('name', e.target.value);
        table.filterAndPaginate();
    });

    document.getElementById('filterLeadType').addEventListener('change', (e) => {
        table.setFilter('leadType', e.target.value);
        table.filterAndPaginate();
    });

    document.getElementById('filterLocation').addEventListener('change', (e) => {
        table.setFilter('location', e.target.value);
        table.filterAndPaginate();
    });

    document.getElementById('filterLastQualification').addEventListener('change', (e) => {
        table.setFilter('lastQualification', e.target.value);
        table.filterAndPaginate();
    });

    document.getElementById('filterCountry').addEventListener('change', (e) => {
        table.setFilter('country', e.target.value);
        table.filterAndPaginate();
    });

    document.getElementById('filterUser').addEventListener('change', (e) => {
        table.setFilter('user', e.target.value);
        table.filterAndPaginate();
    });

    document.getElementById('filterStatus').addEventListener('change', (e) => {
        table.setFilter('status', e.target.value);
        table.filterAndPaginate();
    });

    flatpickr("#filterDateFrom", {
        dateFormat: "Y-m-d",
        allowInput: true,
        onChange: function(selectedDates, dateStr) {
            table.setFilter('dateFrom', dateStr);
            table.filterAndPaginate();
        }
    });

    flatpickr("#filterDateTo", {
        dateFormat: "Y-m-d",
        allowInput: true,
        onChange: function(selectedDates, dateStr) {
            table.setFilter('dateTo', dateStr);
            table.filterAndPaginate();
        }
    });

    // --- Button Listeners ---
    document.getElementById('toggleFilters')?.addEventListener('click', () => {
        const panel = document.getElementById('filterPanel');
        panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'block' : 'none';
        saveState(table);
    });

    document.getElementById('applyFilters').addEventListener('click', () => {
        table.filterAndPaginate();
    });

    document.getElementById('resetFilters').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterLeadType').value = 'all';
        document.getElementById('filterLocation').value = '';
        document.getElementById('filterLastQualification').value = '';
        document.getElementById('filterCountry').value = '';
        document.getElementById('filterUser').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        document.getElementById('filterPanel').style.display = 'none';
        localStorage.removeItem(stateKey);
        table.resetFilters();
        table.filterAndPaginate();
    });

    document.getElementById('clearDates').addEventListener('click', () => {
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        table.setFilter('dateFrom', '');
        table.setFilter('dateTo', '');
        table.filterAndPaginate();
    });
});
</script>
@endsection