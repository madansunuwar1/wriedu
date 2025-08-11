<template>
    <!-- Success/Error Message Popups -->
    <!-- SweetAlert2 is controlled programmatically in the script section -->

    <div class="main-container">
        <div class="widget-content searchable-container list">
            <!-- Header Section with Search and Action Buttons -->
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
                        <input type="text" v-model="filters.search" class="form-control product-search ps-5"
                            placeholder="Search...">
                        <i
                            class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                    </div>
                    <div class="d-flex gap-2">
                        <button @click="toggleFilterPanel" class="icon-btn btn-outline-secondary">
                            <i class="ti ti-filter fs-5"></i>
                            <span class="tooltip-text">Filters</span>
                        </button>
                        <button @click="openImportModal" class="icon-btn btn-primary">
                            <i class="ti ti-upload fs-5"></i>
                            <span class="tooltip-text">Import</span>
                        </button>
                        <button @click="exportData" class="icon-btn btn-outline-primary">
                            <i class="ti ti-file-export fs-5"></i>
                            <span class="tooltip-text">Export</span>
                        </button>
                    </div>
                </div>

                <!-- Collapsible Filter Panel -->
                <div v-show="isFilterPanelVisible" id="filterPanel"
                    class="mt-3 p-3 border rounded bg-light filter-panel-transition"
                    style="background-color: rgb(248 252 255) !important">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">Location</label>
                            <select v-model="filters.location" class="form-select">
                                <option value="">All Locations</option>
                                <option v-for="location in filterOptions.locations" :key="location" :value="location">{{
                                    location }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">Country</label>
                            <select v-model="filters.country" class="form-select">
                                <option value="">All Countries</option>
                                <option v-for="country in filterOptions.countries" :key="country" :value="country">{{
                                    country }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">Level</label>
                            <select v-model="filters.level" class="form-select">
                                <option value="">All Levels</option>
                                <option v-for="level in filterOptions.levels" :key="level" :value="level">{{ level }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 d-flex align-items-end gap-2">
                            <button @click="resetFilters" class="icon-btn btn-outline-secondary">
                                <i class="ti ti-rotate-2 fs-5"></i><span class="tooltip-text">Reset</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- University Data Table -->
            <div class="card border-0 shadow-sm">
                <div v-if="isLoading" class="p-5 text-center">Loading data...</div>
                <div v-else class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="d-flex align-items-center"><i
                                            class="ti ti-building-skyscraper me-1"></i><span>University</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i
                                            class="ti ti-map-pin me-1"></i><span>Location</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i
                                            class="ti ti-book me-1"></i><span>Course</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i
                                            class="ti ti-calendar me-1"></i><span>Intake</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i
                                            class="ti ti-currency-dollar me-1"></i><span>Tuition</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i
                                            class="ti ti-settings me-1"></i><span>Actions</span></div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="paginatedEntries.length === 0">
                                <td colspan="6" class="text-center p-4">No matching records found.</td>
                            </tr>
                            <tr v-for="entry in paginatedEntries" :key="entry.id">
                                <!-- This is the "After" state with the link -->
                                <td>
                                    <!-- Use router-link to navigate to the named route -->
                                    <router-link :to="{ name: 'universit.profile', params: { id: entry.id } }"
                                        class="text-decoration-none d-flex align-items-center">
                                        <img v-if="getUniversityImage(entry.newUniversity)"
                                            :src="getUniversityImage(entry.newUniversity)" :alt="entry.newUniversity"
                                            width="40" height="40" class="rounded-circle me-3"
                                            style="object-fit: contain; background: white;">
                                        <div v-else class="rounded-circle me-3"
                                            style="width: 40px; height: 40px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                            <i class="ti ti-building-skyscraper text-muted"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0 fs-4 text-dark">{{ entry.newUniversity ?? 'N/A'
                                            }}</h6>
                                            <small class="text-muted">{{ entry.country ?? '' }}</small>
                                        </div>
                                    </router-link>
                                </td>
                                <td>{{ entry.newLocation ?? '-' }}</td>
                                <td>{{ entry.newCourse ?? '-' }}</td>
                                <td>{{ entry.newIntake ?? '-' }}</td>
                                <td>{{ entry.newAmount ?? '-' }}</td>
                                <td>
                                    <div class="dropdown dropstart">
                                        <a href="#" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical fs-6"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item d-flex align-items-center gap-3"
                                                    :href="`/admin/data-entries/${entry.id}/edit`"><i
                                                        class="fs-4 ti ti-edit"></i>Edit</a></li>
                                            <li>
                                                <button @click="confirmDelete(entry.id)"
                                                    class="dropdown-item d-flex align-items-center gap-3 text-danger"><i
                                                        class="fs-4 ti ti-trash"></i>Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div v-if="!isLoading && filteredEntries.length > 0" class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ paginationInfo.startItem }} to {{ paginationInfo.endItem }} of {{
                                paginationInfo.totalItems }} entries
                        </div>
                        <div>
                            <nav>
                                <ul class="pagination mb-0">
                                    <!-- Previous Button -->
                                    <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
                                        <a class="page-link" href="#"
                                            @click.prevent="changePage(pagination.currentPage - 1)">
                                            <i class="ti ti-chevron-left"></i>
                                        </a>
                                    </li>

                                    <!-- Page Numbers -->
                                    <li v-for="page in pages" :key="page.name" class="page-item"
                                        :class="{ active: page.isActive, disabled: page.isDisabled }">
                                        <a class="page-link" href="#" @click.prevent="changePage(page.name)">
                                            {{ page.name }}
                                        </a>
                                    </li>

                                    <!-- Next Button -->
                                    <li class="page-item" :class="{ disabled: pagination.currentPage === totalPages }">
                                        <a class="page-link" href="#"
                                            @click.prevent="changePage(pagination.currentPage + 1)">
                                            <i class="ti ti-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="modal fade" tabindex="-1" aria-hidden="true" ref="importModalRef">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Excel File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="handleImport">
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Select Excel File (.xlsx, .csv)</label>
                            <input class="form-control" type="file" name="file" ref="fileInputRef" accept=".xlsx,.csv"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import * as XLSX from 'xlsx';
import { Modal } from 'bootstrap';

// --- STATE ---
const allEntries = ref([]);
const universities = ref([]);
const isLoading = ref(true);
const isFilterPanelVisible = ref(false);
const stateKey = 'universityTableState';

// Reactive objects for filters and pagination
const filters = reactive({
    search: '',
    location: '',
    country: '',
    level: ''
});

const pagination = reactive({
    currentPage: 1,
    itemsPerPage: 10
});

// Modal and file input refs
const importModalRef = ref(null);
let importModalInstance = null;
const fileInputRef = ref(null);

// --- COMPUTED PROPERTIES (Derived State) ---
const pages = computed(() => {
    if (!totalPages.value) return []; // Use totalPages to check if pagination is needed

    const pageNumbers = [];
    const currentPage = pagination.currentPage;
    const lastPage = totalPages.value;
    const range = 2; // How many pages to show on each side of the current page

    let from = currentPage - range;
    if (from < 1) from = 1;

    let to = currentPage + range;
    if (to > lastPage) to = lastPage;

    // Add the page numbers in the calculated range
    for (let page = from; page <= to; page++) {
        pageNumbers.push({ name: page, isActive: page === currentPage, isDisabled: false });
    }

    // Add ellipsis and first page if needed
    if (from > 1) {
        if (from > 2) {
            pageNumbers.unshift({ name: '...', isActive: false, isDisabled: true });
        }
        pageNumbers.unshift({ name: 1, isActive: false, isDisabled: false });
    }

    // Add ellipsis and last page if needed
    if (to < lastPage) {
        if (to < lastPage - 1) {
            pageNumbers.push({ name: '...', isActive: false, isDisabled: true });
        }
        pageNumbers.push({ name: lastPage, isActive: false, isDisabled: false });
    }

    return pageNumbers;
});
// Filter entries based on search, location, country, and level
const filteredEntries = computed(() => {
    // Reset page to 1 whenever filters change, except for the initial load


    if (!allEntries.value.length) return [];

    return allEntries.value.filter(entry => {
        const searchLower = filters.search.toLowerCase();
        const matchesSearch = !filters.search ||
            (entry.newUniversity?.toLowerCase().includes(searchLower)) ||
            (entry.newLocation?.toLowerCase().includes(searchLower)) ||
            (entry.newCourse?.toLowerCase().includes(searchLower));

        const matchesLocation = !filters.location || entry.newLocation === filters.location;
        const matchesCountry = !filters.country || entry.country === filters.country;
        const matchesLevel = !filters.level || entry.level === filters.level;

        return matchesSearch && matchesLocation && matchesCountry && matchesLevel;
    });
});

// Paginate the filtered entries
const paginatedEntries = computed(() => {
    const start = (pagination.currentPage - 1) * pagination.itemsPerPage;
    const end = start + pagination.itemsPerPage;
    return filteredEntries.value.slice(start, end);
});

// Calculate total pages for pagination controls
const totalPages = computed(() => {
    return Math.ceil(filteredEntries.value.length / pagination.itemsPerPage);
});

// Generate dynamic options for filter dropdowns
const filterOptions = computed(() => {
    const locations = [...new Set(allEntries.value.map(e => e.newLocation).filter(Boolean))].sort();
    const countries = [...new Set(allEntries.value.map(e => e.country).filter(Boolean))].sort();
    const levels = [...new Set(allEntries.value.map(e => e.level).filter(Boolean))].sort();
    return { locations, countries, levels };
});

// Generate text for "Showing X to Y of Z"
const paginationInfo = computed(() => {
    const totalItems = filteredEntries.value.length;
    const startItem = totalItems > 0 ? (pagination.currentPage - 1) * pagination.itemsPerPage + 1 : 0;
    const endItem = Math.min(startItem + pagination.itemsPerPage - 1, totalItems);
    return { startItem, endItem, totalItems };
});


// --- METHODS ---

// Fetch initial data from the API
const fetchData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/university-data');
        allEntries.value = response.data.data_entries;
        universities.value = response.data.universities;
    } catch (error) {
        Swal.fire('Error!', 'Failed to load university data.', 'error');
        console.error("Data fetch error:", error);
    } finally {
        isLoading.value = false;
    }
};

