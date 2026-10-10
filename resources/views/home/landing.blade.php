<!-- Landing Page / Splash Screen Component -->
<div class="landing-overlay" id="landingOverlay">
    <!-- Background pattern -->
    <div class="landing-bg-pattern"></div>
    
    <!-- Floating particles -->
    <div class="landing-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="landing-content">
        <div class="landing-logo">
            <div class="landing-icon-ring"></div>
            <img src="{{ asset('assets/logo/logo_click2serve.png') }}" alt="Click2Serve Logo" class="landing-logo-img">
        </div>
        
        <p class="landing-title">Alaminos City Hall Kiosk</p>
        <p class="landing-tagline">Your gateway to efficient city services</p>
        
        
    </div>
    
    <div class="landing-hint">
        Tap anywhere to continue
    </div>
</div>

<script>
    // Landing Page / Splash Screen Logic
    (function() {
        const landingOverlay = document.getElementById('landingOverlay');
        const IDLE_TIMEOUT = 3 * 60 * 1000; // 3 minutes in milliseconds
        let idleTimer = null;
        
        // Use sessionStorage to track first load and last activity across page navigations
        const SESSION_KEY = 'kiosk_landing_shown';
        const ACTIVITY_KEY = 'kiosk_last_activity';
        
        // Check if landing page has been shown in this session
        const hasBeenShownInSession = sessionStorage.getItem(SESSION_KEY) === 'true';
        
        // Get last activity time from sessionStorage or use current time
        let lastActivityTime = parseInt(sessionStorage.getItem(ACTIVITY_KEY)) || Date.now();

        // Show landing page
        function showLanding() {
            if (landingOverlay) {
                landingOverlay.classList.add('active');
                // Reset activity timer
                lastActivityTime = Date.now();
                sessionStorage.setItem(ACTIVITY_KEY, lastActivityTime.toString());
            }
        }

        // Hide landing page
        function hideLanding() {
            if (landingOverlay) {
                landingOverlay.classList.remove('active');
                // Reset activity timer
                lastActivityTime = Date.now();
                sessionStorage.setItem(ACTIVITY_KEY, lastActivityTime.toString());
                // Mark that landing has been shown in this session
                sessionStorage.setItem(SESSION_KEY, 'true');
                // Clear any existing idle timer
                if (idleTimer) {
                    clearTimeout(idleTimer);
                    idleTimer = null;
                }
                // Start idle timer
                startIdleTimer();
            }
        }

        // Start idle timer
        function startIdleTimer() {
            if (idleTimer) {
                clearTimeout(idleTimer);
            }
            idleTimer = setTimeout(() => {
                showLanding();
            }, IDLE_TIMEOUT);
        }

        // Reset idle timer on user activity
        function resetIdleTimer() {
            const now = Date.now();
            // Only reset if there's been actual activity (not just timer checks)
            if (now - lastActivityTime > 1000) {
                lastActivityTime = now;
                sessionStorage.setItem(ACTIVITY_KEY, lastActivityTime.toString());
                if (!landingOverlay.classList.contains('active')) {
                    startIdleTimer();
                }
            }
        }

        // Enhanced touch gesture handling for iPad
        let touchStartTime = 0;
        let touchStartY = 0;
        let touchStartX = 0;
        let isTouchActive = false;

        // Handle touch start on landing overlay
        if (landingOverlay) {
            landingOverlay.addEventListener('touchstart', function(e) {
                touchStartTime = Date.now();
                if (e.touches.length > 0) {
                    touchStartX = e.touches[0].clientX;
                    touchStartY = e.touches[0].clientY;
                }
                isTouchActive = true;
                // Add visual feedback
                this.style.opacity = '0.95';
            }, { passive: true });

            landingOverlay.addEventListener('touchmove', function(e) {
                if (isTouchActive && e.touches.length > 0) {
                    // Prevent default scrolling while touching
                    const deltaX = Math.abs(e.touches[0].clientX - touchStartX);
                    const deltaY = Math.abs(e.touches[0].clientY - touchStartY);
                    // If significant movement, allow it (user is scrolling)
                    if (deltaY > 10 || deltaX > 10) {
                        // User is moving, don't prevent default
                    }
                }
            }, { passive: true });

            landingOverlay.addEventListener('touchend', function(e) {
                const touchDuration = Date.now() - touchStartTime;
                // Hide on any touch (tap, swipe, etc.)
                if (isTouchActive && touchDuration < 1000) {
                    hideLanding();
                }
                isTouchActive = false;
                this.style.opacity = '';
            }, { passive: true });

            // Also handle click for mouse/trackpad
            landingOverlay.addEventListener('click', function(e) {
                hideLanding();
            }, { passive: true });
        }

        // Track user activity events (enhanced for iPad)
        const activityEvents = [
            'mousedown', 'mousemove', 'keypress', 'scroll', 
            'touchstart', 'touchmove', 'touchend', 'click', 'keydown',
            'pointerdown', 'pointermove', 'pointerup'
        ];

        activityEvents.forEach(event => {
            document.addEventListener(event, function(e) {
                // Don't trigger on landing overlay itself (handled separately)
                if (e.target && e.target.closest('.landing-overlay')) {
                    return;
                }

                if (landingOverlay && landingOverlay.classList.contains('active')) {
                    // If landing is showing, hide it on any interaction outside
                    hideLanding();
                } else {
                    // Otherwise, reset idle timer
                    resetIdleTimer();
                }
            }, { passive: true });
        });

        // Show landing page on initial website load, page reload, or after idle timeout
        document.addEventListener('DOMContentLoaded', function () {
            // Check navigation type to detect page reload
            const navigationType = performance.getEntriesByType('navigation')[0]?.type || 'navigate';
            const isPageReload = navigationType === 'reload';
            
            // Check if this is the first load of the session
            if (!hasBeenShownInSession) {
                // First load - show landing page
                showLanding();
            } else if (isPageReload) {
                // Page reload - show landing page
                showLanding();
            } else {
                // Page navigation (not reload) - check if we should show landing based on idle time
                const timeSinceLastActivity = Date.now() - lastActivityTime;
                if (timeSinceLastActivity >= IDLE_TIMEOUT) {
                    // User has been idle, show landing page
                    showLanding();
                } else {
                    // User has been active, start idle timer with remaining time
                    const remainingTime = IDLE_TIMEOUT - timeSinceLastActivity;
                    if (remainingTime > 0) {
                        idleTimer = setTimeout(() => {
                            showLanding();
                        }, remainingTime);
                    }
                }
            }

            const trackingInput = document.getElementById('trackingInput');
            if (trackingInput) {
                // Delay focus until landing is hidden
                setTimeout(() => {
                    if (!landingOverlay.classList.contains('active')) {
                        trackingInput.focus();
                    }
                }, 500);
            }
        });

        // Also handle visibility change (when tab/window becomes visible)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Check if we should show landing based on idle time
                const timeSinceLastActivity = Date.now() - lastActivityTime;
                if (timeSinceLastActivity >= IDLE_TIMEOUT) {
                    showLanding();
                } else {
                    startIdleTimer();
                }
            } else {
                // Pause timer when tab is hidden
                if (idleTimer) {
                    clearTimeout(idleTimer);
                    idleTimer = null;
                }
            }
        });
    })();
</script>

