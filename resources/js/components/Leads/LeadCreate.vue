<template>
    <div v-if="isLoading" class="text-center p-5"><div class="spinner-border text-success"></div></div>
    
    <div v-else class="rounded-md mx-auto mb-4 overflow-hidden">
        <form @submit.prevent="submitForm" novalidate>
            <div class="d-flex flex-column flex-lg-row gap-4">
                <!-- Main Form Container (Unchanged) -->
                <div class="form-container">
                    <h1 class="mb-2 text-[#2e7d32]">Create New Lead</h1>
                    <div class="d-flex align-items-center justify-content-end mb-4 gap-2">
                        <label class="form-label mb-0">{{ isRawMode ? 'Raw Submit Mode' : 'Full Submit Mode' }}</label>
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="isRawMode" style="width: 3em; height: 1.5em;"></div>
                    </div>

                    <!-- Progress Bar -->
                    <div v-if="!isRawMode" class="progress-container"><div class="progress-bar1"><div v-for="step in totalSteps" :key="step" class="progress-step" :class="getStepClass(step)">{{ step }}<span class="step-label">{{ stepLabels[step - 1] }}</span></div></div></div>

                    <!-- Personal Information -->
                    <div v-show="isStepActive(1)" class="form-section">
                        <div class="section-title">Personal Information</div>
                        <div class="form-row"><div class="form-group"><label for="name">Name <span class="text-danger">*</span></label><input type="text" id="name" class="form-control" v-model="form.name" required><div v-if="errors.name" class="error-message">{{ errors.name[0] }}</div></div><div class="form-group"><label for="email">Email</label><input type="email" id="email" class="form-control" v-model="form.email"><div v-if="errors.email" class="error-message">{{ errors.email[0] }}</div></div></div>
                        <div class="form-row"><div class="form-group"><label for="phone">Phone <span class="text-danger">*</span></label><input type="tel" id="phone" class="form-control" v-model="form.phone" required><div v-if="errors.phone" class="error-message">{{ errors.phone[0] }}</div></div><div class="form-group"><label for="locations">Address</label><input type="text" id="locations" class="form-control" v-model="form.locations"></div></div>
                        <div class="form-row"><div class="form-group"><label for="link">Link</label><input type="text" id="link" class="form-control" v-model="form.link"></div></div>
                    </div>
                    
                    <!-- Study Information -->
                    <div v-show="isStepActive(2)" class="form-section">
                        <div class="section-title">Study Information</div>
                         <div class="form-row mt-4"><div class="form-group"><label for="lastqualification">Level</label><select id="lastqualification" v-model="form.lastqualification" class="form-select"><option value="" disabled>Select Last Level</option><option>Intermediate/Diploma</option><option>Bachelor</option><option>Masters</option></select></div><div class="form-group"><label for="courselevel">Course Name</label><input type="text" id="courselevel" v-model="form.courselevel" class="form-control"></div></div>
                        <div class="form-row"><div class="form-group"><label for="passed">Pass Year</label><select id="passed" v-model="form.passed" class="form-select"><option value="" disabled>Select Year</option><option v-for="year in yearOptions" :key="year" :value="year">{{ year }}</option></select></div><div class="form-group"><label for="gpa">GPA / Percentage</label><input type="text" id="gpa" v-model="form.gpa" class="form-control"></div></div>
                    </div>
                    
                    <!-- Test Information -->
                    <div v-show="isStepActive(3)" class="form-section">
                        <div class="section-title">Test Information</div>
                        <div class="form-group"><label for="englishTest">English Language Test</label><select id="englishTest" v-model="form.englishTest" class="form-select"><option value="" disabled>Select Test</option><option>IELTS</option><option>PTE</option><option>ELLT</option><option>No Test</option><option>Duolingo</option><option>MOI</option><option>Other</option></select></div>
                        <div v-if="isStandardTest" class="form-row"><div class="form-group"><label for="higher">Overall Higher</label><input type="text" id="higher" v-model="form.higher" class="form-control"></div><div class="form-group"><label for="less">Not Less than</label><input type="text" id="less" v-model="form.less" class="form-control"></div><div class="form-group"><label for="score">Overall Score</label><input type="text" id="score" :value="calculatedScore" class="form-control" disabled></div></div>
                        <div v-if="form.englishTest === 'No Test'" class="form-row"><div class="form-group"><label for="englishscore">English Score</label><input type="text" id="englishscore" v-model="form.englishscore" class="form-control"></div><div class="form-group"><label for="englishtheory">English Theory</label><input type="text" id="englishtheory" v-model="form.englishtheory" class="form-control"></div></div>
                        <div v-if="form.englishTest === 'Other'" class="form-row"><div class="form-group"><label for="otherScore">Other Test Score</label><input type="text" id="otherScore" v-model="form.otherScore" class="form-control"></div></div>
                    </div>
                    
                    <!-- Source Information -->
                    <div v-show="isStepActive(4)" class="form-section">
                        <div class="section-title">Source Information</div>
                         <div class="form-row"><div class="form-group"><label for="created_by">Source User</label><select id="created_by" v-model="form.created_by" class="form-select"><option disabled :value="null">Select User</option><option v-for="user in dropdownData.users" :key="user.id" :value="user.id">{{ user.name }} {{ user.last }}</option></select></div><div class="form-group"><label for="source">Source of Referral</label><select id="source" v-model="form.source" class="form-select"><option value="" disabled>Select Source</option><option value="facebook">Facebook</option><option value="whatsapp">WhatsApp</option><option value="instagram">Instagram</option><option value="partners">Partners</option><option value="other">Other</option></select></div></div>
                        <div v-if="form.source === 'partners'" class="form-row"><div class="form-group"><label for="partnerDetails">Partner Details</label><input type="text" id="partnerDetails" v-model="form.partnerDetails" class="form-control"></div></div>
                        <div v-if="form.source === 'other'" class="form-row"><div class="form-group"><label for="otherDetails">Other Details</label><input type="text" id="otherDetails" v-model="form.otherDetails" class="form-control"></div></div>
                    </div>

                    <!-- University Information -->
                    <div v-show="isStepActive(5)" class="form-section">
                        <div class="section-title">University Information</div>
                        <div class="form-row"><div class="form-group"><label>Country</label><select v-model="form.country" class="form-select"><option value="" disabled>Select Country</option><option v-for="c in dropdownData.countries" :key="c" :value="c">{{ c }}</option></select></div><div class="form-group"><label>Location</label><select v-model="form.location" @change="fetchUniversities" class="form-select" :disabled="!form.country"><option value="" disabled>Select Location</option><option v-for="l in dropdownData.locations" :key="l" :value="l">{{ l }}</option></select></div></div>
                        <div v-for="(uni, index) in form.universities" :key="index" class="form-row border-top pt-3">
                            <div class="form-group"><label>University</label><select v-model="uni.university" @change="fetchCourses(index)" class="form-select" :disabled="!form.location"><option value="" disabled>Select University</option><option v-for="u in dropdownData.universities" :key="u" :value="u">{{ u }}</option></select></div>
                            <div class="form-group"><label>Course</label><select v-model="uni.course" @change="fetchIntakes(index)" class="form-select" :disabled="!uni.university"><option value="" disabled>Select Course</option><option v-for="c in dropdownData.courses[index]" :key="c" :value="c">{{ c }}</option></select></div>
                            <div class="form-group"><label>Intake</label><select v-model="uni.intake" class="form-select" :disabled="!uni.course"><option value="" disabled>Select Intake</option><option v-for="i in dropdownData.intakes[index]" :key="i" :value="i">{{ i }}</option></select></div>
                        </div>
                    </div>
                    
                    <!-- Document Information -->
                    <div v-show="isStepActive(6)" class="form-section">
                        <div class="section-title">Document Information</div>
                        <div class="flex-container"><div class="flex-section"><h3>Available Documents</h3><div class="document-list"><div v-for="doc in availableDocuments" :key="doc" class="document-item" @click="toggleDocument(doc)"><input type="checkbox" :checked="form.academic.includes(doc)" class="document-checkbox"> {{ doc }}</div><div v-if="!availableDocuments.length" class="document-message">Select a university to see required documents.</div></div></div><div class="flex-section"><h3>Selected Documents</h3><div class="document-list"><div v-for="doc in form.academic" :key="doc" class="document-item" @click="toggleDocument(doc)"><input type="checkbox" checked class="document-checkbox"> {{ doc }}</div><div v-if="!form.academic.length" class="document-message">No documents selected.</div></div></div></div>
                    </div>

                    <!-- Navigation/Submit Buttons -->
                    <div v-if="!isRawMode" class="button-group"><button type="button" class="prev" @click="prevStep" v-show="currentStep > 1">Previous</button><button type="button" class="next bg-[#2e7d32]" @click="nextStep" v-show="currentStep < totalSteps">Next</button><button type="submit" class="btn-submit" v-show="currentStep === totalSteps" :disabled="isSaving">{{ isSaving ? 'Submitting...' : 'Submit' }}</button></div>
                    <div v-if="isRawMode" class="text-center"><button type="submit" class="btn-submit" :disabled="isSaving">{{ isSaving ? 'Submitting...' : 'Submit Raw Data' }}</button></div>
                </div>

                <!-- MODIFIED Preview Container -->
                <div v-if="!isRawMode" class="preview-container" ref="previewElement">
                    <div class="form-preview">
                        <!-- Personal Information -->
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold">Personal Information</p>
                        <div class="section">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Name</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.name}">{{ form.name || 'Not filled' }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.email}">{{ form.email || 'Not filled' }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Phone</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.phone}">{{ form.phone || 'Not filled' }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Address</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.locations}">{{ form.locations || 'Not filled' }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Link</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.link}">{{ form.link || 'Not filled' }}</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Study Information -->
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold">Study Information</p>
                        <div class="section">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Level</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.lastqualification}">{{ form.lastqualification || 'Not filled' }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Course Name</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.courselevel}">{{ form.courselevel || 'Not filled' }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Pass Year</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.passed}">{{ form.passed || 'Not filled' }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">GPA/Percentage</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.gpa}">{{ form.gpa || 'Not filled' }}</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Information -->
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold">Test Information</p>
                        <div class="section">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Selected Test:</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.englishTest}">{{ form.englishTest || 'Not filled' }}</span></div>
                                </div>
                                <div v-if="isStandardTest" class="info-item">
                                    <div class="info-label">Overall Score:</div>
                                    <div class="info-value"><span :class="{'not-filled': !calculatedScore}">{{ calculatedScore || 'Not filled' }}</span></div>
                                </div>
                                <template v-if="form.englishTest === 'No Test'">
                                    <div class="info-item">
                                        <div class="info-label">English Score:</div>
                                        <div class="info-value"><span :class="{'not-filled': !form.englishscore}">{{ form.englishscore || 'Not filled' }}</span></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">English Theory:</div>
                                        <div class="info-value"><span :class="{'not-filled': !form.englishtheory}">{{ form.englishtheory || 'Not filled' }}</span></div>
                                    </div>
                                </template>
                                <div v-if="form.englishTest === 'Other'" class="info-item">
                                    <div class="info-label">Other Test Score:</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.otherScore}">{{ form.otherScore || 'Not filled' }}</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Document Information -->
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold">Document Information</p>
                        <div class="section">
                            <div class="info-item">
                                <div class="info-label">Documents received:</div>
                                <div class="info-value"><span :class="{'not-filled': form.academic.length === 0}">{{ form.academic.join(', ') || 'Not filled' }}</span></div>
                            </div>
                        </div>

                        <!-- University Information -->
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold">University Information</p>
                        <div class="section">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Country:</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.country}">{{ form.country || 'Not filled' }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Location:</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.location}">{{ form.location || 'Not filled' }}</span></div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div v-if="form.universities.every(u => !u.university && !u.course && !u.intake)">
                                    <p class="text-sm text-gray-500 italic">No university preferences added.</p>
                                </div>
                                <div v-else v-for="(uni, index) in form.universities" :key="index" class="university-entry border p-3 rounded bg-gray-50 mb-2 text-sm">
                                    <p class="mb-1"><strong class="text-gray-700">University {{ index + 1 }}:</strong> <span class="text-gray-600">{{ uni.university || 'Not filled' }}</span></p>
                                    <p class="mb-1"><strong class="text-gray-700">Course:</strong> <span class="text-gray-600">{{ uni.course || 'Not filled' }}</span></p>
                                    <p><strong class="text-gray-700">Intake:</strong> <span class="text-gray-600">{{ uni.intake || 'Not filled' }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Source Information -->
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold">Source Information</p>
                        <div class="section">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Source</div>
                                    <div class="info-value"><span :class="{'not-filled': !sourceUserName || sourceUserName === 'Not filled'}">{{ sourceUserName }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Source of Referral</div>
                                    <div class="info-value"><span :class="{'not-filled': !form.source}">{{ form.source || 'Not filled' }}</span></div>
                                </div>
                            </div>
                        </div>

                        <button @click.prevent="downloadPdf" class="bg-[#2e7d32] text-white p-2 w-full mt-4" :disabled="isSaving">Download</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import html2pdf from 'html2pdf.js';

const router = useRouter();
const isLoading = ref(true);
const isSaving = ref(false);
const isRawMode = ref(false);
const currentStep = ref(1);
const totalSteps = 6;
const stepLabels = ["Personal", "Study", "Test", "Source", "University", "Documents"];
const errors = ref({});
const previewElement = ref(null);

const form = reactive({
    name: '', email: '', phone: '', locations: '', link: '',
    lastqualification: '', courselevel: '', passed: '', gpa: '',
    englishTest: '', higher: '', less: '', score: '', englishscore: '', englishtheory: '', otherScore: '',
    created_by: null, source: '', partnerDetails: '', otherDetails: '',
    country: '', location: '', universities: [{ university: '', course: '', intake: '' }],
    academic: [],
    sources: '0',
});

const dropdownData = reactive({
    users: [], countries: [], locations: [], universities: [],
    courses: [[]], intakes: [[]], documents: [],
});

const yearOptions = computed(() => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let i = currentYear; i >= 1980; i--) {
        years.push(i);
    }
    return years;
});
const calculatedScore = computed(() => {
    form.score = (form.higher && form.less) ? `${form.higher}/${form.less}` : '';
    return form.score;
});
const isStandardTest = computed(() => ['IELTS', 'PTE', 'ELLT', 'Duolingo'].includes(form.englishTest));
const availableDocuments = computed(() => dropdownData.documents);
const sourceUserName = computed(() => {
    if (!form.created_by) return 'Not filled';
    const user = dropdownData.users.find(u => u.id === form.created_by);
    return user ? `${user.name} ${user.last}` : 'Not filled';
});

