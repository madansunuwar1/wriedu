@extends('layouts.admin')

@section('title', 'Import Partners')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Import Partners</h1>
            <a href="{{ route('backend.partners.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Partners
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-upload me-2"></i>Upload Partners File
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.partners.process-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Select File</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                       id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Supported formats: Excel (.xlsx, .xls) and CSV (.csv). Maximum file size: 10MB
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Import Notes:</h6>
                                    <ul class="mb-0">
                                        <li>If a partner with the same <strong>Agency Name</strong> and <strong>Address</strong> exists, the record will be updated</li>
                                        <li>New partners will be created if no matching Agency Name and Address combination is found</li>
                                        <li>Email addresses must be unique across all partners</li>
                                        <li>All fields are required: Agency Name, Email, Contact No, Counselor Email, Address</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Import Partners
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-download me-2"></i>Sample Template
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>Download the sample template to ensure your file has the correct format:</p>
                        
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size: 0.8em;">Required Columns</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td><code>agency_name</code></td></tr>
                                    <tr><td><code>email</code></td></tr>
                                    <tr><td><code>contact_no</code></td></tr>
                                    <tr><td><code>agency_counselor_email</code></td></tr>
                                    <tr><td><code>address</code></td></tr>
                                </tbody>
                            </table>
                        </div>

                        <a href="{{ route('backend.partners.download-template') }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-download me-2"></i>Download Sample Template
                        </a>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-question-circle me-2"></i>Help
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6>File Format Requirements:</h6>
                        <ul class="small">
                            <li>First row must contain column headers</li>
                            <li>Use exact column names as shown in template</li>
                            <li>All email addresses must be valid</li>
                            <li>Contact numbers should be in text format</li>
                        </ul>

                        <h6>Import Process:</h6>
                        <ul class="small">
                            <li>Duplicate checking based on Agency Name + Address</li>
                            <li>Email uniqueness validation</li>
                            <li>Automatic updates for existing records</li>
                            <li>Detailed import results provided</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            alert('File size must be less than 10MB');
            e.target.value = '';
        }
    }
});
</script>
@endsection