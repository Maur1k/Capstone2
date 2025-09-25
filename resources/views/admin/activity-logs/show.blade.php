<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activity Log Details - Admin Dashboard</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div style="max-width:1200px; margin:24px auto;">
        <!-- Header -->
        <div
            style="background:#fff; border-radius:10px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:24px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <h2 style="margin:0;">Activity Log Details</h2>
                    <p style="margin:4px 0 0 0; color:#6b7280;">Detailed information about this activity</p>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <a href="{{ route('admin.activity-logs.index') }}"
                        style="background:#6b7280; color:#fff; text-decoration:none; padding:8px 12px; border-radius:6px;">←
                        Back to Logs</a>
                    <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit"
                            style="background:#ef4444; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Activity Details -->
        <div
            style="background:#fff; border-radius:10px; padding:24px; box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:24px;">
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px;">
                <div style="font-size:48px;">{{ $activityLog->activity_icon }}</div>
                <div>
                    <h3 style="margin:0; font-size:24px;">
                        {{ ucfirst(str_replace('_', ' ', $activityLog->activity_type)) }}</h3>
                    <p style="margin:4px 0 0 0; color:#6b7280; font-size:16px;">{{ $activityLog->description }}</p>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:24px;">
                <!-- Officer Information -->
                <div>
                    <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                        Officer Information</h4>
                    <div style="space-y:12px;">
                        <div>
                            <label style="font-weight:600; color:#374151;">Name:</label>
                            <p style="margin:4px 0;">{{ $activityLog->admin->name }}</p>
                        </div>
                        <div>
                            <label style="font-weight:600; color:#374151;">Email:</label>
                            <p style="margin:4px 0;">{{ $activityLog->admin->email }}</p>
                        </div>
                        <div>
                            <label style="font-weight:600; color:#374151;">Department:</label>
                            <p style="margin:4px 0;">{{ $activityLog->admin->department ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label style="font-weight:600; color:#374151;">Role:</label>
                            <p style="margin:4px 0;">{{ ucfirst(str_replace('_', ' ', $activityLog->admin->role)) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Activity Information -->
                <div>
                    <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                        Activity Information</h4>
                    <div style="space-y:12px;">
                        <div>
                            <label style="font-weight:600; color:#374151;">Activity Type:</label>
                            <p style="margin:4px 0;">{{ ucfirst(str_replace('_', ' ', $activityLog->activity_type)) }}
                            </p>
                        </div>
                        <div>
                            <label style="font-weight:600; color:#374151;">Description:</label>
                            <p style="margin:4px 0;">{{ $activityLog->description }}</p>
                        </div>
                        <div>
                            <label style="font-weight:600; color:#374151;">Timestamp:</label>
                            <p style="margin:4px 0;">{{ $activityLog->created_at->format('M d, Y H:i:s') }}</p>
                        </div>
                        <div>
                            <label style="font-weight:600; color:#374151;">Time Ago:</label>
                            <p style="margin:4px 0;">{{ $activityLog->formatted_time }}</p>
                        </div>
                    </div>
                </div>

                <!-- Target Information -->
                @if($activityLog->target_type && $activityLog->target_id)
                    <div>
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                            Target Information</h4>
                        <div style="space-y:12px;">
                            <div>
                                <label style="font-weight:600; color:#374151;">Target Type:</label>
                                <p
                                    style="margin:4px 0; font-family:monospace; background:#f3f4f6; padding:4px 8px; border-radius:4px; display:inline-block;">
                                    {{ $activityLog->target_type }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Target ID:</label>
                                <p
                                    style="margin:4px 0; font-family:monospace; background:#f3f4f6; padding:4px 8px; border-radius:4px; display:inline-block;">
                                    {{ $activityLog->target_id }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Technical Information -->
                <div>
                    <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                        Technical Information</h4>
                    <div style="space-y:12px;">
                        <div>
                            <label style="font-weight:600; color:#374151;">IP Address:</label>
                            <p
                                style="margin:4px 0; font-family:monospace; background:#f3f4f6; padding:4px 8px; border-radius:4px; display:inline-block;">
                                {{ $activityLog->ip_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label style="font-weight:600; color:#374151;">User Agent:</label>
                            <p
                                style="margin:4px 0; font-size:12px; background:#f3f4f6; padding:8px; border-radius:4px; word-break:break-all;">
                                {{ $activityLog->user_agent ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            @if($activityLog->metadata)
                <div style="margin-top:24px;">
                    <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                        Additional Data</h4>
                    <div style="background:#f9fafb; padding:16px; border-radius:8px;">
                        <pre
                            style="margin:0; font-size:14px; color:#374151; white-space:pre-wrap;">{{ json_encode($activityLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
