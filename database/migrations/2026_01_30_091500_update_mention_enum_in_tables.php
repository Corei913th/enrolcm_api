<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\Mention;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         $mentions = "'" . implode("', '", Mention::values()) . "'";
            
            // Update resultats_finaux table
            DB::statement("ALTER TABLE resultats_finaux DROP CONSTRAINT IF EXISTS resultats_finaux_mention_check");
            DB::statement("ALTER TABLE resultats_finaux ADD CONSTRAINT resultats_finaux_mention_check CHECK (mention::text IN ($mentions))");
            
            // Update candidats table
            DB::statement("ALTER TABLE candidats DROP CONSTRAINT IF EXISTS candidats_mention_check");
            DB::statement("ALTER TABLE candidats ADD CONSTRAINT candidats_mention_check CHECK (mention::text IN ($mentions))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't necessarily want to revert to the old list as it might break existing data
    }
};
