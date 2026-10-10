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
            $table->index('building', 'idx_floors_building');
            $table->index(['building', 'name'], 'idx_floors_building_name');
        });

        Schema::table('pois', function (Blueprint $table) {
            $table->index('floor_id', 'idx_pois_floor_id');
            $table->index('category', 'idx_pois_category');
            $table->index(['floor_id', 'category'], 'idx_pois_floor_category');
            $table->index(['x', 'y'], 'idx_pois_coordinates');
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
            $table->dropIndex('idx_floors_building');
            $table->dropIndex('idx_floors_building_name');
        });

        Schema::table('pois', function (Blueprint $table) {
            $table->dropIndex('idx_pois_floor_id');
            $table->dropIndex('idx_pois_category');
            $table->dropIndex('idx_pois_floor_category');
            $table->dropIndex('idx_pois_coordinates');
        });
    }
};
