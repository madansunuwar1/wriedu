<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Index</title>

    @include('backend.script.session')

    @include('backend.script.alert')

    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', Arial, sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .heading {
            text-align: center;
            font-size: 2.5rem;
            color: #28a745;
            margin: 1.5rem 0;
            font-weight: bold;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .table-container {
            overflow-x: auto;
            margin: 1.5rem 0;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .table-right {
            text-align: right;
        }
        tbody tr:hover {
            background-color: #f8f9fa;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 50px auto;
            padding: 30px;
            width: 90%;
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .close-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            cursor: pointer;
            color: #666;
            transition: color 0.3s;
        }
        .close-modal:hover {
            color: #333;
        }
        .form-layout {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .left-section {
            flex: 1;
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
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        .notice-image {
            width: 100px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
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
        .link-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .link-btn:hover {
            background-color: #f0f0f0;
            color: #0056b3;
        }
        .link-btn i {
            margin-right: 0.5rem;
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
        .text-center {
            text-align: center;
        }
        .mt-3 {
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            .heading {
                font-size: 2rem;
            }
            .form-layout {
                flex-direction: column;
            }
            .modal-content {
                margin: 20px;
                padding: 20px;
            }
            th, td {
                padding: 0.75rem;
            }
            .form-group label {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1 class="heading">Image Index</h1>
        </header>

        <div id="success-alert" class="alert alert-success" style="display: none;">
            Operation completed successfully
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>University</th>
                        <th class="table-right">Views Application</th>
                    </tr>
                </thead>
                <tbody id="image-list">
                    @foreach ($data_entries as $data_entry)
                    <tr>
                        <td>{{ $data_entry->newUniversity }}</td>
                        <td class="table-right">
                            <a href="#" class="link-btn" onclick="openModal('{{ $data_entry->id }}')">
                                <i class="fas fa-eye"></i>Update
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 style="color: #1e7e34; margin-bottom: 20px; text-align: center;">University Image</h2>
            
            <form action="{{ route('backend.image.store') }}" method="POST" enctype="multipart/form-data">
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
    </div>

    <script>
        function showSuccess(message) {
            const alert = document.getElementById('success-alert');
            alert.textContent = message;
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 3000);
        }

        function openModal(imageId) {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'block';
            document.getElementById('image-preview').style.display = 'none';
            document.getElementById('file-name').textContent = '';
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target == modal) {
                closeModal();
            }
        }

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

        async function loadImages() {
            try {
                const response = await fetch('/api/images');
                const images = await response.json();
                
                const imageList = document.getElementById('image-list');
                imageList.innerHTML = images.map(image => `
                    <tr>
                        <td>${image.newUniversity}</td>
                        <td class="table-right">
                            <a href="#" class="link-btn" onclick="openModal('${image.id}')">
                                <i class="fas fa-eye"></i>View
                            </a>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Error loading images:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', loadImages);
    </script>
</body>
</html>