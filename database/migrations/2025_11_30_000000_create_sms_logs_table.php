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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->index();
            $table->string('request_type')->nullable(); // civil or other
            $table->unsignedBigInteger('request_id')->nullable();
            $table->string('phone_number');
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->string('status_type')->nullable(); // The status that triggered the SMS (inquiry, accepted, etc.)
            $table->string('message_id')->nullable(); // Semaphore message ID
            $table->string('network')->nullable(); // Carrier network
            $table->string('sender')->nullable(); // Sender name
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamps();

            $table->index(['tracking_number', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};

