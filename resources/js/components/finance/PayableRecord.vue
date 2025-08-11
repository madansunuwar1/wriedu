<template>
  <div class="card border-0 shadow-sm">
    <!-- Card Header -->
    <div class="card-header bg-white border-bottom-0 py-3">
      <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-semibold text-secondary">
          <i class="ti ti-percent me-2"></i>
          Commission Details - {{ commissionPayable.university || 'Loading...' }}
        </h4>
        <router-link
          v-if="commissionPayable.id"
          :to="{ name: 'PayableRecord', params: { id: commissionPayable.id } }"
          class="btn btn-sm btn-primary"
        >
          <i class="ti ti-file-text me-1"></i> View Record
        </router-link>
      </div>
    </div>
    <div class="card-body">
      <!-- Loading Overlay for initial fetch -->
      <div v-if="isFetching" class="loading-overlay" style="display: flex;">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <div v-else id="financeForm">
        <!-- Alert for success messages -->
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ successMessage }}
          <button type="button" class="btn-close" @click="successMessage = ''"></button>
        </div>

        <!-- Main Commission Information Card -->
        <div class="card mb-4 border">
          <div class="card-header bg-light py-3">
            <h5 class="mb-0 fw-semibold text-secondary"><i class="ti ti-info-circle me-2"></i>Commission Information</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div v-for="field in mainInfoFields" :key="field.key" class="col-md-6 mb-3">
                <label class="form-label text-muted small mb-1">{{ field.label }}</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="ti text-muted" :class="field.icon"></i></span>
                  <div
                    v-if="!isEditing(field.key)"
                    class="editable-field form-control"
                    @click="makeEditable(field.key)"
                  >
                    <span class="field-value-content">{{ commissionPayable[field.key] || 'Not set' }}</span>
                    <span class="edit-icon ms-2"><i class="ti ti-edit text-muted"></i></span>
                    <span class="save-indicator ms-2" v-html="getSaveIndicatorHtml(field.key)"></span>
                  </div>
                  <input
                    v-else
                    :ref="el => (fieldRefs[field.key] = el)"
                    type="text"
                    class="form-control"
                    v-model="editableValues[field.key]"
                    @blur="confirmAndSave(field.key)"
                    @keydown.enter.prevent="confirmAndSave(field.key)"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Special Commissions -->
        <div class="card mb-4 border">
          <div class="card-header bg-light py-3">
            <h5 class="mb-0 fw-semibold text-secondary"><i class="ti ti-star me-2"></i>Special Commissions</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div v-for="spec in specialCommissions" :key="spec.key" class="col-md-6 mb-3">
                <div class="border p-3 rounded h-100">
                  <h6 class="mb-3 fw-semibold text-secondary"><i class="ti me-2" :class="spec.icon"></i>{{ spec.label }}</h6>
                  <div class="form-check form-switch mb-3">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      :id="`${spec.key}Toggle`"
                      :checked="commissionPayable[spec.toggleKey]"
                      @change="updateToggle(spec.key, $event.target.checked)"
                    />
                    <label class="form-check-label" :for="`${spec.key}Toggle`">Enable {{ spec.label }}</label>
                  </div>
                  <div v-if="commissionPayable[spec.toggleKey]">
                    <label class="form-label text-muted small mb-1">Commission Value</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="ti ti-percent text-muted"></i></span>
                      <div v-if="!isEditing(spec.key)" class="editable-field form-control" @click="makeEditable(spec.key)">
                        <span class="field-value-content">{{ commissionPayable[spec.key] || 'Not set' }}</span>
                        <span class="edit-icon ms-2"><i class="ti ti-edit text-muted"></i></span>
                        <span class="save-indicator ms-2" v-html="getSaveIndicatorHtml(spec.key)"></span>
                      </div>
                      <input
                        v-else
                        :ref="el => (fieldRefs[spec.key] = el)"
                        type="text"
                        class="form-control"
                        v-model="editableValues[spec.key]"
                        @blur="confirmAndSave(spec.key)"
                        @keydown.enter.prevent="confirmAndSave(spec.key)"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Commission Types -->
        <div class="card border">
          <div class="card-header bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0 fw-semibold text-secondary"><i class="ti ti-list-details me-2"></i>Commission Types</h5>
              <button type="button" class="btn btn-sm btn-outline-primary" @click="addCommissionType"><i class="ti ti-plus me-1"></i> Add Type</button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div v-for="type in commissionTypes" :key="type.key" class="col-md-4 mb-3">
                <div class="border p-3 rounded h-100">
                  <div class="d-flex align-items-center mb-2">
                    <div class="p-2 me-2"><i class="ti text-muted" :class="type.icon"></i></div>
                    <h6 class="mb-0 fw-semibold text-secondary">{{ type.label }}</h6>
                  </div>
                  <div
                    v-if="!isEditing('commission_types', type.key)"
                    class="editable-field form-control"
                    @click="makeEditable('commission_types', type.key)"
                  >
                    <span class="field-value-content">{{ getCommissionTypeValue(type.key) }}</span>
                    <span class="edit-icon ms-2"><i class="ti ti-edit text-muted"></i></span>
                    <span class="save-indicator ms-2" v-html="getSaveIndicatorHtml('commission_types', type.key)"></span>
                  </div>
                  <input
                    v-else
                    :ref="el => (fieldRefs[`commission_types-${type.key}`] = el)"
                    type="text"
                    class="form-control"
                    v-model="editableValues.commission_types[type.key]"
                    @blur="confirmAndSave('commission_types', type.key)"
                    @keydown.enter.prevent="confirmAndSave('commission_types', type.key)"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { nextTick } from 'vue';
