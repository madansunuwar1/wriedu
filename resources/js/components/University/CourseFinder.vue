<template>
  <div class="container-fluid mt-4">
    <h1 class="mb-4">Course Finder</h1>

    <!-- Filters Card -->
    <div class="card mb-4 shadow-sm">
      <div class="card-body">
        <div class="row g-3">
          <!-- Filter Selectors - NOW SEPARATED -->
          <div class="col-md-4 col-lg-2 filter-section">
            <label class="form-label fw-bold">Country</label>
            <TomSelect v-model="selected.country" :options="countryOptions" :settings="tomSelectSettings"
              placeholder="Select Country..." multiple />
          </div>
          <div class="col-md-4 col-lg-2 filter-section">
            <label class="form-label fw-bold">Location</label>
            <TomSelect v-model="selected.location" :options="locationOptions" :settings="tomSelectSettings"
              placeholder="Select Location..." multiple />
          </div>
          <div class="col-md-4 col-lg-2 filter-section">
            <label class="form-label fw-bold">University</label>
            <TomSelect v-model="selected.university" :options="universityOptions" :settings="tomSelectSettings"
              placeholder="Select University..." multiple />
          </div>
          <div class="col-md-4 col-lg-2 filter-section">
            <label class="form-label fw-bold">Course</label>
            <TomSelect v-model="selected.course" :options="courseOptions" :settings="tomSelectSettings"
              placeholder="Select Course..." multiple />
          </div>
          <div class="col-md-4 col-lg-2 filter-section">
            <label class="form-label fw-bold">Intake</label>
            <TomSelect v-model="selected.intake" :options="intakeOptions" :settings="tomSelectSettings"
              placeholder="Select Intake..." multiple />
          </div>

          <!-- Pass Year Selector (was already separate) -->
          <div class="col-md-4 col-lg-2 filter-section">
            <label class="form-label fw-bold">Pass Year</label>
            <TomSelect v-model="selected.passYear" :options="passYearOptions"
              :settings="{ ...tomSelectSettings, multiple: false, allowEmptyOption: true }"
              placeholder="Any Pass Year..." />
          </div>

          <!-- Clear Button -->
          <div class="col-md-4 col-lg-2 filter-section d-flex align-items-end">
            <button @click="clearFilters" class="btn btn-outline-secondary w-100">
              <i class="fas fa-rotate-left me-1"></i> Clear Filters
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
      <p class="mt-2 text-muted">Finding Courses...</p>
    </div>

    <!-- Course Cards -->
    <div v-else class="entries-container row">
      <div v-for="entry in filteredEntries" :key="entry.id" class="col-sm-6 col-lg-4 col-xl-3 mb-4">
        <div class="card overflow-hidden hover-img h-100 shadow-sm course-card">
          <!-- Card Header with Images -->
          <div class="position-relative">
            <img :src="getUniversityInfo(entry.newUniversity, 'background_image')" class="card-img-top card-background"
              :alt="`${entry.newUniversity} background`">
            <span
              class="badge text-bg-light fs-2 lh-sm mb-9 me-9 py-1 px-2 fw-semibold position-absolute bottom-0 end-0">
              {{ entry.newIntake || 'N/A' }}
            </span>
            <div class="university-logo-container">
              <img :src="getUniversityInfo(entry.newUniversity, 'image_link')" :alt="`${entry.newUniversity} logo`"
                class="img-fluid rounded-circle university-logo" v-tooltip="entry.newUniversity || 'N/A'">
            </div>
          </div>

          <!-- Card Body -->
          <div class="card-body p-4 d-flex flex-column">
            <div class="mt-4">
              <span class="badge text-bg-info fs-2 py-1 px-2 lh-sm mb-2">{{ entry.country || 'N/A' }}</span>
              <router-link :to="{ name: 'universit.profile', params: { id: entry.id } }"
                class="d-block my-2 fs-5 text-dark fw-semibold link-primary" v-tooltip="entry.newCourse">
                {{ entry.newCourse || 'N/A Course' }}
              </router-link>
              <div class="fs-6 text-muted mb-3">{{ entry.newUniversity || 'N/A University' }}</div>
              <div class="course-stats mb-3">
                <div class="d-flex align-items-center gap-2"><i class="fas fa-map-marker-alt fa-fw"></i>{{
                  entry.newLocation || 'N/A' }}</div>
                <div class="d-flex align-items-center gap-2"><i class="fas fa-money-bill-wave fa-fw"></i>{{
                  entry.newAmount || 'N/A' }}</div>
                <div class="d-flex align-items-center gap-2"><i class="fas fa-graduation-cap fa-fw"></i>{{
                  entry.newScholarship || 'N/A' }}</div>
              </div>
            </div>
            <div class="mt-auto pt-2">
              <router-link :to="{ name: 'universit.profile', params: { id: entry.id } }"
                class="btn btn-primary w-100">View Details</router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- No Results Message -->
      <div v-if="!filteredEntries.length && !isLoading" class="col-12">
        <div class="text-center py-5">
          <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
          <p class="text-muted fs-4 mt-2">No course entries found matching your criteria.</p>
          <p class="text-muted">Try adjusting or clearing your filters.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import TomSelect from '@/components/layout/TomSelect.vue';

