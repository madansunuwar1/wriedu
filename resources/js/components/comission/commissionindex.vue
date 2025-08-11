<template>
  <div class="main-container">
    <div class="widget-content searchable-container list">
      <!-- Header & Controls -->
      <div class="card card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
          <div class="mb-3 mb-md-0">
            <div class="d-flex align-items-center">
              <i class="ti ti-database text-2xl text-[#2e7d32] me-2"></i>
              <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Commission Rates</div>
            </div>
          </div>
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="position-relative" style="width: 300px;">
              <input type="text" class="form-control product-search ps-5" v-model="searchTerm"
                placeholder="Search rates...">
              <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
            </div>
            <router-link to="/app/commissions/create" class="icon-btn btn-success">
              <i class="ti ti-plus fs-5"></i><span class="tooltip-text">Create New</span>
            </router-link>
            <button @click="openImportModal" class="icon-btn btn-outline-primary">
              <i class="ti ti-file-import fs-5"></i><span class="tooltip-text">Import</span>
            </button>
            <button @click="exportData" class="icon-btn btn-outline-primary">
              <i class="ti ti-file-export fs-5"></i><span class="tooltip-text">Export</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Loading Spinner -->
      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>

      <!-- Table Content -->
      <div v-else class="table-responsive mt-4 mb-4 border rounded-1">
        <table class="table text-nowrap mb-0 align-middle">
          <thead class="text-dark fs-4">
            <tr>
              <th>ID</th>
              <th>University</th>
              <th>Product</th>
              <th>Partner</th>
              <th>Commission</th>
              <th>Bonus</th>
              <th>Intensive</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredRates.length === 0">
              <td colspan="8" class="text-center py-4">No commission rates found.</td>
            </tr>
            <tr v-for="rate in filteredRates" :key="rate.id">
              <td>{{ rate.id }}</td>
              <td>{{ rate.university }}</td>
              <td>{{ rate.product }}</td>
              <td>{{ rate.partner || 'N/A' }}</td>
              <td>{{ formatCommissionTypes(rate.commission_types) }}</td>
              <td>{{ rate.bonus_commission ? 'Yes' : 'No' }}</td>
              <td>{{ rate.intensive_commission ? 'Yes' : 'No' }}</td>
              <td>
                <div class="dropdown dropstart">
                  <a href="#" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical fs-6"></i>
                  </a>
                  <ul class="dropdown-menu">
                    <li><router-link :to="`/app/commissions/record/${rate.id}`"
                        class="dropdown-item d-flex align-items-center gap-3"><i
                          class="fs-4 ti ti-file-text"></i>View</router-link></li>
                    <li><router-link :to="`/app/commissions/edit/${rate.id}`"
                        class="dropdown-item d-flex align-items-center gap-3"><i
                          class="fs-4 ti ti-pencil"></i>Edit</router-link></li>
                    <li><a href="#" @click.prevent="duplicateRate(rate)"
                        class="dropdown-item d-flex align-items-center gap-3"><i
                          class="fs-4 ti ti-copy"></i>Duplicate</a></li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li><a href="#" @click.prevent="deleteRate(rate)"
                        class="dropdown-item d-flex align-items-center gap-3 text-danger"><i
                          class="fs-4 ti ti-trash"></i>Delete</a></li>
                  </ul>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Import Modal -->
    <div class="modal" :class="{ 'show d-block': isImportModalVisible }" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center"><i class="ti ti-file-import text-primary me-2"></i> Import
              CSV/XLSX File</h5>
            <button type="button" class="btn-close" @click="closeImportModal"></button>
          </div>
          <div class="modal-body">
            <div v-if="importError" class="alert alert-danger">{{ importError }}</div>
            <form @submit.prevent="submitImport">
              <div class="mb-3">
                <label for="importFile" class="form-label fw-medium">Select File</label>
                <input type="file" class="form-control" id="importFile" @change="handleFileSelect"
                  accept=".csv,.xlsx,.xls" required>
              </div>
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-outline-secondary" @click="closeImportModal">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="isImporting">
                  <span v-if="isImporting" class="spinner-border spinner-border-sm me-1"></span>
                  Import
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isImportModalVisible" class="modal-backdrop fade show"></div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

const API_URL = '/api/commission-rates';

