<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit About Content - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/about-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Edit About Content</h1>
                    <p class="header-subtitle">Update about content for the website</p>
                </div>
                <a href="{{ route('admin.about.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to About List
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
            <form action="{{ route('admin.about.update', $about->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $about->title) }}" required>
                </div>

                <div class="form-group">
                    <label for="content">Content *</label>
                    <textarea id="content" name="content" rows="10"
                        required>{{ old('content', $about->content) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Image</label>
                    @if($about->image)
                        <div class="current-image">
                            <p>Current Image:</p>
                            <img src="{{ asset($about->image) }}" alt="Current">
                        </div>
                    @endif
                    <div class="image-upload" onclick="document.getElementById('imageInput').click()">
                        <input type="file" id="imageInput" name="image" accept="image/*" onchange="previewImage(this)">
                        <label class="image-upload-label">
                            <svg class="image-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="image-upload-text">Click to upload new image</span>
                        </label>
                    </div>
                    <div id="imagePreview" class="image-preview"></div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $about->is_active) ? 'checked' : '' }}>
                    <label for="is_active">Active</label>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.about.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update About Content
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        }
    </script>
</body>

</html>