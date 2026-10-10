@extends('website.layouts.app')

@section('title', 'Service Completed - Click2Serve')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/tracking-completed.css') }}">

    <div class="completed-page">
        <div class="container">
            <h1>Service Completed</h1>
            <p>Your service request has been completed successfully.</p>

            <div class="status-card">
                <div class="status-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Completed</h2>
                <p>Your service request has been processed and completed.</p>
            </div>

            <div class="action-buttons">
                <a href="{{ route('track') }}" class="btn btn-primary">Track Another Request</a>
                <a href="{{ url('/') }}" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
@endsection