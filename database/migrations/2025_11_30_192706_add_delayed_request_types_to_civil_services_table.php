<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds 'delayed_brap' and 'delayed_regular' to the request_type ENUM in civil_services table.
     */
    public function up(): void
    {
        // Add 'delayed_brap' and 'delayed_regular' to the request_type ENUM
        DB::statement("ALTER TABLE civil_services MODIFY COLUMN request_type ENUM(
            'brap',
            'regular',
            'delayed_brap',
            'delayed_regular'
        ) DEFAULT 'regular'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original request_type values (remove 'delayed_brap' and 'delayed_regular')
        DB::statement("ALTER TABLE civil_services MODIFY COLUMN request_type ENUM(
            'brap',
            'regular'
        ) DEFAULT 'regular'");
    }
};
