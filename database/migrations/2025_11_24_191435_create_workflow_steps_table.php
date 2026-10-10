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
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->enum('workflow_type', ['brap', 'regular', 'kiosk', 'approval']);
            $table->string('title'); // status or step name
            $table->text('description');
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
            
            $table->index(['workflow_type', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
