<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>{{ $serviceName ?? 'Service Types' }} - Click2Serve Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}?v={{ time() }}">
</head>

<body>
    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <!-- Header with back button -->
                <div class="header">
                    <div class="header-left">
                        <button class="back-button" onclick="goBack()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="header-center">
                        <h1 class="page-title">{{ $serviceName ?? 'Service Types' }}</h1>
                    </div>
                    <div class="header-right">
                        <!-- Empty for now, can be used for future elements -->
                    </div>
                </div>

                <!-- Main content -->
                <div class="main-content">
                    <!-- Content Container -->
                    <div class="content-container">
                        <!-- Services Content - Scrollable Area -->
                        <div class="services-content">
                            <div class="services-section">
                                <div class="services-grid other-services">
                                    @forelse($serviceTypes as $serviceType)
                                        <div class="service-tile"
                                            onclick="handleServiceTypeClick('{{ $serviceName }}', '{{ $department }}', '{{ $serviceType->name }}')"
                                            onkeydown="handleKeyPress(event, '{{ $serviceName }}', '{{ $department }}', '{{ $serviceType->name }}')"
                                            tabindex="0" role="button" aria-label="Select {{ $serviceType->name }} service">
                                            <div class="service-icon">
                                                <img src="{{ $serviceType->icon_path ? asset(ltrim($serviceType->icon_path, '/')) : ($service->icon_path ? asset(ltrim($service->icon_path, '/')) : asset('assets/icons/Services.png')) }}"
                                                    alt="{{ $serviceType->name }}" />
                                            </div>
                                            <h3 class="service-title">{{ $serviceType->name }}</h3>
                                        </div>
                                    @empty
                                        <div class="no-services"
                                            style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6b7280;">
                                            <p>No service types available for this service at the moment.</p>
                                            <button class="btn btn-primary" onclick="goToKioskForm()"
                                                style="margin-top: 20px;">
                                                Continue with General Service
                                            </button>
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
        // Debug: Log when page loads
        console.log('Service types page loaded');
        console.log('Service types count:', document.querySelectorAll('.service-tile').length);

        // Add event listeners as fallback
        document.addEventListener('DOMContentLoaded', function () {
            const serviceTiles = document.querySelectorAll('.service-tile');
            console.log('Found service tiles:', serviceTiles.length);

            serviceTiles.forEach((tile, index) => {
                console.log(`Setting up tile ${index}:`, tile);

                // Add click event listener as fallback
                tile.addEventListener('click', function (e) {
                    console.log('Tile clicked via event listener:', e);
                    e.preventDefault();
                    e.stopPropagation();

                    // Get the service details from the tile
                    const serviceName = '{{ $serviceName }}';
                    const department = '{{ $department }}';
                    const selectedType = tile.querySelector('.service-title').textContent.trim();

                    console.log('Redirecting with:', { serviceName, department, selectedType });
                    handleServiceTypeClick(serviceName, department, selectedType);
                });
            });
        });

        function goBack() {
            window.location.href = '/services';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        function handleServiceTypeClick(serviceName, department, selectedType) {
            console.log('Service type clicked:', { serviceName, department, selectedType });

            // Check if this is a Civil Registry service - go directly to kiosk
            if (department === 'Civil Registry Office') {
                window.location.href = `/services/kiosk?service=${encodeURIComponent(serviceName)}&department=${encodeURIComponent(department)}&type=regular&selected_type=${encodeURIComponent(selectedType)}`;
            } else {
                // For Other Services, go to requirements page first
                window.location.href = `/services/requirements?service=${encodeURIComponent(serviceName)}&department=${encodeURIComponent(department)}&selected_type=${encodeURIComponent(selectedType)}`;
            }
        }

        function handleKeyPress(event, serviceName, department, selectedType) {
            // Handle Enter and Space key presses
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                handleServiceTypeClick(serviceName, department, selectedType);
            }
        }

        function goToKioskForm() {
            // Fallback for services without types
            const serviceName = '{{ $serviceName ?? "Service Request" }}';
            const department = '{{ $department ?? "Municipal Office" }}';
            window.location.href = `/services/kiosk?service=${encodeURIComponent(serviceName)}&department=${encodeURIComponent(department)}&type=regular`;
        }
    </script>
</body>

</html>