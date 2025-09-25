<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Service</title>
    <link rel="stylesheet" href="/css/forms.css">
</head>

<body style="background:#e6f7ff; min-height:100vh; display:flex; align-items:center; justify-content:center;">
    <div
        style="width:900px; background:#fff; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:32px; display:flex; gap:24px;">
        <div
            style="flex:1; background:linear-gradient(180deg,#0299d9,#0079b8); color:#fff; border-radius:8px; padding:24px;">
            <a href="{{ route('admin.services.index') }}"
                style="display:inline-block; color:#fff; text-decoration:none; font-weight:600;">← Back</a>
            <h2 style="margin-top:24px;">ADD SERVICE</h2>
        </div>
        <div style="flex:2; display:flex; align-items:center; justify-content:center;">
            <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data"
                style="width:420px; background:#f7f9fb; border-radius:10px; padding:24px;">
                @csrf
                <h3 style="margin-bottom:16px;">Add Service</h3>
                <div style="margin-bottom:12px;">
                    <label for="name" style="display:block; font-size:14px; margin-bottom:6px;">Service Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                        style="width:100%; padding:10px 12px; border:1px solid #cfe2f3; border-radius:6px;">
                </div>
                <div style="margin-bottom:12px;">
                    <label for="department" style="display:block; font-size:14px; margin-bottom:6px;">Department</label>
                    <input id="department" name="department" type="text" value="{{ old('department') }}"
                        style="width:100%; padding:10px 12px; border:1px solid #cfe2f3; border-radius:6px;">
                </div>

                <!-- Modern File Upload Component -->
                <x-file-upload name="icon" label="Service Icon" accept="image/*" required="false" />

                <!-- Fallback text input for icon path -->
                <div style="margin-bottom:12px;">
                    <label for="icon_path" style="display:block; font-size:14px; margin-bottom:6px;">Or enter icon path
                        manually</label>
                    <input id="icon_path" name="icon_path" placeholder="/assets/icons/civil-services.png" type="text"
                        value="{{ old('icon_path') }}"
                        style="width:100%; padding:10px 12px; border:1px solid #cfe2f3; border-radius:6px;">
                </div>

                <label style="display:flex; gap:8px; align-items:center; margin-bottom:12px;">
                    <input type="checkbox" name="is_active" value="1" checked> Active
                </label>
                <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px;">
                    <button type="submit"
                        style="background:#0ea5e9; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">Create</button>
                    <a href="{{ route('admin.services.index') }}"
                        style="text-decoration:none; padding:10px 16px; border-radius:6px; background:#e5e7eb;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>