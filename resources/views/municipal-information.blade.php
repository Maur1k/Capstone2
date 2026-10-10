<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Municipal Information - Click2Serve Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/municipal-information.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/landing-kiosk.css') }}">
</head>

<body>
    @include('home.landing')
    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <!-- Header with back button -->
                <div class="header">
                    <button class="back-button" onclick="goBack()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <h1 class="page-title">Municipal Information</h1>
                </div>

                <!-- Main content -->
                <div class="main-content">
                    <!-- Content Container (White Card) -->
                    <div class="content-container">
                        <!-- Tabs Section -->
                        <div class="tabs-section">
                            <div class="tabs">
                                <button class="tab active" onclick="switchTab('officials')">City Officials</button>
                                <button class="tab" onclick="switchTab('tourist')">Tourist Spots</button>
                            </div>
                            
                        </div>

                        <!-- City Officials Tab -->
                        <div id="officials-tab" class="tab-content active">
                            @if($municipalInfo)
                                <div class="banner-section">
                                    <img src="{{ $municipalInfo->banner_image_url }}" alt="Hundred Islands"
                                        class="banner-image">
                                    <div class="banner-overlay">
                                        <div class="banner-text">
                                            @if($municipalInfo->city_seal)
                                                <img src="{{ $municipalInfo->city_seal_url }}" alt="City Seal"
                                                    class="city-seal">
                                            @endif
                                            <h2 class="city-name">{{ $municipalInfo->city_name }}</h2>
                                            <p class="city-tagline">{{ $municipalInfo->tagline }}</p>
                                        </div>
                                        <div class="mayor-info">
                                            <img src="{{ $municipalInfo->mayor_image_url }}" alt="Mayor"
                                                class="mayor-image">
                                            <h3 class="mayor-name">{{ $municipalInfo->mayor_name }}</h3>
                                            <p class="mayor-title">{{ $municipalInfo->mayor_title }}</p>
                                           
                                            
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="officials-section">
                                <h2 class="section-title">City Officials</h2>
                                @if($officialsByDepartment && $officialsByDepartment->count() > 0)
                                    @foreach($officialsByDepartment as $department => $departmentOfficials)
                                        <div class="department-officials-group" style="margin-bottom: 40px;">
                                            @if($department && $department !== '')
                                                <h3 class="department-title" style="font-size: 1.5rem; font-weight: 700; color: var(--teal-700); margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid var(--teal-700); text-align: center;">
                                                    {{ $department }}
                                                </h3>
                                            @endif
                                            <div class="officials-grid">
                                                @foreach($departmentOfficials as $official)
                                                    <div class="official-card">
                                                        <div class="official-image-wrapper">
                                                            <img src="{{ $official->image_url }}" alt="{{ $official->name }}"
                                                                class="official-image">
                                                        </div>
                                                        <div class="official-info">
                                                            <h3 class="official-name">{{ $official->name }}</h3>
                                                            <p class="official-position">{{ $official->position }}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-officials">
                                        <p>No city officials information available.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Tourist Spots Tab -->
                        <div id="tourist-tab" class="tab-content">
                            <div class="tourist-spots-section">
                                <div class="tourist-spots">
                                    @forelse($touristSpots as $spot)
                                        <div class="tourist-spot">
                                            <img src="{{ $spot->image_url }}" alt="{{ $spot->name }}"
                                                class="tourist-spot-image">
                                            <div class="tourist-spot-content">
                                                <h3>{{ $spot->name }}</h3>
                                                <p>{{ $spot->description }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                                            <p>No tourist spots information available.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat button -->
            <button class="chat" aria-label="chat" onclick="navigateToChatbot()">
                <img src="{{ asset('assets/icons/chatbot.png') }}" alt="chat" />
                <span>Chat</span>
            </button>

            <!-- Footer -->
            <div class="footer">Powered by: Click2Serve</div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Remove active class from all tabs and content
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Add active class to selected tab and content
            event.target.classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        }

        function goBack() {
            window.location.href = '/';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        // Prevent context menu and text selection for kiosk mode
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        document.addEventListener('selectstart', function (e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });

        // Enhanced touch feedback for interactive elements
        document.querySelectorAll('.tab, .back-button, .chat, .official-card, .tourist-spot').forEach(element => {
            element.addEventListener('touchstart', function (e) {
                this.style.transform = 'scale(0.98)';
                this.style.transition = 'transform 0.1s ease';
            });

            element.addEventListener('touchend', function (e) {
                this.style.transform = '';
                this.style.transition = 'all 0.3s ease';
            });

            element.addEventListener('touchcancel', function (e) {
                this.style.transform = '';
                this.style.transition = 'all 0.3s ease';
            });
        });

        // Ensure tab switching works on both click and touch
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function (e) {
                let tabName = this.textContent.toLowerCase().replace(' ', '');
                if (tabName === 'cityofficials') tabName = 'officials';
                if (tabName === 'touristspots') tabName = 'tourist';
                switchTab(tabName);
            });
        });

        // Smooth scrolling for touch devices
        document.addEventListener('DOMContentLoaded', function () {
            // Enable smooth scrolling on touch devices
            if ('ontouchstart' in window) {
                document.body.style.scrollBehavior = 'smooth';
            }
        });
    </script>
</body>

</html>