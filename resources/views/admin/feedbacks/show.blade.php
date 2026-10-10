<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Details - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/feedbacks-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title"><i class="fas fa-star"></i> Feedback Details</h1>
                    <p class="header-subtitle">View detailed feedback information</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="logout-btn">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="utility-links">
        </div>

        <div class="detail-card">
            <div class="detail-header">
                <h2>Feedback Information</h2>
                <div class="actions">
                    <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <form action="{{ route('admin.feedbacks.destroy', $feedback) }}" method="POST" class="inline m-0"
                        onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Tracking Number</div>
                <div class="detail-value"><strong>{{ $feedback->tracking_number }}</strong></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Rating</div>
                <div class="detail-value">
                    <div class="rating-display">
                        <span class="rating-stars">
                            <span class="filled-stars">{{ str_repeat('★', $feedback->rating) }}</span>
                            <span class="empty-stars">{{ str_repeat('☆', 5 - $feedback->rating) }}</span>
                        </span>
                        <span class="rating-badge rating-{{ $feedback->rating }}">{{ $feedback->rating }}/5 -
                            {{ $feedback->rating_text }}</span>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Source</div>
                <div class="detail-value">
                    <span class="source-badge source-{{ $feedback->source }}">
                        {{ ucfirst($feedback->source) }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Date Submitted</div>
                <div class="detail-value">{{ $feedback->created_at->format('F d, Y h:i A') }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Message</div>
                <div class="detail-value">
                    @if($feedback->message)
                        <div class="message-box">{{ $feedback->message }}</div>
                    @else
                        <span class="text-gray-400">No message provided</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>