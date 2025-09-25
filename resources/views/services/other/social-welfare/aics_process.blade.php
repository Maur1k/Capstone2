<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistance to Individual in Crisis Situation (AICS) - Process</title>
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
                    <h1 class="page-title">Assistance to Individual in Crisis Situation (AICS)</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">CHECKLIST OF
                                    REQUIREMENTS</h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:700px;">
                                    <li><strong>General Requirements:</strong>
                                        <ul style="margin-left:20px; margin-top:5px;">
                                            <li>Certificate of Residency/Indigency of client (1 photocopy, 1 original
                                                copy) - Barangay hall where client resides</li>
                                            <li>Any valid identification card of the client/person to be interviewed (2
                                                photocopy)</li>
                                        </ul>
                                    </li>
                                    <li><strong>For Medical Assistance:</strong>
                                        <ul style="margin-left:20px; margin-top:5px;">
                                            <li>Medical Certificate or Medical Abstract (issued within 3 months) (2
                                                photocopy) - Hospital/clinic</li>
                                            <li>Prescription (Reseta) (2 photocopy) - Hospital/clinic</li>
                                            <li>Statement of Account for hospital bill (2 photocopy) - Hospital</li>
                                            <li>Laboratory request (for laboratory request) (2 photocopy) -
                                                Doctor/Physician</li>
                                        </ul>
                                    </li>
                                    <li><strong>For Burial Assistance:</strong>
                                        <ul style="margin-left:20px; margin-top:5px;">
                                            <li>Death Certificate (2 photocopy) - LCR (Local Civil Registry)</li>
                                            <li>Funeral Contract of Service (2 photocopy) - Servicing Funeral Parlor
                                            </li>
                                        </ul>
                                    </li>
                                    <li><strong>For Educational Assistance:</strong>
                                        <ul style="margin-left:20px; margin-top:5px;">
                                            <li>Certificate of Enrolment or registration or assessment form (2
                                                photocopy) - School</li>
                                            <li>Current school year Identification Card - School</li>
                                            <li>Statement of Account (for College Students) (2 photocopy)</li>
                                        </ul>
                                    </li>
                                    <li><strong>For Transportation Assistance:</strong>
                                        <ul style="margin-left:20px; margin-top:5px;">
                                            <li>Police Blotter/Report (2 photocopy) - PNP/POSO</li>
                                        </ul>
                                    </li>
                                    <li><strong>For Food Assistance:</strong>
                                        <ul style="margin-left:20px; margin-top:5px;">
                                            <li>Project Proposal (for residents/clients in CRCGs)</li>
                                            <li>Food distribution list (for residents/clients in CRCGs)</li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESS STEPS</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>Step 1:</strong> Submit requirements and sign in logbook (2 minutes)</p>
                                    <p><strong>Step 2:</strong> Interview and gather data for AICS form (10 minutes)</p>
                                    <p><strong>Step 3:</strong> Review and recommendation by CSWD Officer (5 minutes)
                                    </p>
                                    <p><strong>Step 4:</strong> Endorsement to City Mayor's Office for final approval
                                    </p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">FEES TO BE PAID
                                </h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>No fees required</strong></p>
                                    <p>This is a free service provided by the City Social Welfare and Development Office
                                    </p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESSING TIME
                                </h3>
                                <p style="color:#375e73; margin:0;">Total Processing Time: 17 minutes</p>
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
        function goBack() { window.location.href = '/services/other/social-welfare'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        function goNext() { window.location.href = '/services/other/social-welfare/aics/check'; }
    </script>
</body>

</html>

