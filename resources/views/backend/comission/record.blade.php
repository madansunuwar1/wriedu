@extends('layouts.admin')
@include('backend.script.session')
@include('backend.script.alert')
@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"> 

<div class="card">
  <div class="border-bottom title-part-padding">
    <h4 class="card-title mb-0">Commission Rate</h4>
  </div>

  <style>
    /* Custom Outline Styling */
    .custom-outline {
      background-color: transparent;
      border: 0.5px solid #ccc;
      color: inherit;
    }

    .custom-outline:hover {
      background-color: transparent;
      border-color: #aaa;
      color: inherit;
    }

    .custom-outline:focus {
      box-shadow: none;
      outline: none;
    }

    /* Custom Alert Styling */
    .custom-alert {
      z-index: 1050;
      left: 50%;
      transform: translateX(-50%);
      max-width: 500px;
    }

    /* Consistent Edit Icon Styling */
    .edit-icon {
      cursor: pointer;
      color: #6c757d;
      transition: color 0.3s ease;
      margin-left: 10px;
    }

    .edit-icon:hover {
      color: #007bff;
    }

    /* Comment Box Styling */
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

    .swal2-popup {
      font-size: 1.6rem !important;
    }

    .swal2-title {
      font-size: 2rem !important;
    }

    .swal2-confirm {
      background-color: #4f46e5 !important;
    }

    /* Display fields styling */
    .display-field {
      padding: 8px 12px;
      background-color: #f8f9fa;
      border-radius: 4px;
      border: 1px solid #dee2e6;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .display-field-label {
      font-weight: 500;
      margin-right: 10px;
      color: #495057;
    }

    .display-field-value {
      flex-grow: 1;
    }

    .edit-field {
      display: none;
      margin-bottom: 10px;
    }

    .commission-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 5px 0;
    }

    .commission-display {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 4px;
      padding: 8px 12px;
      margin-bottom: 10px;
    }

    /* Save indicator styling */
    .save-indicator {
      display: none;
      color: #28a745;
      font-size: 0.85rem;
      margin-left: 10px;
      animation: fadeOut 2s forwards;
      animation-delay: 1s;
    }

    @keyframes fadeOut {
      from { opacity: 1; }
      to { opacity: 0; }
    }

    /* Loading spinner */
    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
      border-width: 0.2em;
    }

    /* Toast notification */
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1051;
    }

    .toast {
      min-width: 200px;
      background-color: white;
      border-left: 4px solid #28a745;
      box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
    }

    .toast-success {
      border-left-color: #28a745;
    }

    .toast-error {
      border-left-color: #dc3545;
    }

    /* Clean Icon Container */
    .icon-container {
      width: 35px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background-color: transparent;
      color: #6c757d;
    }

    .icon-container i {
      font-size: 1.2rem;
    }

    .icon-container:hover {
      color: #007bff;
    }
  </style>

  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    <!-- Toast notification container -->
    <div class="toast-container"></div>

    <div id="financeForm" class="needs-validation" novalidate>
      <meta name="csrf-token" content="{{ csrf_token() }}">

      <h5 class="mb-4">Commission Information</h5>

      <!-- University -->
      <div class="d-flex align-items-center mb-3" data-field="university">
        <div class="icon-container me-3">
          <i class="far fa-building"></i>
        </div>
        <div>
          <h6 class="mb-1">University</h6>
          <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
            <span class="field-value-content">{{ $comissions->university }}</span>
            <span class="edit-icon ms-2">
              <i class="far fa-edit" onclick="makeEditable(this)"></i>
            </span>
            <span class="save-indicator"></span>
          </span>
          <input type="text" name="university" class="form-control edit-field" value="{{ $comissions->university }}" style="display: none;" data-field-name="university" data-original-value="{{ $comissions->university }}">
        </div>
      </div>

      <!-- Product -->
      <div class="d-flex align-items-center mb-3" data-field="product">
        <div class="icon-container me-3">
          <i class="far fa-globe"></i>
        </div>
        <div>
          <h6 class="mb-1">Product</h6>
          <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
            <span class="field-value-content">{{ $comissions->product }}</span>
            <span class="edit-icon ms-2">
              <i class="far fa-edit" onclick="makeEditable(this)"></i>
            </span>
            <span class="save-indicator"></span>
          </span>
          <input type="text" name="product" class="form-control edit-field" value="{{ $comissions->product }}" style="display: none;" data-field-name="product" data-original-value="{{ $comissions->product }}">
        </div>
      </div>

      <!-- Course -->
      <div class="d-flex align-items-center mb-3" data-field="course">
        <div class="icon-container me-3">
          <i class="far fa-book"></i>
        </div>
        <div>
          <h6 class="mb-1">Course</h6>
          <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
            <span class="field-value-content">{{ $comissions->course }}</span>
            <span class="edit-icon ms-2">
              <i class="far fa-edit" onclick="makeEditable(this)"></i>
            </span>
            <span class="save-indicator"></span>
          </span>
          <input type="text" name="course" class="form-control edit-field" value="{{ $comissions->course }}" style="display: none;" data-field-name="course" data-original-value="{{ $comissions->course }}">
        </div>
      </div>

      <!-- Commission Section -->
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">Commission Types</h5>
        </div>
        <div class="card-body">
          <!-- Net -->
          <div class="commission-section mb-3" data-field="commission" data-commission-type="Net">
            <div class="d-flex align-items-center">
              <div class="icon-container me-3">
                <i class="far fa-chart-line text-muted"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">Net</h6>
                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                  <span class="field-value-content">{{ $comissions->commission_types['Net'] ?? 'Not specified' }}</span>
                  <span class="edit-icon ms-2">
                    <i class="far fa-edit" onclick="makeEditable(this)"></i>
                  </span>
                  <span class="save-indicator"></span>
                </span>
                <input type="text" class="form-control edit-field" name="commission_data[Net]" value="{{ $comissions->commission_types['Net'] ?? '' }}" style="display: none;" data-field-name="commission_data[Net]" data-commission-type="Net" data-original-value="{{ $comissions->commission_types['Net'] ?? '' }}">
              </div>
            </div>
          </div>

          <!-- Gross -->
          <div class="commission-section mb-3" data-field="commission" data-commission-type="Gross">
            <div class="d-flex align-items-center">
              <div class="icon-container me-3">
                <i class="far fa-money-bill-alt text-muted"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">Gross</h6>
                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                  <span class="field-value-content">{{ $comissions->commission_types['Gross'] ?? 'Not specified' }}</span>
                  <span class="edit-icon ms-2">
                    <i class="far fa-edit" onclick="makeEditable(this)"></i>
                  </span>
                  <span class="save-indicator"></span>
                </span>
                <input type="text" class="form-control edit-field" name="commission_data[Gross]" value="{{ $comissions->commission_types['Gross'] ?? '' }}" style="display: none;" data-field-name="commission_data[Gross]" data-commission-type="Gross" data-original-value="{{ $comissions->commission_types['Gross'] ?? '' }}">
              </div>
            </div>
          </div>

          <!-- Standard -->
          <div class="commission-section mb-3" data-field="commission" data-commission-type="Standard">
            <div class="d-flex align-items-center">
              <div class="icon-container me-3">
                <i class="far fa-star text-muted"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">Standard</h6>
                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                  <span class="field-value-content">{{ $comissions->commission_types['Standard'] ?? 'Not specified' }}</span>
                  <span class="edit-icon ms-2">
                    <i class="far fa-edit" onclick="makeEditable(this)"></i>
                  </span>
                  <span class="save-indicator"></span>
                </span>
                <input type="text" class="form-control edit-field" name="commission_data[Standard]" value="{{ $comissions->commission_types['Standard'] ?? '' }}" style="display: none;" data-field-name="commission_data[Standard]" data-commission-type="Standard" data-original-value="{{ $comissions->commission_types['Standard'] ?? '' }}">
              </div>
            </div>
          </div>

          <!-- Bonus -->
          <div class="commission-section mb-3" data-field="commission" data-commission-type="Bonus">
            <div class="d-flex align-items-center">
              <div class="icon-container me-3">
                <i class="far fa-gift text-muted"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">Bonus</h6>
                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                  <span class="field-value-content">{{ $comissions->commission_types['Bonus'] ?? 'Not specified' }}</span>
                  <span class="edit-icon ms-2">
                    <i class="far fa-edit" onclick="makeEditable(this)"></i>
                  </span>
                  <span class="save-indicator"></span>
                </span>
                <input type="text" class="form-control edit-field" name="commission_data[Bonus]" value="{{ $comissions->commission_types['Bonus'] ?? '' }}" style="display: none;" data-field-name="commission_data[Bonus]" data-commission-type="Bonus" data-original-value="{{ $comissions->commission_types['Bonus'] ?? '' }}">
              </div>
            </div>
          </div>

          <!-- Intensive -->
          <div class="commission-section mb-3" data-field="commission" data-commission-type="Intensive">
            <div class="d-flex align-items-center">
              <div class="icon-container me-3">
                <i class="far fa-bolt text-muted"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">Intensive</h6>
                <span class="field-value group relative cursor-pointer hover:bg-green-100 p-1 inline-flex items-center">
                  <span class="field-value-content">{{ $comissions->commission_types['Intensive'] ?? 'Not specified' }}</span>
                  <span class="edit-icon ms-2">
                    <i class="far fa-edit" onclick="makeEditable(this)"></i>
                  </span>
                  <span class="save-indicator"></span>
                </span>
                <input type="text" class="form-control edit-field" name="commission_data[Intensive]" value="{{ $comissions->commission_types['Intensive'] ?? '' }}" style="display: none;" data-field-name="commission_data[Intensive]" data-commission-type="Intensive" data-original-value="{{ $comissions->commission_types['Intensive'] ?? '' }}">
              </div>
            </div>
          </div>

          <!-- Add New Commission Type -->
          <div class="text-center mt-3">
            <button type="button" class="btn btn-outline-primary" id="addCommissionBtn">
              <i class="far fa-plus"></i> Add Commission Type
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': csrfToken
    }
  });

  // Function to make fields editable
  window.makeEditable = function(icon) {
    const parent = icon.closest('[data-field]');
    const fieldValue = parent.querySelector('.field-value');
    const fieldValueContent = parent.querySelector('.field-value-content');
    const editField = parent.querySelector('.edit-field');

    if (fieldValueContent && editField) {
      // Hide the display value
      if (fieldValue) {
        fieldValue.style.display = 'none';
      } else {
        fieldValueContent.style.display = 'none';
      }

      // Show the edit field
      editField.style.display = 'block';
      editField.focus();

      // Position cursor at the end of text
      const val = editField.value;
      editField.value = '';
      editField.value = val;

      // Store the original value in case we need to revert
      if (!editField.hasAttribute('data-original-value')) {
        editField.setAttribute('data-original-value', fieldValueContent.textContent.trim());
      }
    }
  };

  // Add event listeners for text fields
  const textFields = document.querySelectorAll('.edit-field');
  textFields.forEach(function(field) {
    if (!field.hasAttribute('data-event-added')) {
      field.setAttribute('data-event-added', 'true');

      field.addEventListener('blur', function() {
        const fieldName = this.getAttribute('data-field-name');
        const originalValue = this.getAttribute('data-original-value');
        const newValue = this.value.trim();

        // Only save if value has changed
        if (newValue !== originalValue && newValue !== '') {
          saveField(fieldName, newValue, this);
        } else {
          // Reset to display mode
          resetToDisplayMode(this);
        }
      });

      // Handle Enter key press
      field.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          this.blur(); // Trigger blur event to save
        }
      });
    }
  });

  // Function to save field via AJAX
  function saveField(fieldName, value, element) {
    const parentElement = element.closest('[data-field]');
    const saveIndicator = parentElement.querySelector('.save-indicator');

    // Show saving indicator
    saveIndicator.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
    saveIndicator.style.display = 'inline';

    // Parse the fieldName to handle commission types correctly
    let formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('_method', 'PUT');

    // Check if this is a commission type field
    if (fieldName.includes('commission_data')) {
      // This is a commission type update
      const commissionType = element.getAttribute('data-commission-type');
      
      // We need to update the commission_types field properly
      formData.append('commission_type', commissionType);
      formData.append('commission_value', value);
    } else {
      // Regular field update
      formData.append(fieldName, value);
    }

    $.ajax({
      url: '{{ route("backend.comission.updateCommission", $comissions->id) }}',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        console.log('Save success:', response);

        // Update display text
        const displayElement = parentElement.querySelector('.field-value-content');
        displayElement.textContent = value;

        // Update original value attribute
        element.setAttribute('data-original-value', value);

        // Show success indicator
        saveIndicator.innerHTML = '<i class="far fa-check"></i> Saved';

        // Reset to display mode
        resetToDisplayMode(element);

        // Show toast notification
        showToast('Updated successfully', 'success');

        // Hide save indicator after some time
        setTimeout(function() {
          saveIndicator.style.display = 'none';
        }, 3000);
      },
      error: function(xhr, status, error) {
        console.error('Error saving field:', status, error);
        console.error('Server response:', xhr.responseText);

        // Show error indicator
        saveIndicator.innerHTML = '<i class="far fa-times"></i> Error';
        saveIndicator.style.color = '#dc3545';

        // Show toast notification
        showToast('Error updating field: ' + (xhr.responseJSON?.message || error), 'error');

        // Hide save indicator after some time
        setTimeout(function() {
          saveIndicator.style.display = 'none';
          saveIndicator.style.color = '#28a745'; // Reset color
        }, 3000);
      }
    });
  }

  // Function to reset field to display mode
  function resetToDisplayMode(element) {
    const parentElement = element.closest('[data-field]');
    const displayElement = parentElement.querySelector('.field-value-content');
    const fieldValue = parentElement.querySelector('.field-value');

    if (fieldValue) {
      fieldValue.style.display = 'flex';
    } else if (displayElement) {
      displayElement.style.display = 'inline';
    }

    element.style.display = 'none';
  }

  // Function to show toast notification
  function showToast(message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) return;

    const toast = document.createElement('div');
    toast.className = `toast fade show toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
      <div class="toast-header">
        <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        ${message}
      </div>
    `;

    toastContainer.appendChild(toast);

    // Remove toast after 3 seconds
    setTimeout(function() {
      toast.classList.remove('show');
      setTimeout(function() {
        if (toastContainer.contains(toast)) {
          toastContainer.removeChild(toast);
        }
      }, 300);
    }, 3000);
  }

  // Add Commission Type functionality
  document.getElementById('addCommissionBtn').addEventListener('click', function() {
    // Here you could implement modal for adding new commission types
    alert('This would open a modal to add a new commission type');
  });

  // Make functions available to window scope
  window.saveField = saveField;
  window.resetToDisplayMode = resetToDisplayMode;
  window.showToast = showToast;
});
</script>
@endsection