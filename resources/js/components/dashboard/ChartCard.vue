<template>
    <div class="card chart-card h-100">
        <div class="card-header py-3">
            <h6 class="card-title m-0">{{ title }}</h6>
        </div>
        <div class="card-body d-flex flex-column">
            <div v-if="isLoading" class="d-flex justify-content-center align-items-center flex-grow-1">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="flex-grow-1">
                <slot></slot> <!-- Chart div will be injected here -->
            </div>
            <small v-if="note" class="text-muted text-center mt-1 chart-note">{{ note }}</small>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    isLoading: Boolean,
    note: String,
});
</script>