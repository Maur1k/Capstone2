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
        // Add the new status to the ENUM
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
            'to_be_encoded_brap'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the new status from the ENUM
        DB::statement("ALTER TABLE civil_services MODIFY COLUMN status ENUM(
            'inquiry',
            'checklist_given',
            'incomplete_requirements',
            'accepted',
            'registered',
            'encoded_brap',
            'negative_result',
            'positive_result',
            'rejected'
        )");
    }
};