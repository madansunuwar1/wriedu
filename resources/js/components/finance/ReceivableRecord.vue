<template>
  <div class="card">
    <div class="border-bottom title-part-padding">
      <h4 class="card-title mb-0">Commission Rate</h4>
    </div>
    <div class="card-body">
      <!-- Toast notification container -->
      <div class="toast-container"></div>

      <!-- Loading indicator -->
      <div v-if="loading" class="d-flex justify-content-center align-items-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <span class="ms-3">Loading commission data...</span>
      </div>

      <!-- Error message -->
      <div v-if="error" class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ error }}
        <button @click="fetchCommissionData" class="btn btn-sm btn-outline-danger ms-3">
          <i class="fas fa-redo me-1"></i>Retry
        </button>
      </div>

      <div v-if="!loading && !error" id="financeForm" class="needs-validation" novalidate>
        <h5 class="mb-4">Commission Information</h5>

        <!-- University -->
        <div class="d-flex align-items-center mb-3" :data-field="'university'">
          <div class="icon-container me-3">
            <i class="far fa-building"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-1">University</h6>
            <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
              <span class="field-value-content">{{ commissionData.university || 'Not specified' }}</span>
              <span class="edit-icon ms-2">
                <i class="far fa-edit" @click="makeEditable('university')"></i>
              </span>
              <span class="save-indicator" :class="{ 'd-inline': savingFields.university }">
                <div v-if="savingFields.university" class="spinner-border spinner-border-sm me-1" role="status"></div>
                <i v-else class="fas fa-check"></i>
              </span>
            </span>
            <input
              type="text"
              v-model="editableData.university"
              class="form-control edit-field"
              style="display: none;"
              @blur="saveField('university')"
              @keydown.enter="saveField('university')"
              :disabled="savingFields.university"
            >
          </div>
        </div>

        <!-- Product -->
        <div class="d-flex align-items-center mb-3" :data-field="'product'">
          <div class="icon-container me-3">
            <i class="far fa-globe"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-1">Product</h6>
            <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
              <span class="field-value-content">{{ commissionData.product || 'Not specified' }}</span>
              <span class="edit-icon ms-2">
                <i class="far fa-edit" @click="makeEditable('product')"></i>
              </span>
              <span class="save-indicator" :class="{ 'd-inline': savingFields.product }">
                <div v-if="savingFields.product" class="spinner-border spinner-border-sm me-1" role="status"></div>
                <i v-else class="fas fa-check"></i>
              </span>
            </span>
            <input
              type="text"
              v-model="editableData.product"
              class="form-control edit-field"
              style="display: none;"
              @blur="saveField('product')"
              @keydown.enter="saveField('product')"
              :disabled="savingFields.product"
            >
          </div>
        </div>

        <!-- Partner -->
        <div class="d-flex align-items-center mb-3" :data-field="'partner'">
          <div class="icon-container me-3">
            <i class="far fa-handshake"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-1">Partner</h6>
            <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
              <span class="field-value-content">{{ commissionData.partner || 'Not specified' }}</span>
              <span class="edit-icon ms-2">
                <i class="far fa-edit" @click="makeEditable('partner')"></i>
              </span>
              <span class="save-indicator" :class="{ 'd-inline': savingFields.partner }">
                <div v-if="savingFields.partner" class="spinner-border spinner-border-sm me-1" role="status"></div>
                <i v-else class="fas fa-check"></i>
              </span>
            </span>
            <input
              type="text"
              v-model="editableData.partner"
              class="form-control edit-field"
              style="display: none;"
              @blur="saveField('partner')"
              @keydown.enter="saveField('partner')"
              :disabled="savingFields.partner"
            >
          </div>
        </div>

        <!-- Commission Section -->
        <div class="card mt-4">
          <div class="card-header">
            <h5 class="mb-0">Commission Types</h5>
          </div>
          <div class="card-body">
            <!-- Dynamic Commission Types -->
            <div
              v-for="(value, type) in commissionData.commissionTypes"
              :key="type"
              class="commission-section mb-3"
              :data-field="type"
              data-commission-type="true"
            >
              <div class="d-flex align-items-center">
                <div class="icon-container me-3">
                  <i :class="getCommissionIcon(type)" class="text-muted"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-0 fw-bold">{{ formatCommissionTypeName(type) }}</h6>
                  <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                    <span class="field-value-content">{{ value || 'Not specified' }}</span>
                    <span class="edit-icon ms-2">
                      <i class="far fa-edit" @click="makeEditable(type)"></i>
                    </span>
                    <span class="save-indicator" :class="{ 'd-inline': savingFields[type] }">
                      <div v-if="savingFields[type]" class="spinner-border spinner-border-sm me-1" role="status"></div>
                      <i v-else class="fas fa-check"></i>
                    </span>
                  </span>
                  <input
                    type="text"
                    v-model="editableData.commissionTypes[type]"
                    class="form-control edit-field"
                    style="display: none;"
                    @blur="saveField(type)"
                    @keydown.enter="saveField(type)"
                    :disabled="savingFields[type]"
                  >
                </div>
                <div class="ms-auto">
                  <button
                    @click="deleteCommissionType(type)"
                    class="btn btn-sm btn-outline-danger"
                    :disabled="savingFields[type]"
                    v-if="!isDefaultCommissionType(type)"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Add New Commission Type -->
            <div class="text-center mt-3">
              <button
                type="button"
                class="btn btn-outline-primary"
                @click="showAddCommissionModal"
                :disabled="addingCommissionType"
              >
                <div v-if="addingCommissionType" class="spinner-border spinner-border-sm me-2" role="status"></div>
                <i v-else class="far fa-plus me-2"></i>
                Add Commission Type
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Commission Type Modal -->
  <div class="modal fade" id="addCommissionModal" tabindex="-1" aria-labelledby="addCommissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCommissionModalLabel">Add New Commission Type</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="newCommissionType" class="form-label">Commission Type Name</label>
            <input
              type="text"
              class="form-control"
              id="newCommissionType"
              v-model="newCommissionType.name"
              placeholder="Enter commission type name"
            >
          </div>
          <div class="mb-3">
            <label for="newCommissionValue" class="form-label">Commission Value</label>
            <input
              type="text"
              class="form-control"
              id="newCommissionValue"
              v-model="newCommissionType.value"
              placeholder="Enter commission value (e.g., 5%, $100)"
            >
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button
            type="button"
            class="btn btn-primary"
            @click="addCommissionType"
            :disabled="!newCommissionType.name || addingCommissionType"
          >
            <div v-if="addingCommissionType" class="spinner-border spinner-border-sm me-2" role="status"></div>
            Add Commission Type
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CommissionRate',
  props: {
    commissionId: {
      type: [String, Number],
      default: null
    },
    apiBaseUrl: {
      type: String,
      default: '/api'
    }
  },
  data() {
    return {
      loading: true,
      error: null,
      commissionData: {
        id: null,
        university: '',
        product: '',
        partner: '',
        commissionTypes: {}
      },
      editableData: {
        university: '',
        product: '',
        partner: '',
        commissionTypes: {}
      },
      savingFields: {},
      addingCommissionType: false,
      newCommissionType: {
        name: '',
        value: ''
      },
      defaultCommissionTypes: ['Net', 'Gross', 'Standard', 'Bonus', 'Intensive']
    };
  },
  mounted() {
    this.fetchCommissionData();
  },
  methods: {
    getCsrfToken() {
      let token = document.head.querySelector('meta[name="csrf-token"]');
      return token ? token.content : '';
    },

    async fetchCommissionData() {
      try {
        this.loading = true;
        this.error = null;

        const url = this.commissionId
          ? `${this.apiBaseUrl}/commission/${this.commissionId}`
          : `${this.apiBaseUrl}/commissions`;

        const response = await fetch(url, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          }
        });

        if (!response.ok) {
          throw new Error(`Failed to fetch commission data: ${response.status} ${response.statusText}`);
        }

        const responseData = await response.json();
        let data;
        if (this.commissionId) {
          data = responseData.success ? responseData.data : responseData;
        } else {
          const commissions = Array.isArray(responseData) ? responseData : (responseData.data && Array.isArray(responseData.data) ? responseData.data : []);
          if (commissions.length === 0) {
            throw new Error('No commission data found');
          }
          data = commissions[0];
        }

        if (!data) {
          throw new Error('Invalid commission data received');
        }

        this.commissionData = {
          id: data.id,
          university: data.university || '',
          product: data.product || '',
          partner: data.partner || '',
          commissionTypes: this.parseCommissionTypes(data)
        };

        this.editableData = {
          university: this.commissionData.university,
          product: this.commissionData.product,
          partner: this.commissionData.partner,
          commissionTypes: { ...this.commissionData.commissionTypes }
        };

      } catch (error) {
        console.error('Error fetching commission data:', error);
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },

    async saveField(field) {
      try {
        this.savingFields[field] = true;

        let updateData = {};

        if (['university', 'product', 'partner'].includes(field)) {
          updateData[field] = this.editableData[field];
          this.commissionData[field] = this.editableData[field];
        } else {
          updateData.commissionTypes = {
            ...this.commissionData.commissionTypes,
            [field]: this.editableData.commissionTypes[field]
          };
          this.commissionData.commissionTypes[field] = this.editableData.commissionTypes[field];
        }

        const response = await fetch(`${this.apiBaseUrl}/commission/${this.commissionData.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          },
          body: JSON.stringify(updateData)
        });

        if (!response.ok) {
          throw new Error(`Failed to save ${field}: ${response.status} ${response.statusText}`);
        }

        this.hideEditField(field);
        this.showToast(`${this.formatFieldName(field)} updated successfully`, 'success');

      } catch (error) {
        console.error(`Error saving ${field}:`, error);
        this.showToast(`Failed to save ${this.formatFieldName(field)}: ${error.message}`, 'error');

        if (['university', 'product', 'partner'].includes(field)) {
          this.editableData[field] = this.commissionData[field];
        } else {
          this.editableData.commissionTypes[field] = this.commissionData.commissionTypes[field];
        }
      } finally {
        this.savingFields[field] = false;
      }
    },

    async deleteCommissionType(type) {
      if (!confirm(`Are you sure you want to delete the "${type}" commission type?`)) {
        return;
      }

      try {
        this.savingFields[type] = true;

        const response = await fetch(`${this.apiBaseUrl}/commission/${this.commissionData.id}/commission-type/${type}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          }
        });

        if (!response.ok) {
          throw new Error(`Failed to delete commission type: ${response.status} ${response.statusText}`);
        }

        delete this.commissionData.commissionTypes[type];
        delete this.editableData.commissionTypes[type];

        this.showToast(`Commission type "${type}" deleted successfully`, 'success');

      } catch (error) {
        console.error('Error deleting commission type:', error);
        this.showToast(`Failed to delete commission type: ${error.message}`, 'error');
      } finally {
        this.savingFields[type] = false;
      }
    },

    showAddCommissionModal() {
      this.newCommissionType = { name: '', value: '' };
      const modal = new bootstrap.Modal(document.getElementById('addCommissionModal'));
      modal.show();
    },

    async addCommissionType() {
      if (!this.newCommissionType.name.trim()) {
        this.showToast('Please enter a commission type name', 'error');
        return;
      }

      try {
        this.addingCommissionType = true;

        const response = await fetch(`${this.apiBaseUrl}/commission/${this.commissionData.id}/commission-type`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          },
          body: JSON.stringify({
            type: this.newCommissionType.name,
            value: this.newCommissionType.value
          })
        });

        if (!response.ok) {
          throw new Error(`Failed to add commission type: ${response.status} ${response.statusText}`);
        }

        this.commissionData.commissionTypes[this.newCommissionType.name] = this.newCommissionType.value;
        this.editableData.commissionTypes[this.newCommissionType.name] = this.newCommissionType.value;

        const modal = bootstrap.Modal.getInstance(document.getElementById('addCommissionModal'));
        modal.hide();

        this.showToast(`Commission type "${this.newCommissionType.name}" added successfully`, 'success');

      } catch (error) {
        console.error('Error adding commission type:', error);
        this.showToast(`Failed to add commission type: ${error.message}`, 'error');
      } finally {
        this.addingCommissionType = false;
      }
    },

    getCommissionIcon(type) {
      const icons = {
        'Net': 'far fa-chart-line',
        'Gross': 'far fa-money-bill-alt',
        'Standard': 'far fa-star',
        'Bonus': 'far fa-gift',
        'Intensive': 'far fa-bolt',
        'Progressive': 'far fa-chart-bar'
      };
      return icons[type] || 'far fa-percentage';
    },

    isDefaultCommissionType(type) {
      return this.defaultCommissionTypes.includes(type);
    },

    formatCommissionTypeName(type) {
      return type.charAt(0).toUpperCase() + type.slice(1);
    },

    formatFieldName(field) {
      return field.charAt(0).toUpperCase() + field.slice(1).replace(/([A-Z])/g, ' $1');
    },

    showToast(message, type = 'success') {
      const toastContainer = document.querySelector('.toast-container');

      // Clear any existing toasts
      const existingToasts = toastContainer.querySelectorAll('.toast');
      existingToasts.forEach(toast => {
        toast.remove();
      });

      const toastId = `toast-${Date.now()}`;

      const toastHtml = `
        <div id="${toastId}" class="toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="toast-header">
            <i class="fas ${type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger'} me-2"></i>
            <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">
            ${message}
          </div>
        </div>
      `;

      toastContainer.insertAdjacentHTML('beforeend', toastHtml);

      const toastElement = document.getElementById(toastId);
      const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
      toast.show();

      toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
      });
    },

    parseCommissionTypes(data) {
      const commissionTypes = {};

      if (data.commission_types && typeof data.commission_types === 'object') {
        Object.assign(commissionTypes, data.commission_types);
      }

      if (data.bonus_commission) {
        commissionTypes['Bonus'] = data.bonus_commission;
      }
      if (data.intensive_commission) {
        commissionTypes['Intensive'] = data.intensive_commission;
      }
      if (data.progressive_commission) {
        commissionTypes['Progressive'] = data.progressive_commission;
      }

      if (data.has_bonus_commission && data.bonus_commission) {
        commissionTypes['Bonus'] = data.bonus_commission;
      }
      if (data.has_intensive_commission && data.intensive_commission) {
        commissionTypes['Intensive'] = data.intensive_commission;
      }
      if (data.has_progressive_commission && data.progressive_commission) {
        commissionTypes['Progressive'] = data.progressive_commission;
      }

      return commissionTypes;
    },

    makeEditable(field) {
      if (['university', 'product', 'partner'].includes(field)) {
        this.editableData[field] = this.commissionData[field];
      } else {
        if (!this.editableData.commissionTypes[field]) {
          this.editableData.commissionTypes[field] = this.commissionData.commissionTypes[field] || '';
        }
      }

      this.showEditField(field);
    },

    showEditField(field) {
      this.$nextTick(() => {
        const fieldContainer = document.querySelector(`[data-field="${field}"]`);
        if (fieldContainer) {
          const editField = fieldContainer.querySelector('.edit-field');
          const displayField = fieldContainer.querySelector('.field-value');

          if (editField && displayField) {
            editField.style.display = 'block';
            displayField.style.display = 'none';
            editField.focus();
          }
        }
      });
    },

    hideEditField(field) {
      this.$nextTick(() => {
        const fieldContainer = document.querySelector(`[data-field="${field}"]`);
        if (fieldContainer) {
          const editField = fieldContainer.querySelector('.edit-field');
          const displayField = fieldContainer.querySelector('.field-value');

          if (editField && displayField) {
            editField.style.display = 'none';
            displayField.style.display = 'flex';
          }
        }
      });
    }
  }
};
</script>

