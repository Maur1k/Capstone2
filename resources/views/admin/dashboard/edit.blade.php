<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-edit-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Edit Dashboard</h1>
                    <p class="header-subtitle">Manage kiosk content and municipal information</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="grid-container">
            <a href="{{ route('admin.photo-panel.index') }}" class="card">
                <div class="card-icon">
                    <img src="/assets/icons/building.png" alt="Photo Panel">
                </div>
                <h3 class="card-title">Manage Photo Panel</h3>
                <p class="card-description">Upload and organize photo panel images for the kiosk display</p>
            </a>

            <a href="{{ route('admin.municipal-information.edit') }}" class="card">
                <div class="card-icon">
                    <img src="/assets/icons/municipalinfo.png" alt="Municipal Info">
                </div>
                <h3 class="card-title">Municipal Information</h3>
                <p class="card-description">Update municipal details, contact information, and announcements</p>
            </a>

            <a href="{{ route('admin.city-officials.index') }}" class="card">
                <div class="card-icon">
                    <img src="/assets/icons/mayor.png" alt="City Officials">
                </div>
                <h3 class="card-title">City Officials</h3>
                <p class="card-description">Manage city officials profiles, positions, and contact details</p>
            </a>

            <a href="{{ route('admin.tourist-spots.index') }}" class="card">
                <div class="card-icon">
                    <img src="/assets/icons/3dmap.png" alt="Tourist Spots">
                </div>
                <h3 class="card-title">Tourist Spots</h3>
                <p class="card-description">Add and update tourist attractions and local destinations</p>
            </a>
        </div>

        <p class="footer-text">
            <i class="fas fa-info-circle text-teal-primary mr-2"></i>
            Select any option above to manage specific content areas for your municipal kiosk system
        </p>
    </div>
</body>

</html>