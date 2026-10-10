<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin Setup - Click2Serve</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/login.css">
    <meta name="robots" content="noindex">
</head>

<body class="setup-login">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-crown"></i>
                </div>
                <h1 class="login-title">Super Admin Setup</h1>
                <p class="login-subtitle">Initialize your Alaminos City Click2Serve System</p>
            </div>

            <div class="login-form">
                <div class="form-success mb-24">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>First-time Setup</strong><br>
                        Create the first Super Admin account. This page will be automatically disabled after the first
                        admin is created.
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.setup.store') }}" class="login-form">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', config('app.name') . ' Admin') }}"
                        required autofocus class="form-input" placeholder="Enter your full name">
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email', env('ADMIN_EMAIL')) }}" required
                        class="form-input" placeholder="Enter your email address">
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-group">
                        <input id="password" name="password" type="password" required class="form-input"
                            placeholder="Enter a strong password (min. 8 characters)">
                        <button type="button" onclick="togglePassword('password')" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="password-group">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="form-input" placeholder="Confirm your password">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-user-plus"></i>
                    Create Super Admin
                </button>
            </form>

            <div class="login-footer">
                <p>Alaminos City Click2Serve System &copy; {{ date('Y') }}</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;
            const icon = button.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Add loading state to form submission
        document.querySelector('.login-form').addEventListener('submit', function (e) {
            const submitBtn = this.querySelector('.login-btn');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function () {
            const password = this.value;
            const strength = getPasswordStrength(password);
            updatePasswordStrength(strength);
        });

        function getPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }

        function updatePasswordStrength(strength) {
            // This could be enhanced with visual indicators
            console.log('Password strength:', strength);
        }
    </script>
</body>

</html>