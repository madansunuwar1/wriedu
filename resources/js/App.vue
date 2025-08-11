<template>
  <div id="main-wrapper" :class="{ 'show-sidebar': isSidebarVisible }">
    <!-- Your full sidebar and header template is here... -->
    <aside class="left-sidebar with-vertical">
      <div class="brand-logo d-flex align-items-center gap-2 pt-2">
        <router-link to="/" class="text-nowrap logo-img">
          <img src="/img/wri.png" class="dark-logo" width="40" height="40" alt="Logo-Dark">
        </router-link>
        <div class="ww">WRI Education <br /> Consultancy</div>
        <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
          <i class="ti ti-x"></i>
        </a>
      </div>
      <div class="scroll-sidebar" data-simplebar="">
        <nav class="sidebar-nav">
          <ul id="sidebarnav" class="mb-0">
            <li class="sidebar-item">
              <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/dashboard" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-tachometer-alt fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Dashboard</span>
              </router-link>
            </li>
            <template v-if="auth.permissions.includes('view_applications')">
              <li class="nav-small-cap">
                <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
                <span class="hide-menu">Application</span>
              </li>
              <li class="sidebar-item">
                <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/applications"
                  aria-expanded="false">
                  <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                    <i class="fas fa-list-ul fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">Application History</span>
                </router-link>
              </li>
            </template>
            <template v-if="auth.permissions.includes('view_leads')">
              <li class="nav-small-cap">
                <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
                <span class="hide-menu">Leads</span>
              </li>
              <li class="sidebar-item">
                <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/leadform/indexs"
                  aria-expanded="false">
                  <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                    <i class="fas fa-users-cog fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">Leads Table</span>
                </router-link>
              </li>
            </template>
            <template v-if="['Administrator', 'Manager', 'Leads Manager'].includes(auth.user.role)">
              <li class="nav-small-cap">
                <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
                <span class="hide-menu">Raw Leads</span>
              </li>
              <li class="sidebar-item">
                <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/rawlead" aria-expanded="false">
                  <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                    <i class="fas fa-users-cog fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">Raw Leads</span>
                </router-link>
              </li>
            </template>
            <li class="nav-small-cap">
              <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">Course Finder</span>
            </li>
            <li class="sidebar-item">
              <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/course-finder"
                aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-money-bill-wave fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Course Finder</span>
              </router-link>
            </li>
            <template v-if="auth.permissions.includes('view_enquiries')">
              <li class="nav-small-cap">
                <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
                <span class="hide-menu">Enquiry</span>
              </li>
              <li class="sidebar-item">
                <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/enquiries"
                  aria-expanded="false">
                  <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                    <i class="fas fa-comments fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">Enquiry list</span>
                </router-link>
              </li>
            </template>
            <template v-if="auth.isLoggedIn && auth.user.role">
              <li class="nav-small-cap">
                <i class="fas fa-home nav-small-cap-icon fs-5"></i>
                <span class="hide-menu">Notice</span>
              </li>
              <li class="sidebar-item">
                <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/notices" aria-expanded="false">
                  <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                    <i class="fas fa-exclamation-triangle fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">Notice</span>
                </router-link>
              </li>
            </template>
            <template v-if="auth.permissions.includes('view_data_entries')">
              <li class="nav-small-cap">
                <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
                <span class="hide-menu">University</span>
              </li>
              <li class="sidebar-item">
                <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/universitytable"
                  aria-expanded="false">
                  <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                    <i class="fas fa-list-ol fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">University Table</span>
                </router-link>
              </li>
              <li class="sidebar-item">
                <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/unicreate"
                  aria-expanded="false">
                  <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                    <i class="fas fa-plus-square fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">Add University</span>
                </router-link>
              </li>
            </template>
            <template v-if="auth.permissions.includes('view_finances')">
              <li class="nav-small-cap">
                <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
                <span class="hide-menu">Finance</span>
              </li>
              <li class="sidebar-item">
                <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/finance" aria-expanded="false">
                  <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                    <i class="fas fa-money-bill-wave fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">Finance Table</span>
                </router-link>
              </li>
              <li class="sidebar-item">
                <router-link class="sidebar-link sidebar-link primary-hover-bg" to="/app/commission"
                  aria-expanded="false">
                  <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                    <i class="fas fa-wallet fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">Add Commission</span>
                </router-link>
              </li>
            </template>
            <template
              v-if="['Administrator', 'Manager', 'Leads Manager', 'Applications Manager'].includes(auth.user.role)">
              <li class="nav-small-cap">
                <i class="fas fa-cog nav-small-cap-icon fs-5"></i>
                <span class="hide-menu">Settings</span>
              </li>

              <!-- MODIFIED: Add :class binding to the li -->
              <li class="sidebar-item" :class="{ 'active': isContentManagementOpen }">

                <!-- MODIFIED: Replace Bootstrap attributes with a Vue @click handler -->
                <a class="sidebar-link has-arrow indigo-hover-bg" href="#" @click.prevent="toggleContentManagement"
                  :aria-expanded="isContentManagementOpen">
                  <span class="aside-icon p-2 bg-indigo-subtle rounded-1">
                    <i class="fas fa-tools fs-6"></i>
                  </span>
                  <span class="hide-menu ps-1">Content Management</span>
                </a>

                <!-- MODIFIED: Remove the id and add a :class binding to the ul -->
                <ul aria-expanded="false" class="collapse first-level" :class="{ 'show': isContentManagementOpen }">
                  <li class="sidebar-item"><router-link to="/app/comment" class="sidebar-link"><span
                        class="sidebar-icon"></span><span class="hide-menu">Add Application Comment</span></router-link>
                  </li>
                  <li class="sidebar-item"><router-link to="/app/status" class="sidebar-link"><span
                        class="sidebar-icon"></span><span class="hide-menu">Add Status</span></router-link>
                  </li>
                  <li class="sidebar-item"><router-link to="/app/product" class="sidebar-link"><span
                        class="sidebar-icon"></span><span class="hide-menu">Add Products</span></router-link></li>
                  <li class="sidebar-item"><router-link to="/app/counselor" class="sidebar-link"><span
                        class="sidebar-icon"></span><span class="hide-menu">Add Counselor</span></router-link></li>
                  <li class="sidebar-item"><router-link to="/app/image" class="sidebar-link"><span
                        class="sidebar-icon"></span><span class="hide-menu">Add University Image</span></router-link>
                  </li>
                </ul>
              </li>
            </template>
          </ul>
        </nav>
      </div>
      <div class="fixed-profile mx-3 mt-1">
        <div class="card bg-primary-subtle mb-0 shadow-none">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div class="d-flex align-items-center gap-3">
                <img :src="auth.user.avatar" width="45" height="45" class="img-fluid rounded-circle" alt="user-avatar">
                <div>
                  <h4 class="mb-0 fs-3 fw-normal">{{ auth.user.name }} {{ auth.user.last }}</h4>
                  <span class="text-muted">{{ auth.user.role }}</span>
                </div>
              </div>
              <button @click="logout" class="btn border-0 p-0 position-relative" data-bs-toggle="tooltip"
                data-bs-placement="top" title="Logout">
                <iconify-icon icon="solar:logout-line-duotone" width="30" height="30"></iconify-icon>
              </button>
            </div>
          </div>
        </div>
      </div>
    </aside>

    <div class="page-wrapper">
      <div class="body-wrapper">
        <div class="container-fluid">
          <header class="topbar sticky-top">
            <div class="with-vertical">
              <nav class="navbar navbar-expand-lg p-0">
                <ul class="navbar-nav">
                  <li class="nav-item nav-icon-hover-bg rounded-circle">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)"
                      @click="toggleSidebar($event)">
                      <iconify-icon icon="solar:list-bold-duotone" class="fs-7">+</iconify-icon>
                    </a>
                  </li>
                </ul>
                <ul class="navbar-nav quick-links d-none d-lg-flex align-items-center">
                  <li class="nav-item dropdown-hover d-none d-lg-block me-2"><router-link class="nav-link"
                      to="/app/chat">Chat</router-link></li>
                  <li class="nav-item dropdown-hover d-none d-lg-block me-2"><router-link class="nav-link"
                      to="/app/calendar">Calendar</router-link></li>
                </ul>
                <div class="d-block d-lg-none py-3">
                  <img src="/img/wri.png" class="dark-logo" width="40" height="50" alt="Logo-Dark">
                </div>
                <a class="navbar-toggler p-0 border-0" href="javascript:void(0)" data-bs-toggle="collapse"
                  data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                  aria-label="Toggle navigation">
                  <span class="p-2"><i class="ti ti-dots fs-7"></i></span>
                </a>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                  <div class="d-flex align-items-center justify-content-between">
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                      <li class="nav-item nav-icon-hover-bg rounded-circle">
                        <a class="nav-link theme-toggle-btn" href="javascript:void(0)" @click="toggleTheme($event)"
                          title="Toggle Theme">
                          <iconify-icon icon="solar:moon-line-duotone" class="moon fs-6" v-show="!isDarkMode"
                            key="moon"></iconify-icon>
                          <iconify-icon icon="solar:sun-2-line-duotone" class="sun fs-6" v-show="isDarkMode"
                            key="sun"></iconify-icon>
                        </a>
                      </li>
                      <li class="nav-item dropdown nav-icon-hover-bg rounded-circle notification-container">
                        <a class="nav-link position-relative" href="javascript:void(0)" id="notificationDropdown"
                          aria-expanded="false" @click="toggleNotifications($event)">
                          <iconify-icon icon="solar:bell-line-duotone" class="fs-6"></iconify-icon>
                          <span id="notificationCount" class="notification-badge"
                            :class="{ 'has-notifications': unreadNotificationCount > 0 }">
                            {{ unreadNotificationCount > 99 ? '99+' : unreadNotificationCount }}
                          </span>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                          :class="{ show: showNotifications }" aria-labelledby="notificationDropdown">
                          <div class="d-flex align-items-center justify-content-between py-3 px-4">
                            <h5 class="mb-0 fs-5">Notifications</h5>
                            <a v-if="unreadNotificationCount > 0" href="javascript:void(0)"
                              class="text-muted mark-all-read" @click.prevent="handleMarkAllRead">Mark all read</a>
                          </div>
                          <div class="message-body" data-simplebar="" id="notificationList">
                            <div v-if="isLoadingNotifications" class="text-center p-3">
                              <p>Loading...</p>
                            </div>
                            <div v-else-if="notifications.length === 0" class="text-center py-3 text-muted">No
                              notifications
                            </div>
                            <div v-else>
                              <a v-for="notification in notifications" :key="notification.id" href="javascript:void(0)"
                                @click.prevent="handleNotificationClick(notification)"
                                class="dropdown-item d-flex align-items-center py-6 notification-item"
                                :class="{ 'unread': !notification.read }" :data-id="notification.id">
                                <span class="flex-shrink-0 notification-icon"><i
                                    :class="getIconForNotification(notification.type)" class="fs-5"></i></span>
                                <div class="w-100 ps-3">
                                  <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 fs-3 fw-normal">{{ notification.title || 'Notification' }}</h5>
                                    <span class="fs-2 text-nowrap d-block text-muted">{{ notification.created_at
                                    }}</span>
                                  </div>
                                  <span class="fs-2 d-block mt-1 text-muted">{{ notification.message }}</span>
                                </div>
                              </a>
                            </div>
                          </div>
                        </div>
                      </li>
                      <li class="nav-item dropdown nav-icon-hover-bg rounded-circle">
                        <a class="nav-link position-relative" href="javascript:void(0)" id="reminderDropdown"
                          aria-expanded="false" @click="toggleReminders($event)">
                          <iconify-icon icon="solar:alarm-line-duotone" class="fs-6"></iconify-icon>
                          <span v-if="reminderCount > 0"
                            class="badge1 bg-success rounded-pill position-absolute top-2 start-100 translate-middle">{{
                              reminderCount }}</span>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                          :class="{ show: showReminders }" aria-labelledby="reminderDropdown">
                          <div class="d-flex align-items-center justify-content-between py-3 px-4">
                            <h5 class="mb-0 fs-5">Reminders</h5>
                          </div>
                          <div class="message-body" data-simplebar="">
                            <div v-if="isLoadingReminders" class="text-center p-3">
                              <p>Loading...</p>
                            </div>
                            <div v-else-if="overdueReminders.length === 0" class="text-center py-3 text-muted">
                              No pending reminders
                            </div>
                            <div v-else>
                              <div v-for="reminder in overdueReminders" :key="reminder.id"
                                class="dropdown-item d-flex align-items-center py-6 notification-item">
                                <span class="flex-shrink-0 notification-icon">
                                  <i class="fas fa-business-time fs-5"></i>
                                </span>
                                <div class="w-100 ps-3">
                                  <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 fs-3 fw-normal">{{ reminder.lead_name }}</h5>
                                    <button class="btn btn-sm btn-outline-primary"
                                      @click="handleCompleteReminder(reminder.id)">Done</button>
                                  </div>
                                  <p class="fs-2 d-block mt-1 mb-1 text-muted">{{ reminder.comment }}</p>
                                  <span class="fs-2 d-block text-muted">{{ reminder.date_time }}</span>
                                  <span class="fs-2 d-block text-muted">{{ reminder.time_ago }}</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </li>
                      <li class="nav-item dropdown">
                        <a class="nav-link position-relative ms-6" href="javascript:void(0)" id="userDropdown"
                          aria-expanded="false" @click="toggleUserDropdown($event)">
                          <div class="d-flex align-items-center flex-shrink-0">
                            <div class="user-profile me-sm-3 me-2">
                              <img :src="auth.user.avatar" width="40" class="rounded-circle"
                                :alt="auth.user.name + 's avatar'">
                            </div>
                            <span class="d-sm-none d-block"><i class="fas fa-chevron-down"></i></span>
                            <div class="d-none d-sm-block">
                              <h6 class="fs-4 mb-1 profile-name">{{ auth.user.name }} {{ auth.user.last }}</h6>
                              <p class="fs-3 lh-base mb-0 profile-subtext">{{ auth.user.role }}</p>
                            </div>
                          </div>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                          :class="{ show: showUserDropdown }" aria-labelledby="userDropdown">
                          <div class="profile-dropdown position-relative" data-simplebar="">
                            <div class="d-flex align-items-center justify-content-between pt-3 px-7">
                              <h3 class="mb-0 fs-5">User Profile</h3>
                            </div>
                            <div class="d-flex align-items-center mx-7 py-9 border-bottom">
                              <img :src="auth.user.avatar" alt="user" width="90" class="rounded-circle">
                              <div class="ms-4">
                                <h4 class="mb-0 fs-5 fw-normal">{{ auth.user.name }}</h4>
                                <span class="text-muted">{{ auth.user.role }}</span>
                                <p class="text-muted mb-0 mt-1 d-flex align-items-center">
                                  <i class="fas fa-envelope fs-4 me-1"></i>
                                  {{ auth.user.email }}
                                </p>
                              </div>
                            </div>
                            <div class="message-body">
                              <router-link to="/app/user-profile"
                                class="dropdown-item px-7 d-flex align-items-center py-6">
                                <span class="btn px-3 py-2 bg-info-subtle rounded-1 text-info shadow-none"><i
                                    class="fas fa-wallet fs-7"></i></span>
                                <div class="w-100 ps-3 ms-1">
                                  <h5 class="mb-0 mt-1 fs-4 fw-normal">My Profile</h5><span
                                    class="fs-3 d-block mt-1 text-muted">Account Settings</span>
                                </div>
                              </router-link>
                              <template v-if="auth.permissions.includes('view_users')">
                                <router-link to="/app/user-list"
                                  class="dropdown-item px-7 d-flex align-items-center py-6">
                                  <span class="btn px-3 py-2 bg-success-subtle rounded-1 text-success shadow-none"><i
                                      class="fas fa-shield-alt fs-7"></i></span>
                                  <div class="w-100 ps-3 ms-1">
                                    <h5 class="mb-0 mt-1 fs-4 fw-normal">Manage Users</h5><span
                                      class="fs-3 d-block mt-1 text-muted">User Administration</span>
                                  </div>
                                </router-link>
                                <router-link to="/app/partners"
                                  class="dropdown-item px-7 d-flex align-items-center py-6">
                                  <span class="btn px-3 py-2 bg-success-subtle rounded-1 text-success shadow-none"><i
                                      class="fas fa-handshake fs-7"></i></span>
                                  <div class="w-100 ps-3 ms-1">
                                    <h5 class="mb-0 mt-1 fs-4 fw-normal">Manage Partners</h5><span
                                      class="fs-3 d-block mt-1 text-muted">B2B Agents</span>
                                  </div>
                                </router-link>
                              </template>
                            </div>
                            <div class="py-6 px-7 mb-1">
                              <button @click="logout" class="btn btn-primary w-100">Log Out</button>
                            </div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </nav>
            </div>
          </header>

          <div id="app">
            <router-view></router-view>
          </div>
        </div>
      </div>
      <!-- =================================================== -->
      <!-- KILL FEED NOTIFICATION AREA                         -->
      <!-- =================================================== -->
      <div id="killFeedContainer" class="position-fixed end-0 p-3" style="z-index: 1100; top: 5%;">
        <transition-group name="kill-feed-animation" tag="div">
          <KillFeedToast v-for="toast in toastsWithActiveState" :key="toast.id" :reminder="toast"
            :is-active="toast.isActive" :timeout="TOAST_DISPLAY_TIME" @timeUp="handleToastTimeUp" />
        </transition-group>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, provide, computed, nextTick, inject } from 'vue';
