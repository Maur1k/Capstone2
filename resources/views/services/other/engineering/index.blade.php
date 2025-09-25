<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engineering Services - Click2Serve</title>
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
                    <h1 class="page-title">Engineering Services</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel">
                            <div class="services-grid civil-grid" style="grid-template-columns: repeat(2, 1fr);">
                                <div class="service-tile" tabindex="0"
                                    onclick="window.location.href='/services/other/engineering/locational/process'">
                                    <div class="service-icon">
                                        <img src="{{ asset('assets/icons/locational.png') }}"
                                            alt="Securing Locational Clearance for Building Permit" />
                                    </div>
                                    <h3 class="service-title">Securing Locational Clearance<br />for Building Permit
                                    </h3>
                                </div>

                                <div class="service-tile" tabindex="0"
                                    onclick="window.location.href='/services/other/engineering/building/process'">
                                    <div class="service-icon">
                                        <img src="{{ asset('assets/icons/building.png') }}"
                                            alt="Securing a Building Permit & Other Building Related Permits" />
                                    </div>
                                    <h3 class="service-title">Securing a Building Permit &<br />Other Building Related
                                        Permits</h3>
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
        function goBack() { window.location.href = '/services'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
    </script>
</body>

</html>