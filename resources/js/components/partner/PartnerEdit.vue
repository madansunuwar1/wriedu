<template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex align-items-center">
          <router-link to="/app/partners" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i>
          </router-link>
          <h4 class="mb-0">Update Partner</h4>
        </div>
        <div class="card-body">
          <form @submit.prevent="confirmUpdate">
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
                v-model="form.agency_name"
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
                v-model="form.Address"
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
                v-model="form.email"
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
                v-model="form.contact_no"
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
                v-model="form.agency_counselor_email"
                placeholder="Enter agency counselor email"
              />
              <div v-if="errors.agency_counselor_email" class="invalid-feedback">
                {{ errors.agency_counselor_email[0] }}
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <router-link to="/partners" class="btn btn-secondary">
                Cancel
              </router-link>
              <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                <i class="fas fa-save me-2"></i>
                {{ isSubmitting ? 'Updating...' : 'Update Partner' }}
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
import Swal from 'sweetalert2';

export default {
  props: {
    id: {
      type: [Number, String],
      required: true
    }
  },
  data() {
    return {
      form: {
        agency_name: '',
        Address: '',
        email: '',
        contact_no: '',
        agency_counselor_email: '',
      },
      errors: {},
      isSubmitting: false,
      isLoading: true,
      // Toast configuration
      Toast: Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
    };
  },
  methods: {
    async fetchPartner() {
      try {
        this.isLoading = true;
        const response = await axios.get(`/api/partners/${this.id}`);
        
        // Populate form with fetched data
        this.form = {
          agency_name: response.data.agency_name || '',
          Address: response.data.Address || response.data.address || '', // Handle both cases
          email: response.data.email || '',
          contact_no: response.data.contact_no || '',
          agency_counselor_email: response.data.agency_counselor_email || '',
        };
        
        console.log('Partner data loaded:', response.data);
        
        
      } catch (error) {
        console.error('Error fetching partner:', error);
        
        if (error.response && error.response.status === 404) {
          this.Toast.fire({
            icon: 'error',
            title: 'Partner not found'
          });
          setTimeout(() => this.$router.push('/partners'), 2000);
        } else {
          this.Toast.fire({
            icon: 'error',
            title: 'Error loading partner data'
          });
        }
      } finally {
        this.isLoading = false;
      }
    },
    
    async submitForm() {
      // Clear previous errors
      this.errors = {};
      this.isSubmitting = true;
      
      // Show loading toast
      this.Toast.fire({
        icon: 'info',
        title: 'Updating partner...'
      });
      
      try {
        const response = await axios.put(`/api/partners/${this.id}`, this.form);
        console.log('Partner updated successfully:', response.data);
        
        // Show success toast
        this.Toast.fire({
          icon: 'success',
          title: 'Partner updated successfully!',
          timer: 4000
        });
        
        // Optionally redirect after showing success message
        // setTimeout(() => {
        //   this.$router.push('/partners');
        // }, 2000);
        
      } catch (error) {
        console.error('Error updating partner:', error);
        
        if (error.response && error.response.status === 422) {
          // Validation errors
          this.errors = error.response.data.errors || {};
          
          // Show validation error toast
          this.Toast.fire({
            icon: 'error',
            title: 'Please fix the validation errors',
            timer: 4000
          });
          
        } else if (error.response && error.response.status === 404) {
          this.Toast.fire({
            icon: 'error',
            title: 'Partner not found'
          });
          setTimeout(() => this.$router.push('/partners'), 2000);
          
        } else {
          this.Toast.fire({
            icon: 'error',
            title: 'Error updating partner. Please try again.',
            timer: 4000
          });
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    // Method to show confirmation before updating
    async confirmUpdate() {
      const result = await Swal.fire({
        title: 'Update Partner?',
        text: 'Are you sure you want to update this partner information?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
      });

      if (result.isConfirmed) {
        this.submitForm();
      }
    },
  },
  
  mounted() {
    this.fetchPartner();
  },
};
</script>

<style scoped>
/* Add any custom styles here if needed */
.card {
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.btn:disabled {
  opacity: 0.6;
}

.form-control:focus {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>