<template>
  <!-- secure-mode class handles text selection prevention -->
  <div class="main-container" :class="{ 'secure-mode': !canManageData }">
    <div class="widget-content searchable-container list">
      <!-- Header and Filter Panel -->
      <div class="card card-body">
        <div class="d-flex align-items-center">
          <i class="ti ti-database text-2xl text-[#2e7d32] me-2"></i>
          <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Application Data</div>
        </div>

        <div class="d-flex justify-content-between mt-3">
          <div class="position-relative" style="width: 300px;">
            <input type="text" class="form-control product-search ps-5" v-model="filters.search"
              placeholder="Search applications...">
            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
          </div>
          <div class="d-flex gap-2">
            <button @click="isFilterPanelOpen = !isFilterPanelOpen" title="Toggle Filters"
              class="icon-btn btn-outline-secondary">
              <i class="ti ti-filter fs-5"></i>
              <span class="tooltip-text">Filters</span>
            </button>
            <router-link to="/applications/create" title="Add Application" class="icon-btn btn-success">
              <i class="ti ti-plus fs-5"></i>
              <span class="tooltip-text">Add Application Entry</span>
            </router-link>

            <button v-if="canManageData" @click="isImportModalOpen = true" title="Import Data"
              class="icon-btn btn-outline-primary">
              <i class="ti ti-file-import fs-5"></i>
              <span class="tooltip-text">Import</span>
            </button>

            <button v-if="canManageData" @click="exportData" title="Export Data" class="icon-btn btn-outline-primary">
              <i class="ti ti-file-export fs-5"></i>
              <span class="tooltip-text">Export</span>
            </button>
          </div>
        </div>

        <div v-show="isFilterPanelOpen" class="mt-3 p-3 border rounded bg-light filter-panel-transition">
          <div class="row">
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">University</label>
              <select class="form-select" v-model="filters.university">
                <option value="">All Universities</option>
                <option v-for="uni in filterOptions.universities" :key="uni" :value="uni">{{ uni }}</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Course</label>
              <select class="form-select" v-model="filters.course">
                <option value="">All Courses</option>
                <option v-for="course in filterOptions.courses" :key="course" :value="course">{{ course }}</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Intake</label>
              <select class="form-select" v-model="filters.intake">
                <option value="">All Intakes</option>
                <option v-for="intake in filterOptions.intakes" :key="intake" :value="intake">{{ intake }}</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Status</label>
              <select class="form-select" v-model="filters.status">
                <option value="">All Statuses</option>
                <option v-for="status in filterOptions.statuses" :key="status" :value="status">{{ status }}</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">User</label>
              <select class="form-select" v-model="filters.createdBy">
                <option value="">All Users</option>
                <option v-for="user in users" :key="user.id" :value="user.name">{{ user.name }}</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Date Range</label>
              <flat-pickr v-model="dateRange" :config="flatpickrConfig" class="form-control"
                placeholder="Select date range..."></flat-pickr>
            </div>
            <div class="col-md-6 mb-2 d-flex align-items-end gap-2">
              <button @click="resetFilters" class="icon-btn btn-outline-secondary">
                <i class="ti ti-rotate-2 fs-5"></i>
                <span class="tooltip-text">Reset All Filters</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- The main content area -->
      <div>
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>

        <div v-else class="card border-0 shadow-sm">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 15px;"></th>
                  <th>
                    <div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Student</span></div>
                  </th>
                  <th>
                    <div class="d-flex align-items-center"><i class="ti ti-building me-1"></i><span>University</span>
                    </div>
                  </th>
                  <th>
                    <div class="d-flex align-items-center"><i class="ti ti-book me-1"></i><span>Course</span></div>
                  </th>
                  <th>
                    <div class="d-flex align-items-center"><i class="ti ti-calendar-time me-1"></i><span>Intake</span>
                    </div>
                  </th>
                  <th>
                    <div class="d-flex align-items-center"><i class="ti ti-status-change me-1"></i><span>Status</span>
                    </div>
                  </th>
                  <th>
                    <div class="d-flex align-items-center"><i class="ti ti-users me-1"></i><span>Partner</span></div>
                  </th>
                  <th>
                    <div class="d-flex align-items-center"><i class="ti ti-user-circle me-1"></i><span>User</span></div>
                  </th>
                  <th>
                    <div class="d-flex align-items-center"><i class="ti ti-calendar me-1"></i><span>Date Added</span>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="app in applications" :key="app.id" @click="viewRecord(app.id)" style="cursor:pointer;">
                  <td style="padding: 0 5px; vertical-align: middle; text-align: center;">
                    <div class="status-indicator" :class="getIndicatorClass(app.status)"></div>
                  </td>
                  <td>
                    <div class="user-info d-flex align-items-center" @click.stop>
                      <img :src="app.avatar ? `/storage/avatars/${app.avatar}` : '/assets/images/profile/user-1.jpg'"
                        alt="avatar" class="rounded-circle" width="35">
                      <div class="ms-3 flex-grow-1">
                        <router-link :to="{ name: 'RecordSection', params: { id: app.id } }"
                          class="text-dark text-decoration-none user-name-link">
                          <h6 class="user-name mb-0 fw-medium">{{ app.name || 'N/A' }}</h6>
                          <small class="text-muted">{{ app.email || '' }}</small>
                        </router-link>
                      </div>
                      <div class="dropdown dropstart user-hover-actions">
                        <a href="#" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="ti ti-dots-vertical fs-6"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li v-if="app.status !== 'Dropped'">
                            <a class="dropdown-item d-flex align-items-center gap-3 text-danger" href="#"
                              @click.prevent="withdrawApplication(app.id)">
                              <i class="fs-4 ti ti-trash"></i> Withdraw
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </td>
                  <td class="text-nowrap">{{ app.university || 'N/A' }}</td>
                  <td class="text-nowrap">{{ app.course || 'N/A' }}</td>
                  <td class="text-nowrap">{{ app.intake || 'N/A' }}</td>
                  <td><span class="badge" :class="getStatusClass(app.status)">{{ app.status || 'N/A' }}</span></td>
                  <td>{{ app.partnerDetails || 'N/A' }}</td>
                  <td>
                    <div class="text-nowrap">
                      <i class="ti ti-user-circle me-1"></i>
                      {{ app.created_by ? app.created_by.name : 'N/A' }}
                    </div>
                  </td>
                  <td class="text-nowrap"><i class="ti ti-clock me-1"></i> {{ formatDate(app.created_at) }}</td>
                </tr>
                <tr v-if="!loading && applications.length === 0">
                  <td colspan="9" class="text-center py-4">
                    <h3 class="h5">No applications found</h3>
                    <p class="text-muted">Try adjusting your search or filter criteria.</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="pagination.total > pagination.per_page" class="card-footer bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted">
                Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} entries
              </div>
              <nav>
                <ul class="pagination mb-0">
                  <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)"><i
                        class="ti ti-chevron-left"></i></a>
                  </li>
                  <li v-for="page in pages" :key="page.name" class="page-item"
                    :class="{ active: page.isActive, disabled: page.isDisabled }">
                    <a class="page-link" href="#" @click.prevent="changePage(page.name)">{{ page.name }}</a>
                  </li>
                  <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)"><i
                        class="ti ti-chevron-right"></i></a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Import Modal -->
    <div v-if="isImportModalOpen" class="modal d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
      <div class="modal-content">
        <span class="close" @click="isImportModalOpen = false">×</span>
        <h2 class="modal-title d-flex align-items-center">
          <i class="ti ti-file-import text-primary me-2"></i> Import File
        </h2>
        <form @submit.prevent="handleImport">
          <div class="mb-3">
            <label class="form-label fw-medium">Select File (.xlsx, .csv)</label>
            <input type="file" @change="onFileSelected" accept=".xlsx,.csv" class="form-control" required>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" @click="isImportModalOpen = false" class="btn btn-outline-secondary">
              <i class="ti ti-x me-1"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="isImporting">
              <i class="ti ti-upload me-1"></i> {{ isImporting ? 'Importing...' : 'Import' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- BLUR OVERLAY FOR SCREENSHOT PREVENTION -->
    <div ref="blurOverlay" class="blur-overlay"></div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, inject, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import { debounce } from 'lodash';
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import * as XLSX from 'xlsx';
import { useToast } from 'vue-toastification';

// --- INITIALIZATION ---
const toast = useToast();
const router = useRouter();
const applications = ref([]);
const users = ref([]);
const pagination = ref({});
const filterOptions = ref({ universities: [], courses: [], intakes: [], statuses: [] });
const loading = ref(true);
const isFilterPanelOpen = ref(false);
const dateRange = ref([]);
const isImportModalOpen = ref(false);
const isImporting = ref(false);
const importFile = ref(null);
const userRole = ref(null);
const blurOverlay = ref(null); // UPDATED: Renamed ref

// --- PERMISSIONS ---
const auth = inject('auth');

// 2. Create a computed property based on the INJECTED data.
// This is where canManageData belongs.
const canManageData = computed(() => {
    // Add a safety check in case auth or user hasn't loaded yet.
    if (!auth || !auth.value || !auth.value.user) {
        return false;
    }
    
    const allowedRoles = ['Administrator', 'Manager'];
    return allowedRoles.includes(auth.value.user.role);
});
// --- DATA & FILTERS ---
const filters = reactive({
  search: '',
  university: '',
  course: '',
  intake: '',
  status: '',
  createdBy: '',
  dateFrom: '',
  dateTo: '',
  page: 1,
  per_page: 10,
});

const flatpickrConfig = {
  mode: 'range',
  dateFormat: 'Y-m-d',
  onChange: (selectedDates) => {
    filters.dateFrom = selectedDates[0] ? new Date(selectedDates[0]).toISOString().split('T')[0] : '';
    filters.dateTo = selectedDates.length > 1 ? new Date(selectedDates[1]).toISOString().split('T')[0] : '';
  }
};

// --- STATE MANAGEMENT (Save/Load Filters) ---
const stateKey = 'applicationsTableState';

const saveState = () => {
  const state = {
    filters: { ...filters },
    isFilterPanelOpen: isFilterPanelOpen.value,
    dateRange: dateRange.value,
  };
  sessionStorage.setItem(stateKey, JSON.stringify(state));
};

const loadState = () => {
  const savedState = sessionStorage.getItem(stateKey);
  if (savedState) {
    try {
      const state = JSON.parse(savedState);
      Object.assign(filters, state.filters);
      isFilterPanelOpen.value = state.isFilterPanelOpen || false;
      if (state.dateRange) {
        dateRange.value = state.dateRange;
      }
    } catch (e) {
      console.error("Could not load saved state", e);
      sessionStorage.removeItem(stateKey);
    }
  }
};


// --- API & DATA FETCHING ---
const fetchCurrentUserRole = async () => {
  try {
    userRole.value = 'user'; // Replace with your real user role logic
  } catch (error) {
    console.error("Failed to fetch user role:", error);
    userRole.value = 'user';
  }
};

const fetchApplications = debounce(async () => {
  saveState();
  loading.value = true;
  try {
    const response = await axios.get('/api/applications', { params: filters });
    const { data, ...paginationData } = response.data.applications;
    applications.value = data;
    pagination.value = paginationData;
    users.value = response.data.users || [];
    filterOptions.value = response.data.filterOptions || {};
  } catch (error) {
    console.error("Failed to fetch applications:", error);
    Swal.fire('Error!', 'Could not fetch applications.', 'error');
  } finally {
    loading.value = false;
  }
}, 300);

// --- PAGINATION LOGIC ---
const pages = computed(() => {
  if (!pagination.value.to) return [];
  let from = pagination.value.current_page - 2;
  if (from < 1) from = 1;
  let to = from + 4;
  if (to > pagination.value.last_page) to = pagination.value.last_page;
  const pageNumbers = [];
  for (let page = from; page <= to; page++) {
    pageNumbers.push({ name: page, isActive: page === pagination.value.current_page, isDisabled: false });
  }
  if (from > 1) {
    pageNumbers.unshift({ name: '...', isActive: false, isDisabled: true });
    pageNumbers.unshift({ name: 1, isActive: false, isDisabled: false });
  }
  if (to < pagination.value.last_page) {
    pageNumbers.push({ name: '...', isActive: false, isDisabled: true });
    pageNumbers.push({ name: pagination.value.last_page, isActive: false, isDisabled: false });
  }
  return pageNumbers;
});

const changePage = (page) => {
  if (page === '...' || page < 1 || page > pagination.value.last_page) return;
  filters.page = page;
};

// --- ACTIONS & METHODS ---
const resetFilters = () => {
  Object.assign(filters, {
    search: '', university: '', course: '', intake: '', status: '',
    createdBy: '', dateFrom: '', dateTo: '', page: 1,
  });
  dateRange.value = [];
};

const viewRecord = (id) => { router.push({ name: 'RecordSection', params: { id } }); };
const withdrawApplication = (id) => { Swal.fire({ title: 'Are you sure?', text: "This will withdraw the application. This action cannot be undone.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, withdraw it!' }).then((result) => { if (result.isConfirmed) { axios.post(`/api/applications/${id}/withdraw`).then(() => { toast.success('The application has been withdrawn.'); fetchApplications.flush(); }).catch(error => { toast.error(error.response?.data?.message || 'Could not withdraw.'); }); } }); };
const onFileSelected = (event) => { importFile.value = event.target.files[0]; };
const handleImport = async () => { if (!importFile.value) { Swal.fire('No File', 'Please select a file to import.', 'warning'); return; } isImporting.value = true; const formData = new FormData(); formData.append('file', importFile.value); try { const response = await axios.post('/api/applications/import', formData); Swal.fire('Success!', response.data.message || 'Import successful!', 'success'); isImportModalOpen.value = false; await fetchApplications.flush(); } catch (error) { const errorMsg = error.response?.data?.error || 'Import failed'; Swal.fire('Import Failed', errorMsg, 'error'); } finally { isImporting.value = false; importFile.value = null; } };
const exportData = async () => { Swal.fire({ title: 'Preparing Export', text: 'Fetching all matching application data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } }); try { const { page, per_page, ...exportFilters } = filters; const response = await axios.get('/api/applications', { params: { ...exportFilters, per_page: -1 } }); const allApplications = response.data.applications; if (!allApplications || allApplications.length === 0) { Swal.fire('No Data', 'No applications match the current filters to export.', 'info'); return; } const dataToExport = allApplications.map(app => ({ "Student Name": app.name || '', "Email": app.email || '', "University": app.university || '', "Course": app.course || '', "Intake": app.intake || '', "Status": app.status || '', "Partner": app.partnerDetails || '', "User": app.created_by ? app.created_by.name : 'N/A', "Date Added": formatDate(app.created_at, true) || '' })); const ws = XLSX.utils.json_to_sheet(dataToExport); const wb = XLSX.utils.book_new(); XLSX.utils.book_append_sheet(wb, ws, "Applications"); XLSX.writeFile(wb, "applications_filtered_export.xlsx"); Swal.close(); } catch (error) { console.error('Export failed:', error); Swal.fire('Error!', 'Could not export application data.', 'error'); } };

// --- SCREENSHOT PREVENTION & SECURITY ---
const handleContextMenu = (e) => { if (!canManageData.value) e.preventDefault(); };
// UPDATED: Renamed functions for clarity
const activateBlur = () => { if (!canManageData.value && blurOverlay.value) blurOverlay.value.style.display = 'block'; };
const deactivateBlur = () => { if (blurOverlay.value) blurOverlay.value.style.display = 'none'; };
const handleKeyUp = (e) => { if (e.key === 'PrintScreen') { activateBlur(); setTimeout(deactivateBlur, 200); } }

// --- LIFECYCLE HOOKS ---
onMounted(async () => {
  await fetchCurrentUserRole();
  loadState();
  fetchApplications();

  watch(filters, (newFilters, oldFilters) => {
    const mainFilterChanged = newFilters.search !== oldFilters.search ||
      newFilters.university !== oldFilters.university ||
      newFilters.course !== oldFilters.course ||
      newFilters.intake !== oldFilters.intake ||
      newFilters.status !== oldFilters.status ||
      newFilters.createdBy !== oldFilters.createdBy ||
      newFilters.dateFrom !== oldFilters.dateFrom ||
      newFilters.dateTo !== oldFilters.dateTo;
    if (mainFilterChanged) {
      if (filters.page !== 1) {
        filters.page = 1;
      } else {
        fetchApplications();
      }
    } else {
      fetchApplications();
    }
  }, { deep: true });

  // UPDATED: Use renamed functions for event listeners
  window.addEventListener('contextmenu', handleContextMenu);
  window.addEventListener('blur', activateBlur);
  window.addEventListener('focus', deactivateBlur);
  document.addEventListener('keyup', handleKeyUp);
});

onUnmounted(() => {
  // UPDATED: Clean up listeners with correct function names
  window.removeEventListener('contextmenu', handleContextMenu);
  window.removeEventListener('blur', activateBlur);
  window.removeEventListener('focus', deactivateBlur);
  document.removeEventListener('keyup', handleKeyUp);
  fetchApplications.cancel();
});

// --- HELPER & FORMATTING FUNCTIONS ---
const formatDate = (dateString, forExport = false) => { if (!dateString) return 'N/A'; const date = new Date(dateString); if (forExport) { return date.toISOString().split('T')[0]; } return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }); };
const getStatusClass = (status) => { const classes = { 'Visa Granted': 'border border-success text-success', 'Dropped': 'border border-danger text-danger' }; return classes[status] || 'border border-primary text-primary'; };
const getIndicatorClass = (status) => { if (status === 'Dropped') return 'red'; if (status === 'Visa Granted') return 'green'; return 'bg-transparent'; };
</script>

<style scoped>
/* Main Layout */
.main-container {
  padding: 20px;
  background-color: #f8f9fa;
}

/* Buttons & Tooltips */
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

.icon-btn.btn-success:hover {
  background: #198754;
  border-color: #198754;
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

/* Table Styling */
.status-indicator {
  display: inline-block;
  width: 6px;
  height: 30px;
  border-radius: 3px;
  vertical-align: middle;
}

.red {
  background-color: #F56565;
}

.green {
  background-color: #2e7d32;
}

.table-hover tbody tr:hover {
  background-color: rgba(46, 125, 50, 0.05);
}

.badge {
  font-weight: 500;
  padding: 4px 8px;
  font-size: 12px;
}

.user-name-link:hover .user-name {
  color: #2e7d32;
}

.user-info {
  position: relative;
}

.user-hover-actions {
  opacity: 0;
  transition: opacity 0.2s ease-in-out;
}

tr:hover .user-hover-actions {
  opacity: 1;
}

.dropdown-menu {
  padding: 0 !important;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  display: none;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2s ease;
  z-index: 1010;
}

.user-hover-actions:hover .dropdown-menu {
  display: block;
  opacity: 1;
  visibility: visible;
}

.dropdown-item.text-danger:hover {
  background-color: #fbebeb !important;
  color: #dc3545 !important;
}

/* Filter Panel */
.filter-panel-transition {
  transition: all 0.3s ease;
}

/* Modal Styling */
.modal-content {
  background-color: #fff;
  margin: 10% auto;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  width: 90%;
  max-width: 500px;
  border: none;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2e7d32;
  margin-bottom: 20px;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
  transition: color 0.2s;
}

.close:hover {
  color: #666;
}

/* --- SECURITY STYLES --- */
/* For preventing text selection */
.secure-mode {
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* UPDATED: The blur overlay for screenshot prevention */
.blur-overlay {
  display: none;
  /* Hidden by default */
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  /* Apply a backdrop blur filter */
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px); /* For Safari support */
  /* A subtle background enhances the blur effect */
  background-color: rgba(255, 255, 255, 0.1);
  z-index: 9999;
  /* Must be on top of everything */
}
</style>