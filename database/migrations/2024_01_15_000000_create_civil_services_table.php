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
        Schema::create('civil_services', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('queue_number');
            $table->string('service_type'); // e.g., 'Certificate of Live Birth (On-time)'
            $table->string('status')->default('submitted'); // submitted, under_review, processing, ready_for_pickup, completed, rejected
            $table->string('full_name');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('purpose')->nullable();
            $table->text('additional_info')->nullable();

            // Child information (for birth certificates)
            $table->json('child_info')->nullable(); // Store detailed child information
            $table->json('mother_info')->nullable(); // Store detailed mother information
            $table->json('father_info')->nullable(); // Store detailed father information
            $table->json('contact_info')->nullable(); // Store contact person information

            // Administrative fields
            $table->string('processed_by')->nullable(); // Admin who processed the request
            $table->timestamp('processed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('print_status')->default('pending'); // pending, printed, failed

            $table->timestamps();

            // Indexes for better performance
            $table->index(['tracking_number']);
            $table->index(['status']);
            $table->index(['service_type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('civil_services');
    }
};

