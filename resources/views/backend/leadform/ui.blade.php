@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')



@section('content')
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
    }

    .container {
        max-width: 1200px;
        margin: 30px auto;
        background-color: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }

    .tab-container {
        display: flex;
        align-items: center;
        border-bottom: 2px solid #ddd;
        padding-top: 20px;
    }

    .tab {
        padding: 12px 15px;
        cursor: pointer;
        background: #036b0c;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        color: white;
        font-weight: bold;
        border: 1px solid #ddd;
        border-bottom: none;
        text-align: center;
    }

    .tab:hover {
        background-color: white;
        color: green;
    }

    .tab.active {
        color: green;
        background: #fff;
        border-color: #ccc;
        border-bottom: 2px solid #fff;
    }

    .tabs {
        margin-left: auto; /* Moves Tab 4 to the right */
        padding: 12px 15px;
        cursor: pointer;
        font-weight: bold;
        color: black;
        
    }

    .section {
        display: none;
        padding: 20px;
    }

    .section.active {
        display: block;
    }

    /* Popup styling */
    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .popup-content {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .popup-close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        color: #333;
    }

    textarea {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        resize: vertical;
    }

    button {
        padding: 10px 20px;
        background-color: #28a733;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 15px;
    }

    button:hover {
        background-color: #24a52f;
    }

    .dots{
        font-size:30px;
    }
</style>

<div class="container">
    <!-- Tab Navigation -->
    <div class="tab-container">
    
    <div class="tab active" data-tab="tab1">Student Details</div>
        <div class="tab" data-tab="tab2">Comment</div>
        <div class="tab" data-tab="tab5">Upload</div>
        <div class="tab" data-tab="tab6">Update Student Status</div>

        </div>










        @foreach($leads as $lead)
    <div class="document-item">
        <!-- Document Display Content -->
        <div class="document-info">
    
            <!-- Display other document info (e.g., date, size) -->
        </div>


    <!-- Hidden form that will be shown/submitted when a user is selected -->
    <div id="application-form-container-{{ $lead->id }}" class="application-form-container" style="display:none;">
    <form id="application-form-{{ $lead->id }}" method="POST" action="{{ route('backend.application.record', ['id' => $lead->id, 'type' => 'lead']) }}" onsubmit="return confirmSubmission()">
        @csrf
            <input type="hidden" name="document_id" value="{{ $lead->id }}">
            <input type="hidden" name="selected_user_id" id="selected-user-id-{{ $lead->id }}">
            <div class="form-header">
                <h3>Application for <span id="selected-user-name-{{ $lead->id }}"></span></h3>
                <button type="button" class="close-btn" onclick="hideApplicationForm({{ $lead->id }})">×</button>
            </div>
            <div class="form-content">
                <!-- Application form fields can be added here -->
                <div class="form-group">
                    <label for="notes-{{ $lead->id }}">Notes:</label>
                    <textarea id="notes-{{ $lead->id }}" name="notes" rows="4" class="form-control"></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="fa-solid fa-paper-plane"></i> Submit Application
                </button>
                <button type="button" class="cancel-btn" onclick="hideApplicationForm({{ $lead->id }})">
                    <i class="fa-solid fa-ban"></i> Cancel
                </button>
            </div>
        </form>
    </div>
@endforeach

<script>

    
    // Global error handler to catch undefined errors
    window.onerror = function(message, source, lineno, colno, error) {
        console.error("Caught error:", message, "at", source, ":", lineno);
        // Prevent further errors from causing cascading problems
        return true;
    };

    // Show/Hide the primary dropdown menu
    function toggleDropdown(documentId) {
        if (!documentId) {
            console.error("Invalid document ID in toggleDropdown");
            return;
        }
        
        const dropdown = document.getElementById(`dropdown-${documentId}`);
        if (!dropdown) {
            console.error("Dropdown element not found for ID:", documentId);
            return;
        }
        
        // Find the action dots element safely
        const actionDotsSelector = `#dropdown-${documentId}`;
        const dropdownElement = document.querySelector(actionDotsSelector);
        const actionDots = dropdownElement ? dropdownElement.previousElementSibling : null;
        
        if (!actionDots) {
            console.error("Action dots element not found for dropdown:", actionDotsSelector);
        }

        // Hide all other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(el => {
            if (el.id !== `dropdown-${documentId}`) {
                el.style.display = 'none';
            }
        });

        // Hide all forward options
        document.querySelectorAll('.forward-option').forEach(el => {
            el.style.display = 'none';
        });

        // Toggle current dropdown
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';

        // Toggle visibility of the dots (hide them if the dropdown is open)
        if (actionDots) {
            actionDots.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    }

    // Show forward option and hide "Document Forward" menu item
    function showForwardOption(documentId) {
        if (!documentId) {
            console.error("Invalid document ID in showForwardOption");
            return;
        }
        
        const forwardOption = document.getElementById(`forward-option-${documentId}`);
        if (!forwardOption) {
            console.error("Forward option element not found for ID:", documentId);
            return;
        }
        
        forwardOption.style.display = 'block';
    }

    // Select user and display application form
    function selectUser(documentId, userId, userName) {
        if (!documentId || !userId) {
            console.error("Invalid document ID or user ID in selectUser");
            return;
        }
        
        // Default userName if undefined
        if (!userName) userName = "Selected User";
        
        // Hide the dropdown
        const dropdown = document.getElementById(`dropdown-${documentId}`);
        if (dropdown) dropdown.style.display = 'none';
        
        // Set the selected user in the form
        const userIdField = document.getElementById(`selected-user-id-${documentId}`);
        const userNameSpan = document.getElementById(`selected-user-name-${documentId}`);
        
        if (userIdField) userIdField.value = userId;
        if (userNameSpan) userNameSpan.textContent = userName;
        
        // Show the application form
        const formContainer = document.getElementById(`application-form-container-${documentId}`);
        if (formContainer) formContainer.style.display = 'block';
    }
    
    // Hide application form
    function hideApplicationForm(documentId) {
        if (!documentId) {
            console.error("Invalid document ID in hideApplicationForm");
            return;
        }
        
        const formContainer = document.getElementById(`application-form-container-${documentId}`);
        if (formContainer) formContainer.style.display = 'none';
    }

    // Confirm submission before submitting form
    function confirmSubmission() {
        return confirm('Are you sure you want to submit this application?');
    }

    // Show notification
    function showNotification(message, type = 'success') {
        if (!message) {
            console.error("Empty notification message");
            message = "Operation completed";
        }
        
        if (!type) type = 'success';
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;

        // Add to page
        document.body.appendChild(notification);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 500);
        }, 3000);
    }

    // Format date safely
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return 'Invalid Date';
            
            return date.toLocaleDateString();
        } catch (error) {
            console.error("Error formatting date:", error);
            return 'Date Error';
        }
    }

    // Document ready function
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize any needed functionality here
        console.log("Lead management system initialized");
    });