onMounted(async () => {
    try {
        const response = await axios.get('/api/leads/create-data');
        if (response.data.success) {
            dropdownData.users = response.data.data.users;
            dropdownData.countries = response.data.data.countries;
        }
    } catch (error) {
        Swal.fire('Error', 'Could not load initial form data.', 'error');
    } finally {
        isLoading.value = false;
    }
});

watch(isRawMode, (newValue) => {
    form.sources = newValue ? '1' : '0';
    if (!newValue) currentStep.value = 1;
});
watch(() => form.country, (newCountry) => {
    form.location = '';
    dropdownData.locations = [];
    if (newCountry) fetchLocations(newCountry);
});
watch(() => form.location, (newLocation) => {
    if (!newLocation) {
        return; 
    }

    form.universities = [{ university: '', course: '', intake: '' }];
    dropdownData.universities = [];
    fetchUniversities(newLocation);
});

const getStepClass = (step) => ({ 'completed': step < currentStep.value, 'active': step === currentStep.value });
const isStepActive = (step) => isRawMode.value ? (step === 1) : (step === currentStep.value);
const nextStep = () => { if (currentStep.value < totalSteps) currentStep.value++; };
const prevStep = () => { if (currentStep.value > 1) currentStep.value--; };


const fetchLocations = async (country) => {
    try {
        const { data } = await axios.get(`/get-locations-by-country?country=${country}`);
        // Check for `status === 'success'` and then assign `data.data`
        if (data && data.status === 'success') {
            dropdownData.locations = data.data || [];
        } else {
            dropdownData.locations = [];
        }
    } catch (error) {
        console.error("Error fetching locations:", error);
        dropdownData.locations = [];
    }
};

