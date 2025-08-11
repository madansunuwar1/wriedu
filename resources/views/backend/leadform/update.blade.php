@extends('layouts.admin')

@include('backend.script.session')


@include('backend.script.alert')


@section('content')

<body class="bg-white">

<div class="max-w-2xl mx-auto p-6 rounded-md shadow-lg space-y-8 bg-gradient-to-r from-white">
    <h1 class="text-center text-green-500 text-3xl font-semibold">Update Lead Fill Form</h1>
    
    <!-- Start of the form -->
    <form id="studentForm" action="{{ route('backend.leadform.update', $leads->id) }}" method="POST">
    @csrf
    @method('PUT')
        <!-- Personal details start -->
        <div class="form-container">
            <div class="flex space-x-8">

                <!-- Name of Students -->
                   <!-- Name of Students -->
                   <div class="flex-1">
                    <label for="name" class="block text-gray-700 font-semibold mb-2">Name:</label>
                    <input type="text" id="name" name="name" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('name', $leads->name) }}">
                </div>

                <div class="flex-1" style="display: none;">
    <label for="last" class="block text-gray-700 font-semibold mb-2">Last Name:</label>
    <input type="text" id="last" name="last" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
</div>

            </div>
        </div>
        <!-- Personal details end -->

        <!-- Contact details start -->
        <div class="form-container">
            <div class="flex space-x-8">

                <!-- Email -->
                <div class="flex-1 mt-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email:</label>
                    <input type="email" id="email" name="email" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('email', $leads->email) }}">
                </div>

                <!-- Phone Number -->
                <div class="flex-1 mt-4">
                    <label for="phone" class="block text-gray-700 font-semibold mb-2">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('phone', $leads->phone) }}">
                </div>

            </div>
        </div>
        <!-- Contact details end -->

        <!-- Academic details start -->
        <div class="form-container">
            <div class="flex space-x-8">

           
                <!-- Level of Education -->
                <div class="flex-1 mt-4">
               <label for="lastqualification" class="block text-gray-700 font-semibold mb-2">Level:</label>
              <select id="lastqualification" name="lastqualification" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
              <option value="" disabled selected>Select Last Level</option>
              <option value="Intermediate/Diploma" {{ old('lastqualification', $leads->lastqualification) == 'Intermediate/Diploma' ? 'selected' : '' }}>Intermediate/Diploma</option>
             <option value="Bachelor" {{ old('lastqualification', $leads->lastqualification) == 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
             <option value="Masters" {{ old('lastqualification', $leads->lastqualification) == 'Masters' ? 'selected' : '' }}>Masters</option>
             </select>
           </div>


           <div class="flex-1 mt-4">
                    <label for="courselevel" class="block text-gray-700 font-semibold mb-2"> Course Name :</label>
                    <input type="text" id="courselevel" name="courselevel" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('courselevel', $leads->courselevel) }}">
                </div>


             

            </div>
        </div>
        <!-- Academic details end -->

        <!-- GPA and English Test start -->
        <div class="form-container">
            <div class="flex space-x-8">


              <!-- Year of Passing -->
              <div class="flex-1 mt-4">
    <label for="passed" class="block text-gray-700 font-semibold mb-2">Pass Year:</label>
    <select id="passed" name="passed" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        <option value="" disabled selected>Select Passed Year</option>
        <option value="2015" {{ old('passed', $leads->passed) == '2015' ? 'selected' : '' }}>2015</option>
        <option value="2016" {{ old('passed', $leads->passed) == '2016' ? 'selected' : '' }}>2016</option>
        <option value="2017" {{ old('passed', $leads->passed) == '2017' ? 'selected' : '' }}>2017</option>
        <option value="2018" {{ old('passed', $leads->passed) == '2018' ? 'selected' : '' }}>2018</option>
        <option value="2019" {{ old('passed', $leads->passed) == '2019' ? 'selected' : '' }}>2019</option>
        <option value="2020" {{ old('passed', $leads->passed) == '2020' ? 'selected' : '' }}>2020</option>
        <option value="2021" {{ old('passed', $leads->passed) == '2021' ? 'selected' : '' }}>2021</option>
        <option value="2022" {{ old('passed', $leads->passed) == '2022' ? 'selected' : '' }}>2022</option>
        <option value="2023" {{ old('passed', $leads->passed) == '2023' ? 'selected' : '' }}>2023</option>
        <option value="2024" {{ old('passed', $leads->passed) == '2024' ? 'selected' : '' }}>2024</option>
    </select>
