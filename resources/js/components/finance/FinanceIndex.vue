<template>
  <div class="main-container">
    <div id="financeContentArea" class="widget-content searchable-container list">
      <div class="card card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
          <div class="mb-3 mb-md-0">
            <div class="d-flex align-items-center mb-2">
              <i class="ti ti-database text-2xl text-[#2e7d32] me-2"></i>
              <div class="text-[24px] text-[#2e7d32] font-semibold font-g">
                {{ pageTitle }}
              </div>
            </div>
            <div class="btn-group" role="group">
              <button
                type="button"
                class="btn"
                :class="{ active: view === 'receivable' }"
                @click="setView('receivable')"
              >
                <i class="ti ti-chart-bar me-1"></i> Accounts Receivable
              </button>
              <button
                type="button"
                class="btn"
                :class="{ active: view === 'payable' }"
                @click="setView('payable')"
              >
                <i class="ti ti-moneybag me-1"></i> Commission Payable
              </button>
              <button
                type="button"
                class="btn"
                :class="{ active: view === 'commissions' }"
                @click="setView('commissions')"
              >
                <i class="ti ti-coins me-1"></i> Commission History
              </button>
            </div>
          </div>
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="position-relative" style="width: 250px;">
              <input
                type="text"
                class="form-control product-search ps-5"
                v-model="searchTerm"
                placeholder="Search this table..."
              />
              <i
                class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"
              ></i>
            </div>
         
            <button
              class="icon-btn btn-outline-primary"
              data-bs-toggle="modal"
              :data-bs-target="view === 'receivable' ? '#receivableImportModal' : '#payableImportModal'"
            >
              <i class="ti ti-file-import fs-5"></i><span class="tooltip-text">Import</span>
            </button>
            <button @click="exportToExcel" class="icon-btn btn-outline-primary">
              <i class="ti ti-file-export fs-5"></i><span class="tooltip-text">Export</span>
            </button>
          </div>
        </div>
      </div>

      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-success" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <div v-else class="card border-0 shadow-sm mt-4">

        <!-- Receivable Table -->
        <div v-if="view === 'receivable'" class="table-responsive">
          <table class="table table-hover text-nowrap mb-0 align-middle" ref="receivableDataTable">
            <thead class="table-light">
              <tr>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Student</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-calendar-time me-1"></i><span>Intake</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-file-dollar me-1"></i><span>Total Fees
                      Paid</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-percentage me-1"></i><span>Commission</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="bi bi-gift me-1"></i><span>Bonus</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="bi bi-star me-1"></i><span>Incentive</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-wallet me-1"></i><span>Total (USD)</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-arrows-exchange me-1"></i><span>Ex.
                      Rate</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-cash-banknote me-1"></i><span>Paid (NPR)</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-cash me-1"></i><span>Balance (NPR)</span>
                  </div>
                </th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <template v-if="filteredApplications.length > 0">
                <tr v-for="app in filteredApplications" :key="app.id">
                  <td>
                    <h6 class="fw-semibold mb-1 student-name" @click="navigateToDetail(app, 'receivable')">
                      {{ app.name || 'N/A' }}
                    </h6>
                    <small class="text-muted d-block">{{ app.university || 'N/A' }}</small>
                    <small class="text-muted d-block">{{ app.english || 'N/A' }}</small>
                  </td>
                  <td>{{ app.intake || 'N/A' }}</td>
                  <td>${{ formatNumber(app.fee || 0) }}</td>
                  <td>{{ app.displayedCommissionValueString || 'N/A' }}</td>
                  <td><span class="badge-status"
                      :class="app.displayedBonus > 0 ? 'paid' : 'default'">{{ app.displayedBonus > 0 ? 'Yes' : 'No'
                      }}</span></td>
                  <td><span class="badge-status"
                      :class="app.displayedIncentive > 0 ? 'paid' : 'default'">{{ app.displayedIncentive > 0 ? 'Yes' : 'No'
                      }}</span></td>
                  <td>${{ formatNumber(app.totalValueUSD || 0) }}</td>
                  <td>
                    <div v-if="app.commission_transaction?.type === 'receivable'" class="editable-field"
                      @click.stop>
                      <div v-if="isEditing(app.id, 'exchange_rate')" class="edit-input">
                        <input type="number" :value="app.displayedExchangeRate"
                          @blur="saveField('exchange_rate', $event.target.value, app)"
                          @keydown.enter.prevent="saveField('exchange_rate', $event.target.value, app)"
                          @keydown.esc.prevent="cancelEditing" class="edit-field-input" step="0.01" ref="editInput" />
                      </div>
                      <span v-else class="display-value" @click="startEditing(app, 'exchange_rate')">
                        Npr. {{ formatNumber(app.displayedExchangeRate || 1) }}
                        <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                      </span>
                    </div>
                    <span v-else>-</span>
                  </td>
                  <td>
                    <div v-if="app.commission_transaction?.type === 'receivable'" class="editable-field"
                      @click.stop>
                      <div v-if="isEditing(app.id, 'paid')" class="edit-input">
                        <input type="number" :value="app.displayedPaid"
                          @blur="saveField('paid', $event.target.value, app)"
                          @keydown.enter.prevent="saveField('paid', $event.target.value, app)"
                          @keydown.esc.prevent="cancelEditing" class="edit-field-input" step="0.01" ref="editInput" />
                      </div>
                      <span v-else class="display-value" @click="startEditing(app, 'paid')">
                        Npr. {{ formatNumber(app.displayedPaid || 0) }}
                        <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                      </span>
                    </div>
                    <span v-else>-</span>
                  </td>
                  <td>
                    <span :style="{
                      color: app.balanceDueNPR > 0 ? '#dc3545' : 'inherit',
                      fontWeight: app.balanceDueNPR > 0 ? 'bold' : 'normal'
                    }">Npr. {{ formatNumber(app.balanceDueNPR || 0) }}</span>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" @click.prevent="markAsReceived(app)">Mark as
                            Received</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="11" class="text-center py-4">No Accounts Receivable data available.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Payable Table -->
        <div v-else-if="view === 'payable'" class="table-responsive">
          <table class="table table-hover text-nowrap mb-0 align-middle" ref="payableDataTable">
            <thead class="table-light">
              <tr>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Student</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-calendar-time me-1"></i><span>Intake</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-file-dollar me-1"></i><span>Total Fees
                      Paid</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-percentage me-1"></i><span>Commission</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="bi bi-gift me-1"></i><span>Bonus</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="bi bi-star me-1"></i><span>Incentive</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-wallet me-1"></i><span>Total (USD)</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-arrows-exchange me-1"></i><span>Ex.
                      Rate</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-cash-banknote me-1"></i><span>Paid (NPR)</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-cash me-1"></i><span>Balance (NPR)</span>
                  </div>
                </th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <template v-if="filteredApplications.length > 0">
                <tr v-for="app in filteredApplications" :key="app.id">
                  <td>
                    <h6 class="fw-semibold mb-1 student-name" @click="navigateToDetail(app, 'payable')">
                      {{ app.name || 'N/A' }}
                    </h6>
                    <small class="text-muted d-block">{{ app.university || 'N/A' }}</small>
                    <small class="text-muted d-block">{{ app.english || 'N/A' }}</small>
                  </td>
                  <td>{{ app.intake || 'N/A' }}</td>
                  <td>${{ formatNumber(app.fee || 0) }}</td>
                  <td>{{ app.displayedCommissionValueString || 'N/A' }}</td>
                  <td><span class="badge-status"
                      :class="app.displayedBonus > 0 ? 'paid' : 'default'">{{ app.displayedBonus > 0 ? 'Yes' : 'No'
                      }}</span></td>
                  <td><span class="badge-status"
                      :class="app.displayedIncentive > 0 ? 'paid' : 'default'">{{ app.displayedIncentive > 0 ? 'Yes' : 'No'
                      }}</span></td>
                  <td>${{ formatNumber(app.totalValueUSD || 0) }}</td>
                  <td>
                    <div v-if="app.commission_transaction?.type === 'payable'" class="editable-field"
                      @click.stop>
                      <div v-if="isEditing(app.id, 'exchange_rate')" class="edit-input">
                        <input type="number" :value="app.displayedExchangeRate"
                          @blur="saveField('exchange_rate', $event.target.value, app)"
                          @keydown.enter.prevent="saveField('exchange_rate', $event.target.value, app)"
                          @keydown.esc.prevent="cancelEditing" class="edit-field-input" step="0.01" ref="editInput" />
                      </div>
                      <span v-else class="display-value" @click="startEditing(app, 'exchange_rate')">
                        Npr. {{ formatNumber(app.displayedExchangeRate || 1) }}
                        <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                      </span>
                    </div>
                    <span v-else>-</span>
                  </td>
                  <td>
                    <div v-if="app.commission_transaction?.type === 'payable'" class="editable-field"
                      @click.stop>
                      <div v-if="isEditing(app.id, 'paid')" class="edit-input">
                        <input type="number" :value="app.displayedPaid"
                          @blur="saveField('paid', $event.target.value, app)"
                          @keydown.enter.prevent="saveField('paid', $event.target.value, app)"
                          @keydown.esc.prevent="cancelEditing" class="edit-field-input" step="0.01" ref="editInput" />
                      </div>
                      <span v-else class="display-value" @click="startEditing(app, 'paid')">
                        Npr. {{ formatNumber(app.displayedPaid || 0) }}
                        <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                      </span>
                    </div>
                    <span v-else>-</span>
                  </td>
                  <td>
                    <span :style="{
                      color: app.balanceDueNPR > 0 ? '#dc3545' : 'inherit',
                      fontWeight: app.balanceDueNPR > 0 ? 'bold' : 'normal'
                    }">Npr. {{ formatNumber(app.balanceDueNPR || 0) }}</span>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" @click.prevent="markAsPaidPayable(app)">Mark as Paid</a>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="11" class="text-center py-4">No Commission Payable data available.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Commission History Table -->
        <div v-else-if="view === 'commissions'" class="table-responsive">
          <table class="table table-hover text-nowrap mb-0 align-middle" ref="commissionsDataTable">
            <thead class="table-light">
              <tr>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-calendar me-1"></i><span>Date</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Student</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-building me-1"></i><span>University</span>
                  </div>
                </th>
                 <th>
                  <div class="d-flex align-items-center"><i class="ti ti-building me-1"></i><span>Product</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-calendar-time me-1"></i><span>Intake</span>
                  </div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-category me-1"></i><span>Type</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-coins me-1"></i><span>Commission</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-gift me-1"></i><span>Bonus</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-award me-1"></i><span>Incentive</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-currency-dollar me-1"></i><span>Total
                      (USD)</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-currency-rupee me-1"></i><span>Total
                      (NPR)</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-arrows-exchange me-1"></i><span>Ex.
                      Rate</span></div>
                </th>
                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-check me-1"></i><span>Status</span></div>
                </th>

                <th>
                  <div class="d-flex align-items-center"><i class="ti ti-check me-1"></i><span>Total Paid</span></div>
                </th>


                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <template v-if="filteredCommissions.length > 0">
                <tr v-for="commission in filteredCommissions" :key="commission.id">
                  <td>{{ formatDate(commission.created_at) }}</td>
                  <td>
                    <h6 class="fw-semibold mb-1">{{ commission.student_name || 'N/A' }}</h6>
                    <small class="text-muted">ID: {{ commission.application_id }}</small>
                  </td>
                  <td>{{ commission.university || 'N/A' }}</td>
                   <td>{{ commission.english || 'N/A' }}</td>
                  <td>{{ commission.intake || 'N/A' }}</td>
                  <td>
                    <span class="badge"
                      :class="commission.type === 'receivable' ? 'bg-info' : 'bg-warning'">
                      {{ commission.type === 'receivable' ? 'Receivable' : 'Payable' }}
                    </span>
                  </td>
                  <td>${{ formatNumber(commission.commission_amount || 0) }}</td>
                  <td>${{ formatNumber(commission.bonus_amount || 0) }}</td>
                  <td>${{ formatNumber(commission.incentive_amount || 0) }}</td>
                  <td>${{ formatNumber(commission.total_usd || 0) }}</td>
                  <td>Npr. {{ formatNumber(commission.total_npr || 0) }}</td>
                  <td>{{ formatNumber(commission.exchange_rate || 1) }}</td>
                  <td>
                    <span class="badge" :class="getStatusBadgeClass(commission.status)">
                      {{ commission.status }}
                    </span>
                  </td>

                  <td>{{ commission.paid_amount || 'N/A' }}</td>
                  
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"
                            @click.prevent="viewCommissionDetails(commission)">View Details</a></li>
                        <li v-if="commission.status !== 'completed'"><a class="dropdown-item" href="#"
                            @click.prevent="updateCommissionStatus(commission, 'completed')">Mark as Completed</a></li>
                        <li><a class="dropdown-item text-danger" href="#"
                            @click.prevent="deleteCommission(commission)">Delete</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="13" class="text-center py-4">No commission history available.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div v-if="error" class="alert alert-danger">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import * as XLSX from 'xlsx';