</script>

<style>
    .document-item {
        border-radius: 8px;
        margin-bottom: 10px;
        transition: transform 0.3s ease;
    }

    .document-item:hover {
        transform: translateY(-5px);
    }

    .document-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .document-name {
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }

    .document-actions {
        position: relative;
        display: inline-block;
    }

    .action-dots {
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
        margin-left: 500px;
        border-radius: 50%;
        transition: background-color 0.3s;
        display: block; /* Make sure the dots are visible */
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        z-index: 100;
        min-width: 180px;
        display: none;
    }

    .dropdown-item {
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .dropdown-item:hover {
        background-color: #f5f5f5;
    }

    .forward-option {
        border-top: 1px solid #eee;
        max-height: 200px;
        overflow-y: auto;
    }

    .list-heading {
        font-weight: bold;
        padding: 10px 15px;
        background-color: #f5f5f5;
        margin: 0;
    }

    .user-item {
        padding: 10px 15px 10px 25px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .user-item:hover {
        background-color: #e0f0ff;
    }

    /* Application Form Styles */
    .application-form-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        width: 90%;
        max-width: 600px;
        z-index: 200;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }

    .form-header h3 {
        margin: 0;
        color: #333;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #666;
    }

    .form-content {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-actions {
        padding: 15px 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .submit-btn, .cancel-btn {
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .submit-btn {
        background-color: #4caf50;
        color: white;
        border: none;
    }

    .cancel-btn {
        background-color: #f5f5f5;
        color: #333;
        border: 1px solid #ddd;
    }

    .notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        z-index: 1000;
        animation: slide-in 0.3s ease-out;
    }

    .notification.success {
        background-color: #4caf50;
        color: white;
    }

    .notification.error {
        background-color: #f44336;
        color: white;
    }

    .notification.fade-out {
        animation: fade-out 0.5s forwards;
    }

    @keyframes slide-in {
        from { transform: translateY(100px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @keyframes fade-out {
        from { opacity: 1; }
        to { opacity: 0; }
    }
</style>


    </div>






<!--upload section start here-->

 <div id="tab5" class="section">




<div id="content">
    <!-- The content from backend.upload.index will be loaded here -->
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $(".tab").click(function () {
        let tab = $(this).data("tab");

        if (tab === "tab5") {
            $.ajax({
                url: "{{ route('backend.upload.index') }}", // Ensure this route exists in Laravel
                method: "GET",
                success: function (response) {
                    $("#content").html(response);
                },
                error: function () {
                    $("#content").html("<p>Error loading content.</p>");
                }
            });
        }
    });
});
</script>


</div>


<!--uplod section end here-->



<!--document status section start here-->


<div id="tab6" class="section">
    <h3>Document Status</h3>
    <div class="form-group mb-3">
        <label for="status">Document Status</label>
        <select id="status" class="dropdown">
            <option value="" style="display: none;">Select Status</option>
            @foreach ($documents as $document)
                <option value="{{ $loop->index }}">{{ $document->document }}</option>
            @endforeach
        </select>
    </div>

    <button id="saveButton" type="button" class="btn btn-primary" disabled>Save Permanent</button>

    <div id="statusContainer">
        <div id="statusCircles"></div>
    </div>
</div>

<style>
#statusContainer {
    padding: 20px;
    overflow-x: auto;
}

#statusCircles {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    min-height: 80px;
    padding: 0 20px;
    width: calc(100% - 40px);
}

.status-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    z-index: 2;
    flex: 1;
    margin: 0 10px;
}

.circle {
    width: 15px;
    height: 15px;
    border-radius: 50%;
    border: 2px solid #ccc;
    background-color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 8px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.3s ease;
}

.circle.completed {
    background-color: #4CAF50;
    border-color: #4CAF50;
}

.circle.active {
    background-color: #2196F3;
    border-color: #2196F3;
}

.status-label {
    font-size: 8px;
    color: #666;
    white-space: normal;
    word-wrap: break-word;
    max-width: 100px;
    text-align: center;
    position: absolute;
    top: 100%;
    margin-top: 20px;
    z-index: 3;
}

.base-line {
    position: absolute;
    height: 2px;
    background-color: #ccc;
    top: 38px;
    left: 0;
    z-index: 1;
}

.progress-line {
    position: absolute;
    height: 100%;
    background-color: #4CAF50;
    width: 0;
    transition: width 0.5s ease;
    left: 0;
    top: 0;
}

.checkmark {
    display: none;
    stroke: white;
    stroke-width: 3;
    stroke-linecap: round;
    stroke-linejoin: round;
    width: 12px;
    height: 12px;
}

.completed .checkmark {
    display: block;
}

.completed .number,
.active .number {
    display: none;
}
</style>

<script>
$(document).ready(function () {
    let statusHistory = [];

    // Function to create the checkmark SVG
    function createCheckmarkSVG() {
        return `
            <svg class="checkmark" viewBox="0 0 24 24">
                <path d="M20 6L9 17L4 12"></path>
            </svg>
        `;
    }

    // Function to update the status based on the selected index
    function updateStatus(selectedIndex) {
        const totalSteps = $('.circle').length - 1;

        $('.circle').each(function(index) {
            const $circle = $(this);

            if (index === 0) {
                $circle.removeClass('completed pending').addClass('active');
            }
            else if (index === totalSteps) {
                $circle.removeClass('active pending').addClass('completed');
            }
            else if (index < selectedIndex) {
                $circle.removeClass('active pending').addClass('completed');
            } else if (index === selectedIndex) {
                $circle.removeClass('completed pending').addClass('active');
            } else {
                $circle.removeClass('completed active').addClass('pending');
            }
        });

        // Update progress line width
        const progress = (selectedIndex / totalSteps) * 100;
        $('.progress-line').css('width', `${progress}%`);
    }

    // Function to initialize the status section
    function initialize() {
        const $statusCircles = $('#statusCircles');
        
        // Add base connecting line
        const $baseLine = $('<div>').addClass('base-line');
        const $progressLine = $('<div>').addClass('progress-line');
        $baseLine.append($progressLine);
        $statusCircles.append($baseLine);

        // Add status circles
        $('#status option').not(':first').each(function (index) {
            const $statusItem = $('<div>').addClass('status-item');
            const $circle = $('<div>').addClass('circle pending').attr('data-index', index);

            const $number = $('<span>').addClass('number').text(index + 1);
            const $checkmark = $(createCheckmarkSVG());

            $circle.append($number, $checkmark);
            const $label = $('<div>').addClass('status-label').text($(this).text());

            $statusItem.append($circle, $label);
            $statusCircles.append($statusItem);
        });

        // Load saved status
        const savedHistory = localStorage.getItem('statusHistory');
        if (savedHistory) {
            statusHistory = JSON.parse(savedHistory);
            const maxIndex = Math.max(...statusHistory.map(s => parseInt(s, 10)));
            updateStatus(maxIndex);
        } else {
            $('.progress-line').css('width', '0%');
            updateStatus(0);
        }
    }

    // Save button click event
    $('#saveButton').on('click', function () {
        const selectedStatus = $('#status').val();
        if (!selectedStatus) return;

        statusHistory.push(selectedStatus);
        statusHistory = [...new Set(statusHistory)];
        localStorage.setItem('statusHistory', JSON.stringify(statusHistory));

        updateStatus(parseInt(selectedStatus, 10));
        $('#status').val('');
        $('#saveButton').prop('disabled', true);
    });

    // Enable/Disable save button based on dropdown selection
    $('#status').on('change', function () {
        const selectedStatus = $(this).val();
        $('#saveButton').prop('disabled', !selectedStatus);
    });

    // Initialize the status section
    initialize();
});
</script>




<!--document status section end here-->




    





        

       <!-- Student Details View -->
       <div id="tab1" class="section active">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
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
                            {{ $application->name ?? 'Not provided' }}
                        </span>
                    </div>
                </div>
            </div>

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
                            {{ $application->university ?? 'Not provided' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="course">
                        <span class="text-sm font-medium text-gray-600 block mb-1">Course</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this)">
                            {{ $application->course ?? 'Not provided' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors duration-200" data-field="intake">
                        <span class="text-sm font-medium text-gray-600 block mb-1">Intake</span>
                        <span class="text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded" onclick="makeEditable(this)">
                            {{ $application->intake ?? 'Not provided' }}
                        </span>
                    </div>
                </div>
            </div>
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

<script>
// Function to make fields editable
function makeEditable(element, type = 'text', options = []) {
    let input;
    const currentValue = element.textContent.trim();

    if (type === 'select') {
        // Create select element for dropdown fields
        input = document.createElement('select');
        input.className = 'w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-white';

        // Add default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Select an option';
        defaultOption.disabled = true;
        input.appendChild(defaultOption);

        // Add options from the provided array
        options.forEach(optionText => {
            const option = document.createElement('option');
            option.value = optionText;
            option.textContent = optionText;
            if (currentValue === optionText) {
                option.selected = true;
            }
            input.appendChild(option);
        });
    } else {
        // Create input element for text fields
        input = document.createElement('input');
        input.type = type;
        input.value = currentValue !== 'Not provided' ? currentValue : '';
        input.className = 'w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-white';
    }

    // Replace span with input/select
    const parent = element.parentElement;
    parent.replaceChild(input, element);
    input.focus();

    // Handle save on blur
    input.onblur = async function() {
        const span = document.createElement('span');
        const newValue = this.value || 'Not provided';
        span.textContent = newValue;
        span.className = 'text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded';
        span.onclick = () => makeEditable(span, type, options);

        // Update the value in database
        try {
            const response = await updateField(parent.getAttribute('data-field'), this.value);
            if (response.success) {
                showNotification('Updated successfully', 'success');
            } else {
                showNotification('Update failed', 'error');
                span.textContent = currentValue; // Revert to original value
            }
        } catch (error) {
            console.error('Error updating field:', error);
            showNotification('Update failed', 'error');
            span.textContent = currentValue; // Revert to original value
        }

        parent.replaceChild(span, this);
    };

    // Handle Enter key
    input.onkeypress = function(e) {
        if (e.key === 'Enter') {
            this.blur();
        }
    };

    // Handle Escape key to cancel editing
    input.onkeydown = function(e) {
        if (e.key === 'Escape') {
            const span = document.createElement('span');
            span.textContent = currentValue;
            span.className = 'text-gray-900 block cursor-pointer hover:bg-green-100 p-1 rounded';
            span.onclick = () => makeEditable(span, type, options);
            parent.replaceChild(span, this);
        }
    };
}

// Function to update field in database
async function updateField(field, value) {
    try {
        const response = await fetch('/update-application-field', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                field: field,
                value: value
            })
        });

        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}


// Function to show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white transform transition-all duration-500 translate-y-0 opacity-100 z-50`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remove notification after delay
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 500);
    }, 3000);
}
</script>






















