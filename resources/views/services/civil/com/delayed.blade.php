<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delayed COM - Step 1 - Click2Serve</title>
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
                    <h1 class="page-title">Delayed Certificate of Marriage (COM) - Step 1</h1>

                </div>

                <div class="main-content">
                    <div class="form-panel">
                        <h2 class="form-title">Basic Request Information</h2>
                        <p
                            style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 16px;">
                            Please provide your basic information to request a DelayedCertificate of Marriage
                        </p>
                        <form class="kiosk-form" id="comDelayedStep1Form">
                            <div class="section">
                                <h3 class="section-title">Applicant Information</h3>
                                <div class="grid-3">
                                    <div class="form-field">
                                        <label for="first_name">First Name</label>
                                        <input type="text" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="middle_name">Middle Name</label>
                                        <input type="text" id="middle_name" name="middle_name">
                                    </div>
                                    <div class="form-field">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <h3 class="section-title">Contact & Purpose</h3>
                                <div class="grid-2">
                                    <div class="form-field">
                                        <label for="contact_number">Contact Number</label>
                                        <input type="tel" id="contact_number" name="contact_number" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="address">Complete Address</label>
                                        <input type="text" id="address" name="address" required>
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="form-field">
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" name="email">
                                    </div>
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
        function goBack() { window.location.href = '/services/civil/com/delayed/process'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        document.getElementById('comDelayedStep1Form').addEventListener('submit', function (e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(this));
            sessionStorage.setItem('comDelayedStep1Data', JSON.stringify(data));
            window.location.href = '/services/civil/com/delayed/step2';
        });
    </script>
</body>

</html>