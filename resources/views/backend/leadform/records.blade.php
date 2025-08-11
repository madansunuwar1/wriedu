@extends('layouts.admin')
@include('backend.script.session')
@include('backend.script.alert')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

<style>
    .edit-icon {
        cursor: pointer;
        color: #6c757d;
        transition: color 0.3s ease, opacity 0.15s ease-in-out;
    }

    .edit-icon:hover {
        color: #007bff;
    }

    .field-value .edit-icon {
        display: none;
        opacity: 0;
    }

    .field-value:hover .edit-icon {
        display: inline-block;
        opacity: 1;
    }

    .comment-box {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
    }

    .comment-content {
        margin-bottom: 10px;
    }

    .comment-author {
        font-weight: bold;
        color: #495057;
        margin-bottom: 5px;
    }

    .comment-text {
        color: #212529;
        margin-bottom: 10px;
    }

    .comment-date {
        color: #6c757d;
        font-size: 0.8em;
    }

    .edit-button .link-btn {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .edit-button .link-btn:hover {
        color: #007bff;
    }

    .page-container {
        background-color: white;
        flex-direction: row;
        display: flex;
        gap: 1px;
    }

    .create-section {
        flex: 0 0 500px;
        border-radius: 8px;
        padding: 1rem;
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

    .index-section {
        flex: 1;
        margin-right: 30px;
        background-color: white;
        height: 600px;
        border-radius: 8px;
    }

    .index-section-content {
        height: calc(100% - 95px);
        padding-right: 10px;
        margin-top: 40px;
        border: 2px dashed #24a52f;
        border-radius: 8px;
        overflow-y: scroll;
    }

    .index-section-content::-webkit-scrollbar {
        width: 0;
        background: transparent;
    }

    .index-section-content {
        scrollbar-width: none;
    }

    .documents-table {
        width: 100%;
        border-collapse: separate;
        margin-top: 20px;
        margin-right: 10px;
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
        background-color: #4CAF50;
        width: 0%;
        border-radius: 5px;
    }

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

    h2 {
        margin-bottom: 20px !important;
    }

    .index-section h2 {
        margin-top: 20px !important;
    }

    .alert {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 24px;
        border-radius: 18px;
        z-index: 1000;
    }

    .customize-alert .alert-message {
        margin-right: 30px;
        /* Adjust as needed */
    }

    .alert .btn-close:focus {
        outline: none;
        box-shadow: none;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

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
        z-index: 1;
        transition: background-color 0.3s ease;
    }

    #progressBar li:first-child::after {
        background-color: transparent;
    }

    #progressBar li.completed::after {
        background-color: #28a733;
    }

    #progressBar li.current::after {
        background-color: #28a733;
    }

    #progressBar li:last-child::after {
        display: none;
    }

    #progressBar .step {
        display: block;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .text-icon-person {
        color: #0277bd;
    }

    /* Professional Blue */
    .text-icon-telephone {
        color: #43A047 !important;
    }

    /* Fresh Green */
    .text-icon-geo-alt {
        color: #C62828 !important;
    }

    /* Strong Red */
    .text-icon-envelope {
        color: #1E88E5 !important;
    }

    /* Clean Blue */
    .text-icon-mortarboard {
        color: #8E24AA !important;
    }

    /* Royal Purple */
    .text-icon-check-circle {
        color: #43A047 !important;
    }

    /* Fresh Green */
    .text-icon-calendar-check {
        color: #f57f17 !important;
    }

    /* Warm Amber */
    .text-icon-star-half {
        color: #D81B60 !important;
    }

    /* Bright Rose */
    .text-icon-file-earmark-check {
        color: #0277bd !important;
    }

    /* Professional Blue */
    .text-icon-globe {
        color: #C62828 !important;
    }

    /* Strong Red */
    .text-icon-book {
        color: #1E88E5 !important;
    }

    /* Clean Blue */
    .text-icon-calendar-event {
        color: #f57f17 !important;
    }

    /* Warm Amber */
    .text-icon-certificate {
        color: #43A047 !important;
    }

    /* Fresh Green */
    .text-icon-stars {
        color: #D81B60 !important;
    }

    /* Bright Rose */
    .text-icon-translate {
        color: #8E24AA !important;
    }

    /* Royal Purple */
    .text-icon-journal-text {
        color: #0277bd !important;
    }

    /* Professional Blue */
    .text-icon-person-badge {
        color: #3949AB !important;
    }

    /* Steady Indigo */
