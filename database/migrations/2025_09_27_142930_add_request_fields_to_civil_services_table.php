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
            $table->enum('request_type', ['brap', 'regular'])->default('regular')->after('service_type');
            $table->string('service_name')->nullable()->after('request_type');
            $table->string('department')->nullable()->after('service_name');
            $table->timestamp('accepted_at')->nullable()->after('processed_at');
            $table->timestamp('notified_at')->nullable()->after('accepted_at');
            $table->dropColumn('status');
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
    }
};
