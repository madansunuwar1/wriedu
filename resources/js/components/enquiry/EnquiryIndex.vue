<template>
  <div class="main-container">
    <div class="widget-content searchable-container list">
      <div class="card card-body">
        <div class="row">
          <div class="col-md-12 col-xl-12">
            <div class="d-flex align-items-center">
              <i class="ti ti-file-text text-2xl text-[#2e7d32] me-2"></i>
              <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Enquiry Data</div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
          <div class="position-relative" style="width: 300px;">
            <input type="text" class="form-control product-search ps-5" id="searchInput" placeholder="Search enquiries...">
            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
          </div>
          <div class="d-flex gap-2">
            <button id="toggleFilters" class="icon-btn btn-outline-secondary" @click="toggleFilterPanel">
              <i class="ti ti-filter fs-5"></i><span class="tooltip-text">Filters</span>
            </button>
            <router-link to="/app/enquiries/create" class="icon-btn btn-success">
              <i class="ti ti-plus fs-5"></i><span class="tooltip-text">Add Enquiry</span>
            </router-link>
            <button class="icon-btn btn-outline-primary" @click="openModal">
              <i class="ti ti-upload fs-5"></i><span class="tooltip-text">Import</span>
            </button>
            <button class="icon-btn btn-outline-primary" @click="downloadData">
              <i class="ti ti-file-export fs-5"></i><span class="tooltip-text">Export</span>
            </button>
          </div>
        </div>

        <div id="filterPanel" class="mt-3 p-3 border rounded bg-light filter-panel-transition" :style="{ display: filterPanelOpen ? 'block' : 'none' }">
          <div class="row">
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Country</label>
              <select class="form-select" id="filterCountry" v-model="filterOptions.country" @change="filterAndPaginate">
                <option value="">All Countries</option>
                <option v-for="country in uniqueCountries" :value="country">{{ country }}</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">English Test</label>
              <select class="form-select" id="filterEnglishTest" v-model="filterOptions.englishTest" @change="filterAndPaginate">
                <option value="">All Tests</option>
                <option value="IELTS">IELTS</option>
                <option value="PTE">PTE</option>
                <option value="TOEFL">TOEFL</option>
                <option value="ELLT">ELLT</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label fw-medium">Other Exam</label>
              <select class="form-select" id="filterOtherExam" v-model="filterOptions.otherExam" @change="filterAndPaginate">
                <option value="">All Exams</option>
                <option value="SAT">SAT</option>
                <option value="GRE">GRE</option>
                <option value="GMAT">GMAT</option>
              </select>
            </div>
            <div class="col-md-3 mb-2 d-flex align-items-end gap-2">
              <button id="resetFilters" class="icon-btn btn-outline-secondary" @click="resetFilters">
                <i class="ti ti-rotate-2 fs-5"></i><span class="tooltip-text">Reset</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="dataTable">
            <thead class="table-light">
              <tr>
                <th><div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Name</span></div></th>
                <th><div class="d-flex align-items-center"><i class="ti ti-phone me-1"></i><span>Contact</span></div></th>
                <th><div class="d-flex align-items-center"><i class="ti ti-flag me-1"></i><span>Country</span></div></th>
                <th><div class="d-flex align-items-center"><i class="ti ti-file-text me-1"></i><span>English Tests</span></div></th>
                <th><div class="d-flex align-items-center"><i class="ti ti-certificate me-1"></i><span>Other Exams</span></div></th>
                <th><div class="d-flex align-items-center"><i class="ti ti-calendar me-1"></i><span>Date Added</span></div></th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <tr v-for="enquiry in paginatedRows" :key="enquiry.id" style="cursor: pointer;" @click="navigateToEnquiry(enquiry.id)">
                <td>
                  <div class="user-info d-flex align-items-center">
                    <div class="ms-3 flex-grow-1">
                      <router-link :to="'/app/enquiries/record' + enquiry.id" class="text-dark text-decoration-none user-name-link">
                        <h6 class="user-name mb-0 fw-medium">{{ enquiry.uname || 'N/A' }}</h6>
                        <small class="text-muted">{{ enquiry.email || '' }}</small>
                      </router-link>
                    </div>
                    <div class="user-actions">
                      <div class="dropdown">
                        <button class="action-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <!-- Dropdown menu items -->
                        </ul>
                      </div>
                    </div>
                  </div>
                </td>
                <td>{{ enquiry.contact || '-' }}</td>
                <td>{{ enquiry.country || '-' }}</td>
                <td>{{ getEnglishTests(enquiry) }}</td>
                <td>{{ getOtherExams(enquiry) }}</td>
                <td>{{ formatDate(enquiry.created_at) }}</td>
                <td class="d-none">{{ enquiry.email || '-' }}</td>
              </tr>
              <tr v-if="filteredRows.length === 0">
                <td colspan="6" class="text-center py-4">No enquiries found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="card-footer bg-transparent">
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted" id="paginationInfo">
              Showing <span id="startItem">{{ startItem }}</span> to <span id="endItem">{{ endItem }}</span> of <span id="totalItems">{{ totalItems }}</span> entries
            </div>
            <div>
              <nav aria-label="Page navigation">
                <ul class="pagination mb-0" id="paginationControls">
                  <li class="page-item" :class="{ disabled: currentPage <= 1 }" id="prevPage">
                    <a class="page-link" href="#" aria-label="Previous" @click.prevent="prevPage"><i class="ti ti-chevron-left"></i></a>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage >= totalPages || totalPages === 0 }" id="nextPage">
                    <a class="page-link" href="#" aria-label="Next" @click.prevent="nextPage"><i class="ti ti-chevron-right"></i></a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="modal" v-if="isModalOpen">
      <div class="modal-content">
        <span class="close" @click="closeModal">&times;</span>
        <h2 class="modal-title">Import Excel File</h2>
        <form id="importForm" @submit.prevent="importFile">
          <div class="mb-3">
            <input type="file" class="form-control" name="file" accept=".csv,.xlsx,.xls" required>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" @click="closeModal">Cancel</button>
            <button type="submit" class="btn btn-primary">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';
