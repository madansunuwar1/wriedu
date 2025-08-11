@extends('layouts.admin')
@include('backend.script.session')
@include('backend.script.alert')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@if(session('success')) <script> Swal.fire({ title: 'Success!', text: "{{ session('success') }}", icon: 'success', confirmButtonText: 'OK', customClass: { popup: 'swal-custom-popup', confirmButton: 'swal-custom-ok-button' } }); </script> @endif
@if(session('error')) <script> Swal.fire({ title: 'Error!', text: "{{ session('error') }}", icon: 'error', confirmButtonText: 'OK', customClass: { popup: 'swal-custom-popup', confirmButton: 'swal-custom-ok-button' } }); </script> @endif

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
    .dropdown-menu { z-index: 1010; padding: 0px !important; border-radius: 8px; }
    .dropdown-item.text-danger:hover { background-color: #fbebeb !important; color: #dc3545 !important; }
    .dropdown-item.text-success:hover { background-color: #eaf6ea !important; color: #198754 !important; }
    .user-name-link:hover h6 { color: #2e7d32; }
    .status-new { background-color: #cfe2ff; color: #0a58ca; }
    .status-contacted { background-color: #f8d7da; color: #b02a37; }
    .status-in-progress { background-color: #fff3cd; color: #997404; }
    .status-qualified, .status-converted { background-color: #d1e7dd; color: #146c43; }
    .status-rejected { background-color: #e2e3e5; color: #565e64; }
    .status-on-hold { background-color: #e9d5ff; color: #6f42c1; }
    .status-dropped { background-color: #f8d7da; color: #b02a37; text-decoration: line-through; }
    .status-default { background-color: #e9ecef; color: #495057; }
</style>

<div class="main-container">
    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-users text-2xl text-[#2e7d32] me-2"></i>
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Raw Lead Data</div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative" style="width: 300px;">
                        <input type="text" class="form-control product-search ps-5" id="searchInput" placeholder="Search leads...">
                        <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="bulkActionDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                            Bulk Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="bulkActionDropdownBtn">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#assignUserModal"><i class="ti ti-user-plus me-2"></i>Assign Selected</a></li>
                            <li><a class="dropdown-item text-danger" href="#" id="bulkDeleteBtn"><i class="ti ti-trash me-2"></i>Delete Selected</a></li>
                        </ul>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button id="toggleFilters" class="icon-btn btn-outline-secondary">
                        <i class="ti ti-filter fs-5"></i><span class="tooltip-text">Filters</span>
                    </button>
                    <a href="{{ route('backend.leadform.import') }}" class="icon-btn btn-outline-primary">
                        <i class="ti ti-upload fs-5"></i><span class="tooltip-text">Import</span>
                    </a>
                    <a href="{{ route('rawlead.export.all') }}" class="icon-btn btn-success">
                        <i class="ti ti-file-export fs-5"></i><span class="tooltip-text">Export All</span>
                    </a>
                </div>
            </div>

            <div id="filterPanel" class="mt-3 p-3 border rounded bg-light filter-panel-transition" style="display: none;background-color: rgb(248 252 255) !important">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Status</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Assigned To</label>
                        <select class="form-select" id="filterAssignee">
                            <option value="">Anyone</option>
                            <option value="unassigned">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Preferred Country</label>
                        <select class="form-select" id="filterCountry">
                            <option value="">All Countries</option>
                            @php $uniqueCountries = $rawLeads->pluck('preferred_country')->unique()->filter(fn($value) => !empty($value) && $value !== 'N/A')->sort(); @endphp
                            @foreach($uniqueCountries as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Applying For</label>
                        <select class="form-select" id="filterApplyingFor">
                            <option value="">All Types</option>
                            @php $uniqueApplyingFor = $rawLeads->pluck('applying_for')->unique()->filter(fn($value) => !empty($value) && $value !== 'N/A')->sort(); @endphp
                            @foreach($uniqueApplyingFor as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label fw-medium">Date Added Range</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            <input type="text" class="form-control" id="filterDateFrom" placeholder="From date">
                            <span class="input-group-text">to</span>
                            <input type="text" class="form-control" id="filterDateTo" placeholder="To date">
                        </div>
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end gap-2">
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
                            <th style="width: 30px;"><input type="checkbox" id="selectAllCheckbox" class="form-check-input"></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Name</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-mail me-1"></i><span>Email</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-phone me-1"></i><span>Phone</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-status-change me-1"></i><span>Status</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-user-check me-1"></i><span>Assigned To</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-calendar me-1"></i><span>Date Added</span></div></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($rawLeads as $rawLead)
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('backend.rawleadform.records.show', $rawLead->id) }}'">
                            <td onclick="event.stopPropagation();"><input type="checkbox" class="form-check-input rawlead-checkbox" value="{{ $rawLead->id }}"></td>
                            <td>
                                <div class="user-info d-flex align-items-center" onclick="event.stopPropagation();">
                                    <div class="ms-3 flex-grow-1">
                                        <a href="{{ route('backend.rawleadform.records.show', $rawLead->id) }}" class="text-dark text-decoration-none user-name-link">
                                            <h6 class="user-name mb-0 fw-medium">{{ $rawLead->name ?? 'N/A' }}</h6>
                                        </a>
                                    </div>
                                    <div class="user-actions">
                                        <div class="dropdown">
                                            <button class="action-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('rawlead.edit', $rawLead->id) }}"><i class="fs-4 ti ti-edit"></i>Edit</a></li>
                                                <li><a class="dropdown-item d-flex align-items-center gap-3 text-danger" href="#" onclick="confirmDelete(event, {{ $rawLead->id }})"><i class="fs-4 ti ti-trash"></i>Delete</a></li>
                                                <form id="deleteForm{{$rawLead->id}}" action="{{ route('rawlead.destroy', $rawLead->id) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $rawLead->email ?? '-' }}</td>
                            <td>{{ $rawLead->phone ?? '-' }}</td>
                            <td>
                                @php
                                    $statusClass = 'status-' . str_replace(' ', '-', strtolower($rawLead->status ?? 'default'));
                                    $statusText = ucwords(str_replace('_', ' ', $rawLead->status ?? 'Unknown'));
                                @endphp
                                <span class="badge {{ $statusClass }} status-default">{{ $statusText }}</span>
                            </td>
                            <td>{{ $rawLead->assignee->name ?? 'Unassigned' }}</td>
                            <td>{{ $rawLead->created_at ? $rawLead->created_at->format('Y-m-d') : '-' }}</td>
                            <td class="d-none">{{ $rawLead->preferred_country ?? '-' }}</td>
                            <td class="d-none">{{ $rawLead->applying_for ? ucfirst($rawLead->applying_for) : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center py-4">No raw leads found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted" id="paginationInfo">
                        Showing <span id="startItem">1</span> to <span id="endItem">10</span> of <span id="totalItems">{{ $rawLeads->total() }}</span> entries
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

<!-- Bulk Assign Modal -->
<div class="modal fade" id="assignUserModal" tabindex="-1" aria-labelledby="assignUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="assignUserModalLabel">Assign Selected Leads</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label for="assigneeSelect" class="form-label">Select User to Assign</label><select class="form-select" id="assigneeSelect"><option value="" selected disabled>-- Select User --</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                <div class="mb-3"><label for="assignmentComment" class="form-label">Optional Comment</label><textarea class="form-control" id="assignmentComment" rows="3" placeholder="Add a comment..."></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="confirmAssignBtn">Assign Leads</button></div>
        </div>
    </div>
</div>
<!-- Bulk Action Form -->
<form id="bulkActionForm" method="POST" action=""><input type="hidden" name="_token" value="{{ csrf_token() }}"><div id="bulkIdsContainer"></div></form>

<script>
// --- SweetAlert & Action Functions ---
function confirmDelete(event, leadId) {
    event.preventDefault();
    event.stopPropagation();
    Swal.fire({
        title: 'Are you sure?', text: "This action cannot be undone.", icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Yes, delete it!', customClass: { popup: 'swal-custom-popup' }
    }).then((result) => {
        if (result.isConfirmed) { document.getElementById(`deleteForm${leadId}`).submit(); }
    });
}

function submitBulkAction(action) {
    const selectedIds = Array.from(document.querySelectorAll('.rawlead-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) {
        Swal.fire('No Selection', 'Please select at least one lead.', 'warning');
        return;
    }

    const form = document.getElementById('bulkActionForm');
    const container = document.getElementById('bulkIdsContainer');
    container.innerHTML = '';
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'raw_lead_ids[]';
        input.value = id;
        container.appendChild(input);
    });

    if (action === 'delete') {
        Swal.fire({
            title: 'Are you sure?', text: `You are about to delete ${selectedIds.length} lead(s).`, icon: 'warning',
            showCancelButton: true, confirmButtonText: 'Yes, delete them!', customClass: { popup: 'swal-custom-popup' }
        }).then((result) => {
            if (result.isConfirmed) {
                form.action = "{{ route('backend.rawlead.bulkDestroy') }}";
                form.method = 'POST'; // Laravel bulk delete often uses POST
                form.submit();
            }
        });
    } else if (action === 'assign') {
        const userId = document.getElementById('assigneeSelect').value;
        const comment = document.getElementById('assignmentComment').value;
        if (!userId) {
            Swal.fire('User Not Selected', 'Please select a user to assign.', 'warning');
            return;
        }
        const userInput = document.createElement('input');
        userInput.type = 'hidden';
        userInput.name = 'user_id';
        userInput.value = userId;
        container.appendChild(userInput);

        const commentInput = document.createElement('input');
        commentInput.type = 'hidden';
        commentInput.name = 'assignment_comment';
        commentInput.value = comment;
        container.appendChild(commentInput);

        form.action = "{{ route('backend.rawlead.bulkAssign') }}";
        form.method = 'POST';
        form.submit();
    }
}

// --- Main Table Class ---
class AdvancedTable {
    constructor(tableId, options) {
        this.table = document.getElementById(tableId);
        this.tbody = this.table.querySelector('tbody');
        this.originalRows = Array.from(this.tbody.querySelectorAll('tr'));
        this.itemsPerPage = 10; // You can adjust this
        this.filterOptions = options.filterOptions || {};
        this.currentPage = options.initialPage || 1;
        this.filteredRows = [...this.originalRows];
        this.totalPages = 0;
        this.columnIndices = {
            checkbox: 0, name: 1, email: 2, phone: 3, status: 4, assignee: 5, dateAdded: 6,
            hiddenCountry: 7, hiddenApplyingFor: 8
        };
        this.initPagination();
    }

    initPagination() {
        this.paginationControls = document.getElementById('paginationControls');
        this.prevPage = document.getElementById('prevPage');
        this.nextPage = document.getElementById('nextPage');
        this.paginationControls.addEventListener('click', (e) => {
            e.preventDefault();
            const target = e.target.closest('.page-link');
            if (!target) return;
            const pageItem = target.closest('.page-item');
            if (pageItem.classList.contains('disabled') || pageItem.classList.contains('active')) return;
            if (pageItem.id === 'prevPage') { if (this.currentPage > 1) this.currentPage--; }
            else if (pageItem.id === 'nextPage') { if (this.currentPage < this.totalPages) this.currentPage++; }
            else {
                const pageNumber = parseInt(target.textContent, 10);
                if (!isNaN(pageNumber)) this.currentPage = pageNumber;
            }
            this.updatePagination();
        });
    }

    setFilter(key, value) { this.filterOptions[key] = value; this.currentPage = 1; }
    resetFilters() { this.filterOptions = {}; this.currentPage = 1; }

    _checkRowAgainstFilters(row) {
        if (row.querySelectorAll('td').length < 2) return true; // Keep "No results" row
        const cols = row.querySelectorAll('td');
        const f = this.filterOptions;
        const i = this.columnIndices;

        if (f.search) {
            const term = f.search.toLowerCase();
            const nameText = cols[i.name].textContent.toLowerCase();
            const emailText = cols[i.email].textContent.toLowerCase();
            const phoneText = cols[i.phone].textContent.toLowerCase();
            if (!nameText.includes(term) && !emailText.includes(term) && !phoneText.includes(term)) return false;
        }
        if (f.status && cols[i.status].textContent.trim() !== f.status) return false;
        if (f.assignee) {
            const assigneeText = cols[i.assignee].textContent.trim();
            if (f.assignee === 'unassigned' && assigneeText !== 'Unassigned') return false;
            if (f.assignee !== 'unassigned' && assigneeText !== f.assignee) return false;
        }
        if (f.country && cols[i.hiddenCountry].textContent.trim() !== f.country) return false;
        if (f.applyingFor && cols[i.hiddenApplyingFor].textContent.trim() !== f.applyingFor) return false;
        if (f.dateFrom || f.dateTo) {
            const rowDate = new Date(cols[i.dateAdded].textContent.trim());
            if (isNaN(rowDate.getTime())) return false;
            if (f.dateFrom && rowDate < new Date(f.dateFrom + "T00:00:00")) return false;
            if (f.dateTo && rowDate > new Date(f.dateTo + "T23:59:59")) return false;
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
        updateBulkActionButtonState();
    }

    clearPaginationLinks() { this.paginationControls.querySelectorAll('.page-item:not(#prevPage):not(#nextPage)').forEach(item => item.remove()); }

    generatePaginationLinks() {
        // Using a simpler pagination link generation for clarity
        if (this.totalPages <= 1) return;
        for (let i = 1; i <= this.totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === this.currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            this.nextPage.insertAdjacentElement('beforebegin', li);
        }
    }
}

// --- State Management ---
const stateKey = 'rawLeadTableState';
function saveState(tableInstance) {
    const state = {
        currentPage: tableInstance.currentPage,
        filterOptions: tableInstance.filterOptions,
        filterPanelOpen: document.getElementById('filterPanel').style.display !== 'none',
    };
    localStorage.setItem(stateKey, JSON.stringify(state));
}

function loadState() {
    const savedState = localStorage.getItem(stateKey);
    if (savedState) {
        try {
            const state = JSON.parse(savedState);
            if (state.filterOptions) {
                document.getElementById('searchInput').value = state.filterOptions.search || '';
                document.getElementById('filterStatus').value = state.filterOptions.status || '';
                document.getElementById('filterAssignee').value = state.filterOptions.assignee || '';
                document.getElementById('filterCountry').value = state.filterOptions.country || '';
                document.getElementById('filterApplyingFor').value = state.filterOptions.applyingFor || '';
                document.getElementById('filterDateFrom').value = state.filterOptions.dateFrom || '';
                document.getElementById('filterDateTo').value = state.filterOptions.dateTo || '';
            }
            document.getElementById('filterPanel').style.display = state.filterPanelOpen ? 'block' : 'none';
            return state;
        } catch (e) { localStorage.removeItem(stateKey); return null; }
    }
    return null;
}

// --- Bulk Action UI Logic ---
function updateBulkActionButtonState() {
    const selectedCount = document.querySelectorAll('.rawlead-checkbox:checked').length;
    document.getElementById('bulkActionDropdownBtn').disabled = selectedCount === 0;
}

// --- DOMContentLoaded Initializer ---
document.addEventListener('DOMContentLoaded', function() {
    const loadedState = loadState();
    const table = new AdvancedTable('dataTable', {
        initialPage: loadedState ? loadedState.currentPage : 1,
        filterOptions: loadedState ? loadedState.filterOptions : {}
    });
    window.advancedTableInstance = table;
    table.filterAndPaginate();

    // --- Event Listeners ---
    const addInstantFilter = (id, key) => {
        document.getElementById(id)?.addEventListener('input', (e) => {
            table.setFilter(key, e.target.value);
            table.filterAndPaginate();
        });
    };
    addInstantFilter('searchInput', 'search');
    addInstantFilter('filterStatus', 'status');
    addInstantFilter('filterAssignee', 'assignee');
    addInstantFilter('filterCountry', 'country');
    addInstantFilter('filterApplyingFor', 'applyingFor');

    flatpickr("#filterDateFrom", { dateFormat: "Y-m-d", onChange: (d, s) => { table.setFilter('dateFrom', s); table.filterAndPaginate(); } });
    flatpickr("#filterDateTo", { dateFormat: "Y-m-d", onChange: (d, s) => { table.setFilter('dateTo', s); table.filterAndPaginate(); } });

    document.getElementById('toggleFilters')?.addEventListener('click', () => {
        const panel = document.getElementById('filterPanel');
        panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'block' : 'none';
        saveState(table);
    });

    document.getElementById('resetFilters')?.addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.querySelectorAll('#filterPanel select, #filterPanel input').forEach(el => el.value = '');
        localStorage.removeItem(stateKey);
        table.resetFilters();
        table.filterAndPaginate();
    });

    document.getElementById('clearDates')?.addEventListener('click', () => {
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        table.setFilter('dateFrom', '');
        table.setFilter('dateTo', '');
        table.filterAndPaginate();
    });

    // Bulk Action Listeners
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        document.querySelectorAll('.rawlead-checkbox').forEach(checkbox => {
            if (checkbox.closest('tr').style.display !== 'none') {
                checkbox.checked = this.checked;
            }
        });
        updateBulkActionButtonState();
    });

    document.body.addEventListener('change', (e) => {
        if (e.target.classList.contains('rawlead-checkbox')) {
            updateBulkActionButtonState();
        }
    });

    document.getElementById('bulkDeleteBtn').addEventListener('click', (e) => { e.preventDefault(); submitBulkAction('delete'); });
    document.getElementById('confirmAssignBtn').addEventListener('click', () => submitBulkAction('assign'));
});
</script>
@endsection