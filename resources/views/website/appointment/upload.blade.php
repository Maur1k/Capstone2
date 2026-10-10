@extends('website.layouts.app')

@section('title', 'Book Appointment – Upload Documents')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/appointment-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/website/appointment-upload.css') }}">

    <div class="appointment-container">
        <div class="appointment-card">
            <div class="appointment-form">
                <h1>Book Appointment</h1>

                <!-- Progress bar -->
                <div class="progressbar">
                    <div class="progress-step completed">
                        <span class="checkmark">✓</span>
                        <span class="progress-label">Service</span>
                    </div>
                    <div class="progress-step completed">
                        <span class="checkmark">✓</span>
                        <span class="progress-label">Time and Date</span>
                    </div>
                    <div class="progress-step completed">
                        <span class="checkmark">✓</span>
                        <span class="progress-label">Details</span>
                    </div>
                    <div class="progress-step active">
                        <span class="step-number">4</span>
                        <span class="progress-label">Upload</span>
                    </div>
                    <div class="progress-step">
                        <span class="step-number">5</span>
                        <span class="progress-label">Done</span>
                    </div>
                </div>
                
                <!-- Progress line overlay for completed steps -->
                <style>
                    .progressbar::after {
                        content: '';
                        position: absolute;
                        top: 50%;
                        left: 20px;
                        width: calc(75% - 10px);
                        height: 3px;
                        background: #0093c4;
                        transform: translateY(-50%);
                        z-index: 0;
                        border-radius: 2px;
                    }
                </style>

                <!-- Instructions -->
                <div class="instructions-section">
                    <div class="instruction-card">
                        <div class="instruction-icon">📄</div>
                        <div class="instruction-content">
                            <h4>Upload Required Documents</h4>
                            <p>Please upload the required documents for your civil service appointment. Our officers will
                                review these documents before your appointment.</p>
                        </div>
                    </div>
                </div>

                <!-- Document Requirements -->
                <div class="requirements-section">
                    <h3>Document Requirements</h3>

                    <!-- Service-specific requirements (Optional) -->
                    <div class="service-requirements" id="service-requirements">
                        <!-- This will be populated by JavaScript based on selected service -->
                    </div>

                    <!-- General requirements -->
                    <div class="general-requirements">
                        <h4>Required for All Services</h4>
                        <div class="requirements-grid">
                            <div class="requirement-item" data-requirement="government-id">
                                <div class="requirement-icon"></div>
                                <div class="requirement-content">
                                    <h5>Valid Government ID <span class="required-badge">Required</span></h5>
                                    <p>Driver's License, Passport, or National ID (original + photocopy)</p>
                                    <span class="requirement-status" id="id-status">Not uploaded</span>
                                </div>
                            </div>
                            <div class="requirement-item" data-requirement="photos">
                                <div class="requirement-icon"></div>
                                <div class="requirement-content">
                                    <h5>2x2 Photos <span class="required-badge">Required</span></h5>
                                    <p>4 pieces, recent passport-size photos</p>
                                    <span class="requirement-status" id="photos-status">Not uploaded</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <div class="upload-section">
                    <h3>Upload Your Documents</h3>

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

                <!-- Upload Summary -->
                <div class="upload-summary" id="upload-summary" style="display: none;">
                    <div class="summary-card">
                        <h5>Upload Summary</h5>
                        <div class="summary-details">
                            <div class="summary-item">
                                <span class="summary-label">Files Uploaded:</span>
                                <span class="summary-value" id="files-count">0</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Total Size:</span>
                                <span class="summary-value" id="total-size">0 MB</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Status:</span>
                                <span class="summary-value">Ready for Review</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-actions">
                    <a href="{{ route('appointment.details') }}" class="back-btn">Back</a>
                    <button type="button" id="next-btn" class="btn-primary" disabled>Next</button>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const uploadArea = document.getElementById('upload-area');
            const fileInput = document.getElementById('file-input');
            const uploadedFiles = document.getElementById('uploaded-files');
            const filesList = document.getElementById('files-list');
            const uploadProgress = document.getElementById('upload-progress');
            const progressFill = document.getElementById('progress-fill');
            const progressText = document.getElementById('progress-text');
            const uploadSummary = document.getElementById('upload-summary');
            const filesCount = document.getElementById('files-count');
            const totalSize = document.getElementById('total-size');
            const nextBtn = document.getElementById('next-btn');
            const serviceRequirements = document.getElementById('service-requirements');

            let uploadedFilesData = [];

            // Preserve service data from backend session if not in sessionStorage
            if (!sessionStorage.getItem('selectedService')) {
                fetch('/website/appointment/service-data')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.service) {
                            const serviceData = {
                                serviceName: data.service.serviceName,
                                department: data.service.department || 'Civil Registry',
                                serviceType: data.service.serviceType || data.service.service_type || 'Civil Service',
                                description: data.service.description || ''
                            };
                            sessionStorage.setItem('selectedService', JSON.stringify(serviceData));
                            console.log('Service data loaded from backend on upload page:', serviceData);
                            // Reload requirements after setting service
                            loadServiceRequirements();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading service data:', error);
                    });
            }

            // Load service-specific requirements
            loadServiceRequirements();

            function loadServiceRequirements() {
                // Get selected service from session storage
                let selectedService = JSON.parse(sessionStorage.getItem('selectedService') || '{}');

                                // Debug logging
                    console.log('Selected service from session storage:', selectedService);
                    console.log('Service name:', selectedService.serviceName);

                    // If no service in session storage, try to get from database session
                    if (!selectedService.serviceName) {
                        fetch('/website/appointment/service-data')
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.service) {
                                    selectedService = data.service;
                                    console.log('Retrieved service from database:', selectedService);
                                    loadServiceRequirementsFromData(selectedService);
                                } else {
                                    showNoServiceMessage();
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching service data:', error);
                                showNoServiceMessage();
                            });
                    } else {
                        loadServiceRequirementsFromData(selectedService);
                    }
                }

                function loadServiceRequirementsFromData(selectedService) {
                    if (selectedService.serviceName) {
                        let requirements = getServiceRequirements(selectedService.serviceName);

                        // If no exact match, try partial matching
                        if (!requirements) {
                            requirements = getServiceRequirementsByPartialMatch(selectedService.serviceName);
                        }

                        console.log('Found requirements for:', selectedService.serviceName, requirements);

                        if (requirements) {
                            displayServiceRequirements(requirements);
                        } else {
                            showServiceNotConfiguredMessage(selectedService.serviceName);
                        }
                    } else {
                        showNoServiceMessage();
                    }
                }

                function getServiceRequirementsByPartialMatch(serviceName) {
                    const allRequirements = {
                        'Delayed BRAP Birth Registration Assistance Project': {
                            title: 'Delayed BRAP Requirements',
                            icon: '📋',
                            description: 'Birth Registration Assistance Project - Special program for delayed birth registration',
                            documents: [
                                {
                                    name: 'Certificate of Live Birth (COLB)',
                                    description: 'Municipal Form No.102 (filled) with notarized Affidavit of Delayed Registration on the back',
                                    copies: '4 sets',
                                    icon: '📄',
                                    required: false
                                },
                                {
                                    name: 'Attachments for Muslims/Indigenous',
                                    description: 'Additional documents if applicable to your case',
                                    copies: '4 sets',
                                    icon: '📋',
                                    required: false
                                },
                                {
                                    name: 'Form 1B or Form 1C',
                                    description: 'Form 1B (birth not available) or Form 1C (birth destroyed)',
                                    copies: '1 original',
                                    icon: '📝',
                                    required: false
                                },
                                {
                                    name: 'Affidavit of Two Witnesses',
                                    description: 'Affidavit of two (2) disinterested witnesses',
                                    copies: '2 originals',
                                    icon: '✍️',
                                    required: false
                                }
                            ]
                        },
                        'Delayed Regular Non-BRAP Birth Registration Assistance Project': {
                            title: 'Delayed Regular Requirements',
                            icon: '📋',
                            description: 'Regular delayed registration process with additional proof requirements',
                            documents: [
                                {
                                    name: 'Municipal Form No.102',
                                    description: 'Filled form with notarized Affidavit of Delayed Registration',
                                    copies: '4 sets',
                                    icon: '📄',
                                    required: false
                                },
                                {
                                    name: 'Affidavit of Two Witnesses',
                                    description: 'Affidavit of two (2) disinterested witnesses',
                                    copies: '2 originals',
                                    icon: '✍️',
                                    required: false
                                },
                                {
                                    name: 'Marriage Certificate of Parents',
                                    description: 'If parents are married, provide marriage certificate',
                                    copies: '1 original + 1 photocopy',
                                    icon: '💒',
                                    required: false
                                },
                                {
                                    name: 'Supporting Documents',
                                    description: 'Additional documents to support the delayed registration',
                                    copies: 'As needed',
                                    icon: '📎',
                                    required: false
                                }
                            ]
                        }
                    };

                    // Try partial matching
                    for (const [key, value] of Object.entries(allRequirements)) {
                        if (serviceName.includes('BRAP') && key.includes('BRAP')) {
                            return value;
                        }
                        if (serviceName.includes('Regular') && key.includes('Regular')) {
                            return value;
                        }
                        if (serviceName.includes('Delayed') && key.includes('Delayed')) {
                            return value;
                        }
                    }

                    return null;
                }

                function showServiceNotConfiguredMessage(serviceName) {
                    serviceRequirements.innerHTML = `
                        <div class="no-service-message">
                            <div class="no-service-icon">❓</div>
                            <h4>Service Requirements Not Found</h4>
                            <p>Document requirements for this service are not yet configured.</p>
                            <p><strong>Service:</strong> ${serviceName}</p>
                        </div>
                    `;
                }

                function showNoServiceMessage() {
                    // Debug: Check what's in session storage
                    console.log('No service found. Session storage contents:');
                    for (let i = 0; i < sessionStorage.length; i++) {
                        const key = sessionStorage.key(i);
                        console.log(key + ':', sessionStorage.getItem(key));
                    }

                    // Default message if no service selected
                    serviceRequirements.innerHTML = `
                                        <div class="no-service-message">
                                            <div class="no-service-icon">⚠️</div>
                                            <h4>No Service Selected</h4>
                                            <p>Please go back and select a civil service to see specific document requirements.</p>
                                        </div>
                                    `;
                }

                function getServiceRequirements(serviceName) {
                    const requirements = {
                        'Delayed BRAP Birth Registration Assistance Project': {
                            title: 'Delayed BRAP Requirements',
                            icon: '📋',
                            description: 'Birth Registration Assistance Project - Special program for delayed birth registration',
                            documents: [
                                {
                                    name: 'Certificate of Live Birth (COLB)',
                                    description: 'Municipal Form No.102 (filled) with notarized Affidavit of Delayed Registration on the back',
                                    copies: '4 sets',
                                    icon: '📄',
                                    required: false
                                },
                                {
                                    name: 'Attachments for Muslims/Indigenous',
                                    description: 'Additional documents if applicable to your case',
                                    copies: '4 sets',
                                    icon: '📋',
                                    required: false
                                },
                                {
                                    name: 'Form 1B or Form 1C',
                                    description: 'Form 1B (birth not available) or Form 1C (birth destroyed)',
                                    copies: '1 original',
                                    icon: '📝',
                                    required: false
                                },
                                {
                                    name: 'Affidavit of Two Witnesses',
                                    description: 'Affidavit of two (2) disinterested witnesses',
                                    copies: '2 originals',
                                    icon: '✍️',
                                    required: false
                                }
                            ]
                        },
                        'Delayed Regular Non-BRAP Birth Registration Assistance Project': {
                            title: 'Delayed Regular Requirements',
                            icon: '📋',
                            description: 'Regular delayed registration process with additional proof requirements',
                            documents: [
                                {
                                    name: 'Municipal Form No.102',
                                    description: 'Filled form with notarized Affidavit of Delayed Registration',
                                    copies: '4 sets',
                                    icon: '📄',
                                    required: false
                                },
                                {
                                    name: 'Affidavit of Two Witnesses',
                                    description: 'Affidavit of two (2) disinterested witnesses',
                                    copies: '2 originals',
                                    icon: '✍️',
                                    required: false
                                },
                                {
                                    name: 'Marriage Certificate of Parents',
                                    description: 'If parents are married, provide marriage certificate',
                                    copies: '1 original + 1 photocopy',
                                    icon: '💒',
                                    required: false
                                },
                                {
                                    name: 'Supporting Documents',
                                    description: 'Additional documents to support the delayed registration',
                                    copies: 'As needed',
                                    icon: '📎',
                                    required: false
                                }
                            ]
                        },
                        'Delayed BRAP': {
                            title: 'Delayed BRAP Requirements',
                            icon: '📋',
                            description: 'Birth Registration Assistance Project - Special program for delayed birth registration',
                            documents: [
                                {
                                    name: 'Certificate of Live Birth (COLB)',
                                    description: 'Municipal Form No.102 (filled) with notarized Affidavit of Delayed Registration on the back',
                                    copies: '4 sets',
                                    icon: '📄',
                                    required: false
                                },
                                {
                                    name: 'Attachments for Muslims/Indigenous',
                                    description: 'Additional documents if applicable to your case',
                                    copies: '4 sets',
                                    icon: '📋',
                                    required: false
                                },
                                {
                                    name: 'Form 1B or Form 1C',
                                    description: 'Form 1B (birth not available) or Form 1C (birth destroyed)',
                                    copies: '1 original',
                                    icon: '📝',
                                    required: false
                                },
                                {
                                    name: 'Affidavit of Two Witnesses',
                                    description: 'Affidavit of two (2) disinterested witnesses',
                                    copies: '2 originals',
                                    icon: '✍️',
                                    required: false
                                }
                            ]
                        },
                        'Delayed BRAP (Birth Registration Assistance Project)': {
                            title: 'Delayed BRAP Requirements',
                            icon: '📋',
                            description: 'Birth Registration Assistance Project - Special program for delayed birth registration',
                            documents: [
                                {
                                    name: 'Certificate of Live Birth (COLB)',
                                    description: 'Municipal Form No.102 (filled) with notarized Affidavit of Delayed Registration on the back',
                                    copies: '4 sets',
                                    icon: '📄',
                                    required: false
                                },
                                {
                                    name: 'Attachments for Muslims/Indigenous',
                                    description: 'Additional documents if applicable to your case',
                                    copies: '4 sets',
                                    icon: '📋',
                                    required: false
                                },
                                {
                                    name: 'Form 1B or Form 1C',
                                    description: 'Form 1B (birth not available) or Form 1C (birth destroyed)',
                                    copies: '1 original',
                                    icon: '📝',
                                    required: false
                                },
                                {
                                    name: 'Affidavit of Two Witnesses',
                                    description: 'Affidavit of two (2) disinterested witnesses',
                                    copies: '2 originals',
                                    icon: '✍️',
                                    required: false
                                }
                            ]
                        },
                        'Delayed Regular Non-BRAP': {
                            title: 'Delayed Regular Requirements',
                            icon: '📋',
                            description: 'Regular delayed registration process with additional proof requirements',
                            documents: [
                                {
                                    name: 'Municipal Form No.102',
                                    description: 'Filled form with notarized Affidavit of Delayed Registration',
                                    copies: '4 sets',
                                    icon: '📄',
                                    required: false
                                },
                                {
                                    name: 'Affidavit of Two Witnesses',
                                    description: 'Affidavit of two (2) disinterested witnesses',
                                    copies: '2 originals',
                                    icon: '✍️',
                                    required: false
                                },
                                {
                                    name: 'Marriage Certificate of Parents',
                                    description: 'If parents are married, provide marriage certificate',
                                    copies: '1 original + 1 photocopy',
                                    icon: '💒',
                                    required: false
                                },
                                {
                                    name: 'Supporting Documents',
                                    description: 'Additional documents to support the delayed registration',
                                    copies: 'As needed',
                                    icon: '📎',
                                    required: false
                                }
                            ]
                        },
                        'Delayed Regular (Non-BRAP)': {
                            title: 'Delayed Regular Requirements',
                            icon: '📋',
                            description: 'Regular delayed registration process with additional proof requirements',
                            documents: [
                                {
                                    name: 'Municipal Form No.102',
                                    description: 'Filled form with notarized Affidavit of Delayed Registration',
                                    copies: '4 sets',
                                    icon: '📄',
                                    required: false
                                },
                                {
                                    name: 'Affidavit of Two Witnesses',
                                    description: 'Affidavit of two disinterested persons',
                                    copies: '2 originals',
                                    icon: '✍️',
                                    required: false
                                },
                                {
                                    name: 'Marriage Certificate of Parents',
                                    description: 'If required for your case',
                                    copies: '1 original + photocopy',
                                    icon: '💒',
                                    required: false
                                },
                                {
                                    name: 'Supporting Documents',
                                    description: 'Any other documents explaining the delay',
                                    copies: 'As needed',
                                    icon: '📎',
                                    required: false
                                }
                            ]
                        }
                    };

                    return requirements[serviceName] || null;
                }

                function displayServiceRequirements(requirements) {
                    if (!requirements) {
                        serviceRequirements.innerHTML = `
                                                <div class="no-service-message">
                                                    <div class="no-service-icon">❓</div>
                                                    <h4>Service Requirements Not Found</h4>
                                                    <p>Document requirements for this service are not yet configured.</p>
                                                </div>
                                            `;
                        return;
                    }

                    // All service-specific requirements are now optional
                    const documentsHtml = requirements.documents.map(doc => `
                                            <div class="requirement-item service-specific optional" data-requirement="${doc.name.toLowerCase().replace(/\s+/g, '-')}">
                                                <div class="requirement-icon">${doc.icon}</div>
                                                <div class="requirement-content">
                                                    <h5>${doc.name} <span class="optional-badge">Optional</span></h5>
                                                    <p>${doc.description}</p>
                                                    <div class="requirement-details">
                                                        <span class="copies-info">📋 ${doc.copies}</span>
                                                    </div>
                                                    <span class="requirement-status" id="${doc.name.toLowerCase().replace(/\s+/g, '-')}-status">Not uploaded</span>
                                                </div>
                                            </div>
                                        `).join('');

                    serviceRequirements.innerHTML = `
                                            <div class="service-requirements-header">
                                                <div class="service-icon">${requirements.icon}</div>
                                                <div class="service-info">
                                                    <h4>Optional Requirements</h4>
                                                    <p>These documents can be submitted on-site during your appointment. Uploading them now is optional.</p>
                                                </div>
                                            </div>
                                            <div class="requirements-grid service-specific-grid">
                                                ${documentsHtml}
                                            </div>
                                        `;
                }

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
                            id: Date.now() + Math.random(),
                            file: file  // Store the actual file object
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

                function getMatchedRequirements() {
                    const matched = [];
                    const selectedService = JSON.parse(sessionStorage.getItem('selectedService') || '{}');

                    // Check general requirements (only required ones)
                    if (uploadedFilesData.some(file => file.name.toLowerCase().includes('id') || file.name.toLowerCase().includes('license'))) {
                        matched.push('Government ID');
                    }
                    if (uploadedFilesData.some(file => file.name.toLowerCase().includes('photo') || file.name.toLowerCase().includes('2x2'))) {
                        matched.push('Photos');
                    }

                    // Check service-specific requirements
                    if (selectedService.serviceName) {
                        const requirements = getServiceRequirements(selectedService.serviceName);
                        if (requirements) {
                            requirements.documents.forEach(doc => {
                                if (checkDocumentUpload(doc.name, doc.description)) {
                                    matched.push(doc.name);
                                }
                            });
                        }
                    }

                    return matched;
                }

                function addFileToList(fileData) {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.setAttribute('data-file-id', fileData.id);
                    
                    // Escape HTML to prevent XSS
                    const fileName = escapeHtml(fileData.name);
                    const fileIcon = getFileIcon(fileData.type);
                    const fileSize = formatFileSize(fileData.size);
                    
                    fileItem.innerHTML = `
                        <div class="file-info">
                            <div class="file-icon">${fileIcon}</div>
                            <div class="file-details">
                                <h6>${fileName}</h6>
                                <span class="file-size">${fileSize}</span>
                            </div>
                        </div>
                        <button class="remove-file" type="button" data-file-id="${fileData.id}">×</button>
                    `;

                    // Add click event listener to remove button
                    const removeBtn = fileItem.querySelector('.remove-file');
                    removeBtn.addEventListener('click', function() {
                        removeFile(fileData.id);
                    });

                    filesList.appendChild(fileItem);
                    uploadedFiles.style.display = 'block';
                }

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Make removeFile globally accessible
                window.removeFile = function(fileId) {
                    // Convert fileId to string for comparison
                    const fileIdStr = String(fileId);
                    
                    // Find the file to remove
                    const fileIndex = uploadedFilesData.findIndex(file => String(file.id) === fileIdStr);
                    if (fileIndex === -1) {
                        console.warn('File not found:', fileId);
                        return;
                    }

                    const removedFile = uploadedFilesData[fileIndex];
                    
                    // Remove the file
                    uploadedFilesData.splice(fileIndex, 1);
                    
                    // Update UI
                    updateFileList();
                    updateUploadSummary();
                    updateRequirementStatus();
                    
                    // Show feedback
                    showFloatingMessage(`File "${removedFile.name}" removed successfully`, 'info');
                };

                function updateFileList() {
                    filesList.innerHTML = '';
                    uploadedFilesData.forEach(file => addFileToList(file));

                    if (uploadedFilesData.length === 0) {
                        uploadedFiles.style.display = 'none';
                    }
                }

                function updateUploadSummary() {
                    const count = uploadedFilesData.length;
                    const totalSizeBytes = uploadedFilesData.reduce((sum, file) => sum + file.size, 0);

                    filesCount.textContent = count;
                    totalSize.textContent = formatFileSize(totalSizeBytes);

                    if (count > 0) {
                        uploadSummary.style.display = 'block';
                    } else {
                        uploadSummary.style.display = 'none';
                    }

                    // Update button state based on required documents only
                    updateRequirementStatus();
                }

                function updateRequirementStatus() {
                    // Only General Requirements (ID and Photos) are required
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

                    // Update required status indicators
                    updateStatus('id-status', hasId);
                    updateStatus('photos-status', hasPhotos);

                    // Service-specific requirements (all optional - just for display)
                    const selectedService = JSON.parse(sessionStorage.getItem('selectedService') || '{}');
                    if (selectedService.serviceName) {
                        const requirements = getServiceRequirements(selectedService.serviceName);
                        if (requirements) {
                            requirements.documents.forEach(doc => {
                                const requirementId = doc.name.toLowerCase().replace(/\s+/g, '-') + '-status';
                                const hasDocument = checkDocumentUpload(doc.name, doc.description);
                                updateStatus(requirementId, hasDocument);
                            });
                        }
                    }

                    // Update Next button state - only check required (ID + Photos)
                    updateNextButtonState(hasId, hasPhotos);
                }

                function updateNextButtonState(hasId, hasPhotos) {
                    // Only enable Next button if both required documents are uploaded
                    if (hasId && hasPhotos) {
                        nextBtn.disabled = false;
                        nextBtn.textContent = 'Continue to Confirmation';
                    } else {
                        nextBtn.disabled = true;
                        nextBtn.textContent = 'Next';
                    }
                }

                function checkDocumentUpload(documentName, description) {
                    // Special handling for "Affidavit of Two Witnesses"
                    if (documentName.toLowerCase().includes('affidavit of two witnesses')) {
                        return checkAffidavitOfTwoWitnesses();
                    }

                    // Create comprehensive keyword list for better matching
                    const keywords = [
                        ...documentName.toLowerCase().split(' ').filter(word => word.length > 2),
                        ...description.toLowerCase().split(' ').filter(word => word.length > 3)
                    ];

                    // Add specific document type keywords
                    const documentKeywords = {
                        'certificate of live birth': ['colb', 'birth', 'certificate', 'live', 'municipal', 'form', '102'],
                        'attachments for muslims': ['muslim', 'indigenous', 'attachment', 'cultural'],
                        'form 1b': ['form1b', 'form 1b', 'birth not available', 'unavailable'],
                        'form 1c': ['form1c', 'form 1c', 'birth destroyed', 'destroyed'],
                        'affidavit of two witnesses': ['affidavit', 'witness', 'witnesses', 'disinterested', 'two', '2', 'sworn', 'notarized', 'statement', 'declaration'],
                        'municipal form no.102': ['municipal', 'form', '102', 'no.102'],
                        'marriage certificate of parents': ['marriage', 'certificate', 'parents', 'parent'],
                        'supporting documents': ['supporting', 'document', 'explanation', 'delay'],
                        'valid government id': ['id', 'government', 'valid', 'license', 'passport', 'national'],
                        '2x2 photos': ['photo', 'picture', '2x2', 'passport', 'size']
                    };

                    // Get specific keywords for this document type
                    const specificKeywords = documentKeywords[documentName.toLowerCase()] || [];
                    const allKeywords = [...keywords, ...specificKeywords];

                    return uploadedFilesData.some(file => {
                        const fileName = file.name.toLowerCase();
                        const fileType = file.type.toLowerCase();

                        // Check filename matches
                        const nameMatch = allKeywords.some(keyword => fileName.includes(keyword));

                        // Check file type matches (for photos, documents, etc.)
                        let typeMatch = false;
                        if (documentName.toLowerCase().includes('photo') || documentName.toLowerCase().includes('2x2')) {
                            typeMatch = fileType.includes('image');
                        } else if (documentName.toLowerCase().includes('certificate') || documentName.toLowerCase().includes('form')) {
                            typeMatch = fileType.includes('pdf') || fileType.includes('image') || fileType.includes('word');
                        } else if (documentName.toLowerCase().includes('affidavit')) {
                            typeMatch = fileType.includes('pdf') || fileType.includes('word') || fileType.includes('document');
                        }

                        return nameMatch || typeMatch;
                    });
                }

                function checkAffidavitOfTwoWitnesses() {
                    // Look for files that contain affidavit-related keywords
                    const affidavitKeywords = ['affidavit', 'witness', 'witnesses', 'disinterested', 'two', '2', 'sworn', 'notarized', 'statement', 'declaration'];
                    
                    // Check if any uploaded file matches affidavit criteria
                    const affidavitFiles = uploadedFilesData.filter(file => {
                        const fileName = file.name.toLowerCase();
                        const fileType = file.type.toLowerCase();
                        
                        // Must be a document type
                        const isDocument = fileType.includes('pdf') || fileType.includes('word') || fileType.includes('document');
                        
                        // Must contain affidavit-related keywords
                        const hasAffidavitKeywords = affidavitKeywords.some(keyword => fileName.includes(keyword));
                        
                        return isDocument && hasAffidavitKeywords;
                    });

                    // For "Affidavit of Two Witnesses", we need at least 2 files (2 originals)
                    // But for validation purposes, we'll accept 1 file as a match
                    // The actual requirement of "2 originals" should be handled in the UI display
                    return affidavitFiles.length > 0;
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

                // Next button handler
                nextBtn.addEventListener('click', function () {
                    // Validate required documents (ID + Photos only)
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

                    if (!hasId || !hasPhotos) {
                        const missing = [];
                        if (!hasId) missing.push('Valid Government ID');
                        if (!hasPhotos) missing.push('2x2 Photos');
                        showFloatingMessage(`Please upload required documents: ${missing.join(' and ')}`, 'warning');
                        return;
                    }

                    if (!nextBtn.disabled && uploadedFilesData.length > 0) {
                        // Store uploaded files data
                        sessionStorage.setItem('uploadedFiles', JSON.stringify(uploadedFilesData));

                        // Create final appointment with documents
                        createAppointment();
                    } else {
                        showFloatingMessage('Please upload required documents before continuing.', 'warning');
                    }
                });

                function createAppointment() {
                    // Get all data from session storage
                    const selectedService = JSON.parse(sessionStorage.getItem('selectedService') || '{}');
                    const appointmentData = JSON.parse(sessionStorage.getItem('appointmentData') || '{}');
                    const personalDetails = JSON.parse(sessionStorage.getItem('personalDetails') || '{}');

                    // Create FormData for file upload
                    const formData = new FormData();

                    // Add files from uploadedFilesData array (not session storage)
                    console.log('Uploading files:', uploadedFilesData);
                    console.log('Uploaded files data length:', uploadedFilesData.length);
                    
                    uploadedFilesData.forEach((fileData, index) => {
                        console.log(`File ${index}:`, {
                            name: fileData.name,
                            hasFile: !!fileData.file,
                            fileType: typeof fileData.file
                        });
                        
                        if (fileData.file) {
                            formData.append(`documents[${index}]`, fileData.file);
                            console.log(`Added file ${index}:`, fileData.name);
                        } else {
                            console.error(`File ${index} missing file object:`, fileData);
                        }
                    });

                    // Validate required documents before proceeding
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

                    if (!hasId || !hasPhotos) {
                        const missing = [];
                        if (!hasId) missing.push('Valid Government ID');
                        if (!hasPhotos) missing.push('2x2 Photos');
                        showFloatingMessage(`Please upload required documents: ${missing.join(' and ')}`, 'error');
                        return;
                    }

                    // Debug: Log all data being sent
                    console.log('=== APPOINTMENT CREATION DEBUG ===');
                    console.log('Selected Service:', selectedService);
                    console.log('Appointment Data:', appointmentData);
                    console.log('Personal Details:', personalDetails);
                    console.log('Uploaded Files Data:', uploadedFilesData);
                    console.log('FormData entries:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    // Show loading message
                    showFloatingMessage('Creating your appointment... Please wait.', 'info');

                    fetch('{{ route("website.appointment.store-documents") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                        .then(response => {
                            console.log('Response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            if (data.success) {
                                // Get service name from multiple sources
                                const serviceName = data.appointment.service_name || 
                                                   selectedService.serviceName || 
                                                   selectedService.service_type ||
                                                   'Service Request';
                                
                                console.log('Creating appointment summary with service:', serviceName);
                                
                                // Store appointment data for the done page
                                const appointmentSummary = {
                                    service: serviceName,
                                    date: data.appointment.appointment_date || appointmentData.date || appointmentData.appointment_date || null,
                                    time: data.appointment.appointment_time || appointmentData.time || appointmentData.appointment_time || null,
                                    fullName: data.appointment.full_name || personalDetails.fullName || personalDetails.full_name || 'N/A',
                                    contactNumber: data.appointment.contact_number || personalDetails.contactNumber || personalDetails.contact_number || 'N/A',
                                    documentsCount: data.appointment.documents_count || uploadedFilesData.length,
                                    appointmentNumber: data.appointment.appointment_number || 'N/A'
                                };
                                
                                console.log('Appointment summary:', appointmentSummary);

                                // Store in session storage for done page
                                sessionStorage.setItem('appointmentSummary', JSON.stringify(appointmentSummary));
                                
                                // Also keep the original data in case
                                if (!sessionStorage.getItem('selectedService')) {
                                    sessionStorage.setItem('selectedService', JSON.stringify(selectedService));
                                }
                                if (!sessionStorage.getItem('appointmentData')) {
                                    sessionStorage.setItem('appointmentData', JSON.stringify(appointmentData));
                                }
                                if (!sessionStorage.getItem('personalDetails')) {
                                    sessionStorage.setItem('personalDetails', JSON.stringify(personalDetails));
                                }
                                if (!sessionStorage.getItem('uploadedFiles')) {
                                    sessionStorage.setItem('uploadedFiles', JSON.stringify(uploadedFilesData.map(f => ({ name: f.name, size: f.size }))));
                                }

                                // Show success message with SMS status
                                let successMsg = 'Appointment created successfully!';
                                if (data.sms_sent) {
                                    successMsg += ' SMS confirmation sent to your phone.';
                                }
                                successMsg += ' Redirecting to confirmation...';
                                showFloatingMessage(successMsg, 'success');

                                // Store SMS status for done page
                                sessionStorage.setItem('smsSent', data.sms_sent ? 'true' : 'false');

                                // Redirect to done page with appointment number
                                setTimeout(() => {
                                    const url = new URL('{{ route("appointment.done") }}', window.location.origin);
                                    url.searchParams.set('appointment', data.appointment.appointment_number);
                                    if (data.sms_sent) {
                                        url.searchParams.set('sms', 'sent');
                                    }
                                    window.location.href = url.toString();
                                }, 1500);
                            } else {
                                console.error('Appointment creation failed:', data);
                                showFloatingMessage('Error creating appointment: ' + (data.message || 'Unknown error'), 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error creating appointment:', error);
                            showFloatingMessage('Error creating appointment. Please try again.', 'error');
                        });
                }

                // Debug function to test validation (can be called from browser console)
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
                        )
                    };

                    console.log('General Requirements Status:', generalStatus);

                    const selectedService = JSON.parse(sessionStorage.getItem('selectedService') || '{}');
                    if (selectedService.serviceName) {
                        const requirements = getServiceRequirements(selectedService.serviceName);
                        if (requirements) {
                            const serviceStatus = {};
                            requirements.documents.forEach(doc => {
                                const matched = checkDocumentUpload(doc.name, doc.description);
                                serviceStatus[doc.name] = matched;
                                
                                // Special test for Affidavit of Two Witnesses
                                if (doc.name.toLowerCase().includes('affidavit of two witnesses')) {
                                    console.log('  Affidavit validation details:');
                                    const affidavitFiles = uploadedFilesData.filter(file => {
                                        const fileName = file.name.toLowerCase();
                                        const fileType = file.type.toLowerCase();
                                        const isDocument = fileType.includes('pdf') || fileType.includes('word') || fileType.includes('document');
                                        const hasAffidavitKeywords = ['affidavit', 'witness', 'witnesses', 'disinterested', 'two', '2', 'sworn', 'notarized', 'statement', 'declaration'].some(keyword => fileName.includes(keyword));
                                        return isDocument && hasAffidavitKeywords;
                                    });
                                    console.log('  - Affidavit files found:', affidavitFiles.length);
                                    console.log('  - Files:', affidavitFiles.map(f => f.name));
                                }
                            });
                            console.log('Service-Specific Requirements Status:', serviceStatus);
                        }
                    }

                    console.log('Matched Requirements:', getMatchedRequirements());
                    console.log('Validation Summary:', getValidationSummary());
                };

                function getValidationSummary() {
                    const matched = getMatchedRequirements();
                    const total = uploadedFilesData.length;
                    return `Uploaded ${total} file(s), matched ${matched.length} requirement(s)`;
                }
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
@endsection