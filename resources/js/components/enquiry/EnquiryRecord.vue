<template>
  <div class="position-relative overflow-hidden">
    <!-- Loading state -->
    <div v-if="loading" class="text-center p-4">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="alert alert-danger mx-9 mt-n10">
      {{ error }}
    </div>

    <!-- Main content -->
    <div v-else-if="enquirie" class="card mx-9 mt-n10">
      <div class="card-body pb-0">
        <div class="d-md-flex align-items-center justify-content-between text-center text-md-start">
          <div class="d-md-flex align-items-center">
            <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
              <img src="/assets/images/profile/user-1.jpg" alt="student-img" class="img-fluid rounded-circle" width="100" height="100">
            </div>
            <div class="ms-0 ms-md-3 mb-9 mb-md-0">
              <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-1 text-start">
                <h4 class="me-7 mb-0 text-start fs-7">{{ enquirie.uname }}</h4>
                <span class="badge fs-2 fw-bold rounded-pill bg-primary-subtle text-primary border-primary border">Active</span>
              </div>
              <p class="fs-4 mb-1 text-start">Enquiry Record</p>
            </div>
          </div>
        </div>

        <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start" id="pills-tab" role="tablist">
          <li class="nav-item me-2 me-md-3" role="presentation">
            <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details" aria-selected="true">
              <i class="ti ti-user-circle me-0 me-md-6 fs-6"></i>
              <span class="d-none d-md-block">Student Details</span>
            </button>
          </li>
          <li class="nav-item me-2 me-md-3" role="presentation">
            <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-comments-tab" data-bs-toggle="pill" data-bs-target="#pills-comments" type="button" role="tab" aria-controls="pills-comments" aria-selected="false">
              <i class="ti ti-message-circle me-0 me-md-6 fs-6"></i>
              <span class="d-none d-md-block">Comments</span>
            </button>
          </li>
        </ul>
      </div>
    </div>

    <div v-if="enquirie" class="tab-content mx-10" id="pills-tabContent">
      <!-- Student Details Tab -->
      <div class="tab-pane fade show active" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab" tabindex="0">
        <div class="row">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-body p-4">
                <h5 class="mb-9">Personal Information</h5>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">Student Name</h6>
                    <p class="mb-0 text-start">{{ enquirie?.uname || '' }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">Email</h6>
                    <p class="mb-0 text-start">{{ enquirie?.email || '' }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">Contact Number</h6>
                    <p class="mb-0 text-start">{{ enquirie?.contact || '' }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">Guardian's Name</h6>
                    <p class="mb-0 text-start">{{ enquirie?.guardians || '' }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">Guardian's Number</h6>
                    <p class="mb-0 text-start">{{ enquirie?.contacts || '' }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">Country</h6>
                    <p class="mb-0 text-start">{{ enquirie?.country || '' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card">
              <div class="card-body p-4">
                <h5 class="mb-9">Education & Test Scores</h5>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">Education Level</h6>
                    <p class="mb-0 text-start">{{ enquirie?.education || '' }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">IELTS</h6>
                    <p class="mb-0 text-start">{{ enquirie?.ielts || 'N/A' }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">TOEFL</h6>
                    <p class="mb-0 text-start">{{ enquirie?.toefl || 'N/A' }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-9">
                  <div class="ms-6">
                    <h6 class="mb-1 text-start">Counselor</h6>
                    <p class="mb-0 text-start">{{ enquirie?.counselor || '' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Comments Tab -->
      <div class="tab-pane fade" id="pills-comments" role="tabpanel" aria-labelledby="pills-comments-tab" tabindex="0">
        <div class="row">
          <div class="col-lg-12">
            <div class="card border">
              <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                 <img :src="getCurrentUserAvatar()" alt="user-profile" width="32" height="32" class="rounded-circle">
                  <h6 class="mb-0 text-start ms-6">Add New Comment</h6>
                </div>

                <form @submit.prevent="submitComment" v-if="enquirie">
                  <input type="hidden" name="user_id" :value="currentUser?.id">
                  <input type="hidden" name="enquiry_id" :value="enquirie.id">
                  <div class="userprofile-quill-editors mb-3">
                    <textarea id="comment" v-model="newComment" class="form-control" placeholder="Enter your comment" required></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary shadow-none" :disabled="submittingComment">
                    {{ submittingComment ? 'Posting...' : 'Post' }}
                  </button>
                </form>
              </div>
            </div>

            <div style="height: 92vh; overflow-y: auto;">
              <div v-for="leadComment in sortedComments" :key="leadComment.id" class="p-4 rounded-4 text-bg-light mb-3">
                <div class="card-body border-bottom">
                  <div class="d-flex justify-content-between">
                    <div class="d-flex align-items-center gap-6 flex-wrap">
                      <img :src="getCommentUserAvatar(leadComment)" alt="user-img" class="rounded-circle" width="40" height="40">
                      <h6 class="mb-0 text-start">{{ getCommentAuthorName(leadComment) }}</h6>
                      <span class="fs-2">
                        <span class="p-1 text-bg-light rounded-circle d-inline-block"></span>
                        {{ formatDate(leadComment.created_at) }}
                      </span>
                    </div>
                    <div class="d-flex align-items-center" v-if="canEditComment(leadComment)">
                      <div class="dropdown ms-auto">
                        <a class="text-dark d-flex align-items-center justify-content-center bg-transparent p-2 fs-4 rounded-circle" href="javascript:void(0)" :id="'dropdownMenuButton' + leadComment.id" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="ti ti-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu" :aria-labelledby="'dropdownMenuButton' + leadComment.id">
                          <li><a class="dropdown-item" href="#" @click.prevent="editComment(leadComment)">Edit</a></li>
                          <li><a class="dropdown-item" href="#" @click.prevent="deleteComment(leadComment.id)">Delete</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <p class="text-dark my-3 text-start">{{ leadComment.comment || '' }}</p>
                  <small class="text-muted" v-if="leadComment.updated_by && leadComment.updated_at != leadComment.created_at">(Updated)</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Comment Modal -->
    <div class="modal fade" id="editCommentModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Comment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="editCommentForm" @submit.prevent="updateComment">
            <div class="modal-body">
              <textarea class="form-control" v-model="editCommentText" rows="5" required></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" :disabled="updatingComment">
                {{ updatingComment ? 'Updating...' : 'Update' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  name: 'EnquiryRecord',
  props: {
    id: {
      type: [String, Number],
      default: null
    }
  },
  inheritAttrs: false,
  data() {
    return {
      enquirie: null,
      leadComments: [],
      currentUser: null,
      newComment: '',
      editCommentText: '',
      selectedCommentId: null,
      loading: true,
      error: null,
      submittingComment: false,
      updatingComment: false
    };
  },
  computed: {
    sortedComments() {
      if (!Array.isArray(this.leadComments)) return [];
      return [...this.leadComments].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }
  },
  async mounted() {
    try {
      await Promise.all([
        this.fetchCurrentUser(),
        this.fetchEnquiryData()
      ]);
    } catch (error) {
      console.error('Error in mounted hook:', error);
      this.error = 'Failed to initialize component';
      this.loading = false;
    }

    this.$nextTick(() => {
      try {
        var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        dropdownElementList.map(function (dropdownToggleEl) {
          return new bootstrap.Dropdown(dropdownToggleEl);
        });
      } catch (error) {
        console.error('Error initializing Bootstrap dropdowns:', error);
      }
    });
  },
  methods: {
    async fetchCurrentUser() {
      try {
        const response = await fetch('/api/user/current', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        });

        if (response.ok) {
          const userData = await response.json();
          this.currentUser = userData.user || userData;
        } else {
          console.warn('Current user API not available, using fallback');
          this.currentUser = {
            id: 1,
            name: 'Current User',
            email: 'user@example.com',
            avatar: '/assets/images/profile/user-1.jpg',
            last_name: ''
          };
        }
      } catch (error) {
        console.error('Error fetching current user:', error);
        // Fallback user data
        this.currentUser = {
          id: 1,
          name: 'Current User',
          email: 'user@example.com',
          avatar: '/assets/images/profile/user-1.jpg',
          last_name: ''
        };
      }
    },

    async fetchEnquiryData() {
      try {
        this.loading = true;
        this.error = null;

        const id = this.id || this.$route.params.id;

        if (!id) {
          this.error = 'No enquiry ID provided';
          this.loading = false;
          return;
        }

        const response = await fetch(`/api/enquiries/record/${id}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        this.enquirie = data.enquirie;
        // Use comments exactly as returned from backend - they now include proper user data
        this.leadComments = data.lead_comments || [];

        console.log('Fetched comments:', this.leadComments); // Debug log

      } catch (error) {
        console.error('Error fetching enquiry data:', error);
        this.error = 'Failed to load enquiry data. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    getCommentAuthorName(comment) {
      // Priority order for getting author name
      
      // 1. Use author_name directly from backend (most reliable)
      if (comment.author_name) {
        return comment.author_name;
      }
      
      // 2. Use createdBy object
      if (comment.createdBy && comment.createdBy.name) {
        const lastName = comment.createdBy.last ? ` ${comment.createdBy.last}` : '';
        return `${comment.createdBy.name}${lastName}`;
      }
      
      // 3. Use user object
      if (comment.user && comment.user.name) {
        const lastName = comment.user.last_name ? ` ${comment.user.last_name}` : '';
        return `${comment.user.name}${lastName}`;
      }
      
      // 4. Fallback to user_name
      if (comment.user_name) {
        return comment.user_name;
      }
      
      // 5. Final fallback
      return 'Unknown User';
    },

    getCommentUserAvatar(comment) {
  // Priority order for getting user avatar from user table with storage path
  
  // 1. First check if we have user object with avatar (from user table)
  if (comment.user && comment.user.avatar) {
    // If avatar exists, use storage path, otherwise use default
    return comment.user.avatar ? `/storage/avatars/${comment.user.avatar}` : '/assets/images/profile/user-1.jpg';
  }
  
  // 2. Check createdBy object avatar (from user table)
  if (comment.createdBy && comment.createdBy.avatar) {
    return comment.createdBy.avatar ? `/storage/avatars/${comment.createdBy.avatar}` : '/assets/images/profile/user-1.jpg';
  }
  
  // 3. Check user_avatar field (might be from user table)
  if (comment.user_avatar) {
    return comment.user_avatar ? `/storage/avatars/${comment.user_avatar}` : '/assets/images/profile/user-1.jpg';
  }
  
  // 4. Check direct avatar field
  if (comment.avatar) {
    return comment.avatar ? `/storage/avatars/${comment.avatar}` : '/assets/images/profile/user-1.jpg';
  }
  
  // 5. Default avatar fallback
  return '/assets/images/profile/user-1.jpg';
},

// Helper method to get avatar path for current user
getCurrentUserAvatar() {
  if (this.currentUser && this.currentUser.avatar) {
    return `/storage/avatars/${this.currentUser.avatar}`;
  }
  return '/assets/images/profile/user-1.jpg';
},

// Updated submitComment method to handle avatar properly
async submitComment() {
  if (!this.newComment.trim() || !this.currentUser || !this.enquirie) {
    console.warn('Missing required data for comment submission');
    return;
  }

  try {
    this.submittingComment = true;

    const commentData = {
      user_id: this.currentUser.id,
      enquiry_id: this.enquirie.id,
      comment: this.newComment.trim()
    };

    console.log('Submitting comment:', commentData);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    const response = await fetch('/api/comment', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
      },
      body: JSON.stringify(commentData)
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
    }

    const responseData = await response.json();
    console.log('Comment response:', responseData);

    // Extract comment data from response
    let newCommentData;
    if (responseData.data) {
      newCommentData = responseData.data;
    } else if (responseData.success && responseData.comment) {
      newCommentData = responseData.comment;
    } else {
      newCommentData = responseData;
    }

    // Ensure the comment has an ID
    if (!newCommentData.id) {
      newCommentData.id = Date.now();
    }

    // FIXED: Ensure user data is properly set for the new comment with correct avatar path
    if (!newCommentData.author_name || !newCommentData.user) {
      const avatarPath = this.currentUser.avatar || null;
      newCommentData = {
        ...newCommentData,
        author_name: this.currentUser.name,
        user_avatar: avatarPath,
        user: {
          id: this.currentUser.id,
          name: this.currentUser.name,
          email: this.currentUser.email,
          avatar: avatarPath
        },
        createdBy: {
          name: this.currentUser.name,
          last: this.currentUser.last_name || '',
          avatar: avatarPath
        }
      };
    }

    // Add the new comment to the beginning of the array
    this.leadComments.unshift(newCommentData);
    this.newComment = '';

    console.log('Comment added successfully');

  } catch (error) {
    console.error('Error submitting comment:', error);
    alert(`Failed to submit comment: ${error.message}`);
  } finally {
    this.submittingComment = false;
  }
  },

    canEditComment(comment) {
      // User can edit their own comments
      if (!this.currentUser) return false;
      
      return comment.user_id === this.currentUser.id || 
             comment.created_by === this.currentUser.id;
    },

    formatDate(date) {
      if (!date) return '';
      
      try {
        return new Date(date).toLocaleString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          hour12: true
        });
      } catch (error) {
        console.error('Date formatting error:', error);
        return date;
      }
    },

    async submitComment() {
      if (!this.newComment.trim() || !this.currentUser || !this.enquirie) {
        console.warn('Missing required data for comment submission');
        return;
      }

      try {
        this.submittingComment = true;

        const commentData = {
          user_id: this.currentUser.id,
          enquiry_id: this.enquirie.id,
          comment: this.newComment.trim()
        };

        console.log('Submitting comment:', commentData);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await fetch('/api/comment', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
          },
          body: JSON.stringify(commentData)
        });

        if (!response.ok) {
          const errorData = await response.json().catch(() => ({}));
          throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        const responseData = await response.json();
        console.log('Comment response:', responseData);

        // Extract comment data from response
        let newCommentData;
        if (responseData.data) {
          newCommentData = responseData.data;
        } else if (responseData.success && responseData.comment) {
          newCommentData = responseData.comment;
        } else {
          newCommentData = responseData;
        }

        // Ensure the comment has an ID
        if (!newCommentData.id) {
          newCommentData.id = Date.now();
        }

        // FIXED: Ensure user data is properly set for the new comment
        // If the backend didn't return complete user data, construct it from currentUser
        if (!newCommentData.author_name || !newCommentData.user) {
          newCommentData = {
            ...newCommentData,
            author_name: this.currentUser.name,
            user_avatar: this.currentUser.avatar || '/assets/images/profile/user-6.jpg',
            user: {
              id: this.currentUser.id,
              name: this.currentUser.name,
              email: this.currentUser.email,
              avatar: this.currentUser.avatar || '/assets/images/profile/user-6.jpg'
            },
            createdBy: {
              name: this.currentUser.name,
              last: this.currentUser.last_name || '',
              avatar: this.currentUser.avatar || '/assets/images/profile/user-6.jpg'
            }
          };
        }

        // Add the new comment to the beginning of the array
        this.leadComments.unshift(newCommentData);
        this.newComment = '';

        console.log('Comment added successfully');

      } catch (error) {
        console.error('Error submitting comment:', error);
        alert(`Failed to submit comment: ${error.message}`);
      } finally {
        this.submittingComment = false;
      }
    },

    editComment(comment) {
      if (!comment || !comment.comment) {
        console.error('Invalid comment data for editing');
        return;
      }

      this.editCommentText = comment.comment;
      this.selectedCommentId = comment.id;
      
      try {
        const editCommentModalEl = document.getElementById('editCommentModal');
        if (editCommentModalEl) {
          const editCommentModal = Modal.getOrCreateInstance(editCommentModalEl);
          editCommentModal.show();
        } else {
          console.error('Edit comment modal not found');
        }
      } catch (error) {
        console.error('Error opening edit modal:', error);
      }
    },

    async updateComment() {
      if (!this.editCommentText.trim() || !this.selectedCommentId) {
        console.warn('Missing data for comment update');
        return;
      }

      try {
        this.updatingComment = true;

        const commentData = {
          comment: this.editCommentText.trim()
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch(`/api/comments/${this.selectedCommentId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
          },
          body: JSON.stringify(commentData)
        });

        if (!response.ok) {
          const errorData = await response.json().catch(() => ({}));
          throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        const updatedData = await response.json();

        // Find and update the comment in the array
        const index = this.leadComments.findIndex(c => c.id === this.selectedCommentId);
        if (index !== -1) {
          // Update only specific fields, preserve user data
          this.leadComments[index] = {
            ...this.leadComments[index],
            comment: updatedData.comment || this.editCommentText.trim(),
            updated_at: updatedData.updated_at || new Date().toISOString(),
            updated_by: this.currentUser.id
          };
        }

        // Close the modal
        try {
          const editCommentModalEl = document.getElementById('editCommentModal');
          const editCommentModal = Modal.getInstance(editCommentModalEl);
          if (editCommentModal) {
            editCommentModal.hide();
          }
        } catch (modalError) {
          console.error('Error closing modal:', modalError);
        }

        console.log('Comment updated successfully');

      } catch (error) {
        console.error('Error updating comment:', error);
        alert(`Failed to update comment: ${error.message}`);
      } finally {
        this.updatingComment = false;
      }
    },

    async deleteComment(id) {
      if (!id) {
        console.error('No comment ID provided for deletion');
        return;
      }

      if (!confirm('Are you sure you want to delete this comment?')) {
        return;
      }

      // Store the comment for potential rollback
      const commentIndex = this.leadComments.findIndex(comment => comment.id === id);
      const deletedComment = commentIndex !== -1 ? this.leadComments[commentIndex] : null;

      try {
        // Optimistically remove from UI
        if (commentIndex !== -1) {
          this.leadComments.splice(commentIndex, 1);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch(`/api/comments/${id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
          }
        });

        if (!response.ok) {
          const errorData = await response.json().catch(() => ({}));
          throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        console.log('Comment deleted successfully');

      } catch (error) {
        console.error('Error deleting comment:', error);
        alert(`Failed to delete comment: ${error.message}`);
        
        // Rollback: restore the comment if deletion failed
        if (deletedComment && commentIndex !== -1) {
          this.leadComments.splice(commentIndex, 0, deletedComment);
        }
      }
    },

    // Utility method to refresh comments (can be called externally)
    async refreshComments() {
      const id = this.id || this.$route.params.id;
      if (id) {
        await this.fetchEnquiryData();
      }
    }
  }
};
</script>