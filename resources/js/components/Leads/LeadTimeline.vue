<template>
  <div class="timeline-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="card-title mb-0">Timeline History</h5>
      <button class="btn btn-outline-secondary btn-sm border">
        <i class="bi bi-filter"></i>
      </button>
    </div>

    <div v-if="!groupedActivities || Object.keys(groupedActivities).length === 0" class="text-center text-muted py-5">
      <p>No activity has been recorded for this lead yet.</p>
    </div>

    <div v-else>
      <!-- Loop through each date group -->
      <div v-for="(events, date) in groupedActivities" :key="date" class="timeline-date-group">
        <!-- The Date Chip -->
        <div class="timeline-date-chip-container">
            <span class="timeline-date-chip">{{ formatDate(date) }}</span>
        </div>
        
        <!-- The vertical line for this date group -->
        <div class="timeline-group-line"></div>

        <!-- Loop through events for that date -->
        <div v-for="activity in events" :key="activity.id" class="timeline-item">
            <div class="timeline-time">{{ formatTime(activity.created_at) }}</div>
            <div class="timeline-marker">
                <div class="timeline-icon" :class="getActivityDetails(activity).iconClass">
                    <i :class="getActivityDetails(activity).icon"></i>
                </div>
            </div>
            <div class="timeline-content">
                <h6 class="timeline-title">{{ getActivityDetails(activity).title }}</h6>
                <p class="timeline-body" v-html="getActivityDetails(activity).body"></p>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  activities: {
    type: Array,
    required: true,
    default: () => []
  }
});

// Group activities by date to create the "date chip" structure
const groupedActivities = computed(() => {
  if (!props.activities || props.activities.length === 0) return {};

  // Create a mutable copy and sort it to ensure activities are always in descending order
  const sortedActivities = [...props.activities].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

  return sortedActivities.reduce((groups, activity) => {
    // Group by YYYY-MM-DD to ensure chronological order of dates
    const dateKey = activity.created_at.split('T')[0];
    if (!groups[dateKey]) {
      groups[dateKey] = [];
    }
    groups[dateKey].push(activity);
    return groups;
  }, {});
});

function formatDate(dateString) {
  const date = new Date(dateString);
  // Adding timeZone to avoid off-by-one day errors
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' });
}

function formatTime(dateTimeString) {
  const date = new Date(dateTimeString);
  return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
}

function getActivityDetails(activity) {
    const properties = activity.properties || {};
    const causerName = activity.causer ? `by ${activity.causer.name} ${activity.causer.last}`.trim() : 'by System';
    const eventDate = formatDate(activity.created_at.split('T')[0]);

    // Helper to format field names like 'courseLevel' into 'Course Level'
    const formatFieldName = (field) => {
        if (!field) return '';
        const result = field.replace(/([A-Z])/g, " $1");
        return result.charAt(0).toUpperCase() + result.slice(1);
    };

    let details = {
        icon: 'bi-info-circle-fill',
        iconClass: 'icon-default',
        title: activity.description,
        body: `${causerName} ${eventDate}`,
        hasNote: false,
    };

    switch (properties.action || activity.description) {
        case 'lead_created':
        case 'created':
            details = {
                ...details,
                icon: 'bi-bullseye', // Target icon like the image
                iconClass: 'icon-lead-created',
                title: 'Lead Created',
                body: `${causerName} ${eventDate}`,
                hasNote: true, // Lead created can have a note
            };
            break;

        case 'field_updated':
            const oldValue = properties.old_value || properties.old_value === 0 ? `"${properties.old_value}"` : '<em>empty</em>';
            const newValue = properties.new_value || properties.new_value === 0 ? `"${properties.new_value}"` : '<em>empty</em>';
            
            details = {
                ...details,
                icon: 'bi-pencil-fill',
                iconClass: 'icon-field-updated',
                title: `${formatFieldName(properties.field)} Updated`,
                body: `changed from ${oldValue} to ${newValue} ${causerName} ${eventDate}`,
            };
            break;

        case 'comment_added':
            details = {
                ...details,
                icon: 'bi-chat-dots-fill',
                iconClass: 'icon-comment-added',
                title: 'Comment Added',
                body: `${causerName} ${eventDate}`,
            };
            break;

        case 'application_forwarded':
            details = {
                ...details,
                icon: 'bi-send-fill',
                iconClass: 'icon-application-forwarded',
                title: 'Application Forwarded',
                body: `to <strong>${properties.forwarded_to_user_name}</strong> ${causerName} ${eventDate}`,
            };
            break;
    }
    return details;
}
</script>

<style scoped>
.timeline-wrapper {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.timeline-date-group {
    position: relative;
    /* This padding creates space on the left for the entire timeline structure */
    padding-left: 95px; 
    margin-bottom: 2rem;
}
.timeline-date-group:last-child {
    margin-bottom: 0;
}

.timeline-group-line {
    position: absolute;
    /* Aligned with the center of the timeline-marker */
    left: 115px; 
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-date-chip-container {
    position: relative;
    z-index: 2;
    /* Pulls chip container back to be centered on the line */
    left: 20px; 
    margin-bottom: 1.5rem;
}

.timeline-date-chip {
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 9999px;
    padding: 6px 14px;
    font-size: 0.85rem;
    font-weight: 500;
    color: #495057;
}

.timeline-item {
    display: flex;
    position: relative;
    z-index: 1; 
    align-items: flex-start;
    /* [THE FIX] Add padding to the left of the item content area.
       This makes room for the absolutely positioned marker. */
    padding-left: 35px; 
    margin-bottom: 1.5rem;
}
.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-time {
    position: absolute;
    /* Positions time to the far left of the entire group */
    left: -95px; 
    top: 5px;
    width: 70px;
    text-align: right;
    color: #6c757d;
    font-size: 0.875rem;
}

.timeline-marker {
    position: absolute;
    /* Centers the icon on the line within its parent's padding */
    left: -20px; 
    top: 0;
    line-height: 1;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #fff;
    border: 2px solid #e9ecef;
}

/* Icon-specific styling */
.icon-lead-created { color: #6f42c1; border-color: #d1c4e9; }
.icon-field-updated { color: #fd7e14; border-color: #ffe0b2; }
.icon-comment-added { color: #0d6efd; border-color: #bbdefb; }
.icon-application-forwarded { color: #198754; border-color: #c8e6c9; }
.icon-default { color: #6c757d; border-color: #e0e0e0; }

.timeline-content {
    flex-grow: 1;
}

.timeline-title {
    font-size: 1rem;
    font-weight: 500;
    color: #212529;
    margin-bottom: 0.1rem;
}

.timeline-body {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}
.timeline-body :deep(strong) {
    color: #343a40;
    font-weight: 500;
}
.timeline-body :deep(em) {
    font-style: normal;
    color: #495057;
}

.add-note-link {
    font-size: 0.9rem;
    font-weight: 500;
    color: #0d6efd;
    text-decoration: none;
    transition: color 0.2s ease;
}
.add-note-link:hover {
    color: #0a58ca;
}
</style>