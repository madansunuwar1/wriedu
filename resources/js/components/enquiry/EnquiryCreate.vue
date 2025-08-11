<template>
  <div class="col-lg-12 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body pb-0">
        <h4 class="card-title">Enquiry Form</h4>
        <p class="card-subtitle mb-3">Fill out the form to create a new enquiry.</p>
      </div>
      
      <!-- The form calls the handleSubmit method on submission -->
      <form @submit.prevent="handleSubmit">
        
        <!-- Personal Information Section -->
        <div class="card-body border-top">
          <h5 class="mb-3">Personal Information</h5>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="uname" class="form-label">Student Name</label>
                <input type="text" class="form-control" id="uname" v-model="formData.uname" placeholder="Enter your name" required>
                <small v-if="errors.uname" class="text-danger">{{ errors.uname[0] }}</small>
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="email" class="form-label">Email ID</label>
                <input type="email" class="form-control" id="email" v-model="formData.email" placeholder="Enter your email" required>
                 <small v-if="errors.email" class="text-danger">{{ errors.email[0] }}</small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="contact" class="form-label">Contact Number</label>
                <input type="tel" class="form-control" id="contact" v-model="formData.contact" placeholder="Enter your contact number" required>
                 <small v-if="errors.contact" class="text-danger">{{ errors.contact[0] }}</small>
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="guardians" class="form-label">Guardian's Name</label>
                <input type="text" class="form-control" id="guardians" v-model="formData.guardians" placeholder="Enter guardian's name">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="contacts" class="form-label">Guardian's Number</label>
                <input type="tel" class="form-control" id="contacts" v-model="formData.contacts" placeholder="Enter guardian's number">
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <select class="form-select" id="country" v-model="formData.country">
                  <option value="" disabled>Select Country</option>
                  <option value="USA">USA</option>
                  <option value="UK">UK</option>
                  <option value="Australia">Australia</option>
                  <option value="Canada">Canada</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="education" class="form-label">Education</label>
                <select class="form-select" id="education" v-model="formData.education">
                  <option value="" disabled>Select Education</option>
                  <option value="Intermediate">Intermediate</option>
                  <option value="Bachelor">Bachelor</option>
                  <option value="Master">Master</option>
                </select>
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="about" class="form-label">Know About Us</label>
                <select class="form-select" id="about" v-model="formData.about">
                  <option value="" disabled>Select About</option>
                  <option value="Facebook">Facebook</option>
                  <option value="Tiktok">Tiktok</option>
                  <option value="Exhibition">Exhibition</option>
                  <option value="TV/FM">TV/FM</option>
                  <option value="Instagram">Instagram</option>
                  <option value="Seminar">Seminar</option>
                  <option value="Friends">Friends</option>
                  <option value="NewsPaper">NewsPaper</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Test Scores Section -->
        <div class="card-body border-top">
          <h5 class="mb-3">Test Scores</h5>
          <div class="row">
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="ielts" class="form-label">IELTS</label>
                <input type="text" class="form-control" id="ielts" v-model="formData.ielts" placeholder="Enter your IELTS score">
              </div>
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="toefl" class="form-label">TOEFL</label>
                <input type="text" class="form-control" id="toefl" v-model="formData.toefl" placeholder="Enter your TOEFL score">
              </div>
            </div>
             <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="ellt" class="form-label">ELLT</label>
                <input type="text" class="form-control" id="ellt" v-model="formData.ellt" placeholder="Enter your ELLT score">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="pte" class="form-label">PTE</label>
                <input type="text" class="form-control" id="pte" v-model="formData.pte" placeholder="Enter your PTE score">
              </div>
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="sat" class="form-label">SAT</label>
                <input type="text" class="form-control" id="sat" v-model="formData.sat" placeholder="Enter your SAT score">
              </div>
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="gre" class="form-label">GRE</label>
                <input type="text" class="form-control" id="gre" v-model="formData.gre" placeholder="Enter your GRE score">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="gmat" class="form-label">GMAT</label>
                <input type="text" class="form-control" id="gmat" v-model="formData.gmat" placeholder="Enter your GMAT score">
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="other" class="form-label">Other Test Score</label>
                <input type="text" class="form-control" id="other" v-model="formData.other" placeholder="Enter other test score">
              </div>
            </div>
          </div>
           <div class="row">
            <div class="col-sm-12 col-md-6">
              <div class="mb-3">
                <label for="counselor" class="form-label">Counselor</label>
                <select class="form-select" id="counselor" v-model="formData.counselor">
                  <option value="" disabled>Select Counselor Name</option>
                  <!-- Dynamically populated from API -->
                  <option v-for="name in counselors" :key="name.id" :value="name.id">{{ name.name }}</option>
                  <!-- Hardcoded options as fallback or in addition -->
                  <option value="Ram">Ram</option>
                  <option value="Ichchya">Ichchya</option>
                  <option value="Roshan">Roshan</option>
                  <option value="Prasamsha">Prasamsha</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="mb-3">
                <label for="feedback" class="form-label">Feedback</label>
                <textarea class="form-control" id="feedback" v-model="formData.feedback" rows="3" placeholder="Enter your feedback"></textarea>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Education History Section -->
        <div class="card-body border-top">
          <h5 class="mb-3">Education History</h5>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Level</th>
                  <th>Institution Name</th>
                  <th>Grade/Percentage</th>
                  <th>Completion Year</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>SEE/SLC</td>
                  <td><input type="text" class="form-control" v-model="formData.institution1" placeholder="Enter institution name"></td>
                  <td><input type="text" class="form-control" v-model="formData.grade1" placeholder="Enter grade/percentage"></td>
                  <td><input type="text" class="form-control" v-model="formData.year1" placeholder="Enter completion year"></td>
                </tr>
                <tr>
                  <td>+2</td>
                  <td><input type="text" class="form-control" v-model="formData.institution2" placeholder="Enter institution name"></td>
                  <td><input type="text" class="form-control" v-model="formData.grade2" placeholder="Enter grade/percentage"></td>
                  <td><input type="text" class="form-control" v-model="formData.year2" placeholder="Enter completion year"></td>
                </tr>
                <tr>
                  <td>Bachelor</td>
                  <td><input type="text" class="form-control" v-model="formData.institution3" placeholder="Enter institution name"></td>
                  <td><input type="text" class="form-control" v-model="formData.grade3" placeholder="Enter grade/percentage"></td>
                  <td><input type="text" class="form-control" v-model="formData.year3" placeholder="Enter completion year"></td>
                </tr>
                <tr>
                  <td>Master</td>
                  <td><input type="text" class="form-control" v-model="formData.institution4" placeholder="Enter institution name"></td>
                  <td><input type="text" class="form-control" v-model="formData.grade4" placeholder="Enter grade/percentage"></td>
                  <td><input type="text" class="form-control" v-model="formData.year4" placeholder="Enter completion year"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Form Actions -->
        <div class="p-3 border-top">
          <div class="text-end">
            <button type="submit" class="btn btn-primary" :disabled="isLoading">
              {{ isLoading ? 'Submitting...' : 'Submit Form' }}
            </button>
            <button type="button" @click="resetForm" class="btn bg-danger-subtle text-danger ms-6 px-4">
              Cancel
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Success Popup -->
  <div v-if="showSuccessPopup" class="overlay"></div>
  <div v-if="showSuccessPopup" class="popup">
    <div class="popup-content">
      <h2>Success!</h2>
      <p>Your enquiry has been submitted successfully.</p>
      <button class="popup-btn" @click="closePopup">OK</button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';
