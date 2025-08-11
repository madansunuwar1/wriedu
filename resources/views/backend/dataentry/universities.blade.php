@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<div class="card">
  <div class="border-bottom title-part-padding">
    <h4 class="card-title mb-0">Universities List</h4>
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

    <!-- Form to add/edit a university -->
    <div class="card mb-4" @if(isset($editUniversity) && $editUniversity->background_image) style="background-image: url('{{ $editUniversity->background_image }}'); background-size: cover; background-position: center; background-blend-mode: lighten; position: relative;" @endif>
      @if(isset($editUniversity) && $editUniversity->background_image)
      <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255, 255, 255, 0.85); z-index: 0;"></div>
      @endif
      <div class="card-header" style="position: relative; z-index: 1;">
        @if(isset($editUniversity))
          <h5 class="mb-0">Edit University</h5>
        @else
          <h5 class="mb-0">Add New University</h5>
        @endif
      </div>
      <div class="card-body" style="position: relative; z-index: 1;">
        @if(isset($editUniversity))
          <form id="universityForm" action="{{ route('universities.update', $editUniversity->id) }}" method="POST" class="needs-validation" novalidate>
            @method('PUT')
        @else
          <form id="universityForm" action="{{ route('universities.store') }}" method="POST" class="needs-validation" novalidate>
        @endif
          @csrf
          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="name">University Name</label>
              <input type="text" class="form-control" id="name" name="name" 
                    value="{{ isset($editUniversity) ? $editUniversity->name : old('name') }}" 
                    placeholder="Enter university name" required>
              <div class="valid-feedback">Looks good!</div>
              <div class="invalid-feedback">Please provide a university name.</div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="image_link">University Logo URL</label>
              <input type="url" class="form-control" id="image_link" name="image_link" 
                    value="{{ isset($editUniversity) ? $editUniversity->image_link : old('image_link') }}" 
                    placeholder="Enter logo URL" required>
              <div class="valid-feedback">Looks good!</div>
              <div class="invalid-feedback">Please provide a valid image URL.</div>
              @if(isset($editUniversity) && $editUniversity->image_link)
                <div class="mt-2">
                  <img src="{{ $editUniversity->image_link }}" alt="Logo Preview" style="max-width: 100px; height: auto;">
                </div>
              @endif
            </div>
            <div class="col-md-6">
              <label class="form-label" for="background_image">Background Image URL</label>
              <input type="url" class="form-control" id="background_image" name="background_image" 
                    value="{{ isset($editUniversity) ? $editUniversity->background_image : old('background_image') }}" 
                    placeholder="Enter background image URL">
              <div class="valid-feedback">Looks good!</div>
              <div class="invalid-feedback">Please provide a valid image URL.</div>
              @if(isset($editUniversity) && $editUniversity->background_image)
                <div class="mt-2">
                  <img src="{{ $editUniversity->background_image }}" alt="Background Preview" style="max-width: 100px; height: auto;">
                </div>
              @endif
            </div>
          </div>
          <div class="text-center mt-4">
            <button class="btn btn-primary" type="submit">
              <i class="ti ti-{{ isset($editUniversity) ? 'pencil' : 'plus' }} me-1"></i> 
              {{ isset($editUniversity) ? 'Update' : 'Add' }} University
            </button>
            @if(isset($editUniversity))
              <a href="{{ route('universities.index') }}" class="btn btn-secondary">
                <i class="ti ti-x me-1"></i> Cancel
              </a>
            @endif
          </div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>University Name</th>
            <th>Logo</th>
            <th>Background</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($universities as $university)
          <tr style="{{ $university->background_image ? 'background-image: url('.$university->background_image.'); background-size: cover; background-position: center; background-blend-mode: overlay; background-color: rgba(255, 255, 255, 0.9);' : '' }}">
            <td>{{ $university->id }}</td>
            <td>{{ $university->name }}</td>
            <td>
              <img src="{{ $university->image_link }}" alt="{{ $university->name }}" style="max-width: 100px; height: auto;">
            </td>
            <td>
              @if($university->background_image)
                <img src="{{ $university->background_image }}" alt="Background for {{ $university->name }}" style="max-width: 100px; height: auto;">
              @else
                <span class="text-muted">No background</span>
              @endif
            </td>
            <td>
              <a href="{{ route('universities.edit', $university->id) }}" class="btn btn-sm btn-warning">
                <i class="ti ti-pencil"></i> Edit
              </a>
              <form action="{{ route('universities.destroy', $university->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this university?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                  <i class="ti ti-trash"></i> Delete
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  // Form validation
  (function() {
    'use strict';
    window.addEventListener('load', function() {
      var form = document.getElementById('universityForm');
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