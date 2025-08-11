<template>
    <div class="container-fluid px-4 py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control border-0 bg-light" placeholder="Search users by name or email..." v-model="filters.search">
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                <div class="btn-group" role="group">
                                    <button class="btn" :class="filters.status === 'all' ? 'btn-primary active' : 'btn-outline-primary'" @click="setFilter('status', 'all')">
                                        <i class="fas fa-users me-1"></i>All Users
                                    </button>
                                    <button class="btn" :class="filters.status === 'active' ? 'btn-success active' : 'btn-outline-success'" @click="setFilter('status', 'active')">
                                        <i class="fas fa-circle me-1"></i>Online
                                    </button>
                                    <button class="btn" :class="filters.status === 'inactive' ? 'btn-danger active' : 'btn-outline-danger'" @click="setFilter('status', 'inactive')">
                                        <i class="fas fa-pause-circle me-1"></i>Offline
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading Users...</p>
        </div>

        <div v-else-if="error" class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-danger-subtle text-danger">
                    <div class="card-body text-center py-5">
                         <div class="empty-icon-circle bg-light rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center">
                            <i class="fas fa-exclamation-triangle fs-1"></i>
                        </div>
                        <h3 class="mb-3 fw-semibold">An Error Occurred</h3>
                        <p class="mb-4 fs-6">{{ error }}</p>
                        <button class="btn btn-danger" @click="fetchUsers()"><i class="fas fa-redo me-2"></i>Try Again</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="users.length > 0" class="row" id="usersGrid">
            <div v-for="user in users" :key="user.id" class="col-xl-4 col-lg-6 col-md-6 mb-4 user-card" :data-status="user.is_online ? 'active' : 'inactive'">
                <div class="card border-0 shadow-sm h-100 user-item" :class="user.is_online ? 'border-success' : 'border-danger'">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="position-relative me-3">
                                <img :src="user.avatar ? `/storage/avatars/${user.avatar}` : '/assets/images/profile/user-1.jpg'" alt="avatar" class="rounded-circle avatar-img" width="50" height="50">
                                <span class="position-absolute bottom-0 end-0 rounded-circle status-dot" :class="user.is_online ? 'online' : 'offline'"></span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center flex-wrap">
                                    <h5 class="card-title mb-0 text-dark fw-semibold me-2">{{ user.name }}</h5>
                                    <span class="badge rounded-pill px-2 py-1 status-badge" :class="user.is_online ? 'online' : 'offline'">
                                        <i class="fas fa-circle me-1 status-icon" style="font-size: 6px;"></i>
                                        <span class="status-text">{{ user.is_online ? 'Online' : 'Offline' }}</span>
                                    </span>
                                </div>
                                <p class="text-muted small mb-0"><i class="fas fa-envelope me-1"></i>{{ user.email || 'No email' }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Last seen: <span class="last-activity-text">{{ user.last_activity_human || 'Never' }}</span>
                            </small>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-4"><div class="text-center p-2 bg-light rounded-3"><div class="h6 mb-0 text-primary fw-bold">{{ user.activity_stats.created }}</div><small class="text-muted">Created</small></div></div>
                            <div class="col-4"><div class="text-center p-2 bg-light rounded-3"><div class="h6 mb-0 text-success fw-bold">{{ user.activity_stats.commented }}</div><small class="text-muted">Commented</small></div></div>
                            <div class="col-4"><div class="text-center p-2 bg-light rounded-3"><div class="h6 mb-0 text-info fw-bold">{{ user.activity_stats.status_updated }}</div><small class="text-muted">Status</small></div></div>
                        </div>

                        <div class="d-grid">
                            <router-link :to="{ name: 'activity.record', params: { userId: user.id } }" class="btn btn-primary btn-lg rounded-3">
                                <i class="fas fa-chart-bar me-2"></i>View Activity Report
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm"><div class="card-body text-center py-5"><div class="empty-icon-circle bg-light rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center"><i class="fas fa-user-slash text-muted fs-1"></i></div><h3 class="text-dark mb-3 fw-semibold">No Users Found</h3><p class="text-muted mb-4 fs-6">No users match your current search or filter criteria.</p></div></div>
            </div>
        </div>
        
        <div v-if="!loading && pagination.last_page > 1" class="row mt-4">
            <div class="col-12">
                 <div class="card border-0 shadow-sm"><div class="card-body py-3 d-flex justify-content-center"><nav aria-label="Page navigation"><ul class="pagination mb-0"><li class="page-item" :class="{ 'disabled': !pagination.prev_page_url }"><a class="page-link" href="#" @click.prevent="fetchUsers(pagination.current_page - 1)">«</a></li><li class="page-item" v-for="page in pagination.links.slice(1, -1)" :key="page.label" :class="{ 'active': page.active, 'disabled': !page.url }">

                    <!-- THIS IS THE CORRECTED LINE -->
                    <a class="page-link" href="#" @click.prevent="page.url && fetchUsers(page.label)">{{ page.label }}</a>
                    
                </li><li class="page-item" :class="{ 'disabled': !pagination.next_page_url }"><a class="page-link" href="#" @click.prevent="fetchUsers(pagination.current_page + 1)">»</a></li></ul></nav></div></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, watch } from 'vue';
