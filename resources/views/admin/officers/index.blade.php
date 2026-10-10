<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Officers - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Officers Management</h1>
                    <p class="header-subtitle">Manage officer accounts and permissions</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('admin.officers.create') }}" class="add-btn">
                        <i class="fas fa-plus"></i>
                        Add Officer
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            @if($officers->count() > 0)
                <div class="table-wrapper">
                    <table class="officers-table">
                        <thead>
                            <tr>
                                <th class="w-30p">Officer Details</th>
                                <th class="w-30p">Department</th>
                                <th class="w-15p">Status</th>
                                <th class="w-25p">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($officers as $officer)
                                <tr>
                                    <td>
                                        <div class="officer-info">
                                            <div class="officer-name">{{ $officer->name }}</div>
                                            <div class="officer-email">{{ $officer->email }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="officer-department">{{ $officer->department }}</div>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $officer->is_active ? 'active' : 'inactive' }}">
                                            <i class="fas fa-{{ $officer->is_active ? 'check-circle' : 'times-circle' }}"></i>
                                            {{ $officer->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.officers.edit', $officer) }}" class="action-btn edit">
                                                <i class="fas fa-edit"></i>
                                                Edit
                                            </a>
                                            <button type="button" class="action-btn delete"
                                                onclick="showDeleteModal({{ $officer->id }}, '{{ $officer->name }}')">
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
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h4>No Officers Found</h4>
                    <p>Get started by adding your first officer account.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle modal-icon"></i>
            <h3 class="modal-title">Delete Officer</h3>
            <p class="modal-message">Are you sure you want to delete "<span id="officerName"></span>"? This action
                cannot be undone.</p>
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

    <!-- Floating Messages -->
    <div id="floatingMessage" class="floating-message hidden">
        <i class="fas fa-check-circle"></i>
        <span id="floatingMessageText"></span>
    </div>

    <script>
        // Show floating message
        function showFloatingMessage(message, type = 'success') {
            const messageEl = document.getElementById('floatingMessage');
            const textEl = document.getElementById('floatingMessageText');
            const iconEl = messageEl.querySelector('i');

            textEl.textContent = message;
            messageEl.className = `floating-message ${type}`;

            // Update icon based on type
            if (type === 'success') {
                iconEl.className = 'fas fa-check-circle';
            } else if (type === 'error') {
                iconEl.className = 'fas fa-exclamation-circle';
            } else if (type === 'warning') {
                iconEl.className = 'fas fa-exclamation-triangle';
            }

            messageEl.style.display = 'flex';
            setTimeout(() => {
                messageEl.classList.add('show');
            }, 100);

            setTimeout(() => {
                messageEl.classList.remove('show');
                setTimeout(() => {
                    messageEl.style.display = 'none';
                }, 300);
            }, 3000);
        }

        // Delete modal functions
        function showDeleteModal(officerId, officerName) {
            document.getElementById('officerName').textContent = officerName;
            document.getElementById('deleteForm').action = '{{ route("admin.officers.destroy", ":id") }}'.replace(':id', officerId);
            document.getElementById('deleteModal').classList.add('show');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        // Handle form submissions with AJAX
        document.addEventListener('DOMContentLoaded', function () {
            // Handle delete form submissions
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const url = this.action;

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showFloatingMessage(data.message, 'success');
                            hideDeleteModal();
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showFloatingMessage(data.message || 'An error occurred', 'error');
                            hideDeleteModal();
                        }
                    })
                    .catch(error => {
                        showFloatingMessage('An error occurred', 'error');
                        hideDeleteModal();
                    });
            });

            // Show session messages
            @if(session('status'))
                showFloatingMessage('{{ session('status') }}', 'success');
            @endif

            @if(session('error'))
                showFloatingMessage('{{ session('error') }}', 'error');
            @endif
        });
    </script>
</body>

</html>