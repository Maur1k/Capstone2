<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Queue Number Settings - Officer Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/officer-dashboard.css') }}">
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
        }

        .page-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 32px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: var(--teal-primary);
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-900);
            margin: 0 0 8px 0;
        }

        .page-header p {
            font-size: 0.875rem;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-600);
            margin: 0;
        }

        .status-banner {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: 'Montserrat', sans-serif;
        }

        .form-card {
            background: var(--white);
            border-radius: 12px;
            padding: 28px 32px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            font-size: 0.875rem;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-900);
            transition: all 0.3s ease;
        }

        .form-group input[type="text"]:focus {
            outline: none;
            border-color: var(--teal-primary);
            box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.1);
        }

        .form-group small {
            display: block;
            margin-top: 6px;
            font-size: 0.75rem;
            color: var(--gray-500);
            font-family: 'Montserrat', sans-serif;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-900);
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--gray-200);
        }

        .windows-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .edit-icon {
            color: var(--teal-primary);
            font-size: 1.1rem;
            margin-left: 8px;
            cursor: pointer;
        }

        .window-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 12px;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .window-item-content {
            flex: 1;
        }

        .window-item-number {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 4px;
        }

        .window-item-actions {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .btn-icon {
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-remove {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-remove:hover {
            background: #fecaca;
        }

        .btn-add {
            background: #d1fae5;
            color: #059669;
            margin-top: 12px;
        }

        .btn-add:hover {
            background: #a7f3d0;
        }

        .window-checkbox {
            margin-right: 8px;
        }

        .toggle-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 20px;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .toggle-info {
            flex: 1;
        }

        .toggle-info h3 {
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-900);
            margin: 0 0 6px 0;
        }

        .toggle-info p {
            font-size: 0.8125rem;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-600);
            margin: 0;
            line-height: 1.5;
        }

        .toggle-switch {
            position: relative;
            width: 52px;
            height: 28px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--gray-300);
            transition: 0.3s;
            border-radius: 999px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 3px;
            bottom: 3px;
            background-color: var(--white);
            transition: 0.3s;
            border-radius: 50%;
            box-shadow: var(--shadow-sm);
        }

        .toggle-switch input:checked+.toggle-slider {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
        }

        .toggle-switch input:checked+.toggle-slider:before {
            transform: translateX(24px);
        }

        .info-note {
            background: var(--gray-50);
            border-left: 3px solid var(--teal-primary);
            border-radius: 6px;
            padding: 16px;
            font-size: 0.8125rem;
            font-family: 'Montserrat', sans-serif;
            color: var(--gray-600);
            line-height: 1.6;
        }

        .info-note strong {
            font-weight: 600;
            color: var(--gray-900);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            min-width: 140px;
            justify-content: center;
        }

        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }

        .btn-secondary:hover {
            background: var(--gray-200);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--teal-primary), var(--teal-light));
            color: var(--white);
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 24px 16px;
            }

            .form-card {
                padding: 20px;
            }

            .toggle-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
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
                    <h1 class="header-title">Queue Number Settings</h1>
                    <p class="header-subtitle">{{ $officer->department ?? 'Officer Dashboard' }}</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('officer.dashboard') }}" class="notification-btn" style="text-decoration: none;">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <form method="POST" action="{{ route('officer.logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="page-container" style="max-width: 100%; padding: 24px 16px;">
            

        @if (session('status'))
            <div class="status-banner">
                <i class="fas fa-circle-check"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('officer.queue-number-settings.update') }}">
            @csrf

            <div class="form-card">
                <div class="toggle-section">
                    <div class="toggle-info">
                        <h3>Queue Number Generation</h3>
                        <p>Enable automatic queue number generation for new service requests.</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="enabled" value="1" {{ $queueNumberEnabled ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="info-note">
                    <strong>Note:</strong> This setting applies to
                    <strong>{{ $department ?? 'your department' }}</strong> only. Changes take effect immediately for
                    new requests. Existing requests retain their queue numbers.
                </div>
            </div>

            <div class="form-card">
                <h3 class="section-title">Display Settings</h3>
                
                <div class="form-group">
                    <label for="office_display_name">
                        Office Display Name
                        <i class="fas fa-edit edit-icon" title="Customize the office name shown on queue display"></i>
                    </label>
                    <input type="text" 
                           id="office_display_name" 
                           name="office_display_name" 
                           value="{{ $officeDisplayName ?? $department }}"
                           placeholder="Enter custom office name (leave empty to use default)">
                    <small>This name will be displayed on the queue display screen. Leave empty to use the default department name.</small>
                </div>

                
                <div class="form-group" style="margin-top: 32px;">
                    <label>
                        Queue Column Configuration
                        <i class="fas fa-edit edit-icon" title="Customize the column headers and status mappings on queue display"></i>
                    </label>
                    <small style="margin-bottom: 12px; display: block;">Configure columns for the queue display. Each column can have a custom name and map to specific request statuses.</small>
                    
                    <div id="columns-container" style="margin-bottom: 16px;">
                        @php
                            $columns = $columns ?? [
                                ['key' => 'on_queue', 'name' => 'On Queue', 'statuses' => ['inquiry', 'submitted']],
                                ['key' => 'to_submit', 'name' => 'To Submit', 'statuses' => ['accepted', 'checklist_given', 'incomplete_requirements', 'rejected']],
                                ['key' => 'processed', 'name' => 'Processed', 'statuses' => ['registered', 'completed', 'positive_result', 'negative_result', 'encoded_brap', 'psa_processing']]
                            ];
                        @endphp
                        @foreach($columns as $index => $column)
                            <div class="column-item" data-column-index="{{ $index }}" style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                                <div style="display: flex; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
                                    <div style="flex: 1; min-width: 200px;">
                                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--gray-700); margin-bottom: 6px;">
                                            Column Name
                                        </label>
                                        <input type="text" 
                                               class="column-name-input" 
                                               value="{{ $column['name'] }}"
                                               placeholder="Column Name"
                                               style="width: 100%; padding: 10px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 0.875rem;">
                                    </div>
                                    <div style="flex: 1; min-width: 200px;">
                                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--gray-700); margin-bottom: 6px;">
                                            Statuses
                                        </label>
                                        <select multiple 
                                                class="column-statuses-select" 
                                                style="width: 100%; padding: 10px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 0.875rem; min-height: 120px; background: white;">
                                            @php
                                                $availableStatuses = [
                                                    'submitted', 'inquiry', 'checklist_given', 'incomplete_requirements',
                                                    'accepted', 'registered', 'encoded_brap', 'negative_result',
                                                    'positive_result', 'rejected', 'to_be_encoded_brap', 'psa_processing',
                                                    'posting', 'ready_for_approval', 'waiting_for_psa_negative_certificate',
                                                    'accepted_octogenarian', 'verified_with_problem', 'for_compliance', 'completed'
                                                ];
                                            @endphp
                                            @foreach($availableStatuses as $status)
                                                <option value="{{ $status }}" {{ in_array($status, $column['statuses']) ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small style="display: block; margin-top: 4px; font-size: 0.75rem; color: var(--gray-500);">
                                            Hold Ctrl/Cmd to select multiple statuses
                                        </small>
                                    </div>
                                    <div style="display: flex; align-items: flex-end; padding-bottom: 0;">
                                        <button type="button" 
                                                class="btn-icon btn-remove" 
                                                onclick="removeColumn(this)"
                                                style="padding: 10px 14px; background: #fee2e2; color: #dc2626; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem;"
                                                title="Remove Column">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" class="column-key-input" value="{{ $column['key'] }}">
                            </div>
                        @endforeach
                    </div>
                    
                    <button type="button" 
                            class="btn-add" 
                            onclick="addColumn()"
                            style="padding: 10px 16px; background: #d1fae5; color: #059669; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-plus"></i>
                        Add Column
                    </button>
                    
                    <input type="hidden" id="columns_json" name="columns_json" value="">
                </div>
                 
                 <div class="form-group" style="margin-top: 32px;">
                     <label for="queue_prefix">
                         Queue Number Prefix
                         <i class="fas fa-edit edit-icon" title="Customize the prefix shown before queue numbers (e.g., 'CRO' in 'CRO #1')"></i>
                     </label>
                     <input type="text" 
                            id="queue_prefix" 
                            name="queue_prefix" 
                            value="{{ $queuePrefix ?? '' }}"
                            placeholder="Enter prefix (e.g., CRO, BPLO)"
                            maxlength="10"
                            style="width: 100%; max-width: 300px; padding: 10px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 0.875rem; text-transform: uppercase;">
                     <small>This prefix will appear before queue numbers (e.g., "{{ $queuePrefix ?? 'CRO' }} #1"). Leave empty to use the default generated from department name.</small>
                </div>
            </div>

            <div class="form-actions">                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <script>
        @php
            $maxWindowNumber = 0;
            if (count($windows) > 0) {
                $numbers = array_column($windows, 'number');
                if (!empty($numbers)) {
                    $maxWindowNumber = max($numbers);
                }
            }
        @endphp
        let windowCounter = {{ $maxWindowNumber }};

        function updateWindowsJson() {
            const windows = [];
            const windowItems = document.querySelectorAll('.window-item');
            
            windowItems.forEach(item => {
                const number = parseInt(item.querySelector('.window-number-input').value);
                const name = item.querySelector('.window-name-input').value.trim();
                const active = item.querySelector('.window-active').checked;
                
                windows.push({
                    number: number,
                    name: name || `Window ${number}`,
                    active: active
                });
            });
            
            // Sort by window number
            windows.sort((a, b) => a.number - b.number);
            
            document.getElementById('windows_json').value = JSON.stringify(windows);
        }

        function addWindow() {
            windowCounter++;
            const container = document.getElementById('windows-container');
            
            const windowItem = document.createElement('div');
            windowItem.className = 'window-item';
            windowItem.setAttribute('data-window-number', windowCounter);
            windowItem.innerHTML = `
                <div class="window-item-content">
                    <div class="window-item-number">Window #${windowCounter}</div>
                    <input type="text" 
                           class="window-name-input" 
                           value="Window ${windowCounter}"
                           placeholder="Window ${windowCounter}"
                           style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 0.875rem;">
                    <label style="display: flex; align-items: center; margin-top: 8px; font-size: 0.75rem; color: var(--gray-600);">
                        <input type="checkbox" class="window-checkbox window-active" checked style="margin-right: 6px;">
                        Active
                    </label>
                </div>
                <div class="window-item-actions">
                    <button type="button" class="btn-icon btn-remove" onclick="removeWindow(this)" title="Remove Window">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="hidden" class="window-number-input" value="${windowCounter}">
            `;
            
            container.appendChild(windowItem);
            updateWindowsJson();
        }

        function removeWindow(button) {
            const windowItem = button.closest('.window-item');
            if (confirm('Are you sure you want to remove this window? Queues assigned to this window will still be accessible.')) {
                windowItem.remove();
                updateWindowsJson();
            }
        }

        // Column management functions
        let columnCounter = {{ count($columns ?? []) }};

        function updateColumnsJson() {
            const columns = [];
            const columnItems = document.querySelectorAll('.column-item');
            
            columnItems.forEach((item, index) => {
                const keyInput = item.querySelector('.column-key-input');
                const nameInput = item.querySelector('.column-name-input');
                const statusesSelect = item.querySelector('.column-statuses-select');
                
                const key = keyInput ? keyInput.value.trim() : `column_${index + 1}`;
                let name = nameInput ? nameInput.value.trim() : '';
                
                // Get selected statuses from multi-select dropdown
                const statuses = [];
                if (statusesSelect) {
                    const selectedOptions = Array.from(statusesSelect.selectedOptions);
                    statuses.push(...selectedOptions.map(option => option.value.toLowerCase().trim()));
                }
                
                // Generate name from key if name is empty
                if (!name || !name.trim()) {
                    // Generate name from key: "column_1" -> "Column 1", "to_be_encoded_brap" -> "To Be Encoded Brap"
                    const keyForName = key || `column_${index + 1}`;
                    name = keyForName
                        .split('_')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                        .join(' ');
                }
                
                // Always include the column, even if name was empty (we generated one)
                columns.push({
                    key: key || `column_${index + 1}`,
                    name: name.trim(),
                    statuses: statuses.length > 0 ? statuses : []
                });
            });
            
            document.getElementById('columns_json').value = JSON.stringify(columns);
        }

        function addColumn() {
            columnCounter++;
            const container = document.getElementById('columns-container');
            
            const columnItem = document.createElement('div');
            columnItem.className = 'column-item';
            columnItem.setAttribute('data-column-index', columnCounter);
            columnItem.style.cssText = 'background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 8px; padding: 16px; margin-bottom: 12px;';
            columnItem.innerHTML = `
                <div style="display: flex; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--gray-700); margin-bottom: 6px;">
                            Column Name
                        </label>
                        <input type="text" 
                               class="column-name-input" 
                               value=""
                               placeholder="Column Name"
                               style="width: 100%; padding: 10px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 0.875rem;">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--gray-700); margin-bottom: 6px;">
                            Statuses
                        </label>
                        <select multiple 
                                class="column-statuses-select" 
                                style="width: 100%; padding: 10px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 0.875rem; min-height: 120px; background: white;">
                            <option value="submitted">Submitted</option>
                            <option value="inquiry">Inquiry</option>
                            <option value="checklist_given">Checklist Given</option>
                            <option value="incomplete_requirements">Incomplete Requirements</option>
                            <option value="accepted">Accepted</option>
                            <option value="registered">Registered</option>
                            <option value="encoded_brap">Encoded BRAP</option>
                            <option value="negative_result">Negative Result</option>
                            <option value="positive_result">Positive Result</option>
                            <option value="rejected">Rejected</option>
                            <option value="to_be_encoded_brap">To Be Encoded BRAP</option>
                            <option value="psa_processing">PSA Processing</option>
                            <option value="posting">Posting</option>
                            <option value="ready_for_approval">Ready For Approval</option>
                            <option value="waiting_for_psa_negative_certificate">Waiting For PSA Negative Certificate</option>
                            <option value="accepted_octogenarian">Accepted Octogenarian</option>
                            <option value="verified_with_problem">Verified With Problem</option>
                            <option value="for_compliance">For Compliance</option>
                            <option value="completed">Completed</option>
                        </select>
                        <small style="display: block; margin-top: 4px; font-size: 0.75rem; color: var(--gray-500);">
                            Hold Ctrl/Cmd to select multiple statuses
                        </small>
                    </div>
                    <div style="display: flex; align-items: flex-end; padding-bottom: 0;">
                        <button type="button" 
                                class="btn-icon btn-remove" 
                                onclick="removeColumn(this)"
                                style="padding: 10px 14px; background: #fee2e2; color: #dc2626; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem;"
                                title="Remove Column">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <input type="hidden" class="column-key-input" value="column_${columnCounter}">
            `;
            
            container.appendChild(columnItem);
            
            // Add event listener to the new select element
            const newSelect = columnItem.querySelector('.column-statuses-select');
            if (newSelect) {
                newSelect.addEventListener('change', updateColumnsJson);
            }
            
            // Add event listener to the new name input
            const newNameInput = columnItem.querySelector('.column-name-input');
            if (newNameInput) {
                newNameInput.addEventListener('input', updateColumnsJson);
                newNameInput.addEventListener('change', updateColumnsJson);
            }
            
            updateColumnsJson();
        }

        function removeColumn(button) {
            const columnItem = button.closest('.column-item');
            const columnItems = document.querySelectorAll('.column-item');
            
            if (columnItems.length <= 1) {
                alert('You must have at least one column.');
                return;
            }
            
            if (confirm('Are you sure you want to remove this column?')) {
                columnItem.remove();
                updateColumnsJson();
            }
        }

        // Update JSON on form changes
        document.addEventListener('DOMContentLoaded', function() {
            // Update windows JSON on input changes
            const windowsContainer = document.getElementById('windows-container');
            if (windowsContainer) {
                windowsContainer.addEventListener('input', function(e) {
                    if (e.target.classList.contains('window-name-input') || e.target.classList.contains('window-active')) {
                        updateWindowsJson();
                    }
                });
                
                windowsContainer.addEventListener('change', function(e) {
                    if (e.target.classList.contains('window-active')) {
                        updateWindowsJson();
                    }
                });
            }
            
            // Update columns JSON on input changes
            const columnsContainer = document.getElementById('columns-container');
            if (columnsContainer) {
                columnsContainer.addEventListener('input', function(e) {
                    if (e.target.classList.contains('column-name-input')) {
                        updateColumnsJson();
                    }
                });
                
                columnsContainer.addEventListener('change', function(e) {
                    if (e.target.classList.contains('column-statuses-select')) {
                        updateColumnsJson();
                    }
                });
            }
            
            // Initial updates
            updateWindowsJson();
            updateColumnsJson();
            
            // Update before form submit
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    updateWindowsJson();
                    updateColumnsJson();
                });
            }
        });
    </script>
    </div> <!-- /.container -->
</body>

</html>