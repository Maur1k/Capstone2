<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix existing accepted records that don't have posting_started_at set.
     */
    public function up(): void
    {
        // Set posting_started_at for all accepted Delayed records that don't have it
        // Use accepted_at if available, otherwise use updated_at
        DB::statement("
            UPDATE civil_services 
            SET posting_started_at = COALESCE(accepted_at, updated_at, NOW()) 
            WHERE status = 'accepted' 
            AND service_name LIKE '%Delayed%' 
            AND posting_started_at IS NULL
        ");

        // Also fix positive_result records
        DB::statement("
            UPDATE civil_services 
            SET posting_started_at = COALESCE(updated_at, NOW()) 
            WHERE status = 'positive_result' 
            AND service_name LIKE '%Delayed%' 
            AND posting_started_at IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};

