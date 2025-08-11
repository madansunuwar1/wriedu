<template>
  <div class="col-lg-12 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body pb-0">
        <h4 class="card-title">Application Form</h4>
        <p class="card-subtitle mb-3">Fill out the form to create a new application.</p>
      </div>

      <!-- We use @submit.prevent to stop the default browser form submission -->
      <form @submit.prevent="submitForm" class="needs-validation" :class="{ 'was-validated': wasValidated }" novalidate>
        
        <!-- Personal Details -->
        <div class="card-body border-top">
          <h5 class="mb-3">Personal Details</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" v-model="form.name" placeholder="Enter your name" required>
                <div class="invalid-feedback">Please provide your name.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" v-model="form.email" placeholder="Enter your email" required>
                <div class="invalid-feedback">Please provide a valid email.</div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" v-model="form.phone" placeholder="Enter your phone number" required>
                <div class="invalid-feedback">Please provide a valid phone number.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Academic Details -->
        <div class="card-body border-top">
          <h5 class="mb-3">Academic Details</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="lastqualification" class="form-label">Level</label>
                <select class="form-select" id="lastqualification" v-model="form.lastqualification" required>
                  <option value="" disabled selected>Select Last Level</option>
                  <option value="Intermediate/Diploma">Intermediate/Diploma</option>
                  <option value="Bachelor">Bachelor</option>
                  <option value="Masters">Masters</option>
                </select>
                <div class="invalid-feedback">Please select your education level.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="passed" class="form-label">Pass Year</label>
                <select class="form-select" id="passed" v-model="form.passed" required>
                  <option value="" disabled selected>Select Passed Year</option>
                  <option v-for="year in passYears" :key="year" :value="year">{{ year }}</option>
                </select>
                <div class="invalid-feedback">Please select your passing year.</div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="gpa" class="form-label">GPA / Percentage</label>
                <input type="text" class="form-control" id="gpa" v-model="form.gpa" placeholder="GPA / Percentage" required>
                <div class="invalid-feedback">Please provide your GPA or percentage.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="english" class="form-label">Product</label>
                <select class="form-select" id="english" v-model="form.english" required>
                  <option value="" disabled selected>Select Product</option>
                  <option v-for="product in products" :key="product.id" :value="product.id">{{ product.product }}</option>
                </select>
                <div class="invalid-feedback">Please select a product.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- English Language Test -->
        <div class="card-body border-top">
          <h5 class="mb-3">English Language Test</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="englishTest" class="form-label">English Test</label>
                <select class="form-select" id="englishTest" v-model="form.englishTest" required>
                  <option value="" disabled selected>Select English Test</option>
                  <option value="IELTS">IELTS</option>
                  <option value="PTE">PTE</option>
                  <option value="ELLT">ELLT</option>
                  <option value="No Test">No Test</option>
                  <option value="MOI">MOI</option>
                </select>
                <div class="invalid-feedback">Please select your English test.</div>
              </div>
            </div>
          </div>

          <div v-if="showTestFields" class="row">
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="higher" class="form-label">Overall Higher</label>
                <input type="text" class="form-control" id="higher" v-model="form.higher" placeholder="Enter Overall Higher" :required="showTestFields">
                <div class="invalid-feedback">Please provide overall higher score.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="less" class="form-label">Not Less than</label>
                <input type="text" class="form-control" id="less" v-model="form.less" placeholder="Enter Not Less than" :required="showTestFields">
                <div class="invalid-feedback">Please provide minimum score.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="score" class="form-label">Overall Score</label>
                <input type="text" class="form-control" id="score" :value="overallScore" placeholder="Calculated score" readonly>
              </div>
            </div>
          </div>

          <div v-if="form.englishTest === 'No Test'" class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="englishscore" class="form-label">Overall English Score</label>
                <input type="text" class="form-control" id="englishscore" v-model="form.englishscore" placeholder="Enter English Score" :required="form.englishTest === 'No Test'">
                <div class="invalid-feedback">Please provide English score.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="englishtheory" class="form-label">English Theory</label>
                <input type="text" class="form-control" id="englishtheory" v-model="form.englishtheory" placeholder="Enter English Theory" :required="form.englishTest === 'No Test'">
                <div class="invalid-feedback">Please provide English theory.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Study Destination -->
        <div class="card-body border-top">
          <h5 class="mb-3">Study Destination</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <select class="form-select" id="country" v-model="form.country" required>
                  <option value="" disabled selected>Select Country</option>
                  <option value="USA">USA</option>
                  <option value="UK">UK</option>
                  <option value="Australia">Australia</option>
                  <option value="Canada">Canada</option>
                </select>
                <div class="invalid-feedback">Please select a country.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" v-model="form.location" placeholder="Enter location" required>
                <div class="invalid-feedback">Please provide a location.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- University Preferences -->
        <div class="card-body border-top">
          <h5 class="mb-3">University Preferences</h5>
          <div id="form-rows">
            <div v-for="(preference, index) in form.preferences" :key="index" class="row mb-3">
              <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                  <label :for="`university${index}`" class="form-label">University</label>
                  <select class="form-select" :id="`university${index}`" v-model="preference.university" @change="handleUniversityChange(index)" required>
                    <option value="" disabled>Select University</option>
                    <option v-for="uni in uniqueUniversities" :key="uni" :value="uni">{{ uni }}</option>
                  </select>
                  <div class="invalid-feedback">Please select a university or N/A.</div>
                </div>
              </div>
              <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                  <label :for="`course${index}`" class="form-label">Course</label>
                  <select class="form-select" :id="`course${index}`" v-model="preference.course" @change="handleCourseChange(index)" :disabled="!preference.university" required>
                    <option value="" disabled>Select Course</option>
                    <option v-for="course in coursesForUniversity(index)" :key="course" :value="course">{{ course }}</option>
                  </select>
                  <div class="invalid-feedback">Please select a course or N/A.</div>
                </div>
              </div>
              <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                  <label :for="`intake${index}`" class="form-label">Intake</label>
                  <select class="form-select" :id="`intake${index}`" v-model="preference.intake" :disabled="!preference.course" required>
                    <option value="" disabled>Select Intake</option>
                     <option v-for="intake in intakesForCourse(index)" :key="intake" :value="intake">{{ intake }}</option>
                  </select>
                  <div class="invalid-feedback">Please select an intake or N/A.</div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="button" @click="addPreference" class="btn btn-outline-primary btn-sm me-2">Add More</button>
              <button type="button" @click="removePreference" class="btn btn-outline-danger btn-sm" :disabled="form.preferences.length <= 1">Remove</button>
            </div>
          </div>
        </div>

        <!-- Application Status -->
        <div class="card-body border-top">
          <h5 class="mb-3">Application Status</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="status" class="form-label">Document Status</label>
                <select class="form-select" id="status" v-model="form.status" required>
                  <option value="" disabled selected>Select Document Status</option>
                  <option value="Partially Received">Partially Received</option>
                  <option value="Fully Received">Fully Received</option>
                  <option value="Initiated For Offer">Initiated For Offer</option>
                </select>
                <div class="invalid-feedback">Please select document status.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-6" v-if="form.status === 'Fully Received'">
              <div class="mb-3">
                <label for="additionalinfo" class="form-label">Initiated Offer Information</label>
                <select class="form-select" id="additionalinfo" v-model="form.additionalinfo" :required="form.status === 'Fully Received'">
                  <option value="" disabled selected>Select Initiated</option>
                  <option value="Not Initiated Offer">Not Initiated Offer</option>
                  <option value="Initiated Offer">Initiated Offer</option>
                </select>
                <div class="invalid-feedback">Please select initiated offer information.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Referral Information -->
        <div class="card-body border-top">
          <h5 class="mb-3">Referral Information</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="source" class="form-label">Source of Referral</label>
                <select class="form-select" id="source" v-model="form.source" required>
                  <option value="" disabled>Select Source</option>
                  <option value="facebook">Facebook</option>
                  <option value="whatapps">WhatsApp</option>
                  <option value="instgram">Instagram</option>
                  <option value="partners">Partners</option>
                  <option value="other">Other</option>
                </select>
                <div class="invalid-feedback">Please select referral source.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-6" v-if="form.source === 'partners'">
              <div class="mb-3">
                <label for="partnerDetails" class="form-label">Partner Details</label>
                <input type="text" class="form-control" id="partnerDetails" v-model="form.partnerDetails" placeholder="Enter partner details" :required="form.source === 'partners'">
                <div class="invalid-feedback">Please provide partner details.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-6" v-if="form.source === 'other'">
              <div class="mb-3">
                <label for="otherDetails" class="form-label">Other Referral Details</label>
                <input type="text" class="form-control" id="otherDetails" v-model="form.otherDetails" placeholder="Enter other details" :required="form.source === 'other'">
                <div class="invalid-feedback">Please provide referral details.</div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Submission Buttons -->
        <div class="p-3 border-top">
          <div class="text-end">
            <button type="submit" class="btn btn-primary" :disabled="isLoading">
              <span v-if="isLoading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              <span class="button-text">{{ isLoading ? 'Submitting...' : 'Submit Form' }}</span>
            </button>
            <button type="reset" @click.prevent="resetForm" class="btn bg-danger-subtle text-danger ms-6 px-4">
              Cancel
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
// 1. Uncomment the import for vue-toastification
import { useToast } from 'vue-toastification';

