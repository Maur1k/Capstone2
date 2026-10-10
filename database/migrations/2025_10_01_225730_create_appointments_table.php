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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique();
            $table->foreignId('civil_service_id')->constrained('civil_services')->onDelete('cascade');
            $table->string('service_type'); // Civil service type
            $table->string('full_name');
            $table->string('contact_number');
            $table->string('email');
            $table->text('address');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('assigned_officer')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['appointment_date', 'appointment_time']);
            $table->index(['status']);
            $table->index(['civil_service_id']);
            $table->index(['assigned_officer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
