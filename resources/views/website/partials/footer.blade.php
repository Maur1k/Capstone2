<footer>
    <div>
        <img src="{{ asset('assets/logo/logo_click2serve.png') }}" alt="Click2Serve Logo"
            style="height: 60px; width: auto; margin-bottom: 5px;">
        <p>Copyright © 2025 Click2Serve<br>All rights reserved</p>
        <div class="socials">
            <a href="https://www.facebook.com/cityofalaminos.pangasinan"><i class="fab fa-facebook"></i></a>
        </div>
    </div>
    <div>
        <h3>Company</h3>
        <ul>
            <li><a href="{{ route('website.homepage') }}#about">About us</a></li>
            <li><a href="{{ route('website.homepage') }}#blog">Blog</a></li>
            <li><a href="javascript:void(0)" onclick="openFooterModal('contact')">Contact us</a></li>
        </ul>
    </div>
    <div>
        <h3>Support</h3>
        <ul>
            <li><a href="javascript:void(0)" onclick="openFooterModal('terms')">Terms of service</a></li>
            <li><a href="javascript:void(0)" onclick="openFooterModal('privacy')">Privacy policy</a></li>
        </ul>
    </div>
    <div>
        <h3>Write Us Feedback</h3>
        <form id="websiteFeedbackForm" class="feedback-form">
            <div class="feedback-rating">
                <label class="rating-label">Rate Your Experience with Us</label>
                <div class="star-rating-input" id="starRatingContainer">
                    <input type="radio" name="rating" id="star1" value="1" required>
                    <label for="star1" class="star-label" data-rating="1">★</label>
                    <input type="radio" name="rating" id="star2" value="2" required>
                    <label for="star2" class="star-label" data-rating="2">★</label>
                    <input type="radio" name="rating" id="star3" value="3" required>
                    <label for="star3" class="star-label" data-rating="3">★</label>
                    <input type="radio" name="rating" id="star4" value="4" required>
                    <label for="star4" class="star-label" data-rating="4">★</label>
                    <input type="radio" name="rating" id="star5" value="5" required>
                    <label for="star5" class="star-label" data-rating="5">★</label>
                </div>
            </div>
            <div class="feedback-message">
                <textarea name="message" id="feedbackMessageInput"
                    placeholder="Share your thoughts about your experience..." rows="3" maxlength="1000"></textarea>
            </div>
            <button type="submit" class="feedback-submit-btn">
                <i class="fas fa-paper-plane"></i> Submit Feedback
            </button>
            <div id="feedbackMessageDisplay" class="feedback-message-display" style="display: none;"></div>
        </form>
    </div>
    <div>
        <h3>Credits</h3>
        <p class="credits-text">Content and images sourced from <a href="http://alaminoscity.gov.ph"
                target="_blank">Alaminos City Official Website</a>. All rights belong to the City Government of
            Alaminos.</p>
    </div>
</footer>

<!-- Footer Modals -->
<div id="footerModal" class="footer-modal">
    <div class="footer-modal-content">
        <div class="footer-modal-header">
            <h2 id="footerModalTitle">Title</h2>
            <button class="footer-modal-close" onclick="closeFooterModal()">&times;</button>
        </div>
        <div class="footer-modal-body" id="footerModalBody">
        </div>
        <div class="footer-modal-footer">
            <button onclick="closeFooterModal()" class="btn">Close</button>
        </div>
    </div>
</div>

