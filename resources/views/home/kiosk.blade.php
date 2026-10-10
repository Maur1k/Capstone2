<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Click2Serve Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/kiosk.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing-kiosk.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tracker.css') }}?v={{ time() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @include('home.landing')

    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <div class="header"></div>

                <div class="hero">
                    <div class="photo-panel">
                        <div class="slides">
                            @php
                                $images = \App\Models\PhotoPanelImage::active()->ordered()->get();
                            @endphp
                            @if($images->count() > 0)
                                @foreach($images as $index => $image)
                                    <img class="slide {{ $index === 0 ? 'active' : '' }}" src="{{ $image->image_url }}"
                                        alt="{{ $image->alt_text ?: $image->title }}">
                                @endforeach
                            @else
                                {{-- Fallback images if no database images exist --}}
                                <img class="slide active" src="{{ asset('assets/images/slide1.jpg') }}" alt="slide 1">
                                <img class="slide" src="{{ asset('assets/images/slide2.jpg') }}" alt="slide 2">
                                <img class="slide" src="{{ asset('assets/images/slide3.jpg') }}" alt="slide 3">
                            @endif
                        </div>
                    </div>

                    <div class="center-col">
                        <form id="trackingForm" onsubmit="handleTrackingSubmit(event)">
                            <input class="input-pill" id="trackingInput" placeholder="Enter Reference Code"
                                autocomplete="off" required />
                            <button type="submit" class="track-btn">TRACK</button>
                        </form>
                        <div class="cards">
                            <div class="card" onclick="navigateToServices()">
                                <img src="{{ asset('assets/icons/Services.png') }}" alt="services" />
                                <h3>Services</h3>
                            </div>
                            <div class="card" onclick="navigateTo3DMap()">
                                <img src="{{ asset('assets/icons/3dmap.png') }}" alt="map" />
                                <h3>3D Map &amp; Navigation</h3>
                            </div>
                            <div class="card" onclick="navigateToMunicipalInfo()">
                                <img src="{{ asset('assets/icons/municipalinfo.png') }}" alt="info" />
                                <h3>Municipal Informations</h3>
                            </div>
                        </div>
                    </div>

                    <div class="mayor">
                        <div class="mayor-figure"></div>
                    </div>
                </div>
            </div>
            <button class="flowchart-btn" aria-label="help" onclick="openFlowchart()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 3H21V21H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M3 9H9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M15 9H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M9 3V9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M15 3V9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M9 15H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M12 15V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span>Help</span>
            </button>
            <button class="feedback-btn-kiosk" aria-label="feedback" onclick="openFeedbackModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Feedback</span>
            </button>
            <button class="chat" aria-label="chat" onclick="navigateToChatbot()">
                <img src="{{ asset('assets/icons/chatbot.png') }}" alt="chat" />
                <span>Chat</span>
            </button>
            <div class="footer">Powered by: Click2Serve</div>
        </div>
    </div>

    <!-- Flowchart Modal -->
    <div class="flowchart-overlay" id="flowchartOverlay" onclick="closeFlowchartOnOverlay(event)">
        <div class="flowchart-modal" onclick="event.stopPropagation()">
            <div class="flowchart-header">
                <h2>How to Use the Kiosk</h2>
                <button class="flowchart-close" onclick="closeFlowchart()" aria-label="Close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
            <div class="flowchart-content">
                <!-- Services Flow -->
                <div class="flowchart-section">
                    <div class="flowchart-section-title">
                        <img src="{{ asset('assets/icons/Services.png') }}" alt="Services" class="section-icon">
                        <span>How to Request a Service</span>
                    </div>
                    <div class="flowchart-steps">
                        <div class="flowchart-step">
                            <div class="step-box step-box-service">
                                <div class="step-number">1</div>
                                <div class="step-text">Choose your desired Service</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-service">
                                <div class="step-number">2</div>
                                <div class="step-text">Review Requirements & Details</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-service">
                                <div class="step-number">3</div>
                                <div class="step-text">Fill out the Online Form</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-service">
                                <div class="step-number">4</div>
                                <div class="step-text">Get Tracking Number & Print</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-service">
                                <div class="step-number">5</div>
                                <div class="step-text">Track Status & Visit Office</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3D Map Flow -->
                <div class="flowchart-section">
                    <div class="flowchart-section-title">
                        <img src="{{ asset('assets/icons/3dmap.png') }}" alt="3D Map" class="section-icon">
                        <span>How to Use 3D Navigation</span>
                    </div>
                    <div class="flowchart-steps">
                        <div class="flowchart-step">
                            <div class="step-box step-box-map">
                                <div class="step-number">1</div>
                                <div class="step-text">Open the 3D Map View</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-map">
                                <div class="step-number">2</div>
                                <div class="step-text">Search for specific Offices</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-map">
                                <div class="step-number">3</div>
                                <div class="step-text">Follow Direction to Location</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Municipal Information Flow -->
                <div class="flowchart-section">
                    <div class="flowchart-section-title">
                        <img src="{{ asset('assets/icons/municipalinfo.png') }}" alt="Municipal Info"
                            class="section-icon">
                        <span>Explore Municipal Information</span>
                    </div>
                    <div class="flowchart-steps">
                        <div class="flowchart-step">
                            <div class="step-box step-box-info">
                                <div class="step-number">1</div>
                                <div class="step-text">Select Officials or Tourist Spots</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-info">
                                <div class="step-number">2</div>
                                <div class="step-text">Explore Categories & Names</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-info">
                                <div class="step-number">3</div>
                                <div class="step-text">View Full Profiles & Photos</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ChatBot Flow -->
                <div class="flowchart-section">
                    <div class="flowchart-section-title">
                        <img src="{{ asset('assets/icons/chatbot.png') }}" alt="ChatBot" class="section-icon">
                        <span>Get Help from Click2Serve AI</span>
                    </div>
                    <div class="flowchart-steps">
                        <div class="flowchart-step">
                            <div class="step-box step-box-chat">
                                <div class="step-number">1</div>
                                <div class="step-text">Tap the ChatBot Assistant</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-chat">
                                <div class="step-number">2</div>
                                <div class="step-text">Type or Speak your Question</div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step-box step-box-chat">
                                <div class="step-number">3</div>
                                <div class="step-text">Get Instant Smart Answers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="feedback-modal-overlay" onclick="closeFeedbackModalOnOverlay(event)">
        <div class="feedback-modal-content" onclick="event.stopPropagation()">
            <button class="feedback-modal-close" onclick="closeFeedbackModal()" aria-label="Close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
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
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="star" data-rating="2" aria-label="2 stars">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="star" data-rating="3" aria-label="3 stars">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="star" data-rating="4" aria-label="4 stars">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="star" data-rating="5" aria-label="5 stars">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <p class="rating-text" id="ratingText">Tap to rate</p>
                </div>
                <div class="feedback-message-container">
                    <label for="feedbackMessage" class="feedback-label">Your feedback (optional)</label>
                    <textarea id="feedbackMessage" class="feedback-message-input"
                        placeholder="Share your thoughts about your experience..." rows="4"></textarea>
                </div>
            </div>
            <div class="feedback-modal-footer">
                <button type="button" class="feedback-submit-btn" id="feedbackSubmitBtn" onclick="submitFeedback()"
                    disabled>
                    <span class="btn-text">Submit Feedback</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="notification-modal-overlay" onclick="closeNotificationModalOnOverlay(event)">
        <div class="notification-modal-content" onclick="event.stopPropagation()">
            <div class="notification-icon" id="notificationIcon"></div>
            <div class="notification-header">
                <h2 id="notificationTitle">Notification</h2>
            </div>
            <div class="notification-body">
                <p id="notificationMessage"></p>
            </div>
            <div class="notification-footer">
                <button type="button" class="notification-btn" id="notificationBtn" onclick="closeNotificationModal()">OK</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const panel = document.querySelector('.photo-panel');
            if (!panel) return;
            const slides = Array.from(panel.querySelectorAll('.slide'));
            let i = 0; const n = slides.length;

            function show(idx) {
                // Remove active class from all slides
                slides.forEach((el, j) => el.classList.toggle('active', j === idx));
                i = idx;
            }

            // Auto-scroll every 8 seconds (slower)
            let timer = setInterval(() => { show((i + 1) % n); }, 8000);

            // Touch/swipe support for mobile
            let startX = 0;
            let isScrolling = false;

            panel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isScrolling = true;
                clearInterval(timer); // Pause auto-scroll during touch
            });

            panel.addEventListener('touchmove', (e) => {
                if (!isScrolling) return;
                e.preventDefault(); // Prevent vertical scroll
            });

            panel.addEventListener('touchend', (e) => {
                if (!isScrolling) return;
                isScrolling = false;

                const endX = e.changedTouches[0].clientX;
                const diffX = startX - endX;
                const threshold = 50;

                if (Math.abs(diffX) > threshold) {
                    if (diffX > 0 && i < n - 1) {
                        show(i + 1); // Swipe left - next slide
                    } else if (diffX < 0 && i > 0) {
                        show(i - 1); // Swipe right - previous slide
                    }
                }

                // Resume auto-scroll
                timer = setInterval(() => { show((i + 1) % n); }, 8000);
            });
        })();

        function navigateToServices() {
            window.location.href = '/services';
        }

        function navigateToMunicipalInfo() {
            window.location.href = '/municipal-information';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        function navigateTo3DMap() {
            window.location.href = '/3dmap';
        }

        function handleTrackingSubmit(event) {
            event.preventDefault();
            const trackingNumber = document.getElementById('trackingInput').value.trim();

            if (!trackingNumber) {
                showNotification('Required Field', 'Please enter a tracking number', 'warning');
                return;
            }

            // Show loading state
            const input = document.getElementById('trackingInput');
            const trackBtn = document.querySelector('.track-btn');
            const originalPlaceholder = input.placeholder;
            const originalBtnText = trackBtn.textContent;

            input.placeholder = 'Checking...';
            input.disabled = true;
            trackBtn.disabled = true;
            trackBtn.textContent = 'CHECKING...';

            // Check if tracking number exists
            fetch(`/api/request/track/${trackingNumber}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Navigate to tracker with tracking data
                        window.location.href = `/services/tracker?tracking=${trackingNumber}`;
                    } else {
                        // Reset input state
                        input.placeholder = originalPlaceholder;
                        input.disabled = false;
                        input.value = '';
                        trackBtn.disabled = false;
                        trackBtn.textContent = originalBtnText;
                        input.focus();
                        showNotification('Not Found', 'Tracking number not found. Please check your reference code.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Reset input state
                    input.placeholder = originalPlaceholder;
                    input.disabled = false;
                    input.value = '';
                    trackBtn.disabled = false;
                    trackBtn.textContent = originalBtnText;
                    input.focus();
                    showNotification('Error', 'Unable to check tracking number. Please try again.', 'error');
                });
        }

        // Landing page logic is now in home.landing component

        // Flowchart Modal Functions
        function openFlowchart() {
            const overlay = document.getElementById('flowchartOverlay');
            if (overlay) {
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeFlowchart() {
            const overlay = document.getElementById('flowchartOverlay');
            if (overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function closeFlowchartOnOverlay(event) {
            if (event.target.id === 'flowchartOverlay') {
                closeFlowchart();
            }
        }

        // Close flowchart on Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeFlowchart();
                closeFeedbackModal();
                closeNotificationModal();
            }
        });

        // Notification Modal Functions
        function showNotification(title, message, type = 'success') {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('notificationIcon');
            const titleEl = document.getElementById('notificationTitle');
            const messageEl = document.getElementById('notificationMessage');

            if (!modal || !icon || !titleEl || !messageEl) return;

            // Set icon type
            icon.className = 'notification-icon ' + type;

            // Set content
            titleEl.textContent = title;
            messageEl.textContent = message;

            // Show modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeNotificationModal() {
            const modal = document.getElementById('notificationModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        function closeNotificationModalOnOverlay(event) {
            if (event.target.id === 'notificationModal') {
                closeNotificationModal();
            }
        }

        // Feedback Modal Functions
        let selectedRating = 0;

        function openFeedbackModal() {
            const modal = document.getElementById('feedbackModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
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
            }
        }

        function closeFeedbackModal() {
            const modal = document.getElementById('feedbackModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        function closeFeedbackModalOnOverlay(event) {
            if (event.target.id === 'feedbackModal') {
                closeFeedbackModal();
            }
        }

        // Initialize star rating
        document.addEventListener('DOMContentLoaded', function () {
            const stars = document.querySelectorAll('.star');
            stars.forEach(star => {
                star.addEventListener('click', function () {
                    selectedRating = parseInt(this.getAttribute('data-rating'));
                    updateStarDisplay(selectedRating);
                    const submitBtn = document.getElementById('feedbackSubmitBtn');
                    if (submitBtn) submitBtn.disabled = false;
                });

                star.addEventListener('mouseenter', function () {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    updateStarDisplay(rating, true);
                });
            });

            const starContainer = document.getElementById('starRating');
            if (starContainer) {
                starContainer.addEventListener('mouseleave', function () {
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
                showNotification('Required Field', 'Please select a rating', 'warning');
                return;
            }

            const message = document.getElementById('feedbackMessage');
            const messageValue = message ? message.value.trim() : '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            const submitBtn = document.getElementById('feedbackSubmitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            if (!submitBtn) {
                return;
            }

            submitBtn.disabled = true;
            if (btnText) {
                btnText.textContent = 'Submitting...';
            } else {
                submitBtn.textContent = 'Submitting...';
            }

            // Submit feedback (without tracking number for kiosk homescreen)
            const feedbackData = {
                rating: selectedRating,
                message: messageValue || null
            };

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
                    const contentType = response.headers.get('content-type');
                    let result;

                    if (contentType && contentType.includes('application/json')) {
                        result = await response.json();
                    } else {
                        const text = await response.text();
                        throw new Error('Server returned non-JSON response');
                    }

                    if (!response.ok) {
                        throw new Error(result.message || result.error || 'Server error');
                    }

                    return result;
                })
                .then(result => {
                    if (result.success) {
                        closeFeedbackModal();
                        showNotification('Thank You!', 'Thank you for your feedback!', 'success');
                    } else {
                        const errorMsg = result.message || result.error || 'Failed to submit feedback';
                        showNotification('Error', errorMsg, 'error');
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
                    showNotification('Error', errorMsg, 'error');
                    submitBtn.disabled = false;
                    if (btnText) {
                        btnText.textContent = 'Submit Feedback';
                    } else {
                        submitBtn.textContent = 'Submit Feedback';
                    }
                });
        }
    </script>
</body>

</html>