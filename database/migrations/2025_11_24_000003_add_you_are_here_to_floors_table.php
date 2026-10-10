<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->float('you_are_here_model_x')->nullable()->after('show_you_are_here');
            $table->float('you_are_here_model_y')->nullable()->after('you_are_here_model_x');
            $table->float('you_are_here_model_z')->nullable()->after('you_are_here_model_y');
        });
    }

    public function down()
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->dropColumn([
                'you_are_here_model_x',
                'you_are_here_model_y',
                'you_are_here_model_z',
            ]);
        });
    }
};