<div id="tab2" class="section">

<div class="tabs" data-tab="tab3">
            <a href="javascript:void(0);" id="addCommentBtn" class="text-decoration-none hover-effect" style="color: black;">
                <i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Add Comment
            </a> 
        </div>

@foreach($lead_comments->sortByDesc(function($comment) {     
                return max($comment->updated_at->timestamp, $comment->created_at->timestamp); 
            }) as $lead_comment)     
                <div class="comment-box"
                     data-comment-id="{{ $lead_comment->id }}"
                     data-updated-at="{{ $lead_comment->updated_at->timestamp }}"
                     data-created-at="{{ $lead_comment->created_at->timestamp }}"
                     data-author="{{ $lead_comment->author_name }}"
                     data-user-id="{{ $lead_comment->user_id }}">         
                    <div class="comment-content">             
                        <p class="comment-author">             
                            {{ $lead_comment->author_name }}
                            @if($lead_comment->updated_by && $lead_comment->updated_at != $lead_comment->created_at)                     
                                                 
                            @endif             
                        </p>         
                        <div class="comment-row">                 
                            <div class="text-container">                     
                                <p class="comment-text">{{ $lead_comment->comment }}</p>                     
                                <span class="comment-date">                         
                                    @if($lead_comment->updated_at != $lead_comment->created_at)                             
                                        Updated: {{ $lead_comment->updated_at->format('Y-m-d H:i:s') }}                         
                                    @else                             
                                        {{ $lead_comment->created_at->format('Y-m-d H:i:s') }}                         
                                    @endif                     
                                </span>                 
                            </div>             
                        </div>         
                    </div>         
                    @if(Auth::check() && Auth::id() == $lead_comment->user_id)
                    <div class="edit-button">             
                        <a href="{{ route('backend.leadcomment.update', $lead_comment->id) }}" 
                           class="link-btn" 
                           data-comment-id="{{ $lead_comment->id }}"
                           data-author="{{ $lead_comment->author_name }}">                 
                            <i class="fas fa-pencil-alt"></i> Edit             
                        </a>         
                    </div>
                    @endif     
                </div> 
            @endforeach
    <!-- Modal -->
    <div id="editModal" class="modal">
                <div class="modal-content">
                    <form action="{{ route('backend.leadcomment.update', '__COMMENT_ID__') }}" id="editForm" method="POST" class="edit-comment-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="comment_id" id="comment_id" value="">
                        <input type="hidden" name="updated_by" value="{{ Auth::id() }}">
                        <input type="hidden" name="editor_name" value="{{ Auth::user()->name }}">
                        <h1>Comment</h1>
                        <label for="modal-comment">Leave a Comment</label>
                        <textarea id="modal-comment" name="comment" placeholder="Type your comment here..." required></textarea>
                        <div class="button-group">
                            <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                            <button type="submit" class="update-btn">Update Comment</button>
                        </div>
                    </form>
                </div>
            </div>
    <!-- Notification Toggle -->
    <div class="notification-section">
        <div class="notification-toggle">
            <label class="toggle-switch">
                <input type="checkbox" id="notificationToggle" checked>
                <span class="toggle-slider"></span>
            </label>
            <span class="toggle-label">Enable Notifications</span>
        </div>
    </div>

    <!-- Notification Popup -->
    <div id="notification" class="notification">
        <div class="notification-message"></div>
    </div>

    <!-- Audio for notifications -->
    <audio id="notificationSound">
        <source src="data:audio/mpeg;base64,//uQxAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAADAAAGhgBVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVWqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqr///////////////////////////////////////////8AAAA8TEFNRTMuOTlyBK8AAAAAAAAAABSAJAOkQgAAgAAABobXqrnWAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//sQxAADwAABpAAAACAAADSAAAAETEFNRTMuOTkuNVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVX/+xDEKIPAAAGkAAAAIAAANIAAAARVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV" type="audio/mpeg">
    </audio>
