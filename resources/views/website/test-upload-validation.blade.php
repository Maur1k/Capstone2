@extends('website.layouts.app')

@section('title', 'Upload Validation Test')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/appointment-upload.css') }}">

    <div class="appointment-container">
        <div class="appointment-card">
            <!-- Left Section -->
            <div class="appointment-form">
                <h1>Upload Validation Test</h1>

                <!-- Test Instructions -->
                <div class="instructions-section">
                    <div class="instruction-card">
                        <div class="instruction-icon">🧪</div>
                        <div class="instruction-content">
                            <h4>Test Upload Validation</h4>
                            <p>Upload test files to see how the smart detection system matches files to requirements. Use
                                the browser console to run <code>testValidation()</code> for detailed debugging.</p>
                        </div>
                    </div>
                </div>

                <!-- Simulated Service Selection -->
                <div class="requirements-section">
                    <h3>Test Service: Delayed BRAP</h3>

                    <!-- Service-specific requirements -->
                    <div class="service-requirements" id="service-requirements">
                        <div class="service-requirements-header">
                            <div class="service-icon">📋</div>
                            <div class="service-info">
                                <h4>Delayed BRAP Requirements</h4>
                                <p>Birth Registration Assistance Project - Special program for delayed birth registration
                                </p>
                            </div>
                        </div>
                        <div class="requirements-grid service-specific-grid">
                            <div class="requirement-item service-specific" data-requirement="certificate-of-live-birth">
                                <div class="requirement-icon">📄</div>
                                <div class="requirement-content">
                                    <h5>Certificate of Live Birth (COLB) <span class="required-badge">Required</span></h5>
                                    <p>Municipal Form No.102 (filled) with notarized Affidavit of Delayed Registration on
                                        the back</p>
                                    <div class="requirement-details">
                                        <span class="copies-info">📋 4 sets</span>
                                    </div>
                                    <span class="requirement-status" id="certificate-of-live-birth-status">Not
                                        uploaded</span>
                                </div>
                            </div>
                            <div class="requirement-item service-specific" data-requirement="affidavit-of-two-witnesses">
                                <div class="requirement-icon">✍️</div>
                                <div class="requirement-content">
                                    <h5>Affidavit of Two Witnesses <span class="required-badge">Required</span></h5>
                                    <p>Affidavit of two (2) disinterested witnesses</p>
                                    <div class="requirement-details">
                                        <span class="copies-info">📋 2 originals</span>
                                    </div>
                                    <span class="requirement-status" id="affidavit-of-two-witnesses-status">Not
                                        uploaded</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General requirements -->
                    <div class="general-requirements">
                        <h4>General Requirements (All Services)</h4>
                        <div class="requirements-grid">
                            <div class="requirement-item" data-requirement="government-id">
                                <div class="requirement-icon">🆔</div>
                                <div class="requirement-content">
                                    <h5>Valid Government ID</h5>
                                    <p>Driver's License, Passport, or National ID (original + photocopy)</p>
                                    <span class="requirement-status" id="id-status">Not uploaded</span>
                                </div>
                            </div>
                            <div class="requirement-item" data-requirement="photos">
                                <div class="requirement-icon">📸</div>
                                <div class="requirement-content">
                                    <h5>2x2 Photos</h5>
                                    <p>4 pieces, recent passport-size photos</p>
                                    <span class="requirement-status" id="photos-status">Not uploaded</span>
                                </div>
                            </div>
                            <div class="requirement-item" data-requirement="psa-negative">
                                <div class="requirement-icon">📜</div>
                                <div class="requirement-content">
                                    <h5>PSA Negative Certification</h5>
                                    <p>Certificate of No Record from PSA</p>
                                    <span class="requirement-status" id="psa-status">Not uploaded</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <div class="upload-section">
                    <h3>Upload Test Files</h3>

                    <!-- File Upload Area -->
                    <div class="upload-area" id="upload-area">
                        <div class="upload-content">
                            <div class="upload-icon">📁</div>
                            <h4>Drag & Drop Files Here</h4>
                            <p>or <span class="upload-link">click to browse</span></p>
                            <div class="upload-formats">
                                <span>Supported: PDF, JPG, PNG, DOC, DOCX</span>
                                <span>Max size: 10MB per file</span>
                            </div>
                        </div>
                        <input type="file" id="file-input" name="documents[]" multiple
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: none;">
                    </div>

                    <!-- Uploaded Files List -->
                    <div class="uploaded-files" id="uploaded-files" style="display: none;">
                        <h5>Uploaded Files</h5>
                        <div class="files-list" id="files-list">
                            <!-- Files will be added here dynamically -->
                        </div>
                    </div>

                    <!-- Upload Progress -->
                    <div class="upload-progress" id="upload-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill" id="progress-fill"></div>
                        </div>
                        <span class="progress-text" id="progress-text">Uploading...</span>
                    </div>
                </div>

                <!-- Test Controls -->
                <div class="test-controls">
                    <h3>Test Controls</h3>
                    <div class="test-buttons">
                        <button onclick="testValidation()" class="btn-primary">Run Validation Test</button>
                        <button onclick="clearFiles()" class="btn-secondary">Clear All Files</button>
                        <button onclick="addTestFiles()" class="btn-secondary">Add Test Files</button>
                    </div>
                    <div class="test-info">
                        <p><strong>Test File Names:</strong></p>
                        <ul>
                            <li><code>government_id.jpg</code> - Should match Government ID</li>
                            <li><code>2x2_photo.png</code> - Should match Photos</li>
                            <li><code>psa_negative_certification.pdf</code> - Should match PSA Certification</li>
                            <li><code>birth_certificate.pdf</code> - Should match Certificate of Live Birth</li>
                            <li><code>witness_affidavit.doc</code> - Should match Affidavit of Two Witnesses</li>
                        </ul>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-actions">
                    <a href="{{ route('appointment.upload') }}" class="back-btn">Back to Upload</a>
                    <button type="button" id="next-btn" class="btn-primary" disabled>Test Complete</button>
                </div>
            </div>

            <!-- Right Image -->
            <div class="appointment-image">
                <img src="{{ asset('img/appointment_img.png') }}" alt="Municipal Hall">
            </div>
        </div>
    </div>

    <script>
        // Simulate the upload validation system
        document.addEventListener('DOMContentLoaded', function () {
            // Set up test service
            sessionStorage.setItem('selectedService', JSON.stringify({
                serviceName: 'Delayed BRAP (Birth Registration Assistance Project)',
                department: 'Civil Registry',
                serviceType: 'Birth Registration'
            }));

            const uploadArea = document.getElementById('upload-area');
            const fileInput = document.getElementById('file-input');
            const uploadedFiles = document.getElementById('uploaded-files');
            const filesList = document.getElementById('files-list');
            const uploadProgress = document.getElementById('upload-progress');
            const progressFill = document.getElementById('progress-fill');
            const progressText = document.getElementById('progress-text');
            const nextBtn = document.getElementById('next-btn');

            let uploadedFilesData = [];

            // Upload area click handler
            uploadArea.addEventListener('click', () => {
                fileInput.click();
            });

            // File input change handler
            fileInput.addEventListener('change', handleFiles);

            // Drag and drop handlers
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('drag-over');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('drag-over');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('drag-over');
                const files = e.dataTransfer.files;
                handleFiles({ target: { files } });
            });

            function handleFiles(event) {
                const files = Array.from(event.target.files);

                // Validate files
                const validFiles = files.filter(file => {
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    const allowedTypes = [
                        'application/pdf',
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ];

                    if (file.size > maxSize) {
                        showFloatingMessage(`File "${file.name}" is too large. Maximum size is 10MB.`, 'error');
                        return false;
                    }

                    if (!allowedTypes.includes(file.type)) {
                        showFloatingMessage(`File "${file.name}" has an unsupported format.`, 'error');
                        return false;
                    }

                    return true;
                });

                if (validFiles.length > 0) {
                    uploadFiles(validFiles);
                }
            }

            function uploadFiles(files) {
                showFloatingMessage(`Uploading ${files.length} file(s)...`, 'info');

                // Show progress
                uploadProgress.style.display = 'block';
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 10;
                    progressFill.style.width = progress + '%';
                    progressText.textContent = `Uploading... ${progress}%`;

                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        progressText.textContent = 'Upload complete!';

                        // Simulate file processing
                        setTimeout(() => {
                            processUploadedFiles(files);
                        }, 500);
                    }
                }, 100);
            }

            function processUploadedFiles(files) {
                files.forEach(file => {
                    const fileData = {
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        id: Date.now() + Math.random()
                    };

                    uploadedFilesData.push(fileData);
                    addFileToList(fileData);
                });

                updateUploadSummary();
                updateRequirementStatus();
                hideProgress();

                // Show detailed upload feedback
                const matchedRequirements = getMatchedRequirements();
                if (matchedRequirements.length > 0) {
                    showFloatingMessage(`Files uploaded! Matched: ${matchedRequirements.join(', ')}`, 'success');
                } else {
                    showFloatingMessage('Files uploaded successfully!', 'success');
                }
            }

            function addFileToList(fileData) {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                        <div class="file-info">
                            <div class="file-icon">${getFileIcon(fileData.type)}</div>
                            <div class="file-details">
                                <h6>${fileData.name}</h6>
                                <span class="file-size">${formatFileSize(fileData.size)}</span>
                            </div>
                        </div>
                        <button class="remove-file" onclick="removeFile('${fileData.id}')">×</button>
                    `;

                filesList.appendChild(fileItem);
                uploadedFiles.style.display = 'block';
            }

            function removeFile(fileId) {
                uploadedFilesData = uploadedFilesData.filter(file => file.id !== fileId);
                updateFileList();
                updateUploadSummary();
                updateRequirementStatus();
            }

            function updateFileList() {
                filesList.innerHTML = '';
                uploadedFilesData.forEach(file => addFileToList(file));

                if (uploadedFilesData.length === 0) {
                    uploadedFiles.style.display = 'none';
                }
            }

            function updateUploadSummary() {
                const count = uploadedFilesData.length;

                if (count > 0) {
                    nextBtn.disabled = false;
                    nextBtn.textContent = 'Test Complete';
                } else {
                    nextBtn.disabled = true;
                    nextBtn.textContent = 'Test Complete';
                }
            }

            function updateRequirementStatus() {
                // General requirements with improved detection
                const hasId = uploadedFilesData.some(file => {
                    const fileName = file.name.toLowerCase();
                    const fileType = file.type.toLowerCase();
                    return (fileType.includes('image') && (
                        fileName.includes('id') ||
                        fileName.includes('license') ||
                        fileName.includes('passport') ||
                        fileName.includes('national') ||
                        fileName.includes('government')
                    )) || fileName.includes('id') || fileName.includes('license') || fileName.includes('passport');
                });

                const hasPhotos = uploadedFilesData.some(file => {
                    const fileName = file.name.toLowerCase();
                    const fileType = file.type.toLowerCase();
                    return fileType.includes('image') && (
                        fileName.includes('photo') ||
                        fileName.includes('picture') ||
                        fileName.includes('2x2') ||
                        fileName.includes('passport') ||
                        fileName.includes('size')
                    );
                });

                const hasPsa = uploadedFilesData.some(file => {
                    const fileName = file.name.toLowerCase();
                    return fileName.includes('psa') ||
                        fileName.includes('negative') ||
                        fileName.includes('certification') ||
                        fileName.includes('no record') ||
                        fileName.includes('no-record');
                });

                updateStatus('id-status', hasId);
                updateStatus('photos-status', hasPhotos);
                updateStatus('psa-status', hasPsa);

                // Service-specific requirements
                const hasBirthCert = uploadedFilesData.some(file => {
                    const fileName = file.name.toLowerCase();
                    return fileName.includes('birth') || fileName.includes('certificate') || fileName.includes('colb');
                });

                const hasAffidavit = uploadedFilesData.some(file => {
                    const fileName = file.name.toLowerCase();
                    return fileName.includes('affidavit') || fileName.includes('witness');
                });

                updateStatus('certificate-of-live-birth-status', hasBirthCert);
                updateStatus('affidavit-of-two-witnesses-status', hasAffidavit);
            }

            function updateStatus(elementId, uploaded) {
                const element = document.getElementById(elementId);
                if (element) {
                    if (uploaded) {
                        element.textContent = 'Uploaded ✓';
                        element.className = 'requirement-status uploaded';
                        // Add visual feedback
                        const requirementItem = element.closest('.requirement-item');
                        if (requirementItem) {
                            requirementItem.classList.add('requirement-matched');
                        }
                    } else {
                        element.textContent = 'Not uploaded';
                        element.className = 'requirement-status';
                        // Remove visual feedback
                        const requirementItem = element.closest('.requirement-item');
                        if (requirementItem) {
                            requirementItem.classList.remove('requirement-matched');
                        }
                    }
                }
            }

            function hideProgress() {
                setTimeout(() => {
                    uploadProgress.style.display = 'none';
                    progressFill.style.width = '0%';
                }, 1000);
            }

            function getFileIcon(type) {
                if (type.includes('pdf')) return '📄';
                if (type.includes('image')) return '🖼️';
                if (type.includes('word')) return '📝';
                return '📁';
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function getMatchedRequirements() {
                const matched = [];

                // Check general requirements
                if (uploadedFilesData.some(file => file.name.toLowerCase().includes('id') || file.name.toLowerCase().includes('license'))) {
                    matched.push('Government ID');
                }
                if (uploadedFilesData.some(file => file.name.toLowerCase().includes('photo') || file.name.toLowerCase().includes('2x2'))) {
                    matched.push('Photos');
                }
                if (uploadedFilesData.some(file => file.name.toLowerCase().includes('psa') || file.name.toLowerCase().includes('negative'))) {
                    matched.push('PSA Certification');
                }

                // Check service-specific requirements
                if (uploadedFilesData.some(file => file.name.toLowerCase().includes('birth') || file.name.toLowerCase().includes('certificate'))) {
                    matched.push('Certificate of Live Birth');
                }
                if (uploadedFilesData.some(file => file.name.toLowerCase().includes('affidavit') || file.name.toLowerCase().includes('witness'))) {
                    matched.push('Affidavit of Two Witnesses');
                }

                return matched;
            }

            // Test functions
            window.testValidation = function () {
                console.log('=== UPLOAD VALIDATION TEST ===');
                console.log('Uploaded Files:', uploadedFilesData);
                console.log('Selected Service:', JSON.parse(sessionStorage.getItem('selectedService') || '{}'));

                const generalStatus = {
                    'Government ID': uploadedFilesData.some(file =>
                        file.name.toLowerCase().includes('id') || file.name.toLowerCase().includes('license')
                    ),
                    'Photos': uploadedFilesData.some(file =>
                        file.name.toLowerCase().includes('photo') || file.name.toLowerCase().includes('2x2')
                    ),
                    'PSA Certification': uploadedFilesData.some(file =>
                        file.name.toLowerCase().includes('psa') || file.name.toLowerCase().includes('negative')
                    )
                };

                console.log('General Requirements Status:', generalStatus);

                const serviceStatus = {
                    'Certificate of Live Birth': uploadedFilesData.some(file =>
                        file.name.toLowerCase().includes('birth') || file.name.toLowerCase().includes('certificate')
                    ),
                    'Affidavit of Two Witnesses': uploadedFilesData.some(file =>
                        file.name.toLowerCase().includes('affidavit') || file.name.toLowerCase().includes('witness')
                    )
                };

                console.log('Service-Specific Requirements Status:', serviceStatus);
                console.log('Matched Requirements:', getMatchedRequirements());

                return {
                    generalStatus,
                    serviceStatus,
                    matchedRequirements: getMatchedRequirements()
                };
            };

            window.clearFiles = function () {
                uploadedFilesData = [];
                updateFileList();
                updateUploadSummary();
                updateRequirementStatus();
                showFloatingMessage('All files cleared!', 'info');
            };

            window.addTestFiles = function () {
                const testFiles = [
                    { name: 'government_id.jpg', size: 1024000, type: 'image/jpeg' },
                    { name: '2x2_photo.png', size: 512000, type: 'image/png' },
                    { name: 'psa_negative_certification.pdf', size: 2048000, type: 'application/pdf' },
                    { name: 'birth_certificate.pdf', size: 1536000, type: 'application/pdf' },
                    { name: 'witness_affidavit.doc', size: 768000, type: 'application/msword' }
                ];

                testFiles.forEach(file => {
                    const fileData = {
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        id: Date.now() + Math.random()
                    };

                    uploadedFilesData.push(fileData);
                    addFileToList(fileData);
                });

                updateUploadSummary();
                updateRequirementStatus();
                showFloatingMessage('Test files added! Run testValidation() to see results.', 'success');
            };
        });

        function showFloatingMessage(message, type = 'info') {
            // Remove any existing floating messages
            const existingMessage = document.querySelector('.floating-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Create floating message element
            const floatingMessage = document.createElement('div');
            floatingMessage.className = `floating-message floating-message-${type}`;

            // Add icon based on type
            let icon = '';
            switch (type) {
                case 'success':
                    icon = '✅';
                    break;
                case 'error':
                    icon = '❌';
                    break;
                case 'warning':
                    icon = '⚠️';
                    break;
                default:
                    icon = 'ℹ️';
            }

            floatingMessage.innerHTML = `
                    <div class="floating-message-content">
                        <span class="floating-message-icon">${icon}</span>
                        <span class="floating-message-text">${message}</span>
                        <button class="floating-message-close" onclick="this.parentElement.parentElement.remove()">×</button>
                    </div>
                `;

            // Add to page
            document.body.appendChild(floatingMessage);

            // Animate in
            setTimeout(() => {
                floatingMessage.classList.add('show');
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (floatingMessage.parentElement) {
                    floatingMessage.classList.remove('show');
                    setTimeout(() => {
                        if (floatingMessage.parentElement) {
                            floatingMessage.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }
    </script>

    <style>
        .test-controls {
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .test-controls h3 {
            margin: 0 0 15px 0;
            color: #005f7f;
            font-size: 18px;
            font-weight: 700;
        }

        .test-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn-secondary {
            padding: 10px 20px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            background: #fff;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            border-color: #0093c4;
            color: #0093c4;
            transform: translateY(-1px);
        }

        .test-info {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .test-info p {
            margin: 0 0 10px 0;
            font-weight: 600;
            color: #333;
        }

        .test-info ul {
            margin: 0;
            padding-left: 20px;
        }

        .test-info li {
            margin-bottom: 5px;
            color: #666;
            font-size: 14px;
        }

        .test-info code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #e11d48;
        }
    </style>
@endsection

