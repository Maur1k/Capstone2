<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mayor's Permit for Passenger Boat Service - Process</title>
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
                    <h1 class="page-title">Mayor's Permit for Passenger Boat Service</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">CHECKLIST OF
                                    REQUIREMENTS</h3>
                                <ul
                                    style="text-align:left; color:#375e73; line-height:1.6; margin:0 auto; max-width:700px;">
                                    <li>Community Tax Certificate (CTC) - Original or photocopy (Barangay of Residence)
                                    </li>
                                    <li>Barangay Clearance/Certification - Original and photocopy (Barangay of
                                        Residence)</li>
                                    <li>Barangay Business Clearance - Original (Lucap Barangay Hall - for Lucap
                                        operators only)</li>
                                    <li>City Tourism Office Certification - Original (City Tourism Office)</li>
                                    <li>Association Certification - Original or photocopy (Association)</li>
                                </ul>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESS STEPS</h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p><strong>Step 1:</strong> Submit requirements and fill up MPPBAF (3 minutes)</p>
                                    <p><strong>Step 2:</strong> Payment at City Treasurer's Office (3 minutes)</p>
                                    <p><strong>Step 3:</strong> CTRU processing and permit preparation (3 minutes)</p>
                                    <p><strong>Step 4:</strong> CTRU Head and City Administrator approval (2 minutes)
                                    </p>
                                    <p><strong>Step 5:</strong> Release of Mayor's Permit and sticker (2 minutes)</p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">FEES TO BE PAID
                                </h3>
                                <div style="color:#375e73; margin:0; text-align:left;">
                                    <p>• ICT Fee: Php 25.00</p>
                                    <p>• Mayor's Permit: Php 100.00</p>
                                    <p>• Inspection Fee: Php 200.00</p>
                                    <p>• Land Tax Clearance: Php 50.00</p>
                                    <p>• Account Clearance: Php 50.00</p>
                                    <p>• Sticker Fee: Php 65.00</p>
                                    <p><strong>Total: Php 490.00</strong></p>
                                    <br>
                                    <p><strong>Penalty:</strong> 25% of Mayor's Permit (P25)</p>
                                    <p><strong>Interest:</strong> P2.50 per month per boat</p>
                                </div>
                            </div>

                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:6px;">PROCESSING TIME
                                </h3>
                                <p style="color:#375e73; margin:0;">Total Processing Time: 9 Minutes</p>
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
        function goNext() { window.location.href = '/services/other/ctru/boat/check'; }
    </script>
</body>

</html>