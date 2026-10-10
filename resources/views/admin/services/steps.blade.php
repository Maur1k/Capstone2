<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Service Steps - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/services-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">{{ $service->name }} → {{ $type->name }} - Steps</h1>
                    <p class="header-subtitle">Manage the step-by-step process for this service type</p>
                </div>
                <a href="{{ route('admin.services.types', $service) }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Types
                </a>
        </div>
            </div>

        <!-- Instructions -->
        <div class="instructions">
            <div class="instructions-header">
                <span class="instructions-icon">
                    <i class="fas fa-info-circle"></i>
                </span>
                <strong class="instructions-title">How to edit steps:</strong>
            </div>
            <ul class="instructions-list">
                <li>Click <strong>"Edit"</strong> button next to any step to modify it</li>
                <li>Use <strong>"Order"</strong> field to change the display sequence</li>
                <li>Press <strong>Escape</strong> key to cancel editing</li>
                <li>Only one step can be edited at a time</li>
            </ul>
        </div>

        <!-- Add Step Form -->
        <div class="add-step-form">
            <h2 class="form-title">
                <i class="fas fa-plus-circle"></i>
                Add New Step
            </h2>
            
            <form method="POST" action="{{ route('admin.services.steps.store', [$service, $type]) }}">
            @csrf
                
                <div class="form-group">
                    <label for="title">Step Title <span class="text-required">*</span></label>
                    <input name="title" id="title" placeholder="Enter step title" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Step Details <span class="text-required">*</span></label>
                    <textarea name="content" id="content" placeholder="Enter step details (bullet points allowed)" rows="3" required></textarea>
            </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Step
                </button>
        </form>
        </div>

        <!-- Steps List -->
        <div class="steps-container">
            @forelse($steps as $step)
                <div id="step-{{ $step->id }}" class="step-card">
                    <!-- Display Mode -->
                    <div id="display-{{ $step->id }}">
                        <div class="step-header">
                            <div class="step-info">
                                <span class="step-number">{{ $step->display_order }}</span>
                                <div class="step-title">{{ $step->title }}</div>
                            </div>
                            <div class="step-actions">
                                <button onclick="editStep({{ $step->id }})" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                <button onclick="showDeleteModal({{ $step->id }}, '{{ addslashes($step->title) }}')" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div class="step-content">{{ $step->content }}</div>
                    </div>

                    <!-- Edit Mode -->
                    <div id="edit-{{ $step->id }}" class="edit-form">
                        <div class="edit-header">
                            <span class="edit-badge">
                                <i class="fas fa-edit"></i>
                                EDITING
                            </span>
                        </div>
                        
                        <form method="POST" action="{{ route('admin.services.steps.update', [$service, $type, $step]) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="edit-title-{{ $step->id }}">Step Title</label>
                                <input name="title" id="edit-title-{{ $step->id }}" value="{{ $step->title }}" placeholder="Enter step title" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit-content-{{ $step->id }}">Step Details</label>
                                <textarea name="content" id="edit-content-{{ $step->id }}" placeholder="Enter step details (bullet points allowed)" rows="3" required>{{ $step->content }}</textarea>
                            </div>
                            
                            <div class="edit-actions">
                                <div class="order-input">
                                    <label for="edit-order-{{ $step->id }}">Order:</label>
                                    <input name="display_order" id="edit-order-{{ $step->id }}" value="{{ $step->display_order }}" 
                                        placeholder="Order" type="number" min="0">
                                </div>
                                
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Save Changes
                                </button>
                                
                                <button type="button" onclick="cancelEdit({{ $step->id }})" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-list-ol"></i>
                    <h3>No Steps Yet</h3>
                    <p>Start by adding your first step using the form above.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Floating Messages Container -->
    <div id="floating-messages"></div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle modal-icon"></i>
            <h3 class="modal-title">Delete Step</h3>
            <p class="modal-message">Are you sure you want to delete "<span id="stepTitle"></span>"? This action cannot be undone.</p>
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

            function editStep(stepId) {
                // Hide all other edit modes first
                document.querySelectorAll('[id^="edit-"]').forEach(function (element) {
                element.classList.remove('active');
                });
                document.querySelectorAll('[id^="display-"]').forEach(function (element) {
                    element.style.display = 'block';
                });

                // Show edit mode for this step
                document.getElementById('display-' + stepId).style.display = 'none';
            document.getElementById('edit-' + stepId).classList.add('active');

                // Focus on the title input
                setTimeout(function () {
                    document.querySelector('#edit-' + stepId + ' input[name="title"]').focus();
                }, 100);
            }

        function cancelEdit(stepId) {
            document.getElementById('edit-' + stepId).classList.remove('active');
            document.getElementById('display-' + stepId).style.display = 'block';
        }

        // Delete modal functions
        function showDeleteModal(stepId, stepTitle) {
            document.getElementById('stepTitle').textContent = stepTitle;
            document.getElementById('deleteForm').action = '{{ route("admin.services.steps.destroy", [$service, $type, ":stepId"]) }}'.replace(':stepId', stepId);
            document.getElementById('deleteModal').classList.add('show');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

            // Add keyboard shortcuts
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    // Cancel any open edit mode
                document.querySelectorAll('.edit-form.active').forEach(function (element) {
                            var stepId = element.id.replace('edit-', '');
                            cancelEdit(stepId);
                    });
                }
            });

        // Show success message if redirected from successful action
        @if(session('status'))
            showFloatingMessage('{{ session('status') }}', 'success');
        @endif

        @if(session('error'))
            showFloatingMessage('{{ session('error') }}', 'error');
        @endif
        </script>
</body>
</html>