import { RouterView, RouterLink, useRouter } from 'vue-router';
import KillFeedToast from './components/layout/ReminderToast.vue';
import { formatDistanceToNow } from 'date-fns';
import eventBus from './utils/eventBus';

const router = useRouter();
const isLoading = ref(true);
const isSidebarVisible = ref(false);
const isDarkMode = ref(false);
const auth = ref({
  isLoggedIn: false,
  user: { name: 'Guest', last: '', email: '', avatar: '/assets/images/profile/user-1.jpg', role: '' },
  permissions: []
});
provide('auth', auth);


const showNotifications = ref(false);
const showReminders = ref(false);
const showUserDropdown = ref(false);
const notifications = ref([]);
const isLoadingNotifications = ref(false);
let notificationInterval = null;
const unreadNotificationCount = computed(() => notifications.value.filter(n => !n.read).length);

// ============================================================
// FINAL TWO-SLOT REMINDER FEED LOGIC
// ============================================================
const allServerReminders = ref([]);
const isLoadingReminders = ref(false);
const SESSION_STORAGE_KEY = 'processed_toast_ids';

const reminderQueue = ref([]);
const slot1Toast = ref(null);
const slot2Toast = ref(null);
const processedToastIds = ref(new Set(JSON.parse(sessionStorage.getItem(SESSION_STORAGE_KEY) || '[]')));

