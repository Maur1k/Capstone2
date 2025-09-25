<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('other_services', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('queue_number');
            $table->string('service_type');
            $table->string('status')->default('submitted');
            $table->string('full_name');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('purpose')->nullable();
            $table->json('additional_info')->nullable();
            $table->string('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('print_status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('other_services');
    }
};


