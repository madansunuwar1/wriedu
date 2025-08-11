<template>
  <div class="main-container">
    <div class="widget-content searchable-container list">
      <!-- Header Section with Search and Action Buttons -->
      <div class="card card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <i class="ti ti-bell-ringing text-2xl text-[#2e7d32] me-2"></i>
            <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Notice Board</div>
          </div>
          <div class="d-flex gap-2">
            <div class="position-relative" style="width: 250px;">
              <input 
                type="text" 
                class="form-control product-search ps-5" 
                id="searchInput" 
                placeholder="Search notices..." 
                v-model="searchQuery" 
                @input="filterAndPaginate"
              >
              <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
            </div>
            <a v-if="hasPermission" href="#" class="icon-btn btn-success" @click="createNotice">
              <i class="ti ti-plus fs-5"></i>
              <span class="tooltip-text">Add Notice</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="card border-0 shadow-sm mt-4">
        <div class="card-body text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2 mb-0">Loading notices...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-if="error" class="card border-0 shadow-sm mt-4">
        <div class="card-body text-center py-5">
          <i class="ti ti-alert-circle text-danger fs-1"></i>
          <p class="text-danger mt-2 mb-3">{{ error }}</p>
          <button class="btn btn-primary" @click="fetchNotices">Try Again</button>
        </div>
      </div>

      <!-- Notice Data Table -->
      <div v-if="!loading && !error" class="card border-0 shadow-sm mt-4">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="noticeTable">
            <thead class="table-light">
              <tr>
                <th style="width: 25%;">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-subtask me-1"></i>
                    <span>Title</span>
                  </div>
                </th>
                <th style="width: 45%;">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-file-text me-1"></i>
                    <span>Description</span>
                  </div>
                </th>
                <th style="width: 20%;">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-photo me-1"></i>
                    <span>Image</span>
                  </div>
                </th>
                <th style="width: 10%;">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-settings me-1"></i>
                    <span>Actions</span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <tr v-if="filteredNotices.length === 0">
                <td colspan="4" class="text-center py-4">
                  <i class="ti ti-inbox text-muted fs-1"></i>
                  <p class="mb-0 mt-2">No notices found.</p>
                  <a v-if="hasPermission" href="#" class="btn btn-sm btn-success mt-2" @click="createNotice">
                    Add the First Notice
                  </a>
                </td>
              </tr>
              <tr v-for="notice in paginatedNotices" :key="notice.id">
                <!-- Title -->
                <td class="align-middle">
                  <h6 class="fw-semibold mb-0 fs-4">{{ notice.title }}</h6>
                  <small class="text-muted">{{ formatDate(notice.created_at) }}</small>
                </td>
                <!-- Description -->
                <td class="align-middle">
                  <p class="mb-0">{{ truncateText(notice.description, 100) }}</p>
                </td>
                <!-- Image -->
                <td class="align-middle">
                  <img 
                    v-if="notice.image" 
                    :src="getImageUrl(notice.image)" 
                    alt="Notice Image" 
                    style="width: 120px; height: 60px; object-fit: cover;" 
                    class="rounded"
                    @error="handleImageError"
                  />
                  <span v-else class="text-muted">No Image</span>
                </td>
                <!-- Actions -->
                <td class="align-middle">
                  <div class="dropdown dropstart">
                    <a href="#" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="ti ti-dots-vertical fs-6"></i>
                    </a>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item d-flex align-items-center gap-3" href="#" @click="viewNotice(notice.id)">
                          <i class="fs-4 ti ti-eye"></i>View
                        </a>
                      </li>
                      <li v-if="hasPermission">
                        <a class="dropdown-item d-flex align-items-center gap-3" href="#" @click="editNotice(notice.id)">
                          <i class="fs-4 ti ti-edit"></i>Edit
                        </a>
                      </li>
                      <li v-if="hasPermission">
                        <hr class="dropdown-divider">
                      </li>
                      <li v-if="hasPermission">
                        <a class="dropdown-item d-flex align-items-center gap-3 text-danger" href="#" @click="deleteNotice(notice.id)">
                          <i class="fs-4 ti ti-trash"></i>Delete
                        </a>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Footer -->
        <div class="card-footer bg-transparent">
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted" id="paginationInfo">
              Showing {{ startItem }} to {{ endItem }} of {{ totalItems }} entries
            </div>
            <div>
              <nav aria-label="Page navigation">
                <ul class="pagination mb-0" id="paginationControls">
                  <li class="page-item" :class="{ disabled: currentPage <= 1 }" id="prevPage">
                    <a class="page-link" href="#" aria-label="Previous" @click.prevent="prevPage">
                      <i class="ti ti-chevron-left"></i>
                    </a>
                  </li>
                  <li 
                    v-for="page in visiblePages" 
                    :key="page" 
                    class="page-item" 
                    :class="{ active: page === currentPage }"
                  >
                    <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage >= totalPages || totalPages === 0 }" id="nextPage">
                    <a class="page-link" href="#" aria-label="Next" @click.prevent="nextPage">
                      <i class="ti ti-chevron-right"></i>
                    </a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NoticeBoard',
  data() {
    return {
      notices: [],
      searchQuery: '',
      currentPage: 1,
      itemsPerPage: 8,
      hasPermission: false,
      loading: false,
      error: null
    };
  },
  computed: {
    filteredNotices() {
      if (!this.searchQuery) return this.notices;
      const searchTerm = this.searchQuery.toLowerCase();
      return this.notices.filter(notice => {
        return notice.title.toLowerCase().includes(searchTerm) ||
               notice.description.toLowerCase().includes(searchTerm);
      });
    },
    totalPages() {
      return Math.ceil(this.filteredNotices.length / this.itemsPerPage);
    },
    paginatedNotices() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredNotices.slice(start, end);
    },
    startItem() {
      return this.filteredNotices.length === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
    },
    endItem() {
      return Math.min(this.currentPage * this.itemsPerPage, this.filteredNotices.length);
    },
    totalItems() {
      return this.filteredNotices.length;
    },
    visiblePages() {
      const pages = [];
      const maxVisible = 5;
      let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
      let end = Math.min(this.totalPages, start + maxVisible - 1);
      
      if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
      }
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },
  methods: {
    async fetchNotices() {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('Fetching notices from API...');
        
        // Get CSRF token if it exists
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
          axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        // Simple GET request without auth header initially
        const response = await axios.get('/api/notices');
        
        console.log('API Response:', response.data);
        
        // Ensure we have an array
        if (Array.isArray(response.data)) {
          this.notices = response.data;
        } else if (response.data && Array.isArray(response.data.data)) {
          this.notices = response.data.data;
        } else {
          this.notices = [];
        }
        
        console.log('Notices loaded:', this.notices.length);
        
      } catch (error) {
        console.error('Error fetching notices:', error);
        
        if (error.response) {
          console.error('Response status:', error.response.status);
          console.error('Response data:', error.response.data);
          
          switch (error.response.status) {
            case 401:
              this.error = 'You are not authorized to view notices. Please log in.';
              break;
            case 403:
              this.error = 'You do not have permission to view notices.';
              break;
            case 404:
              this.error = 'Notice API endpoint not found.';
              break;
            case 500:
              this.error = 'Server error. Please try again later.';
              break;
            default:
              this.error = `Error loading notices: ${error.response.status}`;
          }
        } else if (error.request) {
          this.error = 'Unable to connect to the server. Please check your internet connection.';
        } else {
          this.error = 'An unexpected error occurred while loading notices.';
        }
      } finally {
        this.loading = false;
      }
    },
    
    filterAndPaginate() {
      this.currentPage = 1;
    },
    
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
    
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    
    createNotice() {
      window.location.href = '/app/notice/create';
    },
    
    viewNotice(id) {
      window.location.href = `/app/notices/${id}`;
    },
    
    editNotice(id) {
      window.location.href = `/backend/notice/${id}/edit`;
    },
    
    async deleteNotice(id) {
      if (confirm('Are you sure you want to delete this notice?')) {
        try {
          await axios.delete(`/api/notices/${id}`);
          await this.fetchNotices(); // Refresh the list
          this.showAlert('Notice deleted successfully!', 'success');
        } catch (error) {
          console.error('Error deleting notice:', error);
          this.showAlert('Error deleting notice. Please try again.', 'error');
        }
      }
    },
    
    checkPermissions() {
      // You can implement your permission logic here
      // For now, setting to true as placeholder
      this.hasPermission = true;
      
      // Example permission check:
      // const user = this.getAuthenticatedUser();
      // this.hasPermission = user && (user.role === 'admin' || user.role === 'moderator');
    },
    
    truncateText(text, length) {
      if (!text) return '';
      if (text.length <= length) return text;
      return text.substring(0, length) + '...';
    },
    
    formatDate(dateString) {
      if (!dateString) return '';
      try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      } catch (error) {
        return '';
      }
    },
    
    getImageUrl(imagePath) {
      if (!imagePath) return '';
      // Handle both relative and absolute paths
      if (imagePath.startsWith('http')) {
        return imagePath;
      }
      return `/storage/${imagePath}`;
    },
    
    handleImageError(event) {
      event.target.style.display = 'none';
      event.target.nextElementSibling = document.createElement('span');
      event.target.nextElementSibling.textContent = 'Image not found';
      event.target.nextElementSibling.className = 'text-muted';
    },
    
    showAlert(message, type = 'info') {
      // Simple alert implementation
      // You can replace this with a more sophisticated notification system
      const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
      console.log(`${type.toUpperCase()}: ${message}`);
      
      // You could also use a toast notification library here
      alert(message);
    }
  },
  
  async mounted() {
    console.log('NoticeBoard component mounted');
    this.checkPermissions();
    await this.fetchNotices();
  }
};
</script>

