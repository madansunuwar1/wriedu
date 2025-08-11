<template>
  <div class="main-container">
    <div class="widget-content searchable-container list">
      <div class="card card-body">
        <div class="row">
          <div class="col-md-12 col-xl-12">
            <div class="d-flex align-items-center">
              <i class="ti ti-database text-2xl text-[#2e7d32] me-2"></i>
              <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Commission</div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
          <div class="position-relative" style="width: 300px;">
            <input type="text" class="form-control product-search ps-5" v-model="searchQuery" placeholder="Search Finances...">
            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
          </div>
          <div class="d-flex gap-2">
            <button @click="openImportModal" class="icon-btn btn-outline-primary">
              <i class="ti ti-file-import fs-5"></i>
              <span class="tooltip-text">Import</span>
            </button>
            <button @click="downloadData" class="icon-btn btn-outline-primary">
              <i class="ti ti-file-export fs-5"></i>
              <span class="tooltip-text">Download Data</span>
            </button>
          </div>
        </div>
      </div>

      <div class="table-responsive mb-4 border rounded-1">
        <table class="table text-nowrap mb-0 align-middle" id="dataTable">
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
            <tr v-if="loading">
              <td colspan="8" class="text-center py-5">Loading data...</td>
            </tr>
            <tr v-else-if="filteredCommissions.length === 0">
              <td colspan="8" class="text-center py-4">No matching records found.</td>
            </tr>
            <tr v-for="commission in filteredCommissions" :key="commission.id">
              <td>{{ commission.id }}</td>
              <td>{{ commission.university }}</td>
              <td>{{ commission.product }}</td>
              <td>{{ commission.partner }}</td>
              <td>{{ formatCommissionTypes(commission.commission_types) }}</td>
              <td>{{ commission.bonus_commission == 0 ? 'No' : 'Yes' }}</td>
              <td>{{ commission.intensive_commission == 0 ? 'No' : 'Yes' }}</td>
              <td>
                <div class="dropdown dropstart">
                  <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical fs-6"></i>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li>
                      <a class="dropdown-item d-flex align-items-center gap-3" :href="getViewUrl(commission.id)">
                        <i class="fs-4 ti ti-file-text"></i> Views
                      </a>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div id="importModal" class="modal" :class="{ 'show': isImportModalVisible }">
    <div class="modal-content">
      <span class="close" @click="closeImportModal">×</span>
      <h2 class="modal-title d-flex align-items-center">
        <i class="ti ti-file-import text-primary me-2"></i> Import CSV File
      </h2>
      <form @submit.prevent="handleImportSubmit">
        <div class="mb-3">
          <label class="form-label fw-medium">Select CSV/TXT File</label>
          <input type="file" class="form-control" name="file" accept=".csv, .txt" @change="handleFileSelect" required>
        </div>
        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-outline-secondary" @click="closeImportModal">
            <i class="ti ti-x me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary" :disabled="isImporting">
             <span v-if="isImporting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
             <i v-else class="ti ti-upload me-1"></i>
             {{ isImporting ? 'Importing...' : 'Import' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Swal from 'sweetalert2';
import axios from 'axios';

const API_ROUTES = {
    index: '/api/commissions',
    import: '/api/commissions/import'
};

const BASE_WEB_URL = {
    view: '/app/receivable'
};

const commissions = ref([]);
const searchQuery = ref('');
const isImportModalVisible = ref(false);
const fileToImport = ref(null);
const loading = ref(true);
const isImporting = ref(false);

const filteredCommissions = computed(() => {
  if (!searchQuery.value) {
    return commissions.value;
  }
  const lowerCaseQuery = searchQuery.value.toLowerCase();
  return commissions.value.filter(commission =>
    Object.values(commission).some(val =>
      String(val).toLowerCase().includes(lowerCaseQuery)
    )
  );
});

const fetchCommissions = async () => {
    loading.value = true;
    try {
        const response = await axios.get(API_ROUTES.index);
        commissions.value = response.data;
    } catch (error) {
        console.error("Failed to fetch commissions:", error);
        Swal.fire('Error', 'Could not load commission data from the server.', 'error');
    } finally {
        loading.value = false;
    }
};

onMounted(fetchCommissions);

const formatCommissionTypes = (types) => {
  if (types === null || types === undefined) return 'N/A';
  
  let parsedTypes = types;
  if (typeof types === 'string') {
      try {
          parsedTypes = JSON.parse(types);
      } catch (e) {
          return types; 
      }
  }

  if (typeof parsedTypes === 'object' && !Array.isArray(parsedTypes)) {
    return Object.entries(parsedTypes)
      .map(([key, data]) => {
        const value = (typeof data === 'object' && data !== null && 'value' in data) ? data.value : data;
        return `${key} (${value})`;
      })
      .join(', ');
  }
  
  return 'Invalid Data';
};

const openImportModal = () => isImportModalVisible.value = true;
const closeImportModal = () => {
    isImportModalVisible.value = false;
    fileToImport.value = null;
};
const handleFileSelect = (event) => fileToImport.value = event.target.files[0];

const handleImportSubmit = async () => {
  if (!fileToImport.value) {
    Swal.fire('Error', 'Please select a file to import.', 'error');
    return;
  }
  isImporting.value = true;
  const formData = new FormData();
  formData.append('csv_file', fileToImport.value);

  try {
    const response = await axios.post(API_ROUTES.import, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    
    await fetchCommissions();
    closeImportModal();

    Swal.fire({
      icon: 'success',
      title: 'Import Successful!',
      text: response.data.message || 'The data has been imported.',
    });
  } catch (error) {
    console.error('Import failed:', error);
    const errorMessage = error.response?.data?.message || 'An error occurred during the import process.';
    Swal.fire('Import Failed', errorMessage, 'error');
  } finally {
      isImporting.value = false;
  }
};

const downloadData = () => {
  const dataToExport = filteredCommissions.value;
  if (dataToExport.length === 0) {
      Swal.fire('No Data', 'There is no data to download.', 'info');
      return;
  }
  
  const headers = ['ID', 'University', 'Product', 'Partner', 'Commission', 'Bonus', 'Intensive'];
  const csvRows = [headers.join(',')];

  dataToExport.forEach(item => {
      const values = [
          item.id,
          `"${item.university || ''}"`,
          `"${item.product || ''}"`,
          `"${item.partner || ''}"`,
          `"${formatCommissionTypes(item.commission_types) || ''}"`,
          item.bonus_commission == 0 ? 'No' : 'Yes',
          item.intensive_commission == 0 ? 'No' : 'Yes'
      ];
      csvRows.push(values.join(','));
  });

  const csvContent = csvRows.join('\n');
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);
  link.setAttribute('href', url);
  link.setAttribute('download', 'commissions_data.csv');
  link.style.visibility = 'hidden';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const getViewUrl = (id) => `${BASE_WEB_URL.view}/${id}`;

</script>

<style scoped>
.main-container {
  padding: 20px;
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
.badge {
  font-weight: 500;
  padding: 4px 8px;
  font-size: 12px;
}
.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1050;
  overflow: auto;
}
.modal.show {
  display: flex !important;
  justify-content: center;
  align-items: center;
}
.modal-content {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  position: relative;
  width: 90%;
  max-width: 500px;
  animation: modalFadeIn 0.3s ease-out;
  margin: auto;
}
@keyframes modalFadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
.close {
  position: absolute;
  right: 15px;
  top: 10px;
  font-size: 24px;
  font-weight: bold;
  color: #666;
  cursor: pointer;
  transition: color 0.3s ease;
}
.close:hover {
  color: #333;
}
.modal-title {
  margin-top: 0;
  margin-bottom: 20px;
  color: #333;
  font-size: 1.5rem;
}
.modal-content form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
table {
  width: 100%;
  border-collapse: collapse;
}
thead th {
  background-color: #f4f4f4;
  color: #333;
  padding: 12px;
  text-align: left;
  border-bottom: 2px solid #ddd;
}
tbody tr {
  border-bottom: 1px solid #ddd;
}
tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}
tbody td {
  padding: 12px;
  text-align: left;
}
</style>