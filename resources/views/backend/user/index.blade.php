@extends('layouts.admin')
@include('backend.script.alert')

@section('content')

    <!-- Include SweetAlert -->
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
        <style>
            .swal-custom-popup {
                width: 420px !important;
                padding: 15px;
                font-size: 14px;
            }

            .application-dropdown .dropdown-menu {
                position: absolute !important;
                inset: 0px auto auto 0px !important;
                margin: 0px !important;
                transform: translate3d(0px, 40px, 0px) !important;
                width: auto !important;
                min-width: 100% !important;
                max-width: 300px !important;
                z-index: 1050 !important;
            }

            /* Style for clickable dropdown headers */
            .clickable-header {
                cursor: pointer;
                padding: 5px 10px;
                border-radius: 4px;
                transition: all 0.2s ease;
            }

            .clickable-header:hover {
                background-color: rgba(46, 125, 50, 0.1) !important;
                color: #2e7d32 !important;
            }

            /* Style for dropdown sections */
            .dropdown-section {
                margin-left: 15px;
                padding: 5px;
                border-left: 2px solid #dee2e6;
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

            /* Styles for combined dropdown */
            .nested-dropdown-menu {
                padding: 0;
                border: none;
                box-shadow: none;
            }

            .nested-dropdown-section {
                padding: 10px;
                border-bottom: 1px solid #eee;
            }

            .nested-dropdown-section:last-child {
                border-bottom: none;
            }

            .nested-dropdown-header {
                font-weight: bold;
                margin-bottom: 5px;
            }

            .nested-dropdown-item {
                padding: 5px 10px;
                cursor: pointer;
                display: block;
                clear: both;
                font-weight: 400;
                color: #212529;
                text-align: inherit;
                white-space: nowrap;
                background-color: transparent;
                border: 0;
                transition: all 0.2s ease;
            }

            .nested-dropdown-item:hover,
            .nested-dropdown-item:focus {
                color: #1e2125;
                text-decoration: none;
                background-color: #f8f9fa;
            }

            /* Hover effects for dropdown items */
            .application-dropdown .dropdown-menu .form-check {
                padding: 5px 10px;
                margin: 0;
                border-radius: 4px;
                transition: all 0.2s ease;
            }

            .application-dropdown .dropdown-menu .form-check:hover {
                background-color: rgba(46, 125, 50, 0.1);
            }

            .application-dropdown .dropdown-menu .form-check-label {
                cursor: pointer;
                width: 100%;
                padding-left: 5px;
                transition: all 0.2s ease;
            }

            .application-dropdown .dropdown-menu .form-check:hover .form-check-label {
                color: #2e7d32;
            }

            .application-dropdown .dropdown-menu .form-check-input {
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .application-dropdown .dropdown-menu .form-check-input:hover {
                border-color: #2e7d32;
            }

            /* Dropdown toggle button hover */
            .application-dropdown .dropdown-toggle:hover {
                background-color: rgba(46, 125, 50, 0.1);
                color: #2e7d32;
            }

            /* Select all button hover */
            .application-dropdown .btn-outline-primary:hover {
                background-color: #2e7d32 !important;
                color: white !important;
                border-color: #2e7d32 !important;
            }

            /* Checkbox focus style */
            .application-dropdown .form-check-input:focus {
                box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
                border-color: #2e7d32;
            }

            /* Add cursor pointer to dropdown items */
            .dropdown-item {
                cursor: pointer;
            }

            /* Style the table rows for a pointer cursor on hover */
            #dataTable tbody tr {
                cursor: pointer;
            }

            /* Make sure the label has cursor pointer */
            .form-check-label {
                cursor: pointer;
            }

            /* Style for application/lead text hover effect */
            .selected-text {
                transition: all 0.3s ease;
                padding: 3px 8px;
                border-radius: 4px;
            }

            .dropdown-toggle:hover .selected-text {
                background-color: rgba(46, 125, 50, 0.1);
                color: #2e7d32;
            }

            /* Hover effect for dropdown headers */
            .dropdown-header:hover {
                color: green !important;
            }
        </style>
    @endif

    <div class="main-container">
        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-12 col-xl-12">
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">User Management</div>
                    </div>
                    <div class="col-md-6 col-xl-8">
                        <div class="position-relative">
                            <input type="text" class="form-control product-search ps-5" id="searchInput"
                                   onkeyup="filterTable()" placeholder="Search users...">
                            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </div>
                    </div>
                    <div
                        class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <button id="btn-add-user" onclick="showPopup()" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-user-plus text-white me-1 fs-5"></i> Add User
                        </button>
                    </div>
                    <div
                        class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <button id="btn-export-users" onclick="exportUsers()"
                                class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-download text-white me-1 fs-5"></i> Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- User Data Table -->
            <div class="table-responsive mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle" id="dataTable">
                    <thead class="text-dark fs-4">
                    <tr>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">ID</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">User</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Name</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Email</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Assigned Applications/Leads</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Role</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Status</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Actions</h6>
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                     @foreach ($users as $index => $user)
                        <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                               <img src="{{ $user->avatar ? asset('storage/avatars/' . $user->avatar) : asset('assets/images/profile/user-1.jpg') }}" alt="user" width="45" height="45" class="img-fluid rounded-circle">
                            </div>
                        </td>
                            <td>{{ $user->name }} {{ $user->last }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <div class="application-dropdown" data-user-id="{{ $user->id }}">
                                    <button class="btn dropdown-toggle w-auto text-start" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="selected-text">
                                            @php
                                                $assignedApplications = is_array($user->application) ? $user->application : [];
    $assignedLeads = is_array($user->lead) ? $user->lead : [];

                                                $applicationCount = count($assignedApplications);
                                                $leadCount = count($assignedLeads);

                                                if ($applicationCount === 0 && $leadCount === 0) {
                                                    echo 'No assignments';
                                                } else {
                                                    $parts = [];
                                                    if ($applicationCount > 0) {
                                                        $parts[] = $applicationCount . ' Application' . ($applicationCount > 1 ? 's' : '');
                                                    }
                                                    if ($leadCount > 0) {
                                                        $parts[] = $leadCount . ' Lead' . ($leadCount > 1 ? 's' : '');
                                                    }
                                                    echo implode(', ', $parts);
                                                }
                                            @endphp
                                        </span>
                                    </button>
                                    <div class="dropdown-menu p-2 w-auto" style="min-width: 250px;">
                                        <!-- Applications Section - Initially collapsed -->
                                        <div class="mb-3">
                                            <h6 class="dropdown-header clickable-header"
                                                onclick="toggleDropdownSection(this, 'app-section-{{ $user->id }}')">
                                                <i class="ti ti-chevron-right me-1"></i> Applications
                                            </h6>
                                            <div id="app-section-{{ $user->id }}" style="display: none; margin-left: 15px;">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input app-checkbox" type="checkbox"
                                                           name="app-checkbox-{{ $user->id }}"
                                                           id="app-none-{{ $user->id }}"
                                                           value=""
                                                           {{ empty($user->application) ? 'checked' : '' }}
                                                           onchange="handleNoneSelection({{ $user->id }}, this.checked, 'app')">
                                                    <label class="form-check-label w-100"
                                                           for="app-none-{{ $user->id }}">
                                                        No Applications
                                                    </label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2"
                                                        onclick="selectAll( {{ $user->id }}, 'app')">
                                                    Select All Applications
                                                </button>
                                                <div style="max-height: 200px; overflow-y: auto;">
                                                @php
    // List of app-related roles
    $appRoles = ['Applications User', 'Applications Manager', 'Administrator', 'Manager'];
    
    // Filter users who have application-related roles
    $appUsers = $users->filter(function($assignUser) use ($appRoles, $user) {
        // Don't show the current user in their own dropdown
        if ($assignUser->id == $user->id) {
            return false;
        }
        
        // Check if user has a role and if it's in the app roles list
        return $assignUser->role && in_array($assignUser->role, $appRoles);
    });
@endphp
                                                    
                                                    @if($appUsers->count() > 0)
                                                        @foreach ($appUsers as $assignUser)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input app-checkbox"
                                                                       type="checkbox"
                                                                       name="app-checkbox-{{ $user->id }}[]"
                                                                       id="app-{{ $user->id }}-{{ $assignUser->id }}"
                                                                       value="{{ $assignUser->id }}"
                                                                       {{ $user->application && in_array($assignUser->id, $user->application) ? 'checked' : '' }}
                                                                       onchange="handleCheckboxChange({{ $user->id }}, this, 'app')">
                                                                <label class="form-check-label w-100"
                                                                       for="app-{{ $user->id }}-{{ $assignUser->id }}">
                                                                    {{ $assignUser->name }} {{ $assignUser->last }} 
                                                                    <span class="text-muted">({{ $assignUser->role }})</span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-muted text-center py-2">
                                                            No application users available
                                                        </div>
                                                    @endif
                                                </div>
                                                <input type="hidden"
                                                       class="selected-app"
                                                       name="application"
                                                       value="{{ json_encode($user->application) }}"
                                                       data-user-id="{{ $user->id }}"
                                                       data-previous-value="{{ json_encode($user->application) }}"
                                                       data-open-value="{{ json_encode($user->application) }}">
                                                <div id="app-alert-{{ $user->id }}" style="display: none;" class="alert mt-2"></div>
                                            </div>
                                        </div>

                                        <!-- Leads Section - Initially collapsed -->
                                        <div class="mb-2">
                                            <h6 class="dropdown-header clickable-header"
                                                onclick="toggleDropdownSection(this, 'lead-section-{{ $user->id }}')">
                                                <i class="ti ti-chevron-right me-1"></i> Leads
                                            </h6>
                                            <div id="lead-section-{{ $user->id }}" style="display: none; margin-left: 15px;">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input lead-checkbox" type="checkbox"
                                                           name="lead-checkbox-{{ $user->id }}"
                                                           id="lead-none-{{ $user->id }}"
                                                           value=""
                                                           {{ empty($user->lead) ? 'checked' : '' }}
                                                           onchange="handleNoneSelection({{ $user->id }}, this.checked, 'lead')">
                                                    <label class="form-check-label w-100" for="lead-none-{{ $user->id }}">
                                                        No Leads
                                                    </label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2"
                                                        onclick="selectAll({{ $user->id }}, 'lead')">
                                                    Select All Leads
                                                </button>
                                                <div style="max-height: 200px; overflow-y: auto;">
                                                @php
    // List of lead-related roles
    $leadRoles = ['Leads User', 'Leads Manager', 'Administrator', 'Manager'];
    
    // Filter users who have lead-related roles
    $leadUsers = $users->filter(function($assignUser) use ($leadRoles, $user) {
        // Don't show the current user in their own dropdown
        if ($assignUser->id == $user->id) {
            return false;
        }
        
        // Check if user has a role and if it's in the lead roles list
        return $assignUser->role && in_array($assignUser->role, $leadRoles);
    });
@endphp
                                                    
                                                    @if($leadUsers->count() > 0)
                                                        @foreach ($leadUsers as $assignUser)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input lead-checkbox"
                                                                       type="checkbox"
                                                                       name="lead-checkbox-{{ $user->id }}[]"
                                                                       id="lead-{{ $user->id }}-{{ $assignUser->id }}"
                                                                       value="{{ $assignUser->id }}"
                                                                       {{ $user->lead && in_array($assignUser->id, $user->lead) ? 'checked' : '' }}
                                                                       onchange="handleCheckboxChange({{ $user->id }}, this, 'lead')">
                                                                <label class="form-check-label w-100"
                                                                       for="lead-{{ $user->id }}-{{ $assignUser->id }}">
                                                                    {{ $assignUser->name }} {{ $assignUser->last }}
                                                                    <span class="text-muted">({{ $assignUser->role }})</span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-muted text-center py-2">
                                                            No lead users available
                                                        </div>
                                                    @endif
                                                </div>
                                                <input type="hidden"
                                                       class="selected-lead"
                                                       name="lead"
                                                       value="{{ json_encode($user->lead) }}"
                                                       data-user-id="{{ $user->id }}"
                                                       data-previous-value="{{ json_encode($user->lead) }}"
                                                       data-open-value="{{ json_encode($user->lead) }}">
                                                <div id="lead-alert-{{ $user->id }}" style="display: none;" class="alert mt-2"></div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="btn dropdown-toggle w-auto text-start" style="border: none;" name="role"
                                        onchange="updateRole({{ $user->id }}, this.value)" data-user-id="{{ $user->id }}">
                                    @foreach ($roles as $role)
                                        <option
                                            value="{{ $role->name }}" {{ $user->role === $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="alert-{{ $user->id }}" style="display: none;" class="alert mt-2"></div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="badge bg-light-{{ $user->deleted_at ? 'danger' : 'success' }} rounded-3 fw-semibold">
                                        {{ $user->deleted_at ? 'Inactive' : 'Active' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton"
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-6"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                               href="{{ route('user.edit', $user->id) }}">
                                                <i class="fs-4 ti ti-edit"></i> Edit
                                            </a>
                                        </li>
                                        @if($user->deleted_at)
                                            <li>
                                                <form action="{{ route('user.restore', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                            class="dropdown-item d-flex align-items-center gap-3 text-success">
                                                        <i class="fs-4 ti ti-refresh"></i> Restore
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                                      onsubmit="return confirmDelete(event)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="dropdown-item d-flex align-items-center gap-3 text-danger">
                                                        <i class="fs-4 ti ti-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
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

    <!-- Add User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (Route::has('register'))
                        <iframe src="{{ route('register') }}" style="width: 100%; height: 500px; border: none;"></iframe>
                    @else
                        <div class="alert alert-warning">
                            Registration route not available
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    // Initialize stored values for role selects
    const roleSelects = document.querySelectorAll('select[name="role"]');
    roleSelects.forEach(select => {
        select.dataset.previousValue = select.value;
    });

    // Initialize stored values for application and lead dropdowns
    const appInputs = document.querySelectorAll('.selected-app');
    appInputs.forEach(input => {
        input.dataset.previousValue = input.value;
        input.dataset.openValue = input.value;
    });

    const leadInputs = document.querySelectorAll('.selected-lead');
    leadInputs.forEach(input => {
        input.dataset.previousValue = input.value;
        input.dataset.openValue = input.value;
    });

    // Prevent dropdown from closing on clicks inside the menu
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    });

    // Add event listeners for all application dropdowns
    document.querySelectorAll('.application-dropdown .dropdown-menu').forEach(menu => {
        menu.addEventListener('change', function(event) {
            if (event.target.classList.contains('app-checkbox') || event.target.classList.contains('lead-checkbox')) {
                const checkbox = event.target;
                const userId = checkbox.closest('.application-dropdown').dataset.userId;
                const type = checkbox.classList.contains('app-checkbox') ? 'app' : 'lead';
                handleCheckboxChange(userId, checkbox, type);
            }
        });
    });

    // Check if user table is properly populated
    const userRows = document.querySelectorAll('#dataTable tbody tr');
    console.log('User rows count:', userRows.length);
    if (userRows.length === 0) {
        console.warn('No user rows found in the table.');
    }

    // Initialize search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', filterTable);
    }
});

// Show the popup modal for adding a new user
function showPopup() {
    var userModal = new bootstrap.Modal(document.getElementById('userModal'));
    userModal.show();
}

// Filter the table based on the search input
function filterTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("dataTable");
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName("td");
        let showRow = false;

        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent || cells[j].innerText;
            if (cellText.toLowerCase().includes(filter)) {
                showRow = true;
                break;
            }
        }

        rows[i].style.display = showRow ? "" : "none";
    }
}

