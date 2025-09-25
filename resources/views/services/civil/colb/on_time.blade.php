<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On-time COLB - Step 1 - Click2Serve</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('css/civil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
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
                    <h1 class="page-title">On-time Certificate of Live Birth (COLB) - Step 1</h1>
                </div>

                <div class="main-content">
                    <div class="form-panel">
                        <h2 class="form-title">Basic Request Information</h2>
                        <p
                            style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 16px;">
                            Please provide basic information for the Certificate of Live Birth request
                        </p>

                        <form class="kiosk-form" id="colbStep1Form">
                            <div class="section">
                                <h3 class="section-title">Personal Information</h3>
                                <div class="grid-3">
                                    <div class="form-field">
                                        <label for="first_name">First Name</label>
                                        <input type="text" id="first_name" name="first_name"
                                            placeholder="Enter first name" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="middle_name">Middle Name</label>
                                        <input type="text" id="middle_name" name="middle_name"
                                            placeholder="Enter middle name">
                                    </div>
                                    <div class="form-field">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" id="last_name" name="last_name" placeholder="Enter last name"
                                            required>
                                    </div>
                                    <div class="form-field">
                                        <label for="birth_date">Date of Birth</label>
                                        <input type="date" id="birth_date" name="birth_date" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="place_of_birth">Place of Birth</label>
                                        <input type="text" id="place_of_birth" name="place_of_birth" list="pobList"
                                            placeholder="Hospital/Clinic/Address" required>
                                        <datalist id="pobList">
                                            <option value="Alaminos City RHU"></option>
                                            <option value="Western Pangasinan District Hospital"></option>
                                            <option value="Clinic"></option>
                                            <option value="Home"></option>
                                            <option value="Other"></option>
                                        </datalist>
                                    </div>
                                    <div class="form-field">
                                        <label for="contact_number">Contact Number</label>
                                        <input type="tel" id="contact_number" name="contact_number"
                                            placeholder="09xx xxx xxxx" required>
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <h3 class="section-title">Request Details</h3>
                                <div class="grid-2">
                                    <div class="form-field">
                                        <label for="purpose">Purpose of Request</label>
                                        <select id="purpose" name="purpose" required>
                                            <option value="" disabled selected>Select purpose</option>
                                            <option value="Personal Use">Personal Use</option>
                                            <option value="Employment">Employment</option>
                                            <option value="Education">Education</option>
                                            <option value="Government Transaction">Government Transaction</option>
                                            <option value="Legal Proceedings">Legal Proceedings</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-field">
                                    <label for="additional_info">Additional Information</label>
                                    <textarea id="additional_info" name="additional_info"
                                        placeholder="Any additional details or special requests..." rows="3"></textarea>
                                </div>
                            </div>

                            <div class="section">
                                <h3 class="section-title">Contact Information</h3>
                                <div class="grid-2">
                                    <div class="form-field">
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" name="email"
                                            placeholder="your.email@example.com">
                                    </div>
                                    <div class="form-field">
                                        <label for="address">Complete Address</label>
                                        <input type="text" id="address" name="address" list="addressList"
                                            placeholder="Street, Barangay, City" required>
                                        <datalist id="addressList">
                                            <option value="Poblacion, Alaminos City"></option>
                                            <option value="Lucap, Alaminos City"></option>
                                            <option value="San Jose, Alaminos City"></option>
                                            <option value="San Vicente, Alaminos City"></option>
                                            <option value="Other"></option>
                                        </datalist>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="secondary-btn" onclick="goBack()">Cancel</button>
                                <button type="submit" class="primary-btn">Next</button>
                            </div>
                        </form>
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
            window.location.href = '/services/civil';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        // Form submission - proceed to step 2
        document.getElementById('colbStep1Form').addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate required fields
            const requiredFields = this.querySelectorAll('input[required], select[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (field.value.trim() === '') {
                    field.parentElement.classList.add('error');
                    isValid = false;
                } else {
                    field.parentElement.classList.remove('error');
                    field.parentElement.classList.add('success');
                }
            });

            if (!isValid) {
                alert('Please fill in all required fields.');
                return;
            }

            // Store form data in sessionStorage for step 2
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            sessionStorage.setItem('colbStep1Data', JSON.stringify(data));

            // Proceed to step 2
            window.location.href = '/services/civil/colb/on-time/step2';
        });

        // Form validation enhancements
        document.querySelectorAll('input[required], select[required]').forEach(field => {
            field.addEventListener('blur', function () {
                if (this.value.trim() === '') {
                    this.parentElement.classList.add('error');
                } else {
                    this.parentElement.classList.remove('error');
                    this.parentElement.classList.add('success');
                }
            });
        });

        // Phone number formatting
        document.getElementById('contact_number').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0 && value.length <= 11) {
                if (value.length <= 4) {
                    value = value;
                } else if (value.length <= 7) {
                    value = value.slice(0, 4) + ' ' + value.slice(4);
                } else {
                    value = value.slice(0, 4) + ' ' + value.slice(4, 7) + ' ' + value.slice(7);
                }
            }
            e.target.value = value;
        });

        // Auto-focus first field for kiosk use
        document.addEventListener('DOMContentLoaded', function () {
            const firstInput = document.querySelector('input[required]');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 500);
            }
        });
    </script>
</body>

</html>
