@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
<div id="tab1" class="section active">


        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   
        
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
      
        

        <div class="space-y-6">
              <!-- Main Container -->
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen" data-forms-id="{{ $applications->id }}">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Student Details</h1>
           
        </div>

        <div class="space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-xl shadow-sm p-6 transition-all duration-300 hover:shadow-md">
                <div class="flex items-center gap-3 mb-4 border-b pb-3">
                    <i class="fas fa-user text-green-600 text-xl"></i>
                    <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="name">
                        <span class="text-sm font-medium text-gray-600 block mb-1">Student Name</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this)">
                            {{ $applications->name ?? 'Not provided' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="email">
                        <span class="text-sm font-medium text-gray-600 block mb-1">Email</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this)">
                            {{ $applications->email ?? 'Not provided' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="phone">
                        <span class="text-sm font-medium text-gray-600 block mb-1">Phone</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this)">
                            {{ $applications->phone ?? 'Not provided' }}
                        </span>
                    </div>
                </div>
            </div>
            <script>
    // Enhanced form ID retrieval and update handling
    async function getFormId() {
        // Try multiple methods to get the form ID
        
        // Method 1: Check for data attribute on the main container
        const container = document.querySelector('[data-forms-id]');
        if (container?.dataset.formsId) {
            return container.dataset.formsId;
        }
        
        // Method 2: Extract from URL path
        const pathMatch = window.location.pathname.match(/\/forms?\/(\d+)/i);
        if (pathMatch?.[1]) {
            return pathMatch[1];
        }
        
        // Method 3: Check URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const idFromUrl = urlParams.get('id') || urlParams.get('forms_id');
        if (idFromUrl) {
            return idFromUrl;
        }
        
        // Method 4: Extract from edit button href
        const editButton = document.querySelector('a[href*="backend.form.edit"]');
        if (editButton) {
            const editHref = editButton.getAttribute('href');
            const editMatch = editHref.match(/\/(\d+)(?:\/edit)?$/);
            if (editMatch?.[1]) {
                return editMatch[1];
            }
        }
        
        throw new Error('Form ID not found');
    }

    // Enhanced makeEditable function
    async function makeEditable(element, type = 'text', options = []) {
        const field = element.closest('[data-field]').dataset.field;
        const currentValue = element.textContent.trim();
        
        if (type === 'select') {
            const select = document.createElement('select');
            select.className = 'w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500';
            
            // Add default option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Select an option';
            defaultOption.disabled = true;
            select.appendChild(defaultOption);
            
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt;
                option.text = opt;
                if (currentValue === opt) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
            
            element.parentNode.replaceChild(select, element);
            select.focus();
            
            select.onchange = async function() {
                const newValue = this.value;
                if (newValue !== currentValue) {
                    try {
                        await updateDatabase(field, newValue);
                        
                        if (field === 'englishTest') {
                            toggleEnglishTestFields(newValue);
                        }
                    } catch (error) {
                        return;
                    }
                }
                
                restoreSpan(this, newValue || currentValue);
            };
            
        } else {
            const input = document.createElement('input');
            input.type = type;
            input.value = currentValue !== 'Not provided' ? currentValue : '';
            input.className = 'w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500';
            
            element.parentNode.replaceChild(input, element);
            input.focus();
            
            const handleUpdate = async () => {
                const newValue = input.value.trim();
                if (newValue !== currentValue) {
                    try {
                        await updateDatabase(field, newValue);
                    } catch (error) {
                        return;
                    }
                }
                
                restoreSpan(input, newValue || currentValue);
            };
            
            input.onblur = handleUpdate;
            input.onkeydown = (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    input.blur();
                }
                if (e.key === 'Escape') {
                    restoreSpan(input, currentValue);
                }
            };
        }
    }

    // Helper function to restore span element
    function restoreSpan(element, value) {
        const span = document.createElement('span');
        span.className = 'text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded';
        span.textContent = value || 'Not provided';
        span.onclick = () => makeEditable(span, element.tagName === 'SELECT' ? 'select' : 'text',
            Array.from(element.options || []).map(opt => opt.value).filter(v => v));
        element.parentNode.replaceChild(span, element);
    }

    // Enhanced update database function
    async function updateDatabase(field, value) {
        try {
            const formId = await getFormId();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            if (!csrfToken) {
                throw new Error('CSRF token not found - please refresh the page');
            }

            const response = await fetch('/update-student-details', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field,
                    value,
                    forms_id: formId
                })
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => null);
                throw new Error(errorData?.message || `Server error: ${response.status}`);
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Update failed');
            }

            showNotification(`Successfully updated ${field}`, 'success');
            return data;

        } catch (error) {
            console.error('Update failed:', error);
            showNotification(error.message, 'error');
            throw error;
        }
    }

    // Enhanced notification function
    function showNotification(message, type = 'success') {
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `
            notification-toast
            fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50
            ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}
            text-white transform transition-all duration-300
        `;
        
        const icon = document.createElement('i');
        icon.className = `fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2`;
        notification.appendChild(icon);
        
        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;
        notification.appendChild(messageSpan);
        
        document.body.appendChild(notification);
        
        requestAnimationFrame(() => {
            notification.style.transform = 'translateY(0)';
            notification.style.opacity = '1';
        });
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Toggle English test fields function
    function toggleEnglishTestFields(testType) {
        const scoreFields = document.querySelectorAll('[data-field="score"], [data-field="higher"], [data-field="less"]');
        const duolingoField = document.querySelector('[data-field="duolingoScore"]');
        const otherField = document.querySelector('[data-field="otherScore"]');
        
        scoreFields.forEach(field => {
            field.style.display = ['IELTS', 'PTE', 'ELLT', 'MOI'].includes(testType) ? 'block' : 'none';
        });
        
        if (duolingoField) {
            duolingoField.style.display = testType === 'Duolingo' ? 'block' : 'none';
        }
        
        if (otherField) {
            otherField.style.display = testType === 'Other' ? 'block' : 'none';
        }
    }
    </script>



 <!-- Level of Education -->
 <div class="flex-1 mt-4">
               <label for="lastqualification" class="block text-gray-700 font-semibold mb-2">Level:</label>
              <select id="lastqualification" name="lastqualification" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
              <option value="" disabled selected>Select Last Level</option>
              <option value="Intermediate/Diploma" {{ old('lastqualification', $applications->lastqualification) == 'Intermediate/Diploma' ? 'selected' : '' }}>Intermediate/Diploma</option>
             <option value="Bachelor" {{ old('lastqualification', $applications->lastqualification) == 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
             <option value="Masters" {{ old('lastqualification', $applications->lastqualification) == 'Masters' ? 'selected' : '' }}>Masters</option>
             </select>
           </div>

           <!-- Year of Passing -->
           <div class="flex-1 mt-4">
    <label for="passed" class="block text-gray-700 font-semibold mb-2">Pass Year:</label>
    <select id="passed" name="passed" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        <option value="" disabled selected>Select Passed Year</option>
        <option value="2015" {{ old('passed', $applications->passed) == '2015' ? 'selected' : '' }}>2015</option>
        <option value="2016" {{ old('passed', $applications->passed) == '2016' ? 'selected' : '' }}>2016</option>
        <option value="2017" {{ old('passed', $applications->passed) == '2017' ? 'selected' : '' }}>2017</option>
        <option value="2018" {{ old('passed', $applications->passed) == '2018' ? 'selected' : '' }}>2018</option>
        <option value="2019" {{ old('passed', $applications->passed) == '2019' ? 'selected' : '' }}>2019</option>
        <option value="2020" {{ old('passed', $applications->passed) == '2020' ? 'selected' : '' }}>2020</option>
        <option value="2021" {{ old('passed', $applications->passed) == '2021' ? 'selected' : '' }}>2021</option>
        <option value="2022" {{ old('passed', $applications->passed) == '2022' ? 'selected' : '' }}>2022</option>
        <option value="2023" {{ old('passed', $applications->passed) == '2023' ? 'selected' : '' }}>2023</option>
        <option value="2024" {{ old('passed', $applications->passed) == '2024' ? 'selected' : '' }}>2024</option>
    </select>
</div>


            </div>
        </div>

 <!-- Academic details end -->


  <!-- GPA and English Test start -->
  <div class="form-container">
            <div class="flex space-x-8">

                <!-- GPA or Percentage -->
                <div class="flex-1 mt-4">
                    <label for="gpa" class="block text-gray-700 font-semibold mb-2">GPA / Percentage:</label>
                    <input type="text" id="gpa" name="gpa" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="GPA / Percentage" value="{{ old('gpa', $applications->gpa) }}" required>
                </div>

                <!-- Product -->
                <div class="flex-1 mt-4">
               <label for="english" class="block text-gray-700 font-semibold mb-2">Product:</label>
              <select id="english" name="english" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
             <option value="" disabled selected>Select Product</option>
              <option value="Study Group" {{ old('english', $applications->english) == 'Study Group' ? 'selected' : '' }}>Study Group</option>
           </select>
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
        <option value="IELTS" {{ old('englishTest', $applications->englishTest) == 'IELTS' ? 'selected' : '' }}>IELTS</option>
        <option value="PTE" {{ old('englishTest', $applications->englishTest) == 'PTE' ? 'selected' : '' }}>PTE</option>
        <option value="ELLT" {{ old('englishTest', $applications->englishTest) == 'ELLT' ? 'selected' : '' }}>ELLT</option>
        <option value="No Test" {{ old('englishTest', $applications->englishTest) == 'No Test' ? 'selected' : '' }}>No Test</option>
        <option value="MOI" {{ old('englishTest', $applications->englishTest) == 'MOI' ? 'selected' : '' }}>MOI</option>
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
                            placeholder="Enter Overall Higher" value="{{ old('higher', $applications->higher) }}"
                        />
                    </div>

                    <div>
                        <label for="less" class="block text-gray-700 font-semibold mb-2">Not Less than:</label>
                        <input
                            type="text"
                            id="less"
                            name="less"
                            class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Not Less than" value="{{ old('less', $applications->less) }}"
                        />
                    </div>
                    <div>
                        <label for="score" class="block text-gray-700 font-semibold mb-2">Overall Score:</label>
                        <input
                            type="text"
                            id="score"
                            name="score"
                            class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Calculated score" value="{{ old('score', $applications->score) }}"
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
                            placeholder="Enter English Score" value="{{ old('englishscore', $applications->englishscore) }}"
                        />
                    </div>
                    <div>
                        <label for="englishtheory" class="block text-gray-700 font-semibold mb-2">English Theory:</label>
                        <input
                            type="text"
                            id="englishtheory"
                            name="englishtheory"
                            class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter English Theory" value="{{ old('englishtheory', $applications->englishtheory) }}"
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
        <option value="USA" {{ old('country', $applications->country) === 'USA' ? 'selected' : '' }}>USA</option>
        <option value="UK" {{ old('country', $applications->country) === 'UK' ? 'selected' : '' }}>UK</option>
        <option value="Australia" {{ old('country', $applications->country) === 'Australia' ? 'selected' : '' }}>Australia</option>
        <option value="Canada" {{ old('country', $applications->country) === 'Canada' ? 'selected' : '' }}>Canada</option>
    </select>
</div>


                <div class="flex-1 mt-4">
                    <label for="location" class="block text-gray-700 font-semibold mb-2">Location:</label>
                    <input type="text" id="location" name="location" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('location', $applications->location) }}">
                </div>
               

            </div>
        </div>

<!--interest condition end-->

            <!-- University Details -->
            <div class="bg-white rounded-xl shadow-sm p-6 transition-all duration-300 hover:shadow-md">
                <div class="flex items-center gap-3 mb-4 border-b pb-3">
                    <i class="fas fa-university text-green-600 text-xl"></i>
                    <h2 class="text-xl font-semibold text-gray-800">University Details</h2>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="university">
                        <span class="text-sm font-medium text-gray-600 block mb-1">University</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this)">
                            {{ $applications->university ?? 'Not provided' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="course">
                        <span class="text-sm font-medium text-gray-600 block mb-1">Course</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this)">
                            {{ $applications->course ?? 'Not provided' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="intake">
                        <span class="text-sm font-medium text-gray-600 block mb-1">Intake</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this)">
                            {{ $applications->intake ?? 'Not provided' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="country">
                        <span class="text-sm font-medium text-gray-600 block mb-1">Country</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this, 'select', ['USA', 'UK', 'Australia', 'Canada'])">
                            {{ $applications->country ?? 'Not provided' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="location">
                        <span class="text-sm font-medium text-gray-600 block mb-1">Location</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this)">
                            {{ $applications->location ?? 'Not provided' }}
                        </span>
                    </div>
                </div>
            </div>


            <!--processing start here-->


<div class="flex-1">
    <label for="document" class="block text-gray-700 font-semibold mb-2">Document Status:</label>
    <select
        id="document"
        name="document"
        class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        required
        onchange="handleDocumentStatusChange()"
    >
        <option value="" disabled selected>Select your Document Status</option>
        <option value="Partially Received" {{ old('document', $applications->document) === 'Partially Received' ? 'selected' : '' }}>
            Partially Received
        </option>
        <option value="Fully Received" {{ old('document', $applications->document) === 'Fully Received' ? 'selected' : '' }}>
            Fully Received
        </option>
        <option value="Initiated For Offer" {{ old('document', $applications->document) === 'Initiated For Offer' ? 'selected' : '' }}>
            Initiated For Offer
        </option>
    </select>
</div>

<!-- Second dropdown -->
<div class="flex-1 mt-4" id="second-dropdown-container" style="display: none;">
    <label for="additionalinfo" class="block text-gray-700 font-semibold mb-2">Initiated Offer Information:</label>
    <select
        id="additionalinfo"
        name="additionalinfo"
        class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
        <option value="" disabled selected>Select Initiated</option>
        <option value="Not Initiated Offer" {{ old('additionalinfo', $applications->additionalinfo) == 'Not Initiated Offer' ? 'selected' : '' }}>Not Initiated Offer</option>
        <option value="Initiated Offer" {{ old('additionalinfo', $applications->additionalinfo) == 'Initiated Offer' ? 'selected' : '' }}>Initiated Offer</option>

    </select>
</div>

<script>
    function handleDocumentStatusChange() {
        const documentStatus = document.getElementById("document").value;
        const secondDropdownContainer = document.getElementById("second-dropdown-container");
        const additionalInfo = document.getElementById("additionalinfo");

        if (documentStatus === "Fully Received") {
            // Show the second dropdown
            secondDropdownContainer.style.display = "block";
            additionalInfo.value = ""; // Clear selection
        } else {
            // Hide the second dropdown and set "N/A" as the value
            secondDropdownContainer.style.display = "none";
            additionalInfo.innerHTML = `<option value="N/A" selected>N/A</option>`;
        }
    }
</script>
<!--processing end here-->


           <!-- referral section start here-->
<div class="flex space-x-6 mb-6 mt-6">
            <!-- Source of Student Section -->
            <div class="flex-1">
                <label for="source" class="block text-gray-700 font-semibold mb-2">Source of Referral:</label>
                <select id="source" name="source" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="" disabled selected>Select Source of Referral</option>
                    
        <option value="facebook" {{ old('source', $applications->source) == 'facebook' ? 'selected' : '' }}>Facebook</option>
        <option value="whatsapp" {{ old('source', $applications->source) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
        <option value="instagram" {{ old('source', $applications->source) == 'instagram' ? 'selected' : '' }}>Instagram</option>
        <option value="partners" {{ old('source', $applications->source) == 'partners' ? 'selected' : '' }}>Partners</option>
        <option value="other" {{ old('source', $applications->source) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Additional Fields based on selection -->
            <div id="partnerField" class="flex-1 hidden">
                <label for="partnerDetails" class="block text-gray-300 font-semibold mb-2">Partner Details:</label>
                <input type="text" id="partnerDetails" name="partnerDetails" class="w-full p-2 text-sm border border-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter partner details" value="{{ old('partnerDetails', $applications->partnerDetails) }}">
            </div>

            <div id="otherField" class="flex-1 hidden">
                <label for="otherDetails" class="block text-gray-300 font-semibold mb-2">Other Referral Details:</label>
                <input type="text" id="otherDetails" name="otherDetails" class="w-full p-2 text-sm border border-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter other details" value="{{ old('otherDetails', $applications->otherDetails) }}">
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


      


</div>
</div>
</div>

<style>
    /* Add these CSS classes to your stylesheet */
.editable-field {
  position: relative;
  display: flex;
  align-items: center;
}

.edit-icon {
  margin-left: 0.5rem;
  color: #9CA3AF; /* gray-400 */
  transition: color 0.2s;
}

.editable-field:hover .edit-icon {
  color: #059669; /* green-600 */
}
</style>

<style>
    .editable-field {
        position: relative;
        display: flex;
        align-items: center;
    }

    .edit-icon {
        margin-left: 0.5rem;
        color: #9CA3AF;
        transition: color 0.2s;
    }

    .editable-field:hover .edit-icon {
        color: #059669;
    }

    .notification-toast {
        transform: translateY(-20px);
        opacity: 0;
    }
    </style>

    <script>
// Student Details Manager - Combined Implementation
document.addEventListener('DOMContentLoaded', function() {
    initializeCsrfToken();
});

// CSRF Token Management
function initializeCsrfToken() {
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.head.appendChild(meta);
    }
}

function getCsrfToken() {
    const token = 
        document.querySelector('meta[name="csrf-token"]')?.content ||
        document.querySelector('input[name="_token"]')?.value ||
        document.querySelector('input[name="csrf-token"]')?.value;
    
    if (!token) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.head.appendChild(meta);
        return meta.content;
    }
    
    return token;
}

// Form ID Management
async function getFormId() {
    // Method 1: Check for data attribute
    const container = document.querySelector('[data-forms-id]');
    if (container?.dataset.formsId) {
        return container.dataset.formsId;
    }
    
    // Method 2: Extract from URL
    const pathMatch = window.location.pathname.match(/\/forms?\/(\d+)/i);
    if (pathMatch?.[1]) {
        return pathMatch[1];
    }
    
    throw new Error('Forms ID not found');
}




// Update the database function to use the correct route
async function updateDatabase(field, value) {
    try {
        const formsId = await getFormId();
        const csrfToken = getCsrfToken();
        
        if (!csrfToken) {
            throw new Error('CSRF token not available - please refresh the page');
        }

        // Use the correct route format
        const response = await fetch(`/forms/${formsId}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ field, value })
        });

        if (!response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType?.includes('application/json')) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Server error: ${response.status}`);
            }
            throw new Error(`Network error: ${response.status}`);
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'Update failed');
        }

        showNotification(`Successfully updated ${field}`, 'success');
        return data;

    } catch (error) {
        console.error('Update failed:', error);
        const errorMessage = error.message.includes('CSRF')
            ? 'Session expired. Please refresh the page.'
            : error.message || 'Update failed';
        showNotification(errorMessage, 'error');
        throw error;
    }
}








// Main Editable Field Function
function makeEditable(element, type = 'text', options = []) {
    const currentValue = element.textContent.trim();
    const field = element.closest('[data-field]')?.dataset.field;

    if (!field) {
        console.error('No data-field attribute found');
        showNotification('Configuration error', 'error');
        return;
    }

    const input = type === 'select' 
        ? createSelectInput(options, currentValue)
        : createTextInput(type, currentValue);

    element.parentNode.replaceChild(input, element);
    input.focus();

    setupInputHandlers(input, element, field, currentValue, type, options);
}

// Input Creation Functions
function createSelectInput(options, currentValue) {
    const select = document.createElement('select');
    select.className = 'w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-white';
    
    // Add default option
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Select an option';
    defaultOption.disabled = true;
    select.appendChild(defaultOption);
    
    // Add provided options
    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt;
        option.textContent = opt;
        option.selected = currentValue === opt;
        select.appendChild(option);
    });
    
    return select;
}

function createTextInput(type, currentValue) {
    const input = document.createElement('input');
    input.type = type;
    input.value = currentValue !== 'Not provided' ? currentValue : '';
    input.className = 'w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-white';
    return input;
}

// Event Handler Setup
function setupInputHandlers(input, originalElement, field, currentValue, type, options) {
    if (type === 'select') {
        setupSelectHandlers(input, field, currentValue, options);
    } else {
        setupTextInputHandlers(input, field, currentValue, type, options);
    }
}

function setupSelectHandlers(select, field, currentValue, options) {
    select.onchange = async function() {
        const newValue = this.value;
        if (newValue !== currentValue) {
            try {
                await updateDatabase(field, newValue);
                if (field === 'englishTest') {
                    toggleEnglishTestFields(newValue);
                }
            } catch (error) {
                restoreSpan(this, currentValue, 'select', options);
                return;
            }
        }
        restoreSpan(this, newValue || currentValue, 'select', options);
    };
}

function setupTextInputHandlers(input, field, currentValue, type, options) {
    const handleUpdate = async () => {
        const newValue = input.value.trim();
        if (newValue !== currentValue) {
            try {
                await updateDatabase(field, newValue);
                restoreSpan(input, newValue, type, options);
            } catch (error) {
                restoreSpan(input, currentValue, type, options);
            }
        } else {
            restoreSpan(input, currentValue, type, options);
        }
    };

    input.onblur = handleUpdate;
    input.onkeydown = (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            input.blur();
        } else if (e.key === 'Escape') {
            restoreSpan(input, currentValue, type, options);
        }
    };
}

