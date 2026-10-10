<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --ink: #111827; --muted: #4b5563; --paper: #ffffff; --bg: #f3f4f6; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 12px;
            background: var(--bg);
            color: var(--ink);
        }

        .no-print { display: initial; }

        .toolbar {
            max-width: 920px;
            margin: 0 auto 8px auto;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .toolbar .left {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #111827;
            cursor: pointer;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #111827;
            background: #fff;
            font-family: 'Montserrat', sans-serif;
        }
        .btn-primary { background: #111827; color: #fff; }

        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin: 0;
            align-items: flex-end;
        }
        .filters label {
            font-size: 0.85rem;
            color: var(--muted);
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .filters input,
        .filters select {
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: 'Montserrat', sans-serif;
            background: #fff;
            color: var(--ink);
        }

        .sheet {
            max-width: 920px;
            margin: 0 auto;
            background: var(--paper);
            border: 3px solid #111827;
            padding: 18px 20px 16px 20px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.08);
        }

        .report-header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #111827;
        }

        .org-name {
            font-size: 18px;
            font-weight: 800;
            margin: 0;
        }

        .org-sub {
            margin: 4px 0 0 0;
            color: var(--muted);
            font-size: 10px;
            font-weight: 500;
            line-height: 1.3;
        }

        .report-title {
            margin: 12px 0 0 0;
            font-size: 20px;
            font-weight: 900;
            font-style: italic;
            letter-spacing: 0.2px;
        }

        .info-grid {
            margin-top: 12px;
            border-top: 2px solid #111827;
            padding-top: 10px;
        }

        .info-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .info-table td { padding: 5px 8px; vertical-align: middle; }
        .info-label {
            width: 220px;
            background: #111827;
            color: #fff;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-value {
            background: #e5e7eb;
            border-bottom: 1px solid #c7c9cf;
            font-size: 10.5px;
            font-weight: 600;
            color: #111827;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .section-title {
            margin: 14px 0 8px 0;
            font-size: 18px;
            font-weight: 900;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            border: 2px solid #111827;
        }

        .items-table th,
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #111827;
            border-right: 1px solid #111827;
            text-align: left;
        }
        .items-table th:last-child,
        .items-table td:last-child { border-right: none; }

        .items-table thead th {
            background: #111827;
            color: #fff;
            font-weight: 900;
            font-size: 10px;
            letter-spacing: 0.2px;
            border-bottom: 2px solid #111827;
        }

        .muted { color: var(--muted); font-size: 10px; }

        .summary-row {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
            font-size: 10px;
            font-weight: 700;
        }

        .signature-section {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 2px solid #111827;
        }

        .signature-line {
            border-top: 1px solid #111827;
            margin-top: 40px;
            width: 100%;
        }

        .signature-label {
            text-align: center;
            margin-top: 4px;
            font-size: 10px;
            font-weight: 600;
            color: var(--ink);
        }

        @media print {
            @page {
                margin: 0;
                size: A4;
            }
            body {
                padding: 0;
                background: #fff;
                margin: 0;
            }
            .no-print { display: none !important; }
            .sheet {
                box-shadow: none;
                margin: 0;
                padding: 15mm;
                border: none;
            }
            .items-table { page-break-inside: auto; }
            .items-table tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <div class="left">
            <button class="btn btn-primary" onclick="window.print()" type="button">
                <i class="fas fa-print"></i>
                Print
            </button>
            <a href="{{ route('officer.dashboard') }}" class="btn">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>

        <form method="GET" class="filters" action="{{ route('officer.reports.requests') }}">
            <label>
                Date from
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            </label>
            <label>
                Date to
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            </label>
            <label>
                Status
                <select name="status">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ ($filters['status'] ?? 'all') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $s)) }}</option>
                    @endforeach
                </select>
            </label>
            <div style="display:flex; align-items:flex-end; gap:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <a href="{{ route('officer.reports.requests') }}" class="btn"><i class="fas fa-undo"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="sheet">
        <header class="report-header">
            <h1 class="report-title">Requests Report</h1>
            <p class="org-sub">
                {{ $department }} Department<br>
                Generated: {{ now()->format('Y-m-d H:i') }}
            </p>
        </header>

        <section class="info-grid" aria-label="Report information">
            @php($from = $filters['date_from'] ?? null)
            @php($to = $filters['date_to'] ?? null)
            @php($status = $filters['status'] ?? 'all')

            <table class="info-table">
                <tr>
                    <td class="info-label">Prepared By</td>
                    <td class="info-value">{{ optional(auth()->user())->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Report Period</td>
                    <td class="info-value">
                        @if($from && $to)
                            {{ $from }} to {{ $to }}
                        @elseif($from)
                            From {{ $from }}
                        @elseif($to)
                            Up to {{ $to }}
                        @else
                            All dates
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Status Filter</td>
                    <td class="info-value">{{ ucfirst(str_replace('_',' ', $status)) }}</td>
                </tr>
                <tr>
                    <td class="info-label">Description</td>
                    <td class="info-value">Printable list of selected service requests.</td>
                </tr>
            </table>
        </section>

        <h2 class="section-title">Items</h2>

        <table class="items-table" aria-label="Requests table">
            <thead>
                <tr>
                    <th style="width: 16%;">Tracking #</th>
                    <th style="width: 22%;">Name</th>
                    <th style="width: 20%;">Service Type</th>
                    <th style="width: 14%;">Status</th>
                    <th style="width: 18%;">Submitted</th>
                    <th style="width: 10%;">Queue #</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $r)
                    <tr>
                        <td>{{ $r['tracking_number'] }}</td>
                        <td>{{ $r['name'] }}</td>
                        <td>{{ $r['service_type'] }}</td>
                        <td>{{ ucfirst(str_replace('_',' ', $r['status'])) }}</td>
                        <td>{{ $r['submitted_at'] }}</td>
                        <td>{{ $r['queue_number'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted" style="padding: 14px 10px;">No requests found for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary-row">
            <div>
                Total Requests:
                {{ method_exists($requests, 'count') ? $requests->count() : (is_countable($requests) ? count($requests) : 0) }}
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-label">Authorized Signatures</div>
        </div>
    </div>
</body>
</html>