export default {
  name: 'CommissionRateIndex',
  data() {
    return {
      rates: [],
      isLoading: true,
      searchTerm: '',
      isImportModalVisible: false,
      isImporting: false,
      importFile: null,
      importError: null,
    };
  },
  computed: {
    filteredRates() {
      if (!this.searchTerm) {
        return this.rates;
      }
      const lowerSearch = this.searchTerm.toLowerCase();
      return this.rates.filter(rate =>
        (rate.university?.toLowerCase().includes(lowerSearch)) ||
        (rate.product?.toLowerCase().includes(lowerSearch)) ||
        (rate.partner?.toLowerCase().includes(lowerSearch))
      );
    },
  },
  methods: {
    async fetchRates() {
      this.isLoading = true;
      try {
        const response = await axios.get(API_URL);
        if (response.data.success) {
          this.rates = response.data.data;
        }
      } catch (error) {
        Swal.fire('Error', 'Could not fetch commission rates.', 'error');
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    },
    formatCommissionTypes(types) {
      if (!types) return 'N/A';
      if (typeof types === 'string') {
        try {
          types = JSON.parse(types);
        } catch (e) {
          return types; // Return as is if not valid JSON
        }
      }
      if (typeof types !== 'object' || Array.isArray(types)) return 'Invalid Data';

      return Object.entries(types)
        .map(([key, value]) => `${key} (${value})`)
        .join(', ');
    },
    async deleteRate(rate) {
      const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You won't be able to revert deleting "${rate.university}"!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`${API_URL}/${rate.id}`);
          Swal.fire('Deleted!', 'The commission rate has been deleted.', 'success');
          this.fetchRates(); // Refresh list
        } catch (error) {
          Swal.fire('Error', 'Could not delete the rate.', 'error');
        }
      }
    },
    async duplicateRate(rate) {
      try {
        await axios.post(`${API_URL}/${rate.id}/duplicate`);
        Swal.fire('Duplicated!', 'The rate has been duplicated.', 'success');
        this.fetchRates();
      } catch (error) {
        Swal.fire('Error', 'Could not duplicate the rate.', 'error');
      }
    },
    exportData() {
      // The easiest way to trigger a file download from an API endpoint
      window.location.href = `${API_URL}/export`;
    },
    openImportModal() { this.isImportModalVisible = true; },
    closeImportModal() {
      this.isImportModalVisible = false;
      this.importFile = null;
      this.importError = null;
    },
    handleFileSelect(event) {
      this.importFile = event.target.files[0];
    },
    async submitImport() {
      if (!this.importFile) return;
      this.isImporting = true;
      this.importError = null;

      const formData = new FormData();
      formData.append('file', this.importFile);

      try {
        await axios.post(`${API_URL}/import`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        Swal.fire('Success', 'File imported successfully!', 'success');
        this.closeImportModal();
        this.fetchRates();
      } catch (error) {
        this.importError = error.response?.data?.message || 'An unknown error occurred.';
      } finally {
        this.isImporting = false;
      }
    }
  },
  mounted() {
    this.fetchRates();
  },
};
</script>

<style scoped>
/* All the styles from your Blade file are copied here */
.main-container {
  background-color: #f8f9fa;
}

.icon-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  border: 1px solid #dee2e6;
  background: white;
  color: #6c757d;
  text-decoration: none;
  transition: all 0.2s ease;
  cursor: pointer;
}

.icon-btn:hover {
  background: #f8f9fa;
  border-color: #adb5bd;
  color: #495057;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.icon-btn.btn-success {
  background-color: #d4edda;
  color: #155724;
  border-color: #c3e6cb;
}

.icon-btn.btn-success:hover {
  background: #198754;
  border-color: #198754;
  color: white;
}

.icon-btn.btn-outline-primary:hover {
  background: #0d6efd;
  border-color: #0d6efd;
  color: white;
}

.tooltip-text {
  position: absolute;
  bottom: -35px;
  left: 50%;
  transform: translateX(-50%);
  background: #333;
  color: white;
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transition: all 0.2s ease;
  z-index: 1000;
}

.tooltip-text::before {
  content: '';
  position: absolute;
  top: -4px;
  left: 50%;
  transform: translateX(-50%);
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  border-bottom: 4px solid #333;
}

.icon-btn:hover .tooltip-text {
  opacity: 1;
  visibility: visible;
}

.table-hover tbody tr:hover {
  background-color: rgba(46, 125, 50, 0.05);
}

.modal.show {
  background-color: rgba(0, 0, 0, 0.5);
}
</style>