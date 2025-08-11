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

    <form id="financeForm" action="{{ route('backend.comission.updateCommission', $comissions->id) }}" method="POST" class="needs-validation" novalidate>
      @csrf
      @method('PUT')
      
      <!-- University and Country section -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="university">University Name</label>
          <input type="text" class="form-control" id="university" name="university" 
                 placeholder="Enter university name" value="{{ old('university', $comissions->university) }}" required>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide university name.</div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="product">Product</label>
          <input type="text" class="form-control" id="product" name="product" 
                 placeholder="Enter product" value="{{ old('product', $comissions->product) }}" required>
          <div class="invalid-feedback">Please provide product.</div>
        </div>
      </div>

      <!-- Commission section - Dropdown with checkboxes and inline fields -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="commission">Commission</label>
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
                        <input class="form-check-input commission-checkbox" type="checkbox" id="netCommission" name="commission_data[Net][selected]" value="1" {{ isset($commissionsData['Net']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="netCommission">Net</label>
                      </div>
                    </div>
                    <div class="col-8 commission-value-field" id="netValueField" style="{{ isset($commissionsData['Net']) ? 'display:block' : 'display:none' }}">
                      <input type="text" class="form-control" id="netCommissionValue" name="commission_data[Net][value]" placeholder="Net commission value" value="{{ $commissionsData['Net']['value'] ?? '' }}">
                    </div>
                  </div>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <div class="dropdown-item px-0">
                  <div class="row align-items-center">
                    <div class="col-4">
                      <div class="form-check">
                        <input class="form-check-input commission-checkbox" type="checkbox" id="grossCommission" name="commission_data[Gross][selected]" value="1" {{ isset($commissionsData['Gross']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="grossCommission">Gross</label>
                      </div>
                    </div>
                    <div class="col-8 commission-value-field" id="grossValueField" style="{{ isset($commissionsData['Gross']) ? 'display:block' : 'display:none' }}">
                      <input type="text" class="form-control" id="grossCommissionValue" name="commission_data[Gross][value]" placeholder="Gross commission value" value="{{ $commissionsData['Gross']['value'] ?? '' }}">
                    </div>
                  </div>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <div class="dropdown-item px-0">
                  <div class="row align-items-center">
                    <div class="col-4">
                      <div class="form-check">
                        <input class="form-check-input commission-checkbox" type="checkbox" id="standardCommission" name="commission_data[Standard][selected]" value="1" {{ isset($commissionsData['Standard']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="standardCommission">Standard</label>
                      </div>
                    </div>
                    <div class="col-8 commission-value-field" id="standardValueField" style="{{ isset($commissionsData['Standard']) ? 'display:block' : 'display:none' }}">
                      <input type="text" class="form-control" id="standardCommissionValue" name="commission_data[Standard][value]" placeholder="Standard commission value" value="{{ $commissionsData['Standard']['value'] ?? '' }}">
                    </div>
                  </div>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <div class="dropdown-item px-0">
                  <div class="row align-items-center">
                    <div class="col-4">
                      <div class="form-check">
                        <input class="form-check-input commission-checkbox" type="checkbox" id="bonusCommission" name="commission_data[Bonus][selected]" value="1" {{ isset($commissionsData['Bonus']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="bonusCommission">Bonus</label>
                      </div>
                    </div>
                    <div class="col-8 commission-value-field" id="bonusValueField" style="{{ isset($commissionsData['Bonus']) ? 'display:block' : 'display:none' }}">
                      <input type="text" class="form-control" id="bonusCommissionValue" name="commission_data[Bonus][value]" placeholder="Bonus commission value" value="{{ $commissionsData['Bonus']['value'] ?? '' }}">
                    </div>
                  </div>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <div class="dropdown-item px-0">
                  <div class="row align-items-center">
                    <div class="col-4">
                      <div class="form-check">
                        <input class="form-check-input commission-checkbox" type="checkbox" id="intensiveCommission" name="commission_data[Intensive][selected]" value="1" {{ isset($commissionsData['Intensive']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="intensiveCommission">Intensive</label>
                      </div>
                    </div>
                    <div class="col-8 commission-value-field" id="intensiveValueField" style="{{ isset($commissionsData['Intensive']) ? 'display:block' : 'display:none' }}">
                      <input type="text" class="form-control" id="intensiveCommissionValue" name="commission_data[Intensive][value]" placeholder="Intensive commission value" value="{{ $commissionsData['Intensive']['value'] ?? '' }}">
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="invalid-feedback">Please select at least one commission type.</div>
        </div>
      </div>
     
      <!-- Submit button -->
      <div class="text-center mt-4">
        <button class="btn btn-primary" type="submit">
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
        // Check if at least one commission checkbox is selected
        const commissionCheckboxes = document.querySelectorAll('.commission-checkbox:checked');
        if (commissionCheckboxes.length === 0) {
          event.preventDefault();
          event.stopPropagation();
          document.querySelector('[aria-labelledby="dropdownMenuButton"]').classList.add('show');
          document.querySelector('.invalid-feedback').style.display = 'block';
        }
        
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
    
    // Initialize the dropdown text based on selected commissions
    updateDropdownText();
    
    commissionCheckboxes.forEach(function(checkbox) {
      checkbox.addEventListener('change', function(e) {
        e.stopPropagation(); // Prevent dropdown from closing when clicking checkbox
        const commissionType = this.id.replace('Commission', '');
        const valueField = document.getElementById(commissionType.toLowerCase() + 'ValueField');
        
        // Show/hide the value field right next to the checkbox
        if (this.checked) {
          valueField.style.display = 'block';
        } else {
          valueField.style.display = 'none';
        }
        
        // Update dropdown button text
        updateDropdownText();
      });
    });
    
    function updateDropdownText() {
      const selectedTypes = [];
      commissionCheckboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
          const type = checkbox.id.replace('Commission', '');
          selectedTypes.push(type);
        }
      });
      
      if (selectedTypes.length > 0) {
        dropdownButton.innerText = selectedTypes.join(', ');
      } else {
        dropdownButton.innerText = 'Select commission';
      }
    }
    
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
  });
</script>
@endsection