</div>


                <!-- GPA or Percentage -->
                <div class="flex-1 mt-4">
                    <label for="gpa" class="block text-gray-700 font-semibold mb-2">GPA / Percentage:</label>
                    <input type="text" id="gpa" name="gpa" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="GPA / Percentage" value="{{ old('gpa', $leads->gpa) }}" required>
                </div>
            </div>
        </div>
        <!-- GPA and English Test end -->


   <!--english test start here-->
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <div class="flex flex-col gap-4">
                <!-- English Language Test -->
                <div class="w-full">
    <label for="englishTest" class="block text-gray-700 font-semibold mb-2">English Language Test:</label>
    <select
        id="englishTest"
        name="englishTest"
        class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        required
    >
        <option value="" disabled selected>Select English Test</option>
        <option value="IELTS" {{ old('englishTest', $leads->englishTest) == 'IELTS' ? 'selected' : '' }}>IELTS</option>
        <option value="PTE" {{ old('englishTest', $leads->englishTest) == 'PTE' ? 'selected' : '' }}>PTE</option>
        <option value="ELLT" {{ old('englishTest', $leads->englishTest) == 'ELLT' ? 'selected' : '' }}>ELLT</option>
        <option value="No Test" {{ old('englishTest', $leads->englishTest) == 'No Test' ? 'selected' : '' }}>No Test</option>
        <option value="MOI" {{ old('englishTest', $leads->englishTest) == 'MOI' ? 'selected' : '' }}>MOI</option>
    </select>
</div>





                <!-- Conditional fields for tests -->
                <div id="test-fields" class="grid grid-cols-3 gap-4" style="display:none;">
                    <div>
                        <label for="higher" class="block text-gray-700 font-semibold mb-2">Overall Higher:</label>
                        <input
                            type="text"
                            id="higher"
                            name="higher"
                            class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Overall Higher" value="{{ old('higher', $leads->higher) }}"
                        />
                    </div>

                    <div>
                        <label for="less" class="block text-gray-700 font-semibold mb-2">Not Less than:</label>
                        <input
                            type="text"
                            id="less"
                            name="less"
                            class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Not Less than" value="{{ old('less', $leads->less) }}"
                        />
                    </div>
                    <div>
                        <label for="score" class="block text-gray-700 font-semibold mb-2">Overall Score:</label>
                        <input
                            type="text"
                            id="score"
                            name="score"
                            class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Calculated score" value="{{ old('score', $leads->score) }}"
                            readonly
                        />
                    </div>
                </div>

                <!-- No Test Fields -->
                <div id="no-test-fields" class="grid grid-cols-2 gap-4" style="display:none;">
                    <div>
                        <label for="englishscore" class="block text-gray-700 font-semibold mb-2">Overall English Score:</label>
                        <input
                            type="text"
                            id="englishscore"
                            name="englishscore"
                            class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter English Score" value="{{ old('englishscore', $leads->englishscore) }}"
                        />
                    </div>
                    <div>
                        <label for="englishtheory" class="block text-gray-700 font-semibold mb-2">English Theory:</label>
                        <input
                            type="text"
                            id="englishtheory"
                            name="englishtheory"
                            class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter English Theory" value="{{ old('englishtheory', $leads->englishtheory) }}"
                        />
                    </div>
                </div>
            </div>
        </div>
