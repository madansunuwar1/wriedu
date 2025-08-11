<template>
  <div class="main-container">
    <div class="widget-content searchable-container list">
      <!-- Page Title -->
      <div class="card card-body">
        <div class="row">
          <div class="col-md-9 col-xl-9">
            <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Comment Data</div>
          </div>
        </div>
      </div>

      <!-- Success Message Alert -->
      <div v-if="successMessage" class="alert alert-success mt-4">
        {{ successMessage }}
      </div>

      <!-- Add/Edit Form Card -->
      <div class="card mt-4">
        <div class="border-bottom title-part-padding">
          <h4 class="card-title mb-0">{{ isEditing ? 'Edit' : 'Add' }} Dynamic Comment</h4>
        </div>
        <div class="card-body">
          <form id="commentForm" @submit.prevent="submitForm" class="needs-validation" novalidate>
            <div class="row mb-3">
              <div class="col-md-12">
                <label class="form-label" for="applications">Applications</label>
                <input type="text" class="form-control" id="applications" v-model="form.applications" placeholder="Enter applications" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please provide applications info.</div>
              </div>
            </div>
            <div class="text-center mt-4">
              <button class="btn btn-primary" type="submit">
                <i :class="isEditing ? 'ti ti-device-floppy' : 'ti ti-message-plus'" class="me-1"></i>
                {{ isEditing ? 'Update Comment' : 'Add Comment' }}
              </button>
              <button v-if="isEditing" class="btn btn-secondary ms-2" type="button" @click="resetForm">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Comments Table -->
      <div class="table-responsive mt-4 mb-4 border rounded-1">
        <table class="table text-nowrap mb-0 align-middle" id="commentTable">
          <thead class="text-dark fs-4">
            <tr>
              <th><h6 class="fs-4 fw-semibold mb-0">Applications</h6></th>
              <th><h6 class="fs-4 fw-semibold mb-0">Actions</h6></th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="comments.length === 0">
              <td colspan="2" class="text-center">No comments found.</td>
            </tr>
            <tr v-for="comment in comments" :key="comment.id">
              <td>{{ comment.applications }}</td>
              <td>
                <div class="dropdown dropstart">
                  <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical fs-6"></i>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li>
                      <a class="dropdown-item d-flex align-items-center gap-3" href="#" @click.prevent="editComment(comment)">
                        <i class="fs-4 ti ti-edit"></i> Edit
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item d-flex align-items-center gap-3 text-danger" href="#" @click.prevent="deleteComment(comment.id)">
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

<script>
export default {
  data() {
    return {
      form: {
        id: null,
        applications: ''
      },
      comments: [],
      successMessage: ''
    };
  },
  computed: {
    isEditing() {
      return this.form.id !== null;
    }
  },
  created() {
    this.fetchComments();
  },
  methods: {
    // Fetch all comments from the API
    fetchComments() {
      axios.get('/api/comments/add')
        .then(response => {
          this.comments = response.data;
        })
        .catch(error => {
          console.error("Error fetching comments:", error);
        });
    },

    // Handle form submission for both creating and updating
    submitForm() {
      const formElement = this.$el.querySelector('#commentForm');
      formElement.classList.add('was-validated');

      // Prevent submission if form is invalid
      if (!formElement.checkValidity()) {
        return;
      }

      const promise = this.isEditing
        ? axios.put(`/api/comments/${this.form.id}`, this.form)
        : axios.post('/api/comments/create', this.form);

      promise
        .then(() => {
          this.fetchComments();
          const action = this.isEditing ? 'updated' : 'saved';
          this.successMessage = `Comment ${action} successfully!`;
          this.resetForm();
        })
        .catch(error => {
          this.successMessage = ''; // Clear success message on error
          if (error.response && error.response.data && error.response.data.errors) {
            const errorMessages = Object.values(error.response.data.errors).flat();
            alert('Validation Error:\n' + errorMessages.join('\n'));
          } else {
            console.error("Error submitting form:", error);
            alert('An unexpected error occurred. Please try again.');
          }
        });
    },

    // Populate the form for editing a comment
    editComment(comment) {
      this.form = { ...comment };
      this.successMessage = ''; // Clear any previous success messages
      // Scroll to the form for a better user experience
      this.$el.querySelector('#commentForm').scrollIntoView({ behavior: 'smooth' });
    },

    // Delete a comment
    deleteComment(id) {
      if (confirm('Are you sure you want to delete this comment?')) {
        axios.delete(`/api/comments/${id}`)
          .then(() => {
            this.fetchComments();
            // If the deleted comment was being edited, reset the form
            if (this.form.id === id) {
              this.resetForm();
            }
            this.successMessage = 'Comment deleted successfully!';
          })
          .catch(error => {
            console.error("Error deleting comment:", error);
            alert('Could not delete the comment. Please try again.');
          });
      }
    },

    // Reset the form to its initial state
    resetForm() {
      this.form = { id: null, applications: '' };
      const formElement = this.$el.querySelector('#commentForm');
      if (formElement) {
        formElement.classList.remove('was-validated');
      }
    }
  }
};
</script>