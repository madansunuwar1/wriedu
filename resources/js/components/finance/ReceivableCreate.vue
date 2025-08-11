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
            <option v-for="intake in uniqueIntakes" :key="intake" :value="intake">
              {{ intake }}
            </option>
          </select>
          <div v-if="errors.intake" class="invalid-feedback">{{ errors.intake[0] }}</div>
          <div class="invalid-feedback" v-else-if="!form.intake">Please select an intake name.</div>
          <small class="form-text text-muted">Note: If multiple intakes are selected (comma-separated), separate entries will be created for each.</small>
        </div>

        <!-- Partner and Progressive Commission -->
        <div class="row">
          
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
            <small class="form-text text-muted">Note: Separate entries will be created for each selected commission type.</small>
          </div>
        </div>

        <!-- Preview Section -->
        <div class="row" v-if="form.intake && form.commissionTypes.length > 0">
          <div class="col-12 mb-3">
            <div class="alert alert-info">
              <h6>Preview: {{ previewEntriesCount }} entries will be created</h6>
              <ul class="mb-0">
                <li v-for="preview in previewEntries" :key="preview.key">
                  {{ preview.text }}
                </li>
              </ul>
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

.alert-info ul {
  max-height: 150px;
  overflow-y: auto;
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

const data_entries = ref([]);
const commissionOptions = ['Net', 'Gross', 'Standard'];
const errors = ref({});
const isLoading = ref(false);
const showIntakeError = ref(false);

// --- LIFECYCLE HOOKS ---
onMounted(async () => {
  try {
    await Promise.all([fetchIntakes()]);
  } catch (error) {
    console.error("Error during onMounted:", error);
    Swal.fire('Error', 'Failed to load initial data. Please refresh the page.', 'error');
  }
});

async function fetchIntakes() {
  try {
    const response = await axios.get('/api/intakes');
    console.log("Response from server:", response);

    if (response.data && Array.isArray(response.data)) {
      data_entries.value = response.data;
      console.log("Intakes fetched successfully:", data_entries.value);
      showIntakeError.value = false;
    } else {
      console.error("Invalid intake data received:", response.data);
      showIntakeError.value = true;
      Swal.fire('Error', 'Invalid intake data received from the server.', 'error');
      throw new Error('Invalid intake data');
    }
  } catch (error) {
    console.error("Failed to fetch intakes:", error);

    if (error.response) {
      console.error("Error response data:", error.response.data);
      console.error("Error response status:", error.response.status);
      console.error("Error response headers:", error.response.headers);
    }

    showIntakeError.value = true;
    Swal.fire('Error', 'Could not load intake data. Please refresh the page.', 'error');
    throw error;
  }
}

// --- COMPUTED PROPERTIES ---
const commissionDropdownText = computed(() => {
  if (form.value.commissionTypes.length === 0) {
    return 'Select Commission Types';
  }
  return form.value.commissionTypes.join(', ');
});

// Computed properties to get unique values
const uniqueUniversities = computed(() => {
  const universities = data_entries.value
    .map(entry => entry.newUniversity)
    .filter(university => university && university.trim() !== ''); // Remove empty/null values
  
  // Remove duplicates using Set and sort alphabetically
  return [...new Set(universities)].sort();
});

const uniqueIntakes = computed(() => {
  // Month mapping for normalization and ordering
  const monthMap = {
    'jan': 'January', 'january': 'January',
    'feb': 'February', 'february': 'February',
    'mar': 'March', 'march': 'March',
    'apr': 'April', 'april': 'April',
    'may': 'May',
    'jun': 'June', 'june': 'June',
    'jul': 'July', 'july': 'July',
    'aug': 'August', 'august': 'August',
    'sep': 'September', 'sept': 'September', 'september': 'September',
    'oct': 'October', 'october': 'October',
    'nov': 'November', 'november': 'November',
    'dec': 'December', 'december': 'December'
  };

  const monthOrder = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  const intakes = data_entries.value
    .map(entry => entry.newIntake)
    .filter(intake => intake && intake.trim() !== '')
    .flatMap(intake => {
      // Split comma-separated intakes and trim each one
      return intake.split(',').map(i => i.trim()).filter(i => i !== '');
    })
    .map(intake => {
      // Normalize month names
      const lowerIntake = intake.toLowerCase();
      return monthMap[lowerIntake] || intake; // Use original if not a month
    });
  
  // Remove duplicates
  const uniqueIntakes = [...new Set(intakes)];
  
  // Sort months first (in chronological order), then other intakes alphabetically
  return uniqueIntakes.sort((a, b) => {
    const aMonthIndex = monthOrder.indexOf(a);
    const bMonthIndex = monthOrder.indexOf(b);
    
    // Both are months
    if (aMonthIndex !== -1 && bMonthIndex !== -1) {
      return aMonthIndex - bMonthIndex;
    }
    
    // Only a is a month (months come first)
    if (aMonthIndex !== -1) return -1;
    
    // Only b is a month (months come first)
    if (bMonthIndex !== -1) return 1;
    
    // Neither are months, sort alphabetically
    return a.localeCompare(b);
  });
});

// Preview what entries will be created
const previewEntries = computed(() => {
  if (!form.value.intake || form.value.commissionTypes.length === 0) {
    return [];
  }

  // Month mapping for normalization
  const monthMap = {
    'jan': 'January', 'january': 'January',
    'feb': 'February', 'february': 'February',
    'mar': 'March', 'march': 'March',
    'apr': 'April', 'april': 'April',
    'may': 'May',
    'jun': 'June', 'june': 'June',
    'jul': 'July', 'july': 'July',
    'aug': 'August', 'august': 'August',
    'sep': 'September', 'sept': 'September', 'september': 'September',
    'oct': 'October', 'october': 'October',
    'nov': 'November', 'november': 'November',
    'dec': 'December', 'december': 'December'
  };

  // Split intake and normalize month names
  const intakeValues = form.value.intake.split(',')
    .map(intake => intake.trim())
    .filter(intake => intake !== '')
    .map(intake => {
      const lowerIntake = intake.toLowerCase();
      return monthMap[lowerIntake] || intake; // Use original if not a month
    });

  // Remove duplicates
  const uniqueIntakeValues = [...new Set(intakeValues)];
  
  const previews = [];
  
  uniqueIntakeValues.forEach(intake => {
    form.value.commissionTypes.forEach(commissionType => {
      const value = form.value.commissionValues[commissionType] || 0;
      previews.push({
        key: `${intake}-${commissionType}`,
        text: `${intake} - ${commissionType}: ${value}%`
      });
    });
  });
  
  return previews;
});

const previewEntriesCount = computed(() => {
  return previewEntries.value.length;
});

// --- METHODS ---
const handleSubmit = async () => {
  isLoading.value = true;
  errors.value = {};

  try {
    // Validate commission types are selected
    if (form.value.commissionTypes.length === 0) {
      errors.value.commissionTypes = ['Please select at least one commission type.'];
      Swal.fire('Validation Error', 'Please select at least one commission type.', 'error');
      return;
    }

    // Validate commission values
    const missingValues = form.value.commissionTypes.filter(type => 
      !form.value.commissionValues[type] || 
      form.value.commissionValues[type] <= 0 || 
      isNaN(form.value.commissionValues[type])
    );

    if (missingValues.length > 0) {
      errors.value.commissionValues = [`Please provide valid values for: ${missingValues.join(', ')}`];
      Swal.fire('Validation Error', 'Please provide valid values for all selected commission types.', 'error');
      return;
    }

    // Prepare the data to send
    const submissionData = {
      university: form.value.university,
      product: form.value.product,
      intake: form.value.intake,
      has_progressive_commission: form.value.has_progressive_commission,
      progressive_commission: form.value.has_progressive_commission === 'yes' ? form.value.progressive_commission : null,
      has_bonus_commission: form.value.has_bonus_commission,
      bonus_commission: form.value.has_bonus_commission === 'yes' ? form.value.bonus_commission : null,
      has_incentive_commission: form.value.has_incentive_commission,
      incentive_commission: form.value.has_incentive_commission === 'yes' ? form.value.incentive_commission : null,
      commissionTypes: form.value.commissionTypes, // Array of selected types
      commissionValues: form.value.commissionValues // Object with type -> value mapping
    };

    // Debug: Log the data being sent
    console.log('Submitting data:', submissionData);

    // Send the request
    const response = await axios.post('/api/commissions', submissionData, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    console.log('Response:', response.data);

    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Commission entry created successfully!',
      timer: 3000,
      showConfirmButton: false,
    });

    resetForm();

  } catch (error) {
    console.error('Submission failed:', error);
    
    if (error.response) {
      console.error('Error response:', error.response.data);
      
      if (error.response.status === 422) {
        // Validation errors
        if (error.response.data.errors) {
          errors.value = error.response.data.errors;
        }
        Swal.fire('Validation Error', error.response.data.message || 'Please check the form for errors.', 'error');
      } else {
        Swal.fire('Error', error.response.data.message || 'An unexpected error occurred. Please try again.', 'error');
      }
    } else {
      Swal.fire('Error', 'Network error. Please check your connection and try again.', 'error');
    }
  } finally {
    isLoading.value = false;
  }
};



const resetForm = () => {
  form.value = {
    university: '',
    product: '',
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