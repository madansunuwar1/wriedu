<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h2">Partners</h1>
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-info" @click="downloadTemplate('xlsx')" :disabled="isDownloading">
          <i class="fas fa-download me-2"></i>
          <span v-if="isDownloading">Downloading...</span>
          <span v-else>Download Template</span>
        </button>
        <label for="import_file" class="btn btn-success">
          <i class="fas fa-upload me-2"></i> Import Partners
          <input
            ref="fileInput"
            type="file"
            id="import_file"
            name="file"
            accept=".xlsx,.xls,.csv"
            required
            style="display: none"
            @change="handleImport"
          />
        </label>
        <router-link to="/app/partners/create" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i> Add New Partner
        </router-link>
      </div>
    </div>

    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      <pre style="white-space: pre-wrap; margin: 0;">{{ successMessage }}</pre>
      <button type="button" class="btn-close" @click="successMessage = ''" aria-label="Close"></button>
    </div>

    <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
      <pre style="white-space: pre-wrap; margin: 0;">{{ errorMessage }}</pre>
      <button type="button" class="btn-close" @click="errorMessage = ''" aria-label="Close"></button>
    </div>

    <!-- Loading indicator for import -->
    <div v-if="isImporting" class="alert alert-info">
      <i class="fas fa-spinner fa-spin me-2"></i>
      Processing import... Please wait.
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="partners.data && partners.data.length > 0">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Agency Name</th>
                  <th>Address</th>
                  <th>Email</th>
                  <th>Contact No.</th>
                  <th>Counselor Email</th>
                  <th width="200">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="partner in partners.data" :key="partner.id">
                  <td><strong>{{ partner.agency_name }}</strong></td>
                  <td>
                    <i class="fas fa-map-marker-alt text-muted me-1"></i> {{ partner.address || partner.Address }}
                  </td>
                  <td><i class="fas fa-envelope text-muted me-1"></i> {{ partner.email }}</td>
                  <td><i class="fas fa-phone text-muted me-1"></i> {{ partner.contact_no }}</td>
                  <td><i class="fas fa-user text-muted me-1"></i> {{ partner.agency_counselor_email }}</td>
                  <td>
                    <div class="btn-group" role="group">
                      <router-link :to="`/app/partners/${partner.id}/view`" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-eye"></i> View
                      </router-link>
                      <router-link :to="`/app/partners/${partner.id}`" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-edit"></i> Edit
                      </router-link>
                      <button class="btn btn-sm btn-outline-danger" @click="deletePartner(partner.id)">
                        <i class="fas fa-trash"></i> Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
              <ul class="pagination">
                <li
                  class="page-item"
                  v-for="link in partners.links"
                  :key="link.label"
                  :class="{ active: link.active, disabled: !link.url }"
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="fetchPartners(link.url)"
                    v-html="link.label"
                    :class="{ disabled: !link.url }"
                  ></a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <div v-else class="text-center py-5">
          <i class="fas fa-users fa-3x text-muted mb-3"></i>
          <h4 class="text-muted">No partners found</h4>
          <p class="text-muted">Get started by creating your first partner or importing from a file.</p>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-info" @click="downloadTemplate('xlsx')" :disabled="isDownloading">
              <i class="fas fa-download me-2"></i>
              <span v-if="isDownloading">Downloading...</span>
              <span v-else>Download Template (XLSX)</span>
            </button>
             <label for="import_file_empty" class="btn btn-success">
              <i class="fas fa-upload me-2"></i> Import Partners
              <input
                type="file"
                id="import_file_empty"
                name="file"
                accept=".xlsx,.xls,.csv"
                required
                style="display: none"
                @change="handleImport"
              />
            </label>
          <router-link to="/app/partners/create" class="btn btn-primary">
              <i class="fas fa-plus me-2"></i> Add New Partner
            </router-link>
          </div>
        </div>

          <div class="alert alert-info">
            <h6><i class="fas fa-info-circle me-2"></i> Import Rules:</h6>
            <ul class="mb-0 small">
              <li>If a partner with the same <strong>Agency Name</strong> and <strong>Address</strong> exists, the record will be updated</li>
              <li>New partners will be created if no matching combination is found</li>
              <li>All fields are required: Agency Name, Email, Contact No, Counselor Email, Address</li>
              <li>Supported formats: CSV, XLS, XLSX</li>
            </ul>
          </div>
          <div class="card bg-light">
            <div class="card-body">
              <h6 class="card-title">Required Columns (case-insensitive):</h6>
              <div class="row">
                <div class="col-md-6">
                  <ul class="list-unstyled small">
                    <li><code>agency_name</code></li>
                    <li><code>email</code></li>
                    <li><code>contact_no</code></li>
                  </ul>
                </div>
                <div class="col-md-6">
                  <ul class="list-unstyled small">
                    <li><code>agency_counselor_email</code></li>
                    <li><code>address</code></li>
                  </ul>
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
import { ref, onMounted } from 'vue';

