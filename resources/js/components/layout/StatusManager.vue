<!-- resources/js/components/StatusManager.vue -->
<template>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Update Lead Status</h5>
            <div class="d-flex align-items-end gap-3 mb-4">
                <div class="flex-grow-1">
                    <label for="document_status" class="form-label">Status</label>
                    <select id="document_status" v-model="selectedStatus" class="form-select">
                        <option disabled value="">Select a status...</option>
                        <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                    </select>
                </div>
                <button @click="saveStatus" class="btn btn-primary" :disabled="selectedStatus === currentStatus">
                    Save Status
                </button>
            </div>

            <h6 class="mb-3">Status Progress</h6>
            <ul class="progress-bar-list">
                <li v-for="(status, index) in statuses" :key="status" :class="getStepClass(status, index)">
                    <a>
                        <span class="step">{{ index + 1 }}</span>
                        {{ status }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    statuses: Array,
    currentStatus: String,
});
const emit = defineEmits(['status-updated']);

const selectedStatus = ref(props.currentStatus);

watch(() => props.currentStatus, (newVal) => {
    selectedStatus.value = newVal;
});

const currentStatusIndex = computed(() => {
    return props.statuses.indexOf(props.currentStatus);
});

const getStepClass = (status, index) => {
    if (index < currentStatusIndex.value) {
        return 'completed';
    } else if (index === currentStatusIndex.value) {
        return 'current';
    }
    return 'disabled';
};

const saveStatus = async () => {
    const { isConfirmed } = await Swal.fire({
        title: 'Confirm Status Change',
        text: `Change status to "${selectedStatus.value}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!'
    });

    if (isConfirmed) {
        emit('status-updated', selectedStatus.value);
    } else {
        selectedStatus.value = props.currentStatus; // Revert on cancel
    }
};
</script>

<style scoped>
/* Copy of your #progressBar styles, converted to scoped CSS */
.progress-bar-list { list-style: none; padding: 0; display: flex; position: relative; }
.progress-bar-list li { flex: 1; text-align: center; position: relative; }
.progress-bar-list li a { text-decoration: none; display: block; padding: 10px; }
.progress-bar-list li .step { display: block; font-size: 14px; margin-bottom: 5px; }

.progress-bar-list li.disabled a { color: #999; }
.progress-bar-list li.current a { color: #2196F3; font-weight: bold; }
.progress-bar-list li.completed a { color: #28a733; }

.progress-bar-list li:not(:last-child)::after { content: ''; position: absolute; top: 50%; left: 50%; width: 100%; height: 2px; background-color: #ECECEC; z-index: -1; }
.progress-bar-list li.completed::after { background-color: #28a733; }
.progress-bar-list li.current::after { background-image: linear-gradient(to right, #28a733 50%, #ECECEC 50%); }
</style>