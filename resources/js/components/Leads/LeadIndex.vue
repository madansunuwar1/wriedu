<template>
    <div class="main-container">
        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="d-flex align-items-center">
                    <i class="ti ti-users text-2xl text-[#2e7d32] me-2"></i>
                    <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Lead Data</div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <div class="position-relative" style="width: 300px;">
                        <input type="text" class="form-control product-search ps-5" v-model="filters.search"
                            placeholder="Search leads...">
                        <i
                            class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                    </div>
                    <div class="d-flex gap-2">
                        <button @click="isFilterPanelVisible = !isFilterPanelVisible"
                            class="icon-btn btn-outline-secondary">
                            <i class="ti ti-filter fs-5"></i><span class="tooltip-text">Filters</span>
                        </button>
                        <router-link :to="{ name: 'leads.create' }" class="icon-btn btn-success">
                            <i class="ti ti-plus fs-5"></i><span class="tooltip-text">Add Lead</span>
                        </router-link>

                        <!-- This v-if now uses the injected auth data and works correctly -->
                        <button v-if="canManageData" @click="downloadData" class="icon-btn btn-outline-primary">
                            <i class="ti ti-file-export fs-5"></i><span class="tooltip-text">Export</span>
                        </button>
                        <button v-if="canManageData" @click="triggerImport" class="icon-btn btn-outline-info">
                            <i class="ti ti-file-import fs-5"></i><span class="tooltip-text">Import</span>
                        </button>
                    </div>
                </div>

                <div v-show="isFilterPanelVisible" class="mt-3 p-3 border rounded bg-light filter-panel-transition">
                    <div class="row">
                        <div class="col-md-3 mb-2"><label class="form-label fw-medium">Lead Type</label><select
                                class="form-select" v-model="filters.leadType">
                                <option value="">All Leads</option>
                                <option value="raw">Raw Leads Only</option>
                                <option value="hot">Hot Leads Only</option>
                            </select></div>
                        <div class="col-md-3 mb-2"><label class="form-label fw-medium">Location</label><select
                                class="form-select" v-model="filters.location">
                                <option value="">All Locations</option>
                                <option v-for="loc in uniqueProp('locations')" :key="loc" :value="loc">{{ loc }}
                                </option>
                            </select></div>
                        <div class="col-md-3 mb-2"><label class="form-label fw-medium">Last Qualification</label><select
                                class="form-select" v-model="filters.lastQualification">
                                <option value="">All</option>
                                <option v-for="qual in uniqueProp('lastqualification')" :key="qual" :value="qual">{{
                                    qual }}</option>
                            </select></div>
                        <div class="col-md-3 mb-2"><label class="form-label fw-medium">Country</label><select
                                class="form-select" v-model="filters.country">
                                <option value="">All</option>
                                <option v-for="c in uniqueProp('country')" :key="c" :value="c">{{ c }}</option>
                            </select></div>
                        <div class="col-md-3 mb-2"><label class="form-label fw-medium">User</label><select
                                class="form-select" v-model="filters.user">
                                <option value="">All Users</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }} {{
                                    user.last || '' }}</option>
                            </select></div>
                        <div class="col-md-3 mb-2"><label class="form-label fw-medium">Status</label><select
                                class="form-select" v-model="filters.status">
                                <option value="">All</option>
                                <option v-for="s in uniqueProp('status')" :key="s" :value="s">{{ s }}</option>
                            </select></div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">Date Range</label>
                            <flat-pickr v-model="dateRange" :config="flatpickrConfig" class="form-control"
                                placeholder="Select date range..."></flat-pickr>
                        </div>
                        <div class="col-md-3 mb-2 d-flex align-items-end gap-2"><button @click="resetFilters"
                                class="icon-btn btn-outline-secondary"><i class="ti ti-rotate-2 fs-5"></i><span
                                    class="tooltip-text">Reset</span></button></div>
                    </div>
                </div>
            </div>

            <input type="file" ref="fileInput" @change="handleFileImport" accept=".xlsx, .xls" style="display: none;" />

            <div v-if="isLoading" class="text-center py-5">
                <div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div v-else class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15px;"></th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Last Qualification</th>
                                <th>Status</th>
                                <th>User</th>
                                <th>Last Follow-up</th>
                                <th>Date Added</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lead in leads" :key="lead.id" @click="viewLead(lead.id)"
                                style="cursor: pointer;">
                                <td style="padding: 0 5px; vertical-align: middle; text-align: center;">
                                    <div v-if="lead.status == 'Dropped'" class="status-indicator red"></div>
                                    <div v-else-if="lead.status == 'Visa Granted'" class="status-indicator green"></div>
                                    <div v-else-if="lead.sources == '1'" class="status-indicator blue"></div>
                                </td>
                                <td>
                                    <div class="user-info d-flex align-items-center" @click.stop>
                                        <img :src="lead.avatar ? `/storage/avatars/${lead.avatar}` : '/assets/images/profile/user-1.jpg'"
                                            alt="avatar" class="rounded-circle" width="35">

                                        <div class="ms-3 flex-grow-1">
                                            <router-link :to="{ name: 'leads.record', params: { leadId: lead.id } }"
                                                class="text-dark text-decoration-none user-name-link">
                                                <h6 class="user-name mb-0 fw-medium">{{ lead.name || 'N/A' }}</h6>
                                                <small class="text-muted">{{ lead.email || '' }}</small>
                                            </router-link>
                                        </div>

                                        <div class="dropdown dropstart user-hover-actions">
                                            <a href="#" class="text-muted" aria-expanded="false">
                                                <i class="ti ti-dots-vertical fs-6"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li v-if="lead.status !== 'Dropped'">
                                                    <a class="dropdown-item d-flex align-items-center gap-3 text-danger"
                                                        href="#" @click.prevent="withdrawLead(lead.id)">
                                                        <i class="fs-4 ti ti-trash"></i> Withdraw
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ lead.phone || '-' }}</td>
                                <td>{{ lead.lastqualification || '-' }}</td>
                                <td><span class="badge" :class="getStatusBadgeClass(lead)">{{ getDisplayStatus(lead)
                                }}</span></td>
                                <td>{{ lead.creator ? `${lead.creator.name} ${lead.creator.last || ''}` : '-' }}</td>
                                <td>{{ lead.lead_comments.length ? formatRelativeTime(lead.lead_comments[0].created_at)
                                    : '-' }}</td>
                                <td>{{ formatDate(lead.created_at) }}</td>

                            </tr>
                            <tr v-if="leads.length === 0">
                                <td colspan="9" class="text-center py-4">No leads found matching your criteria.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="pagination.total > pagination.per_page" class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{
                            pagination.total || 0 }} entries</div>
                        <nav>
                            <ul class="pagination mb-0">
                                <li class="page-item" :class="{ disabled: !pagination.prev_page_url }"><a
                                        class="page-link" href="#"
                                        @click.prevent="changePage(pagination.current_page - 1)"><i
                                            class="ti ti-chevron-left"></i></a></li>
                                <li v-for="page in pages" :key="page.name" class="page-item"
                                    :class="{ active: page.isActive, disabled: page.isDisabled }"><a class="page-link"
                                        href="#" @click.prevent="changePage(page.name)">{{ page.name }}</a></li>
                                <li class="page-item" :class="{ disabled: !pagination.next_page_url }"><a
                                        class="page-link" href="#"
                                        @click.prevent="changePage(pagination.current_page + 1)"><i
                                            class="ti ti-chevron-right"></i></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