// --- STATE ---
const isLoading = ref(true);
const allEntries = ref([]);
const universityProfiles = ref({});
const universityImages = ref({});

const selected = reactive({
  country: [], location: [], university: [],
  course: [], intake: [], passYear: '',
});

const tomSelectSettings = {
  plugins: ['remove_button'],
  create: false,
  sortField: { field: 'text', direction: 'asc' },
};

// --- API FETCH ---
onMounted(async () => {
  try {
    const { data } = await axios.get('/api/course-finder-data');
    allEntries.value = data.data_entries.map(entry => ({
      ...entry,
      ugGap: parseGapValue(entry.newug),
      pgGap: parseGapValue(entry.newpg),
      level: (entry.level || 'both').toLowerCase(),
      searchableIntakes: (entry.newIntake || '').split(',').map(s => s.trim()).filter(Boolean),
    }));
    universityProfiles.value = data.universities || {};
    universityImages.value = data.images || {};
  } catch (error) {
    console.error("Failed to fetch course finder data:", error);
  } finally {
    isLoading.value = false;
  }
});

// --- FILTERING LOGIC ---

// Helper to generate unique options for dropdowns
const getUniqueOptions = (source, key, isFlatMap = false) => {
  if (!source || !source.length) return [];
  const values = isFlatMap
    ? [...new Set(source.flatMap(entry => entry[key] || []))]
    : [...new Set(source.map(entry => entry[key]))];
  return values.filter(Boolean).sort().map(val => ({ value: val, text: val }));
};

// **NEW: Granular computed properties for each filter's options**
// This is the key to fixing the double-click issue.

// Country options only depend on the master list and never change after load.
const countryOptions = computed(() => getUniqueOptions(allEntries.value, 'country'));

// Location options depend on the master list and the selected countries.
const locationOptions = computed(() => {
  const source = selected.country.length
    ? allEntries.value.filter(e => selected.country.includes(e.country))
    : allEntries.value;
  return getUniqueOptions(source, 'newLocation');
});

// University options depend on the selections above it.
const universityOptions = computed(() => {
  let source = allEntries.value;
  if (selected.country.length) {
    source = source.filter(e => selected.country.includes(e.country));
  }
  if (selected.location.length) {
    source = source.filter(e => selected.location.includes(e.newLocation));
  }
  return getUniqueOptions(source, 'newUniversity');
});

// Course options depend on the selections above it.
const courseOptions = computed(() => {
  let source = allEntries.value;
  if (selected.country.length) source = source.filter(e => selected.country.includes(e.country));
  if (selected.location.length) source = source.filter(e => selected.location.includes(e.newLocation));
  if (selected.university.length) source = source.filter(e => selected.university.includes(e.newUniversity));
  return getUniqueOptions(source, 'newCourse');
});

// Intake options depend on the selections above it.
const intakeOptions = computed(() => {
  let source = allEntries.value;
  if (selected.country.length) source = source.filter(e => selected.country.includes(e.country));
  if (selected.location.length) source = source.filter(e => selected.location.includes(e.newLocation));
  if (selected.university.length) source = source.filter(e => selected.university.includes(e.newUniversity));
  if (selected.course.length) source = source.filter(e => selected.course.includes(e.newCourse));
  return getUniqueOptions(source, 'searchableIntakes', true);
});

