<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Officer</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh; display:flex; align-items:center; justify-content:center;">
    <div class="container-narrow" style="display:flex; align-items:center; justify-content:center; min-height:100vh;">
        <div class="content-wrapper" style="width:100%; max-width:600px;">
            <div class="page-header">
                <div class="action-bar-left">
                    <a href="{{ route('admin.officers.index') }}" class="btn-back">← Back to Officers</a>
                    <h2 class="page-title">CREATE OFFICER ACCOUNT</h2>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Officer Account</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.officers.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="form-label">Full name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required class="form-input">
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="form-input">
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="department" class="form-label">Department</label>
                            <select id="department" name="department" required class="form-select">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department }}" {{ old('department') == $department ? 'selected' : '' }}>
                                        {{ $department }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Temporary Password (optional)</label>
                            <div style="position:relative;">
                                <input id="password" name="password" type="password" placeholder="Auto-generated if empty" class="form-input">
                                <button type="button" onclick="togglePassword('password')"
                                    style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; cursor:pointer; font-size:12px; color:#6c757d; padding:4px 8px; height:28px;">
                                    Show
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Create</button>
                            <a href="{{ route('admin.officers.index') }}" class="btn btn-outline">Cancel</a>
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