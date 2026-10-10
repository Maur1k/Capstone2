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
        Schema::create('municipal_information', function (Blueprint $table) {
            $table->id();
            $table->string('city_name')->default('Alaminos City');
            $table->string('tagline')->default('HOME OF THE Hundred Islands National Park');
            $table->string('mayor_name')->default('ARTH BRYAN G. CELESTE');
            $table->string('mayor_title')->default('CITY MAYOR');
            $table->string('mayor_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('city_seal')->nullable();
            $table->string('iso_certification')->nullable();
            $table->string('email')->default('cso.alaminos@yahoo.com');
            $table->string('phone')->default('(075) 201-2345');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipal_information');
    }
};
