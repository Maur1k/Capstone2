<header>
    <div class="nav-left">
        <a href="{{ route('website.homepage') }}" class="logo">
            <img src="{{ asset('assets/logo/logo_click2serve.png') }}" alt="Click2Serve Logo">
        </a>
        <nav>
            <ul>
                <li><a href="{{ route('website.homepage') }}#home">Home</a></li>
                <li><a href="{{ route('website.homepage') }}#about">About</a></li>
                <li><a href="{{ route('website.homepage') }}#blog">Blog</a></li>

                <!-- Services dropdown with modal -->
                <li class="services-dropdown">
                    <a href="#" id="openServices">Services</a>
                    <div id="servicesModal" class="dropdown-modal">
                        <ul class="service-list">
                            <li class="has-sub">
                                <a href="{{ route('website.services.civil-registry') }}">City Civil Registry Office
                                    Services</a>
                                <div class="sub-modal">
                                    <ul>
                                        <li><a href="{{ route('website.services.civil-registry') }}#delayed-regular">Delayed
                                                Regular Non-BRAP (Birth Registration Assistance
                                                Program)</a></li>
                                        <li><a href="{{ route('steps') }}">Delayed BRAP (Birth Registration Assistance
                                                Program)</a></li>
                                    </ul>
                                </div>
                            </li>

                            <li class="has-sub">
                                <a href="{{ route('website.services.business-permit') }}">Business Permit and
                                    Licensing</a>
                                <div class="sub-modal">
                                    <ul>
                                        <li><a href="{{ route('website.services.business-permit') }}#new-registration">New
                                                Business Registration</a></li>
                                        <li><a href="{{ route('website.services.business-permit') }}#renewal">Renew
                                                Business Permit</a></li>
                                    </ul>
                                </div>
                            </li>

                            <li class="has-sub">
                                <a href="{{ route('website.services.transportation') }}">City Transport and Regulation
                                    Unit</a>
                                <div class="sub-modal">
                                    <ul>
                                        <li><a href="{{ route('website.services.transportation') }}#tricycle-franchise">Tricycle
                                                Franchise</a></li>
                                        <li><a
                                                href="{{ route('website.services.transportation') }}#drivers-accreditation">Driver's
                                                Accreditation</a></li>
                                    </ul>
                                </div>
                            </li>

                            <li class="has-sub">
                                <a href="{{ route('website.services.veterinary') }}">City Veterinary Office</a>
                                <div class="sub-modal">
                                    <ul>
                                        <li><a href="{{ route('website.services.veterinary') }}#pet-registration">Pet
                                                Registration</a></li>
                                        <li><a href="{{ route('website.services.veterinary') }}#anti-rabies">Anti-Rabies
                                                Vaccination for Walk-in Clients</a></li>
                                    </ul>
                                </div>
                            </li>

                            <li class="has-sub">
                                <a href="{{ route('website.services.engineering') }}">Engineering Office</a>
                                <div class="sub-modal">
                                    <ul>
                                        <li><a href="{{ route('website.services.engineering') }}#locational-clearance">Securing
                                                Locational Clearance for Building Permit</a></li>
                                        <li><a href="{{ route('website.services.engineering') }}#building-permit">Securing
                                                a Building Permit & Other Building Related Permits</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                <li><a href="{{ route('appointment.service') }}">Book Appointment</a></li>
            </ul>
        </nav>
    </div>
    <a href="{{ route('track') }}" class="btn">Service Tracker</a>
</header>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const mainModal = document.getElementById("servicesModal");
            const openBtn = document.getElementById("openServices");
            const serviceItems = document.querySelectorAll(".service-list li.has-sub");
            const subModals = document.querySelectorAll(".sub-modal");

            // Toggle main services dropdown
            openBtn.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                mainModal.style.display = (mainModal.style.display === "block") ? "none" : "block";
                subModals.forEach(m => m.style.display = "none");
            });

            // Only the top-level heading toggles its submodal
            serviceItems.forEach(item => {
                const topLink = item.querySelector(':scope > a'); // direct child <a>
                topLink.addEventListener("click", function (e) {
                    // Check if the link has a href that's not just "#"
                    if (topLink.getAttribute('href') === '#') {
                        e.preventDefault();
                        e.stopPropagation();

                        subModals.forEach(m => m.style.display = "none");

                        const targetModal = item.querySelector(".sub-modal");
                        if (targetModal) {
                            targetModal.style.display = "block";
                            targetModal.style.top = item.offsetTop + "px";
                            targetModal.style.left = (mainModal.offsetWidth + 5) + "px";
                        }
                    }
                    // If the link has a real href, let it navigate normally
                });
            });

            // Clicking outside closes everything
            document.addEventListener("click", function (e) {
                if (!mainModal.contains(e.target) && e.target !== openBtn) {
                    mainModal.style.display = "none";
                    subModals.forEach(m => m.style.display = "none");
                }
            });
        });
    </script>
@endpush