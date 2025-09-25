<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Registration - Process</title>
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
                    <h1 class="page-title">Pet Registration</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">SERVICE
                                    DESCRIPTION</h3>
                                <p style="color:#375e73; margin:0; text-align:left;">
                                    To register all pet animals as database for planning, budgeting and regulation in
                                    accordance with provision of IRR of the Alaminos City Veterinary Code.
                                </p>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">REQUIREMENTS</h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:700px;">
                                    <li>Bring your pet to the City Veterinary Office</li>
                                    <li>Valid ID of pet owner</li>
                                    <li>Pet must be physically present for registration</li>
                                    <li>Complete pet information (name, breed, age, color, etc.)</li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESS STEPS</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>Step 1:</strong> Bring pet to office and inquire about requirements (5
                                        minutes)</p>
                                    <p><strong>Step 2:</strong> Fill up Pre-Registration Form (5 minutes)</p>
                                    <p><strong>Step 3:</strong> Encode information into PetRis System and print form (5
                                        minutes)</p>
                                    <p><strong>Step 4:</strong> Pay registration and ICT fees (3 minutes)</p>
                                    <p><strong>Step 5:</strong> Sign logbook and receive Pet Registration Form (1
                                        minute)</p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">FEES TO BE PAID
                                </h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p>• Registration Fee: Php 75.00</p>
                                    <p>• ICT Fee: Php 25.00</p>
                                    <p><strong>Total: Php 100.00</strong></p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESSING TIME
                                </h3>
                                <p style="color:#375e73; margin:0;">Total Processing Time: 12 minutes</p>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">ELIGIBILITY</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p>• Pet owners of Alaminos City and other nearby towns</p>
                                    <p>• All types of pet animals</p>
                                    <p>• Pet must be physically present for registration</p>
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
        function goBack() { window.location.href = '/services/other/veterinary'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        function goNext() { window.location.href = '/services/other/veterinary/pet-registration/check'; }
    </script>
</body>

</html>

