<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Service Types</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div
        style="max-width:1100px; margin:24px auto; background:#fff; border-radius:10px; box-shadow:0 10px 20px rgba(0,0,0,.06); padding:20px;">
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <h2 style="margin:0;">{{ $service->name }} - Types</h2>
            <a href="{{ route('admin.services.index') }}">Back</a>
        </div>
        @if(session('status'))
            <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:6px;margin-top:12px;">
                {{ session('status') }}
            </div>
        @endif
        <div style="display:flex; gap:16px; margin-top:16px;">
            <form method="POST" action="{{ route('admin.services.types.store', $service) }}"
                style="background:#f7f9fb; border:1px solid #e5e7eb; border-radius:10px; padding:16px; min-width:320px;">
                @csrf
                <div style="font-weight:700; margin-bottom:8px;">Add Type</div>
                <input name="name" placeholder="Type name" required
                    style="width:100%; padding:10px 12px; border:1px solid #cfe2f3; border-radius:6px;">
                <input name="icon_path" placeholder="/assets/icons/civil-services.png"
                    style="width:100%; padding:10px 12px; border:1px solid #cfe2f3; border-radius:6px; margin-top:8px;">
                <button type="submit"
                    style="margin-top:10px; background:#0ea5e9; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">Add</button>
            </form>
            <div style="flex:1; display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:12px;">
                @forelse($types as $t)
                    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;">
                        <div style="display:flex; gap:10px; align-items:center;">
                            <img src="{{ $t->icon_path ?: '/assets/icons/civil-services.png' }}"
                                style="width:48px;height:48px;" alt="icon">
                            <div style="font-weight:700;">{{ $t->name }}</div>
                        </div>
                        <div style="display:flex; gap:8px; margin-top:10px;">
                            <a href="{{ route('admin.services.steps', [$service, $t]) }}">Steps</a>
                            <form method="POST" action="{{ route('admin.services.types.update', [$service, $t]) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $t->name }}">
                                <input type="hidden" name="icon_path" value="{{ $t->icon_path }}">
                                <input type="hidden" name="is_active" value="{{ $t->is_active ? 0 : 1 }}">
                                <button type="submit"
                                    style="background:#e5e7eb;border:none;padding:6px 8px;border-radius:6px;cursor:pointer;">{{ $t->is_active ? 'Deactivate' : 'Activate' }}</button>
                            </form>
                            <form method="POST" onsubmit="return confirm('Delete this type?');"
                                action="{{ route('admin.services.types.destroy', [$service, $t]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    style="background:#ef4444;color:#fff;border:none;padding:6px 8px;border-radius:6px;cursor:pointer;">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div>No types yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</body>

</html>