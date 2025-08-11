<template>
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
                <div class="me-3 bg-primary bg-opacity-10 p-3 rounded">
                    <i class="fas fa-history fs-4 text-primary"></i>
                </div>
                <div>
                    <h1 class="h4 mb-0">
                        {{ userName ? `${userName}'s Activity Log` : 'All Activity Logs' }}
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><router-link to="/dashboard">Dashboard</router-link></li>
                            <li class="breadcrumb-item"><router-link :to="{ name: 'activity.index' }">User
                                    Activity</router-link></li>
                            <li class="breadcrumb-item active" aria-current="page">Logs</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form @submit.prevent="applyFilters">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <select class="form-select" v-model="filters.description">
                                <option value="">All Actions</option>
                                <option v-for="desc in descriptions" :key="desc" :value="desc">{{ desc }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" v-model="filters.period">
                                <option value="">Custom Date</option>
                                <option value="day">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="date" class="form-control" v-model="filters.date_from"
                                    :disabled="filters.period !== ''">
                                <span class="input-group-text">to</span>
                                <input type="date" class="form-control" v-model="filters.date_to"
                                    :disabled="filters.period !== ''">
                            </div>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search me-1"></i> Apply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stat Cards -->
        <div v-if="!loadingStats" class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Total Activities</h6>
                                <h3 class="mb-0">{{ stats.total || 0 }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded"><i
                                    class="fas fa-chart-line text-primary fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- More stat cards -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Created</h6>
                                <h3 class="mb-0">{{ stats.created || 0 }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded"><i
                                    class="fas fa-plus-circle text-success fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Status Updated</h6>
                                <h3 class="mb-0">{{ stats.status_updated || 0 }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded"><i
                                    class="fas fa-edit text-warning fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Commented</h6>
                                <h3 class="mb-0">{{ stats.commented || 0 }}</h3>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded"><i
                                    class="fas fa-comments text-info fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Activity Logs</h5>
                <div v-if="!loadingLogs && pagination.total > 0" class="text-muted small">
                    Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} records
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Loading State -->
                <div v-if="loadingLogs" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading Logs...</p>
                </div>
                <!-- No Results -->
                <div v-else-if="logs.length === 0" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-4"></i>
                    <h5 class="mb-2">No Activity Logs Found</h5>
                    <p class="text-muted">There are no logs matching your current filters.</p>
                </div>
                <!-- Table -->
                <div v-else class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">User</th>
                                <th>Action</th>
                                <th>Subject</th>
                                <th>Date & Time</th>
                                <th class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="log in logs" :key="log.id">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center" v-if="log.causer">
                                        <div class="avatar-sm me-3">
                                            <div
                                                class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ log.causer.name }}</h6><small class="text-muted">{{
                                                log.causer.email }}</small>
                                        </div>
                                    </div>
                                    <div v-else>System</div>
                                </td>
                                <td><span class="badge rounded-pill" :class="getActionClass(log.description)"><i
                                            class="fas me-1" :class="getIconClass(log.description)"></i> {{
                                                log.description }}</span></td>
                                <td><span class="text-muted">{{ log.subject_type.split('\\').pop() }} #{{ log.subject_id
                                }}</span></td>
                                <td>
                                    <div>
                                        <small class="fw-medium">{{ new Date(log.created_at).toLocaleDateString()
                                        }}</small><br>
                                        <small class="text-muted">{{ new Date(log.created_at).toLocaleTimeString()
                                        }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button v-if="log.properties && Object.keys(log.properties).length > 0"
                                        class="btn btn-sm btn-outline-primary rounded-pill" @click="showDetails(log)">
                                        <i class="fas fa-eye me-1"></i> View
                                    </button>
                                    <span v-else class="badge bg-light text-muted">No details</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination Footer -->
            <div v-if="!loadingLogs && pagination.last_page > 1" class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-end">
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <!-- Previous Page Link -->
                            <li class="page-item" :class="{ 'disabled': !pagination.prev_page_url }">
                                <a class="page-link" href="#"
                                    @click.prevent="fetchLogs(pagination.current_page - 1)">«</a>
                            </li>

                            <!-- Pagination Links -->
                            <li class="page-item" v-for="page in pagination.links.slice(1, -1)" :key="page.label"
                                :class="{ 'active': page.active, 'disabled': !page.url }">
                                <a class="page-link" href="#" @click.prevent="page.url && fetchLogs(page.label)">{{
                                    page.label }}</a>
                            </li>

                            <!-- Next Page Link -->
                            <li class="page-item" :class="{ 'disabled': !pagination.next_page_url }">
                                <a class="page-link" href="#"
                                    @click.prevent="fetchLogs(pagination.current_page + 1)">»</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <ActivityLogModal :log="selectedLog" @close="selectedLog = null" />

</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import axios from 'axios';
import ActivityLogModal from './ActivityLogModal.vue';

const props = defineProps({
    userId: {
        type: String,
        default: ''
    }
});

// State (this is all correct)
const logs = ref([]);
const stats = ref({});
const descriptions = ref([]);
const pagination = ref({});
const loadingLogs = ref(true);
const loadingStats = ref(true);
const selectedLog = ref(null);
const userName = ref('');

const filters = reactive({
    description: '',
    period: '',
    date_from: '',
    date_to: '',
    user_id: props.userId // This will now be correctly updated by the watcher
});

// Fetching Data (this is all correct)
const fetchData = async (page = 1) => {
    // We now fetch both concurrently for speed
    loadingLogs.value = true;
    loadingStats.value = true;

    // Using Promise.all to run requests in parallel
    await Promise.all([
        fetchLogs(page),
        fetchStats()
    ]);
};

const fetchLogs = async (page = 1) => {
    loadingLogs.value = true;
    try {
        // Ensure user_id is passed from the reactive filters object
        const response = await axios.get('/api/logs', { params: { ...filters, page } });
        logs.value = response.data.data;
        pagination.value = response.data;
        if (props.userId && logs.value.length > 0 && logs.value[0]?.causer) {
            userName.value = logs.value[0].causer.name;
        } else if (!props.userId) {
            userName.value = '';
        }
    } catch (error) {
        console.error("Failed to fetch logs:", error);
    } finally {
        loadingLogs.value = false;
    }
};

const fetchStats = async () => {
    loadingStats.value = true;
    try {
        // Ensure user_id is passed from the reactive filters object
        const response = await axios.get('/api/stats', { params: filters });
        stats.value = response.data;
    } catch (error) {
        console.error("Failed to fetch stats:", error);
    } finally {
        loadingStats.value = false;
    }
};

const fetchDescriptions = async () => {
    try {
        const response = await axios.get('/api/descriptions');
        descriptions.value = response.data;
    } catch (error) {
        console.error("Failed to fetch descriptions:", error);
    }
};

// Methods (this is all correct)
const showDetails = (log) => {
    selectedLog.value = log;
};

const applyFilters = () => {
    if (filters.period) {
        filters.date_from = '';
        filters.date_to = '';
    }
    fetchData(); // Fetch page 1 when filters change
};

// getActionClass and getIconClass methods are correct...
const getActionClass = (desc) => {
    const d = desc.toLowerCase();
    if (d.includes('created')) return 'bg-success-subtle text-success';
    if (d.includes('status')) return 'bg-warning-subtle text-warning';
    if (d.includes('deleted')) return 'bg-danger-subtle text-danger';
    if (d.includes('commented')) return 'bg-info-subtle text-info';
    return 'bg-primary-subtle text-primary';
};

const getIconClass = (desc) => {
    const d = desc.toLowerCase();
    if (d.includes('created')) return 'fa-plus-circle';
    if (d.includes('status')) return 'fa-edit';
    if (d.includes('deleted')) return 'fa-trash-alt';
    if (d.includes('commented')) return 'fa-comments';
    return 'fa-cog';
};


// --- LIFECYCLE AND WATCHERS (THE FIX IS HERE) ---

onMounted(() => {
    // fetchData(); // <-- STEP 1: REMOVE THIS LINE
    fetchDescriptions(); // We can still fetch descriptions, as they are not user-specific
});

// STEP 2: MODIFY THE WATCHER
watch(() => props.userId, (newVal) => {
    // This watcher will now run immediately on component load
    // and whenever the userId prop changes.
    if (newVal) { // Only proceed if we have a valid user ID
        filters.user_id = newVal;
        fetchData(); // This is now the primary trigger for loading data
    } else {
        // Handle case where we navigate to a generic log page without a user ID
        filters.user_id = '';
        fetchData();
    }
}, { immediate: true }); // <-- This makes the watcher run on component creation

</script>

<style scoped>
/* Copy styles from record.blade.php here */
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>