@extends('layouts.admin')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        unicode-bidi: normal;
        /* Override 'isolate' */
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        padding: 20px;
    }

    .flex-container {
        display: flex;
        flex-wrap: wrap;
        /* Enable wrapping */
        gap: 1rem;
        /* Add spacing between items */
        justify-content: space-between;
        /* Align items evenly */
    }

    .flex-item {
        flex: 1 1 calc(33.333% - 1rem);
        /* 3 items per row with spacing */
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
    }


    /* Base styles */
    .content-wrapper {
        padding: 20px;
        max-width: 2500px;
        margin: 0 auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
        /* Reduced from 20px */
        font-family: Arial, sans-serif;
    }

    thead th {
        background-color: #f4f4f4;
        color: #333;
        padding: 6px;
        /* Reduced from 12px */
        text-align: left;
        border-bottom: 2px solid #ddd;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    tbody td {
        padding: 4px 6px;
        /* Reduced from 12px */
        text-align: left;
        border-bottom: 1px solid #ddd;
        word-break: break-word;
    }

    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Header Styles with Flexbox */
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
        padding: 0 20px;
    }

    .heading {
        font-size: 40px;
        color: green;
        margin: 0;
        flex: 1;
    }

    .header-right {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    /* Button Styles */
    .btn-download,
    .import-btn,
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        min-width: 120px;
        height: 40px;
        text-decoration: none;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        white-space: nowrap;
    }

    .btn-download {
        background-color: #28a745;
        color: white;
    }

    .btn-download:hover {
        background-color: #218838;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    /* Table Container */
    .table-container {
        width: 100%;
        overflow-x: auto;
        position: relative;
        margin-top: 20px;
        -webkit-overflow-scrolling: touch;
    }

    /* Modal Styles */
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

    .modal-content {
        position: relative;
        background-color: #fff;
        margin: 5% auto;
        padding: 25px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .close {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 24px;
        cursor: pointer;
    }

    /* Success Modal Styles */
    .tick-icon {
        color: #28a745;
        font-size: 48px;
        text-align: center;
        margin-bottom: 15px;
    }

    .success-message {
        text-align: center;
        font-size: 16px;
        color: #333;
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

    .import-btn,
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        min-width: 120px;
        height: 40px;
        text-decoration: none;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        white-space: nowrap;
    }

    .import-btn {
        background-color: #28a745;
        color: white;
    }

    .import-btn:hover {
        background-color: #218838;
    }

    /* Responsive Design */
    @media screen and (max-width: 1200px) {
        .heading {
            font-size: 32px;
        }

        header {
            padding: 0 15px;
        }

        .content-wrapper {
            padding: 15px;
        }
    }

    @media screen and (max-width: 992px) {
        header {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .heading {
            font-size: 28px;
            text-align: center;
        }

        .header-right {
            justify-content: center;
        }

        .btn-download,
        .import-btn,
        .btn {
            min-width: 100px;
            padding: 8px 12px;
        }
    }

    @media screen and (max-width: 768px) {
        .heading {
            font-size: 24px;
        }

        .header-right {
            flex-direction: column;
            width: 100%;
        }

        .btn-download,
        .import-btn,
        .btn {
            width: 100%;
            max-width: none;
        }

        thead th {
            white-space: nowrap;
            padding: 10px 8px;
        }

        tbody td {
            padding: 10px 8px;
        }

        .modal-content {
            padding: 20px;
            margin: 10% auto;
            width: 95%;
        }
    }

    @media screen and (max-width: 480px) {
        .content-wrapper {
            padding: 10px;
        }

        .heading {
            font-size: 20px;
        }

        .btn-download,
        .import-btn,
        .btn {
            font-size: 13px;
            height: 36px;
        }

        thead th,
        tbody td {
            font-size: 12px;
            padding: 8px 6px;
        }
    }

    /* Print styles */
    @media print {

        .header-right,
        .btn-download,
        .import-btn,
        .btn-danger {
            display: none;
        }

        .heading {
            color: #000;
            text-align: center;
        }

        thead th {
            background-color: #f9f9f9 !important;
            color: #000;
        }

        tbody td {
            padding: 8px;
        }
    }
</style>

<div class="content-wrapper">
    <header>
        <h1 class="heading">Lead Form Data</h1>
        <div class="header-right">
            <button class="import-btn" onclick="openModal()">Import CSV File</button>
            <button id="download-excel" class="btn btn-secondary">Download Excel</button>
            <a href="{{ route('backend.leadform.create') }}" class="btn btn-success">Add Lead Form</a>
        </div>
    </header>

    <!-- Import Modal -->
    <div id="importModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 class="modal-title">Import CSV File</h2>
            <form action="{{ route('leadform.import') }}" method="POST" enctype="multipart/form-data" style="display: inline-block;">
                @csrf
                <input type="file" name="file" accept=".xlsx" required>
                <button type="submit" class="btn btn-secondary">Import CSV</button>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="tick-icon">&#10004;</div>
            <p class="success-message">{{ session('success') }}</p>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <table id="data-table">
            <thead>
                <tr>
                     <div class="label" style="background-color: #ff6347;">A</div>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Location</th>
                    <th>Last Qualification</th>
                    <th>Course Name</th>
                    <th>Passed</th>
                    <th>GPA</th>
                    <th>English Test</th>
                    <th>Higher</th>
                    <th>Less</th>
                    <th>Score</th>
                    <th>English Score</th>
                    <th>English Theory</th>
                    <th>Other</th>
                    <th>Document Status</th>
                    <th>Country</th>
                    <th>Location</th>
                    <th>University</th>
                    <th>Course</th>
                    <th>Intake</th>
                    <th>Document Offer</th>
                    <th>Source</th>
                    <th>Other Details</th>
                    <th>User</th>
                    <th>Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leads as $lead)
                <tr>
                    <td>{{ $lead->name }}</td>
                    <td>{{ $lead->email }}</td>
                    <td>{{ $lead->phone }}</td>
                    <td>{{ $lead->locations }}</td>
                    <td>{{ $lead->lastqualification }}</td>
                    <td>{{ $lead->courselevel }}</td>
                    <td>{{ $lead->passed }}</td>
                    <td>{{ $lead->gpa }}</td>
                    <td>{{ $lead->englishTest }}</td>
                    <td>{{ $lead->higher }}</td>
                    <td>{{ $lead->less }}</td>
                    <td>{{ $lead->score }}</td>
                    <td>{{ $lead->englishscore }}</td>
                    <td>{{ $lead->englishtheory }}</td>
                    <td>{{ $lead->otherScore }}</td>
                    <td>{{ $lead->academic }}</td>
                    <td>{{ $lead->country }}</td>
                    <td>{{ $lead->location }}</td>
                    <td>{{ $lead->university }}</td>
                    <td>{{ $lead->course }}</td>
                    <td>{{ $lead->intake }}</td>
                    <td>{{ $lead->offerss }}</td>
                    <td>{{ $lead->source }}</td>
                    <td>{{ $lead->otherDetails }}</td>
                    <td>{{ $lead->sources }}</td>
                    <td class="hide-date">{{ $lead->created_at->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                    <td>{{ $lead->link }}</td>
                    <td>

                        <form action="{{ route('lead.destroy', $lead->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-download btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Excel download function
    document.getElementById('download-excel').addEventListener('click', function() {
        const table = document.getElementById('data-table');
        const wb = XLSX.utils.table_to_book(table, {
            sheet: "Lead Data"
        });
        XLSX.writeFile(wb, 'lead_data.xlsx');
    });

    // Modal functions
    const modal = document.getElementById('importModal');
    const successModal = document.getElementById('successModal');

    function openModal() {
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }

    // Delete confirmation
    function confirmDelete() {
        return confirm('Are you sure you want to delete this record?');
    }

    // Success message handling
    @if(session('success'))
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'swal-custom-popup',
            confirmButton: 'swal-custom-ok-button'
        }
    });
    @endif
</script>
@endsection