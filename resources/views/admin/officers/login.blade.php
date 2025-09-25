<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Officer Login - Click2Serve</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body class="alaminos-background" style="min-height:100vh; display:flex; align-items:center; justify-content:center;">
    <div class="container-narrow" style="display:flex; align-items:center; justify-content:center; min-height:100vh; position:relative; z-index:2;">
        <div class="alaminos-card" style="width:100%; max-width:500px; padding:32px;">
            <div class="page-header">
                <h2 class="alaminos-title">OFFICER LOGIN</h2>
                <p class="alaminos-subtitle">Access your Alaminos City department dashboard</p>
            </div>
            
            <div class="card" style="background: transparent; box-shadow: none; border: none; padding: 0;">
                <div class="card-header">
                    <h3 class="card-title">Officer Login</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('officer.login') }}">
                        @csrf
                        
                        @if ($errors->any())
                            <div style="background:#fef2f2; color:#b91c1c; padding:10px; border-radius:6px; margin-bottom:16px;">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="form-input">
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

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" style="width:100%;">Login to Officer Dashboard</button>
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