let reminderPollTimer = null;
let lastToastTimeoutId = null;

// Constants controlling the timing
const POLLING_INTERVAL = 10000;
const TOAST_DISPLAY_TIME = 5000;
const ANIMATION_DURATION = 500;
const INITIAL_PROMOTION_DELAY = 1000;

const isFastFillMode = ref(true);
const isContentManagementOpen = ref(false);

function toggleContentManagement() {
  isContentManagementOpen.value = !isContentManagementOpen.value;
}

const toastsWithActiveState = computed(() => {
  const toasts = [];
  if (slot1Toast.value) {
    // The top slot is visually present but not "active" with a progress bar.
    // Its lifetime is controlled by the logic below.
    toasts.push({ ...slot1Toast.value, isActive: false });
  }
  if (slot2Toast.value) {
    // The bottom slot is always "active" with a running progress bar.
    toasts.push({ ...slot2Toast.value, isActive: true });
  }
  return toasts;
});



// --- Core Two-Slot Logic Functions ---

async function runReminderPolling() {
  try {
    if (auth.value.isLoggedIn) {
      await fetchReminders();
    }
  } catch (error) {
    console.error("Polling for reminders failed:", error);
  } finally {
    clearTimeout(reminderPollTimer);
    reminderPollTimer = setTimeout(runReminderPolling, POLLING_INTERVAL);
  }
}

