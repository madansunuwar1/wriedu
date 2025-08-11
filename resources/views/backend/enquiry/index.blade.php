@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')

@section('content')
    <!-- Include required CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
:root {
    --primary-color: #4f46e5;
    --primary-hover: #4338ca;
    --secondary-color: #64748b;
    --success-color: #22c55e;
    --border-color: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --gradient-green-start: #34d399;
    --gradient-green-end: #059669;
}

/* Base Styles */
body {
    background-color: #f8fafc;
    margin: 0;
    padding: 0;
    height: 100vh;
}

.container {
    height: 100vh;
    max-height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
    box-sizing: border-box;
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-radius: 1rem;
    transform: perspective(1000px) rotateX(0deg);
    transition: transform 0.3s ease;
}

/* Header Styles */
.heading {
    text-align: center;
    font-size: 2.5rem;
    color: #2c3e50;  /* Rich dark blue-gray color */
    margin-bottom: 1.5rem;
    font-family: "Georgia", serif;  /* Classical serif font */
    letter-spacing: 0.5px;
    font-weight: 600;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    padding: 1rem 0;
    border-bottom: 2px solid #e5e7eb;
}

.header-right {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    background: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-top: 1rem;
    border: 1px solid #e5e7eb;
    position: relative;
}

/* Add hover effect for interactive elements */
.header-right:hover {
    box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.1),
                0 3px 6px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

/* Button Styles */
.btn, .import-btn {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--gradient-green-start), var(--gradient-green-end));
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    font-size: 0.875rem;
    white-space: nowrap;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transform: translateZ(20px);
    color: white;
}

.btn:hover, .import-btn:hover {
    transform: translateZ(25px) scale(1.05);
    box-shadow: 0 8px 12px -1px rgba(0, 0, 0, 0.15);
}

/* Table Styles */
.table-responsive {
    flex: 1;
    overflow: auto;
    margin-top: 1rem;
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transform: translateZ(10px);
}

table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    font-size: 0.875rem;
}

