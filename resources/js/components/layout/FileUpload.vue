<!-- resources/js/components/FileUpload.vue -->
<template>
  <div class="page-container card">
    <div class="create-section">
      <h2 style="color:green; font-size:20px; margin-bottom:40px; font-family:poppins;">Upload Documents</h2>
      <div 
        class="upload-container" 
        :class="{ dragover: isDragging }"
        @dragover.prevent="isDragging = true"
        @dragenter.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
        @click="triggerFileInput"
      >
        <div v-if="filesToUpload.length === 0">
          <div class="upload-icon">
            <img src="/img/wri.png" alt="Upload icon">
          </div>
          <p class="upload-text">Drag & drop files here or click to select</p>
        </div>
        <div v-else class="progress-section w-100 p-3">
          <div v-for="file in filesToUpload" :key="file.id" class="file-item">
            <div class="file-details">
              <div class="file-name">{{ file.file.name }}</div>
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: file.progress + '%' }"></div>
              </div>
              <div class="d-flex justify-content-between">
                <div class="file-size">{{ (file.file.size / 1024).toFixed(1) }} KB</div>
                <div class="progress-percentage">{{ Math.round(file.progress) }}%</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <input type="file" ref="fileInput" @change="handleFileSelect" multiple class="d-none">
    </div>

    <div class="index-section">
      <h2 style="color:green; font-size:20px; margin-top:30px; font-family:poppins;">Uploaded Files</h2>
      <div class="index-section-content">
        <table class="documents-table" v-if="uploadedFiles.length > 0">
          <tbody>
            <tr v-for="upload in uploadedFiles" :key="upload.id">
              <td>
                <i class="fa-solid fa-circle-check" style="color: green;"></i>
                <div class="file-wrapper">
                  <i class="fa-regular fa-file"></i>
                  <a :href="`/storage/uploads/${upload.fileInput}`" class="file-link" target="_blank">
                    <span class="file-name">{{ upload.fileInput }}</span>
                  </a>
                </div>
              </td>
              <td class="upload-date">{{ new Date(upload.created_at).toLocaleDateString() }}</td>
              <td>
                <div class="action-buttons">
                  <a :href="`/storage/uploads/${upload.fileInput}`" class="view-link" target="_blank"><i class="fas fa-eye"></i></a>
                  <button @click="deleteFile(upload.id)" class="btn btn-link text-danger p-0"><i class="fa-solid fa-xmark"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="text-center p-5 text-muted">No documents uploaded yet.</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
  leadId: [Number, String],
  initialFiles: Array,
});
const emit = defineEmits(['files-updated']);

const fileInput = ref(null);
const isDragging = ref(false);
const filesToUpload = ref([]);
const uploadedFiles = ref([...props.initialFiles]);

watch(() => props.initialFiles, (newVal) => {
    uploadedFiles.value = [...newVal];
});

const triggerFileInput = () => {
  fileInput.value.click();
};

const handleFileSelect = (event) => {
  processFiles(event.target.files);
};

const handleDrop = (event) => {
  isDragging.value = false;
  processFiles(event.dataTransfer.files);
};

const processFiles = (fileList) => {
  for (const file of fileList) {
    const fileWrapper = {
      id: Math.random().toString(36).substr(2, 9), // unique id for v-for key
      file: file,
      progress: 0,
      error: null,
    };
    filesToUpload.value.push(fileWrapper);
    uploadFile(fileWrapper);
  }
};

const uploadFile = async (fileWrapper) => {
  const formData = new FormData();
  formData.append('fileInput', fileWrapper.file);

  try {
    const response = await axios.post(`/api/leads/${props.leadId}/uploads`, formData, {
      onUploadProgress: (progressEvent) => {
        fileWrapper.progress = (progressEvent.loaded / progressEvent.total) * 100;
      },
    });
    // Add to list and remove from upload queue
    uploadedFiles.value.unshift(response.data.upload);
    filesToUpload.value = filesToUpload.value.filter(f => f.id !== fileWrapper.id);
  } catch (error) {
    console.error('Upload failed:', error);
    fileWrapper.error = "Upload Failed";
    Swal.fire('Error', 'File upload failed.', 'error');
  }
};

const deleteFile = async (uploadId) => {
    const { isConfirmed } = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    });
    
    if(isConfirmed) {
        try {
            await axios.delete(`/api/uploads/${uploadId}`);
            uploadedFiles.value = uploadedFiles.value.filter(f => f.id !== uploadId);
            Swal.fire('Deleted!', 'The file has been deleted.', 'success');
        } catch(error) {
            Swal.fire('Error', 'Could not delete file.', 'error');
        }
    }
}
</script>

<style scoped>
/* Copy relevant styles for page-container, create-section, upload-container, etc. here */
.page-container { background-color: white; flex-direction: row; display: flex; gap: 1px; }
.create-section { flex: 0 0 400px; padding: 1rem; }
.upload-container { border: 2px dashed #24a52f; border-radius: 8px; min-height: 300px; text-align: center; cursor: pointer; display: flex; flex-direction: column; justify-content: center; align-items: center; }
.upload-container.dragover { border-color: #4CAF50; background-color: #f0f8f0; }
.upload-icon img { width: 80px; height: 80px; opacity: 0.5; }
.index-section { flex: 1; padding: 1rem; }
.index-section-content { height: 300px; overflow-y: auto; border: 2px dashed #ccc; border-radius: 8px; padding: 1rem; }
.documents-table { width: 100%; }
.documents-table td { padding: 8px; vertical-align: middle; }
.file-name { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.progress-bar { width: 100%; background-color: #e0e0e0; border-radius: 5px; height: 10px; }
.progress-fill { height: 100%; background-color: #4CAF50; border-radius: 5px; }
/* ...and other necessary styles */
</style>