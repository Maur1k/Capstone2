<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Dashboard</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div
        style="max-width:1100px; margin:24px auto; background:#fff; border-radius:10px; box-shadow:0 10px 20px rgba(0,0,0,.06); padding:20px;">
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <h2 style="margin:0;">Edit Dashboard</h2>
            <a href="{{ route('admin.dashboard') }}">Back</a>
        </div>
        <div style="display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:16px; margin-top:16px;">
            <a href="{{ route('admin.settings.municipal.edit') }}"
                style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:24px; text-decoration:none; text-align:center;">
                <img src="/assets/icons/municipalinfo.png" alt="Municipal Info" style="width:96px;height:96px;">
                <div style="font-weight:700; margin-top:8px;">Edit Municipal Information</div>
            </a>
            <a href="{{ route('admin.settings.hero.edit') }}"
                style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:24px; text-decoration:none; text-align:center;">
                <img src="/assets/icons/background_kiosk.png" alt="Dashboard Photo" style="width:96px;height:96px;">
                <div style="font-weight:700; margin-top:8px;">Edit Dashboard Photo</div>
            </a>
        </div>
        <p style="margin-top:12px; color:#6b7280;">Manage municipal info and dashboard hero image.</p>
    </div>
</body>

</html>