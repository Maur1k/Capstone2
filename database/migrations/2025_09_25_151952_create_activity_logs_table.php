<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->string('activity_type'); // 'login', 'logout', 'status_change', 'request_viewed', etc.
            $table->string('description');
            $table->string('target_type')->nullable(); // 'CivilService', 'OtherService', etc.
            $table->unsignedBigInteger('target_id')->nullable(); // ID of the target record
            $table->json('metadata')->nullable(); // Additional data like old_status, new_status, etc.
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['admin_id', 'created_at']);
            $table->index(['activity_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
