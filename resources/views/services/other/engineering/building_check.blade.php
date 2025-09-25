<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Building Permit - Check Requirements</title>
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
                    <h1 class="page-title">Building Permit - Check Requirements</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 800px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title"
                                    style="font-size:24px; margin-bottom:20px; text-align:center;">Do you have all the
                                    required documents?</h3>

                                <div style="text-align:left; color:#375e73; line-height:1.8; margin:20px 0;">
                                    <p style="font-weight:600; margin-bottom:15px;">Please make sure you have:</p>
                                    <ul style="margin-left:20px;">
                                        <li>Approved Certificate of Construction Occupational Safety and Health Program
                                            from DOLE</li>
                                        <li>Locational Clearance (4 original copies)</li>
                                        <li>Notarized Unified application form for building permit</li>
                                        <li>Proof of lot ownership documents</li>
                                        <li>Current Real Property Tax Receipt</li>
                                        <li>Complete plans prepared, signed, and sealed by licensed professionals (4
                                            sets)</li>
                                        <li>Notarized Estimated Cost of the proposed work</li>
                                        <li>Specifications (4 original copies)</li>
                                        <li>Structural Design Analysis (for 2+ storey structures)</li>
                                        <li>Geotechnical Report (for 3+ storey structures)</li>
                                        <li>Fire Suppression System Plan (for 4+ storey structures)</li>
                                        <li>Fire Safety Evaluation Clearance</li>
                                        <li>Assessment of Building Permit Fees</li>
                                        <li>Receipt of Building Permit Fees and Signboard</li>
                                        <li>Logbook signed and sealed by full-time inspector</li>
                                    </ul>
                                </div>

                                <div style="display:flex; gap:36px; justify-content:center; margin-top:30px;">
                                    <div class="service-tile" tabindex="0" onclick="goYes()" style="width:240px;">
                                        <div class="service-icon">
                                            <img src="{{ asset('assets/icons/checkblue.png') }}" alt="Yes" />
                                        </div>
                                        <h3 class="service-title">Yes</h3>
                                    </div>
                                    <div class="service-tile" tabindex="0" onclick="goNo()" style="width:240px;">
                                        <div class="service-icon">
                                            <img src="{{ asset('assets/icons/xred.png') }}" alt="No" />
                                        </div>
                                        <h3 class="service-title">No</h3>
                                    </div>
                                </div>
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
        function goBack() { window.location.href = '/services/other/engineering/building/process'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        function goYes() { window.location.href = '/services/other/engineering/building/request'; }
        function goNo() { window.location.href = '/services/other/engineering/building/process'; }
    </script>
</body>

</html>