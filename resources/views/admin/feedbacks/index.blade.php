<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Feedbacks - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/feedbacks-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title"><i class="fas fa-star"></i> User Feedbacks</h1>
                    <p class="header-subtitle">View and manage client feedback submissions</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="logout-btn no-decoration mr-12">
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

        @if(session('success'))
            <div class="bg-success-light p-4 rounded-lg mb-6">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-card-content">
                    <div class="stats-card-header">

                        <h3>{{ $stats['total'] }}</h3>
                    </div>
                    <p>Total Feedbacks</p>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-card-content">
                    <div class="stats-card-header">

                        <div class="stats-value-wrapper">
                            <h3>{{ number_format($stats['average_rating'], 1) }}</h3>
                            <div class="stats-rating-display">
                                <span class="gold-star">★</span>
                                <span>/5</span>
                            </div>
                        </div>
                    </div>
                    <p>Average Rating</p>
                </div>
            </div>
            @foreach($stats['by_rating'] as $rating => $count)
                <div class="stats-card">
                    <div class="stats-card-content">
                        <div class="stats-card-header">

                            <h3>{{ $count }}</h3>
                        </div>
                        <p>{{ $rating }} Star{{ $rating > 1 ? 's' : '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.feedbacks.index') }}" class="filter-form" id="filterForm">
                <div class="form-group">
                    <label>Search Tracking Number</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Enter tracking number..." id="searchInput">
                </div>
                <div class="form-group">
                    <label>Filter by Rating</label>
                    <select name="rating" id="ratingSelect">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter by Source</label>
                    <select name="source" id="sourceSelect">
                        <option value="">All Sources</option>
                        <option value="kiosk" {{ request('source') == 'kiosk' ? 'selected' : '' }}>Kiosk</option>
                        <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100p">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.feedbacks.index') }}"
                        class="btn btn-secondary mt-8 w-100p text-center block">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
            @if(request()->hasAny(['search', 'rating', 'source']))
                <div class="active-filters">
                    <strong>Active Filters:</strong>
                    @if(request('search'))
                        <span class="filter-badge">
                            Search: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('rating'))
                        <span class="filter-badge">
                            Rating: {{ request('rating') }} Star{{ request('rating') > 1 ? 's' : '' }}
                        </span>
                    @endif
                    @if(request('source'))
                        <span class="filter-badge">
                            Source: {{ ucfirst(request('source')) }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Feedbacks Table -->
        <div class="feedback-table">
            <div class="table-header">
                <h2>All Feedbacks</h2>
            </div>
            @if($feedbacks->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Rating</th>
                            <th>Message</th>
                            <th>Source</th>
                            <th>Date Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($feedbacks as $feedback)
                            <tr>
                                <td><strong>{{ $feedback->tracking_number ?? 'N/A' }}</strong></td>
                                <td>
                                    <div class="rating-badge rating-{{ $feedback->rating }}">
                                        <span class="rating-stars">
                                            <span class="filled-stars">{{ str_repeat('★', $feedback->rating) }}</span>
                                            <span class="empty-stars">{{ str_repeat('☆', 5 - $feedback->rating) }}</span>
                                        </span>
                                        <span>{{ $feedback->rating }}/5</span>
                                    </div>
                                </td>
                                <td>
                                    @if($feedback->message)
                                        <div class="message-preview" title="{{ $feedback->message }}">
                                            {{ Str::limit($feedback->message, 50) }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">No message</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="source-badge source-{{ $feedback->source }}">
                                        {{ ucfirst($feedback->source) }}
                                    </span>
                                </td>
                                <td>{{ $feedback->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.feedbacks.show', $feedback) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <form action="{{ route('admin.feedbacks.destroy', $feedback) }}" method="POST"
                                            class="inline m-0"
                                            onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination">
                    {{ $feedbacks->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No feedbacks found</h3>
                    @if(request()->hasAny(['search', 'rating', 'source']))
                        <p>There are no feedbacks matching your current filters.</p>
                        <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-primary mt-16">
                            <i class="fas fa-times"></i> Clear All Filters
                        </a>
                    @else
                        <p>No feedbacks have been submitted yet.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        // Add Enter key support for search input
        document.getElementById('searchInput')?.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });

        // Auto-submit on select change (optional - uncomment if desired)
        // document.getElementById('ratingSelect')?.addEventListener('change', function() {
        //     document.getElementById('filterForm').submit();
        // });
        // document.getElementById('sourceSelect')?.addEventListener('change', function() {
        //     document.getElementById('filterForm').submit();
        // });
    </script>
</body>

</html>