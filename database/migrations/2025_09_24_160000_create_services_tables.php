<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('department')->nullable();
            $table->string('icon_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('name');
            $table->string('icon_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_type_id')->constrained('service_types')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_steps');
        Schema::dropIfExists('service_types');
        Schema::dropIfExists('services');
    }
};