const fetchUniversities = async (location) => {
    try {
        const { data } = await axios.get(`/get-universities-by-location?location=${location}`);
        if (data && data.status === 'success') {
            dropdownData.universities = data.data || [];
        } else {
            dropdownData.universities = [];
        }
    } catch (error) {
        console.error("Error fetching universities:", error);
        dropdownData.universities = [];
    }
};

const fetchCourses = async (index) => {
    const uni = form.universities[index];
    try {
        const { data } = await axios.get(`/get-courses-by-university?university=${uni.university}&location=${form.location}`);
        if (data && data.status === 'success') {
            dropdownData.courses[index] = data.data || [];
        } else {
            dropdownData.courses[index] = [];
        }
        fetchRequiredDocuments(uni.university);
    } catch (error) {
        console.error("Error fetching courses:", error);
        dropdownData.courses[index] = [];
    }
};

const fetchIntakes = async (index) => {
    const uni = form.universities[index];
    try {
        const { data } = await axios.get(`/get-intakes-by-course?course=${uni.course}&university=${uni.university}&location=${form.location}`);
        if (data && data.status === 'success') {
            dropdownData.intakes[index] = data.data || [];
        } else {
            dropdownData.intakes[index] = [];
        }
    } catch (error) {
        console.error("Error fetching intakes:", error);
        dropdownData.intakes[index] = [];
    }
};

