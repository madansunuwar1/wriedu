@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')

@section('content')
    <style>
        body {
            background-color: white;
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 800px;
            width: 100%;
           background: #f3f3f3;
            padding: 30px;
            border-radius: 10px;
           
        }
        h2 {
    font-size: 36px; 
    font-weight: 700; 
    color: #1e7e34; 
    margin-bottom: 16px; 
    text-align: center; 
    letter-spacing: 0.5px; 
    line-height: 1.4; 
    font-family: 'Arial', sans-serif; 
}

        .form-group label {
            font-size: 20px;
            color: #333;
            font-family:poppins;
            font-weight:bold;
            margin-bottom: 8px;
            display: block;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
            outline: none;
        }
        .form-control::placeholder {
            color: #aaa;
        }
        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
            color: #fff;
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .text-center {
            text-align: center;
        }
        .mt-3 {
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }
    </style>

    <div class="container">
        <h2>Notice Period Update</h2>

        <!-- Success message -->
        <div class="alert" style="display: none;">
            Success message goes here.
        </div>

        <form action="{{ route('backend.notice.update', $notices->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
            <!-- Title Input -->
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Enter title" class="form-control" value="{{ old('title', $notices->title) }}"><br/><br/>
            </div>

            <!-- Description Input -->
            <div class="form-group">
    <label for="description">Description:</label>
    <textarea 
        id="description" 
        name="description" 
        placeholder="Enter description" 
        class="form-control custom-textarea">{{ old('description', $notices->description) }}</textarea>
</div>

<!-- Image Upload Form -->
<div class="form-group">
    <label for="image" class="block text-sm font-medium text-gray-700">Upload Image:</label>
    <input 
        type="file" 
        id="image" 
        name="image" 
        accept="image/*" 
        class="mt-2 block w-full text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        onchange="previewImage(event)">

    <!-- Display Current Image if it Exists -->
    @if ($notices->image)
    <div id="preview-container" class="mt-4">
        <img id="image-preview" class="rounded-lg border" src="{{ asset('storage/' . $notices->image) }}" style="width: 300px; height: 300px; object-fit: cover;" />
    </div>
    @else
    <div id="preview-container" class="mt-4">
        <img id="image-preview" class="rounded-lg border" style="display: none; width: 300px; height: 300px; object-fit: cover;" />
    </div>
    @endif
</div>

<script>
// JavaScript to Preview the Image before Upload
function previewImage(event) {
    const fileInput = event.target;
    const previewImage = document.getElementById('image-preview');
    const file = fileInput.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block'; // Show the preview image
        };
        reader.readAsDataURL(file);
    } else {
        previewImage.src = '';
        previewImage.style.display = 'none'; // Hide the preview image if no file is selected
    }
}
</script>


<style>
    .custom-textarea {
        width: 600px; 
        height: 150px; 
        resize: none; 
    }
</style>


            <!-- Submit Button -->
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    @endsection

