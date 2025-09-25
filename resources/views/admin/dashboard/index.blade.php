<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div class="page-container">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h2 class="page-title">Welcome, {{ $admin->name }}</h2>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>

            <div style="margin-top:16px; display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:12px;">
                <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:14px;">
                    <div style="font-size:12px;color:#0369a1;">Total Officers</div>
                    <div style="font-weight:700;font-size:28px;">{{ $officerCount }}</div>
                </div>
                <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;padding:14px;">
                    <div style="font-size:12px;color:#065f46;">Active</div>
                    <div style="font-weight:700;font-size:28px;">{{ $activeOfficerCount }}</div>
                </div>
                <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px;">
                    <div style="font-size:12px;color:#9a3412;">Inactive</div>
                    <div style="font-weight:700;font-size:28px;">{{ $inactiveOfficerCount }}</div>
                </div>
                <div style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:10px;padding:14px;">
                    <div style="font-size:12px;color:#5b21b6;">Departments</div>
                    <div style="font-weight:700;font-size:28px;">{{ $departmentCount }}</div>
                </div>
            </div>

            <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
                @if($admin->role === 'super_admin')
                    <a href="{{ route('admin.officers.index') }}"
                        style="flex:1; min-width:240px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; text-decoration:none; display:flex; gap:12px; align-items:center; box-shadow:0 6px 16px rgba(0,0,0,.05);">
                        <img src="/assets/icons/civil-services.png" alt="Officers" style="width:48px;height:48px;">
                        <div>
                            <div style="font-weight:700; color:#111827;">Officers</div>
                            <div style="font-size:13px; color:#6b7280;">Add and manage officer accounts</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.services.manage') }}"
                        style="flex:1; min-width:240px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; text-decoration:none; display:flex; gap:12px; align-items:center; box-shadow:0 6px 16px rgba(0,0,0,.05);">
                        <img src="/assets/icons/Services.png" alt="Manage Services" style="width:48px;height:48px;">
                        <div>
                            <div style="font-weight:700; color:#111827;">Manage Services</div>
                            <div style="font-size:13px; color:#6b7280;">Add, edit, or remove service cards</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.dashboard.edit') }}"
                        style="flex:1; min-width:240px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; text-decoration:none; display:flex; gap:12px; align-items:center; box-shadow:0 6px 16px rgba(0,0,0,.05);">
                        <img src="/assets/icons/municipalinfo.png" alt="Edit Dashboard" style="width:48px;height:48px;">
                        <div>
                            <div style="font-weight:700; color:#111827;">Edit Dashboard</div>
                            <div style="font-size:13px; color:#6b7280;">Municipal info and hero image</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.activity-logs.index') }}"
                        style="flex:1; min-width:240px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; text-decoration:none; display:flex; gap:12px; align-items:center; box-shadow:0 6px 16px rgba(0,0,0,.05);">
                        <div
                            style="width:48px;height:48px; background:#f0f9ff; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:24px;">
                            📊</div>
                        <div>
                            <div style="font-weight:700; color:#111827;">Activity Logs</div>
                            <div style="font-size:13px; color:#6b7280;">Monitor officer activities and system usage</div>
                        </div>
                    </a>
                @endif
            </div>

            <div style="margin-top:18px;">
                <h3 style="margin:0 0 8px 0;">Recent Officers</h3>
                <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                    @forelse($recentOfficers as $o)
                        <div
                            style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e5e7eb;">
                            <div>
                                <div style="font-weight:600;">{{ $o->name }}</div>
                                <div style="font-size:12px; color:#6b7280;">{{ $o->department ?? 'N/A' }}</div>
                            </div>
                            <span
                                style="font-size:12px; padding:4px 8px; border-radius:9999px; background:{{ $o->is_active ? '#dcfce7' : '#fee2e2' }}; color:{{ $o->is_active ? '#14532d' : '#7f1d1d' }};">{{ $o->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    @empty
                        <div>No officers yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>

</html>