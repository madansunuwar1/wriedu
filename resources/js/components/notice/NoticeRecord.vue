<template>
  <div class="container notice-container">
    <div class="card overflow-hidden shadow-sm">
      <!-- Notice Header with Image -->
      <div class="position-relative">
        <img v-if="notice.image" :src="`/storage/${notice.image}`" class="notice-img" :alt="notice.title">
        <span v-if="notice.created_at" class="badge bg-light text-dark position-absolute bottom-0 end-0 m-3">
          <i class="bi bi-clock"></i> {{ formatTimeAgo(notice.created_at) }}
        </span>

        <img v-if="user" :src="user.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`"
             alt="Author"
             class="rounded-circle position-absolute bottom-0 start-0 mb-n3 ms-3 author-img"
             data-bs-toggle="tooltip"
             data-bs-placement="top"
             :data-bs-title="user.name">
      </div>

      <!-- Notice Body -->
      <div class="card-body p-4">
        <!-- Category and Title -->
        <span class="badge bg-primary bg-opacity-10 text-primary mb-2">Announcement</span>
        <h1 class="fw-semibold mb-3">{{ notice.title }}</h1>

        <!-- Meta Information -->
        <div class="d-flex align-items-center gap-4 text-muted mb-4">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar"></i> {{ formatDate(notice.created_at) }}
          </div>
          <div class="d-flex align-items-center gap-2 ms-auto">
            <i class="bi bi-clock-history"></i>
            {{ formatDate(notice.display_start_at) }} - {{ formatDate(notice.display_end_at) }}
          </div>
        </div>

        <!-- Notice Content -->
        <div class="notice-content mt-4" v-html="formatDescription(notice.description)"></div>

        <!-- Additional Content Sections -->
        <div v-if="notice.additional_content" class="border-top mt-5 pt-5">
          <h3 class="fw-semibold mb-3">Additional Details</h3>
          <div class="notice-content" v-html="formatDescription(notice.additional_content)"></div>
        </div>

        <!-- Quote Section (if applicable) -->
        <div v-if="notice.quote" class="border-top mt-5 pt-5">
          <div class="quote-block">
            <i class="bi bi-quote fs-4 text-primary"></i>
            <span class="fs-5">{{ notice.quote }}</span>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center py-3">
        <a href="#" class="btn btn-outline-secondary" @click.prevent="goBack">
          <i class="bi bi-arrow-left me-2"></i> Back
        </a>

        <div class="d-flex gap-2">
          <a v-if="user && user.canEditNotices" :href="`/backend/notice/${notice.id}/edit`" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i> Edit
          </a>

          <button class="btn btn-light">
            <i class="bi bi-share"></i>
          </button>
          <button class="btn btn-light">
            <i class="bi bi-bookmark"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Tooltip } from 'bootstrap';
import axios from 'axios';

export default {
  props: {
    id: {
      type: String,
      required: true
    },
    user: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      notice: {}
    };
  },
  methods: {
    formatTimeAgo(date) {
      const now = new Date();
      const createdAt = new Date(date);
      const seconds = Math.floor((now - createdAt) / 1000);

      let interval = Math.floor(seconds / 31536000);
      if (interval >= 1) {
        return interval + " year" + (interval === 1 ? "" : "s") + " ago";
      }
      interval = Math.floor(seconds / 2592000);
      if (interval >= 1) {
        return interval + " month" + (interval === 1 ? "" : "s") + " ago";
      }
      interval = Math.floor(seconds / 86400);
      if (interval >= 1) {
        return interval + " day" + (interval === 1 ? "" : "s") + " ago";
      }
      interval = Math.floor(seconds / 3600);
      if (interval >= 1) {
        return interval + " hour" + (interval === 1 ? "" : "s") + " ago";
      }
      interval = Math.floor(seconds / 60);
      if (interval >= 1) {
        return interval + " minute" + (interval === 1 ? "" : "s") + " ago";
      }
      return Math.floor(seconds) + " second" + (seconds === 1 ? "" : "s") + " ago";
    },
    formatDate(date) {
      const d = new Date(date);
      return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    },
    formatDescription(description) {
      return description ? description.replace(/\n/g, '<br>') : '';
    },
    goBack() {
      this.$router.go(-1);
    },
    async fetchNotice() {
      try {
        const response = await axios.get(`/api/notices/${this.id}`);
        this.notice = response.data;
      } catch (error) {
        console.error('Error fetching notice:', error);
      }
    }
  },
  mounted() {
    this.fetchNotice();
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new Tooltip(tooltipTriggerEl);
    });
  }
};
</script>

<style scoped>
/* Add your styles here */
.notice-container {
  margin-top: 20px;
}

.notice-img {
  width: 100%;
  height: auto;
}

.author-img {
  width: 50px;
  height: 50px;
}

.quote-block {
  display: flex;
  align-items: center;
  gap: 10px;
}
</style>
    