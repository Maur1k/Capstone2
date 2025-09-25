<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click2Serve Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/kiosk.css') }}">
</head>

<body>
    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <div class="header"></div>

                <div class="hero">
                    <div class="photo-panel">
                        <div class="slides">
                            <img class="slide active" src="{{ asset('assets/images/slide1.jpg') }}" alt="slide 1">
                            <img class="slide" src="{{ asset('assets/images/slide2.jpg') }}" alt="slide 2">
                            <img class="slide" src="{{ asset('assets/images/slide3.jpg') }}" alt="slide 3">
                        </div>
                        <div class="dots">
                            <span class="dot active"></span>
                            <span class="dot"></span>
                            <span class="dot"></span>
                        </div>
                    </div>

                    <div class="center-col">
                        <form id="trackingForm" onsubmit="handleTrackingSubmit(event)">
                            <input class="input-pill" id="trackingInput" placeholder="Enter Reference Code" required />
                            <button type="submit" class="track-btn">TRACK</button>
                        </form>
                        <div class="cards">
                            <div class="card" onclick="navigateToServices()">
                                <img src="{{ asset('assets/icons/Services.png') }}" alt="services" />
                                <h3>Services</h3>
                            </div>
                            <div class="card">
                                <img src="{{ asset('assets/icons/3dmap.png') }}" alt="map" />
                                <h3>3D Map &amp; Navigation</h3>
                            </div>
                            <div class="card">
                                <img src="{{ asset('assets/icons/municipalinfo.png') }}" alt="info" />
                                <h3>Municipal Informations</h3>
                            </div>
                        </div>
                    </div>

                    <div class="mayor">
                        <div class="mayor-figure"></div>
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
        (function () {
            const panel = document.querySelector('.photo-panel');
            if (!panel) return;
            const slides = Array.from(panel.querySelectorAll('.slide'));
            const dots = Array.from(panel.querySelectorAll('.dot'));
            let i = 0; const n = slides.length;
            function show(idx) {
                slides.forEach((el, j) => el.classList.toggle('active', j === idx));
                dots.forEach((el, j) => el.classList.toggle('active', j === idx));
                i = idx;
            }
            let timer = setInterval(() => { show((i + 1) % n); }, 4000);
            function go(idx) { show(idx); clearInterval(timer); timer = setInterval(() => { show((i + 1) % n); }, 4000); }
            dots.forEach((d, idx) => d.addEventListener('click', () => go(idx)));
        })();

        function navigateToServices() {
            window.location.href = '/services';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        function handleTrackingSubmit(event) {
            event.preventDefault();
            const trackingNumber = document.getElementById('trackingInput').value.trim();

            if (!trackingNumber) {
                alert('Please enter a tracking number');
                return;
            }

            // Show loading state
            const input = document.getElementById('trackingInput');
            const originalPlaceholder = input.placeholder;
            input.placeholder = 'Checking...';
            input.disabled = true;

            // Check if tracking number exists
            fetch(`/api/request/track/${trackingNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Navigate to tracker with tracking data
                        window.location.href = `/services/tracker?tracking=${trackingNumber}`;
                    } else {
                        alert('Tracking number not found. Please check your reference code.');
                        input.placeholder = originalPlaceholder;
                        input.disabled = false;
                        input.focus();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Unable to check tracking number. Please try again.');
                    input.placeholder = originalPlaceholder;
                    input.disabled = false;
                    input.focus();
                });
        }

        // Auto-focus on tracking input for kiosk use
        document.addEventListener('DOMContentLoaded', function () {
            const trackingInput = document.getElementById('trackingInput');
            if (trackingInput) {
                setTimeout(() => trackingInput.focus(), 500);
            }
        });
    </script>
</body>

</html>