@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')


@include('backend.script.alert')

@section('content')
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* Input and Select Styles */
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #555;
        }

        select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        select:focus, textarea:focus, button:focus {
            outline: none;
            border-color: #4CAF50;
        }

        button {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
            max-width: 200px;
            text-align: center;
            transition: background-color 0.3s ease;
            display: block;
            margin: 0 auto; 
            margin-top:25px;
        }

        button:hover {
            background-color: #45a049;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        /* Styling for Text Fields */
        input[type="text"] {
            width: 95%;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        input[type="text"]:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
            outline: none;
        }

        input[type="text"]::placeholder {
            color: #aaa;
            font-style: italic;
        }

        /* Styling for Error Messages */
        .error {
            color: red;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>

    <form action="{{ route('backend.application.studentstore') }}" method="POST">
    @csrf
        <h1 style="font-family:poppins;">Add Application for Same Student</h1>

        <div class="flex-1 mt-4">
    <label for="country">Country:</label>
    <select name="country" id="country" class="border p-2">
        @foreach ($data_entries as $data_entrie)
            <option value="{{ $data_entrie->country }}" {{ old('country') == $data_entrie->country ? 'selected' : '' }}>
                {{ $data_entrie->country }}
            </option>
        @endforeach
    </select>
</div>


    <!-- Main Search Input Field -->

<!-- Search Field -->
<input type="text" id="searchField" name="searchField" placeholder="Search intake..." class="mt-4 mb-4 p-2 border border-gray-300" />

<!-- Select dropdown -->
<select name="newIntake" id="newIntake" class="border p-2">
    @foreach ($data_entries as $data_entrie)
        <option value="{{ $data_entrie->newIntake }}" {{ old('newIntake') == $data_entrie->newIntake ? 'selected' : '' }}>
            
            {{ $data_entrie->newIntake }}
        </option>
    @endforeach
</select>

<script>
    // Listen for input changes in the search field
    document.getElementById('searchField').addEventListener('input', function() {
        var searchValue = this.value.toLowerCase();
        var options = document.querySelectorAll('#newIntake option');
        
        // Loop through each option
        options.forEach(function(option) {
            var optionText = option.textContent.toLowerCase();

            // Show or hide the option based on search value
            if (optionText.includes(searchValue)) {
                option.style.display = 'block'; // Show option if search matches
            } else {
                option.style.display = 'none'; // Hide option if search doesn't match
            }
        });
    });

    // Optional: reset the dropdown to the first match or the selected value when the user starts typing
    document.getElementById('searchField').addEventListener('focus', function() {
        document.getElementById('newIntake').size = 5; // Increase the visible size of the dropdown
    });

    document.getElementById('searchField').addEventListener('blur', function() {
        document.getElementById('newIntake').size = 1; // Reset the dropdown size when the input field loses focus
    });
</script>




<!-- Search Field -->
<input type="text" id="customSearchField" name="customSearchField" placeholder="Search university..." class="mt-4 mb-4 p-2 border border-gray-300" list="universityOptions" />

<!-- Select dropdown -->
<select name="newUniversity" id="customUniversity" class="border p-2">
    @foreach ($data_entries as $data_entrie)
        <option value="{{ $data_entrie->newUniversity }}" {{ old('newUniversity') == $data_entrie->newUniversity ? 'selected' : '' }}>
            {{ $data_entrie->newUniversity }}
        </option>
    @endforeach
</select>

<script>
    // Listen for input changes in the custom search field
    document.getElementById('customSearchField').addEventListener('input', function() {
        var searchValue = this.value.toLowerCase();
        var options = document.querySelectorAll('#customUniversity option');
        
        // Loop through each option
        options.forEach(function(option) {
            var optionText = option.textContent.toLowerCase();

            // Show or hide the option based on search value
            if (optionText.includes(searchValue)) {
                option.style.display = 'block'; // Show option if search matches
            } else {
                option.style.display = 'none'; // Hide option if search doesn't match
            }
        });
    });

    // Optional: reset the dropdown to the first match or the selected value when the user starts typing
    document.getElementById('customSearchField').addEventListener('focus', function() {
        document.getElementById('customUniversity').size = 5; // Increase the visible size of the dropdown
    });

    document.getElementById('customSearchField').addEventListener('blur', function() {
        document.getElementById('customUniversity').size = 1; // Reset the dropdown size when the input field loses focus
    });
</script>


<!-- Search Field -->
<input type="text" id="courseSearchField" name="courseSearchField" placeholder="Search course..." class="mt-4 mb-4 p-2 border border-gray-300" list="courseOptions" />

<!-- Select dropdown -->
<select name="newCourse" id="courseDetails" class="border p-2">
    @foreach ($data_entries as $data_entrie)
        <option value="{{ $data_entrie->newCourse }}" {{ old('newCourse') == $data_entrie->newCourse ? 'selected' : '' }}>
            {{ $data_entrie->newCourse }}
        </option>
    @endforeach
</select>

<script>
    // Listen for input changes in the custom search field
    document.getElementById('courseSearchField').addEventListener('input', function() {
        var searchValue = this.value.toLowerCase();
        var options = document.querySelectorAll('#courseDetails option');
        
        // Loop through each option
        options.forEach(function(option) {
            var optionText = option.textContent.toLowerCase();

            // Show or hide the option based on search value
            if (optionText.includes(searchValue)) {
                option.style.display = 'block'; // Show option if search matches
            } else {
                option.style.display = 'none'; // Hide option if search doesn't match
            }
        });
    });

    // Optional: reset the dropdown to the first match or the selected value when the user starts typing
    document.getElementById('courseSearchField').addEventListener('focus', function() {
        document.getElementById('courseDetails').size = 5; // Increase the visible size of the dropdown
    });

    document.getElementById('courseSearchField').addEventListener('blur', function() {
        document.getElementById('courseDetails').size = 1; // Reset the dropdown size when the input field loses focus
    });
</script>  
        <div class="mt-4">
            <button type="submit">Submit</button>
        </div>
    </form>
    @endsection
