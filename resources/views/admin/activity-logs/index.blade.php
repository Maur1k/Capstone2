<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Officer Activity Logs - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/activity-logs-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Officer Activity Logs</h1>
                    <p class="header-subtitle">Monitor officer activities and system usage</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stats-card" data-filter="all" onclick="filterByActivity('all')">
                <div class="stats-content">
                    <div class="stats-info">
                        <h3>{{ number_format($stats['total_activities']) }}</h3>
                        <p>Total Activities</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card" data-filter="login" onclick="filterByActivity('login')">
                <div class="stats-content">
                    <div class="stats-info">
                        <h3>{{ $stats['logins_today'] }}</h3>
                        <p>Logins Today</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card" data-filter="status_change" onclick="filterByActivity('status_change')">
                <div class="stats-content">
                    <div class="stats-info">
                        <h3>{{ $stats['status_changes_today'] }}</h3>
                        <p>Status Changes Today</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <h3>
                <i class="fas fa-filter"></i>
                Advanced Filters
            </h3>
            <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Activity Type</label>
                        <select name="activity_type" class="form-input">
                            <option value="">All Activities</option>
                            @foreach($activityTypes as $type)
                                <option value="{{ $type }}" {{ request('activity_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Officer</label>
                        <select name="officer_id" class="form-input">
                            <option value="">All Officers</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->name }} ({{ $officer->department }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="content">
            @if($activityLogs->count() > 0)
                <div class="table-wrapper">
                    <table class="officers-table">
                        <thead>
                            <tr>
                                <th class="w-20p">Officer</th>
                                <th class="w-15p">Activity</th>
                                <th class="w-35p">Description</th>
                                <th class="w-15p">Time</th>
                                <th class="w-15p">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLogs as $log)
                                <tr>
                                    <td>
                                        <div class="officer-info">
                                            <div class="officer-name">{{ $log->admin->name }}</div>
                                            <div class="officer-email">{{ $log->admin->department }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="activity-badge {{ $log->activity_type }}">
                                            <i
                                                class="fas fa-{{ $log->activity_type === 'login' ? 'sign-in-alt' : ($log->activity_type === 'logout' ? 'sign-out-alt' : ($log->activity_type === 'status_change' ? 'exchange-alt' : ($log->activity_type === 'request_viewed' ? 'eye' : ($log->activity_type === 'request_created' ? 'plus' : ($log->activity_type === 'request_updated' ? 'edit' : 'circle'))))) }}"></i>
                                            {{ ucfirst(str_replace('_', ' ', $log->activity_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="description-text">{{ $log->description }}</span>
                                    </td>
                                    <td>
                                        <div class="time-info">
                                            <span class="time-main">{{ $log->created_at->format('M d, Y H:i') }}</span>
                                            <span class="time-ago">{{ $log->formatted_time }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.activity-logs.show', $log) }}" class="action-btn edit">
                                                <i class="fas fa-eye"></i>
                                                View Details
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    @if($activityLogs->currentPage() > 1)
                        <a href="{{ $activityLogs->previousPageUrl() }}" class="pagination-nav">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @else
                        <span class="pagination-nav disabled">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @endif

                    <div class="pagination-info">
                        {{ $activityLogs->currentPage() }} / {{ $activityLogs->lastPage() }}
                    </div>

                    @if($activityLogs->hasMorePages())
                        <a href="{{ $activityLogs->nextPageUrl() }}" class="pagination-nav">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="pagination-nav disabled">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>No Activity Logs Found</h4>
                    <p>Try adjusting your filters or check back later.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function filterByActivity(filterType) {
            document.querySelectorAll('.stats-card').forEach(card => {
                card.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
        }
    </script>
</body>

</html>