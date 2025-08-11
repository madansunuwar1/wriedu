<template>
    <!-- Loading Spinner -->
    <div v-if="isLoading" class="d-flex justify-content-center my-5">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Main Content -->
    <div v-else class="main-container">
        <div class="widget-content searchable-container list">
            <!-- Search and Action Header -->
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-12 col-xl-12">
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">User Management</div>
                    </div>
                    <div class="col-md-6 col-xl-8">
                        <div class="position-relative">
                            <input type="text" class="form-control product-search ps-5" v-model="searchQuery"
                                placeholder="Search users...">
                            <i
                                class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </div>
                    </div>
                    <div
                        class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <button id="btn-add-user" @click="showAddUserOffcanvas"
                            class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-user-plus text-white me-1 fs-5"></i> Add User
                        </button>
                    </div>
                    <div
                        class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <button id="btn-export-users" @click="exportUsers"
                            class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-download text-white me-1 fs-5"></i> Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- User Data Table -->
            <div class="table-responsive mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle" id="dataTable">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">ID</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">User</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Name</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Email</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Assigned Applications/Leads</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Role</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Status</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Actions</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in filteredUsers" :key="user.id">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <router-link :to="{ name: 'user.profile', params: { id: user.id } }">
                                    <img :src="user.avatar" alt="user" width="45" height="45"
                                        class="img-fluid rounded-circle">
                                </router-link>
                            </td>
                            <td>
                                <router-link :to="{ name: 'user.profile', params: { id: user.id } }"
                                    class="text-dark text-decoration-none user-profile-link">
                                    {{ user.name }} {{ user.last }}
                                </router-link>
                            </td>
                            <td>{{ user.email }}</td>
                            <td>
                                <div class="application-dropdown" :data-user-id="user.id">
                                    <button class="btn dropdown-toggle w-auto text-start" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false" @click.stop>
                                        <span class="selected-text">{{ getAssignmentText(user) }}</span>
                                    </button>
                                    <div class="dropdown-menu p-2 w-auto" style="min-width: 250px;" @click.stop>
                                        <!-- Applications Section -->
                                        <div class="mb-3">
                                            <h6 class="dropdown-header clickable-header"
                                                @click="toggleSection(user.id, 'app')">
                                                <i
                                                    :class="['me-1', activeSections[user.id]?.app ? 'ti ti-chevron-down' : 'ti ti-chevron-right']"></i>
                                                Applications
                                            </h6>
                                            <div v-show="activeSections[user.id]?.app" class="dropdown-section">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input app-checkbox" type="checkbox"
                                                        :id="`app-none-${user.id}`"
                                                        :checked="!user.application || user.application.length === 0"
                                                        @change="handleNoneSelection(user, 'application')">
                                                    <label class="form-check-label w-100"
                                                        :for="`app-none-${user.id}`">No Applications</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2"
                                                    @click="selectAll(user, 'application')">Select All
                                                    Applications</button>
                                                <div style="max-height: 200px; overflow-y: auto;">
                                                    <div v-for="assignUser in appAssignableUsers(user.id)"
                                                        :key="assignUser.id" class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox"
                                                            :value="assignUser.id"
                                                            :id="`app-${user.id}-${assignUser.id}`"
                                                            v-model="user.application"
                                                            @change="updateAssignment(user, 'application')">
                                                        <label class="form-check-label w-100"
                                                            :for="`app-${user.id}-${assignUser.id}`">
                                                            {{ assignUser.name }} {{ assignUser.last }} <span
                                                                class="text-muted">({{ assignUser.role }})</span>
                                                        </label>
                                                    </div>
                                                    <div v-if="appAssignableUsers(user.id).length === 0"
                                                        class="text-muted text-center py-2">No other users available
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Leads Section -->
                                        <div class="mb-2">
                                            <h6 class="dropdown-header clickable-header"
                                                @click="toggleSection(user.id, 'lead')">
                                                <i
                                                    :class="['me-1', activeSections[user.id]?.lead ? 'ti ti-chevron-down' : 'ti ti-chevron-right']"></i>
                                                Leads
                                            </h6>
                                            <div v-show="activeSections[user.id]?.lead" class="dropdown-section">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input lead-checkbox" type="checkbox"
                                                        :id="`lead-none-${user.id}`"
                                                        :checked="!user.lead || user.lead.length === 0"
                                                        @change="handleNoneSelection(user, 'lead')">
                                                    <label class="form-check-label w-100"
                                                        :for="`lead-none-${user.id}`">No Leads</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2"
                                                    @click="selectAll(user, 'lead')">Select All Leads</button>
                                                <div style="max-height: 200px; overflow-y: auto;">
                                                    <div v-for="assignUser in leadAssignableUsers(user.id)"
                                                        :key="assignUser.id" class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox"
                                                            :value="assignUser.id"
                                                            :id="`lead-${user.id}-${assignUser.id}`" v-model="user.lead"
                                                            @change="updateAssignment(user, 'lead')">
                                                        <label class="form-check-label w-100"
                                                            :for="`lead-${user.id}-${assignUser.id}`">
                                                            {{ assignUser.name }} {{ assignUser.last }} <span
                                                                class="text-muted">({{ assignUser.role }})</span>
                                                        </label>
                                                    </div>
                                                    <div v-if="leadAssignableUsers(user.id).length === 0"
                                                        class="text-muted text-center py-2">No other users available
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="btn dropdown-toggle w-auto text-start" style="border: none;"
                                    v-model="user.role" @change="updateRole(user)">
                                    <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}
                                    </option>
                                </select>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded-3 fw-semibold"
                                        :class="user.deleted_at ? 'bg-light-danger' : 'bg-light-success'">
                                        {{ user.deleted_at ? 'Inactive' : 'Active' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-6"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li><a class="dropdown-item d-flex align-items-center gap-3"
                                                :href="`/user/${user.id}/edit`"><i class="fs-4 ti ti-edit"></i>Edit</a>
                                        </li>
                                        <li v-if="user.deleted_at"><a
                                                class="dropdown-item d-flex align-items-center gap-3 text-success"
                                                href="#" @click.prevent="restoreUser(user)"><i
                                                    class="fs-4 ti ti-refresh"></i>Restore</a></li>
                                        <li v-else><a class="dropdown-item d-flex align-items-center gap-3 text-danger"
                                                href="#" @click.prevent="confirmDelete(user)"><i
                                                    class="fs-4 ti ti-trash"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add User Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="addUserOffcanvas" aria-labelledby="addUserOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="addUserOffcanvasLabel">Add User</h5>
            <button type="button" class="btn-close" @click="hideAddUserOffcanvas" aria-label="Close"></button>
        </div>
        <form @submit.prevent="addUser" class="d-flex flex-column h-100">
            <div class="offcanvas-body flex-grow-1">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="addUserName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="addUserName" v-model="newUser.name"
                            :class="{ 'is-invalid': errors.name }" placeholder="John" required>
                        <div v-if="errors.name" class="invalid-feedback">{{ errors.name[0] }}</div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="addUserLast" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="addUserLast" v-model="newUser.last"
                            :class="{ 'is-invalid': errors.last }" placeholder="Doe" required>
                        <div v-if="errors.last" class="invalid-feedback">{{ errors.last[0] }}</div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="addUserEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="addUserEmail" v-model="newUser.email"
                            :class="{ 'is-invalid': errors.email }" placeholder="john.doe@example.com" required>
                        <div v-if="errors.email" class="invalid-feedback">{{ errors.email[0] }}</div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="addUserRole" class="form-label">User Role</label>
                        <select class="form-select" id="addUserRole" v-model="newUser.role"
                            :class="{ 'is-invalid': errors.role }" required>
                            <option value="" disabled>Select Role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                        </select>
                        <div v-if="errors.role" class="invalid-feedback">{{ errors.role[0] }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="addUserPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="addUserPassword" v-model="newUser.password"
                            :class="{ 'is-invalid': errors.password }" placeholder="••••••••" required>
                        <div v-if="errors.password" class="invalid-feedback">{{ errors.password[0] }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="addUserConfirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="addUserConfirmPassword"
                            v-model="newUser.password_confirmation" placeholder="••••••••" required>
                    </div>
                </div>
            </div>
            <div class="offcanvas-footer bg-light p-3 border-top">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" @click="hideAddUserOffcanvas">Cancel</button>
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status"
                            aria-hidden="true"></span>
                        {{ isSubmitting ? 'Submitting...' : 'Submit' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import * as bootstrap from 'bootstrap';

const users = ref([]);
const roles = ref([]);
const allUsersForAssignment = ref([]);
const isLoading = ref(true);
const searchQuery = ref('');
const activeSections = reactive({});

let addUserOffcanvasInstance = null;
const isSubmitting = ref(false);
const newUser = reactive({
    name: '',
    last: '',
    email: '',
    role: '',
    password: '',
    password_confirmation: '',
});
const errors = reactive({});

const filteredUsers = computed(() => {
    if (!searchQuery.value) {
        return users.value;
    }
    const query = searchQuery.value.toLowerCase();
    return users.value.filter(user =>
        (user.name && user.name.toLowerCase().includes(query)) ||
        (user.last && user.last.toLowerCase().includes(query)) ||
        (user.email && user.email.toLowerCase().includes(query))
    );
});

const appAssignableUsers = computed(() => (currentUserId) => {
    return allUsersForAssignment.value.filter(u => u.id !== currentUserId);
});

const leadAssignableUsers = computed(() => (currentUserId) => {
    return allUsersForAssignment.value.filter(u => u.id !== currentUserId);
});

const fetchData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/users');
        users.value = response.data.users.data;
        roles.value = response.data.roles;
        allUsersForAssignment.value = response.data.allUsersForAssignment;
        users.value.forEach(user => {
            activeSections[user.id] = { app: false, lead: false };
        });
    } catch (error) {
        console.error("Error fetching data:", error);
        Swal.fire('Error', 'Could not load user data.', 'error');
    } finally {
        isLoading.value = false;
    }
};

const resetNewUserForm = () => {
    Object.assign(newUser, {
        name: '',
        last: '',
        email: '',
        role: '',
        password: '',
        password_confirmation: '',
    });
    Object.keys(errors).forEach(key => delete errors[key]);
};

const addUser = async () => {
    isSubmitting.value = true;
    Object.keys(errors).forEach(key => delete errors[key]);
    try {
        await axios.post('/api/users/store', newUser);
        hideAddUserOffcanvas();
        await fetchData();
        Swal.fire({
            title: 'Success!',
            text: 'User has been created successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    } catch (error) {
        if (error.response && error.response.status === 422) {
            Object.assign(errors, error.response.data.errors);
            Swal.fire('Validation Error', 'Please check the form for errors.', 'error');
        } else {
            console.error("Error adding user:", error);
            Swal.fire('Error!', 'An unexpected error occurred while creating the user.', 'error');
        }
    } finally {
        isSubmitting.value = false;
    }
};

const getAssignmentText = (user) => {
    const appCount = user.application?.length || 0;
    const leadCount = user.lead?.length || 0;
    if (appCount === 0 && leadCount === 0) return 'No assignments';
    let parts = [];
    if (appCount > 0) parts.push(`${appCount} Application${appCount > 1 ? 's' : ''}`);
    if (leadCount > 0) parts.push(`${leadCount} Lead${leadCount > 1 ? 's' : ''}`);
    return parts.join(', ');
};

const updateRole = async (user) => {
    const originalRole = users.value.find(u => u.id === user.id)?.role;
    try {
        await axios.post(`/api/users/${user.id}/update-role`, { role: user.role });
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Role updated successfully',
            showConfirmButton: false,
            timer: 3000
        });
    } catch (error) {
        user.role = originalRole;
        console.error("Error updating role:", error);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: error.response?.data?.message || 'Failed to update role',
            showConfirmButton: false,
            timer: 3000
        });
    }
};

const updateAssignment = async (user, type) => {
    const payload = {};
    if (type === 'application') {
        payload.application = user.application;
    } else if (type === 'lead') {
        payload.lead = user.lead;
    }
    try {
        await axios.post(`/api/users/${user.id}/update-assignments`, payload);
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: 'Assignments updated', showConfirmButton: false, timer: 3000
        });
    } catch (error) {
        console.error(`Error updating ${type}:`, error);
        Swal.fire({
            toast: true, position: 'top-end', icon: 'error',
            title: `Error updating ${type}`, showConfirmButton: false, timer: 3000
        });
        fetchData();
    }
};