</div>

<style>

    .tabs{
        display: flex;
    justify-content: flex-end;
    }
   #tab2 {
    padding: 30px;
    border-radius: 12px;
    max-width: 900px;
    margin: 0 auto;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    background: linear-gradient(145deg, #f0f9f0, #e8f5e8);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

#tab2 h1 {
    text-align: center;
    font-size: 32px;
    background: linear-gradient(135deg, #1a472a, #2e844a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 30px;
    font-weight: 700;
    letter-spacing: -0.5px;
}

.comment-box {
    border: 1px solid #d1e7dd;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 12px;
    background: linear-gradient(145deg, #ffffff, #f8faf8);
    width: 100%;
    box-sizing: border-box;
    position: relative;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 4px rgba(0, 128, 0, 0.05);
}

.comment-box:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(0, 128, 0, 0.08);
    border-color: #86c89a;
}

.comment-content {
    padding-right: 50px;
}

.comment-author {
    font-size: 15px;
    font-weight: 600;
    color: #1a472a;
    margin-bottom: 8px;
    padding: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.edited-by {
    font-size: 13px;
    color: #5c8374;
    font-weight: normal;
    font-style: italic;
    margin-left: 8px;
}

.comment-row {
    position: relative;
    margin-top: 4px;
}

.text-container {
    width: 100%;
    position: relative;
    padding-right: 120px;
}

.comment-text {
    font-size: 15px;
    color: #2c513d;
    margin: 0;
    line-height: 1.6;
    word-wrap: break-word;
    white-space: pre-wrap;
    overflow-wrap: break-word;
    width: 100%;
    display: inline;
}

.comment-date {
    font-size: 12px;
    color: #5c8374;
    white-space: nowrap;
    position: absolute;
    right: 0;
    bottom: 0;
    width: 160px;
    text-align: right;
    background: linear-gradient(90deg, transparent, #f8faf8 20%);
    padding-left: 20px;
}

.edit-button {
    position: absolute;
    right: 20px;
    top: 20px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.comment-box:hover .edit-button {
    opacity: 1;
}

.link-btn {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 8px;
    background: linear-gradient(145deg, #ffffff, #f8faf8);
    text-decoration: none;
    color: #2e844a;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid rgba(46, 132, 74, 0.15);
    box-shadow: 0 2px 4px rgba(46, 132, 74, 0.05);
    transition: background 0.25s ease, color 0.25s ease;
    overflow: hidden;
}

.link-btn:hover {
    background: linear-gradient(145deg, #f0f9f0, #e8f5e8);
    color: #1a472a;
}

.link-btn .fa-pencil-alt {
    font-size: 14px;
    margin-right: 6px;
}

/* Text animation */
.link-btn span {
    display: inline-block;
    max-width: 0;
    opacity: 0;
    overflow: hidden;
    transition: max-width 0.3s ease, opacity 0.2s ease;
    white-space: nowrap;
    vertical-align: middle;
}

.link-btn:hover span {
    max-width: 50px;
    opacity: 1;
}

/* Light reflection effect */
.link-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 50%;
    background: linear-gradient(180deg, 
        rgba(255, 255, 255, 0.15) 0%, 
        rgba(255, 255, 255, 0) 100%);
    border-radius: 8px 8px 0 0;
    pointer-events: none;
}




/* Enhanced Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(46, 132, 74, 0.15);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(4px);
}

.modal.show {
    opacity: 1;
    visibility: visible;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: linear-gradient(145deg, #ffffff, #f8faf8);
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 20px 25px -5px rgba(46, 132, 74, 0.1),
                0 10px 10px -5px rgba(46, 132, 74, 0.04);
    width: 550px;
    max-width: 90%;
    position: relative;
    transform: scale(0.95);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #d1e7dd;
}

.modal.show .modal-content {
    transform: scale(1);
}

.modal h1 {
    text-align: center;
    background: linear-gradient(135deg, #1a472a, #2e844a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 24px;
    margin-bottom: 24px;
    font-weight: 700;
    letter-spacing: -0.025em;
}

.modal label {
    display: block;
    margin-bottom: 8px;
    color: #2c513d;
    font-size: 16px;
    font-weight: 500;
}

.modal textarea {
    width: 100%;
    height: 160px;
    padding: 16px;
    border: 1px solid #d1e7dd;
    border-radius: 8px;
    resize: vertical;
    font-size: 15px;
    background-color: #f8faf8;
    box-sizing: border-box;
    transition: all 0.2s ease;
    font-family: inherit;
    color: #2c513d;
}

.modal textarea:focus {
    outline: none;
    border-color: #2e844a;
    background-color: #fff;
    box-shadow: 0 0 0 3px rgba(46, 132, 74, 0.1);
}

.button-group {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}

.cancel-btn, .update-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.cancel-btn {
    background: linear-gradient(145deg, #f0f9f0, #e8f5e8);
    color: #2c513d;
}

.update-btn {
    background: linear-gradient(145deg, #2e844a, #1a472a);
    color: white;
}

.cancel-btn:hover {
    background: linear-gradient(145deg, #e8f5e8, #d1e7dd);
    transform: translateY(-1px);
}

.update-btn:hover {
    background: linear-gradient(145deg, #34915c, #1a472a);
    transform: translateY(-1px);
}

/* Enhanced Notification Styles */
.notification-section {
    margin-top: 40px;
    padding: 24px;
    border-top: 1px solid #d1e7dd;
}

.notification-toggle {
    display: flex;
    align-items: center;
   
    border-radius: 12px;
    width: fit-content;
}


.toggle-switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
    margin-right: 12px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
    /* Hide the checkbox but keep it functional */
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #d1e7dd;
    transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 28px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

input:checked + .toggle-slider {
    background: linear-gradient(145deg, #2e844a, #1a472a);
}

input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

.toggle-label {
    font-size: 15px;
    color: #2c513d;
    font-weight: 500;
    margin-left: 8px;
    user-select: none; /* Prevent text selection */
}

.notification {
    display: none;
    position: fixed;
    top: 24px;
    right: 24px;
    background: linear-gradient(145deg, #2e844a, #1a472a);
    color: white;
    padding: 16px 24px;
    border-radius: 12px;
    box-shadow: 0 10px 15px -3px rgba(46, 132, 74, 0.2),
                0 4px 6px -2px rgba(46, 132, 74, 0.1);
    z-index: 1000;
    animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification.show {
    display: block;
}

.notification-message {
    font-size: 15px;
    font-weight: 500;
}

@keyframes slideIn {
    from {
        transform: translateX(100%) translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateX(0) translateY(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .edit-button {
        opacity: 1;
        transform: translateY(0);
    }
    
    .link-btn {
        padding: 6px 12px;
        font-size: 13px;
    }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    #tab2 {
        padding: 20px;
    }
    
    .comment-box {
        padding: 16px;
    }
    
    .text-container {
        padding-right: 0;
    }
    
    .comment-date {
        position: relative;
        display: block;
        width: auto;
        text-align: left;
        margin-top: 8px;
        padding-left: 0;
        background: none;
    }
    
    .button-group {
        flex-direction: column;
    }
    
    .cancel-btn, .update-btn {
        width: 100%;
    }
}
</style>

<script>

document.addEventListener('DOMContentLoaded', function() {
    // Get all tabs and content sections
    const tabs = document.querySelectorAll('.tab');
    const sections = document.querySelectorAll('.section');

    // Add click event for each tab
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and sections
            tabs.forEach(t => t.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding section
            this.classList.add('active');
            document.getElementById(tabId)?.classList.add('active');
        });
    });
});
 document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');
        const modalTextarea = document.getElementById('modal-comment');
        const currentUserId = '{{ Auth::id() }}';
        
        // Function to check if user can edit comment
        function canEditComment(commentBox) {
            const commentUserId = commentBox.dataset.userId;
            return currentUserId === commentUserId;
        }
        
        function openModal(commentId, commentText, actionUrl) {
            if (!canEditComment(document.querySelector(`[data-comment-id="${commentId}"]`))) {
                showNotification('You do not have permission to edit this comment.', 'error');
                return;
            }
            
            modal.classList.add('show');
            modalTextarea.value = commentText;
            
            const formAction = actionUrl.replace('__COMMENT_ID__', commentId);
            editForm.action = formAction;
            document.getElementById('comment_id').value = commentId;
            
            document.body.style.overflow = 'hidden';
        }
        
    
    function closeModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // Modal click-outside handler
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Edit button handlers
    document.querySelectorAll('.edit-button a').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const commentBox = this.closest('.comment-box');
            const commentId = commentBox.dataset.commentId;
            const commentText = commentBox.querySelector('.comment-text').textContent.trim();
            const actionUrl = this.getAttribute('href');
            openModal(commentId, commentText, actionUrl);
        });
    });

    // Enhanced form submission handler with direct username storage
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                showNotification('Comment updated successfully!');
                
                const commentId = formData.get('comment_id');
                const commentBox = document.querySelector(`[data-comment-id="${commentId}"]`);
                
                if (commentBox) {
                    // Update comment text
                    commentBox.querySelector('.comment-text').textContent = modalTextarea.value;
                    
                    // Update timestamp and editor info
                    const now = new Date();
                    const nowTimestamp = Math.floor(now.getTime() / 1000);
                    const formattedDate = now.toISOString().slice(0, 19).replace('T', ' ');
                    
                    commentBox.querySelector('.comment-date').textContent = 'Updated: ' + formattedDate;
                    commentBox.dataset.updatedAt = nowTimestamp.toString();

                    // Update editor information with the current user's name
                   
                   
                    // Trigger reordering
                    reorderComments();
                }
            }
        })
        .catch(error => {
            showNotification('Error updating comment. Please try again.');
            console.error('Error:', error);
        });
    });

    // Notification functionality
    const notification = document.getElementById('notification');
    const notificationToggle = document.getElementById('notificationToggle');
    const notificationSound = document.getElementById('notificationSound');
    
    // Load notification preferences from localStorage
    const notificationsEnabled = localStorage.getItem('notificationsEnabled') !== 'false';
    notificationToggle.checked = notificationsEnabled;
    
    // Handle notification toggle
    notificationToggle.addEventListener('change', function() {
        localStorage.setItem('notificationsEnabled', this.checked);
        if (this.checked) {
            showNotification('Notifications enabled');
            playNotificationSound();
        } else {
            showNotification('Notifications disabled');
        }
    });

    // Play notification sound
    function playNotificationSound() {
        if (!notificationToggle.checked) return;
        
        notificationSound.volume = 0.5;
        notificationSound.currentTime = 0;
        
        const playPromise = notificationSound.play();
        if (playPromise !== undefined) {
            playPromise.catch(error => {
                console.log("Error playing sound:", error);
            });
        }
    }

    // Show notification
    function showNotification(message, type = 'success') {
        if (!notificationToggle.checked) return;

        const notificationElement = document.getElementById('notification');
        const messageElement = notificationElement.querySelector('.notification-message');
        
        messageElement.textContent = message;
        notificationElement.className = `notification show ${type}`;
        
        playNotificationSound();
        
        setTimeout(() => {
            notificationElement.classList.remove('show');
        }, 3000);
    }

    // Auto-close notification when clicked
    notification.addEventListener('click', function() {
        this.classList.remove('show');
    });

    // Track user activity
    let lastActivityTime = Date.now();
    
    document.addEventListener('mousemove', () => {
        lastActivityTime = Date.now();
    });

    document.addEventListener('keydown', () => {
        lastActivityTime = Date.now();
    });

    // Check for inactivity every minute
    setInterval(() => {
        const inactiveTime = (Date.now() - lastActivityTime) / 1000;
        if (inactiveTime > 300) { // 5 minutes
            showNotification('Remember to save your changes!', 'warning');
        }
    }, 60000);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Close modal with Escape key
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
        
        // Save changes with Ctrl/Cmd + S
        if ((e.ctrlKey || e.metaKey) && e.key === 's' && modal.classList.contains('show')) {
            e.preventDefault();
            editForm.dispatchEvent(new Event('submit'));
        }
    });

    // Handle window focus/blur for notifications
    window.addEventListener('focus', function() {
        if (notificationToggle.checked) {
            showNotification('Welcome back!');
        }
    });

    // Initialize tooltips
    document.querySelectorAll('.link-btn').forEach(btn => {
        btn.setAttribute('title', 'Edit comment');
    });

    // Form validation
    modalTextarea.addEventListener('input', function() {
        const submitButton = editForm.querySelector('button[type="submit"]');
        submitButton.disabled = this.value.trim().length === 0;
    });
});
</script>





























    <!-- HTML Structure -->
<div id="popup" class="popup">
@include('backend.script.notification')
    <div class="popup-content">
        <span class="popup-close" id="closePopup">&times;</span>
        <h3>Add a Comment</h3>
        <form id="commentForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="mention-wrapper">
                <label>Name:</label>
                <input type="text" id="uname" name="uname" value="{{ auth()->user()->name }}" readonly/>
                <textarea id="comment" name="comment" placeholder="Type your comment here..."></textarea>
                <div id="userSuggestions" class="user-suggestions"></div>
                <div id="mentionedUsers" class="mentioned-users-container"></div>
            </div>
            <button type="submit">Submit Comment</button>
        </form>
    </div>
</div>

<!-- CSS -->
<style>
.popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.popup-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    position: relative;
    width: 90%;
    max-width: 500px;
}

.popup-close {
    position: absolute;
    right: 10px;
    top: 10px;
    cursor: pointer;
    font-size: 24px;
}

.mention-wrapper {
    position: relative;
}

.user-suggestions {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    max-height: 150px;
    overflow-y: auto;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1001;
}

.user-suggestion-item {
    padding: 8px 12px;
    cursor: pointer;
}

.user-suggestion-item:hover {
    background-color: #f5f5f5;
}

textarea {
    width: 100%;
    min-height: 100px;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button {
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

/* Label styling */
label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

/* Input field styling */
input[type="text"] {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 16px;
    background-color: #f8f9fa;
    cursor: default;
}

/* Readonly input styles */
input[type="text"][readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057;
}

input[type="text"][readonly]:hover,
input[type="text"][readonly]:focus {
    border-color: #dee2e6;
    box-shadow: none;
    cursor: default;
}

/* Error state */
input[type="text"].error {
    border-color: #dc3545;
}
</style>

<!-- JavaScript -->
<script>
$(document).ready(function () {
    // Store users data and get logged in user
    const users = @json($users);
    const loggedInUser = @json(auth()->user());
    let mentionedUsers = new Set();
    
    const textarea = $('#comment');
    const userSuggestions = $('#userSuggestions');
    const nameInput = $('#uname');
    const form = $('#commentForm');
    let mentionStart = -1;

    // Set logged in user's name
    nameInput.val(loggedInUser.name);

    // Handle form submission
    form.on('submit', function(e) {
        e.preventDefault();
        
        const commentText = textarea.val().trim();
        if (!commentText) {
            showError('Please enter a comment');
            return false;
        }

        // Create FormData and append CSRF token
        const formData = new FormData(this);
        formData.append('_token', $('input[name="_token"]').val());
        formData.append('mentioned_users', JSON.stringify(Array.from(mentionedUsers)));

        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        // Submit form via AJAX
        $.ajax({
            url: '{{ route("backend.leadcomment.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function(response) {
                // Close popup and reset form
                $('#popup').hide();
                form[0].reset();
                mentionedUsers.clear();
                $('#mentionedUsers').empty();

                // Refresh comments section
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    window.location.href = window.location.pathname + '?tab=tab2';
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Error submitting comment. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showError(errorMessage);
                console.error('Error:', error);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Show error message
    function showError(message) {
        let errorDiv = form.find('.error-message');
        if (!errorDiv.length) {
            errorDiv = $('<div class="error-message"></div>');
            form.append(errorDiv);
        }
        errorDiv.text(message).show();
        setTimeout(() => errorDiv.fadeOut(), 5000);
    }

    // Handle mentions
    textarea.on('input', function(e) {
        const text = this.value;
        const caretPos = this.selectionStart;
        
        for(let i = caretPos - 1; i >= 0; i--) {
            if(text[i] === '@') {
                mentionStart = i;
                const currentMentionSearch = text.substring(i + 1, caretPos);
                showUserSuggestions(currentMentionSearch);
                return;
            } else if(text[i] === ' ') {
                break;
            }
        }
        
        userSuggestions.hide();
        mentionStart = -1;
    });

    function showUserSuggestions(search) {
        const filteredUsers = users.filter(user => 
            user.name.toLowerCase().includes(search.toLowerCase())
        );

        if(filteredUsers.length > 0 && search.length > 0) {
            userSuggestions.empty();
            filteredUsers.forEach(user => {
                $('<div>')
                    .addClass('user-suggestion-item')
                    .text(user.name)
                    .on('click', () => insertMention(user))
                    .appendTo(userSuggestions);
            });
            userSuggestions.show();
        } else {
            userSuggestions.hide();
        }
    }

    function insertMention(user) {
        const text = textarea.val();
        const beforeMention = text.substring(0, mentionStart);
        const afterMention = text.substring(textarea[0].selectionStart);
        const newText = `${beforeMention}@${user.name} ${afterMention}`;
        textarea.val(newText);
        
        mentionedUsers.add(user.id);
        addMentionBadge(user);
        
        userSuggestions.hide();
        mentionStart = -1;
        textarea.focus();
    }

    function addMentionBadge(user) {
        const badge = $('<span>')
            .addClass('mention-badge')
            .html(`
                <span class="mention-name">@${user.name}</span>
                <i class="fas fa-times remove-mention"></i>
            `);
        
        badge.find('.remove-mention').on('click', () => removeMention(user));
        $('#mentionedUsers').append(badge);
    }

    function removeMention(user) {
        mentionedUsers.delete(user.id);
        const text = textarea.val();
        const newText = text.replace(`@${user.name} `, '');
        textarea.val(newText);
        $(`.mention-badge:contains('${user.name}')`).remove();
    }

    // Close suggestions when clicking outside
    $(document).on('click', function(e) {
        if(!$(e.target).closest('.mention-wrapper').length) {
            userSuggestions.hide();
        }
    });

    // Popup handling
    const popup = document.getElementById("popup");
    const addCommentBtn = document.getElementById("addCommentBtn");
    const closePopup = document.getElementById("closePopup");

    if (addCommentBtn) {
        addCommentBtn.addEventListener("click", () => {
            popup.style.display = "flex";
        });
    }

    if (closePopup) {
        closePopup.addEventListener("click", () => {
            popup.style.display = "none";
            form[0].reset();
            mentionedUsers.clear();
            $('#mentionedUsers').empty();
            $('.error-message').hide();
        });
    }

    // Close popup on outside click
    window.addEventListener("click", (event) => {
        if (event.target === popup) {
            popup.style.display = "none";
            form[0].reset();
            mentionedUsers.clear();
            $('#mentionedUsers').empty();
            $('.error-message').hide();
        }
    });
});
</script>
@endsection