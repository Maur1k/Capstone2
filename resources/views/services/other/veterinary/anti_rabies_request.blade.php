<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anti-Rabies Vaccination - Submit Request</title>
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
                    <h1 class="page-title">Anti-Rabies Vaccination - Submit Request</h1>
                </div>

                <div class="main-content">
                    <div class="services-section">
                        <div class="services-panel" style="max-width: 900px;">
                            <div class="service-tile" style="cursor: default;">
                                <h3 class="service-title" style="font-size:20px; margin-bottom:12px;">Submit an
                                    Anti-Rabies Vaccination Request
                                </h3>
                                <form id="antiRabiesRequestForm"
                                    style="max-width:640px; margin:0 auto; text-align:left;">
                                    <div class="form-field">
                                        <label>Pet Owner Full Name : <span style="color:#e53e3e">*</span></label>
                                        <input type="text" id="owner_name" placeholder="Enter Pet Owner Full Name"
                                            required>
                                    </div>
                                    <div class="form-field">
                                        <label>Pet Name : <span style="color:#e53e3e">*</span></label>
                                        <input type="text" id="pet_name" placeholder="Enter Pet Name" required>
                                    </div>
                                    <div class="form-field">
                                        <label>Pet Type : <span style="color:#e53e3e">*</span></label>
                                        <select id="pet_type" required>
                                            <option value="" disabled selected>Select Pet Type</option>
                                            <option value="Dog">Dog</option>
                                            <option value="Cat">Cat</option>
                                        </select>
                                    </div>
                                    <div class="form-field">
                                        <label>Pet Age (in months) : <span style="color:#e53e3e">*</span></label>
                                        <input type="number" id="pet_age" placeholder="Enter Pet Age in Months" min="3"
                                            required>
                                    </div>
                                    <div class="form-field">
                                        <label>Pet Registration Status : <span style="color:#e53e3e">*</span></label>
                                        <select id="registration_status" required>
                                            <option value="" disabled selected>Select Registration Status</option>
                                            <option value="Registered">Registered (Php 300.00)</option>
                                            <option value="Not Registered">Not Registered (Php 500.00)</option>
                                        </select>
                                    </div>
                                    <div class="form-field">
                                        <label>Address : <span style="color:#e53e3e">*</span></label>
                                        <input type="text" id="address" placeholder="Enter Complete Address" required>
                                    </div>
                                    <div class="form-field">
                                        <label>Mobile Number : <span style="color:#e53e3e">*</span></label>
                                        <input type="tel" id="contact_number" placeholder="Enter Phone Number" required>
                                    </div>
                                    <div class="form-field">
                                        <label>Previous Vaccination Date (if any) :</label>
                                        <input type="date" id="previous_vaccination">
                                    </div>
                                    <div style="text-align:center; margin-top:12px;">
                                        <button type="submit" class="primary-btn">Submit Anti-Rabies Vaccination
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
        function goBack() { window.location.href = '/services/other/veterinary/anti-rabies/check'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }

        document.getElementById('antiRabiesRequestForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const ownerName = document.getElementById('owner_name').value.trim();
            const petName = document.getElementById('pet_name').value.trim();
            const petType = document.getElementById('pet_type').value;
            const petAge = document.getElementById('pet_age').value;
            const registrationStatus = document.getElementById('registration_status').value;
            const address = document.getElementById('address').value.trim();
            const contact = document.getElementById('contact_number').value.trim();
            const previousVaccination = document.getElementById('previous_vaccination').value;

            if (!ownerName || !petName || !petType || !petAge || !registrationStatus || !address || !contact) {
                alert('Please fill up all the required fields');
                return;
            }

            if (parseInt(petAge) < 3) {
                alert('Pet must be at least 3 months old for anti-rabies vaccination');
                return;
            }

            const payload = {
                full_name: ownerName,
                pet_name: petName,
                service_type: 'City Veterinary Office - Anti-Rabies Vaccination for Walk-in Clients',
                contact_number: contact,
                address: address,
                purpose: 'Anti-Rabies Vaccination',
                additional_info: {
                    pet_type: petType,
                    pet_age: petAge,
                    registration_status: registrationStatus,
                    previous_vaccination: previousVaccination
                }
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
                        full_name: ownerName,
                        document_type: 'City Veterinary Office - Anti-Rabies Vaccination for Walk-in Clients',
                        tracking_number: result.tracking_number,
                        queue_number: result.queue_number,
                        status: 'submitted',
                        date: new Date().toLocaleDateString(),
                        time: new Date().toLocaleTimeString(),
                        purpose: 'Anti-Rabies Vaccination',
                        contact_number: contact,
                        email: '',
                        address: address,
                        print_status: result.print_status
                    };
                    sessionStorage.setItem('currentTrackingData', JSON.stringify(trackingData));
                    toast('Anti-rabies vaccination request submitted! Printing receipt...', 'info', 3000);
                    setTimeout(() => {
                        window.location.href = `/services/tracker?tracking=${result.tracking_number}&queue=${result.queue_number}`;
                    }, 3000);
                } else {
                    toast('Failed to submit anti-rabies vaccination request', 'error', 3000);
                }
            } catch (err) {
                toast('Network Error: ' + err.message, 'error', 3000);
            }
        });
    </script>
</body>

</html>

