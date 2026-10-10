@extends('website.layouts.app')

@section('title', 'Engineering Office Services - Click2Serve')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/website/service-pages.css') }}">
@endpush

@section('content')
    <div class="service-page">
        <div class="container">
            <h1>Engineering Office Services</h1>
            <p class="service-description">
                The Engineering Office handles building permits, locational clearances, and other construction-related
                services. Visit our office or use the kiosk to submit your request.
            </p>

            <div class="services-grid">
                <div class="service-card" id="locational-clearance">
                    <h3>Securing Locational Clearance for Building Permit</h3>
                    <p>Obtain locational clearance required for building permit applications.</p>
                    <div class="service-steps">
                        <h4>Process Steps:</h4>
                        <ol>
                            <li>Submit application form and required documents (15 minutes)</li>
                            <li>Verification of Land Use (Zoning) - 1 day</li>
                            <li>Assessment and payment order of fees (1 day)</li>
                            <li>Payment at City Treasury Office (2 minutes)</li>
                            <li>Approval and release of Locational Clearance (1 day, 5 minutes)</li>
                            <li>Submit locational clearance request and receive tracking number</li>
                        </ol>
                    </div>
                </div>

                <div class="service-card" id="building-permit">
                    <h3>Securing a Building Permit & Other Building Related Permits</h3>
                    <p>Apply for building permits and other construction-related permits.</p>
                    <div class="service-steps">
                        <h4>Process Steps:</h4>
                        <ol>
                            <li>Submit application form and required documents (30 minutes)</li>
                            <li>Evaluation of plans and documents (1-5 days)</li>
                            <li>Inspection and verification (18 days for highly technical)</li>
                            <li>Assessment and payment order of fees (1 hour)</li>
                            <li>Payment at City Treasury Office (2 minutes)</li>
                            <li>Fire endorsement preparation (5 minutes)</li>
                            <li>Permit approval and release (1 day, 5 minutes)</li>
                            <li>Submit building permit request and receive tracking number</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="contact-info">
                <h3>Contact Information</h3>
                <p><strong>Office:</strong> Engineering Office</p>
                <p><strong>Location:</strong> City Hall, Alaminos City</p>
                <p><strong>Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</p>
            </div>
        </div>
    </div>
@endsection