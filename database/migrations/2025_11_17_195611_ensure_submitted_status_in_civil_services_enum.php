<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, fix any invalid datetime values (0000-00-00 00:00:00)
        DB::statement("UPDATE civil_services SET created_at = NOW() WHERE created_at = '0000-00-00 00:00:00' OR created_at IS NULL");
        DB::statement("UPDATE civil_services SET updated_at = NOW() WHERE updated_at = '0000-00-00 00:00:00' OR updated_at IS NULL");
        
        // Now ensure 'submitted' status is included in the ENUM
        // This migration ensures all statuses from the BRAP and Regular flows are present
        DB::statement("ALTER TABLE civil_services MODIFY COLUMN status ENUM(
            'submitted',
            'inquiry',
            'checklist_given',
            'incomplete_requirements',
            'accepted',
            'registered',
            'encoded_brap',
            'negative_result',
            'positive_result',
            'rejected',
            'to_be_encoded_brap',
            'psa_processing',
            'posting',
            'ready_for_approval'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to state without 'submitted' (though this shouldn't be necessary)
        DB::statement("ALTER TABLE civil_services MODIFY COLUMN status ENUM(
            'inquiry',
            'checklist_given',
            'incomplete_requirements',
            'accepted',
            'registered',
            'encoded_brap',
            'negative_result',
            'positive_result',
            'rejected',
            'to_be_encoded_brap',
            'psa_processing',
            'posting',
            'ready_for_approval'
        )");
    }
};
