@extends('website.layouts.app')

@section('title', 'Track Your Service - Click2Serve')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/reference_code.css') }}">

    <div class="tracking-page">
        <div class="tracking-container">
            <div class="tracking-card">
                <div class="tracking-header">
                    <h1>Track Your Appointment</h1>
                    <p>Enter your reference code to check the status of your appointment</p>
                </div>

                <form action="{{ route('website.track') }}" method="POST" class="tracking-form" id="trackingForm">
                    @csrf
                    <div class="form-group">
                        <label for="reference_code">Reference Code</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                id="reference_code"
                                name="reference_code"
                                placeholder="Enter reference code (e.g., APT202511304308, C2S-20251130-2799)" 
                                class="tracking-input"
                                required 
                                value="{{ old('reference_code') }}" 
                                autocomplete="off" 
                                maxlength="20">
                            <div class="input-validation">
                                <svg class="validation-icon valid-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <svg class="validation-icon invalid-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="input-help">
                            Reference codes start with 'APT', 'C2S', or 'TRK' and are 10-20 characters long
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="error-message">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <div class="error-content">
                                <strong>Tracking Error</strong>
                                <p>{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="track-btn" id="trackBtn" disabled>
                        <span>Track Appointment</span>
                        <svg class="btn-loading" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12a9 9 0 11-6.219-8.56"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Enhanced tracking functionality
        let recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');

        function fillTrackingCode(code, event) {
            const input = document.getElementById('reference_code') || document.querySelector('input[name="reference_code"]');
            if (!input) return;
            
            input.value = code;
            input.focus();

            // Trigger validation
            validateInput(input);

            // Auto-submit after a short delay if valid
            setTimeout(() => {
                if (isValidReferenceCode(code)) {
                    const form = document.getElementById('trackingForm');
                    if (form) {
                        form.submit();
                    }
                }
            }, 500);
        }

        function isValidReferenceCode(code) {
            if (!code) return false;
            const trimmed = code.trim().toUpperCase();
            // Accept TRK, APT, or C2S codes, minimum 10 characters
            return trimmed.length >= 10 && (
                trimmed.startsWith('TRK') || 
                trimmed.startsWith('APT') || 
                trimmed.startsWith('C2S')
            );
        }

        function validateInput(input) {
            if (!input) return false;
            
            const value = input.value.trim();
            const isValid = isValidReferenceCode(value);

            // Update input styling
            input.classList.toggle('valid', isValid && value.length > 0);
            input.classList.toggle('invalid', !isValid && value.length > 0);

            // Update validation icons
            const validIcon = input.parentNode.querySelector('.valid-icon');
            const invalidIcon = input.parentNode.querySelector('.invalid-icon');

            if (validIcon && invalidIcon) {
                validIcon.style.display = isValid && value.length > 0 ? 'block' : 'none';
                invalidIcon.style.display = !isValid && value.length > 0 ? 'block' : 'none';
            }

            // Update button state - try multiple selectors
            let trackBtn = document.getElementById('trackBtn');
            if (!trackBtn) {
                trackBtn = document.querySelector('.track-btn');
            }
            if (!trackBtn) {
                trackBtn = document.querySelector('button[type="submit"]');
            }

            if (trackBtn) {
                if (isValid) {
                    // Enable button - remove all disabled states
                    trackBtn.disabled = false;
                    trackBtn.removeAttribute('disabled');
                    trackBtn.classList.remove('disabled');
                    trackBtn.style.opacity = '1';
                    trackBtn.style.cursor = 'pointer';
                    trackBtn.style.pointerEvents = 'auto';
                    // Force enable by removing disabled class and attribute
                    trackBtn.removeAttribute('aria-disabled');
                } else {
                    // Disable button
                    trackBtn.disabled = true;
                    trackBtn.setAttribute('disabled', 'disabled');
                    trackBtn.classList.add('disabled');
                    trackBtn.style.opacity = '0.6';
                    trackBtn.style.cursor = 'not-allowed';
                    trackBtn.style.pointerEvents = 'none';
                }
            }

            return isValid;
        }

        function addToRecentSearches(code, service) {
            // Remove if already exists
            recentSearches = recentSearches.filter(item => item.code !== code);

            // Add to beginning
            recentSearches.unshift({ code, service, timestamp: Date.now() });

            // Keep only last 5
            recentSearches = recentSearches.slice(0, 5);

            // Save to localStorage
            localStorage.setItem('recentSearches', JSON.stringify(recentSearches));

            // Update UI
            updateRecentSearches();
        }

        function updateRecentSearches() {
            const recentSection = document.getElementById('recentSearches');
            const recentList = document.getElementById('recentList');

            // Check if elements exist before manipulating
            if (!recentSection || !recentList) {
                return; // Elements don't exist, skip silently
            }

            if (recentSearches.length > 0) {
                recentSection.style.display = 'block';
                // Escape HTML to prevent XSS
                recentList.innerHTML = recentSearches.map(item => {
                    const code = String(item.code || '').replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const service = String(item.service || 'Service Request').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    return `
                                <div class="recent-item" onclick="fillTrackingCode('${code}')">
                                    <div class="recent-icon">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <div class="recent-content">
                                        <code>${code}</code>
                                        <span>${service}</span>
                                    </div>
                                    <div class="recent-time">
                                        ${formatTimeAgo(item.timestamp)}
                                    </div>
                                </div>
                            `;
                }).join('');
            } else {
                recentSection.style.display = 'none';
            }
        }

        function formatTimeAgo(timestamp) {
            const now = Date.now();
            const diff = now - timestamp;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 1) return 'Just now';
            if (minutes < 60) return `${minutes}m ago`;
            if (hours < 24) return `${hours}h ago`;
            return `${days}d ago`;
        }

        // Enhanced form functionality
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('reference_code') || document.querySelector('input[name="reference_code"]');
            const trackBtn = document.getElementById('trackBtn') || document.querySelector('.track-btn');

            // Debug: Log initial state
            if (!input) {
                console.error('Tracking input field not found!');
                return;
            }
            if (!trackBtn) {
                console.error('Track button not found!');
                return;
            }

            // Initialize recent searches
            try {
                if (typeof updateRecentSearches === 'function') {
                    updateRecentSearches();
                }
            } catch (e) {
                console.warn('Error initializing recent searches:', e);
            }

            // Validate on page load if there's a value (important for pre-filled values)
            if (input && input.value) {
                // Small delay to ensure DOM is ready
                setTimeout(() => validateInput(input), 100);
            }

            // Auto-focus on input if empty
            if (input && !input.value) {
                input.focus();
            }

            // Input validation and enhancement - use multiple event listeners
            if (input) {
                // Immediate validation on input (most important)
                input.addEventListener('input', function (e) {
                    e.stopPropagation();
                    validateInput(this);
                }, false);

                // Also validate on paste and change events
                input.addEventListener('paste', function(e) {
                    e.stopPropagation();
                    setTimeout(() => validateInput(this), 10);
                }, false);
                
                input.addEventListener('change', function(e) {
                    e.stopPropagation();
                    validateInput(this);
                }, false);
                
                // Validate on blur as well
                input.addEventListener('blur', function() {
                    validateInput(this);
                }, false);

                // Validate on keyup as fallback
                input.addEventListener('keyup', function() {
                    validateInput(this);
                }, false);

                // Force validation on any key press
                input.addEventListener('keydown', function() {
                    setTimeout(() => validateInput(this), 0);
                }, false);
            }

            // Enhanced form submission
            const trackingForm = document.getElementById('trackingForm');
            if (trackingForm) {
                trackingForm.addEventListener('submit', function (e) {
                    const input = document.getElementById('reference_code') || document.querySelector('input[name="reference_code"]');
                    const value = input ? input.value.trim() : '';

                    // Validate input
                    if (!value) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (typeof showError === 'function') {
                            showError('Please enter a reference code');
                        } else {
                            alert('Please enter a reference code');
                        }
                        if (input) input.focus();
                        return false;
                    }

                    if (!isValidReferenceCode(value)) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (typeof showError === 'function') {
                            showError('Please enter a valid reference code (starts with APT, C2S, or TRK and at least 10 characters)');
                        } else {
                            alert('Please enter a valid reference code (starts with APT, C2S, or TRK and at least 10 characters)');
                        }
                        if (input) input.focus();
                        return false;
                    }

                    // Validation passed - proceed with submission
                    // Add to recent searches if function exists
                    try {
                        if (typeof addToRecentSearches === 'function') {
                            const service = input.closest('.sample-code-item')?.dataset.service || 'Service Request';
                            addToRecentSearches(value, service);
                        }
                    } catch (e) {
                        // Ignore errors - form should still submit
                    }

                    // Show loading state
                    const submitBtn = document.getElementById('trackBtn') || document.querySelector('.track-btn');
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                    }
                    
                    // IMPORTANT: Don't prevent default - allow form to submit normally
                    // The form will POST to the server
                }, false); // Use capture phase false (bubbling phase)
            }

            // Add sample code hover effects
            document.querySelectorAll('.sample-code-item').forEach(item => {
                item.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateX(5px)';
                });

                item.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateX(0)';
                });
            });

            // Add feature item animations
            document.querySelectorAll('.feature-item').forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
            });

            // No need for button click handler - form submit will handle everything
            // The disabled attribute on the button will prevent clicks when invalid
        });

        function showError(message) {
            // Remove existing error messages
            const existingError = document.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }

            // Create new error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `
                                <i class="fas fa-exclamation-triangle"></i>
                                <div class="error-content">
                                    <h4>Invalid Input</h4>
                                    <p>${message}</p>
                                </div>
                            `;

            // Insert after the form
            const form = document.querySelector('.tracking-form');
            form.parentNode.insertBefore(errorDiv, form.nextSibling);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }

        // Add smooth scrolling for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
@endsection