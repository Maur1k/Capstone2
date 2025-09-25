<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On-time COM - Step 2 - Click2Serve</title>
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
                    <h1 class="page-title">On-time Certificate of Marriage (COM) - Step 2</h1>
                </div>

                <div class="main-content">
                    <div class="form-panel">
                        <h2 class="form-title">Marriage Details</h2>
                        <p
                            style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 16px;">
                            Provide details of the marriage to complete your request
                        </p>

                        <form class="kiosk-form" id="comStep2Form">
                            <div class="section">
                                <h3 class="section-title">Marriage Information</h3>
                                <div class="grid-3">
                                    <div class="form-field">
                                        <label for="groom_name">Groom's Full Name</label>
                                        <input type="text" id="groom_name" name="groom_name"
                                            placeholder="First Middle Last" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="bride_name">Bride's Full Name</label>
                                        <input type="text" id="bride_name" name="bride_name"
                                            placeholder="First Middle Last" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="marriage_date">Date of Marriage</label>
                                        <input type="date" id="marriage_date" name="marriage_date" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="place_of_marriage">Place of Marriage</label>
                                        <input type="text" id="place_of_marriage" name="place_of_marriage"
                                            placeholder="Church/Court/Mosque" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="secondary-btn" onclick="goBack()">Back</button>
                                <button type="submit" class="primary-btn">Submit Request</button>
                            </div>
                        </form>
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
        function goBack() { window.location.href = '/services/civil/com/on-time'; }
        function navigateToChatbot() { window.location.href = '/chatbot'; }

        document.addEventListener('DOMContentLoaded', function () {
            const step1 = sessionStorage.getItem('comOnTimeStep1Data');
            if (!step1) { alert('Please complete Step 1 first.'); window.location.href = '/services/civil/com/on-time'; return; }
        });

        document.getElementById('comStep2Form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const step2 = Object.fromEntries(formData);
            const step1 = JSON.parse(sessionStorage.getItem('comOnTimeStep1Data') || '{}');

            const fullName = `${step1.first_name || ''} ${step1.middle_name || ''} ${step1.last_name || ''}`.trim();

            const requestData = {
                full_name: fullName,
                document_type: 'Certificate of Marriage (On-time)',
                contact_number: step1.contact_number,
                email: step1.email,
                address: step1.address,
                purpose: step1.purpose,
                additional_info: step1.additional_info,
                additional_info_2: {
                    groom_name: step2.groom_name,
                    bride_name: step2.bride_name,
                    marriage_date: step2.marriage_date,
                    place_of_marriage: step2.place_of_marriage
                }
            };

            const submitBtn = this.querySelector('.primary-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';
            submitBtn.disabled = true;
            try {
                const response = await fetch('/api/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(requestData)
                });
                const result = await response.json();
                if (result.success) {
                    const trackingData = {
                        full_name: fullName,
                        document_type: 'Certificate of Marriage (On-time)',
                        tracking_number: result.tracking_number,
                        queue_number: result.queue_number,
                        status: 'submitted',
                        date: new Date().toLocaleDateString(),
                        time: new Date().toLocaleTimeString(),
                        purpose: step1.purpose,
                        contact_number: step1.contact_number,
                        email: step1.email,
                        address: step1.address,
                        print_status: result.print_status
                    };
                    sessionStorage.setItem('currentTrackingData', JSON.stringify(trackingData));
                    sessionStorage.removeItem('comOnTimeStep1Data');
                    (function () {
                        const overlay = document.createElement('div'); overlay.style.position = 'fixed'; overlay.style.inset = '0'; overlay.style.display = 'grid'; overlay.style.placeItems = 'center'; overlay.style.background = 'rgba(0,0,0,0)'; overlay.style.zIndex = '9999';
                        const bubble = document.createElement('div'); bubble.style.padding = '18px 24px'; bubble.style.borderRadius = '16px'; bubble.style.boxShadow = '0 18px 40px rgba(0,0,0,.28)'; bubble.style.fontFamily = 'Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif'; bubble.style.fontWeight = '600'; bubble.style.fontSize = '18px'; bubble.style.color = '#fff'; bubble.style.background = '#0e90a6'; bubble.textContent = 'Request submitted! Printing receipt...'; overlay.appendChild(bubble); document.body.appendChild(overlay); setTimeout(() => overlay.remove(), 3000);
                    })();
                    setTimeout(() => { window.location.href = `/services/tracker?tracking=${result.tracking_number}&queue=${result.queue_number}`; }, 3000);
                } else {
                    (function () {
                        const o = document.createElement('div'); o.style.position = 'fixed'; o.style.inset = '0'; o.style.display = 'grid'; o.style.placeItems = 'center'; o.style.background = 'rgba(0,0,0,0)'; o.style.zIndex = '9999';
                        const b = document.createElement('div'); b.style.padding = '18px 24px'; b.style.borderRadius = '16px'; b.style.boxShadow = '0 18px 40px rgba(0,0,0,.28)'; b.style.fontFamily = 'Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif'; b.style.fontWeight = '600'; b.style.fontSize = '18px'; b.style.color = '#fff'; b.style.background = '#dc3545'; b.textContent = 'Error submitting request'; o.appendChild(b); document.body.appendChild(o); setTimeout(() => o.remove(), 3000);
                    })();
                }
            } catch (error) {
                (function () {
                    const o = document.createElement('div'); o.style.position = 'fixed'; o.style.inset = '0'; o.style.display = 'grid'; o.style.placeItems = 'center'; o.style.background = 'rgba(0,0,0,0)'; o.style.zIndex = '9999';
                    const b = document.createElement('div'); b.style.padding = '18px 24px'; b.style.borderRadius = '16px'; b.style.boxShadow = '0 18px 40px rgba(0,0,0,.28)'; b.style.fontFamily = 'Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif'; b.style.fontWeight = '600'; b.style.fontSize = '18px'; b.style.color = '#fff'; b.style.background = '#dc3545'; b.textContent = 'Network error'; o.appendChild(b); document.body.appendChild(o); setTimeout(() => o.remove(), 3000);
                })();
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    </script>
</body>

</html>