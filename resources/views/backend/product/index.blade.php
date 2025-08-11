@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<div class="main-container">
    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-9 col-xl-9">
                    <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Product Management</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Form -->
    <div class="card mb-4">
        <div class="border-bottom title-part-padding">
            <h4 class="card-title mb-0">Add Product</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form id="productForm" action="{{ route('backend.product.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label" for="product">Product Name</label>
                        <input type="text" class="form-control" id="product" name="product" placeholder="Enter product name" required>
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

    @if (session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>
    @endif

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
                @foreach ($products as $product)
                <tr>
                    <td>{{ $product->product }}</td>
                    <td>
                        <div class="dropdown dropstart">
                            <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical fs-6"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('product.edit', $product->id) }}">
                                        <i class="fs-4 ti ti-edit"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('product.destroy', $product->id) }}" method="POST" onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-3 text-danger">
                                            <i class="fs-4 ti ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this product?');
    }
</script>

<script>
  (function() {
    'use strict';
    window.addEventListener('load', function() {
      var form = document.getElementById('productForm');
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    }, false);
  })();
</script>
@endsection