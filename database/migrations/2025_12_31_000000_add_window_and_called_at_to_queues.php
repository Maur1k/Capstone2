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
        // Add window_number and called_at to civil_services table
        Schema::table('civil_services', function (Blueprint $table) {
            $table->integer('window_number')->nullable()->after('queue_number');
            $table->timestamp('called_at')->nullable()->after('window_number');
        });

        // Add window_number and called_at to other_services table
        Schema::table('other_services', function (Blueprint $table) {
            $table->integer('window_number')->nullable()->after('queue_number');
            $table->timestamp('called_at')->nullable()->after('window_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('civil_services', function (Blueprint $table) {
            $table->dropColumn(['window_number', 'called_at']);
        });

        Schema::table('other_services', function (Blueprint $table) {
            $table->dropColumn(['window_number', 'called_at']);
        });
    }
};