// Element Restoration
function restoreSpan(input, value, type, options) {
    const span = document.createElement('span');
    span.textContent = value || 'Not provided';
    span.className = 'text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded';
    span.onclick = () => makeEditable(span, type, options);
    input.parentNode.replaceChild(span, input);
}

// Database Operations
async function updateDatabase(field, value) {
    try {
        const formsId = await getFormId();
        const csrfToken = getCsrfToken();
        
        if (!csrfToken) {
            throw new Error('CSRF token not available - please refresh the page');
        }

        const response = await fetch('/update-student-details', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ field, value, forms_id: formsId })
        });

        if (!response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType?.includes('application/json')) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Server error: ${response.status}`);
            }
            throw new Error(`Network error: ${response.status}`);
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'Update failed');
        }

        showNotification(`Successfully updated ${field}`, 'success');
        return data;

    } catch (error) {
        console.error('Update failed:', error);
        const errorMessage = error.message.includes('CSRF')
            ? 'Session expired. Please refresh the page.'
            : error.message || 'Update failed';
        showNotification(errorMessage, 'error');
        throw error;
    }
}

// Notification System
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `
        fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50
        ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}
        text-white transform transition-all duration-300
        opacity-0 translate-y-[-20px]
    `;
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    requestAnimationFrame(() => {
        notification.style.transform = 'translateY(0)';
        notification.style.opacity = '1';
    });
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// English Test Fields Management
function toggleEnglishTestFields(testType) {
    const scoreFields = document.querySelectorAll('[data-field="score"], [data-field="higher"], [data-field="less"]');
    const duolingoField = document.querySelector('[data-field="duolingoScore"]');
    const otherField = document.querySelector('[data-field="otherScore"]');
    
    scoreFields.forEach(field => {
        field.style.display = ['IELTS', 'PTE', 'ELLT', 'MOI'].includes(testType) ? 'block' : 'none';
    });
    
    if (duolingoField) {
        duolingoField.style.display = testType === 'Duolingo' ? 'block' : 'none';
    }
    
    if (otherField) {
        otherField.style.display = testType === 'Other' ? 'block' : 'none';
    }
}
    </script>

@endsection