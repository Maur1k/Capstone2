<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Services</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div
        style="max-width:1200px; margin:24px auto; background:#fff; border-radius:10px; box-shadow:0 10px 20px rgba(0,0,0,.06); padding:20px;">
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <h2 style="margin:0;">Services</h2>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('admin.dashboard') }}">Back</a>
                <a href="{{ route('admin.services.create') }}"
                    style="background:#0ea5e9;color:#fff;padding:8px 12px;border-radius:6px; text-decoration:none;">Add
                    Service</a>
            </div>
        </div>
        @if(session('status'))
            <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:6px;margin-top:12px;">
                {{ session('status') }}
            </div>
        @endif
        <div style="display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:16px; margin-top:16px;">
            @forelse($services as $service)
                <div
                    style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; text-align:center;">
                    <img src="{{ $service->icon_path ?: '/assets/icons/Services.png' }}" alt="icon"
                        style="width:72px;height:72px;">
                    <div style="margin-top:8px; font-weight:700;">{{ $service->name }}</div>
                    <div style="font-size:12px; color:#6b7280;">{{ $service->department }}</div>
                    <div style="margin-top:10px; display:flex; gap:8px; justify-content:center;">
                        <a href="{{ route('admin.services.types', $service) }}">Types</a>
                        <a href="{{ route('admin.services.edit', $service) }}">Edit</a>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                            onsubmit="return confirm('Delete this service?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="background:#ef4444;color:#fff;border:none;padding:6px 8px;border-radius:6px;cursor:pointer;">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div>No services yet.</div>
            @endforelse
        </div>
    </div>
</body>

</html>