async function fetchReminders() {
  if (!auth.value.isLoggedIn) return;
  isLoadingReminders.value = true;
  try {
    const response = await fetch('/api/reminders');
    if (!response.ok) throw new Error('Failed to fetch reminders');
    const fetched = await response.json();
    allServerReminders.value = fetched;
    queueDueReminders(fetched);
  } catch (error) {
    console.error(error);
  } finally {
    isLoadingReminders.value = false;
  }
}

function queueDueReminders(fetchedReminders) {
  const now = new Date();
  fetchedReminders.forEach(reminder => {
    const isDue = new Date(reminder.date_time) <= now;
    const isAlreadyProcessed = processedToastIds.value.has(reminder.id);
    const isAlreadyInQueue = reminderQueue.value.some(r => r.id === reminder.id);
    const isAlreadyInSlots = reminder.id === slot1Toast.value?.id || reminder.id === slot2Toast.value?.id;
    const shouldQueue = isDue && !reminder.is_completed && !isAlreadyProcessed && !isAlreadyInQueue && !isAlreadyInSlots && reminder.lead_id;

    if (shouldQueue) {
      reminderQueue.value.push(reminder);
      playNotificationSound();
    }
  });
  processQueue();
}

function processQueue() {
  if (isFastFillMode.value && !slot1Toast.value && !slot2Toast.value && reminderQueue.value.length > 0) {
    performInitialFastFill();
  }
  else if (!slot2Toast.value && reminderQueue.value.length > 0) {
    slot2Toast.value = reminderQueue.value.shift();
  }
}

