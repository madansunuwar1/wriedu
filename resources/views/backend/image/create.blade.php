<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Period Form</title>

    @include('backend.script.session')

    @include('backend.script.alert')

    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: white;
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: #f3f3f3;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px;
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

        .form-layout {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .left-section {
            flex: 1;
            padding-right: 20px;
            padding: 15px;
        }

        .right-section {
            flex: 1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 20px;
            color: #333;
            font-family: 'Poppins', sans-serif;
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            display: none;
        }

        .btn-primary {
            background-color: #28a745;
            border: none;
            color: #fff;
            font-size: 1rem;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: bold;
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

        #image-preview {
            max-width: 300px;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ddd;
            display: none;
            margin-top: 10px;
        }

        .file-input-container {
            margin-top: 10px;
            position: relative;
            padding: 10px 0;
        }

        .file-input {
            width: 100%;
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            color: #333;
        }

        /* Hide default file input styling */
        .file-input::-webkit-file-upload-button {
            display: none;
        }

        .file-input::before {
            display: none;
        }

        /* Custom file input styling */
        .custom-file-label {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 8px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .custom-file-label:hover {
            background-color: #218838;
        }

        @media (max-width: 768px) {
            .form-layout {
                flex-direction: column;
            }

            .left-section {
                padding-right: 0;
            }

            .container {
                padding: 20px;
                margin: 10px;
            }

            h2 {
                font-size: 28px;
            }

            .form-group label {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>University Image</h2>

        <div class="alert" id="success-alert">
            Success message goes here.
        </div>

        <form  action="{{ route('backend.image.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
            <div class="form-layout">
                <div class="left-section">
                    <div class="form-group">
                        <label for="image">Upload Image:</label>
                        <div class="file-input-container">
                            <input 
                                type="file" 
                                id="image" 
                                name="image" 
                                accept="image/*" 
                                class="file-input"
                                style="opacity: 0; position: absolute;"
                                onchange="previewImage(event)">
                            <label for="image" class="custom-file-label">Choose File</label>
                            <span id="file-name" style="display: block; margin-top: 8px;"></span>
                        </div>
                    </div>
                </div>
                <div class="right-section">
                    <div id="preview-container">
                        <img id="image-preview" alt="Preview">
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const fileInput = event.target;
            const previewImage = document.getElementById('image-preview');
            const fileName = document.getElementById('file-name');
            const file = fileInput.files[0];

            if (file) {
                fileName.textContent = file.name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = '';
                previewImage.src = '';
                previewImage.style.display = 'none';
            }
        }

       
    </script>
</body>
</html>