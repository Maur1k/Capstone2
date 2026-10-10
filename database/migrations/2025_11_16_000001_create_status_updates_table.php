<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('status_updates', function (Blueprint $table) {
            $table->id();
            $table->morphs('statusable');
            $table->string('status', 100);
            $table->date('status_date')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'statusable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_updates');
    }
};