const API_BASE_URL = '/api'; // Define the base URL

export default {
  name: 'FinanceComponent',
  data() {
    return {
      view: 'receivable',
      applications: [],
      commissions: [],
      isLoading: true,
      searchTerm: '',
      editingCell: {
        appId: null,
        field: null,
        originalValue: null,
      },
      error: null,
    };
  },
  computed: {
    pageTitle() {
      if (this.view === 'receivable') return 'Accounts Receivable';
      if (this.view === 'payable') return 'Commission Payable';
      return 'Commission History';
    },
    filteredApplications() {
      if (!this.searchTerm) {
        return this.applications;
      }
      const lowerCaseSearch = this.searchTerm.toLowerCase();
      return this.applications.filter(app => {
        return (app.name && app.name.toLowerCase().includes(lowerCaseSearch)) ||
          (app.university && app.university.toLowerCase().includes(lowerCaseSearch)) ||
          (app.english && app.english.toLowerCase().includes(lowerCaseSearch));
      });
    },
    filteredCommissions() {
      if (!this.searchTerm) {
        return this.commissions;
      }
      const lowerCaseSearch = this.searchTerm.toLowerCase();
      return this.commissions.filter(commission => {
        return (commission.student_name && commission.student_name.toLowerCase().includes(lowerCaseSearch)) ||
          (commission.university && commission.university.toLowerCase().includes(lowerCaseSearch)) ||
          (commission.type && commission.type.toLowerCase().includes(lowerCaseSearch)) ||
          (commission.status && commission.status.toLowerCase().includes(lowerCaseSearch));
      });
    },
  },
  methods: {
    async fetchData() {
      this.isLoading = true;
      this.applications = [];
      this.commissions = [];
      this.error = null;

      try {
        if (this.view === 'commissions') {
          console.log('Fetching commission history...');
          const response = await axios.get(`${API_BASE_URL}/commission-history`);
          console.log('Commission History API Response:', response.data);

          if (response.data.success) {
            this.commissions = response.data.data || response.data.commissions || [];
          } else if (response.data.commissions) {
            this.commissions = response.data.commissions;
          } else if (Array.isArray(response.data)) {
            this.commissions = response.data;
          } else {
            this.commissions = [];
            this.error = 'Failed to fetch commission history: ' + (response.data.message || 'Unknown error');
          }

          console.log('Loaded commissions:', this.commissions.length);
        } else {
          console.log(`Fetching ${this.view} data...`);
          const response = await axios.get(`${API_BASE_URL}/finance`, {
            params: {
              view: this.view,
              status: 'pending',
              exclude_completed: true,
            }
          });
          console.log('Finance API Response:', response.data);

          if (response.data.success) {
            this.applications = response.data.data || response.data.applications || [];
          } else if (response.data.applications) {
            this.applications = response.data.applications;
          } else if (Array.isArray(response.data)) {
            this.applications = response.data;
          } else {
            this.applications = [];
            this.error = 'Failed to fetch finance data: ' + (response.data.message || 'Unknown error');
          }

          console.log('Loaded applications:', this.applications.length);
        }
      } catch (error) {
        console.error("Error fetching data:", error);
        console.error("Error details:", error.response?.data);

        this.error = 'Could not fetch data from the server: ' + (error.response?.data?.message || error.message || 'Unknown error');

        if (this.view === 'commissions') {
          this.commissions = [];
        } else {
          this.applications = [];
        }
      } finally {
        this.isLoading = false;
      }
    },

    setView(newView) {
      if (this.view !== newView) {
        console.log('Switching view to:', newView);
        this.view = newView;
        this.searchTerm = '';
        this.fetchData();
      }
    },

    formatNumber(value, decimals = 2) {
      const num = parseFloat(value);
      if (isNaN(num)) return '0.00';
      return num.toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
      });
    },

    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    getStatusBadgeClass(status) {
      const statusClasses = {
        'received': 'bg-success',
        'paid': 'bg-success',
        'completed': 'bg-success',
        'pending': 'bg-warning',
        'processing': 'bg-info',
        'cancelled': 'bg-danger',
        'failed': 'bg-danger'
      };
      return statusClasses[status] || 'bg-secondary';
    },

    navigateToDetail(app, viewType) {
        if (viewType === 'receivable') {
            this.$router.push({ name: 'ReceivableView', params: { id: app.id } });
        } else if (viewType === 'payable') {
            this.$router.push({ name: 'PayableInvoice', params: { id: app.id } });
        }
    },
    isEditing(appId, field) {
      return this.editingCell.appId === appId && this.editingCell.field === field;
    },

    startEditing(app, field) {
      console.log('Starting edit for app:', app.id, 'field:', field);

      this.editingCell = {
        appId: app.id,
        field: field,
        originalValue: field === 'exchange_rate' ? app.displayedExchangeRate : app.displayedPaid
      };

      this.$nextTick(() => {
        const input = this.$refs.editInput;
        if (input && input.length > 0) {
          input[0].focus();
          input[0].select();
        }
      });
    },

    cancelEditing() {
      console.log('Cancelling edit');
      this.editingCell = {
        appId: null,
        field: null,
        originalValue: null
      };
    },

    async saveField(field, value, app) {
      console.log('Saving field:', field, 'for app:', app.id, 'value:', value);

      if (!value || isNaN(parseFloat(value))) {
        Swal.fire('Error', 'Please enter a valid number.', 'error');
        this.cancelEditing();
        return;
      }

      const numericValue = parseFloat(value);
      if (numericValue < 0) {
        Swal.fire('Error', 'Value cannot be negative.', 'error');
        this.cancelEditing();
        return;
      }

      try {
        const endpoint = `${API_BASE_URL}/finance/update`; // Use API_BASE_URL
        const payload = {
          application_id: app.id,
          field: field,  // Pass the field to update
          value: numericValue, // Pass the numeric value to update
          type: this.view // Pass the type (receivable or payable)
        };

        console.log('Sending update request:', payload);

        const response = await axios.post(endpoint, payload);
        console.log('Update response:', response.data);

        if (response.data.success) {
          // Update local data
          if (field === 'exchange_rate') {
            app.displayedExchangeRate = numericValue;
          } else if (field === 'paid') {
            app.displayedPaid = numericValue;
          }

          // Recalculate balance
          this.recalculateBalance(app);

          Swal.fire('Success', 'Field updated successfully!', 'success');
        } else {
          throw new Error(response.data.message || 'Update failed');
        }
      } catch (error) {
        console.error('Error updating field:', error);
        const errorMessage = error.response?.data?.message || error.message || 'Could not update field.';
        Swal.fire('Error', errorMessage, 'error');

        // Restore original value
        if (field === 'exchange_rate') {
          app.displayedExchangeRate = this.editingCell.originalValue;
        } else if (field === 'paid') {
          app.displayedPaid = this.editingCell.originalValue;
        }
      } finally {
        this.cancelEditing();
      }
    },

    recalculateBalance(app) {
      const totalNPR = (app.totalValueUSD || 0) * (app.displayedExchangeRate || 1);
      const paid = app.displayedPaid || 0;
      app.balanceDueNPR = totalNPR - paid;
      console.log('Recalculated balance for app:', app.id, 'Balance:', app.balanceDueNPR);
    },

    async markAsReceived(app) {
      console.log('Marking as received:', app);

      const result = await Swal.fire({
        title: 'Mark as Received',
        text: 'Are you sure you want to mark this account as received?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark as received',
        cancelButtonText: 'Cancel'
      });

      if (result.isConfirmed) {
        try {
          const response = await axios.put(`/api/markasreceived/${app.id}`, {  // Use API_BASE_URL
            application_id: app.id
          });

          if (response.data.success) {
            Swal.fire('Success', 'Account marked as received!', 'success!');
            this.fetchData(); // Refresh data
          } else {
            throw new Error(response.data.message || 'Failed to mark as received');
          }
        } catch (error) {
          console.error('Error marking as received:', error);
          const errorMessage = error.response?.data?.message || error.message || 'Could not mark as received.';
          Swal.fire('Error', errorMessage, 'error');
        }
      }
    },

    async markAsPaidPayable(app) {
      console.log('Marking as paid:', app);

      const result = await Swal.fire({
        title: 'Mark as Paid',
        text: 'Are you sure you want to mark this commission as paid?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark as paid',
        cancelButtonText: 'Cancel'
      });

      if (result.isConfirmed) {
        try {
          const response = await axios.put(`/api/mark-paid/${app.id}`, {  // Use API_BASE_URL
            application_id: app.id
          });

          if (response.data.success) {
            Swal.fire('Success', 'Commission marked as paid!', 'success');
            this.fetchData(); // Refresh data
          } else {
            throw new Error(response.data.message || 'Failed to mark as paid');
          }
        } catch (error) {
          console.error('Error marking as paid:', error);
          const errorMessage = error.response?.data?.message || error.message || 'Could not mark as paid.';
          Swal.fire('Error', errorMessage, 'error');
        }
      }
    },

    async viewCommissionDetails(commission) {
      console.log('Viewing commission details:', commission);

      const details = `
        <div class="text-start">
          <p><strong>Student:</strong> ${commission.student_name || 'N/A'}</p>
                    <p><strong>University:</strong> ${commission.university || 'N/A'}</p>
          <p><strong>Intake:</strong> ${commission.intake || 'N/A'}</p>
          <p><strong>Type:</strong> ${commission.type || 'N/A'}</p>
          <p><strong>Commission:</strong> $${this.formatNumber(commission.commission_amount || 0)}</p>
          <p><strong>Bonus:</strong> $${this.formatNumber(commission.bonus_amount || 0)}</p>
          <p><strong>Incentive:</strong> $${this.formatNumber(commission.incentive_amount || 0)}</p>
          <p><strong>Total USD:</strong> $${this.formatNumber(commission.total_usd || 0)}</p>
          <p><strong>Total NPR:</strong> Npr. ${this.formatNumber(commission.total_npr || 0)}</p>
          <p><strong>Exchange Rate:</strong> ${this.formatNumber(commission.exchange_rate || 1)}</p>
          <p><strong>Status:</strong> ${commission.status || 'N/A'}</p>
          <p><strong>Date:</strong> ${this.formatDate(commission.created_at)}</p>
        </div>
      `;

      Swal.fire({
        title: 'Commission Details',
        html: details,
        icon: 'info',
        confirmButtonText: 'Close',
        width: '500px'
      });
    },

    async updateCommissionStatus(commission, status) {
      console.log('Updating commission status:', commission.id, 'to:', status);

      const result = await Swal.fire({
        title: 'Update Status',
        text: `Are you sure you want to mark this commission as ${status}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Yes, mark as ${status}`,
        cancelButtonText: 'Cancel'
      });

      if (result.isConfirmed) {
        try {
          const response = await axios.put(`${API_BASE_URL}/commission-history/${commission.id}/status`, {  // Use API_BASE_URL
            status: status
          });

          if (response.data.success) {
            Swal.fire('Success', `Commission marked as ${status}!`, 'success');
            this.fetchData(); // Refresh data
          } else {
            throw new Error(response.data.message || 'Failed to update status');
          }
        } catch (error) {
          console.error('Error updating status:', error);
          const errorMessage = error.response?.data?.message || error.message || 'Could not update status.';
          Swal.fire('Error', errorMessage, 'error');
        }
      }
    },

    async deleteCommission(commission) {
      console.log('Deleting commission:', commission.id);

      const result = await Swal.fire({
        title: 'Delete Commission',
        text: 'Are you sure you want to delete this commission? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc3545'
      });

      if (result.isConfirmed) {
        try {
          const response = await axios.delete(`${API_BASE_URL}/commission-history/${commission.id}`); // Use API_BASE_URL

          if (response.data.success) {
            Swal.fire('Deleted', 'Commission has been deleted.', 'success');
            this.fetchData(); // Refresh data
          } else {
            throw new Error(response.data.message || 'Failed to delete commission');
          }
        } catch (error) {
          console.error('Error deleting commission:', error);
          const errorMessage = error.response?.data?.message || error.message || 'Could not delete commission.';
          Swal.fire('Error', errorMessage, 'error');
        }
      }
    },

    async exportToExcel() {
      console.log('Exporting to Excel for view:', this.view);

      try {
        let data, filename;

        if (this.view === 'commissions') {
          data = this.filteredCommissions.map(commission => ({
            'Date': this.formatDate(commission.created_at),
            'Student': commission.student_name || 'N/A',
            'University': commission.university || 'N/A',
            'Intake': commission.intake || 'N/A',
            'Type': commission.type || 'N/A',
            'Commission (USD)': commission.commission_amount || 0,
            'Bonus (USD)': commission.bonus_amount || 0,
            'Incentive (USD)': commission.incentive_amount || 0,
            'Total (USD)': commission.total_usd || 0,
            'Total (NPR)': commission.total_npr || 0,
            'Exchange Rate': commission.exchange_rate || 1,
            'Status': commission.status || 'N/A'
          }));
          filename = `commission_history_${new Date().toISOString().split('T')[0]}.xlsx`;
        } else {
          data = this.filteredApplications.map(app => ({
            'Student': app.name || 'N/A',
            'University': app.university || 'N/A',
            'English': app.english || 'N/A',
            'Intake': app.intake || 'N/A',
            'Total Fees Paid (USD)': app.fee || 0,
            'Commission': app.displayedCommissionValueString || 'N/A',
            'Bonus': app.displayedBonus > 0 ? 'Yes' : 'No',
            'Incentive': app.displayedIncentive > 0 ? 'Yes' : 'No',
            'Total (USD)': app.totalValueUSD || 0,
            'Exchange Rate': app.displayedExchangeRate || 1,
            'Paid (NPR)': app.displayedPaid || 0,
            'Balance (NPR)': app.balanceDueNPR || 0
          }));
          filename = `${this.view}_${new Date().toISOString().split('T')[0]}.xlsx`;
        }

        if (data.length === 0) {
          Swal.fire('No Data', 'No data available to export.', 'info');
          return;
        }

        // Create workbook and worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet(data);

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, this.view.charAt(0).toUpperCase() + this.view.slice(1));

        // Write file
        XLSX.writeFile(wb, filename);

        Swal.fire('Success', `Data exported to ${filename}`, 'success');
      } catch (error) {
        console.error('Export error:', error);
        Swal.fire('Error', 'Could not export data to Excel.', 'error');
      }
    },
  },

  async mounted() {
    console.log('Finance component mounted');
    await this.fetchData();
  },

  watch: {
    view(newView) {
      console.log('View changed to:', newView);
      this.fetchData();
    }
  }
}
</script>

