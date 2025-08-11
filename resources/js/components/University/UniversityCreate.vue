<template>
  <div class="col-lg-12 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body pb-0">
        <h4 class="card-title">University Data Entry Form</h4>
        <p class="card-subtitle mb-3">Fill out the form to add new university data.</p>
      </div>

      <form @submit.prevent="handleSubmit" :class="{ 'was-validated': wasValidated }" novalidate>
        <!-- University and Location Section -->
        <div class="card-body border-top">
          <h5 class="mb-3">University Information</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newUniversity" class="form-label">University Name</label>
              <input type="text" class="form-control" v-model="formData.newUniversity" id="newUniversity" placeholder="Enter university name" required>
              <div class="invalid-feedback">{{ serverErrors.newUniversity?.[0] || 'Please provide university name.' }}</div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newLocation" class="form-label">Location</label>
              <input type="text" class="form-control" v-model="formData.newLocation" id="newLocation" placeholder="Enter location" required>
              <div class="invalid-feedback">{{ serverErrors.newLocation?.[0] || 'Please provide location.' }}</div>
            </div>
          </div>
        </div>

        <!-- Course and Intake Section -->
        <div class="card-body border-top">
          <h5 class="mb-3">Course Details</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newCourse" class="form-label">Course Name</label>
              <input type="text" class="form-control" v-model="formData.newCourse" id="newCourse" placeholder="Enter course name" required>
              <div class="invalid-feedback">{{ serverErrors.newCourse?.[0] || 'Please provide course name.' }}</div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="intakeInput" class="form-label">Intake Months</label>
              <VueDatePicker
                v-model="formData.newIntake"
                :enable-time-picker="false"
                month-picker
                auto-apply
                placeholder="Select month and year"
              />
              <div class="invalid-feedback">{{ serverErrors.newIntake?.[0] || 'Please provide intake period.' }}</div>
            </div>
          </div>
        </div>

        <!-- Financial Information Section -->
        <div class="card-body border-top">
          <h5 class="mb-3">Financial Information</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newScholarship" class="form-label">Scholarship Details</label>
              <input type="text" class="form-control" v-model="formData.newScholarship" id="newScholarship" placeholder="Enter scholarship details">
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newAmount" class="form-label">Tuition Fees</label>
              <input type="text" class="form-control" v-model="formData.newAmount" id="newAmount" placeholder="Enter tuition amount">
            </div>
          </div>
        </div>

        <!-- Undergraduate Requirements -->
        <div class="card-body border-top">
          <h5 class="mb-3">Undergraduate Requirements</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newIelts" class="form-label">IELTS Requirement</label>
              <input type="text" class="form-control" v-model="formData.newIelts" id="newIelts" placeholder="Enter IELTS score">
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newpte" class="form-label">PTE Requirement</label>
              <input type="text" class="form-control" v-model="formData.newpte" id="newpte" placeholder="Enter PTE score">
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newug" class="form-label">Gap Accepted</label>
              <input type="text" class="form-control" v-model="formData.newug" id="newug" placeholder="Enter gap years accepted">
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newgpaug" class="form-label">GPA/Percentage</label>
              <input type="text" class="form-control" v-model="formData.newgpaug" id="newgpaug" placeholder="Enter GPA requirement">
            </div>
          </div>
        </div>

        <!-- Postgraduate Requirements -->
        <div class="card-body border-top">
          <h5 class="mb-3">Postgraduate Requirements</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newPgIelts" class="form-label">IELTS Requirement</label>
              <input type="text" class="form-control" v-model="formData.newPgIelts" id="newPgIelts" placeholder="Enter IELTS score">
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newPgPte" class="form-label">PTE Requirement</label>
              <input type="text" class="form-control" v-model="formData.newPgPte" id="newPgPte" placeholder="Enter PTE score">
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newpg" class="form-label">Gap Accepted</label>
              <input type="text" class="form-control" v-model="formData.newpg" id="newpg" placeholder="Enter gap years accepted">
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newgpapg" class="form-label">GPA/Percentage</label>
              <input type="text" class="form-control" v-model="formData.newgpapg" id="newgpapg" placeholder="Enter GPA requirement">
            </div>
          </div>
        </div>

        <!-- Additional Information Section -->
        <div class="card-body border-top">
          <h5 class="mb-3">Additional Information</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="newtest" class="form-label">English Waiver Requirement</label>
              <input type="text" class="form-control" v-model="formData.newtest" id="newtest" placeholder="Enter English waiver details">
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="country" class="form-label">Country</label>
              <select class="form-select" v-model="formData.country" id="country" required>
                <option value="" disabled>Select Country</option>
                <option value="USA">USA</option>
                <option value="United Kingdom">United Kingdom</option>
                <option value="Australia">Australia</option>
                <option value="Canada">Canada</option>
                <option value="New Zealand">New Zealand</option>
              </select>
              <div class="invalid-feedback">{{ serverErrors.country?.[0] || 'Please select country.' }}</div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="requireddocuments" class="form-label">Required Documents</label>
              <textarea class="form-control" v-model="formData.requireddocuments" id="requireddocuments" placeholder="List required documents" rows="3" required></textarea>
              <div class="invalid-feedback">{{ serverErrors.requireddocuments?.[0] || 'Please list required documents.' }}</div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
              <label for="level" class="form-label">Level</label>
              <select class="form-select" v-model="formData.level" id="level" required>
                <option value="" disabled>Select Level</option>
                <option value="Undergraduate">Undergraduate</option>
                <option value="Postgraduate">Postgraduate</option>
                <option value="Both">Both</option>
              </select>
              <div class="invalid-feedback">{{ serverErrors.level?.[0] || 'Please select level.' }}</div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="p-3 border-top">
          <div class="text-end">
            <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              <i v-else class="ti ti-device-floppy me-1"></i>
              {{ isSubmitting ? 'Saving...' : 'Save University Data' }}
            </button>
            <button type="button" @click="handleCancel" class="btn bg-danger-subtle text-danger ms-6 px-4">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';