async function performInitialFastFill() {
  if (reminderQueue.value.length === 0) return;

  isFastFillMode.value = false;

  // --- First Toast ---
  const firstToast = reminderQueue.value.shift();
  slot2Toast.value = firstToast;

  await new Promise(resolve => setTimeout(resolve, INITIAL_PROMOTION_DELAY));

  if (!slot2Toast.value || slot2Toast.value.id !== firstToast.id) {
    if (!slot1Toast.value) isFastFillMode.value = true;
    processQueue();
    return;
  }

  const promotedToast = slot2Toast.value;
  slot1Toast.value = promotedToast;
  slot2Toast.value = null;

  // --- Second Toast ---
  await new Promise(resolve => setTimeout(resolve, ANIMATION_DURATION));

  processQueue(); // Attempt to fill slot 2 with the next item.

  // [FIX] If slot 2 is still empty, it means this was the only toast.
  // We must now set a timer for it to disappear from slot 1.
  if (!slot2Toast.value) {
    setFinalToastTimer(promotedToast);
  }
}

function handleToastTimeUp(toastId) {
  if (!slot2Toast.value || slot2Toast.value.id !== toastId) {
    return;
  }

  clearTimeout(lastToastTimeoutId);

  if (slot1Toast.value) {
    processedToastIds.value.add(slot1Toast.value.id);
    sessionStorage.setItem(SESSION_STORAGE_KEY, JSON.stringify(Array.from(processedToastIds.value)));
  }
  const promotedToast = slot2Toast.value;
  slot1Toast.value = promotedToast;
  slot2Toast.value = null;

  setTimeout(() => {
    processQueue();
    // [MODIFIED] If slot 2 is empty after processing the queue, it means the
    // promoted toast is the last one on screen. Set its timer.
    if (!slot2Toast.value) {
      setFinalToastTimer(promotedToast);
    }
  }, ANIMATION_DURATION);
}

