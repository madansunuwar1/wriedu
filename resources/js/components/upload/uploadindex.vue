<template>
  <div class="page-container">
    <!-- Success Alert -->
    <div v-if="successMessage" class="alert alert-success">
      {{ successMessage }}
    </div>

    <!-- Create Section (Left Side) -->
    <div class="create-section">
      <h2 style="color:green; font-size:20px; margin-bottom:40px; font-family:poppins;">
        Upload Documents
      </h2>
      <div 
        id="uploadContainer" 
        class="upload-container"
        :class="{ 'dragover': isDragOver }"
        @click="triggerFileInput"
        @dragenter.prevent="handleDragEnter"
        @dragover.prevent="handleDragOver"
        @dragleave.prevent="handleDragLeave"
        @drop.prevent="handleDrop"
      >
        <div v-if="!isUploading && uploadingFiles.length === 0" class="upload-icon">
          <img :src="uploadIconSrc" alt="Upload icon">
        </div>
        <p v-if="!isUploading && uploadingFiles.length === 0" class="upload-text">
          Drag & drop files here
        </p>
        <input 
          ref="fileInput"
          type="file" 
          multiple 
          accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif"
          @change="handleFileSelect"
          style="display: none;"
        >
        
        <!-- Progress Section -->
        <div v-if="uploadingFiles.length > 0" class="progress-section">
          <div 
            v-for="file in uploadingFiles" 
            :key="file.name" 
            class="file-item"
          >
            <div class="file-details" style="padding: 10px;">
              <div style="display: flex; align-items: flex-start; margin-bottom: 10px;">
                <div class="file-name" style="color: green; text-align: left; margin-left: 0; padding-left: 0;">
                  {{ file.name }}
                </div>
              </div>
              
              <div style="margin-bottom: 5px;">
                <div class="progress-bar">
                  <div class="progress-fill" :style="{ width: file.progress + '%' }"></div>
                </div>
              </div>
              
              <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                <div class="file-size" style="font-size: 12px; color: #666;">
                  {{ (file.size / 1024).toFixed(1) }} KB
                </div>
                <div class="progress-percentage" style="font-size: 12px; color: #666;">
                  {{ Math.round(file.progress) }}%
                </div>
              </div>

              <div class="upload-status">
                <span v-if="file.status === 'uploading'" class="status-uploading"></span>
                <span v-else-if="file.status === 'success'" class="status-success"></span>
                <span v-else-if="file.status === 'error'" class="status-error">Upload Failed</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Index Section (Right Side) -->
    <div class="index-section">
      <h2 style="color:green; font-size:20px; margin-top:30px; font-family:poppins;">
        Uploaded Files
      </h2>
      <div class="index-section-content">
        <table class="documents-table">
          <tbody>
            <tr v-for="upload in uploads" :key="upload.id">
              <td>
                <i class="fa-solid fa-circle-check" style="color: green;"></i>&nbsp;&nbsp;
                <div class="file-wrapper">
                  <div class="file-icon">
                    <i class="fa-regular fa-file"></i>
                  </div>
                  <a 
                    :href="getFileUrl(upload.fileInput)"
                    class="file-link"
                    :download="upload.fileInput"
                  >
                    <span class="file-name">{{ upload.fileInput }}</span>
                  </a>
                </div>
              </td>
              <td class="upload-date">
                {{ formatDate(upload.created_at) }}
              </td>
              <td>
                <div class="action-buttons">
                  <a 
                    :href="getFileUrl(upload.fileInput)"
                    class="view-link"
                    target="_blank"
                  >
                    <i class="fas fa-eye"></i>
                  </a>
                  <button 
                    @click="confirmDelete(upload.id)"
                    class="btn-danger" 
                    style="border: none; background: transparent; padding: 0;"
                  >
                    <i class="fa-solid fa-xmark" style="color:red;"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'FileUploadComponent',
  props: {
    initialUploads: {
      type: Array,
      default: () => []
    },
    uploadRoute: {
      type: String,
      required: true
    },
    deleteRoute: {
      type: String,
      required: true
    },
    uploadIconSrc: {
      type: String,
      default: '/img/wri.png'
    },
    csrfToken: {
      type: String,
      required: true
    },
    applicationId: {
      type: [String, Number],
      default: null
    },
    leadId: {
      type: [String, Number],
      default: null
    }
  },
  data() {
    return {
      uploads: [...this.initialUploads],
      uploadingFiles: [],
      isDragOver: false,
      isUploading: false,
      successMessage: ''
    }
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click()
    },

    handleFileSelect(event) {
      const files = Array.from(event.target.files)
      this.handleFiles(files)
    },

    handleDragEnter() {
      this.isDragOver = true
    },

    handleDragOver() {
      this.isDragOver = true
    },

    handleDragLeave() {
      this.isDragOver = false
    },

    handleDrop(event) {
      this.isDragOver = false
      const files = Array.from(event.dataTransfer.files)
      this.handleFiles(files)
    },

    handleFiles(files) {
      if (files.length === 0) return

      // Filter valid files (max 5MB)
      const validFiles = files.filter(file => {
        if (file.size > 5 * 1024 * 1024) {
          alert(`File ${file.name} is too large. Maximum size is 5MB.`)
          return false
        }
        return true
      })

      if (validFiles.length === 0) return

      // Add files to uploading list
      validFiles.forEach(file => {
        const fileObj = {
          file,
          name: file.name,
          size: file.size,
          progress: 0,
          status: 'uploading'
        }
        this.uploadingFiles.push(fileObj)
        this.uploadFile(fileObj)
      })

      this.isUploading = true
    },

    async uploadFile(fileObj) {
      const formData = new FormData()
      formData.append('fileInput[]', fileObj.file)
      formData.append('_token', this.csrfToken)
      
      if (this.applicationId) {
        formData.append('application_id', this.applicationId)
      }
      
      if (this.leadId) {
        formData.append('lead_id', this.leadId)
      }

      try {
        const response = await axios.post(this.uploadRoute, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
          onUploadProgress: (progressEvent) => {
            if (progressEvent.lengthComputable) {
              const percentComplete = (progressEvent.loaded / progressEvent.total) * 100
              fileObj.progress = percentComplete
            }
          }
        })

        fileObj.status = 'success'
        fileObj.progress = 100
        
        this.successMessage = 'File uploaded successfully!'
        
        // Reload the uploads list after successful upload
        setTimeout(() => {
          this.refreshUploads()
          this.resetUploadState()
        }, 1000)

      } catch (error) {
        console.error('Upload error:', error)
        fileObj.status = 'error'
        
        if (error.response && error.response.data && error.response.data.errors) {
          const errors = Object.values(error.response.data.errors).flat()
          alert('Upload failed: ' + errors.join(', '))
        } else {
          alert('Upload failed: ' + (error.response?.data?.message || 'Unknown error'))
        }
        
        this.resetUploadState()
      }
    },

    resetUploadState() {
      setTimeout(() => {
        this.uploadingFiles = []
        this.isUploading = false
        this.successMessage = ''
      }, 2000)
    },

    async refreshUploads() {
      try {
        // You might need to implement an API endpoint to fetch uploads
        // For now, we'll reload the page or you can implement a proper API call
        window.location.reload()
      } catch (error) {
        console.error('Error refreshing uploads:', error)
      }
    },

    confirmDelete(uploadId) {
      if (confirm('Are you sure you want to delete this document?')) {
        this.deleteFile(uploadId)
      }
    },

    async deleteFile(uploadId) {
      try {
        const response = await axios.delete(`${this.deleteRoute}/${uploadId}`, {
          data: {
            _token: this.csrfToken
          }
        })
        
        // Remove from local array
        this.uploads = this.uploads.filter(upload => upload.id !== uploadId)
        this.successMessage = 'File deleted successfully!'
        
        setTimeout(() => {
          this.successMessage = ''
        }, 3000)
        
      } catch (error) {
        console.error('Delete error:', error)
        alert('Delete failed: ' + (error.response?.data?.message || 'Unknown error'))
      }
    },

    getFileUrl(filename) {
      return `/storage/uploads/${filename}`
    },

    formatDate(dateString) {
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.page-container {
  max-width: 1200px;
  background-color: white;
  display: flex;
  gap: 1px;
}

/* Create Section Styles */
.create-section {
  flex: 0 0 500px;
  border-radius: 8px;
  padding: 1rem;
}

.upload-container {
  border: 2px dashed #24a52f;
  border-radius: 8px;
  height: 500px;
  width: 400px;
  margin-top: 50px;
  text-align: center;
  cursor: pointer;
  position: relative;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: rgba(252, 247, 247, 0.5);
  overflow: hidden;
}

.upload-container.dragover {
  border-color: #4CAF50;
  background-color: #f0f8f0;
}

.upload-icon img {
  width: 80px;
  height: 80px;
  margin-bottom: 1rem;
  opacity: 0.5;
}

/* Index Section Styles */
.index-section {
  flex: 1;
  margin-right: 30px;
  background-color: white;
  height: 600px;
  border-radius: 8px;
}

.index-section-content {
  height: calc(100% - 95px);
  padding-right: 10px;
  margin-top: 40px;
  border: 2px dashed #24a52f;
  border-radius: 8px;
  overflow-y: scroll;
}

/* Hide scrollbar for WebKit browsers */
.index-section-content::-webkit-scrollbar {
  width: 0;
  background: transparent;
}

/* Hide scrollbar for Firefox */
.index-section-content {
  scrollbar-width: none;
}

/* Table Styles */
.documents-table {
  width: 100%;
  border-collapse: separate;
  margin-top: 20px;
  margin-right: 10px;
}

.documents-table tr {
  background: white;
  gap: 5px;
}

.documents-table td {
  border: none;
  gap: 5px;
  vertical-align: middle;
  margin-left: 10px;
}

.documents-table td:first-child {
  display: flex;
  align-items: center;
  gap: 5px;
  margin-left: 30px;
}

.file-item {
  width: 100%;
  margin-bottom: 20px;
  border-radius: 8px;
}

.file-details {
  position: relative;
  padding-bottom: 25px;
}

.file-bottom-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 5px;
}

