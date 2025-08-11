@extends('layouts.admin')
@include('backend.script.session')
@include('backend.script.alert')
@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<!-- Tabler Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-semibold text-secondary">
                <i class="ti ti-percent me-2"></i>
                Commission Details - {{ $commission_payable->university }}
            </h4>
            <a href="{{ route('backend.commission.payable.record', ['id' => $commission_payable->id]) }}"
               class="btn btn-sm btn-primary">
                <i class="ti ti-file-text me-1"></i> View Record
            </a>
        </div>
    </div>
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <i class="ti ti-circle-check me-2"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Toast notification container -->
        <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

        <!-- Loading overlay -->
        <div class="loading-overlay">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div id="financeForm">
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <!-- Main Commission Information Card -->
            <div class="card mb-4 border">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 fw-semibold text-secondary">
                        <i class="ti ti-info-circle me-2"></i>
                        Commission Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- University Field -->
                        <div class="col-md-6 mb-3" data-field="university">
                            <label class="form-label text-muted small mb-1">University</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-building-bank text-muted"></i></span>
                                <div class="editable-field form-control">
                                    <span class="field-value-content">{{ $commission_payable->university }}</span>
                                    <span class="edit-icon ms-2">
                                        <i class="ti ti-edit text-muted"></i>
                                    </span>
                                    <span class="save-indicator ms-2"></span>
                                </div>
                                <input type="text" name="university" class="form-control edit-field d-none"
                                       value="{{ $commission_payable->university }}"
                                       data-field-name="university"
                                       data-original-value="{{ $commission_payable->university }}">
                            </div>
                        </div>

                        <!-- Product Field -->
                        <div class="col-md-6 mb-3" data-field="product">
                            <label class="form-label text-muted small mb-1">Product</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-globe text-muted"></i></span>
                                <div class="editable-field form-control">
                                    <span class="field-value-content">{{ $commission_payable->product }}</span>
                                    <span class="edit-icon ms-2">
                                        <i class="ti ti-edit text-muted"></i>
                                    </span>
                                    <span class="save-indicator ms-2"></span>
                                </div>
                                <input type="text" name="product" class="form-control edit-field d-none"
                                       value="{{ $commission_payable->product }}"
                                       data-field-name="product"
                                       data-original-value="{{ $commission_payable->product }}">
                            </div>
                        </div>

                        <!-- Partner Field -->
                        <div class="col-md-6 mb-3" data-field="partner">
                            <label class="form-label text-muted small mb-1">Partner</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-users text-muted"></i></span>
                                <div class="editable-field form-control">
                                    <span class="field-value-content">{{ $commission_payable->partner }}</span>
                                    <span class="edit-icon ms-2">
                                        <i class="ti ti-edit text-muted"></i>
                                    </span>
                                    <span class="save-indicator ms-2"></span>
                                </div>
                                <input type="text" name="partner" class="form-control edit-field d-none"
                                       value="{{ $commission_payable->partner }}"
                                       data-field-name="partner"
                                       data-original-value="{{ $commission_payable->partner }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bonus, incentive & Progressive Commissions -->
            <div class="card mb-4 border">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 fw-semibold text-secondary">
                        <i class="ti ti-star me-2"></i>
                        Special Commissions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">

                        <!-- Bonus Commission -->
                        <div class="col-md-6 mb-3" data-field="bonus_commission">
                            <div class="border p-3 rounded">
                                <h6 class="mb-3 fw-semibold text-secondary">
                                    <i class="ti ti-gift me-2"></i>Bonus Commission
                                </h6>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input has-commission-radio"
                                           type="checkbox"
                                           id="bonusCommissionToggle"
                                           name="has_bonus_commission"
                                           {{ $commission_payable->has_bonus_commission ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bonusCommissionToggle">
                                        Enable Bonus Commission
                                    </label>
                                </div>
                                <div id="bonusCommissionValueSection"
                                     class="{{ $commission_payable->has_bonus_commission ? '' : 'd-none' }}">
                                    <label class="form-label text-muted small mb-1">Commission Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-percent text-muted"></i></span>
                                        <div class="editable-field form-control">
                                            <span class="field-value-content">
                                                {{ $commission_payable->bonus_commission ?? 'Not set' }}
                                            </span>
                                            <span class="edit-icon ms-2">
                                                <i class="ti ti-edit text-muted"></i>
                                            </span>
                                            <span class="save-indicator ms-2"></span>
                                        </div>
                                        <input type="text" name="bonus_commission"
                                               class="form-control edit-field d-none"
                                               value="{{ $commission_payable->bonus_commission }}"
                                               data-field-name="bonus_commission"
                                               data-original-value="{{ $commission_payable->bonus_commission }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- incentive Commission -->
                        <div class="col-md-6 mb-3" data-field="incentive_commission">
                            <div class="border p-3 rounded">
                                <h6 class="mb-3 fw-semibold text-secondary">
                                    <i class="ti ti-bolt me-2"></i>incentive Commission
                                </h6>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input has-commission-radio"
                                           type="checkbox"
                                           id="incentiveCommissionToggle"
                                           name="has_incentive_commission"
                                           {{ $commission_payable->has_incentive_commission ? 'checked' : '' }}>
                                    <label class="form-check-label" for="incentiveCommissionToggle">
                                        Enable incentive Commission
                                    </label>
                                </div>
                                <div id="incentiveCommissionValueSection"
                                     class="{{ $commission_payable->has_incentive_commission ? '' : 'd-none' }}">
                                    <label class="form-label text-muted small mb-1">Commission Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-percent text-muted"></i></span>
                                        <div class="editable-field form-control">
                                            <span class="field-value-content">
                                                {{ $commission_payable->incentive_commission ?? 'Not set' }}
                                            </span>
                                            <span class="edit-icon ms-2">
                                                <i class="ti ti-edit text-muted"></i>
                                            </span>
                                            <span class="save-indicator ms-2"></span>
                                        </div>
                                        <input type="text" name="incentive_commission"
                                               class="form-control edit-field d-none"
                                               value="{{ $commission_payable->incentive_commission }}"
                                               data-field-name="incentive_commission"
                                               data-original-value="{{ $commission_payable->incentive_commission }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progressive Commission -->
                        <div class="col-md-6 mb-3" data-field="progressive_commission">
                            <div class="border p-3 rounded">
                                <h6 class="mb-3 fw-semibold text-secondary">
                                    <i class="ti ti-chart-line me-2"></i>Progressive Commission
                                </h6>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input has-commission-radio"
                                           type="checkbox"
                                           id="progressiveCommissionToggle"
                                           name="has_progressive_commission"
                                           {{ $commission_payable->has_progressive_commission ? 'checked' : '' }}>
                                    <label class="form-check-label" for="progressiveCommissionToggle">
                                        Enable Progressive Commission
                                    </label>
                                </div>
                                <div id="progressiveCommissionValueSection"
                                     class="{{ $commission_payable->has_progressive_commission ? '' : 'd-none' }}">
                                    <label class="form-label text-muted small mb-1">Commission Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-percent text-muted"></i></span>
                                        <div class="editable-field form-control">
                                            <span class="field-value-content">
                                                {{ $commission_payable->progressive_commission ?? 'Not set' }}
                                            </span>
                                            <span class="edit-icon ms-2">
                                                <i class="ti ti-edit text-muted"></i>
                                            </span>
                                            <span class="save-indicator ms-2"></span>
                                        </div>
                                        <input type="text" name="progressive_commission"
                                               class="form-control edit-field d-none"
                                               value="{{ $commission_payable->progressive_commission }}"
                                               data-field-name="progressive_commission"
                                               data-original-value="{{ $commission_payable->progressive_commission }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Commission Types Card -->
            <div class="card border">
                <div class="card-header bg-light py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold text-secondary">
                            <i class="ti ti-list-details me-2"></i>
                            Commission Types
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addCommissionBtn">
                            <i class="ti ti-plus me-1"></i> Add Type
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Net Commission -->
                        <div class="col-md-4 mb-3" data-field="commission" data-commission-type="Net">
                            <div class="border p-3 rounded h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="p-2 me-2">
                                        <i class="ti ti-chart-line text-muted"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold text-secondary">Net Commission</h6>
                                </div>
                                <div class="editable-field form-control">
                                    <span class="field-value-content">
                                        @if(is_array($commission_payable->commission_types) && isset($commission_payable->commission_types['Net']))
                                        {{ $commission_payable->commission_types['Net'] }}
                                        @elseif(is_string($commission_payable->commission_types))
                                        {{ $commission_payable->commission_types }}
                                        @else
                                        Not specified
                                        @endif
                                    </span>
                                    <span class="edit-icon ms-2">
                                        <i class="ti ti-edit text-muted"></i>
                                    </span>
                                    <span class="save-indicator ms-2"></span>
                                </div>
                                <input type="text" class="form-control edit-field d-none mt-2"
                                       name="commission_data[Net]"
                                       value="@if(is_array($commission_payable->commission_types) && isset($commission_payable->commission_types['Net'])){{ $commission_payable->commission_types['Net'] }}@elseif(is_string($commission_payable->commission_types)){{ $commission_payable->commission_types }}@endif"
                                       data-field-name="commission_data[Net]"
                                       data-commission-type="Net"
                                       data-original-value="@if(is_array($commission_payable->commission_types) && isset($commission_payable->commission_types['Net'])){{ $commission_payable->commission_types['Net'] }}@elseif(is_string($commission_payable->commission_types)){{ $commission_payable->commission_types }}@endif">
                            </div>
                        </div>

                        <!-- Gross Commission -->
                        <div class="col-md-4 mb-3" data-field="commission" data-commission-type="Gross">
                            <div class="border p-3 rounded h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="p-2 me-2">
                                        <i class="ti ti-currency-dollar text-muted"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold text-secondary">Gross Commission</h6>
                                </div>
                                <div class="editable-field form-control">
                                    <span class="field-value-content">
                                        @if(is_array($commission_payable->commission_types) && isset($commission_payable->commission_types['Gross']))
                                        {{ $commission_payable->commission_types['Gross'] }}
                                        @elseif(is_string($commission_payable->commission_types))
                                        {{ $commission_payable->commission_types }}
                                        @else
                                        Not specified
                                        @endif
                                    </span>
                                    <span class="edit-icon ms-2">
                                        <i class="ti ti-edit text-muted"></i>
                                    </span>
                                    <span class="save-indicator ms-2"></span>
                                </div>
                                <input type="text" class="form-control edit-field d-none mt-2"
                                       name="commission_data[Gross]"
                                       value="@if(is_array($commission_payable->commission_types) && isset($commission_payable->commission_types['Gross'])){{ $commission_payable->commission_types['Gross'] }}@elseif(is_string($commission_payable->commission_types)){{ $commission_payable->commission_types }}@endif"
                                       data-field-name="commission_data[Gross]"
                                       data-commission-type="Gross"
                                       data-original-value="@if(is_array($commission_payable->commission_types) && isset($commission_payable->commission_types['Gross'])){{ $commission_payable->commission_types['Gross'] }}@elseif(is_string($commission_payable->commission_types)){{ $commission_payable->commission_types }}@endif">
                            </div>
                        </div>

                        <!-- Standard Commission -->
                        <div class="col-md-4 mb-3" data-field="commission" data-commission-type="Standard">
                            <div class="border p-3 rounded h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="p-2 me-2">
                                        <i class="ti ti-star text-muted"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold text-secondary">Standard Commission</h6>
                                </div>
                                <div class="editable-field form-control">
                                    <span class="field-value-content">
                                        @if(is_array($commission_payable->commission_types) && isset($commission_payable->commission_types['Standard']))
                                        {{ $commission_payable->commission_types['Standard'] }}
                                        @elseif(is_string($commission_payable->commission_types))
                                        {{ $commission_payable->commission_types }}
                                        @else
                                        Not specified
                                        @endif
                                    </span>
                                    <span class="edit-icon ms-2">
                                        <i class="ti ti-edit text-muted"></i>
                                    </span>
                                    <span class="save-indicator ms-2"></span>
                                </div>
                                <input type="text" class="form-control edit-field d-none mt-2"
                                       name="commission_data[Standard]"
                                       value="@if(is_array($commission_payable->commission_types) && isset($commission_payable->commission_types['Standard'])){{ $commission_payable->commission_types['Standard'] }}@elseif(is_string($commission_payable->commission_types)){{ $commission_payable->commission_types }}@endif"
                                       data-field-name="commission_data[Standard]"
                                       data-commission-type="Standard"
                                       data-original-value="@if(is_array($commission_payable->commission_types) && isset($commission_payable->commission_types['Standard'])){{ $commission_payable->commission_types['Standard'] }}@elseif(is_string($commission_payable->commission_types)){{ $commission_payable->commission_types }}@endif">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    /* Enhanced editable fields */
    .editable-field {
        cursor: pointer;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
        transition: all 0.2s;
        min-height: 38px;
        display: flex;
        align-items: center;
    }
    .editable-field:hover {
        background-color: rgba(13, 110, 253, 0.05);
        border-color: #86b7fe;
    }
    .edit-icon {
        opacity: 0;
        transition: opacity 0.2s;
        margin-left: auto;
    }
    .editable-field:hover .edit-icon {
        opacity: 1;
    }

    .save-indicator {
        font-size: 0.75rem;
        display: none;
    }

    .save-indicator .spinner-border {
        width: 0.75rem;
        height: 0.75rem;
        border-width: 0.15em;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1050;
        display: none;
    }

    .toast {
        min-width: 300px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border: none;
    }
    .toast-success {
        border-left: 4px solid #198754;
    }
    .toast-error {
        border-left: 4px solid #dc3545;
    }

    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.5em;
        margin-left: 0;
    }

    .input-group-text {
        min-width: 40px;
        justify-content: center;
        background-color: transparent;
    }

    .bg-light-subtle {
        background-color: #f8f9fa;
    }

    .border {
        border: 1px solid #dee2e6;
    }
</style>

<script>
    // JS logic to handle toggles and AJAX saving
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = '{{ csrf_token() }}';

        function handleCommissionToggle(toggleId, valueSectionId, valueFieldName) {
            const toggle = document.getElementById(toggleId);
            const valueSection = document.getElementById(valueSectionId);
            const hasFieldName = 'has_' + valueFieldName;

            toggle.addEventListener('change', function () {
                if (this.checked) {
                    valueSection.classList.remove('d-none');
                } else {
                    valueSection.classList.add('d-none');
                }

                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('_method', 'PUT');
                formData.append(hasFieldName, this.checked ? 1 : 0);

                if (!this.checked) {
                    formData.append(valueFieldName, '');
                } else {
                    const inputField = valueSection.querySelector('.edit-field');
                    if (inputField) {
                        formData.append(valueFieldName, inputField.value.trim());
                    }
                }

                $.ajax({
                    url: '{{ route("backend.commission.updateCommission", $commission_payable->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function () {
                        console.log('Toggle updated successfully');
                    },
                    error: function (xhr) {
                        console.error('Error updating toggle:', xhr.responseText);
                    }
                });
            });
        }

        handleCommissionToggle('bonusCommissionToggle', 'bonusCommissionValueSection', 'bonus_commission');
        handleCommissionToggle('incentiveCommissionToggle', 'incentiveCommissionValueSection', 'incentive_commission');
        handleCommissionToggle('progressiveCommissionToggle', 'progressiveCommissionValueSection', 'progressive_commission');

        // Editable field handling
        function initializeEditableFields() {
            document.querySelectorAll('.editable-field').forEach(field => {
                field.addEventListener('click', function () {
                    const parent = this.closest('[data-field]');
                    const editField = parent.querySelector('.edit-field');
                    const displayField = parent.querySelector('.editable-field');
                    if (editField) {
                        displayField.classList.add('d-none');
                        editField.classList.remove('d-none');
                        editField.focus();
                        const val = editField.value;
                        editField.value = '';
                        editField.value = val;
                        if (!editField.hasAttribute('data-original-value')) {
                            const contentElement = displayField.querySelector('.field-value-content');
                            editField.setAttribute('data-original-value', contentElement.textContent.trim());
                        }
                    }
                });
            });

            document.querySelectorAll('.edit-field').forEach(field => {
                field.addEventListener('blur', function () {
                    const fieldName = this.getAttribute('data-field-name');
                    const originalValue = this.getAttribute('data-original-value');
                    const newValue = this.value.trim();

                    if (newValue !== originalValue && newValue !== '') {
                        confirmEdit(fieldName, newValue, this);
                    } else {
                        resetToDisplayMode(this);
                    }
                });

                field.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.blur();
                    }
                });
            });
        }

        function confirmEdit(fieldName, value, element) {
            Swal.fire({
                title: 'Confirm Update',
                html: `Update <strong>${getFieldLabel(fieldName)}</strong> to <span class="text-primary">${value}</span>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    saveField(fieldName, value, element);
                } else {
                    resetToDisplayMode(element);
                }
            });
        }

        function getFieldLabel(fieldName) {
            const labels = {
                'university': 'University',
                'product': 'Product',
                'partner': 'Partner',
                'progressive_commission': 'Progressive Commission',
                'bonus_commission': 'Bonus Commission',
                'incentive_commission': 'incentive Commission'
            };
            return labels[fieldName] || fieldName;
        }

        function saveField(fieldName, value, element) {
            const parent = element.closest('[data-field]');
            const displayField = parent.querySelector('.editable-field');
            const displayContent = displayField.querySelector('.field-value-content');
            const saveIndicator = parent.querySelector('.save-indicator');

            saveIndicator.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Saving...';
            saveIndicator.style.display = 'inline';

            let formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'PUT');
            formData.append(fieldName, value);

            $.ajax({
                url: '{{ route("backend.commission.updateCommission", $commission_payable->id) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    displayContent.textContent = value;
                    element.setAttribute('data-original-value', value);
                    saveIndicator.innerHTML = '<i class="ti ti-check me-1 text-success"></i>Saved';
                    resetToDisplayMode(element);
                    showToast('Update successful', 'success');
                    setTimeout(() => saveIndicator.style.display = 'none', 2000);
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                    saveIndicator.innerHTML = '<i class="ti ti-x me-1 text-danger"></i>Error';
                    showToast('Update failed: ' + (xhr.responseJSON?.message || 'Server error'), 'error');
                    setTimeout(() => saveIndicator.style.display = 'none', 3000);
                }
            });
        }

        function resetToDisplayMode(element) {
            const parent = element.closest('[data-field]');
            const displayField = parent.querySelector('.editable-field');
            element.classList.add('d-none');
            displayField.classList.remove('d-none');
        }

        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) return;
            const toast = document.createElement('div');
            toast.className = `toast show align-items-center text-white bg-${type} border-0`;
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="ti ${type === 'success' ? 'ti-circle-check' : 'ti-exclamation-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>`;
            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            setTimeout(() => toast.remove(), 5000);
        }

        initializeEditableFields();
    });
</script>

@endsection