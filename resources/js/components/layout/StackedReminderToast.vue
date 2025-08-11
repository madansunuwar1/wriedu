<template>
  <div
    ref="card"
    class="reminder-card d-flex align-items-center"
    :style="cardStyle"
    @mousedown="dragStart"
    @touchstart.prevent="dragStart"
  >
    <!-- Icon -->
    <span class="flex-shrink-0 notification-icon ms-3">
      <i class="fas fa-business-time fs-5"></i>
    </span>

    <!-- Content -->
    <div class="w-100 ps-3 pe-2 py-2 d-flex flex-column">
      <!-- Top Row: Title and Close Button -->
      <div class="d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fs-3 fw-normal text-truncate me-2">{{ reminder.lead_name }}</h5>
        <button
          type="button"
          class="btn-close flex-shrink-0"
          aria-label="Close"
          @mousedown.stop="dismiss"
          @touchstart.stop="dismiss"
        ></button>
      </div>

      <!-- Middle Row: Comment -->
      <p class="fs-2 d-block mt-1 mb-1 text-muted text-truncate">{{ reminder.comment }}</p>

      <!-- Bottom Row: Date and Complete Button -->
      <div class="d-flex align-items-center justify-content-between mt-1">
        <span class="fs-2 d-block text-muted">{{ new Date(reminder.date_time).toLocaleString() }}</span>
        <button
          type="button"
          class="btn btn-sm btn-success-subtle text-success py-1 px-2"
          @mousedown.stop="completeReminder"
          @touchstart.stop="completeReminder"
        >
          <i class="fas fa-check me-1"></i>
          Complete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue';

const props = defineProps({
  reminder: { type: Object, required: true },
  index: { type: Number, required: true },
});

// *** MODIFICATION: Added 'complete' to the list of emitted events ***
const emit = defineEmits(['dismiss', 'complete']);

const card = ref(null);

const isDragging = ref(false);
const startX = ref(0);
const currentX = ref(0);

const cardStyle = computed(() => {
  if (isDragging.value) {
    const deltaX = currentX.value - startX.value;
    const rotation = deltaX / 20;
    return {
      transform: `translateX(${deltaX}px) rotate(${rotation}deg)`,
      cursor: 'grabbing',
      transition: 'none',
    };
  }
  if (props.index > 0 && props.index < 3) {
    const scale = 1 - props.index * 0.05;
    const translateY = props.index * 12;
    return {
      transform: `scale(${scale}) translateY(${translateY}px)`,
      zIndex: 100 - props.index,
      opacity: 1,
    };
  }
  if (props.index >= 3) {
    return {
      transform: `scale(0.85) translateY(36px)`,
      opacity: 0,
      zIndex: 100 - props.index,
    };
  }
  return {
    transform: 'scale(1) translateY(0)',
    zIndex: 100,
    opacity: 1,
  };
});

function dragStart(event) {
  if (props.index !== 0) return;
  isDragging.value = true;
  startX.value = event.type === 'touchstart' ? event.touches[0].clientX : event.clientX;
  currentX.value = startX.value;
  window.addEventListener('mousemove', dragMove);
  window.addEventListener('mouseup', dragEnd);
  window.addEventListener('touchmove', dragMove);
  window.addEventListener('touchend', dragEnd);
}

function dragMove(event) {
  if (!isDragging.value) return;
  event.preventDefault();
  currentX.value = event.type === 'touchmove' ? event.touches[0].clientX : event.clientX;
}

function dragEnd() {
  if (!isDragging.value) return;
  isDragging.value = false;
  const deltaX = currentX.value - startX.value;
  const threshold = card.value.clientWidth / 3;
  if (Math.abs(deltaX) > threshold) {
    dismiss();
  }
  window.removeEventListener('mousemove', dragMove);
  window.removeEventListener('mouseup', dragEnd);
  window.removeEventListener('touchmove', dragMove);
  window.removeEventListener('touchend', dragEnd);
}

// Emits the 'dismiss' event to the parent
function dismiss() {
  emit('dismiss', props.reminder.id);
}

// *** NEW FUNCTION: Emits the 'complete' event to the parent ***
function completeReminder() {
  emit('complete', props.reminder.id);
}

onUnmounted(() => {
  window.removeEventListener('mousemove', dragMove);
  window.removeEventListener('mouseup', dragEnd);
  window.removeEventListener('touchmove', dragMove);
  window.removeEventListener('touchend', dragEnd);
});
</script>

<style scoped>
.reminder-card {
  position: absolute; /* Crucial for stacking */
  width: 350px;
  max-width: 90vw;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  background: var(--bs-body-bg);
  border: 1px solid var(--bs-border-color-translucent);
  cursor: grab;
  user-select: none; /* Prevent text selection while dragging */
  transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
  overflow: hidden; /* Ensures content doesn't spill out */
}

.reminder-card:active {
  cursor: grabbing;
}

.notification-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

html[data-bs-theme="light"] .notification-icon {
  background-color: #e9ecef;
  color: #6c757d;
}
html[data-bs-theme="dark"] .notification-icon {
  background-color: #343a40;
  color: #adb5bd;
}

.btn-close, .btn {
  --bs-btn-close-focus-shadow: none;
  box-shadow: none !important;
}

/* Make the complete button subtle */
.btn-success-subtle {
    font-weight: 500;
}
</style>