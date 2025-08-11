<template>
  <div class="card">
    <div class="border-bottom title-part-padding">
      <h4 class="card-title mb-0">Universities List</h4>
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

      <!-- Form to add/edit a university -->
      <div class="card mb-4" :style="editUniversity && editUniversity.background_image ? `background-image: url('${editUniversity.background_image}'); background-size: cover; background-position: center; background-blend-mode: lighten;` : ''">
        <div v-if="editUniversity && editUniversity.background_image" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255, 255, 255, 0.85); z-index: 0;"></div>
        <div class="card-header" style="position: relative; z-index: 1;">
          <h5 class="mb-0">{{ editUniversity ? 'Edit University' : 'Add New University' }}</h5>
        </div>
        <div class="card-body" style="position: relative; z-index: 1;">
          <form @submit.prevent="saveUniversity" class="needs-validation" novalidate>
            <div class="row mb-3">
              <div class="col-md-12">
                <label class="form-label" for="name">University Name</label>
                <input type="text" class="form-control" id="name" v-model="university.name" placeholder="Enter university name" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please provide a university name.</div>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label" for="image_link">University Logo URL</label>
                <input type="url" class="form-control" id="image_link" v-model="university.image_link" placeholder="Enter logo URL" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please provide a valid image URL.</div>
                <div v-if="editUniversity && editUniversity.image_link" class="mt-2">
                  <img :src="editUniversity.image_link" alt="Logo Preview" style="max-width: 100px; height: auto;">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="background_image">Background Image URL</label>
                <input type="url" class="form-control" id="background_image" v-model="university.background_image" placeholder="Enter background image URL">
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please provide a valid image URL.</div>
                <div v-if="editUniversity && editUniversity.background_image" class="mt-2">
                  <img :src="editUniversity.background_image" alt="Background Preview" style="max-width: 100px; height: auto;">
                </div>
              </div>
            </div>
            <div class="text-center mt-4">
              <button class="btn btn-primary" type="submit">
                <i class="ti" :class="editUniversity ? 'ti-pencil' : 'ti-plus'"></i>
                {{ editUniversity ? 'Update' : 'Add' }} University
              </button>
              <button v-if="editUniversity" type="button" class="btn btn-secondary" @click="cancelEdit">
                <i class="ti ti-x"></i> Cancel
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>University Name</th>
              <th>Logo</th>
              <th>Background</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="university in universities" :key="university.id" :style="university.background_image ? `background-image: url(${university.background_image}); background-size: cover; background-position: center; background-blend-mode: overlay; background-color: rgba(255, 255, 255, 0.9);` : ''">
              <td>{{ university.id }}</td>
              <td>{{ university.name }}</td>
              <td>
                <img :src="university.image_link" :alt="university.name" style="max-width: 100px; height: auto;">
              </td>
              <td>
                <img v-if="university.background_image" :src="university.background_image" :alt="'Background for ' + university.name" style="max-width: 100px; height: auto;">
                <span v-else class="text-muted">No background</span>
              </td>
              <td>
                <button @click="editUniversityForm(university)" class="btn btn-sm btn-warning">
                  <i class="ti ti-pencil"></i> Edit
                </button>
                <button @click="deleteUniversity(university.id)" class="btn btn-sm btn-danger">
                  <i class="ti ti-trash"></i> Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      universities: [],
      university: {
        name: '',
        image_link: '',
        background_image: ''
      },
      editUniversity: null,
      errors: [],
      successMessage: ''
    };
  },
  created() {
    this.fetchUniversities();
  },
  methods: {
    fetchUniversities() {
      axios.get('/api/manage/universities')
        .then(response => {
          this.universities = response.data;
        })
        .catch(error => {
          console.error('Error fetching universities:', error);
        });
    },
    saveUniversity() {
      this.errors = [];
      if (!this.university.name || !this.university.image_link) {
        this.errors.push('University name and logo URL are required.');
        return;
      }

      if (this.editUniversity) {
        axios.put(`/api/manage/universities/${this.editUniversity.id}`, this.university)
          .then(response => {
            this.successMessage = 'University updated successfully!';
            this.fetchUniversities();
            this.resetForm();
          })
          .catch(error => {
            console.error('Error updating university:', error);
          });
      } else {
        axios.post('/api/manage/universities', this.university)
          .then(response => {
            this.successMessage = 'University added successfully!';
            this.fetchUniversities();
            this.resetForm();
          })
          .catch(error => {
            console.error('Error adding university:', error);
          });
      }
    },
    editUniversityForm(university) {
      this.editUniversity = university;
      this.university = { ...university };
    },
    deleteUniversity(id) {
      if (confirm('Are you sure you want to delete this university?')) {
        axios.delete(`/api/manage/universities/${id}`)
          .then(response => {
            this.successMessage = 'University deleted successfully!';
            this.fetchUniversities();
          })
          .catch(error => {
            console.error('Error deleting university:', error);
          });
      }
    },
    cancelEdit() {
      this.editUniversity = null;
      this.resetForm();
    },
    resetForm() {
      this.university = {
        name: '',
        image_link: '',
        background_image: ''
      };
    }
  }
};
</script>
 