// [NEW] Helper function to handle the removal of the final toast from slot 1.
// This is called from both the normal and fast-fill paths.
function setFinalToastTimer(toast) {
  lastToastTimeoutId = setTimeout(() => {
    if (slot1Toast.value && slot1Toast.value.id === toast.id) {
      processedToastIds.value.add(slot1Toast.value.id);
      sessionStorage.setItem(SESSION_STORAGE_KEY, JSON.stringify(Array.from(processedToastIds.value)));
      slot1Toast.value = null;
      // The screen is now empty, reset fast-fill mode for the next batch.
      isFastFillMode.value = true;
      // Check queue in case something arrived while waiting.
      processQueue();
    }
  }, TOAST_DISPLAY_TIME);
}

function dismissToast(reminderId) {
  processedToastIds.value.add(reminderId);
  sessionStorage.setItem(SESSION_STORAGE_KEY, JSON.stringify(Array.from(processedToastIds.value)));

  if (slot1Toast.value?.id === reminderId) {
    clearTimeout(lastToastTimeoutId);
    slot1Toast.value = null;
  }
  if (slot2Toast.value?.id === reminderId) {
    slot2Toast.value = null;
  }

  nextTick(() => {
    if (!slot1Toast.value && !slot2Toast.value) {
      isFastFillMode.value = true;
    }
    processQueue();
  });
}

// --- The rest of the script is unchanged ---

async function handleCompleteReminder(reminderId) {
  try {
    dismissToast(reminderId);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    await fetch(`/api/reminders/${reminderId}/complete`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    await fetchReminders();
  } catch (error) {
    console.error('Failed to complete reminder:', error);
  }
}

const overdueReminders = computed(() => {
  const now = new Date();
  return allServerReminders.value
    .filter(r => new Date(r.date_time) <= now && !r.is_completed && r.lead_id)
    .sort((a, b) => new Date(b.date_time) - new Date(a.date_time))
    .map(reminder => ({ ...reminder, time_ago: formatDistanceToNow(new Date(reminder.date_time), { addSuffix: true }) }));
});

const reminderCount = computed(() => overdueReminders.value.length);

onMounted(async () => {
  isLoading.value = true;
  await fetchAuthUser();
  applyTheme(localStorage.getItem('theme') || 'light');
  document.addEventListener('click', handleClickOutside);

  if (auth.value.isLoggedIn) {
    fetchNotifications();
    notificationInterval = setInterval(fetchNotifications, 30000);
    runReminderPolling();
  }
  isLoading.value = false;
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  clearInterval(notificationInterval);
  if (reminderPollTimer) clearTimeout(reminderPollTimer);
  if (lastToastTimeoutId) clearTimeout(lastToastTimeoutId);
});

async function fetchAuthUser() {
  try {
    const response = await fetch('/api/auth/user');
    // ... your existing logic ...
    const data = await response.json();
    auth.value = {
      isLoggedIn: true,
      user: { ...data.user, avatar: data.user.avatar || '/assets/images/profile/user-1.jpg' },
      permissions: data.permissions || []
    };
    
    // *** NEW: EMIT THE EVENT AFTER DATA IS LOADED ***
    eventBus.emit('auth-loaded', auth.value);

  } catch (error) {
    console.error("Authentication check failed:", error);
    auth.value.isLoggedIn = false;
    // You might want to emit a default state on failure too
    eventBus.emit('auth-loaded', auth.value); 
  }
}

function toggleSidebar() {
  isSidebarVisible.value = !isSidebarVisible.value;
}

function applyTheme(mode) {
  document.documentElement.setAttribute('data-bs-theme', mode);
  document.body.classList.toggle('dark-layout', mode === 'dark');
  isDarkMode.value = mode === 'dark';
  if (typeof localStorage !== 'undefined') {
    localStorage.setItem('theme', mode);
  }
}

async function toggleTheme(event) {
  event.preventDefault();
  event.stopPropagation();
  const newTheme = isDarkMode.value ? 'light' : 'dark';
  await nextTick(() => applyTheme(newTheme));
}

async function logout(event) {
  event.preventDefault();
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
      console.error('CSRF token not found!');
      window.location.href = '/login';
      return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/logout';
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    form.submit();
  } catch (error) {
    console.error('An unexpected error occurred during logout:', error);
    window.location.href = '/login';
  }
}