export default {
  setup() {
    const partners = ref({ data: [], links: [] });
    const successMessage = ref('');
    const errorMessage = ref('');
    const isDownloading = ref(false);
    const isImporting = ref(false);
    const fileInput = ref(null);

    const fetchPartners = async (url = '/api/partners') => {
      try {
        const response = await axios.get(url);
        partners.value = response.data;
      } catch (error) {
        console.error(error);
        errorMessage.value = 'Failed to fetch partners';
      }
    };

    const deletePartner = async (id) => {
      if (confirm('Are you sure you want to delete this partner?')) {
        try {
          const response = await axios.delete(`/api/partners/${id}`);
          successMessage.value = response.data.message;
          await fetchPartners();
          errorMessage.value = '';
        } catch (error) {
          errorMessage.value = error.response?.data?.message || 'Failed to delete partner';
          successMessage.value = '';
        }
      }
    };

const handleImport = async (event) => {
  try {
    const file = event.target.files[0];

    if (!file) {
      errorMessage.value = 'Please select a file to import';
      return;
    }

    // Reset messages
    successMessage.value = '';
    errorMessage.value = '';
    isImporting.value = true;

    // ENHANCED DEBUGGING - Log all file properties
    console.log('=== FILE DEBUG INFO ===');
    console.log('File name:', file.name);
    console.log('File type (MIME):', file.type);
    console.log('File size:', file.size);
    console.log('Last modified:', new Date(file.lastModified));
    
    // Check file extension
    const fileExtension = file.name.split('.').pop().toLowerCase();
    console.log('File extension:', fileExtension);
    
    // Log what the browser thinks the MIME type is
    console.log('Browser detected MIME type:', file.type);

    // Enhanced client-side validation
    const allowedExtensions = ['xlsx', 'xls', 'csv'];
    if (!allowedExtensions.includes(fileExtension)) {
      errorMessage.value = `Invalid file extension: .${fileExtension}. Please upload CSV, XLS, or XLSX files only.`;
      isImporting.value = false;
      return;
    }

    // Check MIME type more thoroughly
    const allowedMimeTypes = [
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
      'application/vnd.ms-excel', // .xls
      'application/msexcel', // Alternative .xls
      'application/x-msexcel', // Alternative .xls
      'application/x-ms-excel', // Alternative .xls
      'application/x-excel', // Alternative .xls
      'application/x-dos_ms_excel', // Alternative .xls
      'application/xls', // Alternative .xls
      'application/xlsx', // Alternative .xlsx
      'text/csv', // .csv
      'application/csv', // Alternative .csv
      'text/comma-separated-values', // Alternative .csv
      'text/x-comma-separated-values', // Alternative .csv
      'text/plain', // Sometimes CSV files are detected as plain text
      'application/octet-stream' // Generic binary, sometimes used for Excel files
    ];

    console.log('Is MIME type allowed?', allowedMimeTypes.includes(file.type));
    console.log('Empty MIME type?', file.type === '');

    // Create FormData and append file
    const formData = new FormData();
    formData.append('file', file);

    // Log FormData contents (for debugging)
    console.log('FormData created');
    for (let [key, value] of formData.entries()) {
      console.log(`FormData ${key}:`, value);
    }

    console.log('Sending request to: /api/partners/bulk-upload');

    // Send to backend with enhanced error handling
    const response = await axios.post('/api/partners/bulk-upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      timeout: 60000, // 60 second timeout
      onUploadProgress: (progressEvent) => {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
        console.log(`Upload progress: ${percentCompleted}%`);
      }
    });

    console.log('Server response:', response.data);

    successMessage.value = response.data.message;
    errorMessage.value = '';
    
    // Refresh the partners list
    await fetchPartners();

    // Reset the file input
    event.target.value = '';

  } catch (error) {
    console.error('=== UPLOAD ERROR DEBUG ===');
    console.error('Full error object:', error);
    
    if (error.response) {
      // Server responded with error status
      console.error('Response status:', error.response.status);
      console.error('Response headers:', error.response.headers);
      console.error('Response data:', error.response.data);
      
      // Handle validation errors specifically
      if (error.response.status === 422 && error.response.data.errors) {
        const validationErrors = error.response.data.errors;
        console.error('Validation errors:', validationErrors);
        
        if (validationErrors.file) {
          errorMessage.value = `File validation failed: ${validationErrors.file.join(', ')}`;
        } else {
          errorMessage.value = error.response.data.message || 'Validation failed';
        }
      } else {
        errorMessage.value = error.response.data?.message || `Server error (${error.response.status})`;
      }
    } else if (error.request) {
      // Request was made but no response received
      console.error('No response received:', error.request);
      errorMessage.value = 'No response from server. Please check your connection.';
    } else {
      // Something else happened
      console.error('Request setup error:', error.message);
      errorMessage.value = 'Failed to import partners: ' + error.message;
    }
    
    successMessage.value = '';
  } finally {
    isImporting.value = false;
  }
};

    const downloadTemplate = async (format = 'xlsx') => {
      try {
        isDownloading.value = true;
        
        try {
          // Try to call the backend endpoint first
          const response = await axios.get(`/api/partners/download-template?format=${format}`, {
            responseType: 'blob',
            timeout: 10000 // 10 second timeout
          });
          
          const blob = new Blob([response.data]);
          const link = document.createElement('a');
          link.href = URL.createObjectURL(blob);
          link.download = `partners_template.${format}`;
          link.click();
          URL.revokeObjectURL(link.href);
          
          successMessage.value = `Template downloaded successfully (${format.toUpperCase()})`;
          
        } catch (backendError) {
          console.warn('Backend download failed, using client-side generation:', backendError);
          
          // Fallback to client-side CSV generation
          const headers = ['agency_name', 'email', 'contact_no', 'agency_counselor_email', 'Address'];
          const sampleData = [
            ['Sample Agency Ltd', 'contact@sampleagency.com', '1234567890', 'counselor@sampleagency.com', '123 Main Street, City, State']
          ];

          const csvContent = [
            headers.join(','),
            sampleData[0].map(field => `"${field}"`).join(',')
          ].join('\n');
          
          const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
          const link = document.createElement('a');
          link.href = URL.createObjectURL(blob);
          link.download = 'partners_template.csv';
          link.click();
          URL.revokeObjectURL(link.href);
          
          successMessage.value = 'Template downloaded successfully (CSV - fallback)';
        }
        
      } catch (error) {
        console.error('Download error:', error);
        errorMessage.value = 'Failed to download template';
      } finally {
        isDownloading.value = false;
      }
    };

    onMounted(() => {
      fetchPartners();
    });

    return {
      partners,
      successMessage,
      errorMessage,
      isDownloading,
      isImporting,
      fileInput,
      fetchPartners,
      deletePartner,
      handleImport,
      downloadTemplate,
    };
  },
};
</script>

<style scoped>
pre {
  font-family: inherit;
}
</style>