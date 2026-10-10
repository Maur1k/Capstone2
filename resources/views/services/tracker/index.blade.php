<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Tracker - Click2Serve</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/tracker.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/landing-kiosk.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @include('home.landing')
    @php
        $progressIndicatorsEnabled = \App\Models\Setting::getBoolean('feature.progress_indicator', true);
    @endphp
    <!-- Toast Notification Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="feedback-modal-overlay">
        <div class="feedback-modal-content">
            <button class="feedback-modal-close" onclick="closeFeedbackModal()" aria-label="Close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="feedback-modal-header">
                <h2>Rate Your Experience</h2>
                <p>How would you rate your experience with Click2Serve?</p>
            </div>
            <div class="feedback-modal-body">
                <div class="star-rating-container">
                    <svg style="position: absolute; width: 0; height: 0;">
                        <defs>
                            <linearGradient id="starGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#F3BE7C;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#E8A85A;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="star-rating" id="starRating">
                        <button type="button" class="star" data-rating="1" aria-label="1 star">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button type="button" class="star" data-rating="2" aria-label="2 stars">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button type="button" class="star" data-rating="3" aria-label="3 stars">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button type="button" class="star" data-rating="4" aria-label="4 stars">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button type="button" class="star" data-rating="5" aria-label="5 stars">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <p class="rating-text" id="ratingText">Tap to rate</p>
                </div>
                <div class="feedback-message-container">
                    <label for="feedbackMessage" class="feedback-label">Your feedback (optional)</label>
                    <textarea id="feedbackMessage" class="feedback-message-input" placeholder="Share your thoughts about your experience..." rows="4"></textarea>
                </div>
            </div>
            <div class="feedback-modal-footer">
                <button type="button" class="feedback-submit-btn" id="feedbackSubmitBtn" onclick="submitFeedback()" disabled>
                    <span class="btn-text">Submit Feedback</span>
                </button>
            </div>
        </div>
    </div>

    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <div class="header">
                    <button class="back-button" onclick="goBack()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <h1 class="page-title">Service Tracker</h1>
                </div>

                <div class="main-content tracker-layout">
                    <div class="tracker-panel">
                        <div class="tracker-header">
                            <div class="client">

                                <div class="client-name" id="clientName">Loading...</div>
                                <div class="client-desc" id="serviceDesc">Loading service details...</div>
                            </div>
                            <div class="meta">
                                <div class="ref">Reference NO. <span id="refNo">Loading...</span></div>
                                <div class="queue-number" id="queueNumberContainer" style="display: none;">
                                    <strong>Queue NO. <span id="queueNumber">-</span></strong>
                                </div>
                                <div class="status">
                                    <span class="dot" id="statusDot"></span>
                                    <span id="statusText">Loading...</span>
                                </div>
                                <strong>Date Submitted: <span id="dateSubmitted">-</span></strong>
                                <strong>Time Submitted: <span id="timeSubmitted">-</span></strong>
                                <div class="print-receipt-container" style="margin-top: 15px;">
                                    <button id="printReceiptBtn" class="print-receipt-btn" onclick="printReceipt()"
                                        style="display: none;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                                            <path d="M6 9V2H18V9" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M6 18H4C2.89543 18 2 17.1046 2 16V11C2 9.89543 2.89543 9 4 9H20C21.1046 9 22 9.89543 22 11V16C22 17.1046 21.1046 18 20 18H18"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M6 14H18V22H6V14Z" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Print Receipt
                                    </button>
                                </div>
                            </div>
                            @if ($progressIndicatorsEnabled)
                                <div class="next-step-card" id="nextStepCard" style="display: none;">
                                    <div class="next-step-header">
                                        <div class="next-step-label-wrapper">
                                            <span class="next-step-icon">→</span>
                                            <span class="label" id="nextStepLabel">Next Step</span>
                                        </div>
                                        <h2 class="status" id="nextStepStatus">Preparing...</h2>
                                    </div>
                                    <p class="next-step-description" id="nextStepDescription">You're on track—stand by for
                                        the next milestone.</p>
                                    <div class="next-step-instructions" id="nextStepInstructions">
                                        <!-- Bulleted instructions will be populated here -->
                                    </div>
                                    <div class="completion-message" id="completionMessage" style="display: none;">
                                        <div class="completion-content">
                                            <h3>Service Completed Successfully!</h3>
                                            <p>Your request has been fully processed. No further action is required.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="status-timeline" id="statusTimeline" style="display: none;">
                            <h3>Progress Timeline</h3>
                            <div class="timeline" id="timelineContainer">
                                <!-- Timeline items will be dynamically generated here -->
                            </div>
                        </div>


                        <!-- Requirements Tab (only shown when status is checklist_given) -->
                        <div class="requirements-section" id="requirementsSection" style="display: none;">
                            <div class="requirements-header">
                                <h3>Required Documents</h3>
                                <p>Please prepare the following documents for your service request:</p>
                            </div>
                            <div class="requirements-content" id="requirementsContent">
                                <!-- Requirements will be loaded here -->
                            </div>
                        </div>

                        <div class="tracker-actions">
                            <button class="secondary-btn" onclick="goHome()">Home</button>
                            <button class="secondary-btn feedback-btn" onclick="showFeedbackModal()" style="background: linear-gradient(135deg, #F3BE7C 0%, #E8A85A 100%); color: white; border: 2px solid #fff;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px; vertical-align: middle;">
                                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Feedback
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button class="chat" aria-label="chat" onclick="navigateToChatbot()">
                <img src="{{ asset('assets/icons/chatbot.png') }}" alt="chat" />
                <span>Chat</span>
            </button>
            <div class="footer">Powered by: Click2Serve</div>
        </div>
    </div>

    <script>
        function goBack() {
            window.location.href = '/';
        }

        function goHome() {
            window.location.href = '/';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }


        function showErrorMessage(message) {
            const overlay = document.createElement('div');
            overlay.className = 'floating-message-overlay';

            const messageDiv = document.createElement('div');
            messageDiv.className = 'floating-message error';
            messageDiv.innerHTML = `
                <div class="message-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Error</h3>
                <p>${message}</p>
                <div class="button-group">
                    <button class="btn btn-danger" onclick="closeFloatingMessage()">OK</button>
                </div>
            `;

            overlay.appendChild(messageDiv);
            document.body.appendChild(overlay);
        }

        function closeFloatingMessage() {
            const overlay = document.querySelector('.floating-message-overlay');
            if (overlay) {
                overlay.remove();
            }
        }

        const featureToggles = {
            progressIndicators: @json($progressIndicatorsEnabled),
        };

        // Load tracking data when page loads
        document.addEventListener('DOMContentLoaded', function () {
            // Check URL parameters first (for direct access)
            const urlParams = new URLSearchParams(window.location.search);
            const trackingNumber = urlParams.get('tracking');

            // Check session storage (from form submission)
            const trackingData = sessionStorage.getItem('currentTrackingData');
            const isNewSubmission = sessionStorage.getItem('new_submission') === 'true';

            if (trackingNumber) {
                // Clear any existing session storage when accessing via URL
                sessionStorage.removeItem('currentTrackingData');
                // Always fetch fresh data from API when tracking number is in URL
                fetchTrackingData(trackingNumber).then((data) => {
                    if (!data) return;
                    
                    // Check if this is a new submission and schedule feedback modal
                    if (isNewSubmission) {
                        console.log('New submission detected, scheduling feedback modal');
                        sessionStorage.removeItem('new_submission');
                        scheduleFeedbackModal();
                    } else {
                        // Also check if request was created recently (within last 5 minutes)
                        if (data.created_at) {
                            const createdAt = new Date(data.created_at);
                            const now = new Date();
                            const minutesDiff = (now - createdAt) / (1000 * 60);
                            console.log('Request created ' + minutesDiff.toFixed(2) + ' minutes ago');
                            if (minutesDiff <= 5) {
                                console.log('Recent submission detected (within 5 minutes), scheduling feedback modal');
                                scheduleFeedbackModal();
                            }
                        }
                    }
                });
            } else if (trackingData) {
                // Only use session storage if no tracking number in URL
                displayTrackingData(JSON.parse(trackingData));
                // Check if this is a new submission
                if (isNewSubmission) {
                    console.log('New submission detected from session storage, scheduling feedback modal');
                    sessionStorage.removeItem('new_submission');
                    // Schedule feedback modal after 30 seconds for new submissions
                    scheduleFeedbackModal();
                } else {
                    // Also check if request was created recently
                    const parsedData = JSON.parse(trackingData);
                    if (parsedData.created_at) {
                        const createdAt = new Date(parsedData.created_at);
                        const now = new Date();
                        const minutesDiff = (now - createdAt) / (1000 * 60);
                        if (minutesDiff <= 5) {
                            console.log('Recent submission detected (within 5 minutes), scheduling feedback modal');
                            scheduleFeedbackModal();
                        }
                    }
                }
            } else {
                // No tracking data available
                displayError('No tracking information available.');
            }

            // Set up auto-refresh for status updates (every 15 seconds for real-time updates)
            setInterval(function () {
                const currentData = sessionStorage.getItem('currentTrackingData');
                if (currentData) {
                    const data = JSON.parse(currentData);
                    if (data.tracking_number) {
                        refreshTrackingStatus(data.tracking_number);
                    }
                }
            }, 15000); // 15 seconds for better responsiveness
        });

        function displayTrackingData(data) {
            console.log('Displaying tracking data:', data);

            // Update client information
            document.getElementById('clientName').textContent = data.full_name || 'Unknown Client';
            // Show service type (specific service) if available, otherwise show service name (department)
            let serviceDisplay = data.service_type || data.service_name || data.document_type || 'Service Request';
            // Format display names for civil registry services
            serviceDisplay = formatServiceDisplayName(serviceDisplay);
            document.getElementById('serviceDesc').textContent = `Requesting for ${serviceDisplay}`;

            // Update tracking information
            document.getElementById('refNo').textContent = data.tracking_number || 'N/A';

            // Update queue number (only if feature is enabled and queue number exists)
            // Note: The backend should only send queue_number if feature is enabled
            if (data.queue_number) {
                document.getElementById('queueNumber').textContent = data.queue_number;
                document.getElementById('queueNumberContainer').style.display = 'block';
            } else {
                document.getElementById('queueNumberContainer').style.display = 'none';
            }

            // Update status
            const status = data.status || 'inquiry';
            document.getElementById('statusText').textContent = formatStatusText(status);
            updateStatusDot(status);

            // Update date and time submitted
            document.getElementById('dateSubmitted').textContent = data.date || new Date().toLocaleDateString('en-PH', { timeZone: 'Asia/Manila' });
            document.getElementById('timeSubmitted').textContent = formatTime12Hour(data.time);

            // Show timeline
            document.getElementById('statusTimeline').style.display = 'block';
            updateTimelineWithStatusUpdates(data, status);
            updateNextStep(status, data);

            // Show print receipt button
            if (data.tracking_number) {
                document.getElementById('printReceiptBtn').style.display = 'inline-flex';
                // Store tracking data for print function
                window.currentTrackingData = data;
            } else {
                document.getElementById('printReceiptBtn').style.display = 'none';
            }

            // Show requirements section if status is checklist_given
            if (status === 'checklist_given' && data.requirements_checklist) {
                showRequirementsSection(data.requirements_checklist);
            } else {
                document.getElementById('requirementsSection').style.display = 'none';
            }


            // Store data for future use
            sessionStorage.setItem('currentTrackingData', JSON.stringify(data));
        }

        const statusDetails = {
            inquiry: {
                label: 'INQUIRY',
                description: 'Request submitted and received; awaiting initial review.'
            },
            submitted: {
                label: 'SUBMITTED',
                description: 'Request has been logged and is awaiting processing by the office.'
            },
            to_be_encoded_brap: {
                label: 'TO BE ENCODED BRAP',
                description: 'Details are queued for encoding under the Birth Registration Assistance Project.'
            },
            checklist_given: {
                label: 'CHECKLIST GIVEN',
                description: 'Checklist of required documents has been provided; please complete all items.'
            },
            incomplete_requirements: {
                label: 'INCOMPLETE REQUIREMENTS',
                description: 'Submitted documents are incomplete; provide the missing requirements to proceed.'
            },
            accepted: {
                label: 'ACCEPTED',
                description: 'Request has passed initial validation and entered the processing queue.'
            },
            psa_processing: {
                label: 'PSA PROCESSING',
                description: 'Your PSA-related documents are being processed with the issuing agency.'
            },
            registered: {
                label: 'REGISTERED',
                description: 'Record has been officially registered in the system/PSA.'
            },
            processing: {
                label: 'PROCESSING',
                description: 'Your request is actively being worked on by the assigned office.'
            },
            in_progress: {
                label: 'IN PROGRESS',
                description: 'The team is currently working on your request.'
            },
            under_review: {
                label: 'UNDER REVIEW',
                description: 'Documents and details are under review for validation and approval.'
            },
            completed: {
                label: 'COMPLETED',
                description: 'Service is fully completed; no further action is required.'
            },
            ready: {
                label: 'READY',
                description: 'Your request is ready for the next action or release.'
            },
            ready_for_pickup: {
                label: 'READY FOR PICKUP',
                description: 'Documents are prepared and ready to be collected.'
            },
            rejected: {
                label: 'REJECTED',
                description: 'Request was declined; please coordinate with the office for details.'
            },
            cancelled: {
                label: 'CANCELLED',
                description: 'Request has been cancelled per client or office instruction.'
            },
            waiting_for_psa_negative_certificate: {
                label: 'WAITING FOR PSA NEGATIVE CERTIFICATE',
                description: 'Awaiting issuance of the PSA negative certificate.'
            },
            positive_result: {
                label: 'POSITIVE RESULT',
                description: 'PSA returned a positive result; follow the next instructions provided.'
            },
            negative_result: {
                label: 'NEGATIVE RESULT',
                description: 'PSA negative certificate obtained; processing continues.'
            },
            accepted_octogenarian: {
                label: 'ACCEPTED (OCTOGENARIAN)',
                description: 'Accepted under the Octogenarian program; processing will proceed.'
            },
            verified_with_problem: {
                label: 'VERIFIED WITH PROBLEM',
                description: 'Verification found issues; additional action or clarification is needed.'
            },
            for_compliance: {
                label: 'FOR COMPLIANCE',
                description: 'Additional compliance is required from the client to move forward.'
            },
            posting: {
                label: '10 DAYS POSTING',
                description: 'Official posting/notice period of 10 days is ongoing.'
            },
            ready_for_approval: {
                label: 'READY FOR APPROVAL',
                description: 'All checks complete; awaiting final approval.'
            },
            encoded_brap: {
                label: 'ENCODED BRAP',
                description: 'BRAP encoding has been completed; proceeding to the next step.'
            }
        };

        function getStatusInfo(status) {
            const key = (status || '').toLowerCase();
            const fallbackLabel = status ? status.toUpperCase() : 'STATUS';
            return statusDetails[key] || {
                label: fallbackLabel,
                description: 'Status update is in progress.'
            };
        }

        function formatStatusText(status) {
            return getStatusInfo(status).label;
        }

        function formatStatusDescription(status) {
            return getStatusInfo(status).description;
        }

        function formatServiceType(serviceType) {
            return serviceType.replace(/_/g, ' ').toUpperCase();
        }

        function formatServiceDisplayName(serviceName) {
            // Format display names for civil registry services
            if (serviceName.includes('Delayed Regular Non-BRAP') || serviceName.includes('Delayed Regular Non-BRAP Birth Registration Assistance Project')) {
                return 'Delayed Birth Registration (Regular)';
            } else if (serviceName.includes('Delayed BRAP') || serviceName.includes('Delayed BRAP Birth Registration Assistance Project')) {
                return 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
            }
            return serviceName;
        }

        /**
         * Format time to 12-hour format with AM/PM in Asia/Manila timezone
         */
        function formatTime12Hour(timeString) {
            if (timeString) {
                // Check if time already has AM/PM (already in 12-hour format)
                if (timeString.match(/AM|PM/i)) {
                    // Already in 12-hour format, return as is
                    return timeString.trim();
                }
                
                // If time is provided, try to parse and format it
                try {
                    // Try to parse the time string (could be in 24-hour format like "15:49:52")
                    const timeParts = timeString.match(/(\d{1,2}):(\d{2})(?::(\d{2}))?/);
                    if (timeParts) {
                        let hours = parseInt(timeParts[1]);
                        const minutes = timeParts[2];
                        
                        // Convert 24-hour format to 12-hour format with AM/PM
                        const ampm = hours >= 12 ? 'PM' : 'AM';
                        hours = hours % 12;
                        hours = hours === 0 ? 12 : hours; // 0 should be 12 (midnight/noon)
                        
                        return `${hours.toString().padStart(2, '0')}:${minutes} ${ampm}`;
                    }
                } catch (e) {
                    // If parsing fails, fall through to default
                }
            }
            
            // Default: use current time in 12-hour format with Asia/Manila timezone
            return new Date().toLocaleTimeString('en-PH', { 
                timeZone: 'Asia/Manila', 
                hour12: true, 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }

        function updateStatusDot(status) {
            const dot = document.getElementById('statusDot');
            dot.className = 'dot'; // Reset classes

            switch (status.toLowerCase()) {
                case 'inquiry':
                case 'submitted':
                case 'pending':
                case 'waiting_for_psa_negative_certificate':
                    dot.classList.add('pending');
                    break;
                case 'checklist_given':
                case 'encoded_brap':
                case 'in_progress':
                case 'under_review':
                case 'processing':
                case 'psa_processing':
                case 'posting':
                case 'for_compliance':
                    dot.classList.add('progress');
                    break;
                case 'accepted':
                case 'registered':
                case 'positive_result':
                case 'completed':
                case 'ready':
                case 'ready_for_pickup':
                case 'accepted_octogenarian':
                    dot.classList.add('completed');
                    break;
                case 'incomplete_requirements':
                case 'negative_result':
                case 'rejected':
                case 'cancelled':
                case 'verified_with_problem':
                    dot.classList.add('rejected');
                    break;
                default:
                    dot.classList.add('pending');
            }
        }

        function updateTimeline(status) {
            const steps = ['inquiry', 'under_review', 'processing', 'completed'];
            const currentStep = getStepFromStatus(status.toLowerCase());
            const currentTime = new Date().toLocaleString();

            steps.forEach((step, index) => {
                const element = document.getElementById(`step${index + 1}`);
                const timeElement = element.querySelector('small');

                // Remove all classes first
                element.classList.remove('active', 'completed');

                if (index < currentStep) {
                    // Completed steps
                    element.classList.add('completed');
                    if (timeElement && !timeElement.textContent.includes('Completed')) {
                        timeElement.textContent = 'Completed';
                    }
                } else if (index === currentStep) {
                    // Current step - check if it's the final step and status is registered
                    if (index === steps.length - 1 && (status.toLowerCase() === 'registered' || status.toLowerCase() === 'completed')) {
                        // Final step with registered/completed status should be marked as completed
                        element.classList.add('completed');
                        if (timeElement) {
                            timeElement.textContent = 'Completed';
                        }
                    } else {
                        // Current active step (not final or not registered)
                        element.classList.add('active');
                        if (timeElement) {
                            timeElement.textContent = 'In Progress';
                        }
                    }
                } else {
                    // Future steps
                    if (timeElement) {
                        timeElement.textContent = 'Pending';
                    }
                }
            });

            // Update the timeline line progress
            updateTimelineProgress(currentStep, steps.length);
        }

        function updateTimelineWithStatusUpdates(data, currentStatus) {
            const timelineContainer = document.getElementById('timelineContainer');
            const statusUpdates = data.status_updates || [];
            const currentStatusLower = currentStatus.toLowerCase();

            // Clear existing timeline
            timelineContainer.innerHTML = '';

            if (statusUpdates.length === 0) {
                // For newly submitted requests, create a timeline item from the current status
                // This ensures the timeline shows even when no status updates exist yet
                const currentStatusLabel = formatStatusText(currentStatus);
                const currentStatusDescription = formatStatusDescription(currentStatus);
                const isFinalStatus = ['registered', 'completed', 'ready', 'ready_for_pickup'].includes(currentStatusLower);
                const isIncomplete = currentStatusLower === 'incomplete_requirements';

                let statusClass = 'active';
                let statusBadgeClass = 'active';
                let statusBadgeText = 'In Progress';

                if (isIncomplete) {
                    statusClass = 'incomplete';
                    statusBadgeClass = 'incomplete';
                    statusBadgeText = 'Incomplete';
                } else if (isFinalStatus) {
                    statusClass = 'completed';
                    statusBadgeClass = 'completed';
                    statusBadgeText = 'Completed';
                }

                const timelineItem = document.createElement('div');
                timelineItem.className = 'timeline-item';
                timelineItem.classList.add(statusClass);

                timelineItem.innerHTML = `
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <strong class="timeline-title">${currentStatusLabel}</strong>
                            <span class="timeline-status-badge ${statusBadgeClass}">
                                ${statusBadgeText}
                            </span>
                        </div>
                        <p class="timeline-description">${currentStatusDescription}</p>
                        <div class="timeline-dates">
                            <small class="timeline-date">
                                <span class="date-icon">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </span>
                                ${data.date || new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                            </small>
                            <small class="timeline-time">${data.time || new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</small>
                        </div>
                        ${data.remarks ? `<div class="timeline-remarks">
                            <p>${escapeHtml(data.remarks)}</p>
                        </div>` : ''}
                    </div>
                `;

                timelineContainer.appendChild(timelineItem);
                updateTimelineProgress(0, 1, isIncomplete);
                return;
            }

            // Sort status updates by created_at (oldest first)
            const sortedUpdates = [...statusUpdates].sort((a, b) => {
                return new Date(a.created_at) - new Date(b.created_at);
            });

            // Track which statuses we've seen
            const seenStatuses = new Set();
            const uniqueUpdates = [];

            // Get unique statuses (keep the latest occurrence of each status)
            for (let i = sortedUpdates.length - 1; i >= 0; i--) {
                const update = sortedUpdates[i];
                if (!seenStatuses.has(update.status)) {
                    seenStatuses.add(update.status);
                    uniqueUpdates.unshift(update); // Add to beginning to maintain chronological order
                }
            }

            // Create timeline items
            uniqueUpdates.forEach((update, index) => {
                const isLast = index === uniqueUpdates.length - 1;
                const isCurrent = update.status.toLowerCase() === currentStatusLower;
                const isCompleted = !isCurrent && index < uniqueUpdates.length - 1;
                const isIncompleteRequirements = update.status.toLowerCase() === 'incomplete_requirements';

                // Final/completed statuses that should always show as "Completed" even when current
                const finalStatuses = ['registered', 'completed', 'ready', 'ready_for_pickup'];
                const isFinalStatus = finalStatuses.includes(update.status.toLowerCase());

                // Special handling for incomplete_requirements:
                // - If it's the current status, show as incomplete/error (red)
                // - If it's in the past (status has moved forward), show as completed (green)
                let statusClass = 'pending';
                let statusBadgeClass = 'pending';
                let statusBadgeText = 'Pending';

                if (isIncompleteRequirements && isCurrent) {
                    // Current status is incomplete_requirements - show as incomplete/error
                    statusClass = 'incomplete';
                    statusBadgeClass = 'incomplete';
                    statusBadgeText = 'Incomplete';
                } else if (isCurrent && isFinalStatus) {
                    // Current status is a final/completed status - show as completed
                    statusClass = 'completed';
                    statusBadgeClass = 'completed';
                    statusBadgeText = 'Completed';
                } else if (isCurrent) {
                    // Current status (but not incomplete_requirements or final status)
                    statusClass = 'active';
                    statusBadgeClass = 'active';
                    statusBadgeText = 'In Progress';
                } else if (isCompleted) {
                    // Past status - show as completed
                    statusClass = 'completed';
                    statusBadgeClass = 'completed';
                    statusBadgeText = 'Completed';
                }

                // Format date and time
                const updateDate = new Date(update.created_at);
                const dateStr = updateDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const timeStr = updateDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                const statusDate = update.status_date ? new Date(update.status_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : null;

                // Determine status label
                const statusLabel = formatStatusText(update.status);
                const statusDescription = formatStatusDescription(update.status);

                // Create timeline item
                const timelineItem = document.createElement('div');
                timelineItem.className = 'timeline-item';
                timelineItem.classList.add(statusClass);

                timelineItem.innerHTML = `
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <strong class="timeline-title">${statusLabel}</strong>
                            <span class="timeline-status-badge ${statusBadgeClass}">
                                ${statusBadgeText}
                            </span>
                        </div>
                        <p class="timeline-description">${statusDescription}</p>
                        <div class="timeline-dates">
                            <small class="timeline-date">
                                <span class="date-icon">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </span>
                                ${statusDate || dateStr}
                            </small>
                            ${statusDate && statusDate !== dateStr ? `<small class="timeline-time">Recorded: ${dateStr} at ${timeStr}</small>` : `<small class="timeline-time">${timeStr}</small>`}
                        </div>
                        ${update.remarks ? `<div class="timeline-remarks">
                            <p>${escapeHtml(update.remarks)}</p>
                        </div>` : ''}
                    </div>
                `;

                timelineContainer.appendChild(timelineItem);
            });

            // Update timeline progress
            // For incomplete_requirements, don't count it as progress if it's the current status
            let progressIndex = uniqueUpdates.length - 1;
            const hasIncomplete = currentStatusLower === 'incomplete_requirements';
            if (hasIncomplete && uniqueUpdates.length > 0) {
                // If current status is incomplete_requirements, use the previous status index for progress
                progressIndex = Math.max(0, uniqueUpdates.length - 2);
            }
            const progress = uniqueUpdates.length > 1 ? (progressIndex / (uniqueUpdates.length - 1)) * 100 : 0;
            updateTimelineProgress(progressIndex, uniqueUpdates.length, hasIncomplete);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function updateTimelineProgress(currentStep, totalSteps, hasIncomplete = false) {
            const progress = totalSteps > 1 ? (currentStep / (totalSteps - 1)) * 100 : 0;

            // Update the timeline line color
            const timelineLine = document.querySelector('.timeline');
            if (timelineLine) {
                timelineLine.style.setProperty('--timeline-progress', `${progress}%`);
                // Add class to show incomplete status on timeline line
                if (hasIncomplete) {
                    timelineLine.classList.add('has-incomplete');
                } else {
                    timelineLine.classList.remove('has-incomplete');
                }
            }
        }

        function getStepFromStatus(status) {
            switch (status) {
                case 'inquiry':
                case 'submitted':
                case 'pending':
                    return 0; // Step 1: Inquiry
                case 'to_be_encoded_brap':
                case 'checklist_given':
                case 'under_review':
                case 'reviewing':
                case 'waiting_for_psa_negative_certificate':
                    return 1; // Step 2: Under Review
                case 'incomplete_requirements':
                case 'for_compliance':
                case 'verified_with_problem':
                    return 1; // Step 2: Under Review (requires action)
                case 'processing':
                case 'in_progress':
                case 'accepted':
                case 'accepted_octogenarian':
                case 'psa_processing':
                case 'posting':
                    return 2; // Step 3: Processing
                case 'registered':
                case 'positive_result':
                case 'negative_result':
                case 'ready':
                case 'ready_for_pickup':
                case 'completed':
                    return 3; // Step 4: Completed
                default:
                    return 0; // Default to Step 1
            }
        }

        function updateNextStep(status, trackingData = null) {
            if (!featureToggles.progressIndicators) {
                return;
            }

            const card = document.getElementById('nextStepCard');
            if (!card) {
                return;
            }

            // Use stored tracking data if not provided
            if (!trackingData) {
                const storedData = sessionStorage.getItem('currentTrackingData');
                trackingData = storedData ? JSON.parse(storedData) : window.currentTrackingData || {};
            }

            const nextStep = getNextStepInfo(status.toLowerCase(), trackingData);

            if (!nextStep) {
                card.style.display = 'none';
                return;
            }

            document.getElementById('nextStepStatus').textContent = nextStep.title;
            document.getElementById('nextStepDescription').textContent = nextStep.description;

            // Add special styling for completed/registered status
            const statusLower = status.toLowerCase();
            if (statusLower === 'registered' || statusLower === 'completed') {
                card.classList.add('completed-status');
            } else {
                card.classList.remove('completed-status');
            }

            // Populate bulleted instructions
            const instructionsContainer = document.getElementById('nextStepInstructions');
            if (instructionsContainer && nextStep.instructions && nextStep.instructions.length > 0) {
                instructionsContainer.innerHTML = '<ul class="instruction-list">' +
                    nextStep.instructions.map(instruction =>
                        '<li class="instruction-item">' + instruction + '</li>'
                    ).join('') +
                    '</ul><div class="visual-indicator"></div>';
            } else {
                instructionsContainer.innerHTML = '';
            }

            card.style.display = 'block';
        }

        function getNextStepInfo(status, trackingData = {}) {
            // Check if this is an OtherService request
            const isOtherService = trackingData && (
                (trackingData.department && trackingData.department !== 'Civil Registry Office') ||
                (!trackingData.document_type && trackingData.service_type && !trackingData.service_name)
            );
            const departmentName = isOtherService && trackingData.department ? trackingData.department : 'the office';

            const map = {
                'inquiry': {
                    title: 'Encoding & Verification',
                    description: isOtherService 
                        ? 'Your request is being reviewed and validated by our team.' 
                        : 'Your request is being processed and validated by our team.',
                    instructions: isOtherService ? [
                        'Your request is being reviewed and validated by our team',
                        'Check your SMS notifications regularly for status updates',
                        'Prepare any required documents that may be requested',
                        'Keep your tracking number safe for future reference',
                        'Monitor this tracker page for real-time updates'
                    ] : [
                        'Wait for the official requirements checklist to be prepared',
                        'Check your SMS for updates on your request status',
                        'Prepare your valid ID and other basic documents in advance'
                    ]
                },
                'submitted': {
                    title: 'Encoding & Verification',
                    description: isOtherService 
                        ? 'Your request has been submitted and is being processed by our team.' 
                        : 'Your request is being processed and validated by our team.',
                    instructions: isOtherService ? [
                        'Your request has been submitted and is being reviewed',
                        'Check your SMS for updates on your request status',
                        'Prepare any required documents that may be requested',
                        'Monitor this tracker page for real-time updates',
                        'Contact ' + departmentName + ' if you have any questions'
                    ] : [
                        'Wait for the official requirements checklist to be prepared',
                        'Check your SMS for updates on your request status',
                        'Prepare your valid ID and other basic documents in advance'
                    ]
                },
                'to_be_encoded_brap': {
                    title: 'Encoded to BRAP Request List',
                    description: 'Your request has been encoded to the BRAP (Birth Registration Assistance Project) request list.',
                    instructions: [
                        'Your request is now part of the BRAP processing queue',
                        'Our office will verify your documents with PSA',
                        'You will be notified of the verification result (Positive or Negative)',
                        'Please wait for our staff to contact you for updates',
                        'No further action is needed at this time'
                    ]
                },
                'incomplete_requirements': {
                    title: isOtherService ? 'Action Required: Missing Documents' : 'Requirements Follow-up',
                    description: isOtherService 
                        ? 'Some required documents are missing. Please submit the remaining requirements to continue processing.' 
                        : 'Some documents are missing. Please submit the remaining requirements.',
                    instructions: isOtherService ? [
                        'Review the remarks section above for specific missing documents',
                        'Check which documents were not accepted or are missing',
                        'Prepare the missing documents as soon as possible',
                        'Return to ' + departmentName + ' with all required items',
                        'Submit all documents together to avoid further delays',
                        'Your request will proceed once all requirements are complete',
                        'Contact ' + departmentName + ' if you need clarification on any requirement'
                    ] : [
                        'Review the remarks section for specific missing documents',
                        'Prepare the missing documents as soon as possible',
                        'Return to the Civil Registry Office with all required items',
                        'Submit all documents together to avoid further delays',
                        'Your request will proceed once all requirements are complete'
                    ]
                },
                'accepted': {
                    title: isOtherService ? 'Documents Accepted - Processing' : 'ACCEPTED - Your request is being posted for 10 calendar days',
                    description: isOtherService 
                        ? 'Your documents have been accepted and are now being processed. No further action is needed from you at this time.' 
                        : 'Your documents have been accepted. Your application will be posted for 10 calendar days.',
                    instructions: isOtherService ? [
                        'Your submitted documents have been reviewed and accepted',
                        'Your request is now in the processing queue',
                        'Monitor your request status regularly for updates',
                        'You will receive SMS notifications for important updates',
                        'Processing time varies depending on the type of service requested',
                        'No further action is needed from you at this time',
                        'Contact ' + departmentName + ' if you have any questions'
                    ] : [
                        'Your submitted documents have been reviewed and accepted',
                        'Your application will now be posted for public viewing for 10 calendar days',
                        'This posting period is a required step for delayed birth registration',
                        'After the posting period, your document will be registered',
                        'You will be notified via SMS when your document is ready for pick up',
                        'No further action is needed from you at this time'
                    ]
                },
                'psa_processing': {
                    title: isOtherService ? 'Processing & Verification' : 'Registration & Release',
                    description: isOtherService 
                        ? 'Your request is being processed and verified by our team.' 
                        : 'Your certificates are being registered and prepared for release.',
                    instructions: isOtherService ? [
                        'Your request is being processed and verified',
                        'This step involves verification and validation of your submitted documents',
                        'Processing time varies depending on the type of service',
                        'You will receive an SMS notification when processing is complete',
                        'Monitor this tracker page for real-time updates',
                        'Contact ' + departmentName + ' if you have any questions',
                        'No further action is needed from you at this time'
                    ] : [
                        'Your documents are being registered in the official records',
                        'Certificates are being printed and prepared for release',
                        'This final step usually takes 2-3 business days',
                        'You will receive an SMS notification when ready for pickup',
                        'Prepare your valid ID for claiming your documents'
                    ]
                },
                'registered': {
                    title: 'Service Completed Successfully',
                    description: 'Your service request has been successfully registered and completed. Your documents are now part of the official records.',
                    instructions: isOtherService ? [
                        'Your service request has been fully processed and completed',
                        'All documents have been verified and processed',
                        'Your request is now complete and no further action is required',
                        'You may visit ' + departmentName + ' during business hours if you need additional services',
                        'Keep your tracking number for future reference',
                        'Thank you for using our services!'
                    ] : [
                        'Your service request has been fully processed and registered',
                        'All documents have been verified and recorded in the official system',
                        'Your request is now complete and no further action is required',
                        'You may visit the office during business hours if you need copies or additional services',
                        'Keep your tracking number for future reference',
                        'Thank you for using our services!'
                    ]
                },
                // Delayed Birth Registration (Regular) specific statuses
                'waiting_for_psa_negative_certificate': {
                    title: 'Submit PSA Negative Certificate',
                    description: 'Please submit your PSA Negative Certificate along with the required documents.',
                    instructions: [
                        'Obtain a Negative Certificate from PSA (Philippine Statistics Authority)',
                        'Gather all other required documents as per the checklist',
                        'Visit the City Civil Registry Office during office hours (Mon-Fri, 8AM-5PM)',
                        'Submit all documents together for verification',
                        'Our staff will review your documents and update your application status'
                    ]
                },
                'positive_result': {
                    title: 'Positive Result - Proceeding to Posting',
                    description: 'Your documents show a positive result. Your application will proceed to the 10-day posting period.',
                    instructions: [
                        'Your application has received a positive result from verification',
                        'Your request will now be posted for public viewing for 10 days',
                        'This is a required process for delayed birth registration',
                        'After the posting period, your document will be finalized',
                        'You will be notified when your document is ready for pickup'
                    ]
                },
                'accepted_octogenarian': {
                    title: 'Sent to PSA for Evaluation',
                    description: 'Your request has been sent to PSA for evaluation under the Octogenarian category.',
                    instructions: [
                        'Your application qualifies under the Octogenarian category',
                        'PSA (Philippine Statistics Authority) will evaluate your documents',
                        'This evaluation process may take several days',
                        'You will be notified once the evaluation is completed',
                        'Please wait for further updates from our office'
                    ]
                },
                'verified_with_problem': {
                    title: 'Verification Issue - Action Required',
                    description: 'PSA verification found issues with your application. Please visit the office for further instructions.',
                    instructions: [
                        'PSA verification found some issues that need to be addressed',
                        'Please visit the City Civil Registry Office as soon as possible',
                        'Bring a valid ID for verification',
                        'Our staff will explain the issues and guide you on the next steps',
                        'You may need to submit additional documents for compliance'
                    ]
                },
                'for_compliance': {
                    title: 'For Compliance - Submit Required Documents',
                    description: 'Please submit the required compliance documents to proceed with your application.',
                    instructions: [
                        'Review the remarks section for specific documents required',
                        'Prepare all compliance documents as instructed',
                        'Visit the City Civil Registry Office during office hours (Mon-Fri, 8AM-5PM)',
                        'Submit all documents together for review',
                        'Your application will proceed once compliance is complete'
                    ]
                },
                'posting': {
                    title: '10 Days Posting Period',
                    description: 'Your application is currently in the required 10-day posting period.',
                    instructions: [
                        'Your application is posted for public viewing',
                        'This is a required 10-day posting period for delayed birth registration',
                        'No action is required from you during this period',
                        'After 10 days, your document will be finalized',
                        'You will be notified when your document is ready for pickup'
                    ]
                },
                'completed': {
                    title: isOtherService ? 'Service Completed Successfully' : 'COMPLETED - Ready for Pickup',
                    description: isOtherService 
                        ? 'Your service request has been successfully completed. All processing is finished.' 
                        : 'Congratulations! Your delayed birth registration has been completed. Your document is ready for pickup.',
                    instructions: isOtherService ? [
                        'Your service request has been fully processed',
                        'All required steps have been completed',
                        'Your request is now complete and no further action is required',
                        'You may visit the office during business hours if you need additional services',
                        'Keep your tracking number for future reference',
                        'Thank you for using our services!'
                    ] : [
                        'Your delayed birth registration is now complete',
                        'Your document is ready for pickup',
                        'Visit the City Civil Registry Office during office hours (Mon-Fri, 8AM-5PM)',
                        'Bring a valid ID for verification',
                        'Keep your tracking number for your records',
                        'Thank you for using our services!'
                    ]
                },
                'negative_result': {
                    title: 'Negative Result - Action Required',
                    description: 'The result of your application is available. Please comply with the needed requirements to proceed.',
                    instructions: [
                        'Your application has received a negative result from PSA verification',
                        'Review the remarks section for specific requirements needed',
                        'Gather all required documents as instructed',
                        'Visit the City Civil Registry Office during office hours (Mon-Fri, 8AM-5PM)',
                        'Submit all required documents for compliance',
                        'Our staff will review your documents and guide you on next steps'
                    ]
                },
                'processing': {
                    title: 'Processing',
                    description: isOtherService 
                        ? 'Your request is currently being processed by our office.' 
                        : 'Your request is currently being processed by our office.',
                    instructions: isOtherService ? [
                        'Your request is being reviewed and processed',
                        'This may take several business days',
                        'Please wait for updates via SMS',
                        'No action is required from you at this time',
                        'You will be notified once processing is complete',
                        'Contact ' + departmentName + ' if you have any questions'
                    ] : [
                        'Your application is being reviewed and processed',
                        'This may take several business days',
                        'Please wait for updates via SMS',
                        'No action is required from you at this time',
                        'You will be notified once processing is complete'
                    ]
                },
                'in_progress': {
                    title: 'In Progress',
                    description: 'Your request is currently being processed.',
                    instructions: isOtherService ? [
                        'Your application is actively being processed',
                        'Our staff is working on your request',
                        'Please wait for updates via SMS',
                        'No action is required from you at this time',
                        'You will be notified of any updates',
                        'Contact ' + departmentName + ' if you have any questions'
                    ] : [
                        'Your application is actively being processed',
                        'Our staff is working on your request',
                        'Please wait for updates via SMS',
                        'No action is required from you at this time',
                        'You will be notified of any updates'
                    ]
                },
                'under_review': {
                    title: 'Under Review',
                    description: 'Your request is being reviewed by our team.',
                    instructions: isOtherService ? [
                        'Your application is being carefully reviewed',
                        'Our staff is verifying your documents and information',
                        'This review process may take a few days',
                        'Please wait for updates via SMS',
                        'You will be notified once the review is complete',
                        'Contact ' + departmentName + ' if you have any questions'
                    ] : [
                        'Your application is being carefully reviewed',
                        'Our staff is verifying your documents and information',
                        'This review process may take a few days',
                        'Please wait for updates via SMS',
                        'You will be notified once the review is complete'
                    ]
                },
                'ready': {
                    title: 'Ready for Pickup',
                    description: 'Your document is ready for pickup.',
                    instructions: isOtherService ? [
                        'Your documents have been processed and are ready for pickup',
                        'Visit ' + departmentName + ' during business hours (8:00 AM - 5:00 PM)',
                        'Bring a valid government ID for verification',
                        'Present your tracking number when claiming',
                        'Keep your receipt for future reference',
                        'Thank you for using our services!'
                    ] : [
                        'Your document has been processed and is ready',
                        'Visit the City Civil Registry Office during office hours (Mon-Fri, 8AM-5PM)',
                        'Bring a valid ID for verification',
                        'Present your tracking number when claiming',
                        'Keep your tracking number for your records'
                    ]
                },
                'ready_for_pickup': {
                    title: 'Ready for Pickup',
                    description: 'Your document is ready for pickup.',
                    instructions: isOtherService ? [
                        'Your documents have been processed and are ready for pickup',
                        'Visit ' + departmentName + ' during business hours (8:00 AM - 5:00 PM)',
                        'Bring a valid government ID for verification',
                        'Present your tracking number when claiming',
                        'Keep your receipt for future reference',
                        'Thank you for using our services!'
                    ] : [
                        'Your document has been processed and is ready',
                        'Visit the City Civil Registry Office during office hours (Mon-Fri, 8AM-5PM)',
                        'Bring a valid ID for verification',
                        'Present your tracking number when claiming',
                        'Keep your tracking number for your records'
                    ]
                },
                'rejected': {
                    title: 'Request Rejected',
                    description: isOtherService 
                        ? 'Your request has been rejected. Please review the remarks for more information.' 
                        : 'Your request has been rejected. Please visit the office to discuss the reason and possible next steps.',
                    instructions: isOtherService ? [
                        'Your request has been rejected',
                        'Review the remarks section above for the reason',
                        'Contact ' + departmentName + ' for clarification',
                        'You may need to submit a new request with corrected information',
                        'Visit the office during business hours for assistance',
                        'Keep your tracking number for reference'
                    ] : [
                        'Your application has been rejected',
                        'Please visit the City Civil Registry Office during office hours (Mon-Fri, 8AM-5PM)',
                        'Bring a valid ID for verification',
                        'Our staff will explain the reason for rejection',
                        'They will guide you on possible next steps or alternative solutions',
                        'You may be able to resubmit with additional documentation'
                    ]
                },
                'cancelled': {
                    title: 'Request Cancelled',
                    description: isOtherService 
                        ? 'Your request has been cancelled. Please contact the office if you need assistance.' 
                        : 'Your request has been cancelled.',
                    instructions: isOtherService ? [
                        'Your request has been cancelled',
                        'Review the remarks section for cancellation details',
                        'Contact ' + departmentName + ' if you need to resubmit',
                        'You may need to start a new request process',
                        'Visit the office during business hours for assistance',
                        'Keep your tracking number for reference'
                    ] : [
                        'Your application has been cancelled',
                        'If you need to proceed with this service, you may need to submit a new request',
                        'Visit the City Civil Registry Office during office hours (Mon-Fri, 8AM-5PM) for assistance',
                        'Our staff can help you understand the cancellation reason',
                        'They can guide you on how to proceed if you wish to continue'
                    ]
                },
                'ready_for_approval': {
                    title: 'Ready for Approval',
                    description: 'Your request has completed the posting period and is now ready for final approval.',
                    instructions: [
                        'Your application has completed the required 10-day posting period',
                        'Your request is now pending final approval from our office',
                        'This approval process usually takes 1-2 business days',
                        'You will receive an SMS notification once your request is approved',
                        'After approval, your document will be registered and ready for pickup'
                    ]
                },
                'encoded_brap': {
                    title: 'Encoded to BRAP Request List',
                    description: 'Your request has been encoded to the BRAP (Birth Registration Assistance Project) request list.',
                    instructions: [
                        'Your request is now part of the BRAP processing queue',
                        'Our office will verify your documents with PSA',
                        'You will be notified of the verification result (Positive or Negative)',
                        'Please wait for our staff to contact you for updates',
                        'No further action is needed at this time'
                    ]
                },
            };

            return map[status] || null;
        }

        function fetchTrackingData(trackingNumber) {
            console.log('Fetching tracking data for:', trackingNumber);
            return fetch(`/api/request/track/${trackingNumber}`)
                .then(response => {
                    console.log('API response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('API response data:', data);
                    if (data.success && data.data) {
                        displayTrackingData(data.data);
                        return data.data;
                    } else {
                        // If API fails, try to use sessionStorage data if available
                        const sessionData = sessionStorage.getItem('currentTrackingData');
                        if (sessionData) {
                            try {
                                const parsedData = JSON.parse(sessionData);
                                console.log('Using sessionStorage data as fallback:', parsedData);
                                displayTrackingData(parsedData);
                                return parsedData;
                            } catch (e) {
                                console.error('Error parsing sessionStorage data:', e);
                                displayError(data.message || 'Tracking data not found.');
                                return null;
                            }
                        } else {
                            displayError(data.message || 'Tracking data not found.');
                            return null;
                        }
                    }
                })
                .catch(error => {
                    console.error('API fetch error:', error);
                    // Try to use sessionStorage data if API fails
                    const sessionData = sessionStorage.getItem('currentTrackingData');
                    if (sessionData) {
                        try {
                            const parsedData = JSON.parse(sessionData);
                            console.log('Using sessionStorage data after API error:', parsedData);
                            displayTrackingData(parsedData);
                            return parsedData;
                        } catch (e) {
                            console.error('Error parsing sessionStorage data:', e);
                            displayError('Unable to load tracking information.');
                            return null;
                        }
                    } else {
                        displayError('Unable to load tracking information.');
                        return null;
                    }
                });
        }

        function refreshTrackingStatus(trackingNumber) {
            fetch(`/api/request/status/${trackingNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const currentData = JSON.parse(sessionStorage.getItem('currentTrackingData')) || {};
                        const oldStatus = currentData.status || getCurrentStatus();
                        const newStatus = data.status;

                        // Only update UI if status actually changed
                        if (oldStatus !== newStatus) {
                            // Refresh full tracking data to get updated timeline and all information
                            fetch(`/api/request/track/${trackingNumber}`)
                                .then(response => response.json())
                                .then(trackData => {
                                    if (trackData.success && trackData.data) {
                                        const updatedData = trackData.data;
                                        
                                        // Update all UI elements with fresh data
                                        document.getElementById('statusText').textContent = formatStatusText(newStatus);
                                        updateStatusDot(newStatus);
                                        updateTimelineWithStatusUpdates(updatedData, newStatus);
                                        updateNextStep(newStatus, updatedData);

                                        // Show requirements section if status is checklist_given
                                        if (newStatus === 'checklist_given' && updatedData.requirements_checklist) {
                                            showRequirementsSection(updatedData.requirements_checklist);
                                        } else {
                                            document.getElementById('requirementsSection').style.display = 'none';
                                        }

                                        // Show status change notification
                                        showStatusChangeNotification(newStatus);
                                        addStatusChangeAnimation();

                                        // Update stored data
                                        updatedData.updated_at = data.updated_at || new Date().toISOString();
                                        sessionStorage.setItem('currentTrackingData', JSON.stringify(updatedData));
                                        window.currentTrackingData = updatedData;
                                    } else {
                                        // Fallback: update only status if full data fetch fails
                                        document.getElementById('statusText').textContent = formatStatusText(newStatus);
                                        updateStatusDot(newStatus);
                                        updateNextStep(newStatus, currentData);
                                        
                                        if (oldStatus && oldStatus !== newStatus) {
                                            showStatusChangeNotification(newStatus);
                                            addStatusChangeAnimation();
                                        }

                                        // Update stored data
                                        currentData.status = newStatus;
                                        currentData.updated_at = data.updated_at || new Date().toISOString();
                                        if (data.requirements_checklist) {
                                            currentData.requirements_checklist = data.requirements_checklist;
                                        }
                                        sessionStorage.setItem('currentTrackingData', JSON.stringify(currentData));
                                    }
                                })
                                .catch(error => {
                                    console.log('Full data refresh failed:', error);
                                    // Fallback: update only status
                                    document.getElementById('statusText').textContent = formatStatusText(newStatus);
                                    updateStatusDot(newStatus);
                                    updateNextStep(newStatus, currentData);
                                    
                                    if (oldStatus && oldStatus !== newStatus) {
                                        showStatusChangeNotification(newStatus);
                                        addStatusChangeAnimation();
                                    }

                                    // Update stored data
                                    currentData.status = newStatus;
                                    currentData.updated_at = data.updated_at || new Date().toISOString();
                                    if (data.requirements_checklist) {
                                        currentData.requirements_checklist = data.requirements_checklist;
                                    }
                                    sessionStorage.setItem('currentTrackingData', JSON.stringify(currentData));
                                });
                        } else {
                            // Status hasn't changed, but update requirements section if needed
                            if (newStatus === 'checklist_given' && data.requirements_checklist) {
                                showRequirementsSection(data.requirements_checklist);
                            } else if (newStatus !== 'checklist_given') {
                                document.getElementById('requirementsSection').style.display = 'none';
                            }

                            // Update stored data timestamp
                            currentData.updated_at = data.updated_at || new Date().toISOString();
                            if (data.requirements_checklist) {
                                currentData.requirements_checklist = data.requirements_checklist;
                            }
                            sessionStorage.setItem('currentTrackingData', JSON.stringify(currentData));
                        }
                    }
                })
                .catch(error => {
                    console.log('Status refresh failed:', error);
                });
        }

        function getCurrentStatus() {
            const currentData = sessionStorage.getItem('currentTrackingData');
            if (currentData) {
                const data = JSON.parse(currentData);
                return data.status;
            }
            return null;
        }

        function showStatusChangeNotification(newStatus) {
            // Close any existing notification first
            closeFloatingMessage();

            const overlay = document.createElement('div');
            overlay.className = 'floating-message-overlay';

            const statusDisplayName = formatStatusText(newStatus);
            const message = document.createElement('div');
            message.className = 'floating-message success';
            message.innerHTML = `
                <div class="message-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Status Updated</h3>
                <p>Status updated to <strong>${statusDisplayName}</strong></p>
                <div class="button-group">
                    <button class="btn btn-primary" onclick="closeFloatingMessage()">OK</button>
                </div>
            `;

            overlay.appendChild(message);
            document.body.appendChild(overlay);

            // Auto-close after 5 seconds
            setTimeout(() => {
                closeFloatingMessage();
            }, 5000);
        }

        function getStatusDisplayName(status) {
            const statusNames = {
                'inquiry': 'Inquiry',
                'submitted': 'Submitted',
                'pending': 'Pending',
                'to_be_encoded_brap': 'To Be Encoded BRAP',
                'checklist_given': 'Checklist Given',
                'incomplete_requirements': 'Incomplete Requirements',
                'accepted': 'Accepted',
                'registered': 'Registered',
                'under_review': 'Under Review',
                'reviewing': 'Reviewing',
                'processing': 'Processing',
                'in_progress': 'In Progress',
                'positive_result': 'Positive Result',
                'negative_result': 'Negative Result',
                'ready': 'Ready',
                'ready_for_pickup': 'Ready for Pickup',
                'completed': 'Completed',
                'rejected': 'Rejected',
                'cancelled': 'Cancelled',
                // Delayed Birth Registration (Regular) specific statuses
                'waiting_for_psa_negative_certificate': 'Waiting for PSA Negative Certificate',
                'accepted_octogenarian': 'Accepted (Octogenarian)',
                'verified_with_problem': 'Verified With Problem',
                'for_compliance': 'For Compliance',
                'posting': '10 Days Posting'
            };

            return statusNames[status.toLowerCase()] || status;
        }

        function addStatusChangeAnimation() {
            const timeline = document.getElementById('statusTimeline');
            if (timeline) {
                timeline.classList.add('status-updated');
                setTimeout(() => {
                    timeline.classList.remove('status-updated');
                }, 800);
            }
        }

        function showRequirementsSection(requirementsChecklist) {
            const requirementsSection = document.getElementById('requirementsSection');
            const requirementsContent = document.getElementById('requirementsContent');

            if (requirementsChecklist) {
                try {
                    // Parse the requirements checklist (assuming it's JSON)
                    const requirements = typeof requirementsChecklist === 'string'
                        ? JSON.parse(requirementsChecklist)
                        : requirementsChecklist;

                    let requirementsHtml = '';

                    if (Array.isArray(requirements)) {
                        requirements.forEach((req, index) => {
                            requirementsHtml += `
                                <div class="requirement-item">
                                    <div class="requirement-number">${index + 1}</div>
                                    <div class="requirement-text">${req}</div>
                                </div>
                            `;
                        });
                    } else if (typeof requirements === 'object') {
                        Object.entries(requirements).forEach(([key, value], index) => {
                            requirementsHtml += `
                                <div class="requirement-item">
                                    <div class="requirement-number">${index + 1}</div>
                                    <div class="requirement-text">
                                        <strong>${key}:</strong> ${value}
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        // If it's a simple string, split by newlines or commas
                        const reqList = requirements.split(/\n|,|;|\./).filter(req => req.trim());
                        reqList.forEach((req, index) => {
                            if (req.trim()) {
                                requirementsHtml += `
                                    <div class="requirement-item">
                                        <div class="requirement-number">${index + 1}</div>
                                        <div class="requirement-text">${req.trim()}</div>
                                    </div>
                                `;
                            }
                        });
                    }

                    requirementsContent.innerHTML = requirementsHtml;
                    requirementsSection.style.display = 'block';
                } catch (error) {
                    console.error('Error parsing requirements checklist:', error);
                    requirementsContent.innerHTML = `
                        <div class="requirement-item">
                            <div class="requirement-text">${requirementsChecklist}</div>
                        </div>
                    `;
                    requirementsSection.style.display = 'block';
                }
            } else {
                requirementsSection.style.display = 'none';
            }
        }

        function displayError(message) {
            document.getElementById('clientName').textContent = 'Error';
            document.getElementById('serviceDesc').textContent = message;
            document.getElementById('refNo').textContent = 'N/A';
            document.getElementById('statusText').textContent = 'ERROR';
        }

        // Toast notification functions
        function showToast(message, type = 'info', duration = 4000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

            // Icon based on type
            let icon = '';
            if (type === 'success') {
                icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            } else if (type === 'error') {
                icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            } else if (type === 'warning') {
                icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            } else {
                icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 16H12V12H11M12 8H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            }

            toast.innerHTML = `
                <div class="toast-icon">${icon}</div>
                <div class="toast-message">${message}</div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            `;

            container.appendChild(toast);

            // Trigger animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            // Auto remove after duration
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, duration);
        }

        // Print receipt function
        function printReceipt() {
            const data = window.currentTrackingData;
            if (!data || !data.tracking_number) {
                showToast('No tracking data available to print.', 'error');
                return;
            }

            // Disable button during print
            const btn = document.getElementById('printReceiptBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span>Printing...</span>';

            // Show loading toast
            showToast('Sending receipt to printer...', 'info', 2000);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Prepare print data
            // Use service_type (specific service like "Fishtrap/Fishpen Permit") first, 
            // then fall back to service_name (department) or document_type
            const printData = {
                tracking_number: data.tracking_number,
                name: data.full_name || 'N/A',
                service: data.service_type || data.service_name || data.document_type || 'Service Request',
                date: data.date || new Date().toLocaleDateString('en-PH'),
                time: data.time || new Date().toLocaleTimeString('en-PH'),
                queue: data.queue_number || null
            };

            // Send print request
            fetch('/services/tracker/print-receipt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(printData)
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showToast('Receipt sent to printer successfully!', 'success');
                        // Schedule feedback modal to show after 30 seconds
                        scheduleFeedbackModal();
                    } else {
                        const errorMsg = result.error || result.message || 'Unknown error';
                        showToast('Failed to print receipt: ' + errorMsg, 'error', 5000);
                    }
                })
                .catch(error => {
                    console.error('Print error:', error);
                    showToast('Error printing receipt. Please try again.', 'error', 5000);
                })
                .finally(() => {
                    // Re-enable button
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        }

        // Feedback modal functions
        let feedbackModalTimeout = null;
        let selectedRating = 0;

        function scheduleFeedbackModal() {
            console.log('Scheduling feedback modal...');
            
            // Clear any existing timeout
            if (feedbackModalTimeout) {
                clearTimeout(feedbackModalTimeout);
            }

            // Check if feedback was already submitted for this tracking number
            const trackingData = window.currentTrackingData;
            if (trackingData && trackingData.tracking_number) {
                const feedbackKey = 'feedback_submitted_' + trackingData.tracking_number;
                if (sessionStorage.getItem(feedbackKey)) {
                    console.log('Feedback already submitted for this tracking number');
                    return; // Don't show if already submitted
                }
            } else {
                console.warn('No tracking data available for feedback modal');
                return;
            }

            console.log('Feedback modal will show in 15 seconds');
            // Show modal after 15 seconds
            feedbackModalTimeout = setTimeout(() => {
                console.log('Showing feedback modal now');
                showFeedbackModal();
            }, 15000); // 15 seconds
        }

        function showFeedbackModal() {
            console.log('Attempting to show feedback modal...');
            const modal = document.getElementById('feedbackModal');
            if (modal) {
                console.log('Modal element found, showing...');
                modal.classList.add('show');
                // Reset rating
                selectedRating = 0;
                updateStarDisplay(0);
                const messageInput = document.getElementById('feedbackMessage');
                const submitBtn = document.getElementById('feedbackSubmitBtn');
                if (messageInput) messageInput.value = '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const btnText = submitBtn.querySelector('.btn-text');
                    if (btnText) btnText.textContent = 'Submit Feedback';
                }
            } else {
                console.error('Feedback modal element not found!');
            }
        }

        function closeFeedbackModal() {
            const modal = document.getElementById('feedbackModal');
            if (modal) {
                modal.classList.remove('show');
            }
        }

        // Initialize star rating
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    selectedRating = parseInt(this.getAttribute('data-rating'));
                    updateStarDisplay(selectedRating);
                    const submitBtn = document.getElementById('feedbackSubmitBtn');
                    if (submitBtn) submitBtn.disabled = false;
                });

                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    updateStarDisplay(rating, true);
                });
            });

            const starContainer = document.getElementById('starRating');
            if (starContainer) {
                starContainer.addEventListener('mouseleave', function() {
                    updateStarDisplay(selectedRating);
                });
            }
        });

        function updateStarDisplay(rating, isHover = false) {
            const stars = document.querySelectorAll('.star');
            const ratingText = document.getElementById('ratingText');
            const ratingTexts = {
                0: 'Tap to rate',
                1: 'Poor',
                2: 'Fair',
                3: 'Good',
                4: 'Very Good',
                5: 'Excellent'
            };

            stars.forEach((star, index) => {
                const starRating = index + 1;
                const svg = star.querySelector('svg');
                const path = svg.querySelector('path');

                if (starRating <= rating) {
                    // Use gradient fill
                    path.setAttribute('fill', 'url(#starGradient)');
                    path.setAttribute('stroke', '#F3BE7C');
                } else {
                    path.setAttribute('fill', 'none');
                    path.setAttribute('stroke', '#d1d5db');
                }
            });

            if (ratingText) {
                ratingText.textContent = ratingTexts[rating] || 'Tap to rate';
            }
        }

        function submitFeedback() {
            if (selectedRating === 0) {
                showToast('Please select a rating', 'warning');
                return;
            }

            const trackingData = window.currentTrackingData;
            if (!trackingData || !trackingData.tracking_number) {
                showToast('Unable to submit feedback. Tracking data not found.', 'error');
                return;
            }

            const message = document.getElementById('feedbackMessage');
            const messageValue = message ? message.value.trim() : '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!csrfToken) {
                showToast('Security token not found. Please refresh the page.', 'error');
                return;
            }

            const submitBtn = document.getElementById('feedbackSubmitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            if (!submitBtn) {
                showToast('Submit button not found.', 'error');
                return;
            }

            submitBtn.disabled = true;
            if (btnText) {
                btnText.textContent = 'Submitting...';
            } else {
                submitBtn.textContent = 'Submitting...';
            }

            // Submit feedback
            const feedbackData = {
                tracking_number: trackingData.tracking_number,
                rating: selectedRating,
                message: messageValue || null
            };

            console.log('Submitting feedback:', feedbackData);

            fetch('/api/feedback/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Request-Source': 'kiosk',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(feedbackData)
            })
                .then(async response => {
                    console.log('Response status:', response.status);
                    const contentType = response.headers.get('content-type');
                    let result;
                    
                    if (contentType && contentType.includes('application/json')) {
                        result = await response.json();
                    } else {
                        const text = await response.text();
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response: ' + text.substring(0, 100));
                    }

                    if (!response.ok) {
                        console.error('Response not OK:', result);
                        throw new Error(result.message || result.error || 'Server error');
                    }

                    return result;
                })
                .then(result => {
                    console.log('Feedback submission result:', result);
                    if (result.success) {
                        showToast('Thank you for your feedback!', 'success');
                        // Mark as submitted
                        const feedbackKey = 'feedback_submitted_' + trackingData.tracking_number;
                        sessionStorage.setItem(feedbackKey, 'true');
                        closeFeedbackModal();
                    } else {
                        const errorMsg = result.message || result.error || 'Failed to submit feedback';
                        console.error('Feedback submission failed:', errorMsg, result);
                        showToast(errorMsg, 'error', 6000);
                        submitBtn.disabled = false;
                        if (btnText) {
                            btnText.textContent = 'Submit Feedback';
                        } else {
                            submitBtn.textContent = 'Submit Feedback';
                        }
                    }
                })
                .catch(error => {
                    console.error('Feedback submission error:', error);
                    const errorMsg = error.message || 'Error submitting feedback. Please try again.';
                    showToast(errorMsg, 'error', 6000);
                    submitBtn.disabled = false;
                    if (btnText) {
                        btnText.textContent = 'Submit Feedback';
                    } else {
                        submitBtn.textContent = 'Submit Feedback';
                    }
                });
        }

        // Add CSRF token meta tag
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    </script>
</body>

</html>