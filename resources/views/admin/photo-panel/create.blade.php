<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Photo Panel Image - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/photo-panel-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Add Photo Panel Image</h1>
                    <p class="header-subtitle">Upload a new image for the photo panel display</p>
                </div>
                <a href="{{ route('admin.photo-panel.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Images
                </a>
            </div>
        </div>
        
        <div class="form-container">
            <form action="{{ route('admin.photo-panel.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label for="title">Title <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   placeholder="Enter image title">
                            @error('title')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>                 

                        <div class="form-group">
                            <label for="display_order">Display Order</label>
                            <input type="number" 
                                   id="display_order" 
                                   name="display_order" 
                                   value="{{ old('display_order', 0) }}"
                                   min="0"
                                   placeholder="Enter display order">
                            @error('display_order')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                            <small>Lower numbers appear first. Leave empty for auto-assignment.</small>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active">Active (visible on kiosk)</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label for="image">Image <span class="text-red-500">*</span></label>
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*"
                                   onchange="previewImage(this)">
                            @error('image')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                            <small>
                                Recommended size: 1180x540px. Max file size: 10MB.
                                Supported formats: JPEG, PNG, JPG, GIF.
                            </small>
                        </div>

                        <div id="image-preview" class="image-preview">
                            <img id="preview-img" src="" alt="Preview">
                            <div class="mt-2">
                                <small class="text-gray-500">Image preview:</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.photo-panel.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Image
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

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    const previewImg = document.getElementById('preview-img');
                    
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Show success message if redirected from successful creation
        @if(session('success'))
            showFloatingMessage('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showFloatingMessage('{{ session('error') }}', 'error');
        @endif
    </script>
</body>
</html>
