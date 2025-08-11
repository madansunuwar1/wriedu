@extends('layouts.admin')

@section('title', 'Create Partner')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <a href="{{ route('backend.partners.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h4 class="mb-0">Create New Partner</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('partners.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="agency_name" class="form-label">
                            Agency Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="agency_name" 
                               id="agency_name" 
                               class="form-control @error('agency_name') is-invalid @enderror"
                               value="{{ old('agency_name') }}"
                               placeholder="Enter agency name">
                        @error('agency_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="Address" class="form-label">
                            Address <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="Address" 
                               id="Address" 
                               class="form-control @error('Address') is-invalid @enderror"
                               value="{{ old('Address') }}"
                               placeholder="Enter Address">
                        @error('Address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="Enter email address">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contact_no" class="form-label">
                            Contact Number <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="contact_no" 
                               id="contact_no" 
                               class="form-control @error('contact_no') is-invalid @enderror"
                               value="{{ old('contact_no') }}"
                               placeholder="Enter contact number">
                        @error('contact_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="agency_counselor_email" class="form-label">
                            Agency Counselor Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               name="agency_counselor_email" 
                               id="agency_counselor_email" 
                               class="form-control @error('agency_counselor_email') is-invalid @enderror"
                               value="{{ old('agency_counselor_email') }}"
                               placeholder="Enter agency counselor email">
                        @error('agency_counselor_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('backend.partners.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Partner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection