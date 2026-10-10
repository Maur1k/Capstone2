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
        Schema::table('civil_services', function (Blueprint $table) {
            $table->integer('posting_days')->nullable()->after('posting_ended_at');
        });

        // Set default posting_days to 10 for existing records that have posting_started_at
        DB::statement("
            UPDATE civil_services 
            SET posting_days = 10 
            WHERE posting_started_at IS NOT NULL 
            AND posting_days IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('civil_services', function (Blueprint $table) {
            $table->dropColumn('posting_days');
        });
    }
};




