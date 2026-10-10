@extends('website.layouts.app')

@section('title', 'Book Appointment')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/appointment-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/website/appointment-service.css') }}">

    <div class="appointment-container">
        <div class="appointment-card">
            <div class="appointment-form">
                <h1>Book Appointment</h1>

                <!-- Progress bar -->
                <div class="progressbar">
                    <div class="progress-step active">
                        <span class="step-number">1</span>
                        <span class="progress-label">Service</span>
                    </div>
                    <div class="progress-step">
                        <span class="step-number">2</span>
                        <span class="progress-label">Time and Date</span>
                    </div>
                    <div class="progress-step">
                        <span class="step-number">3</span>
                        <span class="progress-label">Details</span>
                    </div>
                    <div class="progress-step">
                        <span class="step-number">4</span>
                        <span class="progress-label">Done</span>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="instructions-section">
                    <div class="instruction-card">
                        <div class="instruction-content">
                            <h4>How to Book an Appointment</h4>
                            <p>Select a civil registration service below. We'll guide you through the appointment booking process step by step.</p>
                        </div>
                    </div>
                </div>

                <!-- Service Selection Form -->
                <form id="serviceForm" method="POST" action="{{ route('website.appointment.select-service') }}">
                    @csrf
                    <input type="hidden" name="service_name" id="selectedServiceName">
                    <input type="hidden" name="department" id="selectedDepartment">
                    <input type="hidden" name="service_type" id="selectedServiceType">

                    <!-- Civil Registration Services Section -->
                    @if($prioritizedServiceTypes->count() > 0)
                        <div class="services-section">
                            <div class="section-header">
                                <h3 class="section-title">Civil Registration Services</h3>
                                <p class="section-description">Birth certificates, marriage certificates, and other civil documents</p>
                            </div>
                            <div class="services-grid">
                                @foreach($prioritizedServiceTypes as $serviceType)
                                    @php
                                        // Display shorter names for better UI
                                        $displayName = $serviceType->name;
                                        $description = '';
                                        $processSteps = [];
                                        
                                        if (strpos($serviceType->name, 'Delayed Regular Non-BRAP') !== false) {
                                            $displayName = 'Delayed Birth Registration (Regular)';
                                            $description = 'For delayed birth registration cases that do not qualify for the BRAP program';
                                            $processSteps = [
                                                'Fill out basic request information',
                                                'Submit request and receive tracking number',
                                                'Wait for officer to provide checklist of requirements',
                                                'Submit required documents for processing',
                                                'Request will be accepted after review',
                                                'Service will be registered and completed'
                                            ];
                                        } elseif (strpos($serviceType->name, 'Delayed BRAP') !== false) {
                                            $displayName = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
                                            $description = 'Birth Registration Assistance Program for delayed registration cases that qualify for BRAP';
                                            $processSteps = [
                                                'Fill out basic request information',
                                                'Submit request and receive tracking number',
                                                'Request will be encoded to BRAP Request List',
                                                'Officer will provide checklist of requirements',
                                                'Submit documents and wait for BRAP processing result',
                                                'After 10 calendar days from acceptance, request will be ready for approval',
                                                'Admin approves and service will be registered'
                                            ];
                                        }
                                    @endphp
                                    <div class="service-option" 
                                        data-service-name="{{ $serviceType->name }}"
                                        data-department="{{ $prioritizedService->department }}"
                                        data-service-type="{{ $serviceType->name }}"
                                        data-description="{{ $description }}"
                                        data-steps="{{ htmlspecialchars(json_encode($processSteps), ENT_QUOTES, 'UTF-8') }}"
                                        onclick="handleServiceSelection(this)">
                                        <div class="service-icon">
                                            <img src="{{ $serviceType->icon_path ? asset(ltrim($serviceType->icon_path, '/')) : ($prioritizedService->icon_path ? asset(ltrim($prioritizedService->icon_path, '/')) : asset('assets/icons/civil-services.png')) }}"
                                                alt="{{ $displayName }}" />
                                        </div>
                                        <h4 class="service-title">{{ $displayName }}</h4>
                                        @if($description)
                                            <p class="service-description">{{ $description }}</p>
                                        @endif
                                        @if(count($processSteps) > 0)
                                            <div class="service-process-preview">
                                                <span class="process-badge">{{ count($processSteps) }} Steps</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    @if($prioritizedServiceTypes->count() == 0)
                        <div class="no-services">
                            <div class="no-services-icon"></div>
                            <h4>No Services Available</h4>
                            <p>We're currently updating our services. Please check back later or contact our office directly.
                            </p>
                            <a href="tel:+63-XXX-XXX-XXXX" class="contact-btn">Call Us</a>
                        </div>
                    @endif
                </form>

                <!-- Buttons -->
                <div class="form-actions">
                    <a href="{{ route('website.homepage') }}" class="cancel-btn">Cancel</a>
                    <button type="button" id="nextBtn" class="btn-primary" onclick="submitForm()" disabled>Next</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedService = null;

        function handleServiceSelection(element) {
            const serviceName = element.getAttribute('data-service-name');
            const department = element.getAttribute('data-department');
            const serviceType = element.getAttribute('data-service-type');
            const description = element.getAttribute('data-description') || '';
            const stepsJson = element.getAttribute('data-steps');
            let processSteps = [];
            
            try {
                processSteps = stepsJson ? JSON.parse(stepsJson) : [];
            } catch (e) {
                console.error('Error parsing process steps:', e);
                processSteps = [];
            }
            
            selectService(serviceName, department, serviceType, description, processSteps, element);
        }

        function selectService(serviceName, department, serviceType, description = '', processSteps = [], selectedElement = null) {
            // For civil services, proceed directly
            selectServiceDirectly(serviceName, department, serviceType, description, processSteps, selectedElement);
        }

        function selectServiceDirectly(serviceName, department, serviceType, description = '', processSteps = [], selectedElement = null) {
            // Remove previous selection
            document.querySelectorAll('.service-option').forEach(option => {
                option.classList.remove('selected');
            });

            // Add selection to the selected option
            if (selectedElement) {
                selectedElement.classList.add('selected');
            } else {
                // Find the matching service option if element not provided
                document.querySelectorAll('.service-option').forEach(option => {
                    const optionServiceName = option.getAttribute('data-service-name');
                    if (optionServiceName === serviceName) {
                        option.classList.add('selected');
                        selectedElement = option;
                    }
                });
            }

            // Store selection
            selectedService = {
                serviceName: serviceName,
                department: department,
                serviceType: serviceType,
                description: description,
                processSteps: processSteps || []
            };

            // Update hidden inputs
            document.getElementById('selectedServiceName').value = serviceName;
            document.getElementById('selectedDepartment').value = department;
            document.getElementById('selectedServiceType').value = serviceType;

            // Save to database with correct field names
            const dbServiceData = {
                service_name: serviceName,
                department: department,
                service_type: serviceType,
                description: description
            };
            saveServiceSelection(dbServiceData);

            // Enable next button
            document.getElementById('nextBtn').disabled = false;

            // Show floating information message
            showServiceInfoMessage(serviceName, description, processSteps);
        }

        function saveServiceSelection(serviceData) {
            fetch('{{ route("website.appointment.store-service") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(serviceData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Service selection saved successfully');
                    } else {
                        console.error('Error saving service selection:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error saving service selection:', error);
                });
        }

        function submitForm() {
            if (selectedService) {
                // Store in session storage for next steps (with all fields)
                const serviceData = {
                    serviceName: selectedService.serviceName,
                    department: selectedService.department,
                    serviceType: selectedService.serviceType || selectedService.service_type,
                    description: selectedService.description || '',
                    processSteps: selectedService.processSteps || []
                };
                sessionStorage.setItem('selectedService', JSON.stringify(serviceData));
                
                console.log('Storing service in sessionStorage:', serviceData);

                // Show confirmation message
                showConfirmationMessage(selectedService.serviceName);

                // Submit form after a short delay to ensure sessionStorage is set
                setTimeout(() => {
                    document.getElementById('serviceForm').submit();
                }, 100);
            } else {
                console.error('No service selected!');
                alert('Please select a service before continuing.');
            }
        }

        function showServiceInfoMessage(serviceName, description, processSteps = []) {
            // Check if this is a civil service
            if (serviceName.includes('Delayed BRAP') || serviceName.includes('Delayed Regular Non-BRAP')) {
                showCivilServiceInfoPage(serviceName, description, processSteps);
            } else {
                showRegularServiceMessage(serviceName, description);
            }
        }

        function showCivilServiceInfoPage(serviceName, description, processSteps = []) {
            // Remove any existing message
            const existingMessage = document.querySelector('.service-info-page');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Format service name for display
            let displayServiceName = serviceName;
            if (serviceName && serviceName.includes('Delayed Regular Non-BRAP')) {
                displayServiceName = 'Delayed Birth Registration (Regular)';
            } else if (serviceName && serviceName.includes('Delayed BRAP')) {
                displayServiceName = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
            }

            // Create floating info page
            const infoPage = document.createElement('div');
            infoPage.className = 'service-info-page';
            // Build steps HTML
            let stepsHTML = '';
            if (processSteps && Array.isArray(processSteps) && processSteps.length > 0) {
                stepsHTML = processSteps.map((step, index) => `
                    <div class="step-item">
                        <span class="step-number">${index + 1}</span>
                        <span class="step-text">${step}</span>
                    </div>
                `).join('');
            } else {
                // Default process flow (4 steps without upload) - matches the progress bar
                stepsHTML = `
                    <div class="step-item">
                        <span class="step-number">1</span>
                        <span class="step-text">Select service (Current step)</span>
                    </div>
                    <div class="step-item">
                        <span class="step-number">2</span>
                        <span class="step-text">Choose your preferred date and time</span>
                    </div>
                    <div class="step-item">
                        <span class="step-number">3</span>
                        <span class="step-text">Provide your personal details</span>
                    </div>
                    <div class="step-item">
                        <span class="step-number">4</span>
                        <span class="step-text">Receive confirmation and appointment number</span>
                    </div>
                `;
            }

            infoPage.innerHTML = `
                                        <div class="info-page-content">
                                            <div class="info-page-header">
                                                <h3>Service Selected</h3>
                                                <button class="info-page-close" onclick="closeInfoPage()">×</button>
                                            </div>
                                            <div class="info-page-body">
                                                <div class="service-info-card">
                                                    <div class="service-info-details">
                                                        <h4>${displayServiceName}</h4>
                                                        <p class="service-description">${description || 'Civil registration service'}</p>
                                                    </div>
                                                </div>
                                                <div class="service-steps">
                                                    <h5>Process Flow</h5>
                                                    <div class="steps-list">
                                                        ${stepsHTML}
                                                    </div>
                                                </div>
                                                <div class="info-page-actions">
                                                    <button class="btn-continue" onclick="closeInfoPage()">Continue with Appointment</button>
                                                </div>
                                            </div>
                                        </div>
                                    `;

            document.body.appendChild(infoPage);

            // Add click outside to close
            infoPage.addEventListener('click', function (e) {
                if (e.target === infoPage) {
                    closeInfoPage();
                }
            });

            // Add escape key to close
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && document.querySelector('.service-info-page')) {
                    closeInfoPage();
                }
            });
        }

        function showRegularServiceMessage(serviceName, description) {
            // Remove any existing message
            const existingMessage = document.querySelector('.service-info-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Create floating message
            const message = document.createElement('div');
            message.className = 'service-info-message';
            message.innerHTML = `
                                            <div class="info-content">
                                                <div class="info-text">
                                                    <h5>Service Selected</h5>
                                                    <p><strong>${serviceName}</strong></p>
                                                    ${description ? `<p class="info-description">${description}</p>` : ''}
                                                    <p class="info-note">Click "Next" to continue with your appointment.</p>
                                                </div>
                                                <button class="info-close" onclick="closeInfoMessage()">×</button>
                                            </div>
                                    `;

            document.body.appendChild(message);

            // Auto-remove after 4 seconds
            setTimeout(() => {
                closeInfoMessage();
            }, 4000);
        }

        function closeInfoMessage() {
            const message = document.querySelector('.service-info-message');
            if (message) {
                message.remove();
            }
        }

        function closeInfoPage() {
            const infoPage = document.querySelector('.service-info-page');
            if (infoPage) {
                infoPage.remove();
            }
        }

        function showConfirmationMessage(serviceName) {
            // Remove any existing confirmation message
            const existingMessage = document.querySelector('.confirmation-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Create confirmation message
            const message = document.createElement('div');
            message.className = 'confirmation-message';
            message.innerHTML = `
                                                                <div class="confirmation-content">
                                                                    <div class="confirmation-icon"></div>
                                                                    <div class="confirmation-text">
                                                                        <h5>Proceeding with Appointment!</h5>
                                                                        <p>You selected: <strong>${serviceName}</strong></p>
                                                                        <p class="confirmation-note">Please wait while we prepare your appointment details...</p>
                                                                    </div>
                                                                </div>
                                                            `;

            document.body.appendChild(message);

            // Auto-close after 3 seconds
            setTimeout(() => {
                closeConfirmationMessage();
            }, 3000);
        }

        function closeConfirmationMessage() {
            const message = document.querySelector('.confirmation-message');
            if (message) {
                message.remove();
            }
        }


        // Auto-select service from query parameter
        function autoSelectServiceFromQuery() {
            const urlParams = new URLSearchParams(window.location.search);
            const serviceParam = urlParams.get('service');
            
            if (serviceParam) {
                console.log('Auto-selecting service from query parameter:', serviceParam);
                
                // Normalize the service parameter for matching
                const normalizedParam = serviceParam.trim().toLowerCase();
                
                // Find the service option that matches the query parameter
                let matchedOption = null;
                
                document.querySelectorAll('.service-option').forEach(option => {
                    const serviceName = option.getAttribute('data-service-name');
                    if (serviceName) {
                        const normalizedServiceName = serviceName.toLowerCase();
                        
                        // Check for exact matches or partial matches
                        // Handle "Delayed Regular Non-BRAP" -> matches "Delayed Regular Non-BRAP Birth Registration..."
                        // Handle "Delayed BRAP" -> matches "Delayed BRAP Birth Registration..."
                        let isMatch = false;
                        
                        if (normalizedParam === 'delayed regular non-brap') {
                            isMatch = normalizedServiceName.includes('delayed regular non-brap') || 
                                     normalizedServiceName.includes('delayed birth registration (regular)');
                        } else if (normalizedParam === 'delayed brap') {
                            isMatch = (normalizedServiceName.includes('delayed brap') && 
                                      !normalizedServiceName.includes('non-brap')) ||
                                     normalizedServiceName.includes('delayed birth registration under birth registration assistance project');
                        } else {
                            // Fallback: use includes for other cases
                            isMatch = normalizedServiceName.includes(normalizedParam);
                        }
                        
                        if (isMatch && !matchedOption) {
                            matchedOption = option;
                            console.log('Found matching service:', serviceName);
                        }
                    }
                });
                
                if (matchedOption) {
                    // Simulate click to select the service
                    setTimeout(() => {
                        handleServiceSelection(matchedOption);
                        // Add visual feedback
                        matchedOption.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        console.log('Service auto-selected successfully');
                    }, 300);
                } else {
                    console.warn('No matching service found for parameter:', serviceParam);
                }
            }
        }

        // Add hover effects and enhanced interactions
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-select service if query parameter is present
            autoSelectServiceFromQuery();

            document.querySelectorAll('.service-option').forEach(option => {
                option.addEventListener('mouseenter', function () {
                    if (!this.classList.contains('selected')) {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 6px 20px rgba(0, 147, 196, 0.15)';
                    }
                });

                option.addEventListener('mouseleave', function () {
                    if (!this.classList.contains('selected')) {
                        this.style.transform = '';
                        this.style.boxShadow = '';
                    }
                });

                // Add click animation
                option.addEventListener('click', function () {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Add smooth scrolling for better UX
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
@endsection