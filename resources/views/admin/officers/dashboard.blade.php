<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Officer Dashboard - {{ $officer->department }}</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div style="max-width:1200px; margin:24px auto;">
        <!-- Header -->
        <div
            style="background:#fff; border-radius:10px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:24px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <h2 style="margin:0;">Officer Dashboard</h2>
                    <p style="margin:4px 0 0 0; color:#6b7280;">{{ $officer->department }}</p>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <span style="color:#6b7280;">Welcome, {{ $officer->name }}</span>
                    <form method="POST" action="{{ route('officer.logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit"
                            style="background:#ef4444; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">Logout</button>
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
                        <p style="margin:0; color:#6b7280; font-size:14px;">Total Requests</p>
                        <h3 style="margin:8px 0 0 0; font-size:24px; color:#1f2937;">{{ $stats['total'] }}</h3>
                    </div>
                    <div style="background:#e0f2fe; padding:12px; border-radius:8px;">
                        <span style="font-size:20px;">📋</span>
                    </div>
                </div>
            </div>

            <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <p style="margin:0; color:#6b7280; font-size:14px;">Pending</p>
                        <h3 style="margin:8px 0 0 0; font-size:24px; color:#f59e0b;">{{ $stats['pending'] }}</h3>
                    </div>
                    <div style="background:#fef3c7; padding:12px; border-radius:8px;">
                        <span style="font-size:20px;">⏳</span>
                    </div>
                </div>
            </div>

            <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <p style="margin:0; color:#6b7280; font-size:14px;">Processing</p>
                        <h3 style="margin:8px 0 0 0; font-size:24px; color:#3b82f6;">{{ $stats['processing'] }}</h3>
                    </div>
                    <div style="background:#dbeafe; padding:12px; border-radius:8px;">
                        <span style="font-size:20px;">⚙️</span>
                    </div>
                </div>
            </div>

            <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <p style="margin:0; color:#6b7280; font-size:14px;">Completed</p>
                        <h3 style="margin:8px 0 0 0; font-size:24px; color:#10b981;">{{ $stats['completed'] }}</h3>
                    </div>
                    <div style="background:#d1fae5; padding:12px; border-radius:8px;">
                        <span style="font-size:20px;">✅</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests Table -->
        <div style="background:#fff; border-radius:10px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,.06);">
            <h3 style="margin:0 0 16px 0;">Department Requests</h3>

            @if(session('status'))
                <div style="background:#ecfdf5; color:#065f46; padding:10px; border-radius:6px; margin-bottom:16px;">
                    {{ session('status') }}
                </div>
            @endif

            @if($allRequests->count() > 0)
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:separate; border-spacing:0 8px;">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:8px;">Tracking #</th>
                                <th style="text-align:left; padding:8px;">Service Type</th>
                                <th style="text-align:left; padding:8px;">Client Name</th>
                                <th style="text-align:left; padding:8px;">Status</th>
                                <th style="text-align:left; padding:8px;">Date Submitted</th>
                                <th style="text-align:left; padding:8px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allRequests as $request)
                                <tr style="background:#f9fafb;">
                                    <td style="padding:10px 8px; font-family:monospace;">{{ $request->tracking_number }}</td>
                                    <td style="padding:10px 8px;">{{ $request->service_type }}</td>
                                    <td style="padding:10px 8px;">{{ $request->full_name }}</td>
                                    <td style="padding:10px 8px;">
                                        @php
                                            $statusColors = [
                                                'submitted' => ['bg' => '#fef3c7', 'text' => '#f59e0b', 'label' => 'Pending'],
                                                'processing' => ['bg' => '#dbeafe', 'text' => '#3b82f6', 'label' => 'Processing'],
                                                'completed' => ['bg' => '#d1fae5', 'text' => '#10b981', 'label' => 'Completed'],
                                                'rejected' => ['bg' => '#fee2e2', 'text' => '#ef4444', 'label' => 'Rejected']
                                            ];
                                            $statusInfo = $statusColors[$request->status] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280', 'label' => ucfirst($request->status)];
                                        @endphp
                                        <div style="position:relative; display:inline-block;">
                                            <button onclick="toggleStatusDropdown('{{ $request->tracking_number }}')"
                                                style="background:{{ $statusInfo['bg'] }}; color:{{ $statusInfo['text'] }}; border:1px solid {{ $statusInfo['text'] }}; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:500; cursor:pointer; display:flex; align-items:center; gap:4px;">
                                                {{ $statusInfo['label'] }}
                                                <span style="font-size:10px;">▼</span>
                                            </button>
                                            <div id="statusDropdown{{ $request->tracking_number }}" 
                                                style="display:none; position:absolute; top:100%; left:0; background:#fff; border:1px solid #e5e7eb; border-radius:6px; box-shadow:0 4px 6px rgba(0,0,0,0.1); z-index:10; min-width:120px;">
                                                <form method="POST" action="{{ route('officer.requests.update-status', $request->tracking_number) }}" style="margin:0;">
                                                    @csrf
                                                    <input type="hidden" name="admin_notes" value="{{ $request->admin_notes ?? '' }}">
                                                    <button type="submit" name="status" value="submitted" 
                                                        style="width:100%; text-align:left; padding:8px 12px; border:none; background:none; cursor:pointer; font-size:12px; color:#f59e0b; {{ $request->status == 'submitted' ? 'background:#fef3c7;' : '' }}">
                                                        Pending
                                                    </button>
                                                    <button type="submit" name="status" value="processing" 
                                                        style="width:100%; text-align:left; padding:8px 12px; border:none; background:none; cursor:pointer; font-size:12px; color:#3b82f6; {{ $request->status == 'processing' ? 'background:#dbeafe;' : '' }}">
                                                        Processing
                                                    </button>
                                                    <button type="submit" name="status" value="completed" 
                                                        style="width:100%; text-align:left; padding:8px 12px; border:none; background:none; cursor:pointer; font-size:12px; color:#10b981; {{ $request->status == 'completed' ? 'background:#d1fae5;' : '' }}">
                                                        Completed
                                                    </button>
                                                    <button type="submit" name="status" value="rejected" 
                                                        style="width:100%; text-align:left; padding:8px 12px; border:none; background:none; cursor:pointer; font-size:12px; color:#ef4444; {{ $request->status == 'rejected' ? 'background:#fee2e2;' : '' }}">
                                                        Rejected
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding:10px 8px; color:#6b7280;">
                                        {{ $request->created_at->format('M d, Y H:i') }}</td>
                                    <td style="padding:10px 8px; white-space:nowrap;">
                                        <div style="display:flex; gap:8px;">
                                            <a href="{{ route('officer.requests.details', $request->tracking_number) }}"
                                                style="background:#3b82f6; color:#fff; text-decoration:none; padding:6px 8px; border-radius:6px; font-size:12px;">
                                                View Details
                                            </a>
                                            <button
                                                onclick="openStatusModal('{{ $request->tracking_number }}', '{{ $request->status }}', '{{ $request->admin_notes ?? '' }}')"
                                                style="background:#0ea5e9; color:#fff; border:none; padding:6px 8px; border-radius:6px; cursor:pointer; font-size:12px;">
                                                Update Status
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align:center; padding:40px; color:#6b7280;">
                    <p style="margin:0;">No requests found for your department.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:10px; padding:24px; width:500px; max-width:90%;">
            <h3 style="margin:0 0 16px 0;">Update Request Status</h3>
            <form method="POST" id="statusForm">
                @csrf
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:14px; margin-bottom:6px;">Tracking Number</label>
                    <input type="text" id="modalTrackingNumber" readonly
                        style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:6px; background:#f9fafb;">
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:14px; margin-bottom:6px;">Status</label>
                    <select id="modalStatus" name="status" required
                        style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:6px;">
                        <option value="submitted">Submitted</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:14px; margin-bottom:6px;">Admin Notes (Optional)</label>
                    <textarea id="modalNotes" name="admin_notes" rows="3"
                        style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:6px; resize:vertical;"
                        placeholder="Add any notes about this request..."></textarea>
                </div>

                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" onclick="closeStatusModal()"
                        style="background:#e5e7eb; color:#374151; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit"
                        style="background:#0ea5e9; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStatusModal(trackingNumber, currentStatus, currentNotes) {
            document.getElementById('modalTrackingNumber').value = trackingNumber;
            document.getElementById('modalStatus').value = currentStatus;
            document.getElementById('modalNotes').value = currentNotes;
            document.getElementById('statusForm').action = '/officer/requests/' + trackingNumber + '/status';
            document.getElementById('statusModal').style.display = 'flex';
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        function toggleStatusDropdown(trackingNumber) {
            // Close all other dropdowns
            const allDropdowns = document.querySelectorAll('[id^="statusDropdown"]');
            allDropdowns.forEach(dropdown => {
                if (dropdown.id !== 'statusDropdown' + trackingNumber) {
                    dropdown.style.display = 'none';
                }
            });

            // Toggle current dropdown
            const dropdown = document.getElementById('statusDropdown' + trackingNumber);
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[onclick^="toggleStatusDropdown"]') && !e.target.closest('[id^="statusDropdown"]')) {
                const allDropdowns = document.querySelectorAll('[id^="statusDropdown"]');
                allDropdowns.forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });

        // Close modal when clicking outside
        document.getElementById('statusModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });
    </script>
</body>

</html>
