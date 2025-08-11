<!-- components/layout/ReminderToast.vue -->
<template>
  <div
    class="kill-feed-toast"
    :class="{ 'is-active': isAnimating }"
    role="alert"
    aria-live="assertive"
    aria-atomic="true"
  >
    <div class="toast-content">
      <i class="fas fa-business-time icon"></i>
      <div class="text-content">
        <strong class="title">{{ reminder.lead_name }}</strong>
        <p class="message">{{ reminder.comment }}</p>
      </div>
    </div>
    <div class="progress-bar-wrapper">
      <div class="progress-bar"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  reminder: { type: Object, required: true },
  isActive: { type: Boolean, required: true },
  timeout: { type: Number, required: true }
});

const emit = defineEmits(['timeUp']);

const isAnimating = ref(false);
let internalTimer = null;
let animationFrameTimer = null; // Use a separate timer for the animation trigger

// A single, reusable function to start the entire process.
function start() {
  // Always clear previous timers to prevent conflicts.
  clearTimeout(internalTimer);
  clearTimeout(animationFrameTimer);
  isAnimating.value = false;

  // Start the logical timer immediately. This is what reports back to the parent.
  internalTimer = setTimeout(() => {
    emit('timeUp', props.reminder.id);
  }, props.timeout);

  // THE FOOLPROOF FIX:
  // Use a tiny setTimeout to trigger the VISUAL animation. This forces a browser
  // reflow, ensuring it sees the initial state before applying the animation class.
  // 50ms is a safe, small delay that is imperceptible to the user.
  animationFrameTimer = setTimeout(() => {
    isAnimating.value = true;
  }, 50);
}

function stop() {
  clearTimeout(internalTimer);
  clearTimeout(animationFrameTimer);
  isAnimating.value = false;
}

// 1. FOR THE VERY FIRST TOAST:
// `onMounted` runs after the element is in the DOM.
onMounted(() => {
  if (props.isActive) {
    start();
  }
});

// 2. FOR ALL SUBSEQUENT TOASTS:
// The watcher handles changes from inactive to active.
watch(
  () => props.isActive,
  (isNowActive, wasActive) => {
    // Only run when changing from false to true, after the initial mount.
    if (isNowActive && !wasActive) {
      start();
    } else if (!isNowActive) {
      stop();
    }
  }
);

// Final cleanup.
onUnmounted(() => {
  stop();
});
</script>

<style scoped>
.kill-feed-toast {
  background-color: rgba(20, 20, 30, 0.9);
  backdrop-filter: blur(8px);
  color: #f0f0f0;
  border-radius: 6px;
  padding: 12px 16px;
  width: 350px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
  border-left: 4px solid #4a90e2;
  position: relative;
  overflow: hidden;
}

.toast-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.icon {
  font-size: 1.5rem;
  color: #4a90e2;
}

.text-content {
  display: flex;
  flex-direction: column;
}

.title {
  font-weight: 600;
}

.message {
  font-size: 0.9rem;
  margin: 0;
  color: #ccc;
}

.progress-bar-wrapper {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: rgba(74, 144, 226, 0.2);
}

.progress-bar {
  height: 100%;
  background-color: #4a90e2;
  width: 0%;
  /* The transition is only applied when the `.is-active` class is added. */
  transition: width v-bind('timeout + "ms"') linear;
}

/* Base state has no transition. We remove this so the transition is always available. */
/* .progress-bar { transition: none; } */

.kill-feed-toast.is-active .progress-bar {
  width: 100%;
}
</style>