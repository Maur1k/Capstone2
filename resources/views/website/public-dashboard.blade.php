<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click2Serve Public Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: linear-gradient(135deg, #e0f2fe 0%, #ecfeff 30%, #f8fafc 100%);
            --card: #ffffff;
            --text: #0f172a;
            --muted: #475569;
            --accent: #0284c7;
            --accent-soft: rgba(2, 132, 199, 0.12);
            --success: #0f766e;
            --warning: #b45309;
            --danger: #b91c1c;
            --radius: 18px;
            --shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .page-shell {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 20px 60px;
        }

        header {
            margin-bottom: 24px;
        }

        header h1 {
            margin: 0;
            font-size: 2.4rem;
        }

        header p {
            margin: 8px 0 0;
            color: var(--muted);
        }

        .hero {
            background: var(--card);
            border-radius: var(--radius);
            padding: 28px;
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .hero-card {
            background: var(--accent-soft);
            border-radius: 16px;
            padding: 18px;
        }

        .hero-card h3 {
            margin: 0;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
        }

        .hero-card strong {
            display: block;
            font-size: 2rem;
            margin-top: 6px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .status-card {
            background: var(--card);
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(15, 23, 42, 0.05);
        }

        .status-card h4 {
            margin: 0;
            font-size: 1rem;
            color: var(--muted);
        }

        .status-card strong {
            display: block;
            margin-top: 10px;
            font-size: 1.8rem;
        }

        .milestones {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .milestone-card {
            background: var(--card);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .milestone-card h5 {
            margin: 0 0 6px;
            font-size: 1rem;
        }

        .milestone-card p {
            margin: 0;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .milestone-card strong {
            display: block;
            margin-top: 8px;
            font-size: 1.6rem;
        }

        .sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }

        .panel {
            background: var(--card);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .panel h3 {
            margin: 0 0 16px;
        }

        .document-list,
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .document-item,
        .timeline-item {
            padding: 16px;
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: #f8fafc;
        }

        .document-item strong,
        .timeline-item strong {
            display: block;
            margin-bottom: 4px;
            font-size: 1rem;
        }

        .document-item span,
        .timeline-item span {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge.pending {
            background: rgba(245, 158, 11, 0.15);
            color: #b45309;
        }

        .badge.approved,
        .badge.registered {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .badge.rejected {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }

        .progress-bar {
            height: 12px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.1);
            overflow: hidden;
            margin-top: 12px;
        }

        .progress-bar span {
            display: block;
            height: 100%;
            background: linear-gradient(135deg, #0ea5e9, #14b8a6);
        }

        .disabled-card {
            background: var(--card);
            padding: 32px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
        }

        .disabled-card h2 {
            margin: 0 0 12px;
        }

        .disabled-card p {
            margin: 0;
            color: var(--muted);
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="page-shell">
        <header>
            <h1>Click2Serve Public Dashboard</h1>
            <p>Live snapshot of PSA processing, supporting documents, and recent milestones.</p>
        </header>

        @if (!$featureEnabled)
            <div class="disabled-card">
                <h2>Dashboard Temporarily Hidden</h2>
                <p>The City Administrator has disabled the public dashboard. Please check back later.</p>
            </div>
        @else
            <section class="hero">
                <div class="hero-card">
                    <h3>Total Requests</h3>
                    <strong>{{ number_format($totals['requests']) }}</strong>
                    <small>{{ $totals['in_process'] }} in progress · {{ $totals['completed'] }} completed</small>
                </div>
                <div class="hero-card">
                    <h3>Completion Rate</h3>
                    <strong>{{ $completionRate }}%</strong>
                    <div class="progress-bar">
                        <span style="width: {{ min($completionRate, 100) }}%;"></span>
                    </div>
                </div>
                <div class="hero-card">
                    <h3>Supporting Documents</h3>
                    <strong>{{ number_format($documentSummary['total'] ?? 0) }}</strong>
                    <small>{{ $documentSummary['pending'] ?? 0 }} pending review</small>
                </div>
            </section>

            <section class="status-grid">
                @foreach ($statusLabels as $key => $label)
                    @php
                        $count = $statusSummary['total'][$key] ?? 0;
                    @endphp
                    <div class="status-card">
                        <h4>{{ $label }}</h4>
                        <strong>{{ number_format($count) }}</strong>
                    </div>
                @endforeach
            </section>

            <section class="milestones">
                @foreach ($milestones as $milestone)
                    <div class="milestone-card">
                        <h5>{{ $milestone['label'] }}</h5>
                        <p>{{ $milestone['description'] }}</p>
                        <strong>{{ number_format($milestone['count']) }}</strong>
                    </div>
                @endforeach
            </section>

            <section class="sections">
                <div class="panel">
                    <h3>Supporting Documents</h3>
                    <div class="document-list">
                        @forelse ($recentDocuments as $document)
                            <div class="document-item">
                                <strong>{{ $document->requirement_name }}</strong>
                                <span>
                                    {{ $document->civilService?->tracking_number ?? 'Unassigned' }} ·
                                    {{ $document->created_at->format('M d, Y') }}
                                </span>
                                <div class="badge {{ $document->status }}">
                                    {{ strtoupper($document->status) }}
                                </div>
                            </div>
                        @empty
                            <p style="color: var(--muted);">No uploads yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="panel">
                    <h3>Latest Status Updates</h3>
                    <div class="timeline">
                        @forelse ($recentUpdates as $update)
                            <div class="timeline-item">
                                <strong>{{ ucfirst(str_replace('_', ' ', $update->status)) }}</strong>
                                <span>
                                    {{ $update->statusable?->tracking_number ?? 'N/A' }} ·
                                    {{ optional($update->status_date)->format('M d, Y') ?? $update->created_at->format('M d, Y') }}
                                </span>
                                <p style="margin: 6px 0 0;">
                                    {{ $update->remarks ? $update->remarks : 'No remarks provided.' }}
                                </p>
                            </div>
                        @empty
                            <p style="color: var(--muted);">Status updates will appear here once available.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        @endif
    </div>
</body>

</html>

