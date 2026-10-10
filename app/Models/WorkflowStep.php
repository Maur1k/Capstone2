<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    protected $fillable = [
        'workflow_type',
        'title',
        'description',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    /**
     * Get workflow steps by type, ordered by display_order
     */
    public static function getByType($type)
    {
        return self::where('workflow_type', $type)
            ->orderBy('display_order')
            ->get()
            ->map(function ($step) {
                // For brap and regular workflows, use 'status' key
                // For kiosk and approval workflows, use 'step' key
                if (in_array($step->workflow_type, ['brap', 'regular'])) {
                    return [
                        'status' => $step->title,
                        'description' => $step->description,
                    ];
                } else {
                    return [
                        'step' => $step->title,
                        'description' => $step->description,
                    ];
                }
            })
            ->toArray();
    }
}