// Export users data to CSV
function exportUsers() {
    // Get users data from the table instead of relying on JSON variable
    const table = document.getElementById("dataTable");
    const rows = table.querySelectorAll("tbody tr");
    const data = [["ID", "Name", "Email", "Role", "Status"]];
    
    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        if (cells.length >= 7) {
            data.push([
                cells[0].textContent.trim(), // ID
                cells[2].textContent.trim(), // Name
                cells[3].textContent.trim(), // Email
                cells[5].querySelector("select") ? 
                    cells[5].querySelector("select").value : cells[5].textContent.trim(), // Role
                cells[6].textContent.trim() // Status
            ]);
        }
    });

    const csvContent = data.map(row => 
        row.map(cell => `"${cell.replace(/"/g, '""')}"`).join(",")
    ).join("\n");
    
    const blob = new Blob([csvContent], {type: "text/csv;charset=utf-8;"});
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", "users_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Confirm user deletion with SweetAlert
function confirmDelete(event) {
    event.preventDefault();
    const form = event.target;

    Swal.fire({
        title: 'Are you sure?',
        text: "This will deactivate the user. Their applications will need to be reassigned.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, deactivate it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

// Toggle dropdown sections when headers are clicked
function toggleDropdownSection(headerElement, sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) {
        console.error(`Section not found: ${sectionId}`);
        return;
    }
    
    const icon = headerElement.querySelector('i');

    if (section.style.display === 'none') {
        section.style.display = 'block';
        icon.classList.remove('ti-chevron-right');
        icon.classList.add('ti-chevron-down');
    } else {
        section.style.display = 'none';
        icon.classList.remove('ti-chevron-down');
        icon.classList.add('ti-chevron-right');
    }
}

function handleNoneSelection(userId, isChecked, type) {
    const container = document.querySelector(`.application-dropdown[data-user-id="${userId}"]`);
    if (!container) {
        console.error(`Container not found for user ID: ${userId}`);
        return;
    }
    
    const hiddenInput = container.querySelector(`.selected-${type}`);
    const dropdownToggle = container.querySelector('.dropdown-toggle .selected-text');
    const noneCheckbox = document.getElementById(`${type}-none-${userId}`);
    const checkboxes = document.querySelectorAll(`input[name="${type}-checkbox-${userId}[]"]`);

    if (isChecked) {
        checkboxes.forEach(cb => cb.checked = false);
        hiddenInput.dataset.previousValue = hiddenInput.value;
        hiddenInput.value = JSON.stringify([]);
        
        // Update dropdown text
        updateDropdownText(userId);

        updateAssignment(userId, [], type);
    }
}

// Function to update the dropdown text based on selected items
function updateDropdownText(userId) {
    const container = document.querySelector(`.application-dropdown[data-user-id="${userId}"]`);
    if (!container) return;
    
    const dropdownToggle = container.querySelector('.dropdown-toggle .selected-text');
    const appInput = container.querySelector('.selected-app');
    const leadInput = container.querySelector('.selected-lead');
    
    const appValues = appInput ? JSON.parse(appInput.value || '[]') : [];
    const leadValues = leadInput ? JSON.parse(leadInput.value || '[]') : [];
    
    const applicationCount = appValues.length;
    const leadCount = leadValues.length;
    
    if (applicationCount === 0 && leadCount === 0) {
        dropdownToggle.textContent = 'No assignments';
    } else {
        const parts = [];
        if (applicationCount > 0) {
            parts.push(applicationCount + ' Application' + (applicationCount > 1 ? 's' : ''));
        }
        if (leadCount > 0) {
            parts.push(leadCount + ' Lead' + (leadCount > 1 ? 's' : ''));
        }
        dropdownToggle.textContent = parts.join(', ');
    }
}

// Generic function to select all checkboxes for a specific type (application or lead)
function selectAll(userId, type) {
    const container = document.querySelector(`.application-dropdown[data-user-id="${userId}"]`);
    if (!container) {
        console.error(`Container not found for user ID: ${userId}`);
        return;
    }
    
    const hiddenInput = container.querySelector(`.selected-${type}`);
    const noneCheckbox = document.getElementById(`${type}-none-${userId}`);
    const checkboxes = document.querySelectorAll(`input[name="${type}-checkbox-${userId}[]"]`);

    if (checkboxes.length === 0) {
        console.warn(`No checkboxes found for ${type} type and user ID: ${userId}`);
        return;
    }

    noneCheckbox.checked = false; // Uncheck the "None" option
    checkboxes.forEach(cb => cb.checked = true); // Check all checkboxes

    const selectedIds = Array.from(checkboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value); // Get all selected IDs

    hiddenInput.value = JSON.stringify(selectedIds); // Update the hidden input value
    
    // Update dropdown text display
    updateDropdownText(userId);

    updateAssignment(userId, selectedIds, type); // Update on server
}

// Generic function to handle checkbox changes (Application or Lead)
function handleCheckboxChange(userId, checkboxElem, type) {
    const container = document.querySelector(`.application-dropdown[data-user-id="${userId}"]`);
    if (!container) {
        console.error(`Container not found for user ID: ${userId}`);
        return;
    }
    
    const hiddenInput = container.querySelector(`.selected-${type}`);
    const noneCheckbox = document.getElementById(`${type}-none-${userId}`);
    const checkboxes = document.querySelectorAll(`input[name="${type}-checkbox-${userId}[]"]`);

    if (checkboxElem.checked) {
        noneCheckbox.checked = false; // Uncheck the "None" option
    }

    const selectedIds = Array.from(checkboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value); // Get all selected IDs

    if (selectedIds.length === 0) {
        noneCheckbox.checked = true; // If no checkboxes are selected, select the "None" option
    }

    hiddenInput.value = JSON.stringify(selectedIds); // Update the hidden input value
    
    // Update dropdown text display
    updateDropdownText(userId);

    updateAssignment(userId, selectedIds, type); // Update on server
}

// Generic function to update assignment (Application or Lead)
function updateAssignment(userId, newAssignments, type) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    
    const container = document.querySelector(`.application-dropdown[data-user-id="${userId}"]`);
    if (!container) {
        console.error(`Application container not found for user ID: ${userId}`);
        return;
    }

    const hiddenInput = container.querySelector(`.selected-${type}`);
    if (!hiddenInput) {
        console.error(`Hidden input for ${type} not found for user ID: ${userId}`);
        return;
    }

    const alertDiv = document.getElementById(`${type}-alert-${userId}`);
    const dropdownButton = container.querySelector('.dropdown-toggle');
    dropdownButton.disabled = true;

    // Show loading state
    const loadingToast = Swal.fire({
        title: 'Updating...',
        text: `Updating ${type} assignments`,
        icon: 'info',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    let url = `/user/${userId}/update-application`;
    let data = {application: newAssignments};

    if (type === 'lead') {
        url = `/user/${userId}/update-lead`;
        data = {lead: newAssignments};
    }
    else if (type === 'app') {
        url = `/user/${userId}/update-application`;
        data = {application: newAssignments};
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            loadingToast.close();

            // Show success toast
            Swal.fire({
                title: 'Success!',
                text: `${type.charAt(0).toUpperCase() + type.slice(1)} assignments updated successfully`,
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            // Update the stored values
            hiddenInput.dataset.previousValue = JSON.stringify(newAssignments);
            hiddenInput.dataset.openValue = JSON.stringify(newAssignments);
        })
        .catch(error => {
            console.error('Error:', error);
            loadingToast.close();

            // Show error toast
            Swal.fire({
                title: 'Error!',
                text: error.message || `Failed to update ${type} assignments`,
                icon: 'error',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            // Revert to previous value
            const prevValue = JSON.parse(hiddenInput.dataset.previousValue);
            hiddenInput.value = JSON.stringify(prevValue);

            // Update checkboxes to match previous state
            updateCheckboxesFromValue(userId, prevValue, type);
        })
        .finally(() => {
            dropdownButton.disabled = false;
            if (alertDiv) alertDiv.style.display = 'none';
        });
}

// Update checkboxes based on stored value
function updateCheckboxesFromValue(userId, value, type) {
    // Uncheck all first
    const allCheckboxes = document.querySelectorAll(`input[name="${type}-checkbox-${userId}[]"]`);
    allCheckboxes.forEach(cb => cb.checked = false);

    // Handle "None" checkbox
    const noneCheckbox = document.getElementById(`${type}-none-${userId}`);
    if (!value || value.length === 0) {
        if (noneCheckbox) noneCheckbox.checked = true;
        return;
    }
    if (noneCheckbox) noneCheckbox.checked = false;

    // Check the appropriate checkboxes
    value.forEach(id => {
        const checkbox = document.getElementById(`${type}-${userId}-${id}`);
        if (checkbox) checkbox.checked = true;
    });
}

// Update user role
function updateRole(userId, newRole) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    
    const select = document.querySelector(`select[name="role"][data-user-id="${userId}"]`);
    if (!select) {
        console.error(`Role select not found for user ID: ${userId}`);
        return;
    }
    
    const alertDiv = document.getElementById(`alert-${userId}`);

    select.disabled = true;

    // Show loading state
    const loadingToast = Swal.fire({
        title: 'Updating...',
        text: 'Updating user role',
        icon: 'info',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`/user/${userId}/update-role`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({role: newRole})
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            loadingToast.close();

            // Show success toast
            Swal.fire({
                title: 'Success!',
                text: 'User role updated successfully',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            // Update previous value
            select.dataset.previousValue = newRole;
        })
        .catch(error => {
            console.error('Error:', error);
            loadingToast.close();

            // Show error toast
            Swal.fire({
                title: 'Error!',
                text: error.message || 'Failed to update user role',
                icon: 'error',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            // Revert to previous value
            select.value = select.dataset.previousValue;
        })
        .finally(() => {
            select.disabled = false;
            if (alertDiv) alertDiv.style.display = 'none';
        });
}

// Add debug function to help troubleshoot UI issues
function debugUserTable() {
    const table = document.getElementById('dataTable');
    if (!table) {
        console.error('Data table not found');
        return;
    }
    
    const rows = table.querySelectorAll('tbody tr');
    console.log(`Found ${rows.length} user rows`);
    
    if (rows.length === 0) {
        // Check if tbody exists
        const tbody = table.querySelector('tbody');
        if (!tbody) {
            console.error('Table body (tbody) not found');
        } else {
            console.log('Table body exists but contains no rows');
        }
        
        // Check if the table is hidden
        const tableDisplay = window.getComputedStyle(table).display;
        console.log(`Table display style: ${tableDisplay}`);
    }
}
</script>
@endsection