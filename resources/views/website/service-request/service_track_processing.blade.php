@extends('website.layouts.app')

@section('title', 'Service Processing - Click2Serve')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/website/tracking-processing.css') }}">

    <div class="processing-page">
        <div class="container">
            <h1>Service Processing</h1>
            <p>Your service request is currently being processed.</p>

            <div class="status-card">
                <div class="status-icon">
                    <i class="fas fa-cog fa-spin"></i>
                </div>
                <h2>Processing</h2>
                <p>Please wait while we process your request...</p>
            </div>

            <div class="action-buttons">
                <a href="{{ route('track') }}" class="btn btn-primary">Check Status</a>
                <a href="{{ url('/') }}" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
@endsection