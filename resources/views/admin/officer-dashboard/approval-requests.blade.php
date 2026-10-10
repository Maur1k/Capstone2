<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>10 Days Posting - {{ $officer->department }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --teal-primary: #008080;
            --teal-light: #00A0A0;
            --teal-dark: #006666;
            --teal-accent: #4DD0E1;
            --teal-bg: #E0F7FA;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 32px;
            position: relative;
        }

        .header {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
            border-radius: 12px;
            padding: 28px 32px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-md);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-title {
            color: var(--white);
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.875rem;
            margin: 8px 0 0 0;
            font-weight: 400;
            font-family: 'Montserrat', sans-serif;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-align: center;
            min-width: 140px;
            justify-content: center;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: var(--white);
            text-decoration: none;
        }

        .section-card {
            background: var(--white);
            border-radius: 12px;
            padding: 28px 32px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            margin-bottom: 24px;
        }

        .section-title {
            color: var(--gray-900);
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0 0 8px 0;
            font-family: 'Montserrat', sans-serif;
        }

        .section-subtitle {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin: 0 0 24px 0;
            font-family: 'Montserrat', sans-serif;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .refresh-btn {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
            color: var(--white);
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 140px;
            justify-content: center;
        }

        .refresh-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .table-container {
            overflow-x: auto;
            width: 100%;
        }

        .table {
            width: 100%;
            min-width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .table-header {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
            color: var(--white);
        }

        .table-header th {
            padding: 16px 20px;
            font-weight: 600;
            font-size: 0.875rem;
            font-family: 'Montserrat', sans-serif;
            text-align: left;
            white-space: nowrap;
        }

        .table-row {
            background: var(--white);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .table-row:hover {
            background: var(--teal-bg);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .table-row td {
            padding: 16px 20px;
            vertical-align: middle;
            white-space: nowrap;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.875rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
        }

        .status-badge.posting {
            background: #fce7f3;
            color: #be185d;
        }

        .status-badge.accepted {
            background: #d1fae5;
            color: #10b981;
        }

        .status-badge.positive_result {
            background: #ecfdf5;
            color: #059669;
        }

        .days-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
        }

        .days-badge.urgent {
            background: #fef3c7;
            color: #d97706;
        }

        .days-badge.normal {
            background: #e0f2fe;
            color: #0369a1;
        }

        .days-badge.complete {
            background: #d1fae5;
            color: #059669;
        }

        .service-type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
        }

        .service-type-badge.brap {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .service-type-badge.regular {
            background: #fef3c7;
            color: #d97706;
        }

        /* Posting period modals */
        .posting-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 12000;
            align-items: center;
            justify-content: center;
        }

        .posting-modal.show {
            display: flex;
        }

        .posting-modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(3px);
        }

        .posting-modal-content {
            position: relative;
            background: var(--white);
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            max-width: 440px;
            width: 92%;
            overflow: hidden;
            animation: slideUp 0.25s ease;
            z-index: 1;
            border: 1px solid var(--gray-200);
        }

        .posting-modal-header {
            padding: 20px 22px 10px;
            text-align: left;
        }

        .posting-modal-title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--gray-800);
        }

        .posting-modal-body {
            padding: 0 22px 16px;
            color: var(--gray-700);
            line-height: 1.6;
        }

        .posting-modal-note {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.9rem;
            color: #0369a1;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .posting-modal-footer {
            padding: 14px 22px 18px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .posting-modal-btn {
            padding: 10px 16px;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .posting-modal-btn.cancel {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .posting-modal-btn.cancel:hover {
            background: var(--gray-300);
        }

        .posting-modal-btn.confirm {
            background: #059669;
            color: var(--white);
        }

        .posting-modal-btn.confirm:hover {
            background: #047857;
        }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 16px;
            color: var(--gray-300);
        }

        .empty-state h4 {
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 8px;
            color: var(--gray-700);
        }

        .empty-state p {
            font-size: 0.875rem;
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            color: var(--gray-600);
        }

        /* Stats cards */
        /* Stats cards with charts */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            position: relative;
            overflow: visible;
            min-height: 170px;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card-content {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            height: 100%;
        }

        .stat-info {
            flex: 1;
            min-width: 0;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-800);
            margin: 0 0 4px 0;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.9375rem;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-600);
            margin-top: 0;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8125rem;
            color: var(--gray-500);
            margin-top: 8px;
        }

        .stat-trend i {
            font-size: 0.75rem;
        }

        .stat-chart-container {
            position: relative;
            width: 110px;
            height: 110px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-chart {
            position: absolute;
            width: 110px;
            height: 110px;
            top: 0;
            left: 0;
            transform: rotate(0deg);
            transition: transform 0.3s ease;
        }

        .stat-card:hover .stat-chart {
            transform: rotate(4deg) scale(1.03);
        }

        .stat-chart-progress {
            transition: stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1), stroke 0.3s ease;
            stroke-dashoffset: 314.16;
        }

        .stat-icon {
            position: absolute;
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white);
            z-index: 2;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.06) rotate(4deg);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
        }

        .stat-icon.total {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
        }

        .stat-icon.regular {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .stat-icon.brap {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .posting-days-setting {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: var(--shadow-sm);
        }

        .posting-days-setting label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .posting-days-control {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .posting-days-control input {
            width: 120px;
            padding: 8px 10px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .posting-days-control button {
            padding: 9px 14px;
            border-radius: 8px;
            border: none;
            background: var(--teal-primary);
            color: var(--white);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .posting-days-control button:hover {
            background: var(--teal-dark);
            transform: translateY(-1px);
        }

        .posting-days-setting small {
            color: var(--gray-500);
            font-size: 0.8rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 24px 16px;
            }

            .header {
                padding: 24px;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .section-card {
                padding: 20px;
            }

            .action-bar {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title"><span id="postingDaysHeader">10</span> Days Posting</h1>
                    <p class="header-subtitle" id="postingDaysSubtitle">{{ $officer->department }} - Delayed Birth Registration requests currently in 10 calendar days posting period</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('officer.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <h3 class="stat-value" id="totalPosting">0</h3>
                        <p class="stat-label">Total in Posting</p>
                        <div class="stat-trend">
                            <i class="fas fa-chart-line"></i>
                            <span id="totalPostingDesc">All posting items</span>
                        </div>
                    </div>
                    <div class="stat-chart-container">
                        <div class="stat-icon total">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <svg class="stat-chart" viewBox="0 0 120 120">
                            <circle class="stat-chart-bg" cx="60" cy="60" r="50" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                            <circle class="stat-chart-progress total" id="chartTotal" cx="60" cy="60" r="50" fill="none"
                                    stroke="url(#totalGradient)" stroke-width="8"
                                    stroke-linecap="round"
                                    stroke-dasharray="314.16"
                                    stroke-dashoffset="314.16"
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

            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <h3 class="stat-value" id="delayedRegularCount">0</h3>
                        <p class="stat-label">Delayed Regular</p>
                        <div class="stat-trend">
                            <i class="fas fa-cog"></i>
                            <span id="regularPercentLabel">0% of total</span>
                        </div>
                    </div>
                    <div class="stat-chart-container">
                        <div class="stat-icon regular">
                            <i class="fas fa-cog"></i>
                        </div>
                        <svg class="stat-chart" viewBox="0 0 120 120">
                            <circle class="stat-chart-bg" cx="60" cy="60" r="50" fill="none" stroke="#dbeafe" stroke-width="8"/>
                            <circle class="stat-chart-progress regular" id="chartRegular" cx="60" cy="60" r="50" fill="none"
                                    stroke="url(#regularGradient)" stroke-width="8"
                                    stroke-linecap="round"
                                    stroke-dasharray="314.16"
                                    stroke-dashoffset="314.16"
                                    transform="rotate(-90 60 60)"/>
                            <defs>
                                <linearGradient id="regularGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#2563eb;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <h3 class="stat-value" id="delayedBrapCount">0</h3>
                        <p class="stat-label">Delayed BRAP</p>
                        <div class="stat-trend">
                            <i class="fas fa-check-circle"></i>
                            <span id="brapPercentLabel">0% of total</span>
                        </div>
                    </div>
                    <div class="stat-chart-container">
                        <div class="stat-icon brap">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <svg class="stat-chart" viewBox="0 0 120 120">
                            <circle class="stat-chart-bg" cx="60" cy="60" r="50" fill="none" stroke="#d1fae5" stroke-width="8"/>
                            <circle class="stat-chart-progress brap" id="chartBrap" cx="60" cy="60" r="50" fill="none"
                                    stroke="url(#brapGradient)" stroke-width="8"
                                    stroke-linecap="round"
                                    stroke-dasharray="314.16"
                                    stroke-dashoffset="314.16"
                                    transform="rotate(-90 60 60)"/>
                            <defs>
                                <linearGradient id="brapGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="section-card">
            <!-- Action Bar -->
            <div class="action-bar" style="flex-wrap: wrap; gap: 12px;">
                <div>
                    <h2 class="section-title">
                        Requests in Posting Period
                    </h2>
                    <p class="section-subtitle">All Delayed Birth Registration (Regular and BRAP) requests currently in the posting period</p>
                </div>
                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <div class="posting-days-setting">
                        <label for="postingDaysInput">Posting period (days)</label>
                        <div class="posting-days-control">
                            <input type="number" id="postingDaysInput" min="1" max="30" step="1" value="10">
                            <button onclick="savePostingDays()"><i class="fas fa-save"></i> Save</button>
                        </div>
                        <small>Used to compute “days remaining” and the posting period. Default is 10 days.</small>
                    </div>
                    <button onclick="refreshPostingRequests()" class="refresh-btn">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Posting Requests Table -->
            <div id="postingRequestsList">
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <h4>Loading posting requests...</h4>
                    <p>Please wait while we fetch the data</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Posting Days Modals -->
    <div id="postingDaysConfirmModal" class="posting-modal">
        <div class="posting-modal-overlay" onclick="closeModal('postingDaysConfirmModal')"></div>
        <div class="posting-modal-content">
            <div class="posting-modal-header">
                <h3 class="posting-modal-title">Confirm Posting Period</h3>
            </div>
            <div class="posting-modal-body">
                <p>Set posting period to <strong id="confirmPostingDaysValue"></strong> days?</p>
                <div class="posting-modal-note">
                    <i class="fas fa-info-circle"></i>
                    <span>This value is used to compute “days remaining” for items in posting. You can change it anytime.</span>
                </div>
            </div>
            <div class="posting-modal-footer">
                <button class="posting-modal-btn cancel" onclick="closeModal('postingDaysConfirmModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="posting-modal-btn confirm" onclick="confirmSavePostingDays()">
                    <i class="fas fa-check"></i> Confirm
                </button>
            </div>
        </div>
    </div>

    <div id="postingDaysSuccessModal" class="posting-modal">
        <div class="posting-modal-overlay" onclick="closeModal('postingDaysSuccessModal')"></div>
        <div class="posting-modal-content">
            <div class="posting-modal-header">
                <h3 class="posting-modal-title">Saved</h3>
            </div>
            <div class="posting-modal-body">
                <p>Posting period updated to <strong id="successPostingDaysValue"></strong> days.</p>
            </div>
            <div class="posting-modal-footer" style="justify-content: flex-end;">
                <button class="posting-modal-btn confirm" onclick="closeModal('postingDaysSuccessModal')">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>
        </div>
    </div>

    <div id="postingDaysErrorModal" class="posting-modal">
        <div class="posting-modal-overlay" onclick="closeModal('postingDaysErrorModal')"></div>
        <div class="posting-modal-content">
            <div class="posting-modal-header">
                <h3 class="posting-modal-title">Invalid Value</h3>
            </div>
            <div class="posting-modal-body">
                <p>Please enter a valid number of days (greater than 0).</p>
            </div>
            <div class="posting-modal-footer" style="justify-content: flex-end;">
                <button class="posting-modal-btn confirm" onclick="closeModal('postingDaysErrorModal')">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>
        </div>
    </div>

    <script>
        const postingDaysKey = `posting_days_${'{{ $officer->department }}'.replace(/\s+/g, '_').toLowerCase()}`;
        const defaultPostingDays = 10;
        const chartCircumference = 2 * Math.PI * 50; // radius 50
        let pendingPostingDays = null;

        function updatePostingHeader(days) {
            const headerDays = document.getElementById('postingDaysHeader');
            const subtitle = document.getElementById('postingDaysSubtitle');
            if (headerDays) headerDays.textContent = days;
            if (subtitle) {
                subtitle.textContent = `{{ $officer->department }} - Delayed Birth Registration requests currently in ${days} calendar day${days === 1 ? '' : 's'} posting period`;
            }
        }

        function showModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('show');
                document.body.style.overflow = '';
            }
            if (id === 'postingDaysConfirmModal') {
                pendingPostingDays = null;
            }
        }

        function getPostingDays() {
            const stored = localStorage.getItem(postingDaysKey);
            const num = parseInt(stored, 10);
            if (!isNaN(num) && num > 0) return num;
            return defaultPostingDays;
        }

        function savePostingDays() {
            const input = document.getElementById('postingDaysInput');
            const value = parseInt(input.value, 10);
            if (isNaN(value) || value <= 0) {
                showModal('postingDaysErrorModal');
                input.value = getPostingDays();
                return;
            }
            pendingPostingDays = value;
            document.getElementById('confirmPostingDaysValue').textContent = value;
            showModal('postingDaysConfirmModal');
        }

        function confirmSavePostingDays() {
            if (pendingPostingDays === null) {
                closeModal('postingDaysConfirmModal');
                return;
            }
            // Save to both localStorage and database
            localStorage.setItem(postingDaysKey, pendingPostingDays);
            
            // Save to database via API
            fetch('/officer/save-posting-days', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ posting_days: pendingPostingDays })
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('successPostingDaysValue').textContent = pendingPostingDays;
                    closeModal('postingDaysConfirmModal');
                    showModal('postingDaysSuccessModal');
                    updatePostingHeader(pendingPostingDays);
                    refreshPostingRequests();
                } else {
                    alert('Error saving posting days: ' + (data.message || 'Unknown error'));
                }
            }).catch(error => {
                console.error('Error saving posting days:', error);
                alert('Error saving posting days. Please try again.');
            });
        }

        function setPostingDaysInput() {
            const input = document.getElementById('postingDaysInput');
            if (input) {
                input.value = getPostingDays();
            }
            updatePostingHeader(getPostingDays());
        }

        function computeDaysRemaining(request, postingDays) {
            // Use stored posting_days if available, otherwise use the current setting
            const requestPostingDays = request.posting_days || postingDays;
            
            if (!request.posting_started_at) {
                // fallback to backend-provided days_remaining
                return typeof request.days_remaining === 'number' ? request.days_remaining : requestPostingDays;
            }
            const start = new Date(request.posting_started_at);
            if (isNaN(start.getTime())) {
                return typeof request.days_remaining === 'number' ? request.days_remaining : requestPostingDays;
            }
            const now = new Date();
            const diffMs = now.getTime() - start.getTime();
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            return Math.max(requestPostingDays - diffDays, 0);
        }

        function animateStatsCharts() {
            const charts = document.querySelectorAll('.stat-chart-progress');
            charts.forEach((chart, index) => {
                const targetOffset = parseFloat(chart.getAttribute('data-target-offset')) || chartCircumference;
                chart.style.strokeDashoffset = chartCircumference;
                chart.style.opacity = '0';
                setTimeout(() => {
                    chart.style.transition = 'stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.5s ease';
                    chart.style.opacity = '1';
                    chart.style.strokeDashoffset = targetOffset;
                }, index * 150);
            });
        }

        // Load posting requests when page loads
        document.addEventListener('DOMContentLoaded', function () {
            setPostingDaysInput();
            loadPostingRequests();
        });

        // Load posting requests
        function loadPostingRequests() {
            const container = document.getElementById('postingRequestsList');
            const postingDays = getPostingDays();
            setPostingDaysInput();

            // Show loading state
            container.innerHTML = '<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><h4>Loading posting requests...</h4><p>Please wait while we fetch the data</p></div>';

            fetch('/officer/posting-requests')
                .then(response => response.json())
                .then(data => {
                    console.log('Posting requests data:', data);
                    
                    // Update stats
                    updateStats(data.requests || []);
                    
                    if (data.requests && data.requests.length > 0) {
                        renderPostingRequests(container, data.requests, postingDays);
                        animateStatsCharts();
                    } else {
                        container.innerHTML = '<div class="empty-state"><i class="fas fa-clipboard-list"></i><h4>No requests in posting period</h4><p>All requests have either completed the posting period or are still being processed</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading posting requests:', error);
                    container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h4>Error</h4><p>Failed to load posting requests. Please try again.</p></div>';
                });
        }

        // Update statistics
        function updateStats(requests) {
            const total = requests.length;
            let delayedRegularCount = 0;
            let delayedBrapCount = 0;

            requests.forEach(request => {
                if (request.is_delayed_brap) {
                    delayedBrapCount++;
                } else {
                    delayedRegularCount++;
                }
            });

            const regularPercent = total > 0 ? (delayedRegularCount / total) * 100 : 0;
            const brapPercent = total > 0 ? (delayedBrapCount / total) * 100 : 0;

            document.getElementById('totalPosting').textContent = total;
            document.getElementById('delayedRegularCount').textContent = delayedRegularCount;
            document.getElementById('delayedBrapCount').textContent = delayedBrapCount;
            document.getElementById('totalPostingDesc').textContent = 'All posting items';
            document.getElementById('regularPercentLabel').textContent = `${regularPercent.toFixed(1)}% of total`;
            document.getElementById('brapPercentLabel').textContent = `${brapPercent.toFixed(1)}% of total`;

            const chartTotal = document.getElementById('chartTotal');
            const chartRegular = document.getElementById('chartRegular');
            const chartBrap = document.getElementById('chartBrap');

            const totalOffset = chartCircumference * (1 - 1); // always full
            const regularOffset = chartCircumference * (1 - (regularPercent / 100));
            const brapOffset = chartCircumference * (1 - (brapPercent / 100));

            if (chartTotal) chartTotal.setAttribute('data-target-offset', totalOffset);
            if (chartRegular) chartRegular.setAttribute('data-target-offset', regularOffset);
            if (chartBrap) chartBrap.setAttribute('data-target-offset', brapOffset);
        }

        // Render posting requests table
        function renderPostingRequests(container, requests, postingDays) {
            console.log('Rendering posting requests table with', requests.length, 'requests');

            let tableHTML = `
                <div class="table-container">
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th>Name</th>
                                <th>Tracking No</th>
                                <th>Service Type</th>
                                <th>Posting Started</th>
                                <th>Days Remaining</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>`;

            requests.forEach(request => {
                const isDelayedBrap = request.is_delayed_brap || false;
                const serviceTypeLabel = isDelayedBrap ? 'BRAP' : 'Regular';
                const serviceTypeClass = isDelayedBrap ? 'brap' : 'regular';
                
                // Calculate days remaining based on configurable posting days
                const daysRemaining = computeDaysRemaining(request, postingDays);
                let daysClass = 'normal';
                if (daysRemaining <= 0) {
                    daysClass = 'complete';
                } else if (daysRemaining <= 3) {
                    daysClass = 'urgent';
                }
                
                // Format posting start date
                let postingStartDisplay = 'N/A';
                if (request.posting_started_at) {
                    const postingDate = new Date(request.posting_started_at);
                    if (!isNaN(postingDate.getTime())) {
                        postingStartDisplay = postingDate.toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'short', 
                            day: 'numeric' 
                        });
                    }
                }

                // Status display
                // Check if posting period is complete (daysRemaining <= 0)
                const isPostingComplete = daysRemaining <= 0;
                let statusText;
                if (request.status === 'posting') {
                    statusText = '10 DAYS POSTING';
                } else if (request.status === 'accepted') {
                    statusText = isPostingComplete ? 'ACCEPTED (POSTED)' : 'ACCEPTED (POSTING)';
                } else if (request.status === 'positive_result') {
                    statusText = isPostingComplete ? 'POSITIVE RESULT (POSTED)' : 'POSITIVE RESULT (POSTING)';
                } else {
                    statusText = request.status.toUpperCase().replace(/_/g, ' ');
                }

                tableHTML += `
                    <tr class="table-row">
                        <td style="font-weight: 600; color: var(--gray-800);">${request.full_name || 'N/A'}</td>
                        <td style="font-family: monospace; color: var(--gray-600); font-size: 14px;">${request.tracking_number}</td>
                        <td>
                            <div style="margin-bottom: 4px;">
                                <span class="service-type-badge ${serviceTypeClass}">${serviceTypeLabel}</span>
                            </div>
                            <div style="font-size: 12px; color: var(--gray-600);">Delayed Birth Registration</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">${postingStartDisplay}</div>
                        </td>
                        <td>
                            <span class="days-badge ${daysClass}">
                                <i class="fas fa-clock"></i>
                                ${daysRemaining <= 0 ? 'Completed' : daysRemaining + ' day' + (daysRemaining !== 1 ? 's' : '') + ' left'}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge ${request.status}">
                                ${statusText}
                            </span>
                        </td>
                    </tr>`;
            });

            tableHTML += `
                        </tbody>
                    </table>
                </div>`;

            container.innerHTML = tableHTML;
        }

        // Refresh posting requests
        function refreshPostingRequests() {
            loadPostingRequests();
        }
    </script>
</body>

</html>
