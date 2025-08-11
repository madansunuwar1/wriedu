<script setup>
import { ref, reactive, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { debounce } from 'lodash';
import { useToast } from 'vue-toastification'; // 1. NEW: Import useToast

const toast = useToast(); // 2. NEW: Instantiate the toast service

// State Management
const rawLeads = ref({ data: [], links: [], total: 0, from: 0, to: 0 });
const isLoading = ref(true);
const filtersVisible = ref(false);
const selectedLeads = ref([]);

const filterOptions = reactive({
  users: {},
  statuses: [],
  countries: [],
  applyingFor: [],
});

const filters = reactive({
  search: '',
  status: '',
  assignee_id: '',
  country: '',
  applying_for: '',
  date_from: '',
  date_to: '',
  per_page: 20,
  page: 1,
});

// Computed Properties
const paginationInfo = computed(() => {
  if (!rawLeads.value.total) return 'No entries found';
  return `Showing ${rawLeads.value.from || 0} to ${rawLeads.value.to || 0} of ${rawLeads.value.total} entries`;
});

const isAllSelected = computed({
  get: () => {
    const pageLeadIds = rawLeads.value.data.map(lead => lead.id);
    return pageLeadIds.length > 0 && pageLeadIds.every(id => selectedLeads.value.includes(id));
  },
  set: (value) => {
    const pageLeadIds = rawLeads.value.data.map(lead => lead.id);
    if (value) {
      selectedLeads.value = [...new Set([...selectedLeads.value, ...pageLeadIds])];
    } else {
      selectedLeads.value = selectedLeads.value.filter(id => !pageLeadIds.includes(id));
    }
  }
});

// Methods
const fetchRawLeads = async (page = 1) => {
  isLoading.value = true;
  filters.page = page;

  try {
    const response = await axios.get('/api/raw-leads', { params: filters });
    rawLeads.value = response.data;
  } catch (error) {
    console.error("Error fetching raw leads:", error);
    toast.error('Could not fetch raw leads.'); // Using toast
  } finally {
    isLoading.value = false;
  }
};

const fetchFilterData = async () => {
  try {
    const response = await axios.get('/api/raw-leads/filter-options');
    const data = response.data;
    filterOptions.users = data.users;
    filterOptions.statuses = data.statuses;
    filterOptions.countries = data.countries;
    filterOptions.applyingFor = data.applyingFor;
  } catch (error) {
    console.error("Error fetching filter options:", error);
    toast.error('Could not load filter options.'); // Using toast
  }
};

const getStatusClass = (status) => {
  return status ? 'status-' + status.toLowerCase().replace(/ /g, '-') : 'status-default';
};

const goToPage = (link) => {
  if (!link.url || link.active) return;
  const pageNumber = new URL(link.url).searchParams.get('page');
  fetchRawLeads(pageNumber);
};

const toggleFilters = () => filtersVisible.value = !filtersVisible.value;

const resetFilters = () => {
  Object.keys(filters).forEach(key => {
    if (key !== 'per_page' && key !== 'page') filters[key] = '';
  });
  // The watcher will automatically trigger fetchRawLeads
};

const confirmDelete = (leadId, leadName) => {
  Swal.fire({
    title: 'Are you sure?',
    text: `You are about to delete "${leadName}". This action cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await axios.delete(`/api/raw-leads/${leadId}`);
        toast.success(`Lead "${leadName}" has been deleted.`); // Using toast
        selectedLeads.value = selectedLeads.value.filter(id => id !== leadId);
        fetchRawLeads(filters.page);
      } catch (error) {
        toast.error('Failed to delete the lead.'); // Using toast
      }
    }
  });
};

const handleBulkDelete = () => {
  if (selectedLeads.value.length === 0) {
    return toast.warning('Please select at least one lead.'); // Using toast
  }
  Swal.fire({
    title: 'Are you sure?',
    text: `You are about to delete ${selectedLeads.value.length} lead(s).`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete them!',
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await axios.post('/api/raw-leads/bulk-destroy', { raw_lead_ids: selectedLeads.value });
        toast.success(`${selectedLeads.value.length} leads have been deleted.`); // Using toast
        selectedLeads.value = [];
        fetchRawLeads(1);
      } catch (error) {
        toast.error('Failed to delete selected leads.'); // Using toast
      }
    }
  });
};

const assigneeIdForModal = ref('');
const assignmentCommentForModal = ref('');

const handleBulkAssign = async () => {
  if (!assigneeIdForModal.value) {
    return toast.warning('Please select a user to assign.'); // Using toast
  }

  const modalElement = document.getElementById('assignUserModal');
  const modal = bootstrap.Modal.getInstance(modalElement);
  if (modal) {
    modal.hide();
  }

  try {
    await axios.post('/api/raw-leads/bulk-assign', {
      raw_lead_ids: selectedLeads.value,
      user_id: assigneeIdForModal.value,
      assignment_comment: assignmentCommentForModal.value
    });
    toast.success(`${selectedLeads.value.length} leads have been assigned and converted.`); // Using toast
    selectedLeads.value = [];
    assigneeIdForModal.value = '';
    assignmentCommentForModal.value = '';
    fetchRawLeads(1);
  } catch (error) {
    toast.error('Failed to assign selected leads.'); // Using toast
  }
};

const listenForNewLeads = () => {
  if (window.Echo) {
    window.Echo.channel('raw-leads')
      .listen('.raw-lead.created', (e) => {
        console.log('Real-time event received for raw-lead.created:', e);
        rawLeads.value.data.unshift(e.rawLead);
        rawLeads.value.total++;
        rawLeads.value.to++;
        // Using toast for real-time notification
        toast.info(`New lead received: ${e.rawLead.name}`);
      });
    console.log("Successfully listening for 'raw-lead.created' event.");
  } else {
    console.error("Laravel Echo is not defined.");
  }
};

// Lifecycle Hooks & Watchers
onMounted(() => {
  fetchFilterData();
  fetchRawLeads();
  listenForNewLeads();

  flatpickr("#filterDateFrom", {
    dateFormat: "Y-m-d",
    onChange: (selectedDates, dateStr) => { filters.date_from = dateStr; }
  });
  flatpickr("#filterDateTo", {
    dateFormat: "Y-m-d",
    onChange: (selectedDates, dateStr) => { filters.date_to = dateStr; }
  });
});

watch(filters, debounce(() => {
  fetchRawLeads(1);
}, 300), { deep: true });
</script>

<template>
  <div class="main-container">
    <div class="widget-content searchable-container list">
      <div class="card card-body">
        <!-- Header -->
        <div class="row">
          <div class="col-md-12 col-xl-12">
            <div class="d-flex align-items-center">
              <i class="ti ti-users text-2xl text-[#2e7d32] me-2"></i>
              <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Raw Lead Data</div>
            </div>
          </div>
        </div>
        <!-- Controls -->
        <div class="d-flex justify-content-between mt-3">
          <div class="d-flex align-items-center gap-2">
            <div class="position-relative" style="width: 300px;">
              <input type="text" class="form-control product-search ps-5" v-model="filters.search"
                placeholder="Search leads...">
              <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
            </div>
            <div class="dropdown">
              <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false" :disabled="selectedLeads.length === 0">
                Bulk Actions ({{ selectedLeads.length }})
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#assignUserModal"><i
                      class="ti ti-user-plus me-2"></i>Assign Selected</a></li>
                <li><a class="dropdown-item text-danger" href="#" @click.prevent="handleBulkDelete"><i
                      class="ti ti-trash me-2"></i>Delete Selected</a></li>
              </ul>
            </div>
          </div>
          <div class="d-flex gap-2">
            <button @click="toggleFilters" class="icon-btn btn-outline-secondary">
              <i class="ti ti-filter fs-5"></i><span class="tooltip-text">Filters</span>
            </button>
            <a href="/app/rawlead/import" class="icon-btn btn-outline-primary">
              <i class="ti ti-upload fs-5"></i><span class="tooltip-text">Import</span>
            </a>
            <a href="/raw-leads/export-all" class="icon-btn btn-success">
              <i class="ti ti-file-export fs-5"></i><span class="tooltip-text">Export All</span>
            </a>
          </div>
        </div>
        <!-- Filter Panel -->
        <div v-if="filtersVisible" class="mt-3 p-3 border rounded bg-light filter-panel-transition"
          style="background-color: rgb(248 252 255) !important">
          <div class="row">
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Status</label>
              <select class="form-select" v-model="filters.status">
                <option value="">All Statuses</option>
                <option v-for="status in filterOptions.statuses" :key="status" :value="status">{{
                  status.charAt(0).toUpperCase() + status.slice(1) }}</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Assigned To</label>
              <select class="form-select" v-model="filters.assignee_id">
                <option value="">Anyone</option>
                <option value="unassigned">Unassigned</option>
                <option v-for="(name, id) in filterOptions.users" :key="id" :value="id">{{ name }}</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Preferred Country</label>
              <select class="form-select" v-model="filters.country">
                <option value="">All Countries</option>
                <option v-for="country in filterOptions.countries" :key="country" :value="country">{{ country }}
                </option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Applying For</label>
              <select class="form-select" v-model="filters.applying_for">
                <option value="">All Types</option>
                <option v-for="type in filterOptions.applyingFor" :key="type" :value="type">{{
                  type.charAt(0).toUpperCase() + type.slice(1) }}</option>
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
              <button @click="resetFilters" class="icon-btn btn-outline-secondary">
                <i class="ti ti-rotate-2 fs-5"></i><span class="tooltip-text">Reset</span>
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Table -->
      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 30px;"><input type="checkbox" class="form-check-input" v-model="isAllSelected"></th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Assigned To</th>
                <th>Country</th>
                <th>Applying For</th>
                <th>Date Added</th>
                <th style="width: 50px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td colspan="10" class="text-center py-4">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="rawLeads.data.length === 0">
                <td colspan="10" class="text-center py-4">No raw leads found matching your criteria.</td>
              </tr>
              <tr v-for="lead in rawLeads.data" :key="lead.id" v-else>
                <td><input type="checkbox" class="form-check-input" :value="lead.id" v-model="selectedLeads"></td>
                <td>{{ lead.name || '-' }}</td>
                <td>{{ lead.email || '-' }}</td>
                <td>{{ lead.phone || '-' }}</td>
                <td><span class="badge" :class="getStatusClass(lead.status)">{{ lead.status || 'Unknown' }}</span></td>
                <td>{{ lead.assignee ? lead.assignee.name : 'Unassigned' }}</td>
                <td>{{ lead.preferred_country || '-' }}</td>
                <td>{{ lead.applying_for || '-' }}</td>
                <td>{{ new Date(lead.created_at).toLocaleDateString() }}</td>
                <td>
                  <div class="dropdown dropstart">
                    <a href="#" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false"><i
                        class="ti ti-dots-vertical fs-6"></i></a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" :href="`/raw-leads/${lead.id}/edit`"><i
                            class="fs-4 ti ti-edit me-2"></i> Edit</a></li>
                      <li><a class="dropdown-item text-danger" href="#"
                          @click.prevent="confirmDelete(lead.id, lead.name)"><i class="fs-4 ti ti-trash me-2"></i>
                          Delete</a></li>
                    </ul>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <div class="card-footer bg-transparent" v-if="!isLoading && rawLeads.total > 0">
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted" id="paginationInfo">{{ paginationInfo }}</div>
            <nav aria-label="Page navigation">
              <ul class="pagination mb-0">
                <li v-for="link in rawLeads.links" :key="link.label" class="page-item"
                  :class="{ 'active': link.active, 'disabled': !link.url }">
                  <a href="#" class="page-link" @click.prevent="goToPage(link)" v-html="link.label"></a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Assign Modal -->
    <div class="modal fade" id="assignUserModal" tabindex="-1" aria-labelledby="assignUserModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="assignUserModalLabel">Assign Selected Leads</h5><button type="button"
              class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="assigneeSelect" class="form-label">Select User to Assign</label>
              <select class="form-select" id="assigneeSelect" v-model="assigneeIdForModal">
                <option value="" selected disabled>-- Select User --</option>
                <option v-for="(name, id) in filterOptions.users" :key="id" :value="id">{{ name }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="assignmentComment" class="form-label">Optional Comment</label>
              <textarea class="form-control" id="assignmentComment" rows="3" placeholder="Add a comment..."
                v-model="assignmentCommentForModal"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="handleBulkAssign"
              :disabled="selectedLeads.length === 0">Assign Leads</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* Paste all the CSS from your original Blade file here */

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

.icon-btn:hover .tooltip-text {
  opacity: 1;
  visibility: visible;
}

.table-hover tbody tr:hover {
  background-color: rgba(46, 125, 50, 0.05);
}

.badge {
  font-weight: 500;
  padding: 4px 8px;
  font-size: 12px;
}

.filter-panel-transition {
  transition: all 0.3s ease;
}

.dropdown-menu {
  z-index: 1010;
  border-radius: 8px;
}

.dropdown-item.text-danger:hover {
  background-color: #fbebeb !important;
  color: #dc3545 !important;
}

/* Status Colors */
.status-new {
  background-color: #cfe2ff;
  color: #0a58ca;
}

.status-contacted {
  background-color: #f8d7da;
  color: #b02a37;
}

.status-in-progress {
  background-color: #fff3cd;
  color: #997404;
}

.status-qualified,
.status-converted {
  background-color: #d1e7dd;
  color: #146c43;
}

.status-rejected {
  background-color: #e2e3e5;
  color: #565e64;
}

.status-on-hold {
  background-color: #e9d5ff;
  color: #6f42c1;
}

.status-dropped {
  background-color: #f8d7da;
  color: #b02a37;
  text-decoration: line-through;
}

.status-default {
  background-color: #e9ecef;
  color: #495057;
}
</style>