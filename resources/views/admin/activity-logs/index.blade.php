<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Officer Activity Logs - Admin Dashboard</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div class="page-container-wide">
        <!-- Header -->
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h2 class="page-title">Officer Activity Logs</h2>
                    <p class="page-subtitle">Monitor officer activities and system usage</p>
                </div>
                <div class="action-bar-right">
                    <a href="{{ route('admin.dashboard') }}" class="btn-back">← Back to Dashboard</a>
                    <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div
            style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:16px; margin-bottom:24px;">
            <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <p style="margin:0; color:#6b7280; font-size:14px;">Total Activities</p>
                        <h3 style="margin:8px 0 0 0; font-size:24px; color:#1f2937;">
                            {{ number_format($stats['total_activities']) }}
                        </h3>
                    </div>
                    <div style="background:#e0f2fe; padding:12px; border-radius:8px;">
                        <span style="font-size:20px;">📊</span>
                    </div>
                </div>
            </div>

            <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <p style="margin:0; color:#6b7280; font-size:14px;">Logins Today</p>
                        <h3 style="margin:8px 0 0 0; font-size:24px; color:#10b981;">{{ $stats['logins_today'] }}</h3>
                    </div>
                    <div style="background:#d1fae5; padding:12px; border-radius:8px;">
                        <span style="font-size:20px;">🔑</span>
                    </div>
                </div>
            </div>

            <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <p style="margin:0; color:#6b7280; font-size:14px;">Status Changes Today</p>
                        <h3 style="margin:8px 0 0 0; font-size:24px; color:#3b82f6;">
                            {{ $stats['status_changes_today'] }}
                        </h3>
                    </div>
                    <div style="background:#dbeafe; padding:12px; border-radius:8px;">
                        <span style="font-size:20px;">🔄</span>
                    </div>
                </div>
            </div>

            <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <p style="margin:0; color:#6b7280; font-size:14px;">Active Officers</p>
                        <h3 style="margin:8px 0 0 0; font-size:24px; color:#8b5cf6;">{{ $stats['active_officers'] }}
                        </h3>
                    </div>
                    <div style="background:#ede9fe; padding:12px; border-radius:8px;">
                        <span style="font-size:20px;">👥</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div
            style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:24px;">
            <h3 style="margin:0 0 16px 0;">Filters</h3>
            <form method="GET" action="{{ route('admin.activity-logs.index') }}"
                style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
                <div>
                    <label style="display:block; font-size:14px; margin-bottom:6px; font-weight:600;">Activity
                        Type</label>
                    <select name="activity_type"
                        style="width:100%; padding:8px 12px; border:1px solid #e5e7eb; border-radius:6px;">
                        <option value="">All Activities</option>
                        @foreach($activityTypes as $type)
                            <option value="{{ $type }}" {{ request('activity_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:14px; margin-bottom:6px; font-weight:600;">Officer</label>
                    <select name="officer_id"
                        style="width:100%; padding:8px 12px; border:1px solid #e5e7eb; border-radius:6px;">
                        <option value="">All Officers</option>
                        @foreach($officers as $officer)
                            <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>
                                {{ $officer->name }} ({{ $officer->department }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:14px; margin-bottom:6px; font-weight:600;">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        style="width:100%; padding:8px 12px; border:1px solid #e5e7eb; border-radius:6px;">
                </div>

                <div>
                    <label style="display:block; font-size:14px; margin-bottom:6px; font-weight:600;">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        style="width:100%; padding:8px 12px; border:1px solid #e5e7eb; border-radius:6px;">
                </div>

                <div style="display:flex; align-items:end; gap:8px;">
                    <button type="submit"
                        style="background:#0ea5e9; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">Filter</button>
                    <a href="{{ route('admin.activity-logs.index') }}"
                        style="background:#e5e7eb; color:#374151; text-decoration:none; padding:8px 16px; border-radius:6px;">Clear</a>
                </div>
            </form>
        </div>

        <!-- Activity Logs Table -->
        <div style="background:#fff; border-radius:10px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
            <h3 style="margin:0 0 16px 0;">Activity Logs</h3>

            @if($activityLogs->count() > 0)
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:separate; border-spacing:0 8px;">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:8px;">Officer</th>
                                <th style="text-align:left; padding:8px;">Activity</th>
                                <th style="text-align:left; padding:8px;">Description</th>
                                <th style="text-align:left; padding:8px;">Target</th>
                                <th style="text-align:left; padding:8px;">Time</th>
                                <th style="text-align:left; padding:8px;">IP Address</th>
                                <th style="text-align:left; padding:8px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLogs as $log)
                                <tr style="background:#f9fafb;">
                                    <td style="padding:10px 8px;">
                                        <div>
                                            <div style="font-weight:600;">{{ $log->admin->name }}</div>
                                            <div style="font-size:12px; color:#6b7280;">{{ $log->admin->department }}</div>
                                        </div>
                                    </td>
                                    <td style="padding:10px 8px;">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <span style="font-size:16px;">{{ $log->activity_icon }}</span>
                                            <span class="{{ $log->activity_color }}" style="font-weight:500;">
                                                {{ ucfirst(str_replace('_', ' ', $log->activity_type)) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td style="padding:10px 8px;">{{ $log->description }}</td>
                                    <td style="padding:10px 8px;">
                                        @if($log->target_type && $log->target_id)
                                            <div
                                                style="font-family:monospace; font-size:12px; background:#f3f4f6; padding:4px 8px; border-radius:4px;">
                                                {{ $log->target_type }} #{{ $log->target_id }}
                                            </div>
                                        @else
                                            <span style="color:#9ca3af;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding:10px 8px; color:#6b7280;">
                                        <div>{{ $log->created_at->format('M d, Y H:i') }}</div>
                                        <div style="font-size:12px;">{{ $log->formatted_time }}</div>
                                    </td>
                                    <td style="padding:10px 8px; font-family:monospace; font-size:12px; color:#6b7280;">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                    <td style="padding:10px 8px;">
                                        <a href="{{ route('admin.activity-logs.show', $log) }}"
                                            style="background:#3b82f6; color:#fff; text-decoration:none; padding:4px 8px; border-radius:4px; font-size:12px;">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="margin-top:20px; display:flex; justify-content:center;">
                    {{ $activityLogs->appends(request()->query())->links() }}
                </div>
            @else
                <div style="text-align:center; padding:40px; color:#6b7280;">
                    <p style="margin:0;">No activity logs found.</p>
                </div>
            @endif
        </div>
    </div>
</body>

</html>