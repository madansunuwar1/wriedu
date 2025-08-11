@extends('layouts.admin')

@section('content')
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Import Raw Leads</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('backend.leadform.rawlead') }}">Raw Leads</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Import</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            @if ($errors->any() && !$errors->has('file') && !session('import_errors'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

             @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
             @endif
             @if (session('error'))
             <div class="alert alert-danger alert-dismissible fade show" role="alert">
                 {{ session('error') }}
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
             </div>
             @endif
             @if (session('warning'))
             <div class="alert alert-warning alert-dismissible fade show" role="alert">
                 {{ session('warning') }}
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
             @endif
             @if (session('info'))
             <div class="alert alert-info alert-dismissible fade show" role="alert">
                 {{ session('info') }}
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
             @endif


            <div class="card card-box mb-30">
                <div class="card-header">
                    <h5 class="card-title pt-2">Upload File for Import</h5>
                </div>
                <div class="card-body">
                    <p>Upload a CSV or Excel file (<code>.csv</code>, <code>.xlsx</code>, <code>.xls</code>) containing raw leads.</p>
                    <p><strong>File Format Instructions:</strong></p>
                    <ul>
                        <li>The first row must be the header row.</li>
                        <li>Header names are case-insensitive (e.g., 'Name' is the same as 'name').</li>
                        <li>
                            <strong>Required Columns (Must be present in header):</strong>
                            <ul>
                                <li><code>Name</code></li>
                                <li><code>Email</code></li>
                                <li><code>Phone</code></li>
                            </ul>
                        </li>
                        <li>
                            <strong>Optional Columns (Include if needed):</strong>
                            <ul>
                                <li><code>Status</code> (Allowed values: {{ implode(', ', $allowedStatuses ?? ['new', 'contacted', 'etc.']) }}. If omitted or empty, defaults to 'new'.)</li>
                                <li><code>AD ID</code></li>
                                <li><code>Country Preference</code></li>
                                <li><code>Subject Preference</code></li>
                                <li><code>Applying For</code></li>
                                <li><code>Assigned User Email</code> (Must be the email of an active user in the system)</li>
                                <li><code>Initial Comment</code> (Will be added as the first comment for the lead)</li>
                            </ul>
                        </li>
                        <li>Extra columns in the file will be ignored.</li>
                        <li>Leads will be matched by <strong>Email</strong> first, then <strong>Phone</strong>. If a match is found, the existing lead will be updated. Otherwise, a new lead is created.</li>
                    </ul>

                    @if (session('import_errors') && is_array(session('import_errors')))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Import Errors Encountered:</strong>
                        <ul>
                            @foreach (session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif


                    <form action="{{ route('rawleadform.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="lead_file" class="form-label">Select Lead File</label>
                          <input class="form-control @error('file') is-invalid @enderror" type="file" id="lead_file" name="file" required accept=".csv, .xlsx, .xls">
                            <small class="form-text text-muted">Max file size: 10MB.</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-upload me-1"></i> Upload and Start Import
                            </button>
                            <a href="{{ route('backend.leadform.rawlead') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
</style>
@endpush

@push('scripts')
<script>
</script>
@endpush