const fetchRequiredDocuments = async (university) => {
    try {
        const { data } = await axios.get(`/get-required-documents?university=${university}&country=${form.country}`);
        if (data && data.status === 'success') {
            dropdownData.documents = data.data || [];
        } else {
            dropdownData.documents = [];
        }
    } catch (error) {
        console.error("Error fetching documents:", error);
        dropdownData.documents = [];
    }
};
const toggleDocument = (doc) => {
    const index = form.academic.indexOf(doc);
    if (index === -1) form.academic.push(doc);
    else form.academic.splice(index, 1);
};

const downloadPdf = () => {
    const element = previewElement.value;
    const opt = { margin: 0.5, filename: `${form.name || 'lead'}_preview.pdf`, image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' } };
    html2pdf().from(element).set(opt).save();
};

const submitForm = async () => {
    isSaving.value = true;
    errors.value = {};
    
    // Flatten university data for submission
    let payload = { ...form };
    if (!isRawMode.value) {
        payload.university = form.universities.map(u => u.university);
        payload.course = form.universities.map(u => u.course);
        payload.intake = form.universities.map(u => u.intake);
        payload.academic = form.academic.join(',');
    } else {
        // For raw mode, only send basic info
        payload = {
            name: form.name,
            phone: form.phone,
            email: form.email,
            locations: form.locations,
            link: form.link,
            created_by: form.created_by,
            sources: '1',
        };
    }
    delete payload.universities;

    try {
        const response = await axios.post('/api/leads', payload);
        Swal.fire('Success!', 'Lead created successfully!', 'success');
        router.push({ name: 'leads.record', params: { leadId: response.data.lead.id } });
    } catch (error) {
        const message = error.response?.data?.message || 'An unknown error occurred.';
        if(error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
        Swal.fire('Error', message, 'error');
    } finally {
        isSaving.value = false;
    }
};
</script>


<style scoped>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Georgia', serif;
    background-color: white;
    padding: 40px 20px;
    color: #2c3e50;
}

.container {
    margin: 0 auto;
}

form {
    background-color: #ffffff;
    border-radius: 12px;
}

h1 {
    font-size: 28px;
    color: #2c3e50;
    text-align: center;
    position: relative;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.form-container {
    font-family: Arial, sans-serif;
    max-width: 1600px;
    margin: 0 auto;
    border-radius: 8px;
}

.flex-section {
    flex: 1;
    min-width: 250px;
}

.dropdown-container {
    position: relative;
    margin-bottom: 20px;
}

.dropdown-input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    background-color: #fff;
    cursor: pointer;
}

.dropdown-input:focus {
    outline: none;
    border-color: #60a5fa;
}

.dropdown-list {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background-color: #ffffff;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

.dropdown-list.show {
    display: block;
}

.dropdown-item {
    padding: 8px 12px;
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: #f3f4f6;
}

.dropdown-item input {
    margin-right: 8px;
}

.document-list {
    background-color: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 12px;
    margin-top: 8px;
    height: calc(100vh - 200px);
    overflow-y: auto;
}

.document-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    border-radius: 4px;
    margin-bottom: 6px;
}

.selected-document {
    background-color: #f0fdf4;
}

.unselected-document {
    background-color: #fef2f2;
}

.document-item .icon {
    font-size: 18px;
}

.selected-document .icon {
    color: #22c55e;
}

.unselected-document .icon {
    color: #ef4444;
}

.document-item button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px 8px;
    border-radius: 4px;
}

