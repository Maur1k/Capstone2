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
        Schema::table('floors', function (Blueprint $table) {
            $table->float('stairs_model_x')->nullable()->after('glb_path');
            $table->float('stairs_model_y')->nullable()->after('stairs_model_x');
            $table->float('stairs_model_z')->nullable()->after('stairs_model_y');
            $table->boolean('use_stairs_as_start')->default(false)->after('stairs_model_z');
            $table->boolean('show_you_are_here')->default(true)->after('use_stairs_as_start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->dropColumn([
                'stairs_model_x',
                'stairs_model_y',
                'stairs_model_z',
                'use_stairs_as_start',
                'show_you_are_here',
            ]);
        });
    }
};


