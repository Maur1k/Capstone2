<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Steps</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div
        style="max-width:900px; margin:24px auto; background:#fff; border-radius:10px; box-shadow:0 10px 20px rgba(0,0,0,.06); padding:20px;">
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <h2 style="margin:0;">{{ $service->name }} → {{ $type->name }} - Steps</h2>
            <a href="{{ route('admin.services.types', $service) }}">Back</a>
        </div>
        @if(session('status'))
            <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:6px;margin-top:12px;">
                {{ session('status') }}
            </div>
        @endif
        <form method="POST" action="{{ route('admin.services.steps.store', [$service, $type]) }}"
            style="margin-top:14px; background:#f7f9fb; border:1px solid #e5e7eb; border-radius:10px; padding:16px;">
            @csrf
            <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                <input name="title" placeholder="Step title" required
                    style="padding:10px 12px; border:1px solid #cfe2f3; border-radius:6px;">
                <textarea name="content" placeholder="Details (bullet points allowed)" rows="3" required
                    style="padding:10px 12px; border:1px solid #cfe2f3; border-radius:6px;"></textarea>
                <button type="submit"
                    style="align-self:start; background:#0ea5e9; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">Add
                    Step</button>
            </div>
        </form>
        <div style="margin-top:16px;">
            @forelse($steps as $step)
                <div
                    style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:12px; margin-bottom:10px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div style="font-weight:700;">{{ $step->title }}</div>
                        <form method="POST" action="{{ route('admin.services.steps.destroy', [$service, $type, $step]) }}"
                            onsubmit="return confirm('Delete this step?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="background:#ef4444;color:#fff;border:none;padding:6px 8px;border-radius:6px;cursor:pointer;">Delete</button>
                        </form>
                    </div>
                    <div style="color:#4b5563; white-space:pre-wrap; margin-top:6px;">{{ $step->content }}</div>
                </div>
            @empty
                <div>No steps yet.</div>
            @endforelse
        </div>
    </div>
</body>

</html>