<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Services</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh;">
    <div
        style="max-width:1100px; margin:24px auto; background:#fff; border-radius:10px; box-shadow:0 10px 20px rgba(0,0,0,.06); padding:20px;">
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <h2 style="margin:0;">Manage Services</h2>
            <a href="{{ route('admin.dashboard') }}">Back</a>
        </div>
        <p style="margin-top:10px; color:#6b7280;">Placeholder page. We will hook this to a Services CRUD next.</p>
        <div style="display:flex; gap:16px; flex-wrap:wrap; margin-top:12px;">
            <div
                style="width:240px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; text-align:center;">
                <img src="/assets/icons/civil-services.png" alt="Civil" style="width:72px;height:72px;">
                <div style="margin-top:8px; font-weight:600;">Civil Services</div>
            </div>
            <div
                style="width:240px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; text-align:center;">
                <img src="/assets/icons/transportation.png" alt="Transportation" style="width:72px;height:72px;">
                <div style="margin-top:8px; font-weight:600;">City Transportation Regulation Unit</div>
            </div>
        </div>
    </div>
</body>

</html>