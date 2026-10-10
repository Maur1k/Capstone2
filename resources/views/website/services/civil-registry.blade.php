@extends('website.layouts.app')

@section('title', 'Civil Registry Services - Click2Serve')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/website/service-pages.css') }}">
@endpush

@section('content')
    <div class="service-page">
        <div class="container">
            <h1>City Civil Registry Office Services</h1>


            <div class="process-steps-section">
                <h2>Appointment Booking Process</h2>
                <div class="service-steps">
                    <ol>
                        <li>Select service (Choose between Regular or BRAP)</li>
                        <li>Choose your preferred date and time</li>
                        <li>Provide your personal details (Name, Contact Number, Age)</li>
                        <li>Receive confirmation and appointment number via SMS</li>
                    </ol>
                </div>
            </div>

            <div class="services-grid">
                <div class="service-card" id="delayed-regular">
                    <h3>Delayed Birth Registration (Regular)</h3>
                    <p>For delayed birth registration cases that do not qualify for the BRAP program.</p>
                    <a href="{{ route('appointment.service') }}?service={{ urlencode('Delayed Regular Non-BRAP') }}" class="btn-primary">
                        Book Appointment
                    </a>
                </div>

                <div class="service-card">
                    <h3>Delayed Birth Registration under Birth Registration Assistance Project (BRAP)</h3>
                    <p>For delayed birth registration cases that qualify for the BRAP program.</p>
                    <a href="{{ route('appointment.service') }}?service={{ urlencode('Delayed BRAP') }}" class="btn-primary">
                        Book Appointment
                    </a>
                </div>
            </div>

            <div class="contact-info">
                <h3>Contact Information</h3>
                <p><strong>Office:</strong> City Civil Registry Office</p>
                <p><strong>Location:</strong> City Hall, Alaminos City</p>
                <p><strong>Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</p>
            </div>
        </div>
    </div>
@endsection