import { debounce } from 'lodash';
import axios from 'axios';

const users = ref([]);
const pagination = ref({});
const loading = ref(true);
const error = ref(null);
const filters = reactive({
    search: '',
    status: 'all',
});
let statusInterval = null;

const fetchUsers = async (page = 1) => {
    loading.value = true;
    error.value = null;
    try {
        const params = {
            page,
            search: filters.search || undefined,
            status: filters.status !== 'all' ? filters.status : undefined,
        };
        const response = await axios.get('/api/users-activity', { params });
        users.value = response.data.data;
        pagination.value = response.data;
    } catch (err) {
        console.error("Failed to fetch users:", err);
        error.value = "Could not load user data. Please try again later.";
    } finally {
        loading.value = false;
    }
};

const updateOnlineStatus = async () => {
    if (users.value.length === 0) return;
    try {
        const response = await axios.get('/api/check-online-users');
        if (response.data.success) {
            const statuses = response.data.user_statuses;
            users.value.forEach(user => {
                if (statuses[user.id]) {
                    user.is_online = statuses[user.id].is_online;
                    user.last_activity_human = statuses[user.id].last_activity_human;
                }
            });
        }
    } catch (err) {
        console.error("Failed to update online status:", err);
    }
};

const setFilter = (key, value) => {
    filters[key] = value;
};

watch(() => filters.search, debounce(() => {
    fetchUsers(1);
}, 300));

watch(() => filters.status, () => {
    fetchUsers(1);
});

onMounted(() => {
    fetchUsers();
    statusInterval = setInterval(updateOnlineStatus, 30000);
});

onUnmounted(() => {
    if (statusInterval) clearInterval(statusInterval);
});
</script>

<style scoped>
.user-item { animation: fadeInUp 0.5s ease forwards; opacity: 0; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.status-dot { width: 12px; height: 12px; border: 2px solid white; transition: background-color 0.3s ease; }
.status-dot.online { background-color: #28a745 !important; box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.3); animation: pulse-green 2s infinite; }
.status-dot.offline { background-color: #6c757d !important; }
@keyframes pulse-green { 0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); } 100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); } }
.status-badge { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.status-badge.online { background-color: rgba(25, 135, 84, 0.15) !important; color: #198754 !important; border: 1px solid rgba(25, 135, 84, 0.3); }
.status-badge.offline { background-color: rgba(108, 117, 125, 0.15) !important; color: #6c757d !important; border: 1px solid rgba(108, 117, 125, 0.3); }
.card { border-radius: 12px; transition: all 0.3s ease; border-left: 4px solid transparent; }
.card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
.card.border-success { border-left-color: #28a745; }
.card.border-danger { border-left-color: #dc3545; }
.avatar-img { width: 50px; height: 50px; object-fit: cover; }
.btn { transition: all .2s ease-in-out; }
.btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.empty-icon-circle { width: 100px; height: 100px; }
.pagination .page-link { border-radius: 50% !important; margin: 0 4px; border: none; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; font-weight: 500;}
.pagination .page-item.active .page-link { transform: scale(1.1); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
</style>