<script>

        document.addEventListener('DOMContentLoaded', function() {
    const englishTestSelect = document.getElementById('englishTest');
    const testFields = document.getElementById('test-fields');
    const noTestFields = document.getElementById('no-test-fields');
    const higherInput = document.getElementById('higher');
    const lessInput = document.getElementById('less');
    const scoreField = document.getElementById('score');

    // Function to reset fields
    function resetFields() {
        higherInput.value = '';
        lessInput.value = '';
        scoreField.value = '';
    }

    // Event listener for test selection
    englishTestSelect.addEventListener('change', function() {
        // Reset all fields first
        resetFields();

        // Hide all fields initially
        testFields.style.display = 'none';
        noTestFields.style.display = 'none';

        // Show appropriate fields based on selection
        switch(this.value) {
            case 'IELTS':
            case 'PTE':
            case 'ELLT':
                testFields.style.display = 'grid';  // Show the test fields for these options
                break;
            case 'No Test':
                noTestFields.style.display = 'grid';  // Show the No Test fields
                break;
            default:
                // If no valid option is selected, hide both fields
                testFields.style.display = 'none';
                noTestFields.style.display = 'none';
                break;
        }
    });

    // Score calculation for standard tests
    [higherInput, lessInput].forEach(input => {
        input.addEventListener('input', function() {
            const higherValue = higherInput.value.trim();
            const lessValue = lessInput.value.trim();

            if (higherValue && lessValue) {
                // Ensure inputs are valid numbers
                if (!isNaN(higherValue) && !isNaN(lessValue)) {
                    scoreField.value = `${higherValue}/${lessValue}`;
                } else {
                    scoreField.value = 'Invalid input';
                }
            } else {
                scoreField.value = '';
            }
        });
    });
});
</script>

<!--english test end here-->

<!--academic section start here-->

