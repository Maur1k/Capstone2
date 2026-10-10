@extends('website.layouts.app')

@section('title', 'Tracking Result - Click2Serve')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/tracker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/website/tracking-result.css') }}">

    @php
        $progressIndicatorsEnabled = \App\Models\Setting::getBoolean('feature.progress_indicator', true);
    @endphp

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- SVG Gradient Definition for Stars -->
    <svg width="0" height="0" style="position: absolute; z-index: -1;">
        <defs>
            <linearGradient id="starGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#F3BE7C;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#E8A85A;stop-opacity:1" />
            </linearGradient>
        </defs>
    </svg>

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

    <div class="tracking-result-page">
        <div class="container">
            <div class="tracker-panel">
                <div class="tracker-header">
                    <div class="client">
                        <div class="client-name" id="clientName">{{ $data['full_name'] ?? 'Unknown Client' }}</div>
                        <div class="client-desc" id="serviceDesc">
                            Requesting for
                            {{ $data['service_type'] ?? $data['service_name'] ?? $data['document_type'] ?? 'Service Request' }}
                        </div>
                    </div>
                    <div class="meta">
                        <div class="ref">Reference NO. <span id="refNo">{{ $data['tracking_number'] ?? 'N/A' }}</span></div>
                        @if(isset($data['queue_number']) && $data['queue_number'])
                            <div class="queue-number" id="queueNumberContainer">
                                <strong>Queue NO. <span id="queueNumber">{{ $data['queue_number'] }}</span></strong>
                            </div>
                        @else
                            <div class="queue-number" id="queueNumberContainer" style="display: none;">
                                <strong>Queue NO. <span id="queueNumber">-</span></strong>
                            </div>
                        @endif
                        <div class="status">
                            <span class="dot" id="statusDot"></span>
                            <span id="statusText">{{ $data['status'] ?? 'Unknown' }}</span>
                        </div>
                        <strong>Date Submitted: <span
                                id="dateSubmitted">{{ $data['date'] ?? date('Y-m-d') }}</span></strong>
                        <strong>Time Submitted: <span
                                id="timeSubmitted">{{ $data['time'] ?? date('H:i:s') }}</span></strong>
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
                    <a href="{{ route('track') }}" class="secondary-btn">Track Another</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const featureToggles = {
            progressIndicators: @json($progressIndicatorsEnabled),
        };

        // Store tracking data
        let trackingData = @json($data);
        const trackingNumber = '{{ $trackingNumber }}';
        let lastKnownStatus = trackingData.status || 'unknown';

        // Load tracking data when page loads
        document.addEventListener('DOMContentLoaded', function () {
            displayTrackingData(trackingData);
            
            // Initialize feedback modal
            initializeFeedbackModal();
            
            // Check if this is a new submission and schedule feedback modal
            checkAndScheduleFeedbackModal();
            
            // Set up auto-refresh for status updates (every 15 seconds for real-time updates)
            let refreshInterval = setInterval(function () {
                refreshTrackingStatus();
            }, 15000);
        });

        function displayTrackingData(data) {
            console.log('Displaying tracking data:', data);

            // Update client information
            document.getElementById('clientName').textContent = data.full_name || 'Unknown Client';
            const serviceDisplay = data.service_type || data.service_name || data.document_type || 'Service Request';
            document.getElementById('serviceDesc').textContent = `Requesting for ${serviceDisplay}`;

            // Update tracking information
            document.getElementById('refNo').textContent = data.tracking_number || 'N/A';

            // Update queue number
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

            // Update date and time submitted - use created_at if available for accurate submission time
            let submittedDate = data.date;
            let submittedTime = data.time;

            // For appointments, use created_at for submission time instead of appointment time
            if (data.created_at) {
                const createdDate = new Date(data.created_at);
                submittedDate = createdDate.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' }).replace(/\//g, '-');
                const hours = String(createdDate.getHours()).padStart(2, '0');
                const minutes = String(createdDate.getMinutes()).padStart(2, '0');
                const seconds = String(createdDate.getSeconds()).padStart(2, '0');
                submittedTime = `${hours}:${minutes}:${seconds}`;
            } else if (data.date && data.time) {
                // Format date properly
                submittedDate = data.date;
                // Ensure time is in HH:MM:SS format
                if (data.time && !data.time.includes(':')) {
                    submittedTime = '00:00:00';
                } else if (data.time) {
                    const timeParts = data.time.split(':');
                    if (timeParts.length === 2) {
                        submittedTime = `${timeParts[0]}:${timeParts[1]}:00`;
                    } else {
                        submittedTime = data.time;
                    }
                }
            }

            document.getElementById('dateSubmitted').textContent = submittedDate || new Date().toLocaleDateString('en-PH', { timeZone: 'Asia/Manila' });
            document.getElementById('timeSubmitted').textContent = formatTime12Hour(submittedTime);

            // Show timeline
            document.getElementById('statusTimeline').style.display = 'block';
            updateTimelineWithStatusUpdates(data, status);
            updateNextStep(status, data);


            // Show requirements section if status is checklist_given
            if (status === 'checklist_given' && data.requirements_checklist) {
                showRequirementsSection(data.requirements_checklist);
            } else {
                document.getElementById('requirementsSection').style.display = 'none';
            }
        }

        function formatStatusText(status) {
            // Convert underscores to spaces and uppercase
            if (!status) return 'UNKNOWN';
            
            // First check for known status mappings
            const statusMap = {
                'inquiry': 'INQUIRY',
                'submitted': 'SUBMITTED',
                'to_be_encoded_brap': 'TO BE ENCODED BRAP',
                'checklist_given': 'CHECKLIST GIVEN',
                'incomplete_requirements': 'INCOMPLETE REQUIREMENTS',
                'accepted': 'ACCEPTED',
                'psa_processing': 'PSA PROCESSING',
                'registered': 'REGISTERED',
                'processing': 'PROCESSING',
                'in_progress': 'IN PROGRESS',
                'under_review': 'UNDER REVIEW',
                'completed': 'COMPLETED',
                'ready': 'READY',
                'ready_for_pickup': 'READY FOR PICKUP',
                'rejected': 'REJECTED',
                'cancelled': 'CANCELLED',
                'negative_result': 'NEGATIVE RESULT',
                'positive_result': 'POSITIVE RESULT',
                'ready_for_approval': 'READY FOR APPROVAL',
                'waiting_for_psa_negative_certificate': 'WAITING FOR PSA NEGATIVE CERTIFICATE',
                'accepted_octogenarian': 'ACCEPTED (OCTOGENARIAN)',
                'verified_with_problem': 'VERIFIED WITH PROBLEM',
                'for_compliance': 'FOR COMPLIANCE',
                'posting': 'POSTING',
                'encoded_brap': 'ENCODED BRAP',
                'pending': 'PENDING',
                'approved': 'APPROVED',
                'declined': 'DECLINED',
                'no_show': 'NO SHOW'
            };
            
            const statusLower = status.toLowerCase();
            if (statusMap[statusLower]) {
                return statusMap[statusLower];
            }
            
            // Fallback: Replace underscores with spaces and uppercase
            return status.replace(/_/g, ' ').toUpperCase();
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
            dot.className = 'dot';

            switch (status.toLowerCase()) {
                case 'inquiry':
                case 'submitted':
                case 'pending':
                    dot.classList.add('pending');
                    break;
                case 'checklist_given':
                case 'to_be_encoded_brap':
                case 'in_progress':
                case 'under_review':
                case 'processing':
                case 'psa_processing':
                case 'accepted':
                    dot.classList.add('progress');
                    break;
                case 'registered':
                case 'positive_result':
                case 'completed':
                case 'ready':
                case 'ready_for_pickup':
                    dot.classList.add('completed');
                    break;
                case 'incomplete_requirements':
                case 'negative_result':
                case 'rejected':
                case 'cancelled':
                    dot.classList.add('rejected');
                    break;
                default:
                    dot.classList.add('pending');
            }
        }

        function updateTimelineWithStatusUpdates(data, currentStatus) {
            const timelineContainer = document.getElementById('timelineContainer');
            const statusUpdates = data.status_updates || [];
            const currentStatusLower = currentStatus.toLowerCase();

            timelineContainer.innerHTML = '';

            if (statusUpdates.length === 0) {
                const currentStatusLabel = formatStatusText(currentStatus);
                const isFinalStatus = ['registered', 'completed', 'ready', 'ready_for_pickup'].includes(currentStatusLower);
                const isIncomplete = currentStatusLower === 'incomplete_requirements';
                const terminalStatuses = ['cancelled', 'rejected'];
                const isTerminalStatus = terminalStatuses.includes(currentStatusLower);

                let statusClass = 'active';
                let statusBadgeClass = 'active';
                let statusBadgeText = 'In Progress';

                if (isTerminalStatus) {
                    statusClass = 'rejected';
                    statusBadgeClass = 'rejected';
                    if (currentStatusLower === 'cancelled') {
                        statusBadgeText = 'Cancelled';
                    } else if (currentStatusLower === 'rejected') {
                        statusBadgeText = 'Rejected';
                    } else {
                        statusBadgeText = 'Terminated';
                    }
                } else if (isIncomplete) {
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
                                                <div class="timeline-dates">
                                                    <small class="timeline-date">
                                                        ${formatTimelineDate(data.created_at || data.date)}
                                                    </small>
                                                    <small class="timeline-time">${formatTimelineTime(data.created_at || data.time)}</small>
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

            const sortedUpdates = [...statusUpdates].sort((a, b) => {
                return new Date(a.created_at) - new Date(b.created_at);
            });

            const seenStatuses = new Set();
            const uniqueUpdates = [];

            for (let i = sortedUpdates.length - 1; i >= 0; i--) {
                const update = sortedUpdates[i];
                if (!seenStatuses.has(update.status)) {
                    seenStatuses.add(update.status);
                    uniqueUpdates.unshift(update);
                }
            }

            uniqueUpdates.forEach((update, index) => {
                const isLast = index === uniqueUpdates.length - 1;
                const isCurrent = update.status.toLowerCase() === currentStatusLower;
                const isCompleted = !isCurrent && index < uniqueUpdates.length - 1;
                const isIncompleteRequirements = update.status.toLowerCase() === 'incomplete_requirements';
                const updateStatusLower = update.status.toLowerCase();

                const finalStatuses = ['registered', 'completed', 'ready', 'ready_for_pickup'];
                const terminalStatuses = ['cancelled', 'rejected'];
                const isFinalStatus = finalStatuses.includes(updateStatusLower);
                const isTerminalStatus = terminalStatuses.includes(updateStatusLower);

                let statusClass = 'pending';
                let statusBadgeClass = 'pending';
                let statusBadgeText = 'Pending';

                // Handle terminal statuses (cancelled, rejected) - these should never show "In Progress"
                if (isTerminalStatus) {
                    statusClass = 'rejected';
                    statusBadgeClass = 'rejected';
                    if (updateStatusLower === 'cancelled') {
                        statusBadgeText = 'Cancelled';
                    } else if (updateStatusLower === 'rejected') {
                        statusBadgeText = 'Rejected';
                    } else {
                        statusBadgeText = 'Terminated';
                    }
                } else if (isIncompleteRequirements && isCurrent) {
                    statusClass = 'incomplete';
                    statusBadgeClass = 'incomplete';
                    statusBadgeText = 'Incomplete';
                } else if (isCurrent && isFinalStatus) {
                    statusClass = 'completed';
                    statusBadgeClass = 'completed';
                    statusBadgeText = 'Completed';
                } else if (isCurrent) {
                    statusClass = 'active';
                    statusBadgeClass = 'active';
                    statusBadgeText = 'In Progress';
                } else if (isCompleted) {
                    statusClass = 'completed';
                    statusBadgeClass = 'completed';
                    statusBadgeText = 'Completed';
                }

                // Format dates and times accurately
                const updateDate = new Date(update.created_at);
                const dateStr = formatTimelineDate(update.created_at);
                const timeStr = formatTimelineTime(update.created_at);
                const statusDate = update.status_date ? formatTimelineDate(update.status_date) : null;

                const statusLabel = formatStatusText(update.status);

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
                                                <div class="timeline-dates">
                                                    <small class="timeline-date">
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

            let progressIndex = uniqueUpdates.length - 1;
            const hasIncomplete = currentStatusLower === 'incomplete_requirements';
            if (hasIncomplete && uniqueUpdates.length > 0) {
                progressIndex = Math.max(0, uniqueUpdates.length - 2);
            }
            updateTimelineProgress(progressIndex, uniqueUpdates.length, hasIncomplete);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Helper function to format timeline dates consistently
        function formatTimelineDate(dateString) {
            if (!dateString) return new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) {
                    // If it's already a formatted date string, return as is
                    return dateString;
                }
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            } catch (e) {
                return dateString;
            }
        }

        // Helper function to format timeline times consistently in 12-hour format with AM/PM
        function formatTimelineTime(dateTimeString) {
            if (!dateTimeString) {
                return new Date().toLocaleTimeString('en-PH', { 
                    timeZone: 'Asia/Manila', 
                    hour12: true, 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
            }

            try {
                // If it's a time string like "HH:MM:SS" or "HH:MM"
                if (typeof dateTimeString === 'string' && dateTimeString.includes(':')) {
                    const timeParts = dateTimeString.split(':');
                    let hours = parseInt(timeParts[0]);
                    const minutes = String(timeParts[1] || '00').padStart(2, '0');
                    
                    // Convert to 12-hour format with AM/PM
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours === 0 ? 12 : hours;
                    
                    return `${hours.toString().padStart(2, '0')}:${minutes} ${ampm}`;
                }

                // If it's a datetime string
                const date = new Date(dateTimeString);
                if (!isNaN(date.getTime())) {
                    // Use toLocaleTimeString with Asia/Manila timezone for 12-hour format
                    return date.toLocaleTimeString('en-PH', { 
                        timeZone: 'Asia/Manila', 
                        hour12: true, 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                }

                return dateTimeString;
            } catch (e) {
                return dateTimeString;
            }
        }

        function updateTimelineProgress(currentStep, totalSteps, hasIncomplete = false) {
            const progress = totalSteps > 1 ? (currentStep / (totalSteps - 1)) * 100 : 0;

            const timelineLine = document.querySelector('.timeline');
            if (timelineLine) {
                timelineLine.style.setProperty('--timeline-progress', `${progress}%`);
                if (hasIncomplete) {
                    timelineLine.classList.add('has-incomplete');
                } else {
                    timelineLine.classList.remove('has-incomplete');
                }
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
                trackingData = window.trackingData || {};
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
                        '<li class="instruction-item">' + escapeHtml(instruction) + '</li>'
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
                    description: 'Your request is currently being processed by our office.',
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

        function refreshTrackingStatus() {
            fetch(`/website/tracking/status/${trackingNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const oldStatus = lastKnownStatus;
                        const newStatus = data.status;

                        // Only update UI if status actually changed
                        if (oldStatus !== newStatus) {
                            // Update status display
                            document.getElementById('statusText').textContent = formatStatusText(newStatus);
                            updateStatusDot(newStatus);
                            updateNextStep(newStatus, trackingData);

                            // Show notification for status change
                            showStatusChangeNotification(newStatus);
                            addStatusChangeAnimation();

                            // Update last known status
                            lastKnownStatus = newStatus;
                            trackingData.status = newStatus;

                            // Refresh full tracking data to get updated timeline
                            fetch(`/website/tracking/data/${trackingNumber}`)
                                .then(response => response.json())
                                .then(trackData => {
                                    if (trackData.success && trackData.data) {
                                        trackingData = trackData.data;
                                        updateTimelineWithStatusUpdates(trackingData, newStatus);
                                        displayTrackingData(trackingData);
                                    }
                                })
                                .catch(error => console.log('Full data refresh failed:', error));
                        }

                        // Update requirements section if needed
                        if (newStatus === 'checklist_given' && data.requirements_checklist) {
                            showRequirementsSection(data.requirements_checklist);
                        } else if (newStatus !== 'checklist_given') {
                            document.getElementById('requirementsSection').style.display = 'none';
                        }

                        // Update tracking data
                        trackingData.updated_at = data.updated_at || new Date().toISOString();
                        if (data.requirements_checklist) {
                            trackingData.requirements_checklist = data.requirements_checklist;
                        }
                    }
                })
                .catch(error => {
                    console.log('Status refresh failed:', error);
                });
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
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path>
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

            setTimeout(() => {
                closeFloatingMessage();
            }, 5000);
        }

        function closeFloatingMessage() {
            const overlay = document.querySelector('.floating-message-overlay');
            if (overlay) {
                overlay.remove();
            }
        }

        function getStatusDisplayName(status) {
            // Use formatStatusText for consistency
            return formatStatusText(status);
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

        // Feedback modal functions
        let feedbackModalTimeout = null;
        let selectedRating = 0;

        function initializeFeedbackModal() {
            // Remove existing event listeners by cloning and replacing
            const starContainer = document.getElementById('starRating');
            if (!starContainer) {
                console.error('Star rating container not found');
                return;
            }

            // Get all stars within the container
            const stars = starContainer.querySelectorAll('.star');
            
            // Remove any existing listeners by removing and re-adding event listeners
            stars.forEach((star, index) => {
                // Clone the star to remove all event listeners
                const newStar = star.cloneNode(true);
                star.parentNode.replaceChild(newStar, star);
                
                // Add click event
                newStar.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    selectedRating = parseInt(this.getAttribute('data-rating'));
                    console.log('Star clicked, rating:', selectedRating);
                    updateStarDisplay(selectedRating);
                    const submitBtn = document.getElementById('feedbackSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        console.log('Submit button enabled');
                    }
                });

                // Add hover event
                newStar.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    updateStarDisplay(rating, true);
                });
            });

            // Add mouse leave event to container
            starContainer.addEventListener('mouseleave', function() {
                updateStarDisplay(selectedRating);
            });
        }

        function checkAndScheduleFeedbackModal() {
            console.log('Checking if feedback modal should be scheduled...');
            
            // Check if this is a new submission (from session storage, URL parameter, or recent creation)
            const urlParams = new URLSearchParams(window.location.search);
            const isNewSubmission = urlParams.get('new') === 'true' || sessionStorage.getItem('new_submission_' + trackingNumber) === 'true';
            
            // Also check if request was created recently (within last 5 minutes)
            let isRecentSubmission = false;
            if (trackingData && trackingData.created_at) {
                const createdAt = new Date(trackingData.created_at);
                const now = new Date();
                const minutesDiff = (now - createdAt) / (1000 * 60);
                isRecentSubmission = minutesDiff <= 5; // Show if created within last 5 minutes
                console.log('Request created ' + minutesDiff.toFixed(2) + ' minutes ago');
            }
            
            if (isNewSubmission || isRecentSubmission) {
                console.log('New/recent submission detected, scheduling feedback modal');
                // Mark that we've checked this submission
                sessionStorage.removeItem('new_submission_' + trackingNumber);
                // Schedule feedback modal to show after 30 seconds
                scheduleFeedbackModal();
            } else {
                console.log('Not a new submission, skipping feedback modal');
            }
        }

        function scheduleFeedbackModal() {
            console.log('Scheduling feedback modal...');
            
            // Clear any existing timeout
            if (feedbackModalTimeout) {
                clearTimeout(feedbackModalTimeout);
            }

            // Check if feedback was already submitted for this tracking number
            const feedbackKey = 'feedback_submitted_' + trackingNumber;
            if (sessionStorage.getItem(feedbackKey)) {
                console.log('Feedback already submitted for this tracking number');
                return; // Don't show if already submitted
            }

            if (!trackingNumber) {
                console.warn('No tracking number available for feedback modal');
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
                // Initialize star interactions when modal is shown
                initializeFeedbackModal();
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

        function updateStarDisplay(rating, isHover = false) {
            const starContainer = document.getElementById('starRating');
            if (!starContainer) {
                console.error('Star rating container not found');
                return;
            }
            
            const stars = starContainer.querySelectorAll('.star');
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
                if (!svg) {
                    console.error('SVG not found in star', index);
                    return;
                }
                const path = svg.querySelector('path');
                if (!path) {
                    console.error('Path not found in star SVG', index);
                    return;
                }

                if (starRating <= rating) {
                    // Use gradient for filled stars
                    path.setAttribute('fill', 'url(#starGradient)');
                    path.setAttribute('stroke', 'url(#starGradient)');
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

            if (!trackingNumber) {
                showToast('Unable to submit feedback. Tracking number not found.', 'error');
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
            const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
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
                tracking_number: trackingNumber,
                rating: selectedRating,
                message: messageValue || null
            };

            console.log('Submitting feedback:', feedbackData);

            fetch('/api/feedback/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Request-Source': 'website',
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
                        const feedbackKey = 'feedback_submitted_' + trackingNumber;
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

        // Toast notification functions
        function showToast(message, type = 'info', duration = 4000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

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

            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, duration);
        }

    </script>
@endsection