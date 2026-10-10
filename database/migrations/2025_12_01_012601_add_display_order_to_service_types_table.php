<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->unsignedInteger('display_order')->default(0)->after('is_active');
        });

        // Set initial display_order based on current order
        $types = DB::table('service_types')->orderBy('id')->get();
        foreach ($types as $index => $type) {
            DB::table('service_types')
                ->where('id', $type->id)
                ->update(['display_order' => $index + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};
