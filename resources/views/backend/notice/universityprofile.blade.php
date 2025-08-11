@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')

<div class="position-relative overflow-hidden">
    <div class="card mx-9">
        <div class="card-body pb-0">
            <div class="d-md-flex align-items-center justify-content-between text-center text-md-start">
                <div class="d-md-flex align-items-center">
                    <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
                    <img src="{{ $matchedUniversity ? $matchedUniversity->image_link : asset('assets/images/backgrounds/bg1.jpg') }}"
                             alt="{{ $university->newUniversity }}"
                             class="img-fluid rounded-circle" {{-- Keep these classes for circular style --}}
                             width="100" height="100" style="object-fit: cover;">
                    </div>
                    <div class="ms-0 ms-md-3 mb-9 mb-md-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-1">
                            <h4 class="me-7 mb-0 fs-7">{{ $university->newUniversity }}</h4>
                            <span class="badge fs-2 fw-bold rounded-pill bg-success-subtle text-success border-success border">
                                Active
                            </span>
                        </div>
                        <p class="fs-4 mb-1 d-flex align-items-center justify-content-center justify-content-md-start text-muted">
                            <i class="ti ti-map-pin me-1 fs-5"></i> {{ $university->newLocation }}, {{ $university->country }}
                        </p>
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start mt-2 gap-2">
                             <span class="badge bg-primary-subtle text-primary">
                                {{ $university->newCourse }}
                             </span>
                            <span class="badge bg-info-subtle text-info">
                                {{ $university->newIntake }} Intake
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 mt-md-0 d-flex justify-content-center gap-2">
                    <button class="btn btn-outline-primary shadow-none px-3">
                       <i class="ti ti-pencil me-1"></i> Edit
                    </button>
                    <button class="btn btn-primary shadow-none px-3">
                        <i class="ti ti-plus me-1"></i> Add Application
                    </button>
                </div>
            </div>

            <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start" id="pills-tab" role="tablist">
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true">
                        <i class="fas fa-info-circle me-0 me-md-2 fs-6"></i>
                        <span class="d-none d-md-block">Profile</span>
                    </button>
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-financial-tab" data-bs-toggle="pill" data-bs-target="#pills-financial" type="button" role="tab" aria-controls="pills-financial" aria-selected="false">
                        <i class="fas fa-coins me-0 me-md-2 fs-6"></i>
                        <span class="d-none d-md-block">Financial</span>
                    </button>
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-language-tab" data-bs-toggle="pill" data-bs-target="#pills-language" type="button" role="tab" aria-controls="pills-language" aria-selected="false">
                        <i class="fas fa-language me-0 me-md-2 fs-6"></i>
                        <span class="d-none d-md-block">Language</span>
                    </button>
                </li>
                 <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-academic-tab" data-bs-toggle="pill" data-bs-target="#pills-academic" type="button" role="tab" aria-controls="pills-academic" aria-selected="false">
                        <i class="fas fa-graduation-cap me-0 me-md-2 fs-6"></i>
                        <span class="d-none d-md-block">Academic</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-documents-tab" data-bs-toggle="pill" data-bs-target="#pills-documents" type="button" role="tab" aria-controls="pills-documents" aria-selected="false">
                         <i class="fas fa-file-alt me-0 me-md-2 fs-6"></i>
                        <span class="d-none d-md-block">Documents</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="tab-content mx-10 mt-4" id="pills-tabContent">

    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
         <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                 <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-info-circle me-2 text-primary"></i> Basic Information
                 </h5>
            </div>
            <div class="card-body p-4">
                 <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 120px;"><i class="fas fa-university me-2 text-muted"></i> University:</span>
                    <span class="fw-medium">{{ $university->newUniversity }}</span>
                 </div>
                 <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 120px;"><i class="fas fa-book me-2 text-muted"></i> Course:</span>
                    <span class="fw-medium">{{ $university->newCourse }}</span>
                 </div>
                 <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 120px;"><i class="fas fa-globe-americas me-2 text-muted"></i> Country:</span>
                    <span class="fw-medium">{{ $university->country }}</span>
                 </div>
                  <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 120px;"><i class="fas fa-map-marker-alt me-2 text-muted"></i> Location:</span>
                    <span class="fw-medium">{{ $university->newLocation }}</span>
                 </div>
                 <div class="d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 120px;"><i class="fas fa-calendar-alt me-2 text-muted"></i> Intake:</span>
                    <span class="fw-medium">{{ $university->newIntake }}</span>
                 </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pills-financial" role="tabpanel" aria-labelledby="pills-financial-tab" tabindex="0">
        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                 <h5 class="mb-0 d-flex align-items-center">
                     <i class="fas fa-coins me-2 text-warning"></i> Financial Information
                 </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 120px;"><i class="fas fa-award me-2 text-muted"></i> Scholarship:</span>
                    <span class="fw-medium">{{ $university->newScholarship ?? 'N/A' }}</span>
                 </div>
                 <div class="d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 120px;"><i class="fas fa-dollar-sign me-2 text-muted"></i> Amount:</span>
                    <span class="fw-medium">{{ $university->newAmount ?? 'N/A' }}</span>
                 </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pills-language" role="tabpanel" aria-labelledby="pills-language-tab" tabindex="0">
         <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                 <h5 class="mb-0 d-flex align-items-center">
                     <i class="fas fa-language me-2 text-info"></i> Language Requirements
                 </h5>
            </div>
            <div class="card-body p-4">
                 <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i> Undergrad IELTS:</span>
                    <span class="fw-medium">{{ $university->newIelts ?? 'N/A' }}</span>
                 </div>
                 <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i> Undergrad PTE:</span>
                    <span class="fw-medium">{{ $university->newpte ?? 'N/A' }}</span>
                 </div>
                 <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i> Postgrad IELTS:</span>
                    <span class="fw-medium">{{ $university->newPgIelts ?? 'N/A' }}</span>
                 </div>
                 <div class="d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 160px;"><i class="fas fa-certificate me-2 text-muted"></i> Postgrad PTE:</span>
                    <span class="fw-medium">{{ $university->newPgPte ?? 'N/A' }}</span>
                 </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pills-academic" role="tabpanel" aria-labelledby="pills-academic-tab" tabindex="0">
        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                 <h5 class="mb-0 d-flex align-items-center">
                     <i class="fas fa-graduation-cap me-2 text-success"></i> Academic Requirements
                 </h5>
            </div>
            <div class="card-body p-4">
                 <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 160px;"><i class="fas fa-user-graduate me-2 text-muted"></i> Undergraduate:</span>
                    <span class="fw-medium">{{ $university->newug ?? 'N/A' }}</span>
                 </div>
                 <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 160px;"><i class="fas fa-user-graduate me-2 text-muted"></i> Postgraduate:</span>
                    <span class="fw-medium">{{ $university->newpg ?? 'N/A' }}</span>
                 </div>
                 <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 160px;"><i class="fas fa-chart-bar me-2 text-muted"></i> Undergrad GPA:</span>
                    <span class="fw-medium">{{ $university->newgpaug ?? 'N/A' }}</span>
                 </div>
                  <div class="mb-3 d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 160px;"><i class="fas fa-chart-bar me-2 text-muted"></i> Postgrad GPA:</span>
                    <span class="fw-medium">{{ $university->newgpapg ?? 'N/A' }}</span>
                 </div>
                 <div class="d-flex align-items-start">
                    <span class="text-muted me-3" style="min-width: 160px;"><i class="fas fa-tasks me-2 text-muted"></i> Additional Tests:</span>
                    <span class="fw-medium">{{ $university->newtest ?? 'N/A' }}</span>
                 </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pills-documents" role="tabpanel" aria-labelledby="pills-documents-tab" tabindex="0">
         <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                 <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-file-alt me-2 text-secondary"></i> Required Documents
                 </h5>
            </div>
            <div class="card-body p-4">
                 <p class="text-dark mb-0">{{ $university->requireddocuments ?? 'No specific documents listed.' }}</p>
            </div>
        </div>
    </div>

</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@endsection