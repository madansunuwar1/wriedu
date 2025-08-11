@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<div class="card">
  <div class="border-bottom title-part-padding">
    <h4 class="card-title mb-0">Create Notice</h4>
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

    <form id="noticeForm" action="{{ route('backend.notice.store') }}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
      @csrf
      
      <!-- Title Field -->
      <div class="row mb-3">
        <div class="col-md-12">
          <label class="form-label" for="title">Notice Title</label>
          <input type="text" class="form-control" id="title" name="title" 
                 placeholder="Enter notice title" value="{{ old('title') }}" required>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a notice title.</div>
        </div>
      </div>

      <!-- Description Textarea -->
      <div class="row mb-3">
        <div class="col-md-12">
          <label class="form-label" for="description">Notice Description</label>
          <textarea class="form-control" id="description" name="description" 
                    placeholder="Enter notice description here..." rows="5" required>{{ old('description') }}</textarea>
          <div class="invalid-feedback">
            Please enter the notice description.
          </div>
        </div>
      </div>

      <!-- Image Upload -->
      <div class="row mb-3">
        <div class="col-md-12">
          <label class="form-label" for="image">Notice Image</label>
          <div class="custom-file">
            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            <div class="invalid-feedback">
              Please select a valid image file.
            </div>
            <small class="text-muted">Accepted formats: JPG, PNG, GIF (Max 2MB)</small>
          </div>
        </div>
      </div>

      <!-- Submit button -->
      <div class="text-center mt-4">
        <button class="btn btn-primary" type="submit">
          <i class="ti ti-send me-1"></i> Publish Notice
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
      var form = document.getElementById('noticeForm');
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    }, false);
  })();

  // Image validation
  document.getElementById('image').addEventListener('change', function(e) {
    const fileInput = e.target;
    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
    const maxSize = 2 * 1024 * 1024; // 2MB
    
    if (fileInput.files.length > 0) {
      const file = fileInput.files[0];
      
      if (!validTypes.includes(file.type)) {
        fileInput.setCustomValidity('Invalid file type. Please upload JPG, PNG or GIF.');
      } else if (file.size > maxSize) {
        fileInput.setCustomValidity('Image size exceeds 2MB limit.');
      } else {
        fileInput.setCustomValidity('');
      }
    }
  });
</script>
@endsection