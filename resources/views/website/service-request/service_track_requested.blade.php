@extends('website.layouts.app')

@section('title', 'Service Requested - Click2Serve')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/tracking-requested.css') }}">

    <div class="requested-page">
        <div class="container">
            <h1>Service Requested</h1>
            <p>Your service request has been submitted successfully.</p>

            <div class="status-card">
                <div class="status-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h2>Request Submitted</h2>
                <p>Your request is now in our queue and will be processed soon.</p>
            </div>

            <div class="action-buttons">
                <a href="{{ route('track') }}" class="btn btn-primary">Track Request</a>
                <a href="{{ url('/') }}" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
@endsection