<style scoped>
.main-container {
  padding: 20px;
}

.student-name {
  cursor: pointer;
  color: #007bff;
  transition: color 0.2s ease;
}

.student-name:hover {
  color: #0056b3;
  text-decoration: underline;
}

.editable-field {
  position: relative;
  min-width: 120px;
}

.display-value {
  display: flex;
  align-items: center;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: background-color 0.2s ease;
}

.display-value:hover {
  background-color: #f8f9fa;
}

.edit-btn {
  background: none;
  border: none;
  color: #6c757d;
  margin-left: 8px;
  padding: 0;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.display-value:hover .edit-btn {
  opacity: 1;
}

.edit-input {
  width: 100%;
}

.edit-field-input {
  width: 100%;
  padding: 4px 8px;
  border: 1px solid #007bff;
  border-radius: 4px;
  font-size: 14px;
}

.edit-field-input:focus {
  outline: none;
  border-color: #0056b3;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.badge-status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.badge-status.paid {
  background-color: #d4edda;
  color: #155724;
}

.badge-status.default {
  background-color: #f8f9fa;
  color: #6c757d;
}

.icon-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.icon-btn:hover .tooltip-text {
  opacity: 1;
  visibility: visible;
}

.tooltip-text {
  position: absolute;
  bottom: -30px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #333;
  color: white;
  padding: 4px 8px;
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
  top: -5px;
  left: 50%;
  transform: translateX(-50%);
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-bottom: 5px solid #333;
}

.btn-group .btn {
  padding: 8px 16px;
  border: 1px solid #dee2e6;
  background-color: white;
  color: #6c757d;
  transition: all 0.2s ease;
}

.btn-group .btn:hover {
  background-color: #f8f9fa;
  color: #495057;
}

.btn-group .btn.active {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}

.btn-group .btn:first-child {
  border-top-left-radius: 6px;
  border-bottom-left-radius: 6px;
}

.btn-group .btn:last-child {
  border-top-right-radius: 6px;
  border-bottom-right-radius: 6px;
}

.btn-group .btn:not(:last-child) {
  border-right: none;
}

.table th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #495057;
  border-bottom: 2px solid #dee2e6;
}

.table td {
  vertical-align: middle;
  padding: 12px;
}

.table-hover tbody tr:hover {
  background-color: #f8f9fa;
}

.card {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  border: 1px solid rgba(0, 0, 0, 0.125);
}

.product-search {
  position: relative;
}

.product-search::placeholder {
  color: #6c757d;
}

@media (max-width: 768px) {
  .d-flex.flex-wrap {
    flex-direction: column;
    gap: 1rem;
  }

  .btn-group {
    flex-direction: column;
    width: 100%;
  }

  .btn-group .btn {
    border-radius: 6px !important;
    margin-bottom: 2px;
  }
}
</style>