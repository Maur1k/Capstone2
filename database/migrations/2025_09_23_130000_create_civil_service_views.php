<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite used in tests does not support CREATE OR REPLACE VIEW
        // Drop if exists first for cross-DB compatibility
        DB::statement('DROP VIEW IF EXISTS birth_services');
        DB::statement('DROP VIEW IF EXISTS marriage_services');
        DB::statement("CREATE VIEW birth_services AS SELECT * FROM civil_services WHERE service_type LIKE '%Birth%'");
        DB::statement("CREATE VIEW marriage_services AS SELECT * FROM civil_services WHERE service_type LIKE '%Marriage%'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS birth_services');
        DB::statement('DROP VIEW IF EXISTS marriage_services');
    }
};