.selected-document button {
    color: #ef4444;
}

.unselected-document button {
    color: #22c55e;
}

h3,
label {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-bottom: 6px;
}

@media (max-width: 768px) {
    .flex-section {
        flex: 1 1 100%;
    }
}

.section-title {
    font-size: 24px;
    color: #2e7d32;
    margin: 35px 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #81c784;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.form-row {
    display: flex;
    gap: 25px;
    margin-bottom: 25px;
}

.form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.form-group label {
    display: block;
    font-size: 16px;
    color: #2e7d32;
    margin-bottom: 10px;
    font-weight: 600;
    letter-spacing: 0.2px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 14px;
    font-size: 15px;
    color: #2c3e50;
    border-radius: 6px;
    border: 1.5px solid #81c784;
    transition: all 0.3s ease;
    font-family: 'Georgia', serif;
    background-color: #fcfcfc;
}

.form-group textarea {
    width: 490px;
    min-height: 120px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #2e7d32;
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
}

.education-table {
    width: 100%;
    border-collapse: collapse;
    margin: 25px 0;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.education-table th,
.education-table td {
    border: 1px solid #e8f5e9;
    padding: 15px 20px;
    text-align: left;
    font-size: 15px;
}

.education-table th {
    background-color: #2e7d32;
    color: #ffffff;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 0.8px;
}

.education-table tr:hover {
    background-color: #f1f8e9;
}

.education-table td input {
    width: 100%;
    padding: 12px 15px;
    border: 1.5px solid #81c784;
    border-radius: 4px;
    font-size: 15px;
    font-family: 'Georgia', serif;
}

.education-table td input:focus {
    outline: none;
    border-color: #2e7d32;
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
}

.btn-submit {
    background-color: #2e7d32;
    color: #fff;
    border: none;
    padding: 16px 35px;
    font-size: 18px;
    border-radius: 6px;
    cursor: pointer;
    display: block;
    margin: 40px auto 0;
    min-width: 220px;
    transition: all 0.3s ease;
    font-weight: 600;
    letter-spacing: 0.5px;
    font-family: 'Georgia', serif;
}

.btn-submit:hover {
    background-color: #1b5e20;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
}

.btn-submit:active {
    transform: translateY(0);
}

.popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #ffffff;
    padding: 25px 30px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    text-align: center;
    min-width: 300px;
}

