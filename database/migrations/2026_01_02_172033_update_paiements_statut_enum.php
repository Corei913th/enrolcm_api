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
        // Supprimer la valeur par défaut temporairement
        DB::statement('ALTER TABLE paiements ALTER COLUMN statut DROP DEFAULT');

        // Changer temporairement en VARCHAR
        DB::statement('ALTER TABLE paiements ALTER COLUMN statut TYPE VARCHAR(50)');

        // Supprimer l'ancien enum s'il existe
        DB::statement('DROP TYPE IF EXISTS "paiements_statut"');

        // Créer le nouvel enum avec toutes les valeurs
        DB::statement("CREATE TYPE \"paiements_statut\" AS ENUM('PENDING', 'VERIFIED', 'REJECTED', 'OCR_VERIFIE', 'PENDING_MANUAL_REVIEW')");

        // Changer la colonne pour utiliser le nouvel enum
        DB::statement('ALTER TABLE paiements ALTER COLUMN statut TYPE "paiements_statut" USING statut::"paiements_statut"');

        // Remettre la valeur par défaut
        DB::statement("ALTER TABLE paiements ALTER COLUMN statut SET DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la valeur par défaut temporairement
        DB::statement('ALTER TABLE paiements ALTER COLUMN statut DROP DEFAULT');

        // Revenir à VARCHAR temporairement
        DB::statement('ALTER TABLE paiements ALTER COLUMN statut TYPE VARCHAR(50)');

        // Supprimer le nouvel enum
        DB::statement('DROP TYPE IF EXISTS "paiements_statut"');

        // Recréer l'ancien enum
        DB::statement("CREATE TYPE \"paiements_statut\" AS ENUM('PENDING', 'VERIFIED', 'REJECTED')");

        // Changer la colonne pour utiliser l'ancien enum
        DB::statement('ALTER TABLE paiements ALTER COLUMN statut TYPE "paiements_statut" USING statut::"paiements_statut"');

        // Remettre la valeur par défaut
        DB::statement("ALTER TABLE paiements ALTER COLUMN statut SET DEFAULT 'PENDING'");
    }
};
