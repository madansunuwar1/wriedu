@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')


@section('content')
<style>
        /* Container and base styles */
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* General table styles */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 20px 0;
        }

        table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
            margin: 20px 0;
            font-family: Arial, sans-serif;
        }

        thead th {
            position: sticky;
            top: 0;
            background-color: #f4f4f4;
            color: #333;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            z-index: 10;
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
            min-width: 100px;
        }

        td img {
            max-width: 100px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Button styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            margin: 0 4px;
            text-decoration: none;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 100px;
            white-space: nowrap;
        }

        .btn-secondary {
            background-color: #006400;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #228B22;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* Header styles */
        .heading {
            text-align: center;
            font-size: clamp(24px, 5vw, 40px);
            color: green;
            margin-bottom: 20px;
        }

        .header-right {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
            align-items: center;
            margin: 20px 0;
        }

        .link-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            color: #007bff;
            text-decoration: none;
            min-width: 80px;
        }

        .link-btn:hover {
            background-color: #f0f0f0;
            color: #0056b3;
        }

        .import-btn {
            background-color: #006400;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            min-width: 100px;
        }

        .import-btn:hover {
            background-color: #228B22;
        }

        /* Action buttons in table */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            padding: 20px;
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .modal-title {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .file-input-container {
            margin: 20px 0;
        }

        .file-input-container input[type="file"] {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }

        /* SweetAlert2 Custom Styles */
        .swal2-popup {
            width: min(420px, 95%) !important;
            padding: clamp(1rem, 3vw, 2rem) !important;
            font-size: clamp(12px, 2vw, 14px) !important;
        }

        .swal2-title {
            color: #006400 !important;
            font-size: clamp(18px, 3vw, 24px) !important;
        }

        .swal2-confirm {
            background-color: #006400 !important;
            color: white !important;
            border: none !important;
            padding: 8px 24px !important;
            border-radius: 4px !important;
        }

        .swal2-confirm:hover {
            background-color: #228B22 !important;
        }

        .swal2-icon.swal2-success {
            border-color: #006400 !important;
            color: #006400 !important;
        }

        .swal2-success-ring {
            border-color: #006400 !important;
        }

        .swal2-loader {
            border-color: #006400 transparent #006400 transparent !important;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .header-right {
                justify-content: center;
                margin-top: 15px;
            }

            .btn, .import-btn, .link-btn {
                padding: 8px 12px;
                font-size: 13px;
                min-width: 90px;
            }

            .modal-content {
                width: 95%;
                margin: 10% auto;
                padding: 15px;
            }

            .modal-title {
                font-size: 20px;
            }

            td, th {
                padding: 8px;
                font-size: 13px;
            }
        }

        @media screen and (max-width: 480px) {
            .container {
                padding: 0 10px;
            }

            .heading {
                font-size: 24px;
            }

            .header-right {
                flex-direction: column;
                align-items: stretch;
            }

            .btn, .import-btn, .link-btn {
                width: 100%;
                margin: 5px 0;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons form {
                width: 100%;
            }

            .action-buttons .btn {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>


    <div class="container">
        <header>
            <h1 class="heading">University Data</h1>
            <div class="header-right">
                <button class="import-btn" onclick="openModal()">Import CSV File</button>
                <button id="download-excel" class="btn btn-secondary">Download Excel</button>
                <a href="{{ route('backend.dataentry.create') }}" class="btn btn-success">Add Lead Form</a>
            </div>
        </header>

        <!-- Import Modal -->
        <div id="importModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 class="modal-title">Import Excel File</h2>
                <form id="importForm" action="{{ route('dataentry.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="file-input-container">
                        <input type="file" name="file" accept=".csv,.xlsx,.xls" required>
                        <button type="submit" class="import-btn">Import File</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
            <table id="data-table">
                <thead>
                    <tr>
                        <th>University</th>
                        <th>Location</th>
                        <th>Course</th>
                        <th>Intake</th>
                        <th>Scholarship</th>
                        <th>Tuition</th>
                        <th>UG Ielts</th>
                        <th>UG Pte</th>
                        <th>PG Ielts</th>
                        <th>PG Pte</th>
                        <th>UG Gap</th>
                        <th>PG Gap</th>
                        <th>UG GPA or Percentage</th>
                        <th>PG GPA or Percentage</th>
                        <th>English Test</th>
                        <th>Country</th>
                        <th>Required Documents</th>
                        <th>Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_entries as $data_entrie)
                        <tr>
                            <td>{{ $data_entrie->newUniversity }}</td>
                            <td>{{ $data_entrie->newLocation }}</td>
                            <td>{{ $data_entrie->newCourse }}</td>
                            <td>{{ $data_entrie->newIntake }}</td>
                            <td>{{ $data_entrie->newScholarship }}</td>
                            <td>{{ $data_entrie->newAmount }}</td>
                            <td>{{ $data_entrie->newIelts }}</td>
                            <td>{{ $data_entrie->newpte }}</td>
                            <td>{{ $data_entrie->newPgIelts }}</td>
                            <td>{{ $data_entrie->newPgPte }}</td>
                            <td>{{ $data_entrie->newug }}</td>
                            <td>{{ $data_entrie->newpg }}</td>
                            <td>{{ $data_entrie->newgpaug }}</td>
                            <td>{{ $data_entrie->newgpapg }}</td>
                            <td>{{ $data_entrie->newtest }}</td>
                            <td>{{ $data_entrie->country }}</td>
                            <td>{{ $data_entrie->requireddocuments }}</td>
                            <td>{{ $data_entrie->level }}</td>
                           
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('data_entrie.edit', $data_entrie->id) }}" class="link-btn">Edit</a>
                                    <form action="{{ route('data_entrie.destroy', $data_entrie->id) }}" method="POST" onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // Success message
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK',
            });
        @endif

        // Original Excel download functionality
        document.getElementById('download-excel').addEventListener('click', function () {
            const table = document.getElementById('data-table');
            const workbook = XLSX.utils.table_to_book(table, { sheet: "Data Entry" });
            XLSX.writeFile(workbook, 'data_entry.xlsx');
        });

        // Modal functionality
        function openModal() {
            document.getElementById('importModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('importModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // File import handling
        document.getElementById('importForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const fileInput = this.querySelector('input[type="file"]');
            
            if (!fileInput.files.length) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please select a file to import',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#006400'
                });
                return;
            }

            // Simple extension check
            const fileName = fileInput.files[0].name.toLowerCase();
            const validExtensions = ['.csv', '.xlsx', '.xls'];
            const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));

            if (!hasValidExtension) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please select a valid CSV or Excel file',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#006400'
                });
                return;
            }

            Swal.fire({
                title: 'Importing...',
                text: 'Please wait while we process your file',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("dataentry.import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(json => {
                        throw new Error(json.message || 'HTTP error! status: ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                closeModal();
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message || 'File imported successfully',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#006400',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Import failed');
                }
            })
            .catch(error => {
                closeModal();
                console.error('Import error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to import file. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#006400'
                });
            });
        });

        // Delete confirmation
        function confirmDelete() {
            return Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                return result.isConfirmed;
            });
        }
    </script>
@endsection