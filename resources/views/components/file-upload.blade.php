@props(['name', 'label', 'accept' => 'image/*', 'current' => null, 'required' => false])

<div class="file-upload-container">
    <label for="{{ $name }}" class="file-upload-label">{{ $label }}</label>

    @if($current)
        <div class="current-image-preview">
            <img src="{{ $current }}" alt="Current" class="current-image">
            <span class="current-label">Current Image</span>
        </div>
    @endif

    <div class="file-upload-area" id="upload-area-{{ $name }}">
        <div class="file-upload-content">
            <svg class="upload-icon" width="48" height="48" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                <path d="M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            <p class="upload-text">Click to upload or drag and drop</p>
            <p class="upload-subtext">{{ $accept === 'image/*' ? 'PNG, JPG, GIF up to 10MB' : 'Any file up to 10MB' }}
            </p>
        </div>
        <input type="file" name="{{ $name }}" id="{{ $name }}" accept="{{ $accept }}" class="file-input" {{ $required ? 'required' : '' }} style="display: none;">
    </div>

    <div class="file-preview" id="preview-{{ $name }}" style="display: none;">
        <img id="preview-image-{{ $name }}" class="preview-image" alt="Preview">
        <div class="preview-actions">
            <button type="button" class="remove-file-btn" onclick="removeFile('{{ $name }}')">Remove</button>
        </div>
    </div>
</div>

<style>
    .file-upload-container {
        margin-bottom: 16px;
    }

    .file-upload-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #374151;
    }

    .current-image-preview {
        margin-bottom: 12px;
        text-align: center;
    }

    .current-image {
        max-width: 120px;
        max-height: 120px;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        object-fit: cover;
    }

    .current-label {
        display: block;
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }

    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f9fafb;
    }

    .file-upload-area:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .file-upload-area.dragover {
        border-color: #3b82f6;
        background: #eff6ff;
        transform: scale(1.02);
    }

    .file-upload-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .upload-icon {
        color: #9ca3af;
        transition: color 0.2s ease;
    }

    .file-upload-area:hover .upload-icon {
        color: #3b82f6;
    }

    .upload-text {
        font-size: 16px;
        font-weight: 500;
        color: #374151;
        margin: 0;
    }

    .upload-subtext {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
    }

    .file-preview {
        margin-top: 12px;
        text-align: center;
    }

    .preview-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        object-fit: cover;
    }

    .preview-actions {
        margin-top: 8px;
    }

    .remove-file-btn {
        background: #ef4444;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.2s ease;
    }

    .remove-file-btn:hover {
        background: #dc2626;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadArea = document.getElementById('upload-area-{{ $name }}');
        const fileInput = document.getElementById('{{ $name }}');
        const preview = document.getElementById('preview-{{ $name }}');
        const previewImage = document.getElementById('preview-image-{{ $name }}');

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
</script>




