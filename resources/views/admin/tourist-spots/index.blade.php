<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tourist Spots - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --teal-primary: #008080;
            --teal-light: #00A0A0;
            --teal-dark: #006666;
            --teal-accent: #4DD0E1;
            --teal-bg: #E0F7FA;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--teal-bg) 0%, #f0fdfa 50%, var(--gray-50) 100%);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Background decorative elements */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 20%, rgba(0, 160, 160, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 128, 128, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 60%, rgba(77, 208, 225, 0.06) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px;
            position: relative;
            z-index: 1;
        }

        .header {
            background: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-light) 100%);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .header-title {
            color: var(--white);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin: 8px 0 0 0;
            font-weight: 400;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-align: center;
        }

        .btn-primary {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            color: var(--white);
            text-decoration: none;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            color: var(--white);
            text-decoration: none;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--teal-dark), #004d4d);
            color: var(--white);
            padding: 10px 20px;
            font-size: 0.875rem;
            border: none;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #004d4d, var(--teal-dark));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 102, 102, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
            color: var(--white);
            padding: 10px 20px;
            font-size: 0.875rem;
            border: none;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, var(--teal-light), var(--teal-primary));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.3);
        }

        .content {
            background: var(--white);
            border-radius: 16px;
            padding: 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
        }

        .spots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
        }

        .spot-card {
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            background: var(--white);
            position: relative;
            overflow: hidden;
        }

        .spot-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--teal-primary), var(--teal-light));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .spot-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--teal-primary);
        }

        .spot-card:hover::before {
            transform: scaleX(1);
        }

        .spot-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            object-position: center;
            border-radius: 12px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }

        .spot-card:hover .spot-image {
            box-shadow: 0 8px 20px rgba(0, 128, 128, 0.2);
        }

        .spot-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 8px;
        }

        .spot-description {
            font-size: 0.9rem;
            color: var(--gray-500);
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .spot-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 16px;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .status-inactive {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--gray-500);
        }

        .empty-state h3 {
            font-size: 1.75rem;
            margin-bottom: 12px;
            color: var(--gray-700);
            font-weight: 600;
        }

        .empty-state p {
            margin-bottom: 32px;
            font-size: 1.1rem;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Floating Messages */
        .floating-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .floating-message.show {
            transform: translateX(0);
        }

        .floating-message.success {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
        }

        .floating-message.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .floating-message.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        /* Confirmation Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: var(--white);
            border-radius: 16px;
            padding: 32px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--gray-200);
        }

        .modal-overlay.show .modal-content {
            transform: scale(1);
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            color: #ef4444;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 8px;
        }

        .modal-message {
            color: var(--gray-500);
            margin-bottom: 24px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-btn-cancel {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .modal-btn-cancel:hover {
            background: var(--gray-300);
        }

        .modal-btn-confirm {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .modal-btn-confirm:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 24px 16px;
            }

            .header {
                padding: 24px;
            }

            .header-title {
                font-size: 2rem;
            }

            .header-content {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .spots-grid {
                grid-template-columns: 1fr;
            }

            .spot-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Tourist Spots</h1>
                    <p class="header-subtitle">Manage tourist spots and attractions</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.dashboard.edit') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Edit Dashboard
                    </a>
                    <a href="{{ route('admin.tourist-spots.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Tourist Spot
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
            @if($spots->count() > 0)
                <div class="spots-grid">
                    @foreach($spots as $spot)
                        <div class="spot-card">
                            <span class="status-badge {{ $spot->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $spot->is_active ? 'Active' : 'Inactive' }}
                            </span>

                            <img src="{{ $spot->image_url }}" alt="{{ $spot->name }}" class="spot-image">

                            <h3 class="spot-name">{{ $spot->name }}</h3>
                            <p class="spot-description">{{ $spot->description }}</p>

                            <div class="spot-actions">
                                <a href="{{ route('admin.tourist-spots.edit', $spot->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <button type="button" class="btn btn-danger"
                                    onclick="showDeleteModal({{ $spot->id }}, '{{ addslashes($spot->name) }}')">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-map-marker-alt empty-icon"></i>
                    <h3>No Tourist Spots</h3>
                    <p>Get started by adding the first tourist spot.</p>
                    <a href="{{ route('admin.tourist-spots.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add First Tourist Spot
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
            <h3 class="modal-title">Delete Tourist Spot</h3>
            <p class="modal-message">Are you sure you want to delete "<span id="spotName"></span>"? This action cannot
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

    <script>
        // Show floating message
        function showFloatingMessage(message, type = 'success') {
            const container = document.getElementById('floating-messages');
            const messageEl = document.createElement('div');
            messageEl.className = `floating-message ${type}`;

            const icon = type === 'success' ? '✓' : type === 'error' ? '✕' : '⚠';
            messageEl.innerHTML = `<span class="message-icon">${icon}</span>${message}`;

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
        function showDeleteModal(spotId, spotName) {
            document.getElementById('spotName').textContent = spotName;
            document.getElementById('deleteForm').action = `/admin/tourist-spots/${spotId}`;
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
                        showFloatingMessage('Tourist spot deleted successfully!', 'success');
                        // Reload page after a short delay
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showFloatingMessage('Error deleting tourist spot. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    hideDeleteModal();
                    showFloatingMessage('Error deleting tourist spot. Please try again.', 'error');
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