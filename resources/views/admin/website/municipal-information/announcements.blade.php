<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Announcements - Admin Panel</title>
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
                    <h1 class="header-title">Manage Announcements</h1>
                    <p class="header-subtitle">Add, edit, or remove announcements for the website</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i>
                        Add Announcement
                    </button>
                </div>
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

        <div class="content">
            @if($announcements->count() > 0)
                <div class="announcements-grid">
                    @foreach($announcements as $announcement)
                        <div class="announcement-card">
                            <span class="status-badge {{ $announcement->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                            </span>

                            @if($announcement->image)
                                <img src="{{ asset($announcement->image) }}" alt="Announcement Image" class="announcement-image">
                            @endif

                            @if($announcement->title)
                                <h3 class="announcement-title">{{ $announcement->title }}</h3>
                            @endif
                            <p class="announcement-content">{{ Str::limit(strip_tags($announcement->content), 150) }}</p>

                            <div class="announcement-meta">
                                <span><i class="fas fa-sort-numeric-down"></i> Order: {{ $announcement->order }}</span>
                            </div>

                            <div class="announcement-actions">
                                <button class="btn btn-warning" onclick="openEditModal({{ $announcement->id }})">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                <form action="{{ route('admin.website.announcements.delete', $announcement->id) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-bullhorn empty-icon"></i>
                    <h3>No Announcements</h3>
                    <p>Get started by adding the first announcement.</p>
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i>
                        Add First Announcement
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="announcementModal" class="form-modal">
        <div class="form-modal-content">
            <div class="modal-header">
                <h2 id="modalTitle" class="modal-title">Add New Announcement</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>

            <form id="announcementForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <div class="form-group">
                    <label for="title">Title (Optional)</label>
                    <input type="text" id="title" name="title" placeholder="Enter announcement title...">
                </div>

                <div class="form-group">
                    <label for="content">Content *</label>
                    <textarea id="content" name="content" rows="6" required
                        placeholder="Enter announcement content..."></textarea>
                </div>

                <div class="form-group">
                    <label>Image (Optional)</label>
                    <div class="image-upload" onclick="document.getElementById('imageInput').click()">
                        <input type="file" id="imageInput" name="image" accept="image/*" onchange="previewImage(this)">
                        <label class="image-upload-label">
                            <svg class="image-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="image-upload-text">Click to upload image</span>
                        </label>
                    </div>
                    <div id="imagePreview" class="image-preview"></div>
                </div>

                <div class="form-group">
                    <label for="order">Display Order</label>
                    <input type="number" id="order" name="order" value="0" min="0" placeholder="0">
                    <small>Lower numbers appear first</small>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" checked>
                    <label for="is_active">Active (visible on website)</label>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const announcements = @json($announcements->keyBy('id'));

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Announcement';
            document.getElementById('announcementForm').action = '{{ route('admin.website.announcements.store') }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('announcementForm').reset();
            document.getElementById('imagePreview').innerHTML = '';
            document.getElementById('is_active').checked = true;
            document.getElementById('announcementModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function openEditModal(id) {
            const announcement = announcements[id];
            if (!announcement) return;

            document.getElementById('modalTitle').textContent = 'Edit Announcement';
            document.getElementById('announcementForm').action = '{{ route('admin.website.announcements.update', ':id') }}'.replace(':id', id);
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('title').value = announcement.title || '';
            document.getElementById('content').value = announcement.content || '';
            document.getElementById('order').value = announcement.order || 0;
            document.getElementById('is_active').checked = announcement.is_active;

            if (announcement.image) {
                document.getElementById('imagePreview').innerHTML = '<img src="{{ asset('') }}' + announcement.image + '" alt="Preview">';
            } else {
                document.getElementById('imagePreview').innerHTML = '';
            }

            document.getElementById('announcementModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('announcementModal').classList.remove('show');
            document.body.style.overflow = 'auto';
        }

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

        // Close modal on outside click
        document.getElementById('announcementModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>

</html>