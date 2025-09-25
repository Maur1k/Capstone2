<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anti-Rabies Vaccination for Walk-in Clients - Process</title>
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
                    <h1 class="page-title">Anti-Rabies Vaccination for Walk-in Clients</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">SERVICE
                                    DESCRIPTION</h3>
                                <p style="color:#375e73; margin:0; text-align:left;">
                                    Anti-rabies vaccination for dogs and cats aged three months and above, in compliance
                                    with the National Rabies Act of 2007 and the Veterinary Code of Alaminos City.
                                </p>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">REQUIREMENTS</h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:700px;">
                                    <li>Bring your pet to the City Veterinary Office</li>
                                    <li>Valid ID of pet owner</li>
                                    <li>Pet must be at least 3 months old</li>
                                    <li>Pet must be physically present for vaccination</li>
                                    <li>Pet must be healthy and not showing signs of illness</li>
                                    <li>Previous vaccination records (if any)</li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESS STEPS</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>Step 1:</strong> Bring pet and inquire about requirements (2 minutes)</p>
                                    <p><strong>Step 2:</strong> Check Personal Information System (PIS) enrollment (3
                                        minutes)</p>
                                    <p><strong>Step 3:</strong> Sign logbook and pay vaccination fees (5 minutes)</p>
                                    <p><strong>Step 4:</strong> Vaccine preparation and inoculation (3 minutes)</p>
                                    <p><strong>Step 5:</strong> Post-vaccination examination and registration (2
                                        minutes)</p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">FEES TO BE PAID
                                </h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>For Registered Pets:</strong></p>
                                    <p>• Anti-Rabies Fee: Php 300.00 per pet</p>
                                    <p><strong>For Not Registered Pets:</strong></p>
                                    <p>• Anti-Rabies Fee: Php 400.00 per pet</p>
                                    <p>• Registration Fee: Php 75.00</p>
                                    <p>• ICT Fee: Php 25.00</p>
                                    <p><strong>Total for Not Registered: Php 500.00 per pet</strong></p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESSING TIME
                                </h3>
                                <p style="color:#375e73; margin:0;">Total Processing Time: 15 minutes</p>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">ELIGIBILITY</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p>• Pet owners of Alaminos City and other nearby towns</p>
                                    <p>• Dogs and cats aged 3 months and above</p>
                                    <p>• Pet must be physically present for vaccination</p>
                                    <p>• Pet must be healthy and not showing signs of illness</p>
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
        function goNext() { window.location.href = '/services/other/veterinary/anti-rabies/check'; }
    </script>
</body>

</html>

