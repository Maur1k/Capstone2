<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renewal of Business Permit - Process</title>
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
                    <h1 class="page-title">Renewal of Business Permit</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">CHECKLIST OF
                                    REQUIREMENTS</h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:700px;">
                                    <li>Real Property Tax Clearance - City Treasurer Office</li>
                                    <li>Barangay Clearance (1 Photocopy) - Where business establishment is located (if
                                        not integrated in iBPLS)</li>
                                    <li>Owner and Representative enrolled to Personal Information System (PIS) -
                                        Management Information System (MIS)</li>
                                    <li>Contract of Lease (Photocopy) - Property Owner (if expired)</li>
                                    <li>Certification from Alaminos City Tourism & Cultural Affairs Office / Inspection
                                        and Recommendation slip from City Market & Cemetery Section Office</li>
                                    <li>License from National Government Agencies (Photocopy) - BSP, FDA, DENR, DFA,
                                        PCSO, DOLE, DOE, DOT, HLURB/DHSUD, and other concerned agencies (if needed) (if
                                        there are changes to be made)</li>
                                    <li>Certificate of Registration for Sole Proprietorship, Partnership / Corporation
                                        (Photocopy) - DTI, SEC (if there are changes to be made)</li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESS STEPS</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>Step 1:</strong> Proceed to BOSS - Submit pre-requirements (10 minutes)
                                    </p>
                                    <p><strong>Step 2:</strong> Assessment and payment at CTO (30 minutes)</p>
                                    <p><strong>Step 3:</strong> Secure Mayor's Permit and sticker (20 minutes)</p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">FEES TO BE PAID
                                </h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p>• Account Clearance: Php 50.00</p>
                                    <p>• Building Clearance: Php 20.00</p>
                                    <p>• Sanitary Permit: Php 100.00</p>
                                    <p>• Sanitary Inspection: based on area</p>
                                    <p>• Documentary stamp: Php 60.00</p>
                                    <p>• ICT Fee: Php 25.00</p>
                                    <p><strong>Total Estimated Fees:</strong> Php 225.00 + Mayor's Permit Fee and
                                        Business Tax + 15% FSIC + Sanitary Inspection fee</p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESSING TIME
                                </h3>
                                <p style="color:#375e73; margin:0;">Overall Processing Time: 60 Minutes</p>
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
        function goBack() { window.location.href = '/services/other/bplo'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        function goNext() { window.location.href = '/services/other/bplo/renewal/check'; }
    </script>
</body>

</html>