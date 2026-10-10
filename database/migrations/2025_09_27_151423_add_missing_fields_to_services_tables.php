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
        // Add missing fields to civil_services table
        Schema::table('civil_services', function (Blueprint $table) {
            if (!Schema::hasColumn('civil_services', 'request_type')) {
                $table->enum('request_type', ['brap', 'regular'])->default('regular')->after('service_type');
            }
            if (!Schema::hasColumn('civil_services', 'service_name')) {
                $table->string('service_name')->nullable()->after('request_type');
            }
            if (!Schema::hasColumn('civil_services', 'department')) {
                $table->string('department')->nullable()->after('service_name');
            }
            if (!Schema::hasColumn('civil_services', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('processed_at');
            }
            if (!Schema::hasColumn('civil_services', 'notified_at')) {
                $table->timestamp('notified_at')->nullable()->after('accepted_at');
            }
        });

        // Update status column in civil_services if it's still string
        Schema::table('civil_services', function (Blueprint $table) {
            if (Schema::hasColumn('civil_services', 'status')) {
                $table->dropColumn('status');
            }
        });
        
        Schema::table('civil_services', function (Blueprint $table) {
            $table->enum('status', [
                'inquiry',
                'checklist_given',
                'incomplete_requirements',
                'accepted',
                'registered',
                'encoded_brap',
                'negative_result',
                'positive_result',
                'rejected'
            ])->default('inquiry')->after('request_type');
        });

        // Add missing fields to other_services table
        Schema::table('other_services', function (Blueprint $table) {
            if (!Schema::hasColumn('other_services', 'request_type')) {
                $table->enum('request_type', ['brap', 'regular'])->default('regular')->after('service_type');
            }
            if (!Schema::hasColumn('other_services', 'service_name')) {
                $table->string('service_name')->nullable()->after('request_type');
            }
            if (!Schema::hasColumn('other_services', 'department')) {
                $table->string('department')->nullable()->after('service_name');
            }
            if (!Schema::hasColumn('other_services', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('processed_at');
            }
            if (!Schema::hasColumn('other_services', 'notified_at')) {
                $table->timestamp('notified_at')->nullable()->after('accepted_at');
            }
        });

        // Update status column in other_services if it's still string
        Schema::table('other_services', function (Blueprint $table) {
            if (Schema::hasColumn('other_services', 'status')) {
                $table->dropColumn('status');
            }
        });
        
        Schema::table('other_services', function (Blueprint $table) {
            $table->enum('status', [
                'inquiry',
                'checklist_given',
                'incomplete_requirements',
                'accepted',
                'registered',
                'encoded_brap',
                'negative_result',
                'positive_result',
                'rejected'
            ])->default('inquiry')->after('request_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('civil_services', function (Blueprint $table) {
            $table->dropColumn(['request_type', 'service_name', 'department', 'accepted_at', 'notified_at']);
            $table->dropColumn('status');
        });
        
        Schema::table('civil_services', function (Blueprint $table) {
            $table->string('status')->default('submitted')->after('service_type');
        });

        Schema::table('other_services', function (Blueprint $table) {
            $table->dropColumn(['request_type', 'service_name', 'department', 'accepted_at', 'notified_at']);
            $table->dropColumn('status');
        });
        
        Schema::table('other_services', function (Blueprint $table) {
            $table->string('status')->default('submitted')->after('service_type');
        });
    }
};