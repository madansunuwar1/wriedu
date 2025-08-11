<template>
  <div class="main-container">
    <!-- Your existing HTML structure here -->
    <div class="widget-content searchable-container list">
      <div class="card card-body">
        <div class="row">
          <div class="col-md-9 col-xl-9">
            <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Product Management</div>
          </div>
        </div>
      </div>
    </div>

    <!-- START: Success Message -->
    <div v-if="showSuccessMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ successMessage }}
      <button type="button" class="btn-close" @click="showSuccessMessage = false" aria-label="Close"></button>
    </div>
    <!-- END: Success Message -->

    <!-- Add Product Form -->
    <div class="card mb-4">
      <div class="border-bottom title-part-padding">
        <h4 class="card-title mb-0">Add Product</h4>
      </div>
      <div class="card-body">
        <form id="productForm" @submit.prevent="addProduct" class="needs-validation" novalidate>
          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="product">Product Name</label>
              <input type="text" class="form-control" id="product" v-model="newProduct" placeholder="Enter product name" required>
              <div class="valid-feedback">Looks good!</div>
              <div class="invalid-feedback">Please provide a product name.</div>
            </div>
          </div>
          <div class="text-center mt-4">
            <button class="btn btn-primary" type="submit">
              <i class="ti ti-plus me-1"></i> Add Product
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Product Table -->
    <div class="table-responsive mb-4 border rounded-1">
      <table class="table text-nowrap mb-0 align-middle" id="productTable">
        <thead class="text-dark fs-4">
          <tr>
            <th><h6 class="fs-4 fw-semibold mb-0">Product</h6></th>
            <th><h6 class="fs-4 fw-semibold mb-0">Actions</h6></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.product }}</td>
            <td>
              <div class="dropdown dropstart">
                <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="ti ti-dots-vertical fs-6"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <li>
                    <a class="dropdown-item d-flex align-items-center gap-3" href="#" @click="editProduct(product.id)">
                      <i class="fs-4 ti ti-edit"></i> Edit
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item d-flex align-items-center gap-3 text-danger" href="#" @click="deleteProduct(product.id)">
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
</template>

<script>
export default {
  data() {
    return {
      products: [],
      newProduct: '',
      // START: New data properties for the success message
      showSuccessMessage: false,
      successMessage: ''
      // END: New data properties
    };
  },
  methods: {
    async fetchProducts() {
      const response = await fetch('/api/products');
      this.products = await response.json();
    },
    async addProduct() {
      // Get the form and check its validity for Bootstrap validation feedback
      const form = document.getElementById('productForm');
      if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
      }
      
      const response = await fetch('/api/products', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product: this.newProduct })
      });

      if (response.ok) {
        this.newProduct = ''; // Clear the input field
        form.classList.remove('was-validated'); // Reset form validation state
        await this.fetchProducts(); // Refresh the product list

        // START: Logic to show the success message
        this.successMessage = 'Product added successfully!';
        this.showSuccessMessage = true;

        // Automatically hide the message after 3 seconds
        setTimeout(() => {
          this.showSuccessMessage = false;
        }, 3000);
        // END: Logic to show the success message
      }
    },
    async editProduct(id) {
      // Implement edit logic
    },
    async deleteProduct(id) {
      const response = await fetch(`/api/products/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      });
      if (response.ok) {
        await this.fetchProducts();
      }
    }
  },
  mounted() {
    this.fetchProducts();
  }
};
</script>