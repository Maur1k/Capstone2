<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>{{ $serviceName ?? 'Service Request' }} - Click2Serve Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/kiosk-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing-kiosk.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @include('home.landing')
    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <!-- Header with back button -->
                <div class="header">
                    <button class="back-button" onclick="goBack()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <h1 class="page-title">
                        @php
                            // Format display names for civil registry services
                            $displaySelectedType = $selectedType ?? '';
                            if (strpos($displaySelectedType, 'Delayed Regular Non-BRAP') !== false) {
                                $displaySelectedType = 'Delayed Birth Registration (Regular)';
                            } elseif (strpos($displaySelectedType, 'Delayed BRAP') !== false) {
                                $displaySelectedType = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
                            }
                            // Display formatted service type or department name for better UI
                            $displayName = !empty($displaySelectedType) ? $displaySelectedType : ($department ?? 'Service Request');
                        @endphp
                        {{ $displayName }}
                    </h1>
                </div>

                <!-- Main content -->
                <div class="main-content">
                    <div class="form-container kiosk-form-container">
                        <div class="form-header">
                            <div class="step-indicator">
                                <span class="step-badge">Step 1</span>
                            </div>
                            @php
                                // Dynamic header based on service type and selected type
                                $headerMessage = 'Please enter your full name and phone number to proceed with your request.';

                                // Format display names for civil registry services
                                $formattedServiceType = $selectedType ?? $serviceName ?? 'service';
                                if (strpos($formattedServiceType, 'Delayed Regular Non-BRAP') !== false) {
                                    $formattedServiceType = 'Delayed Birth Registration (Regular)';
                                } elseif (strpos($formattedServiceType, 'Delayed BRAP') !== false) {
                                    $formattedServiceType = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
                                }

                                if (isset($selectedType) && !empty($selectedType)) {
                                    $headerMessage = "To proceed with {$formattedServiceType}, please enter your full name and phone number below.";
                                } elseif (isset($serviceName)) {
                                    if (strpos($serviceName, 'Birth') !== false || strpos($serviceName, 'COLB') !== false) {
                                        $headerMessage = 'To request a birth certificate, please enter your full name and phone number below.';
                                    } elseif (strpos($serviceName, 'Marriage') !== false) {
                                        $headerMessage = 'For marriage certificate services, please enter your full name and phone number below.';
                                    } elseif (strpos($serviceName, 'Business') !== false || strpos($serviceName, 'BPLO') !== false) {
                                        $headerMessage = 'To apply for business permits, please enter your full name and phone number below.';
                                    } elseif (strpos($serviceName, 'Engineering') !== false) {
                                        $headerMessage = 'For engineering services, please enter your full name and phone number below.';
                                    } elseif (strpos($serviceName, 'Veterinary') !== false) {
                                        $headerMessage = 'To access veterinary services, please enter your full name and phone number below.';
                                    } elseif (strpos($serviceName, 'Transportation') !== false || strpos($serviceName, 'CTRU') !== false) {
                                        $headerMessage = 'For transportation regulation services, please enter your full name and phone number below.';
                                    } elseif (strpos($serviceName, 'Social') !== false || strpos($serviceName, 'Welfare') !== false) {
                                        $headerMessage = 'To access social welfare services, please enter your full name and phone number below.';
                                    }
                                }
                            @endphp
                            <h2>{{ $headerMessage }}</h2>
                        </div>

                        <form id="kioskForm" class="kiosk-form">
                            @csrf
                            <input type="hidden" name="service_name" value="{{ $serviceName }}">
                            <input type="hidden" name="department" value="{{ $department }}">
                            <input type="hidden" name="service_type" value="{{ $serviceType }}">
                            <input type="hidden" name="selected_type" value="{{ $selectedType ?? '' }}">

                            <div class="form-group">
                                <label for="full_name" class="form-label">
                                    Full Name <span class="required-asterisk">*</span>
                                </label>
                                <div class="input-container">
                                    <input type="text" id="full_name" name="full_name" class="form-input"
                                        placeholder="e.g., Juan Dela Cruz" required autocomplete="name">
                                    <div class="input-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="validation-message" id="full_name_error"></div>
                                <div class="input-hint">Enter your complete name as it appears on your ID</div>
                            </div>

                            <div class="form-group">
                                <label for="contact_number" class="form-label">
                                    Mobile Number <span class="required-asterisk">*</span>
                                </label>
                                <div class="input-container">
                                    <input type="tel" id="contact_number" name="contact_number" class="form-input"
                                        placeholder="e.g., 09123456789" required autocomplete="tel">
                                    <div class="input-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27099 2.11999 4.18C2.095 3.90347 2.12787 3.62476 2.21649 3.36162C2.30512 3.09849 2.44756 2.85669 2.63476 2.65162C2.82196 2.44655 3.0498 2.28271 3.30379 2.17052C3.55777 2.05833 3.83233 2.00026 4.10999 2H7.10999C7.59531 1.99522 8.06679 2.16708 8.43376 2.48353C8.80073 2.79999 9.03996 3.23945 9.10999 3.72C9.23662 4.68007 9.47144 5.62273 9.80999 6.53C9.94454 6.88792 9.97366 7.27691 9.89391 7.65088C9.81415 8.02485 9.62886 8.36811 9.35999 8.64L8.08999 9.91C9.51355 12.4135 11.5865 14.4864 14.09 15.91L15.36 14.64C15.6319 14.3711 15.9751 14.1858 16.3491 14.1061C16.7231 14.0263 17.1121 14.0555 17.47 14.19C18.3773 14.5286 19.3199 14.7634 20.28 14.89C20.7658 14.9605 21.2094 15.2032 21.5265 15.5735C21.8437 15.9438 22.0122 16.4186 21.9999 16.91L22 16.92Z"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="validation-message" id="contact_number_error"></div>
                                <div class="input-hint">Enter your 11-digit mobile number (e.g., 09123456789)</div>
                            </div>

                            <!-- Progress Indicator -->
                            <div class="progress-indicator" id="progressIndicator" style="display: none;">
                                <div class="progress-icon">
                                    <svg class="spinner" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                                            <animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite" />
                                            <animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite" />
                                        </circle>
                                    </svg>
                                </div>
                                <div class="progress-message" id="progressMessage">Processing your request...</div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span class="btn-text">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 9V2H18V9M6 18H4C2.89543 18 2 17.1046 2 16V11C2 9.89543 2.89543 9 4 9H20C21.1046 9 22 9.89543 22 11V16C22 17.1046 21.1046 18 20 18H18M6 14H18V22H6V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Submit & Print Receipt
                                    </span>
                                    <span class="btn-loading" style="display: none;">
                                        <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                                stroke-dasharray="31.416" stroke-dashoffset="31.416">
                                                <animate attributeName="stroke-dasharray" dur="2s"
                                                    values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite" />
                                                <animate attributeName="stroke-dashoffset" dur="2s"
                                                    values="0;-15.708;-31.416" repeatCount="indefinite" />
                                            </circle>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Chat button -->
            <button class="chat" aria-label="chat" onclick="navigateToChatbot()">
                <img src="{{ asset('assets/icons/chatbot.png') }}" alt="chat" />
                <span>Chat</span>
            </button>

            <!-- Footer -->
            <div class="footer">Powered by: Click2Serve</div>
        </div>
    </div>

    <script>
        function goBack() {
            window.location.href = '/services';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        // Real-time validation functions
        function validateFullName(name) {
            const nameRegex = /^[a-zA-Z\s\.\-']{2,50}$/;
            return nameRegex.test(name.trim());
        }

        function validatePhoneNumber(phone) {
            const phoneRegex = /^09\d{9}$/;
            return phoneRegex.test(phone.replace(/\s/g, ''));
        }

        function showValidationError(fieldId, message) {
            const errorElement = document.getElementById(fieldId + '_error');
            const inputElement = document.getElementById(fieldId);

            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }

            if (inputElement) {
                inputElement.classList.add('error');
                inputElement.classList.remove('success');
            }
        }

        function clearValidationError(fieldId) {
            const errorElement = document.getElementById(fieldId + '_error');
            const inputElement = document.getElementById(fieldId);

            if (errorElement) {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }

            if (inputElement) {
                inputElement.classList.remove('error');
                inputElement.classList.add('success');
            }
        }

        function updateSubmitButton() {
            const fullName = document.getElementById('full_name').value.trim();
            const contactNumber = document.getElementById('contact_number').value.trim();
            const submitBtn = document.getElementById('submitBtn');

            const isFormValid = validateFullName(fullName) && validatePhoneNumber(contactNumber);
            submitBtn.disabled = !isFormValid;

            if (isFormValid) {
                submitBtn.classList.add('ready');
            } else {
                submitBtn.classList.remove('ready');
            }
        }

        // Real-time validation event listeners
        document.addEventListener('DOMContentLoaded', function () {
            const fullNameInput = document.getElementById('full_name');
            const contactNumberInput = document.getElementById('contact_number');
            const submitBtn = document.getElementById('submitBtn');

            // Full name validation
            fullNameInput.addEventListener('input', function () {
                const value = this.value.trim();

                if (value.length === 0) {
                    clearValidationError('full_name');
                } else if (value.length < 2) {
                    showValidationError('full_name', 'Name must be at least 2 characters long');
                } else if (!validateFullName(value)) {
                    showValidationError('full_name', 'Please enter a valid name (letters, spaces, dots, hyphens, and apostrophes only)');
                } else {
                    clearValidationError('full_name');
                }

                updateSubmitButton();
            });

            // Phone number validation
            contactNumberInput.addEventListener('input', function () {
                let value = this.value.replace(/\D/g, ''); // Remove non-digits

                // Limit to 11 digits
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }

                this.value = value;

                if (value.length === 0) {
                    clearValidationError('contact_number');
                } else if (value.length < 11) {
                    showValidationError('contact_number', 'Phone number must be 11 digits');
                } else if (!validatePhoneNumber(value)) {
                    showValidationError('contact_number', 'Please enter a valid mobile number starting with 09');
                } else {
                    clearValidationError('contact_number');
                }

                updateSubmitButton();
            });

            // Format phone number display
            contactNumberInput.addEventListener('blur', function () {
                let value = this.value.replace(/\D/g, '');
                if (value.length === 11) {
                    this.value = value.replace(/(\d{4})(\d{3})(\d{4})/, '$1 $2 $3');
                }
            });

            contactNumberInput.addEventListener('focus', function () {
                this.value = this.value.replace(/\s/g, '');
            });

            // Touch-friendly interactions
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('touchstart', function () {
                    this.style.transform = 'scale(1.02)';
                });

                input.addEventListener('touchend', function () {
                    this.style.transform = 'scale(1)';
                });
            });

            // Submit button touch effects
            submitBtn.addEventListener('touchstart', function () {
                if (!this.disabled) {
                    this.style.transform = 'scale(0.98)';
                }
            });

            submitBtn.addEventListener('touchend', function () {
                this.style.transform = 'scale(1)';
            });

            // Auto-focus on first input
            fullNameInput.focus();

            // Initial button state
            updateSubmitButton();
        });

        // Handle form submission
        document.getElementById('kioskForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const fullName = document.getElementById('full_name').value.trim();
            const contactNumber = document.getElementById('contact_number').value.trim();
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');

            // Final validation
            if (!validateFullName(fullName)) {
                showValidationError('full_name', 'Please enter a valid name');
                return;
            }

            if (!validatePhoneNumber(contactNumber)) {
                showValidationError('contact_number', 'Please enter a valid mobile number');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';

            // Show progress indicator
            const progressIndicator = document.getElementById('progressIndicator');
            const progressMessage = document.getElementById('progressMessage');
            progressIndicator.style.display = 'flex';
            progressMessage.textContent = 'Submitting your request...';

            // Get form data
            const formData = new FormData(this);

            // Submit request
            fetch('/api/request/kiosk-submit', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update progress indicator
                        progressMessage.textContent = 'Sending SMS notification...';
                        
                        // Store tracking data for tracker page (include queue number for Other Services)
                        // Use the actual status from the response, not hardcoded 'inquiry'
                        const trackingData = {
                            full_name: data.data.full_name,
                            tracking_number: data.data.tracking_number,
                            queue_number: data.data.queue_number || null, // Include queue number for Other Services
                            service_type: data.data.service_type,
                            service_name: data.data.service_name,
                            department: data.data.department,
                            status: data.data.status || 'inquiry', // Use actual status from response
                            date: new Date().toISOString().split('T')[0], // Format: YYYY-MM-DD
                            time: new Date().toTimeString().split(' ')[0], // Format: HH:MM:SS
                            contact_number: data.data.contact_number
                        };
                        sessionStorage.setItem('currentTrackingData', JSON.stringify(trackingData));

                        // Update progress indicator
                        progressMessage.textContent = 'Printing receipt...';
                        
                        setTimeout(() => {
                            progressMessage.textContent = 'Request submitted successfully! Redirecting to tracker...';
                            submitBtn.innerHTML = '<span class="btn-text">✓ Success! Redirecting...</span>';
                            submitBtn.classList.add('success');

                            setTimeout(() => {
                                // Mark as new submission for feedback modal
                                sessionStorage.setItem('new_submission', 'true');
                                window.location.href = '/services/tracker?tracking=' + data.data.tracking_number;
                            }, 1500);
                        }, 1000);
                    } else {
                        progressIndicator.style.display = 'none';
                        alert('Error: ' + (data.message || 'Failed to submit request'));
                        // Reset button state
                        submitBtn.disabled = false;
                        btnText.style.display = 'inline';
                        btnLoading.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    progressIndicator.style.display = 'none';
                    alert('Error: Unable to submit request. Please try again.');
                    // Reset button state
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                });
        });

        // Prevent context menu and text selection for kiosk mode
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        document.addEventListener('selectstart', function (e) {
            e.preventDefault();
        });
    </script>
</body>

</html>