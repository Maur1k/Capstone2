<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POI Manager - Admin Panel</title>
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

        .card h3 {
            color: var(--gray-700);
            font-size: 1.25rem;
            font-weight: 600;
            margin: 24px 0 16px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--teal-accent);
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
        select,
        textarea {
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
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--teal-primary);
            box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.1);
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

        .waypoint-item {
            background: var(--gray-50);
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }

        .waypoint-item:hover {
            border-color: var(--teal-accent);
            box-shadow: var(--shadow-sm);
        }

        .waypoint-item h4 {
            color: var(--gray-800);
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }

        #add-waypoint {
            margin-top: 16px;
        }

        .search-filter-container {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        #poi_search {
            min-width: 250px;
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

            .search-filter-container {
                flex-direction: column;
                align-items: stretch;
            }

            #poi_search {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title"><i class="fas fa-map-marker-alt"></i> POI Manager</h1>
                    <p class="header-subtitle">Manage Points of Interest for wayfinding system</p>
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
            $isEditing = !is_null($editPoi);
            $meta = $isEditing ? ($editPoi->meta ?? []) : [];
            $modelPosition = $meta['modelPosition'] ?? [];
            $waypoints = $meta['waypoints'] ?? [];
            $waypoint1 = $waypoints[0] ?? [];
            $waypoint2 = $waypoints[1] ?? [];
            $extraMeta = $meta;
            unset($extraMeta['modelPosition'], $extraMeta['waypoints'], $extraMeta['description']);
            $additionalMetaValue = old('additional_meta');
            if ($additionalMetaValue === null && !empty($extraMeta)) {
                $additionalMetaValue = json_encode($extraMeta, JSON_PRETTY_PRINT);
            }
        @endphp

        <div class="card">
            <div class="actions" style="justify-content: space-between; margin-bottom: 24px;">
                <h2><i class="fas fa-{{ $isEditing ? 'edit' : 'plus-circle' }}"></i> {{ $isEditing ? 'Edit POI' : 'Create New POI' }}</h2>
                @if ($isEditing)
                    <a href="{{ route('admin.pois.index') }}" class="link-button">
                        <i class="fas fa-times"></i> Exit edit mode
                    </a>
                @endif
            </div>
            <form method="POST" action="{{ $isEditing ? route('admin.pois.update', $editPoi) : route('admin.pois.store') }}">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <label for="floor_id"><i class="fas fa-building"></i> Floor</label>
                <select id="floor_id" name="floor_id" required>
                    <option value="">Select floor</option>
                    @foreach ($floors as $floor)
                        <option value="{{ $floor->id }}"
                            {{ (string) old('floor_id', $isEditing ? $editPoi->floor_id : '') === (string) $floor->id ? 'selected' : '' }}>
                            {{ $floor->building }} - {{ $floor->name }}
                        </option>
                    @endforeach
                </select>

                <div class="grid">
                    <div>
                        <label for="name"><i class="fas fa-tag"></i> Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $isEditing ? $editPoi->name : '') }}" required>
                    </div>
                    <div>
                        <label for="category"><i class="fas fa-folder"></i> Category</label>
                        <input id="category" name="category" type="text" value="{{ old('category', $isEditing ? $editPoi->category : '') }}">
                    </div>
                    <div>
                        <label for="x"><i class="fas fa-arrows-alt-h"></i> X Axis of the Department Office</label>
                        <input id="x" name="x" type="number" step="0.01" value="{{ old('x', $isEditing ? $editPoi->x : '') }}" required>
                    </div>
                    <div>
                        <label for="y"><i class="fas fa-arrows-alt-v"></i> Y Axis of the Department Office</label>
                        <input id="y" name="y" type="number" step="0.01" value="{{ old('y', $isEditing ? $editPoi->y : '') }}" required>
                    </div>
                </div>

                <h3><i class="fas fa-cube"></i> Model Position</h3>
                <div class="grid">
                    <div>
                        <label for="model_position_x">X axis of the Guide Tube</label>
                        <input id="model_position_x" name="model_position_x" type="number" step="0.01" value="{{ old('model_position_x', $modelPosition['x'] ?? '') }}">
                    </div>
                    <div>
                        <label for="model_position_y">Y axis of ending marker of the guide tube</label>
                        <input id="model_position_y" name="model_position_y" type="number" step="0.01" value="{{ old('model_position_y', $modelPosition['y'] ?? '') }}">
                    </div>
                    <div>
                        <label for="model_position_z">Z axis of the guide tube</label>
                        <input id="model_position_z" name="model_position_z" type="number" step="0.01" value="{{ old('model_position_z', $modelPosition['z'] ?? '') }}">
                    </div>
                </div>

                <h3><i class="fas fa-route"></i> Extended Waypoints of the Guide Tube</h3>
                <div id="waypoints-container">
                    @php
                        $waypointsData = old('waypoints', $waypoints ?? []);
                        if (!is_array($waypointsData)) {
                            $waypointsData = [];
                        }
                    @endphp
                    @if (!empty($waypointsData))
                        @foreach ($waypointsData as $index => $waypoint)
                            <div class="waypoint-item" data-index="{{ $index }}">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <h4 style="margin: 0;"><i class="fas fa-map-pin"></i> Waypoint {{ $index + 1 }}</h4>
                                    <button type="button" class="remove-waypoint" style="background: #dc2626; color: white; border: none; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-weight: 600; font-size: 0.9rem;">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                                <div class="grid">
                                    <div>
                                        <label>X</label>
                                        <input name="waypoints[{{ $index }}][x]" type="number" step="0.01" value="{{ old("waypoints.{$index}.x", $waypoint['x'] ?? '') }}">
                                    </div>
                                    <div>
                                        <label>Y</label>
                                        <input name="waypoints[{{ $index }}][y]" type="number" step="0.01" value="{{ old("waypoints.{$index}.y", $waypoint['y'] ?? '') }}">
                                    </div>
                                    <div>
                                        <label>Z</label>
                                        <input name="waypoints[{{ $index }}][z]" type="number" step="0.01" value="{{ old("waypoints.{$index}.z", $waypoint['z'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" id="add-waypoint" style="background: #10b981; color: white; border: none; border-radius: 8px; padding: 12px 24px; cursor: pointer; font-weight: 600; margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Add Waypoint
                </button>

                <div class="actions" style="margin-top: 2rem">
                    <button type="submit" class="primary">
                        <i class="fas fa-{{ $isEditing ? 'save' : 'plus' }}"></i> {{ $isEditing ? 'Update POI' : 'Create POI' }}
                    </button>
                    @if ($isEditing)
                        <a href="{{ route('admin.pois.index') }}" class="link-button">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card">
            <h2><i class="fas fa-list"></i> Existing POIs</h2>
            <div class="search-filter-container">
                <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                    <label for="poi_search" style="font-weight:normal; margin:0; white-space: nowrap;"><i class="fas fa-search"></i> Search:</label>
                    <input type="text" id="poi_search" placeholder="Search POIs..." style="margin: 0;">
                </div>
                <form method="GET" action="{{ route('admin.pois.index') }}" class="inline-form" style="display: flex; align-items: center; gap: 0.5rem;">
                    <label for="filter_floor_id" style="font-weight:normal; margin:0; white-space: nowrap;"><i class="fas fa-filter"></i> Filter by floor:</label>
                    <select id="filter_floor_id" name="floor_id" onchange="this.form.submit()" style="margin: 0; min-width: 200px;">
                        <option value="">All floors</option>
                        @foreach ($floors as $floor)
                            <option value="{{ $floor->id }}"
                                {{ (string)($selectedFloorId ?? request('floor_id')) === (string)$floor->id ? 'selected' : '' }}>
                                {{ $floor->building }} - {{ $floor->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-tag"></i> Name</th>
                        <th><i class="fas fa-folder"></i> Category</th>
                        <th><i class="fas fa-building"></i> Floor</th>
                        <th><i class="fas fa-arrows-alt-h"></i> X</th>
                        <th><i class="fas fa-arrows-alt-v"></i> Y</th>
                        <th><i class="fas fa-cube"></i> Model Position</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pois as $poi)
                        @php
                            $poiMeta = $poi->meta ?? [];
                            $mp = $poiMeta['modelPosition'] ?? null;
                        @endphp
                        <tr class="poi-row" data-search-text="{{ strtolower($poi->name . ' ' . ($poi->category ?? '') . ' ' . optional($poi->floor)->building . ' ' . optional($poi->floor)->name . ' ' . $poi->x . ' ' . $poi->y . ($mp ? ' ' . ($mp['x'] ?? '') . ' ' . ($mp['y'] ?? '') . ' ' . ($mp['z'] ?? '') : '')) }}">
                            <td><strong>{{ $poi->name }}</strong></td>
                            <td>{{ $poi->category }}</td>
                            <td>{{ optional($poi->floor)->building }} - {{ optional($poi->floor)->name }}</td>
                            <td>{{ $poi->x }}</td>
                            <td>{{ $poi->y }}</td>
                            <td>
                                @if ($mp)
                                    <code style="background: var(--gray-100); padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                                        x: {{ $mp['x'] ?? '-' }}, y: {{ $mp['y'] ?? '-' }}, z: {{ $mp['z'] ?? '-' }}
                                    </code>
                                @else
                                    <span style="color: var(--gray-400);">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions" style="gap: 0.5rem;">
                                    <a href="{{ route('admin.pois.index', ['edit' => $poi->id]) }}" class="link-button" style="padding: 8px 16px; font-size: 0.9rem;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form class="inline-form" method="POST" action="{{ route('admin.pois.destroy', $poi) }}" onsubmit="return confirm('Delete this POI?');">
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
                        <tr class="poi-row">
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--gray-500);">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                                No POIs created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const waypointsContainer = document.getElementById('waypoints-container');
            const addWaypointBtn = document.getElementById('add-waypoint');

            function getNextWaypointIndex() {
                const items = waypointsContainer.querySelectorAll('.waypoint-item');
                let maxIndex = -1;
                items.forEach(item => {
                    const index = parseInt(item.getAttribute('data-index') || '0');
                    if (index > maxIndex) {
                        maxIndex = index;
                    }
                });
                return maxIndex + 1;
            }

            addWaypointBtn.addEventListener('click', function() {
                const waypointIndex = getNextWaypointIndex();
                const waypointItem = document.createElement('div');
                waypointItem.className = 'waypoint-item';
                waypointItem.setAttribute('data-index', waypointIndex);
                
                waypointItem.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <h4 style="margin: 0;"><i class="fas fa-map-pin"></i> Waypoint ${waypointIndex + 1}</h4>
                        <button type="button" class="remove-waypoint" style="background: #dc2626; color: white; border: none; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                    <div class="grid">
                        <div>
                            <label>X</label>
                            <input name="waypoints[${waypointIndex}][x]" type="number" step="0.01" value="">
                        </div>
                        <div>
                            <label>Y</label>
                            <input name="waypoints[${waypointIndex}][y]" type="number" step="0.01" value="">
                        </div>
                        <div>
                            <label>Z</label>
                            <input name="waypoints[${waypointIndex}][z]" type="number" step="0.01" value="">
                        </div>
                    </div>
                `;
                
                waypointsContainer.appendChild(waypointItem);
                
                waypointItem.querySelector('.remove-waypoint').addEventListener('click', function() {
                    waypointItem.remove();
                    reindexWaypoints();
                });
            });

            waypointsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-waypoint') || e.target.closest('.remove-waypoint')) {
                    const btn = e.target.classList.contains('remove-waypoint') ? e.target : e.target.closest('.remove-waypoint');
                    btn.closest('.waypoint-item').remove();
                    reindexWaypoints();
                }
            });

            function reindexWaypoints() {
                const items = waypointsContainer.querySelectorAll('.waypoint-item');
                items.forEach((item, index) => {
                    item.setAttribute('data-index', index);
                    const h4 = item.querySelector('h4');
                    if (h4) {
                        h4.innerHTML = `<i class="fas fa-map-pin"></i> Waypoint ${index + 1}`;
                    }
                    const inputs = item.querySelectorAll('input');
                    inputs.forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            const match = name.match(/waypoints\[\d+\]\[([xyz])\]/);
                            if (match) {
                                input.setAttribute('name', `waypoints[${index}][${match[1]}]`);
                            }
                        }
                    });
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('poi_search');
            const poiRows = document.querySelectorAll('.poi-row');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;
                    
                    poiRows.forEach(row => {
                        const searchText = row.getAttribute('data-search-text') || '';
                        if (searchTerm === '' || searchText.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    const tbody = document.querySelector('table tbody');
                    let noResultsRow = tbody.querySelector('.no-results-row');
                    
                    if (visibleCount === 0 && searchTerm !== '') {
                        if (!noResultsRow) {
                            noResultsRow = document.createElement('tr');
                            noResultsRow.className = 'no-results-row';
                            noResultsRow.innerHTML = '<td colspan="7" style="text-align: center; padding: 3rem; color: var(--gray-500);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>No POIs found matching your search.</td>';
                            tbody.appendChild(noResultsRow);
                        }
                        noResultsRow.style.display = '';
                    } else if (noResultsRow) {
                        noResultsRow.style.display = 'none';
                    }
                });
                
                const floorFilter = document.getElementById('filter_floor_id');
                if (floorFilter) {
                    floorFilter.addEventListener('change', function() {
                        searchInput.value = '';
                        searchInput.dispatchEvent(new Event('input'));
                    });
                }
            }
        });
    </script>
</body>
</html>
