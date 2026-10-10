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
        // Add the new statuses to the ENUM: psa_processing, posting, ready_for_approval, submitted
        // Note: 'submitted' is included for completeness even though it's typically the initial state
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
        // Remove the new statuses from the ENUM (revert to previous state)
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
            'to_be_encoded_brap'
        )");
    }
};
