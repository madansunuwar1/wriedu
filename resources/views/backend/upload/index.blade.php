@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<!Doctype html>
<html>

<head>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }



        .page-container {
            max-width: 1200px;
            background-color: white;

            display: flex;
            gap: 1px;
        }

        /* Create Section Styles */
        .create-section {
            flex: 0 0 500px;
            /* Reduced from 700px */
            border-radius: 8px;
            padding: 1rem;
            /* Reduced from 2rem */
        }

        input[type="file"] {
            position: absolute;
            width: 0;
            height: 0;
            padding: 0;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
            visibility: hidden;
        }



        .upload-container {
            border: 2px dashed #24a52f;
            border-radius: 8px;
            height: 500px;
            width: 400px;
            margin-top: 50px;
            text-align: center;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: rgba(252, 247, 247, 0.5);
            overflow: hidden;
        }

        .upload-container.dragover {
            border-color: #4CAF50;
            background-color: #f0f8f0;
        }

        .upload-icon img {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Index Section Styles */
        .index-section {
            flex: 1;
            margin-right: 30px;
            /* Reduced from 50px */
            background-color: white;
            height: 600px;
            /* Reduced from 700px */
            border-radius: 8px;
        }

        .index-section-content {
            height: calc(100% - 95px);
            padding-right: 10px;
            margin-top: 40px;
            border: 2px dashed #24a52f;
            border-radius: 8px;
            overflow-y: scroll;
            /* Allow scrolling */
        }

        /* Hide scrollbar for WebKit browsers */
        .index-section-content::-webkit-scrollbar {
            width: 0;
            /* Remove scrollbar space */
            background: transparent;
            /* Optional: make background transparent */
        }

        /* Hide scrollbar for Firefox */
        .index-section-content {
            scrollbar-width: none;
            /* Hide scrollbar */
        }


        /* Table Styles */
        .documents-table {
            width: 100%;
            /* Changed from 110% */
            border-collapse: separate;
            margin-top: 20px;
            /* Reduced from 30px */
            margin-right: 10px;
            /* Reduced from 20px */
        }

        .documents-table tr {
            background: white;
            gap: 5px;

        }

        .documents-table td {

            border: none;
            gap: 5px;
            vertical-align: middle;
            margin-left: 10px;
        }

        .documents-table td:first-child {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: 30px;
        }

        .file-item {
            width: 100%;
            margin-bottom: 20px;
            border-radius: 8px;

        }

        .file-details {
            position: relative;
            padding-bottom: 25px;
            /* Add space for the bottom elements */
        }

        .file-bottom-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
        }

        .file-size {
            font-size: 12px;

        }

        .progress-percentage {
            font-size: 12px;
            color: #666;
        }


        .file-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .file-name {
            font-size: 17px;
            font-family: poppins;
            color: #333;
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            cursor: pointer;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .file-name:hover {
            color: #4CAF50;

        }

        .file-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
        }

        .file-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
        }

        .upload-date {
            color: #666;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .view-link {
            color: #4CAF50;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .view-icon {
            width: 20px;
            height: 20px;
            color: #4CAF50;
        }

        .success-icon {
            color: #4CAF50;
            margin-right: 8px;
        }

        .progress-section {
            margin-top: 30px;
            width: 300px;


        }

        .progress-bar {
            width: 100%;
            border-radius: 5px;
        }

        .progress-fill {
            height: 10px;
            /* Adjust height as needed */
            background-color: #4CAF50;
            /* Color of the progress fill */
            width: 0%;
            /* Initial width */
            border-radius: 5px;
        }


        /* Custom Scrollbar */
        .index-section-content::-webkit-scrollbar {
            width: 6px;
        }

        .index-section-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .index-section-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .index-section-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Adjust the heading margins */
        h2 {
            margin-bottom: 20px !important;
            /* Override the existing 40px margin */
        }

        .index-section h2 {
            margin-top: 20px !important;
            /* Reduced from 30px */
        }

        /* Alert Styles */
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 4px;
            z-index: 1000;
        }



        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .page-container {
                flex-direction: column;
            }

            .create-section,
            .index-section {
                width: 100%;
                flex: none;
            }
        }
    </style>

</head>