<div class="form-container">
    <style>
        .form-container {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }

        .flex {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .flex-section {
            flex: 1;
            min-width: 250px;
        }

        .dropdown-container {
            position: relative;
        }

        .dropdown-input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            background-color: #fff;
            cursor: pointer;
        }

        .dropdown-input:focus {
            outline: none;
            border-color: #60a5fa;
        }

        .dropdown-list {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .dropdown-list.show {
            display: block;
        }

        .dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        .selected-document {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f3f4f6;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .selected-document .checkmark {
            color: #4caf50;
            font-size: 18px;
        }

        .selected-document button {
            background: none;
            border: none;
            cursor: pointer;
            color: #ef4444;
        }

        @media (max-width: 768px) {
            .flex-section {
                flex: 1 1 100%;
            }
        }
    </style>

    <div class="flex">
        <div class="flex-section">
            <label for="academic" class="block font-semibold mb-2">Document Status:</label>
            <div class="dropdown-container">
                <input type="text" id="academic" class="dropdown-input" placeholder="Select Documents" readonly>
                <input type="hidden" id="academicValue" name="academic">
                <div class="dropdown-list" id="dropdownList"></div>
            </div>
        </div>

        <div class="flex-section">
            <h3 class="font-semibold mb-2">Selected Documents</h3>
            <div id="selectedDocuments" class="space-y-2"></div>
        </div>
    </div>
</div>

<script>
    const documents = [
        "SLC passed Certificate", "SLC Marksheet", "SLC Character Certificate",
        "+2 Transcript", "+2 Migration", "+2 Provisional", "+2 Character Certificate",
        "Diploma Transcript", "Diploma Certificate", "Diploma Migration",
        "Diploma Character Certificate", "Bachelor Transcript", "Bachelor Degree Certificate",
        "Bachelor Migration", "Bachelor Provisional", "Bachelor Character",
        "Master Transcript", "Master Degree Certificate", "Master Character Certificate",
        "Degree Letter", "LOR1", "LOR2", "MOI", "Work Experience", "passport",
        "CV"
    ];

    const academic = document.getElementById('academic');
    const academicValue = document.getElementById('academicValue');
    const dropdownList = document.getElementById('dropdownList');
    const selectedDocuments = document.getElementById('selectedDocuments');
    const selectedDocsMap = new Map();

    // Fetch old values from Laravel
    function initializeOldValues() {
        const oldValue = '{{ old("academic", $leads->academic) }}';
        if (oldValue) {
            const oldDocs = oldValue.split(',');
            oldDocs.forEach(doc => {
                if (doc.trim()) {
                    selectedDocsMap.set(doc.trim(), true);
                }
            });
            updateSelectedDocuments();
            updateAcademicValue();
        }
    }

    // Create checkbox items
    documents.forEach(doc => {
        const div = document.createElement('div');
        div.className = 'dropdown-item';
        const isSelected = '{{ old("academic", $leads->academic) }}'.includes(doc);
        
        div.innerHTML = `
            <input type="checkbox" id="${doc}" value="${doc}" ${isSelected ? 'checked' : ''}>
            <label for="${doc}">${doc}</label>
        `;
        dropdownList.appendChild(div);

        const checkbox = div.querySelector('input');
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedDocsMap.set(doc, true);
            } else {
                selectedDocsMap.delete(doc);
            }
            updateSelectedDocuments();
            updateAcademicValue();
        });
    });

    function updateSelectedDocuments() {
        selectedDocuments.innerHTML = '';
        selectedDocsMap.forEach((value, key) => {
            const div = document.createElement('div');
            div.className = 'selected-document';
            div.innerHTML = `
                <span class="checkmark">✓</span>
                <span>${key}</span>
                <button onclick="removeDocument('${key}')" 
                        style="margin-left: auto; color: #ef4444; padding: 2px 8px; border-radius: 4px;">
                    ×
                </button>
            `;
            selectedDocuments.appendChild(div);
        });
    }

    function updateAcademicValue() {
        const selectedValues = Array.from(selectedDocsMap.keys());
        academic.value = selectedValues.length > 0 ? selectedValues.join(', ') : 'Select Documents';
        academicValue.value = selectedValues.join(',');
    }

    function removeDocument(docName) {
        selectedDocsMap.delete(docName);
        const checkbox = document.querySelector(`input[value="${docName}"]`);
        if (checkbox) {
            checkbox.checked = false;
        }
        updateSelectedDocuments();
        updateAcademicValue();
    }

    // Toggle dropdown
    academic.addEventListener('click', () => {
        dropdownList.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown-container')) {
            dropdownList.classList.remove('show');
        }
    });

    // Initialize with old values
    initializeOldValues();
</script>


<!--academic section end here-->

<!--interest condition start-->


<div class="form-container">
            <div class="flex space-x-8">

                <!-- Level of Education -->
                <div class="flex-1 mt-4">
    <label for="country" class="block text-gray-700 font-semibold mb-2">Country:</label>
    <select id="country" name="country" 
        class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        required>
        <option value="" disabled selected>Select Country</option>
        <option value="USA" {{ old('country', $leads->country) === 'USA' ? 'selected' : '' }}>USA</option>
        <option value="UK" {{ old('country', $leads->country) === 'UK' ? 'selected' : '' }}>UK</option>
        <option value="Australia" {{ old('country', $leads->country) === 'Australia' ? 'selected' : '' }}>Australia</option>
        <option value="Canada" {{ old('country', $leads->country) === 'Canada' ? 'selected' : '' }}>Canada</option>
    </select>
</div>


                <div class="flex-1 mt-4">
                    <label for="location" class="block text-gray-700 font-semibold mb-2">Location:</label>
                    <input type="text" id="location" name="location" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('location', $leads->location) }}">
                </div>
               

            </div>
        </div>

<!--interest condition end-->


