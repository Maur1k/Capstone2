<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('civil_services', function (Blueprint $table) {
            // Add new status flow fields
            $table->timestamp('posting_started_at')->nullable()->after('accepted_at');
            $table->timestamp('posting_ended_at')->nullable()->after('posting_started_at');
            $table->timestamp('approved_at')->nullable()->after('posting_ended_at');
            $table->text('requirements_checklist')->nullable()->after('admin_notes');
            $table->timestamp('checklist_sent_at')->nullable()->after('requirements_checklist');
            $table->boolean('is_holiday_excluded')->default(true)->after('checklist_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('civil_services', function (Blueprint $table) {
            $table->dropColumn([
                'posting_started_at',
                'posting_ended_at', 
                'approved_at',
                'requirements_checklist',
                'checklist_sent_at',
                'is_holiday_excluded'
            ]);
        });
    }
};