// Get university image URL
const getUniversityImage = (universityName) => {
    const university = universities.value.find(u => u.name === universityName);
    return university ? university.image_link : null;
};

// Change pagination page
const changePage = (page) => {
    // Add this line to ignore clicks on '...'
    if (page === '...') return;

    if (page > 0 && page <= totalPages.value) {
        pagination.currentPage = page;
    }
};

// Toggle filter panel visibility
const toggleFilterPanel = () => {
    isFilterPanelVisible.value = !isFilterPanelVisible.value;
};

// Reset all filters to their default state
const resetFilters = () => {
    filters.search = '';
    filters.location = '';
    filters.country = '';
    filters.level = '';
    isFilterPanelVisible.value = false;
    pagination.currentPage = 1;
};

// Confirm and handle deletion of an entry
const confirmDelete = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                // Use the API route for deletion
                await axios.delete(`/api/data-entries/${id}`);
                // Remove from local state for instant UI update
                allEntries.value = allEntries.value.filter(entry => entry.id !== id);
                Swal.fire('Deleted!', 'The data entry has been deleted.', 'success');
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete the entry.', 'error');
                console.error("Delete error:", error);
            }
        }
    });
};

// Open the Bootstrap modal for importing
const openImportModal = () => {
    if (importModalInstance) {
        importModalInstance.show();
    }
};

