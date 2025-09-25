<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Details - {{ $civilRequest->tracking_number ?? $otherRequest->tracking_number }}</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh; overflow-x:auto;">
    <div style="max-width:1200px; margin:24px auto; padding-bottom:40px;">
        <!-- Header -->
        <div
            style="background:#fff; border-radius:10px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:24px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <h2 style="margin:0;">Request Details</h2>
                    <p style="margin:4px 0 0 0; color:#6b7280;">{{ $officer->department }}</p>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <a href="{{ route('officer.dashboard') }}"
                        style="background:#6b7280; color:#fff; text-decoration:none; padding:8px 12px; border-radius:6px;">←
                        Back to Dashboard</a>
                    <form method="POST" action="{{ route('officer.logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit"
                            style="background:#ef4444; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        @if(isset($civilRequest))
            <!-- Civil Service Request Details -->
            <div
                style="background:#fff; border-radius:10px; padding:24px; box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:24px;">
                <h3 style="margin:0 0 20px 0; color:#1f2937;">Civil Service Request Information</h3>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:20px;">
                    <!-- Basic Information -->
                    <div>
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                            Basic Information</h4>
                        <div style="space-y:12px;">
                            <div>
                                <label style="font-weight:600; color:#374151;">Tracking Number:</label>
                                <p
                                    style="margin:4px 0; font-family:monospace; background:#f3f4f6; padding:8px; border-radius:4px;">
                                    {{ $civilRequest->tracking_number }}
                                </p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Queue Number:</label>
                                <p style="margin:4px 0;">{{ $civilRequest->queue_number }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Service Type:</label>
                                <p style="margin:4px 0;">{{ $civilRequest->service_type }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Status:</label>
                                @php
                                    $statusColors = [
                                        'submitted' => '#f59e0b',
                                        'processing' => '#3b82f6',
                                        'completed' => '#10b981',
                                        'rejected' => '#ef4444'
                                    ];
                                    $color = $statusColors[$civilRequest->status] ?? '#6b7280';
                                @endphp
                                <span
                                    style="background:{{ $color }}20; color:{{ $color }}; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:500;">
                                    {{ ucfirst($civilRequest->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div>
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                            Client Information</h4>
                        <div style="space-y:12px;">
                            <div>
                                <label style="font-weight:600; color:#374151;">Full Name:</label>
                                <p style="margin:4px 0;">{{ $civilRequest->full_name }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Contact Number:</label>
                                <p style="margin:4px 0;">{{ $civilRequest->contact_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Email:</label>
                                <p style="margin:4px 0;">{{ $civilRequest->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Address:</label>
                                <p style="margin:4px 0;">{{ $civilRequest->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                @if($civilRequest->additional_info)
                    <div style="margin-top:20px;">
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                            Additional Information</h4>
                        <p style="margin:0; background:#f9fafb; padding:12px; border-radius:6px; white-space:pre-wrap;">
                            {{ $civilRequest->additional_info }}
                        </p>
                    </div>
                @endif

                <!-- Child Information (for birth certificates) -->
                @if($civilRequest->child_info)
                    <div style="margin-top:20px;">
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">Child
                            Information</h4>
                        <div style="background:#f9fafb; padding:12px; border-radius:6px;">
                            @php
                                $childInfo = is_string($civilRequest->child_info) ? json_decode($civilRequest->child_info, true) : $civilRequest->child_info;
                            @endphp
                            @if($childInfo)
                                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">
                                    @foreach($childInfo as $key => $value)
                                        <div>
                                            <label
                                                style="font-weight:600; color:#374151;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</label>
                                            <p style="margin:4px 0;">{{ $value }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Mother Information -->
                @if($civilRequest->mother_info)
                    <div style="margin-top:20px;">
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                            Mother Information</h4>
                        <div style="background:#f9fafb; padding:12px; border-radius:6px;">
                            @php
                                $motherInfo = is_string($civilRequest->mother_info) ? json_decode($civilRequest->mother_info, true) : $civilRequest->mother_info;
                            @endphp
                            @if($motherInfo)
                                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">
                                    @foreach($motherInfo as $key => $value)
                                        <div>
                                            <label
                                                style="font-weight:600; color:#374151;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</label>
                                            <p style="margin:4px 0;">{{ $value }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Father Information -->
                @if($civilRequest->father_info)
                    <div style="margin-top:20px;">
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                            Father Information</h4>
                        <div style="background:#f9fafb; padding:12px; border-radius:6px;">
                            @php
                                $fatherInfo = is_string($civilRequest->father_info) ? json_decode($civilRequest->father_info, true) : $civilRequest->father_info;
                            @endphp
                            @if($fatherInfo)
                                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">
                                    @foreach($fatherInfo as $key => $value)
                                        <div>
                                            <label
                                                style="font-weight:600; color:#374151;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</label>
                                            <p style="margin:4px 0;">{{ $value }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Administrative Information -->
                <div style="margin-top:20px;">
                    <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                        Administrative Information</h4>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:20px;">
                        <div>
                            <label style="font-weight:600; color:#374151;">Date Submitted:</label>
                            <p style="margin:4px 0;">{{ $civilRequest->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label style="font-weight:600; color:#374151;">Last Updated:</label>
                            <p style="margin:4px 0;">{{ $civilRequest->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        @if($civilRequest->processed_by)
                            <div>
                                <label style="font-weight:600; color:#374151;">Processed By:</label>
                                <p style="margin:4px 0;">{{ $civilRequest->processed_by }}</p>
                            </div>
                        @endif
                        @if($civilRequest->processed_at)
                            <div>
                                <label style="font-weight:600; color:#374151;">Processed At:</label>
                                <p style="margin:4px 0;">{{ $civilRequest->processed_at->format('M d, Y H:i') }}</p>
                            </div>
                        @endif
                        @if($civilRequest->admin_notes)
                            <div style="grid-column:1/-1;">
                                <label style="font-weight:600; color:#374151;">Admin Notes:</label>
                                <p
                                    style="margin:4px 0; background:#fef3c7; padding:12px; border-radius:6px; white-space:pre-wrap;">
                                    {{ $civilRequest->admin_notes }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(isset($otherRequest))
            <!-- Other Service Request Details -->
            <div
                style="background:#fff; border-radius:10px; padding:24px; box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:24px;">
                <h3 style="margin:0 0 20px 0; color:#1f2937;">Other Service Request Information</h3>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:20px;">
                    <!-- Basic Information -->
                    <div>
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                            Basic Information</h4>
                        <div style="space-y:12px;">
                            <div>
                                <label style="font-weight:600; color:#374151;">Tracking Number:</label>
                                <p
                                    style="margin:4px 0; font-family:monospace; background:#f3f4f6; padding:8px; border-radius:4px;">
                                    {{ $otherRequest->tracking_number }}
                                </p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Queue Number:</label>
                                <p style="margin:4px 0;">{{ $otherRequest->queue_number }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Service Type:</label>
                                <p style="margin:4px 0;">{{ $otherRequest->service_type }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Status:</label>
                                @php
                                    $statusColors = [
                                        'submitted' => '#f59e0b',
                                        'processing' => '#3b82f6',
                                        'completed' => '#10b981',
                                        'rejected' => '#ef4444'
                                    ];
                                    $color = $statusColors[$otherRequest->status] ?? '#6b7280';
                                @endphp
                                <span
                                    style="background:{{ $color }}20; color:{{ $color }}; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:500;">
                                    {{ ucfirst($otherRequest->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div>
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                            Client Information</h4>
                        <div style="space-y:12px;">
                            <div>
                                <label style="font-weight:600; color:#374151;">Full Name:</label>
                                <p style="margin:4px 0;">{{ $otherRequest->full_name }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Contact Number:</label>
                                <p style="margin:4px 0;">{{ $otherRequest->contact_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Email:</label>
                                <p style="margin:4px 0;">{{ $otherRequest->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label style="font-weight:600; color:#374151;">Address:</label>
                                <p style="margin:4px 0;">{{ $otherRequest->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                @if($otherRequest->additional_info)
                    <div style="margin-top:20px;">
                        <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                            Additional Information</h4>
                        <div style="background:#f9fafb; padding:12px; border-radius:6px;">
                            @php
                                $additionalInfo = is_string($otherRequest->additional_info) ? json_decode($otherRequest->additional_info, true) : $otherRequest->additional_info;
                            @endphp
                            @if($additionalInfo)
                                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">
                                    @foreach($additionalInfo as $key => $value)
                                        <div>
                                            <label
                                                style="font-weight:600; color:#374151;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</label>
                                            <p style="margin:4px 0;">{{ $value }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="margin:0; white-space:pre-wrap;">{{ $otherRequest->additional_info }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Administrative Information -->
                <div style="margin-top:20px;">
                    <h4 style="margin:0 0 12px 0; color:#374151; border-bottom:2px solid #e5e7eb; padding-bottom:8px;">
                        Administrative Information</h4>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:20px;">
                        <div>
                            <label style="font-weight:600; color:#374151;">Date Submitted:</label>
                            <p style="margin:4px 0;">{{ $otherRequest->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label style="font-weight:600; color:#374151;">Last Updated:</label>
                            <p style="margin:4px 0;">{{ $otherRequest->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        @if($otherRequest->processed_by)
                            <div>
                                <label style="font-weight:600; color:#374151;">Processed By:</label>
                                <p style="margin:4px 0;">{{ $otherRequest->processed_by }}</p>
                            </div>
                        @endif
                        @if($otherRequest->processed_at)
                            <div>
                                <label style="font-weight:600; color:#374151;">Processed At:</label>
                                <p style="margin:4px 0;">{{ $otherRequest->processed_at->format('M d, Y H:i') }}</p>
                            </div>
                        @endif
                        @if($otherRequest->admin_notes)
                            <div style="grid-column:1/-1;">
                                <label style="font-weight:600; color:#374151;">Admin Notes:</label>
                                <p
                                    style="margin:4px 0; background:#fef3c7; padding:12px; border-radius:6px; white-space:pre-wrap;">
                                    {{ $otherRequest->admin_notes }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
            <h3 style="margin:0 0 16px 0;">Update Request Status</h3>
            <form method="POST"
                action="{{ route('officer.requests.update-status', $civilRequest->tracking_number ?? $otherRequest->tracking_number) }}">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div>
                        <label style="display:block; font-size:14px; margin-bottom:6px; font-weight:600;">Status</label>
                        <select name="status" required
                            style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:6px;">
                            <option value="submitted" {{ ($civilRequest->status ?? $otherRequest->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="processing" {{ ($civilRequest->status ?? $otherRequest->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ ($civilRequest->status ?? $otherRequest->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="rejected" {{ ($civilRequest->status ?? $otherRequest->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:14px; margin-bottom:6px; font-weight:600;">Admin Notes
                            (Optional)</label>
                        <textarea name="admin_notes" rows="3"
                            style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:6px; resize:vertical;"
                            placeholder="Add any notes about this request...">{{ $civilRequest->admin_notes ?? $otherRequest->admin_notes ?? '' }}</textarea>
                    </div>
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <a href="{{ route('officer.dashboard') }}"
                        style="background:#e5e7eb; color:#374151; text-decoration:none; padding:10px 16px; border-radius:6px;">Cancel</a>
                    <button type="submit"
                        style="background:#0ea5e9; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">Update
                        Status</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>