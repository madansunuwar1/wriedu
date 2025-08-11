@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<div class="card">
  <div class="border-bottom title-part-padding">
    <h4 class="card-title mb-0">Update User Information</h4>
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

    <form id="userForm" action="{{ route('backend.user.update', $users->id) }}" method="POST" class="needs-validation" novalidate>
      @csrf
      @method('PUT')
      
      <!-- Name section -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="name">First Name</label>
          <input type="text" class="form-control" id="name" name="name" 
                 placeholder="Enter first name" value="{{ old('name', $users->name) }}" required>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide first name.</div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="last">Last Name</label>
          <input type="text" class="form-control" id="last" name="last" 
                 placeholder="Enter last name" value="{{ old('last', $users->last) }}" required>
          <div class="invalid-feedback">Please provide last name.</div>
        </div>
      </div>

      <!-- Email section -->
      <div class="row">
        <div class="col-md-12 mb-3">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" class="form-control" id="email" name="email" 
                 placeholder="Enter email address" value="{{ old('email', $users->email) }}" required>
          <div class="invalid-feedback">Please provide a valid email.</div>
        </div>
      </div>

      <!-- Application section -->
      <div class="row">
        <div class="col-md-12 mb-3">
          <label class="form-label" for="application">Application</label>
          <input type="text" class="form-control" id="application" name="application" 
                 placeholder="Enter application" value="{{ old('application', $users->application) }}">
        </div>
      </div>

      <!-- Submit button -->
      <div class="text-center mt-4">
        <button class="btn btn-success" type="submit">
          <i class="ti ti-check me-1"></i> Update User
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
      var form = document.getElementById('userForm');
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

<style>
  .card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  .title-part-padding {
    padding: 1.5rem 1.5rem 0.75rem;
  }
  .card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
  }
  .border-bottom {
    border-bottom: 1px solid #e2e8f0 !important;
  }
  .card-body {
    padding: 1.5rem;
  }
  .form-label {
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 0.5rem;
  }
  .form-control {
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }
  .form-control:focus {
    border-color: #48bb78;
    box-shadow: 0 0 0 0.25rem rgba(72, 187, 120, 0.25);
  }
  .btn-success {
    background-color: #48bb78;
    border-color: #48bb78;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
  }
  .btn-success:hover {
    background-color: #38a169;
    border-color: #38a169;
  }
  .invalid-feedback {
    color: #e53e3e;
    font-size: 0.875rem;
  }
  .valid-feedback {
    color: #48bb78;
    font-size: 0.875rem;
  }
</style>
@endsection