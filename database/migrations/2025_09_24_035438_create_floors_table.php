<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->string('building');
            $table->string('name'); // e.g., "Ground Floor"
            $table->string('image_path', 1024); // path/URL to floorplan PNG/JPG
            $table->integer('width')->nullable(); // image/display width in px (optional, helps mapping)
            $table->integer('height'); // image/display height in px
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('floors');
    }
};
