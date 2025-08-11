@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
    <div class="card">
        <div class="border-bottom title-part-padding">
            <h4 class="card-title mb-0">Update Documents</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('backend.upload.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                
                <!-- Upload Section -->
                <div class="upload-section mb-4">
                    <div id="uploadContainer" class="upload-container p-4 border rounded text-center">
                        <div class="upload-icon mb-3">
                            <img src="{{ asset('img/wri.png') }}" alt="Upload icon" style="height: 60px;">
                        </div>
                        <p class="upload-text mb-2">Drag & drop files here</p>
                        <p class="text-muted small mb-3">Supports: PDF, DOC, DOCX, TXT (Max 5MB each)</p>
                        <input type="file" id="fileInput" name="fileInput[]" multiple accept=".pdf,.doc,.docx,.txt" class="d-none">
                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                            <i class="ti ti-upload me-1"></i> Select Files
                        </button>
                    </div>
                </div>

                <!-- Progress Section -->
                <div class="progress-section">
                    <div id="progressSection" class="file-list"></div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button id="submitButton" type="submit" class="btn btn-primary" disabled>
                        <i class="ti ti-device-floppy me-1"></i> Update Documents
                    </button>
                </div>
            </form>
        </div>
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
                            <div class="file-size">${this.formatFileSize(file.size)}</div>
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

            formatFileSize(bytes) {
                if (bytes < 1024) return bytes + ' bytes';
                else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                else return (bytes / 1048576).toFixed(1) + ' MB';
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
        }

        const uploadManager = new FileUploadManager();
    </script>
@endsection