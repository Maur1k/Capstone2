@extends('website.layouts.app')

@section('title', 'Book Appointment – Personal Details')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/appointment-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/website/appointment-details.css') }}">

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
                    <div class="progress-step active">
                        <span class="step-number">3</span>
                        <span class="progress-label">Details</span>
                    </div>
                    <div class="progress-step">
                        <span class="step-number">4</span>
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
                        width: calc(66.66% - 10px);
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
                        <div class="instruction-content">
                            <h4>Provide Your Contact Information</h4>
                            <p>We only need your full name and contact number for the appointment. This information will be
                                used by our civil registry office.</p>
                        </div>
                    </div>
                </div>

                <!-- Personal Details Form -->
                <div class="personal-details-section">
                    <h3>Personal Information</h3>

                    <!-- Full Name -->
                    <div class="form-group">
                        <label for="full-name">
                            <span class="label-text">Full Name</span>
                            <span class="label-help">Enter your complete name as it appears on your ID</span>
                        </label>
                        <input type="text" id="full-name" name="full_name" placeholder="Enter your full name" required>
                        <div class="input-help">This will be displayed in the civil registry office database</div>
                    </div>

                    <!-- Contact Number -->
                    <div class="form-group">
                        <label for="contact-number">
                            <span class="label-text">Contact Number</span>
                            <span class="label-help">Enter your active mobile number</span>
                        </label>
                        <input type="tel" id="contact-number" name="contact_number" placeholder="Enter your phone number"
                            required>
                        <div class="input-help">We'll use this to contact you about your appointment</div>
                    </div>

                    <!-- Age -->
                    <div class="form-group">
                        <label for="age">
                            <span class="label-text">Age</span>
                            <span class="label-help">Enter your age (required for determining checklist requirements)</span>
                        </label>
                        <input type="number" id="age" name="age" placeholder="Enter your age" min="1" max="150" required>
                        <div class="input-help">This helps us provide the appropriate checklist for your age group</div>
                    </div>

                    <!-- Information Summary -->
                    <div class="info-summary" id="info-summary" style="display: none;">
                        <div class="summary-card">
                            <h5>Your Information Summary</h5>
                            <div class="summary-details">
                                <div class="summary-item">
                                    <span class="summary-label">Name:</span>
                                    <span class="summary-value" id="summary-name">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Contact:</span>
                                    <span class="summary-value" id="summary-contact">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Age:</span>
                                    <span class="summary-value" id="summary-age">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-actions">
                    <a href="{{ route('appointment.tnd') }}" class="back-btn">Back</a>
                    <button type="button" id="next-btn" class="btn-primary" disabled>Next</button>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                            console.log('Service data loaded from backend:', serviceData);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading service data:', error);
                    });
            }

            const fullNameInput = document.getElementById('full-name');
            const contactNumberInput = document.getElementById('contact-number');
            const ageInput = document.getElementById('age');
            const nextBtn = document.getElementById('next-btn');
            const summaryDiv = document.getElementById('info-summary');
            const summaryName = document.getElementById('summary-name');
            const summaryContact = document.getElementById('summary-contact');
            const summaryAge = document.getElementById('summary-age');

            // Update summary when inputs change
            function updateSummary() {
                const fullName = fullNameInput.value.trim();
                const contactNumber = contactNumberInput.value.trim();
                const age = ageInput.value.trim();
                const ageNum = parseInt(age);

                if (fullName && contactNumber && age) {
                    summaryName.textContent = fullName;
                    summaryContact.textContent = contactNumber;
                    // Display age with Octogenarian label if 80 or above
                    if (!isNaN(ageNum) && ageNum >= 80) {
                        summaryAge.textContent = age + ' years old (Octogenarian)';
                    } else {
                        summaryAge.textContent = age + ' years old';
                    }
                    summaryDiv.style.display = 'block';
                    nextBtn.disabled = false;
                    nextBtn.textContent = 'Continue to Confirmation';

                    // Show success message when all fields are filled
                    showFloatingMessage('Perfect! Your information is complete. Ready to proceed!', 'success');
                } else {
                    summaryDiv.style.display = 'none';
                    nextBtn.disabled = true;
                    nextBtn.textContent = 'Next';
                }
            }

            // Add event listeners
            fullNameInput.addEventListener('input', updateSummary);
            contactNumberInput.addEventListener('input', updateSummary);
            ageInput.addEventListener('input', updateSummary);

            // Handle next button click
            nextBtn.addEventListener('click', function () {
                if (!nextBtn.disabled) {
                    // Validate form before proceeding
                    if (!fullNameInput.value.trim() || !contactNumberInput.value.trim() || !ageInput.value.trim()) {
                        showFloatingMessage('Please fill in name, contact number, and age before continuing.', 'error');
                        return;
                    }

                    // Validate age is a valid number
                    const age = parseInt(ageInput.value.trim());
                    if (isNaN(age) || age < 1 || age > 150) {
                        showFloatingMessage('Please enter a valid age (1-150).', 'error');
                        return;
                    }

                    // Store personal details in sessionStorage
                    const personalDetails = {
                        fullName: fullNameInput.value.trim(),
                        contactNumber: contactNumberInput.value.trim(),
                        age: age
                    };
                    sessionStorage.setItem('personalDetails', JSON.stringify(personalDetails));

                    // Save to database with correct field names
                    const dbPersonalData = {
                        full_name: fullNameInput.value.trim(),
                        contact_number: contactNumberInput.value.trim(),
                        age: age
                    };
                    
                    // Ensure all required data is in backend session before creating appointment
                    ensureAllDataInSession().then(() => {
                        return savePersonalDetails(dbPersonalData);
                    }).then(() => {
                        // After saving personal details, create the appointment
                        createAppointment();
                    }).catch(error => {
                        console.error('Error preparing appointment data:', error);
                        showFloatingMessage('Error preparing appointment data. Please try again.', 'error');
                    });
                } else {
                    showFloatingMessage('Please complete all required fields to continue.', 'warning');
                }
            });

            function ensureAllDataInSession() {
                // Get data from sessionStorage
                const selectedService = JSON.parse(sessionStorage.getItem('selectedService') || '{}');
                const appointmentData = JSON.parse(sessionStorage.getItem('appointmentData') || '{}');
                
                const promises = [];
                
                // Ensure service data is in backend session
                if (selectedService.serviceName) {
                    const serviceData = {
                        service_name: selectedService.serviceName,
                        department: selectedService.department || 'Civil Registry',
                        service_type: selectedService.serviceType || selectedService.service_type || 'Civil Service',
                        description: selectedService.description || ''
                    };
                    promises.push(
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
                                console.log('Service data synced to backend session');
                            } else {
                                console.warn('Warning: Could not sync service data:', data.message);
                            }
                        })
                        .catch(error => {
                            console.warn('Warning: Could not sync service data:', error);
                        })
                    );
                }
                
                // Ensure date/time data is in backend session
                if (appointmentData.date && appointmentData.time) {
                    const dateTimeData = {
                        appointment_date: appointmentData.date || appointmentData.appointment_date,
                        appointment_time: appointmentData.time || appointmentData.appointment_time
                    };
                    promises.push(
                        fetch('{{ route("website.appointment.store-datetime") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(dateTimeData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Date/time data synced to backend session');
                            } else {
                                console.warn('Warning: Could not sync date/time data:', data.message);
                            }
                        })
                        .catch(error => {
                            console.warn('Warning: Could not sync date/time data:', error);
                        })
                    );
                }
                
                // Wait for all sync operations to complete
                return Promise.all(promises);
            }

            function savePersonalDetails(data) {
                return fetch('{{ route("website.appointment.store-details") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Personal details saved successfully');
                            return data;
                        } else {
                            console.error('Error saving personal details:', data.message);
                            showFloatingMessage('Error saving personal details. Please try again.', 'error');
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error saving personal details:', error);
                        showFloatingMessage('Error saving personal details. Please try again.', 'error');
                        throw error;
                    });
            }

            function createAppointment() {
                // Get all data from session storage
                const selectedService = JSON.parse(sessionStorage.getItem('selectedService') || '{}');
                const appointmentData = JSON.parse(sessionStorage.getItem('appointmentData') || '{}');
                const personalDetails = JSON.parse(sessionStorage.getItem('personalDetails') || '{}');

                // Show loading message
                showFloatingMessage('Creating your appointment... Please wait.', 'info');

                fetch('{{ route("website.appointment.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                    .then(response => response.json())
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
                                appointmentNumber: data.appointment.appointment_number || 'N/A'
                            };
                            
                            console.log('Appointment summary:', appointmentSummary);

                            // Store in session storage for done page
                            sessionStorage.setItem('appointmentSummary', JSON.stringify(appointmentSummary));
                            
                            // Store SMS status for done page
                            sessionStorage.setItem('smsSent', data.sms_sent ? 'true' : 'false');

                            // Show success message with SMS status
                            let successMsg = 'Appointment created successfully!';
                            if (data.sms_sent) {
                                successMsg += ' SMS confirmation sent to your phone.';
                            }
                            successMsg += ' Redirecting to confirmation...';
                            showFloatingMessage(successMsg, 'success');

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

            // Format phone number input
            contactNumberInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
                if (value.length > 11) {
                    value = value.substring(0, 11); // Limit to 11 digits
                }
                e.target.value = value;
            });

            // Add helpful tooltips
            const tooltips = [
                {
                    element: fullNameInput,
                    message: 'Enter your complete name as it appears on your government ID or passport.'
                },
                {
                    element: contactNumberInput,
                    message: 'Enter your active mobile number. We\'ll use this to contact you.'
                }
            ];

            tooltips.forEach(tooltip => {
                tooltip.element.addEventListener('focus', function () {
                    showTooltip(this, tooltip.message);
                });

                tooltip.element.addEventListener('blur', function () {
                    hideTooltip();
                });
            });
        });

        function showTooltip(element, message) {
            const tooltip = document.createElement('div');
            tooltip.className = 'input-tooltip';
            tooltip.textContent = message;
            document.body.appendChild(tooltip);

            const rect = element.getBoundingClientRect();
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.bottom + 5) + 'px';
        }

        function hideTooltip() {
            const tooltip = document.querySelector('.input-tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        }

        function showFloatingMessage(message, type = 'info') {
            // Remove any existing floating messages
            const existingMessage = document.querySelector('.floating-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Create floating message element
            const floatingMessage = document.createElement('div');
            floatingMessage.className = `floating-message floating-message-${type}`;

            floatingMessage.innerHTML = `
                                <div class="floating-message-content">
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