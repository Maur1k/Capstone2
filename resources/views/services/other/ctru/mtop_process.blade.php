<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorized Tricycle Operator's Permit Service - Process</title>
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
                    <h1 class="page-title">Motorized Tricycle Operator's Permit Service (MTOP)</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">CHECKLIST OF
                                    REQUIREMENTS</h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:700px;">
                                    <li>Community Tax Certificate (CTC) - Original or photocopy (Barangay of residence /
                                        City Treasurer's Office)</li>
                                    <li>Barangay Clearance/Certification - Original or photocopy (Barangay of residence)
                                    </li>
                                    <li>Official Receipt (OR) - Photocopy (Land Transportation Office)</li>
                                    <li>Certificate of Registration (CR) - Photocopy (Land Transportation Office)</li>
                                    <li>Tricycle Operators and Drivers Association (TODA) Certification - Original (TODA
                                        President)</li>
                                    <li>Professional Driver's License - Photocopy (Driver)</li>
                                    <li>Picture (2x2) - Provided by Client</li>
                                    <li>Stencil of Motor and Chassis - Obtained from Tricycle for application</li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESS STEPS</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>Step 1:</strong> Fill up PMTSRF and submit initial requirements (2-5
                                        minutes)</p>
                                    <p><strong>Step 2:</strong> Payment at City Treasurer's Office (3 minutes)</p>
                                    <p><strong>Step 3:</strong> CTRU processing and document preparation (1-2 minutes)
                                    </p>
                                    <p><strong>Step 4:</strong> CTRU Head and City Administrator approval (2 minutes)
                                    </p>
                                    <p><strong>Step 5:</strong> Release of MTOP and sticker (2 minutes)</p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">FEES TO BE PAID
                                </h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>For New Applications:</strong></p>
                                    <p>• ICT Fee: Php 25.00</p>
                                    <p>• MTOP Fee: Php 100.00</p>
                                    <p>• Registration/Filing Fee: Php 150.00</p>
                                    <p>• Franchise Fee: Php 200.00</p>
                                    <p>• Sticker Fee: Php 65.00</p>
                                    <p>• ID: Php 50.00</p>
                                    <p><strong>Total for New: Php 590.00</strong></p>
                                    <br>
                                    <p><strong>For 1 Year Renewal:</strong></p>
                                    <p>• ICT Fee: Php 25.00</p>
                                    <p>• MTOP Fee: Php 100.00</p>
                                    <p>• Registration/Filing Fee: Php 150.00</p>
                                    <p>• Franchise Fee: Php 200.00</p>
                                    <p>• Sticker Fee: Php 65.00</p>
                                    <p>• Account Clearance: Php 50.00</p>
                                    <p>• POSO Clearance: Php 50.00</p>
                                    <p><strong>Total for Renewal: Php 640.00</strong></p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESSING TIME
                                </h3>
                                <p style="color:#375e73; margin:0;">Renewal: 20 Minutes | New Application: 25 Minutes
                                </p>
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
        function goBack() { window.location.href = '/services/other/ctru'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        function goNext() { window.location.href = '/services/other/ctru/mtop/check'; }
    </script>
</body>

</html>