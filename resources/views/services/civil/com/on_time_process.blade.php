<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On-time COM Process - Click2Serve</title>
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
                    <h1 class="page-title">On-time Certificate of Marriage (COM) Process</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">PREPARE DOCUMENTS
                                </h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:640px;">
                                    <li>Municipal Form No. 97 (Certificate of Marriage) – 4 copies</li>
                                    <li>Civil Registry Attachment (for Muslims/IPs) – 4 copies, if applicable</li>
                                    <li>Valid Government-issued ID – 1 photocopy</li>
                                    <li>Affidavit of Cohabitation (if applicable) – 4 original copies</li>
                                    <li>Affidavit of Witnesses (under Article 34) – 4 original copies</li>
                                    <li>If no ID: Police Clearance / Voter’s Certification</li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">GO TO LOCAL CIVIL
                                    REGISTRY OFFICE</h3>
                                <p style="color:#375e73; margin:0;">Present valid ID and get queuing number and ARTA
                                    Monitoring Form.</p>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">INTERVIEW &
                                    ASSISTANCE</h3>
                                <p style="color:#375e73; margin:0;">Civil registry staff interviews and assists client.
                                </p>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PAYMENT OF
                                    APPLICABLE FEES</h3>
                                <p style="color:#375e73; margin:0;">Proceed to the City Treasurer's Office as required.
                                </p>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">SUBMIT REQUIREMENTS
                                </h3>
                                <p style="color:#375e73; margin:0;">Submit documents along with queuing number.</p>
                            </div>

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
        function goBack() { window.location.href = '/services/civil'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        function goNext() { window.location.href = '/services/civil/com/on-time/check'; }
    </script>
</body>

</html>
