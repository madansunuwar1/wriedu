<template>
    <div class="main-container">
        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-9 col-xl-9">
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Document Management</div>
                    </div>
                </div>
            </div>

            <!-- This section is now identical to the original blade file's structure -->
            <div class="flex items-center justify-center my-4">
                <div class="bg-white rounded-lg w-1/3 p-6 shadow-lg">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">Add New Document</h2>
                    </div>

                    <div class="card p-2 mt-3">
                        <form @submit.prevent="storeDocument">
                            <div class="mb-3">
                                <label for="document" class="form-label">Enter Document</label>
                                <input type="text" v-model="newDocument.document" id="document" class="form-control"
                                    placeholder="Enter your document" required>
                            </div>

                            <div class="mb-3">
                                <label for="country" class="form-label">Select Country</label>
                                <select v-model="newDocument.country" id="country" class="form-control" required>
                                    <option value="" disabled>Select a country</option>
                                    <option v-for="entry in data_entries" :key="entry.country" :value="entry.country">{{
                                        entry.country }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Select Document Status</label>
                                <select v-model="newDocument.status" id="status" class="form-control" required>
                                    <option value="" disabled>Select an Application</option>
                                    <option value="lead">Lead</option>
                                    <option value="application">Application</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle" id="documentTable">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">SN</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Document</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Country</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Application</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Actions</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody ref="tableBody">
                        <tr v-if="loading">
                            <td colspan="4" class="text-center py-4">Loading...</td>
                        </tr>
                        <tr v-else v-for="(document, index) in documents" :key="document.id" :data-index="index"
                            draggable="true" @dragstart="handleDragStart($event, index)"
                            @dragover="handleDragOver($event)" @dragenter="handleDragEnter($event)"
                            @drop="handleDrop($event, index)" @dragend="handleDragEnd" class="draggable-row" :class="{
                                'dragging': draggedIndex === index,
                                'drag-over': dragOverIndex === index
                            }" style="cursor: move;">
                                  <td>
                                {{ index + 1 }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-grip-vertical fs-5 text-muted me-2" style="cursor: grab;"></i>
                                    {{ document.document }}
                                </div>
                            </td>
                            <td>{{ document.country }}</td>
                            <td>{{ document.status }}</td>
                            <td>
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-6"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <button @click="destroyDocument(document.id)"
                                                class="dropdown-item d-flex align-items-center gap-3 text-danger">
                                                <i class="fs-4 ti ti-trash"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!loading && documents.length === 0">
                            <td colspan="4" class="text-center py-4">No documents found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const documents = ref([]);
const data_entries = ref([]);
const loading = ref(true);
const draggedIndex = ref(null);
const dragOverIndex = ref(null);

const newDocument = ref({
    document: '',
    country: '',
    status: ''
});

const fetchDocuments = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/documents');
        const sortedDocuments = response.data.documents.sort((a, b) => a.id - b.id);
        documents.value = sortedDocuments;
        data_entries.value = response.data.data_entries;
    } catch (error) {
        console.error('Error fetching documents:', error);
        showErrorAlert('Failed to load data.');
    } finally {
        loading.value = false;
    }
};

const storeDocument = async () => {
    try {
        const response = await axios.post('/api/documents', newDocument.value);
        documents.value.unshift(response.data.document);
        resetForm();
        showSuccessAlert(response.data.message);
    } catch (error) {
        console.error('Error saving document:', error);
        const errorMessage = error.response?.data?.message || 'Failed to save document.';
        showErrorAlert(errorMessage);
    }
};

const destroyDocument = async (id) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2e7d32',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.delete(`/api/documents/${id}`);
            documents.value = documents.value.filter(doc => doc.id !== id);
            showSuccessAlert(response.data.message);
        } catch (error) {
            console.error('Error deleting document:', error);
            showErrorAlert('Failed to delete document.');
        }
    }
};

// Drag and drop functionality
const handleDragStart = (event, index) => {
    draggedIndex.value = index;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', index);

    // Add visual feedback to the dragged element
    setTimeout(() => {
        event.target.style.opacity = '0.5';
    }, 0);
};

const handleDragOver = (event) => {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
};

const handleDragEnter = (event) => {
    event.preventDefault();
    const row = event.currentTarget;
    const index = parseInt(row.dataset.index);
    dragOverIndex.value = index;
};

const handleDrop = async (event, dropIndex) => {
    event.preventDefault();
    dragOverIndex.value = null;

    if (draggedIndex.value === null || draggedIndex.value === dropIndex) {
        return;
    }

    const dragIndex = draggedIndex.value;

    // Create a copy of the array
    const newDocuments = [...documents.value];

    // Remove the dragged item
    const [draggedItem] = newDocuments.splice(dragIndex, 1);

    // Insert at new position
    newDocuments.splice(dropIndex, 0, draggedItem);

    // Update the documents array
    documents.value = newDocuments;

    // Update database IDs based on new positions (1, 2, 3, etc.)
    await updateDatabaseIds();
};

const handleDragEnd = (event) => {
    event.target.style.opacity = '1';
    draggedIndex.value = null;
    dragOverIndex.value = null;
};

const updateDatabaseIds = async () => {
    try {


        // Create update data with new sequential IDs (1, 2, 3, etc.)
        const updateData = documents.value.map((doc, index) => ({
            currentId: doc.id,
            newId: index + 1,
            document: doc.document,
            country: doc.country,
            status: doc.status
        }));

        // Call API to update all IDs in database
        const response = await axios.put('/api/documents/update-ids', {
            documents: updateData
        });

        // Update local documents with new IDs
        documents.value = documents.value.map((doc, index) => ({
            ...doc,
            id: index + 1
        }));

        loadingAlert.close();
        showSuccessAlert('Document order updated successfully!');

    } catch (error) {

        await fetchDocuments();
    }
};

const resetForm = () => {
    newDocument.value.document = '';
    newDocument.value.country = '';
    newDocument.value.status = '';
};

const showSuccessAlert = (message) => {
    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'swal-custom-popup',
            confirmButton: 'swal-custom-ok-button'
        }
    });
};

const showErrorAlert = (message) => {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
};

onMounted(() => {
    fetchDocuments();
});
</script>

<style scoped>
.draggable-row {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.draggable-row:hover {
    background-color: #f8f9fa;
    cursor: move;
}

.draggable-row.dragging {
    opacity: 0.5;
    transform: scale(1.02);
    background-color: #e3f2fd;
    border: 2px dashed #2196f3;
}

.draggable-row.drag-over {
    background-color: #fff3e0;
    border: 2px solid #ff9800;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.ti-grip-vertical {
    transition: color 0.2s ease;
}

.draggable-row:hover .ti-grip-vertical {
    color: #2196f3 !important;
}

/* Smooth animations for reordering */
tbody tr {
    transition: transform 0.2s ease;
}
</style>