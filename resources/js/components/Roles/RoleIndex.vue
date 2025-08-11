<template>
  <div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h2 mb-1 text-dark fw-semibold">
              <i class="bi bi-shield-check me-2 text-primary"></i>
              Roles Management
            </h1>
            <p class="text-muted mb-0">Manage user roles and permissions</p>
          </div>
          <button @click="navigateToCreate" class="btn btn-primary btn-lg shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>
            Create New Role
          </button>
        </div>
      </div>
    </div>

    <!-- Alerts Section -->
    <div class="row mb-4" v-if="successMessage || errorMessage">
      <div class="col-12">
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>
          {{ successMessage }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          {{ errorMessage }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
              <i class="bi bi-people text-primary fs-4"></i>
            </div>
            <h5 class="card-title text-muted mb-1">Total Roles</h5>
            <h3 class="text-dark mb-0">{{ roles.length }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
              <i class="bi bi-shield-check text-success fs-4"></i>
            </div>
            <h5 class="card-title text-muted mb-1">Active Roles</h5>
            <h3 class="text-dark mb-0">{{ roles.filter(role => role.status === 'active').length || roles.length }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
              <i class="bi bi-key text-info fs-4"></i>
            </div>
            <h5 class="card-title text-muted mb-1">Total Permissions</h5>
            <h3 class="text-dark mb-0">{{ totalPermissions }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
              <i class="bi bi-graph-up text-warning fs-4"></i>
            </div>
            <h5 class="card-title text-muted mb-1">Avg Permissions</h5>
            <h3 class="text-dark mb-0">{{ averagePermissions }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Card -->
    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-semibold">
                <i class="bi bi-table me-2 text-primary"></i>
                Roles Overview
              </h5>
              <div class="d-flex gap-2 align-items-center">
                
                <div class="d-flex align-items-center">
                  <span class="me-2">Rows per page:</span>
                  <input type="number" v-model.number="perPage" @change="handlePerPageChange" class="form-control form-control-sm" style="width: 70px;" min="1">
                </div>
              </div>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="border-0 text-muted fw-semibold py-3 ps-4">
                      <i class="bi bi-person-badge me-2"></i>Role Name
                    </th>
                    <th class="border-0 text-muted fw-semibold py-3">
                      <i class="bi bi-card-text me-2"></i>Description
                    </th>
                    <th class="border-0 text-muted fw-semibold py-3 text-center">
                      <i class="bi bi-key me-2"></i>Permissions
                    </th>
                    <th class="border-0 text-muted fw-semibold py-3 text-center">
                      <i class="bi bi-activity me-2"></i>Status
                    </th>
                    <th class="border-0 text-muted fw-semibold py-3 text-center pe-4">
                      <i class="bi bi-gear me-2"></i>Actions
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="role in paginatedRoles" :key="role.id" class="border-bottom">
                    <td class="py-3 ps-4">
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                          <i class="bi bi-person-badge text-primary"></i>
                        </div>
                        <div>
                          <h6 class="mb-0 text-dark fw-semibold">{{ role.name }}</h6>
                          <small class="text-muted">ID: {{ role.id }}</small>
                        </div>
                      </div>
                    </td>
                    <td class="py-3">
                      <span class="text-muted">{{ role.description || 'No description provided' }}</span>
                    </td>
                    <td class="py-3 text-center">
                      <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2">
                        <i class="bi bi-key me-1"></i>
                        {{ role.permissions_count }}
                      </span>
                    </td>
                    <td class="py-3 text-center">
                      <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i>
                        Active
                      </span>
                    </td>
                    <td class="py-3 text-center pe-4">
                      <div class="btn-group" role="group">
                       <router-link 
                          :to="{ name: 'RoleShow', params: { id: role.id } }" 
                          class="btn btn-outline-warning btn-sm" 
                          data-bs-toggle="tooltip" 
                          title="Edit Role">
                          <i class="bi bi-pencil"></i>
                        </router-link>
                        <button @click="deleteRole(role.id)" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete Role">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-if="roles.length === 0" class="text-center py-5">
              <div class="mb-4">
                <i class="bi bi-inbox display-1 text-muted opacity-50"></i>
              </div>
              <h5 class="text-muted mb-3">No roles found</h5>
              <p class="text-muted mb-4">Get started by creating your first role</p>
              <button @click="navigateToCreate" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Create Your First Role
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination Controls -->
    <div class="row mt-4">
      <div class="col-12">
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">Previous</a>
            </li>
            <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
              <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      roles: [],
      successMessage: '',
      errorMessage: '',
      loading: false,
      currentPage: 1,
      perPage: 10, // Default to 10 items per page
    };
  },
  methods: {
    navigateToCreate() {
      try {
        this.$router.push({ name: 'CreateRole' });
      } catch (error) {
        console.log('CreateRole route not found, using fallback navigation');
        this.$router.push('/app/roles/create');
      }
    },
    viewRole(id) {
      try {
        this.$router.push({ name: 'ViewRole', params: { id } });
      } catch (error) {
        console.log('ViewRole route not found, using fallback navigation');
        this.$router.push(`/roles/${id}`);
      }
    },
    editRole(id) {
      try {
        this.$router.push({ name: 'RolesShow', params: { id } });
      } catch (error) {
        console.log('EditRole route not found, using fallback navigation');
        this.$router.push(`/roles/${id}/edit`);
      }
    },
    async fetchRoles() {
      this.loading = true;
      try {
        const response = await axios.get('/api/roles');
        this.roles = response.data;
        this.clearMessages();
      } catch (error) {
        this.showError('Error fetching roles: ' + error.message);
      } finally {
        this.loading = false;
      }
    },
    async deleteRole(id) {
      const confirmed = await this.showConfirmDialog(
        'Delete Role',
        'Are you sure you want to delete this role? This action cannot be undone.'
      );

      if (confirmed) {
        try {
          await axios.delete(`/api/roles/${id}`);
          this.showSuccess('Role deleted successfully');
          this.fetchRoles();
        } catch (error) {
          this.showError('Error deleting role: ' + error.message);
        }
      }
    },
    showSuccess(message) {
      this.successMessage = message;
      this.errorMessage = '';
      setTimeout(() => {
        this.successMessage = '';
      }, 5000);
    },
    showError(message) {
      this.errorMessage = message;
      this.successMessage = '';
      setTimeout(() => {
        this.errorMessage = '';
      }, 5000);
    },
    clearMessages() {
      this.successMessage = '';
      this.errorMessage = '';
    },
    showConfirmDialog(title, message) {
      return new Promise((resolve) => {
        const result = confirm(`${title}\n\n${message}`);
        resolve(result);
      });
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    handlePerPageChange() {
      // Ensure perPage is a valid number and reset to the first page
      if (this.perPage < 1 || isNaN(this.perPage)) {
        this.perPage = 1;
      }
      this.currentPage = 1;
    }
  },
  computed: {
    totalPermissions() {
      return this.roles.reduce((total, role) => total + (role.permissions_count || 0), 0);
    },
    averagePermissions() {
      if (this.roles.length === 0) return 0;
      return Math.round(this.totalPermissions / this.roles.length);
    },
    totalPages() {
      return Math.ceil(this.roles.length / this.perPage);
    },
    paginatedRoles() {
      const start = (this.currentPage - 1) * this.perPage;
      const end = start + parseInt(this.perPage);
      return this.roles.slice(start, end);
    }
  },
  mounted() {
    this.$nextTick(() => {
      try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
          const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
          tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
          });
        }
      } catch (error) {
        console.log('Bootstrap tooltips not available');
      }
    });
    this.fetchRoles();
  }
};
</script>

<style >
.card {
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
}

.btn {
  transition: all 0.2s ease;
}

.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}

.badge {
  font-weight: 500;
  font-size: 0.75rem;
}

.rounded-circle {
  transition: all 0.2s ease;
}

.table tbody tr:hover .rounded-circle {
  transform: scale(1.1);
}

.btn-group .btn-outline-primary:checked,
.btn-group .btn-outline-primary.active {
  background-color: var(--bs-primary);
  border-color: var(--bs-primary);
  color: white;
}

.loading-overlay {
  position: relative;
}

.loading-overlay::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.pagination .page-item.active .page-link {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.pagination .page-item.disabled .page-link {
  pointer-events: none;
  opacity: 0.6;
}

.page-link {
  color: #0d6efd;
}

.page-link:hover {
  color: #0a58ca;
}

@media (max-width: 768px) {
  .container-fluid {
    padding-left: 1rem;
    padding-right: 1rem;
  }

  .btn-group {
    flex-direction: column;
  }

  .d-flex.justify-content-between {
    flex-direction: column;
    gap: 1rem;
  }

  .table-responsive {
    font-size: 0.875rem;
  }
}
</style>