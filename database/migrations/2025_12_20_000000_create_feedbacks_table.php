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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->index();
            $table->unsignedTinyInteger('rating'); // 1-5 stars
            $table->text('message')->nullable(); // Optional feedback message
            $table->string('source')->default('kiosk'); // 'kiosk' or 'website'
            $table->timestamps();

            $table->index(['tracking_number', 'created_at']);
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};

