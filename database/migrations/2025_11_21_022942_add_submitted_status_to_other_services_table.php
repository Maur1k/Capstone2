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
        // Modify the status enum to include 'submitted'
        DB::statement("ALTER TABLE other_services MODIFY COLUMN status ENUM(
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
            'psa_processing'
        ) DEFAULT 'submitted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'submitted' and 'psa_processing' from the enum
        DB::statement("ALTER TABLE other_services MODIFY COLUMN status ENUM(
            'inquiry', 
            'checklist_given', 
            'incomplete_requirements', 
            'accepted', 
            'registered',
            'encoded_brap',
            'negative_result',
            'positive_result',
            'rejected'
        ) DEFAULT 'inquiry'");
    }
};
