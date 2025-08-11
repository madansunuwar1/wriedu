<template>
  <div class="main-container">
    <div class="widget-content searchable-container list">
      <!-- Title -->
      <div class="card card-body">
        <div class="row">
          <div class="col-md-9 col-xl-9">
            <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Counselor Index</div>
          </div>
        </div>
      </div>

      <!-- Add/Update Form Card -->
      <div class="card mt-4">
        <div class="border-bottom title-part-padding">
          <h4 class="card-title mb-0">{{ isEditing ? 'Update Counselor' : 'Add Counselor' }}</h4>
        </div>
        <div class="card-body">
          <!-- We use @submit.prevent to handle submission with a method -->
          <form @submit.prevent="handleSubmit" class="needs-validation" :class="{ 'was-validated': formSubmitted }" novalidate>
            <div class="row mb-3">
              <div class="col-md-12">
                <label class="form-label" for="name">Counselor Name</label>
                <!-- v-model links the input to our state -->
                <input type="text" class="form-control" id="name" v-model="formData.name" placeholder="Enter counselor name" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please provide a counselor name.</div>
              </div>
            </div>

            <div class="text-center mt-4">
              <button class="btn btn-primary" type="submit">
                <i :class="isEditing ? 'ti ti-device-floppy' : 'ti ti-plus'" class="me-1"></i>
                {{ isEditing ? 'Update Counselor' : 'Add Counselor' }}
              </button>
              <button v-if="isEditing" class="btn btn-secondary ms-2" type="button" @click="cancelEdit">
                 Cancel
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Counselors Table -->
      <div class="table-responsive mb-4 border rounded-1 mt-4">
        <table class="table text-nowrap mb-0 align-middle" id="counselorTable">
          <thead class="text-dark fs-4">
            <tr>
              <th>
                <h6 class="fs-4 fw-semibold mb-0">Counselor Name</h6>
              </th>
              <th>
                <h6 class="fs-4 fw-semibold mb-0">Actions</h6>
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- Loader -->
            <tr v-if="loading">
                <td colspan="2" class="text-center">Loading counselors...</td>
            </tr>
            <!-- No Data Message -->
            <tr v-if="!loading && counselors.length === 0">
                <td colspan="2" class="text-center">No counselors found.</td>
            </tr>
            <!-- v-for replaces the @foreach loop -->
            <tr v-for="counselor in counselors" :key="counselor.id">
              <td>{{ counselor.name }}</td>
              <td>
                <div class="dropdown dropstart">
                  <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical fs-6"></i>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li>
                      <!-- @click calls our edit method -->
                      <a class="dropdown-item d-flex align-items-center gap-3" href="#" @click.prevent="startEdit(counselor)">
                        <i class="fs-4 ti ti-edit"></i> Edit
                      </a>
                    </li>
                    <li>
                      <!-- @click calls our delete method -->
                      <a class="dropdown-item d-flex align-items-center gap-3 text-danger" href="#" @click.prevent="confirmDelete(counselor.id)">
                        <i class="fs-4 ti ti-trash"></i> Delete
                      </a>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

// --- STATE ---
const counselors = ref([]); // Holds the list of counselors
const loading = ref(true); // For showing a loading indicator
const isEditing = ref(false); // To toggle between Add and Update mode
const formSubmitted = ref(false); // For Bootstrap validation style

// Use reactive for the form data object
const formData = reactive({
  id: null,
  name: '',
});

// --- LIFECYCLE HOOKS ---
// onMounted is called when the component is first loaded
onMounted(() => {
  fetchCounselors();
});

// --- METHODS ---
const fetchCounselors = async () => {
  loading.value = true;
  try {
    // We use /api/ prefix for API calls
    const response = await axios.get('/api/names');
    counselors.value = response.data;
  } catch (error) {
    console.error("Error fetching counselors:", error);
    Swal.fire('Error!', 'Could not fetch counselors.', 'error');
  } finally {
    loading.value = false;
  }
};

const handleSubmit = async () => {
    formSubmitted.value = true;
    // Basic frontend validation
    if (!formData.name) {
        return;
    }

    if (isEditing.value) {
        await updateCounselor();
    } else {
        await storeCounselor();
    }
};

const storeCounselor = async () => {
  try {
    const response = await axios.post('/api/names', { name: formData.name });
    Swal.fire('Success!', response.data.message, 'success');
    resetForm();
    fetchCounselors(); // Refresh the list
  } catch (error) {
    handleApiError(error, 'Failed to add counselor.');
  }
};

const updateCounselor = async () => {
  try {
    const response = await axios.put(`/api/names/${formData.id}`, { name: formData.name });
    Swal.fire('Success!', response.data.message, 'success');
    resetForm();
    fetchCounselors(); // Refresh the list
  } catch (error) {
    handleApiError(error, 'Failed to update counselor.');
  }
};

const startEdit = (counselor) => {
  isEditing.value = true;
  formData.id = counselor.id;
  formData.name = counselor.name;
  // Scroll to form for better UX
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEdit = () => {
    resetForm();
};

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      deleteCounselor(id);
    }
  });
};

const deleteCounselor = async (id) => {
    try {
        const response = await axios.delete(`/api/names/${id}`);
        Swal.fire('Deleted!', response.data.message, 'success');
        fetchCounselors(); // Refresh the list
    } catch (error) {
        handleApiError(error, 'Failed to delete counselor.');
    }
};

const resetForm = () => {
    isEditing.value = false;
    formSubmitted.value = false;
    formData.id = null;
    formData.name = '';
};

// Helper to handle API validation errors
const handleApiError = (error, defaultMessage) => {
    if (error.response && error.response.status === 422) {
        // Handle validation errors
        const errors = error.response.data.errors;
        const errorMessages = Object.values(errors).flat().join('<br>');
        Swal.fire('Validation Error!', errorMessages, 'error');
    } else {
        console.error(defaultMessage, error);
        Swal.fire('Error!', defaultMessage, 'error');
    }
};
</script>