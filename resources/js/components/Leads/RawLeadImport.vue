<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification'; // 1. NEW: Import useToast

const toast = useToast(); // 2. NEW: Instantiate the toast service

// Component State
const selectedFile = ref(null);
const isUploading = ref(false);
const uploadProgress = ref(0);

// Response State
const uploadResult = ref(null);

// Config State
const allowedStatuses = ref([]);
const maxFileSizeMB = ref(10);

// Computed Properties
const hasErrors = computed(() => uploadResult.value?.errors && uploadResult.value.errors.length > 0);

// Methods
const handleFileChange = (event) => {
    // Reset state when a new file is selected
    uploadResult.value = null;
    uploadProgress.value = 0;

    const file = event.target.files[0];
    if (!file) {
        selectedFile.value = null;
        return;
    }
    selectedFile.value = file;
};

const handleUpload = async () => {
    if (!selectedFile.value) {
        toast.error('Please select a file to upload.'); // Using toast
        return;
    }

    isUploading.value = true;
    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        const response = await axios.post('/api/raw-leads/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            onUploadProgress: (progressEvent) => {
                // Ensure total is not zero to avoid division by zero
                if (progressEvent.total > 0) {
                    uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                }
            },
        });
        
        uploadResult.value = response.data; // Keep the result for potential error display
        
        // 3. UPDATED: Use toasts for notifications
        if (response.data.errors && response.data.errors.length > 0) {
            // If there are specific errors, show a warning toast
            toast.warning(response.data.message || "Import completed with some errors.");
        } else {
            // Otherwise, show a success toast
            toast.success(response.data.message || "Import completed successfully!");
        }


    } catch (error) {
        if (error.response && error.response.data) {
             // Server-provided errors
            uploadResult.value = error.response.data;
            toast.error(error.response.data.message || "An error occurred during import.");
        } else {
            // Network or other client-side errors
            toast.error('A network error occurred. Please try again.');
        }
        console.error('Upload failed:', error);
    } finally {
        isUploading.value = false;
        // Optionally reset the file input so the same file can be re-uploaded if fixed
        // document.getElementById('lead_file').value = '';
    }
};

const fetchImportConfig = async () => {
    try {
        const response = await axios.get('/api/raw-leads/import-config');
        allowedStatuses.value = response.data.allowedStatuses;
        // Correct calculation for MB from bytes
        maxFileSizeMB.value = response.data.maxFileSize / 1024 / 1024;
    } catch (error) {
        console.error('Could not fetch import config:', error);
        toast.error('Could not load page configuration from the server.'); // Using toast
    }
}

onMounted(() => {
    fetchImportConfig();
});

</script>

<template>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Import Raw Leads</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="/raw-leads-vue">Raw Leads</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Import</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

        
                     <div v-if="hasErrors" class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Import Errors Encountered:</h4>
                        <ul>
                            <li v-for="(error, index) in uploadResult.errors" :key="index">{{ error }}</li>
                        </ul>
                    </div>
             


                <div class="card card-box mb-30">
                    <div class="card-header">
                        <h5 class="card-title pt-2">Upload File for Import</h5>
                    </div>
                    <div class="card-body">
                        <p>Upload a CSV or Excel file (<code>.csv</code>, <code>.xlsx</code>, <code>.xls</code>) containing raw leads.</p>
                        <p><strong>File Format Instructions:</strong></p>
                        <ul>
                            <li>The first row must be the header row and names are case-insensitive.</li>
                            <li><strong>Required Columns:</strong> <code>Name</code>, <code>Email</code>, <code>Phone</code></li>
                            <li>
                                <strong>Optional Columns:</strong>
                                <ul>
                                    <li><code>Status</code> (Allowed: {{ allowedStatuses.join(', ') }}. Defaults to 'new'.)</li>
                                    <li><code>AD ID</code>, <code>Country Preference</code>, <code>Subject Preference</code>, <code>Applying For</code></li>
                                    <li><code>Assigned User Email</code>, <code>Initial Comment</code></li>
                                </ul>
                            </li>
                            <li>Leads are matched by Email/Phone to prevent duplicates. Existing leads will be updated.</li>
                        </ul>

                        <form @submit.prevent="handleUpload">
                            <div class="mb-3">
                                <label for="lead_file" class="form-label">Select Lead File</label>
                                <input class="form-control" type="file" id="lead_file" name="file" required accept=".csv, .xlsx, .xls" @change="handleFileChange" :disabled="isUploading">
                                <small class="form-text text-muted">Max file size: {{ maxFileSizeMB }}MB.</small>
                            </div>

                            <!-- Progress Bar -->
                            <div v-if="isUploading" class="progress mt-3" style="height: 25px;">
                               <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" :style="{ width: uploadProgress + '%' }" :aria-valuenow="uploadProgress" aria-valuemin="0" aria-valuemax="100">
                                   {{ uploadProgress }}%
                                </div>
                            </div>


                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary" :disabled="isUploading || !selectedFile">
                                    <span v-if="isUploading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    {{ isUploading ? 'Uploading...' : 'Upload and Start Import' }}
                                </button>
                                <a href="/raw-leads-vue" class="btn btn-secondary ms-2">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add any component-specific styles here if needed */
</style>