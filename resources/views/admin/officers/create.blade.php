<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Officer - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Add Officer</h1>
                    <p class="header-subtitle">Create a new officer account for the system</p>
                </div>
                <a href="{{ route('admin.officers.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Officers
                </a>
                </div>
            </div>
            
        <div class="form-container">
            <form action="{{ route('admin.officers.store') }}" method="POST">
                        @csrf
                
                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label for="name">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Enter officer's full name"
                                   required>
                            @error('name')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="Enter officer's email address"
                                   required>
                            @error('email')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="department">Department <span class="text-red-500">*</span></label>
                            <select id="department" name="department" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department }}" {{ old('department') == $department ? 'selected' : '' }}>
                                        {{ $department }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Temporary Password</label>
                            <div class="password-container">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Auto-generated if empty">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    Show
                                </button>
                            </div>
                            <small>Leave empty to auto-generate a secure password</small>
                            @error('password')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <div class="info-section">
                            <h4>
                                <i class="fas fa-info-circle"></i>
                                Officer Account Information
                            </h4>
                            <p>
                                Officers will receive login credentials via email. They can access the system using their email and the provided password. Officers can manage requests assigned to their department.
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active">Active (can login immediately)</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.officers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Create Officer
                    </button>
            </div>
            </form>
        </div>
    </div>

    <!-- Floating Messages Container -->
    <div id="floating-messages"></div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;

            if (field.type === 'password') {
                field.type = 'text';
                button.textContent = 'Hide';
            } else {
                field.type = 'password';
                button.textContent = 'Show';
            }
        }

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

        // Show success message if redirected from successful creation
        @if(session('status'))
            showFloatingMessage('{{ session('status') }}', 'success');
        @endif

        @if(session('error'))
            showFloatingMessage('{{ session('error') }}', 'error');
        @endif
    </script>
</body>

</html>