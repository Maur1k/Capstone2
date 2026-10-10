<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds 'approved' and 'declined' statuses to the appointments table.
     */
    public function up(): void
    {
        // Add 'approved' and 'declined' to the status ENUM
        DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM(
            'pending',
            'approved',
            'confirmed',
            'declined',
            'completed',
            'cancelled',
            'no_show'
        ) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original status values (remove 'approved' and 'declined')
        DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM(
            'pending',
            'confirmed',
            'completed',
            'cancelled',
            'no_show'
        ) DEFAULT 'pending'");
    }
};
