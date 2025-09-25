<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Officer</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body style="background:#f3f4f6; min-height:100vh; display:flex; align-items:center; justify-content:center;">
    <div class="container-narrow" style="display:flex; align-items:center; justify-content:center; min-height:100vh;">
        <div class="content-wrapper" style="width:100%; max-width:600px;">
            <div class="page-header">
                <div class="action-bar-left">
                    <a href="{{ route('admin.officers.index') }}" class="btn-back">← Back to Officers</a>
                    <h2 class="page-title">EDIT OFFICER ACCOUNT</h2>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Officer Account</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.officers.update', $officer) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name" class="form-label">Full name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $officer->name) }}" required
                                class="form-input">
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $officer->email) }}"
                                required class="form-input">
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="department" class="form-label">Department</label>
                            <select id="department" name="department" required class="form-select">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department }}" {{ old('department', $officer->department) == $department ? 'selected' : '' }}>
                                        {{ $department }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('admin.officers.index') }}" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>