<body>

    <div class="page-container">
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <!-- Create Section (Left Side) -->
        <div class="create-section">
            <h2 style="color:green; font-size:20px; margin-bottom:40px; font-family:poppins;">Upload Documents</h2>
            <div id="uploadContainer" class="upload-container">
                <div class="upload-icon" id="uploadIcon">
                    <img src="{{ asset('img/wri.png') }}" alt="Upload icon">
                </div>
                <p class="upload-text" id="uploadText">Drag & drop files here</p>
                <input type="file" id="fileInput" name="fileInput[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                <div id="progressSection" class="progress-section"></div>
            </div>
        </div>

        <div class="index-section">
            <h2 style="color:green; font-size:20px; margin-top:30px; font-family:poppins;">Uploaded Files</h2>
            <div class="index-section-content">
                <table class="documents-table">
                    <tbody>
                        @foreach ($uploads as $upload)
                        <tr>
                            <td>
                                <i class="fa-solid fa-circle-check" style="color: green;"></i>&nbsp;&nbsp;

                                <div class="file-wrapper">
                                    <div class="file-icon">
                                        <i class="fa-regular fa-file"></i>
                                    </div>
                                    <a href="{{ asset('storage/uploads/' . $upload->fileInput) }}"
                                        class="file-link"
                                        download="{{ $upload->fileInput }}">
                                        <span class="file-name">{{ $upload->fileInput }}</span>
                                    </a>
                                </div>
                            </td>
                            <td class="upload-date">
                                {{ $upload->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
                                <div class="action-buttons">
                                    <a href="{{ asset('storage/uploads/' . $upload->fileInput) }}"
                                        class="view-link"
                                        target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('upload.destroy', $upload->id) }}"
                                        method="POST"
                                        style="display: inline;"
                                        onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger" style="border: none; background: transparent; padding: 0;">
                                            <i class="fa-solid fa-xmark" style="color:red;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>
    </div>

    <script>
        class FileUploadManager {
            constructor() {
                this.files = new Map();
                this.uploadContainer = document.getElementById('uploadContainer');
                this.fileInput = document.getElementById('fileInput');
                this.progressSection = document.getElementById('progressSection');
                this.uploadText = document.getElementById('uploadText');
                this.uploadIcon = document.getElementById('uploadIcon');
                this.isUploading = false;
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
            }

            handleFiles(newFiles) {
                if (newFiles.length > 0) {
                    this.uploadText.style.display = 'none';
                    this.uploadIcon.style.display = 'none';
                    this.uploadContainer.style.padding = '0';
                    this.uploadContainer.style.justifyContent = 'flex-start';
                }

                newFiles.forEach(file => {
                    if (file.size <= 5 * 1024 * 1024) {
                        this.addFile(file);
                        this.uploadFile(file);
                    } else {
                        alert(`File ${file.name} is too large. Maximum size is 5MB.`);
                    }
                });
            }

            addFile(file) {
                if (!this.files.has(file.name)) {
                    this.files.set(file.name, {
                        file,
                        progress: 0
                    });
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.id = `file-${file.name}`;
                    fileItem.innerHTML = `
            <div class="file-details" style="padding: 10px;">
                <div style="display: flex; align-items: flex-start; margin-bottom: 10px;">
                    <div class="file-name" style="
                        color: green;
                        text-align: left;
                        margin-left: 0;
                        padding-left: 0;
                    ">${file.name}</div>
                </div>
                
                <div style="margin-bottom: 5px;">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill-${file.name}"></div>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div class="file-size" style="font-size: 12px; color: #666;">${(file.size / 1024).toFixed(1)} KB</div>
                    <div class="progress-percentage" style="font-size: 12px; color: #666;" id="progressPercentage-${file.name}">0%</div>
                </div>

                <div class="upload-status" id="uploadStatus-${file.name}">
                    <span class="status-uploading"></span>
                </div>
            </div>`;
                    this.progressSection.appendChild(fileItem);
                }
            }
            async uploadFile(file) {
                const formData = new FormData();
                formData.append('fileInput[]', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{route("backend.upload.store")}}', true);

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        this.updateProgress(file.name, percentComplete);
                    }
                });

                xhr.onload = () => {
                    if (xhr.status === 200) {
                        this.updateUploadStatus(file.name, 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        this.updateUploadStatus(file.name, 'error');
                        this.resetUploadContainer();
                    }
                };

                xhr.onerror = () => {
                    this.updateUploadStatus(file.name, 'error');
                    this.resetUploadContainer();
                };

                xhr.send(formData);
            }

            resetUploadContainer() {
                this.uploadText.style.display = 'block';
                this.uploadIcon.style.display = 'block';
                this.uploadContainer.style.padding = '1.5rem';
                this.uploadContainer.style.justifyContent = 'center';
            }

            updateProgress(fileName, percent) {
                const progressFill = document.getElementById(`progressFill-${fileName}`);
                const progressPercentage = document.getElementById(`progressPercentage-${fileName}`);

                progressFill.style.width = `${percent}%`;
                progressPercentage.textContent = `${Math.round(percent)}%`;
            }

            updateUploadStatus(fileName, status) {
                const statusElement = document.getElementById(`uploadStatus-${fileName}`);

                if (status === 'success') {
                    statusElement.innerHTML = '<span class="status-success"></span>';
                } else if (status === 'error') {
                    statusElement.innerHTML = '<span class="status-error">Upload Failed</span>';
                }
            }
        }

        const uploadManager = new FileUploadManager();

        function confirmDelete() {
            return confirm('Are you sure you want to delete this document?');
        }
    </script>
</body>

</html>
@endsection