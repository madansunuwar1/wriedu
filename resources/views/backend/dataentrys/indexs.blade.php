@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')

{{-- External Libraries --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

{{-- Success Message Popup --}}
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

{{-- Page-Specific Styles (Copied from Lead Index) --}}
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
    .icon-btn.btn-primary:hover { background: #0d6efd; border-color: #0d6efd; color: white; }
    .icon-btn.btn-outline-primary:hover { background: #0d6efd; border-color: #0d6efd; color: white; }
    .icon-btn.btn-outline-secondary:hover { background: #6c757d; border-color: #6c757d; color: white; }
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
    .table-hover tbody tr:hover { background-color: rgba(13, 110, 253, 0.05); }
    .filter-panel-transition { transition: all 0.3s ease; }
    .dropdown-menu { z-index: 1010; padding: 0px !important; border-radius: 8px; }
    .dropdown-item.text-danger:hover { background-color: #fbebeb !important; color: #dc3545 !important; }
</style>

<div class="main-container">
    <div class="widget-content searchable-container list">
        {{-- Header Section with Search and Action Buttons --}}
        <div class="card card-body">
            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-building-skyscraper text-2xl text-primary me-2"></i>
                        <div class="text-[24px] text-primary font-semibold font-g">University Data</div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <div class="position-relative" style="width: 300px;">
                    <input type="text" class="form-control product-search ps-5" id="searchInput" placeholder="Search...">
                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                </div>
                <div class="d-flex gap-2">
                    <button id="toggleFilters" class="icon-btn btn-outline-secondary">
                        <i class="ti ti-filter fs-5"></i>
                        <span class="tooltip-text">Filters</span>
                    </button>
                    <button onclick="openModal()" class="icon-btn btn-primary">
                        <i class="ti ti-upload fs-5"></i>
                        <span class="tooltip-text">Import</span>
                    </button>
                    <button id="btn-export-data" onclick="downloadData()" class="icon-btn btn-outline-primary">
                        <i class="ti ti-file-export fs-5"></i>
                        <span class="tooltip-text">Export</span>
                    </button>
                </div>
            </div>

            {{-- Collapsible Filter Panel --}}
            <div id="filterPanel" class="mt-3 p-3 border rounded bg-light filter-panel-transition" style="display: none;background-color: rgb(248 252 255) !important">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Location</label>
                        <select class="form-select" id="filterLocation">
                            <option value="">All Locations</option>
                            @foreach($data_entries->pluck('newLocation')->unique()->filter()->sort() as $location)
                                <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Country</label>
                        <select class="form-select" id="filterCountry">
                            <option value="">All Countries</option>
                            @foreach($data_entries->pluck('country')->unique()->filter()->sort() as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Level</label>
                        <select class="form-select" id="filterLevel">
                            <option value="">All Levels</option>
                            @foreach($data_entries->pluck('level')->unique()->filter()->sort() as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end gap-2">
                        <button id="applyFilters" class="icon-btn btn-primary">
                            <i class="ti ti-check fs-5"></i><span class="tooltip-text">Apply</span>
                        </button>
                        <button id="resetFilters" class="icon-btn btn-outline-secondary">
                            <i class="ti ti-rotate-2 fs-5"></i><span class="tooltip-text">Reset</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- University Data Table --}}
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th><div class="d-flex align-items-center"><i class="ti ti-building-skyscraper me-1"></i><span>University</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-map-pin me-1"></i><span>Location</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-book me-1"></i><span>Course</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-calendar me-1"></i><span>Intake</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-currency-dollar me-1"></i><span>Tuition</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-settings me-1"></i><span>Actions</span></div></th>
                            
                            {{-- Hidden columns for filtering and export --}}
                            <th class="d-none">Scholarship</th>
                            <th class="d-none">UG Ielts</th>
                            <th class="d-none">UG Pte</th>
                            <th class="d-none">PG Ielts</th>
                            <th class="d-none">PG Pte</th>
                            <th class="d-none">UG Gap</th>
                            <th class="d-none">PG Gap</th>
                            <th class="d-none">UG GPA</th>
                            <th class="d-none">PG GPA</th>
                            <th class="d-none">English Test</th>
                            <th class="d-none">Country</th>
                            <th class="d-none">Documents</th>
                            <th class="d-none">Level</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach ($data_entries as $entry)
                        <tr>
                            @php
                                $matchedUniversity = $universities->firstWhere('name', $entry->newUniversity);
                            @endphp
                            <!-- 0: University -->
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($matchedUniversity && $matchedUniversity->image_link)
                                        <img src="{{ $matchedUniversity->image_link }}" alt="{{ $matchedUniversity->name }}" width="40" height="40" class="rounded-circle me-3" style="object-fit: contain; background: white;">
                                    @else
                                        <div class="rounded-circle me-3" style="width: 40px; height: 40px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                            <i class="ti ti-building-skyscraper text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="fw-semibold mb-0 fs-4">{{ $entry->newUniversity ?? 'N/A' }}</h6>
                                        <small class="text-muted">{{ $entry->country ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <!-- 1: Location -->
                            <td>{{ $entry->newLocation ?? '-' }}</td>
                            <!-- 2: Course -->
                            <td>{{ $entry->newCourse ?? '-' }}</td>
                            <!-- 3: Intake -->
                            <td>{{ $entry->newIntake ?? '-' }}</td>
                            <!-- 4: Tuition -->
                            <td>{{ $entry->newAmount ?? '-' }}</td>
                            <!-- 5: Actions -->
                            <td>
                                <div class="dropdown dropstart">
                                    <a href="#" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-6"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('data_entrie.edit', $entry->id) }}"><i class="fs-4 ti ti-edit"></i>Edit</a></li>
                                        <li>
                                            <form action="{{ route('data_entrie.destroy', $entry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this entry? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item d-flex align-items-center gap-3 text-danger"><i class="fs-4 ti ti-trash"></i>Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                            {{-- HIDDEN DATA COLUMNS --}}
                            <td class="d-none">{{ $entry->newScholarship ?? '-' }}</td>   <!-- 6 -->
                            <td class="d-none">{{ $entry->newIelts ?? '-' }}</td>         <!-- 7 -->
                            <td class="d-none">{{ $entry->newpte ?? '-' }}</td>           <!-- 8 -->
                            <td class="d-none">{{ $entry->newPgIelts ?? '-' }}</td>       <!-- 9 -->
                            <td class="d-none">{{ $entry->newPgPte ?? '-' }}</td>         <!-- 10 -->
                            <td class="d-none">{{ $entry->newug ?? '-' }}</td>           <!-- 11 -->
                            <td class="d-none">{{ $entry->newpg ?? '-' }}</td>           <!-- 12 -->
                            <td class="d-none">{{ $entry->newgpaug ?? '-' }}</td>         <!-- 13 -->
                            <td class="d-none">{{ $entry->newgpapg ?? '-' }}</td>         <!-- 14 -->
                            <td class="d-none">{{ $entry->newtest ?? '-' }}</td>         <!-- 15 -->
                            <td class="d-none">{{ $entry->country ?? '-' }}</td>         <!-- 16 -->
                            <td class="d-none">{{ $entry->requireddocuments ?? '-' }}</td><!-- 17 -->
                            <td class="d-none">{{ $entry->level ?? '-' }}</td>           <!-- 18 -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted" id="paginationInfo">
                        Showing <span id="startItem">1</span> to <span id="endItem">10</span> of <span id="totalItems">{{ $data_entries->count() }}</span> entries
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

<!-- Import Modal (Unchanged) -->
<div id="importModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Excel File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="importForm" action="{{ route('dataentry.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Select Excel File (.xlsx, .xls)</label>
                        <input class="form-control" type="file" name="file" accept=".xlsx,.xls" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
// --- Modal and Import Logic (Preserved and Enhanced) ---
function openModal() {
    const modal = new bootstrap.Modal(document.getElementById('importModal'));
    modal.show();
}

document.getElementById('importForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    Swal.fire({
        title: 'Processing Import',
        text: 'Please wait, this may take a moment...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });
    fetch(this.action, {
        method: 'POST', body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success!', data.message, 'success').then(() => window.location.reload());
        } else {
            throw new Error(data.message || 'An unknown error occurred during import.');
        }
    })
    .catch(error => Swal.fire('Error!', error.message, 'error'));
});

// --- Data Export Function ---
function downloadData() {
    // Use the filtered rows from our table instance for accurate export
    const rowsToExport = window.advancedTableInstance.filteredRows;
    const headers = [
        "University", "Location", "Course", "Intake", "Tuition Fee", "Scholarship", "Country", "Level",
        "Required Documents", "UG IELTS", "UG PTE", "PG IELTS", "PG PTE", "UG Gap", "PG Gap",
        "UG GPA/Percentage", "PG GPA/Percentage", "Alternative English Test"
    ];
    const data = [headers];
    const indices = window.advancedTableInstance.columnIndices;

    rowsToExport.forEach(row => {
        const cols = row.querySelectorAll('td');
        const rowData = [
            cols[indices.university].querySelector('h6')?.textContent.trim() || '', // University
            cols[indices.location].textContent.trim(),                      // Location
            cols[indices.course].textContent.trim(),                        // Course
            cols[indices.intake].textContent.trim(),                        // Intake
            cols[indices.tuition].textContent.trim(),                       // Tuition
            cols[indices.hiddenScholarship].textContent.trim(),             // Scholarship
            cols[indices.hiddenCountry].textContent.trim(),                 // Country
            cols[indices.hiddenLevel].textContent.trim(),                   // Level
            cols[indices.hiddenDocuments].textContent.trim(),               // Documents
            cols[indices.hiddenUgIelts].textContent.trim(),                 // UG IELTS
            cols[indices.hiddenUgPte].textContent.trim(),                   // UG PTE
            cols[indices.hiddenPgIelts].textContent.trim(),                 // PG IELTS
            cols[indices.hiddenPgPte].textContent.trim(),                   // PG PTE
            cols[indices.hiddenUgGap].textContent.trim(),                   // UG Gap
            cols[indices.hiddenPgGap].textContent.trim(),                   // PG Gap
            cols[indices.hiddenUgGpa].textContent.trim(),                   // UG GPA
            cols[indices.hiddenPgGpa].textContent.trim(),                   // PG GPA
            cols[indices.hiddenEnglishTest].textContent.trim(),             // English Test
        ];
        data.push(rowData);
    });

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Universities');
    XLSX.writeFile(wb, `universities_data_${new Date().toISOString().split('T')[0]}.xlsx`);
}

// --- Main Table Class (Adapted from Lead Index) ---
class AdvancedTable {
    constructor(tableId, options) {
        this.table = document.getElementById(tableId);
        this.tbody = this.table.querySelector('tbody');
        this.originalRows = Array.from(this.tbody.querySelectorAll('tr'));
        this.itemsPerPage = options.itemsPerPage || 10;
        this.filterOptions = options.filterOptions || {};
        this.currentPage = options.initialPage || 1;
        this.filteredRows = [...this.originalRows];
        this.totalPages = 0;
        this.columnIndices = this.getColumnIndices();
        this.initPagination();
    }

    getColumnIndices() {
        // CRITICAL: This map must match the HTML structure of the <tr> exactly.
        return {
            university: 0, location: 1, course: 2, intake: 3, tuition: 4, actions: 5,
            hiddenScholarship: 6, hiddenUgIelts: 7, hiddenUgPte: 8, hiddenPgIelts: 9, hiddenPgPte: 10,
            hiddenUgGap: 11, hiddenPgGap: 12, hiddenUgGpa: 13, hiddenPgGpa: 14, hiddenEnglishTest: 15,
            hiddenCountry: 16, hiddenDocuments: 17, hiddenLevel: 18
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

    setFilter(key, value) { this.filterOptions[key] = value; this.currentPage = 1; }
    resetFilters() { this.filterOptions = { name: '', location: '', country: '', level: '' }; this.currentPage = 1; }

    _checkRowAgainstFilters(row) {
        const cols = row.querySelectorAll('td');
        const f = this.filterOptions;
        const i = this.columnIndices;

        // Search Filter (checks university, location, and course)
        if (f.name) {
            const searchTerm = f.name.toLowerCase();
            const universityText = cols[i.university].querySelector('h6')?.textContent.toLowerCase() || '';
            const locationText = cols[i.location].textContent.toLowerCase() || '';
            const courseText = cols[i.course].textContent.toLowerCase() || '';
            if (!universityText.includes(searchTerm) && !locationText.includes(searchTerm) && !courseText.includes(searchTerm)) return false;
        }

        // Dropdown Filters
        if (f.location && cols[i.location].textContent.trim() !== f.location) return false;
        if (f.country && cols[i.hiddenCountry].textContent.trim() !== f.country) return false;
        if (f.level && cols[i.hiddenLevel].textContent.trim() !== f.level) return false;

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

    clearPaginationLinks() { this.paginationControls.querySelectorAll('.page-item:not(#prevPage):not(#nextPage)').forEach(item => item.remove()); }

    generatePaginationLinks() {
        const maxVisible = 5; if (this.totalPages <= 1) return;
        const createPageItem = (page, isActive = false, isEllipsis = false) => {
            const li = document.createElement('li'); li.className = `page-item ${isActive ? 'active' : ''} ${isEllipsis ? 'disabled ellipsis' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${isEllipsis ? '...' : page}</a>`; return li;
        };
        if (this.totalPages <= maxVisible) {
            for (let i = 1; i <= this.totalPages; i++) this.nextPage.insertAdjacentElement('beforebegin', createPageItem(i, i === this.currentPage));
        } else {
            this.nextPage.insertAdjacentElement('beforebegin', createPageItem(1, 1 === this.currentPage));
            if (this.currentPage > 3) this.nextPage.insertAdjacentElement('beforebegin', createPageItem(0, false, true));
            let start = Math.max(2, this.currentPage - 1), end = Math.min(this.totalPages - 1, this.currentPage + 1);
            if (this.currentPage === 1) end = 3; if (this.currentPage === this.totalPages) start = this.totalPages - 2;
            for (let i = start; i <= end; i++) this.nextPage.insertAdjacentElement('beforebegin', createPageItem(i, i === this.currentPage));
            if (this.currentPage < this.totalPages - 2) this.nextPage.insertAdjacentElement('beforebegin', createPageItem(0, false, true));
            this.nextPage.insertAdjacentElement('beforebegin', createPageItem(this.totalPages, this.totalPages === this.currentPage));
        }
    }
}

// --- State Management ---
const stateKey = 'universityTableState';
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
            if(state.filterOptions) {
                document.getElementById('filterLocation').value = state.filterOptions.location || '';
                document.getElementById('filterCountry').value = state.filterOptions.country || '';
                document.getElementById('filterLevel').value = state.filterOptions.level || '';
            }
            return state;
        } catch (e) { localStorage.removeItem(stateKey); return null; }
    }
    return null;
}

// --- DOMContentLoaded Initializer ---
document.addEventListener('DOMContentLoaded', function() {
    const loadedState = loadState();
    const initialOptions = {
        itemsPerPage: 10,
        filterOptions: loadedState ? loadedState.filterOptions : { name: '', location: '', country: '', level: '' },
        initialPage: loadedState ? loadedState.currentPage : 1
    };

    const table = new AdvancedTable('dataTable', initialOptions);
    window.advancedTableInstance = table;
    table.filterAndPaginate();

    // --- Event Listeners for Instant Filtering ---
    const applyFiltersAndPaginate = () => { table.filterAndPaginate(); };

    document.getElementById('searchInput').addEventListener('input', (e) => { table.setFilter('name', e.target.value); applyFiltersAndPaginate(); });
    document.getElementById('filterLocation').addEventListener('change', (e) => { table.setFilter('location', e.target.value); applyFiltersAndPaginate(); });
    document.getElementById('filterCountry').addEventListener('change', (e) => { table.setFilter('country', e.target.value); applyFiltersAndPaginate(); });
    document.getElementById('filterLevel').addEventListener('change', (e) => { table.setFilter('level', e.target.value); applyFiltersAndPaginate(); });

    // --- Button Listeners ---
    document.getElementById('toggleFilters')?.addEventListener('click', () => {
        const panel = document.getElementById('filterPanel');
        panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'block' : 'none';
        saveState(table);
    });
    
    document.getElementById('applyFilters').addEventListener('click', applyFiltersAndPaginate);

    document.getElementById('resetFilters').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterLocation').value = '';
        document.getElementById('filterCountry').value = '';
        document.getElementById('filterLevel').value = '';
        document.getElementById('filterPanel').style.display = 'none';
        localStorage.removeItem(stateKey);
        table.resetFilters();
        applyFiltersAndPaginate();
    });
});
</script>

@endsection