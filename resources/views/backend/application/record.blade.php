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
        max-height: 350px;
        overflow-y: auto;
        padding: 10px;
    }

    .progress-bar {
        width: 100%;
        border-radius: 5px;
        height: 10px;
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

    #progressBar {
        list-style: none;
        padding: 0;
        margin: 20px 0;
        display: flex;
        justify-content: space-between;
        position: relative;
    }

    #progressBar li {
        flex: 1;
        text-align: center;
        position: relative;
        font-size: 0.85rem;
        color: #6c757d;
    }

    #progressBar li a {
        text-decoration: none;
        color: inherit;
        display: block;
        padding: 10px 5px;
    }

    #progressBar li .step {
        display: block;
        width: 30px;
        height: 30px;
        line-height: 28px;
        border: 1px solid #ced4da;
        border-radius: 50%;
        margin: 0 auto 10px auto;
        background-color: #fff;
        color: #6c757d;
        font-weight: bold;
    }

    #progressBar li.disabled {
        color: #adb5bd;
    }

    #progressBar li.disabled .step {
        border-color: #e9ecef;
        color: #adb5bd;
    }

    #progressBar li.current {
        color: #0d6efd;
        font-weight: bold;
    }

    #progressBar li.current .step {
        border-color: #0d6efd;
        background-color: #0d6efd;
        color: #fff;
    }

    #progressBar li.completed {
        color: #198754;
    }

    #progressBar li.completed .step {
        border-color: #198754;
        background-color: #198754;
        color: #fff;
    }

    #progressBar li:not(:last-child)::after {
        content: '';
        position: absolute;
        width: calc(100% - 40px);
        height: 2px;
        background-color: #e9ecef;
        top: 15px;
        left: calc(50% + 20px);
        z-index: -1;
        transition: background-color 0.3s ease;
    }

    #progressBar li.completed::after {
        background-color: #198754;
    }

    #progressBar li.current::after {
        background-color: #198754;
    }

    #progressBar li:first-child::after {
        background-color: var(--line-color, transparent);
    }

    #progressBar li.current:first-child::after {
        background-color: transparent;
    }

    #saveButton .spinner-border {
        width: 1rem;
        height: 1rem;
    }

    .swal-custom-popup {
        font-size: 0.9rem;
    }

    .field-value .edit-icon {
        display: none;
        opacity: 0;
        transition: opacity 0.15s ease-in-out;
    }

    .field-value:hover .edit-icon {
        display: inline-block;
        opacity: 1;
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

    .text-icon-person {
        color: #C62828;
        /* Soft Red */
    }

    .text-icon-telephone {
        color: #43A047;
        /* Fresh Green */
    }

    .text-icon-envelope {
        color: #1E88E5;
        /* Clean Blue */
    }

    .text-icon-book {
        color: #D81B60;
        /* Bright Gold */
    }

    .text-icon-award {
        color: #43A047;
        /* Elegant Rose */
    }

    .text-icon-earmark-1 {
        color: #C62828;
        /* Elegant Rose */
    }

    .text-icon-check {
        color: #43A047;
        /* Refined Cyan */
    }

    .text-icon-book-half {
        color: #8E24AA;
        /* Royal Purple */
    }

    .text-icon-calendar {
        color: #1E88E5;
        /* Orange (warm but not aggressive) */
    }

    .text-icon-building {
        color: #3949AB;
        /* Steady Indigo */
    }

    .text-icon-globe {
        color: #C62828;
        /* Deep Crimson */
    }

    .text-icon-calendar-event {
        color: #2E7D32;
        /* Grounded Green */
    }

    .text-icon-wallet {
        color: #FFB300;
        /* Warm Yellow-Gold */
    }
</style>
<script>
    window.partners = @json($partners);
</script>
<div class="position-relative overflow-hidden">
    <div class="card mx-9 mt-n10">
        <div class="card-body pb-0">
            <div class="d-md-flex align-items-center justify-content-between text-center text-md-start">
                <div class="d-md-flex align-items-center">
                    <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
                        <img src="{{ $application->avatar ? asset('storage/avatars/' . $application->avatar) : asset('assets/images/profile/user-1.jpg') }}" alt="spike-img" class="img-fluid rounded-circle" width="100" height="100">
                        <span class="text-bg-primary rounded-circle text-white d-flex align-items-center justify-content-center position-absolute bottom-0 end-0 p-1 border border-2 border-white">
                            <i class="bi bi-plus text-white"></i>
                        </span>
                    </div>
                    <div class="ms-0 ms-md-3 mb-9 mb-md-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-1">
                            <h4 class="me-7 mb-0 fs-7">{{ $application->name }}</h4>
                            <span class="badge fs-2 fw-bold rounded-pill bg-primary-subtle text-primary border-primary border">{{ $application->status ?? '' }}</span>
                        </div>
                        <p class="fs-4 mb-1">Student</p>
                    </div>
                </div>
            </div>
            <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start" id="pills-tab" role="tablist">
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true">
                        <i class="bi bi-person me-0 me-md-6 fs-6 text-primary"></i>
                        <span class="d-none d-md-block">Student Profile</span>
                    </button>
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-doc-tab" data-bs-toggle="pill" data-bs-target="#pills-doc" type="button" role="tab" aria-controls="pills-doc" aria-selected="">
                        <i class="bi bi-file-earmark me-0 me-md-6 fs-6 text-primary"></i>
                        <span class="d-none d-md-block">doc</span>
                    </button>
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-Document-tab" data-bs-toggle="pill" data-bs-target="#pills-Document" type="button" role="tab" aria-controls="pills-Document" aria-selected="false">
                        <i class="bi bi-files me-0 me-md-6 fs-6 text-primary"></i>
                        <span class="d-none d-md-block">Student Document</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-status-tab" data-bs-toggle="pill" data-bs-target="#pills-status" type="button" role="tab" aria-controls="pills-status" aria-selected="false">
                        <i class="bi bi-card-text me-0 me-md-6 fs-6 text-primary"></i>
                        <span class="d-none d-md-block">add To Another University</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="tab-content mx-10" id="pills-tabContent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
        <div class="row">
            <div class="col-lg-4">
                <div class="card ">
                    <div class="card-body p-4">
                        <div class="py-9">
                            <h5 class="mb-9">Personal information</h5>
                            <div class="d-flex align-items-center mb-9" data-field="name">
                                <div class="text-icon-person fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Name</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->name }}
                                        </span>
                                        <span class="edit-icon ms-2">
                                            <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="phone">
                                <div class="text-icon-telephone fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Phone number</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->phone }}
                                        </span>
                                        <span class="edit-icon ms-2">
                                            <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="email">
                                <div class="text-icon-envelope fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Email</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->email }}
                                        </span>
                                        <span class="edit-icon ms-2">
                                            <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
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
                                <div class="text-icon-book fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-book"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">English Test</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->englishTest }}
                                        </span>
                                        <span class="edit-icon ms-2">
                                            <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="gpa">
                                <div class="text-icon-award fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-award"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">GPA</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->gpa }}
                                        </span>
                                        <span class="edit-icon ms-2">
                                            <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="score">
                                <div class="text-icon-earmark-1 fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi-file-earmark-text"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Score</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->score }}
                                        </span>
                                        <span class="edit-icon ms-2">
                                            <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="englishscore">
                                <div class="text-icon-check fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi-translate"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">English Score</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->englishscore }}
                                        </span>
                                        <span class="edit-icon ms-2">
                                            <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="englishtheory">
                                <div class="text-icon-book-half fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-book-half"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">English Theory</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->englishtheory }}
                                        </span>
                                        <span class="edit-icon ms-2">
                                            <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-9" data-field="passed">
                                <div class="text-icon-calendar fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="bi bi-calendar"></i>
                                </div>
                                <div class="ms-6">
                                    <h6 class="mb-1">Passed Date</h6>
                                    <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                        <span class="field-value-content">
                                            {{ $application->passed }}
                                        </span>
                                        <span class="edit-icon ms-2">
                                            <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
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
                            <div class="text-icon-building fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="ms-6">
                                <h6 class="mb-1">University</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->university }}
                                    </span>
                                    <span class="edit-icon ms-2">
                                        <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9" data-field="country">
                            <div class="text-icon-globe fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="bi bi-globe"></i>
                            </div>
                            <div class="ms-6">
                                <h6 class="mb-1">Country</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->country }}
                                    </span>
                                    <span class="edit-icon ms-2">
                                        <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9" data-field="intake">
                            <div class="text-icon-calendar-event fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div class="ms-6">
                                <h6 class="mb-1">Intake</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->intake }}
                                    </span>
                                    <span class="edit-icon ms-2">
                                        <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9" data-field="course">
                            <div class="text-icon-book fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="bi bi-book"></i>
                            </div>
                            <div class="ms-6">
                                <h6 class="mb-1">Course</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->course }}
                                    </span>
                                    <span class="edit-icon ms-2">
                                        <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9" data-field="fee">
                            <div class="text-icon-wallet fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="bi bi-wallet"></i>
                            </div>
                            <div class="ms-6">
                                <h6 class="mb-1">Fee</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        {{ $application->fee }}
                                    </span>
                                    <span class="edit-icon ms-2">
                                        <i class="bi bi-pencil text-success" onclick="makeEditable(this.closest('.field-value'))"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                             <div class="d-flex align-items-center mb-9" data-field="partnerDetails">
                            <div class="bg-info-subtle text-info fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="ti ti-wallet"></i>
                            </div>
                            <div class="ms-6">
                                <h6 class="mb-1">Partner</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    <span class="field-value-content">
                                        @if($application->partner && $application->partner->agency_name)
                                            {{ $application->partner->agency_name }}
                                        @elseif($application->partnerDetails)
                                            Partner ID: {{ $application->partnerDetails }}
                                        @else
                                            No Partner Assigned
                                        @endif
                                    </span>
                                    <span class="edit-icon ms-2">
                                        <i class="fas fa-pencil-alt" onclick="makeEditable(this.closest('.field-value'))"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <div class="bg-info-subtle text-info fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="ti ti-wallet"></i>
                            </div>
                            <div class="ms-6">
                                <h6 class="mb-1">Document Forwarded From</h6>
                                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                                    @if($lead && $lead->user)
                                    <span class="field-value-content">
                                        {{ $lead->user->name }}
                                    </span>
                                    @else
                                    <span class="field-value-content">
                                        User information not available
                                    </span>
                                    @endif
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
                            <form action="{{ route('backend.enquiryhistory.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="application_id" value="{{ $application->id }}">
                                <input type="hidden" name="user_id" value="{{ Auth::check() ? Auth::id() : '' }}">
                                <div class="mb-3">
                                    <select id="application" name="commenttype" class="form-select" required>
                                        <option value="" disabled selected>Select type of</option>
                                        @foreach ($commentAdds as $commentadd)
                                        <option value="{{ $commentadd->applications }}">{{ $commentadd->applications }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="userprofile-quill-editors mb-3">
                                    <div id="editor-feeds">
                                        <textarea id="comment" name="comment" class="form-control" placeholder="Enter Your Comment" required></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary shadow-none">Post</button>
                            </form>
                        </div>
                    </div>
                    @foreach ($lead_comments as $lead_comment)
                    <div class="p-4 rounded-4 text-bg-light mb-3">
                        <div class="card-body border-bottom">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center gap-6 flex-wrap">
                                    @if($lead_comment->user && $lead_comment->user->avatar)
                                    <img src="{{ asset('storage/avatars/' . $lead_comment->user->avatar) }}" alt="user-img" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    @else
                                    <img src="{{ asset('/avatars/male-2.jpg') }}" alt="default-user-img" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    @endif
                                    <h6 class="mb-0">{{ $lead_comment->user->name ?? 'User' }}</h6>
                                    <span class="fs-2">
                                        <span class="p-1 text-bg-light rounded-circle d-inline-block"></span>
                                        {{ $lead_comment->created_at->diffForHumans() }}
                                    </span>
                                    <small class="badge text-bg-secondary">{{ $lead_comment->commenttype }}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="dropdown ms-auto">
                                        <a class="text-dark d-flex align-items-center justify-content-center bg-transparent p-2 fs-4 rounded-circle" href="javascript:void(0)" id="dropdownMenuButton{{ $loop->index }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $loop->index }}">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="openEditPopup('{{ $lead_comment->id }}', '{{ $lead_comment->commenttype }}', '{{ $lead_comment->comment }}')">
                                                    Edit Comment
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('comment.destroy', $lead_comment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?');" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">Delete Comment</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p class="text-dark my-3">{{ $lead_comment->comment }}</p>
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
            <div class="create-section">
                <h2 style="color:green; font-size:20px; margin-bottom:40px; font-family:poppins;">Upload Documents</h2>
                <div id="uploadContainer" class="upload-container">
                    <div class="upload-icon" id="uploadIcon">
                        <img src="{{ asset('img/wri.png') }}" alt="Upload icon">
                    </div>
                    <p class="upload-text" id="uploadText">Drag & drop files here</p>
                    <input type="file" id="fileInput" name="fileInput[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                    <div id="progressSection" class="progress-section" style="display: none;"></div>
                </div>
            </div>
            <div class="index-section">
                <h2 style="color:green; font-size:20px; margin-top:30px; font-family:poppins;">Uploaded Files</h2>
                <div class="index-section-content">
                    <table class="documents-table">
                        <tbody>
                            @forelse ($uploads as $upload)
                            @if ($upload->application_id == $application->id)
                            <tr>
                                <td>
                                    <i class="bi bi-check-circle text-success"></i>
                                    <div class="file-wrapper">
                                        <div class="file-icon">
                                            <i class="bi bi-file-earmark text-primary"></i>
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
                                    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
                                    <div class="action-buttons">
                                        <a href="{{ asset('storage/uploads/' . $upload->fileInput) }}"
                                            class="view-link"
                                            target="_blank" title="View">
                                            <i class="bi bi-eye text-info"></i>
                                        </a>
                                        <form action="{{ route('upload.destroy', $upload->id) }}"
                                            method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger" style="border: none; background: transparent; padding: 0; cursor:pointer;" title="Delete">
                                                <i class="bi bi-x-lg text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">No documents uploaded for this application yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-Document" role="tabpanel" aria-labelledby="pills-Document-tab" tabindex="0">
        <div id="tab4" class="section px-4 my-4 card card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-group mb-3 w-50">
                    <label for="document_status" class="form-label">
                        Document Status
                    </label>
                    <select id="document_status" name="document_status"
                        class="form-select w-100">
                        <option value="">Select Status</option>
                        @foreach($documents as $document)
                        @if($document->status === 'application')
                        <option value="{{ $document->document }}"
                            {{ $application->status === $document->document ? 'selected' : '' }}>
                            {{ $document->document }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="my-auto">
                    <button id="saveButton" type="button" class="btn btn-primary" {{ empty($application->status) ? 'disabled' : '' }}>Save Permanent</button>
                </div>
            </div>
            <ul id="progressBar" role="tablist" class="flex justify-between my-4 p-0"></ul>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-status" role="tabpanel" aria-labelledby="pills-status-tab" tabindex="0">
        <div class="card w-100 mt-4">
            <div class="card-body">
                <div class="d-flex mb-3 align-items-center">
                    <h4 class="card-title mb-0">Add Application for Same Student</h4>
                </div>
                @if(session('success_student_store'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success_student_store') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session('error_student_store'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error_student_store') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <form action="{{ route('backend.application.studentstore') }}" method="POST">
                    @csrf
                    <input type="hidden" name="original_application_id" value="{{ $application->id }}">
                    <input type="hidden" name="name" value="{{ $application->name }}">
                    <input type="hidden" name="email" value="{{ $application->email }}">
                    <input type="hidden" name="phone" value="{{ $application->phone }}">
                    <div class="form-group mb-4">
                        <label for="studyLevel">Study Level:</label>
                        <select name="studyLevel" id="studyLevel" class="form-select">
                            <option value="Undergraduate" {{ old('studyLevel', $application->studyLevel) == 'Undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                            <option value="Postgraduate" {{ old('studyLevel', $application->studyLevel) == 'Postgraduate' ? 'selected' : '' }}>Postgraduate</option>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="country">Country:</label>
                        <select name="country" id="country" class="form-select">
                            <option value="">Select Country</option>
                            @foreach ($data_entries->unique('country')->sortBy('country') as $data_entrie)
                            <option value="{{ $data_entrie->country }}" {{ old('country') == $data_entrie->country ? 'selected' : '' }}>
                                {{ $data_entrie->country }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="newIntake">Intake:</label>
                        <select name="intake" id="newIntake" class="form-select">
                            <option value="">Select Intake</option>
                            @foreach ($data_entries->unique('newIntake')->sortBy('newIntake') as $data_entrie)
                            <option value="{{ $data_entrie->newIntake }}" {{ old('intake') == $data_entrie->newIntake ? 'selected' : '' }}>
                                {{ $data_entrie->newIntake }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="newUniversity">University:</label>
                        <select name="university" id="newUniversity" class="form-select">
                            <option value="">Select University</option>
                            @foreach ($data_entries->unique('newUniversity')->sortBy('newUniversity') as $data_entrie)
                            <option value="{{ $data_entrie->newUniversity }}" {{ old('university') == $data_entrie->newUniversity ? 'selected' : '' }}>
                                {{ $data_entrie->newUniversity }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="newCourse">Course:</label>
                        <select name="course" id="newCourse" class="form-select">
                            <option value="">Select Course</option>
                            @foreach ($data_entries->unique('newCourse')->sortBy('newCourse') as $data_entrie)
                            <option value="{{ $data_entrie->newCourse }}" {{ old('course') == $data_entrie->newCourse ? 'selected' : '' }}>
                                {{ $data_entrie->newCourse }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="fee">Fee:</label>
                        <input type="text" name="fee" id="fee" class="form-control" value="{{ old('fee') }}" placeholder="Enter Fee Amount">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" onsubmit="event.preventDefault(); updateComment();">
                    <div class="modal-body">
                        <input type="hidden" id="editCommentId" name="comment_id">
                        <div class="mb-3">
                            <label for="editCommentType" class="form-label">Comment Type</label>
                            <select id="editCommentType" name="commenttype" class="form-select" required>
                                <option value="" disabled>Select type</option>
                                @foreach ($commentAdds as $commentadd)
                                <option value="{{ $commentadd->applications }}">{{ $commentadd->applications }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editCommentText" class="form-label">Comment</label>
                            <textarea class="form-control" id="editCommentText" name="comment" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let currentActiveEditCancelHandler = null;
    let currentActiveEditElement = null;

    function makeEditable(element) {
        if (currentActiveEditCancelHandler && currentActiveEditElement !== element) {
            currentActiveEditCancelHandler();
        }

        const valueSpan = element.querySelector('.field-value-content');
        const editIcon = element.querySelector('.edit-icon');

        if (!valueSpan) {
            console.error("Could not find '.field-value-content'. Structure may be incorrect.", element);
            return;
        }

        const fieldContainer = element.closest('[data-field]');
        if (!fieldContainer) {
            console.error("Could not find parent with 'data-field'.", element);
            return;
        }
        const fieldName = fieldContainer.getAttribute('data-field');

        if (fieldName === 'partnerDetails') {
            makePartnerEditable(element, valueSpan, editIcon, fieldName);
            return;
        }

        const currentValue = valueSpan.textContent.trim();

        const input = document.createElement('input');
        input.type = 'text';
        input.value = currentValue;
        input.className = 'form-control form-control-sm d-inline-block w-auto';

        const saveBtn = document.createElement('button');
        saveBtn.innerHTML = '<i class="fas fa-check"></i>';
        saveBtn.className = 'btn btn-sm btn-success ms-1 py-1 px-2';
        saveBtn.title = 'Save';

        const cancelBtn = document.createElement('button');
        cancelBtn.innerHTML = '<i class="fas fa-times"></i>';
        cancelBtn.className = 'btn btn-sm btn-secondary ms-1 py-1 px-2';
        cancelBtn.title = 'Cancel';

        const editContainer = document.createElement('div');
        editContainer.className = 'd-inline-flex align-items-center';
        editContainer.appendChild(input);
        editContainer.appendChild(saveBtn);
        editContainer.appendChild(cancelBtn);

        const restoreViewAndClearState = (displayValue = currentValue) => {
            valueSpan.innerHTML = '';
            valueSpan.textContent = displayValue;
            if (editIcon) {
                editIcon.style.display = '';
            }
            if (currentActiveEditElement === element) {
                currentActiveEditCancelHandler = null;
                currentActiveEditElement = null;
            }
        };

        saveBtn.addEventListener('click', function() {
            const newValue = input.value.trim();
            if (newValue === currentValue) {
                restoreViewAndClearState(currentValue);
                return;
            }

            const applicationIdInput = document.querySelector('input[name="application_id"]');
            if (!applicationIdInput) {
                console.error("Application ID input not found.");
                showNotification('Error: Application ID missing.', 'error');
                restoreViewAndClearState(currentValue);
                return;
            }
            const applicationId = applicationIdInput.value;

            fetch(`/backend/application/${applicationId}/update-field`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ field: fieldName, value: newValue })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw new Error(errData.message || `Request failed: ${response.status}`);
                    }).catch(() => {
                        throw new Error(`Request failed: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    restoreViewAndClearState(newValue);
                    showNotification('Field updated successfully!', 'success');
                } else {
                    showNotification('Update failed: ' + (data.message || 'Unknown error'), 'error');
                    restoreViewAndClearState(currentValue);
                }
            })
            .catch(error => {
                console.error('Error updating field:', error);
                showNotification('Update failed: ' + error.message, 'error');
                restoreViewAndClearState(currentValue);
            });
        });

        cancelBtn.addEventListener('click', function() {
            restoreViewAndClearState(currentValue);
        });

        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveBtn.click();
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cancelBtn.click();
            }
        });

        input.addEventListener('blur', function() {
            setTimeout(() => {
                if (document.body.contains(editContainer) && !editContainer.contains(document.activeElement)) {
                    cancelBtn.click();
                }
            }, 0);
        });

        if (editIcon) {
            editIcon.style.display = 'none';
        }
        valueSpan.innerHTML = '';
        valueSpan.appendChild(editContainer);
        input.focus();
        input.select();

        currentActiveEditElement = element;
        currentActiveEditCancelHandler = () => restoreViewAndClearState(currentValue);
    }

    function makePartnerEditable(element, valueSpan, editIcon, fieldName) {
        const currentValue = valueSpan.textContent.trim();

        const dropdown = document.createElement('select');
        dropdown.className = 'form-control form-control-sm d-inline-block w-auto';
        dropdown.style.minWidth = '200px';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Select Partner';
        dropdown.appendChild(defaultOption);

        const partners = window.partners || [];
        partners.forEach(partner => {
            const option = document.createElement('option');
            option.value = partner.id;
            option.textContent = partner.agency_name;

            if (currentValue.includes(partner.agency_name)) {
                option.selected = true;
            }

            dropdown.appendChild(option);
        });

        const saveBtn = document.createElement('button');
        saveBtn.innerHTML = '<i class="fas fa-check"></i>';
        saveBtn.className = 'btn btn-sm btn-success ms-1 py-1 px-2';
        saveBtn.title = 'Save';

        const cancelBtn = document.createElement('button');
        cancelBtn.innerHTML = '<i class="fas fa-times"></i>';
        cancelBtn.className = 'btn btn-sm btn-secondary ms-1 py-1 px-2';
        cancelBtn.title = 'Cancel';

        const editContainer = document.createElement('div');
        editContainer.className = 'd-inline-flex align-items-center';
        editContainer.appendChild(dropdown);
        editContainer.appendChild(saveBtn);
        editContainer.appendChild(cancelBtn);

        const restoreViewAndClearState = (displayValue = currentValue) => {
            valueSpan.innerHTML = '';
            valueSpan.textContent = displayValue;
            if (editIcon) {
                editIcon.style.display = '';
            }
            if (currentActiveEditElement === element) {
                currentActiveEditCancelHandler = null;
                currentActiveEditElement = null;
            }
        };

        saveBtn.addEventListener('click', function() {
            const selectedOption = dropdown.options[dropdown.selectedIndex];
            const selectedValue = dropdown.value;
            const selectedText = selectedOption.text;

            const applicationIdInput = document.querySelector('input[name="application_id"]');
            if (!applicationIdInput) {
                console.error("Application ID input not found.");
                showNotification('Error: Application ID missing.', 'error');
                restoreViewAndClearState(currentValue);
                return;
            }
            const applicationId = applicationIdInput.value;

            fetch(`/backend/application/${applicationId}/update-field`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
               body: JSON.stringify({ field: fieldName, value: selectedValue })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw new Error(errData.message || `Request failed: ${response.status}`);
                    }).catch(() => {
                        throw new Error(`Request failed: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const displayValue = selectedValue ? selectedText : 'No Partner Assigned';
                    restoreViewAndClearState(displayValue);
                    showNotification('Partner updated successfully!', 'success');
                } else {
                    showNotification('Update failed: ' + (data.message || 'Unknown error'), 'error');
                    restoreViewAndClearState(currentValue);
                }
            })
            .catch(error => {
                console.error('Error updating partner:', error);
                showNotification('Update failed: ' + error.message, 'error');
                restoreViewAndClearState(currentValue);
            });
        });

        cancelBtn.addEventListener('click', function() {
            restoreViewAndClearState(currentValue);
        });

        dropdown.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cancelBtn.click();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                saveBtn.click();
            }
        });

        if (editIcon) {
            editIcon.style.display = 'none';
        }
        valueSpan.innerHTML = '';
        valueSpan.appendChild(editContainer);
        dropdown.focus();

        currentActiveEditElement = element;
        currentActiveEditCancelHandler = () => restoreViewAndClearState(currentValue);
    }

    function showNotification(message, type) {
        Swal.fire({
            title: type === 'success' ? 'Success!' : 'Error!',
            text: message,
            icon: type,
            timer: type === 'success' ? 2500 : 4000,
            showConfirmButton: false,
            timerProgressBar: true,
            customClass: { popup: 'swal-custom-popup' }
        });
    }

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
            if (!this.uploadContainer || !this.fileInput || !this.progressSection || !this.uploadText || !this.uploadIcon) {
                console.warn("File upload elements missing, manager not fully initialized.");
                return;
            }
            this.fileInput.addEventListener('change', (e) => { this.handleFiles(Array.from(e.target.files)); });
            this.uploadContainer.addEventListener('click', () => this.fileInput.click());
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => { this.uploadContainer.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); }); });
            ['dragenter', 'dragover'].forEach(evt => { this.uploadContainer.addEventListener(evt, () => { this.uploadContainer.classList.add('dragover'); }); });
            ['dragleave', 'drop'].forEach(evt => { this.uploadContainer.addEventListener(evt, () => { this.uploadContainer.classList.remove('dragover'); }); });
            this.uploadContainer.addEventListener('drop', (e) => { this.handleFiles(Array.from(e.dataTransfer.files)); });
        }

        handleFiles(newFiles) {
            if (!this.progressSection) return;
            let hasFilesToShow = this.progressSection.children.length > 0 || newFiles.length > 0;
            if (newFiles.length > 0) { hasFilesToShow = true; }

            newFiles.forEach(file => {
                if (file.size <= 5 * 1024 * 1024) {
                    if (!this.files.has(file.name)) {
                        this.addFile(file);
                        this.uploadFile(file);
                    } else {
                        showNotification(`File "${file.name}" already added or uploading.`, 'warning');
                    }
                } else {
                    showNotification(`File ${file.name} is too large. Maximum size is 5MB.`, 'error');
                }
            });

            if (hasFilesToShow) {
                this.uploadText.style.display = 'none';
                this.uploadIcon.style.display = 'none';
                this.uploadContainer.style.padding = '0';
                this.uploadContainer.style.justifyContent = 'flex-start';
                this.progressSection.style.display = 'block';
            }
        }

        addFile(file) {
            if (!this.files.has(file.name)) {
                this.files.set(file.name, { file, progress: 0 });
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.id = `file-${file.name}`;
                fileItem.innerHTML = `
                    <div class="file-details" style="padding: 10px;">
                        <div style="display: flex; align-items: flex-start; margin-bottom: 10px;">
                            <div class="file-name" style="color: green; text-align: left; margin-left: 0; padding-left: 0;">${file.name}</div>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill-${file.name}"></div>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                            <div class="file-size">${(file.size / 1024).toFixed(1)} KB</div>
                            <div class="progress-percentage" id="progressPercentage-${file.name}">0%</div>
                        </div>
                        <div class="upload-status small text-muted" id="uploadStatus-${file.name}">Uploading...</div>
                    </div>`;
                this.progressSection.appendChild(fileItem);
            }
        }

        async uploadFile(file) {
            const formData = new FormData();
            formData.append('fileInput[]', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('application_id', '{{ $application->id }}');

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("backend.upload.store") }}', true);

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    this.updateProgress(file.name, percentComplete);
                }
            });

            xhr.onload = () => {
                this.files.delete(file.name);
                if (xhr.status === 200) {
                    this.updateUploadStatus(file.name, 'success');
                    showNotification(`File "${file.name}" uploaded. Reloading...`, 'success');
                    setTimeout(() => { window.location.reload(); }, 1000);
                } else {
                    let errMsg = 'Upload Failed';
                    try { errMsg = JSON.parse(xhr.responseText).message || errMsg; } catch(e){}
                    this.updateUploadStatus(file.name, 'error', errMsg);
                    this.checkIfResetNeeded();
                }
            };

            xhr.onerror = () => {
                this.files.delete(file.name);
                this.updateUploadStatus(file.name, 'error', 'Network Error');
                this.checkIfResetNeeded();
            };

            xhr.send(formData);
        }

        checkIfResetNeeded() {
            if (this.files.size === 0 && (!this.progressSection || this.progressSection.children.length === 0)) {
                this.resetUploadContainer();
            }
        }

        resetUploadContainer() {
            if (!this.uploadText || !this.uploadIcon || !this.uploadContainer || !this.progressSection) return;
            this.uploadText.style.display = 'block';
            this.uploadIcon.style.display = 'block';
            this.uploadContainer.style.padding = '1rem';
            this.uploadContainer.style.justifyContent = 'center';
            this.progressSection.style.display = 'none';
            this.progressSection.innerHTML = '';
        }

        updateProgress(fileName, percent) {
            const progressFill = document.getElementById(`progressFill-${fileName}`);
            const progressPercentage = document.getElementById(`progressPercentage-${fileName}`);
            if (progressFill && progressPercentage) {
                progressFill.style.width = `${Math.round(percent)}%`;
                progressPercentage.textContent = `${Math.round(percent)}%`;
            }
        }

        updateUploadStatus(fileName, status, message = '') {
            const statusElement = document.getElementById(`uploadStatus-${fileName}`);
            const fileItem = document.getElementById(`file-${fileName}`);
            if (statusElement && fileItem) {
                if (status === 'success') {
                    statusElement.innerHTML = '<span class="text-success fw-bold">Success</span>';
                    setTimeout(() => { if(fileItem) fileItem.remove(); this.checkIfResetNeeded(); }, 2000);
                } else if (status === 'error') {
                    statusElement.innerHTML = `<span class="text-danger fw-bold">Error: ${message}</span>`;
                    fileItem.style.border = '1px solid red';
                }
            }
        }
    }

    if (document.getElementById('uploadContainer')) {
        const uploadManager = new FileUploadManager();
    }

    function confirmDelete() {
        return confirm('Are you sure you want to delete this document?');
    }

    $(document).ready(function() {
        if ($('#progressBar').length > 0 && $('#document_status').length > 0) {
            const $progressBar = $('#progressBar');
            const $statusSelect = $('#document_status');
            const $saveButton = $('#saveButton');
            const applicationId = '{{ $application->id }}';
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            function getStatusOptions() {
                const options = [];
                $statusSelect.find('option').not(':first').each(function() {
                    options.push({ value: $(this).val(), text: $(this).text().trim() });
                });
                return options;
            }

            const statusSteps = getStatusOptions();

            function initializeProgressBar() {
                $progressBar.empty();
                if(statusSteps.length === 0) return;
                statusSteps.forEach((step, index) => {
                    const $li = $('<li>').attr('role', 'tab').addClass('disabled').attr('aria-disabled', 'true').attr('aria-selected', 'false');
                    const $a = $('<a>').attr('href', '#').html(`<span class="step">${index + 1}</span> ${step.text}`);
                    $li.append($a); $progressBar.append($li);
                });
            }

            function updateProgressBar(selectedIndex) {
                if(statusSteps.length === 0) return;
                $progressBar.find('li').each(function(index) {
                    const $li = $(this);
                    $li.removeClass('current completed disabled').attr('aria-selected', 'false').attr('aria-disabled', 'true');
                    if (selectedIndex === -1) { $li.addClass('disabled'); $li.css('--line-color', index === 0 ? 'transparent' : '#e9ecef'); }
                    else if (index < selectedIndex) { $li.addClass('completed').attr('aria-disabled', 'false'); $li.css('--line-color', '#198754'); }
                    else if (index === selectedIndex) { $li.addClass('current').attr('aria-selected', 'true').attr('aria-disabled', 'false'); $li.css('--line-color', index === 0 ? 'transparent' : '#198754'); }
                    else { $li.addClass('disabled'); $li.css('--line-color', '#e9ecef'); }
                });
                $progressBar.find('li:first-child').css('--line-color', 'transparent');
            }

            $statusSelect.on('change', function() {
                const selectedStatus = $(this).val();
                const selectedIndex = (selectedStatus && statusSteps.length > 0) ? statusSteps.findIndex(step => step.value === selectedStatus) : -1;
                $saveButton.prop('disabled', !selectedStatus);
                updateProgressBar(selectedIndex);
            });

            $saveButton.on('click', function() {
                const selectedStatus = $statusSelect.val(); if (!selectedStatus) return;
                const originalButtonText = $(this).html();
                $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
                $.ajax({
                    url: `/save-application-status`, type: 'POST',
                    data: { document: selectedStatus, application_id: applicationId, _token: csrfToken },
                    success: function(response) {
                        showNotification(response.message || 'Status updated!', 'success');
                        $('.badge.border-primary').text(response.new_status || selectedStatus);
                        $saveButton.html(originalButtonText).prop('disabled', false);
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error saving status.';
                        try {
                            const errors = xhr.responseJSON;
                            if (errors && errors.error) {
                                errorMsg = errors.error;
                            } else if (errors && errors.message) {
                                errorMsg = errors.message;
                            }
                        } catch(e) {}
                        showNotification(errorMsg, 'error');
                        $saveButton.html(originalButtonText).prop('disabled', false);
                    }
                });
            });

            initializeProgressBar();
            const initialStatus = $statusSelect.val();
            const initialIndex = (initialStatus && statusSteps.length > 0) ? statusSteps.findIndex(step => step.value === initialStatus) : -1;
            updateProgressBar(initialIndex);
            $saveButton.prop('disabled', !initialStatus);
        }
    });

    const editModalElement = document.getElementById('editModal');
    let editModal = null;
    if (editModalElement) { editModal = new bootstrap.Modal(editModalElement); }
    const editForm = document.getElementById('editForm');
    const editCommentIdInput = document.getElementById('editCommentId');
    const editcomment_typeSelect = document.getElementById('editcomment_type');
    const editCommentTextInput = document.getElementById('editCommentText');

    function openEditPopup(commentId, comment_type, commentText) {
        if (!editModal || !editForm || !editCommentIdInput || !editcomment_typeSelect || !editCommentTextInput) {
            console.error("Edit modal elements not found.");
            return;
        }
        editCommentIdInput.value = commentId;
        if ([...editcomment_typeSelect.options].map(o => o.value).includes(comment_type)) {
            editcomment_typeSelect.value = comment_type;
        } else {
            editcomment_typeSelect.value = "";
        }
        editCommentTextInput.value = commentText;
        editForm.action = `/comment/update/${commentId}`;
        editModal.show();
    }

    function updateComment() {
        if (!editForm) return;
        const formData = new FormData(editForm);
        const url = editForm.action;
        if (!formData.has('_method')) {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `Status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if(editModal) editModal.hide();
                showNotification(data.message || 'Comment updated!', 'success');
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                showNotification(data.message || 'Error updating', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error: ' + error.message, 'error');
        });
    }

    if (editForm && !editForm.querySelector('input[name="_method"]')) {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        editForm.prepend(methodInput);
    }
</script>
@endsection