@extends('layouts.admin')
@include('backend.script.alert')

@section('content')

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }



    .page-container {
        background-color: white;
        flex-direction: row;
        display: flex;
        gap: 1px;
    }

    /* Create Section Styles */
    .create-section {
        flex: 0 0 500px;
        /* Reduced from 700px */
        border-radius: 8px;
        padding: 1rem;
        /* Reduced from 2rem */
    }

    input[type="file"] {
        position: absolute;
        width: 0;
        height: 0;
        padding: 0;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
        visibility: hidden;
    }



    .upload-container {
        border: 2px dashed #24a52f;
        border-radius: 8px;
        height: 500px;
        width: 400px;
        margin-top: 50px;
        text-align: center;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: rgba(252, 247, 247, 0.5);
    }

    .upload-container.dragover {
        border-color: #4CAF50;
        background-color: #f0f8f0;
    }

    .upload-icon img {
        width: 80px;
        height: 80px;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Index Section Styles */
    .index-section {
        flex: 1;
        margin-right: 30px;
        /* Reduced from 50px */
        background-color: white;
        height: 600px;
        /* Reduced from 700px */
        border-radius: 8px;
    }

    .index-section-content {
        height: calc(100% - 95px);
        padding-right: 10px;
        margin-top: 40px;
        border: 2px dashed #24a52f;
        border-radius: 8px;
        overflow-y: scroll;
        /* Allow scrolling */
    }

    /* Hide scrollbar for WebKit browsers */
    .index-section-content::-webkit-scrollbar {
        width: 0;
        /* Remove scrollbar space */
        background: transparent;
        /* Optional: make background transparent */
    }

    /* Hide scrollbar for Firefox */
    .index-section-content {
        scrollbar-width: none;
        /* Hide scrollbar */
    }


    /* Table Styles */
    .documents-table {
        width: 100%;
        /* Changed from 110% */
        border-collapse: separate;
        margin-top: 20px;
        /* Reduced from 30px */
        margin-right: 10px;
        /* Reduced from 20px */
    }

    .documents-table tr {
        background: white;
        gap: 5px;

    }

    .documents-table td {

        border: none;
        gap: 5px;
        vertical-align: middle;
        margin-left: 10px;
    }

    .documents-table td:first-child {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-left: 30px;
    }

    .file-item {
        width: 100%;
        margin-bottom: 20px;
        border-radius: 8px;

    }

    .file-details {
        position: relative;
        padding-bottom: 25px;
        /* Add space for the bottom elements */
    }

    .file-bottom-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px;
    }

    .file-size {
        font-size: 12px;

    }

    .progress-percentage {
        font-size: 12px;
        color: #666;
    }


    .file-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .file-name {
        font-size: 17px;
        font-family: poppins;
        color: #333;
        flex-grow: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
        cursor: pointer;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .file-name:hover {
        color: #4CAF50;

    }

    .file-link {
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-grow: 1;
    }

    .file-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-grow: 1;
    }

    .upload-date {
        color: #666;
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .view-link {
        color: #4CAF50;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .view-icon {
        width: 20px;
        height: 20px;
        color: #4CAF50;
    }

    .success-icon {
        color: #4CAF50;
        margin-right: 8px;
    }

    .progress-section {
        margin-top: 30px;
        width: 300px;


    }

    .progress-bar {
        width: 100%;
        border-radius: 5px;
    }

    .progress-fill {
        height: 10px;
        /* Adjust height as needed */
        background-color: #4CAF50;
        /* Color of the progress fill */
        width: 0%;
        /* Initial width */
        border-radius: 5px;
    }


    /* Custom Scrollbar */
    .index-section-content::-webkit-scrollbar {
        width: 6px;
    }

    .index-section-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .index-section-content::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .index-section-content::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Adjust the heading margins */
    h2 {
        margin-bottom: 20px !important;
        /* Override the existing 40px margin */
    }

    .index-section h2 {
        margin-top: 20px !important;
        /* Reduced from 30px */
    }

    /* Alert Styles */
    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 24px;
        border-radius: 4px;
        z-index: 1000;
    }



    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .page-container {
            flex-direction: column;
        }

        .create-section,
        .index-section {
            width: 100%;
            flex: none;
        }
    }
</style>

