<template>
  <div class="card">
    <div class="border-bottom title-part-padding">
      <h4 class="card-title mb-0">Create Notice</h4>
    </div>
    <div class="card-body">
      <div v-if="errors.length" class="alert alert-danger">
        <ul>
          <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
        </ul>
      </div>
      <div v-if="successMessage" class="alert alert-success">
        {{ successMessage }}
      </div>
      <form @submit.prevent="submitForm" class="needs-validation" novalidate enctype="multipart/form-data">
        <div class="row mb-3">
          <div class="col-md-12">
            <label class="form-label" for="title">Notice Title</label>
            <input type="text" class="form-control" id="title" v-model="form.title" placeholder="Enter notice title" required>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a notice title.</div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-12">
            <label class="form-label" for="description">Notice Description</label>
            <textarea class="form-control" id="description" v-model="form.description" placeholder="Enter notice description here..." rows="5" required></textarea>
            <div class="invalid-feedback">Please enter the notice description.</div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-12">
            <label class="form-label" for="image">Notice Image</label>
            <div class="custom-file">
              <input type="file" class="form-control" id="image" @change="handleFileUpload" required>
              <div class="invalid-feedback">Please select a valid image file.</div>
              <small class="text-muted">Accepted formats: JPG, PNG, GIF (Max 2MB)</small>
            </div>
          </div>
        </div>
        <div class="card mt-4 mb-3">
          <div class="card-header">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="addToCalendar" v-model="form.add_to_calendar" checked>
              <label class="form-check-label" for="addToCalendar">
                <strong>Add to Calendar</strong>
              </label>
            </div>
          </div>
          <div class="card-body" id="calendarEventSection">
            <div class="row">
              <div class="col-md-12 mb-3">
                <label class="form-label">Event Priority/Color</label>
                <div class="d-flex gap-3 flex-wrap">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" id="eventDanger" v-model="form.event_color" value="danger">
                    <label class="form-check-label text-danger" for="eventDanger">
                      <i class="fas fa-circle me-1"></i>High Priority
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" id="eventWarning" v-model="form.event_color" value="warning">
                    <label class="form-check-label text-warning" for="eventWarning">
                      <i class="fas fa-circle me-1"></i>Medium Priority
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" id="eventPrimary" v-model="form.event_color" value="primary" checked>
                    <label class="form-check-label text-primary" for="eventPrimary">
                      <i class="fas fa-circle me-1"></i>Normal
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" id="eventSuccess" v-model="form.event_color" value="success">
                    <label class="form-check-label text-success" for="eventSuccess">
                      <i class="fas fa-circle me-1"></i>Completed
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label" for="event_start_date">Event Start Date</label>
                  <input type="date" class="form-control" id="event_start_date" v-model="form.event_start_date">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label" for="event_end_date">Event End Date</label>
                  <input type="date" class="form-control" id="event_end_date" v-model="form.event_end_date">
                  <small class="text-muted">Leave empty if it's a single day event</small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <label class="form-label" for="event_title">Calendar Event Title</label>
                  <input type="text" class="form-control" id="event_title" v-model="form.event_title" placeholder="Leave empty to use notice title">
                  <small class="text-muted">If empty, the notice title will be used for the calendar event</small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center mt-4">
          <button class="btn btn-primary" type="submit">
            <i class="ti ti-send me-1"></i> Publish Notice
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        title: '',
        description: '',
        image: null,
        add_to_calendar: true,
        event_color: 'primary',
        event_start_date: new Date().toISOString().substr(0, 10),
        event_end_date: '',
        event_title: ''
      },
      errors: [],
      successMessage: ''
    };
  },
  methods: {
    handleFileUpload(event) {
      this.form.image = event.target.files[0];
    },
    submitForm() {
      const formData = new FormData();
      formData.append('title', this.form.title);
      formData.append('description', this.form.description);
      formData.append('image', this.form.image);
      formData.append('add_to_calendar', this.form.add_to_calendar);
      formData.append('event_color', this.form.event_color);
      formData.append('event_start_date', this.form.event_start_date);
      formData.append('event_end_date', this.form.event_end_date);
      formData.append('event_title', this.form.event_title);

      axios.post('/api/notices', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      .then(response => {
        this.successMessage = 'Notice created successfully!';
        this.errors = [];
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = Object.values(error.response.data.errors).flat();
        } else {
          this.errors = ['An error occurred while submitting the form.'];
        }
        this.successMessage = '';
      });
    }
  }
};
</script>

<style>
.form-check-label i.fas {
  font-size: 0.8em;
}

#calendarEventSection {
  transition: all 0.3s ease;
}

.card-header .form-check {
  margin-bottom: 0;
}

.text-danger { color: #dc3545 !important; }
.text-warning { color: #ffc107 !important; }
.text-primary { color: #0d6efd !important; }
.text-success { color: #198754 !important; }
</style>