.popup-content {
    margin-bottom: 20px;
}

.popup h2 {
    color: #2e7d32;
    margin-bottom: 15px;
    font-size: 24px;
}

.popup p {
    color: #2c3e50;
    font-size: 16px;
    margin-bottom: 20px;
}

.popup-btn {
    background-color: #2e7d32;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-family: 'Georgia', serif;
}

.popup-btn:hover {
    background-color: #1b5e20;
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

.error {
    border-color: red;
}

.error-message {
    color: red;
    font-size: 0.8em;
    margin-top: 4px;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 20px;
    }

    form {
        padding: 25px;
    }

    .education-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    h1 {
        font-size: 28px;
    }

    .section-title {
        font-size: 22px;
    }
}

.form-container {
    flex: 1;
    background: white;
    padding: 20px;
    border-radius: 8px;
}

.preview-container {
    flex: 1;
    background: white;
    padding: 20px;
    border-radius: 8px;
    background-color: #d1d5db;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.button-group {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

button {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    background-color: #007bff;
    color: white;
    font-weight: bold;
}

button:hover {
    background-color: #0056b3;
}

button.prev {
    background-color: #6c757d;
}


.preview-section {
    margin-bottom: 20px;
}

.preview-section h3 {
    color: #007bff;
    margin-bottom: 10px;
}

.preview-data {
    margin-left: 10px;
}

.preview-data p {
    margin: 5px 0;
    color: #666;
}

.preview-data span {
    font-weight: bold;
    color: #333;
}

.progress-bar1 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 30px;
    margin: 0 auto;
}

