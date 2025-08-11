<template>
    <div class="position-relative overflow-hidden">
        <!-- Loading Spinner -->
        <div v-if="isLoading" class="d-flex justify-content-center align-items-center" style="height: 400px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Profile Content -->
        <div v-else>
            <div class="position-relative overflow-hidden rounded-3">
                <img src="/assets/images/backgrounds/bg1.jpg" alt="profile-background" class="w-100"
                    style="height: 300px; object-fit:cover; object-position: top-center;">
            </div>
            <div class="card mx-md-9 mt-n5">
                <div class="card-body pb-0">
                    <div class="d-md-flex align-items-center justify-content-between text-center text-md-start">
                        <div class="d-md-flex align-items-center">
                            <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
                                <img src="/assets/images/profile/user-1.jpg" alt="user-avatar"
                                    class="img-fluid rounded-circle" width="100" height="100">
                            </div>
                            <div class="ms-0 ms-md-3 mb-9 mb-md-0">
                                <div
                                    class="d-flex align-items-center justify-content-center justify-content-md-start mb-1">
                                    <h4 class="me-7 mb-0 fs-7">{{ user.name }} {{ user.last }}</h4>
                                </div>
                                <p class="fs-4 mb-1">{{ user.email }}</p>
                                <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                                    <span class="bg-success p-1 rounded-circle"></span>
                                    <h6 class="mb-0 ms-2">Active</h6>
                                </div>
                            </div>
                        </div>
                        <button @click="activeTab = 'settings'" class="btn btn-primary px-3 shadow-none">
                            Edit Profile
                        </button>
                    </div>

                    <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start"
                        role="tablist">
                        <li class="nav-item me-2 me-md-3" role="presentation">
                            <button @click="activeTab = 'profile'"
                                class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6"
                                :class="{ 'active': activeTab === 'profile' }" type="button">
                                <i class="ti ti-user-circle me-0 me-md-6 fs-6"></i>
                                <span class="d-none d-md-block">Profile</span>
                            </button>
                        </li>
                        <li class="nav-item me-2 me-md-3" role="presentation">
                            <button @click="activeTab = 'settings'"
                                class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6"
                                :class="{ 'active': activeTab === 'settings' }" type="button">
                                <i class="ti ti-settings me-0 me-md-6 fs-6"></i>
                                <span class="d-none d-md-block">Settings</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content mx-md-10 mt-3" id="pills-tabContent">
                <!-- Profile Tab -->
                <div v-if="activeTab === 'profile'" class="tab-pane fade show active">
                    <div class="card">
                        <div class="card-body p-4">
                            <h4 class="fs-6 mb-4">Basic Information</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <div class="form-control bg-light">{{ user.name }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <div class="form-control bg-light">{{ user.last }}</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="form-control bg-light">{{ user.email }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Account Created</label>
                                <div class="form-control bg-light">
                                    {{ formatDate(user.created_at) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div v-if="activeTab === 'settings'" class="tab-pane fade show active">
                    <div class="card">
                        <div class="card-body p-4">
                            <!-- Let's only show "Edit Profile" if they have permission -->
                            <!-- This requires passing down a 'can' object or similar from your auth user -->
                            <h4 class="fs-6 mb-4">Update Profile</h4>
                            <form @submit.prevent="updateProfile">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" v-model="profileForm.name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" v-model="profileForm.last" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" v-model="profileForm.email"
                                        :disabled="!isEmailEditable" required>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary" :disabled="isSubmittingProfile">
                                        {{ isSubmittingProfile ? 'Updating...' : 'Update Profile' }}
                                    </button>
                                </div>
                            </form>

                            <!-- V-IF CONDITION ADDED HERE -->
                            <!-- Only show Change Password form if the user is viewing their OWN profile (props.id is null/undefined) -->
                            <div v-if="!props.id" class="border-top mt-5 pt-4">
                                <h5 class="mb-4">Change Password</h5>
                                <form @submit.prevent="updatePassword">
                                    <!-- ... password form fields ... -->
                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control"
                                            v-model="passwordForm.current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" v-model="passwordForm.password"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control"
                                            v-model="passwordForm.password_confirmation" required>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary" :disabled="isSubmittingPassword">
                                            {{ isSubmittingPassword ? 'Changing...' : 'Change Password' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';
// Optional: For user-friendly notifications. Install with `npm install sweetalert2`
import Swal from 'sweetalert2';

// --- STATE ---
const props = defineProps({
    id: {
        type: [String, Number],
        required: false, // <-- Change this to false
        default: null      // <-- Good practice to add a default
    },
});
const isLoading = ref(true);
const isSubmittingProfile = ref(false);
const isSubmittingPassword = ref(false);
const user = ref({});
const activeTab = ref('profile');
const authUser = ref(null); // 'profile' or 'settings'

const profileForm = ref({
    name: '',
    last: '',
    email: ''
});

const passwordForm = ref({
    current_password: '',
    password: '',
    password_confirmation: ''
});

const isEmailEditable = computed(() => {
    // If the authenticated user's data isn't loaded yet, default to false for safety.
    if (!authUser.value || !authUser.value.permissions) {
        return false;
    }

    // The logic is now very simple: the field is editable if, and only if,
    // the authenticated user's permissions array includes 'view_users'.
    // This single line enforces your rule for all cases.
    return authUser.value.permissions.includes('view_users');
});
  
const fetchAuthUser = async () => {
    try {
        const response = await axios.get('/api/auth/user');
        authUser.value = response.data; // Stores { user: {...}, permissions: [...] }
    } catch (error) {
        console.error("Failed to fetch authenticated user permissions:", error);
        // We can let this fail silently, the computed property will handle the null state
    }
};
// --- METHODS ---
const fetchUser = async () => {
    // This is the core logic change.
    // If props.id exists, we are viewing a specific user.
    // If props.id is null/undefined, we are viewing the auth user's profile.
    const apiUrl = props.id
        ? `/api/user/profile/${props.id}`  // URL for a specific user
        : '/api/user/profile';             // URL for the authenticated user

    isLoading.value = true;
    try {
        const response = await axios.get(apiUrl); // Use the dynamically built URL

        user.value = response.data;
        // Populate the form
        profileForm.value.name = user.value.name;
        profileForm.value.last = user.value.last;
        profileForm.value.email = user.value.email;
    } catch (error) {
        console.error("Failed to fetch user data:", error);
        Swal.fire('Error', 'Could not load the user profile.', 'error');
    } finally {
        isLoading.value = false;
    }
};

const updateProfile = async () => {
    isSubmittingProfile.value = true;

    // --- THIS IS THE KEY CHANGE ---
    // Build the URL dynamically based on whether an ID prop exists.
    const apiUrl = props.id
        ? `/api/user/profile/update/${props.id}`
        : '/api/user/profile/update';

    try {
        // Use the dynamic URL
        const response = await axios.post(apiUrl, profileForm.value);
        user.value = { ...user.value, ...response.data };
        Swal.fire('Success!', 'The profile has been updated.', 'success');
        activeTab.value = 'profile';
    } catch (error) {
        const message = error.response?.data?.message || 'An error occurred while updating the profile.';
        Swal.fire('Error', message, 'error');
        console.error("Profile update error:", error.response);
    } finally {
        isSubmittingProfile.value = false;
    }
};

const updatePassword = async () => {
    isSubmittingPassword.value = true;
    try {
        const response = await axios.post('/api/user/password/update', passwordForm.value);
        Swal.fire('Success!', response.data.message, 'success');
        passwordForm.value = { current_password: '', password: '', password_confirmation: '' };
    } catch (error) {
        const message = error.response?.data?.message || 'An error occurred.';
        Swal.fire('Error', message, 'error');
    } finally {
        isSubmittingPassword.value = false;
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// --- LIFECYCLE HOOKS ---
onMounted(async () => {
    await Promise.all([
        fetchUser(),
        fetchAuthUser()
    ]);
});

watch(() => props.id, (newId) => {
    fetchUser();
});
</script>

<style scoped>
.bg-light {
    background-color: #f8f9fa !important;
}

.form-control.bg-light {
    border: 1px solid #e9ecef;
    padding: 0.5rem 0.75rem;
    min-height: calc(1.5em + 1rem + 2px);
}

.nav-pills .nav-link {
    color: #6b7280;
}

.nav-pills .nav-link.active {
    color: #2a3547;
    /* Or your theme's primary color */
    background-color: transparent;
    border-bottom: 2px solid #2a3547;
}

.user-profile-tab .nav-link:not(.active):hover {
    border-bottom: 2px solid #e9ecef;
}
</style>