<div class="position-relative overflow-hidden">

    <div class="card mx-9 mt-n10">
        <div class="card-body pb-0">
            <div class="d-md-flex align-items-center justify-content-between text-center text-md-start">
                <div class="d-md-flex align-items-center">
                    <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
                        <img src="{{asset('../assets/images/profile/user-1.jpg')}}" alt="spike-img" class="img-fluid rounded-circle" width="100" height="100">
                        <span class="text-bg-primary rounded-circle text-white d-flex align-items-center justify-content-center position-absolute bottom-0 end-0 p-1 border border-2 border-white">
                            <i class="ti ti-plus"></i>
                        </span>
                    </div>
                    <div class="ms-0 ms-md-3 mb-9 mb-md-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-1">
                            <h4 class="me-7 mb-0 fs-7">{{ $application->name }}</h4>
                            <span class="badge fs-2 fw-bold rounded-pill bg-primary-subtle text-primary border-primary border">Active</span>
                        </div>
                        <p class="fs-4 mb-1">Student</p>
                        <!-- <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                            <span class="bg-success p-1 rounded-circle"></span>
                            <h6 class="mb-0 ms-2">Active</h6>
                        </div> -->
                    </div>
                </div>
                <a href="javascript:void(0)" class="btn btn-primary px-3 shadow-none">Edit Profile</a>
            </div>
            <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start" id="pills-tab" role="tablist">
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true">
                        <i class="ti ti-user-circle me-0 me-md-6  fs-6"></i>
                        <span class="d-none d-md-block">Student Profile</span>
                    </button>
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-doc-tab" data-bs-toggle="pill" data-bs-target="#pills-doc" type="button" role="tab" aria-controls="pills-doc" aria-selected="">
                        <i class="ti ti-user-circle me-0 me-md-6  fs-6"></i>
                        <span class="d-none d-md-block">doc</span>
                    </button>
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-Document-tab" data-bs-toggle="pill" data-bs-target="#pills-Document" type="button" role="tab" aria-controls="pills-Document" aria-selected="false">
                        <i class="ti ti-users me-0 me-md-6  fs-6"></i>
                        <span class="d-none d-md-block">Student Document</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-status-tab" data-bs-toggle="pill" data-bs-target="#pills-status" type="button" role="tab" aria-controls="pills-status" aria-selected="false">
                        <i class="ti ti-id me-0 me-md-6  fs-6"></i>
                        <span class="d-none d-md-block">add To Another University</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="tab-content mx-10" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
        <div class="row">
            <div class="col-lg-4">
                <div class="card ">
                    <div class="card-body p-4">
                        <div class="py-9">
                            <h5 class="mb-9">Personal information</h5>
                            <div class="d-flex align-items-center mb-9" data-field="name">
                                <!-- Name Icon -->
                                <div class="bg-primary-subtle text-primary fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="ti ti-user"></i>
                                </div>

                                <!-- Name with Edit Functionality -->
                                <div class="ms-6" data-field="name">
                                    <h6 class="mb-1">Name</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->name }}
                                        </span>
                                        <!-- Edit Icon -->
                                        <span class="edit-icon ms-2">
                                            <i class="fas fa-pencil-alt" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9">
                                <!-- Phone Icon -->
                                <div class="bg-danger-subtle text-danger fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="ti ti-phone"></i>
                                </div>

                                <!-- Phone Number with Edit Functionality -->
                                <div class="ms-6" data-field="phone">
                                    <h6 class="mb-1">Phone number</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->phone }}
                                        </span>
                                        <!-- Edit Icon -->
                                        <span class="edit-icon ms-2">
                                            <i class="fas fa-pencil-alt" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="email">
                                <!-- Email Icon -->
                                <div class="bg-info-subtle text-info fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="ti ti-mail"></i>
                                </div>

                                <!-- Email Address with Edit Functionality -->
                                <div class="ms-6">
                                    <h6 class="mb-1">Email</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->email }}
                                        </span>
                                        <!-- Edit Icon -->
                                        <span class="edit-icon ms-2">
                                            <i class="fas fa-pencil-alt" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="pt-9">
                            <h5 class="mb-9">Test Information</h5>
                            <div class="d-flex align-items-center mb-9" data-field="englishTest">
                                <!-- Test Icon -->
                                <div class="bg-info-subtle text-info fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="ti ti-book"></i>
                                </div>

                                <!-- English Test with Edit Functionality -->
                                <div class="ms-6">
                                    <h6 class="mb-1">English Test</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->englishTest }}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-9" data-field="score">
                                <!-- Score Icon -->
                                <div class="bg-warning-subtle text-warning fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="ti ti-medal"></i>
                                </div>

                                <!-- Score with Edit Functionality -->
                                <div class="ms-6">
                                    <h6 class="mb-1">Score</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->score }}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-9" data-field="englishscore">
                                <!-- English Score Icon -->
                                <div class="bg-success-subtle text-success fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="ti ti-check"></i>
                                </div>

                                <!-- English Score with Edit Functionality -->
                                <div class="ms-6">
                                    <h6 class="mb-1">English Score</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->englishscore }}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-9" data-field="englishtheory">
                                <!-- English Theory Icon -->
                                <div class="bg-primary-subtle text-primary fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="ti ti-book-open"></i>
                                </div>

                                <!-- English Theory with Edit Functionality -->
                                <div class="ms-6">
                                    <h6 class="mb-1">English Theory</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->englishtheory }}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-9" data-field="passed">
                                <!-- Passed Date Icon -->
                                <div class="bg-secondary-subtle text-secondary fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="ti ti-calendar"></i>
                                </div>

                                <!-- Passed Date with Edit Functionality -->
                                <div class="ms-6">
                                    <h6 class="mb-1">Passed Date</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->passed }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-9">University Infromation</h5>
                        <div class="d-flex align-items-center mb-9" data-field="university">
                            <!-- University Icon -->
                            <div class="bg-info-subtle text-info fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="ti ti-building"></i>
                            </div>

                            <!-- University (Non-Editable) -->
                            <div class="ms-6">
                                <h6 class="mb-1">University</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->university }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-9" data-field="country">
                            <!-- Country Icon -->
                            <div class="bg-warning-subtle text-warning fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="ti ti-globe"></i>
                            </div>

                            <!-- Country (Non-Editable) -->
                            <div class="ms-6">
                                <h6 class="mb-1">Country</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->country }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-9" data-field="intake">
                            <!-- Intake Icon -->
                            <div class="bg-success-subtle text-success fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="ti ti-calendar-event"></i>
                            </div>

                            <!-- Intake (Non-Editable) -->
                            <div class="ms-6">
                                <h6 class="mb-1">Intake</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->intake }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-9" data-field="course">
                            <!-- Course Icon -->
                            <div class="bg-primary-subtle text-primary fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="ti ti-book"></i>
                            </div>

                            <!-- Course (Non-Editable) -->
                            <div class="ms-6">
                                <h6 class="mb-1">Course</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->course }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <!-- Fee Icon -->
                            <div class="bg-info-subtle text-info fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="ti ti-wallet"></i>
                            </div>

                            <!-- Fee (Editable) -->
                            <div class="ms-6" data-field="fee">
                                <h6 class="mb-1">Fee</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->fee }}
                                    </span>
                                    <!-- Edit Icon for Fee -->
                                    <span class="edit-icon ms-2">
                                        <i class="fas fa-pencil-alt" onclick="makeEditable(this.closest('.field-value'))"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class=" p-4">
                    <div class="card border">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{asset('../assets/images/profile/user-1.jpg')}}" alt="user-profile" width="32" height="32" class="rounded-circle">
                                <h6 class="mb-0 ms-6">Add New Comment</h6>
                            </div>

                            <form action="{{ route('backend.application.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="application_id" value="{{ $application->id }}">
                                <input type="hidden" name="user_id" value="{{ Auth::check() ? Auth::id() : '' }}">
                                <div class="mb-3">
                                    <select id="application" name="application" class="form-select" required>
                                        <option value="" disabled selected>Select type of</option>
                                        @foreach ($commentadds as $commentadd)
                                        <option value="{{ $commentadd->applications }}">{{ $commentadd->applications }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="userprofile-quill-editors mb-3">
                                    <div id="editor-feeds">
                                        <textarea id="comment" name="comment" class="form-control" placeholder="enter your comment" required></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary shadow-none">Post</button>
                            </form>

                        </div>
                    </div>

                    @foreach ($comments as $comment)
                    <div class="p-4 rounded-4 text-bg-light mb-3">
                        <div class="card-body border-bottom">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center gap-6 flex-wrap">
                                    <img src="{{ asset('../assets/images/profile/user-6.jpg') }}" alt="user-img" class="rounded-circle" width="40" height="40">
                                    <h6 class="mb-0">{{ $comment->createdBy->name ?? 'Unknown User' }}</h6>
                                    <span class="fs-2">
                                        <span class="p-1 text-bg-light rounded-circle d-inline-block"></span>
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                    <!-- Add the comment type here -->
                                    <small class="badge text-bg-secondary">{{ $comment->application }}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="dropdown ms-auto">
                                        <a class="text-dark d-flex align-items-center justify-content-center bg-transparent p-2 fs-4 rounded-circle" href="javascript:void(0)" id="dropdownMenuButton{{ $loop->index }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $loop->index }}">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="openEditPopup('{{ $comment->id }}', '{{ $comment->application }}', '{{ $comment->comment }}')">
                                                    Edit Comment
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p class="text-dark my-3">{{ $comment->comment }}</p>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-doc" role="tabpanel" aria-labelledby="pills-doc-tab" tabindex="0">

        <div class="page-container card">
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <!-- Create Section (Left Side) -->
            <div class="create-section">
                <h2 style="color:green; font-size:20px; margin-bottom:40px; font-family:poppins;">Upload Documents</h2>
                <div id="uploadContainer" class="upload-container">
                    <div class="upload-icon" id="uploadIcon">
                        <img src="{{ asset('img/wri.png') }}" alt="Upload icon">
                    </div>
                    <p class="upload-text" id="uploadText">Drag & drop files here</p>
                    <input type="file" id="fileInput" name="fileInput[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                    <div id="progressSection" class="progress-section"></div>
                </div>
            </div>

            <div class="index-section">
                <h2 style="color:green; font-size:20px; margin-top:30px; font-family:poppins;">Uploaded Files</h2>
                <div class="index-section-content">
                    <table class="documents-table">
                        <tbody>
                            @foreach ($uploads as $upload)
                            <tr>
                                <td>
                                    <i class="fa-solid fa-circle-check" style="color: green;"></i>&nbsp;&nbsp;

                                    <div class="file-wrapper">
                                        <div class="file-icon">
                                            <i class="fa-regular fa-file"></i>
                                        </div>
                                        <a href="{{ asset('storage/uploads/' . $upload->fileInput) }}"
                                            class="file-link"
                                            download="{{ $upload->fileInput }}">
                                            <span class="file-name">{{ $upload->fileInput }}</span>
                                        </a>
                                    </div>
                                </td>
                                <td class="upload-date">
                                    {{ $upload->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
                                    <div class="action-buttons">
                                        <a href="{{ asset('storage/uploads/' . $upload->fileInput) }}"
                                            class="view-link"
                                            target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('upload.destroy', $upload->id) }}"
                                            method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger" style="border: none; background: transparent; padding: 0;">
                                                <i class="fa-solid fa-xmark" style="color:red;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <div class="tab-pane fade" id="pills-Document" role="tabpanel" aria-labelledby="pills-Document-tab" tabindex="0">
        <div id="tab4" class="section px-4 my-4">
            <div class="flex justify-between">
                <div class="form-group mb-3">
                    <label for="status">Document Status</label>
                    <select id="status" class="form-select w-full">
                        <option value="" style="display: none;">Select Status</option>
                        <option value="1" data-status="offer received">Offer Received</option>
                        <option value="2" data-status="offer started">Offer Started</option>
                        <option value="3" data-status="offer submitted">Offer Submitted</option>
                    </select>
                </div>
                <div class="my-auto">
                    <button id="saveButton" type="button" class="btn btn-primary" disabled>Save Permanent</button>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <ul id="progressBar" role="tablist" class="flex justify-between my-4"></ul>

        <style>
            /* Styling for the progress bar */
            #progressBar {
                list-style: none;
                padding: 0;
                margin: 0;
                display: flex;
                justify-content: space-between;
                position: relative;
            }

            #progressBar li {
                flex: 1;
                text-align: center;
                position: relative;
            }

            #progressBar li a {
                text-decoration: none;
                color: #666;
                display: block;
                padding: 10px;
            }

            #progressBar li.current a {
                color: #2196F3;
                font-weight: bold;
            }

            #progressBar li.completed a {
                color: #28a733;
            }

            #progressBar li.disabled a {
                color: #999;
            }

            #progressBar li:not(:last-child)::after {
                content: '';
                position: absolute;
                top: 50%;
                right: -50%;
                width: 100%;
                height: 2px;
                background-color: #ECECEC;
                /* Default gray color */
                z-index: 1;
                transition: background-color 0.3s ease;
                /* Smooth transition for color changes */
            }

            /* Hide the progress bar line initially for the first step */
            #progressBar li:first-child::after {
                background-color: transparent;
                /* Transparent until the second step is selected */
            }

            /* Completed steps have a green line */
            #progressBar li.completed::after {
                background-color: #28a733;
                /* Green color for completed steps */
            }

            #progressBar li.current::after {
                background-color: #28a733;
                /* Green color for the current step */
            }

            #progressBar li:last-child::after {
                display: none;
            }

            #progressBar .step {
                display: block;
                font-size: 14px;
                margin-bottom: 5px;
            }
        </style>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                console.log("jQuery is loaded and ready!");

                // Function to initialize the progress bar
                function initializeProgressBar() {
                    console.log("Initializing Progress Bar...");
                    const $progressBar = $('#progressBar');
                    $progressBar.empty();

                    // Add steps to the progress bar
                    $('#status option').not(':first').each(function(index) {
                        const stepText = $(this).text().trim();
                        console.log("Adding step:", stepText);

                        const $li = $('<li>')
                            .attr('role', 'tab')
                            .addClass('disabled') // All steps start as disabled
                            .attr('aria-disabled', 'true')
                            .attr('aria-selected', 'false');

                        const $a = $('<a>')
                            .attr('href', '#')
                            .html(`<span class="step">${index + 1}</span> ${stepText}`);

                        $li.append($a);
                        $progressBar.append($li);
                    });

                    console.log("Progress Bar HTML:", $progressBar.html());
                }

                // Function to update the progress bar based on the selected index
                function updateProgressBar(selectedIndex) {
                    console.log("Updating Progress Bar. Selected Index:", selectedIndex);
                    $('#progressBar li').each(function(index) {
                        const $li = $(this);

                        if (selectedIndex === -1) {
                            // If no status is selected, reset all steps to disabled
                            $li.removeClass('current completed').addClass('disabled')
                                .attr('aria-selected', 'false')
                                .attr('aria-disabled', 'true');

                            // Ensure the first step's line remains transparent
                            if (index === 0) {
                                $li.css('--line-color', 'transparent');
                            }
                        } else if (index < selectedIndex) {
                            // Mark as completed
                            $li.removeClass('disabled current').addClass('completed')
                                .attr('aria-selected', 'false')
                                .attr('aria-disabled', 'false');

                            // Show the line for completed steps
                            $li.css('--line-color', '#28a733'); // Green color for completed steps
                        } else if (index === selectedIndex) {
                            // Mark as current
                            $li.removeClass('disabled completed').addClass('current')
                                .attr('aria-selected', 'true')
                                .attr('aria-disabled', 'false');

                            // Show the line for the current step only if it's not the first step
                            if (index > 0) {
                                $li.css('--line-color', '#28a733'); // Green color for the current step
                            } else {
                                $li.css('--line-color', 'transparent'); // Transparent for the first step
                            }
                        } else {
                            // Mark as disabled
                            $li.removeClass('current completed').addClass('disabled')
                                .attr('aria-selected', 'false')
                                .attr('aria-disabled', 'true');

                            // Ensure the line remains transparent for future steps
                            $li.css('--line-color', 'transparent');
                        }
                    });
                }

                // Event listener for status dropdown changes
                $('#status').on('change', function() {
                    const selectedStatus = $(this).val();
                    const selectedIndex = selectedStatus ? $('#status option:selected').index() - 1 : -1;

                    // Enable/disable the save button based on whether a status is selected
                    $('#saveButton').prop('disabled', !selectedStatus);

                    // Update the progress bar
                    updateProgressBar(selectedIndex);
                });

                // Event listener for the save button
                $('#saveButton').on('click', function() {
                    const selectedStatus = $('#status').val();
                    if (!selectedStatus) return;

                    $.ajax({
                        url: '/save-document-status', // Adjust this URL based on your backend route
                        type: 'POST',
                        data: {
                            status: selectedStatus,
                            _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
                        },
                        success: function(response) {
                            $('#saveButton').text('Saved!').prop('disabled', true);
                            setTimeout(function() {
                                $('#saveButton').text('Save Permanent').prop('disabled', false);
                            }, 2000);
                        },
                        error: function() {
                            alert('Error saving status. Try again.');
                        }
                    });
                });

                // Initialize the progress bar when the window loads
                $(window).on('load', function() {
                    console.log("Window loaded, initializing progress bar...");
                    initializeProgressBar();

                    // Ensure the progress bar starts empty if no status is selected
                    const initialSelectedIndex = $('#status').val() ? $('#status option:selected').index() - 1 : -1;
                    updateProgressBar(initialSelectedIndex);
                });
            });
        </script>
    </div>
    <div class="tab-pane fade" id="pills-status" role="tabpanel" aria-labelledby="pills-status-tab" tabindex="0">
        <div class="card w-100">
            <div class="card-body">
                <div class="d-flex mb-3 align-items-center">
                    <h4 class="card-title mb-0">Add Application for Same Student</h4>
                </div>
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Error Message -->
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                <form action="{{ route('backend.application.studentstore') }}" method="POST">
                    @csrf
                    <!-- Study Level Dropdown -->
                    <div class="form-group mb-4">
                        <label for="studyLevel">Study Level:</label>
                        <select name="studyLevel" id="studyLevel" class="form-select">
                            <option value="Undergraduate">Undergraduate</option>
                            <option value="Postgraduate">Postgraduate</option>
                        </select>
                    </div>

                    <!-- Country Dropdown -->
                    <div class="form-group mb-4">
                        <label for="country">Country:</label>
                        <select name="country" id="country" class="form-select">
                            @foreach ($data_entries as $data_entrie)
                            <option value="{{ $data_entrie->country }}" {{ old('country') == $data_entrie->country ? 'selected' : '' }}>
                                {{ $data_entrie->country }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Intake Dropdown -->
                    <div class="form-group mb-4">
                        <label for="newIntake">Intake:</label>
                        <select name="newIntake" id="newIntake" class="form-select">
                            @foreach ($data_entries as $data_entrie)
                            <option value="{{ $data_entrie->newIntake }}" {{ old('newIntake') == $data_entrie->newIntake ? 'selected' : '' }}>
                                {{ $data_entrie->newIntake }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- University Dropdown -->
                    <div class="form-group mb-4">
                        <label for="newUniversity">University:</label>
                        <select name="newUniversity" id="newUniversity" class="form-select">
                            @foreach ($data_entries as $data_entrie)
                            <option value="{{ $data_entrie->newUniversity }}" {{ old('newUniversity') == $data_entrie->newUniversity ? 'selected' : '' }}>
                                {{ $data_entrie->newUniversity }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Course Dropdown -->
                    <div class="form-group mb-4">
                        <label for="newCourse">Course:</label>
                        <select name="newCourse" id="newCourse" class="form-select">
                            @foreach ($data_entries as $data_entrie)
                            <option value="{{ $data_entrie->newCourse }}" {{ old('newCourse') == $data_entrie->newCourse ? 'selected' : '' }}>
                                {{ $data_entrie->newCourse }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    class FileUploadManager {
        constructor() {
            this.files = new Map();
            this.uploadContainer = document.getElementById('uploadContainer');
            this.fileInput = document.getElementById('fileInput');
            this.progressSection = document.getElementById('progressSection');
            this.uploadText = document.getElementById('uploadText');
            this.uploadIcon = document.getElementById('uploadIcon');
            this.isUploading = false;
            this.initializeEventListeners();
        }


        initializeEventListeners() {
            this.fileInput.addEventListener('change', (e) => {
                this.handleFiles(Array.from(e.target.files));
            });

            this.uploadContainer.addEventListener('click', () => this.fileInput.click());

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                this.uploadContainer.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                this.uploadContainer.addEventListener(eventName, () => {
                    this.uploadContainer.classList.add('dragover');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                this.uploadContainer.addEventListener(eventName, () => {
                    this.uploadContainer.classList.remove('dragover');
                });
            });

            this.uploadContainer.addEventListener('drop', (e) => {
                const droppedFiles = Array.from(e.dataTransfer.files);
                this.handleFiles(droppedFiles);
            });
        }

        handleFiles(newFiles) {
            if (newFiles.length > 0) {
                this.uploadText.style.display = 'none';
                this.uploadIcon.style.display = 'none';
                this.uploadContainer.style.padding = '0';
                this.uploadContainer.style.justifyContent = 'flex-start';
            }

            newFiles.forEach(file => {
                if (file.size <= 5 * 1024 * 1024) {
                    this.addFile(file);
                    this.uploadFile(file);
                } else {
                    alert(`File ${file.name} is too large. Maximum size is 5MB.`);
                }
            });
        }

        addFile(file) {
            if (!this.files.has(file.name)) {
                this.files.set(file.name, {
                    file,
                    progress: 0
                });
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.id = `file-${file.name}`;
                fileItem.innerHTML = `
            <div class="file-details" style="padding: 10px;">
                <div style="display: flex; align-items: flex-start; margin-bottom: 10px;">
                    <div class="file-name" style="
                        color: green;
                        text-align: left;
                        margin-left: 0;
                        padding-left: 0;
                    ">${file.name}</div>
                </div>
                
                <div style="margin-bottom: 5px;">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill-${file.name}"></div>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div class="file-size" style="font-size: 12px; color: #666;">${(file.size / 1024).toFixed(1)} KB</div>
                    <div class="progress-percentage" style="font-size: 12px; color: #666;" id="progressPercentage-${file.name}">0%</div>
                </div>

                <div class="upload-status" id="uploadStatus-${file.name}">
                    <span class="status-uploading"></span>
                </div>
            </div>`;
                this.progressSection.appendChild(fileItem);
            }
        }
        async uploadFile(file) {
            const formData = new FormData();
            formData.append('fileInput[]', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{route("backend.upload.store")}}', true);

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    this.updateProgress(file.name, percentComplete);
                }
            });

            xhr.onload = () => {
                if (xhr.status === 200) {
                    this.updateUploadStatus(file.name, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.updateUploadStatus(file.name, 'error');
                    this.resetUploadContainer();
                }
            };

            xhr.onerror = () => {
                this.updateUploadStatus(file.name, 'error');
                this.resetUploadContainer();
            };

            xhr.send(formData);
        }

        resetUploadContainer() {
            this.uploadText.style.display = 'block';
            this.uploadIcon.style.display = 'block';
            this.uploadContainer.style.padding = '1.5rem';
            this.uploadContainer.style.justifyContent = 'center';
        }

        updateProgress(fileName, percent) {
            const progressFill = document.getElementById(`progressFill-${fileName}`);
            const progressPercentage = document.getElementById(`progressPercentage-${fileName}`);

            progressFill.style.width = `${percent}%`;
            progressPercentage.textContent = `${Math.round(percent)}%`;
        }

        updateUploadStatus(fileName, status) {
            const statusElement = document.getElementById(`uploadStatus-${fileName}`);

            if (status === 'success') {
                statusElement.innerHTML = '<span class="status-success"></span>';
            } else if (status === 'error') {
                statusElement.innerHTML = '<span class="status-error">Upload Failed</span>';
            }
        }
    }

    const uploadManager = new FileUploadManager();

    function confirmDelete() {
        return confirm('Are you sure you want to delete this document?');
    }
</script>
<script>
    $(document).ready(function() {
        const toastElement = document.getElementById('notification');
        const toast = new bootstral.Toast(toastElement);
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        window.toast = toast;
        window.editModal = editModal;

        const notificationToggle = document.getElementById('notificationToggle');
        if (notificationToggle) {
            notificationToggle.checked = localStorage.getItem('notificationsEnabled') === 'true';
            notificationToggle.addEventListener('change', function() {
                localStorage.setItem('notificationsEnabled', this.checked);
            });
        }

        const savedActiveTab = localStorage.getItem('activeTab');
        if (savedActiveTab) {
            $(`.tab[data-tab="${savedActiveTab}"]`).addClass('active');
            $(`#${savedActiveTab}`).addClass('active');
        }

        $('.tab').click(function() {
            const targetTab = $(this).data('tab');
            localStorage.setItem('activeTab', targetTab);
            $('.tab').removeClass('active');
            $(this).addClass('active');
            $('.section').removeClass('active');
            $(`#${targetTab}`).addClass('active');

            if (targetTab === 'tab5') {
                $('#tab5').fadeIn();
                $('#addCommentBtn').addClass('active');
            } else if (targetTab === 'tab6') {
                $('#studentPopup').fadeIn();
                $('#addStudentBtn').addClass('active');
            }
        });

        $('#addCommentBtn').click(function() {
            $('.tab[data-tab="tab5"]').addClass('active');
            $('#tab5').fadeIn();
            $(this).addClass('active');
        });

        $('#addStudentBtn').click(function() {
            $('.tab[data-tab="tab6"]').addClass('active');
            $('#studentPopup').fadeIn();
            $(this).addClass('active');
        });

        $('.popup-close').click(function() {
            closePopup($(this).closest('.popup'));
        });

        $(window).click(function(event) {
            if ($(event.target).hasClass('popup')) {
                $('.popup').fadeOut();
                $('.tab[data-tab="tab5"], .tab[data-tab="tab6"]').removeClass('active');
                $('#addCommentBtn, #addStudentBtn').removeClass('active');
            }
        });

        $(document).keydown(function(e) {
            if (e.key === "Escape") {
                $('.popup').fadeOut();
                $('.tab[data-tab="tab5"], .tab[data-tab="tab6"]').removeClass('active');
                $('#addCommentBtn, #addStudentBtn').removeClass('active');
            }
        });

        $('.popup-content').click(function(e) {
            e.stopPropagation();
        });

        $('#tab5 form, #studentPopup form').on('submit', function() {
            closePopup($(this).closest('.popup'));
        });

        document.querySelectorAll('.searchable').forEach((select) => {
            new TomSelect(select, {
                placeholder: 'Type to search...',
                create: false,
                searchField: 'text',
                render: {
                    option: function(data, escape) {
                        return `<div>${escape(data.text)}</div>`;
                    },
                    no_results: function(data, escape) {
                        return `<div class="no-results">No results found for "${escape(data.input)}"</div>`;
                    }
                }
            });
        });
    });

    function openEditPopup(commentId, application, commentText) {
        const form = document.getElementById('editForm');
        document.getElementById('editCommentId').value = commentId;
        document.getElementById('editApplication').value = application;
        document.getElementById('editCommentText').value = commentText;
        form.action = `/comment/update/${commentId}`;
        window.editModal.show();
    }

    function updateComment() {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);
        const commentId = document.getElementById('editCommentId').value;

        fetch(`/comment/update/${commentId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => ({
                        isJson: true,
                        data
                    }));
                } else {
                    return response.text().then(text => ({
                        isJson: false,
                        data: text
                    }));
                }
            })
            .then(({
                isJson,
                data
            }) => {
                if (isJson) {
                    if (data.success) {
                        window.editModal.hide();
                        showNotification('Comment updated successfully!');
                        if (document.getElementById('notificationToggle').checked) {
                            playNotificationSound();
                        }
                        location.reload();
                    } else {
                        showNotification(data.message || 'Error updating comment');
                    }
                } else {
                    window.location.href = data;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error updating comment');
            });
    }

    function showNotification(message) {
        const messageElement = document.querySelector('.notification-message');
        messageElement.textContent = message;
        window.toast.show();
        if (document.getElementById('notificationToggle').checked) {
            playNotificationSound();
        }
    }

    function playNotificationSound() {
        const audio = document.getElementById('notificationSound');
        audio.play().catch(function(error) {
            console.log("Audio playback failed:", error);
        });
    }

    function makeEditable(element, type = 'text', options = []) {
        let input;
        const currentValue = element.textContent.trim();
        const parent = element.parentElement;
        const editIcon = parent.querySelector('.edit-icon');
        if (editIcon) {
            editIcon.classList.add('editing');
        }

        if (type === 'select') {
            input = document.createElement('select');
            input.className = 'w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-white';
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Select an option';
            defaultOption.disabled = true;
            input.appendChild(defaultOption);
            options.forEach(optionText => {
                const option = document.createElement('option');
                option.value = optionText;
                option.textContent = optionText;
                if (currentValue === optionText) option.selected = true;
                input.appendChild(option);
            });
        } else {
            input = document.createElement('input');
            input.type = type;
            input.value = currentValue !== 'Not provided' ? currentValue : '';
            input.className = 'w-full border rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-white';
        }

        parent.replaceChild(input, element);
        setTimeout(() => {
            input.focus();
            input.select();
        }, 0);

        input.addEventListener("blur", async function() {
            const newValue = input.value.trim() || 'Not provided';
            if (newValue === currentValue) {
                revertToSpan(newValue);
                return;
            }

            try {
                const response = await updateField(parent.getAttribute('data-field'), newValue);
                confirmSubmission();
                if (response.success) {
                    showNotification('Updated successfully', 'success');
                    revertToSpan(newValue);
                } else {
                    showNotification('Update failed', 'error');
                    revertToSpan(currentValue);
                }
            } catch (error) {
                console.error('Error updating field:', error);
                showNotification('Update failed', 'error');
                revertToSpan(currentValue);
            }
        });

        input.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                input.blur();
            }
        });

        input.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                revertToSpan(currentValue);
            }
        });

        function revertToSpan(value) {
            const span = document.createElement("span");
            span.textContent = value;
            span.className = "field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center";
            span.onclick = () => makeEditable(span, type, options);
            parent.replaceChild(span, input);
            if (editIcon) {
                editIcon.classList.remove('editing');
            }
            span.appendChild(editIcon);
        }
    }

    async function updateField(field, value) {
        try {
            const response = await fetch('/update-student-details', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    field: field,
                    value: value,
                    applications_id: '{{ $application->id }}'
                })
            });
            return await response.json();
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    function showNotification(message, type = 'success') {
        Swal.fire({
            title: type === 'success' ? 'Success!' : 'Error!',
            text: message,
            icon: type,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'swal-custom-popup',
                confirmButton: 'swal-custom-ok-button'
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'SELECT') {
            const currentField = document.activeElement.parentElement;
            const allFields = Array.from(document.querySelectorAll('[data-field]'));
            const currentIndex = allFields.indexOf(currentField);

            if (e.key === 'ArrowDown' || (e.key === 'Tab' && !e.shiftKey)) {
                e.preventDefault();
                const nextField = allFields[currentIndex + 1];
                if (nextField) {
                    const span = nextField.querySelector('span:last-child');
                    makeEditable(span);
                }
            } else if (e.key === 'ArrowUp' || (e.key === 'Tab' && e.shiftKey)) {
                e.preventDefault();
                const prevField = allFields[currentIndex - 1];
                if (prevField) {
                    const span = prevField.querySelector('span:last-child');
                    makeEditable(span);
                }
            }
        }
    });

    document.addEventListener('click', function(e) {
        const activeElement = document.activeElement;
        if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'SELECT')) {
            const clickedElement = e.target;
            if (!activeElement.contains(clickedElement) && !clickedElement.classList.contains('hover:bg-green-100')) {
                activeElement.blur();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const englishTestField = document.querySelector('[data-field="englishTest"] span:last-child');
        if (englishTestField) {
            const testValue = englishTestField.textContent.trim();
            toggleEnglishTestFields(testValue);
        }
    });

    function toggleEnglishTestFields(testType) {
        const scoreFields = document.querySelectorAll('[data-field="higher"], [data-field="less"], [data-field="score"]');
        const noTestFields = document.querySelectorAll('[data-field="englishscore"], [data-field="englishtheory"]');

        scoreFields.forEach(field => {
            field.style.display = ['IELTS', 'PTE', 'ELLT'].includes(testType) ? 'block' : 'none';
        });

        noTestFields.forEach(field => {
            field.style.display = testType === 'No Test' ? 'block' : 'none';
        });
    }

    function confirmSubmission() {
        return confirm('Are you sure you want to submit this application?');
    };
</script>
@endsection