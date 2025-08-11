<template>
  <div class="container-fluid bg-light min-vh-100 py-4">
    <div class="container">
      <!-- Header Section -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-person-gear me-3 text-primary" style="font-size: 2rem;"></i>
            <div>
              <h1 class="h3 mb-1 text-dark fw-bold">Role Management</h1>
              <p class="text-muted mb-0">
                Create and configure user roles with specific permissions
              </p>
            </div>
          </div>

          <!-- Breadcrumb -->
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="#" class="text-decoration-none">
                  <i class="bi bi-house me-1"></i>Dashboard
                </a>
              </li>
              <li class="breadcrumb-item">
                <a href="#" class="text-decoration-none">
                  <i class="bi bi-people me-1"></i>User Management
                </a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Create Role
              </li>
            </ol>
          </nav>
        </div>
      </div>

      <div class="row justify-content-center">
        <!-- Main Content -->
        <div class="col-lg-8">
          <!-- Main Form Card -->
          <div class="card shadow-lg mb-4">
            <div class="card-header bg-white border-bottom py-4">
              <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                  <i class="bi bi-plus-circle text-primary"></i>
                </div>
                <div>
                  <h2 class="h5 mb-1 fw-semibold">Create New Role</h2>
                  <p class="text-muted small mb-0">
                    Define role properties and assign appropriate permissions
                  </p>
                </div>
              </div>
            </div>

            <div class="card-body p-4">
              <!-- Error Alert -->
              <div v-if="error" class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>
                  <div class="flex-grow-1">{{ error }}</div>
                  <button type="button" class="btn-close" @click="error = ''" aria-label="Close"></button>
                </div>
              </div>

              <form @submit.prevent="submitForm" class="needs-validation" novalidate>
                <!-- Basic Information Section -->
                <div class="mb-5">
                  <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    <h3 class="h6 mb-0 fw-semibold text-dark">Basic Information</h3>
                  </div>

                  <div class="row g-3">
                    <div class="col-12">
                      <label for="name" class="form-label fw-medium">
                        <i class="bi bi-tag me-1"></i>Role Name <span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        class="form-control form-control-lg border-2"
                        id="name"
                        v-model="form.name"
                        placeholder="Enter role name (e.g., Administrator, Editor)"
                        required
                      />
                      <div class="invalid-feedback">
                        <i class="bi bi-exclamation-circle me-1"></i>Please provide a valid role name.
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="description" class="form-label fw-medium">
                        <i class="bi bi-card-text me-1"></i>Description
                      </label>
                      <textarea
                        class="form-control border-2"
                        id="description"
                        v-model="form.description"
                        rows="4"
                        placeholder="Describe the role's purpose and responsibilities..."
                      ></textarea>
                      <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>Provide a clear description of this role's responsibilities
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Permissions Section -->
                <div class="mb-5">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <i class="bi bi-shield-check text-primary me-2"></i>
                      <h3 class="h6 mb-0 fw-semibold text-dark">PERMISSIONS & ACCESS CONTROL</h3>
                    </div>
                    <div>
                      <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="selectAllPermissions">
                        <i class="bi bi-check-all"></i> SELECT ALL
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" @click="clearAllPermissions">
                        <i class="bi bi-x-circle"></i> CLEAR ALL
                      </button>
                    </div>
                  </div>

                  <div class="permissions-container">
                    <div v-if="Object.keys(permissions).length === 0" class="text-center py-3">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                      <p class="mt-2 text-muted">LOADING PERMISSIONS...</p>
                    </div>

                    <div v-else class="permissions-container">
                      <div class="permissions-two-column">
                        <!-- Left Column -->
                        <div class="permission-column">
                          <div v-for="(categoryPermissions, category, categoryIndex) in leftColumnPermissions" :key="category" class="permission-category">
                            <div class="permission-card">
                              <div class="permission-header" @click="toggleAccordion(categoryIndex)">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <i class="bi bi-folder me-2"></i>
                                    {{ category.toUpperCase() }}
                                  </div>
                                  <div class="d-flex align-items-center">
                                    <span class="badge bg-secondary me-2">{{ getCategorySelectedCount(categoryPermissions) }}/{{ categoryPermissions.length }}</span>
                                    <i class="bi chevron-icon" :class="{ 'bi-chevron-down': !isAccordionOpen(categoryIndex), 'bi-chevron-up': isAccordionOpen(categoryIndex) }"></i>
                                  </div>
                                </div>
                              </div>

                              <div class="permission-body" :class="{ 'expanded': isAccordionOpen(categoryIndex) }">
                                <div class="permission-content">
                                  <div class="permission-list">
                                    <div v-for="permission in categoryPermissions" :key="permission.id" class="permission-item">
                                      <div class="form-check">
                                        <input 
                                          class="form-check-input" 
                                          type="checkbox" 
                                          :value="permission.id" 
                                          :id="'permission_' + permission.id" 
                                          v-model="form.permissions" 
                                        />
                                        <label class="form-check-label" :for="'permission_' + permission.id">
                                          <div class="permission-details">
                                            <div class="permission-name">
                                              <i class="bi bi-key me-1"></i>
                                              {{ permission.name }}
                                            </div>
                                            <div class="permission-description text-muted">
                                              {{ permission.description || 'Permission for ' + permission.name.toLowerCase() }}
                                            </div>
                                          </div>
                                        </label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Right Column -->
                        <div class="permission-column">
                          <div v-for="(categoryPermissions, category, categoryIndex) in rightColumnPermissions" :key="category" class="permission-category">
                            <div class="permission-card">
                              <div class="permission-header" @click="toggleAccordion(getAdjustedIndex(categoryIndex))">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <i class="bi bi-folder me-2"></i>
                                    {{ category.toUpperCase() }}
                                  </div>
                                  <div class="d-flex align-items-center">
                                    <span class="badge bg-secondary me-2">{{ getCategorySelectedCount(categoryPermissions) }}/{{ categoryPermissions.length }}</span>
                                    <i class="bi chevron-icon" :class="{ 'bi-chevron-down': !isAccordionOpen(getAdjustedIndex(categoryIndex)), 'bi-chevron-up': isAccordionOpen(getAdjustedIndex(categoryIndex)) }"></i>
                                  </div>
                                </div>
                              </div>

                              <div class="permission-body" :class="{ 'expanded': isAccordionOpen(getAdjustedIndex(categoryIndex)) }">
                                <div class="permission-content">
                                  <div class="permission-list">
                                    <div v-for="permission in categoryPermissions" :key="permission.id" class="permission-item">
                                      <div class="form-check">
                                        <input 
                                          class="form-check-input" 
                                          type="checkbox" 
                                          :value="permission.id" 
                                          :id="'permission_' + permission.id" 
                                          v-model="form.permissions" 
                                        />
                                        <label class="form-check-label" :for="'permission_' + permission.id">
                                          <div class="permission-details">
                                            <div class="permission-name">
                                              <i class="bi bi-key me-1"></i>
                                              {{ permission.name }}
                                            </div>
                                            <div class="permission-description text-muted">
                                              {{ permission.description || 'Permission for ' + permission.name.toLowerCase() }}
                                            </div>
                                          </div>
                                        </label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                      <strong>SELECTED PERMISSIONS:</strong>
                      {{ form.permissions.length }} permission(s) selected.
                      Users with this role will have access to the selected features and functionalities.
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary me-2" :disabled="isSubmitting">
                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-check-circle me-1"></i>
                    <span v-if="isSubmitting">Creating Role...</span>
                    <span v-else>Create Role</span>
                  </button>
                  <button type="button" class="btn btn-secondary" @click="resetForm">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset Form
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Help Card -->
          <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
              <i class="bi bi-lightbulb me-2"></i>Best Practices
            </div>
            <div class="card-body">
              <ul>
                <li><i class="bi bi-check-circle text-success me-1"></i>Use descriptive role names that clearly indicate the user's responsibilities.</li>
                <li><i class="bi bi-check-circle text-success me-1"></i>Follow the principle of least privilege - grant only necessary permissions.</li>
                <li><i class="bi bi-check-circle text-success me-1"></i>Regularly review and update role permissions as business needs change.</li>
                <li><i class="bi bi-check-circle text-success me-1"></i>Document role purposes in the description field for future reference.</li>
              </ul>
            </div>
          </div>
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
      form: {
        name: '',
        description: '',
        permissions: [],
      },
      permissions: {},
      error: '',
      isSubmitting: false,
      openAccordions: [],
    };
  },
  created() {
    this.fetchPermissions();
  },
  computed: {
    leftColumnPermissions() {
      const entries = Object.entries(this.permissions);
      const leftEntries = entries.filter((_, index) => index % 2 === 0);
      return Object.fromEntries(leftEntries);
    },
    rightColumnPermissions() {
      const entries = Object.entries(this.permissions);
      const rightEntries = entries.filter((_, index) => index % 2 === 1);
      return Object.fromEntries(rightEntries);
    },
  },
  methods: {
    fetchPermissions() {
      axios.get('/api/roles/create')
        .then((response) => {
          this.permissions = response.data;
          // Open first accordion by default
          if (Object.keys(this.permissions).length > 0) {
            this.openAccordions = [0];
          }
        })
        .catch((error) => {
          console.error('Error fetching permissions:', error);
          this.error = 'Failed to fetch permissions. Please check your connection and try again.';
        });
    },
    submitForm() {
      if (!this.validateForm()) return;

      this.isSubmitting = true;
      this.error = '';

      axios.post('/api/roles', this.form)
        .then((response) => {
          this.$router.push('/app/roles');
        })
        .catch((error) => {
          this.error = error.response?.data?.message || 'An error occurred while creating the role. Please try again.';
        })
        .finally(() => {
          this.isSubmitting = false;
        });
    },
    validateForm() {
      if (!this.form.name.trim()) {
        this.error = 'Role name is required.';
        return false;
      }
      if (this.form.permissions.length === 0) {
        this.error = 'Please select at least one permission for this role.';
        return false;
      }
      return true;
    },
    selectAllPermissions() {
      const allPermissionIds = [];
      Object.values(this.permissions).forEach((categoryPermissions) => {
        categoryPermissions.forEach((permission) => {
          allPermissionIds.push(permission.id);
        });
      });
      this.form.permissions = allPermissionIds;
    },
    clearAllPermissions() {
      this.form.permissions = [];
    },
    getCategorySelectedCount(categoryPermissions) {
      return categoryPermissions.filter((permission) => this.form.permissions.includes(permission.id)).length;
    },
    resetForm() {
      this.form = {
        name: '',
        description: '',
        permissions: [],
      };
      this.error = '';
    },
    toggleAccordion(index) {
      if (this.openAccordions.includes(index)) {
        this.openAccordions = this.openAccordions.filter(i => i !== index);
      } else {
        this.openAccordions.push(index);
      }
    },
    isAccordionOpen(index) {
      return this.openAccordions.includes(index);
    },
    getAdjustedIndex(rightColumnIndex) {
      // Adjust index for right column to maintain unique indices
      const leftColumnCount = Object.keys(this.leftColumnPermissions).length;
      return rightColumnIndex + leftColumnCount;
    },
  },
};
</script>

