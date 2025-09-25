<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locational Clearance - Submit Request</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('css/civil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    <h1 class="page-title">Locational Clearance - Submit Request</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">Submit a
                                    Locational Clearance Request
                                </h3>
                                <form id="locationalRequestForm"
                                    style="max-width:640px; margin:0 auto; text-align:left;">
                                    <div class="form-field">
                                        <label>Full Name : <span style="color:#e53e3e">*</span></label>
                                        <input type="text" id="full_name" placeholder="Enter Full Name" required>
                                    </div>
                                    <div class="form-field">
                                        <label>Project Type : <span style="color:#e53e3e">*</span></label>
                                        <select id="project_type" required>
                                            <option value="" disabled selected>Select Project Type</option>
                                            <option value="New Construction">New Construction</option>
                                            <option value="Expansion">Expansion</option>
                                            <option value="Renovation">Renovation</option>
                                            <option value="Repair">Repair</option>
                                        </select>
                                    </div>
                                    <div class="form-field">
                                        <label>Property Address : <span style="color:#e53e3e">*</span></label>
                                        <input type="text" id="property_address"
                                            placeholder="Enter Complete Property Address" required>
                                    </div>
                                    <div class="form-field">
                                        <label>Mobile Number : <span style="color:#e53e3e">*</span></label>
                                        <input type="tel" id="contact_number" placeholder="Enter Phone Number" required>
                                    </div>
                                    <div style="text-align:center; margin-top:12px;">
                                        <button type="submit" class="primary-btn">Submit Locational Clearance
                                            Request</button>
                                    </div>
                                </form>
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
        function toast(message, type = 'info', ms = 3000) {
            const overlay = document.createElement('div');
            overlay.style.position = 'fixed';
            overlay.style.inset = '0';
            overlay.style.display = 'grid';
            overlay.style.placeItems = 'center';
            overlay.style.background = 'rgba(0,0,0,0.0)';
            overlay.style.zIndex = '9999';

            const bubble = document.createElement('div');
            bubble.style.padding = '18px 24px';
            bubble.style.borderRadius = '16px';
            bubble.style.boxShadow = '0 18px 40px rgba(0,0,0,.28)';
            bubble.style.fontFamily = 'Poppins, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif';
            bubble.style.fontWeight = '600';
            bubble.style.fontSize = '18px';
            bubble.style.color = '#fff';
            bubble.style.background = type === 'error' ? '#dc3545' : '#0e90a6';
            bubble.textContent = message;

            overlay.appendChild(bubble);
            document.body.appendChild(overlay);
            setTimeout(() => overlay.remove(), ms);
        }
        function goBack() { window.location.href = '/services/other/engineering/locational/check'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }

        document.getElementById('locationalRequestForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const fullName = document.getElementById('full_name').value.trim();
            const projectType = document.getElementById('project_type').value;
            const propertyAddress = document.getElementById('property_address').value.trim();
            const contact = document.getElementById('contact_number').value.trim();
            if (!fullName || !projectType || !propertyAddress || !contact) { alert('Please fill up all the fields'); return; }

            const payload = {
                full_name: fullName,
                project_type: projectType,
                service_type: 'Engineering Office - Securing Locational Clearance for Building Permit',
                contact_number: contact,
                address: propertyAddress,
                purpose: 'Locational Clearance for ' + projectType,
                additional_info: {}
            };

            try {
                const res = await fetch('/api/other-services', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                const text = await res.text();
                let result;
                try { result = JSON.parse(text); } catch (e) { throw new Error(text.slice(0, 120) || 'Invalid server response'); }
                if (result.success) {
                    const trackingData = {
                        full_name: fullName,
                        document_type: 'Engineering Office - Securing Locational Clearance for Building Permit',
                        tracking_number: result.tracking_number,
                        queue_number: result.queue_number,
                        status: 'submitted',
                        date: new Date().toLocaleDateString(),
                        time: new Date().toLocaleTimeString(),
                        purpose: 'Locational Clearance for ' + projectType,
                        contact_number: contact,
                        email: '',
                        address: propertyAddress,
                        print_status: result.print_status
                    };
                    sessionStorage.setItem('currentTrackingData', JSON.stringify(trackingData));
                    toast('Locational clearance request submitted! Printing receipt...', 'info', 3000);
                    setTimeout(() => {
                        window.location.href = `/services/tracker?tracking=${result.tracking_number}&queue=${result.queue_number}`;
                    }, 3000);
                } else {
                    toast('Failed to submit locational clearance request', 'error', 3000);
                }
            } catch (err) {
                toast('Network Error: ' + err.message, 'error', 3000);
            }
        });
    </script>
</body>

</html>