<style>
/* Ensure your CSS is specific enough to override any default styles */
.edit-icon {
  cursor: pointer;
  color: #6c757d;
  transition: color 0.3s ease;
  margin-left: 10px;
}

.edit-icon:hover {
  color: #007bff !important; /* Use !important sparingly */
}

.commission-section {
  border-bottom: 1px solid #f1f1f1;
  padding-bottom: 15px;
}

.commission-section:last-child {
  border-bottom: none;
}

.edit-field {
  display: none;
  margin-bottom: 10px;
}

.save-indicator {
  display: none;
  color: #28a745;
  font-size: 0.85rem;
  margin-left: 10px;
}

.save-indicator.d-inline {
  display: inline-flex !important;
  align-items: center;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
  border-width: 0.2em;
}

.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1051;
}

.toast {
  min-width: 300px;
  background-color: white;
  border-left: 4px solid #28a745;
  box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
}

.toast-success {
  border-left-color: #28a745;
}

.toast-error {
  border-left-color: #dc3545;
}

.icon-container {
  width: 35px;
  height: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background-color: transparent;
  color: #6c757d;
}

.icon-container i {
  font-size: 1.2rem;
}

.icon-container:hover {
  color: #007bff;
}

.field-value {
  transition: all 0.2s ease;
}

.field-value:hover {
  background-color: #f8f9fa !important;
  border-radius: 4px;
}

.modal-content {
  border: none;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
  border-bottom: 1px solid #e9ecef;
  background-color: #f8f9fa;
}

.btn-outline-danger:hover {
  color: white;
  background-color: #dc3545;
  border-color: #dc3545;
}

.flex-grow-1 {
  flex-grow: 1;
}
</style>
