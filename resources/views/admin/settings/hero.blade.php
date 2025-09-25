<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Dashboard Photo</title>
    <link rel="stylesheet" href="/css/forms.css">
</head>

<body style="background:#e6f7ff; min-height:100vh; display:flex; align-items:center; justify-content:center;">
    <div
        style="width:900px; background:#fff; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:32px; display:flex; gap:24px;">
        <div
            style="flex:1; background:linear-gradient(180deg,#0299d9,#0079b8); color:#fff; border-radius:8px; padding:24px;">
            <a href="{{ route('admin.dashboard') }}"
                style="display:inline-block; color:#fff; text-decoration:none; font-weight:600;">← Back</a>
            <h2 style="margin-top:24px;">EDIT DASHBOARD PHOTO</h2>
        </div>
        <div style="flex:2; display:flex; align-items:center; justify-content:center;">
            <form method="POST" action="{{ route('admin.settings.hero.update') }}" enctype="multipart/form-data"
                style="width:420px; background:#f7f9fb; border-radius:10px; padding:24px;">
                @csrf
                @method('PUT')
                @if(session('status'))
                    <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:6px;margin-bottom:12px;">
                        {{ session('status') }}</div>
                @endif
                
                <!-- Modern File Upload Component -->
                <x-file-upload 
                    name="image" 
                    label="Dashboard Hero Image" 
                    accept="image/*"
                    current="{{ $hero }}"
                    required="true" />
                
                <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px;">
                    <button type="submit"
                        style="background:#0ea5e9; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">Save</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
