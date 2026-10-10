@extends('website.layouts.app')

@section('title', 'Book Appointment – Time and Date')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/appointment-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/website/appointment-tnd.css') }}">

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
                    <div class="progress-step active">
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

                <!-- Progress line overlay for completed steps -->
                <style>
                    .progressbar::after {
                        content: '';
                        position: absolute;
                        top: 50%;
                        left: 20px;
                        width: calc(33.33% - 10px);
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
                            <h4>Choose Your Preferred Time & Date</h4>
                            <p>Select when you'd like to visit our office. We'll do our best to accommodate your preferred
                                schedule.</p>
                        </div>
                    </div>
                </div>

                <!-- Office Hours Info -->
                <div class="office-hours-section">
                    <div class="office-hours-card">
                        <div class="office-hours-header">
                            <h5>Office Hours</h5>
                        </div>
                        <div class="office-hours-content">
                            <div class="hours-item">
                                <span class="day">Monday - Friday</span>
                                <span class="time">8:00 AM - 5:00 PM</span>
                            </div>
                            <div class="hours-item">
                                <span class="day">Weekends</span>
                                <span class="time">Closed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Form -->
                <div class="appointment-form-section">
                    <h3>Select Your Appointment</h3>

                    <!-- Date Selection -->
                    <div class="form-group">
                        <label for="appointment-date">
                            <span class="label-text">Preferred Date</span>
                            <span class="label-help">Choose a date within the next 30 days</span>
                        </label>
                        <input type="date" id="appointment-date" name="appointment_date" min="{{ date('Y-m-d') }}"
                            max="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                        <div class="input-help">Available dates are shown in the calendar picker. Weekends are not available.</div>
                    </div>

                    <!-- Time Selection -->
                    <div class="form-group">
                        <label for="appointment-time">
                            <span class="label-text">Preferred Time</span>
                            <span class="label-help">Select your preferred time slot</span>
                        </label>
                        <select id="appointment-time" name="appointment_time" required>
                            <option value="">Choose a time slot</option>
                            <option value="08:00">8:00 AM - Morning</option>
                            <option value="09:00">9:00 AM - Morning</option>
                            <option value="10:00">10:00 AM - Late Morning</option>
                            <option value="11:00">11:00 AM - Late Morning</option>
                            <option value="13:00">1:00 PM - Afternoon</option>
                            <option value="14:00">2:00 PM - Afternoon</option>
                            <option value="15:00">3:00 PM - Late Afternoon</option>
                            <option value="16:00">4:00 PM - Late Afternoon</option>
                        </select>
                        <div class="input-help">Time slots are subject to availability</div>
                    </div>

                    <!-- Appointment Summary -->
                    <div class="appointment-summary" id="appointment-summary" style="display: none;">
                        <div class="summary-card">
                            <h5>Your Appointment Summary</h5>
                            <div class="summary-details">
                                <div class="summary-item">
                                    <span class="summary-label">Date:</span>
                                    <span class="summary-value" id="summary-date">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Time:</span>
                                    <span class="summary-value" id="summary-time">-</span>
                                </div>
                            </div>
                            <div class="summary-warning"
                                style="margin-top: 16px; padding: 12px 16px; background: #fff3cd; border-left: 3px solid #ffc107; border-radius: 4px;">
                                <p style="margin: 0; font-size: 13px; color: #856404; line-height: 1.5;">
                                    <strong>Paalala / Reminder:</strong> Kung ma-late ka, makiki-pila ka na o maghihintay ka
                                    sa queue onsite.
                                    <span style="display: block; margin-top: 4px;">If you arrive late, you will need to join
                                        the queue or wait in the onsite queue.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-actions">
                    <a href="{{ route('appointment.service') }}" class="back-btn">Back</a>
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

            const dateInput = document.getElementById('appointment-date');
            const timeSelect = document.getElementById('appointment-time');
            const nextBtn = document.getElementById('next-btn');
            const summaryDiv = document.getElementById('appointment-summary');
            const summaryDate = document.getElementById('summary-date');
            const summaryTime = document.getElementById('summary-time');

            // Set minimum date to today (allow same-day booking)
            const today = new Date();
            dateInput.min = today.toISOString().split('T')[0];

            // Set maximum date to 30 days from today
            const maxDate = new Date();
            maxDate.setDate(maxDate.getDate() + 30);
            dateInput.max = maxDate.toISOString().split('T')[0];

            // Available time slots
            const timeSlots = [
                { value: '08:00', label: '8:00 AM - Morning' },
                { value: '09:00', label: '9:00 AM - Morning' },
                { value: '10:00', label: '10:00 AM - Late Morning' },
                { value: '11:00', label: '11:00 AM - Late Morning' },
                { value: '13:00', label: '1:00 PM - Afternoon' },
                { value: '14:00', label: '2:00 PM - Afternoon' },
                { value: '15:00', label: '3:00 PM - Late Afternoon' },
                { value: '16:00', label: '4:00 PM - Late Afternoon' }
            ];

            // Function to get current date and time
            function getCurrentDateTime() {
                return new Date();
            }

            // Function to check if appointment time is valid (at least 30 minutes from now)
            function isValidAppointmentTime(selectedDate, selectedTime) {
                const now = getCurrentDateTime();
                const appointmentDateTime = new Date(selectedDate + 'T' + selectedTime);
                
                // Calculate difference in milliseconds
                const diffMs = appointmentDateTime - now;
                const diffMinutes = diffMs / (1000 * 60);
                
                // Must be at least 30 minutes in the future
                return diffMinutes >= 30;
            }

            // Function to check if date is a weekend
            function isWeekend(date) {
                const dayOfWeek = date.getDay();
                return dayOfWeek === 0 || dayOfWeek === 6; // Sunday or Saturday
            }

            // Function to check if time is within working hours (8:00 AM - 5:00 PM)
            function isWithinWorkingHours(timeInMinutes) {
                const startTime = 8 * 60; // 8:00 AM in minutes
                const endTime = 17 * 60; // 5:00 PM in minutes
                return timeInMinutes >= startTime && timeInMinutes < endTime;
            }

            // Function to get reason why time slot is unavailable
            function getUnavailableReason(selectedDate, slotTimeInMinutes, isToday, currentTimeInMinutes) {
                const selectedDateObj = new Date(selectedDate);
                
                // Check if weekend
                if (isWeekend(selectedDateObj)) {
                    return 'Office is closed on weekends';
                }
                
                // Check if within working hours (8:00 AM - 5:00 PM)
                if (!isWithinWorkingHours(slotTimeInMinutes)) {
                    if (slotTimeInMinutes < 8 * 60) {
                        return 'Office opens at 8:00 AM';
                    } else if (slotTimeInMinutes >= 17 * 60) {
                        return 'Office closes at 5:00 PM';
                    }
                    return 'Outside working hours (8:00 AM - 5:00 PM)';
                }
                
                // Check if it's today and time has passed or is too soon
                if (isToday) {
                    if (slotTimeInMinutes < currentTimeInMinutes) {
                        return 'This time has already passed';
                    }
                    if (slotTimeInMinutes < currentTimeInMinutes + 30) {
                        return 'Must book at least 30 minutes in advance';
                    }
                }
                
                return null; // Available
            }

            // Function to filter available time slots based on selected date
            function updateAvailableTimeSlots() {
                const selectedDate = dateInput.value;
                if (!selectedDate) {
                    // Clear time slots if no date selected
                    timeSelect.innerHTML = '<option value="">Choose a time slot</option>';
                    return;
                }

                const now = getCurrentDateTime();
                const selectedDateObj = new Date(selectedDate);
                const todayDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                const selectedDateOnly = new Date(selectedDateObj.getFullYear(), selectedDateObj.getMonth(), selectedDateObj.getDate());
                
                // Check if selected date is today
                const isToday = selectedDateOnly.getTime() === todayDate.getTime();
                
                // Get current time
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();
                const currentTimeInMinutes = currentHour * 60 + currentMinute;
                
                // Calculate minimum time (30 minutes from now)
                const minTimeInMinutes = currentTimeInMinutes + 30;
                
                // Check if weekend
                const isWeekendDay = isWeekend(selectedDateObj);
                
                // Store current selection
                const currentSelection = timeSelect.value;
                
                // Clear existing options
                timeSelect.innerHTML = '<option value="">Choose a time slot</option>';
                
                // Add time slots with availability messages
                timeSlots.forEach(slot => {
                    const [hours, minutes] = slot.value.split(':').map(Number);
                    const slotTimeInMinutes = hours * 60 + minutes;
                    
                    // Get reason if unavailable
                    const unavailableReason = getUnavailableReason(selectedDate, slotTimeInMinutes, isToday, currentTimeInMinutes);
                    
                    const option = document.createElement('option');
                    option.value = slot.value;
                    
                    if (unavailableReason) {
                        // Show unavailable message
                        option.textContent = `${slot.label} - ${unavailableReason}`;
                        option.disabled = true;
                        option.style.color = '#999';
                        option.style.fontStyle = 'italic';
                    } else {
                        // Available slot
                        option.textContent = slot.label;
                    }
                    
                    timeSelect.appendChild(option);
                });
                
                // Restore selection if it's still available
                if (currentSelection && Array.from(timeSelect.options).some(opt => opt.value === currentSelection && !opt.disabled)) {
                    timeSelect.value = currentSelection;
                } else {
                    timeSelect.value = '';
                }
                
                // Update summary
                updateSummary();
            }

            // Update summary when inputs change
            function updateSummary() {
                const selectedDate = dateInput.value;
                const selectedTime = timeSelect.value;

                if (selectedDate && selectedTime) {
                    // Check if the selected option is disabled
                    const selectedOption = timeSelect.options[timeSelect.selectedIndex];
                    if (selectedOption && selectedOption.disabled) {
                        showFloatingMessage('This time slot is not available. Please select an available time.', 'error');
                        timeSelect.value = '';
                        summaryDiv.style.display = 'none';
                        nextBtn.disabled = true;
                        nextBtn.textContent = 'Next';
                        return;
                    }

                    // Validate appointment time (at least 30 minutes from now)
                    if (!isValidAppointmentTime(selectedDate, selectedTime)) {
                        showFloatingMessage('Appointments must be booked at least 30 minutes in advance. Please select a different time.', 'error');
                        timeSelect.value = '';
                        summaryDiv.style.display = 'none';
                        nextBtn.disabled = true;
                        nextBtn.textContent = 'Next';
                        return;
                    }

                    // Format date
                    const dateObj = new Date(selectedDate);
                    const formattedDate = dateObj.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    // Format time
                    const timeObj = new Date('2000-01-01T' + selectedTime);
                    const formattedTime = timeObj.toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });

                    summaryDate.textContent = formattedDate;
                    summaryTime.textContent = formattedTime;
                    summaryDiv.style.display = 'block';
                    nextBtn.disabled = false;
                    nextBtn.textContent = 'Continue to Details';

                    // Show success message when both fields are selected
                    showFloatingMessage('Great! Your appointment is ready. Review the summary below.', 'success');
                } else {
                    summaryDiv.style.display = 'none';
                    nextBtn.disabled = true;
                    nextBtn.textContent = 'Next';
                }
            }

            // Add event listeners
            dateInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                
                // Check if weekend
                if (isWeekend(selectedDate)) {
                    showFloatingMessage('Our office is closed on weekends. Please select a weekday (Monday-Friday).', 'error');
                    this.value = '';
                    updateSummary();
                    return;
                }
                
                // Update available time slots when date changes
                updateAvailableTimeSlots();
            });
            
            timeSelect.addEventListener('change', function() {
                updateSummary();
            });

            // Handle next button click
            nextBtn.addEventListener('click', function () {
                if (!nextBtn.disabled) {
                    // Validate form before proceeding
                    if (!dateInput.value || !timeSelect.value) {
                        showFloatingMessage('Please select both date and time before continuing.', 'error');
                        return;
                    }

                    // Check if the selected option is disabled
                    const selectedOption = timeSelect.options[timeSelect.selectedIndex];
                    if (selectedOption && selectedOption.disabled) {
                        showFloatingMessage('This time slot is not available. Please select an available time.', 'error');
                        return;
                    }

                    // Final validation: Check if appointment is at least 30 minutes from now
                    if (!isValidAppointmentTime(dateInput.value, timeSelect.value)) {
                        showFloatingMessage('Appointments must be booked at least 30 minutes in advance. Please select a different time.', 'error');
                        return;
                    }

                    // Store appointment data in sessionStorage
                    const appointmentData = {
                        date: dateInput.value,
                        time: timeSelect.value
                    };
                    sessionStorage.setItem('appointmentData', JSON.stringify(appointmentData));

                    // Save to database with correct field names
                    const dbData = {
                        appointment_date: dateInput.value,
                        appointment_time: timeSelect.value
                    };
                    
                    // Save and wait for response before redirecting
                    saveDateTime(dbData).then(success => {
                        if (success) {
                            // Show confirmation message
                            showFloatingMessage('Appointment details saved! Redirecting to next step...', 'success');

                            // Small delay before redirect for user to see the message
                            setTimeout(() => {
                                window.location.href = '{{ route("appointment.details") }}';
                            }, 1500);
                        }
                    });
                } else {
                    showFloatingMessage('Please complete all required fields to continue.', 'warning');
                }
            });

            function saveDateTime(data) {
                return fetch('{{ route("website.appointment.store-datetime") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => {
                        return response.json().then(data => {
                            if (!response.ok) {
                                throw new Error(data.message || 'Validation failed');
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        if (data.success) {
                            console.log('Date and time saved successfully');
                            return true;
                        } else {
                            throw new Error(data.message || 'Error saving appointment data');
                        }
                    })
                    .catch(error => {
                        console.error('Error saving date and time:', error);
                        showFloatingMessage(error.message || 'Error saving appointment data. Please try again.', 'error');
                        // Reset form if validation fails
                        timeSelect.value = '';
                        updateSummary();
                        return false;
                    });
            }

            // Initialize available time slots on page load
            updateAvailableTimeSlots();

            // Add helpful tooltips
            const tooltips = [
                {
                    element: dateInput,
                    message: 'Select a date within the next 30 days. Weekends are limited availability.'
                },
                {
                    element: timeSelect,
                    message: 'Choose your preferred time slot. We\'ll confirm availability.'
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