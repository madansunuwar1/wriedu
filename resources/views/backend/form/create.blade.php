@extends('layouts.admin')

@section('content')
<div class="col-lg-12 d-flex align-items-stretch">
  <div class="card w-100">
    <div class="card-body pb-0">
      <h4 class="card-title">Application Form</h4>
      <p class="card-subtitle mb-3">Fill out the form to create a new application.</p>
    </div>
    <form id="studentForm" action="{{ route('backend.applications.store') }}" method="POST" class="needs-validation" novalidate="">
      @csrf
      <input type="hidden" name="created_by" value="{{ Auth::id() }}">

      <div class="card-body border-top">
        <h5 class="mb-3">Personal Details</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
              <div class="invalid-feedback">Please provide your name.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
              <div class="invalid-feedback">Please provide a valid email.</div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
              <div class="invalid-feedback">Please provide a valid phone number.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body border-top">
        <h5 class="mb-3">Academic Details</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="lastqualification" class="form-label">Level</label>
              <select class="form-select" id="lastqualification" name="lastqualification" required>
                <option value="" disabled selected>Select Last Level</option>
                <option value="Intermediate/Diploma">Intermediate/Diploma</option>
                <option value="Bachelor">Bachelor</option>
                <option value="Masters">Masters</option>
              </select>
              <div class="invalid-feedback">Please select your education level.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="passed" class="form-label">Pass Year</label>
              <select class="form-select" id="passed" name="passed" required>
                <option value="" disabled selected>Select Passed Year</option>
                <option value="2015">2015</option>
                <option value="2016">2016</option>
                <option value="2017">2017</option>
                <option value="2018">2018</option>
                <option value="2019">2019</option>
                <option value="2020">2020</option>
                <option value="2021">2021</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
              </select>
              <div class="invalid-feedback">Please select your passing year.</div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="gpa" class="form-label">GPA / Percentage</label>
              <input type="text" class="form-control" id="gpa" name="gpa" placeholder="GPA / Percentage" required>
              <div class="invalid-feedback">Please provide your GPA or percentage.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="english" class="form-label">Product</label>
              <select class="form-select" id="english" name="english">
                <option value="" disabled selected>Select Product</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->product }}</option>
                @endforeach
              </select>
              <div class="invalid-feedback">Please select a product.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body border-top">
        <h5 class="mb-3">English Language Test</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="englishTest" class="form-label">English Test</label>
              <select class="form-select" id="englishTest" name="englishTest" required>
                <option value="" disabled selected>Select English Test</option>
                <option value="IELTS">IELTS</option>
                <option value="PTE">PTE</option>
                <option value="ELLT">ELLT</option>
                <option value="No Test">No Test</option>
                <option value="MOI">MOI</option>
              </select>
              <div class="invalid-feedback">Please select your English test.</div>
            </div>
          </div>
        </div>

        <div id="test-fields" class="row" style="display:none;">
          <div class="col-sm-12 col-md-4">
            <div class="mb-3">
              <label for="higher" class="form-label">Overall Higher</label>
              <input type="text" class="form-control" id="higher" name="higher" placeholder="Enter Overall Higher">
              <div class="invalid-feedback">Please provide overall higher score.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-4">
            <div class="mb-3">
              <label for="less" class="form-label">Not Less than</label>
              <input type="text" class="form-control" id="less" name="less" placeholder="Enter Not Less than">
              <div class="invalid-feedback">Please provide minimum score.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-4">
            <div class="mb-3">
              <label for="score" class="form-label">Overall Score</label>
              <input type="text" class="form-control" id="score" name="score" placeholder="Calculated score" readonly>
            </div>
          </div>
        </div>

        <div id="no-test-fields" class="row" style="display:none;">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="englishscore" class="form-label">Overall English Score</label>
              <input type="text" class="form-control" id="englishscore" name="englishscore" placeholder="Enter English Score">
              <div class="invalid-feedback">Please provide English score.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="englishtheory" class="form-label">English Theory</label>
              <input type="text" class="form-control" id="englishtheory" name="englishtheory" placeholder="Enter English Theory">
              <div class="invalid-feedback">Please provide English theory.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body border-top">
        <h5 class="mb-3">Study Destination</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="country" class="form-label">Country</label>
              <select class="form-select" id="country" name="country" required>
                <option value="" disabled selected>Select Country</option>
                <option value="USA">USA</option>
                <option value="UK">UK</option>
                <option value="Australia">Australia</option>
                <option value="Canada">Canada</option>
              </select>
              <div class="invalid-feedback">Please select a country.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="location" class="form-label">Location</label>
              <input type="text" class="form-control" id="location" name="location" placeholder="Enter location">
              <div class="invalid-feedback">Please provide a location.</div>
            </div>
          </div>
        </div>
      </div>

      @php
      $structuredData = [];
      $allUniversities = collect();
      $data_entries = $data_entries ?? collect();

      foreach ($data_entries as $entry) {
      $university = trim($entry->newUniversity ?? '');
      $course = trim($entry->newCourse ?? '');
      $intakeString = trim($entry->newIntake ?? '');

      if (empty($university) || empty($course) || empty($intakeString)) {
      continue;
      }

      $allUniversities->push($university);

      if (!isset($structuredData[$university])) {
      $structuredData[$university] = [];
      }

      if (!isset($structuredData[$university][$course])) {
      $structuredData[$university][$course] = [];
      }

      $intakes = array_filter(array_map('trim', explode(',', $intakeString)), 'strlen');
      $currentIntakes = $structuredData[$university][$course];
      $mergedIntakes = array_unique(array_merge($currentIntakes, $intakes));
      sort($mergedIntakes);
      $structuredData[$university][$course] = $mergedIntakes;
      }
      $uniqueUniversities = $allUniversities->filter()->unique()->sort()->values();
      @endphp

      <script>
        const universityCourseData = @json($structuredData);
        const allUniversities = @json($uniqueUniversities);
      </script>

      <div class="card-body border-top">
        <h5 class="mb-3">University Preferences</h5>

        <div id="form-rows">
          <div class="row mb-3" id="form-row-1">
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="university1" class="form-label">University</label>
                <select class="form-select university-select" id="university1" name="university[]" data-row-id="1" required>
                  <option value="" selected disabled>Select University</option>
                </select>
                <div class="invalid-feedback">Please select a university or N/A.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="course1" class="form-label">Course</label>
                <select class="form-select course-select" id="course1" name="course[]" data-row-id="1" required disabled>
                  <option value="" selected disabled>Select University First</option>
                </select>
                <div class="invalid-feedback">Please select a course or N/A.</div>
              </div>
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="mb-3">
                <label for="intake1" class="form-label">Intake</label>
                <select class="form-select intake-select" id="intake1" name="intake[]" data-row-id="1" required disabled>
                  <option value="" selected disabled>Select Course First</option>
                </select>
                <div class="invalid-feedback">Please select an intake or N/A.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <button type="button" id="addButton" class="btn btn-outline-primary btn-sm me-2">Add More</button>
            <button type="button" id="removeButton" class="btn btn-outline-danger btn-sm">Remove</button>
          </div>
        </div>
      </div>

      <div class="card-body border-top">
        <h5 class="mb-3">Application Status</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="document" class="form-label">Document Status</label>
              <select class="form-select" id="document" name="document" required onchange="handleDocumentStatusChange()">
                <option value="" disabled selected>Select Document Status</option>
                <option value="Partially Received">Partially Received</option>
                <option value="Fully Received">Fully Received</option>
                <option value="Initiated For Offer">Initiated For Offer</option>
              </select>
              <div class="invalid-feedback">Please select document status.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6" id="second-dropdown-container" style="display: none;">
            <div class="mb-3">
              <label for="additionalinfo" class="form-label">Initiated Offer Information</label>
              <select class="form-select" id="additionalinfo" name="additionalinfo">
                <option value="" disabled selected>Select Initiated</option>
                <option value="Not Initiated Offer">Not Initiated Offer</option>
                <option value="Initiated Offer">Initiated Offer</option>
              </select>
              <div class="invalid-feedback">Please select initiated offer information.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body border-top">
        <h5 class="mb-3">Referral Information</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="source" class="form-label">Source of Referral</label>
              <select class="form-select" id="source" name="source" required>
                <option value="" disabled selected>Select Source</option>
                <option value="facebook">Facebook</option>
                <option value="whatapps">WhatsApp</option>
                <option value="instgram">Instagram</option>
                <option value="partners">Partners</option>
                <option value="other">Other</option>
              </select>
              <div class="invalid-feedback">Please select referral source.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6" id="partnerField" style="display: none;">
            <div class="mb-3">
              <label for="partnerDetails" class="form-label">Partner Details</label>
              <input type="text" class="form-control" id="partnerDetails" name="partnerDetails" placeholder="Enter partner details" list="partnerDetailsList">
              <datalist id="partnerDetailsList">
                @isset($applications)
                @foreach ($applications as $application)
                @if(!empty($application->partnerDetails))
                <option value="{{ $application->partnerDetails }}">{{ $application->partnerDetails }}</option>
                @endif
                @endforeach
                @endisset
              </datalist>
              <div class="invalid-feedback">Please provide partner details.</div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6" id="otherField" style="display: none;">
            <div class="mb-3">
              <label for="otherDetails" class="form-label">Other Referral Details</label>
              <input type="text" class="form-control" id="otherDetails" name="otherDetails" placeholder="Enter other details" list="otherDetailsList">
              <datalist id="otherDetailsList">
                @isset($applications)
                @foreach ($applications as $application)
                @if(!empty($application->otherDetails))
                <option value="{{ $application->otherDetails }}">{{ $application->otherDetails }}</option>
                @endif
                @endforeach
                @endisset
              </datalist>
              <div class="invalid-feedback">Please provide referral details.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="p-3 border-top">
        <div class="text-end">
          <button type="submit" id="submitButton" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
            <span class="button-text">Submit Form</span>
          </button>
          <button type="reset" class="btn bg-danger-subtle text-danger ms-6 px-4">
            Cancel
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
 document.addEventListener('DOMContentLoaded', function() {

  const studentForm = document.getElementById('studentForm');
  const submitButton = document.getElementById('submitButton');
  const spinner = submitButton ? submitButton.querySelector('.spinner-border') : null;
  const buttonText = submitButton ? submitButton.querySelector('.button-text') : null;
  const englishTestSelect = document.getElementById('englishTest');
  const testFields = document.getElementById('test-fields');
  const noTestFields = document.getElementById('no-test-fields');
  const higherInput = document.getElementById('higher');
  const lessInput = document.getElementById('less');
  const scoreField = document.getElementById('score');
  const englishScoreInput = document.getElementById('englishscore');
  const englishTheoryInput = document.getElementById('englishtheory');
  const formRowsContainer = document.getElementById('form-rows');
  const addButton = document.getElementById('addButton');
  const removeButton = document.getElementById('removeButton');
  const documentSelect = document.getElementById("document");
  const secondDropdownContainer = document.getElementById("second-dropdown-container");
  const additionalInfoSelect = document.getElementById("additionalinfo");
  const sourceDropdown = document.getElementById('source');
  const partnerField = document.getElementById('partnerField');
  const otherField = document.getElementById('otherField');
  const partnerDetailsInput = document.getElementById('partnerDetails');
  const otherDetailsInput = document.getElementById('otherDetails');

  let formCounter = 1;

  function populateDropdown(selectElement, optionsArray, placeholder) {
    selectElement.innerHTML = '';
    selectElement.disabled = false;

    const placeholderOption = document.createElement('option');
    placeholderOption.value = "";
    placeholderOption.textContent = placeholder;
    placeholderOption.disabled = true;
    placeholderOption.selected = true;
    selectElement.appendChild(placeholderOption);

    // We'll use the options from the database, which should already include N/A if needed
    optionsArray.forEach(optionValue => {
      const option = document.createElement('option');
      option.value = optionValue;
      option.textContent = optionValue;
      selectElement.appendChild(option);
    });
    selectElement.classList.remove('is-valid', 'is-invalid');
  }

  function resetDropdown(selectElement, placeholder) {
    selectElement.innerHTML = '';
    const placeholderOption = document.createElement('option');
    placeholderOption.value = "";
    placeholderOption.textContent = placeholder;
    placeholderOption.disabled = true;
    placeholderOption.selected = true;
    selectElement.appendChild(placeholderOption);
    
    selectElement.disabled = true;
    selectElement.classList.remove('is-valid', 'is-invalid');
  }

  function handleUniversityChange(universitySelect) {
    const selectedUniversity = universitySelect.value;
    const rowId = universitySelect.dataset.rowId;
    const courseSelect = formRowsContainer.querySelector(`#course${rowId}`);
    const intakeSelect = formRowsContainer.querySelector(`#intake${rowId}`);

    // Reset the intake dropdown
    resetDropdown(intakeSelect, 'Select Course First');
    
    if (selectedUniversity === "N/A") {
      // If university is N/A, enable course dropdown but only with N/A option
      courseSelect.disabled = false;
      populateDropdown(courseSelect, ["N/A"], 'Select Course');
      courseSelect.value = "N/A";
      
      // Also enable intake with N/A only
      intakeSelect.disabled = false;
      populateDropdown(intakeSelect, ["N/A"], 'Select Intake');
      intakeSelect.value = "N/A";
      
    } else if (selectedUniversity && universityCourseData[selectedUniversity]) {
      // Regular case - university is selected and has courses
      const courses = Object.keys(universityCourseData[selectedUniversity]);
      populateDropdown(courseSelect, courses, 'Select Course');
    } else {
      // Reset course dropdown if university is not valid
      resetDropdown(courseSelect, 'Select University First');
    }
  }

  function handleCourseChange(courseSelect) {
    const selectedCourse = courseSelect.value;
    const rowId = courseSelect.dataset.rowId;
    const universitySelect = formRowsContainer.querySelector(`#university${rowId}`);
    const intakeSelect = formRowsContainer.querySelector(`#intake${rowId}`);
    const selectedUniversity = universitySelect.value;

    if (selectedCourse === "N/A") {
      // If course is N/A, automatically set intake to N/A
      intakeSelect.disabled = false;
      populateDropdown(intakeSelect, ["N/A"], 'Select Intake');
      intakeSelect.value = "N/A";
      
    } else if (
      selectedUniversity && 
      selectedCourse && 
      universityCourseData[selectedUniversity] &&
      universityCourseData[selectedUniversity][selectedCourse]
    ) {
      // Regular case - populate intakes for selected course
      const intakes = universityCourseData[selectedUniversity][selectedCourse];
      populateDropdown(intakeSelect, intakes, 'Select Intake');
    } else {
      // Reset intake dropdown
      resetDropdown(intakeSelect, 'Select Course First');
    }
  }

  function initializeFirstRow() {
    formCounter = formRowsContainer.querySelectorAll('.row[id^="form-row-"]').length || 1;
    const firstUniversitySelect = formRowsContainer.querySelector('#university1');
    
    // Make sure we have the N/A option in the allUniversities array
    if (typeof allUniversities !== 'undefined' && !allUniversities.includes("N/A")) {
      allUniversities.push("N/A");
    }
    
    if (firstUniversitySelect && typeof allUniversities !== 'undefined') {
      populateDropdown(firstUniversitySelect, allUniversities, 'Select University');
    }
    
    const firstCourseSelect = formRowsContainer.querySelector('#course1');
    const firstIntakeSelect = formRowsContainer.querySelector('#intake1');
    if (firstCourseSelect) resetDropdown(firstCourseSelect, 'Select University First');
    if (firstIntakeSelect) resetDropdown(firstIntakeSelect, 'Select Course First');
  }

  function addFields() {
    formCounter++;
    const newRow = document.createElement('div');
    newRow.classList.add('row', 'mb-3');
    newRow.id = `form-row-${formCounter}`;

    newRow.innerHTML = `
              <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                  <label for="university${formCounter}" class="form-label">University</label>
                  <select class="form-select university-select" id="university${formCounter}" name="university[]" data-row-id="${formCounter}" required>
                    <option value="" selected disabled>Select University</option>
                  </select>
                  <div class="invalid-feedback">Please select a university or N/A.</div>
                </div>
              </div>
              <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                  <label for="course${formCounter}" class="form-label">Course</label>
                  <select class="form-select course-select" id="course${formCounter}" name="course[]" data-row-id="${formCounter}" required disabled>
                    <option value="" selected disabled>Select University First</option>
                  </select>
                  <div class="invalid-feedback">Please select a course or N/A.</div>
                </div>
              </div>
              <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                  <label for="intake${formCounter}" class="form-label">Intake</label>
                  <select class="form-select intake-select" id="intake${formCounter}" name="intake[]" data-row-id="${formCounter}" required disabled>
                    <option value="" selected disabled>Select Course First</option>
                  </select>
                  <div class="invalid-feedback">Please select an intake or N/A.</div>
                </div>
              </div>
          `;
    formRowsContainer.appendChild(newRow);

    const newUniversitySelect = newRow.querySelector(`#university${formCounter}`);
    if (newUniversitySelect && typeof allUniversities !== 'undefined') {
      populateDropdown(newUniversitySelect, allUniversities, 'Select University');
    }
  }

  function removeFields() {
    if (formCounter > 1) {
      const lastFormRow = document.getElementById(`form-row-${formCounter}`);
      if (lastFormRow) {
        lastFormRow.remove();
        formCounter--;
      }
    }
  }

  // Event delegation for all dropdown changes
  if (formRowsContainer) {
    formRowsContainer.addEventListener('change', function(event) {
      const target = event.target;
      if (target.matches('.university-select')) {
        handleUniversityChange(target);
      } else if (target.matches('.course-select')) {
        handleCourseChange(target);
      }
    });
  }

  if (addButton) addButton.addEventListener('click', addFields);
  if (removeButton) removeButton.addEventListener('click', removeFields);

  // Make sure N/A is in all data structures if it doesn't exist
  if (typeof universityCourseData !== 'undefined') {
    // Add N/A as a university if not present
    if (!universityCourseData["N/A"]) {
      universityCourseData["N/A"] = { "N/A": ["N/A"] };
    }
    
    // Add N/A as a course for each university if not present
    for (const university in universityCourseData) {
      if (!universityCourseData[university]["N/A"]) {
        universityCourseData[university]["N/A"] = ["N/A"];
      }
    }
  }

  initializeFirstRow();

  if (studentForm && submitButton && spinner && buttonText) {
    studentForm.addEventListener('submit', function(event) {
      event.preventDefault();
      event.stopPropagation();
      studentForm.classList.add('was-validated');
      if (studentForm.checkValidity()) {
        submitButton.disabled = true;
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Submitting...';
        try {
          const fields = studentForm.querySelectorAll('input:not([type="hidden"]):not([type="submit"]):not([type="reset"]):not(:disabled):not([readonly]), select:not(:disabled), textarea:not(:disabled)');
          fields.forEach(function(field) {
            if (field.offsetParent !== null && !field.value.trim() && !field.required && field.value !== "N/A") {
              field.value = 'N/A';
            }
            if (field.tagName === 'SELECT' && field.disabled && !field.hasAttribute('required')) {}
          });
          studentForm.querySelectorAll('select:disabled[name]').forEach(select => {
            if (!select.hasAttribute('required') || select.value === 'N/A') {}
          });

        } catch (e) {
          console.error("Error during N/A logic:", e);
        }

        setTimeout(() => {
          studentForm.submit();
        }, 50);
      } else {
        const firstInvalidField = studentForm.querySelector(':invalid');
        if (firstInvalidField) {
          firstInvalidField.focus();
          firstInvalidField.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
          });
        }
      }
    });
  } else {
    console.error("Form or Submit Button elements not found!");
  }

  if (englishTestSelect && testFields && noTestFields && higherInput && lessInput && scoreField && englishScoreInput && englishTheoryInput) {
    function resetEnglishFields() {
      higherInput.value = '';
      lessInput.value = '';
      scoreField.value = '';
      englishScoreInput.value = '';
      englishTheoryInput.value = '';
      higherInput.removeAttribute('required');
      lessInput.removeAttribute('required');
      englishScoreInput.removeAttribute('required');
      englishTheoryInput.removeAttribute('required');
      [higherInput, lessInput, englishScoreInput, englishTheoryInput].forEach(el => el.classList.remove('is-invalid', 'is-valid'));
    }
    englishTestSelect.addEventListener('change', function() {
      resetEnglishFields();
      testFields.style.display = 'none';
      noTestFields.style.display = 'none';
      switch (this.value) {
        case 'IELTS':
        case 'PTE':
        case 'ELLT':
          testFields.style.display = 'flex';
          higherInput.setAttribute('required', '');
          lessInput.setAttribute('required', '');
          break;
        case 'No Test':
          noTestFields.style.display = 'flex';
          englishScoreInput.setAttribute('required', '');
          englishTheoryInput.setAttribute('required', '');
          break;
        case 'MOI':
        default:
          break;
      }
    });
    [higherInput, lessInput].forEach(input => {
      input.addEventListener('input', function() {
        const higherValue = higherInput.value.trim();
        const lessValue = lessInput.value.trim();
        if (higherValue && lessValue && !isNaN(higherValue) && !isNaN(lessValue)) {
          scoreField.value = `${higherValue}/${lessValue}`;
        } else {
          scoreField.value = '';
        }
      });
    });
    englishTestSelect.dispatchEvent(new Event('change'));
  } else {
    console.error("One or more English test field elements not found!");
  }

  window.handleDocumentStatusChange = function() {
    if (!documentSelect || !secondDropdownContainer || !additionalInfoSelect) {
      console.error("Document status elements not found!");
      return;
    }
    const documentStatus = documentSelect.value;
    additionalInfoSelect.classList.remove('is-invalid', 'is-valid');
    if (documentStatus === "Fully Received") {
      secondDropdownContainer.style.display = "block";
      additionalInfoSelect.setAttribute('required', '');
      const naOption = additionalInfoSelect.querySelector('option[value="N/A"]');
      if (naOption) naOption.remove();
      if (!additionalInfoSelect.querySelector('option[value=""]')) {
        const placeholder = document.createElement('option');
        placeholder.value = "";
        placeholder.text = "Select Initiated";
        placeholder.disabled = true;
        placeholder.selected = true;
        additionalInfoSelect.insertBefore(placeholder, additionalInfoSelect.firstChild);
      }
      if (!additionalInfoSelect.querySelector('option[value="Not Initiated Offer"]')) {
        additionalInfoSelect.add(new Option("Not Initiated Offer", "Not Initiated Offer"));
      }
      if (!additionalInfoSelect.querySelector('option[value="Initiated Offer"]')) {
        additionalInfoSelect.add(new Option("Initiated Offer", "Initiated Offer"));
      }
      if (additionalInfoSelect.value === "N/A" || !additionalInfoSelect.value) {
        additionalInfoSelect.value = "";
      }
    } else {
      secondDropdownContainer.style.display = "none";
      additionalInfoSelect.removeAttribute('required');
      additionalInfoSelect.innerHTML = `<option value="N/A" selected>N/A</option>`;
    }
  }
  if (documentSelect) {
    handleDocumentStatusChange();
  }

  if (sourceDropdown && partnerField && otherField && partnerDetailsInput && otherDetailsInput) {
    function handleReferralChange() {
      partnerField.style.display = 'none';
      otherField.style.display = 'none';
      partnerDetailsInput.removeAttribute('required');
      otherDetailsInput.removeAttribute('required');
      partnerDetailsInput.classList.remove('is-invalid', 'is-valid');
      otherDetailsInput.classList.remove('is-invalid', 'is-valid');
      if (sourceDropdown.value === 'partners') {
        partnerField.style.display = 'block';
        partnerDetailsInput.setAttribute('required', '');
      } else if (sourceDropdown.value === 'other') {
        otherField.style.display = 'block';
        otherDetailsInput.setAttribute('required', '');
      }
    }
    sourceDropdown.addEventListener('change', handleReferralChange);
    handleReferralChange();
  } else {
    console.error("Referral source elements not found!");
  }

});
</script>
@endsection