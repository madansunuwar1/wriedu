<template>
    <!-- Loading and Error States -->
    <div v-if="loading" class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading University Profile...</p>
    </div>

    <div v-else-if="error" class="alert alert-danger mx-9">
        {{ error }}
    </div>

    <!-- Main Content -->
    <div v-else-if="university">
        <div class="position-relative overflow-hidden">
            <div class="card mx-9">
                <div class="card-body pb-0">
                    <div class="d-md-flex align-items-center justify-content-between text-center text-md-start">
                        <div class="d-md-flex align-items-center">
                            <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
                                <img :src="universityImage" :alt="university.newUniversity"
                                    class="img-fluid rounded-circle" width="100" height="100"
                                    style="object-fit: cover;">
                            </div>
                            <div class="ms-0 ms-md-3 mb-9 mb-md-0">
                                <div
                                    class="d-flex align-items-center justify-content-center justify-content-md-start mb-1">
                                    <h4 class="me-7 mb-0 fs-7">{{ university.newUniversity }}</h4>
                                    <span
                                        class="badge fs-2 fw-bold rounded-pill bg-success-subtle text-success border-success border">
                                        Active
                                    </span>
                                </div>
                                <p
                                    class="fs-4 mb-1 d-flex align-items-center justify-content-center justify-content-md-start text-muted">
                                    <i class="ti ti-map-pin me-1 fs-5"></i> {{ university.newLocation }}, {{
                                    university.country }}
                                </p>
                                <div
                                    class="d-flex align-items-center justify-content-center justify-content-md-start mt-2 gap-2">
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ university.newCourse }}
                                    </span>
                                    <span class="badge bg-info-subtle text-info">
                                        {{ university.newIntake }} Intake
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="mt-4 mt-md-0 d-flex justify-content-center gap-2">
                            <button @click="editUniversity" class="btn btn-outline-primary shadow-none px-3">
                               <i class="ti ti-pencil me-1"></i> Edit
                            </button>
                            <button @click="addApplication" class="btn btn-primary shadow-none px-3">
                                <i class="ti ti-plus me-1"></i> Add Application
                            </button>
                        </div> -->
                    </div>

                    <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start"
                        id="pills-tab" role="tablist">
                        <li v-for="tab in tabs" :key="tab.id" class="nav-item me-2 me-md-3" role="presentation">
                            <button
                                class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6"
                                :class="{ 'active': activeTab === tab.id }" @click="activeTab = tab.id" type="button"
                                role="tab">
                                <i :class="tab.icon" class="me-0 me-md-2 fs-6"></i>
                                <span class="d-none d-md-block">{{ tab.name }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content mx-10 mt-4" id="pills-tabContent">
            <!-- Profile Tab -->
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'profile' }" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 d-flex align-items-center"><i class="fas fa-info-circle me-2 text-primary"></i>
                            Basic Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                style="min-width: 120px;"><i class="fas fa-university me-2 text-muted"></i>
                                University:</span><span class="fw-medium">{{ university.newUniversity }}</span></div>
                        <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                style="min-width: 120px;"><i class="fas fa-book me-2 text-muted"></i>
                                Course:</span><span class="fw-medium">{{ university.newCourse }}</span></div>
                        <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                style="min-width: 120px;"><i class="fas fa-globe-americas me-2 text-muted"></i>
                                Country:</span><span class="fw-medium">{{ university.country }}</span></div>
                        <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                style="min-width: 120px;"><i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                Location:</span><span class="fw-medium">{{ university.newLocation }}</span></div>
                        <div class="d-flex align-items-start"><span class="text-muted me-3" style="min-width: 120px;"><i
                                    class="fas fa-calendar-alt me-2 text-muted"></i> Intake:</span><span
                                class="fw-medium">{{ university.newIntake }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Financial Tab -->
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'financial' }" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 d-flex align-items-center"><i class="fas fa-coins me-2 text-warning"></i>
                            Financial Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                style="min-width: 120px;"><i class="fas fa-award me-2 text-muted"></i>
                                Scholarship:</span><span class="fw-medium">{{ university.newScholarship || 'N/A'
                                }}</span></div>
                        <div class="d-flex align-items-start"><span class="text-muted me-3" style="min-width: 120px;"><i
                                    class="fas fa-dollar-sign me-2 text-muted"></i> Amount:</span><span
                                class="fw-medium">{{ university.newAmount || 'N/A' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Language Tab -->
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'language' }" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 d-flex align-items-center"><i class="fas fa-language me-2 text-info"></i>
                            Language Requirements</h5>
                    </div>
                    <div class="card-body p-4">
                        <template v-if="university.level === 'Undergraduate'">
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i>
                                    Undergrad IELTS:</span><span class="fw-medium">{{ university.newIelts || 'N/A'
                                    }}</span></div>
                            <div class="d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i>
                                    Undergrad PTE:</span><span class="fw-medium">{{ university.newpte || 'N/A' }}</span>
                            </div>
                        </template>
                        <template v-else-if="university.level === 'Postgraduate'">
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i>
                                    Postgrad IELTS:</span><span class="fw-medium">{{ university.newPgIelts || 'N/A'
                                    }}</span></div>
                            <div class="d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i>
                                    Postgrad PTE:</span><span class="fw-medium">{{ university.newPgPte || 'N/A'
                                    }}</span></div>
                        </template>
                        <template v-else>
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i>
                                    Undergrad IELTS:</span><span class="fw-medium">{{ university.newIelts || 'N/A'
                                    }}</span></div>
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i>
                                    Undergrad PTE:</span><span class="fw-medium">{{ university.newpte || 'N/A' }}</span>
                            </div>
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i>
                                    Postgrad IELTS:</span><span class="fw-medium">{{ university.newPgIelts || 'N/A'
                                    }}</span></div>
                            <div class="d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i>
                                    Postgrad PTE:</span><span class="fw-medium">{{ university.newPgPte || 'N/A'
                                    }}</span></div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Academic Tab -->
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'academic' }" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 d-flex align-items-center"><i
                                class="fas fa-graduation-cap me-2 text-success"></i> Academic
                            Requirements</h5>
                    </div>
                    <div class="card-body p-4">
                        <template v-if="university.level === 'Undergraduate'">
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-user-graduate me-2 text-muted"></i>
                                    Undergraduate:</span><span class="fw-medium">{{ university.newug || 'N/A' }}</span>
                            </div>
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-chart-bar me-2 text-muted"></i> Undergrad
                                    GPA:</span><span class="fw-medium">{{ university.newgpaug || 'N/A' }}</span></div>
                        </template>
                        <template v-else-if="university.level === 'Postgraduate'">
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-user-graduate me-2 text-muted"></i>
                                    Postgraduate:</span><span class="fw-medium">{{ university.newpg || 'N/A' }}</span>
                            </div>
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-chart-bar me-2 text-muted"></i> Postgrad
                                    GPA:</span><span class="fw-medium">{{ university.newgpapg || 'N/A' }}</span></div>
                        </template>
                        <template v-else>
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-user-graduate me-2 text-muted"></i>
                                    Undergraduate:</span><span class="fw-medium">{{ university.newug || 'N/A' }}</span>
                            </div>
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-user-graduate me-2 text-muted"></i>
                                    Postgraduate:</span><span class="fw-medium">{{ university.newpg || 'N/A' }}</span>
                            </div>
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-chart-bar me-2 text-muted"></i> Undergrad
                                    GPA:</span><span class="fw-medium">{{ university.newgpaug || 'N/A' }}</span></div>
                            <div class="mb-3 d-flex align-items-start"><span class="text-muted me-3"
                                    style="min-width: 160px;"><i class="fas fa-chart-bar me-2 text-muted"></i> Postgrad
                                    GPA:</span><span class="fw-medium">{{ university.newgpapg || 'N/A' }}</span></div>
                        </template>
                        <div class="d-flex align-items-start"><span class="text-muted me-3" style="min-width: 160px;"><i
                                    class="fas fa-tasks me-2 text-muted"></i> Additional Tests:</span><span
                                class="fw-medium">{{
                                university.newtest || 'N/A' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Documents Tab -->
            <div class="tab-pane fade" :class="{ 'show active': activeTab === 'documents' }" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 d-flex align-items-center"><i class="fas fa-file-alt me-2 text-secondary"></i>
                            Required
                            Documents</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <template v-if="parsedDocuments.length">
                                <div v-for="(section, index) in parsedDocuments" :key="index" class="mb-4">
                                    <h6 class="text-primary border-bottom pb-2">{{ section.title }} Documents</h6>
                                    <ul class="list-group list-group-flush">
                                        <li v-for="doc in section.docs" :key="doc"
                                            class="list-group-item px-0 py-1 text-dark">
                                            <i class="fas fa-check-circle text-success me-2"></i>{{ doc }}
                                        </li>
                                    </ul>
                                </div>
                            </template>
                            <p v-else class="text-dark mb-0">No specific documents listed.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// Define props to accept the university ID from the parent component/route
const props = defineProps({
    id: {
        type: [Number, String],
        required: true
    }
});

// --- STATE MANAGEMENT ---
const university = ref(null);
const matchedUniversity = ref(null);
const loading = ref(true);
const error = ref(null);
const activeTab = ref('profile');

// --- TABS CONFIGURATION ---
const tabs = ref([
    { id: 'profile', name: 'Profile', icon: 'fas fa-info-circle' },
    { id: 'financial', name: 'Financial', icon: 'fas fa-coins' },
    { id: 'language', name: 'Language', icon: 'fas fa-language' },
    { id: 'academic', name: 'Academic', icon: 'fas fa-graduation-cap' },
    { id: 'documents', name: 'Documents', icon: 'fas fa-file-alt' },
]);

// --- COMPUTED PROPERTIES ---

// Safely get the university image URL with a fallback
const universityImage = computed(() => {
    return matchedUniversity.value?.image_link || '/assets/images/backgrounds/bg1.jpg';
});

// Parse the 'requireddocuments' string into a structured array
const parsedDocuments = computed(() => {
    if (!university.value?.requireddocuments) {
        return [];
    }

    const rawText = university.value.requireddocuments.trim();
    const pattern = /(SLC-|NEB-|Bachelor\/Masters-)/;
    const parts = rawText.split(pattern).filter(p => p.trim() !== '');

    const sections = [];
    for (let i = 0; i < parts.length; i += 2) {
        if (parts[i] && parts[i + 1]) {
            const title = parts[i].replace('-', '').trim();
            const docs = parts[i + 1]
                .split(',')
                .map(doc => doc.trim())
                .filter(doc => doc);

            if (docs.length > 0) {
                sections.push({ title, docs });
            }
        }
    }
    return sections;
});


// --- METHODS ---
const fetchUniversityData = async () => {
    loading.value = true;
    error.value = null;
    try {
        // This now points to your new API route
        const response = await axios.get(`/api/university-profile/${props.id}`);
        university.value = response.data.university;
        matchedUniversity.value = response.data.matchedUniversity;
    } catch (err) {
        console.error("Failed to fetch university data:", err);
        error.value = err.response?.data?.message || 'An error occurred. Please try again.';
    } finally {
        loading.value = false;
    }
};

const editUniversity = () => {
    console.log(`Editing university with ID: ${props.id}`);
    alert('Edit functionality not yet implemented.');
};

const addApplication = () => {
    console.log(`Adding application for university: ${university.value.newUniversity}`);
    alert('Add Application functionality not yet implemented.');
};

// --- LIFECYCLE HOOK ---
onMounted(() => {
    fetchUniversityData();
});
</script>

<style scoped>
.user-profile-tab .nav-link.active,
.user-profile-tab .nav-link:hover {
    border-bottom: 2px solid var(--bs-primary);
}

.img-fluid {
    object-fit: cover;
}
</style>