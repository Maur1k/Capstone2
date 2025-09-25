<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delayed COM - Requirements Check</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('css/civil.css') }}">
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
                    <h1 class="page-title">Delayed Certificate of Marriage (COM) Process</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 820px; text-align:center;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:18px;">Do you have all of
                                    the requirement?</h3>
                                <div style="display:flex; gap:36px; justify-content:center;">
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
        function goBack() { window.location.href = '/services/civil/com/delayed/process'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        function goYes() { window.location.href = '/services/civil/com/delayed'; }
        function goNo() { window.location.href = '/services/civil/com/delayed/process'; }
    </script>
</body>

</html>
