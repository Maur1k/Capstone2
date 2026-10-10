<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds new statuses for Delayed Birth Registration (Regular) and BRAP flows.
     */
    public function up(): void
    {
        // Add new statuses for Delayed Birth Registration flows
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
            'ready_for_approval',
            'waiting_for_psa_negative_certificate',
            'accepted_octogenarian',
            'verified_with_problem',
            'for_compliance',
            'completed'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
};

