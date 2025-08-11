@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<div class="card">
  <div class="border-bottom title-part-padding">
    <h4 class="card-title mb-0">Finance Information</h4>
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

    <form id="financeForm" action="{{ route('backend.Finance.store') }}" method="POST" class="needs-validation" novalidate>
      @csrf
      
      <!-- University and Country section -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="university">University Name</label>
          <input type="text" class="form-control" id="university" name="university" 
                 placeholder="Enter university name" value="{{ old('university') }}" required>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide university name.</div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="country">Country</label>
          <input type="text" class="form-control" id="country" name="country" 
                 placeholder="Enter country" value="{{ old('country') }}" required>
          <div class="invalid-feedback">Please provide country.</div>
        </div>
      </div>

      <!-- Commission section -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="commission">Standard Commission</label>
          <input type="text" class="form-control" id="commission" name="commission" 
                 placeholder="Enter commission" value="{{ old('commission') }}" required>
          <div class="invalid-feedback">Please provide standard commission.</div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="promotional">Promotional Tiered Commission</label>
          <input type="text" class="form-control" id="promotional" name="promotional" 
                 placeholder="Enter promotional commission" value="{{ old('promotional') }}">
        </div>
      </div>

      <!-- Tuition and Comment section -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="tuition">Tuition Fees</label>
          <input type="text" class="form-control" id="tuition" name="tuition" 
                 placeholder="Enter tuition fees" value="{{ old('tuition') }}" required>
          <div class="invalid-feedback">Please provide tuition fees.</div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="comment">Comment</label>
          <input type="text" class="form-control" id="comment" name="comment" 
                 placeholder="Enter any comments" value="{{ old('comment') }}">
        </div>
      </div>

      <!-- Submit button -->
      <div class="text-center mt-4">
        <button class="btn btn-primary" type="submit">
          <i class="ti ti-device-floppy me-1"></i> Save Finance Data
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
      var form = document.getElementById('financeForm');
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