// --- MODIFIED: Import 'inject' to receive data from the parent layout ---
import { ref, reactive, onMounted, onUnmounted, watch, computed, nextTick, inject } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { debounce } from 'lodash';
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import { useReminders } from '../composables/useReminder';
import * as XLSX from 'xlsx';
import { useToast } from 'vue-toastification';

const toast = useToast();
const router = useRouter();

// --- MODIFIED: Inject the 'auth' object provided by the parent layout ---
const auth = inject('auth');

// --- REMOVED: The old props system is no longer needed ---
// const props = defineProps({ ... });

const leads = ref([]);
const users = ref([]);
const pagination = ref({});
const isLoading = ref(true);
const isFilterPanelVisible = ref(false);
const dateRange = ref([]);
const fileInput = ref(null);
const initialLoad = ref(true);
const watchersActive = ref(false);

const filterOptions = ref({
    locations: [],
    statuses: [],
    countries: [],
    lastQualifications: []
});

const filters = reactive({
    search: '', leadType: '', location: '', user: '', status: '',
    page: 1, per_page: 10,
    lastQualification: '', country: '',
    dateFrom: '', dateTo: '',
});

const stateKey = 'leadsTableState';

// --- MODIFIED: This computed property now uses the injected 'auth' object ---
// It exactly matches the working logic from your main layout file.
const canManageData = computed(() => {
    // First, check if 'auth' itself was injected successfully.
    // Then check if its .value and nested properties exist.
    if (!auth || !auth.value || !auth.value.user || !auth.value.user.role) {
        return false;
    }
    const managerRoles = ['Manager', 'Administrator', 'Admin', 'Leads Manager'];
    return managerRoles.includes(auth.value.user.role);
});

