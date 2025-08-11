<template>
  <div class="body-wrapper">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <!-- New Integrated Date Converter Dropdown (Left Side) -->
          <div class="dropdown">
            <button
              class="btn btn-outline-primary"
              type="button"
              @click="toggleConverterDropdown"
              :class="{ 'show': converterDropdownOpen }"
            >
              <i class="fas fa-exchange-alt me-2"></i>Date Converter
            </button>
            <div
              class="dropdown-menu p-3 date-converter-dropdown"
              :class="{ 'show': converterDropdownOpen }"
            >
              <form @submit.prevent class="p-3">
                <div class="btn-group w-100 mb-3" role="group">
                  <input
                    type="radio"
                    class="btn-check"
                    name="conversionType"
                    id="ad_to_bs"
                    value="ad_to_bs"
                    v-model="conversionType"
                  >
                  <label class="btn btn-outline-primary" for="ad_to_bs">AD to BS</label>

                  <input
                    type="radio"
                    class="btn-check"
                    name="conversionType"
                    id="bs_to_ad"
                    value="bs_to_ad"
                    v-model="conversionType"
                  >
                  <label class="btn btn-outline-primary" for="bs_to_ad">BS to AD</label>
                </div>

                <div id="dateInputSection">
                  <div
                    id="adDateInput"
                    class="date-input-section"
                    v-show="conversionType === 'ad_to_bs'"
                  >
                    <label class="form-label fw-bold small">
                      <i class="fas fa-calendar-alt me-1"></i>Gregorian Date (AD)
                    </label>
                    <div class="input-group input-group-sm">
                      <select v-model="adDate.year" class="form-select">
                        <option value="" disabled>Year</option>
                        <option
                          v-for="year in adYears"
                          :key="year"
                          :value="year"
                          :selected="year === currentAdYear"
                        >
                          {{ year }}
                        </option>
                      </select>
                      <select v-model="adDate.month" class="form-select">
                        <option value="" disabled>Month</option>
                        <option
                          v-for="(name, num) in adMonths"
                          :key="num"
                          :value="num"
                          :selected="num === currentAdMonth"
                        >
                          {{ name }}
                        </option>
                      </select>
                      <select v-model="adDate.day" class="form-select">
                        <option value="" disabled>Day</option>
                        <option
                          v-for="day in 31"
                          :key="day"
                          :value="day"
                          :selected="day === currentAdDay"
                        >
                          {{ day }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <div
                    id="bsDateInput"
                    class="date-input-section"
                    v-show="conversionType === 'bs_to_ad'"
                  >
                    <label class="form-label fw-bold small">
                      <i class="fas fa-calendar me-1"></i>Nepali Date (BS)
                    </label>
                    <div class="input-group input-group-sm">
                      <select v-model="bsDate.year" class="form-select">
                        <option value="" selected disabled>Year</option>
                        <option v-for="year in bsYears" :key="year" :value="year">
                          {{ year }}
                        </option>
                      </select>
                      <select v-model="bsDate.month" class="form-select">
                        <option value="" selected disabled>Month</option>
                        <option
                          v-for="(name, num) in bsMonths"
                          :key="num"
                          :value="num"
                        >
                          {{ name }}
                        </option>
                      </select>
                      <select v-model="bsDate.day" class="form-select">
                        <option value="" selected disabled>Day</option>
                        <option v-for="day in 32" :key="day" :value="day">
                          {{ day }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="d-grid mt-3">
                  <button
                    type="button"
                    class="btn btn-primary"
                    @click="convertDate"
                  >
                    <i class="fas fa-sync-alt me-1"></i> Convert
                  </button>
                </div>

                <div class="mt-3" v-html="conversionResult"></div>

                <hr>
              </form>
            </div>
          </div>

          <!-- Calendar Title (Center) -->
          <h4 class="card-title mb-0 text-center calendar-title">Calendar</h4>

          <!-- Calendar Navigation (Right Side) -->
          <div class="calendar-nav ms-auto">
            <button class="btn btn-outline-primary btn-sm" @click="prevMonth">
              <i class="fas fa-chevron-left"></i>
            </button>
            <span class="mx-3 text-dark fw-bold fs-5">{{ convertToNepaliNumerals(calendarData.current_month) || 'Loading...' }}</span>
            <button class="btn btn-outline-primary btn-sm" @click="nextMonth">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>

        <div class="card-body">
          <div class="nepali-calendar">
            <div class="calendar-header">
              <div
                v-for="(dayName, index) in calendarData.day_names"
                :key="index"
                class="day-header"
                :class="{ 'saturday': index === 6 }"
              >
                {{ dayName }}
              </div>
            </div>
            <div class="calendar-grid">
              <div
                v-for="(week, weekIndex) in calendarData.calendar"
                :key="weekIndex"
                class="calendar-week"
              >
                <div
                  v-for="(day, dayIndex) in week"
                  :key="dayIndex"
                  class="calendar-day"
                  :class="{
                    'today': day && day.is_today,
                    'holiday': day && dayIndex === 6, // 6 is Saturday
                    'empty': !day
                  }"
                  :data-date="day ? day.english_date : ''"
                  :data-nepali-date="day ? day.nepali_full_date : ''"
                >
                  <template v-if="day">
                    <div class="day-events">
                      <div
                        v-for="event in day.events"
                        :key="event.id"
                        class="event"
                        :class="`event-${event.color || 'primary'}`"
                        @click="showEventDetails(event)"
                        :data-event-id="event.id"
                      >
                        {{ event.title }}
                      </div>
                    </div>
                    <div class="day-number">
                      <span class="english-date">{{ getEnglishDate(day.english_date) }}</span>
                      <span class="nepali-date">{{ convertToNepaliNumerals(day.nepali_date) }}</span>
                    </div>
                  </template>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Event Modal -->
      <div
        class="modal fade"
        :class="{ 'show': eventModalOpen, 'd-block': eventModalOpen }"
        :style="{ display: eventModalOpen ? 'block' : 'none' }"
        tabindex="-1"
      >
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ selectedEvent.title || 'Event Details' }}</h5>
              <button
                type="button"
                class="btn-close"
                @click="closeEventModal"
              ></button>
            </div>
            <div class="modal-body">
              <div class="event-details">
                <h4 class="mb-4 text-primary">{{ selectedEvent.title || 'Event Details' }}</h4>

                <!-- Image Container -->
                <div class="image-container" v-if="selectedEvent.image_url">
                  <div
                    class="image-placeholder image-loading"
                    v-show="imageLoading"
                  >
                    <div class="spinner"></div>
                    <span>Loading image...</span>
                  </div>
                  <img
                    :src="selectedEvent.image_url"
                    alt="Event Image"
                    class="event-image"
                    :style="{ display: imageLoading ? 'none' : 'block' }"
                    @load="handleImageLoad"
                    @error="handleImageError"
                  />
                </div>
                <div class="image-container" v-else>
                  <div class="image-placeholder">
                    <i class="fas fa-image" style="font-size: 24px; margin-bottom: 10px;"></i>
                    <br>
                    <span>No image available</span>
                  </div>
                </div>

                <!-- Event Meta -->
                <div class="event-meta">
                  <div class="meta-row">
                    <strong><i class="fas fa-tag me-2"></i>Event Type:</strong>
                    <span :class="`badge bg-${selectedEvent.color || 'primary'}`">
                      {{ capitalizeFirst(selectedEvent.color || 'primary') }}
                    </span>
                  </div>
                  <div class="meta-row" v-if="selectedEvent.start_date">
                    <strong><i class="fas fa-calendar-plus me-2"></i>Start Date:</strong>
                    <span>{{ formatDate(selectedEvent.start_date) }}</span>
                  </div>
                  <div class="meta-row" v-if="selectedEvent.end_date">
                    <strong><i class="fas fa-calendar-minus me-2"></i>End Date:</strong>
                    <span>{{ formatDate(selectedEvent.end_date) }}</span>
                  </div>
                </div>

                <!-- Event Description -->
                <div class="event-description" v-if="selectedEvent.description">
                  <h6 class="mb-3"><i class="fas fa-align-left me-2"></i>Description:</h6>
                  <div v-html="selectedEvent.description.replace(/\n/g, '<br>')"></div>
                </div>
                <div class="event-description" v-else>
                  <p class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>No description available for this event.
                  </p>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeEventModal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal backdrop -->
      <div
        class="modal-backdrop fade"
        :class="{ 'show': eventModalOpen }"
        v-show="eventModalOpen"
        @click="closeEventModal"
      ></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NepaliCalendar',
  data() {
    return {
      // Calendar data
      calendarData: {
        calendar: [],
        current_month: '',
        current_year: 0,
        month_number: 0,
        day_names: ['आइतबार', 'सोमबार', 'मंगलबार', 'बुधबार', 'बिहीबार', 'शुक्रबार', 'शनिबार'],
        nepali_month_year: ''
      },
      currentYear: 0,
      currentMonth: 0,

      // Date converter
      converterDropdownOpen: false,
      conversionType: 'ad_to_bs',
      conversionResult: '',

      // AD date inputs
      adDate: {
        year: '',
        month: '',
        day: ''
      },

      // BS date inputs
      bsDate: {
        year: '',
        month: '',
        day: ''
      },

      // Event modal
      eventModalOpen: false,
      selectedEvent: {},
      imageLoading: true,

      // Constants
      adMonths: {
        1: 'Jan', 2: 'Feb', 3: 'Mar', 4: 'Apr', 5: 'May', 6: 'Jun',
        7: 'Jul', 8: 'Aug', 9: 'Sep', 10: 'Oct', 11: 'Nov', 12: 'Dec'
      },

      bsMonths: {
        1: 'Baishakh', 2: 'Jestha', 3: 'Ashadh', 4: 'Shrawan', 5: 'Bhadra', 6: 'Ashwin',
        7: 'Kartik', 8: 'Mangsir', 9: 'Paush', 10: 'Magh', 11: 'Falgun', 12: 'Chaitra'
      },

      // English to Nepali numeral mapping
      englishToNepali: {
        '0': '०',
        '1': '१',
        '2': '२',
        '3': '३',
        '4': '४',
        '5': '५',
        '6': '६',
        '7': '७',
        '8': '८',
        '9': '९'
      }
    }
  },

  computed: {
    currentAdYear() {
      return new Date().getFullYear();
    },

    currentAdMonth() {
      return new Date().getMonth() + 1;
    },

    currentAdDay() {
      return new Date().getDate();
    },

    currentBsYear() {
      const now = new Date();
      // This is an approximation, the actual BS year depends on the exact date.
      // The API will provide the accurate BS year.
      return (now.getMonth() >= 3 && now.getDate() >= 14) ? now.getFullYear() + 57 : now.getFullYear() + 56;
    },

    adYears() {
      const current = this.currentAdYear;
      const years = [];
      for (let i = current + 20; i >= current - 100; i--) {
        years.push(i);
      }
      return years;
    },

    bsYears() {
      const current = this.currentBsYear;
      const years = [];
      for (let i = current + 20; i >= current - 100; i--) {
        years.push(i);
      }
      return years;
    }
  },

  async mounted() {
    // Initialize current date values for the converter
    this.adDate.year = this.currentAdYear;
    this.adDate.month = this.currentAdMonth;
    this.adDate.day = this.currentAdDay;

    // Load initial calendar data
    await this.loadInitialCalendarData();

    // Setup click outside listener for dropdown
    document.addEventListener('click', this.handleClickOutside);
  },

  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside);
  },

  methods: {
    // Nepali numeral conversion methods
    convertToNepaliNumerals(text) {
      if (!text) return '';
      
      return text.toString().replace(/[0-9]/g, (digit) => {
        return this.englishToNepali[digit] || digit;
      });
    },

    convertFromNepaliNumerals(text) {
      if (!text) return '';
      
      const nepaliToEnglish = {
        '०': '0', '१': '1', '२': '2', '३': '3', '४': '4',
        '५': '5', '६': '6', '७': '7', '८': '8', '९': '9'
      };
      
      return text.toString().replace(/[०-९]/g, (digit) => {
        return nepaliToEnglish[digit] || digit;
      });
    },

    // Calendar methods
    async loadInitialCalendarData() {
      try {
        const response = await this.fetchCalendarData();
        this.calendarData = response;
        this.currentYear = response.current_year;
        this.currentMonth = response.month_number;
      } catch (error) {
        console.error('Error loading initial calendar data:', error);
        // Optionally, show an error message to the user
      }
    },

    async loadCalendarData() {
      try {
        const response = await this.fetchCalendarData(this.currentYear, this.currentMonth);
        this.calendarData = response;
      } catch (error) {
        console.error(`Error loading calendar data for ${this.currentYear}-${this.currentMonth}:`, error);
      }
    },

    async fetchCalendarData(year = null, month = null) {
      // The API URL prefix might need to be adjusted based on your environment
      // (e.g., http://localhost:8000/api)
      const baseUrl = '/api';
      const url = year && month ?
        `${baseUrl}/calendar/month-data?year=${year}&month=${month}` :
        `${baseUrl}/calendar`;

        const response = await fetch(url);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        return await response.json();
    },

    async prevMonth() {
      this.currentMonth--;
      if (this.currentMonth < 1) {
        this.currentMonth = 12;
        this.currentYear--;
      }
      await this.loadCalendarData();
    },

    async nextMonth() {
      this.currentMonth++;
      if (this.currentMonth > 12) {
        this.currentMonth = 1;
        this.currentYear++;
      }
      await this.loadCalendarData();
    },

    // Date converter methods
    toggleConverterDropdown() {
      this.converterDropdownOpen = !this.converterDropdownOpen;
    },

    handleClickOutside(event) {
      if (!this.$el.querySelector('.dropdown').contains(event.target)) {
        this.converterDropdownOpen = false;
      }
    },

    async convertDate() {
      this.conversionResult = ''; // Clear previous result
      if (this.conversionType === 'ad_to_bs') {
        await this.convertAdToBs();
      } else {
        await this.convertBsToAd();
      }
    },

    async convertAdToBs() {
      const { year, month, day } = this.adDate;
      if (!year || !month || !day) {
        this.showWarning('Please select the full Gregorian (AD) date.');
        return;
      }

      const adDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      this.showLoading('Converting AD to BS...');

      try {
        const response = await fetch('/api/convert/ad-to-bs', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          },
          body: JSON.stringify({ ad_date: adDate })
        });

        const data = await response.json();
        if (response.ok) {
          const nepaliDate = this.convertToNepaliNumerals(data.converted_date);
          this.showSuccess(`<strong>${nepaliDate} BS</strong>`);
        } else {
          this.showError(data.error || 'Conversion failed.');
        }
      } catch (error) {
        this.showError('An error occurred during the request.');
      }
    },

    async convertBsToAd() {
      const { year, month, day } = this.bsDate;
      if (!year || !month || !day) {
        this.showWarning('Please select the full Nepali (BS) date.');
        return;
      }

      const bsDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      this.showLoading('Converting BS to AD...');

      try {
        const response = await fetch('/api/convert/bs-to-ad', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          },
          body: JSON.stringify({ bs_date: bsDate })
        });

        const data = await response.json();
        if (response.ok) {
          this.showInfo(`<strong>${data.converted_date} AD</strong>`);
        } else {
          this.showError(data.error || 'Invalid BS date.');
        }
      } catch (error) {
        this.showError('An error occurred during the request.');
      }
    },

    // Event modal methods
    showEventDetails(event) {
      this.selectedEvent = event;
      this.imageLoading = !!event.image_url; // Only show loading if there's an image URL
      this.eventModalOpen = true;
      document.body.classList.add('modal-open');
    },

    closeEventModal() {
      this.eventModalOpen = false;
      this.selectedEvent = {};
      document.body.classList.remove('modal-open');
    },

    handleImageLoad() {
      this.imageLoading = false;
    },

    handleImageError() {
      this.imageLoading = false;
      // You can replace the image source or show an error message
      this.selectedEvent.image_url = null;
    },

    // Utility methods
    getEnglishDate(dateString) {
      if (!dateString) return '';
      // getDate() returns day of the month (1-31)
      return new Date(dateString).getDate();
    },

    formatDate(dateString) {
      if (!dateString) return 'Not set';
      try {
        const date = new Date(dateString);
        // Check if the date is valid
        if (isNaN(date.getTime())) return dateString; // Return original string if invalid
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      } catch (error) {
        return dateString; // Return original string on error
      }
    },

    capitalizeFirst(str) {
      if (!str) return '';
      return str.charAt(0).toUpperCase() + str.slice(1);
    },

    getCsrfToken() {
      const token = document.querySelector('meta[name="csrf-token"]');
      return token ? token.getAttribute('content') : '';
    },

    // Message functions
    showError(message) {
      this.conversionResult = `<div class="alert alert-danger alert-sm p-2 mt-2" role="alert">${message}</div>`;
    },
    showSuccess(message) {
      this.conversionResult = `<div class="alert alert-success alert-sm p-2 mt-2" role="alert">${message}</div>`;
    },
    showInfo(message) {
      this.conversionResult = `<div class="alert alert-info alert-sm p-2 mt-2" role="alert">${message}</div>`;
    },
    showWarning(message) {
      this.conversionResult = `<div class="alert alert-warning alert-sm p-2 mt-2" role="alert">${message}</div>`;
    },
    showLoading(message) {
      this.conversionResult = `<div class="alert alert-light alert-sm p-2 mt-2" role="alert">${message}</div>`;
    }
  }
}
</script>