.file-size {
  font-size: 12px;
}

.progress-percentage {
  font-size: 12px;
  color: #666;
}

.file-icon {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.file-name {
  font-size: 17px;
  font-family: poppins;
  color: #333;
  flex-grow: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 200px;
  cursor: pointer;
  text-decoration: none;
  transition: color 0.2s ease;
}

.file-name:hover {
  color: #4CAF50;
}

.file-link {
  text-decoration: none;
  color: inherit;
  display: flex;
  align-items: center;
  gap: 10px;
  flex-grow: 1;
}

.file-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-grow: 1;
}

.upload-date {
  color: #666;
  font-size: 14px;
}

.action-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

.view-link {
  color: #4CAF50;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 4px;
}

.view-icon {
  width: 20px;
  height: 20px;
  color: #4CAF50;
}

.success-icon {
  color: #4CAF50;
  margin-right: 8px;
}

.progress-section {
  margin-top: 30px;
  width: 300px;
}

.progress-bar {
  width: 100%;
  border-radius: 5px;
  background-color: #f0f0f0;
  height: 10px;
}

.progress-fill {
  height: 10px;
  background-color: #4CAF50;
  width: 0%;
  border-radius: 5px;
  transition: width 0.3s ease;
}

/* Alert Styles */
.alert {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 12px 24px;
  border-radius: 4px;
  z-index: 1000;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .page-container {
    flex-direction: column;
  }

  .create-section,
  .index-section {
    width: 100%;
    flex: none;
  }
}
</style>