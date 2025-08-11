@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
    <div class="card">
        <div class="border-bottom title-part-padding">
            <h4 class="card-title mb-0">Update Product</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
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

            <form action="{{ route('backend.product.update', $products->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <!-- Product Input -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="product">Product Name</label>
                        <input type="text" class="form-control" id="product" name="product" 
                               value="{{ old('product', $products->product) }}" placeholder="Enter product name" required>
                        <div class="invalid-feedback">Please provide a product name.</div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button class="btn btn-primary" type="submit">
                        <i class="ti ti-device-floppy me-1"></i> Update Product
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
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
@endsection