<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Click2Serve</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/login.css">
</head>

<body class="admin-login">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="{{ asset('assets/logo/logo_click2serve.png') }}" alt="Click2Serve Logo"
                        class="login-logo-img">
                </div>
                <h1 class="login-title">Admin Login</h1>
                <p class="login-subtitle">Welcome to Alaminos City Click2Serve System</p>
            </div>

            <form method="POST" action="{{ route('admin.login.attempt') }}" class="login-form">
                @csrf
                @if (session('status'))
                    <div class="form-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="form-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        class="form-input" placeholder="Enter your email address">
                    @error('email')
                        <div class="form-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-group">
                        <input id="password" name="password" type="password" required class="form-input"
                            placeholder="Enter your password">
                        <button type="button" onclick="togglePassword('password')" class="password-toggle"
                            aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="form-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="remember-group">
                    <div class="remember-checkbox">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
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
            const passwordGroup = field.closest('.password-group');
            const button = passwordGroup ? passwordGroup.querySelector('.password-toggle') : field.nextElementSibling;

            if (!button) return;

            const icon = button.querySelector('i');
            if (!icon) return;

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
    </script>
</body>

</html>