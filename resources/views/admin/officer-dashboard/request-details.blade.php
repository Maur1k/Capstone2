<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Details - {{ $civilRequest->tracking_number ?? $otherRequest->tracking_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--teal-bg) 0%, #f0fdfa 50%, var(--gray-50) 100%);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* Background decorative elements */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 20%, rgba(0, 160, 160, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 128, 128, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 60%, rgba(77, 208, 225, 0.06) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 100%;
            width: 100%;
            margin: 0;
            padding: 16px 12px;
            position: relative;
            z-index: 1;
        }

        .header {
            background: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-light) 100%);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .header-title {
            color: var(--white);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin: 8px 0 0 0;
            font-weight: 400;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: var(--white);
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            color: var(--white);
            text-decoration: none;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.2);
            backdrop-filter: blur(10px);
            color: var(--white);
            padding: 12px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: 1px solid rgba(239, 68, 68, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: none;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.2);
            color: var(--white);
            text-decoration: none;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert.alert-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .alert.alert-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .content-section {
            background: var(--white);
            border-radius: 16px;
            padding: 28px 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            margin-bottom: 24px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0 0 24px 0;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--teal-primary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }
        }

        @media (min-width: 1200px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 28px;
            }
        }

        /* Enhanced info sections */
        .info-section {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 24px 28px;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .info-section:hover {
            border-color: var(--teal-primary);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.08);
        }

        .info-section h3 {
            margin: 0 0 20px 0;
            color: var(--gray-800);
            font-size: 1.125rem;
            font-weight: 700;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--teal-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-section h3::before {
            content: '';
            width: 4px;
            height: 20px;
            background: var(--teal-primary);
            border-radius: 2px;
        }

        /* Two-column layout for main content */
        .details-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 32px;
        }

        .details-main {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .details-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Full-width sections */
        .full-width-section {
            grid-column: 1 / -1;
        }

        /* Timeline improvements */
        .timeline-container {
            max-height: 600px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .timeline-container::-webkit-scrollbar {
            width: 8px;
        }

        .timeline-container::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 4px;
        }

        .timeline-container::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 4px;
        }

        .timeline-container::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        .info-item {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 6px;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: var(--gray-900);
            font-size: 0.95rem;
            word-break: break-word;
            font-weight: 500;
            line-height: 1.6;
        }

        .tracking-number {
            font-family: monospace;
            background: var(--gray-100);
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 4px solid var(--teal-primary);
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge.inquiry {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .status-badge.to_be_encoded_brap {
            background: rgba(139, 92, 246, 0.1);
            color: #7c3aed;
        }

        .status-badge.checklist_given {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .status-badge.incomplete_requirements {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .status-badge.accepted {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .status-badge.submitted {
            background: rgba(100, 116, 139, 0.1);
            color: #475569;
        }

        .status-badge.psa_processing {
            background: rgba(14, 165, 233, 0.15);
            color: #0369a1;
        }

        .status-badge.posting {
            background: rgba(168, 85, 247, 0.1);
            color: #7c3aed;
        }

        .status-badge.ready_for_approval {
            background: rgba(251, 191, 36, 0.15);
            color: #d97706;
        }

        .status-badge.registered {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .status-badge.encoded_brap {
            background: rgba(139, 92, 246, 0.1);
            color: #7c3aed;
        }

        .status-badge.negative_result {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .status-badge.positive_result {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .status-badge.rejected {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .info-box {
            background: var(--gray-50);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            border-left: 4px solid var(--teal-primary);
            margin-top: 24px;
            transition: all 0.3s ease;
        }

        .info-box:hover {
            border-color: var(--teal-primary);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.08);
        }

        .info-box h4 {
            color: var(--gray-800);
            margin: 0 0 16px 0;
            font-size: 1.125rem;
            font-weight: 700;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--teal-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box h4::before {
            content: '';
            width: 4px;
            height: 20px;
            background: var(--teal-primary);
            border-radius: 2px;
        }

        .info-box p {
            margin: 0;
            color: var(--gray-700);
            white-space: pre-wrap;
            line-height: 1.6;
        }

        .admin-notes {
            background: rgba(254, 243, 199, 0.3);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-top: 8px;
        }

        .admin-notes .info-label {
            color: #d97706;
        }

        .timeline {
            position: relative;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 24px;
            position: relative;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 20px;
            top: 40px;
            bottom: -24px;
            width: 2px;
            background: var(--gray-200);
        }

        .timeline-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
            z-index: 1;
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
        }

        .timeline-content {
            flex: 1;
            background: var(--gray-50);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid var(--teal-primary);
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .timeline-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }

        .timeline-date {
            color: var(--gray-500);
            font-size: 0.875rem;
        }

        .timeline-description {
            color: var(--gray-600);
            margin: 0 0 8px 0;
        }

        .timeline-metadata {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 12px;
        }

        .metadata-item {
            font-size: 0.875rem;
        }

        .metadata-label {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .metadata-label.old {
            color: #ef4444;
        }

        .metadata-label.new {
            color: #10b981;
        }

        .metadata-value {
            color: var(--gray-600);
        }

        .timeline-admin {
            margin-top: 8px;
            color: var(--gray-500);
            font-size: 0.75rem;
        }

        .sms-log {
            background: var(--gray-50);
            padding: 16px;
            border-radius: 12px;
            border-left: 4px solid #10b981;
            margin-bottom: 16px;
        }

        .sms-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .sms-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }

        .sms-status {
            background: #10b981;
            color: var(--white);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .sms-phone {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin: 4px 0;
        }

        .sms-message {
            background: var(--white);
            padding: 12px;
            border-radius: 8px;
            margin: 8px 0;
            color: var(--gray-700);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .sms-date {
            color: var(--gray-500);
            font-size: 0.75rem;
        }

        .sms-response {
            margin-top: 8px;
            color: var(--gray-500);
            font-size: 0.75rem;
        }

        .form-section {
            background: var(--white);
            border-radius: 16px;
            padding: 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0 0 24px 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
            font-size: 0.875rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
            background: var(--white);
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--teal-primary);
            box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 16px;
            justify-content: flex-end;
            padding-top: 24px;
            border-top: 1px solid var(--gray-200);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-align: center;
        }

        .btn-secondary {
            background: var(--gray-500);
            color: var(--white);
        }

        .btn-secondary:hover {
            background: var(--gray-600);
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.3);
        }

        .requirements-form {
            background: var(--gray-50);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid var(--teal-primary);
        }

        .requirements-form .form-label {
            color: var(--gray-800);
            font-size: 1rem;
        }

        .requirements-form .form-textarea {
            background: var(--white);
            font-family: monospace;
            font-size: 0.875rem;
        }

        .requirements-help {
            color: var(--gray-500);
            font-size: 0.75rem;
            margin-top: 4px;
            display: block;
        }

        /* Responsive adjustments */
        @media (max-width: 1400px) {
            .details-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }

            .header {
                padding: 24px 20px;
            }

            .header-title {
                font-size: 2rem;
            }

            .content-section,
            .form-section {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .header-actions {
                flex-direction: column;
                gap: 12px;
            }

            .form-actions {
                flex-direction: column;
            }

            .details-layout {
                gap: 20px;
            }
        }

        @media (min-width: 1401px) {
            .details-layout {
                grid-template-columns: 1.2fr 0.8fr;
            }
        }

        /* Optimize spacing for larger screens */
        @media (min-width: 1200px) {
            .container {
                padding: 20px 24px;
            }

            .content-section {
                padding: 32px 36px;
            }
        }

        @media (min-width: 1600px) {
            .container {
                padding: 24px 32px;
            }

            .content-section {
                padding: 36px 40px;
            }
        }
    </style>
</head>

<body>
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-triangle-exclamation"></i>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Request Details</h1>
                    <p class="header-subtitle">{{ $officer->department }} -
                        {{ $civilRequest->tracking_number ?? $otherRequest->tracking_number }}
                    </p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('officer.dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <form method="POST" action="{{ route('officer.logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if(isset($civilRequest))
            <!-- Civil Service Request Details -->
            <div class="content-section">
                <h2 class="section-title">Civil Service Request Information</h2>

                <div class="info-grid">
                    <!-- Basic Information -->
                    <div class="info-section">
                        <h3>Basic Information</h3>
                        <div class="info-item">
                            <div class="info-label">Tracking Number:</div>
                            <div class="info-value tracking-number">{{ $civilRequest->tracking_number }}</div>
                        </div>
                        @if(\App\Models\Setting::getQueueNumberSettingForDepartment('Civil Registry Office', false) && $civilRequest->queue_number)
                            <div class="info-item">
                                <div class="info-label">Queue Number:</div>
                                <div class="info-value">{{ $civilRequest->queue_number }}</div>
                            </div>
                        @endif
                        <div class="info-item">
                            <div class="info-label">Service Type:</div>
                            <div class="info-value">{{ $civilRequest->service_type }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status:</div>
                            <div class="info-value">
                                @php
                                    $statusColors = [
                                        'submitted' => 'submitted',
                                        'inquiry' => 'inquiry',
                                        'to_be_encoded_brap' => 'to_be_encoded_brap',
                                        'checklist_given' => 'checklist_given',
                                        'incomplete_requirements' => 'incomplete_requirements',
                                        'accepted' => 'accepted',
                                        'psa_processing' => 'psa_processing',
                                        'posting' => 'posting',
                                        'ready_for_approval' => 'ready_for_approval',
                                        'registered' => 'registered',
                                        'encoded_brap' => 'encoded_brap',
                                        'negative_result' => 'negative_result',
                                        'positive_result' => 'positive_result',
                                        'rejected' => 'rejected'
                                    ];
                                    $statusClass = $statusColors[$civilRequest->status] ?? 'inquiry';
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                    {{ ucfirst(str_replace('_', ' ', $civilRequest->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div class="info-section">
                        <h3>Client Information</h3>
                        <div class="info-item">
                            <div class="info-label">Full Name:</div>
                            <div class="info-value">{{ $civilRequest->full_name }}</div>
                        </div>
                        @if($civilRequest->contact_number)
                            <div class="info-item">
                                <div class="info-label">Contact Number:</div>
                                <div class="info-value">{{ $civilRequest->contact_number }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Additional Information -->
                @if($civilRequest->additional_info)
                    <div class="info-box">
                        <h4>Additional Information</h4>
                        <p>{{ $civilRequest->additional_info }}</p>
                    </div>
                @endif

                <!-- Child Information (for birth certificates) -->
                @if($civilRequest->child_info)
                    <div class="info-box">
                        <h4>Child Information</h4>
                        @php
                            $childInfo = is_string($civilRequest->child_info) ? json_decode($civilRequest->child_info, true) : $civilRequest->child_info;
                        @endphp
                        @if($childInfo)
                            <div class="info-grid">
                                @foreach($childInfo as $key => $value)
                                    <div class="info-item">
                                        <div class="info-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</div>
                                        <div class="info-value">{{ $value }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Mother Information -->
                @if($civilRequest->mother_info)
                    <div class="info-box">
                        <h4>Mother Information</h4>
                        @php
                            $motherInfo = is_string($civilRequest->mother_info) ? json_decode($civilRequest->mother_info, true) : $civilRequest->mother_info;
                        @endphp
                        @if($motherInfo)
                            <div class="info-grid">
                                @foreach($motherInfo as $key => $value)
                                    <div class="info-item">
                                        <div class="info-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</div>
                                        <div class="info-value">{{ $value }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Father Information -->
                @if($civilRequest->father_info)
                    <div class="info-box">
                        <h4>Father Information</h4>
                        @php
                            $fatherInfo = is_string($civilRequest->father_info) ? json_decode($civilRequest->father_info, true) : $civilRequest->father_info;
                        @endphp
                        @if($fatherInfo)
                            <div class="info-grid">
                                @foreach($fatherInfo as $key => $value)
                                    <div class="info-item">
                                        <div class="info-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</div>
                                        <div class="info-value">{{ $value }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Administrative Information -->
                <div class="info-section" style="margin-top: 24px;">
                    <h3>Administrative Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Date Submitted:</div>
                            <div class="info-value">{{ $civilRequest->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Last Updated:</div>
                            <div class="info-value">{{ $civilRequest->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                        @if($civilRequest->processed_by)
                            <div class="info-item">
                                <div class="info-label">Processed By:</div>
                                <div class="info-value">{{ $civilRequest->processed_by }}</div>
                            </div>
                        @endif
                        @if($civilRequest->processed_at)
                            <div class="info-item">
                                <div class="info-label">Processed At:</div>
                                <div class="info-value">{{ $civilRequest->processed_at->format('M d, Y H:i') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(isset($otherRequest))
            <!-- Other Service Request Details -->
            <div class="content-section">
                <h2 class="section-title">{{ $otherRequest->service_type ?? 'Other Service' }} Request Information</h2>

                <div class="info-grid">
                    <!-- Basic Information -->
                    <div class="info-section">
                        <h3>Basic Information</h3>
                        <div class="info-item">
                            <div class="info-label">Tracking Number:</div>
                            <div class="info-value tracking-number">{{ $otherRequest->tracking_number }}</div>
                        </div>
                        @if(\App\Models\Setting::getQueueNumberSettingForDepartment($otherRequest->department ?? 'Other Services', false) && $otherRequest->queue_number)
                            <div class="info-item">
                                <div class="info-label">Queue Number:</div>
                                <div class="info-value">{{ $otherRequest->queue_number }}</div>
                            </div>
                        @endif
                        <div class="info-item">
                            <div class="info-label">Service Type:</div>
                            <div class="info-value">{{ $otherRequest->service_type }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status:</div>
                            <div class="info-value">
                                @php
                                    $statusColors = [
                                        'submitted' => 'submitted',
                                        'inquiry' => 'inquiry',
                                        'checklist_given' => 'checklist_given',
                                        'incomplete_requirements' => 'incomplete_requirements',
                                        'accepted' => 'accepted',
                                        'psa_processing' => 'psa_processing',
                                        'registered' => 'registered',
                                        'negative_result' => 'negative_result',
                                        'positive_result' => 'positive_result',
                                        'rejected' => 'rejected'
                                    ];
                                    $statusClass = $statusColors[$otherRequest->status] ?? 'inquiry';
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                    {{ ucfirst(str_replace('_', ' ', $otherRequest->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div class="info-section">
                        <h3>Client Information</h3>
                        <div class="info-item">
                            <div class="info-label">Full Name:</div>
                            <div class="info-value">{{ $otherRequest->full_name }}</div>
                        </div>
                        @if($otherRequest->contact_number)
                            <div class="info-item">
                                <div class="info-label">Contact Number:</div>
                                <div class="info-value">{{ $otherRequest->contact_number }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Additional Information -->
                @if($otherRequest->additional_info)
                    <div class="info-box">
                        <h4>Additional Information</h4>
                        @php
                            $additionalInfo = is_string($otherRequest->additional_info) ? json_decode($otherRequest->additional_info, true) : $otherRequest->additional_info;
                        @endphp
                        @if($additionalInfo && is_array($additionalInfo))
                            <div class="info-grid">
                                @foreach($additionalInfo as $key => $value)
                                    <div class="info-item">
                                        <div class="info-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</div>
                                        <div class="info-value">{{ $value }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>{{ $otherRequest->additional_info }}</p>
                        @endif
                    </div>
                @endif

                <!-- Purpose (if available) -->
                @if($otherRequest->purpose)
                    <div class="info-box">
                        <h4>Purpose</h4>
                        <p>{{ $otherRequest->purpose }}</p>
                    </div>
                @endif

                <!-- Administrative Information -->
                <div class="info-section" style="margin-top: 24px;">
                    <h3>Administrative Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Date Submitted:</div>
                            <div class="info-value">{{ $otherRequest->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Last Updated:</div>
                            <div class="info-value">{{ $otherRequest->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                        @if($otherRequest->processed_by)
                            <div class="info-item">
                                <div class="info-label">Processed By:</div>
                                <div class="info-value">{{ $otherRequest->processed_by }}</div>
                            </div>
                        @endif
                        @if($otherRequest->processed_at)
                            <div class="info-item">
                                <div class="info-label">Processed At:</div>
                                <div class="info-value">{{ $otherRequest->processed_at->format('M d, Y H:i') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Status Timeline -->
        @if(isset($statusUpdates) && $statusUpdates->count() > 0)
            <div class="content-section">
                <h2 class="section-title">Status Timeline</h2>
                <div class="timeline-container">
                    <div class="timeline">
                        @foreach($statusUpdates->sortBy('created_at') as $index => $update)
                            <div class="timeline-item">
                                <div class="timeline-dot">{{ $loop->iteration }}</div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h4 class="timeline-title">{{ ucfirst(str_replace('_', ' ', $update->status)) }}</h4>
                                        <span class="timeline-date">
                                            {{ optional($update->status_date)->format('M d, Y') ?? $update->created_at->format('M d, Y H:i') }}
                                        </span>
                                    </div>
                                    <p class="timeline-description">{{ $update->remarks ?? 'No remarks provided.' }}</p>
                                    <div class="timeline-metadata">
                                        <div class="metadata-item">
                                            <div class="metadata-label new">Recorded At:</div>
                                            <div class="metadata-value">{{ $update->created_at->format('M d, Y H:i') }}</div>
                                        </div>
                                        <div class="metadata-item">
                                            <div class="metadata-label">Status Date:</div>
                                            <div class="metadata-value">
                                                {{ optional($update->status_date)->format('M d, Y') ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($update->admin)
                                        <p class="timeline-admin">Updated by: {{ $update->admin->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif(isset($activityLogs) && $activityLogs->count() > 0)
            <div class="content-section">
                <h2 class="section-title">Status Timeline</h2>
                <div class="timeline-container">
                    <div class="timeline">
                        @foreach($activityLogs as $index => $log)
                            <div class="timeline-item">
                                <div class="timeline-dot">{{ $index + 1 }}</div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h4 class="timeline-title">{{ ucfirst(str_replace('_', ' ', $log->activity_type)) }}
                                        </h4>
                                        <span class="timeline-date">{{ $log->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <p class="timeline-description">{{ $log->description ?? 'No description available' }}</p>
                                    @if($log->metadata && isset($log->metadata['old_values']) && isset($log->metadata['new_values']))
                                        <div class="timeline-metadata">
                                            <div class="metadata-item">
                                                <div class="metadata-label old">Previous Status:</div>
                                                <div class="metadata-value">
                                                    {{ ucfirst($log->metadata['old_values']['status'] ?? 'N/A') }}
                                                </div>
                                            </div>
                                            <div class="metadata-item">
                                                <div class="metadata-label new">New Status:</div>
                                                <div class="metadata-value">
                                                    {{ ucfirst($log->metadata['new_values']['status'] ?? 'N/A') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($log->admin)
                                        <p class="timeline-admin">Performed by: {{ $log->admin->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Checklist Items History -->
        @if(isset($civilRequest) && $civilRequest->checklistItems && $civilRequest->checklistItems->count() > 0)
            <div class="content-section">
                <h2 class="section-title">Checklist of Requirements History</h2>
                <div class="timeline-container">
                    <div class="timeline">
                        @foreach($civilRequest->checklistItems->sortByDesc('created_at') as $index => $checklist)
                            <div class="timeline-item">
                                <div class="timeline-dot" style="background: #3b82f6;">📋</div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h4 class="timeline-title">Checklist of Requirements</h4>
                                        <span class="timeline-date">{{ $checklist->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <div style="margin: 12px 0;">
                                        <strong style="color: var(--gray-700); font-size: 0.875rem;">Required
                                            Documents:</strong>
                                        <ul style="margin: 8px 0 0 20px; padding: 0; color: var(--gray-700);">
                                            @foreach($checklist->items as $item)
                                                <li style="margin-bottom: 4px;">{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @if($checklist->remarks)
                                        <p class="timeline-description">
                                            <strong>Remarks:</strong> {{ $checklist->remarks }}
                                        </p>
                                    @endif
                                    <div class="timeline-metadata">
                                        <div class="metadata-item">
                                            <div class="metadata-label">SMS Status:</div>
                                            <div class="metadata-value">
                                                @if($checklist->sms_status === 'sent')
                                                    <span style="color: #10b981; font-weight: 600;">✓ Sent</span>
                                                    @if($checklist->sent_at)
                                                        ({{ $checklist->sent_at->format('M d, Y H:i') }})
                                                    @endif
                                                @elseif($checklist->sms_status === 'failed')
                                                    <span style="color: #ef4444; font-weight: 600;">✗ Failed</span>
                                                @elseif($checklist->sms_status === 'pending')
                                                    <span style="color: #f59e0b; font-weight: 600;">⏳ Pending</span>
                                                @else
                                                    <span style="color: var(--gray-500);">Not Sent</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($checklist->phone_number)
                                            <div class="metadata-item">
                                                <div class="metadata-label">Phone Number:</div>
                                                <div class="metadata-value">{{ $checklist->phone_number }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    @if($checklist->sms_message)
                                        <div
                                            style="margin-top: 12px; padding: 12px; background: var(--gray-50); border-radius: 8px; border-left: 3px solid #3b82f6;">
                                            <strong style="color: var(--gray-700); font-size: 0.875rem;">SMS Message Sent:</strong>
                                            <p
                                                style="margin: 8px 0 0 0; color: var(--gray-700); font-size: 0.875rem; white-space: pre-wrap;">
                                                {{ $checklist->sms_message }}
                                            </p>
                                        </div>
                                    @endif
                                    @if($checklist->admin)
                                        <p class="timeline-admin">Created by: {{ $checklist->admin->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif(isset($otherRequest) && $otherRequest->checklistItems && $otherRequest->checklistItems->count() > 0)
            <div class="content-section">
                <h2 class="section-title">Checklist of Requirements History</h2>
                <div class="timeline-container">
                    <div class="timeline">
                        @foreach($otherRequest->checklistItems->sortByDesc('created_at') as $index => $checklist)
                            <div class="timeline-item">
                                <div class="timeline-dot" style="background: #3b82f6;">📋</div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h4 class="timeline-title">Checklist of Requirements</h4>
                                        <span class="timeline-date">{{ $checklist->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <div style="margin: 12px 0;">
                                        <strong style="color: var(--gray-700); font-size: 0.875rem;">Required
                                            Documents:</strong>
                                        <ul style="margin: 8px 0 0 20px; padding: 0; color: var(--gray-700);">
                                            @foreach($checklist->items as $item)
                                                <li style="margin-bottom: 4px;">{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @if($checklist->remarks)
                                        <p class="timeline-description">
                                            <strong>Remarks:</strong> {{ $checklist->remarks }}
                                        </p>
                                    @endif
                                    <div class="timeline-metadata">
                                        <div class="metadata-item">
                                            <div class="metadata-label">SMS Status:</div>
                                            <div class="metadata-value">
                                                @if($checklist->sms_status === 'sent')
                                                    <span style="color: #10b981; font-weight: 600;">✓ Sent</span>
                                                    @if($checklist->sent_at)
                                                        ({{ $checklist->sent_at->format('M d, Y H:i') }})
                                                    @endif
                                                @elseif($checklist->sms_status === 'failed')
                                                    <span style="color: #ef4444; font-weight: 600;">✗ Failed</span>
                                                @elseif($checklist->sms_status === 'pending')
                                                    <span style="color: #f59e0b; font-weight: 600;">⏳ Pending</span>
                                                @else
                                                    <span style="color: var(--gray-500);">Not Sent</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($checklist->phone_number)
                                            <div class="metadata-item">
                                                <div class="metadata-label">Phone Number:</div>
                                                <div class="metadata-value">{{ $checklist->phone_number }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    @if($checklist->sms_message)
                                        <div
                                            style="margin-top: 12px; padding: 12px; background: var(--gray-50); border-radius: 8px; border-left: 3px solid #3b82f6;">
                                            <strong style="color: var(--gray-700); font-size: 0.875rem;">SMS Message Sent:</strong>
                                            <p
                                                style="margin: 8px 0 0 0; color: var(--gray-700); font-size: 0.875rem; white-space: pre-wrap;">
                                                {{ $checklist->sms_message }}
                                            </p>
                                        </div>
                                    @endif
                                    @if($checklist->admin)
                                        <p class="timeline-admin">Created by: {{ $checklist->admin->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- SMS Messages Log -->
        @if(isset($smsLogs) && $smsLogs->count() > 0)
            <div class="content-section">
                <h2 class="section-title">SMS Messages</h2>
                @foreach($smsLogs as $sms)
                    <div class="sms-log"
                        style="margin-bottom: 20px; padding: 16px; border-radius: 12px; border-left: 4px solid {{ $sms['status'] === 'sent' ? '#10b981' : '#ef4444' }}; background: {{ $sms['status'] === 'sent' ? '#f0fdf4' : '#fef2f2' }};">
                        <div class="sms-header"
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <div>
                                <h4 class="sms-title" style="margin: 0; color: var(--gray-800); font-size: 1rem;">
                                    SMS Notification
                                    @if($sms['status_type'])
                                        <span style="font-weight: 400; color: var(--gray-500);"> -
                                            {{ ucwords(str_replace('_', ' ', $sms['status_type'])) }}</span>
                                    @endif
                                </h4>
                            </div>
                            <span class="sms-status"
                                style="padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; {{ $sms['status'] === 'sent' ? 'background: #10b981; color: white;' : 'background: #ef4444; color: white;' }}">
                                {{ ucfirst($sms['status']) }}
                            </span>
                        </div>
                        <p class="sms-phone" style="color: var(--gray-600); font-size: 0.875rem; margin: 0 0 8px 0;">To:
                            {{ $sms['phone_number'] }}</p>
                        <div class="sms-message"
                            style="background: var(--white); padding: 12px; border-radius: 8px; border: 1px solid var(--gray-200); margin: 12px 0; word-wrap: break-word; font-size: 0.9rem; line-height: 1.6; color: var(--gray-700); white-space: normal;">
                            {{ trim($sms['message']) }}</div>
                        <div style="display: flex; flex-wrap: wrap; gap: 16px; color: var(--gray-500); font-size: 0.75rem;">
                            <span>
                                <i class="fas fa-clock"></i>
                                {{ $sms['sent_at'] ? \Carbon\Carbon::parse($sms['sent_at'])->format('M d, Y H:i') : \Carbon\Carbon::parse($sms['created_at'])->format('M d, Y H:i') }}
                            </span>
                            @if($sms['message_id'])
                                <span><i class="fas fa-hashtag"></i> ID: {{ $sms['message_id'] }}</span>
                            @endif
                            @if($sms['network'])
                                <span><i class="fas fa-signal"></i> {{ $sms['network'] }}</span>
                            @endif
                            @if($sms['sender'])
                                <span><i class="fas fa-user"></i> {{ $sms['sender'] }}</span>
                            @endif
                        </div>
                        @if($sms['error'])
                            <p class="sms-error" style="color: #ef4444; font-size: 0.875rem; margin-top: 8px;">
                                <i class="fas fa-exclamation-triangle"></i> Error: {{ $sms['error'] }}
                            </p>
                        @endif
                        @if($sms['response'] && $sms['status'] === 'sent')
                            <p class="sms-response" style="color: #10b981; font-size: 0.75rem; margin-top: 8px;">
                                <i class="fas fa-check-circle"></i> {{ $sms['response'] }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="content-section">
                <h2 class="section-title">SMS Messages</h2>
                <div style="text-align: center; padding: 40px 20px; color: var(--gray-500);">
                    <i class="fas fa-sms" style="font-size: 2.5rem; margin-bottom: 12px; color: var(--gray-300);"></i>
                    <p style="margin: 0;">No SMS messages have been sent for this request yet.</p>
                </div>
            </div>
        @endif
    </div>
</body>

</html>