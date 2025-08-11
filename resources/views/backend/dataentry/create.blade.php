@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<div class="col-lg-12 d-flex align-items-stretch">
  <div class="card w-100">
    <div class="card-body pb-0">
      <h4 class="card-title">University Data Entry Form</h4>
      <p class="card-subtitle mb-3">Fill out the form to add new university data.</p>
    </div>
    
    <form id="universityForm" action="{{ route('backend.dataentry.store') }}" method="POST" class="needs-validation" novalidate onsubmit="setDefaultValues()">
      @csrf
      
      <!-- University and Location Section -->
      <div class="card-body border-top">
        <h5 class="mb-3">University Information</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newUniversity" class="form-label">University Name</label>
              <input type="text" class="form-control" id="newUniversity" name="newUniversity" placeholder="Enter university name" required>
              <div class="invalid-feedback">Please provide university name.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newLocation" class="form-label">Location</label>
              <input type="text" class="form-control" id="newLocation" name="newLocation" placeholder="Enter location" required>
              <div class="invalid-feedback">Please provide location.</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Course and Intake Section -->
      <div class="card-body border-top">
        <h5 class="mb-3">Course Details</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newCourse" class="form-label">Course Name</label>
              <input type="text" class="form-control" id="newCourse" name="newCourse" placeholder="Enter course name" required>
              <div class="invalid-feedback">Please provide course name.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newIntake" class="form-label">Intake Months</label>
              <div class="search-container">
                <input type="text" class="form-control" id="newIntake" name="newIntake" placeholder="Search or select intake months" list="intakeList" required>
                <datalist id="intakeList">
                  @php
                    $months = ["January", "February", "March", "April", "May", "June", 
                              "July", "August", "September", "October", "November", "December"];
                  @endphp
                  @foreach($months as $month)
                    <option value="{{ $month }}">{{ $month }}</option>
                  @endforeach
                </datalist>
              </div>
              <div id="storedValues" class="stored-values mt-2"></div>
              <div class="invalid-feedback">Please provide intake period.</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Scholarship and Tuition Section -->
      <div class="card-body border-top">
        <h5 class="mb-3">Financial Information</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newScholarship" class="form-label">Scholarship Details</label>
              <input type="text" class="form-control" id="newScholarship" name="newScholarship" placeholder="Enter scholarship details">
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newAmount" class="form-label">Tuition Fees</label>
              <input type="text" class="form-control" id="newAmount" name="newAmount" placeholder="Enter tuition amount">
            </div>
          </div>
        </div>
      </div>
      
      <!-- Undergraduate Requirements Section -->
      <div class="card-body border-top">
        <h5 class="mb-3">Undergraduate Requirements</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newIelts" class="form-label">IELTS Requirement</label>
              <input type="text" class="form-control" id="newIelts" name="newIelts" placeholder="Enter IELTS score">
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newpte" class="form-label">PTE Requirement</label>
              <input type="text" class="form-control" id="newpte" name="newpte" placeholder="Enter PTE score">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newug" class="form-label">Gap Accepted</label>
              <input type="text" class="form-control" id="newug" name="newug" placeholder="Enter gap years accepted">
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newgpaug" class="form-label">GPA/Percentage</label>
              <input type="text" class="form-control" id="newgpaug" name="newgpaug" placeholder="Enter GPA requirement">
            </div>
          </div>
        </div>
      </div>
      
      <!-- Postgraduate Requirements Section -->
      <div class="card-body border-top">
        <h5 class="mb-3">Postgraduate Requirements</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newPgIelts" class="form-label">IELTS Requirement</label>
              <input type="text" class="form-control" id="newPgIelts" name="newPgIelts" placeholder="Enter IELTS score">
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newPgPte" class="form-label">PTE Requirement</label>
              <input type="text" class="form-control" id="newPgPte" name="newPgPte" placeholder="Enter PTE score">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newpg" class="form-label">Gap Accepted</label>
              <input type="text" class="form-control" id="newpg" name="newpg" placeholder="Enter gap years accepted">
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newgpapg" class="form-label">GPA/Percentage</label>
              <input type="text" class="form-control" id="newgpapg" name="newgpapg" placeholder="Enter GPA requirement">
            </div>
          </div>
        </div>
      </div>
      
      <!-- Additional Information Section -->
      <div class="card-body border-top">
        <h5 class="mb-3">Additional Information</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="newtest" class="form-label">English Waiver Requirement</label>
              <input type="text" class="form-control" id="newtest" name="newtest" placeholder="Enter English waiver details">
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="country" class="form-label">Country</label>
              <select class="form-select" id="country" name="country" required>
                <option value="" disabled selected>Select Country</option>
                <option value="USA">USA</option>
                <option value="United Kingdom">United Kingdom</option>
                <option value="Australia">Australia</option>
                <option value="Canada">Canada</option>
                <option value="New Zealand">New Zealand</option>
              </select>
              <div class="invalid-feedback">Please select country.</div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="requireddocuments" class="form-label">Required Documents</label>
              <textarea class="form-control" id="requireddocuments" name="requireddocuments" placeholder="List required documents" rows="3" required></textarea>
              <div class="invalid-feedback">Please list required documents.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="level" class="form-label">Level</label>
              <select class="form-select" id="level" name="level" required>
                <option value="" disabled selected>Select Level</option>
                <option value="undergraduate">Undergraduate</option>
                <option value="postgraduate">Postgraduate</option>
                <option value="both">Both</option>
              </select>
              <div class="invalid-feedback">Please select level.</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Form Actions -->
      <div class="p-3 border-top">
        <div class="text-end">
          <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Save University Data
          </button>
          <button type="reset" class="btn bg-danger-subtle text-danger ms-6 px-4">
            Cancel
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
  .search-container {
    position: relative;
  }
  
  .stored-values {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
  }
  
  .stored-value {
    background-color: #e9ecef;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
  }
