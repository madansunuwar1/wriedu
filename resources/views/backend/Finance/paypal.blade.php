@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <div class="main-container">
        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-12 col-xl-12">
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Payable</div>
                    </div>
                    <div class="col-md-6 col-xl-8">
                        <div class="position-relative">
                            <input type="text" class="form-control product-search ps-5" id="searchInput"
                                   onkeyup="filterTable()" placeholder="Search Finances...">
                            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <button onclick="openModal()" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-upload text-white me-1 fs-5"></i> Import CSV
                        </button>
                    </div>
                    <div class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <button onclick="downloadData()" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-download text-white me-1 fs-5"></i> Download Data
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
                        <th>Student Name</th>
                        <th>Intake</th>
                        <th>Product</th>
                        <th>Partner</th>
                        <th>Commission</th>
                        <th>Exchange Rate</th>
                        <th>Total Commission</th>
                        <th>Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($applications as $application)
                        <tr>
                            <td>{{ $application->id }}</td>
                            <td>{{ $application->university }}</td>
                            <td>{{ $application->name }}</td>
                            <td>{{ $application->intake }}</td>
                            <td>{{ $application->product }}</td>
                            <td>{{ $application->partnerDetails }}</td>
                            <td>
                                <div class="editable-field" data-appid="{{ $application->id }}"
                                     data-field="commission">
                                    <span class="display-value">{{ $application->store->commission ?? '' }}</span>
                                    <button type="button" class="edit-btn">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <div class="edit-input commission-dropdown" style="display: none;">
                                        <div class="dropdown">
                                            <button class="btn dropdown-toggle form-control text-start custom-outline btn-sm"
                                                    type="button" id="commissionDropdown{{ $application->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                Select commission
                                            </button>
                                            <ul class="dropdown-menu commission-dropdown-menu w-100"
                                                aria-labelledby="commissionDropdown{{ $application->id }}"
                                                style="padding: 15px;">
                                                <li>
                                                    <div class="dropdown-item commission-row">
                                                        <div class="commission-checkbox-container">
                                                            <div class="form-check">
                                                                <input class="form-check-input commission-checkbox"
                                                                       type="checkbox"
                                                                       id="netCommission{{ $application->id }}"
                                                                       name="commissionTypes[]" value="Net">
                                                                <label class="form-check-label"
                                                                       for="netCommission{{ $application->id }}">Net</label>
                                                            </div>
                                                        </div>
                                                        <div class="commission-value-field"
                                                             id="netValueField{{ $application->id }}"
                                                             style="display: none;">
                                                            <input type="text"
                                                                   class="form-control commission-value-input"
                                                                   id="netCommissionValue{{ $application->id }}"
                                                                   name="commissionValues[Net]"
                                                                   placeholder="Enter net value">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <div class="dropdown-item commission-row">
                                                        <div class="commission-checkbox-container">
                                                            <div class="form-check">
                                                                <input class="form-check-input commission-checkbox"
                                                                       type="checkbox"
                                                                       id="grossCommission{{ $application->id }}"
                                                                       name="commissionTypes[]" value="Gross">
                                                                <label class="form-check-label"
                                                                       for="grossCommission{{ $application->id }}">Gross</label>
                                                            </div>
                                                        </div>
                                                        <div class="commission-value-field"
                                                             id="grossValueField{{ $application->id }}"
                                                             style="display: none;">
                                                            <input type="text"
                                                                   class="form-control commission-value-input"
                                                                   id="grossCommissionValue{{ $application->id }}"
                                                                   name="commissionValues[Gross]"
                                                                   placeholder="Enter gross value">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <div class="dropdown-item commission-row">
                                                        <div class="commission-checkbox-container">
                                                            <div class="form-check">
                                                                <input class="form-check-input commission-checkbox"
                                                                       type="checkbox"
                                                                       id="standardCommission{{ $application->id }}"
                                                                       name="commissionTypes[]" value="Standard">
                                                                <label class="form-check-label"
                                                                       for="standardCommission{{ $application->id }}">Standard</label>
                                                            </div>
                                                        </div>
                                                        <div class="commission-value-field"
                                                             id="standardValueField{{ $application->id }}"
                                                             style="display: none;">
                                                            <input type="text"
                                                                   class="form-control commission-value-input"
                                                                   id="standardCommissionValue{{ $application->id }}"
                                                                   name="commissionValues[Standard]"
                                                                   placeholder="Enter standard value">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <div class="dropdown-item commission-row">
                                                        <div class="commission-checkbox-container">
                                                            <div class="form-check">
                                                                <input class="form-check-input commission-checkbox"
                                                                       type="checkbox"
                                                                       id="bonusCommission{{ $application->id }}"
                                                                       name="commissionTypes[]" value="Bonus">
                                                                <label class="form-check-label"
                                                                       for="bonusCommission{{ $application->id }}">Bonus</label>
                                                            </div>
                                                        </div>
                                                        <div class="commission-value-field"
                                                             id="bonusValueField{{ $application->id }}"
                                                             style="display: none;">
                                                            <input type="text"
                                                                   class="form-control commission-value-input"
                                                                   id="bonusCommissionValue{{ $application->id }}"
                                                                   name="commissionValues[Bonus]"
                                                                   placeholder="Enter bonus value">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <div class="dropdown-item commission-row">
                                                        <div class="commission-checkbox-container">
                                                            <div class="form-check">
                                                                <input class="form-check-input commission-checkbox"
                                                                       type="checkbox"
                                                                       id="intensiveCommission{{ $application->id }}"
                                                                       name="commissionTypes[]" value="Intensive">
                                                                <label class="form-check-label"
                                                                       for="intensiveCommission{{ $application->id }}">Intensive</label>
                                                            </div>
                                                        </div>
                                                        <div class="commission-value-field"
                                                             id="intensiveValueField{{ $application->id }}"
                                                             style="display: none;">
                                                            <input type="text"
                                                                   class="form-control commission-value-input"
                                                                   id="intensiveCommissionValue{{ $application->id }}"
                                                                   name="commissionValues[Intensive]"
                                                                   placeholder="Enter intensive value">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <div class="dropdown-item px-0">
                                                        <button type="button"
                                                                class="btn btn-primary btn-sm w-100 save-commission-btn"
                                                                data-appid="{{ $application->id }}">
                                                            Save
                                                        </button>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="editable-field" data-appid="{{ $application->id }}" data-field="exchange">
                                    <span class="display-value">{{ $application->store->exchange ?? '' }}</span>
                                    <button type="button" class="edit-btn">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <div class="edit-input" style="display: none;">
                                        <input type="text" name="exchange" value="{{ $application->store->exchange ?? '' }}"
                                               class="edit-field-input">
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $commission = $application->store->commission ?? 0;
                                    $exchange = $application->store->exchange ?? 0;
                                    $totalCommission = $commission * $exchange;
                                @endphp
                                {{ number_format($totalCommission, 2) }}
                            </td>
                            <td>
                                <div class="editable-field" data-appid="{{ $application->id }}" data-field="remark">
                                    <span class="display-value">{{ $application->store->remark ?? '' }}</span>
                                    <button type="button" class="edit-btn">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <div class="edit-input" style="display: none;">
                                        <input type="text" name="remark" value="{{ $application->store->remark ?? '' }}"
                                               class="edit-field-input">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="importModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Excel File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add import form here -->
                    <form action="{{ route('finance.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="csvFile" class="form-label">Select CSV File</label>
                            <input type="file" class="form-control" id="csvFile" name="csvFile"
                                   accept=".csv,.xlsx,.xls">
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Style for the editable fields */
        .editable-field {
            position: relative;
            display: inline-block;
            margin: 5px 0;
        }

        .display-value {
            padding: 5px;
            min-width: 80px;
            display: inline-block;
        }

        .edit-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            padding: 3px 5px;
            margin-left: 5px;
            transition: color 0.2s;
            opacity: 0; /* Hide by default */
            visibility: hidden; /* Hide by default */
            transition: opacity 0.3s, visibility 0.3s;
        }

        .editable-field:hover .edit-btn {
            opacity: 1; /* Show on hover */
            visibility: visible; /* Show on hover */
        }

        .fas.fa-pencil-alt {
            font-size: 14px;
        }

        .edit-input input {
            padding: 5px 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 100px;
            transition: border-color 0.15s ease-in-out;
        }

        .edit-input input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .edit-input,
        .display-value {
            transition: opacity 0.3s, display 0.3s;
        }

        /* Commission dropdown styles */
        .commission-dropdown {
            min-width: 280px; /* Increased width */
        }

        .commission-dropdown .dropdown-menu {
            width: 100%;
            min-width: 280px; /* Increased width */
            padding: 15px;
        }

        .commission-dropdown .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            text-align: left;
        }

        /* Row layout improvements */
        .commission-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
            width: 100%;
        }

        .commission-checkbox-container {
            margin-bottom: 5px; /* Space between checkbox and input */
        }

        .commission-value-field {
            width: 100%;
            margin-top: 8px; /* Add space between checkbox and input */
            margin-bottom: 8px;
        }

        .commission-value-input {
            width: 100%;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            height: auto;
        }

        /* Save button styling */
        .save-commission-btn {
            margin-top: 10px;
            width: 100%;
            background-color: #2e7d32;
            border-color: #2e7d32;
        }

        .save-commission-btn:hover {
            background-color: #255d2a;
            border-color: #255d2a;
        }

        /* Divider styling */
        .dropdown-divider {
            margin: 8px 0;
        }

        /* SweetAlert custom styles */
        .swal-custom-popup {
            font-family: 'Arial', sans-serif;
        }

        .swal-custom-ok-button {
            background-color: #2e7d32 !important;
        }

        .swal-custom-cancel-button {
            background-color: #dc3545 !important;
            color: white !important;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // Function to filter the table based on search input
        function filterTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("dataTable");
            const rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                let visible = false;
                const cells = rows[i].getElementsByTagName("td");

                // Search through all cells (columns) in the row
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toLowerCase().includes(filter)) {
                        visible = true;
                        break;
                    }
                }

                rows[i].style.display = visible ? "" : "none";
            }
        }

        // Function to download the table data as an Excel file
        function downloadData() {
            const table = document.getElementById("dataTable");
            const wb = XLSX.utils.table_to_book(table, {sheet: "Finance Data"});
            XLSX.writeFile(wb, "finance_data.xlsx");
        }

        // Function to open the import modal
        function openModal() {
            const modal = new bootstrap.Modal(document.getElementById('importModal'));
            modal.show();
        }

        // Save data to the server via AJAX
        function saveData(appId, field, value) {
            // Create form data
            const formData = new FormData();

            // Add required fields to formData
            formData.append('application_id', appId);
            formData.append(field, value);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            // Show "Saving" message
            const loadingSwal = Swal.fire({
                title: 'Saving Data',
                html: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send AJAX request
            fetch('{{ route('stores.index') }}', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Close loading dialog
                    loadingSwal.close();

                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: "Data updated successfully",
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'swal-custom-popup',
                            confirmButton: 'swal-custom-ok-button'
                        }
                    });

                    // Update the display value
                    const displayValue = document.querySelector(`.editable-field[data-appid="${appId}"][data-field="${field}"] .display-value`);
                    if (displayValue) {
                        displayValue.textContent = value;
                    }

                    // If commission or exchange rate changed, update total commission calculation
                    if (field === 'commission' || field === 'exchange') {
                        updateTotalCommission(appId);
                    }
                })
                .catch(error => {
                    // Close loading dialog
                    loadingSwal.close();

                    console.error('Error:', error);

                    // Show error message
                    Swal.fire({
                        title: 'Error!',
                        text: "An error occurred while saving data",
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'swal-custom-popup',
                            confirmButton: 'swal-custom-ok-button'
                        }
                    });
                });
        }

        // Function to update the total commission calculation in the UI
        function updateTotalCommission(appId) {
            const row = document.querySelector(`tr td .editable-field[data-appid="${appId}"]`).closest('tr');
            const commissionDisplay = row.querySelector(`.editable-field[data-field="commission"] .display-value`);
            const exchangeDisplay = row.querySelector(`.editable-field[data-field="exchange"] .display-value`);
            const totalCommissionCell = row.cells[8]; // Total Commission column

            const commissionValue = parseFloat(commissionDisplay.textContent) || 0;
            const exchangeValue = parseFloat(exchangeDisplay.textContent) || 0;
            const totalCommission = commissionValue * exchangeValue;

            totalCommissionCell.textContent = totalCommission.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Get all edit buttons
            const editButtons = document.querySelectorAll('.edit-btn');

            // Add click event listener to each edit button
            editButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.stopPropagation(); // Prevent event bubbling

                    // Get the parent editable field container
                    const container = this.closest('.editable-field');

                    // Toggle display of value and input
                    const displayValue = container.querySelector('.display-value');
                    const editInput = container.querySelector('.edit-input');

                    if (editInput.style.display === 'none') {
                        // Switch to edit mode
                        displayValue.style.display = 'none';
                        editInput.style.display = 'block';

                        if (container.dataset.field === 'commission') {
                            // Special handling for commission dropdown
                            // Don't need to focus on input in dropdown case
                        } else {
                            const inputField = editInput.querySelector('input');
                            inputField.focus();
                            inputField.select(); // Select all text for easier editing
                            // Save the original value in case user cancels
                            inputField.dataset.originalValue = inputField.value;
                        }
                    }
                });
            });

            // Handle commission checkboxes
            document.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('commission-checkbox')) {
                    const appId = e.target.id.replace(/[^0-9]/g, '');
                    const commissionType = e.target.value;
                    const valueField = document.getElementById(`${commissionType.toLowerCase()}ValueField${appId}`);

                    if (e.target.checked) {
                        valueField.style.display = 'block';
                        valueField.querySelector('input').focus();
                    } else {
                        valueField.style.display = 'none';
                    }
                }
            });

            // Handle save button for commission dropdown
            document.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('save-commission-btn')) {
                    e.preventDefault();
                    e.stopPropagation();

                    const appId = e.target.dataset.appid;
                    const container = e.target.closest('.editable-field');
                    const commissionCheckboxes = container.querySelectorAll('.commission-checkbox:checked');

                    if (commissionCheckboxes.length === 0) {
                        Swal.fire({
                            title: 'Warning!',
                            text: "Please select at least one commission type",
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'swal-custom-popup',
                                confirmButton: 'swal-custom-ok-button'
                            }
                        });
                        return;
                    }

                    // Build commission string
                    let commissionData = [];
                    let totalCommissionValue = 0;

                    commissionCheckboxes.forEach(checkbox => {
                        const commissionType = checkbox.value;
                        const valueInput = document.getElementById(`${commissionType.toLowerCase()}CommissionValue${appId}`);
                        const commissionValue = parseFloat(valueInput.value) || 0;

                        if (commissionValue > 0) {
                            commissionData.push(`${commissionType}: ${commissionValue}`);
                            totalCommissionValue += commissionValue;
                        }
                    });

                    // Format for display and storage
                    const commissionString = commissionData.join(', ');

                    // Save the data
                    if (commissionData.length > 0) {
                        saveData(appId, 'commission', totalCommissionValue.toString());

                        // Also save the detailed breakdown for future reference
                        saveData(appId, 'commission_details', commissionString);

                        // Switch back to display mode
                        const displayValue = container.querySelector('.display-value');
                        const editInput = container.querySelector('.edit-input');
                        displayValue.style.display = 'inline-block';
                        editInput.style.display = 'none';
                    } else {
                        Swal.fire({
                            title: 'Warning!',
                            text: "Please enter at least one commission value",
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'swal-custom-popup',
                                confirmButton: 'swal-custom-ok-button'
                            }
                        });
                    }
                }
            });

            // Handle clicking outside the edit field
            document.addEventListener('click', function (e) {
                const editInputs = document.querySelectorAll('.edit-input:not(.commission-dropdown)');

                editInputs.forEach(editInput => {
                    if (editInput.style.display !== 'none') {
                        const container = editInput.closest('.editable-field');

                        // Check if the click was outside the current edit field
                        if (!container.contains(e.target) || (container.contains(e.target) && !editInput.contains(e.target) && !e.target.classList.contains('edit-btn'))) {
                            const inputField = editInput.querySelector('input');
                            const displayValue = container.querySelector('.display-value');
                            const appId = container.dataset.appid;
                            const field = container.dataset.field;
                            const newValue = inputField.value;
                            const originalValue = inputField.dataset.originalValue;

                            // Only show confirmation if value has changed
                            if (newValue !== originalValue) {
                                // Show confirmation dialog
                                Swal.fire({
                                    title: 'Save Changes?',
                                    text: `Do you want to save the new ${field} value: ${newValue}?`,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonText: 'Yes, save it!',
                                    cancelButtonText: 'No, cancel',
                                    customClass: {
                                        popup: 'swal-custom-popup',
                                        confirmButton: 'swal-custom-ok-button',
                                        cancelButton: 'swal-custom-cancel-button'
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Save the data
                                        saveData(appId, field, newValue);
                                    } else {
                                        // Revert to original value
                                        inputField.value = originalValue;
                                    }

                                    // Switch back to display mode
                                    displayValue.style.display = 'inline-block';
                                    editInput.style.display = 'none';
                                });
                            } else {
                                // No changes, just revert to display mode
                                displayValue.style.display = 'inline-block';
                                editInput.style.display = 'none';
                            }
                        }
                    }
                });
            });

            // Handle Enter key press to confirm edit
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    const activeInput = document.activeElement;
                    if (activeInput && activeInput.classList.contains('edit-field-input')) {
                        const container = activeInput.closest('.editable-field');
                        const appId = container.dataset.appid;
                        const field = container.dataset.field;
                        const newValue = activeInput.value;
                        const originalValue = activeInput.dataset.originalValue;

                        // Only proceed if value has changed
                        if (newValue !== originalValue) {
                            // Show confirmation dialog
                            Swal.fire({
                                title: 'Save Changes?',
                                text: `Do you want to save the new ${field} value: ${newValue}?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, save it!',
                                cancelButtonText: 'No, cancel',
                                customClass: {
                                    popup: 'swal-custom-popup',
                                    confirmButton: 'swal-custom-ok-button',
                                    cancelButton: 'swal-custom-cancel-button'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Save the data
                                    saveData(appId, field, newValue);
                                } else {
                                    // Revert to original value
                                    activeInput.value = originalValue;
                                }

                                // Switch back to display mode
                                const displayValue = container.querySelector('.display-value');
                                const editInput = container.querySelector('.edit-input');
                                displayValue.style.display = 'inline-block';
                                editInput.style.display = 'none';
                            });
                        } else {
                            // No changes, just revert to display mode
                            const displayValue = container.querySelector('.display-value');
                            const editInput = container.querySelector('.edit-input');
                            displayValue.style.display = 'inline-block';
                            editInput.style.display = 'none';
                        }

                        e.preventDefault(); // Prevent form submission if in a form
                    }
                }
            });

            // Initialize commission dropdown button text based on existing data
            document.querySelectorAll('[data-field="commission"]').forEach(commissionField => {
                const appId = commissionField.dataset.appid;
                const displayValue = commissionField.querySelector('.display-value').textContent.trim();

                if (displayValue) {
                    const dropdownButton = document.getElementById(`commissionDropdown${appId}`);
                    if (dropdownButton) {
                        dropdownButton.textContent = `Commission: ${displayValue}`;
                    }
                }
            });
        });
    </script>
@endsection
