<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Requests</title>
    <link rel="stylesheet" href="/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/requests-admin.css') }}">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="page-container">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h2 class="page-title">Request Management</h2>
                    <p class="page-subtitle">Manage BRAP and Regular requests</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>

            <!-- Notifications Panel -->
            @if($notifications->count() > 0)
                <div class="notifications-panel">
                    <div class="notifications-header">
                        <h3 class="notifications-title">Notifications ({{ $notifications->count() }})</h3>
                        <button onclick="markAllAsRead()" class="btn btn-sm">Mark All as Read</button>
                    </div>
                    @foreach($notifications as $notification)
                        <div class="notification-item" id="notification-{{ $notification->id }}">
                            <div class="notification-content">
                                <div class="notification-title">{{ $notification->title }}</div>
                                <div class="notification-message">{{ $notification->message }}</div>
                                <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                            <button onclick="markAsRead({{ $notification->id }})" class="btn btn-sm">Mark as Read</button>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Tab Container -->
            <div class="tab-container">
                <div class="tab-header">
                    <button class="tab-button active" onclick="switchTab('brap')">BRAP Requests</button>
                    <button class="tab-button" onclick="switchTab('regular')">Regular Requests</button>
                </div>

                <!-- BRAP Requests Tab -->
                <div id="brap-tab" class="tab-content active">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number">{{ $stats['brap_total'] }}</div>
                            <div class="stat-label">Total BRAP</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $stats['brap_pending'] }}</div>
                            <div class="stat-label">Pending</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $stats['brap_processing'] }}</div>
                            <div class="stat-label">Processing</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $stats['brap_completed'] }}</div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>

                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Tracking No</th>
                                <th>Queue No</th>
                                <th>Service Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="brap-requests-tbody">
                            @foreach($brapRequests as $request)
                                <tr>
                                    <td>{{ $request->full_name }}</td>
                                    <td>{{ $request->tracking_number }}</td>
                                    <td>{{ $request->queue_number }}</td>
                                    <td>{{ $request->service_type }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $request->status }}">
                                            {{ ucwords(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-sm btn-primary"
                                                onclick="viewRequest('{{ $request->tracking_number }}')">View</button>
                                            <button class="btn-sm"
                                                onclick="updateStatus('{{ $request->tracking_number }}', '{{ $request->status }}')">Update
                                                Status</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Regular Requests Tab -->
                <div id="regular-tab" class="tab-content">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number">{{ $stats['regular_total'] }}</div>
                            <div class="stat-label">Total Regular</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $stats['regular_pending'] }}</div>
                            <div class="stat-label">Pending</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $stats['regular_processing'] }}</div>
                            <div class="stat-label">Processing</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $stats['regular_completed'] }}</div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>

                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Tracking No</th>
                                <th>Queue No</th>
                                <th>Service Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="regular-requests-tbody">
                            @foreach($regularRequests as $request)
                                <tr>
                                    <td>{{ $request->full_name }}</td>
                                    <td>{{ $request->tracking_number }}</td>
                                    <td>{{ $request->queue_number }}</td>
                                    <td>{{ $request->service_type }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $request->status }}">
                                            {{ ucwords(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-sm btn-primary"
                                                onclick="viewRequest('{{ $request->tracking_number }}')">View</button>
                                            <button class="btn-sm"
                                                onclick="updateStatus('{{ $request->tracking_number }}', '{{ $request->status }}')">Update
                                                Status</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Request Status</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="statusForm">
                <input type="hidden" id="trackingNumber" name="tracking_number">
                <div class="form-group">
                    <label class="form-label" for="status">New Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="inquiry">Inquiry</option>
                        <option value="checklist_given">Checklist of Requirements Given</option>
                        <option value="incomplete_requirements">Incomplete Requirements</option>
                        <option value="accepted">Accepted</option>
                        <option value="registered">Registered</option>
                        <option value="encoded_brap">Encoded to BRAP Request List</option>
                        <option value="negative_result">Negative Result</option>
                        <option value="positive_result">Positive Result</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="admin_notes">Admin Notes (Optional)</label>
                    <textarea class="form-textarea" id="admin_notes" name="admin_notes"
                        placeholder="Enter any additional notes..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Messages Container -->
    <div id="floating-messages"></div>

    <script>
        // Show floating message
        function showFloatingMessage(message, type = 'success') {
            const container = document.getElementById('floating-messages');
            const messageEl = document.createElement('div');
            messageEl.className = `floating-message ${type}`;

            const icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' :
                type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' :
                    '<i class="fas fa-exclamation-triangle"></i>';
            messageEl.innerHTML = `${icon}${message}`;

            container.appendChild(messageEl);

            // Trigger animation
            setTimeout(() => messageEl.classList.add('show'), 100);

            // Remove after 4 seconds
            setTimeout(() => {
                messageEl.classList.remove('show');
                setTimeout(() => {
                    if (messageEl.parentElement) {
                        container.removeChild(messageEl);
                    }
                }, 300);
            }, 4000);
        }
    </script>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }

        function viewRequest(trackingNumber) {
            window.open('{{ route("admin.requests.view", ":trackingNumber") }}'.replace(':trackingNumber', trackingNumber), '_blank');
        }

        function updateStatus(trackingNumber, currentStatus) {
            document.getElementById('trackingNumber').value = trackingNumber;
            document.getElementById('status').value = currentStatus;
            document.getElementById('admin_notes').value = '';
            document.getElementById('statusModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        function markAsRead(notificationId) {
            fetch('{{ route("admin.notifications.mark-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    notification_id: notificationId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('notification-' + notificationId).style.display = 'none';
                    }
                });
        }

        function markAllAsRead() {
            fetch('{{ route("admin.notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }

        // Handle form submission
        document.getElementById('statusForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route("admin.requests.update-status") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showFloatingMessage('Status updated successfully', 'success');
                        closeModal();
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showFloatingMessage('Error: ' + (data.message || 'Failed to update status'), 'error');
                    }
                })
                .catch(error => {
                    showFloatingMessage('An error occurred. Please try again.', 'error');
                    console.error('Error:', error);
                });
        });

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('statusModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>