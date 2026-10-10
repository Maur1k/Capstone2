<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Officer Dashboard - {{ $officer->department }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/officer-dashboard.css') }}">
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Welcome, {{ $officer->name }}</h1>
                    <p class="header-subtitle">Officer Dashboard - {{ $officer->department }}</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('officer.queue-number-settings') }}" class="notification-btn"
                        style="text-decoration: none;">
                        <i class="fas fa-cog"></i>
                        Queue Settings
                    </a>
                    <button onclick="showNotifications()" class="notification-btn">
                        <i class="fas fa-bell"></i>
                        Notifications
                        <span id="notificationBadge" class="notification-badge">0</span>
                    </button>
                    <a href="{{ route('officer.reports.requests') }}" class="notification-btn"
                        style="text-decoration: none;">
                        <i class="fas fa-print"></i>
                        Print All Requests
                    </a>
                    <form method="POST" action="{{ route('officer.logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics Overview Section -->
        @php
            $total = $stats['total'];
            $pending = $stats['pending'];
            $processing = $stats['processing'];
            $completed = $stats['completed'];
            $pendingPercent = $total > 0 ? ($pending / $total) * 100 : 0;
            $processingPercent = $total > 0 ? ($processing / $total) * 100 : 0;
            $completedPercent = $total > 0 ? ($completed / $total) * 100 : 0;
            
            // Get weekly data (simulated - you can replace with actual data)
            $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $today = now();
            $startOfWeek = $today->copy()->startOfWeek();
            $endOfWeek = $today->copy()->endOfWeek();
        @endphp
        
        <style>
            .overview-container {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
                margin-bottom: 32px;
            }
            
            @media (max-width: 1200px) {
                .overview-container {
                    grid-template-columns: 1fr;
                }
            }
            
            .overview-card {
                background: var(--white);
                border-radius: 16px;
                padding: 28px;
                box-shadow: var(--shadow-md);
                border: 1px solid var(--gray-200);
            }
            
            .overview-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 8px;
            }
            
            .overview-title {
                font-size: 1.125rem;
                font-weight: 700;
                color: var(--gray-800);
                margin: 0;
            }
            
            .overview-subtitle {
                font-size: 0.8125rem;
                color: var(--gray-500);
                margin: 4px 0 24px 0;
            }
            
            .overview-date-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                background: var(--gray-50);
                border: 1px solid var(--gray-200);
                border-radius: 8px;
                font-size: 0.8125rem;
                color: var(--gray-600);
                font-weight: 500;
            }
            
            .overview-date-badge i {
                color: var(--gray-400);
            }
            
            /* Week Navigation Styles */
            .week-nav-container {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .week-nav-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border: 1px solid var(--gray-200);
                background: var(--white);
                border-radius: 8px;
                color: var(--gray-600);
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .week-nav-btn:hover:not(:disabled) {
                background: var(--teal-primary);
                border-color: var(--teal-primary);
                color: white;
            }
            
            .week-nav-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
            
            .week-nav-btn i {
                font-size: 0.75rem;
            }
            
            .week-date-display {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                background: var(--gray-50);
                border: 1px solid var(--gray-200);
                border-radius: 8px;
                font-size: 0.8125rem;
                color: var(--gray-600);
                font-weight: 500;
                min-width: 200px;
                justify-content: center;
            }
            
            .week-date-display i {
                color: var(--teal-primary);
            }
            
            .week-date-display.loading {
                opacity: 0.6;
            }
            
            .week-label {
                font-size: 0.6875rem;
                color: var(--gray-500);
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-right: 4px;
            }
            
            .current-week-indicator {
                font-size: 0.625rem;
                background: var(--teal-primary);
                color: white;
                padding: 2px 6px;
                border-radius: 4px;
                margin-left: 8px;
            }
            
            .overview-left-content {
                display: grid;
                grid-template-columns: 200px 1fr;
                gap: 24px;
                align-items: stretch;
            }
            
            @media (max-width: 900px) {
                .overview-left-content {
                    grid-template-columns: 1fr;
                }
            }
            
            .overview-stats-stack {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }
            
            .overview-stat-box {
                padding: 20px;
                border-radius: 12px;
                border-left: 4px solid;
                background: var(--gray-50);
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .overview-stat-box:hover {
                transform: translateX(4px);
                box-shadow: var(--shadow-sm);
            }
            
            .overview-stat-box.today {
                border-left-color: var(--teal-primary);
            }
            
            .overview-stat-box.week {
                border-left-color: var(--teal-light);
            }
            
            .overview-stat-label {
                font-size: 0.8125rem;
                color: var(--teal-primary);
                font-weight: 600;
                margin-bottom: 8px;
            }
            
            .overview-stat-value {
                font-size: 2.25rem;
                font-weight: 700;
                color: var(--gray-800);
                line-height: 1.1;
                margin-bottom: 8px;
            }
            
            .overview-stat-change {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 0.75rem;
                font-weight: 600;
                padding: 4px 8px;
                border-radius: 4px;
            }
            
            .overview-stat-change.positive {
                color: #10b981;
                background: #d1fae5;
            }
            
            .overview-stat-change.negative {
                color: #ef4444;
                background: #fee2e2;
            }
            
            .overview-stat-change .date-label {
                color: var(--gray-500);
                font-weight: 400;
                margin-left: 4px;
            }
            
            /* Bar Chart */
            .bar-chart-container {
                display: flex;
                flex-direction: column;
                height: 100%;
                min-height: 200px;
            }
            
            .bar-chart {
                flex: 1;
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 12px;
                padding: 20px 0;
                border-bottom: 2px solid var(--gray-200);
            }
            
            .bar-item {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }
            
            .bar-wrapper {
                width: 100%;
                height: 120px;
                display: flex;
                align-items: flex-end;
                justify-content: center;
            }
            
            .bar {
                width: 70%;
                max-width: 40px;
                background: linear-gradient(180deg, var(--teal-primary) 0%, var(--teal-light) 100%);
                border-radius: 6px 6px 0 0;
                transition: all 0.3s ease;
                position: relative;
                cursor: pointer;
            }
            
            .bar:hover {
                background: linear-gradient(180deg, var(--teal-dark) 0%, var(--teal-primary) 100%);
                transform: scaleY(1.05);
            }
            
            .bar-value {
                position: absolute;
                top: -24px;
                left: 50%;
                transform: translateX(-50%);
                font-size: 0.75rem;
                font-weight: 600;
                color: var(--gray-600);
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .bar:hover .bar-value {
                opacity: 1;
            }
            
            .bar-label {
                font-size: 0.75rem;
                color: var(--gray-500);
                font-weight: 500;
                margin-top: 8px;
            }
            
            /* Right Side - Donut Chart Section */
            .overview-right-content {
                display: grid;
                grid-template-columns: 180px 1fr;
                gap: 24px;
                align-items: center;
            }
            
            @media (max-width: 900px) {
                .overview-right-content {
                    grid-template-columns: 1fr;
                    justify-items: center;
                }
            }
            
            .donut-chart-wrapper {
                position: relative;
                width: 160px;
                height: 160px;
            }
            
            .donut-chart {
                width: 100%;
                height: 100%;
                transform: rotate(-90deg);
            }
            
            .donut-segment {
                fill: none;
                stroke-width: 24;
                transition: all 0.3s ease;
                cursor: pointer;
            }
            
            .donut-segment:hover {
                stroke-width: 28;
                filter: brightness(1.1);
            }
            
            .donut-center {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                text-align: center;
            }
            
            .donut-center-label {
                font-size: 0.6875rem;
                color: var(--gray-500);
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .donut-center-value {
                font-size: 1.75rem;
                font-weight: 700;
                color: var(--gray-800);
                line-height: 1.2;
            }
            
            /* Status Table */
            .status-table-container {
                width: 100%;
            }
            
            .status-table {
                width: 100%;
                border-collapse: collapse;
            }
            
            .status-table th {
                text-align: left;
                font-size: 0.75rem;
                font-weight: 600;
                color: var(--gray-500);
                text-transform: uppercase;
                padding: 8px 12px;
                border-bottom: 1px solid var(--gray-200);
            }
            
            .status-table th:last-child,
            .status-table td:last-child {
                text-align: right;
            }
            
            .status-table td {
                padding: 12px;
                font-size: 0.875rem;
                color: var(--gray-700);
                border-bottom: 1px solid var(--gray-100);
            }
            
            .status-table tr {
                cursor: pointer;
                transition: background 0.2s ease;
            }
            
            .status-table tbody tr:hover {
                background: var(--gray-50);
            }
            
            .status-indicator {
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }
            
            .status-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                flex-shrink: 0;
            }
            
            .status-dot.total { background: var(--teal-primary); }
            .status-dot.pending { background: #f59e0b; }
            .status-dot.processing { background: #3b82f6; }
            .status-dot.completed { background: #10b981; }
            
            .status-name {
                font-weight: 500;
            }
            
            .status-percent {
                font-size: 0.75rem;
                color: var(--gray-500);
                font-weight: 500;
            }
            
            .status-count {
                font-weight: 600;
                color: var(--gray-800);
            }
        </style>
        
        <div class="overview-container">
            <!-- Left Panel - Requests Overview -->
            <div class="overview-card">
                <div class="overview-header">
                    <div>
                        <h3 class="overview-title">Requests Overview</h3>
                        <p class="overview-subtitle">The measurement of request activities.</p>
                    </div>
                    <div class="week-nav-container">
                        <button class="week-nav-btn" id="prevWeekBtn" onclick="navigateWeek(-1)" title="Previous Week">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="week-date-display" id="weekDateDisplay">
                            <i class="fas fa-calendar-alt"></i>
                            <span id="weekDateRange">{{ $startOfWeek->format('M j, Y') }} — {{ $endOfWeek->format('M j, Y') }}</span>
                            <span class="current-week-indicator" id="currentWeekBadge">Current</span>
                        </div>
                        <button class="week-nav-btn" id="nextWeekBtn" onclick="navigateWeek(1)" title="Next Week" disabled>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <div class="overview-left-content">
                    <div class="overview-stats-stack">
                        <div class="overview-stat-box today" onclick="showRequestsList('total')">
                            <div class="overview-stat-label">Today Requests</div>
                            <div class="overview-stat-value" id="todayRequestsCount">{{ $stats['today'] ?? 0 }}</div>
                            <div class="overview-stat-change {{ ($stats['today_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-arrow-{{ ($stats['today_change'] ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs($stats['today_change'] ?? 0) }}%
                                <span class="date-label">vs {{ now()->subDay()->format('M j, Y') }}</span>
                            </div>
                        </div>
                        
                        <div class="overview-stat-box week" onclick="showRequestsList('total')">
                            <div class="overview-stat-label">This Week Requests</div>
                            <div class="overview-stat-value" id="weekRequestsCount">{{ $stats['week'] ?? $total }}</div>
                            <div class="overview-stat-change {{ ($stats['week_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-arrow-{{ ($stats['week_change'] ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs($stats['week_change'] ?? 0) }}%
                                <span class="date-label">vs last week</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bar-chart-container">
                        <div class="bar-chart" id="weeklyBarChart">
                            @php
                                $weeklyData = $stats['weekly'] ?? [
                                    'Mon' => rand(5, 30),
                                    'Tue' => rand(5, 30),
                                    'Wed' => rand(5, 30),
                                    'Thu' => rand(5, 30),
                                    'Fri' => rand(5, 30),
                                    'Sat' => rand(2, 15),
                                    'Sun' => rand(2, 15)
                                ];
                                $maxValue = max($weeklyData) > 0 ? max($weeklyData) : 1;
                            @endphp
                            @foreach($weeklyData as $day => $count)
                                <div class="bar-item">
                                    <div class="bar-wrapper">
                                        <div class="bar" style="height: {{ ($count / $maxValue) * 100 }}%;">
                                            <span class="bar-value">{{ $count }}</span>
                                        </div>
                                    </div>
                                    <span class="bar-label">{{ $day }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Panel - Status Distribution -->
            <div class="overview-card">
                <div class="overview-header">
                    <div>
                        <h3 class="overview-title">Status Distribution</h3>
                        <p class="overview-subtitle">Breakdown of requests by current status.</p>
                    </div>
                    <div class="overview-date-badge">
                        <i class="fas fa-calendar-alt"></i>
                        {{ now()->format('M j, Y') }}
                    </div>
                </div>
                
                <div class="overview-right-content">
                    <div class="donut-chart-wrapper">
                        @php
                            $radius = 60;
                            $circumference = 2 * pi() * $radius;
                            $offset = 0;
                            
                            $segments = [
                                ['name' => 'pending', 'value' => $pending, 'color' => '#f59e0b'],
                                ['name' => 'processing', 'value' => $processing, 'color' => '#3b82f6'],
                                ['name' => 'completed', 'value' => $completed, 'color' => '#10b981'],
                            ];
                        @endphp
                        <svg class="donut-chart" viewBox="0 0 160 160">
                            @foreach($segments as $segment)
                                @php
                                    $percent = $total > 0 ? ($segment['value'] / $total) : 0;
                                    $dashLength = $circumference * $percent;
                                    $gapLength = $circumference - $dashLength;
                                @endphp
                                <circle 
                                    class="donut-segment" 
                                    cx="80" 
                                    cy="80" 
                                    r="{{ $radius }}"
                                    stroke="{{ $segment['color'] }}"
                                    stroke-dasharray="{{ $dashLength }} {{ $gapLength }}"
                                    stroke-dashoffset="{{ -$offset }}"
                                    onclick="showRequestsList('{{ $segment['name'] }}')"
                                />
                                @php $offset += $dashLength; @endphp
                            @endforeach
                        </svg>
                        <div class="donut-center">
                            <div class="donut-center-label">Total</div>
                            <div class="donut-center-value">{{ $total }}</div>
                        </div>
                    </div>
                    
                    <div class="status-table-container">
                        <table class="status-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Percent</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr onclick="showRequestsList('pending')">
                                    <td>
                                        <span class="status-indicator">
                                            <span class="status-dot pending"></span>
                                            <span class="status-name">Pending</span>
                                        </span>
                                    </td>
                                    <td class="status-percent">{{ number_format($pendingPercent, 1) }}%</td>
                                    <td class="status-count">{{ $pending }}</td>
                                </tr>
                                <tr onclick="showRequestsList('processing')">
                                    <td>
                                        <span class="status-indicator">
                                            <span class="status-dot processing"></span>
                                            <span class="status-name">Processing</span>
                                        </span>
                                    </td>
                                    <td class="status-percent">{{ number_format($processingPercent, 1) }}%</td>
                                    <td class="status-count">{{ $processing }}</td>
                                </tr>
                                <tr onclick="showRequestsList('completed')">
                                    <td>
                                        <span class="status-indicator">
                                            <span class="status-dot completed"></span>
                                            <span class="status-name">Completed</span>
                                        </span>
                                    </td>
                                    <td class="status-percent">{{ number_format($completedPercent, 1) }}%</td>
                                    <td class="status-count">{{ $completed }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="stats-grid">
            <div class="stats-card" onclick="showRequestsList('total')">
                <div class="stats-content">
                    <div class="stats-info">
                        <h3>{{ $stats['total'] }}</h3>
                        <p>Total Requests</p>
                        <div class="stats-trend">
                            <i class="fas fa-chart-line"></i>
                            <span>All requests</span>
                        </div>
                    </div>
                    <div class="stats-chart-container">
                        <div class="stats-icon total">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        @php $circumference = 2 * pi() * 50; @endphp
                        <svg class="stats-chart" viewBox="0 0 120 120">
                            <circle class="stats-chart-bg" cx="60" cy="60" r="50" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                            <circle class="stats-chart-progress total" cx="60" cy="60" r="50" fill="none" 
                                    stroke="url(#totalGradient)" stroke-width="8" 
                                    stroke-linecap="round" 
                                    stroke-dasharray="{{ $circumference }}" 
                                    stroke-dashoffset="{{ $circumference * (1 - 1) }}"
                                    transform="rotate(-90 60 60)"/>
                            <defs>
                                <linearGradient id="totalGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#14b8a6;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#5eead4;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="stats-card" onclick="showRequestsList('pending')">
                <div class="stats-content">
                    <div class="stats-info">
                        <h3>{{ $stats['pending'] }}</h3>
                        <p>Pending</p>
                        <div class="stats-trend">
                            <i class="fas fa-clock"></i>
                            <span>{{ number_format($pendingPercent, 1) }}% of total</span>
                        </div>
                    </div>
                    <div class="stats-chart-container">
                        <div class="stats-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <svg class="stats-chart" viewBox="0 0 120 120">
                            <circle class="stats-chart-bg" cx="60" cy="60" r="50" fill="none" stroke="#fef3c7" stroke-width="8"/>
                            <circle class="stats-chart-progress pending" cx="60" cy="60" r="50" fill="none" 
                                    stroke="url(#pendingGradient)" stroke-width="8" 
                                    stroke-linecap="round" 
                                    stroke-dasharray="{{ $circumference }}" 
                                    stroke-dashoffset="{{ $circumference * (1 - ($pendingPercent / 100)) }}"
                                    transform="rotate(-90 60 60)"/>
                            <defs>
                                <linearGradient id="pendingGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#d97706;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="stats-card" onclick="showRequestsList('processing')">
                <div class="stats-content">
                    <div class="stats-info">
                        <h3>{{ $stats['processing'] }}</h3>
                        <p>Processing</p>
                        <div class="stats-trend">
                            <i class="fas fa-cog"></i>
                            <span>{{ number_format($processingPercent, 1) }}% of total</span>
                        </div>
                    </div>
                    <div class="stats-chart-container">
                        <div class="stats-icon processing">
                            <i class="fas fa-cog"></i>
                        </div>
                        <svg class="stats-chart" viewBox="0 0 120 120">
                            <circle class="stats-chart-bg" cx="60" cy="60" r="50" fill="none" stroke="#dbeafe" stroke-width="8"/>
                            <circle class="stats-chart-progress processing" cx="60" cy="60" r="50" fill="none" 
                                    stroke="url(#processingGradient)" stroke-width="8" 
                                    stroke-linecap="round" 
                                    stroke-dasharray="{{ $circumference }}" 
                                    stroke-dashoffset="{{ $circumference * (1 - ($processingPercent / 100)) }}"
                                    transform="rotate(-90 60 60)"/>
                            <defs>
                                <linearGradient id="processingGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#2563eb;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="stats-card" onclick="showRequestsList('completed')">
                <div class="stats-content">
                    <div class="stats-info">
                        <h3>{{ $stats['completed'] }}</h3>
                        <p>Completed</p>
                        <div class="stats-trend">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ number_format($completedPercent, 1) }}% of total</span>
                        </div>
                    </div>
                    <div class="stats-chart-container">
                        <div class="stats-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <svg class="stats-chart" viewBox="0 0 120 120">
                            <circle class="stats-chart-bg" cx="60" cy="60" r="50" fill="none" stroke="#d1fae5" stroke-width="8"/>
                            <circle class="stats-chart-progress completed" cx="60" cy="60" r="50" fill="none" 
                                    stroke="url(#completedGradient)" stroke-width="8" 
                                    stroke-linecap="round" 
                                    stroke-dasharray="{{ $circumference }}" 
                                    stroke-dashoffset="{{ $circumference * (1 - ($completedPercent / 100)) }}"
                                    transform="rotate(-90 60 60)"/>
                            <defs>
                                <linearGradient id="completedGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        @php
            $queueEnabled = \App\Models\Setting::getQueueNumberSettingForDepartment($officer->department, false);
        @endphp

        @if($queueEnabled)
            <!-- Queue Table Section -->
            <div class="section-card" id="queue-table-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <h2 class="section-title">
                            Today's Queue
                        </h2>
                        <p class="section-subtitle">Current queue numbers for {{ now()->format('F d, Y') }}</p>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <a href="{{ route('officer.queue-display') }}" target="_blank" class="btn btn-primary"
                            style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                            <i class="fas fa-tv"></i>
                            View Display Screen
                        </a>
                        <button onclick="refreshQueueData()" class="btn btn-secondary"
                            style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-sync-alt"></i>
                            Refresh
                        </button>
                    </div>
                </div>

                <div id="queue-table-container">
                    <div class="empty-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <h4>Loading queue data...</h4>
                        <p>Please wait while we load today's queue information.</p>
                    </div>
                </div>
            </div>
        @endif

        @if($officer->department === 'Civil Registry Office')
            <!-- Quick Actions Section -->
            <div class="section-card">
                <h2 class="section-title">

                    Approval Requests
                </h2>
                <p class="section-subtitle" id="approvalPostingSubtitle">Review on going requests under 10-day posting period</p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px;">
                    <a href="{{ route('officer.approval-requests') }}" class="quick-action-card">
                        <div class="quick-action-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="quick-action-content">
                            <h3 id="approvalPostingCard">Tap to view 10 Days Posting Requests</h3>
                        </div>
                    </a>
                </div>
            </div>
        @endif

        @if($officer->department === 'Civil Registry Office')
            <!-- Civil Registry Office - Request Management -->
            <div class="section-card">
                <h2 class="section-title">
                    Request Management
                </h2>
                <p class="section-subtitle">Manage BRAP and Regular requests</p>

                <!-- Tab Navigation -->
                <div class="tab-navigation">
                    <button id="regular-tab-btn" onclick="switchTab('regular')" class="tab-btn active">
                        Delayed Birth Registration (Regular)
                    </button>
                    <button id="brap-tab-btn" onclick="switchTab('brap')" class="tab-btn">
                        Delayed Birth Registration under Birth Registration Assistance Project (BRAP)
                    </button>
                    <button id="other-tab-btn" onclick="switchTab('other')" class="tab-btn">
                        Other Civil Registry Services
                    </button>
                    <button id="appointments-tab-btn" onclick="switchTab('appointments')" class="tab-btn">
                        Appointments
                    </button>
                </div>

                <!-- Regular Requests Tab (Delayed Regular Non-BRAP) -->
                <div id="regular-tab" class="tab-content active">
                    <div id="regular-requests-table">
                        <!-- Regular requests will be loaded here via AJAX -->
                    </div>
                </div>

                <!-- BRAP Requests Tab (Delayed BRAP) -->
                <div id="brap-tab" class="tab-content">
                    <div id="brap-requests-table">
                        <!-- BRAP requests will be loaded here via AJAX -->
                    </div>
                </div>

                <!-- Other Civil Registry Services Tab -->
                <div id="other-tab" class="tab-content">
                    <div id="other-requests-table">
                        <!-- Other civil registry requests will be loaded here via AJAX -->
                    </div>
                </div>

                <!-- Appointments Tab -->
                <div id="appointments-tab" class="tab-content">

                    <div id="appointments-content">
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt"></i>
                            <h4>Loading Appointments...</h4>
                            <p>Please wait while we load your appointments.</p>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <!-- Other Departments - Show Department Requests Table -->
            <div class="section-card">
                <h2 class="section-title">
                    Department Requests
                </h2>

                @if(session('status'))
                    <div
                        style="background: #ecfdf5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #10b981;">
                        {{ session('status') }}
                    </div>
                @endif

                @if($allRequests->count() > 0)
                    <div class="table-container">
                        <table class="table">
                            <thead class="table-header">
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Service Type</th>
                                    <th>Client Name</th>
                                    <th>Status</th>
                                    <th>Date Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allRequests as $request)
                                    <tr class="table-row">
                                        <td style="font-family: monospace; font-size: 14px; font-weight: 500;">
                                            {{ $request->tracking_number }}
                                        </td>
                                        <td style="font-size: 14px; font-weight: 500;">
                                            {{ $request->service_type ?? $request->service_name ?? 'N/A' }}
                                        </td>
                                        <td style="font-size: 14px; font-weight: 500;">{{ $request->full_name }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'inquiry' => 'inquiry',
                                                    'accepted' => 'accepted',
                                                    'processing' => 'processing',
                                                    'rejected' => 'rejected',
                                                    'registered' => 'registered',
                                                    'psa_processing' => 'psa_processing',
                                                    'posting' => 'posting',
                                                    'ready_for_approval' => 'ready_for_approval',
                                                    'incomplete_requirements' => 'incomplete_requirements',
                                                    'checklist_given' => 'checklist_given',
                                                    'to_be_encoded_brap' => 'to_be_encoded_brap'
                                                ];
                                                $statusClass = $statusColors[$request->status] ?? 'inquiry';

                                                // Get latest status update time
                                                $latestStatusUpdate = $request->statusUpdates()->latest()->first();
                                                $statusUpdateTime = $latestStatusUpdate
                                                    ? $latestStatusUpdate->created_at->format('M d, Y h:i A')
                                                    : ($request->processed_at
                                                        ? $request->processed_at->format('M d, Y h:i A')
                                                        : $request->created_at->format('M d, Y h:i A'));
                                            @endphp
                                            <div>
                                                <span class="status-badge {{ $statusClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                </span>

                                            </div>
                                        </td>
                                        <td style="color: var(--gray-600); font-size: 14px;">
                                            {{ $request->created_at->format('M d, Y h:i A') }}
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <div style="display: flex; gap: 12px;">
                                                <a href="{{ route('officer.requests.details', $request->tracking_number) }}"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                    View Details
                                                </a>
                                                <button
                                                    onclick="openStatusModal('{{ $request->tracking_number }}', '{{ $request->status }}', '{{ addslashes($request->admin_notes ?? '') }}', 'other_service')"
                                                    class="btn btn-secondary">
                                                    <i class="fas fa-edit"></i>
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
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h4>No Requests Found</h4>
                        <p>No requests found for your department.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
    </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-edit"></i>
                    Update Request Status
                </h2>
                <button onclick="closeStatusModal()" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Status Update Form -->
            <form id="statusForm">
                @csrf
                <input type="hidden" id="modalTrackingNumber" name="tracking_number">

                <div class="form-group">
                    <label class="form-label">Tracking Number</label>
                    <input type="text" id="modalTrackingNumberDisplay" readonly class="form-input"
                        style="background: var(--gray-100);">
                </div>

                <div class="form-group">
                    <label class="form-label">New Status</label>
                    <select id="modalStatus" name="status" required class="form-select">
                        <!-- Options will be populated dynamically based on request type -->
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status Date</label>
                    <input type="date" id="modalStatusDate" name="status_date" class="form-input"
                        value="{{ now()->format('Y-m-d') }}" readonly>
                </div>

                <!-- Incomplete Requirements Section (shown when status is incomplete_requirements) -->
                <div id="incompleteRequirementsSection" class="form-group" style="display: none;">
                    <input type="hidden" id="incompleteReqType" value="">
                    <label class="form-label">Select Missing Requirements</label>
                    <p id="incompleteReqTypeLabel"
                        style="font-size: 12px; color: var(--gray-500); margin-bottom: 12px;">For Delayed Birth
                        Registration</p>

                    <!-- Search/Filter Box -->
                    <div style="margin-bottom: 12px;">
                        <input type="text" id="incompleteRequirementsSearch" placeholder="🔍 Search requirements..."
                            style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 14px;"
                            oninput="filterRequirements('incompleteRequirements')">
                    </div>

                    <!-- BRAP Checklist Container -->
                    <div id="incompleteRequirementsListBrap"
                        style="background: var(--gray-50); padding: 12px; border-radius: 8px; border: 1px solid var(--gray-200); max-height: 350px; overflow-y: auto; display: none;">

                        <!-- Category: Forms & Documents -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-alt" style="margin-right: 6px;"></i> Forms & Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Municipal Form No. 102/Certificate of Live Birth (4 Copies)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Municipal Form No. 102/Certificate of Live Birth (4
                                    Copies)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Municipal Form No. 102 Attachment for Islam Religion (4 Copies)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Municipal Form No. 102 Attachment for Islam Religion (4
                                    Copies)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Civil Registry Form 1B (Birth Not Available) or Form 1C (Birth Destroyed)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Civil Registry Form 1B (Birth Not Available) or Form 1C
                                    (Birth Destroyed)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Negative Omnibus Certification/Negative Certification from PSA"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Negative Omnibus Certification/Negative Certification
                                    from PSA</span>
                            </label>
                        </div>

                        <!-- Category: Affidavits -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-stamp" style="margin-right: 6px;"></i> Affidavits & Certifications
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Accomplished Affidavit for Delayed Registration of Birth (at the back of COLB)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Accomplished Affidavit for Delayed Registration of Birth
                                    (at the back of COLB)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit of Two (2) Disinterested Persons"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit of Two (2) Disinterested Persons</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Certificate of Indigency (Issued by Punong Barangay)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Certificate of Indigency (Issued by Punong
                                    Barangay)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit of Admission of Paternity (Back of COLB or PHI)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit of Admission of Paternity (Back of COLB or
                                    PHI)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit to Use Surname of Father (AUSF) under RA 9255"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit to Use Surname of Father (AUSF) under RA
                                    9255</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Sworn Attestation (for child aged 7-17 years old)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Sworn Attestation (for child aged 7-17 years old)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit/Sworn Statement (present whereabouts of mother)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit/Sworn Statement (present whereabouts of
                                    mother)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit of Acknowledgement (non-marital child born before Aug 3, 1988)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit of Acknowledgement (non-marital child born
                                    before Aug 3, 1988)</span>
                            </label>
                        </div>

                        <!-- Category: Proof of Identity -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-id-card" style="margin-right: 6px;"></i> Proof of Identity
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]" value="Barangay Certification"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Barangay Certification</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Medical Record/Immunization Record"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Medical Record/Immunization Record</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Baptismal Certificate (Original from Church)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Baptismal Certificate (Original from Church)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="School Record (Certified Photocopy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">School Record (Certified Photocopy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Voter's Record/Voter's Certification (Original/Certified True Copy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Voter's Record/Voter's Certification (Original/Certified
                                    True Copy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Member's Data Record (PhilHealth, SSS, GSIS, Senior Citizen)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Member's Data Record (PhilHealth, SSS, GSIS, Senior
                                    Citizen)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]" value="Any Government Issued ID"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Any Government Issued ID</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="National ID (with 2x2 Picture)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">National ID (with 2x2 Picture)</span>
                            </label>
                        </div>

                        <!-- Category: Personal Appearance Requirements -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-user-check" style="margin-right: 6px;"></i> Personal Appearance
                                Requirements
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Personal Appearance of Document Owner (18 years and above)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Personal Appearance of Document Owner (18 years and
                                    above)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Personal Appearance of Parents (for Marital Minor)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Personal Appearance of Parents (for Marital Minor)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Personal Appearance of Mother (for Non-marital Minor)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Personal Appearance of Mother (for Non-marital
                                    Minor)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Personal Appearance of Guardian/Grandparents/Oldest Sibling"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Personal Appearance of Guardian/Grandparents/Oldest
                                    Sibling</span>
                            </label>
                        </div>

                        <!-- Category: Authorization Documents -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-signature" style="margin-right: 6px;"></i> Authorization Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Special Power of Attorney (SPA) or Authorization Letter"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Special Power of Attorney (SPA) or Authorization
                                    Letter</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Valid ID of Authorizing Person and Person Authorized"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Valid ID of Authorizing Person and Person
                                    Authorized</span>
                            </label>
                        </div>

                        <!-- Category: Other -->
                        <div class="req-category">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-folder-open" style="margin-right: 6px;"></i> Other Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Other Supporting Documents (specify in remarks)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Other Supporting Documents (specify in remarks)</span>
                            </label>
                        </div>
                    </div>
                    <!-- END BRAP Checklist Container -->

                    <!-- REGULAR Checklist Container -->
                    <div id="incompleteRequirementsListRegular"
                        style="background: var(--gray-50); padding: 12px; border-radius: 8px; border: 1px solid var(--gray-200); max-height: 350px; overflow-y: auto; display: none;">

                        <!-- Category: Forms & Documents -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-alt" style="margin-right: 6px;"></i> Forms & Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Municipal Form No. 102/Certificate of Live Birth (4 Copies)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Municipal Form No. 102/Certificate of Live Birth (4
                                    Copies)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Municipal Form No. 102 Attachment for Islam Religion (4 Copies)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Municipal Form No. 102 Attachment for Islam Religion (4
                                    Copies)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Civil Registry Form 1B (Birth Not Available) or Form 1C (Birth Destroyed)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Civil Registry Form 1B (Birth Not Available) or Form 1C
                                    (Birth Destroyed)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Negative Certification from PSA"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Negative Certification from PSA</span>
                            </label>
                        </div>

                        <!-- Category: Affidavits & Certifications -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-stamp" style="margin-right: 6px;"></i> Affidavits & Certifications
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Accomplished Affidavit for Delayed Registration of Birth (at the back of COLB)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Accomplished Affidavit for Delayed Registration of Birth
                                    (at the back of COLB)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit of Two (2) Disinterested Persons"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit of Two (2) Disinterested Persons</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Certificate of Residency (issued by Barangay Captain)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Certificate of Residency (issued by Barangay
                                    Captain)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Certificate of Marriage, if married (Certified True Copy/PSA Copy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Certificate of Marriage, if married (Certified True
                                    Copy/PSA Copy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Death Certificate, if document owner is deceased (Certified True Copy/PSA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Death Certificate, if document owner is deceased
                                    (Certified True Copy/PSA)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit of Undertaking (if document owner is deceased)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit of Undertaking (if document owner is
                                    deceased)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit of Admission of Paternity (Back of COLB/PHI)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit of Admission of Paternity (Back of
                                    COLB/PHI)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit to Use Surname of Father (AUSF) under RA 9255"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit to Use Surname of Father (AUSF) under RA
                                    9255</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Sworn Attestation (for child aged 7-17 years old)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Sworn Attestation (for child aged 7-17 years old)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit/Sworn Statement (present whereabouts of mother)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit/Sworn Statement (present whereabouts of
                                    mother)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Affidavit of Acknowledgement (non-marital child born before Aug 3, 1988)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Affidavit of Acknowledgement (non-marital child born
                                    before Aug 3, 1988)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Out of Town Registration Certification (PSA Form No.1, Series of 2024)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Out of Town Registration Certification (PSA Form No.1,
                                    Series of 2024)</span>
                            </label>
                        </div>

                        <!-- Category: Documentary Evidence (Document Owner) -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-id-card" style="margin-right: 6px;"></i> Documentary Evidence (Document
                                Owner)
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Baptismal Certificate (Original from Church)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Baptismal Certificate (Original from Church)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="School Record (Certified Photocopy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">School Record (Certified Photocopy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Voter's Record/Voter's Certification (Original/Certified True Copy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Voter's Record/Voter's Certification (Original/Certified
                                    True Copy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Medical Record/Immunization Record"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Medical Record/Immunization Record</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Member's Data Record (PhilHealth, SSS, GSIS, Senior Citizen)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Member's Data Record (PhilHealth, SSS, GSIS, Senior
                                    Citizen)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Barangay Captain's Certification"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Barangay Captain's Certification</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="National ID of Document Owner (for 1 year old and above)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">National ID of Document Owner (for 1 year old and
                                    above)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Any Government Issued ID of Document Owner and Applicant"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Any Government Issued ID of Document Owner and
                                    Applicant</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Updated Community Tax Certificate (CEDULA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Updated Community Tax Certificate (CEDULA)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Unedited 2x2 Photo (white background, within 3 months) - 4 copies"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Unedited 2x2 Photo (white background, within 3 months) -
                                    4 copies</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Birth Certificate of Siblings"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Birth Certificate of Siblings</span>
                            </label>
                        </div>

                        <!-- Category: Parents' Documentary Evidence -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-users" style="margin-right: 6px;"></i> Parents' Documentary Evidence
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Certificate of Live Birth of Parents (Certified True Copy/PSA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Certificate of Live Birth of Parents (Certified True
                                    Copy/PSA)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Any Government-issued ID of Parents (National ID, Driver's License, Passport, etc.)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Any Government-issued ID of Parents (National ID,
                                    Driver's License, Passport, etc.)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Certificate of Marriage of Parents (Certified True Copy/PSA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Certificate of Marriage of Parents (Certified True
                                    Copy/PSA)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Certificate of Death of Parents (Certified True Copy/PSA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Certificate of Death of Parents (Certified True
                                    Copy/PSA)</span>
                            </label>
                        </div>

                        <!-- Category: Personal Appearance Requirements -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-user-check" style="margin-right: 6px;"></i> Personal Appearance
                                Requirements
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Personal Appearance of Document Owner (18 years and above)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Personal Appearance of Document Owner (18 years and
                                    above)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Personal Appearance of Parents (for Marital Minor)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Personal Appearance of Parents (for Marital Minor)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Personal Appearance of Mother (for Non-marital Minor)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Personal Appearance of Mother (for Non-marital
                                    Minor)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Personal Appearance of Guardian/Grandparents/Oldest Sibling (over 21 years)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Personal Appearance of Guardian/Grandparents/Oldest
                                    Sibling (over 21 years)</span>
                            </label>
                        </div>

                        <!-- Category: Foreign Parent Requirements -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-globe" style="margin-right: 6px;"></i> Foreign Parent Requirements (if
                                applicable)
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Certificate of Marriage of Parents (marital child with foreign parent)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Certificate of Marriage of Parents (marital child with
                                    foreign parent)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Birth Certificate of Foreign Parent"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Birth Certificate of Foreign Parent</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Valid Passport or BI Clearance Certificate or ACR I-Card (foreign parent)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Valid Passport or BI Clearance Certificate or ACR I-Card
                                    (foreign parent)</span>
                            </label>
                        </div>

                        <!-- Category: Authorization Documents -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-signature" style="margin-right: 6px;"></i> Authorization Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Special Power of Attorney (SPA) or Authorization Letter"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Special Power of Attorney (SPA) or Authorization
                                    Letter</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Valid ID of Authorizing Person and Person Authorized"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Valid ID of Authorizing Person and Person
                                    Authorized</span>
                            </label>
                        </div>

                        <!-- Category: Other -->
                        <div class="req-category">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-folder-open" style="margin-right: 6px;"></i> Other Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Other Supporting Documents (specify in remarks)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Other Supporting Documents (specify in remarks)</span>
                            </label>
                        </div>
                    </div>
                    <!-- END REGULAR Checklist Container -->

                    <!-- OTHER SERVICES Checklist Container -->
                    <div id="incompleteRequirementsListOther"
                        style="background: var(--gray-50); padding: 12px; border-radius: 8px; border: 1px solid var(--gray-200); max-height: 350px; overflow-y: auto; display: none;">

                        <!-- Category: Identification Documents -->
                        <div class="req-category">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-id-card" style="margin-right: 6px;"></i> Identification Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Valid Government-Issued ID (Original and Photocopy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Valid Government-Issued ID (Original and
                                    Photocopy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Barangay Clearance (Original)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Barangay Clearance (Original)</span>
                            </label>
                        </div>

                        <!-- Category: Application Forms -->
                        <div class="req-category">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-alt" style="margin-right: 6px;"></i> Application Forms
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Filled-out Application Form (Signed)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Filled-out Application Form (Signed)</span>
                            </label>
                        </div>

                        <!-- Category: Supporting Documents -->
                        <div class="req-category">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-folder" style="margin-right: 6px;"></i> Supporting Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Proof of Address (Utility Bill, Bank Statement, etc.)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Proof of Address (Utility Bill, Bank Statement,
                                    etc.)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]" value="Proof of Payment or Receipt"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Proof of Payment or Receipt</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Recent 2x2 ID Photo (2 copies)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Recent 2x2 ID Photo (2 copies)</span>
                            </label>
                        </div>

                        <!-- Category: Service-Specific Documents -->
                        <div class="req-category">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-medical" style="margin-right: 6px;"></i> Service-Specific
                                Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Medical Certificate or Health Clearance (if required)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Medical Certificate or Health Clearance (if
                                    required)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Business Permit or License (if applicable)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Business Permit or License (if applicable)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Authorization Letter (if applying on behalf of someone)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Authorization Letter (if applying on behalf of
                                    someone)</span>
                            </label>
                        </div>

                        <!-- Category: Other -->
                        <div class="req-category">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-folder-open" style="margin-right: 6px;"></i> Other Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="missing_requirements[]"
                                    value="Other Supporting Documents (specify in remarks)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateIncompleteRequirementsPreview()">
                                <span style="font-size: 13px;">Other Supporting Documents (specify in remarks)</span>
                            </label>
                        </div>
                    </div>
                    <!-- END OTHER SERVICES Checklist Container -->

                    <div class="form-group" style="margin-top: 12px;">
                        <label class="form-label">Additional Notes / Remarks (Optional)</label>
                        <textarea id="incompleteRequirementsRemarks" name="incomplete_requirements_remarks" rows="3"
                            class="form-textarea" placeholder="Add any additional notes or remarks..."
                            oninput="updateIncompleteRequirementsPreview()"></textarea>
                    </div>

                    <div class="form-group">
                        <label
                            style="display: flex; align-items: center; cursor: pointer; padding: 12px; background: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                            <input type="checkbox" id="sendIncompleteRequirementsSMS"
                                name="send_incomplete_requirements_sms" value="1"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateIncompleteRequirementsPreview()">
                            <span style="font-size: 14px; font-weight: 500;">Send Missing Requirements via SMS to
                                Client</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SMS Preview</label>
                        <div id="incompleteRequirementsPreview"
                            style="background: var(--gray-50); padding: 16px; border-radius: 8px; border: 1px solid var(--gray-200); min-height: 80px; font-size: 13px; line-height: 1.6; color: var(--gray-700); white-space: pre-wrap;">
                            Select missing requirements and enable SMS to see preview...
                        </div>
                    </div>
                </div>

                <!-- For Compliance Section (shown when status is for_compliance) -->
                <div id="forComplianceSection" class="form-group" style="display: none;">
                    <input type="hidden" id="complianceReqType" value="">
                    <label class="form-label">Select Compliance Requirements</label>
                    <p id="complianceReqTypeLabel"
                        style="font-size: 12px; color: var(--gray-500); margin-bottom: 12px;">For Delayed Birth
                        Registration - Compliance Requirements</p>

                    <!-- Search/Filter Box -->
                    <div style="margin-bottom: 12px;">
                        <input type="text" id="forComplianceSearch" placeholder="🔍 Search requirements..."
                            style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 14px;"
                            oninput="filterRequirements('forCompliance')">
                    </div>

                    <!-- BRAP Compliance Checklist Container -->
                    <div id="forComplianceListBrap"
                        style="background: var(--gray-50); padding: 12px; border-radius: 8px; border: 1px solid var(--gray-200); max-height: 350px; overflow-y: auto; display: none;">

                        <!-- Category: Forms & Documents -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-alt" style="margin-right: 6px;"></i> Forms & Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Municipal Form No. 102/Certificate of Live Birth (4 Copies)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Municipal Form No. 102/Certificate of Live Birth (4
                                    Copies)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Municipal Form No. 102 Attachment for Islam Religion (4 Copies)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Municipal Form No. 102 Attachment for Islam Religion (4
                                    Copies)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Civil Registry Form 1B (Birth Not Available) or Form 1C (Birth Destroyed)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Civil Registry Form 1B (Birth Not Available) or Form 1C
                                    (Birth Destroyed)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Negative Omnibus Certification/Negative Certification from PSA"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Negative Omnibus Certification/Negative Certification
                                    from PSA</span>
                            </label>
                        </div>

                        <!-- Category: Affidavits -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-stamp" style="margin-right: 6px;"></i> Affidavits & Certifications
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Accomplished Affidavit for Delayed Registration of Birth (at the back of COLB)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Accomplished Affidavit for Delayed Registration of Birth
                                    (at the back of COLB)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit of Two (2) Disinterested Persons"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit of Two (2) Disinterested Persons</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Certificate of Indigency (Issued by Punong Barangay)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Certificate of Indigency (Issued by Punong
                                    Barangay)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit of Admission of Paternity (Back of COLB or PHI)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit of Admission of Paternity (Back of COLB or
                                    PHI)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit to Use Surname of Father (AUSF) under RA 9255"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit to Use Surname of Father (AUSF) under RA
                                    9255</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Sworn Attestation (for child aged 7-17 years old)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Sworn Attestation (for child aged 7-17 years old)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit/Sworn Statement (present whereabouts of mother)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit/Sworn Statement (present whereabouts of
                                    mother)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit of Acknowledgement (non-marital child born before Aug 3, 1988)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit of Acknowledgement (non-marital child born
                                    before Aug 3, 1988)</span>
                            </label>
                        </div>

                        <!-- Category: Proof of Identity -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-id-card" style="margin-right: 6px;"></i> Proof of Identity
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]" value="Barangay Certification"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Barangay Certification</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Medical Record/Immunization Record"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Medical Record/Immunization Record</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Baptismal Certificate (Original from Church)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Baptismal Certificate (Original from Church)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="School Record (Certified Photocopy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">School Record (Certified Photocopy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Voter's Record/Voter's Certification (Original/Certified True Copy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Voter's Record/Voter's Certification (Original/Certified
                                    True Copy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Member's Data Record (PhilHealth, SSS, GSIS, Senior Citizen)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Member's Data Record (PhilHealth, SSS, GSIS, Senior
                                    Citizen)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]" value="Any Government Issued ID"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Any Government Issued ID</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="National ID (with 2x2 Picture)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">National ID (with 2x2 Picture)</span>
                            </label>
                        </div>

                        <!-- Category: Personal Appearance Requirements -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-user-check" style="margin-right: 6px;"></i> Personal Appearance
                                Requirements
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Personal Appearance of Document Owner (18 years and above)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Personal Appearance of Document Owner (18 years and
                                    above)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Personal Appearance of Parents (for Marital Minor)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Personal Appearance of Parents (for Marital Minor)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Personal Appearance of Mother (for Non-marital Minor)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Personal Appearance of Mother (for Non-marital
                                    Minor)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Personal Appearance of Guardian/Grandparents/Oldest Sibling"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Personal Appearance of Guardian/Grandparents/Oldest
                                    Sibling</span>
                            </label>
                        </div>

                        <!-- Category: Authorization Documents -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-signature" style="margin-right: 6px;"></i> Authorization Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Special Power of Attorney (SPA) or Authorization Letter"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Special Power of Attorney (SPA) or Authorization
                                    Letter</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Valid ID of Authorizing Person and Person Authorized"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Valid ID of Authorizing Person and Person
                                    Authorized</span>
                            </label>
                        </div>

                        <!-- Category: Other -->
                        <div class="req-category">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-folder-open" style="margin-right: 6px;"></i> Other Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Other Supporting Documents (specify in remarks)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Other Supporting Documents (specify in remarks)</span>
                            </label>
                        </div>
                    </div>
                    <!-- END BRAP Compliance Checklist Container -->

                    <!-- REGULAR Compliance Checklist Container -->
                    <div id="forComplianceListRegular"
                        style="background: var(--gray-50); padding: 12px; border-radius: 8px; border: 1px solid var(--gray-200); max-height: 350px; overflow-y: auto; display: none;">

                        <!-- Category: Forms & Documents -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-alt" style="margin-right: 6px;"></i> Forms & Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Municipal Form No. 102/Certificate of Live Birth (4 Copies)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Municipal Form No. 102/Certificate of Live Birth (4
                                    Copies)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Municipal Form No. 102 Attachment for Islam Religion (4 Copies)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Municipal Form No. 102 Attachment for Islam Religion (4
                                    Copies)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Civil Registry Form 1B (Birth Not Available) or Form 1C (Birth Destroyed)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Civil Registry Form 1B (Birth Not Available) or Form 1C
                                    (Birth Destroyed)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Negative Certification from PSA"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Negative Certification from PSA</span>
                            </label>
                        </div>

                        <!-- Category: Affidavits & Certifications -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-stamp" style="margin-right: 6px;"></i> Affidavits & Certifications
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Accomplished Affidavit for Delayed Registration of Birth (at the back of COLB)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Accomplished Affidavit for Delayed Registration of Birth
                                    (at the back of COLB)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit of Two (2) Disinterested Persons"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit of Two (2) Disinterested Persons</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Certificate of Residency (issued by Barangay Captain)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Certificate of Residency (issued by Barangay
                                    Captain)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Certificate of Marriage, if married (Certified True Copy/PSA Copy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Certificate of Marriage, if married (Certified True
                                    Copy/PSA Copy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Death Certificate, if document owner is deceased (Certified True Copy/PSA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Death Certificate, if document owner is deceased
                                    (Certified True Copy/PSA)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit of Undertaking (if document owner is deceased)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit of Undertaking (if document owner is
                                    deceased)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit of Admission of Paternity (Back of COLB/PHI)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit of Admission of Paternity (Back of
                                    COLB/PHI)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit to Use Surname of Father (AUSF) under RA 9255"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit to Use Surname of Father (AUSF) under RA
                                    9255</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Sworn Attestation (for child aged 7-17 years old)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Sworn Attestation (for child aged 7-17 years old)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit/Sworn Statement (present whereabouts of mother)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit/Sworn Statement (present whereabouts of
                                    mother)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Affidavit of Acknowledgement (non-marital child born before Aug 3, 1988)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Affidavit of Acknowledgement (non-marital child born
                                    before Aug 3, 1988)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Out of Town Registration Certification (PSA Form No.1, Series of 2024)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Out of Town Registration Certification (PSA Form No.1,
                                    Series of 2024)</span>
                            </label>
                        </div>

                        <!-- Category: Documentary Evidence (Document Owner) -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-id-card" style="margin-right: 6px;"></i> Documentary Evidence (Document
                                Owner)
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Baptismal Certificate (Original from Church)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Baptismal Certificate (Original from Church)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="School Record (Certified Photocopy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">School Record (Certified Photocopy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Voter's Record/Voter's Certification (Original/Certified True Copy)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Voter's Record/Voter's Certification (Original/Certified
                                    True Copy)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Medical Record/Immunization Record"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Medical Record/Immunization Record</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Member's Data Record (PhilHealth, SSS, GSIS, Senior Citizen)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Member's Data Record (PhilHealth, SSS, GSIS, Senior
                                    Citizen)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Barangay Captain's Certification"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Barangay Captain's Certification</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="National ID of Document Owner (for 1 year old and above)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">National ID of Document Owner (for 1 year old and
                                    above)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Any Government Issued ID of Document Owner and Applicant"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Any Government Issued ID of Document Owner and
                                    Applicant</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Updated Community Tax Certificate (CEDULA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Updated Community Tax Certificate (CEDULA)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Unedited 2x2 Photo (white background, within 3 months) - 4 copies"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Unedited 2x2 Photo (white background, within 3 months) -
                                    4 copies</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Birth Certificate of Siblings"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Birth Certificate of Siblings</span>
                            </label>
                        </div>

                        <!-- Category: Parents' Documentary Evidence -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-users" style="margin-right: 6px;"></i> Parents' Documentary Evidence
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Certificate of Live Birth of Parents (Certified True Copy/PSA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Certificate of Live Birth of Parents (Certified True
                                    Copy/PSA)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Any Government-issued ID of Parents (National ID, Driver's License, Passport, etc.)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Any Government-issued ID of Parents (National ID,
                                    Driver's License, Passport, etc.)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Certificate of Marriage of Parents (Certified True Copy/PSA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Certificate of Marriage of Parents (Certified True
                                    Copy/PSA)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Certificate of Death of Parents (Certified True Copy/PSA)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Certificate of Death of Parents (Certified True
                                    Copy/PSA)</span>
                            </label>
                        </div>

                        <!-- Category: Personal Appearance Requirements -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-user-check" style="margin-right: 6px;"></i> Personal Appearance
                                Requirements
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Personal Appearance of Document Owner (18 years and above)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Personal Appearance of Document Owner (18 years and
                                    above)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Personal Appearance of Parents (for Marital Minor)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Personal Appearance of Parents (for Marital Minor)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Personal Appearance of Mother (for Non-marital Minor)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Personal Appearance of Mother (for Non-marital
                                    Minor)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Personal Appearance of Guardian/Grandparents/Oldest Sibling (over 21 years)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Personal Appearance of Guardian/Grandparents/Oldest
                                    Sibling (over 21 years)</span>
                            </label>
                        </div>

                        <!-- Category: Foreign Parent Requirements -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-globe" style="margin-right: 6px;"></i> Foreign Parent Requirements (if
                                applicable)
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Certificate of Marriage of Parents (marital child with foreign parent)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Certificate of Marriage of Parents (marital child with
                                    foreign parent)</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Birth Certificate of Foreign Parent"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Birth Certificate of Foreign Parent</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Valid Passport or BI Clearance Certificate or ACR I-Card (foreign parent)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Valid Passport or BI Clearance Certificate or ACR I-Card
                                    (foreign parent)</span>
                            </label>
                        </div>

                        <!-- Category: Authorization Documents -->
                        <div class="req-category" style="margin-bottom: 12px;">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-file-signature" style="margin-right: 6px;"></i> Authorization Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Special Power of Attorney (SPA) or Authorization Letter"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Special Power of Attorney (SPA) or Authorization
                                    Letter</span>
                            </label>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Valid ID of Authorizing Person and Person Authorized"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Valid ID of Authorizing Person and Person
                                    Authorized</span>
                            </label>
                        </div>

                        <!-- Category: Other -->
                        <div class="req-category">
                            <div
                                style="font-weight: 600; color: var(--teal-primary); font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 8px;">
                                <i class="fas fa-folder-open" style="margin-right: 6px;"></i> Other Documents
                            </div>
                            <label class="req-item"
                                style="display: flex; align-items: flex-start; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='var(--gray-100)'"
                                onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="compliance_requirements[]"
                                    value="Other Supporting Documents (specify in remarks)"
                                    style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;"
                                    onchange="updateForCompliancePreview()">
                                <span style="font-size: 13px;">Other Supporting Documents (specify in remarks)</span>
                            </label>
                        </div>
                    </div>
                    <!-- END REGULAR Compliance Checklist Container -->

                    <div class="form-group" style="margin-top: 12px;">
                        <label class="form-label">Additional Notes / Remarks (Optional)</label>
                        <textarea id="forComplianceRemarks" name="compliance_remarks" rows="3" class="form-textarea"
                            placeholder="Add any additional notes or remarks..."
                            oninput="updateForCompliancePreview()"></textarea>
                    </div>

                    <div class="form-group">
                        <label
                            style="display: flex; align-items: center; cursor: pointer; padding: 12px; background: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                            <input type="checkbox" id="sendForComplianceSMS" name="send_compliance_sms" value="1"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateForCompliancePreview()">
                            <span style="font-size: 14px; font-weight: 500;">Send Compliance Requirements via SMS to
                                Client</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SMS Preview</label>
                        <div id="forCompliancePreview"
                            style="background: var(--gray-50); padding: 16px; border-radius: 8px; border: 1px solid var(--gray-200); min-height: 80px; font-size: 13px; line-height: 1.6; color: var(--gray-700); white-space: pre-wrap;">
                            Select compliance requirements and enable SMS to see preview...
                        </div>
                    </div>
                </div>

                <!-- General Remarks Section (hidden when using specific sections) -->
                <div id="generalRemarksSection" class="form-group">
                    <label class="form-label">Remarks (Optional)</label>
                    <textarea id="modalNotes" name="remarks" rows="4" class="form-textarea"
                        placeholder="Add any remarks about this request..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="closeStatusModal()" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Requests List Modal -->
    <div id="requestsListModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 1000px; max-height: 80%; overflow: hidden;">
            <div class="modal-header">
                <h2 id="modalTitle" class="modal-title">
                    <i class="fas fa-list"></i>
                    Requests List
                </h2>
                <button onclick="closeRequestsListModal()" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="requestsListContent" style="max-height: 60vh; overflow-y: auto;">
                <!-- Requests will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Checklist Modal -->
    <div id="checklistModal" class="modal">
        <div class="modal-content" style="max-width: 700px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-clipboard-list"></i>
                    Checklist of Requirements
                </h2>
                <button onclick="closeChecklistModal()" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="checklistForm">
                @csrf
                <input type="hidden" id="checklistTrackingNumber" name="tracking_number">
                <input type="hidden" id="checklistStatus" name="status" value="checklist_given">
                <input type="hidden" id="checklistStatusDate" name="status_date">
                <input type="hidden" id="checklistRemarks" name="remarks">

                <div class="form-group">
                    <label class="form-label">Select Required Documents</label>
                    <div
                        style="background: var(--gray-50); padding: 16px; border-radius: 8px; border: 1px solid var(--gray-200);">
                        <label
                            style="display: flex; align-items: center; padding: 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                            onmouseover="this.style.background='var(--gray-100)'"
                            onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="checklist_items[]" value="Valid ID (any government-issued ID)"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateChecklistPreview()">
                            <span style="font-size: 14px; font-weight: 500;">Valid ID (any government-issued ID)</span>
                        </label>
                        <label
                            style="display: flex; align-items: center; padding: 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                            onmouseover="this.style.background='var(--gray-100)'"
                            onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="checklist_items[]" value="Barangay Certification"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateChecklistPreview()">
                            <span style="font-size: 14px; font-weight: 500;">Barangay Certification</span>
                        </label>
                        <label
                            style="display: flex; align-items: center; padding: 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                            onmouseover="this.style.background='var(--gray-100)'"
                            onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="checklist_items[]" value="Birth Certificate"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateChecklistPreview()">
                            <span style="font-size: 14px; font-weight: 500;">Birth Certificate</span>
                        </label>
                        <label
                            style="display: flex; align-items: center; padding: 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                            onmouseover="this.style.background='var(--gray-100)'"
                            onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="checklist_items[]" value="Marriage Certificate"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateChecklistPreview()">
                            <span style="font-size: 14px; font-weight: 500;">Marriage Certificate</span>
                        </label>
                        <label
                            style="display: flex; align-items: center; padding: 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                            onmouseover="this.style.background='var(--gray-100)'"
                            onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="checklist_items[]" value="Affidavit of Late Registration"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateChecklistPreview()">
                            <span style="font-size: 14px; font-weight: 500;">Affidavit of Late Registration</span>
                        </label>
                        <label
                            style="display: flex; align-items: center; padding: 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                            onmouseover="this.style.background='var(--gray-100)'"
                            onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="checklist_items[]" value="Certificate of No Record"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateChecklistPreview()">
                            <span style="font-size: 14px; font-weight: 500;">Certificate of No Record</span>
                        </label>
                        <label
                            style="display: flex; align-items: center; padding: 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                            onmouseover="this.style.background='var(--gray-100)'"
                            onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="checklist_items[]" value="Parent's IDs"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateChecklistPreview()">
                            <span style="font-size: 14px; font-weight: 500;">Parent's IDs</span>
                        </label>
                        <label
                            style="display: flex; align-items: center; padding: 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                            onmouseover="this.style.background='var(--gray-100)'"
                            onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="checklist_items[]" value="Supporting Documents"
                                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                                onchange="updateChecklistPreview()">
                            <span style="font-size: 14px; font-weight: 500;">Supporting Documents</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Additional Notes / Remarks (Optional)</label>
                    <textarea id="checklistRemarksText" name="checklist_remarks" rows="3" class="form-textarea"
                        placeholder="Add any additional notes or remarks..."
                        onchange="updateChecklistPreview()"></textarea>
                </div>

                <div class="form-group">
                    <label
                        style="display: flex; align-items: center; cursor: pointer; padding: 12px; background: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                        <input type="checkbox" id="sendChecklistSMS" name="send_checklist_sms" value="1"
                            style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                            onchange="updateChecklistPreview()">
                        <span style="font-size: 14px; font-weight: 500;">Send Checklist via SMS to Client</span>
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label">SMS Preview</label>
                    <div id="smsPreview"
                        style="background: var(--gray-50); padding: 16px; border-radius: 8px; border: 1px solid var(--gray-200); min-height: 100px; font-size: 14px; line-height: 1.6; color: var(--gray-700); white-space: pre-wrap;">
                        Select checklist items to see SMS preview...
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="closeChecklistModal()" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Send Checklist + Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Update Confirmation Modal -->
    <div id="statusConfirmModal" class="modal">
        <div class="modal-content status-confirm-modal-content">
            <div class="modal-header status-confirm-header">
                <h2 class="modal-title status-confirm-title">Confirm Status Update</h2>
            </div>
            <div class="modal-body status-confirm-body">
                <p id="confirmMessage" class="status-confirm-message"></p>
            </div>
            <div class="modal-actions status-confirm-actions">
                <button onclick="closeStatusConfirmModal()" class="btn-modal btn-modal-secondary">
                    Cancel
                </button>
                <button id="confirmUpdateBtn" onclick="proceedWithStatusUpdate()" class="btn-modal btn-modal-primary">
                    Update Status
                </button>
            </div>
        </div>
    </div>

    <!-- Success Popup Modal -->
    <div id="successPopupModal" class="modal">
        <div class="modal-content success-popup-modal-content">
            <div class="modal-header success-popup-header">
                <h2 class="modal-title success-popup-title">Status Updated Successfully</h2>
            </div>
            <div class="modal-body success-popup-body">
                <div id="successMessage" class="success-popup-message"></div>
            </div>
            <div class="modal-actions success-popup-actions">
                <button onclick="closeSuccessPopup()" class="btn-modal btn-modal-primary">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications Modal -->
    <div id="notificationsModal" class="modal">
        <div class="modal-content notifications-modal-content">
            <div class="modal-header notifications-modal-header">
                <h2 class="modal-title notifications-modal-title">
                    <i class="fas fa-bell"></i>
                    Notifications
                </h2>
                <div class="notifications-modal-actions">
                    <button onclick="markAllNotificationsAsRead()" class="btn-notification-action">
                        <i class="fas fa-check-double"></i>
                        Mark All Read
                    </button>
                    <button onclick="closeNotificationsModal()" class="modal-close notifications-close-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div id="notificationsContent" class="notifications-content-container">
                <!-- Notifications will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Global variable to store current request type for checklist selection
        let currentModalRequestType = null;

        // Wrapper function for updateStatus button clicks
        function updateStatus(trackingNumber, currentStatus, requestType = null, currentRemarks = '', serviceName = '') {
            // Decode URL-encoded values
            currentRemarks = decodeURIComponent(currentRemarks || '');
            serviceName = decodeURIComponent(serviceName || '');
            
            // Call openStatusModal with correct parameter order
            openStatusModal(trackingNumber, currentStatus, currentRemarks, requestType, serviceName);
        }

        function openStatusModal(trackingNumber, currentStatus, currentRemarks, requestType = null, serviceName = '') {
            console.log('Opening status modal for:', { trackingNumber, currentStatus, requestType, serviceName });

            document.getElementById('modalTrackingNumber').value = trackingNumber;
            document.getElementById('modalTrackingNumberDisplay').value = trackingNumber;
            document.getElementById('modalNotes').value = currentRemarks || '';

            const dateField = document.getElementById('modalStatusDate');
            if (dateField) {
                dateField.value = new Date().toISOString().split('T')[0];
            }

            // Detect Delayed BRAP or Delayed Regular Non-BRAP from service name if not already set
            // Check for various formats: "Delayed Regular Non-BRAP Birth Registration Assistance Project", etc.
            // IMPORTANT: Exclude "Other Civil Registry Services" (Marriage, Death, Issuance) from detection
            const serviceNameLower = (serviceName || '').toLowerCase();
            const hasDelayed = serviceNameLower.includes('delayed');
            const hasNonBrap = serviceNameLower.includes('non-brap') || serviceNameLower.includes('non brap');
            const hasBrap = serviceNameLower.includes('brap');
            
            // Check if this is an "Other Civil Registry Service" (Marriage, Death, Issuance)
            // These should NOT be detected as Delayed Birth Registration
            const isOtherCivilRegistryService = 
                serviceNameLower.includes('certificates of marriage') ||
                serviceNameLower.includes('municipal form no. 97') ||
                serviceNameLower.includes('certificates of death') ||
                serviceNameLower.includes('municipal form no. 103') ||
                serviceNameLower.includes('certified photocopy') ||
                serviceNameLower.includes('transcription of civil registry');
            
            // Only detect Delayed Birth Registration if it's NOT an Other Civil Registry Service
            if (serviceName && hasDelayed && !isOtherCivilRegistryService) {
                // Must be "Delayed Birth Registration" specifically, not just "Delayed"
                const hasBirthRegistration = serviceNameLower.includes('birth registration');
                
                if (hasBirthRegistration) {
                    if (hasBrap && !hasNonBrap) {
                        requestType = 'delayed_brap';
                        console.log('Detected Delayed BRAP from service name');
                    } else if (hasNonBrap) {
                        requestType = 'delayed_regular';
                        console.log('Detected Delayed Regular from service name');
                    } else if (serviceName.includes('Delayed Regular')) {
                        // Fallback: if it says "Delayed Regular" without BRAP, it's Delayed Regular
                        requestType = 'delayed_regular';
                        console.log('Detected Delayed Regular from service name (fallback)');
                    }
                }
            }
            
            console.log('Final requestType after detection:', requestType, 'serviceName:', serviceName);

            // Store current request type globally for checklist selection
            currentModalRequestType = requestType;
            console.log('Current request type for checklist:', currentModalRequestType);

            // Update status options based on request type and current status
            updateStatusOptions(requestType, currentStatus, serviceName);

            // Set current status (but don't allow selecting it)
            document.getElementById('modalStatus').value = '';

            // Add change listener to show checklist modal when "checklist_given" is selected
            // and show/hide incomplete requirements/for compliance section when selected
            const statusSelect = document.getElementById('modalStatus');
            statusSelect.onchange = function () {
                // Hide all special sections first
                document.getElementById('incompleteRequirementsSection').style.display = 'none';
                document.getElementById('forComplianceSection').style.display = 'none';
                document.getElementById('generalRemarksSection').style.display = 'block';

                // Hide all checklist sub-containers
                hideAllChecklistContainers();

                if (this.value === 'checklist_given') {
                    closeStatusModal();
                    openChecklistModal(trackingNumber, currentRemarks, requestType, serviceName);
                } else if (this.value === 'incomplete_requirements') {
                    // Show incomplete requirements section, hide general remarks
                    document.getElementById('incompleteRequirementsSection').style.display = 'block';
                    document.getElementById('generalRemarksSection').style.display = 'none';
                    // Show correct checklist based on request type
                    showCorrectChecklist('incomplete', currentModalRequestType);
                    updateIncompleteRequirementsPreview();
                } else if (this.value === 'for_compliance') {
                    // Show for compliance section, hide general remarks
                    document.getElementById('forComplianceSection').style.display = 'block';
                    document.getElementById('generalRemarksSection').style.display = 'none';
                    // Show correct checklist based on request type
                    showCorrectChecklist('compliance', currentModalRequestType);
                    updateForCompliancePreview();
                } else {
                    // Clear checkboxes for other statuses
                    document.querySelectorAll('input[name="missing_requirements[]"]').forEach(cb => cb.checked = false);
                    document.querySelectorAll('input[name="compliance_requirements[]"]').forEach(cb => cb.checked = false);
                }
            };

            // Check if current status is incomplete_requirements or for_compliance and show section
            if (currentStatus === 'incomplete_requirements') {
                document.getElementById('incompleteRequirementsSection').style.display = 'block';
                document.getElementById('generalRemarksSection').style.display = 'none';
                showCorrectChecklist('incomplete', currentModalRequestType);
            } else if (currentStatus === 'for_compliance') {
                document.getElementById('forComplianceSection').style.display = 'block';
                document.getElementById('generalRemarksSection').style.display = 'none';
                showCorrectChecklist('compliance', currentModalRequestType);
            } else {
                document.getElementById('incompleteRequirementsSection').style.display = 'none';
                document.getElementById('forComplianceSection').style.display = 'none';
            }

            document.getElementById('statusModal').classList.add('show');
        }

        // Hide all checklist containers
        function hideAllChecklistContainers() {
            // Incomplete Requirements containers
            const incBrap = document.getElementById('incompleteRequirementsListBrap');
            const incRegular = document.getElementById('incompleteRequirementsListRegular');
            const incOther = document.getElementById('incompleteRequirementsListOther');
            if (incBrap) incBrap.style.display = 'none';
            if (incRegular) incRegular.style.display = 'none';
            if (incOther) incOther.style.display = 'none';

            // For Compliance containers
            const compBrap = document.getElementById('forComplianceListBrap');
            const compRegular = document.getElementById('forComplianceListRegular');
            if (compBrap) compBrap.style.display = 'none';
            if (compRegular) compRegular.style.display = 'none';
        }

        // Show the correct checklist based on type and request type
        function showCorrectChecklist(sectionType, requestType) {
            const isBrap = requestType === 'delayed_brap' || requestType === 'brap';
            const isRegular = requestType === 'delayed_regular' || requestType === 'regular';
            const isOtherService = requestType === 'other_service' || requestType === 'other';

            console.log('Showing checklist for:', sectionType, 'request type:', requestType, 'isBrap:', isBrap, 'isRegular:', isRegular, 'isOtherService:', isOtherService);

            if (sectionType === 'incomplete') {
                const brapContainer = document.getElementById('incompleteRequirementsListBrap');
                const regularContainer = document.getElementById('incompleteRequirementsListRegular');
                const otherContainer = document.getElementById('incompleteRequirementsListOther');
                const typeLabel = document.getElementById('incompleteReqTypeLabel');

                if (isOtherService) {
                    // Show Other Services checklist
                    if (brapContainer) brapContainer.style.display = 'none';
                    if (regularContainer) regularContainer.style.display = 'none';
                    if (otherContainer) otherContainer.style.display = 'block';
                    if (typeLabel) typeLabel.textContent = 'For Other Services - Select Missing Requirements';
                } else if (isBrap) {
                    if (brapContainer) brapContainer.style.display = 'block';
                    if (regularContainer) regularContainer.style.display = 'none';
                    if (otherContainer) otherContainer.style.display = 'none';
                    if (typeLabel) typeLabel.textContent = 'For Delayed Birth Registration under BRAP (aged 79 and below)';
                } else {
                    // Default to Regular for delayed_regular or regular
                    if (brapContainer) brapContainer.style.display = 'none';
                    if (regularContainer) regularContainer.style.display = 'block';
                    if (otherContainer) otherContainer.style.display = 'none';
                    if (typeLabel) typeLabel.textContent = 'For Regular Delayed Birth Registration (COLB)';
                }
            } else if (sectionType === 'compliance') {
                const brapContainer = document.getElementById('forComplianceListBrap');
                const regularContainer = document.getElementById('forComplianceListRegular');
                const typeLabel = document.getElementById('complianceReqTypeLabel');

                if (isBrap) {
                    if (brapContainer) brapContainer.style.display = 'block';
                    if (regularContainer) regularContainer.style.display = 'none';
                    if (typeLabel) typeLabel.textContent = 'For Delayed Birth Registration under BRAP - Compliance Requirements';
                } else {
                    // Default to Regular for delayed_regular or regular
                    if (brapContainer) brapContainer.style.display = 'none';
                    if (regularContainer) regularContainer.style.display = 'block';
                    if (typeLabel) typeLabel.textContent = 'For Regular Delayed Birth Registration (COLB) - Compliance Requirements';
                }
            }
        }

        // Function to update incomplete requirements SMS preview
        function updateIncompleteRequirementsPreview() {
            const checkboxes = document.querySelectorAll('input[name="missing_requirements[]"]:checked');
            const previewDiv = document.getElementById('incompleteRequirementsPreview');
            const trackingNumber = document.getElementById('modalTrackingNumberDisplay').value;
            const sendSmsCheckbox = document.getElementById('sendIncompleteRequirementsSMS');
            const remarksField = document.getElementById('incompleteRequirementsRemarks');
            const remarks = remarksField ? remarksField.value.trim() : '';

            if (checkboxes.length > 0 && sendSmsCheckbox && sendSmsCheckbox.checked) {
                const missingItems = Array.from(checkboxes).map(cb => cb.value);
                const itemsList = missingItems.join(', ');
                let message = `CLICK2SERVE: Hello, Your request #${trackingNumber} - Some required documents are missing or incomplete. Missing requirements: ${itemsList}.`;

                if (remarks) {
                    message += ` Remarks: ${remarks}.`;
                }

                message += ` Please submit the missing documents to our office. Visit us during office hours (Mon-Fri, 8AM-5PM) to complete your application. Thank you!`;

                previewDiv.textContent = message;
            } else if (checkboxes.length > 0) {
                previewDiv.textContent = 'Enable "Send via SMS" to preview the message.';
            } else {
                previewDiv.textContent = 'Select missing requirements and enable SMS to see preview...';
            }
        }

        // Function to update for compliance SMS preview
        function updateForCompliancePreview() {
            const checkboxes = document.querySelectorAll('input[name="compliance_requirements[]"]:checked');
            const previewDiv = document.getElementById('forCompliancePreview');
            const trackingNumber = document.getElementById('modalTrackingNumberDisplay').value;
            const sendSmsCheckbox = document.getElementById('sendForComplianceSMS');
            const remarksField = document.getElementById('forComplianceRemarks');
            const remarks = remarksField ? remarksField.value.trim() : '';

            if (checkboxes.length > 0 && sendSmsCheckbox && sendSmsCheckbox.checked) {
                const complianceItems = Array.from(checkboxes).map(cb => cb.value);
                const itemsList = complianceItems.join(', ');
                let message = `CLICK2SERVE: Hello, Your request #${trackingNumber} requires compliance with additional requirements. Required documents: ${itemsList}.`;

                if (remarks) {
                    message += ` Remarks: ${remarks}.`;
                }

                message += ` Please submit the required documents to our office. Visit us during office hours (Mon-Fri, 8AM-5PM) to complete your compliance. Thank you!`;

                previewDiv.textContent = message;
            } else if (checkboxes.length > 0) {
                previewDiv.textContent = 'Enable "Send via SMS" to preview the message.';
            } else {
                previewDiv.textContent = 'Select compliance requirements and enable SMS to see preview...';
            }
        }

        // Function to filter requirements by search text
        function filterRequirements(sectionType) {
            let searchInput, listContainers = [];

            if (sectionType === 'incompleteRequirements') {
                searchInput = document.getElementById('incompleteRequirementsSearch');
                // Filter in both BRAP and Regular containers
                const brapContainer = document.getElementById('incompleteRequirementsListBrap');
                const regularContainer = document.getElementById('incompleteRequirementsListRegular');
                if (brapContainer) listContainers.push(brapContainer);
                if (regularContainer) listContainers.push(regularContainer);
            } else if (sectionType === 'forCompliance') {
                searchInput = document.getElementById('forComplianceSearch');
                // Filter in both BRAP and Regular containers
                const brapContainer = document.getElementById('forComplianceListBrap');
                const regularContainer = document.getElementById('forComplianceListRegular');
                if (brapContainer) listContainers.push(brapContainer);
                if (regularContainer) listContainers.push(regularContainer);
            } else {
                return;
            }

            const searchText = searchInput.value.toLowerCase().trim();

            // Filter in all containers
            listContainers.forEach(listContainer => {
                const categories = listContainer.querySelectorAll('.req-category');

                categories.forEach(category => {
                    const items = category.querySelectorAll('.req-item');
                    let hasVisibleItem = false;

                    items.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        if (searchText === '' || text.includes(searchText)) {
                            item.style.display = 'flex';
                            hasVisibleItem = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Show/hide category header based on whether it has visible items
                    category.style.display = hasVisibleItem ? 'block' : 'none';
                });
            });
        }

        function updateStatusOptions(requestType, currentStatus = '', serviceName = '') {
            const statusSelect = document.getElementById('modalStatus');
            if (!statusSelect) {
                console.error('Status select element not found');
                return;
            }

            statusSelect.innerHTML = ''; // Clear existing options

            // IMPORTANT: Check for Delayed Regular Non-BRAP FIRST before Delayed BRAP
            // Detect Delayed Regular Non-BRAP (has Delayed and Non-BRAP)
            // Check for various formats: "Delayed Regular Non-BRAP Birth Registration Assistance Project", "Delayed Birth Registration (Regular)", etc.
            // IMPORTANT: Exclude "Other Civil Registry Services" (Marriage, Death, Issuance) from detection
            const serviceNameLower = (serviceName || '').toLowerCase();
            const hasDelayed = serviceNameLower.includes('delayed');
            const hasNonBrap = serviceNameLower.includes('non-brap') || serviceNameLower.includes('non brap');
            const hasBrap = serviceNameLower.includes('brap');
            const hasRegular = serviceNameLower.includes('regular');
            
            // Check if this is an "Other Civil Registry Service" (Marriage, Death, Issuance)
            // These should NOT be detected as Delayed Birth Registration
            const isOtherCivilRegistryService = 
                serviceNameLower.includes('certificates of marriage') ||
                serviceNameLower.includes('municipal form no. 97') ||
                serviceNameLower.includes('certificates of death') ||
                serviceNameLower.includes('municipal form no. 103') ||
                serviceNameLower.includes('certified photocopy') ||
                serviceNameLower.includes('transcription of civil registry');
            
            // SIMPLIFIED DETECTION: Priority 1 - Check for "Delayed Birth Registration (Regular)"
            // This is the PRIMARY way to detect Delayed Regular requests - make it first priority
            // But ONLY if it's NOT an Other Civil Registry Service
            const isDelayedBirthRegistrationRegular = !isOtherCivilRegistryService && (
                (serviceName && serviceName.includes('Delayed Birth Registration (Regular)')) ||
                (serviceName && serviceName.includes('Delayed Birth Registration(Regular)')) ||
                (serviceNameLower && serviceNameLower.includes('delayed birth registration (regular)'))
            );
            
            // IMPORTANT: Check for Delayed Regular Non-BRAP
            // Priority 1: Check for "Delayed Birth Registration (Regular)" - SIMPLIFIED DETECTION (FIRST PRIORITY)
            // Priority 2: Check requestType directly (should be 'delayed_regular' from API)
            // Priority 3: Check serviceName for "Delayed Regular Non-BRAP" (exact match)
            // Priority 4: Check if serviceName has "Delayed" + "Regular" + "Non-BRAP" (case-insensitive)
            // Priority 5: Check if requestType is 'regular' but serviceName indicates Delayed Regular
            // NOTE: "Non-BRAP" contains "BRAP", so we must check for "Non-BRAP" specifically
            // IMPORTANT: Must also check for "Birth Registration" to exclude Marriage/Death/Issuance services
            const hasBirthRegistration = serviceNameLower.includes('birth registration');
            const isDelayedRegularNonBrap = !isOtherCivilRegistryService && (
                isDelayedBirthRegistrationRegular ||
                requestType === 'delayed_regular' ||
                (serviceName && serviceName.includes('Delayed Regular Non-BRAP')) ||
                (serviceName && hasDelayed && hasRegular && hasNonBrap && hasBirthRegistration) ||
                (serviceName && hasDelayed && hasNonBrap && !hasBrap && hasBirthRegistration) ||
                (requestType === 'regular' && hasDelayed && hasNonBrap && hasBirthRegistration)
            );

            // Detect Delayed BRAP from service name (must have Delayed and BRAP, but NOT Non-BRAP)
            // Also check if requestType is 'brap' but serviceName indicates Delayed BRAP
            // NOTE: Must check for "Non-BRAP" first to exclude it, then check for "BRAP"
            // IMPORTANT: Must also check for "Birth Registration" to exclude Marriage/Death/Issuance services
            const isDelayedBrap = !isDelayedRegularNonBrap && !isOtherCivilRegistryService && (
                requestType === 'delayed_brap' ||
                (hasDelayed && hasBrap && !hasNonBrap && hasBirthRegistration) ||
                (serviceName && serviceName.includes('Delayed') && serviceName.includes('BRAP') && !serviceName.includes('Non-BRAP') && hasBirthRegistration) ||
                (requestType === 'brap' && hasDelayed && !hasNonBrap && hasBirthRegistration)
            );

            console.log('Updating status options - Detection results:', {
                requestType: requestType,
                serviceName: serviceName,
                serviceNameLower: serviceNameLower,
                hasDelayed: hasDelayed,
                hasNonBrap: hasNonBrap,
                hasBrap: hasBrap,
                isDelayedRegularNonBrap: isDelayedRegularNonBrap,
                isDelayedBrap: isDelayedBrap,
                currentStatus: currentStatus
            });

            // Define status flows
            // Delayed BRAP Flow: INQUIRY -> TO BE ENCODED TO BRAP -> POSITIVE/NEGATIVE RESULT
            //                    POSITIVE RESULT -> POSTING -> REGISTERED (end)
            //                    NEGATIVE RESULT -> INCOMPLETE REQUIREMENTS / ACCEPTED / ACCEPTED (OCTOGENARIAN)
            //                    ACCEPTED (OCTOGENARIAN) -> VERIFIED WITH PROBLEM -> FOR COMPLIANCE -> ACCEPTED
            //                    ACCEPTED -> POSTING -> REGISTERED (end)

            if (requestType === 'other_service') {
                // Limited status options for Other Services: only Inquiry, Incomplete Requirements, Registered
                // Allow looping: incomplete_requirements can loop into itself (self-loop)
                // Prevent backwards transitions otherwise

                const allowedStatuses = [];

                if (currentStatus === 'inquiry') {
                    // From inquiry, can go to incomplete_requirements OR directly to registered
                    allowedStatuses.push({ value: 'incomplete_requirements', text: 'Incomplete Requirements' });
                    allowedStatuses.push({ value: 'registered', text: 'Registered' });
                } else if (currentStatus === 'incomplete_requirements') {
                    // From incomplete_requirements, can loop into itself OR go forward to registered
                    allowedStatuses.push({ value: 'incomplete_requirements', text: 'Incomplete Requirements' });
                    allowedStatuses.push({ value: 'registered', text: 'Registered' });
                } else if (currentStatus === 'registered') {
                    // Registered is final status - no further transitions allowed
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No further status updates available (Registered is final)';
                    option.disabled = true;
                    statusSelect.appendChild(option);
                } else {
                    // For any other status (like 'submitted'), allow transition to inquiry
                    allowedStatuses.push({ value: 'inquiry', text: 'Inquiry' });
                }

                console.log('Setting Other Service status options:', allowedStatuses, 'currentStatus:', currentStatus);

                if (allowedStatuses.length > 0) {
                    allowedStatuses.forEach(status => {
                        const option = document.createElement('option');
                        option.value = status.value;
                        option.textContent = status.text;
                        statusSelect.appendChild(option);
                    });
                }
            } else if (isDelayedBrap) {
                // Delayed BRAP - New flow based on requirements:
                // INQUIRY -> TO BE ENCODED TO BRAP -> POSITIVE/NEGATIVE RESULT
                // POSITIVE RESULT -> POSTING -> REGISTERED (end)
                // NEGATIVE RESULT -> INCOMPLETE REQUIREMENTS / ACCEPTED / ACCEPTED (OCTOGENARIAN)
                // INCOMPLETE REQUIREMENTS -> loop / ACCEPTED / ACCEPTED (OCTOGENARIAN)
                // ACCEPTED -> POSTING -> REGISTERED (end)
                // ACCEPTED (OCTOGENARIAN) -> VERIFIED WITH PROBLEM
                // VERIFIED WITH PROBLEM -> FOR COMPLIANCE / ACCEPTED
                // FOR COMPLIANCE -> loop / ACCEPTED

                // Define allowed transitions for each status
                // When 'accepted' is set, posting automatically starts (10 days), then proceed to 'registered'
                const delayedBrapTransitions = {
                    'inquiry': ['to_be_encoded_brap'],
                    'to_be_encoded_brap': ['positive_result', 'negative_result'],
                    'positive_result': ['registered'], // Auto-posting starts, after 10 days -> registered
                    'negative_result': ['incomplete_requirements', 'accepted', 'accepted_octogenarian'],
                    'incomplete_requirements': ['incomplete_requirements', 'accepted', 'accepted_octogenarian'], // Can loop
                    'accepted': ['registered'], // Auto-posting starts, after 10 days -> registered
                    'accepted_octogenarian': ['verified_with_problem', 'accepted'],
                    'verified_with_problem': ['for_compliance', 'accepted'],
                    'for_compliance': ['for_compliance', 'accepted'], // Can loop
                };

                // Status labels for Delayed BRAP
                const delayedBrapStatusLabels = {
                    'inquiry': 'Inquiry',
                    'to_be_encoded_brap': 'To Be Encoded to BRAP Request List',
                    'positive_result': 'Positive Result',
                    'negative_result': 'Negative Result',
                    'incomplete_requirements': 'Incomplete Requirements',
                    'accepted': 'Accepted',
                    'accepted_octogenarian': 'Accepted (Octogenarian)',
                    'verified_with_problem': 'Verified With Problem',
                    'for_compliance': 'For Compliance',
                    'posting': '10 Days Posting',
                    'registered': 'Registered'
                };

                // Get allowed next statuses based on current status
                const allowedNextStatuses = delayedBrapTransitions[currentStatus] || [];

                console.log('Setting Delayed BRAP status options:', allowedNextStatuses, 'currentStatus:', currentStatus);

                if (allowedNextStatuses.length === 0) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No further status updates available';
                    option.disabled = true;
                    statusSelect.appendChild(option);
                } else {
                    allowedNextStatuses.forEach(statusValue => {
                        const option = document.createElement('option');
                        option.value = statusValue;
                        option.textContent = delayedBrapStatusLabels[statusValue] || statusValue;
                        statusSelect.appendChild(option);
                    });
                }
            } else if (requestType === 'brap' && !isDelayedBrap) {
                // Regular BRAP Service (NOT Delayed BRAP) - includes TO BE ENCODED TO BRAP REQUEST LIST and all BRAP-specific statuses
                // This is for regular BRAP requests that go through posting and approval
                const brapServiceStatuses = [
                    { value: 'inquiry', text: 'Inquiry' },
                    { value: 'to_be_encoded_brap', text: 'To Be Encoded to BRAP Request List' },
                    { value: 'checklist_given', text: 'Checklist of Requirements Given' },
                    { value: 'incomplete_requirements', text: 'Incomplete Requirements' },
                    { value: 'accepted', text: 'Accepted' },
                    { value: 'psa_processing', text: 'PSA Processing' },
                    { value: 'posting', text: 'For Posting' },
                    { value: 'ready_for_approval', text: 'Ready for Approval' },
                    { value: 'registered', text: 'Registered' }
                ];

                console.log('Setting Regular BRAP Service status options:', brapServiceStatuses);

                brapServiceStatuses.forEach(status => {
                    const option = document.createElement('option');
                    option.value = status.value;
                    option.textContent = status.text;
                    statusSelect.appendChild(option);
                });
            } else if (isDelayedRegularNonBrap) {
                // Delayed Birth Registration (Regular) - Non-BRAP
                // Flow: INQUIRY -> WAITING FOR PSA NEGATIVE CERTIFICATE -> 
                //       POSITIVE RESULT -> REGISTERED (auto-posting starts, after 10 days -> registered)
                //       OR -> INCOMPLETE REQUIREMENTS (loop) -> ACCEPTED/ACCEPTED (OCTOGENARIAN)
                //       ACCEPTED (OCTOGENARIAN) -> VERIFIED WITH PROBLEM -> FOR COMPLIANCE (loop) -> ACCEPTED
                //       ACCEPTED -> REGISTERED (auto-posting starts, after 10 days -> registered)
                // Note: 'posting' is not in the flow as it's automatic when 'accepted' or 'positive_result' is set

                // Define allowed transitions for each status
                // When 'accepted' or 'positive_result' is set, posting automatically starts (10 days), then proceed to 'registered'
                const delayedRegularTransitions = {
                    'submitted': ['inquiry'], // Allow transition from submitted to inquiry
                    'inquiry': ['waiting_for_psa_negative_certificate'],
                    'waiting_for_psa_negative_certificate': ['positive_result', 'incomplete_requirements', 'accepted', 'accepted_octogenarian'],
                    'positive_result': ['registered'], // Auto-posting starts, after 10 days -> registered
                    'incomplete_requirements': ['incomplete_requirements', 'accepted', 'accepted_octogenarian'], // Can loop or proceed
                    'accepted': ['registered'], // Auto-posting starts, after 10 days -> registered
                    'accepted_octogenarian': ['verified_with_problem', 'accepted'],
                    'verified_with_problem': ['for_compliance', 'accepted'],
                    'for_compliance': ['for_compliance', 'accepted'], // Can loop or proceed
                };

                // Status labels
                const delayedRegularStatusLabels = {
                    'submitted': 'Submitted',
                    'inquiry': 'Inquiry',
                    'waiting_for_psa_negative_certificate': 'Waiting for Client to Submit Negative Certificate from PSA',
                    'positive_result': 'Positive Result',
                    'incomplete_requirements': 'Incomplete Requirements',
                    'accepted': 'Accepted',
                    'accepted_octogenarian': 'Accepted (Octogenarian)',
                    'verified_with_problem': 'Verified With Problem',
                    'for_compliance': 'For Compliance',
                    'registered': 'Registered'
                };

                // Get allowed next statuses based on current status
                const allowedNextStatuses = delayedRegularTransitions[currentStatus] || [];

                console.log('Setting Delayed Regular Non-BRAP status options:', allowedNextStatuses, 'currentStatus:', currentStatus, 'serviceName:', serviceName);

                // Handle registered status (final status)
                if (currentStatus === 'registered') {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No further status updates available (Registered is final)';
                    option.disabled = true;
                    statusSelect.appendChild(option);
                } else if (allowedNextStatuses.length === 0) {
                    // If current status is not in transitions, allow transition to inquiry as a correction
                    if (currentStatus && currentStatus !== 'inquiry' && currentStatus !== 'registered') {
                        const option = document.createElement('option');
                        option.value = 'inquiry';
                        option.textContent = 'Inquiry';
                        statusSelect.appendChild(option);
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No further status updates available';
                        option.disabled = true;
                        statusSelect.appendChild(option);
                    }
                } else {
                    allowedNextStatuses.forEach(statusValue => {
                        const option = document.createElement('option');
                        option.value = statusValue;
                        option.textContent = delayedRegularStatusLabels[statusValue] || statusValue;
                        statusSelect.appendChild(option);
                    });
                }
            } else {
                // No valid request type detected - show error message
                console.error('Unknown request type or service:', { requestType, serviceName, currentStatus });
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Error: Unable to determine request type. Please contact support.';
                option.disabled = true;
                statusSelect.appendChild(option);
            }
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.remove('show');
        }

        // Checklist Modal Functions
        function openChecklistModal(trackingNumber, currentRemarks = '', requestType = null) {
            console.log('Opening checklist modal for:', { trackingNumber, currentRemarks, requestType });

            // Set tracking number
            document.getElementById('checklistTrackingNumber').value = trackingNumber;

            // Set status date
            const dateField = document.getElementById('checklistStatusDate');
            if (dateField) {
                dateField.value = new Date().toISOString().split('T')[0];
            }

            // Set remarks if provided
            document.getElementById('checklistRemarks').value = currentRemarks || '';
            document.getElementById('checklistRemarksText').value = currentRemarks || '';

            // Reset form
            document.querySelectorAll('#checklistForm input[type="checkbox"]').forEach(cb => {
                if (cb.name !== 'send_checklist_sms') {
                    cb.checked = false;
                }
            });
            document.getElementById('sendChecklistSMS').checked = false;
            updateChecklistPreview();

            // Show modal
            document.getElementById('checklistModal').classList.add('show');
        }

        function closeChecklistModal() {
            document.getElementById('checklistModal').classList.remove('show');
        }

        function updateChecklistPreview() {
            const trackingNumber = document.getElementById('checklistTrackingNumber').value || 'TRK123456';
            const checkboxes = document.querySelectorAll('#checklistForm input[name="checklist_items[]"]:checked');
            const remarks = document.getElementById('checklistRemarksText').value;
            const sendSMS = document.getElementById('sendChecklistSMS').checked;

            const items = Array.from(checkboxes).map(cb => cb.value);
            const preview = document.getElementById('smsPreview');

            if (items.length === 0) {
                preview.textContent = 'Select checklist items to see SMS preview...';
                return;
            }

            const itemsList = items.join(', ');
            const remarksText = remarks ? ` Remarks: ${remarks}.` : '';
            const smsText = `CLICK2SERVE: Hello, Your inquiry #${trackingNumber} requires the following documents: ${itemsList}.${remarksText}`;

            preview.textContent = smsText;

            if (!sendSMS) {
                preview.textContent += '\n\n(Note: SMS will not be sent unless "Send Checklist via SMS" is checked)';
            }
        }

        // Handle checklist form submission
        document.getElementById('checklistForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const trackingNumber = document.getElementById('checklistTrackingNumber').value;
            const checkboxes = document.querySelectorAll('input[name="checklist_items[]"]:checked');
            const items = Array.from(checkboxes).map(cb => cb.value);

            if (items.length === 0) {
                alert('Please select at least one checklist item.');
                return;
            }

            const formData = new FormData(this);
            formData.append('status', 'checklist_given');
            formData.append('status_date', document.getElementById('checklistStatusDate').value);
            formData.append('remarks', document.getElementById('checklistRemarks').value);

            // Disable submit button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            fetch('/officer/requests/status', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
                .then(response => response.json().catch(() => ({})))
                .then(result => {
                    if (result.success) {
                        closeChecklistModal();
                        showSuccessPopup('Checklist sent successfully! Status updated to "Checklist Given".');
                        // Reload the page or refresh the requests list
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        alert('Error: ' + (result.message || 'Failed to send checklist. Please try again.'));
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending the checklist. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });

        // Close checklist modal when clicking outside
        document.getElementById('checklistModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeChecklistModal();
            }
        });

        // Floating Requests List Modal Functions
        function showRequestsList(type) {
            const modal = document.getElementById('requestsListModal');
            const title = document.getElementById('modalTitle');
            const content = document.getElementById('requestsListContent');

            // Set title based on type
            const titles = {
                'total': 'All Requests',
                'pending': 'Pending Requests',
                'processing': 'Processing Requests',
                'completed': 'Completed Requests'
            };

            title.textContent = titles[type] || 'Requests List';

            // Show loading state
            content.innerHTML = '<div style="text-align:center; padding:40px; color:#6b7280;">Loading requests...</div>';
            modal.classList.add('show');

            // Fetch requests based on type
            fetchRequestsByType(type, content);
        }

        function closeRequestsListModal() {
            document.getElementById('requestsListModal').classList.remove('show');
        }

        // Status confirmation modal functions
        function closeStatusConfirmModal() {
            document.getElementById('statusConfirmModal').classList.remove('show');
            pendingStatusUpdate = null;
        }

        // Success popup functions
        function showSuccessPopup(message) {
            document.getElementById('successMessage').innerHTML = message;
            document.getElementById('successPopupModal').classList.add('show');
        }

        function closeSuccessPopup() {
            document.getElementById('successPopupModal').classList.remove('show');
        }

        // Error popup functions
        function showErrorPopup(message, title = 'Error') {
            const errorMessageEl = document.getElementById('errorMessage');
            const errorTitleEl = document.getElementById('errorPopupTitle');
            
            if (errorTitleEl) {
                errorTitleEl.textContent = title;
            }
            
            if (errorMessageEl) {
                // Format message with proper HTML
                const formattedMessage = formatErrorMessage(message);
                errorMessageEl.innerHTML = formattedMessage;
            }
            
            document.getElementById('errorPopupModal').classList.add('show');
        }

        function closeErrorPopup() {
            document.getElementById('errorPopupModal').classList.remove('show');
        }

        function formatErrorMessage(message) {
            if (!message) return '';
            
            // Split message into lines
            const lines = message.split('\n').filter(line => line.trim() !== '');
            
            let formatted = '';
            let currentParagraph = '';
            
            lines.forEach((line, index) => {
                line = line.trim();
                
                // Check if this is a key-value pair (e.g., "Posting Started: Dec 01, 2025")
                if (line.match(/^(Posting Started|Posting Ends|Days Remaining):\s*(.+)$/)) {
                    if (currentParagraph) {
                        formatted += '<p>' + currentParagraph + '</p>';
                        currentParagraph = '';
                    }
                    const match = line.match(/^(Posting Started|Posting Ends|Days Remaining):\s*(.+)$/);
                    formatted += `<div class="error-info-item"><strong>${match[1]}:</strong> ${match[2]}</div>`;
                } else if (line.match(/^Please wait until/)) {
                    if (currentParagraph) {
                        formatted += '<p>' + currentParagraph + '</p>';
                        currentParagraph = '';
                    }
                    formatted += '<p style="margin-top: 16px; font-weight: 600; color: var(--gray-900);">' + line + '</p>';
                } else {
                    // Regular text line
                    if (currentParagraph) {
                        currentParagraph += '<br>';
                    }
                    currentParagraph += line;
                }
            });
            
            // Add any remaining paragraph
            if (currentParagraph) {
                formatted += '<p>' + currentParagraph + '</p>';
            }
            
            return formatted || '<p>' + message.replace(/\n/g, '<br>') + '</p>';
        }

        // Notifications functions
        function showNotifications() {
            const modal = document.getElementById('notificationsModal');
            const content = document.getElementById('notificationsContent');

            // Show loading state
            content.innerHTML = '<div class="notifications-empty-state"><div class="notifications-empty-state-icon"><i class="fas fa-spinner fa-spin"></i></div><p class="notifications-empty-state-text">Loading notifications...</p></div>';
            modal.classList.add('show');

            // Fetch notifications
            fetchNotifications(content);
        }

        function closeNotificationsModal() {
            document.getElementById('notificationsModal').classList.remove('show');
        }

        function fetchNotifications(container) {
            fetch('/officer/notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderNotifications(container, data.notifications);
                        updateNotificationBadge(data.unread_count);
                    } else {
                        container.innerHTML = '<div class="notifications-empty-state"><div class="notifications-empty-state-icon" style="color:#ef4444;"><i class="fas fa-exclamation-circle"></i></div><h3 class="notifications-empty-state-title" style="color:#ef4444;">Error</h3><p class="notifications-empty-state-text">Failed to load notifications. Please try again.</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                    container.innerHTML = '<div class="notifications-empty-state"><div class="notifications-empty-state-icon" style="color:#ef4444;"><i class="fas fa-exclamation-circle"></i></div><h3 class="notifications-empty-state-title" style="color:#ef4444;">Error</h3><p class="notifications-empty-state-text">Failed to load notifications. Please try again.</p></div>';
                });
        }

        function renderNotifications(container, notifications) {
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="notifications-empty-state">
                        <div class="notifications-empty-state-icon">
                            <i class="fas fa-bell-slash"></i>
                        </div>
                        <h3 class="notifications-empty-state-title">No notifications</h3>
                        <p class="notifications-empty-state-text">You don't have any notifications yet.</p>
                    </div>
                `;
                return;
            }

            let notificationsHTML = '';
            notifications.forEach(notification => {
                const isRead = notification.read_at !== null;
                const timeAgo = getTimeAgo(notification.created_at);
                const itemClass = isRead ? 'read' : 'unread';

                notificationsHTML += `
                    <div class="notification-item ${itemClass}" onclick="markNotificationAsRead(${notification.id})">
                        <div class="notification-item-header">
                            <h4 class="notification-item-title">${notification.title}</h4>
                            <span class="notification-item-time">${timeAgo}</span>
                        </div>
                        <p class="notification-item-message">${notification.message}</p>
                        ${!isRead ? '<div class="notification-item-indicator"></div>' : ''}
                    </div>
                `;
            });

            container.innerHTML = notificationsHTML;
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }

        function markNotificationAsRead(notificationId) {
            fetch('/officer/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ notification_id: notificationId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh notifications
                        fetchNotifications(document.getElementById('notificationsContent'));
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
        }

        function markAllNotificationsAsRead() {
            fetch('/officer/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh notifications
                        fetchNotifications(document.getElementById('notificationsContent'));
                    }
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                });
        }

        function getTimeAgo(dateString) {
            const now = new Date();
            const date = new Date(dateString);
            const diffInSeconds = Math.floor((now - date) / 1000);

            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
            return `${Math.floor(diffInSeconds / 86400)}d ago`;
        }

        function fetchRequestsByType(type, container) {
            console.log('Fetching requests by type:', type);

            // Get all requests from both CivilService and OtherService
            Promise.all([
                fetch('/officer/requests/brap').then(response => {
                    console.log('Brap response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                }),
                fetch('/officer/requests/regular').then(response => {
                    console.log('Regular response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
            ])
                .then(([brapData, regularData]) => {
                    console.log('Brap data:', brapData);
                    console.log('Regular data:', regularData);

                    let allRequests = [];

                    // Safely combine requests from both sources
                    if (brapData && brapData.requests) {
                        allRequests = [...allRequests, ...brapData.requests];
                    }
                    if (regularData && regularData.requests) {
                        allRequests = [...allRequests, ...regularData.requests];
                    }

                    console.log('Combined all requests:', allRequests);

                    // Filter requests based on type
                    let filteredRequests = [];
                    switch (type) {
                        case 'total':
                            filteredRequests = allRequests;
                            break;
                        case 'pending':
                            // For Other Services: only 'inquiry' is pending
                            // For Civil Services: 'inquiry' is also pending
                            filteredRequests = allRequests.filter(req => req.status === 'inquiry');
                            break;
                        case 'processing':
                            // For Other Services: 'incomplete_requirements' and 'accepted' are processing
                            // For Civil Services: 'checklist_given', 'incomplete_requirements', 'accepted', 'encoded_brap' are processing
                            filteredRequests = allRequests.filter(req => {
                                if (req.request_type === 'other_service') {
                                    return ['incomplete_requirements', 'accepted'].includes(req.status);
                                } else {
                                    return ['checklist_given', 'incomplete_requirements', 'accepted', 'encoded_brap'].includes(req.status);
                                }
                            });
                            break;
                        case 'completed':
                            // For Other Services: 'registered' is completed
                            // For Civil Services: 'registered', 'positive_result', 'negative_result' are completed
                            filteredRequests = allRequests.filter(req => {
                                if (req.request_type === 'other_service') {
                                    return req.status === 'registered';
                                } else {
                                    return ['registered', 'positive_result', 'negative_result'].includes(req.status);
                                }
                            });
                            break;
                    }

                    console.log('Filtered requests for type', type, ':', filteredRequests);
                    renderRequestsListModal(container, filteredRequests, type);
                })
                .catch(error => {
                    console.error('Error fetching requests:', error);
                    console.error('Error details:', {
                        message: error.message,
                        stack: error.stack,
                        name: error.name
                    });
                    container.innerHTML = `
                    <div style="text-align:center; padding:40px; color:#ef4444;">
                        <div style="font-size:48px; margin-bottom:16px;">⚠️</div>
                        <h3 style="margin:0 0 8px 0; color:#ef4444;">Error loading requests</h3>
                        <p style="margin:0 0 16px 0; color:#6b7280;">${error.message}</p>
                        <button onclick="location.reload()" style="background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">
                            Try Again
                        </button>
                    </div>
                `;
                });
        }

        function renderRequestsListModal(container, requests, type) {
            if (requests.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; padding:40px; color:#6b7280;">
                        <div style="font-size:48px; margin-bottom:16px;">📋</div>
                        <h3 style="margin:0 0 8px 0; color:#374151;">No ${type} requests found</h3>
                        <p style="margin:0; color:#6b7280;">There are currently no ${type} requests to display.</p>
                    </div>
                `;
                return;
            }

            let tableHTML = `
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:separate; border-spacing:0 12px;">
                        <thead>
                            <tr style="background:linear-gradient(135deg, var(--teal-primary), var(--teal-light));">
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">CLIENT NAME</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">PHONE NO.</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">REFERENCE NO</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">SERVICE TYPE</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">STATUS</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">DATE CREATED</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            requests.forEach(request => {
                const statusColors = {
                    'inquiry': { bg: '#fef3c7', text: '#f59e0b', label: 'INQUIRY' },
                    'to_be_encoded_brap': { bg: '#e0e7ff', text: '#6366f1', label: 'TO BE ENCODED BRAP' },
                    'checklist_given': { bg: '#dbeafe', text: '#3b82f6', label: 'CHECKLIST GIVEN' },
                    'incomplete_requirements': { bg: '#fef3c7', text: '#f59e0b', label: 'INCOMPLETE' },
                    'accepted': { bg: '#d1fae5', text: '#10b981', label: 'ACCEPTED' },
                    'psa_processing': { bg: '#e0f2fe', text: '#0369a1', label: 'PSA PROCESSING' },
                    'posting': { bg: '#fce7f3', text: '#be185d', label: 'FOR POSTING' },
                    'ready_for_approval': { bg: '#fef3c7', text: '#d97706', label: 'READY FOR APPROVAL' },
                    'registered': { bg: '#10b981', text: '#ffffff', label: 'REGISTERED' }
                };

                const statusInfo = statusColors[request.status] || { bg: '#f3f4f6', text: '#6b7280', label: request.status.toUpperCase() };

                tableHTML += `
                    <tr style="background:#f9fafb; border-radius:8px;">
                        <td style="padding:16px 12px; font-size:14px; font-weight:500;">${request.full_name}</td>
                        <td style="padding:16px 12px; font-family:monospace; font-size:14px; font-weight:500;">${request.contact_number || 'N/A'}</td>
                        <td style="padding:16px 12px; font-family:monospace; font-size:14px; font-weight:500;">${request.tracking_number}</td>
                        <td style="padding:16px 12px; color:#6b7280; font-size:14px; font-weight:500;">${request.service_type}</td>
                        <td style="padding:16px 12px;">
                            <span style="background:${statusInfo.bg}; color:${statusInfo.text}; padding:6px 12px; border-radius:6px; font-size:14px; font-weight:600; text-transform:uppercase;">
                                ${statusInfo.label}
                            </span>
                        </td>
                        <td style="padding:16px 12px; color:#6b7280; font-size:14px;">${request.created_at}</td>
                        <td style="padding:16px 12px; white-space:nowrap;">
                            <div style="display:flex; gap:12px;">
                                <button onclick="viewRequest('${request.tracking_number}')" 
                                    style="background:#3b82f6; color:#fff; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.2s;">
                                    View
                                </button>
                                <button onclick="updateStatus('${request.tracking_number}', '${request.status}', '${request.request_type || 'civil_service'}', '${encodeURIComponent(request.admin_notes || '')}', '${encodeURIComponent(request.service_name || request.service_type || '')}')" 
                                    style="background:#0ea5e9; color:#fff; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.2s;">
                                    Update Status
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tableHTML += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = tableHTML;
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
        document.addEventListener('click', function (e) {
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

        // Close floating modal when clicking outside
        document.getElementById('requestsListModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeRequestsListModal();
            }
        });

        // Close status confirmation modal when clicking outside
        document.getElementById('statusConfirmModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeStatusConfirmModal();
            }
        });

        // Close success popup when clicking outside
        document.getElementById('successPopupModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeSuccessPopup();
            }
        });

        // Close notifications modal when clicking outside
        document.getElementById('notificationsModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeNotificationsModal();
            }
        });

        // Tab switching function for Civil Registry dashboard
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('[id$="-tab"]').forEach(tab => {
                tab.style.display = 'none';
            });

            // Remove active class from all buttons
            document.querySelectorAll('[id$="-tab-btn"]').forEach(button => {
                button.style.color = '#6b7280';
                button.style.borderBottomColor = 'transparent';
                button.style.background = 'none';
            });

            // Show selected tab content
            document.getElementById(tabName + '-tab').style.display = 'block';

            // Add active class to clicked button
            const activeButton = document.getElementById(tabName + '-tab-btn');
            activeButton.style.color = '#2563eb';
            activeButton.style.borderBottomColor = '#2563eb';
            activeButton.style.background = '#f8fafc';

            // Load data for the selected tab
            loadTabData(tabName);
        }

        // Load data for the selected tab
        function loadTabData(tabName) {
            console.log('Loading tab data for:', tabName);

            // Handle appointments and documents tabs
            if (tabName === 'appointments') {
                loadAppointmentsData();
                return;
            }


            const tableContainer = document.getElementById(tabName + '-requests-table');

            // Show loading state
            tableContainer.innerHTML = '<div style="text-align:center; padding:20px; color:#6b7280;">Loading...</div>';

            // Fetch data
            fetch(`/officer/requests/${tabName}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Received data for', tabName, ':', data);
                    if (data.requests && data.requests.length > 0) {
                        renderRequestsTable(tableContainer, data.requests);
                    } else {
                        tableContainer.innerHTML = '<div style="text-align:center; padding:40px; color:#6b7280;">No requests found.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading requests:', error);
                    tableContainer.innerHTML = '<div style="text-align:center; padding:40px; color:#ef4444;">Error loading requests. Please try again.</div>';
                });
        }

        // Render requests table
        function renderRequestsTable(container, requests) {
            console.log('Rendering requests table with', requests.length, 'requests');
            let tableHTML = `
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:separate; border-spacing:0 12px;">
                        <thead>
                            <tr style="background:linear-gradient(135deg, var(--teal-primary), var(--teal-light));">
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">CLIENT NAME</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">PHONE NO.</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">REFERENCE NO.</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">SERVICE TYPE</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">STATUS</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">DATE CREATED</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            requests.forEach(request => {
                console.log('Processing request:', request.tracking_number, 'request_type:', request.request_type);
                const statusColors = {
                    'inquiry': { bg: '#fef3c7', text: '#f59e0b', label: 'INQUIRY' },
                    'to_be_encoded_brap': { bg: '#e0e7ff', text: '#6366f1', label: 'TO BE ENCODED BRAP' },
                    'checklist_given': { bg: '#dbeafe', text: '#3b82f6', label: 'CHECKLIST GIVEN' },
                    'incomplete_requirements': { bg: '#fef3c7', text: '#f59e0b', label: 'INCOMPLETE' },
                    'accepted': { bg: '#d1fae5', text: '#10b981', label: 'ACCEPTED' },
                    'registered': { bg: '#10b981', text: '#ffffff', label: 'REGISTERED' }
                };

                const statusInfo = statusColors[request.status] || { bg: '#f3f4f6', text: '#6b7280', label: request.status.toUpperCase() };

                // Map service names to display names
                let displayServiceType = request.service_type || request.service_name || '';
                if (displayServiceType.includes('Delayed Regular Non-BRAP Birth Registration Assistance Project')) {
                    displayServiceType = 'Delayed Birth Registration (Regular)';
                } else if (displayServiceType.includes('Delayed BRAP Birth Registration Assistance Project')) {
                    displayServiceType = 'Delayed Birth Registration under BRAP';
                }

                tableHTML += `
                    <tr style="background:var(--white); border:1px solid var(--gray-200); border-radius:8px; transition:all 0.3s ease;" onmouseover="this.style.background='var(--teal-bg)'; this.style.transform='translateY(-1px)'; this.style.boxShadow='var(--shadow-sm)';" onmouseout="this.style.background='var(--white)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <td style="padding:16px 20px; font-size:14px; font-weight:500; color:var(--gray-800);">${request.full_name}</td>
                        <td style="padding:16px 20px; font-family:monospace; font-size:14px; font-weight:500; color:var(--gray-800);">${request.contact_number || 'N/A'}</td>
                        <td style="padding:16px 20px; font-family:monospace; font-size:14px; font-weight:500; color:var(--gray-800);">${request.tracking_number}</td>
                        <td style="padding:16px 20px; font-size:14px; font-weight:500; color:var(--gray-600);">${displayServiceType}</td>
                        <td style="padding:16px 20px;">
                            <span style="background:${statusInfo.bg}; color:${statusInfo.text}; padding:6px 12px; border-radius:6px; font-size:14px; font-weight:600; text-transform:uppercase; display:inline-block;">
                                ${statusInfo.label}
                            </span>
                        </td>
                        <td style="padding:16px 20px; font-size:14px; color:var(--gray-600);">${request.created_at}</td>
                        <td style="padding:16px 20px; white-space:nowrap;">
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <button onclick="viewRequest('${request.tracking_number}')" 
                                    style="background:linear-gradient(135deg, #3b82f6, #2563eb); color:#fff; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.3s ease; display:inline-flex; align-items:center; gap:6px;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.3)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button onclick="updateStatus('${request.tracking_number}', '${request.status}', '${request.request_type || 'civil_service'}', '${encodeURIComponent(request.admin_notes || '')}', '${encodeURIComponent(request.service_name || request.service_type || '')}')" 
                                    style="background:linear-gradient(135deg, #0ea5e9, #0284c7); color:#fff; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.3s ease; display:inline-flex; align-items:center; gap:6px;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(14, 165, 233, 0.3)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    <i class="fas fa-edit"></i> Update Status
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tableHTML += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = tableHTML;
        }

        // View request function
        function viewRequest(trackingNumber) {
            window.location.href = `/officer/requests/${trackingNumber}/view`;
        }

        // Update status function
        function updateStatus(trackingNumber, currentStatus, requestType, encodedRemarks = '', encodedServiceName = '') {
            const remarks = encodedRemarks ? decodeURIComponent(encodedRemarks) : '';
            const serviceName = encodedServiceName ? decodeURIComponent(encodedServiceName) : '';
            openStatusModal(trackingNumber, currentStatus, remarks, requestType, serviceName);
        }

        // Global variables for status update
        let pendingStatusUpdate = null;

        // Handle status form submission via AJAX
        document.getElementById('statusForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            // Ensure all required fields are present
            if (!data.tracking_number || !data.status) {
                alert('Please fill in all required fields.');
                return;
            }

            // Validate incomplete requirements if status is incomplete_requirements
            if (data.status === 'incomplete_requirements') {
                const missingRequirements = formData.getAll('missing_requirements[]');
                if (missingRequirements.length === 0) {
                    alert('Please select at least one missing requirement.');
                    return;
                }
                data.missing_requirements = missingRequirements;
                // Use the section-specific remarks
                data.remarks = formData.get('incomplete_requirements_remarks') || '';
            }

            // Validate compliance requirements if status is for_compliance
            if (data.status === 'for_compliance') {
                const complianceRequirements = formData.getAll('compliance_requirements[]');
                if (complianceRequirements.length === 0) {
                    alert('Please select at least one compliance requirement.');
                    return;
                }
                data.compliance_requirements = complianceRequirements;
                // Use the section-specific remarks
                data.remarks = formData.get('compliance_remarks') || '';
            }

            // Log the data being sent for debugging
            console.log('Form data being sent:', data);

            // Store the pending update data
            pendingStatusUpdate = data;

            // Show confirmation modal
            const newStatus = data.status;
            const trackingNumber = data.tracking_number;
            const statusText = document.querySelector(`option[value="${newStatus}"]`).textContent;

            document.getElementById('confirmMessage').textContent =
                `Are you sure you want to update the status of request ${trackingNumber} to "${statusText}"?`;

            document.getElementById('statusConfirmModal').classList.add('show');
        });


        // Proceed with status update after confirmation
        function proceedWithStatusUpdate() {
            if (!pendingStatusUpdate) return;

            const data = pendingStatusUpdate;

            // Show loading state
            const confirmBtn = document.getElementById('confirmUpdateBtn');
            const originalText = confirmBtn.textContent;
            confirmBtn.textContent = 'Updating...';
            confirmBtn.disabled = true;

            fetch('/officer/requests/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
                .then(async response => {
                    console.log('Response status:', response.status);
                    const result = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        // Extract error message from response
                        const errorMessage = result.message || result.error || `HTTP error! status: ${response.status}`;
                        // Check if this is a posting period validation error
                        if (result?.error_type === 'posting_period_not_completed') {
                            const error = new Error(errorMessage);
                            error.errorData = result;
                            throw error;
                        }
                        throw new Error(errorMessage);
                    }

                    return result;
                })
                .then(result => {
                    console.log('Response result:', result);
                    if (result && result.success) {
                        try {
                            // Close confirmation modal
                            closeStatusConfirmModal();

                            // Close status modal
                            closeStatusModal();

                            // Reload current tab data
                            const activeTabElement = document.querySelector('[id$="-tab-btn"]');
                            if (activeTabElement) {
                                const activeTab = activeTabElement.id.replace('-tab-btn', '');
                                loadTabData(activeTab);
                            } else {
                                console.warn('No active tab found, skipping data reload');
                            }

                            // Show success popup with SMS status and update time
                            let successMessage = '';

                            // Add status information
                            if (result.status_formatted) {
                                successMessage += `<div class="success-info-item"><strong>New Status:</strong> ${result.status_formatted}</div>`;
                            }

                            // Add update time
                            if (result.updated_at) {
                                successMessage += `<div class="success-info-item"><strong>Updated At:</strong> ${result.updated_at}</div>`;
                            }

                            // Add SMS status
                            if (result.sms_sent) {
                                successMessage += '<div class="success-info-item"><strong>SMS Notification:</strong> Sent successfully';
                                if (result.sms_message_id || result.sms_network) {
                                    successMessage += '<div class="success-info-details">';
                                    if (result.sms_message_id) {
                                        successMessage += `<span class="success-info-detail">Message ID: ${result.sms_message_id}</span>`;
                                    }
                                    if (result.sms_network) {
                                        successMessage += `<span class="success-info-detail">Network: ${result.sms_network}</span>`;
                                    }
                                    successMessage += '</div>';
                                }
                                successMessage += '</div>';
                            } else if (result.sms_skipped) {
                                successMessage += `<div class="success-info-item"><strong>SMS Notification:</strong> Skipped - ${result.sms_skip_reason || 'Not requested'}</div>`;
                            } else if (result.sms_error) {
                                successMessage += `<div class="success-info-item"><strong>SMS Notification:</strong> Failed - ${result.sms_error}</div>`;
                            } else {
                                successMessage += '<div class="success-info-item"><strong>SMS Notification:</strong> Not sent</div>';
                            }

                            showSuccessPopup(successMessage);
                            
                            // Refresh queue data to update the queue table (if queue is enabled)
                            const queueContainer = document.getElementById('queue-table-container');
                            if (queueContainer) {
                                loadQueueData();
                            }
                            
                            // Reload the current tab data to refresh the request table
                            const activeTab = document.querySelector('.tab-btn.active');
                            if (activeTab) {
                                const tabName = activeTab.id.replace('-tab-btn', '');
                                if (tabName && tabName !== 'appointments') {
                                    loadTabData(tabName);
                                }
                            }
                        } catch (error) {
                            console.error('Error in success handler:', error);
                            alert('Status updated successfully, but there was an error refreshing the page. Please refresh manually.');
                        }
                    } else {
                        console.error('Status update failed:', result);
                        // Check if this is a posting period validation error
                        if (result?.error_type === 'posting_period_not_completed') {
                            showErrorPopup(result.message || 'Cannot update status to Registered', 'Cannot Update Status');
                        } else {
                            showErrorPopup(result?.message || 'Failed to update status', 'Error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Network error:', error);
                    // Check if error has errorData property (from posting period validation)
                    if (error.errorData && error.errorData.error_type === 'posting_period_not_completed') {
                        showErrorPopup(error.errorData.message || error.message, 'Cannot Update Status');
                    } else {
                        // Try to parse error message if it's JSON
                        try {
                            const errorData = JSON.parse(error.message);
                            if (errorData.error_type === 'posting_period_not_completed') {
                                showErrorPopup(errorData.message || error.message, 'Cannot Update Status');
                            } else {
                                showErrorPopup(error.message, 'Error');
                            }
                        } catch (e) {
                            showErrorPopup(error.message, 'Error');
                        }
                    }
                })
                .finally(() => {
                    // Reset button
                    confirmBtn.textContent = originalText;
                    confirmBtn.disabled = false;
                    pendingStatusUpdate = null;
                });
        }




        // Load appointments data
        function loadAppointmentsData() {
            const container = document.getElementById('appointments-content');
            container.innerHTML = '<div class="empty-state"><i class="fas fa-calendar-alt"></i><h4>Loading Appointments...</h4><p>Please wait while we load your appointments.</p></div>';

            fetch('/officer/appointments/')
                .then(response => {
                    console.log('Appointments API response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Appointments API response data:', data);
                    console.log('Appointments data type:', typeof data.appointments);
                    console.log('Appointments is array:', Array.isArray(data.appointments));
                    console.log('Appointments length:', data.appointments ? data.appointments.length : 'undefined');

                    // Handle paginated response
                    const appointments = data.appointments && data.appointments.data ? data.appointments.data : data.appointments;
                    console.log('Processed appointments:', appointments);

                    if (appointments && appointments.length > 0) {
                        renderAppointmentsTable(container, appointments);
                    } else {
                        container.innerHTML = '<div class="empty-state"><i class="fas fa-calendar-alt"></i><h4>No Appointments</h4><p>You don\'t have any appointments assigned.</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading appointments:', error);
                    container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h4>Error Loading Appointments</h4><p>Please try again later.</p></div>';
                });
        }

        // Load documents data
        function loadDocumentsData() {
            const container = document.getElementById('documents-content');
            container.innerHTML = '<div class="empty-state"><i class="fas fa-file-upload"></i><h4>Loading Documents...</h4><p>Please wait while we load your documents.</p></div>';

            fetch('/officer/documents')
                .then(response => {
                    console.log('Documents API response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Documents API response data:', data);
                    console.log('Documents data type:', typeof data.documents);
                    console.log('Documents is array:', Array.isArray(data.documents));
                    console.log('Documents length:', data.documents ? data.documents.length : 'undefined');

                    // Handle paginated response
                    const documents = data.documents && data.documents.data ? data.documents.data : data.documents;
                    console.log('Processed documents:', documents);

                    if (documents && documents.length > 0) {
                        renderDocumentsTable(container, documents);
                    } else {
                        container.innerHTML = '<div class="empty-state"><i class="fas fa-file-upload"></i><h4>No Documents</h4><p>No documents are pending review.</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading documents:', error);
                    container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h4>Error Loading Documents</h4><p>Please try again later.</p></div>';
                });
        }

        // Render appointments table
        function renderAppointmentsTable(container, appointments) {
            let tableHTML = `
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:separate; border-spacing:0 12px;">
                        <thead>
                            <tr style="background:linear-gradient(135deg, var(--teal-primary), var(--teal-light));">
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">APPOINTMENT NO.</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">CLIENT NAME</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">SERVICE TYPE</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">DATE & TIME OF APT.</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">STATUS</th>
                                <th style="text-align:left; padding:16px 20px; font-size:0.875rem; font-weight:600; color:var(--white); white-space:nowrap;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            appointments.forEach(appointment => {
                // Status colors matching the requests table
                const statusColors = {
                    'pending': { bg: '#fef3c7', text: '#f59e0b', label: 'PENDING' },
                    'approved': { bg: '#d1fae5', text: '#10b981', label: 'APPROVED' },
                    'confirmed': { bg: '#dbeafe', text: '#3b82f6', label: 'CONFIRMED' },
                    'completed': { bg: '#10b981', text: '#ffffff', label: 'COMPLETED' },
                    'declined': { bg: '#fee2e2', text: '#dc2626', label: 'DECLINED' },
                    'cancelled': { bg: '#fee2e2', text: '#dc2626', label: 'CANCELLED' }
                };

                const statusInfo = statusColors[appointment.status] || { bg: '#f3f4f6', text: '#6b7280', label: appointment.status.toUpperCase() };

                // Fix date formatting - handle different date formats
                let formattedDateTime = 'Invalid Date';
                try {
                    let dateStr = appointment.appointment_date;
                    let timeStr = appointment.appointment_time;

                    if (dateStr instanceof Date) {
                        dateStr = dateStr.toISOString().split('T')[0];
                    }

                    if (timeStr instanceof Date) {
                        timeStr = timeStr.toTimeString().split(' ')[0];
                    }

                    if (typeof dateStr === 'string') {
                        if (dateStr.includes('T')) {
                            dateStr = dateStr.split('T')[0];
                        }
                    }

                    if (typeof timeStr === 'string') {
                        if (timeStr.includes('T')) {
                            timeStr = timeStr.split('T')[1];
                        }
                        if (timeStr.includes(':')) {
                            const timeParts = timeStr.split(':');
                            timeStr = timeParts[0] + ':' + timeParts[1];
                        }
                    }

                    if (dateStr && timeStr) {
                        const dateTimeStr = dateStr + 'T' + timeStr;
                        const dateObj = new Date(dateTimeStr);
                        if (!isNaN(dateObj.getTime())) {
                            formattedDateTime = dateObj.toLocaleString('en-US', {
                                month: '2-digit',
                                day: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                        } else {
                            formattedDateTime = `${dateStr} ${timeStr}`;
                        }
                    }
                } catch (error) {
                    console.error('Date formatting error:', error);
                    formattedDateTime = `${appointment.appointment_date} ${appointment.appointment_time}`;
                }

                // Format service type name
                let serviceTypeDisplay = appointment.service_type;
                if (serviceTypeDisplay && serviceTypeDisplay.includes('Delayed Regular Non-BRAP')) {
                    serviceTypeDisplay = 'Delayed Birth Registration (Regular)';
                } else if (serviceTypeDisplay && serviceTypeDisplay.includes('Delayed BRAP')) {
                    serviceTypeDisplay = 'Delayed Birth Registration under Birth Registration Assistance Project (BRAP)';
                }

                tableHTML += `
                    <tr style="background:var(--white); border:1px solid var(--gray-200); border-radius:8px; transition:all 0.3s ease;" onmouseover="this.style.background='var(--teal-bg)'; this.style.transform='translateY(-1px)'; this.style.boxShadow='var(--shadow-sm)';" onmouseout="this.style.background='var(--white)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <td style="padding:16px 20px; font-family:monospace; font-size:14px; font-weight:500; color:var(--gray-800);">${appointment.appointment_number}</td>
                        <td style="padding:16px 20px; font-size:14px; font-weight:500; color:var(--gray-800);">${appointment.full_name}</td>
                        <td style="padding:16px 20px; font-size:14px; font-weight:500; color:var(--gray-600);">${serviceTypeDisplay}</td>
                        <td style="padding:16px 20px; font-size:14px; color:var(--gray-600);">${formattedDateTime}</td>
                        <td style="padding:16px 20px;">
                            <span style="background:${statusInfo.bg}; color:${statusInfo.text}; padding:6px 12px; border-radius:6px; font-size:14px; font-weight:600; text-transform:uppercase; display:inline-block;">
                                ${statusInfo.label}
                            </span>
                        </td>
                        <td style="padding:16px 20px; white-space:nowrap;">
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                ${appointment.status === 'pending' ? `
                                    <button onclick="approveAppointment(${appointment.id})" 
                                        style="background:linear-gradient(135deg, #10b981, #059669); color:#fff; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.3s ease; display:inline-flex; align-items:center; gap:6px;"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button onclick="declineAppointment(${appointment.id})" 
                                        style="background:linear-gradient(135deg, #ef4444, #dc2626); color:#fff; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.3s ease; display:inline-flex; align-items:center; gap:6px;"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.3)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                        <i class="fas fa-times"></i> Decline
                                    </button>
                                ` : ''}
                                ${appointment.status === 'approved' ? `
                                    <span style="color:var(--gray-600); font-size:13px; font-style:italic;">Moved to service table</span>
                                ` : ''}
                                ${appointment.status === 'confirmed' ? `
                                    <button onclick="completeAppointment(${appointment.id})" 
                                        style="background:linear-gradient(135deg, #3b82f6, #2563eb); color:#fff; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.3s ease; display:inline-flex; align-items:center; gap:6px;"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.3)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                        <i class="fas fa-check-circle"></i> Complete
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });

            tableHTML += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = tableHTML;
        }

        // Render documents table
        function renderDocumentsTable(container, documents) {
            let html = `
                <div class="table-container">
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th>Document Name</th>
                                <th>Requirement</th>
                                <th>Client Name</th>
                                <th>Upload Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            documents.forEach(document => {
                // Debug: Log document data
                console.log('Document data:', {
                    id: document.id,
                    file_name: document.file_name,
                    civil_service: document.civil_service,
                    appointment: document.appointment,
                    appointment_full_name: document.appointment?.full_name
                });

                const statusClass = getDocumentStatusClass(document.status);
                const uploadDate = new Date(document.created_at).toLocaleDateString();

                html += `
                    <tr class="table-row">
                        <td style="font-size: 14px; font-weight: 500;">${document.file_name}</td>
                        <td style="font-size: 14px; font-weight: 500;">${document.requirement_name}</td>
                        <td style="font-size: 14px; font-weight: 500;">${document.appointment?.full_name || 'N/A'}</td>
                        <td style="color: var(--gray-600); font-size: 14px;">${uploadDate}</td>
                        <td>
                            <span class="status-badge ${statusClass}">
                                ${document.status.charAt(0).toUpperCase() + document.status.slice(1)}
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <button onclick="viewDocument(${document.id})" class="btn-action btn-action-view" title="View Document">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${document.status === 'pending' ? `
                                    <button onclick="approveDocument(${document.id})" class="btn-action btn-action-confirm" title="Approve Document">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectDocument(${document.id})" class="btn-action btn-action-cancel" title="Reject Document">
                                        <i class="fas fa-times"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = html;
        }

        // Helper functions for status classes
        function getAppointmentStatusClass(status) {
            const statusClasses = {
                'pending': 'inquiry',
                'confirmed': 'accepted',
                'completed': 'registered',
                'cancelled': 'rejected',
                'no_show': 'rejected'
            };
            return statusClasses[status] || 'inquiry';
        }

        function getDocumentStatusClass(status) {
            const statusClasses = {
                'pending': 'inquiry',
                'approved': 'registered',
                'rejected': 'rejected'
            };
            return statusClasses[status] || 'inquiry';
        }

        // Appointment management functions
        function approveAppointment(appointmentId) {
            showConfirmationModal('Are you sure you want to approve this appointment? The applicant will receive an SMS notification.', (confirmed) => {
                if (confirmed) {
                    fetch(`/officer/appointments/${appointmentId}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showFloatingNotification('success', 'Appointment Approved', 'The appointment has been approved and SMS notification sent!');
                                loadAppointmentsData();
                                // Reload the appropriate table (regular or brap) based on service type
                                // Check the civil_service data in response to determine which table to reload
                                if (data.civil_service) {
                                    const serviceName = data.civil_service.service_name || '';
                                    const isBrap = serviceName.includes('BRAP') && !serviceName.includes('Non-BRAP');
                                    const tabName = isBrap ? 'brap' : 'regular';
                                    loadTabData(tabName);
                                } else {
                                    // Fallback: reload both tables to ensure the new request appears
                                    loadTabData('regular');
                                    loadTabData('brap');
                                }
                            } else {
                                showFloatingNotification('error', 'Approval Failed', data.error || 'Failed to approve appointment');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showFloatingNotification('error', 'Error', 'An error occurred while approving the appointment');
                        });
                }
            });
        }

        let currentDeclineAppointmentId = null;

        function declineAppointment(appointmentId) {
            currentDeclineAppointmentId = appointmentId;
            document.getElementById('declineReasonInput').value = '';
            document.getElementById('declineAppointmentModal').classList.add('show');
        }

        function closeDeclineAppointmentModal() {
            document.getElementById('declineAppointmentModal').classList.remove('show');
            document.getElementById('declineReasonInput').value = '';
            currentDeclineAppointmentId = null;
        }

        function submitDecline() {
            const reason = document.getElementById('declineReasonInput').value.trim();

            if (!currentDeclineAppointmentId) {
                showFloatingNotification('error', 'Error', 'No appointment selected.');
                return;
            }

            const submitBtn = document.getElementById('submitDeclineBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Declining...';

            fetch(`/officer/appointments/${currentDeclineAppointmentId}/decline`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ reason: reason || null })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDeclineAppointmentModal();
                        showFloatingNotification('success', 'Appointment Declined', 'The appointment has been declined and SMS notification sent!');
                        loadAppointmentsData();
                    } else {
                        showFloatingNotification('error', 'Decline Failed', data.error || 'Failed to decline appointment');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFloatingNotification('error', 'Error', 'An error occurred while declining the appointment');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        function completeAppointment(appointmentId) {
            showConfirmationModal('Are you sure you want to mark this appointment as completed?', (confirmed) => {
                if (confirmed) {
                    fetch(`/officer/appointments/${appointmentId}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showFloatingNotification('success', 'Appointment Completed', 'The appointment has been marked as completed!');
                                loadAppointmentsData();
                            } else {
                                showFloatingNotification('error', 'Completion Failed', data.error || 'Failed to complete appointment');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showFloatingNotification('error', 'Error', 'An error occurred while completing the appointment');
                        });
                } else {
                    showFloatingNotification('info', 'Cancelled', 'Appointment completion cancelled.');
                }
            });
        }

        let currentCancelAppointmentId = null;

        function cancelAppointment(appointmentId) {
            currentCancelAppointmentId = appointmentId;
            document.getElementById('cancelReasonInput').value = '';
            document.getElementById('cancelAppointmentModal').classList.add('show');
        }

        function closeCancelAppointmentModal() {
            document.getElementById('cancelAppointmentModal').classList.remove('show');
            document.getElementById('cancelReasonInput').value = '';
            currentCancelAppointmentId = null;
        }

        function submitCancellation() {
            const reason = document.getElementById('cancelReasonInput').value.trim();

            if (!reason) {
                showFloatingNotification('error', 'Validation Error', 'Please provide a cancellation reason.');
                document.getElementById('cancelReasonInput').focus();
                return;
            }

            if (!currentCancelAppointmentId) {
                showFloatingNotification('error', 'Error', 'No appointment selected.');
                return;
            }

            const submitBtn = document.getElementById('submitCancelBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';

            fetch(`/officer/appointments/${currentCancelAppointmentId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ cancellation_reason: reason })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeCancelAppointmentModal();
                        showFloatingNotification('success', 'Appointment Cancelled', 'The appointment has been cancelled successfully!');
                        loadAppointmentsData();
                    } else {
                        showFloatingNotification('error', 'Cancellation Failed', data.error || 'Failed to cancel appointment');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFloatingNotification('error', 'Error', 'An error occurred while cancelling the appointment');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function () {
            const cancelModal = document.getElementById('cancelAppointmentModal');
            if (cancelModal) {
                cancelModal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeCancelAppointmentModal();
                    }
                });
            }

            const declineModal = document.getElementById('declineAppointmentModal');
            if (declineModal) {
                declineModal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeDeclineAppointmentModal();
                    }
                });
            }
        });

        // Document management functions
        function viewDocument(documentId) {
            window.open(`/officer/documents/${documentId}/view`, '_blank');
        }

        function approveDocument(documentId) {
            showConfirmationModal('Are you sure you want to approve this document?', (confirmed) => {
                if (confirmed) {
                    fetch(`/officer/documents/${documentId}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showFloatingNotification('success', 'Document Approved', 'The document has been approved successfully!');
                                loadDocumentsData();
                            } else {
                                showFloatingNotification('error', 'Approval Failed', data.error || 'Failed to approve document');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showFloatingNotification('error', 'Error', 'An error occurred while approving the document');
                        });
                } else {
                    showFloatingNotification('info', 'Cancelled', 'Document approval cancelled.');
                }
            });
        }

        let currentRejectDocumentId = null;

        function rejectDocument(documentId) {
            currentRejectDocumentId = documentId;
            document.getElementById('rejectReasonInput').value = '';
            document.getElementById('sendRejectionSMS').checked = false;
            document.getElementById('rejectDocumentModal').classList.add('show');
        }

        function closeRejectDocumentModal() {
            document.getElementById('rejectDocumentModal').classList.remove('show');
            document.getElementById('rejectReasonInput').value = '';
            document.getElementById('sendRejectionSMS').checked = false;
            currentRejectDocumentId = null;
        }

        function submitRejection() {
            const reason = document.getElementById('rejectReasonInput').value.trim();
            const sendSMS = document.getElementById('sendRejectionSMS').checked;

            if (!reason) {
                showFloatingNotification('error', 'Validation Error', 'Please provide a rejection reason.');
                document.getElementById('rejectReasonInput').focus();
                return;
            }

            if (!currentRejectDocumentId) {
                showFloatingNotification('error', 'Error', 'No document selected.');
                return;
            }

            const submitBtn = document.getElementById('submitRejectBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rejecting...';

            fetch(`/officer/documents/${currentRejectDocumentId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    rejection_reason: reason,
                    send_sms: sendSMS
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeRejectDocumentModal();
                        let message = 'Document rejected successfully!';
                        if (data.sms_sent) {
                            message += '\n\n📱 SMS notification sent to client.';
                        } else if (data.sms_error) {
                            message += `\n\n⚠️ SMS failed: ${data.sms_error}`;
                        }
                        showFloatingNotification('success', 'Document Rejected', message);
                        loadDocumentsData();
                    } else {
                        showFloatingNotification('error', 'Rejection Failed', data.error || 'Failed to reject document');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFloatingNotification('error', 'Error', 'An error occurred while rejecting the document');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function () {
            const rejectModal = document.getElementById('rejectDocumentModal');
            if (rejectModal) {
                rejectModal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeRejectDocumentModal();
                    }
                });
            }
        });

        // Load initial data when page loads (for Civil Registry officers)
        // --- Floating Notification Functions ---
        function showFloatingNotification(type, title, message) {
            const notification = document.getElementById('floating-notification');
            const icon = notification.querySelector('.notification-icon');
            const notificationTitle = notification.querySelector('.notification-title');
            const notificationMessage = notification.querySelector('.notification-message');

            // Reset classes
            notification.className = 'floating-notification';
            icon.innerHTML = ''; // Clear previous icon

            notification.classList.add(type);
            notificationTitle.textContent = title;
            notificationMessage.textContent = message;

            if (type === 'success') {
                icon.innerHTML = '<i class="fas fa-check-circle"></i>';
            } else if (type === 'error') {
                icon.innerHTML = '<i class="fas fa-times-circle"></i>';
            } else if (type === 'warning') {
                icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
            } else if (type === 'info') {
                icon.innerHTML = '<i class="fas fa-info-circle"></i>';
            }

            notification.style.display = 'flex';
            notification.style.animation = 'slideInRight 0.5s ease-out forwards';

            // Auto-hide after 5 seconds
            setTimeout(() => {
                notification.style.animation = 'fadeOut 0.5s ease-out forwards';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 500);
            }, 5000);
        }

        // Close button for notification
        document.addEventListener('DOMContentLoaded', () => {
            const closeNotificationBtn = document.querySelector('#floating-notification .close-notification');
            if (closeNotificationBtn) {
                closeNotificationBtn.addEventListener('click', () => {
                    const notification = document.getElementById('floating-notification');
                    notification.style.animation = 'fadeOut 0.5s ease-out forwards';
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 500);
                });
            }
        });

        // --- Confirmation Modal Functions ---
        let confirmActionCallback = null;

        function showConfirmationModal(message, callback) {
            const modal = document.getElementById('confirmation-modal');
            const modalMessage = modal.querySelector('.modal-message');
            const confirmYesBtn = document.getElementById('confirm-yes');
            const confirmNoBtn = document.getElementById('confirm-no');

            modalMessage.textContent = message;
            modal.style.display = 'flex';

            confirmActionCallback = callback;

            const handleYes = () => {
                modal.style.display = 'none';
                if (confirmActionCallback) {
                    confirmActionCallback(true);
                }
                confirmYesBtn.removeEventListener('click', handleYes);
                confirmNoBtn.removeEventListener('click', handleNo);
            };

            const handleNo = () => {
                modal.style.display = 'none';
                if (confirmActionCallback) {
                    confirmActionCallback(false);
                }
                confirmYesBtn.removeEventListener('click', handleYes);
                confirmNoBtn.removeEventListener('click', handleNo);
            };

            confirmYesBtn.addEventListener('click', handleYes);
            confirmNoBtn.addEventListener('click', handleNo);
        }

        // Queue Table Functions
        function loadQueueData() {
            const container = document.getElementById('queue-table-container');
            if (!container) return;

            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <h4>Loading queue data...</h4>
                    <p>Please wait while we load today's queue information.</p>
                </div>
            `;

            // Get window number - you can set this based on officer's window assignment
            // For now, we'll get it from a data attribute or use null for all windows
            const windowNumber = document.getElementById('queue-table-section')?.dataset?.window || null;
            const url = '{{ route("officer.queue-data") }}' + (windowNumber ? `?window=${windowNumber}` : '');

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Queue data received:', data);
                    console.log('Columns config:', data.columns);
                    console.log('Column names:', data.column_names);
                    
                    // Always render the display, even with empty data, to show all columns
                    if (data.success) {
                        const queueData = data.data || [];
                        console.log('Rendering queue table with', queueData.length, 'items');
                        // Check for newly called queues and play chime
                        if (queueData.length > 0) {
                            checkForNewCalledQueues(queueData);
                        }
                        renderQueueTable(queueData, data.date, data.column_names, data.queue_prefix, data.columns);
                    } else {
                        // Fallback: render with empty data but still show columns
                        renderQueueTable([], data.date, data.column_names, data.queue_prefix, data.columns);
                    }
                })
                .catch(error => {
                    console.error('Error loading queue data:', error);
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h4>Error Loading Queue Data</h4>
                            <p>Failed to load queue information. Please try again later.</p>
                        </div>
                    `;
                });
        }
        
        // Track previously called queues to detect new calls
        let previousCalledQueues = new Set();
        // Track previous queue states (queue_number -> status) to detect status changes
        let previousQueueStates = new Map();
        
        // Audio context for playing chime sound
        let audioContext = null;
        let audioInitialized = false;
        
        // Initialize audio - resume context if suspended (browser requires user interaction)
        function initQueueAudio() {
            if (audioInitialized && audioContext) {
                // Resume audio context if suspended (browser security requirement)
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(() => {
                        console.log('Audio context resumed');
                    }).catch(err => {
                        console.warn('Failed to resume audio context:', err);
                    });
                }
                return;
            }
            
            try {
                // Create a simple chime sound using Web Audio API
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                audioInitialized = true;
                console.log('Audio context initialized, state:', audioContext.state);
                
                // Resume if suspended
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(() => {
                        console.log('Audio context resumed on init');
                    });
                }
            } catch (e) {
                console.warn('Web Audio API not supported:', e);
            }
        }
        
        // Play chime sound when queue is called
        function playQueueChime() {
            console.log('Attempting to play chime...');
            
            if (!audioContext) {
                initQueueAudio();
            }
            
            if (!audioContext) {
                console.warn('Audio context not available');
                return;
            }
            
            // Resume audio context if suspended (required by browser security)
            if (audioContext.state === 'suspended') {
                audioContext.resume().then(() => {
                    console.log('Audio context resumed, playing chime');
                    playQueueChimeSound();
                }).catch(err => {
                    console.warn('Failed to resume audio context:', err);
                    // Fallback: try to play anyway
                    playQueueChimeSound();
                });
            } else {
                playQueueChimeSound();
            }
        }
        
        // Actual chime sound generation - Enhanced doorbell-style chime
        function playQueueChimeSound() {
            try {
                const now = audioContext.currentTime;
                
                // Create a rich, attention-grabbing doorbell chime
                // Using multiple oscillators for a fuller sound
                
                // === First Chime (Ding) ===
                const osc1 = audioContext.createOscillator();
                const osc1b = audioContext.createOscillator(); // Harmonic
                const gain1 = audioContext.createGain();
                
                osc1.connect(gain1);
                osc1b.connect(gain1);
                gain1.connect(audioContext.destination);
                
                osc1.frequency.setValueAtTime(659.25, now); // E5
                osc1.type = 'sine';
                osc1b.frequency.setValueAtTime(1318.5, now); // E6 (harmonic)
                osc1b.type = 'sine';
                
                gain1.gain.setValueAtTime(0, now);
                gain1.gain.linearRampToValueAtTime(0.5, now + 0.02);
                gain1.gain.exponentialRampToValueAtTime(0.2, now + 0.3);
                gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.8);
                
                osc1.start(now);
                osc1b.start(now);
                osc1.stop(now + 0.8);
                osc1b.stop(now + 0.8);
                
                // === Second Chime (Dong) - Lower tone ===
                const osc2 = audioContext.createOscillator();
                const osc2b = audioContext.createOscillator(); // Harmonic
                const gain2 = audioContext.createGain();
                
                osc2.connect(gain2);
                osc2b.connect(gain2);
                gain2.connect(audioContext.destination);
                
                osc2.frequency.setValueAtTime(523.25, now + 0.15); // C5
                osc2.type = 'sine';
                osc2b.frequency.setValueAtTime(1046.5, now + 0.15); // C6 (harmonic)
                osc2b.type = 'sine';
                
                gain2.gain.setValueAtTime(0, now + 0.15);
                gain2.gain.linearRampToValueAtTime(0.45, now + 0.17);
                gain2.gain.exponentialRampToValueAtTime(0.15, now + 0.5);
                gain2.gain.exponentialRampToValueAtTime(0.01, now + 1.0);
                
                osc2.start(now + 0.15);
                osc2b.start(now + 0.15);
                osc2.stop(now + 1.0);
                osc2b.stop(now + 1.0);
                
                // === Third Chime (Resolution) - Pleasant ending ===
                const osc3 = audioContext.createOscillator();
                const gain3 = audioContext.createGain();
                
                osc3.connect(gain3);
                gain3.connect(audioContext.destination);
                
                osc3.frequency.setValueAtTime(783.99, now + 0.4); // G5
                osc3.type = 'sine';
                
                gain3.gain.setValueAtTime(0, now + 0.4);
                gain3.gain.linearRampToValueAtTime(0.35, now + 0.42);
                gain3.gain.exponentialRampToValueAtTime(0.1, now + 0.8);
                gain3.gain.exponentialRampToValueAtTime(0.01, now + 1.5);
                
                osc3.start(now + 0.4);
                osc3.stop(now + 1.5);
                
                // === Subtle shimmer effect ===
                const shimmer = audioContext.createOscillator();
                const shimmerGain = audioContext.createGain();
                
                shimmer.connect(shimmerGain);
                shimmerGain.connect(audioContext.destination);
                
                shimmer.frequency.setValueAtTime(2637, now); // E7 (high shimmer)
                shimmer.type = 'sine';
                
                shimmerGain.gain.setValueAtTime(0, now);
                shimmerGain.gain.linearRampToValueAtTime(0.08, now + 0.05);
                shimmerGain.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
                
                shimmer.start(now);
                shimmer.stop(now + 0.5);
                
                console.log('Enhanced chime played successfully');
            } catch (e) {
                console.warn('Error playing chime:', e);
            }
        }
        
        // Check if status is in "To Submit" category
        function isToSubmitStatus(status) {
            const statusLower = (status || '').toLowerCase();
            const toSubmitStatuses = ['accepted', 'checklist_given', 'incomplete_requirements', 
                                     'to_be_encoded_brap', 'negative_result', 'accepted_octogenarian', 
                                     'verified_with_problem', 'for_compliance'];
            return toSubmitStatuses.includes(statusLower);
        }
        
        // Check if status is in "On Queue" category
        function isOnQueueStatus(status) {
            const statusLower = (status || '').toLowerCase();
            return statusLower === 'inquiry' || statusLower === 'submitted';
        }
        
        // Check for newly called queues and play chime
        function checkForNewCalledQueues(queueData) {
            const currentCalledQueues = new Set();
            const currentQueueStates = new Map();
            let shouldPlayChime = false;
            
            console.log('Checking for new called queues. Previous states:', Array.from(previousQueueStates.entries()));
            
            // Collect all currently called queue numbers and their states
            queueData.forEach(item => {
                const queueKey = item.queue_number;
                const status = (item.status || '').toLowerCase();
                currentQueueStates.set(queueKey, status);
                
                console.log(`Queue ${queueKey}: status=${status}, is_called=${item.is_called}, called_at=${item.called_at}`);
                
                // Check if queue was called (has called_at timestamp)
                if (item.is_called && item.called_at) {
                    const calledKey = `${item.queue_number}_${item.called_at}`;
                    currentCalledQueues.add(calledKey);
                    
                    // If this is a new called queue, play chime
                    if (!previousCalledQueues.has(calledKey)) {
                        console.log('NEW: Queue called:', item.queue_number, 'at', item.called_at);
                        shouldPlayChime = true;
                    }
                }
                
                // Also check if queue status changed from "On Queue" to "To Submit"
                const previousStatus = previousQueueStates.get(queueKey);
                if (previousStatus) {
                    console.log(`Queue ${queueKey}: Previous status=${previousStatus}, Current status=${status}`);
                    if (isOnQueueStatus(previousStatus) && isToSubmitStatus(status)) {
                        console.log('NEW: Queue moved to To Submit:', item.queue_number, 'from', previousStatus, 'to', status);
                        shouldPlayChime = true;
                    }
                } else {
                    // First time seeing this queue - if it's already in "To Submit", don't play (it was already there)
                    // But if it just moved there, we need to check if called_at was just set
                    if (isToSubmitStatus(status) && item.is_called && item.called_at) {
                        // Check if called_at is recent (within last 30 seconds)
                        const calledAtTime = new Date(item.called_at).getTime();
                        const now = Date.now();
                        const timeDiff = (now - calledAtTime) / 1000; // in seconds
                        if (timeDiff < 30) {
                            console.log('NEW: Queue just moved to To Submit (recent called_at):', item.queue_number);
                            shouldPlayChime = true;
                        }
                    }
                }
            });
            
            // Play chime if needed
            if (shouldPlayChime) {
                console.log('Playing chime for queue status change');
                playQueueChime();
            }
            
            // Update previous called queues set and queue states
            previousCalledQueues = currentCalledQueues;
            previousQueueStates = currentQueueStates;
        }

        function renderQueueTable(queueData, date, columnNames = null, queuePrefix = null, columnsConfig = null) {
            const container = document.getElementById('queue-table-container');
            console.log('Rendering queue table with data:', queueData);
            console.log('renderQueueTable - columnsConfig:', columnsConfig);
            console.log('renderQueueTable - columnNames:', columnNames);
            
            // Get department for prefix fallback
            const department = queueData.length > 0 && queueData[0].department 
                ? queueData[0].department 
                : 'Civil Registry Office';
            
            // Use provided queue prefix or generate default from department
            const prefix = queuePrefix && queuePrefix.trim() !== '' 
                ? queuePrefix.trim().toUpperCase() 
                : getDepartmentCode(department);

            // Use provided columns configuration or fallback to column names or defaults
            let columns = columnsConfig;
            console.log('renderQueueTable - columnsConfig received:', columnsConfig);
            console.log('renderQueueTable - columnNames received:', columnNames);
            
            if (!columns || !Array.isArray(columns) || columns.length === 0) {
                console.log('Using fallback columns - columnsConfig was empty or invalid');
                // Fallback to old columnNames format or defaults
                const cols = columnNames || {
                    on_queue: 'On Queue',
                    to_submit: 'To Submit',
                    processed: 'Processed'
                };
                
                // Convert old format to new format
                columns = [
                    { key: 'on_queue', name: cols.on_queue || 'On Queue', statuses: ['inquiry', 'submitted'] },
                    { key: 'to_submit', name: cols.to_submit || 'To Submit', statuses: ['accepted', 'checklist_given', 'incomplete_requirements', 'rejected'] },
                    { key: 'processed', name: cols.processed || 'Processed', statuses: ['registered', 'completed', 'positive_result', 'negative_result', 'encoded_brap', 'psa_processing'] }
                ];
            }
            
            // Ensure all columns have valid names - fix any empty names
            columns = columns.map((col, idx) => {
                if (!col.name || !col.name.trim()) {
                    // Generate name from key if name is empty
                    let generatedName = 'Unnamed Column';
                    if (col.key && col.key.trim()) {
                        generatedName = col.key
                            .split('_')
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                            .join(' ');
                    } else {
                        generatedName = `Column ${idx + 1}`;
                    }
                    console.warn(`Column ${col.key || 'unnamed'} has empty name, using: ${generatedName}`);
                    return { ...col, name: generatedName };
                }
                return col;
            });
            
            console.log('Final columns to render:', columns);
            console.log('Number of columns:', columns.length);
            columns.forEach((col, idx) => {
                console.log(`Column ${idx + 1}: key="${col.key}", name="${col.name}", statuses=[${col.statuses.join(', ')}]`);
            });

            // Categorize items into columns based on status
            const columnData = {};
            columns.forEach(col => {
                columnData[col.key] = [];
            });

            // Log all column configurations for debugging
            console.log('Available columns for matching:', columns.map(c => ({
                key: c.key,
                name: c.name,
                statuses: c.statuses
            })));

            queueData.forEach(item => {
                // Normalize item status: lowercase and trim
                const rawStatus = item.status || '';
                const status = String(rawStatus).toLowerCase().trim();
                const serviceType = (item.service_type || '').toLowerCase();
                console.log('Categorizing item:', item.tracking_number, 'status:', status, 'raw status:', rawStatus, 'service_type:', serviceType);
                
                let categorized = false;
                
                // Check each column's statuses to find a match
                for (const col of columns) {
                    if (col.statuses && Array.isArray(col.statuses) && col.statuses.length > 0) {
                        // Normalize statuses for comparison (trim and lowercase)
                        const normalizedStatuses = col.statuses.map(s => {
                            const normalized = String(s).toLowerCase().trim();
                            return normalized;
                        });
                        
                        console.log(`  Checking column "${col.name || col.key}" (key: ${col.key}): statuses=`, normalizedStatuses, 'looking for:', status);
                        
                        // Direct match after normalization
                        if (normalizedStatuses.includes(status)) {
                            console.log(`  ✓ MATCHED to column: ${col.name || col.key} (status: "${status}" matches one of: ${normalizedStatuses.join(', ')})`);
                            columnData[col.key].push(item);
                            categorized = true;
                            break;
                        } else {
                            console.log(`  ✗ No match in column "${col.name || col.key}": "${status}" not in [${normalizedStatuses.join(', ')}]`);
                        }
                    } else {
                        console.warn(`  Column "${col.name || col.key}" has no statuses array or empty statuses`);
                    }
                }
                
                // If no match found, put in first column as fallback
                if (!categorized && columns.length > 0) {
                    console.warn(`  ✗ No match found for status "${status}" (raw: "${rawStatus}"), using fallback column: ${columns[0].name || columns[0].key}`);
                    columnData[columns[0].key].push(item);
                }
            });
            
            console.log('Queue categorization:', Object.keys(columnData).reduce((acc, key) => {
                acc[key] = columnData[key].length;
                return acc;
            }, {}));

            // Sort each group by queue number
            const sortByQueueNumber = (a, b) => {
                const numA = parseInt(a.queue_number) || 0;
                const numB = parseInt(b.queue_number) || 0;
                return numA - numB;
            };

            Object.keys(columnData).forEach(key => {
                columnData[key].sort(sortByQueueNumber);
            });

            // Render queue item
            const renderQueueItem = (item) => {
                const serviceType = escapeHtml(item.service_type || 'N/A');
                return `
                    <div class="queue-item">
                        <div class="queue-item-main" onclick="window.location.href='/officer/requests/${item.tracking_number}/details'">
                            <div class="queue-item-number">
                                ${escapeHtml(prefix)} #${item.queue_number}
                            </div>
                            <div class="queue-item-details">
                                <div><strong>${escapeHtml(item.client_name)}</strong></div>
                                <div style="margin-top: 6px; padding: 6px 10px; background: #f0f4f8; border-radius: 6px; font-size: 0.85rem; color: #475569; font-weight: 500; display: flex; align-items: center; gap: 6px; min-width: 0;">
                                    <i class="fas fa-tag" style="font-size: 0.75rem; color: #64748b; flex-shrink: 0;"></i>
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; flex: 1;">${serviceType}</span>
                                </div>
                                <div class="queue-item-time">
                                    <i class="fas fa-clock"></i>
                                    ${item.time_submitted}
                                </div>
                            </div>
                        </div>
                        <div class="queue-item-actions">
                            <a href="/officer/requests/${item.tracking_number}/details" class="btn-view-details">View Details</a>
                            <button onclick="removeFromQueue('${item.tracking_number}', '${escapeHtml(prefix)} #${item.queue_number}', event)" 
                                    class="btn-remove-queue" 
                                    title="Remove from queue">
                                <i class="fas fa-times"></i>
                                Remove
                            </button>
                        </div>
                    </div>
                `;
            };

            // Render column content
            const renderColumn = (items, columnClass) => {
                if (items.length === 0) {
                    return `
                        <div class="queue-empty-state">
                            <i class="fas fa-inbox"></i>
                            <h4>No Items</h4>
                            <p>No queue numbers in this category</p>
                        </div>
                    `;
                }
                return items.map(item => renderQueueItem(item)).join('');
            };

            // Build columns dynamically - always show all columns even if empty
            let columnsHTML = '';
            columns.forEach((col, index) => {
                const items = columnData[col.key] || [];
                
                // Get column name - ensure it's never empty
                let columnName = '';
                if (col.name && col.name.trim()) {
                    columnName = col.name.trim();
                } else if (col.key) {
                    // Generate from key: "to_be_encoded_brap" -> "To Be Encoded Brap"
                    columnName = col.key
                        .split('_')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                        .join(' ');
                } else {
                    columnName = `Column ${index + 1}`;
                }
                
                console.log(`Rendering column ${index + 1}/${columns.length}: key="${col.key}", name="${columnName}", items=${items.length}`);
                
                // Determine header background color with fallback colors
                // First column: dark gray, Last column: green, Middle columns: teal
                // Use direct hex colors as fallback in case CSS variables aren't defined
                let headerBg = '';
                if (index === 0) {
                    headerBg = '#475569'; // gray-600 fallback
                } else if (index === columns.length - 1) {
                    headerBg = '#059669'; // green-600 fallback
                } else {
                    // Middle columns: cycle through different teal shades for variety
                    const tealShades = ['#008080', '#00A0A0', '#006666', '#0e90a6', '#087a93'];
                    const shadeIndex = (index - 1) % tealShades.length;
                    headerBg = tealShades[shadeIndex];
                }
                
                columnsHTML += `
                    <div class="queue-column ${col.key}" style="display: flex; flex-direction: column; min-width: 0; flex: 1; overflow: visible;">
                        <div class="queue-column-header ${col.key}" style="padding: 16px; background: ${headerBg} !important; color: #ffffff !important; border-radius: 8px 8px 0 0; font-weight: 700; font-size: 1rem; min-height: 60px; display: flex; flex-direction: column; justify-content: center; box-sizing: border-box; overflow: visible; position: relative; z-index: 1;">
                            <div style="display: block; line-height: 1.3; color: #ffffff !important; font-weight: 700; font-size: 1rem; word-wrap: break-word; overflow-wrap: break-word; hyphens: auto; overflow: visible; white-space: normal;">${escapeHtml(columnName)}</div>
                            <div style="font-size: 0.875rem; font-weight: 500; margin-top: 4px; opacity: 0.9; color: #ffffff !important;">
                                ${items.length} ${items.length === 1 ? 'item' : 'items'}
                            </div>
                        </div>
                        <div class="queue-column-content" style="flex: 1; overflow-y: auto; min-height: 200px;">
                            ${renderColumn(items, col.key)}
                        </div>
                    </div>
                `;
            });

            // If no columns were configured, show a message
            if (columns.length === 0) {
                console.error('No columns to render!');
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>No Columns Configured</h4>
                        <p>Please configure queue columns in the Queue Number Settings.</p>
                            </div>
                `;
                return;
            }

            let displayHTML = `
                <div class="queue-display-board" style="display: grid; grid-template-columns: repeat(${columns.length}, minmax(200px, 1fr)); gap: 20px; width: 100%; box-sizing: border-box; overflow-x: auto; overflow-y: visible;">
                    ${columnsHTML}
                </div>
            `;

            console.log('Rendering queue display with', columns.length, 'columns:', columns.map(c => ({ key: c.key, name: c.name || 'EMPTY', statuses: c.statuses })));
            container.innerHTML = displayHTML;
        }

        function refreshQueueData() {
            loadQueueData();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Map department names to their codes
        function getDepartmentCode(departmentName) {
            if (!departmentName) return '';
            
            const departmentMap = {
                'Business Permit and Licensing Office': 'BPLO',
                'City Transportation Regulation Unit': 'CTRU',
                'City Engineering Office': 'CEO',
                'Civil Registry Office': 'CRO',
                // Add more departments as needed
            };
            
            // Return the code if found, otherwise generate from department name
            if (departmentMap[departmentName]) {
                return departmentMap[departmentName];
            }
            
            // Fallback: Generate code from department name (first letters of each word)
            const words = departmentName.trim().split(/\s+/);
            if (words.length === 1) {
                return words[0].substring(0, 4).toUpperCase();
            }
            return words.map(word => word.charAt(0).toUpperCase()).join('').substring(0, 4);
        }

        // Store current removal data
        let currentRemovalData = {
            trackingNumber: null,
            queueDisplay: null,
            button: null,
            originalHTML: null
        };

        // Show queue removal confirmation modal
        function showQueueRemoveModal(trackingNumber, queueDisplay, event) {
            // Prevent event propagation to avoid triggering the item click
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }

            // Store removal data
            currentRemovalData.trackingNumber = trackingNumber;
            currentRemovalData.queueDisplay = queueDisplay;
            currentRemovalData.button = event ? event.target.closest('.btn-remove-queue') : document.querySelector(`button[onclick*="${trackingNumber}"]`);
            
            if (currentRemovalData.button) {
                currentRemovalData.originalHTML = currentRemovalData.button.innerHTML;
            }

            // Update modal content
            document.getElementById('queue-remove-display-name').textContent = queueDisplay;
            
            // Show modal
            document.getElementById('queue-remove-modal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Close queue removal modal
        function closeQueueRemoveModal() {
            document.getElementById('queue-remove-modal').style.display = 'none';
            document.body.style.overflow = '';
            currentRemovalData = {
                trackingNumber: null,
                queueDisplay: null,
                button: null,
                originalHTML: null
            };
        }

        // Confirm queue removal
        function confirmQueueRemove() {
            const { trackingNumber, queueDisplay, button } = currentRemovalData;
            
            if (!trackingNumber || !button) {
                closeQueueRemoveModal();
                return;
            }

            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing...';

            fetch('/officer/queue/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tracking_number: trackingNumber
                }),
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                closeQueueRemoveModal();
                
                if (data.success) {
                    // Show success message
                    showNotification('success', 'Queue Removed', `${queueDisplay} has been removed from the queue.`);
                    
                    // Reload queue data
                    loadQueueData();
                } else {
                    // Show error message
                    showNotification('error', 'Error', data.message || 'Failed to remove from queue.');
                    button.disabled = false;
                    button.innerHTML = currentRemovalData.originalHTML;
                }
            })
            .catch(error => {
                console.error('Error removing from queue:', error);
                closeQueueRemoveModal();
                showNotification('error', 'Error', 'An error occurred while removing from queue.');
                button.disabled = false;
                button.innerHTML = currentRemovalData.originalHTML;
            });
        }

        // Remove item from queue (opens modal)
        function removeFromQueue(trackingNumber, queueDisplay, event) {
            showQueueRemoveModal(trackingNumber, queueDisplay, event);
        }

        // Animate statistics charts on page load
        function animateStatsCharts() {
            const charts = document.querySelectorAll('.stats-chart-progress');
            const circumference = 2 * Math.PI * 50; // radius is 50
            
            charts.forEach((chart, index) => {
                // Get the current stroke-dashoffset from the SVG attribute
                const currentOffset = chart.getAttribute('stroke-dashoffset');
                const targetOffset = parseFloat(currentOffset) || 0;
                
                // Start from full circle (circumference) - hidden
                chart.style.strokeDashoffset = circumference;
                chart.style.opacity = '0';
                
                // Animate to target with delay for staggered effect
                setTimeout(() => {
                    chart.style.transition = 'stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.5s ease';
                    chart.style.opacity = '1';
                    chart.style.strokeDashoffset = targetOffset;
                }, index * 150); // Stagger animation by 150ms per card
            });
        }

        // Posting days (10-day posting) text sync
        const postingDaysKeyDashboard = `posting_days_${'{{ $officer->department }}'.replace(/\s+/g, '_').toLowerCase()}`;
        const defaultPostingDaysDashboard = 10;

        function getPostingDaysDashboard() {
            const stored = localStorage.getItem(postingDaysKeyDashboard);
            const num = parseInt(stored, 10);
            if (!isNaN(num) && num > 0) return num;
            return defaultPostingDaysDashboard;
        }

        function updatePostingDaysTexts() {
            const days = getPostingDaysDashboard();
            const subtitle = document.getElementById('approvalPostingSubtitle');
            const card = document.getElementById('approvalPostingCard');
            if (subtitle) {
                subtitle.textContent = `Review on going requests under ${days}-day posting period`;
            }
            if (card) {
                card.textContent = `Tap to view ${days} Days Posting Requests`;
            }
        }

        // Week Navigation Variables
        let currentWeekOffset = 0; // 0 = current week, -1 = last week, etc.
        const maxWeeksBack = 12; // Allow viewing up to 12 weeks back
        
        function navigateWeek(direction) {
            const newOffset = currentWeekOffset + direction;
            
            // Don't go into the future (offset > 0) or too far back
            if (newOffset > 0 || newOffset < -maxWeeksBack) {
                return;
            }
            
            currentWeekOffset = newOffset;
            updateWeekNavButtons();
            fetchWeeklyOverview(currentWeekOffset);
        }
        
        function updateWeekNavButtons() {
            const prevBtn = document.getElementById('prevWeekBtn');
            const nextBtn = document.getElementById('nextWeekBtn');
            const currentBadge = document.getElementById('currentWeekBadge');
            
            // Disable next button if at current week
            nextBtn.disabled = currentWeekOffset >= 0;
            
            // Disable prev button if at max weeks back
            prevBtn.disabled = currentWeekOffset <= -maxWeeksBack;
            
            // Show/hide current week badge
            if (currentWeekOffset === 0) {
                currentBadge.style.display = 'inline';
            } else {
                currentBadge.style.display = 'none';
            }
        }
        
        function fetchWeeklyOverview(weekOffset) {
            const dateDisplay = document.getElementById('weekDateDisplay');
            const weekDateRange = document.getElementById('weekDateRange');
            const weeklyBarChart = document.getElementById('weeklyBarChart');
            const weekRequestsCount = document.getElementById('weekRequestsCount');
            
            // Show loading state
            dateDisplay.classList.add('loading');
            
            fetch(`{{ route('officer.weekly-overview') }}?week_offset=${weekOffset}`, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                dateDisplay.classList.remove('loading');
                
                if (data.success) {
                    // Update date range display
                    weekDateRange.textContent = `${data.start_date} — ${data.end_date}`;
                    
                    // Update week total
                    weekRequestsCount.textContent = data.week_total;
                    
                    // Update bar chart
                    updateBarChart(data.weekly_data);
                    
                    // Update week change indicator
                    updateWeekChangeIndicator(data.week_change);
                }
            })
            .catch(error => {
                console.error('Error fetching weekly overview:', error);
                dateDisplay.classList.remove('loading');
            });
        }
        
        function updateBarChart(weeklyData) {
            const barChart = document.getElementById('weeklyBarChart');
            const maxValue = Math.max(...Object.values(weeklyData), 1);
            
            let barsHtml = '';
            for (const [day, count] of Object.entries(weeklyData)) {
                const heightPercent = (count / maxValue) * 100;
                barsHtml += `
                    <div class="bar-item">
                        <div class="bar-wrapper">
                            <div class="bar" style="height: ${heightPercent}%;">
                                <span class="bar-value">${count}</span>
                            </div>
                        </div>
                        <span class="bar-label">${day}</span>
                    </div>
                `;
            }
            
            barChart.innerHTML = barsHtml;
        }
        
        function updateWeekChangeIndicator(weekChange) {
            const weekStatBox = document.querySelector('.overview-stat-box.week');
            if (!weekStatBox) return;
            
            const changeDiv = weekStatBox.querySelector('.overview-stat-change');
            if (changeDiv) {
                const isPositive = weekChange >= 0;
                changeDiv.className = `overview-stat-change ${isPositive ? 'positive' : 'negative'}`;
                changeDiv.innerHTML = `
                    <i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i>
                    ${Math.abs(weekChange)}%
                    <span class="date-label">vs previous week</span>
                `;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            console.log('Page loaded, officer department:', '{{ $officer->department }}');
            
            // Initialize week navigation buttons
            updateWeekNavButtons();
            
            // Sync posting days text for approval/quick action
            updatePostingDaysTexts();
            
            // Initialize modal confirm button
            const confirmBtn = document.getElementById('confirm-queue-remove');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', confirmQueueRemove);
            }
            
            // Animate statistics charts
            setTimeout(animateStatsCharts, 300); // Small delay to ensure DOM is ready
            
            @if($queueEnabled)
                // Initialize audio for queue chime
                initQueueAudio();
                
                // Enable audio on any user interaction (required by browser security)
                document.addEventListener('click', function() {
                    if (audioContext && audioContext.state === 'suspended') {
                        audioContext.resume().then(() => {
                            console.log('Audio enabled by user interaction');
                        });
                    }
                }, { once: true });
                
                // Also enable on touch/scroll
                document.addEventListener('touchstart', function() {
                    if (audioContext && audioContext.state === 'suspended') {
                        audioContext.resume();
                    }
                }, { once: true });
                
                loadQueueData();
                // Auto-refresh queue data every 30 seconds
                setInterval(loadQueueData, 30000);
            @endif
            @if($officer->department === 'Civil Registry Office')
                loadTabData('brap');
            @endif
        });
    </script>

    <!-- Floating Notification -->
    <div id="floating-notification" class="floating-notification" style="display: none;">
        <div class="notification-icon"></div>
        <div class="notification-content">
            <h5 class="notification-title"></h5>
            <p class="notification-message"></p>
        </div>
        <button class="close-notification"><i class="fas fa-times"></i></button>
    </div>

    <!-- Confirmation Modal -->
    <!-- Queue Removal Confirmation Modal -->
    <div id="queue-remove-modal" class="queue-remove-modal" style="display: none;">
        <div class="queue-remove-modal-overlay" onclick="closeQueueRemoveModal()"></div>
        <div class="queue-remove-modal-content">
            <div class="queue-remove-modal-header">
                <div class="queue-remove-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="queue-remove-modal-title">Remove from Queue</h3>
            </div>
            <div class="queue-remove-modal-body">
                <p class="queue-remove-modal-message">
                    Are you sure you want to remove <strong id="queue-remove-display-name"></strong> from the queue?
                </p>
                <p class="queue-remove-modal-note">
                    <i class="fas fa-info-circle"></i>
                    This will only remove it from the queue display. The request will remain in the system and can still be accessed.
                </p>
            </div>
            <div class="queue-remove-modal-footer">
                <button onclick="closeQueueRemoveModal()" class="btn-modal-cancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button id="confirm-queue-remove" class="btn-modal-confirm">
                    <i class="fas fa-trash"></i> Remove from Queue
                </button>
            </div>
        </div>
    </div>

    <div id="confirmation-modal" class="confirmation-modal" style="display: none;">
        <div class="modal-content">
            <h4 class="modal-title">Confirm Action</h4>
            <p class="modal-message"></p>
            <div class="modal-actions">
                <button id="confirm-yes" class="btn btn-success">Yes</button>
                <button id="confirm-no" class="btn btn-danger">No</button>
            </div>
        </div>
    </div>

    <!-- Appointment Decline Modal -->
    <div id="declineAppointmentModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2 class="modal-title">
                    Decline Appointment
                </h2>
                <button onclick="closeDeclineAppointmentModal()" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 16px; color: var(--gray-700);">
                    Decline this appointment if the client was late or did not attend. An SMS notification will be sent
                    to the applicant.
                </p>
                <div class="form-group">
                    <label class="form-label">Additional Remarks (Optional)</label>
                    <textarea id="declineReasonInput" class="form-input" rows="4"
                        placeholder="Enter additional message or reason for declining (optional). This will be included in the SMS notification."
                        style="resize: vertical; min-height: 100px;"></textarea>
                    <small style="color: var(--gray-500); font-size: 12px; margin-top: 4px; display: block;">
                        If provided, this message will be included in the SMS notification sent to the applicant.
                    </small>
                </div>
            </div>
            <div class="modal-footer"
                style="display: flex; justify-content: flex-end; gap: 12px; padding: 20px; border-top: 1px solid var(--gray-200);">
                <button onclick="closeDeclineAppointmentModal()" class="btn btn-secondary" style="padding: 10px 20px;">
                    <i class="fas fa-times"></i> Close
                </button>
                <button onclick="submitDecline()" class="btn btn-danger" style="padding: 10px 20px;"
                    id="submitDeclineBtn">
                    <i class="fas fa-ban"></i> Decline Appointment
                </button>
            </div>
        </div>
    </div>

    <!-- Error Popup Modal -->
    <div id="errorPopupModal" class="error-popup-modal">
        <div class="error-popup-modal-content">
            <div class="error-popup-header">
                <h3 id="errorPopupTitle" class="error-popup-title">Error</h3>
            </div>
            <div class="error-popup-body">
                <div id="errorMessage" class="error-popup-message"></div>
            </div>
            <div class="error-popup-footer">
                <button onclick="closeErrorPopup()" class="btn-modal btn-modal-primary" style="background: #dc2626; color: white;">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>
        </div>
    </div>
</body>

</html>
</html>
</html>
</html>