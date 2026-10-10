@extends('website.layouts.app')

@section('title', 'Business Permit and Licensing Services - Click2Serve')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/website/service-pages.css') }}">
@endpush

@section('content')
    <div class="service-page">
        <div class="container">
            <h1>Business Permit and Licensing Services</h1>
            <p class="service-description">
                The Business Permit and Licensing Office (BPLO) handles all business registration and permit applications.
                Visit our office or use the kiosk to submit your request.
            </p>

            <div class="services-grid">
                <div class="service-card" id="new-registration">
                    <h3>New Business Registration</h3>
                    <p>Apply for a new business permit to start your business operations.</p>
                    <div class="service-steps">
                        <h4>Process Steps:</h4>
                        <ol>
                            <li>Proceed to BOSS - Submit pre-requirements and application form (25 minutes)</li>
                            <li>Payment of Mayor's Permit and other regulatory fees (10 minutes)</li>
                            <li>Secure Mayor's Permit and Business Plate (25 minutes)</li>
                            <li>Submit application request and receive tracking number</li>
                        </ol>
                    </div>
                </div>

                <div class="service-card" id="renewal">
                    <h3>Business Permit Renewal</h3>
                    <p>Renew your existing business permit to continue operations.</p>
                    <div class="service-steps">
                        <h4>Process Steps:</h4>
                        <ol>
                            <li>Proceed to BOSS - Submit pre-requirements (10 minutes)</li>
                            <li>Assessment and payment at CTO (30 minutes)</li>
                            <li>Secure Mayor's Permit and sticker (20 minutes)</li>
                            <li>Submit renewal request and receive tracking number</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="contact-info">
                <h3>Contact Information</h3>
                <p><strong>Office:</strong> Business Permit and Licensing Office (BPLO)</p>
                <p><strong>Location:</strong> City Hall, Alaminos City</p>
                <p><strong>Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</p>
            </div>
        </div>
    </div>
@endsection