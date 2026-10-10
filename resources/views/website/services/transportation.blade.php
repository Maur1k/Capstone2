@extends('website.layouts.app')

@section('title', 'Transportation Services - Click2Serve')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/website/service-pages.css') }}">
@endpush

@section('content')
    <div class="service-page">
        <div class="container">
            <h1>City Transportation Regulation Unit Services</h1>
            <p class="service-description">
                The City Transportation Regulation Unit (CTRU) manages transportation permits and regulations.
                Visit our office or use the kiosk to submit your request.
            </p>

            <div class="services-grid">
                <div class="service-card" id="tricycle-franchise">
                    <h3>Motorized Tricycle Operator's Permit (MTOP)</h3>
                    <p>Apply for or renew your tricycle operator's permit.</p>
                    <div class="service-steps">
                        <h4>Process Steps:</h4>
                        <ol>
                            <li>Fill up PMTSRF and submit initial requirements (2-5 minutes)</li>
                            <li>Payment at City Treasurer's Office (3 minutes)</li>
                            <li>CTRU processing and document preparation (1-2 minutes)</li>
                            <li>CTRU Head and City Administrator approval (2 minutes)</li>
                            <li>Release of MTOP and sticker (2 minutes)</li>
                            <li>Submit MTOP request and receive tracking number</li>
                        </ol>
                    </div>
                </div>

                <div class="service-card" id="drivers-accreditation">
                    <h3>Mayor's Permit for Passenger Boat Service</h3>
                    <p>Apply for a permit to operate passenger boat services.</p>
                    <div class="service-steps">
                        <h4>Process Steps:</h4>
                        <ol>
                            <li>Submit requirements and fill up MPPBAF (3 minutes)</li>
                            <li>Payment at City Treasurer's Office (3 minutes)</li>
                            <li>CTRU processing and permit preparation (3 minutes)</li>
                            <li>CTRU Head and City Administrator approval (2 minutes)</li>
                            <li>Release of Mayor's Permit and sticker (2 minutes)</li>
                            <li>Submit boat permit request and receive tracking number</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="contact-info">
                <h3>Contact Information</h3>
                <p><strong>Office:</strong> City Transportation Regulation Unit (CTRU)</p>
                <p><strong>Location:</strong> City Hall, Alaminos City</p>
                <p><strong>Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</p>
            </div>
        </div>
    </div>
@endsection