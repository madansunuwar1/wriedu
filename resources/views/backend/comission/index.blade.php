@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"></script>

<style>
    .main-container {
        
        background-color: #f8f9fa;
    }

    .icon-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        background: white;
        color: #6c757d;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .icon-btn:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
        color: #495057;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .icon-btn.btn-success:hover {
        background: #198754;
        border-color: #198754;
        color: white;
    }

    .icon-btn.btn-primary:hover {
        background: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .icon-btn.btn-outline-primary:hover {
        background: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .icon-btn.btn-outline-secondary:hover {
        background: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    .icon-btn.btn-outline-danger:hover {
        background: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .tooltip-text {
        position: absolute;
        bottom: -35px;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        z-index: 1000;
    }

    .tooltip-text::before {
        content: '';
        position: absolute;
        top: -4px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-bottom: 4px solid #333;
    }

    .icon-btn:hover .tooltip-text {
        opacity: 1;
        visibility: visible;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(46, 125, 50, 0.05);
    }

    .badge {
        font-weight: 500;
        padding: 4px 8px;
        font-size: 12px;
    }

    .user-info {
        position: relative;
    }

    .action-dots {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 16px;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .action-dots:hover {
        color: #495057;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        width: 90%;
        max-width: 500px;
        border: none;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2e7d32;
        margin-bottom: 20px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.2s;
    }

    .close:hover {
        color: #666;
    }

    .filter-panel-transition {
        transition: all 0.3s ease;
    }

    .swal-custom-popup {
        /* Define your styles or leave empty */
    }

    .swal-custom-ok-button {
        /* Define your styles or leave empty */
    }

    /* Table styles */
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
    }

    thead th {
        background-color: #f4f4f4;
        color: #333;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #ddd;
    }

    tbody tr {
        border-bottom: 1px solid #ddd;
    }

    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tbody td {
        padding: 12px;
        text-align: left;
    }

    /* Button styles */
    .btn {
        display: inline-block;
        padding: 8px 12px;
        margin: 0 4px;
        text-decoration: none;
        font-size: 14px;
        border-radius: 4px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .btn-secondary {
        background-color: #007bff;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    /* Fixed Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal.show {
        display: flex !important;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        width: 90%;
        max-width: 500px;
        animation: modalFadeIn 0.3s ease-out;
        margin: auto;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .close {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 24px;
        font-weight: bold;
        color: #666;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .close:hover {
        color: #333;
    }

    .modal-title {
        margin-top: 0;
        margin-bottom: 20px;
        color: #333;
        font-size: 1.5rem;
    }

    .modal-content form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .modal-content input[type="file"] {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 100%;
    }

    /* SweetAlert Custom Styles */
    .swal-custom-popup {
        width: 420px !important;
        padding: 15px;
        font-size: 14px;
    }

    .swal-custom-ok-button {
        background-color: green !important;
        color: white !important;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 12px;
    }

    .swal-custom-ok-button:hover {
        background-color: darkgreen !important;
    }

    /* Header and General Styles */
    .heading {
        text-align: center;
        font-size: 40px;
        color: green;
    }

    .header-right {
        display: flex;
        justify-content: flex-end;
    }

    .button-container {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .link-btn {
        display: inline-block;
        padding: 8px 12px;
        color: #007bff;
        text-decoration: none;
    }

    .link-btn:hover {
        background-color: #f0f0f0;
        border-color: #0056b3;
        color: #0056b3;
    }

    .filter-panel-transition {
        transition: all 0.3s ease;
    }
</style>

<div class="main-container">
    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-database text-2xl text-[#2e7d32] me-2"></i>
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Commission</div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <div class="position-relative" style="width: 300px;">
                    <input type="text" class="form-control product-search ps-5" id="searchInput" placeholder="Search Finances...">
                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="openModal()" class="icon-btn btn-outline-primary">
                        <i class="ti ti-file-import fs-5"></i>
                        <span class="tooltip-text">Import</span>
                    </button>
                    <button onclick="downloadData()" class="icon-btn btn-outline-primary">
                        <i class="ti ti-file-export fs-5"></i>
                        <span class="tooltip-text">Download Data</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive mb-4 border rounded-1">
            <table class="table text-nowrap mb-0 align-middle" id="dataTable">
                <thead class="text-dark fs-4">
                    <tr>
                        <th>ID</th>
                        <th>University</th>
                        <th>Product</th>
                        <th>Partner</th>
                        <th>Commission</th>
                        <th>Bouns</th>
                        <th>Intensive</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comissions as $comission)
                    <tr>
                        <td>{{ $comission->id }}</td>
                        <td>{{ $comission->university }}</td>
                        <td>{{ $comission->product }}</td>
                        <td>{{ $comission->partner }}</td>

                        <td>
                            @if(is_null($comission->commission_types))
                                N/A
                            @elseif(is_array($comission->commission_types))
                                @foreach($comission->commission_types as $type => $data)
                                    @if(is_array($data) && isset($data['value']))
                                        {{ $type }} ({{ $data['value'] }})@if (!$loop->last), @endif
                                    @else
                                        {{ $type }} ({{ $data }})@if (!$loop->last), @endif
                                    @endif
                                @endforeach
                            @elseif(is_string($comission->commission_types))
                                {{ $comission->commission_types }}
                            @else
                                Invalid Data
                            @endif
                        </td>

                        <td>{{ $comission->bonus_commission == 0 ? 'No' : 'Yes' }}</td>
                        <td>{{ $comission->intensive_commission == 0 ? 'No' : 'Yes' }}</td>

                        <td>
                            <div class="dropdown dropstart">
                                <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical fs-6"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('backend.comission.record', $comission->id) }}">
                                        <i class="fs-4 ti ti-file-text"></i> Views
                                    </a>
                                    <!-- <li>
                                        <a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('backend.comission.edit', $comission->id) }}">
                                            <i class="fs-4 ti ti-pencil"></i> Edit
                                        </a>
                                    </li> -->
                                    <!--
                                    <li>
                                        <form action="{{ route('backend.comission.delete', $comission->id) }}" method="POST"
                                            onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-3">
                                                <i class="fs-4 ti ti-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                    -->
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="importModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <h2 class="modal-title d-flex align-items-center">
            <i class="ti ti-file-import text-primary me-2"></i> Import CSV File
        </h2>
        <!-- Assuming you have a route named 'finance.import' -->
        <form action="" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Select CSV File</label>
                <input type="file" class="form-control" name="file" accept=".xlsx" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal()">
                    <i class="ti ti-x me-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-upload me-1"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('importModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('importModal').style.display = 'none';
    }
</script>
@endsection