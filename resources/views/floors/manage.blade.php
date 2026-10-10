<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Floor Manager - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --teal-primary: #008080;
            --teal-light: #00A0A0;
            --teal-dark: #006666;
            --teal-accent: #4DD0E1;
            --teal-bg: #E0F7FA;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--teal-bg) 0%, #f0fdfa 50%, var(--gray-50) 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 20%, rgba(0, 160, 160, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 128, 128, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 60%, rgba(77, 208, 225, 0.06) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 24px 32px;
            position: relative;
            z-index: 1;
        }

        .header {
            background: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-light) 100%);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .header-title {
            color: var(--white);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin: 8px 0 0 0;
            font-weight: 400;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: var(--white);
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            color: var(--white);
            text-decoration: none;
        }

        .card {
            background: var(--white);
            border-radius: 16px;
            padding: 32px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 32px;
            border: 1px solid var(--gray-200);
        }

        .card h2 {
            color: var(--gray-800);
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0 0 24px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: var(--gray-700);
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            margin-bottom: 1rem;
            box-sizing: border-box;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus,
        select:focus {
            outline: none;
            border-color: var(--teal-primary);
            box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.1);
        }

        input[type="radio"] {
            margin-right: 8px;
            cursor: pointer;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .actions {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        button, .link-button {
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        button.primary {
            background: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-light) 100%);
            color: white;
            box-shadow: var(--shadow-md);
        }

        button.primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        button.danger {
            background: #dc2626;
            color: white;
        }

        button.danger:hover {
            background: #b91c1c;
            transform: translateY(-2px);
        }

        .link-button {
            background: var(--gray-100);
            text-decoration: none;
            color: var(--gray-700);
            display: inline-block;
        }

        .link-button:hover {
            background: var(--gray-200);
            color: var(--gray-900);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th {
            background: var(--gray-50);
            font-weight: 600;
            color: var(--gray-700);
            padding: 16px;
            text-align: left;
            border-bottom: 2px solid var(--gray-200);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700);
            font-size: 0.95rem;
        }

        tr:hover {
            background: var(--gray-50);
        }

        .status {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status.success {
            background: #ecfdf5;
            color: #047857;
            border: 2px solid #10b981;
        }

        .status.error {
            background: #fef2f2;
            color: #b91c1c;
            border: 2px solid #dc2626;
        }

        .inline-form {
            display: inline;
        }

        code {
            background: var(--gray-100);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            color: var(--gray-700);
            font-family: 'Courier New', monospace;
        }

        .radio-group {
            margin-top: 8px;
        }

        .radio-group label {
            font-weight: normal;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .container {
                padding: 24px 16px;
            }

            .header {
                padding: 24px;
            }

            .header-title {
                font-size: 2rem;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .card {
                padding: 24px;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title"><i class="fas fa-layer-group"></i> Floor Manager</h1>
                    <p class="header-subtitle">Manage building floors and 3D models for wayfinding system</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="status success">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="status error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Validation issues:</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php
            $isEditing = !is_null($editFloor);
            $defaultRouting = 'you_are_here';
            if ($isEditing && $editFloor) {
                if ($editFloor->use_stairs_as_start || !$editFloor->show_you_are_here) {
                    $defaultRouting = 'stairs';
                }
            }
        @endphp

        <div class="card">
            <div class="actions" style="justify-content: space-between; margin-bottom: 24px;">
                <h2><i class="fas fa-{{ $isEditing ? 'edit' : 'plus-circle' }}"></i> {{ $isEditing ? 'Edit Floor' : 'Create New Floor' }}</h2>
                @if ($isEditing)
                    <a href="{{ route('admin.floors.index') }}" class="link-button">
                        <i class="fas fa-times"></i> Exit edit mode
                    </a>
                @endif
            </div>
            <form method="POST" action="{{ $isEditing ? route('admin.floors.update', $editFloor) : route('admin.floors.store') }}" enctype="multipart/form-data">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <div>
                    <label for="name"><i class="fas fa-layer-group"></i> Floor Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $isEditing ? $editFloor->name : '') }}" required>
                </div>

                <div class="grid">
                    <div>
                        <label for="width"><i class="fas fa-arrows-alt-h"></i> Width (px)</label>
                        <input id="width" name="width" type="number" step="1" min="1" value="{{ old('width', $isEditing ? $editFloor->width : '') }}">
                    </div>
                    <div>
                        <label for="height"><i class="fas fa-arrows-alt-v"></i> Height (px)</label>
                        <input id="height" name="height" type="number" step="1" min="1" value="{{ old('height', $isEditing ? $editFloor->height : '') }}" required>
                    </div>
                </div>

                <div class="grid">
                    <div>
                        <label for="glb_file"><i class="fas fa-cube"></i> GLB 3D Model</label>
                        <input id="glb_file" name="glb_file" type="file" accept=".glb,.gltf,.zip">
                        @if ($isEditing && $editFloor->glb_path)
                            <div style="margin-top:.5rem; font-size:0.9rem; color: var(--gray-600);">
                                Current: <code>{{ $editFloor->glb_path }}</code>
                            </div>
                        @else
                            <div style="margin-top:.5rem; font-size:0.9rem; color: var(--gray-500);">
                                If not set, kiosk will use the default <code>/kiosk/assets/map.glb</code>.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid">
                    <div>
                        <label><i class="fas fa-stairs"></i> Stairs Position</label>
                        <div class="grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;">
                            <div>
                                <label for="stairs_model_x" style="font-size: 0.85rem;">Stairs X</label>
                                <input id="stairs_model_x" name="stairs_model_x" type="number" step="0.01"
                                    value="{{ old('stairs_model_x', $isEditing ? $editFloor->stairs_model_x : '') }}">
                            </div>
                            <div>
                                <label for="stairs_model_y" style="font-size: 0.85rem;">Stairs Y</label>
                                <input id="stairs_model_y" name="stairs_model_y" type="number" step="0.01"
                                    value="{{ old('stairs_model_y', $isEditing ? $editFloor->stairs_model_y : '') }}">
                            </div>
                            <div>
                                <label for="stairs_model_z" style="font-size: 0.85rem;">Stairs Z</label>
                                <input id="stairs_model_z" name="stairs_model_z" type="number" step="0.01"
                                    value="{{ old('stairs_model_z', $isEditing ? $editFloor->stairs_model_z : '') }}">
                            </div>
                        </div>
                        <div style="margin-top:.5rem; font-size:0.85rem; color: var(--gray-500);">
                            Used as the start/end position when navigating between floors.
                        </div>
                    </div>

                    <div>
                        <label><i class="fas fa-map-marker-alt"></i> "You Are Here" Position</label>
                        <div class="grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;">
                            <div>
                                <label for="you_are_here_model_x" style="font-size: 0.85rem;">You Are Here X</label>
                                <input id="you_are_here_model_x" name="you_are_here_model_x" type="number" step="0.01"
                                    value="{{ old('you_are_here_model_x', $isEditing ? $editFloor->you_are_here_model_x : '') }}">
                            </div>
                            <div>
                                <label for="you_are_here_model_y" style="font-size: 0.85rem;">You Are Here Y</label>
                                <input id="you_are_here_model_y" name="you_are_here_model_y" type="number" step="0.01"
                                    value="{{ old('you_are_here_model_y', $isEditing ? $editFloor->you_are_here_model_y : '') }}">
                            </div>
                            <div>
                                <label for="you_are_here_model_z" style="font-size: 0.85rem;">You Are Here Z</label>
                                <input id="you_are_here_model_z" name="you_are_here_model_z" type="number" step="0.01"
                                    value="{{ old('you_are_here_model_z', $isEditing ? $editFloor->you_are_here_model_z : '') }}">
                            </div>
                        </div>
                        <div style="margin-top:.5rem; font-size:0.85rem; color: var(--gray-500);">
                            Used when routing mode is "Start routes from \"You Are Here\"" for this floor.
                        </div>
                    </div>

                    <div>
                        <label><i class="fas fa-route"></i> Routing Options</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="routing_mode" value="you_are_here"
                                    {{ old('routing_mode', $defaultRouting) === 'you_are_here' ? 'checked' : '' }}>
                                Start routes from "You Are Here"
                            </label>
                            <label>
                                <input type="radio" name="routing_mode" value="stairs"
                                    {{ old('routing_mode', $defaultRouting) === 'stairs' ? 'checked' : '' }}>
                                Start routes from stairs (hide "You Are Here")
                            </label>
                        </div>
                    </div>
                </div>

                <div class="actions" style="margin-top: 2rem">
                    <button type="submit" class="primary">
                        <i class="fas fa-{{ $isEditing ? 'save' : 'plus' }}"></i> {{ $isEditing ? 'Update Floor' : 'Create Floor' }}
                    </button>
                    @if ($isEditing)
                        <a href="{{ route('admin.floors.index') }}" class="link-button">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card">
            <h2><i class="fas fa-list"></i> Existing Floors</h2>
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-building"></i> Building</th>
                        <th><i class="fas fa-layer-group"></i> Name</th>
                        <th><i class="fas fa-cube"></i> GLB Path</th>
                        <th><i class="fas fa-arrows-alt-h"></i> Width</th>
                        <th><i class="fas fa-arrows-alt-v"></i> Height</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($floors as $floor)
                        <tr>
                            <td><strong>{{ $floor->building }}</strong></td>
                            <td>{{ $floor->name }}</td>
                            <td>
                                @if ($floor->glb_path)
                                    <code>{{ $floor->glb_path }}</code>
                                @else
                                    <span style="color: var(--gray-400);">(default GLB)</span>
                                @endif
                            </td>
                            <td>{{ $floor->width ?? '—' }}</td>
                            <td>{{ $floor->height ?? '—' }}</td>
                            <td>
                                <div class="actions" style="gap: 0.5rem;">
                                    <a href="{{ route('admin.floors.index', ['edit' => $floor->id]) }}" class="link-button" style="padding: 8px 16px; font-size: 0.9rem;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form class="inline-form" method="POST" action="{{ route('admin.floors.destroy', $floor) }}" onsubmit="return confirm('Delete this floor? This will also orphan its POIs.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="danger" style="padding: 8px 16px; font-size: 0.9rem;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--gray-500);">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                                No floors created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