const flatpickrConfig = {
    mode: 'range',
    dateFormat: 'Y-m-d',
    onChange: (selectedDates) => {
        filters.dateFrom = selectedDates[0] ? new Date(selectedDates[0]).toISOString().split('T')[0] : '';
        filters.dateTo = selectedDates.length > 1 ? new Date(selectedDates[1]).toISOString().split('T')[0] : '';
    }
};

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

const uniqueProp = (propName) => {
    const optionMap = {
        'locations': 'locations',
        'status': 'statuses',
        'country': 'countries',
        'lastqualification': 'lastQualifications'
    };

    const optionKey = optionMap[propName];
    return optionKey ? filterOptions.value[optionKey] : [];
};

const fetchLeads = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/leads', { params: filters });
        leads.value = response.data.leads.data;
        users.value = response.data.users;

        if (response.data.filterOptions) {
            filterOptions.value = response.data.filterOptions;
        }

        const { data, ...paginationData } = response.data.leads;
        pagination.value = paginationData;

        saveState();

    } catch (error) {
        toast.error('Could not fetch leads.');
    } finally {
        isLoading.value = false;
    }
};

const withdrawLead = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will withdraw the lead. This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, withdraw it!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(`/api/leads/${id}/withdraw`)
                .then(() => {
                    toast.success('Lead has been withdrawn successfully.');
                    fetchLeads();
                })
                .catch(error => {
                    const errorMsg = error.response?.data?.message || 'Could not withdraw the lead.';
                    toast.error(errorMsg);
                });
        }
    });
};


const changePage = (page) => {
    if (page === '...' || page < 1 || page > pagination.value.last_page) return;
    filters.page = page;
};

const viewLead = (id) => {
    saveState();
    router.push({ name: 'leads.record', params: { leadId: id } });
};

