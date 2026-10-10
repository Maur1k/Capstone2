@extends('website.layouts.app')

@section('title', 'Veterinary Office Services - Click2Serve')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/website/service-pages.css') }}">
@endpush

@section('content')
    <div class="service-page">
        <div class="container">
            <h1>City Veterinary Office Services</h1>
            <p class="service-description">
                The City Veterinary Office provides pet registration and animal health services.
                Visit our office or use the kiosk to submit your request.
            </p>

            <div class="services-grid">
                <div class="service-card" id="pet-registration">
                    <h3>Pet Registration</h3>
                    <p>Register your pets with the City Veterinary Office for proper documentation and tracking.</p>
                    <div class="service-steps">
                        <h4>Process Steps:</h4>
                        <ol>
                            <li>Fill out basic request information (Name, Contact Number)</li>
                            <li>Submit request and receive tracking number</li>
                            <li>Wait for officer to provide checklist of requirements</li>
                            <li>Submit required documents for processing</li>
                            <li>Complete pet registration and receive certificate</li>
                        </ol>
                    </div>
                </div>

                <div class="service-card" id="anti-rabies">
                    <h3>Anti-Rabies Vaccination for Walk-in Clients</h3>
                    <p>Get anti-rabies vaccination for your pets. Walk-in service available during office hours.</p>
                    <div class="service-steps">
                        <h4>Process Steps:</h4>
                        <ol>
                            <li>Bring your pet to the City Veterinary Office</li>
                            <li>Fill out basic information form</li>
                            <li>Veterinary officer will examine your pet</li>
                            <li>Administer anti-rabies vaccination</li>
                            <li>Receive vaccination certificate</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="contact-info">
                <h3>Contact Information</h3>
                <p><strong>Office:</strong> City Veterinary Office</p>
                <p><strong>Location:</strong> City Hall, Alaminos Cityy</p>
                <p><strong>Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</p>
            </div>
        </div>
    </div>
@endsection