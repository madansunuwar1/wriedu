<!-- resources/js/components/layout/InfoCard.vue -->
<template>
  <div class="card mb-4">
    <div class="card-body p-4">
      <h5 class="mb-4">{{ title }}</h5>
      <div v-for="item in fields" :key="item.field" class="d-flex align-items-center mb-4">
        <!-- Icon -->
        <div :class="`text-icon-${item.icon}`" class="fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
          <i class="bi" :class="item.icon"></i>
        </div>
        
        <!-- Label and Value/Input -->
        <div class="ms-3 w-100">
          <h6 class="mb-1 text-muted">{{ item.label }}</h6>
          
          <!-- Display Mode (Not Editing) -->
          <div v-if="editingField !== item.field" @click="startEditing(item)" class="field-value">
            <span class="field-value-content fw-semibold">
              {{ displayValue(item) }}
              <i v-if="item.type !== 'static'" class="bi bi-pencil-square text-primary edit-icon ms-1"></i>
            </span>
          </div>

          <!-- Editing Mode -->
          <div v-else>
            <!-- Render Text Input -->
            <template v-if="!item.type || item.type === 'text'">
              <div class="d-flex align-items-center">
                <input
                  ref="editInput"
                  v-model="editableValue"
                  type="text"
                  class="form-control form-control-sm"
                  @keydown.enter="saveEdit(item.field)"
                  @keydown.esc="cancelEdit"
                />
                <!-- CORRECTED: Added @mousedown.prevent to buttons -->
                <button @mousedown.prevent @click="saveEdit(item.field)" class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i class="bi bi-check-lg"></i></button>
                <button @mousedown.prevent @click="cancelEdit" class="btn btn-sm btn-secondary ms-1 py-1 px-2" title="Cancel"><i class="bi bi-x-lg"></i></button>
              </div>
            </template>

            <!-- Render Select Dropdown -->
            <template v-if="item.type === 'select'">
              <div class="d-flex align-items-center">
                <select
                  ref="editInput"
                  v-model="editableValue"
                  class="form-select form-select-sm"
                  @change="saveEdit(item.field)"
                  @blur="cancelEdit"
                  @keydown.esc="cancelEdit"
                >
                  <option :value="null">-- Select {{ item.label }} --</option>
                  <option v-for="option in item.options" :key="option" :value="option">
                    {{ option }}
                  </option>
                </select>
                <button @mousedown.prevent @click="cancelEdit" class="btn btn-sm btn-secondary ms-1 py-1 px-2" title="Cancel"><i class="bi bi-x-lg"></i></button>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';

const props = defineProps({
  title: String,
  fields: Array,
});

const emit = defineEmits(['field-updated']);

const editingField = ref(null);
const editableValue = ref('');
const editInput = ref(null);

const displayValue = (item) => {
  if (item.value === null || item.value === undefined || item.value === '') {
    return 'Not set';
  }
  return item.value;
};

const startEditing = async (item) => {
  if (item.type === 'static' || editingField.value) {
    return;
  }
  
  editingField.value = item.field;
  editableValue.value = item.value;

  await nextTick();
  if (editInput.value) {
    const el = Array.isArray(editInput.value) ? editInput.value[0] : editInput.value;
    if (el) el.focus();
  }
};

const cancelEdit = () => {
  editingField.value = null;
};

const saveEdit = (field) => {
  const originalField = props.fields.find(f => f.field === field);
  if (originalField && editableValue.value === originalField.value) {
    cancelEdit();
    return;
  }
  
  emit('field-updated', field, editableValue.value);
  cancelEdit();
};
</script>

<style scoped>
.field-value {
  cursor: pointer;
  display: inline-block;
  padding: 2px 4px;
  border-radius: 4px;
  transition: background-color 0.2s ease;
}

.field-value:hover {
    background-color: #eef5ff;
}

.edit-icon {
  opacity: 0;
  transition: opacity 0.2s ease;
}

.field-value:hover .edit-icon {
  opacity: 1;
}

/* Icon colors from your original CSS - ensure icon names match what's passed in the prop */
.text-icon-bi-person { color: #0277bd; }
.text-icon-bi-telephone { color: #43A047; }
.text-icon-bi-geo-alt { color: #C62828; }
.text-icon-bi-envelope { color: #1E88E5; }
.text-icon-bi-bookmark-star { color: #8E24AA; }
.text-icon-bi-check-circle { color: #43A047; }
.text-icon-bi-calendar-check { color: #f57f17; }
.text-icon-bi-star-half { color: #D81B60; }
.text-icon-bi-file-earmark-check { color: #0277bd; }
.text-icon-bi-globe { color: #C62828; }
.text-icon-bi-building-fill { color: #3949AB; }
.text-icon-bi-book { color: #1E88E5; }
.text-icon-bi-calendar-event { color: #f57f17; }
.text-icon-bi-patch-check { color: #43A047; }
.text-icon-bi-stars { color: #D81B60; }
.text-icon-bi-translate { color: #8E24AA; }
.text-icon-bi-journal-text { color: #0277bd; }
.text-icon-bi-person-badge { color: #3949AB; }
</style>