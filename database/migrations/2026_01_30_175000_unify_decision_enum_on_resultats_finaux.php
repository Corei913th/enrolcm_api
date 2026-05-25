<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Dropping existing constraints if they exist to avoid conflict
        DB::statement('ALTER TABLE resultats_finaux DROP CONSTRAINT IF EXISTS resultats_finaux_decision_check');
        DB::statement('ALTER TABLE resultats_finaux DROP CONSTRAINT IF EXISTS resultats_finaux_categorie_admission_check');

        // Adding strict CHECK constraints for 'decision'
        DB::statement("ALTER TABLE resultats_finaux ADD CONSTRAINT resultats_finaux_decision_check CHECK (decision IN ('ADMIS', 'LISTE_ATTENTE', 'REFUSEE'))");

        // Adding strict CHECK constraints for 'categorie_admission'
        DB::statement("ALTER TABLE resultats_finaux ADD CONSTRAINT resultats_finaux_categorie_admission_check CHECK (categorie_admission IN ('STANDARD', 'CONDITIONNEL', 'ELIMINATOIRE'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE resultats_finaux DROP CONSTRAINT IF EXISTS resultats_finaux_decision_check');
        DB::statement('ALTER TABLE resultats_finaux DROP CONSTRAINT IF EXISTS resultats_finaux_categorie_admission_check');
    }
};
