<template>
    <div class="col-xl-2 col-md-4 mb-4 metric-card-col">
        <div class="card shadow h-100 metric-card" :class="cardColorClass">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">{{ title }}</div>
                        <div class="h5 mb-0 font-weight-bold">{{ value }}</div>
                    </div>
                    <div class="col-auto">
                        <i :class="icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    color: String,
});

const cardColorClass = computed(() => {
    if (['hot-lead', 'raw-lead'].includes(props.color)) {
        return `card-${props.color}`;
    }
    return `text-bg-${props.color}`;
});
</script>

<style scoped>
/* Scoped styles ensure they only apply to this component */
.text-xs {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.8);
}
.font-weight-bold {
    font-weight: 700 !important;
}
</style>