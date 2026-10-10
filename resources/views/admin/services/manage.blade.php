<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Services</title>
    <link rel="stylesheet" href="/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Manage Services</h1>
                    <p class="header-subtitle">Placeholder page. We will hook this to a Services CRUD next.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <img src="/assets/icons/civil-services.png" alt="Civil">
                </div>
                <h3 class="service-title">Civil Services</h3>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <img src="/assets/icons/transportation.png" alt="Transportation">
                </div>
                <h3 class="service-title">City Transportation Regulation Unit</h3>
            </div>
        </div>
    </div>
</body>

</html>