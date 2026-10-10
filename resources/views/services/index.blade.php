<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Services - Click2Serve Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/landing-kiosk.css') }}">
</head>

<body>
    @include('home.landing')
    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <!-- Header with back button and info button -->
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
                    <div class="header-right">
                        <button class="info-button" onclick="toggleInfoModal()" title="Department Information">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                <path d="M12 16v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Main content -->
                <div class="main-content">
                    <!-- Content Container -->
                    <div class="content-container">
                        <!-- Services Content - Scrollable Area -->
                        <div class="services-content">
                            <!-- Prioritized Civil Services Section -->
                            <div class="services-section">
                                <h2 class="section-title">Civil Registration Services</h2>
                                <div class="services-grid prioritized-services">
                                    @php
                                        try {
                                            $prioritizedService = \App\Models\Service::where('name', 'Prioritized Civil Services')
                                                ->where('is_active', true)
                                                ->first();
                                            $prioritizedServiceTypes = $prioritizedService ? $prioritizedService->types()->where('is_active', true)->get() : collect();
                                        } catch (\Exception $e) {
                                            $prioritizedServiceTypes = collect();
                                        }
                                    @endphp
                                    @forelse($prioritizedServiceTypes as $serviceType)
                                        @php
                                            // Display shorter names for better UI
                                            $displayName = $serviceType->name;
                                            $description = '';
                                            if (strpos($serviceType->name, 'Delayed Regular Non-BRAP') !== false) {
                                                $displayName = 'Delayed Birth Registration (Regular)';
                                                $description = 'For delayed birth registration cases that do not qualify for BRAP program';
                                            } elseif (strpos($serviceType->name, 'Delayed BRAP') !== false) {
                                                $displayName = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
                                                $description = 'For delayed birth registration cases that qualify for BRAP program';
                                            }
                                        @endphp
                                        <div class="service-tile prioritized-tile"
                                            data-service-type="{{ $serviceType->name }}"
                                            onclick="handlePrioritizedServiceClick('{{ $serviceType->name }}', '{{ $prioritizedService->department }}', '{{ $serviceType->name }}')">
                                            <div class="service-icon">
                                                <img src="{{ $serviceType->icon_path ? asset(ltrim($serviceType->icon_path, '/')) : ($prioritizedService->icon_path ? asset(ltrim($prioritizedService->icon_path, '/')) : asset('assets/icons/civil-services.png')) }}"
                                                    alt="{{ $displayName }}" />
                                            </div>
                                            <h3 class="service-title">{{ $displayName }}</h3>
                                            @if($description)
                                                <p class="service-description">{{ $description }}</p>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="no-services"
                                            style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6b7280;">
                                            <p>No prioritized services available at the moment.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Other Municipal Services Section -->
                            <div class="services-section">
                                <h2 class="section-title">Other City Department Services</h2>
                                <div class="services-grid other-services">
                                    @php
                                        try {
                                            $otherServices = \App\Models\Service::where('is_active', true)
                                                ->where('name', '!=', 'Prioritized Civil Services')
                                                ->orderBy('name')
                                                ->get();
                                        } catch (\Exception $e) {
                                            $otherServices = collect();
                                        }
                                    @endphp
                                    @forelse($otherServices as $service)
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
                                            <p>No other services available at the moment.</p>
                                        </div>
                                    @endforelse
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

                <!-- Information Modal -->
                <div id="infoModal" class="info-modal">
                    <div class="info-modal-content">
                        <div class="info-modal-header">
                            <h2>Department Information</h2>
                            <button class="info-close-button" onclick="toggleInfoModal()">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div class="info-modal-body">
                            <!-- Important Notice Section -->
                            <div class="important-notice">
                                <div class="notice-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="notice-content">
                                    <h4>Important Notice</h4>
                                    <p><strong>One Reference Number = One Request</strong></p>
                                    <p>Each reference number is unique and corresponds to exactly one service request. You cannot use the same reference number for multiple requests or services.</p>
                                </div>
                            </div>

                            <!-- Department Sections -->
                            <div class="departments-container">
                                <div class="department-section">
                                    <h3>Civil Registry Department</h3>
                                    <ul>
                                        <li>
                                            <span class="service-name">Delayed Birth Registration under Birth Registration Assistance Project (BRAP)</span>
                                            <span class="service-description">Register late births under BRAP program for IDs, school enrollment, and employment purposes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Delayed Birth Registration (Regular)</span>
                                            <span class="service-description">Late birth registration for obtaining a valid birth certificate.</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="department-section">
                                    <h3>Social Welfare & Development Office</h3>
                                    <ul>
                                        <li>
                                            <span class="service-name">AICS - Assistance to Individuals in Crisis Situation</span>
                                            <span class="service-description">Financial assistance for people in crisis situations including medical, financial, and burial support.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Child Development Program</span>
                                            <span class="service-description">Daycare services and early learning programs for children.</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="department-section">
                                    <h3>Business Permit & Licensing Office</h3>
                                    <ul>
                                        <li>
                                            <span class="service-name">On-site Application for New Business Permit</span>
                                            <span class="service-description">Complete business registration process including document submission, payment of Mayor's Permit and regulatory fees, and securing business plate. Processing time: approximately 60 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">On-site Application for Renewal of Business Permit</span>
                                            <span class="service-description">Annual renewal process including document submission, assessment and payment at City Treasurer's Office, and securing Mayor's Permit with sticker. Processing time: approximately 60 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Issuance of General Permit</span>
                                            <span class="service-description">Special permits for cooperatives, firecrackers stallholders, and promotions and exhibitors. Processing time: 5-25 minutes depending on category.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Fishtrap/Fishpen Permit</span>
                                            <span class="service-description">Application for fishtrap and fishpen permits including document submission, payment of fees, and permit release. Processing time: 5-25 minutes.</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="department-section">
                                    <h3>City Transportation Regulation Unit</h3>
                                    <ul>
                                        <li>
                                            <span class="service-name">Motorized Tricycle Operator's Permit Service (MTOP)</span>
                                            <span class="service-description">Complete MTOP application process including PMTSRF form submission, payment at City Treasurer's Office, CTRU processing, approval by CTRU Head and City Administrator, and release of permit with sticker. Processing time: approximately 10-14 minutes for renewal, 25 minutes for new applications.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Permit to Travel (Tricycle)</span>
                                            <span class="service-description">Application process for tricycle travel permit including document submission, payment at City Treasurer's Office, CTRU processing, and approval. Processing time: approximately 8 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Order for Dropping (Tricycle)</span>
                                            <span class="service-description">Application for order to drop tricycle from registration including document submission, payment, and approval process. Processing time: approximately 8 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Certificate of Transfer of Ownership (Tricycle)</span>
                                            <span class="service-description">Application for transfer of tricycle ownership including document submission, payment of fees, and certificate release. Processing time: approximately 8 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Special Permit for Out-of-Line Operation (Tricycle)</span>
                                            <span class="service-description">Application for special permit allowing tricycle to operate outside designated route including document submission, payment, and approval. Processing time: approximately 8 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">PUV Registration Service</span>
                                            <span class="service-description">Registration service for Public Utility Vehicles including jeepneys, buses, and taxis. Includes document submission, payment of sticker fees, and registration. Processing time: approximately 5 minutes per vehicle.</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="department-section">
                                    <h3>City Veterinary Office</h3>
                                    <ul>
                                        <li>
                                            <span class="service-name">Pet Registration</span>
                                            <span class="service-description">Complete pet registration process including inquiry about requirements, Pre-Registration Form completion, PetRis System encoding and form printing, payment of registration and ICT fees, and receipt of Pet Registration Form. Processing time: approximately 19 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Anti-Rabies Vaccination for Walk-in Clients</span>
                                            <span class="service-description">Vaccination service to protect pets and people from rabies. Process includes inquiry about requirements, Personal Information System (PIS) enrollment check, logbook signing and payment of vaccination fees, vaccine preparation and inoculation, and post-vaccination examination and registration. Processing time: approximately 15 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Walk-in Veterinary Services</span>
                                            <span class="service-description">Comprehensive veterinary consultation and treatment services including health examination, diagnosis, treatment, and prescription of medications. Processing time: approximately 54 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Deworming of Small and Large Animals</span>
                                            <span class="service-description">Deworming service for small and large animals including assessment of animal condition, deworming procedure, and post-deworming advice. Processing time: approximately 27 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Issuance of Veterinary Health Certificate</span>
                                            <span class="service-description">Veterinary health certificate for animals including animal inspection, payment of fees, and certificate release. Processing time: approximately 7 minutes.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Animal Health Consultation</span>
                                            <span class="service-description">Professional veterinary consultation service including registration, health examination, diagnosis, treatment recommendations, and consultation report. Processing time: approximately 20-33 minutes.</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="department-section">
                                    <h3>City Engineering Office</h3>
                                    <ul>
                                        <li>
                                            <span class="service-name">Securing a Building Permit & Other Building Related Permits</span>
                                            <span class="service-description">Complete building permit application including document submission, plan evaluation (1-5 days), inspection and verification (18 days for highly technical), assessment and payment, fire endorsement, and permit approval and release (1 day). Total processing time: approximately 20-25 days.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Securing Locational Clearance for Building Permit</span>
                                            <span class="service-description">Application process for locational clearance including document submission, land use verification (1 day), assessment and payment order (1 day), payment at City Treasury Office, and approval and release (1 day). Total processing time: approximately 3 days.</span>
                                        </li>
                                        <li>
                                            <span class="service-name">Securing Certificate of Occupancy</span>
                                            <span class="service-description">Application process for certificate of occupancy including document submission, evaluation of plans and documents (1-12 days depending on complexity), final inspection, assessment and payment order, payment at City Treasury Office, and approval and release. Total processing time: approximately 2-13 days depending on application complexity.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="footer">Powered by: Click2Serve</div>
            </div>
        </div>

        <script>
            function goBack() {
                window.location.href = '/';
            }

            // Handle prioritized service clicks
            function handlePrioritizedServiceClick(serviceName, department, selectedType) {
                // Redirect to requirements page first for both BRAP services
                window.location.href = `/services/requirements?service=${encodeURIComponent(serviceName)}&department=${encodeURIComponent(department)}&selected_type=${encodeURIComponent(selectedType)}`;
            }

            // Handle other service clicks
            function handleServiceClick(serviceName, department) {
                // Redirect to service types page first
                window.location.href = `/services/types?service=${encodeURIComponent(serviceName)}&department=${encodeURIComponent(department)}`;
            }

            function navigateToChatbot() {
                window.location.href = '/chatbot';
            }

            // Toggle information modal
            function toggleInfoModal() {
                const modal = document.getElementById('infoModal');
                modal.classList.toggle('show');
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
            document.querySelectorAll('.service-tile, .prioritized-tile, .back-button, .chat').forEach(element => {
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