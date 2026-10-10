<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Service - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/services-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Add Service</h1>
                    <p class="header-subtitle">Create a new service for the kiosk system</p>
                </div>
                <a href="{{ route('admin.services.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Services
                </a>
            </div>
        </div>

        <div class="form-container">
            <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="name">Service Name <span class="text-required">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                        placeholder="Enter service name">
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <input id="department" name="department" type="text" value="{{ old('department') }}"
                        placeholder="Enter department name">
                    @error('department')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Enhanced File Upload Component -->
                <div class="file-upload-container">
                    <label class="file-upload-label">Service Icon</label>
                    <div class="file-upload-area" id="upload-area-icon">
                        <div class="file-upload-content">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <p class="upload-text">Click to upload or drag and drop</p>
                            <p class="upload-subtext">PNG, JPG, GIF up to 10MB</p>
                        </div>
                        <input type="file" name="icon" id="icon" accept="image/*" class="hidden">
                    </div>
                    <div class="file-preview" id="preview-icon" class="hidden">
                        <img id="preview-image-icon" class="preview-image" alt="Preview">
                        <div class="preview-actions">
                            <button type="button" class="remove-file-btn" onclick="removeFile('icon')">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                    @error('icon')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fallback text input for icon path -->
                <div class="form-group">
                    <label for="icon_path">Or enter icon path manually</label>
                    <input id="icon_path" name="icon_path" placeholder="/assets/icons/civil-services.png" type="text"
                        value="{{ old('icon_path') }}">
                    @error('icon_path')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <small class="help-text">
                        Enter a path to an existing icon file
                    </small>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_active" value="1" id="is_active" checked>
                        <label for="is_active">Active (visible on kiosk)</label>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Create Service
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Messages Container -->
    <div id="floating-messages"></div>

    <!-- Success Modal -->
    <div id="successModal" class="modal-overlay">
        <div class="modal-content">
            <i class="fas fa-check-circle modal-icon-success"></i>
            <h3 class="modal-title">Success!</h3>
            <p class="modal-message" id="successMessage"></p>
            <div class="modal-actions">
                <button type="button" class="modal-btn modal-btn-success" onclick="hideSuccessModal()">
                    <i class="fas fa-check"></i>
                    OK
                </button>
            </div>
        </div>
    </div>

    <script>
        // Show floating message
        function showFloatingMessage(message, type = 'success') {
            const container = document.getElementById('floating-messages');
            const messageEl = document.createElement('div');
            messageEl.className = `floating-message ${type}`;

            const icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' :
                type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' :
                    '<i class="fas fa-exclamation-triangle"></i>';
            messageEl.innerHTML = `${icon} <span>${message}</span>`;

            container.appendChild(messageEl);

            // Trigger animation
            setTimeout(() => messageEl.classList.add('show'), 100);

            // Remove after 4 seconds
            setTimeout(() => {
                messageEl.classList.remove('show');
                setTimeout(() => container.removeChild(messageEl), 300);
            }, 4000);
        }

        // File upload functionality
        document.addEventListener('DOMContentLoaded', function () {
            const uploadArea = document.getElementById('upload-area-icon');
            const fileInput = document.getElementById('icon');
            const preview = document.getElementById('preview-icon');
            const previewImage = document.getElementById('preview-image-icon');

            // Click to upload
            uploadArea.addEventListener('click', () => fileInput.click());

            // Drag and drop
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelect(files[0]);
                }
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            function handleFileSelect(file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImage.src = e.target.result;
                        preview.style.display = 'block';
                        uploadArea.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        function removeFile(name) {
            const fileInput = document.getElementById(name);
            const preview = document.getElementById('preview-' + name);
            const uploadArea = document.getElementById('upload-area-' + name);

            fileInput.value = '';
            preview.style.display = 'none';
            uploadArea.style.display = 'block';
        }

        // Success modal functions
        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.add('show');
        }

        function hideSuccessModal() {
            document.getElementById('successModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function (event) {
            const successModal = document.getElementById('successModal');
            if (event.target === successModal) {
                hideSuccessModal();
            }
        });

        // Show success modal if redirected from successful creation
        @if(session('status'))
            showSuccessModal('{{ session('status') }}');
        @endif

        @if(session('error'))
            showFloatingMessage('{{ session('error') }}', 'error');
        @endif
    </script>
</body>

</html>