const router = useRouter();
// 2. Instantiate the toast service
const toast = useToast();

// --- STATE MANAGEMENT ---
const isLoading = ref(false);
const wasValidated = ref(false);

const initialFormState = {
name: '',
email: '',
phone: '',
lastqualification: '',
passed: '',
gpa: '',
english: '', // product_id
englishTest: '',
higher: '',
less: '',
englishscore: '',
englishtheory: '',
country: '',
location: '',
status: '',
additionalinfo: 'N/A',
source: '',
partnerDetails: null,
otherDetails: null,
preferences: [
{ university: '', course: '', intake: '' }
],
};

const form = reactive({ ...initialFormState });

// Data for dropdowns
const products = ref([]);
const structuredData = ref({});
const uniqueUniversities = ref([]);
const passYears = computed(() => {
const currentYear = new Date().getFullYear();
const years = [];
for (let i = currentYear; i >= 2015; i--) {
years.push(i);
}
return years;
});

// --- LIFECYCLE HOOKS ---
onMounted(async () => {
await fetchProducts();
await fetchUniversityData();
});

// --- DATA FETCHING ---
const fetchProducts = async () => {
try {
const response = await axios.get('/api/products');
products.value = response.data;
} catch (error) {
console.error("Error fetching products:", error);
// Enable error toast for data fetching
toast.error("Could not load products.");
}
};