<style scoped>
/* Add style for the converter dropdown width */
.date-converter-dropdown {
    min-width: 300px; /* Reduced for smaller screens */
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.nepali-calendar {
    background: white;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: linear-gradient(135deg, #2ecc71, #27ae60, #1e8449);
    border: 1px solid #dee2e6;
}

.day-header {
    padding: 8px 4px;  /* Reduced padding */
    text-align: center;
    font-weight: 600;
    border-right: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    font-size: 0.8rem; /* Reduced font size */
}

.calendar-day.holiday,
.calendar-day.holiday .nepali-date,
.calendar-day.holiday .english-date {
    color: #dc3545 !important;
}

.day-header.saturday {
    color: #dc3545;
    font-weight: bold;
}

.day-header:last-child {
    border-right: none;
}

.calendar-grid {
    border: 1px solid #dee2e6;
    border-top: none;
}

.calendar-week {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.calendar-day {
    min-height: 80px; /* Reduced height */
    border-right: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    padding: 4px;   /* Reduced padding */
    transition: background-color 0.2s;
    display: flex;
    flex-direction: column;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.calendar-day.today {
    background-color: #e3f2fd;
    border: 2px solid #2196f3;
}

.calendar-day.empty {
    background-color: #fafafa;
    cursor: default;
}

.calendar-day:last-child {
    border-right: none;
}

/*  Central calendar title */
.calendar-title {
  margin-left: auto;
  margin-right: auto;
}

.day-events {
    flex: 0 0 auto;
    max-height: 40px; /* Reduced height */
    overflow-y: auto;
    margin-bottom: 4px;   /* Reduced margin */
    order: 1;
}

.day-number {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    order: 2;
}

.nepali-date {
    font-size: 16px;  /* Reduced font size */
    font-weight: 600;
    color: #333;
    font-family: 'Noto Sans Devanagari', 'Preeti', 'Mukti', 'Kalimati', sans-serif;
    text-align: center;
}

.english-date {
    position: absolute;
    bottom: 2px;
    right: 2px;
    font-size: 10px;   /* Reduced font size */
    color: #666;
}

.event {
    font-size: 10px;  /* Reduced font size */
    padding: 2px 4px;
    margin: 1px 0;
    border-radius: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
    transition: all 0.2s ease;
}

.event:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.event-danger {
    color: #ff4757;
    background-color: rgba(255, 71, 87, 0.15);
    border: 1px solid rgba(255, 55, 66, 0.25);
    border-radius: 12px;
    padding: 0.5rem 1rem 0.5rem 1.5rem;
    font-weight: 500;
    position: relative;
}

.event-danger::before {
    content: "";
    position: absolute;
    left: 0px;
    top: 10%;
    width: 6px;
    height: 80%;
    background-color: #ff4757;
    border-radius: 999px;
}

.event-success {
    color: #2ed573;
    background-color: rgba(46, 213, 115, 0.15);
    border: 1px solid rgba(38, 209, 101, 0.25);
    border-radius: 12px;
    padding: 0.5rem 1rem 0.5rem 1.5rem;
    font-weight: 500;
    position: relative;
}

.event-success::before {
    content: "";
    position: absolute;
    left: 0px;
    top: 10%;
    width: 6px;
    height: 80%;
    background-color: #2ed573;
    border-radius: 999px;
}

.event-primary {
    color: #5352ed;
    background-color: rgba(83, 82, 237, 0.15);
    border: 1px solid rgba(71, 66, 220, 0.25);
    border-radius: 12px;
    padding: 0.5rem 1rem 0.5rem 1.5rem;
    font-weight: 500;
    position: relative;
}

.event-primary::before {
    content: "";
    position: absolute;
    left: 0px;
    top: 10%;
    width: 6px;
    height: 80%;
    background-color: #5352ed;
    border-radius: 999px;
}

.event-warning {
    color: #ffa502;
    background-color: rgba(255, 165, 2, 0.15);
    border: 1px solid rgba(255, 149, 0, 0.25);
    border-radius: 12px;
    padding: 0.5rem 1rem 0.5rem 1.5rem;
    font-weight: 500;
    position: relative;
}

.event-warning::before {
    content: "";
    position: absolute;
    left: 0px;
    top: 10%;
    width: 6px;
    height: 80%;
    background-color: #ffa502;
    border-radius: 999px;
}

.calendar-nav {
  display: flex;
  align-items: center;
}

.event-image {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  display: block;
}

.event-meta {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.event-meta .meta-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  flex-direction: column;  /*Stack on small screens*/
  align-items: flex-start; /*Align left*/
}

.event-meta .meta-row:last-child {
  margin-bottom: 0;
}

.event-description {
  background-color: #ffffff;
  padding: 20px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  line-height: 1.6;
  font-size: 16px;
}

.image-container {
  text-align: center;
  margin-bottom: 20px;
  position: relative;
}

.image-placeholder {
  width: 100%;
  min-height: 200px;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px dashed #dee2e6;
  border-radius: 8px;
  color: #6c757d;
  font-size: 14px;
  position: relative;
}

.image-loading {
  background-color: #e3f2fd;
  color: #1976d2;
  border-color: #bbdefb;
}

.image-error {
  background-color: #ffebee;
  color: #c62828;
  border-color: #ffcdd2;
}

.image-success {
  background-color: #e8f5e8;
  color: #2e7d32;
  border-color: #c8e6c9;
}

.spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #1976d2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 10px;
}
.body-wrapper {
    
    min-height: fit-content !important;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Media query for smaller screens */
@media (max-width: 768px) {
  .calendar-day {
    min-height: 60px; /* Further reduced height */
    padding: 2px; /* Further reduced padding */
  }

  .day-header {
    padding: 6px 2px; /* Further reduced padding */
    font-size: 0.7rem; /* Further reduced font size */
  }

  .nepali-date {
    font-size: 14px; /* Further reduced font size */
  }

  .day-events {
    max-height: 30px; /* Further reduced height */
    margin-bottom: 2px; /* Further reduced margin */
  }

  /* Adjust calendar title  */
  .card-header {
      flex-direction: column; /* Stack elements */
      align-items: center; /* Center horizontally */
  }

  .calendar-title {
      margin: 10px 0; /* Add vertical spacing */
  }

  .calendar-nav {
      margin-top: 10px; /* Add spacing */
  }

  .modal-dialog.modal-xl {
    margin: 10px;
    max-width: calc(100% - 20px);
  }

  .date-converter-dropdown {
      min-width: auto;  /*Allow dropdown to shrink */
      width: 90vw;   /*Take up most of the screen width */
  }

  .mx-10 {
      margin-right: auto !important;
      margin-left: auto !important;
  }

  /* Adjustments for event meta info in the modal */
  .event-meta .meta-row {
      flex-direction: column; /* Stack event meta on smaller screens */
      align-items: flex-start;
      margin-bottom: 10px; /* Add some spacing */
  }

  .event-meta .meta-row strong {
      margin-bottom: 5px; /* Spacing for the label */
  }
}
</style>