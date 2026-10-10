<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Municipal Information - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/municipal-info-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Edit Municipal Information</h1>
                    <p class="header-subtitle">Update the municipal information displayed on the kiosk</p>
                </div>
                <a href="{{ route('admin.dashboard.edit') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Edit Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="list-unstyled">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-container">
            <form action="{{ route('admin.municipal-information.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label for="city_name">City Name</label>
                        <input type="text" id="city_name" name="city_name"
                            value="{{ old('city_name', $municipalInfo->city_name ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="tagline">Tagline</label>
                        <input type="text" id="tagline" name="tagline"
                            value="{{ old('tagline', $municipalInfo->tagline ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="mayor_name">Mayor Name</label>
                        <input type="text" id="mayor_name" name="mayor_name"
                            value="{{ old('mayor_name', $municipalInfo->mayor_name ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="mayor_title">Mayor Title</label>
                        <input type="text" id="mayor_title" name="mayor_title"
                            value="{{ old('mayor_title', $municipalInfo->mayor_title ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email', $municipalInfo->email ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone"
                            value="{{ old('phone', $municipalInfo->phone ?? '') }}" required>
                    </div>

                    <div class="form-group full-width">
                        <label>Mayor Image</label>
                        @if($municipalInfo && $municipalInfo->mayor_image)
                            <div class="current-image">
                                <img src="{{ $municipalInfo->mayor_image_url }}" alt="Current Mayor Image">
                                <p>Current image</p>
                            </div>
                        @endif
                        <div class="image-upload" onclick="document.getElementById('mayor_image').click()">
                            <input type="file" id="mayor_image" name="mayor_image" accept="image/*"
                                onchange="previewImage(this, 'mayor_preview')">
                            <label class="image-upload-label">
                                <svg class="image-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="image-upload-text">Click to upload mayor image</span>
                            </label>
                        </div>
                        <div id="mayor_preview" class="image-preview"></div>
                    </div>

                    <div class="form-group full-width">
                        <label>Banner Image</label>
                        @if($municipalInfo && $municipalInfo->banner_image)
                            <div class="current-image">
                                <img src="{{ $municipalInfo->banner_image_url }}" alt="Current Banner Image">
                                <p>Current image</p>
                            </div>
                        @endif
                        <div class="image-upload" onclick="document.getElementById('banner_image').click()">
                            <input type="file" id="banner_image" name="banner_image" accept="image/*"
                                onchange="previewImage(this, 'banner_preview')">
                            <label class="image-upload-label">
                                <svg class="image-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="image-upload-text">Click to upload banner image</span>
                            </label>
                        </div>
                        <div id="banner_preview" class="image-preview"></div>
                    </div>

                    <div class="form-group full-width">
                        <label>City Seal</label>
                        @if($municipalInfo && $municipalInfo->city_seal)
                            <div class="current-image">
                                <img src="{{ $municipalInfo->city_seal_url }}" alt="Current City Seal">
                                <p>Current image</p>
                            </div>
                        @endif
                        <div class="image-upload" onclick="document.getElementById('city_seal').click()">
                            <input type="file" id="city_seal" name="city_seal" accept="image/*"
                                onchange="previewImage(this, 'seal_preview')">
                            <label class="image-upload-label">
                                <svg class="image-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="image-upload-text">Click to upload city seal</span>
                            </label>
                        </div>
                        <div id="seal_preview" class="image-preview"></div>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Municipal Information
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">';
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>

</html>