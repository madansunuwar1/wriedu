<template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex align-items-center">
         <router-link to="/app/partners" class="btn btn-outline-secondary me-3">
  <i class="fas fa-arrow-left"></i>
</router-link>

          <h4 class="mb-0">Create New Partner</h4>
        </div>
        <div class="card-body">
          <form @submit.prevent="submitForm">
            <div class="mb-3">
              <label for="agency_name" class="form-label">
                Agency Name <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                name="agency_name"
                id="agency_name"
                class="form-control"
                :class="{ 'is-invalid': errors.agency_name }"
                v-model="partner.agency_name"
                placeholder="Enter agency name"
              />
              <div v-if="errors.agency_name" class="invalid-feedback">
                {{ errors.agency_name[0] }}
              </div>
            </div>

            <div class="mb-3">
              <label for="Address" class="form-label">
                Address <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                name="Address"
                id="Address"
                class="form-control"
                :class="{ 'is-invalid': errors.Address }"
                v-model="partner.Address"
                placeholder="Enter Address"
              />
              <div v-if="errors.Address" class="invalid-feedback">
                {{ errors.Address[0] }}
              </div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">
                Email Address <span class="text-danger">*</span>
              </label>
              <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                :class="{ 'is-invalid': errors.email }"
                v-model="partner.email"
                placeholder="Enter email address"
              />
              <div v-if="errors.email" class="invalid-feedback">
                {{ errors.email[0] }}
              </div>
            </div>

            <div class="mb-3">
              <label for="contact_no" class="form-label">
                Contact Number <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                name="contact_no"
                id="contact_no"
                class="form-control"
                :class="{ 'is-invalid': errors.contact_no }"
                v-model="partner.contact_no"
                placeholder="Enter contact number"
              />
              <div v-if="errors.contact_no" class="invalid-feedback">
                {{ errors.contact_no[0] }}
              </div>
            </div>

            <div class="mb-3">
              <label for="agency_counselor_email" class="form-label">
                Agency Counselor Email <span class="text-danger">*</span>
              </label>
              <input
                type="email"
                name="agency_counselor_email"
                id="agency_counselor_email"
                class="form-control"
                :class="{ 'is-invalid': errors.agency_counselor_email }"
                v-model="partner.agency_counselor_email"
                placeholder="Enter agency counselor email"
              />
              <div v-if="errors.agency_counselor_email" class="invalid-feedback">
                {{ errors.agency_counselor_email[0] }}
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="#" class="btn btn-secondary" @click.prevent="goBack">
                Cancel
              </a>
              <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                <i class="fas fa-save me-2"></i>
                <span v-if="isSubmitting">Creating...</span>
                <span v-else>Create Partner</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      partner: {
        agency_name: '',
        Address: '', // Changed from 'Address' to 'address'
        email: '',
        contact_no: '',
        agency_counselor_email: '',
      },
      errors: {},
      isSubmitting: false,
    };
  },
  methods: {
    goBack() {
      console.log('Go back');
      // Implement your back navigation logic here
      // For example: this.$router.go(-1) or this.$router.push('/partners')
    },
    
    async submitForm() {
  // Clear previous errors
  this.errors = {};
  this.isSubmitting = true;

  // Debug: Log the data being sent
  console.log('Data being sent to server:', this.partner);
  console.log('Address field value:', this.partner.Address);

  try {
    // Use POST request for creating new partner
    const response = await axios.post('/api/partners', this.partner);
    
    console.log('Partner created successfully:', response.data);
    
    // Show success message (you can use a toast library or alert)
    alert('Partner created successfully!');
    
    // Optionally redirect to partners list or clear form
    this.resetForm();
    // Or redirect: this.$router.push('/partners');
    
  } catch (error) {
    console.error('Full error object:', error);
    console.error('Error response:', error.response);
    console.error('Error response data:', error.response?.data);
    
    if (error.response && error.response.data && error.response.data.errors) {
      this.errors = error.response.data.errors;
      console.log('Validation errors:', this.errors);
    } else if (error.response && error.response.data && error.response.data.message) {
      alert('Error: ' + error.response.data.message);
    } else {
      alert('An error occurred while creating the partner');
    }
  } finally {
    this.isSubmitting = false;
  }
},
    
    resetForm() {
      this.partner = {
        agency_name: '',
        Address: '', // Changed from 'Address' to 'address'
        email: '',
        contact_no: '',
        agency_counselor_email: '',
      };
      this.errors = {};
    }
  }
};
</script>

<style scoped>
/* Add any custom styles here if needed */
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>