<!--university details start-->

<div id="form-containers" style="margin-top:25px; display: flex; flex-direction: column; height: 100%;">

  <!-- Form rows container -->
  <div id="form-rows" style="flex-grow: 1;">
    <!-- Initial set of fields (University, Course, Intake) -->
    <div class="flex space-x-4" id="form-row-1">
      <!-- University 1 Field -->
      <div class="flex-1 relative">
        <label for="university1" class="block text-gray-700 font-semibold mb-2">University:</label>
        <input type="text" id="university1" name="university[]" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Search or add university" oninput="filterUniversities('university1')" autocomplete="off" value="{{ old('university', $leads->university) }}">
      </div>

      <!-- Course 1 Field -->
      <div class="flex-1 relative">
        <label for="course1" class="block text-gray-700 font-semibold mb-2">Course:</label>
        <input type="text" id="course1" name="course[]" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Search or add course" oninput="filterCourses('course1')" autocomplete="off" value="{{ old('course', $leads->course) }}">
      </div>

      <!-- Intake 1 Field -->
      <div class="flex-1 relative">
        <label for="intake1" class="block text-gray-700 font-semibold mb-2">Intake:</label>
        <input type="text" id="intake1" name="intake[]" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Search or add intake" oninput="filterIntakes('intake1')" autocomplete="off" value="{{ old('intake', $leads->intake) }}">
      </div>
    </div>
  </div>

  <!-- Document Status -->
  <div class="flex-1 mt-4">
  <label for="offerss" class="block text-gray-700 font-semibold mb-2">Document Status:</label>
  <select 
    id="offerss" 
    name="offerss" 
    class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
    required>
    <option value="" disabled selected>Select Document</option>
    <option value="Offer Process" {{ old('offerss', $leads->offerss) === 'Offer Process' ? 'selected' : '' }}>
      Offer Process
    </option>
    <option value="Offer Received" {{ old('offerss', $leads->offerss) === 'Offer Received' ? 'selected' : '' }}>
      Offer Received
    </option>
    <option value="Offer Rejected" {{ old('offerss', $leads->offerss) === 'Offer Rejected' ? 'selected' : '' }}>
      Offer Rejected
    </option>
  </select>
</div>


  <!-- Buttons to Add and Remove Fields (at the bottom) -->
  <div class="mt-4 flex space-x-4 justify-between">
    <button type="button" id="addButton" onclick="addFields()" class="p-2 text-blue-500 rounded hover:text-blue-700">Add</button>
    <button type="button" id="removeButton" onclick="removeFields()" class="p-2 text-red-500 rounded">Remove</button>
  </div>
</div>

<script>
  let formCounter = 1; // Counter to keep track of added form rows

  // Function to add new form fields
  function addFields() {
    formCounter++;

    // Create a new div element to hold the new university, course, and intake fields
    const newFormRow = document.createElement('div');
    newFormRow.classList.add('flex', 'space-x-4');
    newFormRow.id = `form-row-${formCounter}`;

    // Create University input field
    newFormRow.innerHTML += `
      <div class="flex-1 relative">
        <label for="university${formCounter}" class="block text-gray-700 font-semibold mb-2">University:</label>
        <input type="text" id="university${formCounter}" name="university[]" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Search or add university" oninput="filterUniversities('university${formCounter}')" autocomplete="off">
      </div>
    `;

    // Create Course input field
    newFormRow.innerHTML += `
      <div class="flex-1 relative">
        <label for="course${formCounter}" class="block text-gray-700 font-semibold mb-2">Course:</label>
        <input type="text" id="course${formCounter}" name="course[]" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Search or add course" oninput="filterCourses('course${formCounter}')" autocomplete="off">
      </div>
    `;

    // Create Intake input field
    newFormRow.innerHTML += `
      <div class="flex-1 relative">
        <label for="intake${formCounter}" class="block text-gray-700 font-semibold mb-2">Intake:</label>
        <input type="text" id="intake${formCounter}" name="intake[]" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Search or add intake" oninput="filterIntakes('intake${formCounter}')" autocomplete="off">
      </div>
    `;
    
    // Append the new form row to the form rows container
    document.getElementById('form-rows').appendChild(newFormRow);
  }

  // Function to remove the last form row
  function removeFields() {
    if (formCounter > 1) {
      const lastFormRow = document.getElementById(`form-row-${formCounter}`);
      lastFormRow.remove();
      formCounter--;
    }
  }

  // Placeholder functions for filtering universities, courses, and intakes
  function filterUniversities(inputId) {
    // Implement filtering logic here
    console.log(`Filtering universities for ${inputId}`);
  }

  function filterCourses(inputId) {
    // Implement filtering logic here
    console.log(`Filtering courses for ${inputId}`);
  }

  function filterIntakes(inputId) {
    // Implement filtering logic here
    console.log(`Filtering intakes for ${inputId}`);
  }
