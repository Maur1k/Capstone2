<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    @php
        // Format display names for civil registry services
        $displayServiceName = $serviceTypeName ?? 'Service Requirements';
        if (strpos($displayServiceName, 'Delayed Regular Non-BRAP') !== false) {
            $displayServiceName = 'Delayed Birth Registration (Regular)';
        } elseif (strpos($displayServiceName, 'Delayed BRAP') !== false) {
            $displayServiceName = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
        }
    @endphp
    <title>{{ $displayServiceName }} - Click2Serve Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}?v={{ time() }}">
</head>

<body>
    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <!-- Header with back button -->
                <div class="header">
                    <div class="header-left">
                        <button class="back-button" onclick="goBack()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="header-center">
                        <h1 class="page-title">Step by Step Process</h1>
                    </div>
                    <div class="header-right">
                        <!-- Empty for now, can be used for future elements -->
                    </div>
                </div>

                <!-- Main content -->
                <div class="main-content">
                    <!-- Content Container - Single Scrollable Container -->
                    <div class="content-container">
                        <!-- Requirements Content - Scrollable Area (Single Container) -->
                        <div class="services-content requirements-scrollable">
                            <div class="requirements-section">
                                <!-- Requirements Header -->
                                <div class="requirements-header">
                                    <h2 class="requirements-title">{{ $displayServiceName }}</h2>
                                    <p class="requirements-subtitle">Please review the following steps and requirements
                                        before
                                        proceeding</p>
                                </div>

                                <!-- Steps List -->
                                @if($steps && $steps->count() > 0)
                                    <div class="steps-container">
                                        <button class="scroll-indicator" aria-label="Scroll down to see more steps" title="Scroll down">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M7 13l5 5 5-5M7 6l5 5 5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        @foreach($steps as $index => $step)
                                            <div class="step-card {{ $index === 0 ? 'current' : '' }}" tabindex="0"
                                                role="button" aria-label="Step {{ $index + 1 }}: {{ $step->title }}"
                                                onkeydown="handleStepKeyPress(event, {{ $index }})">
                                                <div class="step-header">
                                                    <div class="step-number">{{ $index + 1 }}</div>
                                                    <h3 class="step-title">{{ $step->title }}</h3>
                                                </div>
                                                <div class="step-content">
                                                    {!! nl2br(e($step->content)) !!}
                                                </div>
                                            </div>

                                            @if($index < $steps->count() - 1)
                                                <div class="arrow-down">
                                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="no-steps">
                                        <h3>No Requirements Available</h3>
                                        <p>This service doesn't have specific requirements listed yet.</p>
                                        <button class="btn-next" onclick="proceedToKiosk()" ontouchstart="proceedToKiosk()"
                                            tabindex="0" role="button" aria-label="Continue to Application">
                                            Continue to Application
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                @if($steps && $steps->count() > 0)
                                    <div class="action-buttons">
                                        <button class="btn-next" onclick="proceedToKiosk()" ontouchstart="proceedToKiosk()"
                                            tabindex="0" role="button" aria-label="Continue to next step">
                                            Next
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
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
            // Go back to main services page
            window.location.href = '/services';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        function proceedToKiosk() {
            // Proceed to kiosk form with all parameters
            const urlParams = new URLSearchParams(window.location.search);
            let service = urlParams.get('service');
            let department = urlParams.get('department');
            let selectedType = urlParams.get('selected_type');

            // Fallback: if parameters are not found, try to get them from the page title or use defaults
            if (!service || !department || !selectedType) {
                console.warn('Missing URL parameters, using fallback values');
                service = service || 'Prioritized Civil Services';
                department = department || 'Civil Registry Office';
                selectedType = selectedType || document.querySelector('.requirements-title')?.textContent?.trim() || 'Service Request';
            }

            // Determine service type based on selected type
            let serviceType = 'regular';
            if (selectedType && selectedType.includes('Delayed BRAP Birth Registration Assistance Project')) {
                serviceType = 'brap';
            } else if (selectedType && selectedType.includes('Delayed Regular Non-BRAP Birth Registration Assistance Project')) {
                serviceType = 'regular';
            }

            // Debug logging
            console.log('Proceeding to kiosk with:', {
                service: service,
                department: department,
                selectedType: selectedType,
                serviceType: serviceType
            });

            const kioskUrl = `/services/kiosk?service=${encodeURIComponent(service)}&department=${encodeURIComponent(department)}&type=${serviceType}&selected_type=${encodeURIComponent(selectedType)}`;
            console.log('Kiosk URL:', kioskUrl);

            // Add a small delay to ensure the click is processed
            setTimeout(() => {
                window.location.href = kioskUrl;
            }, 100);
        }

        function handleStepKeyPress(event, index) {
            // Handle Enter and Space key presses for step cards
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                // Focus on the step card for better accessibility
                event.target.focus();
            }
        }

        // Touch and accessibility enhancements
        document.addEventListener('DOMContentLoaded', function () {
            // Add animation to step cards
            const stepCards = document.querySelectorAll('.step-card');
            stepCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);

                // Add touch event listeners for better mobile experience
                card.addEventListener('touchstart', function (e) {
                    this.style.transform = 'translateY(-1px)';
                });

                card.addEventListener('touchend', function (e) {
                    this.style.transform = 'translateY(0)';
                });

                // Add click event for step cards (for touch devices)
                card.addEventListener('click', function (e) {
                    // Scroll the card into view if it's not fully visible
                    this.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });
                });
            });

            // Add touch-friendly button interactions
            const buttons = document.querySelectorAll('.btn-next');
            buttons.forEach(button => {
                button.addEventListener('touchstart', function (e) {
                    this.style.transform = 'translateY(-1px)';
                });

                button.addEventListener('touchend', function (e) {
                    this.style.transform = 'translateY(0)';
                });

                // Prevent double-tap zoom on buttons
                button.addEventListener('touchend', function (e) {
                    e.preventDefault();
                });
            });

            // Optimized scrolling behavior - single container
            const scrollContainer = document.querySelector('.services-content.requirements-scrollable');
            if (scrollContainer) {
                // Enable momentum scrolling on iOS
                scrollContainer.style.webkitOverflowScrolling = 'touch';

                let scrollTimeout;

                // Function to check if container is scrollable and update indicators
                function updateScrollIndicators() {
                    const isScrollable = scrollContainer.scrollHeight > scrollContainer.clientHeight;
                    const isAtBottom = scrollContainer.scrollHeight - scrollContainer.scrollTop <= scrollContainer.clientHeight + 5;
                    const isAtTop = scrollContainer.scrollTop < 5;

                    if (isScrollable) {
                        scrollContainer.classList.add('scrollable');
                        
                        // Hide bottom indicator when scrolled to bottom
                        if (isAtBottom) {
                            scrollContainer.classList.add('scrolled-to-bottom');
                        } else {
                            scrollContainer.classList.remove('scrolled-to-bottom');
                        }
                    } else {
                        scrollContainer.classList.remove('scrollable');
                    }
                }

                // Check on load
                updateScrollIndicators();

                // Update on scroll
                scrollContainer.addEventListener('scroll', function () {
                    updateScrollIndicators();
                    
                    // Add visual feedback during scroll
                    this.style.boxShadow = 'inset 0 0 10px rgba(14, 144, 166, 0.1)';
                    
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        this.style.boxShadow = 'none';
                    }, 150);
                });

                // Update on resize
                window.addEventListener('resize', updateScrollIndicators);
                
                // Use MutationObserver to detect content changes
                const observer = new MutationObserver(updateScrollIndicators);
                observer.observe(scrollContainer, { childList: true, subtree: true });

                // Make scroll indicator clickable/touchable
                const scrollIndicator = document.querySelector('.scroll-indicator');
                if (scrollIndicator) {
                    scrollIndicator.addEventListener('click', function() {
                        const scrollAmount = scrollContainer.clientHeight * 0.8; // Scroll 80% of visible height
                        scrollContainer.scrollBy({
                            top: scrollAmount,
                            behavior: 'smooth'
                        });
                    });

                    // Touch support for better mobile experience
                    scrollIndicator.addEventListener('touchstart', function(e) {
                        e.preventDefault();
                        this.style.transform = 'translateX(-50%) scale(0.95)';
                    });

                    scrollIndicator.addEventListener('touchend', function(e) {
                        e.preventDefault();
                        this.style.transform = 'translateX(-50%) scale(1)';
                        const scrollAmount = scrollContainer.clientHeight * 0.8;
                        scrollContainer.scrollBy({
                            top: scrollAmount,
                            behavior: 'smooth'
                        });
                    });
                }
            }

            // Add keyboard navigation support
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    goBack();
                }
            });

            // Focus management for accessibility
            const firstFocusable = document.querySelector('.step-card, .btn-next');
            if (firstFocusable) {
                firstFocusable.focus();
            }
        });

        // Prevent context menu on touch devices for better kiosk experience
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        // Prevent text selection on touch devices
        document.addEventListener('selectstart', function (e) {
            e.preventDefault();
        });
    </script>
</body>

</html>