<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log Details - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/activity-logs-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Activity Log Details</h1>
                    <p class="header-subtitle">Detailed information about this activity</p>
                </div>
                <a href="{{ route('admin.activity-logs.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Logs
                </a>
            </div>
        </div>

        <div class="card">
            <div class="activity-header">
                <div class="activity-icon">
                    <i
                        class="fas fa-{{ $activityLog->activity_type === 'login' ? 'sign-in-alt' : ($activityLog->activity_type === 'logout' ? 'sign-out-alt' : ($activityLog->activity_type === 'status_change' ? 'exchange-alt' : ($activityLog->activity_type === 'request_viewed' ? 'eye' : ($activityLog->activity_type === 'request_created' ? 'plus' : ($activityLog->activity_type === 'request_updated' ? 'edit' : 'circle'))))) }}"></i>
                </div>
                <div class="activity-info">
                    <h3 class="activity-title">
                        {{ ucfirst(str_replace('_', ' ', $activityLog->activity_type)) }}
                    </h3>
                    <p class="activity-description">{{ $activityLog->description }}</p>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-section">
                    <h4 class="section-title">
                        <i class="fas fa-user"></i>
                        Officer Information
                    </h4>
                    <div class="info-item">
                        <label class="info-label">Name</label>
                        <p class="info-value">{{ $activityLog->admin->name }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Email</label>
                        <p class="info-value">{{ $activityLog->admin->email }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Department</label>
                        <p class="info-value">{{ $activityLog->admin->department ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Role</label>
                        <p class="info-value">{{ ucfirst(str_replace('_', ' ', $activityLog->admin->role)) }}</p>
                    </div>
                </div>

                <div class="info-section">
                    <h4 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Activity Information
                    </h4>
                    <div class="info-item">
                        <label class="info-label">Activity Type</label>
                        <p class="info-value">{{ ucfirst(str_replace('_', ' ', $activityLog->activity_type)) }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Description</label>
                        <p class="info-value">{{ $activityLog->description }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Timestamp</label>
                        <p class="info-value">{{ $activityLog->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Time Ago</label>
                        <p class="info-value">{{ $activityLog->formatted_time }}</p>
                    </div>
                </div>

                @if($activityLog->target_type && $activityLog->target_id)
                    <div class="info-section">
                        <h4 class="section-title">
                            <i class="fas fa-bullseye"></i>
                            Target Information
                        </h4>
                        <div class="info-item">
                            <label class="info-label">Target Type</label>
                            <p class="info-value">
                                <span class="code-block">{{ $activityLog->target_type }}</span>
                            </p>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Target ID</label>
                            <p class="info-value">
                                <span class="code-block">{{ $activityLog->target_id }}</span>
                            </p>
                        </div>
                    </div>
                @endif

                <div class="info-section">
                    <h4 class="section-title">
                        <i class="fas fa-cog"></i>
                        Technical Information
                    </h4>
                    <div class="info-item">
                        <label class="info-label">IP Address</label>
                        <p class="info-value">
                            <span class="code-block">{{ $activityLog->ip_address ?? 'N/A' }}</span>
                        </p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">User Agent</label>
                        <p class="info-value">
                            <span class="code-block code-block-full">{{ $activityLog->user_agent ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            @if($activityLog->metadata)
                <div class="metadata-section">
                    <h4 class="section-title">
                        <i class="fas fa-database"></i>
                        Additional Data
                    </h4>
                    <div class="metadata-block">
                        <pre class="metadata-pre">{{ json_encode($activityLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>

</html>