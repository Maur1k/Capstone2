<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Click2Serve Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
</head>

<body>
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
                    @php
                        try {
                            $municipalName = \App\Models\Setting::get('municipal.name', 'Alaminos City');
                            $municipalTagline = \App\Models\Setting::get('municipal.tagline', 'Home of the Hundred Islands National Park');
                        } catch (\Exception $e) {
                            $municipalName = 'Alaminos City';
                        }
                    @endphp
                    <h1 class="page-title">{{ $municipalName }} - Services</h1>
                </div>

                <!-- Main content -->
                <div class="main-content">
                    <!-- Civil Registry Services Section -->
                    <div class="services-section">
                        <h2 class="section-title">Get Civil Registry Services</h2>
                        <div class="services-grid civil-services">
                            <div class="service-tile civil-service" onclick="window.location.href='/services/civil'">
                                <div class="service-icon">
                                    <img src="{{ asset('assets/icons/civil-services.png') }}" alt="Civil Services" />
                                </div>
                                <h3 class="service-title">Civil Services</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Other Municipal Services Section (Dynamic) -->
                    <div class="services-section">
                        <h2 class="section-title">Other Municipal Services</h2>
                        <div class="services-grid municipal-services">
                            @php
                                try {
                                    $services = \App\Models\Service::where('is_active', true)
                                        ->where('name', '!=', 'Civil Registry Services')
                                        ->orderBy('name')
                                        ->get();
                                } catch (\Exception $e) {
                                    $services = collect();
                                }
                            @endphp
                            @forelse($services as $service)
                                <div class="service-tile"
                                    onclick="handleServiceClick('{{ $service->name }}', '{{ $service->department }}')">
                                    <div class="service-icon">
                                        <img src="{{ $service->icon_path ? asset(ltrim($service->icon_path, '/')) : asset('assets/icons/Services.png') }}"
                                            alt="{{ $service->name }}" />
                                    </div>
                                    <h3 class="service-title">{{ $service->name }}</h3>
                                </div>
                            @empty
                                <div class="no-services"
                                    style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6b7280;">
                                    <p>No services available at the moment.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat button -->
            <button class="chat" aria-label="chat" onclick="navigateToChatbot()">
                <img src="{{ asset('assets/icons/chatbot.png') }}" alt="chat" />
            </button>

            <!-- Footer -->
            <div class="footer">Powered by: Smart Municipal Kiosk</div>
        </div>
    </div>

    <script>
        function goBack() {
            window.location.href = '/';
        }

        // Handle service clicks
        function handleServiceClick(serviceName, department) {
            // Route to specific service pages based on department or service name
            if (department === 'Business Permit and Licensing Office' ||
                serviceName.includes('Business Permit') ||
                serviceName.includes('BPLO')) {
                window.location.href = '/services/other/bplo';
            } else if (department === 'Civil Registry Office' ||
                serviceName.includes('Civil')) {
                window.location.href = '/services/civil';
            } else if (department === 'City Transportation Regulation Unit' ||
                serviceName.includes('Transportation') ||
                serviceName.includes('CTRU')) {
                window.location.href = '/services/other/ctru';
            } else if (department === 'Engineering Office' ||
                serviceName.includes('Engineering')) {
                window.location.href = '/services/other/engineering';
            } else if (department === 'Social Welfare and Development Office' ||
                serviceName.includes('Social Welfare') ||
                serviceName.includes('Social Development')) {
                window.location.href = '/services/other/social-welfare';
            } else if (department === 'City Veterinary Office' ||
                serviceName.includes('Veterinary')) {
                window.location.href = '/services/other/veterinary';
            } else {
                // For other services, show a coming soon message or redirect to a generic page
                alert(`${serviceName} services are coming soon!`);
            }
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }
    </script>
</body>

</html>