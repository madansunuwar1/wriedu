@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<div class="card">
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
      
      <!-- Product Name Field -->
      <div class="row mb-3">
        <div class="col-md-12">
          <label class="form-label" for="product">Product Name</label>
          <input type="text" class="form-control" id="product" name="product" 
                 placeholder="Enter product name" required>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a product name.</div>
        </div>
      </div>

      <!-- Product Description Field -->


      <!-- Submit button -->
      <div class="text-center mt-4">
        <button class="btn btn-primary" type="submit">
          <i class="ti ti-plus me-1"></i> Add Product
        </button>
      </div>
    </form>
  </div>
</div>



<script>
  // Form validation
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