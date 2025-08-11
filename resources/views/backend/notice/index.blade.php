@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')

{{-- External Libraries --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Success Message Popup --}}
@if (session('success'))
<script>
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'OK'
    });
</script>
@endif

{{-- Page-Specific Styles (Copied from previous designs for consistency) --}}
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
    .tooltip-text {
        position: absolute; bottom: -35px; left: 50%; transform: translateX(-50%);
        background: #333; color: white; padding: 6px 12px; border-radius: 4px;
        font-size: 12px; white-space: nowrap; opacity: 0; visibility: hidden;
        transition: all 0.2s ease; z-index: 1000;
    }
    .icon-btn:hover .tooltip-text { opacity: 1; visibility: visible; }
    .table-hover tbody tr:hover { background-color: rgba(46, 125, 50, 0.05); }
    .dropdown-menu { z-index: 1010; padding: 0px !important; border-radius: 8px; }
    .dropdown-item.text-danger:hover { background-color: #fbebeb !important; color: #dc3545 !important; }
</style>

<div class="main-container">
    <div class="widget-content searchable-container list">
        {{-- Header Section with Search and Action Buttons --}}
        <div class="card card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="ti ti-bell-ringing text-2xl text-[#2e7d32] me-2"></i>
                    <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Notice Board</div>
                </div>
                <div class="d-flex gap-2">
                    <div class="position-relative" style="width: 250px;">
                        <input type="text" class="form-control product-search ps-5" id="searchInput" placeholder="Search notices...">
                        <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                    </div>
                    @if (auth()->user()->roles->pluck('name')->intersect(['Administrator', 'Manager', 'Leads Manager', 'Applications Manager'])->isNotEmpty())
                        <a href="{{ route('backend.notice.create') }}" class="icon-btn btn-success">
                            <i class="ti ti-plus fs-5"></i>
                            <span class="tooltip-text">Add Notice</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Notice Data Table --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="noticeTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%;"><div class="d-flex align-items-center"><i class="ti ti-subtask me-1"></i><span>Title</span></div></th>
                            <th style="width: 45%;"><div class="d-flex align-items-center"><i class="ti ti-file-text me-1"></i><span>Description</span></div></th>
                            <th style="width: 20%;"><div class="d-flex align-items-center"><i class="ti ti-photo me-1"></i><span>Image</span></div></th>
                            <th style="width: 10%;"><div class="d-flex align-items-center"><i class="ti ti-settings me-1"></i><span>Actions</span></div></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($notices as $notice)
                        <tr>
                            <!-- 1: Title -->
                            <td class="align-middle">
                                <h6 class="fw-semibold mb-0 fs-4">{{ $notice->title }}</h6>
                            </td>
                            <!-- 2: Description -->
                            <td class="align-middle">
                                <p class="mb-0">{{ \Illuminate\Support\Str::limit($notice->description, 100, '...') }}</p>
                            </td>
                            <!-- 3: Image -->
                            <td class="align-middle">
                                @if($notice->image)
                                    <img src="{{ asset('storage/' . $notice->image) }}" alt="Notice Image" style="width: 120px; height: 60px; object-fit: cover;" class="rounded" />
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <!-- 4: Actions -->
                            <td class="align-middle">
                                <div class="dropdown dropstart">
                                    <a href="#" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-6"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('backend.notice.show', $notice->id) }}"><i class="fs-4 ti ti-eye"></i>View</a></li>
                                        <li><a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('backend.notice.edit', $notice->id) }}"><i class="fs-4 ti ti-edit"></i>Edit</a></li>
                              
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <p class="mb-0">No notices found.</p>
                                @if (auth()->user()->roles->pluck('name')->intersect(['Administrator', 'Manager', 'Leads Manager', 'Applications Manager'])->isNotEmpty())
                                    <a href="{{ route('backend.notice.create') }}" class="btn btn-sm btn-success mt-2">Add the First Notice</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted" id="paginationInfo">
                        Showing <span id="startItem">1</span> to <span id="endItem">10</span> of <span id="totalItems">{{ $notices->count() }}</span> entries
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
// --- Main Table Class (Simplified for Search and Pagination only) ---
class AdvancedTable {
    constructor(tableId, options) {
        this.table = document.getElementById(tableId);
        this.tbody = this.table.querySelector('tbody');
        this.originalRows = Array.from(this.tbody.querySelectorAll('tr'));
        this.itemsPerPage = options.itemsPerPage || 8;
        this.filterOptions = options.filterOptions || { name: '' };
        this.currentPage = options.initialPage || 1;
        this.filteredRows = [...this.originalRows];
        this.totalPages = 0;
        this.initPagination();
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
    
    _checkRowAgainstFilters(row) {
        if (!this.filterOptions.name) return true; // Show all if search is empty
        const searchTerm = this.filterOptions.name.toLowerCase();
        
        // Search in title (column 0) and description (column 1)
        const titleText = row.cells[0]?.textContent.toLowerCase() || '';
        const descriptionText = row.cells[1]?.textContent.toLowerCase() || '';
        
        return titleText.includes(searchTerm) || descriptionText.includes(searchTerm);
    }

    filterAndPaginate() {
        // Hide "no results" row before filtering
        const noResultsRow = this.tbody.querySelector('.no-results-row');
        if (noResultsRow) noResultsRow.remove();

        this.filteredRows = this.originalRows.filter(row => this._checkRowAgainstFilters(row));
        this.totalPages = Math.ceil(this.filteredRows.length / this.itemsPerPage);
        
        if (this.currentPage > this.totalPages && this.totalPages > 0) this.currentPage = this.totalPages;
        else if (this.totalPages === 0) this.currentPage = 1;

        // Show "no results" message if needed
        if (this.filteredRows.length === 0 && this.originalRows.length > 0) {
            this.tbody.innerHTML = `<tr class="no-results-row"><td colspan="4" class="text-center py-4">No notices match your search.</td></tr>`;
        }

        this.updatePagination();
    }

    updatePagination() {
        this.clearPaginationLinks();
        this.originalRows.forEach(row => row.style.display = 'none'); // Hide all original rows first

        // If there are filtered rows, display the correct slice
        if (this.filteredRows.length > 0) {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = Math.min(startIndex + this.itemsPerPage, this.filteredRows.length);
            for (let i = startIndex; i < endIndex; i++) {
                if (this.filteredRows[i]) {
                    // Re-append to tbody to ensure correct order after search
                    this.tbody.appendChild(this.filteredRows[i]);
                    this.filteredRows[i].style.display = '';
                }
            }
        }

        document.getElementById('startItem').textContent = this.filteredRows.length === 0 ? '0' : ((this.currentPage - 1) * this.itemsPerPage) + 1;
        document.getElementById('endItem').textContent = Math.min(this.currentPage * this.itemsPerPage, this.filteredRows.length);
        document.getElementById('totalItems').textContent = this.filteredRows.length;
        
        this.prevPage.classList.toggle('disabled', this.currentPage <= 1);
        this.nextPage.classList.toggle('disabled', this.currentPage >= this.totalPages || this.totalPages === 0);
        this.generatePaginationLinks();
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


// --- DOMContentLoaded Initializer ---
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if there are notices to manage
    if (document.getElementById('tableBody').children.length === 0 || document.querySelector('.no-results-row')) {
        document.getElementById('paginationInfo').style.display = 'none';
        document.getElementById('paginationControls').style.display = 'none';
        return;
    }

    const initialOptions = {
        itemsPerPage: 8,
        initialPage: 1
    };

    const table = new AdvancedTable('noticeTable', initialOptions);
    window.advancedTableInstance = table;
    table.filterAndPaginate(); // Initial render

    // --- Event Listener for Search Input ---
    document.getElementById('searchInput').addEventListener('input', (e) => {
        table.setFilter('name', e.target.value);
        table.filterAndPaginate();
    });
});
</script>

@endsection