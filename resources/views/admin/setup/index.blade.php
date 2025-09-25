<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>First-time Setup</title>
    <link rel="stylesheet" href="/css/admin.css">
    <meta name="robots" content="noindex">
</head>

<body class="alaminos-background" style="min-height:100vh; display:flex; align-items:center; justify-content:center;">
    <div class="container-narrow"
        style="display:flex; align-items:center; justify-content:center; min-height:100vh; position:relative; z-index:2;">
        <div class="alaminos-card" style="width:100%; max-width:500px; padding:32px;">
            <div class="page-header">
                <div class="action-bar-left">

                    <h2 class="alaminos-title">INITIALIZE SUPER ADMIN</h2>
                    <p class="alaminos-subtitle">Set up your Alaminos City Click2Serve System</p>
                </div>
            </div>

            <div class="card" style="background: transparent; box-shadow: none; border: none; padding: 0;">
                <div class="card-header">
                    <h3 class="card-title">Create Super Admin</h3>
                    <p class="page-subtitle">Create the first Super Admin account. This page is automatically disabled
                        after the first admin is created.</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.setup.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="form-label">Full name</label>
                            <input id="name" name="name" type="text"
                                value="{{ old('name', config('app.name') . ' Admin') }}" required class="form-input">
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', env('ADMIN_EMAIL')) }}"
                                required class="form-input">
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
                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <div style="position:relative;">
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="form-input">
                                <button type="button" onclick="togglePassword('password_confirmation')"
                                    style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; cursor:pointer; font-size:12px; color:#6c757d; padding:4px 8px; height:28px;">
                                    Show
                                </button>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Create Admin</button>
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