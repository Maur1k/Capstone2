<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Services Management - Admin Panel</title>
    <link rel="stylesheet" href="/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/services-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Services Management</h1>
                    <p class="header-subtitle">Manage municipal services and monitor system activity</p>
                </div>
                <div class="flex-gap-12">
                    <a href="{{ route('admin.dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('admin.services.create') }}" class="add-service-btn">
                        <i class="fas fa-plus"></i>
                        Add Service
                    </a>
                </div>
            </div>
        </div>



        <!-- Services Grid -->
        <div class="services-grid">
            @forelse($services as $service)
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ $service->icon_path ?: '/assets/icons/Services.png' }}" alt="{{ $service->name }}">
                    </div>
                    <h3 class="service-title">{{ $service->name }}</h3>
                    <p class="service-department">{{ $service->department }}</p>
                    <div class="service-actions">
                        <a href="{{ route('admin.services.types', $service) }}" class="action-btn types">
                            <i class="fas fa-list"></i> Types
                        </a>
                        <a href="{{ route('admin.services.edit', $service) }}" class="action-btn edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button"
                            onclick="showDeleteModal({{ $service->id }}, '{{ addslashes($service->name) }}')"
                            class="action-btn delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state grid-col-full">
                    <i class="fas fa-cogs"></i>
                    <h3>No Services Yet</h3>
                    <p>Get started by adding your first municipal service</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle modal-icon"></i>
            <h3 class="modal-title">Delete Service</h3>
            <p class="modal-message">Are you sure you want to delete "<span id="serviceName"></span>"? This action
                cannot be undone.</p>
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
        // Delete modal functions
        function showDeleteModal(serviceId, serviceName) {
            document.getElementById('serviceName').textContent = serviceName;
            document.getElementById('deleteForm').action = '{{ route("admin.services.destroy", ":id") }}'.replace(':id', serviceId);
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

        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            // Add hover effects to service cards
            const serviceCards = document.querySelectorAll('.service-card');
            serviceCards.forEach(card => {
                card.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-8px)';
                });
                card.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Show success modal if redirected from successful action
            @if(session('status'))
                showSuccessModal('{{ session('status') }}');
            @endif
        });
    </script>
</body>

</html>