async function fetchNotifications() {
  if (!auth.value.isLoggedIn) return;
  isLoadingNotifications.value = true;
  try {
    const response = await fetch('/api/notifications');
    if (!response.ok) throw new Error('Failed to fetch notifications');
    notifications.value = await response.json();
  } catch (error) {
    console.error(error);
  } finally {
    isLoadingNotifications.value = false;
  }
}

function toggleNotifications(event) {
  event.preventDefault();
  event.stopPropagation();
  showNotifications.value = !showNotifications.value;
  showReminders.value = false;
  showUserDropdown.value = false;
  if (showNotifications.value) {
    fetchNotifications();
  }
}

async function handleNotificationClick(notification) {
  if (!notification.read) {
    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      await fetch(`/api/notifications/${notification.id}/mark-as-read`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      });
      const foundNotification = notifications.value.find(n => n.id === notification.id);
      if (foundNotification) foundNotification.read = true;
    } catch (error) {
      console.error('Failed to mark notification as read:', error);
    }
  }
  if (notification.link) router.push(notification.link);
  showNotifications.value = false;
}

async function handleMarkAllRead() {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    await fetch('/api/notifications/mark-all-as-read', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    notifications.value.forEach(n => n.read = true);
  } catch (error) {
    console.error('Failed to mark all notifications as read:', error);
  }
}

function getIconForNotification(type) {
  const icons = {
    comment: 'fas fa-comment-dots',
    mention: 'fas fa-at',
    notice: 'fas fa-bullhorn',
    document_forwarded: 'fas fa-file-export',
    reminder: 'fas fa-clock',
  };
  return icons[type] || 'fas fa-bell';
}

function toggleReminders(event) {
  event.preventDefault();
  event.stopPropagation();
  showReminders.value = !showReminders.value;
  showNotifications.value = false;
  showUserDropdown.value = false;
  if (showReminders.value) {
    fetchReminders();
  }
}

function toggleUserDropdown(event) {
  event.preventDefault();
  event.stopPropagation();
  showUserDropdown.value = !showUserDropdown.value;
  showNotifications.value = false;
  showReminders.value = false;
}

function playNotificationSound() {
  try {
    const audio = new Audio('/assets/sounds/notification.mp3');
    audio.play().catch(e => console.warn("Audio play failed:", e));
  } catch (error) {
    console.error('Error playing notification sound:', error);
  }
}

function handleClickOutside(event) {
  const target = event.target;
  if (target.closest('.notification-container') || target.closest('[aria-labelledby="reminderDropdown"]') || target.closest('[aria-labelledby="userDropdown"]')) {
    return;
  }
  showNotifications.value = false;
  showReminders.value = false;
  showUserDropdown.value = false;
}
</script>

<style>
.badge1 {
  position: absolute;
  top: 25px;
  right: 5px;
  background-color: #28a745 !important;
  color: white;
  border-radius: 50%;
  font-size: 10px;
  min-width: 16px;
  height: 16px;
  line-height: 16px;
  text-align: center;
  box-shadow: 0 0 2px rgba(0, 0, 0, 0.3);
  border: 2px solid white;
  box-sizing: content-box;
}

