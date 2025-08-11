@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<div class="card">
  <div class="border-bottom title-part-padding">
    <h4 class="card-title mb-0">Commission Rate</h4>
  </div>

  <style>
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

    @if (session('error'))
    <div class="alert alert-danger">
      {{ session('error') }}
    </div>
    @endif

    <form id="financeForm" action="{{ route('backend.comission.payable.store') }}" method="POST" class="needs-validation" novalidate>
      @csrf

      <!-- University and Country section -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="university">University Name</label>
          <input type="text" class="form-control" id="university" name="university"
            placeholder="Enter university name" value="{{ old('university') }}" required>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide university name.</div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="product">Product</label>
          <input type="text" class="form-control" id="product" name="product"
            placeholder="Enter product" value="{{ old('product') }}" required>
          <div class="invalid-feedback">Please provide product.</div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="partner">Partner</label>
          <select class="form-control" id="partner" name="partner" required>
            @foreach($partners as $partner)
            <option>Select Partners</option>
            <option value="{{ $partner->id }}">{{ $partner->agency_name }}</option>
            @endforeach
          </select>
          <div class="invalid-feedback">Please select a partner.</div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Progressive Commission?</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="has_progressive_commission" id="progressiveCommissionYes" value="yes" {{ old('has_progressive_commission') == 'yes' ? 'checked' : '' }}>
            <label class="form-check-label" for="progressiveCommissionYes">
              Yes
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="has_progressive_commission" id="progressiveCommissionNo" value="no" {{ old('has_progressive_commission', 'no') == 'no' ? 'checked' : '' }}>
            <label class="form-check-label" for="progressiveCommissionNo">
              No
            </label>
          </div>
        </div>

        <div class="col-md-6 mb-3" id="progressiveCommissionFields" style="display: none;">
          <label class="form-label" for="progressive_commission">Progressive Commission</label>
          <input type="text" class="form-control" id="progressive_commission" name="progressive_commission"
            placeholder="Enter Progressive Commission" value="{{ old('progressive_commission') }}">
          <div class="invalid-feedback">Please provide progressive commission.</div>
        </div>
      </div>

      <!-- Bonus Commission section -->
      <div class="col-md-6 mb-3">
        <label class="form-label">Bonus Commission?</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="has_bonus_commission" id="bonusCommissionYes" value="yes" {{ old('has_bonus_commission') == 'yes' ? 'checked' : '' }}>
          <label class="form-check-label" for="bonusCommissionYes">
            Yes
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="has_bonus_commission" id="bonusCommissionNo" value="no" {{ old('has_bonus_commission', 'no') == 'no' ? 'checked' : '' }}>
          <label class="form-check-label" for="bonusCommissionNo">
            No
          </label>
        </div>
      </div>

      <div class="col-md-6 mb-3" id="bonusCommissionFields" style="display: none;">
        <label class="form-label" for="bonus_commission">Bonus Commission</label>
        <input type="text" class="form-control" id="bonus_commission" name="bonus_commission"
          placeholder="Enter Bonus Commission" value="{{ old('bonus_commission') }}">
        <div class="invalid-feedback">Please provide bonus commission.</div>
      </div>

      <!-- incentive Commission section -->
      <div class="col-md-6 mb-3">
        <label class="form-label">incentive Commission?</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="has_incentive_commission" id="incentiveCommissionYes" value="yes" {{ old('has_incentive_commission') == 'yes' ? 'checked' : '' }}>
          <label class="form-check-label" for="incentiveCommissionYes">
            Yes
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="has_incentive_commission" id="incentiveCommissionNo" value="no" {{ old('has_incentive_commission', 'no') == 'no' ? 'checked' : '' }}>
          <label class="form-check-label" for="incentiveCommissionNo">
            No
          </label>
        </div>
      </div>

      <div class="col-md-6 mb-3" id="incentiveCommissionFields" style="display: none;">
        <label class="form-label" for="incentive_commission">incentive Commission</label>
        <input type="text" class="form-control" id="incentive_commission" name="incentive_commission"
          placeholder="Enter incentive Commission" value="{{ old('incentive_commission') }}">
        <div class="invalid-feedback">Please provide incentive commission.</div>
      </div>

      <!-- Commission section - Dropdown with checkboxes and inline fields -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="commission">Commission(%)</label>
          <div class="dropdown">
            <button class="btn dropdown-toggle form-control text-start custom-outline" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              Select commission
            </button>
            <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton" style="padding: 15px;">
              <li>
                <div class="dropdown-item px-0">
                  <div class="row align-items-center">
                    <div class="col-4">
                      <div class="form-check">
                        <input class="form-check-input commission-checkbox" type="checkbox" id="netCommission" name="commissionTypes[]" value="Net" {{ in_array('Net', old('commissionTypes', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="netCommission">Net</label>
                      </div>
                    </div>
                    <div class="col-8 commission-value-field" id="netValueField" style="{{ in_array('Net', old('commissionTypes', [])) ? 'display: block;' : 'display: none;' }}">
                      <input type="text" class="form-control" id="netCommissionValue" name="commissionValues[Net]" placeholder="Net commission value" value="{{ old('commissionValues.Net') }}">
                    </div>
                  </div>
                </div>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <div class="dropdown-item px-0">
                  <div class="row align-items-center">
                    <div class="col-4">
                      <div class="form-check">
                        <input class="form-check-input commission-checkbox" type="checkbox" id="grossCommission" name="commissionTypes[]" value="Gross" {{ in_array('Gross', old('commissionTypes', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="grossCommission">Gross</label>
                      </div>
                    </div>
                    <div class="col-8 commission-value-field" id="grossValueField" style="{{ in_array('Gross', old('commissionTypes', [])) ? 'display: block;' : 'display: none;' }}">
                      <input type="text" class="form-control" id="grossCommissionValue" name="commissionValues[Gross]" placeholder="Gross commission value" value="{{ old('commissionValues.Gross') }}">
                    </div>
                  </div>
                </div>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <div class="dropdown-item px-0">
                  <div class="row align-items-center">
                    <div class="col-4">
                      <div class="form-check">
                        <input class="form-check-input commission-checkbox" type="checkbox" id="standardCommission" name="commissionTypes[]" value="Standard" {{ in_array('Standard', old('commissionTypes', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="standardCommission">Standard</label>
                      </div>
                    </div>
                    <div class="col-8 commission-value-field" id="standardValueField" style="{{ in_array('Standard', old('commissionTypes', [])) ? 'display: block;' : 'display: none;' }}">
                      <input type="text" class="form-control" id="standardCommissionValue" name="commissionValues[Standard]" placeholder="Standard commission value" value="{{ old('commissionValues.Standard') }}">
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <input type="hidden" id="selectedCommissionTypes" name="selectedCommissionTypes" value="{{ old('selectedCommissionTypes') }}">
          <div class="invalid-feedback">Please select at least one commission type.</div>
        </div>
      </div>

      <!-- Submit button -->
      <div class="text-center mt-4">
        <button class="btn btn-primary" type="submit" id="saveButton">
          <i class="ti ti-device-floppy me-1"></i> Save commission
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // Form validation
  (function() {
    'use strict';
    window.addEventListener('load', function() {
      var form = document.getElementById('financeForm');
      form.addEventListener('submit', function(event) {
        // Check if at least one commission type is selected
        const commissionCheckboxes = document.querySelectorAll('.commission-checkbox:checked');
        if (commissionCheckboxes.length === 0) {
          event.preventDefault();
          event.stopPropagation();
          alert('Please select at least one commission type.');
          return false;
        }

        // Check if commission values are provided for selected types
        let isValid = true;
        commissionCheckboxes.forEach(function(checkbox) {
          const type = checkbox.value;
          const valueField = document.querySelector(`input[name="commissionValues[${type}]"]`);
          if (!valueField.value.trim()) {
            isValid = false;
            valueField.classList.add('is-invalid');
          } else {
            valueField.classList.remove('is-invalid');
          }
        });

        if (!isValid) {
          event.preventDefault();
          event.stopPropagation();
          alert('Please provide values for all selected commission types.');
          return false;
        }

        // Validate other required fields
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }

        form.classList.add('was-validated');
      }, false);
    }, false);
  })();

  // Show/Hide commission value fields based on checkbox status
  document.addEventListener('DOMContentLoaded', function() {
    const commissionCheckboxes = document.querySelectorAll('.commission-checkbox');
    const dropdownButton = document.getElementById('dropdownMenuButton');
    const selectedCommissions = [];
    const hiddenField = document.getElementById('selectedCommissionTypes');

    // Initialize selected commissions based on checked boxes
    commissionCheckboxes.forEach(function(checkbox) {
      if (checkbox.checked) {
        selectedCommissions.push(checkbox.value);
        document.getElementById(checkbox.value.toLowerCase() + 'ValueField').style.display = 'block';
      }
    });

    // Update dropdown button text on page load
    if (selectedCommissions.length > 0) {
      dropdownButton.innerText = selectedCommissions.join(', ');
      hiddenField.value = selectedCommissions.join(',');
    }

    commissionCheckboxes.forEach(function(checkbox) {
      checkbox.addEventListener('change', function(e) {
        e.stopPropagation(); // Prevent dropdown from closing when clicking checkbox
        const commissionType = this.value;
        const valueField = document.getElementById(commissionType.toLowerCase() + 'ValueField');

        // Show/hide the value field right next to the checkbox
        if (this.checked) {
          valueField.style.display = 'block';

          if (!selectedCommissions.includes(commissionType)) {
            selectedCommissions.push(commissionType);
          }
        } else {
          valueField.style.display = 'none';

          const index = selectedCommissions.indexOf(commissionType);
          if (index > -1) {
            selectedCommissions.splice(index, 1);
          }
        }

        // Update dropdown button text and hidden field
        if (selectedCommissions.length > 0) {
          dropdownButton.innerText = selectedCommissions.join(', ');
          hiddenField.value = selectedCommissions.join(',');
        } else {
          dropdownButton.innerText = 'Select commission';
          hiddenField.value = '';
        }
      });
    });

    // Prevent dropdown from closing when clicking inside
    document.querySelector('.dropdown-menu').addEventListener('click', function(e) {
      e.stopPropagation();
    });

    // Prevent dropdown from closing when clicking on input fields
    const commissionValueFields = document.querySelectorAll('.commission-value-field input');
    commissionValueFields.forEach(function(field) {
      field.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    });

    // Progressive Commission
    const progressiveYesRadio = document.getElementById('progressiveCommissionYes');
    const progressiveNoRadio = document.getElementById('progressiveCommissionNo');
    const progressiveFields = document.getElementById('progressiveCommissionFields');

    function toggleProgressiveFields() {
      progressiveFields.style.display = progressiveYesRadio.checked ? 'block' : 'none';
    }

    // Initial state
    toggleProgressiveFields();

    // Event listeners
    progressiveYesRadio.addEventListener('change', toggleProgressiveFields);
    progressiveNoRadio.addEventListener('change', toggleProgressiveFields);

    // Bonus Commission
    const bonusYesRadio = document.getElementById('bonusCommissionYes');
    const bonusNoRadio = document.getElementById('bonusCommissionNo');
    const bonusFields = document.getElementById('bonusCommissionFields');

    function toggleBonusFields() {
      bonusFields.style.display = bonusYesRadio.checked ? 'block' : 'none';
    }

    // Initial state
    toggleBonusFields();

    // Event listeners
    bonusYesRadio.addEventListener('change', toggleBonusFields);
    bonusNoRadio.addEventListener('change', toggleBonusFields);

    // incentive Commission
    const incentiveYesRadio = document.getElementById('incentiveCommissionYes');
    const incentiveNoRadio = document.getElementById('incentiveCommissionNo');
    const incentiveFields = document.getElementById('incentiveCommissionFields');

    function toggleincentiveFields() {
      incentiveFields.style.display = incentiveYesRadio.checked ? 'block' : 'none';
    }

    // Initial state
    toggleincentiveFields();

    // Event listeners
    incentiveYesRadio.addEventListener('change', toggleincentiveFields);
    incentiveNoRadio.addEventListener('change', toggleincentiveFields);
  });
</script>
@endsection