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
                Edit Role
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
                  <h2 class="h5 mb-1 fw-semibold">Edit Role</h2>
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

              <!-- Role Basic Info -->
              <div class="mb-4">
                <div class="row">
                  <div class="col-md-6">
                    <label for="roleName" class="form-label fw-semibold">Role Name</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="roleName"
                      v-model="form.name"
                      placeholder="Enter role name"
                    >
                  </div>
                  <div class="col-md-6">
                    <label for="roleDescription" class="form-label fw-semibold">Description</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="roleDescription"
                      v-model="form.description"
                      placeholder="Enter role description"
                    >
                  </div>
                </div>
              </div>

              <!-- Loading State -->
              <div v-if="loadingPermissions" class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">LOADING PERMISSIONS...</p>
              </div>

              <!-- Permissions Section -->
              <div v-else>
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
                    <div v-if="Object.keys(groupedPermissions).length > 0" class="permissions-two-column">
                      <!-- Left Column -->
                      <div class="permission-column">
                        <div v-for="(category, index) in leftColumnCategories" :key="category" class="permission-category">
                          <div class="permission-card">
                            <div class="permission-header" @click="toggleAccordion(index)">
                              <div class="d-flex justify-content-between align-items-center">
                                <div>
                                  <i class="bi bi-folder me-2"></i>
                                  {{ formatCategoryName(category) }}
                                </div>
                                <div class="d-flex align-items-center">
                                  <span class="badge bg-secondary me-2">
                                    {{ getCategorySelectedCount(category) }}/{{ groupedPermissions[category].length }}
                                  </span>
                                  <i class="bi chevron-icon" :class="{
                                    'bi-chevron-down': !isAccordionOpen(index),
                                    'bi-chevron-up': isAccordionOpen(index)
                                  }"></i>
                                </div>
                              </div>
                            </div>

                            <div class="permission-body" :class="{ 'expanded': isAccordionOpen(index) }">
                              <div class="permission-content">
                                <div class="permission-list">
                                  <div v-for="permission in groupedPermissions[category]" :key="permission.id" class="permission-item">
                                    <div class="form-check">
                                      <input
                                        class="form-check-input"
                                        type="checkbox"
                                        :value="permission.id"
                                        :id="'permission_' + permission.id"
                                        v-model="selectedPermissionIds"
                                      />
                                      <label class="form-check-label" :for="'permission_' + permission.id">
                                        <div class="permission-details">
                                          <div class="permission-name">
                                            <i class="bi bi-key me-1"></i>
                                            {{ formatPermissionName(permission.name) }}
                                          </div>
                                          <div class="permission-description text-muted">
                                            {{ permission.description || 'Allows ' + formatPermissionDescription(permission.name) }}
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
                        <div v-for="(category, index) in rightColumnCategories" :key="category" class="permission-category">
                          <div class="permission-card">
                            <div class="permission-header" @click="toggleAccordion(leftColumnCategories.length + index)">
                              <div class="d-flex justify-content-between align-items-center">
                                <div>
                                  <i class="bi bi-folder me-2"></i>
                                  {{ formatCategoryName(category) }}
                                </div>
                                <div class="d-flex align-items-center">
                                  <span class="badge bg-secondary me-2">
                                    {{ getCategorySelectedCount(category) }}/{{ groupedPermissions[category].length }}
                                  </span>
                                  <i class="bi chevron-icon" :class="{
                                    'bi-chevron-down': !isAccordionOpen(leftColumnCategories.length + index),
                                    'bi-chevron-up': isAccordionOpen(leftColumnCategories.length + index)
                                  }"></i>
                                </div>
                              </div>
                            </div>

                            <div class="permission-body" :class="{ 'expanded': isAccordionOpen(leftColumnCategories.length + index) }">
                              <div class="permission-content">
                                <div class="permission-list">
                                  <div v-for="permission in groupedPermissions[category]" :key="permission.id" class="permission-item">
                                    <div class="form-check">
                                      <input
                                        class="form-check-input"
                                        type="checkbox"
                                        :value="permission.id"
                                        :id="'permission_' + permission.id"
                                        v-model="selectedPermissionIds"
                                      />
                                      <label class="form-check-label" :for="'permission_' + permission.id">
                                        <div class="permission-details">
                                          <div class="permission-name">
                                            <i class="bi bi-key me-1"></i>
                                            {{ formatPermissionName(permission.name) }}
                                          </div>
                                          <div class="permission-description text-muted">
                                            {{ permission.description || 'Allows ' + formatPermissionDescription(permission.name) }}
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

                    <div v-else class="alert alert-warning">
                      <i class="bi bi-exclamation-triangle me-2"></i>
                      No permissions found. Please check your permissions setup.
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-secondary" @click="resetForm">
                  <i class="bi bi-arrow-clockwise me-1"></i>
                  Reset
                </button>
                <button 
                  type="button" 
                  class="btn btn-primary" 
                  @click="submitForm"
                  :disabled="isSubmitting"
                >
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status"></span>
                  <i v-else class="bi bi-check-circle me-1"></i>
                  {{ isSubmitting ? 'Updating...' : 'Update Role' }}
                </button>
              </div>
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
      },
      allPermissions: [],
      rolePermissions: [], // Store the permissions that the role currently has
      selectedPermissionIds: [], // Store selected permission IDs
      categoryMap: {
        1: 'Applications',
        2: 'Leads',
        6: 'System Administration',
        7: 'User Management',
        8: 'Data Entries',
        9: 'Enquiries',
        10: 'Uploads',
        11: 'Products',
        12: 'Finances',
        13: 'Notices'
      },
      loadingPermissions: true,
      error: '',
      isSubmitting: false,
      openAccordions: [0],
    };
  },
  computed: {
    groupedPermissions() {
      const grouped = {};

      this.allPermissions.forEach(permission => {
        const category = permission.category_id || 'Uncategorized';

        if (!grouped[category]) {
          grouped[category] = [];
        }
        grouped[category].push(permission);
      });

      return grouped;
    },
    permissionCategories() {
      return Object.keys(this.groupedPermissions).sort();
    },
    leftColumnCategories() {
      return this.permissionCategories.filter((_, index) => index % 2 === 0);
    },
    rightColumnCategories() {
      return this.permissionCategories.filter((_, index) => index % 2 === 1);
    }
  },
  methods: {
    async fetchPermissions() {
      this.loadingPermissions = true;
      this.error = '';
      
      try {
        const roleId = this.$route.params.id;
        
        try {
          // First, try to get role data
          const roleResponse = await axios.get(`/api/roles/${roleId}`);
          
          // Set role basic info
          this.form.name = roleResponse.data.name || '';
          this.form.description = roleResponse.data.description || '';
          this.rolePermissions = roleResponse.data.permissions || [];
          
          // Try to get all permissions from different possible endpoints
          let allPermissionsResponse;
          
          try {
            allPermissionsResponse = await axios.get('/api/permissions/all');
          } catch (e1) {
            try {
              allPermissionsResponse = await axios.get('/api/all-permissions');
            } catch (e2) {
              try {
                allPermissionsResponse = await axios.get('/api/admin/permissions');
              } catch (e3) {
                try {
                  // Try getting from roles create endpoint
                  allPermissionsResponse = await axios.get('/api/roles/create');
                } catch (e4) {
                  // Last resort: use only the permissions from the role itself
                  console.warn('Could not fetch all permissions, using role permissions only');
                  this.allPermissions = roleResponse.data.all_permissions || roleResponse.data.available_permissions || [];
                  
                  if (this.allPermissions.length === 0) {
                    throw new Error('No permissions data available. Please check your API configuration.');
                  }
                  
                  this.selectedPermissionIds = this.rolePermissions.map(permission => permission.id);
                  return; // Exit early since we have the data we need
                }
              }
            }
          }
          
          // If we got here, one of the API calls succeeded
          this.allPermissions = allPermissionsResponse.data.permissions || allPermissionsResponse.data || [];
          this.selectedPermissionIds = this.rolePermissions.map(permission => permission.id);
          
        } catch (error) {
          console.error('Error in fetchPermissions:', error);
          this.error = 'Failed to load permissions. Please check your API configuration.';
        }
        
      } catch (error) {
        console.error('Error fetching data:', error);
        this.error = error.response?.data?.message || 'Failed to fetch data. Please check your connection and try again.';
      } finally {
        this.loadingPermissions = false;
      }
    },
    formatPermissionName(name) {
      return name.includes(':') ? name.split(':')[1] : name;
    },
    formatCategoryName(category) {
      // Use categoryMap if available, otherwise format the category
      if (this.categoryMap && this.categoryMap[category]) {
        return this.categoryMap[category];
      }
      return category.toString().replace(/_/g, ' ').toUpperCase();
    },
    formatPermissionDescription(name) {
      const action = this.formatPermissionName(name);
      return action.split('_').join(' ').toLowerCase();
    },
    getCategorySelectedCount(category) {
      if (!this.groupedPermissions[category]) return 0;
      return this.groupedPermissions[category].filter(
        permission => this.selectedPermissionIds.includes(permission.id)
      ).length;
    },
    selectAllPermissions() {
      this.selectedPermissionIds = this.allPermissions.map(permission => permission.id);
    },
    clearAllPermissions() {
      this.selectedPermissionIds = [];
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
    validateForm() {
      if (!this.form.name.trim()) {
        this.error = 'Role name is required.';
        return false;
      }
      if (this.selectedPermissionIds.length === 0) {
        this.error = 'Please select at least one permission for this role.';
        return false;
      }
      return true;
    },
    async submitForm() {
      if (!this.validateForm()) return;

      this.isSubmitting = true;
      this.error = '';

      try {
        const roleId = this.$route.params.id;
        const payload = {
          name: this.form.name,
          description: this.form.description,
          permissions: this.selectedPermissionIds // Send selected permission IDs
        };
        
        console.log('Submitting payload:', payload);
        
        const response = await axios.put(`/api/roles/${roleId}`, payload);
        
        // Navigate back to roles list or show success message
        this.$router.push('/app/roles');
      } catch (error) {
        console.error('Error updating role:', error);
        this.error = error.response?.data?.message || 'An error occurred while updating the role. Please try again.';
      } finally {
        this.isSubmitting = false;
      }
    },
    resetForm() {
      // Reset to original fetched data
      this.selectedPermissionIds = this.rolePermissions.map(permission => permission.id);
      this.error = '';
    }
  },
  created() {
    this.fetchPermissions();
  }
};
</script>

<style>
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
  max-height: 800px;
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