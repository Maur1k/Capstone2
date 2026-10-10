<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>About Content Management - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/about-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">About Content Management</h1>
                    <p class="header-subtitle">Manage about page content for the website</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('admin.about.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add About Content
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

        <div class="content">
            @if($abouts->count() > 0)
                <div class="about-grid">
                    @foreach($abouts as $about)
                        <div class="about-card">
                            <span class="status-badge {{ $about->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $about->is_active ? 'Active' : 'Inactive' }}
                            </span>

                            @if($about->image)
                                <img src="{{ asset($about->image) }}" alt="{{ $about->title }}" class="about-image">
                            @endif

                            <h3 class="about-title">{{ $about->title }}</h3>
                            <p class="about-text">{{ Str::limit(strip_tags($about->content), 150) }}</p>

                            <div class="about-actions">
                                <a href="{{ route('admin.about.edit', $about->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <form action="{{ route('admin.about.destroy', $about->id) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this about content?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-info-circle empty-icon"></i>
                    <h3>No About Content</h3>
                    <p>Get started by adding the first about content.</p>
                    <a href="{{ route('admin.about.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add First About Content
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>

</html>