// Pass Year options are static.
const passYearOptions = computed(() => {
  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: 20 }, (_, i) => ({ value: String(currentYear - i), text: String(currentYear - i) }));
  return years;
});

// The final filtered list of results to display.
const filteredEntries = computed(() => {
  if (isLoading.value) return [];
  return allEntries.value.filter(entry => {
    return (!selected.country.length || selected.country.includes(entry.country))
      && (!selected.location.length || selected.location.includes(entry.newLocation))
      && (!selected.university.length || selected.university.includes(entry.newUniversity))
      && (!selected.course.length || selected.course.includes(entry.newCourse))
      && (!selected.intake.length || selected.intake.some(sel => entry.searchableIntakes.includes(sel)))
      && checkPassYear(entry);
  });
});

const checkPassYear = (entry) => {
  if (!selected.passYear) return true;
  const passYearNum = parseInt(selected.passYear, 10);
  const gap = new Date().getFullYear() - passYearNum;
  if (gap < 0) return false;
  const ugAcceptable = gap <= entry.ugGap;
  const pgAcceptable = gap <= entry.pgGap;
  switch (entry.level) {
    case 'undergraduate': return ugAcceptable;
    case 'postgraduate': return pgAcceptable;
    default: return ugAcceptable || pgAcceptable;
  }
};

const clearFilters = () => {
  Object.keys(selected).forEach(key => {
    selected[key] = Array.isArray(selected[key]) ? [] : '';
  });
};

// --- HELPERS --- (No changes below this line)
const parseGapValue = (gapString) => {
  if (gapString === null || String(gapString).trim() === '') return Infinity;
  const trimmedLower = String(gapString).toLowerCase().trim();
  if (['n/a', 'na', 'unlimited', 'no limit', 'any gap', 'flexible'].some(kw => trimmedLower.includes(kw))) return Infinity;
  const match = trimmedLower.match(/\d+/);
  return match ? parseInt(match[0], 10) : Infinity;
};

const getUniversityInfo = (uniName, prop) => {
  const universityNameKey = String(uniName || '').trim();

  const getFullImageUrl = (path) => {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    const baseUrl = window.APP_URL || '';
    const cleanPath = path.replace(/^public\//, '');
    return `${baseUrl}/storage/${cleanPath}`;
  };

  const profile = universityProfiles.value[universityNameKey];
  const legacyImage = universityImages.value[universityNameKey];

  if (prop === 'background_image') {
    const imageUrl = getFullImageUrl(profile?.background_image) || getFullImageUrl(legacyImage?.image);
    return imageUrl || `https://placehold.co/400x150/4e73df/FFFFFF/png?text=${encodeURIComponent(universityNameKey || 'University')}`;
  }

  if (prop === 'image_link') {
    const imageUrl = getFullImageUrl(profile?.image_link);
    const initial = (universityNameKey || 'U').charAt(0).toUpperCase();
    return imageUrl || `https://placehold.co/60x60/E9ECEF/6C757D/png?text=${initial}`;
  }

  return '';
};

const vTooltip = {
  mounted(el, binding) {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
      el.bsTooltip = new bootstrap.Tooltip(el, { title: binding.value, placement: 'top', trigger: 'hover' });
    }
  },
  unmounted(el) {
    if (el.bsTooltip) {
      el.bsTooltip.dispose();
    }
  }
};
</script>

<style scoped>
/* Component-specific styles */
.filter-section .form-label {
  font-size: 0.85rem;
  margin-bottom: 0.5rem;
  color: #495057;
}

.course-card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  border: 1px solid #e3e6f0;
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card-background {
  height: 150px;
  object-fit: cover;
}

.university-logo-container {
  position: absolute;
  bottom: -30px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 60px;
  background-color: white;
  border-radius: 50%;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border: 2px solid white;
}

.university-logo {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.course-stats {
  font-size: 0.9rem;
  color: #555;
}

.course-stats div {
  margin-bottom: 5px;
}

.course-stats i {
  margin-right: 8px;
  color: #0d6efd;
  width: 16px;
  text-align: center;
}

.card-body {
  margin-top: 20px;
}

.link-primary {
  text-decoration: none;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.link-primary:hover {
  color: #0a58ca !important;
  text-decoration: underline !important;
}
</style>