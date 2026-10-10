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
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('user_type')->nullable()->after('user_id'); // 'officer', 'admin', 'client'
            $table->json('data')->nullable()->after('message'); // Store additional data as JSON
            
            $table->index(['user_id', 'user_type']);
            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'user_type']);
            $table->dropIndex(['user_id', 'is_read']);
            $table->dropColumn(['user_id', 'user_type', 'data']);
        });
    }
};