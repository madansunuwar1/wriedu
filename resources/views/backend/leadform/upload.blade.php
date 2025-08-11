@extends('layouts.admin')

@include('backend.script.session')


@include('backend.script.alert')


@section('content')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    text-align: center;
    height: calc(100% + 200px); /* Increase height by 200px */
}

        .form-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container input[type="file"] {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
        }

        .form-container button {
            background-color: #28a745; /* Green color */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            background-color: #218838; /* Darker green */
        }

        .form-container button:active {
            background-color: #1e7e34; /* Even darker green */
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 5px;
            text-align: center;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            float: right;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .alert {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Animated Tick Mark */
        .tick-icon {
            font-size: 50px;
            color: #28a745;
            opacity: 0;
            animation: tick-animation 1s forwards;
            margin-bottom: 15px;
        }

        @keyframes tick-animation {
            0% {
                opacity: 0;
                transform: scale(0);
            }
            50% {
                opacity: 1;
                transform: scale(1.2);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Fade In Message */
        .success-message {
            font-size: 18px;
            color: #333;
            opacity: 0;
            animation: message-animation 1s forwards;
            margin-top: 10px;
        }

        @keyframes message-animation {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="form-container">
        <h2>Import Lead Form</h2>
        <form action="{{ route('leadform.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" accept=".xlsx" required>
            <button type="submit" class="btn btn-secondary">Import CSV</button>
        </form>
    </div>

    <!-- Success Popup Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="tick-icon">&#10004;</div> <!-- Tick mark -->
            <p class="success-message">{{ session('success') }}</p> <!-- Success message -->
        </div>
    </div>

    <script>
        // Check if success message exists in session
        @if(session('success'))
            // Show the modal
            var modal = document.getElementById("successModal");
            var span = document.getElementsByClassName("close")[0];
            modal.style.display = "block";

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        @endif
    </script>
@endsection
