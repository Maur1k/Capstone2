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
        Schema::create('pois', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('category', 100)->nullable();
            $table->float('x'); // X coordinate on the floor image
            $table->float('y'); // Y coordinate
            $table->json('meta')->nullable(); // optional extra data (phone, hours, etc.)
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
        Schema::dropIfExists('pois');
    }
};