import { useRouter } from 'vue-router'; // Make sure to install axios: npm install axios

const toast = useToast();
const router = useRouter();
// Reactive state for form data
const getInitialFormData = () => ({
  uname: '',
  email: '',
  contact: '',
  guardians: '',
  contacts: '',
  country: '',
  education: '',
  about: '',
  ielts: '',
  toefl: '',
  ellt: '',
  pte: '',
  sat: '',
  gre: '',
  gmat: '',
  other: '',
  feedback: '',
  counselor: '',
  institution1: '',
  grade1: '',
  year1: '',
  institution2: '',
  grade2: '',
  year2: '',
  institution3: '',
  grade3: '',
  year3: '',
  institution4: '',
  grade4: '',
  year4: '',
});

const formData = reactive(getInitialFormData());

// State for counselors list, loading, errors, and popup
const counselors = ref([]);
const isLoading = ref(false);
const errors = ref({});


// Fetch counselors when the component is mounted
onMounted(async () => {
  try {
    // Note: The API route is /api/enquiries/create, not /api/enquiry/create
    const response = await axios.get('/api/names'); 
    counselors.value = response.data;
  } catch (error) {
    console.error("Failed to fetch counselors:", error);
       toast.error("Failed to load counselor data.");
  }
});

const handleSubmit = async () => {
  isLoading.value = true;
  errors.value = {};

  // To satisfy the strict 'required' validation on the backend,
  // we'll replace any empty optional fields with 'N/A'.
  const processedData = { ...formData };
  for (const key in processedData) {
    if (processedData[key] === '' || processedData[key] === null) {
      processedData[key] = 'N/A';
    }
  }

  try {
    // Post the processed data to the store endpoint
    await axios.post('/api/enquiries', processedData);
    
    // On success: show popup and reset the form
   toast.success('Enquiry has been submitted successfully!');
     router.push({ name: 'EnquiryIndex' });
    resetForm();

  } catch (error) {
    if (error.response && error.response.status === 422) {
      // Handle validation errors
      errors.value = error.response.data.errors;
    toast.error('Please correct the highlighted errors in the form.'); 
    } else {
      // Handle other server errors
      console.error('An error occurred:', error);
     toast.error('An unexpected error occurred. Please try again.'); 
    }
  } finally {
    isLoading.value = false;
  }
};

const resetForm = () => {
  Object.assign(formData, getInitialFormData());
  errors.value = {};
};

</script>

<style scoped>
/* Scoped styles from the original Blade file for the popup */
.overlay {
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}

.popup {
  display: block;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  z-index: 1001;
}

.popup-content {
  text-align: center;
}

.popup-btn {
  margin-top: 15px;
  padding: 8px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.popup-btn:hover {
  background-color: #0056b3;
}
</style>