@extends('layouts.admin')

@include('backend.script.session')


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
            max-width: 400px;
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
}


        button:hover {
            background-color: #45a049;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        /* Styling for Error Messages */
        .error {
            color: red;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>

    <form action="{{ route('backend.application.store') }}" method="POST">
    @csrf
        <h1 style="font-family:poppins;">Add New Comment</h1>
        
        <div class="flex-1 mt-4">
    <select id="application" name="application" required>
        <option value="" disabled selected>Select type of</option>
        @foreach ($commentadds as $commentadd)
            <option value="{{ $commentadd->applications }}">{{ $commentadd->applications }}</option>
        @endforeach
    </select>
</div>




        <div class="flex-1 mt-4">
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" required></textarea>
        </div>

        <div class="mt-4">
            <button type="submit">Submit</button>
        </div>
    </form>
    @endsection