<script>
    const footerModalData = {
        help: {
            title: "Help Center",
            content: `
                <h3>How can we help you?</h3>
                <p>Welcome to the Click2Serve Help Center. Find answers to common questions below.</p>
                
                <h4>Frequently Asked Questions</h4>
                
                <div class="faq-item">
                    <strong>How do I request a service?</strong>
                    <p>You can request services through our kiosk at the Municipal Hall or online through this platform. Navigate to "Services" and select the service you need.</p>
                </div>
                
                <div class="faq-item">
                    <strong>How do I track my request?</strong>
                    <p>Use the "Service Tracker" button in the header. Enter your reference code to view the status of your request.</p>
                </div>
                
                <div class="faq-item">
                    <strong>What documents do I need?</strong>
                    <p>Required documents vary by service. Check the specific service page for the complete list of requirements.</p>
                </div>
                
                <div class="faq-item">
                    <strong>How long does processing take?</strong>
                    <p>Processing times vary depending on the service. Most requests are processed within 3-5 business days.</p>
                </div>
                
                <h4>Contact Support</h4>
                <p>For additional assistance, please contact:</p>
                <p><strong>Phone:</strong> (075) 551-2249</p>
                <p><strong>Email:</strong> cio_cityofalaminos@yahoo.com.ph</p>
                <p><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
            `
        },
        terms: {
            title: "Terms of Service",
            content: `
                <h3>Terms of Service</h3>
                <p><em>Last updated: November 2025</em></p>
                
                <h4>1. Acceptance of Terms</h4>
                <p>By accessing and using Click2Serve, you accept and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.</p>
                
                <h4>2. Description of Service</h4>
                <p>Click2Serve is a kiosk and online platform for municipal service requests in Alaminos City. The platform allows citizens to request various government services, track their applications, and receive updates.</p>
                
                <h4>3. User Responsibilities</h4>
                <ul>
                    <li>Provide accurate and truthful information</li>
                    <li>Maintain the confidentiality of your reference codes</li>
                    <li>Use the service only for lawful purposes</li>
                    <li>Not attempt to interfere with the service's operations</li>
                </ul>
                
                <h4>4. Service Availability</h4>
                <p>We strive to maintain 24/7 availability but do not guarantee uninterrupted access. Scheduled maintenance and unforeseen issues may temporarily affect service availability.</p>
                
                <h4>5. Limitation of Liability</h4>
                <p>Click2Serve and the City Government of Alaminos shall not be liable for any indirect, incidental, or consequential damages arising from the use of this platform.</p>
                
                <h4>6. Changes to Terms</h4>
                <p>We reserve the right to modify these terms at any time. Continued use of the service after changes constitutes acceptance of the new terms.</p>
            `
        },
        privacy: {
            title: "Privacy Policy",
            content: `
                <h3>Privacy Policy</h3>
                <p><em>Last updated: November 2025</em></p>
                
                <h4>1. Information We Collect</h4>
                <p>We collect information necessary to process your service requests:</p>
                <ul>
                    <li>Personal identification information (name, address, contact details)</li>
                    <li>Government-issued ID numbers</li>
                    <li>Documents required for specific services</li>
                    <li>Usage data and service history</li>
                </ul>
                
                <h4>2. How We Use Your Information</h4>
                <p>Your information is used to:</p>
                <ul>
                    <li>Process and fulfill your service requests</li>
                    <li>Communicate updates about your applications</li>
                    <li>Improve our services and user experience</li>
                    <li>Comply with legal requirements</li>
                </ul>
                
                <h4>3. Data Protection</h4>
                <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                
                <h4>4. Data Sharing</h4>
                <p>We do not sell or share your personal information with third parties except as required by law or necessary to provide government services.</p>
                
                <h4>5. Your Rights</h4>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal data</li>
                    <li>Request correction of inaccurate information</li>
                    <li>Request deletion of your data (subject to legal retention requirements)</li>
                </ul>
                
                <h4>6. Contact Us</h4>
                <p>For privacy-related concerns, contact us at cio_cityofalaminos@yahoo.com.ph</p>
            `
        },
        status: {
            title: "System Status",
            content: `
                <h3>System Status</h3>
                
                <div class="status-item status-operational">
                    <span class="status-indicator"></span>
                    <strong>All Systems Operational</strong>
                </div>
                
                <h4>Service Status</h4>
                
                <div class="status-list">
                    <div class="status-row">
                        <span>Online Service Portal</span>
                        <span class="status-badge operational">Operational</span>
                    </div>
                    <div class="status-row">
                        <span>Kiosk System</span>
                        <span class="status-badge operational">Operational</span>
                    </div>
                    <div class="status-row">
                        <span>Service Tracker</span>
                        <span class="status-badge operational">Operational</span>
                    </div>
                    <div class="status-row">
                        <span>Document Processing</span>
                        <span class="status-badge operational">Operational</span>
                    </div>
                    <div class="status-row">
                        <span>SMS Notifications</span>
                        <span class="status-badge operational">Operational</span>
                    </div>
                </div>
                
                <h4>Recent Updates</h4>
                <p><strong>November 2025:</strong> System maintenance completed. All services running normally.</p>
                
                <h4>Scheduled Maintenance</h4>
                <p>No scheduled maintenance at this time.</p>
                
                <p class="status-note">Status is updated regularly. For real-time support, contact (075) 551-2249.</p>
            `
        },
        contact: {
            title: "Contact Us",
            content: `
                <h3>Get in Touch</h3>
                <p>We're here to help with any questions or concerns about Click2Serve and municipal services.</p>
                
                <h4>City Hall Address</h4>
                <p>Quezon Avenue, Poblacion<br>Alaminos City, 2404<br>Pangasinan, Philippines</p>
                
                <h4>Contact Numbers</h4>
                <p><strong>City Information Office:</strong> (075) 551-2249</p>
                <p><strong>City Administrator Office:</strong> (075) 551-2146</p>
                <p><strong>24/7 Action Center:</strong> Available for emergencies</p>
                
                <h4>Email</h4>
                <p>cio_cityofalaminos@yahoo.com.ph</p>
                
                <h4>Office Hours</h4>
                <p>Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday - Sunday: Closed (except for emergencies)</p>
                
                <h4>Social Media</h4>
                <p>Follow us on Facebook: <strong>Alaminos City para sa Lahat</strong></p>
            `
        }
    };

    function openFooterModal(modalId) {
        const data = footerModalData[modalId];
        if (!data) return;

        document.getElementById('footerModalTitle').textContent = data.title;
        document.getElementById('footerModalBody').innerHTML = data.content;
        document.getElementById('footerModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeFooterModal() {
        document.getElementById('footerModal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    // Close modal on outside click
    document.getElementById('footerModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeFooterModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('footerModal')?.classList.contains('show')) {
            closeFooterModal();
        }
    });

    // Website Feedback Form Handler
    document.getElementById('websiteFeedbackForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const form = this;
        const submitBtn = form.querySelector('.feedback-submit-btn');
        const messageDisplay = document.getElementById('feedbackMessageDisplay');
        const originalBtnText = submitBtn.innerHTML;

        // Get form data
        const formData = new FormData(form);
        const rating = formData.get('rating');
        const messageInput = document.getElementById('feedbackMessageInput');
        const message = messageInput ? messageInput.value.trim() : null;

        if (!rating) {
            showFeedbackMessage('Please select a rating', 'error');
            return;
        }

        // Disable button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!csrfToken) {
            showFeedbackMessage('Security token not found. Please refresh the page.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            return;
        }

        try {
            const response = await fetch('/api/feedback/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Request-Source': 'website',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    tracking_number: null, // Website feedback doesn't require tracking number
                    rating: parseInt(rating),
                    message: message || null
                })
            });

            const result = await response.json();

            if (result.success) {
                showFeedbackMessage('Thank you for your feedback!', 'success');
                form.reset();
                // Reset star labels
                document.querySelectorAll('.star-label').forEach(label => {
                    label.classList.remove('selected', 'hover');
                });
                // Uncheck all radio buttons
                document.querySelectorAll('.star-rating-input input[type="radio"]').forEach(radio => {
                    radio.checked = false;
                });
            } else {
                showFeedbackMessage(result.message || 'Failed to submit feedback. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Feedback submission error:', error);
            showFeedbackMessage('Error submitting feedback. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });

    // Star rating interaction - Fixed logic
    const starContainer = document.getElementById('starRatingContainer');
    if (starContainer) {
        const starLabels = starContainer.querySelectorAll('.star-label');
        const starInputs = starContainer.querySelectorAll('input[type="radio"]');

        // Handle radio button change
        starInputs.forEach((input, index) => {
            input.addEventListener('change', function () {
                const selectedRating = parseInt(this.value);
                updateStarDisplay(selectedRating);
            });
        });

        // Handle label click (also triggers radio)
        starLabels.forEach((label, index) => {
            label.addEventListener('click', function (e) {
                const rating = parseInt(this.getAttribute('data-rating'));
                const radio = document.getElementById('star' + rating);
                if (radio) {
                    radio.checked = true;
                    updateStarDisplay(rating);
                }
            });

            // Hover effect
            label.addEventListener('mouseenter', function () {
                const hoverRating = parseInt(this.getAttribute('data-rating'));
                starLabels.forEach((l, i) => {
                    if (i < hoverRating) {
                        l.classList.add('hover');
                    } else {
                        l.classList.remove('hover');
                    }
                });
            });
        });

        // Remove hover on mouse leave
        starContainer.addEventListener('mouseleave', function () {
            starLabels.forEach(label => {
                label.classList.remove('hover');
            });
            // Restore selected state
            const checkedInput = starContainer.querySelector('input[type="radio"]:checked');
            if (checkedInput) {
                updateStarDisplay(parseInt(checkedInput.value));
            }
        });
    }

    function updateStarDisplay(rating) {
        const starLabels = document.querySelectorAll('.star-label');
        starLabels.forEach((label, index) => {
            const labelRating = parseInt(label.getAttribute('data-rating'));
            if (labelRating <= rating) {
                label.classList.add('selected');
                label.classList.remove('hover');
            } else {
                label.classList.remove('selected', 'hover');
            }
        });
    }

    function showFeedbackMessage(message, type) {
        const messageDisplay = document.getElementById('feedbackMessageDisplay');
        if (!messageDisplay) return;

        messageDisplay.textContent = message;
        messageDisplay.className = 'feedback-message-display ' + type;
        messageDisplay.style.display = 'block';

        setTimeout(() => {
            messageDisplay.style.display = 'none';
        }, 5000);
    }
</script>

<style>
    .feedback-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-top: 12px;
    }

    .feedback-rating {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .star-rating-input {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        gap: 6px;
        align-items: center;
        padding: 8px 0;
    }

    .star-rating-input input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .star-label {
        font-size: 1.75rem;
        color: #d1d5db;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
        line-height: 1;
        display: inline-block;
        position: relative;
    }

    .star-label:hover,
    .star-label.hover {
        color: #F3BE7C;
        transform: scale(1.15);
    }

    .star-label.selected {
        background: linear-gradient(135deg, #F3BE7C 0%, #E8A85A 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transform: scale(1.1);
    }

    .rating-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: rgb(251, 251, 251);
        margin-bottom: 4px;
    }

    .feedback-message {
        width: 100%;
    }

    .feedback-message textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        font-family: inherit;
        resize: vertical;
        min-height: 90px;
        transition: all 0.2s ease;
        box-sizing: border-box;
        background: #ffffff;
    }

    .feedback-message textarea:focus {
        outline: none;
        border-color: #008080;
        box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.1);
        background: #ffffff;
    }

    .feedback-message textarea::placeholder {
        color: #9ca3af;
    }

    .feedback-submit-btn {
        padding: 12px 20px;
        background: linear-gradient(135deg, #008080 0%, #00A0A0 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        box-shadow: 0 2px 8px rgba(0, 128, 128, 0.2);
    }

    .feedback-submit-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #00A0A0 0%, #008080 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 128, 128, 0.3);
    }

    .feedback-submit-btn:active:not(:disabled) {
        transform: translateY(0);
    }

    .feedback-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .feedback-message-display {
        padding: 10px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        margin-top: 8px;
    }

    .feedback-message-display.success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }

    .feedback-message-display.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    @media (max-width: 768px) {
        .feedback-form {
            gap: 10px;
        }

        .star-label {
            font-size: 1.25rem;
        }

        .feedback-message textarea {
            min-height: 70px;
        }
    }
</style>