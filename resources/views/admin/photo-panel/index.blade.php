<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Photo Panel Management - Admin Panel</title>
    <link rel="stylesheet" href="/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/photo-panel-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Photo Panel Management</h1>
                    <p class="header-subtitle">Manage and organize photo panel images for the kiosk display</p>
                </div>
                <div class="flex-gap-12">
                    <a href="{{ route('admin.dashboard.edit') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Edit Dashboard
                    </a>
                    <a href="{{ route('admin.photo-panel.create') }}" class="add-image-btn">
                        <i class="fas fa-plus"></i>
                        Add New Image
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if($images->count() > 0)
            <div class="images-grid" id="sortable-images">
                @foreach($images as $image)
                    <div class="image-card" data-id="{{ $image->id }}">
                        <div class="image-container">
                            <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}">
                            <div class="status-badge {{ $image->is_active ? 'active' : 'inactive' }}">
                                {{ $image->is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                        <div class="image-content">
                            <h3 class="image-title">{{ $image->title ?: 'Untitled' }}</h3>
                            <p class="image-order">Order: {{ $image->display_order }}</p>
                            @if($image->description)
                                <p class="image-description">{{ Str::limit($image->description, 100) }}</p>
                            @endif
                            <div class="image-actions">
                                <a href="{{ route('admin.photo-panel.edit', $image->id) }}" class="action-btn edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="action-btn delete"
                                    onclick="showDeleteModal({{ $image->id }}, '{{ addslashes($image->title ?: 'Untitled') }}')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <h3>No Images Found</h3>
                <p>Start by adding your first photo panel image to create an engaging kiosk display.</p>
                <a href="{{ route('admin.photo-panel.create') }}" class="btn">
                    <i class="fas fa-plus"></i> Add First Image
                </a>
            </div>
        @endif
    </div>

    <!-- Floating Messages Container -->
    <div id="floating-messages"></div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle modal-icon"></i>
            <h3 class="modal-title">Delete Photo Panel Image</h3>
            <p class="modal-message">Are you sure you want to delete "<span id="imageTitle"></span>"? This action cannot
                be undone.</p>
            <div class="modal-actions">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="hideDeleteModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="modal-btn modal-btn-confirm">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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

        // Show delete confirmation modal
        function showDeleteModal(imageId, imageTitle) {
            document.getElementById('imageTitle').textContent = imageTitle;
            document.getElementById('deleteForm').action = `/admin/photo-panel/${imageId}`;
            document.getElementById('deleteModal').classList.add('show');
        }

        // Hide delete confirmation modal
        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        // Handle delete form submission
        document.getElementById('deleteForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    hideDeleteModal();
                    if (data.success) {
                        showFloatingMessage('Photo panel image deleted successfully!', 'success');
                        // Reload page after a short delay
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showFloatingMessage('Error deleting photo panel image. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    hideDeleteModal();
                    showFloatingMessage('Error deleting photo panel image. Please try again.', 'error');
                });
        });

        // Close modal when clicking overlay
        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });

        // Show success message if redirected from create/edit
        @if(session('success'))
            showFloatingMessage('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showFloatingMessage('{{ session('error') }}', 'error');
        @endif

        document.addEventListener('DOMContentLoaded', function () {
            const sortable = Sortable.create(document.getElementById('sortable-images'), {
                animation: 150,
                onEnd: function (evt) {
                    const images = Array.from(document.querySelectorAll('#sortable-images > div')).map((el, index) => ({
                        id: el.dataset.id,
                        display_order: index
                    }));

                    fetch('{{ route("admin.photo-panel.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ images: images })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showFloatingMessage('Image order updated successfully!', 'success');
                            } else {
                                showFloatingMessage('Error updating image order. Please try again.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showFloatingMessage('Error updating image order. Please try again.', 'error');
                        });
                }
            });
        });
    </script>
</body>

</html>