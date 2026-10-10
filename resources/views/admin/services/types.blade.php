<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Service Types - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/services-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">{{ $service->name }} - Types</h1>
                    <p class="header-subtitle">Manage service types and their configurations</p>
                </div>
                <a href="{{ route('admin.services.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Services
                </a>
            </div>
        </div>

        <div class="content">
            <!-- Add Type Form -->
            <div class="form-section">
                <h2 class="form-title">
                    <i class="fas fa-plus-circle"></i>
                    Add Type
                </h2>

                <form method="POST" action="{{ route('admin.services.types.store', $service) }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="name">Type Name <span class="text-required">*</span></label>
                        <input name="name" id="name" placeholder="Enter type name" required>
                    </div>

                    <div class="form-group">
                        <label>Type Icon</label>
                        <div class="drag-drop-area" data-input-id="icon-upload"
                            onclick="document.getElementById('icon-upload').click()">
                            <div id="icon-preview-container" class="hidden">
                                <img id="icon-preview" class="icon-preview" alt="Preview">
                                <div class="preview-hint">Click to change
                                </div>
                            </div>
                            <div id="icon-placeholder">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <div class="upload-text">Drag & drop icon or click to upload</div>
                                <div class="upload-subtext">PNG, JPG, GIF, SVG (max 10MB)</div>
                            </div>
                        </div>
                        <input type="file" id="icon-upload" name="icon" accept="image/*" class="hidden"
                            onchange="handleIconUpload(this)">
                    </div>

                    <div class="form-group">
                        <label for="icon_path">Or enter icon path manually</label>
                        <input name="icon_path" id="icon-path-input" placeholder="/assets/icons/civil-services.png">
                        <small class="help-text">
                            Enter a path to an existing icon file
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full" onclick="return validateForm(this)">
                        <i class="fas fa-plus"></i>
                        Add Type
                    </button>
                </form>
            </div>

            <!-- Types Grid -->
            <div>
                @if($types->count() > 0)
                    <div class="sort-controls-container">
                        <div class="sort-controls-header">
                            <div>
                                <h3 class="sort-title">
                                    <i class="fas fa-sort"></i> Sort Order
                                </h3>
                                <p class="sort-description">
                                    Drag items to reorder or use arrow buttons
                                </p>
                            </div>
                            <button type="button" onclick="saveSortOrder()" class="btn btn-primary" id="save-sort-btn"
                                class="hidden">
                                <i class="fas fa-save"></i> Save Order
                            </button>
                        </div>
                    </div>
                    <div class="types-grid" id="types-container">
                        @foreach($types as $t)
                            <div class="type-card type-card-relative" data-type-id="{{ $t->id }}">
                                <div class="type-sort-handle">
                                    <i class="fas fa-grip-vertical icon-gray-500"></i>
                                </div>
                                <div class="type-sort-arrows">
                                    <button type="button" onclick="moveTypeUp({{ $t->id }})" class="sort-arrow-btn"
                                        title="Move Up">
                                        <i class="fas fa-arrow-up icon-xs"></i>
                                    </button>
                                    <button type="button" onclick="moveTypeDown({{ $t->id }})" class="sort-arrow-btn"
                                        title="Move Down">
                                        <i class="fas fa-arrow-down icon-xs"></i>
                                    </button>
                                </div>
                                <div class="type-header">
                                    <img src="{{ $t->icon_path ? (str_starts_with($t->icon_path, 'http') ? $t->icon_path : (str_starts_with($t->icon_path, '/') ? asset($t->icon_path) : asset('/' . $t->icon_path))) . '?v=' . ($t->updated_at ? $t->updated_at->timestamp : $t->id . '_' . time()) : asset('assets/icons/civil-services.png') . '?v=' . time() }}"
                                        class="icon-preview" data-icon-path="{{ $t->icon_path }}" alt="icon"
                                        onerror="this.onerror=null; this.src='{{ asset('assets/icons/civil-services.png') }}';">
                                    <div class="type-name">{{ $t->name }}</div>
                                </div>

                                <div class="type-actions">
                                    <a href="{{ route('admin.services.steps', [$service, $t]) }}" class="btn btn-secondary">
                                        <i class="fas fa-list"></i>
                                        Steps
                                    </a>
                                    <button onclick="toggleEditForm({{ $t->id }})" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.services.types.update', [$service, $t]) }}"
                                        class="form-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" value="{{ $t->name }}">
                                        <input type="hidden" name="icon_path" value="{{ $t->icon_path }}">
                                        <input type="hidden" name="is_active" value="{{ $t->is_active ? 0 : 1 }}">
                                        <button type="submit" class="btn {{ $t->is_active ? 'btn-warning' : 'btn-secondary' }}">
                                            <i class="fas {{ $t->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            {{ $t->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <button onclick="showDeleteModal({{ $t->id }}, '{{ addslashes($t->name) }}')"
                                        class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </div>

                                <!-- Edit Form -->
                                <div id="edit-form-{{ $t->id }}" class="edit-form">
                                    <form method="POST" action="{{ route('admin.services.types.update', [$service, $t]) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="form-group">
                                            <label for="edit-name-{{ $t->id }}">Type Name</label>
                                            <input name="name" id="edit-name-{{ $t->id }}" value="{{ $t->name }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Change Icon</label>
                                            @if($t->icon_path)
                                                <div class="current-image-preview text-center mb-12">
                                                    <img src="{{ $t->icon_path ? (str_starts_with($t->icon_path, 'http') ? $t->icon_path : (str_starts_with($t->icon_path, '/') ? asset($t->icon_path) : asset('/' . $t->icon_path))) . '?v=' . ($t->updated_at ? $t->updated_at->timestamp : $t->id . '_' . time()) : asset('assets/icons/civil-services.png') . '?v=' . time() }}"
                                                        class="icon-preview w-16 h-16" alt="Current Icon"
                                                        data-icon-path="{{ $t->icon_path }}"
                                                        onerror="this.onerror=null; this.src='{{ asset('assets/icons/civil-services.png') }}';">
                                                    <div class="text-sm text-gray-500 mt-4">
                                                        Current Icon</div>
                                                </div>
                                            @endif
                                            <div class="drag-drop-area" data-input-id="edit-icon-upload-{{ $t->id }}"
                                                onclick="document.getElementById('edit-icon-upload-{{ $t->id }}').click()">
                                                <div id="edit-icon-preview-container-{{ $t->id }}" class="hidden">
                                                    <img id="edit-icon-preview-{{ $t->id }}" class="icon-preview" alt="Preview">
                                                    <div class="preview-hint">Click to
                                                        change</div>
                                                </div>
                                                <div id="edit-icon-placeholder-{{ $t->id }}">
                                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                                    <div class="upload-text">Change icon</div>
                                                </div>
                                            </div>
                                            <input type="file" id="edit-icon-upload-{{ $t->id }}" name="icon" accept="image/*"
                                                class="hidden" onchange="handleEditIconUpload(this, {{ $t->id }})">
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-icon-path-{{ $t->id }}">Icon Path</label>
                                            <input name="icon_path" id="edit-icon-path-{{ $t->id }}"
                                                value="{{ $t->icon_path }}">
                                        </div>

                                        <div class="edit-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i>
                                                Save
                                            </button>
                                            <button type="button" onclick="toggleEditForm({{ $t->id }})"
                                                class="btn btn-secondary">
                                                <i class="fas fa-times"></i>
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <h3>No Types Yet</h3>
                        <p>Start by adding your first service type using the form on the left.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Floating Messages Container -->
    <div id="floating-messages"></div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle modal-icon"></i>
            <h3 class="modal-title">Delete Service Type</h3>
            <p class="modal-message">Are you sure you want to delete "<span id="typeName"></span>"? This action cannot
                be undone.</p>
            <div class="modal-actions">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="hideDeleteModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="form-inline">
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

        // Drag and drop functionality
        function setupDragDrop() {
            const dragDropAreas = document.querySelectorAll('.drag-drop-area');

            dragDropAreas.forEach(area => {
                // Find the associated file input using data attribute
                const inputId = area.getAttribute('data-input-id');
                const fileInput = inputId ? document.getElementById(inputId) : null;

                if (!fileInput) {
                    // Fallback: try to find input in parent form-group
                    const formGroup = area.closest('.form-group');
                    if (formGroup) {
                        const foundInput = formGroup.querySelector('input[type="file"]');
                        if (foundInput) {
                            area.setAttribute('data-input-id', foundInput.id);
                        }
                    }
                    return;
                }

                // Prevent default drag behaviors
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    area.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                area.addEventListener('dragover', (e) => {
                    area.classList.add('dragover');
                });

                area.addEventListener('dragleave', (e) => {
                    area.classList.remove('dragover');
                });

                area.addEventListener('drop', (e) => {
                    area.classList.remove('dragover');
                    const files = e.dataTransfer.files;

                    if (files.length > 0) {
                        // Use FormData to handle file properly
                        if (fileInput.files && fileInput.files.length > 0) {
                            // Clear previous file
                            fileInput.value = '';
                        }

                        // Set the file using DataTransfer (modern browsers)
                        if (typeof DataTransfer !== 'undefined') {
                            try {
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(files[0]);
                                fileInput.files = dataTransfer.files;
                            } catch (err) {
                                // Fallback for older browsers
                                console.warn('DataTransfer not supported, using alternative method');
                            }
                        }

                        // Trigger change event to update preview
                        const changeEvent = new Event('change', { bubbles: true });
                        fileInput.dispatchEvent(changeEvent);
                    }
                });
            });
        }

        // Handle icon upload for new types
        function handleIconUpload(input) {
            console.log('handleIconUpload called', input);
            const file = input.files[0];
            console.log('File selected:', file);

            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    showFloatingMessage('Please select an image file', 'error');
                    input.value = '';
                    return;
                }

                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    showFloatingMessage('File size must be less than 10MB', 'error');
                    input.value = '';
                    return;
                }

                // Clear icon_path input when file is selected
                const iconPathInput = document.getElementById('icon-path-input');
                if (iconPathInput) {
                    iconPathInput.value = '';
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('icon-preview');
                    const container = document.getElementById('icon-preview-container');
                    const placeholder = document.getElementById('icon-placeholder');

                    if (preview && container && placeholder) {
                        preview.src = e.target.result;
                        container.style.display = 'block';
                        placeholder.style.display = 'none';
                        console.log('Preview updated successfully');
                    } else {
                        console.error('Preview elements not found', { preview, container, placeholder });
                    }
                };
                reader.onerror = function () {
                    console.error('FileReader error');
                    showFloatingMessage('Error reading file', 'error');
                };
                reader.readAsDataURL(file);
            } else {
                console.log('No file selected');
                // Reset preview if no file
                const container = document.getElementById('icon-preview-container');
                const placeholder = document.getElementById('icon-placeholder');
                if (container && placeholder) {
                    container.style.display = 'none';
                    placeholder.style.display = 'block';
                }
            }
        }

        // Handle icon upload for edit forms
        function handleEditIconUpload(input, typeId) {
            const file = input.files[0];
            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    showFloatingMessage('Please select an image file', 'error');
                    input.value = '';
                    return;
                }

                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    showFloatingMessage('File size must be less than 10MB', 'error');
                    input.value = '';
                    return;
                }

                // Clear icon_path input when file is selected
                const iconPathInput = document.getElementById(`edit-icon-path-${typeId}`);
                if (iconPathInput) {
                    iconPathInput.value = '';
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById(`edit-icon-preview-${typeId}`);
                    const container = document.getElementById(`edit-icon-preview-container-${typeId}`);
                    const placeholder = document.getElementById(`edit-icon-placeholder-${typeId}`);

                    if (preview && container && placeholder) {
                        preview.src = e.target.result;
                        container.style.display = 'block';
                        placeholder.style.display = 'none';
                    }
                };
                reader.onerror = function () {
                    showFloatingMessage('Error reading file', 'error');
                };
                reader.readAsDataURL(file);
            } else {
                // Reset preview if no file
                const container = document.getElementById(`edit-icon-preview-container-${typeId}`);
                const placeholder = document.getElementById(`edit-icon-placeholder-${typeId}`);
                if (container && placeholder) {
                    container.style.display = 'none';
                    placeholder.style.display = 'block';
                }
            }
        }

        // Toggle edit form
        function toggleEditForm(typeId) {
            const form = document.getElementById(`edit-form-${typeId}`);
            if (form.classList.contains('active')) {
                form.classList.remove('active');
            } else {
                // Close all other edit forms
                document.querySelectorAll('.edit-form.active').forEach(f => f.classList.remove('active'));
                form.classList.add('active');
            }
        }

        // Delete modal functions
        function showDeleteModal(typeId, typeName) {
            document.getElementById('typeName').textContent = typeName;
            document.getElementById('deleteForm').action = '{{ route("admin.services.types.destroy", [$service, ":typeId"]) }}'.replace(':typeId', typeId);
            document.getElementById('deleteModal').classList.add('show');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        // Success modal functions
        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.add('show');
        }

        function hideSuccessModal() {
            document.getElementById('successModal').classList.remove('show');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function (event) {
            const deleteModal = document.getElementById('deleteModal');
            const successModal = document.getElementById('successModal');

            if (event.target === deleteModal) {
                hideDeleteModal();
            }
            if (event.target === successModal) {
                hideSuccessModal();
            }
        });

        // Form validation before submit
        function validateForm(button) {
            const form = button.closest('form');
            const fileInput = form.querySelector('input[type="file"][name="icon"]');
            const iconPathInput = form.querySelector('input[name="icon_path"]');

            // Check if either file or icon_path is provided
            if (!fileInput.files || fileInput.files.length === 0) {
                if (!iconPathInput || !iconPathInput.value || iconPathInput.value.trim() === '') {
                    // Neither file nor path provided - this is okay, will use default
                    console.log('No icon provided, will use default');
                }
            } else {
                console.log('File will be uploaded:', fileInput.files[0].name);
            }

            return true; // Allow form submission
        }

        // Initialize drag and drop when page loads
        document.addEventListener('DOMContentLoaded', function () {
            setupDragDrop();

            // Test file input accessibility
            const fileInput = document.getElementById('icon-upload');
            if (fileInput) {
                console.log('File input found:', fileInput);
                console.log('File input name:', fileInput.name);
            } else {
                console.error('File input not found!');
            }
        });

        // Show success modal if redirected from successful action
        @if(session('status'))
            showSuccessModal('{{ session('status') }}');
            // Force refresh all images after update to bypass cache
            setTimeout(function () {
                document.querySelectorAll('img.icon-preview[data-icon-path]').forEach(function (img) {
                    const iconPath = img.getAttribute('data-icon-path');
                    if (iconPath && iconPath.trim() !== '') {
                        // Build fresh URL with current timestamp
                        let baseUrl = '';
                        if (iconPath.startsWith('http')) {
                            baseUrl = iconPath;
                        } else if (iconPath.startsWith('/')) {
                            baseUrl = '{{ url("") }}' + iconPath;
                        } else {
                            baseUrl = '{{ url("") }}/' + iconPath;
                        }
                        // Add aggressive cache-busting
                        const newUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'v=' + Date.now() + '_' + Math.random();
                        // Force reload
                        img.src = '';
                        setTimeout(function () {
                            img.src = newUrl;
                        }, 100);
                    }
                });
            }, 300);
        @endif

        @if(session('error'))
            showFloatingMessage('{{ session('error') }}', 'error');
        @endif

        // Sorting functionality
        let sortOrderChanged = false;
        let currentOrder = [];

        // Initialize sortable with SortableJS (if available) or use manual drag
        function initializeSorting() {
            const container = document.getElementById('types-container');
            if (!container) return;

            // Get initial order
            updateCurrentOrder();

            // Make cards draggable
            const cards = container.querySelectorAll('.type-card');
            cards.forEach(card => {
                const handle = card.querySelector('.sort-handle');
                if (handle) {
                    handle.addEventListener('mousedown', startDrag);
                }
            });
        }

        function updateCurrentOrder() {
            const container = document.getElementById('types-container');
            if (!container) return;

            currentOrder = Array.from(container.querySelectorAll('.type-card')).map(card =>
                parseInt(card.getAttribute('data-type-id'))
            );
        }

        let draggedElement = null;
        let draggedIndex = -1;

        function startDrag(e) {
            draggedElement = e.target.closest('.type-card');
            if (!draggedElement) return;

            draggedIndex = Array.from(draggedElement.parentNode.children).indexOf(draggedElement);
            draggedElement.style.opacity = '0.5';
            draggedElement.style.cursor = 'grabbing';

            document.addEventListener('mousemove', onDrag);
            document.addEventListener('mouseup', stopDrag);
            e.preventDefault();
        }

        function onDrag(e) {
            if (!draggedElement) return;

            const container = document.getElementById('types-container');
            const cards = Array.from(container.querySelectorAll('.type-card'));
            const afterElement = cards.reduce((closest, child, index) => {
                const box = child.getBoundingClientRect();
                const offset = e.clientY - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child, index: index };
                }
                return closest;
            }, { offset: Number.NEGATIVE_INFINITY }).element;

            if (afterElement && afterElement !== draggedElement) {
                container.insertBefore(draggedElement, afterElement);
                sortOrderChanged = true;
                document.getElementById('save-sort-btn').style.display = 'block';
            }
        }

        function stopDrag() {
            if (draggedElement) {
                draggedElement.style.opacity = '1';
                draggedElement.style.cursor = '';
                draggedElement = null;
            }
            document.removeEventListener('mousemove', onDrag);
            document.removeEventListener('mouseup', stopDrag);
        }

        function moveTypeUp(typeId) {
            const container = document.getElementById('types-container');
            const cards = Array.from(container.querySelectorAll('.type-card'));
            const currentIndex = cards.findIndex(card => parseInt(card.getAttribute('data-type-id')) === typeId);

            if (currentIndex > 0) {
                container.insertBefore(cards[currentIndex], cards[currentIndex - 1]);
                sortOrderChanged = true;
                document.getElementById('save-sort-btn').style.display = 'block';
            }
        }

        function moveTypeDown(typeId) {
            const container = document.getElementById('types-container');
            const cards = Array.from(container.querySelectorAll('.type-card'));
            const currentIndex = cards.findIndex(card => parseInt(card.getAttribute('data-type-id')) === typeId);

            if (currentIndex < cards.length - 1) {
                container.insertBefore(cards[currentIndex + 1], cards[currentIndex]);
                sortOrderChanged = true;
                document.getElementById('save-sort-btn').style.display = 'block';
            }
        }

        function saveSortOrder() {
            const container = document.getElementById('types-container');
            const order = Array.from(container.querySelectorAll('.type-card')).map(card =>
                parseInt(card.getAttribute('data-type-id'))
            );

            fetch('{{ route("admin.services.types.reorder", $service) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ order: order })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showFloatingMessage('Order saved successfully', 'success');
                        sortOrderChanged = false;
                        document.getElementById('save-sort-btn').style.display = 'none';
                        updateCurrentOrder();
                    } else {
                        showFloatingMessage('Error saving order', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFloatingMessage('Error saving order', 'error');
                });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            initializeSorting();
        });
    </script>
</body>

</html>