import * as XLSX from 'xlsx';

export default {
  name: 'EnquiryData',
  data() {
    return {
      isModalOpen: false,
      filterPanelOpen: false,
      filterOptions: {
        search: '',
        country: '',
        englishTest: '',
        otherExam: ''
      },
      enquiries: [],
      currentPage: 1,
      itemsPerPage: 10,
      filteredRows: [],
      uniqueCountries: []
    };
  },
  computed: {
    paginatedRows() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredRows.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredRows.length / this.itemsPerPage);
    },
    startItem() {
      return this.filteredRows.length === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
    },
    endItem() {
      const end = this.currentPage * this.itemsPerPage;
      return end > this.filteredRows.length ? this.filteredRows.length : end;
    },
    totalItems() {
      return this.filteredRows.length;
    }
  },
  mounted() {
    this.fetchEnquiries();
    this.loadState();
  },
  methods: {
    openModal() {
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
    },
    toggleFilterPanel() {
      this.filterPanelOpen = !this.filterPanelOpen;
      this.saveState();
    },
    async fetchEnquiries() {
      try {
        const response = await fetch('/api/enquiries');
        const data = await response.json();
        this.enquiries = data;
        this.uniqueCountries = [...new Set(data.map(enquiry => enquiry.country))];
        this.filterAndPaginate();
      } catch (error) {
        console.error('Error fetching enquiries:', error);
      }
    },
    filterAndPaginate() {
      this.filteredRows = this.enquiries.filter(enquiry => {
        const searchTerm = this.filterOptions.search.toLowerCase();
        const nameMatch = enquiry.uname && enquiry.uname.toLowerCase().includes(searchTerm);
        const emailMatch = enquiry.email && enquiry.email.toLowerCase().includes(searchTerm);
        const contactMatch = enquiry.contact && enquiry.contact.toLowerCase().includes(searchTerm);
        const countryMatch = !this.filterOptions.country || enquiry.country === this.filterOptions.country;
        const englishTestMatch = !this.filterOptions.englishTest || this.getEnglishTests(enquiry).includes(this.filterOptions.englishTest);
        const otherExamMatch = !this.filterOptions.otherExam || this.getOtherExams(enquiry).includes(this.filterOptions.otherExam);

        return (nameMatch || emailMatch || contactMatch) && countryMatch && englishTestMatch && otherExamMatch;
      });

      this.currentPage = 1;
      this.saveState();
    },
    getEnglishTests(enquiry) {
      const tests = [];
      if (enquiry.ielts !== 'N/A') tests.push('IELTS');
      if (enquiry.pte !== 'N/A') tests.push('PTE');
      if (enquiry.toefl !== 'N/A') tests.push('TOEFL');
      if (enquiry.ellt !== 'N/A') tests.push('ELLT');
      return tests.join(', ');
    },
    getOtherExams(enquiry) {
      const exams = [];
      if (enquiry.sat !== 'N/A') exams.push('SAT');
      if (enquiry.gre !== 'N/A') exams.push('GRE');
      if (enquiry.gmat !== 'N/A') exams.push('GMAT');
      return exams.join(', ');
    },
    formatDate(date) {
      return date ? new Date(date).toISOString().split('T')[0] : '-';
    },
    navigateToEnquiry(id) {
      this.$router.push(`/app/enquiries/record/${id}`);
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.saveState();
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.saveState();
      }
    },
    resetFilters() {
      this.filterOptions = {
        search: '',
        country: '',
        englishTest: '',
        otherExam: ''
      };
      localStorage.removeItem('enquiryTableState');
      this.filterAndPaginate();
    },
    saveState() {
      const state = {
        currentPage: this.currentPage,
        filterOptions: this.filterOptions,
        filterPanelOpen: this.filterPanelOpen
      };
      localStorage.setItem('enquiryTableState', JSON.stringify(state));
    },
    loadState() {
      const savedState = localStorage.getItem('enquiryTableState');
      if (savedState) {
        try {
          const state = JSON.parse(savedState);
          this.currentPage = state.currentPage || 1;
          this.filterOptions = state.filterOptions || {};
          this.filterPanelOpen = state.filterPanelOpen || false;
        } catch (e) {
          localStorage.removeItem('enquiryTableState');
        }
      }
    },
    downloadData() {
      const headers = ["Student Name", "Email", "Contact", "Country", "English Tests", "Other Exams", "Date Added"];
      const data = [headers];
      const startIndex = (this.currentPage - 1) * this.itemsPerPage;
      const endIndex = Math.min(startIndex + this.itemsPerPage, this.filteredRows.length);

      this.filteredRows.slice(startIndex, endIndex).forEach(enquiry => {
        const rowData = [
          enquiry.uname || '',
          enquiry.email || '',
          enquiry.contact || '',
          enquiry.country || '',
          this.getEnglishTests(enquiry),
          this.getOtherExams(enquiry),
          this.formatDate(enquiry.created_at)
        ];
        data.push(rowData);
      });

      const ws = XLSX.utils.aoa_to_sheet(data);
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Enquiries');
      XLSX.writeFile(wb, `enquiries_data_${new Date().toISOString().split('T')[0]}.xlsx`);
    },
    importFile(event) {
      const formData = new FormData(event.target);
      fetch('/api/enquiries/import', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          this.closeModal();
          Swal.fire({
            title: 'Success!',
            text: data.message,
            icon: 'success',
            confirmButtonText: 'OK'
          });
          this.fetchEnquiries();
        } else {
          throw new Error(data.message);
        }
      })
      .catch(error => {
        console.error('Import error:', error);
        Swal.fire({
          title: 'Error!',
          text: error.message || 'Failed to import file. Please try again.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      });
    }
  }
};
</script>

<style>
.main-container { background-color: #f8f9fa; }
.icon-btn {
  position: relative; display: inline-flex; align-items: center; justify-content: center;
  width: 40px; height: 40px; border-radius: 8px; border: 1px solid #dee2e6;
  background: white; color: #6c757d; text-decoration: none; transition: all 0.2s ease; cursor: pointer;
}
.icon-btn:hover {
  background: #f8f9fa; border-color: #adb5bd; color: #495057;
  transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.icon-btn.btn-success:hover { background: #198754; border-color: #198754; color: white; }
.icon-btn.btn-primary:hover { background: #0d6efd; border-color: #0d6efd; color: white; }
.icon-btn.btn-outline-primary:hover { background: #0d6efd; border-color: #0d6efd; color: white; }
.icon-btn.btn-outline-secondary:hover { background: #6c757d; border-color: #6c757d; color: white; }
.icon-btn.btn-outline-danger:hover { background: #dc3545; border-color: #dc3545; color: white; }
.tooltip-text {
  position: absolute; bottom: -35px; left: 50%; transform: translateX(-50%);
  background: #333; color: white; padding: 6px 12px; border-radius: 4px;
  font-size: 12px; white-space: nowrap; opacity: 0; visibility: hidden;
  transition: all 0.2s ease; z-index: 1000;
}
.tooltip-text::before {
  content: ''; position: absolute; top: -4px; left: 50%; transform: translateX(-50%);
  border-left: 4px solid transparent; border-right: 4px solid transparent; border-bottom: 4px solid #333;
}
.icon-btn:hover .tooltip-text { opacity: 1; visibility: visible; }
.table-hover tbody tr:hover { background-color: rgba(46, 125, 50, 0.05); }
.user-info { position: relative; }
.user-actions {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  opacity: 0; transition: opacity 0.2s ease; z-index: 10;
}
.user-info:hover .user-actions { opacity: 1; }
.action-dots {
  background: none; border: none; color: #6c757d; font-size: 16px; cursor: pointer;
  padding: 4px 8px; border-radius: 4px; transition: all 0.2s ease;
}
.action-dots:hover { color: #495057; }
.filter-panel-transition { transition: all 0.3s ease; }
.dropdown-menu { z-index: 1010; padding: 0px !important; border-radius: 8px; }
.dropdown-item.text-danger:hover { background-color: #fbebeb !important; color: #dc3545 !important; }
.user-name-link:hover h6 { color: #2e7d32; }
.modal { display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
.modal-content { background-color: #fff; margin: 10% auto; padding: 25px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); width: 90%; max-width: 500px; border: none; }
.modal-title { font-size: 1.25rem; font-weight: 600; color: #2e7d32; margin-bottom: 20px; }
.close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; transition: color 0.2s; }
.close:hover { color: #666; }
</style>