</script>

<!--university details end-->

<!-- referral section start here-->
<div class="flex space-x-6 mb-6 mt-6">
            <!-- Source of Student Section -->
            <div class="flex-1">
                <label for="source" class="block text-gray-700 font-semibold mb-2">Source of Referral:</label>
                <select id="source" name="source" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="" disabled selected>Select Source of Referral</option>
                    
        <option value="facebook" {{ old('source', $leads->source) == 'facebook' ? 'selected' : '' }}>Facebook</option>
        <option value="whatsapp" {{ old('source', $leads->source) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
        <option value="instagram" {{ old('source', $leads->source) == 'instagram' ? 'selected' : '' }}>Instagram</option>
        <option value="other" {{ old('source', $leads->source) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Additional Fields based on selection -->
            <div id="partnerField" class="flex-1 hidden">
                <label for="partnerDetails" class="block text-gray-300 font-semibold mb-2">Partner Details:</label>
                <input type="text" id="partnerDetails" name="partnerDetails" class="w-full p-2 text-sm border border-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter partner details" value="{{ old('partnerDetails', $leads->partnerDetails) }}">
            </div>

            <div id="otherField" class="flex-1 hidden">
                <label for="otherDetails" class="block text-gray-300 font-semibold mb-2">Other Referral Details:</label>
                <input type="text" id="otherDetails" name="otherDetails" class="w-full p-2 text-sm border border-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter other details" value="{{ old('otherDetails', $leads->otherDetails) }}">
            </div>
        </div>

        <script>
            // JavaScript to handle showing/hiding of fields based on the dropdown selection
            const sourceDropdown = document.getElementById('source');
            const partnerField = document.getElementById('partnerField');
            const otherField = document.getElementById('otherField');

            sourceDropdown.addEventListener('change', function() {
                // Hide both fields initially
                partnerField.classList.add('hidden');
                otherField.classList.add('hidden');

                // Show the appropriate field based on the selected option
                if (sourceDropdown.value === 'partners') {
                    partnerField.classList.remove('hidden');
                } else if (sourceDropdown.value === 'other') {
                    otherField.classList.remove('hidden');
                }
            });
        </script>
        <!-- referral section end here -->

        <!-- Submit button -->
        <div class="flex justify-center mt-6">
            <button type="submit" class="px-6 py-3 text-base bg-green-500 text-green font-semibold rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 hover:bg-green-600">
              Update
            </button>
        </div>

    </form>
    <!-- End of the form -->

</div>

<script>
    document.getElementById('studentForm').addEventListener('submit', function(event) {
        // Get all input fields
        const fields = document.querySelectorAll('input');
        
        // Loop through each field and check if it's empty
        fields.forEach(function(field) {
            if (!field.value.trim()) {
                field.value = 'N/A'; // Set to N/A if field is empty
            }
        });
    });
</script>
@endsection