.notification-badge {
  position: absolute;
  top: 16px;
  right: -7px;
  background-color: #dc3545;
  color: white;
  border-radius: 50%;
  font-size: 10px;
  min-width: 16px;
  height: 16px;
  line-height: 16px;
  text-align: center;
  display: none;
  box-shadow: 0 0 2px rgba(0, 0, 0, 0.3);
  border: 2px solid white;
}

.notification-badge.has-notifications {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.notification-item {
  cursor: pointer;
  transition: background-color 0.2s;
}

html[data-bs-theme="light"] .notification-item:hover {
  background-color: #f8f9fa;
}

html[data-bs-theme="light"] .notification-item.unread {
  background-color: #f1f9ff;
}

html[data-bs-theme="dark"] .notification-item:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

html[data-bs-theme="dark"] .notification-item.unread {
  background-color: rgba(46, 134, 222, 0.15);
}

.notification-icon {
  margin-right: 12px;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

html[data-bs-theme="light"] .notification-icon {
  background-color: #e9ecef;
  color: #6c757d;
}

html[data-bs-theme="dark"] .notification-icon {
  background-color: #343a40;
  color: #adb5bd;
}

html[data-bs-theme="light"] .notification-item.unread .notification-icon {
  background-color: #2e86de;
  color: white;
}

html[data-bs-theme="dark"] .notification-item.unread .notification-icon {
  background-color: #2e86de;
  color: white;
}

/* ======================================================== */
/* NEW TWO-SLOT KILL FEED ANIMATION STYLES                  */
/* ======================================================== */
#killFeedContainer {
  pointer-events: none;
  /* Container doesn't block clicks */
  width: 350px;
  max-width: 90vw;
  height: 220px;
  /* Fixed height to contain 2 slots + gap */
}

/* The direct child of transition-group is the tag we specified ('div') */
#killFeedContainer>div {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  /* Toasts appear at the bottom */
  height: 100%;
  gap: 12px;
}

/* All direct children of the transition-group's container div are the toasts */
#killFeedContainer>div>* {
  pointer-events: auto;
  /* Toasts are clickable */
  will-change: transform, opacity;
  position: relative;
  /* Needed for smooth transitions */
}


/* 
 * ENTER ANIMATION (For new toasts entering Slot 2)
 */
.kill-feed-animation-enter-active {
  transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

.kill-feed-animation-enter-from {
  opacity: 0;
  transform: translateX(110%) rotate(3deg);
}


/* 
 * MOVE ANIMATION (The magic for Slot 2 → Slot 1)
 * This is automatically applied by Vue when an element's order changes.
 */
.kill-feed-animation-move {
  transition: transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
}


/* 
 * LEAVE ANIMATION (For toasts being dismissed from Slot 1)
 */
.kill-feed-animation-leave-active {
  transition: all 0.4s cubic-bezier(0.55, 0, 1, 0.45);
  /* CRITICAL: Allows the leaving item to not disrupt the layout */
  position: absolute;
  width: 100%;
}

.kill-feed-animation-leave-to {
  opacity: 0;
  transform: translateX(110%) rotate(-3deg) scale(0.95);
}

/*
  =============================================================
  == ROBUST VUE-CONTROLLED SIDEBAR DROPDOWN OVERRIDE
  =============================================================
  This fixes conflicts with themes that use 'display: none'
  for their collapse functionality.
*/

/*
 * 1. Force the submenu to always be in the layout (not display:none).
 *    The `!important` is necessary here to beat the theme's specificity.
 *    We then use max-height and opacity to control visibility.
*/
.sidebar-nav .sidebar-item .collapse.first-level {
  display: block !important;
  overflow: hidden !important;
  max-height: 0 !important;
  opacity: 0 !important;
  transition: max-height 0.35s ease-in-out, opacity 0.3s ease-in-out !important;
  padding: 0 !important; /* Reset padding to avoid layout jumps */
  margin: 0 !important;  /* Reset margin */
}

/*
 * 2. When Vue adds the `.show` class, we animate it into view.
*/
.sidebar-nav .sidebar-item .collapse.first-level.show {
  max-height: 500px !important; /* A safe large value to accommodate all items */
  opacity: 1 !important;
}
/*
 * 4. Ensure the arrow icon rotates correctly when the menu is open.
 *    The `.active` class is added by Vue to the parent <li>.
*/
.sidebar-item.active > .sidebar-link.has-arrow::after {
  transform: rotate(-135deg) !important;
  /* Fine-tune vertical alignment if needed */
}

</style>