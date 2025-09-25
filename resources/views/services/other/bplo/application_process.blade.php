<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Business Permit - Process</title>
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
                    <h1 class="page-title">Application for New Business Permit</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">CHECKLIST OF
                                    REQUIREMENTS</h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:700px;">
                                    <li>Community Tax Certificate (CTC) - Original (Barangay Hall or City Treasurer)
                                    </li>
                                    <li>Barangay Clearance (1 Photocopy) - Where business establishment is located (if
                                        not integrated in iBPLS)</li>
                                    <li>Owner and Representative enrolled to Personal Information System (PIS) -
                                        Management Information System (MIS)</li>
                                    <li>Contract of Lease / Mayor's Permit of Lessor / Tax Declaration / Certification
                                        from Tourism & Cultural Affairs Office / Market & Cemetery Office</li>
                                    <li>Inspection and Recommendation slip from City Market & Cemetery Section Office
                                    </li>
                                    <li>Special Power of Attorney (SPA) or Authorization letter from business owner (if
                                        applicable)</li>
                                    <li>License from National Government Agencies - BSP, FDA, DENR, DFA, PCSO, DOLE,
                                        DOE, DOT, HLURB/DHSUD (if needed)</li>
                                    <li>Certificate of Registration for Sole Proprietorship, Partnership / Corporation -
                                        DTI, SEC</li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESS STEPS</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>Step 1:</strong> Proceed to BOSS - Submit pre-requirements and
                                        application form (25 minutes)</p>
                                    <p><strong>Step 2:</strong> Payment of Mayor's Permit and other regulatory fees (10
                                        minutes)</p>
                                    <p><strong>Step 3:</strong> Secure Mayor's Permit and Business Plate (25 minutes)
                                    </p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">FEES TO BE PAID
                                </h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p>• Business Plate: Php 200.00</p>
                                    <p>• Business Inspection fee: depends on type/nature of business</p>
                                    <p>• Zoning Clearance: Php 20.00</p>
                                    <p>• Real Property Tax Clearance: Php 50.00</p>
                                    <p>• Sanitary Permit: Php 100.00</p>
                                    <p>• Sanitary Inspection: based on area</p>
                                    <p>• Documentary stamp: Php 90.00</p>
                                    <p>• ICT Fee: Php 25.00</p>
                                    <p><strong>Total Estimated Fees:</strong> Php 485.00 + Mayor's Permit Fee (depends
                                        on capitalization) + 15% FSIC + Sanitary Inspection fee</p>
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
        function goNext() { window.location.href = '/services/other/bplo/application/check'; }
    </script>
</body>

</html>