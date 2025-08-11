<template>
    <div v-if="isLoading" class="d-flex justify-content-center align-items-center" style="height: 80vh;">
        <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;"><span
                class="visually-hidden">Loading...</span></div>
    </div>
    <div v-else-if="!lead" class="text-center py-5">
        <h2>Lead Not Found</h2>
        <router-link :to="{ name: 'leads.index' }" class="btn btn-primary">Back to Leads List</router-link>
    </div>
    <div v-else>
        <!-- [NEW] Top-Level Navigation for Overview/Timeline -->
        <div class="px-9 py-3 d-flex">
            <ul class="nav nav-pills-1">
                <li class="nav-item-1">
                    <button class="nav-link" :class="{ active: mainView === 'overview' }"
                        @click="mainView = 'overview'">Overview</button>
                </li>
                <li class="nav-item-1 ms-2">
                    <button class="nav-link" :class="{ active: mainView === 'timeline' }"
                        @click="mainView = 'timeline'">Timeline</button>
                </li>
            </ul>
        </div>

        <!-- [NEW] Conditional content based on the main view selection -->
        <div class="px-9">
            <!-- ========== OVERVIEW CONTENT ========== -->
            <div v-if="mainView === 'overview'">
                <!-- The original profile header card -->
                <div class="card">
                    <div class="card-body pb-0">
                        <div class="d-md-flex align-items-center justify-content-between">
                            <div class="d-md-flex align-items-center">
                                <img :src="getAvatarUrl(lead)" :alt="lead.name" class="img-fluid rounded-circle"
                                    width="100" height="100">
                                <div class="ms-0 ms-md-3">
                                    <h4 class="me-7 mb-0 fs-7">{{ lead.name }}</h4>
                                    <span class="badge" :class="displayStatus.badgeClass">{{ displayStatus.text
                                        }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- Sub-tabs for the overview page -->
                        <ul class="nav nav-pills user-profile-tab mt-4" role="tablist">
                            <li v-for="tab in subTabs" :key="tab.id" class="nav-item me-2" role="presentation">
                                <button class="nav-link" :class="{ active: activeSubTab === tab.id }"
                                    @click="handleSubTabClick(tab)">
                                    <i class="bi me-2" :class="tab.icon"></i> {{ tab.name }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Sub-tab content -->
                <div class="tab-content mt-4">
                    <div v-show="activeSubTab === 'details'">
                        <div class="row">
                            <div class="col-lg-4">
                                <InfoCard title="Personal Information" :fields="personalFields"
                                    @field-updated="handleFieldUpdate" />
                                <InfoCard title="Academic Information" :fields="academicFields"
                                    @field-updated="handleFieldUpdate" />
                                <InfoCard title="University Information" :fields="universityFields"
                                    @field-updated="handleFieldUpdate" />
                                <InfoCard title="Test Scores" :fields="testScoreFields"
                                    @field-updated="handleFieldUpdate" />
                                <InfoCard title="System Information" :fields="systemFields" :users="users"
                                    @field-updated="handleFieldUpdate" />
                            </div>
                            <div class="col-lg-8">
                                <!-- Comment Form -->
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Add Comment / Set Follow-up</h6>
                                        <form @submit.prevent="submitComment">
                                            <div class="mb-3">
                                                <textarea id="comment-textarea" v-model="newCommentText"
                                                    class="form-control" rows="3" required
                                                    placeholder="Type your comment..."></textarea>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <button type="submit" class="btn btn-primary"
                                                    :disabled="isSubmittingComment">
                                                    <span v-if="isSubmittingComment"
                                                        class="spinner-border spinner-border-sm me-1"
                                                        role="status"></span>
                                                    {{ isSubmittingComment ? 'Posting...' : 'Post Comment' }}
                                                </button>
                                                <div>
                                                    <div class="visually-hidden-input">
                                                        <!-- 2. Move the flat-pickr component inside and remove its class -->
                                                        <flat-pickr ref="flatpickrRef" v-model="followUpDateTime"
                                                            :config="followUpPickerConfig">
                                                        </flat-pickr>
                                                    </div>
                                                    <button v-if="!followUpDateTime" type="button"
                                                        class="btn btn-outline-secondary" @click="openDatePicker">
                                                        <i class="bi bi-calendar-plus me-1"></i>
                                                        Set Follow-up
                                                    </button>
                                                    <div v-else class="btn-group">
                                                        <button type="button" class="btn btn-outline-info"
                                                            @click="openDatePicker">
                                                            <i class="bi bi-bell me-1"></i>
                                                            {{ formatSelectedDate(followUpDateTime) }}
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info"
                                                            @click="clearFollowUp" aria-label="Clear Follow-up">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- Comment List -->
                                <div class="mt-4 w-100" style="max-height: 1500px; overflow-y: auto;">
                                    <div v-for="comment in sortedComments" :key="comment.id"
                                        class="p-4 text-bg-light mb-4 card">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <div class="d-flex align-items-center w-100">
                                                <img :src="getAvatarUrl(comment.created_by)"
                                                    :alt="comment.created_by?.name" class="rounded-circle" width="40"
                                                    height="40">
                                                <div class="ms-3 d-flex align-items-center gap-4 w-100">
                                                    <h6 class="">{{ comment.created_by?.name || 'Unknown User' }} {{
                                                        comment.created_by?.last || '' }}</h6>
                                                    <h6 class="text-muted" style="font-size: 12px;">{{
                                                        formatTimeAgo(comment.created_at) }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mt-4 mb-0" style="white-space: pre-wrap;">{{ comment.comment }}</p>
                                    </div>
                                    <div v-if="!sortedComments.length" class="text-center p-4 text-muted">
                                        No comments yet.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-show="activeSubTab === 'documents'">
                        <FileUpload :lead-id="props.leadId" :initial-files="lead.uploads"
                            @files-updated="fetchLeadData" />
                    </div>
                    <div v-show="activeSubTab === 'status'">
                        <StatusManager :statuses="documentStatuses" :current-status="lead.status"
                            @status-updated="handleStatusUpdate" />
                    </div>
                </div>
            </div>

            <!-- ========== TIMELINE CONTENT ========== -->
            <div v-else-if="mainView === 'timeline'">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">History</h5>
                        <hr />
                        <LeadTimeline :activities="lead.activities || []" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Forwarding Modal -->
        <div v-if="showForwardModal" class="modal fade show" tabindex="-1"
            style="display: block; background-color: rgba(0,0,0,0.5);" @click.self="showForwardModal = false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Forward Application</h5>
                        <button type="button" class="btn-close" @click="showForwardModal = false"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitForwarding" id="forwardingForm">
                            <div class="mb-3">
                                <label class="form-label">Select User to Forward To:</label>
                                <select v-model="forwarding.userId" class="form-select" required>
                                    <option disabled :value="null">-- Please select a user --</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }} {{
                                        user.last }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional):</label>
                                <textarea v-model="forwarding.notes" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" v-model="forwarding.sendEmail"
                                    id="sendEmailCheck">
                                <label class="form-check-label" for="sendEmailCheck">Send Email Notification</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            @click="showForwardModal = false">Cancel</button>
                        <button type="submit" form="forwardingForm" class="btn btn-primary"
                            :disabled="!forwarding.userId || isForwarding">
                            <span v-if="isForwarding" class="spinner-border spinner-border-sm me-1" role="status"
                                aria-hidden="true"></span>
                            {{ isForwarding ? 'Submitting...' : 'Submit Application' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showForwardModal" class="modal-backdrop fade show"></div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import InfoCard from '@/components/layout/InfoCard.vue';
import FileUpload from '@/components/layout/FileUpload.vue';
import StatusManager from '@/components/layout/StatusManager.vue';
import LeadTimeline from './LeadTimeline.vue';
import { useToast } from 'vue-toastification'; // 1. NEW: Import useToast

const toast = useToast(); // 2. NEW: Instantiate the toast service
const props = defineProps({ leadId: [Number, String] });

const mainView = ref('overview'); // 'overview' or 'timeline'
const activeSubTab = ref('details'); // Default sub-tab

const isLoading = ref(true);
const lead = ref(null);
const users = ref([]);
const documentStatuses = ref([]);
const newCommentText = ref('');
const isSubmittingComment = ref(false);
const followUpDateTime = ref(null);
const forwarding = ref({
    userId: null,
    notes: '',
    sendEmail: true,
});
const isForwarding = ref(false);
const showForwardModal = ref(false);
const flatpickrRef = ref(null);

const formOptions = ref({
    countries: [], course_levels: [], locations: [],
    universities: [], courses: [], intakes: [],
});

const followUpPickerConfig = ref({
    enableTime: true, altInput: false, dateFormat: "Y-m-d H:i", minDate: "today",
});

const subTabs = [
    { id: 'details', name: 'Details', icon: 'bi-person' },
    { id: 'documents', name: 'Documents', icon: 'bi-file-earmark' },
    { id: 'status', name: 'Status', icon: 'bi-card-checklist' },
    { id: 'forward', name: 'Forward', icon: 'bi-send' },
];

const sortedComments = computed(() => {
    if (!lead.value || !lead.value.lead_comments) return [];
    return [...lead.value.lead_comments].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const personalFields = computed(() => !lead.value ? [] : [
    { label: 'Name', value: lead.value.name, field: 'name', icon: 'bi-person' },
    { label: 'Phone Number', value: lead.value.phone, field: 'phone', icon: 'bi-telephone' },
    { label: 'Location', value: lead.value.locations, field: 'locations', icon: 'bi-geo-alt' },
    { label: 'Email', value: lead.value.email, field: 'email', icon: 'bi-envelope' },
]);

const academicFields = computed(() => !lead.value ? [] : [
    { label: 'Last Qualification', value: lead.value.lastqualification, field: 'lastqualification', icon: 'bi-bookmark-star' },
    { label: 'Passed Year', value: lead.value.passed, field: 'passed', icon: 'bi-calendar-check' },
    { label: 'GPA/Percentage', value: lead.value.gpa, field: 'gpa', icon: 'bi-star-half' },
    { label: 'Document Received', value: lead.value.academic, field: 'academic', icon: 'bi-file-earmark-check' },
]);

const universityFields = computed(() => !lead.value ? [] : [
    { label: 'Course Level', value: lead.value.courselevel, field: 'courselevel', icon: 'bi-check-circle', type: 'select', options: formOptions.value.course_levels },
    { label: 'Country', value: lead.value.country, field: 'country', icon: 'bi-globe', type: 'select', options: formOptions.value.countries },
    { label: 'Location', value: lead.value.location, field: 'location', icon: 'bi-geo-alt', type: 'select', options: formOptions.value.locations },
    { label: 'University', value: lead.value.university, field: 'university', icon: 'bi-building-fill', type: 'select', options: formOptions.value.universities },
    { label: 'Course', value: lead.value.course, field: 'course', icon: 'bi-book', type: 'select', options: formOptions.value.courses },
    { label: 'Intake', value: lead.value.intake, field: 'intake', icon: 'bi-calendar-event', type: 'select', options: formOptions.value.intakes },
]);

const testScoreFields = computed(() => !lead.value ? [] : [
    { label: 'English Test', value: lead.value.englishTest, field: 'englishTest', icon: 'bi-patch-check' },
    { label: 'Overall Score', value: lead.value.score, field: 'score', icon: 'bi-stars' },
    { label: 'English Score', value: lead.value.englishscore, field: 'englishscore', icon: 'bi-translate' },
    { label: 'English Theory', value: lead.value.englishtheory, field: 'englishtheory', icon: 'bi-journal-text' },
]);

const systemFields = computed(() => !lead.value ? [] : [
    { label: 'Created By', value: lead.value.creator?.name, field: 'created_by', icon: 'bi-person-badge', type: 'static' },
]);

const displayStatus = computed(() => {
    if (!lead.value) {
        return { text: 'N/A', badgeClass: 'bg-secondary' };
    }

    let statusText = lead.value.status || 'N/A';
    let badgeClass = 'bg-primary-subtle text-primary'; // Default class

    if (lead.value.is_forwarded && lead.value.applications && lead.value.applications.length > 0) {
        const latestApplication = lead.value.applications.reduce((latest, current) => {
            return new Date(current.created_at) > new Date(latest.created_at) ? current : latest;
        });
        statusText = latestApplication.status;
    }

    switch (statusText) {
        case 'Visa Granted':
            badgeClass = 'bg-success-subtle text-success';
            break;
        case 'Dropped':
            badgeClass = 'bg-danger-subtle text-danger';
            break;
        case 'Offer Letter Sent':
            badgeClass = 'bg-info-subtle text-info';
            break;
    }

    return { text: statusText, badgeClass };
});

onMounted(fetchLeadData);

watch(() => lead.value?.country, (newVal, oldVal) => {
    if (newVal !== oldVal && newVal) {
        formOptions.value.locations = [];
        formOptions.value.universities = [];
        formOptions.value.courses = [];
        formOptions.value.intakes = [];
        axios.get(`/api/data-entries/get-locations-by-country?country=${newVal}`)
            .then(res => formOptions.value.locations = res.data.data);
    }
});

watch(() => lead.value?.location, (newVal, oldVal) => {
    if (newVal !== oldVal && newVal) {
        formOptions.value.universities = [];
        formOptions.value.courses = [];
        formOptions.value.intakes = [];
        axios.get(`/api/data-entries/get-universities-by-location?location=${newVal}`)
            .then(res => formOptions.value.universities = res.data.data);
    }
});

watch(() => [lead.value?.university, lead.value?.courselevel], ([newUni, newLevel], [oldUni, oldLevel]) => {
    if ((newUni !== oldUni || newLevel !== oldLevel) && newUni && lead.value.location) {
        formOptions.value.courses = [];
        formOptions.value.intakes = [];
        const params = new URLSearchParams({ university: newUni, location: lead.value.location });
        if (newLevel) params.append('level', newLevel);
        axios.get(`/api/data-entries/get-courses-by-university?${params.toString()}`)
            .then(res => formOptions.value.courses = res.data.data);
    }
});

watch(() => lead.value?.course, (newVal, oldVal) => {
    if (newVal !== oldVal && newVal && lead.value.university && lead.value.location) {
        formOptions.value.intakes = [];
        axios.get(`/api/data-entries/get-intakes-by-course?university=${lead.value.university}&location=${lead.value.location}&course=${newVal}`)
            .then(res => formOptions.value.intakes = res.data.data);
    }
});

function openDatePicker() {
    flatpickrRef.value?.fp.open();
}

function clearFollowUp() {
    followUpDateTime.value = null;
}

function formatSelectedDate(dateString) {
    if (!dateString || !flatpickrRef.value?.fp) return '';
    return flatpickrRef.value.fp.formatDate(new Date(dateString), "M j, h:i K");
}

function handleSubTabClick(tab) {
    if (tab.id === 'forward') {
        showForwardModal.value = true;
    } else {
        activeSubTab.value = tab.id;
    }
}

async function fetchLeadData() {
    isLoading.value = true;
    try {
        const response = await axios.get(`/api/leads/${props.leadId}`);
        if (response.data?.success) {
            const data = response.data.data;
            lead.value = data.lead;
            users.value = data.users;
            documentStatuses.value = data.document_statuses;
            formOptions.value = data.form_options;
        } else {
            throw new Error('API response was not successful.');
        }
    } catch (error) {
        console.error("Error fetching lead data:", error);
        toast.error('Could not load lead data.'); // Using toast
        lead.value = null;
    } finally {
        isLoading.value = false;
    }
}

async function handleFieldUpdate(field, value) {
    try {
        const oldValue = lead.value[field];
        const response = await axios.put(`/api/leads/${props.leadId}/update-field`, { field, value });
        
        lead.value[field] = value;
        toast.success('Field updated!'); // Using toast

        if (response.data?.activities) {
            lead.value.activities = response.data.activities;
        }

        const resetMap = {
            country: { location: null, university: null, course: null, intake: null },
            location: { university: null, course: null, intake: null },
            university: { course: null, intake: null },
            courselevel: { course: null, intake: null },
            course: { intake: null }
        };

        if (resetMap[field] && oldValue !== value) {
            const fieldsToReset = resetMap[field];
            const resetPromises = Object.entries(fieldsToReset).map(([key, resetValue]) => {
                if (lead.value[key] !== null) {
                    lead.value[key] = resetValue;
                    return axios.put(`/api/leads/${props.leadId}/update-field`, { field: key, value: resetValue });
                }
            }).filter(Boolean);
            await Promise.all(resetPromises);
            await fetchLeadData();
        }

    } catch (error) {
        console.error("Error updating field:", error);
        toast.error('Failed to update field.'); // Using toast
        await fetchLeadData();
    }
}

async function handleStatusUpdate(newStatus) {
    await handleFieldUpdate('status', newStatus);
}

async function submitComment() {
    if (!newCommentText.value.trim()) {
        toast.warning('Please enter a comment before posting.'); // Using toast
        return;
    }
    isSubmittingComment.value = true;
    try {
        const payload = {
            comment: newCommentText.value,
            date_time: followUpDateTime.value
        };
        const response = await axios.post(`/api/leads/${props.leadId}/comments`, payload);
        if (response.data?.success) {
            lead.value.lead_comments.unshift(response.data.comment);
            if (response.data.activities) {
                lead.value.activities = response.data.activities;
            }
            newCommentText.value = '';
            clearFollowUp();
            toast.success('Comment posted successfully.'); // Using toast
        } else {
            throw new Error(response.data.message || 'Failed to post comment.');
        }
    } catch (error) {
        console.error("Error submitting comment:", error);
        toast.error(error.message || 'An unknown error occurred while posting.'); // Using toast
    } finally {
        isSubmittingComment.value = false;
    }
}

async function submitForwarding() {
    const { isConfirmed } = await Swal.fire({
        title: 'Confirm Submission',
        text: 'Are you sure you want to forward this application?',
        icon: 'question',
        showCancelButton: true
    });
    if (!isConfirmed) return;
    isForwarding.value = true;
    try {
        await axios.post(`/api/leads/${props.leadId}/forward`, forwarding.value);
        toast.success('Application has been forwarded.'); // Using toast
        showForwardModal.value = false;
        forwarding.value = { userId: null, notes: '', sendEmail: true };
        await fetchLeadData();
    } catch (error) {
        console.error("Error forwarding application:", error);
        toast.error(error.response?.data?.message || 'Failed to forward application.'); // Using toast
    } finally {
        isForwarding.value = false;
    }
}

function getAvatarUrl(item) {
    const avatar = item?.avatar || item?.created_by?.avatar;
    return avatar ? `/storage/avatars/${avatar}` : '/assets/images/profile/user-1.jpg';
}

function formatTimeAgo(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const secondsPast = (now.getTime() - date.getTime()) / 1000;
    if (secondsPast < 60) return `${Math.floor(secondsPast)}s ago`;
    if (secondsPast < 3600) return `${Math.floor(secondsPast / 60)}m ago`;
    if (secondsPast < 86400) return `${Math.floor(secondsPast / 3600)}h ago`;
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

<style scoped>
/* Styles for the new top-level nav pills */
.nav-pills-1 .nav-link {
    background-color: transparent;
    border: 2px solid #dee2e6;
    color: #495057;
    padding: 4px 15px;
    border-radius: 9999px;
    /* For pill shape */
    transition: all 0.2s ease-in-out;
}

.nav-pills-1 .nav-link.active,
.nav-pills-1 .show>.nav-link {
    color: #1b5e20;
    border-color: #1b5e20;
    font-weight: 500;
}

/* Styles for the sub-tabs */
.user-profile-tab .nav-link {
    border-radius: 0px !important;
    border: none;
    background-color: transparent;
    color: #6c757d;
}

.user-profile-tab .nav-link-1.active {
    color: #0d6efd;
    background-color: transparent;
    border-bottom: 2px solid #0d6efd;
}


.visually-hidden-input {
    position: absolute;
    width: 1px;
    height: 1px;
    margin: -1px;
    padding: 0;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

.modal {
    z-index: 1050;
}

.modal-backdrop {
    z-index: 1040;
}
</style>