const resetFilters = () => {
    watchersActive.value = false;

    Object.assign(filters, {
        search: '',
        leadType: '',
        location: '',
        user: '',
        status: '',
        page: 1,
        lastQualification: '',
        country: '',
        dateFrom: '',
        dateTo: '',
        per_page: 10
    });
    dateRange.value = [];
    sessionStorage.removeItem(stateKey);

    nextTick(() => {
        watchersActive.value = true;
        fetchLeads();
    });
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString() : '-';

const downloadData = async () => {
    Swal.fire({
        title: 'Preparing Download',
        text: 'Fetching all matching lead data...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const { page, per_page, ...exportFilters } = filters;
        const response = await axios.get('/api/leads/export', {
            params: exportFilters
        });

        const allLeads = response.data;

        if (allLeads.length === 0) {
            Swal.fire('No Data', 'There are no leads matching your current filters to export.', 'info');
            return;
        }

        const dataToExport = allLeads.map(lead => ({
            "ID": lead.id,
            "Name": lead.name || '-',
            "Email": lead.email || '-',
            "Phone": lead.phone || '-',
            "Status": getDisplayStatus(lead),
            "Assigned User": lead.creator ? `${lead.creator.name} ${lead.creator.last || ''}` : '-',
            "Source": lead.sources === '1' ? 'Raw Lead' : 'Hot Lead',
            "Date Added": formatDate(lead.created_at),
            "Interested Location": lead.locations || '-',
            "Country": lead.country || '-',
            "University": lead.location || '-',
            "Course": lead.course || '-',
            "Intake": lead.intake || '-',
            "Course Level": lead.courselevel || '-',
            "Last Qualification": lead.lastqualification || '-',
            "Year Passed": lead.passed || '-',
            "GPA / Percentage": lead.gpa || '-',
            "Academic Gaps": lead.academic || '-',
            "English Test": lead.englishTest || '-',
            "Overall Score": lead.score || '-',
            "English Score (Listening, Reading, etc.)": lead.englishscore || '-',
            "English Theory": lead.englishtheory || '-',
            "Forwarded Notes": lead.forwarded_notes || '-',
        }));

        const ws = XLSX.utils.json_to_sheet(dataToExport);

        ws['!cols'] = [
            { wch: 5 }, { wch: 25 }, { wch: 30 }, { wch: 15 }, { wch: 25 },
            { wch: 20 }, { wch: 12 }, { wch: 12 }, { wch: 20 }, { wch: 15 },
            { wch: 30 }, { wch: 30 }, { wch: 15 }, { wch: 15 }, { wch: 25 },
            { wch: 10 }, { wch: 15 }, { wch: 15 }, { wch: 15 }, { wch: 15 },
            { wch: 35 }, { wch: 15 }, { wch: 40 },
        ];

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Leads Export");
        XLSX.writeFile(wb, "full_leads_export.xlsx");

        Swal.close();

    } catch (error) {
        console.error('Error exporting data:', error);
        toast.error('Could not export the lead data.');
    }
};

const triggerImport = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

const handleFileImport = async (event) => {
    const file = event.target.files[0];
    if (!file) {
        return;
    }

    const formData = new FormData();
    formData.append('file', file);

    Swal.fire({
        title: 'Importing Data',
        text: 'Please wait while we process your file...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await axios.post('/api/leads/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        toast.success(response.data.message || 'Import Complete!');
        Swal.close();
        fetchLeads();

    } catch (error) {
        const errorMessage = error.response?.data?.message || 'An unknown error occurred.';
        Swal.fire({
            icon: 'error',
            title: 'Import Failed',
            text: errorMessage,
        });
    } finally {
        event.target.value = null;
    }
};

const getDisplayStatus = (lead) => {
    if (lead.status === 'Dropped') {
        return 'Dropped';
    }
    if (lead.application && lead.application.status) {
        return lead.application.status;
    }
    return lead.status || '-';
};

const getStatusBadgeClass = (lead) => {
    const displayStatus = getDisplayStatus(lead);
    switch (displayStatus) {
        case 'Dropped':
            return 'bg-danger bg-opacity-10 border border-danger text-danger';
        default:
            return 'bg-primary bg-opacity-10 border border-primary text-primary';
    }
};

const formatRelativeTime = (d) => {
    if (!d) return '-';
    const now = new Date();
    const past = new Date(d);
    const seconds = Math.floor((now - past) / 1000);

    let interval = seconds / 31536000;
    if (interval > 1) {
        const value = Math.floor(interval);
        return `${value} year${value > 1 ? 's' : ''} ago`;
    }
    interval = seconds / 2592000;
    if (interval > 1) {
        const value = Math.floor(interval);
        return `${value} month${value > 1 ? 's' : ''} ago`;
    }
    interval = seconds / 86400;
    if (interval > 1) {
        const value = Math.floor(interval);
        return `${value} day${value > 1 ? 's' : ''} ago`;
    }
    interval = seconds / 3600;
    if (interval > 1) {
        const value = Math.floor(interval);
        return `${value} hour${value > 1 ? 's' : ''} ago`;
    }
    interval = seconds / 60;
    if (interval > 1) {
        const value = Math.floor(interval);
        return `${value} minute${value > 1 ? 's' : ''} ago`;
    }
    if (seconds < 10) {
        return 'just now';
    }
    const value = Math.floor(seconds);
    return `${value} second${value > 1 ? 's' : ''} ago`;
};

const saveState = () => {
    const state = {
        filters: { ...filters },
        pagination: { ...pagination.value },
        isFilterPanelVisible: isFilterPanelVisible.value,
        dateRange: dateRange.value,
        timestamp: Date.now()
    };
    sessionStorage.setItem(stateKey, JSON.stringify(state));
};

const loadState = () => {
    const savedState = sessionStorage.getItem(stateKey);
    if (savedState) {
        try {
            const state = JSON.parse(savedState);

            const maxAge = 24 * 60 * 60 * 1000;
            if (state.timestamp && (Date.now() - state.timestamp) > maxAge) {
                sessionStorage.removeItem(stateKey);
                return false;
            }

            Object.assign(filters, state.filters);
            isFilterPanelVisible.value = state.isFilterPanelVisible || false;
            dateRange.value = state.dateRange || [];

            if (state.pagination) {
                pagination.value = { ...state.pagination };
            }

            return true;
        } catch (error) {
            console.error('Error loading saved state:', error);
            sessionStorage.removeItem(stateKey);
            return false;
        }
    }
    return false;
};

const canFilterByUser = computed(() => {
    if (!auth.value || !auth.value.user || !auth.value.user.role) return false;
    return ['Administrator', 'Leads Manager', 'Manager'].includes(auth.value.user.role);
});

// The useReminders composable is not used in this component, but keeping it
// in case it's added back for some other purpose.
const { start: startReminderChecker } = useReminders([]); // Empty array since auth is handled directly

onMounted(async () => {
    const hasState = loadState();
    await fetchLeads();
    startReminderChecker();
    if (window.Echo) {
      window.Echo.channel('leads').listen('.lead.updated', (data) => {
          const index = leads.value.findIndex(l => l.id === data.lead.id);
          if (index !== -1) leads.value[index] = data.lead;
      });
    }
    nextTick(() => {
        initialLoad.value = false;
        watchersActive.value = true;
    });
});

onUnmounted(() => {
    saveState();
    if (window.Echo) {
      window.Echo.leaveChannel('leads');
    }
});

const debouncedFetch = debounce(() => {
    if (watchersActive.value) {
        fetchLeads();
    }
}, 300);

watch(() => ({
    search: filters.search,
    leadType: filters.leadType,
    location: filters.location,
    user: filters.user,
    status: filters.status,
    lastQualification: filters.lastQualification,
    country: filters.country,
    dateFrom: filters.dateFrom,
    dateTo: filters.dateTo,
}), () => {
    if (!initialLoad.value && watchersActive.value) {
        filters.page = 1;
        debouncedFetch();
    }
}, { deep: true });

watch(() => filters.page, () => {
    if (!initialLoad.value && watchersActive.value) {
        fetchLeads();
    }
});

watch(isFilterPanelVisible, () => {
    if (!initialLoad.value) {
        saveState();
    }
});

if (typeof window !== 'undefined') {
    window.addEventListener('beforeunload', saveState);
}
</script>

<style scoped>
td {
    font-size: 14px !important;
}

.table th,
.table td {
    white-space: nowrap;
    text-overflow: ellipsis;
    vertical-align: middle;
}

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

.icon-btn.btn-primary:hover,
.icon-btn.btn-outline-primary:hover {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.icon-btn.btn-outline-info:hover {
    background: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}

.icon-btn.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
}

.icon-btn.btn-outline-danger:hover {
    background: #dc3545;
    border-color: #dc3545;
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

.blue {
    background-color: #4299E1;
}

.table-hover tbody tr:hover {
    background-color: rgba(46, 125, 50, 0.05);
}

.badge {
    font-weight: 500;
    padding: 4px 8px;
    font-size: 12px;
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

.user-hover-actions:hover>.dropdown-menu {
    display: block;
    margin-top: 0;
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

.dropdown-item.text-success:hover {
    background-color: #eaf6ea !important;
    color: #198754 !important;
}

.user-name-link:hover h6 {
    color: #2e7d32;
}
</style>