const fetchUniversityData = async () => {
try {
const response = await axios.get('/api/university-data');
processUniversityData(response.data.data_entries);
} catch (error) {
console.error("Error fetching university data:", error);
// Enable error toast for data fetching
toast.error("Could not load university data.");
}
};

const processUniversityData = (entries) => {
// ... (your existing logic is perfect here)
const data = {};
const allUnis = new Set();

entries.forEach(entry => {
const university = entry.newUniversity?.trim();
const course = entry.newCourse?.trim();
const intakeString = entry.newIntake?.trim();
if (!university || !course || !intakeString) return;

allUnis.add(university);
if (!data[university]) data[university] = {};
if (!data[university][course]) data[university][course] = new Set();

const intakes = intakeString.split(',').map(i => i.trim()).filter(Boolean);
intakes.forEach(intake => data[university][course].add(intake));
});

for (const uni in data) {
for (const course in data[uni]) {
data[uni][course] = Array.from(data[uni][course]).sort();
}
}

allUnis.add("N/A");
data["N/A"] = { "N/A": ["N/A"] };

structuredData.value = data;
uniqueUniversities.value = Array.from(allUnis).sort();
};


// --- COMPUTED PROPERTIES for DYNAMIC UI ---
const showTestFields = computed(() => ['IELTS', 'PTE', 'ELLT'].includes(form.englishTest));
const overallScore = computed(() => {
if (form.higher && form.less) {
return `${form.higher}/${form.less}`;
}
return '';
});

// Watchers
watch(() => form.status, (newVal) => {
if (newVal !== 'Fully Received') {
form.additionalinfo = 'N/A';
} else {
form.additionalinfo = '';
}
});
watch(() => form.source, (newVal) => {
if (newVal !== 'partners') form.partnerDetails = null;
if (newVal !== 'other') form.otherDetails = null;
});


// --- METHODS for DYNAMIC FORMS ---
const addPreference = () => {
form.preferences.push({ university: '', course: '', intake: '' });
};

const removePreference = () => {
if (form.preferences.length > 1) {
form.preferences.pop();
}
};

const handleUniversityChange = (index) => {
form.preferences[index].course = '';
form.preferences[index].intake = '';
if(form.preferences[index].university === 'N/A') {
form.preferences[index].course = 'N/A';
form.preferences[index].intake = 'N/A';
}
};

const handleCourseChange = (index) => {
form.preferences[index].intake = '';
if(form.preferences[index].course === 'N/A') {
form.preferences[index].intake = 'N/A';
}
};

const coursesForUniversity = (index) => {
const selectedUni = form.preferences[index].university;
if (!selectedUni || !structuredData.value[selectedUni]) return [];
return Object.keys(structuredData.value[selectedUni]).sort();
};

const intakesForCourse = (index) => {
const { university, course } = form.preferences[index];
if (!university || !course || !structuredData.value[university]?.[course]) return [];
return structuredData.value[university][course];
};

const resetForm = () => {
Object.assign(form, initialFormState);
form.preferences = [{ university: '', course: '', intake: '' }];
wasValidated.value = false;
};

// --- FORM SUBMISSION ---
const submitForm = async (event) => {
  wasValidated.value = true;
  if (!event.target.checkValidity()) {
    toast.error("Please fill all required fields correctly.");
    return;
  }

  isLoading.value = true;
  let submissionSuccess = false; // 1. Create a flag to track success

  const payload = {
      ...form,
      score: overallScore.value,
      university: form.preferences.map(p => p.university),
      course: form.preferences.map(p => p.course),
      intake: form.preferences.map(p => p.intake),
  };
  delete payload.preferences;

  try {
    const response = await axios.post('/api/application', payload);
    
    // Show success toast from the API response
    toast.success(response.data.message || "Application submitted successfully!");
    
    submissionSuccess = true; // 2. Set the flag to true on success

  } catch (error) {
    if (error.response && error.response.status === 422) {
      toast.error("Please correct the form errors and try again.");
      console.error("Validation Errors:", error.response.data.errors);
    } else {
      toast.error(error.response?.data?.message || "An unexpected error occurred.");
      console.error("Submission Error:", error);
    }
  } finally {
    isLoading.value = false;
  }

  // 3. Only attempt to navigate if the submission was successful
  if (submissionSuccess) {
    router.push({ name: 'applicationList' }); 
  }
};
</script>

<style scoped>
/* You can add component-specific styles here if needed */
.ms-6 {
margin-left: 1.5rem !important;
}
</style>