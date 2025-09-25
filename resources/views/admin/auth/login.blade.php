<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body class="alaminos-background" style="min-height:100vh; display:flex; align-items:center; justify-content:center;">
    <div class="container-narrow"
        style="display:flex; align-items:center; justify-content:center; min-height:100vh; position:relative; z-index:2;">
        <div class="alaminos-card" style="width:100%; max-width:500px; padding:32px;">
            <div class="page-header">
                <div class="action-bar-left">
                    <h2 class="alaminos-title">LOG IN ADMIN ACCOUNT</h2>
                    <p class="alaminos-subtitle">Welcome to Alaminos City Click2Serve System</p>
                </div>
            </div>

            <div class="card" style="background: transparent; box-shadow: none; border: none; padding: 0;">
                <div class="card-header">
                    <h3 class="card-title">Admin Login</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.login.attempt') }}">
                        @csrf
                        @if (session('status'))
                            <div
                                style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:6px;margin-bottom:12px;">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                class="form-input">
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div style="position:relative;">
                                <input id="password" name="password" type="password" required class="form-input">
                                <button type="button" onclick="togglePassword('password')"
                                    style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; cursor:pointer; font-size:12px; color:#6c757d; padding:4px 8px; height:28px;">
                                    Show
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label style="font-size:14px; display:flex; gap:6px; align-items:center;">
                                <input type="checkbox" name="remember"> Remember me
                            </label>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>

</html>