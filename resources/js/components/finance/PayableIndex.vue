<template>
    <div class="main-container">
        <div class="widget-content searchable-container list">
            <!-- Header & Actions -->
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-12 col-xl-12">
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Commission</div>
                    </div>
                    <div class="col-md-6 col-xl-8">
                        <div class="position-relative">
                            <input
                                type="text"
                                class="form-control product-search ps-5"
                                v-model="searchQuery"
                                placeholder="Search by University, Product, or Partner..."
                            />
                            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <button @click="openImportModal" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-upload text-white me-1 fs-5"></i> Import CSV
                        </button>
                    </div>
                    <div class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <a href="/api/commission-payable-export" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-download text-white me-1 fs-5"></i> Download Data
                        </a>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div v-if="isLoading" class="text-center my-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Data Table -->
            <div v-else class="table-responsive mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th>ID</th>
                            <th>University</th>
                            <th>Product</th>
                            <th>Partner</th>
                            <th>Commission</th>
                            <th>Has Bonus</th>
                            <th>Has Incentive</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="filteredCommissions.length === 0">
                            <td colspan="8" class="text-center py-4">No records found.</td>
                        </tr>
                        <tr v-for="commission in filteredCommissions" :key="commission.id">
                            <td>{{ commission.id }}</td>
                            <td>{{ commission.university }}</td>
                            <td>{{ commission.product }}</td>
                            <td>{{ commission.partner }}</td>
                            <td>{{ formatCommissionTypes(commission.commission_types) }}</td>
                            <td>{{ commission.has_bonus_commission ? 'Yes' : 'No' }}</td>
                            <td>{{ commission.has_incentive_commission ? 'Yes' : 'No' }}</td>
                            <td>
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-6"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <!-- Assuming you have a route/page for viewing a single record -->
                                            <a class="dropdown-item d-flex align-items-center gap-3" :href="`/app/payable/${commission.id}`">
                                                <i class="fs-4 ti ti-file-text"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3" href="#" @click.prevent="deleteCommission(commission.id)">
                                                <i class="fs-4 ti ti-trash text-danger"></i> <span class="text-danger">Delete</span>
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
import { ref, onMounted, computed } from 'vue';
import axios from 'axios'; // Make sure to install axios: npm install axios
import Swal from 'sweetalert2';

// Reactive State
const commissions = ref([]);
const isLoading = ref(true);
const searchQuery = ref('');

// Fetch data when the component is first mounted
onMounted(async () => {
    await fetchCommissions();
});

// Method to fetch data from the API
const fetchCommissions = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/commission-payable');
        commissions.value = response.data;
    } catch (error) {
        console.error('Error fetching commissions:', error);
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Failed to load commission data.',
        });
    } finally {
        isLoading.value = false;
    }
};

// Computed property to filter commissions based on the search query
const filteredCommissions = computed(() => {
    if (!searchQuery.value) {
        return commissions.value;
    }
    const lowerCaseQuery = searchQuery.value.toLowerCase();
    return commissions.value.filter(c =>
        c.university.toLowerCase().includes(lowerCaseQuery) ||
        c.product.toLowerCase().includes(lowerCaseQuery) ||
        c.partner.toLowerCase().includes(lowerCaseQuery)
    );
});

// Method to format the commission_types object/array for display
const formatCommissionTypes = (types) => {
    if (!types) return 'N/A';
    if (typeof types === 'string') return types; // Handles old string data
    if (typeof types === 'object') {
        return Object.entries(types)
            .map(([key, value]) => `${key} (${value})`)
            .join(', ');
    }
    return 'Invalid Data';
};

// Method to handle deletion
const deleteCommission = async (id) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/commission-payable/${id}`);
            // Remove the item from the local array for immediate UI update
            commissions.value = commissions.value.filter(c => c.id !== id);
            Swal.fire(
                'Deleted!',
                'The commission record has been deleted.',
                'success'
            );
        } catch (error) {
            console.error('Error deleting commission:', error);
            Swal.fire({
                icon: 'error',
                title: 'Deletion Failed',
                text: error.response?.data?.message || 'An unexpected error occurred.',
            });
        }
    }
};

// Method to open an import modal (you would need to create this modal)
const openImportModal = () => {
    // This is a placeholder. You would trigger a Bootstrap/custom modal here.
    Swal.fire({
      title: 'Import CSV',
      html: `<input type="file" id="csv-file-input" class="swal2-file" accept=".csv">`,
      showCancelButton: true,
      confirmButtonText: 'Upload',
      preConfirm: () => {
        const file = document.getElementById('csv-file-input').files[0];
        if (!file) {
          Swal.showValidationMessage(`Please select a CSV file`);
        }
        return file;
      }
    }).then(async (result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('csv_file', result.value);

            try {
                const response = await axios.post('/api/commission-payable-import', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                Swal.fire('Success', response.data.message, 'success');
                await fetchCommissions(); // Refresh data after import
            } catch (error) {
                Swal.fire('Error', error.response?.data?.message || 'Import failed', 'error');
            }
        }
    });
};
</script>

<style scoped>
/* You can add component-specific styles here */
.text-danger {
    color: #dc3545 !important;
}
</style>