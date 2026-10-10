<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blog Management - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/blog-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Blog Management</h1>
                    <p class="header-subtitle">Manage blog posts for the website</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Blog Post
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
            @if($blogs->count() > 0)
                <div class="blog-grid">
                    @foreach($blogs as $blog)
                        <div class="blog-card">
                            <span class="status-badge {{ $blog->is_published ? 'status-published' : 'status-draft' }}">
                                {{ $blog->is_published ? 'Published' : 'Draft' }}
                            </span>

                            @if($blog->image)
                                <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}" class="blog-image">
                            @endif

                            <h3 class="blog-title">{{ $blog->title }}</h3>
                            <p class="blog-text">{{ Str::limit(strip_tags($blog->content), 150) }}</p>

                            <div class="blog-meta">
                                @if($blog->author)
                                    <span><i class="fas fa-user"></i> {{ $blog->author }}</span>
                                @endif
                                @if($blog->published_at)
                                    <span><i class="fas fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($blog->published_at)->format('M d, Y') }}</span>
                                @endif
                            </div>

                            <div class="blog-actions">
                                <a href="{{ route('admin.blog.edit', $blog->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <form action="{{ route('admin.blog.destroy', $blog->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this blog post?');">
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
                    <i class="fas fa-blog text-gray-400 mb-24 text-4rem"></i>
                    <h3>No Blog Posts</h3>
                    <p>Get started by adding the first blog post.</p>
                    <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add First Blog Post
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>

</html>