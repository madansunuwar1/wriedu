@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')

@section('content')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-family:poppins;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn {
    display: block;
    width: 30%;
    background-color: #28a745; /* Dark green color */
    color: #fff;
    padding: 10px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #218838; /* Slightly lighter green */
}

    </style>

        <form action="{{ route('backend.name.update', $names->id) }}" method="POST">
         @csrf
        @method('PUT')   
        <h2>Fill Counselor</h2>
        <div class="form-group">
            <label for="name">Counselor Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $names->name) }}" placeholder="Enter your Counselor name" required>
        </div>
       
        <button type="submit" class="btn">Update</button>
    </form>
    @endsection
