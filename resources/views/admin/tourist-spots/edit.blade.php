<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Tourist Spot - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/tourist-spots-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Edit Tourist Spot</h1>
                    <p class="header-subtitle">Update the tourist spot information</p>
                </div>
                <a href="{{ route('admin.tourist-spots.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Tourist Spots
                </a>
            </div>
        </div>

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
            <form action="{{ route('admin.tourist-spots.update', $spot->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Tourist Spot Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $spot->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"
                        required>{{ old('description', $spot->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order"
                        value="{{ old('display_order', $spot->display_order) }}" min="0">
                    <small class="help-text">Lower numbers appear first</small>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $spot->is_active) ? 'checked' : '' }}>
                        <label for="is_active">Active (visible on kiosk)</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tourist Spot Image</label>
                    <div class="image-upload-container">
                        @if($spot->image)
                            <div class="current-image">
                                <img src="{{ $spot->image_url }}" alt="Current Image">
                                <p>Current image</p>
                            </div>
                        @else
                            <!-- Placeholder to keep grid structure if no image -->
                            <div class="current-image preview-upload-area">
                                <p>No image uploaded</p>
                            </div>
                        @endif

                        <div>
                            <div class="image-upload" onclick="document.getElementById('image').click()">
                                <input type="file" id="image" name="image" accept="image/*"
                                    onchange="previewImage(this, 'image_preview')">
                                <label class="image-upload-label">
                                    <svg class="image-upload-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span class="image-upload-text">Click to upload new image (optional)</span>
                                </label>
                            </div>
                            <div id="image_preview" class="image-preview"></div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.tourist-spots.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Tourist Spot
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Messages Container -->
    <div id="floating-messages"></div>

    <script>
        // Show floating message
        function showFloatingMessage(message, type = 'success') {
            const container = document.getElementById('floating-messages');
            const messageEl = document.createElement('div');
            messageEl.className = `floating-message ${type}`;

            const icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' :
                type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' :
                    '<i class="fas fa-exclamation-triangle"></i>';
            messageEl.innerHTML = `${icon}${message}`;

            container.appendChild(messageEl);

            // Trigger animation
            setTimeout(() => messageEl.classList.add('show'), 100);

            // Remove after 4 seconds
            setTimeout(() => {
                messageEl.classList.remove('show');
                setTimeout(() => container.removeChild(messageEl), 300);
            }, 4000);
        }

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">';
                };
                reader.readAsDataURL(file);
            }
        }

        // Show success message if redirected from successful update
        @if(session('success'))
            showFloatingMessage('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showFloatingMessage('{{ session('error') }}', 'error');
        @endif
    </script>
</body>

</html>