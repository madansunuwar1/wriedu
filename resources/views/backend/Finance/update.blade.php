@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
    <div class="card">
        <div class="border-bottom title-part-padding">
            <h4 class="card-title mb-0">Update Finance Details</h4>
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

            <form action="{{ route('backend.Finance.update', $finances->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                
                <!-- University and Country -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="university">University Name</label>
                        <input type="text" class="form-control" id="university" name="university" 
                               value="{{ old('university', $finances->university) }}" required>
                        <div class="invalid-feedback">Please provide university name.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="country">Country</label>
                        <input type="text" class="form-control" id="country" name="country" 
                               value="{{ old('country', $finances->country) }}" required>
                        <div class="invalid-feedback">Please provide country.</div>
                    </div>
                </div>

                <!-- Commission Details -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="commission">Standard Commission</label>
                        <input type="text" class="form-control" id="commission" name="commission" 
                               value="{{ old('commission', $finances->commission) }}" required>
                        <div class="invalid-feedback">Please provide standard commission.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="promotional">Promotional Tiered Commission</label>
                        <input type="text" class="form-control" id="promotional" name="promotional" 
                               value="{{ old('promotional', $finances->promotional) }}" required>
                        <div class="invalid-feedback">Please provide promotional commission.</div>
                    </div>
                </div>

                <!-- Tuition and Comment -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="tuition">Tuition Fees</label>
                        <input type="text" class="form-control" id="tuition" name="tuition" 
                               value="{{ old('tuition', $finances->tuition) }}" required>
                        <div class="invalid-feedback">Please provide tuition fees.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="comment">Comment</label>
                        <input type="text" class="form-control" id="comment" name="comment" 
                               value="{{ old('comment', $finances->comment) }}" required>
                        <div class="invalid-feedback">Please provide comment.</div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button class="btn btn-primary" type="submit">
                        <i class="ti ti-device-floppy me-1"></i> Update Finance Details
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