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
        Schema::create('document_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('civil_service_id')->constrained('civil_services')->onDelete('cascade');
            $table->string('requirement_name'); // e.g., "Birth Certificate", "Valid ID", etc.
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // MIME type
            $table->integer('file_size'); // Size in bytes
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->string('reviewed_by')->nullable(); // Officer who reviewed
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['civil_service_id']);
            $table->index(['status']);
            $table->index(['requirement_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_uploads');
    }
};
