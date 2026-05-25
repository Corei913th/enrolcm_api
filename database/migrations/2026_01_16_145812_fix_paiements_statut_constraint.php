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
        // Supprimer l'ancienne contrainte check si elle existe
        DB::statement('ALTER TABLE paiements DROP CONSTRAINT IF EXISTS paiements_statut_check');

        // Ajouter la nouvelle contrainte avec toutes les valeurs valides
        DB::statement("
            ALTER TABLE paiements 
            ADD CONSTRAINT paiements_statut_check 
            CHECK (statut IN ('PENDING', 'VERIFIED', 'REJECTED', 'OCR_VERIFIE', 'PENDING_MANUAL_REVIEW'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la contrainte
        DB::statement('ALTER TABLE paiements DROP CONSTRAINT IF EXISTS paiements_statut_check');

        // Remettre l'ancienne contrainte (seulement les 3 valeurs de base)
        DB::statement("
            ALTER TABLE paiements 
            ADD CONSTRAINT paiements_statut_check 
            CHECK (statut IN ('PENDING', 'VERIFIED', 'REJECTED'))
        ");
    }
};
