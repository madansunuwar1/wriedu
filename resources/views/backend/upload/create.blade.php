@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 2rem;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 1rem;
            font-family: "Lato", serif;
        }

        .main-content {
            display: flex;
            gap: 2rem;
            flex-direction: row; /* Set the flex direction to row for horizontal layout */
            justify-content: space-between;
        }

        .upload-section, .progress-section {
            flex: 1; /* Make both sections take equal width */
        }

        .upload-container {
            border: 2px dashed #24a52f;
            border-radius: 8px;
            height: 450px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: rgba(252, 247, 247, 0.5);
        }

        .upload-container.dragover {
            border-color: #4CAF50;
            background-color: #f0f8f0;
        }

        .upload-icon img {
            width: 100px;
            height: 100px;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .upload-text {
            color: #666;
        }

        .progress-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .file-details {
            flex: 1;
            margin-right: 1rem;
        }

        .file-name {
            font-weight: 500;
            margin-bottom: 0.25rem;
            word-break: break-all;
        }

        .file-size {
            font-size: 0.85rem;
            color: #666;
        }

        .progress-bar {
            width: 100%;
            height: 12px;
            background-color: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
            margin-bottom: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background-color: #4CAF50;
            width: 0;
            transition: width 0.3s ease;
        }

        .progress-percentage {
            font-size: 0.85rem;
            color: #666;
            text-align: right;
            margin-top: 0.5rem;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #ff4444;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .submit-btn {
            align-self: flex-end;
            padding: 0.8rem 2rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .submit-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        #fileInput {
            display: none; /* Hide file input */
        }

        /* Divider between sections */
        .divider {
            width: 2px;
            height: 400px;
            background-color: rgba(36, 165, 47, 0.2);
            margin: 0 1rem; /* Adds spacing before and after the divider */
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .main-content {
                flex-direction: column; /* Stack sections vertically for smaller screens */
            }
        }
    </style>

    <div class="container">
        <h1>Upload Documents</h1>
        <form action="{{route('backend.upload.store')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="main-content">
            <div class="upload-section">
                <div id="uploadContainer" class="upload-container">
                    <div class="upload-icon">
                        <img src="{{ asset('img/wri.png') }}" alt="Upload icon">
                    </div>
                    <p class="upload-text">Drag & drop files here</p>
                    <input type="file" id="fileInput" name="fileInput[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                </div>
            </div>
            <!-- Divider line added between the upload section and the progress section -->
            <div class="divider"></div>
            <div class="progress-section">
                <div id="progressSection"></div>
            </div>
        </div>
        <button id="submitButton" class="submit-btn" disabled>Submit</button>
        </form>
    </div>

    <script>
        class FileUploadManager {
            constructor() {
                this.files = new Map();

                this.uploadContainer = document.getElementById('uploadContainer');
                this.fileInput = document.getElementById('fileInput');
                this.progressSection = document.getElementById('progressSection');
                this.submitButton = document.getElementById('submitButton');

                this.initializeEventListeners();
            }

            initializeEventListeners() {
                this.fileInput.addEventListener('change', (e) => {
                    this.handleFiles(Array.from(e.target.files));
                });

                this.uploadContainer.addEventListener('click', () => this.fileInput.click());

                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    this.uploadContainer.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    this.uploadContainer.addEventListener(eventName, () => {
                        this.uploadContainer.classList.add('dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    this.uploadContainer.addEventListener(eventName, () => {
                        this.uploadContainer.classList.remove('dragover');
                    });
                });

                this.uploadContainer.addEventListener('drop', (e) => {
                    const droppedFiles = Array.from(e.dataTransfer.files);
                    this.handleFiles(droppedFiles);
                });

                this.submitButton.addEventListener('click', () => this.handleSubmit());
            }

            handleFiles(newFiles) {
                newFiles.forEach(file => {
                    if (file.size <= 5 * 1024 * 1024) {
                        this.addFile(file);
                    } else {
                        alert(`File ${file.name} is too large. Maximum size is 5MB.`);
                    }
                });
            }

            addFile(file) {
                if (!this.files.has(file.name)) {
                    this.files.set(file.name, { file, progress: 0 });

                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.id = `file-${file.name}`;

                    fileItem.innerHTML = ` 
                        <div class="file-details">
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${(file.size / 1024).toFixed(1)} KB</div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill-${file.name}"></div>
                            </div>
                            <div class="progress-percentage" id="progressPercentage-${file.name}">0%</div>
                        </div>
                        <button class="remove-btn" onclick="uploadManager.removeFile('${file.name}')">✕</button>
                    `;

                    this.progressSection.appendChild(fileItem);

                    this.simulateUpload(file.name);
                    this.updateSubmitButton();
                }
            }

            simulateUpload(fileName) {
                let progress = 0;
                const progressFill = document.getElementById(`progressFill-${fileName}`);
                const progressPercentage = document.getElementById(`progressPercentage-${fileName}`);
                const interval = setInterval(() => {
                    progress += 10;
                    if (progress >= 100) {
                        progress = 100;
                        clearInterval(interval);
                    }
                    progressFill.style.width = `${progress}%`;
                    progressPercentage.textContent = `${progress}%`;
                    this.files.get(fileName).progress = progress;
                }, 300);
            }

            removeFile(fileName) {
                this.files.delete(fileName);
                const fileItem = document.getElementById(`file-${fileName}`);
                if (fileItem) {
                    fileItem.remove();
                }
                this.updateSubmitButton();
            }

            updateSubmitButton() {
                this.submitButton.disabled = this.files.size === 0;
            }

            handleSubmit() {
                alert('Files submitted successfully!');
            }
        }

        const uploadManager = new FileUploadManager();
    </script>
@endsection
