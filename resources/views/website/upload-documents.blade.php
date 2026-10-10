<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Documents - Click2Serve</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header h1 {
            color: #2d3748;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            color: #718096;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2d3748;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #667eea;
            background: #f7fafc;
        }

        .file-upload-area.dragover {
            border-color: #667eea;
            background: #edf2f7;
        }

        .file-upload-icon {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 15px;
        }

        .file-upload-text {
            color: #718096;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .file-upload-hint {
            color: #a0aec0;
            font-size: 0.9rem;
        }

        .document-item {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .document-info {
            flex: 1;
        }

        .document-name {
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .document-details {
            color: #718096;
            font-size: 0.9rem;
        }

        .document-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #5a67d8;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .btn-danger {
            background: #e53e3e;
        }

        .btn-danger:hover {
            background: #c53030;
        }

        .btn-success {
            background: #38a169;
        }

        .btn-success:hover {
            background: #2f855a;
        }

        .error {
            background: #fed7d7;
            color: #9b2c2c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success {
            background: #c6f6d5;
            color: #22543d;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .back-link {
            margin-bottom: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .requirements-list {
            background: #f7fafc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .requirements-list h3 {
            color: #2d3748;
            margin-bottom: 15px;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .requirement-item:last-child {
            border-bottom: none;
        }

        .requirement-checkbox {
            margin-right: 10px;
        }

        .requirement-name {
            flex: 1;
            color: #2d3748;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .form-container {
                padding: 20px;
            }

            .document-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .document-actions {
                margin-top: 10px;
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Upload Documents</h1>
            <p>Upload required documents for your civil service request</p>
        </div>

        <div class="back-link">
            <a href="{{ route('website.home') }}">← Back to Home</a>
        </div>

        <div class="form-container">
            <div id="error-message" class="error" style="display: none;"></div>
            <div id="success-message" class="success" style="display: none;"></div>

            <div class="form-group">
                <label for="civil_service_id">Civil Service Request *</label>
                <select id="civil_service_id" name="civil_service_id" required>
                    <option value="">Select a civil service request</option>
                    <!-- This will be populated dynamically -->
                </select>
            </div>

            <div id="requirements-section" style="display: none;">
                <div class="requirements-list">
                    <h3>Required Documents</h3>
                    <div id="requirements-list"></div>
                </div>

                <div class="form-group">
                    <label>Upload Documents</label>
                    <div class="file-upload-area" id="file-upload-area">
                        <div class="file-upload-icon">📁</div>
                        <div class="file-upload-text">Click to upload or drag and drop files here</div>
                        <div class="file-upload-hint">Supported formats: PDF, JPG, PNG, DOC, DOCX (Max 10MB each)</div>
                        <input type="file" id="file-input" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                            style="display: none;">
                    </div>
                </div>

                <div id="uploaded-documents"></div>

                <div style="text-align: center; margin-top: 30px;">
                    <button type="button" id="upload-btn" class="btn" style="display: none;">Upload Documents</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const civilServiceSelect = document.getElementById('civil_service_id');
            const requirementsSection = document.getElementById('requirements-section');
            const requirementsList = document.getElementById('requirements-list');
            const fileUploadArea = document.getElementById('file-upload-area');
            const fileInput = document.getElementById('file-input');
            const uploadedDocuments = document.getElementById('uploaded-documents');
            const uploadBtn = document.getElementById('upload-btn');

            let selectedFiles = [];
            let selectedRequirements = [];

            // Load civil service requests
            loadCivilServiceRequests();

            // Handle civil service selection
            civilServiceSelect.addEventListener('change', function () {
                const civilServiceId = this.value;
                if (civilServiceId) {
                    loadRequirements(civilServiceId);
                } else {
                    requirementsSection.style.display = 'none';
                }
            });

            // Handle file upload area click
            fileUploadArea.addEventListener('click', function () {
                fileInput.click();
            });

            // Handle file input change
            fileInput.addEventListener('change', function () {
                handleFiles(this.files);
            });

            // Handle drag and drop
            fileUploadArea.addEventListener('dragover', function (e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            fileUploadArea.addEventListener('dragleave', function (e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            fileUploadArea.addEventListener('drop', function (e) {
                e.preventDefault();
                this.classList.remove('dragover');
                handleFiles(e.dataTransfer.files);
            });

            // Handle upload button click
            uploadBtn.addEventListener('click', function () {
                uploadDocuments();
            });

            function loadCivilServiceRequests() {
                // This would typically load from an API endpoint
                civilServiceSelect.innerHTML = '<option value="">Select a civil service request</option>';

                // You would implement this to fetch actual civil service requests
                // fetch('/api/civil-services/pending')
                //     .then(response => response.json())
                //     .then(data => {
                //         data.forEach(request => {
                //             const option = document.createElement('option');
                //             option.value = request.id;
                //             option.textContent = `${request.tracking_number} - ${request.service_type}`;
                //             civilServiceSelect.appendChild(option);
                //         });
                //     });
            }

            function loadRequirements(civilServiceId) {
                fetch(`/website/documents/requirements?service_type=${civilServiceId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.required_documents) {
                            displayRequirements(data.required_documents);
                            requirementsSection.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading requirements:', error);
                    });
            }

            function displayRequirements(requirements) {
                requirementsList.innerHTML = '';
                selectedRequirements = [];

                requirements.forEach((requirement, index) => {
                    const requirementItem = document.createElement('div');
                    requirementItem.className = 'requirement-item';
                    requirementItem.innerHTML = `
                        <input type="checkbox" class="requirement-checkbox" id="req-${index}" value="${requirement}">
                        <label for="req-${index}" class="requirement-name">${requirement}</label>
                    `;
                    requirementsList.appendChild(requirementItem);

                    // Add event listener for checkbox
                    const checkbox = requirementItem.querySelector('.requirement-checkbox');
                    checkbox.addEventListener('change', function () {
                        if (this.checked) {
                            selectedRequirements.push(requirement);
                        } else {
                            selectedRequirements = selectedRequirements.filter(req => req !== requirement);
                        }
                    });
                });
            }

            function handleFiles(files) {
                Array.from(files).forEach(file => {
                    if (validateFile(file)) {
                        selectedFiles.push(file);
                        displaySelectedFile(file);
                    }
                });

                if (selectedFiles.length > 0) {
                    uploadBtn.style.display = 'inline-block';
                }
            }

            function validateFile(file) {
                const maxSize = 10 * 1024 * 1024; // 10MB
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

                if (file.size > maxSize) {
                    showError(`File ${file.name} is too large. Maximum size is 10MB.`);
                    return false;
                }

                if (!allowedTypes.includes(file.type)) {
                    showError(`File ${file.name} has an unsupported format.`);
                    return false;
                }

                return true;
            }

            function displaySelectedFile(file) {
                const documentItem = document.createElement('div');
                documentItem.className = 'document-item';
                documentItem.innerHTML = `
                    <div class="document-info">
                        <div class="document-name">${file.name}</div>
                        <div class="document-details">${formatFileSize(file.size)} • ${file.type}</div>
                    </div>
                    <div class="document-actions">
                        <button type="button" class="btn btn-danger" onclick="removeFile('${file.name}')">Remove</button>
                    </div>
                `;
                uploadedDocuments.appendChild(documentItem);
            }

            function removeFile(fileName) {
                selectedFiles = selectedFiles.filter(file => file.name !== fileName);

                // Remove from display
                const documentItems = uploadedDocuments.querySelectorAll('.document-item');
                documentItems.forEach(item => {
                    const nameElement = item.querySelector('.document-name');
                    if (nameElement.textContent === fileName) {
                        item.remove();
                    }
                });

                if (selectedFiles.length === 0) {
                    uploadBtn.style.display = 'none';
                }
            }

            function uploadDocuments() {
                if (selectedFiles.length === 0) {
                    showError('Please select files to upload.');
                    return;
                }

                if (selectedRequirements.length === 0) {
                    showError('Please select at least one requirement.');
                    return;
                }

                const formData = new FormData();
                formData.append('civil_service_id', civilServiceSelect.value);

                selectedFiles.forEach((file, index) => {
                    formData.append(`documents[${index}][file]`, file);
                    formData.append(`documents[${index}][requirement_name]`, selectedRequirements[index] || selectedRequirements[0]);
                });

                fetch('/website/documents/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess('Documents uploaded successfully!');
                            selectedFiles = [];
                            selectedRequirements = [];
                            uploadedDocuments.innerHTML = '';
                            uploadBtn.style.display = 'none';
                            fileInput.value = '';
                        } else {
                            showError(data.error || 'Failed to upload documents. Please try again.');
                        }
                    })
                    .catch(error => {
                        showError('Error uploading documents. Please try again.');
                        console.error('Error:', error);
                    });
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function showError(message) {
                const errorDiv = document.getElementById('error-message');
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                document.getElementById('success-message').style.display = 'none';
            }

            function showSuccess(message) {
                const successDiv = document.getElementById('success-message');
                successDiv.textContent = message;
                successDiv.style.display = 'block';
                document.getElementById('error-message').style.display = 'none';
            }

            // Make removeFile function global
            window.removeFile = removeFile;
        });
    </script>
</body>

</html>