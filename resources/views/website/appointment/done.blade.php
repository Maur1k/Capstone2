@extends('website.layouts.app')

@section('title', 'Book Appointment – Done')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/appointment-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/website/appointment-done.css') }}">

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
                    <div class="progress-step active completed">
                        <span class="checkmark">✓</span>
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
                        width: calc(100% - 40px);
                        height: 3px;
                        background: #10b981;
                        transform: translateY(-50%);
                        z-index: 0;
                        border-radius: 2px;
                    }

                    .progress-step.completed {
                        background: #10b981 !important;
                    }
                </style>

                <!-- Success Animation -->
                <div class="success-animation">
                    <div class="success-icon">
                        <div class="checkmark-circle">
                            <div class="checkmark"></div>
                        </div>
                    </div>
                    <h2>Appointment Booked Successfully!</h2>
                    <p class="success-message">Your civil service appointment has been confirmed. We'll contact you soon
                        with further details.</p>

                    <!-- SMS Notification Badge -->
                    <div class="sms-notification-badge" id="sms-notification-badge" style="display: none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        <span>SMS Confirmation Sent!</span>
                    </div>
                </div>

                <!-- Appointment Summary -->
                <div class="appointment-summary" id="appointment-summary">
                    <h3>Appointment Summary</h3>
                    <div class="summary-content">
                        <div class="summary-item">
                            <span class="summary-label">Reference Number:</span>
                            <span class="summary-value" id="summary-reference">Loading...</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Service:</span>
                            <span class="summary-value" id="summary-service">Loading...</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Date & Time:</span>
                            <span class="summary-value" id="summary-datetime">Loading...</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Contact:</span>
                            <span class="summary-value" id="summary-contact">Loading...</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label" id="summary-age-label">Age:</span>
                            <span class="summary-value" id="summary-age">Loading...</span>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="next-steps">
                    <h3>What Happens Next?</h3>
                    <div class="steps-list">
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>SMS Confirmation</h4>
                                <p>You will receive an SMS confirmation with your appointment details and reference number.
                                </p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Appointment Day</h4>
                                <p>Visit our office at the scheduled time with your required documents.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Information -->
                <div class="important-info">
                    <h3>Important Information</h3>
                    <div class="info-cards">
                        <div class="info-card">
                            <div class="info-icon document-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                            </div>
                            <div class="info-content">
                                <h4>Bring Required Documents</h4>
                                <p>Don't forget to bring all required documents on your appointment day.</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon clock-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </div>
                            <div class="info-content">
                                <h4>Be On Time</h4>
                                <p>Please arrive 15 minutes before your scheduled appointment time.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('website.homepage') }}" class="btn-primary">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                        Return to Home
                    </a>

                    <button onclick="printSummary()" class="btn-outline">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9" />
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <rect x="6" y="14" width="12" height="8" />
                        </svg>
                        Print Summary
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            animateSuccessIcon();
            checkSmsStatus();
            const appointmentNumber = new URLSearchParams(window.location.search).get('appointment');
            if (appointmentNumber) {
                sessionStorage.setItem('appointmentNumber', appointmentNumber);
                fetchAppointmentSummary(appointmentNumber);
            } else {
                loadSummaryFromSession();
            }
        });

        function checkSmsStatus() {
            const urlParams = new URLSearchParams(window.location.search);
            const smsSent = urlParams.get('sms') === 'sent' || sessionStorage.getItem('smsSent') === 'true';

            if (smsSent) {
                const smsBadge = document.getElementById('sms-notification-badge');
                if (smsBadge) {
                    smsBadge.style.display = 'flex';
                    // Add animation
                    setTimeout(() => {
                        smsBadge.classList.add('animate');
                    }, 500);
                }
            }
        }

        const summaryElements = {
            reference: document.getElementById('summary-reference'),
            service: document.getElementById('summary-service'),
            datetime: document.getElementById('summary-datetime'),
            contact: document.getElementById('summary-contact'),
            age: document.getElementById('summary-age'),
            ageLabel: document.getElementById('summary-age-label')
        };

        const summaryDefaults = {
            reference: 'Reference number not available',
            service: 'Service not specified',
            datetime: 'Date and time not specified',
            contact: 'Contact information not provided',
            age: 'Age not provided'
        };

        function fetchAppointmentSummary(appointmentNumber) {
            showSummaryLoading();
            fetch(`/appointment/number/${encodeURIComponent(appointmentNumber)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Appointment not found');
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.appointment) {
                        const mapped = mapAppointmentResponse(data.appointment);
                        sessionStorage.setItem('appointmentSummary', JSON.stringify(mapped));
                        updateSummary(mapped);
                    } else {
                        throw new Error(data.message || 'Appointment not found');
                    }
                })
                .catch(error => {
                    console.error('Appointment summary error:', error);
                    showSummaryError('Unable to load appointment details.');
                });
        }

        function loadSummaryFromSession() {
            try {
                const summary = JSON.parse(sessionStorage.getItem('appointmentSummary') || '{}');
                if (summary && summary.service) {
                    updateSummary(summary);
                    return;
                }

                // Fallback: rebuild from individual session entries
                const selectedService = JSON.parse(sessionStorage.getItem('selectedService') || '{}');
                const appointmentData = JSON.parse(sessionStorage.getItem('appointmentData') || '{}');
                const personalDetails = JSON.parse(sessionStorage.getItem('personalDetails') || '{}');
                const uploadedFiles = JSON.parse(sessionStorage.getItem('uploadedFiles') || '[]');

                // Format service name for display
                let serviceName = selectedService.serviceName || selectedService.service_type || selectedService.service_name;
                if (serviceName && serviceName.includes('Delayed Regular Non-BRAP')) {
                    serviceName = 'Delayed Birth Registration (Regular)';
                } else if (serviceName && serviceName.includes('Delayed BRAP')) {
                    serviceName = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
                }

                // Get appointment number from URL or session
                const urlParams = new URLSearchParams(window.location.search);
                const appointmentNumber = urlParams.get('appointment') || sessionStorage.getItem('appointmentNumber') || 'N/A';

                const fallbackSummary = {
                    reference: appointmentNumber,
                    service: serviceName,
                    date: appointmentData.date || appointmentData.appointment_date,
                    time: appointmentData.time || appointmentData.appointment_time,
                    fullName: personalDetails.fullName || personalDetails.full_name,
                    contactNumber: personalDetails.contactNumber || personalDetails.contact_number,
                    age: personalDetails.age || null
                };

                if (fallbackSummary.service || fallbackSummary.date || fallbackSummary.fullName) {
                    updateSummary(fallbackSummary);
                } else {
                    showSummaryError('No appointment summary available. Please book an appointment first.');
                }
            } catch (error) {
                console.error('Session summary error:', error);
                showSummaryError('Unable to load appointment summary.');
            }
        }

        function mapAppointmentResponse(appointment) {
            // Format service name for display
            let serviceName = appointment.service_name || appointment.service_type || 'Service Request';
            if (serviceName && serviceName.includes('Delayed Regular Non-BRAP')) {
                serviceName = 'Delayed Birth Registration (Regular)';
            } else if (serviceName && serviceName.includes('Delayed BRAP')) {
                serviceName = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
            }

            // Get age from appointment or sessionStorage
            let age = appointment.age || null;
            if (!age) {
                try {
                    const personalDetails = JSON.parse(sessionStorage.getItem('personalDetails') || '{}');
                    age = personalDetails.age || null;
                } catch (e) {
                    console.warn('Could not get age from sessionStorage:', e);
                }
            }

            return {
                reference: appointment.appointment_number || 'N/A',
                service: serviceName,
                date: appointment.appointment_date || null,
                time: appointment.appointment_time || null,
                fullName: appointment.full_name || 'N/A',
                contactNumber: appointment.contact_number || 'N/A',
                age: age
            };
        }

        function updateSummary(summary) {
            summaryElements.reference.textContent = summary.reference || summaryDefaults.reference;
            summaryElements.service.textContent = summary.service || summaryDefaults.service;
            summaryElements.datetime.textContent = formatDateTime(summary.date, summary.time);
            summaryElements.contact.textContent = formatContact(summary.fullName, summary.contactNumber);
            
            // Format and display age
            const age = summary.age || null;
            if (age !== null && age !== undefined) {
                const ageNum = parseInt(age);
                if (!isNaN(ageNum)) {
                    if (ageNum >= 80) {
                        summaryElements.ageLabel.textContent = 'Age (Octogenarian):';
                        summaryElements.age.textContent = `${ageNum} (Octogenarian)`;
                    } else {
                        summaryElements.ageLabel.textContent = 'Age:';
                        summaryElements.age.textContent = ageNum.toString();
                    }
                } else {
                    summaryElements.ageLabel.textContent = 'Age:';
                    summaryElements.age.textContent = summaryDefaults.age;
                }
            } else {
                summaryElements.ageLabel.textContent = 'Age:';
                summaryElements.age.textContent = summaryDefaults.age;
            }
        }

        function formatDateTime(date, time) {
            if (!date && !time) return summaryDefaults.datetime;
            if (!date) return time || summaryDefaults.datetime;
            try {
                const parsedDate = new Date(`${date}T00:00:00`);
                const formattedDate = isNaN(parsedDate) ? date : parsedDate.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                return time ? `${formattedDate} at ${formatTime(time)}` : formattedDate;
            } catch (error) {
                console.warn('Date format warning:', error);
                return `${date} ${time || ''}`.trim();
            }
        }

        function formatTime(time) {
            if (!time || !time.includes(':')) return time || '';
            const [hoursStr, minutes = '00'] = time.split(':');
            const hours = parseInt(hoursStr, 10);
            if (isNaN(hours)) return time;
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHour = hours % 12 || 12;
            return `${displayHour}:${minutes.padStart(2, '0')} ${ampm}`;
        }

        function formatContact(name, number) {
            if (!name && !number) return summaryDefaults.contact;
            if (!name) return number;
            if (!number) return name;
            return `${name} - ${number}`;
        }


        function showSummaryLoading() {
            summaryElements.reference.textContent = 'Loading...';
            summaryElements.service.textContent = 'Loading...';
            summaryElements.datetime.textContent = 'Loading...';
            summaryElements.contact.textContent = 'Loading...';
            summaryElements.age.textContent = 'Loading...';
        }

        function showSummaryError(message) {
            summaryElements.reference.textContent = 'Error loading data';
            summaryElements.service.textContent = 'Error loading data';
            summaryElements.datetime.textContent = 'Error loading data';
            summaryElements.contact.textContent = 'Error loading data';
            summaryElements.age.textContent = 'Error loading data';
            alert(message);
        }

        function animateSuccessIcon() {
            const checkmarkCircle = document.querySelector('.checkmark-circle');
            if (checkmarkCircle) {
                setTimeout(() => checkmarkCircle.classList.add('animate'), 500);
            }
        }

        function getChecklistContent(serviceName, age) {
            const isOctogenarian = age !== null && age >= 80;
            const isRegular = serviceName && (serviceName.includes('Delayed Regular Non-BRAP') || serviceName.includes('Delayed Birth Registration (Regular)'));
            const isBrap = serviceName && (serviceName.includes('Delayed BRAP') && !serviceName.includes('Non-BRAP')) || serviceName.includes('Delayed Birth Registration under Birth Registration Assistance Project (BRAP)');

            if (isOctogenarian) {
                // Octogenarian checklist (same for both Regular and BRAP)
                return `
                                                    <h2>Required Documents Checklist</h2>
                                                    <p><strong>OCTOGENARIAN CHECKLIST (80 years old and above)</strong></p>
                                                    <p>If you are 80 years old and above, please use this simplified checklist:</p>
                                                    <ol>
                                                        <li>Municipal Form No. 102 (4 Copies) - prepared at LCRO</li>
                                                        <li>Municipal Form No. 102 Attachment (Islam, if applicable, 4 Copies)</li>
                                                        <li>Civil Registry Form 1C/Birth Destroyed (issued by LCRO)</li>
                                                        <li>Negative Omnibus Certification/Negative Certification from PSA (processed at LCRO)</li>
                                                        <li>Affidavit for Delayed Registration (at back of COLB)</li>
                                                        <li>Affidavit of Two (2) Disinterested Persons</li>
                                                        <li>Certificate of Indigency (issued by Punong Barangay)</li>
                                                        <li>Any proof of identity:
                                                            <ul>
                                                                <li>a) Barangay Certification</li>
                                                                <li>b) Voter's Record/Certification</li>
                                                                <li>c) Member's Data Record (Senior Citizen, PhilHealth, SSS, GSIS)</li>
                                                                <li>d) Any Government Issued ID</li>
                                                            </ul>
                                                        </li>
                                                        <li>Special Power of Attorney/Authorization Letter (if applicable)</li>
                                                        <li>Valid ID of Authorizing person and authorized person (if applicable)</li>
                                                        <li>Others (if applicable)</li>
                                                        <li>National ID</li>
                                                        <li>2x2 picture</li>
                                                    </ol>
                                                `;
            } else if (isRegular) {
                // Regular service complete checklist (79 and below)
                return `
                                                    <h2>Required Documents Checklist</h2>
                                                    <p><strong>COMPLETE CHECKLIST - Delayed Birth Registration (Regular)</strong></p>
                                                    <ol>
                                                        <li>Municipal Form No. 102 (4 Copies)</li>
                                                        <li>Municipal Form No. 102 Attachment (Islam, if applicable, 4 Copies)</li>
                                                        <li>Civil Registry Form 1B or 1C</li>
                                                        <li>Negative Certification from PSA</li>
                                                        <li>Affidavit for Delayed Registration</li>
                                                        <li>Any 2 documentary evidences:
                                                            <ul>
                                                                <li>a) Baptismal Certificate</li>
                                                                <li>b) School Record</li>
                                                                <li>c) Voter's Record/Certification</li>
                                                                <li>d) Medical/Immunization Record</li>
                                                                <li>e) PhilHealth/SSS/GSIS Record</li>
                                                                <li>f) Barangay Captain's Certification</li>
                                                                <li>g) Other valid documents</li>
                                                            </ul>
                                                        </li>
                                                        <li>National ID (1 year old and above)</li>
                                                        <li>Government Issued ID of owner and applicant</li>
                                                        <li>Updated CEDULA</li>
                                                        <li>Certificate of Marriage (if married, PSA Copy)</li>
                                                        <li>Certificate of Residency</li>
                                                        <li>Death Certificate (if owner deceased, PSA Copy)</li>
                                                        <li>Affidavit of Undertaking (if owner deceased)</li>
                                                        <li>Any 2 evidences of Parents' Identity:
                                                            <ul>
                                                                <li>a) Parents' Birth Certificate</li>
                                                                <li>b) Parents' Government ID</li>
                                                                <li>c) Parents' Marriage Certificate</li>
                                                                <li>d) Parents' Death Certificate</li>
                                                            </ul>
                                                        </li>
                                                        <li>Personal Appearance (18 years old and above)</li>
                                                        <li>Personal Appearance of Parents (Marital Minor)</li>
                                                        <li>Personal Appearance of Mother (Non-marital minor)</li>
                                                        <li>Affidavit/Sworn Statement (Mother's whereabouts)</li>
                                                        <li>2x2 Photo (white background, 4 copies, within 3 months)</li>
                                                        <li>Affidavit of Admission of Paternity (if applicable)</li>
                                                        <li>AUSF under RA 9255 (if applicable, register within 20 days)</li>
                                                        <li>Sworn Attestation (for child 7-17 years old)</li>
                                                        <li>Affidavit of Acknowledgement (non-marital child before Aug 3, 1988)</li>
                                                        <li>Birth Certificate of Siblings (if applicable)</li>
                                                        <li>Affidavit of Two Disinterested Persons</li>
                                                        <li>For Foreigner Parent (if applicable):
                                                            <ul>
                                                                <li>a) Parents' Marriage Certificate</li>
                                                                <li>b) Parents' Birth Certificate</li>
                                                                <li>c) Valid Passport/BI Clearance/ACR I-Card</li>
                                                            </ul>
                                                        </li>
                                                        <li>Certification from MCR (PSA Form No.1, 2024) - Out of Town</li>
                                                        <li>Special Power of Attorney/Authorization Letter</li>
                                                        <li>Valid ID of Authorizing person and authorized person</li>
                                                    </ol>
                                                `;
            } else if (isBrap) {
                // BRAP service complete checklist (79 and below)
                return `
                                                    <h2>Required Documents Checklist</h2>
                                                    <p><strong>COMPLETE CHECKLIST - BRAP (79 years old and below)</strong></p>
                                                    <ol>
                                                        <li>Municipal Form No. 102 (4 Copies)
                                                            <br><em>Note: For Home Birth, prepared at LCRO</em></li>
                                                        <li>Municipal Form No. 102 Attachment (Islam, if applicable, 4 Copies)</li>
                                                        <li>Civil Registry Form 1B or 1C</li>
                                                        <li>Negative Omnibus Certification/Negative Certification from PSA (processed at LCRO)</li>
                                                        <li>Affidavit for Delayed Registration (at back of COLB)</li>
                                                        <li>Affidavit of Two (2) Disinterested Persons</li>
                                                        <li>Certificate of Indigency (issued by Punong Barangay)</li>
                                                        <li>Any proof of identity:
                                                            <ul>
                                                                <li>a) Barangay Certification</li>
                                                                <li>b) Medical/Immunization Record</li>
                                                                <li>c) Baptismal Certificate (Original from Church)</li>
                                                                <li>d) School Record (Certified Copy)</li>
                                                                <li>e) Voter's Record/Certification</li>
                                                                <li>f) Member's Data Record (PhilHealth, SSS, GSIS, Senior Citizen)</li>
                                                                <li>g) Any Government Issued ID</li>
                                                                <li>h) Others (if applicable)</li>
                                                            </ul>
                                                        </li>
                                                        <li>Personal Appearance (18 years old and above)</li>
                                                        <li>Personal Appearance of Parents (Marital Minor)
                                                            <br><em>In default: Grandparents, Oldest Sibling (21+), or Actual Custodian (21+)</em></li>
                                                        <li>Personal Appearance of Mother (Non-marital minor)</li>
                                                        <li>Affidavit/Sworn Statement (Mother's whereabouts if non-appearance)</li>
                                                        <li>Affidavit of Admission of Paternity (if applicable, at back of COLB/PHI)</li>
                                                        <li>AUSF under RA 9255 (if applicable, register within 20 days)</li>
                                                        <li>Sworn Attestation (for child 7-17 years old)</li>
                                                        <li>Affidavit of Acknowledgement (non-marital child before Aug 3, 1988)</li>
                                                        <li>Special Power of Attorney/Authorization Letter (if applicable)</li>
                                                        <li>Valid ID of Authorizing person and authorized person (if applicable)</li>
                                                    </ol>
                                                `;
            } else {
                return '<p>Checklist not available for this service type.</p>';
            }
        }

        function printSummary() {
            // Get service name, reference number, and age from current page or session
            const serviceName = document.getElementById('summary-service').textContent;
            const referenceNumber = document.getElementById('summary-reference').textContent;
            let age = null;

            // Try to get age from sessionStorage
            try {
                const personalDetails = JSON.parse(sessionStorage.getItem('personalDetails') || '{}');
                age = personalDetails.age || null;
            } catch (e) {
                console.warn('Could not get age from sessionStorage:', e);
            }

            // Get checklist content
            const checklistContent = getChecklistContent(serviceName, age);

            // Create a print-friendly version
            const printContent = `
                                                            <html>
                                                                <head>
                                                                    <title>Appointment Summary & Checklist</title>
                                                                    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
                                                                    <style>
                                                                        * { margin: 0; padding: 0; box-sizing: border-box; }
                                                                        body { 
                                                                            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
                                                                            font-size: 14px;
                                                                            line-height: 1.6; 
                                                                            color: #333;
                                                                            margin: 40px auto;
                                                                            max-width: 800px;
                                                                            padding: 0 20px;
                                                                        }
                                                                        .header { 
                                                                            text-align: center; 
                                                                            margin-bottom: 40px; 
                                                                            padding-bottom: 24px;
                                                                            border-bottom: 1px solid #e5e5e5;
                                                                        }
                                                                        .header h1 { 
                                                                            font-size: 24px;
                                                                            font-weight: 600;
                                                                            color: #333; 
                                                                            margin-bottom: 8px;
                                                                            letter-spacing: -0.5px;
                                                                        }
                                                                        .header p { 
                                                                            font-size: 13px;
                                                                            color: #666;
                                                                            font-weight: 400;
                                                                            line-height: 1.5;
                                                                        }
                                                                        .summary { 
                                                                            margin-bottom: 40px; 
                                                                            padding: 24px 0;
                                                                        }
                                                                        .summary h2 { 
                                                                            font-size: 16px;
                                                                            font-weight: 600;
                                                                            color: #333; 
                                                                            margin-bottom: 20px;
                                                                            text-transform: uppercase;
                                                                            letter-spacing: 0.5px;
                                                                        }
                                                                        .summary-item { 
                                                                            margin-bottom: 16px; 
                                                                            padding-bottom: 16px;
                                                                            border-bottom: 1px solid #f0f0f0;
                                                                            display: flex;
                                                                        }
                                                                        .summary-item:last-child { 
                                                                            border-bottom: none; 
                                                                            margin-bottom: 0;
                                                                            padding-bottom: 0;
                                                                        }
                                                                        .summary-label { 
                                                                            font-weight: 500; 
                                                                            color: #555; 
                                                                            min-width: 120px;
                                                                            font-size: 14px;
                                                                        }
                                                                        .summary-value {
                                                                            color: #333;
                                                                            flex: 1;
                                                                            font-size: 14px;
                                                                        }
                                                                        .checklist { 
                                                                            margin-top: 40px; 
                                                                            padding: 0;
                                                                        }
                                                                        .checklist h2 { 
                                                                            font-size: 16px;
                                                                            font-weight: 600;
                                                                            color: #333; 
                                                                            margin-bottom: 12px;
                                                                            text-transform: uppercase;
                                                                            letter-spacing: 0.5px;
                                                                        }
                                                                        .checklist p {
                                                                            font-size: 14px;
                                                                            color: #555;
                                                                            margin-bottom: 16px;
                                                                            line-height: 1.6;
                                                                        }
                                                                        .checklist p strong {
                                                                            font-weight: 600;
                                                                            color: #333;
                                                                        }
                                                                        .checklist ol { 
                                                                            padding-left: 24px; 
                                                                            margin: 0;
                                                                        }
                                                                        .checklist ul { 
                                                                            padding-left: 20px; 
                                                                            margin-top: 8px;
                                                                            margin-bottom: 0;
                                                                        }
                                                                        .checklist li { 
                                                                            margin-bottom: 10px;
                                                                            font-size: 14px;
                                                                            line-height: 1.6;
                                                                            color: #333;
                                                                        }
                                                                        .checklist li em {
                                                                            font-style: italic;
                                                                            color: #666;
                                                                            font-size: 13px;
                                                                        }
                                                                        .footer { 
                                                                            margin-top: 50px; 
                                                                            padding-top: 24px;
                                                                            border-top: 1px solid #e5e5e5; 
                                                                            font-size: 12px; 
                                                                            color: #666; 
                                                                            text-align: center;
                                                                            line-height: 1.8;
                                                                        }
                                                                        .footer p {
                                                                            margin-bottom: 4px;
                                                                        }
                                                                        .footer p:last-child {
                                                                            margin-top: 12px;
                                                                            margin-bottom: 0;
                                                                        }
                                                                        @media print {
                                                                            body { 
                                                                                margin: 0;
                                                                                padding: 20px;
                                                                            }
                                                                            .header { 
                                                                                page-break-after: avoid; 
                                                                            }
                                                                            .checklist { 
                                                                                page-break-inside: avoid; 
                                                                            }
                                                                            .summary-item {
                                                                                page-break-inside: avoid;
                                                                            }
                                                                        }
                                                                    </style>
                                                                </head>
                                                                <body>
                                                                    <div class="header">
                                                                        <h1>Appointment Summary</h1>
                                                                        <p>City Civil Registry Office<br>Municipality of Alaminos City</p>
                                                                    </div>
                                                                     <div class="summary">
                                                                         <h2>Appointment Details</h2>
                                                                         <div class="summary-item">
                                                                             <span class="summary-label">Reference Number: </span> 
                                                                             <span class="summary-value">${referenceNumber}</span>
                                                                         </div>
                                                                         <div class="summary-item">
                                                                             <span class="summary-label">Service:</span> 
                                                                             <span class="summary-value">${serviceName}</span>
                                                                         </div>
                                                                         <div class="summary-item">
                                                                             <span class="summary-label">Date & Time:</span> 
                                                                             <span class="summary-value">${document.getElementById('summary-datetime').textContent}</span>
                                                                         </div>
                                                                         <div class="summary-item">
                                                                             <span class="summary-label">Contact:</span> 
                                                                             <span class="summary-value">${document.getElementById('summary-contact').textContent}</span>
                                                                         </div>
                                                                     </div>
                                                                    <div class="checklist">
                                                                        ${checklistContent}
                                                                    </div>
                                                                    <div class="footer">
                                                                        <p><strong>City Civil Registry Office</strong></p>

                                                                        <p>Please bring all required documents on your appointment day.</p>
                                                                    </div>
                                                                </body>
                                                            </html>
                                                        `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        // Add some interactive effects
        document.querySelectorAll('.info-card').forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-5px)';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
            });
        });

        document.querySelectorAll('.step-item').forEach(step => {
            step.addEventListener('mouseenter', function () {
                this.style.transform = 'translateX(10px)';
            });

            step.addEventListener('mouseleave', function () {
                this.style.transform = 'translateX(0)';
            });
        });
    </script>
@endsection