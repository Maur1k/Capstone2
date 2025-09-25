<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Development Services - Process</title>
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
                    <h1 class="page-title">Child Development Services</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">CHECKLIST OF
                                    REQUIREMENTS</h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:700px;">
                                    <li>Birth Certificate (1 photocopy) - LCR (Local Civil Registry)</li>
                                    <li>Health Card from CHO (1 photocopy) - LCR (Local Civil Registry)</li>
                                    <li>Registration Form - CSWDO (City Social Welfare and Development Office)</li>
                                    <li>Parent's Consent Form - CSWDO</li>
                                    <li>CDC Intake Sheet - CSWDO</li>
                                    <li>Parents Involvement Form - CSWDO</li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESS STEPS</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>Step 1:</strong> Registration of children with required documents (5
                                        minutes)</p>
                                    <p><strong>Step 2:</strong> Attend parents' orientation (2 hours)</p>
                                    <p><strong>Step 3:</strong> Attend child development sessions (2.5 hours per
                                        session)</p>
                                    <p><strong>Step 4:</strong> Monthly sessions for 10 months (June to March)</p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">FEES TO BE PAID
                                </h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p>• Registration Fee: Php 100.00</p>
                                    <p>• Monthly Fee: Php 50.00 (for 10 months)</p>
                                    <p><strong>Total: Php 600.00</strong></p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESSING TIME
                                </h3>
                                <p style="color:#375e73; margin:0;">Total Processing Time: 4 hours, 35 minutes (initial
                                    setup)</p>
                                <p style="color:#375e73; margin:0;">Program Duration: 10 months (June to March)</p>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">ELIGIBILITY</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p>• Children ages 0-4 years old</p>
                                    <p>• All citizens of Alaminos City</p>
                                    <p>• Parents must attend orientation</p>
                                    <p>• Regular attendance in development sessions</p>
                                </div>
                            </div>

                            <div id="steps"></div>
                            <div style="text-align:center; margin-top:16px;">
                                <button class="primary-btn" onclick="goNext()">Next</button>
                            </div>
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
        function goBack() { window.location.href = '/services/other/social-welfare'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        function goNext() { window.location.href = '/services/other/social-welfare/child-dev/check'; }
    </script>
</body>

</html>

