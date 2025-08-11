<template>
  <div class="card shadow">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <button @click="navigateMonth(-1)" class="btn btn-sm btn-outline-secondary">< Prev</button>
      <h5 class="mb-0 fw-bold">{{ calendarState.current_month }}</h5>
      <button @click="navigateMonth(1)" class="btn btn-sm btn-outline-secondary">Next ></button>
    </div>
    <div v-if="isLoading" class="card-body text-center p-5">
      <div class="spinner-border spinner-border-sm" role="status"></div>
    </div>
    <div v-else class="card-body p-2">
      <table class="table table-bordered calendar-table">
        <thead>
          <tr>
            <th v-for="day in calendarState.day_names" :key="day">{{ day }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(week, weekIndex) in calendarState.calendar" :key="weekIndex">
            <td v-for="(day, dayIndex) in week" :key="dayIndex" :class="{ 'today': day && day.is_today, 'disabled': !day }">
              <div v-if="day" class="day-content">
                <div class="date-number">{{ day.nepali_date }}</div>
                <div class="events-container">
                  <div v-for="event in day.events" :key="event.id" class="event-item" :style="{ backgroundColor: event.color || '#4e73df' }">
                    {{ event.title }}
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  initialData: {
    type: Object,
    required: true,
  },
});

const isLoading = ref(false);
const calendarState = ref({ ...props.initialData });
let currentYear = ref(props.initialData.current_year);
let currentMonth = ref(props.initialData.month_number);

// Watch for changes in the initial data prop and update the local state
watch(() => props.initialData, (newData) => {
  calendarState.value = { ...newData };
  currentYear.value = newData.current_year;
  currentMonth.value = newData.month_number;
}, { deep: true });

const navigateMonth = async (direction) => {
  isLoading.value = true;
  let newMonth = currentMonth.value + direction;
  let newYear = currentYear.value;

  if (newMonth > 12) {
    newMonth = 1;
    newYear++;
  } else if (newMonth < 1) {
    newMonth = 12;
    newYear--;
  }

  currentMonth.value = newMonth;
  currentYear.value = newYear;

  try {
    const response = await axios.get('/api/dashboard/calendar-month', {
      params: { year: newYear, month: newMonth }
    });
    calendarState.value = response.data;
  } catch (error) {
    console.error("Failed to load calendar month data:", error);
    // Handle error display if needed
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.calendar-table th { text-align: center; font-size: 0.8rem; padding: 0.5rem; }
.calendar-table td { height: 110px; vertical-align: top; padding: 4px; }
.calendar-table td.disabled { background-color: #f8f9fa; }
.calendar-table td.today .date-number { background-color: #4e73df; color: white; border-radius: 50%; font-weight: bold; }
.day-content { display: flex; flex-direction: column; height: 100%; }
.date-number { font-size: 0.8rem; text-align: right; width: 22px; height: 22px; line-height: 22px; margin-left: auto; }
.events-container { flex-grow: 1; overflow-y: auto; }
.event-item { font-size: 0.7rem; color: white; padding: 2px 4px; border-radius: 3px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>