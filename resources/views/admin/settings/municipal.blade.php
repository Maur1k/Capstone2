<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Municipal Information</title>
    <link rel="stylesheet" href="/css/forms.css">
</head>

<body style="background:#e6f7ff; min-height:100vh; display:flex; align-items:center; justify-content:center;">
    <div
        style="width:900px; background:#fff; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:32px; display:flex; gap:24px;">
        <div
            style="flex:1; background:linear-gradient(180deg,#0299d9,#0079b8); color:#fff; border-radius:8px; padding:24px;">
            <a href="{{ route('admin.dashboard') }}"
                style="display:inline-block; color:#fff; text-decoration:none; font-weight:600;">← Back</a>
            <h2 style="margin-top:24px;">EDIT MUNICIPAL INFORMATION</h2>
        </div>
        <div style="flex:2; display:flex; align-items:center; justify-content:center;">
            <form method="POST" action="{{ route('admin.settings.municipal.update') }}"
                style="width:420px; background:#f7f9fb; border-radius:10px; padding:24px;">
                @csrf
                @method('PUT')
                @if(session('status'))
                    <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:6px;margin-bottom:12px;">
                        {{ session('status') }}</div>
                @endif
                <div style="margin-bottom:12px;">
                    <label for="name" style="display:block; font-size:14px; margin-bottom:6px;">Municipal Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $name) }}" required
                        style="width:100%; padding:10px 12px; border:1px solid #cfe2f3; border-radius:6px;">
                </div>
                <div style="margin-bottom:12px;">
                    <label for="tagline" style="display:block; font-size:14px; margin-bottom:6px;">Tagline</label>
                    <input id="tagline" name="tagline" type="text" value="{{ old('tagline', $tagline) }}"
                        style="width:100%; padding:10px 12px; border:1px solid #cfe2f3; border-radius:6px;">
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px;">
                    <button type="submit"
                        style="background:#0ea5e9; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">Save</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>


