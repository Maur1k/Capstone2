<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="dashboard-layout">
            @if($admin->role === 'super_admin')
                <aside class="dashboard-sidebar">
                    <div class="sidebar-header">
                        <div>
                            <p class="sidebar-label">Super Admin</p>
                            <h3 class="sidebar-title"><i class="fas fa-sliders-h"></i>Settings</h3>
                            <p class="sidebar-subtitle">Quick access to every kiosk/website setting.</p>
                        </div>
                    </div>

                    <div class="sidebar-group">
                        <p class="sidebar-label">Kiosk Management</p>
                        <nav class="sidebar-nav">
                            <a href="{{ route('admin.officers.index') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-user-shield"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">Officers</span>
                                    <span class="sidebar-link-desc">Accounts & roles</span>
                                </div>
                            </a>
                            <a href="{{ route('admin.services.manage') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-briefcase"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">Manage Services</span>
                                    <span class="sidebar-link-desc">Service tiles</span>
                                </div>
                            </a>
                            <a href="{{ route('admin.dashboard.edit') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-pen-to-square"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">Edit Dashboard</span>
                                    <span class="sidebar-link-desc">Hero & info</span>
                                </div>
                            </a>
                            <a href="{{ route('admin.activity-logs.index') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-list-check"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">Activity Logs</span>
                                    <span class="sidebar-link-desc">Officer history</span>
                                </div>
                            </a>
                            <a href="{{ route('admin.feedbacks.index') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-star"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">User Feedbacks</span>
                                    <span class="sidebar-link-desc">Ratings & reviews</span>
                                </div>
                            </a>
                            <a href="{{ route('admin.floors.index') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-cube"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">3D Map Floors</span>
                                    <span class="sidebar-link-desc">Buildings & GLB</span>
                                </div>
                            </a>
                            <a href="{{ route('admin.pois.index') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-location-dot"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">3D Map POIs</span>
                                    <span class="sidebar-link-desc">Wayfinding</span>
                                </div>
                            </a>
                        </nav>
                    </div>

                    <div class="sidebar-group">
                        <p class="sidebar-label">Website Content</p>
                        <nav class="sidebar-nav">
                            <a href="{{ route('admin.about.index') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-circle-info"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">About Content</span>
                                    <span class="sidebar-link-desc">Pages & media</span>
                                </div>
                            </a>
                            <a href="{{ route('admin.blog.index') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-newspaper"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">Blog Posts</span>
                                    <span class="sidebar-link-desc">News & articles</span>
                                </div>
                            </a>
                            <a href="{{ route('admin.website.announcements.index') }}" class="sidebar-link">
                                <span class="sidebar-icon"><i class="fas fa-bullhorn"></i></span>
                                <div class="sidebar-text">
                                    <span class="sidebar-link-title">Announcements</span>
                                    <span class="sidebar-link-desc">Website alerts</span>
                                </div>
                            </a>
                        </nav>
                    </div>
                </aside>
            @endif

            <div class="dashboard-main">
                <div class="header">
                    <div class="header-content">
                        <div>
                            <h1 class="header-title">Welcome, {{ $admin->name }}</h1>
                            <p class="header-subtitle">Admin Dashboard - System Overview</p>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                <div class="utility-links">
                </div>

                <!-- Statistics Cards -->
                @php
                    $statsTotal = max(1, $officerCount + $departmentCount);
                    $officerPercent = ($officerCount / $statsTotal) * 100;
                    $departmentPercent = ($departmentCount / $statsTotal) * 100;
                    $circumference = 2 * pi() * 50;
                @endphp
                <div class="stats-grid">
                    <div class="stats-card" onclick="toggleFilter('total_officers')">
                        <div class="stats-content">
                            <div class="stats-info">
                                <h3>{{ $officerCount }}</h3>
                                <p>Total Officers</p>

                            </div>
                            <div class="stats-chart-container">
                                <div class="stats-icon total">
                                    <i class="fas fa-users"></i>
                                </div>
                                <svg class="stats-chart" viewBox="0 0 120 120">
                                    <circle class="stats-chart-bg" cx="60" cy="60" r="50" fill="none" stroke="#e2e8f0"
                                        stroke-width="8" />
                                    <circle class="stats-chart-progress total" cx="60" cy="60" r="50" fill="none"
                                        stroke="url(#superAdminOfficersGradient)" stroke-width="8"
                                        stroke-linecap="round" stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $circumference * (1 - ($officerPercent / 100)) }}"
                                        transform="rotate(-90 60 60)" />
                                    <defs>
                                        <linearGradient id="superAdminOfficersGradient" x1="0%" y1="0%" x2="100%"
                                            y2="100%">
                                            <stop offset="0%" style="stop-color:#008080;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#00A0A0;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="stats-card" onclick="toggleFilter('departments')">
                        <div class="stats-content">
                            <div class="stats-info">
                                <h3>{{ $departmentCount }}</h3>
                                <p>Departments</p>

                            </div>
                            <div class="stats-chart-container">
                                <div class="stats-icon departments">
                                    <i class="fas fa-building"></i>
                                </div>
                                <svg class="stats-chart" viewBox="0 0 120 120">
                                    <circle class="stats-chart-bg" cx="60" cy="60" r="50" fill="none" stroke="#e2e8f0"
                                        stroke-width="8" />
                                    <circle class="stats-chart-progress departments" cx="60" cy="60" r="50" fill="none"
                                        stroke="url(#superAdminDepartmentsGradient)" stroke-width="8"
                                        stroke-linecap="round" stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $circumference * (1 - ($departmentPercent / 100)) }}"
                                        transform="rotate(-90 60 60)" />
                                    <defs>
                                        <linearGradient id="superAdminDepartmentsGradient" x1="0%" y1="0%" x2="100%"
                                            y2="100%">
                                            <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtered Logs Container -->
                <div id="filteredLogs" class="filtered-logs">
                    <h4><i class="fas fa-list"></i> <span id="logsTitle">Data Details</span></h4>
                    <div id="logsContent">
                        <!-- Logs will be loaded here -->
                    </div>
                </div>


                <!-- Recent Officers Activity Section -->
                <div class="recent-section">
                    <h3><i class="fas fa-clock"></i> Recent Officers Activity</h3>
                    <div id="recentActivityContent">
                        <div class="loading-state">
                            <i class="fas fa-spinner fa-spin"></i> Loading recent activity...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentFilter = null;

        // Toggle filter by activity type
        function toggleFilter(type) {
            const logsContainer = document.getElementById('filteredLogs');
            const clickedCard = event.currentTarget;

            // If clicking the same card, toggle off
            if (currentFilter === type) {
                // Remove active class from all cards
                document.querySelectorAll('.stats-card').forEach(card => {
                    card.classList.remove('active');
                });

                // Hide logs container
                logsContainer.classList.remove('show');
                currentFilter = null;
                return;
            }

            // Remove active class from all cards
            document.querySelectorAll('.stats-card').forEach(card => {
                card.classList.remove('active');
            });

            // Add active class to clicked card
            clickedCard.classList.add('active');

            // Show filtered logs container
            logsContainer.classList.add('show');

            // Update title based on filter type
            const logsTitle = document.getElementById('logsTitle');
            if (type === 'total_officers') {
                logsTitle.textContent = 'Officers List';
            } else if (type === 'departments') {
                logsTitle.textContent = 'Departments List';
            }

            // Set current filter
            currentFilter = type;

            // Fetch filtered logs
            fetchFilteredLogs(type);
        }

        // Fetch filtered logs via AJAX
        function fetchFilteredLogs(type) {
            const logsContent = document.getElementById('logsContent');
            logsContent.innerHTML = '<div class="loading-state"><i class="fas fa-spinner fa-spin"></i> Loading logs...</div>';

            fetch(`/admin/dashboard/filtered-logs?type=${type}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    displayFilteredLogs(data.logs, type);
                })
                .catch(error => {
                    console.error('Error:', error);
                    logsContent.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i> Error loading logs</div>';
                });
        }

        // Display filtered logs
        function displayFilteredLogs(logs, type) {
            const logsContent = document.getElementById('logsContent');

            if (logs.length === 0) {
                logsContent.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h4>No Data Found</h4>
                        <p>No ${type === 'total_officers' ? 'officers' : 'departments'} found</p>
                    </div>
                `;
                return;
            }

            let logsHTML = '';
            logs.forEach(log => {
                const departmentInfo = log.officer_department && log.officer_department !== 'N/A'
                    ? `<span class="department-info">• ${log.officer_department}</span>`
                    : '';

                logsHTML += `
                    <div class="log-item">
                        <div class="log-icon ${getActivityIcon(log.activity_type)}">
                            <i class="fas ${getActivityIconClass(log.activity_type)}"></i>
                        </div>
                        <div class="log-info">
                            <div class="log-officer">
                                ${log.officer_name || 'System'} ${departmentInfo}
                            </div>
                            <div class="log-activity">${log.description}</div>
                        </div>
                        <div class="log-time">${formatTime(log.created_at)}</div>
                    </div>
                `;
            });

            logsContent.innerHTML = logsHTML;
        }

        // Get activity icon class
        function getActivityIconClass(activityType) {
            const iconMap = {
                'login': 'fa-sign-in-alt',
                'logout': 'fa-sign-out-alt',
                'status_change': 'fa-exchange-alt',
                'request_viewed': 'fa-eye',
                'request_created': 'fa-plus',
                'request_updated': 'fa-edit',
                'create': 'fa-plus',
                'update': 'fa-edit',
                'delete': 'fa-trash',
                'view': 'fa-eye'
            };
            return iconMap[activityType] || 'fa-circle';
        }

        // Get activity icon type for styling
        function getActivityIcon(activityType) {
            const iconMap = {
                'login': 'login',
                'logout': 'logout',
                'status_change': 'update',
                'request_viewed': 'create',
                'request_created': 'create',
                'request_updated': 'update',
                'create': 'create',
                'update': 'update',
                'delete': 'delete'
            };
            return iconMap[activityType] || 'login';
        }

        // Format time
        function formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));

            if (diffInMinutes < 1) return 'Just now';
            if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
            if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
            return date.toLocaleDateString();
        }

        // Load recent activity
        function loadRecentActivity() {
            fetch('/admin/dashboard/recent-activity', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    displayRecentActivity(data.activities);
                })
                .catch(error => {
                    console.error('Error loading recent activity:', error);
                    document.getElementById('recentActivityContent').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Error Loading Activity</h4>
                        <p>Unable to load recent officer activity</p>
                    </div>
                `;
                });
        }

        // Display recent activity
        function displayRecentActivity(activities) {
            const content = document.getElementById('recentActivityContent');

            if (activities.length === 0) {
                content.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-clock"></i>
                        <h4>No Recent Activity</h4>
                        <p>No recent officer activity found</p>
                    </div>
                `;
                return;
            }

            let activitiesHTML = '';
            activities.forEach(activity => {
                const departmentInfo = activity.officer_department && activity.officer_department !== 'N/A'
                    ? activity.officer_department
                    : 'No Department';

                activitiesHTML += `
                    <div class="activity-item">
                        <div class="activity-icon ${getActivityIcon(activity.activity_type)}">
                            <i class="fas ${getActivityIconClass(activity.activity_type)}"></i>
                        </div>
                        <div class="activity-info">
                            <div class="activity-officer">${activity.officer_name || 'Unknown'}</div>
                            <div class="activity-description">${activity.description}</div>
                            <div class="activity-department">${departmentInfo}</div>
                        </div>
                        <div class="activity-time">${formatTime(activity.created_at)}</div>
                    </div>
                `;
            });

            content.innerHTML = activitiesHTML;
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            // Load recent activity on page load
            loadRecentActivity();

            // Set up real-time updates every 30 seconds
            setInterval(loadRecentActivity, 30000);

            // Add hover effects to action cards
            document.querySelectorAll('.action-card').forEach(card => {
                card.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-4px)';
                });

                card.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>

</html>