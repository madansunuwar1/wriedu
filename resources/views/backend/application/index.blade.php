    @extends('layouts.admin')
    @include('backend.script.session')
    @include('backend.script.alert')
    @section('content')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"></script>
    @if(session('success'))
    <script>
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
    </script>
    @endif
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
            /* Added for Apply button */
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
            /* Added for Clear Dates button */
            background: #dc3545;
            /* Bootstrap danger color */
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

        .status-indicator {
            display: inline-block;
            width: 6px;
            height: 30px;
            border-radius: 3px;
            vertical-align: middle;
        }

        .red {
            background-color: #F56565;
        }

        .green {
            background-color: #2e7d32;
        }

        .blue {
            background-color: #4299E1;
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

        .user-actions {
            right: 10px;
            transform: translateY(-20%);
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 10;
        }

        .user-info:hover .user-actions {
            opacity: 1;
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

        .dropdown-menu {
            z-index: 1010;
            padding: 0px !important;
            border-radius: 50px;
            /* Higher than parent containers */
        }

        .user-info:hover .user-actions {
            opacity: 1;
        }

        .dropdown-menu.custom-dropdown-right {
            right: 100% !important;
            left: auto !important;
            transform: translate(180px, 20px) !important;
        }

        .dropdown-item.text-danger:hover {
            background-color: white !important;
            /* Red hover */
            color: green !important;
            border-radius: 20px;
        }
    </style>

    <div class="main-container">
        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-12 col-xl-12">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-database text-2xl text-[#2e7d32] me-2"></i>
                            <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Application Data</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <div class="position-relative" style="width: 300px;">
                        <input type="text" class="form-control product-search ps-5" id="searchInput"
                            placeholder="Search application data...">
                        <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="toggleFilters" class="icon-btn btn-outline-secondary">
                            <i class="ti ti-filter fs-5"></i>
                            <span class="tooltip-text">Filters</span>
                        </button>
                        <a id="btn-add-finance" href="{{ route('backend.form.create') }}" class="icon-btn btn-success">
                            <i class="ti ti-plus fs-5"></i>
                            <span class="tooltip-text">Add Application Entry</span>
                        </a>
                        <button class="icon-btn btn-outline-primary" onclick="openModal()">
                            <i class="ti ti-file-import fs-5"></i>
                            <span class="tooltip-text">Import</span>
                        </button>
                        <button id="btn-export-data" onclick="downloadData()" class="icon-btn btn-outline-primary">
                            <i class="ti ti-file-export fs-5"></i>
                            <span class="tooltip-text">Export</span>
                        </button>
                    </div>
                </div>

                <div id="filterPanel" class="mt-3 p-3 border rounded bg-light filter-panel-transition" style="display: none;background-color: rgb(248 252 255) !important">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">University</label>
                            <select class="form-select" id="filterUniversity">
                                <option value="">All Universities</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">Course</label>
                            <select class="form-select" id="filterCourse">
                                <option value="">All Courses</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">Intake</label>
                            <select class="form-select" id="filterIntake">
                                <option value="">All Intakes</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" id="filterStatus">
                                <option value="">All Statuses</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">User</label>
                            <select class="form-select" id="filterCreatedBy">
                                <option value="">All Users</option>
                                @if(isset($users))
                                @foreach($users as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-medium">Date Range</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                <input type="text" class="form-control" id="filterDateFrom" placeholder="From date">
                                <span class="input-group-text">to</span>
                                <input type="text" class="form-control" id="filterDateTo" placeholder="To date">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2 d-flex align-items-end gap-2">
                            <button id="applyFilters" class="icon-btn btn-primary">
                                <i class="ti ti-check fs-5"></i>
                                <span class="tooltip-text">Apply</span>
                            </button>
                            <button id="resetFilters" class="icon-btn btn-outline-secondary">
                                <i class="ti ti-rotate-2 fs-5"></i>
                                <span class="tooltip-text">Reset</span>
                            </button>
                            <button id="clearDates" class="icon-btn btn-outline-danger">
                                <i class="ti ti-trash fs-5"></i>
                                <span class="tooltip-text">Clear Dates</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="importModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">×</span>
                    <h2 class="modal-title d-flex align-items-center">
                        <i class="ti ti-file-import text-primary me-2"></i> Import CSV File
                    </h2>
                    <form> <!-- Consider adding action and method if this form submits somewhere -->
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-medium">Select CSV File</label>
                            <input type="file" class="form-control" name="file" accept=".xlsx,.csv" required>
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

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="dataTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15px;"></th> <!-- Status Indicator -->
                                <th>
                                    <div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Student</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i class="ti ti-building me-1"></i><span>University</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i class="ti ti-book me-1"></i><span>Course</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i class="ti ti-calendar-time me-1"></i><span>Intake</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i class="ti ti-status-change me-1"></i><span>Status</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i class="ti ti-users me-1"></i><span>Partner</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i class="ti ti-user-circle me-1"></i><span>User</span></div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center"><i class="ti ti-calendar me-1"></i><span>Date Added</span></div>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="tableBody">
                            @php
                            $normal = $applications->filter(function ($app) { return $app->status !== 'Dropped' && $app->status !== 'Visa Granted'; });
                            $visaGranted = $applications->where('status', 'Visa Granted');
                            $dropped = $applications->where('status', 'Dropped');
                            $normalSorted = $normal->sortByDesc('created_at');
                            $applications = $normalSorted->concat($visaGranted)->concat($dropped);
                            @endphp
                            @foreach ($applications as $application)
                            <tr>
                                <!-- Index 0: Status Indicator -->
                                <td style="padding: 0 5px; vertical-align: middle; text-align: center;">
                                    @if ($application->status == 'Dropped')
                                    <div class="status-indicator red"></div>
                                    @elseif ($application->status == 'Visa Granted')
                                    <div class="status-indicator green"></div>
                                    @endif
                                </td>
                                <!-- Index 1: Student Info -->
                                <td>
                                    <div class="user-info d-flex align-items-center">
                                        <img src="{{ $application->avatar ? asset('storage/avatars/' . $application->avatar) : asset('assets/images/profile/user-1.jpg') }}" alt="avatar" class="rounded-circle" width="35">
                                    
                                        <div class="ms-3 flex-grow-1">
                                            <a href="{{ route('backend.application.record', $application->id) }}" class="text-dark text-decoration-none user-name-link">
                                                <h6 class="user-name mb-0 fw-medium">{{ $application->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $application->email ?? '' }}</small>
                                            </a>
                                        </div>
                                        <div class="user-actions">
                                            <div class="dropdown">
                                                <button class="action-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end custom-dropdown-right">
                                                    <li>
                                                        <a href="javascript:void(0)" class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                            onclick="confirmWithdrawalWithSweetAlert('{{ $application->id }}')">
                                                            <i class="ti ti-trash fs-4"></i> Withdraw
                                                        </a>
                                                        <form id="withdraw-form-{{ $application->id }}"
                                                            action="{{ route('backend.application.withdraw', ['id' => $application->id]) }}"
                                                            method="POST" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Hidden TD elements start here -->
                                <td class="d-none">{{ $application->email ?? 'N/A' }}</td> <!-- Index 2 -->
                                <td class="d-none">{{ $application->phone ?? 'N/A' }}</td> <!-- Index 3 -->
                                <td class="d-none">{{ $application->location ?? 'N/A' }}</td> <!-- Index 4 -->
                                <td class="d-none">{{ $application->lastqualification ?? 'N/A' }}</td> <!-- Index 5 -->
                                <td class="d-none">{{ $application->passed ?? 'N/A' }}</td> <!-- Index 6 -->
                                <td class="d-none">{{ $application->country ?? 'N/A' }}</td> <!-- Index 7 -->
                                <td class="d-none">{{ $application->gpa ?? 'N/A' }}</td> <!-- Index 8 -->
                                <td class="d-none">{{ $application->english ?? 'N/A' }}</td> <!-- Index 9 -->
                                <td class="d-none">{{ $application->englishTest ?? 'N/A' }}</td> <!-- Index 10 -->
                                <td class="d-none">{{ $application->higher ?? 'N/A' }}</td> <!-- Index 11 -->
                                <td class="d-none">{{ $application->less ?? 'N/A' }}</td> <!-- Index 12 -->
                                <td class="d-none">{{ $application->score ?? 'N/A' }}</td> <!-- Index 13 -->
                                <td class="d-none">{{ $application->englishscore ?? 'N/A' }}</td> <!-- Index 14 -->
                                <td class="d-none">{{ $application->englishtheory ?? 'N/A' }}</td> <!-- Index 15 -->
                                <td class="d-none document-status-cell">{{ $application->document ?? 'N/A' }}</td> <!-- Index 16 -->
                                <td class="d-none">{{ $application->country_application ?? 'N/A' }}</td> <!-- Index 17 -->
                                <!-- Visible TD elements resume -->
                                <td class="text-nowrap">{{ $application->university ?? 'N/A' }}</td> <!-- Index 18 -->
                            <td class="text-nowrap">{{ $application->course ?? 'N/A' }}</td> <!-- Index 19 -->
                                <td class="text-nowrap">{{ $application->intake ?? 'N/A' }}</td> <!-- Index 20 -->
                                <td> <!-- Index 21 -->
                                    @php
                                    $badgeClass = 'bg-primary-subtle text-primary';
                                    if ($application->status == 'Visa Granted') { $badgeClass = 'bg-success-subtle text-success'; }
                                    elseif ($application->status == 'Dropped') { $badgeClass = 'bg-danger-subtle text-danger'; }
                                    @endphp
                                    <span class="badge {{ $badgeClass }} d-inline-flex align-items-center gap-1">
                                        {{ $application->status ?? 'N/A' }}
                                    </span>
                                </td>
                                <!-- More hidden TD elements -->
                                <td class="d-none">{{ $application->additionalinfo ?? 'N/A' }}</td> <!-- Index 22 -->
                                <td class="d-none">{{ $application->source ?? 'N/A' }}</td> <!-- Index 23 -->
                                <td class="d-none">{{ $application->otherDetails ?? 'N/A' }}</td> <!-- Index 24 -->
                                <!-- Visible TD elements resume -->
                                <td>{{ $application->partnerDetails ?? 'N/A' }}</td> <!-- Index 25 -->
                                <td> <!-- Index 26 -->
                                    <div class="text-nowrap"><i class="ti ti-user-circle me-1"></i> {{ $application->createdBy->name ?? 'N/A' }}</div>
                                </td>
                                <!-- More hidden TD elements -->
                                <td class="d-none">{{ $application->searchField ?? 'N/A' }}</td> <!-- Index 27 -->
                                <td class="d-none">{{ $application->customSearchField ?? 'N/A' }}</td> <!-- Index 28 -->
                                <td class="d-none">{{ $application->courseSearchField ?? 'N/A' }}</td> <!-- Index 29 -->
                                <!-- Last visible TD -->
                            <td class="text-nowrap">
    <i class="ti ti-clock me-1"></i> 
    {{ $application->created_at ? $application->created_at->format('Y-m-d (h:i A)') : 'N/A' }}
    </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted" id="paginationInfo">
                            Showing <span id="startItem">1</span> to <span id="endItem">10</span> of <span id="totalItems">{{ $applications->count() }}</span> entries
                        </div>
                        <div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0" id="paginationControls">
                                    <li class="page-item disabled" id="prevPage"><a class="page-link" href="#" aria-label="Previous"><i class="ti ti-chevron-left"></i></a></li>
                                    <li class="page-item" id="nextPage"><a class="page-link" href="#" aria-label="Next"><i class="ti ti-chevron-right"></i></a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function confirmWithdrawalWithSweetAlert(applicationId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, withdraw it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'swal-custom-popup',
                confirmButton: 'swal-custom-ok-button'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if confirmed
                document.getElementById('withdraw-form-' + applicationId).submit();
            }
        });
    }

        var modal = document.getElementById("importModal");
        // Initial styling for the modal if not already styled by CSS
        if (modal && !modal.style.display) {
            modal.style.cssText = `display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);`;
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) modalContent.style.cssText = `background-color: #fff; margin: 10% auto; padding: 25px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); width: 90%; max-width: 500px; border: none;`;
            const closeButton = modal.querySelector('.close');
            if (closeButton) closeButton.style.cssText = `color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; transition: color 0.2s;`;
            const modalTitle = modal.querySelector('.modal-title');
            if (modalTitle) modalTitle.style.marginBottom = '20px';
        }

        function openModal() {
            if (modal) modal.style.display = "block";
        }

        function closeModal() {
            if (modal) modal.style.display = "none";
        }

        window.addEventListener('click', function(event) {
            if (event.target == modal) closeModal();
        });

        function downloadData() {
            let rowsToExport;
            if (window.advancedTableInstance && window.advancedTableInstance.filteredRows) {
                rowsToExport = window.advancedTableInstance.filteredRows;
            } else {
                const tableElement = document.getElementById('dataTable');
                rowsToExport = Array.from(tableElement.querySelectorAll('tbody tr'));
            }

            const headerRow = document.getElementById('dataTable').querySelector('thead tr');
            const thElements = Array.from(headerRow.querySelectorAll('th'));
            const headers = thElements.map(th => {
                const spanInTh = th.querySelector('span');
                return `"${(spanInTh ? spanInTh.textContent.trim() : th.textContent.trim()).replace(/"/g, '""')}"`;
            });

            let csvContent = headers.join(',') + '\n';

            const columnIndices = window.advancedTableInstance ? window.advancedTableInstance.columnIndices : null;

            // This map helps get data from the correct TD based on the header text.
            // It relies on columnIndices being correctly mapped in the AdvancedTable class.
            const exportColumnMap = {};
            if (columnIndices) {
                exportColumnMap[""] = columnIndices.statusIndicator; // For the first (visual status) column
                exportColumnMap["Student"] = columnIndices.name; // 'name' index points to the student info TD
                exportColumnMap["University"] = columnIndices.university;
                exportColumnMap["Course"] = columnIndices.course;
                exportColumnMap["Intake"] = columnIndices.intake;
                exportColumnMap["Status"] = columnIndices.status;
                exportColumnMap["Partner"] = columnIndices.partner;
                exportColumnMap["User"] = columnIndices.createdBy;
                exportColumnMap["Date Added"] = columnIndices.dateAdded;
            }

            rowsToExport.forEach(row => {
                const allTdsInRow = Array.from(row.querySelectorAll('td'));
                const rowData = [];

                thElements.forEach(th => {
                    const headerText = (th.querySelector('span')?.textContent.trim() || th.textContent.trim());
                    let value = 'N/A';

                    const tdIndex = exportColumnMap[headerText];

                    if (tdIndex !== undefined && allTdsInRow[tdIndex]) {
                        const col = allTdsInRow[tdIndex];
                        if (headerText === 'Student') {
                            value = col.querySelector('.user-name h6')?.textContent.trim() || 'N/A';
                            // Optionally, append email:
                            // const emailInStudentCol = col.querySelector('.user-name small.text-muted')?.textContent.trim();
                            // if (emailInStudentCol) value += ` (${emailInStudentCol})`;
                        } else if (headerText === 'Status') {
                            const badge = col.querySelector('span.badge');
                            value = badge ? badge.textContent.trim() : (col.textContent.trim() || 'N/A');
                        } else if (headerText === "" && tdIndex === columnIndices.statusIndicator) { // Status indicator
                            value = ""; // Export as empty for the visual indicator column
                        } else {
                            value = col.textContent.trim() || 'N/A';
                        }
                    }
                    rowData.push(`"${String(value).replace(/"/g, '""')}"`);
                });
                csvContent += rowData.join(',') + '\n';
            });

            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'applications_data.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url); // Clean up the object URL
        }

        class AdvancedTable {
            constructor(tableId, options) {
                this.table = document.getElementById(tableId);
                this.tbody = this.table.querySelector('tbody');
                this.originalRows = Array.from(this.tbody.querySelectorAll('tr'));
                this.itemsPerPage = options.itemsPerPage || 9;
                this.filterOptions = options.filterOptions || {};
                this.currentPage = options.initialPage || 1;
                this.filteredRows = [...this.originalRows];
                this.totalPages = 0;
                this.columnIndices = this.getColumnIndices();
                this.originalOptions = {
                    universities: this.getUniqueValues(this.columnIndices.university),
                    courses: this.getUniqueValues(this.columnIndices.course),
                    intakes: this.getUniqueValues(this.columnIndices.intake),
                    statuses: this.getUniqueValues(this.columnIndices.status),
                };
                this.initPagination();
                this.initDependentFilters();
            }

            getColumnIndices() {
                const indices = {};
                // CRITICAL: This map defines the 0-based index of each <td> cell in a <tr>
                // It MUST match the actual HTML structure of your <tbody> <tr> elements.
                const tdIndexMap = {
                    statusIndicator: 0,
                    studentInfo: 1, // Contains avatar, name (h6.user-name), email (small.text-muted)
                    hiddenEmail: 2,
                    hiddenPhone: 3,
                    hiddenLocation: 4,
                    hiddenLastQualification: 5,
                    hiddenPassed: 6,
                    hiddenCountry: 7,
                    hiddenGpa: 8,
                    hiddenEnglish: 9,
                    hiddenEnglishTest: 10,
                    hiddenHigher: 11,
                    hiddenLess: 12,
                    hiddenScore: 13,
                    hiddenEnglishScore: 14,
                    hiddenEnglishTheory: 15,
                    hiddenDocumentStatus: 16,
                    hiddenCountryApplication: 17,
                    university: 18,
                    course: 19,
                    intake: 20,
                    status: 21,
                    hiddenAdditionalInfo: 22,
                    hiddenSource: 23,
                    hiddenOtherDetails: 24,
                    partner: 25,
                    user: 26, // Created By
                    hiddenSearchField: 27,
                    hiddenCustomSearchField: 28,
                    hiddenCourseSearchField: 29,
                    dateAdded: 30
                };

                indices.statusIndicator = tdIndexMap.statusIndicator;
                indices.name = tdIndexMap.studentInfo; // For searching name/email
                            indices.email = tdIndexMap.hiddenEmail; // For searching specifically in the hidden email column.
                indices.phone = tdIndexMap.hiddenPhone; // For searching specifically in the hidden phone column.

                indices.university = tdIndexMap.university;
                indices.course = tdIndexMap.course;
                indices.intake = tdIndexMap.intake;
                indices.status = tdIndexMap.status;
                indices.partner = tdIndexMap.partner;
                indices.createdBy = tdIndexMap.user;
                indices.dateAdded = tdIndexMap.dateAdded;

                return indices;
            }


            getUniqueValues(columnIndex) {
                if (columnIndex === undefined) return [];
                const values = new Set();
                this.originalRows.forEach(row => {
                    const cols = row.querySelectorAll('td');
                    if (cols.length > columnIndex && cols[columnIndex]) {
                        let value = cols[columnIndex].textContent.trim();
                        if (columnIndex === this.columnIndices.status && cols[columnIndex].querySelector('span.badge')) {
                            value = cols[columnIndex].querySelector('span.badge').textContent.trim();
                        }
                        if (value && value !== 'N/A') values.add(value);
                    }
                });
                return Array.from(values).filter(Boolean).sort();
            }

            initPagination() {
                this.paginationControls = document.getElementById('paginationControls');
                this.prevPage = document.getElementById('prevPage');
                this.nextPage = document.getElementById('nextPage');
                this.paginationInfo = document.getElementById('paginationInfo');
                this.paginationControls.addEventListener('click', (e) => {
                    e.preventDefault();
                    const target = e.target.closest('.page-link');
                    if (!target) return;
                    const pageItem = target.closest('.page-item');
                    if (pageItem.classList.contains('disabled') || pageItem.classList.contains('active') || pageItem.classList.contains('ellipsis')) return;
                    if (pageItem.id === 'prevPage') {
                        if (this.currentPage > 1) this.currentPage--;
                    } else if (pageItem.id === 'nextPage') {
                        if (this.currentPage < this.totalPages) this.currentPage++;
                    } else {
                        const pageNumber = parseInt(target.textContent, 10);
                        if (!isNaN(pageNumber)) this.currentPage = pageNumber;
                    }
                    this.updatePagination();
                });
            }

            initDependentFilters() {
                this.populateFilter('filterUniversity', this.originalOptions.universities);
                this.populateFilter('filterCourse', this.originalOptions.courses);
                this.populateFilter('filterIntake', this.originalOptions.intakes);
                this.populateFilter('filterStatus', this.originalOptions.statuses);
                document.getElementById('filterUniversity').addEventListener('change', (e) => {
                    const val = e.target.value;
                    this.setFilter('university', val);
                    this.updateCourseFilter(val);
                    this.updateIntakeFilter(val, document.getElementById('filterCourse').value);
                    this.filterAndPaginate();
                });
                document.getElementById('filterCourse').addEventListener('change', (e) => {
                    const val = e.target.value;
                    this.setFilter('course', val);
                    this.updateIntakeFilter(document.getElementById('filterUniversity').value, val);
                    this.filterAndPaginate();
                });
                document.getElementById('filterIntake').addEventListener('change', (e) => {
                    this.setFilter('intake', e.target.value);
                    this.filterAndPaginate();
                });
                document.getElementById('filterStatus').addEventListener('change', (e) => {
                    this.setFilter('status', e.target.value);
                    this.filterAndPaginate();
                });
                document.getElementById('filterCreatedBy').addEventListener('change', (e) => {
                    this.setFilter('createdBy', e.target.value);
                    this.filterAndPaginate();
                });
                document.getElementById('filterDateFrom').addEventListener('change', (e) => {
                    this.setFilter('dateFrom', e.target.value);
                    this.validateDateRange();
                    this.filterAndPaginate();
                });
                document.getElementById('filterDateTo').addEventListener('change', (e) => {
                    this.setFilter('dateTo', e.target.value);
                    this.validateDateRange();
                    this.filterAndPaginate();
                });
            }

            validateDateRange() {
                const from = document.getElementById('filterDateFrom').value;
                const to = document.getElementById('filterDateTo').value;
                if (from && to) {
                    const fromDate = new Date(from);
                    const toDate = new Date(to);
                    if (isNaN(fromDate.getTime()) || isNaN(toDate.getTime()) || fromDate > toDate) {
                        Swal.fire({
                            title: 'Invalid Date Range',
                            text: 'The "From" date must be before or the same as the "To" date.',
                            icon: 'warning',
                            customClass: {
                                popup: 'swal-custom-popup',
                                confirmButton: 'swal-custom-ok-button'
                            }
                        });
                        return false;
                    }
                }
                return true;
            }

            populateFilter(filterId, options) {
                const select = document.getElementById(filterId);
                const currentVal = select.value;
                const firstOptionLabel = select.options[0].textContent;
                select.innerHTML = `<option value="">${firstOptionLabel}</option>`;
                options.forEach(option => {
                    if (option) select.innerHTML += `<option value="${option}">${option}</option>`;
                });
                if (select.querySelector(`option[value="${currentVal}"]`)) select.value = currentVal;
            }

            updateCourseFilter(selectedUniversity) {
                const courseSelect = document.getElementById('filterCourse');
                const currentCourse = courseSelect.value;
                const firstOptionLabel = courseSelect.options[0].textContent;
                courseSelect.innerHTML = `<option value="">${firstOptionLabel}</option>`;
                let coursesToShow = [];
                if (selectedUniversity) {
                    const filteredCourses = new Set();
                    this.originalRows.forEach(row => {
                        const cols = row.querySelectorAll('td');
                        const rowUniversity = (this.columnIndices.university !== undefined && cols[this.columnIndices.university]) ? cols[this.columnIndices.university].textContent.trim() : '';
                        const rowCourse = (this.columnIndices.course !== undefined && cols[this.columnIndices.course]) ? cols[this.columnIndices.course].textContent.trim() : '';
                        if (rowUniversity === selectedUniversity && rowCourse && rowCourse !== 'N/A') filteredCourses.add(rowCourse);
                    });
                    coursesToShow = Array.from(filteredCourses).sort();
                } else {
                    coursesToShow = [...this.originalOptions.courses];
                }
                coursesToShow.forEach(course => courseSelect.innerHTML += `<option value="${course}">${course}</option>`);
                if (courseSelect.querySelector(`option[value="${currentCourse}"]`)) courseSelect.value = currentCourse;
                else courseSelect.value = "";
            }

            updateIntakeFilter(selectedUniversity, selectedCourse) {
                const intakeSelect = document.getElementById('filterIntake');
                const currentIntake = intakeSelect.value;
                const firstOptionLabel = intakeSelect.options[0].textContent;
                intakeSelect.innerHTML = `<option value="">${firstOptionLabel}</option>`;
                let intakesToShow = [];
                if (selectedUniversity || selectedCourse) {
                    const filteredIntakes = new Set();
                    this.originalRows.forEach(row => {
                        const cols = row.querySelectorAll('td');
                        const rowUniversity = (this.columnIndices.university !== undefined && cols[this.columnIndices.university]) ? cols[this.columnIndices.university].textContent.trim() : '';
                        const rowCourse = (this.columnIndices.course !== undefined && cols[this.columnIndices.course]) ? cols[this.columnIndices.course].textContent.trim() : '';
                        const rowIntake = (this.columnIndices.intake !== undefined && cols[this.columnIndices.intake]) ? cols[this.columnIndices.intake].textContent.trim() : '';
                        let universityMatch = !selectedUniversity || rowUniversity === selectedUniversity;
                        let courseMatch = !selectedCourse || rowCourse === selectedCourse;
                        if (universityMatch && courseMatch && rowIntake && rowIntake !== 'N/A') filteredIntakes.add(rowIntake);
                    });
                    intakesToShow = Array.from(filteredIntakes).sort();
                } else {
                    intakesToShow = [...this.originalOptions.intakes];
                }
                intakesToShow.forEach(intake => intakeSelect.innerHTML += `<option value="${intake}">${intake}</option>`);
                if (intakeSelect.querySelector(`option[value="${currentIntake}"]`)) intakeSelect.value = currentIntake;
                else intakeSelect.value = "";
            }

            setFilter(key, value) {
                if (!this.filterOptions) this.filterOptions = {};
                this.filterOptions[key] = value;
                if (key !== 'dateFrom' && key !== 'dateTo') this.currentPage = 1;
            }

            resetFilters() {
                this.filterOptions = {
                    name: '',
                    university: '',
                    course: '',
                    intake: '',
                    status: '',
                    createdBy: '',
                    dateFrom: '',
                    dateTo: ''
                };
                this.currentPage = 1;
            }

            _checkRowAgainstFilters(row) {
                const cols = row.querySelectorAll('td');
                let show = true;

                // Name, Email (in student column), Hidden Email, Hidden Phone Search
                if (show && this.filterOptions.name) {
                    const searchTerm = this.filterOptions.name.toLowerCase();
                    let rowMatchesSearch = false;

                    if (this.columnIndices.name !== undefined && cols[this.columnIndices.name]) {
                        const studentCell = cols[this.columnIndices.name];
                        const nameElement = studentCell.querySelector('h6.user-name');
                        if (nameElement && nameElement.textContent.trim().toLowerCase().includes(searchTerm)) {
                            rowMatchesSearch = true;
                        }
                        if (!rowMatchesSearch) {
                            const emailElement = studentCell.querySelector('small.text-muted');
                            if (emailElement && emailElement.textContent.trim().toLowerCase().includes(searchTerm)) {
                                rowMatchesSearch = true;
                            }
                        }
                    }
                    if (!rowMatchesSearch && this.columnIndices.email !== undefined && cols[this.columnIndices.email] && cols[this.columnIndices.email].textContent.trim().toLowerCase().includes(searchTerm)) {
                        rowMatchesSearch = true;
                    }
                    if (!rowMatchesSearch && this.columnIndices.phone !== undefined && cols[this.columnIndices.phone] && cols[this.columnIndices.phone].textContent.trim().toLowerCase().includes(searchTerm)) {
                        rowMatchesSearch = true;
                    }
                    if (!rowMatchesSearch) show = false;
                }

                if (show && this.filterOptions.university && this.columnIndices.university !== undefined && cols[this.columnIndices.university] && (cols[this.columnIndices.university].textContent.trim() || 'N/A') !== this.filterOptions.university) show = false;
                if (show && this.filterOptions.course && this.columnIndices.course !== undefined && cols[this.columnIndices.course] && (cols[this.columnIndices.course].textContent.trim() || 'N/A') !== this.filterOptions.course) show = false;
                if (show && this.filterOptions.intake && this.columnIndices.intake !== undefined && cols[this.columnIndices.intake] && (cols[this.columnIndices.intake].textContent.trim() || 'N/A') !== this.filterOptions.intake) show = false;
                if (show && this.filterOptions.status && this.columnIndices.status !== undefined && cols[this.columnIndices.status]) {
                    const statusElement = cols[this.columnIndices.status].querySelector('span.badge');
                    const cellValue = statusElement ? statusElement.textContent.trim() : (cols[this.columnIndices.status].textContent.trim() || 'N/A');
                    if (cellValue !== this.filterOptions.status) show = false;
                }
                if (show && this.filterOptions.createdBy && this.columnIndices.createdBy !== undefined && cols[this.columnIndices.createdBy]) {
                    const cellCreatedByText = cols[this.columnIndices.createdBy].textContent.trim() || 'N/A';
                    if (!cellCreatedByText.toLowerCase().includes(this.filterOptions.createdBy.toLowerCase())) show = false;
                }
                if (show && (this.filterOptions.dateFrom || this.filterOptions.dateTo) && this.columnIndices.dateAdded !== undefined && cols[this.columnIndices.dateAdded]) {
                    const dateCellFullText = cols[this.columnIndices.dateAdded].textContent.trim();
                    const dateMatch = dateCellFullText.match(/\d{4}-\d{2}-\d{2}/);
                    const dateAddedString = dateMatch ? dateMatch[0] : null;
                    if (!dateAddedString || dateAddedString === 'N/A') {
                        if (this.filterOptions.dateFrom || this.filterOptions.dateTo) show = false;
                    } else {
                        try {
                            const rowDate = new Date(dateAddedString);
                            const filterDateFrom = this.filterOptions.dateFrom ? new Date(this.filterOptions.dateFrom + "T00:00:00") : null;
                            const filterDateTo = this.filterOptions.dateTo ? new Date(this.filterOptions.dateTo + "T23:59:59") : null;
                            if (isNaN(rowDate.getTime())) {
                                if (this.filterOptions.dateFrom || this.filterOptions.dateTo) show = false;
                            } else {
                                let passesFrom = true,
                                    passesTo = true;
                                if (filterDateFrom && !isNaN(filterDateFrom.getTime()) && rowDate < filterDateFrom) passesFrom = false;
                                if (filterDateTo && !isNaN(filterDateTo.getTime()) && rowDate > filterDateTo) passesTo = false;
                                if (!passesFrom || !passesTo) show = false;
                            }
                        } catch (e) {
                            console.error("Error parsing date:", dateAddedString, e);
                            if (this.filterOptions.dateFrom || this.filterOptions.dateTo) show = false;
                        }
                    }
                }
                return show;
            }

            filterAndPaginate() {
                this.filteredRows = this.originalRows.filter(row => this._checkRowAgainstFilters(row));
                this.totalPages = Math.ceil(this.filteredRows.length / this.itemsPerPage);
                if (this.currentPage > this.totalPages && this.totalPages > 0) this.currentPage = this.totalPages;
                else if (this.totalPages === 0) this.currentPage = 1;
                else if (this.currentPage < 1) this.currentPage = 1;
                this.updatePagination();
            }

            updatePagination() {
                this.clearPaginationLinks();
                const startIndex = (this.currentPage - 1) * this.itemsPerPage;
                const endIndex = Math.min(startIndex + this.itemsPerPage, this.filteredRows.length);
                this.originalRows.forEach(row => row.style.display = 'none');
                for (let i = startIndex; i < endIndex; i++) {
                    if (this.filteredRows[i]) this.filteredRows[i].style.display = '';
                }
                document.getElementById('startItem').textContent = this.filteredRows.length === 0 ? '0' : startIndex + 1;
                document.getElementById('endItem').textContent = endIndex;
                document.getElementById('totalItems').textContent = this.filteredRows.length;
                this.prevPage.classList.toggle('disabled', this.currentPage <= 1);
                this.nextPage.classList.toggle('disabled', this.currentPage >= this.totalPages || this.totalPages === 0);
                this.generatePaginationLinks();
                saveState(this);
            }

            clearPaginationLinks() {
                const pageItems = this.paginationControls.querySelectorAll('.page-item:not(#prevPage):not(#nextPage)');
                pageItems.forEach(item => item.remove());
            }

            generatePaginationLinks() {
                const paginationControls = this.paginationControls;
                const nextPageElement = this.nextPage;
                if (this.totalPages <= 1) return;
                const maxVisiblePages = 7;
                let startPage = 1,
                    endPage = this.totalPages;
                if (this.totalPages > maxVisiblePages) {
                    const pagesBefore = Math.floor((maxVisiblePages - 3) / 2);
                    const pagesAfter = maxVisiblePages - 3 - pagesBefore;
                    startPage = this.currentPage - pagesBefore;
                    endPage = this.currentPage + pagesAfter;
                    if (startPage < 1) {
                        endPage += (1 - startPage);
                        startPage = 1;
                    }
                    if (endPage > this.totalPages) {
                        startPage -= (endPage - this.totalPages);
                        endPage = this.totalPages;
                        startPage = Math.max(1, startPage);
                    }
                }
                const createPageItem = (pageNumber, isActive = false, isEllipsis = false) => {
                    const pageItem = document.createElement('li');
                    pageItem.className = `page-item ${isActive ? 'active' : ''} ${isEllipsis ? 'disabled ellipsis' : ''}`;
                    const pageLink = document.createElement('a');
                    pageLink.className = 'page-link';
                    pageLink.href = '#';
                    pageLink.textContent = isEllipsis ? '...' : pageNumber;
                    pageItem.appendChild(pageLink);
                    return pageItem;
                };
                if (startPage > 1) {
                    paginationControls.insertBefore(createPageItem(1), nextPageElement);
                    if (startPage > 2) paginationControls.insertBefore(createPageItem(0, false, true), nextPageElement);
                }
                for (let i = startPage; i <= endPage; i++) paginationControls.insertBefore(createPageItem(i, i === this.currentPage), nextPageElement);
                if (endPage < this.totalPages) {
                    if (endPage < this.totalPages - 1) paginationControls.insertBefore(createPageItem(0, false, true), nextPageElement);
                    paginationControls.insertBefore(createPageItem(this.totalPages), nextPageElement);
                }
            }
        }

        const stateKey = 'financeTableState';

        function saveState(tableInstance) {
            const state = {
                currentPage: tableInstance.currentPage,
                filterOptions: tableInstance.filterOptions,
                searchInput: document.getElementById('searchInput').value,
                filterPanelOpen: document.getElementById('filterPanel').style.display !== 'none',
                filterUniversity: document.getElementById('filterUniversity').value,
                filterCourse: document.getElementById('filterCourse').value,
                filterIntake: document.getElementById('filterIntake').value,
                filterStatus: document.getElementById('filterStatus').value,
                filterCreatedBy: document.getElementById('filterCreatedBy').value,
                filterDateFrom: document.getElementById('filterDateFrom').value,
                filterDateTo: document.getElementById('filterDateTo').value,
            };
            sessionStorage.setItem(stateKey, JSON.stringify(state));
        }

        function loadState() {
            const savedState = sessionStorage.getItem(stateKey);
            if (savedState) {
                try {
                    const state = JSON.parse(savedState);
                    document.getElementById('searchInput').value = state.searchInput || '';
                    document.getElementById('filterUniversity').value = state.filterUniversity || '';
                    // Course & Intake set after table init based on loaded University
                    document.getElementById('filterStatus').value = state.filterStatus || '';
                    document.getElementById('filterCreatedBy').value = state.filterCreatedBy || '';
                    document.getElementById('filterDateFrom').value = state.filterDateFrom || '';
                    document.getElementById('filterDateTo').value = state.filterDateTo || '';
                    document.getElementById('filterPanel').style.display = state.filterPanelOpen ? 'block' : 'none';
                    return state;
                } catch (e) {
                    console.error("Failed to load state:", e);
                    sessionStorage.removeItem(stateKey);
                    return null;
                }
            }
            return null;
        }

        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#filterDateFrom", {
                dateFormat: "Y-m-d",
                allowInput: true
            });
            flatpickr("#filterDateTo", {
                dateFormat: "Y-m-d",
                allowInput: true
            });

            const loadedState = loadState();
            const initialOptions = {
                itemsPerPage: 9,
                filterOptions: loadedState ? loadedState.filterOptions : {
                    name: '',
                    university: '',
                    course: '',
                    intake: '',
                    status: '',
                    createdBy: '',
                    dateFrom: '',
                    dateTo: ''
                },
                initialPage: loadedState ? loadedState.currentPage : 1
            };

            const table = new AdvancedTable('dataTable', initialOptions);
            window.advancedTableInstance = table;

            if (loadedState) {
                table.updateCourseFilter(document.getElementById('filterUniversity').value);
                if (document.getElementById('filterCourse').querySelector(`option[value="${loadedState.filterCourse}"]`)) {
                    document.getElementById('filterCourse').value = loadedState.filterCourse;
                } else {
                    document.getElementById('filterCourse').value = '';
                }

                table.updateIntakeFilter(document.getElementById('filterUniversity').value, document.getElementById('filterCourse').value);
                if (document.getElementById('filterIntake').querySelector(`option[value="${loadedState.filterIntake}"]`)) {
                    document.getElementById('filterIntake').value = loadedState.filterIntake;
                } else {
                    document.getElementById('filterIntake').value = '';
                }
            }

            table.filterAndPaginate();

            document.getElementById('toggleFilters')?.addEventListener('click', function() {
                const panel = document.getElementById('filterPanel');
                if (panel) {
                    panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'block' : 'none';
                    if (window.advancedTableInstance) saveState(window.advancedTableInstance);
                }
            });

            document.getElementById('searchInput').addEventListener('input', function(e) {
                table.setFilter('name', e.target.value);
                table.filterAndPaginate();
            });
            document.getElementById('applyFilters').addEventListener('click', function() {
                if (table.validateDateRange()) table.filterAndPaginate();
            });
            document.getElementById('resetFilters').addEventListener('click', function() {
                document.getElementById('filterUniversity').value = '';
                document.getElementById('filterCourse').value = '';
                document.getElementById('filterIntake').value = '';
                document.getElementById('filterStatus').value = '';
                document.getElementById('filterCreatedBy').value = '';
                document.getElementById('filterDateFrom').value = '';
                document.getElementById('filterDateTo').value = '';
                document.getElementById('searchInput').value = '';
                document.getElementById('filterPanel').style.display = 'none';
                sessionStorage.removeItem(stateKey);
                table.resetFilters();
                table.updateCourseFilter('');
                table.updateIntakeFilter('', '');
                table.filterAndPaginate();
            });
            document.getElementById('clearDates').addEventListener('click', function() {
                document.getElementById('filterDateFrom').value = '';
                document.getElementById('filterDateTo').value = '';
                table.setFilter('dateFrom', '');
                table.setFilter('dateTo', '');
                table.filterAndPaginate();
            });
        });
    </script>

    @endsection