</style>

<script>
  // Form validation
  (function() {
    'use strict';
    window.addEventListener('load', function() {
      var form = document.getElementById('universityForm');
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    }, false);
  })();

  // Set default values for empty fields
  function setDefaultValues() {
    const fields = [
      'newUniversity', 'newLocation', 'newCourse', 'newIntake',
      'newScholarship', 'newAmount', 'newIelts', 'newpte',
      'newPgIelts', 'newPgPte', 'newug', 'newpg', 
      'newgpaug', 'newgpapg', 'newtest', 'country'
    ];

    fields.forEach(function(id) {
      var input = document.getElementById(id);
      if (input && input.value.trim() === '') {
        input.value = 'N/A';
      }
    });
  }

  // Intake month selection functionality
  const months = [
    'January', 'February', 'March', 'April',
    'May', 'June', 'July', 'August',
    'September', 'October', 'November', 'December'
  ];
  
  const intakeInput = document.getElementById('newIntake');
  const storedValuesContainer = document.getElementById('storedValues');
  const storedValues = new Set();

  intakeInput.addEventListener('input', (e) => {
    const value = e.target.value;
    if (months.includes(value)) {
      storedValues.add(value);
      updateStoredValues();
      intakeInput.value = '';
    }
  });

  function updateStoredValues() {
    storedValuesContainer.innerHTML = '';
    storedValues.forEach(value => {
      const span = document.createElement('span');
      span.className = 'stored-value';
      span.textContent = value;
      
      const removeBtn = document.createElement('span');
      removeBtn.innerHTML = ' &times;';
      removeBtn.style.cursor = 'pointer';
      removeBtn.onclick = () => {
        storedValues.delete(value);
        updateStoredValues();
      };
      
      span.appendChild(removeBtn);
      storedValuesContainer.appendChild(span);
    });

    // Update hidden field with selected values
    intakeInput.value = Array.from(storedValues).join(', ');
  }
</script>
@endsection