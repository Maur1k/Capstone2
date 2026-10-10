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
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->morphs('checklistable'); // civil_service_id or other_service_id
            $table->json('items'); // Array of selected requirements
            $table->text('remarks')->nullable(); // Additional notes/remarks
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('sent_at')->nullable(); // When SMS was sent
            $table->string('sms_status')->nullable(); // sent, failed, pending
            $table->text('sms_message')->nullable(); // The actual SMS message sent
            $table->string('phone_number')->nullable(); // Phone number SMS was sent to
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['checklistable_id', 'checklistable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