// import Swal from 'sweetalert2'; // [REMOVED] No longer needed for this component
import { useRouter } from 'vue-router';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { useToast } from 'vue-toastification'; // 1. NEW: Import useToast

const router = useRouter();
const toast = useToast(); // 2. NEW: Instantiate the toast service

// --- STATE MANAGEMENT ---
const formData = reactive({
  newUniversity: '',
  newLocation: '',
  newCourse: '',
  newIntake: null,
  newScholarship: '',
  newAmount: '',
  newIelts: '',
  newpte: '',
  newug: '',
  newgpaug: '',
  newPgIelts: '',
  newPgPte: '',
  newpg: '',
  newgpapg: '',
  newtest: '',
  country: '',
  requireddocuments: '',
  level: ''
});

const wasValidated = ref(false);
const isSubmitting = ref(false);
const serverErrors = ref({});

const handleSubmit = async (event) => {
  wasValidated.value = true;
  serverErrors.value = {};

  if (!event.target.checkValidity()) {
    return;
  }

  isSubmitting.value = true;

  const payload = { ...formData };

  if (payload.newIntake) {
    try {
      let dateObj;
      if (payload.newIntake instanceof Date) {
        dateObj = payload.newIntake;
      } else if (typeof payload.newIntake === 'object' && payload.newIntake.year && payload.newIntake.month !== undefined) {
        dateObj = new Date(payload.newIntake.year, payload.newIntake.month);
      } else if (typeof payload.newIntake === 'string') {
        dateObj = new Date(payload.newIntake);
      } else {
        console.warn('Unexpected newIntake format:', payload.newIntake);
        dateObj = new Date(payload.newIntake);
      }
      if (dateObj && !isNaN(dateObj.getTime())) {
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const month = monthNames[dateObj.getMonth()];
        const year = dateObj.getFullYear();
        payload.newIntake = `${month} ${year}`;
      } else {
        payload.newIntake = 'N/A';
      }
    } catch (error) {
      console.error('Error processing date:', error);
      payload.newIntake = 'N/A';
    }
  } else {
    payload.newIntake = 'N/A';
  }

  for (const key in payload) {
    if (payload[key] === '' && key !== 'newIntake') {
      payload[key] = 'N/A';
    }
  }

  try {
    await axios.post('/api/data-entries', payload);

    // 3. UPDATED: Replace Swal with a success toast
    toast.success('University data has been saved successfully.');

    // Assuming your index route is named 'dataentry.index' or similar. 
    // Please verify this name in your routes file.
    // I'll use a placeholder 'DataEntryIndex' which you should adjust.
      router.push({ name: 'universit.index' }); // Adjust 'DataEntryIndex' to your actual route name

  } catch (error) {
    if (error.response && error.response.status === 422) {
      serverErrors.value = error.response.data.errors;
      // 4. UPDATED: Replace Swal with an error toast
      toast.error('Please check the form for errors.');
    } else {
      // 5. UPDATED: Replace Swal with an error toast
      toast.error('An unexpected error occurred. Please try again.');
    }
  } finally {
    isSubmitting.value = false;
  }
};

const handleCancel = () => {
  router.back();
};
</script>

<style scoped>
.search-container { position: relative; }
.stored-values { display: flex; flex-wrap: wrap; gap: 8px; }
.stored-value {
  background-color: #e9ecef;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  display: inline-flex;
  align-items: center;
  font-weight: 500;
}
.remove-btn {
  margin-left: 8px;
  cursor: pointer;
  font-weight: bold;
  color: #6c757d;
  font-size: 1rem;
  line-height: 1;
}
.remove-btn:hover {
  color: #343a40;
}
</style>