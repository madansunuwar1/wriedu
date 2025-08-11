<!-- resources/js/components/TomSelect.vue -->
<template>
  <select ref="selectElement"><slot/></select>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

const props = defineProps({
  modelValue: [String, Array, Number],
  options: { type: Array, default: () => [] },
  settings: { type: Object, default: () => ({}) },
  placeholder: { type: String, default: 'Select...' }
});

const emit = defineEmits(['update:modelValue']);

const selectElement = ref(null);
let tomSelectInstance = null;

const initializeTomSelect = () => {
  if (tomSelectInstance) {
    tomSelectInstance.destroy();
  }

  const combinedSettings = {
    ...props.settings,
    placeholder: props.placeholder,
    options: props.options,
    items: Array.isArray(props.modelValue) ? props.modelValue : [props.modelValue],
    onChange: (value) => {
      emit('update:modelValue', value);
    },
  };

  tomSelectInstance = new TomSelect(selectElement.value, combinedSettings);
};

onMounted(() => {
  initializeTomSelect();
});

onUnmounted(() => {
  if (tomSelectInstance) {
    tomSelectInstance.destroy();
  }
});

// Watch for changes in props to update Tom Select instance
watch(() => props.modelValue, (newValue) => {
    if (tomSelectInstance && JSON.stringify(tomSelectInstance.items) !== JSON.stringify(newValue)) {
         tomSelectInstance.setValue(newValue, true); // true to suppress onChange event
    }
});

watch(() => props.options, (newOptions) => {
    if (tomSelectInstance) {
        tomSelectInstance.sync(); // sync() is better for reactivity
        tomSelectInstance.clearOptions();
        tomSelectInstance.addOptions(newOptions);
        tomSelectInstance.refreshOptions(false);
        // Re-apply the value in case it got cleared
        tomSelectInstance.setValue(props.modelValue, true);
    }
}, { deep: true });
</script>