import Swal from 'sweetalert2';

export default {
  name: 'CommissionDetails',
  props: {
    id: {
      type: [String, Number],
      required: true,
    },
  },
  data() {
    return {
      isFetching: false,
      commissionPayable: {
        id: null,
        university: '',
        product: '',
        partner: '',
        has_bonus_commission: false,
        bonus_commission: null,
        has_incentive_commission: false,
        incentive_commission: null,
        has_progressive_commission: false,
        progressive_commission: null,
        commission_types: {},
      },
      editableValues: {
        commission_types: {}
      },
      editingState: {
        field: null,
        subfield: null,
        status: 'idle',
        originalValue: null,
      },
      fieldRefs: {},
      successMessage: '',
      mainInfoFields: [
        { key: 'university', label: 'University', icon: 'ti-building-bank' },
        { key: 'product', label: 'Product', icon: 'ti-globe' },
        { key: 'partner', label: 'Partner', icon: 'ti-users' },
      ],
      specialCommissions: [
        { key: 'bonus_commission', toggleKey: 'has_bonus_commission', label: 'Bonus Commission', icon: 'ti-gift' },
        { key: 'incentive_commission', toggleKey: 'has_incentive_commission', label: 'Incentive Commission', icon: 'ti-bolt' },
        { key: 'progressive_commission', toggleKey: 'has_progressive_commission', label: 'Progressive Commission', icon: 'ti-chart-line' },
      ],
      commissionTypes: [
        { key: 'Net', label: 'Net Commission', icon: 'ti-chart-line' },
        { key: 'Gross', label: 'Gross Commission', icon: 'ti-currency-dollar' },
        { key: 'Standard', label: 'Standard Commission', icon: 'ti-star' },
      ],
    };
  },
  methods: {
    async fetchCommissionPayable() {
      this.isFetching = true;
      try {
        const response = await axios.get(`/api/commission-payable/${this.id}`);
        const data = response.data;
        if (typeof data.commission_types !== 'object' || data.commission_types === null) {
          data.commission_types = {};
        }
        this.commissionPayable = data;
      } catch (error) {
        console.error('Error fetching commission payable:', error);
        this.showAlert('Failed to load commission details.', 'error');
      } finally {
        this.isFetching = false;
      }
    },
    isEditing(field, subfield = null) {
      return this.editingState.field === field && this.editingState.subfield === subfield;
    },
    getSaveIndicatorHtml(field, subfield = null) {
      if (!this.isEditing(field, subfield)) return '';
      switch(this.editingState.status) {
        case 'saving':
          return '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Saving...';
        case 'success':
          return '<i class="ti ti-check me-1 text-success"></i>Saved';
        case 'error':
          return '<i class="ti ti-x me-1 text-danger"></i>Error';
        default:
          return '';
      }
    },
    async makeEditable(field, subfield = null) {
      if (this.editingState.status === 'saving') return;
      this.cancelEdit();
      let value;
      if (subfield) {
        value = this.commissionPayable[field]?.[subfield] || '';
        this.editableValues[field] = { ...this.editableValues[field], [subfield]: value };
      } else {
        value = this.commissionPayable[field] || '';
        this.editableValues[field] = value;
      }
      this.editingState = {
        field,
        subfield,
        status: 'editing',
        originalValue: value,
      };
      await nextTick();
      const refKey = subfield ? `${field}-${subfield}` : field;
      const inputEl = this.fieldRefs[refKey];
      if (inputEl) {
        inputEl.focus();
        inputEl.select();
      }
    },
    confirmAndSave(field, subfield = null) {
      if (!this.isEditing(field, subfield)) return;
      let newValue;
      if (subfield) {
        newValue = this.editableValues[field][subfield];
      } else {
        newValue = this.editableValues[field];
      }
      if (newValue === this.editingState.originalValue) {
        this.cancelEdit();
        return;
      }
      const fieldLabel = this.getFieldLabel(field, subfield);
      Swal.fire({
        title: 'Are you sure?',
        text: `Update ${fieldLabel} to "${newValue}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.saveField(field, subfield, newValue);
        } else {
          this.cancelEdit();
        }
      });
    },
    async saveField(field, subfield, value) {
      this.editingState.status = 'saving';
      let payload = {};
      if (subfield) {
        payload[field] = { ...this.commissionPayable[field], [subfield]: value };
      } else {
        payload[field] = value;
      }
      try {
        await axios.put(`/api/commission-payable/${this.id}`, payload);
        if (subfield) {
          this.commissionPayable[field][subfield] = value;
        } else {
          this.commissionPayable[field] = value;
        }
        this.editingState.status = 'success';
        this.successMessage = 'Update successful!';
        setTimeout(() => this.successMessage = '', 3000);
      } catch (error) {
        console.error('Error updating field:', error);
        this.editingState.status = 'error';
        const errorMessage = error.response?.data?.message || 'Update failed.';
        this.showAlert(errorMessage, 'error');
      } finally {
        setTimeout(() => this.cancelEdit(), 2000);
      }
    },
    cancelEdit() {
      this.editingState = { field: null, subfield: null, status: 'idle', originalValue: null };
    },
    async updateToggle(commissionKey, isEnabled) {
      const toggleKey = `has_${commissionKey}`;
      const originalState = this.commissionPayable[toggleKey];
      this.commissionPayable[toggleKey] = isEnabled;
      let payload = { [toggleKey]: isEnabled };
      if (!isEnabled) {
        payload[commissionKey] = null;
        this.commissionPayable[commissionKey] = null;
      }
      try {
        await axios.put(`/api/commission-payable/${this.id}`, payload);
        const label = this.getFieldLabel(commissionKey);
        this.successMessage = `${label} ${isEnabled ? 'enabled' : 'disabled'}.`;
        setTimeout(() => this.successMessage = '', 3000);
      } catch (error) {
        this.commissionPayable[toggleKey] = originalState;
        console.error('Error updating toggle:', error);
        this.showAlert('Failed to update setting.', 'error');
      }
    },
    getCommissionTypeValue(typeKey) {
      return this.commissionPayable.commission_types?.[typeKey] || 'Not specified';
    },
    getFieldLabel(field, subfield = null) {
      if (subfield) return subfield;
      const allFields = [...this.mainInfoFields, ...this.specialCommissions];
      return allFields.find(f => f.key === field)?.label || field;
    },
    addCommissionType() {
      alert('"Add Type" functionality is not yet implemented.');
    },
    showAlert(message, type = 'success') {
      Swal.fire({
        title: type === 'success' ? 'Success' : 'Error',
        text: message,
        icon: type,
        confirmButtonText: 'OK'
      });
    },
  },
  created() {
    this.fetchCommissionPayable();
  },
};
</script>

<style scoped>
/* Styles copied and adapted from the Blade template for consistency */
.editable-field {
  cursor: pointer;
  padding: 0.375rem 0.75rem;
  transition: all 0.2s;
  min-height: 38px;
  display: flex;
  align-items: center;
  background-color: #fff;
}
.editable-field:hover {
  background-color: rgba(13, 110, 253, 0.05);
  border-color: #86b7fe;
}
.edit-icon {
  opacity: 0;
  transition: opacity 0.2s;
  margin-left: auto;
}
.editable-field:hover .edit-icon {
  opacity: 1;
}
.save-indicator {
  font-size: 0.75rem;
  display: flex;
  align-items: center;
}
.save-indicator .spinner-border {
  width: 0.75rem;
  height: 0.75rem;
  border-width: 0.15em;
}
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  display: none;
}
.form-switch .form-check-input {
  width: 2.5em;
  height: 1.5em;
  cursor: pointer;
}
.input-group-text {
  min-width: 40px;
  justify-content: center;
  background-color: transparent;
}
.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}
</style>
