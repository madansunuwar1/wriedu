@extends('layouts.admin')
@include('backend.script.session')
@include('backend.script.alert')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@if(session('success')) <script> Swal.fire({ title: 'Success!', text: "{{ session('success') }}", icon: 'success', confirmButtonText: 'OK', customClass: { popup: 'swal-custom-popup' } }); </script> @endif
@if(session('error')) <script> Swal.fire({ title: 'Error!', text: "{{ session('error') }}", icon: 'error', confirmButtonText: 'OK', customClass: { popup: 'swal-custom-popup' } }); </script> @endif

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
    .user-name-link:hover h6 { color: #2e7d32; }
    .modal { display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
    .modal-content { background-color: #fff; margin: 10% auto; padding: 25px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); width: 90%; max-width: 500px; border: none; }
    .modal-title { font-size: 1.25rem; font-weight: 600; color: #2e7d32; margin-bottom: 20px; }
    .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; transition: color 0.2s; }
    .close:hover { color: #666; }
</style>

<div class="main-container">
    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-2xl text-[#2e7d32] me-2"></i>
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Enquiry Data</div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <div class="position-relative" style="width: 300px;">
                    <input type="text" class="form-control product-search ps-5" id="searchInput" placeholder="Search enquiries...">
                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                </div>
                <div class="d-flex gap-2">
                    <button id="toggleFilters" class="icon-btn btn-outline-secondary">
                        <i class="ti ti-filter fs-5"></i><span class="tooltip-text">Filters</span>
                    </button>
                    <a href="{{ route('backend.enquiry.create') }}" class="icon-btn btn-success">
                        <i class="ti ti-plus fs-5"></i><span class="tooltip-text">Add Enquiry</span>
                    </a>
                    <button onclick="openModal()" class="icon-btn btn-outline-primary">
                        <i class="ti ti-upload fs-5"></i><span class="tooltip-text">Import</span>
                    </button>
                    <button onclick="downloadData()" class="icon-btn btn-outline-primary">
                        <i class="ti ti-file-export fs-5"></i><span class="tooltip-text">Export</span>
                    </button>
                </div>
            </div>

            <div id="filterPanel" class="mt-3 p-3 border rounded bg-light filter-panel-transition" style="display: none;background-color: rgb(248 252 255) !important">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Country</label>
                        <select class="form-select" id="filterCountry">
                            <option value="">All Countries</option>
                            @foreach($enquiries->pluck('country')->unique()->filter() as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">English Test</label>
                        <select class="form-select" id="filterEnglishTest">
                            <option value="">All Tests</option>
                            <option value="IELTS">IELTS</option>
                            <option value="PTE">PTE</option>
                            <option value="TOEFL">TOEFL</option>
                            <option value="ELLT">ELLT</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-medium">Other Exam</label>
                        <select class="form-select" id="filterOtherExam">
                            <option value="">All Exams</option>
                            <option value="SAT">SAT</option>
                            <option value="GRE">GRE</option>
                            <option value="GMAT">GMAT</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end gap-2">
                        <button id="resetFilters" class="icon-btn btn-outline-secondary">
                            <i class="ti ti-rotate-2 fs-5"></i><span class="tooltip-text">Reset</span>
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
                            <th><div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Name</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-phone me-1"></i><span>Contact</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-flag me-1"></i><span>Country</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-file-text me-1"></i><span>English Tests</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-certificate me-1"></i><span>Other Exams</span></div></th>
                            <th><div class="d-flex align-items-center"><i class="ti ti-calendar me-1"></i><span>Date Added</span></div></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($enquiries as $enquiry)
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('backend.enquiryhistory.records', $enquiry->id) }}'">
                            <td>
                                <div class="user-info d-flex align-items-center" onclick="event.stopPropagation();">
                                    <div class="ms-3 flex-grow-1">
                                        <a href="{{ route('backend.enquiryhistory.records', $enquiry->id) }}" class="text-dark text-decoration-none user-name-link">
                                            <h6 class="user-name mb-0 fw-medium">{{ $enquiry->uname ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $enquiry->email ?? '' }}</small>
                                        </a>
                                    </div>
                                    <div class="user-actions">
                                        <div class="dropdown">
                                            <button class="action-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                {{-- The "View" action is now the entire row click --}}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $enquiry->contact ?? '-' }}</td>
                            <td>{{ $enquiry->country ?? '-' }}</td>
                            <td>
                                @php
                                    $englishTests = [];
                                    if($enquiry->ielts !== 'N/A') $englishTests[] = 'IELTS';
                                    if($enquiry->pte !== 'N/A') $englishTests[] = 'PTE';
                                    if($enquiry->toefl !== 'N/A') $englishTests[] = 'TOEFL';
                                    if($enquiry->ellt !== 'N/A') $englishTests[] = 'ELLT';
                                    echo implode(', ', $englishTests);
                                @endphp
                            </td>
                            <td>
                                @php
                                    $otherExams = [];
                                    if($enquiry->sat !== 'N/A') $otherExams[] = 'SAT';
                                    if($enquiry->gre !== 'N/A') $otherExams[] = 'GRE';
                                    if($enquiry->gmat !== 'N/A') $otherExams[] = 'GMAT';
                                    echo implode(', ', $otherExams);
                                @endphp
                            </td>
                            <td>{{ $enquiry->created_at ? $enquiry->created_at->format('Y-m-d') : '-' }}</td>
                            <!-- Hidden columns for search/export -->
                            <td class="d-none">{{ $enquiry->email ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4">No enquiries found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted" id="paginationInfo">
                        Showing <span id="startItem">1</span> to <span id="endItem">10</span> of <span id="totalItems">{{ count($enquiries) }}</span> entries
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

<!-- Import Modal -->
<div id="importModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <h2 class="modal-title">Import Excel File</h2>
        <form id="importForm" action="{{ route('enquiry.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3"><input type="file" class="form-control" name="file" accept=".csv,.xlsx,.xls" required></div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>

<script>
// --- Action & Modal Functions ---
function openModal() { document.getElementById('importModal').style.display = 'block'; }
function closeModal() { document.getElementById('importModal').style.display = 'none'; }
window.onclick = function(event) { if (event.target.id === 'importModal') closeModal(); };

// --- Data Export Function ---
function downloadData() {
    const rowsToExport = window.advancedTableInstance.filteredRows;
    const headers = ["Student Name", "Email", "Contact", "Country", "English Tests", "Other Exams", "Date Added"];
    const data = [headers];
    const i = window.advancedTableInstance.columnIndices;

    rowsToExport.forEach(row => {
        const cols = row.querySelectorAll('td');
        const rowData = [
            cols[i.name].querySelector('h6')?.textContent.trim() || '',
            cols[i.hiddenEmail].textContent.trim(),
            cols[i.contact].textContent.trim(),
            cols[i.country].textContent.trim(),
            cols[i.englishTests].textContent.trim(),
            cols[i.otherExams].textContent.trim(),
            cols[i.dateAdded].textContent.trim()
        ];
        data.push(rowData);
    });

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Enquiries');
    XLSX.writeFile(wb, `enquiries_data_${new Date().toISOString().split('T')[0]}.xlsx`);
}

// --- Main Table Class ---
class AdvancedTable {
    constructor(tableId, options) {
        this.table = document.getElementById(tableId);
        this.tbody = this.table.querySelector('tbody');
        this.originalRows = Array.from(this.tbody.querySelectorAll('tr'));
        this.itemsPerPage = 10;
        this.filterOptions = options.filterOptions || {};
        this.currentPage = options.initialPage || 1;
        this.filteredRows = [...this.originalRows];
        this.totalPages = 0;
        this.columnIndices = {
            name: 0, contact: 1, country: 2, englishTests: 3, otherExams: 4, dateAdded: 5,
            hiddenEmail: 6
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
            const emailText = cols[i.hiddenEmail].textContent.toLowerCase();
            const contactText = cols[i.contact].textContent.toLowerCase();
            if (!nameText.includes(term) && !emailText.includes(term) && !contactText.includes(term)) return false;
        }
        if (f.country && cols[i.country].textContent.trim() !== f.country) return false;
        if (f.englishTest && !cols[i.englishTests].textContent.includes(f.englishTest)) return false;
        if (f.otherExam && !cols[i.otherExams].textContent.includes(f.otherExam)) return false;
        
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
const stateKey = 'enquiryTableState';
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
                document.getElementById('filterCountry').value = state.filterOptions.country || '';
                document.getElementById('filterEnglishTest').value = state.filterOptions.englishTest || '';
                document.getElementById('filterOtherExam').value = state.filterOptions.otherExam || '';
            }
            document.getElementById('filterPanel').style.display = state.filterPanelOpen ? 'block' : 'none';
            return state;
        } catch (e) { localStorage.removeItem(stateKey); return null; }
    }
    return null;
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
    addInstantFilter('filterCountry', 'country');
    addInstantFilter('filterEnglishTest', 'englishTest');
    addInstantFilter('filterOtherExam', 'otherExam');

    document.getElementById('toggleFilters')?.addEventListener('click', () => {
        const panel = document.getElementById('filterPanel');
        panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'block' : 'none';
        saveState(table);
    });

    document.getElementById('resetFilters')?.addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.querySelectorAll('#filterPanel select').forEach(el => el.value = '');
        localStorage.removeItem(stateKey);
        table.resetFilters();
        table.filterAndPaginate();
    });
});
</script>
@endsection