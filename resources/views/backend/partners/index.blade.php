@extends('layouts.admin')

@section('title', 'Partners')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Partners</h1>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#downloadModal">
                    <i class="fas fa-download me-2"></i>Download Template
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-upload me-2"></i>Import Partners
                </button>
                <a href="{{ route('backend.partners.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Partner
                </a>
            </div>
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

        <div class="card">
            <div class="card-body">
                @if($partners->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Agency Name</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                    <th>Contact No.</th>
                                    <th>Counselor Email</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($partners as $partner)
                                    <tr>
                                        <td>
                                            <strong>{{ $partner->agency_name }}</strong>
                                        </td>
                                         <td>
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                            {{ $partner->Address }}
                                        </td>
                                        <td>
                                            <i class="fas fa-envelope text-muted me-1"></i>
                                            {{ $partner->email }}
                                        </td>
                                        <td>
                                            <i class="fas fa-phone text-muted me-1"></i>
                                            {{ $partner->contact_no }}
                                        </td>
                                        <td>
                                            <i class="fas fa-user text-muted me-1"></i>
                                            {{ $partner->agency_counselor_email }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('partners.show', $partner) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('backend.partners.edit', $partner) }}" 
                                                   class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('partners.destroy', $partner) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this partner?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $partners->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No partners found</h4>
                        <p class="text-muted">Get started by creating your first partner or importing from a file.</p>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#downloadModal">
                                <i class="fas fa-download me-2"></i>Download Template
                            </button>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-upload me-2"></i>Import Partners
                            </button>
                            <a href="{{ route('backend.partners.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create New Partner
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-upload me-2"></i>Import Partners
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('backend.partners.process-import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="import_file" class="form-label">Select File</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                       id="import_file" name="file" accept=".xlsx,.xls,.csv" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Supported formats: Excel (.xlsx, .xls) and CSV (.csv). Maximum file size: 10MB
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Import Rules:</h6>
                                <ul class="mb-0 small">
                                    <li>If a partner with the same <strong>Agency Name</strong> and <strong>Address</strong> exists, the record will be updated</li>
                                    <li>New partners will be created if no matching combination is found</li>
                                    <li>Email addresses must be unique across all partners</li>
                                    <li>All fields are required: Agency Name, Email, Contact No, Counselor Email, Address</li>
                                </ul>
                            </div>

                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Required Columns:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled small">
                                                <li><code>agency_name</code></li>
                                                <li><code>email</code></li>
                                                <li><code>contact_no</code></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled small">
                                                <li><code>agency_counselor_email</code></li>
                                                <li><code>address</code></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="importBtn">
                        <i class="fas fa-upload me-2"></i>Import Partners
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Download Template Modal -->
<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downloadModalLabel">
                    <i class="fas fa-download me-2"></i>Download Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Download the sample template to ensure your import file has the correct format.</p>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Template Guidelines:</h6>
                    <ul class="mb-0 small">
                        <li>First row must contain the exact column headers shown below</li>
                        <li>Do not modify the column names</li>
                        <li>All fields are required for each partner</li>
                        <li>Email addresses must be valid and unique</li>
                    </ul>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6>Template will include these columns:</h6>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small">Column Name</th>
                                            <th class="small">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>agency_name</code></td>
                                            <td class="small">Name of the agency</td>
                                        </tr>
                                        <tr>
                                            <td><code>email</code></td>
                                            <td class="small">Primary email address</td>
                                        </tr>
                                        <tr>
                                            <td><code>contact_no</code></td>
                                            <td class="small">Contact phone number</td>
                                        </tr>
                                        <tr>
                                            <td><code>agency_counselor_email</code></td>
                                            <td class="small">Counselor's email</td>
                                        </tr>
                                        <tr>
                                            <td><code>address</code></td>
                                            <td class="small">Full address</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <div class="btn-group">
                    <a href="{{ route('backend.partners.download-template', ['format' => 'csv']) }}" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-file-csv me-2"></i>Download CSV
                    </a>
                    <a href="{{ route('backend.partners.download-template', ['format' => 'xlsx']) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-file-excel me-2"></i>Download Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // File size validation for import
    document.getElementById('import_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const importBtn = document.getElementById('importBtn');
        
        if (file) {
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if (file.size > maxSize) {
                alert('File size must be less than 10MB');
                e.target.value = '';
                importBtn.disabled = true;
            } else {
                importBtn.disabled = false;
            }
        }
    });

    // Show loading state on import form submission
    document.getElementById('importForm').addEventListener('submit', function() {
        const importBtn = document.getElementById('importBtn');
        importBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importing...';
        importBtn.disabled = true;
    });

    // Auto-show import modal if there are validation errors
    @error('file')
        var importModal = new bootstrap.Modal(document.getElementById('importModal'));
        importModal.show();
    @enderror
});
</script>

@endsection