</style>
<div id="toast-container" style="position:absolute; top: 400px; left: 20px; z-index: 9999;"></div>
<div class="position-relative overflow-hidden">
    <div class="card mx-9 mt-n10">
        <div class="card-body pb-0">
            <div class="d-md-flex align-items-center justify-content-between text-center text-md-start">
                <div class="d-md-flex align-items-center">
                    <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
                        @if($lead)
                        <img src="{{ optional($lead)->avatar ? asset('storage/avatars/' . $lead->avatar) : asset('assets/images/profile/user-1.jpg') }}"
                            alt="spike-img" class="img-fluid rounded-circle" width="100" height="100">
                        @else
                        <img src="{{ asset('assets/images/profile/user-1.jpg') }}"
                            alt="spike-img" class="img-fluid rounded-circle" width="100" height="100">
                        @endif
                    </div>
                    <div class="ms-0 ms-md-3 mb-9 mb-md-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-1">
                            <h4 class="me-7 mb-0 fs-7">{{ optional($lead)->name ?? 'not provided' }}</h4>
                            <span>
                                @if(!empty(optional($lead)->forwarded_to))
                                @php
                                $application = optional($lead) ? \App\Models\Application::where('lead_id', $lead->id)->first() : null;
                                @endphp
                                <span class="badge bg-primary-subtle text-primary d-inline-flex align-items-center gap-1" data-lead-id="{{ $lead->id }}" id="status-badge-{{ $lead->id }}">
                                    {{ $application ? $application->status : (optional($lead)->status ?? 'not provided') }}
                                </span>
                                @else
                                <span class="badge bg-primary-subtle text-primary d-inline-flex align-items-center gap-1" data-lead-id="{{ $lead->id }}" id="status-badge-{{ $lead->id }}">
                                    {{ optional($lead)->status ?? 'not provided' }}
                                </span>
                                @endif
                            </span>
                        </div>
                        @push('scripts')
                        <script>
                            window.Echo.channel('leads')
                                .listen('.lead.updated', (e) => {
                                    console.log('Real-time update received:', e.lead);

                                    const leadId = e.lead.id;

                                    // Update badge/status for any lead with matching ID
                                    const badge = document.querySelector(`#status-badge-${leadId}`);
                                    if (badge) {
                                        if (e.lead.forwarded_to && e.lead.application) {
                                            // For forwarded leads, use application status
                                            badge.textContent = e.lead.application.status || 'Not provided';
                                        } else {
                                            // For unforwarded leads, use lead status
                                            badge.textContent = e.lead.status || 'Not provided';
                                        }
                                    } else {
                                        console.warn("Badge not found for lead ID:", leadId);
                                    }
                                });
                        </script>
                        @endpush
                        <p class="fs-4 mb-1">Student</p>
                    </div>
                </div>
            </div>
            <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start"
                id="pills-tab" role="tablist">
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button
                        class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent py-6"
                        id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button"
                        role="tab" aria-controls="pills-profile" aria-selected="true">
                        <i class="bi bi-person me-0 me-md-6  fs-6"></i>
                        <span class="d-none d-md-block">Student Details</span>
                    </button>
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <!-- <button
                        class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6"
                        id="pills-doc-tab" data-bs-toggle="pill" data-bs-target="#pills-doc" type="button" role="tab"
                        aria-controls="pills-doc" aria-selected="">
                        <i class="ti ti-user-circle me-0 me-md-6  fs-6"></i>
                        <span class="d-none d-md-block">Document Upload</span>
                    </button> -->
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button
                        class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6"
                        id="pills-status-tab" data-bs-toggle="pill" data-bs-target="#pills-status" type="button" role="tab"
                        aria-controls="pills-status" aria-selected="">
                        <i class="bi bi-files me-0 me-md-6  fs-6"></i>
                        <span class="d-none d-md-block">Student Status</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <a class="nav-link d-flex align-items-center justify-content-center bg-transparent py-6"
                        href="javascript:void(0)" onclick="showForwardModal()">
                        <i class="bi bi-card-text me-0 me-md-6 fs-6"></i>
                        <span class="d-none d-md-block">Forward Document</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="tab-content mx-10" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel"
        aria-labelledby="pills-profile-tab" tabindex="0">
        <style>
            .row {
                display: flex;
                flex-wrap: wrap;
            }

            .col-lg-4,
            .col-lg-8 {
                display: flex;
                flex-direction: column;
            }

            .col-lg-8 {
                flex: 1;
            }
        </style>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="py-9">
                            <h5 class="mb-9">Personal Information</h5>
                            <div class="d-flex align-items-center mb-9" data-field="name">
                                <div class="text-icon-person fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Name</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->name ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="phone">
                                <div class="text-icon-telephone fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Phone Number</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->phone ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="locations">
                                <div class="text-icon-geo-alt fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Location</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->locations ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="email">
                                <div class="text-icon-envelope fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Email</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->email ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <div class="pt-9">
                            <h5 class="mb-9">Academic Information</h5>
                            <div class="d-flex align-items-center mb-9" data-field="lastqualification">
                                <div class="text-icon-mortarboard fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-mortarboard"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Level</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->lastqualification ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="courselevel">
                                <div class="text-icon-check-circle fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Course</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->courselevel ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="passed">
                                <div class="text-icon-calendar-check fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Passed Year</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->passed ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="gpa">
                                <div class="text-icon-star-half fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-star-half"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">GPA/Percentage</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->gpa ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="academic">
                                <div class="text-icon-file-earmark-check fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-file-earmark-check"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Document Received</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->academic ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <div class="pt-9">
                            <h5 class="mb-9">University Information</h5>
                            <div class="d-flex align-items-center mb-9" data-field="location">
                                <div class="text-icon-geo-alt fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Location</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->location ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="country">
                                <div class="text-icon-globe fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-globe"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Country</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->country ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="course">
                                <div class="text-icon-book fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-book"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Courses</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->course ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="intake">
                                <div class="text-icon-calendar-event fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Intakes</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->intake ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body p-4">
                        <div class="pt-9">
                            <h5 class="mb-9">Test Scores</h5>
                            <div class="d-flex align-items-center mb-9" data-field="englishTest">
                                <div class="text-icon-certificate fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="ti ti-certificate"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">English Test</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->englishTest ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="score">
                                <div class="text-icon-stars fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-stars"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Overall Score</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->score ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="englishscore">
                                <div class="text-icon-translate fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-translate"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">English Score</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->englishscore ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="englishtheory">
                                <div class="text-icon-journal-text fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">English Theory</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content">{{ optional($lead)->englishtheory ?? 'Not provided' }}</span>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="makeEditable(this.closest('.field-value'))"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="creator">
                                <div class="text-icon-person-badge fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Created By</h6>
                                    <div class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center" style="width: 100%;">
                                        <span class="field-value-content" id="user-display">
                                            {{ optional($lead)->creator ? optional($lead->creator)->name : 'Unknown User' }}
                                        </span>
                                        <select name="created_by" class="form-control hidden" id="user-dropdown" onchange="updateUser(this)">
                                            @foreach (\App\Models\User::all() as $user)
                                            <option value="{{ $user->id }}" {{ optional($lead)->created_by == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <span class="edit-icon ms-2"><i class="bi bi-pencil" onclick="toggleDropdown(this)"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 d-flex flex-column">
                <div class="">
                    <div class="card border">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ asset('../assets/images/profile/user-1.jpg') }}" alt="user-profile"
                                    width="32" height="32" class="rounded-circle">
                                <h6 class="mb-0 ms-6">Add New Comment</h6>
                            </div>
                            <form id="comment-form" action="{{ route('backend.leadcomment.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id"
                                    value="{{ Auth::check() ? Auth::id() : '' }}">
                                @if ($lead)
                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                @else
                                <input type="hidden" name="lead_id" value="">
                                @endif
                                <div class="userprofile-quill-editors mb-3">
                                    <div id="editor-feeds">
                                        <textarea id="comment" name="comment" class="form-control"
                                            placeholder="Enter your comment" required></textarea>
                                    </div>
                                </div>
                                <!-- Blade Template -->
                                <div class="d-flex justify-content-between w-[100%]">
                                    <button type="submit" class="btn btn-primary shadow-none">Post</button>

                                    <!-- Custom Date Time Button -->
                                    <div class="dropdown">
                                        <button id="dateTimeBtn" type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Select Date & Time
                                        </button>
                                        <div class="dropdown-menu p-3" style="width: 290px;">
                                            <div class="mb-3">
                                                <label for="datePicker" class="form-label">Date</label>
                                                <input type="date" class="form-control" id="datePicker">
                                            </div>
                                            <div class="mb-3">
                                                <label for="timePicker" class="form-label">Time</label>
                                                <input type="time" class="form-control" id="timePicker">
                                            </div>
                                            <div class="d-grid">
                                                <button type="button" class="btn btn-primary" id="confirmDateTime">Confirm</button>
                                            </div>
                                            <!-- Changed name to date_time to match your database column -->
                                            <input type="hidden" name="date_time" id="selectedDateTime">
                                        </div>
                                    </div>
                                </div>

                                <!-- JavaScript code to handle the custom date time button -->
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const dateTimeBtn = document.getElementById('dateTimeBtn');
                                        const datePicker = document.getElementById('datePicker');
                                        const timePicker = document.getElementById('timePicker');
                                        const confirmBtn = document.getElementById('confirmDateTime');
                                        const selectedDateTime = document.getElementById('selectedDateTime');

                                        // Set default date to today
                                        const today = new Date();
                                        const formattedDate = today.toISOString().split('T')[0];
                                        datePicker.value = formattedDate;

                                        // Set default time to current time
                                        const hours = String(today.getHours()).padStart(2, '0');
                                        const minutes = String(today.getMinutes()).padStart(2, '0');
                                        timePicker.value = `${hours}:${minutes}`;

                                        confirmBtn.addEventListener('click', function() {
                                            const date = datePicker.value;
                                            const time = timePicker.value;

                                            if (date && time) {
                                                // Format the date and time for display
                                                const dateObj = new Date(date + 'T' + time);
                                                const formattedDateTime = formatDateTime(dateObj);

                                                // Update button text
                                                dateTimeBtn.textContent = formattedDateTime;

                                                // Store value in hidden input - format for database (Y-m-d H:i:s)
                                                selectedDateTime.value = date + ' ' + time + ':00';

                                                // Close the dropdown
                                                document.querySelector('.dropdown-menu').classList.remove('show');
                                            }
                                        });

                                        // Format date and time in a user-friendly way
                                        function formatDateTime(dateObj) {
                                            const options = {
                                                year: 'numeric',
                                                month: 'short',
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            };
                                            return dateObj.toLocaleDateString('en-US', options);
                                        }
                                    });
                                </script>
                            </form>
                        </div>
                    </div>
                    <div id="comments-list" style="height: 1500px; overflow-y: auto;">
                        @foreach ($lead_comments->sortByDesc('created_at') as $lead_comment)
                        @if ($lead_comment->lead_id == $lead->id)
                        <div class="p-4 rounded-4 text-bg-light mb-3">
                            <div class="card-body border-bottom">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-6 flex-wrap">
                                        @if($lead_comment->user && $lead_comment->user->avatar)
                                        <img src="{{ asset('storage/avatars/' . $lead_comment->user->avatar) }}" alt="user-img" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                        @else
                                        <img src="{{ asset('/avatars/male-2.jpg') }}" alt="default-user-img" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                        @endif
                                        <h6 class="mb-0">{{ $lead_comment->createdBy->name ?? 'Unknown User' }}
                                            {{ $lead_comment->createdBy->last ?? '' }}
                                        </h6>
                                        <span class="fs-2">
                                            <span class="p-1 text-bg-light rounded-circle d-inline-block"></span>
                                            {{ $lead_comment->created_at->format('F j, Y \a\t g:i a') }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="dropdown ms-auto">
                                            <a class="text-dark d-flex align-items-center justify-content-center bg-transparent p-2 fs-4 rounded-circle"
                                                href="javascript:void(0)" id="dropdownMenuButton{{ $loop->index }}"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </a>
                                            <ul class="dropdown-menu"
                                                aria-labelledby="dropdownMenuButton{{ $loop->index }}">
                                                <li><a class="dropdown-item" href="#">Edit</a></li>
                                                <li><a class="dropdown-item" href="#">Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-dark my-3">{{ $lead_comment->comment }}</p>
                                @if ($lead_comment->updated_by && $lead_comment->updated_at != $lead_comment->created_at)
                                <small class="text-muted">(Updated)</small>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
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

            <div class="create-section">
                <h2 style="color:green; font-size:20px; margin-bottom:40px; font-family:poppins;">Upload
                    Documents</h2>
                <div id="uploadContainer" class="upload-container">
                    <div class="upload-icon" id="uploadIcon">
                        <img src="{{ asset('img/wri.png') }}" alt="Upload icon">
                    </div>
                    <p class="upload-text" id="uploadText">Drag & drop files here</p>
                    <input type="file" id="fileInput" name="fileInput[]" multiple
                        accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                    <div id="progressSection" class="progress-section"></div>
                </div>
            </div>

            <div class="index-section">
                <h2 style="color:green; font-size:20px; margin-top:30px; font-family:poppins;">Uploaded
                    Files</h2>
                <div class="index-section-content">
                    <table class="documents-table">
                        <tbody>
                            @foreach ($uploads as $upload)
                            @if ($upload->lead_id == $lead->id)
                            <tr>
                                <td>
                                    <i class="fa-solid fa-circle-check" style="color: green;"></i>
                                    <div class="file-wrapper">
                                        <div class="file-icon">
                                            <i class="fa-regular fa-file"></i>
                                        </div>
                                        <a href="{{ asset('storage/uploads/' . $upload->fileInput) }}"
                                            class="file-link" download="{{ $upload->fileInput }}">
                                            <span class="file-name">{{ $upload->fileInput }}</span>
                                        </a>
                                    </div>
                                </td>
                                <td class="upload-date">
                                    {{ $upload->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <link
                                        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
                                        rel="stylesheet">
                                    <div class="action-buttons">
                                        <a href="{{ asset('storage/uploads/' . $upload->fileInput) }}"
                                            class="view-link" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('upload.destroy', $upload->id) }}" method="POST"
                                            style="display: inline;" onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger"
                                                style="border: none; background: transparent; padding: 0;">
                                                <i class="fa-solid fa-xmark" style="color:red;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-status" role="tabpanel" aria-labelledby="pills-status-tab" tabindex="0">
        <div id="tab4" class="section px-4 my-4">
            <div class="flex justify-between">
                <div class="form-group mb-3 w-full md:w-1/2">
                    <label for="document_status" class="block mb-2 text-sm font-medium text-gray-700">
                        Document Status
                    </label>
                    <select id="document_status" name="document_status"
                        class="form-select w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">Select Status</option>
                        @foreach($documents as $document)
                        @if($document->status === 'lead')
                        <option value="{{ $document->document }}"
                            {{ $lead->status === $document->document ? 'selected' : '' }}>
                            {{ $document->document }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="my-auto">
                    <button id="saveButton" type="button" class="btn btn-primary" disabled>Save
                        Permanent</button>
                </div>
            </div>
        </div>
    </div>

    <ul id="progressBar" role="tablist" class="flex justify-between my-4"></ul>
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
            formData.append('lead_id', '{{ optional($lead)->id ?? "" }}');

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
        console.log("jQuery is loaded and ready!");

        function initializeProgressBar() {
            console.log("Initializing Progress Bar...");
            const $progressBar = $('#progressBar');
            $progressBar.empty();

            $('#document_status option').not(':first').each(function(index) {
                const stepText = $(this).text().trim();
                console.log("Adding step:", stepText);

                const $li = $('<li>')
                    .attr('role', 'tab')
                    .addClass('disabled')
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

        function updateProgressBar(selectedIndex) {
            console.log("Updating Progress Bar. Selected Index:", selectedIndex);
            $('#progressBar li').each(function(index) {
                const $li = $(this);

                if (selectedIndex === -1) {
                    $li.removeClass('current completed').addClass('disabled')
                        .attr('aria-selected', 'false')
                        .attr('aria-disabled', 'true');
                    if (index === 0) {
                        $li.css('--line-color', 'transparent');
                    }
                } else if (index < selectedIndex) {
                    $li.removeClass('disabled current').addClass('completed')
                        .attr('aria-selected', 'false')
                        .attr('aria-disabled', 'false');
                    $li.css('--line-color', '#28a733');
                } else if (index === selectedIndex) {
                    $li.removeClass('disabled completed').addClass('current')
                        .attr('aria-selected', 'true')
                        .attr('aria-disabled', 'false');
                    if (index > 0) {
                        $li.css('--line-color', '#28a733');
                    } else {
                        $li.css('--line-color', 'transparent');
                    }
                } else {
                    $li.removeClass('current completed').addClass('disabled')
                        .attr('aria-selected', 'false')
                        .attr('aria-disabled', 'true');
                    $li.css('--line-color', 'transparent');
                }
            });
        }

        $('#document_status').on('change', function() {
            const selectedStatus = $(this).val();
            const selectedIndex = selectedStatus ? $('#document_status option:selected').index() - 1 : -1;
            $('#saveButton').prop('disabled', !selectedStatus);
            updateProgressBar(selectedIndex);
        });

        $('#saveButton').on('click', function() {
            const selectedStatus = $('#document_status').val();
            if (!selectedStatus) return;

            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            $(this).prop('disabled', true);

            $.ajax({
                url: '/save-document-status',
                type: 'POST',
                data: {
                    document: selectedStatus,
                    lead_id: '{{ optional($lead)->id ?? "" }}',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#saveButton').text('Saved!').prop('disabled', true);

                    setTimeout(function() {
                        $('#saveButton').text('Save Permanent').prop('disabled', false);

                        // Update the badge in real-time for both forwarded and unforwarded leads
                        if (response && response.status) {
                            const leadId = '{{ optional($lead)->id ?? "" }}';
                            const statusText = response.status; // Get the status from the JSON response

                            // Find the status badge by ID attribute (works for both forwarded and unforwarded)
                            const statusBadge = document.querySelector(`#status-badge-${leadId}`);
                            if (statusBadge) {
                                statusBadge.textContent = statusText;
                            } else {
                                // Fallback to class-based selector if ID-based doesn't work
                                $('.badge.bg-primary-subtle.text-primary').text(statusText);
                            }
                        }
                    }, 2000);
                },
                error: function(xhr) {
                    let errorMsg = 'Error saving status. Try again.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    $('#saveButton').text('Save Permanent').prop('disabled', false);
                    alert(errorMsg);
                }
            });
        });

        $(window).on('load', function() {
            console.log("Window loaded, initializing progress bar...");
            initializeProgressBar();
            const initialSelectedIndex = $('#document_status').val() ? $('#document_status option:selected').index() - 1 : -1;
            updateProgressBar(initialSelectedIndex);
        });
    });
</script>

<div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="forwardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forwardModalLabel">Forward Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="forwardForm" method="POST"
                    action="{{ optional($lead) ? route('backend.lead.forward', ['id' => $lead->id, 'type' => 'lead']) : '#' }}"
                    onsubmit="return confirmSubmission()">
                    @csrf
                    <input type="hidden" name="document_id" value="{{ optional($lead)->id ?? '' }}">
                    <input type="hidden" name="selected_user_id" id="selected-user-id">
                    <input type="hidden" name="selected_user_email" id="selected-user-email">
                    <input type="hidden" name="is_forwarded" value="1">
                    <div class="mb-3">
                        <label for="selected-user-name" class="form-label">Select User:</label>
                        <select class="form-select" id="selected-user-name" onchange="updateUserDetails(this)">
                            <option value="">-- Select User --</option>
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}" data-email="{{ $user->email }}">
                                {{ $user->name ?? 'User' }} {{ $user->last ?? '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes:</label>
                        <textarea id="notes" name="notes" rows="4" class="form-control"
                            placeholder="Enter notes"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="send_email" checked>
                        <label class="form-check-label">Send Email</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showForwardModal() {
        const forwardModal = new bootstrap.Modal(document.getElementById('forwardModal'));
        forwardModal.show();
    }

    function updateUserDetails(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        document.getElementById('selected-user-id').value = selectedOption.value;
        document.getElementById('selected-user-email').value = selectedOption.dataset.email;
    }

    function confirmSubmission() {
        showConfirmation(
            'Confirm Submission',
            'Are you sure you want to submit this application?',
            'Yes, submit it!',
            'No, cancel',
            () => {
                document.getElementById('forwardForm').submit();
                console.log('Submission confirmed');
            },
            () => {
                showAlert('info', 'Cancelled', 'Submission was cancelled.');
                console.log('Submission cancelled');
            },
            'question',
            'swal-custom-ok-button'
        );
        return false;
    }

    function makeEditable(fieldValueElement) {
        const valueSpan = fieldValueElement.querySelector('.field-value-content');
        const editIcon = fieldValueElement.querySelector('.edit-icon');

        // Prevent re-initializing if already in edit mode for this element
        if (fieldValueElement.querySelector('input.form-control-sm')) {
            return;
        }

        const originalValue = valueSpan.textContent.trim();
        const fieldContainer = fieldValueElement.closest('[data-field]');
        const fieldName = fieldContainer ? fieldContainer.dataset.field : '';
        let isProcessingSave = false; // Flag to prevent multiple save/confirmation attempts

        const input = document.createElement('input');
        input.type = 'text';
        input.value = originalValue;
        input.className = 'form-control form-control-sm'; // Match your styling

        const restoreOriginalView = (displayValue) => {
            valueSpan.textContent = displayValue;
            // Ensure input is still a child of fieldValueElement before replacing
            if (input.parentNode === fieldValueElement) {
                fieldValueElement.replaceChild(valueSpan, input);
            } else if (input.parentNode) { // Fallback if DOM was manipulated differently
                input.replaceWith(valueSpan);
            }

            if (editIcon) {
                editIcon.style.display = ''; // Let CSS hover rules take over
            }
            // Remove event listeners to prevent memory leaks or multiple triggers
            input.removeEventListener('keydown', handleKeydown);
            input.removeEventListener('blur', handleBlur);
            isProcessingSave = false; // Reset the flag
        };

        const processSave = (newValue) => {
            if (isProcessingSave) {
                return; // Already processing, do nothing
            }
            isProcessingSave = true;

            if (newValue === originalValue) {
                restoreOriginalView(originalValue); // No change, no confirmation needed
                return;
            }

            showConfirmation(
                'Confirm Update',
                'Are you sure you want to update this field?',
                'Yes, update it!',
                'No, cancel',
                () => { // onConfirm
                    restoreOriginalView(newValue); // Update UI
                    if (fieldName) {
                        updateField(fieldName, newValue); // Call backend update
                    }
                    // isProcessingSave is reset by restoreOriginalView
                },
                () => { // onCancel
                    restoreOriginalView(originalValue); // Revert UI
                    // isProcessingSave is reset by restoreOriginalView
                },
                'question',
                'swal-custom-update-button'
            );
        };

        const handleKeydown = (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(blurTimeout); // Prevent delayed blur from also triggering
                const newValue = input.value.trim();
                processSave(newValue);
            } else if (e.key === 'Escape') {
                clearTimeout(blurTimeout); // Prevent delayed blur
                restoreOriginalView(originalValue);
            }
        };

        let blurTimeout;
        const handleBlur = () => {
            // If a SweetAlert is currently open, don't let blur interfere immediately.
            // The SweetAlert interaction (confirm/cancel) will handle the next steps.
            if (Swal.isVisible()) {
                return;
            }
            clearTimeout(blurTimeout);
            blurTimeout = setTimeout(() => {
                // Only process if the input is still in the DOM and part of the field value element
                // and we are not already in the middle of a save (e.g., triggered by Enter)
                if (input.parentNode === fieldValueElement && !isProcessingSave) {
                    const newValue = input.value.trim();
                    processSave(newValue);
                }
            }, 200); // Slightly increased delay to be safer
        };

        if (editIcon) {
            editIcon.style.display = 'none'; // Hide icon during edit mode
        }

        // Replace the valueSpan with the input field
        if (valueSpan.parentNode === fieldValueElement) {
            fieldValueElement.replaceChild(input, valueSpan);
        } else {
            // Fallback if DOM structure is slightly different, though the above should be correct
            valueSpan.replaceWith(input);
        }

        input.focus();
        input.select();

        input.addEventListener('keydown', handleKeydown);
        input.addEventListener('blur', handleBlur);
    }

    function toggleDropdown(icon) {
        const fieldValue = icon.closest('.field-value');
        const displayElement = fieldValue.querySelector('#user-display');
        const dropdown = fieldValue.querySelector('#user-dropdown');

        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            displayElement.classList.add('hidden');
        } else {
            dropdown.classList.add('hidden');
            displayElement.classList.remove('hidden');
        }
    }

    async function updateField(fieldName, newValue) {
        const leadId = window.location.pathname.split('/').pop();
        const token = document.querySelector('meta[name="csrf-token"]').content;

        // The confirmation has already happened in makeEditable/processSave.
        // Proceed directly with the update without showing another confirmation here.
        try {
            const response = await fetch(`/backend/leadform/${leadId}/update-field`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: fieldName,
                    value: newValue,
                    lead_id: leadId
                })
            });

            const data = await response.json();

            if (!data.success) {
                showAlert('error', 'Error!', data.message || 'Failed to update field', 'OK',
                    'swal-custom-delete-button');
                console.error('Error updating field:', data.message);
                // OPTIONAL: You might want to revert the UI change here if the backend fails.
                // This would involve finding the element and setting its text back to the originalValue.
                // This requires passing originalValue to updateField or re-querying it.
                // For now, this example keeps the optimistic UI update even on backend failure.
            } else {
                showAlert('success', 'Success!', 'Field updated successfully', 'OK',
                    'swal-custom-ok-button');
            }
        } catch (error) {
            showAlert('error', 'Error!', 'An error occurred while updating the field', 'OK',
                'swal-custom-delete-button');
            console.error('Error:', error);
            // OPTIONAL: Also consider reverting UI on network error.
        }
    }

    function updateUser(dropdown) {
        const userId = dropdown.value;
        const userName = dropdown.options[dropdown.selectedIndex].text;
        document.getElementById('user-display').textContent = userName;
        const leadId = window.location.pathname.split('/').pop();
        const token = document.querySelector('meta[name="csrf-token"]').content;

        showConfirmation(
            'Confirm Update',
            'Are you sure you want to update the creator?',
            'Yes, update it!',
            'No, cancel',
            async () => {
                    try {
                        const response = await fetch(`/backend/leadform/${leadId}/update-creator`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                created_by: userId,
                                lead_id: leadId
                            })
                        });

                        const data = await response.json();

                        if (!data.success) {
                            showAlert('error', 'Error!', data.message || 'Failed to update creator', 'OK',
                                'swal-custom-delete-button');
                            console.error('Error updating creator:', data.message);
                        } else {
                            showAlert('success', 'Success!', 'Creator updated successfully', 'OK',
                                'swal-custom-ok-button');
                            dropdown.classList.add('hidden');
                            document.getElementById('user-display').classList.remove('hidden');
                        }
                    } catch (error) {
                        showAlert('error', 'Error!', 'An error occurred while updating the creator', 'OK',
                            'swal-custom-delete-button');
                        console.error('Error:', error);
                    }
                },
                () => {
                    const originalCreator = document.getElementById('user-display').textContent;
                    for (let i = 0; i < dropdown.options.length; i++) {
                        if (dropdown.options[i].text === originalCreator) {
                            dropdown.selectedIndex = i;
                            break;
                        }
                    }
                    dropdown.classList.add('hidden');
                    document.getElementById('user-display').classList.remove('hidden');
                },
                'question',
                'swal-custom-update-button'
        );
    }
