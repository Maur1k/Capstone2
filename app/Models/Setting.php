<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getBoolean(string $key, bool $default = false): bool
    {
        $value = static::get($key);

        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    public static function setBoolean(string $key, bool $value): void
    {
        static::set($key, $value ? '1' : '0');
    }

    /**
     * Get queue number setting for a specific department
     */
    public static function getQueueNumberSettingForDepartment(string $department, bool $default = false): bool
    {
        $key = 'feature.queuing_number.' . str_replace(' ', '_', strtolower($department));
        return static::getBoolean($key, $default);
    }

    /**
     * Set queue number setting for a specific department
     */
    public static function setQueueNumberSettingForDepartment(string $department, bool $value): void
    {
        $key = 'feature.queuing_number.' . str_replace(' ', '_', strtolower($department));
        static::setBoolean($key, $value);
    }

    /**
     * Get queue display office name for a specific department
     */
    public static function getQueueDisplayOfficeName(string $department, ?string $default = null): ?string
    {
        $key = 'queue.display.office_name.' . str_replace(' ', '_', strtolower($department));
        $value = static::get($key);
        // Return default if value is null or empty string
        return ($value !== null && trim($value) !== '') ? trim($value) : $default;
    }

    /**
     * Set queue display office name for a specific department
     */
    public static function setQueueDisplayOfficeName(string $department, ?string $value): void
    {
        $key = 'queue.display.office_name.' . str_replace(' ', '_', strtolower($department));
        static::set($key, $value);
    }

    /**
     * Get queue display window name for a specific department and window number
     */
    public static function getQueueDisplayWindowName(string $department, int $windowNumber, ?string $default = null): ?string
    {
        $key = 'queue.display.window_name.' . str_replace(' ', '_', strtolower($department)) . '.window_' . $windowNumber;
        $value = static::get($key);
        return $value !== null ? $value : $default;
    }

    /**
     * Set queue display window name for a specific department and window number
     */
    public static function setQueueDisplayWindowName(string $department, int $windowNumber, ?string $value): void
    {
        $key = 'queue.display.window_name.' . str_replace(' ', '_', strtolower($department)) . '.window_' . $windowNumber;
        static::set($key, $value);
    }

    /**
     * Get all windows configuration for a department
     * Returns array of windows with number, name, and active status
     */
    public static function getQueueWindows(string $department): array
    {
        $key = 'queue.windows.' . str_replace(' ', '_', strtolower($department));
        $value = static::get($key);
        
        if ($value) {
            $windows = json_decode($value, true);
            if (is_array($windows)) {
                return $windows;
            }
        }
        
        // Default: return windows 1-10 if none configured
        $defaultWindows = [];
        for ($i = 1; $i <= 10; $i++) {
            $defaultWindows[] = [
                'number' => $i,
                'name' => "Window {$i}",
                'active' => true
            ];
        }
        return $defaultWindows;
    }

    /**
     * Set windows configuration for a department
     */
    public static function setQueueWindows(string $department, array $windows): void
    {
        $key = 'queue.windows.' . str_replace(' ', '_', strtolower($department));
        static::set($key, json_encode($windows));
    }

    /**
     * Get window name by number (works for both active and inactive windows)
     * This ensures existing queues with window assignments still work even if window is deactivated
     */
    public static function getWindowNameByNumber(string $department, int $windowNumber, ?string $default = null): ?string
    {
        $windows = static::getQueueWindows($department);
        foreach ($windows as $window) {
            if ($window['number'] == $windowNumber) {
                return $window['name'];
            }
        }
        return $default ?: "Window {$windowNumber}";
    }

    /**
     * Check if a window number is active
     */
    public static function isWindowActive(string $department, int $windowNumber): bool
    {
        $windows = static::getQueueWindows($department);
        foreach ($windows as $window) {
            if ($window['number'] == $windowNumber) {
                return (bool)$window['active'];
            }
        }
        return false;
    }

    /**
     * Get queue column display name (legacy method for backward compatibility)
     */
    public static function getQueueColumnName(string $department, string $columnKey, string $default): string
    {
        $key = 'queue.display.column.' . str_replace(' ', '_', strtolower($department)) . '.' . $columnKey;
        $value = static::get($key);
        return ($value !== null && trim($value) !== '') ? trim($value) : $default;
    }

    /**
     * Set queue column display name (legacy method for backward compatibility)
     */
    public static function setQueueColumnName(string $department, string $columnKey, ?string $value): void
    {
        $key = 'queue.display.column.' . str_replace(' ', '_', strtolower($department)) . '.' . $columnKey;
        $trimmedValue = $value ? trim($value) : null;
        static::set($key, $trimmedValue ?: null);
    }

    /**
     * Get all queue column configurations for a department
     * Returns array of columns with 'key', 'name', and 'statuses' (array of statuses that map to this column)
     */
    public static function getQueueColumns(string $department): array
    {
        $key = 'queue.display.columns.' . str_replace(' ', '_', strtolower($department));
        $value = static::get($key);
        
        if ($value) {
            $columns = json_decode($value, true);
            if (is_array($columns) && !empty($columns)) {
                // Ensure all columns have valid names - always generate names even if empty
                $validColumns = [];
                foreach ($columns as $column) {
                    // Ensure key exists, generate if missing
                    $key = isset($column['key']) && !empty(trim($column['key'])) 
                        ? trim($column['key']) 
                        : 'column_' . (count($validColumns) + 1);
                    
                    // Ensure name exists, generate from key if missing or empty
                    $name = '';
                    if (isset($column['name']) && !empty(trim($column['name']))) {
                        $name = trim($column['name']);
                    } else {
                        // Generate name from key, ensuring it's never empty
                        $name = ucfirst(str_replace('_', ' ', $key));
                        if (empty($name)) {
                            $name = 'Column ' . (count($validColumns) + 1);
                        }
                    }
                    
                    // Ensure statuses array exists
                    $statuses = [];
                    if (isset($column['statuses']) && is_array($column['statuses'])) {
                        $statuses = array_map(function($s) {
                            return strtolower(trim($s));
                        }, array_filter($column['statuses'], function($s) {
                            return !empty(trim($s));
                        }));
                    }
                    
                    $validColumns[] = [
                        'key' => $key,
                        'name' => $name,
                        'statuses' => $statuses
                    ];
                }
                // Only return if we have valid columns
                if (!empty($validColumns)) {
                    return $validColumns;
                }
            }
        }
        
        // Default columns for backward compatibility
        return [
            [
                'key' => 'on_queue',
                'name' => static::getQueueColumnName($department, 'on_queue', 'On Queue'),
                'statuses' => ['inquiry', 'submitted']
            ],
            [
                'key' => 'to_submit',
                'name' => static::getQueueColumnName($department, 'to_submit', 'To Submit'),
                'statuses' => ['accepted', 'checklist_given', 'incomplete_requirements', 'rejected']
            ],
            [
                'key' => 'processed',
                'name' => static::getQueueColumnName($department, 'processed', 'Processed'),
                'statuses' => ['registered', 'completed', 'positive_result', 'negative_result', 'encoded_brap', 'psa_processing']
            ]
        ];
    }

    /**
     * Set queue column configurations for a department
     */
    public static function setQueueColumns(string $department, array $columns): void
    {
        $key = 'queue.display.columns.' . str_replace(' ', '_', strtolower($department));
        
        // Validate and clean columns
        $validatedColumns = [];
        foreach ($columns as $column) {
            if (isset($column['key']) && isset($column['name']) && isset($column['statuses'])) {
                $key = trim($column['key']);
                $name = trim($column['name']);
                
                // Generate name from key if name is empty
                if (empty($name)) {
                    if (!empty($key)) {
                        $name = ucfirst(str_replace('_', ' ', $key));
                    } else {
                        $name = 'Column ' . (count($validatedColumns) + 1);
                    }
                }
                
                // Ensure key exists, generate if missing
                if (empty($key)) {
                    $key = 'column_' . (count($validatedColumns) + 1);
                }
                
                // Normalize statuses: trim, lowercase, and filter empty
                $statuses = [];
                if (is_array($column['statuses'])) {
                    foreach ($column['statuses'] as $status) {
                        $normalizedStatus = strtolower(trim($status));
                        if (!empty($normalizedStatus)) {
                            $statuses[] = $normalizedStatus;
                        }
                    }
                }
                
                $validatedColumns[] = [
                    'key' => $key,
                    'name' => $name,
                    'statuses' => $statuses
                ];
            }
        }
        
        static::set($key, json_encode($validatedColumns));
    }

    /**
     * Get all queue column names for a department (backward compatibility)
     * Returns simple key => name mapping
     */
    public static function getQueueColumnNames(string $department): array
    {
        $columns = static::getQueueColumns($department);
        $result = [];
        foreach ($columns as $column) {
            $result[$column['key']] = $column['name'];
        }
        return $result;
    }

    /**
     * Get queue number prefix for a department
     * Returns the prefix used in queue number display (e.g., "CRO" for "CRO #1")
     */
    public static function getQueueNumberPrefix(string $department, ?string $default = null): ?string
    {
        $key = 'queue.display.prefix.' . str_replace(' ', '_', strtolower($department));
        $value = static::get($key);
        // Return the value if it exists (even if empty string), otherwise return default
        // This allows empty strings to be saved and retrieved, and distinguishes between
        // "not set" (null) and "set to empty" (empty string)
        if ($value !== null) {
            return trim($value);
        }
        return $default;
    }

    /**
     * Set queue number prefix for a department
     */
    public static function setQueueNumberPrefix(string $department, ?string $value): void
    {
        $key = 'queue.display.prefix.' . str_replace(' ', '_', strtolower($department));
        // Trim the value, but allow empty strings to be saved (don't convert to null)
        $trimmedValue = $value !== null ? trim($value) : null;
        static::set($key, $trimmedValue);
    }

    /**
     * Get posting days setting for a specific department
     */
    public static function getPostingDays(string $department, int $default = 10): int
    {
        $key = 'posting.days.' . str_replace(' ', '_', strtolower($department));
        $value = static::get($key);
        return $value !== null ? (int) $value : $default;
    }

    /**
     * Set posting days setting for a specific department
     */
    public static function setPostingDays(string $department, int $days): void
    {
        $key = 'posting.days.' . str_replace(' ', '_', strtolower($department));
        static::set($key, (string) $days);
    }
}