thead th {
    position: sticky;
            top: 0;
            background: linear-gradient(180deg, #f8fafc, #f1f5f9);
            color: var(--text-primary);
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
            z-index: 10;
}

tbody tr:hover {
    background-color: #f1f5f9;
}

tbody td {
    padding: 1rem;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    padding: 20px;
}

.modal-content {
    position: relative;
    background-color: white;
    margin: 5% auto;
    padding: 2rem;
    border-radius: 1rem;
    width: 90%;
    max-width: 500px;
    animation: modalSlide 0.3s ease-out;
}

@keyframes modalSlide {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.close {
    position: absolute;
    right: 20px;
    top: 10px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: var(--text-secondary);
    transition: color 0.3s ease;
}

.close:hover {
    color: var(--text-primary);
}

.modal-title {
    margin-bottom: 20px;
    color: var(--text-primary);
    font-size: 1.5rem;
}

/* File Input Styles */
.file-input-container {
    margin: 20px 0;
}

.file-input-container input[type="file"] {
    margin-bottom: 15px;
    padding: 0.75rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 0.75rem;
    width: 100%;
    transition: all 0.3s ease;
    background: white;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Alert Styles */
.alert {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid transparent;
    border-radius: 0.75rem;
    background-color: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.alert-success {
    background-color: #d1fae5;
    color: #065f46;
    border-color: #34d399;
}

/* Loading Spinner */
.spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
    margin-right: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive Styles */
@media screen and (max-width: 768px) {
    .container {
        padding: 1rem;
    }

    .header-right {
        flex-direction: column;
        align-items: stretch;
    }

    .btn, .import-btn {
        width: 100%;
        margin: 5px 0;
        justify-content: center;
    }

    .modal-content {
        width: 95%;
        margin: 10% auto;
        padding: 1.5rem;
    }

    .heading {
        font-size: 1.75rem;
    }

    .table-responsive {
        margin-top: 0.5rem;
    }
    
    tbody td, thead th {
        padding: 0.3rem;
    }
}

/* DataTables Custom Styling */
.dataTables_wrapper .dataTables_filter input {
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    border: 2px solid var(--border-color);
    border-radius: 0.75rem;
    transition: all 0.3s ease;
    background: white;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
}

.dataTables_wrapper .dataTables_length select {
    padding: 0.5rem;
    border: 2px solid var(--border-color);
    border-radius: 0.5rem;
    background-color: white;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    border-radius: 0.5rem;
    border: none;
    background: linear-gradient(135deg, var(--gradient-green-start), var(--gradient-green-end));
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, var(--gradient-green-end), var(--gradient-green-start));
    border: none;
}
</style>
    <div class="container">
        <!-- Header -->
        <header>
            <h1 class="heading">Enquiry Data</h1>
            <div class="header-right">
                <button class="import-btn" onclick="openModal()">Import Excel File</button>
                <button id="download-excel" class="btn btn-secondary">Download Excel</button>
                <a href="{{ route('backend.enquiry.create') }}" class="btn btn-success">Add Enquiry Form</a>
            </div>
        </header>

        <!-- Import Modal -->
        <div id="importModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 class="modal-title">Import Excel File</h2>
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="file-input-container">
                        <input type="file" name="file" accept=".csv,.xlsx,.xls" required>
                        <div class="mt-2 text-sm text-gray-500">
                            Supported formats: CSV, XLSX, XLS (max 10MB)
                        </div>
                        <button type="submit" class="import-btn mt-4">Import File</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Data Table -->
        <div class="table-responsive">
            <table id="enquiry-table" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Email ID</th>
                        <th>Contact Number</th>
                        <th>Guardian Name</th>
                        <th>Guardian Number</th>
                        <th>Country</th>
                        <th>Education</th>
                        <th>Know About Us</th>
                        <th>IELTS</th>
                        <th>TOEFL</th>
                        <th>ELLT</th>
                        <th>PTE</th>
                        <th>SAT</th>
                        <th>GRE</th>
                        <th>GMAT</th>
                        <th>Other Test Score</th>
                        <th>Feedback</th>
                        <th>Counselor</th>
                        <th>Institution 1</th>
                        <th>Grade 1</th>
                        <th>Year 1</th>
                        <th>Institution 2</th>
                        <th>Grade 2</th>
                        <th>Year 2</th>
                        <th>Institution 3</th>
                        <th>Grade 3</th>
                        <th>Year 3</th>
                        <th>Institution 4</th>
                        <th>Grade 4</th>
                        <th>Year 4</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($enquiries as $enquirie)
                        <tr>
                            <td>{{ $enquirie->id }}</td>
                            <td>{{ $enquirie->uname }}</td>
                            <td>{{ $enquirie->email }}</td>
                            <td>{{ $enquirie->contact }}</td>
                            <td>{{ $enquirie->guardians }}</td>
                            <td>{{ $enquirie->contacts }}</td>
                            <td>{{ $enquirie->country }}</td>
                            <td>{{ $enquirie->education }}</td>
                            <td>{{ $enquirie->about }}</td>
                            <td>{{ $enquirie->ielts }}</td>
                            <td>{{ $enquirie->toefl }}</td>
                            <td>{{ $enquirie->ellt }}</td>
                            <td>{{ $enquirie->pte }}</td>
                            <td>{{ $enquirie->sat }}</td>
                            <td>{{ $enquirie->gre }}</td>
                            <td>{{ $enquirie->gmat }}</td>
                            <td>{{ $enquirie->other }}</td>
                            <td>{{ $enquirie->feedback }}</td>
                            <td>{{ $enquirie->counselor }}</td>
                            <td>{{ $enquirie->institution1 }}</td>
                            <td>{{ $enquirie->grade1 }}</td>
                            <td>{{ $enquirie->year1 }}</td>
                            <td>{{ $enquirie->institution2 }}</td>
                            <td>{{ $enquirie->grade2 }}</td>
                            <td>{{ $enquirie->year2 }}</td>
                            <td>{{ $enquirie->institution3 }}</td>
                            <td>{{ $enquirie->grade3 }}</td>
                            <td>{{ $enquirie->year3 }}</td>
                            <td>{{ $enquirie->institution4 }}</td>
                            <td>{{ $enquirie->grade4 }}</td>
                            <td>{{ $enquirie->year4 }}</td>
                            <td class="px-4 py-2">
    <div class="flex items-center space-x-4">
        <a 
            href="{{ route('enquirie.edit', $enquirie->id) }}" 
            class="p-1.5 rounded-lg transition-colors duration-200 hover:bg-gray-100 text-blue-600 hover:text-blue-800"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil">
                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                <path d="m15 5 4 4"/>
            </svg>
        </a>
        <form action="{{ route('enquirie.destroy', $enquirie->id) }}" method="POST" class="inline-block" onsubmit="return confirmDelete()">
            @csrf
            @method('DELETE')
            <button 
                type="submit"
                class="p-1.5 rounded-lg transition-colors duration-200 hover:bg-gray-100 text-red-600 hover:text-red-800"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
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

    <!-- Required Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#enquiry-table').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                pageLength: 25,
                responsive: true
            });
        });

        // Success message handling
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#006400'
            });
        @endif

        // Modal functionality
        function openModal() {
            document.getElementById('importModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('importModal').style.display = 'none';
            document.getElementById('importForm').reset();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal();
            }
        }

        // Import form handling
        document.getElementById('importForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const fileInput = this.querySelector('input[type="file"]');
            
            if (!validateFile(fileInput)) {
                return;
            }

            try {
                await importFile(formData);
            } catch (error) {
                handleImportError(error);
            }
        });

        // File validation
        function validateFile(fileInput) {
            if (!fileInput.files.length) {
                showError('Please select a file to import');
                return false;
            }

            const file = fileInput.files[0];
            if (file.size > 10 * 1024 * 1024) {
                showError('File size exceeds 10MB limit');
                return false;
            }

            const allowedTypes = [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv'
            ];
            if (!allowedTypes.includes(file.type)) {
                showError('Please upload a valid Excel or CSV file');
                return false;
            }

            return true;
        }

        // Import file
        async function importFile(formData) {
            showLoading('Importing...');

            const response = await fetch('{{ route("enquiry.import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                closeModal();
                await showSuccess(result.message);
                if (result.data.inserted > 0 || result.data.updated > 0) {
                    location.reload();
                }
            } else {
                throw new Error(result.message);
            }
        }

        // Show loading state
        function showLoading(message) {
            Swal.fire({
                title: message,
                text: 'Please wait while we process your file',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Show success message
        async function showSuccess(message) {
            return Swal.fire({
                title: 'Success!',
                html: message.replace(/\n/g, '<br>'),
                icon: 'success',
                confirmButtonColor: '#006400'
            });
        }

        // Show error message
        function showError(message) {
            Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonColor: '#006400'
            });
        }

        // Handle import errors
        function handleImportError(error) {
            console.error('Import error:', error);
            showError(error.message || 'Failed to import file. Please try again.');
        }

        // Excel download functionality
        document.getElementById('download-excel').addEventListener('click', function() {
            const table = document.getElementById('enquiry-table');
            const workbook = XLSX.utils.table_to_book(table, { 
                sheet: "Enquiry",
                raw: true,
                dateNF: 'yyyy-mm-dd'
            });
            XLSX.writeFile(workbook, 'enquiry-data.xlsx');
        });

        // Delete confirmation
        async function confirmDelete() {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            });

            return result.isConfirmed;
        }

        // Enhanced keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // File input change handler
        document.querySelector('input[type="file"]').addEventListener('change', function() {
            const submitButton = this.form.querySelector('button[type="submit"]');
            submitButton.disabled = !this.files.length;
        });

        // Initialize tooltips if using Bootstrap
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // Prevent form submission when pressing enter in the DataTable search
        $(document).on('keydown', '.dataTables_filter input', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        // Custom DataTable settings
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                infoEmpty: "No records available",
                infoFiltered: "(filtered from _MAX_ total records)",
                emptyTable: "No data available",
                zeroRecords: "No matching records found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
            }
        });
    </script>
@endsection