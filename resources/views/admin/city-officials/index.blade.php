<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>City Officials - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/city-officials-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">City Officials</h1>
                    <p class="header-subtitle">Manage city officials and their information</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.dashboard.edit') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Edit Dashboard
                    </a>
                    <a href="{{ route('admin.city-officials.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Official
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="content">
            @if($officials->count() > 0)
                @foreach($officialsByDepartment as $department => $departmentOfficials)
                    <div class="department-section">
                        <h2 class="department-title">
                            <i class="fas fa-building"></i>
                            {{ $department && $department !== '' ? $department : 'General' }}
                        </h2>
                        <div class="officials-table-container">
                            <table class="officials-table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Order</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departmentOfficials as $official)
                                        <tr onmouseover="this.style.background='var(--gray-50)'"
                                            onmouseout="this.style.background='white'">
                                            <td>
                                                <img src="{{ $official->image_url }}" alt="{{ $official->name }}">
                                            </td>
                                            <td class="font-bold text-gray-800">{{ $official->name }}</td>
                                            <td class="text-gray-600">{{ $official->position }}</td>
                                            <td class="text-gray-600">{{ $official->department ?: 'N/A' }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="status-badge {{ $official->is_active ? 'status-active' : 'status-inactive' }}">
                                                    {{ $official->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center text-gray-600">{{ $official->display_order }}</td>
                                            <td class="text-center">
                                                <div class="official-actions">
                                                    <a href="{{ route('admin.city-officials.edit', $official->id) }}"
                                                        class="btn btn-warning py-6 px-12 text-sm">
                                                        <i class="fas fa-edit"></i>
                                                        Edit
                                                    </a>
                                                    <button type="button" class="btn btn-danger py-6 px-12 text-sm"
                                                        onclick="showDeleteModal({{ $official->id }}, '{{ addslashes($official->name) }}')">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="fas fa-users text-4xl text-gray-400 mb-24"></i>
                    <h3>No City Officials</h3>
                    <p>Get started by adding the first city official.</p>
                    <a href="{{ route('admin.city-officials.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add First Official
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Floating Messages Container -->
    <div id="floating-messages"></div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle modal-icon"></i>
            <h3 class="modal-title">Delete City Official</h3>
            <p class="modal-message">Are you sure you want to delete "<span id="officialName"></span>"? This action
                cannot
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
        function showDeleteModal(officialId, officialName) {
            document.getElementById('officialName').textContent = officialName;
            document.getElementById('deleteForm').action = `/admin/city-officials/${officialId}`;
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
                        showFloatingMessage('City official deleted successfully!', 'success');
                        // Reload page after a short delay
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showFloatingMessage('Error deleting city official. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    hideDeleteModal();
                    showFloatingMessage('Error deleting city official. Please try again.', 'error');
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
    </script>
</body>

</html>