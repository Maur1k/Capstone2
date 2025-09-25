<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Tracker - Click2Serve</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tracker.css') }}">
</head>

<body>
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

                <div class="main-content">
                    <div class="tracker-panel">
                        <div class="tracker-header">
                            <div class="client">
                                <div class="client-name" id="clientName">Loading...</div>
                                <div class="client-desc" id="serviceDesc">Loading service details...</div>
                            </div>
                            <div class="meta">
                                <div class="ref">Tracking NO. <span id="refNo">Loading...</span></div>
                                <div class="status">
                                    <span class="dot" id="statusDot"></span>
                                    <span id="statusText">Loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="queue-display">
                            <div class="queue-label">Queueing</div>
                            <div class="queue-number">NO. <span id="queueNo">Loading...</span></div>
                        </div>

                        <div class="request-details" id="requestDetails" style="display: none;">
                            <h3 style="margin-bottom: 15px; color: var(--text-primary);">Request Details</h3>
                            <div class="detail-grid"
                                style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                <div class="detail-item">
                                    <strong>Date Submitted:</strong>
                                    <span id="dateSubmitted">-</span>
                                </div>
                                <div class="detail-item">
                                    <strong>Time Submitted:</strong>
                                    <span id="timeSubmitted">-</span>
                                </div>
                                <div class="detail-item">
                                    <strong>Contact Number:</strong>
                                    <span id="contactNumber">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="status-timeline" id="statusTimeline" style="display: none;">
                            <h3 style="margin-bottom: 15px; color: var(--text-primary);">Progress Timeline</h3>
                            <div class="timeline">
                                <div class="timeline-item active" id="step1">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <strong>Request Submitted</strong>
                                        <small id="step1Time">Just now</small>
                                    </div>
                                </div>
                                <div class="timeline-item" id="step2">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <strong>Under Review</strong>
                                        <small>Pending</small>
                                    </div>
                                </div>
                                <div class="timeline-item" id="step3">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <strong>Processing</strong>
                                        <small>Pending</small>
                                    </div>
                                </div>
                                <div class="timeline-item" id="step4">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <strong>Ready for Pickup</strong>
                                        <small>Pending</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tracker-actions">
                            <button class="secondary-btn" onclick="printReceipt()">Print Receipt</button>
                            <button class="secondary-btn" onclick="goHome()">Home</button>
                        </div>
                    </div>
                </div>
            </div>

            <button class="chat" aria-label="chat" onclick="navigateToChatbot()">
                <img src="{{ asset('assets/icons/chatbot.png') }}" alt="chat" />
            </button>
            <div class="footer">Powered by: Smart Municipal Kiosk</div>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }

        function goHome() {
            window.location.href = '/';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        function printReceipt() {
            const trackingData = sessionStorage.getItem('currentTrackingData');
            if (trackingData) {
                const data = JSON.parse(trackingData);
                // Call print service again
                fetch('/api/request/print-receipt', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        tracking_number: data.tracking_number
                    })
                }).then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('Receipt printed successfully!');
                        } else {
                            alert('Print failed: ' + result.message);
                        }
                    }).catch(error => {
                        alert('Print service error: ' + error.message);
                    });
            } else {
                alert('No tracking data available for printing.');
            }
        }

        // Load tracking data when page loads
        document.addEventListener('DOMContentLoaded', function () {
            // Check URL parameters first (for direct access)
            const urlParams = new URLSearchParams(window.location.search);
            const trackingNumber = urlParams.get('tracking');
            const queueNumber = urlParams.get('queue');

            // Check session storage (from form submission)
            const trackingData = sessionStorage.getItem('currentTrackingData');

            if (trackingData) {
                displayTrackingData(JSON.parse(trackingData));
            } else if (trackingNumber) {
                // Fetch data from API using tracking number
                fetchTrackingData(trackingNumber);
            } else {
                // No tracking data available
                displayError('No tracking information available.');
            }

            // Set up auto-refresh for status updates (every 30 seconds)
            setInterval(function () {
                const currentData = sessionStorage.getItem('currentTrackingData');
                if (currentData) {
                    const data = JSON.parse(currentData);
                    refreshTrackingStatus(data.tracking_number);
                }
            }, 30000); // 30 seconds
        });

        function displayTrackingData(data) {
            // Update client information
            document.getElementById('clientName').textContent = data.full_name || 'Unknown Client';
            document.getElementById('serviceDesc').textContent = `Requesting for ${data.document_type || 'Service Request'}`;

            // Update tracking information
            document.getElementById('refNo').textContent = data.tracking_number || 'N/A';
            document.getElementById('queueNo').textContent = data.queue_number || 'N/A';

            // Update status
            const status = data.status || 'SUBMITTED';
            document.getElementById('statusText').textContent = status.toUpperCase();
            updateStatusDot(status);

            // Show additional details
            document.getElementById('requestDetails').style.display = 'block';
            document.getElementById('dateSubmitted').textContent = data.date || new Date().toLocaleDateString();
            document.getElementById('timeSubmitted').textContent = data.time || new Date().toLocaleTimeString();
            document.getElementById('contactNumber').textContent = data.contact_number || 'N/A';

            // Show timeline
            document.getElementById('statusTimeline').style.display = 'block';
            updateTimeline(status);

            // Update step1 time
            document.getElementById('step1Time').textContent = `${data.date} ${data.time}`;

            // Store data for future use
            sessionStorage.setItem('currentTrackingData', JSON.stringify(data));
        }

        function updateStatusDot(status) {
            const dot = document.getElementById('statusDot');
            dot.className = 'dot'; // Reset classes

            switch (status.toLowerCase()) {
                case 'submitted':
                case 'pending':
                    dot.classList.add('pending');
                    break;
                case 'in_progress':
                case 'under_review':
                case 'processing':
                    dot.classList.add('progress');
                    break;
                case 'completed':
                case 'ready':
                case 'ready_for_pickup':
                    dot.classList.add('completed');
                    break;
                case 'rejected':
                case 'cancelled':
                    dot.classList.add('rejected');
                    break;
                default:
                    dot.classList.add('pending');
            }
        }

        function updateTimeline(status) {
            const steps = ['submitted', 'under_review', 'processing', 'ready_for_pickup'];
            const currentStep = getStepFromStatus(status.toLowerCase());

            steps.forEach((step, index) => {
                const element = document.getElementById(`step${index + 1}`);
                if (index < currentStep) {
                    element.classList.add('active', 'completed');
                } else if (index === currentStep) {
                    element.classList.add('active');
                    element.classList.remove('completed');
                } else {
                    element.classList.remove('active', 'completed');
                }
            });
        }

        function getStepFromStatus(status) {
            switch (status) {
                case 'submitted':
                case 'pending':
                    return 0;
                case 'under_review':
                case 'reviewing':
                    return 1;
                case 'processing':
                case 'in_progress':
                    return 2;
                case 'ready':
                case 'ready_for_pickup':
                case 'completed':
                    return 3;
                default:
                    return 0;
            }
        }

        function fetchTrackingData(trackingNumber) {
            fetch(`/api/request/track/${trackingNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayTrackingData(data.data);
                    } else {
                        displayError(data.message || 'Tracking data not found.');
                    }
                })
                .catch(error => {
                    displayError('Unable to load tracking information.');
                });
        }

        function refreshTrackingStatus(trackingNumber) {
            fetch(`/api/request/status/${trackingNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update only the status-related elements
                        document.getElementById('statusText').textContent = data.status.toUpperCase();
                        updateStatusDot(data.status);
                        updateTimeline(data.status);

                        // Update stored data
                        const currentData = JSON.parse(sessionStorage.getItem('currentTrackingData'));
                        currentData.status = data.status;
                        sessionStorage.setItem('currentTrackingData', JSON.stringify(currentData));
                    }
                })
                .catch(error => {
                    console.log('Status refresh failed:', error);
                });
        }

        function displayError(message) {
            document.getElementById('clientName').textContent = 'Error';
            document.getElementById('serviceDesc').textContent = message;
            document.getElementById('refNo').textContent = 'N/A';
            document.getElementById('queueNo').textContent = 'N/A';
            document.getElementById('statusText').textContent = 'ERROR';
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