<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement('ALTER TABLE paiements ADD COLUMN search_vector tsvector NULL');
        DB::statement('ALTER TABLE utilisateurs ADD COLUMN search_vector tsvector NULL');
        DB::statement('ALTER TABLE candidatures ADD COLUMN search_vector tsvector NULL');

        // CRÉATION DES INDEX GIN POUR PERFORMANCES EXCEPTIONNELLES
        DB::statement('CREATE INDEX idx_paiements_search ON paiements USING GIN(search_vector)');
        DB::statement('CREATE INDEX idx_utilisateurs_search ON utilisateurs USING GIN(search_vector)');
        DB::statement('CREATE INDEX idx_candidatures_search ON candidatures USING GIN(search_vector)');

        // FONCTIONS DE MISE À JOUR DES VECTEURS DE RECHERCHE
        DB::statement("
            CREATE OR REPLACE FUNCTION paiements_search_vector_update() RETURNS trigger AS $$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('french', coalesce(NEW.reference, '')), 'A') ||
                    setweight(to_tsvector('french', coalesce(NEW.validation_notes, '')), 'B') ||
                    setweight(to_tsvector('french', coalesce(NEW.motif_rejet, '')), 'C');
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE OR REPLACE FUNCTION utilisateurs_search_vector_update() RETURNS trigger AS $$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('french', coalesce(NEW.user_name, '')), 'A') ||
                    setweight(to_tsvector('french', coalesce(NEW.email, '')), 'B') ||
                    setweight(to_tsvector('french', coalesce(NEW.telephone, '')), 'C');
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE OR REPLACE FUNCTION candidatures_search_vector_update() RETURNS trigger AS $$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('french', coalesce(NEW.code_cand_temp, '')), 'A') ||
                    setweight(to_tsvector('french', coalesce(NEW.code_cand_def, '')), 'A') ||
                    setweight(to_tsvector('french', coalesce(NEW.motif_rejet, '')), 'B');
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // TRIGGERS POUR MAINTIEN AUTOMATIQUE
        DB::statement("CREATE TRIGGER paiements_search_vector_trigger BEFORE INSERT OR UPDATE ON paiements FOR EACH ROW EXECUTE FUNCTION paiements_search_vector_update()");
        DB::statement("CREATE TRIGGER utilisateurs_search_vector_trigger BEFORE INSERT OR UPDATE ON utilisateurs FOR EACH ROW EXECUTE FUNCTION utilisateurs_search_vector_update()");
        DB::statement("CREATE TRIGGER candidatures_search_vector_trigger BEFORE INSERT OR UPDATE ON candidatures FOR EACH ROW EXECUTE FUNCTION candidatures_search_vector_update()");

        // INDEXATION INITIALE DES DONNÉES EXISTANTES (déclenchée par les triggers)
        // Les triggers mettront automatiquement à jour les search_vector existants lors du premier accès
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression des triggers
        DB::statement("DROP TRIGGER IF EXISTS paiements_search_vector_trigger ON paiements");
        DB::statement("DROP TRIGGER IF EXISTS utilisateurs_search_vector_trigger ON utilisateurs");
        DB::statement("DROP TRIGGER IF EXISTS candidatures_search_vector_trigger ON candidatures");

        // Suppression des fonctions
        DB::statement("DROP FUNCTION IF EXISTS paiements_search_vector_update()");
        DB::statement("DROP FUNCTION IF EXISTS utilisateurs_search_vector_update()");
        DB::statement("DROP FUNCTION IF EXISTS candidatures_search_vector_update()");

        // Suppression des index
        DB::statement("DROP INDEX IF EXISTS idx_paiements_search");
        DB::statement("DROP INDEX IF EXISTS idx_utilisateurs_search");
        DB::statement("DROP INDEX IF EXISTS idx_candidatures_search");

        // Suppression des colonnes (SQL brut)
        DB::statement('ALTER TABLE paiements DROP COLUMN IF EXISTS search_vector');
        DB::statement('ALTER TABLE utilisateurs DROP COLUMN IF EXISTS search_vector');
        DB::statement('ALTER TABLE candidatures DROP COLUMN IF EXISTS search_vector');
    }
};