// Handle the file import submission
const handleImport = async () => {
    const file = fileInputRef.value.files[0];
    if (!file) {
        Swal.fire('No File', 'Please select a file to import.', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);

    Swal.fire({
        title: 'Processing Import',
        text: 'Please wait, this may take a moment...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        // Use the API route for importing
        const response = await axios.post('/api/data-entries/import', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        importModalInstance.hide();
        Swal.fire('Success!', response.data.message, 'success');
        fetchData(); // Refresh data after import
    } catch (error) {
        const errorMessage = error.response?.data?.message || 'An unknown error occurred during import.';
        Swal.fire('Error!', errorMessage, 'error');
    }
};

// Export currently filtered data to an Excel file
const exportData = () => {
    const headers = [
        "University", "Location", "Course", "Intake", "Tuition", "Scholarship", "Country", "Level",
        "Required Documents", "UG IELTS", "UG PTE", "PG IELTS", "PG PTE", "UG Gap", "PG Gap",
        "UG GPA or Percentage", "PG GPA or Percentage", "English Test"
    ];

    const dataToExport = filteredEntries.value.map(entry => [
        entry.newUniversity, entry.newLocation, entry.newCourse, entry.newIntake, entry.newAmount,
        entry.newScholarship, entry.country, entry.level, entry.requireddocuments,
        entry.newIelts, entry.newpte, entry.newPgIelts, entry.newPgPte,
        entry.newug, entry.newpg, entry.newgpaug, entry.newgpapg, entry.newtest
    ]);

    const data = [headers, ...dataToExport];
    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Universities');
    XLSX.writeFile(wb, `universities_data_${new Date().toISOString().split('T')[0]}.xlsx`);
};

// --- LIFECYCLE & STATE MANAGEMENT ---

// Load state from localStorage
const loadState = () => {
    const savedState = localStorage.getItem(stateKey);
    if (savedState) {
        try {
            const state = JSON.parse(savedState);
            Object.assign(filters, state.filters || {});
            pagination.currentPage = state.pagination?.currentPage || 1;
            isFilterPanelVisible.value = state.isFilterPanelVisible || false;
        } catch (e) {
            console.error("Could not parse saved state", e);
            localStorage.removeItem(stateKey);
        }
    }
};

// Save state to localStorage whenever it changes
watch([pagination, isFilterPanelVisible], (newState) => {
    const stateToSave = {
        filters: { ...filters },
        pagination: { ...pagination },
        isFilterPanelVisible: isFilterPanelVisible.value
    };
    localStorage.setItem(stateKey, JSON.stringify(stateToSave));
}, { deep: true });

onMounted(() => {
    loadState();
    fetchData();
    if (importModalRef.value) {
        importModalInstance = new Modal(importModalRef.value);
    }
});

</script>

<style scoped>
/* Copied styles from the original blade file */


.icon-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    background: white;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.icon-btn:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.icon-btn.btn-primary:hover {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.icon-btn.btn-outline-primary:hover {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.icon-btn.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
}

.tooltip-text {
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s ease;
    z-index: 1000;
}

.tooltip-text::before {
    content: '';
    position: absolute;
    top: -4px;
    left: 50%;
    transform: translateX(-50%);
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-bottom: 4px solid #333;
}

.icon-btn:hover .tooltip-text {
    opacity: 1;
    visibility: visible;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.filter-panel-transition {
    transition: all 0.3s ease;
}

.dropdown-menu {
    z-index: 1010;
    padding: 0px !important;
    border-radius: 8px;
}

.dropdown-item.text-danger:hover {
    background-color: #fbebeb !important;
    color: #dc3545 !important;
}

/* Make dropdown buttons work correctly */
.dropdown-item {
    cursor: pointer;
}

.dropdown-item button {
    background: none;
    border: none;
    padding: 0;
    width: 100%;
    text-align: left;
}
</style>