.progress-bar1::before {
    content: '';
    background-color: #ddd;
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    height: 4px;
    width: 100%;
    z-index: 0;
}

.progress-step {
    background-color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 3px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #999;
    position: relative;
    z-index: 1;
}

.progress-step.active {
    border-color: #007bff;
    color: #007bff;
}

.progress-step.completed {
    border-color: #28a745;
    background-color: #28a745;
    color: white;
}

.step-label {
    position: absolute;
    top: 40px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    font-size: 12px;
    color: #666;
}

.progress-container {
    margin-bottom: 100px;
    margin-left: 20px;
    margin-right: 20px;
}

.university-entry {
    padding: 10px;
    border-radius: 5px;
}

.title {
    color: #2e7d32;
    margin-bottom: 1.5rem;
    text-align: center;
    font-size: 2rem;
    top: 0;
}

.form-preview {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.preview-container {
    height: 100vh;
    max-height: 100vh;
    overflow-y: auto;
}

.section {
    padding-bottom: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #81c784;
}

.section:last-child {
    margin-bottom: 0;
    border-bottom: none;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.info-item {
    padding: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: #000000;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.info-value {
    color: #2c3e50;
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 4px;
    min-height: 2.5rem;
    display: flex;
    align-items: center;
}

.not-filled {
    color: #95a5a6;
    font-style: italic;
}

@media (max-width: 600px) {
    .info-grid {
        grid-template-columns: 1fr;
    }

    body {
        padding: 1rem;
    }
}

.preview-container::-webkit-scrollbar {
    width: 8px;
}

.preview-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.preview-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.preview-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.flex-container {
    display: flex;
}

.document-list {
    min-height: 100px;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 0.75rem;
    background-color: #f8fafc;
    margin-bottom: 1rem;
}

.document-item {
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    background-color: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.25rem;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    cursor: pointer;
}

.document-item:hover {
    background-color: #f1f5f9;
}

.document-checkbox {
    margin-right: 8px;
}

.document-error {
    color: #ef4444;
    padding: 0.5rem;
    font-size: 0.875rem;
}

.document-message {
    color: #6b7280;
    padding: 0.5rem;
    font-size: 0.875rem;
    font-style: italic;
}

#selectedDocuments .document-item {
    background-color: #dcfce7;
    border-color: #86efac;
}

#selectedDocuments .document-item:hover {
    background-color: #bbf7d0;
}

.dropdown-container {
    position: relative;
    margin-bottom: 1.5rem;
}

.dropdown-input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    background-color: #ffffff;
    cursor: pointer;
}

.dropdown-list {
    position: absolute;
    width: 100%;
    max-height: 250px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    background-color: white;
    z-index: 10;
    display: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.dropdown-item {
    padding: 0.5rem;
    display: flex;
    align-items: center;
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: #f1f5f9;
}

.dropdown-item label {
    margin-left: 8px;
    cursor: pointer;
    flex-grow: 1;
}

.dropdown-message {
    padding: 0.5rem;
    color: #6b7280;
    font-style: italic;
}

.flex-section {
    flex: 1;
    min-width: 250px;
}

.toggle-checkbox:checked {
    right: 0;
    border-color: #2e7d32;
}

.toggle-checkbox:checked+.toggle-label {
    background-color: #81c784;
}

.toggle-checkbox {
    transition: right 0.2s ease-in-out;
}
</style>