const confirmDelete = (user) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will deactivate the user. Their applications will need to be reassigned.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, deactivate it!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/users/${user.id}`);
                user.deleted_at = new Date().toISOString();
                Swal.fire('Deactivated!', 'The user has been deactivated.', 'success');
            } catch (error) {
                console.error("Error deleting user:", error);
                Swal.fire('Error', 'Failed to deactivate user.', 'error');
            }
        }
    });
};

const restoreUser = async (user) => {
    try {
        await axios.post(`/api/users/${user.id}/restore`);
        user.deleted_at = null;
        Swal.fire('Restored!', 'The user has been restored.', 'success');
    } catch (error) {
        console.error("Error restoring user:", error);
        Swal.fire('Error', 'Failed to restore user.', 'error');
    }
};

const exportUsers = () => {
    window.location.href = '/api/users/export';
};

const showAddUserOffcanvas = () => {
    if (addUserOffcanvasInstance) {
        addUserOffcanvasInstance.show();
    }
};

const hideAddUserOffcanvas = () => {
    if (addUserOffcanvasInstance) {
        addUserOffcanvasInstance.hide();
    }
};

const toggleSection = (userId, section) => {
    if (!activeSections[userId]) {
        activeSections[userId] = { app: false, lead: false };
    }
    activeSections[userId][section] = !activeSections[userId][section];
};

const selectAll = (user, type) => {
    if (type === 'application') {
        user.application = appAssignableUsers.value(user.id).map(u => u.id);
    } else if (type === 'lead') {
        user.lead = leadAssignableUsers.value(user.id).map(u => u.id);
    }
    updateAssignment(user, type);
};

const handleNoneSelection = (user, type) => {
    user[type] = [];
    updateAssignment(user, type);
};

onMounted(() => {
    fetchData();
    const offcanvasEl = document.getElementById('addUserOffcanvas');
    if (offcanvasEl) {
        addUserOffcanvasInstance = new bootstrap.Offcanvas(offcanvasEl);
        offcanvasEl.addEventListener('hidden.bs.offcanvas', resetNewUserForm);
    }
});
</script>

<style>
/* Table & Dropdown Styles */
.application-dropdown {
    position: relative;
}

.application-dropdown .dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 0.25rem;
    width: auto;
    min-width: 100%;
    max-width: 300px;
    z-index: 1050;
}

.clickable-header {
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.clickable-header:hover {
    background-color: rgba(46, 125, 50, 0.1) !important;
    color: #2e7d32 !important;
}

.dropdown-section {
    margin-left: 15px;
    padding: 5px;
    border-left: 2px solid #dee2e6;
}

.dropdown-toggle:hover .selected-text {
    background-color: rgba(46, 125, 50, 0.1);
    color: #2e7d32;
}

.dropdown-header:hover {
    color: green !important;
}

/* Offcanvas Styles */
.offcanvas {
    width: 500px !important;
}

@media (max-width: 768px) {
    .offcanvas {
        width: 100% !important;
    }
}

.offcanvas-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

/* Form Styles */
.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
}

.form-control,
.form-select {
    border-radius: 8px;
    /* padding: 12px 16px; */
    font-size: 14px;
    transition: all 0.2s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: #2e7d32;
    box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.15);
}

.btn {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
}

.btn-primary {
    background-color: #2e7d32;
    border-color: #2e7d32;
}

.btn-primary:hover {
    background-color: #1b5e20;
    border-color: #1b5e20;
}

.invalid-feedback {
    display: block;
}
</style>