<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Officers</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div class="page-container">
        <div class="content-wrapper">
            <div class="page-header">
                <div class="action-bar-left">
                    <a href="{{ route('admin.dashboard') }}" class="btn-back">← Back</a>
                    <h2 class="page-title">Officers</h2>
                </div>
            </div>
            <div class="action-bar">
                <div class="action-bar-left">
                    <a href="{{ route('admin.officers.create') }}" class="btn-add">Add Officer</a>
                </div>
                <div class="action-bar-right">
                    <form method="GET">
                        <input type="text" name="q" value="{{ $q }}" placeholder="Search..." class="search-input">
                    </form>
                </div>
            </div>
            @if(session('status'))
                <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:6px;margin-top:12px;">
                    {{ session('status') }}
                </div>
            @endif
            <div style="margin-top:16px;">
                <table style="width:100%; border-collapse:separate; border-spacing:0 8px;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:8px;">Full Name</th>
                            <th style="text-align:left; padding:8px;">Email</th>
                            <th style="text-align:left; padding:8px;">Department</th>
                            <th style="text-align:left; padding:8px;">Status</th>
                            <th style="padding:8px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($officers as $officer)
                            <tr style="background:#f9fafb;">
                                <td style="padding:10px 8px;">{{ $officer->name }}</td>
                                <td style="padding:10px 8px;">{{ $officer->email }}</td>
                                <td style="padding:10px 8px;">{{ $officer->department }}</td>
                                <td style="padding:10px 8px;">{{ $officer->is_active ? 'Active' : 'Inactive' }}</td>
                                <td style="padding:10px 8px; white-space:nowrap;">
                                    <a href="{{ route('admin.officers.edit', $officer) }}" class="btn-action-edit">✏️
                                        Edit</a>
                                    <form action="{{ route('admin.officers.toggle', $officer) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-action"
                                            style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">{{ $officer->is_active ? '⏸️ Deactivate' : '▶️ Activate' }}</button>
                                    </form>
                                    <form action="{{ route('admin.officers.destroy', $officer) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Delete this officer?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action"
                                            style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">🗑️
                                            Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:10px 8px;">No officers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>