<style scoped>
/* Permissions Container */
.permissions-container {
  width: 100%;
}

/* Two Column Layout */
.permissions-two-column {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.permission-column {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.permission-category {
  width: 100%;
}

/* Permission Card Styling */
.permission-card {
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  overflow: hidden;
  background: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.2s ease;
  height: auto;
  align-self: flex-start;
}

.permission-card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Permission Header */
.permission-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  padding: 1rem;
  cursor: pointer;
  user-select: none;
  transition: background-color 0.2s ease;
}

.permission-header:hover {
  background-color: #e9ecef;
}

/* Permission Body with Smooth Animation */
.permission-body {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.permission-body.expanded {
  max-height: 800px; /* Increased for more content */
  transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.permission-content {
  padding: 1rem;
}

/* Permission List */
.permission-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.permission-item {
  padding: 0.5rem;
  border-radius: 0.25rem;
  transition: background-color 0.2s ease;
}

.permission-item:hover {
  background-color: #f8f9fa;
}

/* Permission Details */
.permission-details {
  margin-left: 0.5rem;
}

.permission-name {
  font-weight: 500;
  color: #212529;
  font-size: 0.875rem;
}

.permission-description {
  font-size: 0.75rem;
  margin-top: 0.25rem;
  line-height: 1.4;
}

/* Form Check Styling */
.form-check {
  display: flex;
  align-items: flex-start;
  min-height: auto;
}

.form-check-input {
  margin-top: 0.125rem;
  flex-shrink: 0;
}

.form-check-label {
  cursor: pointer;
  width: 100%;
}

/* Chevron Icon Animation */
.chevron-icon {
  transition: transform 0.2s ease;
}

/* Badge Styling */
.badge {
  font-size: 0.7rem;
  font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
  .permissions-two-column {
    flex-direction: column;
  }
  
  .permission-column {
    width: 100%;
  }
  
  .permission-header {
    padding: 0.75rem;
  }
  
  .permission-content {
    padding: 0.75rem;
  }
}

/* Loading State */
.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}

/* Alert Styling */
.alert {
  border-radius: 0.375rem;
}

/* Button Hover Effects */
.btn:hover {
  transform: translateY(-1px);
  transition: transform 0.2s ease;
}

.btn:active {
  transform: translateY(0);
}
</style>