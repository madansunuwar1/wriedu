<template>
    <div class="modal fade" :class="{ 'show': log }" :style="{ display: log ? 'block' : 'none' }" tabindex="-1" @click.self="closeModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div v-if="log" class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <h5 class="modal-title fw-bold">Activity Details</h5>
                    <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
                </div>
                <div class="modal-body p-4">
                     <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6"><strong>Performed By:</strong> {{ log.causer?.name || 'System' }}</div>
                            <div class="col-md-6"><strong>Action:</strong> {{ log.description }}</div>
                            <div class="col-md-6"><strong>Subject:</strong> {{ log.subject_type.split('\\').pop() }} #{{ log.subject_id }}</div>
                            <div class="col-md-6"><strong>Timestamp:</strong> {{ new Date(log.created_at).toLocaleString() }}</div>
                        </div>
                    </div>

                    <h6 class="fw-bold">Properties</h6>
                    <div v-if="log.properties && log.properties.attributes" class="table-responsive" style="max-height: 400px;">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Property</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(value, key) in log.properties.attributes" :key="key">
                                    <td class="fw-medium" style="width: 30%;">{{ key }}</td>
                                    <td>
                                        <pre class="mb-0 bg-light p-2 rounded" style="white-space: pre-wrap;"><code>{{ formatValue(value) }}</code></pre>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                     <div v-else class="text-muted">No properties recorded.</div>
                </div>
                 <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-primary" @click="closeModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div v-if="log" class="modal-backdrop fade show"></div>
</template>

<script setup>
const props = defineProps({
    log: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close']);

const closeModal = () => {
    emit('close');
};

const formatValue = (value) => {
    if (typeof value === 'object' && value !== null) {
        return JSON.stringify(value, null, 2);
    }
    return value;
};
</script>

<style scoped>
.modal.show {
    display: block;
    background-color: rgba(0, 0, 0, 0.5);
}
.modal-content {
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
code {
  font-size: 0.85rem;
}
</style>