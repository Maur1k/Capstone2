<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportation Services - Click2Serve</title>
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
                    <h1 class="page-title">Transportation Services</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel">
                            <div class="services-grid civil-grid" style="grid-template-columns: repeat(2, 1fr);">
                                <div class="service-tile" tabindex="0"
                                    onclick="window.location.href='/services/other/ctru/mtop/process'">
                                    <div class="service-icon">
                                        <img src="{{ asset('assets/icons/tricycle.png') }}"
                                            alt="Motorized Tricycle Operator's Permit Service" />
                                    </div>
                                    <h3 class="service-title">Motorized Tricycle<br />Operator's Permit Service (MTOP)
                                    </h3>
                                </div>

                                <div class="service-tile" tabindex="0"
                                    onclick="window.location.href='/services/other/ctru/boat/process'">
                                    <div class="service-icon">
                                        <img src="{{ asset('assets/icons/boat.png') }}"
                                            alt="Mayor's Permit for Passenger Boat Service" />
                                    </div>
                                    <h3 class="service-title">Mayor's Permit for<br />Passenger Boat Service</h3>
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