<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locational Clearance for Building Permit - Process</title>
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
                    <h1 class="page-title">Locational Clearance for Building Permit</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">CHECKLIST OF
                                    REQUIREMENTS</h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:700px;">
                                    <li>Duly accomplished and notarized Unified Application Form for Building Permit (1
                                        copy) - BOSS Office (CEO 2)</li>
                                    <li><strong>Proof of Lot Ownership/Occupancy:</strong>
                                        <ul style="margin-left:20px; margin-top:5px;">
                                            <li><strong>If applicant is the REGISTERED OWNER:</strong>
                                                <ul style="margin-left:20px;">
                                                    <li>Certified True Copy of Certificate of Title (1 photocopy) -
                                                        Registry of Deeds</li>
                                                    <li>Certified true copy of Tax Declaration (1 photocopy) - City
                                                        Assessor's Office</li>
                                                    <li>Land Tax Receipt of current year (1 photocopy) - BOSS Office
                                                        (CTO 2) or City Treasury Office</li>
                                                </ul>
                                            </li>
                                            <li><strong>If no Certificate of Title in applicant's name:</strong>
                                                <ul style="margin-left:20px;">
                                                    <li>Notarized Deed of Absolute Sale or Deed of Donation (1
                                                        photocopy) - Notary Public</li>
                                                    <li>Notarized Pro-forma Affidavit (1 original copy) - Notary Public
                                                    </li>
                                                    <li>Barangay Certification as to lot ownership (1 photocopy) -
                                                        Barangay Hall</li>
                                                </ul>
                                            </li>
                                            <li><strong>If applicant is NOT the owner:</strong>
                                                <ul style="margin-left:20px;">
                                                    <li>Notarized Affidavit of Consent OR Notarized Contract of Lease (1
                                                        original copy) - Notary Public</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>Architectural and Structural Plans signed and sealed by licensed Architect &
                                        Civil Engineer (1 original copy) - Design Professionals</li>
                                    <li>Notarized Detailed Cost of Materials with Specifications (1 original copy) -
                                        Design Professionals</li>
                                    <li>Sworn Special Power of Attorney (1 original copy) - Notary Public</li>
                                    <li>Affidavit of Non-objection from adjacent lot owners (1 original copy) - Notary
                                        Public</li>
                                    <li>Affidavit of Non-objection from Mortgage (1 original copy) - Notary Public</li>
                                    <li>Certification from MARO (1 original copy, 1 photocopy) - Department of Agrarian
                                        Reform</li>
                                    <li>Conversion Clearance or Certificate of Exemption for Conversion (1 original
                                        copy, 1 photocopy) - Department of Agrarian Reform</li>
                                    <li>Environmental Compliance Certificate (1 original copy, 1 photocopy) - Department
                                        of Environment and Natural Resources</li>
                                    <li>Road-Right-of-Way of at least three (3) meters - For dwellings on rear lots</li>
                                    <li>Order of Payment (1 original, 1 photocopy) - BOSS Office (CEO 2)</li>
                                    <li>Official Receipt (1 original, 3 photocopy) - City Treasury Office</li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESS STEPS</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>Step 1:</strong> Submit application form and required documents (15
                                        minutes)</p>
                                    <p><strong>Step 2:</strong> Verification of Land Use (Zoning) - 1 day</p>
                                    <p><strong>Step 3:</strong> Assessment and payment order of fees (1 day)</p>
                                    <p><strong>Step 4:</strong> Payment at City Treasury Office (2 minutes)</p>
                                    <p><strong>Step 5:</strong> Approval and release of Locational Clearance (1 day, 5
                                        minutes)</p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">FEES TO BE PAID
                                </h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p>• Documentary Stamp: Php 30.00</p>
                                    <p>• ICT Fee: Php 25.00</p>
                                    <p>• Additional fees based on assessment</p>
                                    <p><strong>Total: Php 55.00 + fees based on assessment</strong></p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESSING TIME
                                </h3>
                                <p style="color:#375e73; margin:0;">Total Processing Time: 2 Days, 22 minutes</p>
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
        function goBack() { window.location.href = '/services/other/engineering'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }
        function goNext() { window.location.href = '/services/other/engineering/locational/check'; }
    </script>
</body>

</html>