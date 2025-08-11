<template>
  <div class="card">
    <div class="border-bottom title-part-padding">
      <h4 class="card-title mb-0">Create Commission Rate</h4>
    </div>

    <div class="card-body">
      <form @submit.prevent="handleSubmit" novalidate>
        <!-- University and Product section -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="university">University</label>
            <select
              class="form-select"
              id="university"
              v-model="form.university"
              :class="{ 'is-invalid': errors.university }"
              required
            >
              <option value="" disabled>Select University Name</option>
              <option v-for="university in uniqueUniversities" :key="university" :value="university">
                {{ university }}
              </option>
            </select>
            <div v-if="errors.university" class="invalid-feedback">{{ errors.university[0] }}</div>
            <div class="invalid-feedback" v-else-if="!form.university">Please select a university.</div>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="product">Product</label>
            <input
              type="text"
              class="form-control"
              id="product"
              v-model="form.product"
              :class="{ 'is-invalid': errors.product }"
              placeholder="Enter product"
              required
            />
            <div v-if="errors.product" class="invalid-feedback">
              {{ errors.product[0] }}
            </div>
          </div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label" for="intake">Intake</label>
          <select
            class="form-select"
            id="intake"
            v-model="form.intake"
            :class="{ 'is-invalid': errors.intake }"
            required
          >
            <option value="" disabled>Select intake name</option>
            <option v-for="intake in sortedUniqueIntakes" :key="intake" :value="intake">
              {{ intake }}
            </option>
          </select>
          <div v-if="errors.intake" class="invalid-feedback">{{ errors.intake[0] }}</div>
          <div class="invalid-feedback" v-else-if="!form.intake">Please select an intake name.</div>
          <!-- Debug info for intake -->
          <small class="text-muted" v-if="showIntakeError">
            Error loading intakes. Available intakes: {{ sortedUniqueIntakes.length }}
          </small>
        </div>

        <!-- Partner and Progressive Commission -->
        <div class="row">
          <div class="col-md-6 wrapper-a mb-3">
            <label class="form-label" for="partner">Partner</label>
            <select
              class="form-select"
              id="partner"
              v-model="form.partner"
              :class="{ 'is-invalid': errors.partner }"
              required
            >
              <option disabled value="">Select a Partner</option>
              <option v-for="p in partners" :key="p.id" :value="p.id">
                {{ p.agency_name }}
              </option>
            </select>
            <div v-if="errors.partner" class="invalid-feedback">
              {{ errors.partner[0] }}
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Progressive Commission?</label>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                v-model="form.has_progressive_commission"
                id="progressiveYes"
                value="yes"
              />
              <label class="form-check-label" for="progressiveYes">Yes</label>
            </div>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                v-model="form.has_progressive_commission"
                id="progressiveNo"
                value="no"
              />
              <label class="form-check-label" for="progressiveNo">No</label>
            </div>
          </div>
        </div>

        <!-- Conditional Progressive Input -->
        <div class="row" v-if="form.has_progressive_commission === 'yes'">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="progressive_commission">Progressive Commission Details</label>
            <input
              type="text"
              class="form-control"
              id="progressive_commission"
              v-model="form.progressive_commission"
              :class="{ 'is-invalid': errors.progressive_commission }"
              placeholder="e.g., Tiered rates, specific goals"
            />
            <div v-if="errors.progressive_commission" class="invalid-feedback">
              {{ errors.progressive_commission[0] }}
            </div>
          </div>
        </div>

        <!-- Bonus and Incentive Commission Rows -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Bonus Commission?</label>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                v-model="form.has_bonus_commission"
                id="bonusYes"
                value="yes"
              />
              <label class="form-check-label" for="bonusYes">Yes</label>
            </div>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                v-model="form.has_bonus_commission"
                id="bonusNo"
                value="no"
              />
              <label class="form-check-label" for="bonusNo">No</label>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Incentive Commission?</label>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                v-model="form.has_incentive_commission"
                id="incentiveYes"
                value="yes"
              />
              <label class="form-check-label" for="incentiveYes">Yes</label>
            </div>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                v-model="form.has_incentive_commission"
                id="incentiveNo"
                value="no"
              />
              <label class="form-check-label" for="incentiveNo">No</label>
            </div>
          </div>
        </div>

        <!-- Conditional Bonus and Incentive Inputs -->
        <div class="row">
          <div class="col-md-6 mb-3" v-if="form.has_bonus_commission === 'yes'">
            <label class="form-label" for="bonus_commission">Bonus Commission Details</label>
            <input
              type="text"
              class="form-control"
              id="bonus_commission"
              v-model="form.bonus_commission"
              :class="{ 'is-invalid': errors.bonus_commission }"
              placeholder="e.g., 5% on 10th sale"
            />
            <div v-if="errors.bonus_commission" class="invalid-feedback">{{ errors.bonus_commission[0] }}</div>
          </div>
          <div class="col-md-6 mb-3" v-if="form.has_incentive_commission === 'yes'">
            <label class="form-label" for="incentive_commission">Incentive Commission Details</label>
            <input
              type="text"
              class="form-control"
              id="incentive_commission"
              v-model="form.incentive_commission"
              :class="{ 'is-invalid': errors.incentive_commission }"
              placeholder="e.g., Trip for top performer"
            />
            <div v-if="errors.incentive_commission" class="invalid-feedback">{{ errors.incentive_commission[0] }}</div>
          </div>
        </div>

        <!-- Commission Types Dropdown -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="commission">Commission(%)</label>
            <div class="dropdown">
              <button
                class="btn dropdown-toggle form-control text-start custom-outline"
                :class="{ 'is-invalid': errors.commissionTypes || errors.commissionValues }"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                data-bs-auto-close="outside"
              >
                {{ commissionDropdownText }}
              </button>
              <div class="dropdown-menu dropdown-menu-custom w-100 p-3">
                <div v-for="type in commissionOptions" :key="type" class="mb-2">
                  <div class="row align-items-center">
                    <div class="col-4">
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          :id="`check-${type}`"
                          :value="type"
                          v-model="form.commissionTypes"
                        />
                        <label class="form-check-label" :for="`check-${type}`">{{ type }}</label>
                      </div>
                    </div>
                    <div class="col-8" v-if="form.commissionTypes.includes(type)">
                      <input
                        type="number"
                        step="0.01"
                        class="form-control form-control-sm"
                        v-model="form.commissionValues[type]"
                        :placeholder="`${type} value (%)`"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="errors.commissionTypes" class="invalid-feedback d-block">
                {{ errors.commissionTypes[0] }}
              </div>
              <div v-if="errors.commissionValues" class="invalid-feedback d-block">
                Please provide a value for each selected commission type.
              </div>
            </div>
          </div>
        </div>

        <!-- Submit button -->
        <div class="text-center mt-4">
          <button class="btn btn-primary" type="submit" :disabled="isLoading">
            <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <i v-else class="ti ti-device-floppy me-1"></i>
            {{ isLoading ? 'Saving...' : 'Save Commission' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style>
/* Scoped styles ensure they only apply to this component */
.custom-outline {
  background-color: transparent;
  border: 1px solid #ced4da;
  color: inherit;
}
.custom-outline:hover {
  border-color: #86b7fe;
}
.invalid-feedback.d-block {
  display: block !important;
}

/* Custom dropdown styling */
.dropdown-menu-custom {
  max-height: 200px;
  overflow-y: auto;
  position: absolute;
  transform: translate3d(0px, 38px, 0px) !important;
  top: 100%;
  left: 0;
}
select.form-select {
  position: relative !important;
  z-index: 10 !important; /* Make sure it's above other elements */
}

.wrapper-a {
  overflow: visible !important;
  position: relative;
}
</style>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

// --- STATE ---
const form = ref({
  university: '',
  product: '',
  partner: '',
  intake: '',
  has_progressive_commission: 'no',
  progressive_commission: '',
  has_bonus_commission: 'no',
  bonus_commission: '',
  has_incentive_commission: 'no',
  incentive_commission: '',
  commissionTypes: [], // e.g., ['Net', 'Gross']
  commissionValues: {}, // e.g., { Net: 10, Gross: 15 }
});

const partners = ref([]);
const data_entries = ref([]);
const commissionOptions = ['Net', 'Gross', 'Standard'];
const errors = ref({});
const isLoading = ref(false);
const showIntakeError = ref(false);
const showPartnerError = ref(false);

// Month mapping for consistent formatting
const monthMap = {
  // Full names
  'january': 'Jan', 'february': 'Feb', 'march': 'Mar', 'april': 'Apr',
  'may': 'May', 'june': 'Jun', 'july': 'Jul', 'august': 'Aug',
  'september': 'Sep', 'october': 'Oct', 'november': 'Nov', 'december': 'Dec',
  // Abbreviated versions
  'jan': 'Jan', 'feb': 'Feb', 'mar': 'Mar', 'apr': 'Apr',
  'may': 'May', 'jun': 'Jun', 'jul': 'Jul', 'aug': 'Aug',
  'sep': 'Sep', 'oct': 'Oct', 'nov': 'Nov', 'dec': 'Dec'
};

// Month order for sorting
const monthOrder = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

// --- LIFECYCLE HOOKS ---
onMounted(async () => {
  try {
    await Promise.all([fetchPartners(), fetchIntakes()]);
  } catch (error) {
    console.error("Error during onMounted:", error);
    Swal.fire('Error', 'Failed to load initial data. Please refresh the page.', 'error');
  }
});

async function fetchPartners() {
  try {
    const response = await axios.get('/api/partners');
    console.log("Partners API Response:", response.data);
    
    // Handle different response structures
    let partnersData;
    if (response.data && Array.isArray(response.data)) {
      partnersData = response.data;
    } else if (response.data && response.data.data && Array.isArray(response.data.data)) {
      partnersData = response.data.data;
    } else if (response.data && response.data.partners && Array.isArray(response.data.partners)) {
      partnersData = response.data.partners;
    } else {
      console.warn("Unexpected partners response structure:", response.data);
      partnersData = [];
    }
    
    partners.value = partnersData;
    console.log("Partners processed:", partners.value);
    showPartnerError.value = false;
    
  } catch (error) {
    console.error("Failed to fetch partners:", error);
    showPartnerError.value = true;
    
    let errorMessage = 'Could not load partner data.';
    if (error.response) {
      console.error("Partner API Error:", {
        status: error.response.status,
        data: error.response.data,
        headers: error.response.headers
      });
      
      if (error.response.status === 404) {
        errorMessage = 'Partner endpoint not found. Please check the API configuration.';
      } else if (error.response.status === 500) {
        errorMessage = 'Server error while loading partners. Please try again later.';
      }
    } else if (error.request) {
      errorMessage = 'Network error. Please check your connection.';
    }
    
    Swal.fire('Error', errorMessage, 'error');
  }
}

async function fetchIntakes() {
  try {
    const response = await axios.get('/api/intakes');
    console.log("Intakes API Response:", response.data);

    // Handle different response structures for intakes
    let intakesData;
    if (response.data && Array.isArray(response.data)) {
      intakesData = response.data;
    } else if (response.data && response.data.data && Array.isArray(response.data.data)) {
      intakesData = response.data.data;
    } else if (response.data && response.data.intakes && Array.isArray(response.data.intakes)) {
      intakesData = response.data.intakes;
    } else {
      console.warn("Unexpected intakes response structure:", response.data);
      intakesData = [];
    }

    data_entries.value = intakesData;
    console.log("Intakes processed:", data_entries.value);
    showIntakeError.value = false;
    
  } catch (error) {
    console.error("Failed to fetch intakes:", error);
    showIntakeError.value = true;
    
    let errorMessage = 'Could not load intake data.';
    if (error.response) {
      console.error("Intake API Error:", {
        status: error.response.status,
        data: error.response.data,
        headers: error.response.headers
      });
      
      if (error.response.status === 404) {
        errorMessage = 'Intake endpoint not found. Please check the API configuration.';
      } else if (error.response.status === 500) {
        errorMessage = 'Server error while loading intakes. Please try again later.';
      }
    } else if (error.request) {
      errorMessage = 'Network error. Please check your connection.';
    }
    
    Swal.fire('Error', errorMessage, 'error');
  }
}

// --- UTILITY FUNCTIONS ---
function normalizeMonth(monthStr) {
  if (!monthStr) return '';
  const normalized = monthStr.toLowerCase().trim();
  return monthMap[normalized] || monthStr;
}

function extractMonthsFromIntake(intakeStr) {
  if (!intakeStr) return [];
  
  // Split by common separators and clean up
  const parts = intakeStr.split(/[,\s\-\/]+/).filter(part => part.trim() !== '');
  const months = [];
  
  parts.forEach(part => {
    const normalized = normalizeMonth(part);
    if (monthOrder.includes(normalized)) {
      months.push(normalized);
    }
  });
  
  // Remove duplicates
  return [...new Set(months)];
}

function getAllSeparatedIntakes() {
  const allIntakes = [];
  
  console.log("Processing data_entries for intakes:", data_entries.value);
  
  data_entries.value.forEach(entry => {
    console.log("Processing entry:", entry);
    
    // Try different possible field names for intake
    const intakeField = entry.newIntake || entry.intake || entry.intake_name || entry.name;
    
    if (intakeField) {
      // If it's a month-based intake, extract months
      const months = extractMonthsFromIntake(intakeField);
      if (months.length > 0) {
        allIntakes.push(...months);
      } else {
        // If not month-based, use the value as is
        allIntakes.push(intakeField);
      }
    }
  });
  
  console.log("All extracted intakes:", allIntakes);
  
  // Remove duplicates
  const uniqueIntakes = [...new Set(allIntakes)];
  console.log("Unique intakes:", uniqueIntakes);
  
  return uniqueIntakes;
}

function getAllUniqueUniversities() {
  const universities = [];
  
  console.log("Processing data_entries for universities:", data_entries.value);
  
  data_entries.value.forEach(entry => {
    // Try different possible field names for university
    const universityField = entry.newUniversity || entry.university || entry.university_name || entry.name;
    
    if (universityField && universityField.trim() !== '') {
      universities.push(universityField.trim());
    }
  });
  
  console.log("All extracted universities:", universities);
  
  // Remove duplicates and sort alphabetically
  const uniqueUniversities = [...new Set(universities)].sort();
  console.log("Unique universities:", uniqueUniversities);
  
  return uniqueUniversities;
}

// --- COMPUTED PROPERTIES ---
const commissionDropdownText = computed(() => {
  if (form.value.commissionTypes.length === 0) {
    return 'Select Commission Types';
  }
  return form.value.commissionTypes.join(', ');
});

const sortedUniqueIntakes = computed(() => {
  const intakes = getAllSeparatedIntakes();
  
  // Separate month-based intakes from others
  const monthIntakes = intakes.filter(intake => monthOrder.includes(intake));
  const otherIntakes = intakes.filter(intake => !monthOrder.includes(intake));
  
  // Sort month intakes by month order
  const sortedMonthIntakes = monthIntakes.sort((a, b) => {
    const aIndex = monthOrder.indexOf(a);
    const bIndex = monthOrder.indexOf(b);
    return aIndex - bIndex;
  });
  
  // Sort other intakes alphabetically
  const sortedOtherIntakes = otherIntakes.sort();
  
  // Combine them (months first, then others)
  const result = [...sortedMonthIntakes, ...sortedOtherIntakes];
  console.log("Sorted intakes for dropdown:", result);
  
  return result;
});

const uniqueUniversities = computed(() => {
  return getAllUniqueUniversities();
});

// --- METHODS ---
const handleSubmit = async () => {
  isLoading.value = true;
  errors.value = {};

  try {
    const response = await axios.post('/api/commission-payable', form.value);

    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: response.data.message,
      timer: 2000,
      showConfirmButton: false,
    });

    resetForm();

  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
      Swal.fire('Validation Error', 'Please check the form for errors.', 'error');
    } else {
      console.error('Submission failed:', error);
      Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
    }
  } finally {
    isLoading.value = false;
  }
};

const resetForm = () => {
  form.value = {
    university: '',
    product: '',
    partner: '',
    intake: '',
    has_progressive_commission: 'no',
    progressive_commission: '',
    has_bonus_commission: 'no',
    bonus_commission: '',
    has_incentive_commission: 'no',
    incentive_commission: '',
    commissionTypes: [],
    commissionValues: {},
  };
  errors.value = {};
}
</script>