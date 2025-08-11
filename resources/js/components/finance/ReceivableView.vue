<template>
  <div class="main-container">
    <div class="widget-content searchable-container list">
      <!-- Header Card -->
      <div class="card card-body mb-4 shadow-sm">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h3 class="page-title">Accounts Receivable</h3>
          </div>
          <div class="col-md-6 d-flex justify-content-end">
            <button
              type="button"
              class="btn btn-success"
              @click="downloadPDF"
              :disabled="isLoading || !processedApplications.length"
            >
              <i class="fas fa-file-pdf me-2"></i>Download as PDF
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-container text-center p-5">
        <div class="spinner-border text-success" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Loading Receivable Accounts...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-container">
        <div class="alert alert-danger mx-auto" style="max-width: 800px;">
          <h5><i class="fas fa-exclamation-triangle me-2"></i>Error Loading Data</h5>
          <p><strong>Details:</strong> {{ error }}</p>
          <div class="mt-3">
            <button @click="retryLoad" class="btn btn-outline-danger btn-sm me-2">
              <i class="fas fa-redo me-1"></i>Retry ({{ retryAttempts }}/{{ maxRetryAttempts }})
            </button>
            <button @click="goBack" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i>Go Back
            </button>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div v-else>
        <!-- No Invoices Found -->
        <div v-if="!processedApplications.length" class="no-data-container text-center p-5 mx-auto bg-light rounded" style="max-width: 800px;">
          <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
          <h4 class="text-muted">No Receivable Accounts Found</h4>
          <p class="text-secondary">There are no applications matching the criteria for accounts receivable.</p>
          <button @click="retryLoad" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-refresh me-1"></i>Refresh Data
          </button>
        </div>

        <!-- Invoice Container -->
        <div v-else class="invoice-container-wrapper">
          <div
            v-for="app in processedApplications"
            :key="app.id"
            class="invoice-document"
            ref="invoiceDocuments"
          >
            <!-- Header with curved background -->
            <div class="header-bg">
              <div class="header-content">
                <div class="logo-section">
                  <div class="company-name">WRI Education Consultancy</div>
                </div>
              </div>
            </div>

            <!-- Invoice Header -->
            <div class="invoice-header">
              <div class="d-flex justify-content-between w-100">
                <div class="invoice-title">INVOICE</div>
                <div class="logo-icon">
                  <img src="/img/wri.png" width="80" alt="Company Logo" crossorigin="anonymous">
                </div>
              </div>
              <div class="invoice-details">
                <div class="invoice-info">
                  <strong>Invoice Number:</strong> INV-{{ app.id }}<br>
                  <strong>Invoice Date:</strong> {{ formatDate(new Date()) }}<br>
                  <strong>Due Date:</strong> {{ formatDate(addDays(new Date(), 5)) }}
                </div>
              </div>
            </div>

            <!-- Billing Information -->
            <div class="billing-section">
              <div class="bill-to">
                <div class="section-title">BILL TO:</div>
                <div class="billing-details">
                  <strong>Name:</strong> {{ app.name || 'N/A' }}<br>
                  <strong>Product:</strong> {{ app.english || 'N/A' }}<br>
                  <strong>University:</strong> {{ app.university || 'N/A' }}<br>
                  <strong>Intake:</strong> {{ app.intake || 'N/A' }}
                </div>
              </div>
              <div class="payment-info">
                <div class="section-title">PAYMENT INSTRUCTIONS:</div>
                <div class="payment-details">
                  Pay Cheque to:<br>
                  <strong>HANOVER & TYKE</strong>
                </div>
              </div>
            </div>

            <!-- Items Table -->
            <div class="table-container">
              <table class="invoice-table">
                <thead>
                  <tr>
                    <th>ITEM</th>
                    <th>DESCRIPTION</th>
                    <th>COMMISSION</th>
                    <th>RATE</th>
                    <th>AMOUNT</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>Tuition Fees Paid</td>
                    <td>{{ app.commissionValueText }}</td>
                    <td>${{ formatCurrency(app.fee) }}</td>
                    <td>${{ formatCurrency(app.calculatedCommissionAmount) }}</td>
                  </tr>
                  <tr v-if="app.bonusValue > 0">
                    <td>2</td>
                    <td>Bonus</td>
                    <td></td>
                    <td>${{ formatCurrency(app.bonusValue) }}</td>
                    <td>${{ formatCurrency(app.bonusValue) }}</td>
                  </tr>
                  <tr v-if="app.incentiveValue > 0">
                    <td>{{ app.bonusValue > 0 ? '3' : '2' }}</td>
                    <td>Incentive</td>
                    <td></td>
                    <td>${{ formatCurrency(app.incentiveValue) }}</td>
                    <td>${{ formatCurrency(app.incentiveValue) }}</td>
                  </tr>
                  <tr v-if="app.progressiveCommissionValue > 0">
                    <td>{{ (app.bonusValue > 0 ? 1 : 0) + (app.incentiveValue > 0 ? 1 : 0) + 2 }}</td>
                    <td>Progressive Commission</td>
                    <td></td>
                    <td>${{ formatCurrency(app.progressiveCommissionValue) }}</td>
                    <td>${{ formatCurrency(app.progressiveCommissionValue) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Totals Section -->
            <div class="totals-section">
              <div>
                <div class="total-row subtotal">
                  <span>Subtotal</span>
                  <span>${{ formatCurrency(app.totalValueUSD) }}</span>
                </div>

                <div class="total-row editable-field" @dblclick="startEditing(app.id, 'exchange_rate', app.exchangeRate)">
                  <span>Exchange Rate</span>
                  <div class="exchange-rate-controls">
                    <span v-if="!isEditing(app.id, 'exchange_rate')" class="display-value">
                      NPR {{ formatCurrency(app.exchangeRate) }}
                    </span>
                    <div v-else class="edit-input">
                      <input
                        type="number"
                        v-model.number="editingState.value"
                        @blur="confirmAndSaveChanges(app)"
                        @keydown.enter.prevent="confirmAndSaveChanges(app)"
                        @keydown.esc.prevent="cancelEdit"
                        class="edit-field-input"
                        ref="editInput"
                        step="0.0001"
                        min="0.0001"
                      >
                    </div>
                  </div>
                </div>

                <div class="total-row total-npr">
                  <span>Total</span>
                  <span class="value-wrapper">
                    <span>NPR</span>
                    <span>{{ formatCurrency(app.totalValueNPR) }}</span>
                  </span>
                </div>

                <div class="total-row paid-row editable-field" @dblclick="startEditing(app.id, 'paid', app.paidValue)">
                  <span>Paid</span>
                  <div class="paid-controls">
                    <span v-if="!isEditing(app.id, 'paid')" class="display-value">
                      NPR {{ formatCurrency(app.paidValue) }}
                    </span>
                    <div v-else class="edit-input">
                      <input
                        type="number"
                        v-model.number="editingState.value"
                        @blur="confirmAndSaveChanges(app)"
                        @keydown.enter.prevent="confirmAndSaveChanges(app)"
                        @keydown.esc.prevent="cancelEdit"
                        class="edit-field-input"
                        ref="editInput"
                        step="0.01"
                        min="0"
                      >
                    </div>
                  </div>
                </div>

                <div class="total-row final">
                  <span>BALANCE DUE: NPR {{ formatCurrency(app.balanceDueNPR) }}</span>
                </div>
              </div>
            </div>

            <!-- Footer Section -->
            <div class="terms-section">
              <div class="terms-title">TERMS AND CONDITIONS:</div>
              <div class="terms-text">
                Payment is due within 5 days from the invoice date.
              </div>
              <div class="signature-section">
                <p>Authorized Signatory</p>
              </div>
            </div>
            <div class="bottom-curve"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { jsPDF } from 'jspdf';
import html2canvas from 'html2canvas';
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
  name: 'AccountsReceivableView',
  data() {
    return {
      rawApplications: [],
      commissions: [],
      isLoading: true,
      error: null,
      editingState: {
        appId: null,
        field: null,
        value: 0,
        originalValue: 0
      },
      retryAttempts: 0,
      maxRetryAttempts: 3,
      type: 'receivable'
    };
  },
  computed: {
  processedApplications() {
    if (!this.rawApplications) {
      console.warn('Raw applications not loaded yet');
      return [];
    }

    if (!Array.isArray(this.rawApplications)) {
      console.warn('Invalid data structure for applications');
      return [];
    }

    // Only check if rawApplications is empty, not commissions
    if (this.rawApplications.length === 0) {
      return [];
    }

    // Ensure commissions is an array, default to empty array if not loaded
    if (!Array.isArray(this.commissions)) {
      this.commissions = [];
    }

    return this.rawApplications
      .filter(app => app && app.id)
      .map(app => {
        try {
          const processedApp = this.processApplication(app);
          return processedApp;
        } catch (error) {
          console.error('Error processing application:', app, error);
          return null;
        }
      })
      .filter(app => app !== null);
  }
},
  methods: {
 processApplication(app) {
  let commissionValueText = 'N/A';
  let matchingCommission = null;
  let commissionRate = 0;
  let bonusValue = 0;
  let incentiveValue = 0;
  let progressiveCommissionValue = 0;

  // Only try to find matching commission if commissions array exists and has data
  if (Array.isArray(this.commissions) && this.commissions.length > 0 && app.university && app.english) {
    matchingCommission = this.commissions.find(c =>
      c && c.university && c.product &&
      String(c.university).trim().toLowerCase() === String(app.university).trim().toLowerCase() &&
      String(c.product).trim().toLowerCase() === String(app.english).trim().toLowerCase()
    );
  }

  if (matchingCommission) {
    let commissionTypes = matchingCommission.commission_types;
    if (typeof commissionTypes === 'string') {
      try {
        commissionTypes = JSON.parse(commissionTypes);
      } catch (e) {
        console.error(`Invalid JSON for commission_types ID ${matchingCommission.id}:`, e);
        commissionTypes = {};
      }
    }
    if (commissionTypes && typeof commissionTypes === 'object') {
      for (const [key, value] of Object.entries(commissionTypes)) {
        if (!['bonus', 'intensive', 'incentive'].includes(key.toLowerCase())) {
          const parsedValue = parseFloat(value);
          if (!isNaN(parsedValue)) {
            commissionRate = parsedValue / 100;
            commissionValueText = `${key}: ${value}%`;
            break;
          }
        }
      }
    }
    bonusValue = matchingCommission.has_bonus_commission ?
      (parseFloat(matchingCommission.bonus_commission) || 0) : 0;
    incentiveValue = matchingCommission.has_incentive_commission ?
      (parseFloat(matchingCommission.incentive_commission) || 0) : 0;
    progressiveCommissionValue = matchingCommission.has_progressive_commission ?
      (parseFloat(matchingCommission.progressive_commission) || 0) : 0;
  }

  const fee = parseFloat(app.fee) || 0;
  const calculatedCommissionAmount = fee * commissionRate;
  const totalValueUSD = calculatedCommissionAmount + bonusValue + incentiveValue + progressiveCommissionValue;

  // Handle commission transaction data with better defaults
  let paidValue = 0;
  let exchangeRate = 135.0; // Default NPR exchange rate
  let hasCommissionData = false;

  // Check if commission_transaction exists and has proper data
  if (app.commission_transaction) {
    hasCommissionData = true;
    paidValue = parseFloat(app.commission_transaction.paid) || 0;
    exchangeRate = parseFloat(app.commission_transaction.exchange_rate) || 135.0;
    
    console.log('Commission Transaction data found:', {
      paidValue,
      exchangeRate,
      commissionTransaction: app.commission_transaction,
    });
  } else {
    console.log('No commission transaction data - using defaults:', {
      paidValue,
      exchangeRate,
      applicationId: app.id
    });
  }

  const totalValueNPR = totalValueUSD * exchangeRate;
  const balanceDueNPR = Math.max(0, totalValueNPR - paidValue);

  return {
    ...app,
    fee,
    commissionValueText,
    calculatedCommissionAmount,
    bonusValue,
    incentiveValue,
    progressiveCommissionValue,
    totalValueUSD,
    paidValue,
    exchangeRate,
    totalValueNPR,
    balanceDueNPR,
    hasCommissionData,
    commission_id: app.commission_id || matchingCommission?.id || null,
  };
},


    async fetchData(id) {
      if (!id || id === 'undefined' || id === 'null') {
        this.error = 'Invalid or missing ID parameter. Please check the URL.';
        this.isLoading = false;
        return;
      }

      this.isLoading = true;
      this.error = null;

      try {
        console.log('Fetching data for ID:', id);

        const response = await axios.get(`/api/accountreceivable/${encodeURIComponent(id)}`);

        if (!response.data) {
          throw new Error('Empty response from server');
        }

        console.log('Data fetched successfully:', response.data);

        this.rawApplications = Array.isArray(response.data.applications) ? response.data.applications : [];
        this.commissions = Array.isArray(response.data.commissions) ? response.data.commissions : [];

        this.retryAttempts = 0;

        console.log(`Loaded ${this.rawApplications.length} applications and ${this.commissions.length} commissions`);
        console.log('Fetched Applications:', this.rawApplications);
        console.log('Fetched Commissions:', this.commissions);

      } catch (err) {
        console.error("Failed to fetch data:", err);
        this.error = this.getErrorMessage(err);

        if (this.retryAttempts < this.maxRetryAttempts && this.isNetworkError(err)) {
          console.log(`Auto-retrying in 2 seconds (attempt ${this.retryAttempts + 1}/${this.maxRetryAttempts})`);
          setTimeout(() => {
            this.retryLoad();
          }, 2000);
        } else {
          this.showErrorAlert(this.error);
        }
      } finally {
        this.isLoading = false;
      }
    },

    retryLoad() {
      if (this.retryAttempts < this.maxRetryAttempts) {
        this.retryAttempts++;
        const id = this.$route?.params?.id;
        if (id) {
          this.fetchData(id);
        } else {
          this.error = 'No ID parameter available for retry. Please refresh the page.';
          this.isLoading = false;
        }
      } else {
        this.showErrorAlert('Maximum retry attempts reached. Please refresh the page or contact support.');
      }
    },

    goBack() {
      try {
        if (this.$router) {
          this.$router.go(-1);
        } else {
          window.history.back();
        }
      } catch (error) {
        console.error('Navigation error:', error);
        window.location.href = '/';
      }
    },

    getErrorMessage(error) {
      if (error.response) {
        const status = error.response.status;
        const message = error.response.data?.message;

        switch (status) {
          case 404:
            return 'Data not found. Please check if the ID is correct.';
          case 403:
            return 'Access denied. You may not have permission to view this data.';
          case 500:
            return 'Server error. Please try again later or contact support.';
          default:
            return message || `Server error (${status}). Please try again.`;
        }
      } else if (error.request) {
        return 'Unable to connect to server. Please check your internet connection.';
      } else {
        return error.message || 'An unexpected error occurred';
      }
    },

    isNetworkError(error) {
      return !error.response && error.request;
    },

    startEditing(appId, field, currentValue) {
      if (this.editingState.appId) {
        Swal.fire({
          title: 'Info',
          text: 'Please save or cancel the current edit first.',
          icon: 'info',
          timer: 2000,
          showConfirmButton: false
        });
        return;
      }

      this.editingState = {
        appId: appId,
        field: field,
        value: currentValue,
        originalValue: currentValue
      };

      this.$nextTick(() => {
        const input = this.$refs.editInput;
        if (input) {
          const el = Array.isArray(input) ? input[0] : input;
          if (el && el.focus) {
            el.focus();
            el.select();
          }
        }
      });
    },

    isEditing(appId, field) {
      return this.editingState.appId === appId && this.editingState.field === field;
    },

    cancelEdit() {
      this.editingState = { appId: null, field: null, value: 0, originalValue: 0 };
    },

    async confirmAndSaveChanges(app) {
      if (!this.editingState.appId || !app || !app.id) return;

      const { field, value, originalValue } = this.editingState;

      if (Math.abs(parseFloat(value) - parseFloat(originalValue)) < 0.0001) {
        this.cancelEdit();
        return;
      }

      const numValue = parseFloat(value);
      if (isNaN(numValue) || numValue < 0) {
        this.showErrorAlert('Please enter a valid positive number.');
        return;
      }

      const result = await Swal.fire({
        title: 'Save Changes?',
        html: `Update <b>${field.replace('_', ' ')}</b> to <b>${numValue}</b>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#388E3C',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
      });

      if (result.isConfirmed) {
        await this.saveData(app, field, numValue);
      }
      this.cancelEdit();
    },
async saveData(app, field, value) {
    if (!app || !app.id || !field || value === undefined || value === null) {
        this.showErrorAlert('Invalid data for saving. Please try again.');
        return;
    }

    Swal.fire({
        title: 'Saving...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const receivable = {
            field: field,
            value: value,
            type: this.type,
            commission_id: app.commission_id || null // Include the commission_id
        };

        const response = await axios.post(`/finance/accountreceivableview/${app.id}`, receivable);

        if (!response.data || !response.data.success) {
            throw new Error(response.data?.message || 'Save operation failed');
        }

        Swal.fire({
            title: 'Success!',
            text: response.data.message || 'Changes saved successfully',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });

        const id = this.$route?.params?.id;
        if (id) {
            await this.fetchData(id);
        }
    } catch (error) {
        console.error('Save failed:', error);
        this.showErrorAlert(this.getErrorMessage(error));
    }
},


    async downloadPDF() {
      const allDocuments = this.$refs.invoiceDocuments;
      if (!allDocuments || allDocuments.length === 0) {
        this.showErrorAlert('No invoices available for download.');
        return;
      }

      Swal.fire({
        title: 'Generating PDF...',
        text: 'Please wait while we prepare your document.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });

      try {
        await document.fonts.ready;
      } catch (err) {
        console.warn('Font loading check failed, proceeding anyway.', err);
      }

      const pdf = new jsPDF('p', 'mm', 'a4');
      const pdfWidth = 210;
      const pdfHeight = 297;

      try {
        for (let i = 0; i < allDocuments.length; i++) {
          const originalDocElement = allDocuments[i];

          const canvas = await html2canvas(originalDocElement, {
            scale: 3,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            scrollX: 0,
            scrollY: 0,
            windowWidth: originalDocElement.scrollWidth,
            windowHeight: originalDocElement.scrollHeight,
          });

          const imgData = canvas.toDataURL('image/png', 1.0);

          if (i > 0) pdf.addPage();

          const canvasAspectRatio = canvas.width / canvas.height;
          const pageAspectRatio = pdfWidth / pdfHeight;
          let finalWidth, finalHeight;

          if (canvasAspectRatio > pageAspectRatio) {
            finalWidth = pdfWidth;
            finalHeight = pdfWidth / canvasAspectRatio;
          } else {
            finalHeight = pdfHeight;
            finalWidth = pdfHeight * canvasAspectRatio;
          }

          pdf.addImage(imgData, 'PNG', 0, 0, finalWidth, finalHeight);
        }

        const fileName = `WRI-Accounts-Receivable-${new Date().toISOString().slice(0, 10)}.pdf`;
        pdf.save(fileName);

        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Your PDF has been generated and downloaded.',
          timer: 2000,
          showConfirmButton: false
        });

      } catch (error) {
        console.error('PDF generation failed:', error);
        this.showErrorAlert('Failed to generate PDF. Please try again or contact support.');
      }
    },

    formatCurrency(value, digits = 2) {
      const num = parseFloat(value);
      if (isNaN(num)) return '0.00';
      return num.toLocaleString('en-US', {
        minimumFractionDigits: digits,
        maximumFractionDigits: digits
      });
    },

    formatDate(date) {
      if (!date || !(date instanceof Date)) return 'N/A';
      return date.toLocaleDateString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric'
      });
    },

    addDays(date, days) {
      if (!date || !(date instanceof Date)) return new Date();
      const result = new Date(date);
      result.setDate(result.getDate() + days);
      return result;
    },

    showErrorAlert(message) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        html: message,
        confirmButtonColor: '#d33'
      });
    }
  },

  async mounted() {
    try {
      if (!this.$route || !this.$route.params) {
        this.error = 'Route parameters not available. Please check your routing configuration.';
        this.isLoading = false;
        return;
      }

      const id = this.$route.params.id;
      console.log('Component mounted with ID:', id);

      if (!id) {
        this.error = 'No ID parameter found in route. Please check the URL.';
        this.isLoading = false;
        return;
      }

      await this.fetchData(id);
    } catch (error) {
      console.error('Component mount error:', error);
      this.error = 'Failed to initialize component. Please refresh the page.';
      this.isLoading = false;
    }
  },

  beforeUnmount() {
    this.cancelEdit();
  }
};
</script>

<style scoped>
:root {
  --primary-color: #4CAF50;
  --secondary-color: #388E3C;
  --accent-color: #FF9800;
  --light-bg: #F8FBF9;
  --dark-text: #2C3E50;
  --border-color: #DCE7DC;
  --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.main-container {
  font-family: var(--font-family);
  background-color: var(--light-bg);
  color: var(--dark-text);
  padding: 2rem;
}

.page-title {
  font-size: 1.75rem;
  font-weight: bold;
  color: var(--secondary-color);
  margin-bottom: 0;
}

.card.card-body.mb-4.shadow-sm {
  border-radius: 0.5rem;
  background-color: #fff;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

/* Loading and Error States */
.loading-container {
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.error-container .alert {
  border-radius: 0.5rem;
}

.no-data-container {
  border: 2px dashed #ddd;
}

.invoice-container-wrapper {
  max-width: 800px;
  margin: 0 auto;
}

.invoice-document {
  border: 1px solid #eee;
  background: white;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  margin-bottom: 2.5rem;
  display: flex;
  flex-direction: column;
}

.header-bg {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
  height: 80px;
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
}

.header-bg::after {
  content: '';
  position: absolute;
  bottom: -25px;
  left: -50px;
  right: -50px;
  height: 50px;
  background: white;
  border-radius: 50%;
}

.header-content {
  position: relative;
  z-index: 2;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.logo-section {
  color: white;
}

.company-name {
  font-family: "Merriweather", serif;
  font-size: 24px;
  font-weight: bold;
}

.invoice-header {
  padding: 0px 30px 15px;
  position: relative;
  z-index: 3;
  flex-shrink: 0;
}

.invoice-title {
  font-size: 28px;
  font-weight: bold;
  color: var(--secondary-color);
  margin-top: -10px;
}

.invoice-details {
  margin-top: 15px;
  margin-bottom: 20px;
}

.invoice-info {
  font-size: 13px;
  line-height: 1.5;
}

.invoice-info strong {
  color: var(--secondary-color);
}

.billing-section {
  padding: 0 30px 20px;
  display: flex;
  justify-content: space-between;
  gap: 30px;
  flex-shrink: 0;
}

.bill-to,
.payment-info {
  flex: 1;
}

.section-title {
  font-size: 14px;
  font-weight: bold;
  color: var(--secondary-color);
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.billing-details,
.payment-details {
  font-size: 13px;
  color: #666;
  line-height: 1.6;
}

.table-container {
  padding: 0 30px;
  margin-bottom: 20px;
  flex-grow: 1;
}

.invoice-table {
  width: 100%;
  border-collapse: collapse;
}

.invoice-table thead {
  background: var(--secondary-color);
  color: white;
}

.invoice-table th {
  padding: 10px 12px;
  text-align: left;
  font-weight: bold;
  font-size: 13px;
}

.invoice-table th:last-child,
.invoice-table td:last-child {
  text-align: right;
}

.invoice-table tbody tr {
  border-bottom: 1px solid #eee;
}

.invoice-table tbody tr:last-child {
  border-bottom: none;
}

.invoice-table td {
  padding: 10px 12px;
  font-size: 13px;
  color: #666;
  vertical-align: middle;
}

.totals-section {
  padding: 0 30px 25px;
  display: flex;
  justify-content: flex-end;
  flex-shrink: 0;
}

.totals-box {
  min-width: 280px;
  width: 40%;
}

.total-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  font-size: 13px;
  align-items: center;
}

.total-row.subtotal {
  border-top: 2px solid #eee;
  padding-top: 12px;
  font-weight: 500;
}

.value-wrapper {
  display: inline-flex;
  align-items: baseline;
  gap: 5px;
}

.editable-field .display-value {
  cursor: pointer;
  border-bottom: 1px dotted transparent;
  transition: all 0.2s ease;
  padding: 2px 4px;
  border-radius: 3px;
}

.editable-field .display-value:hover {
  border-bottom-color: var(--primary-color);
  background-color: #f0f8f0;
}

.edit-field-input {
  width: 100px;
  padding: 2px 4px;
  border: 1px solid var(--primary-color);
  border-radius: 3px;
  box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
  outline: none;
}

.total-row.final {
  background: var(--secondary-color);
  color: white;
  padding: 12px 15px;
  font-size: 16px;
  font-weight: bold;
  margin-top: 8px;
  border-radius: 5px;
  text-align: right;
}

.terms-section {
  padding: 20px 30px;
  border-top: 2px solid #f0f0f0;
  flex-shrink: 0;
  margin-top: 20px;
}

.terms-title {
  font-size: 14px;
  font-weight: bold;
  color: var(--secondary-color);
  margin-bottom: 8px;
}

.terms-text {
  font-size: 12px;
  color: #666;
  line-height: 1.4;
}

.signature-section {
  margin-top: 2.5rem;
  border-top: 1px solid #555;
  padding-top: 8px;
  width: 200px;
}

.bottom-curve {
  height: 80px;
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
  position: relative;
  flex-shrink: 0;
}

.bottom-curve::before {
  content: '';
  position: absolute;
  top: -20px;
  left: -50px;
  right: -50px;
  height: 40px;
  background: white;
  border-radius: 50%;
}

#downloadPDFBtn {
  background-color: var(--primary-color);
  border: none;
  font-weight: 500;
}

#downloadPDFBtn:hover {
  background-color: var(--secondary-color);
}

.logo-icon img {
  width: 60px !important;
  height: auto;
}
</style>