</script>

<script>
    $(document).ready(function() {
        // Global CSRF token setup for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Create Comment Form Submit Handler
        $(document).on('submit', '#comment-form', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const formData = form.serialize();

            // Disable submit button and show loading state
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Show custom alert
                        showCustomAlert(response.message, 'success');

                        // Clear form
                        form.trigger('reset');

                        // Reset date/time picker UI
                        const datePicker = document.getElementById('datePicker');
                        const timePicker = document.getElementById('timePicker');
                        const dateTimeBtn = document.getElementById('dateTimeBtn');
                        const selectedDateTime = document.getElementById('selectedDateTime');

                        const today = new Date();
                        const formattedDate = today.toISOString().split('T')[0];
                        datePicker.value = formattedDate;
                        const hours = String(today.getHours()).padStart(2, '0');
                        const minutes = String(today.getMinutes()).padStart(2, '0');
                        timePicker.value = `${hours}:${minutes}`;
                        dateTimeBtn.textContent = 'Select Date & Time';
                        selectedDateTime.value = '';

                        // Append new comment
                        $('#comments-list').prepend(response.data.html);
                    } else {
                        showCustomAlert(response.message || 'Something went wrong.', 'danger');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while processing your request';
                    showCustomAlert('error', errorMsg);
                },
                complete: function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html('Save Comment');
                }
            });
        });

        // Function to refresh comments list
        // In records.blade.php, inside the <script> tag at the end.
        function refreshCommentsList() {
            const container = $('#comments-list');
            if (!container.length) return;

            container.html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading comments...</div>');

            const path = window.location.pathname;
            let url;
            let ajaxData = {};
            const currentLeadId = '{{ optional($lead)->id ?? "" }}'; // Safely get current lead ID

            // Determine URL and data based on context
            // This logic assumes 'lead-details' or 'leadform' in path means it's for a specific lead.
            if (path.includes('lead-details') || path.includes('leadform') || path.includes('records')) { // Added 'records' as it's part of view name
                if (!currentLeadId) {
                    console.error("Cannot refresh comments: Lead ID is missing on this page but expected.");
                    container.html('<div class="alert alert-warning">Cannot load comments: Lead information is missing.</div>');
                    return;
                }
                // Use the generic loadComments route and pass lead_id.
                // This matches the controller method being debugged.
                url = '{{ route("lead.comments.load") }}'; // Use named route if available
                ajaxData = {
                    lead_id: currentLeadId
                };
            } else if (path.includes('enquiryhistory')) {
                // Assuming enquiryhistory uses a different loading mechanism or doesn't always need lead_id
                url = '/backend/enquiryhistory/load'; // Adjust if this is a named route
            } else {
                // Fallback: if no specific context, attempt to load general comments
                // This might mean $lead will be null in the controller if lead_id is not sent.
                // The controller should handle this scenario by possibly rendering a different view or error.
                // For 'records.blade.php', this path should ideally not be taken if it requires a lead.
                console.warn("Refreshing comments without a clear lead context. URL: /backend/leadcomment/load");
                url = '{{ route("lead.comments.load") }}';
            }

            $.ajax({
                url: url,
                type: 'GET',
                data: ajaxData, // Pass lead_id if determined above
                success: function(response) {
                    // Handle potential JSON error response from controller
                    if (typeof response === 'object' && response.html) { // Check for our custom error structure
                        container.html(response.html);
                    } else if (typeof response === 'string') {
                        container.html(response); // Assume response is HTML string
                    } else {
                        console.error('Unexpected response format:', response);
                        container.html('<div class="alert alert-danger">Error: Received an unexpected response from the server.</div>');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to load comments. Please refresh the page.';
                    if (xhr.responseJSON && xhr.responseJSON.html) {
                        errorMsg = xhr.responseJSON.html;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        // Try to parse if it's a JSON string with an error message
                        try {
                            const errData = JSON.parse(xhr.responseText);
                            if (errData.message) errorMsg = errData.message;
                        } catch (e) {
                            /* Not JSON or no message field */
                        }
                    }
                    console.error("Error loading comments:", xhr.status, xhr.statusText, xhr.responseText);
                    container.html(`<div class="alert alert-danger">${errorMsg}</div>`);
                }
            });
        }

        // Toast notification function
        function showCustomAlert(message, type = 'success', duration = 3000) {
            const container = document.getElementById('toast-container');

            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert customize-alert alert-dismissible border-${type} text-${type} fade show remove-close-icon`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.style.marginBottom = '10px';
            alertDiv.innerHTML = `
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="d-flex align-items-center me-3 me-md-0 me-md-3">
            <i class="ti ti-info-circle fs-5 me-2 text-${type}"></i>
            ${message}
        </div>
    `;

            // Append to container
            container.appendChild(alertDiv);

            // Fade out and remove after duration
            setTimeout(() => {
                alertDiv.classList.remove('show');
                alertDiv.classList.add('fade');
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 500);
            }, duration);
        }
    });
</script>
@endsection